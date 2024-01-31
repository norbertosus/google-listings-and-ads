<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\GoogleListingsAndAds\Integration;

use Automattic\WooCommerce\GoogleListingsAndAds\Infrastructure\Registerable;
use Automattic\WooCommerce\GoogleListingsAndAds\Infrastructure\Service;
use Automattic\WooCommerce\GoogleListingsAndAds\Value\ChannelVisibility;
use WP_REST_Response;
use WP_REST_Request;

defined( 'ABSPATH' ) || exit;

/**
 * Class WPCOMProxy
 *
 * Initializes the hooks to filter the data sent to the WPCOM proxy depending on the query parameter gla_syncable.
 *
 * @package Automattic\WooCommerce\GoogleListingsAndAds\Integration
 */
class WPCOMProxy implements Service, Registerable {

	/**
	 * The meta key used to filter the items.
	 *
	 * @var string
	 */
	public const KEY_VISIBILITY = '_wc_gla_visibility';

	/**
	 * The Post types to be filtered.
	 *
	 * @var array
	 */
	public static $post_types_to_filter = [
		'product'           => [
			'meta_query' => [
				[
					'key'     => self::KEY_VISIBILITY,
					'value'   => ChannelVisibility::SYNC_AND_SHOW,
					'compare' => '=',
				],
			],
		],
		'shop_coupon'       => [
			'meta_query' => [
				[
					'key'     => self::KEY_VISIBILITY,
					'value'   => ChannelVisibility::SYNC_AND_SHOW,
					'compare' => '=',
				],
				[
					'key'     => 'customer_email',
					'compare' => 'NOT EXISTS',
				],
			],
		],
		'product_variation' => [
			'meta_query' => null,
		],
	];

	/**
	 * Register all filters.
	 */
	public function register(): void {
		// Allow to filter by gla_syncable.
		add_filter(
			'woocommerce_rest_query_vars',
			function ( $valid_vars ) {
				$valid_vars[] = 'gla_syncable';
				return $valid_vars;
			}
		);

		foreach ( array_keys( self::$post_types_to_filter ) as $object_type ) {
			$this->register_object_types_filter( $object_type );
		}
	}

	/**
	 * Register the filters for a specific object type.
	 *
	 * @param string $object_type The object type.
	 */
	protected function register_object_types_filter( string $object_type ): void {
		add_filter(
			'woocommerce_rest_prepare_' . $object_type . '_object',
			[ $this, 'filter_response_by_syncable_item' ],
			9,
			3
		);

		add_filter(
			'woocommerce_rest_prepare_' . $object_type . '_object',
			[ $this, 'filter_metadata' ],
			10,
			3
		);

		add_filter(
			'woocommerce_rest_' . $object_type . '_object_query',
			[ $this, 'filter_by_metaquery' ],
			10,
			2
		);
	}

	/**
	 * Whether the data should be filtered.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return bool
	 */
	protected function should_filter_data( WP_REST_Request $request ): bool {
		// WPCOM proxy will set the gla_syncable to 1 if the request is coming from the proxy and it is the Google App.
		return $request->get_param( 'gla_syncable' ) === '1';
	}

	/**
	 * Filter response by syncable item.
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param mixed            $item     The item.
	 * @param WP_REST_Request  $request  The request object.
	 *
	 * @return WP_REST_Response The response object updated.
	 */
	public function filter_response_by_syncable_item( $response, $item, WP_REST_Request $request ): WP_REST_Response {
		if ( ! $this->should_filter_data( $request ) ) {
			return $response;
		}

		$route               = $request->get_route();
		$pattern             = '/(?P<resource>[\w]+)\/(?P<id>[\d]+$)/';
		$protected_resources = [
			'products',
			'coupons',
		];
		preg_match( $pattern, $route, $matches );

		if ( ! isset( $matches['id'] ) || ! isset( $matches['resource'] ) || ! in_array( $matches['resource'], $protected_resources, true ) ) {
			return $response;
		}

		$meta_data = $response->get_data()['meta_data'] ?? [];

		foreach ( $meta_data as $meta ) {
			if ( $meta->key === self::KEY_VISIBILITY && $meta->value === ChannelVisibility::SYNC_AND_SHOW ) {
				return $response;
			}
		}

		return new WP_REST_Response(
			[
				'code'    => 'gla_rest_item_no_syncable',
				'message' => 'Item not syncable',
				'data'    => [
					'status' => '403',
				],
			],
			403
		);
	}

	/**
	 * Query items with specific args for example where _wc_gla_visibility is set to sync-and-show.
	 *
	 * @param array           $args    The query args.
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return array The query args updated.
	 * */
	public function filter_by_metaquery( array $args, WP_REST_Request $request ): array {
		if ( ! $this->should_filter_data( $request ) ) {
			return $args;
		}

		$post_type         = $args['post_type'];
		$post_type_filters = self::$post_types_to_filter[ $post_type ];

		if ( ! isset( $post_type_filters['meta_query'] ) || ! is_array( $post_type_filters['meta_query'] ) ) {
			return $args;
		}

		$args['meta_query'] = [ ...$args['meta_query'] ?? [], ...$post_type_filters['meta_query'] ];

		return $args;
	}

	/**
	 * Filter the response metadata returning all public metadata and those prefixed with _wc_gla
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param mixed            $item     The item.
	 * @param WP_REST_Request  $request  The request object.
	 *
	 * @return WP_REST_Response The response object updated.
	 */
	public function filter_metadata( WP_REST_Response $response, $item, WP_REST_Request $request ): WP_REST_Response {
		if ( ! $this->should_filter_data( $request ) ) {
			return $response;
		}

		$data = $response->get_data();

		if ( ! isset( $data['meta_data'] ) ) {
			return $response;
		}

		foreach ( $data['meta_data'] as $key => $meta ) {
			if ( str_starts_with( $meta->key, '_' ) && ! str_starts_with( $meta->key, '_wc_gla' ) ) {
				unset( $data['meta_data'][ $key ] );
			}
		}

		$response->set_data( $data );

		return $response;
	}
}

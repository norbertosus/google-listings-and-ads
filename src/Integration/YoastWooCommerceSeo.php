<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\GoogleListingsAndAds\Integration;

use Automattic\WooCommerce\GoogleListingsAndAds\Infrastructure\Registerable;
use Automattic\WooCommerce\GoogleListingsAndAds\Infrastructure\Service;
use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Class YoastWooCommerceSeo
 *
 * @package Automattic\WooCommerce\GoogleListingsAndAds\Integration
 */
class YoastWooCommerceSeo implements Service, Registerable {

	protected const VALUE_KEY = 'yoast_seo';

	/**
	 * @var array Meta values stored by Yoast WooCommerce SEO plugin.
	 */
	protected $yoast_global_identifiers;

	/**
	 * Register a service.
	 */
	public function register(): void {
		add_action( 'gla_product_attribute_value_options_mpn', [ $this, 'add_value_option' ] );
		add_action( 'gla_product_attribute_value_options_gtin', [ $this, 'add_value_option' ] );
		add_action( 'gla_product_attribute_value_mpn', [ $this, 'get_mpn' ] );
		add_action( 'gla_product_attribute_value_gtin', [ $this, 'get_gtin' ] );
	}

	/**
	 * @param array $value_options
	 *
	 * @return array
	 */
	public function add_value_option( array $value_options ): array {
		$value_options[ self::VALUE_KEY ] = 'From Yoast WooCommerce SEO';

		return $value_options;
	}

	/**
	 * @param mixed      $value
	 * @param WC_Product $product
	 *
	 * @return array
	 */
	public function get_mpn( $value, WC_Product $product ): array {
		if ( self::VALUE_KEY === $value ) {
			$value = $this->get_identifier_value( 'mpn', $product );
		}

		return $value;
	}

	/**
	 * @param mixed      $value
	 * @param WC_Product $product
	 *
	 * @return array
	 */
	public function get_gtin( $value, WC_Product $product ): array {
		if ( self::VALUE_KEY === $value ) {
			$gtin_values = [
				$this->get_identifier_value( 'isbn', $product ),
				$this->get_identifier_value( 'gtin8', $product ),
				$this->get_identifier_value( 'gtin12', $product ),
				$this->get_identifier_value( 'gtin13', $product ),
				$this->get_identifier_value( 'gtin14', $product ),
			];

			$gtin_values = array_values(
				array_filter(
					$gtin_values,
					function ( $value ) {
						return null !== $value;
					}
				)
			);

			$value = $gtin_values[0] ?? null;
		}

		return $value;
	}

	/**
	 * @param string     $key
	 * @param WC_Product $product
	 *
	 * @return mixed|null
	 */
	protected function get_identifier_value( string $key, WC_Product $product ) {
		if ( ! isset( $this->yoast_global_identifiers ) ) {
			$this->yoast_global_identifiers = get_post_meta( $product->get_id(), 'wpseo_global_identifier_values', true );
		}

		return ! empty( $this->yoast_global_identifiers[ $key ] ) ? $this->yoast_global_identifiers[ $key ] : null;
	}
}

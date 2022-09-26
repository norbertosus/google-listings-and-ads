<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\GoogleListingsAndAds\Product\Attributes;

use Automattic\WooCommerce\GoogleListingsAndAds\Admin\Product\Attributes\Input\ColorInput;
use Automattic\WooCommerce\GoogleListingsAndAds\Product\AttributeMappingHelper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Color
 *
 * @package Automattic\WooCommerce\GoogleListingsAndAds\Product\Attributes
 */
class Color extends AbstractAttribute {

	/**
	 * Returns the attribute ID.
	 *
	 * Must be the same as a Google product's property name to be set automatically.
	 *
	 * @return string
	 *
	 * @see \Google\Service\ShoppingContent\Product for the list of properties.
	 */
	public static function get_id(): string {
		return 'color';
	}

	/**
	 * Return an array of WooCommerce product types that this attribute can be applied to.
	 *
	 * @return array
	 */
	public static function get_applicable_product_types(): array {
		return [ 'simple', 'variation' ];
	}

	/**
	 * Return the attribute's input class. Must be an instance of `AttributeInputInterface`.
	 *
	 * @return string
	 *
	 * @see AttributeInputInterface
	 *
	 * @since 1.5.0
	 */
	public static function get_input_type(): string {
		return ColorInput::class;
	}

	/**
	 * Returns the attribute name
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return __( 'Color', 'google-listings-and-ads' );
	}

	/**
	 * Returns the attribute sources
	 *
	 * @return array
	 */
	public static function get_sources(): array {
		return AttributeMappingHelper::get_source_taxonomies();
	}

	public static function is_enum(): bool {
		// Todo: TBD. Replace with some trait or other solution avoiding override
		return false;
	}

}

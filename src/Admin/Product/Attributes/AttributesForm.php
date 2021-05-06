<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\GoogleListingsAndAds\Admin\Product\Attributes;

use Automattic\WooCommerce\GoogleListingsAndAds\Admin\Input\Text;
use Automattic\WooCommerce\GoogleListingsAndAds\Product\Attributes\Brand;
use Automattic\WooCommerce\GoogleListingsAndAds\Product\Attributes\GTIN;
use Automattic\WooCommerce\GoogleListingsAndAds\Product\Attributes\MPN;

defined( 'ABSPATH' ) || exit;

/**
 * Class AttributesForm
 *
 * @package Automattic\WooCommerce\GoogleListingsAndAds\Admin\Product\Attributes
 */
class AttributesForm extends AbstractAttributesForm {
	/**
	 * AttributesForm constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data = [] ) {
		$this->set_name( 'attributes' );

		$this->add_attribute( Brand::class, Text::class )
			 ->add_attribute( GTIN::class, Text::class )
			 ->add_attribute( MPN::class, Text::class );

		parent::__construct( $data );
	}
}

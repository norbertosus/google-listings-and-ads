<?php

namespace Automattic\WooCommerce\GoogleListingsAndAds\Utility;

/**
 * A class for dealing with Dimensions.
 *
 * @since x.x.x
 */
class DimensionUtility {

	/**
	 * Width.
	 *
	 * @var int
	 */
	public int $x;
	/**
	 * Height.
	 *
	 * @var int
	 */
	public int $y;

	/**
	 * DimensionUtility constructor.
	 *
	 * @param int $x Width.
	 * @param int $y Height.
	 */
	public function __construct( int $x, int $y ) {
		$this->x = $x;
		$this->y = $y;
	}

	/**
	 * Checks if the image is bigger than the other one.
	 *
	 * @param DimensionUtility $target The image to be compared.
	 *
	 * @return bool true if the image is bigger than the other one otherwise false.
	 */
	public function is_bigger( DimensionUtility $target ): bool {
		return $this->x >= $target->x && $this->y >= $target->y;
	}

	/**
	 * Checks if the image is equal to the other one with a specific precision.
	 *
	 * @param DimensionUtility $target The image to be compared.
	 * @param int|float        $precision The precision to use when comparing the two numbers.
	 *
	 * @return bool true if the image is equal than the other one otherwise false.
	 */
	public function equals( DimensionUtility $target, $precision = 1 ): bool {
		return wp_fuzzy_number_match( $this->x, $target->x, $precision ) && wp_fuzzy_number_match( $this->y, $target->y, $precision );
	}


}


/**
 * Helper functions for handling the cart.
 *
 * @typedef { import( '@playwright/test' ).Page } Page
 */

/**
 * External dependencies
 */
const { expect } = require( '@playwright/test' );

/**
 * Internal dependencies
 */
const config = require( '../config/default.json' );

/**
 * Adds a single product to the cart.
 *
 * @param {Page} page
 * @param {number} productID
 */
export async function singleProductAddToCart( page, productID ) {
	await page.goto( `?p=${ productID }` );

	const addToCart = '.single_add_to_cart_button';
	await page.locator( addToCart ).first().click();
	await expect( page.locator( '.woocommerce-message' ) ).toContainText(
		'been added to your cart'
	);

	// Wait till all tracking event request have been sent after page reloaded.
	await page.waitForLoadState( 'networkidle' );
}

/**
 * Adds a related product to the cart.
 *
 * @param {Page} page
 * @return {number} Product ID of the added product.
 */
export async function relatedProductAddToCart( page ) {
	const addToCart =
		'.wp-block-woocommerce-related-products .add_to_cart_button';

	await page.locator( addToCart ).first().click();
	await expect( page.locator( addToCart ).first() ).toHaveClass( /added/ );

	return await page.$eval( addToCart, ( el ) => {
		return el.getAttribute( 'data-product_id' );
	} );
}

/**
 * Add a product to the cart from a block shop page.
 *
 * @param {Page} page
 */
export async function blockProductAddToCart( page ) {
	const addToCart =
		'.wp-block-button__link.add_to_cart_button:not([disabled])';

	await page.locator( addToCart ).first().click();
	await expect( page.locator( addToCart ).first() ).toHaveClass( /added/ );
}

/**
 * Perform checkout steps to purchase a product.
 *
 * @param {Page} page
 */
export async function checkout( page ) {
	const user = config.addresses.customer.billing;

	await page.goto( 'checkout' );

	await page.locator( '#billing_first_name' ).fill( user.firstname );
	await page.locator( '#billing_last_name' ).fill( user.lastname );
	await page.locator( '#billing_address_1' ).fill( user.addressfirstline );
	await page.locator( '#billing_city' ).fill( user.city );
	await page.locator( '#billing_state' ).selectOption( user.state );
	await page.locator( '#billing_postcode' ).fill( user.postcode );
	await page.locator( '#billing_phone' ).fill( user.phone );
	await page.locator( '#billing_email' ).fill( user.email );

	await page.locator( 'text=Cash on delivery' ).click();
	await expect( page.locator( 'div.payment_method_cod' ) ).toBeVisible();

	await page.locator( 'text=Place order' ).click();

	await expect(
		page.locator( '.woocommerce-thankyou-order-received' )
	).toContainText( 'order has been received' );
}

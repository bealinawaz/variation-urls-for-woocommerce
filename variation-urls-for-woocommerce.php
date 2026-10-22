<?php
/**
 * Plugin Name:          Variation URLs for WooCommerce – Shareable Links to Product Variations
 * Plugin URI:           https://github.com/bealinawaz/variation-urls-for-woocommerce
 * Description:          Updates the address bar when a shopper picks a product variation, so every variation has a link you can share, bookmark, or use in ads.
 * Version:              2.0.0
 * Requires at least:    6.2
 * Requires PHP:         7.4
 * Requires Plugins:     woocommerce
 * Author:               Ali Nawaz
 * Author URI:           https://bealinawaz.com
 * License:              GPLv2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          variation-urls-for-woocommerce
 * WC requires at least: 7.1
 * WC tested up to:      10.3
 *
 * @package Variation_URLs_For_WooCommerce
 */

defined( 'ABSPATH' ) || exit;

define( 'VURL_VERSION', '2.0.0' );
define( 'VURL_FILE', __FILE__ );

require_once __DIR__ . '/includes/class-vurl-settings.php';
require_once __DIR__ . '/includes/class-vurl-frontend.php';

// The plugin never touches orders or the checkout, so it works with both order storage types and block checkout.
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', VURL_FILE, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', VURL_FILE, true );
		}
	}
);

VURL_Settings::init();
VURL_Frontend::init();

if ( is_admin() ) {
	require_once __DIR__ . '/includes/class-vurl-admin.php';
	VURL_Admin::init();
}

register_activation_hook( __FILE__, array( 'VURL_Settings', 'activate' ) );

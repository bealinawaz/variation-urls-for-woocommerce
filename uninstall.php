<?php
/**
 * Removes the plugin's options.
 *
 * @package Variation_URLs_For_WooCommerce
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'vurl_version' );
delete_option( 'vurl_copy_button' );
delete_option( 'vurl_copy_label' );

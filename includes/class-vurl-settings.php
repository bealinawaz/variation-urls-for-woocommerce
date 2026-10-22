<?php
/**
 * Plugin settings and upgrade routine.
 *
 * @package Variation_URLs_For_WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Reads the settings and runs upgrade tasks.
 */
final class VURL_Settings {

	const VERSION_OPTION = 'vurl_version';
	const COPY_OPTION    = 'vurl_copy_button';
	const LABEL_OPTION   = 'vurl_copy_label';

	/**
	 * Registers hooks.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'maybe_upgrade' ) );
	}

	/**
	 * Whether the Copy link button is shown.
	 *
	 * @return bool
	 */
	public static function copy_button_enabled() {
		return 'no' !== get_option( self::COPY_OPTION, 'yes' );
	}

	/**
	 * The Copy link button label.
	 *
	 * @return string
	 */
	public static function copy_label() {
		$label = trim( (string) get_option( self::LABEL_OPTION, '' ) );
		return '' !== $label ? $label : __( 'Copy link to this option', 'variation-urls-for-woocommerce' );
	}

	/**
	 * Runs on activation. Version 1.0 added a /product/{slug}/{variation}/ rewrite rule,
	 * so the stored rules are flushed to remove it.
	 */
	public static function activate() {
		update_option( self::VERSION_OPTION, VURL_VERSION, false );
		flush_rewrite_rules( false );
	}

	/**
	 * Flushes rewrite rules once after an update, for sites that update without reactivating.
	 */
	public static function maybe_upgrade() {
		if ( get_option( self::VERSION_OPTION ) === VURL_VERSION ) {
			return;
		}
		update_option( self::VERSION_OPTION, VURL_VERSION, false );
		flush_rewrite_rules( false );
	}
}

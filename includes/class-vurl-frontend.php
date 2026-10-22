<?php
/**
 * Product page behavior: the variation link in the address bar and the Copy link button.
 *
 * @package Variation_URLs_For_WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Front-end hooks for variable product pages.
 *
 * WooCommerce already pre-selects a variation from query arguments named after the
 * attribute fields (for example ?attribute_pa_color=blue&attribute_size=Large), so the
 * plugin only has to keep those arguments in the address bar. The canonical URL that
 * WordPress and SEO plugins print is the product permalink without query arguments,
 * so every variation link points search engines to the main product page.
 */
final class VURL_Frontend {

	/**
	 * Registers hooks.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'woocommerce_after_variations_form', array( __CLASS__, 'render_copy_button' ) );
	}

	/**
	 * Whether the current request is a single variable product page.
	 *
	 * @return bool
	 */
	private static function is_variable_product_page() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return false;
		}
		$product = wc_get_product( get_queried_object_id() );
		return $product && $product->is_type( 'variable' );
	}

	/**
	 * Loads the script and styles on variable product pages only.
	 */
	public static function enqueue() {
		/**
		 * Filters whether the variation URL script loads on the current page.
		 * Return true to load it on pages that show variable products in other ways, such as quick view.
		 *
		 * @param bool $load Whether to load the script.
		 */
		if ( ! apply_filters( 'vurl_load_assets', self::is_variable_product_page() ) ) {
			return;
		}

		$url = plugin_dir_url( VURL_FILE ) . 'assets/';
		// WooCommerce's variation form is built on jQuery and announces changes with jQuery events.
		wp_enqueue_script( 'vurl-frontend', $url . 'frontend.js', array( 'jquery' ), VURL_VERSION, array( 'in_footer' => true ) );
		wp_localize_script(
			'vurl-frontend',
			'vurlSettings',
			array(
				'copied' => __( 'Link copied.', 'variation-urls-for-woocommerce' ),
				'failed' => __( 'Could not copy the link. Copy it from the address bar instead.', 'variation-urls-for-woocommerce' ),
			)
		);

		if ( VURL_Settings::copy_button_enabled() ) {
			wp_enqueue_style( 'vurl-frontend', $url . 'frontend.css', array(), VURL_VERSION );
		}
	}

	/**
	 * Prints the Copy link button inside the variation form. It stays hidden until
	 * the shopper has chosen a complete variation.
	 */
	public static function render_copy_button() {
		if ( ! VURL_Settings::copy_button_enabled() ) {
			return;
		}
		?>
		<div class="vurl-share" hidden>
			<button type="button" class="button vurl-share__button"><?php echo esc_html( VURL_Settings::copy_label() ); ?></button>
			<span class="vurl-share__status" role="status" aria-live="polite"></span>
		</div>
		<?php
	}

	/**
	 * Builds the shareable link for a variation, or an empty string when the variation
	 * has an attribute set to "Any", which a link cannot pick for the shopper.
	 *
	 * @param WC_Product_Variation $variation Variation.
	 * @return string
	 */
	public static function variation_url( $variation ) {
		$args = array();
		foreach ( $variation->get_variation_attributes() as $key => $value ) {
			if ( '' === $value ) {
				return '';
			}
			$args[ $key ] = rawurlencode( $value );
		}
		return add_query_arg( $args, get_permalink( $variation->get_parent_id() ) );
	}
}

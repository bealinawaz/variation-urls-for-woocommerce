<?php
/**
 * Settings section, plugin links, and variation links in the product editor.
 *
 * @package Variation_URLs_For_WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin hooks.
 */
final class VURL_Admin {

	const SECTION     = 'vurl';
	const AUTHOR_URL  = 'https://bealinawaz.com/';
	const CONTACT_URL = 'https://bealinawaz.com/contact-me/';
	const DONATE_URL  = 'https://www.buymeacoffee.com/bealinawaz';

	/**
	 * Registers hooks.
	 */
	public static function init() {
		add_filter( 'woocommerce_get_sections_products', array( __CLASS__, 'add_section' ) );
		add_filter( 'woocommerce_get_settings_products', array( __CLASS__, 'settings' ), 10, 2 );
		add_action( 'woocommerce_after_settings_products', array( __CLASS__, 'maybe_render_support' ) );
		add_action( 'woocommerce_product_after_variable_attributes', array( __CLASS__, 'render_variation_link' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( VURL_FILE ), array( __CLASS__, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'row_meta' ), 10, 2 );
	}

	/**
	 * Settings screen URL.
	 *
	 * @return string
	 */
	public static function settings_url() {
		return admin_url( 'admin.php?page=wc-settings&tab=products&section=' . self::SECTION );
	}

	/**
	 * Whether the current screen is this plugin's settings section.
	 *
	 * @return bool
	 */
	private static function is_settings_screen() {
		global $current_section;
		return self::SECTION === $current_section;
	}

	/**
	 * Adds the "Variation links" section under WooCommerce → Settings → Products.
	 *
	 * @param array $sections Sections.
	 * @return array
	 */
	public static function add_section( $sections ) {
		$sections[ self::SECTION ] = __( 'Variation links', 'variation-urls-for-woocommerce' );
		return $sections;
	}

	/**
	 * Settings fields for the section. WooCommerce saves and sanitizes them.
	 *
	 * @param array  $settings        Settings of the current section.
	 * @param string $current_section Current section.
	 * @return array
	 */
	public static function settings( $settings, $current_section ) {
		if ( self::SECTION !== $current_section ) {
			return $settings;
		}
		return array(
			array(
				'title' => __( 'Variation links', 'variation-urls-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'When a shopper picks a variation, the address bar changes to a link that opens the product with the same options selected, for example ?attribute_pa_color=blue. The canonical URL stays on the main product page, so search engines do not index each variation separately.', 'variation-urls-for-woocommerce' ),
				'id'    => 'vurl_options',
			),
			array(
				'title'   => __( 'Copy link button', 'variation-urls-for-woocommerce' ),
				'desc'    => __( 'Show a button that copies the link to the selected variation', 'variation-urls-for-woocommerce' ),
				'id'      => VURL_Settings::COPY_OPTION,
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'       => __( 'Button label', 'variation-urls-for-woocommerce' ),
				'desc_tip'    => __( 'Leave empty to use the default label.', 'variation-urls-for-woocommerce' ),
				'id'          => VURL_Settings::LABEL_OPTION,
				'type'        => 'text',
				'default'     => '',
				'placeholder' => __( 'Copy link to this option', 'variation-urls-for-woocommerce' ),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'vurl_options',
			),
		);
	}

	/**
	 * Prints the support box below the settings section.
	 */
	public static function maybe_render_support() {
		if ( ! self::is_settings_screen() ) {
			return;
		}
		?>
		<div class="vurl-support">
			<h2><?php esc_html_e( 'Need help or a custom version?', 'variation-urls-for-woocommerce' ); ?></h2>
			<p>
				<?php
				printf(
					/* translators: %s: link to the author's website. */
					esc_html__( 'Variation URLs for WooCommerce is free and built by Ali Nawaz, a WordPress and WooCommerce developer at %s. Need variation swatches, SEO-friendly variation pages, or a custom product configurator?', 'variation-urls-for-woocommerce' ),
					'<a href="' . esc_url( self::AUTHOR_URL ) . '" target="_blank" rel="noopener">bealinawaz.com</a>'
				);
				?>
			</p>
			<p class="vurl-support__actions">
				<a class="button button-primary" href="<?php echo esc_url( self::CONTACT_URL ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Hire me for custom work', 'variation-urls-for-woocommerce' ); ?></a>
				<a class="button" href="<?php echo esc_url( self::AUTHOR_URL ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Visit bealinawaz.com', 'variation-urls-for-woocommerce' ); ?></a>
				<a class="button" href="<?php echo esc_url( self::DONATE_URL ); ?>" target="_blank" rel="noopener">&#9749; <?php esc_html_e( 'Buy me a coffee', 'variation-urls-for-woocommerce' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Shows a ready-to-share link in each variation's panel on the product edit screen.
	 *
	 * @param int     $loop           Variation index.
	 * @param array   $variation_data Variation data.
	 * @param WP_Post $variation      Variation post.
	 */
	public static function render_variation_link( $loop, $variation_data, $variation ) {
		$product = wc_get_product( $variation->ID );
		if ( ! $product || ! $product->is_type( 'variation' ) || 'publish' !== get_post_status( $product->get_parent_id() ) ) {
			return;
		}
		$url = VURL_Frontend::variation_url( $product );
		$id  = 'vurl-link-' . absint( $loop );
		?>
		<p class="form-row form-row-full vurl-variation-link">
			<label for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Link to this variation', 'variation-urls-for-woocommerce' ); ?></label>
			<?php if ( '' === $url ) : ?>
				<span class="description"><?php esc_html_e( 'Set every attribute to a specific value (not "Any") to get a link that selects this variation.', 'variation-urls-for-woocommerce' ); ?></span>
			<?php else : ?>
				<input type="url" id="<?php echo esc_attr( $id ); ?>" class="code" value="<?php echo esc_attr( $url ); ?>" readonly />
			<?php endif; ?>
		</p>
		<?php
	}

	/**
	 * Loads admin styles on the settings section and the product editor.
	 *
	 * @param string $hook Current admin page.
	 */
	public static function enqueue( $hook ) {
		$screen = get_current_screen();
		$is_product_editor = $screen && 'product' === $screen->id;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only reads which settings tab is open.
		$is_settings = 'woocommerce_page_wc-settings' === $hook && isset( $_GET['section'] ) && self::SECTION === sanitize_key( wp_unslash( $_GET['section'] ) );
		if ( ! $is_product_editor && ! $is_settings ) {
			return;
		}
		wp_enqueue_style( 'vurl-admin', plugin_dir_url( VURL_FILE ) . 'assets/admin.css', array(), VURL_VERSION );
	}

	/**
	 * Adds a Settings link on the Plugins screen.
	 *
	 * @param array $links Links.
	 * @return array
	 */
	public static function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( self::settings_url() ) . '">' . esc_html__( 'Settings', 'variation-urls-for-woocommerce' ) . '</a>' );
		return $links;
	}

	/**
	 * Adds custom development and donation links under the plugin description.
	 *
	 * @param array  $links Plugin meta links.
	 * @param string $file  Plugin file.
	 * @return array
	 */
	public static function row_meta( $links, $file ) {
		if ( plugin_basename( VURL_FILE ) !== $file ) {
			return $links;
		}
		$links[] = '<a href="' . esc_url( self::CONTACT_URL ) . '" target="_blank" rel="noopener">' . esc_html__( 'Custom development', 'variation-urls-for-woocommerce' ) . '</a>';
		$links[] = '<a href="' . esc_url( self::DONATE_URL ) . '" target="_blank" rel="noopener">' . esc_html__( 'Buy me a coffee', 'variation-urls-for-woocommerce' ) . '</a>';
		return $links;
	}
}

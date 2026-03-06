<?php
/**
 * Admin settings page handling.
 *
 * @package WebkihSecureSitemapGenerator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin settings controller.
 */
class WBSSG_Admin_Settings {

	/**
	 * Singleton instance.
	 *
	 * @var WBSSG_Admin_Settings|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return WBSSG_Admin_Settings
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_post_wbssg_regenerate_sitemap', array( $this, 'handle_manual_regeneration' ) );
	}

	/**
	 * Register settings page.
	 *
	 * @return void
	 */
	public function register_settings_page() {
		add_options_page(
			esc_html__( 'Sitemap Generator', 'webkih-secure-sitemap-generator' ),
			esc_html__( 'Sitemap Generator', 'webkih-secure-sitemap-generator' ),
			'manage_options',
			'webkih-secure-sitemap-generator',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register plugin setting.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'wbssg_settings_group',
			'wbssg_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * Sanitize settings values.
	 *
	 * @param array $input Raw input.
	 *
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$input = is_array( $input ) ? $input : array();

		$sanitized = array(
			'enabled'             => isset( $input['enabled'] ) ? 1 : 0,
			'excluded_post_types' => array(),
			'changefreq'          => 'weekly',
			'priority'            => '0.5',
			'ping_search_engines' => isset( $input['ping_search_engines'] ) ? 1 : 0,
		);

		if ( isset( $input['excluded_post_types'] ) && is_array( $input['excluded_post_types'] ) ) {
			$public_post_types = get_post_types( array( 'public' => true ), 'names' );
			foreach ( $input['excluded_post_types'] as $post_type ) {
				$post_type = sanitize_key( $post_type );
				if ( in_array( $post_type, $public_post_types, true ) ) {
					$sanitized['excluded_post_types'][] = $post_type;
				}
			}
		}

		if ( isset( $input['changefreq'] ) ) {
			$allowed_changefreq = array( 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never' );
			$changefreq         = sanitize_key( $input['changefreq'] );
			if ( in_array( $changefreq, $allowed_changefreq, true ) ) {
				$sanitized['changefreq'] = $changefreq;
			}
		}

		if ( isset( $input['priority'] ) ) {
			$priority = (float) sanitize_text_field( wp_unslash( $input['priority'] ) );
			if ( $priority >= 0 && $priority <= 1 ) {
				$sanitized['priority'] = number_format( $priority, 1, '.', '' );
			}
		}

		return $sanitized;
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$generator = WBSSG_Sitemap_Generator::get_instance();
		$settings  = $generator->get_settings();
		include WBSSG_PLUGIN_PATH . 'admin/settings-page.php';
	}

	/**
	 * Handle manual sitemap regeneration.
	 *
	 * @return void
	 */
	public function handle_manual_regeneration() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to perform this action.', 'webkih-secure-sitemap-generator' ) );
		}

		if ( ! isset( $_POST['wbssg_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['wbssg_nonce'] ) ), 'wbssg_save_settings_nonce' ) ) {
			wp_die( esc_html__( 'Invalid security token.', 'webkih-secure-sitemap-generator' ) );
		}

		WBSSG_Sitemap_Generator::get_instance()->regenerate_sitemap();

		$redirect_url = add_query_arg(
			array(
				'page'               => 'webkih-secure-sitemap-generator',
				'wbssg_regenerated'    => 1,
			),
			admin_url( 'options-general.php' )
		);

		wp_safe_redirect( esc_url_raw( $redirect_url ) );
		exit;
	}
}

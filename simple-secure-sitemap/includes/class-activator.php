<?php
/**
 * Activation logic for the plugin.
 *
 * @package SimpleSecureSitemap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin activation.
 */
class SSS_Activator {

	/**
	 * Activate plugin.
	 *
	 * @return void
	 */
	public static function activate() {
		$default_settings = array(
			'enabled'            => 1,
			'excluded_post_types' => array(),
			'changefreq'         => 'weekly',
			'priority'           => '0.5',
			'ping_search_engines' => 1,
		);

		if ( false === get_option( 'sss_settings', false ) ) {
			add_option( 'sss_settings', $default_settings );
		}

		SSS_Sitemap_Generator::add_rewrite_rule();
		flush_rewrite_rules();
	}
}

<?php
/**
 * Activation logic for the plugin.
 *
 * @package WebkihSecureSitemapGenerator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin activation.
 */
class WBSSG_Activator {

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

		if ( false === get_option( 'wbssg_settings', false ) ) {
			add_option( 'wbssg_settings', $default_settings );
		}

		WBSSG_Sitemap_Generator::add_rewrite_rule();
		flush_rewrite_rules();
	}
}

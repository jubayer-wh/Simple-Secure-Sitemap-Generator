<?php
/**
 * Plugin Name:       WEBKIH Secure Sitemap Generator
 * Plugin URI:        https://github.com/jubayer-wh/WEBKIH-Secure-Sitemap-Generator
 * Description:       Lightweight and secure XML sitemap generator with post type filters, manual regeneration, and optional search engine ping.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Jubayer Hossain
 * Author URI:        https://webkih.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       webkih-secure-sitemap-generator
 * Domain Path:       /languages
 *
 * @package WebkihSecureSitemapGenerator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WBSSG_PLUGIN_VERSION', '1.0.0' );
define( 'WBSSG_PLUGIN_FILE', __FILE__ );
define( 'WBSSG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WBSSG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once WBSSG_PLUGIN_PATH . 'includes/class-activator.php';
require_once WBSSG_PLUGIN_PATH . 'includes/class-sitemap-generator.php';
require_once WBSSG_PLUGIN_PATH . 'includes/class-admin-settings.php';

register_activation_hook( __FILE__, array( 'WBSSG_Activator', 'activate' ) );

/**
 * Boot plugin classes.
 *
 * @return void
 */
function wbssg_init_plugin() {
	WBSSG_Sitemap_Generator::get_instance();
	WBSSG_Admin_Settings::get_instance();
}
add_action( 'plugins_loaded', 'wbssg_init_plugin' );

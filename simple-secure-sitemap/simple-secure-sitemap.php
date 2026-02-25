<?php
/**
 * Plugin Name:       Simple Secure Sitemap Generator
 * Plugin URI:        https://example.com/plugins/simple-secure-sitemap/
 * Description:       Lightweight and secure XML sitemap generator with post type filters, manual regeneration, and optional search engine ping.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Simple Secure Sitemap Team
 * Author URI:        https://example.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-secure-sitemap
 * Domain Path:       /languages
 *
 * @package SimpleSecureSitemap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SSS_PLUGIN_VERSION', '1.0.0' );
define( 'SSS_PLUGIN_FILE', __FILE__ );
define( 'SSS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'SSS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once SSS_PLUGIN_PATH . 'includes/class-activator.php';
require_once SSS_PLUGIN_PATH . 'includes/class-sitemap-generator.php';
require_once SSS_PLUGIN_PATH . 'includes/class-admin-settings.php';

register_activation_hook( __FILE__, array( 'SSS_Activator', 'activate' ) );

/**
 * Boot plugin classes.
 *
 * @return void
 */
function sss_init_plugin() {
	SSS_Sitemap_Generator::get_instance();
	SSS_Admin_Settings::get_instance();
}
add_action( 'plugins_loaded', 'sss_init_plugin' );

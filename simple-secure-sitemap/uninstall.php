<?php
/**
 * Uninstall cleanup.
 *
 * @package SimpleSecureSitemap
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'sss_settings' );

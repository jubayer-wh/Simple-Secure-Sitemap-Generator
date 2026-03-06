<?php
/**
 * Admin settings page template.
 *
 * @package WebkihSecureSitemapGenerator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$public_post_types = get_post_types(
	array(
		'public' => true,
	),
	'objects'
);

$settings_updated = filter_input( INPUT_GET, 'settings-updated', FILTER_VALIDATE_BOOLEAN );
$wbssg_regenerated  = filter_input( INPUT_GET, 'wbssg_regenerated', FILTER_VALIDATE_BOOLEAN );
?>
<div class="wrap">
	<h1><?php echo esc_html__( 'Webkih Secure Sitemap Generator', 'webkih-secure-sitemap-generator' ); ?></h1>

	<?php if ( $settings_updated ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo esc_html__( 'Settings saved.', 'webkih-secure-sitemap-generator' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $wbssg_regenerated ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo esc_html__( 'Sitemap regenerated and ping request sent.', 'webkih-secure-sitemap-generator' ); ?></p>
		</div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'wbssg_settings_group' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php echo esc_html__( 'Enable sitemap', 'webkih-secure-sitemap-generator' ); ?></th>
				<td>
					<label for="wbssg_enabled">
						<input type="checkbox" id="wbssg_enabled" name="wbssg_settings[enabled]" value="1" <?php checked( 1, (int) $settings['enabled'] ); ?> />
						<?php echo esc_html__( 'Enable custom sitemap endpoint at /sitemap.xml', 'webkih-secure-sitemap-generator' ); ?>
					</label>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php echo esc_html__( 'Exclude post types', 'webkih-secure-sitemap-generator' ); ?></th>
				<td>
					<?php foreach ( $public_post_types as $post_type ) : ?>
						<label for="wbssg_excluded_<?php echo esc_attr( $post_type->name ); ?>">
							<input
								type="checkbox"
								id="wbssg_excluded_<?php echo esc_attr( $post_type->name ); ?>"
								name="wbssg_settings[excluded_post_types][]"
								value="<?php echo esc_attr( $post_type->name ); ?>"
								<?php checked( in_array( $post_type->name, $settings['excluded_post_types'], true ) ); ?>
							/>
							<?php echo esc_html( $post_type->labels->singular_name ); ?>
						</label><br />
					<?php endforeach; ?>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="wbssg_changefreq"><?php echo esc_html__( 'Default change frequency', 'webkih-secure-sitemap-generator' ); ?></label>
				</th>
				<td>
					<select id="wbssg_changefreq" name="wbssg_settings[changefreq]">
						<?php
						$changefreq_options = array( 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never' );
						foreach ( $changefreq_options as $option ) :
							?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $settings['changefreq'], $option ); ?>>
								<?php echo esc_html( ucfirst( $option ) ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="wbssg_priority"><?php echo esc_html__( 'Default priority (0.0 - 1.0)', 'webkih-secure-sitemap-generator' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="wbssg_priority"
						name="wbssg_settings[priority]"
						value="<?php echo esc_attr( $settings['priority'] ); ?>"
						step="0.1"
						min="0"
						max="1"
					/>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php echo esc_html__( 'Ping search engines', 'webkih-secure-sitemap-generator' ); ?></th>
				<td>
					<label for="wbssg_ping_search_engines">
						<input type="checkbox" id="wbssg_ping_search_engines" name="wbssg_settings[ping_search_engines]" value="1" <?php checked( 1, (int) $settings['ping_search_engines'] ); ?> />
						<?php echo esc_html__( 'Notify search engines after sitemap regeneration.', 'webkih-secure-sitemap-generator' ); ?>
					</label>
				</td>
			</tr>
		</table>

		<?php submit_button( esc_html__( 'Save Settings', 'webkih-secure-sitemap-generator' ) ); ?>
	</form>

	<hr />

	<h2><?php echo esc_html__( 'Manual Regeneration', 'webkih-secure-sitemap-generator' ); ?></h2>
	<p><?php echo esc_html__( 'Use this button to manually trigger sitemap regeneration and optional ping.', 'webkih-secure-sitemap-generator' ); ?></p>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="wbssg_regenerate_sitemap" />
		<?php wp_nonce_field( 'wbssg_save_settings_nonce', 'wbssg_nonce' ); ?>
		<?php submit_button( esc_html__( 'Regenerate Sitemap', 'webkih-secure-sitemap-generator' ), 'secondary' ); ?>
	</form>

	<p>
		<strong><?php echo esc_html__( 'Sitemap URL:', 'webkih-secure-sitemap-generator' ); ?></strong>
		<a href="<?php echo esc_url( home_url( '/sitemap.xml' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( home_url( '/sitemap.xml' ) ); ?></a>
	</p>
</div>

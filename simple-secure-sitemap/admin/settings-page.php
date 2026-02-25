<?php
/**
 * Admin settings page template.
 *
 * @package SimpleSecureSitemap
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
$sss_regenerated  = filter_input( INPUT_GET, 'sss_regenerated', FILTER_VALIDATE_BOOLEAN );
?>
<div class="wrap">
	<h1><?php echo esc_html__( 'Simple Secure Sitemap Generator', 'simple-secure-sitemap' ); ?></h1>

	<?php if ( $settings_updated ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo esc_html__( 'Settings saved.', 'simple-secure-sitemap' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $sss_regenerated ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo esc_html__( 'Sitemap regenerated and ping request sent.', 'simple-secure-sitemap' ); ?></p>
		</div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'sss_settings_group' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php echo esc_html__( 'Enable sitemap', 'simple-secure-sitemap' ); ?></th>
				<td>
					<label for="sss_enabled">
						<input type="checkbox" id="sss_enabled" name="sss_settings[enabled]" value="1" <?php checked( 1, (int) $settings['enabled'] ); ?> />
						<?php echo esc_html__( 'Enable custom sitemap endpoint at /sitemap.xml', 'simple-secure-sitemap' ); ?>
					</label>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php echo esc_html__( 'Exclude post types', 'simple-secure-sitemap' ); ?></th>
				<td>
					<?php foreach ( $public_post_types as $post_type ) : ?>
						<label for="sss_excluded_<?php echo esc_attr( $post_type->name ); ?>">
							<input
								type="checkbox"
								id="sss_excluded_<?php echo esc_attr( $post_type->name ); ?>"
								name="sss_settings[excluded_post_types][]"
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
					<label for="sss_changefreq"><?php echo esc_html__( 'Default change frequency', 'simple-secure-sitemap' ); ?></label>
				</th>
				<td>
					<select id="sss_changefreq" name="sss_settings[changefreq]">
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
					<label for="sss_priority"><?php echo esc_html__( 'Default priority (0.0 - 1.0)', 'simple-secure-sitemap' ); ?></label>
				</th>
				<td>
					<input
						type="number"
						id="sss_priority"
						name="sss_settings[priority]"
						value="<?php echo esc_attr( $settings['priority'] ); ?>"
						step="0.1"
						min="0"
						max="1"
					/>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php echo esc_html__( 'Ping search engines', 'simple-secure-sitemap' ); ?></th>
				<td>
					<label for="sss_ping_search_engines">
						<input type="checkbox" id="sss_ping_search_engines" name="sss_settings[ping_search_engines]" value="1" <?php checked( 1, (int) $settings['ping_search_engines'] ); ?> />
						<?php echo esc_html__( 'Notify search engines after sitemap regeneration.', 'simple-secure-sitemap' ); ?>
					</label>
				</td>
			</tr>
		</table>

		<?php submit_button( esc_html__( 'Save Settings', 'simple-secure-sitemap' ) ); ?>
	</form>

	<hr />

	<h2><?php echo esc_html__( 'Manual Regeneration', 'simple-secure-sitemap' ); ?></h2>
	<p><?php echo esc_html__( 'Use this button to manually trigger sitemap regeneration and optional ping.', 'simple-secure-sitemap' ); ?></p>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="sss_regenerate_sitemap" />
		<?php wp_nonce_field( 'sss_save_settings_nonce', 'sss_nonce' ); ?>
		<?php submit_button( esc_html__( 'Regenerate Sitemap', 'simple-secure-sitemap' ), 'secondary' ); ?>
	</form>

	<p>
		<strong><?php echo esc_html__( 'Sitemap URL:', 'simple-secure-sitemap' ); ?></strong>
		<a href="<?php echo esc_url( home_url( '/sitemap.xml' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( home_url( '/sitemap.xml' ) ); ?></a>
	</p>
</div>

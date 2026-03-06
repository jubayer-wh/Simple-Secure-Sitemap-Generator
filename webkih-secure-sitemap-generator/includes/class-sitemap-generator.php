<?php
/**
 * Sitemap generation and routing.
 *
 * @package WebkihSecureSitemapGenerator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main sitemap generator class.
 */
class WBSSG_Sitemap_Generator {

	/**
	 * Singleton instance.
	 *
	 * @var WBSSG_Sitemap_Generator|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return WBSSG_Sitemap_Generator
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
		add_action( 'init', array( __CLASS__, 'add_rewrite_rule' ) );
		add_filter( 'query_vars', array( $this, 'register_query_var' ) );
		add_action( 'template_redirect', array( $this, 'maybe_render_sitemap' ) );
		add_action( 'save_post', array( $this, 'maybe_regenerate_on_save' ), 10, 3 );
	}

	/**
	 * Register rewrite rule for sitemap endpoint.
	 *
	 * @return void
	 */
	public static function add_rewrite_rule() {
		add_rewrite_rule(
			'^sitemap\.xml$',
			'index.php?wbssg_sitemap=1',
			'top'
		);
	}

	/**
	 * Add query var.
	 *
	 * @param array $vars Public query vars.
	 *
	 * @return array
	 */
	public function register_query_var( $vars ) {
		$vars[] = 'wbssg_sitemap';
		return $vars;
	}

	/**
	 * Render XML sitemap for endpoint.
	 *
	 * @return void
	 */
	public function maybe_render_sitemap() {
		if ( '1' !== get_query_var( 'wbssg_sitemap' ) ) {
			return;
		}

		$settings = $this->get_settings();
		if ( empty( $settings['enabled'] ) ) {
			status_header( 404 );
			exit;
		}

		nocache_headers();
		header( 'Content-Type: application/xml; charset=utf-8' );

		echo $this->build_sitemap_xml(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- XML is escaped in builder.
		exit;
	}

	/**
	 * Generate sitemap after publish updates.
	 *
	 * @param int      $post_id Post ID.
	 * @param WP_Post  $post    Post object.
	 * @param bool     $update  Whether post is updated.
	 *
	 * @return void
	 */
	public function maybe_regenerate_on_save( $post_id, $post, $update ) {
		unset( $update );

		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! $post instanceof WP_Post || 'publish' !== $post->post_status ) {
			return;
		}

		$this->regenerate_sitemap();
	}

	/**
	 * Trigger sitemap regeneration side effects.
	 *
	 * @return void
	 */
	public function regenerate_sitemap() {
		$settings = $this->get_settings();
		if ( ! empty( $settings['ping_search_engines'] ) ) {
			$this->ping_search_engines();
		}
	}

	/**
	 * Build sitemap XML content.
	 *
	 * @return string
	 */
	public function build_sitemap_xml() {
		$settings   = $this->get_settings();
		$post_types = $this->get_included_post_types( $settings );
		$changefreq = $settings['changefreq'];
		$priority   = $settings['priority'];

		$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		$args  = array(
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'no_found_rows'  => true,
			'orderby'        => 'modified',
			'order'          => 'DESC',
			'fields'         => 'ids',
		);
		$query = new WP_Query( $args );

		if ( ! empty( $query->posts ) ) {
			foreach ( $query->posts as $post_id ) {
				$xml .= $this->build_url_node( $post_id, $changefreq, $priority );
			}
		}

		$xml .= '</urlset>';

		return $xml;
	}

	/**
	 * Build single url node.
	 *
	 * @param int    $post_id    Post ID.
	 * @param string $changefreq Change frequency.
	 * @param string $priority   Priority.
	 *
	 * @return string
	 */
	private function build_url_node( $post_id, $changefreq, $priority ) {
		$loc     = esc_url( get_permalink( $post_id ) );
		$lastmod = esc_html( get_post_modified_time( 'c', true, $post_id ) );

		$node  = "\t<url>\n";
		$node .= "\t\t<loc>{$loc}</loc>\n";
		$node .= "\t\t<lastmod>{$lastmod}</lastmod>\n";
		$node .= "\t\t<changefreq>" . esc_html( $changefreq ) . "</changefreq>\n";
		$node .= "\t\t<priority>" . esc_html( $priority ) . "</priority>\n";
		$node .= "\t</url>\n";

		return $node;
	}

	/**
	 * Get post types to include in sitemap.
	 *
	 * @param array $settings Plugin settings.
	 *
	 * @return array
	 */
	private function get_included_post_types( $settings ) {
		$public_post_types = get_post_types(
			array(
				'public' => true,
			),
			'names'
		);

		$excluded = array();
		if ( ! empty( $settings['excluded_post_types'] ) && is_array( $settings['excluded_post_types'] ) ) {
			$excluded = array_map( 'sanitize_key', $settings['excluded_post_types'] );
		}

		$post_types = array_values( array_diff( $public_post_types, $excluded ) );

		if ( empty( $post_types ) ) {
			return array( 'post', 'page' );
		}

		return $post_types;
	}

	/**
	 * Ping search engines with sitemap URL.
	 *
	 * @return void
	 */
	private function ping_search_engines() {
		$sitemap_url = home_url( '/sitemap.xml' );
		$endpoints   = array(
			'https://www.bing.com/ping?sitemap=',
		);

		foreach ( $endpoints as $endpoint ) {
			$ping_url = $endpoint . rawurlencode( $sitemap_url );
			$result   = wp_remote_get( esc_url_raw( $ping_url ), array( 'timeout' => 5 ) );

			if ( is_wp_error( $result ) ) {
				continue;
			}
		}
	}

	/**
	 * Fetch settings with defaults.
	 *
	 * @return array
	 */
	public function get_settings() {
		$defaults = array(
			'enabled'             => 1,
			'excluded_post_types' => array(),
			'changefreq'          => 'weekly',
			'priority'            => '0.5',
			'ping_search_engines' => 1,
		);

		$settings = get_option( 'wbssg_settings', array() );
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		return wp_parse_args( $settings, $defaults );
	}
}

=== Simple Secure Sitemap Generator ===
Contributors: simple-secure-team
Tags: sitemap, xml sitemap, seo, security, performance
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate a secure, customizable, and lightweight XML sitemap with post type filtering, manual regeneration, and optional search engine ping.

== Description ==

Simple Secure Sitemap Generator provides a focused alternative to larger SEO suites:

* Generates XML sitemap at `/sitemap.xml`
* Supports posts, pages, and public custom post types
* Lets administrators exclude selected post types
* Offers default `changefreq` and `priority` controls
* Regenerates automatically when published content changes
* Includes manual regenerate action
* Optionally pings search engines after regeneration
* Built with WordPress security best practices and coding standards in mind

== Installation ==

1. Upload the `simple-secure-sitemap` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **Settings → Sitemap Generator**.
4. Configure options and save.

== Frequently Asked Questions ==

= Why use this plugin if WordPress already has a core sitemap? =

This plugin provides extra control over included content, manual regeneration, and search engine ping in a lightweight package.

= Where is my sitemap located? =

Your sitemap is available at `https://example.com/sitemap.xml`.

= Is this multisite compatible? =

Yes. Settings are stored per-site and the plugin works on multisite installations.

== Changelog ==

= 1.0.0 =
* Initial release.
* Secure XML sitemap generation and settings UI.
* Manual regenerate action and optional search engine ping.

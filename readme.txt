=== Personio Integration Light ===
Contributors: laolaweb, threadi
Tags: personio, jobs, recruitment
Requires at least: 5.9.3
Tested up to: 6.1
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 1.1.1

== Description ==

Import and display your positions from [Personio](https://www.personio.com/) directly on your website. Get full control over how they are displayed.

#### Features

- manual or automatic import of open positions
- import of positions in German and English
- display via 2 Gutenberg blocks, 2 classic widgets or via individual [shortcodes](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/shortcodes.md)
- search engine indexable output (SEO) of list views and open positions
- open position (job description) under website's own URL
- data protection friendly, as no applicant data is collected and stored
- support for classic as well as block themes
- some [WP CLI commands](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/cli.md) for simplified handling of data

#### Requirements

- Personio account with XML interface enabled
- PHP module simpleXML

#### Compatibility tested with

- WPML
- Polylang
- Post Types Order
- Elementor, Themify, Beaver Builder, SiteOrigin (SiteOrigin Widgets Bundle necessary), WPPageBuilder

#### the Pro license includes:

- manual sorting of open positions in list views via drag&drop
- supports all languages Personio offers German, English, French, Spanish, Dutch, Italian, Portuguese
- additional import settings, e.g. import intervals and partial import for very large lists of open jobs
- RichSnippets for optimal findability via search engines like Google
- Customization of slugs (URLs) for list and detailed views of positions
- Shortcode generator for individual views of lists and details
- Extensions for the following PageBuilders: Elementor, Themify, Beaver Builder, SiteOrigin (SiteOrigin Widgets Bundle necessary), WPPageBuilder.

[get the Pro-Version](https://laolaweb.com/plugins/personio-wordpress-plugin/)

The development repository is on [GitHub](https://github.com/threadi/wp-personio-integration-light).

---

== Installation ==

1. Upload "personio-integration-light" to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Enter your Personio URL in the settings.
4. Include one of the different output options for open positions in your company in your website.

== Frequently Asked Questions ==

= Can I use the plugin without a Personio account? =

The plugin can be installed even without Personio account. However, it is not usable without Personio data.

= Does this plugin use iframes? =

No, no iframe of any kind is used to embed data.

= How can I use a form as an application form and submit the applications to Personio? =

No, this is not possible (at this moment). In order to send an application, the applicant must access the link to the Personio website.

== Screenshots ==

1. Field to insert your Personio URL
2. Import-Settings
3. List of imported positions
4. Gutenberg Block for listings

== Changelog ==

= 1.0.0 =
* Initial release

= 1.1.0 =
* Add some hooks for changes in position-listings
* Optimized loading of filters in listings
* Compatibility with WordPress 6.0.2

= 1.1.1 =
* Fixed usage of templates in child-themes

= 1.2.0 =
* Added limit for list of positions in frontend to 10 per list and without pagination
* Optimized compatibility with other plugins using JavaScript in wp-admin
* Compatibility with WordPress 6.1
* Fixed visibility of settings-link in plugin-list
* Fixed pagination of position-list
* Changed some texts
* Update dependencies for Gutenberg-scripts
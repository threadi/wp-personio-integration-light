=== Personio Integration Light ===
Contributors: laolaweb, threadi
Tags: personio, jobs, recruitment
Requires at least: 5.9.3
Tested up to: 6.0.1
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 1.0.0

== Description ==

This plugin provides support for personnel management with [Personio](https://www.personio.com/). Open positions can be imported from Personio and displayed in the website. Applicants can apply for these jobs directly.

#### Features

- manual or automatic import of open positions
- import of positions in German and English
- display via 2 Gutenberg blocks, 2 classic widgets or via individual shortcode
- search engine indexable output (SEO) of list views and open positions
- open position (job description) under website's own URL
- data protection friendly, as no applicant data is collected and stored
- support for classic as well as block themes

#### Requirements

- Personio account with XML interface enabled
- PHP module simpleXML
- PHP module curl

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

[more information about the Pro-Version](https://laolaweb.com/plugins/personio-wordpress-plugin/)

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
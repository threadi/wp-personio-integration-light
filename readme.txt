=== Personio Integration Light ===
Contributors: laolaweb, threadi
Tags: personio, jobs, recruitment, employee
Requires at least: 4.9.24
Tested up to: 6.7
Requires PHP: 8.1
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable tag: @@VersionNumber@@

Import and display your positions from [Personio](https://www.personio.com) directly on your website. Get full control over how they are displayed.

== Description ==

Import and display your positions from [Personio](https://www.personio.com) directly on your website. Get full control over how they are displayed.

Show application forms on your positions and transfer applications from your website to Personio with [Personio Integration Pro](https://laolaweb.com/plugins/personio-wordpress-plugin/).

[youtube https://www.youtube.com/watch?v=0qjFEbKFq3w]

#### Features

- manual or automatic import of open positions in German and English (other languages only in [Personio Integration Pro](https://laolaweb.com/plugins/personio-wordpress-plugin/))
- positions are indexable by search engines (SEO)
- each open position (incl. job description) under its own URL on your website
- data protection-friendly, as no applicant data is collected and stored
- multiple Blocks for Block Editor, 2 classic widgets and [shortcodes](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/shortcodes.md)
- support for classic as well as block themes
- optionally group the lists by categories, departments, offices etc.
- some [WP CLI commands](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/cli.md) for simplified handling of data
- compatible with WCAG

#### Requirements

- Personio account with enabled XML interface
- PHP module SimpleXML

#### Hint ####

The output of the positions is limited to a maximum of 10. Only in [Personio Integration Pro](https://laolaweb.com/plugins/personio-wordpress-plugin/) there is no limitation.

#### the Pro license includes:

- Customization of slugs (URLs) for list and detailed views of positions
- Multiple and customizable application forms incl. export of them via Personio API
- Supports all languages Personio offers German, English, French, Spanish, Dutch, Italian, Portuguese, Swedish, Finnish, Polish
- Support for multilingual plugins Polylang, WPML, Weglot and TranslatePress
- Support for subcompanies and additional offices in positions
- Support for multiple form handler like Contact Form 7, Elementor Forms, Forminator and WPForms
- Use custom feature image on each position
- Unlimited custom files for download on each single position
- Support for tracking of events with Google Analytics 4
- Support full text search for positions in frontend
- Multiple Personio-accounts per website
- Additional import settings, e.g. intervals and partial import for very large lists of open positions and removing of inline styles from position descriptions
- RichSnippets for optimal findability via search engines like Google Jobs
- Support for Open Graph (Facebook, LinkedIn, WhatsApp ...), Twitter Cards and Dublin Core (optionally configurable for all or single positions)
- Support to embed positions from your website in other website via oEmbed (optionally configurable for all or single positions)
- Shortcode generator for individual views of lists and details
- Extensions for the following PageBuilders: Avada, Elementor, Divi, Beaver Builder, SiteOrigin (SiteOrigin Widgets Bundle necessary), WPBakery
- Every privacy values are encrypted (e.g. applicant data and API credentials)
- ... and much more

[get the Pro-Version](https://laolaweb.com/plugins/personio-wordpress-plugin/)

The development repository is on [GitHub](https://github.com/threadi/wp-personio-integration-light).

The Personio logo as part of all distributed icons is a trademark of [Personio SE & Co. KG](https://www.personio.com).

== ClassicPress ==

This plugin is compatible with [ClassicPress](https://www.classicpress.net/).

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

This is supported by the Pro version of the plugin since version 2.0.0. You can find instructions on how to do this [here](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/applications/quickstart.md).

= Does this plugin set any cookies or load data from external? =

No, this plugin does not set cookies nor does it load data externally within the front end of the website. The exception is when images and videos are embedded in the job descriptions coming from Personio. In this case, please check to what extent this affects the privacy of your own website.

= Is this plugin GPRD-compatible? =

Yes, it is without any further settings.

== Screenshots ==

1. Field to insert your Personio URL
2. Import-Settings
3. List of imported positions
4. Gutenberg Block for listings

== Upgrade Notice ==

= 4.0.0 =

Complete revision of the plugin. Please create a backup before installing version 4.0.0 or newer.

== Changelog ==

= @@VersionNumber@@ =
- Added compatibility with plugin Duplicate Page to prevent the duplication of positions with this plugin
- Added some more hooks
- Added support for using filter on static front page
- Added GitHub action to build release ZIP
- Added style for archive with theme Blocksy
- Added support for using filter on preview-pages while preparing the website
- Added hint for WordPress-own help for this plugin
- Changed CSS class to mark active list filter to "personio-filter-list-selected"
- plugin version number is now automatically generated in readme.txt during plugin release build
- Moved changelog from readme.txt in GitHub-repository
- Optimized documentation of deleted position via WP CLI
- Optimized handling of filter output in frontend
- Optimized position object
- Usage of filter on archive block marked as deprecated (will be removed on next major release)
- Extended help for debug mode
- Colored the helper tab for better visibility
- Updated dependencies
- Removed not needed additional translation file
- Cleaned up third party support from unused code
- Removed unused ID attribute from position object
- Fixed missing anchor for filter
- Fixed wrong textdomain in main filter template (which results in english and not translatable texts for links and buttons)
- Fixed compatibility with WordPress 6.7 if any compatibility check results in a message in backend
- Fixed output of custom styles for individual supported theme (like Blocksy)
- Fixed output of select filter via KSES-rules
- Fixed typo in job listing HTML-template

[older changes](https://github.com/threadi/wp-personio-integration-light/blob/master/changelog.md)

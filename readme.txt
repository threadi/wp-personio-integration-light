=== Personio Integration Light ===
Contributors: laolaweb, threadi
Tags: personio, jobs, recruitment, employee
Requires at least: 4.9.24
Tested up to: 6.8
Requires PHP: 8.1
Requires CP:  2.0
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
- compatible with Content Security Policy settings

#### Requirements

- Personio account with enabled XML interface
- PHP module SimpleXML

#### Hint ####

The output of the positions is limited to a maximum of 10. Only in [Personio Integration Pro](https://laolaweb.com/plugins/personio-wordpress-plugin/) there is no limitation.

#### the Pro license includes:

- Customization of slugs (URLs) for list and detailed views of positions
- Multiple and customizable application forms incl. export of them via Personio API
- Supports all languages Personio offers German, English, French, Spanish, Dutch, Italian, Portuguese, Swedish, Finnish, Polish, Czech
- Support for multilingual plugins Bogo, Polylang, WPML, Weglot and TranslatePress
- Support for legal entities and other workplaces in positions
- Support for salaries for open positions
- Use GoogleMaps or OpenStreetMap for show you locations with open positions
- Support for multiple form handler like Avada Forms, Contact Form 7, Elementor Forms, Everest Forms, Fluent Forms, Forminator, Ninja Forms and WPForms
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
- Extensions for the following PageBuilders: Avada, Beaver Builder, Divi, Elementor, SiteOrigin (SiteOrigin Widgets Bundle necessary), WPBakery
- Also compatible with Avia (from Enfold) and Kubio AI
- Every privacy values is encrypted (e.g. applicant data and API credentials)
- ... and much more

[get the Pro-Version](https://laolaweb.com/plugins/personio-wordpress-plugin/)

The development repository is on [GitHub](https://github.com/threadi/wp-personio-integration-light).

We also provide a number of [hooks](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/hooks.md) as help for developers.

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
- Plugin structure revised with modern security mechanisms and design
- Added new extension for manual import of positions from Personio
- Added new object to handle all settings
- Prepared support for new Personio API V2, which is still in beta and not usable for productive systems
- Added encryption for sensible data like API credentials
- Added backend page for list of applications as hint for using the Pro
- Added support for Say What for hint to translate taxonomy terms
- Added new email object which handles all emails this plugin is sending
- Added new email trigger: if position has been deleted, if new position has been imported, if any error occurred during import
- Added new statistic about the plugin data, which could also be sent via email on regular base
- Added option to change the from-email in each email
- Added email-template for all emails
- New centralized widget handling for every supported PageBuilder
- Added new extension category "Widgets"
- Added hint for additional offices which are usable in Pro-plugin
- Added new compatibility check for Oxygen
- Added info-page for Pro plugin with option to install the Pro-plugin with valid license key
- Added new handling for admin notices for better overview over messages from the plugin
- Added links to edit position settings in Personio if login URL is given
- Added info in admin footer if a page from our plugin is loaded
- Added option to import project configuration during setup
- Added log for error 500 during imports which also prevents hanging import tasks
- Added option to reset the plugin in backend settings (in preparation for Cyber Resilience Act)
- Added support for check for multilingual plugin Bogo
- Now requires PHP 8.1 or newer
- Now using custom database object to get all errors which might be occurred
- Get actual language via get_locale() and reduce usage of unnecessary additional hooks for multilingual plugins
- Import of positions are now also handled as extension
- Changed application hint in menu to be more stabil
- Extensions for positions replaced with less complex way
- Extensions can now check if other extension they require are enabled
- More code optimizations with PHPStan
- Optimized output of some log entries
- Extended style options for positions in Block Editor
- Style optimization for extension table
- Using WP_Error for any error handling
- Extended the limitation for REST API requests regarding our own Block Editor blocks
- Now using our own intervals for WordPress cronjobs
- Renamed "Application link" to "Option to apply"
- Renamed "Content" to "Description"
- Show hint in backend if no description is available for single position
- Block settings are now visible on load
- All Blocks are now in their own category "Personio Integration"
- The help will show primary the help for the actual called page in backend
- Optimized handling of header and footer settings for our block templates on theme switch
- Checking whether a required plugin for an extension is installed (without it having to be activated)
- Optimized HTML-code for grouped archive templates (now allows better styling via custom CSS)
- Optimized handling of Personio account in this plugin (like links to the login or the position edit URL)
- Cleanup styling of all blocks
- Reduced the output of to many style elements on classic themes
- Optimized handling for extensions: disable or enable only the listed in actual view, mark the "all" list
- Updated Intro-script
- Updated dependencies
- Fixed missing usage of some block styles (like margin and padding) in block themes
- Removed Position_Extension_Base in favor of less complex way to extend the position data
- Removed check for WpPageBuilder compatibility
- Removed already deprecated hook "personio_integration_personioposition_columns"

[older changes](https://github.com/threadi/wp-personio-integration-light/blob/master/changelog.md)

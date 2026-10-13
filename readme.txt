=== Personio Integration Light ===
Contributors: laolaweb, threadi
Tags: personio, jobs, recruitment, employee
Requires at least: 6.6
Tested up to: 7.1
Requires PHP: 8.2
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

✅ manual or automatic import of open positions in German and English (other languages only in [Personio Integration Pro](https://laolaweb.com/plugins/personio-wordpress-plugin/))
✅ positions are indexable by search engines (SEO)
✅ each open position (incl. job description) under its own URL on your website
✅ data protection-friendly, as no applicant data is collected and stored
✅ multiple Blocks for Block Editor, 2 classic widgets and [shortcodes](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/shortcodes.md)
✅ support for classic as well as block themes
✅ optionally group the lists by categories, departments, offices etc.
✅ some [WP CLI commands](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/cli.md) for simplified handling of data
✅ compatible with WCAG
✅ compatible with Content Security Policy settings
✅ Use AI to generate the presentation of your open positions in frontend

#### Requirements

🔌 Personio account with enabled XML interface
🔌 PHP module SimpleXML

#### Hint ####

The output of the positions as a list is limited to a maximum of 10. Only in [Personio Integration Pro](https://laolaweb.com/plugins/personio-wordpress-plugin/) there is no limitation.

#### the Pro license includes:

➕ Native support for following PageBuilders: Avada, Beaver Builder, Bold Page Builder, Breakdance, Bricks, Brizy, Divi 4 & 5, Elementor, Elements in GeneratePress Premium, Fusion, LiveComposer, Oxygen, PageLayer, SiteOrigin (SiteOrigin Widgets Bundle necessary), Themify, WPBakery und ZionBuilder
➕ Also compatible with Avia (from Enfold), Colibri, Kubio, Nimble Builder, SeedProd Pro, Visual Composer
➕ Multiple and customizable application forms incl. export of them via Personio API
➕ Support for multiple form handler like Avada Forms, Contact Form 7, Elementor Forms, Everest Forms, Fluent Forms, Forminator, Ninja Forms and WPForms
➕ Customization of slugs (URLs) for list and detailed views of positions
➕ Supports all languages Personio offers: German, English, French, Spanish, Dutch, Italian, Portuguese, Swedish, Finnish, Polish, Czech
➕ Support for multilingual plugins Bogo, Polylang, WPML, Weglot and TranslatePress
➕ Support for legal entities and other workplaces on positions
➕ Support for salaries for open positions
➕ Use GoogleMaps or OpenStreetMap for show you locations with open positions
➕ Use custom feature image on each position
➕ Assign unlimited custom files for download or videos on each single position
➕ Support for tracking of events with Matomo and Google Analytics 4
➕ Support for Open Graph (Facebook, LinkedIn, WhatsApp ...), Twitter Cards and Dublin Core (optionally configurable for all or single positions)
➕ Support full text search for positions in frontend
➕ Use multiple Personio-accounts in one website
➕ Additional import settings, e.g. intervals and partial import for very large lists of open positions and removing of inline styles from position descriptions
➕ RichSnippets for optimal findability via search engines like Google Jobs
➕ Support to embed positions from your website in other website via oEmbed (optionally configurable for all or single positions)
➕ Shortcode generator for individual views of lists and details
➕ ISO 27001/27002 compliant encryption all sensitive data (e.g., applicant data and API credentials).
➕ ... and much more

🆙 [Get the Pro-Version](https://laolaweb.com/plugins/personio-wordpress-plugin/)

== ClassicPress ==

This plugin is compatible with [ClassicPress](https://www.classicpress.net/).

== Repository, documentation and reliability ==

The development repository is on [GitHub](https://github.com/threadi/wp-personio-integration-light).

We also provide several [hooks](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/hooks.md) as help for developers.

The Personio logo as part of all distributed icons is a trademark of [Personio SE & Co. KG](https://www.personio.com).

Each release of this plugin will only be published if it fulfills the following conditions:

✅ Compliance with WordPress Coding Standards
✅ PHPStan check for possible bugs
✅ PHP Unit tests

---

== Installation ==

1. Upload "personio-integration-light" to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Enter your Personio URL in the settings.
4. Include one of the different output options for open positions in your company in your website.

== Frequently Asked Questions ==

= Can I use the plugin without a Personio account? =

The plugin can be installed even without a Personio account. However, it is unusable without Personio data.

= Does this plugin use iframes? =

No, no iframe of any kind is used to embed data.

= How can I use a form as an application form and submit the applications to Personio? =

This is supported by the Pro version of the plugin since version 2.0.0. You can find instructions on how to do this [here](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/applications/quickstart.md).

= Does this plugin set any cookies or load data from external? =

No, this plugin does not set cookies, nor does it load data externally within the front end of the website. The exception is when images and videos are embedded in the job descriptions coming from Personio. In this case, please check to what extent this affects the privacy of your own website.

= Is this plugin GPRD-compatible? =

Yes, it is without any further settings.

== Screenshots ==

1. Setup leads you through the first import of your positions from Personio
2. The imported positions in WordPress
3. Main settings for the plugin
4. Your positions from Personio in the frontend of your website

== Upgrade Notice ==

= 5.0.0 =

Complete revision of the plugin. Please create a backup before installing version 5.0.0 or newer.

== Changelog ==

= @@VersionNumber@@ =
- Personio API v2 is now available; however, since Personio's position data via their API v2 is still incomplete,
  we continue to recommend using the tried-and-true XML interface.
- Added compatibility check for Enfold
- Added more abilities to manage your position templates with AI
- Added abilities to run and cancel the import of positions, to delete all positions and to read the log with AI, and extended the import state ability with the progress
- Added a link to edit a single position in your Personio account in the list of all positions in backend
- Added setting to chose, which API to use for import of positions
- Added Playwright for automated visual tests before each release
- Added new hooks
- Now requires WordPress 6.6 or newer
- Optimized some visibility conditions for settings on our blocks
- Optimized check for Elementor Pro and PRO Elements
- Optimized support for Personio API v2 (still no position import possible)
- Optimized the position count handling
- Optimized resetting this plugin in a network
- Modernize usage of global functions to speed up loading times
- Optimized schedule handling of they could not be added
- Switches from introJS to driver.js as technical base for the intro
- Show warning in backend if crypt key has been changed (additional to the log entry)
- Force to reload the WordPress-own permalink cache on uninstallation
- Secure handling of styles in Blocks
- Import and deletion of positions use atomic locks, orphaned locks are released after one hour
- Credentials and tokens are anonymized in the debug log of API requests
- Template parts and template names from shortcodes and blocks are checked against allowlists
- Templates saved via abilities may not contain blocks with arbitrary HTML, shortcodes or remote content
- The license key is not part of URLs anymore and is not loaded on every request
- Log cleanup runs at most once per hour, the log table is paginated in the database
- Release ZIP from GitHub does not contain development files anymore
- Settings default view is now DataView for all new installations (old stay unchanged)
- Updated the settings library
- Updated the setup library
- Updated dependencies
- Fixed a potential SQL injection during search in backend (only usable by logged in users)
- Fixed potential XSS through shortcodes (only usable by logged in users)
- Fixed the intro handling
- Fixed some typos
- Fixed missing logging of errors if debug mode is enabled with restriction for one or more categories
- Fixed XML errors of a faulty Personio feed blocking the following imports in the same process
- Fixed a deletion of all positions, which was aborted by an error, blocking every further deletion
- Fixed encryption key being sent to the license server
- Fixed possible XSS in the log, in the manual import dialog and in emails through data from Personio
- Fixed allowed HTML for form fields being extended for all content of the website
- Fixed missing capability checks on dismissing hints, installing Pro and switching the settings view
- Fixed validation of Personio URL and login URL being skipped in REST requests
- Fixed only one position per group being shown if positions are grouped
- Fixed single position widgets showing nothing on paginated pages
- Fixed email about deleted positions being sent after every import with all positions ever deleted
- Fixed email about new positions being sent without new positions
- Fixed all block templates being removed for classic themes
- Fixed API import skipping every position and reporting an error on the last page
- Fixed endless polling in backend if an import or deletion broke up
- Fixed some possible PHP errors with unexpected setting values
- Fixed actions in dialogs (e.g. template import, log download) failing with “The link you followed has expired” in the DataView settings
- Fixed empty error messages of failed requests to the Personio API

[older changes](https://github.com/threadi/wp-personio-integration-light/blob/master/changelog.md)

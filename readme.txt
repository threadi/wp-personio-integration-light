=== Personio Integration Light ===
Contributors: laolaweb, threadi
Tags: personio, jobs, recruitment, employee
Requires at least: 4.9.24
Tested up to: 6.6
Requires PHP: 8.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 3.2.1

Import and display your positions from [Personio](https://www.personio.com) directly on your website. Get full control over how they are displayed.

== Description ==

Import and display your positions from [Personio](https://www.personio.com) directly on your website. Get full control over how they are displayed.

Show application forms on your positions and transfer applications from your website to Personio with [Personio Integration Pro](https://laolaweb.com/plugins/personio-wordpress-plugin/).

#### Hint ####

The output of the positions is limited to a maximum of 10. Only in [Personio Integration Pro](https://laolaweb.com/plugins/personio-wordpress-plugin/) there is no limitation.

#### Features

- manual or automatic import of open positions in German and English (other languages only in [Personio Integration Pro](https://laolaweb.com/plugins/personio-wordpress-plugin/))
- positions are indexable by search engines (SEO)
- each open position (job description) under own URL on your website
- data protection friendly, as no applicant data is collected and stored
- multiple Blocks for Block Editor, 2 classic widgets and [shortcodes](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/shortcodes.md)
- support for classic as well as block themes
- optionally group the lists by categories, departments, offices etc.
- some [WP CLI commands](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/cli.md) for simplified handling of data
- compatible with WCAG

#### Requirements

- Personio account with enabled XML interface
- PHP module SimpleXML

#### the Pro license includes:

- Customization of slugs (URLs) for list and detailed views of positions
- Multiple and customizable application forms incl. export of them via Personio API
- Supports all languages Personio offers German, English, French, Spanish, Dutch, Italian, Portuguese, Swedish, Finnish, Polish
- Support for multilingual plugins Polylang, WPML, Weglot and TranslatePress
- Support for subcompanies and additional offices in positions
- Support for multiple form handler like Contact Form 7, Forminator and WPForms
- Use custom feature image on each position
- Unlimited custom files for download on each single position
- Manual sorting of open positions in list views via drag&drop
- Sorting of position details visible in frontend via drag&drop
- Support for tracking of events with Google Analytics 4
- Support full text search for positions in frontend
- Multiple Personio-accounts per website
- Additional import settings, e.g. intervals and partial import for very large lists of open positions and removing of inline styles from position descriptions
- RichSnippets for optimal findability via search engines like Google Jobs
- Support for Open Graph (Facebook, LinkedIn, WhatsApp ...), Twitter Cards and Dublin Core (optionally configurable for all or single positions)
- Support to embed positions from your website in other website via oEmbed (optionally configurable for all or single positions)
- Shortcode generator for individual views of lists and details
- Extensions for the following PageBuilders: Avada, Elementor, Divi, Beaver Builder, SiteOrigin (SiteOrigin Widgets Bundle necessary), WPBakery
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

= 3.0.0 =

Complete revision of the plugin. Please create a backup before installing version 3.0.0 or newer.

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
* Added possibility to group the list of positions by one of the taxonomies
  (also added in Gutenberg-Block as filter-option)
* Optimized compatibility with other plugins using JavaScript in wp-admin
* Optimized support for some popular WordPress-themes like Astra
* Removed possibility to link the position-title in detail-view
* Removed our own custom post type from post type list in Redirection-plugin
* Compatibility with WordPress 6.1
* Updated dependencies for Gutenberg-scripts
* Fixed usage of archive template for child-themes
* Fixed REST-API reading of excerpt of positions
* Fixed visibility of settings-link in plugin-list
* Fixed pagination of positions-archive

= 1.2.1 =
* Optimized filter-formular for better scrolling in website after submitting
* Previously untranslatable texts adapted
* Fixed embedding of new group-title-template
* Fixed parameter "ids" on Shortcode "personioPositions"

= 1.2.2 =
* Add button to cancel running import after it runs for 1 hour
* Extended logging on import of positions by Personio
* Fixed wrong occupation slug for filter in archive listings
* Fixed some typos in texts

= 2.0.0 =
* Added link "back to list" on detail-view (optional).
* Added possibility to different translate/change "Apply to this position" in archive- and single-view
* Optimized support for Yoast SEO and Rank Math
* Optimized select- and multi-select-field for plugin-settings
* Optimized translation for german and german_formal.
* Fully compatible with PHP 7.4, 8.0, 8.1 and 8.2
* More logging of events with enabled plugin-debug-mode
* Advanced support for themes: GeneratePress, OceanWP, StartIt, Twenty Fourteen, Kadence, Neve, Hestia, Total, BeTheme
* Fixed loading of position content in detail page

= 2.0.1 =
* Fixed usage of classic position widget

= 2.0.2 =
* Reset import-flag on plugin-activation
* Fixed compatibility with import of Pro-plugin

= 2.0.3 =
* Added field for custom back to list url in template-settings
* Removed Block preview in Block Editor for fix error in Block-themes
* Updates Blocks for actual Gutenberg-enhancements
* Compatibility with WordPress 6.2
* Fixed some typos in texts

= 2.0.4 =
* Optimized taxonomy-handling
* Fixed typo in filter-template

= 2.0.5 =
* Make sure that the cronjob are installed in any case
* Support for Open Graph-settings by plugin [OG](https://wordpress.org/plugins/og/)
* Optimized Block Editor single block: load unlimited positions for select-field
* Code-optimization for Personio application URL
* Code-optimization to reduce db-requests in frontend
* Fixed Block Editor single block: disable fields if given PersonioID is not available anymore
* Fixed support for positions without descriptions
* Fixed import of missing createdAt from Personio
* Fixed resetting of position order during import
* Fixed filter regarding pagination

= 2.1.0 =
* Enhanced support for Block Editor: 2 new Blocks (for filter), new options for styling
* Filter on list Block is not automatic enabled for new Blocks anymore
* Optimized loading of database-objects
* Added missing Personio-driven schedule time
* Fixed usage of some Gutenberg-options on list-Block
* Fixed target url for filter in frontend
* Fixed visibility of back to list link

= 2.2.0 =
* Added 2 Block Templates for Block Themes
* Added one more Block for application links
* Added more styling-options to each Block
* Added warning about changed templates in Child-themes
* Optimized Block-definitions
* Fixed usage of Personio-XML-URL for validating the Personio-URL (e.g. if the career-pages on Personio are disabled)

= 2.2.1 =
* Solved SVN-problems

= 2.2.2 =
* Cleanup SVN

= 2.2.3 =
* Fixed missing files in SVN

= 2.2.4 =
* Fixed usage of attributes in Gutenberg list-block

= 2.2.5 =
* Optimized check for post-type in list-view
* Compatibility with WordPress 6.2.2

= 2.2.6 =
* Added filter for excerpt-taxonomy

= 2.2.7 =
* Added hint for pro-version if Divi is used
* Added keyword-import for each position (if set)
* Extended Wordpress-own search regarding the keywords for positions (only for exact match)
  incl. new option in settings to disable it

= 2.2.8 =
* Added hints for other supported page builder.
* Fixed template loading of template does not exist in theme or Pro-plugin.

= 2.2.9 =
* Fixed release-number.

= 2.2.10 =
* Restrict access to REST API Endpoint for taxonomies
* Fixed saving of taxonomy-defaults after first installation

= 2.2.11 =
* Fixed usage of attribute filter

= 2.3.0 =
* Added view of imported position data in backend
* Added hint for Avada-support in Pro-version
* Added anchor on filter of type linklist
* Added active marker for linklist-filter on category
* Added language-specific links to Personio login and support pages
* Compatibility with WordPress 6.3
* Optimized loading of taxonomies of positions
* default translations are now also visible in backend

= 2.3.1 =
* Added filter to individually prevent our custom the_content filter from being run
* Fixed reset query after loop

= 2.3.2 =
* Add anchor to pagination in position-lists
* Add possibility for multiple offices per position
* Some optimizations for compatibility with Pro-version
* Fix keyword import

= 2.3.3 =
* Fixed missing form settings file in repository
* Fixed some typos in texts

= 2.4.0 =
* Added new block for position details in Block Editor, usable on single page
* Added new block for position description in Block Editor, usable on single page
* Added support for accessibility with aria-labels (regarding WCAG)
* Added hints on all links which open new windows that they open new windows (regarding WCAG)
* Added hook personio_integration_delete_single_position for individual manipulation of the deletion of single positions during import
* Added hint to review plugin every 90 days
* Added option on hook for filetypes on list block
* Extended the Dashboard widget where the newest imported positions are mentioned
* Compatibility with WordPress 6.3.1
* Optimized handling of tabs in settings-page
* Renamed excerpt to detail for compatibility with Personio wording
* Updated import handling if Personio-URL is not available
* Updated translations
* Updated Block Editor single template
* Updated dependencies for Gutenberg-scripts
* Fixed reading of employment type

= 2.4.1 =
* Added WPML settings to prevent translation of our own post types and taxonomies
* Added our own importer under Tools > Importer
* Run import even if the Personio timestamp has not been changed but no positions are in local database
* Extended log for deletion of positions
* Optimized log-styling
* Updated translations
* Fixed missing files for Block Editor in WordPress-SVN
* Fixed error in dashboard if pro-plugin is not active

= 2.5.0 =
* Added occupation detail as sub-entry for occupation-category
* Added occupation detail as possible filter in Block Editor
* Added new user role "Manage Personio-based Positions" which will be able to manage all position stuff
* Added check if our own import cronjob has been run under tools > site health
* Added check if configured Personio-URL is available under tools > site health
* Plugin is now complete translated in austria and swiss language
* Optimized capability-checks for our own custom post type and all used taxonomies
* Optimized text in frontend if no positions are available.
* Added more hooks
* Updated all translations for values in position details set by Personio
* Updated order of settings-tabs
* Fixed visibility of import button above listing in WordPress 6.3 or newer
* Fixed visibility of back to list button in frontend

= 2.5.1 =
* Fixed order by on grouping

= 2.5.2 =
* Compatibility with ActivityPub to optionally publish positions from Personio in the fediverse (e.g. Mastodon)
* Compatibility with WordPres 6.3.2
* Removed unnecessary spaces around position description

= 2.5.3 =
* Changed text domain for any texts to match WordPress-requirements:
=> from wp-personio-integration to personio-integration-light
* Added warning for users who have changed the texts of our plugin to use the new text domain
* Compatibility with WordPres 6.4
* Updated dependencies for Gutenberg-scripts
* Remove local embedded translation-files
* Fixed template-loading for Gutenberg
* Fixed version setting during uninstalling

= 2.5.4 =
* Compatibility with WordPres 6.4.1
* Fixed language setting for taxonomies

= 2.5.5 =
* Added possibility to use templates for job description in frontend
* Updates description block to use different template-driven layouts for it
* Optimized paths for plugin-files to prevent error in WP CLI
* Updated dependencies for Gutenberg-scripts
* Fixed missed text-domain usages
* Fixed missing block translations

= 2.6.0 =
* Added chance to use different templates for archive listings
* Added option on Blocks to choose the archive listing template
* Advanced logging during import of positions
* Fixed possible bug on archive page if job description is enabled there
* Fixed visibility of text domain hint if Pro plugin is used

= 2.6.1 =
* Added support for multilingual-plugin Weglot to detect the active language
* Added new hooks
* Changed import-format for createAt to UNIX-timestamp
* Sort by date now sort the positions by its createAt-value from Personio
* Better check for third party functions
* Compatibility with WordPress 6.4.2
* Fixed missing styles with some blocks in Block Editor

= 2.6.2 =
* Compatibility with WordPress 6.4.3
* Updated dependencies for Gutenberg-scripts
* Fix for possible code injection in search

= 3.0.0 =
* Completely revised plugin
* Now only compatible with PHP 8.0 or newer
* And compatible with WordPress since 4.9.24 (also usable with ClassicPress)
* Added setup for first installations
* Added support for Multisite-installations
* Added some additional classes in templates for better custom styling-possibilities
* Added option to choose a content template on listings
* Added support for additional plugins: Open Graph and Twitter Tags, SEOFramework, SEOPress, Slim SEO
* Added link to switch between frontend- and backend-view of single position
* Added new templates for position title and excerpts
* Added possibility to export and import all settings
* Added daily checks for availability of the Personio-page of your positions
* Added new pattern in Block Editor for fast implementing custom views of positions
* Advanced Blocks for Positions in Block Editor
* Advanced classic widgets for Positions
* Added more simple initial styling for more often used themes
* Optimized all templates for better handling and optimized output
* Optimized check for existing part-templates
* Many new hooks (total 137) which are now documented [in the repository](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/hooks.md)
* Compatible with WordPress Coding Standards 3.0 (WCS3.0)
* New WCS3.0 compatible WP CLI commands (old ones does not exist anymore)
* New WP- and react-driven dialogs for each interaction with the plugin
* New check for configured Personio-URL in Site Health
* Now compatible with the WordPress-plugin AMP
* And now compatibly with the WordPress-plugin PDF Generator for WP to print your positions as PDF in frontend
* Check for and re-install missing cron-events (e.g. for automatically import positions) if they are missing
* Removed short intervals for cronjobs as it is discouraged by WordPress
* Removed support for multilingual-plugins to detect the actual language (this is now only in Pro-plugin)
* Removed usage of filter in classic widgets
* Mark the filter options on Block "Personio Positions" as deprecated incl. warning for user to use Filter Block instead
* Extended Sitemap XML for Positions
* Fixed usage of classic widgets
* Fixed wrong Position count on dashboard
* Fixed sorting in Log-table

= 3.0.1 =
* SVN cleanup

= 3.0.2 =
* Added filter to change the result for check if FSE-theme is used
* Compatibility with WordPress 6.5.2
* Fixed handling of updates together with Pro

= 3.0.3 =
* Optimized handling of language-assignments during import
* Optimized handling for languages im WPML is enabled (but we only support its usage on Pro)
* Optimized schedule state toggling for availability-extension
* Improved import speed
* Changed default max age for log entries from 50 to 20 days
* Updated dependencies
* Fixed import schedule
* Fixed possible error in position by missing language-setting for position
* Fixed possible error with Rank Math on single pages
* Fixed generation of pagination links

= 3.0.4 =
* Added more actions on import
* Show Personio Timestamp in log for each import
* Compatibility with WordPress 6.5.3

= 3.0.5 =
* Added new extension which allows to show the used XML from Personio on each position
* Added more hooks in import
* Optimized import state
* Optimized internal usage of settings of this plugin
* Fixed missing permalink refresh after import of positions
* Fixed some typos
* Fixed generation of documentation during plugin release

= 3.0.6 =
* Optimized internal usage of settings of this plugin
* Optimized internal usage of schedules of this plugin
* Optimized schedule check in frontend
* Fixed typos

= 3.0.7 =
* Compatibility with WordPress 6.6
* Optimized check for Polylang free and pro

= 3.0.8 =
* Added new hooks for custom template optimizations
* Optimized check for disabled Personio XML API
* Updated setup component
* Updated dependencies
* Extended compatibility with plugins which use WordPress events incorrectly
* Fixed listing template for archive which prevented link color for each link in list
* Fixed possible notices during uninstallation

= 3.0.9 =
* Added some hooks for internal optimizations
* Optimized handling of trigger re-import if Personio URL is not cleaned up
* Fixed wrong loaded single block

= 3.0.10 =
* Do not install cronjobs before setup has been completed
* Fixed on loading of Personio URL for import if no URL is configured
* Fixed format of email notification on import errors

= 3.0.11 =
* Optimized REST API detection
* Fixed PHP-Warnings in intro-object if Divi or other plugins will be enabled
* Fixed template for grouped view

= 3.1.0 =
* Added not grouped-setting for list block
* Added categories for log entries
* Added new option to enter the Personio Login URL to help reach your Personio account from WordPress faster
* Added deletion of extension data during uninstallation of this plugin
* Added documentation how to implement custom extensions for this plugin
* Compatible with PHP 8.4
* Optimized translation of position contents for Blocks in Block Editor
* Optimized email-format for info about errors during import
* Hide Pro-hint for more entries on some specific pages
* All protocol entries are now translatable
* Use wp_add_inline_style() instead of styling-template
* Updated Blocks for React 19 compatibility (for future WP-version)
* Log is now restricted to 10.000 entries for better performance, only changeable in Pro
* Removed styling template
* Fixed visibility of Single Block
* Fixed handling of grouping list if not data for grouping is available

= 3.1.1 =
* Updates dependencies
* Fixed missing files in WordPress-repository

= 3.1.2 =
* Only import taxonomies for main languages (prevent e.g. missing keywords for other languages)
* Show limitation hint only if 10+ positions are imported
* Updated dependencies
* Update WP Easy Setup configuration for better compatibility with other plugins which use this
* Optimized cleanup of extensions during uninstallation
* Fixed WP Easy Setup for running on older WordPress-versions
* Fixed limitation of lists of positions in frontend
* Fixed missing inline styles in Block Editor (e.g. to hide the filter-title there)

= 3.1.3 =
* Downgrading wordpress-scripts for compatibility for our own Blocks with WordPress < 6.6
* Check for db-type on deleting logs to prevent possible SQLite errors

= 3.1.4 =
* Updated dependencies
* Fixed delete of log with SQLite (e.g. in playground)

= 3.1.5 =
* Personio URL can now also insert without protocol (if https:// is missing)
* Fixed query for positions without specific language
* Fixed delete of log in playground

= 3.2.0 =
* Added logging for Personio database queries if debugging is enabled
* Added sanitize for our own setting fields
* Added loading screen for setup and option to skip it
* Added new hook for individual tasks per post type endpoint
* Added visibility state for positions if their visibility is restricted by global settings
* Added hints which taxonomies are changeable e.g. via Loco Translate
* Added translation options for blocks
* Added some styling in the filter blocks
* Added new help system within page builder widgets (could be disabled in advanced settings)
* Added more hooks
* Optimized handling of Blocks for Block Editor
* Optimized handling for transients in backend
* Optimized error handling for JS-errors in backend
* Hide more third party plugin actions for positions
* Re-Import now also possible without jQuery in backend
* Position details in Block Single and Details are now loaded dynamically
* Use main settings as defaults in classic widgets
* Revert support for WPML-translation of our own taxonomies (now really only in Pro)
* Remove Block Editor templates on uninstall
* Fixed single view state
* Fixed setup progress bar which now stops at 100%
* Fixed possible error with unknown custom extensions categories
* Fixed group listings to hide additional terms per group entity

= 3.2.1 =
* Small code optimizations
* Updated dependencies
* Prevent composer plattform check
* Fixed uninstaller routine

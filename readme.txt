=== Personio Integration Light ===
Contributors: laolaweb, threadi
Tags: personio, jobs, recruitment, employee
Requires at least: 5.9.3
Tested up to: 6.4.1
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 2.6.0

== Description ==

Import and display your positions from [Personio](https://www.personio.com) directly on your website. Get full control over how they are displayed.

Show application forms on your positions and transfer applications from your website to Personio with [Personio Integration Pro](https://laolaweb.com/plugins/personio-wordpress-plugin/).

#### Hint ####

The output of the positions is limited to a maximum of 10. Only in [Personio Integration Pro](https://laolaweb.com/plugins/personio-wordpress-plugin/) there is no limitation.

#### Features

- manual or automatic import of open positions
- import of positions in German and English
- output via 7 blocks for Block Editor, 2 classic widgets or via individual [shortcodes](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/shortcodes.md)
- search engine indexable output (SEO) of list views and open positions
- open position (job description) under website's own URL
- data protection friendly, as no applicant data is collected and stored
- support for classic as well as block themes
- optionally group the lists by categories, departments, offices etc.
- some [WP CLI commands](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/cli.md) for simplified handling of data
- WCAG-compatible

#### Requirements

- Personio account with XML interface enabled
- PHP module simpleXML

#### Compatibility tested with

- WPML and Polylang for language-detection
- Post Types Order
- Elementor, Themify, Beaver Builder, SiteOrigin (SiteOrigin Widgets Bundle necessary), WPPageBuilder, Divi

#### the Pro license includes:

- application formulars incl. export of them via Personio API
- manual sorting of open positions in list views via drag&drop
- sorting of position details in frontend via drag&drop
- supports all languages Personio offers German, English, French, Spanish, Dutch, Italian, Portuguese - compatible with translations via Polylang
- support for tracking of events with Google Analytics 4
- text search in frontend for applicants
- additional import settings, e.g. intervals and partial import for very large lists of open positions and removing of inline styles from job descriptions
- RichSnippets for optimal findability via search engines like Google
- Customization of slugs (URLs) for list and detailed views of positions
- Shortcode generator for individual views of lists and details
- Extensions for the following PageBuilders: Elementor, Divi, Themify, Beaver Builder, SiteOrigin (SiteOrigin Widgets Bundle necessary), WPPageBuilder, WPBakery, Avada
- support for subcompanies and additional offices in positions

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
* Fixed missing styles with some blocks in Block Editor

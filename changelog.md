# Changelog

## [Unreleased]

### Changed

- Log if Personio does not send any last timestamp for the request of open positions
- Optimized output of import results on WP CLI
- Some more PHP Unit Tests

### Fixed

- Fixed missed loading of block scripts
- Fixed the missing removing of the running flag if an import error occurs during import via WP CLI

## [5.1.3] - 19.01.2026

### Fixed

- Fixed the missing check for ACF Pro
- Fixed wrong handling of deleted positions during the generation of the deleted position email

## [5.1.2] - 19.01.2026

### Added

- Added multiple new PHP Unit Tests
- Added some new hooks
- Added support for detection of ACF Pro

### Changed

- Reading position details is now more accurate
- Optimized PHP Unit Tests
- Updated dependencies

### Fixed

- Fixed a missing check for direct access in two templates
- Fixed to show automatic import in the log for deletion of a single position

## [5.1.1] - 05.01.2026

### Changed

- Compatibility with older Pro-versions regarding the advanced tab for settings
- PHP Unit tests now also run on local build of releases

## [5.1.0] - 05.01.2026

### Added

- Added automatic PHP compatibility check before each release
- Added over 100 PHP Unit tests for over 200 situations
- Added some more hooks
- Added check for used language and show a hint if we have no translations for it

### Changed

- Changed the hook name "position_integration_position_title" to "personio_integration_light_position_title"
  for compatibility with WCS
- Changed styling for select fields in blocks to the default styling by Block Editor
- Use minified blocks.css for classic themes which are using the block editor
- Optimized release generation with dynamic URLs
- Optimized import handling if an error occurs and the next import reads the previous import state
- Optimized multiple typos in code and documentation
- Optimized handling of encrypted strings
- Updated dependencies

### Fixed

- Fixed wrong options for listing template in classic themes
- Fixed multiple typos in code comments

## [5.0.6] - 22.12.2025

### Changes

- Optimized code for actual WordPress Coding Standards & PHPStan
- Updated dependencies

### Fixed

- Fixed the missing $ use_li variable in the default archive listing
- Fixed deletion of reset transients during uninstallation of the plugin

## [5.0.5] - 24.11.2025

### Added

- Added support for navigation link block in WordPress 6.9

### Changed

- Check if the string to encrypt has any content
- Now also compatible with PHP 8.5
- Optimized blocks for WordPress 6.9 usage
- Optimized support for translation for blocks
- Updated dependencies

### Fixed

- Fixed wrong group sorting for <li>-using archive templates
- Fixed an unnecessary load of the number of positions on every request
- Fixed the missing custom block category for non-fse-themes with support for block editor

## [5.0.4] - 17.11.2025

### Changed

- Optimized check of our own schedules
- Consider wrong WooCommerce usage for the meta-boxes hook
- Set messages from license check as prioritized to show them first
- Log if the plugin has been updated
- Updated dependencies
- Compatible with WordPress 6.9

### Fixed

- Fixed missing logging of errors during the creation of schedules

## [5.0.3] - 10.11.2025

### Added

- Added compatibility-detection for GeneratePress Premium

### Changed

- Changed priority to load admin footer text to prevent errors from third party plugins
  which do not use this hook the correct way
- Extended the error reporting for any PHP errors during the import of positions
- Updated dependencies

### Fixed

- Fixed missing listing styles for non-FSE themes

## [5.0.2] - 03.11.2025

### Added

- Added detection of the plugin Pods

### Changed

- Text optimization in the import log
- Optimization of the last PHPStan warnings
- Optimization of schedule handling
- Updated dependencies

### Fixed

- Fixed the grouping of positions which were not correctly sorted
- Fixed wrong email headers

### Removed

- Removed legacy style usage to fulfill WordPress Plugin Checker tests

## [5.0.1] - 27.10.2025

### Changed

- Check for object on results for the query for positions
- Optimized cron deletion on uninstallation
- Updated dependencies

### Fixed

- Fixed the wrong URL usage after the Pro plugin is installed

## [5.0.0] - 21.10.2025

### Added

- Plugin structure revised with modern security mechanisms and design
- Added a new extension for manual import of positions from Personio
- Added a new object to handle all settings
- Prepared support for new Personio API V2, which is still in beta and not usable for productive systems
- Added encryption for sensible data like API credentials
- Added a backend page for the list of applications as a hint for using the Pro
- Added support for Say What for a hint to translate taxonomy terms
- Added a new email object which handles all emails this plugin is sending
- Added new email trigger: if a position has been deleted, if a new position has been imported, if any error occurred during import
- Added a new statistic about the plugin data, which could also be sent via email on regular base
- Added option to change the from-email in each email
- Added email-template for all emails
- New centralized widget handling for every supported PageBuilder
- Added the new extension category "Widgets"
- Added hint for additional offices which are usable in Pro-plugin
- Added a new compatibility check for Oxygen
- Added the info-page for Pro plugin with the option to install the Pro-plugin with a valid license key
- Added new handling for admin notices for better overview over messages from the plugin
- Added links to edit position settings in Personio if the login URL is given
- Added info in the admin footer if a page from our plugin is loaded
- Added option to import project configuration during setup
- Added log for error 500 during the imports, which also prevents hanging import tasks
- Added option to reset the plugin in backend settings (in preparation for Cyber Resilience Act)
- Added support for check for multilingual plugin Bogo

### Changed

- Now requires PHP 8.1 or newer
- Now using a custom database object to get all errors which might be occurred
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

### Fixed

- Fixed missing usage of some block styles (like margin and padding) in block themes

### Removed

- Removed Position_Extension_Base in favor of less complex way to extend the position data
- Removed check for WpPageBuilder compatibility
- Removed already deprecated hook "personio_integration_personioposition_columns"

## [4.3.3] - 11.08.2025

### Changed

- Now also compatible with ClassicPress 2.x

## [4.3.2] - 24.07.2025

### Changed

- Optimized output of update hints in plugin list

### Fixed

- Fixed grouped list of position which has been missing its key

## [4.3.1] - 05.05.2025

### Fixed

- Fixed missing non-german initial setup

## [4.3.0] - 28.04.2025

### Added

- Added some CSS for theme TwentyTwenty for better initial view
- Added setting for required extensions
- Added possibility to change extension state via URL

### Changed

- Changed text in table of positions if no positions are imported
- Optimized Block Editor loading
- Extend support for extension regarding its PHP-strict compatibility
- Optimized build process regarding check against WordPress Coding Standards
- GitHub action does not fail if it automatically fixes code issues
- Extension state can now also be changed without JavaScript
- Updated review URL
- Updated dependencies

### Fixed

- Fixed usage of form filter if simple permalinks are used
- Fixed missing custom styles for widgets in classic themes

## [4.2.7] - 16.04.2025

### Changed

- Code optimization on multiple lines
- Change visibility of schedule entities

### Fixed

- Fixed faulty HTML-code in classic widget edit form

## [4.2.6] - 14.04.2025

### Changed

- Use own styles also in terms management
- Optimized compatibility with exotic filesystem modes
- Hide dashboard widget if setup has not been run
- Removed date filter above positions table in backend

## [4.2.5] - 07.04.2025

### Added

- Added link to edit positions on Personio
- Added info about where a position has been imported from
- Added hint for application forms in Pro on edit page of position
- Added detection for WP Multilang which could supported by Pro plugin

### Changed

- Pagination of log table is now optimized
- Small style-optimization for settings page
- Optimized links to Personio account in edit page of position
- Optimized help for some setting options
- Optimized WP CLI messages
- composer.json is now part of the release
- Prevent any bulk action on position table
- Log import cancellation
- Renamed help menu entry

### Fixed

- Fixed using underscore in term class names for filter

## [4.2.4] - 10.03.2025

### Added

- Added support for Secure Custom Fields detection
- Added link to support forum on plugin in plugin list
- Added new filter-hook for the terms in any filter

### Changed

- Set compatibility with WordPress 6.8
- Log database errors
- Renamed support object Secure Custom Fields to Advanced Custom Fields
- Delete the dismisses transients on uninstallation

### Fixed

- Fixed wrong usage of plugin name as ID in backend
- Fixed wrong written hook name

## [4.2.3] - 18.02.2025

### Fixed

- Fixed influence on foreign custom post types

## [4.2.2] - 17.02.2025

### Changed

- Search for post meta fields of our own cpt in backend (e.g. search for Position ID is now possible)
- Optimized button styles for options in backend

### Fixed

- Fixed missing hook on Block Detail
- Fixed error on intro usage
- Fixed canceling of intro on first intro page

## [4.2.1] - 03.02.2025

### Fixed

- Fixed error in release-tagging

## [4.2.0] - 03.02.2025

### Added

- Added default options to hide title and reset link on filter

### Changed

- Optimized import if no position is returned from Personio (and no other errors occur)
- Optimized loading of JS in backend to in order not to influence the loading times there too much
- Optimized output of positions in WordPress dashboard
- Changed dialog and handling of extension state changes
- All external links are now marked with an icon
- Clearer error text for AJAX errors
- Show PHP-version-hint only after setup has been run

### Removed

- Removed support for filter on archive-widget (please use the filter-widget for this)
- Removed support for all since version 3.0.0 as deprecated marked functions and attributes

### Fixed

- Fixed potential PHP-warning regarding filter in templates
- Fixed missing usage of colon and line break settings for details template
- Fixed missing translations

## [4.1.0] - 06.01.2025

### Added

- Added compatibility with plugin Duplicate Page to prevent the duplication of positions with this plugin
- Added some more hooks
- Added support for using filter on static front page
- Added GitHub action to build release ZIP
- Added style for archive with theme Blocksy
- Added support for using filter on preview-pages while preparing the website
- Added hint for WordPress-own help for this plugin

### Changed

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

### Removed

- Removed not needed additional translation file
- Cleaned up third party support from unused code
- Removed unused ID attribute from position object

### Fixed

- Fixed missing anchor for filter
- Fixed wrong textdomain in main filter template (which results in english and not translatable texts for links and buttons)
- Fixed compatibility with WordPress 6.7 if any compatibility check results in a message in backend
- Fixed output of custom styles for individual supported theme (like Blocksy)
- Fixed output of select filter via KSES-rules
- Fixed typo in job listing HTML-template

## [4.0.2] - 2024-11-29

### Changed

- Optimized log table regarding the category filter

### Fixed

- Fixed template versioning format

## [4.0.1] - 2024-11-11

### Changed

- Changed some typos in texts
- Optimized PHP version hint
- Optimized import log texts

### Fixed

- Fixed update run for compatibility with WordPress 6.7

## [4.0.0] - 2024-11-05

### Added

- Added help system which uses the integrated help context from WordPress
- Added possibility to link to individual anchors in filter
- Added new libraries for setup and dialog
- Added filter for errors in log
- Added some new hooks
- Added active marker for categories in extension table
- Added warning for outdated PHP-versions in preparation for future plugin updates
- Added hint for extension of positions in position table
- Added output of generated CSS classes for all properties of a single position in list and single view
- Added new log state: info
- Added detection for third-party SEO-plugins Yoast, Rank Math and The Seo Framework

### Changed

- Errors during import of positions are now visible in dialog in backend
- Changed usage of post_content column: it now contains the output for job description in the configured template
- Changed too general internal slugs to prevent collision with other plugins
- Some code optimizations
- Some style optimizations in backend
- Updated dependencies
- Prevent composer plattform check
- Replaced redirect targets for actual referer with WP-own function
- Remove unnecessary check for Pro-plugin
- Optimized securing of settings in classic widgets
- Optimized uninstallation

### Fixed

- Fixed uninstaller routine
- Fixed missing output for classic widget for single position
- Fixed email format for import error
- Fixed limit for listings
- Fixed class usage in listing templates
- Fixed missing styling for description in Block Editor
- Fixed disable all extensions call: it disabled only the extensions which could be enabled by users
- Fixed support for plugin "PDF Generator for WP"
- Fixed wrong loading of page builder object list during uninstallation
- Fixed visibility of archive links in backend

## [3.2.0] - 2024-09-20

### Added

- Added logging for Personio database queries if debugging is enabled
- Added sanitize for our own setting fields
- Added loading screen for setup and option to skip it
- Added new hook for individual tasks per post type endpoint
- Added visibility state for positions if their visibility is restricted by global settings
- Added hints which taxonomies are changeable e.g. via Loco Translate
- Added translation options for blocks
- Added some styling in the filter blocks
- Added new help system within page builder widgets (could be disabled in advanced settings)
- Added more hooks

### Changed

- Optimized handling of Blocks for Block Editor
- Optimized handling for transients in backend
- Optimized error handling for JS-errors in backend
- Hide more third party plugin actions for positions
- Re-Import now also possible without jQuery in backend
- Position details in Block Single and Details are now loaded dynamically
- Use main settings as defaults in classic widgets

### Removed

- Revert support for WPML-translation of our own taxonomies (now really only in Pro)
- Remove Block Editor templates on uninstall

### Fixed

- Fixed single view state
- Fixed setup progress bar which now stops at 100%
- Fixed possible error with unknown custom extensions categories
- Fixed group listings to hide additional terms per group entity

## [3.1.5] - 2024-08-23

### Changed

- Personio URL can now also insert without protocol (if https:// is missing)

### Fixed

- Fixed query for positions without specific language
- Fixed delete of log in playground

## [3.1.4] - 2024-08-20

### Changed

- Updated dependencies

### Fixed

- Fixed delete of log with SQLite (e.g. in playground)

## [3.1.3] - 2024-08-19

### Changed

- Downgrading wordpress-scripts for compatibility for our own Blocks with WordPress < 6.6
- Check for db-type on deleting logs to prevent possible SQLite errors

## [3.1.2] - 2024-08-15

### Changed

- Only import taxonomies for main languages (prevent e.g. missing keywords for other languages)
- Show limitation hint only if 10+ positions are imported
- Updated dependencies
- Update WP Easy Setup configuration for better compatibility with other plugins which use this
- Optimized cleanup of extensions during uninstallation

### Fixed

- Fixed WP Easy Setup for running on older WordPress-versions
- Fixed limitation of lists of positions in frontend
- Fixed missing inline styles in Block Editor (e.g. to hide the filter-title there)

## [3.1.1] - 2024-08-06

### Changed

- Updates dependencies

### Fixed

- Fixed missing files in WordPress-repository

## [3.1.0] - 2024-08-05

### Added

- Added not grouped-setting for list block
- Added categories for log entries
- Added new option to enter the Personio Login URL to help reach your Personio account from WordPress faster
- Added deletion of extension data during uninstallation of this plugin
- Added documentation how to implement custom extensions for this plugin

### Changed

- Compatible with PHP 8.4
- Optimized translation of position contents for Blocks in Block Editor
- Optimized email-format for info about errors during import
- Hide Pro-hint for more entries on some specific pages
- All protocol entries are now translatable
- Use wp_add_inline_style() instead of styling-template
- Updated Blocks for React 19 compatibility (for future WP-version)
- Log is now restricted to 10.000 entries for better performance, only changeable in Pro

### Removed

- Removed styling template

### Fixed

- Fixed visibility of Single Block
- Fixed handling of grouping list if not data for grouping is available

## [3.0.11] - 2014-07-17

### Changed

- Optimized REST API detection

### Fixed

- Fixed PHP-Warnings in intro-object if Divi or other plugins will be enabled
- Fixed template for grouped view

## [3.0.10] - 2024-07-16

## Changed

- Do not install cronjobs before setup has been completed

### Fixed

- Fixed on loading of Personio URL for import if no URL is configured
- Fixed format of email notification on import errors

## [3.0.9] - 2024-07-12

### Added

- Added some hooks for internal optimizations

### Changed

- Optimized handling of trigger re-import if Personio URL is not cleaned up

### Fixed

- Fixed wrong loaded single block

## [3.0.8] - 2024-07-10

### Added

- Added new hooks for custom template optimizations

### Changed

- Optimized check for disabled Personio XML API
- Updated setup component
- Updated dependencies
- Extended compatibility with plugins which use WordPress events incorrectly

### Fixed

- Fixed listing template for archive which prevented link color for each link in list
- Fixed possible notices during uninstallation

## [3.0.7] - 2024-07-08

### Changed

- Compatibility with WordPress 6.6
- Optimized check for Polylang free and pro

## [3.0.6] - 2024-05-21

### Changed

- Optimized internal usage of settings of this plugin
- Optimized internal usage of schedules of this plugin
- Optimized schedule check in frontend

### Fixed

- Fixed typos

## [3.0.5] - 2024-05-13

### Added

- Added new extension which allows to show the used XML from Personio on each position
- Added more hooks in import

### Changed

- Optimized import state
- Optimized internal usage of settings of this plugin

### Fixed

- Fixed missing permalink refresh after import of positions
- Fixed some typos
- Fixed generation of documentation during plugin release

## [3.0.4] - 2024-05-08

### Added

- Added more actions on import

### Changed

- Show Personio Timestamp in log for each import
- Compatibility with WordPress 6.5.3

## [3.0.3] - 2024-04-30

### Changed

- Optimized handling of language-assignments during import
- Optimized handling for languages im WPML is enabled (but we only support its usage on Pro)
- Optimized schedule state toggling for availability-extension
- Improved import speed
- Changed default max age for log entries from 50 to 20 days
- Updated dependencies

### Fixed

- Fixed import schedule
- Fixed possible error in position by missing language-setting for position
- Fixed possible error with Rank Math on single pages
- Fixed generation of pagination links

## [3.0.2] - 2024-04-23

### Added

- Added filter to change the result for check if FSE-theme is used

### Changed

- Compatibility with WordPress 6.5.2

### Fixed

- Fixed handling of updates together with Pro

## [3.0.1] - 2024-04-22

### Fixed

- SVN cleanup

## [3.0.0] - 2024-04-22

### Added

- Completely revised plugin
- Now only compatible with PHP 8.0 or newer
- And compatible with WordPress since 4.9.24 (also usable with ClassicPress)
- Added setup for first installations
- Added support for Multisite-installations
- Added some additional classes in templates for better custom styling-possibilities
- Added option to choose a content template on listings
- Added support for additional plugins: Open Graph and Twitter Tags, SEOFramework, SEOPress, Slim SEO
- Added link to switch between frontend- and backend-view of single position
- Added new templates for position title and excerpts
- Added possibility to export and import all settings
- Added daily checks for availability of the Personio-page of your positions
- Added new pattern in Block Editor for fast implementing custom views of positions
- Advanced Blocks for Positions in Block Editor
- Advanced classic widgets for Positions
- Added more simple initial styling for more often used themes

### Changed

- Optimized all templates for better handling and optimized output
- Optimized check for existing part-templates
- Many new hooks (total 137) which are now documented [in the repository](https://github.com/threadi/wp-personio-integration-light/blob/master/doc/hooks.md)
- Compatible with WordPress Coding Standards 3.0 (WCS3.0)
- New WCS3.0 compatible WP CLI commands (old ones does not exist anymore)
- New WP- and react-driven dialogs for each interaction with the plugin
- New check for configured Personio-URL in Site Health
- Now compatible with the WordPress-plugin AMP
- And now compatibly with the WordPress-plugin PDF Generator for WP to print your positions as PDF in frontend
- Check for and re-install missing cron-events (e.g. for automatically import positions) if they are missing
- Removed short intervals for cronjobs as it is discouraged by WordPress
- Removed support for multilingual-plugins to detect the actual language (this is now only in Pro-plugin)
- Removed usage of filter in classic widgets
- Mark the filter options on Block "Personio Positions" as deprecated incl. warning for user to use Filter Block instead
- Extended Sitemap XML for Positions

## Fixed

- Fixed usage of classic widgets
- Fixed wrong Position count on dashboard
- Fixed sorting in Log-table

## [2.6.3] - 2024-03-13

### Changed

- Updated dependencies

## [2.6.2] - 2024-01-26

### Changed

- Compatibility with WordPress 6.4.3
- Updated dependencies for Gutenberg-scripts

### Fixed

- Fix for possible code injection in search

## [2.6.1] 2023-12-18

### Added

- Added support for multilingual-plugin Weglot to detect the active language
- Added new hooks

### Changed

- Changed import-format for createAt to UNIX-timestamp
- Sort by date now sort the positions by its createAt-value from Personio
- Better check for third party functions
- Compatibility with WordPress 6.4.2

### Fixed

- Fixed missing styles with some blocks in Block Editor

## [2.6.0] - 2023-11-30

### Added

- Added chance to use different templates for archive listings
- Added option on Blocks to choose the archive listing template

### Changed

- Advanced logging during import of positions

## Fixed

- Fixed possible bug on archive page if job description is enabled there
- Fixed visibility of text domain hint if Pro plugin is used

## [2.5.5] - 2023-11-26

### Added

- Added possibility to use templates for job description in frontend

### Changed

- Updates description block to use different template-driven layouts for it
- Optimized paths for plugin-files to prevent error in WP CLI
- Updated dependencies for Gutenberg-scripts

### Fixed

- Fixed missed text-domain usages
- Fixed missing block translations

## [2.5.4] - 2023-10-28

### Changed

- Compatibility with WordPres 6.4.1

### Fixed

- Fixed language setting for taxonomies

## [2.5.3] - 2023-10-26

### Added

- Added warning for users who have changed the texts of our plugin to use the new text domain

### Changed

- Changed text domain for any texts to match WordPress-requirements:
  => from wp-personio-integration to personio-integration-light
- Compatibility with WordPres 6.4
- Updated dependencies for Gutenberg-scripts

### Removed

- Remove local embedded translation-files

### Fixed

- Fixed template-loading for Gutenberg
- Fixed version setting during uninstalling

## [2.5.2] - 2023-10-10

### Changed

- Compatibility with ActivityPub to optionally publish positions from Personio in the fediverse (e.g. Mastodon)
- Compatibility with WordPres 6.3.2

### Removed

- Removed unnecessary spaces around position description

## [2.5.1] - 2023-09-22

### Fixed

- Fixed order by on grouping

## [2.5.0] - 2023-09-20

### Added

- Added occupation detail as sub-entry for occupation-category
- Added occupation detail as possible filter in Block Editor
- Added new user role "Manage Personio-based Positions" which will be able to manage all position stuff
- Added check if our own import cronjob has been run under tools > site health
- Added check if configured Personio-URL is available under tools > site health

### Changed

- Plugin is now complete translated in austria and swiss language
- Optimized capability-checks for our own custom post type and all used taxonomies
- Optimized text in frontend if no positions are available.
- Added more hooks
- Updated all translations for values in position details set by Personio
- Updated order of settings-tabs

### Fixed

- Fixed visibility of import button above listing in WordPress 6.3 or newer
- Fixed visibility of back to list button in frontend

## [2.4.1] - 2023-09-12

### Added

- Added WPML settings to prevent translation of our own post types and taxonomies
- Added our own importer under Tools > Importer

### Changed

- Run import even if the Personio timestamp has not been changed but no positions are in local database
- Extended log for deletion of positions
- Optimized log-styling
- Updated translations

### Fixed

- Fixed missing files for Block Editor in WordPress-SVN
- Fixed error in dashboard if pro-plugin is not active

## [2.4.0] - 2023-09-04

### Added

- Added new block for position details in Block Editor, usable on single page
- Added new block for position description in Block Editor, usable on single page
- Added support for accessibility with aria-labels (regarding WCAG)
- Added hints on all links which open new windows that they open new windows (regarding WCAG)
- Added hook personio_integration_delete_single_position for individual manipulation of the deletion of single positions during import
- Added hint to review plugin every 90 days
- Added option on hook for filetypes on list block

### Changed

- Extended the Dashboard widget where the newest imported positions are mentioned
- Compatibility with WordPress 6.3.1
- Optimized handling of tabs in settings-page
- Renamed excerpt to detail for compatibility with Personio wording
- Updated import handling if Personio-URL is not available
- Updated translations
- Updated Block Editor single template
- Updated dependencies for Gutenberg-scripts

### Fixed

- Fixed reading of employment type

## [2.3.3] - 2023-08-24

### Fixed

- Fixed missing form settings file in repository
- Fixed some typos in texts

## [2.3.2] - 2023-08-22

### Added

- Add anchor to pagination in position-lists
- Add possibility for multiple offices per position
- Some optimizations for compatibility with Pro-version

### Fixed

- Fix keyword import

## [2.3.1] - 2023-07-05

### Added

- Added filter to individually prevent our custom the_content filter from being run

### Fixed

- Fixed reset query after loop

## [2.3.0] - 2023-06-30

### Added

- Added view of imported position data in backend
- Added hint for Avada-support in Pro-version
- Added anchor on filter of type linklist
- Added active marker for linklist-filter on category
- Added language-specific links to Personio login and support pages

### Changed

- Compatibility with WordPress 6.3
- Optimized loading of taxonomies of positions
- default translations are now also visible in backend

## [2.2.11] - 2023-06-20

### Fixed

- Fixed usage of attribute filter

## [2.2.10] - 2023-06-19

### Changed

- Restrict access to REST API Endpoint for taxonomies

### Fixed

- Fixed saving of taxonomy-defaults after first installation

## [2.2.9] - 2023-06-14

### Fixed

- Fixed release-number.

## [2.2.8] - 2023-06-14

### Added

- Added hints for other supported page builder.

### Fixed

- Fixed template loading of template does not exist in theme or Pro-plugin.

## [2.2.7] - 2023-06-02

### Added

- Added hint for pro-version if Divi is used
- Added keyword-import for each position (if set)

### Changed

- Extended Wordpress-own search regarding the keywords for positions (only for exact match)
  incl. new option in settings to disable it

## [2.2.6] - 2023-05-30

### Added

- Added filter for excerpt-taxonomy

## [2.2.5] - 2023-05-22

### Changed

- Optimized check for post-type in list-view
- Compatibility with WordPress 6.2.2

## [2.2.4] - 2023-05-16

### Fixed

- Fixed usage of attributes in Gutenberg list-block

## [2.2.3] - 2023-05-15

### Fixed

- Fixed missing files in SVN

## [2.2.2] - 2023-05-15

### Fixed

- Cleanup SVN

## [2.2.1] - 2023-05-15

### Fixed

- Solved SVN-problems

## [2.2.0] - 2023-05-15

### Added

- Added 2 Block Templates for Block Themes
- Added one more Block for application links
- Added more styling-options to each Block
- Added warning about changed templates in Child-themes

### Changed

- Optimized Block-definitions

### Fixed

- Fixed usage of Personio-XML-URL for validating the Personio-URL (e.g. if the career-pages on Personio are disabled)

## [2.1.0] - 2023-04-20

### Changed

- Enhanced support for Block Editor: 2 new Blocks (for filter), new options for styling
- Filter on list Block is not automatic enabled for new Blocks anymore
- Optimized loading of database-objects
- Added missing Personio-driven schedule time

### Fixed

- Fixed usage of some Gutenberg-options on list-Block
- Fixed target url for filter in frontend
- Fixed visibility of back to list link

## [2.0.5] - 2023-04-11

### Changed

- Make sure that the cronjob are installed in any case
- Support for Open Graph-settings by plugin [OG](https://wordpress.org/plugins/og/)
- Optimized Block Editor single block: load unlimited positions for select-field
- Code-optimization for Personio application URL
- Code-optimization to reduce db-requests in frontend

### Fixed

- Fixed Block Editor single block: disable fields if given PersonioID is not available anymore
- Fixed support for positions without descriptions
- Fixed import of missing createdAt from Personio
- Fixed resetting of position order during import
- Fixed filter regarding pagination

## [2.0.4] - 2023-03-09

### Changed

- Optimized taxonomy-handling

### Fixed

- Fixed typo in filter-template

## [2.0.3] - 2023-02-12

### Added

- Added field for custom back to list url in template-settings

### Changed

- Removed Block preview in Block Editor for fix error in Block-themes
- Updates Blocks for actual Gutenberg-enhancements
- Compatibility with WordPress 6.2

### Fixed

- Fixed some typos in texts

## [2.0.2] - 2023-02-08

### Changed

- Reset import-flag on plugin-activation

### Fixed

- Fixed compatibility with import of Pro-plugin

## [2.0.1] - 2023-01-30

### Fixed

- Fixed usage of classic position widget

## [2.0.0] - 2023-01-12

### Added

- Added link "back to list" on detail-view (optional).
- Added possibility to different translate/change "Apply to this position" in archive- and single-view

### Changed

- Optimized support for Yoast SEO and Rank Math
- Optimized select- and multi-select-field for plugin-settings
- Optimized translation for german and german_formal.
- Fully compatible with PHP 7.4, 8.0, 8.1 and 8.2
- More logging of events with enabled plugin-debug-mode
- Advanced support for themes: GeneratePress, OceanWP, StartIt, Twenty Fourteen, Kadence, Neve, Hestia, Total, BeTheme

### Fixed

- Fixed loading of position content in detail page

## [1.2.2] - 2022-12-15

### Added

- Add button to cancel running import after it runs for 1 hour

### Changed

- Extended logging on import of positions by Personio

### Fixed

- Fixed wrong occupation slug for filter in archive listings
- Fixed some typos in texts

## [1.2.1] - 2022-11-28

### Changed

- Optimized filter-formular for better scrolling in website after submitting
- Previously untranslatable texts adapted

### Fixed

- Fixed embedding of new group-title-template
- Fixed parameter "ids" on Shortcode "personioPositions"

## [1.2.0] - 2022-11-04

### Added

- Added limit for list of positions in frontend to 10 per list and without pagination
- Added possibility to group the list of positions by one of the taxonomies
  (also added in Gutenberg-Block as filter-option)

### Changed

- Optimized compatibility with other plugins using JavaScript in wp-admin
- Optimized support for some popular WordPress-themes like Astra
- Removed possibility to link the position-title in detail-view
- Removed our own custom post type from post type list in Redirection-plugin
- Compatibility with WordPress 6.1
- Updated dependencies for Gutenberg-scripts

### Fixed

- Fixed usage of archive template for child-themes
- Fixed REST-API reading of excerpt of positions
- Fixed visibility of settings-link in plugin-list
- Fixed pagination of positions-archive

## [1.1.1] - 2022-09-02

### Fixed

- Fixed usage of templates in child-themes

## [1.1.0] - 2022-08-22

### Added

- Add some hooks for changes in position-listings

### Changed

- Optimized loading of filters in listings
- Compatibility with WordPress 6.0.2

## [1.0.0] - 2022-06-15

### Added

- Initial release

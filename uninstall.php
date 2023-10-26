<?php

/**
 * Tasks to run during uninstallation of this plugin.
 */

use personioIntegration\installer;

// set version number
const WP_PERSONIO_INTEGRATION_VERSION = '@@VersionNumber@@';

// save plugin-path
const WP_PERSONIO_INTEGRATION_PLUGIN = __FILE__;

// include necessary files
include 'inc/autoload.php';
include 'inc/constants.php';

(new installer)->removeAllData( array( get_option('personioIntegrationDeleteOnUninstall', 0) ) );

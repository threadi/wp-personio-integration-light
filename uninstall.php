<?php

/**
 * Tasks to run during uninstallation of this plugin.
 */

use personioIntegration\installer;

// get plugin-path for uninstaller-functions
const WP_PERSONIO_INTEGRATION_PLUGIN = __FILE__;
const WP_PERSONIO_INTEGRATION_VERSION = '0.6.5';

// include necessary files
include 'inc/autoload.php';
include 'inc/constants.php';

(new installer)->removeAllData( [get_option('personioIntegrationDeleteOnUninstall', 0)] );
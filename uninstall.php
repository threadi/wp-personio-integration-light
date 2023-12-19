<?php
/**
 * Tasks to run during uninstallation of this plugin.
 *
 * @package personio-integration-light
 */

use personioIntegration\installer;

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// set version number.
const WP_PERSONIO_INTEGRATION_VERSION = '@@VersionNumber@@';

// save plugin-path.
const WP_PERSONIO_INTEGRATION_PLUGIN = __FILE__;

// include necessary files.
require 'inc/autoload.php';
require 'inc/constants.php';

( new Installer() )->removeAllData( array( get_option( 'personioIntegrationDeleteOnUninstall', 0 ) ) );

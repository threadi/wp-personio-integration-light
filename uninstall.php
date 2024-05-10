<?php
/**
 * Tasks to run during uninstallation of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight;

use PersonioIntegrationLight\Plugin\Uninstaller;

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// do nothing if PHP-version is not 8.0 or newer.
if ( version_compare( PHP_VERSION, '8.0', '<' ) ) {
	return;
}


// set version number.
define( 'WP_PERSONIO_INTEGRATION_VERSION', '@@VersionNumber@@' );

// save plugin-path.
define( 'WP_PERSONIO_INTEGRATION_PLUGIN', __FILE__ );

if ( file_exists( __DIR__ . '/lib/autoload.php' ) ) {
	require_once __DIR__ . '/lib/autoload.php';
}

// include necessary files.
require 'inc/constants.php';

// run uninstaller.
Uninstaller::get_instance()->run( array( get_option( 'personioIntegrationDeleteOnUninstall', 0 ) ) );

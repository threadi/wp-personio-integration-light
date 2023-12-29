<?php
/**
 * Tasks to run during uninstallation of this plugin.
 *
 * @package personio-integration-light
 */

namespace App;

use App\Plugin\Uninstaller;

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

Uninstaller::get_instance()->run( array( get_option( 'personioIntegrationDeleteOnUninstall', 0 ) ) );

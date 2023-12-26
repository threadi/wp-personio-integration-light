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

// include necessary files.
require 'inc/autoload.php';
require 'inc/constants.php';

( new Uninstaller() )->run( array( get_option( 'personioIntegrationDeleteOnUninstall', 0 ) ) );

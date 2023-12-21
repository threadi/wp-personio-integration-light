<?php
/**
 * Plugin Name:       Personio Integration Light
 * Description:       Provides recruiting handling for Personio.
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Version:           @@VersionNumber@@
 * Author:            laOlaWeb
 * Author URI:        https://laolaweb.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       personio-integration-light
 *
 * @package personio-integration-light
 */

namespace personioIntegration;

// set version number.
define( 'WP_PERSONIO_INTEGRATION_VERSION', '@@VersionNumber@@');

// save plugin-path.
define( 'WP_PERSONIO_INTEGRATION_PLUGIN', __FILE__);

if ( file_exists( __DIR__ . '/lib/autoload.php' ) ) {
	require_once __DIR__ . '/lib/autoload.php';
}

require_once __DIR__ . '/inc/constants.php';

add_action( 'plugins_loaded', function() {
	Init::get_instance()->init();
} );

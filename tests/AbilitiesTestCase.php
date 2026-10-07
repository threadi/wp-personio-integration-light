<?php
/**
 * File to handle the main object for each test class for abilities.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests;

use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\Imports\Xml;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use WP_Ability;

/**
 * Object to handle the preparations for each test class for abilities.
 */
abstract class AbilitiesTestCase extends PersonioTestCase {

	/**
	 * The category of the abilities of this plugin, which is also the prefix of their names.
	 *
	 * @var string
	 */
	protected static string $category = 'personio-integration';

	/**
	 * Prepare the test environment.
	 *
	 * @return void
	 */
	public function set_up(): void {
		parent::set_up();

		// skip the test if the WordPress under test does not support abilities (they are available since WordPress 6.9).
		if ( ! function_exists( 'wp_get_ability' ) ) {
			$this->markTestSkipped( 'The Abilities API is not available in this WordPress.' );
		}

		// The import only collects the new positions if an import extension is enabled while the plugin is loaded.
		// The tests start without any settings, so this is not the case here: initialize the imports again, now
		// that the plugin has been activated. This is done for each test, as WordPress resets the hooks after each test.
		Imports::get_instance()->init();

		// mark that neither an import nor a deletion is running.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, 0 );

		// delete all positions to have a clean start.
		PersonioPosition::get_instance()->delete_positions();

		// use the Personio URL with valid positions.
		update_option( 'personioIntegrationUrl', self::$personio_url );
	}

	/**
	 * Run the ability with the given name as the actual user and return its result.
	 *
	 * @param string $name  The name of the ability without the category, e.g. "get-positions".
	 * @param mixed  $input The input for the ability.
	 *
	 * @return mixed
	 */
	protected function run_ability( string $name, mixed $input = null ): mixed {
		// get the ability.
		$ability = wp_get_ability( self::$category . '/' . $name );

		// it must be registered.
		$this->assertInstanceOf( WP_Ability::class, $ability );

		// run it.
		return $ability->execute( $input );
	}

	/**
	 * Create a user with the given role and set it as the actual user.
	 *
	 * @param string $role The role, e.g. "administrator".
	 *
	 * @return int
	 */
	protected function set_user( string $role ): int {
		$user_id = self::factory()->user->create( array( 'role' => $role ) );
		wp_set_current_user( $user_id );
		return $user_id;
	}

	/**
	 * Import the positions from the given Personio URL without using an ability and return their amount.
	 *
	 * @param string $url The Personio URL, the one with valid positions is used if empty.
	 *
	 * @return int
	 */
	protected function import_positions( string $url = '' ): int {
		// set the Personio URL.
		update_option( 'personioIntegrationUrl', empty( $url ) ? self::$personio_url : $url );

		// import them via XML-import.
		$imports_obj = new Xml();
		$imports_obj->run();

		// mark that the import is not running anymore.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );

		// return the amount of positions.
		return $this->count_positions();
	}

	/**
	 * Count all currently imported positions.
	 *
	 * @return int
	 */
	protected function count_positions(): int {
		return Positions::get_instance()->get_positions_count();
	}
}

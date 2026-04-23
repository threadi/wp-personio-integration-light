<?php
/**
 * File to some test scenarios for one topic.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Scenario;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to run some test scenarios for one topic.
 */
class Uninstall extends PersonioTestCase {

	/**
	 * Run uninstallation with deletion of all external files and test the database.
	 *
	 * @return void
	 */
	public function test_uninstall_without_delete_all(): void {
		// run the uninstallation.
		$this->uninstallation();
	}

	/**
	 * Run uninstallation with deletion of all external files and test the database.
	 *
	 * @return void
	 */
	public function test_uninstall_with_delete_all(): void {
		// run the uninstallation.
		$this->uninstallation( array( 1 ) );
	}

	/**
	 * The main tests for uninstallation.
	 *
	 * @param array<int,int> $delete_all Marker to delete all.
	 *
	 * @return void
	 */
	private function uninstallation( array $delete_all = array() ): void {
		// get the list of settings.
		$settings = array();
		foreach( \PersonioIntegrationLight\Plugin\Settings::get_instance()->get_settings_object()->get_settings() as $setting ) {
			$settings[ $setting->get_name() ] = $setting->get_value();
		}

		// set a transient.
		$transient = \PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transients::get_instance()->add();
		$transient->set_name( 'my_test' );
		$transient->save();
		$this->assertIsObject( \PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transients::get_instance()->get_transient_by_name( 'my_test' ) );

		// run the uninstallation.
		\PersonioIntegrationLight\Plugin\Uninstaller::get_instance()->run( $delete_all );

		// test if the transient has been deleted.
		$test_transient = \PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transients::get_instance()->get_transient_by_name( 'my_test' );
		$this->assertFalse( $test_transient->is_set() );

		// do not test the settings if the deletion is not requested.
		if( empty( $delete_all ) ) {
			return;
		}

		// test if the settings have been deleted.
		foreach( $settings as $name => $value ) {
			$value = get_option( $name );
			$this->assertIsBool( $value );
			$this->assertFalse( $value );
		}
	}
}

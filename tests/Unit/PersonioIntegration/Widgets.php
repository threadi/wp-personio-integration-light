<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Widgets.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\PersonioIntegration;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\Widgets.
 */
class Widgets extends PersonioTestCase {
	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_widgets(): void {
		$widgets = \PersonioIntegrationLight\PersonioIntegration\Widgets::get_instance()->get_widgets();
		$this->assertIsArray( $widgets );
		$this->assertNotEmpty( $widgets );
		$this->assertIsString( $widgets[0] );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function get_widgets_as_objects(): void {
		$widgets = \PersonioIntegrationLight\PersonioIntegration\Widgets::get_instance()->get_widgets_as_objects();
		$this->assertIsArray( $widgets );
		$this->assertNotEmpty( $widgets );
		$this->assertInstanceOf( '\PersonioIntegrationLight\PersonioIntegration\Widget_Base', $widgets[0] );
	}
}

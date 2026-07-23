<?php
/**
 * Data-driven tests for the settings validation callbacks.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Plugin;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Test the templates.
 */
class Templates extends PersonioTestCase {
	/**
	 * Test that the filter template ignores unknown filtertypes.
	 *
	 * @return void
	 */
	public function test_filter_template_ignores_unknown_filtertype(): void {
		self::get_single_position();

		ob_start();
		\PersonioIntegrationLight\Plugin\Templates::get_instance()->get_filter_template( 'office', array( 'filtertype' => 'no-such-filtertype', 'anchor' => '' ) );
		$output = ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * Test that the filter template renders known filtertypes.
	 *
	 * @return void
	 */
	public function test_filter_template_renders_known_filtertype(): void {
		self::get_single_position();

		ob_start();
		\PersonioIntegrationLight\Plugin\Templates::get_instance()->get_filter_template( 'office', array( 'filtertype' => 'linklist', 'anchor' => '' ) );
		$output = ob_get_clean();

		$this->assertNotSame( '', $output );
	}
}

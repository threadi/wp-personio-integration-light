<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\PageBuilder\Page_Builders.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\PageBuilder;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in the class PersonioIntegrationLight\PageBuilder\Page_Builders.
 */
class Page_Builders extends PersonioTestCase {
	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_name(): void {
		$name = \PersonioIntegrationLight\PageBuilder\Page_Builders::get_instance()->get_name();
		$this->assertIsString( $name );
		$this->assertNotEmpty( $name );
		$this->assertEquals( 'Page Builders', $name );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_page_builders(): void {
		$page_builders = \PersonioIntegrationLight\PageBuilder\Page_Builders::get_instance()->get_page_builders();
		$this->assertIsArray( $page_builders );
		$this->assertNotEmpty( $page_builders );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_page_builders_as_objects(): void {
		$page_builders = \PersonioIntegrationLight\PageBuilder\Page_Builders::get_instance()->get_page_builders_as_objects();
		$this->assertIsArray( $page_builders );
		$this->assertNotEmpty( $page_builders );
		foreach( $page_builders as $page_builder ) {
			$this->assertInstanceOf( '\PersonioIntegrationLight\PageBuilder\PageBuilder_Base', $page_builder );
		}
	}

	/**
	 * Test if the Gutenberg object can be loaded.
	 *
	 * @return void
	 */
	public function test_get_gutenberg(): void {
		$page_builders = \PersonioIntegrationLight\PageBuilder\Page_Builders::get_instance()->get_page_builders_as_objects();
		$gutenberg = false;
		foreach( $page_builders as $page_builder ) {
			if( 'gutenberg' === $page_builder->get_name() ) {
				$gutenberg = $page_builder;
			}
		}
		if( $gutenberg ) {
			$this->assertInstanceOf( '\PersonioIntegrationLight\PageBuilder\Gutenberg', $gutenberg );
		}
		else {
			$this->hasFailed();
		}
	}
}

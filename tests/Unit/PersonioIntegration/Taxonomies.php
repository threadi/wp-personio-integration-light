<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Taxonomies.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\Taxonomies.
 */
class Taxonomies extends WP_UnitTestCase {

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_taxonomies(): void {
		$taxonomies = \PersonioIntegrationLight\PersonioIntegration\Taxonomies::get_instance()->get_taxonomies();
		$this->assertIsArray( $taxonomies );
		$this->assertNotEmpty( $taxonomies );
		$this->assertArrayHasKey( WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY, $taxonomies );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_taxonomy_labels(): void {
		$taxonomy_labels = \PersonioIntegrationLight\PersonioIntegration\Taxonomies::get_instance()->get_taxonomy_labels();
		$this->assertIsArray( $taxonomy_labels );
		$this->assertNotEmpty( $taxonomy_labels );
		$this->assertArrayHasKey( WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY, $taxonomy_labels );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_taxonomy_label(): void {
		$taxonomy_labels = \PersonioIntegrationLight\PersonioIntegration\Taxonomies::get_instance()->get_taxonomy_label( WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY );
		$this->assertIsArray( $taxonomy_labels );
		$this->assertNotEmpty( $taxonomy_labels );
	}

}

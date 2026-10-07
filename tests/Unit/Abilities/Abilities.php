<?php
/**
 * Tests for class PersonioIntegrationLight\Abilities\Abilities.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Abilities;

use PersonioIntegrationLight\Tests\AbilitiesTestCase;
use WP_Ability;
use WP_Error;

/**
 * Object to test functions in the class PersonioIntegrationLight\Abilities\Abilities.
 */
class Abilities extends AbilitiesTestCase {

	/**
	 * Return all abilities this plugin provides: their name, the marker whether they only read data and a valid input.
	 *
	 * @return array<string,array<int,mixed>>
	 */
	public function get_abilities(): array {
		// a valid input for the abilities which need a template.
		$template = array( 'content' => '<!-- wp:paragraph --><p>Test</p><!-- /wp:paragraph -->' );

		return array(
			'get-positions'        => array( 'get-positions', true, array() ),
			'get-position'         => array( 'get-position', true, array() ),
			'get-taxonomies'       => array( 'get-taxonomies', true, array() ),
			'get-import-status'    => array( 'get-import-status', true, array() ),
			'run-import'           => array( 'run-import', false, array() ),
			'cancel-import'        => array( 'cancel-import', false, array() ),
			'delete-positions'     => array( 'delete-positions', false, array() ),
			'get-log'              => array( 'get-log', true, array() ),
			'get-builders'         => array( 'get-builders', true, array() ),
			'get-template-catalog' => array( 'get-template-catalog', true, array() ),
			'get-template'         => array( 'get-template', true, array() ),
			'preview-template'     => array( 'preview-template', false, $template ),
			'save-template'        => array( 'save-template', false, $template ),
			'reset-template'       => array( 'reset-template', false, array() ),
		);
	}

	/**
	 * Test if the category for our abilities is registered.
	 *
	 * @return void
	 */
	public function test_category_is_registered(): void {
		$this->assertTrue( wp_has_ability_category( self::$category ) );
	}

	/**
	 * Test if each ability is registered in our category and available for connected applications.
	 *
	 * @dataProvider get_abilities
	 *
	 * @param string $name     The name of the ability.
	 * @param bool   $readonly Whether the ability only reads data.
	 *
	 * @return void
	 */
	public function test_ability_is_registered( string $name, bool $readonly ): void {
		// get the ability.
		$ability = wp_get_ability( self::$category . '/' . $name );

		// test it.
		$this->assertInstanceOf( WP_Ability::class, $ability );
		$this->assertSame( self::$category, $ability->get_category() );

		// test its meta data.
		$meta = $ability->get_meta();
		$this->assertTrue( $meta['show_in_rest'] );
		$this->assertTrue( $meta['mcp']['public'] );
		$this->assertSame( $readonly, $meta['annotations']['readonly'] );
	}

	/**
	 * Test that no other abilities are registered in our category than the tested ones.
	 *
	 * @return void
	 */
	public function test_no_untested_abilities(): void {
		// get the names of our abilities.
		$names = array();
		foreach ( wp_get_abilities() as $ability ) {
			if ( self::$category === $ability->get_category() ) {
				$names[] = str_replace( self::$category . '/', '', $ability->get_name() );
			}
		}
		sort( $names );

		// get the expected names.
		$expected = array_keys( $this->get_abilities() );
		sort( $expected );

		// test it.
		$this->assertSame( $expected, $names );
	}

	/**
	 * Test that only the abilities, which remove data, are marked as destructive.
	 *
	 * @return void
	 */
	public function test_destructive_abilities(): void {
		// get the names of our destructive abilities.
		$names = array();
		foreach ( array_keys( $this->get_abilities() ) as $name ) {
			$meta = wp_get_ability( self::$category . '/' . $name )->get_meta();
			if ( ! empty( $meta['annotations']['destructive'] ) ) {
				$names[] = $name;
			}
		}
		sort( $names );

		// test it.
		$this->assertSame( array( 'delete-positions', 'reset-template' ), $names );
	}

	/**
	 * Test that no ability can be used without login.
	 *
	 * @dataProvider get_abilities
	 *
	 * @param string              $name     The name of the ability.
	 * @param bool                $readonly Whether the ability only reads data.
	 * @param array<string,mixed> $input    A valid input for the ability.
	 *
	 * @return void
	 */
	public function test_ability_is_not_available_without_login( string $name, bool $readonly, array $input ): void {
		// use no user.
		wp_set_current_user( 0 );

		// run the ability.
		$result = $this->run_ability( $name, $input );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'ability_invalid_permissions', $result->get_error_code() );
	}

	/**
	 * Test that a subscriber can not use any ability.
	 *
	 * @dataProvider get_abilities
	 *
	 * @param string              $name     The name of the ability.
	 * @param bool                $readonly Whether the ability only reads data.
	 * @param array<string,mixed> $input    A valid input for the ability.
	 *
	 * @return void
	 */
	public function test_ability_is_not_available_for_subscriber( string $name, bool $readonly, array $input ): void {
		// use a subscriber.
		$this->set_user( 'subscriber' );

		// run the ability.
		$result = $this->run_ability( $name, $input );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'ability_invalid_permissions', $result->get_error_code() );
	}

	/**
	 * Test to get the list of positions.
	 *
	 * @return void
	 */
	public function test_get_positions(): void {
		// import the positions and use an editor.
		$count = $this->import_positions();
		$this->set_user( 'editor' );

		// get the positions.
		$result = $this->run_ability( 'get-positions', array() );

		// test it.
		$this->assertIsArray( $result );
		$this->assertGreaterThan( 0, $count );
		$this->assertSame( $count, $result['total'] );
		$this->assertSame( $count, $result['count'] );
		$this->assertCount( $count, $result['positions'] );

		// test the first position.
		$position = $result['positions'][0];
		$this->assertGreaterThan( 0, $position['post_id'] );
		$this->assertNotEmpty( $position['personio_id'] );
		$this->assertNotEmpty( $position['title'] );
		$this->assertNotEmpty( $position['url'] );
		$this->assertArrayNotHasKey( 'description', $position );
	}

	/**
	 * Test to get the list of positions if there are none.
	 *
	 * @return void
	 */
	public function test_get_positions_without_positions(): void {
		// use an editor.
		$this->set_user( 'editor' );

		// get the positions.
		$result = $this->run_ability( 'get-positions', array() );

		// test it.
		$this->assertIsArray( $result );
		$this->assertSame( 0, $result['total'] );
		$this->assertSame( array(), $result['positions'] );
	}

	/**
	 * Test to get a limited list of positions with their descriptions.
	 *
	 * @return void
	 */
	public function test_get_positions_with_limit_and_description(): void {
		// import the positions and use an editor.
		$count = $this->import_positions();
		$this->set_user( 'editor' );

		// get the positions.
		$result = $this->run_ability(
			'get-positions',
			array(
				'limit'            => 2,
				'with_description' => true,
			)
		);

		// test it.
		$this->assertIsArray( $result );
		$this->assertSame( 2, $result['count'] );
		$this->assertSame( $count, $result['total'] );
		$this->assertCount( 2, $result['positions'] );
		$this->assertIsArray( $result['positions'][0]['description'] );
		$this->assertArrayHasKey( 'application_url', $result['positions'][0] );
	}

	/**
	 * Test to get the list of positions with a limit which is not allowed.
	 *
	 * @return void
	 */
	public function test_get_positions_with_invalid_limit(): void {
		// use an editor.
		$this->set_user( 'editor' );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $this->run_ability( 'get-positions', array( 'limit' => 101 ) ) );
	}

	/**
	 * Test to get the list of positions filtered by an unknown taxonomy.
	 *
	 * @return void
	 */
	public function test_get_positions_with_unknown_taxonomy(): void {
		// use an editor.
		$this->set_user( 'editor' );

		// get the positions.
		$result = $this->run_ability( 'get-positions', array( 'taxonomies' => array( 'unknown' => 'value' ) ) );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'personio_integration_unknown_taxonomy', $result->get_error_code() );
	}

	/**
	 * Test to get the list of positions filtered by a term.
	 *
	 * @return void
	 */
	public function test_get_positions_filtered_by_term(): void {
		// import the positions and use an editor.
		$count = $this->import_positions();
		$this->set_user( 'editor' );

		// get the first taxonomy which is used by a position.
		$taxonomy = false;
		foreach ( $this->run_ability( 'get-taxonomies', array() )['taxonomies'] as $entry ) {
			foreach ( $entry['terms'] as $term ) {
				if ( $term['count'] > 0 ) {
					$taxonomy = array( $entry['slug'] => $term['slug'] );
					$expected = $term['count'];
					break 2;
				}
			}
		}
		$this->assertIsArray( $taxonomy );

		// get the positions with this term.
		$result = $this->run_ability(
			'get-positions',
			array(
				'taxonomies' => $taxonomy,
				'limit'      => 100,
			)
		);

		// test it.
		$this->assertIsArray( $result );
		$this->assertSame( $expected, $result['total'] );
		$this->assertLessThanOrEqual( $count, $result['total'] );

		// each returned position must have a term in this taxonomy.
		foreach ( $result['positions'] as $position ) {
			$this->assertNotEmpty( ( (array) $position['taxonomies'] )[ array_key_first( $taxonomy ) ] );
		}
	}

	/**
	 * Test to get a single position by its post-ID.
	 *
	 * @return void
	 */
	public function test_get_position_by_post_id(): void {
		// get a test position object and use an editor.
		$test_position_obj = self::get_single_position();
		$this->set_user( 'editor' );

		// get the position.
		$result = $this->run_ability( 'get-position', array( 'post_id' => $test_position_obj->get_id() ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertSame( $test_position_obj->get_id(), $result['post_id'] );
		$this->assertSame( $test_position_obj->get_personio_id(), $result['personio_id'] );
		$this->assertSame( $test_position_obj->get_title(), $result['title'] );
		$this->assertIsArray( $result['description'] );
		$this->assertNotEmpty( $result['application_url'] );
	}

	/**
	 * Test to get a single position by its Personio ID.
	 *
	 * @return void
	 */
	public function test_get_position_by_personio_id(): void {
		// get a test position object and use an editor.
		$test_position_obj = self::get_single_position();
		$this->set_user( 'editor' );

		// get the position.
		$result = $this->run_ability( 'get-position', array( 'personio_id' => $test_position_obj->get_personio_id() ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertSame( $test_position_obj->get_id(), $result['post_id'] );
	}

	/**
	 * Test to get a single position without any ID.
	 *
	 * @return void
	 */
	public function test_get_position_without_id(): void {
		// use an editor.
		$this->set_user( 'editor' );

		// get the position.
		$result = $this->run_ability( 'get-position', array() );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'personio_integration_missing_position', $result->get_error_code() );
	}

	/**
	 * Test to get a post which is not a position.
	 *
	 * @return void
	 */
	public function test_get_position_which_is_no_position(): void {
		// create a post and use an editor.
		$post_id = self::factory()->post->create();
		$this->set_user( 'editor' );

		// get the position.
		$result = $this->run_ability( 'get-position', array( 'post_id' => $post_id ) );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'personio_integration_position_not_found', $result->get_error_code() );
	}

	/**
	 * Test to get the taxonomies with their terms and the languages.
	 *
	 * @return void
	 */
	public function test_get_taxonomies(): void {
		// import the positions and use an editor.
		$this->import_positions();
		$this->set_user( 'editor' );

		// get the taxonomies.
		$result = $this->run_ability( 'get-taxonomies', array() );

		// test it.
		$this->assertIsArray( $result );
		$this->assertNotEmpty( $result['taxonomies'] );
		$this->assertNotEmpty( (array) $result['languages'] );

		// test the first taxonomy.
		$taxonomy = $result['taxonomies'][0];
		$this->assertNotEmpty( $taxonomy['slug'] );
		$this->assertNotEmpty( $taxonomy['name'] );
		$this->assertNotEmpty( $taxonomy['label'] );
		$this->assertIsArray( $taxonomy['terms'] );
	}

	/**
	 * Test to get a single taxonomy.
	 *
	 * @return void
	 */
	public function test_get_single_taxonomy(): void {
		// use an editor.
		$this->set_user( 'editor' );

		// get the slug of the first taxonomy.
		$slug = $this->run_ability( 'get-taxonomies', array() )['taxonomies'][0]['slug'];

		// get this taxonomy.
		$result = $this->run_ability( 'get-taxonomies', array( 'taxonomy' => $slug ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertCount( 1, $result['taxonomies'] );
		$this->assertSame( $slug, $result['taxonomies'][0]['slug'] );
	}

	/**
	 * Test that an editor can read positions, but not the state of the import.
	 *
	 * @return void
	 */
	public function test_get_import_status_is_not_available_for_editor(): void {
		// use an editor.
		$this->set_user( 'editor' );

		// test it.
		$this->assertIsArray( $this->run_ability( 'get-positions', array() ) );
		$this->assertInstanceOf( WP_Error::class, $this->run_ability( 'get-import-status', array() ) );
	}

	/**
	 * Test to get the state of the import if no import is running.
	 *
	 * @return void
	 */
	public function test_get_import_status(): void {
		// import the positions and use an administrator.
		$count = $this->import_positions();
		$this->set_user( 'administrator' );

		// get the state.
		$result = $this->run_ability( 'get-import-status', array() );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['is_running'] );
		$this->assertSame( '', $result['started_at'] );
		$this->assertSame( $count, $result['position_count'] );
		$this->assertSame( array(), $result['errors'] );
		$this->assertTrue( $result['has_personio_url'] );
		$this->assertSame( 'xml_import', $result['import_type'] );
		$this->assertSame( $count, $result['new_positions'] );
		$this->assertGreaterThan( 0, $result['progress']['max'] );
		$this->assertSame( $result['progress']['max'], $result['progress']['count'] );
	}

	/**
	 * Test to get the state of the import if an import is running.
	 *
	 * @return void
	 */
	public function test_get_import_status_if_import_is_running(): void {
		// use an administrator.
		$this->set_user( 'administrator' );

		// mark that an import is already running.
		$started = time() - 90;
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, $started );

		// get the state.
		$result = $this->run_ability( 'get-import-status', array() );

		// test it.
		$this->assertIsArray( $result );
		$this->assertTrue( $result['is_running'] );
		$this->assertSame( gmdate( 'c', $started ), $result['started_at'] );
	}

	/**
	 * Test to get the state of the import after an import with errors.
	 *
	 * @return void
	 */
	public function test_get_import_status_after_import_with_errors(): void {
		// import the faulty positions and use an administrator.
		$this->import_positions( self::$personio_faulty_url );
		$this->set_user( 'administrator' );

		// get the state.
		$result = $this->run_ability( 'get-import-status', array() );

		// test it.
		$this->assertIsArray( $result );
		$this->assertNotEmpty( $result['errors'] );
		$this->assertSame( wp_strip_all_tags( $result['errors'][0] ), $result['errors'][0] );
	}
}

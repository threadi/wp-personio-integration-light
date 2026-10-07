<?php
/**
 * Tests for class PersonioIntegrationLight\Abilities\Template_Abilities.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Abilities;

use PersonioIntegrationLight\Tests\AbilitiesTestCase;
use WP_Error;

/**
 * Object to test functions in the class PersonioIntegrationLight\Abilities\Template_Abilities.
 *
 * The templates use only blocks of WordPress, so the tests do not depend on the built blocks of this plugin.
 */
class Template_Abilities extends AbilitiesTestCase {

	/**
	 * A valid template which only uses blocks of WordPress.
	 *
	 * @var string
	 */
	private static string $template = '<!-- wp:group {"tagName":"main"} --><main class="wp-block-group"><!-- wp:heading --><h2 class="wp-block-heading">Test headline</h2><!-- /wp:heading --></main><!-- /wp:group -->';

	/**
	 * Prepare the test environment.
	 *
	 * @return void
	 */
	public function set_up(): void {
		parent::set_up();

		// the templates are only available for administrators.
		$this->set_user( 'administrator' );
	}

	/**
	 * Use a block theme for the actual test, as the templates of the block editor are only available with it.
	 *
	 * @return void
	 */
	private function use_block_theme(): void {
		// get the first installed block theme, preferred the one from the test data of WordPress.
		$block_theme = '';
		foreach ( wp_get_themes() as $slug => $theme ) {
			if ( ! $theme->is_block_theme() ) {
				continue;
			}
			if ( empty( $block_theme ) || 'block-theme' === $slug ) {
				$block_theme = (string) $slug;
			}
		}

		// skip the test if no block theme is installed.
		if ( empty( $block_theme ) ) {
			$this->markTestSkipped( 'No block theme is installed.' );
		}

		// use it.
		switch_theme( $block_theme );
	}

	/**
	 * Return the template types.
	 *
	 * @return array<string,array<int,string>>
	 */
	public function get_template_types(): array {
		return array(
			'single'  => array( 'single' ),
			'archive' => array( 'archive' ),
		);
	}

	/**
	 * Return the abilities which need an available page builder, with a valid input.
	 *
	 * @return array<string,array<int,mixed>>
	 */
	public function get_template_abilities(): array {
		return array(
			'get-template-catalog' => array( 'get-template-catalog', array() ),
			'get-template'         => array( 'get-template', array() ),
			'preview-template'     => array( 'preview-template', array( 'content' => self::$template ) ),
			'save-template'        => array(
				'save-template',
				array(
					'content' => self::$template,
					'dry_run' => false,
				)
			),
			'reset-template'       => array( 'reset-template', array( 'dry_run' => false ) ),
		);
	}

	/**
	 * Test to get the page builders with a classic theme: the block editor is listed, but not available.
	 *
	 * @return void
	 */
	public function test_get_builders_with_classic_theme(): void {
		// run the ability.
		$result = $this->run_ability( 'get-builders', array() );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( wp_is_block_theme() );
		$builders = array_column( $result['builders'], null, 'name' );
		$this->assertArrayHasKey( 'gutenberg', $builders );
		$this->assertFalse( $builders['gutenberg']['available'] );
		$this->assertFalse( $builders['gutenberg']['can_save'] );
		$this->assertNotEmpty( $builders['gutenberg']['reason'] );
	}

	/**
	 * Test to use the templates with a classic theme: no page builder is available for them.
	 *
	 * @dataProvider get_template_abilities
	 *
	 * @param string              $name  The name of the ability.
	 * @param array<string,mixed> $input The input for the ability.
	 *
	 * @return void
	 */
	public function test_template_ability_with_classic_theme( string $name, array $input ): void {
		// run the ability.
		$result = $this->run_ability( $name, $input );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'personio_integration_no_builder_available', $result->get_error_code() );
	}

	/**
	 * Test to get the page builders with a block theme: the block editor is available.
	 *
	 * @return void
	 */
	public function test_get_builders_with_block_theme(): void {
		$this->use_block_theme();

		// run the ability.
		$result = $this->run_ability( 'get-builders', array() );

		// test it.
		$this->assertIsArray( $result );
		$builders = array_column( $result['builders'], null, 'name' );
		$this->assertTrue( $builders['gutenberg']['available'] );
		$this->assertTrue( $builders['gutenberg']['can_save'] );
		$this->assertSame( '', $builders['gutenberg']['reason'] );
		$this->assertSame( array( 'single', 'archive' ), array_keys( (array) $builders['gutenberg']['template_types'] ) );
	}

	/**
	 * Test that an editor can read the page builders and the catalog, but not the templates.
	 *
	 * @dataProvider get_template_abilities
	 *
	 * @param string              $name  The name of the ability.
	 * @param array<string,mixed> $input The input for the ability.
	 *
	 * @return void
	 */
	public function test_template_ability_as_editor( string $name, array $input ): void {
		$this->use_block_theme();

		// use an editor.
		$this->set_user( 'editor' );

		// the page builders can be read.
		$this->assertIsArray( $this->run_ability( 'get-builders', array() ) );

		// run the ability.
		$result = $this->run_ability( $name, $input );

		// test it: only the catalog is available.
		if ( 'get-template-catalog' === $name ) {
			$this->assertIsArray( $result );
			return;
		}
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'ability_invalid_permissions', $result->get_error_code() );
	}

	/**
	 * Test to get the catalog with the elements for templates.
	 *
	 * @return void
	 */
	public function test_get_template_catalog(): void {
		$this->use_block_theme();

		// run the ability.
		$result = $this->run_ability( 'get-template-catalog', array() );

		// test it.
		$this->assertIsArray( $result );
		$this->assertSame( 'gutenberg', $result['builder'] );
		$this->assertNotEmpty( $result['format'] );
		$this->assertNotEmpty( $result['elements'] );
		$this->assertNotEmpty( $result['hints'] );

		// each element must have a name and a description.
		foreach ( $result['elements'] as $element ) {
			$this->assertNotEmpty( $element['name'] );
			$this->assertNotEmpty( $element['description'] );
		}

		// the allowed values must be given as list of names and labels.
		$values = (array) $result['values'];
		foreach ( array( 'taxonomies', 'listing_templates', 'excerpt_templates', 'jobdescription_templates' ) as $list ) {
			$this->assertArrayHasKey( $list, $values );
			$this->assertNotEmpty( $values[ $list ] );
			$this->assertArrayHasKey( 'name', $values[ $list ][0] );
			$this->assertArrayHasKey( 'label', $values[ $list ][0] );
		}
	}

	/**
	 * Test to get the catalog for an unknown page builder.
	 *
	 * @return void
	 */
	public function test_get_template_catalog_for_unknown_builder(): void {
		$this->use_block_theme();

		// run the ability.
		$result = $this->run_ability( 'get-template-catalog', array( 'builder' => 'unknown' ) );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'personio_integration_unknown_builder', $result->get_error_code() );
	}

	/**
	 * Test to get the templates of this plugin.
	 *
	 * @dataProvider get_template_types
	 *
	 * @param string $type The template type.
	 *
	 * @return void
	 */
	public function test_get_template( string $type ): void {
		$this->use_block_theme();

		// run the ability.
		$result = $this->run_ability( 'get-template', array( 'type' => $type ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertSame( 'gutenberg', $result['builder'] );
		$this->assertSame( $type, $result['type'] );
		$this->assertSame( 'plugin', $result['source'] );
		$this->assertFalse( $result['is_customized'] );
		$this->assertStringContainsString( '<!-- wp:', $result['content'] );
		$this->assertSame( '', $result['css'] );
	}

	/**
	 * Test to get a template with an unknown type.
	 *
	 * @return void
	 */
	public function test_get_template_with_unknown_type(): void {
		$this->use_block_theme();

		// run the ability.
		$result = $this->run_ability( 'get-template', array( 'type' => 'unknown' ) );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'ability_invalid_input', $result->get_error_code() );
	}

	/**
	 * Test to validate and render a valid template.
	 *
	 * @dataProvider get_template_types
	 *
	 * @param string $type The template type.
	 *
	 * @return void
	 */
	public function test_preview_template( string $type ): void {
		$this->use_block_theme();

		// import the positions, as the detail view is rendered for a position.
		$this->import_positions();

		// run the ability.
		$result = $this->run_ability(
			'preview-template',
			array(
				'type'    => $type,
				'content' => self::$template,
			)
		);

		// test it.
		$this->assertIsArray( $result );
		$this->assertTrue( $result['valid'] );
		$this->assertSame( array(), $result['errors'] );
		$this->assertStringContainsString( 'Test headline', $result['html'] );
		$this->assertFalse( $result['truncated'] );

		// the detail view must be rendered for a position.
		if ( 'single' === $type ) {
			$this->assertGreaterThan( 0, $result['post_id'] );
		}

		// nothing must have been saved.
		$this->assertFalse( $this->run_ability( 'get-template', array( 'type' => $type ) )['is_customized'] );
	}

	/**
	 * Test to only validate a template.
	 *
	 * @return void
	 */
	public function test_preview_template_without_rendering(): void {
		$this->use_block_theme();

		// run the ability.
		$result = $this->run_ability(
			'preview-template',
			array(
				'content' => self::$template,
				'render'  => false,
			)
		);

		// test it.
		$this->assertIsArray( $result );
		$this->assertTrue( $result['valid'] );
		$this->assertSame( '', $result['html'] );
	}

	/**
	 * Test to validate a template with an unknown block.
	 *
	 * @return void
	 */
	public function test_preview_template_with_unknown_block(): void {
		$this->use_block_theme();

		// run the ability.
		$result = $this->run_ability( 'preview-template', array( 'content' => '<!-- wp:unknown/block /-->' ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['valid'] );
		$this->assertNotEmpty( $result['errors'] );
		$this->assertStringContainsString( 'unknown/block', $result['errors'][0] );
	}

	/**
	 * Test to preview a template without its content.
	 *
	 * @return void
	 */
	public function test_preview_template_without_content(): void {
		$this->use_block_theme();

		// run the ability.
		$result = $this->run_ability( 'preview-template', array() );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'ability_invalid_input', $result->get_error_code() );
	}

	/**
	 * Test that a template is not saved without the explicit request for it.
	 *
	 * @return void
	 */
	public function test_save_template_is_a_dry_run_by_default(): void {
		$this->use_block_theme();

		// run the ability.
		$result = $this->run_ability( 'save-template', array( 'content' => self::$template ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertTrue( $result['dry_run'] );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'create', $result['action'] );
		$this->assertSame( array(), $result['errors'] );

		// nothing must have been saved.
		$this->assertFalse( $this->run_ability( 'get-template', array() )['is_customized'] );
	}

	/**
	 * Test that a template with errors is not saved.
	 *
	 * @return void
	 */
	public function test_save_template_with_errors(): void {
		$this->use_block_theme();

		// run the ability.
		$result = $this->run_ability(
			'save-template',
			array(
				'content' => '<!-- wp:unknown/block /-->',
				'dry_run' => false,
			)
		);

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'none', $result['action'] );
		$this->assertNotEmpty( $result['errors'] );

		// nothing must have been saved.
		$this->assertFalse( $this->run_ability( 'get-template', array() )['is_customized'] );
	}

	/**
	 * Test to save, change and reset a template.
	 *
	 * @dataProvider get_template_types
	 *
	 * @param string $type The template type.
	 *
	 * @return void
	 */
	public function test_save_and_reset_template( string $type ): void {
		$this->use_block_theme();

		// get the other template type, which must stay untouched.
		$other_type = 'single' === $type ? 'archive' : 'single';

		// save the template.
		$result = $this->run_ability(
			'save-template',
			array(
				'type'    => $type,
				'content' => self::$template,
				'dry_run' => false,
			)
		);

		// test it.
		$this->assertIsArray( $result );
		$this->assertTrue( $result['done'] );
		$this->assertSame( 'create', $result['action'] );
		$this->assertGreaterThan( 0, $result['id'] );

		// the saved template must be returned now.
		$template = $this->run_ability( 'get-template', array( 'type' => $type ) );
		$this->assertTrue( $template['is_customized'] );
		$this->assertSame( self::$template, $template['content'] );

		// the other template must be untouched.
		$this->assertFalse( $this->run_ability( 'get-template', array( 'type' => $other_type ) )['is_customized'] );

		// change the template and add CSS.
		$changed = self::$template . '<!-- wp:paragraph --><p>Test paragraph</p><!-- /wp:paragraph -->';
		$update  = $this->run_ability(
			'save-template',
			array(
				'type'    => $type,
				'content' => $changed,
				'css'     => '.test { color: red; }',
				'dry_run' => false,
			)
		);

		// test it.
		$this->assertTrue( $update['done'] );
		$this->assertSame( 'update', $update['action'] );
		$this->assertSame( $result['id'], $update['id'] );
		$template = $this->run_ability( 'get-template', array( 'type' => $type ) );
		$this->assertSame( $changed, $template['content'] );
		$this->assertSame( '.test { color: red; }', $template['css'] );

		// check what the reset would do: it must not reset anything.
		$check = $this->run_ability( 'reset-template', array( 'type' => $type ) );
		$this->assertTrue( $check['dry_run'] );
		$this->assertFalse( $check['done'] );
		$this->assertSame( 'reset', $check['action'] );
		$this->assertTrue( $this->run_ability( 'get-template', array( 'type' => $type ) )['is_customized'] );

		// reset the template.
		$reset = $this->run_ability(
			'reset-template',
			array(
				'type'    => $type,
				'dry_run' => false,
			)
		);

		// test it.
		$this->assertTrue( $reset['done'] );
		$this->assertSame( 'reset', $reset['action'] );

		// the template of this plugin must be returned again.
		$template = $this->run_ability( 'get-template', array( 'type' => $type ) );
		$this->assertFalse( $template['is_customized'] );
		$this->assertSame( 'plugin', $template['source'] );
		$this->assertSame( '', $template['css'] );
	}

	/**
	 * Test to reset a template which is not customized.
	 *
	 * @return void
	 */
	public function test_reset_template_which_is_not_customized(): void {
		$this->use_block_theme();

		// run the ability.
		$result = $this->run_ability( 'reset-template', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'none', $result['action'] );
		$this->assertSame( array(), $result['errors'] );
	}
}

<?php
/**
 * File to handle support for pagebuilder Gutenberg aka Block Editor.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks_Basis;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Patterns;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Templates;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Variations;

/**
 * Object to handle the Gutenberg support.
 */
class Gutenberg extends PageBuilder_Base {
	/**
	 * Internal name of the page builder.
	 *
	 * @var string
	 */
	protected string $name = 'gutenberg';

	/**
	 * Instance of this object.
	 *
	 * @var ?Gutenberg
	 */
	private static ?Gutenberg $instance = null;

	/**
	 * Constructor for Init-Handler.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() { }

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Gutenberg {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize this PageBuilder support.
	 *
	 * @return void
	 */
	public function init(): void {
		// bail if Gutenbergs main functions does not exist.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// add our custom blocks.
		add_action( 'init', array( $this, 'register_blocks' ) );

		// add our custom pattern.
		add_action( 'init', array( $this, 'add_pattern' ) );

		// add our custom variations.
		add_action( 'init', array( $this, 'add_variations' ) );

		// bail if theme is not an FSE-theme with Block support.
		if ( ! $this->theme_support_block_templates() ) {
			// remove hint from settings.
			add_filter( 'personio_integration_settings', array( $this, 'remove_fse_hint' ) );
			return;
		}

		// add our custom templates and set to use them.
		add_action( 'init', array( $this, 'add_templates' ) );
		add_filter( 'personio_integration_load_single_template', '__return_true' );
		add_filter( 'personio_integration_load_archive_template', '__return_true' );

		// call parent init.
		parent::init();
	}

	/**
	 * Initialize the templates for Block Editor.
	 *
	 * @return void
	 */
	public function add_templates(): void {
		Templates::get_instance()->init();
	}

	/**
	 * Check if active theme supports block templates.
	 *
	 * @return bool
	 * @noinspection PhpUndefinedFunctionInspection
	 */
	private function theme_support_block_templates(): bool {
		if (
			! $this->current_theme_is_fse_theme() &&
			( ! function_exists( 'gutenberg_supports_block_templates' ) || ! gutenberg_supports_block_templates() )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the current theme is a block theme.
	 *
	 * @return bool
	 * @noinspection PhpUndefinedFunctionInspection
	 */
	private function current_theme_is_fse_theme(): bool {
		if ( function_exists( 'wp_is_block_theme' ) ) {
			return (bool) wp_is_block_theme();
		}
		if ( function_exists( 'gutenberg_is_fse_theme' ) ) {
			return (bool) gutenberg_is_fse_theme();
		}
		return false;
	}

	/**
	 * Return list of available blocks.
	 *
	 * @return array
	 */
	public function get_widgets(): array {
		$list = array(
			'PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Application_Button',
			'PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Archive',
			'PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Description',
			'PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Detail',
			'PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Filter_List',
			'PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Filter_Select',
			'PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Single',
		);

		// return resulting list.
		return apply_filters( 'personio_integration_gutenberg_blocks', $list );
	}

	/**
	 * Add our custom blocks.
	 *
	 * @return void
	 */
	public function register_blocks(): void {
		foreach ( $this->get_widgets() as $block_class_name ) {
			$obj = call_user_func( $block_class_name . '::get_instance' );
			if ( $obj instanceof Blocks_Basis ) {
				$obj->register();
			}
		}
	}

	/**
	 * Remove the FSE-hint from settings.
	 *
	 * @param array $settings Array with the settings.
	 *
	 * @return array
	 */
	public function remove_fse_hint( array $settings ): array {
		if ( isset( $settings['settings_section_template_list']['fields']['personio_integration_fse_theme_hint'] ) ) {
			unset( $settings['settings_section_template_list']['fields']['personio_integration_fse_theme_hint'] );
		}
		return $settings;
	}

	/**
	 * Initialize the pattern-object to register them.
	 *
	 * @return void
	 */
	public function add_pattern(): void {
		Patterns::get_instance()->init();
	}

	/**
	 * Initialize the variations-object to register them.
	 *
	 * @return void
	 */
	public function add_variations(): void {
		Variations::get_instance()->init();
	}
}

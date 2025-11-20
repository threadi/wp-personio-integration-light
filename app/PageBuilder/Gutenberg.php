<?php
/**
 * File to handle support for pagebuilder Gutenberg aka Block Editor.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Page;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Section;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Tab;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks_Basis;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Patterns;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Templates;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Variations;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;

/**
 * Object to handle the Gutenberg support.
 */
class Gutenberg extends PageBuilder_Base {
	/**
	 * The internal name of this extension.
	 *
	 * @var string
	 */
	protected string $name = 'gutenberg';

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Gutenberg
	 */
	private static ?Gutenberg $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Gutenberg {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this PageBuilder support.
	 *
	 * @return void
	 */
	public function init(): void {
		// add our custom blocks.
		add_action( 'init', array( $this, 'register_blocks' ) );

		// add our custom pattern.
		add_action( 'init', array( $this, 'add_pattern' ) );

		// add our custom variations.
		add_action( 'init', array( $this, 'add_variations' ) );

		// initialize the templates.
		add_action( 'init', array( $this, 'add_templates' ) );

		// add our custom block category.
		add_filter( 'block_categories_all', array( $this, 'add_block_category' ) );

		// bail if theme is not an FSE-theme with Block support.
		if ( ! $this->theme_support_block_templates() ) {
			return;
		}

		// add our custom templates and set to use them.
		add_action( 'init', array( $this, 'add_the_settings' ), 50 );
		add_filter( 'personio_integration_load_single_template', '__return_true' );
		add_filter( 'personio_integration_load_archive_template', '__return_true' );

		// misc.
		add_filter( 'body_class', array( $this, 'add_body_classes' ) );

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
	public function theme_support_block_templates(): bool {
		if (
			! $this->current_theme_is_fse_theme() &&
			( ! function_exists( 'gutenberg_supports_block_templates' ) || ! gutenberg_supports_block_templates() )
		) {
			return false;
		}

		return $this->current_theme_is_fse_theme();
	}

	/**
	 * Check if the current theme is a block theme.
	 *
	 * @return bool
	 * @noinspection PhpUndefinedFunctionInspection
	 */
	private function current_theme_is_fse_theme(): bool {
		$resulting_value = false;
		if ( function_exists( 'wp_is_block_theme' ) ) {
			$resulting_value = (bool) wp_is_block_theme();
		}
		if ( function_exists( 'gutenberg_is_fse_theme' ) ) {
			$resulting_value = (bool) gutenberg_is_fse_theme();
		}

		/**
		 * Filter whether this theme is a block theme (true) or not (false).
		 *
		 * @since 3.0.2 Available since 3.0.2
		 * @param bool $resulting_value The resulting value.
		 */
		return apply_filters( 'personio_integration_is_block_theme', $resulting_value );
	}

	/**
	 * Return list of available blocks.
	 *
	 * @return array<string>
	 */
	public function get_widgets(): array {
		$list = array();

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
			// extend the class name to match callable.
			$class_name = $block_class_name . '::get_instance';

			// bail if it is not callable.
			if ( ! is_callable( $class_name ) ) {
				continue;
			}

			// initiate object.
			$obj = $class_name();

			// bail if object is not "Blocks_Basis".
			if ( ! $obj instanceof Blocks_Basis ) {
				continue;
			}

			// run registering of this block.
			$obj->register();
		}
	}

	/**
	 * Add FSE-hint in settings.
	 *
	 * @return void
	 */
	public function add_the_settings(): void {
		// get settings object.
		$settings_obj = Settings::get_instance();

		// get the settings page.
		$settings_page = $settings_obj->get_page( 'personioPositions' );

		// bail if page could not be found.
		if ( ! $settings_page instanceof Page ) {
			return;
		}

		// get template tab.
		$template_tab = $settings_page->get_tab( 'templates' );

		// bail if template tab could not be found.
		if ( ! $template_tab instanceof Tab ) {
			return;
		}

		// get the section.
		$section = $template_tab->get_section( 'settings_section_template_list' );

		// bail if section could not be found.
		if ( ! $section instanceof Section ) {
			return;
		}

		// override the callback.
		$section->set_callback( array( $this, 'show_fse_hint' ) );
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

	/**
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Block Editor', 'personio-integration-light' );
	}

	/**
	 * Return whether this extension is enabled (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return class_exists( 'WP_Block_Type_Registry' ) && function_exists( 'register_block_type' ) && ! Helper::is_plugin_active( 'classic-editor/classic-editor.php' );
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return __( 'Use the Page Builder with which WordPress is already delivered to style your positions in the frontend of your website. This extension is automatically enabled if your WordPress does not disable the Block Editor.', 'personio-integration-light' );
	}

	/**
	 * Remove our own templates on uninstallation.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		Templates::get_instance()->remove_db_templates();
	}

	/**
	 * Add position specific classes in body class for single view.
	 *
	 * @param array<string> $css_classes List of classes.
	 *
	 * @return array<string>
	 */
	public function add_body_classes( array $css_classes ): array {
		// bail if this is not a single page.
		if ( ! is_single() ) {
			return $css_classes;
		}

		// bail if this is not our cpt.
		if ( PersonioPosition::get_instance()->get_name() !== get_post_type() ) {
			return $css_classes;
		}

		// get the position as object.
		$position_obj = Positions::get_instance()->get_position( get_queried_object_id() );

		// bail if position is not valid.
		if ( ! $position_obj->is_valid() ) {
			return $css_classes;
		}

		// add the position specific classes.
		$css_classes[] = \PersonioIntegrationLight\Plugin\Templates::get_instance()->get_classes_of_position( $position_obj );

		// return resulting classes.
		return $css_classes;
	}

	/**
	 * Show fse hint above template list.
	 *
	 * Will be removed if no FSE-theme is used.
	 *
	 * @return void
	 */
	public function show_fse_hint(): void {
		// get Block Editor URL.
		$editor_url = add_query_arg(
			array(
				'path' => '/wp_template/all',
			),
			admin_url( 'site-editor.php' )
		);

		/* translators: %1$s will be replaced with the name of the theme, %2$s will be replaced by the URL for the editor */
		echo '<p class="personio-integration-hint">' . wp_kses_post( sprintf( __( 'You are using with <i>%1$s</i> a modern block theme. The settings here will therefore might not work. Edit the archive- and single-template under <a href="%2$s">Appearance > Editor > Templates > Manage</a>.', 'personio-integration-light' ), esc_html( Helper::get_theme_title() ), esc_url( $editor_url ) ) ) . '</p>';
	}

	/**
	 * Return the installation state of the dependent plugin/theme.
	 *
	 * @return bool
	 */
	public function is_installed(): bool {
		return true;
	}

	/**
	 * Add our custom block category for all of our own widgets.
	 *
	 * @param array<int,array<string,mixed>> $block_categories List of block categories.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public function add_block_category( array $block_categories ): array {
		// add our custom block category.
		$block_categories[] = array(
			'slug'  => 'personio-integration',
			'title' => __( 'Personio Integration', 'personio-integration-light' ),
		);

		// return resulting list.
		return $block_categories;
	}
}

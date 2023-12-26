<?php
/**
 * File to handle template-tasks of this plugin.
 *
 * TODO Verwalten von Liste möglicher Templates (showTitle, showExcert etc.), so dass diese sich dynamisch ändern könnten
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

class Templates {
	/**
	 * Instance of this object.
	 *
	 * @var ?Init
	 */
	private static ?Templates $instance = null;

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
	public static function get_instance(): Templates {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Return possible archive-templates.
	 *
	 * @return array
	 */
	public function get_archive_templates(): array {
		return apply_filters(
			'personio_integration_templates_archive',
			array(
				'default' => __( 'Default', 'personio-integration-light' ),
				'listing' => __( 'Listings', 'personio-integration-light' ),
			)
		);
	}

	/**
	 * Load a template if it exists.
	 *
	 * Also load the requested file if it is located in the /wp-content/themes/xy/personio-integration-light/ directory.
	 *
	 * @param string $template The template to use.
	 * @return string
	 */
	public function get_template( string $template ): string {
		if ( is_embed() ) {
			return $template;
		}

		// check if requested template exist in theme.
		$theme_template = locate_template( trailingslashit( basename( dirname( WP_PERSONIO_INTEGRATION_PLUGIN ) ) ) . $template );
		if ( $theme_template ) {
			return $theme_template;
		}

		// check if requested template exist in plugin which uses our hook.
		$plugin_template = plugin_dir_path( apply_filters( 'personio_integration_set_template_directory', WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'templates/' . $template;
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}

		// return template from light-plugin.
		return plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'templates/' . $template;
	}

	/**
	 * Check if given template exist.
	 *
	 * @param string $template The searched template as to plugins template directory relative path.
	 * @return bool
	 */
	public function has_template( string $template ): bool {
		// check if requested template exist in theme.
		$theme_template = locate_template( trailingslashit( basename( dirname( WP_PERSONIO_INTEGRATION_PLUGIN ) ) ) . $template );
		if ( $theme_template ) {
			return true;
		}

		// check if requested template exist in plugin which uses our hook.
		$plugin_template = plugin_dir_path( apply_filters( 'personio_integration_set_template_directory', WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'templates/' . $template;
		if ( file_exists( $plugin_template ) ) {
			return true;
		}

		// return template from light-plugin.
		return file_exists( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'templates/' . $template );
	}
}

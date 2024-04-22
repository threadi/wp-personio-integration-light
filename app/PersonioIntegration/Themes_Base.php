<?php
/**
 * File to handle basic settings for theme-support.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;

/**
 * Object to handle extension of Positions in Pro-plugin.
 */
class Themes_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * Name of CSS-file for this theme.
	 *
	 * @var string
	 */
	protected string $css_file = '';

	/**
	 * Holds the wrapper-classes of this theme.
	 *
	 * @var string
	 */
	protected string $wrapper_classes = '';

	/**
	 * Initialize the support.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ) );
	}

	/**
	 * Return the name of this object.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Add own CSS and JS for backend.
	 *
	 * @return void
	 */
	public function add_styles(): void {
		wp_enqueue_style(
			'personio-integration-' . $this->get_name(),
			Helper::get_plugin_url() . 'css/' . $this->get_css_file(),
			array( 'personio-integration-styles', 'personio-integration-additional-styles' ),
			Helper::get_file_version( Helper::get_plugin_path() . 'css/' . $this->get_css_file() ),
		);
	}

	/**
	 * Get the CSS-file for the theme.
	 *
	 * @return string
	 */
	private function get_css_file(): string {
		// get the file name.
		$css_file = $this->css_file;

		// if debug-mode is not enabled, use minified file.
		if ( ! defined( 'WP_DEBUG' ) || ( defined( 'WP_DEBUG' ) && ! WP_DEBUG ) ) {
			$css_file = str_replace( '.css', '.min.css', $css_file );
		}

		// get the name of the used theme.
		$theme_name = $this->get_name();

		/**
		 * Filter the used CSS file for this theme.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $css_file Name of the CSS-file located in /css in this plugin.
		 * @param string $theme_name Internal name of the used theme (slug of the theme).
		 */
		return apply_filters( 'personio_integration_theme_css', $css_file, $theme_name );
	}

	/**
	 * Return the wrapper classes used for single- and archive-view.
	 *
	 * @return mixed
	 */
	public function get_wrapper_classes(): string {
		$wrapper_classes = $this->wrapper_classes;
		$theme_name      = $this->get_name();

		/**
		 * Filter the used CSS wrapper classes for this theme.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $css_file Name of the wrapper-classes.
		 * @param string $theme_name Internal name of the used theme (slug of the theme).
		 */
		return apply_filters( 'personio_integration_theme_wrapper_classes', $wrapper_classes, $theme_name );
	}
}

<?php
/**
 * File to handle the CSS, which belongs to the block templates for positions.
 *
 * @package personio-integration-light
 */

declare(strict_types=1);

namespace PersonioIntegrationLight\PageBuilder\Gutenberg;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;

/**
 * Save and output the CSS for the block templates of positions.
 *
 * The CSS is only loaded on the pages which use the template (detail view or list of positions).
 */
class Template_Styles {
	/**
	 * The name of the option with the CSS per template type.
	 */
	public const OPTION = 'personioIntegrationTemplateCss';

	/**
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Template_Styles
	 */
	private static ?Template_Styles $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Template_Styles {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ) );
	}

	/**
	 * Return the CSS for the given template type.
	 *
	 * @param string $type The template type, e.g. "single".
	 *
	 * @return string
	 */
	public function get_css( string $type ): string {
		$list = get_option( self::OPTION, array() );
		if ( ! is_array( $list ) || ! isset( $list[ $type ] ) || ! is_string( $list[ $type ] ) ) {
			return '';
		}
		return $list[ $type ];
	}

	/**
	 * Save the CSS for the given template type. An empty CSS removes it.
	 *
	 * @param string $type The template type, e.g. "single".
	 * @param string $css  The CSS.
	 *
	 * @return void
	 */
	public function set_css( string $type, string $css ): void {
		$list = get_option( self::OPTION, array() );
		if ( ! is_array( $list ) ) {
			$list = array();
		}

		// save or remove the CSS.
		$css = $this->sanitize( $css );
		if ( '' === $css ) {
			unset( $list[ $type ] );
		} else {
			$list[ $type ] = $css;
		}

		// remove the option if no CSS is left.
		if ( empty( $list ) ) {
			delete_option( self::OPTION );
			return;
		}
		update_option( self::OPTION, $list, false );
	}

	/**
	 * Sanitize the given CSS: no HTML, which could end the style element.
	 *
	 * @param string $css The CSS.
	 *
	 * @return string
	 */
	public function sanitize( string $css ): string {
		$css = wp_strip_all_tags( $css );
		$css = str_replace( '<', '', $css );
		return trim( $css );
	}

	/**
	 * Add the CSS on the pages, which use the templates for positions.
	 *
	 * @return void
	 */
	public function add_styles(): void {
		// get the template type of the actual page.
		$type = '';
		if ( is_singular( PersonioPosition::get_instance()->get_name() ) ) {
			$type = 'single';
		} elseif ( is_post_type_archive( PersonioPosition::get_instance()->get_name() ) ) {
			$type = 'archive';
		}

		// bail if the page does not use a template for positions.
		if ( '' === $type ) {
			return;
		}

		// bail if no CSS is set.
		$css = $this->get_css( $type );
		if ( '' === $css ) {
			return;
		}

		// add the CSS.
		wp_register_style( 'personio-integration-template', false, array(), md5( $css ) );
		wp_enqueue_style( 'personio-integration-template' );
		wp_add_inline_style( 'personio-integration-template', $css );
	}
}

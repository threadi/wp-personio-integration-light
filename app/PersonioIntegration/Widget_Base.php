<?php
/**
 * File to handle widget extensions for positions.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;

/**
 * Object which handles the base functions for widget extensions.
 */
class Widget_Base extends Extensions_Base {
	/**
	 * Internal name of the used category.
	 *
	 * @var string
	 */
	protected string $extension_category = 'widgets';

	/**
	 * Path to Block object.
	 *
	 * @var string
	 */
	protected string $gutenberg = '';

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Widget_Base
	 */
	private static ?Widget_Base $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Widget_Base {
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
		// bail if object is not enabled.
		if ( ! $this->is_enabled() ) {
			return;
		}

		// add to PageBuilders.
		add_filter( 'personio_integration_gutenberg_blocks', array( $this, 'add_block' ) );

		// add shortcode for this widget.
		add_shortcode( 'personio_integration_' . $this->get_name(), array( $this, 'get_shortcode' ) );

		// run additional tasks for this object.
		$instance = $this;
		/**
		 * Run additional tasks for this object.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param Widget_Base $instance The widget object.
		 */
		do_action( 'personio_integration_light_widgets_' . $this->get_name(), $instance );
	}

	/**
	 * Whether this extension is enabled by default (true) or not (false).
	 *
	 * @return bool
	 */
	protected function is_default_enabled(): bool {
		return true;
	}

	/**
	 * Return whether this extension is enabled (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return true;
	}

	/**
	 * Return whether this extension can be enabled by the user (true) or not (false).
	 *
	 * @return bool
	 */
	public function can_be_enabled_by_user(): bool {
		return true;
	}

	/**
	 * Return the field of this widget.
	 *
	 * @param string        $filter The requested filter.
	 * @param array<string> $attributes The settings for this field.
	 *
	 * @return void
	 */
	public function get_field( string $filter, array $attributes ): void {}

	/**
	 * Return the rendered widget.
	 *
	 * @param array<string,mixed> $attributes Attributes to configure the rendering.
	 *
	 * @return string
	 */
	public function render( array $attributes ): string {
		return '';
	}

	/**
	 * Add the block for circle search.
	 *
	 * @param array<string> $blocks List of blocks.
	 *
	 * @return array<string>
	 */
	public function add_block( array $blocks ): array {
		$blocks[] = $this->gutenberg;
		return $blocks;
	}

	/**
	 * Return the widgets shortcode content.
	 *
	 * @param array<string,mixed> $attributes List of attributes.
	 *
	 * @return string
	 */
	public function get_shortcode( array $attributes ): string {
		return $this->render( $attributes );
	}

	/**
	 * Return the list of params this widget requires.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function get_params(): array {
		return array();
	}

	/**
	 * Return position as object by request.
	 *
	 * Hints:
	 * - Bug https://github.com/WordPress/gutenberg/issues/40714 prevents clean usage in Query Loop (backend bad, frontend ok)
	 *
	 * @return Position|false
	 */
	public function get_position_by_request(): Position|false {
		// get positions object.
		$positions = Positions::get_instance();

		// return the position as object if the called ID is valid.
		$post_id = get_the_ID();
		if ( $post_id > 0 ) {
			$position_obj = $positions->get_position( $post_id );
			if ( $position_obj->is_valid() ) {
				return $position_obj;
			}
		}

		// fallback: get a random position, only during AJAX-request (e.g. in Gutenberg).
		if ( Helper::is_rest_request() ) {
			$position_array = $positions->get_positions( 1 );
			if ( ! empty( $position_array ) ) {
				return $position_array[0];
			}
		}

		// return the object.
		return false;
	}

	/**
	 * Return a shortcode description.
	 *
	 * @return string
	 */
	public function get_shortcode_description(): string {
		// concat the returning text.
		$text = '<code data-copied-label="' . esc_attr__( 'copied', 'personio-integration-light' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'personio-integration-light' ) . '">[personio_integration_' . $this->get_name() . ']</code><br>';

		// get the params.
		$params = $this->get_params();

		// add them if they are filled.
		if ( ! empty( $params ) ) {
			$text .= '<i>' . __( 'Attributes:', 'personio-integration-light' ) . '</i><ul>';
			foreach ( $params as $name => $param ) {
				$text .= '<li><code data-copied-label="' . esc_attr__( 'copied', 'personio-integration-light' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'personio-integration-light' ) . '">' . $name . '</code> ' . $param['label'] . ( $param['required'] ? ' <em>' . __( 'required', 'personio-integration-light' ) . '</em>' : '' ) . '</li>';
			}
			$text .= '</ul>';
			$text .= '<i>' . __( 'Example:', 'personio-integration-light' ) . '</i><br>' . $this->get_shortcode_example();
		} else {
			$text .= '<i>' . __( 'Does not have any attributes.', 'personio-integration-light' ) . '</i>';
		}

		// return the resulting text.
		return '<div>' . $text . '</div>';
	}

	/**
	 * Return a shortcode example.
	 *
	 * @return string
	 */
	private function get_shortcode_example(): string {
		// collect the params here.
		$params = '';

		// get all required params.
		foreach ( $this->get_params() as $name => $param ) {
			// bail if it is not required.
			if ( empty( $param['required'] ) ) {
				continue;
			}

			// bail if no example value is set.
			if ( empty( $param['example_value'] ) ) {
				continue;
			}

			// add this to the list with the configured example value.
			$params .= ' ' . $name . '="' . $param['example_value'] . '"';
		}

		// return resulting example.
		return '<code data-copied-label="' . esc_attr__( 'copied', 'personio-integration-light' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'personio-integration-light' ) . '">[personio_integration_' . $this->get_name() . $params . ']</code>';
	}
}

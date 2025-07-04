<?php
/**
 * File to handle widget extensions for positions.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

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
		do_action( 'personio_integration_light_widget_' . $this->get_name(), $instance );
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
		return false;
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
	 * Return a shortcode description.
	 *
	 * @return string
	 */
	public function get_shortcode_description(): string {
		return '';
	}
}

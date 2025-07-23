<?php
/**
 * File to handle the filter select widget.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Widgets;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use PersonioIntegrationLight\PersonioIntegration\Widget_Base;

/**
 * Object to handle the filter select widget.
 */
class Filter_Select extends Widget_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'widget_filter_select';

	/**
	 * Name if the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationWidgetFilterSelectStatus';

	/**
	 * Name if the setting tab where the setting field is visible.
	 *
	 * @var string
	 */
	protected string $setting_tab = '';

	/**
	 * Path to Block object.
	 *
	 * @var string
	 */
	protected string $gutenberg = '\PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Filter_Select';

	/**
	 * Instance of this object.
	 *
	 * @var ?Filter_List
	 */
	private static ?Filter_Select $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Filter_Select {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Select Filter', 'personio-integration-light' );
	}

	/**
	 * Return whether this extension is enabled (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return 1 === absint( get_option( $this->get_settings_field_name() ) );
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return __('Provides a widget to show filter as select-based dropdown-list for Personio positions.', 'personio-integration-light');
	}

	/**
	 * Return the rendered widget.
	 *
	 * @param array<string,mixed> $attributes Attributes to configure the rendering.
	 *
	 * @return string
	 */
	public function render( array $attributes ): string {
		return Archive::get_instance()->render( $attributes );
	}

	/**
	 * Return the list of params this widget requires.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function get_params(): array {
		// get the possible field values.
		$values = array();
		foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy ) {
			$values[] = $taxonomy['slug'];
		}

		// generate the list.
		$list = ' <code data-copied-label="' . esc_attr__( 'copied', 'wp-personio-integration' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'wp-personio-integration' ) . '">' . implode( '</code>, <code>', $values ) . '</code>';

		// return the list of params for this widget.
		return array(
			'filter' => array(
				'label'         => __( 'Name of chosen template, one of these values:', 'personio-integration-light' ) . $list,
				'example_value' => $values[0],
				'required'      => false,
			),
			'hideFilterTitle' => array(
				'label'         => __( 'Hide filter title', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'hideResetLink' => array(
				'label'         => __( 'Hide reset link', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'space_between' => array(
				'label'         => __( 'Space between filters, number between 0 and 100', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
		);
	}
}

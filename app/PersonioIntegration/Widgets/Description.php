<?php
/**
 * File to handle the description widget.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Widgets;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Widget_Base;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Templates;

/**
 * Object to handle the description widget.
 */
class Description extends Widget_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'widget_description';

	/**
	 * Name if the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationWidgetDescriptionStatus';

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
	protected string $gutenberg = '\PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Description';

	/**
	 * Instance of this object.
	 *
	 * @var ?Description
	 */
	private static ?Description $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Description {
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
		return __( 'Description', 'personio-integration-light' );
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
		return __('Provides a widget to show the description of a single Personio position.', 'personio-integration-light');
	}

	/**
	 * Return the rendered widget.
	 *
	 * @param array<string,mixed> $attributes Attributes to configure the rendering.
	 *
	 * @return string
	 */
	public function render( array $attributes ): string {
		$position = $this->get_position_by_request();
		if ( ( $position instanceof Position && ! $position->is_valid() ) || ! ( $position instanceof Position ) ) {
			return '';
		}

		// set actual language.
		$position->set_lang( Languages::get_instance()->get_current_lang() );

		// collect the attributes.
		$attributes = array_merge( $attributes, array(
			'personioid'              => absint( $position->get_personio_id() ),
			'jobdescription_template' => empty( $attributes['template'] ) ? get_option( 'personioIntegrationTemplateJobDescription' ) : $attributes['template'],
			'templates'               => array( 'content' ),
		));

		// generate styling.
		if( ! empty( $attributes['styles'] ) ) {
			Helper::add_inline_style( $attributes['styles'] );
		}

		// return the output of the template.
		return Templates::get_instance()->get_direct_content_template( $position, $attributes );
	}

	/**
	 * Return the list of params this widget requires.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function get_params(): array {
		// get the possible field values.
		$templates = array();
		foreach ( PersonioPosition::get_instance()->get_jobdescription_templates_via_rest_api() as $template ) {
			$templates[] = $template['value'];
		}

		// generate the template-list.
		$template_list = ' <code data-copied-label="' . esc_attr__( 'copied', 'wp-personio-integration' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'wp-personio-integration' ) . '">' . implode( '</code>, <code>', $templates ) . '</code>';

		// return the list of params for this widget.
		return array(
			'template' => array(
				'label'         => __( 'Name of chosen template, one of these values:', 'wp-personio-integration' ) . $template_list,
				'example_value' => $templates[0],
				'required'      => false,
			),
		);
	}
}

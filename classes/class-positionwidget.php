<?php
/**
 * File for a classic widget for single position.
 *
 * @package personio-integration-light
 */

namespace personioIntegration;

use WP_Widget;

/**
 * Object to provide an old-fashion widget for positions.
 */
class PositionWidget extends WP_Widget {

	use Helper_Widget;

	/**
	 * Initialize this widget.
	 */
	public function __construct() {
		$widget_options = array(
			'classname'   => 'personioIntegration\PositionWidget',
			'description' => __( 'Provides a Widget to show a single position provided by Personio.', 'personio-integration-light' ),
		);
		parent::__construct(
			'PersonioPositionWidget',
			__( 'Personio Position', 'personio-integration-light' ),
			$widget_options
		);
	}

	/**
	 * Get the fields for this widget.
	 *
	 * @return array[]
	 */
	private function getFields(): array {
		// get the actual positions.
		$positions_obj = Positions::get_instance();
		$positions     = array();
		foreach ( $positions_obj->get_positions( 0 ) as $position ) {
			$positions[ $position->ID ] = $position->getTitle();
		}

		return array(
			'postId'              => array(
				'type'   => 'select',
				'title'  => __( 'Select position', 'personio-integration-light' ),
				'std'    => '',
				'values' => $positions,
			),
			'showTitle'           => array(
				'type'   => 'select',
				'title'  => __( 'Show title', 'personio-integration-light' ),
				'std'    => 'yes',
				'values' => array(
					'yes' => esc_html__( 'Show', 'personio-integration-light' ),
					'no'  => esc_html__( 'Hide', 'personio-integration-light' ),
				),
			),
			'linkTitle'           => array(
				'type'   => 'select',
				'title'  => __( 'link title', 'personio-integration-light' ),
				'std'    => 'no',
				'values' => array(
					'yes' => esc_html__( 'Show', 'personio-integration-light' ),
					'no'  => esc_html__( 'Hide', 'personio-integration-light' ),
				),
			),
			'showExcerpt'         => array(
				'type'   => 'select',
				'title'  => __( 'Show detail', 'personio-integration-light' ),
				'std'    => 'no',
				'values' => array(
					'yes' => esc_html__( 'Show', 'personio-integration-light' ),
					'no'  => esc_html__( 'Hide', 'personio-integration-light' ),
				),
			),
			'excerptTemplates'    => array(
				'type'     => 'select',
				'title'    => __( 'Choose detail components', 'personio-integration-light' ),
				'multiple' => true,
				'std'      => array( 'recruitingCategory', 'schedule', 'office' ),
				'values'   => personio_integration_admin_categories_labels(),
			),
			'showContent'         => array(
				'type'   => 'select',
				'title'  => __( 'Show content', 'personio-integration-light' ),
				'std'    => 'yes',
				'values' => array(
					'yes' => esc_html__( 'Show', 'personio-integration-light' ),
					'no'  => esc_html__( 'Hide', 'personio-integration-light' ),
				),
			),
			'showApplicationForm' => array(
				'type'   => 'select',
				'title'  => __( 'Show application link', 'personio-integration-light' ),
				'std'    => 'yes',
				'values' => array(
					'yes' => esc_html__( 'Show', 'personio-integration-light' ),
					'no'  => esc_html__( 'Hide', 'personio-integration-light' ),
				),
			),
		);
	}

	/**
	 * Add entry-formular with settings for the widget.
	 *
	 * @param array $instance The instance of the widget.
	 *
	 * @return void
	 * @noinspection PhpMissingReturnTypeInspection*/
	public function form( $instance ) {
		$this->create_widget_field_output( $this->getFields(), $instance );
	}

	/**
	 * Save updated settings from the formular.
	 *
	 * @param array $new_instance The new instance.
	 * @param array $old_instance The old instance.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ): array {
		return $this->secure_widget_fields( $this->getFields(), $new_instance, $old_instance );
	}

	/**
	 * Output of the widget in frontend.
	 *
	 * @param array $args List of arguments.
	 * @param array $settings List of settings.
	 *
	 * @return void
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 * @noinspection DuplicatedCode
	 * @noinspection PhpMissingReturnTypeInspection
	 */
	public function widget( $args, $settings ) {
		// collect the configured templates.
		$templates = '';
		if ( 'yes' === $settings['showTitle'] ) {
			$templates .= ( strlen( $templates ) > 0 ? ',' : '' ) . 'title';
		}
		if ( 'yes' === $settings['showExcerpt'] ) {
			$templates .= ( strlen( $templates ) > 0 ? ',' : '' ) . 'excerpt';
		}
		if ( 'yes' === $settings['showContent'] ) {
			$templates .= ( strlen( $templates ) > 0 ? ',' : '' ) . 'content';
		}
		if ( 'yes' === $settings['showApplicationForm'] ) {
			$templates .= ( strlen( $templates ) > 0 ? ',' : '' ) . 'formular';
		}

		// get the excerpt-templates.
		$excerpt_templates = '';
		if ( ! empty( $settings['excerptTemplates'] ) ) {
			$excerpt_templates = implode( ',', $settings['excerptTemplates'] );
		}

		// link title.
		$do_not_link = true;
		if ( 'yes' === $settings['linkTitle'] ) {
			$do_not_link = false;
		}

		$attribute_defaults = array(
			'id'        => $settings['postId'],
			'templates' => $templates,
			'excerpt'   => $excerpt_templates,
			'donotlink' => $do_not_link,
		);

		// add wrapper from template around widget-content.
		echo $args['before_widget'];

		// get the output.
		echo personio_integration_position_shortcode( $attribute_defaults );

		// add wrapper from template around widget-content.
		echo $args['after_widget'];
	}
}

<?php
/**
 * File for a classic widget for multiple positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Widgets;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use PersonioIntegrationLight\Plugin\Templates;
use WP_Widget;

/**
 * Object to provide an old-fashion widget for positions.
 */
class Positions extends WP_Widget {

	use Helper;

	/**
	 * Initialize this widget.
	 */
	public function __construct() {
		$widget_options = array(
			'classname'   => 'personioIntegration\PositionsWidget',
			'description' => __( 'Provides a Widget to show a list of positions provided by Personio.', 'personio-integration-light' ),
		);
		parent::__construct(
			'PersonioPositionsWidget',
			__( 'Personio Positions', 'personio-integration-light' ),
			$widget_options
		);
	}

	/**
	 * Get fields for this widget.
	 *
	 * @return array[]
	 */
	private function getFields(): array {
		return array(
			'template'            => array(
				'type'   => 'select',
				'title'  => __( 'Choose template', 'personio-integration-light' ),
				'std'    => get_option( 'personioIntegrationTemplateContentListingTemplate' ),
				'values' => Templates::get_instance()->get_archive_templates(),
			),
			'limit'               => array(
				'type'    => 'number',
				'title'   => __( 'Amount', 'personio-integration-light' ),
				'default' => 0,
			),
			'sort'                => array(
				'type'   => 'select',
				'title'  => __( 'Sort Direction', 'personio-integration-light' ),
				'std'    => 'asc',
				'values' => array(
					'asc'  => esc_html__( 'ascending', 'personio-integration-light' ),
					'desc' => esc_html__( 'descending', 'personio-integration-light' ),
				),
			),
			'sortby'              => array(
				'type'   => 'select',
				'title'  => __( 'Sort by', 'personio-integration-light' ),
				'std'    => 'title',
				'values' => array(
					'title' => esc_html__( 'title', 'personio-integration-light' ),
					'date'  => esc_html__( 'date', 'personio-integration-light' ),
				),
			),
			'groupby'             => array(
				'type'   => 'select',
				'title'  => __( 'Group by', 'personio-integration-light' ),
				'std'    => 'title',
				'values' => array_merge( array( '' => __( 'Ungrouped', 'personio-integration-light' ) ), Taxonomies::get_instance()->get_taxonomy_labels_for_settings() ),
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
				'std'    => 'yes',
				'values' => array(
					'yes' => esc_html__( 'Show', 'personio-integration-light' ),
					'no'  => esc_html__( 'Hide', 'personio-integration-light' ),
				),
			),
			'showExcerpt'         => array(
				'type'   => 'select',
				'title'  => __( 'Show detail', 'personio-integration-light' ),
				'std'    => 'yes',
				'values' => array(
					'yes' => esc_html__( 'Show', 'personio-integration-light' ),
					'no'  => esc_html__( 'Hide', 'personio-integration-light' ),
				),
			),
			'excerptTemplates'    => array(
				'type'     => 'select',
				'title'    => __( 'Choose detail components', 'personio-integration-light' ),
				'multiple' => true,
				'std'      => get_option( 'personioIntegrationTemplateExcerptDefaults' ),
				'values'   => Taxonomies::get_instance()->get_taxonomy_labels_for_settings(),
			),
			'showContent'         => array(
				'type'   => 'select',
				'title'  => __( 'Show content', 'personio-integration-light' ),
				'std'    => 'no',
				'values' => array(
					'yes' => esc_html__( 'Show', 'personio-integration-light' ),
					'no'  => esc_html__( 'Hide', 'personio-integration-light' ),
				),
			),
			'content_template'    => array(
				'type'   => 'select',
				'title'  => __( 'Choose content template', 'personio-integration-light' ),
				'std'    => get_option( 'personioIntegrationTemplateListingContentTemplate' ),
				'values' => Templates::get_instance()->get_jobdescription_templates(),
			),
			'showApplicationForm' => array(
				'type'   => 'select',
				'title'  => __( 'Show application link', 'personio-integration-light' ),
				'std'    => 'no',
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
	 * @param array $instance The instance.
	 *
	 * @return void
	 * @noinspection PhpMissingReturnTypeInspection
	 */
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
		if ( is_array( $settings['excerptTemplates'] ) ) {
			$excerpt_templates = implode( ',', $settings['excerptTemplates'] );
		}

		// link title.
		$do_not_link = true;
		if ( 'yes' === $settings['linkTitle'] ) {
			$do_not_link = false;
		}

		// limit.
		$limit = 0;
		if ( ! empty( $settings['limit'] ) ) {
			$limit = $settings['limit'];
		}

		$attribute_defaults = array(
			'showfilter'              => false,
			'templates'               => $templates,
			'excerpt'                 => $excerpt_templates,
			'donotlink'               => $do_not_link,
			'sort'                    => $settings['sort'],
			'sortby'                  => $settings['sortby'],
			'groupby'                 => $settings['groupby'],
			'limit'                   => $limit,
			'jobdescription_template' => $settings['content_template'],
		);

		// add wrapper from template around widget-content.
		echo wp_kses_post( $args['before_widget'] );

		// get the output.
		echo wp_kses_post( PersonioPosition::get_instance()->shortcode_archive( $attribute_defaults ) );

		// add wrapper from template around widget-content.
		echo wp_kses_post( $args['after_widget'] );
	}
}

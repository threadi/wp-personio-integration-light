<?php

namespace personioIntegration;

use WP_Widget;

/**
 * Object to provide an old-fashion widget for positions.
 */
class PositionsWidget extends WP_Widget {

    use helper_widget;

    /**
     * Initialize this widget.
     */
    public function __construct() {
        $widget_options = array (
            'classname' => 'personioIntegration\PositionsWidget',
            'description' => __('Provides a Widget to show a list of positions provided by Personio.', 'personio-integration-light')
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
    private function getFields(): array
    {
        return array(
	        'template'     => array(
		        'type'          => 'select',
		        'title'         => __( 'Choose template', 'personio-integration-light' ),
		        'std'       => 'title',
		        'values'       => personio_integration_archive_templates()
	        ),
            'limit'     => array(
                'type'          => 'number',
                'title'         => __( 'amount', 'personio-integration-light' ),
                'default'       => 0
            ),
            'sort'     => array(
                'type'          => 'select',
                'title'         => __( 'Sort Direction', 'personio-integration-light' ),
                'std'       => 'asc',
                'values'       => [
                    'asc' => esc_html__( 'ascending', 'personio-integration-light' ),
                    'desc' => esc_html__( 'descending', 'personio-integration-light' )
                ]
            ),
            'sortby'     => array(
                'type'          => 'select',
                'title'         => __( 'Sort by', 'personio-integration-light' ),
                'std'       => 'title',
                'values'       => [
                    'title' => esc_html__( 'title', 'personio-integration-light' ),
                    'date' => esc_html__( 'date', 'personio-integration-light' )
                ]
            ),
            'groupby'     => array(
                'type'          => 'select',
                'title'         => __( 'Group by', 'personio-integration-light' ),
                'std'       => 'title',
                'values'       => array_merge(['' => __('ungrouped', 'personio-integration-light')], personio_integration_admin_categories_labels())
            ),
            'showTitle'     => array(
                'type'          => 'select',
                'title'         => __( 'Show title', 'personio-integration-light' ),
                'std'       => 'yes',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'personio-integration-light' ),
                    'no' => esc_html__( 'Hide', 'personio-integration-light' )
                ]
            ),
            'linkTitle'     => array(
                'type'          => 'select',
                'title'         => __( 'link title', 'personio-integration-light' ),
                'std'       => 'yes',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'personio-integration-light' ),
                    'no' => esc_html__( 'Hide', 'personio-integration-light' )
                ]
            ),
            'showExcerpt'     => array(
                'type'          => 'select',
                'title'         => __( 'Show detail', 'personio-integration-light' ),
                'std'       => 'yes',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'personio-integration-light' ),
                    'no' => esc_html__( 'Hide', 'personio-integration-light' )
                ]
            ),
            'excerptTemplates'     => array(
                'type'          => 'select',
                'title'         => __( 'Choose detail components', 'personio-integration-light' ),
                'multiple'  => true,
                'std'       => ['recruitingCategory','schedule','office'],
                'values'       => personio_integration_admin_categories_labels()
            ),
            'showContent'     => array(
                'type'          => 'select',
                'title'         => __( 'Show content', 'personio-integration-light' ),
                'std'       => 'no',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'personio-integration-light' ),
                    'no' => esc_html__( 'Hide', 'personio-integration-light' )
                ]
            ),
            'showApplicationForm'     => array(
                'type'          => 'select',
                'title'         => __( 'Show application link', 'personio-integration-light' ),
                'std'       => 'no',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'personio-integration-light' ),
                    'no' => esc_html__( 'Hide', 'personio-integration-light' )
                ]
            )
        );
    }

    /**
     * Add entry-formular with settings for the widget.
     *
     * @param $instance
     *
     * @return void
     * @noinspection PhpMissingReturnTypeInspection
     */
    function form( $instance ) {
        $this->createWidgetFieldOutput( $this->getFields(), $instance );
    }

    /**
     * Save updated settings from the formular.
     *
     * @param $new_instance
     * @param $old_instance
     * @return array
     */
    function update( $new_instance, $old_instance ): array
    {
        return $this->secureWidgetFields( $this->getFields(), $new_instance, $old_instance );
    }

    /**
     * Output of the widget in frontend.
     *
     * @param $args
     * @param $settings
     *
     * @return void
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     * @noinspection DuplicatedCode
     * @noinspection PhpMissingReturnTypeInspection
     */
    function widget( $args, $settings ) {
        // collect the configured templates
        $templates = '';
        if( $settings["showTitle"] == "yes" ) {
            $templates .= (strlen($templates) > 0 ? ',': '').'title';
        }
        if( $settings["showExcerpt"] == "yes" ) {
            $templates .= (strlen($templates) > 0 ? ',': '').'excerpt';
        }
        if( $settings["showContent"] == "yes" ) {
            $templates .= (strlen($templates) > 0 ? ',': '').'content';
        }
        if( $settings["showApplicationForm"] == "yes" ) {
            $templates .= (strlen($templates) > 0 ? ',': '').'formular';
        }

        // get the excerpt-templates
        $excerptTemplates = '';
        if( is_array($settings["excerptTemplates"]) ) {
            $excerptTemplates = implode(",", $settings["excerptTemplates"]);
        }

        // link title?
        $doNotLink = true;
        if( $settings["linkTitle"] == "yes" ) {
            $doNotLink = false;
        }

        // limit?
        $limit = 0;
        if( !empty($settings["limit"]) ) {
            $limit = $settings["limit"];
        }

        $attribute_defaults = [
            'templates' => $templates,
            'excerpt' => $excerptTemplates,
            'donotlink' => $doNotLink,
            'sort' => $settings["sort"],
            'sortby' => $settings["sortby"],
            'groupby' => $settings["groupby"],
            'limit' => $limit
        ];

        // add wrapper from template around widget-content
        echo $args['before_widget'];

        // get the output
        echo personio_integration_positions_shortcode( $attribute_defaults );

        // add wrapper from template around widget-content
        echo $args['after_widget'];
    }

}

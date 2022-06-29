<?php

namespace personioIntegration;

use WP_Widget;

/**
 * Object to provide an old-fashion widget for positions.
 */
class PositionsWidget extends WP_Widget {

    use helper;

    /**
     * Initialize this widget.
     */
    public function __construct() {
        $widget_options = array (
            'classname' => 'personioIntegration\PositionsWidget',
            'description' => __('Provides a Widget to show a list of positions provided by Personio.', 'wp-personio-integration')
        );
        parent::__construct(
                'PersonioPositionsWidget',
                __( 'Personio Positions', 'wp-personio-integration' ),
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
        return [
            'limit'     => array(
                'type'          => 'number',
                'title'         => __( 'amount', 'wp-personio-integration' ),
                'default'       => 0
            ),
            'sort'     => array(
                'type'          => 'select',
                'title'         => __( 'Sort Direction', 'wp-personio-integration' ),
                'std'       => 'asc',
                'values'       => [
                    'asc' => esc_html__( 'ascending', 'wp-personio-integration' ),
                    'desc' => esc_html__( 'descending', 'wp-personio-integration' )
                ]
            ),
            'sortby'     => array(
                'type'          => 'select',
                'title'         => __( 'Sort by', 'wp-personio-integration' ),
                'std'       => 'title',
                'values'       => [
                    'title' => esc_html__( 'title', 'wp-personio-integration' ),
                    'date' => esc_html__( 'date', 'wp-personio-integration' )
                ]
            ),
            'showTitle'     => array(
                'type'          => 'select',
                'title'         => __( 'Show title', 'wp-personio-integration' ),
                'std'       => 'yes',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'wp-personio-integration' ),
                    'no' => esc_html__( 'Hide', 'wp-personio-integration' )
                ]
            ),
            'linkTitle'     => array(
                'type'          => 'select',
                'title'         => __( 'link title', 'wp-personio-integration' ),
                'std'       => 'yes',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'wp-personio-integration' ),
                    'no' => esc_html__( 'Hide', 'wp-personio-integration' )
                ]
            ),
            'showExcerpt'     => array(
                'type'          => 'select',
                'title'         => __( 'Show excerpt', 'wp-personio-integration' ),
                'std'       => 'yes',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'wp-personio-integration' ),
                    'no' => esc_html__( 'Hide', 'wp-personio-integration' )
                ]
            ),
            'excerptTemplates'     => array(
                'type'          => 'select',
                'title'         => __( 'Choose excerpt components', 'wp-personio-integration' ),
                'multiple'  => true,
                'std'       => ['recruitingCategory','schedule','office'],
                'values'       => personio_integration_admin_categories_labels()
            ),
            'showContent'     => array(
                'type'          => 'select',
                'title'         => __( 'Show content', 'wp-personio-integration' ),
                'std'       => 'no',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'wp-personio-integration' ),
                    'no' => esc_html__( 'Hide', 'wp-personio-integration' )
                ]
            ),
            'showApplicationForm'     => array(
                'type'          => 'select',
                'title'         => __( 'Show application link', 'wp-personio-integration' ),
                'std'       => 'no',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'wp-personio-integration' ),
                    'no' => esc_html__( 'Hide', 'wp-personio-integration' )
                ]
            )
        ];
    }

    /**
     * Add entry-formular with settings for the widget.
     *
     * @param $instance
     * @return void
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
     * @return void
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
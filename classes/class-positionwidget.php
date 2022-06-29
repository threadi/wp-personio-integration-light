<?php

namespace personioIntegration;

use WP_Widget;

/**
 * Object to provide an old-fashion widget for positions.
 */
class PositionWidget extends WP_Widget {

    use helper;

    /**
     * Initialize this widget.
     */
    public function __construct() {
        $widget_options = array (
            'classname' => 'personioIntegration\PositionWidget',
            'description' => __('Provides a Widget to show a single position provided by Personio.', 'wp-personio-integration')
        );
        parent::__construct(
                'PersonioPositionWidget',
                __( 'Personio Position', 'wp-personio-integration' ),
                $widget_options
        );
    }

    /**
     * Get the fields for this widget.
     *
     * @return array[]
     */
    private function getFields(): array
    {
        // get the actual positions
        $positionsObj = new Positions();
        $positionsList = $positionsObj->getPositions(0);
        $positions = [];
        foreach ( $positionsList as $position ) {
            $positions[$position->ID] = $position->getTitle();
        }

        return [
            'postId'     => array(
                'type'          => 'select',
                'title'         => __( 'Select position', 'wp-personio-integration' ),
                'std'           => '',
                'values'       => $positions
            ),
            'showTitle'     => array(
                'type'          => 'select',
                'title'         => __( 'Show title', 'wp-personio-integration' ),
                'std'           => 'yes',
                'values'        => [
                    'yes' => esc_html__( 'Show', 'wp-personio-integration' ),
                    'no' => esc_html__( 'Hide', 'wp-personio-integration' )
                ]
            ),
            'linkTitle'     => array(
                'type'          => 'select',
                'title'         => __( 'link title', 'wp-personio-integration' ),
                'std'           => 'no',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'wp-personio-integration' ),
                    'no' => esc_html__( 'Hide', 'wp-personio-integration' )
                ]
            ),
            'showExcerpt'     => array(
                'type'          => 'select',
                'title'         => __( 'Show excerpt', 'wp-personio-integration' ),
                'std'           => 'no',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'wp-personio-integration' ),
                    'no' => esc_html__( 'Hide', 'wp-personio-integration' )
                ]
            ),
            'excerptTemplates'     => array(
                'type'          => 'select',
                'title'         => __( 'Choose excerpt components', 'wp-personio-integration' ),
                'multiple'      => true,
                'std'           => ['recruitingCategory','schedule','office'],
                'values'       => personio_integration_admin_categories_labels()
            ),
            'showContent'     => array(
                'type'          => 'select',
                'title'         => __( 'Show content', 'wp-personio-integration' ),
                'std'       => 'yes',
                'values'       => [
                    'yes' => esc_html__( 'Show', 'wp-personio-integration' ),
                    'no' => esc_html__( 'Hide', 'wp-personio-integration' )
                ]
            ),
            'showApplicationForm'     => array(
                'type'          => 'select',
                'title'         => __( 'Show application link', 'wp-personio-integration' ),
                'std'       => 'yes',
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
        if( !empty($settings["excerptTemplates"]) ) {
            $excerptTemplates = implode(",", $settings["excerptTemplates"]);
        }

        // link title?
        $doNotLink = true;
        if( $settings["linkTitle"] == "yes" ) {
            $doNotLink = false;
        }

        $attribute_defaults = [
            'id' => $settings['postId'],
            'templates' => $templates,
            'excerpt' => $excerptTemplates,
            'donotlink' => $doNotLink
        ];

        // add wrapper from template around widget-content
        echo $args['before_widget'];

        // get the output
        echo personio_integration_position_shortcode( $attribute_defaults );

        // add wrapper from template around widget-content
        echo $args['after_widget'];
    }

}
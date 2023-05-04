<?php

namespace personioIntegration\gutenberg;

use WP_Block_Template;

/**
 * Object to handle all Gutenberg-templates of this plugin.
 */
class templates {
    private static ?templates $instance = null;

    /**
     * Constructor, not used as this a Singleton object.
     */
    private function __construct() {}

    /**
     * Prevent cloning of this object.
     *
     * @return void
     */
    private function __clone() { }

    /**
     * Return the instance of this Singleton object.
     */
    public static function get_instance(): templates
    {
        if( !static::$instance instanceof static ) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Initialize the block template via necessary hooks.
     *
     * @return void
     */
    public function init(): void
    {
        add_filter( 'get_block_templates', [$this, 'add_block_templates'], 10, 3 );
        add_filter( 'pre_get_block_file_template', [$this, 'get_block_file_template'], 10, 3 );
        add_action( 'switch_theme', [$this, 'update_db_templates'], 10, 0 );
    }

    /**
     * Add our own Block templates.
     *
     * @source BlockTemplatesController.php from WooCommerce
     *
     * @param $result
     * @param $query
     * @param $template_type
     * @return array
     * @noinspection PhpIssetCanBeReplacedWithCoalesceInspection
     */
    public function add_block_templates( $result, $query, $template_type ): array
    {
        if( !$this->theme_support_block_templates() ) {
            return $result;
        }

        // get post type
        $post_type      = isset( $query['post_type'] ) ? $query['post_type'] : '';
        $slugs          = isset( $query['slug__in'] ) ? $query['slug__in'] : array();

        // get our templates from filesystem
        $templates = $this->get_block_templates( $slugs, $template_type );
        foreach( $templates as $template ) {
            $block_template = $template->get_object();
            // hide template if post-types doesnt match
            if ( $post_type &&
                isset( $block_template->post_types ) &&
                ! in_array( $post_type, $block_template->post_types, true )
            ) {
                continue;
            }

            $result[]     = $template->get_block_template();
        }

        // return resulting list of templates
        return $result;
    }

    /**
     * Check if active theme supports block templates.
     *
     * @return bool
     * @noinspection PhpUndefinedFunctionInspection
     */
    private function theme_support_block_templates(): bool
    {
        if (
            ! $this->current_theme_is_fse_theme() &&
            ( ! function_exists( 'gutenberg_supports_block_templates' ) || !gutenberg_supports_block_templates() )
        ) {
            return false;
        }

        return true;
    }

    /**
     * Check if the current theme is a block theme.
     *
     * @return bool
     * @noinspection PhpCastIsUnnecessaryInspection
     * @noinspection PhpUndefinedFunctionInspection
     */
    private function current_theme_is_fse_theme(): bool
    {
        if ( function_exists( 'wp_is_block_theme' ) ) {
            return (bool) wp_is_block_theme();
        }
        if ( function_exists( 'gutenberg_is_fse_theme' ) ) {
            return (bool) gutenberg_is_fse_theme();
        }
        return false;
    }

    /**
     * Get the supported block templates.
     *
     * @param $slugs
     * @param $template_type
     * @return array
     */
    private function get_block_templates( $slugs, $template_type ): array
    {
        // initialize return array
        $templates = [];

        // loop through the block templates and add them as template-objects to the array
        foreach( $this->get_templates() as $template_slug => $settings ) {
            // ignore template if it does not match a requested slug (if given)
            if( !empty($slugs) && !in_array( $template_slug, $slugs ) ) {
                continue;
            }

            // create template-object
            $template_obj = new template();
            $template_obj->set_type( $template_type );
            $template_obj->set_slug( $template_slug );
            $template_obj->set_source( $settings['source'] );
            $template_obj->set_title( $settings['title'] );
            $template_obj->set_description( $settings['description'] );
            if( $template_obj->is_valid() ) {
                $templates[$template_slug] = $template_obj;
            }
        }

        return array_merge($templates, $this->get_templates_from_db( $slugs, $template_type ));
    }

    /**
     * Get block template as object for save-request.
     *
     * @param $template
     * @param $id
     * @param $template_type
     * @return null|WP_Block_Template
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function get_block_file_template( $template, $id, $template_type )
    {
        $template_name_parts = explode( '//', $id );

        if ( count( $template_name_parts ) < 2 ) {
            return $template;
        }

        list( $template_id, $template_slug ) = $template_name_parts;

        // if it is not our own template
        if( $template_id !== WP_PERSONIO_GUTENBERG_PARENT_ID ) {
            return $template;
        }

        // get list of our own block templates
        $templates = $this->get_templates();

        // get the settings for the requested template
        $settings = $templates[$template_slug];

        if( empty($settings) ) {
            return $template;
        }

        // create the template-object
        $template_obj = new template();
        $template_obj->set_template( $template_slug );
        $template_obj->set_type( $template_type );
        $template_obj->set_slug( $template_slug );
        $template_obj->set_source( $settings['source'] );
        $template_obj->set_title( $settings['title'] );
        $template_obj->set_description( $settings['description'] );

        // return the resulting object if it is valid
        if( $template_obj->is_valid() ) {
            return $template_obj->get_block_template();
        }

        // otherwise return the initial value
        return $template;
    }

    /**
     * Return list of available block templates.
     *
     * @return array
     */
    private function get_templates(): array
    {
        // define the list
        $templates = [
            'single-'.WP_PERSONIO_INTEGRATION_CPT => [
                'title' => __('Single Position', 'wp-personio-integration'),
                'description' => __('Displays a single position.', 'wp-personio-integration'),
                'source' => 'plugin'
            ],
            'archive-'.WP_PERSONIO_INTEGRATION_CPT => [
                'title' => __('Archive Positions', 'wp-personio-integration'),
                'description' => __('Displays your positions.', 'wp-personio-integration'),
                'source' => 'plugin'
            ]
        ];

        // return the list
        return apply_filters('personio_integration_block_templates', $templates);
    }

    /**
     * Get templates from DB to override the template from files.
     *
     * @param $slugs
     * @param $template_type
     * @return array
     */
    public function get_templates_from_db( $slugs, $template_type ): array
    {
        $query = array(
            'post_type'      => $template_type,
            'posts_per_page' => -1,
            'no_found_rows'  => true,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'wp_theme',
                    'field'    => 'name',
                    'terms'    => array( WP_PERSONIO_GUTENBERG_PARENT_ID, get_stylesheet() ),
                ),
            ),
        );

        if ( is_array( $slugs ) && count( $slugs ) > 0 ) {
            $query['post_name__in'] = $slugs;
        }

        $check_query         = new \WP_Query( $query );
        $saved_woo_templates = $check_query->posts;

        $templates = [];
        foreach( $saved_woo_templates as $post ) {
            $template_obj = new template();
            $template_obj->set_post_id( $post->ID );
            $template_obj->set_template( $post->post_name );
            $template_obj->set_type( $post->post_type );
            $template_obj->set_slug( $post->post_name );
            $template_obj->set_source('custom');
            $template_obj->set_title( $post->post_title );
            $template_obj->set_description( $post->post_excerpt );
            $template_obj->set_content( $post->post_content );
            $templates[$post->post_name] = $template_obj;
        }

        // return list of templates
        return $templates;
    }

    /**
     * Update the db-based themes if theme has been switched to another block-theme.
     *
     * @return void
     */
    public function update_db_templates(): void
    {
        if( !$this->current_theme_is_fse_theme() ) {
            return;
        }

        // loop through the templates an update their template-parts in content to the new theme
        foreach( $this->get_templates_from_db([], 'wp_template') as $template ) {
            $updated_content = $template->update_theme_attribute_in_content($template->get_content());
            $query = [
                'ID' => $template->get_post_id(),
                'post_content' => $updated_content
            ];
            wp_update_post($query);
        }

    }

}

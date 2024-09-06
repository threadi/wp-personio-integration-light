<?php
/**
 * File to handle multiple Gutenberg-templates.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder\Gutenberg;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use WP_Block_Template;
use WP_Query;

/**
 * Object to handle all Gutenberg-templates of this plugin.
 */
class Templates {
	/**
	 * The instance of this object.
	 *
	 * @var Templates|null
	 */
	private static ?Templates $instance = null;

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
	public static function get_instance(): Templates {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize the block template via necessary hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter( 'get_block_templates', array( $this, 'add_block_templates' ), 10, 3 );
		add_filter( 'pre_get_block_file_template', array( $this, 'get_block_file_template' ), 10, 3 );
		add_action( 'switch_theme', array( $this, 'update_db_templates' ), 10, 0 );
	}

	/**
	 * Add our own Block templates.
	 *
	 * @source BlockTemplatesController.php from WooCommerce
	 *
	 * @param array  $template_list Resulting list of block templates.
	 * @param array  $query The query.
	 * @param string $template_type The template type.
	 *
	 * @return array
	 * @noinspection PhpIssetCanBeReplacedWithCoalesceInspection
	 **/
	public function add_block_templates( array $template_list, array $query, string $template_type ): array {
		// get post type.
		$post_type = isset( $query['post_type'] ) ? $query['post_type'] : '';
		$slugs     = isset( $query['slug__in'] ) ? $query['slug__in'] : array();

		// get our own templates.
		$templates = $this->get_block_templates( $slugs, $template_type );

		// loop through the templates and add them to the resulting list if they are valid.
		foreach ( $templates as $template ) {
			$block_template = $template->get_object();
			// hide template if post-types doesnt match.
			if ( $post_type &&
				isset( $block_template->post_types ) &&
				! in_array( $post_type, $block_template->post_types, true )
			) {
				continue;
			}

			$template_list[] = $template->get_block_template();
		}

		// return resulting list of templates.
		return $template_list;
	}

	/**
	 * Get the supported block templates from file system (plugin-source) AND database (custom templates from user).
	 *
	 * @param array  $slugs List of slugs.
	 * @param string $template_type The template.
	 *
	 * @return array
	 */
	private function get_block_templates( array $slugs, string $template_type ): array {
		// initialize return array.
		$templates = array();

		// loop through the block templates and add them as template-objects to the array.
		foreach ( $this->get_templates() as $template_slug => $settings ) {
			// ignore template if it does not match a requested slug (if given).
			if ( ! empty( $slugs ) && ! in_array( $template_slug, $slugs, true ) ) {
				continue;
			}

			// create template-object.
			$template_obj = new Template();
			$template_obj->set_type( $template_type );
			$template_obj->set_slug( $template_slug );
			$template_obj->set_source( $settings['source'] );
			$template_obj->set_title( $settings['title'] );
			$template_obj->set_description( $settings['description'] );
			if ( $template_obj->is_valid() ) {
				$templates[ $template_slug ] = $template_obj;
			}
		}

		// return merged list of templates from filesystem AND database.
		return array_merge( $templates, $this->get_templates_from_db( $slugs, $template_type ) );
	}

	/**
	 * Get block template as object for save-request.
	 *
	 * @param null|WP_Block_Template $template The template.
	 * @param string                 $id The id of the template.
	 * @param string                 $template_type The template type.
	 * @return array|null
	 */
	public function get_block_file_template( null|WP_Block_Template $template, string $id, string $template_type ): null|WP_Block_Template {
		$template_name_parts = explode( '//', $id );

		if ( count( $template_name_parts ) < 2 ) {
			return $template;
		}

		list( $template_id, $template_slug ) = $template_name_parts;

		// if it is not our own template.
		if ( WP_PERSONIO_GUTENBERG_PARENT_ID !== $template_id ) {
			return $template;
		}

		// get list of our own block templates.
		$templates = $this->get_templates();

		// get the settings for the requested template.
		if ( ! empty( $templates[ $template_slug ] ) ) {
			$settings = $templates[ $template_slug ];
		} else {
			return $template;
		}

		// create the template-object.
		$template_obj = new Template();
		$template_obj->set_template( $template_slug );
		$template_obj->set_type( $template_type );
		$template_obj->set_slug( $template_slug );
		$template_obj->set_source( $settings['source'] );
		$template_obj->set_title( $settings['title'] );
		$template_obj->set_description( $settings['description'] );

		// return the resulting object if it is valid.
		if ( $template_obj->is_valid() ) {
			return $template_obj->get_block_template();
		}

		// otherwise return the initial value.
		return $template;
	}

	/**
	 * Return list of available block templates.
	 *
	 * @return array
	 */
	private function get_templates(): array {
		// define the list.
		$templates = array(
			'single-' . PersonioPosition::get_instance()->get_name()  => array(
				'title'       => __( 'Single Position', 'personio-integration-light' ),
				'description' => __( 'Displays a single position.', 'personio-integration-light' ),
				'source'      => 'plugin',
			),
			'archive-' . PersonioPosition::get_instance()->get_name() => array(
				'title'       => __( 'Archive Positions', 'personio-integration-light' ),
				'description' => __( 'Displays your positions.', 'personio-integration-light' ),
				'source'      => 'plugin',
			),
		);

		/**
		 * Filter the list of available block templates.
		 *
		 * @since 2.2.0 Available since 2.2.0.
		 *
		 * @param array $templates The list of templates.
		 */
		return apply_filters( 'personio_integration_block_templates', $templates );
	}

	/**
	 * Get templates from DB to override the template from files.
	 *
	 * @param array  $slugs The slugs.
	 * @param string $template_type The template type.
	 * @return array
	 */
	public function get_templates_from_db( array $slugs, string $template_type ): array {
		// define query for custom template in db.
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

		if ( count( $slugs ) > 0 ) {
			$query['post_name__in'] = $slugs;
		}

		$check_query      = new WP_Query( $query );
		$custom_templates = $check_query->posts;

		$templates = array();
		foreach ( $custom_templates as $post ) {
			$template_obj = new Template();
			$template_obj->set_post_id( $post->ID );
			$template_obj->set_template( $post->post_name );
			$template_obj->set_type( $post->post_type );
			$template_obj->set_slug( $post->post_name );
			$template_obj->set_source( 'custom' );
			$template_obj->set_title( $post->post_title );
			$template_obj->set_description( $post->post_excerpt );
			$template_obj->set_content( $post->post_content );
			$templates[ $post->post_name ] = $template_obj;
		}

		// return list of templates.
		return $templates;
	}

	/**
	 * Update the db-based templates if theme has been switched to another block-theme.
	 *
	 * E.g. necessary to adjust header- and footer-templates.
	 *
	 * @return void
	 */
	public function update_db_templates(): void {
		// loop through the templates and update their template-parts in content to the new theme.
		foreach ( $this->get_templates_from_db( array(), 'wp_template' ) as $template ) {
			$updated_content = $template->update_theme_attribute_in_content( $template->get_content() );
			$query           = array(
				'ID'           => $template->get_post_id(),
				'post_content' => $updated_content,
			);
			wp_update_post( $query );
		}
	}

	/**
	 * Remove our templates from DB.
	 *
	 * @return void
	 */
	public function remove_db_templates(): void {
		// loop through the templates and update their template-parts in content to the new theme.
		foreach ( $this->get_templates_from_db( array(), 'wp_template' ) as $template ) {
			wp_delete_post( $template->get_post_id() );
		}
	}
}

<?php
/**
 * File to handle the post-type PersonioPostion.
 *
 * @package personio-integration-light
 */

namespace App\PersonioIntegration\PostTypes;

use App\PersonioIntegration\Helper;
use personioIntegration\Positions;
use Post_Type;
use WP_Post;
use WP_REST_Response;

/**
 * Object of this cpt.
 */
class PersonioPosition extends Post_Type {
	/**
	 * Set name of this cpt.
	 *
	 * @var string
	 */
	protected string $name = WP_PERSONIO_INTEGRATION_CPT;

	/**
	 * Instance of this object.
	 *
	 * @var ?PersonioPosition
	 */
	private static ?PersonioPosition $instance = null;

	/**
	 * Constructor for Init-Handler.
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
	public static function get_instance(): PersonioPosition {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize this post-type.
	 *
	 * @return void
	 */
	public function init(): void {
		// register this taxonomy.
		add_action( 'init', array( $this, 'register') );

		// change rest api.
		add_filter( 'rest_prepare_' . $this->get_name(), array( $this, 'rest_prepare' ), 12, 2 );
	}

	/**
	 * Register this post-type.
	 *
	 * @return void
	 */
	public function register(): void {
		$labels = array(
			'name'               => __( 'Positions', 'personio-integration-light' ),
			'singular_name'      => __( 'Position', 'personio-integration-light' ),
			'menu_name'          => __( 'Positions', 'personio-integration-light' ),
			'parent_item_colon'  => __( 'Parent Position', 'personio-integration-light' ),
			'all_items'          => __( 'All Positions', 'personio-integration-light' ),
			'view_item'          => __( 'View Position in frontend', 'personio-integration-light' ),
			'view_items'         => __( 'View Positions', 'personio-integration-light' ),
			'edit_item'          => __( 'View Position in backend', 'personio-integration-light' ),
			'search_items'       => __( 'Search Position', 'personio-integration-light' ),
			'not_found'          => __( 'Not Found', 'personio-integration-light' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'personio-integration-light' ),
		);

		// get the slugs.
		$archive_slug = apply_filters( 'personio_integration_archive_slug', Helper::get_archive_slug() );
		$detail_slug  = apply_filters( 'personio_integration_detail_slug', Helper::get_detail_slug() );

		// set arguments for our own cpt.
		$args = array(
			'label'               => $labels['name'],
			'description'         => '',
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'custom-fields' ),
			'public'              => true,
			'hierarchical'        => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'has_archive'         => $archive_slug,
			'can_export'          => false,
			'exclude_from_search' => false,
			'taxonomies'          => array(
				WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY,
				WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY,
				WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION,
				WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE,
				WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT,
				WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES,
				WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE,
				WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY,
				WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE,
				WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE,
				WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS,
			),
			'publicly_queryable'  => (bool) $detail_slug,
			'show_in_rest'        => true,
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts'       => 'do_not_allow',
				'delete_posts'       => 'do_not_allow',
				'edit_post'          => 'read_' . WP_PERSONIO_INTEGRATION_CPT,
				'edit_posts'         => 'read_' . WP_PERSONIO_INTEGRATION_CPT,
				'edit_others_posts'  => 'do_not_allow',
				'read_post'          => 'do_not_allow',
				'read_posts'         => 'do_not_allow',
				'publish_posts'      => 'do_not_allow',
				'read_private_posts' => 'do_not_allow',
			),
			'menu_icon'           => trailingslashit(plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN )) . 'gfx/personio_icon.png',
			'rewrite'             => array(
				'slug' => $detail_slug,
			),
		);
		register_post_type( WP_PERSONIO_INTEGRATION_CPT, $args );

		// register personioId als postmeta to be published in rest-api,
		// which is necessary for our Blocks.
		register_post_meta(
			WP_PERSONIO_INTEGRATION_CPT,
			WP_PERSONIO_INTEGRATION_CPT_PM_PID,
			array(
				'type'         => 'integer',
				'single'       => true,
				'show_in_rest' => true,
			)
		);
	}

	/**
	 * Change the REST API-response for own cpt.
	 *
	 * @param WP_REST_Response $data The response object.
	 * @param WP_Post          $post The requested object.
	 * @return WP_REST_Response
	 * @noinspection PhpUnused
	 */
	public function rest_prepare( WP_REST_Response $data, WP_Post $post ): WP_REST_Response {
		// get positions-object.
		$positions = Positions::get_instance();

		// get the position as object.
		$position = $positions->get_position( $post->ID );

		// generate content.
		$content = $position->get_content();

		// generate except.
		$excerpt = $position->get_excerpt();

		// add result to response.
		$data->data['excerpt'] = array(
			'rendered'  => $excerpt,
			'raw'       => '',
			'protected' => false,
		);
		$data->data['content'] = $content;

		// set response.
		return $data;
	}

}

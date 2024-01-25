<?php
/**
 * File to handle our own custom post type PersonioPostion.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\PostTypes;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Personio;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\Post_Type;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use PersonioIntegrationLight\Plugin\Admin\Admin;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Templates;
use WP_Post;
use WP_Query;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Object of this custom post type.
 */
class PersonioPosition extends Post_Type {
	/**
	 * Set name of this cpt.
	 *
	 * @var string
	 */
	protected string $name = WP_PERSONIO_INTEGRATION_MAIN_CPT;

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
		// register taxonomies for this cpt.
		Taxonomies::get_instance()->init();

		// register this cpt.
		add_action( 'init', array( $this, 'register' ) );

		// REST-API-hooks.
		add_filter( 'rest_prepare_' . $this->get_name(), array( $this, 'rest_prepare' ), 12, 2 );
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );

		// define our 2 shortcodes.
		add_action( 'init', array( $this, 'shortcodes' ) );

		// log deletion of position.
		add_action( 'before_delete_post', array( $this, 'delete' ) );

		// manage backend columns.
		add_filter( 'manage_' . $this->get_name() . '_posts_columns', array( $this, 'add_column' ) );
		add_action( 'manage_' . $this->get_name() . '_posts_custom_column', array( $this, 'add_column_content' ), 10, 2 );
		add_filter( 'bulk_actions-edit-' . $this->get_name(), array( $this, 'remove_bulk_actions' ), 10, 0 );
		add_filter( 'post_row_actions', array( $this, 'remove_actions' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'add_filter' ) );
		add_filter( 'parse_query', array( $this, 'use_filter' ) );
		add_filter( 'views_edit-' . $this->get_name(), array( $this, 'hide_cpt_filter' ), 10, 0 );
		add_filter( 'pre_get_posts', array( $this, 'ignore_author' ) );

		// edit positions.
		add_action( 'admin_init', array( $this, 'remove_cpt_supports' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'add_meta_boxes', array( $this, 'remove_third_party_meta_boxes' ), PHP_INT_MAX );
		add_action( 'admin_menu', array( $this, 'disable_create_options' ) );

		// use our own hooks.
		add_filter( 'personio_integration_get_shortcode_attributes', array( $this, 'check_filter_type' ) );

		// misc hooks.
		add_filter( 'posts_search', array( $this, 'extend_search' ), 10, 2 );
		add_filter( 'wp_sitemaps_posts_entry', array( $this, 'add_sitemap_data' ), 10, 2 );
	}

	/**
	 * Register this custom post type.
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

		// get slugs.
		$archive_slug = Helper::get_archive_slug();
		$single_slug  = Helper::get_detail_slug();

		// set arguments for our own cpt.
		$args = array(
			'label'               => $labels['name'],
			'description'         => '',
			'labels'              => $labels,
			'supports'            => array( '' ),
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
			'publicly_queryable'  => (bool) $single_slug,
			'show_in_rest'        => true,
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts'       => 'do_not_allow',
				'delete_posts'       => 'do_not_allow',
				'edit_post'          => 'read_' . $this->get_name(),
				'edit_posts'         => 'read_' . $this->get_name(),
				'edit_others_posts'  => 'do_not_allow',
				'read_post'          => 'do_not_allow',
				'read_posts'         => 'do_not_allow',
				'publish_posts'      => 'do_not_allow',
				'read_private_posts' => 'do_not_allow',
			),
			'menu_icon'           => Helper::get_plugin_url() . 'gfx/personio_icon.png',
			'rewrite'             => array(
				'slug' => $single_slug,
			),
		);
		register_post_type( $this->get_name(), $args );

		// register personioId als postmeta to be published in rest-api,
		// which is necessary for our Blocks.
		register_post_meta(
			WP_PERSONIO_INTEGRATION_MAIN_CPT,
			WP_PERSONIO_INTEGRATION_MAIN_CPT_PM_PID,
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

	/**
	 * Define our 2 shortcodes.
	 *
	 * @return void
	 */
	public function shortcodes(): void {
		add_shortcode( 'personioPosition', array( $this, 'shortcode_single' ) );
		add_shortcode( 'personioPositions', array( $this, 'shortcode_archive' ) );
	}

	/**
	 * Output of single positions via shortcode and any PageBuilder.
	 * Example: [personioPosition lang="de" personioid="96" templates="title,content,formular" excerpt="recruitingCategory,schedule,office,department,seniority,experience,occupation"]
	 *
	 * Parameter:
	 * - personioid => PersonioId of the position (required)
	 * - lang => sets the language for the output, defaults to default-language from plugin-settings
	 * - templates => comma-separated list of template to use, defaults to title and excerpt
	 * - excerpt => comma-separated list of details to display, defaults to recruitingCategory, schedule, office
	 * - donotlink => if position-title should be linked (0) or not (1), defaults to link (0)
	 * - jobdescription_template => define specific template for job description (defaults to setting under positions > settings > templates)
	 *
	 * Templates:
	 * - title => show position title
	 * - excerpt => show detail configured by excerpt-parameter
	 * - content => show language-specific content
	 * - formular => show application-button
	 *
	 * @param array $attributes The shortcode attributes.
	 * @return string
	 */
	public function shortcode_single( array $attributes = array() ): string {
		// convert single shortcode attributes.
		$personio_attributes = $this->get_single_shortcode_attributes( $attributes );

		// do not output anything without ID.
		if ( $personio_attributes['personioid'] <= 0 ) {
			if ( 1 === absint( get_option( 'personioIntegration_debug', 0 ) ) ) {
				return '<div><p>' . __( 'Detail-view called without the PersonioId of a position.', 'personio-integration-light' ) . '</p></div>';
			}
			return '';
		}

		// get the position by its PersonioId.
		$positions = Positions::get_instance();
		$position  = $positions->get_position_by_personio_id( $personio_attributes['personioid'] );

		// do not show this position if it is not valid or could not be loaded.
		if ( $position && ! $position->is_valid() || ! $position ) {
			if ( 1 === absint( get_option( 'personioIntegration_debug', 0 ) ) ) {
				return '<div><p>' . __( 'Given Id is not a valid position-Id.', 'personio-integration-light' ) . '</p></div>';
			}
			return '';
		}

		// set language.
		$position->lang = $personio_attributes['lang'];

		// get the attributes defaults.
		$default_attributes = $this->get_single_shortcode_attributes_defaults();

		/**
		 * Change settings for output.
		 *
		 * @since 2.0.0 Available since 2.0.0.
		 *
		 * @param array $personio_attributes The attributes used for this output.
		 * @param array $default_attributes The default attributes.
		 */
		$personio_attributes = apply_filters( 'personio_integration_get_template', $personio_attributes, $default_attributes );

		// generate styling.
		$styles = ! empty( $personio_attributes['styles'] ) ? $personio_attributes['styles'] : '';

		// collect the output.
		ob_start();
		include Templates::get_instance()->get_template( 'single-' . WP_PERSONIO_INTEGRATION_MAIN_CPT . '-shortcode' . $personio_attributes['template'] . '.php' );
		return ob_get_clean();
	}

	/**
	 * Output of list of positions via shortcode and any PageBuilder.
	 * Example: [personioPositions filter="office,recruitingCategory,occupationCategory,department,employmenttype,seniority,schedule,experience,language" filtertype="select" lang="de" templates="title,excerpt,content" excerpt="recruitingCategory,schedule,office,department,seniority,experience,occupation" ids="96,97"]
	 *
	 * Parameter:
	 * - lang => sets the language for the output, defaults to default-language from plugin-settings
	 * - showfilter => enables the filter for this list-view, default: disabled
	 * - filter => comma-separated list of filter which will be visible above the list, default: empty
	 * - filtertype => sets the type of filter to use (select or linklist), default: select
	 * - template => set the main template to use for listing
	 * - templates => comma-separated list of template to use, defaults to title and excerpt
	 *  - jobdescription_template => define specific template for job description (defaults to setting under positions > settings > templates)
	 * - excerpt => comma-separated list of details to display, defaults to recruitingCategory, schedule, office
	 * - ids => comma-separated list of PositionIDs to display, default: empty
	 * - sort => direction for sorting the resulting list (asc or desc), default: asc
	 * - sortby => Field to be sorted by (title or date), default: title
	 * - limit => limit the items in the list (-1 for unlimited, 0 for pagination-setting), default: 0
	 * - listing_template => template to use for archive, default: default
	 *
	 * Filter:
	 * - office
	 * - recruitingCategory
	 * - occupationCategory
	 * - department
	 * - employmenttype
	 * - seniority
	 * - schedule
	 * - experience
	 *
	 * Templates for each position:
	 * - title => show position title
	 * - excerpt => show details configured by excerpt-parameter
	 * - content => show language-specific content
	 * - formular => show application-button
	 *
	 * @param array $attributes The shortcode attributes.
	 *
	 * @return string
	 */
	public function shortcode_archive( array $attributes = array() ): string {
		// set pagination settings.
		$pagination = true;

		/**
		 * Set pagination settings.
		 *
		 * @since 1.2.0 Available since 1.2.0.
		 *
		 * @param bool $pagination The pagination setting (true to disable it).
		 *
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		$pagination = apply_filters( 'personio_integration_pagination', $pagination );

		// define the default values for each attribute.
		$attribute_defaults = array(
			'lang'                    => Languages::get_instance()->get_current_lang(),
			'showfilter'              => ( 1 === absint( get_option( 'personioIntegrationEnableFilter', 0 ) ) ),
			'filter'                  => implode( ',', get_option( 'personioIntegrationTemplateFilter', '' ) ),
			'filtertype'              => get_option( 'personioIntegrationFilterType', 'select' ),
			'template'                => '',
			'templates'               => implode( ',', get_option( 'personioIntegrationTemplateContentList', '' ) ),
			'listing_template'        => get_option( 'personioIntegrationTemplateContentListingTemplate', 'default' ),
			'jobdescription_template' => get_option( 'personioIntegrationTemplateListingContentTemplate', 'default' ),
			'excerpt'                 => implode( ',', get_option( 'personioIntegrationTemplateExcerptDefaults', '' ) ),
			'ids'                     => '',
			'donotlink'               => ( 0 === absint( get_option( 'personioIntegrationEnableLinkInList', 0 ) ) ),
			'sort'                    => 'asc',
			'sortby'                  => 'title',
			'limit'                   => 0,
			'nopagination'            => $pagination,
			'groupby'                 => '',
			'styles'                  => '',
			'classes'                 => '',
		);

		// define the settings for each attribute (array or string).
		$attribute_settings = array(
			'id'                      => 'string',
			'lang'                    => 'string',
			'showfilter'              => 'bool',
			'filter'                  => 'array',
			'template'                => 'string',
			'listing_template'        => 'listing_template',
			'jobdescription_template' => 'jobdescription_template',
			'templates'               => 'array',
			'excerpt'                 => 'array',
			'ids'                     => 'array',
			'donotlink'               => 'bool',
			'sort'                    => 'string',
			'sortby'                  => 'string',
			'limit'                   => 'unsignedint',
			'filtertype'              => 'string',
			'nopagination'            => 'bool',
			'groupby'                 => 'string',
			'styles'                  => 'string',
			'classes'                 => 'string',
		);

		// add taxonomies which are available as filter.
		foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
			// bail if no slug is set for this taxonomy.
			if ( empty( $taxonomy['slug'] ) ) {
				continue;
			}
			if ( 1 === absint( $taxonomy['useInFilter'] ) ) {
				if ( ! empty( $GLOBALS['wp']->query_vars['personiofilter'] ) && ! empty( $GLOBALS['wp']->query_vars['personiofilter'][ $taxonomy['slug'] ] ) ) {
					$attribute_defaults[ $taxonomy['slug'] ] = 0;
					$attribute_settings[ $taxonomy['slug'] ] = 'filter';
				}
			}
		}

		// get the attributes to filter.
		$personio_attributes = Helper::get_shortcode_attributes( $attribute_defaults, $attribute_settings, $attributes );

		// get positions-object for search.
		$positions_obj = Positions::get_instance();

		// filter for specific ids.
		if ( ! empty( $personio_attributes['ids'][0] ) ) {
			// convert id-list from PersonioId in post_id.
			$resulting_list = array();
			foreach ( $personio_attributes['ids'] as $personio_id ) {
				$position = $positions_obj->get_position_by_personio_id( $personio_id );
				if ( $position instanceof Position ) {
					$resulting_list[] = $position->get_id();
				}
			}
			$personio_attributes['ids'] = $resulting_list;
		}

		// set limit.
		$limit_by_wp                  = $personio_attributes['limit'] ? ( absint( get_option( 'posts_per_page' ) ) > 10 ? absint( get_option( 'posts_per_page' ) ) : 10 ) : 10;
		$personio_attributes['limit'] = apply_filters( 'personio_integration_limit', $limit_by_wp, $personio_attributes['limit'] );

		// get the positions.
		$positions                         = $positions_obj->get_positions( $personio_attributes['limit'], $personio_attributes );
		$GLOBALS['personio_query_results'] = $positions_obj->get_results();

		/**
		 * Change settings for output.
		 *
		 * @since 2.0.0 Available since first release.
		 *
		 * @param array $personio_attributes The attributes used for this output.
		 * @param array $attribute_defaults The default attributes.
		 */
		$personio_attributes = apply_filters( 'personio_integration_get_template', $personio_attributes, $attribute_defaults );

		// generate styling.
		$styles = ! empty( $personio_attributes['styles'] ) ? $personio_attributes['styles'] : '';

		// set the group-title.
		$group_title = '';

		// collect the output.
		ob_start();
		include Templates::get_instance()->get_template( 'archive-' . WP_PERSONIO_INTEGRATION_MAIN_CPT . '-shortcode' . $personio_attributes['template'] . '.php' );
		return ob_get_clean();
	}

	/**
	 * Initialize additional REST API endpoints.
	 *
	 * @return void
	 */
	public function rest_api_init(): void {
		// return possible taxonomies.
		register_rest_route(
			'personio/v1',
			'/taxonomies/',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_taxonomies_via_rest_api' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		// return possible details templates.
		register_rest_route(
			'personio/v1',
			'/details-templates/',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_details_templates_via_rest_api' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		// return possible jobdescription templates.
		register_rest_route(
			'personio/v1',
			'/jobdescription-templates/',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_jobdescription_templates_via_rest_api' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		// return possible archive-listing templates.
		register_rest_route(
			'personio/v1',
			'/archive-templates/',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_archive_templates_via_rest_api' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	/**
	 * Return list of available taxonomies for REST-API.
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function get_taxonomies_via_rest_api(): array {
		$taxonomies_labels_array = Taxonomies::get_instance()->get_taxonomy_labels_for_settings();
		$taxonomies              = array();
		$count                   = 0;
		foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
			if ( 1 === absint( $taxonomy['useInFilter'] ) ) {
				++$count;
				$terms_as_objects = get_terms( array( 'taxonomy' => $taxonomy_name ) );
				$term_count       = 0;
				$terms            = array(
					array(
						'id'    => $term_count,
						'label' => __( 'Please choose', 'personio-integration-light' ),
						'value' => 0,
					),
				);
				foreach ( $terms_as_objects as $term ) {
					++$term_count;
					$terms[] = array(
						'id'    => $term_count,
						'label' => $term->name,
						'value' => $term->term_id,
					);
				}
				if ( ! empty( $taxonomies_labels_array[ $taxonomy['slug'] ] ) ) {
					$taxonomies[] = array(
						'id'      => $count,
						'label'   => $taxonomies_labels_array[ $taxonomy['slug'] ],
						'value'   => $taxonomy['slug'],
						'entries' => $terms,
					);
				}
			}
		}

		// return resulting list of taxonomies.
		return $taxonomies;
	}

	/**
	 * Return list of possible templates for details in REST API.
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function get_details_templates_via_rest_api(): array {
		$templates = array(
			array(
				'id'    => 1,
				'label' => __( 'Default', 'personio-integration-light' ),
				'value' => 'default',
			),
			array(
				'id'    => 2,
				'label' => __( 'As list', 'personio-integration-light' ),
				'value' => 'list',
			),
		);

		/**
		 * Filter the available details-templates for REST API.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param array $templates The templates.
		 */
		return apply_filters( 'personio_integration_rest_templates_details', $templates );
	}

	/**
	 * Return list of possible templates for job description in REST API.
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function get_jobdescription_templates_via_rest_api(): array {
		$templates = array(
			array(
				'id'    => 1,
				'label' => __( 'Default', 'personio-integration-light' ),
				'value' => 'default',
			),
			array(
				'id'    => 2,
				'label' => __( 'As list', 'personio-integration-light' ),
				'value' => 'list',
			),
		);

		/**
		 * Filter the available jobdescription-templates for REST API.
		 *
		 * @since 2.6.0 Available since 2.6.0.
		 *
		 * @param array $templates The templates.
		 */
		return apply_filters( 'personio_integration_rest_templates_jobdescription', $templates );
	}

	/**
	 * Return list of possible templates for archives in REST API.
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function get_archive_templates_via_rest_api(): array {
		$templates = array(
			array(
				'id'    => 1,
				'label' => __( 'Default', 'personio-integration-light' ),
				'value' => 'default',
			),
			array(
				'id'    => 2,
				'label' => __( 'Listing', 'personio-integration-light' ),
				'value' => 'listing',
			),
		);

		/**
		 * Filter the available archive-templates for REST API.
		 *
		 * @since 2.6.0 Available since 2.6.0.
		 *
		 * @param array $templates The templates.
		 */
		return apply_filters( 'personio_integration_rest_templates_archive', $templates );
	}

	/**
	 * Log every deletion of a position.
	 *
	 * @param int $post_id The ID of the post which will be deleted.
	 * @return void
	 */
	public function delete( int $post_id ): void {
		// bail if this is not our own cpt.
		if ( $this->get_name() !== get_post_type( $post_id ) ) {
			return;
		}

		// get position.
		$positions_obj = Positions::get_instance();
		$position_obj  = $positions_obj->get_position( $post_id );

		// log deletion.
		$log = new Log();
		$log->add_log( 'Position ' . $position_obj->get_personio_id() . ' has been deleted.', 'success' );
	}

	/**
	 * Add columns to position-table in backend.
	 *
	 * @param array $columns List of columns.
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function add_column( array $columns ): array {
		// create new column-array.
		$new_columns = array();

		// add column for Pro-hint with sorting.
		$false = false;

		/**
		 * Hide the additional the sort column which is only filled in Pro.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param array $false Set true to hide the buttons.
		 */
		if( ! apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			$new_columns['sort'] = __( 'Sorting', 'personio-integration-light' );
		}

		// add column for PersonioId.
		$new_columns['id'] = __( 'PersonioID', 'personio-integration-light' );

		// remove checkbox-column if pro is not active.
		if ( false === Helper::is_plugin_active( 'personio-integration/personio-integration.php' ) ) {
			unset( $columns['cb'] );
		}

		// return results.
		return array_merge( $new_columns, $columns );
	}

	/**
	 * Add content to the column in the position-table in backend.
	 *
	 * @param string $column Name of the column.
	 * @param int    $post_id The ID of the WP_Post-object.
	 * @return void
	 */
	public function add_column_content( string $column, int $post_id ): void {
		if ( 'id' === $column ) {
			$position = new Position( $post_id );
			echo absint( $position->get_personio_id() );
		}

		if ( 'sort' === $column ) {
			echo '<span class="pro-marker">' . esc_html__( 'Only in Pro', 'personio-integration-light' ) . '</span>';
		}
	}

	/**
	 * Remove any bulk actions for our own cpt.
	 *
	 * @return array
	 */
	public function remove_bulk_actions(): array {
		return array();
	}

	/**
	 * Remove all actions except "view" and "edit" for our own cpt.
	 *
	 * @param array   $actions List of actions.
	 * @param WP_Post $post Object of the post.
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function remove_actions( array $actions, WP_Post $post ): array {
		if ( WP_PERSONIO_INTEGRATION_MAIN_CPT === get_post_type() ) {
			$actions         = array(
				'view' => $actions['view'],
			);
			$actions['edit'] = '<a href="' . esc_url( get_edit_post_link( $post->ID ) ) . '">' . __( 'Edit', 'personio-integration-light' ) . '</a>';
			return $actions;
		}
		return $actions;
	}

	/**
	 * Add filter for our own cpt on lists in admin.
	 *
	 * @return void
	 */
	public function add_filter(): void {
		$post_type = sanitize_text_field( wp_unslash( filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) );
		if ( is_null( $post_type ) ) {
			$post_type = '';
		}

		if ( WP_PERSONIO_INTEGRATION_MAIN_CPT === $post_type ) {
			// add filter for each taxonomy.
			foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
				// show only taxonomies which are visible in filter.
				if ( 1 === absint( $taxonomy['useInFilter'] ) ) {
					// get the taxonomy as object.
					$taxonomy = get_taxonomy( $taxonomy_name );

					// get its terms.
					$terms = get_terms(
						array(
							'taxonomy'   => $taxonomy_name,
							'hide_empty' => false,
						)
					);

					// list terms only if they are available.
					if ( ! empty( $terms ) ) {
						?>
						<!--suppress HtmlFormInputWithoutLabel -->
						<select name="admin_filter_<?php echo esc_attr( $taxonomy_name ); ?>">
							<option value="0"><?php echo esc_html( $taxonomy->label ); ?></option>
							<?php
							foreach ( $terms as $term ) {
								?>
								<option value="<?php echo esc_attr( $term->term_id ); ?>"<?php echo ( absint( filter_input( INPUT_GET, 'admin_filter_' . $taxonomy_name, FILTER_SANITIZE_NUMBER_INT ) ) === $term->term_id ) ? ' selected="selected"' : ''; ?>><?php echo esc_html( $term->name ); ?></option>
								<?php
							}
							?>
						</select>
						<?php
					}
				}
			}
		}
	}

	/**
	 * Use filter in admin on edit-page for filtering the cpt-items.
	 *
	 * @param WP_Query $query The WP_Query-object.
	 * @return void
	 */
	public function use_filter( WP_Query $query ): void {
		global $pagenow;
		$post_type = sanitize_text_field( wp_unslash( filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) );
		if ( is_null( $post_type ) ) {
			$post_type = 'post';
		}

		if ( WP_PERSONIO_INTEGRATION_MAIN_CPT === $post_type && 'edit.php' === $pagenow ) {
			// add filter for each taxonomy.
			$tax_query = array();
			foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
				if ( 1 === absint( $taxonomy['useInFilter'] ) ) {
					if ( absint( wp_unslash( filter_input( INPUT_GET, 'admin_filter_' . $taxonomy_name, FILTER_SANITIZE_NUMBER_INT ) ) ) > 0 ) {
						$tax_query[] = array(
							'taxonomy' => $taxonomy_name,
							'field'    => 'term_id',
							'terms'    => absint( wp_unslash( filter_input( INPUT_GET, 'admin_filter_' . $taxonomy_name, FILTER_SANITIZE_NUMBER_INT ) ) ),
						);
					}
				}
			}
			if ( ! empty( $tax_query ) ) {
				if ( count( $tax_query ) > 1 ) {
					$query->set(
						'tax_query',
						array(
							'relation' => 'AND',
							$tax_query,
						)
					);
				} else {
					$query->set( 'tax_query', $tax_query );
				}
			}
		}
	}

	/**
	 * Hide cpt filter-view.
	 *
	 * @return array
	 */
	public function hide_cpt_filter(): array {
		return array();
	}

	/**
	 * Force list-view of our own cpt to ignore author as filter.
	 *
	 * @param WP_Query $query The WP_Query-object.
	 * @return WP_Query
	 */
	public function ignore_author( WP_Query $query ): WP_Query {
		if ( is_admin() && ! empty( $query->query_vars['post_type'] ) && $this->get_name() === $query->query_vars['post_type'] ) {
			$query->set( 'author', 0 );
		}
		return $query;
	}

	/**
	 * Remove supports from our own cpt and change our taxonomies.
	 * Goal: edit-page without any generic settings.
	 *
	 * @return void
	 */
	public function remove_cpt_supports(): void {
		// remove generic meta box for slug.
		remove_meta_box( 'slugdiv', $this->get_name(), 'normal' );

		// remove generic taxonomy-meta-boxes.
		foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $settings ) {
			$taxonomy              = get_taxonomy( $taxonomy_name );
			$taxonomy->meta_box_cb = false;
			register_taxonomy( $taxonomy_name, $this->get_name(), $taxonomy );
		}
	}

	/**
	 * Add Box with hints for editing.
	 * Add Open Graph Meta-box für edit-page of positions.
	 *
	 * @return void
	 */
	public function add_meta_box(): void {
		// TODO für Pro ausblendbar machen.
		add_meta_box(
			$this->get_name().'-edit-hints',
			__( 'About this page', 'personio-integration-light' ),
			array( $this, 'get_meta_box_data_hint' ),
			$this->get_name()
		);

		add_meta_box(
			$this->get_name().'-id',
			__( 'PersonioID', 'personio-integration-light' ),
			array( $this, 'get_meta_box_for_personio_id' ),
			$this->get_name()
		);

		add_meta_box(
			$this->get_name().'-title',
			__( 'Title', 'personio-integration-light' ),
			array( $this, 'get_meta_box_for_title' ),
			$this->get_name()
		);

		add_meta_box(
			$this->get_name().'-text',
			__( 'Description', 'personio-integration-light' ),
			array( $this, 'get_meta_box_for_description' ),
			$this->get_name()
		);

		// get taxonomies as object.
		$taxonomies_obj = Taxonomies::get_instance();

		// add meta box for each supported taxonomy.
		foreach ( $taxonomies_obj->get_taxonomies() as $taxonomy_name => $settings ) {
			$labels = $taxonomies_obj->get_taxonomy_label( $taxonomy_name );
			add_meta_box(
				$this->get_name().'-taxonomy-' . $taxonomy_name,
				$labels['name'],
				array( $this, 'get_meta_box_for_taxonomy' ),
				$this->get_name(),
				'side'
			);
		}

		// add meta box with Pro-hint for subcompany-field which is only in Pro.
		add_meta_box(
			$this->get_name().'-taxonomy-subcompany',
			__( 'Subcompany', 'personio-integration-light' ),
			array( $this, 'get_meta_box_for_pro_taxonomy' ),
			$this->get_name(),
			'side'
		);
	}

	/**
	 * Remove all meta boxes which are not part of this post type.
	 *
	 * @return void
	 */
	public function remove_third_party_meta_boxes(): void {
		global $wp_meta_boxes;

		/**
		 * Get the actual screen.
		 */
		if ( empty( $screen ) )
			$screen = get_current_screen();
		elseif ( is_string( $screen ) )
			$screen = convert_to_screen( $screen );

		$page = $screen->id;

		// bail if this is not our own cpt.
		if( WP_PERSONIO_INTEGRATION_MAIN_CPT !== $page ) {
			return;
		}

		$false = false;
		/**
		 * Prevent removing of all meta boxes in cpt edit view.
		 *
		 * Caution: the boxes will not be able to be saved.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param bool $false Set true to prevent removing of each meta box.
		 */
		if( apply_filters( 'personio_integration_get_template', $false ) ) {
			return;
		}

		/**
		 * Loop through the boxes for this cpt and remove all which do not belong to our plugin.
		 */
		foreach( $wp_meta_boxes[$page] as $context => $priority_boxes ) {
			foreach( $priority_boxes as $boxes ) {
				foreach( $boxes as $box ) {
					if ( false !== $box && false === str_contains( $box['id'], $this->get_name() ) ) {
						remove_meta_box( $box['id'], $page, $context );
					}
				}
			}
		}
	}

	/**
	 * Box with hints why editing of Position-data is not allowed.
	 *
	 * @param WP_Post $post Object of the post.
	 * @return void
	 */
	public function get_meta_box_data_hint( WP_Post $post ): void {
		if ( $post->ID > 0 ) {
			$position = new Position( $post->ID );
			if ( $position->is_valid() ) {
				$url = Helper::get_personio_login_url();
				/* translators: %1$s will be replaced by the URL for Personio */
				printf( wp_kses_post( __( 'These are the data of your open position <i>%1$s</i> we imported from Personio. Please edit the job details in your <a href="%2$s" target="_blank">Personio account (opens new window)</a>.', 'personio-integration-light' ) ), esc_html( $position->get_title() ), esc_url( $url ) );
			}
		}
	}

	/**
	 * Show personioId in meta box.
	 *
	 * @param WP_Post $post Object of the post.
	 * @return void
	 */
	public function get_meta_box_for_personio_id( WP_Post $post ): void {
		$position_obj = Positions::get_instance()->get_position( $post->ID );
		echo wp_kses_post( $position_obj->get_personio_id() );
	}

	/**
	 * Show title of position in meta box.
	 *
	 * @param WP_Post $post Object of the post.
	 * @return void
	 */
	public function get_meta_box_for_title( WP_Post $post ): void {
		$position_obj = Positions::get_instance()->get_position( $post->ID );
		echo wp_kses_post( $position_obj->get_title() );
	}

	/**
	 * Show content of position in meta box.
	 *
	 * @param WP_Post $post Object of the post.
	 *
	 * @return void
	 * @noinspection PhpUnusedParameterInspection
	 **/
	public function get_meta_box_for_description( WP_Post $post ): void {
		the_content();
	}

	/**
	 * Show any taxonomy of position in their own meta box.
	 *
	 * @param WP_Post $post Object of the post.
	 * @param array   $attr The attributes.
	 * @return void
	 */
	public function get_meta_box_for_taxonomy( WP_Post $post, array $attr ): void {
		$position_obj  = Positions::get_instance()->get_position( $post->ID );
		$taxonomy_name = str_replace( $this->get_name().'-taxonomy-', '', $attr['id'] );
		$content       = $position_obj->get_term_by_field( $taxonomy_name, 'name' );
		if ( empty( $content ) ) {
			echo '<i>' . esc_html__( 'No data', 'personio-integration-light' ) . '</i>';
		} else {
			// create filter url.
			$filter_url = add_query_arg(
				array(
					's'                              => '',
					'post_type'                      => $this->get_name(),
					'admin_filter_' . $taxonomy_name => $position_obj->get_term_by_field( $taxonomy_name, 'term_id' ),
					'paged'                          => 1,
				),
				admin_url() . 'edit.php'
			);

			// output.
			echo '<a href="' . esc_url( $filter_url ) . '">' . wp_kses_post( $content ) . '</a>';
		}
	}

	/**
	 * Show any taxonomy of position in their own meta box.
	 *
	 * @param WP_Post $post Object of the post.
	 * @param array   $attr The attributes.
	 *
	 * @return void
	 * @noinspection PhpUnusedParameterInspection
	 **/
	public function get_meta_box_for_pro_taxonomy( WP_Post $post, array $attr ): void {
		if ( ! empty( $attr['title'] ) ) {
			/* translators: %1$s will be replaced with the plugin pro-name */
			Admin::get_instance()->show_pro_hint( __( 'Use this taxonomy with %1$s.', 'personio-integration-light' ) );
		}
	}

	/**
	 * Through a bug in WordPress we must remove the "create"-option manually.
	 *
	 * @return void
	 */
	public function disable_create_options(): void {
		global $pagenow, $typenow;

		if ( is_admin() && ! empty( $typenow ) && ! empty( $pagenow ) && 'edit.php' === $pagenow && ! empty( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) && stripos( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'edit.php' ) && stripos( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'post_type=' . $typenow ) && ! stripos( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'page' ) ) {
			$pagenow = 'edit-' . $typenow . '.php';
		}
	}

	/**
	 * Convert attributes for shortcodes.
	 *
	 * @param array $attributes List of attributes.
	 *
	 * @return array
	 */
	public function get_single_shortcode_attributes( array $attributes ): array {
		// define the default values for each attribute.
		$attribute_defaults = $this->get_single_shortcode_attributes_defaults();

		// define the settings for each attribute (array or string).
		$attribute_settings = array(
			'personioid'              => 'int',
			'lang'                    => 'string',
			'templates'               => 'array',
			'excerpt'                 => 'array',
			'donotlink'               => 'bool',
			'styles'                  => 'string',
			'classes'                 => 'string',
			'jobdescription_template' => 'jobdescription_template',
		);
		return Helper::get_shortcode_attributes( $attribute_defaults, $attribute_settings, $attributes );
	}

	/**
	 * Return attribute defaults for shortcode in single-view.
	 *
	 * @return array
	 */
	private function get_single_shortcode_attributes_defaults(): array {
		return array(
			'personioid'              => 0,
			'lang'                    => Languages::get_instance()->get_main_language(),
			'template'                => '',
			'templates'               => implode( ',', get_option( 'personioIntegrationTemplateContentDefaults', array() ) ),
			'jobdescription_template' => get_option( 'personioIntegrationTemplateJobDescription', 'default' ),
			'excerpt'                 => implode( ',', get_option( 'personioIntegrationTemplateExcerptDetail', array() ) ),
			'donotlink'               => 1,
			'styles'                  => '',
			'classes'                 => '',
		);
	}

	/**
	 * Convert term-name to term-id if it is set in shortcode-attributes and configure shortcode-attribute.
	 *
	 * @param array $settings List of settings for a shortcode with 3 parts: defaults, settings & attributes.
	 * @return array
	 */
	public function check_filter_type( array $settings ): array {
		if ( ! empty( $settings['attributes']['filtertype'] ) ) {
			if ( ! in_array( $settings['attributes']['filtertype'], array( 'linklist', 'select' ), true ) ) {
				$settings['attributes']['filtertype'] = 'linklist';
			}
		}

		// return resulting arrays.
		return array(
			'defaults'   => $settings['defaults'],
			'settings'   => $settings['settings'],
			'attributes' => $settings['attributes'],
		);
	}

	/**
	 * Extend the WP-own search.
	 *
	 * @param string   $search The search-string.
	 * @param WP_Query $wp_query The query-object.
	 *
	 * @return string
	 */
	public function extend_search( string $search, WP_Query $wp_query ): string {
		global $wpdb;

		// bail on search in backend.
		if ( is_admin() ) {
			return $search;
		}

		// bail if extension of search is not enabled.
		if ( 0 === absint( get_option( 'personioIntegrationExtendSearch', 0 ) ) ) {
			return $search;
		}

		// bail of search string is empty.
		if ( empty( $search ) ) {
			return $search;
		}

		// get search request.
		$term = $wp_query->query_vars['s'];

		// create and return changed statement.
		return ' AND (
        (
            1 = 1 ' . $search . '
        )
        OR (
            ' . $wpdb->posts . ".post_type = '" . WP_PERSONIO_INTEGRATION_MAIN_CPT . "'
            AND EXISTS(
                SELECT * FROM " . $wpdb->terms . '
                INNER JOIN ' . $wpdb->term_taxonomy . '
                    ON ' . $wpdb->term_taxonomy . '.term_id = ' . $wpdb->terms . '.term_id
                INNER JOIN ' . $wpdb->term_relationships . '
                    ON ' . $wpdb->term_relationships . '.term_taxonomy_id = ' . $wpdb->term_taxonomy . ".term_taxonomy_id
                WHERE taxonomy = 'personioKeywords'
                    AND object_id = " . $wpdb->posts . '.ID
                    AND ' . $wpdb->terms . ".name LIKE '%" . $term . "%'
            )
        )
    )";
	}

	/**
	 * Return the link to manage items of this cpt in backend.
	 *
	 * @param bool $without_admin_url True if the URL should contain get_admin_url().
	 *
	 * @return string
	 */
	public function get_link( bool $without_admin_url = false ): string {
		return add_query_arg(
			array(
				'post_type' => self::get_instance()->get_name(),
			),
			( $without_admin_url ? '' : get_admin_url() ) . 'edit.php'
		);
	}

	/**
	 * Change sitemapXML-data for positions.
	 *
	 * Add last modification date and priority.
	 *
	 * @param array   $entry
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	public function add_sitemap_data( array $entry, WP_Post $post ): array {
		if( $this->get_name() === get_post_type( $post ) ) {
			$position = Positions::get_instance()->get_position( $post->ID );
			$entry['lastmod'] = gmdate( 'Y-m-d', $position->get_created_at() );
			$entry['priority'] = 0.8;

			/**
			 * Filter the data for the sitemap-entry for single position.
			 *
			 * @since 3.0.0 Available since 3.0.0.
			 *
			 * @param array $entry List of data for the sitemap.xml of this single position.
			 * @param Personio $position The Personio-object.
			 */
			return apply_filters( 'personio_integration_sitemap_entry', $entry, $position );
		}
		return $entry;
	}
}

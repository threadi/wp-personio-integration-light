<?php
/**
 * File to handle the post-type PersonioPostion.
 *
 * @package personio-integration-light
 */

namespace App\PersonioIntegration\PostTypes;

use App\Helper;
use App\PersonioIntegration\Position;
use App\PersonioIntegration\Positions;
use App\PersonioIntegration\Post_Type;
use App\PersonioIntegration\Taxonomies;
use App\Plugin\Templates;
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
		// register our taxonomies.
		Taxonomies::get_instance()->init();

		// register this cpt.
		add_action( 'init', array( $this, 'register' ) );

		// change rest api.
		add_filter( 'rest_prepare_' . $this->get_name(), array( $this, 'rest_prepare' ), 12, 2 );

		// define our 2 shortcodes.
		add_action( 'init', array( $this, 'shortcodes' ) );
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
			'menu_icon'           => trailingslashit( plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'gfx/personio_icon.png',
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

	/**
	 * Define our 2 shortcodes.
	 *
	 * @return void
	 */
	public function shortcodes(): void {
		add_shortcode( 'personioPosition', array( $this, 'shortcode_position' ) );
		add_shortcode( 'personioPositions', array( $this, 'shortcode_positions' ) );
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
	public function shortcode_position( array $attributes = array() ): string {
		// convert single shortcode attributes.
		$personio_attributes = personio_integration_get_single_shortcode_attributes( $attributes );

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
		if ( $position && ! $position->isValid() || ! $position ) {
			if ( 1 === absint( get_option( 'personioIntegration_debug', 0 ) ) ) {
				return '<div><p>' . __( 'Given Id is not a valid position-Id.', 'personio-integration-light' ) . '</p></div>';
			}
			return '';
		}

		// set language.
		$position->lang = $personio_attributes['lang'];

		// change settings for output.
		$personio_attributes = apply_filters( 'personio_integration_get_template', $personio_attributes, personio_integration_get_single_shortcode_attributes_defaults() );

		// generate styling.
		$styles = ! empty( $personio_attributes['styles'] ) ? $personio_attributes['styles'] : '';

		// collect the output.
		ob_start();
		include Templates::get_instance()->get_template( 'single-' . WP_PERSONIO_INTEGRATION_CPT . '-shortcode' . $personio_attributes['template'] . '.php' );
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
	 * @return string
	 */
	function shortcode_positions( array $attributes = array() ): string {
		// define the default values for each attribute.
		$attribute_defaults = array(
			'lang'             => helper::get_current_lang(),
			'showfilter'       => ( 1 === absint( get_option( 'personioIntegrationEnableFilter', 0 ) ) ),
			'filter'           => implode( ',', get_option( 'personioIntegrationTemplateFilter', '' ) ),
			'filtertype'       => get_option( 'personioIntegrationFilterType', 'select' ),
			'template'         => '',
			'templates'        => implode( ',', get_option( 'personioIntegrationTemplateContentList', '' ) ),
			'listing_template' => get_option( 'personioIntegrationTemplateContentListingTemplate', 'default' ),
			'excerpt'          => implode( ',', get_option( 'personioIntegrationTemplateExcerptDefaults', '' ) ),
			'ids'              => '',
			'donotlink'        => ( 0 === absint( get_option( 'personioIntegrationEnableLinkInList', 0 ) ) ),
			'sort'             => 'asc',
			'sortby'           => 'title',
			'limit'            => 0,
			'nopagination'     => apply_filters( 'personio_integration_pagination', true ),
			'groupby'          => '',
			'styles'           => '',
			'classes'          => '',
		);

		// define the settings for each attribute (array or string).
		$attribute_settings = array(
			'id'               => 'string',
			'lang'             => 'string',
			'showfilter'       => 'bool',
			'filter'           => 'array',
			'template'         => 'string',
			'listing_template' => 'listing_template',
			'templates'        => 'array',
			'excerpt'          => 'array',
			'ids'              => 'array',
			'donotlink'        => 'bool',
			'sort'             => 'string',
			'sortby'           => 'string',
			'limit'            => 'unsignedint',
			'filtertype'       => 'string',
			'nopagination'     => 'bool',
			'groupby'          => 'string',
			'styles'           => 'string',
			'classes'          => 'string',
		);

		// add taxonomies which are available as filter.
		foreach ( apply_filters( 'personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES ) as $taxonomy_name => $taxonomy ) {
			if ( ! empty( $taxonomy['slug'] ) && 1 === absint( $taxonomy['useInFilter'] ) ) {
				if ( ! empty( $_GET['personiofilter'] ) && ! empty( $_GET['personiofilter'][ $taxonomy['slug'] ] ) ) {
					$attribute_defaults[ $taxonomy['slug'] ] = 0;
					$attribute_settings[ $taxonomy['slug'] ] = 'filter';
				}
			}
			if ( ! empty( $taxonomy['slug'] ) && 1 === absint( $taxonomy['useInFilter'] ) ) {
				unset( $attribute_defaults[ $taxonomy['slug'] ] );
				unset( $attribute_settings[ $taxonomy['slug'] ] );
			}
		}

		// get the attributes to filter.
		$personio_attributes = helper::get_shortcode_attributes( $attribute_defaults, $attribute_settings, $attributes );

		// get positions-object for search.
		$positions_obj = Positions::get_instance();

		// unset the id-list if it is empty.
		// TODO get better solution for limit.
		if ( empty( $personio_attributes['ids'][0] ) ) {
			unset( $personio_attributes['ids'] );
		} else {
			// convert id-list from PersonioId in post_id.
			$resulting_list = array();
			foreach ( $personio_attributes['ids'] as $personio_id ) {
				$position = $positions_obj->get_position_by_personio_id( $personio_id );
				if ( $position instanceof Position ) {
					$resulting_list[] = $position->ID;
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

		// change settings for output.
		$personio_attributes = apply_filters( 'personio_integration_get_template', $personio_attributes, $attribute_defaults );

		// generate styling.
		$styles = ! empty( $personio_attributes['styles'] ) ? $personio_attributes['styles'] : '';

		// set the group-title.
		// TODO check compatibility.
		$group_title = '';

		// collect the output.
		ob_start();
		include Templates::get_instance()->get_template( 'archive-' . WP_PERSONIO_INTEGRATION_CPT . '-shortcode' . $personio_attributes['template'] . '.php' );
		return ob_get_clean();
	}
}

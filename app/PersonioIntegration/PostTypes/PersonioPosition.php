<?php
/**
 * File to handle our own custom post type PersonioPostion.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\PostTypes;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Extensions;
use PersonioIntegrationLight\PersonioIntegration\Extensions_Base;
use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\Personio;
use PersonioIntegrationLight\PersonioIntegration\Personio_Accounts;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\Post_Type;
use PersonioIntegrationLight\PersonioIntegration\Statistics;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use PersonioIntegrationLight\PersonioIntegration\Themes;
use PersonioIntegrationLight\PersonioIntegration\Widgets;
use PersonioIntegrationLight\Plugin\Admin\Admin;
use PersonioIntegrationLight\Plugin\Compatibilities\Loco;
use PersonioIntegrationLight\Plugin\Compatibilities\SayWhat;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Setup;
use PersonioIntegrationLight\Plugin\Templates;
use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_Taxonomy;

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
	 * Constructor for this object.
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
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this post-type.
	 *
	 * @return void
	 */
	public function init(): void {
		// register taxonomies for this cpt.
		Taxonomies::get_instance()->init();

		// enable theme-support.
		Themes::get_instance()->init();

		// init imports.
		Imports::get_instance()->init();

		// add our own widgets.
		Widgets::get_instance()->init();

		// enable extensions.
		Extensions::get_instance()->init();

		// initialize the statistics.
		Statistics::get_instance()->init();

		// register this cpt.
		add_action( 'init', array( $this, 'register' ) );

		// REST-API-hooks.
		add_filter( 'rest_prepare_' . $this->get_name(), array( $this, 'rest_prepare' ), 12, 2 );
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );

		// log deletion of position.
		add_action( 'before_delete_post', array( $this, 'delete' ) );

		// manage backend columns.
		add_filter( 'manage_' . $this->get_name() . '_posts_columns', array( $this, 'add_column' ) );
		add_action( 'manage_' . $this->get_name() . '_posts_custom_column', array( $this, 'add_column_content' ), 10, 2 );
		add_filter( 'bulk_actions-edit-' . $this->get_name(), array( $this, 'remove_bulk_actions' ), PHP_INT_MAX, 0 );
		add_filter( 'post_row_actions', array( $this, 'remove_actions' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'add_filter' ) );
		add_action( 'parse_query', array( $this, 'use_filter' ) );
		add_filter( 'views_edit-' . $this->get_name(), array( $this, 'hide_cpt_filter' ), 10, 0 );
		add_filter( 'pre_get_posts', array( $this, 'ignore_author' ) );
		add_filter( 'posts_search', array( $this, 'search_also_in_meta_fields' ), 10, 2 );

		// edit positions.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
		add_action( 'add_meta_boxes', array( $this, 'remove_third_party_meta_boxes' ), PHP_INT_MAX );
		add_action( 'admin_menu', array( $this, 'disable_create_options' ) );
		add_filter( 'admin_footer_text', array( $this, 'show_plugin_hint_in_footer' ) );

		// add ajax-hooks.
		add_action( 'wp_ajax_personio_get_deletion_info', array( $this, 'get_deletion_info' ) );
		add_action( 'wp_ajax_personio_run_import', array( $this, 'run_import_via_ajax' ) );
		add_action( 'wp_ajax_personio_get_import_info', array( $this, 'get_import_info' ) );
		add_action( 'wp_ajax_personio_get_import_dialog', array( $this, 'get_import_dialog' ) );

		// use our own hooks.
		add_filter( 'personio_integration_get_shortcode_attributes', array( $this, 'check_filter_type' ) );
		add_filter( 'personio_integration_dashboard_widgets', array( $this, 'add_dashboard_widget' ) );
		add_action( 'personio_integration_import_max_count', array( $this, 'update_import_max_step' ) );
		add_action( 'personio_integration_import_count', array( $this, 'update_import_step' ) );
		add_action( 'personio_integration_import_ended', array( $this, 'import_ended' ) );
		add_filter( 'personio_integration_extend_position_object', array( $this, 'add_pro_extensions' ) );
		add_action( 'personio_integration_import_of_url_starting', array( $this, 'update_import_status' ), 10, 0 );
		add_filter( 'personio_integration_log_categories', array( $this, 'add_log_categories' ) );
		add_filter( 'personio_integration_limit', array( $this, 'set_limit' ), 10, 2 );
		add_filter( 'personio_integration_light_help_tabs', array( $this, 'add_help' ), 20 );
		add_filter( 'personio_integration_light_help_tabs', array( $this, 'add_shortcode_help' ), 60 );
		add_action( 'personio_integration_light_edit_position_box_taxonomy', array( $this, 'show_hint_for_additional_offices' ), 10, 2 );

		// misc hooks.
		add_filter( 'posts_search', array( $this, 'extend_search' ), 10, 2 );
		add_filter( 'wp_sitemaps_posts_entry', array( $this, 'add_sitemap_data' ), 10, 2 );
		add_filter( 'months_dropdown_results', array( $this, 'remove_date_filter' ), 10, 2 );
	}

	/**
	 * Register this custom post type.
	 *
	 * @return void
	 */
	public function register(): void {
		$labels = array(
			'name'              => __( 'Positions', 'personio-integration-light' ),
			'singular_name'     => __( 'Position', 'personio-integration-light' ),
			'menu_name'         => __( 'Positions', 'personio-integration-light' ),
			'parent_item_colon' => __( 'Parent Position', 'personio-integration-light' ),
			'all_items'         => __( 'All Positions', 'personio-integration-light' ),
			'view_item'         => __( 'View Position in frontend', 'personio-integration-light' ),
			'view_items'        => __( 'View Positions', 'personio-integration-light' ),
			'edit_item'         => __( 'View Position in backend', 'personio-integration-light' ),
			'search_items'      => __( 'Search Position', 'personio-integration-light' ),
			'not_found'         => __( 'No positions imported', 'personio-integration-light' ),
		);

		if ( Helper::is_personio_url_set() ) {
			/* translators: %1$s will be replaced by a URL. */
			$labels['not_found'] .= '. ' . sprintf( __( 'Start import <a href="%1$s" class="personio-integration-import-hint">now</a>.', 'personio-integration-light' ), Helper::get_import_url() );
		} else {
			/* translators: %1$s will be replaced by a URL. */
			$labels['not_found'] .= '. ' . sprintf( __( 'Add your Personio URL <a href="%1$s">in the settings</a>.', 'personio-integration-light' ), Helper::get_settings_url() );
		}

		// get slugs.
		$archive_slug = Helper::get_archive_slug();
		$single_slug  = Helper::get_single_slug();

		// set arguments for our own cpt.
		$args = array(
			'label'               => $labels['name'],
			'description'         => '',
			'labels'              => $labels,
			'supports'            => array( '' ),
			'public'              => true,
			'hierarchical'        => false,
			'show_ui'             => Setup::get_instance()->is_completed(),
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'has_archive'         => $archive_slug,
			'can_export'          => false,
			'exclude_from_search' => false,
			'taxonomies'          => array_keys( Taxonomies::get_instance()->get_taxonomies() ),
			'publicly_queryable'  => true,
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

		// register personioId als post-meta to be published in rest-api,
		// which is necessary for our Blocks.
		register_post_meta(
			$this->get_name(),
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
	 * In this way we add fields which are missing through the supports-setting during registering the cpt.
	 * And we format the content, which comes from Personio as array.
	 *
	 * @param WP_REST_Response $data The response object.
	 * @param WP_Post          $post The requested object.
	 *
	 * @return WP_REST_Response
	 * @noinspection PhpUnused
	 */
	public function rest_prepare( WP_REST_Response $data, WP_Post $post ): WP_REST_Response {
		// get positions-object.
		$positions = Positions::get_instance();

		// get the position as object.
		$position_obj = $positions->get_position( $post->ID );

		// get content of the position.
		$content = Templates::get_instance()->get_direct_content_template( $position_obj, array() );

		// generate except.
		$excerpt = $position_obj->get_excerpt();

		// add result to response.
		$data->data['excerpt'] = array(
			'rendered'  => $excerpt,
			'raw'       => '',
			'protected' => false,
		);
		$data->data['title']   = array(
			'rendered' => $position_obj->get_title(),
			'raw'      => $position_obj->get_title(),
		);
		$data->data['content'] = array( 'rendered' => $content );

		$data->data['meta'] = array(
			'personioId' => absint( $position_obj->get_personio_id() ),
		);

		// set response.
		return $data;
	}

	/**
	 * Initialize additional REST API endpoints.
	 *
	 * @return void
	 */
	public function rest_api_init(): void {
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

		// bail if user is not logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// extend our own cpt with methods to change the positions.
		register_rest_route(
			'wp/v2',
			$this->get_name(),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'change_positions' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		// extend our own cpt with delete method to delete all positions in one rush.
		register_rest_route(
			'wp/v2',
			$this->get_name(),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_positions' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	/**
	 * Return list of possible templates for details in REST API.
	 *
	 * @return array<int,array<string,mixed>>
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
		 * @param array<int,array<string,mixed>> $templates The templates.
		 */
		return apply_filters( 'personio_integration_rest_templates_details', $templates );
	}

	/**
	 * Return list of possible templates for job description in REST API.
	 *
	 * @return array<int,array<string,mixed>>
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
		 * @param array<int,array<string,mixed>> $templates The templates.
		 */
		return apply_filters( 'personio_integration_rest_templates_jobdescription', $templates );
	}

	/**
	 * Return list of possible templates for archives in REST API.
	 *
	 * @return array<int,array<string,mixed>>
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
		 * @param array<int,array<string,mixed>> $templates The templates.
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

		// bail if object is not valid.
		if ( ! $position_obj->is_valid() ) {
			return;
		}

		// get active user.
		$user = wp_get_current_user();

		// if no user could be found, check if we are running on WP CLI.
		if ( is_null( $user ) ) { // @phpstan-ignore function.impossibleType
			$username = 'WP CLI';
		} else {
			$username = $user->display_name;
		}

		// log the deletion.
		Log::get_instance()->add( sprintf( 'Position %1$s has been deleted by %2$s.', esc_html( $position_obj->get_personio_id() ), esc_html( $username ) ), 'success', 'import' );
	}

	/**
	 * Add columns to position-table in backend.
	 *
	 * @param array<string|int,string> $columns List of columns.
	 * @return array<string|int,string>
	 */
	public function add_column( array $columns ): array {
		// create new column-array.
		$new_columns = array();

		// add sort column for pro-hint.
		$new_columns['sort'] = __( 'Sorting', 'personio-integration-light' );

		// replace language-column with our own.
		if ( ! empty( $columns[ 'taxonomy-' . WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES ] ) ) {
			unset( $columns[ 'taxonomy-' . WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES ] );
			$columns = Helper::add_array_in_array_on_position( $columns, count( $columns ) - 1, array( WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES => __( 'Languages', 'personio-integration-light' ) ) );
		}

		// add column for PersonioId.
		$new_columns['id'] = __( 'PersonioID', 'personio-integration-light' );

		// remove checkbox.
		unset( $columns['cb'] );

		// merge the lists and return them.
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
		global $wp;

		// get position as object.
		$position_obj = Positions::get_instance()->get_position( $post_id );

		// bail if position is not valid.
		if ( ! $position_obj->is_valid() ) {
			return;
		}

		// show ID-column.
		if ( 'id' === $column ) {
			echo esc_html( $position_obj->get_personio_id() );
		}

		// show sort column with pro-hint.
		if ( 'sort' === $column ) {
			echo '<span class="pro-marker">' . esc_html__( 'Only in Pro', 'personio-integration-light' ) . '</span>';
		}

		// show languages with its names.
		if ( WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES === $column ) {
			// get main-url.
			$url = add_query_arg( filter_input( INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_FULL_SPECIAL_CHARS ), '', home_url( $wp->request ) );

			// get languages in the project.
			$languages = Languages::get_instance()->get_languages();

			// loop through the languages of this position and show each of them.
			foreach ( explode( ', ', $position_obj->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES, 'name' ) ) as $index => $language_name ) {
				// bail if languages is not available.
				if ( empty( $languages[ $language_name ] ) ) {
					continue;
				}

				// create filter-url for this language.
				$lang_url = add_query_arg( array( 'language' => $language_name ), $url );

				// show comma-separator.
				if ( $index > 0 ) {
					echo ', ';
				}

				// show link and name of the language.
				echo '<a href="' . esc_url( $lang_url ) . '">' . esc_html( $languages[ $language_name ] ) . '</a>';
			}
		}
	}

	/**
	 * Remove any bulk actions for our own cpt.
	 *
	 * @return array<string>
	 */
	public function remove_bulk_actions(): array {
		return array();
	}

	/**
	 * Remove all actions except "view" and "edit" for our own cpt.
	 *
	 * @param array<string,string> $actions List of actions.
	 * @param WP_Post              $post Object of the post.
	 * @return array<string,string>
	 */
	public function remove_actions( array $actions, WP_Post $post ): array {
		// bail if this is not our cpt.
		if ( $this->get_name() !== get_post_type() ) {
			return $actions;
		}

		$new_actions = array();
		if ( ! empty( $actions['view'] ) ) {
			$new_actions = array(
				'view' => $actions['view'],
			);
		}

		// get edit-URL.
		$edit_url = get_edit_post_link( $post->ID );

		// add the edit-URL to the action-list if it is set.
		if ( ! is_null( $edit_url ) ) {
			$new_actions['edit'] = '<a href="' . esc_url( $edit_url ) . '">' . __( 'Edit', 'personio-integration-light' ) . '</a>';
		}

		// return resulting list.
		return $new_actions;
	}

	/**
	 * Add filter for our own cpt on lists in admin.
	 *
	 * @return void
	 */
	public function add_filter(): void {
		// get requested post type.
		$post_type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// bail if no post type is given.
		if ( is_null( $post_type ) ) {
			return;
		}

		// bail if this is not our cpt.
		if ( $this->get_name() !== $post_type ) {
			return;
		}

		// add filter for each taxonomy.
		foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
			// show only taxonomies which are visible in filter.
			if ( 1 !== absint( $taxonomy['useInFilter'] ) ) {
				continue;
			}

			// get the taxonomy as object.
			$taxonomy = get_taxonomy( $taxonomy_name );

			// bail if taxonomy could not be loaded.
			if ( ! $taxonomy instanceof WP_Taxonomy ) {
				continue;
			}

			// get its terms.
			$terms = get_terms( array( 'taxonomy' => $taxonomy_name ) );

			// list terms only if they are available.
			if ( is_array( $terms ) && ! empty( $terms ) ) {
				?>
				<!--suppress HtmlFormInputWithoutLabel -->
				<select name="admin_filter_<?php echo esc_attr( $taxonomy_name ); ?>">
					<option value="0"><?php echo esc_html( $taxonomy->label ); ?></option>
					<?php
					foreach ( $terms as $term ) {
						?>
						<option value="<?php echo absint( $term->term_id ); ?>"<?php echo ( absint( filter_input( INPUT_GET, 'admin_filter_' . $taxonomy_name, FILTER_SANITIZE_NUMBER_INT ) ) === $term->term_id ) ? ' selected="selected"' : ''; ?>><?php echo esc_html( $term->name ); ?></option>
						<?php
					}
					?>
				</select>
				<?php
			}
		}
	}

	/**
	 * Use filter in admin on edit-page for filtering the cpt-items.
	 *
	 * @param WP_Query $query The WP_Query-object.
	 *
	 * @return void
	 */
	public function use_filter( WP_Query $query ): void {
		global $pagenow;

		// get requested post type.
		$post_type = sanitize_text_field( wp_unslash( filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) );

		// bail if no post type is given.
		if ( empty( $post_type ) ) {
			return;
		}

		// bail if this is not our cpt.
		if ( $this->get_name() !== $post_type ) {
			return;
		}

		// bail if page is not edit.php.
		if ( 'edit.php' !== $pagenow ) {
			return;
		}

		// add filter for each taxonomy.
		$tax_query = array();
		foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
			if ( ( 1 === absint( $taxonomy['useInFilter'] ) ) && absint( wp_unslash( filter_input( INPUT_GET, 'admin_filter_' . $taxonomy_name, FILTER_SANITIZE_NUMBER_INT ) ) ) > 0 ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy_name,
					'field'    => 'term_id',
					'terms'    => absint( wp_unslash( filter_input( INPUT_GET, 'admin_filter_' . $taxonomy_name, FILTER_SANITIZE_NUMBER_INT ) ) ),
				);
			}
		}

		// bail if no query is set.
		if ( empty( $tax_query ) ) {
			return;
		}

		// set query depending on size.
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

	/**
	 * Hide cpt filter-view. Simply return empty array.
	 *
	 * @return array<string>
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
		// bail if we are not in wp-admin.
		if ( ! is_admin() ) {
			return $query;
		}

		// set to ignore the author.
		if ( ! empty( $query->query_vars['post_type'] ) && $this->get_name() === $query->query_vars['post_type'] ) {
			$query->set( 'author', 0 );
		}
		return $query;
	}

	/**
	 * Add Box with hints for editing.
	 * Add Open Graph Meta-box für edit-page of positions.
	 *
	 * @param string  $post_type The requested post type.
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function add_meta_boxes( string $post_type, WP_Post $post ): void {
		// get the requested position as object.
		$position_obj = Positions::get_instance()->get_position( $post->ID );

		add_meta_box(
			$this->get_name() . '-edit-hints',
			__( 'About this page', 'personio-integration-light' ),
			array( $this, 'get_meta_box_data_hint' ),
			$this->get_name()
		);

		add_meta_box(
			$this->get_name() . '-id',
			__( 'PersonioID', 'personio-integration-light' ),
			array( $this, 'get_meta_box_for_personio_id' ),
			$this->get_name()
		);

		add_meta_box(
			$this->get_name() . '-title',
			__( 'Title', 'personio-integration-light' ) . Personio_Accounts::get_instance()->get_personio_edit_link( $position_obj ),
			array( $this, 'get_meta_box_for_title' ),
			$this->get_name()
		);

		add_meta_box(
			$this->get_name() . '-text',
			__( 'Description', 'personio-integration-light' ) . Personio_Accounts::get_instance()->get_personio_edit_link( $position_obj ),
			array( $this, 'get_meta_box_for_description' ),
			$this->get_name()
		);

		add_meta_box(
			$this->get_name() . '-personio',
			__( 'Personio Account', 'personio-integration-light' ),
			array( $this, 'get_meta_box_for_personio' ),
			$this->get_name()
		);

		add_meta_box(
			$this->get_name() . '-pro-form',
			__( 'Application form', 'personio-integration-light' ),
			array( $this, 'get_meta_box_for_application_form' ),
			$this->get_name()
		);

		// get taxonomies as object.
		$taxonomies_obj = Taxonomies::get_instance();

		// get the taxonomy settings.
		$taxonomies_settings = $taxonomies_obj->get_taxonomies();

		// add meta box for each supported taxonomy.
		foreach ( $taxonomies_obj->get_taxonomies() as $taxonomy_name => $settings ) {
			// get label.
			$labels = $taxonomies_obj->get_taxonomy_label( $taxonomy_name );

			// get the taxonomy settings.
			$taxonomy_settings = $taxonomies_settings[ $taxonomy_name ];

			// add changeable hint on title if enabled.
			$changeable_hint = '';
			if ( isset( $taxonomy_settings['changeable'] ) ) {
				// create dialog.
				$dialog = array(
					'title'   => __( 'Texts changeable', 'personio-integration-light' ),
					'texts'   => array(
						'<p><strong>' . __( 'The texts of this taxonomy could be changed.', 'personio-integration-light' ) . '</strong></p>',
						/* translators: %1$s will be replaced by the plugin URL for Loco Translate. */
						'<p>' . sprintf( __( 'They are in the language file of the plugin and can be changed with any plugin that supports their editing,<br>e.g. with <a href="%1$s" target="_blank">Loco Translate (opens new window)</a> or <a href="%2$s" target="_blank">Say What (opens new window)</a>.', 'personio-integration-light' ), esc_url( Loco::get_instance()->get_plugin_url() ), esc_url( SayWhat::get_instance()->get_plugin_url() ) ) . '</p>',
					),
					'buttons' => array(
						array(
							'action'  => 'closeDialog();',
							'variant' => 'primary',
							'text'    => __( 'OK', 'personio-integration-light' ),
						),
					),
				);

				// set the URL.
				$url = '#';

				/**
				 * Adjust the dialog for hint for possibility to translate terms.
				 *
				 * @since 5.0.0 Available since 5.0.0.
				 * @param array<string,mixed> $dialog The dialog to change.
				 * @param string $taxonomy_name The taxonomy name.
				 */
				$dialog = apply_filters( 'personio_integration_light_term_translate_hint', $dialog, $taxonomy_name );

				// add link.
				$changeable_hint = '<a href="' . esc_url( $url ) . '" class="easy-dialog-for-wordpress" data-dialog="' . esc_attr( Helper::get_json( $dialog ) ) . '"><span class="dashicons dashicons-translation"></span></a>';
			}

			// add edit link.
			$changeable_hint .= Personio_Accounts::get_instance()->get_personio_edit_link( $position_obj );

			// add a box for single taxonomy.
			add_meta_box(
				$this->get_name() . '-taxonomy-' . $taxonomy_name,
				$labels['name'] . $changeable_hint,
				array( $this, 'get_meta_box_for_taxonomy' ),
				$this->get_name(),
				'side'
			);
		}

		// add meta box with Pro-hint for subcompany-field which is only in Pro.
		add_meta_box(
			$this->get_name() . '-taxonomy-subcompany',
			__( 'Subcompany', 'personio-integration-light' ),
			array( $this, 'get_meta_box_for_pro_taxonomy' ),
			$this->get_name(),
			'side'
		);
	}

	/**
	 * Remove all meta boxes which are not part of this post type.
	 *
	 * @param string $post_type The used post type.
	 *
	 * @return void
	 */
	public function remove_third_party_meta_boxes( string $post_type ): void {
		global $wp_meta_boxes;

		// bail if this is not our own cpt.
		if ( $this->get_name() !== $post_type ) {
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
		 *
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_position_prevent_meta_box_remove', $false ) ) {
			return;
		}

		/**
		 * Loop through the boxes for this cpt and remove all which do not belong to our plugin.
		 */
		foreach ( $wp_meta_boxes[ $this->get_name() ] as $context => $priority_boxes ) {
			foreach ( $priority_boxes as $boxes ) {
				foreach ( $boxes as $box ) {
					// bail of box is not an array.
					if ( ! is_array( $box ) ) {
						continue;
					}

					/**
					 * Decide if we should not remove the support for this meta-box.
					 *
					 * @since 3.0.0 Available since 3.0.0.
					 *
					 * @param bool $false Return true to ignore this box.
					 * @param array $box Settings of the meta-box.
					 *
					 * @noinspection PhpConditionAlreadyCheckedInspection
					 */
					if ( apply_filters( 'personio_integration_do_not_hide_meta_box', $false, $box ) ) {
						continue;
					}

					// check if box is not from our own plugin.
					if ( false === str_contains( $box['id'], $this->get_name() ) ) {
						remove_meta_box( $box['id'], $this->get_name(), $context );
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
		// bail if no post is requested.
		if ( 0 === $post->ID ) {
			return;
		}

		// get the position.
		$position_obj = Positions::get_instance()->get_position( $post->ID );

		// bail if position is not valid.
		if ( ! $position_obj->is_valid() ) {
			return;
		}

		// get the edit URL.
		$url = Personio_Accounts::get_instance()->get_edit_link_on_personio( $position_obj );

		// use main Personio URL if no edit URL could be loaded.
		if ( empty( $url ) ) {
			$url = Personio_Accounts::get_instance()->get_login_url();
		}

		// show hint.
		/* translators: %1$s will be replaced by the URL for Personio, %2$s will be replaced with the URL for the Personio account. */
		printf( wp_kses_post( __( 'These are the data of your open position <i>%1$s</i> we imported from Personio. Please edit the position data in your <a href="%2$s" target="_blank">Personio account (opens new window)</a>.', 'personio-integration-light' ) ), esc_html( $position_obj->get_title() ), esc_url( $url ) );
	}

	/**
	 * Show personioId in meta box.
	 *
	 * @param WP_Post $post Object of the post.
	 * @return void
	 */
	public function get_meta_box_for_personio_id( WP_Post $post ): void {
		// get the position as object.
		$position_obj = Positions::get_instance()->get_position( $post->ID );

		// show the Personio ID of this position.
		echo wp_kses_post( $position_obj->get_personio_id() );

		/**
		 * Run additional tasks.
		 *
		 * @since 4.1.0 Available since 4.1.0.
		 * @param Position $position_obj The position as object.
		 */
		do_action( 'personio_integration_light_edit_position_box_personio_id', $position_obj );
	}

	/**
	 * Show title of position in meta box.
	 *
	 * @param WP_Post $post Object of the post.
	 * @return void
	 */
	public function get_meta_box_for_title( WP_Post $post ): void {
		// get the position as object.
		$position_obj = Positions::get_instance()->get_position( $post->ID );

		// show the title of this position.
		echo wp_kses_post( $position_obj->get_title() );

		/**
		 * Run additional tasks.
		 *
		 * @since 4.1.0 Available since 4.1.0.
		 * @param Position $position_obj The position as object.
		 */
		do_action( 'personio_integration_light_edit_position_box_title', $position_obj );
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
		// get the requested position as object.
		$position_obj = Positions::get_instance()->get_position( $post->ID );

		// get the edit URL.
		$url = Personio_Accounts::get_instance()->get_edit_link_on_personio( $position_obj );

		// use main Personio URL if no edit URL could be loaded.
		if ( empty( $url ) ) {
			$url = Personio_Accounts::get_instance()->get_login_url();
		}

		// get the content array.
		$content_array = $position_obj->get_content();

		// bail if array is empty and show hint.
		if ( empty( $content_array ) ) {
			/* translators: %1$s will be replaced by a URL. */
			echo '<p class="personio-pro-hint">' . wp_kses_post( sprintf( __( 'No description available for this position. Please add it <a href="%1$s" target="_blank">in your Personio account</a>.', 'personio-integration-light' ), esc_url( $url ) ) ) . '</p>';
			return;
		}

		// bail if jobdescription entry is empty and show hint.
		if ( empty( $content_array['jobDescription'] ) ) {
			/* translators: %1$s will be replaced by a URL. */
			echo '<p class="personio-pro-hint">' . wp_kses_post( sprintf( __( 'No description available for this position. Please add it <a href="%1$s" target="_blank">in your Personio account</a>.', 'personio-integration-light' ), esc_url( $url ) ) ) . '</p>';
			return;
		}

		// show the description.
		the_content();

		/**
		 * Run additional tasks.
		 *
		 * @since 4.1.0 Available since 4.1.0.
		 * @param WP_post $post The post as object.
		 */
		do_action( 'personio_integration_light_edit_position_box_content', $post );
	}

	/**
	 * Show link to Personio edit page if login URL is given.
	 *
	 * @param WP_Post $post Object of the post.
	 *
	 * @return void
	 */
	public function get_meta_box_for_personio( WP_Post $post ): void {
		// get the requested position as object.
		$position_obj = Positions::get_instance()->get_position( $post->ID );

		// get the Personio URL.
		$personio_url = $position_obj->get_personio_account()->get_url();

		// show used Personio URL, if set.
		if ( ! empty( $personio_url ) ) {
			echo '<p>' . esc_html__( 'Used Personio URL', 'personio-integration-light' ) . ': <a href="' . esc_url( $personio_url ) . '" target="_blank">' . esc_url( $personio_url ) . '</a></p>';
		}

		// get the configured Personio Login URL.
		$personio_edit_url = Personio_Accounts::get_instance()->get_edit_link_on_personio( $position_obj );

		// bail if no login URL is given.
		if ( empty( $personio_edit_url ) ) {
			// show hint.
			/* translators: %1$s will be replaced by a URL. */
			echo '<p>' . wp_kses_post( sprintf( __( 'Please add your Personio Login URL on <a href="%1$s">the settings page</a> to get a link to the edit-page of this position in Personio.', 'personio-integration-light' ), Helper::get_settings_url() ) ) . '</p>';

			// do nothing more.
			return;
		}

		// show the edit link.
		/* translators: %1$s will be replaced by a URL. */
		echo '<p>' . wp_kses_post( sprintf( __( '<a href="%1$s" target="_blank">Edit (opens new window)</a> this position on Personio.', 'personio-integration-light' ), esc_url( $personio_edit_url ) ) ) . '</p>';
	}

	/**
	 * Show any taxonomy of position in their own meta box.
	 *
	 * @param WP_Post              $post Object of the post.
	 * @param array<string,string> $attr The attributes.
	 *
	 * @return void
	 **/
	public function get_meta_box_for_taxonomy( WP_Post $post, array $attr ): void {
		// get the requested position as object.
		$position_obj = Positions::get_instance()->get_position( $post->ID );

		// get the requested taxonomy from the box ID as string.
		$taxonomy_name = str_replace( $this->get_name() . '-taxonomy-', '', $attr['id'] );

		// get the terms of this taxonomy on this position.
		$terms = $position_obj->get_terms_by_field( $taxonomy_name );

		// if no terms could be loaded, show hint.
		if ( empty( $terms ) ) {
			echo '<i>' . esc_html__( 'No data received.', 'personio-integration-light' ) . '</i>';
		} else {
			// loop through the terms and add them to the list.
			foreach ( $terms as $index => $term ) {
				if ( $index > 0 ) {
					echo ', ';
				}

				// get label.
				$label = $term->name;

				// special case for the language taxonomy.
				if ( WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES === $taxonomy_name ) {
					// get languages in the project.
					$languages = Languages::get_instance()->get_languages();
					// get the label from the language list.
					$label = $languages[ $term->name ];
				}

				// create filter url.
				$filter_url = add_query_arg(
					array(
						's'                              => '',
						'post_type'                      => $this->get_name(),
						'admin_filter_' . $taxonomy_name => $term->term_id,
						'paged'                          => 1,
					),
					admin_url() . 'edit.php'
				);

				// output.
				echo '<a href="' . esc_url( $filter_url ) . '">' . esc_html( $label ) . '</a> (' . absint( $term->count ) . ')';
			}
		}

		/**
		 * Run additional tasks.
		 *
		 * @since 4.1.0 Available since 4.1.0.
		 * @param Position $position_obj The position as object.
		 * @param array $attr Attributes used for this meta box.
		 */
		do_action( 'personio_integration_light_edit_position_box_taxonomy', $position_obj, $attr );
	}

	/**
	 * Show any taxonomy of position in their own meta box.
	 *
	 * @param WP_Post              $post Object of the post.
	 * @param array<string,string> $attr The attributes.
	 *
	 * @return void
	 * @noinspection PhpUnused
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function get_meta_box_for_pro_taxonomy( WP_Post $post, array $attr ): void {
		// bail if no title is set.
		if ( empty( $attr['title'] ) ) {
			return;
		}

		// show the hint.
		/* translators: %1$s will be replaced with the plugin pro-name */
		echo wp_kses_post( Admin::get_instance()->get_pro_hint( __( 'Use this taxonomy with %1$s.', 'personio-integration-light' ) ) );
	}

	/**
	 * Through a bug in WordPress we must remove the "create"-option manually.
	 *
	 * @return void
	 */
	public function disable_create_options(): void {
		global $pagenow, $typenow;

		// bail if requested list is not our cpt.
		if ( $this->get_name() !== $typenow ) {
			return;
		}

		// bail if we are not in wp-admin.
		if ( ! is_admin() ) {
			return;
		}

		// run further checks and set the edit url.
		if ( ! empty( $typenow ) && ! empty( $pagenow ) && 'edit.php' === $pagenow && ! empty( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) && stripos( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'edit.php' ) && stripos( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'post_type=' . $typenow ) && ! stripos( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'page' ) ) {
			$pagenow = 'edit-' . $typenow . '.php';
		}
	}

	/**
	 * Convert attributes for shortcodes.
	 *
	 * @param array<string,string> $attributes List of attributes.
	 *
	 * @return array<string,mixed>
	 * @noinspection PhpUnused
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
			'excerpt_template'        => 'excerpt_template',
			'jobdescription_template' => 'jobdescription_template',
		);
		return Helper::get_shortcode_attributes( $attribute_defaults, $attribute_settings, $attributes );
	}

	/**
	 * Return attribute defaults for shortcode in single-view.
	 *
	 * @return array<string,mixed>
	 */
	private function get_single_shortcode_attributes_defaults(): array {
		$default_values = array(
			'personioid'              => 0,
			'lang'                    => Languages::get_instance()->get_main_language(),
			'template'                => '',
			'templates'               => implode( ',', get_option( 'personioIntegrationTemplateContentDefaults' ) ),
			'excerpt_template'        => get_option( 'personioIntegrationTemplateDetailsExcerptsTemplate' ),
			'jobdescription_template' => get_option( 'personioIntegrationTemplateJobDescription' ),
			'excerpt'                 => implode( ',', get_option( 'personioIntegrationTemplateExcerptDetail' ) ),
			'donotlink'               => 1,
			'styles'                  => '',
			'classes'                 => '',
		);

		/**
		 * Filter the attribute-defaults.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array<string,mixed> $default_values The list of default values for each attribute used to display positions in frontend.
		 */
		return apply_filters( 'personio_integration_position_attribute_defaults', $default_values );
	}

	/**
	 * Check for allowed filter-type.
	 *
	 * @param array<string,array<string,mixed>> $settings List of settings for a shortcode with 3 parts: defaults, settings & attributes.
	 * @return array<string,array<string,mixed>>
	 */
	public function check_filter_type( array $settings ): array {
		if ( ! empty( $settings['attributes']['filtertype'] ) ) {
			$filter_types = Helper::get_filter_types();
			if ( empty( $filter_types[ $settings['attributes']['filtertype'] ] ) ) {
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
		$term = (string) $wp_query->query_vars['s'];

		// create and return changed statement.
		return ' AND (
        (
            1 = 1 ' . $search . '
        )
        OR (
            ' . $wpdb->posts . ".post_type = '" . $this->get_name() . "'
            AND EXISTS(
                SELECT * FROM " . $wpdb->terms . '
                INNER JOIN ' . $wpdb->term_taxonomy . '
                    ON ' . $wpdb->term_taxonomy . '.term_id = ' . $wpdb->terms . '.term_id
                INNER JOIN ' . $wpdb->term_relationships . '
                    ON ' . $wpdb->term_relationships . '.term_taxonomy_id = ' . $wpdb->term_taxonomy . ".term_taxonomy_id
                WHERE taxonomy = 'personioKeywords'
                    AND object_id = " . $wpdb->posts . '.ID
                    AND ' . $wpdb->terms . ".name LIKE '%" . (string) esc_sql( $term ) . "%'
            )
        )
    )";
	}

	/**
	 * Change SitemapXML-data for positions.
	 *
	 * Add last modification date and priority.
	 *
	 * @param array<string,mixed> $entry The entry-data.
	 * @param WP_Post             $post The post-object.
	 *
	 * @return array<string,mixed>
	 */
	public function add_sitemap_data( array $entry, WP_Post $post ): array {
		// bail if this is not our cpt.
		if ( $this->get_name() !== get_post_type( $post ) ) {
			return $entry;
		}

		// get the position as object.
		$position = Positions::get_instance()->get_position( $post->ID );

		// get the date.
		$entry['lastmod'] = gmdate( 'Y-m-d', absint( $position->get_created_at() ) );

		// set the priority for sitemap.
		$entry['priority'] = 0.8;

		/**
		 * Filter the data for the sitemap-entry for single position.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array<string,mixed> $entry List of data for the sitemap.xml of this single position.
		 * @param Position $position The position as object.
		 */
		return apply_filters( 'personio_integration_sitemap_entry', $entry, $position );
	}

	/**
	 * Add dashboard-widget to show list of positions.
	 *
	 * @param array<int,array<string,mixed>> $dashboard_widgets List of widgets on dashboard.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public function add_dashboard_widget( array $dashboard_widgets ): array {
		// bail if setup has not been run.
		if ( ! Setup::get_instance()->is_completed() ) {
			return $dashboard_widgets;
		}

		$dashboard_widgets[] = array(
			'id'       => 'dashboard_personio_integration_positions',
			'label'    => __( 'Positions imported from Personio', 'personio-integration-light' ),
			'callback' => array( $this, 'get_dashboard_widget_content' ),
		);

		// return resulting list.
		return $dashboard_widgets;
	}

	/**
	 * Output the contents of the dashboard widget
	 *
	 * @param string               $post The post as object.
	 * @param array<string,string> $callback_args List of arguments.
	 */
	public function get_dashboard_widget_content( string $post, array $callback_args ): void {
		// bail if we miss data.
		if ( ! ( empty( $post ) && ! empty( $callback_args ) ) ) {
			return;
		}

		// get the position count.
		$position_count = absint( get_option( 'personioIntegrationPositionCount', 0 ) );

		// get the positions object.
		$positions_obj = Positions::get_instance();

		// show wrapper.
		?>
		<div class="community-events">
		<?php

		// show results.
		if ( 0 === $position_count ) {
			echo '<p>' . esc_html__( 'Actually there are no positions imported from Personio.', 'personio-integration-light' ) . '</p>';
		} else {
			/**
			 * Run additional tasks before the positions have been loaded.
			 *
			 * @since 5.0.0 Available since 5.0.0.
			 */
			do_action( 'personio_integration_light_dashboard_widget_pre_query' );

			// get list of the newest 3 positions.
			$positions_list = $positions_obj->get_positions(
				3,
				array(
					'sortby' => 'date',
					'sort'   => 'DESC',
				)
			);

			/**
			 * Run additional tasks after the positions have been loaded.
			 *
			 * @since 5.0.0 Available since 5.0.0.
			 */
			do_action( 'personio_integration_light_dashboard_widget_post_query' );

			// show the list.
			?>
			<ul class="personio-positions activity-block">
			<?php
			foreach ( $positions_list as $position ) {
				?>
				<li><a href="<?php echo esc_url( $position->get_link() ); ?>"><?php echo esc_html( $position->get_title() ); ?></a></li>
				<?php
			}
			?>
			</ul>
			<p><a href="<?php echo esc_url( $this->get_link() ); ?>">
			<?php
			/* translators: %1$d will be replaced by the count of positions */
			printf( esc_html__( 'Show all %1$d positions', 'personio-integration-light' ), absint( $position_count ) );
			?>
				</a></p>
			<?php
		}

		// show end of wrapper.
		?>
		</div>
		<?php
	}

	/**
	 * Return whether a single page of our own custom post type is called in frontend.
	 *
	 * @return bool
	 */
	public function is_single_page_called(): bool {
		// bail if single is not called.
		if ( ! is_single() ) {
			return false;
		}

		// get the queried object.
		$object = get_queried_object();

		// return true if object iss from our cpt.
		return ( $object instanceof WP_Post && $this->get_name() === $object->post_type );
	}

	/**
	 * Delete ALL positions via REST API request, WP CLI or direct call.
	 *
	 * This is the main function to deletion every position.
	 *
	 * Terms the positions have been used are not deleted in this step.
	 *
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function delete_positions(): void {
		// bail if deletion is actual running.
		if ( absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING ) ) > 0 ) {
			return;
		}

		// bail if import is running.
		if ( absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) ) > 0 ) {
			return;
		}

		// reset info values.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_COUNT, 0 );

		// mark as running.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() );

		// set label.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_STATUS, __( 'Deleting of positions is running ..', 'personio-integration-light' ) );

		/**
		 * Run custom actions before deleting of all positions is running.
		 *
		 * @since 3.0.0 Available since release 3.0.0.
		 */
		do_action( 'personio_integration_deletion_starting' );

		// get positions.
		$positions      = Positions::get_instance()->get_positions();
		$position_count = count( $positions );

		// get Personio URLs and languages.
		$personio_urls = Personio_Accounts::get_instance()->get_personio_urls();
		$languages     = Languages::get_instance()->get_languages();

		// set max count.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_MAX, $position_count + ( count( $personio_urls ) * count( $languages ) ) );

		// show cli hint.
		$progress = Helper::is_cli() ? \WP_CLI\Utils\make_progress_bar( 'Deleting all local positions', $position_count ) : false;

		// loop through all positions to delete them.
		foreach ( $positions as $position ) {
			// delete it.
			wp_delete_post( $position->get_id(), true );

			// show progress.
			$progress ? $progress->tick() : false;

			// update counter.
			update_option( WP_PERSONIO_INTEGRATION_DELETE_COUNT, absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_COUNT ) ) + 1 );
		}
		// finalize progress.
		$progress ? $progress->finish() : false;

		// delete position count.
		delete_option( 'personioIntegrationPositionCount' );

		// set label.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_STATUS, __( 'Cleanup database ..', 'personio-integration-light' ) );

		// delete options regarding the import.
		foreach ( $personio_urls as $personio_url ) {
			$personio_obj = new Personio( $personio_url );
			foreach ( $languages as $language_name => $lang ) {
				$personio_obj->remove_timestamp( $language_name );
				$personio_obj->remove_md5( $language_name );

				// update counter.
				update_option( WP_PERSONIO_INTEGRATION_DELETE_COUNT, absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_COUNT ) ) + 1 );
			}

			// update counter.
			update_option( WP_PERSONIO_INTEGRATION_DELETE_COUNT, absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_COUNT ) ) + 1 );
		}

		// output success-message.
		Helper::is_cli() ? \WP_CLI::success( $position_count . ' positions from local database deleted.' ) : false;

		// get current user for logging.
		$user = wp_get_current_user();

		if ( is_null( $user ) ) { // @phpstan-ignore function.impossibleType
			$username = 'WP CLI';
		} else {
			$username = $user->display_name;
		}

		// log this event.
		Log::get_instance()->add( sprintf( 'Positions has been deleted by %1$s.', esc_html( $username ) ), 'success', 'import' );

		/**
		 * Run custom actions after deletion of all positions has been done.
		 *
		 * @since 3.0.0 Available since release 3.0.0.
		 */
		do_action( 'personio_integration_deletion_ended' );

		// update label.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_STATUS, __( 'Deleting of positions has been run.', 'personio-integration-light' ) );

		// mark as not running.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, 0 );
	}

	/**
	 * Change settings on single position.
	 *
	 * @param WP_REST_Request $data The data of the REST request.
	 *
	 * @return void
	 */
	public function change_positions( WP_REST_Request $data ): void {
		$params = $data->get_params();

		// bail if no params are set.
		if ( empty( $params ) ) {
			return;
		}

		// bail if no task is set.
		if ( empty( $params['task'] ) ) {
			return;
		}

		// bail if no post id is set.
		if ( empty( $params['post'] ) ) {
			return;
		}

		/**
		 * Run the individual task.
		 */
		do_action( 'personio_integration_light_endpoint_task', $params );

		// answer with error.
		wp_send_json( array( 'success' => false ) );
	}

	/**
	 * Return info about deletion progress.
	 *
	 * @return void
	 */
	public function get_deletion_info(): void {
		// return actual and max count of import steps.
		wp_send_json(
			array(
				absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_COUNT, 0 ) ),
				absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_MAX ) ),
				absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, 0 ) ),
				wp_kses_post( get_option( WP_PERSONIO_INTEGRATION_DELETE_STATUS, '' ) ),
				wp_json_encode( get_option( WP_PERSONIO_INTEGRATION_DELETE_ERRORS, array() ) ),
			)
		);
	}

	/**
	 * Start Import via AJAX.
	 *
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function run_import_via_ajax(): void {
		// check nonce.
		check_ajax_referer( 'personio-run-import', 'nonce' );

		// get the import object.
		$imports_obj = Imports::get_instance()->get_import_extension();

		// bail if no extension is enabled.
		if ( ! $imports_obj ) {
			// log this.
			/* translators: %1$s will be replaced by a URL. */
			Log::get_instance()->add( sprintf( __( 'No import extension enabled! Go to <a href="%1$s">the extensions</a> and enable the import type you want to use.', 'personio-integration-light' ), Extensions::get_instance()->get_link( 'imports' ) ), 'error', 'import' );

			// add this as error.
			update_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, array( __( 'No import extension enabled!', 'personio-integration-light' ) ) );

			// return error message.
			wp_send_json_error();
		}

		// run import.
		$imports_obj->run();

		// return success message.
		wp_send_json_success();
	}

	/**
	 * Return state of the actual running import.
	 *
	 * Format: Step;MaxSteps;Running;StatusLabel;Errors
	 *
	 * @return void
	 */
	public function get_import_info(): void {
		// check nonce.
		check_ajax_referer( 'personio-get-import-info', 'nonce' );

		// return actual and max count of import steps.
		wp_send_json(
			array(
				absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_COUNT, 0 ) ),
				absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_MAX ) ),
				absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) ),
				wp_kses_post( get_option( WP_PERSONIO_INTEGRATION_IMPORT_STATUS, '' ) ),
				wp_json_encode( get_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, array() ) ),
			)
		);
	}

	/**
	 * Return the import dialog where user could decide to start the import of positions.
	 *
	 * @return void
	 */
	public function get_import_dialog(): void {
		// check nonce.
		check_ajax_referer( 'personio-import-dialog', 'nonce' );

		// define dialog.
		$dialog = array(
			'detail' => array(
				'title'   => __( 'Run import', 'personio-integration-light' ),
				'texts'   => array(
					/* translators: %1$s will be replaced by the Personio URL */
					'<p>' . sprintf( __( '<strong>Do you really want to import open positions from<br>%1$s ?</strong>', 'personio-integration-light' ), '<a href="' . esc_url( Helper::get_personio_url() ) . '" target="_blank">' . esc_url( Helper::get_personio_url() ) . '</a>' ) . '</p>',
				),
				'buttons' => array(
					array(
						'action'  => 'personio_start_import();',
						'variant' => 'primary',
						'text'    => __( 'Yes', 'personio-integration-light' ),
					),
					array(
						'action'  => 'closeDialog();',
						'variant' => 'secondary',
						'text'    => __( 'No', 'personio-integration-light' ),
					),
				),
			),
		);

		/**
		 * Filter the initial import dialog.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $dialog The dialog to send.
		 */
		$dialog = apply_filters( 'personio_integration_import_dialog', $dialog );

		// send response as JSON.
		wp_send_json( $dialog );
	}

	/**
	 * Update max count.
	 *
	 * @param int $max_count The value to add.
	 *
	 * @return void
	 */
	public function update_import_max_step( int $max_count ): void {
		update_option( WP_PERSONIO_INTEGRATION_OPTION_MAX, absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_MAX ) ) + $max_count );
	}

	/**
	 * Update count.
	 *
	 * @param int $count The value to add.
	 *
	 * @return void
	 */
	public function update_import_step( int $count ): void {
		update_option( WP_PERSONIO_INTEGRATION_OPTION_COUNT, absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_COUNT ) ) + $count );
	}

	/**
	 * Run this if import of positions has been ended.
	 *
	 * @return void
	 */
	public function import_ended(): void {
		// save actual position count.
		update_option( 'personioIntegrationPositionCount', Positions::get_instance()->get_positions_count() );
	}

	/**
	 * Return list of pro-extension we show as demo in the extensions table.
	 *
	 * @return array<int,array<string,string>>
	 */
	private function get_pro_extensions(): array {
		$false = false;
		/**
		 * Hide hint for Pro-plugin.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Set true to hide the hint.
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			return array();
		}

		/**
		 * Hide the extensions for pro-version if Pro is installed but license not entered.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Set true to hide the extensions.
		 *
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_hide_pro_extensions', $false ) ) {
			return array();
		}

		return array(
			array(
				'name'        => 'personio_forms',
				'label'       => __( 'Application forms', 'personio-integration-light' ),
				'description' => __( 'Use application forms directly on your website. Use our own form handler, WPForms, Contact Form 7 or Ninja Forms.', 'personio-integration-light' ),
				'category'    => 'forms',
			),
			array(
				'name'        => 'feature_image',
				'label'       => __( 'Feature Image', 'personio-integration-light' ),
				'description' => __( 'Add a feature image to each position on your website. Or one image for all positions.', 'personio-integration-light' ),
				'category'    => 'positions',
			),
			array(
				'name'        => 'files',
				'label'       => __( 'Files', 'personio-integration-light' ),
				'description' => __( 'Add an unlimited list of files to each position on your website.', 'personio-integration-light' ),
				'category'    => 'positions',
			),
			array(
				'name'        => 'multilingual',
				'label'       => __( 'Multilingual', 'personio-integration-light' ),
				'description' => __( 'Use Polylang, TranslatePress or WPML for optimal multilingual presentation of your positions.', 'personio-integration-light' ),
				'category'    => 'multilingual',
			),
			array(
				'name'        => 'personio_accounts',
				'label'       => __( 'Multiple Personio Accounts', 'personio-integration-light' ),
				'description' => __( 'Use positions from multiple Personio accounts in your website.', 'personio-integration-light' ),
				'category'    => 'positions',
			),
			array(
				'name'        => 'social_media',
				'label'       => __( 'Social Media', 'personio-integration-light' ),
				'description' => __( 'Add features to your positions to advertise them optimally on social media platforms such as Fediverse (e.g. Mastodon), X (aka twitter), WhatsApp, Telegram or Facebook. Google Jobs is also supported.', 'personio-integration-light' ),
				'category'    => 'seo',
			),
			array(
				'name'        => 'tracking',
				'label'       => __( 'Tracking', 'personio-integration-light' ),
				'description' => __( 'Measure the success of advertising your vacancies on your website, for example with Google Analytics and Matomo.', 'personio-integration-light' ),
				'category'    => 'tracking',
			),
			array(
				'name'        => 'divi',
				'label'       => __( 'Divi', 'personio-integration-light' ),
				'description' => __( 'Use one of the most used page builder to style your positions in your website.', 'personio-integration-light' ),
				'category'    => 'pagebuilder',
			),
			array(
				'name'        => 'elementor',
				'label'       => __( 'Elementor', 'personio-integration-light' ),
				'description' => __( 'Use the most used page builder to style your positions in your website.', 'personio-integration-light' ),
				'category'    => 'pagebuilder',
			),
			array(
				'name'        => 'wpbakery',
				'label'       => __( 'WP Bakery', 'personio-integration-light' ),
				'description' => __( 'Use the self declared #1 WordPress Page Builder to style your positions in your website.', 'personio-integration-light' ),
				'category'    => 'pagebuilder',
			),
		);
	}

	/**
	 * Add extension objects for pro-extensions to the table of extensions.
	 *
	 * @param array<Extensions_Base> $extensions The list of extensions.
	 *
	 * @return array<Extensions_Base>
	 */
	public function add_pro_extensions( array $extensions ): array {
		foreach ( $this->get_pro_extensions() as $extension ) {
			// create a placebo Extension_Base object.
			$obj = new Extensions_Base();
			$obj->set_name( $extension['name'] );
			$obj->set_label( $extension['label'] );
			$obj->set_description( $extension['description'] );
			$obj->set_plugin_source( 'pro' );
			$obj->set_category( $extension['category'] );

			// add it to the list.
			$extensions[] = $obj;
		}

		// return resulting list.
		return $extensions;
	}

	/**
	 * Update the state in dialog.
	 *
	 * @return void
	 */
	public function update_import_status(): void {
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_STATUS, __( 'Positions are importing ..', 'personio-integration-light' ) );
	}

	/**
	 * Add categories for our cpt for logging.
	 *
	 * @param array<string,string> $categories List of categories.
	 *
	 * @return array<string,string>
	 */
	public function add_log_categories( array $categories ): array {
		// add categories we need for our cpt.
		$categories['import'] = __( 'Import', 'personio-integration-light' );
		$categories['cli']    = __( 'WP CLI', 'personio-integration-light' );

		// return resulting list.
		return $categories;
	}

	/**
	 * Set limit for listings.
	 *
	 * @param int $wp_limit The limit set via WP.
	 * @param int $list_limit The limit set for the listing.
	 *
	 * @return int
	 */
	public function set_limit( int $wp_limit, int $list_limit ): int {
		// set limit.
		$limit = 10;

		/**
		 * Filter the max allowed limit.
		 *
		 * @since 4.0.0 Available since 4.0.0.
		 * @param int $limit The max. limit to use.
		 */
		$limit = apply_filters( 'personio_integration_light_limit', $limit );

		// bail if list limit is <= $limit but > 0.
		if ( $list_limit > 0 && $list_limit <= $limit ) {
			return $list_limit;
		}

		// bail if wp_limit is <= $limit.
		if ( $wp_limit <= $limit ) {
			return $wp_limit;
		}

		// return the limit.
		return $limit;
	}

	/**
	 * Add help for the cpt.
	 *
	 * @param array<int,array<string,string>> $help_list List of help tabs.
	 *
	 * @return array<int,array<string,string>>
	 */
	public function add_help( array $help_list ): array {
		// collect the content for the help.
		$content  = Helper::get_logo_img( true ) . '<h2>' . __( 'Positions from Personio', 'personio-integration-light' ) . '</h2><p>' . __( 'You manage all your positions yourself in Personio. They are imported into your WordPress website using the WordPress plugin <i>Personio Integration Light</i>. They are then displayed on your website.', 'personio-integration-light' ) . '</p>';
		$content .= '<p><strong>' . __( 'Steps to use:', 'personio-integration-light' ) . '</strong></p>';
		$content .= '<ol>';
		/* translators: %1$s will be replaced by a URL. */
		$content .= '<li>' . sprintf( __( 'Add your Personio URL <a href="%1$s">in the settings</a>.', 'personio-integration-light' ), esc_url( Helper::get_settings_url() ) ) . '</li>';
		/* translators: %1$s will be replaced by a URL. */
		$content .= '<li>' . sprintf( __( 'Import your positions <a href="%1$s" class="personio-integration-import-hint">via click on the button</a>.', 'personio-integration-light' ), esc_url( Helper::get_import_url() ) ) . '</li>';
		/* translators: %1$s will be replaced by a URL. */
		$content .= '<li>' . sprintf( __( 'Check the positions <a href="%1$s">in your frontend</a>.', 'personio-integration-light' ), esc_url( $this->get_archive_url() ) ) . '</li>';
		/* translators: %1$s will be replaced by a URL. */
		$content .= '<li>' . sprintf( __( 'Change settings <a href="%1$s">for the template</a> to optimize the view.', 'personio-integration-light' ), esc_url( Helper::get_settings_url( 'personioPositions', 'templates' ) ) ) . '</li>';
		// add menu entry for applications (with hint to pro).
		$false = false;
		/**
		 * Hide hint for Pro-plugin.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Set true to hide the hint.
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( ! apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			/* translators: %1$s will be replaced by a URL. */
			$content .= '<li>' . sprintf( __( '<a href="%1$s" target="_blank">Order Personio Integration Pro (opens new window)</a> for individual application forms and much more options.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) . '</li>';
		}
		$content .= '</ol>';

		// add help for the positions in general.
		$help_list[] = array(
			'id'      => $this->get_name() . '-cpt',
			'title'   => __( 'Personio Positions', 'personio-integration-light' ),
			'content' => $content,
		);

		// return resulting list.
		return $help_list;
	}

	/**
	 * Add help for the cpt.
	 *
	 * @param array<int,array<string,string>> $help_list List of help tabs.
	 *
	 * @return array<int,array<string,string>>
	 */
	public function add_shortcode_help( array $help_list ): array {
		// collect the content for the help.
		$content  = Helper::get_logo_img( true ) . '<h2>' . __( 'Shortcodes', 'personio-integration-light' ) . '</h2><p>' . __( 'We provide multiple shortcodes with numerous options. These can be used to output the open positions.', 'personio-integration-light' ) . '</p>';
		$content .= '<p><strong>' . __( 'When to use:', 'personio-integration-light' ) . '</strong></p>';
		$content .= '<p>' . __( 'Shortcodes should only be used if your own theme or PageBuilder is not supported by this plugin.', 'personio-integration-light' ) . '</p>';
		$false    = false;
		/**
		 * Hide hint for Pro-plugin.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Set true to hide the hint.
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( ! apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			$content .= '<p>' . __( 'With <i>Personio Integration Light</i>, we only support the Block Editor in this regard.', 'personio-integration-light' ) . '</p>';
			$content .= '<p>' . __( 'With <i>Personio Integration Pro</i> you can also manage your positions with Avada, Avia/Enfold, Elementor, Divi and many other PageBuilders and do not have to use shortcodes.', 'personio-integration-light' ) . '</p>';
		}
		$content .= '<p><strong>' . __( 'How to use:', 'personio-integration-light' ) . '</strong></p>';
		$content .= '<ol>';
		$content .= '<li>' . __( 'Import your open positions from Personio.', 'personio-integration-light' ) . '</li>';
		$content .= '<li>' . __( 'Add the shortcode you want to use where you want to output data for your positions.', 'personio-integration-light' ) . '</li>';
		$content .= '</ol>';
		$content .= '<p><strong>' . __( 'Example 1:', 'personio-integration-light' ) . '</strong></p>';
		$content .= '<p>' . __( 'This shortcode will output a list of positions including the filter above it. The filter will provide 2 properties. The posititions are displayed with a title and 2 job details matching the filter.', 'personio-integration-light' ) . '</p>';
		$content .= '<code>[personioPositions showfilter="1" filter="recruitingCategory,schedule" filtertype="linklist" templates="title,excerpt" excerpt="recruitingCategory,schedule"]</code>';
		$content .= '<p><strong>' . __( 'Example 2:', 'personio-integration-light' ) . '</strong></p>';
		$content .= '<p>' . __( 'This shortcode will output a single position with the Personio ID 42. The title, the job description and the link to the application form at Personio are output.', 'personio-integration-light' ) . '</p>';
		$content .= '<code>[personioPosition templates="title,content,formular" personioid="42"]</code>';
		$content .= '<p><strong>' . __( 'Documentation:', 'personio-integration-light' ) . '</strong></p>';
		/* translators: %1$s will be replaced by a URL. */
		$content .= '<p>' . sprintf( __( 'The complete documentation on the possibilities with shortcodes can be found <a href="%1$s" target="_blank">here (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_shortcode_documentation_url() ) ) . '</p>';
		$content .= '<p><strong>' . __( 'Further notes:', 'personio-integration-light' ) . '</strong></p>';
		$content .= '<ol>';
		$content .= '<li>' . __( 'If you want to customize the output of shortcodes in terms of styling, you have to write and store the necessary style properties yourself. If necessary, contact the person responsible for your project.', 'personio-integration-light' ) . '</li>';
		$false    = false;
		/**
		 * Hide hint for Pro-plugin.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Set true to hide the hint.
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( ! apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			/* translators: %1$s will be replaced by a URL. */
			$content .= '<li>' . sprintf( __( '<a href="%1$s" target="_blank">Order Personio Integration Pro (opens new window)</a> to get more flexible widgets for your theme or page builder.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) . '</li>';
		}
		$content .= '</ol>';

		// add help for the positions in general.
		$help_list[] = array(
			'id'      => $this->get_name() . '-shortcodes',
			'title'   => __( 'Shortcodes', 'personio-integration-light' ),
			'content' => $content,
		);

		// return resulting list.
		return $help_list;
	}

	/**
	 * Search in backend also in every postmeta for our positions.
	 *
	 * @param string   $search The searched keywords.
	 * @param WP_Query $wp_query The WP_Query-object.
	 *
	 * @return string
	 */
	public function search_also_in_meta_fields( string $search, WP_Query $wp_query ): string {
		// bail if we are not in admin.
		if ( ! is_admin() ) {
			return $search;
		}

		// bail if search string is empty.
		if ( empty( $search ) ) {
			return $search;
		}

		// bail if no keywords are available.
		if ( empty( $wp_query->get( 's' ) ) ) {
			return $search;
		}

		// bail if this is not our cpt.
		if ( $this->get_name() !== $wp_query->get( 'post_type' ) ) {
			return $search;
		}

		// get db object.
		global $wpdb;

		// get the search term from the query.
		$search_term = $wp_query->query['s'];
		$search      = '';

		// build SQL to search post title and content.
		$search .= "($wpdb->posts.post_title LIKE '%" . $wpdb->esc_like( $search_term ) . "%') OR ($wpdb->posts.post_content LIKE '%" . $wpdb->esc_like( $search_term ) . "%')";

		// add SQL to also search postmeta table for matching custom field values.
		$search .= " OR EXISTS (
			SELECT * FROM $wpdb->postmeta
			WHERE post_id = $wpdb->posts.ID
			AND meta_value LIKE '%" . $wpdb->esc_like( $search_term ) . "%'
		)";

		// return resulting search SQL string.
		return ' AND (' . $search . ') ';
	}

	/**
	 * Show hint on application forms in Pro plugins.
	 *
	 * @return void
	 */
	public function get_meta_box_for_application_form(): void {
		// show the hint.
		/* translators: %1$s will be replaced with the plugin pro-name */
		echo wp_kses_post( Admin::get_instance()->get_pro_hint( __( 'Use individual application forms for this position with %1$s.', 'personio-integration-light' ) ) );
	}

	/**
	 * Remove date filter on our own cpt.
	 *
	 * @param array<string> $months List of months.
	 * @param string        $post_type The used post type.
	 *
	 * @return array<string>
	 */
	public function remove_date_filter( array $months, string $post_type ): array {
		// bail if this is not our post type.
		if ( $this->get_name() !== $post_type ) {
			return $months;
		}

		// return an empty list.
		return array();
	}

	/**
	 * Show hint about additional offices, usable in Pro-plugin.
	 *
	 * @param Position            $position_obj The called position object.
	 * @param array<string,mixed> $attr The attributes of the meta box.
	 *
	 * @return void
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function show_hint_for_additional_offices( Position $position_obj, array $attr ): void {
		$false = false;
		/**
		 * Hide hint for Pro-plugin.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Set true to hide the hint.
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			return;
		}

		// get the requested taxonomy from the box ID as string.
		$taxonomy_name = str_replace( $this->get_name() . '-taxonomy-', '', $attr['id'] );

		// bail if taxonomy name is not office.
		if ( WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE !== $taxonomy_name ) {
			return;
		}

		/* translators: %1$s will be replaced by the Pro-plugin name. */
		echo wp_kses_post( Admin::get_instance()->get_pro_hint( __( 'Use additional offices with %1$s', 'personio-integration-light' ) ) );
	}

	/**
	 * Show hint in footer in backend on listing and single view of positions there.
	 *
	 * @param string $content The actual footer content.
	 *
	 * @return string
	 */
	public function show_plugin_hint_in_footer( string $content ): string {
		// get requested post type.
		$post_type = (string) filter_input( INPUT_GET, 'post_type' );

		// get requested post.
		$post = absint( filter_input( INPUT_GET, 'post' ) );

		// bail if this is not the listing or the single view of a position in backend.
		if ( $post_type !== $this->get_name() && get_post_type( $post ) !== $this->get_name() ) {
			return $content;
		}

		// show hint for our plugin.
		/* translators: %1$s will be replaced by the plugin name. */
		return $content . ' ' . sprintf( __( 'This page is provided by the plugin %1$s.', 'personio-integration-light' ), '<em>' . Helper::get_plugin_name() . '</em>' );
	}
}

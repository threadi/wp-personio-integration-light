<?php
/**
 * Main file for initialization of this plugin in frontend and backend.
 *
 * @package personio-integration-light
 */

use personioIntegration\helper;
use personioIntegration\Import;
use personioIntegration\Log;
use personioIntegration\Position;
use personioIntegration\Positions;
use personioIntegration\updates;
use Yoast\WP\SEO\Presentations\Indexable_Presentation;

/**
 * Add position as custom posttype.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_position_posttype(): void {
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
	$archive_slug = apply_filters( 'personio_integration_archive_slug', helper::get_archive_slug() );
	$detail_slug  = apply_filters( 'personio_integration_detail_slug', helper::get_detail_slug() );

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
		'menu_icon'           => plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/gfx/personio_icon.png',
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
add_action( 'init', 'personio_integration_add_position_posttype' );

/**
 * Add taxonomies used with the personio posttype.
 * Each will be visible in REST-API, also public.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_taxonomies(): void {
	// set default taxonomy-settings
	// -> could be overwritten by each taxonomy in taxonomies.php.
	$taxonomy_array_default = array(
		'hierarchical'       => true,
		'labels'             => '',
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => false,
		'show_in_nav_menus'  => false,
		'show_admin_column'  => true,
		'show_tagcloud'      => true,
		'show_in_quick_edit' => true,
		'show_in_rest'       => true,
		'query_var'          => true,
		'capabilities'       => array(
			'manage_terms' => 'read_' . WP_PERSONIO_INTEGRATION_CPT,
			'edit_terms'   => 'read_' . WP_PERSONIO_INTEGRATION_CPT,
			'delete_terms' => 'do_not_allow',
			'assign_terms' => 'read_' . WP_PERSONIO_INTEGRATION_CPT,
		),
	);

	// loop through our own taxonomies and configure them.
	foreach ( apply_filters( 'personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES ) as $taxonomy_name => $taxonomy ) {
		// get properties.
		$taxonomy_array             = array_merge( $taxonomy_array_default, $taxonomy['attr'] );
		$taxonomy_array['labels']   = helper::get_taxonomy_label( $taxonomy_name );
		$taxonomy_array['defaults'] = helper::get_taxonomy_defaults( $taxonomy_name );

		// remove slugs for not logged in users.
		if ( ! is_user_logged_in() ) {
			$taxonomy_array['rewrite'] = false;
		}

		// apply additional settings for taxonomy.
		$taxonomy_array = apply_filters( 'get_' . $taxonomy_name . '_translate_taxonomy', $taxonomy_array, $taxonomy_name );

		// do not show any taxonomy in menu if Personio URL is not available.
		if ( ! personioIntegration\helper::is_personio_url_set() ) {
			$taxonomy_array['show_in_menu'] = false;
		}

		// register taxonomy.
		register_taxonomy( $taxonomy_name, array( WP_PERSONIO_INTEGRATION_CPT ), $taxonomy_array );

		// filter for translations of entries in this taxonomy.
		add_filter( 'get_' . $taxonomy_name, 'personio_integration_translate_taxonomy', 10, 2 );
	}
}
add_action( 'init', 'personio_integration_add_taxonomies', 0 );

/**
 * One-time function to create taxonomy-defaults.
 *
 * @return void
 */
function personio_integration_add_taxonomy_defaults(): void {
	// Exit if the work has already been done.
	if ( 1 === absint( get_option( 'personioTaxonomyDefaults', 0 ) ) ) {
		return;
	}

	// loop through our own taxonomies and configure them.
	foreach ( apply_filters( 'personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES ) as $taxonomy_name => $taxonomy ) {
		// add default terms to taxonomy if they do not exist (only in admin or via CLI).
		$taxonomy_obj = get_taxonomy( $taxonomy_name );
		if ( ! empty( $taxonomy_obj->defaults ) && ( is_admin() || helper::is_cli() ) ) {
			$has_terms = get_terms( array( 'taxonomy' => $taxonomy_name ) );
			if ( empty( $has_terms ) ) {
				personioIntegration\helper::add_terms( $taxonomy_obj->defaults, $taxonomy_name );
			}
		}
	}

	// Add or update the wp_option.
	update_option( 'personioTaxonomyDefaults', 1 );
}
add_action( 'init', 'personio_integration_add_taxonomy_defaults', 20 );

/**
 * Change the REST API-response for own cpt.
 *
 * @param WP_REST_Response $data The response object.
 * @param WP_Post          $post The requested object.
 * @return WP_REST_Response
 * @noinspection PhpUnused
 */
function personio_integration_rest_changes( WP_REST_Response $data, WP_Post $post ): WP_REST_Response {
	// get positions-object.
	$positions = positions::get_instance();

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
add_filter( 'rest_prepare_' . WP_PERSONIO_INTEGRATION_CPT, 'personio_integration_rest_changes', 12, 2 );

/**
 * Register an old-fashion Wordpress-widget only if Block-widgets are disabled.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_register_widget(): void {
	if ( function_exists( 'wp_use_widgets_block_editor' ) && ! wp_use_widgets_block_editor() ) {
		register_widget( 'personioIntegration\PositionWidget' );
		register_widget( 'personioIntegration\PositionsWidget' );
	}
}
add_action( 'widgets_init', 'personio_integration_register_widget' );

/**
 * Un-Register an old-fashion Wordpress-widget only if Block-widgets are disabled.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_unregister_widget(): void {
	if ( function_exists( 'wp_use_widgets_block_editor' ) && ! wp_use_widgets_block_editor() ) {
		unregister_widget( 'personioIntegration\PositionWidget' );
		unregister_widget( 'personioIntegration\PositionsWidget' );
		delete_option( 'widget_personiopositionwidget' );
		delete_option( 'widget_personiopositionswidget' );
	}
}
add_action( 'widgets_init', 'personio_integration_unregister_widget', 20 );

/**
 * Run the scheduled positions-import.
 * Only if it is enabled.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_schudule_event_import_positions(): void {
	if ( 1 === absint( get_option( 'personioIntegrationEnablePositionSchedule', 0 ) ) ) {
		new Import();
	}
}
add_action( 'personio_integration_schudule_events', 'personio_integration_schudule_event_import_positions', 10, 0 );

/**
 * Add link in toolbar to list of positions.
 * Only if Personio URL is given and list-view is not disabled.
 *
 * @param WP_Admin_Bar $admin_bar The object of the Admin-Bar.
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_custom_toolbar( WP_Admin_Bar $admin_bar ): void {
	if ( get_option( 'personioIntegrationUrl', false ) && 0 === absint( get_option( 'personioIntegrationDisableListSlug', 0 ) ) ) {
		$admin_bar->add_menu(
			array(
				'id'     => 'personio-position-list',
				'parent' => 'site-name',
				'title'  => __( 'Personio Positions', 'personio-integration-light' ),
				'href'   => get_post_type_archive_link( WP_PERSONIO_INTEGRATION_CPT ),
			)
		);
	}
}
add_action( 'admin_bar_menu', 'personio_integration_add_custom_toolbar', 100 );

/**
 * Get template for archive or single view.
 *
 * @param string $template The requested template.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_use_cpt_template( string $template ): string {
	if ( WP_PERSONIO_INTEGRATION_CPT === get_post_type( get_the_ID() ) ) {
		// if the theme is a fse-theme.
		if ( Helper::theme_is_fse_theme() ) {
			return ABSPATH . WPINC . '/template-canvas.php';
		}

		// single-view for classic themes.
		if ( is_single() ) {
			return personio_integration_get_single_template( $template );
		}

		// archive-view for classic themes.
		return personio_integration_get_archive_template( $template );
	}
	return $template;
}
add_filter( 'template_include', 'personio_integration_use_cpt_template' );

/**
 * Get language-specific labels for categories.
 *
 * @return array
 */
function personio_integration_admin_categories_labels(): array {
	return apply_filters(
		'personio_integration_cat_labels',
		array(
			'recruitingCategory' => esc_html__( 'recruiting category', 'personio-integration-light' ),
			'schedule'           => esc_html__( 'schedule', 'personio-integration-light' ),
			'office'             => esc_html__( 'office', 'personio-integration-light' ),
			'department'         => esc_html__( 'department', 'personio-integration-light' ),
			'employmenttype'     => esc_html__( 'employment types', 'personio-integration-light' ),
			'seniority'          => esc_html__( 'seniority', 'personio-integration-light' ),
			'experience'         => esc_html__( 'experience', 'personio-integration-light' ),
			'occupation'         => esc_html__( 'Job type', 'personio-integration-light' ),
			'occupation_detail'  => esc_html__( 'Job type details', 'personio-integration-light' ),
		)
	);
}

/**
 * Get language-specific labels for content templates.
 *
 * This also defines the order of the templates in backend and frontend.
 *
 * @return array
 */
function personio_integration_admin_template_labels(): array {
	return apply_filters(
		'personio_integration_admin_template_labels',
		array(
			'title'    => esc_html__( 'title', 'personio-integration-light' ),
			'excerpt'  => esc_html__( 'details', 'personio-integration-light' ),
			'content'  => esc_html__( 'content', 'personio-integration-light' ),
			'formular' => esc_html__( 'application link', 'personio-integration-light' ),
		)
	);
}

/**
 * Return true for import any positions.
 *
 * @return bool
 * @noinspection PhpUnused
 */
function personio_integration_import_single_position(): bool {
	return true;
}
add_filter( 'personio_integration_import_single_position', 'personio_integration_import_single_position', 10, 2 );

/**
 * Add own CSS and JS for frontend.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_styles_frontend(): void {
	wp_enqueue_style(
		'personio-integration-styles',
		trailingslashit( plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'css/styles.css',
		array(),
		filemtime( trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'css/styles.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'personio_integration_add_styles_frontend', PHP_INT_MAX );

/**
 * Change all attributes zu lowercase
 *
 * @param array $values List of shortcode attributes.
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_lowercase_attributes( array $values ): array {
	// TODO better solution?
	$array = array();
	foreach ( $values['attributes'] as $name => $attribute ) {
		$array[ strtolower( $name ) ] = $attribute;
	}
	return array(
		'defaults'   => $values['defaults'],
		'settings'   => $values['settings'],
		'attributes' => $array,
	);
}
add_filter( 'personio_integration_get_shortcode_attributes', 'personio_integration_lowercase_attributes', 5 );

/**
 * Remove our own cpt from post type list in Redirection-plugin.
 *
 * @param array $post_types List of post-types.
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_redirection_post_types( array $post_types ): array {
	// TODO testen.
	if ( ! empty( $post_types[ WP_PERSONIO_INTEGRATION_CPT ] ) ) {
		unset( $post_types[ WP_PERSONIO_INTEGRATION_CPT ] );
	}
	return $post_types;
}
add_filter( 'redirection_post_types', 'personio_integration_redirection_post_types' );

/**
 * Optimize Yoast-generated og:description-text.
 * Without this Yoast uses the page content with formular or button-texts.
 *
 * @param string                 $meta_og_description The actual description for OpenGraph.
 * @param Indexable_Presentation $presentation The WPSEO Presentation object.
 * @return string
 */
function personio_integration_yoast_description( string $meta_og_description, Indexable_Presentation $presentation ): string {
	if ( WP_PERSONIO_INTEGRATION_CPT === $presentation->model->object_sub_type ) {
		$position = new Position( $presentation->model->object_id );
		return preg_replace( '/\s+/', ' ', $position->get_content() );
	}
	return $meta_og_description;
}
add_filter( 'wpseo_opengraph_desc', 'personio_integration_yoast_description', 10, 2 );

/**
 * Optimize RankMath-generated meta-description and og:description.
 * Without this RankMath uses plain post_content, which is JSON and not really nice to read.
 *
 * @param string $description The actual description.
 * @return string
 */
function personio_integration_rankmath_description( string $description ): string {
	if ( is_single() ) {
		$object = get_queried_object();
		if ( $object instanceof WP_Post && WP_PERSONIO_INTEGRATION_CPT === $object->post_type ) {
			$position = new Position( $object->ID );
			return preg_replace( '/\s+/', ' ', $position->get_content() );
		}
	}
	return $description;
}
add_filter( 'rank_math/frontend/description', 'personio_integration_rankmath_description' );

/**
 * Check on each load if plugin-version has been changed.
 * If yes, run appropriated functions for migrate to the new version.
 *
 * @return void
 */
function personio_integration_update(): void {
	// get installed plugin-version (version of the actual files in this plugin).
	$installed_plugin_version = WP_PERSONIO_INTEGRATION_VERSION;

	// get db-version (version which was last installed).
	$db_plugin_version = get_option( 'personioIntegrationVersion', '1.0.0' );

	// compare version if we are not in development-mode.
	// TODO better solution for env-mode.
	if ( '@@VersionNumber@@' !== $installed_plugin_version && version_compare( $installed_plugin_version, $db_plugin_version, '>' ) ) {
		// TODO cleanup.
		switch ( $db_plugin_version ) {
			case '1.2.3':
				// nothing to do as 1.2.3 is the first version with this update-check.
				break;
			default:
				Updates::version123();
				Updates::version205();
				Updates::version211();
				Updates::version227();
				Updates::version240();
				Updates::version250();
				Updates::version255();
				break;
		}

		// save new plugin-version in DB.
		update_option( 'personioIntegrationVersion', $installed_plugin_version );
	}
}
add_action( 'plugins_loaded', 'personio_integration_update' );

/**
 * Add each position to list during import.
 *
 * @return true
 */
function personio_integration_import_single_position_filter_existing(): bool {
	return true;
}
add_filter( 'personio_integration_import_single_position_filter_existing', 'personio_integration_import_single_position_filter_existing' );

/**
 * Add endpoints for requests from our own Blocks.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_rest_api(): void {
	// return possible taxonomies.
	register_rest_route(
		'personio/v1',
		'/taxonomies/',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'personio_integration_rest_api_taxonomies',
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
			'callback'            => 'personio_integration_rest_api_jobdescription_templates',
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
			'callback'            => 'personio_integration_rest_api_archive_templates',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'rest_api_init', 'personio_integration_rest_api' );

/**
 * Return list of available taxonomies for REST-API.
 *
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_rest_api_taxonomies(): array {
	$taxonomies_labels_array = personio_integration_admin_categories_labels();
	$taxonomies              = array();
	$count                   = 0;
	foreach ( apply_filters( 'personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES ) as $taxonomy_name => $taxonomy ) {
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
 * Return list of possible templates for job description in REST API.
 *
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_rest_api_jobdescription_templates(): array {
	return apply_filters(
		'personio_integration_rest_templates_jobdescription',
		array(
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
		)
	);
}

/**
 * Return list of possible templates for archive templates in REST API.
 *
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_rest_api_archive_templates(): array {
	return apply_filters(
		'personio_integration_rest_templates_archive',
		array(
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
		)
	);
}

/**
 * Return list of possible templates for job description.
 *
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_jobdescription_templates(): array {
	return apply_filters(
		'personio_integration_templates_jobdescription',
		array(
			'default' => __( 'Default', 'personio-integration-light' ),
			'list'    => __( 'As list', 'personio-integration-light' ),
		)
	);
}

/**
 * Return list of possible templates for archive listings.
 *
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_archive_templates(): array {
	return apply_filters(
		'personio_integration_templates_archive',
		array(
			'default' => __( 'Default', 'personio-integration-light' ),
			'listing' => __( 'Listings', 'personio-integration-light' ),
		)
	);
}

/**
 * Optimize output of plugin OG.
 *
 * @source https://de.wordpress.org/plugins/og/
 * @param array $og_array List of OpenGraph-settings from OG-plugin.
 * @return array
 */
function personio_integration_og_optimizer( array $og_array ): array {
	if ( is_singular( WP_PERSONIO_INTEGRATION_CPT ) ) {
		// get position as object.
		$post_id        = get_queried_object_id();
		$position       = new Position( $post_id );
		$position->lang = helper::get_wp_lang(); // TODO check.

		// get description.
		$description = wp_strip_all_tags( $position->get_content() );
		$description = preg_replace( '/\s+/', ' ', $description );

		// update settings.
		$og_array['og']['title']            = $position->getTitle();
		$og_array['og']['description']      = $description;
		$og_array['twitter']['title']       = $position->getTitle();
		$og_array['twitter']['description'] = $description;
		$og_array['schema']['title']        = $position->getTitle();
		$og_array['schema']['description']  = $description;
	}

	// return resulting list.
	return $og_array;
}
add_filter( 'og_array', 'personio_integration_og_optimizer' );

/**
 * Translate the term-names of each plugin-own taxonomy if set.
 * Only in frontend, not in backend.
 *
 * @param WP_Term $_term The term as object.
 * @param string  $taxonomy The taxonomy as string.
 * @return mixed
 * @noinspection PhpUnused
 */
function personio_integration_translate_taxonomy( WP_Term $_term, string $taxonomy ): WP_Term {
	if ( WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES !== $taxonomy ) {
		// read from defaults for the taxonomy.
		$array = helper::get_taxonomy_defaults( $taxonomy );
		if ( ! empty( $array[ $_term->name ] ) ) {
			$_term->name = $array[ $_term->name ];
		}
	}
	return $_term;
}

/**
 * Log every deletion of a position.
 *
 * @param int $post_id The ID of the post which will be deleted.
 * @return void
 */
function personio_integration_action_to_delete_position( int $post_id ): void {
	// bail if this is not our own cpt.
	if ( WP_PERSONIO_INTEGRATION_CPT !== get_post_type( $post_id ) ) {
		return;
	}

	// get position.
	$positions_obj = Positions::get_instance();
	$position_obj  = $positions_obj->get_position( $post_id );

	// log deletion.
	$log = new Log();
	$log->add_log( 'Position ' . $position_obj->getPersonioId() . ' has been deleted.', 'success' );
}
add_action( 'before_delete_post', 'personio_integration_action_to_delete_position' );

/**
 * Add endpoint for requests to check cronjobs.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_rest_api(): void {
	register_rest_route(
		'personio/v1',
		'/import_cron_checks/',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'personio_integration_rest_api_import_cron_checks',
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
		)
	);
	register_rest_route(
		'personio/v1',
		'/url_availability_checks/',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'personio_integration_rest_api_url_availability_check',
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
		)
	);
}
add_action( 'rest_api_init', 'personio_integration_admin_rest_api' );

/**
 * Return result after checking cronjob-states.
 *
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_rest_api_import_cron_checks(): array {
	// define default results.
	$result = array(
		'label'       => __( 'Personio Integration Import Cron Check', 'personio-integration-light' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Personio Integration Light', 'personio-integration-light' ),
			'color' => 'gray',
		),
		'description' => __( 'Running cronjobs help to import new positions from Personio automatically.<br><strong>All ok with the cronjob!</strong>', 'personio-integration-light' ),
		'actions'     => '',
		'test'        => 'personio_integration_rest_api_import_cron_checks',
	);

	// get scheduled event.
	$scheduled_event = wp_get_scheduled_event( 'personio_integration_schudule_events' );

	// event does not exist => show error.
	if ( false === $scheduled_event ) {
		$url                   = add_query_arg(
			array(
				'action' => 'personioPositionsCreateSchedules',
				'nonce'  => wp_create_nonce( 'wp-personio-integration-create-schedules' ),
			),
			get_admin_url() . 'admin.php'
		);
		$result['status']      = 'recommended';
		$result['description'] = __( 'Cronjob to import new Positions from Personio does not exist!', 'personio-integration-light' );
		/* translators: %1$s will be replaced by the URL to recreate the schedule */
		$result['actions'] = sprintf( '<p><a href="%1$s" class="button button-primary">Recreate the schedules</a></p>', $url );

		// return this result.
		return $result;
	}

	// if scheduled event exist, check if next run is in the past.
	if ( $scheduled_event->timestamp < time() ) {
		$result['status'] = 'recommended';
		/* translators: %1$s will be replaced by the date of the planned next schedule run (which is in the past) */
		$result['description'] = sprintf( __( 'Cronjob to import new Positions from Personio should have been run at %1$s, but was not executed!<br><strong>Please check the cron-system of your WordPress-installation.</strong>', 'personio-integration-light' ), helper::get_format_date_time( gmdate( 'Y-m-d H:i:s', $scheduled_event->timestamp ) ) );

		// return this result.
		return $result;
	}

	// return result.
	return $result;
}

/**
 * Check the Personio-URL availability.
 *
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_rest_api_url_availability_check(): array {
	// define default results.
	$result = array(
		'label'       => __( 'Personio Integration URL availability Check', 'personio-integration-light' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Personio Integration Light', 'personio-integration-light' ),
			'color' => 'gray',
		),
		/* translators: %1$s and %2$s will be replaced by the Personio-URL */
		'description' => sprintf( __( 'The Personio-URL <a href="%1$s" target="_blank">%2$s (opens new window)</a> is necessary to import new positions.<br><strong>All ok with the URL!</strong>', 'personio-integration-light' ), helper::get_personio_url(), helper::get_personio_url() ),
		'actions'     => '',
		'test'        => 'personio_integration_rest_api_url_availability_check',
	);

	// -> should return HTTP-Status 200.
	$response = wp_remote_get(
		helper::get_personio_xml_url( helper::get_personio_url() ),
		array(
			'timeout'     => 30,
			'redirection' => 0,
		)
	);
	// get the body with the contents.
	$body = wp_remote_retrieve_body( $response );
	if ( ( is_array( $response ) && ! empty( $response['response']['code'] ) && 200 !== $response['response']['code'] ) || str_starts_with( $body, '<!doctype html>' ) ) {
		$url_settings     = add_query_arg(
			array(
				'post_type' => WP_PERSONIO_INTEGRATION_CPT,
				'page'      => 'personioPositions',
			),
			'edit.php'
		);
		$result['status'] = 'recommended';
		/* translators: %1$s and %2$s will be replaced by the Personio-URL, %3$s will be replaced by the settings-URL, %4$s will be replaced by the URL to login on Personio */
		$result['description'] = sprintf( __( 'The Personio-URL <a href="%1$s" target="_blank">%2$s (opens new window)</a> is not available for the import of positions!<br><strong>Please check if you have entered the correct URL <a href="%3$s">in the plugin-settings</a>.<br>Also check if you have enabled the XML-API in your <a href="%4$s" target="_blank">Personio-account (opens new window)</a> under Settings > Recruiting > Career Page > Activations.</strong>', 'personio-integration-light' ), helper::get_personio_url(), helper::get_personio_url(), $url_settings, helper::get_personio_login_url() );
	}

	// return result.
	return $result;
}

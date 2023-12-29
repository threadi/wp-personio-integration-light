<?php
/**
 * File to handle multiple taxonomies of this plugin.
 *
 * @package personio-integration-light
 */

namespace App\PersonioIntegration;

use App\Helper;
use WP_Term;

/**
 * The object which handles multiple taxonomies.
 */
class Taxonomies {

	/**
	 * Instance of this object.
	 *
	 * @var ?Taxonomies
	 */
	private static ?Taxonomies $instance = null;

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
	public static function get_instance(): Taxonomies {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize taxonomies.
	 *
	 * @return void
	 */
	public function init(): void {
		// register taxonomies.
		add_action( 'init', array( $this, 'register' ) );

		// create defaults.
		add_action( 'init', array( $this, 'create_defaults' ) );
	}

	/**
	 * One-time function to create taxonomy-defaults.
	 *
	 * @return void
	 */
	public function create_defaults(): void {
		// Exit if the work has already been done.
		if ( 1 === absint( get_option( 'personioTaxonomyDefaults', 0 ) ) ) {
			return;
		}

		// loop through our own taxonomies and configure them.
		foreach ( apply_filters( 'personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES ) as $taxonomy_name => $taxonomy ) {
			// add default terms to taxonomy if they do not exist (only in admin or via CLI).
			$taxonomy_obj = get_taxonomy( $taxonomy_name );
			if ( ! empty( $taxonomy_obj->defaults ) && ( is_admin() || Helper::is_cli() ) ) {
				$has_terms = get_terms( array( 'taxonomy' => $taxonomy_name ) );
				if ( empty( $has_terms ) ) {
					Helper::add_terms( $taxonomy_obj->defaults, $taxonomy_name );
				}
			}
		}

		// Add or update the wp_option.
		update_option( 'personioTaxonomyDefaults', 1 );
	}

	/**
	 * Register our own taxonomies on each page load.
	 *
	 * @return void
	 */
	public function register(): void {
		foreach ( $this->get_taxonomies() as $taxonomy_name => $settings ) {
			// get properties.
			$taxonomy_array             = array_merge( $this->get_default_settings(), $settings['attr'] );
			$taxonomy_array['labels']   = Helper::get_taxonomy_label( $taxonomy_name );
			$taxonomy_array['defaults'] = Helper::get_taxonomy_defaults( $taxonomy_name );

			// remove slugs for not logged in users.
			if ( ! is_user_logged_in() ) {
				$taxonomy_array['rewrite'] = false;
			}

			// apply additional settings for taxonomy.
			$taxonomy_array = apply_filters( 'get_' . $taxonomy_name . '_translate_taxonomy', $taxonomy_array, $taxonomy_name );

			// do not show any taxonomy in menu if Personio URL is not available.
			if ( ! Helper::is_personio_url_set() ) {
				$taxonomy_array['show_in_menu'] = false;
			}

			// register taxonomy.
			register_taxonomy( $taxonomy_name, array( WP_PERSONIO_INTEGRATION_CPT ), $taxonomy_array );

			// filter for translations of entries in this taxonomy.
			add_filter( 'get_' . $taxonomy_name, array( $this, 'translate' ), 10, 2 );
		}
	}

	/**
	 * Return the taxonomies this plugin is using.
	 *
	 * @return array
	 */
	private function get_taxonomies(): array {
		return apply_filters(
			'personio_integration_taxonomies',
			array(
				WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY => array(
					'attr'        => array( // taxonomy settings deviating from default.
						'rewrite' => array( 'slug' => 'recruitingCategory' ),
					),
					'slug'        => 'recruitingCategory',
					'useInFilter' => 1,
				),
				WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY => array(
					'attr'        => array( // taxonomy settings deviating from default.
						'rewrite' => array( 'slug' => 'occupationCategory' ),
					),
					'slug'        => 'occupation',
					'useInFilter' => 1,
				),
				WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION => array(
					'attr'        => array( // taxonomy settings deviating from default.
						'rewrite' => array( 'slug' => 'occupation' ),
					),
					'slug'        => 'occupation_detail',
					'useInFilter' => 1,
				),
				WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE    => array(
					'attr'        => array( // taxonomy settings deviating from default.
						'rewrite' => array( 'slug' => 'office' ),
					),
					'slug'        => 'office',
					'useInFilter' => 1,
				),
				WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT => array(
					'attr'        => array( // taxonomy settings deviating from default.
						'rewrite' => array( 'slug' => 'department' ),
					),
					'slug'        => 'department',
					'useInFilter' => 1,
				),
				WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE => array(
					'attr'        => array( // taxonomy settings deviating from default.
						'rewrite' => array( 'slug' => 'employmenttype' ),
					),
					'slug'        => 'employmenttype',
					'useInFilter' => 1,
				),
				WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY => array(
					'attr'        => array( // taxonomy settings deviating from default.
						'rewrite' => array( 'slug' => 'seniority' ),
					),
					'slug'        => 'seniority',
					'useInFilter' => 1,
				),
				WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE  => array(
					'attr'        => array( // taxonomy settings deviating from default.
						'rewrite' => array( 'slug' => 'schedule' ),
					),
					'slug'        => 'schedule',
					'useInFilter' => 1,
				),
				WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE => array(
					'attr'        => array( // taxonomy settings deviating from default.
						'rewrite' => array( 'slug' => 'experience' ),
					),
					'slug'        => 'experience',
					'useInFilter' => 1,
				),
				WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES => array(
					'attr'        => array( // taxonomy settings deviating from default.
						'show_ui' => false,
					),
					'slug'        => 'language',
					'useInFilter' => 0,
				),
				WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS  => array(
					'attr'        => array( // taxonomy settings deviating from default.
						'rewrite' => array( 'slug' => 'keyword' ),
					),
					'slug'        => 'keyword',
					'useInFilter' => 1,
				),
			)
		);
	}

	/**
	 * Translate a term of a given taxonomy.
	 *
	 * @param WP_Term $_term The term.
	 * @param string  $taxonomy The taxonomy.
	 *
	 * @return WP_Term
	 */
	public function translate( WP_Term $_term, string $taxonomy ): WP_Term {
		if ( WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES !== $taxonomy ) {
			// read from defaults for the taxonomy.
			$array = Helper::get_taxonomy_defaults( $taxonomy );
			if ( ! empty( $array[ $_term->name ] ) ) {
				$_term->name = $array[ $_term->name ];
			}
		}
		return $_term;
	}

	/**
	 * Get default settings for each taxonomy.
	 *
	 * @return array
	 */
	private function get_default_settings(): array {
		return array(
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
	}

	/**
	 * Get category labels.
	 *
	 * @return array
	 */
	public function get_cat_labels(): array {
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
}

<?php
/**
 * File to handle all taxonomies in this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Languages;
use WP_REST_Server;
use WP_Screen;
use WP_Term;

/**
 * The object which handles all taxonomies in this plugin.
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
	private function __clone() {}

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
		add_action( 'init', array( $this, 'register' ), 0 );

		// use REST hooks.
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );

		// our own hooks.
		add_filter( 'personio_integration_get_shortcode_attributes', array( $this, 'check_taxonomies' ) );
		add_filter( 'personio_integration_get_list_attributes', array( $this, 'filter_by_attributes' ), 10, 2 );

		// hide some taxonomies in columns.
		add_filter( 'hidden_columns', array( $this, 'hide_columns' ), 10, 3 );
	}

	/**
	 * One-time function to create taxonomy-defaults.
	 *
	 * @param array $callback Callback for each step (optional).
	 *
	 * @return void
	 */
	public function create_defaults( array $callback = array() ): void {
		// disable taxonomy-counting for more speed.
		wp_defer_term_counting( true );

		$i = 0;

		// loop through our own taxonomies and add their default terms.
		foreach ( self::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
			// add default terms to taxonomy if they do not exist (only in admin or via CLI).
			$taxonomy_obj = get_taxonomy( $taxonomy_name );
			if ( ! empty( $taxonomy_obj->defaults ) ) {
				$has_terms = get_terms( array( 'taxonomy' => $taxonomy_name ) );
				if ( empty( $has_terms ) ) {
					$this->add_terms( $taxonomy_obj->defaults, $taxonomy_name, $callback );

					// count.
					++$i;

					// flush cache every 100 items for more speed.
					if ( 0 === $i % 100 ) {
						wp_cache_flush();
					}
				} elseif ( ! empty( $callback ) && is_callable( $callback ) ) {
					call_user_func( $callback, count( $taxonomy_obj->defaults ) );
				}
			}
		}

		// re-enable taxonomy-counting.
		wp_defer_term_counting( false );
	}

	/**
	 * Register our own taxonomies on each page load.
	 *
	 * @return void
	 */
	public function register(): void {
		// loop through the taxonomies our plugin will be using.
		foreach ( $this->get_taxonomies() as $taxonomy_name => $settings ) {
			// get properties.
			$taxonomy_array             = array_merge( $this->get_default_settings(), $settings['attr'] );
			$taxonomy_array['labels']   = $this->get_taxonomy_label( $taxonomy_name );
			$taxonomy_array['defaults'] = $this->get_default_terms_for_taxonomy( $taxonomy_name );

			// remove slugs for not logged in users.
			if ( ! is_user_logged_in() ) {
				$taxonomy_array['rewrite'] = false;
			}

			/**
			 * Filter the taxonomy array just before it is registered.
			 *
			 * @since 3.0.0 Available since 3.0.0.
			 *
			 * @param array $taxonomy_array List of settings for the taxonomy.
			 * @param string $taxonomy_name Name of the taxonomy.
			 */
			$taxonomy_array = apply_filters( 'get_' . $taxonomy_name . '_translate_taxonomy', $taxonomy_array, $taxonomy_name );

			// do not show any taxonomy in menu if Personio URL is not available.
			if ( ! Helper::is_personio_url_set() ) {
				$taxonomy_array['show_in_menu'] = false;
			}

			// register this taxonomy.
			register_taxonomy( $taxonomy_name, array( PersonioPosition::get_instance()->get_name() ), $taxonomy_array );

			// filter for translations of entries in this taxonomy.
			add_filter( 'get_' . $taxonomy_name, array( $this, 'translate' ), 10, 2 );
		}
	}

	/**
	 * Return the taxonomies this plugin is using with its individual settings.
	 *
	 * @return array
	 */
	public function get_taxonomies(): array {
		$taxonomies = array(
			WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY => array(
				'attr'        => array(
					'rewrite' => array( 'slug' => 'recruitingCategory' ),
				),
				'slug'        => 'recruitingCategory',
				'useInFilter' => 1,
				'append'      => false,
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY => array(
				'attr'        => array(
					'rewrite' => array( 'slug' => 'occupationCategory' ),
				),
				'slug'        => 'occupation',
				'useInFilter' => 1,
				'append'      => false,
				'changeable'  => true,
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION => array(
				'attr'        => array(
					'rewrite' => array( 'slug' => 'occupation' ),
				),
				'slug'        => 'occupation_detail',
				'useInFilter' => 1,
				'append'      => false,
				'changeable'  => true,
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE     => array(
				'attr'        => array(
					'rewrite' => array( 'slug' => 'office' ),
				),
				'slug'        => 'office',
				'useInFilter' => 1,
				'append'      => true,
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT => array(
				'attr'        => array(
					'rewrite' => array( 'slug' => 'department' ),
				),
				'slug'        => 'department',
				'useInFilter' => 1,
				'append'      => false,
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE => array(
				'attr'                 => array(
					'rewrite' => array( 'slug' => 'employmenttype' ),
				),
				'slug'                 => 'employmenttype',
				'useInFilter'          => 1,
				'initiallyHideInTable' => 1,
				'append'               => false,
				'changeable'           => true,
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY  => array(
				'attr'                 => array(
					'rewrite' => array( 'slug' => 'seniority' ),
				),
				'slug'                 => 'seniority',
				'useInFilter'          => 1,
				'initiallyHideInTable' => 1,
				'append'               => false,
				'changeable'           => true,
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE   => array(
				'attr'                 => array(
					'rewrite' => array( 'slug' => 'schedule' ),
				),
				'slug'                 => 'schedule',
				'useInFilter'          => 1,
				'initiallyHideInTable' => 1,
				'append'               => false,
				'changeable'           => true,
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE => array(
				'attr'                 => array(
					'rewrite' => array( 'slug' => 'experience' ),
				),
				'slug'                 => 'experience',
				'useInFilter'          => 1,
				'initiallyHideInTable' => 1,
				'append'               => false,
				'changeable'           => true,
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES  => array(
				'attr'        => array(
					'rewrite' => array( 'slug' => 'language' ),
					'show_ui' => false,
				),
				'slug'        => 'language',
				'useInFilter' => 0,
				'append'      => true,
				'changeable'  => true,
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS   => array(
				'attr'                 => array(
					'rewrite' => array( 'slug' => 'keyword' ),
				),
				'slug'                 => 'keyword',
				'useInFilter'          => 1,
				'initiallyHideInTable' => 1,
				'append'               => true,
			),
		);

		/**
		 * Filter all taxonomies and return the resulting list as array.
		 *
		 * @since 1.0.0 Available since first release.
		 *
		 * @param array $taxonomies The list of taxonomies.
		 */
		return apply_filters( 'personio_integration_taxonomies', $taxonomies );
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
		// bail if requested taxonomy is the language taxonomy.
		if ( WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES === $taxonomy ) {
			return $_term;
		}
		// read from defaults for the taxonomy.
		$array = $this->get_default_terms_for_taxonomy( $taxonomy );
		if ( ! empty( $array[ $_term->name ] ) ) {
			$_term->name = $array[ $_term->name ];
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
			'hierarchical'       => false,
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
				'manage_terms' => 'read_' . PersonioPosition::get_instance()->get_name(),
				'edit_terms'   => 'read_' . PersonioPosition::get_instance()->get_name(),
				'delete_terms' => 'do_not_allow',
				'assign_terms' => 'read_' . PersonioPosition::get_instance()->get_name(),
			),
		);
	}

	/**
	 * Return the labels for all known taxonomies.
	 *
	 * @return array[]
	 */
	public function get_taxonomy_labels(): array {
		return array(
			WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY => array(
				'name'          => _x( 'Categories', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Category', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search category', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit category', 'personio-integration-light' ),
				'update_item'   => __( 'Update category', 'personio-integration-light' ),
				'menu_name'     => __( 'Categories', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY => array(
				'name'          => _x( 'Job types', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Job type', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search Job type', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit Job type', 'personio-integration-light' ),
				'update_item'   => __( 'Update Job type', 'personio-integration-light' ),
				'menu_name'     => __( 'Job types', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION => array(
				'name'          => _x( 'Job type details', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Job type detail', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search Job type detail', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit Job type detail', 'personio-integration-light' ),
				'update_item'   => __( 'Update Job type detail', 'personio-integration-light' ),
				'menu_name'     => __( 'Job type details', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE     => array(
				'name'          => _x( 'Locations', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Location', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search location', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit location', 'personio-integration-light' ),
				'update_item'   => __( 'Update location', 'personio-integration-light' ),
				'menu_name'     => __( 'Locations', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT => array(
				'name'          => _x( 'Departments', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Department', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search department', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit department', 'personio-integration-light' ),
				'update_item'   => __( 'Update department', 'personio-integration-light' ),
				'menu_name'     => __( 'Departments', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE => array(
				'name'          => _x( 'Employment types', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Employment type', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search employment type', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit employment type', 'personio-integration-light' ),
				'update_item'   => __( 'Update employment type', 'personio-integration-light' ),
				'menu_name'     => __( 'Employment types', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY  => array(
				'name'          => _x( 'Experiences', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Experience', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search Experience', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit Experience', 'personio-integration-light' ),
				'update_item'   => __( 'Update Experience', 'personio-integration-light' ),
				'menu_name'     => __( 'Experiences', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE   => array(
				'name'          => _x( 'Contract types', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Contract type', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search Contract type', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit Contract type', 'personio-integration-light' ),
				'update_item'   => __( 'Update Contract type', 'personio-integration-light' ),
				'menu_name'     => __( 'Contract types', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE => array(
				'name'          => _x( 'Years of experiences', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Years of experience', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search years of experience', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit years of experience', 'personio-integration-light' ),
				'update_item'   => __( 'Update years of experience', 'personio-integration-light' ),
				'menu_name'     => __( 'Years of experiences', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES  => array(
				'name'          => _x( 'Languages', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Language', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search language', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit language', 'personio-integration-light' ),
				'update_item'   => __( 'Update language', 'personio-integration-light' ),
				'menu_name'     => __( 'Languages', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS   => array(
				'name'          => _x( 'Keywords', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Keyword', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search Keywords', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit Keyword', 'personio-integration-light' ),
				'update_item'   => __( 'Update keyword', 'personio-integration-light' ),
				'menu_name'     => __( 'Keywords', 'personio-integration-light' ),
			),
		);
	}

	/**
	 * Get language-depending taxonomy-labels for frontend.
	 *
	 * @param string $taxonomy The requested taxonomy.
	 * @param string $language_code The requested language (optional).
	 *
	 * @return array
	 */
	public function get_taxonomy_label( string $taxonomy, string $language_code = '' ): array {
		// get actual locale.
		$locale = get_locale();

		// switch to requested language.
		if ( ! is_admin() ) {
			// if no language is requested, use the current language.
			if ( empty( $language_code ) ) {
				$language_code = Languages::get_instance()->get_current_lang();
			}

			// get mappings.
			$language_mappings = Languages::get_instance()->get_lang_mappings( $language_code );

			// get WP-lang-compatible language-code.
			if ( empty( $language_code ) && ! empty( $language_mappings ) ) {
				$language_code = $language_mappings[0];
			} else {
				$language_code = Languages::get_instance()->get_fallback_language_name();
			}

			// switch the language.
			switch_to_locale( $language_code );
		}

		// get ALL taxonomy labels in the requested language.
		$array = $this->get_taxonomy_labels();

		// revert the locale-setting.
		if ( ! is_admin() ) {
			switch_to_locale( $locale );
		}

		// if the requested taxonomy does not exist in the array add it as empty setting.
		if ( empty( $array[ $taxonomy ] ) ) {
			$array[ $taxonomy ] = array();
		}

		// get the label.
		$label = $array[ $taxonomy ];

		/**
		 * Filter the taxonomy label.
		 *
		 * @since 1.0.0 Available since first release.
		 *
		 * @param string $label The label.
		 * @param string $taxonomy The taxonomy.
		 */
		return apply_filters( 'personio_integration_filter_taxonomy_label', $label, $taxonomy );
	}

	/**
	 * Get taxonomy labels for settings (only slug and label).
	 *
	 * @return array
	 */
	public function get_taxonomy_labels_for_settings(): array {
		$labels = array(
			'recruitingCategory' => esc_html__( 'Recruiting category', 'personio-integration-light' ),
			'schedule'           => esc_html__( 'Schedule', 'personio-integration-light' ),
			'office'             => esc_html__( 'Office', 'personio-integration-light' ),
			'department'         => esc_html__( 'Department', 'personio-integration-light' ),
			'employmenttype'     => esc_html__( 'Employment types', 'personio-integration-light' ),
			'seniority'          => esc_html__( 'Seniority', 'personio-integration-light' ),
			'experience'         => esc_html__( 'Experience', 'personio-integration-light' ),
			'occupation'         => esc_html__( 'Job type', 'personio-integration-light' ),
			'occupation_detail'  => esc_html__( 'Job type details', 'personio-integration-light' ),
		);

		/**
		 * Change category list.
		 *
		 * @since 1.0.0 Available since first release.
		 *
		 * @param array $labels The list of labels (internal name/slug => label).
		 */
		return apply_filters( 'personio_integration_cat_labels', $labels );
	}

	/**
	 * Convert term-name to term-id if it is set in shortcode-attributes and configure shortcode-attribute.
	 *
	 * @param array $settings List of settings for a shortcode with 3 parts: defaults, settings & attributes.
	 * @return array
	 */
	public function check_taxonomies( array $settings ): array {
		// check each taxonomy if it is used as restriction for this list.
		foreach ( self::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
			$slug = strtolower( $taxonomy['slug'] );
			if ( ! empty( $settings['attributes'][ $slug ] ) ) {
				$term = get_term_by( 'id', $settings['attributes'][ $slug ], $taxonomy_name );
				if ( ! empty( $term ) ) {
					$settings['defaults'][ $taxonomy['slug'] ]   = 0;
					$settings['settings'][ $taxonomy['slug'] ]   = 'filter';
					$settings['attributes'][ $taxonomy['slug'] ] = $term->term_id;
				}
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
	 * Add terms from an array to a taxonomy.
	 *
	 * @param array  $list_or_terms List of terms to add.
	 * @param string $taxonomy_name Name of the taxonomy.
	 * @param array  $callback Callback if term has been processed.
	 *
	 * @return void
	 */
	private function add_terms( array $list_or_terms, string $taxonomy_name, array $callback ): void {
		foreach ( $list_or_terms as $term => $term_title ) {
			if ( ! term_exists( $term, $taxonomy_name ) ) {
				wp_insert_term(
					$term,
					$taxonomy_name
				);
			}

			// update steps via callback.
			if ( ! empty( $callback ) && is_callable( $callback ) ) {
				call_user_func( $callback, 1 );
			}
		}
	}

	/**
	 * Return internal taxonomy name by given slug.
	 *
	 * @param string $slug The requested slug.
	 *
	 * @return string|false
	 */
	public function get_taxonomy_name_by_slug( string $slug ): string|false {
		foreach ( $this->get_taxonomies() as $taxonomy_name => $settings ) {
			if ( $slug === $settings['slug'] ) {
				return $taxonomy_name;
			}
		}
		return false;
	}

	/**
	 * Return count of taxonomy default labels.
	 *
	 * @return int
	 */
	public function get_taxonomy_defaults_count(): int {
		$count = 0;
		foreach ( $this->get_taxonomy_defaults() as $labels ) {
			$count = $count + count( $labels );
		}
		return $count;
	}

	/**
	 * Return the taxonomy defaults.
	 *
	 * @return array
	 */
	private function get_taxonomy_defaults(): array {
		return array(
			WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY => array(
				'accounting_and_finance'                   => __( 'Accounting/Finance', 'personio-integration-light' ),
				'administrative_and_clerical'              => __( 'Administrative/Clerical', 'personio-integration-light' ),
				'banking_and_real_estate'                  => __( 'Banking/Real Estate/Mortgage Professionals', 'personio-integration-light' ),
				'building_construction_and_skilled_trades' => __( 'Building Construction/Skilled Trades', 'personio-integration-light' ),
				'business_and_strategic_development'       => __( 'Business/Strategic Management', 'personio-integration-light' ),
				'creative_and_design'                      => __( 'Creative/Design', 'personio-integration-light' ),
				'customer_support_and_client_care'         => __( 'Customer Support/Client Care', 'personio-integration-light' ),
				'editorial_and_writing'                    => __( 'Editorial/Writing', 'personio-integration-light' ),
				'engineering'                              => __( 'Engineering', 'personio-integration-light' ),
				'food_services_and_hospitality'            => __( 'Food Services/Hospitality', 'personio-integration-light' ),
				'human_resources'                          => __( 'Human Resources', 'personio-integration-light' ),
				'installation_and_maintenance_repair'      => __( 'Installation/Maintenance/Repair', 'personio-integration-light' ),
				'it_software'                              => __( 'IT/Software Development', 'personio-integration-light' ),
				'legal'                                    => __( 'Legal', 'personio-integration-light' ),
				'logistics_and_transportation'             => __( 'Logistics/Transportation', 'personio-integration-light' ),
				'marketing_and_product'                    => __( 'Marketing/Product', 'personio-integration-light' ),
				'medical_health'                           => __( 'Medical/Health', 'personio-integration-light' ),
				'other'                                    => __( 'Other', 'personio-integration-light' ),
				'production_and_operations'                => __( 'Production/Operations', 'personio-integration-light' ),
				'project_and_program_management'           => __( 'Project/Program Management', 'personio-integration-light' ),
				'quality_assurance_and_saftey'             => __( 'Quality Assurance/Safety', 'personio-integration-light' ),
				'rd_and_science'                           => __( 'R&D/Science', 'personio-integration-light' ),
				'sales_and_business_development'           => __( 'Sales/Business Development', 'personio-integration-light' ),
				'security_and_protective_services'         => __( 'Security/Protective Services', 'personio-integration-light' ),
				'training_instruction'                     => __( 'Training/Instruction', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION => array(
				'acturial_analysis'                        => __( 'Actuarial Analysis', 'personio-integration-light' ),
				'bookkeeping'                              => __( 'Bookkeeping/General Ledger', 'personio-integration-light' ),
				'financial_control'                        => __( 'Financial Control', 'personio-integration-light' ),
				'corporate_finance'                        => __( 'Corporate Finance', 'personio-integration-light' ),
				'accounts_payable_and_receivable'          => __( 'Accounts Payable/Receivable', 'personio-integration-light' ),
				'financial_reporting'                      => __( 'Financial Planning/Advising', 'personio-integration-light' ),
				'financial_analysis'                       => __( 'Financial Analysis/Research/Reporting', 'personio-integration-light' ),
				'corporate_accounting'                     => __( 'Corporate Accounting', 'personio-integration-light' ),
				'fund_accounting'                          => __( 'Fund Accounting', 'personio-integration-light' ),
				'claims_review'                            => __( 'Claims Review and Adjusting', 'personio-integration-light' ),
				'securities_analysis'                      => __( 'Securities Analysis/Research', 'personio-integration-light' ),
				'real_estate_appraisal'                    => __( 'Real Estate Appraisal', 'personio-integration-light' ),
				'real_estate_leasing'                      => __( 'Real Estate Leasing/Acquisition', 'personio-integration-light' ),
				'collections'                              => __( 'Collections', 'personio-integration-light' ),
				'investment_management'                    => __( 'Investment Management', 'personio-integration-light' ),
				'credit_review'                            => __( 'Credit Review/Analysis', 'personio-integration-light' ),
				'risk_management'                          => __( 'Risk Management/Compliance', 'personio-integration-light' ),
				'tax_assessment'                           => __( 'Tax Assessment and Collections', 'personio-integration-light' ),
				'tax_accounting'                           => __( 'Tax Accounting', 'personio-integration-light' ),
				'policy_underwriting'                      => __( 'Policy Underwriting', 'personio-integration-light' ),
				'financial_products_sales'                 => __( 'Financial Products Sales/Brokerage', 'personio-integration-light' ),
				'audit'                                    => __( 'Audit', 'personio-integration-light' ),
				'general_other_accounting_finance'         => __( 'General/Other: Accounting/Finance', 'personio-integration-light' ),
				'records_management'                       => __( 'Filing/Records Management', 'personio-integration-light' ),
				'executive_support'                        => __( 'Executive Support', 'personio-integration-light' ),
				'data_entry'                               => __( 'Data Entry/Order Processing', 'personio-integration-light' ),
				'reception'                                => __( 'Front Desk/Reception', 'personio-integration-light' ),
				'property_management'                      => __( 'Property Management', 'personio-integration-light' ),
				'office_management'                        => __( 'Office Management', 'personio-integration-light' ),
				'administrative'                           => __( 'Administrative Support', 'personio-integration-light' ),
				'claims_processing'                        => __( 'Claims Processing', 'personio-integration-light' ),
				'transaction'                              => __( 'Transcription', 'personio-integration-light' ),
				'secretary'                                => __( 'Paralegal & Legal Secretary', 'personio-integration-light' ),
				'general_other_administrative_clerical'    => __( 'General/Other: Administrative/Clerical', 'personio-integration-light' ),
				'loan_officer_and_originator'              => __( 'Loan Officer/Originator', 'personio-integration-light' ),
				'escrow_officer_and_manager'               => __( 'Escrow Officer/Manager', 'personio-integration-light' ),
				'store_and_branch_management'              => __( 'Store/Branch Management', 'personio-integration-light' ),
				'mortgage_broker'                          => __( 'Mortgage Broker', 'personio-integration-light' ),
				'real_estate_agent_and_broker'             => __( 'Real Estate Agent/Broker', 'personio-integration-light' ),
				'real_estate_law'                          => __( 'Real Estate Law', 'personio-integration-light' ),
				'credit_manager'                           => __( 'Credit Manager', 'personio-integration-light' ),
				'bank_teller'                              => __( 'Bank Teller', 'personio-integration-light' ),
				'underwriter'                              => __( 'Underwriter', 'personio-integration-light' ),
				'title_officer_and_closer'                 => __( 'Title Officer/Closer', 'personio-integration-light' ),
				'site_superintendent'                      => __( 'Site Superintendent', 'personio-integration-light' ),
				'concrete_and_masonry'                     => __( 'Concrete and Masonry', 'personio-integration-light' ),
				'heavy_equipment_operation'                => __( 'Heavy Equipment Operation', 'personio-integration-light' ),
				'roofing'                                  => __( 'Roofing', 'personio-integration-light' ),
				'electrician'                              => __( 'Electrician', 'personio-integration-light' ),
				'flooring_and_tiling_and_painting_and_wallpapering' => __( 'Flooring/Tiling/Painting/Wallpapering', 'personio-integration-light' ),
				'hvac'                                     => __( 'HVAC', 'personio-integration-light' ),
				'plumbing_and_pipefitting'                 => __( 'Plumbing/Pipefitting', 'personio-integration-light' ),
				'ironwork_and_metal_fabrication'           => __( 'Ironwork/Metal Fabrication', 'personio-integration-light' ),
				'cad_and_drafting'                         => __( 'CAD/Drafting', 'personio-integration-light' ),
				'surveying'                                => __( 'Surveying', 'personio-integration-light' ),
				'sheetrock_and_plastering'                 => __( 'Sheetrock/Plastering', 'personio-integration-light' ),
				'carpentry_and_framing'                    => __( 'Carpentry/Framing', 'personio-integration-light' ),
				'general_and_other_building_construction_and_skilled_trades' => __( 'General/Other: Construction/Skilled Trades', 'personio-integration-light' ),
				'business_analysis_and_research'           => __( 'Business Analysis/Research', 'personio-integration-light' ),
				'managerial_consulting'                    => __( 'Managerial Consulting', 'personio-integration-light' ),
				'franchise_business_ownership'             => __( 'Franchise-Business Ownership', 'personio-integration-light' ),
				'business_unit_management'                 => __( 'Business Unit Management', 'personio-integration-light' ),
				'president_and_top_executive'              => __( 'President/Top Executive', 'personio-integration-light' ),
				'public_health_administration'             => __( 'Public Health Administration', 'personio-integration-light' ),
				'hotel_and_lodging_management'             => __( 'Hotel/Lodging Management', 'personio-integration-light' ),
				'hospital_and_clinic_administration'       => __( 'Hospital/Clinic Administration', 'personio-integration-light' ),
				'mergers_and_acquisitions'                 => __( 'Mergers and Acquisitions', 'personio-integration-light' ),
				'restaurant_management'                    => __( 'Restaurant Management', 'personio-integration-light' ),
				'school_and_college_administration'        => __( 'School/College Administration', 'personio-integration-light' ),
				'town_and_city_planning'                   => __( 'Town/City Planning', 'personio-integration-light' ),
				'strategic_planning_and_intelligence'      => __( 'Strategic Planning/Intelligence', 'personio-integration-light' ),
				'general_and_other_business_and_strategic_management' => __( 'General/Other: Business/Strategic Management', 'personio-integration-light' ),
				'architecture_and_interior_design'         => __( 'Architecture/Interior Design', 'personio-integration-light' ),
				'computer_animation_multimedia'            => __( 'Computer Animation & Multimedia', 'personio-integration-light' ),
				'creative_direction_and_lead'              => __( 'Creative Direction/Lead', 'personio-integration-light' ),
				'graphic_arts_and_illustration'            => __( 'Graphic Arts/Illustration', 'personio-integration-light' ),
				'industrial_design'                        => __( 'Industrial Design', 'personio-integration-light' ),
				'fashion_accessories_design'               => __( 'Fashion & Accessories Design', 'personio-integration-light' ),
				'photography_and_videography'              => __( 'Photography and Videography', 'personio-integration-light' ),
				'web_and_ui_and_ux_design'                 => __( 'Web/UI/UX Design', 'personio-integration-light' ),
				'advertising_writing_creative'             => __( 'Advertising Writing (Creative)', 'personio-integration-light' ),
				'general_and_other_creative_and_design'    => __( 'General/Other: Creative/Design', 'personio-integration-light' ),
				'call_center'                              => __( 'Call Center', 'personio-integration-light' ),
				'flight_attendant'                         => __( 'Flight Attendant', 'personio-integration-light' ),
				'hair_cutting_and_styling'                 => __( 'Hair Cutting/Styling', 'personio-integration-light' ),
				'retail_customer_service'                  => __( 'Retail Customer Service', 'personio-integration-light' ),
				'account_management_non_commissioned'      => __( 'Account Management (Non-Commissioned)', 'personio-integration-light' ),
				'customer_training'                        => __( 'Customer Training', 'personio-integration-light' ),
				'reservations_and_ticketing'               => __( 'Reservations/Ticketing', 'personio-integration-light' ),
				'general_and_other_customer_support_and_client_care' => __( 'General/Other: Customer Support/Client Care', 'personio-integration-light' ),
				'technical_customer_service'               => __( 'Technical Customer Service', 'personio-integration-light' ),
				'documentation_and_technical_writing'      => __( 'Documentation/Technical Writing', 'personio-integration-light' ),
				'journalism'                               => __( 'Journalism', 'personio-integration-light' ),
				'digital_content_development'              => __( 'Digital Content Development', 'personio-integration-light' ),
				'editing_proofreading'                     => __( 'Editing & Proofreading', 'personio-integration-light' ),
				'general_and_other_editorial_and_writing'  => __( 'General/Other: Editorial/Writing', 'personio-integration-light' ),
				'translation_and_interpretation'           => __( 'Translation/Interpretation', 'personio-integration-light' ),
				'civil__structural_engineering'            => __( 'Civil & Structural Engineering', 'personio-integration-light' ),
				'bio_engineering'                          => __( 'Bio-Engineering', 'personio-integration-light' ),
				'chemical_engineering'                     => __( 'Chemical Engineering', 'personio-integration-light' ),
				'electrical_and_electronics_engineering'   => __( 'Electrical/Electronics Engineering', 'personio-integration-light' ),
				'energy_and_nuclear_engineering'           => __( 'Energy/Nuclear Engineering', 'personio-integration-light' ),
				'rf_and_wireless_engineering'              => __( 'RF/Wireless Engineering', 'personio-integration-light' ),
				'aeronautic_and_avionic_engineering'       => __( 'Aeronautic/Avionic Engineering', 'personio-integration-light' ),
				'mechanical_engineering'                   => __( 'Mechanical Engineering', 'personio-integration-light' ),
				'systems_and_process_engineering'          => __( 'Systems/Process Engineering', 'personio-integration-light' ),
				'industrial_and_manufacturing_engineering' => __( 'Industrial/Manufacturing Engineering', 'personio-integration-light' ),
				'naval_architecture_and_marine_engineering' => __( 'Naval Architecture/Marine Engineering', 'personio-integration-light' ),
				'environmental_and_geological_engineering' => __( 'Environmental and Geological Engineering', 'personio-integration-light' ),
				'general_and_other_engineering'            => __( 'General/Other: Engineering', 'personio-integration-light' ),
				'food_beverage_serving'                    => __( 'Food & Beverage Serving', 'personio-integration-light' ),
				'host_and_hostess'                         => __( 'Host/Hostess', 'personio-integration-light' ),
				'guest_services_and_concierge'             => __( 'Guest Services/Concierge', 'personio-integration-light' ),
				'food_preparation_and_cooking'             => __( 'Food Preparation/Cooking', 'personio-integration-light' ),
				'guide_tour'                               => __( 'Guide (Tour)', 'personio-integration-light' ),
				'front_desk_and_reception'                 => __( 'Front Desk/Reception', 'personio-integration-light' ),
				'wine_steward_sommelier'                   => __( 'Wine Steward (Sommelier)', 'personio-integration-light' ),
				'general_and_other_food_services_and_hospitality' => __( 'General/Other: Food Services', 'personio-integration-light' ),
				'corporate_development_and_training'       => __( 'Corporate Development and Training', 'personio-integration-light' ),
				'compensation_and_benefits_policy'         => __( 'Compensation/Benefits Policy', 'personio-integration-light' ),
				'diversity_management_and_eeo_and_compliance' => __( 'Diversity Management/EEO/Compliance', 'personio-integration-light' ),
				'academic_admissions_and_advising'         => __( 'Academic Admissions and Advising', 'personio-integration-light' ),
				'payroll_and_benefits_administration'      => __( 'Payroll and Benefits Administration', 'personio-integration-light' ),
				'recruiting_and_sourcing'                  => __( 'Recruiting/Sourcing', 'personio-integration-light' ),
				'hr_systems_administration'                => __( 'HR Systems Administration', 'personio-integration-light' ),
				'general_and_other_human_resources'        => __( 'General/Other: Human Resources', 'personio-integration-light' ),
				'computer_and_electronics_and_telecomm_install_and_maintain_and_repair' => __( 'Computer/Electronics/Telecomm Install/Maintain/Repair', 'personio-integration-light' ),
				'oil_rig_pipeline_install_and_maintain_and_repair' => __( 'Oil Rig & Pipeline Install/Maintain/Repair', 'personio-integration-light' ),
				'facilities_maintenance'                   => __( 'Facilities Maintenance', 'personio-integration-light' ),
				'janitorial_cleaning'                      => __( 'Janitorial & Cleaning', 'personio-integration-light' ),
				'vehicle_repair_and_maintenance'           => __( 'Vehicle Repair and Maintenance', 'personio-integration-light' ),
				'wire_and_cable_install_and_maintain_and_repair' => __( 'Wire and Cable Install/Maintain/Repair', 'personio-integration-light' ),
				'landscaping'                              => __( 'Landscaping', 'personio-integration-light' ),
				'equipment_install_and_maintain_and_repair' => __( 'Equipment Install/Maintain/Repair', 'personio-integration-light' ),
				'locksmith'                                => __( 'Locksmith', 'personio-integration-light' ),
				'general_and_other_installation_and_maintenance_and_repair' => __( 'General/Other: Installation/Maintenance/Repair', 'personio-integration-light' ),
				'usability_and_information_architecture'   => __( 'Usability/Information Architecture', 'personio-integration-light' ),
				'desktop_service_and_support'              => __( 'Desktop Service and Support', 'personio-integration-light' ),
				'computer_and_network_security'            => __( 'Computer/Network Security', 'personio-integration-light' ),
				'database_development_and_administration'  => __( 'Database Development/Administration', 'personio-integration-light' ),
				'enterprise_software_implementation_consulting' => __( 'Enterprise Software Implementation & Consulting', 'personio-integration-light' ),
				'it_project_management'                    => __( 'IT Project Management', 'personio-integration-light' ),
				'software_and_system_architecture'         => __( 'Software/System Architecture', 'personio-integration-light' ),
				'software_and_web_development'             => __( 'Software/Web Development', 'personio-integration-light' ),
				'network_and_server_administration'        => __( 'Network and Server Administration', 'personio-integration-light' ),
				'systems_analysis__it'                     => __( 'Systems Analysis - IT', 'personio-integration-light' ),
				'telecommunications_administration_and_management' => __( 'Telecommunications Administration/Management', 'personio-integration-light' ),
				'general_and_other_it_software'            => __( 'General/Other: IT/Software Development', 'personio-integration-light' ),
				'labor__employment_law'                    => __( 'Labor & Employment Law', 'personio-integration-light' ),
				'patent_and_ip_law'                        => __( 'Patent/IP Law', 'personio-integration-light' ),
				'regulatory_and_compliance_law'            => __( 'Regulatory/Compliance Law', 'personio-integration-light' ),
				'tax_law'                                  => __( 'Tax Law', 'personio-integration-light' ),
				'attorney'                                 => __( 'Attorney', 'personio-integration-light' ),
				'contracts_administration'                 => __( 'Contracts Administration', 'personio-integration-light' ),
				'paralegal__legal_secretary'               => __( 'Paralegal & Legal Secretary', 'personio-integration-light' ),
				'general_and_other_legal'                  => __( 'General/Other: Legal', 'personio-integration-light' ),
				'car_van_and_bus_driving'                  => __( 'Car, Van and Bus Driving', 'personio-integration-light' ),
				'train_or_rail_operator'                   => __( 'Train or Rail Operator', 'personio-integration-light' ),
				'purchasing_goods_and_services'            => __( 'Purchasing Goods and Services', 'personio-integration-light' ),
				'piloting_air_and_marine'                  => __( 'Piloting: Air and Marine', 'personio-integration-light' ),
				'cargo_and_baggage_handling'               => __( 'Cargo and Baggage Handling', 'personio-integration-light' ),
				'hazardous_materials_handling'             => __( 'Hazardous Materials Handling', 'personio-integration-light' ),
				'merchandise_planning_and_buying'          => __( 'Merchandise Planning and Buying', 'personio-integration-light' ),
				'import_and_export_administration'         => __( 'Import/Export Administration', 'personio-integration-light' ),
				'cost_estimating'                          => __( 'Cost Estimating', 'personio-integration-light' ),
				'messenger_and_courier'                    => __( 'Messenger/Courier', 'personio-integration-light' ),
				'truck_driving'                            => __( 'Truck Driving', 'personio-integration-light' ),
				'supplier_management_and_vendor_management' => __( 'Supplier Management/Vendor Management', 'personio-integration-light' ),
				'equipment_and_forklift_and_crane_operation' => __( 'Equipment/Forklift/Crane Operation', 'personio-integration-light' ),
				'inventory_planning_and_management'        => __( 'Inventory Planning and Management', 'personio-integration-light' ),
				'vehicle_dispatch_routing_and_scheduling'  => __( 'Vehicle Dispatch, Routing and Scheduling', 'personio-integration-light' ),
				'shipping_and_receiving_and_warehousing'   => __( 'Shipping and Receiving/Warehousing', 'personio-integration-light' ),
				'general_and_other_logistics_and_transportation' => __( 'General/Other: Logistics/Transportation', 'personio-integration-light' ),
				'visual_and_display_merchandising'         => __( 'Visual/Display Merchandising', 'personio-integration-light' ),
				'brand_and_product_marketing'              => __( 'Brand/Product Marketing', 'personio-integration-light' ),
				'direct_marketing_crm'                     => __( 'Direct Marketing (CRM)', 'personio-integration-light' ),
				'events_and_promotional_marketing'         => __( 'Events/Promotional Marketing', 'personio-integration-light' ),
				'investor_and_public_and_media_relations'  => __( 'Investor and Public/Media Relations', 'personio-integration-light' ),
				'marketing_communications'                 => __( 'Marketing Communications', 'personio-integration-light' ),
				'market_research'                          => __( 'Market Research', 'personio-integration-light' ),
				'media_planning_and_buying'                => __( 'Media Planning and Buying', 'personio-integration-light' ),
				'marketing_production_and_traffic'         => __( 'Marketing Production/Traffic', 'personio-integration-light' ),
				'product_management'                       => __( 'Product Management', 'personio-integration-light' ),
				'telemarketing'                            => __( 'Telemarketing', 'personio-integration-light' ),
				'copy_writing_and_editing'                 => __( 'Copy Writing/Editing', 'personio-integration-light' ),
				'general_and_other_marketing_and_product'  => __( 'General/Other: Marketing/Product', 'personio-integration-light' ),
				'healthcare_aid'                           => __( 'Healthcare Aid', 'personio-integration-light' ),
				'pharmacy'                                 => __( 'Pharmacy', 'personio-integration-light' ),
				'nutrition_and_diet'                       => __( 'Nutrition and Diet', 'personio-integration-light' ),
				'nursing'                                  => __( 'Nursing', 'personio-integration-light' ),
				'laboratory_and_pathology'                 => __( 'Laboratory/Pathology', 'personio-integration-light' ),
				'physician_assistant_and_nurse_practitioner' => __( 'Physician Assistant/Nurse Practitioner', 'personio-integration-light' ),
				'optical'                                  => __( 'Optical', 'personio-integration-light' ),
				'medical_therapy_and_rehab_services'       => __( 'Medical Therapy/Rehab Services', 'personio-integration-light' ),
				'medical_practitioner'                     => __( 'Medical Practitioner', 'personio-integration-light' ),
				'mental_health'                            => __( 'Mental Health', 'personio-integration-light' ),
				'medical_imaging'                          => __( 'Medical Imaging', 'personio-integration-light' ),
				'emt_and_paramedic'                        => __( 'EMT/Paramedic', 'personio-integration-light' ),
				'social_service'                           => __( 'Social Service', 'personio-integration-light' ),
				'sports_medicine'                          => __( 'Sports Medicine', 'personio-integration-light' ),
				'veterinary_and_animal_care'               => __( 'Veterinary/Animal Care', 'personio-integration-light' ),
				'dental_assistant_and_hygienist'           => __( 'Dental Assistant/Hygienist', 'personio-integration-light' ),
				'dental_practitioner'                      => __( 'Dental Practitioner', 'personio-integration-light' ),
				'general_and_other_medical_and_health'     => __( 'General/Other: Medical/Health', 'personio-integration-light' ),
				'work_at_home'                             => __( 'Work at Home', 'personio-integration-light' ),
				'career_fair'                              => __( 'Career Fair', 'personio-integration-light' ),
				'other'                                    => __( 'Other', 'personio-integration-light' ),
				'waste_pick_up_and_removal'                => __( 'Waste Pick-up and Removal', 'personio-integration-light' ),
				'operations_and_plant_management'          => __( 'Operations/Plant Management', 'personio-integration-light' ),
				'equipment_operations'                     => __( 'Equipment Operations', 'personio-integration-light' ),
				'scientific_and_technical_production'      => __( 'Scientific/Technical Production', 'personio-integration-light' ),
				'layout_prepress_printing_binding_operations' => __( 'Layout, Prepress, Printing, & Binding Operations', 'personio-integration-light' ),
				'assembly_and_assembly_line'               => __( 'Assembly/Assembly Line', 'personio-integration-light' ),
				'moldmaking_and_casting'                   => __( 'Moldmaking/Casting', 'personio-integration-light' ),
				'metal_fabrication_and_welding'            => __( 'Metal Fabrication and Welding', 'personio-integration-light' ),
				'audio_and_video_broadcast_postproduction' => __( 'Audio/Video Broadcast & Postproduction', 'personio-integration-light' ),
				'sewing_and_tailoring'                     => __( 'Sewing and Tailoring', 'personio-integration-light' ),
				'laundry_and_dry_cleaning_operations'      => __( 'Laundry and Dry-Cleaning Operations', 'personio-integration-light' ),
				'machining_and_cnc'                        => __( 'Machining/CNC', 'personio-integration-light' ),
				'general_and_other_production_and_operations' => __( 'General/Other: Production/Operations', 'personio-integration-light' ),
				'event_planning_and_coordination'          => __( 'Event Planning/Coordination', 'personio-integration-light' ),
				'program_management'                       => __( 'General/Other: Project/Program Management', 'personio-integration-light' ),
				'project_management'                       => __( 'IT Project Management', 'personio-integration-light' ),
				'general_and_other_project_and_program_management' => __( 'General/Other: Project/Program Management', 'personio-integration-light' ),
				'occupational_health_and_safety'           => __( 'Occupational Health and Safety', 'personio-integration-light' ),
				'building_and_construction_inspection'     => __( 'Building/Construction Inspection', 'personio-integration-light' ),
				'fraud_investigation'                      => __( 'Fraud Investigation', 'personio-integration-light' ),
				'iso_certification'                        => __( 'ISO Certification', 'personio-integration-light' ),
				'food_safety_and_inspection'               => __( 'Food Safety and Inspection', 'personio-integration-light' ),
				'production_quality_assurance'             => __( 'Production Quality Assurance', 'personio-integration-light' ),
				'six_sigma_and_black_belt_and_tqm'         => __( 'Six Sigma/Black Belt/TQM', 'personio-integration-light' ),
				'software_quality_assurance'               => __( 'Software Quality Assurance', 'personio-integration-light' ),
				'vehicle_inspection'                       => __( 'Vehicle Inspection', 'personio-integration-light' ),
				'environmental_protection_and_conservation' => __( 'Environmental Protection/Conservation', 'personio-integration-light' ),
				'general_and_other_quality_assurance_and_safety' => __( 'General/Other: Quality Assurance/Safety', 'personio-integration-light' ),
				'biological_and_chemical_research'         => __( 'Biological/Chemical Research', 'personio-integration-light' ),
				'materials_and_physical_research'          => __( 'Materials/Physical Research', 'personio-integration-light' ),
				'mathematical_and_statistical_research'    => __( 'Mathematical/Statistical Research', 'personio-integration-light' ),
				'clinical_research'                        => __( 'Clinical Research', 'personio-integration-light' ),
				'new_product_rd'                           => __( 'New Product R&D', 'personio-integration-light' ),
				'pharmaceutical_research'                  => __( 'Pharmaceutical Research', 'personio-integration-light' ),
				'environmental_and_geological_testing_analysis' => __( 'Environmental/Geological Testing & Analysis', 'personio-integration-light' ),
				'general_and_other_r_and_d_and_science'    => __( 'General/Other: R&D/Science', 'personio-integration-light' ),
				'account_management_commissioned'          => __( 'Account Management (Commissioned)', 'personio-integration-light' ),
				'field_sales'                              => __( 'Field Sales', 'personio-integration-light' ),
				'business_development_and_new_accounts'    => __( 'Business Development/New Accounts', 'personio-integration-light' ),
				'retail_and_counter_sales_and_cashier'     => __( 'Retail/Counter Sales and Cashier', 'personio-integration-light' ),
				'wholesale_and_reselling_sales'            => __( 'Wholesale/Reselling Sales', 'personio-integration-light' ),
				'international_sales'                      => __( 'International Sales', 'personio-integration-light' ),
				'fundraising'                              => __( 'Fundraising', 'personio-integration-light' ),
				'technical_presales_support__technical_sales' => __( 'Technical Presales Support & Technical Sales', 'personio-integration-light' ),
				'telesales'                                => __( 'Telesales', 'personio-integration-light' ),
				'travel_agent_and_ticket_sales'            => __( 'Travel Agent/Ticket Sales', 'personio-integration-light' ),
				'media_and_advertising_sales'              => __( 'Media and Advertising Sales', 'personio-integration-light' ),
				'insurance_agent_and_broker'               => __( 'Insurance Agent/Broker', 'personio-integration-light' ),
				'sales_support_and_assistance'             => __( 'Sales Support/Assistance', 'personio-integration-light' ),
				'financial_products_sales_and_brokerage'   => __( 'Financial Products Sales/Brokerage', 'personio-integration-light' ),
				'general_and_other_sales_and_business_development' => __( 'General/Other: Sales/Business Development', 'personio-integration-light' ),
				'customs_and_immigration'                  => __( 'Customs/Immigration', 'personio-integration-light' ),
				'firefighting_and_rescue'                  => __( 'Firefighting and Rescue', 'personio-integration-light' ),
				'airport_security_and_screening'           => __( 'Airport Security and Screening', 'personio-integration-light' ),
				'store_security_and_loss_prevention'       => __( 'Store Security/Loss Prevention', 'personio-integration-light' ),
				'security_intelligence_analysis'           => __( 'Security Intelligence & Analysis', 'personio-integration-light' ),
				'police_law_enforcement'                   => __( 'Police-Law Enforcement', 'personio-integration-light' ),
				'security_guard'                           => __( 'Security Guard', 'personio-integration-light' ),
				'correctional_officer'                     => __( 'Correctional Officer', 'personio-integration-light' ),
				'military_combat'                          => __( 'Military Combat', 'personio-integration-light' ),
				'general_and_other_security_and_protective_services' => __( 'General/Other: Security/Protective Services', 'personio-integration-light' ),
				'corporate_development'                    => __( 'Corporate Development and Training', 'personio-integration-light' ),
				'continuing_and_adult'                     => __( 'Continuing/Adult', 'personio-integration-light' ),
				'elementary_school'                        => __( 'Elementary School', 'personio-integration-light' ),
				'software_and_web_training'                => __( 'Software/Web Training', 'personio-integration-light' ),
				'early_childhood_care'                     => __( 'Early Childhood Care & Development', 'personio-integration-light' ),
				'university'                               => __( 'University', 'personio-integration-light' ),
				'junior_and_high_school'                   => __( 'Junior/High School', 'personio-integration-light' ),
				'classroom_teaching'                       => __( 'Classroom Teaching', 'personio-integration-light' ),
				'special_education'                        => __( 'Special Education', 'personio-integration-light' ),
				'fitness_and_sports'                       => __( 'Fitness & Sports Training/Instruction', 'personio-integration-light' ),
				'general_other_training_and_instruction'   => __( 'General/Other: Training/Instruction', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE => array(
				'permanent'       => __( 'Permanent employee', 'personio-integration-light' ),
				'intern'          => __( 'Intern', 'personio-integration-light' ),
				'trainee'         => __( 'Trainee', 'personio-integration-light' ),
				'freelance'       => __( 'Freelance', 'personio-integration-light' ),
				'temporary'       => __( 'Temporary', 'personio-integration-light' ),
				'working_student' => __( 'Working Student', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE   => array(
				'full-time'         => __( 'full-time', 'personio-integration-light' ),
				'part-time'         => __( 'part-time', 'personio-integration-light' ),
				'full-or-part-time' => __( 'full- or part-time', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY  => array(
				'entry-level' => __( 'entry-level', 'personio-integration-light' ),
				'experienced' => __( 'experienced', 'personio-integration-light' ),
				'executive'   => __( 'executive', 'personio-integration-light' ),
				'student'     => __( 'student', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE => array(
				'lt-1'  => __( 'less than 1 year', 'personio-integration-light' ),
				'1-2'   => __( '1-2 years', 'personio-integration-light' ),
				'2-5'   => __( '2-5 years', 'personio-integration-light' ),
				'5-7'   => __( '5-7 years', 'personio-integration-light' ),
				'7-10'  => __( '7-10 years', 'personio-integration-light' ),
				'10-15' => __( '10-15 years', 'personio-integration-light' ),
				'ht15'  => __( 'more than 15 years', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES  => Languages::get_instance()->get_languages(),
		);
	}

	/**
	 * Get language-specific defaults for a requested taxonomy terms.
	 *
	 * @param string $taxonomy The requested taxonomy.
	 * @param string $language_code The requested language-name (e.g. 'de').
	 *
	 * @return array
	 */
	public function get_default_terms_for_taxonomy( string $taxonomy, string $language_code = '' ): array {
		// set language in frontend to read the texts depending on main-language.
		$locale = get_locale();
		// switch to requested language.
		if ( ! is_admin() ) {
			// if no language is requested, use the current language.
			if ( empty( $language_code ) ) {
				$language_code = Languages::get_instance()->get_current_lang();
			}

			// get mappings.
			$language_mappings = Languages::get_instance()->get_lang_mappings( $language_code );

			// get WP-lang-compatible language-code.
			if ( empty( $language_code ) && ! empty( $language_mappings ) ) {
				$language_code = $language_mappings[0];
			} else {
				$language_code = Languages::get_instance()->get_fallback_language_name();
			}

			// switch the language.
			switch_to_locale( $language_code );
		}

		// get ALL defaults for all taxonomies as array.
		$array = $this->get_taxonomy_defaults();

		// revert the locale-setting.
		if ( ! is_admin() ) {
			switch_to_locale( $locale );
		}

		// return nothing of requested taxonomy does not have any defaults.
		if ( empty( $array[ $taxonomy ] ) ) {
			return array();
		}

		// return resulting defaults for requested taxonomy.
		return $array[ $taxonomy ];
	}

	/**
	 * Hide some taxonomy-columns in our own cpt-table.
	 *
	 * @param array     $hidden List of columns to hide.
	 * @param WP_Screen $screen Actual screen-object.
	 * @param bool      $use_defaults If defaults should be used.
	 *
	 * @return mixed
	 */
	public function hide_columns( array $hidden, WP_Screen $screen, bool $use_defaults ): array {
		if ( $use_defaults && PersonioPosition::get_instance()->get_name() === $screen->post_type ) {
			foreach ( $this->get_taxonomies() as $taxonomy_name => $settings ) {
				if ( ! empty( $settings['initiallyHideInTable'] ) && 1 === absint( $settings['initiallyHideInTable'] ) ) {
					$hidden[] = 'taxonomy-' . $taxonomy_name;
				}
			}
		}
		return $hidden;
	}

	/**
	 * Get list of taxonomy-labels for settings.
	 *
	 * @param array|bool $taxonomies Given list of enabled taxonomies.
	 *
	 * @return array
	 */
	public function get_labels_for_settings( array|bool $taxonomies ): array {
		if ( is_bool( $taxonomies ) ) {
			$taxonomies = array();
		}

		// get taxonomies.
		$labels = $this->get_taxonomy_labels_for_settings();

		/**
		 * Filter the taxonomy labels for template filter in listing before adding them to the settings.
		 *
		 * @since 2.3.0 Available since 2.3.0.
		 *
		 * @param array $labels List of labels.
		 * @param array $taxonomies List of taxonomies.
		 */
		return apply_filters( 'personio_integration_settings_get_list', $labels, $taxonomies );
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

		// delete all taxonomies.
		register_rest_route(
			'personio/v1',
			'/taxonomies/',
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_all' ),
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
		$taxonomies_labels_array = self::get_instance()->get_taxonomy_labels_for_settings();
		$taxonomies              = array();
		$count                   = 0;
		foreach ( self::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
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
	 * Delete all taxonomies which depends on our own custom post type.
	 *
	 * @return void
	 * @noinspection SqlResolve
	 */
	public function delete_all(): void {
		global $wpdb;

		// delete the content of all taxonomies.
		// -> hint: some will be newly insert after next wp-init.
		$taxonomies = self::get_instance()->get_taxonomies();
		$progress   = Helper::is_cli() ? \WP_CLI\Utils\make_progress_bar( 'Delete all local taxonomies', count( $taxonomies ) ) : false;
		foreach ( $taxonomies as $taxonomy_name => $settings ) {
			// get all terms with direct db access.
			$terms = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->prepare(
					'SELECT ' . $wpdb->terms . '.term_id
                    FROM ' . $wpdb->terms . '
                    INNER JOIN
                        ' . $wpdb->term_taxonomy . '
                        ON
                         ' . $wpdb->term_taxonomy . '.term_id = ' . $wpdb->terms . '.term_id
                    WHERE ' . $wpdb->term_taxonomy . '.taxonomy = %s',
					array( $taxonomy_name )
				)
			);

			// delete them.
			foreach ( $terms as $term ) {
				$wpdb->delete( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$wpdb->terms,
					array(
						'term_id' => $term->term_id,
					)
				);
			}

			// delete all taxonomy-entries.
			$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy_name ), array( '%s' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

			// cleanup options.
			delete_option( $taxonomy_name . '_children' );

			// log in debug-mode.
			if ( 1 === absint( get_option( 'personioIntegration_debug' ) ) ) {
				$log = new Log();
				$log->add_log( 'Taxonomy ' . $taxonomy_name . ' has been deleted.', 'success', 'import' );
			}

			// show progress.
			$progress ? $progress->tick() : false;
		}
		// finalize progress.
		$progress ? $progress->finish() : false;

		// output success-message.
		Helper::is_cli() ? \WP_CLI::success( count( $taxonomies ) . ' taxonomies from local database has been cleaned.' ) : false;
	}

	/**
	 * Set filter for taxonomies through attributes from the used PageBuilder.
	 *
	 * @param array $attributes List of pre-filtered attributes.
	 * @param array $attributes_set_by_pagebuilder List of unfiltered attributes, set by used pagebuilder.
	 *
	 * @return array
	 */
	public function filter_by_attributes( array $attributes, array $attributes_set_by_pagebuilder ): array {
		foreach ( $this->get_taxonomies() as $taxonomy ) {
			if ( ! empty( $attributes_set_by_pagebuilder[ $taxonomy['slug'] ] ) ) {
				$attributes[ $taxonomy['slug'] ] = absint( $attributes_set_by_pagebuilder[ $taxonomy['slug'] ] );
			}
		}
		return $attributes;
	}
}

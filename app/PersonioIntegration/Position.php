<?php
/**
 * File for handling single position as object.
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
use PersonioIntegrationLight\Plugin\Templates;
use SimpleXMLElement;
use WP_Term;

/**
 * Represents a single position.
 *
 * $data is an array which holds all contents for a position:
 * - post-data-rows (saved by their post-table-row-names)
 * - values for taxonomy-terms (saved by their taxonomy-names)
 * - custom Personio-settings (like personioId)
 *
 * Hint: personioId is a string.
 */
class Position {

	/**
	 * Array for all properties of this object.
	 *
	 * @var array
	 */
	protected array $data = array();

	/**
	 * List of all taxonomy-terms of this object.
	 *
	 * @var array
	 */
	private array $taxonomy_terms = array();

	/**
	 * Log-Object.
	 *
	 * @var Log
	 */
	private Log $log;

	/**
	 * Marker if debug-Mode is active
	 *
	 * @var bool
	 */
	private bool $debug;

	/**
	 * The post ID of this position.
	 *
	 * @depecated Please use get_id() instead.
	 *
	 * @var string
	 */
	public string $ID;

	/**
	 * Constructor for this position.
	 *
	 * @param int $post_id The post_id of this position.
	 */
	public function __construct( int $post_id ) {
		// get log-object.
		$this->log = new Log();

		// get debug-mode.
		$this->debug = 1 === absint( get_option( 'personioIntegration_debug' ) );

		if ( $post_id > 0 ) {
			// get the post as array to save it in this object.
			$post_array = get_post( $post_id, ARRAY_A );

			// if result is not an array, create an empty array.
			if ( ! is_array( $post_array ) ) {
				$post_array = array();
			}

			// if result is not our post-type, create an empty array.
			if ( ! empty( $post_array['post_type'] ) && PersonioPosition::get_instance()->get_name() !== $post_array['post_type'] ) {
				$post_array       = array();
				$this->data['ID'] = 0;
				return;
			}

			// set the WP_Post-settings in this object.
			$this->data = $post_array;

			// set deprecated ID param on object.
			$this->ID = $this->get_id();

			// set the main language for this position.
			$this->set_lang( Languages::get_instance()->get_main_language() );
		} else {
			$this->data['ID'] = 0;
		}
	}

	/**
	 * Saves the actual values of this object in the databases.
	 *
	 * @return void
	 */
	public function save(): void {
		// do not save anything without personioId.
		if ( empty( $this->get_personio_id() ) ) {
			$this->log->add_log( __( 'Position could not be saved as the PersonioId is missing.', 'personio-integration-light' ), 'error', 'import' );
			return;
		}

		// do not save anything without language setting.
		if ( empty( $this->get_lang() ) ) {
			/* translators: %1$s will be replaced by the Personio ID. */
			$this->log->add_log( sprintf( __( 'Position with PersonioId %1$s could not be saved as the position does not have a language set.', 'personio-integration-light' ), esc_html( $this->data['personioId'] ) ), 'error', 'import' );
			return;
		}

		$false = false;
		/**
		 * Filter if position should be imported.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param bool $false Return false to import this position.
		 * @param Position $this The object of the position.
		 */
		if ( apply_filters( 'personio_integration_check_requirement_to_import_single_position', $false, $this ) ) {
			return;
		}

		// set ordering if not set atm.
		if ( empty( $this->data['menu_order'] ) ) {
			$this->data['menu_order'] = 0;
		}

		// search for the personioID to get an existing post-object.
		if ( 0 === $this->get_id() ) {
			// set ID to 0.
			$this->data['ID'] = 0;

			// collect all posts.
			$posts = array();

			// get all positions with the given Personio ID.
			foreach ( Positions::get_instance()->get_positions( -1, array( 'personioid' => $this->data['personioId'] ) ) as $position_obj ) {
				$lang    = $this->get_lang();
				$post_id = $position_obj->get_id();
				/**
				 * Filter the post_id.
				 *
				 * Could return false to force a non-existing position.
				 *
				 * @since 1.0.0 Available since first release.
				 *
				 * @param int $post_id The post_id to check.
				 * @param string $lang The used language.
				 */
				if ( apply_filters( 'personio_integration_import_single_position_filter_existing', $post_id, $lang ) ) {
					$posts[] = $position_obj->get_id();
				}
			}
			if ( 1 === count( $posts ) ) {
				// get the post-id to update its data.
				$this->data['ID'] = $posts[0];
				// get the menu_order to obtain its value during update.
				$this->data['menu_order'] = get_post_field( 'menu_order', $posts[0] );
			} elseif ( 1 < count( $posts ) ) {
				// something is wrong.
				// -> delete all entries with this personioId without trash.
				// -> it will be saved as new entry after this.
				foreach ( $posts as $post_id ) {
					wp_delete_post( $post_id, true );
				}

				// log this event.
				$this->log->add_log( 'PersonioId ' . $this->data['personioId'] . ' existed in database multiple times. Cleanup done.', 'error', 'import' );
			}
		} else {
			// set ID to 0.
			$this->data['ID'] = 0;
		}

		$false = false;
		/**
		 * Filter if position should be imported after we get an ID.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param bool $false Return false to import this position.
		 * @param Position $this The object of the position.
		 */
		if ( apply_filters( 'personio_integration_prevent_import_of_single_position', $false, $this ) ) {
			return;
		}

		// prepare data to be saved
		// -> overwrite title and content only for the main language.
		$array = array(
			'ID'          => $this->get_id(),
			'post_status' => 'publish',
			'post_type'   => PersonioPosition::get_instance()->get_name(),
			'menu_order'  => absint( $this->data['menu_order'] ),
		);
		if ( Languages::get_instance()->get_main_language() === $this->get_lang() ) {
			$array['post_title']   = $this->data['post_title'];
			$array['post_content'] = $this->data['post_content'];
		} else {
			$array['post_title']   = get_post_field( 'post_title', $this->data['ID'] );
			$array['post_content'] = get_post_field( 'post_content', $this->data['ID'] );
		}

		/**
		 * Filter the prepared position-data just before its saved.
		 *
		 * @since 1.0.0 Available since first release.
		 *
		 * @param array $array The position data as array.
		 * @param Position $this The object we are in.
		 */
		$array = apply_filters( 'personio_integration_import_single_position_filter_before_saving', $array, $this );

		// save the position.
		$result = wp_insert_post( $array );

		// if error occurred log it.
		if ( is_wp_error( $result ) ) {
			// log this event.
			$this->log->add_log( 'Position with personioId ' . $this->data['personioId'] . ' could not be saved! Error: ' . $result->get_error_message(), 'error', 'import' );
		} elseif ( 0 === absint( $result ) ) {
			// log this event.
			$this->log->add_log( 'Position with personioId ' . $this->data['personioId'] . ' could not be saved! Got no error from WordPress.', 'error', 'import' );
		} elseif ( absint( $result ) > 0 ) {
			// save the post-ID in the object.
			$this->data['ID'] = absint( $result );

			/**
			 * Run hook for individual settings after Position has been saved (inserted or updated).
			 *
			 * @since 2.0.0 Available since 2.0.0.
			 *
			 * @param Position $this The object of this position.
			 */
			do_action( 'personio_integration_import_single_position_save', $this );

			// add the Personio ID.
			update_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_MAIN_CPT_PM_PID, $this->data['personioId'] );

			// assign the position to its terms.
			$taxonomies = Taxonomies::get_instance()->get_taxonomies();
			foreach ( $taxonomies as $taxonomy_name => $taxonomy ) {
				if ( ! empty( $taxonomy['attr']['rewrite']['slug'] ) ) {
					// first remove all existing relations if list is appended (but not for language-taxonomy and only for main language).
					if ( $taxonomy['append'] && WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES !== $taxonomy_name && $this->get_lang() === Languages::get_instance()->get_main_language() ) {
						wp_delete_object_term_relationships( $this->get_id(), array( $taxonomy_name ) );
					}

					// then assign the terms of this taxonomy to the object.
					$this->update_terms( $taxonomy_name, $taxonomy_name, $taxonomy['append'] );
				}
			}

			// add created at as post meta field.
			update_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_MAIN_CPT_CREATEDAT, strtotime( $this->data['createdAt'] ) );

			// add all language-specific titles.
			update_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_LANG_POSITION_TITLE . '_' . $this->get_lang(), $this->data['post_title'] );

			// convert the job description from JSON to array.
			$job_description = json_decode( $this->data['post_content'], true );

			// add all language-specific texts.
			update_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->get_lang(), $job_description );

			// get count of split texts to delete the existing ones.
			$max = absint( get_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->get_lang() . '_split', 0 ) );
			for ( $i = 0;$i < $max;$i++ ) {
				delete_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->get_lang() . '_' . $i );
			}

			// add the split language-specific texts.
			if ( ! empty( $job_description['jobDescription'] ) ) {
				foreach ( $job_description['jobDescription'] as $index => $description_part ) {
					update_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->get_lang() . '_' . $index, $description_part );
				}
			}

			// save the count of split texts.
			update_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->get_lang() . '_split', count( $job_description['jobDescription'] ) );

			/**
			 * Run hook for individual settings after all settings for the position have been saved.
			 *
			 * @since 3.0.0 Available since 3.0.0.
			 *
			 * @param Position $this The object of this position.
			 */
			do_action( 'personio_integration_import_single_position_save_finished', $this );

			// mark as changed.
			update_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_UPDATED, 1 );

			// add log in debug-mode.
			if ( false !== $this->debug ) {
				/* translators: %1$s will be replaced by the PersonioID, %2$s by the language name. */
				$this->log->add_log( sprintf( __( 'Position %1$s successfully imported or updated in %2$s.', 'personio-integration-light' ), esc_html( $this->get_personio_id() ), esc_html( Languages::get_instance()->get_language_title( $this->get_lang() ) ) ), 'success', 'import' );
			}
		}
	}

	/**
	 * Update terms of single taxonomy for this position.
	 *
	 * @param string $value The value of the term.
	 * @param string $taxonomy The taxonom the term is assigned to.
	 * @param bool   $append If we add the value or override existing values.
	 *
	 * @return void
	 */
	public function update_terms( string $value, string $taxonomy, bool $append ): void {
		if ( ! empty( $this->data[ $value ] ) ) {
			if ( is_array( $this->data[ $value ] ) ) {
				foreach ( $this->data[ $value ] as $value ) {
					$this->update_term( $value, $taxonomy, $append );
				}
			} else {
				$this->update_term( $this->data[ $value ], $taxonomy, $append );
			}
		}
	}

	/**
	 * Update single term of single taxonomy for this position.
	 *
	 * @param string $value    The value of the term.
	 * @param string $taxonomy The taxonom the term is assigned to.
	 * @param bool   $append   If we add the value or override existing values.
	 *
	 * @return void
	 */
	public function update_term( string $value, string $taxonomy, bool $append ): void {
		// get the term-object.
		$term = get_term_by( 'name', $value, $taxonomy );

		// if no term is found add it.
		if ( ! $term ) {
			$term_array = wp_insert_term( $value, $taxonomy );
			if ( ! is_wp_error( $term_array ) ) {
				$term = get_term( $term_array['term_id'], $taxonomy );
			} elseif ( false !== $this->debug ) {
				$this->log->add_log( 'Term ' . $value . ' could not be imported in ' . $taxonomy, 'error', 'import' );
			}
		}

		// assign the position to this term.
		if ( $term instanceof WP_Term ) {
			wp_set_post_terms( $this->data['ID'], array( $term->term_id ), $taxonomy, $append );
		}
	}

	/**
	 * Get a term field by a given taxonomy for this single position.
	 *
	 * @param string $taxonomy The taxonomy.
	 * @param string $field    The field.
	 * @param bool   $no_list Prevent usage of list, only use first term.
	 *
	 * @return string
	 */
	public function get_term_by_field( string $taxonomy, string $field, bool $no_list = false ): string {
		if ( empty( $this->taxonomy_terms[ $taxonomy ] ) ) {
			$taxonomy_terms = get_the_terms( $this->get_id(), $taxonomy );
			if ( ! is_wp_error( $taxonomy_terms ) ) {
				$this->taxonomy_terms[ $taxonomy ] = $taxonomy_terms;
			}
		}
		if ( ! empty( $this->taxonomy_terms[ $taxonomy ] ) ) {
			$term_string = '';
			if ( false === $no_list ) {
				foreach ( $this->taxonomy_terms[ $taxonomy ] as $term ) {
					if ( ! empty( $term_string ) ) {
						$term_string .= ', ';
					}
					$term_string .= $term->$field;
				}
			} elseif ( ! empty( $this->taxonomy_terms[ $taxonomy ] ) ) {
				$term_string = $this->taxonomy_terms[ $taxonomy ][0]->$field;
			}
			return $term_string;
		}
		return '';
	}

	/**
	 * Get a list of terms by a given taxonomy for this single position.
	 *
	 * @param string $taxonomy The taxonomy.
	 *
	 * @return array
	 */
	public function get_terms_by_field( string $taxonomy ): array {
		if ( empty( $this->taxonomy_terms[ $taxonomy ] ) ) {
			$this->taxonomy_terms[ $taxonomy ] = array();
			$taxonomy_terms                    = get_the_terms( $this->get_id(), $taxonomy );
			if ( is_array( $taxonomy_terms ) ) {
				$this->taxonomy_terms[ $taxonomy ] = $taxonomy_terms;
			}
		}

		// return list of terms.
		return $this->taxonomy_terms[ $taxonomy ];
	}

	/**
	 * Get the language-specific title of this position.
	 *
	 * @return string
	 */
	public function get_title(): string {
		if ( empty( $this->data['post_title'] ) ) {
			$this->set_title( get_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_LANG_POSITION_TITLE . '_' . $this->get_lang(), true ) );
		}

		$title = $this->data['post_title'];
		/**
		 * Filter the title of the position.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param string $title The title.
		 * @param Position $this The position object.
		 */
		return apply_filters( 'position_integration_position_title', $title, $this );
	}

	/**
	 * Set title for this position.
	 *
	 * @param string $title The title.
	 *
	 * @return void
	 */
	public function set_title( string $title ): void {
		$this->data['post_title'] = $title;
	}

	/**
	 * Get the PersonioId of this position.
	 *
	 * @return string
	 */
	public function get_personio_id(): string {
		if ( empty( $this->data['personioId'] ) ) {
			$this->set_personio_id( get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_MAIN_CPT_PM_PID, true ) );
		}
		return $this->data['personioId'];
	}

	/**
	 * Set Personio ID.
	 *
	 * @param string $personio_id The Personio ID.
	 *
	 * @return void
	 */
	public function set_personio_id( string $personio_id ): void {
		$this->data['personioId'] = $personio_id;
	}

	/**
	 * Get the details of this position.
	 *
	 * @return false|string
	 */
	public function get_excerpt(): false|string {
		return Templates::get_instance()->get_excerpt( $this, PersonioPosition::get_instance()->get_single_shortcode_attributes( array() ), true );
	}

	/**
	 * Check if the position-object is valid. It checks if it contains data and if the ID is set.
	 *
	 * @return bool
	 */
	public function is_valid(): bool {
		return ! empty( $this->data ) && ! empty( $this->data['ID'] );
	}

	/**
	 * Get the WP_Post-ID of this object.
	 *
	 * @return int
	 */
	public function get_id(): int {
		return absint( $this->data['ID'] );
	}

	/**
	 * Return the permalink for this position.
	 *
	 * @return string
	 */
	public function get_link(): string {
		$url = '#';
		if ( ! empty( $this->data['ID'] ) ) {
			$url = get_permalink( $this->data['ID'] );
		}

		/**
		 * Filter the public url of a single position.
		 *
		 * @since 3.2.0 Available since 3.2.0.
		 *
		 * @param string $url The URL.
		 * @param Position $this The object of the position.
		 */
		return apply_filters( 'personio_integration_single_url', $url, $this );
	}

	/**
	 * Return created at date as timestamp.
	 *
	 * @return string
	 */
	public function get_created_at(): string {
		$date = get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_MAIN_CPT_CREATEDAT, true );
		if ( empty( $date ) ) {
			return time() . '';
		}
		return $date;
	}

	/**
	 * Return the language of this object.
	 *
	 * @return string
	 */
	public function get_lang(): string {
		if ( empty( $this->data['personioLanguages'] ) ) {
			return Languages::get_instance()->get_main_language();
		}
		return $this->data['personioLanguages'];
	}

	/**
	 * Set the language.
	 *
	 * @param string $lang The language.
	 *
	 * @return void
	 */
	public function set_lang( string $lang ): void {
		// reset title if language will be changed.
		if ( ! empty( $this->data['personioLanguages'] ) && $lang !== $this->data['personioLanguages'] ) {
			$this->set_title( '' );
		}

		// set new language.
		$this->data['personioLanguages'] = $lang;
	}

	/**
	 * Return the language-specific post_content.
	 *
	 * @return array
	 */
	public function get_content(): array {
		return (array) get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->get_lang(), true );
	}

	/**
	 * Get the language-specific content of this position (aka jobDescriptions).
	 *
	 * @return array
	 */
	public function get_content_as_array(): array {
		$content = $this->get_content();
		if ( ! empty( $content['jobDescription'] ) ) {
			return $content['jobDescription'];
		}
		return array();
	}

	/**
	 * Set the content (only from import as XML).
	 *
	 * @param SimpleXMLElement $job_descriptions The description as XML.
	 *
	 * @return void
	 */
	public function set_content( SimpleXMLElement $job_descriptions ): void {
		$value = $job_descriptions;
		foreach ( $job_descriptions as $v ) {
			if ( 0 === $v->count() ) {
				$value = array( 'jobDescription' => array() );
			}
			if ( 1 === $v->count() ) {
				$value = array( 'jobDescription' => array( $v->jobDescription ) );
			}
		}
		$this->data['post_content'] = wp_json_encode( $value );
	}

	/**
	 * Set department.
	 *
	 * @param string $department The department.
	 *
	 * @return void
	 */
	public function set_department( string $department ): void {
		$this->data['personioDepartment'] = $department;
	}

	/**
	 * Set keywords. We save them as array.
	 *
	 * @param string $keywords List of keywords, separated by ','.
	 *
	 * @return void
	 */
	public function set_keywords( string $keywords ): void {
		$this->data['personioKeywords'] = explode( ',', $keywords );
	}

	/**
	 * Set office.
	 *
	 * @param string $office The office.
	 *
	 * @return void
	 */
	public function set_office( string $office ): void {
		$this->data['personioOffice'] = $office;
	}

	/**
	 * Set recruiting category.
	 *
	 * @param string $recruiting_category The recruitment category.
	 *
	 * @return void
	 */
	public function set_recruiting_category( string $recruiting_category ): void {
		$this->data['personioRecruitingCategory'] = $recruiting_category;
	}

	/**
	 * Set employment type.
	 *
	 * @param string $employment_type The employment type.
	 *
	 * @return void
	 */
	public function set_employment_type( string $employment_type ): void {
		$this->data['personioEmploymentType'] = $employment_type;
	}

	/**
	 * Set seniority.
	 *
	 * @param string $seniority The seniority.
	 *
	 * @return void
	 */
	public function set_seniority( string $seniority ): void {
		$this->data['personioSeniority'] = $seniority;
	}

	/**
	 * Set schedule.
	 *
	 * @param string $schedule The schedule.
	 *
	 * @return void
	 */
	public function set_schedule( string $schedule ): void {
		$this->data['personioSchedule'] = $schedule;
	}

	/**
	 * Set the years of experience.
	 *
	 * @param string $years_of_experience The years of experience.
	 *
	 * @return void
	 */
	public function set_years_of_experience( string $years_of_experience ): void {
		$this->data['personioExperience'] = $years_of_experience;
	}

	/**
	 * Set occupation.
	 *
	 * @param string $occupation The occupation.
	 *
	 * @return void
	 */
	public function set_occupation( string $occupation ): void {
		$this->data['personioOccupation'] = $occupation;
	}

	/**
	 * Set the occupation category.
	 *
	 * @param string $occupation_category The occupation category.
	 *
	 * @return void
	 */
	public function set_occupation_category( string $occupation_category ): void {
		$this->data['personioOccupationCategory'] = $occupation_category;
	}

	/**
	 * Set created at.
	 *
	 * @param string $created_at The created at-value.
	 *
	 * @return void
	 */
	public function set_created_at( string $created_at ): void {
		$this->data['createdAt'] = $created_at;
	}

	/**
	 * Return the application-URL (link to Personio career portal).
	 *
	 * @param bool $without_application True if application-hash should NOT be added.
	 *
	 * @return string
	 */
	public function get_application_url( bool $without_application = false ): string {
		return $this->get_personio_account()->get_application_url( $this, $without_application );
	}

	/**
	 * Return the Personio object for this position.
	 *
	 * @return Personio
	 */
	private function get_personio_account(): Personio {
		$url = Helper::get_personio_url();

		/**
		 * Filter the Personio account URL to use for the Personio object.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $url The main URL.
		 * @param Position $this The object of the requested position.
		 */
		return new Personio( apply_filters( 'personio_integration_get_personio_url', $url, $this ) );
	}

	/**
	 * Return all settings.
	 *
	 * @return array
	 */
	public function get_settings(): array {
		return $this->data;
	}

	/**
	 * Return a registered extension for this position.
	 *
	 * @param string $name Class-name of extension based on Position_Extends_Base.
	 *
	 * @return false|Position_Extensions_Base
	 */
	public function get_extension( string $name ): false|Position_Extensions_Base {
		// bail if name does not exist.
		if ( ! class_exists( $name ) ) {
			return false;
		}

		// return the object.
		return new $name( $this->get_id() );
	}

	/**
	 * Extend the data-setting for single position.
	 *
	 * @param string $setting_name The name of the setting.
	 * @param string $value Its value.
	 *
	 * @return void
	 */
	public function add_setting( string $setting_name, string $value ): void {
		$this->data[ $setting_name ] = $value;
	}

	/**
	 * Return requested settings.
	 *
	 * @param string $setting_name The name of the setting.
	 * @return string
	 */
	public function get_setting( string $setting_name ): string {
		// bail if setting does not exist.
		if ( empty( $this->data[ $setting_name ] ) ) {
			return '';
		}

		// return the value.
		return $this->data[ $setting_name ];
	}

	/**
	 * Return whether this position is actually visible (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_visible(): bool {
		return ! empty(
			Positions::get_instance()->get_positions(
				1,
				array(
					'ids'                   => array( $this->get_id() ),
					'personio_run_in_admin' => true,
				)
			)
		);
	}
}

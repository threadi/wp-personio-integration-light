<?php
/**
 * File for handling single position as object.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Templates;
use SimpleXMLElement;
use WP_Post;
use WP_Query;
use WP_Term;

/**
 * Represents a single position.
 */
class Position {

	/**
	 * Array for all properties of this object.
	 *
	 * @var array
	 */
	protected array $data = array();

	/**
	 * Marker for language this object is using for its texts.
	 *
	 * @var string
	 */
	public string $lang = '';

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
			if ( ! empty( $post_array['post_type'] ) && WP_PERSONIO_INTEGRATION_MAIN_CPT !== $post_array['post_type'] ) {
				$post_array = array();
			}

			// set the WP_Post-settings in this object.
			$this->data = $post_array;

			// set the main language for this position.
			$this->set_lang( Languages::get_instance()->get_main_language() );
		}
	}

	/**
	 * Saves the actual values of this object in the databases.
	 *
	 * @return void
	 */
	public function save(): void {
		// do not save anything without personioId.
		if ( empty( $this->data['personioId'] ) ) {
			$this->log->add_log( __( 'Position could not be saved as the PersonioId is missing.', 'personio-integration-light' ), 'error' );
			return;
		}

		// do not save anything without language setting.
		if ( empty( $this->data['lang'] ) ) {
			$this->log->add_log( __( 'Position could not be saved as the PersonioId does not have a language set.', 'personio-integration-light' ), 'error' );
			return;
		}

		// set ordering if not set atm.
		if ( empty( $this->data['menu_order'] ) ) { // TODO nur pro?
			$this->data['menu_order'] = 0;
		}

		// search for the personioID to get an existing post-object.
		if ( empty( $this->data['ID'] ) ) {
			$query   = array(
				'post_type'   => PersonioPosition::get_instance()->get_name(),
				'fields'      => 'ids',
				'post_status' => 'any',
				'meta_query'  => array(
					array(
						'key'     => WP_PERSONIO_INTEGRATION_MAIN_CPT_PM_PID,
						'value'   => $this->data['personioId'],
						'compare' => '=',
					),
				),
			);
			$results = new WP_Query( $query );
			$posts   = array();
			foreach ( $results->posts as $post_id ) {
				$lang = $this->get_lang();

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
					$posts[] = $post_id;
				}
			}
			if ( 1 === count( $posts ) ) {
				// get the post-id to update its data.
				$this->data['ID'] = $results->posts[0];
				// get the menu_order to obtain its value during update.
				$this->data['menu_order'] = get_post_field( 'menu_order', $results->posts[0] );
			} elseif ( 1 < count( $posts ) ) {
				// something is wrong.
				// -> delete all entries with this personioId.
				// -> it will be saved as new entry after this.
				foreach ( $posts as $post_id ) {
					wp_delete_post( $post_id );
				}

				// set ID to 0.
				$this->data['ID'] = 0;

				// log this event.
				$this->log->add_log( 'PersonioId ' . $this->data['personioId'] . ' existed in database multiple times. Cleanup done.', 'error' );
			} else {
				// set ID to 0.
				$this->data['ID'] = 0;
			}
		} else {
			// set ID to 0.
			$this->data['ID'] = 0;
		}

		// prepare data to be saved
		// -> overwrite title and content only for the main language.
		$array = array(
			'ID'          => $this->data['ID'],
			'post_status' => 'publish',
			'post_type'   => WP_PERSONIO_INTEGRATION_MAIN_CPT,
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
			$this->log->add_log( 'Position with personioId ' . $this->data['personioId'] . ' could not be saved! Error: ' . $result->get_error_message(), 'error' );
		} elseif ( 0 === absint( $result ) ) {
			// log this event.
			$this->log->add_log( 'Position with personioId ' . $this->data['personioId'] . ' could not be saved! Got no error from WordPress.', 'error' );
		} elseif ( absint( $result ) > 0 ) {
			// save the post-ID.
			$this->data['ID'] = absint( $result );

			/**
			 * Run hook for individual settings after Position has been saved (inserted or updated).
			 *
			 * @since 2.0.0 Available since 2.0.0.
			 *
			 * @param Position $this The object of this position.
			 */
			do_action( 'personio_integration_import_single_position_save', $this );

			// add personioId.
			update_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_MAIN_CPT_PM_PID, $this->data['personioId'] );

			// assign the position to its terms.
			$this->update_term( 'recruitingCategory', 'personioRecruitingCategory', false );
			$this->update_term( 'occupationCategory', 'personioOccupationCategory', false );
			$this->update_term( 'occupation', 'personioOccupation', false );
			$this->update_term( 'office', 'personioOffice', true );
			$this->update_term( 'department', 'personioDepartment', false );
			$this->update_term( 'lang', 'personioLanguages', true );
			$this->update_term( 'employmentType', 'personioEmploymentType', false );
			$this->update_term( 'seniority', 'personioSeniority', false );
			$this->update_term( 'schedule', 'personioSchedule', false );
			$this->update_term( 'yearsOfExperience', 'personioExperience', false );

			// import keywords as single terms if set.
			if ( ! empty( $this->data['keywords'] ) ) {
				$keywords = explode( ',', $this->data['keywords'] );
				foreach ( $keywords as $keyword ) {
					// get the term-object.
					$term = get_term_by( 'name', $keyword, WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS );
					// if no term is found add it.
					if ( ! $term ) {
						$term_array = wp_insert_term( $keyword, WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS );
						if ( ! is_wp_error( $term_array ) ) {
							$term = get_term( $term_array['term_id'], WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS );
						} elseif ( false !== $this->debug ) {
							$this->log->add_log( 'Keyword-Term ' . $keyword . ' could not be imported in ' . WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS . '.', 'error' );
						}
					}
					if ( $term instanceof WP_Term ) {
						wp_set_post_terms( $this->get_id(), $term->term_id, WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS, true );
					}
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

			// mark as changed.
			update_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_UPDATED, 1 );

			// add log in debug-mode.
			if ( false !== $this->debug ) {
				$this->log->add_log( 'Position ' . $this->get_personio_id() . ' successfully imported or updated in ' . $this->get_lang() . '.', 'success' );
			}
		}
	}

	/**
	 * Update a single term for this position.
	 *
	 * @param string $value The value of the term.
	 * @param string $taxonomy The taxonom the term is assigned to.
	 * @param bool   $append If we add the value or override existing values.
	 * @param bool   $do_not_add Prevent adding this term.
	 * @return void
	 */
	public function update_term( string $value, string $taxonomy, bool $append, bool $do_not_add = false ): void {
		if ( ! empty( $this->data[ $value ] ) ) {
			// get the term-object.
			$term = get_term_by( 'name', $this->data[ $value ], $taxonomy );
			// if no term is found add it.
			if ( ! $term && ! $do_not_add ) {
				$term_array = wp_insert_term( $this->data[ $value ], $taxonomy );
				if ( ! is_wp_error( $term_array ) ) {
					$term = get_term( $term_array['term_id'], $taxonomy );
				} elseif ( false !== $this->debug ) {
					$this->log->add_log( 'Term ' . $this->data[ $value ] . ' could not be imported in ' . $taxonomy, 'error' );
				}
			}
			if ( $term instanceof WP_Term ) {
				wp_set_post_terms( $this->data['ID'], $term->term_id, $taxonomy, $append );
			}
		}
	}

	/**
	 * Get a term field of a given taxonomy.
	 *
	 * @param string $taxonomy The taxonomy.
	 * @param string $field The field.
	 * @return string
	 */
	public function get_term_by_field( string $taxonomy, string $field ): string {
		if ( empty( $this->taxonomy_terms[ $taxonomy ] ) ) {
			$taxonomy_terms = get_the_terms( $this->data['ID'], $taxonomy );
			if( ! is_wp_error( $taxonomy_terms ) ) {
				$this->taxonomy_terms[ $taxonomy ] = $taxonomy_terms;
			}
		}
		if ( ! empty( $this->taxonomy_terms[ $taxonomy ] ) ) {
			$term_string = '';
			foreach ( $this->taxonomy_terms[ $taxonomy ] as $term ) {
				if ( ! empty( $term_string ) ) {
					$term_string .= ', ';
				}
				$term_string .= $term->$field;
			}
			return $term_string;
		}
		return '';
	}

	/**
	 * Get the term of the employment type.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_employment_type_name(): string {
		return $this->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE, 'name' );
	}

	/**
	 * Get the term of the recruiting category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_recruiting_category_name(): string {
		return $this->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY, 'name' );
	}

	/**
	 * Get the term of the schedule.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_schedule_name(): string {
		return $this->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE, 'name' );
	}

	/**
	 * Get the term of the office category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_office_name(): string {
		return $this->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE, 'name' );
	}

	/**
	 * Get office term id.
	 *
	 * @return int
	 * @noinspection PhpUnused
	 */
	public function get_office_term_id(): int {
		return absint( $this->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE, 'term_id' ) );
	}

	/**
	 * Get the term of the department category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_department_name(): string {
		return $this->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT, 'name' );
	}

	/**
	 * Get the term of the seniority category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_seniority_name(): string {
		return $this->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY, 'name' );
	}

	/**
	 * Get the term of the experience category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_experience_name(): string {
		return $this->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE, 'name' );
	}

	/**
	 * Get the term of the keyword category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_keywords_type_name(): string {
		return $this->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS, 'name' );
	}

	/**
	 * Get the term of the occupation category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_occupation_category_name(): string {
		return $this->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY, 'name' );
	}

	/**
	 * Get the term of the occupation category detail.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_occupation_name(): string {
		return $this->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION, 'name' );
	}

	/**
	 * Get the language-specific title of this position.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_title(): string {
		return get_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_LANG_POSITION_TITLE . '_' . $this->get_lang(), true );
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
	 * Get the language-specific content of this position (aka jobDescriptions).
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function get_content_as_array(): array {
		$content = get_post_meta( $this->get_id(), WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->get_lang(), true );
		if ( ! empty( $content['jobDescription'] ) ) {
			return $content['jobDescription'];
		}
		return array();
	}

	/**
	 * Get the PersonioId of this position.
	 *
	 * @return string
	 */
	public function get_personio_id(): string {
		if ( ! empty( $this->data['ID'] ) ) {
			return get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_MAIN_CPT_PM_PID, true );
		}
		return '0';
	}

	/**
	 * Get the details of this position.
	 *
	 * @return false|string
	 */
	public function get_excerpt(): false|string {
		ob_start();
		Templates::get_instance()->get_excerpt_template( $this, get_option( 'personioIntegrationTemplateExcerptDefaults' ) );
		return ob_get_clean();
	}

	/**
	 * Check if the position-object is valid. It checks if it contains data.
	 *
	 * @return bool
	 */
	public function is_valid(): bool {
		return ! empty( $this->data );
	}

	/**
	 * Get the WP_Post-ID of this object.
	 *
	 * @return int
	 */
	public function get_id(): int {
		return $this->data['ID'];
	}

	/**
	 * Return the permalink for this position.
	 *
	 * @return string
	 */
	public function get_link(): string {
		if ( ! empty( $this->data['ID'] ) ) {
			return get_permalink( $this->data['ID'] );
		}
		return '';
	}

	/**
	 * Return created at date as timestamp.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_created_at(): string {
		return get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_MAIN_CPT_CREATEDAT, true );
	}

	/**
	 * Get the language of this object.
	 *
	 * @return string
	 */
	private function get_lang(): string {
		return $this->data['lang'];
	}

	/**
	 * Set the language.
	 *
	 * @param string $lang The language.
	 *
	 * @return void
	 */
	public function set_lang( string $lang ): void {
		$this->data['lang'] = $lang;
	}

	/**
	 * Return the language-specific post_content.
	 *
	 * @return array
	 */
	public function get_content(): array {
		return get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->get_lang(), true );
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
		$this->data['department'] = $department;
	}

	/**
	 * Set keywords.
	 *
	 * @param string $keywords List of keywords, separated by ','.
	 *
	 * @return void
	 */
	public function set_keywords( string $keywords ): void {
		$this->data['keywords'] = $keywords;
	}

	/**
	 * Set office.
	 *
	 * @param string $office The office.
	 *
	 * @return void
	 */
	public function set_office( string $office ): void {
		$this->data['office'] = $office;
	}

	/**
	 * Set Personio ID.
	 *
	 * @param int $personio_id The Personio Id.
	 *
	 * @return void
	 */
	public function set_personio_id( int $personio_id ): void {
		$this->data['personioId'] = $personio_id;
	}

	/**
	 * Set recruiting category.
	 *
	 * @param string $recruiting_category The recruitment category.
	 *
	 * @return void
	 */
	public function set_recruiting_category( string $recruiting_category ): void {
		$this->data['recruitingCategory'] = $recruiting_category;
	}

	/**
	 * Set employment type.
	 *
	 * @param string $employment_type The employment type.
	 *
	 * @return void
	 */
	public function set_employment_type( string $employment_type ): void {
		$this->data['employmentType'] = $employment_type;
	}

	/**
	 * Set seniority.
	 *
	 * @param string $seniority The seniority.
	 *
	 * @return void
	 */
	public function set_seniority( string $seniority ): void {
		$this->data['seniority'] = $seniority;
	}

	/**
	 * Set schedule.
	 *
	 * @param string $schedule The schedule.
	 *
	 * @return void
	 */
	public function set_schedule( string $schedule ): void {
		$this->data['schedule'] = $schedule;
	}

	/**
	 * Set the years of experience.
	 *
	 * @param string $years_of_experience The years of experience.
	 *
	 * @return void
	 */
	public function set_years_of_experience( string $years_of_experience ): void {
		$this->data['yearsOfExperience'] = $years_of_experience;
	}

	/**
	 * Set occupation.
	 *
	 * @param string $occupation The occupation.
	 *
	 * @return void
	 */
	public function set_occupation( string $occupation ): void {
		$this->data['occupation'] = $occupation;
	}

	/**
	 * Set the occupation category.
	 *
	 * @param string $occupation_category The occupation category.
	 *
	 * @return void
	 */
	public function set_occupation_category( string $occupation_category ): void {
		$this->data['occupationCategory'] = $occupation_category;
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
	 * Return the application-URL (link to Personio).
	 *
	 * @param bool $without_application True if application-hash should NOT be added.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_application_url( bool $without_application = false ): string {
		$personio_obj = new Personio( Helper::get_personio_url() );
		return $personio_obj->get_application_url( $this, $without_application );
	}

	/**
	 * Add single file to list of files for this position.
	 *
	 * @param int $attachment_id Attachment-ID of the file.
	 *
	 * @return void
	 */
	public function add_file( int $attachment_id ): void {
		// get list of files.
		$files = $this->get_files();

		// add the new one.
		$files[] = $attachment_id;

		// save the list.
		update_post_meta( $this->get_id(), 'files', $files );
	}

	/**
	 * Get list of files for this position.
	 *
	 * @return array
	 */
	public function get_files(): array {
		// get list of files.
		$files = get_post_meta( $this->get_id(), 'files', true );

		// return empty array if list does not contain any file.
		if( empty( $files ) ) {
			return array();
		}

		// return list.
		return $files;
	}

	/**
	 * Remove single file from position.
	 *
	 * @param int $attachment_id Attachment-ID of the file.
	 *
	 * @return void
	 */
	public function remove_file( int $attachment_id ): void {
		// get list of files.
		$files = $this->get_files();

		// remove the attachment-ID from the list.
		unset($files[array_search( $attachment_id, $files )]);

		// save the resulting list.
		update_post_meta( $this->get_id(), 'files', $files );
	}
}

<?php
/**
 * File for handling single position as object.
 *
 * TODO do not use getter/setter.
 *
 * @package personio-integration-light
 */

namespace personioIntegration;

use App\PersonioIntegration\Helper;
use Exception;
use SimpleXMLElement;
use WP_Query;
use WP_Term;
use function apply_filters;

/**
 * Represents a single position.
 */
class Position {

	/**
	 * Array for all properties of this object.
	 *
	 * @var array
	 */
	private array $data = array();

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
		$this->debug = 1 === absint( get_option( 'personioIntegration_debug', 0 ) );

		if ( $post_id > 0 ) {
			// set the main language.
			$this->lang = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY );
			// get the post-data.
			$post_array = get_post( $post_id, ARRAY_A );
			if ( ! is_array( $post_array ) ) {
				$post_array = array();
				// get post-object.
				$this->post = get_post( $post_id );
			}
			if ( ! empty( $post_array['post_type'] ) && WP_PERSONIO_INTEGRATION_CPT !== $post_array['post_type'] ) {
				$post_array = array();
			}
			$this->data = $post_array;
		}
	}

	/**
	 * Magic getter for any properties.
	 *
	 * TODO replace it?
	 *
	 * @param string $variable_name The requested variable name.
	 * @return mixed
	 * @throws Exception If variable_name is unknown.
	 */
	public function __get( string $variable_name ) {
		if ( ! array_key_exists( $variable_name, $this->data ) ) {
			/* translators: %1$s is replaced with "string" */
			throw new Exception( esc_html( printf( __( 'Unknown property %s.', 'personio-integration-light' ), esc_html( $variable_name ) ) ) );
		} elseif ( 'post_excerpt' === $variable_name ) {
			return apply_filters( 'the_excerpt', $this->data[ $variable_name ] );
		} else {
			return $this->data[ $variable_name ];
		}
	}

	/**
	 * Magic setter for any properties.
	 *
	 * TODO replace it?
	 *
	 * @param string                  $variable_name The variable name.
	 * @param SimpleXMLElement|string $value The value to set.
	 *
	 * @return void
	 */
	public function __set( string $variable_name, SimpleXMLElement|string $value ) {
		if ( $value instanceof SimpleXMLElement ) {
			if ( 'post_content' === $variable_name ) {
				foreach ( $value as $v ) {
					if ( 0 === $v->count() ) {
						$value = array( 'jobDescription' => array() );
					}
					if ( 1 === $v->count() ) {
						$value = array( 'jobDescription' => array( $v->jobDescription ) );
					}
				}
			}
			$value = wp_json_encode( $value );
		}
		$this->data[ $variable_name ] = $value;
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

		// get the language to data-array.
		$this->data['lang'] = $this->lang;

		// set ordering if not set atm.
		if ( empty( $this->data['menu_order'] ) ) {
			$this->data['menu_order'] = 0;
		}

		// search for the personioID to get an existing post-object.
		if ( empty( $this->data['ID'] ) ) {
			$query   = array(
				'post_type'   => WP_PERSONIO_INTEGRATION_CPT,
				'fields'      => 'ids',
				'post_status' => 'publish',
				'meta_query'  => array(
					array(
						'key'     => WP_PERSONIO_INTEGRATION_CPT_PM_PID,
						'value'   => $this->data['personioId'],
						'compare' => '=',
					),
				),
			);
			$results = new WP_Query( $query );
			$posts   = array();
			foreach ( $results->posts as $post_id ) {
				// optional filter the post-ID.
				if ( apply_filters( 'personio_integration_import_single_position_filter_existing', $post_id, $this->lang ) ) {
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
			'post_type'   => WP_PERSONIO_INTEGRATION_CPT,
			'menu_order'  => absint( $this->data['menu_order'] ),
		);
		if ( get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY ) === $this->lang ) {
			$array['post_title']   = $this->data['post_title'];
			$array['post_content'] = $this->data['post_content'];
		} else {
			$array['post_title']   = get_post_field( 'post_title', $this->data['ID'] );
			$array['post_content'] = get_post_field( 'post_content', $this->data['ID'] );
		}

		// filter the prepared position-data.
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

			// run hook on save of position.
			do_action( 'personio_integration_import_single_position_save', $this );

			// add personioId.
			update_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_CPT_PM_PID, $this->data['personioId'] );

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
			if ( strlen( $this->keywords ) > 0 ) {
				$keywords = explode( ',', $this->keywords );
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
						wp_set_post_terms( $this->data['ID'], $term->term_id, WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS, true );
					}
				}
			}

			// add created at as post meta field.
			update_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_CPT_CREATEDAT, strtotime( $this->data['createdAt'] ) );

			// add all language-specific titles.
			update_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_TITLE . '_' . $this->lang, $this->data['post_title'] );

			// convert the job description from JSON to array.
			$job_description = json_decode( $this->data['post_content'], true );

			// add all language-specific texts.
			update_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->lang, $job_description );

			// get count of split texts to delete the existing ones.
			$max = absint( get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->lang . '_split', 0 ) );
			for ( $i = 0;$i < $max;$i++ ) {
				delete_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->lang . '_' . $i );
			}

			// add the split language-specific texts.
			if ( ! empty( $job_description['jobDescription'] ) ) {
				foreach ( $job_description['jobDescription'] as $index => $description_part ) {
					update_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->lang . '_' . $index, $description_part );
				}
			}

			// save the count of split texts.
			update_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->lang . '_split', count( $job_description['jobDescription'] ) );

			// mark as changed.
			update_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_UPDATED, 1 );

			// add log in debug-mode.
			if ( false !== $this->debug ) {
				$this->log->add_log( 'Position ' . $this->data['personioId'] . ' successfully imported or updated in ' . $this->data['lang'] . '.', 'success' );
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
	protected function get_term_name( string $taxonomy, string $field ): string {
		if ( empty( $this->taxonomy_terms[ $taxonomy ] ) ) {
			$this->taxonomy_terms[ $taxonomy ] = get_the_terms( $this->data['ID'], $taxonomy );
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
	public function getEmploymentTypeName(): string {
		return $this->get_term_name( WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE, 'name' );
	}

	/**
	 * Get the term of the recruiting category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function getRecruitingCategoryName(): string {
		return $this->get_term_name( WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY, 'name' );
	}

	/**
	 * Get the term of the schedule.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function getScheduleName(): string {
		return $this->get_term_name( WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE, 'name' );
	}

	/**
	 * Get the term of the office category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function getOfficeName(): string {
		return $this->get_term_name( WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE, 'name' );
	}

	/**
	 * Get office term id.
	 *
	 * @return int
	 * @noinspection PhpUnused
	 */
	public function getOfficeTermId(): int {
		return absint( $this->get_term_name( WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE, 'term_id' ) );
	}

	/**
	 * Get the term of the department category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function getDepartmentName(): string {
		return $this->get_term_name( WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT, 'name' );
	}

	/**
	 * Get the term of the seniority category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function getSeniorityName(): string {
		return $this->get_term_name( WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY, 'name' );
	}

	/**
	 * Get the term of the experience category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function getExperienceName(): string {
		return $this->get_term_name( WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE, 'name' );
	}

	/**
	 * Get the term of the keyword category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function getKeywordsTypeName(): string {
		return $this->get_term_name( WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS, 'name' );
	}

	/**
	 * Get the term of the occupation category.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function getOccupationCategoryName(): string {
		return $this->get_term_name( WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY, 'name' );
	}

	/**
	 * Get the term of the occupation category detail.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function getOccupationName(): string {
		return $this->get_term_name( WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION, 'name' );
	}

	/**
	 * Get the language-specific content of this position (aka jobDescriptions).
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function getTitle(): string {
		if ( 0 === strlen( $this->lang ) ) {
			$this->lang = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY );
		}
		return get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_TITLE . '_' . $this->lang, true );
	}

	/**
	 * Get the language-specific content of this position (aka jobDescriptions).
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function getContentAsArray(): array {
		if ( 0 === strlen( $this->lang ) ) {
			$this->lang = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY );
		}
		$content = get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT . '_' . $this->lang, true );
		if ( ! empty( $content['jobDescription'] ) ) {
			return $content['jobDescription'];
		}
		return array();
	}

	/**
	 * Get the language-specific content of this position (aka jobDescriptions).
	 *
	 * TODO find better way outside of this object.
	 *
	 * @param string $template The used template.
	 * @return string
	 */
	public function get_content( string $template = 'default' ): string {
		// use old template if it exists.
		$template_file = 'parts/properties-content.php';

		// if old template does not exist, use the one we configured.
		if ( ! Helper::has_template( $template_file ) ) {
			if ( empty( $template ) ) {
				$template = 'default';
			}
			$template_file = 'parts/jobdescription/' . $template . '.php';
		}

		// get position.
		$position = $this;

		// get template and return it.
		ob_start();
		include Helper::get_template( $template_file );
		return ob_get_clean();
	}

	/**
	 * Get the personioId of this position.
	 *
	 * @return mixed
	 * @noinspection PhpUnused
	 */
	public function getPersonioId() {
		if ( ! empty( $this->data['ID'] ) ) {
			return get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_CPT_PM_PID, true );
		}
		return 0;
	}

	/**
	 * Get the details of this position.
	 *
	 * @return false|string
	 */
	public function get_excerpt(): false|string {
		ob_start();
		personio_integration_get_excerpt( $this, get_option( 'personioIntegrationTemplateExcerptDefaults', array() ) );
		return ob_get_clean();
	}

	/**
	 * Check if the position-object is valid. It checks if it contains data.
	 *
	 * @return bool
	 */
	public function isValid(): bool {
		return ! empty( $this->data );
	}

	/**
	 * Return the permalink for this position.
	 *
	 * TODO move to pro.
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
	 * Save sorting for this position.
	 *
	 * TODO move to pro.
	 *
	 * @param int $i The order to set.
	 * @return void
	 */
	public function set_order( int $i ): void {
		$this->data['menu_order'] = $i;
		wp_insert_post( $this->data );
	}

	/**
	 * Return created at date.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_created_at(): string {
		return get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_CPT_CREATEDAT, true );
	}
}

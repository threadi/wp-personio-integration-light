<?php

namespace personioIntegration;

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
    private array $data = [];

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
    private array $_taxonomyTerms = [];

    /**
     * Log-Object.
     *
     * @var Log
     */
    private Log $_log;

    /**
     * Marker if debug-Mode is active
     *
     * @var bool
     */
    private bool $_debug = false;

    /**
     * Constructor for this position.
     */
    public function __construct( $postId ) {
        // get log-object
        $this->_log = new Log();

        // get debug-mode
        $this->_debug = get_option('personioIntegration_debug', 0) == 1;

        if( $postId > 0 ) {
            // set the main language
            $this->lang = get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY);
            // get the post-data
            $postArray = get_post($postId, ARRAY_A);
            if( !is_array($postArray) ) {
                $postArray = [];
                // get post-object
                $this->post = get_post($postId);
            }
            if( !empty($postArray["post_type"]) && $postArray["post_type"] !== WP_PERSONIO_INTEGRATION_CPT ) {
                $postArray = [];
            }
            $this->data = $postArray;
        }
    }

    /**
     * Magic getter for any properties.
     *
     * @param $varName
     * @return mixed
     * @throws Exception
     */
    public function __get( $varName ){
        if (!array_key_exists($varName,$this->data)){
            /* translators: %1$s is replaced with "string" */
            throw new Exception(printf(__('Unknown property %s.', 'wp-personio-integration'), $varName));
        }
        elseif( $varName == "post_excerpt" ) {
            return apply_filters('the_excerpt', $this->data[$varName]);
        }
        else return $this->data[$varName];

    }

    /**
     * Magic setter for any properties.
     *
     * @param $varName
     * @param $value
     * @return void
     */
    public function __set( $varName, $value ){
        if( $value instanceof SimpleXMLElement ) {
            $value = json_encode($value);
        }
        $this->data[$varName] = $value;
    }

    /**
     * Saves the actual values of this object in the databases.
     *
     * @return void
     */
    public function save()
    {
        // do not save anything without personioId
        if( empty($this->data['personioId']) ) {
            $this->_log->addLog(__('Position could not be saved as the PersonioId is missing.', 'wp-personio-integration'), 'error');
            return;
        }

        // get the language to data-array
        $this->data['lang'] = $this->lang;

        // set ordering of not set atm
        if( empty($this->data['menu_order']) ) {
            $this->data['menu_order'] = 0;
        }

        // search for the personioID to get an existing post-object
        if( empty($this->data['ID']) ) {
            $query = [
                'post_type' => WP_PERSONIO_INTEGRATION_CPT,
                'fields' => 'ids',
                'post_status' => 'publish',
                'meta_query' => [
                    [
                        'key' => WP_PERSONIO_INTEGRATION_CPT_PM_PID,
                        'value' => $this->data['personioId'],
                        'compare' => '='
                    ]
                ]
            ];
            $posts = new WP_Query($query);
            if( $posts->post_count == 1 ) {
                // get the post-id to update its data
                $this->data['ID'] = $posts->posts[0];
            }
            elseif( $posts->post_count > 1 ) {
                // something is wrong
                // -> delete all entries with this personioId
                // -> it will be saved as new entry after this
                foreach( $posts->posts as $position ) {
                    wp_delete_post($position);
                }
                // set ID to 0
                $this->data['ID'] = 0;
            }
            else {
                // set ID to 0
                $this->data['ID'] = 0;
            }
        }
        else {
            // set ID to 0
            $this->data['ID'] = 0;
        }

        // save the position
        // -> overwrite title and content only for the main language
        $array = [
            'ID' => $this->data['ID'],
            'post_status' => 'publish',
            'post_type' => WP_PERSONIO_INTEGRATION_CPT,
            'menu_order' => (int)$this->data['menu_order']
        ];
        if( $this->lang == get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY) ) {
            $array['post_title'] = $this->data['post_title'];
            $array['post_content'] = $this->data['post_content'];
        }
        else {
            $array['post_title'] = get_post_field('post_title', $this->data['ID']);
            $array['post_content'] = get_post_field('post_content', $this->data['ID']);
        }
        $this->data['ID'] = wp_insert_post($array);

        if( $this->data['ID'] > 0 ) {
            // add personioId
            update_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_CPT_PM_PID, $this->data['personioId']);

            // assign the position to its terms
            $this->updateTerm( 'recruitingCategory', 'personioRecruitingCategory', false );
            $this->updateTerm( 'occupationCategory', 'personioOccupationCategory', false );
            $this->updateTerm( 'office', 'personioOffice', false );
            $this->updateTerm( 'department', 'personioDepartment', false );
            $this->updateTerm( 'lang', 'personioLanguages', true, true );
            $this->updateTerm( 'employmentType', 'personioEmploymentType', false, true );
            $this->updateTerm( 'seniority', 'personioSeniority', false, true );
            $this->updateTerm( 'schedule', 'personioSchedule', false, true );
            $this->updateTerm( 'yearsOfExperience', 'personioExperience', false, true );

            // add all language-specific titles ..
            update_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_TITLE.'_'.$this->lang, $this->data['post_title']);

            // add all language-specific texts ..
            update_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT.'_'.$this->lang, json_decode($this->data['post_content'], true));

            // mark as changed
            update_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_UPDATED, 1);
        }
    }

    /**
     * Update a single term for this position.
     *
     * @param $value
     * @param $taxonomy
     * @param $append
     * @param bool $doNotAdd
     * @return void
     */
    private function updateTerm($value, $taxonomy, $append, bool $doNotAdd = false ) {
        if( !empty($this->data[$value]) ) {
            // get the term-object
            $term = get_term_by('name', $this->data[$value], $taxonomy);
            // if no term is found add it
            if (!$term && !$doNotAdd) {
                $termArray = wp_insert_term($this->data[$value], $taxonomy);
                if( !is_wp_error($termArray) ) {
                    $term = get_term($termArray['term_id'], $taxonomy);
                }
                elseif( false !== $this->_debug ) {
                    $this->_log->addLog('Term '.$this->data[$value].' could not be imported in '.$taxonomy, 'error');
                }
            }
            if ($term instanceof WP_Term) {
                wp_set_post_terms($this->data['ID'], $term->term_id, $taxonomy, $append);
            }
        }
    }

    /**
     * Get the term of a given taxonomy.
     *
     * @param $taxonomy
     * @param $field
     * @return string
     */
    private function _getTermName( $taxonomy, $field ): string
    {
        if( empty($this->_taxonomyTerms[$taxonomy]) ) {
            $this->_taxonomyTerms[$taxonomy] = get_the_terms($this->data['ID'], $taxonomy);
        }
        if( !empty($this->_taxonomyTerms[$taxonomy]) ) {
            return $this->_taxonomyTerms[$taxonomy][0]->$field;
        }
        return '';
    }

    /**
     * Get the term of the employment type.
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getEmploymentTypeName(): string
    {
        return $this->_getTermName( WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE, 'name' );
    }

    /**
     * Get the term of the recruiting category.
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getRecruitingCategoryName(): string
    {
        return $this->_getTermName( WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY, 'name' );
    }

    /**
     * Get the term of the schedule.
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getScheduleName(): string
    {
        return $this->_getTermName( WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE, 'name' );
    }

    /**
     * Get the term of the office category.
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getOfficeName(): string
    {
        return $this->_getTermName( WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE, 'name' );
    }

    /**
     * Get office term id.
     *
     * @return int
     * @noinspection PhpUnused
     */
    public function getOfficeTermId(): int
    {
        return absint($this->_getTermName( WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE, 'term_id' ));
    }

    /**
     * Get the term of the department category.
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getDepartmentName(): string
    {
        return $this->_getTermName( WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT, 'name' );
    }

    /**
     * Get the term of the seniority category.
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getSeniorityName(): string
    {
        return $this->_getTermName( WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY, 'name' );
    }

    /**
     * Get the term of the experience category.
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getExperienceName(): string
    {
        return $this->_getTermName( WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE, 'name' );
    }

    /**
     * Get the term of the recruiting category.
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getOccupationCategoryName(): string
    {
        return $this->_getTermName( WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY, 'name' );
    }

    /**
     * Get the language-specific content of this position (aka jobDescriptions).
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getTitle(): string
    {
        if( strlen($this->lang) == 0 ) {
            $this->lang = get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY);
        }
        return get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_TITLE.'_'.$this->lang, true );
    }

    /**
     * Get the language-specific content of this position (aka jobDescriptions).
     *
     * @return array
     * @noinspection PhpUnused
     */
    public function getContentAsArray(): array
    {
        if( strlen($this->lang) == 0 ) {
            $this->lang = get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY);
        }
        $content = get_post_meta( $this->data['ID'], WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT.'_'.$this->lang, true );
        if( !empty($content['jobDescription']) ) {
            return $content['jobDescription'];
        }
        return [];
    }

    /**
     * Get the language-specific content of this position (aka jobDescriptions).
     *
     * @return string
     */
    public function getContent(): string
    {
        $position = $this;
        ob_start();
        include helper::getTemplate('parts/properties-content.php');
        return ob_get_clean();
    }

    /**
     * Get the personioId of this position.
     *
     * @return mixed
     * @noinspection PhpUnused
     */
    public function getPersonioId() {
        if( !empty($this->data['ID']) ) {
            return get_post_meta($this->data['ID'], WP_PERSONIO_INTEGRATION_CPT_PM_PID, true);
        }
        return 0;
    }

    /**
     * Get the excerpt of this position.
     *
     * @return false|string
     */
    public function getExcerpt()
    {
        ob_start();
        personio_integration_get_excerpt($this, get_option('personioIntegrationTemplateExcerptDefaults', []));
        return ob_get_clean();
    }

    /**
     * Check if the position-object is valid. It checks if it contains data.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return !empty($this->data);
    }

    /**
     * Return the permalink for this position.
     *
     * @return string
     */
    public function getLink(): string
    {
        return get_permalink($this->data['ID']);
    }

    /**
     * Save sorting for this position.
     *
     * @param int $i
     * @return void
     */
    public function setOrder(int $i)
    {
        $this->data['menu_order'] = $i;
        wp_insert_post($this->data);
    }

}
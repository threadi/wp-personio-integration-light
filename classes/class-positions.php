<?php

namespace personioIntegration;

use WP_Query;

/**
 * Object to handle positions.
 */
class Positions {

    // result of positions-query
    private WP_Query $_results;

    public function __construct()
    {
    }

    /**
     * Get positions from database as Position-objects.
     * Optionally limited by a number.
     *
     * @param int $limit
     * @param array $parameterToAdd
     * @return array
     */
    public function getPositions(int $limit = -1, array $parameterToAdd = [] ): array
    {
        $query = [
            'post_type' => WP_PERSONIO_INTEGRATION_CPT,
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'no_found_rows' => empty($parameterToAdd['nopagination']) ? false: $parameterToAdd['nopagination'],
            'order' => 'asc',
            'orderby' => 'title',
            'paged' => (get_query_var('paged')) ? get_query_var('paged') : 1
        ];
        if( !empty($parameterToAdd['ids']) ) {
            $query['post__in'] = $parameterToAdd['ids'];
        }
        if( !empty($parameterToAdd['sort']) ) {
            $query['order'] = $parameterToAdd['sort'];
        }
        if( !empty($parameterToAdd['sortby']) && in_array($parameterToAdd['sortby'], ['title','date']) ) {
            $query['orderby'] = $parameterToAdd['sortby'];
        }
        if( !empty($parameterToAdd['personioid']) ) {
            $query['meta_query'] = [
                [
                    'key' => WP_PERSONIO_INTEGRATION_CPT_PM_PID,
                    'value' => $parameterToAdd['personioid'],
                    'compare' => '='
                ]
            ];
        }
        // add taxonomies as filter
        $tax_query = [];
        foreach( WP_PERSONIO_INTEGRATION_TAXONOMIES as $taxonomy_name => $taxonomy ) {
            if( !empty($parameterToAdd[$taxonomy['slug']]) ) {
                $tax_query[] = [
                    'taxonomy' => $taxonomy_name,
                    'field' => 'term_id',
                    'terms' => $parameterToAdd[$taxonomy['slug']]
                ];
            }
        }
        if( !empty($tax_query) ) {
            if( count($tax_query) > 1 ) {
                $query['tax_query'] = [
                    'relation' => 'AND',
                    $tax_query
                ];
            }
            else {
                $query['tax_query'] = $tax_query;
            }
        }
        $this->_results = new WP_Query($query);
        $array = [];
        foreach( $this->_results->posts as $post ) {
            $positionObject = new Position($post->ID);
            if( !empty($parameterToAdd["lang"]) ) {
                $positionObject->lang = $parameterToAdd["lang"];
            }
            $array[] = $positionObject;
        }
        return $array;
    }

    /**
     * Return the complete WP_Query-result.
     *
     * @return WP_Query
     */
    public function getResult(): WP_Query
    {
        return $this->_results;
    }

    /**
     * Get a single position by its personioId.
     *
     * @param $personioid
     * @return Position
     */
    public function getPositionByPersonioId($personioid): ?Position
    {
        $this->getPositions(1, ['personioid' => $personioid]);
        $result = $this->getResult();
        if( $result->post_count == 1 ) {
            return new Position($result->posts[0]->ID);
        }
        return null;
    }

}
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
        foreach( apply_filters('personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES) as $taxonomy_name => $taxonomy ) {
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
        elseif( !empty($parameterToAdd['groupby']) ) {
            $taxonomy = helper::get_taxonomy_name_by_simple_name( $parameterToAdd['groupby'] );
            if( !empty($taxonomy) ) {
                $terms = get_terms([
                    'taxonomy' => $taxonomy,
                    'fields' => 'ids',   //get the IDs only
                    'hide_empty' => true,
                ]);
                $query['tax_query'] = [
                    [
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $terms
                    ]
                ];
                add_filter('posts_join', [$this, 'addTaxonomyTableToPositionQuery']);
                add_filter('posts_orderby', [$this, 'setPositionQueryOrderByForGroup']);
            }
        }
        // get the results
        $this->_results = new WP_Query($query);

        // remove filter
        remove_filter('posts_join', [$this, 'addTaxonomyTableToPositionQuery']);
        remove_filter('posts_orderby', [$this, 'setPositionQueryOrderByForGroup']);

        // get the positions as object in array
        // -> optionally grouped by a given taxonomy
        $array = [];
        foreach( $this->_results->posts as $post ) {
            // get the position object
            $positionObject = new Position($post->ID);

            // set used language on position-object
            if( !empty($parameterToAdd["lang"]) ) {
                $positionObject->lang = $parameterToAdd["lang"];
            }

            // consider grouping of entries in list
            if( !empty($parameterToAdd['groupby']) ) {
                $array[helper::get_taxonomy_name_of_position($parameterToAdd['groupby'], $positionObject)] = $positionObject;
            }
            else {
                // ungrouped simply add the position to the list
                $array[] = $positionObject;
            }
        }
        ksort($array);
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

    /**
     * Return the request-query.
     *
     * @return array
     */
    public function getQuery(): array
    {
        return $this->_results->query;
    }

    /**
     * Helper for order the results by taxonomy name via 'posts_orderby'-filter.
     *
     * @param $orderby_statement
     * @return string
     */
    public function setPositionQueryOrderByForGroup($orderby_statement): string
    {
        return " wp_terms.name ASC, ".$orderby_statement;
    }

    /**
     * Helper to add the term-table in the sql-statement to order the results by a given taxonomy name
     * via 'posts_join'-filter.
     *
     * @param $join
     * @return string
     */
    public function addTaxonomyTableToPositionQuery( $join ): string
    {
        global $wpdb;
        return $join." LEFT JOIN $wpdb->terms ON $wpdb->terms.term_id = $wpdb->term_relationships.term_taxonomy_id";
    }

}
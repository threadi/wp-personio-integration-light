<?php
/**
 * File for handling positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Transients;
use WP_Post;
use WP_Query;

/**
 * Object to handle positions.
 */
class Positions {

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Positions
	 */
	protected static ?Positions $instance = null;

	/**
	 * Variable to hold the results of a query.
	 *
	 * @var WP_Query
	 */
	private WP_Query $results;

	/**
	 * Variable to hold the list of initialized Positions.
	 *
	 * @var array[Position]
	 */
	private array $positions = array();

	/**
	 * Constructor, not used as this a Singleton object.
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
	public static function get_instance(): Positions {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Return Position object of given id.
	 *
	 * @param int    $post_id The ID of the post object.
	 * @param string $language_code The language-code to use for contents of the requested position (optional).
	 *
	 * @return Position
	 */
	public function get_position( int $post_id, string $language_code = '' ): Position {
		if ( empty( $this->positions[ $post_id . $language_code ] ) ) {
			$postion_obj = new Position( $post_id );
			/**
			 * Filter the requested position object.
			 *
			 * @since 3.0.0 Available since 3.0.0.
			 *
			 * @param Position $position_obj The object of the position.
			 * @param string $language_code The requested language.
			 */
			$this->positions[ $post_id . $language_code ] = apply_filters( 'personio_integration_get_position_obj', $postion_obj, $language_code );
			if ( ! empty( $language_code ) ) {
				$this->positions[ $post_id . $language_code ]->set_lang( $language_code );
			}
		}
		return $this->positions[ $post_id . $language_code ];
	}

	/**
	 * Get positions from database as Position-objects.
	 * Optionally limited by a number.
	 *
	 * @param int   $limit The limit, defaults to -1 for default-limiting.
	 * @param array $parameter_to_add The parameter to add.
	 *
	 * @return array
	 */
	public function get_positions( int $limit = -1, array $parameter_to_add = array() ): array {
		$query = array(
			'post_type'      => PersonioPosition::get_instance()->get_name(),
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'no_found_rows'  => empty( $parameter_to_add['nopagination'] ) ? false : $parameter_to_add['nopagination'],
			'order'          => 'asc',
			'orderby'        => 'title',
			'paged'          => ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1,
			'fields'         => 'ids',
		);
		if ( ! empty( $parameter_to_add['ids'] ) ) {
			$query['post__in'] = $parameter_to_add['ids'];
		}
		if ( ! empty( $parameter_to_add['sort'] ) ) {
			$query['order'] = $parameter_to_add['sort'];
		}
		if ( ! empty( $parameter_to_add['sortby'] ) && 'title' === $parameter_to_add['sortby'] ) {
			$query['orderby']                = $parameter_to_add['sortby'];
			$query['personio_explicit_sort'] = 1;
		}
		if ( ! empty( $parameter_to_add['sortby'] ) && 'date' === $parameter_to_add['sortby'] ) {
			$query['meta_key']               = WP_PERSONIO_INTEGRATION_MAIN_CPT_CREATEDAT;
			$query['orderby']                = 'meta_value';
			$query['personio_explicit_sort'] = 1;
		}
		if ( ! empty( $parameter_to_add['personioid'] ) ) {
			$query['meta_query'] = array(
				array(
					'key'     => WP_PERSONIO_INTEGRATION_MAIN_CPT_PM_PID,
					'value'   => $parameter_to_add['personioid'],
					'compare' => '=',
				),
			);
		}

		// add taxonomies as filter.
		$tax_query = array();
		foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
			if ( ! empty( $parameter_to_add[ $taxonomy['slug'] ] ) ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy_name,
					'field'    => 'term_id',
					'terms'    => $parameter_to_add[ $taxonomy['slug'] ],
				);
			}
		}
		if ( ! empty( $tax_query ) ) {
			if ( count( $tax_query ) > 1 ) {
				$query['tax_query'] = array(
					'relation' => 'AND',
					$tax_query,
				);
			} else {
				$query['tax_query'] = $tax_query;
			}
		} elseif ( ! empty( $parameter_to_add['groupby'] ) ) {
			$taxonomy_name = Taxonomies::get_instance()->get_taxonomy_name_by_slug( $parameter_to_add['groupby'] );
			if ( ! empty( $taxonomy_name ) ) {
				$terms = get_terms(
					array(
						'taxonomy'   => $taxonomy_name,
						'fields'     => 'ids',
						'hide_empty' => true,
					)
				);
				if ( is_array( $terms ) && ! empty( $terms ) ) {
					$query['tax_query'] = array(
						array(
							'taxonomy' => $taxonomy_name,
							'field'    => 'term_id',
							'terms'    => $terms,
						),
					);
					add_filter( 'posts_join', array( $this, 'add_taxonomy_table_to_position_query' ) );
					add_filter( 'posts_orderby', array( $this, 'set_position_query_order_by_for_group' ) );
				}
			}
		}

		/**
		 * Filter the custom query for positions just before it is used.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $query The configured query.
		 * @param array $parameter_to_add The parameter to filter for.
		 */
		$query = apply_filters( 'personio_integration_positions_query', $query, $parameter_to_add );

		// get the results.
		$this->results = new WP_Query( $query );

		// add log for this query if debug is enabled.
		if ( 1 === absint( get_option( 'personioIntegration_debug', 0 ) ) ) {
			$log = new Log();
			$log->add_log( __( 'Query-Debug', 'personio-integration-light' ) . ': ' . wp_json_encode( $query ) . '<br><br>' . __( 'Result', 'personio-integration-light' ) . ': ' . wp_json_encode( $this->get_results() ) . '<br><br>' . __( 'Used URL', 'personio-integration-light' ) . ': ' . esc_url( Helper::get_current_url() ), 'success', 'system' );
		}

		// remove filter.
		remove_filter( 'posts_join', array( $this, 'add_taxonomy_table_to_position_query' ) );
		remove_filter( 'posts_orderby', array( $this, 'set_position_query_order_by_for_group' ) );

		// get the positions as object in array
		// -> optionally grouped by a given taxonomy.
		$resulting_position_list = array();
		foreach ( $this->results->posts as $post_id ) {
			/**
			 * Filter the resulting ID / object.
			 *
			 * @since 3.0.0 Available since 3.0.0
			 * @param int|WP_Post $post_id The ID or object to use.
			 */
			$position_object = $this->get_position( apply_filters( 'personio_integration_positions_loop_id', $post_id ) );

			// set used language on position-object.
			if ( ! empty( $parameter_to_add['lang'] ) ) {
				$position_object->set_lang( $parameter_to_add['lang'] );
			}

			// consider grouping of entries in list.
			if ( ! empty( $parameter_to_add['groupby'] ) ) {
				$resulting_position_list[ $position_object->get_term_by_field( $parameter_to_add['groupby'], 'name' ) ] = $position_object;
			} else {
				// ungrouped simply add the position to the list.
				$resulting_position_list[] = $position_object;
			}
		}

		// sort the list by key.
		ksort( $resulting_position_list );

		/**
		 * Filter the resulting list of position objects.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $resulting_position_list List of resulting position objects.
		 * @param int $limit The limitation of the list.
		 * @param array $parameter_to_add The list of parameters used to get this list.
		 */
		return apply_filters( 'personio_integration_positions_resulting_list', $resulting_position_list, $limit, $parameter_to_add );
	}

	/**
	 * Return the complete WP_Query-result.
	 *
	 * @return WP_Query
	 */
	public function get_results(): WP_Query {
		return $this->results;
	}

	/**
	 * Get a single position by its PersonioID.
	 *
	 * @param string $personioid The PersonioID.
	 * @return Position|null
	 */
	public function get_position_by_personio_id( string $personioid ): ?Position {
		$array = $this->get_positions( 1, array( 'personioid' => $personioid ) );
		if ( ! empty( $array ) ) {
			return $array[0];
		}
		return null;
	}

	/**
	 * Helper for order the results by taxonomy name via 'posts_orderby'-filter.
	 *
	 * @param string $orderby_statement The order by statement.
	 * @return string
	 */
	public function set_position_query_order_by_for_group( string $orderby_statement ): string {
		global $wpdb;
		return ' ' . $wpdb->terms . '.name ASC, ' . $orderby_statement;
	}

	/**
	 * Helper to add the term-table in the sql-statement to order the results by a given taxonomy name
	 * via 'posts_join'-filter.
	 *
	 * @param string $join The join statement.
	 *
	 * @return string
	 */
	public function add_taxonomy_table_to_position_query( string $join ): string {
		global $wpdb;
		return $join . " LEFT JOIN $wpdb->terms ON $wpdb->terms.term_id = $wpdb->term_relationships.term_taxonomy_id";
	}

	/**
	 * Return count of positions in db.
	 *
	 * @return int
	 */
	public function get_positions_count(): int {
		return count( $this->get_positions() );
	}

	/**
	 * Trigger re-import hint.
	 *
	 * @return void
	 */
	public function trigger_reimport_hint(): void {
		// create re-import dialog.
		$dialog = array(
			'title'   => __( 'Re-Import positions', 'personio-integration-light' ),
			'texts'   => array(
				'<p>' . __( 'After click on the button below all positions in WordPress will be removed and new imported from Personio.', 'personio-integration-light' ) . '</p>',
			),
			'buttons' => array(
				array(
					'action'  => 'personio_delete_positions( true );',
					'variant' => 'primary',
					'text'    => __( 'Start re-import', 'personio-integration-light' ),
				),
				array(
					'action'  => 'closeDialog();',
					'variant' => 'secondary',
					'text'    => __( 'Cancel', 'personio-integration-light' ),
				),
			),
		);

		// create alternative url for re-import.
		$url = add_query_arg(
			array(
				'action' => 'personioPositionsReImport',
				'nonce'  => wp_create_nonce( 'personio-integration-re-import' ),
			),
			get_admin_url() . 'admin.php'
		);

		// and trigger hint for re-import of positions.
		$transient_obj = Transients::get_instance()->add();
		$transient_obj->set_name( 'personio_integration_reimport_hint' );
		$transient_obj->set_type( 'success' );
		$transient_obj->set_message( __( 'You have changed settings that would make it advisable to re-import the positions from Personio. Click on the following button: ', 'personio-integration-light' ) . '</p><p><a href="' . esc_url( $url ) . '" class="button button-primary wp-easy-dialog" data-dialog="' . esc_attr( wp_json_encode( $dialog ) ) . '">' . __( 'Re-Import all positions', 'personio-integration-light' ) . '</a>' );
		$transient_obj->save();
	}
}

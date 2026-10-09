<?php
/**
 * File for handling the abilities we provide.
 *
 * Hints:
 * Use custom settings with this hook: https://developer.wordpress.org/reference/hooks/wp_register_ability_args/
 *
 * @package personio-integration-light
 */

declare(strict_types=1);

namespace PersonioIntegrationLight\Abilities;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use PersonioIntegrationLight\Plugin\Languages;
use WP_Error;
use WP_Query;
use WP_Term;

/**
 * Object to add support for abilities.
 *
 * The positions are managed in Personio, so the abilities never edit them. They are only read, refreshed by an
 * import from Personio or removed from WordPress, see Import_Abilities.
 */
class Abilities {
	/**
	 * The name for the ability category.
	 */
	public const ABILITY_CATEGORY = 'personio-integration';

	/**
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Abilities
	 */
	private static ?Abilities $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
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
	public static function get_instance(): Abilities {
		if ( \is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		// show the abilities in the settings.
		Abilities_Settings::get_instance()->init();

		// bail if the abilities are disabled.
		if ( ! Abilities_Settings::get_instance()->is_enabled() ) {
			return;
		}

		// use hooks.
		add_action( 'wp_abilities_api_init', array( $this, 'add_abilities' ) );
		add_action( 'wp_abilities_api_categories_init', array( $this, 'add_ability_category' ) );

		// initialize the abilities for templates of page builders.
		Template_Abilities::get_instance()->init();

		// initialize the abilities to control the import and the deletion of positions.
		Import_Abilities::get_instance()->init();

		// initialize the ability to read the log.
		Log_Abilities::get_instance()->init();
	}

	/**
	 * Return the meta settings for abilities, which only read data.
	 *
	 * @return array<string,mixed>
	 */
	public function get_readonly_meta(): array {
		return array(
			'show_in_rest' => true,
			'mcp'          => array( 'public' => true ), // expose via the MCP Adapter of WordPress.
			'annotations'  => array(
				'readonly'    => true,
				'destructive' => false,
				'idempotent'  => true,
			),
		);
	}

	/**
	 * Add our own ability category.
	 *
	 * @return void
	 */
	public function add_ability_category(): void {
		// bail if function does not exist.
		if ( ! \function_exists( 'wp_register_ability_category' ) ) {
			return;
		}

		// add the category.
		wp_register_ability_category(
			self::ABILITY_CATEGORY,
			array(
				'label'       => __( 'Personio Integration', 'personio-integration-light' ),
				'description' => __( 'Abilities for positions from Personio in WordPress.', 'personio-integration-light' ),
			)
		);
	}

	/**
	 * Add abilities this plugin provides.
	 *
	 * @return void
	 */
	public function add_abilities(): void {
		// bail if support for abilities is missing.
		if ( ! \function_exists( 'wp_register_ability' ) ) {
			return;
		}

		// the schema for the base data of a position.
		$position_properties = array(
			'post_id'         => array(
				'type'        => 'integer',
				'description' => __( 'The ID of this position in WordPress.', 'personio-integration-light' ),
			),
			'personio_id'     => array(
				'type'        => 'string',
				'description' => __( 'The ID of this position in Personio.', 'personio-integration-light' ),
			),
			'title'           => array(
				'type'        => 'string',
				'description' => __( 'The title of this position.', 'personio-integration-light' ),
			),
			'language'        => array(
				'type'        => 'string',
				'description' => __( 'The language of the texts of this position.', 'personio-integration-light' ),
			),
			'url'             => array(
				'type'        => 'string',
				'description' => __( 'The URL of this position in the frontend.', 'personio-integration-light' ),
			),
			'created_at'      => array(
				'type'        => 'string',
				'description' => __( 'The date this position was created in Personio (ISO 8601).', 'personio-integration-light' ),
			),
			'taxonomies'      => array(
				'type'                 => 'object',
				'description'          => __( 'The terms of this position, indexed by the slug of the taxonomy (see get-taxonomies).', 'personio-integration-light' ),
				'additionalProperties' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
			),
			'description'     => array(
				'type'        => 'array',
				'description' => __( 'The parts of the job description with their headline and text (HTML). Only present if requested or for a single position.', 'personio-integration-light' ),
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'name'  => array( 'type' => 'string' ),
						'value' => array( 'type' => 'string' ),
					),
				),
			),
			'application_url' => array(
				'type'        => 'string',
				'description' => __( 'The URL to apply for this position.', 'personio-integration-light' ),
			),
		);

		// add the ability to get the list of positions.
		wp_register_ability(
			self::ABILITY_CATEGORY . '/get-positions',
			array(
				'label'               => __( 'Get list of positions', 'personio-integration-light' ),
				'description'         => __( 'Returns the open positions from Personio in WordPress. The positions are managed in Personio and can not be changed here. Use the returned post_id to request the details of a single position. To design how positions are shown on the website (templates for the detail view and the list), use get-builders and get-template-catalog instead of building it from these values.', 'personio-integration-light' ),
				'category'            => self::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'search'           => array(
							'type'        => 'string',
							'description' => __( 'Limit the results to positions matching this text.', 'personio-integration-light' ),
						),
						'language'         => array(
							'type'        => 'string',
							'description' => __( 'Return the texts of the positions in this language, e.g. "de". See get-taxonomies for the active languages. The main language is used if not set.', 'personio-integration-light' ),
						),
						'taxonomies'       => array(
							'type'                 => 'object',
							'description'          => __( 'Limit the results to positions with these terms: the slug of the taxonomy as key and the slug of a term as value, e.g. {"office":"berlin"}. See get-taxonomies.', 'personio-integration-light' ),
							'additionalProperties' => array( 'type' => 'string' ),
						),
						'limit'            => array(
							'type'        => 'integer',
							'description' => __( 'The maximum amount of positions to return, 20 by default and 100 at most.', 'personio-integration-light' ),
							'minimum'     => 1,
							'maximum'     => 100,
						),
						'with_description' => array(
							'type'        => 'boolean',
							'description' => __( 'Return the job description of every position as well. This makes the response much bigger, so use it only if the texts are needed.', 'personio-integration-light' ),
							'default'     => false,
						),
					),
					'default'    => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'count'     => array(
							'type'        => 'integer',
							'description' => __( 'The amount of returned positions.', 'personio-integration-light' ),
						),
						'total'     => array(
							'type'        => 'integer',
							'description' => __( 'The amount of all positions matching the request.', 'personio-integration-light' ),
						),
						'positions' => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => $position_properties,
							),
						),
					),
				),
				'execute_callback'    => array( $this, 'get_positions' ),
				'permission_callback' => array( $this, 'has_read_permission' ),
				'meta'                => $this->get_readonly_meta(),
			)
		);

		// add the ability to get a single position with all of its data.
		wp_register_ability(
			self::ABILITY_CATEGORY . '/get-position',
			array(
				'label'               => __( 'Get a single position', 'personio-integration-light' ),
				'description'         => __( 'Returns a single position from Personio in WordPress with all of its data, including the job description. The position is managed in Personio and can not be changed here.', 'personio-integration-light' ),
				'category'            => self::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'     => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the position in WordPress, as returned by get-positions.', 'personio-integration-light' ),
						),
						'personio_id' => array(
							'type'        => 'string',
							'description' => __( 'The ID of the position in Personio. Used if no post_id is given.', 'personio-integration-light' ),
						),
						'language'    => array(
							'type'        => 'string',
							'description' => __( 'Return the texts of the position in this language, e.g. "de". The main language is used if not set.', 'personio-integration-light' ),
						),
					),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => $position_properties,
				),
				'execute_callback'    => array( $this, 'get_position' ),
				'permission_callback' => array( $this, 'has_read_permission' ),
				'meta'                => $this->get_readonly_meta(),
			)
		);

		// add the ability to get the taxonomies and their terms.
		wp_register_ability(
			self::ABILITY_CATEGORY . '/get-taxonomies',
			array(
				'label'               => __( 'Get taxonomies, terms and languages', 'personio-integration-light' ),
				'description'         => __( 'Returns the taxonomies positions are organized in - like office, department or employment type - with the terms which actually exist, and the active languages. Use these values to filter the results of get-positions.', 'personio-integration-light' ),
				'category'            => self::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'taxonomy' => array(
							'type'        => 'string',
							'description' => __( 'Limit the result to this taxonomy, by its slug. All taxonomies are returned if this is not set.', 'personio-integration-light' ),
						),
					),
					'default'    => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'taxonomies' => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => array(
									'slug'  => array(
										'type'        => 'string',
										'description' => __( 'The slug of this taxonomy, used in the filters, templates and attributes of this plugin.', 'personio-integration-light' ),
									),
									'name'  => array(
										'type'        => 'string',
										'description' => __( 'The internal name of this taxonomy in WordPress.', 'personio-integration-light' ),
									),
									'label' => array(
										'type'        => 'string',
										'description' => __( 'The human readable label of this taxonomy.', 'personio-integration-light' ),
									),
									'terms' => array(
										'type'  => 'array',
										'items' => array(
											'type'       => 'object',
											'properties' => array(
												'slug'  => array( 'type' => 'string' ),
												'label' => array( 'type' => 'string' ),
												'count' => array(
													'type' => 'integer',
													'description' => __( 'The amount of positions assigned to this term.', 'personio-integration-light' ),
												),
											),
										),
									),
								),
							),
						),
						'languages'  => array(
							'type'                 => 'object',
							'description'          => __( 'The active languages: the language code as key and its label as value.', 'personio-integration-light' ),
							'additionalProperties' => array( 'type' => 'string' ),
						),
					),
				),
				'execute_callback'    => array( $this, 'get_taxonomies' ),
				'permission_callback' => array( $this, 'has_read_permission' ),
				'meta'                => $this->get_readonly_meta(),
			)
		);

		// add the ability to get the state of the import.
		wp_register_ability(
			self::ABILITY_CATEGORY . '/get-import-status',
			array(
				'label'               => __( 'Get the state of the position import', 'personio-integration-light' ),
				'description'         => __( 'Returns whether an import of positions from Personio is running right now, since when and how far it is, its last state and errors and how many positions exist. Use this to find out why positions are missing and to follow a running import. Use run-import to start an import, cancel-import to release an import which got stuck and get-log for the details of past imports.', 'personio-integration-light' ),
				'category'            => self::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'    => 'object',
					'default' => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'is_running'       => array(
							'type'        => 'boolean',
							'description' => __( 'True if an import is running right now.', 'personio-integration-light' ),
						),
						'started_at'       => array(
							'type'        => 'string',
							'description' => __( 'The date the running import has been started (ISO 8601), empty if none is running. An import which is running for more than an hour is most likely stuck.', 'personio-integration-light' ),
						),
						'progress'         => array(
							'type'        => 'object',
							'description' => __( 'The progress of the running or the last import in steps. These are not the amounts of positions.', 'personio-integration-light' ),
							'properties'  => array(
								'count' => array(
									'type'        => 'integer',
									'description' => __( 'The steps which are done.', 'personio-integration-light' ),
								),
								'max'   => array(
									'type'        => 'integer',
									'description' => __( 'The steps to do.', 'personio-integration-light' ),
								),
							),
						),
						'status'           => array(
							'type'        => 'string',
							'description' => __( 'The last state message of the import.', 'personio-integration-light' ),
						),
						'errors'           => array(
							'type'        => 'array',
							'description' => __( 'Errors of the last import.', 'personio-integration-light' ),
							'items'       => array( 'type' => 'string' ),
						),
						'position_count'   => array(
							'type'        => 'integer',
							'description' => __( 'The amount of positions which exist in WordPress.', 'personio-integration-light' ),
						),
						'new_positions'    => array(
							'type'        => 'integer',
							'description' => __( 'The amount of positions which have been added by the last import.', 'personio-integration-light' ),
						),
						'import_type'      => array(
							'type'        => 'string',
							'description' => __( 'The internal name of the used import type, empty if none is enabled.', 'personio-integration-light' ),
						),
						'has_personio_url' => array(
							'type'        => 'boolean',
							'description' => __( 'True if the URL of a Personio account is configured.', 'personio-integration-light' ),
						),
						'next_import'      => array(
							'type'        => 'string',
							'description' => __( 'The date of the next automatic import (ISO 8601), empty if none is scheduled.', 'personio-integration-light' ),
						),
					),
				),
				'execute_callback'    => array( $this, 'get_import_status' ),
				'permission_callback' => array( $this, 'has_permission' ),
				'meta'                => $this->get_readonly_meta(),
			)
		);
	}

	/**
	 * Return whether the actual user is allowed to use our abilities for settings.
	 *
	 * @return bool
	 */
	public function has_permission(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Return whether the actual user is allowed to read the positions via our abilities.
	 *
	 * @return bool
	 */
	public function has_read_permission(): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Return the requested language, if it is active, otherwise the main language.
	 *
	 * @param array<string,mixed> $input The given input.
	 *
	 * @return string
	 */
	private function get_language( array $input ): string {
		// get the main language as fallback.
		$main_language = Languages::get_instance()->get_main_language();

		// bail if no language is requested.
		if ( empty( $input['language'] ) || ! \is_scalar( $input['language'] ) ) {
			return $main_language;
		}

		// use the requested language only if it is active.
		$language = sanitize_key( (string) $input['language'] );
		if ( ! \array_key_exists( $language, Languages::get_instance()->get_active_languages() ) ) {
			return $main_language;
		}
		return $language;
	}

	/**
	 * Return the taxonomies, which are visible (without internal taxonomies).
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function get_visible_taxonomies(): array {
		$list = array();
		foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
			// bail if taxonomy has no slug or is internal.
			if ( empty( $taxonomy['slug'] ) || isset( $taxonomy['attr']['show_ui'] ) ) {
				continue;
			}

			// bail if this taxonomy does not exist.
			if ( ! taxonomy_exists( $taxonomy_name ) ) {
				continue;
			}

			$list[ $taxonomy_name ] = $taxonomy;
		}
		return $list;
	}

	/**
	 * Return a list of positions from Personio in WordPress.
	 *
	 * @param mixed $input The given input.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function get_positions( mixed $input = array() ): array|WP_Error {
		$input = \is_array( $input ) ? $input : array();

		// get the limit.
		$limit = isset( $input['limit'] ) && \is_scalar( $input['limit'] ) ? min( 100, max( 1, absint( $input['limit'] ) ) ) : 20;

		// create the query.
		$query = array(
			'post_type'      => PersonioPosition::get_instance()->get_name(),
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'fields'         => 'ids',
		);

		// limit the results to a search text, if given.
		if ( ! empty( $input['search'] ) && \is_scalar( $input['search'] ) ) {
			$query['s'] = sanitize_text_field( (string) $input['search'] );
		}

		// limit the results to terms of taxonomies, if given.
		if ( ! empty( $input['taxonomies'] ) && \is_array( $input['taxonomies'] ) ) {
			$tax_query = array();
			foreach ( $input['taxonomies'] as $taxonomy_slug => $term_slug ) {
				// get the taxonomy name.
				$taxonomy_name = Taxonomies::get_instance()->get_taxonomy_name_by_slug( (string) $taxonomy_slug );

				// bail if the taxonomy is unknown.
				if ( empty( $taxonomy_name ) || ! \is_scalar( $term_slug ) ) {
					/* translators: %1$s will be replaced by the slug of a taxonomy. */
					return new WP_Error( 'personio_integration_unknown_taxonomy', \sprintf( __( 'The taxonomy %1$s is unknown. Use a slug from get-taxonomies.', 'personio-integration-light' ), (string) $taxonomy_slug ), array( 'status' => 400 ) );
				}

				$tax_query[] = array(
					'taxonomy' => $taxonomy_name,
					'field'    => 'slug',
					'terms'    => sanitize_title( (string) $term_slug ),
				);
			}
			$query['tax_query'] = array_merge( array( 'relation' => 'AND' ), $tax_query ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Filter positions by terms.
		}

		// run the query.
		$results = new WP_Query( $query );

		// get the language and whether the description should be returned.
		$language         = $this->get_language( $input );
		$with_description = isset( $input['with_description'] ) && filter_var( $input['with_description'], FILTER_VALIDATE_BOOLEAN );

		// collect the positions.
		$positions = array();
		foreach ( $results->get_posts() as $post_id ) {
			$position_obj = Positions::get_instance()->get_position( absint( \is_object( $post_id ) ? $post_id->ID : $post_id ), $language );

			// bail if position is not valid.
			if ( ! $position_obj->is_valid() ) {
				continue;
			}

			$positions[] = $this->get_position_data( $position_obj, $with_description );
		}

		// return the resulting list.
		return array(
			'count'     => \count( $positions ),
			'total'     => absint( $results->found_posts ),
			'positions' => $positions,
		);
	}

	/**
	 * Return a single position with all of its data.
	 *
	 * @param mixed $input The given input.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function get_position( mixed $input = array() ): array|WP_Error {
		$input = \is_array( $input ) ? $input : array();

		// get the post-ID.
		$post_id = isset( $input['post_id'] ) && \is_scalar( $input['post_id'] ) ? absint( $input['post_id'] ) : 0;

		// get the post-ID by the Personio-ID, if no post-ID is given.
		if ( 0 === $post_id && ! empty( $input['personio_id'] ) && \is_scalar( $input['personio_id'] ) ) {
			$position_obj = Positions::get_instance()->get_position_by_personio_id( sanitize_text_field( (string) $input['personio_id'] ) );
			if ( $position_obj instanceof Position ) {
				$post_id = $position_obj->get_id();
			}
		}

		// bail if no post-ID is given.
		if ( 0 === $post_id ) {
			return new WP_Error( 'personio_integration_missing_position', __( 'No position has been given or found. Set post_id or personio_id, see get-positions.', 'personio-integration-light' ), array( 'status' => 400 ) );
		}

		// bail if this is not a published position.
		if ( PersonioPosition::get_instance()->get_name() !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
			return new WP_Error( 'personio_integration_position_not_found', __( 'No position could be found for the given ID.', 'personio-integration-light' ), array( 'status' => 404 ) );
		}

		// get the position.
		$position_obj = Positions::get_instance()->get_position( $post_id, $this->get_language( $input ) );

		// bail if the position is not valid.
		if ( ! $position_obj->is_valid() ) {
			return new WP_Error( 'personio_integration_position_not_found', __( 'No position could be found for the given ID.', 'personio-integration-light' ), array( 'status' => 404 ) );
		}

		// return the data.
		return $this->get_position_data( $position_obj, true );
	}

	/**
	 * Return the data of a single position.
	 *
	 * Hint:
	 * We only return data, which is visible in the frontend anyway. Internal data (e.g. the raw
	 * response of the Personio API) must not leave the site through an ability.
	 *
	 * @param Position $position_obj     The position.
	 * @param bool     $with_description True to add the job description.
	 *
	 * @return array<string,mixed>
	 */
	private function get_position_data( Position $position_obj, bool $with_description ): array {
		// get the terms of this position.
		$taxonomies = array();
		foreach ( $this->get_visible_taxonomies() as $taxonomy_name => $taxonomy ) {
			$terms = array();
			foreach ( $position_obj->get_terms_by_field( $taxonomy_name ) as $term ) {
				if ( $term instanceof WP_Term ) {
					$terms[] = $term->name;
				}
			}
			if ( ! empty( $terms ) ) {
				$taxonomies[ (string) $taxonomy['slug'] ] = $terms;
			}
		}

		// get the creation date from the meta directly, as get_created_at() returns the actual time if it is not set.
		$created_at_meta = get_post_meta( $position_obj->get_id(), WP_PERSONIO_INTEGRATION_MAIN_CPT_CREATEDAT, true );
		$created_at      = \is_scalar( $created_at_meta ) ? absint( $created_at_meta ) : 0;

		// collect the data.
		$data = array(
			'post_id'     => $position_obj->get_id(),
			'personio_id' => $position_obj->get_personio_id(),
			'title'       => $position_obj->get_title(),
			'language'    => $position_obj->get_lang(),
			'url'         => $position_obj->get_link(),
			'created_at'  => $created_at > 0 ? gmdate( 'c', $created_at ) : '',
			'taxonomies'  => (object) $taxonomies,
		);

		// add the job description, if requested.
		if ( $with_description ) {
			$description = array();
			foreach ( $position_obj->get_content_as_array() as $part ) {
				$description[] = array(
					'name'  => $part['name'] ?? '',
					'value' => wp_kses_post( $part['value'] ?? '' ),
				);
			}
			$data['description']     = $description;
			$data['application_url'] = $position_obj->get_application_url();
		}

		/**
		 * Filter the data of a position, which is returned by the abilities.
		 *
		 * Only add data, which is visible in the frontend anyway.
		 *
		 * @since 5.3.0 Available since 5.3.0.
		 *
		 * @param array<string,mixed> $data             The data.
		 * @param Position            $position_obj     The position.
		 * @param bool                $with_description True if the details are requested.
		 */
		return apply_filters( 'personio_integration_ability_position_data', $data, $position_obj, $with_description );
	}

	/**
	 * Return the taxonomies positions are organized in, with their terms, and the active languages.
	 *
	 * @param mixed $input The given input.
	 *
	 * @return array<string,mixed>
	 */
	public function get_taxonomies( mixed $input = array() ): array {
		$input = \is_array( $input ) ? $input : array();

		// get the requested taxonomy, if given.
		$requested = isset( $input['taxonomy'] ) && \is_scalar( $input['taxonomy'] ) ? sanitize_text_field( (string) $input['taxonomy'] ) : '';

		// collect the taxonomies.
		$taxonomies = array();
		foreach ( $this->get_visible_taxonomies() as $taxonomy_name => $taxonomy ) {
			// bail if another taxonomy has been requested.
			if ( ! empty( $requested ) && $requested !== $taxonomy['slug'] && $requested !== $taxonomy_name ) {
				continue;
			}

			// get the terms of this taxonomy.
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy_name,
					'hide_empty' => false,
				)
			);

			// collect the terms.
			$term_list = array();
			if ( \is_array( $terms ) ) {
				foreach ( $terms as $term ) {
					// bail if this is not a term object.
					if ( ! $term instanceof WP_Term ) { // @phpstan-ignore instanceof.alwaysTrue
						continue;
					}

					$term_list[] = array(
						'slug'  => $term->slug,
						'label' => $term->name,
						'count' => absint( $term->count ),
					);
				}
			}

			// get the label.
			$labels = Taxonomies::get_instance()->get_taxonomy_label( $taxonomy_name );

			// add this taxonomy to the list.
			$taxonomies[] = array(
				'slug'  => (string) $taxonomy['slug'],
				'name'  => $taxonomy_name,
				'label' => ! empty( $labels['name'] ) ? (string) $labels['name'] : (string) $taxonomy['slug'],
				'terms' => $term_list,
			);
		}

		// return the resulting list.
		return array(
			'taxonomies' => $taxonomies,
			'languages'  => (object) Languages::get_instance()->get_active_languages(),
		);
	}

	/**
	 * Return the state of the position import.
	 *
	 * @return array<string,mixed>
	 */
	public function get_import_status(): array {
		// get the errors of the last import.
		$errors = get_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, array() );
		$errors = \is_array( $errors ) ? array_values( array_map( 'wp_strip_all_tags', array_filter( $errors, 'is_string' ) ) ) : array();

		// get the next scheduled import (the event is scheduled with arguments, so we search the cron list).
		$next_import = false;
		$crons       = _get_cron_array();
		foreach ( $crons as $timestamp => $hooks ) {
			if ( isset( $hooks['personio_integration_schedule_events'] ) ) {
				$next_import = absint( $timestamp );
				break;
			}
		}

		// get the start time of the running import.
		$running_since = absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) );

		// get the positions which have been added by the last import.
		$new_positions = get_option( WP_PERSONIO_INTEGRATION_IMPORT_NEW_POSITIONS, array() );

		// get the import object.
		$imports_obj = Imports::get_instance()->get_import_extension();

		// return the state.
		return array(
			'is_running'       => $running_since > 0,
			'started_at'       => $running_since > 0 ? gmdate( 'c', $running_since ) : '',
			'progress'         => array(
				'count' => absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_COUNT, 0 ) ),
				'max'   => absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_MAX, 0 ) ),
			),
			'status'           => wp_strip_all_tags( (string) get_option( WP_PERSONIO_INTEGRATION_IMPORT_STATUS, '' ) ),
			'errors'           => $errors,
			'position_count'   => Positions::get_instance()->get_positions_count(),
			'new_positions'    => \is_array( $new_positions ) ? \count( $new_positions ) : 0,
			'import_type'      => $imports_obj ? $imports_obj->get_name() : '',
			'has_personio_url' => ! empty( get_option( 'personioIntegrationUrl' ) ),
			'next_import'      => \is_int( $next_import ) ? gmdate( 'c', $next_import ) : '',
		);
	}
}

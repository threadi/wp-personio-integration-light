<?php
/**
 * File for the ability to read the log of this plugin.
 *
 * @package personio-integration-light
 */

declare(strict_types=1);

namespace PersonioIntegrationLight\Abilities;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Log;
use WP_Error;

/**
 * Object to provide the ability to read the log of this plugin (e.g. for AI).
 *
 * The log explains what happened during imports and why something failed. It can only be read here.
 */
class Log_Abilities {
	/**
	 * The amount of entries which are returned by default.
	 */
	private const DEFAULT_LIMIT = 20;

	/**
	 * The maximum amount of entries which are returned.
	 */
	private const MAX_LIMIT = 100;

	/**
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Log_Abilities
	 */
	private static ?Log_Abilities $instance = null;

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
	public static function get_instance(): Log_Abilities {
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
		add_action( 'wp_abilities_api_init', array( $this, 'add_abilities' ) );
	}

	/**
	 * Add the abilities.
	 *
	 * @return void
	 */
	public function add_abilities(): void {
		// bail if support for abilities is missing.
		if ( ! \function_exists( 'wp_register_ability' ) ) {
			return;
		}

		// add the ability to read the log.
		wp_register_ability(
			Abilities::ABILITY_CATEGORY . '/get-log',
			array(
				'label'               => __( 'Get the log', 'personio-integration-light' ),
				'description'         => __( 'Returns the newest entries of the log of this plugin, newest first. Use it to find out what happened during an import or a deletion of positions and why something failed. Without debug mode only errors and the results of imports are logged, and old entries are removed automatically. The log can not be changed here.', 'personio-integration-light' ),
				'category'            => Abilities::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'limit'       => array(
							'type'        => 'integer',
							'description' => __( 'The maximum amount of entries to return, 20 by default and 100 at most.', 'personio-integration-light' ),
							'minimum'     => 1,
							'maximum'     => self::MAX_LIMIT,
						),
						'category'    => array(
							'type'        => 'string',
							'description' => __( 'Limit the entries to this category, by its name, e.g. "import". The possible categories are returned as "categories".', 'personio-integration-light' ),
						),
						'errors_only' => array(
							'type'        => 'boolean',
							'description' => __( 'Return only errors.', 'personio-integration-light' ),
							'default'     => false,
						),
					),
					'default'    => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'count'      => array(
							'type'        => 'integer',
							'description' => __( 'The amount of returned entries.', 'personio-integration-light' ),
						),
						'entries'    => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => array(
									'date'     => array(
										'type'        => 'string',
										'description' => __( 'The date of this entry (ISO 8601).', 'personio-integration-light' ),
									),
									'state'    => array(
										'type'        => 'string',
										'description' => __( 'The state of this entry: "error", "success" or "info".', 'personio-integration-light' ),
									),
									'category' => array(
										'type'        => 'string',
										'description' => __( 'The name of the category of this entry.', 'personio-integration-light' ),
									),
									'text'     => array(
										'type'        => 'string',
										'description' => __( 'The logged text, without HTML.', 'personio-integration-light' ),
									),
								),
							),
						),
						'categories' => array(
							'type'        => 'array',
							'description' => __( 'The categories which can be used as filter.', 'personio-integration-light' ),
							'items'       => array(
								'type'       => 'object',
								'properties' => array(
									'name'  => array( 'type' => 'string' ),
									'label' => array( 'type' => 'string' ),
								),
							),
						),
					),
				),
				'execute_callback'    => array( $this, 'get_log' ),
				'permission_callback' => array( Abilities::get_instance(), 'has_permission' ),
				'meta'                => Abilities::get_instance()->get_readonly_meta(),
			)
		);
	}

	/**
	 * Return the newest entries of the log.
	 *
	 * @param mixed $input The given input.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function get_log( mixed $input = array() ): array|WP_Error {
		$input = \is_array( $input ) ? $input : array();

		// get the parameters.
		$limit       = isset( $input['limit'] ) && \is_scalar( $input['limit'] ) ? min( self::MAX_LIMIT, max( 1, absint( $input['limit'] ) ) ) : self::DEFAULT_LIMIT;
		$category    = isset( $input['category'] ) && \is_scalar( $input['category'] ) ? sanitize_key( (string) $input['category'] ) : '';
		$errors_only = isset( $input['errors_only'] ) && filter_var( $input['errors_only'], FILTER_VALIDATE_BOOLEAN );

		// get the categories.
		$categories = Log::get_instance()->get_categories();

		// bail if the requested category is unknown.
		if ( '' !== $category && ! \array_key_exists( $category, $categories ) ) {
			/* translators: %1$s will be replaced by the name of a category, %2$s by a list of names. */
			return new WP_Error( 'personio_integration_unknown_log_category', \sprintf( __( 'The log category %1$s is unknown. Possible categories are: %2$s', 'personio-integration-light' ), $category, implode( ', ', array_keys( $categories ) ) ), array( 'status' => 400 ) );
		}

		// the log object reads its filters from the request of the log table in the backend, so we set them via its hooks.
		$filters = array(
			'personio_integration_light_log_limit'         => static function () use ( $limit ): int {
				return $limit;
			},
			'personio_integration_light_log_category'      => static function () use ( $category ): string {
				return $category;
			},
			'personio_integration_light_log_md5'           => static function (): string {
				return '';
			},
			'personio_integration_light_log_errors'        => static function () use ( $errors_only ): int {
				return $errors_only ? 1 : 0;
			},
			'personio_integration_light_log_entries_order' => static function (): string {
				return 'DESC';
			},
		);
		foreach ( $filters as $hook => $callback ) {
			add_filter( $hook, $callback, PHP_INT_MAX );
		}

		// get the entries, newest first.
		try {
			$rows = Log::get_instance()->get_entries();
		} finally {
			foreach ( $filters as $hook => $callback ) {
				remove_filter( $hook, $callback, PHP_INT_MAX );
			}
		}

		// collect the entries.
		$entries = array();
		foreach ( $rows as $row ) {
			// bail if this is not a valid entry.
			if ( ! \is_array( $row ) ) {
				continue;
			}

			// get the date, which is saved in UTC.
			$timestamp = isset( $row['date'] ) && \is_string( $row['date'] ) ? strtotime( $row['date'] . ' UTC' ) : false;

			$entries[] = array(
				'date'     => false !== $timestamp ? gmdate( 'c', $timestamp ) : '',
				'state'    => isset( $row['state'] ) && \is_scalar( $row['state'] ) ? (string) $row['state'] : '',
				'category' => isset( $row['category'] ) && \is_scalar( $row['category'] ) ? (string) $row['category'] : '',
				'text'     => $this->get_as_text( isset( $row['log'] ) && \is_scalar( $row['log'] ) ? (string) $row['log'] : '' ),
			);
		}

		// collect the categories for the response.
		$category_list = array();
		foreach ( $categories as $name => $label ) {
			$category_list[] = array(
				'name'  => (string) $name,
				'label' => wp_strip_all_tags( (string) $label ),
			);
		}

		// return the result.
		return array(
			'count'      => \count( $entries ),
			'entries'    => $entries,
			'categories' => $category_list,
		);
	}

	/**
	 * Return a logged text, which is saved as HTML, as plain text.
	 *
	 * @param string $log The logged text.
	 *
	 * @return string
	 */
	private function get_as_text( string $log ): string {
		// keep line breaks.
		$log = (string) preg_replace( '#<br\s*/?>#i', "\n", $log );

		// remove the HTML and decode the entities.
		return trim( html_entity_decode( wp_strip_all_tags( $log ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	}
}

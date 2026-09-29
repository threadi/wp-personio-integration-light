<?php
/**
 * File for the abilities to work with templates of page builders.
 *
 * @package personio-integration-light
 */

declare(strict_types=1);

namespace PersonioIntegrationLight\Abilities;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use PersonioIntegrationLight\Plugin\Templates;
use WP_Error;
use WP_Query;

/**
 * Object to provide abilities to work with templates of page builders (e.g. for AI).
 *
 * The abilities are independent of the page builder. Each page builder is supported by an adapter,
 * see Template_Adapter_Base and the filter "personio_integration_template_ability_adapters".
 */
class Template_Abilities {
	/**
	 * The maximum length of the rendered HTML returned by the preview.
	 */
	private const MAX_PREVIEW_LENGTH = 100000;

	/**
	 * The maximum length of the CSS for a template.
	 */
	private const MAX_CSS_LENGTH = 100000;

	/**
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Template_Abilities
	 */
	private static ?Template_Abilities $instance = null;

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
	public static function get_instance(): Template_Abilities {
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
	 * Return the list of adapters for page builders.
	 *
	 * @return array<string,Template_Adapter_Base>
	 */
	public function get_adapters(): array {
		$adapters = array();

		/**
		 * Filter the list of adapters, which make templates of page builders accessible for abilities.
		 *
		 * Each entry must be an object based on \PersonioIntegrationLight\Abilities\Template_Adapter_Base.
		 *
		 * @since 5.3.0 Available since 5.3.0.
		 * @param array<int,Template_Adapter_Base> $adapters List of adapters.
		 */
		$list = apply_filters( 'personio_integration_template_ability_adapters', $adapters );

		// use only valid adapters, with their name as key.
		$result = array();
		foreach ( (array) $list as $adapter ) {
			if ( ! $adapter instanceof Template_Adapter_Base ) { // @phpstan-ignore instanceof.alwaysTrue
				continue;
			}
			$result[ $adapter->get_name() ] = $adapter;
		}

		// return the resulting list.
		return $result;
	}

	/**
	 * Return the adapter for the requested page builder.
	 *
	 * If no page builder is requested, the only available one is used.
	 *
	 * @param string $builder The internal name of the page builder (optional).
	 *
	 * @return Template_Adapter_Base|WP_Error
	 */
	public function get_adapter( string $builder = '' ): Template_Adapter_Base|WP_Error {
		// get the available adapters.
		$available = array_filter(
			$this->get_adapters(),
			function ( Template_Adapter_Base $adapter ) {
				return $adapter->is_available();
			}
		);

		// use the only available one if none is requested.
		if ( empty( $builder ) ) {
			if ( 1 === \count( $available ) ) {
				return reset( $available );
			}
			if ( empty( $available ) ) {
				return new WP_Error( 'personio_integration_no_builder_available', __( 'No page builder with support for templates is available. Use get-builders for details.', 'personio-integration-light' ) );
			}
			return new WP_Error( 'personio_integration_builder_required', __( 'More than one page builder is available. Set the parameter "builder", use get-builders to get the list.', 'personio-integration-light' ) );
		}

		// bail if the requested page builder is unknown.
		$adapters = $this->get_adapters();
		if ( empty( $adapters[ $builder ] ) ) {
			return new WP_Error( 'personio_integration_unknown_builder', __( 'The requested page builder is not supported. Use get-builders to get the list.', 'personio-integration-light' ) );
		}

		// bail if the requested page builder is not available.
		if ( ! $adapters[ $builder ]->is_available() ) {
			return new WP_Error( 'personio_integration_builder_not_available', $adapters[ $builder ]->get_unavailable_reason() );
		}

		// return the adapter.
		return $adapters[ $builder ];
	}

	/**
	 * Return whether the actual user can read the templates.
	 *
	 * @return bool
	 */
	public function has_template_permission(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Return the schema for the parameter "builder".
	 *
	 * @return array<string,string>
	 */
	private function get_builder_schema(): array {
		return array(
			'type'        => 'string',
			'description' => __( 'The internal name of the page builder, see get-builders. Can be omitted if only one page builder is available.', 'personio-integration-light' ),
		);
	}

	/**
	 * Return the schema for the parameter "type".
	 *
	 * @return array<string,mixed>
	 */
	private function get_type_schema(): array {
		return array(
			'type'        => 'string',
			'description' => __( 'The template type: "single" for the detail view of a position, "archive" for the list of positions.', 'personio-integration-light' ),
			'enum'        => array( 'single', 'archive' ),
			'default'     => 'single',
		);
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

		// the annotations for all our abilities, as they only read data.
		$meta = array(
			'show_in_rest' => true,
			'mcp'          => array( 'public' => true ), // expose via the MCP Adapter of WordPress.
			'annotations'  => array(
				'readonly'    => true,
				'destructive' => false,
				'idempotent'  => true,
			),
		);

		// add the ability to get the page builders.
		wp_register_ability(
			Abilities::ABILITY_CATEGORY . '/get-builders',
			array(
				'label'               => __( 'Get page builders for templates', 'personio-integration-light' ),
				'description'         => __( 'Returns the page builders whose templates for positions can be read and previewed with the template abilities. Start here before working on templates: use the name of an available page builder as parameter "builder" for get-template-catalog, get-template and preview-template.', 'personio-integration-light' ),
				'category'            => Abilities::ABILITY_CATEGORY,
				// no input: an object without properties, so null and an empty object are accepted (an empty list of properties would be sent as JSON array, which is rejected by some AI APIs).
				'input_schema'        => array(
					'type'    => 'object',
					'default' => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'builders' => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => array(
									'name'           => array( 'type' => 'string' ),
									'label'          => array( 'type' => 'string' ),
									'available'      => array( 'type' => 'boolean' ),
									'reason'         => array(
										'type'        => 'string',
										'description' => __( 'Why the page builder is not available, if so.', 'personio-integration-light' ),
									),
									'template_types' => array(
										'type' => 'object',
										'additionalProperties' => array( 'type' => 'string' ),
									),
									'can_save'       => array(
										'type'        => 'boolean',
										'description' => __( 'True if templates of this page builder can be saved with save-template.', 'personio-integration-light' ),
									),
								),
							),
						),
					),
				),
				'execute_callback'    => array( $this, 'get_builders' ),
				'permission_callback' => array( Abilities::get_instance(), 'has_read_permission' ),
				'meta'                => $meta,
			)
		);

		// add the ability to get the catalog of elements for templates.
		wp_register_ability(
			Abilities::ABILITY_CATEGORY . '/get-template-catalog',
			array(
				'label'               => __( 'Get elements for templates', 'personio-integration-light' ),
				'description'         => __( 'Returns everything needed to build a template for positions with a page builder: the format of templates, the elements of this plugin with their attributes and examples, the allowed values (e.g. taxonomies for details and filters, templates for lists and descriptions) and hints. Always use this before writing a template and use only the listed elements and values.', 'personio-integration-light' ),
				'category'            => Abilities::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'builder' => $this->get_builder_schema(),
					),
					'default'    => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'builder'        => array( 'type' => 'string' ),
						'format'         => array( 'type' => 'string' ),
						'template_types' => array(
							'type'                 => 'object',
							'additionalProperties' => array( 'type' => 'string' ),
						),
						'elements'       => array(
							'type'  => 'array',
							'items' => array( 'type' => 'object' ),
						),
						'values'         => array(
							'type'                 => 'object',
							'description'          => __( 'The allowed values for the attributes of the elements, e.g. the slugs of taxonomies for details and filters or the templates for lists, details and descriptions. Each entry is a list of objects with "name" (the value to use) and "label".', 'personio-integration-light' ),
							'additionalProperties' => array(
								'type'  => 'array',
								'items' => array( 'type' => 'object' ),
							),
						),
						'hints'          => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
					),
				),
				'execute_callback'    => array( $this, 'get_catalog' ),
				'permission_callback' => array( Abilities::get_instance(), 'has_read_permission' ),
				'meta'                => $meta,
			)
		);

		// add the ability to get a template.
		wp_register_ability(
			Abilities::ABILITY_CATEGORY . '/get-template',
			array(
				'label'               => __( 'Get template for positions', 'personio-integration-light' ),
				'description'         => __( 'Returns the actual template for the detail view ("single") or the list ("archive") of positions in the format of the page builder. Use it as starting point for changes.', 'personio-integration-light' ),
				'category'            => Abilities::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'builder' => $this->get_builder_schema(),
						'type'    => $this->get_type_schema(),
					),
					'default'    => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'builder'       => array( 'type' => 'string' ),
						'type'          => array( 'type' => 'string' ),
						'id'            => array( 'type' => 'string' ),
						'source'        => array( 'type' => 'string' ),
						'is_customized' => array( 'type' => 'boolean' ),
						'content'       => array( 'type' => 'string' ),
						'css'           => array(
							'type'        => 'string',
							'description' => __( 'The CSS saved for this template with save-template.', 'personio-integration-light' ),
						),
					),
				),
				'execute_callback'    => array( $this, 'get_template' ),
				'permission_callback' => array( $this, 'has_template_permission' ),
				'meta'                => $meta,
			)
		);

		// add the ability to validate and preview a template.
		wp_register_ability(
			Abilities::ABILITY_CATEGORY . '/preview-template',
			array(
				'label'               => __( 'Validate and preview template for positions', 'personio-integration-light' ),
				'description'         => __( 'Validates a template in the format of the page builder and renders it for a position. Nothing is saved. Fix all returned errors and check the warnings and the rendered HTML before a template is used.', 'personio-integration-light' ),
				'category'            => Abilities::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'builder' => $this->get_builder_schema(),
						'type'    => $this->get_type_schema(),
						'content' => array(
							'type'        => 'string',
							'description' => __( 'The template content in the format of the page builder.', 'personio-integration-light' ),
						),
						'post_id' => array(
							'type'        => 'integer',
							'description' => __( 'The post-ID of a published position to use for the preview of a single template, see get-positions. The newest position is used if not set.', 'personio-integration-light' ),
						),
						'render'  => array(
							'type'        => 'boolean',
							'description' => __( 'Render the template and return the HTML. Set to false to only validate it.', 'personio-integration-light' ),
							'default'     => true,
						),
					),
					'required'   => array( 'content' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'builder'   => array( 'type' => 'string' ),
						'type'      => array( 'type' => 'string' ),
						'valid'     => array(
							'type'        => 'boolean',
							'description' => __( 'True if the template has no errors.', 'personio-integration-light' ),
						),
						'errors'    => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
						'warnings'  => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
						'post_id'   => array( 'type' => 'integer' ),
						'html'      => array( 'type' => 'string' ),
						'truncated' => array(
							'type'        => 'boolean',
							'description' => __( 'True if the HTML has been shortened because of its length.', 'personio-integration-light' ),
						),
					),
				),
				'execute_callback'    => array( $this, 'preview_template' ),
				'permission_callback' => array( $this, 'has_template_permission' ),
				'meta'                => array(
					'show_in_rest' => true,
					'mcp'          => array( 'public' => true ), // expose via the MCP Adapter of WordPress.
					'annotations'  => array(
						// not marked as readonly, as readonly abilities must be called via GET, which is not usable for the long template content.
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
					),
				),
			)
		);

		// the result of the abilities which change templates.
		$change_output_schema = array(
			'type'       => 'object',
			'properties' => array(
				'builder'  => array( 'type' => 'string' ),
				'type'     => array( 'type' => 'string' ),
				'dry_run'  => array( 'type' => 'boolean' ),
				'done'     => array(
					'type'        => 'boolean',
					'description' => __( 'True if the change has been made.', 'personio-integration-light' ),
				),
				'action'   => array(
					'type'        => 'string',
					'description' => __( 'What has been done or would be done: "create", "update", "reset" or "none".', 'personio-integration-light' ),
				),
				'id'       => array( 'type' => 'integer' ),
				'errors'   => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'warnings' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'message'  => array( 'type' => 'string' ),
			),
		);

		// add the ability to save a template.
		wp_register_ability(
			Abilities::ABILITY_CATEGORY . '/save-template',
			array(
				'label'               => __( 'Save template for positions', 'personio-integration-light' ),
				'description'         => __( 'Saves a template for the detail view ("single") or the list ("archive") of positions as customized template of the page builder, optionally with its CSS. The template is validated first and not saved if it has errors. Call it first with "dry_run": true, show the result to the user and save with "dry_run": false only after the user agreed. The previous version is kept as revision, reset-template restores the original template.', 'personio-integration-light' ),
				'category'            => Abilities::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'builder' => $this->get_builder_schema(),
						'type'    => $this->get_type_schema(),
						'content' => array(
							'type'        => 'string',
							'description' => __( 'The template content in the format of the page builder.', 'personio-integration-light' ),
						),
						'css'     => array(
							'type'        => 'string',
							'description' => __( 'CSS for this template, loaded only on the pages which use it. Omit it to keep the actual CSS, use an empty string to remove it.', 'personio-integration-light' ),
						),
						'dry_run' => array(
							'type'        => 'boolean',
							'description' => __( 'Only validate and return what would happen, without saving. Set to false to save.', 'personio-integration-light' ),
							'default'     => true,
						),
					),
					'required'   => array( 'content' ),
				),
				'output_schema'       => $change_output_schema,
				'execute_callback'    => array( $this, 'save_template' ),
				'permission_callback' => array( $this, 'has_template_permission' ),
				'meta'                => array(
					'show_in_rest' => true,
					'mcp'          => array( 'public' => true ), // expose via the MCP Adapter of WordPress.
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => false, // the previous version is kept as revision.
						'idempotent'  => true,
					),
				),
			)
		);

		// add the ability to reset a template.
		wp_register_ability(
			Abilities::ABILITY_CATEGORY . '/reset-template',
			array(
				'label'               => __( 'Reset template for positions', 'personio-integration-light' ),
				'description'         => __( 'Removes the customized template for the detail view ("single") or the list ("archive") of positions and its CSS, so the original template of this plugin or the theme is used again. The customized template is moved to the trash. Call it first with "dry_run": true and reset with "dry_run": false only after the user agreed.', 'personio-integration-light' ),
				'category'            => Abilities::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'builder' => $this->get_builder_schema(),
						'type'    => $this->get_type_schema(),
						'dry_run' => array(
							'type'        => 'boolean',
							'description' => __( 'Only return what would happen, without resetting. Set to false to reset.', 'personio-integration-light' ),
							'default'     => true,
						),
					),
					'default'    => array(),
				),
				'output_schema'       => $change_output_schema,
				'execute_callback'    => array( $this, 'reset_template' ),
				'permission_callback' => array( $this, 'has_template_permission' ),
				'meta'                => array(
					'show_in_rest' => true,
					'mcp'          => array( 'public' => true ), // expose via the MCP Adapter of WordPress.
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => true,
					),
				),
			)
		);
	}

	/**
	 * Return the list of page builders.
	 *
	 * @return array<string,mixed>
	 */
	public function get_builders(): array {
		$builders = array();
		foreach ( $this->get_adapters() as $adapter ) {
			$builders[] = array(
				'name'           => $adapter->get_name(),
				'label'          => $adapter->get_label(),
				'available'      => $adapter->is_available(),
				'reason'         => $adapter->is_available() ? '' : $adapter->get_unavailable_reason(),
				'template_types' => $adapter->get_template_types(),
				'can_save'       => $adapter->can_save(),
			);
		}

		return array( 'builders' => $builders );
	}

	/**
	 * Return the catalog to build templates with the requested page builder.
	 *
	 * @param array<string,mixed> $input The given input.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function get_catalog( array $input = array() ): array|WP_Error {
		// get the adapter.
		$adapter = $this->get_adapter( isset( $input['builder'] ) ? sanitize_key( (string) $input['builder'] ) : '' );
		if ( $adapter instanceof WP_Error ) {
			return $adapter;
		}

		// return the catalog.
		return array(
			'builder'        => $adapter->get_name(),
			'format'         => $adapter->get_format(),
			'template_types' => $adapter->get_template_types(),
			'elements'       => $adapter->get_elements(),
			'values'         => (object) $this->get_values(),
			'hints'          => $adapter->get_hints(),
		);
	}

	/**
	 * Return the template of the requested type.
	 *
	 * @param array<string,mixed> $input The given input.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function get_template( array $input = array() ): array|WP_Error {
		// get the adapter.
		$adapter = $this->get_adapter( isset( $input['builder'] ) ? sanitize_key( (string) $input['builder'] ) : '' );
		if ( $adapter instanceof WP_Error ) {
			return $adapter;
		}

		// get the type.
		$type = $this->get_type( $adapter, $input );
		if ( $type instanceof WP_Error ) {
			return $type;
		}

		// get the template.
		$template = $adapter->get_template( $type );
		if ( $template instanceof WP_Error ) {
			return $template;
		}

		// return the result.
		return array_merge(
			array(
				'builder' => $adapter->get_name(),
				'type'    => $type,
			),
			$template,
			array( 'css' => $adapter->get_css( $type ) )
		);
	}

	/**
	 * Validate and render the given template.
	 *
	 * @param array<string,mixed> $input The given input.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function preview_template( array $input = array() ): array|WP_Error {
		// get the adapter.
		$adapter = $this->get_adapter( isset( $input['builder'] ) ? sanitize_key( (string) $input['builder'] ) : '' );
		if ( $adapter instanceof WP_Error ) {
			return $adapter;
		}

		// get the type.
		$type = $this->get_type( $adapter, $input );
		if ( $type instanceof WP_Error ) {
			return $type;
		}

		// get the content.
		$content = isset( $input['content'] ) ? (string) $input['content'] : '';

		// validate the content.
		$validation = $adapter->validate( $type, $content );
		$errors     = isset( $validation['errors'] ) ? array_values( $validation['errors'] ) : array();
		$warnings   = isset( $validation['warnings'] ) ? array_values( $validation['warnings'] ) : array();

		// prepare the result.
		$result = array(
			'builder'   => $adapter->get_name(),
			'type'      => $type,
			'valid'     => empty( $errors ),
			'errors'    => $errors,
			'warnings'  => $warnings,
			'post_id'   => 0,
			'html'      => '',
			'truncated' => false,
		);

		// bail if the rendering is not requested or the content is empty.
		if ( ( isset( $input['render'] ) && ! $input['render'] ) || '' === trim( $content ) ) {
			return $result;
		}

		// get the position for a single template.
		$post_id = 0;
		if ( 'single' === $type ) {
			$post_id = $this->get_preview_post_id( isset( $input['post_id'] ) ? absint( $input['post_id'] ) : 0 );
			if ( $post_id instanceof WP_Error ) {
				$result['errors'][] = $post_id->get_error_message();
				$result['valid']    = false;
				return $result;
			}
		}
		$result['post_id'] = $post_id;

		// render the template.
		$html = $adapter->render( $type, $content, $post_id );
		if ( $html instanceof WP_Error ) {
			$result['errors'][] = $html->get_error_message();
			$result['valid']    = false;
			return $result;
		}

		// limit the length of the HTML.
		if ( \strlen( $html ) > self::MAX_PREVIEW_LENGTH ) {
			$html                = substr( $html, 0, self::MAX_PREVIEW_LENGTH );
			$result['truncated'] = true;
		}
		$result['html'] = $html;

		// return the result.
		return $result;
	}

	/**
	 * Validate and save the given template.
	 *
	 * @param array<string,mixed> $input The given input.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function save_template( array $input = array() ): array|WP_Error {
		// get the adapter.
		$adapter = $this->get_adapter( isset( $input['builder'] ) ? sanitize_key( (string) $input['builder'] ) : '' );
		if ( $adapter instanceof WP_Error ) {
			return $adapter;
		}

		// get the type.
		$type = $this->get_type( $adapter, $input );
		if ( $type instanceof WP_Error ) {
			return $type;
		}

		// get the parameters.
		$content = isset( $input['content'] ) ? (string) $input['content'] : '';
		$css     = isset( $input['css'] ) && \is_scalar( $input['css'] ) ? (string) $input['css'] : null;
		$dry_run = ! isset( $input['dry_run'] ) || (bool) $input['dry_run'];

		// prepare the result.
		$result = array(
			'builder'  => $adapter->get_name(),
			'type'     => $type,
			'dry_run'  => $dry_run,
			'done'     => false,
			'action'   => 'none',
			'id'       => 0,
			'errors'   => array(),
			'warnings' => array(),
			'message'  => '',
		);

		// bail if saving is not supported.
		if ( ! $adapter->can_save() ) {
			$result['errors'][] = __( 'Templates of this page builder can not be saved via abilities.', 'personio-integration-light' );
			return $result;
		}

		// check the CSS: it requires the same capability as the additional CSS of WordPress.
		if ( ! \is_null( $css ) ) {
			if ( ! current_user_can( 'edit_css' ) ) {
				$result['errors'][] = __( 'You are not allowed to save CSS. Omit the parameter "css".', 'personio-integration-light' );
			}
			if ( \strlen( $css ) > self::MAX_CSS_LENGTH ) {
				/* translators: %1$d will be replaced by a number. */
				$result['errors'][] = \sprintf( __( 'The CSS is too long, the maximum are %1$d characters.', 'personio-integration-light' ), self::MAX_CSS_LENGTH );
			}
		}

		// validate the content.
		$validation         = $adapter->validate( $type, $content );
		$result['errors']   = array_merge( $result['errors'], isset( $validation['errors'] ) ? array_values( $validation['errors'] ) : array() );
		$result['warnings'] = isset( $validation['warnings'] ) ? array_values( $validation['warnings'] ) : array();

		// bail on errors.
		if ( ! empty( $result['errors'] ) ) {
			$result['message'] = __( 'The template has not been saved. Fix the errors first.', 'personio-integration-light' );
			return $result;
		}

		// save the template (or check what would happen).
		$saved = $adapter->save_template( $type, $content, $css, $dry_run );
		if ( $saved instanceof WP_Error ) {
			$result['errors'][] = $saved->get_error_message();
			return $result;
		}
		$result['action'] = isset( $saved['action'] ) ? (string) $saved['action'] : 'none';
		$result['id']     = isset( $saved['id'] ) ? absint( $saved['id'] ) : 0;
		$result['done']   = ! $dry_run;

		// add the notes of the page builder, e.g. about other templates which are affected.
		if ( ! empty( $saved['notes'] ) && \is_array( $saved['notes'] ) ) {
			$result['warnings'] = array_merge( $result['warnings'], array_values( array_map( 'strval', $saved['notes'] ) ) );
		}

		// add a message.
		if ( $dry_run ) {
			$result['message'] = 'update' === $result['action'] ? __( 'The template is valid. Saving would replace the actual customized template (it is kept as revision). Ask the user before saving with "dry_run": false.', 'personio-integration-light' ) : __( 'The template is valid. Saving would create a customized template, which is used instead of the original template. Ask the user before saving with "dry_run": false.', 'personio-integration-light' );
		} else {
			$result['message'] = __( 'The template has been saved and is used on the website now.', 'personio-integration-light' );
		}

		// return the result.
		return $result;
	}

	/**
	 * Reset the template of the requested type.
	 *
	 * @param array<string,mixed> $input The given input.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function reset_template( array $input = array() ): array|WP_Error {
		// get the adapter.
		$adapter = $this->get_adapter( isset( $input['builder'] ) ? sanitize_key( (string) $input['builder'] ) : '' );
		if ( $adapter instanceof WP_Error ) {
			return $adapter;
		}

		// get the type.
		$type = $this->get_type( $adapter, $input );
		if ( $type instanceof WP_Error ) {
			return $type;
		}

		// get the parameters.
		$dry_run = ! isset( $input['dry_run'] ) || (bool) $input['dry_run'];

		// prepare the result.
		$result = array(
			'builder'  => $adapter->get_name(),
			'type'     => $type,
			'dry_run'  => $dry_run,
			'done'     => false,
			'action'   => 'none',
			'id'       => 0,
			'errors'   => array(),
			'warnings' => array(),
			'message'  => '',
		);

		// bail if saving is not supported.
		if ( ! $adapter->can_save() ) {
			$result['errors'][] = __( 'Templates of this page builder can not be reset via abilities.', 'personio-integration-light' );
			return $result;
		}

		// reset the template (or check what would happen).
		$reset = $adapter->reset_template( $type, $dry_run );
		if ( $reset instanceof WP_Error ) {
			$result['errors'][] = $reset->get_error_message();
			return $result;
		}
		$result['action'] = isset( $reset['action'] ) ? (string) $reset['action'] : 'none';
		$result['id']     = isset( $reset['id'] ) ? absint( $reset['id'] ) : 0;
		$result['done']   = ! $dry_run && 'reset' === $result['action'];

		// add the notes of the page builder, e.g. about other templates which are affected.
		if ( ! empty( $reset['notes'] ) && \is_array( $reset['notes'] ) ) {
			$result['warnings'] = array_merge( $result['warnings'], array_values( array_map( 'strval', $reset['notes'] ) ) );
		}

		// add a message.
		if ( 'none' === $result['action'] ) {
			$result['message'] = __( 'The template is not customized, the original template is used already.', 'personio-integration-light' );
		} elseif ( $dry_run ) {
			$result['message'] = __( 'Resetting would remove the customized template and its CSS (the template is moved to the trash). Ask the user before resetting with "dry_run": false.', 'personio-integration-light' );
		} else {
			$result['message'] = __( 'The customized template has been removed, the original template is used again.', 'personio-integration-light' );
		}

		// return the result.
		return $result;
	}

	/**
	 * Return the requested template type, if it is supported by the adapter.
	 *
	 * @param Template_Adapter_Base $adapter The adapter.
	 * @param array<string,mixed>   $input   The given input.
	 *
	 * @return string|WP_Error
	 */
	private function get_type( Template_Adapter_Base $adapter, array $input ): string|WP_Error {
		$type = isset( $input['type'] ) ? sanitize_key( (string) $input['type'] ) : 'single';
		if ( ! \array_key_exists( $type, $adapter->get_template_types() ) ) {
			return new WP_Error( 'personio_integration_unknown_template_type', __( 'The requested template type is not supported by this page builder.', 'personio-integration-light' ) );
		}
		return $type;
	}

	/**
	 * Return the post-ID of the position to use for the preview.
	 *
	 * @param int $post_id The requested post-ID (optional).
	 *
	 * @return int|WP_Error
	 */
	private function get_preview_post_id( int $post_id ): int|WP_Error {
		// use the requested position, if it is a published position.
		if ( $post_id > 0 ) {
			if ( PersonioPosition::get_instance()->get_name() !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
				return new WP_Error( 'personio_integration_unknown_position', __( 'The requested position does not exist or is not published. Use get-positions to get the post-IDs of positions.', 'personio-integration-light' ) );
			}
			return $post_id;
		}

		// otherwise, use the newest position.
		$query = new WP_Query(
			array(
				'post_type'      => PersonioPosition::get_instance()->get_name(),
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		if ( empty( $query->posts ) ) {
			return new WP_Error( 'personio_integration_no_positions', __( 'There are no positions for the preview. Import positions first.', 'personio-integration-light' ) );
		}
		$first = $query->posts[0];
		return \is_int( $first ) ? $first : absint( $first->ID );
	}

	/**
	 * Return a list of "value => label" as list for the catalog.
	 *
	 * @param array<int|string,mixed> $entries The list.
	 *
	 * @return array<int,array<string,string>>
	 */
	private function get_as_value_list( array $entries ): array {
		$result = array();
		foreach ( $entries as $name => $label ) {
			$result[] = array(
				'name'  => (string) $name,
				'label' => wp_strip_all_tags( \is_scalar( $label ) ? (string) $label : '' ),
			);
		}
		return $result;
	}

	/**
	 * Return the taxonomies of positions, which can be used in details and filters.
	 *
	 * @return array<int,array<string,string>>
	 */
	public function get_taxonomies(): array {
		$list = array();
		foreach ( Abilities::get_instance()->get_visible_taxonomies() as $taxonomy_name => $taxonomy ) {
			$labels = Taxonomies::get_instance()->get_taxonomy_label( $taxonomy_name );
			$list[] = array(
				'name'  => (string) $taxonomy['slug'],
				'label' => ! empty( $labels['name'] ) ? (string) $labels['name'] : (string) $taxonomy['slug'],
			);
		}
		return $list;
	}

	/**
	 * Return the allowed values for the attributes of the elements.
	 *
	 * @return array<string,array<int,array<string,string>>>
	 */
	public function get_values(): array {
		$values = array(
			'taxonomies'               => $this->get_taxonomies(),
			'listing_templates'        => $this->get_as_value_list( Templates::get_instance()->get_archive_templates() ),
			'excerpt_templates'        => $this->get_as_value_list( Templates::get_instance()->get_excerpts_templates() ),
			'jobdescription_templates' => $this->get_as_value_list( Templates::get_instance()->get_jobdescription_templates() ),
		);

		/**
		 * Filter the allowed values for the attributes of the elements in templates.
		 *
		 * Each entry is a list of "name" (the value to use) and "label".
		 *
		 * @since 5.3.0 Available since 5.3.0.
		 * @param array<string,array<int,array<string,string>>> $values The list of values.
		 */
		return apply_filters( 'personio_integration_template_ability_values', $values );
	}

	/**
	 * Return the allowed names of the given value list.
	 *
	 * @param string $name The name of the value list, e.g. "taxonomies".
	 *
	 * @return array<int,string>
	 */
	public function get_allowed_values( string $name ): array {
		$values = $this->get_values();
		if ( empty( $values[ $name ] ) ) {
			return array();
		}
		return array_values( array_map( 'strval', wp_list_pluck( $values[ $name ], 'name' ) ) );
	}
}

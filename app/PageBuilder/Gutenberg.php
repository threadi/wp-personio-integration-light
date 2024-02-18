<?php
/**
 * File to handle support for pagebuilder Gutenberg aka Block Editor.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Templates;
use PersonioIntegrationLight\PageBuilder_Base;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;

/**
 * Object to handle the Gutenberg support.
 */
class Gutenberg extends PageBuilder_Base {

	/**
	 * Instance of this object.
	 *
	 * @var ?Gutenberg
	 */
	private static ?Gutenberg $instance = null;

	/**
	 * Constructor for Init-Handler.
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
	public static function get_instance(): Gutenberg {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize this PageBuilder support.
	 *
	 * @return void
	 */
	public function init(): void {
		// bail if theme is not an FSE-theme with Block support.
		if ( ! $this->theme_support_block_templates() ) {
			// remove hint from settings.
			add_filter( 'personio_integration_settings', array( $this, 'remove_fse_hint' ) );
			return;
		}

		// add our custom templates.
		add_action( 'init', array( $this, 'add_templates' ) );

		// add our custom blocks.
		add_action( 'init', array( $this, 'add_blocks' ) );
	}

	/**
	 * Initialize the templates for Block Editor.
	 *
	 * @return void
	 */
	public function add_templates(): void {
		$templates_obj = Templates::get_instance();
		$templates_obj->init();
	}

	/**
	 * Check if active theme supports block templates.
	 *
	 * @return bool
	 * @noinspection PhpUndefinedFunctionInspection
	 */
	private function theme_support_block_templates(): bool {
		if (
			! $this->current_theme_is_fse_theme() &&
			( ! function_exists( 'gutenberg_supports_block_templates' ) || ! gutenberg_supports_block_templates() )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the current theme is a block theme.
	 *
	 * @return bool
	 * @noinspection PhpUndefinedFunctionInspection
	 */
	private function current_theme_is_fse_theme(): bool {
		if ( function_exists( 'wp_is_block_theme' ) ) {
			return (bool) wp_is_block_theme();
		}
		if ( function_exists( 'gutenberg_is_fse_theme' ) ) {
			return (bool) gutenberg_is_fse_theme();
		}
		return false;
	}

	/**
	 * Get detail-templates from attributes-array.
	 *
	 * @param array $attributes List of attributes.
	 * @return string
	 */
	private function get_details_array( array $attributes ): string {
		if ( ! empty( $attributes['excerptTemplates'] ) ) {
			return implode( ',', $attributes['excerptTemplates'] );
		}
		return '';
	}

	/**
	 * Generate template-string from given attributes.
	 *
	 * @param array $attributes List of attributes.
	 * @return string
	 */
	private function get_template_parts( array $attributes ): string {
		$templates = '';
		if ( $attributes['showTitle'] ) {
			$templates .= ( strlen( $templates ) > 0 ? ',' : '' ) . 'title';
		}
		if ( $attributes['showExcerpt'] ) {
			$templates .= ( strlen( $templates ) > 0 ? ',' : '' ) . 'excerpt';
		}
		if ( $attributes['showContent'] ) {
			$templates .= ( strlen( $templates ) > 0 ? ',' : '' ) . 'content';
		}
		if ( $attributes['showApplicationForm'] ) {
			$templates .= ( strlen( $templates ) > 0 ? ',' : '' ) . 'formular';
		}
		return $templates;
	}

	/**
	 * Return the block class depending on its blockId.
	 *
	 * @param array $attributes List of attributes.
	 * @return string
	 */
	private function get_block_class( array $attributes ): string {
		if ( ! empty( $attributes['blockId'] ) ) {
			return 'personio-integration-block-' . $attributes['blockId'];
		}
		return '';
	}

	/**
	 * Add our custom blocks.
	 *
	 * @return void
	 */
	public function add_blocks(): void {
		// include Blocks only if Gutenberg exists and the PersonioURL is set.
		if ( function_exists( 'register_block_type' ) && Helper::is_personio_url_set() ) {
			// collect attributes for single block.
			$single_attributes = array(
				'id'                  => array(
					'type'    => 'integer',
					'default' => 0,
				),
				'showTitle'           => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'linkTitle'           => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'showExcerpt'         => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'excerptTemplates'    => array(
					'type'    => 'array',
					'default' => array( 'recruitingCategory', 'schedule', 'office' ),
				),
				'showContent'         => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'showApplicationForm' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'blockId'             => array(
					'type' => 'string',
				),
			);
			/**
			 * Filter the attributes for single Block.
			 *
			 * @since 2.0.0 Available since 2.0.0
			 *
			 * @param array $single_attributes The settings as array.
			 */
			$single_attributes = apply_filters( 'personio_integration_gutenberg_block_single_attributes', $single_attributes );

			// register single block.
			register_block_type(
				plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/show/',
				array(
					'render_callback' => array( $this, 'get_single' ),
					'attributes'      => $single_attributes,
				)
			);

			// collect attributes for list block.
			$list_attributes = array(
				'preview'             => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'showFilter'          => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'filter'              => array(
					'type'    => 'array',
					'default' => array( 'recruitingCategory', 'schedule', 'office' ),
				),
				'filtertype'          => array(
					'type'    => 'string',
					'default' => 'linklist',
				),
				'limit'               => array(
					'type'    => 'integer',
					'default' => 0,
				),
				'template'            => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'sort'                => array(
					'type'    => 'string',
					'default' => 'asc',
				),
				'sortby'              => array(
					'type'    => 'string',
					'default' => 'title',
				),
				'groupby'             => array(
					'type'    => 'string',
					'default' => '',
				),
				'showTitle'           => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'linkTitle'           => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'showExcerpt'         => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'excerptTemplates'    => array(
					'type'    => 'array',
					'default' => array( 'recruitingCategory', 'schedule', 'office' ),
				),
				'showContent'         => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'showApplicationForm' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'blockId'             => array(
					'type'    => 'string',
					'default' => '',
				),
			);
			/**
			 * Filter the attributes for the List Block.
			 *
			 * @param array $list_attributes List of attributes.
			 */
			$list_attributes = apply_filters( 'personio_integration_gutenberg_block_list_attributes', $list_attributes );

			// register list block.
			register_block_type(
				plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/list/',
				array(
					'render_callback' => array( $this, 'get_archive' ),
					'attributes'      => $list_attributes,
				)
			);

			// collect attributes for filter-list block.
			$list_attributes = array(
				'preview'         => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'filter'          => array(
					'type'    => 'array',
					'default' => array( 'recruitingCategory', 'schedule', 'office' ),
				),
				'blockId'         => array(
					'type'    => 'string',
					'default' => '',
				),
				'hideResetLink'   => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'hideFilterTitle' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'space_between'   => array(
					'type'    => 'integer',
					'default' => 0,
				),
			);
			/**
			 * Filter the attributes for the Filter List Block.
			 *
			 * @param array $list_attributes List of attributes.
			 */
			$list_attributes = apply_filters( 'personio_integration_gutenberg_block_filter_list_attributes', $list_attributes );

			// register filter-list block.
			register_block_type(
				plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/filter-list/',
				array(
					'render_callback' => array( $this, 'get_filter_list' ),
					'attributes'      => $list_attributes,
				)
			);

			// collect attributes for filter-select block.
			$list_attributes = array(
				'preview'          => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'filter'           => array(
					'type'    => 'array',
					'default' => array( 'recruitingCategory', 'schedule', 'office' ),
				),
				'blockId'          => array(
					'type'    => 'string',
					'default' => '',
				),
				'hideResetLink'    => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'hideSubmitButton' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'hideFilterTitle'  => array(
					'type'    => 'boolean',
					'default' => false,
				),
			);
			/**
			 * Filter the attributes for the Filter Select Block.
			 *
			 * @param array $list_attributes List of attributes.
			 */
			$list_attributes = apply_filters( 'personio_integration_gutenberg_block_filter_select_attributes', $list_attributes );

			// register filter-list block.
			register_block_type(
				plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/filter-select/',
				array(
					'render_callback' => array( $this, 'get_filter_select' ),
					'attributes'      => $list_attributes,
				)
			);

			// collect attributes for application-button block.
			$list_attributes = array(
				'preview' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'blockId' => array(
					'type'    => 'string',
					'default' => '',
				),
			);
			/**
			 * Filter the attributes for the Application Button Block.
			 *
			 * @param array $list_attributes List of attributes.
			 */
			$list_attributes = apply_filters( 'personio_integration_gutenberg_block_application_button_select_attributes', $list_attributes );

			// register application-button block.
			register_block_type(
				plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/application-button/',
				array(
					'render_callback' => array( $this, 'get_application_button' ),
					'attributes'      => $list_attributes,
				)
			);

			// collect attributes for details block.
			$list_attributes = array(
				'preview'          => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'blockId'          => array(
					'type'    => 'string',
					'default' => '',
				),
				'template'         => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'excerptTemplates' => array(
					'type'    => 'array',
					'default' => array( 'recruitingCategory', 'schedule', 'office' ),
				),
				'colon'            => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'wrap'             => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'separator'        => array(
					'type'    => 'string',
					'default' => ', ',
				),
			);
			/**
			 * Filter the attributes for the Detail Block.
			 *
			 * @param array $list_attributes List of attributes.
			 */
			$list_attributes = apply_filters( 'personio_integration_gutenberg_block_detail_attributes', $list_attributes );

			// register details block.
			register_block_type(
				plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/details/',
				array(
					'render_callback' => array( $this, 'get_details' ),
					'attributes'      => $list_attributes,
				)
			);

			// collect attributes for description block.
			$list_attributes = array(
				'template' => array(
					'type'    => 'string',
					'default' => 'default',
				),
				'preview'  => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'blockId'  => array(
					'type'    => 'string',
					'default' => '',
				),
			);
			/**
			 * Filter the attributes for the Description Block.
			 *
			 * @param array $list_attributes List of attributes.
			 */
			$list_attributes = apply_filters( 'personio_integration_gutenberg_block_description_attributes', $list_attributes );

			// register details block.
			register_block_type(
				plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/description/',
				array(
					'render_callback' => array( $this, 'get_description' ),
					'attributes'      => $list_attributes,
				)
			);

			// register translations.
			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'wp-personio-integration-show-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
				wp_set_script_translations( 'wp-personio-integration-list-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
				wp_set_script_translations( 'wp-personio-integration-filter-list-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
				wp_set_script_translations( 'wp-personio-integration-filter-select-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
				wp_set_script_translations( 'wp-personio-integration-application-button-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
				wp_set_script_translations( 'wp-personio-integration-details-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
				wp_set_script_translations( 'wp-personio-integration-description-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
			}
		}
	}

	/**
	 * Get the content for single position.
	 *
	 * @param array $attributes List of attributes for this position.
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_single( array $attributes ): string {
		// link title?
		$do_not_link = true;
		if ( $attributes['linkTitle'] ) {
			$do_not_link = false;
		}

		// set ID as class.
		$class = $this->get_block_class( $attributes );

		// get block-classes.
		$styles_array          = array();
		$block_html_attributes = '';
		if ( function_exists( 'get_block_wrapper_attributes' ) ) {
			$block_html_attributes = get_block_wrapper_attributes();

			// get styles.
			$styles = Helper::get_attribute_value_from_html( 'style', $block_html_attributes );
			if ( ! empty( $styles ) ) {
				$styles_array[] = '.entry.' . $class . ' { ' . $styles . ' }';
			}
		}

		// define attribute-defaults.
		$attribute_defaults = array(
			'templates'  => $this->get_template_parts( $attributes ),
			'excerpt'    => $this->get_details_array( $attributes ),
			'donotlink'  => $do_not_link,
			'personioid' => $attributes['id'],
			'styles'     => implode( PHP_EOL, $styles_array ),
			'classes'    => $class . ' ' . Helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
		);

		/**
		 * Filter the attributes for the single template.
		 *
		 * @param array $list_attributes List of attributes.
		 */
		return PersonioPosition::get_instance()->shortcode_single( apply_filters( 'personio_integration_get_gutenberg_single_attributes', $attribute_defaults ) );
	}

	/**
	 * Get the archive of positions.
	 *
	 * @param array $attributes List of attributes.
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_archive( array $attributes ): string {
		// collect the configured templates.
		$templates = $this->get_template_parts( $attributes );

		// set ID as class.
		$class = $this->get_block_class( $attributes );

		// get block-classes.
		$styles_array          = array();
		$block_html_attributes = '';
		if ( function_exists( 'get_block_wrapper_attributes' ) ) {
			$block_html_attributes = get_block_wrapper_attributes();

			// get styles.
			$styles = Helper::get_attribute_value_from_html( 'style', $block_html_attributes );
			if ( ! empty( $styles ) ) {
				$styles_array[] = '.' . $class . ' { ' . $styles . ' }';
			}
			if ( ! empty( $attributes['style'] ) && ! empty( $attributes['style']['spacing'] ) && ! empty( $attributes['style']['spacing']['blockGap'] ) ) {
				$value = $attributes['style']['spacing']['blockGap'];
				// convert var-setting to var-style-entity.
				if ( str_contains( $attributes['style']['spacing']['blockGap'], 'var:' ) ) {
					$value = str_replace( '|', '--', $value );
					$value = str_replace( 'var:', '', $value );
					$value = 'var(--wp--' . $value . ')';
				}
				$styles_array[] = 'body .' . $class . ' { margin-bottom: ' . $value . '; }';
			}
		}

		// collect all settings for this block.
		$attribute_defaults = array(
			'templates'         => $templates,
			'excerpt'           => $this->get_details_array( $attributes ),
			'donotlink'         => ! $attributes['linkTitle'],
			'sort'              => $attributes['sort'],
			'sortby'            => $attributes['sortby'],
			'groupby'           => $attributes['groupby'],
			'limit'             => absint( $attributes['limit'] ),
			'filter'            => implode( ',', $attributes['filter'] ),
			'filtertype'        => $attributes['filtertype'],
			'showfilter'        => $attributes['showFilter'],
			'show_back_to_list' => '',
			'styles'            => implode( PHP_EOL, $styles_array ),
			'classes'           => $class . ' ' . Helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
			'listing_template'  => $attributes['template'],
		);

		/**
		 * Filter the attributes for the archive template.
		 *
		 * @param array $list_attributes List of attributes.
		 */
		return PersonioPosition::get_instance()->shortcode_archive( apply_filters( 'personio_integration_get_gutenberg_list_attributes', $attribute_defaults, $attributes ) );
	}

	/**
	 * Get the filter as linklist.
	 *
	 * @param array $attributes List of attributes.
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_filter_list( array $attributes ): string {
		// set ID as class.
		$class = $this->get_block_class( $attributes );

		// get block-classes.
		$styles_array          = array();
		$block_html_attributes = '';
		if ( function_exists( 'get_block_wrapper_attributes' ) ) {
			$block_html_attributes = get_block_wrapper_attributes();

			// get styles.
			$styles = Helper::get_attribute_value_from_html( 'style', $block_html_attributes );
			if ( ! empty( $styles ) ) {
				$styles_array[] = '.' . $class . ' { ' . $styles . ' }';
			}

			if ( ! empty( $class ) ) {
				if ( ! empty( $attributes['hideResetLink'] ) ) {
					$styles_array[] = '.entry.' . $class . ' .personio-position-filter-reset { display: none }';
				}
				if ( ! empty( $attributes['hideFilterTitle'] ) ) {
					$styles_array[] = '.entry.' . $class . ' legend { display: none }';
				}
				if ( ! empty( $attributes['space_between'] ) ) {
					$styles_array[] = '.entry.' . $class . ' .personio-position-filter-linklist > div { margin-right: ' . $attributes['space_between'] . 'px }';
				}
			}
		}

		// collect all settings for this block.
		$attributes = array(
			'templates'  => '',
			'filter'     => implode( ',', $attributes['filter'] ),
			'filtertype' => 'linklist',
			'showfilter' => true,
			'styles'     => implode( PHP_EOL, $styles_array ),
			'classes'    => $class . ' ' . Helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
		);

		/**
		 * Filter the attributes for the Filter List template.
		 *
		 * @param array $list_attributes List of attributes.
		 */
		return PersonioPosition::get_instance()->shortcode_archive( apply_filters( 'personio_integration_get_gutenberg_filter_list_attributes', $attributes ) );
	}

	/**
	 * Get the filter as select-boxes.
	 *
	 * @param array $attributes List of attributes.
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_filter_select( array $attributes ): string {
		// set ID as class.
		$class = $this->get_block_class( $attributes );

		// get block-classes.
		$styles_array          = array();
		$block_html_attributes = '';
		if ( function_exists( 'get_block_wrapper_attributes' ) ) {
			$block_html_attributes = get_block_wrapper_attributes();

			// get styles.
			$styles = Helper::get_attribute_value_from_html( 'style', $block_html_attributes );
			if ( ! empty( $styles ) ) {
				$styles_array[] = '.' . $class . ' { ' . $styles . ' }';
			}

			if ( ! empty( $class ) ) {
				if ( ! empty( $attributes['hideResetLink'] ) ) {
					$styles_array[] = '.entry.' . $class . ' .personio-position-filter-reset { display: none }';
				}
				if ( ! empty( $attributes['hideSubmitButton'] ) ) {
					$styles_array[] = '.entry.' . $class . ' button { display: none }';
				}
				if ( ! empty( $attributes['hideFilterTitle'] ) ) {
					$styles_array[] = '.entry.' . $class . ' legend { display: none }';
				}
			}
		}

		// collect all settings for this block.
		$attributes = array(
			'templates'  => '',
			'filter'     => implode( ',', $attributes['filter'] ),
			'filtertype' => 'select',
			'showfilter' => true,
			'styles'     => implode( PHP_EOL, $styles_array ),
			'classes'    => $class . ' ' . Helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
		);

		/**
		 * Filter the attributes for the Filter Select template.
		 *
		 * @param array $list_attributes List of attributes.
		 */
		return PersonioPosition::get_instance()->shortcode_archive( apply_filters( 'personio_integration_get_gutenberg_filter_select_attributes', $attributes ) );
	}

	/**
	 * Return application-button.
	 *
	 * @param array $attributes List of attributes.
	 *
	 * @return string
	 */
	public function get_application_button( array $attributes ): string {
		$position = $this->get_position_by_request();
		if ( ( $position instanceof Position && ! $position->is_valid() ) || ! ( $position instanceof Position ) ) {
			return '';
		}

		// set ID as class.
		$class = '';
		if ( ! empty( $attributes['blockId'] ) ) {
			$class = 'personio-integration-block-' . $attributes['blockId'];
		}

		// get block-classes.
		$styles_array          = array();
		$block_html_attributes = '';
		if ( function_exists( 'get_block_wrapper_attributes' ) ) {
			$block_html_attributes = get_block_wrapper_attributes();

			// get styles.
			$styles = Helper::get_attribute_value_from_html( 'style', $block_html_attributes );
			if ( ! empty( $styles ) ) {
				$styles_array[] = '.entry.' . $class . ' { ' . $styles . ' }';
			}
		}

		$attributes = array(
			'personioid' => absint( $position->get_personio_id() ),
			'templates'  => array( 'formular' ),
			'styles'     => implode( PHP_EOL, $styles_array ),
			'classes'    => $class . ' ' . Helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
		);

		// get the output.
		ob_start();
		\PersonioIntegrationLight\Plugin\Templates::get_instance()->get_application_link_template( $position, $attributes );
		return ob_get_clean();
	}

	/**
	 * Get the list chosen details of single position.
	 *
	 * @param array $attributes List of attributes.
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_details( array $attributes ): string {
		$position = $this->get_position_by_request();
		if ( ( $position instanceof Position && ! $position->is_valid() ) || ! ( $position instanceof Position ) ) {
			return '';
		}

		// get setting for colon.
		$colon = ': ';
		if ( false === $attributes['colon'] ) {
			$colon = '';
		}

		// get setting for line break.
		$line_break = '<br>';
		if ( false === $attributes['wrap'] ) {
			$line_break = '';
		}

		// get separator.
		$separator = get_option( 'personioIntegrationTemplateExcerptSeparator' ) . ' ';
		if ( ! empty( $attributes['separator'] ) ) {
			$separator = $attributes['separator'];
		}

		// get settings for templates.
		$template = 'default';
		if ( ! empty( $attributes['template'] ) ) {
			$template = $attributes['template'];
		}

		$details = array();

		// loop through the chosen details.
		foreach ( $attributes['excerptTemplates'] as $detail ) {
			// get the terms of this taxonomy.
			foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
				if ( $detail === $taxonomy['slug'] ) {
					// get value.
					$value = $position->get_term_by_field( $taxonomy_name, 'name' );

					// bail if no value is available.
					if ( empty( $value ) ) {
						continue;
					}

					// get labels of this taxonomy.
					$labels = Taxonomies::get_instance()->get_taxonomy_label( $taxonomy_name );

					$details[ $labels['name'] ] = $value;
				}
			}
		}

		// get content for output.
		ob_start();
		include \PersonioIntegrationLight\Plugin\Templates::get_instance()->get_template( 'parts/details/' . $template . '.php' );
		return ob_get_clean();
	}

	/**
	 * Get the job description of single position.
	 *
	 * @param array $attributes List of attributes.
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_description( array $attributes ): string {
		$position = $this->get_position_by_request();
		if ( ( $position instanceof Position && ! $position->is_valid() ) || ! ( $position instanceof Position ) ) {
			return '';
		}

		// set ID as class.
		$class = '';
		if ( ! empty( $attributes['blockId'] ) ) {
			$class = 'personio-integration-block-' . $attributes['blockId'];
		}

		// get block-classes.
		$styles_array          = array();
		$block_html_attributes = '';
		if ( function_exists( 'get_block_wrapper_attributes' ) ) {
			$block_html_attributes = get_block_wrapper_attributes();

			// get styles.
			$styles = Helper::get_attribute_value_from_html( 'style', $block_html_attributes );
			if ( ! empty( $styles ) ) {
				$styles_array[] = '.entry.' . $class . ' { ' . $styles . ' }';
			}
		}

		$attributes = array(
			'personioid'              => absint( $position->get_personio_id() ),
			'jobdescription_template' => empty( $attributes['template'] ) ? get_option( 'personioIntegrationTemplateJobDescription' ) : $attributes['template'],
			'templates'               => array( 'content' ),
			'styles'                  => implode( PHP_EOL, $styles_array ),
			'classes'                 => $class . ' ' . Helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
		);

		// get the output.
		ob_start();
		\PersonioIntegrationLight\Plugin\Templates::get_instance()->get_content_template( $position, $attributes );
		return ob_get_clean();
	}

	/**
	 * Get Position as object by request.
	 *
	 * @return Position|false
	 */
	public function get_position_by_request(): Position|false {
		// get positions object.
		$positions = Positions::get_instance();

		// get the position as object.
		// -> is no id is available choose a random one (e.g. for preview in Gutenberg).
		$post_id = get_the_ID();
		if ( empty( $post_id ) ) {
			$position_array = $positions->get_positions( 1 );
			$position       = $position_array[0];
		} else {
			$position = $positions->get_position( $post_id );
		}

		// return the object.
		return $position;
	}

	/**
	 * Remove the FSE-hint from settings.
	 *
	 * @param array $settings Array with the settings.
	 *
	 * @return array
	 */
	public function remove_fse_hint( array $settings ): array {
		if ( isset( $settings['settings_section_template_list']['fields']['personio_integration_fse_theme_hint'] ) ) {
			unset( $settings['settings_section_template_list']['fields']['personio_integration_fse_theme_hint'] );
		}
		return $settings;
	}
}

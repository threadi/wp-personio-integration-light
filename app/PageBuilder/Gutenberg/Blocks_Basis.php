<?php
/**
 * File to handle main functions for each block.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder\Gutenberg;

// prevent direct access.
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\Positions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Object to handle main functions for each block.
 */
class Blocks_Basis {
	/**
	 * Internal name of this block.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * Path to the directory where block.json resides.
	 *
	 * @var string
	 */
	protected string $path = '';

	/**
	 * Attributes this block is using.
	 *
	 * @var array
	 */
	protected array $attributes = array();

	/**
	 * The instance of this object.
	 *
	 * @var Blocks_Basis|null
	 */
	private static ?Blocks_Basis $instance = null;

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
	public static function get_instance(): Blocks_Basis {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Register this block.
	 *
	 * @return void
	 */
	public function register(): void {
		register_block_type(
			$this->get_path(),
			array(
				'render_callback' => array( $this, 'render' ),
				'attributes'      => $this->get_attributes(),
			)
		);

		// embed translation if available.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wp-personio-integration-'.$this->get_name().'-editor-script', 'personio-integration-light', Helper::get_plugin_path() . 'languages/' );
		}
	}

	/**
	 * Return the block class depending on its blockId.
	 *
	 * @param array $attributes List of attributes.
	 *
	 * @return string
	 */
	protected function get_block_class( array $attributes ): string {
		if ( ! empty( $attributes['blockId'] ) ) {
			return 'personio-integration-block-' . $attributes['blockId'];
		}
		return '';
	}

	/**
	 * Generate template-string from given attributes.
	 *
	 * @param array $attributes List of attributes.
	 * @return string
	 */
	protected function get_template_parts( array $attributes ): string {
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
	 * Get detail-templates from attributes-array.
	 *
	 * @param array $attributes List of attributes.
	 * @return string
	 */
	protected function get_details_array( array $attributes ): string {
		if ( ! empty( $attributes['excerptTemplates'] ) ) {
			return implode( ',', $attributes['excerptTemplates'] );
		}
		return '';
	}

	/**
	 * Return the list of attributes for this block.
	 *
	 * @return array
	 */
	protected function get_attributes(): array {
		$single_attributes = $this->attributes;
		/**
		 * Filter the attributes for a Block.
		 *
		 * @since 2.0.0 Available since 2.0.0
		 *
		 * @param array $single_attributes The settings as array.
		 */
		return apply_filters( 'personio_integration_gutenberg_block_'.$this->get_name().'_attributes', $single_attributes );
	}

	/**
	 * Return absolute path to JSON of this block.
	 *
	 * @return string
	 */
	protected function get_path(): string {
		$path = Helper::get_plugin_path() . $this->path;
		/**
		 * Filter the path of a Block.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param string $path The absolute path to the block.json.
		 */
		return apply_filters( 'personio_integration_gutenberg_block_'.$this->get_name().'_path', $path );
	}

	/**
	 * Return the internal name of this block.
	 *
	 * @return string
	 */
	protected function get_name(): string {
		return $this->name;
	}

	/**
	 * Get Position as object by request.
	 *
	 * Hints:
	 * - Bug https://github.com/WordPress/gutenberg/issues/40714 prevents clean usage in Query Loop (backend bad, frontend ok)
	 *
	 * @return Position|false
	 */
	protected function get_position_by_request(): Position|false {
		// get positions object.
		$positions = Positions::get_instance();

		// get the position as object.
		// -> is no id is available choose a random one (e.g. for preview in Gutenberg).
		$post_id = get_the_ID();
		$position_obj = false;
		if( $post_id > 0 ) {
			$position_obj = $positions->get_position( $post_id );
			if( ! $position_obj->is_valid() ) {
				$position_obj = false;
			}
		}

		// fallback: get a random position.
		if ( ! $position_obj ) {
			$position_array = $positions->get_positions( 1 );
			$position_obj       = $position_array[0];
		}

		// bail if no valid position object could be loaded.
		if( ! $position_obj->is_valid() ) {
			return false;
		}

		// return the object.
		return $position_obj;
	}
}
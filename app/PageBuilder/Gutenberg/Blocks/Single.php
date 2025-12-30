<?php
/**
 * File to handle the single position block.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks_Basis;

/**
 * Object to handle this block.
 */
class Single extends Blocks_Basis {

	/**
	 * Internal name of this block.
	 *
	 * @var string
	 */
	protected string $name = 'show';

	/**
	 * Path to the directory where block.json resides.
	 *
	 * @var string
	 */
	protected string $path = 'blocks/show/';

	/**
	 * Attributes this block is using.
	 *
	 * @var array<string,array<string,mixed>>
	 */
	protected array $attributes = array(
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
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Single
	 */
	private static ?Single $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Single {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the content for single position.
	 *
	 * @param array<string,mixed> $attributes List of attributes for this position.
	 * @return string
	 */
	public function render( array $attributes ): string {
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
			'personioid' => (string)$attributes['id'],
			'styles'     => implode( PHP_EOL, $styles_array ),
			'classes'    => $class . ' ' . Helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
		);

		/**
		 * Filter the attributes for this template.
		 *
		 * @since 2.5.0 Available since 2.5.0
		 *
		 * @param array $attribute_defaults List of attributes to use.
		 * @param array $attributes List of attributes vom PageBuilder.
		 */
		$attributes = apply_filters( 'personio_integration_get_list_attributes', $attribute_defaults, $attributes );

		// return the rendered content.
		return \PersonioIntegrationLight\PersonioIntegration\Widgets\Single::get_instance()->render( $attributes );
	}
}

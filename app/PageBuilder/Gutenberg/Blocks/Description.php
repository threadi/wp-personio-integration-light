<?php
/**
 * File to handle the description block.
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
class Description extends Blocks_Basis {

	/**
	 * Internal name of this block.
	 *
	 * @var string
	 */
	protected string $name = 'description';

	/**
	 * Path to the directory where block.json resides.
	 *
	 * @var string
	 */
	protected string $path = 'blocks/description/';

	/**
	 * Attributes this block is using.
	 *
	 * @var array<string,array<string,mixed>>
	 */
	protected array $attributes = array(
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
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Description
	 */
	private static ?Description $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Description {
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
		// set ID as class.
		$classes = '';
		if ( ! empty( $attributes['blockId'] ) ) {
			$classes = 'personio-integration-block-' . $attributes['blockId'];
		}

		// get block-classes.
		$styles_array          = array();
		$block_html_attributes = '';
		if ( function_exists( 'get_block_wrapper_attributes' ) ) {
			$block_html_attributes = get_block_wrapper_attributes();

			// get styles.
			$styles = Helper::get_attribute_value_from_html( 'style', $block_html_attributes );
			if ( ! empty( $styles ) ) {
				$styles_array[] = '.entry-content.' . $classes . ' { ' . $styles . ' }';
			}
		}

		// add the attributes.
		$attributes['styles'] = implode( PHP_EOL, $styles_array );
		$attributes['classes'] = $classes . ' ' . Helper::get_attribute_value_from_html( 'class', $block_html_attributes );

		// return the rendered template.
		return \PersonioIntegrationLight\PersonioIntegration\Widgets\Description::get_instance()->render( $attributes );
	}
}

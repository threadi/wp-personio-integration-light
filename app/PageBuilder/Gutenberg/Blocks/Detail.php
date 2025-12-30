<?php
/**
 * File to handle the detail block.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks_Basis;
use PersonioIntegrationLight\PersonioIntegration\Widgets\Details;
use PersonioIntegrationLight\Plugin\Languages;

/**
 * Object to handle this block.
 */
class Detail extends Blocks_Basis {

	/**
	 * Internal name of this block.
	 *
	 * @var string
	 */
	protected string $name = 'details';

	/**
	 * Path to the directory where block.json resides.
	 *
	 * @var string
	 */
	protected string $path = 'blocks/details/';

	/**
	 * Attributes this block is using.
	 *
	 * @var array<string,array<string,mixed>>
	 */
	protected array $attributes = array(
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
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Detail
	 */
	private static ?Detail $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Detail {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Return the content for single position.
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

		// map the settings.
		$attributes['excerpt'] = $attributes['excerptTemplates'];
		$attributes['lang'] = Languages::get_instance()->get_current_lang();
		$attributes['line_break'] = $attributes['wrap'];
		$attributes['excerpt_template'] = $attributes['template'];

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
		return Details::get_instance()->render( $attributes );
	}
}

<?php
/**
 * File to handle the application button block.
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
class Application_Button extends Blocks_Basis {

	/**
	 * Internal name of this block.
	 *
	 * @var string
	 */
	protected string $name = 'application-button';

	/**
	 * Path to the directory where block.json resides.
	 *
	 * @var string
	 */
	protected string $path = 'blocks/application-button/';

	/**
	 * Attributes this block is using.
	 *
	 * @var array<string,array<string,mixed>>
	 */
	protected array $attributes = array(
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
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Application_Button
	 */
	private static ?Application_Button $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Application_Button {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Return the content of the application button.
	 *
	 * @param array<string,mixed> $attributes List of attributes for this position.
	 * @return string
	 */
	public function render( array $attributes ): string {
		// set ID as class.
		$classes = $this->get_block_class( $attributes );

		// get block-classes.
		$styles_array          = array();
		$block_html_attributes = '';
		if ( function_exists( 'get_block_wrapper_attributes' ) ) {
			$block_html_attributes = get_block_wrapper_attributes();

			// get styles.
			$styles = Helper::sanitize_css_declarations( (string) Helper::get_attribute_value_from_html( 'style', $block_html_attributes ) );
			if ( ! empty( $styles ) && ! empty( $classes ) ) {
				$styles_array[] = '.entry.' . $classes . ' { ' . $styles . ' }';
			}
		}

		$attributes = array(
			'templates'  => array( 'formular' ),
			'styles'     => implode( PHP_EOL, $styles_array ),
			'classes'    => $classes . ' ' . Helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
		);
		return \PersonioIntegrationLight\PersonioIntegration\Widgets\Application_Button::get_instance()->render( $attributes );
	}
}

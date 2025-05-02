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
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Templates;

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
	 * Variable for instance of this Singleton object.
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
	 * Get the content for single position.
	 *
	 * @param array<string,mixed> $attributes List of attributes for this position.
	 * @return string
	 */
	public function render( array $attributes ): string {
		$position = $this->get_position_by_request();
		if ( ! ( $position instanceof Position ) || ! $position->is_valid() ) {
			return '';
		}

		// set actual language.
		$position->set_lang( Languages::get_instance()->get_current_lang() );

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
		Templates::get_instance()->get_application_link_template( $position, $attributes );
		$content = ob_get_clean();
		if( ! $content ) {
			return '';
		}
		return $content;
	}
}

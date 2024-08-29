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
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Templates;

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
	 * @var array
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
	 * Get the content for single position.
	 *
	 * @param array $attributes List of attributes for this position.
	 * @return string
	 */
	public function render( array $attributes ): string {
		$position = $this->get_position_by_request();
		if ( ( $position instanceof Position && ! $position->is_valid() ) || ! ( $position instanceof Position ) ) {
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
			'personioid'              => absint( $position->get_personio_id() ),
			'jobdescription_template' => empty( $attributes['template'] ) ? get_option( 'personioIntegrationTemplateJobDescription' ) : $attributes['template'],
			'templates'               => array( 'content' ),
			'styles'                  => implode( PHP_EOL, $styles_array ),
			'classes'                 => $class . ' ' . Helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
		);

		// get the output.
		ob_start();
		Templates::get_instance()->get_content_template( $position, $attributes );
		return ob_get_clean();
	}
}

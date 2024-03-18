<?php
/**
 * File to handle the filter list block.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks;

// prevent direct access.
defined( 'ABSPATH' ) or exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks_Basis;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;

/**
 * Object to handle this block.
 */
class Filter_List extends Blocks_Basis {

	/**
	 * Internal name of this block.
	 *
	 * @var string
	 */
	protected string $name = 'filter-list';

	/**
	 * Path to the directory where block.json resides.
	 *
	 * @var string
	 */
	protected string $path = 'blocks/filter-list/';

	/**
	 * Attributes this block is using.
	 *
	 * @var array
	 */
	protected array $attributes = array(
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
	 * Get the content for single position.
	 *
	 * @param array $attributes List of attributes for this position.
	 * @return string
	 */
	public function render( array $attributes ): string {
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
		$attribute_defaults = array(
			'templates'  => '',
			'filter'     => implode( ',', $attributes['filter'] ),
			'filtertype' => 'linklist',
			'showfilter' => true,
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
		return PersonioPosition::get_instance()->shortcode_archive( apply_filters( 'personio_integration_get_list_attributes', $attribute_defaults, $attributes ) );
	}
}

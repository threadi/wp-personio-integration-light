<?php
/**
 * File to handle the archive position block.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks_Basis;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;

/**
 * Object to handle this block.
 */
class Archive extends Blocks_Basis {

	/**
	 * Internal name of this block.
	 *
	 * @var string
	 */
	protected string $name = 'list';

	/**
	 * Path to the directory where block.json resides.
	 *
	 * @var string
	 */
	protected string $path = 'blocks/list/';

	/**
	 * Attributes this block is using.
	 *
	 * @var array
	 */
	protected array $attributes = array(
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
	 * Get the content for single position.
	 *
	 * @param array $attributes List of attributes for this position.
	 * @return string
	 */
	public function render( array $attributes ): string {
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

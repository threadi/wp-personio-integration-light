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
	 * @var array<string,array<string,mixed>>
	 */
	protected array $attributes = array(
		'preview'             => array(
			'type'    => 'boolean',
			'default' => false,
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
		'positionBackgroundColor' => array(
			'type' => 'string',
			'default' => ''
		),
		'positionBackgroundColorHover' => array(
			'type' => 'string',
			'default' => ''
		)
	);

	/**
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Archive
	 */
	private static ?Archive $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Archive {
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
		// collect the configured templates.
		$templates = $this->get_template_parts( $attributes );

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
				$styles_array[] = '.' . $classes . ' { ' . $styles . ' }';
			}
			// blockGap: only strings, convert preset notation, then sanitize.
			$block_gap = $attributes['style']['spacing']['blockGap'] ?? '';
			if ( is_string( $block_gap ) && '' !== $block_gap ) {
				if ( str_contains( $block_gap, 'var:' ) ) {
					$block_gap = 'var(--wp--' . str_replace( array( '|', 'var:' ), array( '--', '' ), $block_gap ) . ')';
				}
				$declaration = Helper::get_css_declaration( 'margin-bottom', $block_gap );
				if ( ! empty( $declaration ) && ! empty( $classes ) ) {
					$styles_array[] = 'body .' . $classes . ' { ' . $declaration . ' }';
				}
			}
		}

		// set background for single positions in the list.
		$selector = ! empty( $classes ) ? '.' . $classes : '.wp-block-wp-personio-integration-list';
		$bg = Helper::sanitize_css_color( ! empty( $attributes['positionBackgroundColor'] ) ? $attributes['positionBackgroundColor'] : '' );
		if ( ! empty( $bg ) ) {
			$styles_array[] = $selector . ' .personioposition { background-color: ' . $bg . ' }';
		}
		$bg_hover = Helper::sanitize_css_color( ! empty( $attributes['positionBackgroundColorHover'] ) ? $attributes['positionBackgroundColorHover'] : '' );
		if ( ! empty( $bg_hover ) ) {
			$styles_array[] = $selector . ' .personioposition:hover { background-color: ' . $bg_hover . ' }';
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
			'showfilter'        => false,
			'show_back_to_list' => '',
			'styles'            => implode( PHP_EOL, $styles_array ),
			'classes'           => $classes . ' ' . Helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
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
		return \PersonioIntegrationLight\PersonioIntegration\Widgets\Archive::get_instance()->render( apply_filters( 'personio_integration_get_list_attributes', $attribute_defaults, $attributes ) );
	}
}

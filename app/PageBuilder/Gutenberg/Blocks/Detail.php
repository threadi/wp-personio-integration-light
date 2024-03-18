<?php
/**
 * File to handle the detail block.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks;

// prevent direct access.
defined( 'ABSPATH' ) or exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks_Basis;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use PersonioIntegrationLight\Plugin\Templates;

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
	 * @var array
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
	 * Get the content for single position.
	 *
	 * @param array $attributes List of attributes for this position.
	 * @return string
	 */
	public function render( array $attributes ): string {
		$position = $this->get_position_by_request();
		if ( ! ( $position instanceof Position ) || ! $position->is_valid() ) {
			return '';
		}

		// get setting for colon.
		$colon = ': ';
		if ( false === $attributes['colon'] ) {
			$colon = '';
		}

		// get setting for line break.
		$line_break = '<br>';
		if ( false === $attributes['wrap'] ) {
			$line_break = '';
		}

		// get separator.
		$separator = get_option( 'personioIntegrationTemplateExcerptSeparator' ) . ' ';
		if ( ! empty( $attributes['separator'] ) ) {
			$separator = $attributes['separator'];
		}

		// get settings for templates.
		$template = 'default';
		if ( ! empty( $attributes['template'] ) ) {
			$template = $attributes['template'];
		}

		$details = array();

		// loop through the chosen details.
		foreach ( $attributes['excerptTemplates'] as $detail ) {
			// get the terms of this taxonomy.
			foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
				if ( $detail === $taxonomy['slug'] ) {
					// get value.
					$value = $position->get_term_by_field( $taxonomy_name, 'name' );

					// bail if no value is available.
					if ( empty( $value ) ) {
						continue;
					}

					// get labels of this taxonomy.
					$labels = Taxonomies::get_instance()->get_taxonomy_label( $taxonomy_name );

					$details[ $labels['name'] ] = $value;
				}
			}
		}

		// get content for output.
		ob_start();
		include Templates::get_instance()->get_template( 'parts/details/' . $template . '.php' );
		return ob_get_clean();
	}
}

<?php
/**
 * File to handle the detail widget.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Widgets;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use PersonioIntegrationLight\PersonioIntegration\Widget_Base;
use PersonioIntegrationLight\Plugin\Templates;
use WP_Term;

/**
 * Object to handle the detail widget.
 */
class Details extends Widget_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'widget_details';

	/**
	 * Name if the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationWidgetDetailStatus';

	/**
	 * Name if the setting tab where the setting field is visible.
	 *
	 * @var string
	 */
	protected string $setting_tab = '';

	/**
	 * Path to Block object.
	 *
	 * @var string
	 */
	protected string $gutenberg = '\PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Detail';

	/**
	 * Instance of this object.
	 *
	 * @var ?Details
	 */
	private static ?Details $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Details {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Details', 'personio-integration-light' );
	}

	/**
	 * Return whether this extension is enabled (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return 1 === absint( get_option( $this->get_settings_field_name() ) );
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return __('Provides a widget to show the details of a single Personio position.', 'personio-integration-light');
	}

	/**
	 * Return the rendered widget.
	 *
	 * @param array<string,mixed> $attributes Attributes to configure the rendering.
	 *
	 * @return string
	 */
	public function render( array $attributes ): string {
		$position = $this->get_position_by_request();
		if ( ! ( $position instanceof Position ) || ! $position->is_valid() ) {
			return '';
		}

		// collect the details in this array.
		$details       = array();
		$taxonomy_data = array();

		// get the configured separator.
		$separator = get_option( 'personioIntegrationTemplateExcerptSeparator' ) . ' ';
		if ( ! empty( $attributes['separator'] ) ) {
			$separator = $attributes['separator'];
		}

		// get colon setting.
		$colon = ':';
		if ( isset( $attributes['colon'] ) && '' === $attributes['colon'] ) {
			$colon = '';
		}

		// get line break from setting.
		$line_break = '<br>';
		if ( isset( $attributes['line_break'] ) && '' === $attributes['line_break'] ) {
			$line_break = ' ';
		}

		// get the excerpts for this position.
		if ( ! empty( $attributes['excerpt'] ) ) {

			// loop through each configured detail taxonomy.
			foreach ( $attributes['excerpt'] as $taxonomy_slug ) {
				// get taxonomy name by given slug (e.g. office => personioOffice).
				$taxonomy_name = Taxonomies::get_instance()->get_taxonomy_name_by_slug( $taxonomy_slug );

				// bail if taxonomy for the given slug could not be found.
				if ( ! $taxonomy_name ) {
					continue;
				}

				// get the taxonomy plural label.
				$taxonomy_label = Taxonomies::get_instance()->get_taxonomy_label( $taxonomy_name, $attributes['lang'] )['name'];

				// get the default terms for the terms of this taxonomy.
				$terms_label = Taxonomies::get_instance()->get_default_terms_for_taxonomy( $taxonomy_name, $attributes['lang'] );

				// get terms this position is using on this taxonomy.
				$terms = get_the_terms( $position->get_id(), $taxonomy_name );

				// bail on error.
				if ( is_wp_error( $terms ) ) {
					return '';
				}

				$false = false;
				/**
				 * Filter whether to show terms of single taxonomy as list or not.
				 *
				 * TODO remove this?
				 *
				 * @since 3.0.8 Available since 3.0.8.
				 * @param bool $false True to show the list.
				 * @param array<WP_Term>|false $terms List of terms.
				 * @noinspection PhpConditionAlreadyCheckedInspection
				 */
				$show_term_list = apply_filters( 'personio_integration_show_term_list', $false, $terms );

				// if term exist, get the corresponding term-label.
				if ( ! empty( $terms ) ) {
					$values = '';
					foreach ( $terms as $term ) {
						// set the name to use.
						$name = $term->name;

						// if terms slug is in list of default term labels, use this.
						if ( ! empty( $terms_label[ $term->slug ] ) ) {
							$name = $terms_label[ $term->slug ];
						}

						// for terms without default term labels, we add them to the list.
						if ( ! empty( $values ) ) {
							$values .= $separator;
						}

						// add terms name to the list.
						$values .= $term->name;
					}

					// set collected values as detail content.
					if ( ! empty( $values ) ) {
						$details[ $taxonomy_label ] = $values;
					}
				}

				// add the taxonomy itself to the list.
				$taxonomy_data[ $taxonomy_label ] = get_taxonomy( $taxonomy_name );
			}
		}

		if ( ! empty( $details ) ) {
			// get configured template if none has been set for this output.
			if ( empty( $attributes['excerpt_template'] ) ) {
				$template = get_option( is_singular() ? 'personioIntegrationTemplateDetailsExcerptsTemplate' : 'personioIntegrationTemplateListingExcerptsTemplate' );
			} else {
				$template = $attributes['excerpt_template'];
			}

			// generate styling.
			if( ! empty( $attributes['styles'] ) ) {
				Helper::add_inline_style( $attributes['styles'] );
			}

			// get template and return it.
			ob_start();
			include Templates::get_instance()->get_template( 'parts/details/' . $template . '.php' );
			$content = ob_get_clean();
			if ( ! $content ) {
				return '';
			}
			return $content;
		}

		// return nothing.
		return '';
	}

	/**
	 * Return the list of params this widget requires.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function get_params(): array {
		// get the possible field values.
		$values = array();
		foreach ( PersonioPosition::get_instance()->get_details_templates_via_rest_api() as $template ) {
			$values[] = $template['value'];
		}

		// generate the list.
		$list = ' <code data-copied-label="' . esc_attr__( 'copied', 'wp-personio-integration' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'wp-personio-integration' ) . '">' . implode( '</code>, <code>', $values ) . '</code>';

		// get the taxonomies.
		$taxonomies = array();
		foreach( Taxonomies::get_instance()->get_taxonomies() as $settings ) {
			// bail if it is not used for filter.
			if( empty( $settings['useInFilter'] ) ) {
				continue;
			}

			// add to the list.
			$taxonomies[] = $settings['slug'];
		}

		// generate the detail-list.
		$detail_list = ' <code data-copied-label="' . esc_attr__( 'copied', 'personio-integration-light' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'personio-integration-light' ) . '">' . implode( '</code>, <code>', $taxonomies ) . '</code>';

		// return the list of params for this widget.
		return array(
			'template' => array(
				'label'         => __( 'Name of chosen template, one of these values:', 'personio-integration-light' ) . $list,
				'example_value' => $values[0],
				'required'      => false,
			),
			'excerptTemplates' => array(
				'label'         => __( 'List of details to show, any of these values, comma-separated as list:', 'personio-integration-light' ) . $detail_list,
				'example_value' => $taxonomies[0],
				'required'      => false,
			),
			'colon' => array(
				'label'         => __( 'With colon', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'wrap' => array(
				'label'         => __( 'With line break', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'separator' => array(
				'label'         => __( 'Separator', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
		);
	}
}

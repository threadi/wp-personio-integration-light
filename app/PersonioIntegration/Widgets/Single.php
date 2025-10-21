<?php
/**
 * File to handle the single widget.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Widgets;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use PersonioIntegrationLight\PersonioIntegration\Widget_Base;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Templates;

/**
 * Object to handle the description widget.
 */
class Single extends Widget_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'widget_single';

	/**
	 * Name if the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationWidgetSingleStatus';

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
	protected string $gutenberg = '\PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Single';

	/**
	 * Instance of this object.
	 *
	 * @var ?Single
	 */
	private static ?Single $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Single {
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
		return __( 'Single', 'personio-integration-light' );
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
		return __( 'Provides a widget to show a single Personio position.', 'personio-integration-light' );
	}

	/**
	 * Return the rendered widget.
	 *
	 * @param array<string,mixed> $attributes Attributes to configure the rendering.
	 *
	 * @return string
	 */
	public function render( array $attributes ): string {
		// convert single shortcode attributes.
		$personio_attributes = $this->get_single_shortcode_attributes( $attributes );

		// check if Personio ID is given and a string.
		if ( ! is_string( $personio_attributes['personioid'] ) ) {
			$personio_attributes['personioid'] = '';
		}

		// do not output anything without ID.
		if ( empty( $personio_attributes['personioid'] ) ) {
			if ( 1 === absint( get_option( 'personioIntegration_debug' ) ) ) {
				$message    = __( 'Single-view called without the PersonioId for a position.', 'personio-integration-light' );
				$wrapper_id = 'position-no-id';
				$type       = '';
				ob_start();
				include_once Templates::get_instance()->get_template( 'parts/properties-hint.php' );
				$content = ob_get_clean();
				if ( ! $content ) {
					return '';
				}
				return $content;
			}
			return '';
		}

		// get the position by its Personio ID.
		$position = Positions::get_instance()->get_position_by_personio_id( $personio_attributes['personioid'] );

		// do not show this position if it is not valid or could not be loaded.
		if ( ! $position || ! $position->is_valid() ) {
			if ( 1 === absint( get_option( 'personioIntegration_debug' ) ) ) {
				$message    = __( 'Given Id is not a valid position-Id.', 'personio-integration-light' );
				$wrapper_id = 'position' . $personio_attributes['personioid'];
				$type       = '';
				ob_start();
				include_once Templates::get_instance()->get_template( 'parts/properties-hint.php' );
				$content = ob_get_clean();
				if ( ! $content ) {
					return '';
				}
				return $content;
			}
			return '';
		}

		// get the attributes defaults.
		$default_attributes = $this->get_single_shortcode_attributes_defaults();

		/**
		 * Change settings for output.
		 *
		 * @since 2.0.0 Available since 2.0.0.
		 *
		 * @param array $personio_attributes The attributes used for this output.
		 * @param array $default_attributes The default attributes.
		 */
		$personio_attributes = apply_filters( 'personio_integration_get_template', $personio_attributes, $default_attributes );

		// set language.
		$position->set_lang( $personio_attributes['lang'] );
		$position->set_title( '' );

		// collect the output.
		ob_start();

		/**
		 * Run custom actions before the output of the archive listing.
		 *
		 * @since 3.2.0 Available since 3.2.0.
		 * @param array $personio_attributes List of attributes.
		 */
		do_action( 'personio_integration_get_template_before', $personio_attributes );

		// embed content.
		include Templates::get_instance()->get_template( 'parts/content.php' );

		// return resulting code.
		$content = ob_get_clean();
		if ( ! $content ) {
			return '';
		}
		return $content;
	}

	/**
	 * Convert attributes for shortcodes.
	 *
	 * @param array<string,mixed> $attributes List of attributes.
	 *
	 * @return array<string,string|array<int,mixed>>
	 */
	public function get_single_shortcode_attributes( array $attributes ): array {
		// define the default values for each attribute.
		$attribute_defaults = $this->get_single_shortcode_attributes_defaults();

		// define the settings for each attribute (array or string).
		$attribute_settings = array(
			'personioid'              => 'string',
			'lang'                    => 'string',
			'templates'               => 'array',
			'excerpt'                 => 'array',
			'donotlink'               => 'bool',
			'styles'                  => 'string',
			'classes'                 => 'string',
			'excerpt_template'        => 'excerpt_template',
			'jobdescription_template' => 'jobdescription_template',
		);
		return Helper::get_shortcode_attributes( $attribute_defaults, $attribute_settings, $attributes );
	}

	/**
	 * Return attribute defaults for shortcode in single-view.
	 *
	 * @return array<string,mixed>
	 */
	private function get_single_shortcode_attributes_defaults(): array {
		$default_values = array(
			'personioid'              => '',
			'lang'                    => Languages::get_instance()->get_main_language(),
			'template'                => '',
			'templates'               => implode( ',', get_option( 'personioIntegrationTemplateContentDefaults' ) ),
			'excerpt_template'        => get_option( 'personioIntegrationTemplateDetailsExcerptsTemplate' ),
			'jobdescription_template' => get_option( 'personioIntegrationTemplateJobDescription' ),
			'excerpt'                 => implode( ',', get_option( 'personioIntegrationTemplateExcerptDetail' ) ),
			'donotlink'               => 1,
			'styles'                  => '',
			'classes'                 => '',
		);

		/**
		 * Filter the attribute-defaults.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array<string,mixed> $default_values The list of default values for each attribute used to display positions in frontend.
		 */
		return apply_filters( 'personio_integration_position_attribute_defaults', $default_values );
	}

	/**
	 * Return the list of params this widget requires.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function get_params(): array {
		// get the possible field values.
		$values = array();
		foreach ( Positions::get_instance()->get_positions() as $position_obj ) {
			$values[] = $position_obj->get_personio_id();
		}

		// generate the list.
		$list = ' <code data-copied-label="' . esc_attr__( 'copied', 'personio-integration-light' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'personio-integration-light' ) . '">' . implode( '</code>, <code>', $values ) . '</code>';

		// get the possible field values.
		$excerpts = array();
		foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy ) {
			$excerpts[] = $taxonomy['slug'];
		}

		// generate the list.
		$excerpts_list = ' <code data-copied-label="' . esc_attr__( 'copied', 'personio-integration-light' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'personio-integration-light' ) . '">' . implode( '</code>, <code>', $excerpts ) . '</code>';

		// return the list of params for this widget.
		return array(
			'id'                  => array(
				'label'         => __( 'Personio ID of the position to show', 'personio-integration-light' ) . $list,
				'example_value' => $values[0],
				'required'      => false,
			),
			'showTitle'           => array(
				'label'         => __( 'Show title', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'linkTitle'           => array(
				'label'         => __( 'Link title', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'showExcerpt'         => array(
				'label'         => __( 'Show excerpt', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'excerptTemplates'    => array(
				'label'         => __( 'Choose details', 'personio-integration-light' ) . $excerpts_list,
				'example_value' => $excerpts[0],
				'required'      => false,
			),
			'showContent'         => array(
				'label'         => __( 'Show content', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'showApplicationForm' => array(
				'label'         => __( 'Show option to apply', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
		);
	}
}

<?php
/**
 * File to handle the application button widget.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Widgets;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Widget_Base;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Templates;

/**
 * Object to handle the application button widget.
 */
class Application_Button extends Widget_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'widget_application_button';

	/**
	 * Name if the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationWidgetApplicationButtonStatus';

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
	protected string $gutenberg = '\PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Application_Button';

	/**
	 * Instance of this object.
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
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Application Button', 'personio-integration-light' );
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
		return __( 'Provides a widget to show the application button for single position.', 'personio-integration-light' );
	}

	/**
	 * Return the rendered widget.
	 *
	 * @param array<string,mixed> $attributes Attributes to configure the rendering.
	 *
	 * @return string
	 */
	public function render( array $attributes ): string {
		$false = false;
		/**
		 * Bail if no button should be visible.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param bool $false Return true to prevent button-output.
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_hide_button', $false ) ) {
			return '';
		}

		// get the requested position.
		$position = $this->get_position_by_request();
		if ( ! $position instanceof Position || ! $position->is_valid() ) {
			return '';
		}

		// get the Personio ID for attributes.
		$attributes['personioid'] = absint( $position->get_personio_id() );

		// set actual language.
		$position->set_lang( Languages::get_instance()->get_current_lang() );

		// add styles if not set.
		if ( ! isset( $attributes['styles'] ) ) {
			$attributes['styles'] = '';
		}

		// define where this application-link is displayed.
		$text_position = 'archive';
		if ( is_single() ) {
			$text_position = 'single';
		}

		// set back to list-link.
		$back_to_list_url = get_option( 'personioIntegrationTemplateBackToListUrl', '' );
		if ( empty( $back_to_list_url ) ) {
			$back_to_list_url = PersonioPosition::get_instance()->get_archive_url();
		}

		// reset back to list-link.
		if ( 'archive' === $text_position || ( isset( $attributes['show_back_to_list'] ) && empty( $attributes['show_back_to_list'] ) ) ) {
			$back_to_list_url = '';
		}

		// set classes, if not set.
		if ( ! isset( $attributes['classes'] ) ) {
			$attributes['classes'] = '';
		}

		// generate styling.
		Helper::add_inline_style( $attributes['styles'] );

		// get application URL.
		$link = $position->get_application_url();

		/**
		 * Filter the application URL.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param string $link The URL.
		 * @param Position $position The position as object.
		 * @param array<string,mixed> $attributes List of attributes used for the output.
		 */
		$link = apply_filters( 'personio_integration_light_position_application_link', $link, $position, $attributes );

		$target = '_blank';
		/**
		 * Set and filter the value for the target-attribute.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $target The target value.
		 * @param Position $position The position as object.
		 * @param array<string,mixed> $attributes List of attributes used for the output.
		 */
		$target = apply_filters( 'personio_integration_back_to_list_target_attribute', $target, $position, $attributes );

		// get and output template.
		ob_start();
		include Templates::get_instance()->get_template( 'parts/properties-application-button.php' );
		$content = ob_get_clean();
		if ( ! $content ) {
			return '';
		}

		/**
		 * Filter the output of the application button.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param string $content The content to output.
		 * @param array<string,mixed> $attributes List of used attributes.
		 * @param Position $position The position object.
		 */
		return apply_filters( 'personio_integration_light_application_button_output', $content, $attributes, $position );
	}
}

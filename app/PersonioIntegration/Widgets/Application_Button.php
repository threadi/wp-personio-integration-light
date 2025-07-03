<?php
/**
 * File to handle the application button widget.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Widgets;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Widget_Base;

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
		return __('Provides a widget to show filter as link-based dropdown-list for Personio positions.', 'personio-integration-light');
	}

	/**
	 * Return the rendered widget.
	 *
	 * @param array<string,mixed> $attributes Attributes to configure the rendering.
	 *
	 * @return string
	 */
	public function render( array $attributes ): string {
		return Archive::get_instance()->render( $attributes );
	}
}

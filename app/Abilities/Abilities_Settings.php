<?php
/**
 * File for the settings and hints about the abilities of this plugin.
 *
 * @package personio-integration-light
 */

declare(strict_types=1);

namespace PersonioIntegrationLight\Abilities;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use easySettingsForWordPress\Fields\Checkbox;
use easySettingsForWordPress\Page;
use easySettingsForWordPress\Tab;
use PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transients;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Admin\Callback_TextInfo;
use PersonioIntegrationLight\Plugin\Settings;
use Throwable;
use WP_Error;
use WP_Post;

/**
 * Show the abilities of this plugin in the settings.
 *
 * The abilities are made for applications, which the user connects to WordPress himself (e.g. an AI assistant via
 * an MCP plugin). Nothing is sent anywhere by this plugin, so the texts describe the feature neutrally, and it can
 * be disabled completely.
 */
class Abilities_Settings {
	/**
	 * The option to enable or disable the abilities.
	 */
	public const OPTION = 'personioIntegrationAbilities';

	/**
	 * The name of the tab for advanced settings.
	 */
	public const TAB = 'personio_integration_advanced';

	/**
	 * The name of the sub tab for the abilities.
	 */
	public const SUBTAB = 'personio_integration_abilities';

	/**
	 * The meta key, which marks templates saved via the template abilities.
	 *
	 * The value is "ai_{builder}_{type}_template", e.g. "ai_elementor_single_template".
	 */
	public const MARKER_META = 'personio_integration_ability_template';

	/**
	 * Instance of this object.
	 *
	 * @var ?Abilities_Settings
	 */
	private static ?Abilities_Settings $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return instance of this object as singleton.
	 *
	 * @return Abilities_Settings
	 */
	public static function get_instance(): Abilities_Settings {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'add_settings' ), 30 );
		add_action( 'admin_action_personio_integration_reset_ability_template', array( $this, 'reset_template_by_request' ) );
		add_filter( 'display_post_states', array( $this, 'add_post_state' ), 10, 2 );
	}

	/**
	 * Return whether the abilities of this plugin are enabled.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return 1 === absint( get_option( self::OPTION, 1 ) );
	}

	/**
	 * Return whether WordPress supports abilities (since WordPress 6.9).
	 *
	 * @return bool
	 */
	public function is_api_available(): bool {
		return function_exists( 'wp_register_ability' );
	}

	/**
	 * Return whether the MCP Adapter, which connects applications with the abilities, is active.
	 *
	 * @return bool
	 */
	public function is_mcp_adapter_active(): bool {
		return class_exists( '\WP\MCP\Core\McpAdapter' );
	}

	/**
	 * Return the URL of the settings for the abilities.
	 *
	 * @return string
	 */
	public function get_url(): string {
		return Helper::get_settings_url( 'personioPositions', self::TAB, self::SUBTAB );
	}

	/**
	 * Return the marker for a template saved via abilities.
	 *
	 * @param string $builder The internal name of the page builder.
	 * @param string $type    The template type.
	 *
	 * @return string
	 */
	public function get_marker( string $builder, string $type ): string {
		return 'ai_' . sanitize_key( $builder ) . '_' . sanitize_key( $type ) . '_template';
	}

	/**
	 * Add the settings for the abilities as sub tab of the advanced settings.
	 *
	 * @return void
	 */
	public function add_settings(): void {
		// get the settings object and page.
		$settings_obj  = Settings::get_instance()->get_settings_object();
		$settings_page = $settings_obj->get_page( 'personioPositions' );

		// bail if the page could not be found.
		if ( ! $settings_page instanceof Page ) {
			return;
		}

		// get the tab for advanced settings.
		$advanced_tab = $settings_page->get_tab( self::TAB );

		// bail if the tab could not be found.
		if ( ! $advanced_tab instanceof Tab ) {
			return;
		}

		// add the sub tab.
		$tab = $advanced_tab->add_tab( self::SUBTAB, 15 );
		$tab->set_title( __( 'Abilities', 'personio-integration-light' ) );

		// add a section.
		$section = $tab->add_section( 'personio_integration_abilities_section', 10 );
		$section->set_title( __( 'Abilities for connected applications', 'personio-integration-light' ) );
		$section->set_setting( $settings_obj );

		// add the setting to enable or disable the abilities.
		$setting = $settings_obj->add_setting( self::OPTION );
		$setting->set_section( $section );
		$setting->set_type( 'integer' );
		$setting->set_default( 1 );
		$field = new Checkbox( $settings_obj );
		$field->set_title( __( 'Provide abilities', 'personio-integration-light' ) );
		$field->set_description( __( 'Provides the abilities of this plugin via the Abilities API of WordPress. Applications, which you connect to your WordPress yourself (e.g. an AI assistant via an MCP plugin), can then read your positions and create templates for the detail view and the list of positions. The positions themselves can not be changed this way, as Personio is the leading system for them. This plugin sends no data to such applications on its own. Disable this option if you do not want to use it.', 'personio-integration-light' ) );
		$setting->set_field( $field );

		// add the status.
		$setting = $settings_obj->add_setting( 'personioIntegrationAbilitiesStatus' );
		$setting->set_section( $section );
		$setting->prevent_export( true );
		$field = new Callback_TextInfo( $settings_obj );
		$field->set_title( __( 'Status', 'personio-integration-light' ) );
		$field->set_callback( array( $this, 'get_status_html' ) );
		$setting->set_field( $field );

		// add the list of abilities.
		$setting = $settings_obj->add_setting( 'personioIntegrationAbilitiesList' );
		$setting->set_section( $section );
		$setting->prevent_export( true );
		$field = new Callback_TextInfo( $settings_obj );
		$field->set_title( __( 'Available abilities', 'personio-integration-light' ) );
		$field->set_callback( array( $this, 'get_abilities_html' ) );
		$setting->set_field( $field );

		// add the templates of the page builders.
		$setting = $settings_obj->add_setting( 'personioIntegrationAbilitiesTemplates' );
		$setting->set_section( $section );
		$setting->prevent_export( true );
		$field = new Callback_TextInfo( $settings_obj );
		$field->set_title( __( 'Templates for your page builders', 'personio-integration-light' ) );
		$field->set_callback( array( $this, 'get_templates_html' ) );
		$setting->set_field( $field );

		// add the guide.
		$setting = $settings_obj->add_setting( 'personioIntegrationAbilitiesGuide' );
		$setting->set_section( $section );
		$setting->prevent_export( true );
		$field = new Callback_TextInfo( $settings_obj );
		$field->set_title( __( 'Getting started', 'personio-integration-light' ) );
		$field->set_callback( array( $this, 'get_guide_html' ) );
		$setting->set_field( $field );
	}

	/**
	 * Return a line of the status with an icon.
	 *
	 * @param bool   $ok   Whether the requirement is fulfilled.
	 * @param string $text The text (HTML).
	 *
	 * @return string
	 */
	private function get_status_line( bool $ok, string $text ): string {
		return '<li><span class="dashicons dashicons-' . ( $ok ? 'yes' : 'no' ) . '"></span> ' . $text . '</li>';
	}

	/**
	 * Return the status of the abilities as HTML.
	 *
	 * @return string
	 */
	public function get_status_html(): string {
		// bail if the abilities are disabled.
		if ( ! $this->is_enabled() ) {
			return '<p>' . esc_html__( 'The abilities are disabled.', 'personio-integration-light' ) . '</p>';
		}

		$lines = array();

		// the Abilities API.
		if ( $this->is_api_available() ) {
			$lines[] = $this->get_status_line( true, esc_html__( 'Your WordPress supports abilities.', 'personio-integration-light' ) );
		} else {
			$lines[] = $this->get_status_line( false, esc_html__( 'Your WordPress does not support abilities yet. They are available since WordPress 6.9.', 'personio-integration-light' ) );
		}

		// the MCP Adapter (optional, other MCP plugins with support for abilities work as well).
		if ( $this->is_mcp_adapter_active() ) {
			/* translators: %1$s will be replaced by a URL. */
			$lines[] = $this->get_status_line( true, sprintf( __( 'The MCP Adapter is active. Its default endpoint for connected applications is %1$s', 'personio-integration-light' ), '<code>' . esc_html( rest_url( 'mcp/mcp-adapter-default-server' ) ) . '</code>' ) );
		} else {
			$lines[] = '<li><span class="dashicons dashicons-info"></span> ' . esc_html__( 'To connect applications like AI assistants, you need an MCP plugin with support for the abilities of WordPress.', 'personio-integration-light' ) . '</li>';
		}

		// return the list.
		return '<ul>' . implode( '', $lines ) . '</ul>';
	}

	/**
	 * Return the list of our abilities as HTML.
	 *
	 * @return string
	 */
	public function get_abilities_html(): string {
		$abilities = array(
			'get-positions'        => __( 'Read the list of positions.', 'personio-integration-light' ),
			'get-position'         => __( 'Read a single position with its job description.', 'personio-integration-light' ),
			'get-taxonomies'       => __( 'Read the taxonomies, terms and languages of the positions.', 'personio-integration-light' ),
			'get-import-status'    => __( 'Read the state of the import from Personio.', 'personio-integration-light' ),
			'get-builders'         => __( 'Read the page builders, whose templates can be used.', 'personio-integration-light' ),
			'get-template-catalog' => __( 'Read the elements of this plugin for templates.', 'personio-integration-light' ),
			'get-template'         => __( 'Read the template for the detail view or the list of positions.', 'personio-integration-light' ),
			'preview-template'     => __( 'Check and preview a template without saving it.', 'personio-integration-light' ),
			'save-template'        => __( 'Save a template for the detail view or the list of positions.', 'personio-integration-light' ),
			'reset-template'       => __( 'Remove a template saved via abilities, so the previous template is used again.', 'personio-integration-light' ),
		);

		$html = '<ul>';
		foreach ( $abilities as $name => $description ) {
			$html .= '<li><code>' . esc_html( Abilities::ABILITY_CATEGORY . '/' . $name ) . '</code> - ' . esc_html( $description ) . '</li>';
		}
		$html .= '</ul>';
		$html .= '<p>' . esc_html__( 'The positions themselves can only be read, as they are managed in Personio.', 'personio-integration-light' ) . '</p>';
		return $html;
	}

	/**
	 * Return the templates of the page builders with their state as HTML.
	 *
	 * @return string
	 */
	public function get_templates_html(): string {
		// bail if the abilities can not be used.
		if ( ! $this->is_enabled() || ! $this->is_api_available() ) {
			return '<p>' . esc_html__( 'Available as soon as the abilities are provided.', 'personio-integration-light' ) . '</p>';
		}

		// get the page builders.
		$adapters = Template_Abilities::get_instance()->get_adapters();

		// bail if no page builder is supported.
		if ( empty( $adapters ) ) {
			return '<p>' . esc_html__( 'No supported page builder found.', 'personio-integration-light' ) . '</p>';
		}

		// create a table with a row per page builder.
		$html = '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Page builder', 'personio-integration-light' ) . '</th>';
		foreach ( reset( $adapters )->get_template_types() as $label ) {
			$html .= '<th>' . esc_html( $label ) . '</th>';
		}
		$html .= '</tr></thead><tbody>';
		foreach ( $adapters as $adapter ) {
			$html .= '<tr><td>' . esc_html( $adapter->get_label() ) . '</td>';

			// show the reason, if the page builder is not available.
			if ( ! $adapter->is_available() ) {
				$html .= '<td colspan="' . count( $adapter->get_template_types() ) . '">' . esc_html( $adapter->get_unavailable_reason() ) . '</td></tr>';
				continue;
			}

			// show the state of each template.
			foreach ( array_keys( $adapter->get_template_types() ) as $type ) {
				$html .= '<td>' . $this->get_template_state_html( $adapter, $type ) . '</td>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody></table>';

		// return the resulting HTML.
		return $html;
	}

	/**
	 * Return the state of a template as HTML, with a button to reset it, if it was saved via abilities.
	 *
	 * @param Template_Adapter_Base $adapter The adapter of the page builder.
	 * @param string                $type    The template type.
	 *
	 * @return string
	 */
	private function get_template_state_html( Template_Adapter_Base $adapter, string $type ): string {
		// get the template, errors of a page builder must not break the settings.
		try {
			$template = $adapter->get_template( $type );
		} catch ( Throwable $e ) {
			$template = new WP_Error( 'personio_integration_template_error', $e->getMessage() );
		}
		if ( $template instanceof WP_Error ) {
			return esc_html( $template->get_error_message() );
		}

		// the template is not saved via abilities.
		if ( empty( $template['is_customized'] ) ) {
			$id     = isset( $template['id'] ) && is_scalar( $template['id'] ) ? (string) $template['id'] : '';
			$source = isset( $template['source'] ) && is_string( $template['source'] ) ? $template['source'] : '';
			if ( '' === $id || 'none' === $source ) {
				return esc_html__( 'Classic template of this plugin', 'personio-integration-light' );
			}
			if ( 'plugin' === $source ) {
				return esc_html__( 'Template of this plugin', 'personio-integration-light' );
			}
			if ( 'theme' === $source ) {
				return esc_html__( 'Template of the theme', 'personio-integration-light' );
			}
			if ( ctype_digit( $id ) ) {
				/* translators: %1$s will be replaced by an ID. */
				return esc_html( sprintf( __( 'Template of the page builder (ID %1$s)', 'personio-integration-light' ), $id ) );
			}
			return esc_html__( 'Template of the page builder', 'personio-integration-light' );
		}

		// the template is saved via abilities: offer to reset it.
		$html = esc_html__( 'Created via abilities', 'personio-integration-light' );
		if ( $adapter->can_save() ) {
			$url    = add_query_arg(
				array(
					'action'  => 'personio_integration_reset_ability_template',
					'builder' => $adapter->get_name(),
					'type'    => $type,
					'nonce'   => wp_create_nonce( 'personio-integration-reset-ability-template' ),
				),
				get_admin_url() . 'admin.php'
			);
			$dialog = array(
				'title'   => __( 'Reset template', 'personio-integration-light' ),
				'texts'   => array(
					'<p><strong>' . __( 'Do you really want to reset this template?', 'personio-integration-light' ) . '</strong></p>',
					'<p>' . __( 'The template will be moved to the trash. The template, which was used before, will be used again.', 'personio-integration-light' ) . '</p>',
				),
				'buttons' => array(
					array(
						'action'  => 'location.href="' . $url . '";',
						'variant' => 'primary',
						'text'    => __( 'Yes, reset it', 'personio-integration-light' ),
					),
					array(
						'action'  => 'closeDialog();',
						'variant' => 'secondary',
						'text'    => __( 'Cancel', 'personio-integration-light' ),
					),
				),
			);
			$html  .= '<br><a href="' . esc_url( $url ) . '" class="button easy-dialog-for-wordpress" data-dialog="' . esc_attr( Helper::get_json( $dialog ) ) . '">' . esc_html__( 'Reset', 'personio-integration-light' ) . '</a>';
		}
		return $html;
	}

	/**
	 * Return the guide for connecting an application as HTML.
	 *
	 * @return string
	 */
	public function get_guide_html(): string {
		$html = '<ol>';
		/* translators: %1$s will be replaced by a URL. */
		$html .= '<li>' . sprintf( __( 'Install and activate an MCP plugin of your choice, which supports the abilities of WordPress (e.g. the <a href="%1$s" target="_blank">MCP Adapter</a>).', 'personio-integration-light' ), esc_url( 'https://github.com/WordPress/mcp-adapter' ) ) . '</li>';
		$html .= '<li>' . esc_html__( 'Make sure the abilities of this plugin are available in your MCP plugin. Depending on the plugin, you may have to enable them in its settings.', 'personio-integration-light' ) . '</li>';
		/* translators: %1$s will be replaced by a URL. */
		$html .= '<li>' . sprintf( __( 'Set up the access for your application as described by your MCP plugin, e.g. with an <a href="%1$s">application password</a> for your user.', 'personio-integration-light' ), esc_url( admin_url( 'profile.php#application-passwords-section' ) ) ) . '</li>';
		$html .= '<li>' . esc_html__( 'Connect your application (e.g. an AI assistant with MCP support) with the endpoint of your MCP plugin and the access data.', 'personio-integration-light' ) . '</li>';
		$html .= '</ol>';

		// add examples for prompts.
		$html .= '<p><strong>' . esc_html__( 'Examples for requests to your application:', 'personio-integration-light' ) . '</strong></p>';
		$html .= '<ul>';
		$html .= '<li><em>' . esc_html__( 'Which positions are open in Berlin right now?', 'personio-integration-light' ) . '</em></li>';
		$html .= '<li><em>' . esc_html__( 'Create a template for the detail view of my positions with the details on the left and the application button on the right.', 'personio-integration-light' ) . '</em></li>';
		$html .= '<li><em>' . esc_html__( 'Change the list of positions so it shows a filter for the office above the positions.', 'personio-integration-light' ) . '</em></li>';
		$html .= '</ul>';

		// add a note about the permissions and the check before saving.
		$html .= '<p>' . esc_html__( 'The application acts with the permissions of the user whose access data it uses. Templates are only saved after a check, and the application is asked to show you the result before saving it. Templates saved this way can be reset here at any time.', 'personio-integration-light' ) . '</p>';
		return $html;
	}

	/**
	 * Reset a template, which was saved via abilities, by request from the settings.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function reset_template_by_request(): void {
		// check nonce.
		check_admin_referer( 'personio-integration-reset-ability-template', 'nonce' );

		// get the target URL.
		$referer = wp_get_referer();
		$url     = is_string( $referer ) ? $referer : $this->get_url();

		// bail if the user is not allowed to change templates.
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_safe_redirect( $url );
			exit;
		}

		// get the page builder and the template type and reset the template.
		$builder = isset( $_GET['builder'] ) ? sanitize_key( wp_unslash( $_GET['builder'] ) ) : '';
		$type    = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : '';
		$result  = $this->reset_template( $builder, $type );

		// show the result.
		$transient_obj = Transients::get_instance()->add();
		$transient_obj->set_name( 'personio_integration_abilities_reset' );
		if ( $result instanceof WP_Error ) {
			$transient_obj->set_message( __( 'The template could not be reset:', 'personio-integration-light' ) . ' ' . esc_html( $result->get_error_message() ) );
			$transient_obj->set_type( 'error' );
		} elseif ( 'none' === ( $result['action'] ?? '' ) ) {
			$transient_obj->set_message( __( 'There was no template to reset.', 'personio-integration-light' ) );
			$transient_obj->set_type( 'success' );
		} else {
			$transient_obj->set_message( __( 'The template has been reset. The template, which was used before, is used again.', 'personio-integration-light' ) );
			$transient_obj->set_type( 'success' );
		}
		$transient_obj->save();

		// forward the user.
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Reset the template of the given type, which was saved via abilities, for the given page builder.
	 *
	 * @param string $builder The internal name of the page builder.
	 * @param string $type    The template type.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function reset_template( string $builder, string $type ): array|WP_Error {
		// get the page builder.
		$adapter = '' !== $builder ? Template_Abilities::get_instance()->get_adapter( $builder ) : new WP_Error( 'personio_integration_missing_builder', __( 'No page builder given.', 'personio-integration-light' ) );
		if ( $adapter instanceof WP_Error ) {
			return $adapter;
		}

		// check the template type.
		if ( ! array_key_exists( $type, $adapter->get_template_types() ) ) {
			return new WP_Error( 'personio_integration_unknown_type', __( 'The template type is unknown.', 'personio-integration-light' ) );
		}

		// reset the template, errors of a page builder must not break the request.
		try {
			return $adapter->reset_template( $type, false );
		} catch ( Throwable $e ) {
			return new WP_Error( 'personio_integration_reset_failed', $e->getMessage() );
		}
	}

	/**
	 * Mark templates saved via abilities in the lists of templates of the page builders.
	 *
	 * @param mixed $states The states of the post.
	 * @param mixed $post   The post.
	 *
	 * @return mixed
	 */
	public function add_post_state( mixed $states, mixed $post ): mixed {
		// bail without usable data.
		if ( ! is_array( $states ) || ! $post instanceof WP_Post ) {
			return $states;
		}

		// mark the templates with our marker.
		$marker = get_post_meta( $post->ID, self::MARKER_META, true );
		if ( is_string( $marker ) && preg_match( '/^ai_[a-z0-9_]*(single|archive)_template$/', $marker ) ) {
			$states['personio_integration_abilities'] = __( 'Created via abilities', 'personio-integration-light' );
		}
		return $states;
	}
}

<?php
/**
 * File to handle plugin-settings.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Export;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Checkbox;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Checkboxes;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\MultiSelect;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Number;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Radio;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Select;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Text;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Import;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Page;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Section;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Tab;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Extensions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;

/**
 * Object tot handle settings.
 */
class Settings {
	/**
	 * Instance of this object.
	 *
	 * @var ?Settings
	 */
	private static ?Settings $instance = null;

	/**
	 * Constructor for Settings-Handler.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Settings {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the settings.
	 *
	 * @return void
	 */
	public function init(): void {
		// set all settings for this plugin.
		add_action( 'init', array( $this, 'add_settings' ) );

		// use our own hooks.
		add_filter( 'personio_integration_log_categories', array( $this, 'add_log_categories' ) );
		add_filter( 'personio_integration_light_help_tabs', array( $this, 'add_help' ), 30 );
		add_filter( 'personio_integration_light_settings_tab_title', array( $this, 'add_pro_on_title' ), 10, 2 );
	}

	/**
	 * Define ALL settings for this plugin.
	 *
	 * @return void
	 */
	public function add_settings(): void {
		// get taxonomies.
		$list_template_filter = array();
		$list_excerpt         = array();
		$detail_excerpt       = array();
		if ( Helper::is_personio_url_set() ) {
			$list_template_filter = Taxonomies::get_instance()->get_labels_for_settings( get_option( 'personioIntegrationTemplateFilter' ) );
			$list_excerpt         = Taxonomies::get_instance()->get_labels_for_settings( get_option( 'personioIntegrationTemplateExcerptDefaults' ) );
			$detail_excerpt       = Taxonomies::get_instance()->get_labels_for_settings( get_option( 'personioIntegrationTemplateExcerptDetail' ) );
		}

		// set title.
		$title = __( 'Personio Integration Light', 'personio-integration-light' );
		/**
		 * Filter for settings title.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $title The title.
		 */
		$title = apply_filters( 'personio_integration_settings_title', $title ) . ' ' . __( 'Settings', 'personio-integration-light' );

		/**
		 * Configure the basic settings object.
		 */
		$settings_obj  = \PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings::get_instance();
		$settings_page = $settings_obj->add_page( 'personioPositions' );
		$settings_obj->set_slug( 'personio_integration_light' );
		$settings_obj->set_plugin_slug( WP_PERSONIO_INTEGRATION_PLUGIN );
		$settings_obj->set_menu_title( __( 'Settings', 'personio-integration-light' ) );
		$settings_obj->set_title( $title );
		$settings_obj->set_menu_slug( 'personioPositions' );
		$settings_obj->set_menu_parent_slug( 'edit.php?post_type=' . PersonioPosition::get_instance()->get_name() );
		$settings_obj->set_menu_position( 1 );
		$settings_obj->set_url( Helper::get_plugin_url() . '/app/Dependencies/easySettingsForWordPress/' );
		$settings_obj->set_path( Helper::get_plugin_path() . '/app/Dependencies/easySettingsForWordPress/' );

		// initialize this settings object, if setup has been completed or if this is a REST API request.
		if ( Helper::is_rest_request() || Setup::get_instance()->is_completed() ) {
			$settings_obj->init();
		}

		/**
		 * Configure all tabs for this object.
		 */
		// the basic tab.
		$general_tab = $settings_page->add_tab( 'basic', 10 );
		$general_tab->set_title( __( 'Basic Settings', 'personio-integration-light' ) );
		$settings_page->set_default_tab( $general_tab );

		// the templates tab.
		$templates_tab = $settings_page->add_tab( 'templates', 20 );
		$templates_tab->set_title( __( 'Templates', 'personio-integration-light' ) );

		// the pro tab.
		$pro_tab = $settings_page->add_tab( 'use_pro', 30 );
		$pro_tab->set_title( __( 'Applications, SEO & more', 'personio-integration-light' ) );
		$pro_tab->set_not_linked( true );

		// add the extension tab.
		$extensions_tab = $settings_page->add_tab( 'extensions', 40 );
		$extensions_tab->set_title( __( 'Extensions', 'personio-integration-light' ) );
		$extensions_tab->set_hide_save( true );
		$extensions_tab->set_callback( array( $this, 'show_extensions_hint' ) );

		// the advanced tab.
		$advanced_tab = $settings_page->add_tab( 'advanced', 50 );
		$advanced_tab->set_title( __( 'Additional settings', 'personio-integration-light' ) );

		// the logs tab.
		$logs_tab = $settings_page->add_tab( 'logs', 60 );
		$logs_tab->set_title( __( 'Logs', 'personio-integration-light' ) );
		$logs_tab->set_hide_save( true );
		$logs_tab->set_callback( array( '\PersonioIntegrationLight\Plugin\Admin\Logs', 'show' ) );

		// the copyright tab.
		$copyright_tab = $settings_page->add_tab( 'copyright', 900 );
		$copyright_tab->set_title( '&nbsp;' );
		$copyright_tab->set_tab_class( 'copyright' );
		$copyright_tab->set_hide_save( true );
		$copyright_tab->set_callback( array( $this, 'show_copyright' ) );

		// the help tab.
		$help_tab = $settings_page->add_tab( 'help', 1000 );
		$help_tab->set_title( __( 'Questions? Check our forum!', 'personio-integration-light' ) );
		$help_tab->set_tab_class( 'nav-tab-help' );
		$help_tab->set_url( Helper::get_plugin_support_url() );
		$help_tab->set_url_target( '_blank' );

		/**
		 * Create sections for this settings object.
		 */
		// the main section.
		$general_tab_main = $general_tab->add_section( 'settings_section_main', 10 );
		$general_tab_main->set_title( __( 'General settings', 'personio-integration-light' ) );
		$general_tab_main->set_setting( $settings_obj );

		// the template list section.
		$template_list = $templates_tab->add_section( 'settings_section_template_list', 10 );
		$template_list->set_title( __( 'List View', 'personio-integration-light' ) );
		$template_list->set_setting( $settings_obj );

		// the template detail section.
		$template_detail = $templates_tab->add_section( 'settings_section_template_detail', 20 );
		$template_detail->set_title( __( 'Detail View', 'personio-integration-light' ) );
		$template_detail->set_setting( $settings_obj );

		// the template other section.
		$template_other = $templates_tab->add_section( 'settings_section_template_other', 30 );
		$template_other->set_title( __( 'Other settings', 'personio-integration-light' ) );
		$template_other->set_setting( $settings_obj );

		// the advanced section.
		$advanced = $advanced_tab->add_section( 'settings_section_advanced', 10 );
		$advanced->set_title( __( 'Additional settings', 'personio-integration-light' ) );
		$advanced->set_setting( $settings_obj );
		$advanced->set_callback( array( $this, 'show_advanced_hint' ) );

		// create a hidden page for hidden settings.
		$hidden_page = $settings_obj->add_page( 'hidden_page' );

		// create a hidden tab on this page.
		$hidden_tab = $hidden_page->add_tab( 'hidden_tab', 10 );

		// the hidden section for any not visible settings.
		$hidden = $hidden_tab->add_section( 'hidden_section', 20 );
		$hidden->set_setting( $settings_obj );

		/**
		 * Add the settings for the main settings.
		 */
		/* translators: %1$s will be replaced by a link to the Pro plugin page. */
		$pro_hint = __( 'Use multiple Personio accounts in one website with %1$s.', 'personio-integration-light' );
		$true     = true;

		// add setting.
		$personio_url_setting = $settings_obj->add_setting( 'personioIntegrationUrl' );
		$personio_url_setting->set_section( $general_tab_main );
		$personio_url_setting->set_show_in_rest( true );
		$personio_url_setting->set_type( 'string' );
		$personio_url_setting->set_default( '' );
		$personio_url_setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl', 'validate' ) );
		$field = new Text();
		$field->set_title( __( 'Personio URL', 'personio-integration-light' ) );
		/* translators: %1$s is replaced with the URL to Personio login for account access, %2$s is replaced with the url to the Personio support */
		$field->set_description( sprintf( __( 'You find this URL in your <a href="%1$s" target="_blank">Personio-account (opens new window)</a> under Settings > Recruiting > Career Page > Activations.<br><strong>Hint:</strong> You have to enable the XML-feed under Settings > Recruiting > Career in your Personio account.<br>If you have any questions about the URL provided by Personio, please contact the <a href="%2$s" target="_blank">Personio support (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_personio_login_url() ), esc_url( Helper::get_personio_support_url() ) ) . '</p>' . apply_filters( 'personio_integration_admin_show_pro_hint', $pro_hint, $true ) );
		$field->set_placeholder( Helper::get_personio_url_example() );
		$field->set_sanitize_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl', 'validate' ) );
		$personio_url_setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE );
		$setting->set_section( $general_tab_main );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'string' );
		$setting->set_default( Languages::get_instance()->get_current_lang() );
		$setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\MainLanguage', 'validate' ) );
		$field = new Radio();
		$field->set_title( __( 'Main language', 'personio-integration-light' ) );
		$field->set_description( __( 'Set the main language you will use for your open positions.', 'personio-integration-light' ) );
		$field->set_options( Languages::get_instance()->get_languages() );
		$field->set_sanitize_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\MainLanguage', 'validate' ) );
		$setting->set_field( $field );

		// add setting.
		/* translators: %1$s will be replaced by a link to the Pro plugin page. */
		$pro_hint = __( 'Use all languages supported by Personio with %1$s.', 'personio-integration-light' );
		$setting  = $settings_obj->add_setting( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION );
		$setting->set_section( $general_tab_main );
		$setting->set_type( 'array' );
		$setting->set_default( array( Languages::get_instance()->get_current_lang() => 1 ) );
		$setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\Languages', 'validate' ) );
		$field = new Checkboxes();
		$field->set_title( __( 'Used languages', 'personio-integration-light' ) );
		$field->set_description( __( 'Activate the languages in which your positions should be displayed on the website.', 'personio-integration-light' ) . apply_filters( 'personio_integration_admin_show_pro_hint', $pro_hint, $true ) );
		$field->set_options( Languages::get_instance()->get_languages() );
		$field->set_sanitize_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\Languages', 'validate' ) );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationLoginUrl' );
		$setting->set_section( $general_tab_main );
		$setting->set_type( 'string' );
		$setting->set_default( '' );
		$setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationLoginUrl', 'validate' ) );
		$field = new Text();
		$field->set_title( __( 'Personio Login URL', 'personio-integration-light' ) );
		/* translators: %1$s is replaced with the URL to the Personio support */
		$field->set_description( sprintf( __( 'This URL is used by Personio to give you a unique login URL to your Personio account. It will be communicated to you when you register with Personio.<br>Entering this URL activates links in the WordPress backend that allow you to quickly switch from WordPress to Personio to edit positions.<br>This is NOT the URL where your open positions are visible.<br>If you have any questions about this URL, please contact the <a href="%1$s" target="_blank">Personio support (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_personio_support_url() ) ) );
		$field->set_placeholder( Helper::get_personio_login_url_example() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$field->set_sanitize_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationLoginUrl', 'validate' ) );
		$setting->set_field( $field );

		/**
		 * Add the settings for the template list section.
		 */
		// add setting.
		$setting_enable_filter = $settings_obj->add_setting( 'personioIntegrationEnableFilter' );
		$setting_enable_filter->set_section( $template_list );
		$setting_enable_filter->set_show_in_rest( true );
		$setting_enable_filter->set_type( 'integer' );
		$setting_enable_filter->set_default( 0 );
		$field = new Checkbox();
		$field->set_title( __( 'Enable filter on list-view', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting_enable_filter->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateFilter' );
		$setting->set_section( $template_list );
		$setting->set_type( 'array' );
		$setting->set_default( array( 'recruitingCategory', 'schedule', 'office' ) );
		$field = new MultiSelect();
		$field->set_title( __( 'Available filter for details', 'personio-integration-light' ) );
		$field->set_description( __( 'Mark multiple default filter for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ) );
		$field->set_options( $list_template_filter );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$field->add_depend( $setting_enable_filter, 1 );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationFilterType' );
		$setting->set_section( $template_list );
		$setting->set_type( 'string' );
		$setting->set_default( 'linklist' );
		$field = new Select();
		$field->set_title( __( 'Choose filter-type', 'personio-integration-light' ) );
		$field->set_description( __( 'This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ) );
		$field->set_options( Helper::get_filter_types() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$field->add_depend( $setting_enable_filter, 1 );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationHideFilterTitle' );
		$setting->set_section( $template_list );
		$setting->set_type( 'integer' );
		$setting->set_default( 1 );
		$field = new Checkbox();
		$field->set_title( __( 'Hide filter title', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$field->add_depend( $setting_enable_filter, 1 );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationHideFilterReset' );
		$setting->set_section( $template_list );
		$setting->set_type( 'integer' );
		$setting->set_default( 0 );
		$field = new Checkbox();
		$field->set_title( __( 'Hide reset link', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$field->add_depend( $setting_enable_filter, 1 );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateContentListingTemplate' );
		$setting->set_section( $template_list );
		$setting->set_type( 'string' );
		$setting->set_default( 'default' );
		$field = new Select();
		$field->set_title( __( 'Choose template for listing', 'personio-integration-light' ) );
		/* translators: %1$s will be replaced with the documentation-URL */
		$field->set_description( sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ) );
		$field->set_options( Helper::get_filter_types() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateContentList' );
		$setting->set_section( $template_list );
		$setting->set_type( 'array' );
		$setting->set_default( array( 'title', 'excerpt' ) );
		$field = new MultiSelect();
		$field->set_title( __( 'List View', 'personio-integration-light' ) );
		$field->set_description( __( 'Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ) );
		$field->set_options( Templates::get_instance()->get_template_labels() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateListingExcerptsTemplate' );
		$setting->set_section( $template_list );
		$setting->set_type( 'string' );
		$setting->set_default( 'default' );
		$field = new Select();
		$field->set_title( __( 'Choose template for position details', 'personio-integration-light' ) );
		/* translators: %1$s will be replaced with the documentation-URL */
		$field->set_description( sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ) );
		$field->set_options( Templates::get_instance()->get_excerpts_templates() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateExcerptDefaults' );
		$setting->set_section( $template_list );
		$setting->set_type( 'array' );
		$setting->set_default( array( 'recruitingCategory', 'schedule', 'office' ) );
		$field = new MultiSelect();
		$field->set_title( __( 'Choose details for positions in list-view', 'personio-integration-light' ) );
		$field->set_description( __( 'Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ) );
		$field->set_options( $list_excerpt );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateListingContentTemplate' );
		$setting->set_section( $template_list );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'string' );
		$setting->set_default( 'default' );
		$field = new Select();
		$field->set_title( __( 'Choose template for content in list-view', 'personio-integration-light' ) );
		/* translators: %1$s will be replaced with the documentation-URL */
		$field->set_description( sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ) );
		$field->set_options( Templates::get_instance()->get_jobdescription_templates() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationEnableLinkInList' );
		$setting->set_section( $template_list );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'integer' );
		$setting->set_default( 1 );
		$field = new Checkbox();
		$field->set_title( __( 'Enable link to single on list-view', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		/**
		 * Add the settings for the template detail section.
		 */
		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateContentDefaults' );
		$setting->set_section( $template_detail );
		$setting->set_type( 'array' );
		$setting->set_default( array( 'title', 'content', 'formular' ) );
		$field = new MultiSelect();
		$field->set_title( __( 'Choose templates', 'personio-integration-light' ) );
		$field->set_description( __( 'Mark multiple default templates for each detail-view of single positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ) );
		$field->set_options( Templates::get_instance()->get_template_labels() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateDetailsExcerptsTemplate' );
		$setting->set_section( $template_detail );
		$setting->set_type( 'string' );
		$setting->set_default( 'default' );
		$field = new Select();
		$field->set_title( __( 'Choose template for content in list-view', 'personio-integration-light' ) );
		/* translators: %1$s will be replaced with the documentation-URL */
		$field->set_description( sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ) );
		$field->set_options( Templates::get_instance()->get_excerpts_templates() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateExcerptDetail' );
		$setting->set_section( $template_detail );
		$setting->set_type( 'array' );
		$setting->set_default( array( 'recruitingCategory', 'schedule', 'office' ) );
		$field = new MultiSelect();
		$field->set_title( __( 'Choose details', 'personio-integration-light' ) );
		$field->set_description( __( 'Mark multiple details for single-view of positions. Only used if template "detail" is enabled for detail-view. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ) );
		$field->set_options( $detail_excerpt );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateJobDescription' );
		$setting->set_section( $template_detail );
		$setting->set_type( 'string' );
		$setting->set_default( 'default' );
		$field = new Select();
		$field->set_title( __( 'Choose job description template in details-view', 'personio-integration-light' ) );
		/* translators: %1$s will be replaced with the documentation-URL */
		$field->set_description( sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ) );
		$field->set_options( Templates::get_instance()->get_jobdescription_templates() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting_back_to_list_button = $settings_obj->add_setting( 'personioIntegrationTemplateBackToListButton' );
		$setting_back_to_list_button->set_section( $template_detail );
		$setting_back_to_list_button->set_type( 'integer' );
		$setting_back_to_list_button->set_default( 0 );
		$field = new Checkbox();
		$field->set_title( __( 'Enable back to list-link', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting_back_to_list_button->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateBackToListUrl' );
		$setting->set_section( $template_detail );
		$setting->set_type( 'string' );
		$setting->set_default( '' );
		$field = new Text();
		$field->set_title( __( 'URL for back to list-link', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$field->add_depend( $setting_back_to_list_button, 1 );
		$setting->set_field( $field );

		/**
		 * Add the settings for the template other section.
		 */
		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateExcerptSeparator' );
		$setting->set_section( $template_other );
		$setting->set_type( 'string' );
		$setting->set_default( ', ' );
		$field = new Text();
		$field->set_title( __( 'Separator for details-listing', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		/**
		 * Add the settings for the advanced section.
		 */
		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationExtendSearch' );
		$setting->set_section( $advanced );
		$setting->set_type( 'integer' );
		$setting->set_default( 0 );
		$field = new Checkbox();
		$field->set_title( __( 'Note the position-keywords in search in frontend', 'personio-integration-light' ) );
		$field->set_description( __( 'If activated, the keywords stored at the locations in Personio are taken into account within the WordPress full-text search.', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationMaxAgeLogEntries' );
		$setting->set_section( $advanced );
		$setting->set_type( 'integer' );
		$setting->set_default( 20 );
		$field = new Number();
		$field->set_title( __( 'max. Age for log entries in days', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationUrlTimeout' );
		$setting->set_section( $advanced );
		$setting->set_type( 'integer' );
		$setting->set_default( 30 );
		$field = new Number();
		$field->set_title( __( 'Timeout for URL-request in Seconds', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$field->set_sanitize_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\UrlTimeout', 'validate' ) );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationShowHelp' );
		$setting->set_section( $advanced );
		$setting->set_type( 'integer' );
		$setting->set_default( 1 );
		$field = new Checkbox();
		$field->set_title( __( 'Show help', 'personio-integration-light' ) );
		$field->set_description( __( 'If enabled you will get help texts on your page builder if you edit positions.', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add import.
		Import::get_instance()->add_settings( $settings_obj, $advanced );

		// add export.
		Export::get_instance()->add_settings( $settings_obj, $advanced );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationDeleteOnUninstall' );
		$setting->set_section( $advanced );
		$setting->set_type( 'integer' );
		$setting->set_default( 1 );
		$field = new Checkbox();
		$field->set_title( __( 'Delete all imported data on uninstall', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegration_debug' );
		$setting->set_section( $advanced );
		$setting->set_type( 'integer' );
		$setting->set_default( 0 );
		$field = new Checkbox();
		$field->set_title( __( 'Debug-Mode', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		/* translators: %1$s will be replaced by a URL. */
		$field->set_description( sprintf( __( 'When activated, the plugin logs many processes. This information can then be seen <a href="%1$s">in the log</a>. This helps to analyze any problems that may occur. At the same time, all open positions are retrieved in full at any time - it will not be checked whether anything has been changed in Personio. <strong>We do not recommend using this mode permanently in a productive system.</strong>', 'personio-integration-light' ), esc_url( Helper::get_settings_url( 'personioPositions', 'logs' ) ) ) );
		$setting->set_field( $field );

		/**
		 * Add the settings for the template other section.
		 */
		// add setting.
		$setting = $settings_obj->add_setting( WP_PERSONIO_INTEGRATION_TRANSIENTS_LIST );
		$setting->set_section( $hidden );
		$setting->set_type( 'array' );
		$setting->set_default( array() );
		$setting->prevent_export( true );

		// add setting.
		$setting = $settings_obj->add_setting( 'personio_integration_update_slugs' );
		$setting->set_section( $hidden );
		$setting->set_type( 'integer' );
		$setting->set_default( 0 );
		$setting->prevent_export( true );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationLightInstallDate' );
		$setting->set_section( $hidden );
		$setting->set_type( 'integer' );
		$setting->set_default( time() );
		$setting->prevent_export( true );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationPositionScheduleInterval' );
		$setting->set_section( $hidden );
		$setting->set_type( 'string' );
		$setting->set_default( 'daily' );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationVersion' );
		$setting->set_section( $hidden );
		$setting->set_type( 'string' );
		$setting->set_default( WP_PERSONIO_INTEGRATION_VERSION );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationPageBuilder' );
		$setting->set_section( $hidden );
		$setting->set_type( 'array' );
		$setting->set_default( array() );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationPositionCount' );
		$setting->set_section( $hidden );
		$setting->set_type( 'integer' );
		$setting->set_default( 0 );
		$setting->prevent_export( true );

		// add setting.
		$setting = $settings_obj->add_setting( 'personio_integration_schedules' );
		$setting->set_section( $hidden );
		$setting->set_type( 'array' );
		$setting->set_default( array() );
	}

	/**
	 * Show copyright hints.
	 *
	 * @return void
	 */
	public function show_copyright(): void {
		?>
		<div class="wrap">
		<?php echo wp_kses_post( Helper::get_logo_img( true ) ); ?>
		<p>
			<?php
			/* translators: %1$s will be replaced by the URL for Personio */
			echo wp_kses_post( sprintf( __( 'The Personio logo as part of all distributed icons is a trademark of <a href="%1$s" target="_blank">Personio SE & Co. KG (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_personio_url() ) ) );
			?>
		</p>
		</div>
		<?php
	}

	/**
	 * Add help for the cpt.
	 *
	 * @param array<int,array<string,string>> $help_list List of help tabs.
	 *
	 * @return array<int,array<string,string>>
	 */
	public function add_help( array $help_list ): array {
		// collect the content for the help.
		$content  = Helper::get_logo_img( true ) . '<h2>' . __( 'Settings', 'personio-integration-light' ) . '</h2><p>' . __( 'We provide you with a variety of possible settings. You can use these to influence the behavior of the plugin as well as the appearance of your positions in the frontend.', 'personio-integration-light' ) . '</p>';
		$content .= '<p><strong>' . __( 'How to use:', 'personio-integration-light' ) . '</strong></p>';
		$content .= '<ol>';
		/* translators: %1$s will be replaced by a URL. */
		$content .= '<li>' . sprintf( __( 'Call up the <a href="%1$s">page with the settings</a>.', 'personio-integration-light' ), esc_url( Helper::get_settings_url() ) ) . '</li>';
		$content .= '<li>' . __( 'You will find a short explanation for each setting.', 'personio-integration-light' ) . '</li>';
		$content .= '<li>' . __( 'Adjust the settings to your requirements.', 'personio-integration-light' ) . '</li>';
		$content .= '<li>' . __( 'Check your settings where they should apply.', 'personio-integration-light' ) . '</li>';
		$false    = false;
		/**
		 * Hide hint for Pro-plugin.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Set true to hide the hint.
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( ! apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			/* translators: %1$s will be replaced by a URL. */
			$content .= '<li>' . sprintf( __( '<a href="%1$s" target="_blank">Order Personio Integration Pro (opens new window)</a> to get much more settings.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) . '</li>';
		}
		$content .= '</ol>';

		// add help for the positions in general.
		$help_list[] = array(
			'id'      => PersonioPosition::get_instance()->get_name() . '-settings',
			'title'   => __( 'Settings', 'personio-integration-light' ),
			'content' => $content,
		);

		// return resulting list.
		return $help_list;
	}

	/**
	 * Add pro hint on tab title.
	 *
	 * @param string $title The title of the tab.
	 * @param Tab    $tab The used tab.
	 *
	 * @return string
	 */
	public function add_pro_on_title( string $title, Tab $tab ): string {
		// bail if tab is not "use_pro".
		if ( 'use_pro' !== $tab->get_name() ) {
			return $title;
		}

		// add the title.
		return $title . ' <a class="pro-marker" href="' . esc_url( Helper::get_pro_url() ) . '" target="_blank">Pro</a>';
	}

	/**
	 * Add import category for log view.
	 *
	 * @param array<string,string> $categories List of categories.
	 *
	 * @return array<string,string>
	 */
	public function add_log_categories( array $categories ): array {
		// add categories we need for our settings.
		$categories['settings'] = __( 'Settings', 'personio-integration-light' );

		// return resulting list.
		return $categories;
	}

	/**
	 * Show pro hint for advanced options.
	 *
	 * @return void
	 */
	public function show_advanced_hint(): void {
		/* translators: %1$s will be replaced with the plugin Pro name */
		echo wp_kses_post( apply_filters( 'personio_integration_admin_show_pro_hint', __( 'With %1$s you get more advanced options, e.g. to change the URL of archives with positions.', 'personio-integration-light' ), false ) );
	}

	/**
	 * Return the hidden section of the settings object.
	 *
	 * @return false|Section
	 */
	public function get_hidden_section(): Section|false {
		// get settings object.
		$settings_obj = \PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings::get_instance();

		// create a hidden page for hidden settings.
		$hidden_page = $settings_obj->get_page( 'hidden_page' );

		// bail if page could not be found.
		if ( ! $hidden_page instanceof Page ) {
			return false;
		}

		// create a hidden tab on this page.
		$hidden_tab = $hidden_page->get_tab( 'hidden_tab' );

		// bail if tab could not be found.
		if ( ! $hidden_tab instanceof Tab ) {
			return false;
		}

		// the hidden section for any not visible settings.
		$hidden_section = $hidden_tab->get_section( 'hidden_section' );

		// bail if section could not be found.
		if ( ! $hidden_section instanceof Section ) {
			return false;
		}

		// return the hidden section object.
		return $hidden_section;
	}

	/**
	 * Show hint where extensions could be managed.
	 *
	 * @return void
	 */
	public function show_extensions_hint(): void {
		/* translators: %1$s will be replaced by a URL. */
		echo '<p class="personio-integration-hint">' . wp_kses_post( sprintf( __( 'Manage your active extensions <a href="%1$s">here</a>. Depending on the active extensions, your setting options will expand here.', 'personio-integration-light' ), esc_url( Extensions::get_instance()->get_link() ) ) ) . '</p>';
	}
}

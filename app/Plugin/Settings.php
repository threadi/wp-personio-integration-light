<?php
/**
 * File to handle plugin-settings.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Checkbox;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Checkboxes;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\MultiSelect;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Number;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Radio;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Select;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Text;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Tab;
use PersonioIntegrationLight\Helper;
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
		$settings_obj = \PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings::get_instance();
		$settings_obj->set_slug( 'personio_integration_light' );
		$settings_obj->set_plugin_slug( WP_PERSONIO_INTEGRATION_PLUGIN );
		$settings_obj->set_menu_title( __( 'Settings', 'personio-integration-light' ) );
		$settings_obj->set_title( $title );
		$settings_obj->set_menu_slug( 'personioPositions' );
		$settings_obj->set_menu_parent_slug( 'edit.php?post_type=' . PersonioPosition::get_instance()->get_name() );
		$settings_obj->set_menu_position( 1 );

		// initialize this settings object.
		$settings_obj->init();

		/**
		 * Configure all tabs for this object.
		 */
		// the basic tab.
		$general_tab = $settings_obj->add_tab( 'basic' );
		$general_tab->set_title( __( 'Basic Settings', 'personio-integration-light' ) );
		$settings_obj->set_default_tab( $general_tab );

		// the templates tab.
		$templates_tab = $settings_obj->add_tab( 'templates' );
		$templates_tab->set_title( __( 'Templates', 'personio-integration-light' ) );

		// the pro tab.
		$pro_tab = $settings_obj->add_tab( 'use_pro' );
		$pro_tab->set_title( __( 'Applications, SEO & more', 'personio-integration-light' ) );
		$pro_tab->set_not_linked( true );

		// the advanced tab.
		$advanced_tab = $settings_obj->add_tab( 'advanced' );
		$advanced_tab->set_title( __( 'Advanced', 'personio-integration-light' ) );

		// the logs tab.
		$logs_tab = $settings_obj->add_tab( 'logs' );
		$logs_tab->set_title( __( 'Logs', 'personio-integration-light' ) );
		$logs_tab->set_hide_save( true );
		$logs_tab->set_callback( array( '\PersonioIntegrationLight\Plugin\Admin\Logs', 'show' ) );

		// the copyright tab.
		$copyright_tab = $settings_obj->add_tab( 'copyright' );
		$copyright_tab->set_title( '&nbsp;' );
		$copyright_tab->set_tab_class( 'copyright' );
		$copyright_tab->set_hide_save( true );
		$copyright_tab->set_callback( array( $this, 'show_copyright' ) );

		// the help tab.
		$help_tab = $settings_obj->add_tab( 'help' );
		$help_tab->set_title( __( 'Questions? Check our forum!', 'personio-integration-light' ) );
		$help_tab->set_tab_class( 'nav-tab-help' );
		$help_tab->set_url( Helper::get_plugin_support_url() );
		$help_tab->set_url_target( '_blank' );

		/**
		 * Create sections for this settings object.
		 */
		// the main section.
		$general_tab_main = $general_tab->add_section( 'settings_section_main' );
		$general_tab_main->set_title( __( 'General Settings', 'personio-integration-light' ) );
		$general_tab_main->set_setting( $settings_obj );

		// the template list section.
		$template_list = $templates_tab->add_section( 'settings_section_template_list' );
		$template_list->set_title( __( 'List View', 'personio-integration-light' ) );
		$template_list->set_setting( $settings_obj );
		$template_list->set_callback( array( $this, 'show_fse_hint' ) );

		// the template detail section.
		$template_detail = $templates_tab->add_section( 'settings_section_template_detail' );
		$template_detail->set_title( __( 'Detail View', 'personio-integration-light' ) );
		$template_detail->set_setting( $settings_obj );

		// the template other section.
		$template_other = $templates_tab->add_section( 'settings_section_template_other' );
		$template_other->set_title( __( 'Other settings', 'personio-integration-light' ) );
		$template_other->set_setting( $settings_obj );

		// the advanced section.
		$advanced = $advanced_tab->add_section( 'settings_section_advanced' );
		$advanced->set_title( __( 'Advanced settings', 'personio-integration-light' ) );
		$advanced->set_setting( $settings_obj );
		$advanced->set_callback( array( $this, 'show_advanced_hint' ) );

		// the hidden section.
		$hidden = $advanced_tab->add_section( 'hidden_section' );
		$hidden->set_setting( $settings_obj );

		/**
		 * Add the settings for the main settings.
		 */
		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationUrl' );
		$setting->set_section( $general_tab_main );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'string' );
		$setting->set_default( '' );
		$setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl', 'validate' ) );
		$field = new Text();
		$field->set_title( __( 'Personio URL', 'personio-integration-light' ) );
		/* translators: %1$s is replaced with the URL to Personio login for account access, %2$s is replaced with the url to the Personio support */
		$field->set_description( sprintf( __( 'You find this URL in your <a href="%1$s" target="_blank">Personio-account (opens new window)</a> under Settings > Recruiting > Career Page > Activations.<br><strong>Hint:</strong> You have to enable the XML-feed under Settings > Recruiting > Career in your Personio account.<br>If you have any questions about the URL provided by Personio, please contact the <a href="%2$s" target="_blank">Personio support (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_personio_login_url() ), esc_url( Helper::get_personio_support_url() ) ) );
		$field->set_placeholder( Helper::get_personio_url_example() );
		$setting->set_field( $field );

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
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION );
		$setting->set_section( $general_tab_main );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'array' );
		$setting->set_default( array( Languages::get_instance()->get_current_lang() => 1 ) );
		$setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\Languages', 'validate' ) );
		$field = new Checkboxes();
		$field->set_title( __( 'Used languages', 'personio-integration-light' ) );
		$field->set_description( __( 'Activate the languages in which your jobs should be displayed on the website.', 'personio-integration-light' ) );
		$field->set_options( Languages::get_instance()->get_languages() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationLoginUrl' );
		$setting->set_section( $general_tab_main );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'string' );
		$setting->set_default( '' );
		$setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationLoginUrl', 'validate' ) );
		$field = new Text();
		$field->set_title( __( 'Personio Login URL', 'personio-integration-light' ) );
		/* translators: %1$s is replaced with the URL to the Personio support */
		$field->set_description( sprintf( __( 'This URL is used by Personio to give you a unique login URL to your Personio account. It will be communicated to you when you register with Personio.<br>This is NOT the URL where your open positions are visible.<br>If you have any questions about this URL, please contact the <a href="%1$s" target="_blank">Personio support (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_personio_support_url() ) ) );
		$field->set_placeholder( Helper::get_personio_login_url_example() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		/**
		 * Add the settings for the template list section.
		 */
		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationEnableFilter' );
		$setting->set_section( $template_list );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'integer' );
		$setting->set_default( 0 );
		$field = new Checkbox();
		$field->set_title( __( 'Enable filter on list-view', 'personio-integration-light' ) );
		$field->set_description( __( 'Activate the languages in which your jobs should be displayed on the website.', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateFilter' );
		$setting->set_section( $template_list );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'array' );
		$setting->set_default( array( 'recruitingCategory', 'schedule', 'office' ) );
		$field = new MultiSelect();
		$field->set_title( __( 'Available filter for details', 'personio-integration-light' ) );
		$field->set_description( __( 'Mark multiple default filter for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ) );
		$field->set_options( $list_template_filter );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationFilterType' );
		$setting->set_section( $template_list );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'string' );
		$setting->set_default( 'linklist' );
		$field = new Select();
		$field->set_title( __( 'Choose filter-type', 'personio-integration-light' ) );
		$field->set_description( __( 'This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ) );
		$field->set_options( Helper::get_filter_types() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationHideFilterTitle' );
		$setting->set_section( $template_list );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'integer' );
		$setting->set_default( 1 );
		$field = new Checkbox();
		$field->set_title( __( 'Hide filter title', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationHideFilterReset' );
		$setting->set_section( $template_list );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'integer' );
		$setting->set_default( 0 );
		$field = new Checkbox();
		$field->set_title( __( 'Hide reset link', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateContentListingTemplate' );
		$setting->set_section( $template_list );
		$setting->set_show_in_rest( true );
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
		$setting->set_show_in_rest( true );
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
		$setting->set_show_in_rest( true );
		$setting->set_type( 'string' );
		$setting->set_default( 'default' );
		$field = new Select();
		$field->set_title( __( 'Choose template for listing', 'personio-integration-light' ) );
		/* translators: %1$s will be replaced with the documentation-URL */
		$field->set_description( sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ) );
		$field->set_options( Templates::get_instance()->get_excerpts_templates() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateExcerptDefaults' );
		$setting->set_section( $template_list );
		$setting->set_show_in_rest( true );
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
		$setting->set_show_in_rest( true );
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
		$setting->set_show_in_rest( true );
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
		$setting->set_show_in_rest( true );
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
		$setting->set_show_in_rest( true );
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
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateBackToListButton' );
		$setting->set_section( $template_detail );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'integer' );
		$setting->set_default( 0 );
		$field = new Checkbox();
		$field->set_title( __( 'Enable back to list-link', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateBackToListUrl' );
		$setting->set_section( $template_detail );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'string' );
		$setting->set_default( '' );
		$field = new Text();
		$field->set_title( __( 'URL for back to list-link', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		/**
		 * Add the settings for the template other section.
		 */
		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationTemplateExcerptSeparator' );
		$setting->set_section( $template_other );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'string' );
		$setting->set_default( ', ' );
		$field = new Text();
		$field->set_title( __( 'Separator for details-listing', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		/**
		 * Add the settings for the template other section.
		 */
		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationExtendSearch' );
		$setting->set_section( $advanced );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'integer' );
		$setting->set_default( 0 );
		$field = new Checkbox();
		$field->set_title( __( 'Note the position-keywords in search in frontend', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationMaxAgeLogEntries' );
		$setting->set_section( $advanced );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'integer' );
		$setting->set_default( 20 );
		$field = new Number();
		$field->set_title( __( 'max. Age for log entries in days', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationUrlTimeout' );
		$setting->set_section( $advanced );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'integer' );
		$setting->set_default( 30 );
		$field = new Number();
		$field->set_title( __( 'Timeout for URL-request in Seconds', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationShowHelp' );
		$setting->set_section( $advanced );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'integer' );
		$setting->set_default( 1 );
		$field = new Checkbox();
		$field->set_title( __( 'Show help', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationDeleteOnUninstall' );
		$setting->set_section( $advanced );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'integer' );
		$setting->set_default( 1 );
		$field = new Checkbox();
		$field->set_title( __( 'Delete all imported data on uninstall', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegration_debug' );
		$setting->set_section( $advanced );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'integer' );
		$setting->set_default( 1 );
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
		 * Hide pro hint in help.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Set true to hide the buttons.
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
		if( 'use_pro' !== $tab->get_name() ) {
			return $title;
		}

		// add the title.
		return $title . ' <a class="pro-marker" href="' . esc_url( Helper::get_pro_url() ) . '" target="_blank">Pro</a>';
	}

	/**
	 * Add import categories.
	 *
	 * @param array $categories List of categories.
	 *
	 * @return array
	 */
	public function add_log_categories( array $categories ): array {
		// add categories we need for our settings.
		$categories['settings'] = __( 'Settings', 'personio-integration-light' );

		// return resulting list.
		return $categories;
	}

	/**
	 * Show fse hint above template list.
	 *
	 * Will be removed if no FSE-theme is used.
	 *
	 * @return void
	 */
	public function show_fse_hint(): void {
		// get Block Editor URL.
		$editor_url = add_query_arg(
			array(
				'path' => '/wp_template/all',
			),
			admin_url( 'site-editor.php' )
		);

		/* translators: %1$s will be replaced with the name of the theme, %2$s will be replaced by the URL for the editor */
		echo '<p class="personio-integration-hint">' . wp_kses_post( sprintf( __( 'You are using with <i>%1$s</i> a modern block theme. The settings here will therefore might not work. Edit the archive- and single-template under <a href="%2$s">Appearance > Editor > Templates > Manage</a>.', 'personio-integration-light' ), esc_html( Helper::get_theme_title() ), esc_url( $editor_url ) ) ) . '</p>';
	}

	/**
	 * Show pro hint for advanced options.
	 *
	 * @return void
	 */
	public function show_advanced_hint(): void {
		/* translators: %1$s will be replaced with the plugin Pro name */
		echo '<p class="personio-pro-hint">' . wp_kses_post( sprintf( __( 'With %1$s you get more advanced options, e.g. to change the URL of archives with positions.', 'personio-integration-light' ), 'TODO' ) ) . '</p>';
	}
}

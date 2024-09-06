<?php
/**
 * File to handle plugin-settings.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
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
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Variable for complete settings.
	 *
	 * @var array
	 */
	private array $settings = array();

	/**
	 * Variable for tab settings.
	 *
	 * @var array
	 */
	private array $tabs = array();

	/**
	 * Initialize the settings.
	 *
	 * @return void
	 */
	public function init(): void {
		// set all settings for this plugin.
		add_action( 'init', array( $this, 'set_settings' ) );

		// add import and export of settings.
		Settings_Export::get_instance()->init();
		Settings_Import::get_instance()->init();

		// register all settings for this plugin.
		add_action( 'init', array( $this, 'register_settings' ) );
		add_action( 'init', array( $this, 'change_settings' ) );

		// register fields to manage the settings.
		add_action( 'admin_init', array( $this, 'register_fields' ) );
		add_action( 'admin_init', array( $this, 'register_field_callbacks' ) );
		add_action( 'rest_api_init', array( $this, 'register_field_callbacks' ) );

		// add admin-menu.
		add_action( 'admin_menu', array( $this, 'add_settings_menu' ) );

		// secure our own plugin settings.
		add_action( 'updated_option', array( $this, 'secure_settings' ), 10, 3 );

		// use our own hooks.
		add_filter( 'personio_integration_log_categories', array( $this, 'add_log_categories' ) );
	}

	/**
	 * Define ALL settings for this plugin.
	 *
	 * @return void
	 */
	public function set_settings(): void {
		// set tabs.
		$this->tabs = array(
			array(
				'label'         => __( 'Basic Settings', 'personio-integration-light' ),
				'key'           => '',
				'settings_page' => 'personioIntegrationMainSettings',
				'page'          => 'personioPositions',
				'order'         => 10,
			),
			array(
				'label'         => __( 'Templates', 'personio-integration-light' ),
				'key'           => 'templates',
				'settings_page' => 'personioIntegrationPositionsTemplates',
				'page'          => 'personioPositions',
				'order'         => 20,
			),
			array(
				'label'         => __( 'Import', 'personio-integration-light' ),
				'key'           => 'import',
				'settings_page' => 'personioIntegrationPositionsImport',
				'page'          => 'personioPositions',
				'order'         => 30,
			),
			array(
				'label'    => __( 'Applications, SEO & more', 'personio-integration-light' ),
				'key'      => 'use_pro',
				'only_pro' => true,
				'page'     => 'personioPositions',
				'order'    => 40,
			),
			array(
				'label'         => __( 'Advanced', 'personio-integration-light' ),
				'key'           => 'advanced',
				'settings_page' => 'personioIntegrationPositionsAdvanced',
				'page'          => 'personioPositions',
				'order'         => 800,
			),
			array(
				'label'    => __( 'Logs', 'personio-integration-light' ),
				'key'      => 'logs',
				'callback' => array( 'PersonioIntegrationLight\Plugin\Admin\Logs', 'show' ),
				'page'     => 'personioPositions',
				'order'    => 900,
			),
			array(
				'label'    => '&nbsp;',
				'key'      => 'copyright',
				'callback' => array( $this, 'show_copyright' ),
				'class'    => 'copyright',
				'page'     => 'personioPositions',
				'order'    => 1000,
			),
			array(
				'label'      => __( 'Questions? Check our forum!', 'personio-integration-light' ),
				'key'        => 'help',
				'url'        => Helper::get_plugin_support_url(),
				'url_target' => '_blank',
				'class'      => 'nav-tab-help nav-tab-active',
				'page'       => 'personioPositions',
				'order'      => 2000,
			),
		);

		// reset tabs if Personio URL is not set.
		if ( ! Helper::is_personio_url_set() ) {
			$this->tabs = array(
				array(
					'label'         => __( 'General Settings', 'personio-integration-light' ),
					'key'           => '',
					'settings_page' => 'personioIntegrationMainSettings',
					'page'          => 'personioPositions',
				),
				array(
					'label'       => __( 'Enter Personio URL to get more options', 'personio-integration-light' ),
					'key'         => 'enter_url',
					'do_not_link' => true,
					'page'        => 'personioPositions',
				),
			);
		}

		// get taxonomies.
		$list_template_filter = array();
		$list_excerpt         = array();
		$detail_excerpt       = array();
		if ( Helper::is_personio_url_set() ) {
			$list_template_filter = Taxonomies::get_instance()->get_labels_for_settings( get_option( 'personioIntegrationTemplateFilter' ) );
			$list_excerpt         = Taxonomies::get_instance()->get_labels_for_settings( get_option( 'personioIntegrationTemplateExcerptDefaults' ) );
			$detail_excerpt       = Taxonomies::get_instance()->get_labels_for_settings( get_option( 'personioIntegrationTemplateExcerptDetail' ) );
		}

		// get Block Editor URL.
		$editor_url = add_query_arg(
			array(
				'path' => '/wp_template/all',
			),
			admin_url( 'site-editor.php' )
		);

		// define settings for this plugin.
		$this->settings = array(
			'settings_section_main'            => array(
				'label'         => __( 'General Settings', 'personio-integration-light' ),
				'settings_page' => 'personioIntegrationMainSettings',
				'callback'      => '__return_true',
				'fields'        => array(
					'personioIntegrationUrl'              => array(
						'label'               => __( 'Personio URL', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Text', 'get' ),
						/* translators: %1$s is replaced with the url to Personio login for account access, %2$s is replaced with the url to the Personio support */
						'description'         => sprintf( __( 'You find this URL in your <a href="%1$s" target="_blank">Personio-account (opens new window)</a> under Settings > Recruiting > Career Page > Activations.<br><strong>Hint:</strong> You have to enable the XML-feed under Settings > Recruiting > Career in your Personio account.<br>If you have any questions about the URL provided by Personio, please contact the <a href="%2$s" target="_blank">Personio support (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_personio_login_url() ), esc_url( Helper::get_personio_support_url() ) ),
						/* translators: %1$s is replaced with the name of the Pro-plugin */
						'pro_hint'            => __( 'Use multiple Personio accounts in one website with %1$s.', 'personio-integration-light' ),
						'placeholder'         => Helper::get_personio_url_example(),
						'highlight'           => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'default'           => '',
							'show_in_rest'      => true,
							'sanitize_callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl', 'validate' ),
							'type'              => 'string',
						),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\PersonioIntegrationUrl', 'save' ),
					),
					WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE => array(
						'label'               => __( 'Main language', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Radios', 'get' ),
						'description'         => __( 'Set the main language you will use for your open positions.', 'personio-integration-light' ),
						'options'             => Languages::get_instance()->get_languages(),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'show_in_rest'      => true,
							'sanitize_callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\MainLanguage', 'validate' ),
							'type'              => 'string',
							'default'           => Languages::get_instance()->get_current_lang(),
						),
					),
					WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION => array(
						'label'               => __( 'Used languages', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Checkboxes', 'get' ),
						'options'             => Languages::get_instance()->get_languages(),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'default'           => array( Languages::get_instance()->get_current_lang() => 1 ),
							'sanitize_callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\Languages', 'validate' ),
							'type'              => 'array',
						),
						/* translators: %1$s is replaced with the name of the Pro-plugin */
						'pro_hint'            => __( 'Use all languages supported by Personio with %s.', 'personio-integration-light' ),
					),
					'personioIntegrationLoginUrl'         => array(
						'label'               => __( 'Personio Login URL', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Text', 'get' ),
						/* translators: %1$s is replaced with the url to the Personio support */
						'description'         => sprintf( __( 'This URL is used by Personio to give you a unique login URL to your Personio account. It will be communicated to you when you register with Personio.<br>This is NOT the URL where your open positions are visible.<br>If you have any questions about this URL, please contact the <a href="%1$s" target="_blank">Personio support (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_personio_support_url() ) ),
						'placeholder'         => Helper::get_personio_login_url_example(),
						'register_attributes' => array(
							'default'           => '',
							'type'              => 'string',
							'sanitize_callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationLoginUrl', 'validate' ),
						),
					),
				),
			),
			'settings_section_template_list'   => array(
				'label'         => __( 'List View', 'personio-integration-light' ),
				'settings_page' => 'personioIntegrationPositionsTemplates',
				'callback'      => '__return_true',
				'fields'        => array(
					'personio_integration_fse_theme_hint' => array(
						'label'       => '',
						'field'       => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\TextHints', 'get' ),
						/* translators: %1$s will be replaced with the name of the theme, %2$s will be replaced by the URL for the editor */
						'description' => sprintf( __( 'You are using with <i>%1$s</i> a modern block theme. The settings here will therefore might not work. Edit the archive- and single-template under <a href="%2$s">Appearance > Editor > Templates > Manage</a>.', 'personio-integration-light' ), esc_html( Helper::get_theme_title() ), esc_url( $editor_url ) ),
						'highlight'   => true,
					),
					'personioIntegrationEnableFilter'     => array(
						'label'               => __( 'Enable filter on list-view', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'integer',
							'default' => 0,
						),
						'class'               => 'personio-integration-template-filter',
					),
					'personioIntegrationTemplateFilter'   => array(
						'label'               => __( 'Available filter for details', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Select', 'get' ),
						'options'             => $list_template_filter,
						'description'         => __( 'Mark multiple default filter for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						/* translators: %1$s is replaced with "string" */
						'pro_hint'            => __( 'Sort this list with %s.', 'personio-integration-light' ),
						'register_attributes' => array(
							'type'    => 'array',
							'default' => array( 'recruitingCategory', 'schedule', 'office' ),
						),
						'depends'             => array(
							'personioIntegrationEnableFilter' => 1,
						),
					),
					'personioIntegrationFilterType'       => array(
						'label'               => __( 'Choose filter-type', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						'options'             => Helper::get_filter_types(),
						'description'         => __( 'This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'string',
							'default' => 'linklist',
						),
						'depends'             => array(
							'personioIntegrationEnableFilter' => 1,
						),
					),
					'personioIntegrationTemplateContentListingTemplate' => array(
						'label'               => __( 'Choose template for listing', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description'         => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'options'             => Templates::get_instance()->get_archive_templates(),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'string',
							'default' => 'default',
						),
						'class'               => 'personio-integration-template-listing-template',
					),
					'personioIntegrationTemplateContentList' => array(
						'label'               => __( 'Choose templates for positions in list-view', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Select', 'get' ),
						'options'             => Templates::get_instance()->get_template_labels(),
						'description'         => __( 'Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'array',
							'default' => array( 'title', 'excerpt' ),
						),
						'class'               => 'personio-integration-template-content-list',
					),
					'personioIntegrationTemplateListingExcerptsTemplate' => array(
						'label'               => __( 'Choose template for details in list-view', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description'         => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'options'             => Templates::get_instance()->get_excerpts_templates(),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'string',
							'default' => 'default',
						),
						'class'               => 'personio-integration-template-excerpts-template',
					),
					'personioIntegrationTemplateExcerptDefaults' => array(
						'label'               => __( 'Choose details for positions in list-view', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Select', 'get' ),
						'options'             => $list_excerpt,
						'description'         => __( 'Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'array',
							'default' => array( 'recruitingCategory', 'schedule', 'office' ),
						),
						'class'               => 'personio-integration-template-excerpts-defaults',
					),
					'personioIntegrationTemplateListingContentTemplate' => array(
						'label'               => __( 'Choose template for content in list-view', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description'         => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'options'             => Templates::get_instance()->get_jobdescription_templates(),
						'readonly'            => ! Helper::is_personio_url_set(),
						'default'             => 'default',
						'register_attributes' => array(
							'type'    => 'string',
							'default' => 'default',
						),
						'class'               => 'personio-integration-template-content-template',
					),
					'personioIntegrationEnableLinkInList' => array(
						'label'               => __( 'Enable link to single on list-view', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'integer',
							'default' => 1,
						),
					),
				),
			),
			'settings_section_template_detail' => array(
				'label'         => __( 'Single View', 'personio-integration-light' ),
				'settings_page' => 'personioIntegrationPositionsTemplates',
				'callback'      => '__return_true',
				'fields'        => array(
					'personioIntegrationTemplateContentDefaults' => array(
						'label'               => __( 'Choose templates', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Select', 'get' ),
						'options'             => Templates::get_instance()->get_template_labels(),
						'description'         => __( 'Mark multiple default templates for each detail-view of single positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'array',
							'default' => array( 'title', 'content', 'formular' ),
						),
						'class'               => 'personio-integration-template-content-template-2',
					),
					'personioIntegrationTemplateDetailsExcerptsTemplate' => array(
						'label'               => __( 'Choose template for details in details-view', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description'         => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'options'             => Templates::get_instance()->get_excerpts_templates(),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'string',
							'default' => 'default',
						),
						'class'               => 'personio-integration-template-excerpts-template-2',
					),
					'personioIntegrationTemplateExcerptDetail' => array(
						'label'               => __( 'Choose details', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Select', 'get' ),
						'options'             => $detail_excerpt,
						'description'         => __( 'Mark multiple details for single-view of positions. Only used if template "detail" is enabled for detail-view. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						/* translators: %1$s is replaced with "string" */
						'pro_hint'            => __( 'Sort this list with %s.', 'personio-integration-light' ),
						'register_attributes' => array(
							'type'    => 'array',
							'default' => array( 'recruitingCategory', 'schedule', 'office' ),
						),
						'class'               => 'personio-integration-template-excerpt-detail-2',
					),
					'personioIntegrationTemplateJobDescription' => array(
						'label'               => __( 'Choose job description template in details-view', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						'options'             => Templates::get_instance()->get_jobdescription_templates(),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description'         => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'string',
							'default' => 'default',
						),
					),
					'personioIntegrationTemplateBackToListButton' => array(
						'label'               => __( 'Enable back to list-link', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'integer',
							'default' => 0,
						),
					),
					'personioIntegrationTemplateBackToListUrl' => array(
						'label'               => __( 'URL for back to list-link', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Text', 'get' ),
						/* translators: %1$s will be replaced by the list-slug */
						'description'         => sprintf( __( 'If empty the link will be set to list-slug <a href="%1$s">%1$s</a>.', 'personio-integration-light' ), esc_url( trailingslashit( get_home_url() ) . Helper::get_archive_slug() ) ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'string',
							'default' => '',
						),
						'depends'             => array(
							'personioIntegrationTemplateBackToListButton' => 1,
						),
					),
				),
			),
			'settings_section_template_other'  => array(
				'label'         => __( 'Other settings', 'personio-integration-light' ),
				'settings_page' => 'personioIntegrationPositionsTemplates',
				'callback'      => '__return_true',
				'fields'        => array(
					'personioIntegrationTemplateExcerptSeparator' => array(
						'label'               => __( 'Separator for details-listing', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Text', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'string',
							'default' => ', ',
						),
					),
				),
			),
			'settings_section_advanced'        => array(
				'label'         => __( 'Advanced settings', 'personio-integration-light' ),
				'settings_page' => 'personioIntegrationPositionsAdvanced',
				'callback'      => '__return_true',
				'fields'        => array(
					'personioIntegration_advanced_pro_hint' => array(
						'label'    => '',
						'field'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\ProHint', 'get' ),
						/* translators: %1$s will be replaced with the plugin Pro name */
						'pro_hint' => __( 'With %1$s you get more advanced options, e.g. to change the URL of archives with positions.', 'personio-integration-light' ),
					),
					'personioIntegrationExtendSearch'      => array(
						'label'               => __( 'Note the position-keywords in search in frontend', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'integer',
							'default' => 1,
						),
					),
					'personioIntegrationMaxAgeLogEntries'  => array(
						'label'               => __( 'max. Age for log entries in days', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Number', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'integer',
							'default' => 20,
						),
					),
					'personioIntegrationUrlTimeout'        => array(
						'label'               => __( 'Timeout for URL-request in Seconds', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Number', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'sanitize_callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\UrlTimeout', 'validate' ),
							'type'              => 'integer',
							'default'           => 30,
						),
					),
					'personioIntegrationShowHelp'          => array(
						'label'               => __( 'Show help', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'integer',
							'default' => 1,
						),
					),
					'personioIntegrationDeleteOnUninstall' => array(
						'label'               => __( 'Delete all imported data on uninstall', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'integer',
							'default' => 1,
						),
					),
					'personioIntegration_debug'            => array(
						'label'               => __( 'Debug-Mode', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'    => 'integer',
							'default' => 0,
						),
					),
				),
			),
			'settings_section_import'          => array(
				'label'         => __( 'Import of positions from Personio', 'personio-integration-light' ),
				'settings_page' => 'personioIntegrationPositionsImport',
				'callback'      => '__return_true',
				'fields'        => array(
					'personioIntegrationImportNow' => array(
						'label' => __( 'Get open positions from Personio', 'personio-integration-light' ),
						'field' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\ImportPositions', 'get' ),
						'class' => 'personio-integration-import-now',
					),
					'personioIntegrationDeleteNow' => array(
						'label' => __( 'Delete local positions', 'personio-integration-light' ),
						'field' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\DeletePositions', 'get' ),
						'class' => 'personio-integration-delete-now',
					),
					'personioIntegrationEnablePositionSchedule' => array(
						'label'               => __( 'Enable automatic import', 'personio-integration-light' ),
						'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'description'         => __( 'The automatic import is run once per day. You don\'t have to worry about updating your jobs on the website yourself.', 'personio-integration-light' ),
						/* translators: %s will be replaced with the Pro-plugin-URL. */
						'pro_hint'            => __( 'Use more import options with the %s. Among other things, you get the possibility to change the time interval for imports and partial imports of very large position lists.', 'personio-integration-light' ),
						'register_attributes' => array(
							'type'    => 'integer',
							'default' => 1,
						),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\Import', 'save' ),
						'class'               => 'personio-integration-automatic-import',
					),
				),
			),
			'settings_section_import_other'    => array(
				'label'         => __( 'Other settings', 'personio-integration-light' ),
				'settings_page' => 'personioIntegrationPositionsImport',
				'callback'      => '__return_true',
				'fields'        => array(),
			),
			'hidden_section'                   => array(
				'settings_page' => 'hidden_personio_page',
				'fields'        => array(
					WP_PERSONIO_INTEGRATION_TRANSIENTS_LIST => array(
						'register_attributes' => array(
							'type'    => 'array',
							'default' => array(),
						),
						'do_not_export'       => true,
					),
					'personio_integration_update_slugs'   => array(
						'register_attributes' => array(
							'type'    => 'integer',
							'default' => 0,
						),
						'do_not_export'       => true,
					),
					'personioIntegrationLightInstallDate' => array(
						'register_attributes' => array(
							'type'    => 'integer',
							'default' => time(),
						),
						'do_not_export'       => true,
					),
					'personioIntegrationPositionScheduleInterval' => array(
						'register_attributes' => array(
							'type'    => 'string',
							'default' => 'daily',
						),
					),
					'personioIntegrationVersion'          => array(
						'register_attributes' => array(
							'type'    => 'string',
							'default' => WP_PERSONIO_INTEGRATION_VERSION,
						),
					),
					'personioIntegrationPageBuilder'      => array(
						'register_attributes' => array(
							'type'    => 'array',
							'default' => array(),
						),
						'do_not_export'       => true,
					),
				),
			),
		);
	}

	/**
	 * Register the settings.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		foreach ( $this->get_settings() as $section_settings ) {
			foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
				if ( ! isset( $field_settings['do_not_register'] ) ) {
					$args = array();
					if ( ! empty( $field_settings['register_attributes'] ) ) {
						unset( $field_settings['register_attributes']['default'] );
						$args = $field_settings['register_attributes'];
					}
					register_setting(
						$section_settings['settings_page'],
						$field_name,
						$args
					);
					add_filter( 'option_' . $field_name, array( $this, 'sanitize_option' ), 10, 2 );
				}
			}
		}
	}

	/**
	 * Register fields to manage the settings.
	 *
	 * @return void
	 */
	public function register_fields(): void {
		foreach ( $this->get_settings() as $section_name => $section_settings ) {
			if ( ! empty( $section_settings ) && ! empty( $section_settings['settings_page'] ) && ! empty( $section_settings['label'] ) && ! empty( $section_settings['callback'] ) ) {
				// bail if fields is empty and callback is just true.
				if ( empty( $section_settings['fields'] ) && '__return_true' === $section_settings['callback'] ) {
					continue;
				}

				$args = array();
				if ( isset( $section_settings['before_section'] ) ) {
					$args['before_section'] = $section_settings['before_section'];
				}
				if ( isset( $section_settings['after_section'] ) ) {
					$args['after_section'] = $section_settings['after_section'];
				}

				// add section.
				add_settings_section(
					$section_name,
					$section_settings['label'],
					$section_settings['callback'],
					$section_settings['settings_page'],
					$args
				);

				// add fields in this section.
				foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
					// get arguments for this field.
					$arguments = array(
						'label_for'         => $field_name,
						'fieldId'           => $field_name,
						'options'           => ! empty( $field_settings['options'] ) ? $field_settings['options'] : array(),
						'description'       => ! empty( $field_settings['description'] ) ? $field_settings['description'] : '',
						'placeholder'       => ! empty( $field_settings['placeholder'] ) ? $field_settings['placeholder'] : '',
						'pro_hint'          => ! empty( $field_settings['pro_hint'] ) ? $field_settings['pro_hint'] : '',
						'highlight'         => ! empty( $field_settings['highlight'] ) ? $field_settings['highlight'] : false,
						'readonly'          => ! empty( $field_settings['readonly'] ) ? $field_settings['readonly'] : false,
						'hide_empty_option' => ! empty( $field_settings['hide_empty_option'] ) ? $field_settings['hide_empty_option'] : false,
						'depends'           => ! empty( $field_settings['depends'] ) ? $field_settings['depends'] : array(),
						'class'             => ! empty( $field_settings['class'] ) ? $field_settings['class'] : array(),
					);

					/**
					 * Filter the arguments for this field.
					 *
					 * @param array $arguments List of arguments.
					 * @param array $field_settings Setting for this field.
					 * @param string $field_name Internal name of the field.
					 */
					$arguments = apply_filters( 'personio_integration_setting_field_arguments', $arguments, $field_settings, $field_name );

					// add the field.
					add_settings_field(
						$field_name,
						$field_settings['label'],
						$field_settings['field'],
						$section_settings['settings_page'],
						$section_name,
						$arguments
					);
				}
			}
		}
	}

	/**
	 * Register field callbacks.
	 *
	 * @return void
	 */
	public function register_field_callbacks(): void {
		foreach ( $this->get_settings() as $section_settings ) {
			if ( ! empty( $section_settings ) && ! empty( $section_settings['settings_page'] ) ) {
				if ( ! empty( $section_settings['fields'] ) ) {
					foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
						if ( ! empty( $field_settings['callback'] ) ) {
							add_filter( 'pre_update_option_' . $field_name, $field_settings['callback'], 10, 2 );
						}
					}
				}
			}
		}
	}

	/**
	 * Add settings-page for the plugin if setup has been completed.
	 *
	 * @return void
	 */
	public function add_settings_menu(): void {
		if ( Setup::get_instance()->is_completed() ) {
			$title = __( 'Personio Integration Light', 'personio-integration-light' );

			// add menu entry for settings page.
			add_submenu_page(
				PersonioPosition::get_instance()->get_link( true ),
				/**
				 * Filter for settings title.
				 *
				 * @since 3.0.0 Available since 3.0.0.
				 *
				 * @param string $title The title.
				 */
				apply_filters( 'personio_integration_settings_title', $title ) . ' ' . __( 'Settings', 'personio-integration-light' ),
				__( 'Settings', 'personio-integration-light' ),
				'manage_' . PersonioPosition::get_instance()->get_name(),
				'personioPositions',
				array( $this, 'add_settings_content' ),
				1
			);
		}
	}

	/**
	 * Create the admin-page with tab-navigation.
	 *
	 * @return void
	 */
	public function add_settings_content(): void {
		// check user capabilities.
		if ( ! current_user_can( 'manage_' . PersonioPosition::get_instance()->get_name() ) ) {
			return;
		}

		// get the active tab from the request-param.
		$tab = sanitize_text_field( wp_unslash( filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) );

		// set page to show.
		$page = 'personioIntegrationMainSettings';

		// hide the save button.
		$hide_save_button = false;

		// set callback to use.
		$callback = '';

		// output wrapper.
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<nav class="nav-tab-wrapper">
				<?php
				foreach ( $this->get_tabs() as $tab_settings ) {
					// bail if tab-settings are not an array.
					if ( ! is_array( $tab_settings ) ) {
						continue;
					}

					// bail if tab-settings are not for the settings-page.
					if ( 'personioPositions' !== $tab_settings['page'] ) {
						continue;
					}

					// bail if tab should be hidden.
					if ( ! empty( $tab_settings['hidden'] ) ) {
						if ( $tab === $tab_settings['key'] ) {
							$page = $tab_settings['settings_page'];
						}
						continue;
					}

					// Set url.
					$url    = Helper::get_settings_url( 'personioPositions', $tab_settings['key'] );
					$target = '_self';
					if ( ! empty( $tab_settings['url'] ) ) {
						$url = $tab_settings['url'];
						if ( ! empty( $tab_settings['url_target'] ) ) {
							$target = $tab_settings['url_target'];
						}
					}

					// Set class for tab and page for form-view.
					$class = '';
					if ( ! empty( $tab_settings['class'] ) ) {
						$class .= ' ' . $tab_settings['class'];
					}
					if ( $tab === $tab_settings['key'] ) {
						$class .= ' nav-tab-active';
						if ( ! empty( $tab_settings['settings_page'] ) ) {
							$page = $tab_settings['settings_page'];
						}
						if ( ! empty( $tab_settings['callback'] ) ) {
							$callback = $tab_settings['callback'];
							$page     = '';
						}
						if ( isset( $tab_settings['do_not_save'] ) ) {
							$hide_save_button = $tab_settings['do_not_save'];
						}
					}

					// decide which tab-type we want to output.
					if ( isset( $tab_settings['only_pro'] ) && false !== $tab_settings['only_pro'] ) {
						?>
						<span class="nav-tab" title="<?php echo esc_attr__( 'Only in Pro.', 'personio-integration-light' ); ?>"><?php echo esc_html( $tab_settings['label'] ); ?> <a class="pro-marker" href="<?php echo esc_url( Helper::get_pro_url() ); ?>" target="_blank">Pro <span class="dashicons dashicons-external"></span></a></span>
						<?php
					} elseif ( isset( $tab_settings['do_not_link'] ) && false !== $tab_settings['do_not_link'] ) {
						?>
						<span class="nav-tab"><?php echo esc_html( $tab_settings['label'] ); ?></span>
						<?php
					} else {
						?>
						<a href="<?php echo esc_url( $url ); ?>" class="nav-tab<?php echo esc_attr( $class ); ?>" target="<?php echo esc_attr( $target ); ?>"><?php echo esc_html( $tab_settings['label'] ); ?></a>
						<?php
					}
				}
				?>
			</nav>

			<div class="tab-content">
			<?php
			if ( ! empty( $page ) ) {
				// show errors.
				settings_errors();

				?>
					<form method="post" action="<?php echo esc_url( get_admin_url() ); ?>options.php" class="personio-integration-settings">
					<?php
					settings_fields( $page );
					do_settings_sections( $page );
					$hide_save_button ? '' : submit_button();
					?>
					</form>
					<?php
			}

			if ( ! empty( $callback ) ) {
				call_user_func( $callback );
			}
			?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return the settings and save them on the object.
	 *
	 * @return array
	 */
	public function get_settings(): array {
		$settings = $this->settings;

		/**
		 * Filter the plugin-settings.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param array $settings The settings as array.
		 */
		$this->settings = apply_filters( 'personio_integration_settings', $settings );

		// return the resulting settings.
		return $this->settings;
	}

	/**
	 * Return the value of a single actual setting.
	 *
	 * @param string $setting The requested setting as string.
	 *
	 * @return string
	 */
	public function get_setting( string $setting ): string {
		return get_option( $setting );
	}

	/**
	 * Return settings for single field.
	 *
	 * @param string $field The requested fiel.
	 * @param array  $settings The settings to use.
	 *
	 * @return array
	 */
	public function get_settings_for_field( string $field, array $settings = array() ): array {
		foreach ( ( empty( $settings ) ? $this->get_settings() : $settings ) as $section_settings ) {
			foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
				if ( $field === $field_name ) {
					return $field_settings;
				}
			}
		}
		return array();
	}

	/**
	 * Change settings depending on additional hooks.
	 *
	 * @return void
	 */
	public function change_settings(): void {
		$false = false;
		/**
		 * Hide the additional buttons for reviews or pro-version.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param array $false Set true to hide the buttons.
		 */
		if ( apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			add_filter( 'personio_integration_settings', array( $this, 'remove_pro_hints_from_settings' ) );
			add_filter( 'personio_integration_settings_tabs', array( $this, 'remove_pro_hints_from_tabs' ) );
			add_filter( 'personio_integration_personioposition_columns', array( $this, 'remove_pro_hints_from_columns' ) );
		}
	}

	/**
	 * Remove the pro hints in the settings.
	 *
	 * @param array $settings List of settings.
	 *
	 * @return array
	 */
	public function remove_pro_hints_from_settings( array $settings ): array {
		foreach ( $settings as $section_name => $section_settings ) {
			if ( ! empty( $section_settings['settings_page'] ) ) {
				foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
					if ( isset( $settings[ $section_name ]['fields'][ $field_name ]['pro_hint'] ) ) {
						unset( $settings[ $section_name ]['fields'][ $field_name ]['pro_hint'] );
					}
				}
			}
		}
		return $settings;
	}

	/**
	 * Return the tabs for the settings page.
	 *
	 * @return array
	 */
	public function get_tabs(): array {
		$tabs = $this->tabs;
		/**
		 * Filter the list of tabs.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param array $false Set true to hide the buttons.
		 */
		$tabs = apply_filters( 'personio_integration_settings_tabs', $tabs );

		// sort them by 'order'-field.
		usort( $tabs, array( $this, 'sort_tabs' ) );

		// return resulting list of tabs.
		return $tabs;
	}

	/**
	 * Sort the tabs by 'order'-field.
	 *
	 * @param array $a Tab 1 to check.
	 * @param array $b Tab 2 to compare with tab 1.
	 *
	 * @return int
	 */
	public function sort_tabs( array $a, array $b ): int {
		if ( empty( $a['order'] ) ) {
			$a['order'] = 500;
		}
		if ( empty( $b['order'] ) ) {
			$b['order'] = 500;
		}
		return $a['order'] - $b['order'];
	}

	/**
	 * Remove tabs with pro hints from tab-listing.
	 *
	 * @param array $tabs List of tabs.
	 *
	 * @return array
	 */
	public function remove_pro_hints_from_tabs( array $tabs ): array {
		// loop through the tabs and remove the pro hints.
		foreach ( $tabs as $tab_name => $setting ) {
			if ( isset( $setting['only_pro'] ) ) {
				unset( $tabs[ $tab_name ] );
			}
		}

		// return resulting list of tabs for settings.
		return $tabs;
	}

	/**
	 * Initialize the options of this plugin, set its default values.
	 *
	 * Only used during installation.
	 *
	 * @return void
	 */
	public function initialize_options(): void {
		$this->set_settings();
		foreach ( $this->get_settings() as $section_settings ) {
			foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
				if ( isset( $field_settings['register_attributes']['default'] ) && ! get_option( $field_name ) ) {
					add_option( $field_name, $field_settings['register_attributes']['default'], '', true );
				}
			}
		}
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
	 * Return whether a specific settings page is called.
	 *
	 * @param string $settings_page The requested settings page.
	 *
	 * @return bool
	 */
	public static function is_settings_page( string $settings_page ): bool {
		$tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// bail if no value is set.
		if ( is_null( $tab ) ) {
			return false;
		}

		// compare the values.
		return $tab === $settings_page;
	}

	/**
	 * Remove the sort column from positions table.
	 *
	 * @param array $columns List of columns.
	 *
	 * @return array
	 */
	public function remove_pro_hints_from_columns( array $columns ): array {
		unset( $columns['sort'] );
		return $columns;
	}

	/**
	 * Secure settings in DB.
	 *
	 * @param string $option The option which has been saved.
	 * @param mixed  $old_value The old value.
	 * @param mixed  $new_value The new value.
	 *
	 * @return void
	 */
	public function secure_settings( string $option, mixed $old_value, mixed $new_value ): void {
		// bail if updated option is 'personio_integration_settings'.
		if ( 'personio_integration_settings' === $option ) {
			return;
		}

		// bail if option is not part of our plugin.
		if ( false === stripos( $option, 'personio' ) ) {
			return;
		}

		// get settings.
		$settings = $this->get_settings();

		// remove the callbacks from settings.
		foreach ( $settings as $section_name => $section_settings ) {
			if ( ! empty( $section_settings['callback'] ) ) {
				unset( $settings[ $section_name ]['callback'] );
			}
			if ( ! empty( $section_settings['fields'] ) ) {
				foreach ( $section_settings['fields'] as $field_name => $field ) {
					if ( ! empty( $field['field'] ) ) {
						unset( $settings[ $section_name ]['fields'][ $field_name ]['field'] );
					}
					if ( ! empty( $field['callback'] ) ) {
						unset( $settings[ $section_name ]['fields'][ $field_name ]['callback'] );
					}
					if ( ! empty( $field['register_attributes']['sanitize_callback'] ) ) {
						unset( $settings[ $section_name ]['fields'][ $field_name ]['register_attributes']['sanitize_callback'] );
					}
				}
			}
		}

		// save complete settings in single option field.
		update_option( 'personio_integration_settings', $settings );

		// log this change if debug is enabled.
		if ( 1 === absint( get_option( 'personioIntegration_debug', 0 ) ) ) {
			// get settings for this option.
			$setting = $this->get_settings_for_field( $option );

			// bail if setting could not be found.
			if ( empty( $setting ) ) {
				return;
			}

			// bail if no label is set.
			if ( empty( $setting['label'] ) ) {
				return;
			}

			// get the user.
			$user      = wp_get_current_user();
			$user_name = __( 'Unknown', 'personio-integration-light' );
			if ( ! is_null( $user ) ) {
				$user_name = $user->display_name;
			}

			// finally lot it.
			$log = new Log();
			/* translators: $1%s will be replaced by the setting label, %2$s by the username, %3$s by the old value, %4$s by the new value. */
			$log->add_log( sprintf( __( 'Setting for <i>%1$s</i> has been changed by %2$s.<br>Old: %3$s<br>New: %4$s', 'personio-integration-light' ), esc_html( $setting['label'] ), esc_html( $user_name ), esc_html( $old_value ), esc_html( $new_value ) ), 'success', 'settings' );
		}
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
	 * Sanitize our own option values before output.
	 *
	 * @param mixed  $value The value.
	 * @param string $option The option-name.
	 *
	 * @return mixed
	 */
	public function sanitize_option( mixed $value, string $option ): mixed {
		// get field settings.
		$field_settings = $this->get_settings_for_field( $option, $this->settings );

		// bail if no type is set.
		if ( empty( $field_settings['register_attributes']['type'] ) ) {
			return $value;
		}

		// if type is array, secure for array.
		if ( 'array' === $field_settings['register_attributes']['type'] ) {
			// if it is an array, use it 1:1.
			if ( is_array( $value ) ) {
				return $value;
			}

			// secure the value.
			return (array) $value;
		}

		// if type is int, secure value for int.
		if ( 'integer' === $field_settings['register_attributes']['type'] ) {
			return absint( $value );
		}

		// return the value.
		return $value;
	}
}

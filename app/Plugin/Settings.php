<?php
/**
 * File to handle plugin-settings.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

		// register all settings for this plugin.
		add_action( 'init', array( $this, 'register_settings' ) );
		add_action( 'init', array( $this, 'change_settings' ) );

		// register fields to manage the settings.
		add_action( 'admin_init', array( $this, 'register_fields' ) );
		add_action( 'admin_init', array( $this, 'register_field_callbacks' ) );
		add_action( 'rest_api_init', array( $this, 'register_field_callbacks' ) );

		// register setting-actions.
		add_action( 'admin_action_personio_integration_export_settings', array( $this, 'export_settings' ) );
		add_action( 'wp_ajax_personio_integration_settings_import_file', array( $this, 'import_settings' ) );

		// add admin-menu.
		add_action( 'admin_menu', array( $this, 'add_settings_menu' ) );
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
				'label' => __( 'Basic Settings', 'personio-integration-light' ),
				'key'   => '',
				'page'  => 'personioIntegrationPositions',
			),
			array(
				'label' => __( 'Templates', 'personio-integration-light' ),
				'key'   => 'templates',
				'page'  => 'personioIntegrationPositionsTemplates',
			),
			array(
				'label' => __( 'Import', 'personio-integration-light' ),
				'key'   => 'import',
				'page'  => 'personioIntegrationPositionsImport',
			),
			array(
				'label'    => __( 'Applications, SEO & more', 'personio-integration-light' ),
				'key' => 'use_pro',
				'only_pro' => true,
			),
			array(
				'label' => __( 'Advanced', 'personio-integration-light' ),
				'key'   => 'advanced',
				'page'  => 'personioIntegrationPositionsAdvanced',
			),
			array(
				'label'    => __( 'Logs', 'personio-integration-light' ),
				'key'      => 'logs',
				'callback' => array( 'PersonioIntegrationLight\Plugin\Admin\Logs', 'show' ),
			),
			array(
				'label'    => '&nbsp;',
				'key'      => 'copyright',
				'callback' => array( $this, 'show_copyright' ),
				'class' => 'copyright'
			),
			array(
				'label' => __( 'Questions? Check our forum!', 'personio-integration-light' ),
				'key'   => 'help',
				'url'   => Helper::get_plugin_support_url(),
				'class' => 'nav-tab-help nav-tab-active',
			),
		);

		// reset tabs if Personio URL is not set.
		if ( ! Helper::is_personio_url_set() ) {
			$this->tabs = array(
				array(
					'label' => __( 'General Settings', 'personio-integration-light' ),
					'key'   => '',
					'page'  => 'personioIntegrationPositions',
				),
				array(
					'label'       => __( 'Enter Personio URL to get more options', 'personio-integration-light' ),
					'key' => 'enter_url',
					'do_not_link' => true,
				),
			);
		}

		// get taxonomies.
		$list_template_filter = array();
		$list_excerpt = array();
		$detail_excerpt = array();
		if ( Helper::is_personio_url_set() ) {
			$list_template_filter = Taxonomies::get_instance()->get_labels_for_settings( get_option( 'personioIntegrationTemplateFilter' ) );
			$list_excerpt         = Taxonomies::get_instance()->get_labels_for_settings( get_option( 'personioIntegrationTemplateExcerptDefaults' ) );
			$detail_excerpt       = Taxonomies::get_instance()->get_labels_for_settings( get_option( 'personioIntegrationTemplateExcerptDetail' ) );
		}

		// get editor URL.
		$editor_url = add_query_arg(
			array(
				'path' => '/wp_template/all',
			),
			admin_url( 'site-editor.php' )
		);

		// define settings for this plugin.
		$this->settings = array(
			'settings_section_main'            => array(
				'label'    => __( 'General Settings', 'personio-integration-light' ),
				'page'     => 'personioIntegrationPositions',
				'callback' => '__return_true',
				'fields'   => array(
					'personioIntegrationUrl'              => array(
						'label'               => __( 'Personio URL', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Text', 'get' ),
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
						'callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\PersonioIntegrationUrl', 'save' )
					),
					WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE => array(
						'label'               => __( 'Main language', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Radios', 'get' ),
						'description'         => __( 'Set the main language you will use for your open positions.', 'personio-integration-light' ),
						'options'             => Languages::get_instance()->get_languages(),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'show_in_rest'      => true,
							'sanitize_callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\MainLanguage', 'validate' ),
							'type'              => 'string',
							'default'             => Languages::get_instance()->get_current_lang(),
						),
					),
					WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION => array(
						'label'               => __( 'Used languages', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Checkboxes', 'get' ),
						'options'             => Languages::get_instance()->get_languages(),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'default'             => array( Languages::get_instance()->get_current_lang() => 1 ),
							'sanitize_callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\Languages', 'validate' ),
							'type'              => 'array',
						),
						/* translators: %1$s is replaced with the name of the Pro-plugin */
						'pro_hint'            => __( 'Use all languages supported by Personio with %s.', 'personio-integration-light' ),
					),
				),
			),
			'settings_section_template_list'   => array(
				'label'    => __( 'List View', 'personio-integration-light' ),
				'page'     => 'personioIntegrationPositionsTemplates',
				'callback' => '__return_true',
				'fields'   => array(
					'personio_integration_fse_theme_hint'                      => array(
						'label'       => '',
						'field'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\TextHints', 'get' ),
						/* translators: %1$s will be replaced with the name of the theme, %2$s will be replaced by the URL for the editor */
						'description' => sprintf( __( 'You are using with <i>%1$s</i> a modern block theme. The settings here will therefore might not work. Edit the archive- and single-template under <a href="%2$s">Appearance > Editor > Templates > Manage</a>.', 'personio-integration-light' ), esc_html( Helper::get_theme_title() ), esc_url( $editor_url ) ),
						'highlight'   => true,
					),
					'personioIntegrationEnableFilter'     => array(
						'label'               => __( 'Enable filter on list-view', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => 0,
						),
					),
					'personioIntegrationTemplateFilter'   => array(
						'label'               => __( 'Available filter for details', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Select', 'get' ),
						'options'             => $list_template_filter,
						'description'         => __( 'Mark multiple default filter for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						/* translators: %1$s is replaced with "string" */
						'pro_hint'            => __( 'Sort this list with %s.', 'personio-integration-light' ),
						'register_attributes' => array(
							'type' => 'array',
							'default'             => array( 'recruitingCategory', 'schedule', 'office' ),
						),
						'depends' => array(
							'personioIntegrationEnableFilter' => 1
						)
					),
					'personioIntegrationFilterType'       => array(
						'label'       => __( 'Choose filter-type', 'personio-integration-light' ),
						'field'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						'options'     => Helper::get_filter_types(),
						'description' => __( 'This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'    => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'string',
							'default'     => 'linklist',
						),
						'depends' => array(
							'personioIntegrationEnableFilter' => 1
						)
					),
					'personioIntegrationTemplateContentListingTemplate' => array(
						'label'       => __( 'Choose template for listing', 'personio-integration-light' ),
						'field'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description' => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'options'     => Templates::get_instance()->get_archive_templates(),
						'readonly'    => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'string',
							'default'     => 'default',
						)
					),
					'personioIntegrationTemplateContentList' => array(
						'label'       => __( 'Choose templates for positions in list-view', 'personio-integration-light' ),
						'field'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Select', 'get' ),
						'options'     => Templates::get_instance()->get_template_labels(),
						'description' => __( 'Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'    => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'array',
							'default'     => array( 'title', 'excerpt' ),
						)
					),
					'personioIntegrationTemplateListingExcerptsTemplate' => array(
						'label'       => __( 'Choose template for details in list-view', 'personio-integration-light' ),
						'field'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description' => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'options'     => Templates::get_instance()->get_excerpts_templates(),
						'readonly'    => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'string',
							'default'     => 'default',
						)
					),
					'personioIntegrationTemplateExcerptDefaults' => array(
						'label'       => __( 'Choose details for positions in list-view', 'personio-integration-light' ),
						'field'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Select', 'get' ),
						'options'     => $list_excerpt,
						'description' => __( 'Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'    => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'array',
							'default'     => array( 'recruitingCategory', 'schedule', 'office' ),
						)
					),
					'personioIntegrationTemplateListingContentTemplate' => array(
						'label'       => __( 'Choose template for content in list-view', 'personio-integration-light' ),
						'field'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description' => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'options'     => Templates::get_instance()->get_jobdescription_templates(),
						'readonly'    => ! Helper::is_personio_url_set(),
						'default'     => 'default',
						'register_attributes' => array(
							'type' => 'string',
							'default'     => 'default',
						)
					),
					'personioIntegrationEnableLinkInList' => array(
						'label'               => __( 'Enable link to single on list-view', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => 1,
						),
					),
				),
			),
			'settings_section_template_detail' => array(
				'label'    => __( 'Single View', 'personio-integration-light' ),
				'page'     => 'personioIntegrationPositionsTemplates',
				'callback' => '__return_true',
				'fields'   => array(
					'personioIntegrationTemplateContentDefaults' => array(
						'label'       => __( 'Choose templates', 'personio-integration-light' ),
						'field'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Select', 'get' ),
						'options'     => Templates::get_instance()->get_template_labels(),
						'description' => __( 'Mark multiple default templates for each detail-view of single positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'    => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'array',
							'default'     => array( 'title', 'content', 'formular' ),
						)
					),
					'personioIntegrationTemplateDetailsExcerptsTemplate' => array(
						'label'       => __( 'Choose template for details in details-view', 'personio-integration-light' ),
						'field'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description' => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'options'     => Templates::get_instance()->get_excerpts_templates(),
						'readonly'    => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'string',
							'default'     => 'default',
						)
					),
					'personioIntegrationTemplateExcerptDetail' => array(
						'label'       => __( 'Choose details', 'personio-integration-light' ),
						'field'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Select', 'get' ),
						'options'     => $detail_excerpt,
						'description' => __( 'Mark multiple details for single-view of positions. Only used if template "detail" is enabled for detail-view. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'    => ! Helper::is_personio_url_set(),
						/* translators: %1$s is replaced with "string" */
						'pro_hint'    => __( 'Sort this list with %s.', 'personio-integration-light' ),
						'register_attributes' => array(
							'type' => 'array',
							'default'     => array( 'recruitingCategory', 'schedule', 'office' ),
						)
					),
					'personioIntegrationTemplateJobDescription' => array(
						'label'       => __( 'Choose job description template in details-view', 'personio-integration-light' ),
						'field'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						'options'     => Templates::get_instance()->get_jobdescription_templates(),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description' => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'readonly'    => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'string',
							'default'     => 'default',
						)
					),
					'personioIntegrationTemplateBackToListButton' => array(
						'label'               => __( 'Enable back to list-link', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => 0,
						),
					),
					'personioIntegrationTemplateBackToListUrl' => array(
						'label'               => __( 'URL for back to list-link', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Text', 'get' ),
						/* translators: %1$s will be replaced by the list-slug */
						'description'         => sprintf( __( 'If empty the link will be set to list-slug <a href="%1$s">%1$s</a>.', 'personio-integration-light' ), esc_url( trailingslashit( get_home_url() ) . Helper::get_archive_slug() ) ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'string',
							'default'             => '',
						),
						'depends' => array(
							'personioIntegrationTemplateBackToListButton' => 1
						)
					),
				),
			),
			'settings_section_template_other'  => array(
				'label'    => __( 'Other settings', 'personio-integration-light' ),
				'page'     => 'personioIntegrationPositionsTemplates',
				'callback' => '__return_true',
				'fields'   => array(
					'personioIntegrationTemplateExcerptSeparator' => array(
						'label'    => __( 'Separator for details-listing', 'personio-integration-light' ),
						'field' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Text', 'get' ),
						'readonly' => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'string',
							'default'             => ', ',
						),
					),
				),
			),
			'settings_section_advanced'        => array(
				'label'    => __( 'Advanced settings', 'personio-integration-light' ),
				'page'     => 'personioIntegrationPositionsAdvanced',
				'callback' => '__return_true',
				'fields'   => array(
					'personioIntegration_advanced_pro_hint' => array(
						'label'    => '',
						'field' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\ProHint', 'get' ),
						/* translators: %1$s will be replaced with the plugin Pro name */
						'pro_hint' => __( 'With %1$s you get more advanced options, e.g. to change the URL of archives with positions.', 'personio-integration-light' ),
					),
					'personioIntegrationExtendSearch'      => array(
						'label'               => __( 'Note the position-keywords in search in frontend', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => 1,
						),
					),
					'personioIntegrationMaxAgeLogEntries'  => array(
						'label'    => __( 'max. Age for log entries in days', 'personio-integration-light' ),
						'field' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Number', 'get' ),
						'readonly' => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => 50,
						),
					),
					'personioIntegrationUrlTimeout'        => array(
						'label'               => __( 'Timeout for URL-request in Seconds', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Number', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'sanitize_callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\UrlTimeout', 'validate' ),
							'type'              => 'integer',
							'default'             => 30,
						),
					),
					'personioIntegrationDeleteOnUninstall' => array(
						'label'               => __( 'Delete all imported data on uninstall', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => 1,
						),
					),
					'personioIntegrationResetIntro' => array(
						'label'    => __( 'Reset intro', 'personio-integration-light' ),
						'field' => array( 'PersonioIntegrationLight\Plugin\Intro', 'show_reset_button' ),
					),
					'personioIntegrationImportSettings' => array(
						'label'    => __( 'Import settings', 'personio-integration-light' ),
						'field' => array( $this, 'show_import_button' ),
					),
					'personioIntegrationExportSettings' => array(
						'label'    => __( 'Export settings', 'personio-integration-light' ),
						'field' => array( $this, 'show_export_button' ),
					),
					'personioIntegration_debug'            => array(
						'label'               => __( 'Debug-Mode', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => 0,
						),
					),
				),
			),
			'settings_section_import'          => array(
				'label'    => __( 'Import of positions from Personio', 'personio-integration-light' ),
				'page'     => 'personioIntegrationPositionsImport',
				'callback' => '__return_true',
				'fields'   => array(
					'personioIntegrationImportNow' => array(
						'label'    => __( 'Get open positions from Personio', 'personio-integration-light' ),
						'field' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\ImportPositions', 'get' ),
					),
					'personioIntegrationDeleteNow' => array(
						'label'    => __( 'Delete local positions', 'personio-integration-light' ),
						'field' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\DeletePositions', 'get' ),
					),
					'personioIntegrationEnablePositionSchedule' => array(
						'label'               => __( 'Enable automatic import', 'personio-integration-light' ),
						'field'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'description' => __( 'The automatic import is run once per day. You don\'t have to worry about updating your jobs on the website yourself.', 'personio-integration-light' ),
						'pro_hint'            => __( 'Use more import options with the %s. Among other things, you get the possibility to change the time interval for imports and partial imports of very large position lists.', 'personio-integration-light' ),
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => 1,
						),
						'callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\Import', 'save' )
					),
				),
			),
			'hidden_section'                   => array(
				'fields' => array(
					'wp_easy_setup_completed'         => array(
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => 0,
						),
					),
					'personio_integration_transients' => array(
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => 0,
						),
						'do_not_export' => true
					),
					'personio-integration-intro' => array(
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => 0,
						),
					),
					'personio_integration_update_slugs' => array(
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => 0,
						),
						'do_not_export' => true
					),
					'personioIntegrationLightInstallDate' => array(
						'register_attributes' => array(
							'type' => 'integer',
							'default'             => time(),
						),
						'do_not_export' => true
					)
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
			if ( ! empty( $section_settings['page'] ) ) {
				foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
					if( ! isset($field_settings['do_not_register']) ) {
						$args = array();
						if( ! empty( $field_settings['register_attributes'] ) ) {
							unset($field_settings['register_attributes']['default']);
							$args = $field_settings['register_attributes'];
						}
						register_setting(
							$section_settings['page'],
							$field_name,
							$args
						);
					}
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
			if ( ! empty( $section_settings ) && ! empty( $section_settings['page'] ) && ! empty( $section_settings['label'] ) && ! empty( $section_settings['callback'] ) ) {
				$args = array();
				if( isset( $section_settings['before_section'] ) ) {
					$args['before_section'] = $section_settings['before_section'];
				}
				if( isset( $section_settings['after_section'] ) ) {
					$args['after_section'] = $section_settings['after_section'];
				}

				// add section.
				add_settings_section(
					$section_name,
					$section_settings['label'],
					$section_settings['callback'],
					$section_settings['page'],
					$args
				);

				// add fields in this section.
				if ( ! empty( $section_settings['fields'] ) ) {
					foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
						// get arguments for this field.
						$arguments = array(
							'label_for'   => $field_name,
							'fieldId'     => $field_name,
							'options'     => ! empty( $field_settings['options'] ) ? $field_settings['options'] : array(),
							'description' => ! empty( $field_settings['description'] ) ? $field_settings['description'] : '',
							'placeholder' => ! empty( $field_settings['placeholder'] ) ? $field_settings['placeholder'] : '',
							'pro_hint'    => ! empty( $field_settings['pro_hint'] ) ? $field_settings['pro_hint'] : '',
							'highlight'   => ! empty( $field_settings['highlight'] ) ? $field_settings['highlight'] : false,
							'readonly'    => ! empty( $field_settings['readonly'] ) ? $field_settings['readonly'] : false,
							'hide_empty_option' => ! empty( $field_settings['hide_empty_option'] ) ? $field_settings['hide_empty_option'] : false,
							'depends' => ! empty( $field_settings['depends'] ) ? $field_settings['depends'] : array(),
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
							$section_settings['page'],
							$section_name,
							$arguments
						);
					}
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
			if ( ! empty( $section_settings ) && ! empty( $section_settings['page'] ) && ! empty( $section_settings['label'] ) && ! empty( $section_settings['callback'] ) ) {
				// add fields in this section.
				if ( ! empty( $section_settings['fields'] ) ) {
					foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
						if( !empty( $field_settings['callback'] ) ) {
							add_filter( 'pre_update_option_'.$field_name, $field_settings['callback'], 10, 2 );
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

			// add menu entry for applications (with hint to pro).
			$false = false;
			/**
			 * Hide the additional the sort column which is only filled in Pro.
			 *
			 * @since 3.0.0 Available since 3.0.0
			 *
			 * @param array $false Set true to hide the buttons.
			 */
			if( ! apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
				add_submenu_page(
					PersonioPosition::get_instance()->get_link( true ),
					__( 'Personio Integration Light', 'personio-integration-light' ) . ' ' . __( 'Settings', 'personio-integration-light' ),
					__( 'Applications', 'personio-integration-light' ),
					'manage_' . PersonioPosition::get_instance()->get_name(),
					'#',
					false,
					2
				);
			}
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
		$page = 'personioIntegrationPositions';

		// set callback to use.
		$callback = '';

		// output wrapper.
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<nav class="nav-tab-wrapper">
				<?php
				foreach ( $this->get_tabs() as $tab_settings ) {
					// bail if settings are not an array.
					if( ! is_array( $tab_settings ) ) {
						continue;
					}

					// Set url.
					$url    = Helper::get_settings_url( $tab_settings['key'] );
					$target = '_self';
					if ( ! empty( $tab_settings['url'] ) ) {
						$url    = $tab_settings['url'];
						$target = '_blank';
					}

					// Set class for tab and page for form-view.
					$class = '';
					if ( ! empty( $tab_settings['class'] ) ) {
						$class .= ' ' . $tab_settings['class'];
					}
					if ( $tab === $tab_settings['key'] ) {
						$class .= ' nav-tab-active';
						if ( ! empty( $tab_settings['page'] ) ) {
							$page = $tab_settings['page'];
						}
						if ( ! empty( $tab_settings['callback'] ) ) {
							$callback = $tab_settings['callback'];
							$page     = '';
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
					submit_button();
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
	 * Return the settings.
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
		return apply_filters( 'personio_integration_settings', $settings );
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
	 *
	 * @return array
	 */
	public function get_settings_for_field( string $field ): array {
		foreach ( $this->get_settings() as $section_settings ) {
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
		if( apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			add_filter( 'personio_integration_settings', array( $this, 'remove_pro_hints_from_settings' ) );
			add_filter( 'personio_integration_settings_tabs', array( $this, 'remove_pro_hints_from_tabs' ) );
		}
	}

	/**
	 * Remove the pro hints in the settings.
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function remove_pro_hints_from_settings( array $settings ): array {
		foreach ( $settings as $section_name => $section_settings ) {
			if ( ! empty( $section_settings['page'] ) ) {
				foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
					if( isset($settings[$section_name]['fields'][$field_name]['pro_hint']) ) {
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
	private function get_tabs(): array {
		$tabs = $this->tabs;

		/**
		 * Filter the list of tabs.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param array $false Set true to hide the buttons.
		 */
		return apply_filters( 'personio_integration_settings_tabs', $tabs );
	}

	/**
	 * Remove tabs with pro hints from tab-listing.
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function remove_pro_hints_from_tabs( array $tabs ): array {
		// loop through the tabs and remove the pro hints.
		foreach( $tabs as $tab_name => $setting ) {
			if( isset($setting['only_pro']) ) {
				unset( $tabs[$tab_name]);
			}
		}

		// return resulting list of tabs for settings.
		return $tabs;
	}

	/**
	 * Initialize the options of this plugin, set its default values.
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
	 * Show import button.
	 *
	 * @return void
	 */
	public function show_import_button(): void {
		// define import-dialog.
		$dialog = array(
			'title' => __( 'Import settings', 'personio-integration-light' ),
			'texts' => array(
				'<p>' . __( 'Uploading a new configuration overwrites all current settings.<br>Imported positions are not affected by this.', 'personio-integration-light' ) . '</p>',
				'<label for="import_settings_file">'.__( 'Choose file to import:', 'personio-integration-light' ).'</label>',
				'<input type="file" id="import_settings_file" name="import_settings_file" accept="application/json">'
			),
			'buttons' => array(
				array(
					'action' => 'personio_integration_import_settings_file();',
					'variant' => 'primary',
					'text' => __( 'Import file', 'personio-integration-light' )
				),
				array(
					'action' => 'closeDialog();',
					'variant' => 'primary',
					'text' => __( 'Cancel', 'personio-integration-light' )
				),
			),
		);

		// output button.
		?><a href="" class="button button-primary wp-easy-dialog" data-dialog="<?php echo esc_attr( wp_json_encode($dialog) ); ?>"><?php echo esc_html__( 'Import settings', 'personion-integration-light' ); ?></a><?php
	}

	/**
	 * Show export button.
	 *
	 * @return void
	 */
	public function show_export_button(): void {
		// define download-URL.
		$download_url = add_query_arg(
			array(
				'action' => 'personio_integration_export_settings',
				'nonce' => wp_create_nonce( 'personio-integration-export-settings' )
			),
			get_admin_url() . 'admin.php'
		);

		// define export-dialog.
		$dialog = array(
			'title' => __( 'Export settings', 'personio-integration-light' ),
			'texts' => array(
				'<p>'.__( 'Click on the button to download an export of all actual settings in this plugin.', 'personio-integration-light' ).'</p>'
			),
			'buttons' => array(
				array(
					'action' => 'location.href="'.$download_url.'";closeDialog();',
					'variant' => 'primary',
					'text' => __( 'Download', 'personio-integration-light' )
				),
				array(
					'action' => 'closeDialog();',
					'variant' => 'primary',
					'text' => __( 'Cancel', 'personio-integration-light' )
				),
			),
		);

		// output button.
		?><a href="" class="button button-primary wp-easy-dialog" data-dialog="<?php echo esc_attr( wp_json_encode($dialog) ); ?>"><?php echo esc_html__( 'Export settings', 'personion-integration-light' ); ?></a><?php
	}

	/**
	 * Export actual settings as JSON-file.
	 *
	 * @return void
	 */
	public function export_settings(): void {
		// check for nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'personio-integration-export-settings' ) ) {
			return;
		}

		// get settings.
		$settings_list = array();
		foreach( $this->get_settings() as $section_settings ) {
			foreach( $section_settings['fields'] as $field_name => $field_settings ) {
				if( ! empty( $field_settings['register_attributes']) && empty( $field_settings['do_not_export'] ) )  {
					$settings_list[ $field_name ] = get_option( $field_name );
				}
			}
		}

		// create filename for JSON-download-file.
		$filename = gmdate( 'YmdHi' ) . '_' . get_option( 'blogname' ) . '_Personio_Integration_Light_Settings.json';
		/**
		 * File the filename for JSON-download of all settings.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $filename The generated filename.
		 */
		$filename = apply_filters( 'personio_integration_settings_export_filename', $filename );

		// set header for response as JSON-download.
		header( 'Content-type: application/json' );
		header( 'Content-Disposition: attachment; filename=' . sanitize_file_name( $filename ) );
		echo wp_json_encode( $settings_list );
		exit;
	}

	/**
	 * Import settings file via AJAX.
	 *
	 * @return void
	 */
	public function import_settings(): void {
		// check nonce.
		check_ajax_referer( 'personio-integration-settings-import-file', 'nonce' );

		// bail if file has no size.
		if( 0 === $_FILES['file']['size'] ) {
			wp_send_json( array( 'html' => __( 'The uploaded file is no size.', 'personio-integration-light' ) ) );
		}

		// bail if file type is not JSON.
		if( 'application/json' !== $_FILES['file']['type'] ) {
			wp_send_json( array( 'html' => __( 'The uploaded file is not a valid JSON-file.', 'personio-integration-light' ) ) );
		}

		// allow JSON-files.
		add_filter('upload_mimes', array( $this, 'allow_json' ) );

		// bail if file type is not JSON.
		$filetype = wp_check_filetype( $_FILES['file']['name'] );
		if( 'json' !== $filetype['ext'] ) {
			wp_send_json( array( 'html' => __( 'The uploaded file does not have the file extension <i>.json</i>.', 'personio-integration-light' ) ) );
		}

		// bail if uploaded file is not readable.
		if( ! file_exists( $_FILES['file']['tmp_name'] ) ) {
			wp_send_json( array( 'html' => __( 'The uploaded file could not be saved. Contact your hoster about this problem.', 'personio-integration-light' ) ) );
		}

		// read the file.
		$file_content = file_get_contents( $_FILES['file']['tmp_name'] );

		// convert JSON to array.
		$settings_array = json_decode( $file_content, ARRAY_A );

		// import the settings.
		foreach( $settings_array as $field_name => $field_value ) {
			update_option( $field_name, $field_value );
		}

		// return "all ok".
		wp_send_json( array( 'html' => __( 'Import has been successfully run.', 'personio-integration-light' ) ) );
	}

	/**
	 * Allow SVG as file-type.
	 *
	 * @param array $file_types
	 *
	 * @return array
	 */
	public function allow_json( array $file_types ): array {
		$new_filetypes = array();
		$new_filetypes['json'] = 'application/json';
		return array_merge( $file_types, $new_filetypes );
	}

	/**
	 * Show copyright hints.
	 *
	 * @return void
	 */
	public function show_copyright(): void {
		?><div class="wrap"><?php
		echo Helper::get_logo_img( true );
		?><p>The Personio logo as part of all distributed icons is a trademark of <a href="https://www.personio.de/">Personio SE & Co. KG</a>.</p><?php
		?></div><?php
	}
}

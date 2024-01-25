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
use PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl;

/**
 * Initialize this plugin.
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

		// add admin-menu.
		add_action( 'admin_menu', array( $this, 'add_settings_menu' ) );

		// cleanup setting.
		add_filter( 'pre_update_option_personioIntegrationUrl', array( $this, 'cleanup_personio_url_setting' ) );
	}

	/**
	 * Define ALL settings.
	 *
	 * @return void
	 */
	public function set_settings(): void {
		// set tabs.
		$this->tabs = array(
			'general'   => array(
				'label' => __( 'General Settings', 'personio-integration-light' ),
				'key'   => '',
				'page'  => 'personioIntegrationPositions',
			),
			'templates' => array(
				'label' => __( 'Templates', 'personio-integration-light' ),
				'key'   => 'templates',
				'page'  => 'personioIntegrationPositionsTemplates',
			),
			'import'    => array(
				'label' => __( 'Import', 'personio-integration-light' ),
				'key'   => 'import',
				'page'  => 'personioIntegrationPositionsImport',
			),
			'pro_hint'  => array(
				'label'    => __( 'Applications, SEO & more', 'personio-integration-light' ),
				'only_pro' => true,
			),
			'advanced'  => array(
				'label' => __( 'Advanced', 'personio-integration-light' ),
				'key'   => 'advanced',
				'page'  => 'personioIntegrationPositionsAdvanced',
			),
			'logs'      => array(
				'label'    => __( 'Logs', 'personio-integration-light' ),
				'key'      => 'advanced',
				'callback' => array( 'PersonioIntegrationLight\Plugin\Admin\Logs', 'show' ),
			),
			'help'      => array(
				'label' => __( 'Questions? Check our forum!', 'personio-integration-light' ),
				'key'   => 'help',
				'url'   => Helper::get_plugin_support_url(),
				'class' => 'nav-tab-help nav-tab-active',
			),
		);

		// reset tabs if Personio URL is not set.
		if ( ! Helper::is_personio_url_set() ) {
			$this->tabs = array(
				'general' => array(
					'label' => __( 'General Settings', 'personio-integration-light' ),
					'key'   => '',
					'page'  => 'personioIntegrationPositions',
				),
				'go_pro'  => array(
					'label'       => __( 'Enter Personio URL to get more options', 'personio-integration-light' ),
					'do_not_link' => true,
				),
			);
		}

		// get languages.
		$languages_obj = Languages::get_instance();
		$language_name = $languages_obj->get_main_language();

		// get taxonomies.
		$labels         = Taxonomies::get_instance()->get_taxonomy_labels_for_settings();
		$default_filter = get_option( 'personioIntegrationTemplateFilter', array() );

		/**
		 * Filter the taxonomy labels before adding them to the settings.
		 *
		 * @since 2.3.0 Available since 2.3.0.
		 *
		 * @param array $labels List of labels.
		 * @param array $default_filter List of default filters.
		 */
		$filter = apply_filters( 'personio_integration_settings_get_list', $labels, $default_filter );

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
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Text', 'get' ),
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
						'default'             => '',
					),
					WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION => array(
						'label'               => __( 'Used languages', 'personio-integration-light' ),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Checkboxes', 'get' ),
						'options'             => Languages::get_instance()->get_languages(),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'sanitize_callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\Languages', 'validate' ),
							'type'              => 'array',
						),
						/* translators: %1$s is replaced with the name of the Pro-plugin */
						'pro_hint'            => __( 'Use all languages supported by Personio with %s.', 'personio-integration-light' ),
						'default'             => array( $language_name => 1 ),
					),
					WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE => array(
						'label'               => __( 'Main language', 'personio-integration-light' ),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Multiple_Radios', 'get' ),
						'description'         => __( 'Set the main language you will use for your open positions.', 'personio-integration-light' ),
						'options'             => Languages::get_instance()->get_languages(),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'show_in_rest'      => true,
							'sanitize_callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\MainLanguage', 'validate' ),
							'type'              => 'string',
						),
						'default'             => Languages::get_instance()->get_current_lang(),
					),
				),
			),
			'settings_section_template_list'   => array(
				'label'    => __( 'List View', 'personio-integration-light' ),
				'page'     => 'personioIntegrationPositionsTemplates',
				'callback' => '__return_true',
				'fields'   => array(
					'fse_theme_hint'                      => array(
						'label'       => '',
						'callback'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\TextHints', 'get' ),
						/* translators: %1$s will be replaced with the name of the theme, %2$s will be replaced by the URL for the editor */
						'description' => sprintf( __( 'You are using with <i>%1$s</i> a modern block theme. The settings here will therefore might not work. Edit the archive- and single-template under <a href="%2$s">Appearance > Editor > Templates > Manage</a>.', 'personio-integration-light' ), esc_html( Helper::get_theme_title() ), esc_url( $editor_url ) ),
						'highlight'   => true,
					),
					'personioIntegrationEnableFilter'     => array(
						'label'               => __( 'Enable filter on list-view', 'personio-integration-light' ),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
						),
						'default'             => 0,
					),
					'personioIntegrationTemplateFilter'   => array(
						'label'               => __( 'Available filter for details', 'personio-integration-light' ),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\MultiSelect', 'get' ),
						'options'             => $filter,
						'description'         => __( 'Mark multiple default filter for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						/* translators: %1$s is replaced with "string" */
						'pro_hint'            => __( 'Sort this list with %s.', 'personio-integration-light' ),
						'register_attributes' => array(
							'type' => 'integer',
						),
						'default'             => array( 'recruitingCategory', 'schedule', 'office' ),
					),
					'personioIntegrationFilterType'       => array(
						'label'       => __( 'Choose filter-type', 'personio-integration-light' ),
						'callback'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						'options'     => Helper::get_filter_types(),
						'description' => __( 'This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'    => ! Helper::is_personio_url_set(),
						'default'     => 'linklist',
					),
					'personioIntegrationTemplateContentListingTemplate' => array(
						'label'       => __( 'Choose template for listing', 'personio-integration-light' ),
						'callback'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description' => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'options'     => Templates::get_instance()->get_archive_templates(),
						'readonly'    => ! Helper::is_personio_url_set(),
						'default'     => 'default',
					),
					'personioIntegrationTemplateContentList' => array(
						'label'       => __( 'Choose templates for positions in list-view', 'personio-integration-light' ),
						'callback'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\MultiSelect', 'get' ),
						'options'     => Templates::get_instance()->get_template_labels(),
						'description' => __( 'Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'    => ! Helper::is_personio_url_set(),
						'default'     => array( 'title', 'excerpt' ),
					),
					'personioIntegrationTemplateListingExcerptsTemplate' => array(
						'label'       => __( 'Choose template for details in list-view', 'personio-integration-light' ),
						'callback'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description' => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'options'     => Templates::get_instance()->get_excerpts_templates(),
						'readonly'    => ! Helper::is_personio_url_set(),
						'default'     => 'default',
					),
					'personioIntegrationTemplateExcerptDefaults' => array(
						'label'       => __( 'Choose details for positions in list-view', 'personio-integration-light' ),
						'callback'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\MultiSelect', 'get' ),
						'options'     => $filter,
						'description' => __( 'Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'    => ! Helper::is_personio_url_set(),
						'default'     => array( 'recruitingCategory', 'schedule', 'office' ),
					),
					'personioIntegrationTemplateListingContentTemplate' => array(
						'label'       => __( 'Choose template for content in list-view', 'personio-integration-light' ),
						'callback'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description' => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'options'     => Templates::get_instance()->get_jobdescription_templates(),
						'readonly'    => ! Helper::is_personio_url_set(),
						'default'     => 'default',
					),
					'personioIntegrationEnableLinkInList' => array(
						'label'               => __( 'Enable link to single on list-view', 'personio-integration-light' ),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
						),
						'default'             => 1,
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
						'callback'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\MultiSelect', 'get' ),
						'options'     => Templates::get_instance()->get_template_labels(),
						'description' => __( 'Mark multiple default templates for each detail-view of single positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'    => ! Helper::is_personio_url_set(),
						'default'     => array( 'title', 'content', 'formular' ),
					),
					'personioIntegrationTemplateDetailsExcerptsTemplate' => array(
						'label'       => __( 'Choose template for details in details-view', 'personio-integration-light' ),
						'callback'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description' => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'options'     => Templates::get_instance()->get_excerpts_templates(),
						'readonly'    => ! Helper::is_personio_url_set(),
						'default'     => 'default',
					),
					'personioIntegrationTemplateExcerptDetail' => array(
						'label'       => __( 'Choose details', 'personio-integration-light' ),
						'callback'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\MultiSelect', 'get' ),
						'options'     => $filter,
						'description' => __( 'Mark multiple details for single-view of positions. Only used if template "detail" is enabled for detail-view. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'    => ! Helper::is_personio_url_set(),
						/* translators: %1$s is replaced with "string" */
						'pro_hint'    => __( 'Sort this list with %s.', 'personio-integration-light' ),
						'default'     => array( 'recruitingCategory', 'schedule', 'office' ),
					),
					'personioIntegrationTemplateJobDescription' => array(
						'label'       => __( 'Choose job description template in details-view', 'personio-integration-light' ),
						'callback'    => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Select', 'get' ),
						'options'     => Templates::get_instance()->get_jobdescription_templates(),
						/* translators: %1$s will be replaced with the documentation-URL */
						'description' => sprintf( __( 'You could add own custom templates as described in the <a href="%1$s" target="_blank">documentation (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_template_documentation_url() ) ),
						'readonly'    => ! Helper::is_personio_url_set(),
						'default'     => 'default',
					),
					'personioIntegrationTemplateBackToListButton' => array(
						'label'               => __( 'Enable back to list-link', 'personio-integration-light' ),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
						),
						'default'             => 0,
					),
					'personioIntegrationTemplateBackToListUrl' => array(
						'label'               => __( 'URL for back to list-link', 'personio-integration-light' ),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Text', 'get' ),
						/* translators: %1$s will be replaced by the list-slug */
						'description'         => sprintf( __( 'If empty the link will be set to list-slug <a href="%1$s">%1$s</a>.', 'personio-integration-light' ), esc_url( trailingslashit( get_home_url() ) . Helper::get_archive_slug() ) ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'string',
						),
						'default'             => '',
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
						'callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Text', 'get' ),
						'readonly' => ! Helper::is_personio_url_set(),
						'default'  => ', ',
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
						'callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\ProHint', 'get' ),
						/* translators: %1$s will be replaced with the plugin Pro name */
						'pro_hint' => __( 'With %1$s you get more advanced options, e.g. to change the URL of archives with positions.', 'personio-integration-light' ),
					),
					'personioIntegrationExtendSearch'      => array(
						'label'               => __( 'Note the position-keywords in search in frontend', 'personio-integration-light' ),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
						),
						'default'             => 1,
					),
					'personioIntegrationMaxAgeLogEntries'  => array(
						'label'    => __( 'max. Age for log entries in days', 'personio-integration-light' ),
						'callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Number', 'get' ),
						'readonly' => ! Helper::is_personio_url_set(),
						'default'  => 50,
					),
					'personioIntegrationUrlTimeout'        => array(
						'label'               => __( 'Timeout for URL-request in Seconds', 'personio-integration-light' ),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Number', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'sanitize_callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\UrlTimeout', 'validate' ),
							'type'              => 'integer',
						),
						'default'             => 30,
					),
					'personioIntegrationDeleteOnUninstall' => array(
						'label'               => __( 'Delete all imported data on uninstall', 'personio-integration-light' ),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
						),
						'default'             => 1,
					),
					'personioIntegrationResetIntro' => array(
						'label'    => __( 'Reset intro', 'personio-integration-light' ),
						'callback' => array( 'PersonioIntegrationLight\Plugin\Intro', 'show_reset_button' ),
					),
					'personioIntegration_debug'            => array(
						'label'               => __( 'Debug-Mode', 'personio-integration-light' ),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type' => 'integer',
						),
						'default'             => 0,
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
						'callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\ImportPositions', 'get' ),
					),
					'personioIntegrationDeleteNow' => array(
						'label'    => __( 'Delete local positions', 'personio-integration-light' ),
						'callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\DeletePositions', 'get' ),
					),
					'personioIntegrationEnablePositionSchedule' => array(
						'label'               => __( 'Enable automatic import', 'personio-integration-light' ),
						'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'description' => __( 'The automatic import is run once per day. You don\'t have to worry about updating your jobs on the website yourself.', 'personio-integration-light' ),
						/* translators: %1$s is replaced with "string" */
						'pro_hint'            => __( 'Use more import options with the %s. Among other things, you get the possibility to change the time interval for imports and partial imports of very large position lists.', 'personio-integration-light' ),
						'register_attributes' => array(
							'type' => 'integer',
						),
						'default'             => 1,
					),
				),
			),
			'hidden_section'                   => array(
				'fields' => array(
					'wp_easy_setup_completed'         => array(
						'register_attributes' => array(
							'type' => 'integer',
						),
						'default'             => 0,
					),
					'personio_integration_transients' => array(
						'register_attributes' => array(
							'type' => 'integer',
						),
						'default'             => 0,
					),
					'personio-integration-intro' => array(
						'register_attributes' => array(
							'type' => 'integer',
						),
						'default'             => 0,
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
			if ( ! empty( $section_settings['page'] ) ) {
				foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
					register_setting(
						$section_settings['page'],
						$field_name,
						! empty( $field_settings['register_attributes'] ) ? $field_settings['register_attributes'] : array()
					);
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
				// add section.
				add_settings_section(
					$section_name,
					$section_settings['label'],
					$section_settings['callback'],
					$section_settings['page']
				);

				// add fields in this section.
				if ( ! empty( $section_settings['fields'] ) ) {
					foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
						add_settings_field(
							$field_name,
							$field_settings['label'],
							$field_settings['callback'],
							$section_settings['page'],
							$section_name,
							array(
								'label_for'   => $field_name,
								'fieldId'     => $field_name,
								'options'     => ! empty( $field_settings['options'] ) ? $field_settings['options'] : array(),
								'description' => ! empty( $field_settings['description'] ) ? $field_settings['description'] : '',
								'placeholder' => ! empty( $field_settings['placeholder'] ) ? $field_settings['placeholder'] : '',
								'pro_hint'    => ! empty( $field_settings['pro_hint'] ) ? $field_settings['pro_hint'] : '',
								'highlight'   => ! empty( $field_settings['highlight'] ) ? $field_settings['highlight'] : false,
								'readonly'    => ! empty( $field_settings['readonly'] ) ? $field_settings['readonly'] : false,
							)
						);
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
			// add menu entry for settings page.
			add_submenu_page(
				PersonioPosition::get_instance()->get_link( true ),
				__( 'Personio Integration Light', 'personio-integration-light' ) . ' ' . __( 'Settings', 'personio-integration-light' ),
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
		if ( empty( $tab ) ) {
			$tab = 'general';
		}

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
				foreach ( $this->get_tabs() as $tab_name => $tab_settings ) {
					// Set url.
					$url    = add_query_arg(
						array(
							'post_type' => PersonioPosition::get_instance()->get_name(),
							'page'      => 'personioPositions',
							'tab'       => $tab_name,
						),
						'edit.php'
					);
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
					if ( $tab === $tab_name ) {
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
	 * Return a single actual setting.
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
	 * Cleanup the Personio URL setting before saving it.
	 *
	 * @param string $value The value to clean.
	 *
	 * @return string
	 */
	public function cleanup_personio_url_setting( string $value ): string {
		return PersonioIntegrationUrl::cleanup_url_string( $value );
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
		foreach( $tabs as $tab_name => $setting ) {
			if( isset($setting['only_pro']) ) {
				unset( $tabs[$tab_name]);
			}
		}
		return $tabs;
	}
}

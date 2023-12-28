<?php
/**
 * File to handle plugin-settings.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

use App\Helper;
use App\PersonioIntegration\PostTypes\PersonioPosition;
use App\PersonioIntegration\Taxonomies;
use function Sodium\add;

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

		// register fields to manage the settings.
		add_action( 'admin_init', array( $this, 'register_fields' ) );

		// add admin-menu.
		add_action( 'admin_menu', array( $this, 'add_settings_menu' ) );
	}

	/**
	 * Set ALL settings.
	 *
	 * @return void
	 */
	public function set_settings(): void {
		// set tabs.
		$this->tabs = array(
			'general' => array(
				'label' => __( 'General Settings', 'personio-integration-light' ),
				'key' => '',
				'page' => 'personioIntegrationPositions'
			),
			'templates' => array(
				'label' => __( 'Templates', 'personio-integration-light' ),
				'key' => 'templates',
				'page' => 'personioIntegrationPositionsTemplates'
			),
			'applications' => array(
				'label' => __( 'Applications', 'personio-integration-light' ),
				'only_pro' => true
			),
			'application_forms' => array(
				'label' => __( 'Application Forms', 'personio-integration-light' ),
				'only_pro' => true
			),
			'seo' => array(
				'label' => __( 'SEO', 'personio-integration-light' ),
				'only_pro' => true
			),
			'tracking' => array(
				'label' => __( 'Tracking', 'personio-integration-light' ),
				'only_pro' => true
			),
			'permissions' => array(
				'label' => __( 'Permissions', 'personio-integration-light' ),
				'only_pro' => true
			),
			'advanced' => array(
				'label' => __( 'Advanced', 'personio-integration-light' ),
				'key' => 'advanced',
				'page' => 'personioIntegrationPositionsAdvanced'
			)
		);

		// set settings for this plugin.
		$this->settings = array(
			'settings_section_main' => array(
				'label'    => __( 'General Settings', 'personio-integration-light' ),
				'page'     => 'personioIntegrationPositions',
				'callback' => '__return_true',
				'fields'   => array(
					'personioIntegrationUrl'       => array(
						'label'               => __( 'Personio URL', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\Text', 'get' ),
						/* translators: %1$s is replaced with the url to personio account, %2$s is replaced with the url to the personio support */
						'description'         => sprintf( __( 'You find this URL in your <a href="%1$s" target="_blank">Personio-account (opens new window)</a> under Settings > Recruiting > Career Page > Activations.<br>If you have any questions about the URL provided by Personio, please contact the <a href="%2$s">Personio support</a>.', 'personio-integration-light' ), esc_url( helper::get_personio_login_url() ), esc_url( helper::get_personio_support_url() ) ),
						'placeholder'         => Helper::is_german_language() ? 'https://dein-unternehmen.jobs.personio.de' : 'https://yourcompany.jobs.personio.com',
						'highlight'           => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'default'           => '',
							'show_in_rest'      => true,
							'sanitize_callback' => array( 'App\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl', 'validate' ),
							'type'              => 'string',
						)
					),
					'personioIntegrationLanguages' => array(
						'label'               => __( 'Used languages', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\Multiple_Checkboxes', 'get' ),
						'options'             => Languages::get_instance()->get_languages(),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'sanitize_callback' => array( 'App\Plugin\Admin\SettingsValidation\Languages', 'validate' ),
							'type'              => 'array',
						)
					),
					WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE => array(
						'label'               => __( 'Main language', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\Multiple_Radios', 'get' ),
						'options'             => Languages::get_instance()->get_languages(),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'sanitize_callback' => array( 'App\Plugin\Admin\SettingsValidation\MainLanguages', 'validate' ),
							'type'              => 'string',
						)
					)
				)
			),
			'settings_section_template_list' => array(
				'label'    => __( 'List View', 'personio-integration-light' ),
				'page'     => 'personioIntegrationPositionsTemplates',
				'callback' => '__return_true',
				'fields' => array(
					'personioIntegrationEnableFilter' => array(
						'label'               => __( 'Enable filter on list-view', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'              => 'integer',
						)
					),
					'personioIntegrationFilterType' => array(
						'label'               => __( 'Choose filter-type', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\Select', 'get' ),
						'options'             => Helper::get_filter_types(),
						'description' => __( 'This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
					),
					'personioIntegrationTemplateContentListingTemplate' => array(
						'label'               => __( 'Choose template for listing', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\Select', 'get' ),
						'options'             => Templates::get_instance()->get_archive_templates(),
						'readonly'            => ! Helper::is_personio_url_set(),
					),
					'personioIntegrationTemplateContentList' => array(
						'label'               => __( 'Choose templates for positions in list-view', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\MultiSelect', 'get' ),
						'options'             => Templates::get_instance()->get_template_labels(),
						'description'      => __( 'Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
					),
					'personioIntegrationTemplateExcerptDefaults' => array(
						'label'               => __( 'Choose details for positions in list-view', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\MultiSelect', 'get' ),
						'options'             => apply_filters( 'personio_integration_settings_get_list', Taxonomies::get_instance()->get_cat_labels(), get_option( 'personioIntegrationTemplateExcerptDefaults', array() ) ),
						'description'      => __( 'Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
					),
					'personioIntegrationEnableLinkInList' => array(
						'label'               => __( 'Enable link to single on list-view', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
						'register_attributes' => array(
							'type'              => 'integer',
						)
					),
				)
			),
			'settings_section_template_detail' => array(
				'label'    => __( 'Single View', 'personio-integration-light' ),
				'page'     => 'personioIntegrationPositionsTemplates',
				'callback' => '__return_true',
				'fields' => array(
					'personioIntegrationTemplateContentDefaults' => array(
						'label'               => __( 'Choose templates', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\MultiSelect', 'get' ),
						'options'             => Templates::get_instance()->get_template_labels(),
						'description'      => __( 'Mark multiple default templates for each detail-view of single positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
					),
					'personioIntegrationTemplateJobDescription' => array(
						'label'               => __( 'Choose job description template', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\Select', 'get' ),
						'options'             => Templates::get_instance()->get_jobdescription_templates(),
						'description' => __( 'Choose template to output each job description in detail view. You could add your own template as described in GitHub.', 'personio-integration-light' ),
						'readonly'            => ! Helper::is_personio_url_set(),
					),
					'personioIntegrationTemplateBackToListButton' => array(
						'label'               => __( 'Enable back to list-link', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\Checkbox', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
					),
					'personioIntegrationTemplateBackToListUrl' => array(
						'label'               => __( 'URL for back to list-link', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\Text', 'get' ),
						/* translators: %1$s will be replaced by the list-slug */
						'description' => sprintf( __( 'If empty the link will be set to list-slug <a href="%1$s">%1$s</a>.', 'personio-integration-light' ), esc_url( trailingslashit( get_home_url() ) . helper::get_archive_slug() ) ),
						'readonly'            => ! Helper::is_personio_url_set(),
					),
				)
			),
			'settings_section_template_other' => array(
				'label'    => __( 'Other settings', 'personio-integration-light' ),
				'page'     => 'personioIntegrationPositionsTemplates',
				'callback' => '__return_true',
				'fields' => array(
					'personioIntegrationTemplateExcerptSeparator' => array( // TODO darauf verzichten wegen Template-Nutzung?
						'label'               => __( 'Separator for details-listing', 'personio-integration-light' ),
						'callback'            => array( 'App\Plugin\Admin\SettingFields\Text', 'get' ),
						'readonly'            => ! Helper::is_personio_url_set(),
					),
				)
			)
		);
	}

	/**
	 * Register the settings.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		foreach( $this->settings as $section_settings ) {
			foreach( $section_settings['fields'] as $field_name => $field_settings ) {
				register_setting(
					$section_settings['page'],
					$field_name,
					!empty( $field_settings['register_attributes'] ) ? $field_settings['register_attributes'] : array()
				);
			}
		}
	}

	/**
	 * Register fields to manage the settings.
	 *
	 * @return void
	 */
	public function register_fields(): void {
		foreach( $this->settings as $section_name => $section_settings ) {
			if( !empty($section_settings) ) {
				// add section.
				add_settings_section(
					$section_name,
					$section_settings['label'],
					$section_settings['callback'],
					$section_settings['page']
				);

				// add fields in this section.
				if( !empty($section_settings['fields']) ) {
					foreach( $section_settings['fields'] as $field_name => $field_settings ) {
						add_settings_field(
							$field_name,
							$field_settings['label'],
							$field_settings['callback'],
							$section_settings['page'],
							$section_name,
							array(
								'label_for'   => $field_name,
								'fieldId'     => $field_name,
								'options' => !empty( $field_settings['options'] ) ? $field_settings['options'] : array(),
								'description' => !empty( $field_settings['description'] ) ? $field_settings['description'] : '',
								'placeholder' => !empty( $field_settings['placeholder'] ) ? $field_settings['placeholder'] : '',
								'highlight'   => !empty( $field_settings['highlight'] ) ? $field_settings['highlight'] : false,
								'readonly'    => !empty( $field_settings['readonly'] ) ? $field_settings['readonly'] : false
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Add settings-page for the plugin.
	 *
	 * @return void
	 */
	public function add_settings_menu(): void {
		add_submenu_page(
			'edit.php?post_type=' . PersonioPosition::get_instance()->get_name(),
			__( 'Personio Integration Settings', 'personio-integration-light' ),
			__( 'Settings', 'personio-integration-light' ),
			'manage_' . PersonioPosition::get_instance()->get_name(),
			'personioPositions',
			array( $this, 'add_settings_content' ),
			1
		);
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

		// get the active tab from the $_GET param.
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';

		// set page to show.
		$page = 'personioIntegrationPositions';

		// output wrapper.
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<nav class="nav-tab-wrapper">
				<?php
				foreach( $this->tabs as $tab_name => $tab_settings ) {
					// Set url.
					$url = add_query_arg(
						array(
							'post_type' => 'personioposition',
							'page' => 'personioPositions',
							'tab' => $tab_name
						),
						'edit.php'
					);

					// Set class for tab and page for form-view.
					$class = '';
					if( $tab === $tab_name ) {
						$class = ' nav-tab-active';
						$page = $tab_settings['page'];
					}

					$title = '';
					if( isset($tab_settings['only_pro']) && false !== $tab_settings['only_pro'] ) {
						$title = esc_attr__( 'Only in Pro.', 'personio-integration-light' );

						// Output tab.
						?><span class="nav-tab" title="<?php echo esc_html( $title ); ?>"><?php echo esc_html( $tab_settings['label'] ); ?> <span class="pro-marker">Pro</span></span><?php
					}
					else {

						// Output tab.
						?><a href="<?php echo esc_url( $url ); ?>" class="nav-tab<?php echo esc_attr( $class ); ?>" title="<?php echo esc_html( $title ); ?>"><?php echo esc_html( $tab_settings['label'] ); ?></a><?php
					}
				}
				?>
			</nav>

			<div class="tab-content">
			<?php
				// show errors.
				settings_errors();

				?>
				<form method="POST" action="<?php echo esc_url( get_admin_url() ); ?>options.php">
					<?php
					settings_fields( $page );
					do_settings_sections( $page );
					submit_button();
					?>
				</form>
			</div>
		</div>
		<?php
	}

}

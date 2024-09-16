<?php
/**
 * File with main initializer for this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\Post_Types;
use PersonioIntegrationLight\Plugin\Admin\Admin;
use PersonioIntegrationLight\Third_Party_Plugins;
use PersonioIntegrationLight\Widgets\Widgets;

/**
 * Initialize this plugin.
 */
class Init {
	/**
	 * Instance of this object.
	 *
	 * @var ?Init
	 */
	private static ?Init $instance = null;

	/**
	 * Constructor for Init-Handler.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() { }

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Init {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize this plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		// init transients.
		Transients::get_instance()->init();

		// register settings.
		Settings::get_instance()->init();

		// check setup state.
		Setup::get_instance()->init();

		// check intro state.
		Intro::get_instance()->init();

		// register our post-type and dependent taxonomies.
		Post_Types::get_instance()->init();

		// init classic widget support.
		Widgets::get_instance()->init();

		// init templates.
		Templates::get_instance()->init();

		// init wp-admin-support.
		Admin::get_instance()->init();

		// init roles.
		Roles::get_instance()->init();

		// init third-party-support.
		Third_Party_Plugins::get_instance()->init();

		// init schedules.
		Schedules::get_instance()->init();

		// init compatibility-checks.
		Compatibilities::get_instance()->init();

		// install db tables on plugin-installation.
		add_action( 'personio_integration_install_db_tables', array( $this, 'install_db_tables' ) );

		// register cli.
		add_action( 'cli_init', array( $this, 'cli' ) );

		// register frontend scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles_frontend' ), PHP_INT_MAX );

		// add action links on plugin-list.
		add_filter( 'plugin_action_links_' . plugin_basename( WP_PERSONIO_INTEGRATION_PLUGIN ), array( $this, 'add_setting_link' ) );

		// add update message in plugin list.
		add_action( 'in_plugin_update_message-' . plugin_basename( WP_PERSONIO_INTEGRATION_PLUGIN ), array( $this, 'add_plugin_update_hints' ), 10, 2 );

		// request-hooks.
		add_action( 'wp', array( $this, 'update_slugs' ) );
		add_action( 'init', array( $this, 'light_init' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
	}

	/**
	 * Tasks to run on activation.
	 *
	 * @return void
	 */
	public function activation(): void {
		Installer::get_instance()->activation();
	}

	/**
	 * Tasks to run on deactivation.
	 *
	 * @return void
	 */
	public function deactivation(): void {
		Schedules::get_instance()->delete_all();
	}

	/**
	 * Register WP-CLI.
	 *
	 * @return void
	 */
	public function cli(): void {
		\WP_CLI::add_command( 'personio', 'PersonioIntegrationLight\Plugin\Cli' );
	}

	/**
	 * Add own CSS and JS for frontend.
	 *
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function add_styles_frontend(): void {
		wp_enqueue_style(
			'personio-integration-styles',
			Helper::get_plugin_url() . 'css/styles.css',
			array(),
			Helper::get_file_version( Helper::get_plugin_path() . 'css/styles.css' )
		);

		/**
		 * Load listing-style from Block "list" if FSE-theme is NOT used.
		 */
		if ( ! Helper::theme_is_fse_theme() ) {
			wp_enqueue_style(
				'personio-integration-additional-styles',
				Helper::get_plugin_url() . 'blocks/list/build/style-index.css',
				array(),
				Helper::get_file_version( Helper::get_plugin_path() . 'blocks/list/build/style-index.css' )
			);
		}
	}

	/**
	 * Add link to plugin-settings in plugin-list.
	 *
	 * @param array $links List of links.
	 * @return array
	 */
	public function add_setting_link( array $links ): array {
		// if setup has not been completed, show link here.
		if ( ! Setup::get_instance()->is_completed() ) {
			$links[] = "<a href='" . esc_url( Setup::get_instance()->get_setup_link() ) . "'>" . __( 'Setup', 'personio-integration-light' ) . '</a>';
		} else {
			// adds the link to for settings.
			$links[] = "<a href='" . esc_url( Helper::get_settings_url() ) . "'>" . __( 'Settings', 'personio-integration-light' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Update slugs on request.
	 *
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function update_slugs(): void {
		if ( 1 === absint( get_option( 'personio_integration_update_slugs' ) ) ) {
			flush_rewrite_rules();
			update_option( 'personio_integration_update_slugs', 0 );
		}
	}

	/**
	 * Register our custom query_vars for frontend.
	 *
	 * @param array $query_vars List of query vars.
	 *
	 * @return array
	 */
	public function add_query_vars( array $query_vars ): array {
		// variable for filter.
		$query_vars[] = 'personiofilter';
		return $query_vars;
	}

	/**
	 * Show info from update_notice-section in readme.txt in the WordPress-repository.
	 *
	 * @param array  $data List of plugin-infos.
	 * @param object $response The response-data.
	 *
	 * @return void
	 */
	public function add_plugin_update_hints( array $data, object $response ): void {
		// bail if plugin_data is empty.
		if ( empty( $plugin_data ) ) {
			return;
		}

		// bail if response has no new version.
		if ( ! isset( $response->new_version ) ) {
			return;
		}

		// get transient with notice hints and check if actual version has one.
		$notice_hints = get_transient( 'personio_integration_light_plugin_update_notices' );
		$notice_hint  = '';
		if ( ! empty( $notice_hints[ $response->new_version ] ) ) {
			$notice_hint = $notice_hints[ $response->new_version ];
		}

		// if no notices is set, try to get one.
		if ( empty( $notice_hint ) ) {
			// get actual readme.txt from repository.
			$readme_response = wp_safe_remote_get( 'https://plugins.svn.wordpress.org/personio-integration-light/trunk/readme.txt' );
			if ( ! is_wp_error( $readme_response ) && ! empty( $readme_response['body'] ) ) {
				$notice_hint = $this->parse_plugin_update_notice( $readme_response['body'], $response->new_version );
				if ( ! empty( $notice_hint ) ) {
					// save the response as notice.
					$transient_value = array(
						$response->new_version => $notice_hint,
					);
					set_transient( 'personio_integration_light_plugin_update_notices', $transient_value, 86400 );
				}
			}
		}

		// show hint, if set.
		if ( ! empty( $notice_hint ) ) {
			echo '<div class="personio-integration-plugin-update-notice">' . wp_kses_post( $notice_hint ) . '</div>';
		}
	}

	/**
	 * Parse update notice from readme file.
	 *
	 * @param  string $content WooCommerce readme file content.
	 * @param  string $new_version WooCommerce new version.
	 * @return string
	 */
	private function parse_plugin_update_notice( string $content, string $new_version ): string {
		$upgrade_notice = '';

		// get upgrade notice section.
		if ( preg_match( '/(?<===) Upgrade Notice ==(.*?)(?===)/ms', $content, $section ) ) {
			$upgrade_section_content = $section[0];
			if ( preg_match( '/(?<==) ' . preg_quote( $new_version, null ) . ' =(.*?)(?==)/ms', $upgrade_section_content, $version_notes ) ) {
				$upgrade_notice = $version_notes[1];
			} elseif ( preg_match( '/(?<==) ' . preg_quote( $new_version, null ) . ' =(.*)(?==|$)/ms', $upgrade_section_content, $version_notes ) ) {
				$upgrade_notice = $version_notes[1];
			}
		}
		return $upgrade_notice;
	}

	/**
	 * Install db-table of registered objects.
	 *
	 * @return void
	 */
	public function install_db_tables(): void {
		foreach ( apply_filters( 'personio_integration_objects_with_db_tables', array( 'PersonioIntegrationLight\Log' ) ) as $obj_name ) {
			$obj = new $obj_name();
			if ( method_exists( $obj, 'create_table' ) ) {
				$obj->create_table();
			}
		}
	}

	/**
	 * Delete db-tables of registered objects.
	 *
	 * @return void
	 */
	public function delete_db_tables(): void {
		foreach ( apply_filters( 'personio_integration_objects_with_db_tables', array( 'PersonioIntegrationLight\Log' ) ) as $obj_name ) {
			if ( str_contains( $obj_name, 'PersonioIntegrationLight\\' ) ) {
				$obj = new $obj_name();
				if ( method_exists( $obj, 'delete_table' ) ) {
					$obj->delete_table();
				}
			}
		}
	}

	/**
	 * Enable check for updates for old pro-version if light has been updated but pro not.
	 *
	 * @return void
	 */
	public function light_init(): void {
		if ( Helper::is_plugin_active( 'personio-integration/personio-integration.php' ) ) {
			$path = trailingslashit( plugin_dir_path( WP_PLUGIN_DIR . '/personio-integration/personio-integration.php' ) ) . 'inc/update.php';
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}
}

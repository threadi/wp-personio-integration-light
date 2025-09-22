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
use PersonioIntegrationLight\PersonioIntegration\Api;
use PersonioIntegrationLight\PersonioIntegration\Post_Types;
use PersonioIntegrationLight\Plugin\Admin\Admin;
use PersonioIntegrationLight\Third_Party_Plugins;
use PersonioIntegrationLight\Widgets\Widgets;
use WP_Query;

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
	 * Constructor for this object.
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
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		// register settings.
		Settings::get_instance()->init();

		// check setup state.
		Setup::get_instance()->init();

		// init wp-admin-support.
		Admin::get_instance()->init();

		// initialize the API support.
		Api::get_instance()->init();

		// check intro state.
		Intro::get_instance()->init();

		// register our post-type and dependent taxonomies.
		Post_Types::get_instance()->init();

		// init classic widget support.
		Widgets::get_instance()->init();

		// init templates.
		Templates::get_instance()->init();

		// init roles.
		Roles::get_instance()->init();

		// init third-party-support.
		Third_Party_Plugins::get_instance()->init();

		// init schedules.
		Schedules::get_instance()->init();

		// init compatibility-checks.
		Compatibilities::get_instance()->init();

		// init email support.
		Emails::get_instance()->init();

		// install db tables on plugin-installation.
		add_action( 'personio_integration_install_db_tables', array( $this, 'install_db_tables' ) );

		// register cli.
		add_action( 'cli_init', array( $this, 'cli' ) );

		// register frontend scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles_frontend' ), PHP_INT_MAX );

		// add action links on plugin-list.
		add_filter( 'plugin_action_links_' . plugin_basename( WP_PERSONIO_INTEGRATION_PLUGIN ), array( $this, 'add_setting_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_row_meta_links' ), 10, 2 );

		// add update message in plugin list.
		add_action( 'in_plugin_update_message-' . plugin_basename( WP_PERSONIO_INTEGRATION_PLUGIN ), array( $this, 'add_plugin_update_hints' ), 10, 2 );

		// request-hooks.
		add_action( 'wp', array( $this, 'update_slugs' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'parse_query', array( $this, 'check_static_front_filter' ) );
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
	 * @param array<int,string> $links List of links.
	 * @return array<int,string>
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
	 * Add links in row meta.
	 *
	 * @param array<string,string> $links List of links.
	 * @param string               $file The requested plugin file name.
	 *
	 * @return array<string,string>
	 */
	public function add_row_meta_links( array $links, string $file ): array {
		// bail if this is not our plugin.
		if ( WP_PERSONIO_INTEGRATION_PLUGIN !== WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $file ) {
			return $links;
		}

		// add our custom links.
		$row_meta = array(
			'support' => '<a href="' . esc_url( Helper::get_plugin_support_url() ) . '" target="_blank" title="' . esc_html__( 'Support Forum', 'personio-integration-light' ) . '">' . esc_html__( 'Support Forum', 'personio-integration-light' ) . '</a>',
		);

		/**
		 * Filter the links in row meta of our plugin in plugin list.
		 *
		 * @since 4.2.4 Available since 4.2.4.
		 * @param array<string,string> $row_meta List of links.
		 */
		$row_meta = apply_filters( 'personio_integration_light_plugin_row_meta', $row_meta );

		// return the resulting list of links.
		return array_merge( $links, $row_meta );
	}

	/**
	 * Update slugs on request.
	 *
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function update_slugs(): void {
		if ( 1 !== absint( get_option( 'personio_integration_update_slugs' ) ) ) {
			return;
		}

		// flush the rewrite rules.
		flush_rewrite_rules();

		// disable the flag to update them.
		update_option( 'personio_integration_update_slugs', 0 );
	}

	/**
	 * Register our custom query_vars for frontend.
	 *
	 * @param array<int,string> $query_vars List of query vars.
	 *
	 * @return array<int,string>
	 */
	public function add_query_vars( array $query_vars ): array {
		// variable for filter.
		$query_vars[] = 'personiofilter';
		return $query_vars;
	}

	/**
	 * Show info from update_notice-section in readme.txt in the WordPress-repository.
	 *
	 * @param array<string,mixed> $data List of plugin-infos.
	 * @param object              $response The response-data.
	 *
	 * @return void
	 */
	public function add_plugin_update_hints( array $data, object $response ): void {
		// bail if plugin_data is empty.
		if ( empty( $data ) ) {
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
			echo '</p></div><div class="notice inline notice-warning notice-alt personio-integration-plugin-update-notice"><p>' . wp_kses_post( $notice_hint ) . '</p></div><div><p>';
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
	 * Install db-tables of registered objects.
	 *
	 * Hint: the objects must just have a function "create_table".
	 *
	 * @return void
	 */
	public function install_db_tables(): void {
		$objects = array( '\PersonioIntegrationLight\Log' );
		/**
		 * Add additional objects for this plugin which use custom tables.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 * @param array<int,string> $objects List of objects.
		 */
		foreach ( apply_filters( 'personio_integration_objects_with_db_tables', $objects ) as $obj_name ) {
			// bail if class does not exist.
			if ( ! class_exists( $obj_name ) ) {
				continue;
			}

			// get the object.
			$obj = new $obj_name();

			// bail if object does not have a function "create_table".
			if ( ! method_exists( $obj, 'create_table' ) ) {
				continue;
			}

			// call the function to create its table(s).
			$obj->create_table();
		}
	}

	/**
	 * Delete db-tables of registered objects.
	 *
	 * @return void
	 */
	public function delete_db_tables(): void {
		$objects = array( '\PersonioIntegrationLight\Log' );
		/**
		 * Add additional objects for this plugin which use custom tables.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 * @param array<int,string> $objects List of objects.
		 */
		foreach ( apply_filters( 'personio_integration_objects_with_db_tables', $objects ) as $obj_name ) {
			if ( str_contains( $obj_name, 'PersonioIntegrationLight\\' ) ) {
				$obj = new $obj_name();
				if ( method_exists( $obj, 'delete_table' ) ) {
					$obj->delete_table();
				}
			}
		}
	}

	/**
	 * Check for static front page with active filter and change the main query settings to show the page with filter.
	 *
	 * @param WP_Query $query The query object.
	 *
	 * @return void
	 */
	public function check_static_front_filter( WP_Query $query ): void {
		// bail if this is not the main query.
		if ( ! $query->is_main_query() ) {
			return;
		}

		// bail if 'personiofilter' is not set.
		if ( empty( $query->get( 'personiofilter' ) ) ) {
			return;
		}

		// bail if this is a single page, an archive or a preview.
		if ( is_single() || is_archive() || is_preview() ) {
			return;
		}

		// bail if page on front is not used.
		$page_on_front = absint( get_option( 'page_on_front' ) );
		if ( 0 === $page_on_front ) {
			return;
		}

		// bail if pagename is set.
		if ( ! empty( $query->get( 'pagename' ) ) ) {
			return;
		}

		// we assume this is a static frontpage with "personiofilter".
		$query->is_home     = false;
		$query->is_page     = true;
		$query->is_singular = true;
		$query->set( 'page_id', $page_on_front );
	}
}

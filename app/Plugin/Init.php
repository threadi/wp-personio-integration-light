<?php
/**
 * File with main initializer for this plugin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

use App\PersonioIntegration\PostTypes\PersonioPosition;
use App\Plugin\Admin\Admin;
use App\Third_Party_Plugins;
use App\Widgets\Widgets;
use WP_Admin_Bar;

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
		// register our post-types and taxonomies.
		PersonioPosition::get_instance()->init();

		// register settings.
		Settings::get_instance()->init();

		// init classic widget support.
		Widgets::get_instance()->init();

		// init templates.
		Templates::get_instance()->init();

		// init wp-admin-support.
		Admin::get_instance()->init();

		// init third-party-support.
		Third_Party_Plugins::get_instance()->init();

		// init site health.
		Site_Health::get_instance()->init();

		// on activation.
		register_activation_hook( WP_PERSONIO_INTEGRATION_PLUGIN, array( $this, 'activation' ) );

		// on deactivation.
		register_deactivation_hook( WP_PERSONIO_INTEGRATION_PLUGIN, array( $this, 'deactivation' ) );

		// register cli.
		add_action( 'cli_init', array( $this, 'cli' ) );

		// register admin bar customizations.
		add_action( 'admin_bar_menu', array( $this, 'add_custom_toolbar' ), 100 );

		// register frontend scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles_frontend' ), PHP_INT_MAX );

		// add action links on plugin-list.
		add_filter( 'plugin_action_links_' . plugin_basename( WP_PERSONIO_INTEGRATION_PLUGIN ), array( $this, 'add_setting_link' ) );
	}

	/**
	 * Tasks to run on activation.
	 *
	 * @return void
	 */
	public function activation(): void {
		Installer::activation();
	}

	/**
	 * Tasks to run on deactivation.
	 *
	 * @return void
	 */
	public function deactivation(): void {
		wp_clear_scheduled_hook( 'personio_integration_schudule_events' );
	}

	/**
	 * Register WP-CLI.
	 *
	 * @return void
	 */
	public function cli(): void {
		\WP_CLI::add_command( 'personio', 'personioIntegration\Cli' );
	}

	/**
	 * Add link in toolbar to list of positions.
	 * Only if Personio URL is given and list-view is not disabled.
	 *
	 * @param WP_Admin_Bar $admin_bar The object of the Admin-Bar.
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function add_custom_toolbar( WP_Admin_Bar $admin_bar ): void {
		if ( get_option( 'personioIntegrationUrl', false ) && 0 === absint( get_option( 'personioIntegrationDisableListSlug', 0 ) ) ) {
			$admin_bar->add_menu(
				array(
					'id'     => 'personio-position-list',
					'parent' => 'site-name',
					'title'  => __( 'Personio Positions', 'personio-integration-light' ),
					'href'   => get_post_type_archive_link( WP_PERSONIO_INTEGRATION_CPT ),
				)
			);
		}
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
			trailingslashit( plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'css/styles.css',
			array(),
			filemtime( trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'css/styles.css' )
		);
	}

	/**
	 * Add link to plugin-settings in plugin-list.
	 *
	 * @param array $links List of links.
	 * @return array
	 */
	public function add_setting_link( array $links ): array {
		// build and escape the URL.
		$url = add_query_arg(
			array(
				'page'      => 'personioPositions',
				'post_type' => WP_PERSONIO_INTEGRATION_CPT,
			),
			get_admin_url() . 'edit.php'
		);

		// create the link.
		$settings_link = "<a href='" . esc_url( $url ) . "'>" . __( 'Settings', 'personio-integration-light' ) . '</a>';

		// adds the link to the end of the array.
		$links[] = $settings_link;

		return $links;
	}
}

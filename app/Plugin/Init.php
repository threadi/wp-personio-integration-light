<?php
/**
 * File with main initializer for this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PageBuilder\Gutenberg;
use PersonioIntegrationLight\PageBuilder_Base;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Admin\Admin;
use PersonioIntegrationLight\Third_Party_Plugins;
use PersonioIntegrationLight\Widgets\Widgets;
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
		// init transients.
		Transients::get_instance()->init();

		// register settings.
		Settings::get_instance()->init();

		// check setup state.
		Setup::get_instance()->init();

		// check intro state.
		Intro::get_instance()->init();

		// register our post-types and taxonomies.
		PersonioPosition::get_instance()->init();

		// init classic widget support.
		Widgets::get_instance()->init();

		// init templates.
		Templates::get_instance()->init();

		// init wp-admin-support.
		Admin::get_instance()->init();

		// init roles.
		Roles::get_instance()->init();

		// add our own Gutenberg-pagebuilder-support.
		add_filter( 'personio_integration_pagebuilder', array( $this, 'add_pagebuilder_gutenberg' ) );

		$page_builder_objects = array();
		/**
		 * Register supported page builders.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $page_builder_objects The list of page builders.
		 */
		foreach ( apply_filters( 'personio_integration_pagebuilder', $page_builder_objects ) as $page_builder_obj ) {
			if ( $page_builder_obj instanceof PageBuilder_Base ) {
				$page_builder_obj->init();
			}
		}

		// init third-party-support.
		Third_Party_Plugins::get_instance()->init();

		// init schedules.
		Schedules::get_instance()->init();

		// register cli.
		add_action( 'cli_init', array( $this, 'cli' ) );

		// register admin bar customizations.
		add_action( 'admin_bar_menu', array( $this, 'add_custom_toolbar' ), 100 );

		// register frontend scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles_frontend' ), PHP_INT_MAX );

		// add action links on plugin-list.
		add_filter(
			'plugin_action_links_' . plugin_basename( WP_PERSONIO_INTEGRATION_PLUGIN ),
			array(
				$this,
				'add_setting_link',
			)
		);

		// request-hooks.
		add_action( 'wp', array( $this, 'update_slugs' ) );
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
	 * Add link in toolbar to list of positions.
	 * Only if Personio URL is given and list-view is not disabled.
	 *
	 * @param WP_Admin_Bar $admin_bar The object of the Admin-Bar.
	 * @return void
	 */
	public function add_custom_toolbar( WP_Admin_Bar $admin_bar ): void {
		if ( Helper::is_personio_url_set() && 0 === absint( get_option( 'personioIntegrationDisableListSlug' ) ) ) {
			$admin_bar->add_menu(
				array(
					'id'     => PersonioPosition::get_instance()->get_name().'-archive',
					'parent' => 'site-name',
					'title'  => __( 'Personio Positions', 'personio-integration-light' ),
					'href'   => get_post_type_archive_link( WP_PERSONIO_INTEGRATION_MAIN_CPT ),
				)
			);

			// add links in admin-bar in backend.
			if ( is_admin() ) {
				// add link to view position in frontend if one is called in backend.
				$post_id = absint( filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT ) );
				if ( $post_id > 0 && WP_PERSONIO_INTEGRATION_MAIN_CPT === get_post_type( $post_id ) ) {
					$position_obj = Positions::get_instance()->get_position( $post_id );
					$admin_bar->add_menu(
						array(
							'id'     => 'personio-integration-detail',
							'parent' => null,
							'group'  => null,
							'title'  => __( 'View Position in frontend', 'personio-integration-light' ),
							'href'   => $position_obj->get_link(),
						)
					);
				}
				else {
					$post_type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
					if( ! empty( $post_type) && WP_PERSONIO_INTEGRATION_MAIN_CPT === $post_type ) {
						$admin_bar->add_menu(
							array(
								'id'     => 'personio-integration-list',
								'parent' => null,
								'group'  => null,
								'title'  => __( 'View Positions in frontend', 'personio-integration-light' ),
								'href'   => get_post_type_archive_link( WP_PERSONIO_INTEGRATION_MAIN_CPT ),
							)
						);
					}
				}
			}
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
			Helper::get_plugin_url() . 'css/styles.css',
			array(),
			filemtime( Helper::get_plugin_path() . 'css/styles.css' )
		);

		/**
		 * Load listing-style from Block "list" if FSE-theme is NOT used.
		 */
		if ( ! Helper::theme_is_fse_theme() ) {
			wp_enqueue_style(
				'personio-integration-additional-styles',
				Helper::get_plugin_url() . 'blocks/list/build/style-index.css',
				array(),
				filemtime( Helper::get_plugin_path() . 'blocks/list/build/style-index.css' )
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
		// create the link.
		$settings_link = "<a href='" . esc_url( Helper::get_settings_url() ) . "'>" . __( 'Settings', 'personio-integration-light' ) . '</a>';

		// adds the link to the end of the array.
		$links[] = $settings_link;

		return $links;
	}

	/**
	 * Update slugs on request.
	 *
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function update_slugs(): void {
		if ( false !== get_transient( 'personio_integration_update_slugs' ) ) {
			flush_rewrite_rules();
			delete_transient( 'personio_integration_update_slugs' );
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
	 * Add the pagebuilder Gutenberg as object to the list.
	 *
	 * @param array $pagebuilder_objects List of pagebuilder as objects.
	 *
	 * @return array
	 */
	public function add_pagebuilder_gutenberg( array $pagebuilder_objects ): array {
		$pagebuilder_objects[] = Gutenberg::get_instance();
		return $pagebuilder_objects;
	}
}

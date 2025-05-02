<?php
/**
 * File for handling site health options of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use WP_Screen;

/**
 * Helper-function for Dashboard options of this plugin.
 */
class Help_System {
	/**
	 * Instance of this object.
	 *
	 * @var ?Help_System
	 */
	private static ?Help_System $instance = null;

	/**
	 * Constructor for Init-Handler.
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
	public static function get_instance(): Help_System {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the site health support.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'current_screen', array( $this, 'add_help' ) );
		add_filter( 'personio_integration_light_help_tabs', array( $this, 'add_applications_help' ), 50 );
		add_filter( 'personio_integration_light_help_tabs', array( $this, 'add_documentation_help' ), 70 );
	}

	/**
	 * Add the help box to our own pages with the configured contents.
	 *
	 * @param WP_Screen $screen The screen object.
	 *
	 * @return void
	 */
	public function add_help( WP_Screen $screen ): void {
		// bail if we are not in our cpt.
		if ( PersonioPosition::get_instance()->get_name() !== $screen->post_type ) {
			return;
		}

		// get the help tabs.
		$help_tabs = $this->get_help_tabs();
		// bail if list is empty.
		if ( empty( $help_tabs ) ) {
			return;
		}

		// add our own help tabs.
		foreach ( $help_tabs as $help_tab ) {
			$screen->add_help_tab( $help_tab );
		}

		// add the sidebar.
		$this->add_sidebar( $screen );
	}

	/**
	 * Add the sidebar with its content.
	 *
	 * @param WP_Screen $screen The screen object.
	 *
	 * @return void
	 */
	private function add_sidebar( WP_Screen $screen ): void {
		// get content for sidebar.
		$sidebar_content = '<p><strong>' . __( 'Question not answered?', 'personio-integration-light' ) . '</strong></p><p><a href="' . esc_url( Helper::get_plugin_support_url() ) . '" target="_blank">' . esc_html__( 'Ask in our forum', 'personio-integration-light' ) . '</a></p>';

		/**
		 * Filter the sidebar content.
		 *
		 * @since 4.0.0 Available since 4.0.0.
		 * @param string $sidebar_content The content.
		 */
		$sidebar_content = apply_filters( 'personio_integration_light_help_sidebar_content', $sidebar_content );

		// add help sidebar with the given content.
		$screen->set_help_sidebar( $sidebar_content );
	}

	/**
	 * Return the list of help tabs.
	 *
	 * @return array<string,mixed>
	 */
	private function get_help_tabs(): array {
		$list = array();

		/**
		 * Filter the list of help tabs with its contents.
		 *
		 * @since 4.0.0 Available since 4.0.0.
		 * @param array<string,mixed> $list List of help tabs.
		 */
		return apply_filters( 'personio_integration_light_help_tabs', $list );
	}

	/**
	 * Add help for using applications.
	 *
	 * @param array<string,mixed> $help_list List of help tabs.
	 *
	 * @return array<string,mixed>
	 */
	public function add_applications_help( array $help_list ): array {
		// add menu entry for applications (with hint to pro).
		$false = false;
		/**
		 * Hide the application help with its pro hint.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Set true to hide the buttons.
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			return $help_list;
		}

		// collect the content for the help.
		$content  = Helper::get_logo_img( true ) . '<h2>' . __( 'Applications', 'personio-integration-light' ) . '</h2><p>' . __( 'We enable you to advertise your jobs on your own website. Applicants can find them and apply for them.', 'personio-integration-light' ) . '</p>';
		$content .= '<p><strong>' . __( 'How to get applications:', 'personio-integration-light' ) . '</strong></p>';
		$content .= '<ol>';
		$content .= '<li>' . __( 'Publish your open positions in your website.', 'personio-integration-light' ) . '</li>';
		/* translators: %1$s will be replaced by a URL. */
		$content .= '<li>' . sprintf( __( 'Show the application link on each position. Enable this <a href="%1$s">in the template settings</a>.', 'personio-integration-light' ), esc_url( Helper::get_settings_url( 'personioPositions', 'templates' ) ) ) . '</li>';
		/* translators: %1$s will be replaced by a URL. */
		$content .= '<li>' . sprintf( __( '<a href="%1$s" target="_blank">Order Personio Integration Pro (opens new window)</a> to use application forms in your website.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) . '</li>';
		$content .= '</ol>';

		// add help for the positions in general.
		$help_list[] = array(
			'id'      => PersonioPosition::get_instance()->get_name() . '-applications',
			'title'   => __( 'Applications', 'personio-integration-light' ),
			'content' => $content,
		);

		// return resulting list.
		return $help_list;
	}

	/**
	 * Add help for using applications.
	 *
	 * @param array $help_list List of help tabs.
	 *
	 * @return array
	 */
	public function add_documentation_help( array $help_list ): array {
		// collect the content for the help.
		/* translators: %1$s will be replaced by a URL. */
		$content = Helper::get_logo_img( true ) . '<h2>' . __( 'Documentation', 'personio-integration-light' ) . '</h2><p>' . sprintf( __( 'We provide some documentations for the WordPress plugin <i>Personio Integration Light</i> at <a href="%1$s" target="_blank">GitHub (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_github_documentation_link() ) ) . '</p>';

		// add help for the positions in general.
		$help_list[] = array(
			'id'      => PersonioPosition::get_instance()->get_name() . '-documentation',
			'title'   => __( 'Documentations', 'personio-integration-light' ),
			'content' => $content,
		);

		// return resulting list.
		return $help_list;
	}
}

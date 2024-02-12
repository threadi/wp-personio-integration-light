<?php
/**
 * File to handle intro for this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;

/**
 * Initialize this plugin.
 */
class Intro {
	/**
	 * Instance of this object.
	 *
	 * @var ?Intro
	 */
	private static ?Intro $instance = null;

	/**
	 * Constructor for this handler.
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
	public static function get_instance(): Intro {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		// add admin-actions.
		add_action( 'admin_action_personioPositionsIntroReset', array( $this, 'reset_intro' ) );

		// bail if intro has been run.
		if( 1 === absint(get_option( 'personio-integration-intro' ) ) ) {
			return;
		}

		$false = false;
		/**
		 * Hide intro via hook.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Return true to hide the intro.
		 */
		if( apply_filters( 'personio_integration_templates_archive', $false ) ) {
			return;
		}

		// add our script.
		add_action( 'admin_enqueue_scripts', array( $this, 'add_js' ), PHP_INT_MAX );

		// add AJAX-actions.
		add_action( 'wp_ajax_personio_intro_closed', array( $this, 'closed' ) );
	}

	/**
	 * Set the intro to closed.
	 *
	 * @return void
	 */
	public function set_closed(): void {
		update_option( 'personio-integration-intro', 1 );
	}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	public function closed(): void {
		// check nonce.
		check_ajax_referer( 'personio-intro-closed', 'nonce' );

		// bail if function is not called via AJAX.
		if ( ! defined( 'DOING_AJAX' ) ) {
			wp_die();
		}

		// save that intro has been closed.
		$this->set_closed();
	}

	/**
	 * Add the intro.js-scripts and -styles.
	 *
	 * @source https://introjs.com/docs/examples/basic/hello-world
	 *
	 * @return void
	 */
	public function add_js(): void {
		// embed necessary scripts for dialog.
		$path = Helper::get_plugin_path() . 'node_modules/intro.js/minified/';
		$url  = Helper::get_plugin_url() . 'node_modules/intro.js/minified/';

		// bail if path does not exist.
		if ( ! file_exists( $path ) ) {
			return;
		}

		// embed the JS-script from intro.js.
		wp_enqueue_script(
			'personio-integration-intro-js',
			$url . 'intro.min.js',
			array(),
			filemtime( trailingslashit($path) . 'intro.min.js' ),
			true
		);

		// embed our own JS-script.
		wp_enqueue_script(
			'personio-integration-intro-custom-js',
			Helper::get_plugin_url() . 'admin/intro.js',
			array( 'personio-integration-intro-js', 'personio_integration-admin-js' ),
			filemtime( Helper::get_plugin_path() . '/admin/intro.js' ),
			true
		);

		// embed the CSS-file.
		wp_enqueue_style(
			'personio-integration-intro-js',
			$url. 'introjs.min.css',
			array(),
			filemtime( trailingslashit($path) . 'introjs.min.css' ),
		);

		// embed the CSS-file.
		wp_enqueue_style(
			'personio-integration-intro-custom-js',
			Helper::get_plugin_url() . 'admin/intro.css',
			array(),
			filemtime( Helper::get_plugin_path() . '/admin/intro.css' ),
		);

		// add php-vars to our js-script.
		wp_localize_script(
			'personio-integration-intro-custom-js',
			'personioIntegrationLightIntroJsVars',
			array(
				'ajax_url'                => admin_url( 'admin-ajax.php' ),
				'intro_closed_nonce'      => wp_create_nonce( 'personio-intro-closed' ),
				'button_title_next'       => __( 'Next', 'personio-integration-light' ),
				'button_title_back'       => __( 'Back', 'personio-integration-light' ),
				'button_title_done'       => __( 'Done', 'personio-integration-light' ),
				'step_1_title' => __( 'Intro', 'personio-integration-light' ),
				'step_1_intro' => __( 'Thank you for installing Personio Integration Light. We will show you some basics to use this plugin.', 'personio-integration-light' ),
				'step_2_title' => __( 'Your positions', 'personio-integration-light' ),
				'step_2_intro' => __( 'This is the list of your positions from Personio. This will be updated daily or if you run the import.', 'personio-integration-light' ),
				'step_3_title' => __( 'Change the view', 'personio-integration-light' ),
				'step_3_intro' => __( 'Choose the columns you need in you position list. Which columns are filled depends on the data in your Personio account.', 'personio-integration-light' ),
				'step_4_title' => __( 'Run the import', 'personio-integration-light' ),
				'step_4_intro' => __( 'On this button you could run the import of new Positions any time. They can be displayed immediately afterwards in the frontend to your visitors.', 'personio-integration-light' ),
				'step_5_title' => __( 'Frontend view', 'personio-integration-light' ),
				'step_5_intro' => __( 'Here you will find the link to the positions in your frontend. You can configured the view in the settings.', 'personio-integration-light' ),
				'step_6_title' => __( 'Settings', 'personio-integration-light' ),
				'step_6_intro' => __( 'The settings of this plugin help you to individualize the use of your Personio positions on your website.', 'personio-integration-light' ),
				'step_7_title' => __( 'Thank you for using Personio Integration Light', 'personio-integration-light' ),
				/* translators: %1$s, %2$s and %3$s will be replaced by URLs */
                'step_7_intro' =>  sprintf( __( 'If you have any questions, please do not hesitate to ask them <a href="%1$s" target="_blank">in our forum (opens new window)</a>.<br>You are also welcome to <a href="%2$s" target="_blank">rate the plugin (opens new window)</a>.<br>If you also want to collect applications in your website, take a look at our <a href="%3$s" target="_blank">Personio Integration Pro (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_plugin_support_url() ), esc_url( Helper::get_review_url() ), esc_url( Helper::get_pro_url() ) ),

			)
		);
	}

	/**
	 * Show reset button for intro via Settings.php.
	 *
	 * @return void
	 */
	public static function show_reset_button(): void {
		$false = false;
		/**
		 * Hide intro via hook.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Return true to hide the intro.
		 */
		if( apply_filters( 'personio_integration_templates_archive', $false ) ) {
			echo esc_html__( 'Intro is disabled via custom hook.', 'personio-integration-light' );
		}
		else {
			$url = add_query_arg(
				array(
					'action' => 'personioPositionsIntroReset',
					'nonce'  => wp_create_nonce( 'personio-integration-intro-reset' ),
				),
				get_admin_url() . 'admin.php'
			);
			?><p><a href="<?php echo esc_url( $url ); ?>" class="button button-primary personio-integration-reset-intro"><?php echo esc_html__( 'Rerun the intro', 'personio-integration-light' ); ?></a></p><?php
		}
	}

	/**
	 * Reset intro via request.
	 *
	 * @return void
	 */
	public function reset_intro(): void {
		// check nonce.
		check_ajax_referer( 'personio-integration-intro-reset', 'nonce' );

		// delete the actual setting.
		delete_option( 'personio-integration-intro' );

		// redirect user to intro-start.
		wp_safe_redirect( $this->get_start_url() );
	}

	/**
	 * Return the URL where set setup starts.
	 *
	 * @return string
	 */
	public function get_start_url(): string {
		return PersonioPosition::get_instance()->get_link();
	}
}

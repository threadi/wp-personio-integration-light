<?php
/**
 * File for handling all extensions for our cpt.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Setup;

/**
 * Object to handle different themes to output templates of our plugin.
 */
class Extensions {

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Extensions
	 */
	protected static ?Extensions $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
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
	public static function get_instance(): Extensions {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initialize the extensions.
	 *
	 * @return void
	 */
	public function init(): void {
		// use our own hooks.
		add_filter( 'personio_integration_extend_position_object', array( $this, 'add_extensions' ) );
		add_action( 'esfw_process_init', array( $this, 'initialize_extensions_in_setup' ), 30 );
		add_filter( 'personio_integration_light_help_tabs', array( $this, 'add_help' ), 40 );

		// bail if setup is not completed.
		if ( ! Setup::get_instance()->is_completed() && ! defined( 'PERSONIO_INTEGRATION_UPDATE_RUNNING' ) && ! defined( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING' ) ) {
			return;
		}

		// add AJAX-actions.
		add_action( 'wp_ajax_personio_extension_state', array( $this, 'change_extension_state' ) );

		// add admin-actions.
		add_action( 'admin_action_personio_integration_extension_disable_all', array( $this, 'disable_all_by_request' ) );
		add_action( 'admin_action_personio_integration_extension_enable_all', array( $this, 'enable_all_by_request' ) );

		// misc.
		add_action( 'admin_menu', array( $this, 'add_extension_menu' ) );

		// initialize our extensions.
		$this->initialize_extensions();
	}

	/**
	 * Initialize extensions for this object.
	 *
	 * @return void
	 */
	public function initialize_extensions(): void {
		foreach ( $this->get_extensions_as_objects() as $extension_obj ) {
			$extension_obj->init();

			/**
			 * Run additional action after extension as been initialized.
			 *
			 * @since 4.0.0 Available since 4.0.0.
			 * @param Extensions_Base $extension_obj The extension object.
			 */
			do_action( 'personio_integration_light_extension_initialized', $extension_obj );
		}
	}

	/**
	 * Get extensions as list of Extension_Base-objects.
	 *
	 * @return array
	 */
	public function get_extensions_as_objects(): array {
		// the list of objects.
		$list = array();

		// loop through them.
		foreach ( $this->get_extensions() as $extension_name ) {
			// bail if name is not a string.
			if ( ! is_string( $extension_name ) && $extension_name instanceof Extensions_Base ) {
				$list[] = $extension_name;
				continue;
			}

			// bail if method does not exist.
			if ( ! method_exists( $extension_name, 'get_instance' ) ) {
				continue;
			}

			// bail if method is not callable.
			if ( ! is_callable( $extension_name . '::get_instance' ) ) {
				continue;
			}

			// get the object.
			$list[] = call_user_func( $extension_name . '::get_instance' );
		}

		// return resulting list.
		return $list;
	}

	/**
	 * Initialize extensions for this object on request from setup.
	 *
	 * @param string $config_name The name of the setup-configuration.
	 *
	 * @return void
	 */
	public function initialize_extensions_in_setup( string $config_name ): void {
		// bail if this is not our setup.
		if ( Setup::get_instance()->get_setup_name() !== $config_name ) {
			return;
		}

		$this->initialize_extensions();
	}

	/**
	 * Set list of extensions.
	 *
	 * @param array $extension_list List of extensions.
	 *
	 * @return array
	 */
	public function add_extensions( array $extension_list ): array {
		// add extensions we deliver in this plugin.
		$extension_list[] = '\PersonioIntegrationLight\PersonioIntegration\Availability';
		$extension_list[] = '\PersonioIntegrationLight\PersonioIntegration\Show_Position_Xml';
		$extension_list[] = '\PersonioIntegrationLight\PageBuilder\Page_Builders';

		// return resulting list.
		return $extension_list;
	}

	/**
	 * Return the list of available extensions.
	 *
	 * Hint: list contains only the class-name, not the objects.
	 *
	 * @return array
	 */
	public function get_extensions(): array {
		$list = array();
		/**
		 * Filter the possible extensions.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $list List of extensions.
		 */
		return apply_filters( 'personio_integration_extend_position_object', $list );
	}

	/**
	 * Change extension state via request.
	 *
	 * @return void
	 */
	public function change_extension_state(): void {
		// check none.
		check_ajax_referer( 'personio-integration-extension-state', 'nonce' );

		// get the name of the extension to change.
		$extension_name = filter_input( INPUT_POST, 'extension', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! empty( $extension_name ) ) {
			// get the extension object.
			$obj = $this->get_extension_by_name( $extension_name );
			if ( $obj instanceof Extensions_Base ) {
				// toggle the state.
				$obj->toggle_state();

				// return success-message.
				wp_send_json(
					array(
						'success' => true,
						'enabled' => $obj->is_enabled(),
						'title'   => $obj->is_enabled() ? __( 'Enabled', 'personio-integration-light' ) : __( 'Disabled', 'personio-integration-light' ),
					)
				);
			}
		}

		// return error in response.
		wp_send_json( array( 'success' => false ) );
	}

	/**
	 * Get an extension by its internal name.
	 *
	 * @param string $name The searched name.
	 *
	 * @return Extensions_Base|false
	 */
	private function get_extension_by_name( string $name ): Extensions_Base|false {
		foreach ( $this->get_extensions_as_objects() as $extension_obj ) {
			// bail if name does not match.
			if ( $extension_obj->get_name() !== $name ) {
				continue;
			}

			// return this object.
			return $extension_obj;
		}
		return false;
	}

	/**
	 * Add the extension menu to manage extension for this cpt.
	 *
	 * @return void
	 */
	public function add_extension_menu(): void {
		// add main menu as setup entry.
		add_submenu_page(
			PersonioPosition::get_instance()->get_link( true ),
			__( 'Personio Integration Light Extensions', 'personio-integration-light' ),
			__( 'Extensions', 'personio-integration-light' ),
			'manage_' . PersonioPosition::get_instance()->get_name(),
			'personioPositionExtensions',
			array( $this, 'display' ),
			160
		);
	}

	/**
	 * Get URL for backend list of extensions.
	 *
	 * @param string $category The category (optional).
	 *
	 * @return string
	 */
	public function get_link( string $category = '' ): string {
		$query = array(
			'post_type' => PersonioPosition::get_instance()->get_name(),
			'page'      => 'personioPositionExtensions',
		);

		if ( ! empty( $category ) ) {
			$query['category'] = $category;
		}

		// return resulting URL.
		return add_query_arg(
			$query,
			get_admin_url() . 'edit.php'
		);
	}

	/**
	 * Show list of extensions to manage.
	 *
	 * @return void
	 */
	public function display(): void {
		// get table for extensions.
		$extensions_table = new Tables\Extensions();
		$extensions_table->prepare_items();

		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html__( 'Extensions for Personio Integration', 'personio-integration-light' ); ?></h1>
			<div>
				<?php
				$extensions_table->views();
				$extensions_table->display();
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Disable all extensions which could be enabled by user via request.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function disable_all_by_request(): void {
		check_admin_referer( 'personio-integration-extension-disable-all', 'nonce' );

		// loop through all extensions and enable them.
		foreach ( $this->get_extensions_as_objects() as $extension_obj ) {
			// bail if this extension could not be disabled by user.
			if ( ! $extension_obj->can_be_enabled_by_user() ) {
				continue;
			}

			// disable this extension.
			$extension_obj->set_disabled();
		}

		// redirect user.
		wp_safe_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * Enable all extensions which could be enabled by user via request.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function enable_all_by_request(): void {
		check_admin_referer( 'personio-integration-extension-enable-all', 'nonce' );

		$this->enable_all();

		// redirect user.
		wp_safe_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * Enable all extensions which could be enabled by user.
	 *
	 * @return void
	 */
	public function enable_all(): void {
		// loop through all extensions and enable them.
		foreach ( $this->get_extensions_as_objects() as $extension_obj ) {
			// bail if this extension could not be enabled by user.
			if ( ! $extension_obj->can_be_enabled_by_user() ) {
				continue;
			}

			// enable this extension.
			$extension_obj->set_enabled();
		}
	}

	/**
	 * Uninstall all extensions.
	 *
	 * @return void
	 */
	public function uninstall_all(): void {
		foreach ( $this->get_extensions_as_objects() as $extension_obj ) {
			$extension_obj->uninstall();
		}
	}

	/**
	 * Add help for extensions.
	 *
	 * @param array $help_list List of help tabs.
	 *
	 * @return array
	 */
	public function add_help( array $help_list ): array {
		// collect the content for the help.
		$content  = Helper::get_logo_img( true ) . '<h2>' . __( 'Extensions', 'personio-integration-light' ) . '</h2><p>' . __( 'We provide you with a variety of extensions for the plugin. These extend the possibilities you have with the plugin for your vacancies.', 'personio-integration-light' ) . '</p>';
		$content .= '<p><strong>' . __( 'How to use:', 'personio-integration-light' ) . '</strong></p>';
		$content .= '<ol>';
		/* translators: %1$s will be replaced by a URL. */
		$content .= '<li>' . sprintf( __( 'Call up the <a href="%1$s">list of extensions</a>.', 'personio-integration-light' ), esc_url( $this->get_link() ) ) . '</li>';
		$content .= '<li>' . __( 'Activate the extension you require by clicking on the button provided.', 'personio-integration-light' ) . '</li>';
		$content .= '<li>' . __( 'Check whether the extension still offers settings. Follow the instructions that are displayed.', 'personio-integration-light' ) . '</li>';
		$false    = false;
		/**
		 * Hide pro hint in help.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param array $false Set true to hide the buttons.
		 *                     @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( ! apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			/* translators: %1$s will be replaced by a URL. */
			$content .= '<li>' . sprintf( __( '<a href="%1$s" target="_blank">Order Personio Integration Pro (opens new window)</a> to get much more extensions.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) . '</li>';
		}
		$content .= '</ol>';

		// add help for the positions in general.
		$help_list[] = array(
			'id'      => PersonioPosition::get_instance()->get_name() . '-extensions',
			'title'   => __( 'Extensions', 'personio-integration-light' ),
			'content' => $content,
		);

		// return resulting list.
		return $help_list;
	}
}

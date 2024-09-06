<?php
/**
 * File for handling all extensions for our cpt.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

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
		add_action( 'wp_easy_setup_process_init', array( $this, 'initialize_extensions_in_setup' ), 30 );

		if ( ! Setup::get_instance()->is_completed() && ! defined( 'PERSONIO_INTEGRATION_UPDATE_RUNNING' ) && ! defined( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING' ) ) {
			return;
		}

		// add AJAX-actions.
		add_action( 'wp_ajax_personio_extension_state', array( $this, 'change_extension_state' ) );

		// add admin-actions.
		add_action( 'admin_action_personio_integration_extension_disable_all', array( $this, 'disable_all' ) );
		add_action( 'admin_action_personio_integration_extension_enable_all', array( $this, 'enable_all' ) );

		// misc.
		add_action( 'admin_menu', array( $this, 'add_extension_menu' ) );

		// initialize our extensions for the positions-cpt.
		$this->initialize_extensions();
	}

	/**
	 * Initialize extensions for this object.
	 *
	 * @return void
	 */
	public function initialize_extensions(): void {
		foreach ( $this->get_extensions() as $extension_name ) {
			if ( is_string( $extension_name ) && method_exists( $extension_name, 'get_instance' ) && is_callable( $extension_name . '::get_instance' ) ) {
				$obj = call_user_func( $extension_name . '::get_instance' );
				$obj->init();
			}
		}
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
	 * Set list of extension we use for our position object.
	 *
	 * @param array $extension_list List of extensions for the Position-object.
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
		 * Filter the possible extensions for the Personio-object.
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
		foreach ( $this->get_extensions() as $extension_name ) {
			if ( method_exists( $extension_name, 'get_instance' ) && is_callable( $extension_name . '::get_instance' ) ) {
				$obj = call_user_func( $extension_name . '::get_instance' );
				if ( $obj instanceof Extensions_Base && $obj->get_name() === $name ) {
					return $obj;
				}
			}
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
	 * Disable all extensions via request.
	 *
	 * @return void
	 */
	public function disable_all(): void {
		check_admin_referer( 'personio-integration-extension-disable-all', 'nonce' );

		// loop through all extensions and enable them.
		foreach ( $this->get_extensions() as $extension_name ) {
			if ( is_string( $extension_name ) && method_exists( $extension_name, 'get_instance' ) && is_callable( $extension_name . '::get_instance' ) ) {
				$obj = call_user_func( $extension_name . '::get_instance' );
				if ( $obj instanceof Extensions_Base ) {
					$obj->set_disabled();
				}
			} elseif ( $extension_name instanceof Extensions_Base ) {
				$extension_name->set_disabled();
			}
		}

		// redirect user.
		wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
		exit;
	}

	/**
	 * Disable all extensions via request.
	 *
	 * @return void
	 */
	public function enable_all(): void {
		check_admin_referer( 'personio-integration-extension-enable-all', 'nonce' );

		// loop through all extensions and enable them.
		foreach ( $this->get_extensions() as $extension_name ) {
			if ( is_string( $extension_name ) && method_exists( $extension_name, 'get_instance' ) && is_callable( $extension_name . '::get_instance' ) ) {
				$obj = call_user_func( $extension_name . '::get_instance' );
				if ( $obj instanceof Extensions_Base ) {
					$obj->set_enabled();
				}
			} elseif ( $extension_name instanceof Extensions_Base ) {
				$extension_name->set_enabled();
			}
		}

		// redirect user.
		wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
		exit;
	}

	/**
	 * Uninstall all extensions.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		foreach ( $this->get_extensions() as $extension_name ) {
			if ( is_string( $extension_name ) && method_exists( $extension_name, 'get_instance' ) && is_callable( $extension_name . '::get_instance' ) ) {
				$obj = call_user_func( $extension_name . '::get_instance' );
				$obj->uninstall();
			}
		}
	}
}

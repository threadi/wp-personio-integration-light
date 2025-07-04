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
use PersonioIntegrationLight\Plugin\Transients;

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
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
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

		// add AJAX-actions.
		add_action( 'wp_ajax_personio_extension_state', array( $this, 'change_extension_state' ) );

		// add admin-actions.
		add_action( 'admin_action_personio_integration_extension_disable_all', array( $this, 'disable_all_by_request' ) );
		add_action( 'admin_action_personio_integration_extension_enable_all', array( $this, 'enable_all_by_request' ) );
		add_action( 'admin_action_personio_integration_change_extension_state', array( $this, 'change_extension_state_by_request' ) );

		// misc.
		add_action( 'admin_menu', array( $this, 'add_extension_menu' ) );

		// initialize our extensions.
		$this->initialize_extensions();
	}

	/**
	 * Initialize extensions for this plugin.
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
	 * Initialize extensions during plugin activation.
	 *
	 * @return void
	 */
	public function activation(): void {
		foreach ( $this->get_extensions_as_objects() as $extension_obj ) {
			// initialize the main settings for this extension.
			$extension_obj->add_global_settings();

			// and the custom settings for each extension.
			$extension_obj->add_settings();
		}
	}

	/**
	 * Get extensions as list of Extension_Base-objects.
	 *
	 * @return array<Extensions_Base>
	 */
	public function get_extensions_as_objects(): array {
		// the list of objects.
		$list = array();

		// loop through them.
		foreach ( $this->get_extensions() as $extension_name ) {
			// bail if name is not a string.
			if ( $extension_name instanceof Extensions_Base ) {
				$list[] = $extension_name;
				continue;
			}

			// bail if method does not exist.
			if ( ! method_exists( $extension_name, 'get_instance' ) ) {
				continue;
			}

			// get object name.
			$obj_name = $extension_name . '::get_instance';

			// bail if method is not callable.
			if ( ! is_callable( $obj_name ) ) {
				continue;
			}

			// get the object.
			$list[] = $obj_name();
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
	 * Set list of extensions we deliver in Light.
	 *
	 * @param array<string> $extension_list List of extensions.
	 *
	 * @return array<string>
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
	 * Return the list of all available extensions.
	 *
	 * Hint: list contains only the class-name, not the objects.
	 *
	 * @return array<string|Extensions_Base>
	 */
	public function get_extensions(): array {
		$list = array();
		/**
		 * Filter the possible extensions.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array<string|Extensions_Base> $list List of extensions.
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

		// bail if no extension name was set.
		if ( empty( $extension_name ) ) {
			wp_send_json_error(
				array(
					'detail' =>
						array(
							'title'   => __( 'No extension given', 'personio-integration-light' ),
							'texts'   => array(
								'<p>' . __( 'Please check the request you sent and try it again.', 'personio-integration-light' ) . '</p>',
							),
							'buttons' => array(
								array(
									'action'  => 'closeDialog();',
									'variant' => 'primary',
									'text'    => __( 'OK', 'personio-integration-light' ),
								),
							),
						),
				)
			);
		}

		// get the extension object.
		$obj = $this->get_extension_by_name( $extension_name );

		// bail if extension could not be found.
		if ( ! $obj instanceof Extensions_Base ) {
			wp_send_json_error(
				array(
					'detail' =>
						array(
							'title'   => __( 'Extension not found', 'personio-integration-light' ),
							'texts'   => array(
								'<p>' . __( 'Please check the request you sent and try it again.', 'personio-integration-light' ) . '</p>',
							),
							'buttons' => array(
								array(
									'action'  => 'closeDialog();',
									'variant' => 'primary',
									'text'    => __( 'OK', 'personio-integration-light' ),
								),
							),
						),
				)
			);
		}

		// toggle the state of the extension (this triggers extension-own handlers).
		$obj->toggle_state();

		// return success-message depending on the new extension state.
		$title        = __( 'Extension has been disabled', 'personio-integration-light' );
		$text         = array(
			/* translators: %1$s will be replaced by the name of the extension. */
			'<p>' . sprintf( __( 'The extension %1$s has been disabled.', 'personio-integration-light' ), '<i>' . esc_html( $obj->get_label() ) . '</i>' ) . '</p>',
		);
		$button_title = __( 'Disabled', 'personio-integration-light' );
		if ( $obj->is_enabled() ) {
			$title        = __( 'Extension has been enabled', 'personio-integration-light' );
			$text         = array(
				/* translators: %1$s will be replaced by the name of the extension. */
				'<p><strong>' . sprintf( __( 'The extension %1$s has been successfully enabled.', 'personio-integration-light' ), '<i>' . esc_html( $obj->get_label() ) . '</i>' ) . '</strong></p>',
			);
			if( ! empty( $obj->get_setting_sub_tab() ) ) {
				/* translators: %1$s will be replaced by a URL. */
				$text[] = '<p>' . sprintf( __( 'Now <a href="%1$s">go to the settings</a> to configure the extension.', 'personio-integration-light' ), esc_url( $obj->get_settings_link() ) ) . '</p>';
			}
			$button_title = __( 'Enabled', 'personio-integration-light' );
		}

		// create the dialog for the answer.
		$dialog = array(
			'title'   => $title,
			'texts'   => $text,
			'buttons' => array(
				array(
					'action'  => 'closeDialog();',
					'variant' => 'primary',
					'text'    => __( 'OK', 'personio-integration-light' ),
				),
			),
		);

		/**
		 * Filter the success dialog if state of extension has been changed.
		 */
		$dialog = apply_filters( 'personio_integration_light_extension_state_changed_dialog', $dialog, $obj );

		if ( $obj->is_enabled() ) {
			// send the answer.
			wp_send_json_success(
				array(
					'detail'       => $dialog,
					'button_title' => $button_title,
				)
			);
		}

		// send the answer.
		wp_send_json_error(
			array(
				'detail'       => $dialog,
				'button_title' => $button_title,
			)
		);
	}

	/**
	 * Change extension state via request.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function change_extension_state_by_request(): void {
		// check none.
		check_admin_referer( 'personio-integration-extension-state', 'nonce' );

		// get transients as object.
		$transients_obj = Transients::get_instance();

		// get the name of the extension to change.
		$extension_name = filter_input( INPUT_GET, 'extension', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// bail if no extension name is given.
		if ( empty( $extension_name ) ) {
			// show error.
			$transient_obj = $transients_obj->add();
			$transient_obj->set_name( 'personio_integration_extension_toggle_state' );
			$transient_obj->set_type( 'error' );
			$transient_obj->set_message( __( 'Error when calling the status change of an extension! Extension not specified.', 'personio-integration-light' ) );
			$transient_obj->save();

			// redirect user.
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// get the extension object.
		$obj = $this->get_extension_by_name( $extension_name );

		// bail if object could not be loaded.
		if ( ! $obj instanceof Extensions_Base ) {
			// show error.
			$transient_obj = $transients_obj->add();
			$transient_obj->set_name( 'personio_integration_extension_toggle_state' );
			$transient_obj->set_type( 'error' );
			/* translators: %1$s will be replaced by a name. */
			$transient_obj->set_message( sprintf( __( 'Error when calling the status change of an extension! Given extension %1$s could not be loaded.', 'personio-integration-light' ), $extension_name ) );
			$transient_obj->save();

			// redirect user.
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// toggle the state of the extension (this triggers extension-own handlers).
		$obj->toggle_state();

		// show ok message.
		$transient_obj = $transients_obj->add();
		$transient_obj->set_name( 'personio_integration_extension_toggle_state' );
		$transient_obj->set_type( 'success' );
		/* translators: %1$s will be replaced by a name, %2$s by "enabled" or "disabled". */
		$transient_obj->set_message( sprintf( __( 'The extension %1$s has been %2$s.', 'personio-integration-light' ), $obj->get_label(), $obj->is_enabled() ? __( 'enabled', 'personio-integration-light' ) : __( 'disabled', 'personio-integration-light' ) ) );
		$transient_obj->save();

		// redirect user.
		wp_safe_redirect( wp_get_referer() );
		exit;
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
		// only if Setup has been completed.
		if( ! Setup::get_instance()->is_completed() ) {
			return;
		}

		// add main menu as setup entry.
		add_submenu_page(
			PersonioPosition::get_instance()->get_link( true ),
			__( 'Personio Integration Light Extensions', 'personio-integration-light' ),
			__( 'Extensions', 'personio-integration-light' ),
			'manage_' . PersonioPosition::get_instance()->get_name(),
			'personioPositionExtensions',
			array( $this, 'display' ),
			2
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
	 * @param array<int,array<string,string>> $help_list List of help tabs.
	 *
	 * @return array<int,array<string,string>>
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
		 * Hide hint for Pro-plugin.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Set true to hide the hint.
		 * @noinspection PhpConditionAlreadyCheckedInspection
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

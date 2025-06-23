<?php
/**
 * File with main handler for all emails this plugin will generate.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Page;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Emails\DeletedPositions;
use PersonioIntegrationLight\Plugin\Emails\NewPositions;

/**
 * Object for all emails this plugin will generate.
 */
class Emails {
	/**
	 * Instance of this object.
	 *
	 * @var ?Emails
	 */
	private static ?Emails $instance = null;

	/**
	 * Constructor for this object.
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
	public static function get_instance(): Emails {
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
		// add email settings.
		add_action( 'init', array( $this, 'add_settings' ), 20 );

		// use our own hooks.
		add_filter( 'personio_integration_light_help_tabs', array( $this, 'add_emails_help' ), 80 );
		add_filter( 'personio_integration_log_categories', array( $this, 'add_log_categories' ) );
		add_action( 'personio_integration_import_finished', array( $this, 'trigger_new_positions' ), 10, 0 );
		add_action( 'personio_integration_import_finished', array( $this, 'trigger_deleted_positions' ), 10, 0 );
		add_filter( 'personio_integration_schedules', array( $this, 'add_schedules' ) );

		// use actions.
		add_action( 'admin_action_personioPositionsEmailTest', array( $this, 'send_test_email_by_request' ) );

		// add our email template.
		add_filter('wp_mail', array( $this, 'set_email_template' ) );
	}

	/**
	 * Add our settings for emails.
	 *
	 * @return void
	 */
	public function add_settings(): void {
		// get settings object.
		$settings_obj = \PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings::get_instance();

		// get main settings page.
		$settings_page = $settings_obj->get_page( 'personioPositions' );

		// bail if page does not exist.
		if( ! $settings_page instanceof Page ) {
			return;
		}

		// the email tab.
		$email_tab = $settings_page->add_tab( 'emails', 80 );
		$email_tab->set_title( __( 'Emails', 'personio-integration-light' ) );

		// add setting for each supported trigger.
		foreach( $this->get_email_trigger() as $email_class_name ) {
			// bail if it is not a string.
			if( ! is_string( $email_class_name ) ) {
				continue;
			}

			// bail if class does not exist.
			if( ! class_exists( $email_class_name ) ) {
				continue;
			}

			// get the object.
			$obj = new $email_class_name();

			// bail if object is not Email_Base.
			if( ! $obj instanceof Email_Base ) {
				continue;
			}

			// get settings.
			$obj->add_settings( $settings_obj, $email_tab );
		}
	}

	/**
	 * Return list of all available email trigger.
	 *
	 * @return array<int,string>
	 */
	private function get_email_trigger(): array {
		$trigger = array(
			'\PersonioIntegrationLight\Plugin\Emails\DeletedPositions',
			'\PersonioIntegrationLight\Plugin\Emails\ImportError',
			'\PersonioIntegrationLight\Plugin\Emails\NewPositions',
			'\PersonioIntegrationLight\Plugin\Emails\Report',
		);

		/**
		 * Filter the list of possible email objects.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param array<int,string> $trigger List of Email trigger objects.
		 */
		return apply_filters( 'personio_integration_light_emails', $trigger );
	}

	/**
	 * Return the email object which as the given name.
	 *
	 * @param string $name The name.
	 *
	 * @return Email_Base|false
	 */
	private function get_email_trigger_by_name( string $name ): Email_Base|false {
		// add setting for each supported trigger.
		foreach( $this->get_email_trigger() as $email_class_name ) {
			// bail if it is not a string.
			if ( ! is_string( $email_class_name ) ) {
				continue;
			}

			// bail if class does not exist.
			if ( ! class_exists( $email_class_name ) ) {
				continue;
			}

			// get the object.
			$obj = new $email_class_name();

			// bail if object is not Email_Base.
			if( ! $obj instanceof Email_Base ) {
				continue;
			}

			// bail if name does not match.
			if( $name !== $obj->get_name() ) {
				continue;
			}

			// return this object.
			return $obj;
		}

		return false;
	}

	/**
	 * Add email category
	 *
	 * @param array<string,string> $categories List of categories.
	 *
	 * @return array<string,string>
	 */
	public function add_log_categories( array $categories ): array {
		// add categories we need for our settings.
		$categories['emails'] = __( 'Emails', 'personio-integration-light' );

		// return resulting list.
		return $categories;
	}

	/**
	 * Trigger the email with info about new imported positions.
	 *
	 * @return void
	 */
	public function trigger_new_positions(): void {
		// get the actual list of new position from this import.
		$new_positions = get_option( WP_PERSONIO_INTEGRATION_IMPORT_NEW_POSITIONS, array() );

		// send email.
		$email = new NewPositions();
		$email->set_new_positions( $new_positions );
		$email->send();
	}

	/**
	 * Trigger the email with info about new imported positions.
	 *
	 * @return void
	 */
	public function trigger_deleted_positions(): void {
		// get the actual list of new position from this import.
		$deleted_positions = get_option( WP_PERSONIO_INTEGRATION_IMPORT_DELETED_POSITIONS, array() );

		// bail if no new positions have been imported.
		if( empty( $deleted_positions ) ) {
			return;
		}

		// send email.
		$email = new DeletedPositions();
		$email->set_deleted_positions( $deleted_positions );
		$email->send();
	}

	/**
	 * Add help for using emails.
	 *
	 * @param array<int,array<string,mixed>> $help_list List of help tabs.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public function add_emails_help( array $help_list ): array {
		// collect the content for the help.
		$content  = Helper::get_logo_img( true ) . '<h2>' . __( 'Emails', 'personio-integration-light' ) . '</h2><p>' . __( 'We enable you to advertise your jobs on your own website. Applicants can find them and apply for them.', 'personio-integration-light' ) . '</p>';
		$content .= '<p><strong>' . __( 'How to use emails:', 'personio-integration-light' ) . '</strong></p>';
		$content .= '<ol>';
		$content .= '<li>' . __( 'Take a look at the list of emails in the settings.', 'personio-integration-light' ) . '</li>';
		$content .= '<li>' . __( 'Enable the emails you want to get.', 'personio-integration-light' ) . '</li>';
		$content .= '<li>' . __( 'Set one or more recipients for this email.', 'personio-integration-light' ) . '</li>';
		$content .= '</ol>';
		$content .= '<p><strong>' . __( 'Important notes:', 'personio-integration-light' ) . '</strong></p>';
		$content .= '<ul>';
		$content .= '<li>' . sprintf( __( 'Check and test whether you can send emails to the recipients from your project. This depends on many factors that our plugin does not influence. <a href="%1$s" target="_blank">SMTP plugins (opens new window)</a> may help here.', 'personio-integration-light' ), 'https://wordpress.org/plugins/tags/smtp/' ) . '</li>';
		$content .= '</ul>';

		// add help for the positions in general.
		$help_list[] = array(
			'id'      => PersonioPosition::get_instance()->get_name() . '-emails',
			'title'   => __( 'Emails', 'personio-integration-light' ),
			'content' => $content,
		);

		// return resulting list.
		return $help_list;
	}

	/**
	 * Add our own schedule to the list.
	 *
	 * @param array<string> $list_of_schedules List of schedules.
	 *
	 * @return array<string>
	 */
	public function add_schedules( array $list_of_schedules  ): array {
		// add the schedule-objekt, if report is enabled.
		$list_of_schedules[] = '\PersonioIntegrationLight\Plugin\Schedules\Report';

		// return resulting list.
		return $list_of_schedules;
	}

	/**
	 * Send test-email by request.
	 *
	 * @return void
	 */
	public function send_test_email_by_request(): void {
		// check referer.
		check_admin_referer( 'personio-integration-email-test', 'nonce' );

		// get the object name.
		$email_object_name = filter_input( INPUT_GET, 'object', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// bail if no object name is given.
		if( empty( $email_object_name ) ) {
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// get the object.
		$email_obj = $this->get_email_trigger_by_name( $email_object_name );

		// bail if no object could be found.
		if( ! $email_obj instanceof Email_Base ) {
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// trigger the test-email of this object.
		$email_obj->send_test();

		// add hint.
		$transient_obj = Transients::get_instance()->add();
		$transient_obj->set_name( 'personio_integration_light_email_testmail' );
		$transient_obj->set_type( 'success' );
		/* translators: %1$s will be replaced by a name. */
		$transient_obj->set_message( sprintf( __( 'Test-Email has been send. Check now your inbox in %1$s.', 'personio-integration-light' ), implode( ', ', $email_obj->get_recipients() ) ) );
		$transient_obj->save();

		// let the user return.
		wp_safe_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * Add our own email template for emails we send.
	 *
	 * @param mixed $args
	 *
	 * @return mixed
	 */
	public function set_email_template( mixed $args ): mixed {
		// bail if args is not an array.
		if( ! is_array( $args ) ) {
			return $args;
		}

		// bail if no header is set.
		if( ! isset( $args['headers'] ) ) {
			return $args;
		}

		// bail if header "X-Mailer" is not set.
		if( ! isset( $args['headers']['X-Mailer'] ) ) {
			return $args;
		}

		// bail if header "X-Mailer" is not our plugin.
		if( $args['headers']['X-Mailer'] !== Helper::get_plugin_name() ) {
			return $args;
		}

		// bail if email is already HTML.
		if( str_contains($args["message"], '<html') || str_contains($args["message"], '<HTML') || str_contains($args["message"], '<body') ) {
			return $args;
		}

		// get the contents.
		$subject = $args["subject"];
		$body = wpautop( $args["message"] );

		// load your custom email template file.
		ob_start();
		include Templates::get_instance()->get_template( 'emails/default.php' );
		$body = ob_get_clean();
		if( ! $body ) {
			return $args;
		}

		// set the new HTML-formatted body.
		$args["message"] = $body;

		// return resulting mail configuration.
		return $args;
	}
}

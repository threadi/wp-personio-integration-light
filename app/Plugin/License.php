<?php
/**
 * File for handling of license-input of Pro-plugin and info-texts about it.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transients;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use WP_Screen;

/**
 * Handling for license-input of Pro-plugin and info-texts about it.
 */
class License {
	/**
	 * The license key.
	 *
	 * @var string
	 */
	private string $key = '';

	/**
	 * Instance of this object.
	 *
	 * @var ?License
	 */
	private static ?License $instance = null;

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
	public static function get_instance(): License {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		$false    = false;
		/**
		 * Hide hint for Pro-plugin.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Set true to hide the hint.
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			return;
		}

		// use hooks.
		add_action( 'admin_menu', array( $this, 'add_menu' ), 20 );
		add_action( 'admin_action_personio_integration_light_check_pro_key', array( $this, 'check_by_request' ) );
		add_action( 'admin_action_personio_integration_light_install_pro', array( $this, 'install_by_request' ) );
		add_action( 'admin_action_personio_integration_light_acknowledge_costs_loading', array( $this, 'acknowledge_costs_loading_by_request' ) );
		add_action( 'admin_action_personio_integration_light_revoke_acknowledge_costs_loading', array( $this, 'revoke_acknowledge_costs_loading_by_request' ) );

		global $wp_version;
		if ( version_compare( $wp_version, '5.1.0', '>' ) ) {
			add_filter( 'http_request_reject_unsafe_urls', array( $this, 'allow_own_safe_domain' ), 10, 2 );
		} else {
			add_filter( 'http_request_reject_unsafe_urls', '__return_false' );
		}
	}

	/**
	 * Add the menu entry.
	 *
	 * @return void
	 */
	public function add_menu(): void {
		// add Pro link.
		add_submenu_page(
			PersonioPosition::get_instance()->get_link( true ),
			__( 'Enable Personio Integration Pro', 'personio-integration-light' ),
			__( 'Enable Pro', 'personio-integration-light' ),
			'manage_options',
			'personioPositionsPro',
			array( $this, 'show_pro_page' ),
			10
		);
	}

	/**
	 * Add the boxes for the Pro info page.
	 *
	 * Which boxes are visible depends on the state:
	 * - Pro is not installed => Show where to buy.
	 * - Pro is installed but not active => Activate plugin and start license setup.
	 * - Pro is installed and active but license is not enabled => start license setup.
	 *
	 * Hint: this page is not visible with enabled Pro license.
	 *
	 * @return void
	 */
	private function add_meta_boxes(): void {
		// show box to enter the license key.
		add_meta_box(
			PersonioPosition::get_instance()->get_name() . '-pro-infos',
			__( 'Your benefits', 'personio-integration-light' ),
			array( $this, 'show_pro_infos' ),
			get_current_screen(),
			'normal'
		);

		// show box with the actual costs.
		add_meta_box(
			PersonioPosition::get_instance()->get_name() . '-pro-costs',
			__( 'Costs', 'personio-integration-light' ),
			array( $this, 'show_pro_licence_costs' ),
			get_current_screen(),
			'normal'
		);

		// show box to enter the license key.
		add_meta_box(
			PersonioPosition::get_instance()->get_name() . '-pro-license-key',
			__( 'Enter your license', 'personio-integration-light' ),
			array( $this, 'show_pro_licence_key_field' ),
			get_current_screen(),
			'side'
		);
	}

	/**
	 * Show the Pro page.
	 *
	 * @return void
	 */
	public function show_pro_page(): void {
		// add the boxes.
		$this->add_meta_boxes();

		// get screen.
		$screen = get_current_screen();

		// bail if screen could not be loaded.
		if ( ! $screen instanceof WP_Screen ) {
			return;
		}

		// output.
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<?php
						do_meta_boxes( $screen, 'normal', null );
						?>
					</div>
					<div id="postbox-container-1" class="postbox-container">
						<?php
						do_meta_boxes( $screen, 'side', null );
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Show box to enter the Pro license key.
	 *
	 * @return void
	 */
	public function show_pro_licence_key_field(): void {
		?>
		<p><?php echo esc_html__( 'If you already have a license key, you can enter it here. We will then install Personio Integration Pro as a plugin for you and store your license there so that you can get started right away.', 'personio-integration-light' ); ?></p>
		<form method="post" action="<?php echo esc_url( get_admin_url() ); ?>admin.php">
			<input type="hidden" name="action" value="personio_integration_light_check_pro_key">
			<?php wp_nonce_field( 'personio-integration-license-key' ); ?>
			<div class="personio-integration-field">
				<label for="licence_key"><?php echo esc_html__( 'Licence key', 'personio-integration-light' ); ?>:</label>
				<input type="text" id="licence_key" name="licence_key" value="" required>
				<p>
					<strong><?php echo esc_html__( 'You do not have a key?', 'personio-integration-light' ) ?></strong>
					<?php
					/* translators: %1$s will be replaced by a URL. */
					echo sprintf(__( 'Get one <a href="%1$s" target="_blank">here</a>.', 'personio-integration-light' ), Helper::get_pro_url() );
					?>
				</p>
			</div>
			<div class="personio-integration-field">
				<label for="privacy"><input type="checkbox" id="privacy" name="privacy" value="" required> <?php echo esc_html__( 'I agree that a request containing my domain will be sent to laolaweb.com.', 'personio-integration-light' ); ?></label>
			</div>
			<div class="personio-integration-field">
				<button type="submit" class="button button-primary"><?php echo esc_html__( 'Submit', 'personio-integration-light' ); ?></button>
			</div>
		</form>
		<?php
	}

	/**
	 * Check the given license key.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function check_by_request(): void {
		// check nonce.
		check_admin_referer( 'personio-integration-license-key' );

		// get entered license key.
		$this->key = filter_input( INPUT_POST, 'licence_key', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// bail if no license key is given.
		if( empty( $this->key ) ) {
			// show error message.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_license_request_error' );
			$transient_obj->set_message( __( 'Please enter a license key!', 'personio-integration-light' ) );
			$transient_obj->set_type( 'hint' );
			$transient_obj->save();

			// forward user.
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// send request to our license server to check the key.
		// create body with all necessary values to validate the license key.
		$body = $this->get_data();

		// send request to API to verify the key.
		$query    = array(
			'header' => array(
				'Content-Type' => 'application/json; charset=utf-8',
			),
			'method' => 'POST',
			'body'   => $body,
		);
		$response = wp_remote_post( WP_PERSONIO_INTEGRATION_LIGHT_LICENCE_URL, $query );

		// bail on error.
		if ( is_wp_error( $response ) ) {
			// log this event.
			Log::get_instance()->add( __( 'Following error occurred during the request to the license server:', 'personio-integration-light' ) . ' <code>' . wp_json_encode( $response ) . '</code>', 'error', 'system' );

			// show error message.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_license_request_error' );
			$transient_obj->set_message( __( 'An error occurred during the request to the license server! Check the log for more details.', 'personio-integration-light' ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->save();

			// forward user.
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// get the HTTP status.
		$http_status = absint( wp_remote_retrieve_response_code( $response ) );

		// get the content.
		$response_body  = wp_remote_retrieve_body( $response );
		$response_array = json_decode( $response_body, true );

		// if HTTP response is 400, show error.
		if( 400 === $http_status ) {
			// show error message.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_license_error' );
			$transient_obj->set_message( __( 'Response from license server:', 'personio-integration-light' ) . ' <code>' . esc_html( $response_array['message'] ) . '</code>' );
			$transient_obj->set_type( 'error' );
			$transient_obj->save();

			// forward user.
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// get the body.
		if ( 200 === $http_status ) {
			// create URL to install Pro.
			$url = add_query_arg(
				array(
					'action' => 'personio_integration_light_install_pro',
					'nonce' => wp_create_nonce( 'personio-integration-light-install-pro' ),
					'key' => $this->key
				),
				get_admin_url() . 'admin.php'
			);

			// show success message.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_license_success' );
			$transient_obj->set_message( __( '<strong>The specified license is valid.</strong> Click on the following button so that we can install and configure Personio Integration Pro for you.', 'personio-integration-light' ) . ' <br><br><a href="' . esc_url( $url ) . '" class="button button-primary">' . esc_html__( 'Install now', 'personio-integration-light' ) . '</a>' );
			$transient_obj->set_type( 'success' );
			$transient_obj->save();

			// forward user.
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// show error message.
		$transient_obj = Transients::get_instance()->add();
		$transient_obj->set_name( 'personio_integration_license_error' );
		$transient_obj->set_message( __( 'The test could not be performed. Please try again later.', 'personio-integration-light' ) );
		$transient_obj->set_type( 'error' );
		$transient_obj->save();

		// forward user.
		wp_safe_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * Show the actual Pro costs.
	 *
	 * @return void
	 */
	public function show_pro_licence_costs(): void {
		// get the flag if user has already acknowledged the loading of the costs.
		if( 1 !== absint( get_user_meta( get_current_user_id(), 'personio-integration-acknowledge-costs-loading', true ) ) ) {
			?>
			<form method="post" action="<?php echo esc_url( get_admin_url() ); ?>admin.php">
				<input type="hidden" name="action" value="personio_integration_light_acknowledge_costs_loading">
				<?php wp_nonce_field( 'personio-integration-license-costs' ); ?>
				<div class="personio-integration-field">
					<label for="privacy"><input type="checkbox" id="privacy" name="privacy" value="" required> <?php echo esc_html__( 'I agree that a request will be sent to laolaweb.com to retrieve the current prices.', 'personio-integration-light' ); ?></label>
				</div>
				<div class="personio-integration-field">
					<button type="submit" class="button button-primary"><?php echo esc_html__( 'Submit', 'personio-integration-light' ); ?></button>
				</div>
			</form>
			<img src="<?php echo esc_url( Helper::get_plugin_url() ) . 'gfx/laolaweb-logo.svg'; ?>" alt="">
			<?php
			return;
		}

		// load the costs.
		$url = add_query_arg(
			array(
				'source' => 'personio-integration-light',
				'plugin' => 'personio-integration',
			),
			WP_PERSONIO_INTEGRATION_LIGHT_COSTS_URL
		);

		// get possible periods and the billing address for the license from laolaweb.com.
		$query    = array(
			'header' => array(
				'Content-Type' => 'application/json; charset=utf-8',
			),
			'method' => 'GET',
		);
		$response = wp_remote_get( $url, $query );

		// get the HTTP status.
		$http_status = absint( wp_remote_retrieve_response_code( $response ) );

		// get the content.
		$response_body  = wp_remote_retrieve_body( $response );
		$response_array = json_decode( $response_body, true );

		// bail if HTTP status is not 200.
		if( 200 !== $http_status ) {
			// log this event.
			Log::get_instance()->add( __( 'Following error occurred during the request to the license server:', 'personio-integration-light' ) . ' <code>' . wp_json_encode( $response ) . '</code>', 'error', 'system' );

			// show success message.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_license_costs_error' );
			$transient_obj->set_message( __( 'Error during loading the costs for Personio Integration Pro. Check the log for details.', 'personio-integration-light' ) . ' <a href="' . esc_url( $url ) . '" class="button button-primary">' . esc_html__( 'Install now', 'personio-integration-light' ) . '</a>' );
			$transient_obj->set_type( 'success' );
			$transient_obj->save();

			// forward user.
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// bail if response does not contain "periods".
		if( empty( $response_array['periods'] ) ) {
			// show success message.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_license_costs_error' );
			$transient_obj->set_message( __( 'Got no data for costs from laolaweb.com. Please try again later.', 'personio-integration-light' ) . ' <a href="' . esc_url( $url ) . '" class="button button-primary">' . esc_html__( 'Install now', 'personio-integration-light' ) . '</a>' );
			$transient_obj->set_type( 'success' );
			$transient_obj->save();

			// forward user.
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		?>
		<table>
			<tr>
			<?php
				foreach( $response_array['periods'] as $period ) {
					?>
						<td>
							<?php
								if( ! empty( $period['image'] ) ) {
									?><img src="<?php echo esc_url( $period['image'] ); ?>" alt=""><?php
								}
								else {
								?>
									<h3><?php echo esc_html( $period['label'] ); ?></h3>
								<?php
								}
							?>
						</td>
					<?php
				}
			?>
			</tr>
			<tr>
				<?php
					foreach( $response_array['periods'] as $period ) {
						?>
						<td>
							<a href="<?php echo esc_url( Helper::get_pro_url() ); ?>" title="<?php echo esc_attr( $period['label'] ); ?>" class="button button-primary" target="_blank"><?php echo esc_html__( 'More infos & book', 'personio-integration-light' ); ?></a>
						</td>
						<?php
					}
				?>
			</tr>
		</table>
		<img src="<?php echo esc_url( Helper::get_plugin_url() ) . 'gfx/laolaweb-logo.svg'; ?>" alt="">
		<?php
			// generate URL to revoke the loading of costs.
			$url = add_query_arg(
				array(
					'action' => 'personio_integration_light_revoke_acknowledge_costs_loading',
					'nonce' => wp_create_nonce( 'personio-integration-license-costs-revoke' )
				),
				get_admin_url() . 'admin.php'
			)
		?>
		<a href="<?php echo esc_url( $url ); ?>" class="button button-primary"><?php echo esc_html__( 'Revoke consent to load data', 'personio-integration-light' ); ?></a>
		<?php
	}

	/**
	 * Show the Pro benefits.
	 *
	 * @return void
	 */
	public function show_pro_infos(): void {
		echo Helper::get_logo_img( true );
		?>
			<ul>
				<li><?php echo __( 'Customization of slugs (URLs) for list and detailed views of positions', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Multiple and customizable application forms incl. export of them via Personio API', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Supports all languages Personio offers German, English, French, Spanish, Dutch, Italian, Portuguese, Swedish, Finnish, Polish, Czech', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Support for multilingual plugins Polylang, WPML, Weglot and TranslatePress', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Support for subcompanies and additional offices in positions', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Support for salaries for open positions', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Use GoogleMaps or OpenStreetMap for show you locations with open positions', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Support for multiple form handler like Avada Forms, Contact Form 7, Elementor Forms, Fluent Forms, Forminator and WPForms', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Use custom feature image on each position', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Unlimited custom files for download on each single position', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Support for tracking of events with Google Analytics 4', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Support full text search for positions in frontend', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Multiple Personio-accounts per website', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Additional import settings, e.g. intervals and partial import for very large lists of open positions and removing of inline styles from position descriptions', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'RichSnippets for optimal findability via search engines like Google Jobs', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Support for Open Graph (Facebook, LinkedIn, WhatsApp …), Twitter Cards and Dublin Core (optionally configurable for all or single positions)', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Support to embed positions from your website in other website via oEmbed (optionally configurable for all or single positions)', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Shortcode generator for individual views of lists and details', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Extensions for the following PageBuilders: Avada, Beaver Builder, Divi, Elementor, SiteOrigin (SiteOrigin Widgets Bundle necessary), WPBakery', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Also compatible with Avia (from Enfold) and Kubio AI', 'personio-integration-light' ); ?></li>
				<li><?php echo __( 'Every privacy values are encrypted (e.g. applicant data and API credentials)', 'personio-integration-light' ); ?></li>
				<li><?php echo __( '.. and much more', 'personio-integration-light' ); ?></li>
			</ul>
		<?php
	}

	/**
	 * Collect data to send to laolaweb.com.
	 *
	 * @return array<string,mixed>
	 */
	private function get_data(): array {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$wp_version = '1.0.0';

		// get plugin-data.
		$plugin_light_data = \get_plugin_data( WP_PERSONIO_INTEGRATION_PLUGIN );

		// get wp-version-data.
		require ABSPATH . WPINC . '/version.php';

		// return values as array.
		return array(
			'plugin'               => 'personio-integration',
			'key'                  => $this->key,
			'hash'                 => Crypt::get_instance()->get_method()->get_hash(),
			'domain'               => preg_replace( '(^https?://)', '', get_option( 'siteurl' ) ),
			'plugin_version_light' => $plugin_light_data['Version'],
			'wp_version'           => $wp_version,
		);
	}

	/**
	 * Install Pro plugin by request (only with valid license key).
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function install_by_request(): void {
		// check nonce.
		check_admin_referer( 'personio-integration-light-install-pro', 'nonce' );

		// get entered license key.
		$this->key = filter_input( INPUT_GET, 'key', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// bail if no license key is given.
		if( empty( $this->key ) ) {
			// show error message.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_install_error' );
			$transient_obj->set_message( __( 'No license key given!', 'personio-integration-light' ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->save();

			// forward user.
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// bail if Pro is already installed, but not active.
		if( Helper::is_plugin_installed( 'personio-integration/personio-integration.php' ) ) {
			// activate the plugin.
			require_once ABSPATH . 'wp-admin/includes/admin.php';
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			if( ! is_null( activate_plugin( 'personio-integration/personio-integration.php' ) ) ) {
				// show error message.
				$transient_obj = Transients::get_instance()->add();
				$transient_obj->set_name( 'personio_integration_install_error' );
				$transient_obj->set_message( __( 'Personio Integration Pro is installed, but could not be activated!', 'personio-integration-light' ) );
				$transient_obj->set_type( 'error' );
				$transient_obj->save();

				// forward user.
				wp_safe_redirect( wp_get_referer() );
				exit;
			}

			// set the license key.
			update_option( 'personioIntegrationLicenseKey', $this->key );

			// set the referrer URL.
			$url = wp_get_referer();

			/**
			 * Filter the referer URL after Personio Integration Pro has been installed and activated.
			 *
			 * @since 5.0.0 Available since 5.0.0.
			 * @param string $url The URL to use as forward target.
			 */
			$url = apply_filters( 'personio_integration_light_url_after_pro_installation', $url );

			// forward user.
			wp_safe_redirect( $url );
			exit;
		}

		// download the actual package.
		$response = wp_safe_remote_get(
			WP_PERSONIO_INTEGRATION_PRO_UPDATE_URL,
			array(
				'timeout' => get_option( 'personioIntegrationUrlTimeout' ),
				'headers' => array(
					'Accept' => 'application/json',
				),
			)
		);

		// bail on error.
		if ( is_wp_error( $response ) ) {
			// log this event.
			Log::get_instance()->add( __( 'Following error occurred during the request for actual Pro package:', 'personio-integration-light' ) . ' <code>' . wp_json_encode( $response ) . '</code>', 'error', 'system' );

			// show error message.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_install_error' );
			$transient_obj->set_message( __( 'An error occurred during the request to the license server! Check the log for more details.', 'personio-integration-light' ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->save();

			// forward user.
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// get the content.
		$response_body  = wp_remote_retrieve_body( $response );
		$response_array = json_decode( $response_body, true );

		// bail if "download_url" is not given.
		if( empty( $response_array['download_url'] ) ) {
			// log this event.
			Log::get_instance()->add( __( 'Faulty response from install server: no download URL given.', 'personio-integration-light' ), 'error', 'system' );

			// show error message.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_install_error' );
			$transient_obj->set_message( __( 'An error occurred during the request to the license server! Check the log for more details.', 'personio-integration-light' ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->save();

			// forward user.
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// get the download URL.
		$download_url = $response_array['download_url'];

		/**
		 * Filter the download URL during installation of Personio Integration Pro.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param string $download_url The download URL.
		 */
		$download_url = apply_filters( 'personio_integration_light_download_pro_url', $download_url );

		// install and activate the plugin.
		if( ! is_null( Helper::install_plugin( $download_url, 'personio-integration' ) ) ) {
			// show error message.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_install_error' );
			$transient_obj->set_message( __( '<strong>Error during installing Personio Integration Pro!</strong> Please check the logs for more details.', 'personio-integration-light' ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->save();

			// forward user.
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// set the license key.
		update_option( 'personioIntegrationLicenseKey', $this->key );

		// set the referrer URL.
		$url = wp_get_referer();

		/**
		 * Filter the referer URL after Personio Integration Pro has been installed and activated.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param string $url The URL to use as forward target.
		 */
		$url = apply_filters( 'personio_integration_light_url_after_pro_installation', $url );

		// forward user.
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Allow our license-url for requests during update.
	 *
	 * @param bool   $return_value True if the domain in the URL is safe.
	 * @param string $url The requested URL.
	 *
	 * @return bool
	 */
	public function allow_own_safe_domain( bool $return_value, string $url ): bool {
		if ( strpos( $url, wp_parse_url( WP_PERSONIO_INTEGRATION_LIGHT_LICENCE_URL, PHP_URL_HOST ) ) ) {
			return true;
		}
		return $return_value;
	}

	/**
	 * Save on user settings that he acknowledged to load the costs from laolaweb.com.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function acknowledge_costs_loading_by_request(): void {
		// check nonce.
		check_admin_referer( 'personio-integration-license-costs' );

		// save the data.
		update_user_meta( get_current_user_id(), 'personio-integration-acknowledge-costs-loading', 1 );

		// forward user.
		wp_safe_redirect( wp_get_referer() . '#personioposition-pro-costs' );
		exit;
	}

	/**
	 * Revoke in user settings that he acknowledged to load the costs from laolaweb.com.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function revoke_acknowledge_costs_loading_by_request(): void {
		// check nonce.
		check_admin_referer( 'personio-integration-license-costs-revoke', 'nonce' );

		// save the data.
		delete_user_meta( get_current_user_id(), 'personio-integration-acknowledge-costs-loading', 1 );

		// forward user.
		wp_safe_redirect( wp_get_referer() . '#personioposition-pro-costs' );
		exit;
	}
}

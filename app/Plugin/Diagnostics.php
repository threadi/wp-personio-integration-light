<?php
/**
 * File to handle diagnostic tools.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use easySettingsForWordPress\Page;
use easySettingsForWordPress\Tab;
use PersonioIntegrationLight\Helper;
use TcpAnalyzer\TcpAnalyzer;

/**
 * Object to handle diagnostic tools.
 */
class Diagnostics {
	/**
	 * The results as array.
	 *
	 * @var array<string,mixed>
	 */
	private array $results = array();

	/**
	 * Instance of this object.
	 *
	 * @var ?Diagnostics
	 */
	private static ?Diagnostics $instance = null;

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
	public static function get_instance(): Diagnostics {
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
		add_action( 'init', array( $this, 'add_settings' ), 30 );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_css_and_js' ) );
		add_action( 'wp_ajax_personio_integration_light_run_diagnostics', array( $this, 'run_diagnostics_via_ajax' ) );
	}

	/**
	 * Add the styles and scripts.
	 *
	 * @param string $hook The requested hook.
	 *
	 * @return void
	 */
	public function add_css_and_js( string $hook ): void {
		// bail if our hook is not loaded.
		if ( 'personioposition_page_personioPositions' !== $hook ) {
			return;
		}

		// add script.
		wp_enqueue_script(
			'personio-integration-light-diagnostics',
			Helper::get_plugin_url() . 'admin/diagnostics.js',
			array(),
			Helper::get_file_version( Helper::get_plugin_path() . 'admin/diagnostics.js' ),
			true
		);

		// add style.
		wp_enqueue_style(
			'personio-integration-light-diagnostics',
			Helper::get_plugin_url() . 'admin/diagnostics.css',
			array(),
			Helper::get_file_version( Helper::get_plugin_path() . '/admin/diagnostics.css' ),
		);

		// add php-vars to our js-script.
		wp_localize_script(
			'personio-integration-light-diagnostics',
			'personioIntegrationLightDiagnosticsJsVars',
			array(
				'ajax_url'              => admin_url( 'admin-ajax.php' ),
				'run_diagnostics_nonce' => wp_create_nonce( 'personio-integration-light-diagnostics' ),
				'lbl_diagnose_running'  => __( 'Diagnose running', 'personio-integration-light' ),
				'lbl_diagnose_start'    => __( 'Start diagnose', 'personio-integration-light' ),
				'lbl_copy'              => __( 'Copy the results', 'personio-integration-light' ),
				'lbl_copied'            => __( 'Copied', 'personio-integration-light' ),
			)
		);
	}

	/**
	 * Add the tools in settings.
	 *
	 * @return void
	 */
	public function add_settings(): void {
		// get settings object.
		$settings_obj = Settings::get_instance()->get_settings_object();

		// get the main settings page.
		$settings_page = $settings_obj->get_page( 'personioPositions' );

		// bail if the page could not be loaded.
		if ( ! $settings_page instanceof Page ) {
			return;
		}

		// get the additional settings tab.
		$additional_settings_tab = $settings_page->get_tab( 'personio_integration_advanced' );
		if ( ! $additional_settings_tab instanceof Tab ) {
			return;
		}

		// add a tab for additional settings.
		$diagnostic_tab = $additional_settings_tab->add_tab( 'personio_integration_diagnostics', 20 );
		$diagnostic_tab->set_title( __( 'Diagnostics', 'personio-integration-light' ) );
		$diagnostic_tab->set_callback( array( $this, 'render_page' ) );
		$diagnostic_tab->set_hide_save( true );
	}

	/**
	 * Show the diagnosis page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		// bail if capabilities are missing.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>
		<div class="wrap">
			<h2><?php echo esc_html__( 'Diagnosis of the connection to laolaweb.com', 'personio-integration-light' ); ?></h2>
			<p><?php echo esc_html__( 'This tool checks why this project might not be able to connect to the laOlaWeb license server, and displays information that you can send to us if you need support.', 'personio-integration-light' ); ?></p>
			<p>
				<button class="button button-primary personio-integration-light-diagnostics"><?php echo esc_html__( 'Start diagnose', 'personio-integration-light' ); ?></button>
				<button class="button button-primary personio-integration-light-copy-diagnostic disabled"><?php echo esc_html__( 'Copy the results', 'personio-integration-light' ); ?></button>
			</p>
			<pre class="personio-integration-light-diagnostics-results"></pre>
		</div>
		<?php
	}

	/**
	 * Run the diagnostic via AJAX.
	 *
	 * @return void
	 */
	public function run_diagnostics_via_ajax(): void {
		// check nonce.
		check_ajax_referer( 'personio-integration-light-diagnostics', 'nonce' );

		// run the diagnostics.
		$this->run_diagnostics();

		// prepare the result.
		$report  = '=== ' . esc_html__( 'Diagnose results', 'personio-integration-light' ) . " ===\n";
		$report .= esc_html__( 'Time:', 'personio-integration-light' ) . ' ' . current_time( 'mysql' ) . "\n\n";

		foreach ( $this->get_results() as $test_name => $result ) {
			switch ( $test_name ) {
				case 'external_ip':
					$ip      = $result['value'];
					$report .= '--- ' . esc_html__( 'External IP', 'personio-integration-light' ) . " ---\n";
					$report .= $ip ? esc_html__( 'Your external IP:', 'personio-integration-light' ) . ' ' . $ip . "\n\n" : esc_html__( 'The external IP address could not be determined.', 'personio-integration-light' ) . "\n\n";
					break;
				case 'dns':
					$report .= '--- ' . esc_html__( 'DNS resolution', 'personio-integration-light' ) . " ---\n";
					if ( $result['value'] === $result['data']['host'] ) {
						$report .= esc_html__( 'ERROR: Could not resolve {$host} ({$ms} ms).', 'personio-integration-light' ) . "\n\n";
					} else {
						/* translators: %1$s will be replaced by the domain, %2$s by the IP and %3$s by the time to check.*/
						$report .= wp_kses_post( sprintf( __( '%1$s resolves to %2$s (%3$s ms)', 'personio-integration-light' ), $result['data']['host'], $result['value'], $result['duration_ms'] ) ) . "\n\n";
					}
					break;
				case 'tcp_connect':
					/* translators: %1$s will be replaced by the port. */
					$report .= '--- ' . wp_kses_post( sprintf( __( 'TCP connection test to the license server (port %1$s)', 'personio-integration-light' ), $result['data']['port'] ) ) . " ---\n";
					if ( 'success' === $result['status'] ) {
						/* translators: %1$s will be replaced by the host, %2$s by the port. */
						$report .= wp_kses_post( sprintf( __( 'OK: TCP connection to %1$s:%2$s established successfully in %3$s ms.', 'personio-integration-light' ), $result['data']['host'], $result['data']['port'], $result['duration_ms'] ) ) . "\n\n";
					} else {
						/* translators: %1$s will be replaced by the host, %2$s by the port. */
						$report .= wp_kses_post( sprintf( __( 'ERROR: TCP connection to %1$s:%2$s could not be established. Error code: %3$s - this suggests a firewall or blocking issue (timeout) rather than a DNS or application problem.', 'personio-integration-light' ), $result['data']['host'], $result['data']['port'], $result['errno'] ) ) . "\n\n";
					}
					break;
				case 'http':
					$report .= '--- ' . esc_html__( 'HTTP Request to the License Server', 'personio-integration-light' ) . " ---\n";
					if ( 'success' === $result['status'] ) {
						/* translators: %1$s will be replaced by the HTTP state. */
						$report .= wp_kses_post( sprintf( __( 'HTTP Status: %1$s<br>DNS Lookup: %2$s ms | TCP Connect: %3$s ms | SSL Handshake: %4$s ms | Time to First Byte: %5$s ms | Total: %6$s ms', 'personio-integration-light' ), $result['value'], $result['data']['timing']['dns_ms'], $result['data']['timing']['connect_ms'], $result['data']['timing']['tls_ms'], $result['data']['timing']['ttfb_ms'], $result['data']['timing']['total_ms'] ) ) . "\n\n";
					} else {
						/* translators: %1$s will be replaced by the error code. */
						$report .= wp_kses_post( sprintf( __( 'ERROR: Got error: %1$s', 'personio-integration-light' ), '<code>' . $result['error_code'] . '</code>' ) ) . "\n\n";
					}
					break;
				case 'traceroute':
					$report .= '--- ' . esc_html__( 'Traceroute (best effort)', 'personio-integration-light' ) . " ---\n";
					if ( 'skipped' === $result['status'] ) {
						$report .= esc_html__( 'shell_exec() is not available on this server. True traceroute is not possible on most shared hosting environments for security reasons (it requires root privileges for raw sockets). Instead, use the DNS/TCP/HTTP values above - if there is a firewall block, these usually clearly indicate that the timeout is specifically occurring during the connection.', 'personio-integration-light' );
					} else {
						foreach ( $result['value'] as $value ) {
							$times = array();

							foreach ( $value['times_ms'] as $time_ms ) {
								$times[] = sprintf( '%.3f ms', $time_ms );
							}

							$report .= sprintf(
								"%2d  %-15s  %s\n",
								$value['hop'],
								$value['ip'] ?? '*',
								array() === $times ? '*' : implode( '  ', $times )
							);
						}
					}
					break;
			}
		}

		// response with the result.
		wp_send_json_success( $report );
	}

	/**
	 * Run the diagnostics.
	 *
	 * @return void
	 */
	public function run_diagnostics(): void {
		// get the domain from the license URL.
		$domain = wp_parse_url( WP_PERSONIO_INTEGRATION_LIGHT_LICENCE_URL, PHP_URL_HOST );

		// bail if domain could not be parsed.
		if ( ! is_string( $domain ) || empty( $domain ) ) { // @phpstan-ignore function.alreadyNarrowedType,booleanOr.alwaysFalse,empty.variable
			return;
		}

		// get the analyzer object.
		$tcp_analyzer = new TcpAnalyzer();

		// configure the tests we want to run.
		$tcp_analyzer->set_tests(
			array(
				'external_ip' => array(),
				'dns'         => array( 'host' => $domain ),
				'tcp_connect' => array(
					'host' => $domain,
					'port' => 443,
				),
				'http'        => array( 'url' => 'https://' . $domain ),
				'traceroute'  => array( 'host' => $domain ),
			)
		);

		// run them.
		$tcp_analyzer->run();

		// get the result.
		$this->results = $tcp_analyzer->get_results_as_array();
	}

	/**
	 * Return the results.
	 *
	 * @return mixed[]
	 */
	public function get_results(): array {
		return $this->results;
	}
}

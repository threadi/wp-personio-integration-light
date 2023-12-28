<?php
/**
 * File for handling site health options of this plugin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin\Admin;

use App\Helper;
use App\PersonioIntegration\Positions;

/**
 * Helper-function for Dashboard options of this plugin.
 */
class Dashboard {
	/**
	 * Instance of this object.
	 *
	 * @var ?Dashboard
	 */
	private static ?Dashboard $instance = null;

	/**
	 * Constructor for Init-Handler.
	 */
	private function __construct() {
	}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {
	}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Dashboard {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initialize the site health support.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
	}

	/**
	 * Add the dashboard widget.
	 *
	 * @return void
	 */
	public function add_dashboard_widget(  ): void {
		// only if Personio URL is available.
		if ( ! Helper::is_personio_url_set() ) {
			return;
		}

		// add dashboard widget to show the newest imported positions.
		wp_add_dashboard_widget(
			'dashboard_personio_integration_positions',
			__( 'Positions imported from Personio', 'personio-integration-light' ),
			array( $this, 'get_widget_content' ),
			null,
			array(),
			'side',
			'high'
		);
	}

	/**
	 * Output the contents of the dashboard widget
	 *
	 * @param string $post The post as object.
	 * @param array  $callback_args List of arguments.
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function get_widget_content( string $post, array $callback_args ): void {
		$positions_obj = Positions::get_instance();
		if ( function_exists( 'personio_integration_set_ordering' ) ) {
			remove_filter( 'pre_get_posts', 'personio_integration_set_ordering' );
		}
		$positions_list = $positions_obj->get_positions(
			3,
			array(
				'sortby' => 'date',
				'sort'   => 'DESC',
			)
		);
		if ( function_exists( 'personio_integration_set_ordering' ) ) {
			add_filter( 'pre_get_posts', 'personio_integration_set_ordering' ); }
		if ( 0 === count( $positions_list ) ) {
			echo '<p>' . esc_html__( 'Actually there are no positions imported from Personio.', 'personio-integration-light' ) . '</p>';
		} else {
			$link = add_query_arg(
				array(
					'post_type' => WP_PERSONIO_INTEGRATION_CPT,
				),
				get_admin_url() . 'edit.php'
			);

			?><ul class="personio_positions">
			<?php
			foreach ( $positions_list as $position ) {
				?>
				<li><a href="<?php echo esc_url( get_permalink( $position->ID ) ); ?>"><?php echo esc_html( $position->getTitle() ); ?></a></li>
				<?php
			}
			?>
			</ul>
			<p><a href="<?php echo esc_url( $link ); ?>">
					<?php
					/* translators: %1$d will be replaced by the count of positions */
					printf( esc_html__( 'Show all %1$d positions', 'personio-integration-light' ), absint( get_option( WP_PERSONIO_OPTION_COUNT, 0 ) ) );
					?>
				</a></p>
			<?php
		}
	}

}

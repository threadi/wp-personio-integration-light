<?php
/**
 * File for handling statistics about the positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Page;

/**
 * Object to handle statistics about the positions.
 */
class Statistics {

	/**
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Statistics
	 */
	protected static ?Statistics $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
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
	public static function get_instance(): Statistics {
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
		// add settings.
		add_action( 'init', array( $this, 'add_the_settings' ), 20 );
	}

	/**
	 * Add the tab with the report.
	 *
	 * @return void
	 */
	public function add_the_settings(): void {
		// get settings object.
		$settings_obj = \PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings::get_instance();

		// get main settings page.
		$settings_page = $settings_obj->get_page( 'personioPositions' );

		// bail if page does not exist.
		if ( ! $settings_page instanceof Page ) {
			return;
		}

		// add the statistics tab.
		$statistics_tab = $settings_page->add_tab( 'statistics', 90 );
		$statistics_tab->set_title( __( 'Statistics', 'personio-integration-light' ) );
		$statistics_tab->set_callback( array( $this, 'show_statistic' ) );
	}

	/**
	 * Return the statistic we collect as array.
	 *
	 * Structure:
	 * Name => Value
	 *
	 * Example:
	 * Count of positions => 42
	 *
	 * @return array<string,mixed>
	 */
	private function get(): array {
		// create the statistic array.
		$statistics = array();

		// get all positions.
		$positions = Positions::get_instance()->get_positions();

		// add entry about the positions.
		$statistics[ __( 'Count of positions:', 'personio-integration-light' ) ] = count( $positions );

		// get all taxonomies.
		$taxonomies = Taxonomies::get_instance()->get_taxonomies();

		// add entry about the taxonomies.
		$statistics[ __( 'Count of taxonomies:', 'personio-integration-light' ) ] = count( $taxonomies );

		// add entry about the used locations.
		$terms = get_terms( array( 'taxonomy' => WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE ) );
		if ( ! is_array( $terms ) ) {
			$terms = array();
		}
		$statistics[ __( 'Used main workplaces:', 'personio-integration-light' ) ] = count( $terms );

		/**
		 * Filter the statistics.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param array<string,mixed> $statistics The statistic array.
		 */
		return apply_filters( 'personio_integration_light_statistics', $statistics );
	}

	/**
	 * Return an HTML-table with the statistic data.
	 *
	 * @return string
	 */
	public function get_table(): string {
		ob_start();
		?>
		<table>
			<?php
			foreach ( $this->get() as $label => $value ) {
				?>
				<tr><th><?php echo esc_html( $label ); ?></th><td><?php echo esc_html( $value ); ?></td></tr>
				<?php
			}
			?>
		</table>
		<?php
		$content = ob_get_clean();
		if ( ! $content ) {
			return '';
		}
		return $content;
	}

	/**
	 * Show the statistics.
	 *
	 * @return void
	 */
	public function show_statistic(): void {
		?>
		<h2><?php echo esc_html__( 'Statistics', 'personio-integration-light' ); ?></h2>
		<?php echo wp_kses_post( $this->get_table() ); ?>
		<?php
	}
}

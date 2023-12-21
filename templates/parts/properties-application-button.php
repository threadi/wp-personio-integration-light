<?php
/**
 * Template for output the application button.
 *
 * @version: 1.0.0
 * @package personio-integration-light
 */

use personioIntegration\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Output an application-button for a single position.
 *
 * @version: 1.0.0
 */

// embed block-specific styling.
require Helper::get_template( 'parts/styling.php' );

?>
<div class="entry-content">
	<p class="personio-integration-application-button<?php echo esc_attr( $attributes['classes'] ); ?>">
		<a class="personio-integration-application-button" href="<?php echo esc_url( Helper::get_personio_application_url( $position ) ); ?>" target="_blank">
			<?php
			if ( 'archive' === $text_position ) {
				echo esc_html_x( 'Apply for this position', 'archive', 'personio-integration-light' );
			} else {
				echo esc_html_x( 'Apply for this position', 'single', 'personio-integration-light' );
			}
			?>
		</a>
		<?php
		if ( ! empty( $back_to_list_url ) ) {
			?>
			<a class="personio-integration-back-button" href="<?php echo esc_url( $back_to_list_url ); ?>">
				<?php esc_html_e( 'back to list', 'personio-integration-light' ); ?>
			</a>
			<?php
		}
		?>
	</p>
</div>
<?php

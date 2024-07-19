<?php
/**
 * Template for output the application button.
 *
 * @param array $attributes List of settings.
 * @param string $text_position Defines where the text is output (single or archive-view).
 * @param string $back_to_list_url Define the text for "back to list" link. If empty link will not be displayed.
 * @param string $target Defines the value for the target-attribute.
 *
 * @version: 3.1.0
 * @package personio-integration-light
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output an application-button for a single position.
 */
?>
<div class="entry-content">
	<p class="personio-integration-application-button<?php echo esc_attr( $attributes['classes'] ); ?>">
		<a class="personio-integration-application-button" href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $target ); ?>">
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

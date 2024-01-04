<?php
/**
 * Show styles.
 *
 * @version: 3.0.0
 * @package personio-integration-light
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output of block-specific styles
 */

if ( ! empty( $styles ) ) {
	?>
		<style>
			<?php echo $styles; ?>
		</style>
	<?php
}

<?php
/**
 * Template for output the content of a single position.
 *
 * @param array     $personio_attributes List of settings.
 * @param Position $position       The positions object.
 *
 * @package personio-integration-light
 * @version: 4.0.0
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;

?>
	<article id="post-<?php echo absint( $position->get_id() ); ?>" class="site-main entry inside-article container site-content site-container content-bg content-area ht-container <?php echo esc_attr( apply_filters( 'personio_integration_light_position_get_classes', $position ) ); ?>" role="region" aria-label="<?php echo esc_attr__( 'Position', 'personio-integration-light' ); ?>">
		<?php
		foreach ( $personio_attributes['templates'] as $template ) {
			do_action( 'personio_integration_get_' . $template, $position, $personio_attributes );
		}
		?>
	</article>
<?php

<?php
/**
 * Template for output of a single position.
 *
 * @version: 3.0.0
 * @package personio-integration-light
 */

defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Plugin\Templates;

// embed block-specific styling.
require Templates::get_instance()->get_template( 'parts/styling.php' );

?>
	<article id="post-<?php echo absint( $position->get_id() ); ?>" class="site-main post-<?php echo absint( $position->get_id() ); ?> <?php echo esc_attr( get_post_type( $position->get_id() ) ); ?> type-<?php echo esc_attr( get_post_type( $position->get_id() ) ); ?> status-<?php echo esc_attr( get_post_status( $position->get_id() ) ); ?> entry inside-article container qodef-container-inner site-content site-container content-bg content-area ht-container <?php echo esc_attr( $personio_attributes['classes'] ); ?>" role="region" aria-label="<?php echo esc_html__( 'Position', 'personio-integration-light' ); ?>">
		<?php
		foreach ( $personio_attributes['templates'] as $template ) {
			do_action( 'personio_integration_get_' . $template, $position, $personio_attributes );
		}
		?>
	</article>
<?php

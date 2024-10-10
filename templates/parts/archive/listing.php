<?php
/**
 * Listing-template for archive-listing.
 *
 * @version 3.3.0
 * @package personio-integration-light
 */

use PersonioIntegrationLight\Plugin\Templates;

?><ul class="personio-integration-archive-listing <?php echo esc_attr( $personio_attributes['classes'] ); ?>">
<?php
while ( $GLOBALS['personio_query_results']->have_posts() ) :
	$GLOBALS['personio_query_results']->the_post();

	// get the Position as object with the requested language.
	$position_obj = $positions_obj->get_position( get_the_id(), $personio_attributes['lang'] );

	// get group title.
	include Templates::get_instance()->get_template( 'parts/part-grouptitle.php' );

	?>
	<li>
		<article id="post-<?php echo absint( $position_obj->get_id() ); ?>" class="site-main entry inside-article container qodef-container-inner site-content site-container content-bg content-area ht-container ht-container <?php echo esc_attr( apply_filters( 'personio_integration_light_position_get_classes', $position_obj )); ?>" role="region" aria-label="<?php echo esc_attr__( 'Positions', 'personio-integration-light' ); ?>">
			<?php
			foreach ( $personio_attributes['templates'] as $template ) {
				do_action( 'personio_integration_get_' . $template, $position_obj, $personio_attributes );
			}
			?>
		</article>
	</li>
	<?php
endwhile;
?>
</ul>

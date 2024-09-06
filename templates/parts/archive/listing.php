<?php
/**
 * Listing-template for archive-listing.
 *
 * @version 3.0.0
 * @package personio-integration-light
 */

use PersonioIntegrationLight\Plugin\Templates;

?><ul class="personio-integration-archive-listing">
<?php
while ( $GLOBALS['personio_query_results']->have_posts() ) :
	$GLOBALS['personio_query_results']->the_post();

	// get the Position as object with the requested language.
	$position = $positions_obj->get_position( get_the_id(), $personio_attributes['lang'] );

	// get group title.
	include Templates::get_instance()->get_template( 'parts/part-grouptitle.php' );

	?>
	<li>
		<article id="post-<?php echo absint( $position->get_id() ); ?>" class="site-main post-<?php echo absint( $position->get_id() ); ?> <?php echo esc_attr( get_post_type( $position->ID ) ); ?> type-<?php echo esc_attr( get_post_type( $position->get_id() ) ); ?> status-<?php echo esc_attr( get_post_status( $position->get_id() ) ); ?> entry inside-article container qodef-container-inner site-content site-container content-bg content-area ht-container ht-container <?php echo esc_attr( $personio_attributes['classes'] ); ?>" role="region" aria-label="<?php echo esc_attr__( 'Positions', 'personio-integration-light' ); ?>">
			<?php
			foreach ( $personio_attributes['templates'] as $template ) {
				do_action( 'personio_integration_get_' . $template, $position, $personio_attributes );
			}
			?>
		</article>
	</li>
	<?php
endwhile;
?>
</ul>

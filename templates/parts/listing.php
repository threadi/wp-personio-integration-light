<?php
/**
 * Template for output of a list of positions.
 *
 * @param array    $personio_attributes List of settings.
 *
 * @package personio-integration-light
 * @version: 4.0.0
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Plugin\Templates;

if ( ! empty( $personio_attributes['listing_template'] ) && ! empty( $personio_attributes['templates'] ) ) {

	// loop through the list by using set listing template.
	if ( $GLOBALS['personio_query_results']->have_posts() ) :
		include Templates::get_instance()->get_template( 'parts/archive/' . $personio_attributes['listing_template'] . '.php' );
		include Templates::get_instance()->get_template( 'parts/part-pagination.php' );
	else :
		?><article class="site-main entry inside-article container site-content site-container content-bg content-area ht-container"><div class="entry-content"><p><?php echo esc_html__( 'There are currently no positions available.', 'personio-integration-light' ); ?></p></div></article>
		<?php
	endif;
}
wp_reset_postdata();

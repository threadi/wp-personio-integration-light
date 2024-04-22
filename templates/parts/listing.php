<?php
/**
 * Template for output of a list of positions.
 *
 * @package personio-integration-light
 * @version: 3.0.0
 */

use PersonioIntegrationLight\Plugin\Templates;

defined( 'ABSPATH' ) || exit;

if ( ! empty( $personio_attributes['listing_template'] ) && ! empty( $personio_attributes['templates'] ) ) {

	// loop through the list by using set listing template.
	if ( $GLOBALS['personio_query_results']->have_posts() ) :
		include Templates::get_instance()->get_template( 'parts/archive/' . $personio_attributes['listing_template'] . '.php' );
		include Templates::get_instance()->get_template( 'parts/part-pagination.php' );
	else :
		?><article class="site-main entry inside-article container qodef-container-inner site-content site-container content-bg content-area ht-container ht-container"><div class="entry-content"><p><?php echo esc_html__( 'There are currently no positions available.', 'personio-integration-light' ); ?></p></div></article>
		<?php
	endif;
}
wp_reset_postdata();

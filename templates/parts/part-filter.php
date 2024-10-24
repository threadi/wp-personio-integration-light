<?php
/**
 * Template: part-filter.php
 *
 * @param array $personio_attributes The attributes.
 * @param string $anchor The ID to use to ID as anchor-target.
 * @param string $link_to_anchor The anchor for any targets (form or link).
 *
 * @version 4.0.0
 * @package personio-integration-light
 */

use PersonioIntegrationLight\Helper;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

if ( ! empty( $personio_attributes['filter'] ) && false !== $personio_attributes['showfilter'] && 0 < absint( get_option( 'personioIntegrationPositionCount', 0 ) ) ) :
	?>
	<article id="<?php echo esc_attr( $anchor ); ?>" class="site-main entry entry-content container inside-article container site-content site-content site-container content-bg content-area <?php echo esc_attr( $personio_attributes['classes'] ); ?>" role="region" aria-label="<?php echo esc_attr__( 'Filter for positions', 'personio-integration-light' ); ?>">
		<form action="<?php echo esc_url( apply_filters( 'personio_integration_light_filter_url', Helper::get_current_url(), $link_to_anchor ) ); ?>" class="entry-content personio-position-filter personio-position-filter-<?php echo esc_attr( $personio_attributes['filtertype'] ); ?> site-content site-container content-bg content-area">
			<legend><?php echo esc_html__( 'Filter', 'personio-integration-light' ); ?></legend>
			<?php

			do_action( 'personio_integration_filter_pre', $personio_attributes );

			foreach ( $personio_attributes['filter'] as $filter ) :
				do_action( 'personio_integration_get_filter', $filter, $personio_attributes, $link_to_anchor );
			endforeach;

			do_action( 'personio_integration_filter_post', $personio_attributes );

			?>
			<button type="submit"><?php echo esc_html__( 'Search', 'personio-integration-light' ); ?></button>
			<a href="<?php echo esc_url( apply_filters( 'personio_integration_light_filter_url', remove_query_arg( 'personiofilter' ), $link_to_anchor ) ); ?>" class="personio-position-filter-reset"><?php echo esc_html__( 'Reset Filter', 'wp-personio-integration' ); ?></a>
		</form>
	</article>
	<?php
endif;

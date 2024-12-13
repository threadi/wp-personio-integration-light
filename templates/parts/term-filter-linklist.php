<?php
/**
 * Show select-filter for a chosen taxonomy.
 *
 * @param string $filter Internal name of used filter.
 * @param string $filtername Public name of used filter.
 * @param string $value Actual selected value.
 * @param array $terms List of terms to show.
 * @param array $attributes The attributes.
 *
 * @package personio-integration-light
 * @version: 4.0.0
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Template for output a single term filter as linklist.
 */
?>
	<div<?php echo ! empty( $GLOBALS['wp']->query_vars['personiofilter'][ $filter ] ) ? ' class="personio-filter-selected"' : ''; ?>>
		<label><?php echo esc_html( $filtername ); ?></label>
		<ul>
			<?php
			$term_count = count( $terms );
			for ( $t = 0;$t < $term_count;$t++ ) {
				if ( ! empty( $terms[ $t ] ) ) {
					$url = apply_filters( 'personio_integration_light_filter_url', add_query_arg( array( 'personiofilter[' . $filter . ']' => $terms[ $t ]->term_id ) ), $attributes['link_to_anchor'] );
					?>
					<li><a href="<?php echo esc_url( $url ); ?>" class="
											<?php
											echo esc_attr( apply_filters( 'personio_integration_light_term_get_classes', $terms[ $t ] ) );
											echo ( $terms[ $t ]->term_id === $value ? ' personio-filter-selected' : '' );
											?>
					"><?php echo esc_html( $terms[ $t ]->name ); ?></a></li>
											<?php
				}
			}
			?>
		</ul>
	</div>
<?php

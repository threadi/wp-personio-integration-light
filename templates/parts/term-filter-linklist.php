<?php
/**
 * Show select-filter for a chosen taxonomy.
 *
 * @param string $filter Internal name of used filter.
 * @param string $filtername Public name of used filter.
 * @param int $value Actual selected value.
 * @param array $terms List of terms to show.
 * @param array $attributes The attributes.
 *
 * @package personio-integration-light
 * @version: 5.5.0
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Template for output a term filter as "linklist".
 */
?>
	<div<?php echo ! empty( $GLOBALS['wp']->query_vars['personiofilter'][ $filter ] ) ? ' class="personio-filter-list-selected"' : ''; ?>>
		<p><?php echo esc_html( $filtername ); ?></p>
		<ul>
			<?php
			$personio_integration_term_count = count( $terms );
			for ( $personio_integration_t = 0;$personio_integration_t < $personio_integration_term_count;$personio_integration_t++ ) {
				if ( ! empty( $terms[ $personio_integration_t ] ) ) {
					$personio_integration_url = apply_filters( 'personio_integration_light_filter_url', add_query_arg( array( 'personiofilter[' . $filter . ']' => $terms[ $personio_integration_t ]->term_id ) ), $attributes['link_to_anchor'] );
					?>
					<li><a href="<?php echo esc_url( $personio_integration_url ); ?>" class="
											<?php
											echo esc_attr( apply_filters( 'personio_integration_light_term_get_classes', $terms[ $personio_integration_t ] ) );
											echo ( $terms[ $personio_integration_t ]->term_id === $value ? ' personio-filter-selected' : '' );
											?>
					"
				       <?php
				       echo ( $terms[ $personio_integration_t ]->term_id === $value ? ' aria-current="true"' : '' );
					   ?>
						><?php echo esc_html( $terms[ $personio_integration_t ]->name ); ?></a></li>
											<?php
				}
			}
			?>
		</ul>
	</div>
<?php

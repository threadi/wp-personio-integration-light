<?php
/**
 * Show select-filter for a chosen taxonomy.
 *
 * @version: 3.0.0
 * @package personio-integration-light
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template for output a single term filter a linklist.
 */
?>
	<div<?php echo ! empty( $GLOBALS['wp']->query_vars['personiofilter'][ $filter ] ) ? ' class="personio-filter-selected"' : ''; ?>>
		<label><?php echo esc_html( $filtername ); ?></label>
		<ul>
			<?php
			$term_count = count( $terms );
			for ( $t = 0;$t < $term_count;$t++ ) {
				if ( ! empty( $terms[ $t ] ) ) {
					$url = add_query_arg( 'personiofilter[' . $filter . ']', $terms[ $t ]->term_id );
					if ( ! empty( $form_id ) ) {
						$url .= '#' . $form_id;
					}
					?>
					<li><a href="<?php echo esc_url( $url ); ?>"<?php echo ( $terms[ $t ]->term_id === $value ? ' class="personio-filter-selected"' : '' ); ?>><?php echo esc_html( $terms[ $t ]->name ); ?></a></li>
											<?php
				}
			}
			?>
		</ul>
	</div>
<?php

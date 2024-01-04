<?php
/**
 * Show select-filter for a chosen taxonomy.
 *
 * @version: 3.0.0
 * @package personio-integration-light
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template for output a single term filter with select-field.
 */
?>
	<div>
		<label for="personiofilter<?php echo esc_attr( $filter ); ?>"><?php echo esc_html( $filtername ); ?>:</label>
		<select name="personiofilter[<?php echo esc_attr( $filter ); ?>]" id="personiofilter<?php echo esc_attr( $filter ); ?>">
			<option value="0"><?php echo esc_html__( 'Please choose', 'personio-integration-light' ); ?></option>
			<?php
			$term_count = count( $terms );
			for ( $t = 0;$t < $term_count;$t++ ) {
				?>
				<option value="<?php echo absint( $terms[ $t ]->term_id ); ?>"<?php echo ( $terms[ $t ]->term_id === $value ? ' selected="selected"' : '' ); ?>><?php echo esc_html( $terms[ $t ]->name ); ?></option>
											<?php
			}
			?>
		</select>
	</div>
<?php

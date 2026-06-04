<?php
/**
 * Show select-filter for a chosen taxonomy.
 *
 * @param string $filter     Internal name of used filter.
 * @param string $filtername Public name of used filter.
 * @param int $value      Actual selected value.
 * @param array  $terms      List of terms to show.
 * @param array $attributes The attributes.
 *
 * @package personio-integration-light
 * @version: 5.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template for output a single-term filter with select-field.
 */
?>
	<div>
		<label for="personiofilter<?php echo esc_attr( $filter ); ?>"><?php echo esc_html( $filtername ); ?>:</label>
		<select name="personiofilter[<?php echo esc_attr( $filter ); ?>]" id="personiofilter<?php echo esc_attr( $filter ); ?>">
			<option value="0"><?php echo esc_html__( 'Please choose', 'personio-integration-light' ); ?></option>
			<?php
			$personio_integration_term_count = count( $terms );
			for ( $personio_integration_t = 0;$personio_integration_t < $personio_integration_term_count;$personio_integration_t++ ) {
				?>
				<option value="<?php echo esc_attr( $terms[ $personio_integration_t ]->term_id ); ?>"<?php echo ( $terms[ $personio_integration_t ]->term_id === $value ? ' selected="selected"' : '' ); ?> class="<?php echo esc_attr( apply_filters( 'personio_integration_light_term_get_classes', $terms[ $personio_integration_t ] ) ); ?>"><?php echo esc_html( $terms[ $personio_integration_t ]->name ); ?></option>
											<?php
			}
			?>
		</select>
	</div>
<?php

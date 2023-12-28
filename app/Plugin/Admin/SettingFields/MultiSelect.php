<?php
/**
 * File to handle a single text field for classic settings.
 *
 * @package personio-integration-light
 */

namespace App\Plugin\Admin\SettingFields;

/**
 * Initialize the field.
 */
class MultiSelect {

	public static function get( array $attributes ): void {
		if ( ! empty( $attributes['fieldId'] ) && ! empty( $attributes['options'] ) ) {
			// change attributes via hook.
			$attributes = apply_filters( 'personio_integration_settings_multiselect_attr', $attributes );

			// get value from config.
			$actual_values = get_option( $attributes['fieldId'], array() );
			if ( empty( $actual_values ) ) {
				$actual_values = array();
			}

			// or get them from request.
			if ( isset( $_POST[ $attributes['fieldId'] ] ) && is_array( $_POST[ $attributes['fieldId'] ] ) ) {
				$actual_values = array();
				$values        = array_map( 'sanitize_text_field', wp_unslash( $_POST[ $attributes['fieldId'] ] ) );
				foreach ( $values as $key => $item ) {
					$actual_values[ absint( $key ) ] = sanitize_text_field( $item );
				}
			}

			// if $actual_values is a string, convert it.
			if ( ! is_array( $actual_values ) ) {
				$actual_values = explode( ',', $actual_values );
			}

			// use values as key if set.
			if ( ! empty( $attributes['useValuesAsKeys'] ) ) {
				$new_array = array();
				foreach ( $attributes['values'] as $value ) {
					$new_array[ $value ] = $value;
				}
				$attributes['values'] = $new_array;
			}

			// get title.
			$title = '';
			if ( isset( $attributes['title'] ) ) {
				$title = $attributes['title'];
			}

			// get additional classes.
			$classes = apply_filters( 'personio_integration_settings_multiselect_classes', array(), $attributes );

			// set readonly attribute.
			if ( isset( $attributes['readonly'] ) && false !== $attributes['readonly'] ) {
				?>
				<input type="hidden" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>_ro" value="<?php echo esc_attr( implode( ',', $actual_values ) ); ?>" />
				<?php
			}

			?>
			<select id="<?php echo esc_attr( $attributes['fieldId'] ); ?>" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>[]" multiple class="personio-field-width <?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo isset( $attr['readonly'] ) && false !== $attr['readonly'] ? ' disabled="disabled"' : ''; ?> title="<?php echo esc_attr( $title ); ?>">
				<?php
				foreach ( $attributes['options'] as $key => $value ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>"<?php echo in_array( $key, $actual_values, true ) ? ' selected="selected"' : ''; ?>><?php echo esc_html( $value ); ?></option>
					<?php
				}
				?>
			</select>
			<?php
			if ( ! empty( $attributes['description'] ) ) {
				echo '<p>' . wp_kses_post( $attributes['description'] ) . '</p>';
			}

			// show optional hint for our Pro-version.
			if ( ! empty( $attributes['pro_hint'] ) ) {
				do_action( 'personio_integration_admin_show_pro_hint', $attributes['pro_hint'] );
			}
		}
	}
}

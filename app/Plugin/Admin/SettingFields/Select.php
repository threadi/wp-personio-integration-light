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
class Select {

	public static function get( array $attributes ): void {
		if ( ! empty( $attributes['fieldId'] ) && ! empty( $attributes['options'] ) ) {
			// get value from config.
			$value = get_option( $attributes['fieldId'], '' );

			// or get it from request.
			if ( isset( $_POST[ $attributes['fieldId'] ] ) ) {
				$value = sanitize_text_field( wp_unslash( $_POST[ $attributes['fieldId'] ] ) );
			}

			// get title.
			$title = '';
			if ( isset( $attributes['title'] ) ) {
				$title = $attributes['title'];
			}

			// set readonly attribute.
			if ( isset( $attributes['readonly'] ) && false !== $attributes['readonly'] ) {
				?>
				<input type="hidden" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>_ro" value="<?php echo esc_attr( $value ); ?>" />
				<?php
			}

			?>
			<select id="<?php echo esc_attr( $attributes['fieldId'] ); ?>" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>" class="personio-field-width" title="<?php echo esc_attr( $title ); ?>"<?php echo isset( $attr['readonly'] ) && false !== $attr['readonly'] ? ' disabled="disabled"' : ''; ?>>
				<option value=""></option>
				<?php
				foreach ( $attributes['options'] as $key => $label ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>"<?php echo ( $value === $key ? ' selected="selected"' : '' ); ?>><?php echo esc_html( $label ); ?></option>
					<?php
				}
				?>
			</select>
			<?php
			if ( ! empty( $attributes['description'] ) ) {
				echo '<p>' . wp_kses_post( $attributes['description'] ) . '</p>';
			}
		} elseif ( empty( $attributes['values'] ) && ! empty( $attributes['noValues'] ) ) {
			echo '<p>' . esc_html( $attributes['noValues'] ) . '</p>';
		}
	}
}

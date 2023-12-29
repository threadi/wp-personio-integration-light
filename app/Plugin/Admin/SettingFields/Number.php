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
class Number {

	/**
	 * Get the output.
	 *
	 * @param array $attributes The settings for this field.
	 *
	 * @return void
	 */
	public static function get( array $attributes ): void {
		if ( ! empty( $attributes['fieldId'] ) ) {
			// get value from config.
			$value = get_option( $attributes['fieldId'], '' );

			// or get if from request.
			if ( isset( $_POST[ $attributes['fieldId'] ] ) ) {
				$value = sanitize_text_field( wp_unslash( $_POST[ $attributes['fieldId'] ] ) );
			}

			// get title.
			$title = '';
			if ( isset( $attributes['title'] ) ) {
				$title = $attributes['title'];
			}

			?>
			<input type="number" id="<?php echo esc_attr( $attributes['fieldId'] ); ?>" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>" value="<?php echo esc_attr( $value ); ?>" class="personio-field-width"<?php echo isset( $attr['readonly'] ) && false !== $attr['readonly'] ? ' disabled="disabled"' : ''; ?> title="<?php echo esc_attr( $title ); ?>">
			<?php
			if ( ! empty( $attributes['description'] ) ) {
				echo '<p>' . wp_kses_post( $attributes['description'] ) . '</p>';
			}
		}
	}
}

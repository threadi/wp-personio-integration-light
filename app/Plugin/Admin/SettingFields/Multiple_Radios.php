<?php
/**
 * File to handle a single text field for classic settings.
 *
 * @package personio-integration-light
 */

namespace App\Plugin\Admin\SettingFields;

use App\Plugin\Languages;

/**
 * Initialize the field.
 */
class Multiple_Radios {

	public static function get( array $attributes ): void {
		if ( ! empty( $attributes['fieldId'] ) ) {
			foreach ( $attributes['options'] as $key => $enabled ) {
				// get check state.
				$checked = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, '' ) === $key ? ' checked="checked"' : '';

				// get the language name.
				$language_name = Languages::get_instance()->get_label( $key );

				// get title.
				/* translators: %1$s is replaced with "string" */
				$title = sprintf( __( 'Mark to set %1$s as default language in the frontend.', 'personio-integration-light' ), esc_html($language_name) );

				// readonly.
				$readonly = '';
				if ( isset( $attributes['readonly'] ) && false !== $attributes['readonly'] ) {
					$readonly = ' disabled="disabled"';
					?>
					<input type="hidden" id="<?php echo esc_attr( $attributes['fieldId'] . $key ); ?>" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>" value="<?php echo esc_html( ! empty( $checked ) ? '1' : '0' ); ?>">
					<?php
				}
				if ( 0 === absint( $enabled ) ) {
					$readonly = ' disabled="disabled"';
					$title    = '';
				}

				// output.
				?>
				<div>
					<input type="radio" id="<?php echo esc_attr( $attributes['fieldId'] . $key ); ?>" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>" value="<?php echo esc_attr( $key ); ?>"<?php echo esc_attr( $checked ) . esc_attr( $readonly ); ?> title="<?php echo esc_attr( $title ); ?>">
					<label for="<?php echo esc_attr( $attributes['fieldId'] . $key ); ?>"><?php echo esc_html( $language_name ); ?></label>
				</div>
				<?php
			}
		}
	}
}

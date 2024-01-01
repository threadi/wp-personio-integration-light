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
class Multiple_Checkboxes {

	/**
	 * Get the output.
	 *
	 * @param array $attributes The settings for this field.
	 *
	 * @return void
	 */
	public static function get( array $attributes ): void {
		if ( ! empty( $attributes['fieldId'] ) ) {
			foreach ( $attributes['options'] as $key => $enabled ) {

				// get language name.
				$languages = Languages::get_instance()->get_languages();
				$language_name = $languages[$key];

				// get checked-marker.
				$checked = 1 === absint( get_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $key, 0 ) ) ? ' checked="checked"' : '';

				// get title.
				/* translators: %1$s is replaced with "string" */
				$title = sprintf( __( 'Mark to enable %1$s', 'personio-integration-light' ), $language_name );

				// readonly.
				$readonly = '';
				if ( isset( $attributes['readonly'] ) && false !== $attributes['readonly'] ) {
					$readonly = ' disabled="disabled"';
					?>
					<input type="hidden" id="<?php echo esc_attr( $attributes['fieldId'] . $key ); ?>" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_html( ! empty( $checked ) ? '1' : '0' ); ?>">
					<?php
				}

				// output.
				?>
				<div>
					<input type="checkbox" id="<?php echo esc_attr( $attributes['fieldId'] . $key ); ?>" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>[<?php echo esc_attr( $key ); ?>]" value="1"<?php echo esc_attr( $checked ) . esc_attr( $readonly ); ?> title="<?php echo esc_attr( $title ); ?>">
					<label for="<?php echo esc_attr( $attributes['fieldId'] . $key ); ?>"><?php echo esc_html( $language_name ); ?></label>
				</div>
				<?php
			}

			// show optional hint for our Pro-version.
			if ( ! empty( $attributes['pro_hint'] ) ) {
				$message = $attributes['pro_hint'];
				/**
				 * Show hint for Pro-plugin with individual text.
				 *
				 * @since 1.0.0 Available since first release.
				 *
				 * @param string $message The individual text.
				 */
				do_action( 'personio_integration_admin_show_pro_hint', $message );
			}
		}
	}
}

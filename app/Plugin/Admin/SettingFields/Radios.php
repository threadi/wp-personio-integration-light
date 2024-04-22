<?php
/**
 * File to handle a single text field for classic settings.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingFields;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Initialize the field.
 */
class Radios {

	/**
	 * Get the output.
	 *
	 * @param array $attributes The settings for this field.
	 *
	 * @return void
	 */
	public static function get( array $attributes ): void {
		if ( ! empty( $attributes['fieldId'] ) ) {
			/**
			 * Change Radio-field-attributes.
			 *
			 * @since 3.0.0 Available since 3.0.0.
			 *
			 * @param array $attributes List of attributes of this Radio-field.
			 */
			$attributes = apply_filters( 'personio_integration_settings_radio_attr', $attributes );

			foreach ( $attributes['options'] as $key => $language_name ) {
				// get check state.
				$checked = get_option( $attributes['fieldId'] ) === $key ? ' checked="checked"' : '';

				// get title.
				/* translators: %1$s is replaced with "string" */
				$title = sprintf( __( 'Mark to set %1$s as default language in the frontend.', 'personio-integration-light' ), esc_html( $language_name ) );

				// readonly.
				$readonly = '';
				if ( isset( $attributes['readonly'] ) && false !== $attributes['readonly'] ) {
					$readonly = ' disabled="disabled"';
					if ( ! empty( $checked ) ) {
						?>
						<input type="hidden" id="<?php echo esc_attr( $attributes['fieldId'] . $key ); ?>" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>" value="<?php echo esc_attr( $key ); ?>">
						<?php
					}
				}

				// set disabled attribute if set.
				$disabled = '';
				if ( ! empty( $attributes['options_disabled'][ $key ] ) ) {
					$disabled = ' disabled';
				}

				// output.
				?>
				<div>
					<input type="radio" id="<?php echo esc_attr( $attributes['fieldId'] . $key ); ?>" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>" value="<?php echo esc_attr( $key ); ?>"<?php echo esc_attr( $checked ) . esc_attr( $readonly ); ?> title="<?php echo esc_attr( $title ); ?>"<?php echo esc_attr( $disabled ); ?>>
					<label for="<?php echo esc_attr( $attributes['fieldId'] . $key ); ?>"><?php echo esc_html( $language_name ); ?></label>
				</div>
				<?php

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
}

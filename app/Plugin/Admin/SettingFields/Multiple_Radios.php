<?php
/**
 * File to handle a single text field for classic settings.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingFields;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Plugin\Languages;

/**
 * Initialize the field.
 */
class Multiple_Radios {

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
				// get check state.
				$checked = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, '' ) === $key ? ' checked="checked"' : '';

				// get the language name.
				$languages     = Languages::get_instance()->get_languages();
				$language_name = $languages[ $key ];

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

				// output.
				?>
				<div>
					<input type="radio" id="<?php echo esc_attr( $attributes['fieldId'] . $key ); ?>" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>" value="<?php echo esc_attr( $key ); ?>"<?php echo esc_attr( $checked ) . esc_attr( $readonly ); ?> title="<?php echo esc_attr( $title ); ?>">
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

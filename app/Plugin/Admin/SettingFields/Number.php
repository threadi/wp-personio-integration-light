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
			$value = get_option( $attributes['fieldId'] );

			// or get it from request.
			$request_value = sanitize_text_field( wp_unslash( filter_input( INPUT_POST, $attributes['fieldId'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) );
			if ( ! empty( $request_value ) ) {
				$value = $request_value;
			}

			// get title.
			$title = '';
			if ( isset( $attributes['title'] ) ) {
				$title = $attributes['title'];
			}

			?>
			<input type="number" id="<?php echo esc_attr( $attributes['fieldId'] ); ?>" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>" value="<?php echo esc_attr( $value ); ?>" class="personio-field-width"<?php echo isset( $attributes['readonly'] ) && false !== $attributes['readonly'] ? ' disabled="disabled"' : ''; ?> title="<?php echo esc_attr( $title ); ?>" data-depends="<?php echo esc_attr( wp_json_encode( $attributes['depends'] ) ); ?>">
			<?php
			if ( ! empty( $attributes['description'] ) ) {
				echo '<p>' . wp_kses_post( $attributes['description'] ) . '</p>';
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

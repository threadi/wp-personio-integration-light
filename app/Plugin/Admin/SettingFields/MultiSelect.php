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

/**
 * Initialize the field.
 */
class MultiSelect {

	/**
	 * Get the output.
	 *
	 * @param array $attributes The settings for this field.
	 *
	 * @return void
	 */
	public static function get( array $attributes ): void {
		if ( ! empty( $attributes['fieldId'] ) && ! empty( $attributes['options'] ) ) {
			/**
			 * Change MultiSelect-field-attributes.
			 *
			 * @since 2.0.0 Available since 2.0.0.
			 *
			 * @param array $attributes List of attributes of this MultiSelect-field.
			 */
			$attributes = apply_filters( 'personio_integration_settings_multiselect_attr', $attributes );

			// get value from config.
			$actual_values = get_option( $attributes['fieldId'], array() );
			if ( empty( $actual_values ) ) {
				$actual_values = array();
			}

			// or get it from request.
			$request_value = wp_unslash( filter_input( INPUT_POST, $attributes['fieldId'], FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
			if ( ! empty( $request_value ) ) {
				$request_value = array_map( 'sanitize_text_field', $request_value );
				foreach ( $request_value as $key => $item ) {
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

			$css_classes = array();

			/**
			 * Get additional CSS-classes for multiselect-field.
			 *
			 * @since 2.0.0 Available since 2.0.0.
			 *
			 * @param array $css_classes List of additional CSS-classes.
			 * @param array $attributes List of attributes.
			 */
			$classes = apply_filters( 'personio_integration_settings_multiselect_classes', $css_classes, $attributes );

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

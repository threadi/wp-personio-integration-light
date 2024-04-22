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
class Checkbox {

	/**
	 * Get the output.
	 *
	 * @param array $attributes The settings for this field.
	 *
	 * @return void
	 */
	public static function get( array $attributes ): void {
		if ( ! empty( $attributes['fieldId'] ) ) {
			// get title.
			$title = '';
			if ( isset( $attributes['title'] ) ) {
				$title = $attributes['title'];
			}

			// set readonly attribute.
			$readonly = '';
			if ( isset( $attributes['readonly'] ) && false !== $attributes['readonly'] ) {
				$readonly = ' disabled';
				?>
				<input type="hidden" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>_ro" value="<?php echo ( 1 === absint( get_option( $attributes['fieldId'], 0 ) ) || 1 === absint( filter_input( INPUT_POST, $attributes['fieldId'], FILTER_SANITIZE_NUMBER_INT ) ) ) ? '1' : '0'; ?>">
				<?php
			}

			?>
			<input type="checkbox" id="<?php echo esc_attr( $attributes['fieldId'] ); ?>"
					name="<?php echo esc_attr( $attributes['fieldId'] ); ?>"
					value="1"
				<?php
				echo ( 1 === absint( get_option( $attributes['fieldId'] ) ) || 1 === absint( filter_input( INPUT_GET, $attributes['fieldId'], FILTER_SANITIZE_NUMBER_INT ) ) ) ? ' checked="checked"' : '';
				?>
				<?php echo esc_attr( $readonly ); ?>
					class="personio-field-width"
					title="<?php echo esc_attr( $title ); ?>" data-depends="<?php echo esc_attr( wp_json_encode( $attributes['depends'] ) ); ?>"
			>
			<?php

			// show optional description for this checkbox.
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

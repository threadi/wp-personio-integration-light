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
class Select {

	/**
	 * Get the output.
	 *
	 * @param array $attributes The settings for this field.
	 *
	 * @return void
	 */
	public static function get( array $attributes ): void {
		// check nonce.
		if ( isset( $_REQUEST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'personio-integration-setup' ) ) {
			// redirect user back.
			wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
			exit;
		}

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

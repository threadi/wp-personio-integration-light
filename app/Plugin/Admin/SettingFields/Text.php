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
class Text {

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

		if ( ! empty( $attributes['fieldId'] ) ) {
			// get value from config.
			$value = get_option( $attributes['fieldId'], '' );

			// get value from request.
			if ( isset( $_POST[ $attributes['fieldId'] ] ) ) {
				$value = sanitize_text_field( wp_unslash( $_POST[ $attributes['fieldId'] ] ) );
			}

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
				<input type="hidden" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>_ro" value="<?php echo esc_attr( $value ); ?>">
				<?php
			}

			// mark as highlighted if set.
			if ( isset( $attributes['highlight'] ) && false !== $attributes['highlight'] ) {
				?>
				<div class="highlight">
				<?php
			}

			// output.
			?>
			<input type="text" id="<?php echo esc_attr( $attributes['fieldId'] ); ?>" name="<?php echo esc_attr( $attributes['fieldId'] ); ?>" value="<?php echo esc_attr( $value ); ?>"
				<?php
				echo ! empty( $attributes['placeholder'] ) ? ' placeholder="' . esc_attr( $attributes['placeholder'] ) . '"' : '';
				?>
				<?php echo esc_attr( $readonly ); ?> class="widefat" title="<?php echo esc_attr( $title ); ?>">
			<?php
			if ( ! empty( $attributes['description'] ) ) {
				echo '<p>' . wp_kses_post( $attributes['description'] ) . '</p>';
			}

			// end mark as highlighted if set.
			if ( isset( $attributes['highlight'] ) && false !== $attributes['highlight'] ) {
				?>
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

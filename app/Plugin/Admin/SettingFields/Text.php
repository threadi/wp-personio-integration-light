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
class Text {

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
		}
	}
}

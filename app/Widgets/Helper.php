<?php
/**
 * File with widget-helper tasks for the plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Widgets;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use const WP_CLI;

/**
 * Trait with helper-functions.
 */
trait Helper {
	/**
	 * Prüfe, ob der Import per CLI aufgerufen wird.
	 * Z.B. um einen Fortschrittsbalken anzuzeigen.
	 *
	 * @return bool
	 */
	public static function is_cli(): bool {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * Create output for Widget-fields.
	 *
	 * @param array $fields List of fields in this widget.
	 * @param array $instance Current settings.
	 * @return void
	 */
	protected function create_widget_field_output( array $fields, array $instance ): void {
		foreach ( $fields as $name => $field ) {
			switch ( $field['type'] ) {
				case 'select':
					// get actual value.
					$selected_value = array( ! empty( $instance[ $name ] ) ? $instance[ $name ] : $field['std'] );

					// multiselect.
					$multiple = '';
					if ( isset( $field['multiple'] ) && false !== $field['multiple'] ) {
						$multiple = ' multiple="multiple"';
						if ( ! empty( $instance[ $name ] ) && is_array( $instance[ $name ] ) ) {
							$selected_value = array();
							foreach ( $field['values'] as $n => $v ) {
								if ( false !== in_array( $n, $instance[ $name ], true ) ) {
									$selected_value[] = $n;
								}
							}
						}
					}

					// define field-name.
					$name = $this->get_field_name( $name );
					if ( isset( $field['multiple'] ) && false !== $field['multiple'] ) {
						$name .= '[]';
					}

					// output.
					?>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" name="<?php echo esc_attr( $name ); ?>"<?php echo esc_attr( $multiple ); ?>>
							<?php
							foreach ( $field['values'] as $value => $title ) {
								?>
								<option value="<?php echo esc_attr( $value ); ?>"<?php echo ( in_array( $value, $selected_value, true ) ? ' selected="selected"' : '' ); ?>><?php echo esc_html( $title ); ?></option>
															<?php
							}
							?>
						</select>
					</p>
					<?php
					break;
				case 'number':
					$value = ! empty( $instance[ $name ] ) ? $instance[ $name ] : $field['default'];
					?>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
						<input class="widefat" type="number" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" value="<?php echo esc_attr( $value ); ?>" /></p>
					</p>
					<?php
					break;
				case 'text':
					echo '<p>' . wp_kses_post( $field['text'] ) . '</p>';
					break;
			}
		}
	}

	/**
	 * Secure the widget-fields.
	 *
	 * @param array $fields List of fields.
	 * @param array $new_instance The new instance.
	 * @param array $instance The old instance.
	 * @return array
	 */
	protected function secure_widget_fields( array $fields, array $new_instance, array $instance ): array {
		foreach ( $fields as $name => $field ) {
			switch ( $field['type'] ) {
				case 'select':
					if ( ! empty( $field['multiple'] ) ) {
						$values = array();
						if ( ! empty( $new_instance[ $name ] ) ) {
							foreach ( $new_instance[ $name ] as $v ) {
								$values[] = sanitize_text_field( $v );
							}
						}
						$instance[ $name ] = $values;
					} else {
						$instance[ $name ] = sanitize_text_field( $new_instance[ $name ] );
					}
					break;
				case 'number':
					$instance[ $name ] = absint( $new_instance[ $name ] );
					break;
			}
		}
		return $instance;
	}
}

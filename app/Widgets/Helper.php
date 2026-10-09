<?php
/**
 * File with widget-helper tasks for the plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Widgets;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

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
		return \defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * Create output for Widget-fields.
	 *
	 * @param array<string,array<string,mixed>> $fields List of fields in this widget.
	 * @param array<string,mixed>               $instance Current settings.
	 * @return void
	 */
	protected function create_widget_field_output( array $fields, array $instance ): void {
		foreach ( $fields as $name => $field ) {
			switch ( $field['type'] ) {
				case 'select':
					// check if this is a multiselect.
					$is_multiple = isset( $field['multiple'] ) && false !== $field['multiple'];

					// get actual value (saved setting or the default) as list of strings.
					// -> option values are array keys which could be integers, so compare them as strings.
					$current_value  = ! empty( $instance[ $name ] ) ? $instance[ $name ] : ( $field['std'] ?? '' );
					$selected_value = array_map( 'strval', array_filter( (array) $current_value, 'is_scalar' ) );

					// define field-ID and field-name.
					$field_id   = $this->get_field_id( $name );
					$field_name = $this->get_field_name( $name );
					if ( $is_multiple ) {
						$field_name .= '[]';
					}

					// output.
					?>
					<p>
						<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
						<select class="widefat" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>"<?php echo $is_multiple ? ' multiple="multiple"' : ''; ?>>
							<?php
							foreach ( $field['values'] as $value => $title ) {
								?>
								<option value="<?php echo esc_attr( $value ); ?>"<?php echo ( \in_array( (string) $value, $selected_value, true ) ? ' selected="selected"' : '' ); ?>><?php echo esc_html( $title ); ?></option>
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
						<input class="widefat" type="number" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" value="<?php echo esc_attr( $value ); ?>" />
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
	 * @param array<string,mixed> $fields List of fields.
	 * @param array<string,mixed> $new_instance The new instance.
	 * @param array<string,mixed> $instance The old instance.
	 * @return array<string,mixed>
	 */
	protected function secure_widget_fields( array $fields, array $new_instance, array $instance ): array {
		foreach ( $fields as $name => $field ) {
			switch ( $field['type'] ) {
				case 'select':
					if ( ! empty( $field['multiple'] ) ) {
						$values = array();
						if ( ! empty( $new_instance[ $name ] ) && \is_array( $new_instance[ $name ] ) ) {
							foreach ( $new_instance[ $name ] as $v ) {
								$values[] = sanitize_text_field( $v );
							}
						}
						$instance[ $name ] = $values;
					} else {
						$instance[ $name ] = sanitize_text_field( $new_instance[ $name ] ?? '' );
					}
					break;
				case 'number':
					$instance[ $name ] = absint( ! empty( $new_instance[ $name ] ) ? $new_instance[ $name ] : 0 );
					break;
			}
		}
		return $instance;
	}
}

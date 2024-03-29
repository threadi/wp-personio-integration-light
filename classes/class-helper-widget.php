<?php

namespace personioIntegration;

trait helper_widget {
    /**
     * Prüfe, ob der Import per CLI aufgerufen wird.
     * Z.B. um einen Fortschrittsbalken anzuzeigen.
     *
     * @return bool
     */
    public static function isCLI(): bool
    {
        return defined( 'WP_CLI' ) && WP_CLI;
    }

    /**
     * Create output for Widget-fields.
     *
     * @param $fields
     * @param $instance
     * @return void
     */
    protected function createWidgetFieldOutput( $fields, $instance ) {
        foreach( $fields as $name => $field ) {
            switch( $field["type"] ) {
                case "select":
                    // get actual value
                    $selectedValue = [!empty($instance[$name]) ? $instance[$name] : $field["std"]];

                    // multiselect
                    $multiple = '';
                    if( isset($field["multiple"]) && false !== $field["multiple"]) {
                        $multiple = ' multiple="multiple"';
                        if( !empty($instance[$name]) && is_array($instance[$name]) ) {
                            $selectedValue = [];
                            foreach( $field["values"] as $n => $v ) {
                                if( false !== in_array($n, $instance[$name]) ) {
                                    $selectedValue[] = $n;
                                }
                            }
                        }
                    }
                    ?>
                    <p>
                        <label for="<?php echo esc_attr($this->get_field_name($name)); ?>"><?php echo esc_html($field["title"]); ?></label>
                        <select class="widefat" id="<?php echo esc_attr($this->get_field_name($name)); ?>" name="<?php echo esc_attr($this->get_field_name($name));echo (isset($field["multiple"]) && false !== $field["multiple"]) ? '[]' : ''; ?>"<?php echo esc_attr($multiple); ?>>
                            <?php
                            foreach( $field["values"] as $value => $title ) {
                                ?><option value="<?php echo esc_attr($value); ?>"<?php echo (in_array($value, $selectedValue) ? ' selected="selected"' : ''); ?>><?php echo esc_html($title); ?></option><?php
                            }
                            ?>
                        </select>
                    </p>
                    <?php
                    break;
                case "number":
                    $value = !empty($instance[$name]) ? $instance[$name] : $field["default"];
                    ?>
                    <p>
                        <label for="<?php echo esc_attr($this->get_field_id($name)); ?>"><?php echo esc_html($field["title"]); ?></label>
                        <input class="widefat" type="number" id="<?php echo esc_attr($this->get_field_id($name)); ?>" name="<?php echo esc_attr($this->get_field_name($name)); ?>" value="<?php echo esc_attr( $value ); ?>" /></p>
                    </p>
                    <?php
                    break;
            }
        }
    }

    /**
     * Secure the widget-fields.
     *
     * @param $fields
     * @param $new_instance
     * @param $instance
     * @return mixed
     */
    protected function secureWidgetFields( $fields, $new_instance, $instance  ) {
        foreach( $fields as $name => $field ) {
            switch( $field["type"] ) {
                case "select":
                    if( !empty($field["multiple"]) ) {
                        $values = [];
                        if( !empty($new_instance[$name]) ) {
                            foreach ($new_instance[$name] as $v) {
                                $values[] = sanitize_text_field($v);
                            }
                        }
                        $instance[$name] = $values;
                    }
                    else {
                        $instance[$name] = sanitize_text_field($new_instance[$name]);
                    }
                    break;
                case "number":
                    $instance[$name] = absint($new_instance[$name]);
                    break;
            }
        }
        return $instance;
    }
}
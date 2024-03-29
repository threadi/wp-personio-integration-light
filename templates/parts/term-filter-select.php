<?php

defined( 'ABSPATH' ) || exit;

/**
 * Template for output a single term filter with select-field.
 *
 * @version: 1.0.0
 */

?>
    <div>
        <label for="personiofilter<?php echo $filter; ?>"><?php echo esc_html($filtername); ?>:</label>
        <select name="personiofilter[<?php echo esc_attr($filter); ?>]" id="personiofilter<?php echo $filter; ?>">
            <option value="0"><?php _e('Please choose', 'personio-integration-light'); ?></option>
            <?php
            for( $t=0;$t<count($terms);$t++ ) {
                ?><option value="<?php echo absint($terms[$t]->term_id); ?>"<?php echo ($terms[$t]->term_id == $value ? ' selected="selected"' : ''); ?>><?php echo esc_html($terms[$t]->name); ?></option><?php
            }
            ?>
        </select>
    </div>
<?php

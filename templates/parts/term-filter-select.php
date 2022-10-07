<?php

defined( 'ABSPATH' ) || exit;

/**
 * Template for output a single term filter with select-field.
 */

?>
    <div>
        <label for="personiofilteroffice"><?php echo esc_html($filtername); ?>:</label>
        <select name="personiofilter[<?php echo esc_attr($filter); ?>]">
            <option value="0">Please choose</option>
            <?php
            for( $t=0;$t<count($terms);$t++ ) {
                ?><option value="<?php echo absint($terms[$t]->term_id); ?>"<?php echo ($terms[$t]->term_id == $value ? ' selected="selected"' : ''); ?>><?php echo esc_html($terms[$t]->name); ?></option><?php
            }
            ?>
        </select>
    </div>
<?php
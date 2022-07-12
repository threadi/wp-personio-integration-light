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
            foreach( $terms as $term ) {
                ?><option value="<?php echo absint($term->term_id); ?>"<?php echo ($term->term_id == $value ? ' selected="selected"' : ''); ?>><?php echo esc_html($term->name); ?></option><?php
            }
            ?>
        </select>
    </div>
<?php
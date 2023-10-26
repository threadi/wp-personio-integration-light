<?php

use personioIntegration\helper;

defined( 'ABSPATH' ) || exit;

/**
 * Output an application-button for a single position.
 *
 * @version: 1.0.0
 */

// embed block-specific styling
include helper::getTemplate("parts/styling.php");

?>
<div class="entry-content">
    <p class="personio-integration-application-button<?php echo esc_attr($attributes['classes']); ?>">
        <a class="personio-integration-application-button" href="<?php echo helper::get_personio_application_url($position); ?>" target="_blank">
            <?php
            if( $textPosition == 'archive' ) {
                echo _x('Apply for this position', 'archive', 'personio-integration-light');
            }
            else {
                echo _x('Apply for this position', 'single', 'personio-integration-light');
            }
            ?>
        </a>
        <?php
        if( !empty($back_to_list_url) ) {
            ?>
            <a class="personio-integration-back-button" href="<?php echo $back_to_list_url ;?>">
                <?php _e('back to list', 'personio-integration-light'); ?>
            </a>
        <?php
        }
    ?>
    </p>
</div>
<?php

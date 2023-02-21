<?php

defined( 'ABSPATH' ) || exit;

/**
 * Output an application-button for a single position.
 */

?>
<div class="entry-content">
    <p class="personio-integration-application-button">
        <a class="personio-integration-application-button" href="<?php echo get_option('personioIntegrationUrl', ''); ?>/job/<?php echo absint($position->getPersonioId()); ?>?display=<?php echo get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY); ?>#apply" target="_blank">
            <?php
            if( $textPosition == 'archive' ) {
                echo _x('Apply for this position', 'archive', 'wp-personio-integration');
            }
            else {
                echo _x('Apply for this position', 'single', 'wp-personio-integration');
            }
            ?>
        </a>
        <?php
        if( get_option('personioIntegrationTemplateBackToListButton', 0) == 1 ) {
            ?>
            <a class="personio-integration-back-button" href="<?php echo $back_to_list_url ;?>">
                <?php _e('back to list', 'wp-personio-integration'); ?>
            </a>
        <?php
        }
    ?>
    </p>
</div>
<?php

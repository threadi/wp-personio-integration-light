<?php

defined( 'ABSPATH' ) || exit;

/**
 * Output an application-button for a single position.
 */

?>
<div class="entry-content">
    <p class="personio-integration-application-button">
        <a href="<?php echo get_option('personioIntegrationUrl', ''); ?>/job/<?php echo $position->getPersonioId(); ?>?display=<?php echo get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY); ?>#apply" target="_blank">
            <?php echo __('Apply for this position', 'wp-personio-integration'); ?>
        </a>
    </p>
</div>
<?php

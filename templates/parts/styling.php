<?php

defined( 'ABSPATH' ) || exit;

/**
 * Output of block-specific styles
 */

if(!empty($styles) ) {
    ?>
        <style>
            <?php echo $styles; ?>
        </style>
    <?php
}
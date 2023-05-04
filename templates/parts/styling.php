<?php

defined( 'ABSPATH' ) || exit;

/**
 * Output of block-specific styles
 *
 * @version: 1.0.0
 */

if(!empty($styles) ) {
    ?>
        <style>
            <?php echo $styles; ?>
        </style>
    <?php
}
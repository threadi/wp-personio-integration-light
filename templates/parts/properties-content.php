<?php

defined( 'ABSPATH' ) || exit;

/**
 * Output of the content a single position.
 */

$contentArray = $position->getContentAsArray();
foreach( $contentArray as $content ) {
    ?><h3><?php echo esc_html($content['name']); ?></h3><?php
    ?><p><?php echo wp_kses_post($content['value']); ?></p><?php
}
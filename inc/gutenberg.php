<?php
/**
 * Add pagebuilder Gutenberg.
 *
 * @package personio-integration-light
 */

use App\PageBuilder\Gutenberg;

/**
 * Add the pagebuilder Gutenberg as object to the list.
 *
 * @param array $pagebuilder_objects List of pagebuilder as objects.
 *
 * @return array
 */
function personio_integration_pagebuilder_gutenberg( array $pagebuilder_objects ): array {
	$pagebuilder_objects[] = Gutenberg::get_instance();
	return $pagebuilder_objects;
}
add_filter( 'personio_integration_pagebuilder', 'personio_integration_pagebuilder_gutenberg' );

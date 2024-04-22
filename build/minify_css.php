<?php
/**
 * File to create minified CSS-files with MatthiasMullie\Minify.
 *
 * @package personio-integration-light
 */

require '../lib/autoload.php';

use MatthiasMullie\Minify\CSS;

// bail if no arguments given.
if( empty( $argv ) ) {
	return;
}

// bail if no file is given.
if( empty($argv[1]) ) {
	return;
}

// bail if given file does not exist.
if( ! file_exists( $argv[1] ) ) {
	return;
}

// create target-file-name (add ".min" before ".css").
$target_filename = str_replace( '.css', '.min.css', $argv[1] );

// run minification.
$minifier = new CSS($argv[1]);
$minifier->minify($target_filename);

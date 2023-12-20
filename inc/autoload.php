<?php
/**
 * File for autoloader of this plugin.
 *
 * @package personio-integration-light
 */

/**
 * Add autoloader for each php-class in this plugin.
 */
spl_autoload_register( 'personio_integration_autoloader' );

/**
 * The autoloader-function.
 *
 * @param string $class_name The called class name.
 *
 * @return void
 */
function personio_integration_autoloader( string $class_name ): void {

	// If the specified $class_name does not include our namespace, duck out.
	if ( ! str_contains( $class_name, 'personioIntegration' ) ) {
		return;
	}

	// Split the class name into an array to read the namespace and class.
	$file_parts = explode( '\\', $class_name );

	// Do a reverse loop through $file_parts to build the path to the file.
	$namespace        = '';
	$filepath         = '';
	$file_name        = '';
	$file_parts_count = count( $file_parts );
	for ( $i = 1; $i < $file_parts_count; $i++ ) {
		// Read the current component of the file part.
		$current = strtolower( $file_parts[ $i ] );
		$current = str_ireplace( '_', '-', $current );

		// If we're at the first entry, then we're at the filename.
		$file_name = '';
		if ( $file_parts_count - 1 === $i ) {
			$file_name = $current . '.php';
		} else {
			$namespace = $namespace . '/' . $current;
		}
	}

	if ( ! empty( $file_name ) ) {
		$dirs = apply_filters( 'personio_integration_class_dirs', array( __FILE__ ) );
		foreach ( $dirs as $dir ) {
			// Now build a path to the file using mapping to the file location.
			$filepath_pre = trailingslashit( dirname( $dir, 2 ) . '/classes/' . $namespace );
			foreach ( array( 'class', 'interface', 'abstract' ) as $type ) {
				$filepath = $filepath_pre . $type . '-' . strtolower( $file_name );

				// If the file exists in the specified path, then include it.
				if ( file_exists( $filepath ) ) {
					include_once $filepath;
				}
			}
		}
	}
}

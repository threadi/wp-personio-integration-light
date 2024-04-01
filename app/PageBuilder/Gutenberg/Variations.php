<?php
/**
 * File to handle Gutenberg-variations.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder\Gutenberg;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;

/**
 * Object to handle Gutenberg-variations of this plugin.
 */
class Variations {
	/**
	 * The instance of this object.
	 *
	 * @var Variations|null
	 */
	private static ?Variations $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Variations {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'enqueue_block_editor_assets', array( $this, 'add_script' ) );
	}

	/**
	 * Add script with variations.
	 *
	 * @return void
	 */
	public function add_script(): void {
		wp_enqueue_script(
			'personio-integration-light-variations',
			Helper::get_plugin_url() . 'blocks/variations.js',
			array( 'wp-blocks' ),
			Helper::get_file_version( Helper::get_plugin_path() . 'blocks/variations.js' ),
			true
		);
	}
}

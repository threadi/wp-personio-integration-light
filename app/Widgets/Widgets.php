<?php
/**
 * File which init the classic widget support.
 *
 * @package personio-integration-light
 */

namespace App\Widgets;

/**
 * Handler for Widgets.
 */
class Widgets {
	/**
	 * Instance of this object.
	 *
	 * @var ?Widgets
	 */
	private static ?Widgets $instance = null;

	/**
	 * Constructor for Init-Handler.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() { }

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Widgets {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize the support for classic widgets.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'widgets_init', array( $this, 'activate' ) );
		add_action( 'widgets_init', array( $this, 'deactivate' ), 20 );
	}

	/**
	 * Enable our own widgets if Block widgets are disabled.
	 *
	 * @return void
	 */
	public function activate(): void {
		if ( function_exists( 'wp_use_widgets_block_editor' ) && ! wp_use_widgets_block_editor() ) {
			register_widget( 'App\Widgets\Position' );
			register_widget( 'App\Widgets\Positions' );
		}
	}

	/**
	 * Disable our own widgets if Block widgets are enabled OR uninstallation is running.
	 *
	 * @return void
	 */
	public function deactivate(): void {
		if ( ( function_exists( 'wp_use_widgets_block_editor' ) && wp_use_widgets_block_editor() )  ) {
			$this->uninstall();
		}
	}

	/**
	 * Run this on every uninstallation of this plugin.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		unregister_widget( 'App\Widgets\Position' );
		unregister_widget( 'App\Widgets\Positions' );
		delete_option( 'widget_personiopositionwidget' );
		delete_option( 'widget_personiopositionswidget' );
	}

}

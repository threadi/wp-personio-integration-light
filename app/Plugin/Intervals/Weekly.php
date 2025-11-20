<?php
/**
 * File to handle the weekly interval.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Intervals;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Plugin\Interval_Base;

/**
 * Object to handle the weekly interval.
 */
class Weekly extends Interval_Base {

	/**
	 * Name of the method.
	 *
	 * @var string
	 */
	protected string $name = 'weekly';

	/**
	 * Time of the interval.
	 *
	 * @var int
	 */
	protected int $time = WEEK_IN_SECONDS;

	/**
	 * Instance of this object.
	 *
	 * @var ?Weekly
	 */
	private static ?Weekly $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Weekly {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Return the title of this interval.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return __( 'Once a week', 'personio-integration-light' );
	}
}

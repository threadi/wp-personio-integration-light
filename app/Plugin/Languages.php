<?php
/**
 * File to handle all language-related tasks.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

/**
 * Initialize the field.
 */
class Languages {
	/**
	 * Instance of this object.
	 *
	 * @var ?Languages
	 */
	private static ?Languages $instance = null;

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
	public static function get_instance(): Languages {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Return an array of the supported languages.
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function get_languages(): array {
		return apply_filters( 'personio_integration_supported_languages', WP_PERSONIO_INTEGRATION_LANGUAGES_COMPLETE );
	}

	/**
	 * Get the name of the given languages.
	 *
	 * @param string $lang The requested language.
	 * @return string
	 */
	public function get_label( string $lang ): string {
		$languages = apply_filters(
			'personio_integration_languages_names',
			array(
				'de' => __( 'German', 'personio-integration-light' ),
				'en' => __( 'English', 'personio-integration-light' ),
			)
		);
		return $languages[ $lang ];
	}
}

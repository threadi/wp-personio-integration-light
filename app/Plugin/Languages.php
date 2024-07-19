<?php
/**
 * File to handle all language-related tasks.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Handler for any language-tasks.
 */
class Languages {
	/**
	 * Instance of this object.
	 *
	 * @var ?Languages
	 */
	private static ?Languages $instance = null;

	/**
	 * List of languages this plugin supports.
	 *
	 * @var array|int[]
	 */
	private array $languages;

	/**
	 * List of languages (format: "xx") and their mappings to WP-language (format: "xx_YY")
	 *
	 * @var array
	 */
	private array $language_to_wp_lang_mapping = array(
		'de' => array(
			'de_DE',
			'de_DE_format',
			'de_CH',
			'de_CH_informal',
			'de_AT',
		),
		'en' => array(
			'en_US',
			'en_UK',
		),
	);

	/**
	 * Fallback-language.
	 *
	 * @var string
	 */
	private string $fallback_language_name = 'en';

	/**
	 * Constructor for Init-Handler.
	 */
	private function __construct() {
		$this->languages = array(
			'de' => __( 'German', 'personio-integration-light' ),
			'en' => __( 'English', 'personio-integration-light' ),
		);
	}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

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
		$languages = $this->languages;

		/**
		 * Return the supported languages.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $languages List of supported languages.
		 */
		return apply_filters( 'personio_integration_supported_languages', $languages );
	}

	/**
	 * Return the internal name of the main language from plugin settings.
	 *
	 * Defaults to ISO-639 language-names (e.g. "de").
	 * Optional uses the WP-language-names (e.g. "de_DE" but also "af").
	 *
	 * @param bool $use_wp_lang True if return value should be WP-language.
	 *
	 * @return string
	 */
	public function get_main_language( bool $use_wp_lang = false ): string {
		// get setting for main language.
		$language_name = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE );

		// return its name of it is a known language.
		if ( ! empty( $this->get_languages()[ $language_name ] ) ) {

			// return the wp-language.
			if ( $use_wp_lang ) {
				return $this->get_lang_mappings( $language_name )[0];
			}

			// return the ISO-639-language.
			return $language_name;
		}

		// return nothing for not supported languages.
		return self::get_current_lang();
	}

	/**
	 * Return only the active languages with the main language as first entry.
	 *
	 * @param bool $with_main_language True to use main language in list.
	 *
	 * @return array
	 */
	public function get_active_languages( bool $with_main_language = true ): array {
		// get active languages from settings.
		$active_languages = get_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION );

		// get all languages.
		$all_languages = $this->get_languages();

		// add active languages to returning list if they exist as language.
		$languages = array();

		// list with main language.
		if ( $with_main_language ) {
			$languages = array(
				$this->get_main_language() => $all_languages[ $this->get_main_language() ],
			);
		}
		foreach ( $this->get_languages() as $language_name => $label ) {
			if ( ! empty( $active_languages[ $language_name ] ) && empty( $languages[ $language_name ] ) ) {
				$languages[ $language_name ] = $label;
			}
		}

		// return resulting list.
		return $languages;
	}

	/**
	 * Return the fallback language name.
	 *
	 * @return string
	 */
	public function get_fallback_language_name(): string {
		// check if configured fallback language name is supported and return it.
		if ( ! empty( $this->get_languages()[ $this->fallback_language_name ] ) ) {
			return $this->fallback_language_name;
		}

		/**
		 * Define the fallback-language (only used if defined fallback language is not enabled in the settings).
		 *
		 * E.g. if english is not enabled, the fallback language will be set to english.
		 */
		$fallback_language = 'en';

		/**
		 * Filter the fallback language.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $wp_lang The language-name (e.g. "en").
		 */
		return apply_filters( 'personio_integration_fallback_language', $fallback_language );
	}

	/**
	 * Return whether the given language is supported or not.
	 *
	 * @param string $language_name The requested language name (e.g. "en").
	 *
	 * @return bool
	 */
	public function is_language_supported( string $language_name ): bool {
		return ! empty( $this->get_languages()[ $language_name ] );
	}

	/**
	 * Check whether the current language in this Wordpress-project is a german language.
	 *
	 * @return bool
	 */
	public function is_german_language(): bool {
		$german_languages = array(
			'de',
			'de-DE',
			'de-DE_formal',
			'de-CH',
			'de-ch-informal',
			'de-AT',
		);

		// return result: true if the actual WP-language is a german language.
		return in_array( $this->get_current_lang(), $german_languages, true );
	}

	/**
	 * Return the current language in frontend and backend
	 * depending on our own supported languages as 2-char-string (e.g. "en").
	 *
	 * If detected language is not supported by our plugin, use the fallback language.
	 *
	 * @return string
	 */
	public function get_current_lang(): string {
		$wp_language = substr( get_bloginfo( 'language' ), 0, 2 );

		// if language is not known, use fallback language.
		if ( ! self::get_instance()->is_language_supported( $wp_language ) ) {
			$wp_language = self::get_instance()->get_fallback_language_name();
		}

		/**
		 * Filter the resulting language.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $wp_language The language-name (e.g. "en").
		 */
		return apply_filters( 'personio_integration_current_language', $wp_language );
	}

	/**
	 * Return mapping to WP-language (e.g. 'de_DE') for given language-name (e.g. 'de').
	 *
	 * @param string $language_name The requested language name (e.g. 'de').
	 *
	 * @return string[]
	 */
	public function get_lang_mappings( string $language_name ): array {
		$mapping_languages = $this->language_to_wp_lang_mapping;

		/**
		 * Filter the possible mapping languages.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $mapping_languages List of language mappings.
		 */
		$mapping_languages = apply_filters( 'personio_integration_language_mappings', $mapping_languages );

		// return empty array is entry does not exist.
		if ( empty( $mapping_languages[ $language_name ] ) ) {
			return array();
		}

		// return the mappings for the requested language.
		return $mapping_languages[ $language_name ];
	}

	/**
	 * Return mapping language-name (e.g. 'en') for given WP-language (e.g. 'en_US').
	 *
	 * @param string $language_name The requested language name (e.g. 'en_US').
	 *
	 * @return string
	 */
	public function get_mapping_lang( string $language_name ): string {
		$mapping_languages = $this->language_to_wp_lang_mapping;

		/**
		 * Filter the possible mapping languages.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $mapping_languages List of language mappings.
		 */
		$mapping_languages = apply_filters( 'personio_integration_language_mappings', $mapping_languages );

		// return the mapping for the requested language.
		foreach ( $mapping_languages as $language => $shortcodes ) {
			if ( in_array( $language_name, $shortcodes, true ) ) {
				return $language;
			}
		}
		return '';
	}

	/**
	 * Get the language title of the given language key.
	 *
	 * @param string $language_key The given language key.
	 *
	 * @return string
	 */
	public function get_language_title( string $language_key ): string {
		// bail if no key is given.
		if ( empty( $language_key ) ) {
			return '';
		}

		$languages = self::get_instance()->get_languages();

		// use fallback language if language could not be detected.
		if ( empty( $languages[ $language_key ] ) ) {
			$language_key = $this->fallback_language_name;
		}

		// return the title of the language.
		return $languages[ $language_key ];
	}
}

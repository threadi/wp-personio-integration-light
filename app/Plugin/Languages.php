<?php
/**
 * File to handle all language-related tasks.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

use App\Helper;
use Exception;

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
		return apply_filters( 'personio_integration_supported_languages', $this->languages );
	}

	/**
	 * Return the value of the main language.
	 *
	 * @return string
	 */
	public function get_main_language(): string {
		// get setting.
		$language_name = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE );

		// return its name of it is a known language.
		if( !empty( $this->get_languages()[$language_name] ) ) {
			return $language_name;
		}

		// return nothing for not supported languages.
		return self::get_wp_lang();
	}

	/**
	 * Return only the active languages.
	 *
	 * @return array
	 */
	public function get_active_languages(): array {
		// get active languages from settings.
		$active_languages = get_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, array() );

		// add active languages to returning list if they exist as language.
		$languages = array();
		foreach( $this->get_languages() as $language_name => $label ) {
			if( !empty($active_languages[$language_name]) ) {
				$languages[$language_name] = $label;
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
		if( !empty( $this->get_languages()[$this->fallback_language_name] ) ) {
			return $this->fallback_language_name;
		}

		// otherwise return just "en".
		return 'en';
	}

	/**
	 * Return whether the given language is supported or not.
	 *
	 * @param string $language_name The requested language name (e.g. "en").
	 *
	 * @return bool
	 */
	public function is_language_supported( string $language_name ): bool {
		return !empty( $this->get_languages()[$language_name] );
	}

	/**
	 * Check whether a german language is used in this Wordpress-projekt.
	 *
	 * @return bool
	 */
	public function is_german_language(): bool {
		$german_languages = array(
			'de-DE',
			'de-DE_formal',
			'de-CH',
			'de-ch-informal',
			'de-AT',
		);

		// return result: true if the actual WP-language is a german language.
		return in_array( get_bloginfo( 'language' ), $german_languages, true );
	}

	/**
	 * Return the default Wordpress-language depending on our own support.
	 * If language is unknown for our plugin, use english.
	 *
	 * @return string
	 */
	public function get_wp_lang(): string {
		$wp_lang = substr( get_bloginfo( 'language' ), 0, 2 );

		/**
		 * Consider the main language set in Polylang for the web page.
		 */
		if ( Helper::is_plugin_active( 'polylang/polylang.php' ) && function_exists( 'pll_default_language' ) ) {
			$wp_lang = pll_default_language();
		}

		/**
		 * Consider the main language set in WPML for the web page.
		 */
		if ( Helper::is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$wp_lang = apply_filters( 'wpml_default_language', null );
		}

		/**
		 * Get main language set in weglot for the web page.
		 */
		if ( Helper::is_plugin_active( 'weglot/weglot.php' ) && function_exists( 'weglot_get_service' ) ) {
			try {
				$language_object = weglot_get_service( 'Language_Service_Weglot' )->get_original_language();
				if ( $language_object ) {
					$wp_lang = $language_object->getInternalCode();
				}
			} catch ( Exception $e ) {
				// TODO log errors.
			}
		}

		// if language is not known, use default language.
		if ( Languages::get_instance()->is_language_supported( $wp_lang ) ) {
			$wp_lang = Languages::get_instance()->get_fallback_language_name();
		}

		// return resulting language.
		return $wp_lang;
	}

	/**
	 * Return the current language depending on our own support.
	 * If language is unknown for our plugin, use english.
	 *
	 * @return string
	 */
	public function get_current_lang(): string {
		$wp_lang = substr( get_bloginfo( 'language' ), 0, 2 );

		/**
		 * Consider the main language set in Polylang for the web page
		 */
		if ( Helper::is_plugin_active( 'polylang/polylang.php' ) && function_exists( 'pll_current_language' ) ) {
			$wp_lang = pll_current_language();
		}

		/**
		 * Consider the main language set in WPML for the web page
		 */
		if ( Helper::is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$wp_lang = apply_filters( 'wpml_current_language', null );
		}

		/**
		 * Get current language set in weglot for the web page.
		 */
		if ( Helper::is_plugin_active( 'weglot/weglot.php' ) && function_exists( 'weglot_get_current_language' ) ) {
			try {
				$wp_lang = weglot_get_current_language();
			} catch ( Exception $e ) {
				// TODO log errors.
			}
		}

		// if language is not known, use default language.
		if ( Languages::get_instance()->is_language_supported( $wp_lang ) ) {
			$wp_lang = Languages::get_instance()->get_fallback_language_name();
		}

		// return resulting language.
		return $wp_lang;
	}
}

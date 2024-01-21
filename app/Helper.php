<?php
/**
 * File with general helper tasks for the plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Templates;
use WP_Post;
use WP_Post_Type;
use WP_Rewrite;

/**
 * The helper class itself.
 */
class Helper {

	/**
	 * Return the logo as img
	 *
	 * @return string
	 */
	public static function get_logo_img(): string {
		return '<img src="' . self::get_plugin_url() . 'gfx/personio_icon.png" alt="">';
	}

	/**
	 * Get the language-depending list-slug.
	 *
	 * @return string
	 */
	public static function get_archive_slug(): string {
		$slug = 'positions';
		if ( Languages::get_instance()->is_german_language() ) {
			$slug = 'stellen';
		}

		/**
		 * Change the archive slug.
		 *
		 * @since 1.0.0 Available since first release.
		 *
		 * @param string $slug The archive slug.
		 */
		return apply_filters( 'personio_integration_archive_slug', $slug );
	}

	/**
	 * Get the language-depending detail-slug.
	 *
	 * @return string
	 */
	public static function get_detail_slug(): string {
		$slug = 'position';
		if ( Languages::get_instance()->is_german_language() ) {
			$slug = 'stelle';
		}

		/**
		 * Change the detail slug.
		 *
		 * @since 1.0.0 Available since first release.
		 *
		 * @param string $single_slug The archive slug.
		 */
		return apply_filters( 'personio_integration_detail_slug', $slug );
	}

	/**
	 * Return the language-specific URL where the user can find information about the Pro-version of this plugin.
	 *
	 * @return string
	 */
	public static function get_pro_url(): string {
		$url = 'https://laolaweb.com/en/plugins/personio-wordpress-plugin/';
		if ( Languages::get_instance()->is_german_language() ) {
			$url = 'https://laolaweb.com/plugins/personio-wordpress-plugin/';
		}
		return $url;
	}

	/**
	 * Return the URL which starts the import manually.
	 *
	 * @return string
	 */
	public static function get_import_url(): string {
		return add_query_arg(
			array(
				'action' => 'personioPositionsImport',
				'nonce'  => wp_create_nonce( 'wp-personio-integration-import' ),
			),
			get_admin_url() . 'admin.php'
		);
	}

	/**
	 * Return the url to remove all positions in local database.
	 *
	 * @return string
	 */
	public static function get_delete_url(): string {
		return add_query_arg(
			array(
				'action' => 'personioPositionsDelete',
				'nonce'  => wp_create_nonce( 'wp-personio-integration-delete' ),
			),
			get_admin_url() . 'admin.php'
		);
	}

	/**
	 * Get list of available filter types.
	 *
	 * @return array
	 */
	public static function get_filter_types(): array {
		$types = array(
			'select'   => __( 'select-box', 'personio-integration-light' ),
			'linklist' => __( 'list of links', 'personio-integration-light' ),
		);

		/**
		 * Change the list of possible filter-types.
		 *
		 * @since 1.0.0 Available since first release.
		 *
		 * @param array $types The list of types.
		 */
		return apply_filters( 'personio_integration_filter_types', $types );
	}

	/**
	 * Checks if the current request is a WP REST API request.
	 *
	 * Case #1: After WP_REST_Request initialisation
	 * Case #2: Support "plain" permalink settings and check if `rest_route` starts with `/`
	 * Case #3: It can happen that WP_Rewrite is not yet initialized,
	 *          so do this (wp-settings.php)
	 * Case #4: URL Path begins with wp-json/ (your REST prefix)
	 *          Also supports WP installations in sub-folders
	 *
	 * @returns boolean
	 * @author matzeeable
	 */
	public static function is_admin_api_request(): bool {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST // Case #1.
			|| isset( $GLOBALS['wp']->query_vars['rest_route'] ) // (#2)
				&& str_starts_with( $GLOBALS['wp']->query_vars['rest_route'], '/' ) ) {
			return true;
		}

		// Case #3.
		global $wp_rewrite;
		if ( is_null( $wp_rewrite ) ) {
			$wp_rewrite = new WP_Rewrite();
		}

		// Case #4.
		$rest_url    = wp_parse_url( trailingslashit( rest_url() ) );
		$current_url = wp_parse_url( add_query_arg( array() ) );
		return str_starts_with( $current_url['path'], $rest_url['path'] );
	}

	/**
	 * Check and secure the allowed shortcode-attributes.
	 *
	 * @param array $attribute_defaults List of attribute defaults.
	 * @param array $attribute_settings List of attribute settings.
	 * @param array $attributes List of actual attribute values.
	 * @return array
	 */
	public static function get_shortcode_attributes( array $attribute_defaults, array $attribute_settings, array $attributes ): array {
		$filtered = array(
			'defaults'   => $attribute_defaults,
			'settings'   => $attribute_settings,
			'attributes' => $attributes,
		);

		/**
		 * Pre-filter the given attributes.
		 *
		 * @since 2.0.0 Available since first release.
		 *
		 * @param array $filtered The list of attributes.
		 */
		$filtered = apply_filters( 'personio_integration_get_shortcode_attributes', $filtered );

		// get pre-filtered array.
		$attribute_defaults = $filtered['defaults'];
		$attribute_settings = $filtered['settings'];
		$attributes         = $filtered['attributes'];

		// concat the lists.
		$attributes = shortcode_atts( $attribute_defaults, $attributes );

		// check if language-setting is valid.
		if ( ! Languages::get_instance()->is_language_supported( $attributes['lang'] ) ) {
			$attributes['lang'] = Languages::get_instance()->get_fallback_language_name();
		}

		// check each attribute depending on its setting.
		foreach ( $attributes as $name => $attribute ) {
			if ( ! empty( $attribute_settings[ $name ] ) ) {
				if ( 'array' === $attribute_settings[ $name ] ) {
					if ( ! empty( $attribute ) ) {
						if ( ! is_array( $attribute ) ) {
							$attributes[ $name ] = array_map( 'trim', explode( ',', $attribute ) );
						} else {
							$attributes[ $name ] = $attribute;
						}
					} else {
						$attributes[ $name ] = array();
					}
				}
				if ( 'int' === $attribute_settings[ $name ] ) {
					$attributes[ $name ] = absint( $attribute );
				}
				if ( 'unsignedint' === $attribute_settings[ $name ] ) {
					$attributes[ $name ] = (int) $attribute;
				}
				if ( 'bool' === $attribute_settings[ $name ] ) {
					$attributes[ $name ] = boolval( $attribute );
				}
				if ( 'filter' === $attribute_settings[ $name ] ) {
					// if filter is set in config.
					$attributes[ $name ] = absint( $attribute );
					// if filter is set via request.
					if ( ! empty( $GLOBALS['wp']->query_vars['personiofilter'][ $name ] ) ) {
						$attributes[ $name ] = absint( $GLOBALS['wp']->query_vars['personiofilter'][ $name ] );
					}
				}
				if ( 'listing_template' === $attribute_settings[ $name ] ) {
					$attributes[ $name ] = $attribute;
					if ( false === Templates::get_instance()->has_template( 'parts/archive/' . $attribute . '.php' ) ) {
						$attributes[ $name ] = 'default';
					}
				}
				if ( 'jobdescription_template' === $attribute_settings[ $name ] ) {
					$attributes[ $name ] = $attribute;
					if ( false === Templates::get_instance()->has_template( 'parts/jobdescription/' . $attribute . '.php' ) ) {
						$attributes[ $name ] = 'default';
					}
				}
			}
		}

		// return the resulting array with checked and secured attributes.
		return $attributes;
	}

	/**
	 * Format a given datetime with WP-settings and functions.
	 *
	 * @param string $date The date as YYYY-MM-DD.
	 * @return string
	 */
	public static function get_format_date_time( string $date ): string {
		$dt = get_date_from_gmt( $date );
		return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $dt ) );
	}

	/**
	 * Checks whether a given plugin is active.
	 *
	 * Used because WP's own function is_plugin_active() is not accessible everywhere.
	 *
	 * @param string $plugin Path to the requested plugin relative to plugin-directory.
	 * @return bool
	 */
	public static function is_plugin_active( string $plugin ): bool {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true );
	}

	/**
	 * Return the absolute URL to the plugin.
	 *
	 * @return string
	 */
	public static function get_plugin_url(): string {
		return trailingslashit( plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ) );
	}

	/**
	 * Return the absolute local filesystem-path to the plugin.
	 *
	 * @return string
	 */
	public static function get_plugin_path(): string {
		return trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) );
	}

	/**
	 * Return whether the PersonioURL is set or not.
	 *
	 * @return bool
	 */
	public static function is_personio_url_set(): bool {
		$url = get_option( 'personioIntegrationUrl', '' );
		return ! empty( $url );
	}

	/**
	 * Return whether the current theme is a block-theme.
	 *
	 * @return bool
	 */
	public static function theme_is_fse_theme(): bool {
		if ( function_exists( 'wp_is_block_theme' ) ) {
			return wp_is_block_theme();
		}
		return false;
	}

	/**
	 * Check if Settings-Errors-entry already exists in array.
	 *
	 * @param string $entry The entry.
	 * @param array  $errors The list of errors.
	 * @return false
	 */
	public static function check_if_setting_error_entry_exists_in_array( string $entry, array $errors ): bool {
		foreach ( $errors as $error ) {
			if ( $error['setting'] === $entry ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Prüfe, ob der Import per CLI aufgerufen wird.
	 * Z.B. um einen Fortschrittsbalken anzuzeigen.
	 *
	 * @return bool
	 */
	public static function is_cli(): bool {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * Get current URL in frontend and backend.
	 *
	 * @return string
	 */
	public static function get_current_url(): string {
		if ( is_admin() && ! empty( $_SERVER['REQUEST_URI'] ) ) {
			return admin_url( basename( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );
		}

		// set return value for page url.
		$page_url = '';

		// get actual object.
		$object = get_queried_object();
		if ( $object instanceof WP_Post_Type ) {
			$page_url = get_post_type_archive_link( $object->name );
		}
		if ( $object instanceof WP_Post ) {
			$page_url = get_permalink( $object->ID );
		}

		// return result.
		return $page_url;
	}

	/**
	 * Regex to get html tag attribute value.
	 *
	 * @param string $attrib The attribute.
	 * @param string $tag The tag.
	 * @return string
	 */
	public static function get_attribute_value_from_html( string $attrib, string $tag ): string {
		// get attribute from html tag.
		$re = '/' . preg_quote( $attrib, null ) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
		if ( preg_match( $re, $tag, $match ) ) {
			return urldecode( $match[2] );
		}
		return false;
	}

	/**
	 * Get all files of directory recursively.
	 *
	 * @param string $path The path.
	 * @return array
	 */
	public static function get_files_from_directory( string $path = '.' ): array {
		// get WP_Filesystem as object.
		global $wp_filesystem;
		WP_Filesystem();

		// load files recursive in array and return resulting list.
		return self::get_files( $wp_filesystem->dirlist( $path, true, true ), $path );
	}

	/**
	 * Recursively load files from given array.
	 *
	 * @param array  $files Array of file we iterate through.
	 * @param string $path Absolute path where the files are located.
	 * @param array  $file_list List of files.
	 *
	 * @return array
	 */
	private static function get_files( array $files, string $path, array $file_list = array() ): array {
		foreach ( $files as $filename => $settings ) {
			if ( 'f' === $settings['type'] ) {
				$file_list[ $filename ] = $path . $filename;
			}
			if ( 'd' === $settings['type'] ) {
				$file_list = self::get_files( $settings['files'], $path . trailingslashit( $filename ), $file_list );
			}
		}

		return $file_list;
	}

	/**
	 * Get language-specific Personio account login url.
	 *
	 * @return string
	 */
	public static function get_personio_login_url(): string {
		if ( 'de' === Languages::get_instance()->get_current_lang() ) {
			return 'https://www.personio.de/login/';
		}
		return 'https://www.personio.com/login/';
	}

	/**
	 * Get language-specific Personio account support url.
	 *
	 * @return string
	 */
	public static function get_personio_support_url(): string {
		if ( 'de' === Languages::get_instance()->get_current_lang() ) {
			return 'https://support.personio.de/';
		}
		return 'https://support.personio.de/hc/en-us/';
	}

	/**
	 * Return the configured Personio-URL.
	 *
	 * @return string
	 */
	public static function get_personio_url(): string {
		return apply_filters( 'personio_integration_url', get_option( 'personioIntegrationUrl', '' ) );
	}

	/**
	 * Get list of blogs in a multisite-installation.
	 *
	 * @return array
	 */
	public static function get_blogs(): array {
		if ( false === is_multisite() ) {
			return array();
		}

		// Get DB-connection.
		global $wpdb;

		// get blogs in this site-network.
		return $wpdb->get_results(
			"
            SELECT blog_id
            FROM {$wpdb->blogs}
            WHERE site_id = '{$wpdb->siteid}'
            AND spam = '0'
            AND deleted = '0'
            AND archived = '0'
        "
		);
	}

	/**
	 * Return the settings-URL.
	 *
	 * @param string $tab String which represents the tab to link to.
	 *
	 * @return string
	 */
	public static function get_settings_url( string $tab = '' ): string {
		$params = array(
			'post_type' => PersonioPosition::get_instance()->get_name(),
			'page'      => 'personioPositions',
		);
		if ( ! empty( $tab ) ) {
			$params['tab'] = $tab;
		}
		return add_query_arg( $params, get_admin_url() . 'edit.php' );
	}

	/**
	 * Return the name of this plugin.
	 *
	 * @return string
	 */
	public static function get_plugin_name(): string {
		$plugin_data = get_plugin_data( WP_PERSONIO_INTEGRATION_PLUGIN );
		if ( ! empty( $plugin_data ) && ! empty( $plugin_data['Name'] ) ) {
			return $plugin_data['Name'];
		}
		return '';
	}

	/**
	 * Return language-depending Personio URL example.
	 *
	 * @return string
	 */
	public static function get_personio_url_example(): string {
		return Languages::get_instance()->is_german_language() ? 'https://dein-unternehmen.jobs.personio.de' : 'https://yourcompany.jobs.personio.com';
	}

	/**
	 * Return URL for documentation about templates.
	 *
	 * @return string
	 */
	public static function get_template_documentation_url(): string {
		return Languages::get_instance()->is_german_language() ? 'https://github.com/threadi/wp-personio-integration-light/blob/master/doc/templates_de.md' : 'https://github.com/threadi/wp-personio-integration-light/blob/master/doc/templates.md';
	}

	/**
	 * Return the plugin support url: the forum on wordpress.org.
	 *
	 * @return string
	 */
	public static function get_plugin_support_url(): string {
		return 'https://wordpress.org/support/plugin/personio-integration-light/';
	}
}

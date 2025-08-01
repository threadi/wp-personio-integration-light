<?php
/**
 * File with general helper tasks for the plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Templates;
use Plugin_Upgrader;
use SimpleXMLElement;
use WP_Ajax_Upgrader_Skin;
use WP_Error;
use WP_Filesystem_Base;
use WP_Filesystem_Direct;
use WP_Post;
use WP_Post_Type;
use WP_Rewrite;
use WP_Screen;

/**
 * The helper class itself.
 */
class Helper {

	/**
	 * Return the logo as img
	 *
	 * @param bool $big_logo True to output the big logo.
	 *
	 * @return string
	 */
	public static function get_logo_img( bool $big_logo = false ): string {
		if ( $big_logo ) {
			return '<img src="' . self::get_plugin_url() . 'gfx/personio_logo_big.png" alt="Personio Logo" class="logo">';
		}
		return '<img src="' . self::get_plugin_url() . 'gfx/personio_icon.png" alt="Personio Logo" class="logo">';
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
	 * @noinspection PhpUnused
	 */
	public static function get_import_url(): string {
		return add_query_arg(
			array(
				'action' => 'personioPositionsImport',
				'nonce'  => wp_create_nonce( 'personio-integration-import' ),
			),
			get_admin_url() . 'admin.php'
		);
	}

	/**
	 * Return the url to remove all positions in local database.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public static function get_delete_url(): string {
		return add_query_arg(
			array(
				'action' => 'personioPositionsDelete',
				'nonce'  => wp_create_nonce( 'personio-integration-delete' ),
			),
			get_admin_url() . 'admin.php'
		);
	}

	/**
	 * Get list of available filter types.
	 *
	 * @return array<string,string>
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
		 * @param array<string,string> $types The list of types.
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
	public static function is_rest_request(): bool {
		if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) // Case #1.
			|| ( isset( $GLOBALS['wp']->query_vars['rest_route'] ) // (#2)
				&& str_starts_with( $GLOBALS['wp']->query_vars['rest_route'], '/' ) ) ) {
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
		if ( is_array( $current_url ) && is_array( $rest_url ) && isset( $current_url['path'], $rest_url['path'] ) ) {
			return str_starts_with( $current_url['path'], $rest_url['path'] );
		}
		return false;
	}

	/**
	 * Check and secure the allowed shortcode-attributes.
	 *
	 * @param array<string,mixed> $attribute_defaults List of attribute defaults.
	 * @param array<string,mixed> $attribute_settings List of attribute settings.
	 * @param array<string,mixed> $attributes List of actual attribute values.
	 * @return array<string,string|array<int,mixed>>
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

		// check if language-setting is valid, if given.
		if ( ! empty( $attributes['lang'] ) && ! Languages::get_instance()->is_language_supported( $attributes['lang'] ) ) {
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
					$attributes[ $name ] = (bool) $attribute;
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
				if ( 'excerpt_template' === $attribute_settings[ $name ] ) {
					$attributes[ $name ] = $attribute;
					if ( false === Templates::get_instance()->has_template( 'parts/details/' . $attribute . '.php' ) ) {
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
	 * Checks whether a given plugin is installed.
	 *
	 * @param string $plugin Path to the requested plugin relative to plugin-directory.
	 * @return bool
	 */
	public static function is_plugin_installed( string $plugin ): bool {
		return file_exists( trailingslashit( WP_PLUGIN_DIR ) . $plugin );
	}

	/**
	 * Return the absolute URL to the plugin (already trailed with slash).
	 *
	 * @return string
	 */
	public static function get_plugin_url(): string {
		return trailingslashit( plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ) );
	}

	/**
	 * Return the absolute local filesystem-path (already trailed with slash) to the plugin.
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
		return ! empty( self::get_personio_url() );
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
	 * @param string                  $entry The entry.
	 * @param array<string|int,mixed> $errors The list of errors.
	 * @return bool
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
	 * Check if WP CLI has been called.
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
		if ( ! empty( $_SERVER['REQUEST_URI'] ) && is_admin() ) {
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

		// return empty string if no URL could be loaded.
		if ( ! $page_url ) {
			return '';
		}

		// return result.
		return $page_url;
	}

	/**
	 * Regex to get html tag attribute value.
	 *
	 * @param string $attribute The attribute.
	 * @param string $tag The tag.
	 * @return string|false
	 */
	public static function get_attribute_value_from_html( string $attribute, string $tag ): string|false {
		// get attribute from html tag.
		$re = '/' . preg_quote( $attribute, null ) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
		if ( preg_match( $re, $tag, $match ) ) {
			return urldecode( $match[2] );
		}
		return false;
	}

	/**
	 * Get all files of directory recursively.
	 *
	 * @param string $path The path.
	 *
	 * @return array<string>
	 */
	public static function get_files_from_directory( string $path = '.' ): array {
		// get WP_Filesystem as object.
		$wp_filesystem = self::get_wp_filesystem();

		// get the file list.
		$files = $wp_filesystem->dirlist( $path, true, true );

		// bail if no files could be loaded.
		if ( ! $files ) {
			return array();
		}

		// load files recursive in array and return resulting list.
		return self::get_files( $files, $path );
	}

	/**
	 * Recursively load files from given array.
	 *
	 * @param array<string,array<string,mixed>> $files Array of file we iterate through.
	 * @param string                            $path Absolute path where the files are located.
	 * @param array<string>                     $file_list List of files.
	 *
	 * @return array<string>
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
	 * Return language-specific Personio account login url.
	 *
	 * @return string
	 */
	public static function get_personio_login_url(): string {
		// get the configured Personio Login URL.
		$personio_login_url = get_option( 'personioIntegrationLoginUrl' );

		// return default URLs, if no Login URL is configured.
		if ( empty( $personio_login_url ) ) {
			if ( Languages::get_instance()->is_german_language() ) {
				return 'https://www.personio.de/login/';
			}
			return 'https://www.personio.com/login/';
		}

		// return the custom Personio Login URL.
		return $personio_login_url;
	}

	/**
	 * Return HTML-link with icon to edit specific entity in Personio account.
	 *
	 * @param Position $position_obj The position object.
	 *
	 * @return string
	 */
	public static function get_personio_edit_link( Position $position_obj ): string {
		// get the configured Personio Login URL.
		$personio_login_url = get_option( 'personioIntegrationLoginUrl' );

		// bail if no login URL is given.
		if( empty( $personio_login_url ) ) {
			return '';
		}

		return ' <a href="' . esc_url( $personio_login_url . '/recruiting/positions/' . $position_obj->get_personio_id() ) . '" target="_blank" class="personio-integration-icon-link"><span class="dashicons dashicons-edit"></span></a>';
	}

	/**
	 * Get language-specific Personio account support url.
	 *
	 * @return string
	 */
	public static function get_personio_support_url(): string {
		if ( Languages::get_instance()->is_german_language() ) {
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
		$url = get_option( 'personioIntegrationUrl' );

		/**
		 * Filter the Personio URL.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $url The configured Personio URL.
		 */
		return apply_filters( 'personio_integration_url', $url );
	}

	/**
	 * Get list of blogs in a multisite-installation.
	 *
	 * @return array<string,mixed>
	 */
	public static function get_blogs(): array {
		if ( false === is_multisite() ) {
			return array();
		}

		// Get DB-connection.
		global $wpdb;

		// get blogs in this site-network.
		return $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
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
	 * @param string $page The page to call (e.g. "personioPositions").
	 * @param string $tab  String which represents the tab to link to.
	 * @param string $sub_tab String for the sub-tab to link to.
	 *
	 * @return string
	 */
	public static function get_settings_url( string $page = 'personioPositions', string $tab = '', string $sub_tab = '' ): string {
		$params = array(
			'post_type' => PersonioPosition::get_instance()->get_name(),
			'page'      => $page,
		);
		if ( ! empty( $tab ) ) {
			$params['tab'] = $tab;
		}
		if ( ! empty( $sub_tab ) ) {
			$params['subtab'] = $sub_tab;
		}
		return add_query_arg( $params, get_admin_url() . 'edit.php' );
	}

	/**
	 * Return the name of this plugin.
	 *
	 * @return string
	 */
	public static function get_plugin_name(): string {
		// get the plugin data.
		$plugin_data = get_plugin_data( WP_PERSONIO_INTEGRATION_PLUGIN );

		// bail if no 'Name' is in the result.
		if ( empty( $plugin_data['Name'] ) ) {
			return '';
		}

		// return the plugin name.
		return $plugin_data['Name'];
	}

	/**
	 * Return language-depending Personio URL example.
	 *
	 * @return string
	 */
	public static function get_personio_url_example(): string {
		return Languages::get_instance()->is_german_language() ? 'https://dein-unternehmen.jobs.personio.de' : 'https://your-company.jobs.personio.com';
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
	 * Return language-depending Personio Login URL example.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public static function get_personio_login_url_example(): string {
		return Languages::get_instance()->is_german_language() ? 'https://dein-unternehmen.personio.de' : 'https://your-company.personio.com';
	}

	/**
	 * Return the plugin support url: the forum on WordPress.org.
	 *
	 * @return string
	 */
	public static function get_plugin_support_url(): string {
		return 'https://wordpress.org/support/plugin/personio-integration-light/';
	}

	/**
	 * Return the title of the actual theme.
	 *
	 * @return string
	 */
	public static function get_theme_title(): string {
		return wp_get_theme()->get( 'Name' );
	}

	/**
	 * Return the review-URL.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public static function get_review_url(): string {
		return 'https://wordpress.org/support/plugin/personio-integration-light/reviews/#new-post';
	}

	/**
	 * Return list of our own cpts as names.
	 *
	 * @return array<int,string>
	 */
	public static function get_list_of_our_cpts(): array {
		$list = array(
			PersonioPosition::get_instance()->get_name(),
		);

		/**
		 * Filter the list of custom post types this plugin is using.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array<int,string> $list The list of post types.
		 */
		return apply_filters( 'personio_integration_list_of_cpts', $list );
	}

	/**
	 * Replace all linebreaks in given string.
	 *
	 * @param string $text_to_parse The text where we replace the line breaks.
	 *
	 * @return string
	 */
	public static function replace_linebreaks( string $text_to_parse ): string {
		// get the result.
		$result = preg_replace( '/\s+/', ' ', $text_to_parse );

		// bail if result is not a string.
		if ( ! is_string( $result ) ) {
			return '';
		}

		// return the resulting string.
		return $result;
	}

	/**
	 * Return the version of the given file.
	 *
	 * With WP_DEBUG or plugin-debug enabled its @filemtime().
	 * Without this it's the plugin-version.
	 *
	 * @param string $filepath The absolute path to the requested file.
	 *
	 * @return string
	 */
	public static function get_file_version( string $filepath ): string {
		// check for WP_DEBUG.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			return (string) filemtime( $filepath );
		}

		// check for own debug.
		if ( 1 === absint( get_option( 'personioIntegration_debug', 0 ) ) ) {
			return (string) filemtime( $filepath );
		}

		$plugin_version = WP_PERSONIO_INTEGRATION_VERSION;

		/**
		 * Filter the used file version (for JS- and CSS-files which get enqueued).
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $plugin_version The plugin-version.
		 * @param string $filepath The absolute path to the requested file.
		 */
		return apply_filters( 'personio_integration_file_version', $plugin_version, $filepath );
	}

	/**
	 * Add new entry with its key on specific position in array.
	 *
	 * @param array<int|string,mixed>|null $fields The array we want to change.
	 * @param int                          $position The position where the new array should be added.
	 * @param array<int|string,mixed>      $array_to_add The new array which should be added.
	 *
	 * @return array<int|string,mixed>
	 */
	public static function add_array_in_array_on_position( array|null $fields, int $position, array $array_to_add ): array {
		if ( is_null( $fields ) ) {
			return array();
		}
		return array_slice( $fields, 0, $position, true ) + $array_to_add + array_slice( $fields, $position, null, true );
	}

	/**
	 * Update list of used page builder.
	 *
	 * @param string $page_builder_name The name of the page builder to add to the list.
	 * @return void
	 */
	public static function update_page_builder_list( string $page_builder_name ): void {
		$page_builder_list = get_option( 'personioIntegrationPageBuilder' );
		if ( ! is_array( $page_builder_list ) ) {
			$page_builder_list = array();
		}
		if ( ! in_array( $page_builder_name, $page_builder_list, true ) ) {
			$page_builder_list[] = $page_builder_name;
			update_option( 'personioIntegrationPageBuilder', $page_builder_list );
		}
	}

	/**
	 * Return URL for shortcode documentation.
	 *
	 * @return string
	 */
	public static function get_shortcode_documentation_url(): string {
		return Languages::get_instance()->is_german_language() ? 'https://github.com/threadi/wp-personio-integration-light/blob/master/doc/shortcodes_de.md' : 'https://github.com/threadi/wp-personio-integration-light/blob/master/doc/shortcodes.md';
	}

	/**
	 * Add custom inline style for output.
	 *
	 * @param string $style The style (CSS-code).
	 *
	 * @return void
	 */
	public static function add_inline_style( string $style ): void {
		// bail if style it empty.
		if ( empty( $style ) ) {
			return;
		}

		// set the style.
		wp_add_inline_style( 'wp-block-library', $style );
	}

	/**
	 * Return the GitHub documentation link.
	 *
	 * @return string
	 */
	public static function get_github_documentation_link(): string {
		return 'https://github.com/threadi/wp-personio-integration-light/tree/master/doc';
	}

	/**
	 * Return whether we should load styles depending on actual called backend page.
	 *
	 * @param string $hook The used hook.
	 *
	 * @return bool
	 */
	public static function do_not_load_styles( string $hook ): bool {
		// bail if function is used in frontend.
		if ( ! is_admin() ) {
			return false;
		}

		// do not load our files outside our own backend pages.
		if ( function_exists( 'get_current_screen' ) && in_array( $hook, array( 'edit.php', 'post.php', 'edit-tags.php', 'term.php' ), true ) ) {
			$screen = get_current_screen();
			// bail if screen could not be loaded.
			if ( ! $screen instanceof WP_Screen ) {
				return false;
			}
			if ( ! in_array( $screen->post_type, apply_filters( 'personio_integration_light_do_not_load_on_cpt', array( PersonioPosition::get_instance()->get_name() ) ), true ) ) {
				return true;
			}
		} elseif ( ! str_contains( $hook, 'personio' ) && ! str_contains( $hook, 'options-permalink.php' ) ) {
			// bail if none of our pages is used.
			return true;
		}

		// return false to not prevent the loading of styles in backend.
		return false;
	}

	/**
	 * Return the WP Filesystem object.
	 *
	 * @param bool $local True to get the local filesystem object.
	 *
	 * @return WP_Filesystem_Base
	 */
	public static function get_wp_filesystem( bool $local = false ): WP_Filesystem_Base {
		// get WP Filesystem-handler for local files if requested.
		if ( $local ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

			return new WP_Filesystem_Direct( false );
		}

		// get global WP Filesystem handler.
		require_once ABSPATH . '/wp-admin/includes/file.php';
		\WP_Filesystem();
		global $wp_filesystem;

		// bail if wp_filesystem is not of "WP_Filesystem_Base".
		if ( ! $wp_filesystem instanceof WP_Filesystem_Base ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
			return new WP_Filesystem_Direct( false );
		}

		// return local object on any error.
		if ( $wp_filesystem->errors->has_errors() ) {
			// log this event.
			/* translators: %1$s will be replaced by a name. */
			Log::get_instance()->add( sprintf( __( '<strong>Error during loading the required WordPress-own filesystem object!</strong><br>We will now use the local filesystem object and hope it will work.<br><br>Tipps to solve this:<ul><li>Check the following error and speak to your WordPress administrator about it.</li><li>Check your <em>wp-config.php</em> if you have the constant "FS_METHOD" set there. If yes, remove it and check if your WordPress can save media files.</li><li>Ask the support of your hoster for help.</li></ul>Used filesystem mode: <em>%1$s</em><br>The following errors occurred:', 'personio-integration-light' ), get_filesystem_method() ) . ' <code>' . wp_json_encode( $wp_filesystem->errors ) . '</code>', 'error', 'system' );

			// embed the local directory object.
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

			return new WP_Filesystem_Direct( false );
		}

		// return the requested filesystem object.
		return $wp_filesystem;
	}

	/**
	 * Return the writable wp-config.php path.
	 *
	 * @return string
	 */
	public static function get_wp_config_path(): string {
		$wp_config_php = 'wp-config';
		/**
		 * Filter to change the filename of the used wp-config.php without its extension .php.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param string $wp_config_php The filename.
		 */
		$wp_config_php = apply_filters( 'personio_integration_light_wp_config_name', $wp_config_php );

		// get path for wp-config.php.
		$wp_config_php_path = ABSPATH . $wp_config_php . '.php';

		/**
		 * Filter the path for the wp-config.php before we return it.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param string $wp_config_php_path The path.
		 */
		return apply_filters( 'personio_integration_light_wp_config_path', $wp_config_php_path );
	}

	/**
	 * Return whether a given file is writable.
	 *
	 * @param string $file The file with absolute path.
	 *
	 * @return bool
	 */
	public static function is_writable( string $file ): bool {
		return self::get_wp_filesystem()->is_writable( $file );
	}

	/**
	 * Create JSON from given array.
	 *
	 * @param array<string|int,mixed>|WP_Error|SimpleXMLElement $source The source array.
	 * @param int                                               $flag Flags to use for this JSON.
	 *
	 * @return string
	 */
	public static function get_json( array|WP_Error|SimpleXMLElement $source, int $flag = 0 ): string {
		// create JSON.
		$json = wp_json_encode( $source, $flag );

		// bail if creating the JSON failed.
		if ( ! $json ) {
			return '';
		}

		// return resulting JSON-string.
		return $json;
	}

	/**
	 * Return the URL where the user could manage its API integrations in Personio.
	 *
	 * @return string
	 */
	public static function get_personio_api_management_url(): string {
		return get_option( 'personioIntegrationLoginUrl' ) . '/configuration/marketplace/connected';
	}

	/**
	 * Return the Personio documentation about API credentials.
	 *
	 * @return string
	 */
	public static function get_personio_api_documentation_url(): string {
		if ( Languages::get_instance()->is_german_language() ) {
			return 'https://support.personio.de/hc/de/articles/4404623630993-API-Zugriffsdaten-generieren-und-verwalten';
		}
		return 'https://support.personio.de/hc/en-us/articles/4404623630993-Generate-and-manage-API-credentials';
	}

	/**
	 * Install a plugin by given download URL.
	 *
	 * @param string $download_url The download URL.
	 * @param string $plugin_slug The plugin slug.
	 *
	 * @return int|null|bool|WP_Error
	 */
	public static function install_plugin( string $download_url, string $plugin_slug ): int|null|bool|WP_Error {
		// include required libs for installation.
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
		require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

		// run the installer.
		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $download_url ); // @phpstan-ignore property.nonObject

		// bail if error occurred.
		if ( is_wp_error( $result ) ) {
			// log this event.
			Log::get_instance()->add( __( 'Following during installing Personio Integration Pro:', 'personio-integration-light' ) . ' <code>' . wp_json_encode( $result ) . '</code>', 'error', 'system' );

			// do nothing more.
			return false;
		}

		// get plugin path.
		$plugin_path = self::get_plugin_path_from_slug( $plugin_slug );

		// bail if no path could be found.
		if ( false === $plugin_path ) {
			// log this event.
			Log::get_instance()->add( __( 'Personio Integration Pro plugin path could not be read.', 'personio-integration-light' ), 'error', 'system' );

			// do nothing more.
			return false;
		}

		// activate the plugin.
		require_once ABSPATH . 'wp-admin/includes/admin.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		return activate_plugin( $plugin_path );
	}

	/**
	 * Return plugin path from slug.
	 *
	 * @source WooCommerce
	 *
	 * @param string $slug The requested slug.
	 *
	 * @return false|string
	 */
	public static function get_plugin_path_from_slug( string $slug ): false|string {
		$plugins = get_plugins();

		if ( str_contains( $slug, '/' ) ) {
			// The slug is already a plugin path.
			return $slug;
		}

		foreach ( $plugins as $plugin_path => $data ) {
			$path_parts = explode( '/', $plugin_path );
			if ( $path_parts[0] === $slug ) {
				return $plugin_path;
			}
		}

		return false;
	}
}

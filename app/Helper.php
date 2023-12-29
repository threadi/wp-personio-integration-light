<?php
/**
 * File with general helper tasks for the plugin.
 *
 * @package personio-integration-light
 */

namespace App;

use App\PersonioIntegration\Position;
use App\Plugin\Languages;
use App\Plugin\Templates;
use Exception;
use WP_Post;
use WP_Post_Type;
use WP_Rewrite;

/**
 * The helper class itself.
 */
class Helper {

	/**
	 * Add terms from an array to a taxonomy.
	 *
	 * @param array  $list_or_terms List of terms to add.
	 * @param string $taxonomy The taxonomy.
	 * @return void
	 */
	public static function add_terms( array $list_or_terms, string $taxonomy ): void {
		foreach ( $list_or_terms as $term => $term_title ) {
			if ( ! term_exists( $term, $taxonomy ) ) {
				wp_insert_term(
					$term,
					$taxonomy
				);
			}
		}
	}

	/**
	 * Check whether a german language is used in this Wordpress-projekt.
	 *
	 * @return bool
	 */
	public static function is_german_language(): bool {
		// TODO find better way for languages.
		$german_languages = array(
			'de-DE',
			'de-DE_formal',
			'de-CH',
			'de-AT',
		);
		return in_array( get_bloginfo( 'language' ), $german_languages, true );
	}

	/**
	 * Return the logo as img
	 *
	 * @return string
	 */
	private static function get_logo_img(): string {
		return '<img src="' . self::get_plugin_path() . 'gfx/personio_icon.png" alt="">';
	}

	/**
	 * Return the translated transient-string of given transient.
	 *
	 * TODO use transients-object instead of this.
	 *
	 * @param string $transient The transient.
	 * @return string
	 */
	public static function get_admin_transient_content( string $transient ): string {
		$transient_value = get_transient( $transient );

		$array = array(
			'personio_integration_no_simplexml'           => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p><u>%s</u> %s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				__( 'Plugin was not activated!', 'personio-integration-light' ),
				__( 'The PHP extension simplexml is missing on the system. Please contact your hoster about this.', 'personio-integration-light' )
			),
			'personio_integration_no_url_set'             => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p><u>%s</u> %s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				__( 'The specification of your Personio URL is still pending.', 'personio-integration-light' ),
				sprintf(
					/* translators: %1$s is replaced with "string" */
					__( 'To do this, please go to the <a href="%s">settings page</a>.', 'personio-integration-light' ),
					esc_url(
						add_query_arg(
							array(
								'page'      => 'personioPositions',
								'post_type' => WP_PERSONIO_INTEGRATION_CPT,
							),
							get_admin_url() . 'edit.php'
						)
					)
				)
			),
			'personio_integration_no_position_imported'   => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p><u>%s</u> %s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				__( 'You have not imported your open positions from Personio until now.', 'personio-integration-light' ),
				/* translators: %1$s is replaced with "string" */
				__( 'Click on the following button to import your positions from Personio now:', 'personio-integration-light' ) . ' <br><br><a href="' . self::get_import_url() . '" class="button button-primary">' . __( 'Run import', 'personio-integration-light' ) . '</a>'
			),
			'personio_integration_import_run'             => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				sprintf(
					/* translators: %1$s is replaced with "string", %2$s is replaced with "string" */
					__(
						'<strong>The import has been manually run.</strong> Please check the list of positions <a href="%1$s">in backend</a> and <a href="%2$s">frontend</a>.',
						'personio-integration-light'
					),
					esc_url(
						add_query_arg(
							array(
								'post_type' => WP_PERSONIO_INTEGRATION_CPT,
							),
							get_admin_url() . 'edit.php'
						)
					),
					get_post_type_archive_link( WP_PERSONIO_INTEGRATION_CPT )
				)
			),
			'personio_integration_import_cancel'          => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				sprintf(
				/* translators: %1$s is replaced with "string", %2$s is replaced with "string" */
					__(
						'<strong>The import has been canceled.</strong> Please check the list of positions <a href="%1$s">in backend</a> and <a href="%2$s">frontend</a>.',
						'personio-integration-light'
					),
					esc_url(
						add_query_arg(
							array(
								'post_type' => WP_PERSONIO_INTEGRATION_CPT,
							),
							get_admin_url() . 'edit.php'
						)
					),
					get_post_type_archive_link( WP_PERSONIO_INTEGRATION_CPT )
				)
			),
			'personio_integration_delete_run'             => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				__(
					'<strong>The positions has been deleted.</strong> You can run the import anytime again to import positions.',
					'personio-integration-light'
				)
			),
			'personio_integration_could_not_delete'       => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				__(
					'<strong>The positions could not been deleted.</strong> An import is actual running.',
					'personio-integration-light'
				)
			),
			'personio_integration_import_now'             => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				__( '<strong>The specified Personio URL is reachable.</strong> Click on the following button to import your positions from Personio now:', 'personio-integration-light' ) . ' <br><br><a href="' . self::get_import_url() . '" class="button button-primary personio-integration-import-hint">' . __( 'Run import', 'personio-integration-light' ) . '</a>'
			),
			'personio_integration_url_not_usable'         => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				sprintf(
					/* translators: %1$s is replaced with the entered Personio-URL */
					__( 'The specified Personio URL %s is not usable for this plugin. Please double-check the URL in your Personio-account under Settings > Recruiting > Career Page > Activations. Please also check if the XML interface is enabled there.', 'personio-integration-light' ),
					''
				)
			),
			'personio_integration_limit_hint'             => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				sprintf(
				/* translators: %1$s is replaced with "string" */
					__( 'The list of positions is limited to a maximum of 10 entries in the frontend. With <a href="%s">Personio Integration Pro version</a>, any number of positions can be displayed.', 'personio-integration-light' ),
					self::get_pro_url()
				)
			),
			'personio_integration_import_canceled'        => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				__( '<strong>The running import has been canceled.</strong> Click on the following button to start a new import. If it also takes to long please check your hosting logfiles for possible restrictions mentioned there.', 'personio-integration-light' ) . ' <br><br><a href="' . self::get_import_url() . '" class="button button-primary personio-integration-import-hint">' . __( 'Run import', 'personio-integration-light' ) . '</a>'
			),
			'personio_integration_old_templates'          => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>%s<p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				__( '<strong>You are using a child theme that contains outdated Personio Integration Light template files.</strong> Please compare the following files in your child-theme with the one this plugin provides:', 'personio-integration-light' ),
				$transient_value,
				__( 'Hint: the version-number in the header of the files must match.', 'personio-integration-light' )
			),
			'personio_integration_divi'                   => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
				sprintf( __( 'We realized that you are using Divi - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Divi.', 'personio-integration-light' ), self::get_pro_url() ),
			),
			'personio_integration_elementor'              => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
				sprintf( __( 'We realized that you are using Elementor - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Elementor.', 'personio-integration-light' ), self::get_pro_url() ),
			),
			'personio_integration_wpbakery'               => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
				sprintf( __( 'We realized that you are using WPBakery - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in WPBakery.', 'personio-integration-light' ), self::get_pro_url() ),
			),
			'personio_integration_beaver'                 => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
				sprintf( __( 'We realized that you are using Beaver Builder - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Beaver Builder.', 'personio-integration-light' ), self::get_pro_url() ),
			),
			'personio_integration_siteorigin'             => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
				sprintf( __( 'We realized that you are using Site Origin - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Site Origin.', 'personio-integration-light' ), self::get_pro_url() ),
			),
			'personio_integration_themify'                => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
				sprintf( __( 'We realized that you are using Themify - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Themify.', 'personio-integration-light' ), self::get_pro_url() ),
			),
			'personio_integration_avada'                  => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
				sprintf( __( 'We realized that you are using Avada - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Avada.', 'personio-integration-light' ), self::get_pro_url() ),
			),
			'personio_integration_admin_show_review_hint' => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				sprintf(
				/* translators: %1$s is replaced with "string" */
					sprintf( __( 'Your use the WordPress-plugin Personio Integration since more than %d days. Do you like it? Feel free to <a href="https://wordpress.org/plugins/personio-integration-light/#reviews" target="_blank">leave us a review (opens new window)</a>.', 'personio-integration-light' ), ( absint( get_option( 'personioIntegrationLightInstallDate', 1 ) - time() ) / 60 / 60 / 24 ) ) . ' <span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span>',
					self::get_pro_url()
				)
			),
			'personio_integration_admin_show_text_domain_hint' => sprintf(
				'<h3>' . self::get_logo_img() . '%s</h3><p>%s</p>',
				__( 'Personio Integration', 'personio-integration-light' ),
				__( 'You are using our old text domain to adapt textes of Personio Integration Light. Please note that we have a new text domain. You need to re-enter your customizations in this one. Sorry for the inconvenience - it will be a one-time thing.', 'personio-integration-light' ),
			),
		);

		if ( empty( $array[ $transient ] ) ) {
			return '';
		}

		return $array[ $transient ];
	}

	/**
	 * Get the language-depending list-slug.
	 *
	 * @return string
	 */
	public static function get_archive_slug(): string {
		$slug = 'positions';
		if ( 'de' === self::get_wp_lang() ) {
			$slug = 'stellen';
		}
		return $slug;
	}

	/**
	 * Get the language-depending detail-slug.
	 *
	 * @return string
	 */
	public static function get_detail_slug(): string {
		$slug = 'position';
		if ( 'de' === self::get_wp_lang() ) {
			$slug = 'stelle';
		}
		return $slug;
	}

	/**
	 * Return the URL with information about the Pro-version of this plugin.
	 *
	 * @return string
	 */
	public static function get_pro_url(): string {
		return 'https://laolaweb.com/plugins/personio-wordpress-plugin/';
	}

	/**
	 * Return the url to start the import manually.
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
	 * Return the url to remove all positions.
	 *
	 * @return string
	 */
	public static function get_delete_url(): string {
		return esc_url(
			add_query_arg(
				array(
					'action' => 'personioPositionsDelete',
					'nonce'  => wp_create_nonce( 'wp-personio-integration-delete' ),
				),
				get_admin_url() . 'admin.php'
			)
		);
	}

	/**
	 * Get the name of a taxonomy on a position.
	 *
	 * @param string   $taxonomy The taxonomy.
	 * @param Position $position The requested positon.
	 * @return string
	 */
	public static function get_taxonomy_name_of_position( string $taxonomy, Position $position ): string {
		$name = '';
		switch ( $taxonomy ) {
			case 'recruitingCategory':
				$name = $position->get_recruiting_category_name();
				break;
			case 'schedule':
				$name = $position->get_schedule_name();
				break;
			case 'office':
				$name = $position->get_office_name();
				break;
			case 'department':
				$name = $position->get_department_name();
				break;
			case 'seniority':
				$name = $position->get_seniority_name();
				break;
			case 'experience':
				$name = $position->get_experience_name();
				break;
			case 'occupation':
				$name = $position->get_occupation_category_name();
				break;
			case 'occupation_detail':
				$name = $position->get_occupation_name();
				break;
			case 'employmenttype':
				$name = $position->get_employment_type_name();
				break;
			case 'keyword':
				$name = $position->get_keywords_type_name();
				break;
		}
		return apply_filters( 'personio_integration_get_taxonomy_from_position', $name, $taxonomy, $position );
	}

	/**
	 * Get the taxonomy name by its simple name.
	 * E.g. from "recruitingCategory" to "personioRecruitingCategory".
	 *
	 * TODO necessary?
	 *
	 * @param string $simple_name The simple name of the requested taxonomy.
	 * @return string
	 */
	public static function get_taxonomy_name_by_simple_name( string $simple_name ): string {
		$taxonomy = '';
		switch ( $simple_name ) {
			case 'recruitingCategory':
				$taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY;
				break;
			case 'schedule':
				$taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE;
				break;
			case 'office':
				$taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE;
				break;
			case 'department':
				$taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT;
				break;
			case 'seniority':
				$taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY;
				break;
			case 'experience':
				$taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE;
				break;
			case 'occupation':
				$taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY;
				break;
			case 'occupation_detail':
				$taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION;
				break;
			case 'employmentTypes':
				$taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE;
				break;
			case 'keyword':
				$taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS;
				break;
		}
		return $taxonomy;
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
		return apply_filters( 'personio_integration_filter_types', $types );
	}

	/**
	 * Return an array with supported languages resorted with the default language as first entry.
	 *
	 * TODO find better way.
	 *
	 * @return array
	 */
	public static function get_active_languages_with_default_first(): array {
		$new_array              = array();
		$lang_key               = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY );
		$new_array[ $lang_key ] = WP_PERSONIO_INTEGRATION_LANGUAGES[ $lang_key ];
		return array_merge( $new_array, WP_PERSONIO_INTEGRATION_LANGUAGES );
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
			|| isset( $_GET['rest_route'] ) // (#2)
				&& str_starts_with( sanitize_text_field( wp_unslash( $_GET['rest_route'] ) ), '/' ) ) {
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
		// pre-filter the given attributes.
		$filtered = apply_filters(
			'personio_integration_get_shortcode_attributes',
			array(
				'defaults'   => $attribute_defaults,
				'settings'   => $attribute_settings,
				'attributes' => $attributes,
			)
		);

		// get pre-filtered array.
		$attribute_defaults = $filtered['defaults'];
		$attribute_settings = $filtered['settings'];
		$attributes         = $filtered['attributes'];

		// concat the lists.
		$attributes = shortcode_atts( $attribute_defaults, $attributes );

		// check if language-setting is valid.
		if ( empty( WP_PERSONIO_INTEGRATION_LANGUAGES[ $attributes['lang'] ] ) ) {
			$attributes['lang'] = WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY;
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
					if ( ! empty( $_GET['personiofilter'][ $name ] ) ) {
						$attributes[ $name ] = absint( wp_unslash( $_GET['personiofilter'][ $name ] ) );
					}
				}
				if ( 'listing_template' === $attribute_settings[ $name ] ) {
					$attributes[ $name ] = $attribute;
					if ( false === Templates::get_instance()->has_template( 'parts/archive/' . $attribute . '.php' ) ) {
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
	 * Return the absolute url to the plugin.
	 *
	 * @return string
	 */
	public static function get_plugin_path(): string {
		return trailingslashit( plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ) );
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
	 * Return the default Wordpress-language depending on our own support.
	 * If language is unknown for our plugin, use english.
	 *
	 * @return string
	 */
	public static function get_wp_lang(): string {
		$wp_lang = substr( get_bloginfo( 'language' ), 0, 2 );

		/**
		 * Consider the main language set in Polylang for the web page.
		 */
		if ( self::is_plugin_active( 'polylang/polylang.php' ) && function_exists( 'pll_default_language' ) ) {
			$wp_lang = pll_default_language();
		}

		/**
		 * Consider the main language set in WPML for the web page.
		 */
		if ( self::is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$wp_lang = apply_filters( 'wpml_default_language', null );
		}

		/**
		 * Get main language set in weglot for the web page.
		 */
		if ( self::is_plugin_active( 'weglot/weglot.php' ) && function_exists( 'weglot_get_service' ) ) {
			try {
				$language_object = weglot_get_service( 'Language_Service_Weglot' )->get_original_language();
				if ( $language_object ) {
					$wp_lang = $language_object->getInternalCode();
				}
			} catch ( Exception $e ) {
				// TODO log errors.
			}
		}

		// if language not set, use default language.
		if ( empty( $wp_lang ) ) {
			$wp_lang = WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY;
		}

		// if language is not known, use default language.
		$languages = Languages::get_instance()->get_languages();
		if ( empty( $languages[ $wp_lang ] ) ) {
			$wp_lang = WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY;
		}
		return $wp_lang;
	}

	/**
	 * Return the current language depending on our own support.
	 * If language is unknown for our plugin, use english.
	 *
	 * @return string
	 */
	public static function get_current_lang(): string {
		$wp_lang = substr( get_bloginfo( 'language' ), 0, 2 );

		/**
		 * Consider the main language set in Polylang for the web page
		 */
		if ( self::is_plugin_active( 'polylang/polylang.php' ) && function_exists( 'pll_current_language' ) ) {
			$wp_lang = pll_current_language();
		}

		/**
		 * Consider the main language set in WPML for the web page
		 */
		if ( self::is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$wp_lang = apply_filters( 'wpml_current_language', null );
		}

		/**
		 * Get current language set in weglot for the web page.
		 */
		if ( self::is_plugin_active( 'weglot/weglot.php' ) && function_exists( 'weglot_get_current_language' ) ) {
			try {
				$wp_lang = weglot_get_current_language();
			} catch ( Exception $e ) {
				// TODO log errors.
			}
		}

		// if language not set, use default language.
		if ( empty( $wp_lang ) ) {
			$wp_lang = WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY;
		}

		// if language is not known, use default language.
		$languages = Languages::get_instance()->get_languages();
		if ( empty( $languages[ $wp_lang ] ) ) {
			$wp_lang = WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY;
		}
		return $wp_lang;
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
	 * Get taxonomy-labels.
	 *
	 * TODO check language switching.
	 *
	 * @param string $taxonomy The requested taxonomy.
	 * @return array
	 */
	public static function get_taxonomy_label( string $taxonomy ): array {
		$locale = get_locale();
		if ( ! is_admin() ) {
			switch_to_locale( self::get_current_lang() );
		}
		$array = array(
			WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY => array(
				'name'          => _x( 'Categories', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Category', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search category', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit category', 'personio-integration-light' ),
				'update_item'   => __( 'Update category', 'personio-integration-light' ),
				'menu_name'     => __( 'Categories', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY => array(
				'name'          => _x( 'Job types', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Job type', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search Job type', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit Job type', 'personio-integration-light' ),
				'update_item'   => __( 'Update Job type', 'personio-integration-light' ),
				'menu_name'     => __( 'Job types', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION => array(
				'name'          => _x( 'Job type details', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Job type detail', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search Job type detail', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit Job type detail', 'personio-integration-light' ),
				'update_item'   => __( 'Update Job type detail', 'personio-integration-light' ),
				'menu_name'     => __( 'Job type details', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE     => array(
				'name'          => _x( 'Locations', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Location', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search location', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit location', 'personio-integration-light' ),
				'update_item'   => __( 'Update location', 'personio-integration-light' ),
				'menu_name'     => __( 'Locations', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT => array(
				'name'          => _x( 'Departments', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Department', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search department', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit department', 'personio-integration-light' ),
				'update_item'   => __( 'Update department', 'personio-integration-light' ),
				'menu_name'     => __( 'Departments', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE => array(
				'name'          => _x( 'Employment types', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Employment type', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search employment type', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit employment type', 'personio-integration-light' ),
				'update_item'   => __( 'Update employment type', 'personio-integration-light' ),
				'menu_name'     => __( 'Employment types', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY  => array(
				'name'          => _x( 'Experiences', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Experience', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search Experience', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit Experience', 'personio-integration-light' ),
				'update_item'   => __( 'Update Experience', 'personio-integration-light' ),
				'menu_name'     => __( 'Experiences', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE   => array(
				'name'          => _x( 'Contract types', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Contract type', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search Contract type', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit Contract type', 'personio-integration-light' ),
				'update_item'   => __( 'Update Contract type', 'personio-integration-light' ),
				'menu_name'     => __( 'Contract types', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE => array(
				'name'          => _x( 'Years of experiences', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Years of experience', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search years of experience', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit years of experience', 'personio-integration-light' ),
				'update_item'   => __( 'Update years of experience', 'personio-integration-light' ),
				'menu_name'     => __( 'Years of experiences', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES  => array(
				'name'          => _x( 'Languages', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Language', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search language', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit language', 'personio-integration-light' ),
				'update_item'   => __( 'Update language', 'personio-integration-light' ),
				'menu_name'     => __( 'Languages', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS   => array(
				'name'          => _x( 'Keywords', 'taxonomy general name', 'personio-integration-light' ),
				'singular_name' => _x( 'Keyword', 'taxonomy singular name', 'personio-integration-light' ),
				'search_items'  => __( 'Search Keywords', 'personio-integration-light' ),
				'edit_item'     => __( 'Edit Keyword', 'personio-integration-light' ),
				'update_item'   => __( 'Update keyword', 'personio-integration-light' ),
				'menu_name'     => __( 'Keywords', 'personio-integration-light' ),
			),
		);
		// revert the locale-setting.
		if ( ! is_admin() ) {
			switch_to_locale( $locale );
		}
		if ( empty( $array[ $taxonomy ] ) ) {
			$array[ $taxonomy ] = array();
		}
		return apply_filters( 'personio_integration_filter_taxonomy_label', $array[ $taxonomy ], $taxonomy );
	}

	/**
	 * Get language-specific defaults for a taxonomy.
	 *
	 * TODO check the language switching.
	 *
	 * @param string $taxonomy The requested taxonomy.
	 * @return array
	 */
	public static function get_taxonomy_defaults( string $taxonomy ): array {
		// set language in frontend to read the texts depending on main-language.
		$locale = get_locale();
		if ( ! is_admin() ) {
			switch_to_locale( self::get_current_lang() );
		}
		$array = array(
			WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY => array(
				'accounting_and_finance'                   => __( 'Accounting/Finance', 'personio-integration-light' ),
				'administrative_and_clerical'              => __( 'Administrative/Clerical', 'personio-integration-light' ),
				'banking_and_real_estate'                  => __( 'Banking/Real Estate/Mortgage Professionals', 'personio-integration-light' ),
				'building_construction_and_skilled_trades' => __( 'Building Construction/Skilled Trades', 'personio-integration-light' ),
				'business_and_strategic_development'       => __( 'Business/Strategic Management', 'personio-integration-light' ),
				'creative_and_design'                      => __( 'Creative/Design', 'personio-integration-light' ),
				'customer_support_and_client_care'         => __( 'Customer Support/Client Care', 'personio-integration-light' ),
				'editorial_and_writing'                    => __( 'Editorial/Writing', 'personio-integration-light' ),
				'engineering'                              => __( 'Engineering', 'personio-integration-light' ),
				'food_services_and_hospitality'            => __( 'Food Services/Hospitality', 'personio-integration-light' ),
				'human_resources'                          => __( 'Human Resources', 'personio-integration-light' ),
				'installation_and_maintenance_repair'      => __( 'Installation/Maintenance/Repair', 'personio-integration-light' ),
				'it_software'                              => __( 'IT/Software Development', 'personio-integration-light' ),
				'legal'                                    => __( 'Legal', 'personio-integration-light' ),
				'logistics_and_transportation'             => __( 'Logistics/Transportation', 'personio-integration-light' ),
				'marketing_and_product'                    => __( 'Marketing/Product', 'personio-integration-light' ),
				'medical_health'                           => __( 'Medical/Health', 'personio-integration-light' ),
				'other'                                    => __( 'Other', 'personio-integration-light' ),
				'production_and_operations'                => __( 'Production/Operations', 'personio-integration-light' ),
				'project_and_program_management'           => __( 'Project/Program Management', 'personio-integration-light' ),
				'quality_assurance_and_saftey'             => __( 'Quality Assurance/Safety', 'personio-integration-light' ),
				'rd_and_science'                           => __( 'R&D/Science', 'personio-integration-light' ),
				'sales_and_business_development'           => __( 'Sales/Business Development', 'personio-integration-light' ),
				'security_and_protective_services'         => __( 'Security/Protective Services', 'personio-integration-light' ),
				'training_instruction'                     => __( 'Training/Instruction', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION => array(
				'acturial_analysis'                        => __( 'Actuarial Analysis', 'personio-integration-light' ),
				'bookkeeping'                              => __( 'Bookkeeping/General Ledger', 'personio-integration-light' ),
				'financial_control'                        => __( 'Financial Control', 'personio-integration-light' ),
				'corporate_finance'                        => __( 'Corporate Finance', 'personio-integration-light' ),
				'accounts_payable_and_receivable'          => __( 'Accounts Payable/Receivable', 'personio-integration-light' ),
				'financial_reporting'                      => __( 'Financial Planning/Advising', 'personio-integration-light' ),
				'financial_analysis'                       => __( 'Financial Analysis/Research/Reporting', 'personio-integration-light' ),
				'corporate_accounting'                     => __( 'Corporate Accounting', 'personio-integration-light' ),
				'fund_accounting'                          => __( 'Fund Accounting', 'personio-integration-light' ),
				'claims_review'                            => __( 'Claims Review and Adjusting', 'personio-integration-light' ),
				'securities_analysis'                      => __( 'Securities Analysis/Research', 'personio-integration-light' ),
				'real_estate_appraisal'                    => __( 'Real Estate Appraisal', 'personio-integration-light' ),
				'real_estate_leasing'                      => __( 'Real Estate Leasing/Acquisition', 'personio-integration-light' ),
				'collections'                              => __( 'Collections', 'personio-integration-light' ),
				'investment_management'                    => __( 'Investment Management', 'personio-integration-light' ),
				'credit_review'                            => __( 'Credit Review/Analysis', 'personio-integration-light' ),
				'risk_management'                          => __( 'Risk Management/Compliance', 'personio-integration-light' ),
				'tax_assessment'                           => __( 'Tax Assessment and Collections', 'personio-integration-light' ),
				'tax_accounting'                           => __( 'Tax Accounting', 'personio-integration-light' ),
				'policy_underwriting'                      => __( 'Policy Underwriting', 'personio-integration-light' ),
				'financial_products_sales'                 => __( 'Financial Products Sales/Brokerage', 'personio-integration-light' ),
				'audit'                                    => __( 'Audit', 'personio-integration-light' ),
				'general_other_accounting_finance'         => __( 'General/Other: Accounting/Finance', 'personio-integration-light' ),
				'records_management'                       => __( 'Filing/Records Management', 'personio-integration-light' ),
				'executive_support'                        => __( 'Executive Support', 'personio-integration-light' ),
				'data_entry'                               => __( 'Data Entry/Order Processing', 'personio-integration-light' ),
				'reception'                                => __( 'Front Desk/Reception', 'personio-integration-light' ),
				'property_management'                      => __( 'Property Management', 'personio-integration-light' ),
				'office_management'                        => __( 'Office Management', 'personio-integration-light' ),
				'administrative'                           => __( 'Administrative Support', 'personio-integration-light' ),
				'claims_processing'                        => __( 'Claims Processing', 'personio-integration-light' ),
				'transaction'                              => __( 'Transcription', 'personio-integration-light' ),
				'secretary'                                => __( 'Paralegal & Legal Secretary', 'personio-integration-light' ),
				'general_other_administrative_clerical'    => __( 'General/Other: Administrative/Clerical', 'personio-integration-light' ),
				'loan_officer_and_originator'              => __( 'Loan Officer/Originator', 'personio-integration-light' ),
				'escrow_officer_and_manager'               => __( 'Escrow Officer/Manager', 'personio-integration-light' ),
				'store_and_branch_management'              => __( 'Store/Branch Management', 'personio-integration-light' ),
				'mortgage_broker'                          => __( 'Mortgage Broker', 'personio-integration-light' ),
				'real_estate_agent_and_broker'             => __( 'Real Estate Agent/Broker', 'personio-integration-light' ),
				'real_estate_law'                          => __( 'Real Estate Law', 'personio-integration-light' ),
				'credit_manager'                           => __( 'Credit Manager', 'personio-integration-light' ),
				'bank_teller'                              => __( 'Bank Teller', 'personio-integration-light' ),
				'underwriter'                              => __( 'Underwriter', 'personio-integration-light' ),
				'title_officer_and_closer'                 => __( 'Title Officer/Closer', 'personio-integration-light' ),
				'site_superintendent'                      => __( 'Site Superintendent', 'personio-integration-light' ),
				'concrete_and_masonry'                     => __( 'Concrete and Masonry', 'personio-integration-light' ),
				'heavy_equipment_operation'                => __( 'Heavy Equipment Operation', 'personio-integration-light' ),
				'roofing'                                  => __( 'Roofing', 'personio-integration-light' ),
				'electrician'                              => __( 'Electrician', 'personio-integration-light' ),
				'flooring_and_tiling_and_painting_and_wallpapering' => __( 'Flooring/Tiling/Painting/Wallpapering', 'personio-integration-light' ),
				'hvac'                                     => __( 'HVAC', 'personio-integration-light' ),
				'plumbing_and_pipefitting'                 => __( 'Plumbing/Pipefitting', 'personio-integration-light' ),
				'ironwork_and_metal_fabrication'           => __( 'Ironwork/Metal Fabrication', 'personio-integration-light' ),
				'cad_and_drafting'                         => __( 'CAD/Drafting', 'personio-integration-light' ),
				'surveying'                                => __( 'Surveying', 'personio-integration-light' ),
				'sheetrock_and_plastering'                 => __( 'Sheetrock/Plastering', 'personio-integration-light' ),
				'carpentry_and_framing'                    => __( 'Carpentry/Framing', 'personio-integration-light' ),
				'general_and_other_building_construction_and_skilled_trades' => __( 'General/Other: Construction/Skilled Trades', 'personio-integration-light' ),
				'business_analysis_and_research'           => __( 'Business Analysis/Research', 'personio-integration-light' ),
				'managerial_consulting'                    => __( 'Managerial Consulting', 'personio-integration-light' ),
				'franchise_business_ownership'             => __( 'Franchise-Business Ownership', 'personio-integration-light' ),
				'business_unit_management'                 => __( 'Business Unit Management', 'personio-integration-light' ),
				'president_and_top_executive'              => __( 'President/Top Executive', 'personio-integration-light' ),
				'public_health_administration'             => __( 'Public Health Administration', 'personio-integration-light' ),
				'hotel_and_lodging_management'             => __( 'Hotel/Lodging Management', 'personio-integration-light' ),
				'hospital_and_clinic_administration'       => __( 'Hospital/Clinic Administration', 'personio-integration-light' ),
				'mergers_and_acquisitions'                 => __( 'Mergers and Acquisitions', 'personio-integration-light' ),
				'restaurant_management'                    => __( 'Restaurant Management', 'personio-integration-light' ),
				'school_and_college_administration'        => __( 'School/College Administration', 'personio-integration-light' ),
				'town_and_city_planning'                   => __( 'Town/City Planning', 'personio-integration-light' ),
				'strategic_planning_and_intelligence'      => __( 'Strategic Planning/Intelligence', 'personio-integration-light' ),
				'general_and_other_business_and_strategic_management' => __( 'General/Other: Business/Strategic Management', 'personio-integration-light' ),
				'architecture_and_interior_design'         => __( 'Architecture/Interior Design', 'personio-integration-light' ),
				'computer_animation_multimedia'            => __( 'Computer Animation & Multimedia', 'personio-integration-light' ),
				'creative_direction_and_lead'              => __( 'Creative Direction/Lead', 'personio-integration-light' ),
				'graphic_arts_and_illustration'            => __( 'Graphic Arts/Illustration', 'personio-integration-light' ),
				'industrial_design'                        => __( 'Industrial Design', 'personio-integration-light' ),
				'fashion_accessories_design'               => __( 'Fashion & Accessories Design', 'personio-integration-light' ),
				'photography_and_videography'              => __( 'Photography and Videography', 'personio-integration-light' ),
				'web_and_ui_and_ux_design'                 => __( 'Web/UI/UX Design', 'personio-integration-light' ),
				'advertising_writing_creative'             => __( 'Advertising Writing (Creative)', 'personio-integration-light' ),
				'general_and_other_creative_and_design'    => __( 'General/Other: Creative/Design', 'personio-integration-light' ),
				'call_center'                              => __( 'Call Center', 'personio-integration-light' ),
				'flight_attendant'                         => __( 'Flight Attendant', 'personio-integration-light' ),
				'hair_cutting_and_styling'                 => __( 'Hair Cutting/Styling', 'personio-integration-light' ),
				'retail_customer_service'                  => __( 'Retail Customer Service', 'personio-integration-light' ),
				'account_management_non_commissioned'      => __( 'Account Management (Non-Commissioned)', 'personio-integration-light' ),
				'customer_training'                        => __( 'Customer Training', 'personio-integration-light' ),
				'reservations_and_ticketing'               => __( 'Reservations/Ticketing', 'personio-integration-light' ),
				'general_and_other_customer_support_and_client_care' => __( 'General/Other: Customer Support/Client Care', 'personio-integration-light' ),
				'technical_customer_service'               => __( 'Technical Customer Service', 'personio-integration-light' ),
				'documentation_and_technical_writing'      => __( 'Documentation/Technical Writing', 'personio-integration-light' ),
				'journalism'                               => __( 'Journalism', 'personio-integration-light' ),
				'digital_content_development'              => __( 'Digital Content Development', 'personio-integration-light' ),
				'editing_proofreading'                     => __( 'Editing & Proofreading', 'personio-integration-light' ),
				'general_and_other_editorial_and_writing'  => __( 'General/Other: Editorial/Writing', 'personio-integration-light' ),
				'translation_and_interpretation'           => __( 'Translation/Interpretation', 'personio-integration-light' ),
				'civil__structural_engineering'            => __( 'Civil & Structural Engineering', 'personio-integration-light' ),
				'bio_engineering'                          => __( 'Bio-Engineering', 'personio-integration-light' ),
				'chemical_engineering'                     => __( 'Chemical Engineering', 'personio-integration-light' ),
				'electrical_and_electronics_engineering'   => __( 'Electrical/Electronics Engineering', 'personio-integration-light' ),
				'energy_and_nuclear_engineering'           => __( 'Energy/Nuclear Engineering', 'personio-integration-light' ),
				'rf_and_wireless_engineering'              => __( 'RF/Wireless Engineering', 'personio-integration-light' ),
				'aeronautic_and_avionic_engineering'       => __( 'Aeronautic/Avionic Engineering', 'personio-integration-light' ),
				'mechanical_engineering'                   => __( 'Mechanical Engineering', 'personio-integration-light' ),
				'systems_and_process_engineering'          => __( 'Systems/Process Engineering', 'personio-integration-light' ),
				'industrial_and_manufacturing_engineering' => __( 'Industrial/Manufacturing Engineering', 'personio-integration-light' ),
				'naval_architecture_and_marine_engineering' => __( 'Naval Architecture/Marine Engineering', 'personio-integration-light' ),
				'environmental_and_geological_engineering' => __( 'Environmental and Geological Engineering', 'personio-integration-light' ),
				'general_and_other_engineering'            => __( 'General/Other: Engineering', 'personio-integration-light' ),
				'food_beverage_serving'                    => __( 'Food & Beverage Serving', 'personio-integration-light' ),
				'host_and_hostess'                         => __( 'Host/Hostess', 'personio-integration-light' ),
				'guest_services_and_concierge'             => __( 'Guest Services/Concierge', 'personio-integration-light' ),
				'food_preparation_and_cooking'             => __( 'Food Preparation/Cooking', 'personio-integration-light' ),
				'guide_tour'                               => __( 'Guide (Tour)', 'personio-integration-light' ),
				'front_desk_and_reception'                 => __( 'Front Desk/Reception', 'personio-integration-light' ),
				'wine_steward_sommelier'                   => __( 'Wine Steward (Sommelier)', 'personio-integration-light' ),
				'general_and_other_food_services_and_hospitality' => __( 'General/Other: Food Services', 'personio-integration-light' ),
				'corporate_development_and_training'       => __( 'Corporate Development and Training', 'personio-integration-light' ),
				'compensation_and_benefits_policy'         => __( 'Compensation/Benefits Policy', 'personio-integration-light' ),
				'diversity_management_and_eeo_and_compliance' => __( 'Diversity Management/EEO/Compliance', 'personio-integration-light' ),
				'academic_admissions_and_advising'         => __( 'Academic Admissions and Advising', 'personio-integration-light' ),
				'payroll_and_benefits_administration'      => __( 'Payroll and Benefits Administration', 'personio-integration-light' ),
				'recruiting_and_sourcing'                  => __( 'Recruiting/Sourcing', 'personio-integration-light' ),
				'hr_systems_administration'                => __( 'HR Systems Administration', 'personio-integration-light' ),
				'general_and_other_human_resources'        => __( 'General/Other: Human Resources', 'personio-integration-light' ),
				'computer_and_electronics_and_telecomm_install_and_maintain_and_repair' => __( 'Computer/Electronics/Telecomm Install/Maintain/Repair', 'personio-integration-light' ),
				'oil_rig_pipeline_install_and_maintain_and_repair' => __( 'Oil Rig & Pipeline Install/Maintain/Repair', 'personio-integration-light' ),
				'facilities_maintenance'                   => __( 'Facilities Maintenance', 'personio-integration-light' ),
				'janitorial_cleaning'                      => __( 'Janitorial & Cleaning', 'personio-integration-light' ),
				'vehicle_repair_and_maintenance'           => __( 'Vehicle Repair and Maintenance', 'personio-integration-light' ),
				'wire_and_cable_install_and_maintain_and_repair' => __( 'Wire and Cable Install/Maintain/Repair', 'personio-integration-light' ),
				'landscaping'                              => __( 'Landscaping', 'personio-integration-light' ),
				'equipment_install_and_maintain_and_repair' => __( 'Equipment Install/Maintain/Repair', 'personio-integration-light' ),
				'locksmith'                                => __( 'Locksmith', 'personio-integration-light' ),
				'general_and_other_installation_and_maintenance_and_repair' => __( 'General/Other: Installation/Maintenance/Repair', 'personio-integration-light' ),
				'usability_and_information_architecture'   => __( 'Usability/Information Architecture', 'personio-integration-light' ),
				'desktop_service_and_support'              => __( 'Desktop Service and Support', 'personio-integration-light' ),
				'computer_and_network_security'            => __( 'Computer/Network Security', 'personio-integration-light' ),
				'database_development_and_administration'  => __( 'Database Development/Administration', 'personio-integration-light' ),
				'enterprise_software_implementation_consulting' => __( 'Enterprise Software Implementation & Consulting', 'personio-integration-light' ),
				'it_project_management'                    => __( 'IT Project Management', 'personio-integration-light' ),
				'software_and_system_architecture'         => __( 'Software/System Architecture', 'personio-integration-light' ),
				'software_and_web_development'             => __( 'Software/Web Development', 'personio-integration-light' ),
				'network_and_server_administration'        => __( 'Network and Server Administration', 'personio-integration-light' ),
				'systems_analysis__it'                     => __( 'Systems Analysis - IT', 'personio-integration-light' ),
				'telecommunications_administration_and_management' => __( 'Telecommunications Administration/Management', 'personio-integration-light' ),
				'general_and_other_it_software'            => __( 'General/Other: IT/Software Development', 'personio-integration-light' ),
				'labor__employment_law'                    => __( 'Labor & Employment Law', 'personio-integration-light' ),
				'patent_and_ip_law'                        => __( 'Patent/IP Law', 'personio-integration-light' ),
				'regulatory_and_compliance_law'            => __( 'Regulatory/Compliance Law', 'personio-integration-light' ),
				'tax_law'                                  => __( 'Tax Law', 'personio-integration-light' ),
				'attorney'                                 => __( 'Attorney', 'personio-integration-light' ),
				'contracts_administration'                 => __( 'Contracts Administration', 'personio-integration-light' ),
				'paralegal__legal_secretary'               => __( 'Paralegal & Legal Secretary', 'personio-integration-light' ),
				'general_and_other_legal'                  => __( 'General/Other: Legal', 'personio-integration-light' ),
				'car_van_and_bus_driving'                  => __( 'Car, Van and Bus Driving', 'personio-integration-light' ),
				'train_or_rail_operator'                   => __( 'Train or Rail Operator', 'personio-integration-light' ),
				'purchasing_goods_and_services'            => __( 'Purchasing Goods and Services', 'personio-integration-light' ),
				'piloting_air_and_marine'                  => __( 'Piloting: Air and Marine', 'personio-integration-light' ),
				'cargo_and_baggage_handling'               => __( 'Cargo and Baggage Handling', 'personio-integration-light' ),
				'hazardous_materials_handling'             => __( 'Hazardous Materials Handling', 'personio-integration-light' ),
				'merchandise_planning_and_buying'          => __( 'Merchandise Planning and Buying', 'personio-integration-light' ),
				'import_and_export_administration'         => __( 'Import/Export Administration', 'personio-integration-light' ),
				'cost_estimating'                          => __( 'Cost Estimating', 'personio-integration-light' ),
				'messenger_and_courier'                    => __( 'Messenger/Courier', 'personio-integration-light' ),
				'truck_driving'                            => __( 'Truck Driving', 'personio-integration-light' ),
				'supplier_management_and_vendor_management' => __( 'Supplier Management/Vendor Management', 'personio-integration-light' ),
				'equipment_and_forklift_and_crane_operation' => __( 'Equipment/Forklift/Crane Operation', 'personio-integration-light' ),
				'inventory_planning_and_management'        => __( 'Inventory Planning and Management', 'personio-integration-light' ),
				'vehicle_dispatch_routing_and_scheduling'  => __( 'Vehicle Dispatch, Routing and Scheduling', 'personio-integration-light' ),
				'shipping_and_receiving_and_warehousing'   => __( 'Shipping and Receiving/Warehousing', 'personio-integration-light' ),
				'general_and_other_logistics_and_transportation' => __( 'General/Other: Logistics/Transportation', 'personio-integration-light' ),
				'visual_and_display_merchandising'         => __( 'Visual/Display Merchandising', 'personio-integration-light' ),
				'brand_and_product_marketing'              => __( 'Brand/Product Marketing', 'personio-integration-light' ),
				'direct_marketing_crm'                     => __( 'Direct Marketing (CRM)', 'personio-integration-light' ),
				'events_and_promotional_marketing'         => __( 'Events/Promotional Marketing', 'personio-integration-light' ),
				'investor_and_public_and_media_relations'  => __( 'Investor and Public/Media Relations', 'personio-integration-light' ),
				'marketing_communications'                 => __( 'Marketing Communications', 'personio-integration-light' ),
				'market_research'                          => __( 'Market Research', 'personio-integration-light' ),
				'media_planning_and_buying'                => __( 'Media Planning and Buying', 'personio-integration-light' ),
				'marketing_production_and_traffic'         => __( 'Marketing Production/Traffic', 'personio-integration-light' ),
				'product_management'                       => __( 'Product Management', 'personio-integration-light' ),
				'telemarketing'                            => __( 'Telemarketing', 'personio-integration-light' ),
				'copy_writing_and_editing'                 => __( 'Copy Writing/Editing', 'personio-integration-light' ),
				'general_and_other_marketing_and_product'  => __( 'General/Other: Marketing/Product', 'personio-integration-light' ),
				'healthcare_aid'                           => __( 'Healthcare Aid', 'personio-integration-light' ),
				'pharmacy'                                 => __( 'Pharmacy', 'personio-integration-light' ),
				'nutrition_and_diet'                       => __( 'Nutrition and Diet', 'personio-integration-light' ),
				'nursing'                                  => __( 'Nursing', 'personio-integration-light' ),
				'laboratory_and_pathology'                 => __( 'Laboratory/Pathology', 'personio-integration-light' ),
				'physician_assistant_and_nurse_practitioner' => __( 'Physician Assistant/Nurse Practitioner', 'personio-integration-light' ),
				'optical'                                  => __( 'Optical', 'personio-integration-light' ),
				'medical_therapy_and_rehab_services'       => __( 'Medical Therapy/Rehab Services', 'personio-integration-light' ),
				'medical_practitioner'                     => __( 'Medical Practitioner', 'personio-integration-light' ),
				'mental_health'                            => __( 'Mental Health', 'personio-integration-light' ),
				'medical_imaging'                          => __( 'Medical Imaging', 'personio-integration-light' ),
				'emt_and_paramedic'                        => __( 'EMT/Paramedic', 'personio-integration-light' ),
				'social_service'                           => __( 'Social Service', 'personio-integration-light' ),
				'sports_medicine'                          => __( 'Sports Medicine', 'personio-integration-light' ),
				'veterinary_and_animal_care'               => __( 'Veterinary/Animal Care', 'personio-integration-light' ),
				'dental_assistant_and_hygienist'           => __( 'Dental Assistant/Hygienist', 'personio-integration-light' ),
				'dental_practitioner'                      => __( 'Dental Practitioner', 'personio-integration-light' ),
				'general_and_other_medical_and_health'     => __( 'General/Other: Medical/Health', 'personio-integration-light' ),
				'work_at_home'                             => __( 'Work at Home', 'personio-integration-light' ),
				'career_fair'                              => __( 'Career Fair', 'personio-integration-light' ),
				'other'                                    => __( 'Other', 'personio-integration-light' ),
				'waste_pick_up_and_removal'                => __( 'Waste Pick-up and Removal', 'personio-integration-light' ),
				'operations_and_plant_management'          => __( 'Operations/Plant Management', 'personio-integration-light' ),
				'equipment_operations'                     => __( 'Equipment Operations', 'personio-integration-light' ),
				'scientific_and_technical_production'      => __( 'Scientific/Technical Production', 'personio-integration-light' ),
				'layout_prepress_printing_binding_operations' => __( 'Layout, Prepress, Printing, & Binding Operations', 'personio-integration-light' ),
				'assembly_and_assembly_line'               => __( 'Assembly/Assembly Line', 'personio-integration-light' ),
				'moldmaking_and_casting'                   => __( 'Moldmaking/Casting', 'personio-integration-light' ),
				'metal_fabrication_and_welding'            => __( 'Metal Fabrication and Welding', 'personio-integration-light' ),
				'audio_and_video_broadcast_postproduction' => __( 'Audio/Video Broadcast & Postproduction', 'personio-integration-light' ),
				'sewing_and_tailoring'                     => __( 'Sewing and Tailoring', 'personio-integration-light' ),
				'laundry_and_dry_cleaning_operations'      => __( 'Laundry and Dry-Cleaning Operations', 'personio-integration-light' ),
				'machining_and_cnc'                        => __( 'Machining/CNC', 'personio-integration-light' ),
				'general_and_other_production_and_operations' => __( 'General/Other: Production/Operations', 'personio-integration-light' ),
				'event_planning_and_coordination'          => __( 'Event Planning/Coordination', 'personio-integration-light' ),
				'program_management'                       => __( 'General/Other: Project/Program Management', 'personio-integration-light' ),
				'project_management'                       => __( 'IT Project Management', 'personio-integration-light' ),
				'general_and_other_project_and_program_management' => __( 'General/Other: Project/Program Management', 'personio-integration-light' ),
				'occupational_health_and_safety'           => __( 'Occupational Health and Safety', 'personio-integration-light' ),
				'building_and_construction_inspection'     => __( 'Building/Construction Inspection', 'personio-integration-light' ),
				'fraud_investigation'                      => __( 'Fraud Investigation', 'personio-integration-light' ),
				'iso_certification'                        => __( 'ISO Certification', 'personio-integration-light' ),
				'food_safety_and_inspection'               => __( 'Food Safety and Inspection', 'personio-integration-light' ),
				'production_quality_assurance'             => __( 'Production Quality Assurance', 'personio-integration-light' ),
				'six_sigma_and_black_belt_and_tqm'         => __( 'Six Sigma/Black Belt/TQM', 'personio-integration-light' ),
				'software_quality_assurance'               => __( 'Software Quality Assurance', 'personio-integration-light' ),
				'vehicle_inspection'                       => __( 'Vehicle Inspection', 'personio-integration-light' ),
				'environmental_protection_and_conservation' => __( 'Environmental Protection/Conservation', 'personio-integration-light' ),
				'general_and_other_quality_assurance_and_safety' => __( 'General/Other: Quality Assurance/Safety', 'personio-integration-light' ),
				'biological_and_chemical_research'         => __( 'Biological/Chemical Research', 'personio-integration-light' ),
				'materials_and_physical_research'          => __( 'Materials/Physical Research', 'personio-integration-light' ),
				'mathematical_and_statistical_research'    => __( 'Mathematical/Statistical Research', 'personio-integration-light' ),
				'clinical_research'                        => __( 'Clinical Research', 'personio-integration-light' ),
				'new_product_rd'                           => __( 'New Product R&D', 'personio-integration-light' ),
				'pharmaceutical_research'                  => __( 'Pharmaceutical Research', 'personio-integration-light' ),
				'environmental_and_geological_testing_analysis' => __( 'Environmental/Geological Testing & Analysis', 'personio-integration-light' ),
				'general_and_other_r_and_d_and_science'    => __( 'General/Other: R&D/Science', 'personio-integration-light' ),
				'account_management_commissioned'          => __( 'Account Management (Commissioned)', 'personio-integration-light' ),
				'field_sales'                              => __( 'Field Sales', 'personio-integration-light' ),
				'business_development_and_new_accounts'    => __( 'Business Development/New Accounts', 'personio-integration-light' ),
				'retail_and_counter_sales_and_cashier'     => __( 'Retail/Counter Sales and Cashier', 'personio-integration-light' ),
				'wholesale_and_reselling_sales'            => __( 'Wholesale/Reselling Sales', 'personio-integration-light' ),
				'international_sales'                      => __( 'International Sales', 'personio-integration-light' ),
				'fundraising'                              => __( 'Fundraising', 'personio-integration-light' ),
				'technical_presales_support__technical_sales' => __( 'Technical Presales Support & Technical Sales', 'personio-integration-light' ),
				'telesales'                                => __( 'Telesales', 'personio-integration-light' ),
				'travel_agent_and_ticket_sales'            => __( 'Travel Agent/Ticket Sales', 'personio-integration-light' ),
				'media_and_advertising_sales'              => __( 'Media and Advertising Sales', 'personio-integration-light' ),
				'insurance_agent_and_broker'               => __( 'Insurance Agent/Broker', 'personio-integration-light' ),
				'sales_support_and_assistance'             => __( 'Sales Support/Assistance', 'personio-integration-light' ),
				'financial_products_sales_and_brokerage'   => __( 'Financial Products Sales/Brokerage', 'personio-integration-light' ),
				'general_and_other_sales_and_business_development' => __( 'General/Other: Sales/Business Development', 'personio-integration-light' ),
				'customs_and_immigration'                  => __( 'Customs/Immigration', 'personio-integration-light' ),
				'firefighting_and_rescue'                  => __( 'Firefighting and Rescue', 'personio-integration-light' ),
				'airport_security_and_screening'           => __( 'Airport Security and Screening', 'personio-integration-light' ),
				'store_security_and_loss_prevention'       => __( 'Store Security/Loss Prevention', 'personio-integration-light' ),
				'security_intelligence_analysis'           => __( 'Security Intelligence & Analysis', 'personio-integration-light' ),
				'police_law_enforcement'                   => __( 'Police-Law Enforcement', 'personio-integration-light' ),
				'security_guard'                           => __( 'Security Guard', 'personio-integration-light' ),
				'correctional_officer'                     => __( 'Correctional Officer', 'personio-integration-light' ),
				'military_combat'                          => __( 'Military Combat', 'personio-integration-light' ),
				'general_and_other_security_and_protective_services' => __( 'General/Other: Security/Protective Services', 'personio-integration-light' ),
				'corporate_development'                    => __( 'Corporate Development and Training', 'personio-integration-light' ),
				'continuing_and_adult'                     => __( 'Continuing/Adult', 'personio-integration-light' ),
				'elementary_school'                        => __( 'Elementary School', 'personio-integration-light' ),
				'software_and_web_training'                => __( 'Software/Web Training', 'personio-integration-light' ),
				'early_childhood_care'                     => __( 'Early Childhood Care & Development', 'personio-integration-light' ),
				'university'                               => __( 'University', 'personio-integration-light' ),
				'junior_and_high_school'                   => __( 'Junior/High School', 'personio-integration-light' ),
				'classroom_teaching'                       => __( 'Classroom Teaching', 'personio-integration-light' ),
				'special_education'                        => __( 'Special Education', 'personio-integration-light' ),
				'fitness_and_sports'                       => __( 'Fitness & Sports Training/Instruction', 'personio-integration-light' ),
				'general_other_training_and_instruction'   => __( 'General/Other: Training/Instruction', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE => array(
				'permanent'       => __( 'Permanent employee', 'personio-integration-light' ),
				'intern'          => __( 'Intern', 'personio-integration-light' ),
				'trainee'         => __( 'Trainee', 'personio-integration-light' ),
				'freelance'       => __( 'Freelance', 'personio-integration-light' ),
				'temporary'       => __( 'Temporary', 'personio-integration-light' ),
				'working_student' => __( 'Working Student', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE   => array(
				'full-time'         => __( 'full-time', 'personio-integration-light' ),
				'part-time'         => __( 'part-time', 'personio-integration-light' ),
				'full-or-part-time' => __( 'full- or part-time', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY  => array(
				'entry-level' => __( 'entry-level', 'personio-integration-light' ),
				'experienced' => __( 'experienced', 'personio-integration-light' ),
				'executive'   => __( 'executive', 'personio-integration-light' ),
				'student'     => __( 'student', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE => array(
				'lt-1'  => __( 'less than 1 year', 'personio-integration-light' ),
				'1-2'   => __( '1-2 years', 'personio-integration-light' ),
				'2-5'   => __( '2-5 years', 'personio-integration-light' ),
				'5-7'   => __( '5-7 years', 'personio-integration-light' ),
				'7-10'  => __( '7-10 years', 'personio-integration-light' ),
				'10-15' => __( '10-15 years', 'personio-integration-light' ),
				'ht15'  => __( 'more than 15 years', 'personio-integration-light' ),
			),
			WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES  => Languages::get_instance()->get_languages(),
		);
		// revert the locale-setting.
		if ( ! is_admin() ) {
			switch_to_locale( $locale );
		}
		if ( empty( $array[ $taxonomy ] ) ) {
			return array();
		}
		return $array[ $taxonomy ];
	}

	/**
	 * Check if this message has been dismissed.
	 *
	 * TODO replace with transients-object.
	 *
	 * @param string $transient The transient.
	 *
	 * @return bool
	 */
	public static function is_transient_not_dismissed( string $transient ): bool {
		$db_record = self::get_admin_transient_cache( $transient );

		if ( 'forever' === $db_record ) {
			return false;
		} elseif ( absint( $db_record ) >= time() ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Get the transient dismissed time.
	 *
	 * TODO replace with transients-object.
	 *
	 * @param string $transient The transient.
	 * @return false|int
	 */
	private static function get_admin_transient_cache( string $transient ): false|int {
		if ( ! $transient ) {
			return false;
		}
		$cache_key = 'pi-dismissed-' . md5( $transient );
		$timeout   = get_option( $cache_key );
		$timeout   = 'forever' === $timeout ? time() + 60 : absint( $timeout );

		if ( empty( $timeout ) || time() > $timeout ) {
			return false;
		}

		return $timeout;
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
	 * Get generated Personio-application-URL.
	 *
	 * TODO find better way.
	 *
	 * @param Position $position The Position-object.
	 * @param bool     $without_application Whether the link should be generated with form-#hashtag.
	 * @return string
	 */
	public static function get_personio_application_url( $position, bool $without_application = false ): string {
		if ( $without_application ) {
			return get_option( 'personioIntegrationUrl', '' ) . '/job/' . absint( $position->get_personio_id() ) . '?display=' . get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY );
		}
		return get_option( 'personioIntegrationUrl', '' ) . '/job/' . absint( $position->get_personio_id() ) . '?display=' . get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY ) . '#apply';
	}

	/**
	 * Get current url for frontend.
	 *
	 * @return string
	 */
	public static function get_current_url(): string {
		// set page url.
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
	 * Regex to get html tag attribute value
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
	 * TODO replace with WP_Filesystem?
	 *
	 * @param string $path The path.
	 * @param array  $file_list The list of files.
	 * @return array
	 */
	public static function get_file_from_directory( string $path = '.', array $file_list = array() ): array {
		$ignore = array( '.', '..' );
		$dh     = opendir( $path );
		while ( false !== ( $file = readdir( $dh ) ) ) {
			if ( ! in_array( $file, $ignore, true ) ) {
				$filepath = $path . '/' . $file;
				if ( is_dir( $filepath ) ) {
					$file_list = self::get_file_from_directory( $filepath, $file_list );
				} else {
					$file_list[ $file ] = $filepath;
				}
			}
		}
		closedir( $dh );
		return $file_list;
	}

	/**
	 * Return the Personio-XML-URL without any parameter.
	 *
	 * TODO find better way.
	 *
	 * @param string $domain The domain.
	 * @return string
	 */
	public static function get_personio_xml_url( string $domain ): string {
		if ( empty( $domain ) ) {
			return '';
		}
		return $domain . '/xml';
	}

	/**
	 * Get language-specific personio account login url.
	 *
	 * TODO find better way.
	 *
	 * @return string
	 */
	public static function get_personio_login_url(): string {
		if ( 'de' === self::get_current_lang() ) {
			return 'https://www.personio.de/login/';
		}
		return 'https://www.personio.com/login/';
	}

	/**
	 * Get language-specific personio account support url.
	 *
	 * @return string
	 */
	public static function get_personio_support_url(): string {
		if ( 'de' === self::get_current_lang() ) {
			return 'https://support.personio.de/';
		}
		return 'https://support.personio.de/hc/en-us/';
	}

	/**
	 * Set the import schedule.
	 *
	 * @return void
	 */
	public static function set_import_schedule(): void {
		if ( ! wp_next_scheduled( 'personio_integration_schudule_events' ) ) {
			wp_schedule_event( time(), get_option( 'personioIntegrationPositionScheduleInterval' ), 'personio_integration_schudule_events' );
		}
	}

	/**
	 * Return the configured personio-URL.
	 *
	 * @return string
	 */
	public static function get_personio_url(): string {
		return get_option( 'personioIntegrationUrl', '' );
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
}

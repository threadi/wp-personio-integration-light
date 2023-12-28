<?php
/**
 * File for handling frontend tasks only.
 *
 * @package personio-integration-light
 */

use App\helper;
use personioIntegration\Position;
use personioIntegration\Positions;

/**
 * Change output of post_content for the custom post type of this plugin.
 *
 * @param string $content The content to output.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_content_output( string $content ): string {
	// change the output for our own cpt.
	if ( ! helper::is_admin_api_request() && ! is_admin()
		&& is_single() && WP_PERSONIO_INTEGRATION_CPT === get_post_type( get_the_ID() ) && apply_filters( 'personio_integration_show_content', true ) ) {

		// set attributes for single output.
		$attributes = array(
			'personioid' => get_post_meta( get_the_ID(), WP_PERSONIO_INTEGRATION_CPT_PM_PID, true ),
		);

		// return the output of shortcode-function.
		return personio_integration_position_shortcode( $attributes );
	}

	// return results.
	return $content;
}
add_filter( 'the_content', 'personio_integration_content_output', 5 );

/**
 * Change output of detail in archive-pages for the custom post type of this plugin.
 *
 * @param string $excerpt The excerpt to output.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_excerpt_output( string $excerpt ): string {
	if ( ! helper::is_admin_api_request()
		&& ! is_single()
		&& WP_PERSONIO_INTEGRATION_CPT === get_post_type( get_the_ID() )
	) {
		// set attributes for single output.
		$attributes = array(
			'personioId' => get_post_meta( get_the_ID(), WP_PERSONIO_INTEGRATION_CPT_PM_PID, true ),
			'templates'  => 'excerpt',
		);

		// return the output of shortcode-function.
		return personio_integration_position_shortcode( $attributes );
	}
	return $excerpt;
}
add_filter( 'the_excerpt', 'personio_integration_excerpt_output' );
add_filter( 'get_the_excerpt', 'personio_integration_excerpt_output' );

/**
 * Get position title for list.
 *
 * @param Position $position The position as object.
 * @param array    $attributes The attributes.
 * @return void
 * @noinspection PhpUnused
 * @noinspection DuplicatedCode
 */
function personio_integration_get_title( Position $position, array $attributes ): void {
	// set the header-size (h1 for single, h2 for list).
	$heading_size = '2';
	if ( ! did_action( 'elementor/loaded' ) && is_single() ) {
		$heading_size = '1';
	}
	// and h3 if list is grouped.
	if ( ! empty( $attributes['groupby'] ) ) {
		$heading_size = '3';
	}

	if ( false !== $attributes['donotlink'] ) {
		?>
		<header class="entry-content default-max-width">
			<h<?php echo absint( $heading_size ); ?> class="entry-title"><?php echo esc_html( $position->getTitle() ); ?></h<?php echo absint( $heading_size ); ?>>
		</header>
		<?php
	} else {
		?>
		<header class="entry-content default-max-width">
			<h<?php echo absint( $heading_size ); ?> class="entry-title"><a href="<?php echo esc_url( get_permalink( $position->ID ) ); ?>"><?php echo esc_html( $position->getTitle() ); ?></a></h<?php echo absint( $heading_size ); ?>>
		</header>
		<?php
	}
}
add_action( 'personio_integration_get_title', 'personio_integration_get_title', 10, 2 );

/**
 * Get position excerpts for list.
 *
 * @param Position $position The position as object.
 * @param array    $attributes The attributes.
 * @return void
 */
function personio_integration_get_excerpt( Position $position, array $attributes ): void {
	$excerpt   = '';
	$separator = get_option( 'personioIntegrationTemplateExcerptSeparator', ', ' ) . ' ';
	if ( empty( $attributes['excerpt'] ) ) {
		$attributes['excerpt'] = $attributes;
	}
	if ( ! empty( $attributes['excerpt'] ) ) {
		foreach ( $attributes['excerpt'] as $excerpt_template ) {
			$string_to_add = helper::get_taxonomy_name_of_position( $excerpt_template, $position );
			if ( strlen( $excerpt ) > 0 && strlen( $string_to_add ) > 0 ) {
				$excerpt .= $separator;
			}
			$excerpt .= $string_to_add;
		}
	}
	if ( ! empty( $excerpt ) ) {
		?>
		<div class="entry-content">
			<p><?php echo esc_html( $excerpt ); ?></p>
		</div>
		<?php
	}
}
add_action( 'personio_integration_get_excerpt', 'personio_integration_get_excerpt', 10, 2 );

/**
 * Get position content for list.
 *
 * @param Position $position   The position as object.
 * @param array    $attributes The attributes.
 *
 * @return void
 */
function personio_integration_get_content( Position $position, array $attributes ): void {
	if ( ! empty( $position->getContentAsArray() ) ) {
		?>
		<div class="entry-content">
			<?php
				echo wp_kses_post( $position->get_content( ( isset( $attributes['jobdescription_template'] ) ? $attributes['jobdescription_template'] : '' ) ) );
			?>
		</div>
		<?php
	}
}
add_action( 'personio_integration_get_content', 'personio_integration_get_content', 10, 2 );

/**
 * Get position application-link-button for list.
 *
 * @param Position $position The position as object.
 * @param array    $attributes The attributes.
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_get_formular( Position $position, array $attributes ): void {
	// convert attributes.
	$attributes = personio_integration_get_single_shortcode_attributes( $attributes );

	$text_position = 'archive';
	if ( is_single() ) {
		$text_position = 'single';
	}

	// set back to list-link.
	$back_to_list_url = get_option( 'personioIntegrationTemplateBackToListUrl', '' );
	if ( empty( $back_to_list_url ) ) {
		$back_to_list_url = get_post_type_archive_link( WP_PERSONIO_INTEGRATION_CPT );
	}

	// reset back to list-link.
	if ( 0 === absint( get_option( 'personioIntegrationTemplateBackToListButton', 0 ) ) || 'archive' === $text_position || ( isset( $attributes['show_back_to_list'] ) && empty( $attributes['show_back_to_list'] ) ) ) {
		$back_to_list_url = '';
	}

	// generate styling.
	$styles = ! empty( $attributes['styles'] ) ? $attributes['styles'] : '';

	// get template.
	include helper::get_template( 'parts/properties-application-button.php' );
}
add_action( 'personio_integration_get_formular', 'personio_integration_get_formular', 10, 2 );

/**
 * Update each post-object with the language-specific texts of a position.
 *
 * @param WP_Post $post The post as object.
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_update_post_object( WP_Post $post ): void {
	if ( WP_PERSONIO_INTEGRATION_CPT === $post->post_type ) {
		// get positions object.
		$positions = positions::get_instance();

		// get the position as object.
		$position = $positions->get_position( get_the_ID() );

		// set language.
		$position->lang = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY );

		// override the post_title.
		$post->post_title = $position->getTitle();

		// override the post_content.
		$post->post_content = $position->get_content();

		// override the post_excerpt.
		$post->post_excerpt = $position->get_excerpt();
	}
}
add_action( 'the_post', 'personio_integration_update_post_object' );

/**
 * Get single template.
 *
 * @param string $single_template The template.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_single_template( string $single_template ): string {
	if ( WP_PERSONIO_INTEGRATION_CPT === get_post_type( get_the_ID() ) ) {
		$path = helper::get_template( 'single-personioposition.php' );
		if ( file_exists( $path ) ) {
			$single_template = $path;
		}
	}
	return $single_template;
}
add_filter( 'single_template', 'personio_integration_get_single_template' );

/**
 * Get archive template.
 *
 * @param string $archive_template The template.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_archive_template( string $archive_template ): string {
	if ( is_post_type_archive( WP_PERSONIO_INTEGRATION_CPT ) ) {
		$path = helper::get_template( 'archive-personioposition.php' );
		if ( file_exists( $path ) ) {
			$archive_template = $path;
		}
	}
	return $archive_template;
}
add_filter( 'archive_template', 'personio_integration_get_archive_template' );

/**
 * Show a filter in frontend restricted to positions which are visible in list.
 *
 * @param string $filter Name of the filter (taxonomy-slug).
 * @param array  $attributes List of attributes for the filter.
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_get_filter( string $filter, array $attributes ): void {
	$taxonomy_to_use = '';
	$term_ids        = array();

	// get the terms of this taxonomy.
	foreach ( apply_filters( 'personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES ) as $taxonomy_name => $taxonomy ) {
		if ( $filter === $taxonomy['slug'] && 1 === absint( $taxonomy['useInFilter'] ) ) {
			$taxonomy_to_use = $taxonomy_name;
			$terms           = get_terms( array( 'taxonomy' => $taxonomy_name ) );
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( $term->count > 0 ) {
						$term_ids[] = $term->term_id;
					}
				}
			}
		}
	}

	// show term as filter only if its name is known.
	if ( strlen( $taxonomy_to_use ) > 0 ) {
		// get the terms of this taxonomy.
		$terms = get_terms(
			array(
				'taxonomy' => $taxonomy_to_use,
				'include'  => $term_ids,
			)
		);

		if ( ! empty( $terms ) ) {

			// get the value.
			$value = 0;
			// -> if filter is set by editor.
			if ( ! empty( $attributes['office'] ) ) {
				$value = $attributes['office'];
			}
			// -> if filter is set by user in frontend.
			if ( ! empty( $_GET['personiofilter'] ) && ! empty( $_GET['personiofilter'][ $filter ] ) ) {
				$value = sanitize_text_field( wp_unslash( $_GET['personiofilter'][ $filter ] ) );
			}

			// set name.
			$taxonomy   = get_taxonomy( $taxonomy_to_use );
			$filtername = $taxonomy->labels->singular_name;

			// get url.
			$page_url = helper::get_current_url();

			// output of filter.
			include helper::get_template( 'parts/term-filter-' . $attributes['filtertype'] . '.php' );
		}
	}
}
add_action( 'personio_integration_get_filter', 'personio_integration_get_filter', 10, 2 );

/**
 * Convert term-name to term-id if it is set in shortcode-attributes and configure shortcode-attribute.
 *
 * @param array $settings List of settings for a shortcode with 3 parts: defaults, settings & attributes.
 * @return array
 */
function personio_integration_check_filter_type( array $settings ): array {
	if ( ! empty( $settings['attributes']['filtertype'] ) ) {
		if ( ! in_array( $settings['attributes']['filtertype'], array( 'linklist', 'select' ), true ) ) {
			$settings['attributes']['filtertype'] = 'linklist';
		}
	}

	// return resulting arrays.
	return array(
		'defaults'   => $settings['defaults'],
		'settings'   => $settings['settings'],
		'attributes' => $settings['attributes'],
	);
}
add_filter( 'personio_integration_get_shortcode_attributes', 'personio_integration_check_filter_type' );

/**
 * Convert term-name to term-id if it is set in shortcode-attributes and configure shortcode-attribute.
 *
 * @param array $settings List of settings for a shortcode with 3 parts: defaults, settings & attributes.
 * @return array
 */
function personio_integration_check_taxonomies( array $settings ): array {
	// check each taxonomy if it is used as restriction for this list.
	foreach ( apply_filters( 'personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES ) as $taxonomy_name => $taxonomy ) {
		$slug = strtolower( $taxonomy['slug'] );
		if ( ! empty( $settings['attributes'][ $slug ] ) ) {
			$term = get_term_by( 'id', $settings['attributes'][ $slug ], $taxonomy_name );
			if ( ! empty( $term ) ) {
				$settings['defaults'][ $taxonomy['slug'] ]   = 0;
				$settings['settings'][ $taxonomy['slug'] ]   = 'filter';
				$settings['attributes'][ $taxonomy['slug'] ] = $term->term_id;
			}
		}
	}

	// return resulting arrays.
	return array(
		'defaults'   => $settings['defaults'],
		'settings'   => $settings['settings'],
		'attributes' => $settings['attributes'],
	);
}
add_filter( 'personio_integration_get_shortcode_attributes', 'personio_integration_check_taxonomies' );

/**
 * Return attribute defaults for shortcode in single-view.
 *
 * @return array
 */
function personio_integration_get_single_shortcode_attributes_defaults(): array {
	return array(
		'personioid'              => 0,
		'lang'                    => helper::get_current_lang(),
		'template'                => '',
		'templates'               => implode( ',', get_option( 'personioIntegrationTemplateContentDefaults', array() ) ),
		'jobdescription_template' => get_option( 'personioIntegrationTemplateJobDescription', 'default' ),
		'excerpt'                 => implode( ',', get_option( 'personioIntegrationTemplateExcerptDetail', array() ) ),
		'donotlink'               => 1,
		'styles'                  => '',
		'classes'                 => '',
	);
}

/**
 * Convert attributes for shortcodes.
 *
 * @param array $attributes List of attributes.
 * @return array
 */
function personio_integration_get_single_shortcode_attributes( array $attributes ): array {
	// define the default values for each attribute.
	$attribute_defaults = personio_integration_get_single_shortcode_attributes_defaults();

	// define the settings for each attribute (array or string).
	$attribute_settings = array(
		'personioid' => 'int',
		'lang'       => 'string',
		'templates'  => 'array',
		'excerpt'    => 'array',
		'donotlink'  => 'bool',
		'styles'     => 'string',
		'classes'    => 'string',
	);
	return helper::get_shortcode_attributes( $attribute_defaults, $attribute_settings, $attributes );
}

/**
 * Extend the WP-own search.
 *
 * @param string   $search The search-string.
 * @param WP_Query $wp_query The query-object.
 *
 * @return string
 */
function personio_integration_extend_search( string $search, WP_Query $wp_query ): string {
	global $wpdb;

	// bail on search in backend.
	if ( is_admin() ) {
		return $search;
	}

	// bail if extension of search is not enabled.
	if ( 0 === absint( get_option( 'personioIntegrationExtendSearch', 0 ) ) ) {
		return $search;
	}

	// bail of search string is empty.
	if ( empty( $search ) ) {
		return $search;
	}

	// get search request.
	$term = $wp_query->query_vars['s'];

	// create and return changed statement.
	return ' AND (
        (
            1 = 1 ' . $search . '
        )
        OR (
            ' . $wpdb->posts . ".post_type = '" . WP_PERSONIO_INTEGRATION_CPT . "'
            AND EXISTS(
                SELECT * FROM " . $wpdb->terms . '
                INNER JOIN ' . $wpdb->term_taxonomy . '
                    ON ' . $wpdb->term_taxonomy . '.term_id = ' . $wpdb->terms . '.term_id
                INNER JOIN ' . $wpdb->term_relationships . '
                    ON ' . $wpdb->term_relationships . '.term_taxonomy_id = ' . $wpdb->term_taxonomy . ".term_taxonomy_id
                WHERE taxonomy = 'personioKeywords'
                    AND object_id = " . $wpdb->posts . '.ID
                    AND ' . $wpdb->terms . ".name LIKE '%" . $term . "%'
            )
        )
    )";
}
add_filter( 'posts_search', 'personio_integration_extend_search', 10, 2 );

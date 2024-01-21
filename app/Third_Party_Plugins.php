<?php
/**
 * File to handle third-party-plugin-support.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\Plugin\Languages;
use WP_Post;
use Yoast\WP\SEO\Presentations\Indexable_Presentation;

/**
 * Handler for third-party-plugins.
 */
class Third_Party_Plugins {
	/**
	 * Instance of this object.
	 *
	 * @var ?Third_Party_Plugins
	 */
	private static ?Third_Party_Plugins $instance = null;

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
	public static function get_instance(): Third_Party_Plugins {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize the third-party-plugin-support.
	 *
	 * @return void
	 */
	public function init(): void {
		// Plugin Redirection.
		add_filter( 'redirection_post_types', array( $this, 'redirection' ) );

		// Plugin Yoast.
		add_filter( 'wpseo_opengraph_desc', array( $this, 'yoast' ), 10, 2 );

		// Plugin Rank Math.
		add_filter( 'rank_math/frontend/description', array( $this, 'rank_math' ) );

		// Plugin OG.
		add_filter( 'og_array', array( $this, 'og_optimizer' ) );

		// Plugin Easy Language.
		add_filter( 'easy_language_possible_post_types', array( $this, 'remove_easy_language_support' ) );

		// Plugin Open Graph.
		add_filter( 'fb_og_desc', array( $this, 'open_graph_optimizer' ) );

		// Plugin SEOFramework.
		add_filter( 'the_seo_framework_meta_render_data', array( $this, 'seoframework' ) );
		add_filter( 'the_seo_framework_schema_graph_data', array( $this, 'seoframework_schema' ) );

		// Plugin SEOPress.
		add_filter( 'seopress_social_og_desc', array( $this, 'seopress_og_description' ) );
		add_filter( 'seopress_titles_desc', array( $this, 'seopress_titles' ) );
	}

	/**
	 * Plugin Redirection.
	 *
	 * @param array $post_types List of post-types the Redirection-plugin supports.
	 *
	 * @return array
	 */
	public function redirection( array $post_types ): array {
		if ( ! empty( $post_types[ WP_PERSONIO_INTEGRATION_MAIN_CPT ] ) ) {
			unset( $post_types[ WP_PERSONIO_INTEGRATION_MAIN_CPT ] );
		}
		return $post_types;
	}

	/**
	 * Optimize Yoast-generated og:description-text.
	 * Without this Yoast uses the page content with formular or button-texts.
	 *
	 * @param string                 $meta_og_description The actual description for OpenGraph.
	 * @param Indexable_Presentation $presentation The WPSEO Presentation object.
	 * @return string
	 */
	public function yoast( string $meta_og_description, Indexable_Presentation $presentation ): string {
		if ( WP_PERSONIO_INTEGRATION_MAIN_CPT === $presentation->model->object_sub_type && absint( $presentation->model->object_id ) > 0 ) {
			// return resulting text without line breaks.
			return $this->replace_linebreaks( $this->get_content( $presentation->model->object_id ) );
		}
		return $meta_og_description;
	}

	/**
	 * Optimize RankMath-generated meta-description and og:description.
	 * Without this RankMath uses plain post_content, which is JSON and not really nice to read.
	 *
	 * @param string $description The actual description.
	 * @return string
	 */
	public function rank_math( string $description ): string {
		if ( is_single() ) {
			$object = get_queried_object();
			if ( $object instanceof WP_Post && WP_PERSONIO_INTEGRATION_MAIN_CPT === $object->post_type ) {
				// return resulting text without line breaks.
				return $this->replace_linebreaks( $this->get_content( $object->ID ) );
			}
		}
		return $description;
	}

	/**
	 * Optimize output of plugin OG.
	 *
	 * @source https://de.wordpress.org/plugins/og/
	 * @param array $og_array List of OpenGraph-settings from OG-plugin.
	 * @return array
	 */
	public function og_optimizer( array $og_array ): array {
		if ( is_singular( WP_PERSONIO_INTEGRATION_MAIN_CPT ) ) {
			// get description.
			$description = wp_strip_all_tags( $this->get_content( get_queried_object_id() ) );
			$description = preg_replace( '/\s+/', ' ', $description );

			// update settings.
			$position                           = new Position( get_queried_object_id() );
			$og_array['og']['title']            = $position->get_title();
			$og_array['og']['description']      = $description;
			$og_array['twitter']['title']       = $position->get_title();
			$og_array['twitter']['description'] = $description;
			$og_array['schema']['title']        = $position->get_title();
			$og_array['schema']['description']  = $description;
		}

		// return resulting list.
		return $og_array;
	}

	/**
	 * Remove our CPTs from list of possible post-types in easy-language-plugin.
	 *
	 * @param array $post_types List of post-types.
	 *
	 * @return array
	 */
	public function remove_easy_language_support( array $post_types ): array {
		if ( ! empty( $post_types[ WP_PERSONIO_INTEGRATION_MAIN_CPT ] ) ) {
			unset( $post_types[ WP_PERSONIO_INTEGRATION_MAIN_CPT ] );
		}
		return $post_types;
	}

	/**
	 * Get position content as string.
	 *
	 * @param int $post_id The ID of the requested position.
	 *
	 * @return string
	 */
	private function get_content( int $post_id ): string {
		$position = new Position( $post_id );
		$position->set_lang( Languages::get_instance()->get_current_lang() );
		$description = $position->get_content();
		if ( ! empty( $description ) ) {
			$text = '';
			foreach ( $description['jobDescription'] as $content ) {
				$text .= $content['name'] . ' ' . $content['value'];
			}

			// return resulting text.
			return $text;
		}

		// return nothing.
		return '';
	}

	/**
	 * Replace all linebreaks in given string.
	 *
	 * @param string $text_to_parse The text where we replace the line breaks.
	 *
	 * @return string
	 */
	private function replace_linebreaks( string $text_to_parse ): string {
		return preg_replace( '/\s+/', ' ', $text_to_parse );
	}

	/**
	 * Optimize output for plugin Open Graph and Twitter Card Tags.
	 *
	 * @param string $description The string the plugin would use as description.
	 *
	 * @return string
	 */
	public function open_graph_optimizer( string $description ): string {
		if ( is_singular( WP_PERSONIO_INTEGRATION_MAIN_CPT ) ) {
			// get description.
			return $this->replace_linebreaks( wp_strip_all_tags( $this->get_content( get_queried_object_id() ) ) );
		}

		// return resulting text.
		return $description;
	}

	/**
	 * Optimize output for description with plugin SEOFramework.
	 *
	 * @param array $fields The SEO-fields the framework has collected.
	 *
	 * @return array
	 */
	public function seoframework( array $fields ): array {
		if ( is_singular( WP_PERSONIO_INTEGRATION_MAIN_CPT ) ) {
			$description                                       = $this->replace_linebreaks( wp_strip_all_tags( $this->get_content( get_queried_object_id() ) ) );
			$fields['description']['attributes']['content']    = $description;
			$fields['og:description']['attributes']['content'] = $description;
			$fields['twitter:description']['attributes']['content'] = $description;
		}
		return $fields;
	}

	/**
	 * Optimize output for schema with plugin SEOFramework.
	 *
	 * @param array $graph A list of fields for SEO-output.
	 *
	 * @return array
	 */
	public function seoframework_schema( array $graph ): array {
		if ( is_singular( WP_PERSONIO_INTEGRATION_MAIN_CPT ) ) {
			foreach ( $graph as $index => $entry ) {
				if ( ! empty( $entry['description'] ) && 'WebPage' === $entry['@type'] ) {
					$graph[ $index ]['description'] = $this->replace_linebreaks( wp_strip_all_tags( $this->get_content( get_queried_object_id() ) ) );
				}
			}
		}
		return $graph;
	}

	/**
	 * Optimize the output for SEO-og:description with plugin SEOPress.
	 *
	 * @param string $meta_og_description The meta-tag the plugin would use as description.
	 *
	 * @return string
	 */
	public function seopress_og_description( string $meta_og_description ): string {
		if ( is_singular( WP_PERSONIO_INTEGRATION_MAIN_CPT ) ) {
			// get og:description.
			return '<meta property="og:description" content="' . wp_kses_post( $this->replace_linebreaks( wp_strip_all_tags( $this->get_content( get_queried_object_id() ) ) ) ) . '" />';
		}

		// return resulting text.
		return $meta_og_description;
	}

	/**
	 * Optimize the output for SEO-description with plugin SEOPress.
	 *
	 * @param string $description The SEO-description text.
	 *
	 * @return string
	 */
	public function seopress_titles( string $description ): string {
		if ( is_singular( WP_PERSONIO_INTEGRATION_MAIN_CPT ) ) {
			// get description.
			return $this->replace_linebreaks( wp_strip_all_tags( $this->get_content( get_queried_object_id() ) ) );
		}

		// return resulting text.
		return $description;
	}
}

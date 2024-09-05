<?php
/**
 * File to handle third-party-plugin-support.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Compatibilities\Wpml;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Templates;
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
		add_filter( 'manage_edit-' . PersonioPosition::get_instance()->get_name() . '_columns', array( $this, 'remove_yoast_columns' ) );

		// Plugin Rank Math.
		add_filter( 'rank_math/frontend/description', array( $this, 'rank_math' ) );
		add_filter( 'manage_edit-' . PersonioPosition::get_instance()->get_name() . '_columns', array( $this, 'remove_rank_math_columns' ) );

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

		// Plugin Simple Custom Post Order.
		add_action( 'admin_init', array( $this, 'scpo_remove_filter' ), 11 );

		// Plugin TranslatePress.
		add_filter( 'trp_translating_capability', array( $this, 'translatepress_hide_option' ) );

		// Plugin WPML.
		add_filter( 'manage_' . PersonioPosition::get_instance()->get_name() . '_posts_columns', array( $this, 'remove_wpml_column' ), 20 );
		add_filter( 'personio_integration_positions_query', array( $this, 'wpml_suppress_filters' ) );

		// Plugin Borlabs.
		add_action( 'add_meta_boxes', array( $this, 'borlabs_meta_boxes' ), PHP_INT_MAX );

		// Plugin PDF Generator for WP.
		add_filter( 'wps_wpg_customize_template_post_content', array( $this, 'pdf_generator_get_content' ), 10, 2 );

		// Plugin Slim SEO.
		add_filter( 'slim_seo_meta_description_generated', array( $this, 'slim_seo_description_get_content' ), 10, 2 );
	}

	/**
	 * Plugin Redirection.
	 *
	 * @param array $post_types List of post-types the Redirection-plugin supports.
	 *
	 * @return array
	 */
	public function redirection( array $post_types ): array {
		if ( ! empty( $post_types[ PersonioPosition::get_instance()->get_name() ] ) ) {
			unset( $post_types[ PersonioPosition::get_instance()->get_name() ] );
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
		if ( PersonioPosition::get_instance()->get_name() === $presentation->model->object_sub_type && absint( $presentation->model->object_id ) > 0 ) {
			// return resulting text without line breaks.
			return Helper::replace_linebreaks( $this->get_content( $presentation->model->object_id ) );
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
		if ( PersonioPosition::get_instance()->is_single_page_called() ) {
			// return resulting text without line breaks.
			return Helper::replace_linebreaks( $this->get_content( get_queried_object_id() ) );
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
		if ( is_singular( PersonioPosition::get_instance()->get_name() ) ) {
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
		if ( ! empty( $post_types[ PersonioPosition::get_instance()->get_name() ] ) ) {
			unset( $post_types[ PersonioPosition::get_instance()->get_name() ] );
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
		if ( ! empty( $description ) && ! empty( $description['jobDescription'] ) ) {
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
	 * Optimize output for plugin Open Graph and Twitter Card Tags.
	 *
	 * @param string $description The string the plugin would use as description.
	 *
	 * @return string
	 */
	public function open_graph_optimizer( string $description ): string {
		if ( is_singular( PersonioPosition::get_instance()->get_name() ) ) {
			// get description.
			return Helper::replace_linebreaks( wp_strip_all_tags( $this->get_content( get_queried_object_id() ) ) );
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
		if ( is_singular( PersonioPosition::get_instance()->get_name() ) ) {
			$description                                       = Helper::replace_linebreaks( wp_strip_all_tags( $this->get_content( get_queried_object_id() ) ) );
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
		if ( is_singular( PersonioPosition::get_instance()->get_name() ) ) {
			foreach ( $graph as $index => $entry ) {
				if ( ! empty( $entry['description'] ) && 'WebPage' === $entry['@type'] ) {
					$graph[ $index ]['description'] = Helper::replace_linebreaks( wp_strip_all_tags( $this->get_content( get_queried_object_id() ) ) );
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
		if ( is_singular( PersonioPosition::get_instance()->get_name() ) ) {
			// get og:description.
			return '<meta property="og:description" content="' . wp_kses_post( Helper::replace_linebreaks( wp_strip_all_tags( $this->get_content( get_queried_object_id() ) ) ) ) . '" />';
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
		if ( is_singular( PersonioPosition::get_instance()->get_name() ) ) {
			// get description.
			return Helper::replace_linebreaks( wp_strip_all_tags( $this->get_content( get_queried_object_id() ) ) );
		}

		// return resulting text.
		return $description;
	}

	/**
	 * Prevent usage of order our own ctp positions via plugin Simple Custom Order.
	 *
	 * @return void
	 */
	public function scpo_remove_filter(): void {
		global $pagenow;
		if ( 'edit-' . PersonioPosition::get_instance()->get_name() . '.php' === $pagenow ) {
			wp_dequeue_script( 'scporderjs' );
		}
	}

	/**
	 * Remove Yoast's columns from our own cpt.
	 *
	 * @param array $columns List of columns.
	 *
	 * @return array
	 */
	public function remove_yoast_columns( array $columns ): array {
		if ( isset( $columns['wpseo-score'] ) ) {
			unset( $columns['wpseo-score'] );
			unset( $columns['wpseo-score-readability'] );
			unset( $columns['wpseo-title'] );
			unset( $columns['wpseo-metadesc'] );
			unset( $columns['wpseo-focuskw'] );
			unset( $columns['wpseo-links'] );
			unset( $columns['wpseo-linked'] );
		}
		return $columns;
	}

	/**
	 * Remove Rank Math's columns from our own cpt.
	 *
	 * @param array $columns List of columns.
	 *
	 * @return array
	 */
	public function remove_rank_math_columns( array $columns ): array {
		if ( isset( $columns['rank_math_seo_details'] ) ) {
			unset( $columns['rank_math_seo_details'] );
			unset( $columns['rank_math_title'] );
			unset( $columns['rank_math_description'] );
		}
		return $columns;
	}

	/**
	 * Remove SEO Frameworks meta box from our own cpt as it could not be saved.
	 *
	 * @return void
	 */
	public function remove_seo_framework_meta_box(): void {
		remove_meta_box( 'tsf-inpost-box', PersonioPosition::get_instance()->get_name(), 'normal' );
	}

	/**
	 * Hide translation-option on our own custom post type pages.
	 *
	 * @param string $capability The actual capability.
	 *
	 * @return string
	 */
	public function translatepress_hide_option( string $capability ): string {
		// bail if this is admin.
		if ( is_admin() ) {
			return $capability;
		}

		// get actual object.
		$object_id = get_queried_object_id();

		// bail if this is not our cpt.
		if ( get_post_type( $object_id ) !== PersonioPosition::get_instance()->get_name() ) {
			return $capability;
		}

		// return 'god' to disable any translation-options on our cpt.
		return 'god';
	}

	/**
	 * Remove WPML translation columns.
	 *
	 * @param array $columns List of columns.
	 *
	 * @return array
	 */
	public function remove_wpml_column( array $columns ): array {
		if ( isset( $columns['icl_translations'] ) ) {
			unset( $columns['icl_translations'] );
		}

		// return resulting list.
		return $columns;
	}

	/**
	 * Remove meta boxes added by Borlabs from our cpts.
	 *
	 * @return void
	 */
	public function borlabs_meta_boxes(): void {
		foreach ( Helper::get_list_of_our_cpts() as $cpt ) {
			remove_meta_box( 'borlabs-cookie-meta-box', $cpt, 'normal' );
		}
	}

	/**
	 * Get content for PDF print via PDF Generator for WP.
	 *
	 * @param string  $content Content to output.
	 * @param WP_Post $post The post-object which is used for the output.
	 *
	 * @return string
	 */
	public function pdf_generator_get_content( string $content, WP_Post $post ): string {
		// bail if this is not our cpt.
		if ( PersonioPosition::get_instance()->get_name() !== $post->post_type ) {
			return $content;
		}

		// get the requested position.
		$position_obj = Positions::get_instance()->get_position( $post->ID );

		// return our compiled content.
		return Templates::get_instance()->get_content_template( $position_obj, array(), true );
	}

	/**
	 * Return the position description for meta description with Slim SEO.
	 *
	 * @param string  $meta_description The description.
	 * @param WP_Post $post The post-object.
	 *
	 * @return string
	 */
	public function slim_seo_description_get_content( string $meta_description, WP_Post $post ): string {
		// bail if this is not our cpt.
		if ( PersonioPosition::get_instance()->get_name() !== get_post_type( $post ) ) {
			return $meta_description;
		}

		// get the requested position.
		$position_obj = Positions::get_instance()->get_position( $post->ID );

		// return our compiled content.
		return Templates::get_instance()->get_content_template( $position_obj, array(), true );
	}

	/**
	 * Suppress filters for position query if WPML is enabled.
	 *
	 * @param array $query The data for WP_Query.
	 *
	 * @return array
	 */
	public function wpml_suppress_filters( array $query ): array {
		// bail if wpml is not active.
		if ( ! Wpml::get_instance()->is_active() ) {
			return $query;
		}

		$false = false;
		/**
		 * Bail via filter.
		 *
		 * @since 3.0.3 Available since 3.0.3.
		 *
		 * @param bool $false Whether optimizations should be prevented (true) or not (false)
		 * @param array $query The running position query.
		 *
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_prevent_wpml_optimizations', $false, $query ) ) {
			return $query;
		}

		$query['suppress_filters'] = true;
		return $query;
	}
}

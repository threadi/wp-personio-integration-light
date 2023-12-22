<?php
/**
 * File to handle third-party-plugin-support.
 *
 * @package personio-integration-light
 */

namespace App\PersonioIntegration;

use personioIntegration\Position;
use WP_Post;
use Yoast\WP\SEO\Presentations\Indexable_Presentation;

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
	}

	/**
	 * Plugin Redirection.
	 *
	 * TODO test!
	 *
	 * @param array $post_types List of post-types the Redirection-plugin supports.
	 *
	 * @return array
	 */
	public function redirection( array $post_types ): array {
		if ( ! empty( $post_types[ WP_PERSONIO_INTEGRATION_CPT ] ) ) {
			unset( $post_types[ WP_PERSONIO_INTEGRATION_CPT ] );
		}
		return $post_types;
	}

	/**
	 * Optimize Yoast-generated og:description-text.
	 * Without this Yoast uses the page content with formular or button-texts.
	 *
	 * TODO test!
	 *
	 * @param string                 $meta_og_description The actual description for OpenGraph.
	 * @param Indexable_Presentation $presentation The WPSEO Presentation object.
	 * @return string
	 */
	public function yoast( string $meta_og_description, Indexable_Presentation $presentation ): string {
		if ( WP_PERSONIO_INTEGRATION_CPT === $presentation->model->object_sub_type ) {
			$position = new Position( $presentation->model->object_id );
			return preg_replace( '/\s+/', ' ', $position->get_content() );
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
			if ( $object instanceof WP_Post && WP_PERSONIO_INTEGRATION_CPT === $object->post_type ) {
				$position = new Position( $object->ID );
				return preg_replace( '/\s+/', ' ', $position->get_content() );
			}
		}
		return $description;
	}
}

<?php
/**
 * File to handle multiple Gutenberg-patterns.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder\Gutenberg;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object to handle all Gutenberg-patterns of this plugin.
 */
class Patterns {
	/**
	 * The instance of this object.
	 *
	 * @var Patterns|null
	 */
	private static ?Patterns $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
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
	public static function get_instance(): Patterns {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		// register pattern and categories.
		$this->register_category();
		$this->register_patterns();
	}

	/**
	 * Return the in this plugin available pattern.
	 *
	 * @return array[]
	 */
	private function get_patterns(): array {
		$patterns = array(
			'personio-integration-light/single-position' => array(
				'title'       => __( 'Personio Integration Single View', 'personio-integration-light' ),
				'description' => __( 'Display single position.', 'personio-integration-light' ),
				'template'    => 'gutenberg/pattern-single.html',
			),
			'personio-integration-light/query-loop'      => array(
				'title'       => __( 'Personio Integration Query Loop', 'personio-integration-light' ),
				'description' => __( 'Predefined layout for query loop.', 'personio-integration-light' ),
				'template'    => 'gutenberg/query-loop.html',
				'args'        => array(
					'blockTypes' => array( 'core/query' ),
				),
			),
		);

		/**
		 * Filter the list of pattern we provide for Gutenberg / Block Editor.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $patterns List of patterns.
		 */
		return apply_filters( 'personio_integration_gutenberg_pattern', $patterns );
	}

	/**
	 * Register our patterns.
	 *
	 * @return void
	 */
	private function register_patterns(): void {
		// bail if needed function is not available.
		if ( ! function_exists( 'register_block_pattern' ) ) {
			return;
		}

		// get WP Filesystem-handler.
		require_once ABSPATH . '/wp-admin/includes/file.php';
		\WP_Filesystem();
		global $wp_filesystem;

		// loop through the patterns and add them.
		foreach ( $this->get_patterns() as $pattern_name => $pattern ) {
			// bail if no template is given.
			if ( empty( $pattern['template'] ) ) {
				continue;
			}

			// get path of templates.
			$template_path = \PersonioIntegrationLight\Plugin\Templates::get_instance()->get_template( $pattern['template'] );

			// bail if file does not exist.
			if ( ! file_exists( $template_path ) ) {
				continue;
			}

			// get content for pattern from template.
			$content = $wp_filesystem->get_contents( $template_path );

			// bail if no content could be loaded.
			if ( empty( $content ) ) {
				continue;
			}

			// create arguments.
			$args = array(
				'title'       => $pattern['title'],
				'description' => $pattern['description'],
				'categories'  => array( 'personio-integration' ),
				'keywords'    => array( 'personio' ),
				'content'     => $content,
				'source'      => 'plugin',
			);

			// add custom args, if set.
			if ( isset( $pattern['args'] ) ) {
				$args = array_merge( $args, $pattern['args'] );
			}

			// register pattern.
			register_block_pattern( $pattern_name, $args );
		}
	}

	/**
	 * Register our own category for patterns.
	 *
	 * @return void
	 */
	private function register_category(): void {
		register_block_pattern_category(
			'personio-integration',
			array( 'label' => __( 'Personio Integration', 'personio-integration-light' ) )
		);
	}
}

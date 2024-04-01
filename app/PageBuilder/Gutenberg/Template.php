<?php
/**
 * File to handle single Gutenberg-template.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder\Gutenberg;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use WP_Block_Template;

/**
 * Object to represent a single gutenberg template for our own plugin.
 */
class Template {

	/**
	 * The template type.
	 *
	 * @var string
	 */
	private string $type;

	/**
	 * The template slug.
	 *
	 * @var string
	 */
	private string $slug;

	/**
	 * The template source.
	 *
	 * @var string
	 */
	private string $source;

	/**
	 * The template title.
	 *
	 * @var string
	 */
	private string $title;

	/**
	 * The template description.
	 *
	 * @var string
	 */
	private string $description;

	/**
	 * The template itself.
	 *
	 * @var string
	 */
	private string $template;

	/**
	 * The template content.
	 *
	 * @var string
	 */
	private string $content;

	/**
	 * The template ID.
	 *
	 * @var int
	 */
	private int $post_id;

	/**
	 * Constructor, not used as this a Singleton object.
	 */
	public function __construct() {}

	/**
	 * Get path to template file.
	 *
	 * @return string
	 */
	public function get_file_path(): string {
		return Helper::get_plugin_path() . 'templates/gutenberg/' . $this->get_slug() . '.html';
	}

	/**
	 * Get template type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Set template type.
	 *
	 * @param string $type The type.
	 * @return void
	 */
	public function set_type( string $type ): void {
		$this->type = $type;
	}

	/**
	 * Get template slug.
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Set template slug.
	 *
	 * @param string $slug The slug.
	 * @return void
	 */
	public function set_slug( string $slug ): void {
		$this->slug = $slug;
	}

	/**
	 * Get template source.
	 *
	 * @return string
	 */
	public function get_source(): string {
		return $this->source;
	}

	/**
	 * Set template source.
	 *
	 * @param string $source The source.
	 * @return void
	 */
	public function set_source( string $source ): void {
		$this->source = $source;
	}

	/**
	 * Get template title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * Set template source.
	 *
	 * @param string $title The title.
	 * @return void
	 */
	public function set_title( string $title ): void {
		$this->title = $title;
	}

	/**
	 * Get template title.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Set template source.
	 *
	 * @param string $description The description.
	 * @return void
	 */
	public function set_description( string $description ): void {
		$this->description = $description;
	}

	/**
	 * Return this object as object.
	 *
	 * @return mixed
	 */
	public function get_object(): object {
		$new_template_item = array(
			'slug'        => $this->get_slug(),
			'id'          => WP_PERSONIO_GUTENBERG_PARENT_ID . '//' . $this->get_slug(),
			'path'        => $this->get_file_path(),
			'type'        => $this->get_type(),
			'theme'       => WP_PERSONIO_GUTENBERG_PARENT_ID,
			'source'      => $this->get_source(),
			'title'       => $this->get_title(),
			'description' => $this->get_description(),
			'post_types'  => array(), // Don't appear in any Edit Post template selector dropdown.
		);

		return (object) $new_template_item;
	}

	/**
	 * Return this object as block template.
	 *
	 * @return WP_Block_Template
	 * @noinspection PhpTernaryExpressionCanBeReducedToShortVersionInspection
	 */
	public function get_block_template(): WP_Block_Template {
		// check the source of this template (plugin or theme).
		$template_is_from_theme = 'theme' === $this->get_source();

		// get the theme name.
		$theme_name = wp_get_theme()->get( 'TextDomain' );

		// create Block Template  object.
		$template                 = new WP_Block_Template();
		$template->id             = $template_is_from_theme ? $theme_name . '//' . $this->get_slug() : WP_PERSONIO_GUTENBERG_PARENT_ID . '//' . $this->get_slug();
		$template->theme          = $template_is_from_theme ? $theme_name : WP_PERSONIO_GUTENBERG_PARENT_ID;
		$template->content        = $this->inject_theme_attribute_in_content( $this->get_content() );
		$template->source         = $this->get_source() ? $this->get_source() : 'plugin';
		$template->slug           = $this->get_slug();
		$template->type           = $this->get_type();
		$template->title          = $this->get_title();
		$template->description    = $this->get_description();
		$template->status         = 'publish';
		$template->has_theme_file = true;
		$template->origin         = $this->get_source();
		$template->is_custom      = false;
		$template->post_types     = array();
		$template->area           = 'uncategorized';

		// return result.
		return $template;
	}

	/**
	 * Parse block template content to inject theme-specific attributes in it (e.g. for header and footer).
	 *
	 * @source WooCommerce BlockTemplateUtils.php
	 * @param string $template_content The content with block data.
	 * @return string Updated wp_template content.
	 */
	private function inject_theme_attribute_in_content( string $template_content ): string {
		$theme               = wp_get_theme()->get_stylesheet();
		$has_updated_content = false;
		$new_content         = '';
		$template_blocks     = parse_blocks( $template_content );

		$blocks = $this->flatten_blocks( $template_blocks );
		foreach ( $blocks as &$block ) {
			if (
				'core/template-part' === $block['blockName'] &&
				! isset( $block['attrs']['theme'] )
			) {
				$block['attrs']['theme'] = $theme;
				$has_updated_content     = true;
			}
		}

		if ( $has_updated_content ) {
			foreach ( $template_blocks as &$block ) {
				$new_content .= serialize_block( $block );
			}

			return $new_content;
		}

		return $template_content;
	}

	/**
	 * Parse block template content to inject theme-specific attributes in it (e.g. for header and footer).
	 *
	 * @source WooCommerce BlockTemplateUtils.php
	 *
	 * @param string $template_content The content with blocks.
	 *
	 * @return string Updated wp_template content.
	 */
	public function update_theme_attribute_in_content( string $template_content ): string {
		$theme               = wp_get_theme()->get_stylesheet();
		$has_updated_content = false;
		$new_content         = '';
		$template_blocks     = parse_blocks( $template_content );

		$blocks = $this->flatten_blocks( $template_blocks );
		foreach ( $blocks as &$block ) {
			if ( 'core/template-part' === $block['blockName'] ) {
				$block['attrs']['theme'] = $theme;
				$has_updated_content     = true;
			}
		}

		if ( $has_updated_content ) {
			foreach ( $template_blocks as &$block ) {
				$new_content .= serialize_block( $block );
			}

			return $new_content;
		}

		return $template_content;
	}

	/**
	 * Returns an array containing the references of
	 * the passed blocks and their inner blocks.
	 *
	 * @source WooCommerce BlockTemplateUtils.php
	 * @param array $blocks List of blocks.
	 * @return array block references to the passed blocks and their inner blocks.
	 */
	private function flatten_blocks( array &$blocks ): array {
		$all_blocks = array();
		$queue      = array();
		foreach ( $blocks as &$block ) {
			$queue[] = &$block;
		}
		$queue_count = count( $queue );

		while ( $queue_count > 0 ) {
			$block = &$queue[0];
			array_shift( $queue );
			$all_blocks[] = &$block;

			if ( ! empty( $block['innerBlocks'] ) ) {
				foreach ( $block['innerBlocks'] as &$inner_block ) {
					$queue[] = &$inner_block;
				}
			}

			$queue_count = count( $queue );
		}

		return $all_blocks;
	}

	/**
	 * Get template-name for this template (e.g. "archive-xy").
	 *
	 * @return string
	 */
	public function get_template(): string {
		return $this->template;
	}

	/**
	 * Set template-name for this template (e.g. "archive-xy").
	 *
	 * @param string $template The template.
	 * @return void
	 */
	public function set_template( string $template ): void {
		$this->template = $template;
	}

	/**
	 * The template-object is valid if the template-file exist.
	 *
	 * @return bool
	 */
	public function is_valid(): bool {
		return file_exists( $this->get_file_path() );
	}

	/**
	 * Get the content.
	 *
	 * @return string
	 */
	public function get_content(): string {
		if ( empty( $this->content ) ) {
			// get WP Filesystem-handler.
			require_once ABSPATH . '/wp-admin/includes/file.php';
			\WP_Filesystem();
			global $wp_filesystem;

			$this->content = $wp_filesystem->get_contents( $this->get_file_path() );
		}
		return $this->content;
	}

	/**
	 * Set the content.
	 *
	 * @param string $content The content.
	 * @return void
	 */
	public function set_content( string $content ): void {
		$this->content = $content;
	}

	/**
	 * Get post_id if template resists in DB.
	 *
	 * @return int
	 */
	public function get_post_id(): int {
		return $this->post_id;
	}

	/**
	 * Set post_id if template resists in DB.
	 *
	 * @param int $post_id The post id.
	 * @return void
	 */
	public function set_post_id( int $post_id ): void {
		$this->post_id = $post_id;
	}
}

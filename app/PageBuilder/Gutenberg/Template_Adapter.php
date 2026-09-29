<?php
/**
 * File for the adapter, which makes the block templates accessible for abilities.
 *
 * @package personio-integration-light
 */

declare(strict_types=1);

namespace PersonioIntegrationLight\PageBuilder\Gutenberg;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Abilities\Abilities_Settings;
use PersonioIntegrationLight\Abilities\Template_Abilities;
use PersonioIntegrationLight\Abilities\Template_Adapter_Base;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use Throwable;
use WP_Block_Template;
use WP_Block_Type_Registry;
use WP_Error;
use WP_Post;
use WP_Query;

/**
 * Adapter for the block templates of block themes (Gutenberg / Site Editor).
 */
class Template_Adapter extends Template_Adapter_Base {
	/**
	 * The prefix of the names of our blocks.
	 */
	private const BLOCK_PREFIX = 'wp-personio-integration/';

	/**
	 * Return the internal name of the page builder.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'gutenberg';
	}

	/**
	 * Return the human-readable name of the page builder.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Block Editor (Gutenberg)', 'personio-integration-light' );
	}

	/**
	 * Return whether the block templates can be used, which requires a block theme.
	 *
	 * @return bool
	 */
	public function is_available(): bool {
		return Helper::theme_is_fse_theme();
	}

	/**
	 * Return the reason why the block templates can not be used.
	 *
	 * @return string
	 */
	public function get_unavailable_reason(): string {
		return __( 'The active theme is not a block theme. Templates of block themes are only available with block themes.', 'personio-integration-light' );
	}

	/**
	 * Return a description of the format of block templates.
	 *
	 * @return string
	 */
	public function get_format(): string {
		return __( 'WordPress block markup: serialized blocks as HTML comments, e.g. <!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group">…</div><!-- /wp:group -->. Blocks without inner content are self-closing, e.g. <!-- wp:wp-personio-integration/application-button /-->. The template is a block template of the active block theme, so it contains the whole page including the template parts for header and footer.', 'personio-integration-light' );
	}

	/**
	 * Return the slug of the block template for the given type.
	 *
	 * @param string $type The template type.
	 *
	 * @return string
	 */
	private function get_slug( string $type ): string {
		return $type . '-' . PersonioPosition::get_instance()->get_name();
	}

	/**
	 * Return the descriptions of attributes of our blocks, which are not in their block.json.
	 *
	 * @return array<string,string>
	 */
	private function get_attribute_descriptions(): array {
		return array(
			'excerptTemplates'    => __( 'The details to show, as list of taxonomy slugs, see values.taxonomies. E.g. ["recruitingCategory","office"].', 'personio-integration-light' ),
			'filter'              => __( 'The filters to show, as list of taxonomy slugs, see values.taxonomies. E.g. ["office","department"].', 'personio-integration-light' ),
			'template'            => __( 'The template for the output: for the details see values.excerpt_templates, for the description see values.jobdescription_templates.', 'personio-integration-light' ),
			'groupby'             => __( 'Group the list by this taxonomy slug, see values.taxonomies. Empty for no grouping.', 'personio-integration-light' ),
			'sort'                => __( 'The sort direction: "asc" or "desc".', 'personio-integration-light' ),
			'sortby'              => __( 'Sort by "title" or "date".', 'personio-integration-light' ),
			'limit'               => __( 'The maximum amount of positions to show, 0 for all.', 'personio-integration-light' ),
			'id'                  => __( 'The post-ID of the position to show. Only needed outside of templates for positions.', 'personio-integration-light' ),
			'hideFilterTitle'     => __( 'Hide the title of the filter.', 'personio-integration-light' ),
			'hideResetLink'       => __( 'Hide the link to reset the filter.', 'personio-integration-light' ),
			'hideSubmitButton'    => __( 'Hide the submit button of the filter.', 'personio-integration-light' ),
			'showTitle'           => __( 'Show the title of the position.', 'personio-integration-light' ),
			'linkTitle'           => __( 'Link the title to the detail view of the position.', 'personio-integration-light' ),
			'showExcerpt'         => __( 'Show the details of the position (see excerptTemplates).', 'personio-integration-light' ),
			'showContent'         => __( 'Show the job description.', 'personio-integration-light' ),
			'showApplicationForm' => __( 'Show the application button.', 'personio-integration-light' ),
		);
	}

	/**
	 * Return the template types for which our blocks are meant.
	 *
	 * @return array<string,array<int,string>>
	 */
	private function get_block_template_types(): array {
		$list = array(
			self::BLOCK_PREFIX . 'details'            => array( 'single' ),
			self::BLOCK_PREFIX . 'description'        => array( 'single' ),
			self::BLOCK_PREFIX . 'application-button' => array( 'single' ),
			self::BLOCK_PREFIX . 'list'               => array( 'archive' ),
			self::BLOCK_PREFIX . 'filter-list'        => array( 'archive' ),
			self::BLOCK_PREFIX . 'filter-select'      => array( 'archive' ),
			self::BLOCK_PREFIX . 'show'               => array(),
			self::BLOCK_PREFIX . 'description-part'   => array( 'single' ),
			self::BLOCK_PREFIX . 'field'              => array( 'single' ),
			self::BLOCK_PREFIX . 'files'              => array( 'single' ),
			self::BLOCK_PREFIX . 'salary'             => array( 'single' ),
			self::BLOCK_PREFIX . 'videos'             => array( 'single' ),
			self::BLOCK_PREFIX . 'map'                => array( 'single', 'archive' ),
			self::BLOCK_PREFIX . 'general-application-form' => array( 'archive' ),
			self::BLOCK_PREFIX . 'filter-linklist'    => array( 'archive' ),
			self::BLOCK_PREFIX . 'circle-search'      => array( 'archive' ),
			self::BLOCK_PREFIX . 'search'             => array( 'archive' ),
			self::BLOCK_PREFIX . 'position-count'     => array( 'archive' ),
			self::BLOCK_PREFIX . 'term-count'         => array( 'archive' ),
		);

		/**
		 * Filter the template types for which our blocks are meant.
		 *
		 * @since 5.3.0 Available since 5.3.0.
		 * @param array<string,array<int,string>> $list List of block names with their template types.
		 */
		return apply_filters( 'personio_integration_gutenberg_ability_block_template_types', $list );
	}

	/**
	 * Return an example for the given block.
	 *
	 * @param string $block_name The block name.
	 *
	 * @return string
	 */
	private function get_example( string $block_name ): string {
		$examples = array(
			self::BLOCK_PREFIX . 'details'       => '<!-- wp:wp-personio-integration/details {"excerptTemplates":["recruitingCategory","office"]} /-->',
			self::BLOCK_PREFIX . 'description'   => '<!-- wp:wp-personio-integration/description {"template":"default"} /-->',
			self::BLOCK_PREFIX . 'list'          => '<!-- wp:wp-personio-integration/list {"showExcerpt":true,"excerptTemplates":["office"]} /-->',
			self::BLOCK_PREFIX . 'filter-list'   => '<!-- wp:wp-personio-integration/filter-list {"filter":["office","department"]} /-->',
			self::BLOCK_PREFIX . 'filter-select' => '<!-- wp:wp-personio-integration/filter-select {"filter":["office","department"]} /-->',
			self::BLOCK_PREFIX . 'show'          => '<!-- wp:wp-personio-integration/show {"id":123} /-->',
		);

		// return the example or a self-closing block without attributes.
		return $examples[ $block_name ] ?? '<!-- wp:' . $block_name . ' /-->';
	}

	/**
	 * Return the elements, which can be used in block templates: our blocks and useful core blocks.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public function get_elements(): array {
		$elements               = array();
		$attribute_descriptions = $this->get_attribute_descriptions();
		$template_types         = $this->get_block_template_types();

		// add our own blocks.
		foreach ( WP_Block_Type_Registry::get_instance()->get_all_registered() as $block_type ) {
			if ( ! str_starts_with( $block_type->name, self::BLOCK_PREFIX ) ) {
				continue;
			}

			// collect the attributes, without the internal ones.
			$attributes = array();
			foreach ( (array) $block_type->attributes as $name => $settings ) {
				if ( in_array( $name, array( 'blockId', 'preview', 'lock', 'metadata', 'className', 'style' ), true ) ) {
					continue;
				}
				$attributes[ $name ] = array(
					'type'        => is_array( $settings ) && isset( $settings['type'] ) ? $settings['type'] : 'string',
					'description' => $attribute_descriptions[ $name ] ?? '',
					'default'     => is_array( $settings ) && isset( $settings['default'] ) ? $settings['default'] : null,
				);
			}

			$elements[] = array(
				'name'           => $block_type->name,
				'title'          => (string) $block_type->title,
				'description'    => (string) $block_type->description,
				'template_types' => $template_types[ $block_type->name ] ?? array(),
				'attributes'     => $attributes,
				'example'        => $this->get_example( $block_type->name ),
			);
		}

		// add useful core blocks.
		$core_blocks = array(
			array(
				'name'           => 'core/template-part',
				'title'          => __( 'Template part', 'personio-integration-light' ),
				'description'    => __( 'Header or footer of the theme, see the hint about the available template parts.', 'personio-integration-light' ),
				'template_types' => array( 'single', 'archive' ),
				'example'        => '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->',
			),
			array(
				'name'           => 'core/post-title',
				'title'          => __( 'Title of the position', 'personio-integration-light' ),
				'description'    => __( 'Shows the title of the position.', 'personio-integration-light' ),
				'template_types' => array( 'single' ),
				'example'        => '<!-- wp:post-title {"level":1} /-->',
			),
			array(
				'name'           => 'core/query-title',
				'title'          => __( 'Title of the list', 'personio-integration-light' ),
				'description'    => __( 'Shows the title of the list of positions.', 'personio-integration-light' ),
				'template_types' => array( 'archive' ),
				'example'        => '<!-- wp:query-title {"type":"archive"} /-->',
			),
			array(
				'name'           => 'core/group',
				'title'          => __( 'Group', 'personio-integration-light' ),
				'description'    => __( 'Container for the layout of other blocks.', 'personio-integration-light' ),
				'template_types' => array( 'single', 'archive' ),
				'example'        => '<!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:post-title /--></div><!-- /wp:group -->',
			),
			array(
				'name'           => 'core/columns',
				'title'          => __( 'Columns', 'personio-integration-light' ),
				'description'    => __( 'Shows blocks in columns, e.g. the details next to the description.', 'personio-integration-light' ),
				'template_types' => array( 'single', 'archive' ),
				'example'        => '<!-- wp:columns --><div class="wp-block-columns"><!-- wp:column {"width":"66%"} --><div class="wp-block-column" style="flex-basis:66%"><!-- wp:wp-personio-integration/description /--></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:wp-personio-integration/details /--><!-- wp:wp-personio-integration/application-button /--></div><!-- /wp:column --></div><!-- /wp:columns -->',
			),
			array(
				'name'           => 'core/heading',
				'title'          => __( 'Heading', 'personio-integration-light' ),
				'description'    => __( 'A heading, e.g. above the description.', 'personio-integration-light' ),
				'template_types' => array( 'single', 'archive' ),
				'example'        => '<!-- wp:heading --><h2 class="wp-block-heading">Your tasks</h2><!-- /wp:heading -->',
			),
		);
		foreach ( $core_blocks as $core_block ) {
			$core_block['attributes'] = array();
			$elements[]               = $core_block;
		}

		// return the resulting list.
		return $elements;
	}

	/**
	 * Return hints for building block templates.
	 *
	 * @return array<int,string>
	 */
	public function get_hints(): array {
		$hints = array(
			__( 'Start with get-template and change the actual template instead of writing a new one from scratch.', 'personio-integration-light' ),
			__( 'The texts of the positions come from Personio. Do not write them into the template, use the blocks of this plugin to show them.', 'personio-integration-light' ),
			__( 'In the template for the detail view, the blocks of this plugin show the position of the actual page. Do not set the attribute "id" there.', 'personio-integration-light' ),
			__( 'Use only the values from this catalog for the attributes (e.g. taxonomy slugs and templates). Unknown values are not shown.', 'personio-integration-light' ),
			__( 'Use only valid block markup. Every opening block comment needs a closing one, unless the block is self-closing. Attributes must be valid JSON.', 'personio-integration-light' ),
			__( 'Do not use block bindings (core/post-meta) or custom HTML for the data of positions. Only the blocks of this plugin show them in the correct language and format.', 'personio-integration-light' ),
			__( 'Check every template with preview-template and fix all errors before it is used.', 'personio-integration-light' ),
			__( 'The blocks of this plugin support the usual block settings for typography, colors and spacing (e.g. "fontSize", "style"). Do not set the attribute "blockId", it is managed by the block editor.', 'personio-integration-light' ),
			__( 'CSS for the template belongs into the parameter "css" of save-template, not into the template. It is loaded only on the pages which use this template. Use own class names via "className" and prefix them to avoid conflicts with the theme.', 'personio-integration-light' ),
			__( 'Saving: call save-template first with "dry_run": true, show the result to the user and save with "dry_run": false only after the user agreed. reset-template restores the original template.', 'personio-integration-light' ),
		);

		// add the available template parts of the theme.
		$template_parts = array();
		foreach ( get_block_templates( array(), 'wp_template_part' ) as $template_part ) {
			$template_parts[] = $template_part->slug;
		}
		if ( ! empty( $template_parts ) ) {
			/* translators: %1$s will be replaced by a list of slugs. */
			$hints[] = sprintf( __( 'Available template parts of the active theme: %1$s.', 'personio-integration-light' ), implode( ', ', array_unique( $template_parts ) ) );
		}

		return $hints;
	}

	/**
	 * Return the actual block template of the given type.
	 *
	 * @param string $type The template type.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function get_template( string $type ): array|WP_Error {
		$slug = $this->get_slug( $type );

		// get the template, a customized one is preferred.
		$result = false;
		foreach ( get_block_templates( array( 'slug__in' => array( $slug ) ), 'wp_template' ) as $template ) {
			if ( $slug !== $template->slug ) {
				continue;
			}
			if ( false === $result || $this->is_customized( $template ) ) {
				$result = $template;
			}
		}

		// bail if no template could be found.
		if ( ! $result instanceof WP_Block_Template ) {
			return new WP_Error( 'personio_integration_template_not_found', __( 'The template could not be found.', 'personio-integration-light' ) );
		}

		// return the template.
		return array(
			'id'            => (string) $result->id,
			'source'        => (string) $result->source,
			'is_customized' => $this->is_customized( $result ),
			'content'       => (string) $result->content,
		);
	}

	/**
	 * Return whether block templates can be saved via abilities.
	 *
	 * @return bool
	 */
	public function can_save(): bool {
		return $this->is_available();
	}

	/**
	 * Return the CSS for the template of the given type.
	 *
	 * @param string $type The template type.
	 *
	 * @return string
	 */
	public function get_css( string $type ): string {
		return Template_Styles::get_instance()->get_css( $type );
	}

	/**
	 * Return the post of the customized template of the given type, if one exists.
	 *
	 * @param string $type The template type.
	 *
	 * @return WP_Post|null
	 */
	private function get_customized_post( string $type ): ?WP_Post {
		foreach ( Templates::get_instance()->get_templates_from_db( array( $this->get_slug( $type ) ), 'wp_template' ) as $template ) {
			$post = get_post( $template->get_post_id() );
			if ( $post instanceof WP_Post ) {
				return $post;
			}
		}
		return null;
	}

	/**
	 * Save the given block template as customized template of the given type.
	 *
	 * An existing customized template is updated (the previous version is kept as revision),
	 * otherwise a customized template is created, like the Site Editor does it.
	 *
	 * @param string      $type    The template type.
	 * @param string      $content The block markup.
	 * @param string|null $css     The CSS for this template, null to keep the actual CSS, "" to remove it.
	 * @param bool        $dry_run True to only return what would happen.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function save_template( string $type, string $content, ?string $css, bool $dry_run ): array|WP_Error {
		// bail if block templates are not available.
		if ( ! $this->can_save() ) {
			return new WP_Error( 'personio_integration_saving_not_supported', $this->get_unavailable_reason() );
		}

		// get the customized template, if it exists.
		$post   = $this->get_customized_post( $type );
		$slug   = $this->get_slug( $type );
		$result = array(
			'action' => $post instanceof WP_Post ? 'update' : 'create',
			'id'     => $post instanceof WP_Post ? $post->ID : 0,
		);

		// bail on dry run.
		if ( $dry_run ) {
			return $result;
		}

		// update the existing customized template.
		if ( $post instanceof WP_Post ) {
			// keep the actual version as revision, if none exists yet (WordPress only saves the new version as revision).
			if ( empty( wp_get_post_revisions( $post->ID ) ) ) {
				wp_save_post_revision( $post->ID );
			}

			$post_id = wp_update_post(
				array(
					'ID'           => $post->ID,
					'post_content' => wp_slash( $content ),
				),
				true
			);
		} else {
			// get the title of the actual template.
			$title = $slug;
			foreach ( get_block_templates( array( 'slug__in' => array( $slug ) ), 'wp_template' ) as $block_template ) {
				if ( $slug === $block_template->slug && ! empty( $block_template->title ) ) {
					$title = (string) $block_template->title;
					break;
				}
			}

			// create the customized template, assigned to our templates like the Site Editor does it.
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'wp_template',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $title,
					'post_content' => wp_slash( $content ),
				),
				true
			);
			if ( ! $post_id instanceof WP_Error ) {
				wp_set_post_terms( $post_id, WP_PERSONIO_GUTENBERG_PARENT_ID, 'wp_theme' );
			}
		}

		// bail on error.
		if ( $post_id instanceof WP_Error ) {
			return $post_id;
		}

		// mark the template as saved via abilities.
		update_post_meta( $post_id, Abilities_Settings::MARKER_META, Abilities_Settings::get_instance()->get_marker( $this->get_name(), $type ) );

		// save the CSS, if given.
		if ( ! is_null( $css ) ) {
			Template_Styles::get_instance()->set_css( $type, $css );
		}

		// return the result.
		$result['id'] = $post_id;
		return $result;
	}

	/**
	 * Remove the customized template of the given type and its CSS.
	 *
	 * The template is moved to the trash, so it can be restored.
	 *
	 * @param string $type    The template type.
	 * @param bool   $dry_run True to only return what would happen.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function reset_template( string $type, bool $dry_run ): array|WP_Error {
		// bail if block templates are not available.
		if ( ! $this->can_save() ) {
			return new WP_Error( 'personio_integration_saving_not_supported', $this->get_unavailable_reason() );
		}

		// get the customized template.
		$post    = $this->get_customized_post( $type );
		$has_css = '' !== $this->get_css( $type );

		// prepare the result.
		$result = array(
			'action' => $post instanceof WP_Post || $has_css ? 'reset' : 'none',
			'id'     => $post instanceof WP_Post ? $post->ID : 0,
		);

		// bail on dry run or if nothing is to do.
		if ( $dry_run || 'none' === $result['action'] ) {
			return $result;
		}

		// move the customized template to the trash.
		if ( $post instanceof WP_Post && ! wp_trash_post( $post->ID ) ) {
			return new WP_Error( 'personio_integration_reset_failed', __( 'The customized template could not be removed.', 'personio-integration-light' ) );
		}

		// remove the CSS.
		Template_Styles::get_instance()->set_css( $type, '' );

		// return the result.
		return $result;
	}

	/**
	 * Return whether the given template has been customized in this WordPress.
	 *
	 * @param WP_Block_Template $template The template.
	 *
	 * @return bool
	 */
	private function is_customized( WP_Block_Template $template ): bool {
		return 'custom' === $template->source || absint( $template->wp_id ) > 0;
	}

	/**
	 * Validate the given block template.
	 *
	 * @param string $type    The template type.
	 * @param string $content The block markup.
	 *
	 * @return array<string,array<int,string>>
	 */
	public function validate( string $type, string $content ): array {
		$errors   = array();
		$warnings = array();

		// bail if the content is empty.
		if ( '' === trim( $content ) ) {
			return array(
				'errors'   => array( __( 'The template is empty.', 'personio-integration-light' ) ),
				'warnings' => array(),
			);
		}

		// collect the allowed values.
		$abilities = Template_Abilities::get_instance();
		$allowed   = array(
			'taxonomies'               => $abilities->get_allowed_values( 'taxonomies' ),
			'excerpt_templates'        => $abilities->get_allowed_values( 'excerpt_templates' ),
			'jobdescription_templates' => $abilities->get_allowed_values( 'jobdescription_templates' ),
		);

		// get the available template parts.
		$template_parts = array();
		foreach ( get_block_templates( array(), 'wp_template_part' ) as $template_part ) {
			$template_parts[] = $template_part->slug;
		}

		// check the blocks.
		$used_blocks = array();
		$this->check_blocks( parse_blocks( $content ), $errors, $warnings, $used_blocks, $allowed, $template_parts );

		// check the content for the template type.
		if ( 'single' === $type && empty( preg_grep( '/^' . preg_quote( self::BLOCK_PREFIX, '/' ) . '/', $used_blocks ) ) ) {
			$warnings[] = __( 'The template for the detail view uses no block of this plugin. The data of positions is only shown with the blocks of this plugin, see get-template-catalog.', 'personio-integration-light' );
		}
		if ( 'archive' === $type && ! in_array( self::BLOCK_PREFIX . 'list', $used_blocks, true ) && ! in_array( 'core/query', $used_blocks, true ) ) {
			$warnings[] = __( 'The template for the list of positions contains no block which shows positions (wp-personio-integration/list or core/query).', 'personio-integration-light' );
		}
		if ( ! in_array( 'core/template-part', $used_blocks, true ) ) {
			$warnings[] = __( 'The template contains no template part. Header and footer of the theme will be missing.', 'personio-integration-light' );
		}

		// return the result.
		return array(
			'errors'   => array_values( array_unique( $errors ) ),
			'warnings' => array_values( array_unique( $warnings ) ),
		);
	}

	/**
	 * Check a list of values of an attribute against the allowed values.
	 *
	 * @param mixed             $value      The value of the attribute (list or string).
	 * @param array<int,string> $allowed    The allowed values.
	 * @param string            $block_name The block name.
	 * @param string            $attribute  The attribute name.
	 * @param string            $list_name  The name of the value list in the catalog.
	 * @param array<int,string> $errors     The list of errors.
	 *
	 * @return void
	 */
	private function check_values( mixed $value, array $allowed, string $block_name, string $attribute, string $list_name, array &$errors ): void {
		// bail if no values are allowed (e.g. the list could not be loaded).
		if ( empty( $allowed ) ) {
			return;
		}

		// get the values as list.
		$values = is_array( $value ) ? $value : array( $value );
		foreach ( $values as $single_value ) {
			if ( ! is_scalar( $single_value ) || '' === (string) $single_value ) {
				continue;
			}
			if ( ! in_array( (string) $single_value, $allowed, true ) ) {
				/* translators: %1$s will be replaced by a value, %2$s by an attribute name, %3$s by a block name, %4$s by the name of a list. */
				$errors[] = sprintf( __( 'The value %1$s of the attribute %2$s of the block %3$s is unknown. Use a name from values.%4$s.', 'personio-integration-light' ), (string) $single_value, $attribute, $block_name, $list_name );
			}
		}
	}

	/**
	 * Check the given blocks and their inner blocks.
	 *
	 * @param array<int|string,array<string,mixed>> $blocks         The parsed blocks.
	 * @param array<int,string>                     $errors         The list of errors.
	 * @param array<int,string>                     $warnings       The list of warnings.
	 * @param array<int,string>                     $used_blocks    The list of used block names.
	 * @param array<string,array<int,string>>       $allowed        The allowed values.
	 * @param array<int,string>                     $template_parts The available template parts.
	 *
	 * @return void
	 */
	private function check_blocks( array $blocks, array &$errors, array &$warnings, array &$used_blocks, array $allowed, array $template_parts ): void {
		$registry = WP_Block_Type_Registry::get_instance();

		foreach ( $blocks as $block ) {
			$block_name = isset( $block['blockName'] ) && is_string( $block['blockName'] ) ? $block['blockName'] : '';

			// check content outside of blocks.
			if ( empty( $block_name ) ) {
				$inner_html = isset( $block['innerHTML'] ) && is_string( $block['innerHTML'] ) ? $block['innerHTML'] : '';
				// a block comment, which could not be parsed, e.g. because of invalid JSON in its attributes.
				if ( str_contains( $inner_html, '<!-- wp:' ) ) {
					$errors[] = __( 'The template contains invalid block markup, e.g. a block comment with invalid JSON attributes.', 'personio-integration-light' );
				} elseif ( '' !== trim( wp_strip_all_tags( $inner_html ) ) ) {
					$warnings[] = __( 'The template contains content outside of blocks. It will be shown as classic content.', 'personio-integration-light' );
				}
				continue;
			}
			$used_blocks[] = $block_name;

			// check the block type.
			if ( ! $registry->is_registered( $block_name ) ) {
				/* translators: %1$s will be replaced by the block name. */
				$errors[] = sprintf( __( 'The block %1$s is unknown.', 'personio-integration-light' ), $block_name );
			}

			// check the attributes.
			$attributes = $block['attrs'] ?? array();
			if ( ! is_array( $attributes ) ) {
				/* translators: %1$s will be replaced by the block name. */
				$errors[]   = sprintf( __( 'The attributes of the block %1$s are not valid JSON.', 'personio-integration-light' ), $block_name );
				$attributes = array();
			}

			// check block bindings to post meta, which do not show the data of positions.
			if ( ! empty( $attributes['metadata']['bindings'] ) ) {
				$errors[] = __( 'Block bindings do not show the data of positions. Use the blocks of this plugin instead, see get-template-catalog.', 'personio-integration-light' );
			}

			// check the attributes of our blocks and some core blocks.
			switch ( $block_name ) {
				case self::BLOCK_PREFIX . 'details':
					$this->check_values( $attributes['excerptTemplates'] ?? array(), $allowed['taxonomies'], $block_name, 'excerptTemplates', 'taxonomies', $errors );
					$this->check_values( $attributes['template'] ?? '', $allowed['excerpt_templates'], $block_name, 'template', 'excerpt_templates', $errors );
					break;
				case self::BLOCK_PREFIX . 'list':
				case self::BLOCK_PREFIX . 'show':
					$this->check_values( $attributes['excerptTemplates'] ?? array(), $allowed['taxonomies'], $block_name, 'excerptTemplates', 'taxonomies', $errors );
					$this->check_values( $attributes['groupby'] ?? '', $allowed['taxonomies'], $block_name, 'groupby', 'taxonomies', $errors );
					break;
				case self::BLOCK_PREFIX . 'description':
					$this->check_values( $attributes['template'] ?? '', $allowed['jobdescription_templates'], $block_name, 'template', 'jobdescription_templates', $errors );
					break;
				case self::BLOCK_PREFIX . 'filter-list':
				case self::BLOCK_PREFIX . 'filter-select':
				case self::BLOCK_PREFIX . 'filter-linklist':
					$this->check_values( $attributes['filter'] ?? array(), $allowed['taxonomies'], $block_name, 'filter', 'taxonomies', $errors );
					break;
				case 'core/template-part':
					$slug = isset( $attributes['slug'] ) && is_scalar( $attributes['slug'] ) ? (string) $attributes['slug'] : '';
					if ( ! in_array( $slug, $template_parts, true ) ) {
						/* translators: %1$s will be replaced by the slug. */
						$warnings[] = sprintf( __( 'The template part %1$s does not exist in the active theme.', 'personio-integration-light' ), $slug );
					}
					break;
			}

			// check the inner blocks.
			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$this->check_blocks( $block['innerBlocks'], $errors, $warnings, $used_blocks, $allowed, $template_parts );
			}
		}
	}

	/**
	 * Render the given block template.
	 *
	 * @param string $type    The template type.
	 * @param string $content The block markup.
	 * @param int    $post_id The post-ID of the position (for single templates).
	 *
	 * @return string|WP_Error
	 */
	public function render( string $type, string $content, int $post_id ): string|WP_Error {
		global $post, $wp_query;

		// secure the global state.
		$original_post  = $post;
		$original_query = $wp_query;

		// prepare the global state for the template type.
		if ( 'single' === $type ) {
			$position = get_post( $post_id );
			if ( ! $position instanceof WP_Post ) {
				return new WP_Error( 'personio_integration_unknown_position', __( 'The requested position does not exist.', 'personio-integration-light' ) );
			}
			$wp_query = new WP_Query( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored below.
				array(
					'p'         => $position->ID,
					'post_type' => $position->post_type,
				)
			);
			$post     = $position; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored below.
			setup_postdata( $post );
		} else {
			$wp_query = new WP_Query( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored below.
				array(
					'post_type'      => PersonioPosition::get_instance()->get_name(),
					'post_status'    => 'publish',
					'posts_per_page' => 10,
				)
			);
		}

		// render the blocks, including any direct output.
		$ob_level = ob_get_level();
		try {
			ob_start();
			$html  = do_blocks( $content );
			$html .= (string) ob_get_clean();
		} catch ( Throwable $e ) {
			if ( ob_get_level() > $ob_level ) {
				ob_end_clean();
			}
			$html = new WP_Error( 'personio_integration_render_error', __( 'The template could not be rendered:', 'personio-integration-light' ) . ' ' . $e->getMessage() );
		} finally {
			// restore the global state.
			$wp_query = $original_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restoring the original value.
			$post     = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restoring the original value.
			if ( $original_post instanceof WP_Post ) {
				setup_postdata( $original_post );
			} else {
				wp_reset_postdata();
			}
		}

		// return the result.
		return $html;
	}
}

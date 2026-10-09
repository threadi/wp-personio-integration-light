<?php
/**
 * File for the base of adapters, which make templates of a page builder accessible for abilities.
 *
 * @package personio-integration-light
 */

declare(strict_types=1);

namespace PersonioIntegrationLight\Abilities;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

use WP_Error;

/**
 * Base object for adapters, which make templates of a page builder accessible for abilities (e.g. for AI).
 *
 * Each page builder stores its templates in its own format. An adapter translates between
 * the abilities and this format. Add further adapters via the filter "personio_integration_template_ability_adapters".
 */
abstract class Template_Adapter_Base {
	/**
	 * Return the internal name of the page builder, e.g. "gutenberg".
	 *
	 * @return string
	 */
	abstract public function get_name(): string;

	/**
	 * Return the human-readable name of the page builder.
	 *
	 * @return string
	 */
	abstract public function get_label(): string;

	/**
	 * Return whether the templates of this page builder can be used in this WordPress.
	 *
	 * @return bool
	 */
	abstract public function is_available(): bool;

	/**
	 * Return the reason why the templates of this page builder can not be used.
	 *
	 * @return string
	 */
	public function get_unavailable_reason(): string {
		return '';
	}

	/**
	 * Return the supported template types with their label.
	 *
	 * @return array<string,string>
	 */
	public function get_template_types(): array {
		return array(
			'single'  => __( 'Detail view of a single position', 'personio-integration-light' ),
			'archive' => __( 'List of positions', 'personio-integration-light' ),
		);
	}

	/**
	 * Return a description of the format of templates of this page builder.
	 *
	 * @return string
	 */
	abstract public function get_format(): string;

	/**
	 * Return the elements (blocks, widgets, modules …), which can be used in templates.
	 *
	 * Format per entry:
	 * - name => the internal name of the element.
	 * - title => the human-readable name.
	 * - description => what the element shows.
	 * - attributes => list of attributes with type, description, default and optional enum.
	 * - example => an example how to use it in the template format.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	abstract public function get_elements(): array;

	/**
	 * Return hints for building templates with this page builder.
	 *
	 * @return array<int,string>
	 */
	public function get_hints(): array {
		return array();
	}

	/**
	 * Return the actual template of the given type.
	 *
	 * Format of the result:
	 * - content => the template in the format of the page builder.
	 * - source => where the template comes from, e.g. "plugin" or "custom".
	 * - is_customized => true if the template has been customized in this WordPress.
	 * - is_ability_template => optional, true if the customized template has been saved via abilities (has the marker
	 *   Abilities_Settings::MARKER_META). If omitted, every customized template is handled as saved via abilities.
	 * - id => the ID of the template in the page builder.
	 *
	 * @param string $type The template type, e.g. "single".
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	abstract public function get_template( string $type ): array|WP_Error;

	/**
	 * Validate the given template content.
	 *
	 * Format of the result:
	 * - errors => list of problems, which must be fixed.
	 * - warnings => list of hints, which should be checked.
	 *
	 * @param string $type    The template type, e.g. "single".
	 * @param string $content The template content in the format of the page builder.
	 *
	 * @return array<string,array<int,string>>
	 */
	abstract public function validate( string $type, string $content ): array;

	/**
	 * Render the given template content for the given position and return the resulting HTML.
	 *
	 * @param string $type    The template type, e.g. "single".
	 * @param string $content The template content in the format of the page builder.
	 * @param int    $post_id The post-ID of the position to use (for single templates).
	 *
	 * @return string|WP_Error
	 */
	abstract public function render( string $type, string $content, int $post_id ): string|WP_Error;

	/**
	 * Return whether templates of this page builder can be saved and reset via abilities.
	 *
	 * @return bool
	 */
	public function can_save(): bool {
		return false;
	}

	/**
	 * Return the CSS, which belongs to the template of the given type.
	 *
	 * @param string $type The template type, e.g. "single".
	 *
	 * @return string
	 */
	public function get_css( string $type ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		return '';
	}

	/**
	 * Save the given template (and optional CSS) as customized template of the given type.
	 *
	 * The content is already validated. Format of the result:
	 * - action => "create" or "update".
	 * - id => the ID of the template in the page builder.
	 * - notes => optional list of hints for the user, e.g. about other templates which are affected.
	 * - filtered => optional, true if the content has been changed by a security filter before saving.
	 *
	 * @param string      $type    The template type, e.g. "single".
	 * @param string      $content The template content in the format of the page builder.
	 * @param string|null $css     The CSS for this template, null to keep the actual CSS, "" to remove it.
	 * @param bool        $dry_run True to only return what would happen.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function save_template( string $type, string $content, ?string $css, bool $dry_run ): array|WP_Error { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		return new WP_Error( 'personio_integration_saving_not_supported', __( 'Templates of this page builder cannot be saved via abilities.', 'personio-integration-light' ) );
	}

	/**
	 * Remove the customized template of the given type (and its CSS), so the template of the plugin or theme is used again.
	 *
	 * Format of the result:
	 * - action => "reset" or "none" (if the template was not customized).
	 * - notes => optional list of hints for the user.
	 *
	 * @param string $type    The template type, e.g. "single".
	 * @param bool   $dry_run True to only return what would happen.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function reset_template( string $type, bool $dry_run ): array|WP_Error { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		return new WP_Error( 'personio_integration_saving_not_supported', __( 'Templates of this page builder cannot be reset via abilities.', 'personio-integration-light' ) );
	}
}

<?php
/**
 * File to represent the old helper-object from < 3.0.0.
 *
 * @deprecated since 3.0.0
 * @package personio-integration-light
 */

namespace personioIntegration;

use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Templates;
use PersonioIntegrationLight\Plugin\Transients;

class helper {
	public static function get_current_lang(): string {
		_deprecated_function( __FUNCTION__, '3.0.0', '\PersonioIntegrationLight\PersonioIntegration\Languages::get_instance()->get_current_lang()' );
		return Languages::get_instance()->get_current_lang();
	}
	public static function get_wp_lang(): string {
		_deprecated_function( __FUNCTION__, '3.0.0', '\PersonioIntegrationLight\PersonioIntegration\Languages::get_instance()->get_current_lang()' );
		return Languages::get_instance()->get_current_lang();
	}
	public static function get_taxonomy_name_of_position( $taxonomy, $position ): string {
		_deprecated_function( __FUNCTION__, '3.0.0' );
		if( $position instanceof \PersonioIntegrationLight\PersonioIntegration\Position ) {
			return $position->get_term_by_field( Taxonomies::get_instance()->get_taxonomy_name_by_slug( $taxonomy ), 'name' );
		}
		return '';
	}
	public static function get_taxonomy_name_by_simple_name( $simple_taxonomy_name ): string {
		_deprecated_function( __FUNCTION__, '3.0.0', 'Taxonomies::get_instance()->get_taxonomy_name_by_slug()' );
		return Taxonomies::get_instance()->get_taxonomy_name_by_slug( $simple_taxonomy_name );
	}
	public static function get_taxonomy_label( $taxonomy_name ): array {
		_deprecated_function( __FUNCTION__, '3.0.0', '\PersonioIntegrationLight\PersonioIntegration\Taxonomies::get_instance()->get_taxonomy_label()' );
		return Taxonomies::get_instance()->get_taxonomy_label( $taxonomy_name );
	}
	public static function getTemplate( $template ): string {
		_deprecated_function( __FUNCTION__, '3.0.0', '\PersonioIntegrationLight\Plugin\Templates::get_instance()->get_template()' );
		return Templates::get_instance()->get_template( $template );
	}
	public static function has_template( string $template ): bool {
		_deprecated_function( __FUNCTION__, '3.0.0', '\PersonioIntegrationLight\Plugin\Templates::get_instance()->has_template()' );
		return Templates::get_instance()->has_template( $template );
	}
	public static function get_personio_application_url( $position, $without_application = false ): string {
		_deprecated_function( __FUNCTION__, '3.0.0', 'Position->get_application_url()' );
		if( $position instanceof \PersonioIntegrationLight\PersonioIntegration\Position ) {
			return $position->get_application_url();
		}
		return '';
	}
	public static function is_transient_not_dismissed( $transient ): bool {
		_deprecated_function( __FUNCTION__, '3.0.0', 'Transients::get_instance()->get_transient_by_name()' );
		return Transients::get_instance()->get_transient_by_name( $transient )->is_set();
	}
	public static function get_filter_types(): array {
		_deprecated_function( __FUNCTION__, '3.0.0', '\PersonioIntegrationLight\Helper::get_filter_types()' );
		return \PersonioIntegrationLight\Helper::get_filter_types();
	}
	public static function is_personioUrl_set(): bool {
		_deprecated_function( __FUNCTION__, '3.0.0', '\PersonioIntegrationLight\Helper::is_personio_url_set()' );
		return \PersonioIntegrationLight\Helper::is_personio_url_set();
	}
	public static function is_plugin_active( $plugin ): bool {
		_deprecated_function( __FUNCTION__, '3.0.0', '\PersonioIntegrationLight\Helper::is_plugin_active()' );
		return \PersonioIntegrationLight\Helper::is_plugin_active( $plugin );
	}
}

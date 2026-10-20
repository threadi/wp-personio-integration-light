<?php
/**
 * File to show hints for administrators in the frontend.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Abilities\Template_Abilities;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Compatibilities;
use PersonioIntegrationLight\Plugin\Settings;
use WP_Admin_Bar;
use WP_Error;

/**
 * Object to show hints for administrators in the frontend.
 *
 * Adds the entry "Edit view" in the admin bar on the detail view and the list of objects. It shows which template
 * is used for the actual view, where it could be edited and links to the help in the backend.
 */
class Frontend_Hints {
	/**
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Frontend_Hints
	 */
	private static ?Frontend_Hints $instance = null;

	/**
	 * The path of the template file, which is used for the actual request.
	 *
	 * @var string
	 */
	private string $template_file = '';

	/**
	 * Constructor, not used as this a Singleton object.
	 */
	protected function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Frontend_Hints {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the hints.
	 *
	 * @return void
	 */
	public function init(): void {
		// remember the used template file (as late as possible to get the file which is really used).
		add_filter( 'template_include', array( $this, 'remember_template_file' ), PHP_INT_MAX );

		// add the entry in the admin bar.
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_entry' ), 90 );
	}

	/**
	 * Remember the template file which is used for the actual request.
	 *
	 * @param string $template The path to the template file.
	 *
	 * @return string
	 */
	public function remember_template_file( string $template ): string {
		$this->template_file = $template;
		return $template;
	}

	/**
	 * Return whether the hints should be shown for the actual request.
	 *
	 * @return bool
	 */
	private function is_enabled(): bool {
		// bail in the backend.
		if ( is_admin() ) {
			return false;
		}

		// bail if the hints are disabled.
		if ( 1 !== absint( get_option( 'personio_integration_light_show_frontend_hints', 1 ) ) ) {
			return false;
		}

		// bail if the user is not allowed to change the settings of this plugin.
		if ( ! current_user_can( Settings::get_instance()->get_settings_object()->get_capability() ) ) {
			return false;
		}

		$true = true;
		/**
		 * Filter whether the hints for administrators should be shown in the frontend.
		 *
		 * @since 6.1.0 Available since 6.1.0.
		 * @param bool $true True to show the hints.
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		return (bool) apply_filters( 'personio_integration_light_show_frontend_hints', $true );
	}

	/**
	 * Return the type of the actual view: "single" for the detail view of an object, "archive" for the list of objects
	 * or an empty string for any other view.
	 *
	 * @return string
	 */
	private function get_view_type(): string {
		$post_type = PersonioPosition::get_instance()->get_name();
		if ( is_singular( $post_type ) ) {
			return 'single';
		}
		if ( is_post_type_archive( $post_type ) ) {
			return 'archive';
		}
		return '';
	}

	/**
	 * Return the information about the template which is used for the given view type.
	 *
	 * Format of the result:
	 * - label => description of the used template.
	 * - edit_url => URL to edit the template (empty if it could not be edited in WordPress).
	 *
	 * @param string $type The view type ("single" or "archive").
	 *
	 * @return array<string,string>
	 */
	public function get_template_info( string $type ): array {
		$info = array(
			'label'    => '',
			'edit_url' => '',
		);

		// for block themes: use the block template.
		if ( Helper::theme_is_fse_theme() ) {
			$info = $this->get_block_template_info( $type, $info );
		} else {
			$info = $this->get_template_file_info( $info );
		}

		/**
		 * Filter the information about the template which is used for the actual view of objects.
		 *
		 * Used e.g. by page builders, which provide their own templates for objects.
		 *
		 * @since 6.1.0 Available since 6.1.0.
		 * @param array<string,string> $info The information: "label" describes the used template, "edit_url" is the URL to edit it.
		 * @param string               $type The view type ("single" or "archive").
		 */
		$info = apply_filters( 'personio_integration_light_frontend_template_info', $info, $type );

		// return the result with the expected keys.
		return array(
			'label'    => is_string( $info['label'] ?? null ) ? $info['label'] : '',
			'edit_url' => is_string( $info['edit_url'] ?? null ) ? $info['edit_url'] : '',
		);
	}

	/**
	 * Return the information about the used block template.
	 *
	 * @param string               $type The view type.
	 * @param array<string,string> $info The information.
	 *
	 * @return array<string,string>
	 */
	private function get_block_template_info( string $type, array $info ): array {
		// get the adapter for block templates.
		$adapters = Template_Abilities::get_instance()->get_adapters();
		if ( empty( $adapters['gutenberg'] ) ) {
			return $info;
		}

		// get the template.
		$template = $adapters['gutenberg']->get_template( $type );
		if ( $template instanceof WP_Error ) {
			return $info;
		}

		// set the label depending on its source.
		if ( ! empty( $template['is_customized'] ) ) {
			$info['label'] = __( 'Customized block template', 'personio-integration-light' );
		} elseif ( 'theme' === $template['source'] ) {
			$info['label'] = __( 'Block template of your theme', 'personio-integration-light' );
		} else {
			$info['label'] = __( 'Block template of this plugin', 'personio-integration-light' );
		}

		// set the URL to edit it in the Site Editor.
		$template_id = is_string( $template['id'] ) ? $template['id'] : '';
		if ( ! empty( $template_id ) && current_user_can( 'edit_theme_options' ) ) {
			$info['edit_url'] = $this->get_site_editor_url( $template_id );
		}

		// return the resulting information.
		return $info;
	}

	/**
	 * Return the URL to edit the given block template in the Site Editor.
	 *
	 * @param string $template_id The ID of the template, e.g. "twentytwentyfive//single-position".
	 *
	 * @return string
	 */
	private function get_site_editor_url( string $template_id ): string {
		// since WordPress 6.8 the Site Editor uses paths for its views.
		if ( version_compare( (string) get_bloginfo( 'version' ), '6.8', '>=' ) ) {
			return add_query_arg(
				array(
					'p'      => rawurlencode( '/wp_template/' . $template_id ),
					'canvas' => 'edit',
				),
				admin_url( 'site-editor.php' )
			);
		}

		return add_query_arg(
			array(
				'postType' => 'wp_template',
				'postId'   => rawurlencode( $template_id ),
				'canvas'   => 'edit',
			),
			admin_url( 'site-editor.php' )
		);
	}

	/**
	 * Return the information about the used template file of a classic theme.
	 *
	 * @param array<string,string> $info The information.
	 *
	 * @return array<string,string>
	 */
	private function get_template_file_info( array $info ): array {
		// bail if no template file is known.
		if ( empty( $this->template_file ) ) {
			return $info;
		}

		// get the normalized paths.
		$file             = wp_normalize_path( $this->template_file );
		$plugin_path      = wp_normalize_path( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) );
		$stylesheet_path  = trailingslashit( wp_normalize_path( get_stylesheet_directory() ) );
		$template_path    = trailingslashit( wp_normalize_path( get_template_directory() ) );
		$plugin_subfolder = trailingslashit( basename( dirname( WP_PERSONIO_INTEGRATION_PLUGIN ) ) );

		// check for the template of this plugin.
		if ( str_starts_with( $file, $plugin_path ) ) {
			$info['label'] = __( 'Template of this plugin', 'personio-integration-light' );
			return $info;
		}

		// check for templates in the theme.
		foreach ( array( $stylesheet_path, $template_path ) as $theme_path ) {
			if ( ! str_starts_with( $file, $theme_path ) ) {
				continue;
			}

			// get the relative path of the file in the theme.
			$relative_path = substr( $file, strlen( $theme_path ) );

			// check for a customized template of this plugin in the theme.
			if ( str_starts_with( $relative_path, $plugin_subfolder ) ) {
				/* translators: %1$s will be replaced by the path of the file in the theme. */
				$info['label'] = sprintf( __( 'Customized template in your theme (%1$s)', 'personio-integration-light' ), $relative_path );
				return $info;
			}

			/* translators: %1$s will be replaced by the path of the file in the theme. */
			$info['label'] = sprintf( __( 'Template of your theme (%1$s)', 'personio-integration-light' ), $relative_path );
			return $info;
		}

		// the template comes from another plugin.
		/* translators: %1$s will be replaced by a file name. */
		$info['label'] = sprintf( __( 'Template of another plugin (%1$s)', 'personio-integration-light' ), basename( $file ) );
		return $info;
	}

	/**
	 * Add the entry in the admin bar.
	 *
	 * @param WP_Admin_Bar $admin_bar The admin bar object.
	 *
	 * @return void
	 */
	public function add_admin_bar_entry( WP_Admin_Bar $admin_bar ): void {
		// bail if hints should not be shown.
		if ( ! $this->is_enabled() ) {
			return;
		}

		// bail if this is not a view of objects.
		$type = $this->get_view_type();
		if ( empty( $type ) ) {
			return;
		}

		// get the information about the used template.
		$info = $this->get_template_info( $type );

		// add the main entry.
		$admin_bar->add_node(
			array(
				'id'    => 'pi-edit-view',
				'title' => '<span class="ab-icon dashicons dashicons-admin-appearance" aria-hidden="true" style="top:2px"></span><span class="ab-label">' . esc_html( 'single' === $type ? __( 'Edit position view', 'personio-integration-light' ) : __( 'Edit position list', 'personio-integration-light' ) ) . '</span>',
				'href'  => ! empty( $info['edit_url'] ) ? $info['edit_url'] : '',
			)
		);

		// show the used template.
		if ( ! empty( $info['label'] ) ) {
			$admin_bar->add_node(
				array(
					'id'     => 'pi-edit-view-template',
					'parent' => 'pi-edit-view',
					/* translators: %1$s will be replaced by the description of the used template. */
					'title'  => esc_html( sprintf( __( 'Used template: %1$s', 'personio-integration-light' ), $info['label'] ) ),
				)
			);
		}

		// add the link to edit the template.
		if ( ! empty( $info['edit_url'] ) ) {
			$admin_bar->add_node(
				array(
					'id'     => 'pi-edit-view-edit',
					'parent' => 'pi-edit-view',
					'title'  => esc_html__( 'Edit template', 'personio-integration-light' ),
					'href'   => $info['edit_url'],
				)
			);
		}

		// add the hint for the Pro plugin if a not natively supported page builder is used.
		$page_builders = Compatibilities::get_instance()->get_active_page_builder_labels();
		$false = false;
		/**
		 * Hide hint for Pro-plugin.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false Set true to hide the hint.
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( ! empty( $page_builders ) && ! apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			$admin_bar->add_node(
				array(
					'id'     => 'pi-edit-view-pro',
					'parent' => 'pi-edit-view',
					/* translators: %1$s will be replaced by the names of page builders. */
					'title'  => esc_html( sprintf( __( 'Design with %1$s (Pro)', 'personio-integration-light' ), implode( ', ', $page_builders ) ) ),
					'href'   => Helper::get_pro_url(),
					'meta'   => array( 'target' => '_blank' ),
				)
			);
		}

		// add the link to disable these hints.
		$admin_bar->add_node(
			array(
				'id'     => 'pi-edit-view-disable',
				'parent' => 'pi-edit-view',
				'title'  => esc_html__( 'Hide these hints', 'personio-integration-light' ),
				'href'   => Helper::get_settings_url( 'personioPositions', 'personio_integration_advanced' ),
			)
		);
	}
}

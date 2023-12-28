<?php
/**
 * File to handle template-tasks of this plugin.
 *
 * TODO Verwalten von Liste möglicher Templates (showTitle, showExcert etc.), so dass diese sich dynamisch ändern könnten
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

use App\Helper;

class Templates {
	/**
	 * Instance of this object.
	 *
	 * @var ?Init
	 */
	private static ?Templates $instance = null;

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
	public static function get_instance(): Templates {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize the templates.
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter( 'template_include', array( $this, 'get_cpt_template' ) );

		// TODO richtige Stelle?
		add_filter( 'personio_integration_get_shortcode_attributes', array( $this, 'get_lowercase_attributes' ), 5 );

		// check for changed templates.
		add_action( 'admin_init', array( $this, 'check_child_theme_templates' ) );
	}

	/**
	 * Return possible archive-templates.
	 *
	 * @return array
	 */
	public function get_archive_templates(): array {
		return apply_filters(
			'personio_integration_templates_archive',
			array(
				'default' => __( 'Default', 'personio-integration-light' ),
				'listing' => __( 'Listings', 'personio-integration-light' ),
			)
		);
	}

	/**
	 * Load a template if it exists.
	 *
	 * Also load the requested file if it is located in the /wp-content/themes/xy/personio-integration-light/ directory.
	 *
	 * @param string $template The template to use.
	 * @return string
	 */
	public function get_template( string $template ): string {
		if ( is_embed() ) {
			return $template;
		}

		// check if requested template exist in theme.
		$theme_template = locate_template( trailingslashit( basename( dirname( WP_PERSONIO_INTEGRATION_PLUGIN ) ) ) . $template );
		if ( $theme_template ) {
			return $theme_template;
		}

		// check if requested template exist in plugin which uses our hook.
		$plugin_template = plugin_dir_path( apply_filters( 'personio_integration_set_template_directory', WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'templates/' . $template;
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}

		// return template from light-plugin.
		return plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'templates/' . $template;
	}

	/**
	 * Check if given template exist.
	 *
	 * @param string $template The searched template as to plugins template directory relative path.
	 * @return bool
	 */
	public function has_template( string $template ): bool {
		// check if requested template exist in theme.
		$theme_template = locate_template( trailingslashit( basename( dirname( WP_PERSONIO_INTEGRATION_PLUGIN ) ) ) . $template );
		if ( $theme_template ) {
			return true;
		}

		// check if requested template exist in plugin which uses our hook.
		$plugin_template = plugin_dir_path( apply_filters( 'personio_integration_set_template_directory', WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'templates/' . $template;
		if ( file_exists( $plugin_template ) ) {
			return true;
		}

		// return template from light-plugin.
		return file_exists( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'templates/' . $template );
	}

	/**
	 * Get template for archive or single view.
	 *
	 * @param string $template The requested template.
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function get_cpt_template( string $template ): string {
		if ( WP_PERSONIO_INTEGRATION_CPT === get_post_type( get_the_ID() ) ) {
			// if the theme is a fse-theme.
			if ( Helper::theme_is_fse_theme() ) {
				return ABSPATH . WPINC . '/template-canvas.php';
			}

			// single-view for classic themes.
			if ( is_single() ) {
				return personio_integration_get_single_template( $template );
			}

			// archive-view for classic themes.
			return personio_integration_get_archive_template( $template );
		}
		return $template;
	}

	/**
	 * Get language-specific labels for content templates.
	 *
	 * This also defines the order of the templates in backend and frontend.
	 *
	 * @return array
	 */
	public function get_template_labels(): array {
		return apply_filters(
			'personio_integration_admin_template_labels',
			array(
				'title'    => esc_html__( 'title', 'personio-integration-light' ),
				'excerpt'  => esc_html__( 'details', 'personio-integration-light' ),
				'content'  => esc_html__( 'content', 'personio-integration-light' ),
				'formular' => esc_html__( 'application link', 'personio-integration-light' ),
			)
		);
	}

	/**
	 * Change all attributes zu lowercase
	 *
	 * @param array $values List of shortcode attributes.
	 * @return array
	 * @noinspection PhpUnused
	 */
	function get_lowercase_attributes( array $values ): array {
		// TODO better solution?
		$array = array();
		foreach ( $values['attributes'] as $name => $attribute ) {
			$array[ strtolower( $name ) ] = $attribute;
		}
		return array(
			'defaults'   => $values['defaults'],
			'settings'   => $values['settings'],
			'attributes' => $array,
		);
	}

	/**
	 * Return list of possible templates for job description.
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	function get_jobdescription_templates(): array {
		return apply_filters(
			'personio_integration_templates_jobdescription',
			array(
				'default' => __( 'Default', 'personio-integration-light' ),
				'list'    => __( 'As list', 'personio-integration-light' ),
			)
		);
	}

	/**
	 * Check for changed templates of our own plugin in the child-theme, if one is used.
	 *
	 * TODO testen
	 *
	 * @return void
	 */
	public function check_child_theme_templates(): void {
		// bail if it is not a child-theme.
		if ( ! is_child_theme() ) {
			delete_transient( 'personio_integration_old_templates' );
			return;
		}

		// get path for child-theme-templates-directory and check its existence.
		$path = trailingslashit( get_stylesheet_directory() ) . 'personio-integration-light';
		if ( ! file_exists( $path ) ) {
			delete_transient( 'personio_integration_old_templates' );
			return;
		}

		// get all files from child-theme-templates-directory.
		$files = helper::get_file_from_directory( $path );
		if ( empty( $files ) ) {
			delete_transient( 'personio_integration_old_templates' );
			return;
		}

		// get list of all templates of this plugin.
		$plugin_files = helper::get_file_from_directory( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/templates' );

		// collect warnings.
		$warnings = array();

		// set headers to check.
		$headers = array(
			'version' => 'Version',
		);

		// check the files from child-theme and compare them with our own.
		foreach ( $files as $file ) {
			// check only files wich are exist in our plugin.
			if ( isset( $plugin_files[ basename( $file ) ] ) ) {
				// get the file-version-data.
				$file_data = get_file_data( $file, $headers );
				// only check more if something could be read.
				if ( isset( $file_data['version'] ) ) {
					// if version is not set, show warning.
					if ( empty( $file_data['version'] ) ) {
						$warnings[] = $file;
					}
					elseif ( ! empty( $plugin_files[ basename( $file ) ] ) ) {
						// compare files.
						$plugin_file_data = get_file_data( $plugin_files[ basename( $file ) ], $headers );
						if ( isset( $plugin_file_data['version'] ) ) {
							if ( version_compare( $plugin_file_data['version'], $file_data['version'], '>' ) ) {
								$warnings[] = $file;
							}
						}
					}
				}
			}
		}

		if ( ! empty( $warnings ) ) {
			// generate html-list of the files.
			$html_list = '<ul>';
			foreach ( $warnings as $file ) {
				$html_list .= '<li>' . esc_html( basename( $file ) ) . '</li>';
			}
			$html_list .= '</ul>';

			// show a transient.
			set_transient( 'personio_integration_old_templates', $html_list );
		} else {
			delete_transient( 'personio_integration_old_templates' );
		}
	}

}

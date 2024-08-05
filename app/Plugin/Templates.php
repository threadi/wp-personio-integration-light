<?php
/**
 * File to handle template-tasks of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use WP_Post;

/**
 * Handler for templates.
 */
class Templates {
	/**
	 * Instance of this object.
	 *
	 * @var ?Templates
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
		// check for changed templates.
		add_action( 'admin_init', array( $this, 'check_child_theme_templates' ) );

		// support templates hooks.
		add_filter( 'single_template', array( $this, 'get_single_template' ) );
		add_filter( 'archive_template', array( $this, 'get_archive_template' ) );

		// support content hooks.
		add_filter( 'the_content', array( $this, 'prepare_content_template' ) );
		add_filter( 'the_excerpt', array( $this, 'prepare_excerpt_template' ) );
		add_action( 'the_post', array( $this, 'update_post_object' ) );
		add_filter( 'the_title', array( $this, 'update_post_title' ), 10, 2 );
		add_filter( 'single_post_title', array( $this, 'update_post_title' ), 10, 2 );

		// our own hooks.
		add_action( 'personio_integration_get_title', array( $this, 'get_title_template' ), 10, 2 );
		add_action( 'personio_integration_get_excerpt', array( $this, 'get_excerpt' ), 10, 2 );
		add_action( 'personio_integration_get_content', array( $this, 'get_content_template' ), 10, 2 );
		add_action( 'personio_integration_get_formular', array( $this, 'get_application_link_template' ), 10, 2 );
		add_action( 'personio_integration_get_filter', array( $this, 'get_filter_template' ), 10, 2 );
		add_filter( 'personio_integration_get_shortcode_attributes', array( $this, 'get_lowercase_attributes' ), 5 );
		add_filter( 'personio_integration_get_list_attributes', array( $this, 'filter_attributes_for_templates' ), 10, 2 );

		// expand kses-filter.
		add_filter( 'wp_kses_allowed_html', array( $this, 'add_kses_html' ), 10, 2 );
	}

	/**
	 * Return possible archive-templates.
	 *
	 * @return array
	 */
	public function get_archive_templates(): array {
		$templates = array(
			'default' => __( 'Default', 'personio-integration-light' ),
			'listing' => __( 'Listings', 'personio-integration-light' ),
		);

		/**
		 * Filter the list of available templates for archive listings.
		 *
		 * @since 2.6.0 Available since 2.6.0
		 *
		 * @param array $templates List of templates (filename => label).
		 */
		return apply_filters( 'personio_integration_templates_archive', $templates );
	}

	/**
	 * Return path to a requested template if it exists.
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

		// set the directory for template to use.
		$directory = WP_PERSONIO_INTEGRATION_PLUGIN;

		/**
		 * Set template directory.
		 *
		 * Defaults to our own plugin-directory.
		 *
		 * @since 1.0.0 Available since first release.
		 *
		 * @param string $directory The directory to use.
		 */
		$plugin_template = plugin_dir_path( apply_filters( 'personio_integration_set_template_directory', $directory ) ) . 'templates/' . $template;
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

		// set the directory for template to use.
		$directory = WP_PERSONIO_INTEGRATION_PLUGIN;

		/**
		 * Set template directory.
		 *
		 * Defaults to our own plugin-directory.
		 *
		 * @since 1.0.0 Available since first release.
		 *
		 * @param string $directory The directory to use.
		 */
		$plugin_template = plugin_dir_path( apply_filters( 'personio_integration_set_template_directory', $directory ) ) . 'templates/' . $template;
		if ( file_exists( $plugin_template ) ) {
			return true;
		}

		// return template from light-plugin.
		return file_exists( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'templates/' . $template );
	}

	/**
	 * Get language-specific labels for content templates.
	 *
	 * This also defines the order of the templates in backend and frontend.
	 *
	 * @return array
	 */
	public function get_template_labels(): array {
		$templates = array(
			'title'    => esc_html__( 'Title', 'personio-integration-light' ),
			'excerpt'  => esc_html__( 'Details', 'personio-integration-light' ),
			'content'  => esc_html__( 'Content', 'personio-integration-light' ),
			'formular' => esc_html__( 'Application link', 'personio-integration-light' ),
		);

		/**
		 * Filter the list of available templates for content.
		 *
		 * @since 2.6.0 Available since 2.6.0
		 *
		 * @param array $templates List of templates (filename => label).
		 */
		return apply_filters( 'personio_integration_admin_template_labels', $templates );
	}

	/**
	 * Change all attributes zu lowercase
	 *
	 * @param array $values List of shortcode attributes.
	 * @return array
	 */
	public function get_lowercase_attributes( array $values ): array {
		return array(
			'defaults'   => $values['defaults'],
			'settings'   => $values['settings'],
			'attributes' => array_change_key_case( $values['attributes'] ),
		);
	}

	/**
	 * Return list of possible templates for job description.
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function get_jobdescription_templates(): array {
		$templates = array(
			'default' => __( 'Default', 'personio-integration-light' ),
			'list'    => __( 'As list', 'personio-integration-light' ),
		);

		/**
		 * Filter the list of available templates for job description.
		 *
		 * @since 2.6.0 Available since 2.6.0
		 *
		 * @param array $templates List of templates (filename => label).
		 */
		return apply_filters( 'personio_integration_templates_jobdescription', $templates );
	}

	/**
	 * Return list of possible templates for excerpts.
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function get_excerpts_templates(): array {
		$templates = array(
			'default' => __( 'Default', 'personio-integration-light' ),
			'list'    => __( 'As list', 'personio-integration-light' ),
		);

		/**
		 * Filter the list of available templates for excerpts.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param array $templates List of templates (filename => label).
		 */
		return apply_filters( 'personio_integration_templates_excerpts', $templates );
	}

	/**
	 * Check for changed templates of our own plugin in the child-theme, if one is used.
	 *
	 * @return void
	 */
	public function check_child_theme_templates(): void {
		// bail if it is not a child-theme.
		if ( ! is_child_theme() ) {
			Transients::get_instance()->get_transient_by_name( 'personio_integration_old_templates' )->delete();
			return;
		}

		// get path for child-theme-templates-directory and check its existence.
		$path = trailingslashit( get_stylesheet_directory() ) . 'personio-integration-light/';
		if ( ! file_exists( $path ) ) {
			Transients::get_instance()->get_transient_by_name( 'personio_integration_old_templates' )->delete();
			return;
		}

		// get all files from child-theme-templates-directory.
		$files = Helper::get_files_from_directory( $path );
		if ( empty( $files ) ) {
			Transients::get_instance()->get_transient_by_name( 'personio_integration_old_templates' )->delete();
			return;
		}

		// get list of all templates of this plugin.
		$plugin_files = Helper::get_files_from_directory( Helper::get_plugin_path() . 'templates/' );

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
				// get the file-version-data of the child-template-file.
				$file_data = get_file_data( $file, $headers );
				// only check more if something could be read.
				if ( isset( $file_data['version'] ) ) {
					// if version is not set, show warning.
					if ( empty( $file_data['version'] ) ) {
						$warnings[] = $file;
					} elseif ( ! empty( $plugin_files[ basename( $file ) ] ) ) {
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

		// get transients-object.
		$transients_obj = Transients::get_instance();

		if ( ! empty( $warnings ) ) {
			// generate html-list of the files.
			$html_list = '<ul>';
			foreach ( $warnings as $file ) {
				$html_list .= '<li>' . esc_html( basename( $file ) ) . '</li>';
			}
			$html_list .= '</ul>';

			// show a transient.
			$transient_obj = $transients_obj->add();
			$transient_obj->set_name( 'personio_integration_old_templates' );
			$transient_obj->set_message( __( '<strong>You are using a child theme that contains outdated Personio Integration Light template files.</strong> Please compare the following files in your child-theme with the one this plugin provides:', 'personio-integration-light' ) . $html_list . __( '<strong>Hint:</strong> the version-number in the header of the files must match.', 'personio-integration-light' ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->set_dismissible_days( 60 );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( 'personio_integration_old_templates' )->delete();
		}
	}

	/**
	 * Get the path to the single template.
	 *
	 * @param string $single_template The path to the single template.
	 * @return string
	 */
	public function get_single_template( string $single_template ): string {
		// bail if this is not our cpt.
		if ( PersonioPosition::get_instance()->get_name() !== get_post_type( get_the_ID() ) ) {
			return $single_template;
		}

		$false = false;
		/**
		 * Decide whether to use our own template (false) or not (true).
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param bool $false Return true if our own single template should not be used.
		 * @param string $single_template The single template which will be used instead.
		 */
		if ( apply_filters( 'personio_integration_load_single_template', $false, $single_template ) ) {
			return $single_template;
		}

		// return single template of our own plugin.
		return $this->get_template( 'single-personioposition.php' );
	}

	/**
	 * Get the path to the archive template.
	 *
	 * @param string $archive_template The path to the archive template.
	 * @return string
	 */
	public function get_archive_template( string $archive_template ): string {
		// bail if it is not our post type.
		if ( ! is_post_type_archive( PersonioPosition::get_instance()->get_name() ) ) {
			return $archive_template;
		}

		$false = false;
		/**
		 * Decide whether to use our own archive template (false) or not (true).
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param bool $false Return true if our own archive template should not be used.
		 * @param string $archive_template The archive template which will be used instead.
		 */
		if ( apply_filters( 'personio_integration_load_archive_template', $false, $archive_template ) ) {
			return $archive_template;
		}

		// return our own archive template.
		return $this->get_template( 'archive-' . PersonioPosition::get_instance()->get_name() . '.php' );
	}

	/**
	 * Change output of post_content for the custom post type of this plugin.
	 *
	 * @param string $content The content.
	 *
	 * @return string
	 */
	public function prepare_content_template( string $content ): string {
		// bail if this is not our own cpt.
		if ( PersonioPosition::get_instance()->get_name() !== get_post_type( get_the_ID() ) ) {
			return $content;
		}

		$true = true;
		/**
		 * Filter whether the content template should be used (false) or not (true).
		 *
		 * @param bool $true False if content template should not be used.
		 */
		if ( ! apply_filters( 'personio_integration_show_content', $true ) ) {
			return $content;
		}

		/**
		 * Set arguments to load content of this position via shortcode-function
		 */
		$arguments = array(
			'personioid' => get_post_meta( get_the_ID(), WP_PERSONIO_INTEGRATION_MAIN_CPT_PM_PID, true ),
		);
		return wp_kses_post( PersonioPosition::get_instance()->shortcode_single( $arguments ) );
	}

	/**
	 * Change output of post_content for the custom post type of this plugin.
	 *
	 * @param string $content The content.
	 *
	 * @return string
	 */
	public function prepare_excerpt_template( string $content ): string {
		// bail if this is not our own cpt.
		if ( PersonioPosition::get_instance()->get_name() !== get_post_type( get_the_ID() ) ) {
			return $content;
		}

		// get position as object.
		$position_obj = Positions::get_instance()->get_position( get_the_ID() );

		// return the excerpt-template.
		return $this->get_excerpt( $position_obj, PersonioPosition::get_instance()->get_single_shortcode_attributes( array() ), true );
	}

	/**
	 * Get position title for list.
	 *
	 * @param Position $position The position as object.
	 * @param array    $attributes The attributes.
	 *
	 * @return void
	 * @noinspection PhpUnusedParameterInspection
	 **/
	public function get_title_template( Position $position, array $attributes ): void {
		// set the header-size (h1 for single, h2 for list).
		$heading_size = '2';

		if ( is_single() ) {
			$heading_size = '1';
		}

		// and h3 if list is grouped.
		if ( ! empty( $attributes['groupby'] ) ) {
			$heading_size = '3';
		}

		/**
		 * Filter the heading size.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $heading_size The heading size.
		 * @param Position $position The object ob the requested position.
		 * @param array $attributes List of attributes.
		 */
		$heading_size = apply_filters( 'personio_integration_title_size', $heading_size, $position, $attributes );

		// output for not linked title.
		if ( false !== $attributes['donotlink'] ) {
			include self::get_instance()->get_template( 'parts/part-title.php' );
		} else {
			include self::get_instance()->get_template( 'parts/part-title-linked.php' );
		}
	}

	/**
	 * Get the position details as excerpt via template.
	 *
	 * @param Position $position   The position as object.
	 * @param array    $attributes The attributes.
	 * @param bool     $use_return True if this function should return and not echo for output.
	 *
	 * @return string
	 */
	public function get_excerpt( Position $position, array $attributes, bool $use_return = false ): string {
		// collect the details in this array.
		$details       = array();
		$taxonomy_data = array();

		// get the configured separator.
		$separator = get_option( 'personioIntegrationTemplateExcerptSeparator' ) . ' ';

		// get colon setting.
		$colon = ':';

		// get line break from setting.
		$line_break = '<br>';

		// get the excerpts for this position.
		if ( ! empty( $attributes['excerpt'] ) ) {
			foreach ( $attributes['excerpt'] as $taxonomy_slug ) {
				// get taxonomy name by given slug.
				$taxonomy_name = Taxonomies::get_instance()->get_taxonomy_name_by_slug( $taxonomy_slug );

				// bail if taxonomy could not be found.
				if ( ! $taxonomy_name ) {
					continue;
				}

				// get taxonomy label.
				$taxonomy_label = Taxonomies::get_instance()->get_taxonomy_label( $taxonomy_name, $attributes['lang'] )['name'];

				// get label in for this output configured language.
				$terms_label = Taxonomies::get_instance()->get_default_terms_for_taxonomy( $taxonomy_name, $attributes['lang'] );

				// get terms this position is using on this taxonomy.
				$terms = get_the_terms( $position->get_id(), $taxonomy_name );

				$false = false;
				/**
				 * Filter whether to show terms of single taxonomy as list or not.
				 *
				 * @since 3.0.8 Available since 3.0.8.
				 * @param bool $false True to show the list.
				 * @param array $terms List of terms.
				 */
				$show_term_list = apply_filters( 'personio_integration_show_term_list', $false, $terms );

				// if term exist, get the corresponding term-label.
				if ( ! empty( $terms ) ) {
					$added  = false;
					$values = '';
					foreach ( $terms as $term ) {
						if ( ! empty( $terms_label[ $term->slug ] ) ) {
							$details[ $taxonomy_label ] = $terms_label[ $term->slug ];
							$added                      = true;
						} elseif ( $show_term_list ) {
							if ( ! empty( $values ) ) {
								$values .= $separator;
							}
							$values .= $term->name;
						}
					}

					if ( ! empty( $values ) ) {
						$details[ $taxonomy_label ] = $values;
						$added                      = true;
					}

					// for not translated label.
					if ( ! $added ) {
						$details[ $taxonomy_label ] = $terms[0]->name;
					}
				}
				$taxonomy_data[ $taxonomy_label ] = get_taxonomy( $taxonomy_name );
			}
		}

		if ( ! empty( $details ) ) {
			// get configured template of none has been set for this output.
			if ( empty( $attributes['excerpt_template'] ) ) {
				$template = Settings::get_instance()->get_setting( is_singular() ? 'personioIntegrationTemplateDetailsExcerptsTemplate' : 'personioIntegrationTemplateListingExcerptsTemplate' );
			} else {
				$template = $attributes['excerpt_template'];
			}

			// get template and return it.
			ob_start();
			include $this->get_template( 'parts/details/' . $template . '.php' );
			$content = ob_get_clean();

			// return content depending on setting.
			if ( $use_return ) {
				return $content;
			}
			echo wp_kses_post( $content );
			return '';
		}

		// return nothing.
		return '';
	}

	/**
	 * Get position application-link-button for list.
	 *
	 * @param Position $position The position as object.
	 * @param array    $attributes The attributes.
	 *
	 * @return void
	 */
	public function get_application_link_template( Position $position, array $attributes ): void {
		// bail if we are in admin.
		if ( is_admin() ) {
			return;
		}

		$false = false;
		/**
		 * Bail if no button should be visible.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param bool $false Return true to prevent button-output.
		 */
		if ( apply_filters( 'personio_integration_hide_button', $false ) ) {
			return;
		}

		// convert attributes.
		$attributes = PersonioPosition::get_instance()->get_single_shortcode_attributes( $attributes );

		// define where this application-link is displayed.
		$text_position = 'archive';
		if ( is_single() ) {
			$text_position = 'single';
		}

		// set back to list-link.
		$back_to_list_url = get_option( 'personioIntegrationTemplateBackToListUrl', '' );
		if ( empty( $back_to_list_url ) ) {
			$back_to_list_url = get_post_type_archive_link( PersonioPosition::get_instance()->get_name() );
		}

		// reset back to list-link.
		if ( 0 === absint( get_option( 'personioIntegrationTemplateBackToListButton' ) ) || 'archive' === $text_position || ( isset( $attributes['show_back_to_list'] ) && empty( $attributes['show_back_to_list'] ) ) ) {
			$back_to_list_url = '';
		}

		// generate styling.
		Helper::add_inline_style( $attributes['styles'] );

		// get application URL.
		$link = $position->get_application_url();

		/**
		 * Set and filter the value for the target-attribute.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param Position $position The Position as object.
		 * @param array $attributes List of attributes used for the output.
		 */
		$target = apply_filters( 'personio_integration_back_to_list_target_attribute', '_blank', $position, $attributes );

		// get and output template.
		include $this->get_template( 'parts/properties-application-button.php' );
	}

	/**
	 * Update each post-object with the language-specific texts of a position.
	 *
	 * @param WP_Post $post The post as object.
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function update_post_object( WP_Post $post ): void {
		if ( PersonioPosition::get_instance()->get_name() === $post->post_type ) {
			// get positions object.
			$positions_object = Positions::get_instance();

			// get the position as object.
			$position_object = $positions_object->get_position( get_the_ID() );

			// set language to output language-specific content of the position.
			$position_object->set_lang( Languages::get_instance()->get_main_language() );
		}
	}

	/**
	 * Set position title in actual language.
	 *
	 * Necessary primary for FSE-themes.
	 *
	 * @param string             $post_title The title.
	 * @param int|string|WP_Post $post_id The post ID.
	 *
	 * @return string
	 */
	public function update_post_title( string $post_title, int|string|WP_Post $post_id = 0 ): string {
		// bail if this is not our cpt.
		if ( PersonioPosition::get_instance()->get_name() !== get_post_type( $post_id ) ) {
			return $post_title;
		}

		// get the post id from object.
		if ( $post_id instanceof WP_Post ) {
			$post_id = $post_id->ID;
		}

		// get the position object.
		$position_obj = Positions::get_instance()->get_position( absint( $post_id ) );

		// return resulting title.
		return $position_obj->get_title();
	}

	/**
	 * Show a filter in frontend restricted to positions which are visible in list.
	 *
	 * @param string $filter Name of the filter (taxonomy-slug).
	 * @param array  $attributes List of attributes for the filter.
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function get_filter_template( string $filter, array $attributes ): void {
		$taxonomy_to_use = '';
		$term_ids        = array();

		// get the terms we want to use in filter-output.
		foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
			if ( $filter === $taxonomy['slug'] && 1 === absint( $taxonomy['useInFilter'] ) ) {
				$taxonomy_to_use = $taxonomy_name;
				$terms           = get_terms( array( 'taxonomy' => $taxonomy_name ) );
				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						if ( $term->count > 0 ) {
							$term_ids[] = $term->term_id;
						}
					}
				}
			}
		}

		// show term as filter only if its name is known.
		if ( strlen( $taxonomy_to_use ) > 0 ) {
			// get the terms of this taxonomy.
			$terms = get_terms(
				array(
					'taxonomy' => $taxonomy_to_use,
					'include'  => $term_ids,
				)
			);

			if ( ! empty( $terms ) ) {
				// get the value.
				$value = 0;
				// -> if filter is set by user in frontend.
				if ( ! empty( $GLOBALS['wp']->query_vars['personiofilter'] ) && ! empty( $GLOBALS['wp']->query_vars['personiofilter'][ $filter ] ) ) {
					$value = absint( $GLOBALS['wp']->query_vars['personiofilter'][ $filter ] );
				}

				// get name.
				$filtername = Taxonomies::get_instance()->get_taxonomy_label( $taxonomy_to_use )['name'];

				// get url.
				$page_url = Helper::get_current_url();

				// output of filter.
				include $this->get_template( 'parts/term-filter-' . $attributes['filtertype'] . '.php' );
			}
		}
	}

	/**
	 * Return the content with configured template.
	 *
	 * @param Position $position   The position as object.
	 * @param array    $attributes The attributes used for output the template.
	 * @param bool     $use_return True if this function should return and not echo for output.
	 *
	 * @return string
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function get_content_template( Position $position, array $attributes, bool $use_return = false ): string {
		// use old template if it exists.
		$template_file = 'parts/properties-content.php';

		// if old template does not exist, use the one we configured.
		if ( ! $this->has_template( $template_file ) ) {
			// get configured template if none has been set for this output.
			if ( empty( $attributes['jobdescription_template'] ) ) {
				$template = Settings::get_instance()->get_setting( is_singular() ? 'personioIntegrationTemplateJobDescription' : 'personioIntegrationTemplateListingContentTemplate' );
				if ( ! $this->has_template( $template_file ) ) {
					// set default template if none has been configured (should never happen).
					$template = 'default';
				}
			} else {
				$template = $attributes['jobdescription_template'];
			}
			$template_file = 'parts/jobdescription/' . $template . '.php';
		}

		// get template and return it.
		ob_start();
		include $this->get_template( $template_file );
		$content = ob_get_clean();

		// return content depending on setting.
		if ( $use_return ) {
			return $content;
		}
		echo wp_kses_post( $content );
		return '';
	}

	/**
	 * Extend kses-filter for form-element if our own cpt is called.
	 *
	 * @param array  $allowed_tags List of allowed tags and attributes.
	 * @param string $context The context where this is called.
	 *
	 * @return array
	 */
	public function add_kses_html( array $allowed_tags, string $context ): array {
		$false = false;
		/**
		 * Prevent filtering the HTML-code via kses.
		 * We need this only for the filter-form.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param bool $false False if filter should be run.
		 */
		if ( apply_filters( 'personio_integration_add_kses_filter', $false ) ) {
			return $allowed_tags;
		}

		if ( 'post' === $context ) {
			$allowed_tags['form'] = array(
				'action' => true,
				'method' => true,
				'class'  => true,
				'id'     => true,
			);
		}
		return $allowed_tags;
	}

	/**
	 * Set attributes for output with help of attributes from the used PageBuilder.
	 *
	 * @param array $attributes List of pre-filtered attributes.
	 * @param array $attributes_set_by_pagebuilder List of unfiltered attributes, set by used pagebuilder.
	 *
	 * @return array
	 */
	public function filter_attributes_for_templates( array $attributes, array $attributes_set_by_pagebuilder ): array {
		if ( ! isset( $attributes['lang'] ) ) {
			$attributes['lang'] = Languages::get_instance()->get_current_lang();
		}
		if ( isset( $attributes_set_by_pagebuilder['jobdescription_template'] ) ) {
			$attributes['jobdescription_template'] = $attributes_set_by_pagebuilder['jobdescription_template'];
		}
		if ( ! isset( $attributes['classes'] ) ) {
			$attributes['classes'] = '';
		}
		return $attributes;
	}
}

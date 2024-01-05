<?php
/**
 * File to handle template-tasks of this plugin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

use App\Helper;
use App\PersonioIntegration\Position;
use App\PersonioIntegration\Positions;
use App\PersonioIntegration\PostTypes\PersonioPosition;
use App\PersonioIntegration\Taxonomies;
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
		add_filter( 'template_include', array( $this, 'get_cpt_template' ) );

		// check for changed templates.
		add_action( 'admin_init', array( $this, 'check_child_theme_templates' ) );

		// support templates hooks.
		add_filter( 'single_template', array( $this, 'get_single_template' ) );
		add_filter( 'archive_template', array( $this, 'get_archive_template' ) );

		// support content hooks.
		add_filter( 'the_content', array( $this, 'prepare_content_template' ) );
		add_filter( 'the_excerpt', array( $this, 'prepare_excerpt_template' ) );
		add_filter( 'get_the_excerpt', array( $this, 'prepare_excerpt_template' ) );
		add_action( 'the_post', array( $this, 'update_post_object' ) );
		add_filter( 'the_title', array( $this, 'update_post_title' ), 10, 2 );
		add_filter( 'single_post_title', array( $this, 'update_post_title' ), 10, 2 );

		// our own hooks.
		add_action( 'personio_integration_get_title', array( $this, 'get_title_template' ), 10, 2 );
		add_action( 'personio_integration_get_excerpt', array( $this, 'get_excerpt_template' ), 10, 2 );
		add_action( 'personio_integration_get_content', array( $this, 'get_content_template' ), 10, 2 );
		add_action( 'personio_integration_get_formular', array( $this, 'get_formular_template' ), 10, 2 );
		add_action( 'personio_integration_get_filter', array( $this, 'get_filter_template' ), 10, 2 );
		add_filter( 'personio_integration_get_shortcode_attributes', array( $this, 'get_lowercase_attributes' ), 5 );
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
		return apply_filters( 'personio_integration_templates_archive',	$templates );
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
	 * Get template for archive or single view.
	 *
	 * @param string $template The requested template.
	 * @return string
	 */
	public function get_cpt_template( string $template ): string {
		if ( WP_PERSONIO_INTEGRATION_MAIN_CPT === get_post_type( get_the_ID() ) ) {
			// if the theme is a fse-theme.
			if ( Helper::theme_is_fse_theme() ) {
				return ABSPATH . WPINC . '/template-canvas.php';
			}

			// single-view for classic themes.
			if ( is_single() ) {
				return $this->get_single_template( $template );
			}

			// archive-view for classic themes.
			return $this->get_archive_template( $template );
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
		$templates = array(
			'title'    => esc_html__( 'title', 'personio-integration-light' ),
			'excerpt'  => esc_html__( 'details', 'personio-integration-light' ),
			'content'  => esc_html__( 'content', 'personio-integration-light' ),
			'formular' => esc_html__( 'application link', 'personio-integration-light' ),
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
			'attributes' => array_change_key_case($values['attributes'] ),
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

		if ( ! empty( $warnings ) ) {
			// generate html-list of the files.
			$html_list = '<ul>';
			foreach ( $warnings as $file ) {
				$html_list .= '<li>' . esc_html( basename( $file ) ) . '</li>';
			}
			$html_list .= '</ul>';

			// show a transient.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_no_simplexml' );
			$transient_obj->set_message( __( '<strong>You are using a child theme that contains outdated Personio Integration Light template files.</strong> Please compare the following files in your child-theme with the one this plugin provides:', 'personio-integration-light' ) . $html_list . __( '<strong>Hint:</strong> the version-number in the header of the files must match.', 'personio-integration-light' ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->set_dismissible_days( 60 );
			$transient_obj->save();
		} else {
			Transients::get_instance()->get_transient_by_name( 'personio_integration_old_templates' )->delete();
		}
	}

	/**
	 * Get single template.
	 *
	 * @param string $single_template The template.
	 * @return string
	 */
	public function get_single_template( string $single_template ): string {
		if ( WP_PERSONIO_INTEGRATION_MAIN_CPT === get_post_type( get_the_ID() ) ) {
			$path = $this->get_template( 'single-personioposition.php' );
			if ( file_exists( $path ) ) {
				$single_template = $path;
			}
		}
		return $single_template;
	}

	/**
	 * Get archive template.
	 *
	 * @param string $archive_template The template.
	 * @return string
	 */
	public function get_archive_template( string $archive_template ): string {
		if ( is_post_type_archive( WP_PERSONIO_INTEGRATION_MAIN_CPT ) ) {
			$path = $this->get_template( 'archive-personioposition.php' );
			if ( file_exists( $path ) ) {
				$archive_template = $path;
			}
		}
		return $archive_template;
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
		if ( WP_PERSONIO_INTEGRATION_MAIN_CPT !== get_post_type( get_the_ID() ) ) {
			return $content;
		}

		// get position as object.
		$position_obj = Positions::get_instance()->get_position( get_the_ID() );

		// return the content of the content-template.
		return $this->get_content_template( $position_obj, array(), true );
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
		if ( WP_PERSONIO_INTEGRATION_MAIN_CPT !== get_post_type( get_the_ID() ) ) {
			return $content;
		}

		// get position as object.
		$position_obj = Positions::get_instance()->get_position( get_the_ID() );

		// return the excerpt-template.
		return $this->get_excerpt_template( $position_obj, array() );
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
		if ( ! did_action( 'elementor/loaded' ) && is_single() ) {
			$heading_size = '1';
		}
		// and h3 if list is grouped.
		if ( ! empty( $attributes['groupby'] ) ) {
			$heading_size = '3';
		}

		// output for not linked title.
		if( false !== $attributes['donotlink'] ) {
			include Templates::get_instance()->get_template( 'parts/part-title.php' );
		}
		else {
			include Templates::get_instance()->get_template( 'parts/part-title-linked.php' );
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
	public function get_excerpt_template( Position $position, array $attributes, bool $use_return = false ): string {
		// collect the details in this array
		$details   = array();

		// get the configured separator
		$separator = get_option( 'personioIntegrationTemplateExcerptSeparator', ', ' ) . ' ';

		// get colon setting.
		$colon = ":";

		// get line break from setting.
		$line_break = '<br>';

		// get the excerpts for this position.
		if ( ! empty( $attributes['excerpt'] ) ) {
			foreach ( $attributes['excerpt'] as $taxonomy_slug ) {
				// get taxonomy name by given slug.
				$taxonomy_name = Taxonomies::get_instance()->get_taxonomy_name_by_slug( $taxonomy_slug );

				// get taxonomy label.
				$taxonomy_label = Taxonomies::get_instance()->get_taxonomy_label( $taxonomy_name, $attributes['lang'] )['name'];

				// get label in for this output configured language.
				$terms_label = Taxonomies::get_instance()->get_default_terms_for_taxonomy( $taxonomy_name, $attributes['lang'] );

				// get terms this position is using on this taxonomy.
				$terms = get_the_terms( $position->get_id(), $taxonomy_name );

				// if term exist, get the corresponding term-label.
				if( !empty($terms) ) {
					$added = false;
					foreach ( $terms as $term ) {
						if( !empty($terms_label[ $term->slug ]) ) {
							$details[$taxonomy_label] = $terms_label[ $term->slug ];
							$added = true;
						}
					}

					// for not translated label.
					if( ! $added ) {
						$details[$taxonomy_label] = $terms[0]->name;
					}
				}
			}
		}
		if ( ! empty( $details ) ) {
			// get configured template of none has been set for this output.
			if( empty($attributes['excerpt_template']) ) {
				$template = Settings::get_instance()->get_setting(is_singular() ? 'personioIntegrationTemplateDetailsExcerptsTemplate' : 'personioIntegrationTemplateListingExcerptsTemplate' );
			}
			else {
				$template = $attributes['excerpt_template'];
			}
			$template_file = 'parts/details/'.$template.'.php';

			// get template and return it.
			ob_start();
			include $this->get_template( $template_file );
			$content = ob_get_clean();

			// return content depending on setting.
			if( $use_return ) {
				return $content;
			}
			echo $content;
			return '';
		}

		// return nothing
		return '';
	}

	/**
	 * Get position application-link-button for list.
	 *
	 * @param Position $position The position as object.
	 * @param array    $attributes The attributes.
	 *
	 * @return void
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function get_formular_template( Position $position, array $attributes ): void {
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
			$back_to_list_url = get_post_type_archive_link( WP_PERSONIO_INTEGRATION_MAIN_CPT );
		}

		// reset back to list-link.
		if ( 0 === absint( get_option( 'personioIntegrationTemplateBackToListButton', 0 ) ) || 'archive' === $text_position || ( isset( $attributes['show_back_to_list'] ) && empty( $attributes['show_back_to_list'] ) ) ) {
			$back_to_list_url = '';
		}

		// generate styling.
		$styles = ! empty( $attributes['styles'] ) ? $attributes['styles'] : '';

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
		if ( WP_PERSONIO_INTEGRATION_MAIN_CPT === $post->post_type ) {
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
	 * @param string $post_title The title.
	 * @param int|string|WP_Post $post_id The post ID.
	 *
	 * @return string
	 */
	public function update_post_title( string $post_title, int|string|WP_Post $post_id ): string {
		// change the title only for our own cpt.
		if( WP_PERSONIO_INTEGRATION_MAIN_CPT === get_post_type( $post_id ) ) {
			if( $post_id instanceof WP_Post ) {
				$post_id = $post_id->ID;
			}
			$position_obj = Positions::get_instance()->get_position( absint( $post_id ) );
			return $position_obj->get_title();
		}

		// return the title.
		return $post_title;
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
				// -> if filter is set by editor.
				if ( ! empty( $attributes['office'] ) ) {
					$value = $attributes['office'];
				}
				// -> if filter is set by user in frontend.
				if ( ! empty( $_GET['personiofilter'] ) && ! empty( $_GET['personiofilter'][ $filter ] ) ) {
					$value = absint( wp_unslash( $_GET['personiofilter'][ $filter ] ) );
				}

				// set name.
				$taxonomy   = get_taxonomy( $taxonomy_to_use );
				$filtername = $taxonomy->labels->singular_name;

				// get url.
				$page_url = helper::get_current_url();

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
		if( ! $this->has_template( $template_file ) ) {
			// get configured template if none has been set for this output.
			if( empty($attributes['jobdescription_template']) ) {
				$template = Settings::get_instance()->get_setting(is_singular() ? 'personioIntegrationTemplateJobDescription' : 'personioIntegrationTemplateListingContentTemplate' );
				if( ! $this->has_template( $template_file ) ) {
					// set default template if none has been configured (should never happen).
					$template = 'default';
				}
			}
			else {
				$template = $attributes['jobdescription_template'];
			}
			$template_file = 'parts/jobdescription/'.$template.'.php';
		}

		// get template and return it.
		ob_start();
		include $this->get_template( $template_file );
		$content = ob_get_clean();

		// return content depending on setting.
		if( $use_return ) {
			return $content;
		}
		echo $content;
		return '';
	}
}

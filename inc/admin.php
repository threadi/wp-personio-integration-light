<?php
/**
 * File for functions to run in wp-admin only.
 *
 * @package personio-integration-light
 */

use personioIntegration\helper;
use personioIntegration\Import;
use personioIntegration\Position;
use personioIntegration\Positions;

/**
 * Add own CSS and JS for backend.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_styles_and_js_admin(): void {
	// admin-specific styles.
	wp_enqueue_style(
		'personio_integration-admin-css',
		plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/admin/styles.css',
		array(),
		filemtime( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/admin/styles.css' ),
	);

	// admin- and backend-styles for attribute-type-output.
	wp_enqueue_style(
		'personio_integration-styles',
		plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/css/styles.css',
		array(),
		filemtime( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/css/styles.css' )
	);

	// backend-JS.
	wp_enqueue_script(
		'personio_integration-admin-js',
		plugins_url( '/admin/js.js', WP_PERSONIO_INTEGRATION_PLUGIN ),
		array( 'jquery' ),
		filemtime( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/admin/js.js' ),
		true
	);

	// add php-vars to our js-script.
	wp_localize_script(
		'personio_integration-admin-js',
		'customJsVars',
		array(
			'ajax_url'                => admin_url( 'admin-ajax.php' ),
			'pro_url'                 => helper::get_pro_url(),
			'label_go_pro'            => __( 'Get Personio Integration Pro', 'personio-integration-light' ),
			'dismiss_nonce'           => wp_create_nonce( 'wp-dismiss-notice' ),
			'run_import_nonce'        => wp_create_nonce( 'personio-run-import' ),
			'get_import_nonce'        => wp_create_nonce( 'personio-get-import-info' ),
			'label_reset_sort'        => __( 'Reset sorting', 'personio-integration-light' ),
			'label_run_import'        => __( 'Run import', 'personio-integration-light' ),
			'label_import_is_running' => __( 'Import is running', 'personio-integration-light' ),
			'txt_please_wait'         => __( 'Please wait', 'personio-integration-light' ),
			'txt_import_hint'         => __( 'Performing the import could take a few minutes. If a timeout occurs, a manual import is not possible this way. Then the automatic import should be used.', 'personio-integration-light' ),
			'txt_import_has_been_run' => sprintf(
				/* translators: %1$s is replaced with "string", %2$s is replaced with "string" */
				__(
					'<strong>The import has been manually run.</strong> Please check the list of positions <a href="%1$s">in backend</a> and <a href="%2$s">frontend</a>.',
					'personio-integration-light'
				),
				esc_url(
					add_query_arg(
						array(
							'post_type' => WP_PERSONIO_INTEGRATION_CPT,
						),
						get_admin_url() . 'edit.php'
					)
				),
				get_post_type_archive_link( WP_PERSONIO_INTEGRATION_CPT )
			),
			'label_ok'                => __( 'OK', 'personio-integration-light' ),
		)
	);

	// embed necessary scripts for progressbar.
	if ( ( ! empty( $_GET['post_type'] ) && WP_PERSONIO_INTEGRATION_CPT === $_GET['post_type'] ) || ( ! empty( $_GET['import'] ) && 'personio-integration-importer' === $_GET['import'] ) ) {
		$wp_scripts = wp_scripts();
		wp_enqueue_script( 'jquery-ui-progressbar' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style(
			'personio-jquery-ui-styles',
			'https://code.jquery.com/ui/' . $wp_scripts->registered['jquery-ui-core']->ver . '/themes/smoothness/jquery-ui.min.css',
			false,
			'1.0.0',
			false
		);
	}
}
add_action( 'admin_enqueue_scripts', 'personio_integration_add_styles_and_js_admin', PHP_INT_MAX );

/**
 * Add a dashboard widget to show positions.
 *
 * @noinspection PhpUnused
 */
function personio_integration_add_dashboard_widgets(): void {
	// only if Personio URL is available.
	if ( ! helper::is_personioUrl_set() ) {
		return;
	}

	// add dashboard widget to show the newest imported positions.
	wp_add_dashboard_widget(
		'dashboard_personio_integration_positions',
		__( 'Positions imported from Personio', 'personio-integration-light' ),
		'personio_integration_dashboard_widget_function',
		null,
		array(),
		'side',
		'high'
	);
}
add_action( 'wp_dashboard_setup', 'personio_integration_add_dashboard_widgets', 10 );

/**
 * Output the contents of the dashboard widget
 *
 * @param string $post The post as object.
 * @param array  $callback_args List of arguments.
 *
 * @noinspection PhpUnusedParameterInspection
 */
function personio_integration_dashboard_widget_function( string $post, array $callback_args ): void {
	$positions_obj = Positions::get_instance();
	if ( function_exists( 'personio_integration_set_ordering' ) ) {
		remove_filter( 'pre_get_posts', 'personio_integration_set_ordering' );
	}
	$positions_list = $positions_obj->getPositions(
		3,
		array(
			'sortby' => 'date',
			'sort'   => 'DESC',
		)
	);
	if ( function_exists( 'personio_integration_set_ordering' ) ) {
		add_filter( 'pre_get_posts', 'personio_integration_set_ordering' ); }
	if ( 0 === count( $positions_list ) ) {
		echo '<p>' . esc_html__( 'Actually there are no positions imported from Personio.', 'personio-integration-light' ) . '</p>';
	} else {
		$link = add_query_arg(
			array(
				'post_type' => WP_PERSONIO_INTEGRATION_CPT,
			),
			get_admin_url() . 'edit.php'
		);

		?><ul class="personio_positions">
		<?php
		foreach ( $positions_list as $position ) {
			?>
			<li><a href="<?php echo esc_url( get_permalink( $position->ID ) ); ?>"><?php echo esc_html( $position->getTitle() ); ?></a></li>
									<?php
		}
		?>
		</ul>
		<p><a href="<?php echo esc_url( $link ); ?>">
		<?php
			/* translators: %1$d will be replaced by the count of positions */
			printf( esc_html__( 'Show all %1$d positions', 'personio-integration-light' ), absint( get_option( WP_PERSONIO_OPTION_COUNT, 0 ) ) );
		?>
		</a></p>
		<?php
	}
}

/**
 * Generate transient-based messages in backend.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_notices(): void {
	// show transients.
	foreach ( apply_filters( 'personio_integration_admin_transients', WP_PERSONIO_INTEGRATION_TRANSIENTS ) as $transient => $settings ) {
		if ( false !== get_transient( $transient ) ) {
			// marker to show the transient.
			$show = true;

			// check if this transient is dismissed to some time.
			if ( ! helper::is_transient_not_dismissed( $transient ) ) {
				continue;
			}

			// hide on specific pages.
			$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			if ( isset( $settings['options']['hideOnPages'] ) && in_array( $page, $settings['options']['hideOnPages'], true ) ) {
				$show = false;
			}

			// hide if other transient is also visible.
			if ( isset( $settings['options']['hideIfTransients'] ) ) {
				foreach ( $settings['options']['hideIfTransients'] as $transient_to_check ) {
					if ( false !== get_transient( $transient_to_check ) ) {
						$show = false;
					}
				}
			}

			// hide on settings-tab.
			$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
			if ( isset( $settings['options']['hideOnSettingsTabs'] ) && in_array( $tab, $settings['options']['hideOnSettingsTabs'], true ) ) {
				$show = false;
			}

			// get the translated content.
			$settings['content'] = helper::get_admin_transient_content( $transient );

			// do not show anything on empty content.
			if ( empty( $settings['content'] ) ) {
				$show = false;
			}

			// show it.
			if ( $show ) {
				?>
				<div class="wp-personio-integration-transient updated <?php echo esc_attr( $settings['type'] ); ?>" data-dismissible="<?php echo esc_attr( $transient ); ?>-14">
					<?php echo wp_kses_post( $settings['content'] ); ?>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo esc_html__( 'Dismiss this notice.', 'personio-integration-light' ); ?></span></button>
				</div>
				<?php

				// remove the transient.
				delete_transient( $transient );

				// disable plugin if option is set.
				if ( ! empty( $settings['options']['disable_plugin'] ) ) {
					deactivate_plugins( plugin_basename( WP_PERSONIO_INTEGRATION_PLUGIN ) );
				}
			}
		}
	}
}
add_action( 'admin_notices', 'personio_integration_admin_notices' );

/**
 * Add columns to position-table in backend.
 *
 * @param array $columns List of columns.
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_admin_position_add_column( array $columns ): array {
	// create new column-array.
	$new_columns = array();

	// add column for PersonioId.
	$new_columns['id'] = __( 'PersonioID', 'personio-integration-light' );

	// remove checkbox-column if pro is not active.
	if ( false === Helper::is_plugin_active( 'personio-integration/personio-integration.php' ) ) {
		unset( $columns['cb'] );
	}

	// return results.
	return array_merge( $new_columns, $columns );
}
add_filter( 'manage_' . WP_PERSONIO_INTEGRATION_CPT . '_posts_columns', 'personio_integration_admin_position_add_column', 10 );

/**
 * Add content to the column in the position-table in backend.
 *
 * @param string $column Name of the column.
 * @param int    $post_id The ID of the WP_Post-object.
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_position_column_content( string $column, int $post_id ): void {
	if ( 'id' === $column ) {
		$position = new Position( $post_id );
		echo absint( $position->getPersonioId() );
	}
}
add_action( 'manage_' . WP_PERSONIO_INTEGRATION_CPT . '_posts_custom_column', 'personio_integration_admin_add_position_column_content', 10, 2 );

/**
 * Add link to plugin-settings in plugin-list.
 *
 * @param array $links List of links.
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_setting_link( array $links ): array {
	// build and escape the URL.
	$url = add_query_arg(
		array(
			'page'      => 'personioPositions',
			'post_type' => WP_PERSONIO_INTEGRATION_CPT,
		),
		get_admin_url() . 'edit.php'
	);

	// create the link.
	$settings_link = "<a href='" . esc_url( $url ) . "'>" . __( 'Settings', 'personio-integration-light' ) . '</a>';

	// adds the link to the end of the array.
	$links[] = $settings_link;

	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( WP_PERSONIO_INTEGRATION_PLUGIN ), 'personio_integration_admin_add_setting_link' );

/**
 * Activate transient-based hint if configuration does not contain the necessary URL.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_check_config(): void {
	if ( ! helper::is_personioUrl_set() ) {
		set_transient( 'personio_integration_no_url_set', 1, 60 );
	} elseif ( get_option( 'personioIntegrationPositionCount', 0 ) > 0 ) {
		set_transient( 'personio_integration_limit_hint', 0 );
	}
}
add_action( 'admin_init', 'personio_integration_admin_check_config' );

/**
 * Show hint to review our plugin every 90 days.
 *
 * @return void
 */
function personio_integration_admin_show_review_hint(): void {
	$install_date = absint( get_option( 'personioIntegrationLightInstallDate', 0 ) );
	if ( $install_date > 0 ) {
		if ( time() > strtotime( '+90 days', $install_date ) ) {
			for ( $d = 2;$d < 10;$d++ ) {
				if ( time() > strtotime( '+' . ( $d * 90 ) . ' days', $install_date ) ) {
					delete_option( 'pi-dismissed-' . md5( 'personio_integration_admin_show_review_hint' ) );
				}
			}
			set_transient( 'personio_integration_admin_show_review_hint', 1 );
		} else {
			delete_transient( 'personio_integration_admin_show_review_hint' );
		}
	}
}
add_action( 'admin_init', 'personio_integration_admin_show_review_hint' );

/**
 * Activate transient-based hint if configuration is set but no positions are imported until now.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_check_position_count(): void {
	if ( helper::is_personioUrl_set() && 0 === absint( get_option( 'personioIntegrationPositionCount', 0 ) ) ) {
		set_transient( 'personio_integration_no_position_imported', 1, 60 );
	}
}
add_action( 'admin_init', 'personio_integration_admin_check_position_count' );

/**
 * Remove any bulk actions for our own cpt.
 *
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 */
function personio_integration_admin_remove_bulk_actions(): array {
	return array();
}
add_filter( 'bulk_actions-edit-' . WP_PERSONIO_INTEGRATION_CPT, 'personio_integration_admin_remove_bulk_actions', 10, 0 );

/**
 * Remove all actions except "view" and "edit" for our own cpt.
 *
 * @param array   $actions List of actions.
 * @param WP_Post $post Object of the post.
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_admin_remove_actions( array $actions, WP_Post $post ): array {
	if ( WP_PERSONIO_INTEGRATION_CPT === get_post_type() ) {
		$actions         = array(
			'view' => $actions['view'],
		);
		$actions['edit'] = '<a href="' . esc_url( get_edit_post_link( $post->ID ) ) . '">' . __( 'Edit', 'personio-integration-light' ) . '</a>';
		return $actions;
	}
	return $actions;
}
add_filter( 'post_row_actions', 'personio_integration_admin_remove_actions', 10, 2 );

/**
 * Add filter for our own cpt on lists in admin.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_filter(): void {
	$post_type = ( isset( $_GET['post_type'] ) ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : 'post';

	if ( WP_PERSONIO_INTEGRATION_CPT === $post_type ) {
		// add filter for each taxonomy.
		foreach ( apply_filters( 'personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES ) as $taxonomy_name => $taxonomy ) {
			// show only taxonomies which are visible in filter.
			if ( 1 === absint( $taxonomy['useInFilter'] ) ) {
				// get the taxonomy as object.
				$taxonomy = get_taxonomy( $taxonomy_name );

				// get its terms.
				$terms = get_terms(
					array(
						'taxonomy'   => $taxonomy_name,
						'hide_empty' => false,
					)
				);

				// list terms only if they are available.
				if ( ! empty( $terms ) ) {
					?>
						<!--suppress HtmlFormInputWithoutLabel -->
						<select name="admin_filter_<?php echo esc_attr( $taxonomy_name ); ?>">
							<option value="0"><?php echo esc_html( $taxonomy->label ); ?></option>
							<?php
							foreach ( $terms as $term ) {
								?>
								<option value="<?php echo esc_attr( $term->term_id ); ?>"<?php echo ( isset( $_GET[ 'admin_filter_' . $taxonomy_name ] ) && absint( $_GET[ 'admin_filter_' . $taxonomy_name ] ) === $term->term_id ) ? ' selected="selected"' : ''; ?>><?php echo esc_html( $term->name ); ?></option>
															<?php
							}
							?>
						</select>
					<?php
				}
			}
		}
	}
}
add_action( 'restrict_manage_posts', 'personio_integration_admin_add_filter' );

/**
 * Use filter in admin on edit-page for filtering the cpt-items.
 *
 * @param WP_Query $query The WP_Query-object.
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_use_filter( WP_Query $query ): void {
	global $pagenow;
	$post_type = ( isset( $_GET['post_type'] ) ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : 'post';

	if ( WP_PERSONIO_INTEGRATION_CPT === $post_type && 'edit.php' === $pagenow ) {
		// add filter for each taxonomy.
		$tax_query = array();
		foreach ( apply_filters( 'personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES ) as $taxonomy_name => $taxonomy ) {
			if ( 1 === absint( $taxonomy['useInFilter'] ) ) {
				if ( isset( $_GET[ 'admin_filter_' . $taxonomy_name ] ) && absint( wp_unslash( $_GET[ 'admin_filter_' . $taxonomy_name ] ) ) > 0 ) {
					$tax_query[] = array(
						'taxonomy' => $taxonomy_name,
						'field'    => 'term_id',
						'terms'    => absint( wp_unslash( $_GET[ 'admin_filter_' . $taxonomy_name ] ) ),
					);
				}
			}
		}
		if ( ! empty( $tax_query ) ) {
			if ( count( $tax_query ) > 1 ) {
				$query->set(
					'tax_query',
					array(
						'relation' => 'AND',
						$tax_query,
					)
				);
			} else {
				$query->set( 'tax_query', $tax_query );
			}
		}
	}
}
add_filter( 'parse_query', 'personio_integration_admin_use_filter' );

/**
 * Handles Ajax request to persist notices dismissal.
 * Uses check_ajax_referer to verify nonce.
 *
 * TODO use transients-object instead of this.
 *
 * @return void
 * @noinspection PhpUnused
 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
 */
function personio_integration_admin_dismiss(): void {
	// get values.
	$option_name        = isset( $_POST['option_name'] ) ? sanitize_text_field( wp_unslash( $_POST['option_name'] ) ) : false;
	$dismissible_length = isset( $_POST['dismissible_length'] ) ? sanitize_text_field( wp_unslash( $_POST['dismissible_length'] ) ) : 14;

	if ( 'forever' !== $dismissible_length ) {
		// If $dismissible_length is not an integer default to 14.
		$dismissible_length = ( 0 === absint( $dismissible_length ) ) ? 14 : $dismissible_length;
		$dismissible_length = strtotime( absint( $dismissible_length ) . ' days' );
	}

	// check nonce.
	check_ajax_referer( 'wp-dismiss-notice', 'nonce' );

	// save value.
	update_site_option( 'pi-dismissed-' . md5( $option_name ), $dismissible_length );

	// return nothing.
	wp_die();
}

/**
 * Start Import via AJAX.
 *
 * @return void
 * @noinspection PhpUnused
 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
 */
function personio_integration_admin_run_import(): void {
	// check nonce.
	check_ajax_referer( 'personio-run-import', 'nonce' );

	// run import.
	new Import();

	// return nothing.
	wp_die();
}

/**
 * Return state of the actual running import.
 *
 * @return void
 * @noinspection PhpUnused
 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
 */
function personio_integration_admin_get_import_info(): void {
	// check nonce.
	check_ajax_referer( 'personio-get-import-info', 'nonce' );

	// return actual and max count of import steps.
	echo absint( get_option( WP_PERSONIO_OPTION_COUNT, 0 ) ) . ';' . absint( get_option( WP_PERSONIO_OPTION_MAX ) ) . ';' . absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) );

	// return nothing else.
	wp_die();
}

/**
 * Add AJAX-endpoints.
 */
add_action(
	'admin_init',
	function () {
		add_action( 'wp_ajax_nopriv_dismiss_admin_notice', 'personio_integration_admin_dismiss' );
		add_action( 'wp_ajax_dismiss_admin_notice', 'personio_integration_admin_dismiss' );

		add_action( 'wp_ajax_nopriv_personio_run_import', 'personio_integration_admin_run_import' );
		add_action( 'wp_ajax_personio_run_import', 'personio_integration_admin_run_import' );

		add_action( 'wp_ajax_nopriv_personio_get_import_info', 'personio_integration_admin_get_import_info' );
		add_action( 'wp_ajax_personio_get_import_info', 'personio_integration_admin_get_import_info' );
	}
);

/**
 * Show hint for our Pro-version.
 *
 * @param string $hint The individual hint to show before pro-hint.
 * @return void
 */
function personio_integration_admin_show_pro_hint( string $hint ): void {
	echo '<p class="personio-pro-hint">' . sprintf( wp_kses_post( $hint ), '<a href="' . esc_url( helper::get_pro_url() ) . '" target="_blank">Personio Integration Pro (opens new window)</a>' ) . '</p>';
}
add_action( 'personio_integration_admin_show_pro_hint', 'personio_integration_admin_show_pro_hint', 10, 1 );

/**
 * Add marker for free version on body-element
 *
 * @param string $classes List of classes for body-element in wp-admin.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_body_class_free( string $classes ): string {
	$classes .= ' personio-integration-free';
	if ( ! helper::is_personioUrl_set() ) {
		$classes .= ' personio-integration-url-missing';
	}
	return $classes;
}
add_filter( 'admin_body_class', 'personio_integration_admin_add_body_class_free' );

/**
 * Update slugs on request.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_update_slugs(): void {
	if ( false !== get_transient( 'personio_integration_update_slugs' ) ) {
		flush_rewrite_rules();
		delete_transient( 'personio_integration_update_slugs' );
	}
}
add_action( 'wp', 'personio_integration_update_slugs' );

/**
 * Hide cpt filter-view.
 *
 * @return array
 */
function personio_integration_hide_cpt_filter(): array {
	return array();
}
add_filter( 'views_edit-' . WP_PERSONIO_INTEGRATION_CPT, 'personio_integration_hide_cpt_filter', 10, 0 );

/**
 * Force list-view of our own cpt to ignore author as filter.
 *
 * @param WP_Query $query The WP_Query-object.
 * @return WP_Query
 */
function personio_integration_ignore_author( WP_Query $query ): WP_Query {
	if ( is_admin() && ! empty( $query->query_vars['post_type'] ) && WP_PERSONIO_INTEGRATION_CPT === $query->query_vars['post_type'] ) {
		$query->set( 'author', 0 );
	}
	return $query;
}
add_filter( 'pre_get_posts', 'personio_integration_ignore_author' );

/**
 * Check for changed templates of our own plugin in the child-theme, if one is used.
 *
 * @return void
 */
function personio_integration_check_child_theme_templates(): void {
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
add_action( 'admin_init', 'personio_integration_check_child_theme_templates' );

/**
 * Check for supported PageBuilder and show hint if Pro-version would support it.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_check_for_pagebuilder(): void {
	// bail if our Pro-plugin is active.
	if ( false !== Helper::is_plugin_active( 'personio-integration/personio-integration.php' ) ) {
		delete_transient( 'personio_integration_divi' );
		delete_transient( 'personio_integration_elementor' );
		delete_transient( 'personio_integration_wpbakery' );
		delete_transient( 'personio_integration_beaver' );
		delete_transient( 'personio_integration_siteorigin' );
		delete_transient( 'personio_integration_themify' );
		delete_transient( 'personio_integration_avada' );
		return;
	}

	/**
	 * Check for Divi PageBuilder or Divi Theme.
	 */
	if ( false === Helper::is_plugin_active( 'personio-integration-divi/personio-integration-divi.php' ) && ( Helper::is_plugin_active( 'divi-builder/divi-builder.php' ) || 'Divi' === wp_get_theme()->get( 'Name' ) ) ) {
		set_transient( 'personio_integration_divi', 1 );
	} else {
		delete_transient( 'personio_integration_divi' );
	}

	/**
	 * Check for Elementor.
	 */
	if ( did_action( 'elementor/loaded' ) ) {
		set_transient( 'personio_integration_elementor', 1 );
	} else {
		delete_transient( 'personio_integration_elementor' );
	}

	/**
	 * Check for WPBakery.
	 */
	if ( Helper::is_plugin_active( 'js_composer/js_composer.php' ) ) {
		set_transient( 'personio_integration_wpbakery', 1 );
	} else {
		delete_transient( 'personio_integration_wpbakery' );
	}

	/**
	 * Check for Beaver Builder.
	 */
	if ( class_exists( 'FLBuilder' ) ) {
		set_transient( 'personio_integration_beaver', 1 );
	} else {
		delete_transient( 'personio_integration_beaver' );
	}

	/**
	 * Check for SiteOrigin.
	 */
	if ( Helper::is_plugin_active( 'siteorigin-panels/siteorigin-panels.php' ) ) {
		set_transient( 'personio_integration_siteorigin', 1 );
	} else {
		delete_transient( 'personio_integration_siteorigin' );
	}

	/**
	 * Check for Themify.
	 */
	if ( Helper::is_plugin_active( 'themify-builder/themify-builder.php' ) ) {
		set_transient( 'personio_integration_themify', 1 );
	} else {
		delete_transient( 'personio_integration_themify' );
	}

	/**
	 * Check for Avada.
	 */
	if ( Helper::is_plugin_active( 'fusion-builder/fusion-builder.php' ) ) {
		set_transient( 'personio_integration_avada', 1 );
	} else {
		delete_transient( 'personio_integration_avada' );
	}
}
add_action( 'admin_init', 'personio_integration_admin_check_for_pagebuilder' );

/**
 * Remove supports from our own cpt and change our taxonomies.
 * Goal: edit-page without any generic settings.
 *
 * @return void
 */
function personio_integration_light_admin_remove_cpt_supports(): void {
	// remove title, editor and custom fields.
	remove_post_type_support( WP_PERSONIO_INTEGRATION_CPT, 'title' );
	remove_post_type_support( WP_PERSONIO_INTEGRATION_CPT, 'editor' );
	remove_post_type_support( WP_PERSONIO_INTEGRATION_CPT, 'custom-fields' );

	// remove meta box for slug.
	remove_meta_box( 'slugdiv', WP_PERSONIO_INTEGRATION_CPT, 'normal' );

	// remove taxonomy-meta-boxes.
	foreach ( apply_filters( 'personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES ) as $taxonomy_name => $settings ) {
		$taxonomy              = get_taxonomy( $taxonomy_name );
		$taxonomy->meta_box_cb = false;
		register_taxonomy( $taxonomy_name, WP_PERSONIO_INTEGRATION_CPT, $taxonomy );
	}
}
add_action( 'admin_init', 'personio_integration_light_admin_remove_cpt_supports' );

/**
 * Add Box with hints for editing.
 * Add Open Graph Meta-box für edit-page of positions.
 *
 * @return void
 */
function personio_integration_light_admin_add_meta_boxes_prioritized(): void {
	add_meta_box( 'personio-edit-hints', __( 'Show Personio position data', 'personio-integration-light' ), 'personio_integration_admin_light_personio_meta_box', WP_PERSONIO_INTEGRATION_CPT );
}
add_action( 'add_meta_boxes', 'personio_integration_light_admin_add_meta_boxes_prioritized', 10 );

/**
 * Add Box with hints for editing.
 * Add Open Graph Meta-box für edit-page of positions.
 *
 * @return void
 */
function personio_integration_light_admin_add_meta_boxes(): void {
	add_meta_box( 'personio-position-personio-id', __( 'PersonioID', 'personio-integration-light' ), 'personio_integration_admin_personio_meta_box_personio_id', WP_PERSONIO_INTEGRATION_CPT );
	add_meta_box( 'personio-position-title', __( 'Title', 'personio-integration-light' ), 'personio_integration_admin_personio_meta_box_title', WP_PERSONIO_INTEGRATION_CPT );
	add_meta_box( 'personio-position-text', __( 'Description', 'personio-integration-light' ), 'personio_integration_admin_personio_meta_box_description', WP_PERSONIO_INTEGRATION_CPT );
	foreach ( apply_filters( 'personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES ) as $taxonomy_name => $settings ) {
		$labels = helper::get_taxonomy_label( $taxonomy_name );
		add_meta_box( 'personio-position-taxonomy-' . $taxonomy_name, $labels['name'], 'personio_integration_admin_personio_meta_box_taxonomy', WP_PERSONIO_INTEGRATION_CPT, 'side' );
	}
}
add_action( 'add_meta_boxes', 'personio_integration_light_admin_add_meta_boxes', 30 );

/**
 * Box with hints why editing of Position-data is not allowed.
 *
 * @param WP_Post $post Object of the post.
 * @return void
 */
function personio_integration_admin_light_personio_meta_box( WP_Post $post ): void {
	if ( $post->ID > 0 ) {
		$position = new Position( $post->ID );
		if ( $position->isValid() ) {
			$url = helper::get_personio_login_url();
			/* translators: %1$s will be replaced by the URL for Personio */
			printf( esc_html__( 'At this point we show you the imported data of your open position <i>%1$s</i>. Please edit the job details in your <a href="%2$s" target="_blank">Personio account (opens new window)</a>.', 'personio-integration-light' ), esc_html( $position->getTitle() ), esc_url( $url ) );
		}
	}
}

/**
 * Show personioId in meta box.
 *
 * @param WP_Post $post Object of the post.
 * @return void
 */
function personio_integration_admin_personio_meta_box_personio_id( WP_Post $post ): void {
	$position_obj = Positions::get_instance()->get_position( $post->ID );
	echo wp_kses_post( $position_obj->getPersonioId() );
}

/**
 * Show title of position in meta box.
 *
 * @param WP_Post $post Object of the post.
 * @return void
 */
function personio_integration_admin_personio_meta_box_title( WP_Post $post ): void {
	$position_obj = Positions::get_instance()->get_position( $post->ID );
	echo wp_kses_post( $position_obj->getTitle() );
}

/**
 * Show content of position in meta box.
 *
 * @param WP_Post $post Object of the post.
 * @return void
 */
function personio_integration_admin_personio_meta_box_description( WP_Post $post ): void {
	$position_obj = Positions::get_instance()->get_position( $post->ID );
	echo wp_kses_post( $position_obj->getContent() );
}

/**
 * Show any taxonomy of position in meta box.
 *
 * @param WP_Post $post Object of the post.
 * @param array   $attr The attributes.
 * @return void
 */
function personio_integration_admin_personio_meta_box_taxonomy( WP_Post $post, array $attr ): void {
	$position_obj  = Positions::get_instance()->get_position( $post->ID );
	$taxonomy_name = str_replace( 'personio-position-taxonomy-', '', $attr['id'] );
	$taxonomy_obj  = get_taxonomy( $taxonomy_name );
	$content       = helper::get_taxonomy_name_of_position( $taxonomy_obj->rewrite['slug'], $position_obj );
	if ( empty( $content ) ) {
		echo '<i>' . esc_html__( 'No data', 'personio-integration-light' ) . '</i>';
	} else {
		echo wp_kses_post( $content );
	}
}

/**
 * Remove our CPTs from list of possible post-types in easy-language-plugin.
 *
 * @param array $post_types List of post-types.
 *
 * @return mixed
 */
function personio_integration_admin_remove_easy_language_support( array $post_types ): array {
	if ( ! empty( $post_types[ WP_PERSONIO_INTEGRATION_CPT ] ) ) {
		unset( $post_types[ WP_PERSONIO_INTEGRATION_CPT ] );
	}
	return $post_types;
}
add_filter( 'easy_language_possible_post_types', 'personio_integration_admin_remove_easy_language_support' );

/**
 * Add custom importer for positions under Tools > Import.
 *
 * @return void
 */
function personio_integration_admin_add_importer(): void {
	register_importer(
		'personio-integration-importer',
		__( 'Personio', 'personio-integration-light' ),
		__( 'Import positions from Personio', 'personio-integration-light' ),
		'personio_integration_admin_add_menu_content_importexport'
	);
}
add_action( 'admin_init', 'personio_integration_admin_add_importer' );

/**
 * Through a bug in WordPress we must remove the "create"-option manually.
 *
 * @return void
 */
function personio_integration_admin_disable_create_options(): void {
	global $pagenow, $typenow;

	if ( is_admin() && ! empty( $typenow ) && ! empty( $pagenow ) && 'edit.php' === $pagenow && ! empty( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) && stripos( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'edit.php' ) && stripos( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'post_type=' . $typenow ) && ! stripos( sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'page' ) ) {
		$pagenow = 'edit-' . $typenow . '.php'; // TODO find better solution.
	}
}
add_action( 'admin_menu', 'personio_integration_admin_disable_create_options' );

/**
 * Allow our own capability to save settings.
 */
function personio_integration_admin_allow_save_settings(): void {
	$settings_pages = array(
		'personioIntegrationPositions',
		'personioIntegrationPositionsTemplates',
		'personioIntegrationPositionsImportExport',
		'personioIntegrationPositionsAdvanced',
	);
	foreach ( apply_filters( 'personio_integration_admin_settings_pages', $settings_pages ) as $settings_page ) {
		add_filter(
			'option_page_capability_' . $settings_page,
			function () {
				return 'manage_' . WP_PERSONIO_INTEGRATION_CPT;
			},
			10,
			0
		);
	}
}
add_action( 'admin_init', 'personio_integration_admin_allow_save_settings' );

/**
 * Add custom status-check for running cronjobs of our own plugin.
 * Only if personio-URL is set.
 *
 * @param array $statuses List of tests to run.
 * @return array
 */
function personio_integration_admin_set_site_status_test( array $statuses ): array {
	if ( helper::is_personioUrl_set() ) {
		$statuses['async']['personio_integration_import_cron_checks']     = array(
			'label'    => __( 'Personio Integration Import Cron Check', 'personio-integration-light' ),
			'test'     => rest_url( 'personio/v1/import_cron_checks' ),
			'has_rest' => true,
		);
		$statuses['async']['personio_integration_url_availability_check'] = array(
			'label'    => __( 'Personio Integration URL availability check', 'personio-integration-light' ),
			'test'     => rest_url( 'personio/v1/url_availability_checks' ),
			'has_rest' => true,
		);
	}
	return $statuses;
}
add_filter( 'site_status_tests', 'personio_integration_admin_set_site_status_test' );

/**
 * Create our own schedules via click.
 *
 * @return void
 */
function personio_integration_create_schedules(): void {
	check_ajax_referer( 'wp-personio-integration-create-schedules', 'nonce' );

	// check if import-schedule does already exist.
	helper::set_import_schedule();

	// redirect user.
	wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
}
add_action( 'admin_action_personioPositionsCreateSchedules', 'personio_integration_create_schedules' );

/**
 * Checks for old text domain and triggers warning if it is used.
 *
 * @return void
 */
function personio_integration_admin_check_for_old_text_domain(): void {
	if ( is_textdomain_loaded( 'wp-personio-integration' ) && false === Helper::is_plugin_active( 'personio-integration/personio-integration.php' ) ) {
		set_transient( 'personio_integration_admin_show_text_domain_hint', 1 );
	} else {
		delete_transient( 'personio_integration_admin_show_text_domain_hint' );
	}
}
add_action( 'admin_init', 'personio_integration_admin_check_for_old_text_domain' );

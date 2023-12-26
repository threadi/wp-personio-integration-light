<?php
/**
 * File for handling of all Gutenberg-specifics.
 *
 * @package personio-integration-light
 */

use App\helper;
use personioIntegration\gutenberg\templates;
use personioIntegration\Positions;

/**
 * Gutenberg-Callback to get the content for single position.
 *
 * @param array $attributes List of attributes for this position.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_single( array $attributes ): string {
	// link title?
	$do_not_link = true;
	if ( $attributes['linkTitle'] ) {
		$do_not_link = false;
	}

	// set ID as class.
	$class = personio_integration_get_block_class( $attributes );

	// get block-classes.
	$styles_array          = array();
	$block_html_attributes = '';
	if ( function_exists( 'get_block_wrapper_attributes' ) ) {
		$block_html_attributes = get_block_wrapper_attributes();

		// get styles.
		$styles = helper::get_attribute_value_from_html( 'style', $block_html_attributes );
		if ( ! empty( $styles ) ) {
			$styles_array[] = '.entry.' . $class . ' { ' . $styles . ' }';
		}
	}

	// define attribute-defaults.
	$attribute_defaults = array(
		'templates'  => personio_integration_get_gutenberg_templates( $attributes ),
		'excerpt'    => personio_integration_get_details_array( $attributes ),
		'donotlink'  => $do_not_link,
		'personioid' => $attributes['id'],
		'styles'     => implode( PHP_EOL, $styles_array ),
		'classes'    => $class . ' ' . helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
	);

	// get the output.
	return personio_integration_position_shortcode( apply_filters( 'personio_integration_get_gutenberg_single_attributes', $attribute_defaults ) );
}

/**
 * Get detail-templates from attributes-array.
 *
 * @param array $attributes List of attributes.
 * @return string
 */
function personio_integration_get_details_array( array $attributes ): string {
	if ( ! empty( $attributes['excerptTemplates'] ) ) {
		return implode( ',', $attributes['excerptTemplates'] );
	}
	return '';
}

/**
 * Gutenberg-Callback to get the list of positions.
 *
 * @param array $attributes List of attributes.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_list( array $attributes ): string {
	// collect the configured templates.
	$templates = personio_integration_get_gutenberg_templates( $attributes );

	// set ID as class.
	$class = personio_integration_get_block_class( $attributes );

	// get block-classes.
	$styles_array          = array();
	$block_html_attributes = '';
	if ( function_exists( 'get_block_wrapper_attributes' ) ) {
		$block_html_attributes = get_block_wrapper_attributes();

		// get styles.
		$styles = helper::get_attribute_value_from_html( 'style', $block_html_attributes );
		if ( ! empty( $styles ) ) {
			$styles_array[] = '.' . $class . ' { ' . $styles . ' }';
		}
		if ( ! empty( $attributes['style'] ) && ! empty( $attributes['style']['spacing'] ) && ! empty( $attributes['style']['spacing']['blockGap'] ) ) {
			$value = $attributes['style']['spacing']['blockGap'];
			// convert var-setting to var-style-entity.
			if ( str_contains( $attributes['style']['spacing']['blockGap'], 'var:' ) ) {
				$value = str_replace( '|', '--', $value );
				$value = str_replace( 'var:', '', $value );
				$value = 'var(--wp--' . $value . ')';
			}
			$styles_array[] = 'body .' . $class . ' { margin-bottom: ' . $value . '; }';
		}
	}

	// collect all settings for this block.
	$attribute_defaults = array(
		'templates'         => $templates,
		'excerpt'           => personio_integration_get_details_array( $attributes ),
		'donotlink'         => ! $attributes['linkTitle'],
		'sort'              => $attributes['sort'],
		'sortby'            => $attributes['sortby'],
		'groupby'           => $attributes['groupby'],
		'limit'             => absint( $attributes['limit'] ),
		'filter'            => implode( ',', $attributes['filter'] ),
		'filtertype'        => $attributes['filtertype'],
		'showfilter'        => $attributes['showFilter'],
		'show_back_to_list' => '',
		'styles'            => implode( PHP_EOL, $styles_array ),
		'classes'           => $class . ' ' . helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
		'listing_template'  => $attributes['template'],
	);

	// get the output.
	return personio_integration_positions_shortcode( apply_filters( 'personio_integration_get_gutenberg_list_attributes', $attribute_defaults, $attributes ) );
}

/**
 * Gutenberg-Callback to get the filter as linklist.
 *
 * @param array $attributes List of attributes.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_filter_list( array $attributes ): string {
	// set ID as class.
	$class = personio_integration_get_block_class( $attributes );

	// get block-classes.
	$styles_array          = array();
	$block_html_attributes = '';
	if ( function_exists( 'get_block_wrapper_attributes' ) ) {
		$block_html_attributes = get_block_wrapper_attributes();

		// get styles.
		$styles = helper::get_attribute_value_from_html( 'style', $block_html_attributes );
		if ( ! empty( $styles ) ) {
			$styles_array[] = '.' . $class . ' { ' . $styles . ' }';
		}

		if ( ! empty( $class ) ) {
			if ( ! empty( $attributes['hideResetLink'] ) ) {
				$styles_array[] = '.entry.' . $class . ' .personio-position-filter-reset { display: none }';
			}
			if ( ! empty( $attributes['hideFilterTitle'] ) ) {
				$styles_array[] = '.entry.' . $class . ' legend { display: none }';
			}
			if ( ! empty( $attributes['space_between'] ) ) {
				$styles_array[] = '.entry.' . $class . ' .personio-position-filter-linklist > div { margin-right: ' . $attributes['space_between'] . 'px }';
			}
		}
	}

	// collect all settings for this block.
	$attributes = array(
		'templates'  => '',
		'filter'     => implode( ',', $attributes['filter'] ),
		'filtertype' => 'linklist',
		'showfilter' => true,
		'styles'     => implode( PHP_EOL, $styles_array ),
		'classes'    => $class . ' ' . helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
	);

	// get the output.
	return personio_integration_positions_shortcode( apply_filters( 'personio_integration_get_gutenberg_filter_list_attributes', $attributes ) );
}

/**
 * Gutenberg-Callback to get the filter as select-boxes.
 *
 * @param array $attributes List of attributes.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_filter_select( array $attributes ): string {
	// set ID as class.
	$class = personio_integration_get_block_class( $attributes );

	// get block-classes.
	$styles_array          = array();
	$block_html_attributes = '';
	if ( function_exists( 'get_block_wrapper_attributes' ) ) {
		$block_html_attributes = get_block_wrapper_attributes();

		// get styles.
		$styles = helper::get_attribute_value_from_html( 'style', $block_html_attributes );
		if ( ! empty( $styles ) ) {
			$styles_array[] = '.' . $class . ' { ' . $styles . ' }';
		}

		if ( ! empty( $class ) ) {
			if ( ! empty( $attributes['hideResetLink'] ) ) {
				$styles_array[] = '.entry.' . $class . ' .personio-position-filter-reset { display: none }';
			}
			if ( ! empty( $attributes['hideSubmitButton'] ) ) {
				$styles_array[] = '.entry.' . $class . ' button { display: none }';
			}
			if ( ! empty( $attributes['hideFilterTitle'] ) ) {
				$styles_array[] = '.entry.' . $class . ' legend { display: none }';
			}
		}
	}

	// collect all settings for this block.
	$attributes = array(
		'templates'  => '',
		'filter'     => implode( ',', $attributes['filter'] ),
		'filtertype' => 'select',
		'showfilter' => true,
		'styles'     => implode( PHP_EOL, $styles_array ),
		'classes'    => $class . ' ' . helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
	);

	// get the output.
	return personio_integration_positions_shortcode( apply_filters( 'personio_integration_get_gutenberg_filter_select_attributes', $attributes ) );
}

/**
 * Return the block class depending on its blockId.
 *
 * @param array $attributes List of attributes.
 * @return string
 */
function personio_integration_get_block_class( array $attributes ): string {
	if ( ! empty( $attributes['blockId'] ) ) {
		return 'personio-integration-block-' . $attributes['blockId'];
	}
	return '';
}

/**
 * Return application-button.
 *
 * @param array $attributes List of attributes.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_application_button( array $attributes ): string {
	// get positions object.
	$positions = positions::get_instance();

	// get the position as object.
	// -> is no id is available choose a random one (e.g. for preview in Gutenberg).
	$post_id = get_the_ID();
	if ( empty( $post_id ) ) {
		$position_array = $positions->get_positions( 1 );
		$position       = $position_array[0];
	} else {
		$position = $positions->get_position( $post_id );
	}
	if ( ! $position->isValid() ) {
		return '';
	}

	// set ID as class.
	$class = '';
	if ( ! empty( $attributes['blockId'] ) ) {
		$class = 'personio-integration-block-' . $attributes['blockId'];
	}

	// get block-classes.
	$styles_array          = array();
	$block_html_attributes = '';
	if ( function_exists( 'get_block_wrapper_attributes' ) ) {
		$block_html_attributes = get_block_wrapper_attributes();

		// get styles.
		$styles = helper::get_attribute_value_from_html( 'style', $block_html_attributes );
		if ( ! empty( $styles ) ) {
			$styles_array[] = '.entry.' . $class . ' { ' . $styles . ' }';
		}
	}

	$attributes = array(
		'personioid' => absint( $position->getPersonioId() ),
		'templates'  => array( 'formular' ),
		'styles'     => implode( PHP_EOL, $styles_array ),
		'classes'    => $class . ' ' . helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
	);

	// get the output.
	ob_start();
	do_action( 'personio_integration_get_formular', $position, $attributes );
	return ob_get_clean();
}

/**
 * Register the Gutenberg-Blocks with all necessary settings.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_blocks(): void {
	// include Blocks only if Gutenberg exists and the PersonioURL is set.
	if ( function_exists( 'register_block_type' ) && helper::is_personio_url_set() ) {
		// collect attributes for single block.
		$single_attributes = array(
			'id'                  => array(
				'type'    => 'integer',
				'default' => 0,
			),
			'showTitle'           => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'linkTitle'           => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'showExcerpt'         => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'excerptTemplates'    => array(
				'type'    => 'array',
				'default' => array( 'recruitingCategory', 'schedule', 'office' ),
			),
			'showContent'         => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showApplicationForm' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'blockId'             => array(
				'type' => 'string',
			),
		);
		$single_attributes = apply_filters( 'personio_integration_gutenberg_block_single_attributes', $single_attributes );

		// register single block.
		register_block_type(
			plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/show/',
			array(
				'render_callback' => 'personio_integration_get_single',
				'attributes'      => $single_attributes,
			)
		);

		// collect attributes for list block.
		$list_attributes = array(
			'preview'             => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'showFilter'          => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'filter'              => array(
				'type'    => 'array',
				'default' => array( 'recruitingCategory', 'schedule', 'office' ),
			),
			'filtertype'          => array(
				'type'    => 'string',
				'default' => 'linklist',
			),
			'limit'               => array(
				'type'    => 'integer',
				'default' => 0,
			),
			'template'            => array(
				'type'    => 'string',
				'default' => 'default',
			),
			'sort'                => array(
				'type'    => 'string',
				'default' => 'asc',
			),
			'sortby'              => array(
				'type'    => 'string',
				'default' => 'title',
			),
			'groupby'             => array(
				'type'    => 'string',
				'default' => '',
			),
			'showTitle'           => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'linkTitle'           => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showExcerpt'         => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'excerptTemplates'    => array(
				'type'    => 'array',
				'default' => array( 'recruitingCategory', 'schedule', 'office' ),
			),
			'showContent'         => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'showApplicationForm' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'blockId'             => array(
				'type'    => 'string',
				'default' => '',
			),
		);
		$list_attributes = apply_filters( 'personio_integration_gutenberg_block_list_attributes', $list_attributes );

		// register list block.
		register_block_type(
			plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/list/',
			array(
				'render_callback' => 'personio_integration_get_list',
				'attributes'      => $list_attributes,
			)
		);

		// collect attributes for filter-list block.
		$list_attributes = array(
			'preview'         => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'filter'          => array(
				'type'    => 'array',
				'default' => array( 'recruitingCategory', 'schedule', 'office' ),
			),
			'blockId'         => array(
				'type'    => 'string',
				'default' => '',
			),
			'hideResetLink'   => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'hideFilterTitle' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'space_between'   => array(
				'type'    => 'integer',
				'default' => 0,
			),
		);
		$list_attributes = apply_filters( 'personio_integration_gutenberg_block_filter_list_attributes', $list_attributes );

		// register filter-list block.
		register_block_type(
			plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/filter-list/',
			array(
				'render_callback' => 'personio_integration_get_filter_list',
				'attributes'      => $list_attributes,
			)
		);

		// collect attributes for filter-select block.
		$list_attributes = array(
			'preview'          => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'filter'           => array(
				'type'    => 'array',
				'default' => array( 'recruitingCategory', 'schedule', 'office' ),
			),
			'blockId'          => array(
				'type'    => 'string',
				'default' => '',
			),
			'hideResetLink'    => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'hideSubmitButton' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'hideFilterTitle'  => array(
				'type'    => 'boolean',
				'default' => false,
			),
		);
		$list_attributes = apply_filters( 'personio_integration_gutenberg_block_filter_select_attributes', $list_attributes );

		// register filter-list block.
		register_block_type(
			plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/filter-select/',
			array(
				'render_callback' => 'personio_integration_get_filter_select',
				'attributes'      => $list_attributes,
			)
		);

		// collect attributes for application-button block.
		$list_attributes = array(
			'preview' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'blockId' => array(
				'type'    => 'string',
				'default' => '',
			),
		);
		$list_attributes = apply_filters( 'personio_integration_gutenberg_block_application_button_select_attributes', $list_attributes );

		// register application-button block.
		register_block_type(
			plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/application-button/',
			array(
				'render_callback' => 'personio_integration_get_application_button',
				'attributes'      => $list_attributes,
			)
		);

		// collect attributes for details block.
		$list_attributes = array(
			'preview'          => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'blockId'          => array(
				'type'    => 'string',
				'default' => '',
			),
			'excerptTemplates' => array(
				'type'    => 'array',
				'default' => array( 'recruitingCategory', 'schedule', 'office' ),
			),
			'colon'            => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'wrap'             => array(
				'type'    => 'boolean',
				'default' => true,
			),
		);
		$list_attributes = apply_filters( 'personio_integration_gutenberg_block_detail_attributes', $list_attributes );

		// register details block.
		register_block_type(
			plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/details/',
			array(
				'render_callback' => 'personio_integration_get_details',
				'attributes'      => $list_attributes,
			)
		);

		// collect attributes for description block.
		$list_attributes = array(
			'template' => array(
				'type'    => 'string',
				'default' => 'default',
			),
			'preview'  => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'blockId'  => array(
				'type'    => 'string',
				'default' => '',
			),
		);
		$list_attributes = apply_filters( 'personio_integration_gutenberg_block_description_attributes', $list_attributes );

		// register details block.
		register_block_type(
			plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'blocks/description/',
			array(
				'render_callback' => 'personio_integration_get_description',
				'attributes'      => $list_attributes,
			)
		);

		// register translations.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wp-personio-integration-show-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
			wp_set_script_translations( 'wp-personio-integration-list-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
			wp_set_script_translations( 'wp-personio-integration-filter-list-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
			wp_set_script_translations( 'wp-personio-integration-filter-select-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
			wp_set_script_translations( 'wp-personio-integration-application-button-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
			wp_set_script_translations( 'wp-personio-integration-details-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
			wp_set_script_translations( 'wp-personio-integration-description-editor-script', 'personio-integration-light', trailingslashit( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . 'languages/' );
		}
	}
}
add_action( 'init', 'personio_integration_add_blocks', 10 );

/**
 * Generate template-string from given attributes.
 *
 * @param array $attributes List of attributes.
 * @return string
 */
function personio_integration_get_gutenberg_templates( array $attributes ): string {
	$templates = '';
	if ( $attributes['showTitle'] ) {
		$templates .= ( strlen( $templates ) > 0 ? ',' : '' ) . 'title';
	}
	if ( $attributes['showExcerpt'] ) {
		$templates .= ( strlen( $templates ) > 0 ? ',' : '' ) . 'excerpt';
	}
	if ( $attributes['showContent'] ) {
		$templates .= ( strlen( $templates ) > 0 ? ',' : '' ) . 'content';
	}
	if ( $attributes['showApplicationForm'] ) {
		$templates .= ( strlen( $templates ) > 0 ? ',' : '' ) . 'formular';
	}
	return $templates;
}

/**
 * Initialize the block template handling.
 *
 * @return void
 */
function personio_integration_add_templates(): void {
	$templates_obj = templates::get_instance();
	$templates_obj->init();
}
add_action( 'init', 'personio_integration_add_templates', 10 );

/**
 * Gutenberg-Callback to get the list chosen details of single position.
 *
 * @param array $attributes List of attributes.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_details( array $attributes ): string {
	// get positions object.
	$positions = positions::get_instance();

	// get the position as object.
	// -> is no id is available choose a random one (e.g. for preview in Gutenberg).
	$post_id = get_the_ID();
	if ( empty( $post_id ) ) {
		$position_array = $positions->get_positions( 1 );
		$position       = $position_array[0];
	} else {
		$position = $positions->get_position( $post_id );
	}
	if ( ! $position->isValid() ) {
		return '';
	}

	// get setting for colon.
	$colon = ': ';
	if ( false === $attributes['colon'] ) {
		$colon = '';
	}

	// get setting for line break.
	$line_break = '<br>';
	if ( false === $attributes['wrap'] ) {
		$line_break = '';
	}

	// get content for output.
	ob_start();
	// loop through the chosen details.
	foreach ( $attributes['excerptTemplates'] as $detail ) {
		// get the terms of this taxonomy.
		foreach ( apply_filters( 'personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES ) as $taxonomy_name => $taxonomy ) {
			if ( $detail === $taxonomy['slug'] ) {
				// get value.
				$value = Helper::get_taxonomy_name_of_position( $detail, $position );

				// bail if no value is available.
				if ( empty( $value ) ) {
					continue;
				}

				// get labels of this taxonomy.
				$labels = Helper::get_taxonomy_label( $taxonomy_name );

				// output.
				echo '<p><strong>' . esc_html( $labels['name'] ) . esc_html( $colon ) . '</strong>' . wp_kses_post( $line_break . $value ) . '</p>';
			}
		}
	}
	return ob_get_clean();
}

/**
 * Gutenberg-Callback to get the job description of single position.
 *
 * @param array $attributes List of attributes.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_description( array $attributes ): string {
	// get positions object.
	$positions = positions::get_instance();

	// get the position as object.
	// -> is no id is available choose a random one (e.g. for preview in Gutenberg).
	$post_id = get_the_ID();
	if ( empty( $post_id ) ) {
		$position_array = $positions->get_positions( 1 );
		$position       = $position_array[0];
	} else {
		$position = $positions->get_position( $post_id );
	}
	if ( ! $position->isValid() ) {
		return '';
	}

	// set ID as class.
	$class = '';
	if ( ! empty( $attributes['blockId'] ) ) {
		$class = 'personio-integration-block-' . $attributes['blockId'];
	}

	// get block-classes.
	$styles_array          = array();
	$block_html_attributes = '';
	if ( function_exists( 'get_block_wrapper_attributes' ) ) {
		$block_html_attributes = get_block_wrapper_attributes();

		// get styles.
		$styles = helper::get_attribute_value_from_html( 'style', $block_html_attributes );
		if ( ! empty( $styles ) ) {
			$styles_array[] = '.entry.' . $class . ' { ' . $styles . ' }';
		}
	}

	$attributes = array(
		'personioid'              => absint( $position->getPersonioId() ),
		'jobdescription_template' => empty( $attributes['template'] ) ? get_option( 'personioIntegrationTemplateJobDescription', 'default' ) : $attributes['template'],
		'templates'               => array( 'content' ),
		'styles'                  => implode( PHP_EOL, $styles_array ),
		'classes'                 => $class . ' ' . helper::get_attribute_value_from_html( 'class', $block_html_attributes ),
	);

	// get the output.
	ob_start();
	do_action( 'personio_integration_get_content', $position, $attributes );
	return ob_get_clean();
}

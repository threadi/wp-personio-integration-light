<?php
/**
 * File to handle the archive widget.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Widgets;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use PersonioIntegrationLight\PersonioIntegration\Widget_Base;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Templates;

/**
 * Object to handle the archive widget.
 */
class Archive extends Widget_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'widget_archive';

	/**
	 * Name if the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationWidgetArchiveStatus';

	/**
	 * Name if the setting tab where the setting field is visible.
	 *
	 * @var string
	 */
	protected string $setting_tab = '';

	/**
	 * Path to Block object.
	 *
	 * @var string
	 */
	protected string $gutenberg = '\PersonioIntegrationLight\PageBuilder\Gutenberg\Blocks\Archive';

	/**
	 * Instance of this object.
	 *
	 * @var ?Archive
	 */
	private static ?Archive $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Archive {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Archive', 'wp-personio-integration' );
	}

	/**
	 * Return whether this extension is enabled (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return 1 === absint( get_option( $this->get_settings_field_name() ) );
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return __( 'Show a list of positions.', 'wp-personio-integration' );
	}

	/**
	 * Return the rendered widget.
	 *
	 * @param array<string,mixed> $attributes Attributes to configure the rendering.
	 *
	 * @return string
	 */
	public function render( array $attributes ): string {
		// set pagination settings.
		$pagination = true;
		/**
		 * Set pagination settings.
		 *
		 * @since 1.2.0 Available since 1.2.0.
		 *
		 * @param bool $pagination The pagination setting (true to disable it).
		 *
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		$pagination = apply_filters( 'personio_integration_pagination', $pagination );

		// define the default values for each attribute.
		$attribute_defaults = array(
			'lang'                    => Languages::get_instance()->get_current_lang(),
			'showfilter'              => ( 1 === absint( get_option( 'personioIntegrationEnableFilter' ) ) ),
			'filter'                  => implode( ',', get_option( 'personioIntegrationTemplateFilter' ) ),
			'filtertype'              => get_option( 'personioIntegrationFilterType' ),
			'template'                => '',
			'templates'               => implode( ',', get_option( 'personioIntegrationTemplateContentList' ) ),
			'listing_template'        => get_option( 'personioIntegrationTemplateContentListingTemplate' ),
			'excerpt_template'        => get_option( 'personioIntegrationTemplateListingExcerptsTemplate' ),
			'jobdescription_template' => get_option( 'personioIntegrationTemplateListingContentTemplate' ),
			'excerpt'                 => implode( ',', get_option( 'personioIntegrationTemplateExcerptDefaults' ) ),
			'ids'                     => '',
			'donotlink'               => ( 0 === absint( get_option( 'personioIntegrationEnableLinkInList' ) ) ),
			'sort'                    => 'asc',
			'sortby'                  => 'title',
			'limit'                   => 0,
			'nopagination'            => $pagination,
			'groupby'                 => '',
			'styles'                  => '',
			'classes'                 => $this->get_default_classes(),
			'anchor'                  => '',
			'link_to_anchor'          => '',
		);

		// define the settings for each attribute (array or string).
		$attribute_settings = array(
			'id'                      => 'string',
			'lang'                    => 'string',
			'showfilter'              => 'bool',
			'filter'                  => 'array',
			'template'                => 'string',
			'listing_template'        => 'listing_template',
			'excerpt_template'        => 'excerpt_template',
			'jobdescription_template' => 'jobdescription_template',
			'templates'               => 'array',
			'excerpt'                 => 'array',
			'ids'                     => 'array',
			'donotlink'               => 'bool',
			'sort'                    => 'string',
			'sortby'                  => 'string',
			'limit'                   => 'unsignedint',
			'filtertype'              => 'string',
			'nopagination'            => 'bool',
			'groupby'                 => 'string',
			'styles'                  => 'string',
			'classes'                 => 'string',
			'anchor'                  => 'string',
			'link_to_anchor'          => 'string',
		);

		// add taxonomies which are available as filter.
		foreach ( Taxonomies::get_instance()->get_taxonomies() as $taxonomy_name => $taxonomy ) {
			// bail if no slug is set for this taxonomy.
			if ( empty( $taxonomy['slug'] ) ) {
				continue;
			}
			if ( ! empty( $GLOBALS['wp']->query_vars['personiofilter'] ) && ! empty( $GLOBALS['wp']->query_vars['personiofilter'][ $taxonomy['slug'] ] ) && ( 1 === absint( $taxonomy['useInFilter'] ) ) ) {
				$attribute_defaults[ $taxonomy['slug'] ] = 0;
				$attribute_settings[ $taxonomy['slug'] ] = 'filter';
			}
		}

		// get the attributes to filter.
		$personio_attributes = Helper::get_shortcode_attributes( $attribute_defaults, $attribute_settings, $attributes );

		// get positions-object for search.
		$positions_obj = Positions::get_instance();

		// filter for specific ids.
		if ( ! empty( $personio_attributes['ids'][0] ) ) {
			// convert id-list from PersonioId in post_id.
			$resulting_list = array();
			foreach ( $personio_attributes['ids'] as $personio_id ) {
				$position = $positions_obj->get_position_by_personio_id( $personio_id );
				if ( $position instanceof Position ) {
					$resulting_list[] = $position->get_id();
				}
			}
			$personio_attributes['ids'] = $resulting_list;
		}

		// set limits.
		$limit_by_wp   = absint( get_option( 'posts_per_page' ) );
		$limit_by_list = absint( $personio_attributes['limit'] );

		/**
		 * Change the limit for positions in frontend.
		 *
		 * @since 2.0.0 Available since 2.0.0.
		 *
		 * @param int $limit_by_wp The limit define by wp which will be used for the list.
		 * @param int $limit_by_list The limit explicit set for this listing.
		 */
		$personio_attributes['limit'] = apply_filters( 'personio_integration_limit', $limit_by_wp, $limit_by_list );

		// get the positions.
		$positions                         = $positions_obj->get_positions( $personio_attributes['limit'], $personio_attributes );
		$GLOBALS['personio_query_results'] = $positions_obj->get_results();

		/**
		 * Change settings for output.
		 *
		 * @since 1.2.0 Available since 1.2.0.
		 *
		 * @param array $personio_attributes The attributes used for this output.
		 * @param array $attribute_defaults The default attributes.
		 */
		$personio_attributes = apply_filters( 'personio_integration_get_template', $personio_attributes, $attribute_defaults );

		/**
		 * Run custom actions before the output of the archive listing.
		 *
		 * @since 3.2.0 Available since 3.2.0.
		 * @param array $personio_attributes List of attributes.
		 */
		do_action( 'personio_integration_get_template_before', $personio_attributes );

		// generate styling.
		Helper::add_inline_style( $personio_attributes['styles'] );

		// for backwards compatibility.
		$form_id = '';

		// set the group-title.
		$group_title = '';

		// get pagination.
		$query      = array(
			'base'    => str_replace( (string) PHP_INT_MAX, '%#%', esc_url( get_pagenum_link( PHP_INT_MAX ) ) ),
			'format'  => '?paged=%#%',
			'current' => max( 1, get_query_var( 'paged' ) ),
			'total'   => $positions_obj->get_results()->max_num_pages,
		);
		$pagination = paginate_links( $query );
		if ( is_null( $pagination ) ) {
			$pagination = '';
		}

		// collect the output.
		ob_start();
		if ( ! empty( $personio_attributes['styles'] ) && Helper::is_rest_request() ) {
			wp_styles()->print_inline_style( 'wp-block-library' );
		}

		// embed filter.
		require Templates::get_instance()->get_template( 'parts/part-filter.php' );

		// embed the listing content.
		include Templates::get_instance()->get_template( 'parts/listing.php' );

		// get the content.
		$content = ob_get_clean();

		// return the content.
		if ( ! $content ) {
			return '';
		}
		return $content;
	}

	/**
	 * Return list of default classes depending on main settings.
	 *
	 * @return string
	 */
	private function get_default_classes(): string {
		// initiate the list of classes.
		$css_classes = array();

		// add hide title.
		if ( 1 === absint( get_option( 'personioIntegrationHideFilterTitle' ) ) ) {
			$css_classes[] = 'personio-hide-title';
		}

		// add hide reset.
		if ( 1 === absint( get_option( 'personioIntegrationHideFilterReset' ) ) ) {
			$css_classes[] = 'personio-hide-reset';
		}

		/**
		 * Filter the default classes for each output of positions.
		 *
		 * @since 4.2.0 Available since 4.2.0
		 * @param array<int,string> $css_classes List of classes.
		 */
		$css_classes = apply_filters( 'personio_integration_light_default_css_classes', $css_classes );

		// return the resulting list.
		return implode( ' ', $css_classes );
	}

	/**
	 * Return the list of params this widget requires.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function get_params(): array {
		// get the possible field values.
		$values = array();
		foreach ( PersonioPosition::get_instance()->get_archive_templates_via_rest_api() as $template ) {
			$values[] = $template['value'];
		}
		$list = ' <code data-copied-label="' . esc_attr__( 'copied', 'wp-personio-integration' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'wp-personio-integration' ) . '">' . implode( '</code>, <code>', $values ) . '</code>';

		// sort values.
		$sort = array(
			'asc',
			'desc'
		);
		$sort_list = ' <code data-copied-label="' . esc_attr__( 'copied', 'wp-personio-integration' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'wp-personio-integration' ) . '">' . implode( '</code>, <code>', $sort ) . '</code>';

		// sort by values.
		$sortby = array(
			'title',
			'date'
		);
		$sortby_list = ' <code data-copied-label="' . esc_attr__( 'copied', 'wp-personio-integration' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'wp-personio-integration' ) . '">' . implode( '</code>, <code>', $sort ) . '</code>';

		// get the taxonomies.
		$taxonomies = array();
		foreach( Taxonomies::get_instance()->get_taxonomies() as $settings ) {
			// bail if it is not used for filter.
			if( empty( $settings['useInFilter'] ) ) {
				continue;
			}

			// add to the list.
			$taxonomies[] = $settings['slug'];
		}
		$groupby_list = ' <code data-copied-label="' . esc_attr__( 'copied', 'personio-integration-light' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'personio-integration-light' ) . '">' . implode( '</code>, <code>', $taxonomies ) . '</code>';

		// get the possible field values.
		$excerpts = array();
		foreach ( Templates::get_instance()->get_excerpts_templates() as $key => $value ) {
			$excerpts[] = $key;
		}
		$excerpt_list = ' <code data-copied-label="' . esc_attr__( 'copied', 'wp-personio-integration' ) . '" title="' . esc_attr__( 'Click to copy this code in your clipboard', 'wp-personio-integration' ) . '">' . implode( '</code>, <code>', $excerpts ) . '</code>';

		// return the list of params for this widget.
		return array(
			'template' => array(
				'label'         => __( 'Name of chosen template, one of these values:', 'personio-integration-light' ) . $list,
				'example_value' => $values[0],
				'required'      => false,
			),
			'limit' => array(
				'label'         => __( 'Amount of entries in the list. "0" for unlimited.', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'sort' => array(
				'label'         => __( 'Sort direction, one of these values:', 'personio-integration-light' ) . $sort_list,
				'example_value' => $sort[0],
				'required'      => false,
			),
			'sortby' => array(
				'label'         => __( 'Sort by, one of these values:', 'personio-integration-light' ) . $sortby_list,
				'example_value' => $sortby[0],
				'required'      => false,
			),
			'groupby' => array(
				'label'         => __( 'Group by, one of these values:', 'personio-integration-light' ) . $groupby_list,
				'example_value' => $groupby_list[0],
				'required'      => false,
			),
			'showTitle' => array(
				'label'         => __( 'Show title', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'linkTitle' => array(
				'label'         => __( 'Link title', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'showExcerpt' => array(
				'label'         => __( 'Show excerpt', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'excerptTemplates' => array(
				'label'         => __( 'Choose details, any of these values:', 'personio-integration-light' ) . $excerpt_list,
				'example_value' => $excerpts[0],
				'required'      => false,
			),
			'showContent' => array(
				'label'         => __( 'View content', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
			'showApplicationForm' => array(
				'label'         => __( 'View option to apply', 'personio-integration-light' ),
				'example_value' => 1,
				'required'      => false,
			),
		);
	}
}

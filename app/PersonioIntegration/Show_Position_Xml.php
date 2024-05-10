<?php
/**
 * File to handle saving and display of Personio XML for single position.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Settings;
use SimpleXMLElement;
use WP_Post;

/**
 * Object to handle availability-checks for positions.
 */
class Show_Position_Xml extends Extensions_Base {
	/**
	 * The internal name of this extension.
	 *
	 * @var string
	 */
	protected string $name = 'show_position_xml';

	/**
	 * Name of the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationEnableShowPositionXmlStatus';

	/**
	 * Internal name of the used category.
	 *
	 * @var string
	 */
	protected string $extension_category = 'positions';

	/**
	 * Initialize this plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		// bail if extension is not enabled.
		if ( ! $this->is_enabled() && ! defined( 'PERSONIO_INTEGRATION_UPDATE_RUNNING' ) && ! defined( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING' ) ) {
			return;
		}

		// add the meta box.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		// use our own hooks.
		add_filter( 'personio_integration_import_single_position_xml', array( $this, 'add_xml_to_position_object_on_import' ), 10, 2 );
		add_action( 'personio_integration_import_single_position_save', array( $this, 'save_xml_on_position' ) );
	}

	/**
	 * Add Box with hints for editing.
	 * Add Open Graph Meta-box für edit-page of positions.
	 *
	 * @return void
	 */
	public function add_meta_box(): void {
		add_meta_box(
			PersonioPosition::get_instance()->get_name() . '-show-xml',
			__( 'XML from Personio for this position', 'personio-integration-light' ),
			array( $this, 'show_xml' ),
			PersonioPosition::get_instance()->get_name(),
			'advanced',
			'low'
		);
	}

	/**
	 * Show the XML.
	 *
	 * @param WP_Post $post The called post object.
	 *
	 * @return void
	 */
	public function show_xml( WP_Post $post ): void {
		$position_obj = Positions::get_instance()->get_position( $post->ID );
		$xml          = $this->get_extension( $position_obj )->get_xml();

		// bail if no xml is given.
		if ( empty( $xml ) ) {
			echo esc_html__( 'No XML saved. Try to re-import your positions.', 'personio-integration-light' );
			return;
		}

		// show the xml.
		echo '<code>' . wp_kses_post( nl2br( htmlentities( $xml ) ) ) . '</code>';
	}

	/**
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Show Position XML', 'personio-integration-light' );
	}

	/**
	 * Return whether this extension is enabled (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return 1 === absint( Settings::get_instance()->get_setting( $this->get_settings_field_name() ) );
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		/* translators: %1$s will be replaced by the URL for the positions list. */
		return sprintf( __( 'Show the last used XML from Personio for a single position on their edit page. You find the edit pages in the <a href="%1$s">list of positions</a>.', 'personio-integration-light' ), esc_url( PersonioPosition::get_instance()->get_link() ) );
	}

	/**
	 * Whether this extension is enabled by default (true) or not (false).
	 *
	 * @return bool
	 */
	protected function is_default_enabled(): bool {
		return false;
	}

	/**
	 * Remove inline styles on job description during import, if enabled.
	 *
	 * @param Position              $position_obj The Position object we want to change.
	 * @param SimpleXMLElement|null $position The XML-object.
	 *
	 * @return Position
	 */
	public function add_xml_to_position_object_on_import( Position $position_obj, ?SimpleXMLElement $position ): Position {
		// set the xml on position, convert from object to xml.
		$this->get_extension( $position_obj )->set_xml( $position_obj, $position->asXML() );

		// return resulting object.
		return $position_obj;
	}

	/**
	 * Get the extension for the position-object itself.
	 *
	 * @param Position $position_obj The object of the position.
	 *
	 * @return Position_Extensions_Base
	 */
	private function get_extension( Position $position_obj ): Position_Extensions_Base {
		return $position_obj->get_extension( 'PersonioIntegrationLight\PersonioIntegration\Extensions\Show_Xml' );
	}

	/**
	 * Save the xml on position via extension.
	 *
	 * @param Position $position_obj The object of the position.
	 *
	 * @return void
	 */
	public function save_xml_on_position( Position $position_obj ): void {
		$this->get_extension( $position_obj )->save( $position_obj );
	}
}

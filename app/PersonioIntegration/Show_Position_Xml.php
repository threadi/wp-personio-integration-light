<?php
/**
 * File to handle saving and display of Personio XML for single position.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Extensions\Show_Xml;
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
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Show_Position_Xml
	 */
	private static ?Show_Position_Xml $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Show_Position_Xml {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this extension.
	 *
	 * @return void
	 */
	public function init(): void {
		// bail if extension is not enabled.
		if ( ! defined( 'PERSONIO_INTEGRATION_UPDATE_RUNNING' ) && ! defined( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING' ) && ! $this->is_enabled() ) {
			return;
		}

		// add the meta box.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		// use our own hooks.
		add_filter( 'personio_integration_import_single_position_xml', array( $this, 'add_xml_to_position_object_on_import' ), 10, 2 );
	}

	/**
	 * Add box to show the XML-code.
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
	 * Show the XML in meta box.
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
		return 1 === absint( get_option( $this->get_settings_field_name() ) );
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
	 * Remove inline styles on job description during import, if enabled.
	 *
	 * @param Position         $position_obj The Position object we want to change.
	 * @param SimpleXMLElement $xml_object The XML-object.
	 *
	 * @return Position
	 */
	public function add_xml_to_position_object_on_import( Position $position_obj, SimpleXMLElement $xml_object ): Position {
		// get the XML-code from the object.
		$xml = $xml_object->asXML();

		// bail if no XML code given.
		if ( ! is_string( $xml ) ) {
			return $position_obj;
		}

		// set the xml on position, convert from object to xml.
		$this->get_extension( $position_obj )->set_xml( $xml );

		// return resulting object.
		return $position_obj;
	}

	/**
	 * Get the extension for the position-object itself.
	 *
	 * @param Position $position_obj The object of the position.
	 *
	 * @return Show_Xml
	 */
	private function get_extension( Position $position_obj ): Show_Xml {
		return new Show_Xml( $position_obj->get_id() );
	}
}

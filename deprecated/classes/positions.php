<?php
/**
 * File to represent the old positions-object from < 3.0.0.
 *
 * @deprecated since 3.0.0
 * @package personio-integration-light
 */

namespace personioIntegration;

class positions {
	public function getPositions(int $limit = -1, array $parameterToAdd = [] ): array {
		add_filter( 'personio_integration_positions_resulting_list', array( $this, 'convert_position_objects' ) );
		return \PersonioIntegrationLight\PersonioIntegration\Positions::get_instance()->get_positions( $limit, $parameterToAdd );
	}
	public function convert_position_objects( array $resulting_position_list ): array {
		$new_list = array();
		foreach( $resulting_position_list as $wrong_object ) {
			$obj = new \personioIntegration\position( $wrong_object->get_id() );
			$new_list[] = $obj;
		}
		return $new_list;
	}
}

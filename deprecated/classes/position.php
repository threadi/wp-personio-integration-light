<?php
/**
 * File to represent the old position-object from < 3.0.0.
 *
 * @deprecated since 3.0.0
 * @package personio-integration-light
 */

namespace personioIntegration;

class position extends \PersonioIntegrationLight\PersonioIntegration\Position {
	public function isValid(): bool {
		_deprecated_function( __FUNCTION__, '3.0.0', 'Position->is_valid()' );
		return $this->is_valid();
	}
	public function getPersonioId() {
		_deprecated_function( __FUNCTION__, '3.0.0', 'Position->get_personio_id()' );
		return $this->get_personio_id();
	}
	public function getTitle(): string {
		_deprecated_function( __FUNCTION__, '3.0.0', 'Position->get_title()' );
		return $this->get_title();
	}
}

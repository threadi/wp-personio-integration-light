<?php
/**
 * Data-driven tests for the settings validation callbacks.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Plugin;

use PersonioIntegrationLight\Plugin\Admin\SettingsValidation\MainLanguage;
use PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl;
use PersonioIntegrationLight\Plugin\Admin\SettingsValidation\ScheduleInterval;
use PersonioIntegrationLight\Plugin\Admin\SettingsValidation\UrlTimeout;
use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Test the settings validation callbacks (pure input -> output logic).
 */
class SettingsValidation extends PersonioTestCase {

	/**
	 * Prepare a deterministic environment for the value-based cases.
	 *
	 * @return void
	 */
	public function set_up(): void {
		parent::set_up();

		// make the main-language fallback deterministic ("en").
		update_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, 'en' );
	}

	/**
	 * Assert that a validation callback maps a given input to the expected output.
	 *
	 * @dataProvider provide_validation_cases
	 *
	 * @param callable $validator The static validation callback.
	 * @param mixed    $input     The value passed in.
	 * @param mixed    $expected  The expected return value.
	 * @param string   $message   Assertion message.
	 *
	 * @return void
	 */
	public function test_settings_validation( callable $validator, mixed $input, mixed $expected, string $message ): void {
		$this->assertSame( $expected, $validator( $input ), $message );
	}

	/**
	 * Cases: [ validator, input, expected, message ].
	 *
	 * Add one row per validating setting - no new test method needed.
	 *
	 * @return array<string,array{0:callable,1:mixed,2:mixed,3:string}>
	 */
	public static function provide_validation_cases(): array {
		return array(
			// UrlTimeout: absint with a "> 0" requirement.
			'timeout: positive kept'     => array( array( UrlTimeout::class, 'validate' ), '30', 30, 'positive timeout stays' ),
			'timeout: negative absinted' => array( array( UrlTimeout::class, 'validate' ), '-5', 5, 'negative -> absint' ),
			'timeout: non-numeric -> 0'  => array( array( UrlTimeout::class, 'validate' ), 'abc', 0, 'non-numeric -> 0' ),
			'timeout: empty -> 0'        => array( array( UrlTimeout::class, 'validate' ), '', 0, 'empty -> 0' ),

			// ScheduleInterval: empty and a WP-core interval that always exists.
			'schedule: empty -> empty'   => array( array( ScheduleInterval::class, 'validate' ), '', '', 'empty interval -> empty' ),
			'schedule: known kept'       => array( array( ScheduleInterval::class, 'validate' ), 'personio_integration_daily', 'personio_integration_daily', 'known interval kept' ),

			// MainLanguage: supported kept, unsupported falls back to the main language.
			'lang: en kept'              => array( array( MainLanguage::class, 'validate' ), 'en', 'en', 'supported language kept' ),
			'lang: de kept'              => array( array( MainLanguage::class, 'validate' ), 'de', 'de', 'supported language kept' ),
			'lang: unknown -> fallback'  => array( array( MainLanguage::class, 'validate' ), 'xx', 'en', 'unsupported -> main language' ),

			// PersonioIntegrationUrl: domain allowlist (security-relevant, real suffix check).
			'url: official .com'         => array( array( PersonioIntegrationUrl::class, 'check_personio_in_url' ), 'https://example.jobs.personio.com', true, 'official .com accepted' ),
			'url: official .de'          => array( array( PersonioIntegrationUrl::class, 'check_personio_in_url' ), 'https://example.jobs.personio.de', true, 'official .de accepted' ),
			'url: foreign rejected'      => array( array( PersonioIntegrationUrl::class, 'check_personio_in_url' ), 'https://example.com', false, 'foreign domain rejected' ),
			'url: lookalike rejected'    => array( array( PersonioIntegrationUrl::class, 'check_personio_in_url' ), 'https://example.jobs.personio.com.attacker.tld', false, 'lookalike suffix rejected' ),
			'url: garbage invalid'       => array( array( PersonioIntegrationUrl::class, 'validate_url' ), 'not a url', false, 'garbage url invalid' ),
			'url: valid personio url'    => array( array( PersonioIntegrationUrl::class, 'validate_url' ), 'https://example.jobs.personio.com', true, 'valid url accepted' ),
		);
	}

	/**
	 * Kept separate on purpose: this asserts a *side effect* (a settings error),
	 * not a return value, so it does not fit the value-based provider above.
	 *
	 * @return void
	 */
	public function test_schedule_interval_flags_unknown_interval(): void {
		$before = count( get_settings_errors() );
		ScheduleInterval::validate( 'this-interval-does-not-exist' );
		$this->assertGreaterThan( $before, count( get_settings_errors() ) );
	}
}

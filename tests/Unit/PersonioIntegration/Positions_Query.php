<?php
/**
 * Tests for the position query builder (attribute handling).
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\PersonioIntegration;

use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Test the attribute handling of Positions::get_positions().
 */
class Positions_Query extends PersonioTestCase {

	/**
	 * Prepare the test environment.
	 *
	 * @return void
	 */
	public function set_up(): void {
		parent::set_up();

		// ensure a known set of positions exists.
		self::get_single_position();
	}

	/**
	 * Test the limit restriction.
	 *
	 * @return void
	 */
	public function test_limit_restricts_count(): void {
		$all = Positions::get_instance()->get_positions( -1 );
		$this->assertGreaterThan( 1, count( $all ), 'fixture should contain several positions' );
		$this->assertCount( 1, Positions::get_instance()->get_positions( 1 ) );
	}

	/**
	 * Test the ID restriction.
	 *
	 * @return void
	 */
	public function test_ids_restricts_to_given_ids(): void {
		$all      = Positions::get_instance()->get_positions( -1 );
		$first_id = $all[0]->get_id();

		// test it.
		$result = Positions::get_instance()->get_positions( -1, array( 'ids' => array( $first_id ) ) );
		$this->assertCount( 1, $result );
		$this->assertSame( $first_id, $result[0]->get_id() );
	}

	/**
	 * Test the personioid restriction.
	 *
	 * @return void
	 */
	public function test_personioid_restricts_to_matching_position(): void {
		$all         = Positions::get_instance()->get_positions( -1 );
		$personio_id = $all[0]->get_personio_id();

		// test it.
		$result = Positions::get_instance()->get_positions( -1, array( 'personioid' => $personio_id ) );
		$this->assertCount( 1, $result );
		$this->assertSame( $personio_id, $result[0]->get_personio_id() );
	}

	/**
	 * Test the sort order.
	 *
	 * @return void
	 */
	public function test_sort_desc_reverses_title_order(): void {
		$asc_ids = array_map(
			static fn( $p ) => $p->get_id(),
			Positions::get_instance()->get_positions( -1, array( 'sort' => 'asc', 'sortby' => 'title' ) )
		);
		$desc_ids = array_map(
			static fn( $p ) => $p->get_id(),
			Positions::get_instance()->get_positions( -1, array( 'sort' => 'desc', 'sortby' => 'title' ) )
		);

		// test the result.
		$this->assertNotSame( $asc_ids, $desc_ids, 'desc order should differ from asc order' );
		$this->assertEqualsCanonicalizing( $asc_ids, $desc_ids, 'both orderings must contain the same positions' );
	}
}

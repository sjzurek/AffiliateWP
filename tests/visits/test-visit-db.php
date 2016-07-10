<?php
/**
 * Tests for Affiliate_WP_Visits_DB class
 *
 * @covers Affiliate_WP_Visits_DB
 * @group database
 * @group visits
 */
class Visits_DB_Tests extends WP_UnitTestCase {

	/**
	 * Test affiliates.
	 *
	 * @access public
	 * @var array
	 */
	public $affiliates = array();

	/**
	 * Test visits.
	 *
	 * @access public
	 * @var array
	 */
	public $visits = array();

	/**
	 * Tear down.
	 */
	public function tearDown() {
		// Reset fixtures.
		$this->affiliates = array();
		$this->visits = array();

		parent::tearDown();
	}

	/**
	 * @covers Affiliate_WP_Visits_DB::get_visits()
	 */
	public function test_get_visits_should_return_array_of_Visit_objects_if_not_count_query() {
		$this->_set_up_visits( 4 );

		$results = affiliate_wp()->visits->get_visits();

		// Check a random visit.
		$this->assertInstanceOf( 'AffWP\Visit', $results[ rand( 0, 3 ) ] );
	}

	/**
	 * @covers Affiliate_WP_Visits_DB::get_visits()
	 */
	public function test_get_visits_should_turn_integer_if_count_query() {
		$this->_set_up_visits( 4 );

		$results = affiliate_wp()->visits->get_visits( array(), $count = true );

		$this->assertTrue( is_numeric( $results ) );
	}

	/**
	 * Helper to set up a user, an affiliate, and visits.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param int   $count      Optional. Number of visits to create. Default 3.
	 * @param array $visit_args Optional. Arguments for adding visits. Default empty array.
	 */
	public function _set_up_visits( $count = 3, $visit_args = array() ) {
		$this->affiliates[] = affwp_add_affiliate( array(
			'user_id' => $this->factory()->user->create()
		) );

		for ( $i = 1; $i <= $count; $i++ ) {
			$this->visits[] = affiliate_wp()->visits->add( array(
				'affiliate_id' => $this->affiliates[0]
			) );
		}
	}
}

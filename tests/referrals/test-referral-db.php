<?php
/**
 * Tests for Affiliate_WP_DB_Affiliates class
 *
 * @covers Affiliate_WP_Referrals_DB
 * @group database
 * @group referrals
 */
class Referrals_DB_Tests extends WP_UnitTestCase {

	/**
	 * Test affiliates.
	 *
	 * @access public
	 * @var array
	 */
	public $affiliates = array();

	/**
	 * Test referrals.
	 *
	 * @access public
	 * @var array
	 */
	public $referrals = array();

	/**
	 * Tear down.
	 */
	public function tearDown() {
		// Reset fixtures.
		$this->affiliates = array();
		$this->referrals = array();

		parent::tearDown();
	}

	/**
	 * @covers Affiliate_WP_Referrals_DB::get_referrals()
	 */
	public function test_get_referrals_should_return_array_of_Referral_objects_if_not_count_query() {
		$this->_set_up_referrals( 4 );

		$results = affiliate_wp()->referrals->get_referrals();

		// Check a random referral.
		$this->assertInstanceOf( 'AffWP\Referral', $results[ rand( 0, 3 ) ] );
	}

	/**
	 * @covers Affiliate_WP_Referrals_DB::get_referrals()
	 */
	public function test_get_referrals_should_return_integer_if_count_query() {
		$this->_set_up_referrals( 4 );

		$results = affiliate_wp()->referrals->get_referrals( array(), $count = true );

		$this->assertTrue( is_numeric( $results ) );
	}

	/**
	 * Helper to set up a user, an affiliate, and referrals.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param int   $count         Optional. Number of referrals to create. Default 3.
	 * @param array $referral_args Optional. Arguments for adding referrals. Default empty array.
	 */
	public function _set_up_referrals( $count = 3, $referral_args = array() ) {
		$this->affiliates[] = affiliate_wp()->affiliates->add( array(
			'user_id' => $this->factory->user->create()
		) );

		for ( $i = 1; $i <= $count; $i++ ) {
			$this->referrals[] = affiliate_wp()->referrals->add( array(
				'affiliate_id' => $this->affiliates[0]
			) );
		}
	}
}

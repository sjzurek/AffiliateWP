<?php
use AffWP\Visit as Visit;

/**
 * Tests for AffWP\Visit
 *
 * @covers AffWP\Visit
 * @covers AffWP\Object
 *
 * @group visits
 * @group objects
 */
class AffWP_Visit_Tests extends WP_UnitTestCase {

	/**
	 * @covers AffWP\Object::get_instance()
	 */
	public function test_get_instance_with_invalid_visit_id_should_return_false() {
		$this->assertFalse( Visit::get_instance( 0 ) );
	}

	/**
	 * @covers AffWP\Object::get_instance()
	 */
	public function test_get_instance_with_visit_id_should_return_Visit_object() {
		$user_id = $this->factory->user->create();

		$affiliate_id = affiliate_wp()->affiliates->add( array(
			'user_id' => $user_id
		) );

		$referral_id = affiliate_wp()->referrals->add( array(
			'affiliate_id' => $affiliate_id
		) );

		$visit_id = affiliate_wp()->visits->add( array(
			'referral_id'  => $referral_id,
			'affiliate_id' => $affiliate_id
		) );

		$visit = Visit::get_instance( $visit_id );

		$this->assertInstanceOf( 'AffWP\Visit', $visit );
	}
}

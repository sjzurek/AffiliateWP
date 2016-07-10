<?php
use AffWP\Referral as Referral;

/**
 * Tests for AffWP\Referral
 *
 * @covers AffWP\Referral
 * @covers AffWP\Object
 *
 * @group referrals
 * @group objects
 */
class AffWP_Referral_Tests extends WP_UnitTestCase {

	/**
	 * @covers AffWP\Object::get_instance()
	 */
	public function test_get_instance_with_invalid_referral_id_should_return_false() {
		$this->assertFalse( Referral::get_instance( 0 ) );
	}

	/**
	 * @covers AffWP\Object::get_instance()
	 */
	public function test_get_instance_with_referral_id_should_return_Referral_object() {
		$user_id = $this->factory->user->create();

		$affiliate_id = affiliate_wp()->affiliates->add( array(
			'user_id' => $user_id
		) );

		$referral_id = affiliate_wp()->referrals->add( array(
			'affiliate_id' => $affiliate_id
		) );

		$referral = Referral::get_instance( $referral_id );

		$this->assertInstanceOf( 'AffWP\Referral', $referral );
	}
}

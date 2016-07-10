<?php
use AffWP\Affiliate as Affiliate;

/**
 * Tests for AffWP\Affiliate
 *
 * @covers AffWP\Affiliate
 * @covers AffWP\Object
 *
 * @group affiliates
 * @group objects
 */
class AffWP_Affiliate_Tests extends WP_UnitTestCase {

	/**
	 * @covers AffWP\Object::get_instance()
	 */
	public function test_get_instance_with_invalid_affiliate_id_should_return_false() {
		$this->assertFalse( Affiliate::get_instance( 0 ) );
	}

	/**
	 * @covers AffWP\Object::get_instance()
	 */
	public function test_get_instance_with_affiliate_id_should_return_Affiliate_object() {
		$user_id = $this->factory->user->create();

		$affiliate_id = affiliate_wp()->affiliates->add( array(
			'user_id' => $user_id
		) );

		$affiliate = Affiliate::get_instance( $affiliate_id );

		$this->assertInstanceOf( 'AffWP\Affiliate', $affiliate );
	}

}

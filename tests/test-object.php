<?php
/**
 * AffWP\Object tests
 *
 * @covers \AffWP\Object
 */
class AffWP_Object_Tests extends WP_UnitTestCase {

	protected $_user_id, $_affiliate_id, $_referral_id, $_visit_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->_user_id = $this->factory->user->create();

		$this->_affiliate_id = affiliate_wp()->affiliates->add( array(
			'user_id' => $this->_user_id
		) );

		$this->_referral_id = affiliate_wp()->referrals->add( array(
			'affiliate_id' => $this->_affiliate_id
		) );

		$this->_visit_id = affiliate_wp()->visits->add( array(
			'referral_id'  => $this->_referral_id,
			'affiliate_id' => $this->_affiliate_id
		) );
	}

	/**
	 * Tear down.
	 */
	public function tearDown() {
		affwp_delete_visit( $this->_visit_id );
		affwp_delete_referral( $this->_referral_id );
		affwp_delete_affiliate( $this->_affiliate_id, true );

		parent::tearDown();
	}

	/**
	 * @covers \AffWP\Object::__get()
	 */
	public function test__get_with_real_property_should_return_property_value() {
		$status = affwp_get_affiliate_status( $this->_affiliate_id );

		$this->assertSame( $status, affwp_get_affiliate( $this->_affiliate_id )->status );
	}

	/**
	 * @covers \AffWP\Object::__get()
	 */
	public function test__get_with_fake_property_should_return_null() {
		$key = rand_str( 8 );

		$this->assertNull( affwp_get_affiliate( $this->_affiliate_id )->{$key} );
	}

	/**
	 * @covers \AffWP\Object::set()
	 */
	public function test_set_should_return_always_true_if_save_is_default_false() {
		$this->assertTrue( affwp_get_affiliate( $this->_affiliate_id )->set( 'foo', 'bar' ) );
	}

	/**
	 * @covers \AffWP\Object::__set()
	 */
	public function test__set_magic_method_should_set_property_in_memory_only() {
		$key   = rand_str( 5 );
		$value = rand_str( 10 );

		$affiliate = affwp_get_affiliate( $this->_affiliate_id );
		$affiliate->{$key} = $value;

		$this->assertSame( $value, $affiliate->{$key} );
		$this->assertNull( affwp_get_affiliate( $this->_affiliate_id )->{$key} );
	}

	/**
	 * @covers \AffWP\Object\set()
	 */
	public function test_set_should_set_property_in_memory_only() {
		$key   = rand_str( 5 );
		$value = rand_str( 10 );

		$affiliate = affwp_get_affiliate( $this->_affiliate_id );
		$affiliate->set( $key, $value );

		$this->assertSame( $value, $affiliate->{$key} );
		$this->assertNull( affwp_get_affiliate( $this->_affiliate_id )->{$key} );
	}

	/**
	 * @covers \AffWP\Object::set()
	 */
	public function test_set_with_save_parameter_true_and_fake_key_should_return_false() {
		$key   = rand_str( 5 );
		$value = rand_str( 10 );
		
		$affiliate = affwp_get_affiliate( $this->_affiliate_id );
		$this->assertFalse( $affiliate->set( $key, $value, $save = true ) );
	}

	/**
	 * @covers \AffWP\Object::set()
	 */
	public function test_set_with_save_parameter_true_and_real_key_should_return_true() {
		$affiliate = affwp_get_affiliate( $this->_affiliate_id );

		// Initial status should be 'active'.
		$this->assertSame( 'active', $affiliate->status );

		// Update status to 'pending'.
		$this->assertTrue( $affiliate->set( 'status', 'pending', $save = true ) );

		// Saved status should be 'pending'.
		$this->assertSame( 'pending', affwp_get_affiliate( $this->_affiliate_id )->status );
	}

	/**
	 * @covers \AffWP\Object::save()
	 */
	public function test_save_on_success_should_return_true() {
		$affiliate = affwp_get_affiliate( $this->_affiliate_id );

		$affiliate->set( 'status', 'pending' );

		$this->assertTrue( $affiliate->save() );
	}

	/**
	 * @covers \AffWP\Object::save()
	 */
	public function test_save_on_failure_should_return_false() {
		$affiliate = affwp_get_affiliate( $this->_affiliate_id );

		// Delete affiliate to force the update process to fail.
		affwp_delete_affiliate( $this->_affiliate_id );

		$this->assertFalse( $affiliate->save() );
	}
}

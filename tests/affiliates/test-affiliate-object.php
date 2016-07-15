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

	public $user_id, $affiliate_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->user_id = $this->factory->user->create();

		$this->affiliate_id = affiliate_wp()->affiliates->add( array(
			'user_id' => $this->user_id
		) );
	}

	/**
	 * Tear down.
	 */
	public function tearDown() {
		affwp_delete_affiliate( $this->affiliate_id );

		parent::tearDown();
	}

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
		$affiliate = Affiliate::get_instance( $this->affiliate_id );

		$this->assertInstanceOf( 'AffWP\Affiliate', $affiliate );
	}

	/**
	 * @covers \AffWP\Affiliate
	 */
	public function test_affiliate_user_object_should_be_lazy_loadable() {
		$this->assertInstanceOf( '\WP_User', affwp_get_affiliate( $this->affiliate_id )->user );
	}

	/**
	 * @covers \AffWP\Affiliate
	 */
	public function test_lazy_loaded_user_object_should_contain_first_name_user_meta_in_data_object() {
		$first_name = rand_str( 10 );

		update_user_meta( $this->user_id, 'first_name', $first_name );

		$this->assertEquals( $first_name, affwp_get_affiliate( $this->affiliate_id )->user->data->first_name );
	}

	/**
	 * @covers \AffWP\Affiliate
	 */
	public function test_lazy_loaded_user_object_should_contain_last_name_user_meta_in_data_object() {
		$last_name = rand_str( 10 );

		update_user_meta( $this->user_id, 'last_name', $last_name );

		$this->assertSame( $last_name, affwp_get_affiliate( $this->affiliate_id )->user->data->last_name );
	}

	/**
	 * @covers \AffWP\Affiliate
	 */
	public function test_earnings_property_should_be_of_type_float() {
		affwp_increase_affiliate_earnings( $this->affiliate_id, '1.50' );

		$earnings = affwp_get_affiliate( $this->affiliate_id )->earnings;

		$this->assertSame( 'double', gettype( $earnings ) );
	}
}

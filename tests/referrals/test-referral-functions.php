<?php
/**
 * Tests for Referral functions in referral-functions.php.
 *
 * @group referrals
 * @group functions
 */
class Referral_Functions_Tests extends WP_UnitTestCase {

	/**
	 * Test affiliate ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_affiliate_id = 0;

	/**
	 * Test referral ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_referral_id = 0;

	/**
	 * Test visit ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_visit_id = 0;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$args = array(
			'user_id'  => 1,
			'earnings' => 0
		);

		$this->_affiliate_id = affiliate_wp()->affiliates->add( $args );


		$args = array(
			'affiliate_id' => $this->_affiliate_id,
			'amount'       => 10,
			'status'       => 'pending',
			'context'      => 'tests',
			'custom'       => 4,
			'reference'    => 5
		);

		$this->_referral_id = affiliate_wp()->referrals->add( $args );

		$args = array(
			'affiliate_id' => $this->_affiliate_id
		);

		$this->_visit_id = affiliate_wp()->visits->add( $args );
	}

	/**
	 * Tear down.
	 */
	public function tearDown() {
		affwp_delete_affiliate( $this->_affiliate_id );
		affwp_delete_referral( $this->_referral_id );
		affwp_delete_visit( $this->_visit_id );

		parent::tearDown();
	}

	/**
	 * @covers affwp_get_referral()
	 */
	public function test_get_referral_with_no_referral_should_return_false() {
		$this->assertFalse( affwp_get_referral() );
	}

	/**
	 * @covers affwp_get_referral()
	 */
	public function test_get_referral_with_invalid_referral_object_should_return_false() {
		$this->assertFalse( affwp_get_referral( new stdClass() ) );
	}

	/**
	 * @covers affwp_get_referral()
	 */
	public function test_get_referral_with_valid_referral_object_should_return_a_referral_object() {
		$referral = affwp_get_referral( $this->_referral_id );

		$this->assertInstanceOf( 'AffWP\Referral', $referral );
		$this->assertEquals( $this->_referral_id, $referral->referral_id );
	}

	/**
	 * @covers affwp_get_referral()
	 */
	public function test_get_referral_with_invalid_referral_id_should_return_false() {
		$this->assertFalse( affwp_get_referral( 0 ) );
	}

	/**
	 * @covers affwp_get_referral_status()
	 */
	public function test_get_referral_status_with_invalid_referral_id_should_return_false() {
		$this->assertFalse( affwp_get_referral_status( 0 ) );
	}

	/**
	 * @covers affwp_get_referral_status()
	 */
	public function test_get_referral_status_with_valid_referral_id_should_return_a_status() {
		$this->assertEquals( 'pending', affwp_get_referral_status( $this->_referral_id ) );
	}

	/**
	 * @covers affwp_get_referral_status()
	 */
	public function test_get_referral_status_with_invalid_referral_object_should_return_false() {
		$this->assertFalse( affwp_get_referral_status( affwp_get_referral() ) );
	}

	/**
	 * @covers affwp_get_referral_status()
	 */
	public function test_get_referral_status_With_valid_referral_object_should_return_a_status() {
		$referral = affwp_get_referral( $this->_referral_id );

		$this->assertEquals( 'pending', affwp_get_referral_status( $referral ) );
	}

	/**
	 * @covers affwp_get_referral_status_label()
	 */
	public function test_get_referral_status_label_with_no_referral_should_return_false() {
		$this->assertFalse( affwp_get_referral_status_label( null ) );
	}

	/**
	 * @covers affwp_get_referral_status_label()
	 */
	public function test_get_referral_status_label_with_invalid_referral_id_should_return_false() {
		$this->assertFalse( affwp_get_referral_status_label( 0 ) );
	}

	/**
	 * @covers affwp_get_referral_status_label()
	 */
	public function test_get_referral_status_label_with_valid_referral_id_should_return_the_label() {
		$this->assertEquals( 'Pending', affwp_get_referral_status_label( $this->_referral_id ) );
	}

	/**
	 * @covers affwp_get_referral_status_label()
	 */
	public function test_get_referral_status_label_with_invalid_referral_object_should_return_false() {
		$this->assertFalse( affwp_get_referral_status_label( affwp_get_referral() ) );
	}

	/**
	 * @covers affwp_get_referral_status_label()
	 */
	public function test_get_referral_status_label_with_valid_referral_object_should_return_the_label() {
		$referral = affwp_get_referral( $this->_referral_id );

		$this->assertEquals( 'Pending', affwp_get_referral_status_label( $referral ) );
	}

	/**
	 * @covers affwp_set_referral_status()
	 */
	public function test_get_referral_status_with_no_referral_should_return_false() {
		$this->assertFalse( affwp_get_referral_status( null ) );
	}

	/**
	 * @covers affwp_set_referral_status()
	 */
	public function test_set_referral_status_with_invalid_referral_id_should_return_false() {
		$this->assertFalse( affwp_set_referral_status( 0 ) );
	}

	/**
	 * @covers affwp_set_referral_status()
	 */
	public function test_set_referral_status_with_valid_referral_id_and_valid_status_should_return_true() {
		$this->assertTrue( affwp_set_referral_status( $this->_referral_id , 'unpaid' ) );
	}

	/**
	 * @covers affwp_set_referral_status()
	 */
	public function test_set_referral_status_with_invalid_referral_object_should_return_false() {
		$this->assertFalse( affwp_set_referral_status( affwp_get_referral() ) );
	}

	/**
	 * @covers affwp_set_referral_status()
	 */
	public function test_set_referral_status_with_valid_referral_object_and_valid_status_should_return_true() {
		$referral = affwp_get_referral( $this->_referral_id );

		$this->assertTrue( affwp_set_referral_status( $referral, 'unpaid' ) );
	}

	/**
	 * @covers affwp_get_referral_status()
	 */
	public function test_set_referral_status_should_update_status() {
		$this->assertEquals( 'pending', affwp_get_referral_status( $this->_referral_id ) );

		affwp_set_referral_status( $this->_referral_id, 'unpaid' );
		$this->assertEquals( 'unpaid', affwp_get_referral_status( $this->_referral_id ) );

		affwp_set_referral_status( $this->_referral_id, 'rejected' );
		$this->assertEquals( 'rejected', affwp_get_referral_status( $this->_referral_id ) );
	}

	/**
	 * @covers affwp_set_referral_status()
	 */
	public function test_set_referral_status_with_new_status_paid_should_increase_earnings() {
		$referral     = affwp_get_referral( $this->_referral_id );
		$old_earnings = affwp_get_affiliate_earnings( $this->_affiliate_id );

		affwp_set_referral_status( $referral->referral_id, 'paid' );

		$new_earnings = affwp_get_affiliate_earnings( $this->_affiliate_id );

		$this->assertEquals( $old_earnings += $referral->amount, $new_earnings );
	}

	/**
	 * @covers affwp_set_referral_status()
	 */
	public function test_set_referral_status_with_new_status_paid_should_increase_referral_count() {
		$referral     = affwp_get_referral( $this->_referral_id );
		$old_count    = affwp_get_affiliate_referral_count( $this->_affiliate_id );

		affwp_set_referral_status( $referral, 'paid' );

		$new_count = affwp_get_affiliate_referral_count( $this->_affiliate_id );

		$this->assertEquals( ++$old_count, $new_count );
	}

	/**
	 * @covers affwp_set_referral_status()
	 */
	public function test_set_referral_status_with_new_status_unpaid_old_status_pending_should_be_marked_accepted() {
		affiliate_wp()->referrals->update( $this->_referral_id, array(
			'visit_id' => $this->_visit_id
		) );

		$referral = affwp_get_referral( $this->_referral_id );

		$this->assertEquals( 'pending', affwp_get_referral_status( $referral ) );

		affwp_set_referral_status( $referral, 'unpaid' );
		$this->assertEquals( 'unpaid', affwp_get_referral_status( $referral ) );

		$visit = affiliate_wp()->visits->get( $referral->visit_id );
		$this->assertEquals( $referral->referral_id, $visit->referral_id );
	}

	/**
	 * @covers affwp_set_referral_status()
	 */
	public function test_set_referral_status_with_new_status_not_paid_old_status_paid_should_decrease_earnings() {
		// Inflate earnings.
		affwp_increase_affiliate_earnings( $this->_affiliate_id, '30' );

		// Start with 'paid' (default 'pending').
		affwp_set_referral_status( $this->_referral_id, 'paid' );

		$referral = affwp_get_referral( $this->_referral_id );
		$old_earnings = affwp_get_affiliate_earnings( $this->_affiliate_id );

		// Switch to 'unpaid'.
		affwp_set_referral_status( $referral, 'unpaid' );

		$new_earnings = affwp_get_affiliate_earnings( $this->_affiliate_id );

		// New earnings = $old_earnings minus the deducted referral amount.
		$this->assertEquals( $old_earnings - $referral->amount, $new_earnings );
	}

	/**
	 * @covers affwp_set_referral_status()
	 */
	public function test_set_referral_status_with_new_status_not_paid_old_status_paid_should_decrease_referral_count() {
		// Inflate referral count.
		affwp_increase_affiliate_referral_count( $this->_affiliate_id );
		affwp_increase_affiliate_referral_count( $this->_affiliate_id );

		// Start with 'paid' (default 'pending').
		affwp_set_referral_status( $this->_referral_id, 'paid' );

		$referral = affwp_get_referral( $this->_referral_id );
		$old_count = affwp_get_affiliate_referral_count( $this->_affiliate_id );

		// Switch to 'unpaid'.
		affwp_set_referral_status( $referral, 'unpaid' );

		$new_count = affwp_get_affiliate_referral_count( $this->_affiliate_id );

		// New count = $old_count minus 1.
		$this->assertEquals( --$old_count, $new_count );
	}

	/**
	 * @covers affwp_add_referral()
	 */
	public function test_add_referral_with_emty_user_id_empty_affiliate_should_return_zero() {
		$this->assertSame( 0, affwp_add_referral( null ) );
	}

	/**
	 * @covers affwp_add_referral()
	 */
	public function test_add_referral_with_invalid_affiliate_id_should_return_zero() {
		$this->assertSame( 0, affwp_add_referral( array( 'affiliate_id' => 999999999999 ) ) );
	}

	/**
	 * @covers affwp_add_referral()
	 */
	public function test_add_referral_should_default_to_pending_status() {
		$referral_id = affwp_add_referral( array(
			'affiliate_id' => $this->_affiliate_id
		) );

		$this->assertEquals( 'pending', affwp_get_referral_status( $referral_id ) );
	}

	/**
	 * @covers affwp_add_referral()
	 */
	public function test_add_referral_should_return_referral_id_on_success() {
		$referral_id = affwp_add_referral( array(
			'affiliate_id' => $this->_affiliate_id
		) );

		$this->assertInstanceOf( 'AffWP\Referral', affwp_get_referral( $referral_id ) );
	}

	/**
	 * @covers affwp_delete_referral()
	 */
	public function test_delete_referral_with_no_referral_should_return_false() {
		$this->assertFalse( affwp_delete_referral( null ) );
	}

	/**
	 * @covers affwp_delete_referral()
	 */
	public function test_delete_referral_with_invalid_referral_id_should_return_false() {
		$this->assertFalse( affwp_delete_referral( 0 ) );
	}

	/**
	 * @covers affwp_delete_referral()
	 */
	public function test_delete_referral_with_valid_referral_id_should_return_true() {
		$this->assertTrue( affwp_delete_referral( $this->_referral_id ) );
	}

	/**
	 * @covers affwp_delete_referral()
	 */
	public function test_delete_referral_with_invalid_referral_object_should_return_false() {
		$this->assertFalse( affwp_delete_referral( affwp_get_referral() ) );
	}

	/**
	 * @covers affwp_delete_referral()
	 */
	public function test_delete_referral_with_valid_referral_object_should_return_true() {
		$referral = affwp_get_referral( $this->_referral_id );

		$this->assertTrue( affwp_delete_referral( $referral ) );
	}

	/**
	 * @covers affwp_delete_referral()
	 */
	public function test_delete_referral_with_status_paid_should_decrease_affiliate_earnings() {
		// Needs to be paid.
		affwp_set_referral_status( $this->_referral_id, 'paid' );

		// Inflate earnings.
		affwp_increase_affiliate_earnings( $this->_affiliate_id, '10' );

		$old_earnings = affwp_get_affiliate_earnings( $this->_affiliate_id );

		affwp_delete_referral( $this->_referral_id );

		$new_earnings = affwp_get_affiliate_earnings( $this->_affiliate_id );

		$this->assertEquals( $old_earnings - 10, $new_earnings );
	}

	/**
	 * @covers affwp_delete_referral()
	 */
	public function test_delete_referral_with_status_paid_should_decrease_referral_count() {
		// Needs to be paid.
		affwp_set_referral_status( $this->_referral_id, 'paid' );

		$old_count = affwp_get_affiliate_referral_count( $this->_affiliate_id );

		affwp_delete_referral( $this->_referral_id );

		$new_count = affwp_get_affiliate_referral_count( $this->_affiliate_id );

		$this->assertEquals( --$old_count, $new_count );
	}
}

<?php
/**
 * Tests for Affiliate_WP_DB_Affiliates class
 *
 * @covers Affiliate_WP_DB
 * @group database
 */
class AffiliateWP_DB_Tests extends WP_UnitTestCase {

	/**
	 * Test user ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_user_id = 0;

	/**
	 * Test affiliate ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_affiliate_id = 0;

	/**
	 * Tests referral ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_referral_id = 0;

	/**
	 * Test creative ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_creative_id = 0;

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

		$this->_user_id = $this->factory->user->create();

		$this->_affiliate_id = affwp_add_affiliate( array(
			'user_id' => $this->_user_id
		) );

		$this->_referral_id = affwp_add_referral( array(
			'affiliate_id' => $this->_affiliate_id
		) );

		$this->_creative_id = affwp_add_creative();

		$this->_visit_id = affiliate_wp()->visits->add( array(
			'affiliate_id' => $this->_affiliate_id
		) );
	}

	/**
	 * Tear down.
	 */
	public function tearDown() {
		wp_delete_user( $this->_user_id );
		affwp_delete_affiliate( $this->_affiliate_id );
		affwp_delete_referral( $this->_referral_id );
		affwp_delete_creative( $this->_creative_id );
		affwp_delete_visit( $this->_visit_id );

		parent::tearDown();
	}

	/**
	 * @covers Affiliate_WP_DB::insert()
	 */
	public function test_insert_should_unslash_data_before_inserting_into_db() {
		$description = addslashes( "Couldn't be simpler" );

		// Confirm the incoming value is slashed. (Simulating $_POST, which is slashed by core).
		$this->assertSame( "Couldn\'t be simpler", $description );

		// Fire ->add() which fires ->insert().
		$referral_id = affiliate_wp()->referrals->add( array(
			'affiliate_id' => $this->_affiliate_id,
			'description'  => $description
		) );

		$stored = affiliate_wp()->referrals->get_column( 'description', $referral_id );

		$this->assertSame( wp_unslash( $description ), $stored );
	}

	/**
	 * @covers Affiliate_WP_DB::update()
	 */
	public function test_update_should_unslash_data_before_inserting_into_db() {
		$description = addslashes( "Couldn't be simpler" );

		// Confirm the incoming value is slashed. (Simulating $_POST, which is slashed by core).
		$this->assertSame( "Couldn\'t be simpler", $description );

		// Fire ->update_referral() which fires ->update()
		affiliate_wp()->referrals->update_referral( $this->_referral_id, array(
			'description' => $description
		) );

		$stored = affiliate_wp()->referrals->get_column( 'description', $this->_referral_id );

		$this->assertSame( wp_unslash( $description ), $stored );
	}
}

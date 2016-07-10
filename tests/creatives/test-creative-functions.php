<?php
/**
 * Tests for Creative functions in creative-functions.php.
 *
 * @group creatives
 * @group functions
 */
class Creative_Functions_Tests extends WP_UnitTestCase {

	/**
	 * Test creative ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_creative_id = 0;

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
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->_user_id = self::factory()->user->create();

		$this->_affiliate_id = affiliate_wp()->affiliates->add( array(
			'user_id' => $this->_user_id
		) );

		$this->_creative_id = affiliate_wp()->creatives->add();
	}

	/**
	 * Tear down.
	 */
	public function tearDown() {
		affwp_delete_creative( $this->_creative_id );
		affwp_delete_affiliate( $this->_affiliate_id );
		self::delete_user( $this->_user_id );

		parent::tearDown();
	}

	/**
	 * @covers affwp_get_creative()
	 */
	public function test_get_creative_with_no_creative_should_return_false() {
		$this->assertFalse( affwp_get_creative() );
	}

	/**
	 * @covers affwp_get_creative()
	 */
	public function test_get_creative_with_invalid_creative_id_should_return_false() {
		$this->assertFalse( affwp_get_creative( 0 ) );
	}

	/**
	 * @covers affwp_get_creative()
	 */
	public function test_get_creative_with_valid_creative_id_should_return_creative() {
		$this->assertInstanceOf( 'AffWP\Creative', affwp_get_creative( $this->_creative_id ) );
	}

	/**
	 * @covers affwp_get_creative()
	 */
	public function test_get_creative_with_invalid_creative_object_should_return_false() {
		$this->assertFalse( affwp_get_creative( new stdClass() ) );
	}

	/**
	 * @covers affwp_get_creative()
	 */
	public function test_get_creative_with_valid_creative_object_should_return_creative() {
		$creative = affwp_get_creative( $this->_creative_id );

		$this->assertInstanceOf( 'AffWP\Creative', affwp_get_creative( $creative ) );
	}

	/**
	 * @covers affwp_add_creative()
	 */
	public function test_add_creative_should_always_true_no_matter_what_you_pass_in_the_array() {
		$this->assertTrue( affwp_add_creative( array(
			'these'    => 'values',
			'can'      => 'be',
			'anything' => 'really!'
		) ) );

		$this->assertTrue( affwp_add_creative() );
	}

	/**
	 * @covers affwp_update_creative()
	 */
	public function test_update_creative_with_empty_creative_id_should_return_false() {
		$this->assertFalse( affwp_update_creative() );
	}

	/**
	 * @covers affwp_update_creative()
	 */
	public function test_update_creative_with_invalid_creative_id_should_return_false() {
		$this->assertFalse( affwp_update_creative( array(
			'creative_id' => 0
		) ) );
	}

	/**
	 * @covers affwp_update_creative()
	 */
	public function test_update_creative_should_return_true_on_success() {
		$this->assertTrue( affwp_update_creative( array(
			'creative_id' => $this->_creative_id
		) ) );
	}

	/**
	 * @covers affwp_update_creative()
	 */
	public function test_update_creative_default_name_should_be_Creative() {
		affwp_update_creative( array(
			'creative_id' => $this->_creative_id,
			'name'        => ''
		) );

		$this->assertSame( 'Creative', affwp_get_creative( $this->_creative_id )->name );
	}

	/**
	 * @covers affwp_update_creative()
	 */
	public function test_update_creative_default_description_should_be_empty() {
		affwp_update_creative( array(
			'creative_id' => $this->_creative_id,
			'description' => ''
		) );

		$this->assertEmpty( affwp_get_creative( $this->_creative_id )->description );
	}

	/**
	 * @covers affwp_update_creative()
	 */
	public function test_update_creative_default_url_should_be_site_url() {
		affwp_update_creative( array(
			'creative_id' => $this->_creative_id,
			'url'         => ''
		) );

		$this->assertSame( get_site_url(), affwp_get_creative( $this->_creative_id )->url );
	}

	/**
	 * @covers affwp_update_creative()
	 */
	public function test_update_creative_default_text_should_be_site_name() {
		affwp_update_creative( array(
			'creative_id' => $this->_creative_id,
			'text'        => ''
		) );

		$site_name = get_bloginfo( 'name' );

		$this->assertSame( $site_name, affwp_get_creative( $this->_creative_id )->text );
	}

	/**
	 * @covers affwp_update_creative()
	 */
	public function test_update_creative_default_image_should_be_empty() {
		affwp_update_creative( array(
			'creative_id' => $this->_creative_id,
			'image'       => ''
		) );

		$this->assertEmpty( affwp_get_creative( $this->_creative_id )->image );
	}

	/**
	 * @covers affwp_update_creative()
	 */
	public function test_update_creative_default_status_should_be_empty() {
		affwp_update_creative( array(
			'creative_id' => $this->_creative_id,
			'status'      => ''
		) );

		$this->assertEmpty( affwp_get_creative( $this->_creative_id )->status );
	}

	/**
	 * @covers affwp_delete_creative()
	 */
	public function test_delete_creative_with_empty_creative_should_return_false() {
		$this->assertFalse( affwp_delete_creative( affwp_get_creative() ) );
	}

	/**
	 * @covers affwp_delete_creative()
	 */
	public function test_delete_creative_with_invalid_creative_id_should_return_false() {
		$this->assertFalse( affwp_delete_creative( 0 ) );
	}

	/**
	 * @covers affwp_delete_creative()
	 */
	public function test_delete_creative_with_valid_creative_id_should_return_true() {
		$this->assertTrue( affwp_delete_creative( $this->_creative_id ) );
	}

	/**
	 * @covers affwp_delete_creative()
	 */
	public function test_delete_creative_with_invalid_creative_object_should_return_false() {
		$this->assertFalse( affwp_delete_creative( affwp_get_creative() ) );
	}

	/**
	 * @covers affwp_delete_creative()
	 */
	public function test_delete_creative_with_valid_creative_object_should_return_true() {
		$creative = affwp_get_creative( $this->_creative_id );

		$this->assertTrue( affwp_delete_creative( $creative ) );
	}

	/**
	 * @covers affwp_set_creative_status()
	 */
	public function test_set_creative_status_with_no_creative_id_should_return_false() {
		$this->assertFalse( affwp_set_creative_status( affwp_get_creative() ) );
	}

	/**
	 * @covers affwp_set_creative_status()
	 */
	public function test_set_creative_status_with_invalid_creative_id_should_return_false() {
		$this->assertFalse( affwp_set_creative_status( 0 ) );
	}

	/**
	 * @covers affwp_set_creative_status()
	 */
	public function test_set_creative_status_with_valid_creative_id_should_return_true() {
		$this->assertTrue( affwp_set_creative_status( $this->_creative_id ) );
	}

	/**
	 * @covers affwp_set_creative_status()
	 */
	public function test_set_creative_status_with_invalid_creative_object_should_return_false() {
		$this->assertFalse( affwp_set_creative_status( affwp_get_creative() ) );
	}

	/**
	 * @covers affwp_set_creative_status()
	 */
	public function test_set_creative_status_with_valid_creative_object_should_return_true() {
		$creative = affwp_get_creative( $this->_creative_id );

		$this->assertTrue( affwp_set_creative_status( $creative ) );
	}

	/**
	 * @covers affwp_set_creative_status()
	 */
	public function test_set_creative_status_with_empty_status_should_update_status_to_empty() {
		affwp_set_creative_status( $this->_creative_id );

		$this->assertEmpty( affwp_get_creative( $this->_creative_id )->status );
	}

	/**
	 * @covers affwp_set_creative_status()
	 */
	public function test_set_creative_status_with_non_empty_status_should_update_status_to_that_value() {
		affwp_set_creative_status( $this->_creative_id, 'whatever' );

		$this->assertSame( 'whatever', affwp_get_creative( $this->_creative_id )->status );
	}

}

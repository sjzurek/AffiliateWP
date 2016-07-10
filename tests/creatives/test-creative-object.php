<?php
use AffWP\Creative as Creative;

/**
 * Tests for AffWP\Creative
 *
 * @covers AffWP\Creative
 * @covers AffWP\Object
 *
 * @group creatives
 * @group objects
 */
class AffWP_Creative_Tests extends WP_UnitTestCase {

	/**
	 * @covers AffWP\Object::get_instance()
	 */
	public function test_get_instance_with_invalid_creative_id_should_return_false() {
		$this->assertFalse( Creative::get_instance( 0 ) );
	}

	/**
	 * @covers AffWP\Object::get_instance()
	 */
	public function test_get_instance_with_creative_id_should_return_Creative_object() {
		$creative_id = affiliate_wp()->creatives->add();

		$this->assertInstanceOf( 'AffWP\Creative', Creative::get_instance( $creative_id ) );
	}
}

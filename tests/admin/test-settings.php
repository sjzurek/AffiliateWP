<?php
/**
 * Tests for Affiliate_WP_Settings
 *
 * @covers Affiliate_WP_Settings
 */
class Afilliate_Settings_Tests extends WP_UnitTestCase {

	/**
	 * Settings instance.
	 *
	 * @access public
	 * @var Affiliate_WP_Settings
	 */
	public $settings;

	/**
	 * Tests set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->settings = new Affiliate_WP_Settings();

		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/settings/display-settings.php';
	}

	/**
	 * @covers Affiliate_WP_Settings::set()
	 */
	public function test_set_method_should_update_options_property_in_memory_only() {
		$affiliates_area = 100000;
		$this->settings->set( array(
			'affiliates_page' => $affiliates_area
		) );

		$options = $this->settings->get_all();
		$actual  = get_option( 'affwp_settings', array() );

		$this->assertSame( $options['affiliates_page'], $affiliates_area );
		$this->assertNotSame( $actual['affiliates_page'], $affiliates_area );
	}

	/**
	 * @covers Affiliate_WP_Settings::set()
	 */
	public function test_set_method_with_save_trigger_should_update_settings() {
		$affiliates_area = rand( 1, 200 );
		$this->settings->set( array(
			'affiliates_page' => $affiliates_area
		), $save = true );

		$options = $this->settings->get_all();
		$actual  = get_option( 'affwp_settings', array() );

		$this->assertSame( $options['affiliates_page'], $affiliates_area );
		$this->assertSame( $actual['affiliates_page'], $affiliates_area );
	}

	/**
	 * @covers Affiliate_WP_Settings::is_setting_disabled()
	 */
	public function test_is_setting_disabled() {
		// Default should be false.
		$this->assertFalse( $this->settings->get( 'debug_mode' ) );

		// Define and reset $settings.
		define( 'AFFILIATE_WP_DEBUG', true );
		$this->settings = new Affiliate_WP_Settings();

		// Constant should override the value.
		$this->assertTrue( $this->settings->get( 'debug_mode' ) );

		$registered_settings = $this->settings->get_registered_settings();
		$args = array_merge( $registered_settings['misc']['debug_mode'], array( 'id' => 'debug_mode' ) );

		$this->assertTrue( $this->settings->is_setting_disabled( $args ) );
	}
}

<?php
/**
 * Objects: Affiliate
 *
 * @package AffiliateWP
 * @category Core
 *
 * @since 1.9
 */

namespace AffWP;

/**
 * Implements an affiliate object.
 *
 * @since 1,9
 *
 * @see AffWP\Object
 * @see affwp_get_affiliate()
 */
final class Affiliate extends Object {

	/**
	 * Affiliate ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $affiliate_id = 0;

	/**
	 * Object ID (alias for affiliate_id).
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $ID = 0;

	/**
	 * Affiliate user ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $user_id = 0;

	/**
	 * Affiliate rate.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 *
	 * @see Affiliate::get_rate()
	 */
	protected $rate;

	/**
	 * Affiliate rate type.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 *
	 * @see Affiliate::get_rate_type()
	 */
	protected $rate_type;

	/**
	 * Affiliate payment email.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $payment_email;

	/**
	 * Affiliate status.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $status;

	/**
	 * Affiliate earnings.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $earnings;

	/**
	 * Affiliate referrals
	 *
	 * @since 1.8\9
	 * @access public
	 * @var int
	 */
	public $referrals;

	/**
	 * Affiliate referral visits.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $visits;

	/**
	 * Affiliate registration date.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $date_registered;

	/**
	 * Token to use for generating cache keys.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 *
	 * @see AffWP\Object::get_cache_key()
	 */
	public static $cache_token = 'affwp_affiliates';

	/**
	 * Object type.
	 *
	 * Used as the cache group and for accessing object DB classes in the parent.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 */
	public static $object_type = 'affiliate';

	/**
	 * Sanitizes an affiliate object field.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param string $field        Object field.
	 * @param mixed  $value        Field value.
	 * @return mixed Sanitized field value.
	 */
	public static function sanitize_field( $field, $value ) {
		if ( in_array( $field, array( 'affiliate_id', 'user_id', 'referrals', 'visits', 'ID' ) ) ) {
			$value = (int) $value;
		}

		return $value;
	}

	/**
	 * Retrieves the object instance.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param int $object Object ID.
	 * @return object|false Object instance or false.
	 */
	public static function get_instance( $object_id ) {
		self::$object_group = affiliate_wp()->affiliates->cache_group;

		return parent::get_instance( $object_id );
	}

	/**
	 * Constructor.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param Affiliate $affiliate Affiliate object.
	 */
	public function __construct( $affiliate ) {
		parent::__construct( $affiliate );

		$primary_key = affiliate_wp()->affiliates->primary_key;

		$this->ID = $this->{$primary_key};
	}

	/**
	 * Gets values of non-public properties.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param string $key Property to retrieve a value for.
	 * @return mixed Property value.
	 */
	public function __get( $key ) {
		switch ( $key ) {
			// Derived properties.
			case 'rate':
				$value = $this->get_rate();
				break;
			case 'rate_type':
				$value = $this->get_rate_type();
				break;
			case 'payment_email':
				$value = $this->get_payment_email();
				break;

			// Everything else.
			default:
				$value = parent::__get( $key );
				break;
		}

		return $value;
	}

	/**
	 * Retrieves the affiliate rate type.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @return string Rate type. If empty, defaults to the global referral rate type.
	 */
	protected function get_rate_type() {

		if ( empty( $this->rate_type ) ) {
			return affiliate_wp()->settings->get( 'referral_rate_type', 'percentage' );
		}

		return $this->rate_type;
	}

	/**
	 * Retrieves the affiliate rate.
	 *
	 * @since 1.9
	 * @access private
	 *
	 * @return int Rate. If empty, defaults to the global referral rate.
	 */
	private function get_rate() {
		if ( empty( $this->rate ) ) {
			return affiliate_wp()->settings->get( 'referral_rate', 20 );
		}

		return $this->rate;
	}

	/**
	 * Retrieves the payment email.
	 *
	 * If not set or invalid, the affiliate's account email is used instead.
	 *
	 * @since 1.9
	 * @access private
	 *
	 * @return string Payment email.
	 */
	private function get_payment_email() {
		if ( empty( $this->payment_email ) || ! is_email( $this->payment_email ) ) {
			$email = affwp_get_affiliate_email( $this->ID );
		} else {
			$email = $this->payment_email;
		}

		return $email;
	}

	/**
	 * Determines if the current affiliate has a custom rate value.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @return bool True if the affiliate has a custom rate, otherwise false.
	 */
	public function has_custom_rate() {
		return empty( $this->rate ) ? false : true;
	}
}

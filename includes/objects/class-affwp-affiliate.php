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
 *
 * @property-read int     $ID   Alias for `$affiliate_id`.
 * @property-read WP_User $user User object.
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
	 * @access public
	 * @var string
	 *
	 * @see Affiliate::rate()
	 */
	public $rate;

	/**
	 * Affiliate rate type.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 *
	 * @see Affiliate::rate_type()
	 */
	public $rate_type;

	/**
	 * Affiliate payment email.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $payment_email;

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
	 * @var float
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
	 * Database group.
	 *
	 * Used in \AffWP\Object for accessing the affiliates DB class methods.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public static $db_group = 'affiliates';

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
	 * Retrieves the values of the given key.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param string $key Key to retrieve the value for.
	 * @return mixed|\WP_User Value.
	 */
	public function __get( $key ) {
		if ( 'user' === $key ) {
			return $this->build_the_user_object();
		}

		return parent::__get( $key );
	}

	/**
	 * Builds the lazy-loaded user object with first and last name fields.
	 *
	 * @since 1.9
	 * @access private
	 *
	 * @return false|\WP_User Built user object or false if it doesn't exist.
	 */
	private function build_the_user_object() {
		$user = get_user_by( 'id', $this->user_id );

		if ( $user ) {
			foreach ( array( 'first_name', 'last_name' ) as $field ) {
				$user->data->{$field} = get_user_meta( $this->user_id, $field, true );
			}
		}

		return $user;
	}

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

		if ( 'earnings' === $field ) {
			$value = floatval( $value );
		}

		return $value;
	}

	/**
	 * Retrieves the affiliate rate type.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return string Rate type. If empty, defaults to the global referral rate type.
	 */
	public function rate_type() {
		if ( empty( $this->rate_type ) ) {
			return affiliate_wp()->settings->get( 'referral_rate_type', 'percentage' );
		}

		return $this->rate_type;
	}

	/**
	 * Retrieves the affiliate rate.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return int Rate. If empty, defaults to the global referral rate.
	 */
	public function rate() {
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
	 * @access public
	 *
	 * @return string Payment email.
	 */
	public function payment_email() {
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
	 * @access public
	 *
	 * @return bool True if the affiliate has a custom rate, otherwise false.
	 */
	public function has_custom_rate() {
		return empty( $this->rate ) ? false : true;
	}
}

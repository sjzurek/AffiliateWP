<?php
/**
 * Objects: Referral
 *
 * @package AffiliateWP
 * @category Core
 *
 * @since 1.9
 */

namespace AffWP;

/**
 * Implements a referral object.
 *
 * @since 1,9
 *
 * @see AffWP\Object
 * @see affwp_get_referral()
 */
final class Referral extends Object {

	/**
	 * Referral ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $referral_id = 0;

	/**
	 * Object ID (alias for referral_id).
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $ID = 0;

	/**
	 * Affiliate ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $affiliate_id = 0;

	/**
	 * Visit ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $visit_id = 0;

	/**
	 * Referral description.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $description;

	/**
	 * Referral status.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $status;

	/**
	 * Referral amount.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $amount;

	/**
	 * Referral currency.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $currency;

	/**
	 * Custom referral data.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $custom;

	/**
	 * Referral context (usually integration).
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $context;

	/**
	 * Referral campaign name.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $campaign;

	/**
	 * Referral reference.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $reference;

	/**
	 * Products associated with the referral.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $products;

	/**
	 * Date the referral was created.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $date;

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
	public static $cache_token = 'affwp_referrals';

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
	public static $object_type = 'referral';

	/**
	 * Sanitizes a referral object field.
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
		if ( in_array( $field, array( 'referral_id', 'affiliate_id', 'visit_id', 'ID' ) ) ) {
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
		self::$object_group = affiliate_wp()->referrals->cache_group;

		return parent::get_instance( $object_id );
	}

	/**
	 * Constructor.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param Referral $referral Referral object.
	 */
	public function __construct( $referral ) {
		parent::__construct( $referral );

		$primary_key = affiliate_wp()->referrals->primary_key;

		$this->ID = $this->{$primary_key};
	}

}

<?php
/**
 * Objects: Visit
 *
 * @package AffiliateWP
 * @category Core
 *
 * @since 1.9
 */

namespace AffWP;

/**
 * Implements a visit object.
 *
 * @since 1,9
 *
 * @see AffWP\Object
 * @see affwp_get_visit()
 */
final class Visit extends Object {

	/**
	 * Visit ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $visit_id = 0;

	/**
	 * Object ID (alias for visit_id).
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
	 * Referral ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $referral_id = 0;

	/**
	 * Visit URL.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $url;

	/**
	 * Visit referrer.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $referrer;

	/**
	 * Referral campaign name.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $campaign;

	/**
	 * Visit IP address (IPv4).
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $ip;

	/**
	 * Date the visit was created.
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
	public static $cache_token = 'affwp_visits';

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
	public static $object_type = 'visit';

	/**
	 * Sanitizes a visit object field.
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
		if ( in_array( $field, array( 'visit_id', 'affiliate_id', 'referral_id', 'ID' ) ) ) {
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
		self::$object_group = affiliate_wp()->visits->cache_group;

		return parent::get_instance( $object_id );
	}

	/**
	 * Constructor.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param Visit $visit Visit object.
	 */
	public function __construct( $visit ) {
		parent::__construct( $visit );

		$primary_key = affiliate_wp()->visits->primary_key;

		$this->ID = $this->{$primary_key};
	}

}

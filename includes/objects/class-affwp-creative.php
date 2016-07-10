<?php
/**
 * Objects: Creative
 *
 * @package AffiliateWP
 * @category Core
 *
 * @since 1.9
 */

namespace AffWP;

/**
 * Implements a creative object.
 *
 * @since 1,9
 *
 * @see AffWP\Object
 * @see affwp_get_creative()
 */
final class Creative extends Object {

	/**
	 * Creative ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $creative_id = 0;

	/**
	 * Object ID (alias for creative_id).
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $ID = 0;

	/**
	 * Name of the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $name;

	/**
	 * Description for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $description;

	/**
	 * URL for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $url;

	/**
	 * Text for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $text;

	/**
	 * Image URL for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $image;

	/**
	 * Status for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $status;

	/**
	 * Creation date for the creative.
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
	public static $cache_token = 'affwp_creatives';

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
	public static $object_type = 'creative';

	/**
	 * Sanitizes a creative object field.
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
		if ( in_array( $field, array( 'creative_id', 'ID' ) ) ) {
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
		self::$object_group = affiliate_wp()->creatives->cache_group;

		return parent::get_instance( $object_id );
	}

	/**
	 * Constructor.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param Creative $creative Creative object.
	 */
	public function __construct( $creative ) {
		parent::__construct( $creative );

		$primary_key = affiliate_wp()->creatives->primary_key;

		$this->ID = $this->{$primary_key};
	}

}

<?php

class Affiliate_WP_Visits_DB extends Affiliate_WP_DB {

	/**
	 * Cache group for queries.
	 *
	 * @internal DO NOT change. This is used externally both as a cache group and shortcut
	 *           for accessing db class instances via affiliate_wp()->{$cache_group}->*.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $cache_group = 'visits';

	/**
	 * Object type to query for.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $query_object_type = 'AffWP\Visit';

	public function __construct() {
		global $wpdb;

		if( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single visits table for the whole network
			$this->table_name  = 'affiliate_wp_visits';
		} else {
			$this->table_name  = $wpdb->prefix . 'affiliate_wp_visits';
		}
		$this->primary_key = 'visit_id';
		$this->version     = '1.0';
	}

	/**
	 * Retrieves a visit object.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @see Affiliate_WP_DB::get_core_object()
	 *
	 * @param int|object|AffWP\Visit $visit Visit ID or object.
	 * @return AffWP\Visit|null Visit object, null otherwise.
	 */
	public function get_object( $visit ) {
		return $this->get_core_object( $visit, $this->query_object_type );
	}

	public function get_columns() {
		return array(
			'visit_id'     => '%d',
			'affiliate_id' => '%d',
			'referral_id'  => '%d',
			'url'          => '%s',
			'referrer'     => '%s',
			'campaign'     => '%s',
			'ip'           => '%s',
			'date'         => '%s',
		);
	}

	public function get_column_defaults() {
		return array(
			'affiliate_id' => 0,
			'referral_id'  => 0,
			'date'         => date( 'Y-m-d H:i:s' ),
			'referrer'     => ! empty( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '',
			'campaign'     => ! empty( $_REQUEST['campaign'] )    ? $_REQUEST['campaign']    : ''
		);
	}

	/**
	 * Retrieve visits from the database
	 *
	 * @access  public
	 * @since   1.0
	 * @param   array $args
	 * @param   bool  $count  Return only the total number of results found (optional)
	*/
	public function get_visits( $args = array(), $count = false ) {
		global $wpdb;

		$defaults = array(
			'number'          => 20,
			'offset'          => 0,
			'affiliate_id'    => 0,
			'referral_id'     => 0,
			'referral_status' => '',
			'campaign'        => '',
			'order'           => 'DESC',
			'orderby'         => 'visit_id'
		);

		$args = wp_parse_args( $args, $defaults );

		if( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$where = '';

		// visits for specific affiliates
		if( ! empty( $args['affiliate_id'] ) ) {

			if( is_array( $args['affiliate_id'] ) ) {
				$affiliate_ids = implode( ',', array_map( 'intval', $args['affiliate_id'] ) );
			} else {
				$affiliate_ids = intval( $args['affiliate_id'] );
			}

			$where .= "WHERE `affiliate_id` IN( {$affiliate_ids} ) ";

		}

		// visits for specific referral
		if( ! empty( $args['referral_id'] ) ) {

			if( is_array( $args['referral_id'] ) ) {
				$referral_ids = implode( ',', array_map( 'intval', $args['referral_id'] ) );
			} else {
				$referral_ids = intval( $args['referral_id'] );
			}

			$where .= "WHERE `referral_id` IN( {$referral_ids} ) ";

		}

		// visits for specific campaign
		if( ! empty( $args['campaign'] ) ) {

			if( empty( $where ) ) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}

			if( is_array( $args['campaign'] ) ) {
				$where .= " `campaign` IN(" . implode( ',', array_map( 'esc_sql', $args['campaign'] ) ) . ") ";
			} else {
				$where .= " `campaign` = '" . esc_sql( $args['campaign'] ) . "' ";
			}

		}

		// visits for specific referral status
		if ( ! empty( $args['referral_status'] ) ) {

			if ( 'converted' === $args['referral_status'] ) {
				$where .= "WHERE `referral_id` > 0";
			} elseif ( 'unconverted' === $args['referral_status'] ) {
				$where .= "WHERE `referral_id` = 0";
			}

		}

		// Visits for a date or date range
		if( ! empty( $args['date'] ) ) {

			if( is_array( $args['date'] ) ) {

				if( ! empty( $args['date']['start'] ) ) {

					$start = esc_sql( date( 'Y-m-d H:i:s', strtotime( $args['date']['start'] ) ) );

					if( ! empty( $where ) ) {
						$where .= " AND `date` >= '{$start}'";
					} else {
						$where .= " WHERE `date` >= '{$start}'";
					}

				}

				if( ! empty( $args['date']['end'] ) ) {

					$end = esc_sql( date( 'Y-m-d H:i:s', strtotime( $args['date']['end'] ) ) );

					if( ! empty( $where ) ) {
						$where .= " AND `date` <= '{$end}'";
					} else {
						$where .= " WHERE `date` <= '{$end}'";
					}

				}

			} else {

				$year  = date( 'Y', strtotime( $args['date'] ) );
				$month = date( 'm', strtotime( $args['date'] ) );
				$day   = date( 'd', strtotime( $args['date'] ) );

				if( empty( $where ) ) {
					$where .= " WHERE";
				} else {
					$where .= " AND";
				}

				$where .= " $year = YEAR ( date ) AND $month = MONTH ( date ) AND $day = DAY ( date )";
			}

		}

		// Build the search query
		if( ! empty( $args['search'] ) ) {

			if( empty( $where ) ) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}

			if ( filter_var( $args['search'], FILTER_VALIDATE_IP ) ) {
				$where .= " `ip` LIKE '%%" . esc_sql( $args['search'] ) . "%%' ";
			} else {
				$search_value = esc_sql( $args['search'] );

				$where .= " ( `referrer` LIKE '%%" . $search_value . "%%' OR `url` LIKE '%%" . $search_value . "%%' ) ";
			}
		}

		if ( 'DESC' === strtoupper( $args['order'] ) ) {
			$order = 'DESC';
		} else {
			$order = 'ASC';
		}

		$orderby = array_key_exists( $args['orderby'], $this->get_columns() ) ? $args['orderby'] : $this->primary_key;

		// Overload args values for the benefit of the cache.
		$args['orderby'] = $orderby;
		$args['order']   = $order;

		$key = ( true === $count ) ? md5( 'affwp_visits_count' . serialize( $args ) ) : md5( 'affwp_visits_' . serialize( $args ) );

		$last_changed = wp_cache_get( 'last_changed', $this->cache_group );
		if ( ! $last_changed ) {
			wp_cache_set( 'last_changed', microtime(), $this->cache_group );
		}

		$cache_key = "{$key}:{$last_changed}";

		$results = wp_cache_get( $cache_key, $this->cache_group );

		if ( false === $results ) {

			if ( true === $count ) {

				$results = absint( $wpdb->get_var( "SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$where};" ) );

			} else {

				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT * FROM {$this->table_name} {$where} ORDER BY {$orderby} {$order} LIMIT %d, %d;",
						absint( $args['offset'] ),
						absint( $args['number'] )
					)
				);

			}
		}

		// Convert to AffWP\Visit objects.
		if ( is_array( $results ) ) {
			$results = array_map( 'affwp_get_visit', $results );
		}

		wp_cache_add( $cache_key, $results, $this->cache_group, HOUR_IN_SECONDS );

		return $results;

	}

	/**
	 * Returns the number of results found for a given query
	 *
	 * @param  array  $args
	 * @return int
	 */
	public function count( $args = array() ) {
		return $this->get_visits( $args, true );
	}

	/**
	 * Adds a visit to the database.
	 *
	 * @access public
	 *
	 * @param array $data Optional. Arguments for adding a new visit. Default empty array.
	 * @return int ID of the added visit.
	 */
	public function add( $data = array() ) {

		if( ! empty( $data['url'] ) ) {
			$data['url'] = affwp_sanitize_visit_url( $data['url'] );
		}

		if( ! empty( $data['campaign'] ) ) {

			// Make sure campaign is not longer than 50 characters
			$data['campaign'] = substr( $data['campaign'], 0, 50 );

		}

		$visit_id = $this->insert( $data, 'visit' );


		affwp_increase_affiliate_visit_count( $data['affiliate_id'] );

		return $visit_id;
	}

	/**
	 * Updates a visit.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param int|AffWP\Visit $visit_id Visit ID or object.
	 * @param array           $data     Optional. Data array. Default empty array.
	 * @return int|false The visit ID if successfully updated, false otherwise.
	 */
	public function update_visit( $visit, $data = array() ) {

		if ( ! $visit = affwp_get_visit( $visit ) ) {
			return false;
		}

		if ( ! empty( $data['url'] ) ) {
			$data['url'] = affwp_sanitize_visit_url( $data['url'] );
		}

		if ( ! empty( $data['campaign'] ) ) {
			$data['campaign'] = substr( $data['campaign'], 0, 50 );
		}

		if ( ! empty( $data['affiliate_id'] ) ) {
			// If the passed affiliate ID is invalid, ignore the new value.
			if ( ! affwp_get_affiliate( $data['affiliate_id'] ) ) {
				$data['affiliate_id'] = $visit->affiliate_id;
			}
		}
		if ( $this->update( $visit->ID, $data, '', 'visit' ) ) {
			$updated_visit = affwp_get_visit( $visit->ID );

			// Handle visit counts if the affiliate was changed.
			if ( $updated_visit->affiliate_id !== $visit->affiliate_id ) {
				affwp_decrease_affiliate_visit_count( $visit->affiliate_id );
				affwp_increase_affiliate_visit_count( $updated_visit->affiliate_id );
			}
			return $visit->ID;
		}
		return false;
	}

	/**
	 * Creates the visits database table.
	 *
	 * @access public
	 *
	 * @see dbDelta()
	 */
	public function create_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE {$this->table_name} (
			visit_id bigint(20) NOT NULL AUTO_INCREMENT,
			affiliate_id bigint(20) NOT NULL,
			referral_id bigint(20) NOT NULL,
			url mediumtext NOT NULL,
			referrer mediumtext NOT NULL,
			campaign varchar(50) NOT NULL,
			ip tinytext NOT NULL,
			date datetime NOT NULL,
			PRIMARY KEY  (visit_id),
			KEY affiliate_id (affiliate_id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}
}

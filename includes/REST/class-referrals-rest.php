<?php
namespace AffWP\Referral;

use \AffWP\REST\Controller as Controller;

/**
 * Implements REST routes and endpoints for Referrals.
 *
 * @since 1.9
 *
 * @see AffWP\REST\Controller
 */
class REST extends Controller {

	/**
	 * Registers Referral routes.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Server $wp_rest_server Server object.
	 */
	public function register_routes( $wp_rest_server ) {
		register_rest_route( $this->namespace, '/referrals/', array(
			'methods' => $wp_rest_server::READABLE,
			'callback' => array( $this, 'ep_get_referrals' )
		) );

		register_rest_route( $this->namespace, '/referrals/(?P<id>\d+)', array(
			'methods'  => $wp_rest_server::READABLE,
			'callback' => array( $this, 'ep_referral_id' ),
			'args'     => array(
				'id' => array(
					'required'          => true,
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				)
			),
//			'permission_callback' => function( $request ) {
//				return current_user_can( 'manage_affiliates' );
//			}
		) );
	}

	/**
	 * Base endpoint to retrieve all referrals.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return array|\WP_Error Array of referrals, otherwise WP_Error.
	 */
	public function ep_get_referrals() {
		$referrals = affiliate_wp()->referrals->get_referrals( array(
			'number' => -1,
			'order'  => 'ASC'
		) );

		if ( empty( $referrals ) ) {
			return new \WP_Error(
				'no_referrals',
				'No referrals were found.',
				array( 'status' => 404 )
			);
		}

		return $referrals;
	}

	/**
	 * Endpoint to retrieve an referral by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $args Request arguments.
	 * @return \AffWP\Referral|\WP_Error Referral object or \WP_Error object if not found.
	 */
	public function ep_referral_id( $args ) {
		if ( ! $referral = \affwp_get_referral( $args['id'] ) ) {
			return new \WP_Error(
				'invalid_referral_id',
				'Invalid referral ID',
				array( 'status' => 404 )
			);
		}

		return $referral;
	}

}

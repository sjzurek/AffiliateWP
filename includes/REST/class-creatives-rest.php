<?php
namespace AffWP\Creative;

use \AffWP\REST\Controller as Controller;

/**
 * Implements REST routes and endpoints for Creatives.
 *
 * @since 1.9
 *
 * @see AffWP\REST\Controller
 */
class REST extends Controller {

	/**
	 * Registers Creative routes.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Server $wp_rest_server Server object.
	 */
	public function register_routes( $wp_rest_server ) {
		register_rest_route( $this->namespace, '/creatives/', array(
			'methods' => $wp_rest_server::READABLE,
			'callback' => array( $this, 'ep_get_creatives' )
		) );

		register_rest_route( $this->namespace, '/creatives/(?P<id>\d+)', array(
			'methods'  => $wp_rest_server::READABLE,
			'callback' => array( $this, 'ep_creative_id' ),
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
	 * Base endpoint to retrieve all creatives.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return array|\WP_Error Array of creatives, otherwise WP_Error.
	 */
	public function ep_get_creatives() {
		$creatives = affiliate_wp()->creatives->get_creatives( array(
			'number' => -1,
			'order'  => 'ASC'
		) );

		if ( empty( $creatives ) ) {
			return new \WP_Error(
				'no_creatives',
				'No creatives were found.',
				array( 'status' => 404 )
			);
		}

		return $creatives;
	}

	/**
	 * Endpoint to retrieve a creative by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $args Request arguments.
	 * @return \AffWP\Creative|\WP_Error Creative object or \WP_Error object if not found.
	 */
	public function ep_creative_id( $args ) {
		if ( ! $creative = \affwp_get_creative( $args['id'] ) ) {
			return new \WP_Error(
				'invalid_creative_id',
				'Invalid creative ID',
				array( 'status' => 404 )
			);
		}

		return $creative;
	}

}

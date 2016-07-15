<?php

class Affiliate_WP_PayPal extends Affiliate_WP_Base {

	/**
	 * Get thigns started
	 *
	 * @access  public
	 * @since   1.9
	 */
	public function init() {

		$this->context = 'paypal';

		add_action( 'wp_footer', array( $this, 'scripts' ) );
		add_action( 'wp_ajax_affwp_maybe_insert_paypal_referral', array( $this, 'maybe_insert_referral' ) );
		add_action( 'wp_ajax_nopriv_affwp_maybe_insert_paypal_referral', array( $this, 'maybe_insert_referral' ) );
		add_action( 'init', array( $this, 'process_ipn' ) );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

	}

	/**
	 * Add JS to site footer for detecting PayPal form submissions
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function scripts() {
?>
		<script type="text/javascript">
			var affwp_scripts;
		jQuery(document).ready(function($) {


			$('form').on('submit', function(e) {

				var action = $(this).prop( 'action' );

				if( ! action.indexOf( 'paypal.com/cgi-bin/webscr' ) ) {
					return;
				}

				e.preventDefault();

				var $form = $(this);
				var ipn_url = "<?php echo home_url( 'index.php?affwp-listener=paypal' ); ?>";

				$.ajax({
					type: "POST",
					data: {
						action: 'affwp_maybe_insert_paypal_referral'
					},
					url: affwp_scripts.ajaxurl,
					success: function (response) {

						console.log( response );

						$form.append( '<input type="hidden" name="custom" value="' + response.data.ref + '"/>' );
						$form.append( '<input type="hidden" name="notify_url" value="' + ipn_url + '"/>' );

						$form.get(0).submit();

					}

				}).fail(function (response) {

					if ( window.console && window.console.log ) {
						console.log( response );
					}

				});

			});
		});
		</script>
<?php
	}

	/**
	 * Create a referral during PayPal form submission if customer was referred
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function maybe_insert_referral() {

		$response = array();

		if( $this->was_referred() ) {

			$referral_id = $this->insert_pending_referral( 0.01, affiliate_wp()->tracking->get_visit_id() . '|' . $this->affiliate_id, __( 'Pending PayPal referral', 'affiliate-wp' ) );

			if( $referral_id && $this->debug ) {

				$this->log( 'Pending referral created successfully during maybe_insert_referral()' );

			} elseif ( $this->debug ) {

				$this->log( 'Pending referral failed to be created during maybe_insert_referral()' );

			}

			$response['ref'] = affiliate_wp()->tracking->get_visit_id() . '|' . $this->affiliate_id . '|' . $referral_id;

		}

		wp_send_json_success( $response );

	}

	/**
	 * Process PayPal IPN requests in order to mark referrals as Unpaid
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function process_ipn() {

		if( empty( $_GET['affwp-listener'] ) || 'paypal' !== strtolower( $_GET['affwp-listener'] ) ) {
			return;
		}

		// TODO verify IPN here
		if( $this->debug ) {

			$this->log( 'IPN verified successfully during process_ipn()' );
			$this->log( 'IPN Data: ' . print_r( $_POST, true ) );

		}

		$total        = sanitize_text_field( $_POST['mc_gross'] );
		$custom       = explode( '|', $_POST['custom'] );
		$visit_id     = $custom[0];
		$affiliate_id = $custom[1];
		$referral_id  = $custom[2];
		$visit        = affwp_get_visit( $visit_id );
		$referral     = affwp_get_referral( $referral_id );

		if( ! $visit || ! $referral ) {

			if( $this->debug ) {

				if( ! $visit ) {

					$this->log( 'Visit not successfully retrieved during process_ipn()' );

				}

				if( ! $referral ) {

					$this->log( 'Referral not successfully retrieved during process_ipn()' );

				}

			}

			die( 'Missing visit or referral data' );
		}

		if( 'pending' !== $referral->status ) {

			if( $this->debug ) {

				$this->log( 'Referral has status other than Pending during process_ipn()' );

			}

			die( 'Referral not pending' );
		}

		$visit->set( 'referral_id', $referral->id, true );

		if( $this->debug ) {

			$this->log( 'Referral ID successfully retrieved during process_ipn()' );

		}

		if( 'completed' === strtolower( $_POST['payment_status'] ) ) {

			$reference   = sanitize_text_field( $_POST['txn_id'] );
			$description = ! empty( $_POST['item_name'] ) ? sanitize_text_field( $_POST['item_name'] ) : sanitize_text_field( $_POST['payer_email'] );
			$amount      = $this->calculate_referral_amount( $total, $reference, 0, $referral->affiliate_id );

			$referral->set( 'description', $description );
			$referral->set( 'amount', $amount );
			$referral->set( 'reference', $reference, true );

			$completed = $this->complete_referral( $reference );

			if( $completed ) {

				if( $this->debug ) {

					$this->log( 'Referral completed successfully during process_ipn()' );

				}

				die( 'Referral completed successfully' );

			} else if ( $this->debug ) {

				$this->log( 'Referral failed to be completed during process_ipn()' );

			}

			die( 'Referral not completed successfully' );

		}

	}

	/**
	 * Sets up the reference link in the Referrals table
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'paypal' != $referral->context ) {

			return $reference;

		}

		$url = 'https://www.paypal.com/webscr?cmd=_history-details-from-hub&id=' . $reference ;

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

}
new Affiliate_WP_PayPal;
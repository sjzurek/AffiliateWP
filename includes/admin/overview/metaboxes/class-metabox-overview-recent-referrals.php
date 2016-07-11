<?php
/**
 * The AffWP_Meta_Box_Overview_Recent_Referrals class.
 *
 * This class extends AffWP_Meta_Box_Base, and defines
 * a meta box which displays recent referrals.
 *
 * @since  1.9
 * @see    AffWP_Meta_Box_Base
 */
class AffWP_Meta_Box_Overview_Recent_Referrals extends AffWP_Meta_Box_Base {

	/**
	 * The id of the meta box. Must be unique.
	 *
	 * @access  public
	 * @since   1.9
	 */
	public $meta_box_id = 'overview-recent-referrals';

	/**
	 * The name of the meta box. Must be unique.
	 *
	 * @access  public
	 * @since   1.9
	 */
	public $meta_box_name;

	/**
	 * The position in which the meta box will be loaded
	 * Either 'normal', 'side', or 'advanced'.
	 *
	 * @access  public
	 * @var     $context
	 * @since   1.9
	 */
	public $context = 'secondary';

	/**
	 * Initialize
	 *
	 * @access  public
	 * @return  void
	 * @since   1.9
	 */
	public function init() {
		add_action( 'add_meta_box',              array( $this, 'add_meta_box' ) );
		add_action( 'affwp_overview_meta_boxes', array( $this, 'add_meta_box' ) );

		$this->meta_box_name = __( 'Recent Referrals', 'affiliate-wp' );
	}

	/**
	 * Displays the content of the metabox
	 *
	 * @return mixed content  The metabox content
	 * @since  1.9
	 */
	public function content() {

		$referrals = affiliate_wp()->referrals->get_referrals(
			apply_filters( 'affwp_overview_recent_referrals',
				array(
					'number' => 5,
					'status' => 'unpaid'
				)
			)
		); ?>

		<table class="affwp_table">

			<thead>

				<tr>
					<th><?php _e( 'Affiliate', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'Amount', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'Description', 'affiliate-wp' ); ?></th>
				</tr>

			</thead>

			<tbody>
			<?php if( $referrals ) : ?>
				<?php foreach( $referrals as $referral  ) : ?>
					<tr>
						<td><?php echo affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id ); ?></td>
						<td><?php echo affwp_currency_filter( $referral->amount ); ?></td>
						<td><?php echo ! empty( $referral->description ) ? esc_html( $referral->description ) : ''; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="3"><?php _e( 'No referrals recorded yet', 'affiliate-wp' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>

		</table>
	<?php }
}

new AffWP_Meta_Box_Overview_Recent_Referrals;
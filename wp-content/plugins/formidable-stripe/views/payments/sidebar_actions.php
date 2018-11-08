<?php if ( $payment->status === 'authorized' && FrmStrpApiHelper::can_by_captured( $payment->receipt_id ) ) { ?>
	<div class="misc-pub-section">
		<span class="frm_credit-card-alt_icon frm_icon_font wp-media-buttons-icon"></span>
		<?php esc_html_e( 'Authorized:', 'formidable-payments' ) ?>
		<?php FrmStrpPaymentsController::capture_link( $payment ); ?>
	</div>
<?php } ?>

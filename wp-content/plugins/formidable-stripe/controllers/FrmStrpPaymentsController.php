<?php

class FrmStrpPaymentsController {

	public static function get_receipt_link( $receipt ) {
		$link = '<a href="https://dashboard.stripe.com/payments/' . esc_attr( $receipt ) . '" target="_blank">';
		$link .= esc_html( $receipt );
		$link .= '</a>';
		return $link;
	}

	public static function get_delete_card_link( $card_id ) {
		$link = '<button class="frm-stripe-delete-card" data-cid="' . esc_attr( $card_id ) . '">';
		$link .= __( 'Delete card', 'formidable-stripe' );
		$link .= '</button>';
		return $link;
	}

	public static function delete_card( $args ) {
		return FrmStrpApiHelper::delete_card( $args['id'] );
	}

	public static function manage_cards() {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return '';
		}

		FrmStrpActionsController::load_scripts( array() );

		$cards = FrmStrpApiHelper::get_cards( $user_id );

		ob_start();
		include( FrmStrpAppHelper::plugin_path() . '/views/payments/manage-cards.php' );
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * If the payment has been authorized, include a link to capture
	 *
	 * @since 1.15
	 */
	public static function show_capture_link( $payment ) {
		include( FrmStrpAppHelper::plugin_path() . '/views/payments/sidebar_actions.php' );
	}

	/**
	 * Echo the ajax link to capture a payment
	 *
	 * @since 1.15
	 * @param object $charge - The payment object
	 */
	public static function capture_link( $payment ) {
		$link = admin_url( 'admin-ajax.php?action=frm_trans_capture&payment_id=' . $payment->id . '&nonce=' . wp_create_nonce( 'frm_trans_ajax' ) );
		$link = '<a href="' . esc_url( $link ) . '" class="frm_trans_ajax_link" data-tempid="' . esc_attr( $payment->id ) . '">';
		$link .= __( 'Capture now', 'formidable-stripe' );
		$link .= '</a>';
		echo $link;
	}

	/**
	 * Process the ajax request to capture a charge
	 *
	 * @since 1.15
	 */
	public static function capture_charge() {
		FrmAppHelper::permission_check( 'frm_edit_entries' );
		check_ajax_referer( 'frm_trans_ajax', 'nonce' );

		$payment_id = FrmAppHelper::get_param( 'payment_id', '', 'get', 'sanitize_text_field' );
		if ( $payment_id ) {
			$frm_payment = new FrmTransPayment();
			$payment = $frm_payment->get_one( $payment_id );

			$captured = FrmStrpApiHelper::capture_charge( $payment->receipt_id );
			if ( $captured ) {
				FrmTransPaymentsController::change_payment_status( $payment, 'complete' );
				$message = __( 'Captured', 'formidable-stripe' );
			} else {
				$message = __( 'Failed', 'formidable-stripe' );
			}
		} else {
			$message = __( 'Oops! No payment was selected for the charge.', 'formidable-stripe' );
		}

		echo $message;
		wp_die();
	}
}

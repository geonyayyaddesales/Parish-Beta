<?php

class FrmStrpEventsController {

	private $event;
	private $invoice;
	private $charge;
	private $status;

	public function process_event() {
		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents('php://input');
		$event_json = json_decode( $input );

		// for extra security, retrieve from the Stripe API
		if ( isset( $event_json->id ) ) {
			$mode = isset( $event_json->livemode ) && $event_json->livemode ? 'live' : 'test';
			FrmStrpApiHelper::initialize_api( $mode );
			status_header( 200 );

			$this->event = FrmStrpApiHelper::get_event( $event_json->id );
			if ( empty( $this->event ) ) {
				$response = 'No event found for '. $event_json->id;
				FrmTransLog::log_message( $response );
				echo json_encode( array( 'response' => $response, 'success' => false ) );
				wp_die();
			}

			$this->invoice = $this->event->data->object;
			$this->charge = isset( $this->invoice->charge ) ? $this->invoice->charge : false;

			$events = array(
				'invoice.payment_succeeded' => 'complete',
				'invoice.payment_failed'    => 'failed',
				'charge.refunded'           => 'refunded',
				'charge.captured'           => 'complete',
			);

			if ( isset( $events[ $this->event->type ] ) ) {
				$this->status = $events[ $this->event->type ];
				$this->set_payment_status();
			} elseif ( $this->event->type == 'customer.subscription.deleted' ) {
				$this->subscription_canceled();
			} elseif ( $this->event->type == 'customer.subscription.updated' ) {
				$this->maybe_subscription_canceled();
			}
		} else {
			FrmTransLog::log_message( 'No id found in the received content: '. print_r( $event_json, 1 ) );
			status_header( 500 );
		}

		wp_die();
	}

	private function set_payment_status() {
		if ( $this->status == 'refunded' ) {
			$this->charge = $this->invoice->id;
		}

		$frm_payment = new FrmTransPayment();
		$payment = false;
		if ( $this->charge ) {
			$payment = $frm_payment->get_one_by( $this->charge, 'receipt_id' );
		}
		if ( ! $payment && $this->status == 'refunded' ) {
			// if the refunded payment doesn't exist, stop here
			FrmTransLog::log_message( 'No action taken. The refunded payment does not exist' );
			echo json_encode( array('response' => 'no payment exists', 'success' => false ) );
			return;
		}

		$run_triggers = false;

		if ( ! $payment ) {
			$payment = $this->prepare_from_invoice();
			$run_triggers = true;
		} elseif ( $payment->status != $this->status ) {
			$payment_values = (array) $payment;

			$is_partial_refund = $this->is_partial_refund();
			if ( $is_partial_refund ) {
				$this->set_partial_refund( $payment_values );
				$amount_refunded = number_format( $this->invoice->amount_refunded / 100, 2 );
				$note = sprintf( __( 'Payment partially refunded %s', 'formidable-payments' ), $amount_refunded );
			} else {
				$payment_values['status'] = $payment->status = $this->status;
				$note = sprintf( __( 'Payment %s', 'formidable-payments' ), $payment_values['status'] );
			}

			FrmTransAppHelper::add_note_to_payment( $payment_values, $note );

			$u = $frm_payment->update( $payment->id, $payment_values );

			echo json_encode( array( 'response' => 'Payment ' . $payment->id . ' was updated', 'success' => true ) );
			if ( ! $is_partial_refund ) {
				$run_triggers = true;
			}
		}

		if ( $run_triggers && $payment && $payment->action_id ) {
			FrmTransActionsController::trigger_payment_status_change( array(
				'status' => $this->status, 'payment' => $payment,
			) );
		}
	}

	private function maybe_subscription_canceled() {
		if ( $this->invoice->cancel_at_period_end == true ) {
			$this->subscription_canceled( 'future_cancel' );
		}
	}

	private function subscription_canceled( $status = 'canceled' ) {
		$sub = $this->get_subscription( $this->invoice->id );
		if ( ! $sub ) {
			return false;
		}

		if ( $sub->status == $status ) {
			FrmTransLog::log_message( 'No action taken since the subscription is already canceled.' );
			echo json_encode( array(
				'response' => 'Already canceled',
				'success'  => true,
			) );
			return false;
		}

		FrmTransSubscriptionsController::change_subscription_status( array(
			'status' => $status,
			'sub'    => $sub,
		) );
	}

	private function prepare_from_invoice() {
		if ( empty( $this->invoice->subscription ) ) {
			// this isn't a subscription
			FrmTransLog::log_message( 'No action taken since this is not a subscription.' );
			echo json_encode( array( 'response' => 'Invoice missing', 'success' => false ) );
			return false;
		}

		$sub = $this->get_subscription( $this->invoice->subscription );
		if ( ! $sub ) {
			return false;
		}

		$payment = $this->get_payment_for_sub( $sub->id );

		$payment_values = (array) $payment;
		$this->set_payment_values( $payment_values );

		$frm_payment = new FrmTransPayment();

		$is_first_payment = ( $payment->receipt_id == '' );
		if ( $is_first_payment ) {
			// the first payment for the subscription needs to be updated with the receipt id
			$frm_payment->update( $payment->id, $payment_values );
			$payment_id = $payment->id;
		} else {
			// if this isn't the first, create a new payment
			$payment_id = $frm_payment->create( $payment_values );
		}

		$this->update_next_bill_date( $sub, $payment_values );

		$payment = $frm_payment->get_one( $payment_id );
		return $payment;
	}

	private function get_subscription( $sub_id ) {
		$frm_sub = new FrmTransSubscription();
		$sub = $frm_sub->get_one_by( $sub_id, 'sub_id' );
		if ( ! $sub ) {
			// If this isn't an existing subscription, it must be a charge for another site/plugin
			FrmTransLog::log_message( 'No action taken since there is not a matching subscription for ' . $sub_id );
			echo json_encode( array(
				'response' => 'Invoice missing',
				'success' => false,
			) );
		}

		return $sub;
	}

	private function get_payment_for_sub( $sub_id ) {
		$frm_payment = new FrmTransPayment();
		return $frm_payment->get_one_by( $sub_id, 'sub_id' );
	}

	private function set_payment_values( &$payment_values ) {
		$payment_values['begin_date'] = date( 'Y-m-d' );
		$payment_values['expire_date'] = '0000-00-00';

		foreach ( $this->invoice->lines->data as $line ) {
			$payment_values['amount']      = number_format( ( $line->amount / 100 ), 2, '.', '' );
			$payment_values['begin_date']  = date( 'Y-m-d', $line->period->start );
			$payment_values['expire_date'] = date( 'Y-m-d', $line->period->end );
		}

		$payment_values['receipt_id']  = $this->charge ? $this->charge : __( 'None', 'formidable-stripe' );
		$payment_values['status']      = $this->status;

		$payment_values['meta_value'] = array();
		$payment_values['created_at'] = current_time( 'mysql', 1 );

		FrmTransAppHelper::add_note_to_payment( $payment_values );
	}

	private function update_next_bill_date( $sub, $payment ) {
		$frm_sub = new FrmTransSubscription();
		if ( $payment['status'] == 'complete' ) {
			$frm_sub->update( $sub->id, array( 'next_bill_date' => $payment['expire_date'] ) );
		} elseif ( $payment['status'] == 'refunded' ) {
			$frm_sub->update( $sub->id, array( 'next_bill_date' => $payment['begin_date'] ) );
		}
	}

	private function is_partial_refund() {
		$partial = false;
		if ( $this->status == 'refunded' ) {
			$amount = $this->invoice->amount;
			$amount_refunded = $this->invoice->amount_refunded;
			$partial = ( $amount != $amount_refunded );
		}
		return $partial;
	}

	private function set_partial_refund( &$payment_values ) {
		$payment_values['amount'] = $this->invoice->amount - $this->invoice->amount_refunded;
		$payment_values['amount'] = number_format( $payment_values['amount'] / 100, 2 );
	}
}

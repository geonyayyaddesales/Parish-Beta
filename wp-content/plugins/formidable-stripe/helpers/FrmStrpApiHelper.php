<?php

class FrmStrpApiHelper {

	public static function initialize_api( $mode = 'auto' ) {
		$settings = new FrmStrpSettings();

		// check to see if we are in test mode
		if ( $mode != 'auto' ) {
			$setting_name = $mode . '_secret';
			$secret_key = $settings->settings->$setting_name;
		} else if ( $settings->settings->test_mode ) {
			$secret_key = $settings->settings->test_secret;
		} else {
			$secret_key = $settings->settings->live_secret;
		}

		$success = false;
		try {
			\Stripe\Stripe::setApiKey( $secret_key );
			$success = true;
		} catch ( Exception $e ) {
			FrmTransLog::log_message( 'Stripe API initialization failed.' );
		}

		return $success;
	}

	/**
	 * For compatibility with all api versions, we'll need to force it sometimes
	 * @since 1.15
	 */
	public static function set_api_version( $version ) {
		try {
			\Stripe\Stripe::setApiVersion( $version );
		} catch ( Exception $e ) {
			FrmTransLog::log_message( 'Stripe API version could not be set.' );
		}
	}

	public static function cancel_subscription( $sub_id ) {
		self::initialize_api();
		try {
			$sub = \Stripe\Subscription::retrieve( $sub_id );
			if ( current_user_can('administrator') ) {
				$cancel = $sub->cancel( array( 'at_period_end' => true ) );
			} else {
				$customer = self::get_customer();
				if ( is_object( $customer ) && $sub->customer == $customer->id ) {
					$cancel = $sub->cancel( array( 'at_period_end' => true ) );
				} else {
					$cancel = false;
				}
			}
		} catch ( Exception $e ) {
			FrmTransLog::log_message( $e->getMessage() );
			$cancel = false;
		}

		return $cancel && ( $cancel->status == 'canceled' || $cancel->cancel_at_period_end == true );
	}

	public static function refund_payment( $payment_id ) {
		self::initialize_api();
		try {
			\Stripe\Refund::create( array( 'charge' => $payment_id ) );
			$refunded = true;
		} catch ( Exception $e ) {
			FrmTransLog::log_message( $e->getMessage() );
			$refunded = false;
		}
		return $refunded;
	}

	/**
	 * Get the payment from Stripe
	 *
	 * @since 1.15
	 * @param string $payment_id - The Stripe payment id
	 */
	public static function get_charge( $payment_id ) {
		self::initialize_api();
		try {
			$payment = \Stripe\Charge::retrieve( $payment_id );
		} catch ( Exception $e ) {
			FrmTransLog::log_message( $e->getMessage() );
			$payment = false;
		}
		return $payment;
	}

	/**
	 * Check if a charge has already be authorized
	 *
	 * @since 1.15
	 * @param string $charge_id - The Stripe charge id
	 * @return boolean
	 */
	public static function can_by_captured( $charge_id ) {
		$charge = self::get_charge( $charge_id );
		return ! empty( $charge ) && ! $charge->captured && $charge->status === 'succeeded';
	}

	/**
	 * @since 1.15
	 * @param string $charge_id - The id of the Stripe charge
	 * @return boolean
	 */
	public static function capture_charge( $charge_id ) {
		self::initialize_api();
		try {
			$payment = \Stripe\Charge::retrieve( $charge_id );
			$payment->capture();
			$charged = true;
		} catch ( Exception $e ) {
			FrmTransLog::log_message( $e->getMessage() );
			$charged = false;
		}
		return $charged;
	}

	public static function get_customer_subscriptions() {
		$customer = self::get_customer();
		if ( is_object( $customer ) ) {
			$subscriptions = \Stripe\Subscription::all( array( 'customer' => $customer->id ) );
		} else {
			$subscriptions = array();
		}

		return $subscriptions;
	}

	public static function get_customer( $options = array() ) {
		$customer_id = false;
		$user_id = 0;
		if ( isset( $options['user_id'] ) && $options['user_id'] ) {
			$user_id = $options['user_id'];
		} elseif ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		}

		if ( isset( $options['user_id'] ) ) {
			unset( $options['user_id'] );
		}

		$meta_name = self::get_customer_id_meta_name();
		if ( $user_id ) {
			$customer_id = get_user_meta( $user_id, $meta_name, true );
			if ( ! isset( $options['email'] ) ) {
				$user_info = get_userdata( $user_id );
				$options['email'] = $user_info->user_email;
			}
		}

		try {
			if ( $customer_id ) {
				$customer = \Stripe\Customer::retrieve( $customer_id );
				if ( is_object( $customer ) && isset( $options['source'] ) ) {
					$customer->source = $options['source'];
					$customer->save();
				}
			} else {
				$customer = \Stripe\Customer::create( $options );

				if ( is_object( $customer ) && isset( $user_id ) ) {
					update_user_meta( $user_id, $meta_name, $customer->id );
				}
			}
		} catch ( \Stripe\Error\Card $e ) {
			$customer = self::get_stripe_exception( $e );
			if ( $customer_id && strpos( $customer, 'No such customer' ) !== false ) {
				FrmTransLog::log_message( 'Reset customer id for user #' . $user_id );
				delete_user_meta( $user_id, $meta_name );
			}
		} catch( \Stripe\Error\Base $e ) {
			$customer = self::get_stripe_exception( $e );
		} catch ( Exception $e ) {
			FrmTransLog::log_message( $e->getMessage() );
			$customer = $e->getMessage();
		}

		return $customer;
	}

	public static function get_customer_by_id( $user_id ) {
		self::initialize_api();
		$meta_name = self::get_customer_id_meta_name();
		$customer_id = get_user_meta( $user_id, $meta_name, true );

		if ( $customer_id ) {
			try {
				$customer = \Stripe\Customer::retrieve( $customer_id );
				if ( isset( $customer->deleted ) && $customer->deleted ) {
					$customer = false;
					delete_user_meta( $user_id, $meta_name );
				}
			} catch ( Exception $e ) {
				$customer = false;
			}
		} else {
			$customer = false;
		}

		return $customer;
	}

	/**
	 * If test mode is running, save the id somewhere else
	 */
	private static function get_customer_id_meta_name() {
		$settings = new FrmStrpSettings();
		$meta_name = '_frmstrp_customer_id';

		if ( $settings->settings->test_mode ) {
			$meta_name .= '_test';
		}

		return $meta_name;
	}

	public static function get_cards( $user_id ) {
		$cards = array();

		if ( ! empty( $user_id ) ) {
			$customer = self::get_customer_by_id( $user_id );

			if ( $customer ) {
				$saved_cards = $customer->sources->all( array( 'object' => 'card' ) );
				$default_card = $customer->default_source;

				foreach ( $saved_cards->data as $card ) {
					$cards[ $card->id ] = array(
						'card'    => $card,
						'default' => ( $card->id === $default_card ),
					);
				}
			}
		}

		return $cards;
	}

	public static function delete_card( $card_id ) {
		$response = array( 'success' => false, 'error' => '' );
		$user_id = get_current_user_id();

		if ( $user_id ) {
			$customer = self::get_customer_by_id( $user_id );
			if ( $customer ) {
				try {
					$stripe_response = $customer->sources->retrieve( $card_id )->delete();
					$response['success'] = $stripe_response->deleted;
				} catch ( \Stripe\Error\Base $e ) {
					self::get_stripe_exception( $e );
				} catch ( Exception $e ) {
					$response['error'] = $e->getMessage();
					FrmTransLog::log_message( $response['error'] );
				}
			}
		} else {
			$response['error'] = 'User is not logged in';
		}

		return $response;
	}

	public static function get_event( $id ) {
		$event = false;
		try {
			$event = \Stripe\Event::retrieve( $id );
		} catch ( \Stripe\Error\Base $e ) {
			self::get_stripe_exception( $e );
		} catch ( Exception $e ) {
			FrmTransLog::log_message( $e->getMessage() );
		}

		return $event;
	}

	public static function get_stripe_exception( $e ) {
		$body = $e->getJsonBody();
		$error = $body['error'];
		FrmTransLog::log_message( print_r( $error, 1 ) );
		$error['code'] = apply_filters( 'frm_stripe_error_code', $error['code'], $e );
		$message = self::get_translated_error( $error['code'], $error['message'] );
		return esc_html( $message );
	}

	private static function get_translated_error( $code, $message = '' ) {
		if ( $message !== '' ) {
			$locale = get_locale();
			if ( $locale === 'en_US' ) {
				return $message;
			}
		}

		$messages = array(
			'incorrect_number'    => __( 'The card number is incorrect.', 'formidable-stripe' ),
			'invalid_number'      => __( 'The card number is not a valid credit card number.', 'formidable-stripe' ),
			'invalid_expiry_month' => __( 'The card\'s expiration month is invalid.', 'formidable-stripe' ),
			'invalid_expiry_year' => __( 'The card\'s expiration year is invalid.', 'formidable-stripe' ),
			'invalid_cvc'         => __( 'The card\'s security code is invalid.', 'formidable-stripe' ),
			'expired_card'        => __( 'The card has expired.', 'formidable-stripe' ),
			'incorrect_cvc'       => __( 'The card\'s security code is incorrect.', 'formidable-stripe' ),
			'incorrect_zip'       => __( 'The card\'s zip code failed validation.', 'formidable-stripe' ),
			'card_declined'       => __( 'The card was declined.', 'formidable-stripe' ),
			'missing'             => __( 'There is no card on a customer that is being charged.', 'formidable-stripe' ),
			'processing_error'    => __( 'An error occurred while processing the card.', 'formidable-stripe' ),
			'rate_limit'          => __( 'An error occurred due to requests hitting the API too quickly. Please let us know if you\'re consistently running into this error.', 'formidable-stripe' ),
			'invalid_swipe_data'  => __( 'The card\'s swipe data is invalid.', 'formidable-stripe' ),
			'rate_limit_error'    => __( 'Too many requests hit the API too quickly.', 'formidable-stripe' ),
			'invalid_request_error' => __( 'Invalid request errors arise when your request has invalid parameters.', 'formidable-stripe' ),
			'authentication_error' => __( 'Failed to properly authenticate in the request.', 'formidable-stripe' ),
			'api_connection_error' => __( 'Failed to connect to Stripe\'s API. This usually means you have an issue with TLS 1.2 on your server.', 'formidable-stripe' ),
		);

		$messages = apply_filters( 'frm_stripe_error_messages', $messages );

		if ( isset( $messages[ $code ] ) ) {
			$message = $messages[ $code ];
		}

		return $message;
	}
}

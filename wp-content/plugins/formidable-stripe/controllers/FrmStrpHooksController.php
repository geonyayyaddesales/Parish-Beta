<?php

class FrmStrpHooksController {

	public static function load_hooks() {
		if ( is_admin() ) {
			add_action( 'admin_init', 'FrmStrpAppController::include_updater', 1 );

			add_filter( 'frm_pay_action_defaults', 'FrmStrpActionsController::add_action_defaults' );
			add_action( 'frm_pay_show_stripe_options', 'FrmStrpActionsController::add_action_options' );
			add_filter( 'frm_before_save_payment_action', 'FrmStrpActionsController::before_save_settings' );

			if ( defined( 'DOING_AJAX' ) ) {
				$frmStrpEventsController = new FrmStrpEventsController();
				add_action( 'wp_ajax_nopriv_frm_strp_event', array( &$frmStrpEventsController, 'process_event' ) );
				add_action( 'wp_ajax_frm_strp_event', array( &$frmStrpEventsController, 'process_event' ) );
				add_action( 'wp_ajax_frm_trans_capture', 'FrmStrpPaymentsController::capture_charge' );
			}

			add_filter( 'frm_pay_stripe_receipt', 'FrmStrpPaymentsController::get_receipt_link' );

			add_action( 'frm_add_settings_section', 'FrmStrpSettingsController::add_settings_section' );
			add_action( 'frm_pay_stripe_sidebar', 'FrmStrpPaymentsController::show_capture_link' );
		}

		add_action( 'plugins_loaded', 'FrmStrpAppController::load_lang' );
		register_activation_hook( dirname( dirname( __FILE__ ) ) . '/formidable-stripe.php', 'FrmStrpAppController::install' );

		add_filter( 'frm_payment_gateways', 'FrmStrpAppController::add_gateway' );
		add_action( 'rest_api_init', 'FrmStrpAppController::add_api_routes' );

		add_action( 'frm_entry_form', 'FrmStrpActionsController::add_hidden_token_field' );
		add_filter( 'frm_validate_credit_card_field_entry', 'FrmStrpActionsController::remove_cc_validation', 20, 3 );
		add_action( 'frm_enqueue_form_scripts', 'FrmStrpActionsController::maybe_load_scripts' );
		add_action( 'frm_enqueue_stripe_scripts', 'FrmStrpActionsController::load_scripts' );

		add_shortcode( 'frm-stripe-cards', 'FrmStrpPaymentsController::manage_cards' );

		add_filter( 'frm_include_credit_card', '__return_true' );
	}
}

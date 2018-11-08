<?php

class FrmStrpAppController {

    public static function load_lang() {
        load_plugin_textdomain( 'formidable-stripe', false, FrmStrpAppHelper::plugin_folder() . '/languages/' );
    }

    public static function include_updater() {
		if ( class_exists( 'FrmAddon' ) ) {
			FrmStrpUpdate::load_hooks();
		}
    }

	public static function install( $old_db_version = false ) {
		FrmTransAppController::install( $old_db_version );
	}

	public static function add_gateway( $gateways ) {
		$gateways['stripe'] = array(
			'label'     => 'Stripe',
			'user_label' => __( 'Credit Card', 'formidable-stripe' ),
			'class'     => 'Strp',
			'recurring' => true,
			'include'   => array(
				'billing_first_name', 'billing_last_name',
				'credit_card', 'billing_address',
			),
		);
		return $gateways;
	}

	public static function add_api_routes() {
		register_rest_route( 'frm-strp/v1', '/card/(?P<id>[a-z0-9 _]+)', array(
			'methods'  => 'DELETE',
			'callback' => array( 'FrmStrpPaymentsController', 'delete_card' ),
			'permission_callback' => 'is_user_logged_in',
		) );
	}
}

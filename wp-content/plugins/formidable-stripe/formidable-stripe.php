<?php
/*
Plugin Name: Formidable Stripe
Description: Collect Stripe payments using your Formidable Forms
Version: 1.16
Plugin URI: https://formidableforms.com/
Author URI: https://formidableforms.com/
Author: Strategy11
Text Domain: formidable-stripe
*/

function frm_strp_autoloader( $class_name ) {
	$is_stripe_class = preg_match( '/^Stripe\\\\.+$/', $class_name );
	$is_frm_strp_class = preg_match( '/^FrmStrp.+$/', $class_name );

    // Only load Frm classes here
	if ( ! $is_frm_strp_class && ! $is_stripe_class ) {
        return;
    }

    $filepath = dirname(__FILE__);

	if ( $is_stripe_class ) {
		$class_names = str_replace( 'Stripe\\', '', $class_name );
		$class_names = explode( '\\', $class_names );
		$class_name = implode( '/', $class_names );

		$filepath .= '/lib/';
	} else {
		if ( preg_match( '/^.+Helper$/', $class_name ) ) {
			$filepath .= '/helpers/';
		} elseif ( preg_match( '/^.+Controller$/', $class_name ) ) {
			$filepath .= '/controllers/';
		} else {
			$filepath .= '/models/';
		}
	}

	$filepath .= $class_name .'.php';

    if ( file_exists($filepath) ) {
        include($filepath);
    }
}

// Add the autoloader
spl_autoload_register('frm_strp_autoloader');

FrmStrpHooksController::load_hooks();

if ( ! function_exists( 'frm_trans_autoloader' ) ) {
	include( dirname( __FILE__ ) . '/formidable-payments/formidable-payments.php' );
}

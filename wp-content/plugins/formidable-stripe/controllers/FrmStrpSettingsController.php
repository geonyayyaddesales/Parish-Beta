<?php

class FrmStrpSettingsController {

    public static function add_settings_section( $sections ) {
        $sections['stripe'] = array( 'class' => __CLASS__, 'function' => 'route' );
        return $sections;
    }

	public static function route() {
		$action = FrmAppHelper::get_param('action');
		if ( $action == 'process-form' ) {
			return self::process_form();
		} else {
			return self::global_settings_form();
		}
	}


	public static function global_settings_form( $atts = array() ) {
		$default_atts = array( 'errors' => array(), 'message' => '' );
		$atts = array_merge( $atts, $default_atts );
		$errors = $atts['errors'];
		$message = $atts['message'];

		$keys = array(
			'test_secret'  => __( 'Test Secret Key', 'formidable-stripe' ),
			'test_publish' => __( 'Test Publishable Key', 'formidable-stripe' ),
			'live_secret'  => __( 'Live Secret Key', 'formidable-stripe' ),
			'live_publish' => __( 'Live Publishable Key', 'formidable-stripe' ),
		);

		$settings = new FrmStrpSettings();

		include( FrmStrpAppHelper::plugin_path() . '/views/settings/form.php' );
	}

	public static function process_form() {
		$atts = array( 'errors' => array(), 'message' => '' );

		$settings = new FrmStrpSettings();
		$settings->update( $_POST );

		if ( empty( $atts['errors'] ) ) {
			$settings->store();
			$atts['message'] = __( 'Settings Saved', 'formidable-stripe' );
		}
            
		self::global_settings_form( $atts );
	}
}

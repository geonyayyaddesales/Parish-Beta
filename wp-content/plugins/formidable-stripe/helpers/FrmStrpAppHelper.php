<?php

class FrmStrpAppHelper {

	public static function plugin_path() {
		return dirname( dirname( __FILE__ ) );
	}

    public static function plugin_folder() {
        return basename( self::plugin_path() );
    }

    public static function plugin_url() {
		return plugins_url( '', self::plugin_path() . '/formidable-stripe.php' );
    }

	public static function is_debug() {
		return defined('WP_DEBUG') && WP_DEBUG;
	}
}

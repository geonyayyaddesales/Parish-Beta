<?php

class FrmStrpUpdate extends FrmAddon {

	public $plugin_file;
	public $plugin_name = 'Stripe';
	public $download_id = 310430;
	public $version = '1.16';

	public function __construct() {
		$this->plugin_file = dirname( dirname( __FILE__ ) ) . '/formidable-stripe.php';
		parent::__construct();
	}

	public static function load_hooks() {
		add_filter( 'frm_include_addon_page', '__return_true' );
		new FrmStrpUpdate();
	}

}

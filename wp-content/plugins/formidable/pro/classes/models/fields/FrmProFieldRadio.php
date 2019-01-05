<?php

/**
 * @since 3.0
 */
class FrmProFieldRadio extends FrmFieldRadio {
	
	protected function field_settings_for_type() {
		$settings = parent::field_settings_for_type();

		$settings['default_blank'] = false;
		$settings['read_only'] = true;

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}
}

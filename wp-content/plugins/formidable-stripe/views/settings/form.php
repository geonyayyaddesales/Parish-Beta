<table class="form-table">
	<tr class="form-field" valign="top">
		<td class="frm_left_label">
			<label><?php esc_html_e( 'Test Mode', 'formidable-stripe' ) ?></label>
		</td>
		<td>
			<label for="frm_strp_test_mode">
				<input type="checkbox" name="frm_strp_test_mode" id="frm_strp_test_mode" value="1" <?php checked(  $settings->settings->test_mode, 1 ) ?> />
				<?php esc_html_e( 'Use the Stripe test mode', 'formidable-stripe' ) ?>
			</label>
			<?php if ( ! is_ssl() ) { ?>
				<br/><em><?php esc_html_e( 'Your site is not using SSL. Before using Stripe to collect live payments, you will need to install an SSL certificate on your site.', 'formidable-stripe' ) ?></em>
			<?php } ?>
		</td>
	</tr>
	<?php foreach ( $keys as $key => $label ) { ?>
	<tr class="form-field" valign="top">
		<td class="frm_left_label">
			<label for="frm_strp_<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $label ) ?></label>
		</td>
		<td>
			<input type="text" name="frm_strp_<?php echo esc_attr( $key ) ?>" id="frm_strp_<?php echo esc_attr( $key ) ?>" value="<?php echo esc_attr( $settings->settings->{$key} ) ?>" class="regular_text" />
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td class="frm_left_label">
			<?php esc_html_e( 'Automatic Processing', 'formidable-stripe' ); ?>
		</td>
		<td>
			<?php esc_html_e( 'Stripe notifies your site of any recurring payments, refunds issued, and failed payments. In order to receive these notifications, you must add a new Webhook URL for your site in your Stripe Dashboard > Settings > Webhooks. The URL should be set to:', 'formidable-stripe' ) ?>
			<pre><?php echo esc_url_raw( admin_url('admin-ajax.php?action=frm_strp_event') ) ?></pre>
		</td>
	</tr>
</table>
    



<?php

/**
 * Provide a admin options area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      0.0.4
 */
?>
<!--
<div class="wrap">

	<?php
	if ( !current_user_can('manage_options') )
		wp_die( _e( 'Silence is golden', 'market-exporter' ) );
	?>
	
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
		
  <form method="post" action="options.php">
	  
	  <?php $settings = get_option( 'market_exporter_shop_settings' ); ?>
	  <?php settings_fields( $this->plugin_name ); ?>

		<h3><?php _e( '&lt;shop&gt; element settings', 'market-exporter' ); ?></h3>
		<p><?php _e( 'Settings that are only used in the <b>shop</b> section of the YML file. Website name is the <i>name</i> field, Company name is used for the <i>company</i> field.', 'market-exporter' ); ?></p>

	  <table class="form-table">
		  <tr>
			  <th scope="row"><?php _e( 'Website Name', 'market-exporter' );?></th>
			  <td>
				  <input type="text" name="market_exporter_shop_settings[website_name]"
														 id="market_exporter_shop_settings[website_name]"
														 value="<?=esc_attr( $settings['website_name'] ); ?>"
														 maxlength="20">
					<p><small><?php _e( 'Not longer than 20 characters. Has to be the name of the shop, that is configured in Yandex Market.', 'market-exporter' ); ?></small></p>
				</td>
		  </tr>
		  <tr>
			  <th scope="row"><?php _e( 'Company Name', 'market-exporter' );?></th>
			  <td>
				  <input type="text" name="market_exporter_shop_settings[company_name]"
														 id="market_exporter_shop_settings[company_name]"
														 value="<?=esc_attr( $settings['company_name'] ); ?>">
					<p><small><?php _e( 'Full company name. Not published in Yandex Market.', 'market-exporter' ); ?></small></p>
				</td>
		  </tr>
	  </table>

    <?php submit_button(); ?>
  </form>

</div>
-->
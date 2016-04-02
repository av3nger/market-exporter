<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      0.0.1
 */
?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php
	if ( !empty( $_POST[ $this->plugin_name.'-generate' ] ) ) {
		if ( !current_user_can('manage_options') )
			wp_die( _e( 'Silence is golden', 'market-exporter' ) );
			
		check_admin_referer( $this->plugin_name.'-generate' );

		$return_code = $this->generate_YML();
		switch ($return_code) {
			case 100:
				echo ' <p>' . sprintf( __( 'Currently only the following currency is supported: Russian Ruble (RUB), Ukrainian Hryvnia (UAH), US Dollar (USD) and Euro (EUR). Please <a href="%s">update currency</a>.', 'market-exporter' ), admin_url( 'admin.php?page=wc-settings' ) ) . '</p>';
				break;
			case 200:
				echo '	<p>' . sprintf( __( 'Unable to find any products. Are you sure <a href="%s">some exist</a>?', 'market-exporter' ), admin_url( 'post-new.php?post_type=product' ) ) . '</p>';
				break;
			default:
				echo '	<p>' . sprintf( __( 'File exported successfully: <a href="%s">%s</a>.', 'market-exporter' ), $return_code, $return_code ) . '</p>';
		}

	// Display the form by default.
	} else {
	?>
		<form method="post" action="">
		<?php wp_nonce_field( $this->plugin_name.'-generate' ) ?>
		<p><?php _e( 'This plugin is used to generate a valid YML file for exporting your products in WooCommerce to Yandex Market.', 'market-exporter' ); ?></p>	
						
		<p><?php _e( 'Please be patient while the YML file is generated. This can take a while if your server is slow (inexpensive hosting) or if you have many products in WooCommerce. Do not navigate away from this page until this script is done or the YML file will not be created. You will be notified via this page when the process is completed.', 'market-exporter' ); ?></p>	

		<p><?php _e( 'To begin, just press the button below.', 'market-exporter'); ?></p>
		
		<p><input type="submit" class="button hide-if-no-js" name="market-exporter-generate" id="market-exporter-generate" value="<?php _e( 'Generate YML file', 'market-exporter' ) ?>" /></p>
		
		<noscript><p><em><?php _e( 'You must enable Javascript in order to proceed!', 'market-exporter' ); ?></em></p></noscript>
		
		</form><br>
		
		<?php
		// If someone clicks on Delete file button.
		if ( !empty( $_POST[ $this->plugin_name.'-delete' ] ) ) {
			if ( isset( $_POST['files'] ) )
				$this->delete_files( $_POST['files'] );
		}			
		?>

		<h2><?php _e( 'Generated YML files:', 'market-exporter' ); ?></h2>
		<form method="post" action="" name="list-files" id="market-exporter">
			<?php wp_nonce_field( $this->plugin_name ) ?>
			
			<table>
				<tr>
					<td class="id"><input type="checkbox" onClick="toggle(this)"></td>
					<td class="name"><?php _e( 'File name', 'market-exporter' ); ?></td>
					<td class="link"><?php _e( 'Action', 'market-exporter' ); ?></td>
				</tr>
				<?php
				$upload_dir = wp_upload_dir();
				$folder = trailingslashit( $upload_dir['baseurl'] ).trailingslashit( $this->plugin_name );
				
				$files = $this->get_files();
				if ( $files ):
					foreach( $files as $file ):
					?>
					<tr>
						<td><input type="checkbox" name="files[]" value="<?=$file['name'];?>"></td>
						<td><?=$file['name'];?></td>
						<td><a href="<?=$folder.$file['name'];?>" target="_blank"><?php _e( 'Open file', 'market-exporter' ); ?></a></td>
					</tr>
					<?php endforeach;
				endif; ?>
			</table>
			
			<p><input type="submit" class="button hide-if-no-js" name="market-exporter-delete" id="market-exporter-delete123" value="<?php _e( 'Delete selected files', 'market-exporter' ) ?>" /></p>
			
			<noscript><p><em><?php _e( 'You must enable Javascript in order to proceed!', 'market-exporter' ); ?></em></p></noscript>
		</form>
	<?php
	}
	?>
</div>
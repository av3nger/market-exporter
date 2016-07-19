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
			wp_die( _e( 'Silence is golden', $this->plugin_name ) );
			
		check_admin_referer( $this->plugin_name.'-generate' );

		// Select what version of plugin to use.
		$plugin_option = get_option( 'market_exporter_shop_settings' );
		if ( isset ( $plugin_option['develop'] ) && ( $plugin_option['develop'] == 'yes' ) ) {
			$ME = new ME_WC();
		} else {
			$ME = new Market_Exporter_YML( $this->plugin_name );
		}

		$return_code = $ME->generate_YML();

		// TODO: remove this before production
		echo "<pre>";
		//print_r( $return_code );
		echo "</pre>";

		//wp_die();
		switch ($return_code) {
			case 100:
				echo ' <p>' . sprintf( __( 'Currently only the following currency is supported: Russian Ruble (RUB), Ukrainian Hryvnia (UAH), US Dollar (USD) and Euro (EUR). Please <a href="%s">update currency</a>.', $this->plugin_name ), admin_url( 'admin.php?page=wc-settings' ) ) . '</p>';
				break;
			case 200:
				echo ' <p>' . sprintf( __( 'No shipping methods are available. Please <a href="%s">update or add at least one</a>.', $this->plugin_name ), admin_url( 'admin.php?page=wc-settings&tab=shipping' ) ) . '</p>';
				break;
			case 300:
				echo '	<p>' . sprintf( __( 'Unable to find any products. Are you sure <a href="%s">some exist</a>?', $this->plugin_name ), admin_url( 'post-new.php?post_type=product' ) ) . '</p>';
				break;
			default:
				echo '	<p>' . sprintf( __( 'File exported successfully: <a href="%s">%s</a>.', $this->plugin_name ), $return_code, $return_code ) . '</p>';
		}

	// Display the form by default.
	} else {
	?>
		<form method="post" action="">
		<?php wp_nonce_field( $this->plugin_name.'-generate' ) ?>
		<p><?php _e( 'This plugin is used to generate a valid YML file for exporting your products in WooCommerce to Yandex Market.', $this->plugin_name ); ?></p>
						
		<p><?php _e( 'Please be patient while the YML file is generated. This can take a while if your server is slow (inexpensive hosting) or if you have many products in WooCommerce. Do not navigate away from this page until this script is done or the YML file will not be created. You will be notified via this page when the process is completed.', $this->plugin_name ); ?></p>

		<p><?php _e( 'To begin, just press the button below.', $this->plugin_name); ?></p>
		
		<p><input type="submit" class="button hide-if-no-js" name="market-exporter-generate" id="market-exporter-generate" value="<?php _e( 'Generate YML file', $this->plugin_name ) ?>" /></p>
		
		<noscript><p><em><?php _e( 'You must enable Javascript in order to proceed!', $this->plugin_name ); ?></em></p></noscript>
		
		</form>
		
		<?php
		// If someone clicks on Delete file button.
		$market_exporter_fs = new Market_Exporter_FS( $this->plugin_name );
		if ( !empty( $_POST[ $this->plugin_name.'-delete' ] ) ) {
			if ( isset( $_POST['files'] ) )
				$market_exporter_fs->delete_files( $_POST['files'] );
		}			
		?>

		<h2><?php _e( 'Generated YML files:', 'market-exporter' ); ?></h2>
		<form method="post" action="" name="list-files" id="market-exporter">
			<?php wp_nonce_field( $this->plugin_name ) ?>
			
			<table>
				<tr>
					<td class="id"><input type="checkbox" onClick="toggle(this)"></td>
					<td class="name"><?php _e( 'File name', $this->plugin_name ); ?></td>
					<td class="link"><?php _e( 'Action', $this->plugin_name ); ?></td>
				</tr>
				<?php
				$upload_dir = wp_upload_dir();
				$folder = trailingslashit( $upload_dir['baseurl'] ).trailingslashit( $this->plugin_name );

				$files = $market_exporter_fs->get_files();
				if ( $files ):
					foreach( $files as $file ):
					?>
					<tr>
						<td><input type="checkbox" name="files[]" value="<?=$file['name'];?>"></td>
						<td><?=$file['name'];?></td>
						<td><a href="<?=$folder.$file['name'];?>" target="_blank"><?php _e( 'Open file', $this->plugin_name ); ?></a></td>
					</tr>
					<?php endforeach;
				endif; ?>
			</table>
			
			<p><input type="submit" class="button hide-if-no-js" name="market-exporter-delete" id="market-exporter-delete" value="<?php _e( 'Delete selected files', $this->plugin_name ) ?>" /></p>
			
			<noscript><p><em><?php _e( 'You must enable Javascript in order to proceed!', $this->plugin_name ); ?></em></p></noscript>
		</form>
		
		
		<h2><?php _e( 'News:', 'market-exporter' ); ?></h2>
		<strong>Июль 2016:</strong><br><br>
		Несколько слов по поводу последнего обновления 0.2.6. В последней версии WooCommerce были внесены значительные изменения в то как работает доставка, а именно - появились зоны. Пока данный функционал полностью не реализован в плагине, рекомендуется устанавливать параметры доставки в партнерском интерфейсе Яндекс Маркет.<br><br>
		Также, сейчас я работаю над созданием нового сервиса для работы с Яндекс Маркет. Сервис будет работать по API с WooCommerce и интегрироваться с различными сервисами Яндекса. Мне нужны бета-тестеры. Кому интересно, пишите мне на <a href="mailto:a.vanyukov@testor.ru">a.vanyukov@testor.ru</a>.
		
	<?php
	}
	?>
</div>
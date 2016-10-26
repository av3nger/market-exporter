<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      0.0.1
 */
?>

<div class="wrap" id="me_pages">

	<div class="version">
		<?= __( 'Version: ', $this->plugin_name ) . $this->version; ?>
	</div>

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<nav class="nav-tab-wrapper">
		<a href="<?= admin_url( 'admin.php?page=' . $this->plugin_name . '&amp;tab=generate' ); ?>"
		   class="nav-tab <?php if ( !isset( $_GET['tab'] ) || $_GET['tab'] == 'generate' ) echo 'nav-tab-active'; ?>">
			<?php _e( 'Generate file', 'market-exporter' ); ?>
		</a>
		<a href="<?= admin_url( 'admin.php?page=' . $this->plugin_name . '&amp;tab=files' ); ?>"
		   class="nav-tab <?php if ( $_GET['tab'] == 'files' ) echo 'nav-tab-active'; ?>">
			<?php _e('Files', $this->plugin_name); ?>
		</a>
		<a href="<?= admin_url( 'admin.php?page=' . $this->plugin_name . '&amp;tab=settings' ); ?>"
		   class="nav-tab <?php if ( $_GET['tab'] == 'settings' ) echo 'nav-tab-active'; ?>">
			<?php _e('Settings', $this->plugin_name); ?>
		</a>
		<a href="<?= admin_url( 'admin.php?page=' . $this->plugin_name . '&amp;tab=news' ); ?>"
		   class="nav-tab <?php if ( $_GET['tab'] == 'news' ) echo 'nav-tab-active'; ?>">
			<?php _e('News', $this->plugin_name); ?>
		</a>
	</nav>

	<?php // Display general tab.
	if ( !isset( $_GET['tab'] ) || $_GET['tab'] == 'generate' ) :
		if ( !empty( $_POST[ $this->plugin_name.'-generate' ] ) ) :
			if ( !current_user_can('manage_options') )
				wp_die( _e( "You don't have the permission to do this.", $this->plugin_name ) );

			check_admin_referer( $this->plugin_name.'-generate' );

			// Select what version of plugin to use.
			$plugin_option = get_option( 'market_exporter_shop_settings' );
			$ME = new ME_WC();

			$return_code = $ME->generate_YML();

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
		else : ?>
			<form method="post" action="">
			<?php wp_nonce_field( $this->plugin_name.'-generate' ) ?>
			<p><?php _e( 'This plugin is used to generate a valid YML file for exporting your products in WooCommerce to Yandex Market.', $this->plugin_name ); ?></p>

			<p><?php _e( 'Please be patient while the YML file is generated. This can take a while if your server is slow (inexpensive hosting) or if you have many products in WooCommerce. Do not navigate away from this page until this script is done or the YML file will not be created. You will be notified via this page when the process is completed.', $this->plugin_name ); ?></p>

			<p><?php _e( 'To begin, just press the button below.', $this->plugin_name); ?></p>

			<p><input type="submit" class="button button-primary hide-if-no-js" name="market-exporter-generate" id="market-exporter-generate" value="<?php _e( 'Generate YML file', $this->plugin_name ) ?>" /></p>

			<noscript><p><em><?php _e( 'You must enable Javascript in order to proceed!', $this->plugin_name ); ?></em></p></noscript>

			</form>
		<?php endif; ?>

		<!-- end general tab -->
	<?php elseif( $_GET['tab'] == 'files' ) :

		// If someone clicks on Delete file button.
		$market_exporter_fs = new Market_Exporter_FS( $this->plugin_name );
		if ( !empty( $_POST[ $this->plugin_name.'-delete' ] ) ) {
			if ( isset( $_POST['files'] ) )
				$market_exporter_fs->delete_files( $_POST['files'] );
		}
		?>

		<h2><?php _e( 'Generated YML files', 'market-exporter' ); ?></h2>
		<form method="post" action="" name="list-files" id="market-exporter">
			<?php wp_nonce_field( $this->plugin_name ) ?>

			<table class="widefat">
				<thead>
					<tr>
						<th class="row-title id"><input type="checkbox" onClick="toggle(this)"></th>
						<th class="row-title name"><?php _e( 'File name', $this->plugin_name ); ?></th>
						<th class="row-title link"><?php _e( 'Action', $this->plugin_name ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$upload_dir = wp_upload_dir();
					$folder = trailingslashit( $upload_dir['baseurl'] ).trailingslashit( $this->plugin_name );

					$files = $market_exporter_fs->get_files();
					if ( $files ):
						foreach( $files as $file ):
							?>
							<tr>
								<td class="row-title"><input type="checkbox" name="files[]" value="<?=$file['name'];?>"></td>
								<td><?=$file['name'];?></td>
								<td><a href="<?=$folder.$file['name'];?>" target="_blank"><?php _e( 'Open file', $this->plugin_name ); ?></a></td>
							</tr>
						<?php endforeach;
					endif; ?>
				</tbody>
			</table>

			<p><input type="submit" class="button button-primary hide-if-no-js" name="market-exporter-delete" id="market-exporter-delete" value="<?php _e( 'Delete selected files', $this->plugin_name ) ?>" /></p>

			<noscript><p><em><?php _e( 'You must enable Javascript in order to proceed!', $this->plugin_name ); ?></em></p></noscript>
		</form>

		<!-- end files tab -->
	<?php elseif ( $_GET['tab'] == 'settings' ) :

		/*
		$args = [
			'taxonomy'           => 'product_cat'
		];
		wp_terms_checklist( 0, $args );
		*/

		// check user capabilities
		if (!current_user_can('manage_options')) {
			return;
		}

		// TODO: add error/update messages.

		// Check if the user have submitted the settings.
		// Wordpress will add the "settings-updated" $_GET parameter to the url.
		if ( isset( $_GET[ 'settings-updated' ] ) ) {
			// Add settings saved message with the class of "updated"
			add_settings_error(
				'market_exporter_messages',
				'market_exporter_message',
				__( 'Settings Saved', $this->plugin_name ),
				'updated'
			);
		}

		// Show error/update messages
		settings_errors( 'market_exporter_messages' );
		?>

		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "wporg"
			settings_fields($this->plugin_name);
			// output setting sections and their fields
			// (sections are registered for "wporg", each field is registered to a specific section)
			do_settings_sections($this->plugin_name);
			// output save settings button
			submit_button('Save Settings');
			?>
		</form>

		<!-- end settings tab -->
	<?php elseif ( $_GET['tab'] == 'news' ) : ?>

		<h2><?php _e( 'News', 'market-exporter' ); ?></h2>

		<h4>Октябрь 2016</h4>
		<p>В плагине кардинально поменялась структура страниц в системе навигации WordPress. Теперь вместо разрозненной системы, где настройки
		находятся в разделе <i>WooCommerce - Настройки - Товары</i>, а генрация файла осуществляется через <i>Инструменты - Market Exporter</i>, все
		перемещено под общий пункт меню <i>WooCommerce - Market Exporter</i>.</p>
		<p>Из нововведений стоит омтетить следующее:</p>
		<ul>
			<li>Переработан код, отвечающий за выборку товаров. Теперь вместо прямого доступа к базе данных, используются стандартные
				методы от WooCommerce и WordPress. А это означает, что любые изменения и обновления WordPress и WooCommerce больше не
				затрагивают функционал плагина. Также решены пробелмы, когда импортированные товары не находились плагином.</li>
			<li>Незначительные изменения в обработке названий товаров. Запрещенные символы преобразуются в соответствующие
				разрешенные эквиваленты.</li>
		</ul>

		<h4>Июль 2016</h4>
		<p>Несколько слов по поводу последнего обновления 0.2.6. В последней версии WooCommerce были внесены значительные изменения в то как работает доставка, а именно - появились зоны. Пока данный функционал полностью не реализован в плагине, рекомендуется устанавливать параметры доставки в партнерском интерфейсе Яндекс Маркет.</p>
		<p>Также, сейчас я работаю над созданием нового сервиса для работы с Яндекс Маркет. Сервис будет работать по API с WooCommerce и интегрироваться с различными сервисами Яндекса. Мне нужны бета-тестеры. Кому интересно, пишите мне на <a href="mailto:a.vanyukov@testor.ru">a.vanyukov@testor.ru</a>.</p>

		<!-- end news tab -->
	<?php endif; ?>
</div>
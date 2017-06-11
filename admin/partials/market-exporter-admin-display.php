<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package Market_Exporter
 * @since 0.0.1
 */

if ( ! isset( $_GET['tab'] ) ) { // Input var ok.
	$tab = 'generate';
} else {
	$tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) ); // Input var ok.
}
?>

<div class="wrap" id="me_pages">

	<div class="version">
		<?php
		printf( // WPCS: XSS OK.
			/* translators: version number */
			__( 'Version: %s', 'market-exporter' ),
			$this->version
		);
		?>
	</div>

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<nav class="nav-tab-wrapper">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '&amp;tab=generate' ) ); ?>"
		   class="nav-tab <?php echo ( 'generate' === $tab ) ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Generate file', 'market-exporter' ); ?>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '&amp;tab=files' ) ); ?>"
		   class="nav-tab <?php echo ( 'files' === $tab ) ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Files', 'market-exporter' ); ?>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '&amp;tab=settings' ) ); ?>"
		   class="nav-tab <?php echo ( 'settings' === $tab ) ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Settings', 'market-exporter' ); ?>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '&amp;tab=news' ) ); ?>"
		   class="nav-tab <?php echo ( 'news' === $tab ) ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'News', 'market-exporter' ); ?>
		</a>
	</nav>

	<?php // Display general tab.
	if ( 'generate' === $tab ) :
		if ( ! empty( $_POST[ $this->plugin_name.'-generate' ] ) ) :
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( // WPCS: XSS OK.
					__( "You don't have the permission to do this.", 'market-exporter' )
				);
			}

			check_admin_referer( $this->plugin_name . '-generate' );

			// Select what version of plugin to use.
			$plugin_option = get_option( 'market_exporter_shop_settings' );
			$market_exporter = new ME_WC();

			$return_code = $market_exporter->generate_YML();

			switch ( $return_code ) {
				case 100:
					echo ' <p>' . sprintf( __( 'Currently only the following currency is supported: Russian Ruble (RUB), Ukrainian Hryvnia (UAH), US Dollar (USD) and Euro (EUR). Please <a href="%s">update currency</a>.', 'market-exporter' ), admin_url( 'admin.php?page=wc-settings' ) ) . '</p>';
					break;
				case 200:
					echo ' <p>' . sprintf( __( 'No shipping methods are available. Please <a href="%s">update or add at least one</a>.', 'market-exporter' ), admin_url( 'admin.php?page=wc-settings&tab=shipping' ) ) . '</p>';
					break;
				case 300:
					echo '	<p>' . sprintf( __( 'Unable to find any products. Are you sure <a href="%s">some exist</a>?', 'market-exporter' ), admin_url( 'post-new.php?post_type=product' ) ) . '</p>';
					break;
				default:
					echo '	<p>' . sprintf( __( 'File exported successfully: <a href="%1$s">%2$s</a>.', 'market-exporter' ), $return_code, $return_code ) . '</p>';
			}
		else :
			// Display the form by default. ?>

			<form method="post" action="">
				<?php wp_nonce_field( $this->plugin_name . '-generate' ) ?>
				<p><?php esc_html_e( 'This plugin is used to generate a valid YML file for exporting your products in WooCommerce to Yandex Market.', 'market-exporter' ); ?></p>

				<p><?php esc_html_e( 'Please be patient while the YML file is generated. This can take a while if your server is slow (inexpensive hosting) or if you have many products in WooCommerce. Do not navigate away from this page until this script is done or the YML file will not be created. You will be notified via this page when the process is completed.', 'market-exporter' ); ?></p>

				<p><?php esc_html_e( 'To begin, just press the button below.', 'market-exporter' ); ?></p>

				<p><input type="submit" class="button button-primary hide-if-no-js" name="market-exporter-generate" id="market-exporter-generate" value="<?php esc_attr_e( 'Generate YML file', 'market-exporter' ) ?>" /></p>

				<noscript><p><em><?php esc_html_e( 'You must enable Javascript in order to proceed!', 'market-exporter' ); ?></em></p></noscript>

			</form>
		<?php endif; ?>

		<!-- end general tab -->
	<?php
	elseif ( 'files' === $tab ) :

		// If someone clicks on Delete file button.
		$market_exporter_fs = new Market_Exporter_FS( $this->plugin_name );
		if ( ! empty( $_POST[ $this->plugin_name . '-delete' ] ) && isset( $_POST['files'] ) ) {
			$market_exporter_fs->delete_files( $_POST['files'] );
		} ?>

		<h2><?php esc_html_e( 'Generated YML files', 'market-exporter' ); ?></h2>
		<form method="post" action="" name="list-files" id="market-exporter">
			<?php wp_nonce_field( $this->plugin_name ) ?>

			<table class="widefat">
				<thead>
				<tr>
					<th class="row-title id"><input type="checkbox" onClick="toggle(this)"></th>
					<th class="row-title name"><?php esc_html_e( 'File name', 'market-exporter' ); ?></th>
					<th class="row-title link"><?php esc_html_e( 'Action', 'market-exporter' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				$upload_dir = wp_upload_dir();
				$folder = trailingslashit( $upload_dir['baseurl'] ) . trailingslashit( $this->plugin_name );

				$files = $market_exporter_fs->get_files();
				if ( $files ) :
					foreach ( $files as $file ) :?>
						<tr>
							<td class="row-title"><input type="checkbox" name="files[]" value="<?php echo $file['name']; ?>"></td>
							<td><?php echo $file['name']; ?></td>
							<td><a href="<?php echo $folder . $file['name']; ?>" target="_blank"><?php esc_html_e( 'Open file', 'market-exporter' ); ?></a></td>
						</tr>
					<?php endforeach;
				endif; ?>
				</tbody>
			</table>

			<p><input type="submit" class="button button-primary hide-if-no-js" name="market-exporter-delete" id="market-exporter-delete" value="<?php esc_attr_e( 'Delete selected files', 'market-exporter' ) ?>" /></p>

			<noscript><p><em><?php esc_html_e( 'You must enable Javascript in order to proceed!', 'market-exporter' ); ?></em></p></noscript>
		</form>

		<!-- end files tab -->
	<?php
	elseif ( 'settings' === $tab ) :

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// TODO: add error/update messages.
		// Check if the user have submitted the settings.
		// WordPress will add the "settings-updated" $_GET parameter to the url.
		if ( isset( $_GET[ 'settings-updated' ] ) ) {
			// Add settings saved message with the class of "updated".
			add_settings_error(
				'market_exporter_messages',
				'market_exporter_message',
				__( 'Settings Saved', 'market-exporter' ),
				'updated'
			);
		}

		// Show error/update messages.
		settings_errors( 'market_exporter_messages' );
		?>

		<form action="options.php" method="post">
			<?php
			// Output security fields for the registered setting "wporg".
			settings_fields( $this->plugin_name );
			// Output setting sections and their fields
			// (sections are registered for "wporg", each field is registered to a specific section).
			do_settings_sections( $this->plugin_name );
			// Output save settings button.
			submit_button( __( 'Save Settings', 'market-exporter' ) );
			?>
		</form>

		<!-- end settings tab -->
	<?php elseif ( 'news' === $tab ) : ?>

		<h2><?php esc_html_e( 'News', 'market-exporter' ); ?></h2>

		<h4>Июнь 2017</h4>
		<p>Долгожданная обновленная версия плагина. Добавлена экспериментальная поддержка тегов model и param! Наконец-то появилась возможность выгружать габариты и вес товара.
			В данном релизе исправлено огромное количество багов и ошибок. Переработан каждый файл плагина, чтобы работать быстрее и стабильнее. И самое главное - для
			тех, кто не хочет обновлять версию PHP, вновь вернулась поддержка версий до 5.4.</p>
		<p>Ваше мнение и поддержка очень важны для меня! Если Вам нравится плагин, оставьте отзыв <a href="https://wordpress.org/plugins/market-exporter/#reviews" target="_blank">здесь</a>!</p>

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
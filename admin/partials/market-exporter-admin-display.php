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
			Market_Exporter::$version
		);
		?>
	</div>

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

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

			$return_code = $market_exporter->generate_yml();

			switch ( $return_code ) {
				case 100:
					echo ' <p>' . sprintf( __( 'Currently only the following currency is supported: Russian Ruble (RUB), Ukrainian Hryvnia (UAH), Tenge (KZT), US Dollar (USD) and Euro (EUR). Please <a href="%s">update currency</a>.', 'market-exporter' ), admin_url( 'admin.php?page=wc-settings' ) ) . '</p>';
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
	<?php endif; ?>
</div>

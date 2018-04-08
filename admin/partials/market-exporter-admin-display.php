<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/av3nger/market-exporter/
 * @since      0.0.1
 *
 * @package    Market_Exporter
 * @subpackage Market_Exporter/admin/partials
 */

?>

<div class="wrap me-wrapper" id="me_pages">

	<div class="me-version">
		<?php
		printf( // WPCS: XSS OK.
			/* translators: version number */
			__( 'Version: %s', 'market-exporter' ),
			$this->version
		);
		?>
	</div>

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php $old_options = get_option( 'market_exporter_shop_settings' ); ?>
	<?php var_dump( $old_options ); ?>

	<?php
	$options = array();

	$options['name']     = isset( $old_options['website_name'] ) ? $old_options['website_name'] : get_bloginfo( 'name' );
	$options['company']  = isset( $old_options['company_name'] ) ? $old_options['company_name'] : '';
	$options['url']      = get_site_url();
	$options['platform'] = __( 'WordPress', 'market-exporter' );
	$options['version']  = get_bloginfo( 'version' );
	$options['agency']   = '';
	$options['email']    = get_bloginfo( 'admin_email' );

	var_dump( $options );
	?>

	<div class="me-list-group">

		<div class="me-list-group-panel" id="me_yml_store">

			<div class="me-list-header">
				<h2><?php esc_html_e( '<shop>', 'market-exporter' ); ?></h2>
				<h4><?php esc_html_e( 'header elements', 'market-exporter' ); ?></h4>

				<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Add field', 'market-exporter' ); ?>">
			</div>

			<div>
				<?php
				$elements = Market_Exporter_Elements::get_header_elements();
				foreach ( $elements as $element => $value ) {
					// If this option has been configured - skip this list.
					if ( isset( $options[ $element ] ) && ! empty( $options[ $element ] ) ) {
						continue;
					}

					// If not - remove it from the array and display a label.
					unset( $elements[ $element ] );
					echo "<span>{$element}</span>";
				}
				?>
			</div>

			<?php foreach ( $elements as $element => $value ) : ?>
				<?php Market_Exporter_Elements::print_element( $element, $options[ $element ], 'header' ); ?>
			<?php endforeach; ?>
		</div>

<!--
		https://yandex.ru/support/partnermarket/export/yml.html

		<div class="me-list-group-panel" id="me_yml_offer">

			<div class="me-list-group-item">
				<span class="dashicons dashicons-move" aria-hidden="true"></span>
				sadasdasd
			</div>

			<div class="me-list-group-item">
				<span class="dashicons dashicons-move" aria-hidden="true"></span>
				sadasdasd
			</div>

		</div>
-->

	</div>
</div>

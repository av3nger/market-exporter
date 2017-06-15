<?php
/**
 * Market Exporter: Market_Exporter_Activator class
 *
 * This class defines all code necessary to run during the plugin's activation. Fired during plugin activation.
 *
 * @package Market_Exporter
 * @since   0.0.1
 */
class Market_Exporter_Activator {

	/**
	 * Activation procedure.
	 *
	 * We need to check if Market Exporter was already installed.
	 * If not - populate DB with default fields (website name, company name).
	 *
	 * @since 0.0.4
	 */
	public static function activate() {
		global $wpdb;

		// Leave this for now, so it deletes for everyone.
		delete_option( 'market_exporter_website_name' );
		delete_option( 'market_exporter_company_name' );
		delete_option( 'market-exporter-settings' );

		$market_exporter_options = $wpdb->get_var(
			"SELECT option_id
				    FROM $wpdb->options
				    WHERE option_name = 'market_exporter_shop_settings'" );

		if ( ! isset( $market_exporter_options ) ) {
			$settings = array(
				'website_name'    => get_bloginfo( 'name' ),
				'company_name'    => get_bloginfo( 'name' ),
				'file_date'       => true,
				'image_count'     => 10,
				'vendor'          => 'not_set',
				'model'           => 'not_set',
				'market_category' => 'not_set',
				'backorders'      => false,
				'sales_notes'     => '',
				'size'            => false,
			);
			update_option( 'market_exporter_shop_settings', $settings );
		}
	}

}
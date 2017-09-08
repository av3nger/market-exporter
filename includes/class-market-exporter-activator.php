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
		// Leave this for now, so it deletes for everyone.
		delete_option( 'market_exporter_website_name' );
		delete_option( 'market_exporter_company_name' );
		delete_option( 'market-exporter-settings' );

		$options = get_option( 'market_exporter_shop_settings' );
		if ( ! $options ) {
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
				'cron'            => false,
				'delivery'        => false,
				'pickup'          => false,
				'store'           => false,
			);
			update_option( 'market_exporter_shop_settings', $settings );
		}

		$version = get_option( 'market_exporter_version' );

		if ( version_compare( $version, '0.4.4', '<=' ) ) {
			self::update_0_4_4();
		}

		if ( version_compare( $version, '1.0.0-beta.1', '<' ) ) {
			self::update_1_0_0_beta_1();
		}

		// Update version.
		update_option( 'market_exporter_version', Market_Exporter::$version );
	}

	/**
	 * Update to version 0.4.4.
	 *
	 * @since 0.4.4
	 */
	public static function update_0_4_4() {
		$options = get_option( 'market_exporter_shop_settings' );

		// Update cron settings in options.
		if ( ! isset( $options['cron'] ) ) {
			$options['cron'] = false;
			update_option( 'market_exporter_shop_settings', $options );
		}

		// Removed unused cron schedules.
		wp_clear_scheduled_hook( 'market_exporter_daily' );
	}

	/**
	 * Update to version 1.0.0.
	 *
	 * @since 0.4.5
	 */
	public static function update_1_0_0_beta_1() {
		$options = get_option( 'market_exporter_shop_settings' );

		// Remove market_category setting.
		unset( $options['market_category'] );

		// Init typePrefix option.
		if ( ! isset( $options['type_prefix'] ) ) {
			$options['type_prefix'] = 'not_set';
		}

		// Init manufacturer_warranty option.
		if ( ! isset( $options['warranty'] ) ) {
			$options['warranty'] = 'not_set';
		}

		// Init country_of_origin option.
		if ( ! isset( $options['origin'] ) ) {
			$options['origin'] = 'not_set';
		}

		// Init delivery option.
		if ( ! isset( $options['delivery'] ) ) {
			$options['delivery'] = 'disabled';
		}

		// Init pickup option.
		if ( ! isset( $options['pickup'] ) ) {
			$options['pickup'] = 'disabled';
		}

		// Init store option.
		if ( ! isset( $options['store'] ) ) {
			$options['store'] = 'disabled';
		}

		update_option( 'market_exporter_shop_settings', $options );
	}
}
<?php
/**
 * Market Exporter: Market_Exporter_Deactivator class
 *
 * This class defines all code necessary to run during the plugin's deactivation. Fired during plugin deactivation.
 *
 * @package Market_Exporter
 * @since   0.0.1
 */
class Market_Exporter_Deactivator {

	/**
	 * Deactivate function
	 *
	 * @since 0.0.1
	 */
	public static function deactivate() {
		// Find out when the last event was scheduled.
		$timestamp = wp_next_scheduled( 'market_exporter_cron' );
		// Unschedule previous event if any.
		wp_unschedule_event( $timestamp, 'market_exporter_cron' );
	}

}

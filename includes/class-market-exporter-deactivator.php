<?php

/**
 * Fired during plugin deactivation
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since    0.0.1
 */
class Market_Exporter_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.0.1
	 */
	public static function deactivate() {
		// find out when the last event was scheduled
		$timestamp = wp_next_scheduled( 'market_exporter_daily' );
		// unschedule previous event if any
		wp_unschedule_event( $timestamp, 'market_exporter_daily' );
	}

}

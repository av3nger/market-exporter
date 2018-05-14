<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/av3nger/market-exporter/
 * @since      0.0.1
 *
 * @package    Market_Exporter
 * @subpackage Market_Exporter/includes
 */

namespace Market_Exporter\Core;

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.0.1
 * @package    Market_Exporter
 * @subpackage Market_Exporter/includes
 * @author     Anton Vanyukov <a.vanyukov@testor.ru>
 */
class Deactivator {

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

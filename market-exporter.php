<?php

/**
 * The plugin bootstrap file
 *
 * @package Market Exporter
 *
 * @wordpress-plugin
 * Plugin Name: Market Exporter
 * Plugin URI: https://github.com/av3nger/market-exporter/
 * Description: Market Exporter provides a way to export products from WooCommerce installations into a YML file for use in Yandex Market.
 * Version: 0.3.1
 * Author: Anton Vanyukov
 * Author URI: http://www.vanyukov.su
 * License: GPLv2 or later
 * Text Domain: market-exporter
 * Domain Path: /languages
 * WC requires at least: 2.4
 * WC tested up to: 2.6.6
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Glogal variables
define( 'MARKET_EXPORTER__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-market-exporter-activator.php
 *
 * @since    0.0.1
 */
function activate_market_exporter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-market-exporter-activator.php';
	Market_Exporter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-market-exporter-deactivator.php
 *
 * @since    0.0.1
 */
function deactivate_market_exporter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-market-exporter-deactivator.php';
	Market_Exporter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_market_exporter' );
register_deactivation_hook( __FILE__, 'deactivate_market_exporter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-market-exporter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */
function run_market_exporter() {

	$plugin = new Market_Exporter();
	$plugin->run();

}
run_market_exporter();

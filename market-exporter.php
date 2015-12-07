<?php
/**
 * The plugin bootstrap file
 *
 * @package Market Exporter
 *
 * @wordpress-plugin
 * Plugin Name: Market Exporter
 * Plugin URI: http://www.vanyukov.su/market-exporter/
 * Description: Market Exporter provides a way to export products from WooCommerce installations into a YML file for use in Yandex Market.
 * Version: 0.0.3
 * Author: Anton Vanyukov
 * Author URI: http://www.vanyukov.su
 * License: GPLv2 or later
 * Text Domain: market-exporter
 * Domain Path: /languages
 * WC requires at least: 2.4
 * WC tested up to: 2.4
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'MARKET_EXPORTER__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_market_exporter() {
	Market_Exporter::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_market_exporter() {
	Market_Exporter::deactivate();
}

register_activation_hook( __FILE__, 'activate_market_exporter' );
register_deactivation_hook( __FILE__, 'deactivate_market_exporter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'class-market-exporter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
add_action( 'init', 'run_market_exporter' );
function run_market_exporter() {
	$plugin = new Market_Exporter();
}
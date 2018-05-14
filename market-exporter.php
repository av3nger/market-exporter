<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/av3nger/market-exporter/
 * @since             0.0.1
 * @package           Market_Exporter
 *
 * @wordpress-plugin
 * Plugin Name:       Market Exporter
 * Plugin URI:        https://github.com/av3nger/market-exporter/
 * Description:       Market Exporter provides a way to export products from WooCommerce installations into a YML file for use in Yandex Market.
 * Version:           1.1.0
 * Author:            Anton Vanyukov
 * Author URI:        http://www.vanyukov.su
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       market-exporter
 * Domain Path:       /languages
 * WC requires at least: 2.6.9
 * WC tested up to:      3.2.1
 */

namespace Market_Exporter;

use Market_Exporter\Core\Activator;
use Market_Exporter\Core\Deactivator;
use Market_Exporter\Core\Market_Exporter;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MARKET_EXPORTER_VERSION', '1.1.0' );

/**
 * Plugin directory.
 */
define( 'MARKET_EXPORTER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 *
 * @since 0.0.1
 */
function activate_market_exporter() {
	/* @noinspection PhpIncludeInspection */
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';
	Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator.php
 *
 * @since 0.0.1
 */
function deactivate_market_exporter() {
	/* @noinspection PhpIncludeInspection */
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
	Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_market_exporter' );
register_deactivation_hook( __FILE__, 'deactivate_market_exporter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
/* @noinspection PhpIncludeInspection */
require plugin_dir_path( __FILE__ ) . 'includes/class-market-exporter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 0.0.1
 */
function run_market_exporter() {

	$plugin = new Market_Exporter();
	$plugin->run();

}
run_market_exporter();

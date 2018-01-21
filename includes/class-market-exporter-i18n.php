<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/av3nger/market-exporter/
 * @since      0.0.1
 *
 * @package    Market_Exporter
 * @subpackage Market_Exporter/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.0.1
 * @package    Market_Exporter
 * @subpackage Market_Exporter/includes
 * @author     Anton Vanyukov <a.vanyukov@testor.ru>
 */
class Market_Exporter_i18n {

	/**
	 * The domain specified for this plugin.
	 *
	 * @since  0.0.1
	 * @access private
	 * @var    string $domain The domain identifier for this plugin.
	 */
	private $domain;

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 0.0.1
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

	/**
	 * Set the domain equal to that of the specified domain.
	 *
	 * @since 0.0.1
	 * @param string $domain The domain that represents the locale of this plugin.
	 */
	public function set_domain( $domain ) {
		$this->domain = $domain;
	}

}

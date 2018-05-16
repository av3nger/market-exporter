<?php
/**
 * Register WP REST API endpoints
 *
 * @link       https://github.com/av3nger/market-exporter/
 * @since      1.1.0
 *
 * @package    Market_Exporter
 * @subpackage Market_Exporter/includes
 */

namespace Market_Exporter\Core;

/**
 * Register WP REST API endpoints.
 *
 * This singleton class defines and registers all endpoints needed for React components.
 *
 * @since      1.1.0
 * @package    Market_Exporter
 * @subpackage Market_Exporter/includes
 * @author     Anton Vanyukov <a.vanyukov@testor.ru>
 */
class Endpoints extends \WP_REST_Controller {

	/**
	 * Class instance.
	 *
	 * @var Endpoints|null
	 */
	private static $instance = null;

	/**
	 * API version.
	 *
	 * @var string
	 */
	protected $version = '1';

	/**
	 * Get class instance.
	 *
	 * @return Endpoints|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$slug = ME::get_instance()->get_plugin_name();
		$namespace = "{$slug}/v{$this->version}";
		$endpoint = '/settings/';

		register_rest_route( $namespace, $endpoint, array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => function() {
				return get_option( 'market_exporter_shop_settings' );
			},
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
		) );
	}

}

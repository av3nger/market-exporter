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
		$namespace = $slug . '/v' . $this->version;

		register_rest_route( $namespace, '/settings/', array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => function() {
					return get_option( 'market_exporter_settings' );
				},
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			),
			array(
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_settings' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			),
		) );

		register_rest_route( $namespace,'/elements/(?P<type>[-\w]+)', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_elements' ),
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
		) );
	}

	/**
	 * Update settings.
	 *
	 * @param \WP_REST_Request $request
	 */
	public function update_settings( \WP_REST_Request $request ) {

	}

	/**
	 * Get YML elements array.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_elements( \WP_REST_Request $request ) {
		$method = "get_{$request['type']}_elements";

		if ( ! method_exists( 'Market_Exporter\Admin\YML_Elements', $method ) ) {
			return new \WP_Error( 'method-not-found', printf(
				/* translators: %s: method name */
				__( 'Method %s not found.', 'market-exporter' ),
				$method
			) );
		}

		$elements = call_user_func( array( 'Market_Exporter\Admin\YML_Elements', $method ) );

		return new \WP_REST_Response( $elements, 200 );
	}

}

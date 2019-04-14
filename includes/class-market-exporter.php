<?php
/**
 * Market Exporter: Market_Exporter class
 *
 * The core plugin class. This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks. Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package Market_Exporter
 * @since   0.0.1
 */
class Market_Exporter {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  0.0.1
	 * @access protected
	 * @var    Market_Exporter_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  0.0.1
	 * @access protected
	 * @var    string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since  0.0.1
	 * @since  0.4.4            Changed from protected to public static.
	 * @access protected
	 * @var    string $version  The current version of the plugin.
	 */
	public static $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {

		$this->plugin_name = 'market-exporter';
		self::$version     = '1.0.5';

		$this->load_dependencies();
		$this->set_locale();

		// Check if plugin has WooCommerce installed and active.
		$this->loader->add_action( 'admin_init', $this, 'run_plugin' );
		if ( ! self::check_prerequisites() ) {
			$this->loader->add_action( 'admin_notices', $this, 'plugin_activation_message' );
			return;
		}

		$notice = get_option( 'market_exporter_notice_hide' );

		if ( 'true' !== $notice ) {
			$this->loader->add_action( 'admin_notices', $this, 'plugin_rate_message' );
		}

		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Market_Exporter_Loader. Orchestrates the hooks of the plugin.
	 * - Market_Exporter_i18n. Defines internationalization functionality.
	 * - Market_Exporter_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  0.0.1
	 * @access private
	 */
	private function load_dependencies() {

		// Include Freemius SDK.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'freemius/start.php';
		$this->init_fremius();
		// Signal that SDK was initiated.
		do_action( 'market_exporter_fremius_loaded' );

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-market-exporter-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-market-exporter-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-market-exporter-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-market-exporter-fs.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-market-exporter-wc.php';

		$this->loader = new Market_Exporter_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  0.0.1
	 * @access private
	 */
	private function set_locale() {

		$plugin_i18n = new Market_Exporter_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since  0.0.1
	 * @access private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Market_Exporter_Admin( $this->get_plugin_name() );
		$plugin_yml = new ME_WC();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		// Add Plugin page.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_page', 99 );
		// Add Settings page.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		// Add Settings link to plugin in plugins list.
		$basename = plugin_basename( MARKET_EXPORTER__PLUGIN_DIR . 'market-exporter.php' );
		$this->loader->add_filter( "plugin_action_links_{$basename}", $plugin_admin, 'plugin_add_settings_link' );
		// Add cron support.
		$this->loader->add_action( 'market_exporter_cron', $plugin_yml, 'generate_yml' );
		// Add ajax support to dismiss notice.
		$this->loader->add_action( 'wp_ajax_dismiss_rate_notice', $this, 'dismiss_notice' );
		// Add support to update file on product update.
		$this->loader->add_action( 'woocommerce_update_product', $plugin_admin, 'generate_file_on_update' );

		// Freemius.
        $this->init_fremius()->add_filter( 'connect_message_on_update', array( $this, 'connect_message_on_update' ), 10, 6 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 0.0.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of WordPress and to define
	 * internationalization functionality.
	 *
	 * @since  0.0.1
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  0.0.1
	 * @return Market_Exporter_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since      0.0.1
	 * @return     string  The version number of the plugin.
	 * @deprecated 0.4.4   Exchanged for public static variable.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Is WooCommerce installed? Is it active? If not - don't activate the plugin.
	 *
	 * Checks the availability of WooCommerce. If not WooCommerce not available - we disable the Market Exporter.
	 * First we get a list of activated plugins. If our plugin is there - we suppress the "Activation successful" message.
	 * And deactivate the plugin. The error message is registered in define_admin_hooks().
	 *
	 * @since 0.0.1
	 */
	public function run_plugin() {

		if ( ! self::check_prerequisites() ) {
			$plugins = get_option( 'active_plugins' );
			$market_exporter = plugin_basename( MARKET_EXPORTER__PLUGIN_DIR . 'market-exporter.php' );
			if ( in_array( $market_exporter, $plugins ) ) {
				unset( $_GET['activate'] );
				deactivate_plugins( MARKET_EXPORTER__PLUGIN_DIR . 'market-exporter.php' );
			}
		}

	}

	/**
	 * Checks if WooCommerce is installed and active.
	 *
	 * Check if get_plugins() function exists. Needed for checks during __construct.
	 * Check if WooCommerce is installed using get_plugins().
	 * Check if WooCommerce is active using is_plugin_active().
	 *
	 * @since  0.0.1
	 * @return bool
	 */
	public static function check_prerequisites() {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$woo_installed = get_plugins( '/woocommerce' );
		if ( empty( $woo_installed ) || ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Message to display if we did not find WooCommerce.
	 *
	 * @since 0.0.1
	 */
	public function plugin_activation_message() {
		?>
		<div class="error notice">
			<p><?php _e( 'The Market Exporter plugin requires <a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a> to be installed and activated. Please check your configuration.', 'market-exporter' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Rate the plugin message.
	 *
	 * @since 0.4.4
	 */
	public function plugin_rate_message() {
		if ( 'woocommerce_page_market-exporter' !== get_current_screen()->id ) {
			return;
		}
		?>
		<div class="notice notice-success is-dismissible" id="rate-notice">
			<p><?php _e( 'Do you like the plugin? Please support the development by <a href="https://wordpress.org/plugins/market-exporter/">writing a review</a>!', 'market-exporter' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Dismiss notice to rate the plugin.
	 *
	 * @since 0.4.4
	 */
	public function dismiss_notice() {
		check_ajax_referer( 'me_dismiss_notice' );
		// If user clicks to ignore the notice, add that to their user meta.
		update_option( 'market_exporter_notice_hide', 'true' );
		wp_die(); // All ajax handlers die when finished.
	}

	/**
     * Init Freemius.
     *
     * @since 1.0.5
     *
	 * @return Freemius
	 * @throws Freemius_Exception
	 */
	private function init_fremius() {

		global $market_exporter_fremius;

		if ( ! isset( $market_exporter_fremius ) ) {
			$market_exporter_fremius = fs_dynamic_init( array(
				'id'             => '3640',
				'slug'           => 'market-exporter',
				'type'           => 'plugin',
				'public_key'     => 'pk_8e3bfb7fdecdacb5e4b56998fbe73',
				'is_premium'     => false,
				'has_addons'     => false,
				'has_paid_plans' => false,
				'menu'           => array(
					'slug'    => 'market-exporter',
					'account' => false,
					'contact' => false,
					'support' => false,
					'parent'  => array(
						'slug' => 'woocommerce',
					),
				),
			) );
		}

		return $market_exporter_fremius;

    }

	/**
     * Show opt-in message for current users.
     *
     * @since 1.0.5
     *
	 * @param string $message          Current message.
	 * @param string $user_first_name  User name.
	 * @param string $plugin_title     Plugin title.
	 * @param string $user_login       User login.
	 * @param string $site_link        Link to site.
	 * @param string $freemius_link    Link to Freemius.
	 *
	 * @return string
	 */
    public function connect_message_on_update( $message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link ) {
	    return sprintf(
		    __( 'Hey %1$s', 'market-exporter' ) . ',<br>' .
		    __( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'market-exporter' ),
		    $user_first_name,
		    '<b>' . $plugin_title . '</b>',
		    '<b>' . $user_login . '</b>',
		    $site_link,
		    $freemius_link
	    );
    }

}

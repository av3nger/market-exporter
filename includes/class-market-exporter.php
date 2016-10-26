<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since    0.0.1
 */
class Market_Exporter {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      Market_Exporter_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {

		$this->plugin_name = 'market-exporter';
		$this->version = '0.3.1';

		$this->load_dependencies();
		$this->set_locale();

		// Check if plugin has WooCommerce installed and active.
		$this->loader->add_action( 'admin_init', $this, 'run_plugin' );
		if ( !self::check_prerequisites() ) {
			$this->loader->add_action( 'admin_notices', $this, 'plugin_activation_message' ) ;
			return;
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
	 * @since    0.0.1
	 * @access   private
	 */
	private function load_dependencies() {

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
	 * @since    0.0.1
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Market_Exporter_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_admin_hooks() {
		
		$plugin_admin = new Market_Exporter_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_yml = new ME_WC();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		// Add Plugin page.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_page', 99 );
		// Add Settings page.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		// Add Settings link to plugin in plugins list.
		$basename = plugin_basename( MARKET_EXPORTER__PLUGIN_DIR . 'market-exporter.php' );
		$this->loader->add_filter( 'plugin_action_links_'.$basename, $plugin_admin, 'plugin_add_settings_link' );
		// Add cron support
		$this->loader->add_action( 'admin_init', $plugin_admin, 'crontab_activate' );
		$this->loader->add_action( 'market_exporter_daily', $plugin_yml, 'generate_YML' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.0.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.0.1
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.0.1
	 * @return    Market_Exporter_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.0.1
	 * @return    string    The version number of the plugin.
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
   * @since		0.0.1
   */	
	public function run_plugin() {
		if ( !self::check_prerequisites() ) {
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
   * @since		0.0.1
   */
	public static function check_prerequisites() {
		if ( ! function_exists( 'get_plugins' ) )
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$woo_installed = get_plugins('/woocommerce');
		if ( empty($woo_installed) )
			return false;

		if ( !is_plugin_active('woocommerce/woocommerce.php') )
			return false;

		return true;
	}
	
  /**
   * Message to display if we didn't find WooCommerce.
   *
   * @since		0.0.1
   */
  public function plugin_activation_message() {
    ?>
    <div class="error notice">
        <p><?php _e( 'The Market Exporter plugin requires <a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a> to be installed and activated. Please check your configuration.', 'market-exporter' ); ?></p>
    </div>
    <?php
  }
	

}

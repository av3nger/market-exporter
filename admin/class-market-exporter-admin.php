<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since		0.0.1
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Market_Exporter_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since			0.0.1
	 * @access		private
	 * @var				string							$plugin_name			The ID of this plugin.
	 */
	private $plugin_name;
	
	/**
	 * The function prefix.
	 *
	 * @since			0.0.4
	 * @access		private
	 * @var				string							$plugin_prefix		THe function prefix.
	 */
	private $plugin_prefix;

	/**
	 * The version of this plugin.
	 *
	 * @since			0.0.1
	 * @access		private
	 * @var				string							$version					The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since			0.0.1
	 * @param			string							$plugin_name      The name of this plugin.
	 * @param			string							$plugin_prefix    Prefix for options. Also hardcoded into plugin activation.
	 * @param			string							$version					The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_prefix = 'market_exporter';
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Market_Exporter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Market_Exporter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/market-exporter-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Market_Exporter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Market_Exporter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/market-exporter-admin.js', array( 'jquery' ), $this->version, false );

	}
	
  /**
   * Add sub menu page to the tools main menu.
   *
   * @since			0.0.1
   */
	public function add_admin_page() {
    add_management_page(
	      __( 'Market Exporter', 'market-exporter' ),
	      __( 'Market Exporter', 'market-exporter' ),
	      'manage_options',
	      $this->plugin_name,
	      array ( $this, 'display_admin_page' )
    );
	}
	
  /**
   * Display plugin page.
   *
   * @since			0.0.1
   */
	public function display_admin_page() {
		global $wpdb;
		require_once plugin_dir_path( __FILE__ ).'partials/market-exporter-admin-display.php';
	}
	
	/**
	 * Add options page.
	 *
	 * @since			0.0.4
	 */
	public function add_options_page() {
		add_options_page(
				__( 'Market Exporter Settings', 'market-exporter' ),
				__( 'Market Exporter', 'market-exporter' ),
				'manage_options',
				$this->plugin_name.'-settings',
				array( $this, 'display_options_page' )
		);
	}
	
	/**
	 * Display options page.
	 *
	 * @since			0.0.4
	 */
	public function display_options_page() {
		require_once plugin_dir_path( __FILE__ ).'partials/market-exporter-options-display.php';
	}
	
	/**
	 * Add settings section.
	 *
	 * Here we add a new section to the settings page and populate it with settings fields.
	 *
	 * @since			0.0.4
	 */
	public function register_settings() {
		// Add new section to settings pages.
		add_settings_section(
				$this->plugin_prefix.'_general',
				__( 'Shop settings', 'market-exporter' ),
				array( $this, $this->plugin_prefix.'_general_cb' ),
				$this->plugin_name
		);
		
		// Register settings fields.
		// Website name
		add_settings_field(
				$this->plugin_prefix.'_website_name',
				__( 'Website name', 'market-exporter' ),
				array( $this, $this->plugin_prefix.'_website_name_cb' ),
				$this->plugin_name,
				$this->plugin_prefix.'_general',
				array( 'label_for', $this->plugin_prefix.'_website_name' )
		);
		
		// Company name
		add_settings_field(
				$this->plugin_prefix.'_company_name',
				__( 'Company name', 'market-exporter' ),
				array( $this, $this->plugin_prefix.'_company_name_cb' ),
				$this->plugin_name,
				$this->plugin_prefix.'_general',
				array( 'label_for', $this->plugin_prefix.'_company_name' )
		);
		
		register_setting( $this->plugin_name, $this->plugin_prefix.'_website_name', 'sanitize_text_field' );
		register_setting( $this->plugin_name, $this->plugin_prefix.'_company_name', 'sanitize_text_field' );
	}

	/**
	 * Render the text for the general settings section.
	 *
	 * @since			0.0.4
	 */
	public function market_exporter_general_cb() {
		echo '<p>' . __( 'Settings that are only used in the <b>shop</b> section of the YML file. Website name is the <i>name</i> field, Company name is used for the <i>company</i> field.', 'market-exporter' ) . '</p>';
	}
	
	/**
	 * Render the website name input.
	 *
	 * @since			0.0.4
	 */
	public function market_exporter_website_name_cb() {
		$website_name = get_option( $this->plugin_prefix.'_website_name' );
		echo '<input type="text" name="'.$this->plugin_prefix.'_website_name'.'"
														 id="'.$this->plugin_prefix.'_website_name'.'"
														 value="'.esc_html( $website_name ).'">';
	}
	
	/**
	 * Render the company name input.
	 *
	 * @since			0.0.4
	 */
	public function market_exporter_company_name_cb() {
		$company_name = get_option( $this->plugin_prefix.'_company_name' );
		echo '<input type="text" name="'.$this->plugin_prefix.'_company_name'.'"
														 id="'.$this->plugin_prefix.'_company_name'.'"
														 value="'.esc_html( $company_name ).'">';
	}
	
  /**
   * Write YML file to /wp-content/uploads/ dir.
   *
   * @since			0.0.1
   * @param			string							$yml							Variable to display contents of the YML file.
   * @return		string																Return the path of the saved file.
   */
	public function write_file( $yml ) {
		
		$url = wp_nonce_url( 'tools.php?page=market-exporter', $this->plugin_name );
		if (false === ($creds = request_filesystem_credentials($url, '', false, false, null) ) ) {
	    // If we get here, then we don't have credentials yet,
	    // but have just produced a form for the user to fill in,
	    // so stop processing for now.
	    return true; // Stop the normal page form from displaying.
		}
		
		// Mow we have some credentials, try to get the wp_filesystem running.
		if ( ! WP_Filesystem($creds) ) {
	    // Our credentials were no good, ask the user for them again.
	    request_filesystem_credentials($url, $method, true, false, $form_fields);
	    return true;
		}
							
		// Get the upload directory and make a ym-export-YYYY-mm-dd.yml file.
		$upload_dir = wp_upload_dir();		
		$filename = 'ym-export-'.Date("Y-m-d").'.yml';
		$folder = trailingslashit( $upload_dir['basedir'] ).trailingslashit( $this->plugin_name );
		$filepath = $folder.$filename;

		// By this point, the $wp_filesystem global should be working, so let's use it to create a file.
		global $wp_filesystem;
		
		// Check if 'uploads/market-exporter' folder exists. If not - create it.
		if ( !$wp_filesystem->exists( $folder ) ) {
			if ( !$wp_filesystem->mkdir( $folder, FS_CHMOD_DIR ) ) {
				_e( "Error creating directory.", 'market-exporter' );
			}
		
		}
		// Create the file.
		if ( !$wp_filesystem->put_contents( $filepath, $yml, FS_CHMOD_FILE ) ) {
			_e( "Error uploading file.", 'market-exporter' );
		}

		return $upload_dir['baseurl'].'/'.$this->plugin_name.'/'.$filename;
	}

	/**
	 * Get currency
	 *
	 * @since			0.0.4
	 * @return		string																Returns the currency set in WooCommerce.
	 */
	public function get_currecny() {
		global $wpdb;
		return $wpdb->get_var(
									"SELECT option_value
									 FROM $wpdb->options
									 WHERE option_name = 'woocommerce_currency'" );
	}
	
	/**
	 * Get categories
	 *
	 * @since			0.0.4
	 * @return		array																	Return the array of categories with IDs and parent IDs.
	 */
	public function get_categories() {
		global $wpdb;
		return $wpdb->get_results(
									"SELECT c.term_id AS id, c.name, p.parent AS parent
									 FROM $wpdb->terms c
									 LEFT JOIN $wpdb->term_taxonomy p ON c.term_id = p.term_id
									 WHERE p.taxonomy = 'product_cat'" );
	}

	/**
	 * Get delivery option.
	 *
	 * First check if local delivery option is available. If not return price of flat rate shipping.
	 * Both delivery methods have different fields responsible for price ('fee' for local delivery, 'cost' - flat rate).
	 *
	 * @since			0.0.4
	 * @return		integer																	Return the price of delivery.
	 */
	public function get_delivery() {
		global $wpdb;
		$local_shipping = maybe_unserialize( $wpdb->get_var(
									"SELECT option_value
									 FROM $wpdb->options
									 WHERE option_name = 'woocommerce_local_delivery_settings'" ) );

		if ( $local_shipping['enabled'] == 'no' ) {
			$flat_rate = maybe_unserialize( $wpdb->get_var(
									"SELECT option_value
									 FROM $wpdb->options
									 WHERE option_name = 'woocommerce_flat_rate_settings'" ) );
			return $flat_rate['cost'];
				
		} else {
			return $local_shipping['fee'];
		}
	}
		
	/**
	 * Get products.
	 *
	 * @since			0.0.4
	 * @return		array																	Return the array of products.
	 */
	public function get_products() {
		global $wpdb;
		return $wpdb->get_results(
								 "SELECT p.ID, p.post_title AS name, p.post_excerpt AS description, m1.meta_value AS vendorCode
									FROM $wpdb->posts p
									INNER JOIN $wpdb->postmeta m1 ON p.ID = m1.post_id AND m1.meta_key = '_sku'
									INNER JOIN $wpdb->postmeta m2 ON p.ID = m2.post_id AND m2.meta_key = '_visibility'
									WHERE p.post_type = 'product'
											AND p.post_status = 'publish'
											AND p.post_password = ''
											AND m2.meta_value != 'hidden'
									ORDER BY p.ID DESC" );
	}

}

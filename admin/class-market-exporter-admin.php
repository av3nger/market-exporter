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
	 * Create the section beneath the products tab
	 **/
	public function add_section_page( $sections ) {
		$sections['market-exporter-settings'] = __( 'Market Exporter', 'market-exporter' );
		return $sections;
	}
	
	/**
	 * Add settings to the specific section we created before.
	 *
	 * @since			0.0.5
	 */
	public function add_section_page_settings( $settings, $current_section ) {
		// Check if the current section is what we want.
		if ( $current_section == 'market-exporter-settings' ) {

			// Used for selection of 'vendor' property.
			$attributes_array['not_set'] = __( 'Disabled', 'market-exporter' );
			foreach ( $this->get_attributes() as $attribute )
				$attributes_array[ $attribute[0] ] = $attribute[1];


			$settings_slider = array(
					// Add Title to the Settings.
					array(
							'name'		=> __( 'Global settings', 'market-exporter' ),
							'type'		=> 'title',
							'desc'		=> __( 'Settings that are  used in the export process.', 'market-exporter' ),
							'id'			=> 'market-exporter-settings'
					),
					// Add website name text field option.
					array(
							'name'		=> __( 'Website Name', 'market-exporter' ),
							'desc_tip'=> __( 'Not longer than 20 characters. Has to be the name of the shop, that is configured in Yandex Market.', 'market-exporter' ),
							'id'			=> 'market_exporter_shop_settings[website_name]',
							'type'		=> 'text'
					),
					// Add company name text field option.
					array(
							'name'		=> __( 'Company Name', 'market-exporter' ),
							'desc_tip'=> __( 'Full company name. Not published in Yandex Market.', 'market-exporter' ),
							'id'			=> 'market_exporter_shop_settings[company_name]',
							'type'		=> 'text'
					),
					// Add image count text field option.
					array(
							'name'		=> __( 'Images per product', 'market-exporter' ),
							'desc_tip'=> __( 'Max number of images to export for product. Max 10 images.', 'market-exporter' ),
							'id'			=> 'market_exporter_shop_settings[image_count]',
							'type'		=> 'text'
					),
					// Add selection of 'vendor' property.
					array(
							'name'		=> __( 'Vendor property', 'market-exporter' ),
							'desc_tip'=> __( 'Custom property used to specify vendor.', 'market-exporter' ),
							'id'			=> 'market_exporter_shop_settings[vendor]',
							'type'		=> 'select',
							'options'	=> $attributes_array
					),
					// Add market_category text field option.
					array(
							'name'		=> __( 'Market category property', 'market-exporter' ),
							'desc'		=> sprintf( __( 'Can be set to a value from <a href="%s" target="_blank">this list</a> only.', 'market-exporter' ), 'http://download.cdn.yandex.net/market/market_categories.xls' ),
							'desc_tip'=> __( 'Category of product on Yandex Market.', 'market-exporter' ),
							'id'			=> 'market_exporter_shop_settings[market_category]',
							'type'		=> 'select',
							'options'	=> $attributes_array
					),
					// Add sales_notes field option.
					array(
							'name'		=> __( 'Enable sales_notes', 'market-exporter' ),
							'desc'		=> __( 'If enabled will use product field "short description" as value for property "sales_notes".', 'market-exporter' ),
							'desc_tip'=> __( 'Not longer than 50 characters.', 'market-exporter' ),
							'id'			=> 'market_exporter_shop_settings[sales_notes]',
							'type'		=> 'checkbox'
					),					
					array(
							'type'		=> 'sectionend',
							'id'			=> 'market-exporter-settings'
					)
			);

			return $settings_slider;

		// If not, return the standard settings.
		} else {
			return $settings;
		}
	}
	
	/**
	 * Add settings fields.
	 *
	 * @since			0.0.4
	 */
	public function register_settings() {		
		register_setting( $this->plugin_name, 'market_exporter_shop_settings', array( $this, 'validate_shop_settings_array') );
	}

	/**
	 * Sanitize shop settings array.
	 *
	 * @since			0.0.5
	 * @param			array							$input      				Current settings.
	 * @return		array							$output							Sanitized settings.
	 */	
	public function validate_shop_settings_array( $input ) {
  	$output = get_option( 'market_exporter_shop_settings' );
		
		$output['website_name']	= sanitize_text_field( $input['website_name'] );
		$output['company_name']	= sanitize_text_field( $input['company_name'] );
		
		// According to Yandex up to 10 images per product.
		$images = intval( $input['image_count'] );
		if ( $images > 10 ) {
			$output['image_count']	= 10;
		} else {
			$output['image_count']	= $images;
		}

		$output['vendor'] = sanitize_text_field( $input['vendor'] );
		$output['market_category'] = sanitize_text_field( $input['market_category'] );
		$output['sales_notes'] = sanitize_text_field( $input['sales_notes'] );

    return $output;
	}
	
	/**
	 * Add Setings link to plugin in plugins list.
	 *
	 * @since			0.0.5
	 * @param			array							$links      				Links for the current plugin.
	 * @return		array																	New links array for the current plugin.
	 */
	public function plugin_add_settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=wc-settings&tab=products&section=market-exporter-settings">' . __( 'Settings' ) . '</a>';
    array_unshift( $links, $settings_link );
  	return $links;
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
	 * Checks if the selected currency in WooCommerce is supported by Yandex Market.
	 * As of today it is allowed to list products in six currencies: RUB, UAH, BYR, KZT, USD and EUR.
	 * But! WooCommerce doesn't support BYR and KZT. And USD and EUR can be used only to export products.
	 * They will still be listed in RUB or UAH.
	 *
	 * @since			0.0.4
	 * @return		string																Returns currency if it is supported, else false.
	 */
	public function get_currecny() {
		global $wpdb;
		$currency = $wpdb->get_var(
									"SELECT option_value
									 FROM $wpdb->options
									 WHERE option_name = 'woocommerce_currency'" );
									 
		switch ( $currency ) {
			case 'RUB':
				return 'RUR';
			case 'UAH':
			case 'USD';
			case 'EUR':
				return $currency;
			default:
				return false;
		}
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
								 "SELECT p.ID, p.post_title AS name, p.post_content AS description, m1.meta_value AS vendorCode, p.post_excerpt AS sales_notes
									FROM $wpdb->posts p
									INNER JOIN $wpdb->postmeta m1 ON p.ID = m1.post_id AND m1.meta_key = '_sku'
									INNER JOIN $wpdb->postmeta m2 ON p.ID = m2.post_id AND m2.meta_key = '_visibility'
									INNER JOIN $wpdb->postmeta m3 ON p.ID = m3.post_id AND m3.meta_key = '_stock_status'
									WHERE p.post_type = 'product'
											AND p.post_status = 'publish'
											AND p.post_password = ''
											AND m2.meta_value != 'hidden'
											AND m3.meta_value != 'outofstock'
									ORDER BY p.ID DESC" );
	}

	/**
	 * Get price.
	 *
	 * @since			0.0.6
   * @param			int								$id									Product ID for which to get images.
	 * @return		array																	Return the price and sale_price of product.
	 */
	public function get_price( $id ) {
		global $wpdb;
		return $wpdb->get_row(
									"SELECT p1.meta_value AS price, p2.meta_value AS sale_price
									 FROM $wpdb->postmeta p1
									 INNER JOIN $wpdb->postmeta p2 ON p2.post_id = p1.post_id AND p2.meta_key = '_sale_price'
									 WHERE p1.meta_key = '_regular_price'
									 		AND p1.post_id = $id", ARRAY_A );
	}

	/**
	 * Get images.
	 *
	 * @since			0.0.6
   * @param			int								$id									Product ID for which to get images.
   * @param			int								$count							Number of images to get.
	 * @return		array																	Return the array of images.
	 */
	public function get_images( $id, $count ) {
		global $wpdb;
		return $wpdb->get_col(
									"SELECT guid
									 FROM $wpdb->posts
									 WHERE post_parent = $id
									 		AND ( post_mime_type = 'image/png' OR post_mime_type = 'image/jpeg' )
									 ORDER BY ID ASC
									 LIMIT $count" );
	}
	
	/**
	 * Get custom attributes.
	 *
	 * Used on WooCommerce settings page. It lets the user choose which of the custom attributes to use for vendor value.
	 *
	 * @since			0.0.7
	 * @return		array																	Return the array of custom attributes.
	 */
	public function get_attributes() {
		global $wpdb;
		return $wpdb->get_results(
									"SELECT attribute_name AS attr_key, attribute_label AS attr_value
									 FROM $wpdb->prefix"."woocommerce_attribute_taxonomies", ARRAY_N );
	}

}

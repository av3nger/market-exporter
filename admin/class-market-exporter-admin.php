<?php

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
	 * @since     0.0.1
	 * @access    private
	 * @var       string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since     0.0.1
	 * @access    private
	 * @var       string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      0.0.1
	 *
	 * @param      string $plugin_name   The name of this plugin.
	 * @param      string $plugin_prefix Prefix for options. Also hardcoded into plugin activation.
	 * @param      string $version       The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since     0.0.1
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
	 * @since   0.0.1
	 */
	public function add_admin_page() {
		add_management_page(
			__( 'Market Exporter', 'market-exporter' ),
			__( 'Market Exporter', 'market-exporter' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_admin_page' )
		);
	}

	/**
	 * Display plugin page.
	 *
	 * @since   0.0.1
	 */
	public function display_admin_page() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/market-exporter-admin-display.php';
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
	 * @since   0.0.5
	 */
	public function add_section_page_settings( $settings, $current_section ) {
		// Check if the current section is what we want.
		if ( $current_section == 'market-exporter-settings' ) {

			// Used for selection of 'vendor' property.
			$attributes_array['not_set'] = __( 'Disabled', 'market-exporter' );
			foreach ( $this->get_attributes() as $attribute ) {
				$attributes_array[ $attribute[0] ] = $attribute[1];
			}


			$settings_slider = array(
				// Add Title to the Settings.
				array(
					'name' => __( 'Global settings', 'market-exporter' ),
					'type' => 'title',
					'desc' => __( 'Settings that are  used in the export process.', 'market-exporter' ),
					'id'   => 'market-exporter-settings'
				),
				// Add website name text field option.
				array(
					'name'     => __( 'Website Name', 'market-exporter' ),
					'desc_tip' => __( 'Not longer than 20 characters. Has to be the name of the shop, that is configured in Yandex Market.', 'market-exporter' ),
					'id'       => 'market_exporter_shop_settings[website_name]',
					'type'     => 'text'
				),
				// Add company name text field option.
				array(
					'name'     => __( 'Company Name', 'market-exporter' ),
					'desc_tip' => __( 'Full company name. Not published in Yandex Market.', 'market-exporter' ),
					'id'       => 'market_exporter_shop_settings[company_name]',
					'type'     => 'text'
				),
				// Add backorders field option.
				array(
					'name' => __( 'Add date to YML file name', 'market-exporter' ),
					'desc' => __( 'If enabled YML file will have current date at the end (for example, ym-export-2015-12-30.yml).', 'market-exporter' ),
					'id'   => 'market_exporter_shop_settings[file_date]',
					'type' => 'checkbox'
				),
				// Add image count text field option.
				array(
					'name'     => __( 'Images per product', 'market-exporter' ),
					'desc_tip' => __( 'Max number of images to export for product. Max 10 images.', 'market-exporter' ),
					'id'       => 'market_exporter_shop_settings[image_count]',
					'type'     => 'text'
				),
				// Add selection of 'vendor' property.
				array(
					'name'     => __( 'Vendor property', 'market-exporter' ),
					'desc_tip' => __( 'Custom property used to specify vendor.', 'market-exporter' ),
					'id'       => 'market_exporter_shop_settings[vendor]',
					'type'     => 'select',
					'options'  => $attributes_array
				),
				// Add market_category text field option.
				array(
					'name'     => __( 'Market category property', 'market-exporter' ),
					'desc'     => sprintf( __( 'Can be set to a value from <a href="%s" target="_blank">this list</a> only.', 'market-exporter' ), 'http://download.cdn.yandex.net/market/market_categories.xls' ),
					'desc_tip' => __( 'Category of product on Yandex Market.', 'market-exporter' ),
					'id'       => 'market_exporter_shop_settings[market_category]',
					'type'     => 'select',
					'options'  => $attributes_array
				),
				// Add sales_notes field option.
				array(
					'name'     => __( 'Enable sales_notes', 'market-exporter' ),
					'desc'     => __( 'If enabled will use product field "short description" as value for property "sales_notes".', 'market-exporter' ),
					'desc_tip' => __( 'Not longer than 50 characters.', 'market-exporter' ),
					'id'       => 'market_exporter_shop_settings[sales_notes]',
					'type'     => 'checkbox'
				),
				// Add backorders field option.
				array(
					'name' => __( 'Export products with backorders', 'market-exporter' ),
					'desc' => __( 'If enabled products that are available for backorder will be exported to YML.', 'market-exporter' ),
					'id'   => 'market_exporter_shop_settings[backorders]',
					'type' => 'checkbox'
				),
				array(
					'type' => 'sectionend',
					'id'   => 'market-exporter-settings'
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
	 * @since   0.0.4
	 */
	public function register_settings() {
		register_setting( $this->plugin_name, 'market_exporter_shop_settings', array(
			$this,
			'validate_shop_settings_array'
		) );
	}

	/**
	 * Sanitize shop settings array.
	 *
	 * @since   0.0.5
	 *
	 * @param   array $input Current settings.
	 *
	 * @return  array             $output     Sanitized settings.
	 */
	public function validate_shop_settings_array( $input ) {
		$output = get_option( 'market_exporter_shop_settings' );

		$output['website_name'] = sanitize_text_field( $input['website_name'] );
		$output['company_name'] = sanitize_text_field( $input['company_name'] );

		// According to Yandex up to 10 images per product.
		$images = intval( $input['image_count'] );
		if ( $images > 10 ) {
			$output['image_count'] = 10;
		} else {
			$output['image_count'] = $images;
		}

		$output['vendor']          = sanitize_text_field( $input['vendor'] );
		$output['market_category'] = sanitize_text_field( $input['market_category'] );
		$output['sales_notes']     = sanitize_text_field( $input['sales_notes'] );
		$output['backorders']      = sanitize_text_field( $input['backorders'] );
		$output['file_date']       = sanitize_text_field( $input['file_date'] );

		return $output;
	}

	/**
	 * Add Setings link to plugin in plugins list.
	 *
	 * @since   0.0.5
	 *
	 * @param   array $links Links for the current plugin.
	 *
	 * @return  array                          New links array for the current plugin.
	 */
	public function plugin_add_settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=wc-settings&tab=products&section=market-exporter-settings">' . __( 'Settings' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Initiate file system for read/write operations.
	 *
	 * @since   0.0.8
	 * @return  bool                          Return true if everything ok.
	 */
	function init_fs() {
		$url = wp_nonce_url( 'tools.php?page=market-exporter', $this->plugin_name );
		if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) {
			// If we get here, then we don't have credentials yet,
			// but have just produced a form for the user to fill in,
			// so stop processing for now.
			return true; // Stop the normal page form from displaying.
		}

		// Mow we have some credentials, try to get the wp_filesystem running.
		if ( ! WP_Filesystem( $creds ) ) {
			// Our credentials were no good, ask the user for them again.
			request_filesystem_credentials( $url, $method, true, false, $form_fields );

			return true;
		}

		return true;
	}

	/**
	 * Write YML file to /wp-content/uploads/ dir.
	 *
	 * @since   0.0.1
	 *
	 * @param    string $yml  Variable to display contents of the YML file.
	 * @param    string $date Yes or No for date at the end of the file.
	 *
	 * @return  string                        Return the path of the saved file.
	 */
	public function write_file( $yml, $date ) {
		// If unable to initialize filesystem, quit.
		if ( ! $this->init_fs() ) {
			return false;
		}

		// By this point, the $wp_filesystem global should be working, so let's use it to create a file.		
		global $wp_filesystem;

		// Get the upload directory and make a ym-export-YYYY-mm-dd.yml file.
		$upload_dir = wp_upload_dir();
		$folder     = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $this->plugin_name );
		if ( $date == 'yes' ) {
			$filename = 'ym-export-' . date( "Y-m-d" ) . '.yml';
		} else {
			$filename = 'ym-export.yml';
		}

		$filepath = $folder . $filename;

		// Check if 'uploads/market-exporter' folder exists. If not - create it.
		if ( ! $wp_filesystem->exists( $folder ) ) {
			if ( ! $wp_filesystem->mkdir( $folder, FS_CHMOD_DIR ) ) {
				_e( "Error creating directory.", 'market-exporter' );
			}

		}
		// Create the file.
		if ( ! $wp_filesystem->put_contents( $filepath, $yml, FS_CHMOD_FILE ) ) {
			_e( "Error uploading file.", 'market-exporter' );
		}

		return $upload_dir['baseurl'] . '/' . $this->plugin_name . '/' . $filename;
	}

	/**
	 * Get a list of generated YML files.
	 *
	 * @since   0.0.8
	 * @return  array                          Returns an array of generated files.
	 */
	function get_files() {
		// If unable to initialize filesystem, quit.
		if ( ! $this->init_fs() ) {
			return false;
		}

		// By this point, the $wp_filesystem global should be working, so let's use it to create a file.		
		global $wp_filesystem;

		// Get the upload directory and make a ym-export-YYYY-mm-dd.yml file.
		$upload_dir = wp_upload_dir();
		$folder     = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $this->plugin_name );

		return $wp_filesystem->dirlist( $folder );
	}

	/**
	 * Delete selected files.
	 *
	 * @since      0.0.8
	 *
	 * @param      array                        Array of filenames to delete.
	 */
	function delete_files( $files ) {
		// If unable to initialize filesystem, quit.
		if ( ! $this->init_fs() ) {
			return false;
		}

		// By this point, the $wp_filesystem global should be working, so let's use it to create a file.		
		global $wp_filesystem;

		// Get the upload directory and make a ym-export-YYYY-mm-dd.yml file.
		$upload_dir = wp_upload_dir();
		$folder     = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $this->plugin_name );

		foreach ( $files as $file ):
			$wp_filesystem->delete( $folder . $file );
		endforeach;

		return true;
	}

	/**
	 * Get currency
	 *
	 * Checks if the selected currency in WooCommerce is supported by Yandex Market.
	 * As of today it is allowed to list products in six currencies: RUB, UAH, BYR, KZT, USD and EUR.
	 * But! WooCommerce doesn't support BYR and KZT. And USD and EUR can be used only to export products.
	 * They will still be listed in RUB or UAH.
	 *
	 * @since   0.0.4
	 * @return  string                        Returns currency if it is supported, else false.
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
	 * @since   0.0.4
	 * @return  array                          Return the array of categories with IDs and parent IDs.
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
	 * @since   0.0.4
	 * @return  integer                        Return the price of delivery.
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
	 * Get simple products.
	 *
	 * Get simple products that are either in stock or avavilable for backorder.
	 *
	 * @since   0.0.4
	 *
	 * @param    string $backorders Yes or No for backorders.
	 *
	 * @return  array                                Return the array of products.
	 */
	public function get_products( $backorders ) {
		global $wpdb;

		return $wpdb->get_results(
			"SELECT p.ID, p.post_title AS name, p.post_content AS description, m1.meta_value AS vendorCode, p.post_excerpt AS sales_notes, m3.meta_value AS stock, m0.meta_value AS options
									FROM $wpdb->posts p
									INNER JOIN $wpdb->postmeta m0 ON p.ID = m0.post_id AND m0.meta_key = '_product_attributes'
									INNER JOIN $wpdb->postmeta m1 ON p.ID = m1.post_id AND m1.meta_key = '_sku'
									INNER JOIN $wpdb->postmeta m2 ON p.ID = m2.post_id AND m2.meta_key = '_visibility'
									INNER JOIN $wpdb->postmeta m3 ON p.ID = m3.post_id AND m3.meta_key = '_stock_status'
									INNER JOIN $wpdb->postmeta m4 ON p.ID = m4.post_id AND m4.meta_key = '_backorders'
									WHERE p.post_type = 'product'
											AND p.post_status = 'publish'
											AND p.post_password = ''
											AND m2.meta_value != 'hidden'
											" . ( $backorders == 'no' ? "AND m3.meta_value = 'instock'" : "" ) . "
											AND (m3.meta_value != 'outofstock' OR m4.meta_value = 'yes')
									ORDER BY p.ID DESC" );
	}

	/**
	 * Get IDs and SKU of variable products.
	 *
	 * Get IDs for variable product that are either in stock or avavilable for backorder.
	 *
	 * @since   0.2.0
	 *
	 * @param    int $prodID Product ID for which to fetch variable products.
	 *
	 * @return  array                            Return the array of variable product IDs and SKU.
	 */
	public function get_var_products( $prodID ) {
		global $wpdb;

		return $wpdb->get_results(
			"SELECT p.ID, m1.meta_value AS vendorCode
									FROM $wpdb->posts p
									INNER JOIN $wpdb->postmeta m1 ON p.ID = m1.post_id AND m1.meta_key = '_sku'
									WHERE p.post_type = 'product_variation'
											AND p.post_status = 'publish'
											AND p.post_password = ''
											AND p.post_parent = $prodID" );
	}

	/**
	 * Get the variable product link.
	 *
	 * For variable products you have to provide a direct link to the product.
	 * In WooCommerce this link is the same as the parent link plus it has added this appended
	 * to it: ?attribute_pa_[attribute_name]=[attribute_value].
	 * For example: ?attribute_pa_color=black.
	 *
	 * @since   0.2.0
	 *
	 * @param    int    $prodID Product ID for which to fetch data.
	 * @param    string $attr   Attribute in format pa_[attribute_name].
	 *
	 * @return  array                              Return the attribute value for the link.
	 */
	public function get_var_link( $prodID, $attr ) {
		global $wpdb;

		return $wpdb->get_row(
			"SELECT meta_value
								  FROM $wpdb->postmeta
								  WHERE post_id = $prodID
								  		AND meta_key = 'attribute_$attr'" );
	}

	/**
	 * Get price.
	 *
	 * @since   0.0.6
	 *
	 * @param    int $id Product ID for which to get price.
	 *
	 * @return  array                              Return the price and sale_price of product.
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
	 * @since   0.0.6
	 *
	 * @param    int $id    Product ID for which to get images.
	 * @param    int $count Number of images to get.
	 *
	 * @return  array                                Return the array of images.
	 */
	public function get_images( $id, $count ) {
		global $wpdb;

		return $wpdb->get_col(
			"SELECT p.guid
                 FROM $wpdb->postmeta AS pm
                 INNER JOIN $wpdb->posts AS p ON pm.meta_value=p.ID
                 WHERE pm.post_id = $id
                      AND pm.meta_key = '_thumbnail_id'
                 ORDER BY p.post_date DESC LIMIT $count" );
	}

	/**
	 * Get custom attributes.
	 *
	 * Used on WooCommerce settings page. It lets the user choose which of the custom attributes to use for vendor value.
	 *
	 * @since      0.0.7
	 * @return      array                                Return the array of custom attributes.
	 */
	public function get_attributes() {
		global $wpdb;
		
		return $wpdb->get_results(
			"SELECT attribute_name AS attr_key, attribute_label AS attr_value
								 FROM $wpdb->prefix" . "woocommerce_attribute_taxonomies", ARRAY_N );
	}

	/*
	 * Register crontab.
	 *
	 * @since   0.3.0
	 */
	public function crontab_activate() {
		// Schedule task
		if( !wp_next_scheduled( 'market_exporter_daily' ) ) {
			wp_schedule_event( time(), 'daily', 'market_exporter_daily' );
		}
	}

	/**
	 * Generate YML file.
	 *
	 * This is used for generating YML with CRON.
	 *
	 * @since      0.3.0
	 */
	public function generate_YML() {
		global $wpdb;

		// Check currency.
		$currency = $this->get_currecny();
		// Get plugin settings.
		$shop_settings = get_option( 'market_exporter_shop_settings' );
		// Get products.
		$ya_offers = $this->get_products( $shop_settings['backorders'] );

		if ( ! isset( $shop_settings['file_date'] ) ) {
			$shop_settings['file_date'] = 'yes';
		}

		if ( ! isset( $shop_settings['image_count'] ) ) {
			$shop_settings['image_count'] = 10;
		}

		$yml = '<?xml version="1.0" encoding="' . get_bloginfo( "charset" ) . '"?>' . PHP_EOL;
		$yml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">' . PHP_EOL;
		$yml .= '<yml_catalog date="' . date( "Y-m-d H:i" ) . '">' . PHP_EOL;
		$yml .= '  <shop>' . PHP_EOL;
		$yml .= '    <name>' . esc_html( $shop_settings['website_name'] ) . '</name>' . PHP_EOL;
		$yml .= '    <company>' . esc_html( $shop_settings['company_name'] ) . '</company>' . PHP_EOL;
		$yml .= '    <url>' . get_site_url() . '</url>' . PHP_EOL;
		$yml .= '    <currencies>' . PHP_EOL;
		if ( $currency == 'USD' || $currency == 'EUR' ) {
			$yml .= '      <currency id="RUR" rate="1"/>' . PHP_EOL;
			$yml .= '      <currency id="' . $currency . '" rate="СВ"/>' . PHP_EOL;
		} else {
			$yml .= '      <currency id="' . $currency . '" rate="1"/>' . PHP_EOL;
		}
		$yml .= '    </currencies>' . PHP_EOL;
		$yml .= '    <categories>' . PHP_EOL;
		foreach ( $this->get_categories() as $category ):
			if ( $category->parent == 0 ) {
				$yml .= '      <category id="' . $category->id . '">' . wp_strip_all_tags( $category->name ) . '</category>' . PHP_EOL;
			} else {
				$yml .= '      <category id="' . $category->id . '" parentId="' . $category->parent . '">' . wp_strip_all_tags( $category->name ) . '</category>' . PHP_EOL;
			}
		endforeach;
		$yml .= '    </categories>' . PHP_EOL;
		$yml .= '    <local_delivery_cost>' . $this->get_delivery() . '</local_delivery_cost>' . PHP_EOL;
		$yml .= '    <offers>' . PHP_EOL;
		foreach ( $ya_offers as $offer ):
			/*
				So what we do here is basically assume the product is a simple product and has no variations.
				We then check for variations. And if the product is indeed a variable product, we will list all variations as simple products.
			*/
			$has_variations  = false;
			$variation_count = 1;

			// Check if product has variations.
			$unser = array_values( unserialize( $offer->options ) );
			if ( $unser[0]['is_variation'] == 1 ) {
				$has_variations  = true;
				$variations      = $this->get_var_products( $offer->ID );
				$variation_count = count( $variations );
			}

			////// TODO: GET SKU OF VARIATION PRODUCT

			while ( $variation_count > 0 ):
				$variation_count --;
				$var_link = '';
				$offerID  = $has_variations ? $variations[ $variation_count ]->ID : $offer->ID;
				$offerSKU = $offer->vendorCode;

				// Probably there is a better way to get this value, but...
				// We are getting the last bit for the link: for example ?attribute_pa_color=black
				if ( $has_variations ) {
					$offer_options = unserialize( $offer->options );
					$link          = $this->get_var_link( $offerID, array_values( $offer_options )[0]['name'] );
					$var_link      = '?attribute_' . array_values( $offer_options )[0]['name'] . '=' . $link->meta_value;

					if ( $variations[ $variation_count ]->vendorCode ) {
						$offerSKU = $variations[ $variation_count ]->vendorCode;
					}
				}

				$images     = $this->get_images( $offerID, $shop_settings['image_count'] );
				$categoryId = get_the_terms( $offer->ID, 'product_cat' );
				$yml .= '      <offer id="' . $offerID . '" available="' . ( $offer->stock != "outofstock" ? "true" : "false" ) . '">' . PHP_EOL;
				$yml .= '        <url>' . get_permalink( $offer->ID ) . $var_link . '</url>' . PHP_EOL;
				// Price.
				$price = $this->get_price( $offerID );
				if ( $price['sale_price'] && ( $price['sale_price'] < $price['price'] ) ) {
					$yml .= '        <price>' . $price['sale_price'] . '</price>' . PHP_EOL;
					$yml .= '        <oldprice>' . $price['price'] . '</oldprice>' . PHP_EOL;
				} else {
					$yml .= '        <price>' . $price['price'] . '</price>' . PHP_EOL;
				}
				$yml .= '        <currencyId>' . $currency . '</currencyId>' . PHP_EOL;
				$yml .= '        <categoryId>' . $categoryId[0]->term_id . '</categoryId>' . PHP_EOL;
				// Market category.
				if ( isset( $shop_settings['market_category'] ) && $shop_settings['market_category'] != 'not_set' ) {
					$market_category = wc_get_product_terms( $offer->ID, 'pa_' . $shop_settings['market_category'], array( 'fields' => 'names' ) );
					if ( $market_category ) {
						$yml .= '        <market_category>' . wp_strip_all_tags( array_shift( $market_category ) ) . '</market_category>' . PHP_EOL;
					}
				}
				foreach ( $images as $image ):
					if ( strlen( utf8_decode( $image ) ) <= 512 ) {
						$yml .= '        <picture>' . $image . '</picture>' . PHP_EOL;
					}
				endforeach;
				$yml .= '        <delivery>true</delivery>' . PHP_EOL;
				$yml .= '        <name>' . wp_strip_all_tags( $offer->name ) . '</name>' . PHP_EOL;
				// Vendor.
				if ( isset( $shop_settings['vendor'] ) && $shop_settings['vendor'] != 'not_set' ) {
					$vendor = wc_get_product_terms( $offer->ID, 'pa_' . $shop_settings['vendor'], array( 'fields' => 'names' ) );
					if ( $vendor ) {
						$yml .= '        <vendor>' . wp_strip_all_tags( array_shift( $vendor ) ) . '</vendor>' . PHP_EOL;
					}
				}
				// Vendor code.
				if ( $offer->vendorCode ) {
					$yml .= '        <vendorCode>' . wp_strip_all_tags( $offerSKU ) . '</vendorCode>' . PHP_EOL;
				}
				// Description.
				if ( $offer->description ) {
					$yml .= '        <description>' . htmlspecialchars( html_entity_decode( wp_strip_all_tags( $offer->description ), ENT_COMPAT, "UTF-8" ) ) . '</description>' . PHP_EOL;
				}
				// Sales notes.
				if ( ( $shop_settings['sales_notes'] == 'yes' ) && ( $offer->sales_notes ) ) {
					$yml .= '        <sales_notes>' . wp_strip_all_tags( $offer->sales_notes ) . '</sales_notes>' . PHP_EOL;
				}
				$yml .= '      </offer>' . PHP_EOL;
			endwhile;
		endforeach;
		$yml .= '    </offers>' . PHP_EOL;
		$yml .= '  </shop>' . PHP_EOL;
		$yml .= '</yml_catalog>' . PHP_EOL;

		$file_path = $this->write_file( $yml, $shop_settings['file_date'] );

		// Reset Query.
		wp_reset_query();
		// Clear the SQL result cache.
		$wpdb->flush();
	}


}

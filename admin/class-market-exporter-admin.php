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
	 * Get custom attributes.
	 *
	 * Used on WooCommerce settings page. It lets the user choose which of the custom attributes to use for vendor value.
	 *
	 * @since      0.0.7
	 * @return      array                                Return the array of custom attributes.
	 */
	private function get_attributes() {
		global $wpdb;

		return $wpdb->get_results(
			"SELECT attribute_name AS attr_key, attribute_label AS attr_value
								 FROM $wpdb->prefix" . "woocommerce_attribute_taxonomies", ARRAY_N );
	}

	/*
	 * Register crontab.
	 *
	 * @since   0.2.0
	 */
	public function crontab_activate() {
		// Schedule task
		if( !wp_next_scheduled( 'market_exporter_daily' ) ) {
			wp_schedule_event( time(), 'daily', 'market_exporter_daily' );
		}
	}

}

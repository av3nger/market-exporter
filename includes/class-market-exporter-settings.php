<?php
/**
 * Plugin settings functionality.
 *
 * @link       https://github.com/av3nger/market-exporter/
 * @since      1.1.0
 *
 * @package    Market_Exporter
 * @subpackage Market_Exporter/includes
 */

/**
 * Plugin settings functionality.
 *
 * Everything related to registering, validation/updating plugin settings.
 *
 * @package    Market_Exporter
 * @subpackage Market_Exporter/includes
 * @author     Anton Vanyukov <a.vanyukov@testor.ru>
 */
class Market_Exporter_Settings {
	/**
	 * The ID of this plugin.
	 *
	 * @access private
	 * @var    string  $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * Market_Exporter_Settings constructor.
	 *
	 * @param string $plugin_name  The name of this plugin.
	 */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

	/**
	 * Add settings fields.
	 *
	 * @since 0.0.4
	 * @since 1.1.0  Moved from Market_Exporter_Admin
	 */
	public function register_settings() {
		register_setting(
			$this->plugin_name,
			'market_exporter_shop_settings',
			array( $this, 'validate_shop_settings_array' )
		);

		/**
		 **************************
		 * Global settings
		 **************************
		 */
		add_settings_section(
			'market_exporter_section_general',
			__( 'Global settings', 'market-exporter' ),
			null,
			$this->plugin_name
		);

		$this->get_global_settings();

		/**
		 **************************
		 * Offers settings
		 **************************
		 */
		add_settings_section(
			'market_exporter_section_offers',
			__( 'Settings for offers', 'market-exporter' ),
			null,
			$this->plugin_name
		);

		$this->get_offer_settings();

		/**
		 **************************
		 * Shop tag settings
		 **************************
		 */
		add_settings_section(
			'market_exporter_section_delivery',
			__( 'Delivery options', 'market-exporter' ),
			null,
			$this->plugin_name
		);

		$this->get_shop_settings();
		$this->get_delivery_settings();

		/**
		 **************************
		 * Extra settings
		 **************************
		 */
		add_settings_section(
			'market_exporter_section_extra',
			__( 'Extra settings', 'market-exporter' ),
			null,
			$this->plugin_name
		);

		$this->get_extra_settings();
	}

	/**
	 * Register global settings fields.
	 */
	private function get_global_settings() {
		// Add website name text field option.
		add_settings_field(
			'market_exporter_website_name',
			__( 'Website name', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_general',
			array(
				'label_for'   => 'website_name',
				'placeholder' => __( 'Website name', 'market-exporter' ),
				'description' => __( 'Not longer than 20 characters. Has to be the name of the shop, that is configured in Yandex Market.', 'market-exporter' ),
				'type'        => 'text',
			)
		);

		// Add company name text field option.
		add_settings_field(
			'market_exporter_company_name',
			__( 'Company name', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_general',
			array(
				'label_for'   => 'company_name',
				'placeholder' => __( 'Company name', 'market-exporter' ),
				'description' => __( 'Full company name. Not published in Yandex Market.', 'market-exporter' ),
				'type'        => 'text',
			)
		);

		// Add file_date field option.
		add_settings_field(
			'market_exporter_file_date',
			__( 'Add date to YML file name', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_general',
			array(
				'label_for'   => 'file_date',
				'description' => __( 'If enabled YML file will have current date at the end: ym-export-yyyy-mm-dd.yml.', 'market-exporter' ),
				'type'        => 'checkbox',
			)
		);

		// Add cron options.
		add_settings_field(
			'market_exporter_cron',
			__( 'Cron', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_general',
			array(
				'label_for'   => 'cron',
				'type'        => 'select',
				'options'     => array(
					'disabled'   => __( 'Disabled', 'market-exporter' ),
					'hourly'     => __( 'Every hour', 'market-exporter' ),
					'twicedaily' => __( 'Twice a day', 'market-exporter' ),
					'daily'      => __( 'Daily', 'market-exporter' ),
				),
			)
		);
	}

	/**
	 * Register offer settings fields.
	 */
	private function get_offer_settings() {
		// Add selection of 'vendor' property.
		$attributes_array['not_set'] = __( 'Disabled', 'market-exporter' );
		foreach ( $this->get_attributes() as $attribute ) {
			$attributes_array[ $attribute[0] ] = $attribute[1];
		}

		add_settings_field(
			'market_exporter_vendor',
			__( 'Vendor', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'vendor',
				'description' => __( 'Vendor property.', 'market-exporter' ),
				'type'        => 'select',
				'options'     => $attributes_array,
			)
		);

		// Add selection of 'model' property.
		add_settings_field(
			'market_exporter_model',
			__( 'Model', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'model',
				'description' => __( 'Model property.', 'market-exporter' ),
				'type'        => 'select',
				'options'     => $attributes_array,
			)
		);

		add_settings_field(
			'market_exporter_type_prefix',
			__( 'typePrefix', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'type_prefix',
				'description' => __( 'Property typePrefix. Type or product category.', 'market-exporter' ),
				'type'        => 'select',
				'options'     => $attributes_array,
			)
		);

		// Add backorders field option.
		add_settings_field(
			'market_exporter_backorders',
			__( 'Export products with backorders', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'backorders',
				'description' => __( 'If enabled products that are available for backorder will be exported to YML.', 'market-exporter' ),
				'type'        => 'checkbox',
			)
		);

		// Add categories multiselect option.
		add_settings_field(
			'market_exporter_include_cat',
			__( 'Include selected categories', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'include_cat',
				'description' => __( 'Only selected categories will be included in the export file. Hold down the control (ctrl) button on Windows or command (cmd) on Mac to select multiple options. If nothing is selected - all the categories will be exported.', 'market-exporter' ),
				'type'        => 'multiselect',
			)
		);

		// Add sales_notes field.
		add_settings_field(
			'market_exporter_sales_notes',
			__( 'sales_notes', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'sales_notes',
				'description' => __( 'Not longer than 50 characters.', 'market-exporter' ),
				'type'        => 'textarea',
			)
		);

		// Add manufacturer_warranty field.
		add_settings_field(
			'market_exporter_warranty',
			__( 'Manufacturer warranty', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'warranty',
				'description' => __( 'Define if manufacturer warranty is available for selected product. Available values: true of false.', 'market-exporter' ),
				'type'        => 'select',
				'options'     => $attributes_array,
			)
		);

		// Add country_of_origin field.
		add_settings_field(
			'market_exporter_origin',
			__( 'Country of origin', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'origin',
				'description' => sprintf(
					/* translators: %s: link to naming rules */
					__( 'Define country of origin for a product. See <a href="%s" target="_blank">this link</a> for a list of available values.', 'market-exporter' ),
				'http://partner.market.yandex.ru/pages/help/Countries.pdf' ),
				'type'        => 'select',
				'options'     => $attributes_array,
			)
		);

		// Add weight and size option.
		add_settings_field(
			'market_exporter_size',
			__( 'Weight and size data', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'size',
				'description' => __( 'If enabled weight and size data from WooCommerce will be exported to Weight and Dimensions elements.', 'market-exporter' ),
				'type'        => 'checkbox',
			)
		);

		// Add parameters multiselect option.
		add_settings_field(
			'market_exporter_params',
			__( 'Use selected parameters', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'params',
				'description' => __( 'Selected attributes will be exported as a parameters. Hold down the control (ctrl) button on Windows or command (cmd) on Mac to select multiple options.', 'market-exporter' ),
				'type'        => 'multiselect',
			)
		);

		// Add all parameters select.
		add_settings_field(
			'market_exporter_params_all',
			__( 'Export all parameters', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'params_all',
				'description' => __( 'All available attributes will be exported as parameters.', 'market-exporter' ),
				'type'        => 'checkbox',
			)
		);

		// Add image count option.
		add_settings_field(
			'market_exporter_image_count',
			__( 'Images per product', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'image_count',
				'placeholder' => __( 'Images per product', 'market-exporter' ),
				'description' => __( 'Not more than 10 images', 'market-exporter' ),
				'type'        => 'text',
			)
		);
	}

	/**
	 * Register shop tag settings fields.
	 */
	private function get_shop_settings() {
		$select_options = array(
			'disabled' => __( 'Disabled', 'market-exporter' ),
			'true'     => __( 'true', 'market-exporter' ),
			'false'    => __( 'false', 'market-exporter' ),
		);

		// Delivery element.
		add_settings_field(
			'market_exporter_delivery',
			__( 'Delivery', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_delivery',
			array(
				'label_for'   => 'delivery',
				'description' => __( "Use the delivery element to indicate the possibility of delivery to the buyer's address in the home region of the store.", 'market-exporter' ),
				'type'        => 'select',
				'options'     => $select_options,
			)
		);

		// Pickup element.
		add_settings_field(
			'market_exporter_pickup',
			__( 'Pickup', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_delivery',
			array(
				'label_for'   => 'pickup',
				'description' => __( 'Use the pickup element to indicate the possibility of receiving goods at the issuance point.', 'market-exporter' ),
				'type'        => 'select',
				'options'     => $select_options,
			)
		);

		// Store element.
		add_settings_field(
			'market_exporter_store',
			__( 'Store', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_delivery',
			array(
				'label_for'   => 'store',
				'description' => __( 'Use the store element to indicate the possibility of buying without a preliminary order at the point of sale.', 'market-exporter' ),
				'type'        => 'select',
				'options'     => $select_options,
			)
		);
	}

	/**
	 * Register delivery options settings fields.
	 */
	private function get_delivery_settings() {
		// Add all parameters select.
		add_settings_field(
			'market_exporter_delivery_options',
			__( 'Use delivery-options', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_delivery',
			array(
				'label_for'   => 'delivery_options',
				'description' => __( 'Use delivery-options parameters defined below. Global options.', 'market-exporter' ),
				'type'        => 'checkbox',
			)
		);

		// Cost element.
		add_settings_field(
			'market_exporter_cost',
			__( 'Cost', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_delivery',
			array(
				'label_for'   => 'cost',
				'placeholder' => __( '100', 'market-exporter' ),
				'description' => __( 'Delivery-options cost element. Used to indicate the price of delivery. Use maximum value if cost is differs for different locations.', 'market-exporter' ),
				'type'        => 'text',
			)
		);

		// Days element.
		add_settings_field(
			'market_exporter_days',
			__( 'Days', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_delivery',
			array(
				'label_for'   => 'days',
				'placeholder' => __( '0, 1, 2, 3-5, etc', 'market-exporter' ),
				'description' => __( 'Delivery-options days element. Either a value or a range for the actual days it takes to deliver a product.', 'market-exporter' ),
				'type'        => 'text',
			)
		);

		// Days element.
		add_settings_field(
			'market_exporter_order_before',
			__( 'Order before', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_delivery',
			array(
				'label_for'   => 'order_before',
				'placeholder' => __( '0-24', 'market-exporter' ),
				'description' => __( 'Delivery-options order-before element. Accepts values from 0 to 24. If the order is made before this time, delivery will be on time.', 'market-exporter' ),
				'type'        => 'text',
			)
		);
	}

	/**
	 * Register extra settings fields.
	 */
	private function get_extra_settings() {
		// Delivery element.
		add_settings_field(
			'market_exporter_description',
			__( 'Product description', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_extra',
			array(
				'label_for'   => 'description',
				'description' => __( 'Specify the way the description is exported. Default is to try and get the product description, if empty - get short description.', 'market-exporter' ),
				'type'        => 'select',
				'options'     => array(
					'default' => __( 'Default', 'market-exporter' ),
					'long'    => __( 'Only description', 'market-exporter' ),
					'short'   => __( 'Only short description', 'market-exporter' ),
				),
			)
		);

		// Add on product update hook.
		add_settings_field(
			'market_exporter_update_on_change',
			__( 'Update file on product change', 'market-exporter' ),
			array( 'Market_Exporter_Admin', 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_extra',
			array(
				'label_for'   => 'update_on_change',
				'description' => __( 'Regenerate file on product create/update.', 'market-exporter' ),
				'type'        => 'checkbox',
			)
		);
	}

	/**
	 * Get custom attributes.
	 *
	 * Used on WooCommerce settings page. It lets the user choose which of the custom attributes to use for vendor value.
	 *
	 * @since 0.0.7
	 * @since 1.1.0  Moved from Market_Exporter_Admin
	 *
	 * @return array Return the array of custom attributes.
	 */
	private function get_attributes() {
		global $wpdb;

		return $wpdb->get_results(
			"SELECT attribute_name AS attr_key, attribute_label AS attr_value
					FROM {$wpdb->prefix}woocommerce_attribute_taxonomies", ARRAY_N ); // db call ok; no-cache ok.
	}

	/**
	 * Sanitize shop settings array.
	 *
	 * @since 0.0.5
	 * @since 1.1.0  Moved from Market_Exporter_Admin
	 *
	 * @param array $input  Current settings.
	 *
	 * @return array $output Sanitized settings.
	 */
	public function validate_shop_settings_array( $input ) {
		$output = get_option( 'market_exporter_shop_settings' );

		/**
		 * General options.
		 */
		$output['website_name'] = sanitize_text_field( $input['website_name'] );
		$output['company_name'] = sanitize_text_field( $input['company_name'] );
		$output['file_date']    = ( isset( $input['file_date'] ) ) ? true : false;
		$output['cron']         = sanitize_text_field( $input['cron'] );
		// Update cron schedule.
		Market_Exporter_Admin::update_cron_schedule( $output['cron'] );

		/**
		 * Product options.
		 */
		// According to Yandex up to 10 images per product.
		$images = intval( $input['image_count'] );
		if ( $images > 10 ) {
			$output['image_count'] = 10;
		} elseif ( $images <= 0 ) {
			$output['image_count'] = 1;
		} else {
			$output['image_count'] = $images;
		}

		$output['vendor']          = sanitize_text_field( $input['vendor'] );
		$output['model']           = sanitize_text_field( $input['model'] );
		$output['type_prefix']     = sanitize_text_field( $input['type_prefix'] );
		$output['warranty']        = sanitize_text_field( $input['warranty'] );
		$output['origin']          = sanitize_text_field( $input['origin'] );
		if ( ! function_exists( 'sanitize_textarea_field' ) ) {
			$output['sales_notes'] = sanitize_text_field( $input['sales_notes'] );
		} else {
			$output['sales_notes'] = sanitize_textarea_field( $input['sales_notes'] );
		}

		$output['backorders']      = ( isset( $input['backorders'] ) ) ? true : false;
		$output['size']            = ( isset( $input['size'] ) ) ? true : false;

		// Convert to int array.
		if ( isset( $input['include_cat'] ) ) {
			$output['include_cat'] = array_map( 'intval', $input['include_cat'] );
		} else {
			$output['include_cat'] = array();
		}

		$output['params_all']      = ( isset( $input['params_all'] ) ) ? true : false;

		// Only save individual params if all params checkbox is not set.
		if ( isset( $input['params'] ) && ! $output['params_all'] ) {
			$output['params']      = array_map( 'intval', $input['params'] );
		} else {
			$output['params']      = array();
		}

		/**
		 * Delivery options.
		 */
		$output['delivery'] = sanitize_text_field( $input['delivery'] );
		$output['pickup']   = sanitize_text_field( $input['pickup'] );
		$output['store']    = sanitize_text_field( $input['store'] );

		/**
		 * Delivery-options settings.
		 */
		$output['delivery_options'] = isset( $input['delivery_options'] ) ? true : false;
		$output['cost']             = isset( $input['cost'] ) ? absint( $input['cost'] ) : 0;
		$output['days']             = isset( $input['days'] ) ? sanitize_text_field( $input['days'] ) : '';
		$output['order_before']     = '';
		if ( isset( $input['order_before'] ) && ! empty( $input['order_before'] ) ) {
			$output['order_before'] = absint( $input['order_before'] );
		}

		/**
		 * Extra options.
		 */
		$output['description']      = sanitize_text_field( $input['description'] );
		$output['update_on_change'] = ( isset( $input['update_on_change'] ) ) ? true : false;

		return $output;
	}

}

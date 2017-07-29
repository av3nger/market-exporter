<?php
/**
 * Market Exporter: Market_Exporter_Admin class
 *
 * The admin-specific functionality of the plugin. Defines the plugin name, version, and two examples hooks for
 * how to enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package Market_Exporter
 * @since   0.0.1
 */
class Market_Exporter_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since  0.0.1
	 * @access private
	 * @var    string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * Plugin options.
	 *
	 * @since  0.4.4
	 * @access private
	 * @var    array $options  Current plugin options.
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 0.0.1
	 * @param string $plugin_name The name of this plugin.
	 */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
		$this->options     = get_option( 'market_exporter_shop_settings' );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 0.0.1
	 * @param string $hook  Page from where it is called.
	 */
	public function enqueue_styles( $hook ) {
		if ( 'woocommerce_page_market-exporter' !== $hook ) {
			return;
		}
		wp_enqueue_style( "{$this->plugin_name}-select2", plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), null, 'all' );
		wp_enqueue_style( "{$this->plugin_name}-admin", plugin_dir_url( __FILE__ ) . 'css/market-exporter-admin.css', array(), null, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 0.0.1
	 * @param string $hook  Page from where it is called.
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'woocommerce_page_market-exporter' !== $hook ) {
			return;
		}
		wp_enqueue_script( "{$this->plugin_name}-select2", plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), null, false );
		wp_enqueue_script( "{$this->plugin_name}-admin", plugin_dir_url( __FILE__ ) . 'js/market-exporter-admin.js', array( 'jquery' ), null, false );

		wp_localize_script( "{$this->plugin_name}-admin", 'ajax_strings', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'me_dismiss_notice' ),
		) );
	}

	/**
	 * Add sub menu page to the WooCommerce menu.
	 *
	 * @since 0.0.1
	 */
	public function add_admin_page() {
		add_submenu_page(
			'woocommerce',
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
	 * @since 0.0.1
	 */
	public function display_admin_page() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/market-exporter-admin-display.php';
	}

	/**
	 * Add settings fields.
	 *
	 * @since 0.0.4
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

		// Add file_date field option.
		add_settings_field(
			'market_exporter_file_date',
			__( 'Add date to YML file name', 'market-exporter' ),
			array( $this, 'input_fields_cb' ),
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
			array( $this, 'input_fields_cb' ),
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

		/**
		 **************************
		 * Shop tag settings
		 **************************
		 */
		add_settings_section(
			'market_exporter_section_shop',
			__( 'Settings in the shop tag', 'market-exporter' ),
			null,
			$this->plugin_name
		);

		// Add website name text field option.
		add_settings_field(
			'market_exporter_website_name',
			__( 'Website name', 'market-exporter' ),
			array( $this, 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_shop',
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
			array( $this, 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_shop',
			array(
				'label_for'   => 'company_name',
				'placeholder' => __( 'Company name', 'market-exporter' ),
				'description' => __( 'Full company name. Not published in Yandex Market.', 'market-exporter' ),
				'type'        => 'text',
			)
		);

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

		// Add selection of 'vendor' property.
		$attributes_array['not_set'] = __( 'Disabled', 'market-exporter' );
		foreach ( $this->get_attributes() as $attribute ) {
			$attributes_array[ $attribute[0] ] = $attribute[1];
		}

		add_settings_field(
			'market_exporter_vendor',
			__( 'Vendor', 'market-exporter' ),
			array( $this, 'input_fields_cb' ),
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
			array( $this, 'input_fields_cb' ),
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
			array( $this, 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'type_prefix',
				'description' => sprintf(
					/* translators: %s: link to naming rules */
					__( 'typePrefix property. Type or product category. See <a href="%s" target="_blank">this link</a> for naming rules.', 'market-exporter' ),
				'https://yandex.ru/support/partnermarket/elements/vendor-name-model.html#mistakes__wrong-type' ),
				'type'        => 'select',
				'options'     => $attributes_array,
			)
		);

		// Add backorders field option.
		add_settings_field(
			'market_exporter_backorders',
			__( 'Export products with backorders', 'market-exporter' ),
			array( $this, 'input_fields_cb' ),
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
			array( $this, 'input_fields_cb' ),
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
			array( $this, 'input_fields_cb' ),
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
			array( $this, 'input_fields_cb' ),
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
			array( $this, 'input_fields_cb' ),
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
			array( $this, 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'size',
				'description' => __( 'If enabled weight and size data from WooCommerce will be exported to Width, Depth, Height and Weight params.', 'market-exporter' ),
				'type'        => 'checkbox',
			)
		);

		// Add parameters multiselect option.
		add_settings_field(
			'market_exporter_params',
			__( 'Use selected parameters', 'market-exporter' ),
			array( $this, 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_offers',
			array(
				'label_for'   => 'params',
				'description' => __( 'Selected attributes will be exported as a parameters. Hold down the control (ctrl) button on Windows or command (cmd) on Mac to select multiple options.', 'market-exporter' ),
				'type'        => 'multiselect',
			)
		);

	}

	/**
	 * Callback function for add_settings_field().
	 * The values for $args are defined at the add_settings_field() function.
	 *
	 * @since 0.3.0
	 * @param array $args Arguments array.
	 */
	public function input_fields_cb( $args ) {
		if ( 'text' === esc_attr( $args['type'] ) || 'checkbox' === esc_attr( $args['type'] ) ) : ?>

			<input id="<?php echo esc_attr( $args['label_for'] ); ?>"
				   type="<?php echo esc_attr( $args['type'] ); ?>"
				   name="market_exporter_shop_settings[<?php echo esc_attr( $args['label_for'] ); ?>]"
				   value="<?php echo esc_attr( $this->options[ $args['label_for'] ] ); ?>"
					<?php if ( 'text' === esc_attr( $args['type'] ) ) :?>
						placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
					<?php endif; ?>
					<?php echo ( 'checkbox' === esc_attr( $args['type'] ) ) ? checked( $this->options[ $args['label_for'] ] ) : ''; ?>
			>

		<?php elseif ( 'textarea' === esc_attr( $args['type'] ) ) : ?>

			<textarea cols="39" rows="3" maxlength="50" id="<?php echo esc_attr( $args['label_for'] ); ?>"
					  name="market_exporter_shop_settings[<?php echo esc_attr( $args['label_for'] ); ?>]"
					  title="<?php echo esc_attr( $args['label_for'] ); ?>"><?php echo esc_html( $this->options[ $args['label_for'] ] ); ?></textarea>

		<?php elseif ( 'select' === esc_attr( $args['type'] ) ) : ?>

			<select id="<?php echo esc_attr( $args['label_for'] ); ?>"
					name="market_exporter_shop_settings[<?php echo esc_attr( $args['label_for'] ); ?>]">
				<?php foreach ( $args['options'] as $key => $value ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $this->options[ $args['label_for'] ] === $key ); ?>>
						<?php echo esc_html( $value ); ?>
					</option>
				<?php endforeach; ?>
			</select>

		<?php
		elseif ( 'multiselect' === esc_attr( $args['type'] ) ) :
			$select_array = array();
			if ( isset( $this->options[ $args['label_for'] ] ) ) {
				$select_array = $this->options[ $args['label_for'] ];
			}

			echo '<select id="' . esc_attr( $args['label_for'] ) . '" name="market_exporter_shop_settings[' . esc_attr( $args['label_for'] ) . '][]" multiple>';

			/*
			 * So far multiselect can be for included categories and parameters.
			 * The categories multiselect can include subcategories, that's why we have to go over all of the subcategories and parents.
			 * The parameters multiselect only includes top-level items.
			 */
			if ( 'include_cat' === esc_attr( $args['label_for'] ) ) {
				foreach ( get_categories( array(
					'taxonomy' => 'product_cat',
					'parent'   => 0,
				)) as $category ) {
					echo '<option value="' . esc_attr( $category->cat_ID ) . '" ' . selected( in_array( $category->cat_ID, $select_array, true ) ) . '>' . esc_html( $category->name ) . '</option>';
					foreach ( get_categories( array(
						'taxonomy' => 'product_cat',
						'parent'   => $category->cat_ID,
					)) as $subcategory ) {
						echo '<option value="' . esc_attr( $subcategory->cat_ID ) . '" ' . selected( in_array( $subcategory->cat_ID, $select_array, true ) ) . '>&mdash;&nbsp;' . esc_html( $subcategory->name ) . '</option>';
					}
				}
			}

			if ( 'params' === esc_attr( $args['label_for'] ) ) {
				foreach ( wc_get_attribute_taxonomies() as $attribute ) {
					echo '<option value="' . esc_attr( $attribute->attribute_id ) . '" ' . selected( in_array( $attribute->attribute_id, $select_array, true ) ) . '>' . esc_html( $attribute->attribute_label ) . '</option>';
				}
			}
			echo '</select>';
		endif;

		if ( isset( $args['description'] ) ) : ?>
			<p class="description">
				<?php
				$tags = array(
					'a'      => array(
						'href'  => array(),
						'title' => array(),
					),
					'br'     => array(),
					'em'     => array(),
					'strong' => array(),
				);
				echo wp_kses( $args['description'], $tags ); ?>
			</p>
		<?php endif;
	}

	/**
	 * Sanitize shop settings array.
	 *
	 * @since  0.0.5
	 * @param  array $input  Current settings.
	 * @return array $output Sanitized settings.
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
		$output['file_date']       = ( isset( $input['file_date'] ) ) ? true : false;
		$output['size']            = ( isset( $input['size'] ) ) ? true : false;

		// Convert to int array.
		if ( isset( $input['include_cat'] ) ) {
			$output['include_cat'] = array_map( 'intval', $input['include_cat'] );
		}
		if ( isset( $input['params'] ) ) {
			$output['params']      = array_map( 'intval', $input['params'] );
		}

		$output['cron']            = sanitize_text_field( $input['cron'] );
		// Update cron schedule.
		$this->update_cron_schedule( $output['cron'] );

		return $output;
	}

	/**
	 * Add Setings link to plugin in plugins list.
	 *
	 * @since  0.0.5
	 * @param  array $links Links for the current plugin.
	 * @return array $links New links array for the current plugin.
	 */
	public function plugin_add_settings_link( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name . '&tab=settings' ) . '">' . __( 'Settings', 'market-exporter' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Get custom attributes.
	 *
	 * Used on WooCommerce settings page. It lets the user choose which of the custom attributes to use for vendor value.
	 *
	 * @since  0.0.7
	 * @return array Return the array of custom attributes.
	 */
	private function get_attributes() {
		global $wpdb;

		return $wpdb->get_results(
			"SELECT attribute_name AS attr_key, attribute_label AS attr_value
					FROM $wpdb->prefix" . "woocommerce_attribute_taxonomies", ARRAY_N );
	}

	/**
	 * Register crontab.
	 *
	 * @since 0.2.0
	 * @deprecated 0.4.4
	 */
	public function crontab_activate() {
		// Schedule task.
		if ( ! wp_next_scheduled( 'market_exporter_cron' ) ) {
			wp_schedule_event( time(), 'hourly', 'market_exporter_cron' );
		}
	}

	/**
	 * Update cron schedule.
	 *
	 * @since 0.4.4
	 * @param string $interval  Cron interval. Accepts: hourly, twicedaily, daily.
	 */
	public function update_cron_schedule( $interval ) {
		wp_clear_scheduled_hook( 'market_exporter_cron' );
		if ( 'disabled' !== $interval ) {
			wp_schedule_event( time(), $interval, 'market_exporter_cron' );
		}
	}

}

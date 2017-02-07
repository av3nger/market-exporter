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
        wp_enqueue_style( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), null, 'all' );
		wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/market-exporter-admin.css', array(), null, 'all' );
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
        wp_enqueue_script( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), null, false );
		wp_enqueue_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/market-exporter-admin.js', array( 'jquery' ), null, false );
	}

	/**
	 * Add sub menu page to the WooCommerce menu.
	 *
	 * @since   0.0.1
	 */
	public function add_admin_page() {
        add_submenu_page(
            'woocommerce',
            __( 'Market Exporter', $this->plugin_name ),
            __( 'Market Exporter', $this->plugin_name ),
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
	 * Add settings fields.
	 *
	 * @since   0.0.4
	 */
	public function register_settings() {
        register_setting(
            $this->plugin_name,
            'market_exporter_shop_settings',
            array( &$this, 'validate_shop_settings_array' )
        );

        add_settings_section(
            'market_exporter_section_general',
            __('Global settings', $this->plugin_name),
            array( &$this, 'section_general_cb' ),
            $this->plugin_name
        );

		// Add website name text field option.
        add_settings_field(
            'market_exporter_website_name',
            __( 'Website name', $this->plugin_name ),
            array( &$this, 'input_fields_cb' ),
            $this->plugin_name,
            'market_exporter_section_general',
            [
                'label_for'         => 'website_name',
                'placeholder'       => __( 'Website name', $this->plugin_name ),
                'description'       => __( 'Not longer than 20 characters. Has to be the name of the shop, that is configured in Yandex Market.', $this->plugin_name ),
                'type'              => 'text'
            ]
        );

		// Add company name text field option.
        add_settings_field(
            'market_exporter_company_name',
            __( 'Company name', $this->plugin_name ),
            array( &$this, 'input_fields_cb' ),
            $this->plugin_name,
            'market_exporter_section_general',
            [
                'label_for'         => 'company_name',
                'placeholder'       => __( 'Company name', $this->plugin_name ),
                'description'       => __( 'Full company name. Not published in Yandex Market.', $this->plugin_name ),
                'type'              => 'text'
            ]
        );

		// Add file_date field option.
        add_settings_field(
            'market_exporter_file_date',
            __( 'Add date to YML file name', $this->plugin_name ),
            array( &$this, 'input_fields_cb' ),
            $this->plugin_name,
            'market_exporter_section_general',
            [
                'label_for'         => 'file_date',
                'description'       => __( 'If enabled YML file will have current date at the end: ym-export-yyyy-mm-dd.yml.', $this->plugin_name ),
                'type'              => 'checkbox'
            ]
        );

		// Add image count text field option.
		add_settings_field(
			'market_exporter_image_count',
			__( 'Images per product', $this->plugin_name ),
			array( &$this, 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_general',
			[
				'label_for'         => 'image_count',
				'placeholder'       => __( 'Images per product', $this->plugin_name ),
				'description'       => __( 'Max number of images to export for product. Max 10 images.', $this->plugin_name ),
				'type'              => 'text'
			]
		);

		// Add selection of 'vendor' property.
		$attributes_array['not_set'] = __( 'Disabled', $this->plugin_name );
		foreach ( $this->get_attributes() as $attribute ) {
			$attributes_array[ $attribute[0] ] = $attribute[1];
		}
		add_settings_field(
			'market_exporter_vendor',
			__( 'Vendor property', $this->plugin_name ),
			array( &$this, 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_general',
			[
				'label_for'         => 'vendor',
				'description'       => __( 'Custom property used to specify vendor.', $this->plugin_name ),
				'type'              => 'select',
				'options'			=> $attributes_array
			]
		);

		// Add market_category text field option.
		add_settings_field(
			'market_exporter_market_category',
			__( 'Market category property', $this->plugin_name ),
			array( &$this, 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_general',
			[
				'label_for'         => 'market_category',
				'description'       => sprintf( __( 'Category of product on Yandex Market. Can be set to a value from <a href="%s" target="_blank">this list</a> only.', $this->plugin_name ), 'http://download.cdn.yandex.net/market/market_categories.xls' ),
				'type'              => 'select',
				'options'			=> $attributes_array
			]
		);

		// Add backorders field option.
		add_settings_field(
			'market_exporter_backorders',
			__( 'Export products with backorders', $this->plugin_name ),
			array( &$this, 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_general',
			[
				'label_for'         => 'backorders',
				'description'       => __( 'If enabled products that are available for backorder will be exported to YML.', $this->plugin_name ),
				'type'              => 'checkbox'
			]
		);

		// Add categories multiselect option.
		add_settings_field(
			'market_exporter_include_cat',
			__( 'Include selected categories', $this->plugin_name ),
			array( &$this, 'input_fields_cb' ),
			$this->plugin_name,
			'market_exporter_section_general',
			[
				'label_for'         => 'include_cat',
				'description'       => __( 'Only selected categories will be included in the export file. Hold down the control (ctrl) button on Windows or command (cmd) on Mac to select multiple options. If nothing is selected - all the categories will be exported.', $this->plugin_name ),
				'type'              => 'multiselect'
			]
		);


        // Add sales_notes field.
        add_settings_field(
            'market_exporter_sales_notes',
            __( 'Sales notes', $this->plugin_name ),
            array( &$this, 'input_fields_cb' ),
            $this->plugin_name,
            'market_exporter_section_general',
            [
                'label_for'         => 'sales_notes',
                'placeholder'       => __( 'Sales notes', $this->plugin_name ),
                'description'       => __( 'Not longer than 50 characters.', $this->plugin_name ),
                'type'              => 'textarea'
            ]
        );


        // Add weight and size option.
        add_settings_field(
            'market_exporter_size',
            __( 'Export weight and size data', $this->plugin_name ),
            array( &$this, 'input_fields_cb' ),
            $this->plugin_name,
            'market_exporter_section_general',
            [
                'label_for'         => 'size',
                'description'       => __( 'If enabled weight and size data from WooCommerce will be exported to Width, Depth, Height and Weight params.', $this->plugin_name ),
                'type'              => 'checkbox'
            ]
        );

        // Add parameters multiselect option.
        add_settings_field(
            'market_exporter_params',
            __( 'Use selected parameters', $this->plugin_name ),
            array( &$this, 'input_fields_cb' ),
            $this->plugin_name,
            'market_exporter_section_general',
            [
                'label_for'         => 'params',
                'description'       => __( 'Selected attributes will be exported as a parameters. Hold down the control (ctrl) button on Windows or command (cmd) on Mac to select multiple options.', $this->plugin_name ),
                'type'              => 'multiselect'
            ]
        );
	}

    /**
     * Callback function for add_settings_section().
     * $args have the following keys defined: title, id, callback.
     * The values are defined at the add_settings_section() function.
     *
     * @since 0.3.0
     * @param $args
     */
    public function section_general_cb( $args ) {
        ?>
        <p id="<?= esc_attr( $args[ 'id' ] ); ?>">
            <?= esc_html__( 'Settings that are used in the export process.', $this->plugin_name ); ?>
        </p>
        <?php
    }

    /**
     * Callback function for add_settings_field().
     * The values for $args are defined at the add_settings_field() function.
     *
     * @since 0.3.0
     * @param $args
     */
    public function input_fields_cb( $args ) {
        $options = get_option('market_exporter_shop_settings');

        if ( esc_attr( $args[ 'type' ] ) == 'text' || esc_attr( $args[ 'type' ] ) == 'checkbox' ) : ?>

            <input id="<?= esc_attr( $args[ 'label_for' ] ); ?>"
				   type="<?= esc_attr( $args[ 'type' ] ); ?>"
                   name="market_exporter_shop_settings[<?= esc_attr( $args[ 'label_for' ] ); ?>]"
                   value="<?= esc_attr( $options[ $args[ 'label_for' ] ] ); ?>"
                   <?php if ( esc_attr( $args[ 'type' ] ) == 'text' ) :?>placeholder="<?= esc_attr( $args[ 'placeholder' ] ); endif; ?>"
				   <?php if ( esc_attr( $args[ 'type' ] ) == 'checkbox' && $options[ $args[ 'label_for' ] ] == 'yes' ) echo "checked"; ?>>

        <?php elseif ( esc_attr( $args[ 'type' ] ) == 'textarea' ) : ?>

            <textarea cols="39" rows="3" maxlength="50" id="<?= esc_attr( $args[ 'label_for' ] ); ?>"
                      name="market_exporter_shop_settings[<?= esc_attr( $args[ 'label_for' ] ); ?>]"><?= $options[ $args[ 'label_for' ] ]; ?></textarea>

        <?php elseif ( esc_attr( $args[ 'type' ] ) == 'select' ) : ?>

			<select id="<?= esc_attr( $args[ 'label_for' ] ); ?>"
					name="market_exporter_shop_settings[<?= esc_attr( $args[ 'label_for' ] ); ?>]">
				<?php foreach( $args[ 'options' ] as $key => $value ) : ?>
					<option value="<?= $key; ?>" <?php if ( $options[ $args[ 'label_for' ] ] == $key ) echo 'selected'; ?>>
						<?= $value; ?>
					</option>
				<?php endforeach; ?>
			</select>

		<?php
        /*
            NOTE: I'm not sure I want to keep this part. Because below is a much shorter version of the same code,
            although it's harder to read then the code here. I'll leave it here for now, just in case I messed up.

            elseif ( esc_attr( $args[ 'type' ] ) == 'multiselect' ) :

            if ( esc_attr( $args[ 'label_for' ] ) == 'include_cat' ) :
                $select_array = [];
                if ( isset( $options[ $args[ 'label_for' ] ] ) )
                    $select_array = $options[ $args[ 'label_for' ] ];
                ?>
                <select size="10" id="<?= esc_attr( $args[ 'label_for' ] ); ?>"
                        name="market_exporter_shop_settings[<?= esc_attr( $args[ 'label_for' ] ); ?>][]"
                        multiple>
                    <?php foreach ( get_categories( [ 'taxonomy' => 'product_cat', 'parent' => 0 ] ) as $category ) : ?>
                        <option value="<?= $category->cat_ID; ?>"
                            <?php if ( in_array( $category->cat_ID, $select_array ) ) echo "selected"; ?>><?= $category->name; ?></option>
                        <?php foreach ( get_categories( [ 'taxonomy' => 'product_cat', 'parent' => $category->cat_ID ] ) as $subcategory ) : ?>
                            <option value="<?= $subcategory->cat_ID; ?>"
                                <?php if ( in_array( $subcategory->cat_ID, $select_array ) ) echo "selected"; ?>><?= "&mdash;&nbsp;" . $subcategory->name; ?></option>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
            <?php endif;

            if ( esc_attr( $args[ 'label_for' ] ) == 'params' ) :
                $select_array = [];
                if ( isset( $options[ $args[ 'label_for' ] ] ) )
                    $select_array = $options[ $args[ 'label_for' ] ];
                ?>
                <select size="10" id="<?= esc_attr( $args[ 'label_for' ] ); ?>"
                        name="market_exporter_shop_settings[<?= esc_attr( $args[ 'label_for' ] ); ?>][]"
                        multiple>
                    <?php foreach ( wc_get_attribute_taxonomies() as $attribute ) : ?>
                        <option value="<?= $attribute->attribute_id; ?>"
                            <?php if ( in_array( $attribute->attribute_id, $select_array ) ) echo "selected"; ?>><?= $attribute->attribute_label; ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif;

		    endif;
        */

        elseif ( esc_attr( $args[ 'type' ] ) == 'multiselect' ) :
            $select_array = [];
            if ( isset( $options[ $args[ 'label_for' ] ] ) )
                $select_array = $options[ $args[ 'label_for' ] ];

            echo '<select id="' . esc_attr( $args[ 'label_for' ] ) . '" name="market_exporter_shop_settings[' . esc_attr( $args[ 'label_for' ] ) . '][]" multiple>';
                /*
                 * So far multiselect can be for included categories and parameters.
                 * The categories multiselect can include subcategories, that's why we have to go over all of the subcategories and parents.
                 * The parameters multiselect only includes top-level items.
                 */
                if ( esc_attr( $args[ 'label_for' ] ) == 'include_cat' ) {
                    foreach (get_categories(['taxonomy' => 'product_cat', 'parent' => 0]) as $category) {
                        echo '<option value="' . $category->cat_ID . '" ' . (in_array($category->cat_ID, $select_array) ? "selected" : "") . '>' . $category->name . '</option>';
                        foreach (get_categories(['taxonomy' => 'product_cat', 'parent' => $category->cat_ID]) as $subcategory)
                            echo '<option value="' . $subcategory->cat_ID . '" ' . (in_array($subcategory->cat_ID, $select_array) ? "selected" : "") . '>&mdash;&nbsp;' . $subcategory->name . '</option>';
                    }
                }

                if ( esc_attr( $args[ 'label_for' ] ) == 'params' ) {
                    foreach ( wc_get_attribute_taxonomies() as $attribute )
                        echo '<option value="' . $attribute->attribute_id . '" ' . ( in_array( $attribute->attribute_id, $select_array ) ? "selected" : "" ) . '>' . $attribute->attribute_label . '</option>';
                }
            echo '</select>';
        endif; ?>


		<p class="description">
			<?= $args[ 'description' ]; ?>
		</p>

		<?php
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
		$output['company_name']	= sanitize_text_field( $input['company_name'] );

		// According to Yandex up to 10 images per product.
		$images = intval( $input['image_count'] );
		if ( $images > 10 ) {
			$output['image_count'] = 10;
		} else {
			$output['image_count'] = $images;
		}

		$output['vendor']           = sanitize_text_field( $input['vendor'] );
		$output['market_category']  = sanitize_text_field( $input['market_category'] );
		$output['sales_notes']      = sanitize_textarea_field( $input['sales_notes'] );

		$output['backorders']	= ( isset( $input['backorders'] ) ) ? true : false;
		$output['file_date']	= ( isset( $input['file_date'] ) ) ? true : false;
        $output['size']         = ( isset( $input['size'] ) ) ? true : false;

		// Convert to int array.
		$output['include_cat']	= array_map( 'intval', $input['include_cat'] );
        $output['params']       = array_map( 'intval', $input['params'] );

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
		$settings_link = "<a href=" . admin_url( 'admin.php?page=' . $this->plugin_name . '&tab=settings' ) . ">" . __( 'Settings', $this->plugin_name ) . "</a>";
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

	/**
	 * Register crontab.
	 *
	 * @since   0.2.0
	 */
	public function crontab_activate() {
		// Schedule task
		if ( ! wp_next_scheduled( 'market_exporter_daily' ) ) {
			wp_schedule_event( time(), 'five_seconds', 'market_exporter_daily' );
		}
	}

}

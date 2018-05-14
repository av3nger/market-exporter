<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/av3nger/market-exporter/
 * @since      0.0.1
 *
 * @package    Market_Exporter
 * @subpackage Market_Exporter/admin
 */

namespace Market_Exporter\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Market_Exporter
 * @subpackage Market_Exporter/admin
 * @author     Anton Vanyukov <a.vanyukov@testor.ru>
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since  0.0.1
	 * @access private
	 * @var    string  $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.3
	 * @access private
	 * @var    string  $version  The current version of this plugin.
	 */
	private $version;

	/**
	 * Plugin options.
	 *
	 * @since 0.4.4
	 * @since 1.1.0  Changed to static.
	 *
	 * @access private
	 *
	 * @var    array   $options  Current plugin options.
	 */
	private static $options;

	/**
	 * Admin constructor.
	 *
	 * @since 0.0.1
	 * @param string $plugin_name  The name of this plugin.
	 * @param string $version      Plugin version.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		self::$options     = get_option( 'market_exporter_shop_settings' );
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
		wp_enqueue_style( "{$this->plugin_name}-admin", plugin_dir_url( __FILE__ ) . 'css/app.min.css', array(), $this->version, 'all' );
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
		wp_enqueue_script( "{$this->plugin_name}-admin", plugin_dir_url( __FILE__ ) . 'js/app.min.js', array( 'jquery' ), $this->version, false );

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
		/* @noinspection PhpIncludeInspection */
		require_once plugin_dir_path( __FILE__ ) . 'partials/market-exporter-admin-display.php';
		?>
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<div id="wrap-me-component"></div>
		<?php
	}

	/**
	 * Callback function for add_settings_field().
	 * The values for $args are defined at the add_settings_field() function.
	 *
	 * @since 0.3.0
	 * @since 1.1.0  Changed to static.
	 *
	 * @param array $args Arguments array.
	 */
	public static function input_fields_cb( $args ) {
		if ( 'text' === esc_attr( $args['type'] ) || 'checkbox' === esc_attr( $args['type'] ) ) :
			$value = isset( self::$options[ $args['label_for'] ] ) ? self::$options[ $args['label_for'] ] : false;
			?>

			<input id="<?php echo esc_attr( $args['label_for'] ); ?>"
				   type="<?php echo esc_attr( $args['type'] ); ?>"
				   name="market_exporter_shop_settings[<?php echo esc_attr( $args['label_for'] ); ?>]"
				   value="<?php echo esc_attr( $value ); ?>"
					<?php if ( 'text' === esc_attr( $args['type'] ) ) :?>
						placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
					<?php endif; ?>
					<?php echo ( 'checkbox' === esc_attr( $args['type'] ) ) ? checked( $value ) : ''; ?>
			>

		<?php elseif ( 'textarea' === esc_attr( $args['type'] ) ) : ?>

			<textarea cols="39" rows="3" maxlength="50" id="<?php echo esc_attr( $args['label_for'] ); ?>"
					  name="market_exporter_shop_settings[<?php echo esc_attr( $args['label_for'] ); ?>]"
					  title="<?php echo esc_attr( $args['label_for'] ); ?>"><?php echo esc_html( self::$options[ $args['label_for'] ] ); ?></textarea>

		<?php elseif ( 'select' === esc_attr( $args['type'] ) ) : ?>

			<select id="<?php echo esc_attr( $args['label_for'] ); ?>"
					name="market_exporter_shop_settings[<?php echo esc_attr( $args['label_for'] ); ?>]">
				<?php foreach ( $args['options'] as $key => $value ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( self::$options[ $args['label_for'] ] === $key ); ?>>
						<?php echo esc_html( $value ); ?>
					</option>
				<?php endforeach; ?>
			</select>

		<?php
		elseif ( 'multiselect' === esc_attr( $args['type'] ) ) :
			$select_array = array();
			if ( isset( self::$options[ $args['label_for'] ] ) ) {
				$select_array = self::$options[ $args['label_for'] ];
			}

			// Disable params multiselect if export all params checkbox is selected.
			if ( 'params' === esc_attr( $args['label_for'] ) && self::$options['params_all'] ) {
				echo '<select id="' . esc_attr( $args['label_for'] ) . '" name="market_exporter_shop_settings[' . esc_attr( $args['label_for'] ) . '][]" multiple="multiple" disabled>';
			} else {
				echo '<select id="' . esc_attr( $args['label_for'] ) . '" name="market_exporter_shop_settings[' . esc_attr( $args['label_for'] ) . '][]" multiple="multiple">';
			}

			/*
			 * So far multiselect can be for included categories and parameters.
			 * The categories multiselect can include subcategories, that's why we have to go over all of the subcategories and parents.
			 * The parameters multiselect only includes top-level items.
			 */
			if ( 'include_cat' === esc_attr( $args['label_for'] ) ) {
				foreach ( get_terms( array(
					'hide_empty'   => 0,
					'parent'       => 0,
					'taxonomy'     => 'product_cat',
				)) as $category ) {
					echo '<option value="' . esc_attr( $category->term_id ) . '" ' . selected( in_array( $category->term_id, $select_array, true ) ) . '>' . esc_html( $category->name ) . '</option>';
					self::get_cats_from_array( $category->term_id, $select_array );
				}
			}

			if ( 'params' === esc_attr( $args['label_for'] ) ) {
				foreach ( wc_get_attribute_taxonomies() as $attribute ) {
					echo '<option value="' . esc_attr( $attribute->attribute_id ) . '" ' . selected( in_array( absint( $attribute->attribute_id ), $select_array, true ) ) . '>' . esc_html( $attribute->attribute_label ) . '</option>';
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
	 * Recursive function to populate a list with sub categories.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @param   int   $cat_id        Category ID.
	 * @param   array $select_array  Array of selected category IDs.
	 * @used-by Market_Exporter_Admin::input_fields_cb()
	 */
	private static function get_cats_from_array( $cat_id, $select_array ) {
		static $tabs = 0;
		$tabs++;

		$subcategories = get_terms( array(
			'hide_empty'   => 0,
			'parent'       => $cat_id,
			'taxonomy'     => 'product_cat',
		) );

		if ( ! empty( $subcategories ) ) {
			foreach ( $subcategories as $subcategory ) {
				echo '<option value="' . esc_attr( $subcategory->term_id ) . '" ' . selected( in_array( $subcategory->term_id, $select_array, true ), true, false ) . '>' . esc_html( str_repeat( '&mdash;&nbsp;', $tabs ) ) . esc_html( $subcategory->name ) . '</option>';
				self::get_cats_from_array( $subcategory->term_id, $select_array );
				$tabs--;
			}
		}
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
	public static function update_cron_schedule( $interval ) {
		wp_clear_scheduled_hook( 'market_exporter_cron' );
		if ( 'disabled' !== $interval ) {
			wp_schedule_event( time(), $interval, 'market_exporter_cron' );
		}
	}

	/**
	 * Generate file on update.
	 *
	 * @since 1.0.0
	 * @used-by Market_Exporter::define_admin_hooks()
	 */
	public function generate_file_on_update() {
		if ( isset( self::$options['update_on_change'] ) && self::$options['update_on_change'] ) {
			$doing_cron = get_option( 'market_exporter_doing_cron' );
			// Already doing cron, exit.
			if ( isset( $doing_cron ) && $doing_cron ) {
				return;
			}

			update_option( 'market_exporter_doing_cron', true );
			wp_schedule_single_event( time(), 'market_exporter_cron' );
		}
	}

}

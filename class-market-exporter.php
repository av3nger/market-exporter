<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the admin area of the site.
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization and admin-specific hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class Market_Exporter {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 */
	protected $version;

	/**
	 */
	public function __construct() {

		$this->plugin_name = 'market-exporter';
		$this->version = '0.0.2';
		
		// Check if plugin has WooCommerce installed and active.
		add_action( 'admin_init', array( &$this, 'run_plugin' ) );
		// If not passed checks - don't go further.
		if ( !self::check_prerequisites() ) {
			return;
		}
		
		$this->set_locale();
		$this->define_admin_hooks();

	}
	
	/**
	 * Check startup dependencies.
	 *
	 * For vYandex Market plugin to function properly it is required that WooCommerce is installed and activated.
	 */
	public static function activate() {
 	}
	
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 */
	public static function deactivate() {
	}
	
	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {
		load_plugin_textdomain(
			$this->plugin_name,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {			
		add_action( 'admin_menu', array( &$this, 'add_admin_menu' ) );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
	
	/**
	 * Со следующими тремя функциями я сношался ровно одну ночь и один день!
	 * Суть следующая: перед активацией плагина происходит проверка на наличие установленного и активированного плагина WooCommerce.
	 * Если WooCommerce не установлен, или установлен, но не активирован, то мы высвечиваем пользователю сообщение об ошибке.
	 * Возможно, код можно оптимизировать. Но я не готов сейчас вдаваться в это. Пусть поработает так.
	 */
	
  /**
   * Is WooCommerce installed? Is it active? If not - don't activate the plugin.
   */	
	public function run_plugin() {
		if ( !self::check_prerequisites() ) {
			add_action( 'admin_notices', array( $this, 'plugin_activation' ) ) ;
			$plugins = get_option( 'active_plugins' );
			$market_exporter = plugin_basename( MARKET_EXPORTER__PLUGIN_DIR . 'market-exporter.php' );
			if ( in_array( $market_exporter, $plugins ) ) {
				// Suppress "Plugin activated" notice.
		    unset( $_GET['activate'] );
				deactivate_plugins( MARKET_EXPORTER__PLUGIN_DIR . 'market-exporter.php' );	
			}
		}
	}

  /**
   * Message to display if we didn't find WooCommerce.
   */
  public function plugin_activation() {
    ?>
    <div class="error notice">
        <p><?php _e( 'The Market Exporter plugin requires <a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a> to be installed and activated. Please check your configuration.', 'market-exporter' ); ?></p>
    </div>
    <?php
  }

  /**
   * Checks if WooCommerce is installed and active.
   */
	static function check_prerequisites() {
		
		// Check if get_plugins() function exists. Needed for checks during __construct.
		if ( ! function_exists( 'get_plugins' ) )
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Check if WooCommerce is installed.
		$woo_installed = get_plugins('/woocommerce');
		if ( empty($woo_installed) )
			return false;
			
		// Check if WooCommerce is active.
		if ( !is_plugin_active('woocommerce/woocommerce.php') )
			return false;

		return true;
	}

  /**
   * Add sub menu page to the tools main menu.
   */
	function add_admin_menu() {
	    add_management_page(
	        __( 'Market Exporter', 'market-exporter' ),
	        __( 'Market Exporter', 'market-exporter' ),
	        'manage_options',
	        'market-exporter',
	        array ( &$this, 'page_interface' )
	    );
	}
	
  /**
   * Write YML file to /wp-content/uploads/ dir.
   *
   * @param		string			$yml	Variable to display contents of the YML file.
   * @return	string						Return the path of the saved file.
   */
	function write_file( $yml ) {
		$url = wp_nonce_url('tools.php?page=market-exporter','market-exporter');
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
		$folder = trailingslashit($upload_dir['basedir']).'market-exporter/';
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

		return $upload_dir['baseurl'].'/market-exporter/'.$filename;
	}
		
  /**
   * Display plugin page.
   */
	function page_interface() {
		global $wpdb;
		
		?>
		<div class="wrap">
			<h2><?php _e('Market Exporter', 'market-exporter'); ?></h2>

			<?php // If someone clicks the button
			if ( !empty( $_POST['market-exporter'] ) ) {
				if ( !current_user_can('manage_options') )
					wp_die( _e( "Silence is golden", 'market-exporter' ) );
					
				check_admin_referer( 'market-exporter' );
				
				/*
				Although it is advised to use WP_Query instead of $wpdb, it is really a bit overkill for our task.
				
				Some stats I got during testing:
				$wpdb->get_results returns 18 queries in around 5 ms time compared to 22 queries and around 40 ms query time when using WP_Query.
				If disabling cache settings on WP_Query, query count drops to 20 queries in around 7 ms time.
				If using nopaging option, query count goes down even further to 18 queries. query time is about the same as using $wpdb->get_results.

				Example of WP_Query usage:
					
				$query = new WP_Query( array(
											'post_type' => 'product',
											'post_status' => 'publish',
											'nopaging' => true,
											'cache_results' => false,
											'update_post_meta_cache' => false,
											'update_post_term_cache' => false ) );
					
				if ( !$query->have_posts() ) {
					echo '	<p>' . sprintf( __( "Unable to find any products. Are you sure <a href='%s'>some exist</a>?", 'vyandex-market' ), admin_url( 'post-new.php?post_type=product' ) ) . "</p></div>";
					return;
				}
				*/

				// Check currency.
				$ya_currency = $wpdb->get_var(
											"SELECT option_value
											 FROM $wpdb->options
											 WHERE option_name = 'woocommerce_currency'" );
				if ( $ya_currency != 'RUB' ) {
					echo '	<p>' . sprintf( __( "Currently only Russian Ruble (RUB) currency is supported. Please <a href='%s'>update currency</a>.", 'market-exporter' ), admin_url( 'admin.php?page=wc-settings' ) ) . "</p>";
					return;
				}
				
				// Get categories.
				$ya_categories = $wpdb->get_results(
											"SELECT c.term_id AS id, c.name, p.parent AS parent
											 FROM $wpdb->terms c
											 LEFT JOIN $wpdb->term_taxonomy p ON c.term_id = p.term_id
											 WHERE p.taxonomy = 'product_cat'" );
											 
				// Get local delivery cost.
				$ya_local_delivery = maybe_unserialize( $wpdb->get_var(
											"SELECT option_value
											 FROM $wpdb->options
											 WHERE option_name = 'woocommerce_local_delivery_settings'" ) );
				
				// Get products.
				// TODO: let users choose which field is description - post_content or post_excerpt.
				// TODO: _stock_status from postmeta table.
				// TODO: Maybe is's better to do a LEFT JOIN with visibility instead of included SELECT?
				if ( !$ya_offers = $wpdb->get_results(
										 "SELECT p.ID, p.post_title AS name, p.post_excerpt AS description, m.meta_value AS vendorCode
											FROM $wpdb->posts p
											LEFT JOIN $wpdb->postmeta m ON m.post_id = p.ID AND m.meta_key = '_sku'
											WHERE p.post_type = 'product'
												AND p.post_status = 'publish'
												AND p.post_password = ''
												AND ( SELECT m.meta_value FROM wp_postmeta m WHERE m.post_id = p.ID AND m.meta_key = '_visibility' ) != 'hidden'
											ORDER BY p.ID DESC" ) )
				{
					echo '	<p>' . sprintf( __( "Unable to find any products. Are you sure <a href='%s'>some exist</a>?", 'market-exporter' ), admin_url( 'post-new.php?post_type=product' ) ) . "</p>";
					return;
				}

				/* Debugging:
				echo "<pre>";
				print_r($ya_offers);
				echo "</pre>";
				*/

				$yml = '<?xml version="1.0" encoding="'.get_bloginfo( "charset" ).'"?>'.PHP_EOL;
				$yml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'.PHP_EOL;
				$yml .= '<yml_catalog date="'.Date("Y-m-d H:i").'">'.PHP_EOL;
				$yml .= '  <shop>'.PHP_EOL;
				$yml .= '    <name>'.get_bloginfo( "name" ).'</name>'.PHP_EOL;
				$yml .= '    <company>'.get_bloginfo( "name" ).'</company>'.PHP_EOL;
				$yml .= '    <url>'.get_site_url().'</url>'.PHP_EOL;
				$yml .= '    <currencies>'.PHP_EOL;
				$yml .= '      <currency id="RUR" rate="1" plus="0"/>'.PHP_EOL;
				$yml .= '    </currencies>'.PHP_EOL;
				$yml .= '    <categories>'.PHP_EOL;
				foreach ( $ya_categories as $category ):
					if ($category->parent == 0) {
						$yml .= '      <category id="'.$category->id.'">'.$category->name.'</category>'.PHP_EOL;
					} else {
						$yml .= '      <category id="'.$category->id.'" parentId="'.$category->parent.'">'.$category->name.'</category>'.PHP_EOL;
					}
				endforeach;
				$yml .= '    </categories>'.PHP_EOL;				
				$yml .= '    <local_delivery_cost>'.$ya_local_delivery["fee"].'</local_delivery_cost>'.PHP_EOL;
				$yml .= '    <offers>'.PHP_EOL;
				/*
				while ( $query->have_posts() ) : $query->the_post();
					//get_the_title();
					$yml .= '      <offer id="'.get_the_id().'" type="vendor.model" available="true">'.PHP_EOL;
					$yml .= '        <url>'.get_permalink().'</url>'.PHP_EOL;
					$yml .= '      </offer>'.PHP_EOL;
				endwhile;
				*/
				foreach ( $ya_offers as $offer ):
					$price = $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_price' AND post_id = '$offer->ID'");
					$image = $wpdb->get_var("SELECT guid FROM $wpdb->posts WHERE post_parent = '$offer->ID' AND post_mime_type = 'image/png' LIMIT 0,1");
					$categoryId = get_the_terms( $offer->ID, 'product_cat' );
					
					$yml .= '      <offer id="'.$offer->ID.'" available="true">'.PHP_EOL;
					$yml .= '        <url>'.get_permalink($offer->ID).'</url>'.PHP_EOL;
					$yml .= '        <price>'.$price.'</price>'.PHP_EOL;
					$yml .= '        <currencyId>RUR</currencyId>'.PHP_EOL;
					$yml .= '        <categoryId>'.$categoryId[0]->term_id.'</categoryId>'.PHP_EOL;
					$yml .= '        <picture>'.$image.'</picture>'.PHP_EOL;
					$yml .= '        <delivery>true</delivery>'.PHP_EOL;
					$yml .= '        <local_delivery_cost>'.$ya_local_delivery["fee"].'</local_delivery_cost>'.PHP_EOL;
					$yml .= '        <name>'.$offer->name.'</name>'.PHP_EOL;
					$yml .= '        <description>'.$offer->description.'</description>'.PHP_EOL;
					$yml .= '        <vendorCode>'.$offer->vendorCode.'</vendorCode>'.PHP_EOL;
					$yml .= '      </offer>'.PHP_EOL;
				endforeach;
				$yml .= '    </offers>'.PHP_EOL;
				$yml .= '  </shop>'.PHP_EOL;
				$yml .= '</yml_catalog>'.PHP_EOL;
	
				// Reset Query.
				wp_reset_query();
				// Clear the SQL result cache.
				$wpdb->flush();

				/* Debugging: */
				echo "<pre>";
				echo strtr($yml,Array("<"=>"&lt;","&"=>"&amp;"));
				echo "</pre>";
				//*/
				
				$file_path = self::write_file( $yml );
				echo '	<p>' . sprintf( __( "File exported successfully: <a href=\"%s\">%s</a>.", 'market-exporter' ), $file_path, $file_path ) . "</p>";
				
			// Display the form by default.
			} else {
			?>
				<form method="post" action="">
				<?php wp_nonce_field('market-exporter') ?>
				<p><?php _e( "This plugin is used to generate a valid YML file for exporting your products in WooCommerce to Yandex Market.", 'market-exporter' ); ?></p>	
								
				<p><?php _e( "Please be patient while the YML file is generated. This can take a while if your server is slow (inexpensive hosting) or if you have many products in WooCommerce. Do not navigate away from this page until this script is done or the YML file will not be created. You will be notified via this page when the process is completed.", 'market-exporter' ); ?></p>	

				<p><?php _e( 'To begin, just press the button below.', 'market-exporter'); ?></p>
				
				<p><input type="submit" class="button hide-if-no-js" name="market-exporter" id="market-exporter" value="<?php _e( 'Generate YML file', 'market-exporter' ) ?>" /></p>
				
				<noscript><p><em><?php _e( 'You must enable Javascript in order to proceed!', 'market-exporter' ) ?></em></p></noscript>
				
				</form>
			<?php
			}
			?>
			
		</div>
		<?php

	}

}

<?php
/**
 * Class Market_Exporter_YML
 *
 * A class that utilizes YML specific functionality of the plugin.
 */

class Market_Exporter_YML {

	private $plugin_name;

	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
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
	private function get_currecny() {
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
	private function get_categories() {
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
	private function get_delivery() {
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
	private function get_products( $backorders ) {
		global $wpdb;

		return $wpdb->get_results(
			"SELECT p.ID, p.post_title AS name, p.post_content AS description, m1.meta_value AS vendorCode, p.post_excerpt AS sales_notes, m3.meta_value AS stock, m0.meta_value AS options
									FROM $wpdb->posts p
									INNER JOIN $wpdb->postmeta m0 ON p.ID = m0.post_id AND m0.meta_key = '_product_attributes'
									INNER JOIN $wpdb->postmeta m1 ON p.ID = m1.post_id AND m1.meta_key = '_sku'
									INNER JOIN $wpdb->postmeta m2 ON p.ID = m2.post_id AND m2.meta_key = '_visibility'
									INNER JOIN $wpdb->postmeta m3 ON p.ID = m3.post_id AND m3.meta_key = '_stock_status'
									WHERE p.post_type = 'product'
											AND p.post_status = 'publish'
											AND p.post_password = ''
											AND m2.meta_value != 'hidden'
											" . ( $backorders == 'no' ? "AND m3.meta_value = 'instock'" : "" ) . "
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
	private function get_var_products( $prodID ) {
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
	 * @return  array           Return the attribute value for the link.
	 */
	private function get_var_link( $prodID, $attr ) {
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
	private function get_price( $id ) {
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
	private function get_images( $id, $count ) {
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
	 * Generate YML file.
	 *
	 * This is used for generating YML with CRON.
	 *
	 * @since     0.2.0
	 * @return    int         Return code
	 *                        100 - invalid currency
	 *                        300 - no products
	 *                        $file_path - everything is ok
	 */
	public function generate_YML() {
		// Check currency.
		if ( !$currency = $this->get_currecny() )
			return 100;

		// Get plugin settings.
		$shop_settings = get_option( 'market_exporter_shop_settings' );

		// Get products.
		if ( !$ya_offers = $this->get_products( $shop_settings['backorders'] ) )
			return 300;

		if ( ! isset( $shop_settings['file_date'] ) )
			$shop_settings['file_date'] = 'yes';

		if ( ! isset( $shop_settings['image_count'] ) )
			$shop_settings['image_count'] = 10;

		$yml = '<?xml version="1.0" encoding="'.get_bloginfo( "charset" ).'"?>'.PHP_EOL;
		$yml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'.PHP_EOL;
		$yml .= '<yml_catalog date="'.date("Y-m-d H:i").'">'.PHP_EOL;
		$yml .= '  <shop>'.PHP_EOL;
		$yml .= '    <name>'.esc_html( $shop_settings['website_name'] ).'</name>'.PHP_EOL;
		$yml .= '    <company>'.esc_html( $shop_settings['company_name'] ).'</company>'.PHP_EOL;
		$yml .= '    <url>'.get_site_url().'</url>'.PHP_EOL;
		$yml .= '    <currencies>'.PHP_EOL;
		if ( $currency == 'USD' || $currency == 'EUR' ) {
			$yml .= '      <currency id="RUR" rate="1"/>'.PHP_EOL;
			$yml .= '      <currency id="'.$currency.'" rate="СВ"/>'.PHP_EOL;
		} else {
			$yml .= '      <currency id="'.$currency.'" rate="1"/>'.PHP_EOL;
		}
		$yml .= '    </currencies>'.PHP_EOL;
		$yml .= '    <categories>'.PHP_EOL;
		foreach ( $this->get_categories() as $category ):
			if ($category->parent == 0) {
				$yml .= '      <category id="'.$category->id.'">'.wp_strip_all_tags( $category->name ).'</category>'.PHP_EOL;
			} else {
				$yml .= '      <category id="'.$category->id.'" parentId="'.$category->parent.'">'.wp_strip_all_tags( $category->name).'</category>'.PHP_EOL;
			}
		endforeach;
		$yml .= '    </categories>'.PHP_EOL;
		$yml .= '    <local_delivery_cost>'.$this->get_delivery().'</local_delivery_cost>'.PHP_EOL;
		$yml .= '    <offers>'.PHP_EOL;
		foreach ( $ya_offers as $offer ):
			/*
				So what we do here is basically assume the product is a simple product and has no variations.
				We then check for variations. And if the product is indeed a variable product, we will list all variations as simple products.
			*/
			$has_variations  = false;
			$variation_count = 1;

			// Check if product has variations.
			$unser = array_values( unserialize( $offer->options ) );
			if ( $unser != null ) {
				foreach ( $unser as $uns ) {
					if ( $uns['is_variation'] == 1 ) {
						$has_variations  = true;
						$variations      = $this->get_var_products( $offer->ID );
						$variation_count = count( $variations );
					}
				}
			}

			while ( $variation_count > 0 ):
				$variation_count--;
				$var_link = '';
				$offerID = $has_variations ? $variations[$variation_count]->ID : $offer->ID;
				$offerSKU = $offer->vendorCode;

				// Probably there is a better way to get this value, but...
				// We are getting the last bit for the link: for example ?attribute_pa_color=black
				if ($has_variations) {
					foreach ( $unser as $uns ) {
						if ( $uns['is_variation'] == 1 ) {
							$link = $this->get_var_link( $offerID, $uns['name'] );
							$var_link = '?attribute_' . $uns['name'] . '=' . $link->{'meta_value'};
						}
					}

					if ( $variations[$variation_count]->vendorCode )
						$offerSKU = $variations[$variation_count]->vendorCode;
				}

				$images = $this->get_images( $offerID, $shop_settings['image_count'] );
				$categoryId = get_the_terms( $offer->ID, 'product_cat' );
				$yml .= '      <offer id="'.$offerID.'" available="'.( $offer->stock != "outofstock" ? "true" : "false" ).'">'.PHP_EOL;
				$yml .= '        <url>'.get_permalink($offer->ID).$var_link.'</url>'.PHP_EOL;
				// Price.
				$price = $this->get_price( $offerID );
				if ( $price['sale_price'] && ( $price['sale_price'] < $price['price'] ) ) {
					$yml .= '        <price>'.$price['sale_price'].'</price>'.PHP_EOL;
					$yml .= '        <oldprice>'.$price['price'].'</oldprice>'.PHP_EOL;
				} else {
					$yml .= '        <price>'.$price['price'].'</price>'.PHP_EOL;
				}
				$yml .= '        <currencyId>'.$currency.'</currencyId>'.PHP_EOL;
				$yml .= '        <categoryId>'.$categoryId[0]->term_id.'</categoryId>'.PHP_EOL;
				// Market category.
				if ( isset( $shop_settings['market_category'] ) && $shop_settings['market_category'] != 'not_set' ) {
					$market_category = wc_get_product_terms( $offer->ID, 'pa_'.$shop_settings['market_category'], array( 'fields' => 'names' ) );
					if ( $market_category )
						$yml .= '        <market_category>'.wp_strip_all_tags( array_shift( $market_category ) ).'</market_category>'.PHP_EOL;
				}
				foreach ( $images as $image ):
					if ( strlen( utf8_decode( $image ) ) <= 512 )
						$yml .= '        <picture>'.$image.'</picture>'.PHP_EOL;
				endforeach;
				$yml .= '        <delivery>true</delivery>'.PHP_EOL;
				$yml .= '        <name>'.wp_strip_all_tags( $offer->name ).'</name>'.PHP_EOL;
				// Vendor.
				if ( isset( $shop_settings['vendor'] ) && $shop_settings['vendor'] != 'not_set' ) {
					$vendor = wc_get_product_terms( $offer->ID, 'pa_'.$shop_settings['vendor'], array( 'fields' => 'names' ) );
					if ( $vendor )
						$yml .= '        <vendor>'.wp_strip_all_tags( array_shift( $vendor ) ).'</vendor>'.PHP_EOL;
				}
				// Vendor code.
				if ( $offer->vendorCode )
					$yml .= '        <vendorCode>'.wp_strip_all_tags( $offerSKU ).'</vendorCode>'.PHP_EOL;
				// Description.
				if ( $offer->description )
					//$yml .= '        <description>'.htmlspecialchars( html_entity_decode( wp_strip_all_tags( $offer->description ), ENT_COMPAT, "UTF-8" ) ).'</description>'.PHP_EOL;
					$yml .= ' <description><![CDATA['.html_entity_decode( $offer->description, ENT_COMPAT, "UTF-8" ).']]></description>'.PHP_EOL;
				// Sales notes.
				if ( ( $shop_settings['sales_notes'] == 'yes' ) && ( $offer->sales_notes ) )
					$yml .= '        <sales_notes>'.wp_strip_all_tags( $offer->sales_notes ).'</sales_notes>'.PHP_EOL;
				$yml .= '      </offer>'.PHP_EOL;
			endwhile;
		endforeach;
		$yml .= '    </offers>'.PHP_EOL;
		$yml .= '  </shop>'.PHP_EOL;
		$yml .= '</yml_catalog>'.PHP_EOL;

		// Reset Query.
		wp_reset_query();
		// Clear the SQL result cache.
		//$wpdb->flush();

		$market_exporter_fs = new Market_Exporter_FS( $this->plugin_name );
		$file_path = $market_exporter_fs->write_file( $yml, $shop_settings['file_date'] );
		return $file_path;
	}

}
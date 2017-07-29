<?php
/**
 * Market Exporter: ME_WC class
 *
 * A class that utilizes WooCommerce builtin functions to generate the YML instead of querying the database.
 *
 * @package Market_Exporter
 * @since   0.3.0
 */
class ME_WC {

	/**
	 * Settings variable
	 *
	 * @access private
	 * @var mixed|void
	 */
	private $settings;

	/**
	 * Weight units
	 *
	 * @access private
	 * @var array
	 */
	private $weight_units;

	/**
	 * Size units
	 *
	 * @access private
	 * @var array
	 */
	private $size_units;

	/**
	 * Constructor method.
	 *
	 * @since 0.3.0
	 */
	public function __construct() {
		// Get plugin settings.
		$this->settings = get_option( 'market_exporter_shop_settings' );

		if ( ! isset( $this->settings['image_count'] ) ) {
			$this->settings['image_count'] = 10;
		}

		// Available units for weight (mg, g, kg).
		$this->weight_units = array(
			'mg',
			'g',
			'kg',
		);
		// Available units for size (mm, cm, m).
		$this->size_units = array(
			'mm',
			'cm',
			'm',
		);
	}

	/**
	 * Generate YML file.
	 *
	 * Available error codes:
	 *      false - everything is ok
	 *      100   - wrong currency
	 *      200   - no shipping method available
	 *      300   - no available products
	 *
	 * @since  0.3.0
	 * @return int|string
	 */
	public function generate_YML() {
		// Check currency.
		if ( ! $currency = $this->check_currency() ) {
			return 100;
		}

		// Get products.
		if ( ! $query = $this->check_products() ) {
			return 300;
		}

		// Generate XML data.
		$yml  = '';
		$yml .= $this->yml_header( $currency );
		$yml .= $this->yml_offers( $currency, $query );
		$yml .= $this->yml_footer();

		// Create file.
		$market_exporter_fs = new Market_Exporter_FS( 'market-exporter' );
		$file_path = $market_exporter_fs->write_file( $yml, $this->settings['file_date'] );
		return $file_path;
	}

	/**
	 * Check currency.
	 *
	 * Checks if the selected currency in WooCommerce is supported by Yandex Market.
	 * As of today it is allowed to list products in six currencies: RUB, UAH, BYR, KZT, USD and EUR.
	 * But! WooCommerce doesn't support BYR and KZT. And USD and EUR can be used only to export products.
	 * They will still be listed in RUB or UAH.
	 *
	 * @since  0.3.0
	 * @return string Returns currency if it is supported, else false.
	 */
	private function check_currency() {

		$currency = get_woocommerce_currency();

		switch ( $currency ) {
			case 'RUB':
				return 'RUR';
			case 'BYR':
				return 'BYN';
			case 'UAH':
			case 'BYN':
			case 'USD':
			case 'EUR':
				return $currency;
			default:
				return false;
		}
	}

	/**
	 * Check if any products ara available for export.
	 *
	 * @since  0.3.0
	 * @return bool|WP_Query Return products.
	 */
	private function check_products() {

		$args = array(
			'posts_per_page' => -1,
			'post_type'     => array( 'product' ),
			'post_status'   => 'publish',
			'meta_query'    => array(
				array(
					'key'   => '_price',
					'value' => 0,
					'compare' => '>',
					'type'  => 'NUMERIC',
				),
				// TODO: по умолчанию выгружать товары у которых “Статус остатка – В наличии.” и остаток при этом больше 0.
				// Таким образом товары доступные в WooCommerce для предзаказа выгружаться по умолчанию не будут.
				// А включением настройки "Экспорт товаров со статусом предзаказ" давать возможность отображать товары
				// со “Статус остатка – В наличии.” и остатком 0 и меньше нуля. Тогда в ЯМ будут выгружаться и товары
				// в предзаказе WooCommerce.
				array(
					'key'   => '_stock_status',
					'value' => 'instock',
				),
			),
			'orderby'   => 'ID',
			'order'     => 'DESC',
		);

		// If in options some specific categories are defined for export only.
		if ( isset( $this->settings['include_cat'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy'  => 'product_cat',
					'field'     => 'term_id',
					'terms'     => $this->settings['include_cat'],
				),
			);
		}

		$query = new WP_Query( $args );

		if ( 0 !== $query->found_posts ) {
			return $query;
		}

		return false;
	}

	/**
	 * Replace characters that are not allowed in the YML file.
	 *
	 * @since  0.3.0
	 * @param  string $string String to clean.
	 * @return mixed
	 */
	private function clean( $string ) {
		$string = str_replace( '"', '&quot;', $string );
		$string = str_replace( '&', '&amp;', $string );
		$string = str_replace( '>', '&gt;', $string );
		$string = str_replace( '<', '&lt;', $string );
		$string = str_replace( '\'', '&apos;', $string );
		return $string;
	}

	/**
	 * Generate YML header.
	 *
	 * @since  0.3.0
	 * @param  string $currency Currency abbreviation.
	 *
	 * @return string
	 */
	private function yml_header( $currency ) {
		$yml  = '<?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . '"?>' . PHP_EOL;
		$yml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">' . PHP_EOL;
		$yml .= '<yml_catalog date="' . current_time( 'Y-m-d H:i' ) . '">' . PHP_EOL;
		$yml .= '  <shop>' . PHP_EOL;
		$yml .= '    <name>' . esc_html( $this->settings['website_name'] ) . '</name>' . PHP_EOL;
		$yml .= '    <company>' . esc_html( $this->settings['company_name'] ) . '</company>' . PHP_EOL;
		$yml .= '    <url>' . get_site_url() . '</url>' . PHP_EOL;

		$yml .= '    <currencies>' . PHP_EOL;
		if ( ( 'USD' === $currency ) || ( 'EUR' === $currency ) ) {
			$yml .= '      <currency id="RUR" rate="1"/>' . PHP_EOL;
			$yml .= '      <currency id="' . $currency . '" rate="СВ" />' . PHP_EOL;
		} else {
			$yml .= '      <currency id="' . $currency . '" rate="1" />' . PHP_EOL;
		}

		$yml .= '    </currencies>' . PHP_EOL;

		$yml .= '    <categories>' . PHP_EOL;

		$args = array(
			'taxonomy' => 'product_cat',
			'orderby'  => 'term_id',
		);
		// Maybe we need to include only selected categories?
		if ( isset( $this->settings['include_cat'] ) ) {
			$args['include'] = $this->settings['include_cat'];
		}
		foreach ( get_categories( $args ) as $category ) :
			if ( 0 === $category->parent ) {
				$yml .= '      <category id="' . $category->cat_ID . '">' . wp_strip_all_tags( $category->name ) . '</category>' . PHP_EOL;
			} else {
				$yml .= '      <category id="' . $category->cat_ID . '" parentId="' . $category->parent . '">' . wp_strip_all_tags( $category->name ) . '</category>' . PHP_EOL;
			}
		endforeach;
		$yml .= '    </categories>' . PHP_EOL;
		$yml .= '    <offers>' . PHP_EOL;

		return $yml;
	}

	/**
	 * Generate YML body with offers.
	 *
	 * @since  0.3.0
	 * @param  string   $currency Currency abbreviation.
	 * @param  WP_Query $query    Query.
	 * @return string
	 */
	private function yml_offers( $currency, WP_Query $query ) {

		$yml = '';

		while ( $query->have_posts() ) :

			$query->the_post();

			$product = wc_get_product( $query->post->ID );
			// We use a separate variable for offer because we will be rewriting it for variable products.
			$offer = $product;

			/*
			 * By default we set $variation_count to 1.
			 * That means that there is at least one product available.
			 * Variation products will have more than 1 count.
			 */
			$variations = [];
			$variation_count = 1;
			if ( $product->is_type( 'variable' ) ) :
				$variations = $product->get_available_variations();
				$variation_count = count( $variations );
			endif;

			while ( $variation_count > 0 ) :
				$variation_count--;

				// If variable product, get product id from $variations array.
				$offer_id = ( ( $product->is_type( 'variable' ) ) ? $variations[ $variation_count ]['variation_id'] : $product->get_id() );

				// Prepare variation link.
				$var_link = '';
				if ( $product->is_type( 'variable' ) ) :
					$variable_attribute = wc_get_product_variation_attributes( $offer_id );
					$var_link = '?' . key( $variable_attribute ) . '=' . current( $variable_attribute );

					// This has to work but we need to think of a way to save the initial offer variable.
					$offer = new WC_Product_Variation( $offer_id );
				endif;

				// NOTE: Below this point we start using $offer instead of $product.
				$yml .= '      <offer id="' . $offer_id . '" available="' . ( ( $offer->is_in_stock() ) ? 'true' : 'false' ) . '">' . PHP_EOL;
				$yml .= '        <url>' . esc_url( get_permalink( $offer->get_id() ) . $var_link ) . '</url>' . PHP_EOL;

				// Price.
				if ( $offer->get_sale_price() && ( $offer->get_sale_price() < $offer->get_regular_price() ) ) {
					$yml .= '        <price>' . $offer->get_sale_price() . '</price>' . PHP_EOL;
					$yml .= '        <oldprice>' . $offer->get_regular_price() . '</oldprice>' . PHP_EOL;
				} else {
					$yml .= '        <price>' . $offer->get_regular_price() . '</price>' . PHP_EOL;
				}

				$yml .= '        <currencyId>' . $currency . '</currencyId>' . PHP_EOL;

				// Category.
				// Not using $offer_id, because variable products inherit category from parent.
				$categories = get_the_terms( $product->get_id(), 'product_cat' );
				// TODO: display error message if category is not set for product.
				if ( $categories ) {
					$category = array_shift( $categories );
					$yml .= '        <categoryId>' . $category->term_id . '</categoryId>' . PHP_EOL;
				}

				// TODO: get all the images.
				$image = get_the_post_thumbnail_url( $offer->get_id(), 'full' );
				// If no image found for product, it's probably a variation without an image, get the image from parent.
				if ( ! $image ) {
					$image = get_the_post_thumbnail_url( $product->get_id(), 'full' );
				}
				if ( strlen( utf8_decode( $image ) ) <= 512 ) {
					$yml .= '        <picture>' . esc_url( $image ) . '</picture>' . PHP_EOL;
				}

				$yml .= '        <name>' . $this->clean( $offer->get_title() ) . '</name>' . PHP_EOL;

				// type_prefix.
				if ( isset( $this->settings['type_prefix'] ) && 'not_set' !== $this->settings['type_prefix'] ) {
					$type_prefix = $offer->get_attribute( 'pa_' . $this->settings['type_prefix'] );
					if ( $type_prefix ) {
						$yml .= '        <typePrefix>' . wp_strip_all_tags( $type_prefix ) . '</typePrefix>' . PHP_EOL;
					}
				}

				// Vendor.
				if ( isset( $this->settings['vendor'] ) && 'not_set' !== $this->settings['vendor'] ) {
					$vendor = $offer->get_attribute( 'pa_' . $this->settings['vendor'] );
					if ( $vendor ) {
						$yml .= '        <vendor>' . wp_strip_all_tags( $vendor ) . '</vendor>' . PHP_EOL;
					}
				}

				// Model.
				if ( isset( $this->settings['model'] ) && 'not_set' !== $this->settings['model'] ) {
					$model = $offer->get_attribute( 'pa_' . $this->settings['model'] );
					if ( $model ) {
						$yml .= '        <model>' . wp_strip_all_tags( $model ) . '</model>' . PHP_EOL;
					}
				}

				// Vendor code.
				if ( $offer->get_sku() ) {
					$yml .= '        <vendorCode>' . $offer->get_sku() . '</vendorCode>' . PHP_EOL;
				}

				// Description.
				if ( self::woo_latest_versions() ) {
					$description = $offer->get_description();
					if ( empty( $description ) ) {
						$description = $product->get_description();
					}
				} else {
					if ( $product->is_type( 'variable' ) && ! $offer->get_variation_description() ) {
						$description = $offer->get_variation_description();
					} else {
						$description = $offer->post->post_content;
					}
				}

				if ( $description ) {
					$yml .= '        <description><![CDATA[' . html_entity_decode( $description, ENT_COMPAT, 'UTF-8' ) . ']]></description>' . PHP_EOL;
				}
				// Sales notes.
				if ( strlen( $this->settings['sales_notes'] ) > 0 ) {
					$yml .= '        <sales_notes>' . wp_strip_all_tags( $this->settings['sales_notes'] ) . '</sales_notes>' . PHP_EOL;
				}

				// Manufacturer warranty.
				if ( isset( $this->settings['warranty'] ) && 'not_set' !== $this->settings['warranty'] ) {
					$warranty = $offer->get_attribute( 'pa_' . $this->settings['warranty'] );
					if ( $warranty ) {
						$yml .= '        <manufacturer_warranty>' . wp_strip_all_tags( $warranty ) . '</manufacturer_warranty>' . PHP_EOL;
					}
				}

				// Coutry of origin.
				if ( isset( $this->settings['origin'] ) && 'not_set' !== $this->settings['origin'] ) {
					$origin = $offer->get_attribute( 'pa_' . $this->settings['origin'] );
					if ( $origin ) {
						$yml .= '        <country_of_origin>' . wp_strip_all_tags( $origin ) . '</country_of_origin>' . PHP_EOL;
					}
				}

				// Params: size and weight.
				if ( isset( $this->settings['size'] ) && $this->settings['size'] ) :
					if ( $offer->has_weight() ) {
						$weight_unit = esc_attr( get_option( 'woocommerce_weight_unit' ) );
						if ( in_array( $weight_unit, $this->weight_units, true ) ) {
							$yml .= '        <param name="' . __( 'Weight', 'woocommerce' ) . '" unit="' . __( $weight_unit, 'woocommerce' ) . '">' . $offer->get_weight() . '</param>' . PHP_EOL;
						}
					}

					if ( $offer->has_dimensions() ) {
						$size_unit = esc_attr( get_option( 'woocommerce_dimension_unit' ) );
						if ( in_array( $size_unit, $this->size_units ) ) {
							$yml .= '        <param name="' . __( 'Length', 'woocommerce' ) . '" unit="' . __( $size_unit, 'woocommerce' ) . '">' . $offer->get_length() . '</param>' . PHP_EOL;
							$yml .= '        <param name="' . __( 'Width', 'woocommerce' ) . '" unit="' . __( $size_unit, 'woocommerce' ) . '">' . $offer->get_width() . '</param>' . PHP_EOL;
							$yml .= '        <param name="' . __( 'Height', 'woocommerce' ) . '" unit="' . __( $size_unit, 'woocommerce' ) . '">' . $offer->get_height() . '</param>' . PHP_EOL;
						}
					}
				endif;

				// Params: selected parameters.
				if ( isset( $this->settings['params'] ) ) {
					$attributes = $product->get_attributes();
					foreach ( $this->settings['params'] as $param_id ) :
						if ( array_key_exists( wc_attribute_taxonomy_name_by_id( $param_id ), $attributes ) ) {
							$yml .= '        <param name="' . wc_attribute_label( wc_attribute_taxonomy_name_by_id( $param_id ) ) . '">' . $product->get_attribute( wc_attribute_taxonomy_name_by_id( $param_id ) ) . '</param>' . PHP_EOL;
						}
					endforeach;
				}
				$yml .= '      </offer>' . PHP_EOL;
			endwhile;

		endwhile;

		return $yml;
	}

	/**
	 * Generate YML footer.
	 *
	 * @since  0.3.0
	 * @return string
	 */
	private function yml_footer() {

		$yml  = '    </offers>' . PHP_EOL;
		$yml .= '  </shop>' . PHP_EOL;
		$yml .= '</yml_catalog>' . PHP_EOL;

		return $yml;
	}

	/**
	 * Check WooCommerce version.
	 *
	 * Used to check what code to use. Older version of WooCommerce (prior to 3.0.0) use some older functions
	 * that are deprecated in newer versions.
	 *
	 * @since  0.4.1
	 * @param  string $version WooCommerce version.
	 * @return bool
	 */
	private static function woo_latest_versions( $version = '3.0.0' ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$woo_installed = get_plugins( '/woocommerce' );
		$woo_version = $woo_installed['woocommerce.php']['Version'];

		if ( version_compare( $woo_version, $version, '>=' ) ) {
			return true;
		}

		return false;
	}

}

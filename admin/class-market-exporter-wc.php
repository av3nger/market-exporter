<?php
/**
 * Class ME_WC
 *
 * A class that utilizes WooCommerce builtin functions to generate the YML instead of querying the database.
 *
 * @since     0.3.0
 */

class ME_WC {

    private $settings;
    private $weight_units;
    private $size_units;

	/**
	 * Constructor method.
	 *
	 * @since     0.3.0
	 */
	public function __construct() {
		// Get plugin settings.
		$this->settings = get_option( 'market_exporter_shop_settings' );

		// Init default values if not set in config.
		if ( ! isset( $this->settings['file_date'] ) )
			$this->settings['file_date'] = 'yes';

		if ( ! isset( $this->settings['image_count'] ) )
			$this->settings['image_count'] = 10;

        if ( ! isset( $this->settings['size'] ) )
            $this->settings["size"] = false;

        // Available units for weight (mg, g, kg)
        $this->weight_units = ['mg', 'g', 'kg'];
        // Available units for size (mm, cm, m)
        $this->size_units = ['mm', 'cm', 'm'];
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
	 * @since     0.3.0
	 * @return    int|string
	 */
	public function generate_YML() {
		// Check currency.
		if ( ! $currency = $this->check_currecny() )
			return 100;

		// Get products.
		if ( ! $query = $this->check_products() )
			return 300;

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
	 * @since     0.3.0
	 * @return    string      Returns currency if it is supported, else false.
	 */
	private function check_currecny() {

		$currency = get_woocommerce_currency();

		switch ( $currency ) {
			case 'RUB':
				return 'RUR';
            case 'BYR':
                return 'BYN';
			case 'UAH':
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
	 * @since     0.3.0
	 * @return    bool|WP_Query     Return products.
	 */
	private function check_products() {

		$args = array(
			'posts_per_page' => -1,
			//'post_type' => array('product', 'product_variation'),
			'post_type'     => array('product'),
			'post_status'   => 'publish',
			'meta_query'    => array(
				array(
					'key'   => '_price',
					'value' => 0,
					'compare' => '>',
					'type'  => 'NUMERIC'
				),
                // TODO: по умолчанию выгружать товары у которых “Статус остатка – В наличии.” и остаток при этом больше 0.
                // Таким образом товары доступные в WooCommerce для предзаказа выгружаться по умолчанию не будут.
                // А включением настройки "Экспорт товаров со статусом предзаказ" давать возможность отображать товары
                // со “Статус остатка – В наличии.” и остатком 0 и меньше нуля. Тогда в ЯМ будут выгружаться и товары
                // в предзаказе WooCommerce.
                array(
					'key'   => '_stock_status',
					'value' => 'instock'
				)
			),
			'orderby'   => 'ID',
			'order'     => 'DESC'
		);

        // If in options some specific categories are defined for export only.
        if ( isset( $this->settings[ 'include_cat' ] ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy'  => 'product_cat',
                    'field'     => 'term_id',
                    'terms'     => $this->settings[ 'include_cat' ]
                ]
            ];
        }

		$query = new WP_Query( $args );

		if ( $query->found_posts != 0 )
			return $query;

		return false;
	}

    /**
     * Replace characters that are not allowed in the YML file.
     *
     * @since     0.3.0
     * @param     $string
     * @return    mixed
     */
	private function clean( $string ) {
	    $string = str_replace( '"', '&quot;', $string);
        $string = str_replace( '&', '&amp;', $string);
        $string = str_replace( '>', '&gt;', $string);
        $string = str_replace( '<', '&lt;', $string);
        $string = str_replace( '\'', '&apos;', $string);
        //$string = str_replace( '&nbsp;', '', $string );
	    return $string;
    }

	/**
	 * Generate YML header.
	 *
	 * @since     0.3.0
	 * @param     $currency
	 *
	 * @return    string
	 */
	private function yml_header( $currency ) {

		$yml  = '<?xml version="1.0" encoding="' . get_bloginfo( "charset" ) . '"?>'.PHP_EOL;
		$yml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'.PHP_EOL;
		$yml .= '<yml_catalog date="' . date( "Y-m-d H:i" ) . '">'.PHP_EOL;
		$yml .= '  <shop>'.PHP_EOL;
		$yml .= '    <name>' . esc_html( $this->settings['website_name'] ) . '</name>'.PHP_EOL;
		$yml .= '    <company>' . esc_html( $this->settings['company_name'] ) . '</company>'.PHP_EOL;
		$yml .= '    <url>' . get_site_url() . '</url>'.PHP_EOL;

		$yml .= '    <currencies>'.PHP_EOL;
		if ( ( $currency == 'USD' ) || ( $currency == 'EUR' ) ):
			$yml .= '      <currency id="RUR" rate="1"/>'.PHP_EOL;
			$yml .= '      <currency id="' . $currency . '" rate="СВ" />'.PHP_EOL;
		else:
			$yml .= '      <currency id="' . $currency . '" rate="1" />'.PHP_EOL;
		endif;
		$yml .= '    </currencies>'.PHP_EOL;

		$yml .= '    <categories>'.PHP_EOL;
		foreach ( get_categories( array( 'taxonomy' => 'product_cat', 'orderby' => 'term_id' ) ) as $category ):
			if ( $category->parent == 0 ) {
				$yml .= '      <category id="' . $category->cat_ID . '">' . wp_strip_all_tags( $category->name ) . '</category>'.PHP_EOL;
			} else {
				$yml .= '      <category id="' . $category->cat_ID . '" parentId="' . $category->parent . '">' . wp_strip_all_tags( $category->name) . '</category>'.PHP_EOL;
			}
		endforeach;
		$yml .= '    </categories>'.PHP_EOL;
		$yml .= '    <offers>'.PHP_EOL;

		return $yml;
	}

	/**
	 * Generate YML body with offers.
	 *
	 * @since     0.3.0
	 * @param     $currency
	 * @param     $query
	 *
	 * @return    string
	 */
	private function yml_offers( $currency, $query ) {

		$yml = '';

		while ( $query->have_posts() ):

			$query->the_post();

			$product = wc_get_product( $query->post->ID );
			// We use a seperate variable for offer because we will be rewriting it for variable products.
			$offer = $product;

			/*
			 * By default we set $variation_count to 1.
			 * That means that there is at least one product available.
			 * Variation products will have more than 1 count.
			 */
			$variation_count = 1;
            if ( $product->is_type( 'variable' ) ):
                $variations = $product->get_available_variations();
                $variation_count = count( $variations );
            endif;

			while ( $variation_count > 0 ):
				$variation_count--;

				// If variable product, get product id from $variations array.
				$offerID = ( ( $product->is_type( 'variable' ) ) ? $variations[ $variation_count ][ 'variation_id' ] : $product->id );

				// Prepare variation link.
				$var_link = '';
				if ( $product->is_type( 'variable' ) ):
					$variable_attribute = wc_get_product_variation_attributes( $offerID );
					$var_link = '?' . key( $variable_attribute ) . '=' . current( $variable_attribute );

					// This has to work but we need to think of a way to save the initial offer variable.
					$offer = new WC_Product_Variation( $offerID );
				endif;

				// NOTE: Below this point we start using $offer instead of $product.
				$yml .= '      <offer id="' . $offerID . '" available="'.( ( $offer->is_in_stock() ) ? "true" : "false" ).'">'.PHP_EOL;
				$yml .= '        <url>' . esc_url( get_permalink( $offer->id ) . $var_link ) . '</url>'.PHP_EOL;

				// Price.
				if ( $offer->sale_price && ( $offer->sale_price < $offer->regular_price ) ):
					$yml .= '        <price>' . $offer->sale_price . '</price>'.PHP_EOL;
					$yml .= '        <oldprice>' . $offer->regular_price . '</oldprice>'.PHP_EOL;
				else:
					$yml .= '        <price>' . $offer->regular_price . '</price>'.PHP_EOL;
				endif;

				$yml .= '        <currencyId>'.$currency.'</currencyId>'.PHP_EOL;

				// Category.
				// Not using $offerID, because variable products inherit category from parent.
				$categories = get_the_terms( $product->id, 'product_cat' );
				$category = array_shift( $categories );
				$yml .= '        <categoryId>' . $category->term_id . '</categoryId>'.PHP_EOL;

				// Market category.
				if ( isset( $this->settings['market_category'] ) && $this->settings['market_category'] != 'not_set' ):
					$market_category = wc_get_product_terms( $offerID, 'pa_'.$this->settings['market_category'], array( 'fields' => 'names' ) );
					if ( $market_category )
						$yml .= '        <market_category>' . wp_strip_all_tags( array_shift( $market_category ) ) . '</market_category>'.PHP_EOL;
				endif;

				// TODO: get all the images
				$image = get_the_post_thumbnail_url( null, 'full' );
				//foreach ( $images as $image ):
					if ( strlen( utf8_decode( $image ) ) <= 512 )
						$yml .= '        <picture>' . esc_url( $image ) . '</picture>'.PHP_EOL;
				//endforeach;

				$yml .= '        <delivery>true</delivery>'.PHP_EOL;
				$yml .= '        <name>' . $this->clean( $offer->get_title() ) . '</name>'.PHP_EOL;

				// Vendor.
				if ( isset( $this->settings['vendor'] ) && $this->settings['vendor'] != 'not_set' ) {
					$vendor = wc_get_product_terms( $offerID, 'pa_' . $this->settings['vendor'], array( 'fields' => 'names' ) );
					if ( $vendor )
						$yml .= '        <vendor>' . wp_strip_all_tags( array_shift( $vendor ) ) . '</vendor>'.PHP_EOL;
				}

				// Vendor code.
				if ( $offer->sku )
					$yml .= '        <vendorCode>' . $offer->sku . '</vendorCode>'.PHP_EOL;

				// Description.
				if ( $offer->post->post_content )
					$yml .= '        <description><![CDATA[' . html_entity_decode( $offer->post->post_content, ENT_COMPAT, "UTF-8" ) . ']]></description>'.PHP_EOL;
				// Sales notes.
				if ( strlen( $this->settings['sales_notes'] ) > 0 )
					$yml .= '        <sales_notes>' . wp_strip_all_tags( $this->settings['sales_notes'] ) . '</sales_notes>'.PHP_EOL;

				// Params: size and weight.
                if ( $this->settings['size'] != false ):
                    if ($product->has_weight())
                    {
                        $weight_unit = esc_attr( get_option( 'woocommerce_weight_unit' ) );
                        if ( in_array( $weight_unit, $this->weight_units ) )
                            $yml .= '        <param name="' . __('Weight', 'woocommerce') . '" unit="' . __($weight_unit, 'woocommerce') . '">' . $product->get_weight() . '</param>' . PHP_EOL;
                    }

                    if ($product->has_dimensions())
                    {
                        $size_unit = esc_attr( get_option( 'woocommerce_dimension_unit' ) );
                        if ( in_array( $size_unit, $this->size_units ) )
                        {
                            $yml .= '        <param name="' . __('Length', 'woocommerce') . '" unit="' . __($size_unit, 'woocommerce') . '">' . $product->get_length() . '</param>' . PHP_EOL;
                            $yml .= '        <param name="' . __('Width', 'woocommerce') . '" unit="' . __($size_unit, 'woocommerce') . '">' . $product->get_width() . '</param>' . PHP_EOL;
                            $yml .= '        <param name="' . __('Height', 'woocommerce') . '" unit="' . __($size_unit, 'woocommerce') . '">' . $product->get_height() . '</param>' . PHP_EOL;
                        }
                    }
                endif;

                // Params: selected parameters.
                $attributes = $product->get_attributes();
                foreach ( $this->settings['params'] as $param_id ) :
                    if ( array_key_exists( wc_attribute_taxonomy_name_by_id( $param_id ), $attributes ) )
                        $yml .= '        <param name="' . wc_attribute_label( wc_attribute_taxonomy_name_by_id( $param_id ) ) . '">' . $product->get_attribute( wc_attribute_taxonomy_name_by_id( $param_id ) ) . '</param>' . PHP_EOL;
                endforeach;
                /* Backup for params
                foreach ( $attributes as $attribute ) :
                    foreach ( $this->settings['params'] as $param_id ) :
                        if ( $attribute['name'] == wc_attribute_taxonomy_name_by_id( $param_id ) )
                            $yml .= '        <param name="' . wc_attribute_label( wc_attribute_taxonomy_name_by_id( $param_id ) ) . '">' . $product->get_attribute( wc_attribute_taxonomy_name_by_id( $param_id ) ) . '</param>' . PHP_EOL;
                    endforeach;
                endforeach; */

				$yml .= '      </offer>'.PHP_EOL;
			endwhile;

		endwhile;

		return $yml;
	}

	/**
	 * Generate YML footer.
	 *
	 * @since     0.3.0
	 * @return    string
	 */
	private function yml_footer() {

		$yml  = '    </offers>'.PHP_EOL;
		$yml .= '  </shop>'.PHP_EOL;
		$yml .= '</yml_catalog>'.PHP_EOL;

		return $yml;
	}

}
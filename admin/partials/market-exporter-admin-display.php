<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      0.0.1
 */
?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php // If someone clicks the button
	if ( !empty( $_POST[ $this->plugin_name ] ) ) {
		if ( !current_user_can('manage_options') )
			wp_die( _e( 'Silence is golden', 'market-exporter' ) );
			
		check_admin_referer( $this->plugin_name );

		// Check currency.
		if ( !$currency = $this->get_currecny() ) {
			echo '	<p>' . sprintf( __( 'Currently only the following currency is supported: Russian Ruble (RUB), Ukrainian Hryvnia (UAH), US Dollar (USD) and Euro (EUR). Please <a href="%s">update currency</a>.', 'market-exporter' ), admin_url( 'admin.php?page=wc-settings' ) ) . "</p>";
			return;
		}

		// Get products.
		if ( !$ya_offers = $this->get_products() ) {
			echo '	<p>' . sprintf( __( 'Unable to find any products. Are you sure <a href="%s">some exist</a>?', 'market-exporter' ), admin_url( 'post-new.php?post_type=product' ) ) . "</p>";
			return;
		}
		
		$shop_settings = get_option( 'market_exporter_shop_settings' );
		if ( !isset( $shop_settings['image_count'] ) )
			$shop_settings['image_count'] = 10;

		$yml = '<?xml version="1.0" encoding="'.get_bloginfo( "charset" ).'"?>'.PHP_EOL;
		$yml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'.PHP_EOL;
		$yml .= '<yml_catalog date="'.Date("Y-m-d H:i").'">'.PHP_EOL;
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
		
			$price = $this->get_price( $offer->ID );
			$images = $this->get_images( $offer->ID, $shop_settings['image_count'] );
			$categoryId = get_the_terms( $offer->ID, 'product_cat' );
			
			$yml .= '      <offer id="'.$offer->ID.'" available="true">'.PHP_EOL;
			$yml .= '        <url>'.get_permalink($offer->ID).'</url>'.PHP_EOL;
			$yml .= '        <price>'.$price.'</price>'.PHP_EOL;
			$yml .= '        <currencyId>'.$currency.'</currencyId>'.PHP_EOL;
			$yml .= '        <categoryId>'.$categoryId[0]->term_id.'</categoryId>'.PHP_EOL;
			foreach ( $images as $image ):
				if ( strlen( utf8_decode( $image ) ) <= 512 )
					$yml .= '        <picture>'.$image.'</picture>'.PHP_EOL;
			endforeach;
			$yml .= '        <delivery>true</delivery>'.PHP_EOL;
			$yml .= '        <name>'.wp_strip_all_tags( $offer->name ).'</name>'.PHP_EOL;
			$yml .= '        <description>'.wp_strip_all_tags( $offer->description ).'</description>'.PHP_EOL;
			if ($offer->vendorCode)
				$yml .= '        <vendorCode>'.wp_strip_all_tags( $offer->vendorCode ).'</vendorCode>'.PHP_EOL;
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
		
		/* Debugging: */
		//echo "<pre>";
		//print_r($ya_offers);
		//echo strtr($yml,Array("<"=>"&lt;","&"=>"&amp;"));
		//echo "</pre>";
		/**/
		
		$file_path = $this->write_file( $yml );
		echo '	<p>' . sprintf( __( 'File exported successfully: <a href="%s">%s</a>.', 'market-exporter' ), $file_path, $file_path ) . '</p>';
		
	// Display the form by default.
	} else {
	?>
		<form method="post" action="">
		<?php wp_nonce_field( $this->plugin_name ) ?>
		<p><?php _e( 'This plugin is used to generate a valid YML file for exporting your products in WooCommerce to Yandex Market.', 'market-exporter' ); ?></p>	
						
		<p><?php _e( 'Please be patient while the YML file is generated. This can take a while if your server is slow (inexpensive hosting) or if you have many products in WooCommerce. Do not navigate away from this page until this script is done or the YML file will not be created. You will be notified via this page when the process is completed.', 'market-exporter' ); ?></p>	

		<p><?php _e( 'To begin, just press the button below.', 'market-exporter'); ?></p>
		
		<p><input type="submit" class="button hide-if-no-js" name="market-exporter" id="market-exporter" value="<?php _e( 'Generate YML file', 'market-exporter' ) ?>" /></p>
		
		<noscript><p><em><?php _e( 'You must enable Javascript in order to proceed!', 'market-exporter' ) ?></em></p></noscript>
		
		</form>
	<?php
	}
	?>
</div>
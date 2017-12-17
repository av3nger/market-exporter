<?php
/**
 * Base offer class for export.
 *
 * @package Market_Exporter
 *
 * @since 1.0.3
 */

/**
 * Class Market_Exporter_Offer
 */
class Market_Exporter_Offer {

	/**
	 * Product variable.
	 *
	 * @var WC_Product
	 */
	private $product;

	private $settings;

	/**
	 * Market_Exporter_Offer constructor.
	 *
	 * @param WC_Product $product  Init with product.
	 */
	public function __construct( WC_Product $product ) {
		$this->product  = $product;
		$this->settings = get_option( 'market_exporter_shop_settings' );
	}

	/**
	 * Generate offer item for YML file.
	 *
	 * @param int $id  Product or variation ID.
	 *
	 * @return string
	 */
	public function get_offer() {
		$yml = '';

		if ( $this->product->is_type( 'variable' ) ) {
			foreach ( $this->product->get_children() as $id ) {
				$yml .= $this->get_offer_object( $id );
			}
		} else {
			$yml .= $this->get_offer_object( $this->product->get_id() );
		}


		$offer_id = ( ( $this->product->is_type( 'variable' ) ) ? $variations[ $variation_count ]['variation_id'] : $this->product->get_id() );

		$offer_id = $this->product->get_id();
		$is_variable = $this->product->is_type( 'variable' );

		$offer_object = $this->get_offer_object( $offer_id, $is_variable );

		return $offer_object;

		$yml  = '      <offer id="' . $offer_id . '" ' . ( ( $type_prefix_set ) ? 'type="vendor.model"' : '' ) . ' available="' . ( ( $offer->is_in_stock() ) ? 'true' : 'false' ) . '">' . PHP_EOL;
		$yml .= <<<EOD
		

		</offer>
EOD;

		return $yml;
	}

	/**
	 * @param int $id  Either product or variation id.
	 *
	 * @return string
	 */
	private function get_offer_object( $id ) {

		if ( $this->product->is_type( 'variable' ) ) {
			$offer = new WC_Product_Variation( $id );
		} else {
			$offer = $this->product;
		}

		// This is used for detecting if typePrefix is set. If it is, we need to add type="vendor.model" to the
		// offer and remove the name attribute.
		$type_prefix_set = false;
		if ( isset( $this->settings['type_prefix'] ) && 'not_set' !== $this->settings['type_prefix'] ) {
			$type_prefix = $this->product->get_attribute( 'pa_' . $this->settings['type_prefix'] );
			if ( $type_prefix ) {
				$type_prefix_set = 'type="vendor.model"';
			}
		}

		$yml = "      <offer id=\"{$id}\" {$type_prefix_set} available=\"{$offer->is_in_stock()}\">" . PHP_EOL;
		
		//$yml = '      <offer id="' . $id . '" ' . ( ( $type_prefix_set ) ? 'type="vendor.model"' : '' ) . ' available="' . ( ( $offer->is_in_stock() ) ? 'true' : 'false' ) . '">' . PHP_EOL;

		return $yml;
	}
}

<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/av3nger/market-exporter/
 * @since      0.0.1
 *
 * @package    Market_Exporter
 * @subpackage Market_Exporter/admin/partials
 */

?>

<div class="wrap" id="me_pages">

	<div class="version">
		<?php
		printf( // WPCS: XSS OK.
			/* translators: version number */
			__( 'Version: %s', 'market-exporter' ),
			$this->version
		);
		?>
	</div>

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

</div>

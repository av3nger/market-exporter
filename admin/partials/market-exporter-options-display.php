<?php

/**
 * Provide a admin options area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      0.0.4
 */
?>
 
<div class="wrap">

	<?php
	if ( !current_user_can('manage_options') )
		wp_die( _e( 'Silence is golden', 'market-exporter' ) );
	?>
	
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	
  <form method="post" action="options.php">
    <?php
      settings_fields( $this->plugin_name );
      do_settings_sections( $this->plugin_name );
      submit_button();
    ?>
  </form>

</div>
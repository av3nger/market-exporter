<?php
/**
 * The file system specific functionality of the plugin.
 */

class Market_Exporter_FS {

	private $plugin_name;

	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

	/**
	 * Initiate file system for read/write operations.
	 *
	 * @since   0.0.8
	 * @return  bool                          Return true if everything ok.
	 */
	function init_fs() {
		$url = wp_nonce_url( 'tools.php?page=market-exporter', $this->plugin_name );

		// Need to include file.php for cron.
		if( ! function_exists('request_filesystem_credentials') ) {
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');}

		if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) {
			// If we get here, then we don't have credentials yet,
			// but have just produced a form for the user to fill in,
			// so stop processing for now.
			return true; // Stop the normal page form from displaying.
		}

		// Mow we have some credentials, try to get the wp_filesystem running.
		if ( ! WP_Filesystem( $creds ) ) {
			// Our credentials were no good, ask the user for them again.
			request_filesystem_credentials( $url, "", true, false, null );

			return true;
		}

		return true;
	}

	/**
	 * Write YML file to /wp-content/uploads/ dir.
	 *
	 * @since   0.0.1
	 *
	 * @param    string $yml  Variable to display contents of the YML file.
	 * @param    string $date Yes or No for date at the end of the file.
	 *
	 * @return  string                        Return the path of the saved file.
	 */
	public function write_file( $yml, $date ) {
		// If unable to initialize filesystem, quit.
		if ( ! $this->init_fs() ) {
			return false;
		}

		// By this point, the $wp_filesystem global should be working, so let's use it to create a file.
		global $wp_filesystem;

		// Get the upload directory and make a ym-export-YYYY-mm-dd.yml file.
		$upload_dir = wp_upload_dir();
		$folder     = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $this->plugin_name );
		if ( $date == 'yes' ) {
			$filename = 'ym-export-' . date( "Y-m-d" ) . '.yml';
		} else {
			$filename = 'ym-export.yml';
		}

		$filepath = $folder . $filename;

		// Check if 'uploads/market-exporter' folder exists. If not - create it.
		if ( ! $wp_filesystem->exists( $folder ) ) {
			if ( ! $wp_filesystem->mkdir( $folder, FS_CHMOD_DIR ) ) {
				_e( "Error creating directory.", 'market-exporter' );
			}

		}
		// Create the file.
		if ( ! $wp_filesystem->put_contents( $filepath, $yml, FS_CHMOD_FILE ) ) {
			_e( "Error uploading file.", 'market-exporter' );
		}

		return $upload_dir['baseurl'] . '/' . $this->plugin_name . '/' . $filename;
	}

	/**
	 * Get a list of generated YML files.
	 *
	 * @since   0.0.8
	 * @return  array                          Returns an array of generated files.
	 */
	function get_files() {
		// If unable to initialize filesystem, quit.
		if ( ! $this->init_fs() ) {
			return false;
		}

		// By this point, the $wp_filesystem global should be working, so let's use it to create a file.
		global $wp_filesystem;

		// Get the upload directory and make a ym-export-YYYY-mm-dd.yml file.
		$upload_dir = wp_upload_dir();
		$folder     = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $this->plugin_name );

		return $wp_filesystem->dirlist( $folder );
	}

	/**
	 * Delete selected files.
	 *
	 * @since      0.0.8
	 * @param      array  $files                        Array of filenames to delete.
     * @return     bool
	 */
	function delete_files( $files ) {
		// If unable to initialize filesystem, quit.
		if ( ! $this->init_fs() ) {
			return false;
		}

		// By this point, the $wp_filesystem global should be working, so let's use it to create a file.
		global $wp_filesystem;

		// Get the upload directory and make a ym-export-YYYY-mm-dd.yml file.
		$upload_dir = wp_upload_dir();
		$folder     = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $this->plugin_name );

		foreach ( $files as $file ):
			$wp_filesystem->delete( $folder . $file );
		endforeach;

		return true;
	}

}
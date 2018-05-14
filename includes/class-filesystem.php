<?php
/**
 * Filesystem class
 *
 * @link       https://github.com/av3nger/market-exporter/
 * @since      0.0.1
 *
 * @package    Market_Exporter
 * @subpackage Market_Exporter/includes
 */

namespace Market_Exporter\Core;

/**
 * Market Exporter: Filesystem class
 *
 * The file system specific functionality of the plugin.
 *
 * @since      0.0.1
 * @package    Market_Exporter
 * @subpackage Market_Exporter/includes
 * @author     Anton Vanyukov <a.vanyukov@testor.ru>
 */
class Filesystem {

	/**
	 * Plugin slug.
	 *
	 * @access private
	 * @var    string $plugin_name Plugin slug.
	 */
	private $plugin_name;

	/**
	 * Use WP_Filesystem API.
	 *
	 * @since 1.0.4
	 * @var bool $fs_api
	 */
	private $fs_api = false;

	/**
	 * Market_Exporter_FS constructor.
	 *
	 * @param string $plugin_name Plugin slug.
	 */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

	/**
	 * Initiate file system for read/write operations.
	 *
	 * @since  0.0.8
	 * @return bool Return true if everything ok.
	 */
	function init_fs() {
		$url = wp_nonce_url( "tools.php?page={$this->plugin_name}", $this->plugin_name );

		// Need to include file.php for cron.
		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		// Check if the user has write permissions.
		$access_type = get_filesystem_method();
		if ( 'direct' === $access_type ) {
			$this->fs_api = true;

			// You can safely run request_filesystem_credentials() without any issues
			// and don't need to worry about passing in a URL.
			$credentials = request_filesystem_credentials( $url, '', false, false, null );

			// Mow we have some credentials, try to get the wp_filesystem running.
			if ( ! WP_Filesystem( $credentials ) ) {
				// Our credentials were no good, ask the user for them again.
				return false;
			}
		} else {
			// Don't have direct write access.
			$this->fs_api = false;
		}

		return true;
	}

	/**
	 * Write YML file to /wp-content/uploads/ dir.
	 *
	 * @since  0.0.1
	 * @param  string $yml  Variable to display contents of the YML file.
	 * @param  string $date Yes or No for date at the end of the file.
	 * @return string       Return the path of the saved file.
	 */
	public function write_file( $yml, $date ) {
		// If unable to initialize filesystem, quit.
		if ( ! $this->init_fs() ) {
			return false;
		}

		// Get the upload directory and make a ym-export-YYYY-mm-dd.yml file.
		$upload_dir = wp_upload_dir();
		$folder     = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $this->plugin_name );
		if ( $date ) {
			$filename = 'ym-export-' . date( 'Y-m-d' ) . '.yml';
		} else {
			$filename = 'ym-export.yml';
		}

		$filepath = $folder . $filename;

		// Use WP_Filesystem API.
		if ( $this->fs_api ) {
			// By this point, the $wp_filesystem global should be working, so let's use it to create a file.
			/* @var \WP_Filesystem_Base $wp_filesystem */
			global $wp_filesystem;

			// Check if 'uploads/market-exporter' folder exists. If not - create it.
			if ( ! $wp_filesystem->exists( $folder ) ) {
				if ( ! $wp_filesystem->mkdir( $folder, FS_CHMOD_DIR ) ) {
					esc_html_e( 'Error creating directory.', 'market-exporter' );
				}
			}
			// Create the file.
			if ( ! $wp_filesystem->put_contents( $filepath, $yml, FS_CHMOD_FILE ) ) {
				esc_html_e( 'Error uploading file.', 'market-exporter' );
			}
		} else {
			// Check if 'uploads/market-exporter' folder exists. If not - create it.
			if ( ! is_dir( $folder ) ) {
				if ( ! @wp_mkdir_p( $folder ) ) {
					esc_html_e( 'Error creating directory.', 'market-exporter' );
				}
			}
			// Create the file.
			$file = fopen( $filepath, 'w' );
			if ( ! fwrite( $file, $yml ) ) {
				esc_html_e( 'Error uploading file.', 'market-exporter' );
			} elseif ( $file ) {
				fclose( $file );
			}
		}

		return $upload_dir['baseurl'] . '/' . $this->plugin_name . '/' . $filename;
	}

	/**
	 * Get a list of generated YML files.
	 *
	 * @since  0.0.8
	 * @return array|bool Returns an array of generated files.
	 */
	function get_files() {
		// If unable to initialize filesystem, quit.
		if ( ! $this->init_fs() ) {
			return false;
		}

		// Get the upload directory and make a ym-export-YYYY-mm-dd.yml file.
		$upload_dir = wp_upload_dir();
		$folder     = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $this->plugin_name );

		// Use WP_Filesystem API.
		if ( $this->fs_api ) {
			// By this point, the $wp_filesystem global should be working, so let's use it to create a file.
			/* @var \WP_Filesystem_Base $wp_filesystem */
			global $wp_filesystem;
			return $wp_filesystem->dirlist( $folder );
		} else {
			$dir = scandir( $folder );
			// Let's form the same array as dirlist provides.
			$structure = array();
			foreach ( $dir as $directory ) {
				if ( '.' === $directory || '..' === $directory ) {
					continue;
				}

				$structure[ $directory ]['name'] = $directory;
			}
			return $structure;
		}
	}

	/**
	 * Delete selected files.
	 *
	 * @since  0.0.8
	 * @param  array $files Array of file names to delete.
	 * @return bool
	 */
	function delete_files( $files ) {
		// If unable to initialize filesystem, quit.
		if ( ! $this->init_fs() ) {
			return false;
		}

		// Get the upload directory and make a ym-export-YYYY-mm-dd.yml file.
		$upload_dir = wp_upload_dir();
		$folder     = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $this->plugin_name );

		// Use WP_Filesystem API.
		if ( $this->fs_api ) {
			// By this point, the $wp_filesystem global should be working, so let's use it to create a file.
			/* @var \WP_Filesystem_Base $wp_filesystem */
			global $wp_filesystem;

			foreach ( $files as $file ) {
				$wp_filesystem->delete( $folder . $file );
			}
		} else {
			foreach ( $files as $file ) {
				@unlink( $folder . $file );
			}
		}

		return true;
	}

}

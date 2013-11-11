<?php
/**
 * Plugin Name.
 *
 * @package   S3_CloudFront_URLs
 * @author    Lew Goettner <lew@goettner.net>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 Lew Goettner
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @package S3_CloudFront_URLs_AJAX
 * @author  Lew Goettner <lew@goettner.net>
 */
class S3_CloudFront_URLs_AJAX {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * TODO:
		 *
		 * - Rename "S3_CloudFront_URLs" to the name of your initial plugin class
		 *
		 */
		$plugin = S3_CloudFront_URLs::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Add AJAX Call
		add_action('wp_ajax_qloudstat_numbers', array($this, 'process_qloudstat_ajax'));

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	
	/**
	 * Process the request for file downloads
	 *
	 * @since    1.0.0
	 */
	public function process_qloudstat_ajax() {
		// check to see if the submitted nonce matches with the
		// generated nonce we created earlier
		if ( ! wp_verify_nonce( $_POST['qs_nonce'], $this->plugin_slug.'-qs-nonce' ) )
			die ( 'Tisk, tisk!');
	 
		// ignore the request if the current user doesn't have
		// sufficient permissions
		if ( ! current_user_can( 'upload_files' ) )
			die ( 'Tisk, tisk!');

		// Config Values
		$qs_key = get_option($this->plugin_slug."-qloudstat-api-key");
		$qs_secret = get_option($this->plugin_slug."-qloudstat-api-secret");

		$api_url = get_option($this->plugin_slug."-qloudstat-api-url");

		// POST Values
		$filename = $_POST['filename'];

		// Setup a call for 200s, filtered to the filename
		$url = $api_url;
		$url .= '/uri,statuscode/hits/values';
		$url .= '?filter=' . urlencode("$filename,200");
		$url .= '&from=' . get_option($this->plugin_slug.'-qloudstat-start-date');

	    try {

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
	        curl_setopt($ch, CURLOPT_USERPWD, $qs_key . ":" . $qs_secret);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	        $resp = curl_exec($ch);

	        // validate CURL status
	        if(curl_errno($ch))
	            throw new Exception(curl_error($ch), 500);

	        // validate HTTP status code (user/password credential issues)
	        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        if ($status_code != 200)
	            throw new Exception("Response with Status Code [" . $status_code . "].", 500);

	        $response = json_decode($resp);

	        $hits = 0;
	        if (isset($response->table->rows) && ( count($response->table->rows) > 0 ) ) {
	        	$hits = $response->table->rows[0]->c[2]->v;
	        }

	        // Check for the value
	        // $hits = $response->table->rows->2

	        header( "Content-Type: application/json" );
	        echo json_encode(array('hits' => $hits, 'filename' => $filename));
	        exit();

	    }
	    catch(Exception $ex) {
	        echo $ex;
	    }
	    if ($ch != null) curl_close($ch);

	}



}

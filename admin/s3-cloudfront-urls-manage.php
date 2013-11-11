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
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package S3_CloudFront_URLs_Manage
 * @author  Lew Goettner <lew@goettner.net>
 */
class S3_CloudFront_URLs_Manage {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

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

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

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
	 * Register and enqueue admin-specific style sheet.
	 *
	 * TODO:
	 *
	 * - Rename "S3_CloudFront_URLs" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), S3_CloudFront_URLs::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * TODO:
	 *
	 * - Rename "S3_CloudFront_URLs" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			// Add AWS SDK
			wp_enqueue_script( $this->plugin_slug . '-aws-sdk', 'https://sdk.amazonaws.com/js/aws-sdk-2.0.0-rc1.min.js', null, '2.0.0-rc1');
			wp_enqueue_script( $this->plugin_slug . '-manage-script', plugins_url( 'assets/js/manage.js', __FILE__ ), array( 'jquery' ), S3_CloudFront_URLs::VERSION );

			// in javascript, object properties are accessed as s3_cloudfront_settings.ajax_url, etc
			wp_localize_script( $this->plugin_slug . '-manage-script', 's3_cloudfront_settings',
            	array( 
            		's3BucketName' => get_option($this->plugin_slug.'-bucket'), 
            		'cloudFrontURL' => get_option($this->plugin_slug.'-cloudfront-url'),
            		'bucket_prefix' => get_option($this->plugin_slug.'-bucket-prefix'),
            		'accessKeyId' => get_option($this->plugin_slug.'-access-key-id'),
            		'secretAccessKey' => get_option($this->plugin_slug.'-secret-access-key'),
            		'ajax_url' => admin_url('admin-ajax.php'),
            		'qs_nonce' => wp_create_nonce( $this->plugin_slug.'-qs-nonce' ),
            		'qs_setup' => $this->is_qloudstat_setup()
            	) 
            );

		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'S3/CloudFront Uploads', $this->plugin_slug ),
			__( 'S3 Uploads', $this->plugin_slug ),
			'upload_files',
			$this->plugin_slug . '-manage',
			array( $this, 'display_plugin_admin_page' ),
			null,
			'30.33'
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/manage.php' );
	}

	// Simple check for Qloudstat setup
	private function is_qloudstat_setup() {
		$has_api = stristr(get_option($this->plugin_slug.'-qloudstat-api-url'), 'api.qloudstat.com');
		$has_key = strlen(get_option($this->plugin_slug.'-qloudstat-api-key')) > 0;
		$has_secret = strlen(get_option($this->plugin_slug.'-qloudstat-api-secret')) > 0;
		if ($has_api && $has_key && $has_secret) {
			return(true);
		} else {
			return(false);
		}
	}

}

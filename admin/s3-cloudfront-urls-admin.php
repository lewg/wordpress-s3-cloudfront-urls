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
 * @package S3_CloudFront_URLs_Admin
 * @author  Lew Goettner <lew@goettner.net>
 */
class S3_CloudFront_URLs_Admin {

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

		// Add settings
		add_action( 'admin_init', array($this, 'initialize_options'));

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		// add_action( 'TODO', array( $this, 'action_method_name' ) );
		// add_filter( 'TODO', array( $this, 'filter_method_name' ) );

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
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), S3_CloudFront_URLs::VERSION );
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
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'S3/CloudFront Settings', $this->plugin_slug ),
			__( 'S3/CloudFront', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function initialize_options() {
		/// S3 Settings
	    add_settings_section(  
	        's3_settings_section',         	// ID used to identify this section and with which to register options  
	        'S3 Settings',                  // Title to be displayed on the administration page  
	        's3_settings_section_callback', // Callback used to render the description of the section  
	        $this->plugin_slug             	// Page on which to add this section of options  
	    );  

	    add_settings_field( 
	    	$this->plugin_slug . "-access-key-id", 
	    	'Access Key Id', 
	    	array($this, 'setting_string_callback'),
	    	$this->plugin_slug,
	    	's3_settings_section',
	    	array(
	    		'field' => $this->plugin_slug . '-access-key-id',
	    	)
	    );

	    register_setting( 
	    	$this->plugin_slug, 
	    	$this->plugin_slug . "-access-key-id"
	    	// '$sanitize_callback'
	    );

	    add_settings_field( 
	    	$this->plugin_slug . "-secret-access-key", 
	    	'Secret Access Key', 
	    	array($this, 'setting_string_callback'),
	    	$this->plugin_slug,
	    	's3_settings_section',
	    	array(
	    		'field' => $this->plugin_slug . '-secret-access-key',
	    	)
	    );

	    register_setting( 
	    	$this->plugin_slug, 
	    	$this->plugin_slug . '-secret-access-key'
	    	// '$sanitize_callback'
	    );

	    add_settings_field( 
	    	$this->plugin_slug . "-bucket", 
	    	'Bucket Name', 
	    	array($this, 'setting_string_callback'),
	    	$this->plugin_slug,
	    	's3_settings_section',
	    	array(
	    		'field' => $this->plugin_slug . '-bucket',
	    	)
	    );

	    register_setting( 
	    	$this->plugin_slug, 
	    	$this->plugin_slug . '-bucket'
	    	// '$sanitize_callback'
	    );

	    add_settings_field( 
	    	$this->plugin_slug . "-bucket-prefix", 
	    	'Bucket Prefix', 
	    	array($this, 'setting_string_callback'),
	    	$this->plugin_slug,
	    	's3_settings_section',
	    	array(
	    		'field' => $this->plugin_slug . '-bucket-prefix',
	    		'hint' => 'Prefix all files with this value. (Optional)'
	    	)
	    );

	    register_setting( 
	    	$this->plugin_slug, 
	    	$this->plugin_slug . "-bucket-prefix"
	    	// '$sanitize_callback'
	    );

	    add_settings_section(  
	        'cloudfront_settings_section',         	// ID used to identify this section and with which to register options  
	        'CloudFront Settings',                  // Title to be displayed on the administration page  
	        'cloudfront_settings_section_callback', // Callback used to render the description of the section  
	        $this->plugin_slug             	// Page on which to add this section of options  
	    );  

	    add_settings_field( 
	    	$this->plugin_slug . "-cloudfront-url", 
	    	'CloudFront Base URL', 
	    	array($this, 'setting_string_callback'),
	    	$this->plugin_slug,
	    	'cloudfront_settings_section',
	    	array(
	    		'field' => $this->plugin_slug . '-cloudfront-url',
	    		'hint' => 'Include http:// or https://'
	    	)
	    );

	    register_setting( 
	    	$this->plugin_slug, 
	    	$this->plugin_slug . "-cloudfront-url"
	    	// '$sanitize_callback'
	    );


	}

	public function s3_settings_section_callback() {
		echo 'Enter the keys for accessing your S3 bucket.';
	}

	public function cloudfront_settings_section_callback() {
		echo 'Settings for your CloudFront distribution.';
	}

	public function setting_string_callback($args) {
		$field = esc_attr($args['field']);
		$setting = esc_attr( get_option( $args['field'] ) );
		echo "<input type='text' name='$field' value='$setting' />";
		if (array_key_exists('hint', $args)) {
			echo " <em>" . htmlspecialchars($args['hint']);
		}
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}

}

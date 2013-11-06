<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   S3_CloudFront_URLs
 * @author    Lew Goettner <lew@goettner.net>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 Lew Goettner
 *
 * @wordpress-plugin
 * Plugin Name:       S3/CloudFront Upload Tool
 * Plugin URI:        TODO
 * Description:       Tool specifically for uploading to S3 for the purpose of generating CloudFront Links.
 * Version:           1.0.0
 * Author:            Lew Goettner
 * Author URI:        http://www.goettner.net
 * Text Domain:       s3-cloudfront-urls
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/lewg/wordpress-s3-cloudfront-urls
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . '/public/s3-cloudfront-urls.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'S3_CloudFront_URLs', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'S3_CloudFront_URLs', 'deactivate' ) );


add_action( 'plugins_loaded', array( 'S3_CloudFront_URLs', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . '/admin/s3-cloudfront-urls-admin.php' );
	add_action( 'plugins_loaded', array( 'S3_CloudFront_URLs_Admin', 'get_instance' ) );

	require_once( plugin_dir_path( __FILE__ ) . '/admin/s3-cloudfront-urls-manage.php' );
	add_action( 'plugins_loaded', array( 'S3_CloudFront_URLs_Manage', 'get_instance' ) );


}

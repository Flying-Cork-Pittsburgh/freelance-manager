<?php
/*
Plugin Name: Freelance Manager
Description: Helps WordPress freelancers to manage clients sites. Run this on your website.
Plugin URI: https://github.com/andrewwoods/freelance-manager
Version: 0.1
Author: awoods
Author URI: http://andrewwoods.net
*/

define('FREMGR_VERSION', 0.1);

require_once 'post-types/client.php';
require_once 'post-types/message.php';

if ( defined('WP_CLI') && WP_CLI ) {
	include_once __DIR__ . '/wpcli_client.php';
}

/**
* Primary class for Freelance Manager plugin
*
* Track information about all of your clients from your wordpress dashboard
*
*
* @package  Freelance_Manager
* @access   public
*/
class Freelance_Manager
{
	private static $instance = null;

	/**
	 * constructor - manages all the actions and filters
	 *
	 * @since 0.1
	 * @return void
	 */
	public function __construct() {
		register_activation_hook( __FILE__, array( 'Freelance_Manager', 'activation' ) );
		register_uninstall_hook( __FILE__, array( 'Freelance_Manager', 'uninstall' ) );
	}



	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since 0.1
	 * @return Freelancer A single instance of this class.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * STUB - Performs these tasks when the plugin is activated.
	 *
	 * @since 0.1
	 * @todo update users that use site_admin role and set their role to editor 
	 *       - since site_admin role will no longer exist.
	 *
	 * @param  void
	 * @return void
	 */
	public static function activation() {}

	/**
	 * STUB - Performs these tasks when the plugin is uninstalled.
	 *
	 * @since 0.1
	 * @todo update users that use site_admin role and set their role to editor 
	 *       - since site_admin role will no longer exist.
	 *
	 * @return void
	 */
	public static function uninstall() {}

}

/**
 * Create class loader for Freelance Manager classes
 *
 * Make it possible for developers to lazy load classes without having to hardcode require statements.
 * Transforms the class name into a file path that gets require_once'd
 *
 * @since 0.1
 *
 * @param  string $class_name  the name of the class to load
 * @return void
 */
function fremgr_autoloader( $class_name ) {
    $slug = sanitize_title_with_dashes( $class_name, '', 'save' );
    $slug = str_replace('_', '-', $slug);

    $file = 'class-' . $slug . '.php';
    $file_path = plugin_dir_path( __FILE__ ) . 'classes/' . $file;

    if ( file_exists( $file_path ) ) {
        include_once $file_path;
    }

}

spl_autoload_register( 'fremgr_autoloader' );


$freelance_manager = Freelance_Manager::get_instance();
$client_admin = new Client_Admin();
$message_admin = new Message_Admin();


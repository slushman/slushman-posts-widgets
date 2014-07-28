<?php

/**
 * Slushman Posts Widgets: widgets that displays posts 
 *
 * Loosely based on the WordPress Plugin Boilerplate by Tom McFarlin
 * 
 * @package   Slushman Posts Widgets
 * @author    Slushman <chris@slushman.com>
 * @copyright Copyright (c) 2014, Slushman
 * @license   GPL-2.0+
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link      http://slushman.com/plugins/slushman-posts-widgets
 * @version   0.1
 * 
 * @wordpress-plugin
 * Plugin Name: 		Slushman Posts Widgets
 * Plugin URI: 			http://slushman.com/plugins/slushman-posts-widgets
 * Description: 		Widgets that displays posts
 * Version: 			0.1
 * Author: 				Slushman
 * Author URI: 			http://www.slushman.com
 * Text Domain:			slushman-posts-widgets
 * Domain Path:			/languages
 * Github Plugin URI: 	https://github.com/slushman/slushman-posts-widgets
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/**
 * Includes the plugin class file
 */
require_once( plugin_dir_path( __FILE__ ) . 'classes/class-slushman-post-widgets.php' );

/**
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 * Activations errors are saved as a plugin option
 */
register_activation_hook( __FILE__, array( 'Slushman_Posts_Widgets', 	'activate' ) );
register_deactivation_hook( __FILE__, array( 'Slushman_Posts_Widgets', 	'deactivate' ) );

/**
 * Loads the plugin instance when plugins are loaded
 */
add_action( 'plugins_loaded', array( 'Slushman_Posts_Widgets', 'get_instance' ) );

/**
 * Create a global variable for accessing the plugin options
 */
$slushman_post_widgets_settings = get_option( 'slushman_spw_options' );



/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/**
 * Includes the admin file and loads the instance of it when the plugins are loaded.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'classes/class-admin-spw.php' );

	add_action( 'plugins_loaded', array( 'Slushman_Posts_Widgets_Admin', 'get_instance' ) );

} // End of admin check



/*----------------------------------------------------------------------------*
 * Widgets
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'classes/class-widget-featured-posts.php' );
require_once( plugin_dir_path( __FILE__ ) . 'classes/class-widget-upcoming-posts.php' );

add_action( 'widgets_init', create_function( '', 'register_widget("slushman_featured_posts_widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("slushman_upcoming_posts_widget");' ) );

?>
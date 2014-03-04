<?php

/*
Plugin Name: Slushman Posts Widgets
Plugin URI: http://slushman.com/plugins/slushman-posts-widgets
Description: Widgets that displays posts
Version: 0.1
Author: Slushman
Author URI: http://www.slushman.com
License: GPLv2

**************************************************************************

  Copyright (C) 2013 Slushman

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General License for more details.

  You should have received a copy of the GNU General License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

**************************************************************************

*/

$widgets = array( 'upcoming-posts', 'featured-posts' );

foreach ( $widgets as $widget ) {

	require_once( plugin_dir_path( __FILE__ ) . '/inc/slushman-' . $widget . '-widget.php' );

} // End of $files foreach loop

$tools = array( 'slushman_toolkit', 'make_fields' );

foreach ( $tools as $tool ) {

	require_once( plugin_dir_path( __FILE__ ) . '/toolkit/' . $tool . '.php' );

} // End of $files foreach loop

if ( !class_exists( 'Slushman_Posts_Widgets' ) ) { //Start Class

	class Slushman_Posts_Widgets {
	
		public static $instance;

		private $make_fields;
		private $slushkit;
		private $options;

/**
 * Constructor
 */	
		function __construct() {
		
			self::$instance = $this;

			// Include the config file
            require_once( plugin_dir_path( __FILE__ ) . 'inc/config.php' );

			$sets_args = array( 'constants' => $this->constants, 'sections' => $this->sections, 'fields' => $this->fields );

			$this->make_fields 	= new Slushman_Toolkit_Make_Fields;
			$this->slushkit 	= new Slushman_Toolkit;
			
			// Runs when plugin is activated
			register_activation_hook( __FILE__, array( $this, 'install' ) );

			// Register and define the settings
			// add_action( 'admin_init', array( $this, 'settings_reg' ) );

			// Add menu
			// add_action( 'admin_menu', array( $this, 'add_menu' ) );
			
			//	Add "Settings" link to plugin page
			// add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ) , array( $this, 'settings_link' ) );
			
			add_action( 'activated_plugin', array( $this, 'save_error' ) );

			// Enqueue stylesheets
			add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_styles' ) );

			// Show IDs
			add_filter( 'manage_posts_columns', array( $this, 'custom_columns' ) );
			add_action( 'manage_posts_custom_column', array( $this, 'fill_column' ), 10, 2 );
			add_filter( 'manage_pages_columns', array( $this, 'custom_columns' ) );
			add_action( 'manage_pages_custom_column', array( $this, 'fill_column' ), 10, 2 );
			add_filter( 'manage_link_columns', array( $this, 'custom_link_columns' ) );
			add_action( 'manage_link_custom_column', array( $this, 'fill_column' ), 10, 2 );

			// Figure this out for later, not working properly
			// http://pippinsplugins.com/add-user-id-column-to-the-wordpress-users-table/
			// add_filter( 'manage_users_columns', array( $this, 'custom_user_columns' ) );
			// add_action( 'manage_users_custom_column', array( $this, 'fill_user_id_column' ), 10, 2 );

			$this->constants 	= $this->constants + array( 'file' => __FILE__ );
			$this->options 		= (array) get_option( $this->constants['name'] );

		} // End of __construct()

/**
 * Creates the plugin settings
 *
 * Creates an array containing each setting and sets the default values to blank.
 * Then saves the array in the plugin option.
 *
 * @since	0.1
 * 
 * @uses	settings_init
 */	
		function install() {

			$this->settings_init();

		} // End of install()



/* ==========================================================================
   Plugin Settings
   ========================================================================== */		

/**
 * Creates the plugin settings
 *
 * Creates an array containing each setting and sets the default values to blank.
 * Then saves the array in the plugin option.
 *
 * @since	0.1
 * 
 * @uses	update_option
 */		
		function settings_init() {

			$settings = array();

			foreach ( $this->fields as $field ) {

				$settings[$field['underscored']] = $field['value'];

			} // End of $fields foreach loop
		
			update_option( $this->constants['name'], $settings );
			
		} // End of settings_init()

/**
 * Registers the plugin option, settings, and sections
 *
 * Instead of writing the registration for each field, I used a foreach loop to write them all.
 * add_settings_field has an argument that can pass data to the callback, which I used to send the specifics
 * of each setting field to the callback that actually creates the setting field. 
 *
 * @since	0.1
 * 
 * @uses	register_setting
 * @uses	add_settings_section
 * @uses	add_settings_field
 */	
		function settings_reg() {

			$options = (array) get_option( $this->constants['name'] );

			register_setting( 
				$this->constants['name'], 
				$this->constants['name'],
				array( $this, 'validate_options' )
			);

			foreach ( $this->sections as $section ) {

				if ( isset( $section['desc'] ) && !empty( $section['desc'] ) ) {
	        
	                $section['desc'] 	= '<div class="inside">' . $section['desc'] . '</div>';
	                $callback 			= create_function( '', 'echo "' . str_replace( '"', '\"', $section['desc'] ) . '";' );
	        
	            } else {
	        
	                $callback = '__return_false';
	        
	            } // End of description check

				add_settings_section( 
					$this->constants['prefix'] . $section['underscored'], 
					$section['name'] . ' Settings', 
					$callback, 
					$this->constants['name']
				);

			} // End of $sections foreach loop
			
			foreach ( $this->fields as $field ) {

				$defaults 	= array( 'desc' => '', 'id' => '', 'type' => 'text', 'sels' => array(), 'size' => '' );
				$field 		= wp_parse_args( $field, $defaults );

				$corv 				= ( $field['type'] == 'checkbox' ? 'check' : 'value' );
				$dorl				= ( $field['type'] == 'checkbox' ? 'label' : 'desc' );
				$args[$corv] 		= $options[$field['underscored']];
				$args[$dorl] 		= $field['desc'];
				$args['blank']		= ( $field['type'] == 'select' ? TRUE : '' );
				$args['class']		= $this->constants['prefix'] . $field['underscored'];
				$args['id'] 		= $field['underscored'];
				$args['name'] 		= $this->constants['name'] . '[' . $field['underscored'] . ']';
				$args['selections']	= $field['sels'];
				$args['size']		= $field['size'];
				$args['type'] 		= $field['type'];
				
				add_settings_field(
					$this->constants['prefix'] . $field['underscored'] . '_field', 
					$field['name'], 
					array( $this, 'create_settings' ), 
					$this->constants['name'],
					$this->constants['prefix'] . $field['section'],
					$args
				);
				
			} // End of $fields foreach

		} // End of settings_reg()

/**
 * Creates the settings fields
 *
 * Accepts the $params from settings_reg() and creates a setting field
 *
 * @since	0.1
 *
 * @params	$params		The data specific to this setting field, comes from settings_reg()
 * 
 * @uses	checkbox
 */	
 		function create_settings( $params ) {

 			$defaults 	= array( 'blank' => '', 'check' => '', 'class' => '', 'desc' => '', 'id' => '', 'label' => '', 'name' => '', 'selections' => '', 'size' => '', 'type' => 'text', 'value' => '' );
 			$args 		= wp_parse_args( $params, $defaults );
 					
 			switch ( $args['type'] ) {
	 			
	 			case ( 'email' )		:
	 			case ( 'number' )		:
	 			case ( 'tel' ) 			: 
	 			case ( 'url' ) 			: 
	 			case ( 'text' ) 		: echo $this->make_fields->make_text( $args ); break;
	 			case ( 'checkbox' ) 	: echo $this->make_fields->make_checkbox( $args ); break;
	 			case ( 'textarea' )		: echo $this->make_fields->make_textarea( $args ); break;
	 			case ( 'checkboxes' ) 	: echo $this->make_fields->make_checkboxes( $args ); break;
	 			case ( 'radios' ) 		: echo $this->make_fields->make_radios( $args ); break;
	 			case ( 'select' )		: echo $this->make_fields->make_select( $args ); break;
	 			case ( 'file' )			: echo $this->make_fields->make_file( $args ); break;
	 			case ( 'password' )		: echo $this->make_fields->make_password( $args ); break;
	 			
 			} // End of $inputtype switch
			
		} // End of create_settings_fn()

/**
 * Validates the plugin settings before they are saved
 *
 * Loops through each plugin setting and sanitizes the data before returning it.
 *
 * @since	0.1
 *
 * @uses    sanitize_email
 * @uses    esc_url
 * @uses    sanitize_text_field
 * @uses    esc_textarea
 * @uses    sanitize_phone
 */				
		function validate_options( $input ) {

			foreach ( $this->fields as $field ) {

				$name = $field['underscored'];
			
				switch ( $field['type'] ) {
	 			
		 			case ( 'email' )		: $valid[$name] = sanitize_email( $input[$name] ); break;
		 			case ( 'number' )		: $valid[$name] = intval( $input[$name] ); break;
		 			case ( 'url' ) 			: $valid[$name] = esc_url( $input[$name] ); break;
		 			case ( 'text' ) 		: $valid[$name] = sanitize_text_field( $input[$name] ); break;
		 			case ( 'textarea' )		: $valid[$name] = esc_textarea( $input[$name] ); break;
		 			case ( 'checkgroup' ) 	: 
		 			case ( 'radios' ) 		: 
		 			case ( 'select' )		: $valid[$name] = strip_tags( $input[$name] ); break;
		 			case ( 'tel' ) 			: $valid[$name] = $this->slushkit->sanitize_phone( $input[$name] ); break;
		 			case ( 'checkbox' ) 	: $valid[$name] = ( isset( $input[$name] ) ? 1 : 0 ); break;
		 			
	 			} // End of $inputtype switch
			
			} // End of $checks foreach

			return $valid;
		
		} // End of validate_options()

/**
 * Creates the settings page
 *
 * @since	0.1
 *
 * @uses	get_plugin_data
 * @uses	plugins_url
 * @uses	settings_fields
 * @uses	do_settings_sections
 * @uses	submit_button
 */					
		function get_page() {

			global $slushkit;

			$plugin = get_plugin_data( $this->constants['file'] ); ?>
			<div class="wrap">
			<div class="icon32" style="background-image:url(<?php echo plugins_url( 'images/logo.png', $this->constants['file'] ); ?>); background-repeat:no-repeat;"></div>
			<h2><?php echo $plugin['Name']; ?></h2><?php
			//settings_errors();
			?><form method="post" action="options.php"><?php

				$error = get_option( 'plugin_error' );

				$slushkit->print_array( $error );
			
				settings_fields( $this->constants['name'] );
				do_settings_sections( $this->constants['name'] );
				echo '<br />'; 
				submit_button(); ?>
				
			</form>
			</div><?php

		} // End of get_page()

/**
 * Adds a link to the plugin settings page to the plugin's listing on the plugin page
 *
 * @since	0.1
 * 
 * @uses	admin_url
 */			
		function settings_link( $links ) {
		
			$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=' . $this->constants['name'] ), __( 'Settings' ) );
			
			array_unshift( $links, $settings_link );
			
			return $links;
			
		} // End of settings_link()

/**
 * Adds the plugin settings page to the appropriate admin menu
 *
 * @since	0.1
 * 
 * @uses	add_options_page
 */				
		function add_menu() {

			if ( $this->constants['menu'] == 'options' ) {

				add_options_page( 
					__( $this->constants['plug'] . ' Settings' ), 
					__( $this->constants['plug'] ), 
					'manage_options', 
					$this->constants['name'], 
					array( $this, 'get_page' ) 
				);

			} elseif ( $this->constants['menu'] == 'submenu' ) {

				add_submenu_page(
					'edit.php?post_type=' . $this->constants['cpt'],
					__( $this->constants['plug'] . ' Settings' ),
					__( 'Settings' ),
					'edit_posts',
					$this->constants['slug'] . '-settings',
					array( $this, 'get_page' )
				);

			} // End of menu check
		
		} // End of add_menu()

		function save_error() {
		    
		    update_option( 'plugin_error',  ob_get_contents() );
		
		} // End of save_error()



/* ==========================================================================
   Styles and Scripts
   ========================================================================== */		
		
/**
 * Registers all the styles and enqueues the public-facing style
 *
 * @uses	wp_register_style
 * @uses	plugins_url
 * @uses	wp_enqueue_style
 */
		function add_styles() {
			
			wp_register_style( $this->constants['slug'],  plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_style( $this->constants['slug'] );
			
		} // End of add_styles()		



/* ==========================================================================
   Columns
   ========================================================================== */

/**
 * Adds the ID column to the edit post or edit pages table.
 */
		function custom_columns( $defaults ) {

			$other['cb'] = $defaults['cb'];
			$other['id'] = __( 'ID' );

			unset( $defaults['cb'] );

			return array_merge( $other, $defaults );

		} // End of custom_columns()
       
/**
 * Echoes the ID of the post/page/link/user that is being iterated over.
 */
		function fill_column( $column_name, $id ) {
			
			if( 'id' == $column_name ) {
				echo $id;
			}

		} // End of fill_column()

/**
 * Adds the ID column to the edit links table.
 */          
		function custom_link_columns( $columns ) {

			$other['cb'] 	= $columns['cb'];
			$other['id'] 	= '<th>' . __('ID') . '</th>';

			return array_merge( $other, $columns );

		} // End of custom_link_columns()   

/**
 * Adds the ID column to the edit users table.
 */
		function custom_user_columns( $columns ) {

			$columns['user_id'] = '<th>' . __('User ID') . '</th>';

			return $columns;

		} // End of custom_link_columns()

		function fill_user_id_column( $column_name, $user_id ) {

		    $user = get_userdata( $user_id );

			if ( 'user_id' == $column_name ) {

				return $user_id;

			}

		    return $value;

		} 


/* ==========================================================================
   Plugin Functions
   ========================================================================== */

/**
 * Returns posts based on the params
 *
 * Three parameters are required:
 * 	Limit: how many posts to return
 * 	Order: basically, ascending or descending
 * 	Orderby: What the returned posts should be ordered by
 * 		Options: none, post ID, author, title, post slug, date, last modified date, 
 * 				post/page parent ID, random, comment count, and menu order (pages)
 * 
 * @param	array 	$params 	An array containing the params
 * 
 * @return	array	An array of posts (ids, associative array, or post objects)
 */
		function fetch_posts( $params ) {

			// This is required
			$args['posts_per_page'] = $params['limit'];

			// This is required
			$args['order'] = $params['order'];

			// This is required
			$args['orderby'] = $params['orderby'];

			// Uses the author id, separate with commas for multiple authors
			// Use a negative author ID to exclude that author's posts
			$args['author'] = ( isset( $params['authors'] ) ? $params['authors'] :  '' );

			// Uses the category id, separate with commas for multiple categories
			$args['cat'] = ( isset( $params['categories'] ) ? $params['categories'] :  '' );

			// Uses the category name, separate with commas for multiple categories
			$args['category_name'] = ( isset( $params['category_names'] ) ? $params['category_names'] :  '' );

			// Uses the tag names, separate with commas for multiple tags
			$args['tag'] = ( isset( $params['tags'] ) ? $params['tags'] :  '' );

			// Uses the tag IDs, separate with commas for multiple tags
			$args['tag_id'] = ( isset( $params['tag_IDs'] ) ? $params['tag_IDs'] :  '' );

			// Return options: ids, id=>parent, all, defaults to all if not set
			$args['fields'] = ( isset( $params['return'] ) ? $params['return'] : 'all' );

			$args['ignore_sticky_posts'] = ( isset( $params['stickies'] ) ? 0 : 1 );

			// At some point, come back and add in:
			// time and date
			// custom post types
			// taxonomies
			// meta keys, values, compares, etc

			// Post status options defaults to publish if not set
			// future, publish, pending, draft, auto-draft, private, inherit, trash, any
			$args['post_status'] = ( isset( $params['type'] ) ? $params['type'] : 'publish' );

			$posts = new WP_Query( $args );

			return $posts;

		} // End of fetch_posts()

/**
 * Gets the template file
 *
 * Looks in the following locations:
 *  Child theme directory
 *  Parent theme directory
 *  wp-content directory
 *  Default template directory within the plugin folder
 *
 * Idea from gigpress
 *
 * @since	0.1
 *
 * @param	array	$param		The name of the template
 *
 * @uses	file_exists
 * @uses	get_stylesheet_directory
 * @uses	get_template_directory
 * @uses	WP_CONTENT_DIR
 * @uses	plugin_dir_path
 *
 * @return	$path	string		The path to the template
 */
	function get_template( $template ) {
	
		$name 	= strtolower( $template );
		$prefix = '/spw-';
		$end	= 'templates/' . $name . '.php';
	
		if ( file_exists( get_stylesheet_directory() . $prefix . $end ) ) {
		
			$path = get_stylesheet_directory() . $prefix . $end;
		
		} elseif ( file_exists( get_template_directory() . $prefix . $end ) ) {
		
			$path = get_template_directory() . $prefix . $end;
		
		} elseif ( file_exists( WP_CONTENT_DIR . $prefix . $end ) ) {
		
			$path = WP_CONTENT_DIR . $prefix . $end;
		
		} else {
		
			$path = plugin_dir_path( __FILE__ ) . $end;
		
		} // End of file checks
		
		return $path;
		
	} // End of get_template()



				
	} // End of Slushman_Posts_Widgets class
	
} // End of class check

$slushman_posts_widgets = new Slushman_Posts_Widgets();

?>
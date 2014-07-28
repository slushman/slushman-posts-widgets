<?php

/**
 * Slushman Posts Widgets
 *
 * This is a class handling the admin-facing portions of the plugin.
 *
 * @package   Slushman Posts Widgets
 * @author    Slushman <chris@slushman.com>
 * @license   GPL-2.0+
 * @link      http://slushman.com/plugins/slushman-posts-widgets
 * @copyright Copyright (c) 2014, Slushman
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) { die; }

require_once( plugin_dir_path( __FILE__ ) . '../toolkit/make_admin.php' );

if ( !class_exists( 'Slushman_Posts_Widgets_Admin' ) ) {

	class Slushman_Posts_Widgets_Admin extends Slushman_Make_Admin {

/**
 * Instance of this class.
 *
 * @access 	protected
 * @since 	0.1
 * @var 	object
 */
		protected static $instance = null;

/**
 * Initialize the plugin by loading admin scripts & styles and adding a
 * settings page and menu.
 *
 * @access 	public
 * @since 	0.1
 * 
 * @uses 	check_required()
 * @uses 	set_columns()
 * @uses 	set_fields()
 * @uses 	set_groups()
 * @uses 	set_menu()
 * @uses 	set_sections()
 * @uses 	set_tabs()
 * @uses 	setup()
 *
 * @return 	void
 */
		public function __construct() {

			$this->add_actions();

			// Show IDs
			add_filter( 'manage_posts_columns', array( $this, 'custom_columns' ) );
			add_action( 'manage_posts_custom_column', array( $this, 'fill_column' ), 10, 2 );
			add_filter( 'manage_pages_columns', array( $this, 'custom_columns' ) );
			add_action( 'manage_pages_custom_column', array( $this, 'fill_column' ), 10, 2 );

		} // End of __construct()

/**
 * Return an instance of this class.
 *
 * @access 	public
 * @since 	0.1
 *
 * @return 	object 		A single instance of this class.
 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {

				self::$instance = new self;
			
			}

			return self::$instance;
		
		} // End of get_instance()



/* ==========================================================================
   Columns
   ========================================================================== */

/**
 * Adds the ID column to the edit post or edit pages table
 */
		function custom_columns( $defaults ) {

			$other['cb'] = $defaults['cb'];
			$other['id'] = __( 'ID' );

			unset( $defaults['cb'] );

			return array_merge( $other, $defaults );

		} // End of custom_columns()
       
/**
 * Echoes the ID of the post/page/link/user that is being iterated over
 */
		function fill_column( $column_name, $id ) {
			
			if( 'id' == $column_name ) {
				echo $id;
			}

		} // End of fill_column()



	} // End of class

} // End of class check

?>
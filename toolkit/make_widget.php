<?php 

require_once( plugin_dir_path( __FILE__ ) . '../toolkit/make_field.php' );
require_once( plugin_dir_path( __FILE__ ) . '../toolkit/make_sanitized.php' );

if ( !class_exists( 'Slushman_Make_Widget' ) ) {

	class Slushman_Make_Widget extends WP_Widget {

/**
 * An array of options for controlling the widget
 *
 * @access 	protected
 * @since 	0.1
 * @var 	array
 */
		protected $control = array();

/**
 * An array of option fields to process
 *
 * See make_field() class for field options
 *
 * @access 	protected
 * @since 	0.1
 * @var 	array
 */
		protected $fields = array();

/**
 * The internationalization domain
 *
 * @access 	protected
 * @since 	0.1
 * @var 	string
 */
		protected $i18n = '';

/**
 * The base id of the widget
 *
 * @access 	protected
 * @since 	0.1
 * @var 	string
 */
		public $id = '';

/**
 * The name of the widget
 *
 * @access 	protected
 * @since 	0.1
 * @var 	string
 */
		public $name = '';

/**
 * An array of options for the widget
 *
 * @access 	protected
 * @since 	0.1
 * @var 	array
 */
		protected $opts = array();

/**
 * Unique identifier for a widget.
 *
 * @access 	protected
 * @since 	0.1
 * @var      string
 */
		protected $widget_slug = '';
 
/**
 * Create the widget.
 *
 * @uses	parent::__construct
 */	 
		function __construct() {
        
			// Define in extension
     		
		} // End of __construct function

/**
 * The output of the front-end of the widget
 *
 * @access 	protected
 * @since 	0.1
 *
 * @param   array   $args           The widget arguments
 * @param   array   $instance       Previously saved values from database.
 */
		protected function widget_output( $args, $instance ) {

			// Define in extension

		} // End of widget_output()

/**
 * Flushes WordPress cache for this widget
 *
 * @access 	public
 * @since 	0.1
 * 
 * @uses    wp_cache_delete()
 * 
 * @return  void
 */
		public function flush_widget_cache() {

			wp_cache_delete( $this->get_widget_slug(), 'widget' );

		} // End of flush_widget_cache()
 
/**
 * Back-end widget form.
 *
 * @see     WP_Widget::form()
 *
 * @uses    wp_parse_args
 * @uses    esc_attr
 * @uses    get_field_id
 * @uses    get_field_name
 * @uses    checked
 *
 * @param   array   $instance   Previously saved values from database.
 */     
		public function form( $instance ) {

			foreach ( $this->fields as $field ) {

				$field['value'] 		= ( isset( $instance[$field['atts']['id']] ) ? $instance[$field['atts']['id']] : $field['value'] );
				$field['atts']['id'] 	= $this->get_field_id( $field['atts']['id'] );
				$field['atts']['name'] 	= $this->get_field_name( $field['atts']['name'] );

				echo '<p>';

				$this->make_field( $field );

				echo '</p>';

			} // End of $fields foreach

		} // End of form()
    
/**
 * Front-end display of widget.
 *
 * @see		WP_Widget::widget()
 *
 * @param	array	$args		Widget arguments.
 * @param 	array	$instance	Saved values from database.
 *
 * @uses	apply_filters
 */	 	 
		public function widget( $args, $instance ) {

			// Check if there is a cached output
			$cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

			if ( !is_array( $cache ) ) { 

				$cache = array(); 

			}

			if ( ! isset ( $args['widget_id'] ) ) { 

				$args['widget_id'] = $this->id;

			}

			if ( isset ( $cache[ $args['widget_id'] ] ) ) {

				return print $cache[ $args['widget_id'] ];

			}

			extract( $args, EXTR_SKIP );

			$widget_string = '';
			$widget_string .= $before_widget;
			
			ob_start();

			$title 			= ( empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] ) );
			$widget_string 	.= ( !empty( $title ) ? $before_title . $title . $after_title : '' );

			$this->widget_output( $args, $instance );

			$widget_string					.= ob_get_clean();
			$widget_string					.= $after_widget;
			$cache[ $args['widget_id'] ]	= $widget_string;

			wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );

			echo $widget_string;

		} // End of widget function

/**
 * Sanitize widget form values as they are saved.
 *
 * @see     WP_Widget::update()
 *
 * @param   array   $new_instance   Values just sent to be saved.
 * @param   array   $old_instance   Previously saved values from database.
 *
 * @return  array   $instance       Updated safe values to be saved.
 */     
		public function update( $new_instance, $old_instance ) {

			$instance = $old_instance;

			foreach ( $this->fields as $field ) {

				$instance[$field['atts']['name']] = $this->sanitize( $field['type'], $new_instance[$field['atts']['name']] );

			} // End of $fields foreach

			return $instance;

		} // End of update()



/* ==========================================================================
   Add Methods
   ========================================================================== */

/**
 * Add all the actions and filters to the WordPress actions
 *
 * @access 	protected
 * @since 	0.1
 *
 * @uses 	add_action()
 * 
 * @return 	string 		Plugin slug variable.
 */
		public function add_actions() {
			
			parent::__construct( $this->id, $this->name, $this->opts, $this->control );

			add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
			add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
			add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

		} // End of add_actions()




/* ==========================================================================
   Get Methods
   ========================================================================== */

/**
 * Return the widget slug.
 *
 * @since 		0.1
 *
 * @return 	string 		Plugin slug variable.
 */
		public function get_widget_slug() {
			
			return $this->widget_slug;

		} // End of get_widget_slug()



/* ==========================================================================
   Injection Containers

   These remove any dependency and coupling of the public functions from the
   other classes needed by this class.
   ========================================================================== */

/**
 * Returns an HTML form field from the Slushman_Toolkit_Make_Field class
 *
 * @access  public
 * @since   0.1
 * 
 * @param   array       $field      An array of args for the field
 *
 * @uses    Slushman_Toolkit_Make_Fields
 * @uses    create_field()
 * 
 * @return  mixed       a formatted HTML input field
 */
		public function make_field( $field ) {

			$make_field = new Slushman_Toolkit_Make_Field( $field );

			echo $make_field->create_field();

		} // End of make_field()
        
/**
 * Returns data sanitized by the Slushman_Make_Sanitized class
 *
 * @access  public
 * @since   0.1
 * 
 * @param   string      $type       The data type
 * @param   mixed       $data       The data to be sanitized
 *
 * @uses    Slushman_Make_Sanitized
 * @uses    clean()
 * 
 * @return  mixed       The sanitized data
 */
		public function sanitize( $type, $data ) {

			$sanitize   = new Slushman_Make_Sanitized( array( 'type' => $type, 'data' => $data ) );
			$return     = $sanitize->clean();

			unset( $sanitize );

			return $return;

		} // End of sanitize()



	} // End of class

} // End of class check
    
?>
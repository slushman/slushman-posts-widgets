<?php 

class slushman_upcoming_posts_widget extends WP_Widget {
 
/**
 * Create the widget.
 *
 * @uses	parent::__construct
 */	 
    function __construct() {
    
        $name 					= __( 'Slushman Upcoming Posts Widget' );
     	$opts['description'] 	= __( 'Displays upcoming posts.', 'slushman-upcoming-posts-widget' );
 		$opts['classname']		= 'slushman_upcoming_posts_widget';
 		$control				= array( 'width' => '', 'height' => '' );
		
 		parent::__construct( false, $name, $opts, $control );

        // Future i10n support
        // load_plugin_textdomain( PLUGIN_LOCALE, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
        
        $order_sels[] = array( 'label' => 'Ascending', 'value' => 'ASC' );
        $order_sels[] = array( 'label' => 'Descending', 'value' => 'DESC' );

        $orderby_sels[] = array( 'label' => 'No order', 'value' => 'none' );
        $orderby_sels[] = array( 'label' => 'Post ID', 'value' => 'ID' );
        $orderby_sels[] = array( 'label' => 'Author', 'value' => 'author' );
        $orderby_sels[] = array( 'label' => 'Title', 'value' => 'title' );
        $orderby_sels[] = array( 'label' => 'Post slug', 'value' => 'name' );
        $orderby_sels[] = array( 'label' => 'Date', 'value' => 'date' );
        $orderby_sels[] = array( 'label' => 'By last modified date', 'value' => 'modified' );
        $orderby_sels[] = array( 'label' => 'By post/page parent ID', 'value' => 'parent' );
        $orderby_sels[] = array( 'label' => 'Random', 'value' => 'rand' );
        $orderby_sels[] = array( 'label' => 'Number of comments', 'value' => 'comment_count' );
        $orderby_sels[] = array( 'label' => 'Page order', 'value' => 'menu_order' );
        //$orderby_sels[] = array( 'label' => 'Meta Value', 'value' => 'meta_value' );
        //$orderby_sels[] = array( 'label' => 'Meta value Number', 'value' => 'meta_value_num' );

        // Form fields
        // required: name, underscored, type, & value. optional: desc, sels, size
        $this->fields[] = array( 'name' => 'Title', 'underscored' => 'title', 'type' => 'text', 'value' => 'Upcoming Posts' );
        $this->fields[] = array( 'name' => 'Display how many posts?', 'underscored' => 'how_many', 'type' => 'number', 'value' => 5 );
        // $this->fields[] = array( 'name' => 'Display featured image', 'underscored' => 'featured_image', 'type' => 'checkbox', 'value' => 0 );
        $this->fields[] = array( 'name' => 'Post order', 'underscored' => 'post_order', 'type' => 'select', 'value' => 'ASC', 'sels' => $order_sels );
        $this->fields[] = array( 'name' => 'Posts ordered by', 'underscored' => 'post_orderby', 'type' => 'select', 'value' => 'none', 'sels' => $orderby_sels );
        //$this->fields[] = array( 'name' => 'Hide widget if empty', 'underscored' => 'hide_empty', 'type' => 'checkbox', 'value' => 0 );
 		
    } // End of __construct function

/**
 * The output of the front-end of the widget
 *
 * @param   array   $instance  Previously saved values from database.
 *
 * @uses    xprofile_get_field_data
 * @uses    oembed_transient
 * @uses    find_on_page
 */
    function widget_output( $args, $instance ) {

        global $slushman_posts_widgets, $slushkit;

        $post_args['limit']     = $instance['how_many'];
        $post_args['type']      = 'future';
        $post_args['order']     = $instance['post_order'];
        $post_args['orderby']   = $instance['post_orderby'];

        $posts = $slushman_posts_widgets->fetch_posts( $post_args );

        // $slushkit->print_array( $posts );

        foreach( $posts->posts as $post ) {

            //$slushkit->print_array( $post );

            include( $slushman_posts_widgets->get_template( 'upcoming-widget' ) );

        } // End of $posts foreach
        
    } // End of widget_output()
 
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
    function form( $instance ) {

        global $slushman_posts_widgets;
    
        foreach ( $this->fields as $field ) {

            $corv               = ( $field['type'] == 'checkbox' ? 'check' : 'value' );
            $args[$corv]        = ( isset( $instance[$field['underscored']] ) ? $instance[$field['underscored']] : $field['value'] );
            $args['blank']      = ( $field['type'] == 'select' ? TRUE : '' );
            $args['class']      = $field['underscored'] . ( $field['type'] == 'text' ? ' widefat' : '' );
            $args['desc']       = ( !empty( $field['desc'] ) ? $field['desc'] : '' );
            $args['id']         = $this->get_field_id( $field['underscored'] );
            $args['label']      = $field['name'];
            $args['name']       = $this->get_field_name( $field['underscored'] );
            $args['selections'] = ( !empty( $field['sels'] ) ? $field['sels'] : array() );
            $args['type']       = ( empty( $field['type'] ) ? '' : $field['type'] );
            
            echo '<p>' . $slushman_posts_widgets->create_settings( $args ) . '</p>';
            
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
   function widget( $args, $instance ) {

		extract( $args );
 	
	 	echo $before_widget;
	 	
	 	$title = ( !empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '' );
	 	
	 	echo ( !empty( $title ) ? $before_title . $title . $after_title : '' );
	 	
	 	echo '<div id="sidebar-me">';

        $this->widget_output( $args, $instance );
		
		echo '</div><!-- End of #sidebar-me -->';
 	
        echo $after_widget;

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
    function update( $new_instance, $old_instance ) {
        
        $instance = $old_instance;

        foreach ( $this->fields as $field ) {

            $name = $field['underscored'];
            
            switch ( $field['type'] ) {
            
                case ( 'email' )        : $instance[$name] = sanitize_email( $new_instance[$name] ); break;
                case ( 'number' )       : $instance[$name] = intval( $new_instance[$name] ); break;
                case ( 'url' )          : $instance[$name] = esc_url( $new_instance[$name] ); break;
                case ( 'text' )         : $instance[$name] = sanitize_text_field( $new_instance[$name] ); break;
                case ( 'textarea' )     : $instance[$name] = esc_textarea( $new_instance[$name] ); break;
                case ( 'checkgroup' )   : $instance[$name] = strip_tags( $new_instance[$name] ); break;
                case ( 'radios' )       : $instance[$name] = strip_tags( $new_instance[$name] ); break;
                case ( 'select' )       : $instance[$name] = strip_tags( $new_instance[$name] ); break;
                case ( 'tel' )          : $instance[$name] = $slushkit->sanitize_phone( $new_instance[$name] ); break;
                case ( 'checkbox' )     : $instance[$name] = ( isset( $new_instance[$name] ) ? 1 : 0 ); break;
                
            } // End of $inputtype switch

        } // End of $fields foreach
        
        return $instance;
        
    } // End of update()
    
} // End of class slushman_upcoming_posts_widget

// Register the widget if its selected
add_action( 'widgets_init', 'slushman_upcoming_posts_widget_init' );

/**
 * Initiates widgets based on the plugin options
 *
 * Registers the widget with WordPress
 *
 * @since	0.1
 * 
 * @uses	register_widget
 */		
function slushman_upcoming_posts_widget_init() {

	register_widget( 'slushman_upcoming_posts_widget' );

} // End of slushman_upcoming_posts_widget()
    
?>
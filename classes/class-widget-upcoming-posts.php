<?php 

require_once( plugin_dir_path( __FILE__ ) . '../toolkit/make_widget.php' );

class slushman_upcoming_posts_widget extends Slushman_Make_Widget {
 
/**
 * Create the widget.
 *
 * @uses	parent::__construct
 */	 
	function __construct() {
	    
		$plugin						= Slushman_Posts_Widgets::get_instance();
		$this->control				= array( 'width' => '', 'height' => '' );
		$this->i18n					= $plugin->get_i18n();
		$this->id					= 'slushman-upcoming-posts';
		$this->name					= __( 'Upcoming Posts', $this->i18n );
		$this->opts['description']	= __( 'Displays upcoming posts.', $this->i18n );
		$this->opts['classname']	= 'slushman_upcoming_posts';
        
		$order_sels[]		= array( 'label' => 'Ascending', 				'value' => 'ASC' );
		$order_sels[]		= array( 'label' => 'Descending', 				'value' => 'DESC' );
		
		$orderby_sels[]		= array( 'label' => 'No order', 				'value' => 'none' );
		$orderby_sels[]		= array( 'label' => 'Post ID', 					'value' => 'ID' );
		$orderby_sels[]		= array( 'label' => 'Author', 					'value' => 'author' );
		$orderby_sels[]		= array( 'label' => 'Title', 					'value' => 'title' );
		$orderby_sels[]		= array( 'label' => 'Post slug', 				'value' => 'name' );
		$orderby_sels[]		= array( 'label' => 'Date', 					'value' => 'date' );
		$orderby_sels[]		= array( 'label' => 'By last modified date', 	'value' => 'modified' );
		$orderby_sels[]		= array( 'label' => 'By post/page parent ID', 	'value' => 'parent' );
		$orderby_sels[]		= array( 'label' => 'Random', 					'value' => 'rand' );
		$orderby_sels[]		= array( 'label' => 'Number of comments', 		'value' => 'comment_count' );
		$orderby_sels[]		= array( 'label' => 'Page order', 				'value' => 'menu_order' );
		//$orderby_sels[]	= array( 'label' => 'Meta Value', 				'value' => 'meta_value' );
		//$orderby_sels[]	= array( 'label' => 'Meta value Number', 		'value' => 'meta_value_num' );

		// Form fields
		$i = 0;

		$this->fields[$i]['atts']		= array( 'class' => 'widefat', 'id' => 'title', 'name' => 'title' );
		$this->fields[$i]['label']		= __( 'Title', $this->i18n ) . ': ';
		$this->fields[$i]['type']		= 'text';
		$this->fields[$i]['value']		= __( 'Upcoming Posts', $this->i18n );
		$i++;
		
		$this->fields[$i]['atts']		= array( 'id' => 'how_many', 'max' => 10 ,'name' => 'how_many' );
		$this->fields[$i]['label']		= __( 'Number of posts to show', $this->i18n ) . ': ';
		$this->fields[$i]['type']		= 'number';
		$this->fields[$i]['value']		= 4;
		$i++;
		
		$this->fields[$i]['atts']		= array( 'id' => 'post_order', 'name' => 'post_order' );
		$this->fields[$i]['label']		= __( 'Post order', $this->i18n ) . ': ';
		$this->fields[$i]['selections']	= $order_sels;
		$this->fields[$i]['type']		= 'select';
		$this->fields[$i]['value']		= 'ASC';
		$i++;
		
		$this->fields[$i]['atts']		= array( 'id' => 'post_orderby', 'name' => 'post_orderby' );
		$this->fields[$i]['label']		= __( 'Post ordered by', $this->i18n ) . ': ';
		$this->fields[$i]['selections']	= $orderby_sels;
		$this->fields[$i]['type']		= 'select';
		$this->fields[$i]['value']		= 'none';
		$i++;
		
		// $this->fields[$i]['atts']	= array( 'id' => 'hide_empty', 'name' => 'hide_empty' );
		// $this->fields[$i]['label']	= __( 'Hide widget if empty', $this->i18n ) . ': ';
		// $this->fields[$i]['type']	= 'checkbox';
		// $this->fields[$i]['value']	= 0;
		// $i++;
		
		$this->add_actions();

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

		$plugin					= Slushman_Posts_Widgets::get_instance();
		$post_args['limit']		= $instance['how_many'];
		$post_args['type']		= 'future';
		$post_args['order']		= $instance['post_order'];
		$post_args['orderby']	= $instance['post_orderby'];
		$posts					= $plugin->fetch_posts( $post_args );

		//echo '<pre>'; print_r( $posts ); echo '</pre>';

		$class = $this->get_widget_class( $posts->post_count );

		?><div class="<?php echo $class; ?>"><?php

		foreach( $posts->posts as $post ) {

			$custom	= get_post_custom( $post->ID );

			include( $plugin->get_template( 'upcoming-widget' ) );

		} // End of $posts foreach

	} // End of widget_output()

/**
 * Returns a class name based on the post count
 * 
 * @param  	int 		$count 		The number of posts
 * @return 	string        			The class name
 */
	private function get_widget_class( $count ) {

		$class = '';

		if ( 1 === $count ) {

			$class = 'single';

		} elseif ( 2 === $count ) {

			$class = 'pair';

		} elseif ( $count > 2 ) {

			$class = 'set';

		}

		return $class;

	} // End of get_widget_class()


    
} // End of class slushman_upcoming_posts_widget

?>
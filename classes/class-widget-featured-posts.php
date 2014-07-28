<?php 

require_once( plugin_dir_path( __FILE__ ) . '../toolkit/make_widget.php' );

class slushman_featured_posts_widget extends Slushman_Make_Widget {
 
/**
 * Create the widget.
 *
 * @uses	parent::__construct
 */	 
	function __construct() {

		$plugin						= Slushman_Posts_Widgets::get_instance();
		$this->control				= array( 'width' => '', 'height' => '' );
		$this->i18n					= $plugin->get_i18n();
		$this->id					= 'slushman-featured-posts';
		$this->name					= __( 'Featured Posts' );
		$this->opts['description']	= __( 'Displays featured posts', $this->i18n );
		$this->opts['classname']	= 'slushman_featured_posts';

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
		$this->fields[$i]['value']		= __( 'Featured Posts', $this->i18n );
		$i++;
		
		$this->fields[$i]['atts']		= array( 'id' => 'which_posts', 'name' => 'which_posts' );
		$this->fields[$i]['desc']		= __( 'Please use post IDs', $this->i18n );
		$this->fields[$i]['label']		= __( 'Which posts?', $this->i18n ) . ': ';
		$this->fields[$i]['type']		= 'text';
		$this->fields[$i]['value']		= '';
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
 * @uses    check_post_count
 */
	function widget_output( $args, $instance ) {

		$plugin	= Slushman_Posts_Widgets::get_instance();
		$posts	= explode( ',', $instance['which_posts'] );

		?><div class="featured_posts"><?php

		foreach( $posts as $postID ) {

			$post	= get_post( $postID, OBJECT );
			$custom	= get_post_custom( $postID );

			include( $plugin->get_template( 'featured-widget' ) );

		} // End of $posts foreach

		?></div><?php

	} // End of widget_output()

/**
 * Checks the posts count. If less than or equal to four, it gets all the current published post's IDs,
 * makes them into an array, and returns them.
 *
 * If the post count is greater than four, returns the param $posts array
 * 
 * @param   array   $instance   The instance array
 *
 * @global  $slushman_posts_widgets
 * @global  $slushkit
 *
 * @uses    wp_count_posts
 * @uses    fetch_posts
 * 
 * @return  string   $posts      An string of post IDs
 */
	function check_post_count( $instance ) {

		$plugin		= Slushman_Post_Widgets::get_instance();
		$post_count	= wp_count_posts();
		$posts		= '';

		if ( ( $post_count->publish <= 4 && $post_count->publish > 0 ) || empty( $instance['which_posts'] ) ) {

			$fetch_args['type']     = 'publish';
			$fetch_args['limit']    = '4';
			$fetch_args['order']    = $instance['post_order'];
			$fetch_args['orderby']  = $instance['post_orderby'  ];

			$fetched_posts = $plugin->fetch_posts( $fetch_args );

			foreach ( $fetched_posts->posts as $post ) {

				$posts[] = $post->ID;

			} // End of $fietched_posts foreach loop

		} elseif ( !empty( $instance['which_posts'] ) ) {

			$posts = explode( ',', $instance['which_posts'] );

		} // End of post count check

		return implode( ',', $posts );

	} // End of check_post_count();
    
} // End of class
    
?>
<div class="upcoming_post">

	<div class="upcoming_image"><?php
		echo get_the_post_thumbnail( $post->ID, 'home_thumb', array( 'class' => 'upcoming_pic' ) );
	?></div><!-- End of .upcoming_image -->

	<div class="upcoming_words">
		<span class="upcoming_title"><?php echo $post->post_title; ?></span>   <span class="upcoming_date"> <?php echo get_the_date( 'm.d.y' ); ?></span>
	</div><!-- End of .upcoming_words -->

</div><!-- End of .upcoming_post -->
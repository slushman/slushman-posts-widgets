<div class="featured_post">

	<div class="featured_image">
		<a href="<?php echo $post->guid; ?>" class="featured_post_link"><?php
			echo get_the_post_thumbnail( $post->ID, 'widget_thumb', array( 'class' => 'featured_pic' ) );
		?></a>
	</div><!-- End of .upcoming_image -->

</div><!-- End of .upcoming_post -->
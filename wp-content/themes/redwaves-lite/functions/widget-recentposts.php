<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: RedWave Recent Posts
	Description: A widget that displays your recent posts.
	Version: 1.0

-----------------------------------------------------------------------------------*/


class redwaves_recent_posts_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'redwaves_recent_posts_widget',
			__('RedWaves: Recent Posts','redwaves-lite'),
			array( 'description' => __( 'Display the most recent posts from all categories','redwaves-lite' ) )
		);
	}

 	public function form( $instance ) {
		$defaults = array(
			'comment_num' => 1,
			'date' => 1,
			'show_thumb' => 1,
			'show_excerpt' => 0,
			'excerpt_length' => 10
		);
		$instance = wp_parse_args((array) $instance, $defaults);
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Recent Posts','redwaves-lite' );
		$qty = isset( $instance[ 'qty' ] ) ? esc_attr( $instance[ 'qty' ] ) : 5;
		$comment_num = isset( $instance[ 'comment_num' ] ) ? esc_attr( $instance[ 'comment_num' ] ) : 1;
		$show_excerpt = isset( $instance[ 'show_excerpt' ] ) ? esc_attr( $instance[ 'show_excerpt' ] ) : 1;
		$date = isset( $instance[ 'date' ] ) ? esc_attr( $instance[ 'date' ] ) : 1;
		$excerpt_length = isset( $instance[ 'excerpt_length' ] ) ? intval( $instance[ 'excerpt_length' ] ) : 10;
		$show_thumb = isset( $instance[ 'show_thumb' ] ) ? esc_attr( $instance[ 'show_thumb' ] ) : 1;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','redwaves-lite' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'qty' ); ?>"><?php _e( 'Number of Posts to show','redwaves-lite' ); ?></label> 
			<input id="<?php echo $this->get_field_id( 'qty' ); ?>" name="<?php echo $this->get_field_name( 'qty' ); ?>" type="number" min="2" max="10" step="1" value="<?php echo $qty; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id("show_thumb"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_thumb"); ?>" name="<?php echo $this->get_field_name("show_thumb"); ?>" value="1" <?php if (isset($instance['show_thumb'])) { checked( 1, $instance['show_thumb'], true ); } ?> />
				<?php _e( 'Show Thumbnails', 'redwaves-lite'); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id("date"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("date"); ?>" name="<?php echo $this->get_field_name("date"); ?>" value="1" <?php checked( 1, $instance['date'], true ); ?> />
				<?php _e( 'Show post date', 'redwaves-lite'); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id("comment_num"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("comment_num"); ?>" name="<?php echo $this->get_field_name("comment_num"); ?>" value="1" <?php checked( 1, $instance['comment_num'], true ); ?> />
				<?php _e( 'Show number of comments', 'redwaves-lite'); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id("show_excerpt"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_excerpt"); ?>" name="<?php echo $this->get_field_name("show_excerpt"); ?>" value="1" <?php checked( 1, $instance['show_excerpt'], true ); ?> />
				<?php _e( 'Show excerpt', 'redwaves-lite'); ?>
			</label>
		</p>
		
		<p>
	       <label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Excerpt Length:', 'redwaves-lite' ); ?>
	       <input id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="number" min="1" step="1" value="<?php echo $excerpt_length; ?>" />
	       </label>
       </p>
	   
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['qty'] = intval( $new_instance['qty'] );
		$instance['comment_num'] = intval( $new_instance['comment_num'] );
		$instance['date'] = intval( $new_instance['date'] );
		$instance['show_thumb'] = intval( $new_instance['show_thumb'] );
		$instance['show_excerpt'] = intval( $new_instance['show_excerpt'] );
		$instance['excerpt_length'] = intval( $new_instance['excerpt_length'] );
		return $instance;
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$comment_num = $instance['comment_num'];
		$date = $instance['date'];
		$qty = (int) $instance['qty'];
		$show_thumb = (int) $instance['show_thumb'];
		$show_excerpt = $instance['show_excerpt'];
		$excerpt_length = $instance['excerpt_length'];

		echo $before_widget;
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		echo self::get_cat_posts( $qty, $comment_num, $date, $show_thumb, $show_excerpt, $excerpt_length );
		echo $after_widget;
	}

	public function get_cat_posts( $qty, $comment_num, $date, $show_thumb, $show_excerpt, $excerpt_length ) {
		$posts = new WP_Query(
			"orderby=date&order=DESC&posts_per_page=". ($qty - 1)
		);

		echo '<div class="recent-posts-wrap"><ul>';
		
		while ( $posts->have_posts() ) { $posts->the_post(); ?>
			<?php echo '<li class="post-box horizontal-container">'; ?>
				<?php if ( $show_thumb == 1 ) : ?>
				<div class="widget-post-img">
					<a rel="nofollow" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
					<?php if ( has_post_thumbnail() ) {
					the_post_thumbnail('tiny'); ?>
					</a>
					<?php } else { ?>
					<a rel="nofollow" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/nothumb-80x80.png" width="80" height="80" alt="<?php the_title_attribute(); ?>" /></a>
					<?php } ?>
				</div>
				<?php endif; ?>				
					<div class="widget-post-data">
						<div class="widget-post-title">
							<a rel="nofollow" href="<?php the_permalink()?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
						</div>
						<?php if ( $date == 1 || $comment_num == 1 ) : ?>
						<div class="post-info widget-post-info">
							<?php if ( $date == 1 ) : 
							redwaves_posted();
							endif; ?>
							<?php if ( $comment_num == 1 ) : 
							redwaves_entry_comments();					
							endif; ?>                                                     
						</div><!--.post-info-->
						<?php endif; ?>
						<?php if ( $show_excerpt == 1 ) : ?>
						<div class="widget-post-excerpt">
							<?php echo redwaves_excerpt($excerpt_length); ?>
						</div>
						<?php endif; ?>
					</div>

			<?php echo '</li>'; ?>
		<?php }
		echo '</ul></div>'."\r\n";
	}
	
}
add_action( 'widgets_init', create_function( '', 'register_widget( "redwaves_recent_posts_widget" );' ) );
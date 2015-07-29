<?php
	/**
	* The template part for displaying results in search pages.
	*
	* Learn more: http://codex.wordpress.org/Template_Hierarchy
	*
	* @package redwaves-lite
	*/
?>

	<?php
		$display = get_theme_mod( 'display', 'excerpt_smallfeatured' );
		if ( $display && $display === 'excerpt_smallfeatured') { ?>
		<article id="post-<?php the_ID(); ?>" class="post-box small-post-box">  
		<div class="post-img small-post-img">
			<?php if ( has_post_thumbnail() ) : ?> 
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<?php the_post_thumbnail('smallfeatured'); ?>
			</a>
			<?php else : ?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<img width="298" height="248" src="<?php echo get_template_directory_uri() . '/images/nothumb-298x248.png'; ?>" class="attachment-featured wp-post-image" alt="<?php the_title_attribute(); ?>">
			</a>   
			<?php endif; ?>
		</div>
		<div class="post-data small-post-data">
			<div class="post-data-container">
				<header class="entry-header">
					<div class="entry-meta post-info">
						<?php the_title( sprintf( '<h2 class="entry-title post-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() )      ), '</a></h2>' ); ?>
						<?php //display meta info if enabled
							$archives_post_meta = get_theme_mod( 'archives_post_meta', '1' );	
							if ( $archives_post_meta ) { 
								redwaves_entry_category(); 
								redwaves_entry_author();
								redwaves_posted();
								redwaves_entry_comments();	   		   
							}?>              
					</div><!-- .entry-meta -->
				</header><!-- .entry-header -->
				<div class="entry-content post-excerpt">
					<?php
						/* translators: %s: Name of current post */
						the_excerpt();
					?>
				</div><!-- .entry-content -->
				<div class="readmore">
					<a href="<?php echo get_permalink(); ?>"><?php esc_attr_e('Read More', 'redwaves-lite'); ?></a>
				</div>
			</div><!-- .post-data-container -->
		</div><!-- .post-data -->
		<?php } else { ?>  
		<article id="post-<?php the_ID(); ?>" class="post-box big-post-box">
		<div class="post-data-container">
			<div class="single_featured">
				<?php if ( has_post_thumbnail() ) : ?> 
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
				<?php the_post_thumbnail('big'); ?>
				</a>
				<?php else : ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
				<img width="666" height="333" src="<?php echo get_template_directory_uri() . '/images/nothumb-666x333.png'; ?>" class="attachment-featured wp-post-image" alt="<?php the_title_attribute(); ?>">
				</a>   
				<?php endif; ?>
			</div>
			<header class="entry-header">
				<div class="entry-meta post-info">
					<?php the_title( sprintf( '<h2 class="entry-title post-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() )      ), '</a></h2>' ); ?>
					<?php //display meta info if enabled
							$archives_post_meta = get_theme_mod( 'archives_post_meta', '1' );	
							if ( $archives_post_meta && $archives_post_meta === '1' ) { 
							redwaves_entry_category(); 
							redwaves_entry_author();
							redwaves_posted();
							redwaves_entry_comments();	   		   
						}?>              
				</div><!-- .entry-meta -->
			</header><!-- .entry-header -->
			<div class="entry-content post-excerpt">
				<?php
					/* translators: %s: Name of current post */
					the_excerpt();
				?>
			</div><!-- .entry-content -->
			<div class="readmore">
                <a href="<?php echo get_permalink(); ?>"><?php esc_attr_e('Read More', 'redwaves-lite'); ?></a>
			</div>
		</div><!-- .post-data-container -->
		
	<?php } ?>
	
</article><!-- #post-## -->
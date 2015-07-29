<?php
	/**
	* The main template file.
	*
	* This is the most generic template file in a WordPress theme
	* and one of the two required files for a theme (the other being style.css).
	* It is used to display a page when nothing more specific matches a query.
	* E.g., it puts together the home page when no home.php file exists.
	* Learn more: http://codex.wordpress.org/Template_Hierarchy
	*
	* @package redwaves-lite
	*/
	
get_header(); ?>
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php if ( have_posts() ) : ?>
		<?php /* Start the Loop */ ?>
		<?php while ( have_posts() ) : the_post(); ?>
		<?php
			/* Include the Post-Format-specific template for the content.
				* If you want to override this in a child theme, then include a file
				* called content-___.php (where ___ is the Post Format name) and that will be used instead.
			*/
			get_template_part( 'content', get_post_format() );
		?>
		<?php endwhile; ?>
		<?php else : ?>
		<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>
		
		<?php // if WP version 4.1.0 or above use the_posts_pagination() built in function.
		if (4.1 <= floatval(get_bloginfo('version'))):?>                    
		<?php the_posts_pagination( array(
			'mid_size' => 2,
			'prev_text' => __( '&#8249; Previous', 'redwaves-lite' ),
			'next_text' => __( 'Next &#8250;', 'redwaves-lite' ),
		) ); ?>
		<?php else : ?>
		<?php redwaves_pagination(); ?>
		<?php endif; ?>
	</main><!-- #main -->
</div><!-- #primary -->
<?php //show sidebar only if enabled.
	$sidebar_settings = get_theme_mod( 'sidebar_settings', 'right_sidebar' );
	if ($sidebar_settings === 'right_sidebar' || $sidebar_settings === 'left_sidebar') {
		get_sidebar();
	} 
?>
<?php get_footer(); ?>	
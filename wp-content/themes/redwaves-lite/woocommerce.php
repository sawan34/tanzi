<?php
	/**
	* The template for displaying all pages.
	*
	* This is the template that displays all pages by default.
	* Please note that this is the WordPress construct of pages
	* and that other 'pages' on your WordPress site will use a
	* different template.
	*
	* @package redwaves-lite
	*/
	
get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		
		<?php woocommerce_content(); ?>
		
	</main><!-- #main -->
</div><!-- #primary -->

<?php //show sidebar only if enabled.
	$sidebar_settings = get_theme_mod( 'sidebar_settings', 'right_sidebar' );
	if ($sidebar_settings === 'right_sidebar' || $sidebar_settings === 'left_sidebar') {
		get_sidebar();
	} 
?>
<?php get_footer(); ?>
		
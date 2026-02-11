<?php
/**
 * Template Name: Homepage
 *
 * If the page is built with Elementor, renders Elementor content.
 * Otherwise, falls back to Customizer-driven template parts.
 *
 * @package LesnaMax
 */

get_header(); ?>

<main id="main-content" class="site-main">

	<?php
	if ( function_exists( 'lesnamax_is_elementor_page' ) && lesnamax_is_elementor_page() ) :
		// Elementor takes over — render its content
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
	else :
		// Default: Customizer-driven homepage sections
	?>

		<?php get_template_part( 'template-parts/hero-slider' ); ?>

		<div class="container">
			<?php get_template_part( 'template-parts/featured-products' ); ?>
		</div>

		<div class="container">
			<?php get_template_part( 'template-parts/promo-banners' ); ?>
		</div>

		<?php get_template_part( 'template-parts/category-links' ); ?>

		<div class="container">
			<?php get_template_part( 'template-parts/by-room' ); ?>
		</div>

		<div class="container">
			<?php get_template_part( 'template-parts/by-categories' ); ?>
		</div>

	<?php endif; ?>

</main>

<?php get_footer();

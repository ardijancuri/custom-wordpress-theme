<?php
/**
 * Template Name: Homepage
 *
 * Renders Customizer-driven template parts.
 * If built with Elementor, also renders Elementor content after the theme sections.
 *
 * @package LesnaMax
 */

get_header(); ?>

<main id="main-content" class="site-main">

	<?php get_template_part( 'template-parts/hero-slider' ); ?>

	<div class="container">
		<?php get_template_part( 'template-parts/featured-products' ); ?>
	</div>

	<div class="container">
		<?php get_template_part( 'template-parts/promo-banners' ); ?>
		<?php get_template_part( 'template-parts/category-links' ); ?>
	</div>

	<div class="container">
		<?php
		get_template_part( 'template-parts/product-slider', null, array(
			'title'         => get_theme_mod( 'lesnamax_slider_1_title', '' ),
			'category_slug' => get_theme_mod( 'lesnamax_slider_1_category', '' ),
		) );
		?>
	</div>

	<?php get_template_part( 'template-parts/newsletter-section' ); ?>

	<?php
	// Render Elementor content if the page was built with it
	if ( function_exists( 'lesnamax_is_elementor_page' ) && lesnamax_is_elementor_page() ) :
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
	endif;
	?>

</main>

<?php get_footer();

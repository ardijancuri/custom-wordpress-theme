<?php
/**
 * Template Name: Homepage
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
	</div>

	<?php get_template_part( 'template-parts/category-links' ); ?>

	<div class="container">
		<?php get_template_part( 'template-parts/by-room' ); ?>
	</div>

	<div class="container">
		<?php get_template_part( 'template-parts/by-categories' ); ?>
	</div>

</main>

<?php get_footer();

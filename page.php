<?php
/**
 * The template for displaying all pages.
 *
 * @package LesnaMax
 */

get_header(); ?>

<main id="main-content" class="site-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( lesnamax_is_elementor_page() ) : ?>
			<?php the_content(); ?>
		<?php else : ?>
			<div class="container">
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php if ( get_the_title() ) : ?>
						<h1 class="page-title"><?php the_title(); ?></h1>
					<?php endif; ?>
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</article>
			</div>
		<?php endif; ?>
	<?php endwhile; ?>
</main>

<?php get_footer();

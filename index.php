<?php
/**
 * The main template file.
 *
 * @package LesnaMax
 */

get_header(); ?>

<main id="main-content" class="site-main">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="posts-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<h2 class="entry-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>
						<div class="entry-excerpt">
							<?php the_excerpt(); ?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination( array(
				'prev_text' => lesnamax_icon( 'chevron-left' ) . esc_html__( 'Para', 'lesnamax' ),
				'next_text' => esc_html__( 'Tjetra', 'lesnamax' ) . lesnamax_icon( 'chevron-right' ),
			) ); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Asnje postim nuk u gjet.', 'lesnamax' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php get_footer();

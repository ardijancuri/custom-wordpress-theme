<?php
/**
 * 404 Page Template
 *
 * @package LesnaMax
 */

get_header(); ?>

<main id="main-content" class="site-main">
	<div class="container">
		<div class="page-404">
			<h1 class="page-404__title">404</h1>
			<p class="page-404__text"><?php esc_html_e( 'Faqja qe kërkoni nuk u gjet.', 'lesnamax' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
				<?php esc_html_e( 'Kthehu ne fillim', 'lesnamax' ); ?>
			</a>
		</div>
	</div>
</main>

<?php get_footer();

<?php
/**
 * Newsletter Section Template Part
 *
 * Full-width banner with background image, newsletter form, and social links.
 * Integrates with Mailchimp for WordPress (MC4WP).
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

$title    = get_theme_mod( 'lesnamax_newsletter_title', 'Newsletter' );
$subtitle = get_theme_mod( 'lesnamax_newsletter_subtitle', 'Merr ofertat më të mira direkt në emailin tënd.' );
$bg_image = get_theme_mod( 'lesnamax_newsletter_bg_image', '' );

$bg_style = $bg_image ? ' style="background-image: url(' . esc_url( $bg_image ) . ');"' : '';
?>
<section class="newsletter-section"<?php echo $bg_style; ?>>
	<div class="newsletter-section__overlay"></div>
	<div class="container">
		<div class="newsletter-section__inner">
			<div class="newsletter-section__content">
				<div class="newsletter-section__icon">
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
						<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
						<polyline points="22,6 12,13 2,6"></polyline>
					</svg>
				</div>
				<div class="newsletter-section__text">
					<?php if ( $title ) : ?>
						<h2 class="newsletter-section__title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<?php if ( $subtitle ) : ?>
						<p class="newsletter-section__subtitle"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<div class="newsletter-section__right">
				<div class="newsletter-section__form">
					<?php if ( shortcode_exists( 'mc4wp_form' ) ) : ?>
						<?php echo do_shortcode( '[mc4wp_form]' ); ?>
					<?php else : ?>
						<form class="newsletter-fallback-form" method="post" action="#">
							<div class="newsletter-form-row">
								<input type="email" name="email" placeholder="<?php esc_attr_e( 'Emaili juaj...', 'lesnamax' ); ?>" required>
								<button type="submit"><?php esc_html_e( 'Abonohu', 'lesnamax' ); ?></button>
							</div>
						</form>
					<?php endif; ?>
					<label class="newsletter-section__terms">
						<input type="checkbox" name="newsletter_terms">
						<?php esc_html_e( 'Pranoj kushtet e përdorimit', 'lesnamax' ); ?>
					</label>
				</div>

				<div class="newsletter-section__social">
					<a href="<?php echo esc_url( get_theme_mod( 'lesnamax_social_facebook', '#' ) ); ?>" class="newsletter-section__social-link" aria-label="Facebook" target="_blank" rel="noopener">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
					</a>
					<a href="<?php echo esc_url( get_theme_mod( 'lesnamax_social_instagram', '#' ) ); ?>" class="newsletter-section__social-link" aria-label="Instagram" target="_blank" rel="noopener">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>

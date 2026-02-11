<?php
/**
 * Promo Banners Template Part
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

$banner_1_image = get_theme_mod( 'lesnamax_promo_1_image' );
$banner_1_title = get_theme_mod( 'lesnamax_promo_1_title', 'ZBRIJE PRODUKTET E REJA' );
$banner_1_link  = get_theme_mod( 'lesnamax_promo_1_link', '#' );

$banner_2_image = get_theme_mod( 'lesnamax_promo_2_image' );
$banner_2_title = get_theme_mod( 'lesnamax_promo_2_title', 'KU ESHTE LESNAMAX' );
$banner_2_link  = get_theme_mod( 'lesnamax_promo_2_link', '#' );
?>
<section class="section">
	<div class="promo-banners">
		<a href="<?php echo esc_url( $banner_1_link ); ?>" class="promo-banner">
			<?php if ( $banner_1_image ) : ?>
				<img class="promo-banner__image" src="<?php echo esc_url( $banner_1_image ); ?>" alt="<?php echo esc_attr( $banner_1_title ); ?>" loading="lazy">
			<?php else : ?>
				<div class="promo-banner__image" style="background-color: var(--color-bg-light);"></div>
			<?php endif; ?>
			<div class="promo-banner__content">
				<h3 class="promo-banner__title"><?php echo esc_html( $banner_1_title ); ?></h3>
			</div>
		</a>

		<a href="<?php echo esc_url( $banner_2_link ); ?>" class="promo-banner">
			<?php if ( $banner_2_image ) : ?>
				<img class="promo-banner__image" src="<?php echo esc_url( $banner_2_image ); ?>" alt="<?php echo esc_attr( $banner_2_title ); ?>" loading="lazy">
			<?php else : ?>
				<div class="promo-banner__image" style="background-color: var(--color-bg-light);"></div>
			<?php endif; ?>
			<div class="promo-banner__content">
				<h3 class="promo-banner__title"><?php echo esc_html( $banner_2_title ); ?></h3>
			</div>
		</a>
	</div>
</section>

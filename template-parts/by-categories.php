<?php
/**
 * By Categories Carousel Template Part
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$categories = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'number'     => 12,
	'parent'     => 0,
	'exclude'    => array( get_option( 'default_product_cat' ) ),
) );

if ( empty( $categories ) || is_wp_error( $categories ) ) {
	return;
}
?>
<?php $by_categories_title = get_theme_mod( 'lesnamax_by_categories_title', 'By categories' ); ?>
<section class="section by-categories">
	<?php if ( $by_categories_title ) : ?>
		<h2 class="section-title"><?php echo esc_html( $by_categories_title ); ?></h2>
	<?php endif; ?>
	<div class="categories-carousel">
		<div class="categories-carousel__track">
			<?php foreach ( $categories as $category ) :
				$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
				$image_url    = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : wc_placeholder_img_src();
				$cat_link     = get_term_link( $category );
			?>
				<a href="<?php echo esc_url( $cat_link ); ?>" class="category-card">
					<img
						class="category-card__image"
						src="<?php echo esc_url( $image_url ); ?>"
						alt="<?php echo esc_attr( $category->name ); ?>"
						loading="lazy"
					>
					<span class="category-card__name"><?php echo esc_html( $category->name ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>

		<button class="carousel-arrow carousel-arrow--prev" aria-label="<?php esc_attr_e( 'Para', 'lesnamax' ); ?>">
			<?php lesnamax_icon( 'chevron-left' ); ?>
		</button>
		<button class="carousel-arrow carousel-arrow--next" aria-label="<?php esc_attr_e( 'Tjetra', 'lesnamax' ); ?>">
			<?php lesnamax_icon( 'chevron-right' ); ?>
		</button>
	</div>
</section>

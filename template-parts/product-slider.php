<?php
/**
 * Product Slider Template Part
 *
 * Reusable horizontal product carousel.
 * Accepts 'title' and 'category_slug' via $args.
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$slider_title   = ! empty( $args['title'] ) ? $args['title'] : '';
$category_slug  = ! empty( $args['category_slug'] ) ? $args['category_slug'] : '';

$query_args = array(
	'limit'   => 12,
	'status'  => 'publish',
	'orderby' => 'date',
	'order'   => 'DESC',
);

if ( $category_slug ) {
	$query_args['category'] = array( $category_slug );
}

$products = wc_get_products( $query_args );

if ( empty( $products ) ) {
	return;
}
?>
<section class="product-slider">
	<div class="product-slider__header">
		<?php if ( $slider_title ) : ?>
			<h2 class="section-title"><?php echo esc_html( $slider_title ); ?></h2>
		<?php endif; ?>
	</div>

	<div class="product-slider__carousel">
		<div class="product-slider__track">
			<?php foreach ( $products as $product_object ) :
				$product_id    = $product_object->get_id();
				$product_name  = $product_object->get_name();
				$product_link  = $product_object->get_permalink();
				$product_image = wp_get_attachment_image_src( $product_object->get_image_id(), 'woocommerce_thumbnail' );
				$image_url     = $product_image ? $product_image[0] : wc_placeholder_img_src();

				// Get product's primary category
				$terms = get_the_terms( $product_id, 'product_cat' );
				$cat_name = '';
				if ( $terms && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						if ( $term->parent !== 0 ) {
							$cat_name = $term->name;
							break;
						}
					}
					if ( ! $cat_name ) {
						$cat_name = $terms[0]->name;
					}
				}
			?>
				<a href="<?php echo esc_url( $product_link ); ?>" class="product-slider__card">
					<div class="product-slider__card-image-wrap">
						<img
							class="product-slider__card-image"
							src="<?php echo esc_url( $image_url ); ?>"
							alt="<?php echo esc_attr( $product_name ); ?>"
							loading="lazy"
						>
					</div>
					<?php if ( $cat_name ) : ?>
						<span class="product-slider__card-category"><?php echo esc_html( $cat_name ); ?></span>
					<?php endif; ?>
					<span class="product-slider__card-name"><?php echo esc_html( $product_name ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>

		<button class="product-slider__arrow product-slider__arrow--prev" aria-label="<?php esc_attr_e( 'Para', 'lesnamax' ); ?>">
			<?php lesnamax_icon( 'chevron-left' ); ?>
		</button>
		<button class="product-slider__arrow product-slider__arrow--next" aria-label="<?php esc_attr_e( 'Tjetra', 'lesnamax' ); ?>">
			<?php lesnamax_icon( 'chevron-right' ); ?>
		</button>
	</div>
</section>

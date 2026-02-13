<?php
/**
 * Product Card Template Part
 *
 * Used in product grids across the theme (homepage, shop, related products).
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
	return;
}

$product_id    = $product->get_id();
$product_link  = get_permalink( $product_id );
$product_title = $product->get_name();
$product_image = wp_get_attachment_image_src( $product->get_image_id(), 'lesnamax-product-card' );
$image_url     = $product_image ? $product_image[0] : wc_placeholder_img_src( 'lesnamax-product-card' );
?>
<div class="product-card" data-product-id="<?php echo esc_attr( $product_id ); ?>">
	<!-- Image -->
	<div class="product-card__image-wrap">
		<?php lesnamax_product_badge(); ?>
		<a href="<?php echo esc_url( $product_link ); ?>">
			<img
				class="product-card__image"
				src="<?php echo esc_url( $image_url ); ?>"
				alt="<?php echo esc_attr( $product_title ); ?>"
				loading="lazy"
				width="300"
				height="300"
			>
		</a>
	</div>

	<!-- Body -->
	<div class="product-card__body">
		<!-- Category -->
		<?php
		$product_cats = get_the_terms( $product_id, 'product_cat' );
		if ( $product_cats && ! is_wp_error( $product_cats ) ) :
			$primary_cat = $product_cats[0];
		?>
			<span class="product-card__category"><?php echo esc_html( $primary_cat->name ); ?></span>
		<?php endif; ?>

		<!-- Title -->
		<h3 class="product-card__title">
			<a href="<?php echo esc_url( $product_link ); ?>"><?php echo esc_html( $product_title ); ?></a>
		</h3>

		<!-- Color Swatches -->
		<?php lesnamax_color_swatches(); ?>

		<!-- Availability -->
		<?php lesnamax_product_availability(); ?>

		<!-- Price -->
		<div class="product-card__price">
			<?php if ( $product->is_on_sale() ) : ?>
				<span class="price-original"><?php echo wp_kses_post( wc_price( $product->get_regular_price() ) ); ?></span>
				<span class="price-current"><?php echo wp_kses_post( wc_price( $product->get_sale_price() ) ); ?></span>
			<?php else : ?>
				<span class="price-current"><?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?></span>
			<?php endif; ?>
		</div>

		<!-- Actions -->
		<div class="product-card__actions">
			<button
				class="product-card__wishlist"
				data-product-id="<?php echo esc_attr( $product_id ); ?>"
				aria-label="<?php esc_attr_e( 'Shto ne listen e deshirave', 'lesnamax' ); ?>"
			>
				<?php lesnamax_icon( 'heart' ); ?>
			</button>

			<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
				<button
					class="product-card__add-to-cart ajax-add-to-cart"
					data-product-id="<?php echo esc_attr( $product_id ); ?>"
					data-quantity="1"
					aria-label="<?php echo esc_attr( sprintf( __( 'Shto %s ne shporte', 'lesnamax' ), $product_title ) ); ?>"
				>
					<?php lesnamax_icon( 'cart' ); ?>
				</button>
			<?php else : ?>
				<a href="<?php echo esc_url( $product_link ); ?>" class="product-card__add-to-cart">
					<?php esc_html_e( 'Shiko', 'lesnamax' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>

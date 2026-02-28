<?php
/**
 * Featured Products with Category Tabs Template Part
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$shop_page_id = wc_get_page_id( 'shop' );
$shop_url     = $shop_page_id > 0 ? get_permalink( $shop_page_id ) : '';

if ( empty( $shop_url ) || home_url( '/' ) === $shop_url ) {
	$shop_url = home_url( '/shop/' );
}

// Get product categories with icons
$category_icons = array(
	'karrige'          => 'karrige.png',
	'kuzhina'          => 'kuzhina.png',
	'dysheke'          => 'dysheke.png',
	'ndricimi'         => 'ndricimi.png',
	'tavolina-ushqimi' => 'tavolina ushqimi.png',
	'zyre'             => 'zyre.png',
	'canta'            => 'canta.png',
);

$categories = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'parent'     => 0,
	'number'     => 7,
	'exclude'    => array( get_option( 'default_product_cat' ) ),
) );

// Featured products
$featured_products = wc_get_products( array(
	'limit'    => 8,
	'status'   => 'publish',
	'featured' => true,
	'orderby'  => 'date',
	'order'    => 'DESC',
) );

// Fallback: latest products if no featured
if ( empty( $featured_products ) ) {
	$featured_products = wc_get_products( array(
		'limit'   => 8,
		'status'  => 'publish',
		'orderby' => 'date',
		'order'   => 'DESC',
	) );
}
?>
<section class="section featured-products">
	<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
		<div class="category-tabs-wrapper">
			<div class="category-tabs">
				<button class="category-tab is-active" data-category="all" data-url="<?php echo esc_url( $shop_url ); ?>">
					<span class="category-tab__label"><?php esc_html_e( 'Të gjitha', 'lesnamax' ); ?></span>
				</button>
				<?php foreach ( $categories as $cat ) : ?>
					<button class="category-tab" data-category="<?php echo esc_attr( $cat->slug ); ?>" data-url="<?php echo esc_url( get_term_link( $cat ) ); ?>">
						<span class="category-tab__label"><?php echo esc_html( $cat->name ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="products-grid">
		<?php
		foreach ( $featured_products as $product_object ) {
			$GLOBALS['product'] = $product_object;
			setup_postdata( $product_object->get_id() );
			get_template_part( 'template-parts/product-card' );
		}
		wp_reset_postdata();
		?>
	</div>

	<div class="featured-products__cta">
		<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn--outline featured-products__view-all" data-shop-url="<?php echo esc_url( $shop_url ); ?>">
			<?php esc_html_e( 'Shiko Të Gjitha', 'lesnamax' ); ?>
		</a>
	</div>
</section>

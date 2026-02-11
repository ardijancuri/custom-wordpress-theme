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
		<div class="category-tabs">
			<button class="category-tab is-active" data-category="all">
				<span class="category-tab__label"><?php esc_html_e( 'Te gjitha', 'lesnamax' ); ?></span>
			</button>
			<?php foreach ( $categories as $cat ) :
				$slug     = $cat->slug;
				$icon_file = isset( $category_icons[ $slug ] ) ? $category_icons[ $slug ] : '';
			?>
				<button class="category-tab" data-category="<?php echo esc_attr( $slug ); ?>">
					<?php if ( $icon_file ) : ?>
						<img
							class="category-tab__icon"
							src="<?php echo esc_url( LESNAMAX_URI . '/assets/images/categories/' . $icon_file ); ?>"
							alt="<?php echo esc_attr( $cat->name ); ?>"
							loading="lazy"
						>
					<?php endif; ?>
					<span class="category-tab__label"><?php echo esc_html( $cat->name ); ?></span>
				</button>
			<?php endforeach; ?>
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
</section>

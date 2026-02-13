<?php
/**
 * WooCommerce Single Product Override
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<main id="main-content" class="site-main">
	<div class="container">
		<?php lesnamax_breadcrumbs(); ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php
			global $product;
			$product_id = $product->get_id();

			// Recently viewed tracking data
			$rv_image_url = wp_get_attachment_image_url( $product->get_image_id(), 'lesnamax-product-thumb' );
			if ( ! $rv_image_url ) {
				$rv_image_url = wc_placeholder_img_src( 'lesnamax-product-thumb' );
			}
			?>
			<div
				data-recently-viewed-product
				data-product-id="<?php echo esc_attr( $product_id ); ?>"
				data-product-name="<?php echo esc_attr( $product->get_name() ); ?>"
				data-product-price="<?php echo esc_attr( wp_strip_all_tags( wc_price( $product->get_price() ) ) ); ?>"
				data-product-image="<?php echo esc_url( $rv_image_url ); ?>"
				data-product-url="<?php echo esc_url( get_permalink( $product_id ) ); ?>"
				style="display:none;"
			></div>

			<div id="product-<?php echo esc_attr( $product_id ); ?>" <?php wc_product_class( '', $product ); ?>>

				<!-- Main Product Layout -->
				<div class="single-product-layout">

					<!-- Product Gallery -->
					<div class="product-gallery">
						<?php lesnamax_product_badge(); ?>
						<?php
						$attachment_ids = $product->get_gallery_image_ids();
						$main_image_id  = $product->get_image_id();
						$all_images     = array();

						if ( $main_image_id ) {
							$all_images[] = $main_image_id;
						}

						if ( ! empty( $attachment_ids ) ) {
							$all_images = array_merge( $all_images, $attachment_ids );
						}

						if ( ! empty( $all_images ) ) :
						?>
							<!-- Thumbnails -->
							<div class="product-gallery__thumbs">
								<?php foreach ( $all_images as $index => $img_id ) :
									$thumb_url = wp_get_attachment_image_url( $img_id, 'lesnamax-product-thumb' );
									$full_url  = wp_get_attachment_image_url( $img_id, 'lesnamax-product-single' );
								?>
									<div class="product-gallery__thumb <?php echo $index === 0 ? 'is-active' : ''; ?>" data-full="<?php echo esc_url( $full_url ); ?>">
										<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy">
									</div>
								<?php endforeach; ?>
							</div>

							<!-- Main Image -->
							<div class="product-gallery__main">
								<?php
								$main_url = wp_get_attachment_image_url( $all_images[0], 'lesnamax-product-single' );
								?>
								<img src="<?php echo esc_url( $main_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
							</div>
						<?php else : ?>
							<div class="product-gallery__main">
								<?php echo wc_placeholder_img( 'lesnamax-product-single' ); ?>
							</div>
						<?php endif; ?>
					</div>

					<!-- Product Details -->
					<div class="product-details">
						<?php
						$product_cats = get_the_terms( $product_id, 'product_cat' );
						if ( $product_cats && ! is_wp_error( $product_cats ) ) :
							$primary_cat = $product_cats[0];
						?>
							<a href="<?php echo esc_url( get_term_link( $primary_cat ) ); ?>" class="product-details__category">
								<?php echo esc_html( $primary_cat->name ); ?>
							</a>
						<?php endif; ?>
						<h1 class="product-details__title"><?php the_title(); ?></h1>

						<!-- Product Meta Table -->
						<div class="product-meta-table">
							<?php if ( $product->get_sku() ) : ?>
								<div class="product-meta-row">
									<span class="product-meta-label"><?php esc_html_e( 'SKU', 'lesnamax' ); ?></span>
									<span class="product-meta-value"><?php echo esc_html( $product->get_sku() ); ?></span>
								</div>
							<?php endif; ?>

							<?php
							// Custom attributes (Material, Color, etc.)
							$attributes = $product->get_attributes();
							foreach ( $attributes as $attr_key => $attribute ) :
								if ( $attribute->is_taxonomy() ) {
									$terms     = wp_get_post_terms( $product_id, $attribute->get_name(), array( 'fields' => 'names' ) );
									$attr_name = wc_attribute_label( $attribute->get_name() );
									$value     = implode( ', ', $terms );
								} else {
									$attr_name = $attribute->get_name();
									$value     = implode( ', ', $attribute->get_options() );
								}
							?>
								<div class="product-meta-row">
									<span class="product-meta-label"><?php echo esc_html( $attr_name ); ?></span>
									<span class="product-meta-value"><?php echo esc_html( $value ); ?></span>
								</div>
							<?php endforeach; ?>

							<?php if ( $product->get_stock_quantity() !== null ) : ?>
								<div class="product-meta-row">
									<span class="product-meta-label"><?php esc_html_e( 'Stoku', 'lesnamax' ); ?></span>
									<span class="product-meta-value"><?php echo esc_html( $product->get_stock_quantity() ); ?> <?php esc_html_e( 'ne stok', 'lesnamax' ); ?></span>
								</div>
							<?php endif; ?>
						</div>

						<!-- Availability -->
						<?php lesnamax_product_availability(); ?>

						<!-- Price -->
						<div class="product-price-large">
							<span class="price-label"><?php esc_html_e( 'Çmimi', 'lesnamax' ); ?></span>
							<?php if ( $product->is_on_sale() ) : ?>
								<del><?php echo wp_kses_post( wc_price( $product->get_regular_price() ) ); ?></del>
								<ins><?php echo wp_kses_post( wc_price( $product->get_sale_price() ) ); ?></ins>
							<?php else : ?>
								<?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?>
							<?php endif; ?>
						</div>

						<!-- Actions -->
						<div class="product-actions">
							<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
								<button
									class="btn btn--primary ajax-add-to-cart"
									data-product-id="<?php echo esc_attr( $product_id ); ?>"
									data-quantity="1"
								>
									<?php lesnamax_icon( 'cart' ); ?>
									<?php esc_html_e( 'Shto ne shporte', 'lesnamax' ); ?>
								</button>
							<?php else : ?>
								<span class="btn btn--outline"><?php esc_html_e( 'Nuk ka stok', 'lesnamax' ); ?></span>
							<?php endif; ?>

							<button
								class="product-card__wishlist"
								data-product-id="<?php echo esc_attr( $product_id ); ?>"
								aria-label="<?php esc_attr_e( 'Shto ne listen e deshirave', 'lesnamax' ); ?>"
							>
								<?php lesnamax_icon( 'heart' ); ?>
							</button>
						</div>
					</div>

				</div>

				<!-- Product Tabs Section -->
				<div class="product-tabs-section">
					<div class="product-tabs">
						<div class="product-tabs__nav">
							<button class="product-tab__trigger is-active" data-tab="description">
								<?php esc_html_e( 'Pershkrimi i plote', 'lesnamax' ); ?>
							</button>
							<button class="product-tab__trigger" data-tab="category-products">
								<?php esc_html_e( 'Tipat ne kete kategori', 'lesnamax' ); ?>
							</button>
						</div>

						<!-- Description Tab -->
						<div class="product-tab__panel is-active" data-tab="description">
							<div class="product-tab__content">
								<div class="product-description">
									<?php the_content(); ?>
								</div>
								<div class="tech-info">
									<h3 class="tech-info__title"><?php esc_html_e( 'INFORMACIONE TEKNIKE', 'lesnamax' ); ?></h3>
									<?php if ( $product->get_weight() ) : ?>
										<p><strong><?php esc_html_e( 'Pesha:', 'lesnamax' ); ?></strong> <?php echo esc_html( $product->get_weight() . ' ' . get_option( 'woocommerce_weight_unit' ) ); ?></p>
									<?php endif; ?>
									<?php if ( $product->get_dimensions( false ) ) :
										$dimensions = $product->get_dimensions( false );
									?>
										<?php if ( $dimensions['length'] ) : ?>
											<p><strong><?php esc_html_e( 'Gjatesia:', 'lesnamax' ); ?></strong> <?php echo esc_html( $dimensions['length'] . ' ' . get_option( 'woocommerce_dimension_unit' ) ); ?></p>
										<?php endif; ?>
										<?php if ( $dimensions['width'] ) : ?>
											<p><strong><?php esc_html_e( 'Gjeresia:', 'lesnamax' ); ?></strong> <?php echo esc_html( $dimensions['width'] . ' ' . get_option( 'woocommerce_dimension_unit' ) ); ?></p>
										<?php endif; ?>
										<?php if ( $dimensions['height'] ) : ?>
											<p><strong><?php esc_html_e( 'Lartesia:', 'lesnamax' ); ?></strong> <?php echo esc_html( $dimensions['height'] . ' ' . get_option( 'woocommerce_dimension_unit' ) ); ?></p>
										<?php endif; ?>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<!-- Category Products Tab -->
						<div class="product-tab__panel" data-tab="category-products">
							<?php
							$terms = get_the_terms( $product_id, 'product_cat' );
							if ( $terms && ! is_wp_error( $terms ) ) {
								$category  = $terms[0];
								$cat_products = wc_get_products( array(
									'limit'    => 4,
									'status'   => 'publish',
									'category' => array( $category->slug ),
									'exclude'  => array( $product_id ),
									'orderby'  => 'rand',
								) );

								if ( ! empty( $cat_products ) ) {
									echo '<div class="products-grid">';
									foreach ( $cat_products as $cat_product ) {
										$GLOBALS['product'] = $cat_product;
										get_template_part( 'template-parts/product-card' );
									}
									echo '</div>';
									wp_reset_postdata();
								}
							}
							?>
						</div>
					</div>
				</div>

				<!-- Related Products -->
				<div class="related-products">
					<h2 class="related-products__title"><?php esc_html_e( 'Produkte te ngjajshme', 'lesnamax' ); ?></h2>
					<?php
					$related_ids = wc_get_related_products( $product_id, 4 );
					if ( ! empty( $related_ids ) ) {
						echo '<div class="products-grid">';
						foreach ( $related_ids as $related_id ) {
							$GLOBALS['product'] = wc_get_product( $related_id );
							get_template_part( 'template-parts/product-card' );
						}
						echo '</div>';
						wp_reset_postdata();
					}
					?>
				</div>

				<!-- More from Category -->
				<?php
				$terms = get_the_terms( $product_id, 'product_cat' );
				if ( $terms && ! is_wp_error( $terms ) ) :
					$category  = $terms[0];
					$more_products = wc_get_products( array(
						'limit'    => 4,
						'status'   => 'publish',
						'category' => array( $category->slug ),
						'exclude'  => array_merge( array( $product_id ), $related_ids ?: array() ),
						'orderby'  => 'date',
						'order'    => 'DESC',
					) );

					if ( ! empty( $more_products ) ) :
				?>
					<div class="related-products">
						<h2 class="related-products__title"><?php esc_html_e( 'Tjera ne kete kategori', 'lesnamax' ); ?></h2>
						<div class="products-grid">
							<?php
							foreach ( $more_products as $more_product ) {
								$GLOBALS['product'] = $more_product;
								get_template_part( 'template-parts/product-card' );
							}
							wp_reset_postdata();
							?>
						</div>
					</div>
				<?php
					endif;
				endif;
				?>

			</div>

		<?php endwhile; ?>
	</div>
</main>

<?php get_footer();

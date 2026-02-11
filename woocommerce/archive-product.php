<?php
/**
 * WooCommerce Archive Product (Shop Page) Override
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<main id="main-content" class="site-main">
	<div class="container">
		<?php lesnamax_breadcrumbs(); ?>

		<div class="shop-header">
			<div class="shop-header__left">
				<?php if ( is_product_category() ) : ?>
					<h1 class="shop-header__title"><?php single_term_title(); ?></h1>
				<?php elseif ( is_search() ) : ?>
					<h1 class="shop-header__title"><?php printf( esc_html__( 'Rezultatet per: %s', 'lesnamax' ), get_search_query() ); ?></h1>
				<?php else : ?>
					<h1 class="shop-header__title"><?php woocommerce_page_title(); ?></h1>
				<?php endif; ?>
			</div>

			<div class="shop-header__right">
				<!-- View Toggle -->
				<div class="view-toggle">
					<span class="shop-controls__label"><?php esc_html_e( 'View', 'lesnamax' ); ?></span>
					<button class="view-toggle__btn is-active" data-view="grid" aria-label="<?php esc_attr_e( 'Pamja grid', 'lesnamax' ); ?>">
						<?php lesnamax_icon( 'grid' ); ?>
					</button>
					<button class="view-toggle__btn" data-view="list" aria-label="<?php esc_attr_e( 'Pamja liste', 'lesnamax' ); ?>">
						<?php lesnamax_icon( 'list' ); ?>
					</button>
				</div>
			</div>
		</div>

		<div class="shop-layout">
			<!-- Sidebar Filters -->
			<aside class="shop-sidebar" role="complementary">
				<?php get_template_part( 'template-parts/sidebar-filters' ); ?>
			</aside>

			<!-- Products Area -->
			<div class="shop-content">
				<!-- Shop Controls -->
				<div class="shop-controls">
					<div class="shop-controls__left">
						<label class="shop-controls__label" for="shop-sort">
							<?php esc_html_e( 'Sort', 'lesnamax' ); ?>
						</label>
						<?php
						$orderby_options = array(
							'menu_order' => esc_html__( 'By default', 'lesnamax' ),
							'date'       => esc_html__( 'Te rejat', 'lesnamax' ),
							'price'      => esc_html__( 'Cmimi: ulet ne larte', 'lesnamax' ),
							'price-desc' => esc_html__( 'Cmimi: larte ne ulet', 'lesnamax' ),
							'popularity' => esc_html__( 'Popullariteti', 'lesnamax' ),
							'rating'     => esc_html__( 'Vleresimi', 'lesnamax' ),
						);
						$current_orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'menu_order';
						?>
						<select class="form-select shop-sort-select" id="shop-sort">
							<?php foreach ( $orderby_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_orderby, $value ); ?>>
									<?php echo esc_html( $label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="shop-controls__right">
						<label class="shop-controls__label" for="shop-show">
							<?php esc_html_e( 'Show', 'lesnamax' ); ?>
						</label>
						<?php
						$per_page_options = array( 8, 12, 20, 25 );
						$current_per_page = isset( $_GET['per_page'] ) ? absint( $_GET['per_page'] ) : 12;
						?>
						<select class="form-select shop-perpage-select" id="shop-show">
							<?php foreach ( $per_page_options as $num ) : ?>
								<option value="<?php echo esc_attr( $num ); ?>" <?php selected( $current_per_page, $num ); ?>>
									<?php echo esc_html( $num ); ?>
								</option>
							<?php endforeach; ?>
						</select>

						<!-- Pagination info -->
						<?php
						$total = $wp_query->found_posts;
						$paged = max( 1, get_query_var( 'paged' ) );
						$start = ( $paged - 1 ) * $current_per_page + 1;
						$end   = min( $paged * $current_per_page, $total );
						?>
						<span class="shop-controls__label">
							<?php echo esc_html( $start ); ?>-<?php echo esc_html( $end ); ?>
						</span>
					</div>
				</div>

				<!-- Product Grid -->
				<div class="shop-products products-grid view-grid"
					data-category="<?php echo is_product_category() ? esc_attr( get_queried_object()->slug ) : ''; ?>">
					<?php
					if ( woocommerce_product_loop() ) {
						while ( have_posts() ) {
							the_post();
							wc_get_template_part( 'content', 'product' );
						}
					} else {
						echo '<p class="no-products">' . esc_html__( 'Asnje produkt nuk u gjet.', 'lesnamax' ) . '</p>';
					}
					?>
				</div>

				<!-- Pagination -->
				<div class="shop-pagination">
					<?php
					the_posts_pagination( array(
						'prev_text' => '&laquo;',
						'next_text' => '&raquo;',
					) );
					?>
				</div>
			</div>
		</div>
	</div>
</main>

<?php get_footer();

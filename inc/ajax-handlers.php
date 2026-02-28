<?php
/**
 * AJAX Handlers
 *
 * Handles AJAX requests for add-to-cart, search, and product filtering.
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

/**
 * AJAX Add to Cart.
 */
function lesnamax_ajax_add_to_cart() {
	check_ajax_referer( 'lesnamax_ajax_nonce', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity   = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;

	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => __( 'Produkt i pavlefshem.', 'lesnamax' ) ) );
	}

	$product = wc_get_product( $product_id );

	if ( ! $product ) {
		wp_send_json_error( array( 'message' => __( 'Produkti nuk u gjet.', 'lesnamax' ) ) );
	}

	$added = WC()->cart->add_to_cart( $product_id, $quantity );

	if ( $added ) {
		wp_send_json_success( array(
			'message'    => sprintf( __( '%s u shtua ne shporte.', 'lesnamax' ), $product->get_name() ),
			'cart_count' => WC()->cart->get_cart_contents_count(),
			'cart_total' => WC()->cart->get_cart_total(),
		) );
	} else {
		wp_send_json_error( array( 'message' => __( 'Nuk mund te shtohet ne shporte.', 'lesnamax' ) ) );
	}
}
add_action( 'wp_ajax_lesnamax_add_to_cart', 'lesnamax_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_lesnamax_add_to_cart', 'lesnamax_ajax_add_to_cart' );

/**
 * AJAX Product Search.
 */
function lesnamax_ajax_search_products() {
	check_ajax_referer( 'lesnamax_search_nonce', 'nonce' );

	$query = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';

	if ( empty( $query ) || strlen( $query ) < 3 ) {
		wp_send_json_error( array( 'message' => __( 'Kërko me se paku 3 shkronja.', 'lesnamax' ) ) );
	}

	$products = wc_get_products( array(
		'limit'  => 6,
		'status' => 'publish',
		's'      => $query,
	) );

	if ( empty( $products ) ) {
		wp_send_json_success( array(
			'html'  => '',
			'count' => 0,
		) );
	}

	$search_url = add_query_arg( array(
		's'         => $query,
		'post_type' => 'product',
	), home_url( '/' ) );

	ob_start();
	?>
	<div class="search-results-header">
		<span class="search-results-header__title"><?php printf( esc_html__( 'Produktet (%d)', 'lesnamax' ), count( $products ) ); ?></span>
		<a href="<?php echo esc_url( $search_url ); ?>" class="search-results-header__link"><?php esc_html_e( 'Shfaq të gjitha rezultatet', 'lesnamax' ); ?></a>
	</div>
	<?php
	foreach ( $products as $product ) {
		$image_url = wp_get_attachment_image_url( $product->get_image_id(), 'lesnamax-product-thumb' );
		if ( ! $image_url ) {
			$image_url = wc_placeholder_img_src( 'lesnamax-product-thumb' );
		}
		?>
		<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" class="search-result-item">
			<img class="search-result-item__image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
			<div class="search-result-item__info">
				<span class="search-result-item__name"><?php echo esc_html( $product->get_name() ); ?></span>
				<span class="search-result-item__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
			</div>
		</a>
		<?php
	}
	$html = ob_get_clean();

	wp_send_json_success( array(
		'html'  => $html,
		'count' => count( $products ),
	) );
}
add_action( 'wp_ajax_lesnamax_search_products', 'lesnamax_ajax_search_products' );
add_action( 'wp_ajax_nopriv_lesnamax_search_products', 'lesnamax_ajax_search_products' );

/**
 * AJAX Product Filtering.
 */
function lesnamax_ajax_filter_products() {
	check_ajax_referer( 'lesnamax_filter_nonce', 'nonce' );

	$filters = isset( $_POST['filters'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['filters'] ) ), true ) : array();

	if ( ! is_array( $filters ) ) {
		$filters = array();
	}

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => isset( $filters['per_page'] ) ? absint( $filters['per_page'] ) : 12,
		'paged'          => isset( $filters['page'] ) ? absint( $filters['page'] ) : 1,
	);

	// Orderby
	if ( isset( $filters['orderby'] ) ) {
		switch ( $filters['orderby'] ) {
			case 'date':
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
			case 'price':
				$args['meta_key'] = '_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				break;
			case 'price-desc':
				$args['meta_key'] = '_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'popularity':
				$args['meta_key'] = 'total_sales';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			default:
				$args['orderby']  = 'menu_order title';
				$args['order']    = 'ASC';
				break;
		}
	}

	// Tax query
	$tax_query = array( 'relation' => 'AND' );

	// Category filter
	if ( ! empty( $filters['category'] ) ) {
		$categories = is_array( $filters['category'] ) ? $filters['category'] : array( $filters['category'] );
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array_map( 'sanitize_text_field', $categories ),
		);
	}

	// Attribute filters
	$attribute_taxonomies = array( 'pa_model', 'pa_ngjyra' );
	foreach ( $attribute_taxonomies as $taxonomy ) {
		if ( ! empty( $filters[ $taxonomy ] ) ) {
			$terms = is_array( $filters[ $taxonomy ] ) ? $filters[ $taxonomy ] : array( $filters[ $taxonomy ] );
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_text_field', $terms ),
			);
		}
	}

	// Flags filter
	if ( ! empty( $filters['flags'] ) ) {
		$flag_terms = is_array( $filters['flags'] ) ? $filters['flags'] : array( $filters['flags'] );
		$tax_query[] = array(
			'taxonomy' => 'product_tag',
			'field'    => 'slug',
			'terms'    => array_map( 'sanitize_text_field', $flag_terms ),
		);
	}

	if ( count( $tax_query ) > 1 ) {
		$args['tax_query'] = $tax_query;
	}

	// Price filter
	if ( ! empty( $filters['price_min'] ) || ! empty( $filters['price_max'] ) ) {
		$meta_query = array( 'relation' => 'AND' );

		if ( ! empty( $filters['price_min'] ) ) {
			$meta_query[] = array(
				'key'     => '_price',
				'value'   => absint( $filters['price_min'] ),
				'compare' => '>=',
				'type'    => 'NUMERIC',
			);
		}

		if ( ! empty( $filters['price_max'] ) ) {
			$meta_query[] = array(
				'key'     => '_price',
				'value'   => absint( $filters['price_max'] ),
				'compare' => '<=',
				'type'    => 'NUMERIC',
			);
		}

		$args['meta_query'] = $meta_query;
	}

	// Sale filter
	if ( isset( $filters['flags'] ) && in_array( 'sale', $filters['flags'], true ) ) {
		$args['post__in'] = wc_get_product_ids_on_sale();
	}

	$query = new WP_Query( $args );

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			global $product;
			$product = wc_get_product( get_the_ID() );
			get_template_part( 'template-parts/product-card' );
		}
	} else {
		echo '<p class="no-products">' . esc_html__( 'Asnje produkt nuk u gjet.', 'lesnamax' ) . '</p>';
	}
	$html = ob_get_clean();

	// Pagination
	$base_url = '';
	if ( ! empty( $filters['current_url'] ) ) {
		$base_url = esc_url_raw( $filters['current_url'] );
	}
	if ( empty( $base_url ) ) {
		$base_url = wp_get_referer();
	}
	if ( empty( $base_url ) ) {
		$base_url = wc_get_page_permalink( 'shop' );
	}

	$base_url        = remove_query_arg( array( 'paged', 'product-page' ), $base_url );
	$base_separator  = false === strpos( $base_url, '?' ) ? '?' : '&';
	$pagination_base = $base_url . $base_separator . 'paged=%#%';

	$pagination = paginate_links( array(
		'base'      => $pagination_base,
		'total'     => $query->max_num_pages,
		'current'   => $args['paged'],
		'format'    => '',
		'type'      => 'plain',
		'prev_text' => lesnamax_get_icon( 'chevron-left' ),
		'next_text' => lesnamax_get_icon( 'chevron-right' ),
	) );

	wp_reset_postdata();

	wp_send_json_success( array(
		'html'       => $html,
		'count'      => $query->found_posts,
		'pagination' => $pagination,
	) );
}
add_action( 'wp_ajax_lesnamax_filter_products', 'lesnamax_ajax_filter_products' );
add_action( 'wp_ajax_nopriv_lesnamax_filter_products', 'lesnamax_ajax_filter_products' );

/**
 * AJAX Get Cart Drawer Contents.
 */
function lesnamax_ajax_get_cart_drawer() {
	check_ajax_referer( 'lesnamax_ajax_nonce', 'nonce' );

	ob_start();
	lesnamax_render_cart_drawer_items();
	$body_html = ob_get_clean();

	ob_start();
	lesnamax_render_cart_drawer_footer();
	$footer_html = ob_get_clean();

	wp_send_json_success( array(
		'body'       => $body_html,
		'footer'     => $footer_html,
		'cart_count' => WC()->cart->get_cart_contents_count(),
	) );
}
add_action( 'wp_ajax_lesnamax_get_cart_drawer', 'lesnamax_ajax_get_cart_drawer' );
add_action( 'wp_ajax_nopriv_lesnamax_get_cart_drawer', 'lesnamax_ajax_get_cart_drawer' );

/**
 * Render cart drawer items HTML.
 */
function lesnamax_render_cart_drawer_items() {
	$cart_items = WC()->cart->get_cart();

	if ( empty( $cart_items ) ) {
		echo '<div class="flyout-drawer__empty">';
		echo '<p>' . esc_html__( 'Shporta juaj është bosh.', 'lesnamax' ) . '</p>';
		echo '<a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '" class="btn btn--primary">' . esc_html__( 'Bli Tani', 'lesnamax' ) . '</a>';
		echo '</div>';
		return;
	}

	foreach ( $cart_items as $cart_item_key => $cart_item ) {
		$product   = $cart_item['data'];
		$quantity  = $cart_item['quantity'];
		$image_url = wp_get_attachment_image_url( $product->get_image_id(), 'lesnamax-product-thumb' );
		if ( ! $image_url ) {
			$image_url = wc_placeholder_img_src( 'lesnamax-product-thumb' );
		}
		$product_price = WC()->cart->get_product_price( $product );
		$product_name  = $product->get_name();
		$product_link  = get_permalink( $product->get_id() );
		?>
		<div class="flyout-item" data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>">
			<a href="<?php echo esc_url( $product_link ); ?>" class="flyout-item__image">
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product_name ); ?>">
			</a>
			<div class="flyout-item__details">
				<a href="<?php echo esc_url( $product_link ); ?>" class="flyout-item__name"><?php echo esc_html( $product_name ); ?></a>
				<div class="flyout-item__price"><?php echo wp_kses_post( $product_price ); ?></div>
				<div class="flyout-item__quantity">
					<button type="button" class="flyout-qty-btn flyout-qty-btn--minus" data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>">-</button>
					<span class="flyout-qty-value"><?php echo esc_html( $quantity ); ?></span>
					<button type="button" class="flyout-qty-btn flyout-qty-btn--plus" data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>">+</button>
				</div>
			</div>
			<button type="button" class="flyout-item__remove" data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="<?php esc_attr_e( 'Hiq', 'lesnamax' ); ?>">
				&times;
			</button>
		</div>
		<?php
	}
}

/**
 * Render cart drawer footer HTML.
 */
function lesnamax_render_cart_drawer_footer() {
	$cart = WC()->cart;
	if ( $cart->is_empty() ) {
		return;
	}

	// Similar products slider
	lesnamax_render_cart_drawer_recommendations();
	?>
	<div class="flyout-drawer__total">
		<span><?php esc_html_e( 'Nëntotali', 'lesnamax' ); ?></span>
		<span class="flyout-drawer__total-amount"><?php echo wp_kses_post( $cart->get_cart_subtotal() ); ?></span>
	</div>
	<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn--outline btn--block"><?php esc_html_e( 'Shiko Shportën', 'lesnamax' ); ?></a>
	<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn--primary btn--block"><?php esc_html_e( 'Vazhdo me Pagesën', 'lesnamax' ); ?></a>
	<?php
}

/**
 * Render cart drawer product recommendations.
 */
function lesnamax_render_cart_drawer_recommendations() {
	$cart      = WC()->cart;
	$cart_ids  = array();

	foreach ( $cart->get_cart() as $item ) {
		$cart_ids[] = $item['product_id'];
	}

	// Try cross-sells first
	$product_ids = $cart->get_cross_sells();

	// Fallback: get related products from cart items
	if ( empty( $product_ids ) && ! empty( $cart_ids ) ) {
		$product_ids = wc_get_related_products( $cart_ids[0], 8, $cart_ids );
	}

	// Fallback: popular products
	if ( empty( $product_ids ) ) {
		$popular = wc_get_products( array(
			'limit'    => 8,
			'status'   => 'publish',
			'orderby'  => 'popularity',
			'order'    => 'DESC',
			'exclude'  => $cart_ids,
			'return'   => 'ids',
		) );
		$product_ids = $popular;
	}

	// Exclude products already in cart
	$product_ids = array_diff( $product_ids, $cart_ids );
	$product_ids = array_slice( $product_ids, 0, 8 );

	if ( empty( $product_ids ) ) {
		return;
	}
	?>
	<div class="flyout-recs">
		<div class="flyout-recs__header">
			<span class="flyout-recs__title"><?php esc_html_e( 'Mund të ju pëlqejë', 'lesnamax' ); ?></span>
			<div class="flyout-recs__arrows">
				<button type="button" class="flyout-recs__arrow flyout-recs__arrow--prev" aria-label="<?php esc_attr_e( 'Mbrapa', 'lesnamax' ); ?>">
					<?php lesnamax_icon( 'chevron-left' ); ?>
				</button>
				<button type="button" class="flyout-recs__arrow flyout-recs__arrow--next" aria-label="<?php esc_attr_e( 'Para', 'lesnamax' ); ?>">
					<?php lesnamax_icon( 'chevron-right' ); ?>
				</button>
			</div>
		</div>
		<div class="flyout-recs__track">
			<?php foreach ( $product_ids as $pid ) :
				$product = wc_get_product( $pid );
				if ( ! $product || ! $product->is_visible() ) {
					continue;
				}
				$image_url = wp_get_attachment_image_url( $product->get_image_id(), 'lesnamax-product-thumb' );
				if ( ! $image_url ) {
					$image_url = wc_placeholder_img_src( 'lesnamax-product-thumb' );
				}
			?>
				<div class="flyout-rec-card">
					<a href="<?php echo esc_url( get_permalink( $pid ) ); ?>" class="flyout-rec-card__image">
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
					</a>
					<div class="flyout-rec-card__info">
						<a href="<?php echo esc_url( get_permalink( $pid ) ); ?>" class="flyout-rec-card__name"><?php echo esc_html( $product->get_name() ); ?></a>
						<span class="flyout-rec-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
					</div>
					<?php if ( $product->is_in_stock() && $product->is_type( 'simple' ) ) : ?>
						<button type="button" class="flyout-rec-card__add product-card__add-to-cart" data-product-id="<?php echo esc_attr( $pid ); ?>" aria-label="<?php esc_attr_e( 'Shto në shportë', 'lesnamax' ); ?>">+</button>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * AJAX Update Cart Item Quantity.
 */
function lesnamax_ajax_update_cart_item() {
	check_ajax_referer( 'lesnamax_ajax_nonce', 'nonce' );

	$cart_key = isset( $_POST['cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_key'] ) ) : '';
	$quantity = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 0;

	if ( empty( $cart_key ) ) {
		wp_send_json_error( array( 'message' => __( 'Artikull i pavlefshem.', 'lesnamax' ) ) );
	}

	if ( $quantity === 0 ) {
		WC()->cart->remove_cart_item( $cart_key );
	} else {
		WC()->cart->set_quantity( $cart_key, $quantity );
	}

	ob_start();
	lesnamax_render_cart_drawer_items();
	$body_html = ob_get_clean();

	ob_start();
	lesnamax_render_cart_drawer_footer();
	$footer_html = ob_get_clean();

	wp_send_json_success( array(
		'body'       => $body_html,
		'footer'     => $footer_html,
		'cart_count' => WC()->cart->get_cart_contents_count(),
	) );
}
add_action( 'wp_ajax_lesnamax_update_cart_item', 'lesnamax_ajax_update_cart_item' );
add_action( 'wp_ajax_nopriv_lesnamax_update_cart_item', 'lesnamax_ajax_update_cart_item' );

/**
 * AJAX Get Wishlist Products.
 */
function lesnamax_ajax_get_wishlist_products() {
	check_ajax_referer( 'lesnamax_ajax_nonce', 'nonce' );

	$product_ids = isset( $_POST['product_ids'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['product_ids'] ) ), true ) : array();

	if ( ! is_array( $product_ids ) || empty( $product_ids ) ) {
		wp_send_json_success( array(
			'html' => '<div class="flyout-drawer__empty"><p>' . esc_html__( 'Lista e Dëshirave është bosh.', 'lesnamax' ) . '</p></div>',
		) );
	}

	$product_ids = array_map( 'absint', $product_ids );

	ob_start();
	foreach ( $product_ids as $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! $product || ! $product->is_visible() ) {
			continue;
		}

		$image_url = wp_get_attachment_image_url( $product->get_image_id(), 'lesnamax-product-thumb' );
		if ( ! $image_url ) {
			$image_url = wc_placeholder_img_src( 'lesnamax-product-thumb' );
		}
		$product_link = get_permalink( $product_id );
		?>
		<div class="flyout-item" data-product-id="<?php echo esc_attr( $product_id ); ?>">
			<a href="<?php echo esc_url( $product_link ); ?>" class="flyout-item__image">
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
			</a>
			<div class="flyout-item__details">
				<a href="<?php echo esc_url( $product_link ); ?>" class="flyout-item__name"><?php echo esc_html( $product->get_name() ); ?></a>
				<div class="flyout-item__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
				<?php if ( $product->is_in_stock() && $product->is_type( 'simple' ) ) : ?>
					<button type="button" class="btn btn--sm btn--primary flyout-wishlist-add-to-cart" data-product-id="<?php echo esc_attr( $product_id ); ?>">
						<?php esc_html_e( 'Shto ne Shporte', 'lesnamax' ); ?>
					</button>
				<?php endif; ?>
			</div>
			<button type="button" class="flyout-item__remove flyout-wishlist-remove" data-product-id="<?php echo esc_attr( $product_id ); ?>" aria-label="<?php esc_attr_e( 'Hiq', 'lesnamax' ); ?>">
				&times;
			</button>
		</div>
		<?php
	}
	$html = ob_get_clean();

	if ( empty( trim( $html ) ) ) {
		$html = '<div class="flyout-drawer__empty"><p>' . esc_html__( 'Lista e Dëshirave është bosh.', 'lesnamax' ) . '</p></div>';
	}

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_lesnamax_get_wishlist_products', 'lesnamax_ajax_get_wishlist_products' );
add_action( 'wp_ajax_nopriv_lesnamax_get_wishlist_products', 'lesnamax_ajax_get_wishlist_products' );

/**
 * AJAX Homepage Tab Products.
 */
function lesnamax_ajax_homepage_tab_products() {
	check_ajax_referer( 'lesnamax_ajax_nonce', 'nonce' );

	$category = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : 'all';

	if ( 'all' === $category ) {
		$products = wc_get_products( array(
			'limit'    => 8,
			'status'   => 'publish',
			'featured' => true,
			'orderby'  => 'date',
			'order'    => 'DESC',
		) );

		if ( count( $products ) < 8 ) {
			$exclude_ids = array();
			foreach ( $products as $featured_product ) {
				if ( $featured_product instanceof WC_Product ) {
					$exclude_ids[] = $featured_product->get_id();
				}
			}

			$extra_products = wc_get_products( array(
				'limit'   => 8 - count( $products ),
				'status'  => 'publish',
				'exclude' => $exclude_ids,
				'orderby' => 'date',
				'order'   => 'DESC',
			) );

			if ( ! empty( $extra_products ) ) {
				$products = array_merge( $products, $extra_products );
			}
		}
	} else {
		$products = wc_get_products( array(
			'limit'    => 8,
			'status'   => 'publish',
			'category' => array( $category ),
			'orderby'  => 'date',
			'order'    => 'DESC',
		) );
	}

	ob_start();
	if ( ! empty( $products ) ) {
		foreach ( $products as $product_object ) {
			$GLOBALS['product'] = $product_object;
			get_template_part( 'template-parts/product-card' );
		}
		wp_reset_postdata();
	} else {
		echo '<p class="no-products">' . esc_html__( 'Asnje produkt nuk u gjet.', 'lesnamax' ) . '</p>';
	}
	$html = ob_get_clean();

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_lesnamax_homepage_tab_products', 'lesnamax_ajax_homepage_tab_products' );
add_action( 'wp_ajax_nopriv_lesnamax_homepage_tab_products', 'lesnamax_ajax_homepage_tab_products' );

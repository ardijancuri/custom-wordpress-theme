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
		wp_send_json_error( array( 'message' => __( 'Kerko me se paku 3 shkronja.', 'lesnamax' ) ) );
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

	ob_start();
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
	$pagination = paginate_links( array(
		'total'   => $query->max_num_pages,
		'current' => $args['paged'],
		'format'  => '?paged=%#%',
		'type'    => 'plain',
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

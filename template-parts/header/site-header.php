<?php
/**
 * Site Header Template Part
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="header-main">
	<div class="container flex flex-center flex-between">
		<!-- Logo -->
		<div class="site-logo">
			<?php lesnamax_site_logo(); ?>
		</div>

		<!-- Search -->
		<div class="header-search">
			<form class="header-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input
					type="search"
					class="header-search__input"
					placeholder="<?php esc_attr_e( 'Kerko produkt?', 'lesnamax' ); ?>"
					value="<?php echo get_search_query(); ?>"
					name="s"
					autocomplete="off"
				>
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<input type="hidden" name="post_type" value="product">
				<?php endif; ?>
				<button type="submit" class="header-search__btn" aria-label="<?php esc_attr_e( 'Kerko', 'lesnamax' ); ?>">
					<?php lesnamax_icon( 'search' ); ?>
				</button>
			</form>
			<div class="search-results" id="search-results" aria-live="polite"></div>
		</div>

		<!-- Header Icons -->
		<div class="header-icons">
			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<!-- Account -->
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="header-icon-link" aria-label="<?php esc_attr_e( 'Llogaria ime', 'lesnamax' ); ?>">
					<?php lesnamax_icon( 'account' ); ?>
				</a>

				<!-- Catalog -->
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="header-icon-link" aria-label="<?php esc_attr_e( 'Katalogu', 'lesnamax' ); ?>">
					<?php lesnamax_icon( 'catalog' ); ?>
				</a>

				<!-- Wishlist -->
				<button type="button" class="header-icon-link header-wishlist" id="header-wishlist" aria-label="<?php esc_attr_e( 'Lista e deshirave', 'lesnamax' ); ?>">
					<?php lesnamax_icon( 'heart' ); ?>
					<span class="header-icon-count wishlist-count" data-count="<?php echo esc_attr( lesnamax_wishlist_count() ); ?>"><?php echo esc_html( lesnamax_wishlist_count() ); ?></span>
				</button>

				<!-- Cart -->
				<button type="button" class="header-icon-link header-cart" id="header-cart" aria-label="<?php esc_attr_e( 'Shporta', 'lesnamax' ); ?>">
					<?php lesnamax_icon( 'cart' ); ?>
					<span class="header-icon-count cart-count"><?php echo esc_html( lesnamax_cart_count() ); ?></span>
				</button>
			<?php endif; ?>

			<!-- Mobile Menu Toggle -->
			<button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="<?php esc_attr_e( 'Menu', 'lesnamax' ); ?>" aria-expanded="false">
				<?php lesnamax_icon( 'menu' ); ?>
			</button>
		</div>
	</div>
</div>

<?php
/**
 * Custom Search Form
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="search-form-input"><?php esc_html_e( 'Kërko', 'lesnamax' ); ?></label>
	<div class="header-search__form">
		<input
			type="search"
			id="search-form-input"
			class="header-search__input"
			placeholder="<?php esc_attr_e( 'Kërko produkt?', 'lesnamax' ); ?>"
			value="<?php echo get_search_query(); ?>"
			name="s"
		>
		<?php if ( class_exists( 'WooCommerce' ) ) : ?>
			<input type="hidden" name="post_type" value="product">
		<?php endif; ?>
		<button type="submit" class="header-search__btn" aria-label="<?php esc_attr_e( 'Kërko', 'lesnamax' ); ?>">
			<?php lesnamax_icon( 'search' ); ?>
		</button>
	</div>
</form>

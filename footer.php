<?php
/**
 * The footer template.
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;
?>

	<?php get_template_part( 'template-parts/footer/site-footer' ); ?>

</div><!-- #page -->

<?php if ( class_exists( 'WooCommerce' ) ) : ?>
<!-- Cart Drawer -->
<div class="flyout-overlay" id="cart-drawer-overlay">
	<div class="flyout-drawer flyout-drawer--right" id="cart-drawer" role="dialog" aria-label="<?php esc_attr_e( 'Shporta', 'lesnamax' ); ?>">
		<div class="flyout-drawer__header">
			<h3><?php esc_html_e( 'Shporta', 'lesnamax' ); ?></h3>
			<button type="button" class="flyout-drawer__close" aria-label="<?php esc_attr_e( 'Mbyll', 'lesnamax' ); ?>">
				<?php lesnamax_icon( 'close' ); ?>
			</button>
		</div>
		<div class="flyout-drawer__body" id="cart-drawer-body">
			<?php lesnamax_render_cart_drawer_items(); ?>
		</div>
		<div class="flyout-drawer__footer" id="cart-drawer-footer">
			<?php lesnamax_render_cart_drawer_footer(); ?>
		</div>
	</div>
</div>

<!-- Wishlist Drawer -->
<div class="flyout-overlay" id="wishlist-drawer-overlay">
	<div class="flyout-drawer flyout-drawer--right" id="wishlist-drawer" role="dialog" aria-label="<?php esc_attr_e( 'Lista e Dëshirave', 'lesnamax' ); ?>">
		<div class="flyout-drawer__header">
			<h3><?php esc_html_e( 'Lista e Dëshirave', 'lesnamax' ); ?></h3>
			<button type="button" class="flyout-drawer__close" aria-label="<?php esc_attr_e( 'Mbyll', 'lesnamax' ); ?>">
				<?php lesnamax_icon( 'close' ); ?>
			</button>
		</div>
		<div class="flyout-drawer__body" id="wishlist-drawer-body">
			<div class="flyout-drawer__empty">
				<p><?php esc_html_e( 'Lista e Dëshirave eshte bosh.', 'lesnamax' ); ?></p>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>

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

<!-- Cart Notification -->
<div class="cart-notification" id="cart-notification" role="alert" aria-live="polite">
	<div class="cart-notification__message"></div>
	<a href="<?php echo class_exists( 'WooCommerce' ) ? esc_url( wc_get_cart_url() ) : '#'; ?>" class="cart-notification__link">
		<?php esc_html_e( 'Shiko shporten', 'lesnamax' ); ?>
	</a>
</div>

<?php wp_footer(); ?>
</body>
</html>

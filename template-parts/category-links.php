<?php
/**
 * Category Quick Links Template Part
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

$links = array(
	array(
		'label' => get_theme_mod( 'lesnamax_catlink_1_label', 'OUTLET' ),
		'url'   => get_theme_mod( 'lesnamax_catlink_1_url', '#' ),
	),
	array(
		'label' => get_theme_mod( 'lesnamax_catlink_2_label', 'MOBILJE' ),
		'url'   => get_theme_mod( 'lesnamax_catlink_2_url', '#' ),
	),
	array(
		'label' => get_theme_mod( 'lesnamax_catlink_3_label', 'FLETUSHKA' ),
		'url'   => get_theme_mod( 'lesnamax_catlink_3_url', '#' ),
	),
);

$has_links = false;
foreach ( $links as $link ) {
	if ( ! empty( $link['label'] ) ) {
		$has_links = true;
		break;
	}
}

if ( ! $has_links ) {
	return;
}
?>
<div class="category-links">
	<?php foreach ( $links as $link ) : ?>
		<?php if ( ! empty( $link['label'] ) ) : ?>
			<a href="<?php echo esc_url( $link['url'] ); ?>" class="category-link">
				<?php echo esc_html( $link['label'] ); ?>
			</a>
		<?php endif; ?>
	<?php endforeach; ?>
</div>

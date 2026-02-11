<?php
/**
 * Announcement Bar Template Part
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

$announcement_text = get_theme_mod( 'lesnamax_announcement_text', 'Zbritje ne produkte online.' );
$show_announcement = get_theme_mod( 'lesnamax_announcement_show', true );

if ( ! $show_announcement || empty( $announcement_text ) ) {
	return;
}
?>
<div id="announcement-bar" class="announcement-bar" role="complementary" aria-label="<?php esc_attr_e( 'Njoftim', 'lesnamax' ); ?>">
	<div class="container">
		<span class="announcement-bar__text"><?php echo esc_html( $announcement_text ); ?></span>
	</div>
	<button class="announcement-bar__close" aria-label="<?php esc_attr_e( 'Mbyll njoftimin', 'lesnamax' ); ?>">
		<?php lesnamax_icon( 'close' ); ?>
	</button>
</div>

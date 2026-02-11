<?php
/**
 * Sidebar Template
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'shop-sidebar' ) ) {
	return;
}
?>
<aside id="sidebar" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'shop-sidebar' ); ?>
</aside>

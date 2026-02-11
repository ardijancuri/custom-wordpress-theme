<?php
/**
 * Navigation Template Part
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;
?>
<nav class="primary-nav" id="primary-nav" role="navigation" aria-label="<?php esc_attr_e( 'Navigimi kryesor', 'lesnamax' ); ?>">
	<div class="container">
		<div class="primary-nav__inner">
			<!-- Categories Dropdown Trigger -->
			<button class="nav-categories-trigger" id="nav-categories-trigger" aria-expanded="false">
				<?php lesnamax_icon( 'grid' ); ?>
				<span><?php esc_html_e( 'KATEGORITË', 'lesnamax' ); ?></span>
				<?php lesnamax_icon( 'chevron-down' ); ?>
			</button>

			<!-- Main Menu -->
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-menu',
					'menu_id'        => 'primary-menu',
					'depth'          => 2,
					'walker'         => new LesnaMax_Mega_Menu_Walker(),
					'fallback_cb'    => false,
				) );
			}
			?>
		</div>
	</div>
</nav>

<!-- Mobile Navigation Overlay -->
<div class="mobile-nav-overlay" id="mobile-nav-overlay">
	<div class="mobile-nav" id="mobile-nav">
		<div class="mobile-nav__header">
			<div class="site-logo">
				<?php lesnamax_site_logo(); ?>
			</div>
			<button class="mobile-nav__close" id="mobile-nav-close" aria-label="<?php esc_attr_e( 'Mbyll menune', 'lesnamax' ); ?>">
				<?php lesnamax_icon( 'close' ); ?>
			</button>
		</div>
		<div class="mobile-nav__content">
			<?php
			if ( has_nav_menu( 'mobile' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'mobile',
					'container'      => false,
					'menu_class'     => 'mobile-menu',
					'depth'          => 2,
					'fallback_cb'    => false,
				) );
			} elseif ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'mobile-menu',
					'depth'          => 2,
					'fallback_cb'    => false,
				) );
			}
			?>
		</div>
	</div>
</div>

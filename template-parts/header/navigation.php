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

			<!-- Categories Dropdown Panel -->
			<?php if ( class_exists( 'WooCommerce' ) ) :
				$mega_categories = get_terms( array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
					'parent'     => 0,
					'exclude'    => array( get_option( 'default_product_cat' ) ),
				) );
				if ( ! empty( $mega_categories ) && ! is_wp_error( $mega_categories ) ) :
			?>
			<div class="categories-dropdown-panel" id="categories-dropdown-panel">
				<div class="container">
					<div class="categories-mega-grid">
						<?php foreach ( $mega_categories as $cat ) :
							$thumb_id  = get_term_meta( $cat->term_id, 'thumbnail_id', true );
							$thumb_url = $thumb_id ? wp_get_attachment_url( $thumb_id ) : wc_placeholder_img_src();
							$cat_link  = get_term_link( $cat );
						?>
						<a href="<?php echo esc_url( $cat_link ); ?>" class="categories-mega-item">
							<img
								class="categories-mega-item__image"
								src="<?php echo esc_url( $thumb_url ); ?>"
								alt="<?php echo esc_attr( $cat->name ); ?>"
								loading="lazy"
							>
							<span class="categories-mega-item__name"><?php echo esc_html( $cat->name ); ?></span>
						</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<?php endif; endif; ?>

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

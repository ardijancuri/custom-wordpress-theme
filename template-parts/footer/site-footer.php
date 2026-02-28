<?php
/**
 * Site Footer Template Part
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

$phone   = get_theme_mod( 'lesnamax_footer_phone', '+38344 / 77-99-77' );
$email   = get_theme_mod( 'lesnamax_footer_email', 'online.lesnamax@gmail.com' );
$address = get_theme_mod( 'lesnamax_footer_address', 'Magjistralja Prishtine Fushe Kosove' );
$hours   = get_theme_mod( 'lesnamax_footer_hours', 'E HENE - E PREMTE 08:00 - 20:00 / E SHTUNE - E DIEL 09:00 - 18:00' );

$footer_col_1_enabled = wp_validate_boolean( get_theme_mod( 'lesnamax_footer_col1_enabled', get_theme_mod( 'lesnamax_footer_col_1_enabled', true ) ) );
$footer_col_2_enabled = wp_validate_boolean( get_theme_mod( 'lesnamax_footer_col2_enabled', get_theme_mod( 'lesnamax_footer_col_2_enabled', true ) ) );
$footer_col_3_enabled = wp_validate_boolean( get_theme_mod( 'lesnamax_footer_col3_enabled', get_theme_mod( 'lesnamax_footer_col_3_enabled', true ) ) );

$footer_col_1_title = (string) get_theme_mod( 'lesnamax_footer_col1_title', get_theme_mod( 'lesnamax_footer_col_1_title', 'RRETH NESH' ) );
$footer_col_2_title = (string) get_theme_mod( 'lesnamax_footer_col2_title', get_theme_mod( 'lesnamax_footer_col_2_title', 'TEPIHE' ) );
$footer_col_3_title = (string) get_theme_mod( 'lesnamax_footer_col3_title', get_theme_mod( 'lesnamax_footer_col_3_title', 'KUZHINA' ) );
?>
<footer class="site-footer" role="contentinfo">
	<div class="container">
		<div class="footer-grid">
			<!-- Column 1 -->
			<div class="footer-col<?php echo $footer_col_1_enabled ? '' : ' footer-col--disabled'; ?>"<?php echo $footer_col_1_enabled ? '' : ' aria-hidden="true"'; ?>>
				<?php if ( $footer_col_1_enabled ) : ?>
					<?php if ( '' !== trim( $footer_col_1_title ) ) : ?>
						<h4 class="footer-col__title"><?php echo esc_html( $footer_col_1_title ); ?></h4>
					<?php endif; ?>
					<?php
					if ( has_nav_menu( 'footer_1' ) ) {
						wp_nav_menu( array(
							'theme_location' => 'footer_1',
							'container'      => false,
							'menu_class'     => 'footer-menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						) );
					} else {
						?>
						<ul class="footer-menu">
							<li><a href="#"><?php esc_html_e( 'Kontakti', 'lesnamax' ); ?></a></li>
							<li><a href="#"><?php esc_html_e( 'DEKOR OUTLET', 'lesnamax' ); ?></a></li>
							<li><a href="#"><?php esc_html_e( 'PRODUKTE PER KUZHINE', 'lesnamax' ); ?></a></li>
						</ul>
						<?php
					}
					?>
				<?php endif; ?>
			</div>

			<!-- Column 2 -->
			<div class="footer-col<?php echo $footer_col_2_enabled ? '' : ' footer-col--disabled'; ?>"<?php echo $footer_col_2_enabled ? '' : ' aria-hidden="true"'; ?>>
				<?php if ( $footer_col_2_enabled ) : ?>
					<?php if ( '' !== trim( $footer_col_2_title ) ) : ?>
						<h4 class="footer-col__title"><?php echo esc_html( $footer_col_2_title ); ?></h4>
					<?php endif; ?>
					<?php
					if ( has_nav_menu( 'footer_2' ) ) {
						wp_nav_menu( array(
							'theme_location' => 'footer_2',
							'container'      => false,
							'menu_class'     => 'footer-menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						) );
					} else {
						?>
						<ul class="footer-menu">
							<li><a href="#"><?php esc_html_e( 'TEKSTIL & PERDE', 'lesnamax' ); ?></a></li>
							<li><a href="#"><?php esc_html_e( 'DEKOR', 'lesnamax' ); ?></a></li>
							<li><a href="#"><?php esc_html_e( 'DYSHEKE', 'lesnamax' ); ?></a></li>
						</ul>
						<?php
					}
					?>
				<?php endif; ?>
			</div>

			<!-- Column 3 -->
			<div class="footer-col<?php echo $footer_col_3_enabled ? '' : ' footer-col--disabled'; ?>"<?php echo $footer_col_3_enabled ? '' : ' aria-hidden="true"'; ?>>
				<?php if ( $footer_col_3_enabled ) : ?>
					<?php if ( '' !== trim( $footer_col_3_title ) ) : ?>
						<h4 class="footer-col__title"><?php echo esc_html( $footer_col_3_title ); ?></h4>
					<?php endif; ?>
					<?php
					if ( has_nav_menu( 'footer_3' ) ) {
						wp_nav_menu( array(
							'theme_location' => 'footer_3',
							'container'      => false,
							'menu_class'     => 'footer-menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						) );
					} else {
						?>
						<ul class="footer-menu">
							<li><a href="#"><?php esc_html_e( 'MOBILJE', 'lesnamax' ); ?></a></li>
							<li><a href="#"><?php esc_html_e( 'Produkte dekorative', 'lesnamax' ); ?></a></li>
							<li><a href="#"><?php esc_html_e( 'Ngjitcom', 'lesnamax' ); ?></a></li>
						</ul>
						<?php
					}
					?>
				<?php endif; ?>
			</div>

			<!-- Column 4 - Contact -->
			<div class="footer-col footer-contact">
				<p class="footer-contact__label"><?php esc_html_e( 'YOU HAVE A QUESTION?', 'lesnamax' ); ?></p>
				<div class="footer-contact__phone">
					<?php lesnamax_icon( 'phone' ); ?>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
				</div>
				<p class="footer-contact__hours"><?php echo wp_kses( str_replace( '/', '<br>', $hours ), array( 'br' => array() ) ); ?></p>
				<p class="footer-contact__email">
					<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				</p>
				<p class="footer-contact__address"><?php echo esc_html( $address ); ?></p>
				<div class="footer-contact__cta">
					<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary">
						<?php esc_html_e( 'CONTACT US', 'lesnamax' ); ?>
					</a>
				</div>
			</div>
		</div>

		<div class="footer-bottom">
			<p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Mundësuar nga', 'lesnamax' ); ?> <a href="https://oninova.net" target="_blank" rel="noopener noreferrer">Oninova</a></p>
		</div>
	</div>
</footer>

<?php
/**
 * LesnaMax Theme Functions
 *
 * @package LesnaMax
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Theme constants
define( 'LESNAMAX_VERSION', '1.0.0' );
define( 'LESNAMAX_DIR', get_template_directory() );
define( 'LESNAMAX_URI', get_template_directory_uri() );

// Core includes
require_once LESNAMAX_DIR . '/inc/theme-setup.php';
require_once LESNAMAX_DIR . '/inc/enqueue.php';
require_once LESNAMAX_DIR . '/inc/template-tags.php';
require_once LESNAMAX_DIR . '/inc/customizer.php';
require_once LESNAMAX_DIR . '/inc/walker-mega-menu.php';

// WooCommerce includes (only if WooCommerce is active)
if ( class_exists( 'WooCommerce' ) ) {
	require_once LESNAMAX_DIR . '/inc/woocommerce-hooks.php';
	require_once LESNAMAX_DIR . '/inc/ajax-handlers.php';
}

// Elementor compatibility (only if Elementor is active)
if ( did_action( 'elementor/loaded' ) || defined( 'ELEMENTOR_VERSION' ) ) {
	require_once LESNAMAX_DIR . '/inc/elementor-compat.php';
}

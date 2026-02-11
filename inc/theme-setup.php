<?php
/**
 * Theme Setup
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function lesnamax_setup() {
	// Make theme available for translation
	load_theme_textdomain( 'lesnamax', LESNAMAX_DIR . '/languages' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails
	add_theme_support( 'post-thumbnails' );

	// Custom image sizes
	add_image_size( 'lesnamax-product-card', 300, 300, true );
	add_image_size( 'lesnamax-product-single', 600, 600, false );
	add_image_size( 'lesnamax-product-thumb', 80, 80, true );
	add_image_size( 'lesnamax-hero', 1920, 700, true );
	add_image_size( 'lesnamax-category', 400, 300, true );
	add_image_size( 'lesnamax-promo', 660, 400, true );

	// Custom logo support
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 180,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// Register navigation menus
	register_nav_menus( array(
		'primary'  => esc_html__( 'Primary Menu', 'lesnamax' ),
		'mobile'   => esc_html__( 'Mobile Menu', 'lesnamax' ),
		'footer_1' => esc_html__( 'Footer Column 1', 'lesnamax' ),
		'footer_2' => esc_html__( 'Footer Column 2', 'lesnamax' ),
		'footer_3' => esc_html__( 'Footer Column 3', 'lesnamax' ),
	) );

	// HTML5 support
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	// WooCommerce support
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 300,
		'single_image_width'    => 600,
		'product_grid'          => array(
			'default_rows'    => 3,
			'min_rows'        => 1,
			'default_columns' => 4,
			'min_columns'     => 1,
			'max_columns'     => 4,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Elementor support
	add_theme_support( 'elementor' );
}
add_action( 'after_setup_theme', 'lesnamax_setup' );

/**
 * Register widget areas.
 */
function lesnamax_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Shop Sidebar', 'lesnamax' ),
		'id'            => 'shop-sidebar',
		'description'   => esc_html__( 'Widgets for shop sidebar.', 'lesnamax' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widgets', 'lesnamax' ),
		'id'            => 'footer-widgets',
		'description'   => esc_html__( 'Widgets for footer area.', 'lesnamax' ),
		'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="footer-widget-title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'lesnamax_widgets_init' );

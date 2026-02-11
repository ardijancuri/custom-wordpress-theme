<?php
/**
 * Elementor Compatibility
 *
 * Theme locations, editor styles, and full-width template support.
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Elementor theme locations.
 *
 * Allows Elementor Theme Builder to override header, footer, etc.
 */
function lesnamax_elementor_register_locations( $elementor_theme_manager ) {
	$elementor_theme_manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'lesnamax_elementor_register_locations' );

/**
 * Enqueue theme styles in Elementor editor for consistent preview.
 */
function lesnamax_elementor_editor_styles() {
	wp_enqueue_style(
		'lesnamax-style',
		LESNAMAX_URI . '/assets/css/style.css',
		array(),
		LESNAMAX_VERSION
	);
}
add_action( 'elementor/editor/before_enqueue_styles', 'lesnamax_elementor_editor_styles' );

/**
 * Enqueue theme styles in Elementor preview for accurate rendering.
 */
function lesnamax_elementor_preview_styles() {
	wp_enqueue_style(
		'lesnamax-style',
		LESNAMAX_URI . '/assets/css/style.css',
		array(),
		LESNAMAX_VERSION
	);
}
add_action( 'elementor/preview/enqueue_styles', 'lesnamax_elementor_preview_styles' );

/**
 * Add Elementor-specific body classes.
 */
function lesnamax_elementor_body_classes( $classes ) {
	if ( lesnamax_is_elementor_page() ) {
		$classes[] = 'lesnamax-elementor-page';
	}
	return $classes;
}
add_filter( 'body_class', 'lesnamax_elementor_body_classes' );

/**
 * Check if current page is built with Elementor.
 */
function lesnamax_is_elementor_page( $post_id = null ) {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( ! $post_id ) {
		return false;
	}

	return \Elementor\Plugin::$instance->documents->get( $post_id )
		&& \Elementor\Plugin::$instance->documents->get( $post_id )->is_built_with_elementor();
}

/**
 * Set default Elementor container width to match theme.
 */
function lesnamax_elementor_default_settings( $settings ) {
	$settings['container_width'] = 1320;
	return $settings;
}
add_filter( 'elementor/kit/default_settings', 'lesnamax_elementor_default_settings' );

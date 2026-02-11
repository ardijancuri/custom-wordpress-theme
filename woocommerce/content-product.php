<?php
/**
 * WooCommerce Content Product Override
 *
 * This template overrides the default WooCommerce product loop item.
 * It simply delegates to our custom product-card template part.
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

get_template_part( 'template-parts/product-card' );

<?php
/**
 * Custom Nav Walker for Mega Menu
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Custom walker class for the primary navigation mega menu.
 */
class LesnaMax_Mega_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Track if current item has children.
	 */
	private $has_children = false;

	/**
	 * Starts the list before the elements are added.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent = str_repeat( "\t", $depth );
		if ( $depth === 0 ) {
			$output .= "\n{$indent}<div class=\"mega-menu-dropdown\">\n";
			$output .= "{$indent}\t<div class=\"mega-menu-inner container\">\n";
			$output .= "{$indent}\t\t<ul class=\"mega-menu-list\">\n";
		} else {
			$output .= "\n{$indent}<ul class=\"mega-menu-sublist\">\n";
		}
	}

	/**
	 * Ends the list after the elements are added.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$indent = str_repeat( "\t", $depth );
		if ( $depth === 0 ) {
			$output .= "{$indent}\t\t</ul>\n";
			$output .= "{$indent}\t</div>\n";
			$output .= "{$indent}</div>\n";
		} else {
			$output .= "{$indent}</ul>\n";
		}
	}

	/**
	 * Starts the element output.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item    = $data_object;
		$indent  = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		// Check if this item has children
		$this->has_children = in_array( 'menu-item-has-children', $classes );

		if ( $depth === 0 && $this->has_children ) {
			$classes[] = 'has-mega-menu';
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= $indent . '<li' . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$item_output = isset( $args->before ) ? $args->before : '';
		$item_output .= '<a' . $attributes . '>';
		$item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . $title . ( isset( $args->link_after ) ? $args->link_after : '' );

		if ( $depth === 0 && $this->has_children ) {
			$item_output .= ' <span class="nav-arrow">';
			ob_start();
			lesnamax_icon( 'chevron-down' );
			$item_output .= ob_get_clean();
			$item_output .= '</span>';
		}

		$item_output .= '</a>';
		$item_output .= isset( $args->after ) ? $args->after : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

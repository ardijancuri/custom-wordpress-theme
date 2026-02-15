<?php
/**
 * Custom Nav Walker for Mega Menu
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Custom walker class for the primary navigation mega menu.
 * Auto-fetches all WooCommerce subcategories for product_cat parents.
 */
class LesnaMax_Mega_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Track if current item has children.
	 */
	public $has_children = false;

	/**
	 * The current depth-0 parent item.
	 */
	private $current_parent = null;

	/**
	 * Whether subcategories were auto-rendered from WooCommerce.
	 */
	private $auto_rendered = false;

	/**
	 * Whether start_lvl was called for the current depth-0 item.
	 */
	private $dropdown_opened = false;

	/**
	 * Cached WooCommerce subcategories for the current parent.
	 */
	private $wc_subcategories = null;

	/**
	 * Helper: get WooCommerce child categories for a product_cat menu item.
	 */
	private function get_wc_subcategories( $item ) {
		if ( $item->object !== 'product_cat' ) {
			return array();
		}
		$terms = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'parent'     => $item->object_id,
			'orderby'    => 'name',
			'order'      => 'ASC',
		) );
		if ( is_wp_error( $terms ) ) {
			return array();
		}
		return $terms;
	}

	/**
	 * Helper: render subcategory cards.
	 */
	private function render_subcategory_cards( &$output, $subcategories, $indent ) {
		foreach ( $subcategories as $cat ) {
			$cat_link = get_term_link( $cat );
			$thumb_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );

			if ( $thumb_id ) {
				$img_url = wp_get_attachment_image_url( $thumb_id, 'medium' );
				$output .= "{$indent}<a href=\"" . esc_url( $cat_link ) . "\" class=\"mega-menu-card\">";
				$output .= '<div class="mega-menu-card__image-wrap">';
				$output .= '<img class="mega-menu-card__image" src="' . esc_url( $img_url ) . '" alt="' . esc_attr( $cat->name ) . '">';
				$output .= '<span class="mega-menu-card__label">' . esc_html( $cat->name ) . '</span>';
				$output .= '</div>';
				$output .= "</a>\n";
			} else {
				$output .= "{$indent}<a href=\"" . esc_url( $cat_link ) . "\" class=\"mega-menu-card mega-menu-card--text-only\">";
				$output .= '<span class="mega-menu-card__label">' . esc_html( $cat->name ) . '</span>';
				$output .= "</a>\n";
			}
		}
	}

	/**
	 * Starts the list before the elements are added.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent = str_repeat( "\t", $depth );
		if ( $depth === 0 ) {
			$this->dropdown_opened = true;

			$output .= "\n{$indent}<div class=\"mega-menu-dropdown\">\n";
			$output .= "{$indent}\t<div class=\"mega-menu-inner container\">\n";
			$output .= "{$indent}\t\t<div class=\"mega-menu-subcategories\">\n";

			// Auto-render all WooCommerce child categories for product_cat parents.
			if ( $this->wc_subcategories && ! empty( $this->wc_subcategories ) ) {
				$this->auto_rendered = true;
				$this->render_subcategory_cards( $output, $this->wc_subcategories, "{$indent}\t\t\t" );
			}
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
			$output .= "{$indent}\t\t</div>\n"; // .mega-menu-subcategories
			$output .= "{$indent}\t</div>\n";    // .mega-menu-inner
			$output .= "{$indent}</div>\n";       // .mega-menu-dropdown
			$this->auto_rendered = false;
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

		// Check if this item has children in the menu
		$this->has_children = in_array( 'menu-item-has-children', $classes );

		if ( $depth === 0 ) {
			$this->current_parent  = $item;
			$this->dropdown_opened = false;
			$this->wc_subcategories = null;

			// Check for WooCommerce subcategories regardless of menu structure.
			if ( $item->object === 'product_cat' ) {
				$this->wc_subcategories = $this->get_wc_subcategories( $item );
			}

			// Add mega menu class if item has menu children OR WooCommerce subcategories.
			if ( $this->has_children || ! empty( $this->wc_subcategories ) ) {
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

			if ( $this->has_children || ! empty( $this->wc_subcategories ) ) {
				$item_output .= ' <span class="nav-arrow">';
				ob_start();
				lesnamax_icon( 'chevron-down' );
				$item_output .= ob_get_clean();
				$item_output .= '</span>';
			}

			$item_output .= '</a>';
			$item_output .= isset( $args->after ) ? $args->after : '';

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

		} elseif ( $depth === 1 ) {
			// Skip walker's depth-1 items if we already auto-rendered from WooCommerce.
			if ( $this->auto_rendered ) {
				return;
			}

			// Fallback for non-product_cat parents: render menu items normally.
			$atts         = array();
			$atts['href'] = ! empty( $item->url ) ? $item->url : '';
			$title        = apply_filters( 'the_title', $item->title, $item->ID );

			if ( $item->object === 'product_cat' ) {
				$thumb_id = get_term_meta( $item->object_id, 'thumbnail_id', true );
				if ( $thumb_id ) {
					$img_url = wp_get_attachment_image_url( $thumb_id, 'medium' );

					$output .= $indent . '<a href="' . esc_url( $atts['href'] ) . '" class="mega-menu-card">';
					$output .= '<div class="mega-menu-card__image-wrap">';
					$output .= '<img class="mega-menu-card__image" src="' . esc_url( $img_url ) . '" alt="' . esc_attr( $title ) . '">';
					$output .= '<span class="mega-menu-card__label">' . esc_html( $title ) . '</span>';
					$output .= '</div>';
					$output .= '</a>';
				} else {
					$output .= $indent . '<a href="' . esc_url( $atts['href'] ) . '" class="mega-menu-card mega-menu-card--text-only">';
					$output .= '<span class="mega-menu-card__label">' . esc_html( $title ) . '</span>';
					$output .= '</a>';
				}
			} else {
				$output .= $indent . '<a href="' . esc_url( $atts['href'] ) . '" class="mega-menu-card mega-menu-card--text-only">';
				$output .= '<span class="mega-menu-card__label">' . esc_html( $title ) . '</span>';
				$output .= '</a>';
			}
		}
	}

	/**
	 * Ends the element output.
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		if ( $depth === 0 ) {
			// If the Walker didn't open a dropdown (no menu children) but we have
			// WooCommerce subcategories, render the full dropdown now.
			if ( ! $this->dropdown_opened && ! empty( $this->wc_subcategories ) ) {
				$output .= "\n<div class=\"mega-menu-dropdown\">\n";
				$output .= "\t<div class=\"mega-menu-inner container\">\n";
				$output .= "\t\t<div class=\"mega-menu-subcategories\">\n";
				$this->render_subcategory_cards( $output, $this->wc_subcategories, "\t\t\t" );
				$output .= "\t\t</div>\n";
				$output .= "\t</div>\n";
				$output .= "</div>\n";
			}

			$output .= "</li>\n";
			$this->wc_subcategories = null;
			$this->dropdown_opened  = false;
		}
	}
}

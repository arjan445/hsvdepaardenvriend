<?php
/**
 * Bootstrap 5-navigatie.
 *
 * @package paardenvriend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PV_Nav_Walker extends Walker_Nav_Menu {
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="dropdown-menu">';
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes      = empty( $item->classes ) ? array() : (array) $item->classes;
		$has_children = in_array( 'menu-item-has-children', $classes, true );
		$active       = (bool) array_intersect( array( 'current-menu-item', 'current-menu-ancestor', 'current_page_item', 'current_page_parent', 'current-menu-parent' ), $classes );
		$target       = $item->target ? ' target="' . esc_attr( $item->target ) . '" rel="noopener"' : '';
		$current      = $active ? ' aria-current="page"' : '';

		if ( 0 === $depth ) {
			$output .= '<li class="nav-item' . ( $has_children ? ' dropdown' : '' ) . '">';
			if ( $has_children ) {
				$output .= '<a class="nav-link dropdown-toggle' . ( $active ? ' active' : '' ) . '" href="' . esc_url( $item->url ) . '" role="button" data-bs-toggle="dropdown" aria-expanded="false"' . $current . '>' . esc_html( $item->title ) . '</a>';
			} else {
				$output .= '<a class="nav-link' . ( $active ? ' active' : '' ) . '" href="' . esc_url( $item->url ) . '"' . $target . $current . '>' . esc_html( $item->title ) . '</a>';
			}
		} else {
			$output .= '<li><a class="dropdown-item' . ( $active ? ' active' : '' ) . '" href="' . esc_url( $item->url ) . '"' . $target . $current . '>' . esc_html( $item->title ) . '</a>';
		}
	}
}

/* Menu zolang er nog geen menu is ingesteld. */
function pv_fallback_menu() {
	$items = array();
	foreach ( array( 'over-ons', 'sponsoren' ) as $slug ) {
		$p = get_page_by_path( $slug );
		if ( $p ) {
			$items[] = array( get_permalink( $p ), strtolower( get_the_title( $p ) ) );
		}
	}
	if ( post_type_exists( 'hsv_wedstrijd' ) ) {
		$items[] = array( get_post_type_archive_link( 'hsv_wedstrijd' ), 'agenda' );
		$items[] = array( get_post_type_archive_link( 'hsv_uitslag' ), 'uitslagen' );
	}
	foreach ( array( 'leden', 'contact' ) as $slug ) {
		$p = get_page_by_path( $slug );
		if ( $p ) {
			$items[] = array( get_permalink( $p ), strtolower( get_the_title( $p ) ) );
		}
	}
	echo '<ul class="navbar-nav ms-auto">';
	foreach ( $items as $i ) {
		printf( '<li class="nav-item"><a class="nav-link" href="%s">%s</a></li>', esc_url( $i[0] ), esc_html( $i[1] ) );
	}
	echo '</ul>';
}

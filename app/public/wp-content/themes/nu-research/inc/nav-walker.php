<?php
/**
 * Primary-nav walker: disclosure (split-button) pattern for depth-2 menus.
 *
 * Parent items with children render as a normal link followed by a separate
 * chevron <button aria-expanded aria-controls> that toggles the submenu, so
 * the parent page stays reachable on touch. Buttons ship with the `hidden`
 * attribute: without JS submenus are simply always visible and the useless
 * toggles never appear (same progressive-enhancement contract as nav.js).
 *
 * @package nu-research
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nav walker that emits disclosure toggles and identifiable submenus.
 */
class NU_Research_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Menu-item ID of the parent whose submenu is about to open, so
	 * start_lvl() can give the <ul> the id the button's aria-controls names.
	 *
	 * @var int
	 */
	private $submenu_parent_id = 0;

	/**
	 * Open a submenu list.
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= sprintf(
			'<ul class="nav-submenu" id="submenu-%d">',
			(int) $this->submenu_parent_id
		);
	}

	/**
	 * Close a submenu list.
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}

	/**
	 * Render a menu item; append the disclosure toggle after parent links.
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		parent::start_el( $output, $item, $depth, $args, $id );

		$has_children = in_array( 'menu-item-has-children', (array) $item->classes, true );

		if ( ! $has_children || 0 !== $depth ) {
			return;
		}

		$this->submenu_parent_id = (int) $item->ID;

		$output .= sprintf(
			'<button class="submenu-toggle" aria-expanded="false" aria-controls="submenu-%1$d" hidden>' .
				'<span class="screen-reader-text">%2$s</span>' .
				'<span class="submenu-chevron" aria-hidden="true"></span>' .
			'</button>',
			(int) $item->ID,
			/* translators: %s: parent menu item label. */
			esc_html( sprintf( __( '%s submenu', 'nu-research' ), $item->title ) )
		);
	}
}

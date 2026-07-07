<?php
/**
 * Spotlight-style search palette (demo).
 *
 * Adds a search button to the utility bar and a centered command-palette
 * overlay opened by that button or the Cmd/Ctrl+S shortcut. The results are a
 * fixed set of program pages filtered client-side — this is a UI demo, not a
 * live query against the database.
 *
 * @package nu-research
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The demo result set: program pages the palette can jump to. Kept in PHP so
 * the URLs resolve through the theme's slug helper instead of being hard-coded.
 *
 * @return array<int, array{title:string, url:string, kind:string}>
 */
function nu_research_search_items() {
	$items = array(
		array( 'About the Program', 'about-the-program', 'Page' ),
		array( 'Highlights & Team', 'highlights-team', 'Page' ),
		array( 'Events & Info Sessions', 'events', 'Page' ),
		array( 'Press & Publications', 'press', 'Page' ),
		array( 'Blog', 'blog', 'Page' ),
		array( 'Apply & Eligibility', 'apply-eligibility', 'Page' ),
		array( 'Frequently Asked Questions', 'apply-eligibility', 'FAQ' ),
		array( 'Contact', 'contact', 'Page' ),
	);

	$out = array();
	foreach ( $items as $item ) {
		$out[] = array(
			'title' => $item[0],
			'url'   => nu_research_page_url( $item[1] ),
			'kind'  => $item[2],
		);
	}

	return $out;
}

/**
 * Enqueue the palette's styles and script, and hand the demo data to JS.
 */
function nu_research_search_assets() {
	wp_enqueue_style(
		'nu-research-search',
		get_theme_file_uri( 'assets/css/search.css' ),
		array( 'nu-research-main' ),
		(string) filemtime( get_theme_file_path( 'assets/css/search.css' ) )
	);

	wp_enqueue_script(
		'nu-research-search',
		get_theme_file_uri( 'assets/js/search.js' ),
		array(),
		(string) filemtime( get_theme_file_path( 'assets/js/search.js' ) ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_localize_script(
		'nu-research-search',
		'nuResearchSearch',
		array( 'items' => nu_research_search_items() )
	);
}
add_action( 'wp_enqueue_scripts', 'nu_research_search_assets' );

/**
 * Markup for the search button. The `⌘S` hint is decorative (hidden from
 * assistive tech); `aria-keyshortcuts` carries the real shortcut for screen
 * readers.
 *
 * @return string
 */
function nu_research_search_trigger_html() {
	$icon = '<svg class="search-trigger__icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false">'
		. '<circle cx="7" cy="7" r="5" stroke="currentColor" stroke-width="1.6"></circle>'
		. '<line x1="11" y1="11" x2="14.5" y2="14.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"></line>'
		. '</svg>';

	return '<button type="button" class="search-trigger" aria-haspopup="dialog" aria-controls="site-search-modal" aria-expanded="false" aria-keyshortcuts="Meta+S Control+S">'
		. $icon
		. '<span class="search-trigger__label">' . esc_html__( 'Search', 'nu-research' ) . '</span>'
		. '<kbd class="search-trigger__kbd" aria-hidden="true">&#8984;S</kbd>'
		. '</button>';
}

/**
 * Append the search button to the primary (bottom) navigation as its own item,
 * right-aligned on desktop. The utility bar up top keeps the application status.
 *
 * @param string   $items The <li> markup for the menu.
 * @param stdClass $args  wp_nav_menu arguments.
 * @return string
 */
function nu_research_search_nav_item( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	return $items . '<li class="menu-item nav-item--search">' . nu_research_search_trigger_html() . '</li>';
}
add_filter( 'wp_nav_menu_items', 'nu_research_search_nav_item', 10, 2 );

/**
 * The overlay itself, printed once at the end of the document. Ships hidden;
 * JavaScript reveals it. No-JS visitors never see a dead control because the
 * trigger only does anything once the script wires it up.
 */
function nu_research_search_modal() {
	?>
	<div id="site-search-modal" class="search-modal" data-open="false" hidden>
		<div class="search-modal__backdrop" data-search-dismiss></div>

		<div
			class="search-modal__panel"
			role="dialog"
			aria-modal="true"
			aria-label="<?php esc_attr_e( 'Search the site', 'nu-research' ); ?>"
		>
			<div class="search-modal__field">
				<svg class="search-modal__field-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true" focusable="false">
					<circle cx="9" cy="9" r="6.4" stroke="currentColor" stroke-width="1.7"></circle>
					<line x1="14" y1="14" x2="18" y2="18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"></line>
				</svg>
				<input
					type="search"
					class="search-modal__input"
					placeholder="<?php esc_attr_e( 'Search the program…', 'nu-research' ); ?>"
					aria-label="<?php esc_attr_e( 'Search', 'nu-research' ); ?>"
					autocomplete="off"
					autocapitalize="off"
					spellcheck="false"
					role="combobox"
					aria-expanded="false"
					aria-controls="search-modal-results"
					aria-autocomplete="list"
				>
				<button type="button" class="search-modal__close" data-search-dismiss aria-label="<?php esc_attr_e( 'Close search', 'nu-research' ); ?>">
					<kbd>Esc</kbd>
				</button>
			</div>

			<ul id="search-modal-results" class="search-modal__results" role="listbox" aria-label="<?php esc_attr_e( 'Search results', 'nu-research' ); ?>" hidden></ul>

			<p class="search-modal__status" role="status" aria-live="polite"></p>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'nu_research_search_modal' );

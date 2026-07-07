<?php
/**
 * SCF-powered content blocks.
 *
 * Each block lives in blocks/{name}/ as a block.json — with its field
 * definitions inlined under "acf.fields" — plus a render.php template.
 * Secure Custom Fields auto-generates the editor sidebar from the field
 * definitions (no editor JavaScript needed), while the front-end markup
 * comes entirely from the PHP templates, which reuse the same theme helpers
 * (nu_research_cta, nu_research_section_header) the original hard-coded
 * front-page template used.
 *
 * @package nu-research
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register every block directory under blocks/.
 *
 * Requires Secure Custom Fields: without it the "acf" key in block.json is
 * inert (no render callback, no fields), so skip registration entirely
 * rather than expose broken blocks in the inserter.
 */
function nu_research_register_scf_blocks() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	foreach ( glob( get_theme_file_path( 'blocks/*/block.json' ) ) as $block_json ) {
		register_block_type( dirname( $block_json ) );
	}
}
add_action( 'init', 'nu_research_register_scf_blocks' );

/**
 * Add a dedicated block category so the blocks are easy to find in the inserter.
 *
 * @param array $categories Existing block categories.
 * @return array
 */
function nu_research_block_category( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'nu-research',
			'title' => __( 'WordPress Research Fellows', 'nu-research' ),
		)
	);
	return $categories;
}
add_filter( 'block_categories_all', 'nu_research_block_category' );

/**
 * Thin line icon for the pillars block (Lucide-style, 2px stroke, drawn with
 * currentColor so the accent is set in CSS).
 *
 * @param string $name Icon key: network | medal | pyramid.
 */
function nu_research_pillar_icon( $name ) {
	$paths = array(
		'network' => '<circle cx="12" cy="5" r="3"/><circle cx="5" cy="19" r="3"/><circle cx="19" cy="19" r="3"/><path d="M10.5 7.5 6.5 16.2M13.5 7.5l4 8.7M8 19h8"/>',
		'medal'   => '<circle cx="12" cy="14" r="6"/><path d="M8.7 9 6 3h4l2 4.5L14 3h4l-2.7 6"/><path d="M12 11.5v3l1.8 1"/>',
		'pyramid' => '<path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 3v17"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return;
	}

	printf(
		'<svg class="pillar-icon" viewBox="0 0 24 24" width="44" height="44" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
		$paths[ $name ] // Static markup defined above; contains no user input.
	);
}

/**
 * Abstract wordmark glyph for the logo marquee, drawn with currentColor so the
 * whole logo (mark + name) tints as one in CSS. Same 2px line-icon language as
 * the pillar icons above.
 *
 * @param string $name Mark key.
 */
function nu_research_logo_mark( $name ) {
	$paths = array(
		'orbit'   => '<circle cx="12" cy="12" r="3.2"/><ellipse cx="12" cy="12" rx="10" ry="4"/><ellipse cx="12" cy="12" rx="4" ry="10"/>',
		'blocks'  => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
		'helix'   => '<path d="M7 3c0 4.5 10 4.5 10 9s-10 4.5-10 9"/><path d="M17 3c0 4.5-10 4.5-10 9s10 4.5 10 9"/><path d="M8.5 6h7M8.5 18h7"/>',
		'peak'    => '<path d="M3 20 10 6l4 7 2.2-3.4L21 20Z"/>',
		'column'  => '<path d="M12 3 3.5 8h17L12 3Z"/><path d="M6 8v9M10 8v9M14 8v9M18 8v9"/><path d="M3 21h18"/>',
		'hexagon' => '<path d="M12 2 21 7v10l-9 5-9-5V7Z"/><path d="M12 8 16 10.5v5L12 18l-4-2.5v-5Z"/>',
		'node'    => '<circle cx="12" cy="5" r="2.5"/><circle cx="5" cy="19" r="2.5"/><circle cx="19" cy="19" r="2.5"/><path d="M10.6 7.2 6.4 16.5M13.4 7.2l4.2 9.3M7.5 19h9"/>',
		'prism'   => '<path d="M12 3 3 20h18Z"/><path d="M12 3v17M3 20l9-9 9 9"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		$name = 'hexagon';
	}

	printf(
		'<svg class="logo-mark" viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
		$paths[ $name ] // Static markup keyed above; contains no user input.
	);
}

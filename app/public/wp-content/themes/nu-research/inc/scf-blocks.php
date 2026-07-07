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

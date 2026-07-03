<?php
/**
 * One-shot site assembly for the WordPress Research Fellows microsite.
 * Run with: ./wp.sh eval-file setup-site.php
 * Idempotent: safe to re-run (checks for existing groups/pages/menu/media).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$base = '/Users/darshanaswathappa/Local Sites/northeastern-research/app';

/* -------------------------------------------------- 1. ACF field groups */
if ( ! function_exists( 'acf_import_field_group' ) ) {
	WP_CLI::error( 'ACF is not active.' );
}

$groups = json_decode( file_get_contents( $base . '/design-reference/acf-field-groups.json' ), true );
foreach ( $groups as $group ) {
	$existing = acf_get_field_group( $group['key'] );
	if ( $existing ) {
		WP_CLI::log( "ACF group exists: {$group['key']}" );
		continue;
	}
	acf_import_field_group( $group );
	WP_CLI::log( "Imported ACF group: {$group['key']}" );
}

/* -------------------------------------------------- 2. Pages */
$pages = array(
	'home'              => array( 'title' => 'Home', 'template' => '', 'content' => '' ),
	'about-the-program' => array( 'title' => 'About the Program', 'template' => 'page-about.php', 'content' => '' ),
	'highlights-team'   => array( 'title' => 'Highlights & Team', 'template' => 'page-highlights-team.php', 'content' => '' ),
	'apply-eligibility' => array( 'title' => 'Apply & Eligibility', 'template' => 'page-apply.php', 'content' => '[swe_fellows_application]' ),
	'contact'           => array( 'title' => 'Contact', 'template' => 'page-contact.php', 'content' => '' ),
);

$page_ids = array();
foreach ( $pages as $slug => $def ) {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		$page_ids[ $slug ] = $existing->ID;
		WP_CLI::log( "Page exists: {$slug} (#{$existing->ID})" );
		continue;
	}
	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $def['title'],
			'post_name'    => $slug,
			'post_content' => $def['content'],
		)
	);
	if ( $def['template'] ) {
		update_post_meta( $id, '_wp_page_template', $def['template'] );
	}
	$page_ids[ $slug ] = $id;
	WP_CLI::log( "Created page: {$slug} (#{$id})" );
}

/* -------------------------------------------------- 3. Front page + identity */
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_ids['home'] );
update_option( 'blogname', 'WordPress Research Fellows Program' );
update_option( 'blogdescription', 'Dept. of Software Engineering · Summer Research' );

/* -------------------------------------------------- 4. Primary menu */
$menu_name = 'Primary';
$menu      = wp_get_nav_menu_object( $menu_name );
if ( ! $menu ) {
	$menu_id = wp_create_nav_menu( $menu_name );
	foreach ( array( 'home', 'about-the-program', 'highlights-team', 'apply-eligibility', 'contact' ) as $i => $slug ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-object-id' => $page_ids[ $slug ],
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => $i + 1,
			)
		);
	}
	WP_CLI::log( "Created menu #{$menu_id}" );
} else {
	$menu_id = $menu->term_id;
	WP_CLI::log( "Menu exists: #{$menu_id}" );
}
set_theme_mod( 'nav_menu_locations', array( 'primary' => $menu_id ) );

/* -------------------------------------------------- 5. Media imports */
function swe_setup_import_image( $path, $title ) {
	// Reuse an existing attachment with the same title if present.
	$existing = get_posts(
		array(
			'post_type'   => 'attachment',
			'title'       => $title,
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	$upload = wp_upload_bits( basename( $path ), null, file_get_contents( $path ) );
	if ( ! empty( $upload['error'] ) ) {
		WP_CLI::warning( "Upload failed for {$path}: {$upload['error']}" );
		return 0;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	return (int) $attachment_id;
}

$img_dir = $base . '/design-reference/img';
$team_imgs = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$team_imgs[ $i ] = swe_setup_import_image( "{$img_dir}/team-{$i}.jpg", "Fellow portrait {$i}" );
}
$proj_imgs = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$proj_imgs[ $i ] = swe_setup_import_image( "{$img_dir}/proj-{$i}.jpg", "Project image {$i}" );
}
WP_CLI::log( 'Media imported: ' . implode( ',', $team_imgs ) . ' / ' . implode( ',', $proj_imgs ) );

/* -------------------------------------------------- 6. ACF content */
$hl_page = $page_ids['highlights-team'];

$team_rows = array(
	array( 'field_swe_tm_photo' => $team_imgs[1], 'field_swe_tm_name' => 'Maya Chen', 'field_swe_tm_major' => 'Software Engineering ’28', 'field_swe_tm_mentor' => 'Prof. Reyes', 'field_swe_tm_focus' => 'Rebuilding the plugin update-check API for reliability under load.' ),
	array( 'field_swe_tm_photo' => $team_imgs[2], 'field_swe_tm_name' => 'Jordan Osei', 'field_swe_tm_major' => 'Computer Science ’27', 'field_swe_tm_mentor' => 'Prof. Naik', 'field_swe_tm_focus' => 'Prototyping a faster block-editor rendering path for large posts.' ),
	array( 'field_swe_tm_photo' => $team_imgs[3], 'field_swe_tm_name' => 'Priya Patel', 'field_swe_tm_major' => 'Software Engineering ’28', 'field_swe_tm_mentor' => 'Prof. Reyes', 'field_swe_tm_focus' => 'Auditing core plugins for common OWASP vulnerabilities.' ),
	array( 'field_swe_tm_photo' => $team_imgs[4], 'field_swe_tm_name' => 'Lucas Ferreira', 'field_swe_tm_major' => 'Computer Science ’27', 'field_swe_tm_mentor' => 'Prof. Whitfield', 'field_swe_tm_focus' => 'Adding screen-reader support to the media library grid.' ),
	array( 'field_swe_tm_photo' => $team_imgs[5], 'field_swe_tm_name' => 'Sofia Marino', 'field_swe_tm_major' => 'Software Engineering ’29', 'field_swe_tm_mentor' => 'Prof. Naik', 'field_swe_tm_focus' => 'Building a caching layer for high-traffic REST endpoints.' ),
	array( 'field_swe_tm_photo' => $team_imgs[6], 'field_swe_tm_name' => 'Daniel Kim', 'field_swe_tm_major' => 'Computer Science ’28', 'field_swe_tm_mentor' => 'Prof. Whitfield', 'field_swe_tm_focus' => 'Keyboard-navigation pass across the admin dashboard widgets.' ),
);
update_field( 'field_swe_team_repeater', $team_rows, $hl_page );

$highlight_rows = array(
	array( 'field_swe_hl_image' => $proj_imgs[1], 'field_swe_hl_title' => 'Plugin Health Dashboard', 'field_swe_hl_track' => 'Plugin Architecture', 'field_swe_hl_summary' => 'A lightweight admin widget surfacing update failures and dependency conflicts across a multisite network before they cause downtime.', 'field_swe_hl_students' => 'Maya Chen, Priya Patel' ),
	array( 'field_swe_hl_image' => $proj_imgs[2], 'field_swe_hl_title' => 'Focus-Mode Block Editor', 'field_swe_hl_track' => 'Editor & Block UX', 'field_swe_hl_summary' => 'An opt-in distraction-free writing mode for the block editor, cutting visual chrome without losing block controls.', 'field_swe_hl_students' => 'Jordan Osei' ),
	array( 'field_swe_hl_image' => $proj_imgs[3], 'field_swe_hl_title' => 'Automated A11y Linter', 'field_swe_hl_track' => 'Accessibility', 'field_swe_hl_summary' => 'A CI check that flags missing alt text and low-contrast theme combinations before a build ships.', 'field_swe_hl_students' => 'Lucas Ferreira, Daniel Kim' ),
	array( 'field_swe_hl_image' => $proj_imgs[4], 'field_swe_hl_title' => 'Edge Cache Layer', 'field_swe_hl_track' => 'Performance & Security', 'field_swe_hl_summary' => 'A drop-in object-cache backend that cut average REST response time by 40% on a test multisite install.', 'field_swe_hl_students' => 'Sofia Marino' ),
);
update_field( 'field_swe_highlights_repeater', $highlight_rows, $hl_page );

$deadline_rows = array(
	array( 'field_swe_dl_label' => 'Applications open', 'field_swe_dl_date' => '20261201' ),
	array( 'field_swe_dl_label' => 'Priority deadline', 'field_swe_dl_date' => '20270206' ),
	array( 'field_swe_dl_label' => 'Final deadline', 'field_swe_dl_date' => '20270320' ),
	array( 'field_swe_dl_label' => 'Decisions released', 'field_swe_dl_date' => '20270410' ),
	array( 'field_swe_dl_label' => 'Program begins', 'field_swe_dl_date' => '20270601' ),
);
update_field( 'field_swe_deadlines_repeater', $deadline_rows, $page_ids['apply-eligibility'] );

WP_CLI::success( 'Site assembly complete.' );

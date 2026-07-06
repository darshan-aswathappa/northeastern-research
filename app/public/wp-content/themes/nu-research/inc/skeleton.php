<?php
/**
 * Skeleton loading screens.
 *
 * Renders a layout-matching placeholder overlay on first paint and removes it
 * once the page finishes loading — so slow connections see a structured
 * skeleton instead of a blank page. (The theme's AOS setup holds [data-aos]
 * content at opacity:0 until window load, which is exactly the window this
 * fills.) An Appearance -> Skeleton Loading screen toggles the feature and an
 * admin-only "simulate" delay for previewing the skeleton on a fast connection.
 *
 * The overlay is server-rendered and visible by default, so there is no flash
 * of unstyled content; JavaScript only removes it. A <noscript> rule hides the
 * overlay when scripts are disabled, so no-JS visitors are never stuck on it.
 *
 * @package nu-research
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Upper bound for the simulate delay, in milliseconds.
 */
define( 'NU_SKELETON_MAX_DELAY', 10000 );

/* -------------------------------------------------------------------------- *
 * Options + helpers.
 * -------------------------------------------------------------------------- */

/**
 * Whether skeleton screens are shown on the front end.
 *
 * @return bool
 */
function nu_skeleton_enabled() {
	return (bool) get_option( 'nu_skeleton_enabled', true );
}

/**
 * Whether the admin-only simulate/preview delay is switched on.
 *
 * @return bool
 */
function nu_skeleton_sim_enabled() {
	return (bool) get_option( 'nu_skeleton_sim_enabled', false );
}

/**
 * Configured simulate delay in milliseconds, clamped to a sane maximum.
 *
 * @return int
 */
function nu_skeleton_sim_delay() {
	return min( absint( get_option( 'nu_skeleton_sim_delay', 2000 ) ), NU_SKELETON_MAX_DELAY );
}

/**
 * Delay (ms) to hold the skeleton before revealing content. The simulation is
 * a preview aid for administrators, so real visitors always get 0 — the
 * skeleton clears the moment the page finishes loading.
 *
 * @return int
 */
function nu_skeleton_effective_delay() {
	if ( nu_skeleton_sim_enabled() && current_user_can( 'manage_options' ) ) {
		return nu_skeleton_sim_delay();
	}
	return 0;
}

/* -------------------------------------------------------------------------- *
 * Admin settings screen (Appearance -> Skeleton Loading).
 * -------------------------------------------------------------------------- */

/**
 * Register the settings screen under the Appearance menu.
 */
function nu_skeleton_admin_menu() {
	add_theme_page(
		__( 'Skeleton Loading', 'nu-research' ),
		__( 'Skeleton Loading', 'nu-research' ),
		'manage_options',
		'nu-skeleton',
		'nu_skeleton_settings_page'
	);
}
add_action( 'admin_menu', 'nu_skeleton_admin_menu' );

/**
 * Register options, section, and fields with the Settings API.
 */
function nu_skeleton_settings_init() {
	register_setting(
		'nu_skeleton_group',
		'nu_skeleton_enabled',
		array(
			'type'              => 'boolean',
			'sanitize_callback' => 'nu_skeleton_sanitize_checkbox',
			'default'           => true,
		)
	);
	register_setting(
		'nu_skeleton_group',
		'nu_skeleton_sim_enabled',
		array(
			'type'              => 'boolean',
			'sanitize_callback' => 'nu_skeleton_sanitize_checkbox',
			'default'           => false,
		)
	);
	register_setting(
		'nu_skeleton_group',
		'nu_skeleton_sim_delay',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'nu_skeleton_sanitize_delay',
			'default'           => 2000,
		)
	);

	add_settings_section(
		'nu_skeleton_main',
		__( 'Skeleton screens', 'nu-research' ),
		'nu_skeleton_section_intro',
		'nu-skeleton'
	);

	add_settings_field(
		'nu_skeleton_enabled',
		__( 'Enable skeleton screens', 'nu-research' ),
		'nu_skeleton_field_enabled',
		'nu-skeleton',
		'nu_skeleton_main'
	);
	add_settings_field(
		'nu_skeleton_sim_enabled',
		__( 'Simulate loading', 'nu-research' ),
		'nu_skeleton_field_sim_enabled',
		'nu-skeleton',
		'nu_skeleton_main'
	);
	add_settings_field(
		'nu_skeleton_sim_delay',
		__( 'Simulation delay', 'nu-research' ),
		'nu_skeleton_field_sim_delay',
		'nu-skeleton',
		'nu_skeleton_main'
	);
}
add_action( 'admin_init', 'nu_skeleton_settings_init' );

/**
 * Normalise a checkbox value to '1' (on) or '' (off). Unchecked boxes are not
 * posted, so the Settings API hands the callback null — treated as off.
 *
 * @param mixed $value Raw submitted value.
 * @return string
 */
function nu_skeleton_sanitize_checkbox( $value ) {
	return ( '1' === $value || 1 === $value || true === $value ) ? '1' : '';
}

/**
 * Clamp the delay to a non-negative integer within the allowed maximum.
 *
 * @param mixed $value Raw submitted value.
 * @return int
 */
function nu_skeleton_sanitize_delay( $value ) {
	return min( absint( $value ), NU_SKELETON_MAX_DELAY );
}

/**
 * Intro copy for the settings section.
 */
function nu_skeleton_section_intro() {
	echo '<p>' . esc_html__( 'Show a layout-matching placeholder while each page finishes loading, instead of a blank screen or a spinner.', 'nu-research' ) . '</p>';
}

/**
 * Enable-feature checkbox field.
 */
function nu_skeleton_field_enabled() {
	printf(
		'<label><input type="checkbox" name="nu_skeleton_enabled" value="1" %s> %s</label>',
		checked( nu_skeleton_enabled(), true, false ),
		esc_html__( 'Show skeleton screens on the front end while pages load.', 'nu-research' )
	);
}

/**
 * Simulate-loading checkbox field.
 */
function nu_skeleton_field_sim_enabled() {
	printf(
		'<label><input type="checkbox" name="nu_skeleton_sim_enabled" value="1" %s> %s</label><p class="description">%s</p>',
		checked( nu_skeleton_sim_enabled(), true, false ),
		esc_html__( 'Hold the skeleton for the delay below so it can be previewed.', 'nu-research' ),
		esc_html__( 'Only affects logged-in administrators — regular visitors never see the artificial delay.', 'nu-research' )
	);
}

/**
 * Simulate-delay number field.
 */
function nu_skeleton_field_sim_delay() {
	printf(
		'<input type="number" name="nu_skeleton_sim_delay" value="%s" min="0" max="%d" step="100" class="small-text"> %s',
		esc_attr( nu_skeleton_sim_delay() ),
		absint( NU_SKELETON_MAX_DELAY ),
		esc_html__( 'milliseconds', 'nu-research' )
	);
}

/**
 * Render the settings page.
 */
function nu_skeleton_settings_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Skeleton Loading', 'nu-research' ); ?></h1>
		<?php settings_errors(); ?>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'nu_skeleton_group' );
			do_settings_sections( 'nu-skeleton' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/* -------------------------------------------------------------------------- *
 * Front-end assets + overlay.
 * -------------------------------------------------------------------------- */

/**
 * Enqueue the skeleton stylesheet and reveal script, and pass the effective
 * (admin-only) simulate delay to the script.
 */
function nu_skeleton_enqueue() {
	if ( is_admin() || ! nu_skeleton_enabled() ) {
		return;
	}

	wp_enqueue_style(
		'nu-research-skeleton',
		get_theme_file_uri( 'assets/css/skeleton.css' ),
		array( 'nu-research-main' ),
		(string) filemtime( get_theme_file_path( 'assets/css/skeleton.css' ) )
	);

	wp_enqueue_script(
		'nu-research-skeleton',
		get_theme_file_uri( 'assets/js/skeleton.js' ),
		array(),
		(string) filemtime( get_theme_file_path( 'assets/js/skeleton.js' ) ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_localize_script(
		'nu-research-skeleton',
		'nuSkeleton',
		array( 'delay' => nu_skeleton_effective_delay() )
	);
}
add_action( 'wp_enqueue_scripts', 'nu_skeleton_enqueue' );

/**
 * Map the current view to a skeleton partial name.
 *
 * @return string
 */
function nu_skeleton_partial_name() {
	if ( is_front_page() ) {
		return 'front';
	}

	$templates = array(
		'page-events.php'          => 'events',
		'page-press.php'           => 'press',
		'page-about.php'           => 'about',
		'page-highlights-team.php' => 'highlights-team',
		'page-apply.php'           => 'apply',
		'page-contact.php'         => 'contact',
	);

	foreach ( $templates as $template => $partial ) {
		if ( is_page_template( $template ) ) {
			return $partial;
		}
	}

	return 'generic';
}

/**
 * Print the skeleton overlay right after <body> opens. The shared header
 * placeholder is common to every view; the content placeholder is chosen per
 * template so it mirrors the real page's structure.
 */
function nu_skeleton_render() {
	if ( is_admin() || ! nu_skeleton_enabled() ) {
		return;
	}

	$partial = get_theme_file_path( 'template-parts/skeleton/' . nu_skeleton_partial_name() . '.php' );
	if ( ! file_exists( $partial ) ) {
		return;
	}
	?>
	<div class="nu-skeleton" aria-hidden="true">
		<?php require get_theme_file_path( 'template-parts/skeleton/header.php' ); ?>
		<div class="nu-skeleton-main">
			<?php require $partial; ?>
		</div>
	</div>
	<noscript><style>.nu-skeleton{display:none !important;}</style></noscript>
	<?php
}
add_action( 'wp_body_open', 'nu_skeleton_render' );

/**
 * Shared skeleton for the eyebrow + heading + intro "section header" pattern
 * (used across most templates). Reuses the real .section-header class so
 * spacing matches the live page.
 */
function nu_skeleton_section_header() {
	?>
	<div class="section-header">
		<span class="sk sk-eyebrow"></span>
		<span class="sk sk-h1 sk-w-60"></span>
		<span class="sk sk-line sk-w-90"></span>
		<span class="sk sk-line sk-w-75"></span>
	</div>
	<?php
}

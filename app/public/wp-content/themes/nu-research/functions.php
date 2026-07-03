<?php
/**
 * NU Research theme setup.
 *
 * @package nu-research
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NU_RESEARCH_VERSION', '1.0.0' );

/**
 * Theme supports and nav menus.
 */
function nu_research_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary program navigation', 'nu-research' ),
		)
	);
}
add_action( 'after_setup_theme', 'nu_research_setup' );

/**
 * Styles and scripts.
 */
function nu_research_enqueue() {
	wp_enqueue_style(
		'nu-research-fonts',
		'https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Libre+Baskerville:ital,wght@0,400;0,700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'nu-research-main',
		get_theme_file_uri( 'assets/css/main.css' ),
		array( 'nu-research-fonts' ),
		(string) filemtime( get_theme_file_path( 'assets/css/main.css' ) )
	);

	wp_enqueue_script(
		'nu-research-nav',
		get_theme_file_uri( 'assets/js/nav.js' ),
		array(),
		(string) filemtime( get_theme_file_path( 'assets/js/nav.js' ) ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'nu_research_enqueue' );

/**
 * Preconnect to the font host so text renders fast on first visit.
 *
 * @param array  $urls          Resource hint URLs.
 * @param string $relation_type Hint type being filtered.
 * @return array
 */
function nu_research_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
		$urls[] = array( 'href' => 'https://fonts.googleapis.com' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'nu_research_resource_hints', 10, 2 );

/**
 * Meta description: page excerpt when set, site tagline otherwise.
 */
function nu_research_meta_description() {
	$description = get_bloginfo( 'description' );

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post && has_excerpt( $post ) ) {
			$description = wp_strip_all_tags( get_the_excerpt( $post ) );
		}
	}

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}
}
add_action( 'wp_head', 'nu_research_meta_description', 1 );

/**
 * Helper: URL for a theme image asset.
 *
 * @param string $file Filename inside assets/img/.
 * @return string
 */
function nu_research_img( $file ) {
	return get_theme_file_uri( 'assets/img/' . sanitize_file_name( $file ) );
}

/**
 * Helper: home-relative page URL by slug, so templates never hard-code hosts.
 *
 * @param string $slug Page slug.
 * @return string
 */
function nu_research_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * Reusable section header (eyebrow + heading + intro), mirroring the design
 * system's SectionHeader component.
 *
 * @param string $eyebrow Overline label.
 * @param string $heading Heading text.
 * @param string $intro   Intro paragraph (optional).
 * @param string $tag     Heading tag, h1 or h2.
 */
function nu_research_section_header( $eyebrow, $heading, $intro = '', $tag = 'h2' ) {
	$tag = ( 'h1' === $tag ) ? 'h1' : 'h2';
	?>
	<div class="section-header">
		<?php if ( $eyebrow ) : ?>
			<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>
		<?php echo '<' . esc_html( $tag ) . ' class="section-heading">' . esc_html( $heading ) . '</' . esc_html( $tag ) . '>'; ?>
		<?php if ( $intro ) : ?>
			<p class="section-intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Reusable pill CTA button with the signature red arrow badge.
 *
 * @param string $label Button label.
 * @param string $url   Destination.
 */
function nu_research_cta( $label, $url ) {
	?>
	<a class="btn btn-primary" href="<?php echo esc_url( $url ); ?>">
		<span class="btn-label"><?php echo esc_html( $label ); ?></span>
		<span class="btn-arrow" aria-hidden="true">&rarr;</span>
	</a>
	<?php
}

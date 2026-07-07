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

// SCF-powered content blocks (hero, overview, media cards, CTA) in blocks/.
require_once get_theme_file_path( 'inc/scf-blocks.php' );

// Disclosure-pattern walker for the depth-2 primary navigation.
require_once get_theme_file_path( 'inc/nav-walker.php' );

// Skeleton loading screens + the Appearance -> Skeleton Loading settings.
require_once get_theme_file_path( 'inc/skeleton.php' );

// Spotlight-style search palette (header button + Cmd/Ctrl+S overlay).
require_once get_theme_file_path( 'inc/search.php' );

/**
 * Security response headers.
 */
function nu_research_security_headers() {
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
}
add_action( 'send_headers', 'nu_research_security_headers' );

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

	// Style the block-editor previews with the same CSS the front end uses, so
	// the home-page blocks look right inside the editor iframe.
	add_editor_style( 'assets/css/main.css' );

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
	// Fonts are self-hosted via @font-face in main.css — no external request.
	wp_enqueue_style(
		'nu-research-main',
		get_theme_file_uri( 'assets/css/main.css' ),
		array(),
		(string) filemtime( get_theme_file_path( 'assets/css/main.css' ) )
	);

	// AOS (Animate On Scroll) — loaded from CDN.
	wp_enqueue_style(
		'aos',
		'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css',
		array(),
		'2.3.4'
	);
	wp_enqueue_script(
		'aos',
		'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js',
		array(),
		'2.3.4',
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
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

	// Scroll-reactive logo marquee (nu/logo-marquee block).
	wp_enqueue_script(
		'nu-research-marquee',
		get_theme_file_uri( 'assets/js/marquee.js' ),
		array(),
		(string) filemtime( get_theme_file_path( 'assets/js/marquee.js' ) ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'nu_research_enqueue' );

/**
 * Head preloads: the two most-used self-hosted fonts (so text swaps in early),
 * plus the hero background image on the front page (the LCP element — a CSS
 * background can't be discovered by the preload scanner without this).
 */
function nu_research_preloads() {
	$fonts = array( 'assets/fonts/hankengrotesk-var.woff2', 'assets/fonts/sourceserif4-var.woff2' );
	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_theme_file_uri( $font ) )
		);
	}

	if ( is_front_page() ) {
		printf(
			'<link rel="preload" href="%s" as="image" fetchpriority="high">' . "\n",
			esc_url( nu_research_img( 'hero.jpg' ) )
		);
	}

	// The About page opens with a full-bleed photo — its LCP element.
	if ( is_page_template( 'page-about.php' ) ) {
		printf(
			'<link rel="preload" href="%s" as="image" fetchpriority="high">' . "\n",
			esc_url( nu_research_img( 'collab.jpg' ) )
		);
	}
}
add_action( 'wp_head', 'nu_research_preloads', 1 );

/**
 * Trim front-end weight for this classic, block-free theme: drop the block
 * editor's default CSS, the classic-theme shim, global styles, and the emoji
 * detection script — none of which this theme's markup uses. Keeps them in
 * the admin so the editor is unaffected.
 */
function nu_research_dequeue_unused() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
}
add_action( 'wp_enqueue_scripts', 'nu_research_dequeue_unused', 100 );

/**
 * Remove the emoji detection script/styles and the wp-embed script on the
 * front end (a main-thread task and two requests real visitors don't need).
 */
function nu_research_disable_extras() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	add_filter( 'emoji_svg_url', '__return_false' );

	// Core global styles emit an inline block of preset variables this theme
	// never uses. Removing the generating actions is more reliable than a
	// late wp_dequeue_style, which can miss the wp_footer-printed copy.
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
}
add_action( 'init', 'nu_research_disable_extras' );

/**
 * Drop the wp-embed script (used only for embedding this site elsewhere).
 */
function nu_research_dequeue_embed() {
	wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer', 'nu_research_dequeue_embed' );

/**
 * Meta description: page excerpt when set, site tagline otherwise.
 *
 * Yields to an SEO plugin (Yoast) when one is active — it owns the meta
 * description, Open Graph, canonical, and sitemap output, so emitting our own
 * would duplicate the tag.
 */
function nu_research_meta_description() {
	if ( defined( 'WPSEO_VERSION' ) ) {
		return;
	}

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
 * Reusable breadcrumb: Home › [ancestors…] › current page. Rendered on every
 * view except the front page, where it would be redundant.
 *
 * @param string $label     Current (non-linked) label. Defaults to the page title.
 * @param array  $ancestors Optional intermediate links between Home and the
 *                          current label, each array( 'label' => …, 'url' => … ).
 */
function nu_research_breadcrumb( $label = '', $ancestors = array() ) {
	if ( is_front_page() ) {
		return;
	}

	if ( '' === $label ) {
		$label = get_the_title();
	}

	$sep = '<span class="breadcrumb-sep" aria-hidden="true">&rsaquo;</span>';
	?>
	<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'nu-research' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'nu-research' ); ?></a>
		<?php
		foreach ( $ancestors as $crumb ) {
			if ( empty( $crumb['label'] ) ) {
				continue;
			}
			echo wp_kses_post( $sep );
			printf(
				'<a href="%s">%s</a>',
				esc_url( $crumb['url'] ),
				esc_html( $crumb['label'] )
			);
		}
		echo wp_kses_post( $sep );
		?>
		<span aria-current="page"><?php echo esc_html( $label ); ?></span>
	</nav>
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

/**
 * Tighten default excerpts for the card grid and use a subtle ellipsis.
 */
function nu_research_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'nu_research_excerpt_length' );

function nu_research_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'nu_research_excerpt_more' );

/**
 * Category filter bar for the blog listing and archives: an "All" pill plus one
 * pill per non-empty category, linking to each category archive. The pill for
 * the category currently being viewed is marked active.
 */
function nu_research_category_filter_bar() {
	$categories = get_categories( array( 'hide_empty' => true ) );
	if ( empty( $categories ) ) {
		return;
	}

	$blog_id  = (int) get_option( 'page_for_posts' );
	$blog_url = $blog_id ? get_permalink( $blog_id ) : home_url( '/' );
	$all_active = ! is_category();
	?>
	<nav class="filter-bar" aria-label="<?php esc_attr_e( 'Filter posts by category', 'nu-research' ); ?>">
		<a
			class="filter-pill<?php echo $all_active ? ' is-active' : ''; ?>"
			href="<?php echo esc_url( $blog_url ); ?>"
			<?php echo $all_active ? 'aria-current="page"' : ''; ?>
		><?php esc_html_e( 'All', 'nu-research' ); ?></a>
		<?php
		foreach ( $categories as $category ) :
			$active = is_category( $category->term_id );
			?>
			<a
				class="filter-pill<?php echo $active ? ' is-active' : ''; ?>"
				href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>"
				<?php echo $active ? 'aria-current="page"' : ''; ?>
			><?php echo esc_html( $category->name ); ?></a>
		<?php endforeach; ?>
	</nav>
	<?php
}

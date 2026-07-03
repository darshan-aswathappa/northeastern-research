<?php
/**
 * Home-page content blocks.
 *
 * The front page is a real, editable WordPress page (Pages -> Home). Its
 * hero / overview / media / CTA sections are these server-rendered blocks, so
 * an editor can see and change the copy from the admin dashboard while the
 * front-end markup stays byte-identical to the original hard-coded template —
 * each render callback reuses the same theme helpers (nu_research_cta,
 * nu_research_section_header) the template used.
 *
 * @package nu-research
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the blocks and their server-side render callbacks.
 */
function nu_research_register_blocks() {
	register_block_type(
		'nu/hero',
		array(
			'render_callback' => 'nu_research_render_hero',
			'attributes'      => array(
				'eyebrow'  => array( 'type' => 'string', 'default' => 'Summer Research Program' ),
				'heading'  => array( 'type' => 'string', 'default' => 'WordPress Research Fellows' ),
				'lead'     => array( 'type' => 'string', 'default' => 'A 10-week paid summer fellowship where undergraduate Software Engineering students build, test, and ship real WordPress tooling alongside faculty mentors.' ),
				'ctaLabel' => array( 'type' => 'string', 'default' => 'Apply Now' ),
				'ctaSlug'  => array( 'type' => 'string', 'default' => 'apply-eligibility' ),
				'image'    => array( 'type' => 'string', 'default' => 'hero.jpg' ),
			),
		)
	);

	register_block_type(
		'nu/section-header',
		array(
			'render_callback' => 'nu_research_render_section_header',
			'attributes'      => array(
				'eyebrow' => array( 'type' => 'string', 'default' => 'Program Overview' ),
				'heading' => array( 'type' => 'string', 'default' => 'Build the tools you use every day' ),
				'intro'   => array( 'type' => 'string', 'default' => 'The WordPress Research Fellows Program pairs Software Engineering undergraduates with faculty mentors to solve real problems in plugin architecture, editor UX, performance, security, and accessibility — the same systems that power the department’s own web infrastructure.' ),
			),
		)
	);

	register_block_type(
		'nu/media-card',
		array(
			'render_callback' => 'nu_research_render_media_card',
			'attributes'      => array(
				'image'    => array( 'type' => 'string', 'default' => 'mentor.jpg' ),
				'imageAlt' => array( 'type' => 'string', 'default' => 'Fellow working at a laptop with a mentor' ),
				'heading'  => array( 'type' => 'string', 'default' => 'Hands-on, mentored research' ),
				'body'     => array( 'type' => 'string', 'default' => 'Each fellow is paired with a faculty mentor and joins a small track team focused on one problem area — plugin architecture, editor UX, performance, or accessibility. No prior WordPress experience required, just solid PHP or JS fundamentals.' ),
				'reverse'  => array( 'type' => 'boolean', 'default' => false ),
				'section'  => array( 'type' => 'string', 'default' => 'section section-tight' ),
			),
		)
	);

	register_block_type(
		'nu/track-badges',
		array(
			'render_callback' => 'nu_research_render_track_badges',
			'attributes'      => array(
				'label'  => array( 'type' => 'string', 'default' => 'Research tracks' ),
				'tracks' => array(
					'type'    => 'array',
					'items'   => array( 'type' => 'string' ),
					'default' => array(
						'Plugin Architecture',
						'Editor & Block UX',
						'Performance & Security',
						'Accessibility',
						'Developer Tooling',
						'Cloud & Deployment',
					),
				),
			),
		)
	);

	register_block_type(
		'nu/cta-band',
		array(
			'render_callback' => 'nu_research_render_cta_band',
			'attributes'      => array(
				'heading'  => array( 'type' => 'string', 'default' => 'Ready to spend your summer building?' ),
				'lead'     => array( 'type' => 'string', 'default' => 'Applications for Summer 2027 open December 1, 2026.' ),
				'ctaLabel' => array( 'type' => 'string', 'default' => 'See Eligibility & Deadlines' ),
				'ctaSlug'  => array( 'type' => 'string', 'default' => 'apply-eligibility' ),
			),
		)
	);
}
add_action( 'init', 'nu_research_register_blocks' );

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
 * Editor-only script that registers the block UIs (no build step required).
 */
function nu_research_block_editor_assets() {
	wp_enqueue_script(
		'nu-research-blocks',
		get_theme_file_uri( 'assets/js/blocks.js' ),
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
		(string) filemtime( get_theme_file_path( 'assets/js/blocks.js' ) ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'nu_research_block_editor_assets' );

/* -------------------------------------------------------------------------- *
 * Render callbacks. Each mirrors the original front-page.php markup exactly.
 * -------------------------------------------------------------------------- */

/**
 * Hero section.
 *
 * @param array $a Block attributes.
 * @return string
 */
function nu_research_render_hero( $a ) {
	ob_start();
	?>
	<section class="hero" style="background-image:url('<?php echo esc_url( nu_research_img( $a['image'] ) ); ?>');">
		<div class="hero-overlay">
			<div class="wrap">
				<div class="hero-content">
					<p class="eyebrow eyebrow-on-dark"><?php echo esc_html( $a['eyebrow'] ); ?></p>
					<h1 class="hero-heading"><?php echo esc_html( $a['heading'] ); ?></h1>
					<p class="hero-lead"><?php echo esc_html( $a['lead'] ); ?></p>
					<?php nu_research_cta( $a['ctaLabel'], nu_research_page_url( $a['ctaSlug'] ) ); ?>
				</div>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Program-overview section header.
 *
 * @param array $a Block attributes.
 * @return string
 */
function nu_research_render_section_header( $a ) {
	ob_start();
	?>
	<section class="section">
		<div class="wrap">
			<?php nu_research_section_header( $a['eyebrow'], $a['heading'], $a['intro'] ); ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Media card (image + heading + body), optionally reversed.
 *
 * @param array $a Block attributes.
 * @return string
 */
function nu_research_render_media_card( $a ) {
	$card_class = 'media-card' . ( $a['reverse'] ? ' media-card-reverse' : '' );
	ob_start();
	?>
	<section class="<?php echo esc_attr( $a['section'] ); ?>">
		<div class="wrap">
			<div class="<?php echo esc_attr( $card_class ); ?>">
				<div class="media-card-image">
					<img src="<?php echo esc_url( nu_research_img( $a['image'] ) ); ?>" alt="<?php echo esc_attr( $a['imageAlt'] ); ?>" width="1000" height="750" loading="lazy">
				</div>
				<div class="media-card-body">
					<h2><?php echo esc_html( $a['heading'] ); ?></h2>
					<p><?php echo esc_html( $a['body'] ); ?></p>
				</div>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Research-track badge row.
 *
 * @param array $a Block attributes.
 * @return string
 */
function nu_research_render_track_badges( $a ) {
	$tracks = is_array( $a['tracks'] ) ? $a['tracks'] : array();
	ob_start();
	?>
	<section class="section section-tight" aria-label="<?php echo esc_attr( $a['label'] ); ?>">
		<div class="wrap">
			<ul class="badge-row">
				<?php foreach ( $tracks as $track ) : ?>
					<li class="badge badge-outline"><?php echo esc_html( $track ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Closing call-to-action band.
 *
 * @param array $a Block attributes.
 * @return string
 */
function nu_research_render_cta_band( $a ) {
	ob_start();
	?>
	<section class="section section-cta">
		<div class="wrap cta-wrap">
			<h2><?php echo esc_html( $a['heading'] ); ?></h2>
			<p class="cta-lead"><?php echo esc_html( $a['lead'] ); ?></p>
			<?php nu_research_cta( $a['ctaLabel'], nu_research_page_url( $a['ctaSlug'] ) ); ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

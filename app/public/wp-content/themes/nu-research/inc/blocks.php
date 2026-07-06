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
				'eyebrow'  => array(
					'type'    => 'string',
					'default' => 'Summer Research Program',
				),
				'heading'  => array(
					'type'    => 'string',
					'default' => 'WordPress Research Fellows',
				),
				'lead'     => array(
					'type'    => 'string',
					'default' => 'A 10-week paid summer fellowship where undergraduate Software Engineering students build, test, and ship real WordPress tooling alongside faculty mentors.',
				),
				'ctaLabel' => array(
					'type'    => 'string',
					'default' => 'Apply Now',
				),
				'ctaSlug'  => array(
					'type'    => 'string',
					'default' => 'apply-eligibility',
				),
				'image'    => array(
					'type'    => 'string',
					'default' => 'hero.jpg',
				),
			),
		)
	);

	register_block_type(
		'nu/section-header',
		array(
			'render_callback' => 'nu_research_render_section_header',
			'attributes'      => array(
				'eyebrow' => array(
					'type'    => 'string',
					'default' => 'Program Overview',
				),
				'heading' => array(
					'type'    => 'string',
					'default' => 'Build the tools you use every day',
				),
				'intro'   => array(
					'type'    => 'string',
					'default' => 'The WordPress Research Fellows Program pairs Software Engineering undergraduates with faculty mentors to solve real problems in plugin architecture, editor UX, performance, security, and accessibility — the same systems that power the department’s own web infrastructure.',
				),
			),
		)
	);

	register_block_type(
		'nu/media-card',
		array(
			'render_callback' => 'nu_research_render_media_card',
			'attributes'      => array(
				'image'    => array(
					'type'    => 'string',
					'default' => 'mentor.jpg',
				),
				'imageAlt' => array(
					'type'    => 'string',
					'default' => 'Fellow working at a laptop with a mentor',
				),
				'heading'  => array(
					'type'    => 'string',
					'default' => 'Hands-on, mentored research',
				),
				'body'     => array(
					'type'    => 'string',
					'default' => 'Each fellow is paired with a faculty mentor and joins a small track team focused on one problem area — plugin architecture, editor UX, performance, or accessibility. No prior WordPress experience required, just solid PHP or JS fundamentals.',
				),
				'reverse'  => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'section'  => array(
					'type'    => 'string',
					'default' => 'section section-tight',
				),
			),
		)
	);

	register_block_type(
		'nu/track-badges',
		array(
			'render_callback' => 'nu_research_render_track_badges',
			'attributes'      => array(
				'label'  => array(
					'type'    => 'string',
					'default' => 'Research tracks',
				),
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
				'heading'  => array(
					'type'    => 'string',
					'default' => 'Ready to spend your summer building?',
				),
				'lead'     => array(
					'type'    => 'string',
					'default' => 'Applications for Summer 2027 open December 1, 2026.',
				),
				'ctaLabel' => array(
					'type'    => 'string',
					'default' => 'See Eligibility & Deadlines',
				),
				'ctaSlug'  => array(
					'type'    => 'string',
					'default' => 'apply-eligibility',
				),
			),
		)
	);

	register_block_type(
		'nu/hero-billboard',
		array(
			'render_callback' => 'nu_research_render_hero_billboard',
			'attributes'      => array(
				'eyebrow'  => array(
					'type'    => 'string',
					'default' => 'WordPress Research Fellows',
				),
				'heading'  => array(
					'type'    => 'string',
					'default' => 'Build. Test. Ship.',
				),
				'lead'     => array(
					'type'    => 'string',
					'default' => 'A 10-week paid summer fellowship where undergraduate Software Engineering students build, test, and ship real WordPress tooling alongside faculty mentors.',
				),
				'ctaLabel' => array(
					'type'    => 'string',
					'default' => 'Apply Now',
				),
				'ctaSlug'  => array(
					'type'    => 'string',
					'default' => 'apply-eligibility',
				),
				'image'    => array(
					'type'    => 'string',
					'default' => 'hero.jpg',
				),
				'imageAlt' => array(
					'type'    => 'string',
					'default' => 'Fellows working together in a research lab',
				),
			),
		)
	);

	register_block_type(
		'nu/pillars',
		array(
			'render_callback' => 'nu_research_render_pillars',
			'attributes'      => array(
				'heading' => array(
					'type'    => 'string',
					'default' => 'A deep commitment to your research success',
				),
				'intro'   => array(
					'type'    => 'string',
					'default' => '',
				),
				'items'   => array(
					'type'    => 'array',
					'items'   => array( 'type' => 'string' ),
					'default' => array(),
				),
			),
		)
	);

	register_block_type(
		'nu/ambition',
		array(
			'render_callback' => 'nu_research_render_ambition',
			'attributes'      => array(
				'eyebrow'           => array(
					'type'    => 'string',
					'default' => 'Empower Your Ambition',
				),
				'heading'           => array(
					'type'    => 'string',
					'default' => 'Research experience that advances careers',
				),
				'lead'              => array(
					'type'    => 'string',
					'default' => 'Take your career in a new direction with hands-on WordPress research, a paid 10-week fellowship, and mentored, experience-driven work that will set you far ahead of the pack.',
				),
				'statValue'         => array(
					'type'    => 'string',
					'default' => '#1',
				),
				'statCaption'       => array(
					'type'    => 'string',
					'default' => 'University for co-ops and internships (U.S. News & World Report, 2025)',
				),
				'imagePrimary'      => array(
					'type'    => 'string',
					'default' => 'mentor.jpg',
				),
				'imagePrimaryAlt'   => array(
					'type'    => 'string',
					'default' => 'A fellow smiling while working alongside a mentor',
				),
				'imageSecondary'    => array(
					'type'    => 'string',
					'default' => 'hero.jpg',
				),
				'imageSecondaryAlt' => array(
					'type'    => 'string',
					'default' => 'Fellows working together in a research lab',
				),
			),
		)
	);

	register_block_type(
		'nu/journey-cards',
		array(
			'render_callback' => 'nu_research_render_journey_cards',
			'attributes'      => array(
				'label' => array(
					'type'    => 'string',
					'default' => 'Your research journey',
				),
				'cards' => array(
					'type'    => 'array',
					'items'   => array( 'type' => 'string' ),
					'default' => array(),
				),
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

/*
-------------------------------------------------------------------------- *
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
	<section class="section" data-aos="fade-up">
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
	$card_class  = 'media-card' . ( $a['reverse'] ? ' media-card-reverse' : '' );
	$img_aos     = $a['reverse'] ? 'fade-left' : 'fade-right';
	$body_aos    = $a['reverse'] ? 'fade-right' : 'fade-left';
	ob_start();
	?>
	<section class="<?php echo esc_attr( $a['section'] ); ?>">
		<div class="wrap">
			<div class="<?php echo esc_attr( $card_class ); ?>">
				<div class="media-card-image" data-aos="<?php echo esc_attr( $img_aos ); ?>">
					<img src="<?php echo esc_url( nu_research_img( $a['image'] ) ); ?>" alt="<?php echo esc_attr( $a['imageAlt'] ); ?>" width="1000" height="750" loading="lazy">
				</div>
				<div class="media-card-body" data-aos="<?php echo esc_attr( $body_aos ); ?>" data-aos-delay="100">
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
				<?php foreach ( $tracks as $i => $track ) : ?>
					<li class="badge badge-outline" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $i * 75 ); ?>"><?php echo esc_html( $track ); ?></li>
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
		<div class="wrap cta-wrap" data-aos="fade-up">
			<h2><?php echo esc_html( $a['heading'] ); ?></h2>
			<p class="cta-lead"><?php echo esc_html( $a['lead'] ); ?></p>
			<?php nu_research_cta( $a['ctaLabel'], nu_research_page_url( $a['ctaSlug'] ) ); ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

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
 * Full-black billboard hero: eyebrow, large serif heading, lead, CTA, and the
 * photo on a side panel.
 *
 * @param array $a Block attributes.
 * @return string
 */
function nu_research_render_hero_billboard( $a ) {
	ob_start();
	?>
	<section class="hero-billboard">
		<div class="hero-billboard-inner wrap">
			<div class="hero-billboard-content">
				<p class="eyebrow eyebrow-on-dark"><?php echo esc_html( $a['eyebrow'] ); ?></p>
				<h1 class="hero-billboard-heading"><?php echo esc_html( $a['heading'] ); ?></h1>
				<?php if ( $a['lead'] ) : ?>
					<p class="hero-billboard-lead"><?php echo esc_html( $a['lead'] ); ?></p>
				<?php endif; ?>
				<?php nu_research_cta( $a['ctaLabel'], nu_research_page_url( $a['ctaSlug'] ) ); ?>
			</div>
			<div class="hero-billboard-media">
				<img src="<?php echo esc_url( nu_research_img( $a['image'] ) ); ?>" alt="<?php echo esc_attr( $a['imageAlt'] ); ?>" width="1600" height="900" fetchpriority="high">
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Commitment pillars: section heading + intro over a three-up grid of
 * icon / title / body items. Items are pipe-delimited "icon|title|body".
 *
 * @param array $a Block attributes.
 * @return string
 */
function nu_research_render_pillars( $a ) {
	$items = is_array( $a['items'] ) ? $a['items'] : array();
	ob_start();
	?>
	<section class="section pillars-section">
		<div class="wrap">
			<div class="section-header">
				<h2 class="pillars-heading"><?php echo esc_html( $a['heading'] ); ?></h2>
				<?php if ( $a['intro'] ) : ?>
					<p class="section-intro"><?php echo esc_html( $a['intro'] ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $items ) : ?>
				<ul class="pillars-grid">
					<?php
					foreach ( $items as $i => $item ) :
						$parts = array_pad( explode( '|', $item, 3 ), 3, '' );
						?>
						<li class="pillar" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $i * 100 ); ?>">
							<?php nu_research_pillar_icon( sanitize_key( $parts[0] ) ); ?>
							<h3 class="pillar-title"><?php echo esc_html( $parts[1] ); ?></h3>
							<p class="pillar-body"><?php echo esc_html( $parts[2] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Ambition banner: flat black band with a photo collage — large photo over a
 * red stat tile and a second photo — beside eyebrow / serif heading / lead.
 * On small screens the copy leads and the second photo is dropped, matching
 * the design system's mobile treatment.
 *
 * @param array $a Block attributes.
 * @return string
 */
function nu_research_render_ambition( $a ) {
	ob_start();
	?>
	<section class="section ambition-section">
		<div class="wrap ambition-inner">
			<div class="ambition-copy" data-aos="fade-up">
				<p class="eyebrow eyebrow-on-dark"><?php echo esc_html( $a['eyebrow'] ); ?></p>
				<h2 class="ambition-heading"><?php echo esc_html( $a['heading'] ); ?></h2>
				<p class="ambition-lead"><?php echo esc_html( $a['lead'] ); ?></p>
			</div>
			<div class="ambition-media" data-aos="fade-up" data-aos-delay="100">
				<div class="ambition-photo ambition-photo-primary">
					<img src="<?php echo esc_url( nu_research_img( $a['imagePrimary'] ) ); ?>" alt="<?php echo esc_attr( $a['imagePrimaryAlt'] ); ?>" width="1600" height="900" loading="lazy">
				</div>
				<div class="ambition-stat">
					<p class="ambition-stat-value"><?php echo esc_html( $a['statValue'] ); ?></p>
					<p class="ambition-stat-caption"><?php echo esc_html( $a['statCaption'] ); ?></p>
				</div>
				<div class="ambition-photo ambition-photo-secondary">
					<img src="<?php echo esc_url( nu_research_img( $a['imageSecondary'] ) ); ?>" alt="<?php echo esc_attr( $a['imageSecondaryAlt'] ); ?>" width="1000" height="750" loading="lazy">
				</div>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Research-journey cards: a labelled three-up grid of photo cards, each with a
 * red arrow text link. Cards are pipe-delimited
 * "image|alt|title|body|ctaLabel|slug".
 *
 * @param array $a Block attributes.
 * @return string
 */
function nu_research_render_journey_cards( $a ) {
	$cards = is_array( $a['cards'] ) ? $a['cards'] : array();
	ob_start();
	?>
	<section class="section journey-section">
		<div class="wrap">
			<div class="section-header">
				<h2 class="journey-heading"><?php echo esc_html( $a['label'] ); ?></h2>
			</div>
			<?php if ( $cards ) : ?>
				<ul class="journey-grid">
					<?php
					foreach ( $cards as $i => $card ) :
						$parts = array_pad( explode( '|', $card, 6 ), 6, '' );
						?>
						<li class="journey-card" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $i * 100 ); ?>">
							<div class="journey-card-media ratio-4-3">
								<img src="<?php echo esc_url( nu_research_img( $parts[0] ) ); ?>" alt="<?php echo esc_attr( $parts[1] ); ?>" width="1000" height="750" loading="lazy">
							</div>
							<h3 class="journey-card-title"><?php echo esc_html( $parts[2] ); ?></h3>
							<p class="journey-card-body"><?php echo esc_html( $parts[3] ); ?></p>
							<?php if ( $parts[4] ) : ?>
								<a class="arrow-link" href="<?php echo esc_url( nu_research_page_url( $parts[5] ) ); ?>">
									<?php echo esc_html( $parts[4] ); ?><span class="arrow-link-glyph" aria-hidden="true">&rarr;</span>
								</a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

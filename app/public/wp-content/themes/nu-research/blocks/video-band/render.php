<?php
/**
 * Fellows: Video Band — a full-width self-hosted HTML5 player. The file lives
 * in the theme's assets/media/ directory so the embed stays same-origin (no
 * mixed-content or third-party requests).
 *
 * @package nu-research
 */

$nu_video    = get_field( 'video' );
$nu_poster   = get_field( 'poster' );
$nu_label    = get_field( 'label' );
$nu_captions = get_field( 'captions' );

$nu_video_url    = get_theme_file_uri( 'assets/media/' . sanitize_file_name( $nu_video ) );
$nu_poster_url   = nu_research_img( $nu_poster ? $nu_poster : 'collab.jpg' );
$nu_captions_url = $nu_captions ? get_theme_file_uri( 'assets/media/' . sanitize_file_name( $nu_captions ) ) : '';
?>
<section class="section video-band" aria-label="<?php echo esc_attr( $nu_label ); ?>">
	<div class="wrap" data-aos="fade-up">
		<video class="video-band-player" controls preload="metadata" playsinline aria-label="<?php echo esc_attr( $nu_label ); ?>"<?php echo $nu_poster_url ? ' poster="' . esc_url( $nu_poster_url ) . '"' : ''; ?>>
			<source src="<?php echo esc_url( $nu_video_url ); ?>" type="video/mp4">
			<?php if ( $nu_captions_url ) : ?>
				<track kind="captions" src="<?php echo esc_url( $nu_captions_url ); ?>" srclang="en" label="English" default>
			<?php endif; ?>
			<p class="video-band-fallback">
				<?php
				printf(
					/* translators: %s: link to download the video file. */
					esc_html__( 'Your browser does not support embedded video. %s', 'nu-research' ),
					'<a href="' . esc_url( $nu_video_url ) . '">' . esc_html__( 'Download the video', 'nu-research' ) . '</a>'
				);
				?>
			</p>
		</video>
	</div>
</section>

/**
 * Photo marquee: the strip drifts left on its own, speeds up with the page's
 * scroll velocity, and reverses direction when the visitor scrolls up. The
 * track holds two identical groups, so translating within one group-width
 * loops seamlessly. Honors prefers-reduced-motion (strip stays static) and
 * only animates while on screen.
 */
( () => {
	'use strict';

	const marquees = document.querySelectorAll( '[data-marquee]' );
	if ( ! marquees.length || window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	const BASE_SPEED = 0.5; // px per frame at rest
	const MAX_BOOST = 18; // cap on scroll-driven extra speed
	const VELOCITY_GAIN = 0.18; // how strongly page scroll feeds the strip
	const VELOCITY_DECAY = 0.92; // per-frame easing back to the base drift

	let lastScrollY = window.scrollY;
	let scrollDelta = 0;

	window.addEventListener(
		'scroll',
		() => {
			scrollDelta += window.scrollY - lastScrollY;
			lastScrollY = window.scrollY;
		},
		{ passive: true }
	);

	marquees.forEach( ( marquee ) => {
		const track = marquee.querySelector( '[data-marquee-track]' );
		const group = marquee.querySelector( '.photo-marquee-group' );
		if ( ! track || ! group ) {
			return;
		}

		let offset = 0;
		let velocity = 0;
		let direction = 1; // 1 = leftward drift (scrolling down), -1 = rightward
		let visible = false;
		let running = false;

		const frame = () => {
			if ( ! visible ) {
				running = false;
				return;
			}

			if ( scrollDelta !== 0 ) {
				direction = scrollDelta > 0 ? 1 : -1;
				velocity += Math.abs( scrollDelta ) * VELOCITY_GAIN;
				scrollDelta = 0;
			}
			velocity = Math.min( velocity * VELOCITY_DECAY, MAX_BOOST );

			// offsetWidth includes each image's right margin, so one group-width
			// is exactly the seamless wrap distance.
			const width = group.offsetWidth;
			if ( width > 0 ) {
				offset = ( ( ( offset + direction * ( BASE_SPEED + velocity ) ) % width ) + width ) % width;
				track.style.transform = `translate3d(${ -offset }px, 0, 0)`;
			}

			requestAnimationFrame( frame );
		};

		new IntersectionObserver( ( entries ) => {
			visible = entries[ 0 ].isIntersecting;
			if ( visible && ! running ) {
				running = true;
				requestAnimationFrame( frame );
			}
		} ).observe( marquee );
	} );
} )();

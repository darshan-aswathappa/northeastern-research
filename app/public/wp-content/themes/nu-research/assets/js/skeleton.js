/**
 * Skeleton reveal.
 *
 * The overlay is server-rendered and visible on first paint. This script fades
 * it out once the page has loaded (plus an optional admin-only preview delay
 * passed in nuSkeleton.delay), then removes it from the DOM. A hard failsafe
 * guarantees the overlay is never left up if a resource hangs.
 */
( function () {
	'use strict';

	var overlay = document.querySelector( '.nu-skeleton' );
	if ( ! overlay ) {
		return;
	}

	var config = window.nuSkeleton || {};
	var delay = parseInt( config.delay, 10 );
	if ( isNaN( delay ) || delay < 0 ) {
		delay = 0;
	}

	var reduceMotion = !! ( window.matchMedia &&
		window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches );

	var hidden = false;

	function remove() {
		if ( overlay && overlay.parentNode ) {
			overlay.parentNode.removeChild( overlay );
		}
	}

	function hide() {
		if ( hidden ) {
			return;
		}
		hidden = true;

		if ( reduceMotion ) {
			remove();
			return;
		}

		overlay.addEventListener( 'transitionend', remove, { once: true } );
		overlay.classList.add( 'is-hiding' );
		// Fallback in case the transition never fires (e.g. tab backgrounded).
		window.setTimeout( remove, 600 );
	}

	function scheduleHide() {
		window.setTimeout( hide, delay );
	}

	if ( document.readyState === 'complete' ) {
		scheduleHide();
	} else {
		window.addEventListener( 'load', scheduleHide );
	}

	// Failsafe: never leave the overlay covering the page for a real visitor if
	// an asset stalls. Bounded above the preview delay so the simulation still
	// runs to completion for administrators.
	window.setTimeout( hide, delay + 6000 );
}() );

/**
 * Accessible mobile navigation disclosure.
 *
 * Progressive enhancement: without JS the nav is simply always visible
 * (the collapsed state only applies once `.nav-ready` is set on <html>).
 */
( function () {
	'use strict';

	var toggle = document.querySelector( '.nav-toggle' );
	var nav = document.getElementById( 'primary-nav' );
	var mq = window.matchMedia( '(min-width: 768px)' );

	if ( ! toggle || ! nav ) {
		return;
	}

	document.documentElement.classList.add( 'nav-ready' );

	function setOpen( open ) {
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		nav.setAttribute( 'data-collapsed', open ? 'false' : 'true' );
	}

	// Start collapsed on small screens only.
	setOpen( mq.matches );

	toggle.addEventListener( 'click', function () {
		setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' );
	} );

	// Escape closes the menu and returns focus to the toggle.
	nav.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key && ! mq.matches ) {
			setOpen( false );
			toggle.focus();
		}
	} );

	// Keep state sane across viewport changes.
	mq.addEventListener( 'change', function ( e ) {
		setOpen( e.matches );
	} );
} )();

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
	var mq = window.matchMedia( '(min-width: 1050px)' );

	if ( ! toggle || ! nav ) {
		return;
	}

	document.documentElement.classList.add( 'nav-ready' );

	function setOpen( open ) {
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		nav.setAttribute( 'data-collapsed', open ? 'false' : 'true' );
		// Drive the full-screen overlay on mobile/tablet via a body class.
		// On desktop the CSS media query (max-width: 1049px) ensures this is a no-op.
		document.body.classList.toggle( 'nav-open', open && ! mq.matches );
	}

	// Start collapsed on small screens only.
	setOpen( mq.matches );

	toggle.addEventListener( 'click', function () {
		setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' );
	} );

	/* --- Submenu disclosures (depth-2 dropdowns / mobile accordions) ------ */

	var subToggles = Array.prototype.slice.call(
		nav.querySelectorAll( '.submenu-toggle' )
	);

	function submenuFor( btn ) {
		return document.getElementById( btn.getAttribute( 'aria-controls' ) );
	}

	function setSubmenu( btn, open ) {
		btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		var panel = submenuFor( btn );
		if ( panel ) {
			panel.setAttribute( 'data-open', open ? 'true' : 'false' );
		}
	}

	function closeAllSubmenus( except ) {
		subToggles.forEach( function ( btn ) {
			if ( btn !== except ) {
				setSubmenu( btn, false );
			}
		} );
	}

	// Buttons ship hidden so no-JS visitors (who see submenus expanded) never
	// meet a dead control. Reveal them and start every submenu collapsed.
	subToggles.forEach( function ( btn ) {
		btn.removeAttribute( 'hidden' );
		setSubmenu( btn, false );

		btn.addEventListener( 'click', function () {
			var open = btn.getAttribute( 'aria-expanded' ) !== 'true';
			closeAllSubmenus( btn );
			setSubmenu( btn, open );
		} );
	} );

	// Escape: close an open submenu first (focus back on its toggle);
	// otherwise, on mobile, close the whole nav.
	nav.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' !== e.key ) {
			return;
		}

		var openBtn = null;
		subToggles.forEach( function ( btn ) {
			var panel = submenuFor( btn );
			if (
				'true' === btn.getAttribute( 'aria-expanded' ) &&
				( btn === e.target || ( panel && panel.contains( e.target ) ) )
			) {
				openBtn = btn;
			}
		} );

		if ( openBtn ) {
			setSubmenu( openBtn, false );
			openBtn.focus();
			return;
		}

		if ( ! mq.matches ) {
			setOpen( false );
			toggle.focus();
		}
	} );

	// Light dismiss: clicking or tabbing outside the nav closes any open
	// dropdown (matches how visitors expect desktop dropdowns to behave).
	document.addEventListener( 'click', function ( e ) {
		if ( ! nav.contains( e.target ) ) {
			closeAllSubmenus( null );
		}
	} );

	document.addEventListener( 'focusin', function ( e ) {
		if ( ! nav.contains( e.target ) ) {
			closeAllSubmenus( null );
		}
	} );

	// Keep state sane across viewport changes.
	mq.addEventListener( 'change', function ( e ) {
		setOpen( e.matches );
		closeAllSubmenus( null );
	} );
} )();

// Initialise AOS after all deferred scripts have loaded.
window.addEventListener( 'load', function () {
	if ( typeof AOS !== 'undefined' ) {
		AOS.init( {
			duration: 600,   // ms per animation
			once:     true,  // animate only the first time the element enters view
			offset:   80,    // px from viewport edge before triggering
		} );
	}
} );

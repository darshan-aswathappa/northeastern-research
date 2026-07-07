/**
 * Spotlight-style site search (demo).
 *
 * A centered command-palette overlay opened by the header search button or the
 * Cmd/Ctrl+S shortcut. This is a front-end demo: it filters a fixed list of
 * pages passed in from PHP — there's no server query. Enter opens the
 * highlighted result.
 */
( function () {
	'use strict';

	var cfg = window.nuResearchSearch || {};
	var items = Array.isArray( cfg.items ) ? cfg.items : [];
	var modal = document.getElementById( 'site-search-modal' );
	var trigger = document.querySelector( '.search-trigger' );

	if ( ! modal ) {
		return;
	}

	var panel = modal.querySelector( '.search-modal__panel' );
	var input = modal.querySelector( '.search-modal__input' );
	var results = modal.querySelector( '.search-modal__results' );
	var status = modal.querySelector( '.search-modal__status' );
	var lastFocused = null;
	var debounceTimer = null;
	var activeIndex = -1;

	/* --- Open / close ------------------------------------------------------ */

	function isOpen() {
		return modal.getAttribute( 'data-open' ) === 'true';
	}

	function open() {
		if ( isOpen() ) {
			return;
		}
		lastFocused = document.activeElement;
		modal.hidden = false;
		// Force a reflow so the enter transition runs from the hidden state.
		void modal.offsetWidth;
		modal.setAttribute( 'data-open', 'true' );
		document.documentElement.classList.add( 'search-open' );
		if ( trigger ) {
			trigger.setAttribute( 'aria-expanded', 'true' );
		}
		input.value = '';
		input.focus();
		render( items ); // Show all pages as suggestions on open.
	}

	function close() {
		if ( ! isOpen() ) {
			return;
		}
		modal.setAttribute( 'data-open', 'false' );
		document.documentElement.classList.remove( 'search-open' );
		if ( trigger ) {
			trigger.setAttribute( 'aria-expanded', 'false' );
		}
		clearResults();

		// Hide after the exit transition so it leaves the tab order.
		window.setTimeout( function () {
			if ( ! isOpen() ) {
				modal.hidden = true;
			}
		}, 200 );

		if ( lastFocused && typeof lastFocused.focus === 'function' ) {
			lastFocused.focus();
		}
	}

	/* --- Results ----------------------------------------------------------- */

	function clearResults() {
		results.textContent = '';
		results.hidden = true;
		activeIndex = -1;
		input.setAttribute( 'aria-expanded', 'false' );
		input.removeAttribute( 'aria-activedescendant' );
		setStatus( '' );
	}

	function setStatus( text ) {
		if ( status ) {
			status.textContent = text;
		}
	}

	// Build each row with DOM methods + textContent so nothing is interpreted
	// as HTML (no innerHTML with dynamic strings).
	function buildResult( item, index ) {
		var li = document.createElement( 'li' );
		li.className = 'search-modal__result';
		li.id = 'search-result-' + index;
		li.setAttribute( 'role', 'option' );
		li.setAttribute( 'aria-selected', 'false' );

		var link = document.createElement( 'a' );
		link.className = 'search-modal__result-link';
		link.href = item.url || '#';
		link.tabIndex = -1;

		var title = document.createElement( 'span' );
		title.className = 'search-modal__result-title';
		title.textContent = item.title || '';
		link.appendChild( title );

		if ( item.kind ) {
			var kind = document.createElement( 'span' );
			kind.className = 'search-modal__result-kind';
			kind.textContent = item.kind;
			link.appendChild( kind );
		}

		li.appendChild( link );
		li.addEventListener( 'mousemove', function () {
			setActive( index );
		} );

		return li;
	}

	function render( list ) {
		results.textContent = '';
		activeIndex = -1;

		if ( ! list.length ) {
			results.hidden = true;
			input.setAttribute( 'aria-expanded', 'false' );
			setStatus( 'No matches for this demo search.' );
			return;
		}

		list.forEach( function ( item, i ) {
			results.appendChild( buildResult( item, i ) );
		} );

		results.hidden = false;
		input.setAttribute( 'aria-expanded', 'true' );
		setStatus(
			list.length + ( list.length === 1 ? ' result.' : ' results.' ) +
			' Use arrow keys to browse.'
		);
	}

	function filter( query ) {
		var q = query.toLowerCase();
		return items.filter( function ( item ) {
			return (
				( item.title && item.title.toLowerCase().indexOf( q ) > -1 ) ||
				( item.kind && item.kind.toLowerCase().indexOf( q ) > -1 )
			);
		} );
	}

	function optionEls() {
		return Array.prototype.slice.call( results.querySelectorAll( '.search-modal__result' ) );
	}

	function setActive( index ) {
		var options = optionEls();
		if ( ! options.length ) {
			return;
		}
		if ( index < 0 ) {
			index = options.length - 1;
		} else if ( index >= options.length ) {
			index = 0;
		}

		options.forEach( function ( el, i ) {
			var on = i === index;
			el.classList.toggle( 'is-active', on );
			el.setAttribute( 'aria-selected', on ? 'true' : 'false' );
			if ( on ) {
				input.setAttribute( 'aria-activedescendant', el.id );
				el.scrollIntoView( { block: 'nearest' } );
			}
		} );
		activeIndex = index;
	}

	function openActive() {
		var options = optionEls();
		var target = activeIndex > -1 ? options[ activeIndex ] : options[ 0 ];
		if ( target ) {
			var link = target.querySelector( 'a' );
			if ( link ) {
				window.location.href = link.getAttribute( 'href' );
				return true;
			}
		}
		return false;
	}

	function onInput() {
		var query = input.value.trim();
		window.clearTimeout( debounceTimer );

		debounceTimer = window.setTimeout( function () {
			render( query.length ? filter( query ) : items );
		}, 120 );
	}

	/* --- Keyboard ---------------------------------------------------------- */

	// Global shortcut: Cmd+S (mac) / Ctrl+S (win/linux) toggles the palette.
	document.addEventListener( 'keydown', function ( e ) {
		var key = ( e.key || '' ).toLowerCase();
		if ( key === 's' && ( e.metaKey || e.ctrlKey ) && ! e.altKey ) {
			e.preventDefault();
			if ( isOpen() ) {
				close();
			} else {
				open();
			}
		}
	} );

	// In-dialog keys: Escape, arrow navigation, Enter.
	modal.addEventListener( 'keydown', function ( e ) {
		if ( ! isOpen() ) {
			return;
		}

		switch ( e.key ) {
			case 'Escape':
				e.preventDefault();
				close();
				break;
			case 'ArrowDown':
				e.preventDefault();
				setActive( activeIndex + 1 );
				break;
			case 'ArrowUp':
				e.preventDefault();
				setActive( activeIndex - 1 );
				break;
			case 'Enter':
				e.preventDefault();
				openActive();
				break;
			default:
				break;
		}
	} );

	// Focus trap: keep Tab within the panel while the dialog is open.
	modal.addEventListener( 'keydown', function ( e ) {
		if ( e.key !== 'Tab' || ! isOpen() ) {
			return;
		}
		var focusable = panel.querySelectorAll(
			'a[href], button:not([disabled]), input, [tabindex]:not([tabindex="-1"])'
		);
		var list = Array.prototype.filter.call( focusable, function ( el ) {
			return el.offsetParent !== null || el === input;
		} );
		if ( ! list.length ) {
			return;
		}
		var first = list[ 0 ];
		var last = list[ list.length - 1 ];

		if ( e.shiftKey && document.activeElement === first ) {
			e.preventDefault();
			last.focus();
		} else if ( ! e.shiftKey && document.activeElement === last ) {
			e.preventDefault();
			first.focus();
		}
	} );

	/* --- Wiring ------------------------------------------------------------ */

	if ( trigger ) {
		trigger.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			open();
		} );
	}

	input.addEventListener( 'input', onInput );

	// Dismiss when clicking the backdrop or a control marked for it.
	modal.addEventListener( 'click', function ( e ) {
		if ( e.target.closest( '[data-search-dismiss]' ) ) {
			e.preventDefault();
			close();
		}
	} );
} )();

/**
 * Progressive enhancement for [swe_fellows_application].
 *
 * Without this file the form is a single ordinary <form> posting to
 * admin-post.php. With it, the three fieldsets become a step flow:
 * - per-step native validation before advancing
 * - progress list with aria-current="step"
 * - focus moves to the step's legend on navigation
 * - final submit goes to the REST endpoint via fetch()
 * - success and errors are announced (role="alert" / aria-live)
 */
( function () {
	'use strict';

	function init( root ) {
		var form = root.querySelector( '.swe-app-form' );
		var steps = root.querySelectorAll( '.swe-app-step' );
		var progress = root.querySelector( '[data-progress]' );
		var progressItems = root.querySelectorAll( '[data-progress-step]' );
		var live = root.querySelector( '.swe-app-live' );
		var errors = root.querySelector( '.swe-app-errors' );
		var success = root.querySelector( '[data-success]' );
		var current = 1;
		var total = steps.length;

		if ( ! form || total < 2 || 'undefined' === typeof window.sweAppConfig ) {
			return;
		}

		// Reveal JS-only chrome.
		progress.hidden = false;
		root.querySelectorAll( '[data-nav]' ).forEach( function ( nav ) {
			nav.hidden = false;
		} );
		var backFinal = root.querySelector( '[data-nav-back]' );
		if ( backFinal ) {
			backFinal.hidden = false;
		}

		function stepTitle( fieldset ) {
			var legend = fieldset.querySelector( 'legend' );
			if ( ! legend ) {
				return '';
			}
			// Announce only the step title — the count is added by the template.
			var clone = legend.cloneNode( true );
			var count = clone.querySelector( '.swe-app-step-count' );
			if ( count ) {
				count.remove();
			}
			return clone.textContent.replace( /\s+/g, ' ' ).trim();
		}

		function show( n, moveFocus ) {
			current = Math.min( Math.max( n, 1 ), total );

			steps.forEach( function ( fieldset ) {
				var num = parseInt( fieldset.getAttribute( 'data-step' ), 10 );
				fieldset.hidden = num !== current;
			} );

			progressItems.forEach( function ( item ) {
				var num = parseInt( item.getAttribute( 'data-progress-step' ), 10 );
				item.classList.toggle( 'is-active', num === current );
				item.classList.toggle( 'is-complete', num < current );
				if ( num === current ) {
					item.setAttribute( 'aria-current', 'step' );
				} else {
					item.removeAttribute( 'aria-current' );
				}
			} );

			var active = steps[ current - 1 ];
			if ( moveFocus ) {
				var legend = active.querySelector( 'legend' );
				if ( legend ) {
					legend.focus();
				}
			}

			live.textContent = window.sweAppConfig.i18n.stepAnnouncement
				.replace( '%1$s', String( current ) )
				.replace( '%2$s', String( total ) )
				.replace( '%3$s', stepTitle( active ) );
		}

		function stepValid( fieldset ) {
			var fields = fieldset.querySelectorAll( 'input, select, textarea' );
			for ( var i = 0; i < fields.length; i++ ) {
				if ( ! fields[ i ].checkValidity() ) {
					fields[ i ].reportValidity();
					return false;
				}
			}
			return true;
		}

		function showError( message ) {
			errors.hidden = false;
			errors.replaceChildren();
			var p = document.createElement( 'p' );
			p.textContent = message;
			errors.appendChild( p );
			errors.focus();
		}

		root.querySelectorAll( '.swe-app-next' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				if ( stepValid( steps[ current - 1 ] ) ) {
					errors.hidden = true;
					show( current + 1, true );
				}
			} );
		} );

		root.querySelectorAll( '.swe-app-back' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				errors.hidden = true;
				show( current - 1, true );
			} );
		} );

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();

			if ( ! stepValid( steps[ current - 1 ] ) ) {
				return;
			}

			var submitBtn = form.querySelector( '.swe-app-submit' );
			if ( ! submitBtn ) {
				return;
			}
			submitBtn.disabled = true;
			submitBtn.textContent = window.sweAppConfig.i18n.submitting;

			var payload = {
				name: form.querySelector( '[name="name"]' ).value,
				email: form.querySelector( '[name="email"]' ).value,
				class_year: form.querySelector( '[name="class_year"]' ).value,
				track: form.querySelector( '[name="track"]' ).value,
				coursework: form.querySelector( '[name="coursework"]' ).value,
				statement: form.querySelector( '[name="statement"]' ).value,
				swe_website: form.querySelector( '[name="swe_website"]' ).value,
			};

			window
				.fetch( window.sweAppConfig.restUrl, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': window.sweAppConfig.nonce,
					},
					body: JSON.stringify( payload ),
				} )
				.then( function ( res ) {
					return res.json().then( function ( body ) {
						return { ok: res.ok, body: body };
					} );
				} )
				.then( function ( result ) {
					if ( ! result.ok ) {
						var message =
							result.body && result.body.message
								? result.body.message
								: window.sweAppConfig.i18n.networkError;
						throw new Error( message );
					}
					form.hidden = true;
					progress.hidden = true;
					errors.hidden = true;
					success.hidden = false;
					success.focus();
				} )
				.catch( function ( err ) {
					submitBtn.disabled = false;
					submitBtn.textContent = window.sweAppConfig.i18n.submitLabel;
					showError( err.message || window.sweAppConfig.i18n.networkError );
				} );
		} );

		show( 1, false );
	}

	document.querySelectorAll( '[data-swe-app]' ).forEach( init );
} )();

/**
 * Farmacia Costa 2026 — interazioni di base.
 * JavaScript vanilla, nessuna dipendenza. Tutte le funzionalità essenziali
 * (navigazione, telefono, WhatsApp, contenuti) funzionano anche senza JS.
 */
( function () {
	'use strict';

	document.documentElement.classList.add( 'fc-js' );

	var reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* Header: stato "scrolled". */
	var header = document.querySelector( '.fc-header' );
	if ( header ) {
		var ticking = false;
		var updateHeader = function () {
			header.classList.toggle( 'is-stuck', window.scrollY > 8 );
			ticking = false;
		};
		window.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				window.requestAnimationFrame( updateHeader );
				ticking = true;
			}
		}, { passive: true } );
		updateHeader();
	}

	/* Reveal allo scroll con IntersectionObserver. */
	var revealTargets = document.querySelectorAll( '.fc-reveal' );
	if ( revealTargets.length ) {
		if ( reducedMotion || ! ( 'IntersectionObserver' in window ) ) {
			revealTargets.forEach( function ( el ) {
				el.classList.add( 'is-visible' );
			} );
		} else {
			var observer = new IntersectionObserver( function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-visible' );
						observer.unobserve( entry.target );
					}
				} );
			}, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 } );
			revealTargets.forEach( function ( el ) {
				observer.observe( el );
			} );
		}
	}

	/* Menu mobile (details): chiusura con Escape o clic esterno. */
	var mobileNav = document.querySelector( '[data-fc-nav]' );
	if ( mobileNav ) {
		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && mobileNav.open ) {
				mobileNav.open = false;
				mobileNav.querySelector( 'summary' ).focus();
			}
		} );
		document.addEventListener( 'click', function ( event ) {
			if ( mobileNav.open && ! mobileNav.contains( event.target ) ) {
				mobileNav.open = false;
			}
		} );
	}

	/* Mappa: caricamento solo dopo consenso esplicito. */
	document.querySelectorAll( '[data-fc-map-load]' ).forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			var wrap = button.closest( '.fc-map' );
			var src = wrap ? wrap.getAttribute( 'data-fc-map-src' ) : '';
			if ( ! wrap || ! src ) {
				return;
			}
			var iframe = document.createElement( 'iframe' );
			iframe.src = src;
			iframe.title = wrap.getAttribute( 'data-fc-map-title' ) || 'Mappa';
			iframe.loading = 'lazy';
			iframe.referrerPolicy = 'no-referrer-when-downgrade';
			iframe.allowFullscreen = true;
			wrap.innerHTML = '';
			wrap.appendChild( iframe );
		} );
	} );

	/* Form: focus sul riepilogo (successo o errori) dopo il redirect. */
	var focusTarget = document.querySelector( '[data-fcc-focus]' );
	if ( focusTarget && window.location.search.indexOf( 'fcc_status=' ) !== -1 ) {
		focusTarget.focus();
	}

	/* Form: nota dedicata quando il motivo è "Invio ricetta". */
	var reasonSelect = document.getElementById( 'fcc-motivo' );
	var ricettaNote = document.querySelector( '[data-fcc-ricetta-note]' );
	if ( reasonSelect && ricettaNote ) {
		var toggleNote = function () {
			ricettaNote.hidden = 'ricetta' !== reasonSelect.value;
		};
		reasonSelect.addEventListener( 'change', toggleNote );
		toggleNote();
	}

	/* Form: invio via fetch con fallback al POST classico. */
	var form = document.querySelector( '.fcc-form' );
	if ( form && window.fetch ) {
		form.addEventListener( 'submit', function ( event ) {
			if ( ! form.reportValidity() ) {
				event.preventDefault();
				return;
			}
			event.preventDefault();

			var submitButton = form.querySelector( '[type="submit"]' );
			if ( submitButton ) {
				submitButton.disabled = true;
			}

			// getAttribute: l'input hidden name="action" oscurerebbe form.action (DOM clobbering).
			fetch( form.getAttribute( 'action' ), {
				method: 'POST',
				body: new FormData( form ),
				headers: { 'X-Requested-With': 'fetch' },
			} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( data ) {
					var wrap = form.closest( '.fcc-form-wrap' );
					if ( ! wrap ) {
						return;
					}
					wrap
						.querySelectorAll( '.fcc-form-errors, .fcc-form-success' )
						.forEach( function ( el ) {
							el.remove();
						} );

					if ( data.ok ) {
						var success = document.createElement( 'div' );
						success.className = 'fcc-form-success';
						success.setAttribute( 'role', 'status' );
						success.setAttribute( 'tabindex', '-1' );
						success.innerHTML =
							'<h3>Richiesta inviata, grazie!</h3><p>Ti risponderemo il prima possibile durante gli orari di apertura. Per necessità urgenti chiamaci o scrivici su WhatsApp.</p>';
						wrap.insertBefore( success, form );
						form.reset();
						success.focus();
					} else {
						var errors = data.errors || {};
						var box = document.createElement( 'div' );
						box.className = 'fcc-form-errors';
						box.setAttribute( 'role', 'alert' );
						box.setAttribute( 'tabindex', '-1' );
						var list = Object.keys( errors )
							.map( function ( field ) {
								return (
									'<li><a href="#fcc-' +
									field +
									'">' +
									String( errors[ field ] ).replace( /</g, '&lt;' ) +
									'</a></li>'
								);
							} )
							.join( '' );
						box.innerHTML = '<h3>Controlla questi campi:</h3><ul>' + list + '</ul>';
						wrap.insertBefore( box, form );
						box.focus();
					}
				} )
				.catch( function () {
					// In caso di problemi di rete si torna all'invio classico.
					form.submit();
				} )
				.finally( function () {
					if ( submitButton ) {
						submitButton.disabled = false;
					}
				} );
		} );
	}
} )();

/**
 * Trova il servizio: ricerca testuale + filtro per categoria, senza ricaricare
 * la pagina. Lo stato è condivisibile via URL (?q=…&cat=…). Senza JavaScript
 * tutte le card restano visibili e i link continuano a funzionare.
 */
( function () {
	'use strict';

	document.querySelectorAll( '[data-fc-finder]' ).forEach( function ( finder ) {
		var input = finder.querySelector( '[data-fc-search-input]' );
		var chips = Array.prototype.slice.call( finder.querySelectorAll( '[data-fc-cat]' ) );
		var cards = Array.prototype.slice.call( finder.querySelectorAll( '[data-fc-service]' ) );
		var status = finder.querySelector( '[data-fc-status]' );
		var empty = finder.querySelector( '[data-fc-empty]' );
		var resetBtn = finder.querySelector( '[data-fc-reset]' );

		if ( ! cards.length ) {
			return;
		}

		var state = { q: '', cat: '' };

		var normalize = function ( text ) {
			return String( text || '' )
				.toLowerCase()
				.normalize( 'NFD' )
				.replace( /[̀-ͯ]/g, '' );
		};

		var apply = function () {
			var query = normalize( state.q ).trim();
			var visible = 0;

			cards.forEach( function ( card ) {
				var matchCat = ! state.cat || card.getAttribute( 'data-fc-category' ) === state.cat;
				var matchText = ! query || normalize( card.getAttribute( 'data-fc-search' ) ).indexOf( query ) !== -1;
				var show = matchCat && matchText;
				card.hidden = ! show;
				if ( show ) {
					visible++;
				}
			} );

			chips.forEach( function ( chip ) {
				chip.setAttribute( 'aria-pressed', chip.getAttribute( 'data-fc-cat' ) === state.cat ? 'true' : 'false' );
			} );

			if ( status ) {
				status.textContent =
					1 === visible ? '1 servizio trovato' : visible + ' servizi trovati';
			}
			if ( empty ) {
				empty.hidden = visible > 0;
			}

			// Stato condivisibile senza ricaricare la pagina.
			if ( window.history && window.history.replaceState ) {
				var params = new URLSearchParams( window.location.search );
				if ( state.q ) {
					params.set( 'q', state.q );
				} else {
					params.delete( 'q' );
				}
				if ( state.cat ) {
					params.set( 'cat', state.cat );
				} else {
					params.delete( 'cat' );
				}
				var qs = params.toString();
				window.history.replaceState(
					null,
					'',
					window.location.pathname + ( qs ? '?' + qs : '' ) + window.location.hash
				);
			}
		};

		if ( input ) {
			input.addEventListener( 'input', function () {
				state.q = input.value;
				apply();
			} );
		}

		chips.forEach( function ( chip ) {
			chip.addEventListener( 'click', function () {
				var cat = chip.getAttribute( 'data-fc-cat' );
				state.cat = state.cat === cat ? '' : cat;
				apply();
			} );
		} );

		if ( resetBtn ) {
			resetBtn.addEventListener( 'click', function () {
				state.q = '';
				state.cat = '';
				if ( input ) {
					input.value = '';
					input.focus();
				}
				apply();
			} );
		}

		// Stato iniziale dall'URL.
		var params = new URLSearchParams( window.location.search );
		state.q = params.get( 'q' ) || '';
		state.cat = params.get( 'cat' ) || finder.getAttribute( 'data-fc-initial-cat' ) || '';
		if ( input && state.q ) {
			input.value = state.q;
		}
		apply();
	} );
} )();

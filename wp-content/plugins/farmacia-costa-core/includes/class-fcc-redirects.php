<?php
/**
 * Redirect 301 per URL storici (vedi CONTENT-AUDIT.md §9).
 *
 * Nessun redirect di massa verso la homepage: ogni voce punta alla
 * destinazione più pertinente. La mappa è estendibile via filtro.
 *
 * @package FarmaciaCostaCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gestione redirect.
 */
class FCC_Redirects {

	/**
	 * Aggancia gli hook.
	 */
	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'maybe_redirect' ), 1 );
	}

	/**
	 * Mappa vecchio percorso → nuovo percorso.
	 *
	 * @return array<string,string>
	 */
	public static function map() {
		$map = array(
			// Pagina tamponi Covid: servizio obsoleto, si reindirizza all'elenco servizi.
			'/tamponi/' => '/i-nostri-servizi/',
		);

		/**
		 * Estende la mappa dei redirect 301.
		 *
		 * @param array $map percorso relativo => percorso relativo o URL assoluto.
		 */
		return apply_filters( 'fcc_redirects', $map );
	}

	/**
	 * Applica il redirect solo su richieste che finirebbero in 404.
	 */
	public static function maybe_redirect() {
		if ( ! is_404() ) {
			return;
		}
		$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$path    = trailingslashit( strtolower( (string) wp_parse_url( $request, PHP_URL_PATH ) ) );

		$map = self::map();
		if ( isset( $map[ $path ] ) ) {
			$target = $map[ $path ];
			if ( 0 !== strpos( $target, 'http' ) ) {
				$target = home_url( $target );
			}
			wp_safe_redirect( $target, 301 );
			exit;
		}
	}
}

<?php
/**
 * Helper pubblici per i dati aziendali centralizzati.
 *
 * Tutti i template, i pattern e i componenti leggono i dati della farmacia
 * da queste funzioni: un'unica fonte di verità (opzione `fcc_settings`).
 *
 * @package FarmaciaCostaCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valori predefiniti dei dati aziendali.
 *
 * Gli orari provengono dall'audit del sito precedente e NON sono pubblicati
 * finché l'opzione `hours_verified` non viene attivata dal titolare.
 *
 * @return array<string,mixed>
 */
function fcc_defaults() {
	return array(
		'business_name'  => 'Farmacia Costa',
		'phone_display'  => '0781 61840',
		'phone_e164'     => '+39078161840',
		'whatsapp'       => '39078161840',
		'email'          => 'farmaciacostabanco@gmail.com',
		'address'        => 'Piazza Matteotti, 5',
		'cap'            => '09013',
		'city'           => 'Carbonia',
		'province'       => 'SU',
		'lat'            => '',
		'lng'            => '',
		'hours'          => array(
			'mon' => array( '08:30-13:00', '16:30-20:30' ),
			'tue' => array( '08:30-13:00', '16:30-20:30' ),
			'wed' => array( '08:30-13:00', '16:30-20:30' ),
			'thu' => array( '08:30-13:00', '16:30-20:30' ),
			'fri' => array( '08:30-13:00', '16:30-20:30' ),
			'sat' => array( '08:30-13:00' ),
			'sun' => array(),
		),
		'hours_verified' => 0,
		'hours_note'     => '',
		'closures'       => '',
		'facebook'       => 'https://www.facebook.com/farmaciacostaSNC/',
		'instagram'      => 'https://www.instagram.com/farmacia_costa_carbonia/',
		'maps_url'       => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( 'Farmacia Costa, Piazza Matteotti 5, 09013 Carbonia SU' ),
		'wa_message'     => 'Buongiorno, avrei bisogno di informazioni sui servizi della Farmacia Costa.',
		'form_recipient' => '',
	);
}

/**
 * Legge un dato aziendale.
 *
 * @param string $key     Chiave del dato.
 * @param mixed  $default Valore di riserva se la chiave non esiste.
 * @return mixed
 */
function fcc_get( $key, $default = '' ) {
	$settings = wp_parse_args( (array) get_option( 'fcc_settings', array() ), fcc_defaults() );
	return $settings[ $key ] ?? $default;
}

/**
 * Link telefonico cliccabile (tel:).
 *
 * @return string
 */
function fcc_phone_href() {
	return 'tel:' . preg_replace( '/[^+\d]/', '', (string) fcc_get( 'phone_e164' ) );
}

/**
 * URL WhatsApp con messaggio precompilato codificato.
 *
 * Il messaggio non deve mai contenere dati personali o sanitari.
 *
 * @param string $message Messaggio; vuoto = messaggio predefinito dalle impostazioni.
 * @return string
 */
function fcc_whatsapp_url( $message = '' ) {
	$number  = preg_replace( '/\D/', '', (string) fcc_get( 'whatsapp' ) );
	$message = '' !== $message ? $message : (string) fcc_get( 'wa_message' );
	$url     = 'https://wa.me/' . $number;
	if ( '' !== $message ) {
		$url .= '?text=' . rawurlencode( $message );
	}
	return $url;
}

/**
 * URL per le indicazioni stradali.
 *
 * @return string
 */
function fcc_maps_url() {
	return (string) fcc_get( 'maps_url' );
}

/**
 * Indirizzo su una riga: "Piazza Matteotti, 5 — 09013 Carbonia (SU)".
 *
 * @return string
 */
function fcc_address_line() {
	return sprintf(
		'%s — %s %s (%s)',
		fcc_get( 'address' ),
		fcc_get( 'cap' ),
		fcc_get( 'city' ),
		fcc_get( 'province' )
	);
}

/**
 * Gli orari sono stati verificati dal titolare e possono essere pubblicati?
 *
 * @return bool
 */
function fcc_hours_verified() {
	return (bool) fcc_get( 'hours_verified' );
}

/**
 * Etichette dei giorni in italiano.
 *
 * @return array<string,string>
 */
function fcc_day_labels() {
	return array(
		'mon' => __( 'Lunedì', 'farmacia-costa-core' ),
		'tue' => __( 'Martedì', 'farmacia-costa-core' ),
		'wed' => __( 'Mercoledì', 'farmacia-costa-core' ),
		'thu' => __( 'Giovedì', 'farmacia-costa-core' ),
		'fri' => __( 'Venerdì', 'farmacia-costa-core' ),
		'sat' => __( 'Sabato', 'farmacia-costa-core' ),
		'sun' => __( 'Domenica', 'farmacia-costa-core' ),
	);
}

/**
 * Orari come tabella accessibile, oppure invito a chiamare se non verificati.
 *
 * @return string HTML già sottoposto a escaping.
 */
function fcc_render_hours() {
	if ( ! fcc_hours_verified() ) {
		return '<p class="fcc-hours-unverified">' . sprintf(
			/* translators: 1: link telefono, 2: numero di telefono. */
			wp_kses_post( __( 'Per conoscere gli orari aggiornati chiamaci allo <a href="%1$s">%2$s</a> o scrivici su WhatsApp.', 'farmacia-costa-core' ) ),
			esc_url( fcc_phone_href() ),
			esc_html( fcc_get( 'phone_display' ) )
		) . '</p>';
	}

	$hours  = (array) fcc_get( 'hours' );
	$labels = fcc_day_labels();
	$html   = '<table class="fcc-hours"><caption class="screen-reader-text">' . esc_html__( 'Orari di apertura', 'farmacia-costa-core' ) . '</caption><tbody>';

	foreach ( $labels as $key => $label ) {
		$ranges = array_filter( (array) ( $hours[ $key ] ?? array() ) );
		$value  = $ranges
			? implode( ' / ', array_map( 'esc_html', $ranges ) )
			: '<span class="fcc-hours-closed">' . esc_html__( 'Chiuso', 'farmacia-costa-core' ) . '</span>';
		$html  .= '<tr><th scope="row">' . esc_html( $label ) . '</th><td>' . $value . '</td></tr>';
	}
	$html .= '</tbody></table>';

	$note = trim( (string) fcc_get( 'hours_note' ) );
	if ( '' !== $note ) {
		$html .= '<p class="fcc-hours-note">' . esc_html( $note ) . '</p>';
	}
	$closures = trim( (string) fcc_get( 'closures' ) );
	if ( '' !== $closures ) {
		$html .= '<p class="fcc-hours-closures">' . esc_html( $closures ) . '</p>';
	}

	return $html;
}

/**
 * Email destinataria del form (impostazione dedicata, con fallback all'email admin).
 *
 * @return string
 */
function fcc_form_recipient() {
	$recipient = sanitize_email( (string) fcc_get( 'form_recipient' ) );
	return $recipient ? $recipient : get_option( 'admin_email' );
}

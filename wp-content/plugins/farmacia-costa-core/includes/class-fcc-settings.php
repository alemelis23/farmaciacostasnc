<?php
/**
 * Pagina di amministrazione "Farmacia": dati aziendali centralizzati.
 *
 * @package FarmaciaCostaCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registra la pagina impostazioni e la sanitizzazione dell'opzione.
 */
class FCC_Settings {

	const OPTION = 'fcc_settings';

	/**
	 * Aggancia gli hook.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
	}

	/**
	 * Voce di menu dedicata.
	 */
	public static function menu() {
		add_menu_page(
			__( 'Dati della farmacia', 'farmacia-costa-core' ),
			__( 'Farmacia', 'farmacia-costa-core' ),
			'manage_options',
			'fcc-settings',
			array( __CLASS__, 'render_page' ),
			'dashicons-plus-alt',
			59
		);
	}

	/**
	 * Registra l'opzione con sanitizzazione.
	 */
	public static function register() {
		register_setting(
			'fcc_settings_group',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
			)
		);
	}

	/**
	 * Sanitizza tutti i campi dell'opzione.
	 *
	 * @param array $input Dati grezzi dal form.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$input = (array) $input;
		$clean = array();

		$clean['business_name'] = sanitize_text_field( $input['business_name'] ?? '' );
		$clean['phone_display'] = sanitize_text_field( $input['phone_display'] ?? '' );
		$clean['phone_e164']    = preg_replace( '/[^+\d]/', '', (string) ( $input['phone_e164'] ?? '' ) );
		$clean['whatsapp']      = preg_replace( '/\D/', '', (string) ( $input['whatsapp'] ?? '' ) );
		$clean['email']         = sanitize_email( $input['email'] ?? '' );
		$clean['form_recipient'] = sanitize_email( $input['form_recipient'] ?? '' );
		$clean['address']       = sanitize_text_field( $input['address'] ?? '' );
		$clean['cap']           = sanitize_text_field( $input['cap'] ?? '' );
		$clean['city']          = sanitize_text_field( $input['city'] ?? '' );
		$clean['province']      = sanitize_text_field( $input['province'] ?? '' );
		$clean['lat']           = sanitize_text_field( $input['lat'] ?? '' );
		$clean['lng']           = sanitize_text_field( $input['lng'] ?? '' );
		$clean['hours_verified'] = empty( $input['hours_verified'] ) ? 0 : 1;
		$clean['hours_note']    = sanitize_text_field( $input['hours_note'] ?? '' );
		$clean['closures']      = sanitize_text_field( $input['closures'] ?? '' );
		$clean['facebook']      = esc_url_raw( $input['facebook'] ?? '' );
		$clean['instagram']     = esc_url_raw( $input['instagram'] ?? '' );
		$clean['maps_url']      = esc_url_raw( $input['maps_url'] ?? '' );
		$clean['wa_message']    = sanitize_text_field( $input['wa_message'] ?? '' );

		$clean['hours'] = array();
		$days           = array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' );
		foreach ( $days as $day ) {
			$raw = (string) ( $input['hours'][ $day ] ?? '' );
			// Formato: fasce separate da virgola, es. "08:30-13:00, 16:30-20:30".
			$ranges = array();
			foreach ( explode( ',', $raw ) as $range ) {
				$range = trim( $range );
				if ( '' !== $range && preg_match( '/^\d{1,2}:\d{2}\s*-\s*\d{1,2}:\d{2}$/', $range ) ) {
					$ranges[] = preg_replace( '/\s*-\s*/', '-', $range );
				}
			}
			$clean['hours'][ $day ] = $ranges;
		}

		return $clean;
	}

	/**
	 * Campo di testo della pagina impostazioni.
	 *
	 * @param string $key   Chiave.
	 * @param string $label Etichetta.
	 * @param string $hint  Descrizione facoltativa.
	 * @param string $type  Tipo input.
	 */
	private static function field( $key, $label, $hint = '', $type = 'text' ) {
		$value = fcc_get( $key );
		printf(
			'<tr><th scope="row"><label for="fcc-%1$s">%2$s</label></th><td><input type="%3$s" class="regular-text" id="fcc-%1$s" name="%4$s[%1$s]" value="%5$s">%6$s</td></tr>',
			esc_attr( $key ),
			esc_html( $label ),
			esc_attr( $type ),
			esc_attr( self::OPTION ),
			esc_attr( (string) $value ),
			$hint ? '<p class="description">' . esc_html( $hint ) . '</p>' : ''
		);
	}

	/**
	 * Pagina impostazioni.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$hours  = (array) fcc_get( 'hours' );
		$labels = fcc_day_labels();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Dati della farmacia', 'farmacia-costa-core' ); ?></h1>
			<p><?php esc_html_e( 'Questi dati alimentano header, footer, pagina contatti, pulsanti di chiamata/WhatsApp e dati strutturati. Modificali qui una sola volta: il sito si aggiorna ovunque.', 'farmacia-costa-core' ); ?></p>
			<form action="options.php" method="post">
				<?php settings_fields( 'fcc_settings_group' ); ?>

				<h2><?php esc_html_e( 'Identità e contatti', 'farmacia-costa-core' ); ?></h2>
				<table class="form-table" role="presentation">
					<?php
					self::field( 'business_name', __( 'Nome attività', 'farmacia-costa-core' ) );
					self::field( 'phone_display', __( 'Telefono (come mostrato)', 'farmacia-costa-core' ), __( 'Esempio: 0781 61840', 'farmacia-costa-core' ) );
					self::field( 'phone_e164', __( 'Telefono (formato internazionale)', 'farmacia-costa-core' ), __( 'Usato nei link tel:. Esempio: +39078161840', 'farmacia-costa-core' ) );
					self::field( 'whatsapp', __( 'Numero WhatsApp', 'farmacia-costa-core' ), __( 'Solo cifre con prefisso internazionale, senza +. Esempio: 39078161840', 'farmacia-costa-core' ) );
					self::field( 'wa_message', __( 'Messaggio WhatsApp predefinito', 'farmacia-costa-core' ), __( 'Non inserire mai dati personali o sanitari.', 'farmacia-costa-core' ) );
					self::field( 'email', __( 'Email pubblica', 'farmacia-costa-core' ), '', 'email' );
					self::field( 'form_recipient', __( 'Email destinataria del form', 'farmacia-costa-core' ), __( 'Se vuota, viene usata l\'email amministratore del sito.', 'farmacia-costa-core' ), 'email' );
					?>
				</table>

				<h2><?php esc_html_e( 'Sede', 'farmacia-costa-core' ); ?></h2>
				<table class="form-table" role="presentation">
					<?php
					self::field( 'address', __( 'Indirizzo', 'farmacia-costa-core' ) );
					self::field( 'cap', __( 'CAP', 'farmacia-costa-core' ) );
					self::field( 'city', __( 'Città', 'farmacia-costa-core' ) );
					self::field( 'province', __( 'Provincia (sigla)', 'farmacia-costa-core' ) );
					self::field( 'lat', __( 'Latitudine', 'farmacia-costa-core' ), __( 'Facoltativa; usata nei dati strutturati. Ricavala da Google Maps.', 'farmacia-costa-core' ) );
					self::field( 'lng', __( 'Longitudine', 'farmacia-costa-core' ) );
					self::field( 'maps_url', __( 'Link indicazioni stradali', 'farmacia-costa-core' ), __( 'Link "Apri in Google Maps".', 'farmacia-costa-core' ), 'url' );
					?>
				</table>

				<h2><?php esc_html_e( 'Orari', 'farmacia-costa-core' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Formato: fasce separate da virgola, es. "08:30-13:00, 16:30-20:30". Lascia vuoto per "Chiuso".', 'farmacia-costa-core' ); ?></p>
				<table class="form-table" role="presentation">
					<?php foreach ( $labels as $key => $label ) : ?>
						<tr>
							<th scope="row"><label for="fcc-hours-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
							<td>
								<input type="text" class="regular-text" id="fcc-hours-<?php echo esc_attr( $key ); ?>"
									name="<?php echo esc_attr( self::OPTION ); ?>[hours][<?php echo esc_attr( $key ); ?>]"
									value="<?php echo esc_attr( implode( ', ', array_filter( (array) ( $hours[ $key ] ?? array() ) ) ) ); ?>">
							</td>
						</tr>
					<?php endforeach; ?>
					<tr>
						<th scope="row"><?php esc_html_e( 'Orari verificati', 'farmacia-costa-core' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( self::OPTION ); ?>[hours_verified]" value="1" <?php checked( fcc_hours_verified() ); ?>>
								<?php esc_html_e( 'Confermo che gli orari indicati sono corretti e possono essere pubblicati sul sito.', 'farmacia-costa-core' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'Finché questa casella non è attiva, il sito invita a chiamare per gli orari e non li pubblica.', 'farmacia-costa-core' ); ?></p>
						</td>
					</tr>
					<?php
					self::field( 'hours_note', __( 'Nota sugli orari', 'farmacia-costa-core' ), __( 'Esempio: "Orario continuato nel mese di agosto".', 'farmacia-costa-core' ) );
					self::field( 'closures', __( 'Chiusure straordinarie', 'farmacia-costa-core' ), __( 'Esempio: "Chiusi il 15 agosto".', 'farmacia-costa-core' ) );
					?>
				</table>

				<h2><?php esc_html_e( 'Profili social', 'farmacia-costa-core' ); ?></h2>
				<table class="form-table" role="presentation">
					<?php
					self::field( 'facebook', __( 'Facebook', 'farmacia-costa-core' ), '', 'url' );
					self::field( 'instagram', __( 'Instagram', 'farmacia-costa-core' ), '', 'url' );
					?>
				</table>

				<?php submit_button( __( 'Salva i dati della farmacia', 'farmacia-costa-core' ) ); ?>
			</form>
		</div>
		<?php
	}
}

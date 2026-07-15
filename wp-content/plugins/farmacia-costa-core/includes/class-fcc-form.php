<?php
/**
 * Form di contatto: rendering, validazione server, invio email.
 *
 * Protezioni: nonce (CSRF), honeypot, trappola temporale, rate limiting per IP,
 * sanitizzazione input, escaping output, nessun dato personale nei log,
 * nessun indirizzo destinatario esposto nel markup o nel JavaScript.
 * I messaggi NON vengono salvati nel database (minimizzazione dei dati):
 * viene usato solo un transient temporaneo per ripopolare il form dopo un errore.
 *
 * @package FarmaciaCostaCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gestione del form di contatto.
 */
class FCC_Form {

	const ACTION      = 'fcc_contact';
	const RATE_LIMIT  = 5;      // Invii massimi per finestra.
	const RATE_WINDOW = 900;    // Finestra di 15 minuti.
	const MIN_SECONDS = 3;      // Tempo minimo di compilazione.

	/**
	 * Aggancia gli hook.
	 */
	public static function init() {
		add_action( 'admin_post_' . self::ACTION, array( __CLASS__, 'handle' ) );
		add_action( 'admin_post_nopriv_' . self::ACTION, array( __CLASS__, 'handle' ) );
	}

	/**
	 * Motivi di contatto ammessi.
	 *
	 * @return array<string,string>
	 */
	public static function reasons() {
		return array(
			'informazioni'  => __( 'Informazioni sui servizi', 'farmacia-costa-core' ),
			'prenotazione'  => __( 'Prenotazione di un servizio', 'farmacia-costa-core' ),
			'disponibilita' => __( 'Disponibilità di un prodotto', 'farmacia-costa-core' ),
			'ricetta'       => __( 'Invio ricetta / ritiro farmaci', 'farmacia-costa-core' ),
			'amministrativa' => __( 'Richiesta amministrativa', 'farmacia-costa-core' ),
			'altro'         => __( 'Altro', 'farmacia-costa-core' ),
		);
	}

	/**
	 * Modalità di risposta ammesse.
	 *
	 * @return array<string,string>
	 */
	public static function reply_modes() {
		return array(
			'telefono' => __( 'Telefonata', 'farmacia-costa-core' ),
			'email'    => __( 'Email', 'farmacia-costa-core' ),
			'whatsapp' => __( 'WhatsApp', 'farmacia-costa-core' ),
		);
	}

	/**
	 * Firma della trappola temporale.
	 *
	 * @param string $timestamp Timestamp Unix come stringa.
	 * @return string
	 */
	private static function sign_timestamp( $timestamp ) {
		return wp_hash( 'fcc_ts|' . $timestamp );
	}

	/**
	 * Stato del form dal redirect (successo o errori), letto una sola volta.
	 *
	 * @return array{status:string,errors:array,values:array}
	 */
	private static function feedback() {
		$out = array(
			'status' => '',
			'errors' => array(),
			'values' => array(),
		);
		// I parametri seguono un redirect emesso dal nostro stesso handler:
		// niente nonce necessario, nessuna azione di scrittura.
		$status = isset( $_GET['fcc_status'] ) ? sanitize_key( $_GET['fcc_status'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$token  = isset( $_GET['fcc_token'] ) ? sanitize_key( $_GET['fcc_token'] ) : '';   // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( 'ok' === $status ) {
			$out['status'] = 'ok';
			return $out;
		}
		if ( 'error' === $status && $token ) {
			$data = get_transient( 'fcc_form_' . $token );
			delete_transient( 'fcc_form_' . $token );
			if ( is_array( $data ) ) {
				$out['status'] = 'error';
				$out['errors'] = (array) ( $data['errors'] ?? array() );
				$out['values'] = (array) ( $data['values'] ?? array() );
			}
		}
		return $out;
	}

	/**
	 * Rendering del form (chiamato dal pattern del tema).
	 *
	 * @return string HTML.
	 */
	public static function render() {
		$feedback = self::feedback();
		$values   = $feedback['values'];
		$errors   = $feedback['errors'];
		$privacy  = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';

		ob_start();
		?>
		<div class="fcc-form-wrap" id="fcc-form">
			<?php if ( 'ok' === $feedback['status'] ) : ?>
				<div class="fcc-form-success" role="status" tabindex="-1" data-fcc-focus>
					<h3><?php esc_html_e( 'Richiesta inviata, grazie!', 'farmacia-costa-core' ); ?></h3>
					<p><?php esc_html_e( 'Ti risponderemo il prima possibile durante gli orari di apertura. Per necessità urgenti chiamaci o scrivici su WhatsApp.', 'farmacia-costa-core' ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( 'error' === $feedback['status'] && $errors ) : ?>
				<div class="fcc-form-errors" role="alert" tabindex="-1" data-fcc-focus id="fcc-form-errors">
					<h3><?php esc_html_e( 'Controlla questi campi:', 'farmacia-costa-core' ); ?></h3>
					<ul>
						<?php foreach ( $errors as $field => $message ) : ?>
							<li><a href="#fcc-<?php echo esc_attr( $field ); ?>"><?php echo esc_html( $message ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<form class="fcc-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
				<input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>">
				<?php wp_nonce_field( self::ACTION, 'fcc_nonce' ); ?>
				<input type="hidden" name="fcc_ts" value="<?php echo esc_attr( (string) time() ); ?>">
				<input type="hidden" name="fcc_sig" value="<?php echo esc_attr( self::sign_timestamp( (string) time() ) ); ?>">
				<input type="hidden" name="fcc_redirect" value="<?php echo esc_url( get_permalink() ? get_permalink() : home_url( '/' ) ); ?>">

				<p class="fcc-hp" aria-hidden="true">
					<label for="fcc-website"><?php esc_html_e( 'Non compilare questo campo', 'farmacia-costa-core' ); ?></label>
					<input type="text" id="fcc-website" name="fcc_website" tabindex="-1" autocomplete="off" value="">
				</p>

				<div class="fcc-form-grid">
					<p class="fcc-field <?php echo isset( $errors['nome'] ) ? 'has-error' : ''; ?>">
						<label for="fcc-nome"><?php esc_html_e( 'Nome e cognome', 'farmacia-costa-core' ); ?> <span class="fcc-req" aria-hidden="true">*</span></label>
						<input type="text" id="fcc-nome" name="fcc_nome" required autocomplete="name"
							value="<?php echo esc_attr( $values['nome'] ?? '' ); ?>"
							<?php echo isset( $errors['nome'] ) ? 'aria-invalid="true" aria-describedby="fcc-err-nome"' : ''; ?>>
						<?php if ( isset( $errors['nome'] ) ) : ?><span class="fcc-field-error" id="fcc-err-nome"><?php echo esc_html( $errors['nome'] ); ?></span><?php endif; ?>
					</p>

					<p class="fcc-field <?php echo isset( $errors['telefono'] ) ? 'has-error' : ''; ?>">
						<label for="fcc-telefono"><?php esc_html_e( 'Telefono', 'farmacia-costa-core' ); ?> <span class="fcc-req" aria-hidden="true">*</span></label>
						<input type="tel" id="fcc-telefono" name="fcc_telefono" required autocomplete="tel"
							value="<?php echo esc_attr( $values['telefono'] ?? '' ); ?>"
							<?php echo isset( $errors['telefono'] ) ? 'aria-invalid="true" aria-describedby="fcc-err-telefono"' : ''; ?>>
						<?php if ( isset( $errors['telefono'] ) ) : ?><span class="fcc-field-error" id="fcc-err-telefono"><?php echo esc_html( $errors['telefono'] ); ?></span><?php endif; ?>
					</p>

					<p class="fcc-field <?php echo isset( $errors['email'] ) ? 'has-error' : ''; ?>">
						<label for="fcc-email"><?php esc_html_e( 'Email', 'farmacia-costa-core' ); ?> <span class="fcc-req" aria-hidden="true">*</span></label>
						<input type="email" id="fcc-email" name="fcc_email" required autocomplete="email"
							value="<?php echo esc_attr( $values['email'] ?? '' ); ?>"
							<?php echo isset( $errors['email'] ) ? 'aria-invalid="true" aria-describedby="fcc-err-email"' : ''; ?>>
						<?php if ( isset( $errors['email'] ) ) : ?><span class="fcc-field-error" id="fcc-err-email"><?php echo esc_html( $errors['email'] ); ?></span><?php endif; ?>
					</p>

					<p class="fcc-field <?php echo isset( $errors['motivo'] ) ? 'has-error' : ''; ?>">
						<label for="fcc-motivo"><?php esc_html_e( 'Motivo del contatto', 'farmacia-costa-core' ); ?> <span class="fcc-req" aria-hidden="true">*</span></label>
						<select id="fcc-motivo" name="fcc_motivo" required
							<?php echo isset( $errors['motivo'] ) ? 'aria-invalid="true" aria-describedby="fcc-err-motivo"' : ''; ?>>
							<option value=""><?php esc_html_e( 'Seleziona…', 'farmacia-costa-core' ); ?></option>
							<?php foreach ( self::reasons() as $key => $label ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $values['motivo'] ?? '', $key ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php if ( isset( $errors['motivo'] ) ) : ?><span class="fcc-field-error" id="fcc-err-motivo"><?php echo esc_html( $errors['motivo'] ); ?></span><?php endif; ?>
					</p>
				</div>

				<p class="fcc-field fcc-field-ricetta-note" hidden data-fcc-ricetta-note>
					<?php esc_html_e( 'Per l\'invio di ricette non inserire nel messaggio dati sanitari, numeri di ricetta o nomi di farmaci: ti richiameremo noi per concordare il ritiro.', 'farmacia-costa-core' ); ?>
				</p>

				<p class="fcc-field <?php echo isset( $errors['messaggio'] ) ? 'has-error' : ''; ?>">
					<label for="fcc-messaggio"><?php esc_html_e( 'Messaggio', 'farmacia-costa-core' ); ?> <span class="fcc-req" aria-hidden="true">*</span></label>
					<textarea id="fcc-messaggio" name="fcc_messaggio" rows="5" required
						<?php echo isset( $errors['messaggio'] ) ? 'aria-invalid="true" aria-describedby="fcc-err-messaggio"' : ''; ?>><?php echo esc_textarea( $values['messaggio'] ?? '' ); ?></textarea>
					<?php if ( isset( $errors['messaggio'] ) ) : ?><span class="fcc-field-error" id="fcc-err-messaggio"><?php echo esc_html( $errors['messaggio'] ); ?></span><?php endif; ?>
				</p>

				<fieldset class="fcc-field fcc-field-radios">
					<legend><?php esc_html_e( 'Come preferisci essere ricontattato?', 'farmacia-costa-core' ); ?></legend>
					<?php foreach ( self::reply_modes() as $key => $label ) : ?>
						<label class="fcc-radio">
							<input type="radio" name="fcc_risposta" value="<?php echo esc_attr( $key ); ?>" <?php checked( $values['risposta'] ?? 'telefono', $key ); ?>>
							<?php echo esc_html( $label ); ?>
						</label>
					<?php endforeach; ?>
				</fieldset>

				<p class="fcc-field fcc-field-consent <?php echo isset( $errors['privacy'] ) ? 'has-error' : ''; ?>">
					<label for="fcc-privacy" id="fcc-privacy-label">
						<input type="checkbox" id="fcc-privacy" name="fcc_privacy" value="1" required
							<?php echo isset( $errors['privacy'] ) ? 'aria-invalid="true" aria-describedby="fcc-err-privacy"' : ''; ?>>
						<span>
						<?php
						if ( $privacy ) {
							printf(
								/* translators: %s: URL informativa privacy. */
								wp_kses_post( __( 'Ho letto l\'<a href="%s">informativa privacy</a> e acconsento al trattamento dei dati per rispondere alla mia richiesta.', 'farmacia-costa-core' ) ),
								esc_url( $privacy )
							);
						} else {
							esc_html_e( 'Acconsento al trattamento dei dati per rispondere alla mia richiesta.', 'farmacia-costa-core' );
						}
						?>
						</span>
					</label>
					<?php if ( isset( $errors['privacy'] ) ) : ?><span class="fcc-field-error" id="fcc-err-privacy"><?php echo esc_html( $errors['privacy'] ); ?></span><?php endif; ?>
				</p>

				<p class="fcc-field-submit">
					<button type="submit" class="fcc-button"><?php esc_html_e( 'Invia la tua richiesta', 'farmacia-costa-core' ); ?></button>
				</p>
			</form>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Validazione e invio.
	 */
	public static function handle() {
		$redirect = isset( $_POST['fcc_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['fcc_redirect'] ) ) : home_url( '/' );
		// Accetta solo redirect interni.
		$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );

		// CSRF.
		if ( ! isset( $_POST['fcc_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fcc_nonce'] ), self::ACTION ) ) {
			self::bounce( $redirect, array( 'nome' => __( 'La sessione è scaduta: invia di nuovo il modulo.', 'farmacia-costa-core' ) ), array() );
		}

		// Honeypot: risposta "finta ok" per non aiutare i bot.
		if ( ! empty( $_POST['fcc_website'] ) ) {
			self::done( $redirect );
		}

		// Trappola temporale.
		$timestamp = isset( $_POST['fcc_ts'] ) ? sanitize_text_field( wp_unslash( $_POST['fcc_ts'] ) ) : '';
		$signature = isset( $_POST['fcc_sig'] ) ? sanitize_text_field( wp_unslash( $_POST['fcc_sig'] ) ) : '';
		if ( ! $timestamp || ! hash_equals( self::sign_timestamp( $timestamp ), $signature ) || ( time() - (int) $timestamp ) < self::MIN_SECONDS ) {
			self::done( $redirect );
		}

		// Rate limiting per IP (hash dell'IP: nessun IP in chiaro nel database).
		$ip_hash = md5( 'fcc|' . ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
		$count   = (int) get_transient( 'fcc_rl_' . $ip_hash );
		if ( $count >= self::RATE_LIMIT ) {
			self::bounce( $redirect, array( 'nome' => __( 'Hai inviato troppe richieste: riprova tra qualche minuto o chiamaci.', 'farmacia-costa-core' ) ), array() );
		}
		set_transient( 'fcc_rl_' . $ip_hash, $count + 1, self::RATE_WINDOW );

		// Raccolta e sanitizzazione.
		$values = array(
			'nome'      => sanitize_text_field( wp_unslash( $_POST['fcc_nome'] ?? '' ) ),
			'telefono'  => sanitize_text_field( wp_unslash( $_POST['fcc_telefono'] ?? '' ) ),
			'email'     => sanitize_email( wp_unslash( $_POST['fcc_email'] ?? '' ) ),
			'motivo'    => sanitize_key( wp_unslash( $_POST['fcc_motivo'] ?? '' ) ),
			'messaggio' => sanitize_textarea_field( wp_unslash( $_POST['fcc_messaggio'] ?? '' ) ),
			'risposta'  => sanitize_key( wp_unslash( $_POST['fcc_risposta'] ?? 'telefono' ) ),
		);
		$consent = ! empty( $_POST['fcc_privacy'] );

		// Validazione con messaggi specifici.
		$errors = array();
		if ( '' === $values['nome'] ) {
			$errors['nome'] = __( 'Inserisci il tuo nome e cognome.', 'farmacia-costa-core' );
		}
		if ( '' === $values['telefono'] || ! preg_match( '/^[+\d\s().\/-]{6,20}$/', $values['telefono'] ) ) {
			$errors['telefono'] = __( 'Inserisci un numero di telefono valido.', 'farmacia-costa-core' );
		}
		if ( '' === $values['email'] || ! is_email( $values['email'] ) ) {
			$errors['email'] = __( 'Inserisci un indirizzo email valido.', 'farmacia-costa-core' );
		}
		if ( ! array_key_exists( $values['motivo'], self::reasons() ) ) {
			$errors['motivo'] = __( 'Seleziona il motivo del contatto.', 'farmacia-costa-core' );
		}
		if ( strlen( $values['messaggio'] ) < 10 ) {
			$errors['messaggio'] = __( 'Scrivi un messaggio di almeno 10 caratteri.', 'farmacia-costa-core' );
		}
		if ( strlen( $values['messaggio'] ) > 3000 ) {
			$errors['messaggio'] = __( 'Il messaggio è troppo lungo (massimo 3000 caratteri).', 'farmacia-costa-core' );
		}
		if ( ! array_key_exists( $values['risposta'], self::reply_modes() ) ) {
			$values['risposta'] = 'telefono';
		}
		if ( ! $consent ) {
			$errors['privacy'] = __( 'Per inviare la richiesta è necessario il consenso privacy.', 'farmacia-costa-core' );
		}

		if ( $errors ) {
			self::bounce( $redirect, $errors, $values );
		}

		// Invio email tramite wp_mail (compatibile con plugin SMTP).
		$reasons  = self::reasons();
		$replies  = self::reply_modes();
		$subject  = sprintf(
			/* translators: 1: nome sito, 2: motivo. */
			__( '[%1$s] Nuova richiesta dal sito: %2$s', 'farmacia-costa-core' ),
			wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			$reasons[ $values['motivo'] ]
		);
		$body = implode(
			"\n",
			array(
				__( 'Nuova richiesta dal form del sito.', 'farmacia-costa-core' ),
				'',
				__( 'Nome e cognome:', 'farmacia-costa-core' ) . ' ' . $values['nome'],
				__( 'Telefono:', 'farmacia-costa-core' ) . ' ' . $values['telefono'],
				__( 'Email:', 'farmacia-costa-core' ) . ' ' . $values['email'],
				__( 'Motivo:', 'farmacia-costa-core' ) . ' ' . $reasons[ $values['motivo'] ],
				__( 'Risposta preferita:', 'farmacia-costa-core' ) . ' ' . $replies[ $values['risposta'] ],
				'',
				__( 'Messaggio:', 'farmacia-costa-core' ),
				$values['messaggio'],
				'',
				__( 'Consenso privacy: sì', 'farmacia-costa-core' ) . ' (' . gmdate( 'Y-m-d H:i' ) . ' UTC)',
			)
		);
		$headers = array( 'Reply-To: ' . $values['nome'] . ' <' . $values['email'] . '>' );

		$sent = wp_mail( fcc_form_recipient(), $subject, $body, $headers );

		if ( ! $sent ) {
			// Log tecnico senza dati personali.
			error_log( 'FCC_Form: invio email non riuscito (wp_mail ha restituito false).' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			self::bounce(
				$redirect,
				array( 'nome' => __( 'Non siamo riusciti a inviare la richiesta per un problema tecnico. Riprova più tardi oppure chiamaci.', 'farmacia-costa-core' ) ),
				$values
			);
		}

		self::done( $redirect );
	}

	/**
	 * Redirect con errori (o risposta JSON se richiesta via fetch).
	 *
	 * @param string $redirect URL di ritorno.
	 * @param array  $errors   Errori campo => messaggio.
	 * @param array  $values   Valori da ripopolare.
	 */
	private static function bounce( $redirect, $errors, $values ) {
		if ( self::wants_json() ) {
			wp_send_json( array(
				'ok'     => false,
				'errors' => $errors,
			), 400 );
		}
		$token = wp_generate_password( 16, false, false );
		set_transient(
			'fcc_form_' . $token,
			array(
				'errors' => $errors,
				'values' => $values,
			),
			300
		);
		wp_safe_redirect( add_query_arg( array(
			'fcc_status' => 'error',
			'fcc_token'  => $token,
		), $redirect ) . '#fcc-form' );
		exit;
	}

	/**
	 * Redirect di successo (o risposta JSON).
	 *
	 * @param string $redirect URL di ritorno.
	 */
	private static function done( $redirect ) {
		if ( self::wants_json() ) {
			wp_send_json( array( 'ok' => true ) );
		}
		wp_safe_redirect( add_query_arg( 'fcc_status', 'ok', $redirect ) . '#fcc-form' );
		exit;
	}

	/**
	 * La richiesta arriva dal miglioramento JavaScript (fetch)?
	 *
	 * @return bool
	 */
	private static function wants_json() {
		$requested_with = isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) : '';
		return 'fetch' === strtolower( $requested_with );
	}
}

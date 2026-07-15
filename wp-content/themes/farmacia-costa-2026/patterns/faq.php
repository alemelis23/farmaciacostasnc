<?php
/**
 * Title: Domande frequenti
 * Slug: farmacia-costa/faq
 * Categories: farmacia-costa
 * Inserter: no
 *
 * Accordion nativo details/summary. Solo risposte basate su informazioni
 * confermate; gli orari rimandano alla fonte centralizzata finché non verificati.
 *
 * @package FarmaciaCosta2026
 */

$fc_has_core = function_exists( 'fcc_get' );
?>
<!-- wp:html -->
<section class="fc-section" aria-labelledby="fc-faq-title">
	<div class="fc-container">
		<div class="fc-section__head fc-reveal">
			<p class="fc-kicker"><?php esc_html_e( 'Risposte rapide', 'farmacia-costa-2026' ); ?></p>
			<h2 id="fc-faq-title"><?php esc_html_e( 'Domande frequenti', 'farmacia-costa-2026' ); ?></h2>
		</div>
		<div class="fc-faq fc-reveal">
			<details>
				<summary><?php esc_html_e( 'Dove si trova la Farmacia Costa?', 'farmacia-costa-2026' ); ?></summary>
				<div>
					<p><?php esc_html_e( 'In Piazza Matteotti 5, nel centro di Carbonia, accanto alla chiesa di San Ponziano e al Comune.', 'farmacia-costa-2026' ); ?></p>
					<?php if ( $fc_has_core ) : ?>
						<p><a href="<?php echo esc_url( fcc_maps_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Apri le indicazioni in Google Maps', 'farmacia-costa-2026' ); ?></a></p>
					<?php endif; ?>
				</div>
			</details>
			<details>
				<summary><?php esc_html_e( 'Quali sono gli orari di apertura?', 'farmacia-costa-2026' ); ?></summary>
				<div>
					<?php
					if ( $fc_has_core ) {
						echo wp_kses( fcc_render_hours(), fc26_kses_allowed() );
					}
					?>
				</div>
			</details>
			<details>
				<summary><?php esc_html_e( 'Come posso contattare un farmacista?', 'farmacia-costa-2026' ); ?></summary>
				<div>
					<?php if ( $fc_has_core ) : ?>
						<p>
							<?php
							printf(
								/* translators: 1: link telefono, 2: numero, 3: link WhatsApp. */
								wp_kses_post( __( 'Chiamaci allo <a href="%1$s">%2$s</a> negli orari di apertura, oppure <a href="%3$s" target="_blank" rel="noopener">scrivici su WhatsApp</a>: risponde direttamente il banco della farmacia.', 'farmacia-costa-2026' ) ),
								esc_url( fcc_phone_href() ),
								esc_html( fcc_get( 'phone_display' ) ),
								esc_url( fcc_whatsapp_url() )
							);
							?>
						</p>
					<?php endif; ?>
				</div>
			</details>
			<details>
				<summary><?php esc_html_e( 'Come funziona l\'invio della ricetta?', 'farmacia-costa-2026' ); ?></summary>
				<div>
					<p><?php esc_html_e( 'Contattaci per telefono o WhatsApp prima di passare: prepariamo i farmaci e li trovi pronti al ritiro in farmacia. Non inviare dati sanitari nel primo messaggio: ti guidiamo noi.', 'farmacia-costa-2026' ); ?></p>
					<p><a href="<?php echo esc_url( home_url( '/ricetta-in-farmacia/' ) ); ?>"><?php esc_html_e( 'Scopri come funziona il servizio', 'farmacia-costa-2026' ); ?></a></p>
				</div>
			</details>
			<details>
				<summary><?php esc_html_e( 'Serve la prenotazione per i servizi?', 'farmacia-costa-2026' ); ?></summary>
				<div>
					<p><?php esc_html_e( 'Dipende dal servizio: nella pagina di ogni servizio trovi l\'indicazione "Su prenotazione" oppure "Senza prenotazione". Se hai dubbi, chiamaci: ti diciamo subito come organizzarti.', 'farmacia-costa-2026' ); ?></p>
					<p><a href="<?php echo esc_url( home_url( '/i-nostri-servizi/' ) ); ?>"><?php esc_html_e( 'Vedi tutti i servizi', 'farmacia-costa-2026' ); ?></a></p>
				</div>
			</details>
		</div>
	</div>
</section>
<!-- /wp:html -->

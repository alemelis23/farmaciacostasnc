<?php
/**
 * Title: Ricetta in tre passaggi
 * Slug: farmacia-costa/ricetta-steps
 * Categories: farmacia-costa
 * Inserter: no
 *
 * Flusso informativo: nessun upload di ricette o dati sanitari
 * (vedi CONTENT-AUDIT.md §6). Messaggio WhatsApp neutro.
 *
 * @package FarmaciaCosta2026
 */

$fc_has_core = function_exists( 'fcc_get' );
$fc_wa_url   = $fc_has_core
	? fcc_whatsapp_url( __( 'Buongiorno, vorrei concordare il ritiro di alcuni farmaci in farmacia.', 'farmacia-costa-2026' ) )
	: '';
?>
<!-- wp:html -->
<section class="fc-section fc-section--deep" aria-labelledby="fc-ricetta-title">
	<div class="fc-container fc-container--wide">
		<div class="fc-section__head fc-reveal">
			<p class="fc-kicker"><?php esc_html_e( 'Risparmia tempo', 'farmacia-costa-2026' ); ?></p>
			<h2 id="fc-ricetta-title"><?php esc_html_e( 'I tuoi farmaci pronti al ritiro, in tre passaggi', 'farmacia-costa-2026' ); ?></h2>
			<p><?php esc_html_e( 'Avvisaci prima di passare: prepariamo noi quello che ti serve e lo trovi pronto al banco.', 'farmacia-costa-2026' ); ?></p>
		</div>
		<ol class="fc-steps fc-reveal">
			<li>
				<h3><?php esc_html_e( 'Contattaci', 'farmacia-costa-2026' ); ?></h3>
				<p><?php esc_html_e( 'Chiamaci o scrivici su WhatsApp: concordiamo insieme cosa ti serve, senza inviare dati sanitari nel primo messaggio.', 'farmacia-costa-2026' ); ?></p>
			</li>
			<li>
				<h3><?php esc_html_e( 'Prepariamo tutto noi', 'farmacia-costa-2026' ); ?></h3>
				<p><?php esc_html_e( 'Un farmacista verifica la disponibilità dei prodotti e mette da parte il tuo ordine. Se qualcosa manca, ti avvisiamo subito.', 'farmacia-costa-2026' ); ?></p>
			</li>
			<li>
				<h3><?php esc_html_e( 'Ritira in farmacia', 'farmacia-costa-2026' ); ?></h3>
				<p><?php esc_html_e( 'Passa in Piazza Matteotti quando preferisci, negli orari di apertura: paghi al ritiro, come al banco. Non è una vendita online.', 'farmacia-costa-2026' ); ?></p>
			</li>
		</ol>
		<div class="fc-hero__ctas fc-reveal" style="margin-top:2rem">
			<?php if ( $fc_has_core ) : ?>
				<a class="fc-btn fc-btn--whatsapp" href="<?php echo esc_url( $fc_wa_url ); ?>" target="_blank" rel="noopener">
					<?php echo fc26_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php esc_html_e( 'Scrivici su WhatsApp', 'farmacia-costa-2026' ); ?>
				</a>
				<a class="fc-btn fc-btn--outline" style="color:#F8F4EC" href="<?php echo esc_url( fcc_phone_href() ); ?>"><?php esc_html_e( 'Chiama la farmacia', 'farmacia-costa-2026' ); ?></a>
			<?php endif; ?>
			<a class="fc-btn fc-btn--terracotta" href="<?php echo esc_url( home_url( '/ricetta-in-farmacia/' ) ); ?>"><?php esc_html_e( 'Come funziona nel dettaglio', 'farmacia-costa-2026' ); ?></a>
		</div>
	</div>
</section>
<!-- /wp:html -->

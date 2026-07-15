<?php
/**
 * Title: Contatti e mappa
 * Slug: farmacia-costa/contact-section
 * Categories: farmacia-costa
 * Inserter: no
 *
 * La mappa di terze parti viene caricata solo dopo un clic esplicito
 * (nessuna richiesta a Google prima del consenso). Indirizzo e indicazioni
 * restano disponibili anche senza JavaScript.
 *
 * @package FarmaciaCosta2026
 */

if ( ! function_exists( 'fcc_get' ) ) {
	return;
}

$fc_map_query = rawurlencode( fcc_get( 'business_name' ) . ', ' . fcc_get( 'address' ) . ', ' . fcc_get( 'cap' ) . ' ' . fcc_get( 'city' ) );
$fc_map_src   = 'https://www.google.com/maps?q=' . $fc_map_query . '&output=embed';
?>
<!-- wp:html -->
<section class="fc-section fc-section--white" aria-labelledby="fc-contact-title" id="orari">
	<div class="fc-container fc-container--wide">
		<div class="fc-section__head fc-reveal">
			<p class="fc-kicker"><?php esc_html_e( 'Siamo qui', 'farmacia-costa-2026' ); ?></p>
			<h2 id="fc-contact-title"><?php esc_html_e( 'Vieni a trovarci in Piazza Matteotti', 'farmacia-costa-2026' ); ?></h2>
		</div>
		<div class="fc-contact__grid">
			<div class="fc-reveal">
				<ul class="fc-contact__list">
					<li>
						<?php echo fc26_icon( 'pin', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span>
							<strong><?php esc_html_e( 'Indirizzo', 'farmacia-costa-2026' ); ?></strong>
							<?php echo esc_html( fcc_address_line() ); ?><br>
							<a href="<?php echo esc_url( fcc_maps_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Ottieni indicazioni', 'farmacia-costa-2026' ); ?></a>
						</span>
					</li>
					<li>
						<?php echo fc26_icon( 'phone', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span>
							<strong><?php esc_html_e( 'Telefono e WhatsApp', 'farmacia-costa-2026' ); ?></strong>
							<a href="<?php echo esc_url( fcc_phone_href() ); ?>"><?php echo esc_html( fcc_get( 'phone_display' ) ); ?></a> ·
							<a href="<?php echo esc_url( fcc_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Scrivici su WhatsApp', 'farmacia-costa-2026' ); ?></a>
						</span>
					</li>
					<li>
						<?php echo fc26_icon( 'mail', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span>
							<strong><?php esc_html_e( 'Email', 'farmacia-costa-2026' ); ?></strong>
							<a href="mailto:<?php echo esc_attr( antispambot( fcc_get( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( fcc_get( 'email' ) ) ); ?></a>
						</span>
					</li>
					<li>
						<?php echo fc26_icon( 'clock', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span>
							<strong><?php esc_html_e( 'Orari', 'farmacia-costa-2026' ); ?></strong>
							<?php echo wp_kses( fcc_render_hours(), fc26_kses_allowed() ); ?>
						</span>
					</li>
				</ul>
			</div>
			<div class="fc-reveal">
				<div class="fc-map" data-fc-map-src="<?php echo esc_url( $fc_map_src ); ?>" data-fc-map-title="<?php echo esc_attr__( 'Mappa: Farmacia Costa, Piazza Matteotti 5, Carbonia', 'farmacia-costa-2026' ); ?>">
					<div class="fc-map__placeholder">
						<p><strong><?php esc_html_e( 'Mappa di Google Maps', 'farmacia-costa-2026' ); ?></strong></p>
						<p><?php esc_html_e( 'La mappa viene caricata da Google solo se lo richiedi: premendo il pulsante accetti che Google riceva la tua richiesta.', 'farmacia-costa-2026' ); ?></p>
						<div class="fc-map__actions">
							<button type="button" class="fc-btn fc-btn--primary fc-btn--small" data-fc-map-load><?php esc_html_e( 'Carica la mappa', 'farmacia-costa-2026' ); ?></button>
							<a class="fc-btn fc-btn--outline fc-btn--small" href="<?php echo esc_url( fcc_maps_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Apri in Google Maps', 'farmacia-costa-2026' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- /wp:html -->

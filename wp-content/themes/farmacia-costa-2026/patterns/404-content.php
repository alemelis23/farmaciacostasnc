<?php
/**
 * Title: Contenuto pagina 404
 * Slug: farmacia-costa/404-content
 * Categories: farmacia-costa
 * Inserter: no
 *
 * @package FarmaciaCosta2026
 */

$fc_has_core = function_exists( 'fcc_get' );
?>
<!-- wp:html -->
<div class="fc-404 fc-container">
	<p class="fc-kicker" style="justify-content:center"><?php esc_html_e( 'Errore 404', 'farmacia-costa-2026' ); ?></p>
	<h1><?php esc_html_e( 'Questa pagina non è disponibile', 'farmacia-costa-2026' ); ?></h1>
	<p><?php esc_html_e( 'L\'indirizzo potrebbe essere cambiato con il nuovo sito. Puoi tornare alla home, cercare tra i servizi oppure contattarci direttamente: ti aiutiamo noi.', 'farmacia-costa-2026' ); ?></p>
	<div class="fc-hero__ctas">
		<a class="fc-btn fc-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Vai alla homepage', 'farmacia-costa-2026' ); ?></a>
		<a class="fc-btn fc-btn--outline" href="<?php echo esc_url( home_url( '/i-nostri-servizi/' ) ); ?>"><?php esc_html_e( 'Cerca tra i servizi', 'farmacia-costa-2026' ); ?></a>
		<?php if ( $fc_has_core ) : ?>
			<a class="fc-btn fc-btn--outline" href="<?php echo esc_url( fcc_phone_href() ); ?>">
				<?php echo fc26_icon( 'phone', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'Chiama la farmacia', 'farmacia-costa-2026' ); ?>
			</a>
			<a class="fc-btn fc-btn--whatsapp" href="<?php echo esc_url( fcc_whatsapp_url() ); ?>" target="_blank" rel="noopener">
				<?php echo fc26_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'Scrivici su WhatsApp', 'farmacia-costa-2026' ); ?>
			</a>
			<a class="fc-btn fc-btn--outline" href="<?php echo esc_url( fcc_maps_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Ottieni indicazioni', 'farmacia-costa-2026' ); ?></a>
		<?php endif; ?>
	</div>
</div>
<!-- /wp:html -->

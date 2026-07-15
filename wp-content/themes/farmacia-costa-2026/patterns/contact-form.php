<?php
/**
 * Title: Form di contatto
 * Slug: farmacia-costa/contact-form
 * Categories: farmacia-costa
 * Inserter: no
 *
 * Il markup e la logica del form vivono nel plugin farmacia-costa-core.
 *
 * @package FarmaciaCosta2026
 */

if ( ! class_exists( 'FCC_Form' ) ) {
	return;
}
?>
<!-- wp:html -->
<section class="fc-section" aria-labelledby="fc-form-title">
	<div class="fc-container">
		<div class="fc-section__head">
			<p class="fc-kicker"><?php esc_html_e( 'Scrivici', 'farmacia-costa-2026' ); ?></p>
			<h2 id="fc-form-title"><?php esc_html_e( 'Invia la tua richiesta', 'farmacia-costa-2026' ); ?></h2>
			<p><?php esc_html_e( 'Ti rispondiamo negli orari di apertura, nel modo che preferisci. Non inserire nel messaggio dati sanitari o numeri di ricetta.', 'farmacia-costa-2026' ); ?></p>
		</div>
		<?php echo FCC_Form::render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generato e già sottoposto a escaping nel plugin. ?>
	</div>
</section>
<!-- /wp:html -->

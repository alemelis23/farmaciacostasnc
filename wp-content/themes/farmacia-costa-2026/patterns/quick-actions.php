<?php
/**
 * Title: Azioni rapide
 * Slug: farmacia-costa/quick-actions
 * Categories: farmacia-costa
 * Inserter: no
 *
 * @package FarmaciaCosta2026
 */

$fc_has_core = function_exists( 'fcc_get' );
?>
<!-- wp:html -->
<section class="fc-section fc-section--white" aria-labelledby="fc-quick-title">
	<div class="fc-container fc-container--wide">
		<div class="fc-section__head fc-reveal">
			<p class="fc-kicker"><?php esc_html_e( 'Subito al punto', 'farmacia-costa-2026' ); ?></p>
			<h2 id="fc-quick-title"><?php esc_html_e( 'Come possiamo aiutarti?', 'farmacia-costa-2026' ); ?></h2>
		</div>
		<div class="fc-quick fc-reveal">
			<a href="<?php echo esc_url( home_url( '/i-nostri-servizi/' ) ); ?>">
				<?php echo fc26_icon( 'heart', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'Prenota o richiedi un servizio', 'farmacia-costa-2026' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/ricetta-in-farmacia/' ) ); ?>">
				<?php echo fc26_icon( 'rx', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'Invia la ricetta', 'farmacia-costa-2026' ); ?>
			</a>
			<?php if ( $fc_has_core ) : ?>
				<a href="<?php echo esc_url( fcc_phone_href() ); ?>">
					<?php echo fc26_icon( 'phone', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php esc_html_e( 'Parla con un farmacista', 'farmacia-costa-2026' ); ?>
				</a>
				<a href="<?php echo esc_url( home_url( '/contattaci/#orari' ) ); ?>">
					<?php echo fc26_icon( 'clock', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php esc_html_e( 'Scopri gli orari', 'farmacia-costa-2026' ); ?>
				</a>
				<a href="<?php echo esc_url( fcc_maps_url() ); ?>" target="_blank" rel="noopener">
					<?php echo fc26_icon( 'pin', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php esc_html_e( 'Ottieni indicazioni', 'farmacia-costa-2026' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
<!-- /wp:html -->

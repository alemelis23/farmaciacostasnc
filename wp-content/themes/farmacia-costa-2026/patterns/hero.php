<?php
/**
 * Title: Hero homepage
 * Slug: farmacia-costa/hero
 * Categories: farmacia-costa
 * Inserter: no
 *
 * @package FarmaciaCosta2026
 */

$fc_has_core = function_exists( 'fcc_get' );
$fc_city     = $fc_has_core ? fcc_get( 'city' ) : 'Carbonia';
?>
<!-- wp:html -->
<section class="fc-hero" aria-labelledby="fc-hero-title">
	<div class="fc-container fc-container--wide">
		<div class="fc-hero__grid">
			<div>
				<p class="fc-kicker"><?php esc_html_e( 'Piazza Matteotti · dal 1938 verso il futuro', 'farmacia-costa-2026' ); ?></p>
				<h1 id="fc-hero-title">
					<?php
					printf(
						/* translators: %s: città in corsivo. */
						wp_kses_post( __( 'La farmacia di <em>%s</em>, dal 1938', 'farmacia-costa-2026' ) ),
						esc_html( $fc_city )
					);
					?>
				</h1>
				<p class="fc-hero__sub"><?php esc_html_e( 'Competenza, prevenzione e ascolto al servizio della tua salute. Nella stessa piazza da quattro generazioni, con la telemedicina di oggi.', 'farmacia-costa-2026' ); ?></p>
				<div class="fc-hero__ctas">
					<a class="fc-btn fc-btn--primary" href="<?php echo esc_url( home_url( '/i-nostri-servizi/' ) ); ?>"><?php esc_html_e( 'Scopri i servizi', 'farmacia-costa-2026' ); ?></a>
					<?php if ( $fc_has_core ) : ?>
						<a class="fc-btn fc-btn--outline" href="<?php echo esc_url( fcc_phone_href() ); ?>"><?php esc_html_e( 'Parla con un farmacista', 'farmacia-costa-2026' ); ?></a>
						<a class="fc-btn fc-btn--whatsapp" href="<?php echo esc_url( fcc_whatsapp_url() ); ?>" target="_blank" rel="noopener">
							<?php echo fc26_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php esc_html_e( 'Scrivici su WhatsApp', 'farmacia-costa-2026' ); ?>
						</a>
					<?php endif; ?>
				</div>
				<?php if ( $fc_has_core ) : ?>
					<div class="fc-hero__meta">
						<span><?php echo fc26_icon( 'pin', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( fcc_address_line() ); ?></span>
						<span><?php echo fc26_icon( 'phone', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="<?php echo esc_url( fcc_phone_href() ); ?>"><?php echo esc_html( fcc_get( 'phone_display' ) ); ?></a></span>
					</div>
				<?php endif; ?>
			</div>

			<div class="fc-hero__media">
				<figure>
					<!-- PLACEHOLDER: sostituire con una fotografia reale della farmacia (vedi EDITOR-GUIDE.md §7). -->
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/placeholder-farmacia.svg' ) ); ?>"
						alt="<?php esc_attr_e( 'La sede della Farmacia Costa in Piazza Matteotti a Carbonia (immagine segnaposto in attesa della fotografia reale)', 'farmacia-costa-2026' ); ?>"
						width="800" height="920" fetchpriority="high" decoding="async">
				</figure>
				<div class="fc-hero__badge">
					<?php echo fc26_icon( 'heart', 28 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span>
						<strong><?php esc_html_e( 'Dal 1938', 'farmacia-costa-2026' ); ?></strong>
						<small><?php esc_html_e( 'la prima farmacia di Carbonia', 'farmacia-costa-2026' ); ?></small>
					</span>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- /wp:html -->

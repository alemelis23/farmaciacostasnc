<?php
/**
 * Title: Trova il servizio
 * Slug: farmacia-costa/service-finder
 * Categories: farmacia-costa
 * Inserter: no
 *
 * Ricerca testuale + filtro per categoria sulle card dei servizi attivi.
 * Senza JavaScript tutte le card restano visibili e navigabili.
 * Nessuna finalità diagnostica: aiuta solo a orientarsi tra i servizi.
 *
 * @package FarmaciaCosta2026
 */

if ( ! function_exists( 'fcc_get' ) ) {
	return;
}

$fc_services = get_posts(
	array(
		'post_type'      => 'fc_servizio',
		'posts_per_page' => 50,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'relation' => 'OR',
				array( 'key' => '_fcc_active', 'compare' => 'NOT EXISTS' ),
				array( 'key' => '_fcc_active', 'value' => '1' ),
			),
		),
	)
);

$fc_categories = get_terms(
	array(
		'taxonomy'   => 'fc_categoria',
		'hide_empty' => true,
	)
);
?>
<!-- wp:html -->
<section class="fc-section" aria-labelledby="fc-finder-title">
	<div class="fc-container fc-container--wide" data-fc-finder data-fc-initial-cat="<?php echo esc_attr( is_tax( 'fc_categoria' ) ? get_queried_object()->slug : '' ); ?>">
		<div class="fc-section__head fc-reveal">
			<p class="fc-kicker"><?php esc_html_e( 'Prevenzione e servizi', 'farmacia-costa-2026' ); ?></p>
			<h2 id="fc-finder-title"><?php esc_html_e( 'Trova il servizio giusto per te', 'farmacia-costa-2026' ); ?></h2>
			<p><?php esc_html_e( 'Controllo del cuore, pressione, glicemia, telemedicina: cerca per parola o filtra per categoria. Per qualsiasi dubbio i nostri farmacisti sono a disposizione.', 'farmacia-costa-2026' ); ?></p>
		</div>

		<?php if ( $fc_services ) : ?>
			<div class="fc-finder__controls fc-reveal">
				<div class="fc-finder__search">
					<?php echo fc26_icon( 'search', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<label class="screen-reader-text" for="fc-finder-input"><?php esc_html_e( 'Cerca un servizio', 'farmacia-costa-2026' ); ?></label>
					<input type="search" id="fc-finder-input" data-fc-search-input
						placeholder="<?php esc_attr_e( 'Cerca: pressione, cuore, glicemia…', 'farmacia-costa-2026' ); ?>"
						autocomplete="off">
				</div>
				<?php if ( $fc_categories && ! is_wp_error( $fc_categories ) ) : ?>
					<div class="fc-finder__filters" role="group" aria-label="<?php esc_attr_e( 'Filtra per categoria', 'farmacia-costa-2026' ); ?>">
						<?php foreach ( $fc_categories as $fc_cat ) : ?>
							<button type="button" class="fc-chip" data-fc-cat="<?php echo esc_attr( $fc_cat->slug ); ?>" aria-pressed="false">
								<?php echo esc_html( $fc_cat->name ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<p class="fc-finder__status" data-fc-status role="status" aria-live="polite"></p>

			<div class="fc-cards">
				<?php foreach ( $fc_services as $fc_service ) : ?>
					<?php echo fc26_service_card( $fc_service->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endforeach; ?>
			</div>

			<div class="fc-finder__empty" data-fc-empty hidden>
				<p><?php esc_html_e( 'Nessun servizio corrisponde alla tua ricerca. Prova con un\'altra parola, oppure chiedici direttamente: ti indirizziamo noi.', 'farmacia-costa-2026' ); ?></p>
				<div class="fc-hero__ctas" style="justify-content:center">
					<button type="button" class="fc-btn fc-btn--outline fc-btn--small" data-fc-reset><?php esc_html_e( 'Azzera la ricerca', 'farmacia-costa-2026' ); ?></button>
					<a class="fc-btn fc-btn--whatsapp fc-btn--small" href="<?php echo esc_url( fcc_whatsapp_url() ); ?>" target="_blank" rel="noopener">
						<?php echo fc26_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php esc_html_e( 'Chiedi su WhatsApp', 'farmacia-costa-2026' ); ?>
					</a>
				</div>
			</div>
		<?php else : ?>
			<p><?php esc_html_e( 'I servizi saranno pubblicati a breve. Nel frattempo chiamaci o scrivici su WhatsApp per qualsiasi necessità.', 'farmacia-costa-2026' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<!-- /wp:html -->

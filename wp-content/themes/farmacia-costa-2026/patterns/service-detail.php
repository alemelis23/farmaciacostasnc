<?php
/**
 * Title: Dettaglio servizio
 * Slug: farmacia-costa/service-detail
 * Categories: farmacia-costa
 * Inserter: no
 *
 * Corpo della pagina servizio: breadcrumb, contenuto, scheda informativa
 * con prenotazione/durata/prezzo (solo se confermati), CTA contestuali,
 * FAQ e servizi correlati. Include l'avvertenza sanitaria.
 *
 * @package FarmaciaCosta2026
 */

if ( ! function_exists( 'fcc_get' ) || 'fc_servizio' !== get_post_type() ) {
	return;
}

$fc_id       = get_the_ID();
$fc_booking  = (string) get_post_meta( $fc_id, '_fcc_booking', true );
$fc_duration = (string) get_post_meta( $fc_id, '_fcc_duration', true );
$fc_price    = (string) get_post_meta( $fc_id, '_fcc_price', true );
$fc_cta      = (string) get_post_meta( $fc_id, '_fcc_cta_label', true );
$fc_faqs     = class_exists( 'FCC_CPT' ) ? FCC_CPT::get_faqs( $fc_id ) : array();
$fc_terms    = get_the_terms( $fc_id, 'fc_categoria' );
$fc_term     = ( $fc_terms && ! is_wp_error( $fc_terms ) ) ? $fc_terms[0] : null;

$fc_booking_labels = array(
	'none'        => __( 'Non serve prenotazione: vieni negli orari di apertura.', 'farmacia-costa-2026' ),
	'recommended' => __( 'Prenotazione consigliata: chiamaci o scrivici prima di passare.', 'farmacia-costa-2026' ),
	'required'    => __( 'Su prenotazione: contattaci per concordare l\'appuntamento.', 'farmacia-costa-2026' ),
);

$fc_wa_message = sprintf(
	/* translators: %s: nome del servizio. */
	__( 'Buongiorno, avrei bisogno di informazioni sul servizio %s.', 'farmacia-costa-2026' ),
	get_the_title()
);

// Servizi correlati: stessa categoria, escluso il corrente.
$fc_related = get_posts(
	array(
		'post_type'      => 'fc_servizio',
		'posts_per_page' => 3,
		'post__not_in'   => array( $fc_id ),
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'tax_query'      => $fc_term ? array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'fc_categoria',
				'field'    => 'term_id',
				'terms'    => $fc_term->term_id,
			),
		) : array(),
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'relation' => 'OR',
				array( 'key' => '_fcc_active', 'compare' => 'NOT EXISTS' ),
				array( 'key' => '_fcc_active', 'value' => '1' ),
			),
		),
	)
);
?>
<!-- wp:html -->
<div class="fc-section" style="padding-top:clamp(1.5rem,3vw,2.5rem)">
	<div class="fc-container fc-container--wide">
		<nav class="fc-breadcrumb" aria-label="<?php esc_attr_e( 'Percorso', 'farmacia-costa-2026' ); ?>">
			<ol>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'farmacia-costa-2026' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/i-nostri-servizi/' ) ); ?>"><?php esc_html_e( 'Servizi', 'farmacia-costa-2026' ); ?></a></li>
				<li aria-current="page"><?php echo esc_html( get_the_title() ); ?></li>
			</ol>
		</nav>

		<div class="fc-service__layout">
			<article>
				<?php if ( $fc_term ) : ?>
					<p class="fc-kicker"><?php echo esc_html( $fc_term->name ); ?></p>
				<?php endif; ?>
				<h1><?php echo esc_html( get_the_title() ); ?></h1>
				<div class="fc-prose">
					<?php the_content(); ?>
				</div>

				<?php if ( $fc_faqs ) : ?>
					<h2 style="margin-top:2.5rem"><?php esc_html_e( 'Domande frequenti su questo servizio', 'farmacia-costa-2026' ); ?></h2>
					<div class="fc-faq">
						<?php foreach ( $fc_faqs as $fc_faq ) : ?>
							<details>
								<summary><?php echo esc_html( $fc_faq['q'] ); ?></summary>
								<div><p><?php echo esc_html( $fc_faq['a'] ); ?></p></div>
							</details>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<p class="fc-disclaimer"><?php esc_html_e( 'Queste informazioni non sostituiscono il parere del medico. Per la lettura dei risultati e per qualsiasi decisione sulla tua salute rivolgiti sempre al tuo medico curante.', 'farmacia-costa-2026' ); ?></p>
			</article>

			<aside class="fc-service__facts" aria-labelledby="fc-facts-title">
				<h2 id="fc-facts-title"><?php esc_html_e( 'In breve', 'farmacia-costa-2026' ); ?></h2>
				<dl>
					<?php if ( isset( $fc_booking_labels[ $fc_booking ] ) ) : ?>
						<div>
							<dt><?php esc_html_e( 'Prenotazione', 'farmacia-costa-2026' ); ?></dt>
							<dd><?php echo esc_html( $fc_booking_labels[ $fc_booking ] ); ?></dd>
						</div>
					<?php endif; ?>
					<?php if ( $fc_duration ) : ?>
						<div>
							<dt><?php esc_html_e( 'Durata', 'farmacia-costa-2026' ); ?></dt>
							<dd><?php echo esc_html( $fc_duration ); ?></dd>
						</div>
					<?php endif; ?>
					<?php if ( $fc_price ) : ?>
						<div>
							<dt><?php esc_html_e( 'Costo', 'farmacia-costa-2026' ); ?></dt>
							<dd><?php echo esc_html( $fc_price ); ?></dd>
						</div>
					<?php endif; ?>
					<div>
						<dt><?php esc_html_e( 'Dove', 'farmacia-costa-2026' ); ?></dt>
						<dd><?php echo esc_html( fcc_address_line() ); ?></dd>
					</div>
				</dl>
				<a class="fc-btn fc-btn--whatsapp" href="<?php echo esc_url( fcc_whatsapp_url( $fc_wa_message ) ); ?>" target="_blank" rel="noopener">
					<?php echo fc26_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo esc_html( $fc_cta ? $fc_cta : __( 'Chiedi informazioni', 'farmacia-costa-2026' ) ); ?>
				</a>
				<a class="fc-btn fc-btn--outline" href="<?php echo esc_url( fcc_phone_href() ); ?>">
					<?php echo fc26_icon( 'phone', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php esc_html_e( 'Chiama la farmacia', 'farmacia-costa-2026' ); ?>
				</a>
			</aside>
		</div>

		<?php if ( $fc_related ) : ?>
			<section aria-labelledby="fc-related-title" style="margin-top:clamp(3rem,6vw,4.5rem)">
				<h2 id="fc-related-title"><?php esc_html_e( 'Potrebbero interessarti anche', 'farmacia-costa-2026' ); ?></h2>
				<div class="fc-cards" style="margin-top:1.5rem">
					<?php foreach ( $fc_related as $fc_rel ) : ?>
						<?php echo fc26_service_card( $fc_rel->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
	</div>
</div>
<!-- /wp:html -->

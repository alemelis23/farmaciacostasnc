<?php
/**
 * Title: Footer
 * Slug: farmacia-costa/footer
 * Categories: farmacia-costa
 * Inserter: no
 *
 * Footer con dati aziendali centralizzati, servizi principali,
 * social e collegamenti legali. Nessun dato societario inventato.
 *
 * @package FarmaciaCosta2026
 */

$fc_has_core = function_exists( 'fcc_get' );

$fc_services = $fc_has_core ? get_posts(
	array(
		'post_type'      => 'fc_servizio',
		'posts_per_page' => 6,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'relation' => 'AND',
			array(
				'relation' => 'OR',
				array( 'key' => '_fcc_active', 'compare' => 'NOT EXISTS' ),
				array( 'key' => '_fcc_active', 'value' => '1' ),
			),
		),
	)
) : array();

$fc_privacy_url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
$fc_cookie_page = get_page_by_path( 'cookie-policy' );
?>
<!-- wp:html -->
	<div class="fc-container fc-container--wide">
		<div class="fc-footer__grid">
			<div class="fc-footer__about">
				<a class="fc-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#F8F4EC">
					<span class="fc-logo__mark"><?php echo fc26_icon( 'cross', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span class="fc-logo__name"><?php echo esc_html( $fc_has_core ? fcc_get( 'business_name' ) : get_bloginfo( 'name' ) ); ?></span>
				</a>
				<p><?php esc_html_e( 'La prima farmacia di Carbonia, aperta nel 1938 con la fondazione della città. Oggi come allora: farmacisti che ascoltano, servizi per la prevenzione e telemedicina in Piazza Matteotti.', 'farmacia-costa-2026' ); ?></p>
				<?php if ( $fc_has_core && ( fcc_get( 'facebook' ) || fcc_get( 'instagram' ) ) ) : ?>
					<div class="fc-footer__social">
						<?php if ( fcc_get( 'facebook' ) ) : ?>
							<a href="<?php echo esc_url( fcc_get( 'facebook' ) ); ?>" target="_blank" rel="noopener">
								<span class="screen-reader-text"><?php esc_html_e( 'Farmacia Costa su Facebook (si apre in una nuova scheda)', 'farmacia-costa-2026' ); ?></span>
								<?php echo fc26_icon( 'facebook', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
						<?php if ( fcc_get( 'instagram' ) ) : ?>
							<a href="<?php echo esc_url( fcc_get( 'instagram' ) ); ?>" target="_blank" rel="noopener">
								<span class="screen-reader-text"><?php esc_html_e( 'Farmacia Costa su Instagram (si apre in una nuova scheda)', 'farmacia-costa-2026' ); ?></span>
								<?php echo fc26_icon( 'instagram', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div>
				<h2><?php esc_html_e( 'Il sito', 'farmacia-costa-2026' ); ?></h2>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/i-nostri-servizi/' ) ); ?>"><?php esc_html_e( 'Tutti i servizi', 'farmacia-costa-2026' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/ricetta-in-farmacia/' ) ); ?>"><?php esc_html_e( 'Invia la ricetta', 'farmacia-costa-2026' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/chi-siamo/' ) ); ?>"><?php esc_html_e( 'Chi siamo e il team', 'farmacia-costa-2026' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contattaci/' ) ); ?>"><?php esc_html_e( 'Contatti', 'farmacia-costa-2026' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/lavora-con-noi/' ) ); ?>"><?php esc_html_e( 'Lavora con noi', 'farmacia-costa-2026' ); ?></a></li>
				</ul>
			</div>

			<?php if ( $fc_services ) : ?>
				<div>
					<h2><?php esc_html_e( 'Servizi principali', 'farmacia-costa-2026' ); ?></h2>
					<ul>
						<?php foreach ( $fc_services as $fc_service ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $fc_service ) ); ?>"><?php echo esc_html( get_the_title( $fc_service ) ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( $fc_has_core ) : ?>
				<div>
					<h2><?php esc_html_e( 'Dove siamo e orari', 'farmacia-costa-2026' ); ?></h2>
					<address>
						<?php echo esc_html( fcc_address_line() ); ?><br>
						<a href="<?php echo esc_url( fcc_phone_href() ); ?>"><?php echo esc_html( fcc_get( 'phone_display' ) ); ?></a><br>
						<a href="<?php echo esc_url( fcc_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Scrivici su WhatsApp', 'farmacia-costa-2026' ); ?></a><br>
						<a href="mailto:<?php echo esc_attr( antispambot( fcc_get( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( fcc_get( 'email' ) ) ); ?></a>
					</address>
					<?php echo wp_kses( fcc_render_hours(), fc26_kses_allowed() ); ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="fc-footer__legal">
			<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $fc_has_core ? fcc_get( 'business_name' ) : get_bloginfo( 'name' ) ); ?> · <?php echo esc_html( $fc_has_core ? fcc_get( 'city' ) : '' ); ?></p>
			<ul>
				<?php if ( $fc_privacy_url ) : ?>
					<li><a href="<?php echo esc_url( $fc_privacy_url ); ?>"><?php esc_html_e( 'Privacy Policy', 'farmacia-costa-2026' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $fc_cookie_page ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( $fc_cookie_page ) ); ?>"><?php esc_html_e( 'Cookie Policy', 'farmacia-costa-2026' ); ?></a></li>
				<?php endif; ?>
				<li>
					<!-- Aggancio per la CMP: il gestore del consenso deve intercettare .fc-manage-cookies (vedi README). -->
					<a href="#" class="fc-manage-cookies"><?php esc_html_e( 'Gestisci preferenze cookie', 'farmacia-costa-2026' ); ?></a>
				</li>
			</ul>
		</div>
	</div>
<!-- /wp:html -->

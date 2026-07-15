<?php
/**
 * Title: Header
 * Slug: farmacia-costa/header
 * Categories: farmacia-costa
 * Inserter: no
 *
 * Header sticky con logo, navigazione accessibile (fallback nativo con
 * details/summary su mobile, nessuna dipendenza JS) e azioni rapide.
 *
 * @package FarmaciaCosta2026
 */

$fc_has_core = function_exists( 'fcc_phone_href' );

$fc_menu = array(
	array( 'label' => __( 'Home', 'farmacia-costa-2026' ), 'url' => home_url( '/' ) ),
	array( 'label' => __( 'Servizi', 'farmacia-costa-2026' ), 'url' => home_url( '/i-nostri-servizi/' ) ),
	array( 'label' => __( 'Invia la ricetta', 'farmacia-costa-2026' ), 'url' => home_url( '/ricetta-in-farmacia/' ) ),
	array( 'label' => __( 'Chi siamo', 'farmacia-costa-2026' ), 'url' => home_url( '/chi-siamo/' ) ),
);

// "Consigli" compare solo se esiste la pagina articoli e almeno un articolo pubblicato.
$fc_posts_page  = (int) get_option( 'page_for_posts' );
$fc_posts_count = wp_count_posts();
if ( $fc_posts_page && 'publish' === get_post_status( $fc_posts_page ) && $fc_posts_count && (int) $fc_posts_count->publish > 0 ) {
	$fc_menu[] = array( 'label' => __( 'Consigli', 'farmacia-costa-2026' ), 'url' => get_permalink( $fc_posts_page ) );
}
$fc_menu[] = array( 'label' => __( 'Contatti', 'farmacia-costa-2026' ), 'url' => home_url( '/contattaci/' ) );

$fc_current = trailingslashit( home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) ) );

$fc_menu_items = '';
foreach ( $fc_menu as $fc_item ) {
	$fc_is_current  = trailingslashit( $fc_item['url'] ) === $fc_current;
	$fc_menu_items .= sprintf(
		'<li><a href="%s"%s>%s</a></li>',
		esc_url( $fc_item['url'] ),
		$fc_is_current ? ' aria-current="page"' : '',
		esc_html( $fc_item['label'] )
	);
}
?>
<!-- wp:html -->
	<div class="fc-header__inner">
		<a class="fc-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<span class="fc-logo__mark"><?php echo fc26_icon( 'cross', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<span class="fc-logo__name">
				<?php echo esc_html( $fc_has_core ? fcc_get( 'business_name' ) : get_bloginfo( 'name' ) ); ?>
				<span class="fc-logo__tag"><?php esc_html_e( 'Carbonia · dal 1938', 'farmacia-costa-2026' ); ?></span>
			</span>
		</a>

		<nav class="fc-nav fc-nav--desktop" aria-label="<?php esc_attr_e( 'Navigazione principale', 'farmacia-costa-2026' ); ?>">
			<ul class="fc-nav__list"><?php echo $fc_menu_items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
		</nav>

		<div class="fc-header__actions">
			<?php if ( $fc_has_core ) : ?>
				<a class="fc-btn fc-btn--outline fc-btn--small fc-btn--call" href="<?php echo esc_url( fcc_phone_href() ); ?>">
					<?php echo fc26_icon( 'phone', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo esc_html( fcc_get( 'phone_display' ) ); ?>
				</a>
				<a class="fc-btn fc-btn--whatsapp fc-btn--small" href="<?php echo esc_url( fcc_whatsapp_url() ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Scrivici su WhatsApp', 'farmacia-costa-2026' ); ?>">
					<?php echo fc26_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="fc-btn__label">WhatsApp</span>
				</a>
			<?php endif; ?>

			<nav class="fc-nav fc-nav--mobile" aria-label="<?php esc_attr_e( 'Menu', 'farmacia-costa-2026' ); ?>">
				<details class="fc-nav__disclosure" data-fc-nav>
					<summary class="fc-nav__toggle" aria-label="<?php esc_attr_e( 'Menu di navigazione', 'farmacia-costa-2026' ); ?>">
						<span class="fc-nav__toggle-bars" aria-hidden="true"><span></span><span></span><span></span></span>
						<span class="fc-nav__toggle-label"><?php esc_html_e( 'Menu', 'farmacia-costa-2026' ); ?></span>
					</summary>
					<div class="fc-nav__panel">
						<ul class="fc-nav__list"><?php echo $fc_menu_items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
						<?php if ( $fc_has_core ) : ?>
							<div class="fc-nav__panel-actions">
								<a class="fc-btn fc-btn--primary" href="<?php echo esc_url( fcc_phone_href() ); ?>">
									<?php echo fc26_icon( 'phone', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php esc_html_e( 'Chiama la farmacia', 'farmacia-costa-2026' ); ?>
								</a>
								<a class="fc-btn fc-btn--whatsapp" href="<?php echo esc_url( fcc_whatsapp_url() ); ?>" target="_blank" rel="noopener">
									<?php echo fc26_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php esc_html_e( 'Scrivici su WhatsApp', 'farmacia-costa-2026' ); ?>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</details>
			</nav>
		</div>
	</div>
<!-- /wp:html -->

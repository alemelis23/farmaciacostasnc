<?php
/**
 * Farmacia Costa 2026 — funzioni del tema.
 *
 * Il tema gestisce solo la presentazione. Dati aziendali, servizi, team,
 * form e dati strutturati vivono nel plugin `farmacia-costa-core`:
 * il sito resta integro anche cambiando tema.
 *
 * @package FarmaciaCosta2026
 */

defined( 'ABSPATH' ) || exit;

define( 'FC26_VERSION', '1.0.0' );

/**
 * Supporti del tema.
 */
function fc26_setup() {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/main.css' );
	// Pattern remoti e core nascosti: gli editor vedono solo i pattern della farmacia.
	remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', 'fc26_setup' );

add_filter( 'should_load_remote_block_patterns', '__return_false' );

/**
 * Stili e script frontend.
 */
function fc26_assets() {
	wp_enqueue_style(
		'fc26-main',
		get_theme_file_uri( 'assets/css/main.css' ),
		array(),
		FC26_VERSION
	);

	wp_enqueue_script(
		'fc26-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		array(),
		FC26_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	// Filtro servizi: solo dove serve (home e archivio servizi).
	if ( is_front_page() || is_post_type_archive( 'fc_servizio' ) || is_tax( 'fc_categoria' ) ) {
		wp_enqueue_script(
			'fc26-service-filter',
			get_theme_file_uri( 'assets/js/service-filter.js' ),
			array(),
			FC26_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fc26_assets' );

/**
 * Categorie di pattern del tema.
 */
function fc26_pattern_categories() {
	register_block_pattern_category(
		'farmacia-costa',
		array( 'label' => __( 'Farmacia Costa', 'farmacia-costa-2026' ) )
	);
}
add_action( 'init', 'fc26_pattern_categories' );

/**
 * Icone SVG inline, accessibili e ottimizzate (nessun icon font).
 *
 * @param string $name Nome icona.
 * @param int    $size Dimensione in px.
 * @return string SVG.
 */
function fc26_icon( $name, $size = 24 ) {
	$paths = array(
		'phone'     => '<path d="M6.6 3.2c.5-.5 1.3-.4 1.7.1l2 2.6c.4.5.3 1.2-.1 1.6l-1 1a.9.9 0 0 0-.2 1c.5 1.2 1.2 2.3 2.2 3.3s2.1 1.7 3.3 2.2c.3.2.8.1 1-.2l1-1c.4-.4 1.1-.5 1.6-.1l2.6 2c.5.4.6 1.2.1 1.7l-1.2 1.2c-.6.6-1.4.9-2.2.7-2.8-.6-5.5-2-7.7-4.2S6.1 10.2 5.5 7.4c-.2-.8.1-1.6.7-2.2z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'whatsapp'  => '<path d="M12 3.5a8.4 8.4 0 0 0-7.3 12.7L3.5 20l3.9-1.1A8.5 8.5 0 1 0 12 3.5z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9.2 8.4c-.3-.6-.5-.6-.8-.6h-.6c-.2 0-.6.1-.9.4-.3.4-1.1 1.1-1.1 2.7s1.2 3.1 1.3 3.3c.2.2 2.3 3.6 5.6 4.9 2.8 1.1 3.3.9 3.9.8.6 0 1.9-.8 2.1-1.5.3-.7.3-1.4.2-1.5l-.7-.4-2.4-1.2c-.3-.1-.6-.2-.8.2l-1 1.3c-.2.2-.4.2-.7.1-.3-.2-1.3-.5-2.5-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.6-.7c.2-.2.2-.4.3-.6.1-.2 0-.4 0-.6L9.2 8.4z" fill="currentColor" transform="scale(0.82) translate(2.6 1.2)"/>',
		'pin'       => '<path d="M12 21s-7-5.3-7-11a7 7 0 0 1 14 0c0 5.7-7 11-7 11z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.6" fill="none" stroke="currentColor" stroke-width="1.8"/>',
		'clock'     => '<circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M12 7.5V12l3 2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'mail'      => '<rect x="3.5" y="5.5" width="17" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m4.5 7 7.5 6 7.5-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'heart'     => '<path d="M12 20.3 5.4 13.6a4.6 4.6 0 0 1 0-6.5 4.5 4.5 0 0 1 6.4 0l.2.3.2-.3a4.5 4.5 0 0 1 6.4 0 4.6 4.6 0 0 1 0 6.5z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8.5 12.5h2l1-2 1.6 3.4 1-1.4h1.4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>',
		'drop'      => '<path d="M12 3.5s6 6.2 6 10.3a6 6 0 0 1-12 0C6 9.7 12 3.5 12 3.5z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9.5 14a2.6 2.6 0 0 0 2.5 2.7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
		'stetho'    => '<path d="M6 4v5a4 4 0 0 0 8 0V4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M10 13v2.5a4.5 4.5 0 0 0 9 0V13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="19" cy="10.5" r="2" fill="none" stroke="currentColor" stroke-width="1.8"/>',
		'ear'       => '<path d="M8 19a3 3 0 0 0 5.8-.7c.3-1.5 1-2.4 2-3.4a6.5 6.5 0 1 0-10.4-7.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M9.5 9.5a3.5 3.5 0 0 1 6.4 1.9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
		'card'      => '<rect x="3.5" y="5.5" width="17" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 9.5h17M7 14h4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
		'rx'        => '<path d="M7 4h5a3.5 3.5 0 0 1 0 7H7V4zM7 11v9M12 11l6 9M18 11l-6 9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'arrow'     => '<path d="M5 12h14m-6-6 6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'search'    => '<circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4.5 4.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
		'cross'     => '<path d="M9.5 4.5h5v5h5v5h-5v5h-5v-5h-5v-5h5z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
		'facebook'  => '<path d="M14 8.5h2.5V5.5H14a3.5 3.5 0 0 0-3.5 3.5v2h-2v3h2v6.5h3V14h2.5l.5-3h-3V9a.9.9 0 0 1 1-.5z" fill="currentColor"/>',
		'instagram' => '<rect x="4" y="4" width="16" height="16" rx="4.5" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3.6" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="16.7" cy="7.3" r="1.2" fill="currentColor"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="fc-icon fc-icon--%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		(int) $size,
		$paths[ $name ]
	);
}

/**
 * Barra mobile inferiore (Chiama / WhatsApp / Indicazioni) e pulsante
 * WhatsApp flottante su desktop. Le informazioni essenziali funzionano
 * anche senza JavaScript: sono semplici link.
 */
function fc26_persistent_actions() {
	if ( ! function_exists( 'fcc_phone_href' ) ) {
		return;
	}
	?>
	<nav class="fc-mobile-bar" aria-label="<?php esc_attr_e( 'Azioni rapide', 'farmacia-costa-2026' ); ?>">
		<a href="<?php echo esc_url( fcc_phone_href() ); ?>">
			<?php echo fc26_icon( 'phone', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( 'Chiama', 'farmacia-costa-2026' ); ?></span>
		</a>
		<a href="<?php echo esc_url( fcc_whatsapp_url() ); ?>" target="_blank" rel="noopener">
			<?php echo fc26_icon( 'whatsapp', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span>WhatsApp</span>
		</a>
		<a href="<?php echo esc_url( fcc_maps_url() ); ?>" target="_blank" rel="noopener">
			<?php echo fc26_icon( 'pin', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( 'Indicazioni', 'farmacia-costa-2026' ); ?></span>
		</a>
	</nav>
	<a class="fc-wa-float" href="<?php echo esc_url( fcc_whatsapp_url() ); ?>" target="_blank" rel="noopener">
		<?php echo fc26_icon( 'whatsapp', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span class="fc-wa-float__label"><?php esc_html_e( 'Scrivici su WhatsApp', 'farmacia-costa-2026' ); ?></span>
	</a>
	<?php
}
add_action( 'wp_footer', 'fc26_persistent_actions' );

/**
 * Escaping ammesso per l'HTML dei pattern (SVG, link, tabella orari).
 *
 * @return array
 */
function fc26_kses_allowed() {
	$allowed        = wp_kses_allowed_html( 'post' );
	$svg_attrs      = array(
		'class'          => true,
		'width'          => true,
		'height'         => true,
		'viewbox'        => true,
		'aria-hidden'    => true,
		'focusable'      => true,
		'fill'           => true,
		'stroke'         => true,
		'stroke-width'   => true,
		'stroke-linecap' => true,
		'stroke-linejoin' => true,
		'd'              => true,
		'x'              => true,
		'y'              => true,
		'rx'             => true,
		'cx'             => true,
		'cy'             => true,
		'r'              => true,
		'transform'      => true,
	);
	$allowed['svg']    = $svg_attrs;
	$allowed['path']   = $svg_attrs;
	$allowed['rect']   = $svg_attrs;
	$allowed['circle'] = $svg_attrs;
	return $allowed;
}

/**
 * Card servizio riutilizzabile (archivio, home, correlati).
 *
 * @param int  $post_id   ID servizio.
 * @param bool $show_meta Mostra badge prenotazione.
 * @return string HTML.
 */
function fc26_service_card( $post_id, $show_meta = true ) {
	$title   = get_the_title( $post_id );
	$short   = (string) get_post_meta( $post_id, '_fcc_short', true );
	$booking = (string) get_post_meta( $post_id, '_fcc_booking', true );
	$terms   = get_the_terms( $post_id, 'fc_categoria' );
	$term    = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;

	$icon_map = array(
		'telemedicina'         => 'stetho',
		'prevenzione'          => 'heart',
		'autoanalisi'          => 'drop',
		'consulenze'           => 'ear',
		'servizi-al-cittadino' => 'card',
	);
	$icon = $term && isset( $icon_map[ $term->slug ] ) ? $icon_map[ $term->slug ] : 'cross';

	$badge = '';
	if ( $show_meta && 'required' === $booking ) {
		$badge = '<span class="fc-badge">' . esc_html__( 'Su prenotazione', 'farmacia-costa-2026' ) . '</span>';
	} elseif ( $show_meta && 'none' === $booking ) {
		$badge = '<span class="fc-badge fc-badge--free">' . esc_html__( 'Senza prenotazione', 'farmacia-costa-2026' ) . '</span>';
	} elseif ( $show_meta && 'recommended' === $booking ) {
		$badge = '<span class="fc-badge">' . esc_html__( 'Prenotazione consigliata', 'farmacia-costa-2026' ) . '</span>';
	}

	$search_haystack = strtolower( $title . ' ' . $short . ' ' . ( $term ? $term->name : '' ) );

	return sprintf(
		'<article class="fc-card fc-card--service" data-fc-service data-fc-category="%1$s" data-fc-search="%2$s">
			<div class="fc-card__icon">%3$s</div>
			<div class="fc-card__body">
				%4$s
				<h3 class="fc-card__title"><a href="%5$s">%6$s</a></h3>
				<p class="fc-card__text">%7$s</p>
				%8$s
			</div>
			<span class="fc-card__cta" aria-hidden="true">%9$s %10$s</span>
		</article>',
		esc_attr( $term ? $term->slug : '' ),
		esc_attr( $search_haystack ),
		fc26_icon( $icon, 28 ),
		$term ? '<span class="fc-card__kicker">' . esc_html( $term->name ) . '</span>' : '',
		esc_url( get_permalink( $post_id ) ),
		esc_html( $title ),
		esc_html( $short ),
		$badge,
		esc_html__( 'Scopri il servizio', 'farmacia-costa-2026' ),
		fc26_icon( 'arrow', 18 )
	);
}

/**
 * Pagina 404: suggerisce la ricerca e i contatti (vedi templates/404.html).
 * Meta viewport e theme-color.
 */
function fc26_meta_head() {
	echo '<meta name="theme-color" content="#14524A">' . "\n";
}
add_action( 'wp_head', 'fc26_meta_head', 1 );

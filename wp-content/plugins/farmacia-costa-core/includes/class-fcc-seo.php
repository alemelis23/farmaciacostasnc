<?php
/**
 * SEO di base: meta description, canonical per archivi, Open Graph, Twitter Card.
 *
 * Se è attivo un plugin SEO dedicato (Yoast, Rank Math, SEOPress, AIOSEO),
 * questa classe non emette nulla per evitare duplicazioni.
 *
 * @package FarmaciaCostaCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Metadati SEO.
 */
class FCC_SEO {

	/**
	 * Aggancia gli hook.
	 */
	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'output' ), 4 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta' ) );
		add_filter( 'document_title_parts', array( __CLASS__, 'title_parts' ) );
	}

	/**
	 * È attivo un plugin SEO dedicato?
	 *
	 * @return bool
	 */
	private static function seo_plugin_active() {
		return defined( 'WPSEO_VERSION' )
			|| defined( 'RANK_MATH_VERSION' )
			|| defined( 'SEOPRESS_VERSION' )
			|| defined( 'AIOSEO_VERSION' );
	}

	/**
	 * Meta box "Descrizione SEO" per pagine, articoli e servizi.
	 */
	public static function meta_box() {
		if ( self::seo_plugin_active() ) {
			return;
		}
		foreach ( array( 'page', 'post', 'fc_servizio' ) as $type ) {
			add_meta_box( 'fcc-seo', __( 'Descrizione SEO', 'farmacia-costa-core' ), array( __CLASS__, 'render_box' ), $type, 'normal', 'low' );
		}
	}

	/**
	 * Campo descrizione.
	 *
	 * @param WP_Post $post Post corrente.
	 */
	public static function render_box( $post ) {
		wp_nonce_field( 'fcc_seo_meta', 'fcc_seo_nonce' );
		$desc = get_post_meta( $post->ID, '_fcc_meta_desc', true );
		?>
		<p>
			<label for="fcc-meta-desc"><?php esc_html_e( 'Meta description (max 160 caratteri). Se vuota viene usato il riassunto.', 'farmacia-costa-core' ); ?></label>
			<textarea id="fcc-meta-desc" name="fcc_meta_desc" rows="2" class="large-text" maxlength="170"><?php echo esc_textarea( $desc ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Salvataggio.
	 *
	 * @param int $post_id ID del post.
	 */
	public static function save_meta( $post_id ) {
		if ( ! isset( $_POST['fcc_seo_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fcc_seo_nonce'] ), 'fcc_seo_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, '_fcc_meta_desc', sanitize_textarea_field( wp_unslash( $_POST['fcc_meta_desc'] ?? '' ) ) );
	}

	/**
	 * Tagline coerente nel titolo della home.
	 *
	 * @param array $parts Parti del titolo.
	 * @return array
	 */
	public static function title_parts( $parts ) {
		if ( is_front_page() && empty( $parts['tagline'] ) ) {
			$parts['tagline'] = sprintf(
				/* translators: %s: città. */
				__( 'La farmacia di %s, dal 1938', 'farmacia-costa-core' ),
				fcc_get( 'city' )
			);
		}
		return $parts;
	}

	/**
	 * Descrizione della richiesta corrente.
	 *
	 * @return string
	 */
	private static function description() {
		if ( is_front_page() ) {
			$desc = is_singular() ? (string) get_post_meta( get_the_ID(), '_fcc_meta_desc', true ) : '';
			if ( '' !== $desc ) {
				return wp_strip_all_tags( $desc );
			}
			return sprintf(
				/* translators: 1: nome, 2: città. */
				__( '%1$s, a %2$s dal 1938: farmaci, telemedicina, autoanalisi e consigli di farmacisti che conosci. In Piazza Matteotti, nel cuore della città.', 'farmacia-costa-core' ),
				fcc_get( 'business_name' ),
				fcc_get( 'city' )
			);
		}
		if ( is_singular() ) {
			$desc = (string) get_post_meta( get_the_ID(), '_fcc_meta_desc', true );
			if ( '' === $desc ) {
				$desc = (string) get_post_meta( get_the_ID(), '_fcc_short', true );
			}
			if ( '' === $desc && has_excerpt() ) {
				$desc = get_the_excerpt();
			}
			if ( '' === $desc ) {
				// Fallback: prime frasi del contenuto della pagina.
				$post = get_post();
				if ( $post ) {
					$desc = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 28, '…' );
				}
			}
			return wp_strip_all_tags( $desc );
		}
		if ( is_post_type_archive( 'fc_servizio' ) ) {
			return sprintf(
				/* translators: %s: città. */
				__( 'Tutti i servizi di %1$s a %2$s: telemedicina, prevenzione, autoanalisi e consulenze. Scopri come prenotare.', 'farmacia-costa-core' ),
				fcc_get( 'business_name' ),
				fcc_get( 'city' )
			);
		}
		return '';
	}

	/**
	 * Emissione meta tag.
	 */
	public static function output() {
		if ( self::seo_plugin_active() ) {
			return;
		}

		$desc = self::description();
		if ( $desc ) {
			echo '<meta name="description" content="' . esc_attr( wp_html_excerpt( $desc, 160, '…' ) ) . '">' . "\n";
		}

		// Canonical per archivi (per i singolari ci pensa WP core con rel_canonical).
		if ( is_post_type_archive( 'fc_servizio' ) ) {
			echo '<link rel="canonical" href="' . esc_url( get_post_type_archive_link( 'fc_servizio' ) ) . '">' . "\n";
		} elseif ( is_tax( 'fc_categoria' ) ) {
			$link = get_term_link( get_queried_object() );
			if ( ! is_wp_error( $link ) ) {
				echo '<link rel="canonical" href="' . esc_url( $link ) . '">' . "\n";
			}
		}

		// Open Graph e Twitter Card.
		$title = wp_get_document_title();
		$url   = is_singular() ? get_permalink() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
		echo '<meta property="og:locale" content="it_IT">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
		echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		if ( $desc ) {
			echo '<meta property="og:description" content="' . esc_attr( wp_html_excerpt( $desc, 200, '…' ) ) . '">' . "\n";
		}
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";

		$image = '';
		if ( is_singular() && has_post_thumbnail() ) {
			$image = get_the_post_thumbnail_url( null, 'large' );
		}
		if ( ! $image ) {
			$logo_id = get_theme_mod( 'custom_logo' );
			$image   = $logo_id ? (string) wp_get_attachment_image_url( $logo_id, 'full' ) : '';
		}
		if ( $image ) {
			echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		}
		echo '<meta name="twitter:card" content="summary">' . "\n";
	}
}

<?php
/**
 * Contenuti strutturati: servizi, categorie di servizio, team.
 *
 * @package FarmaciaCostaCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Custom post type e meta box.
 */
class FCC_CPT {

	/**
	 * Aggancia gli hook.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_boxes' ) );
		add_action( 'save_post_fc_servizio', array( __CLASS__, 'save_service_meta' ) );
		add_action( 'save_post_fc_team', array( __CLASS__, 'save_team_meta' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'hide_inactive_services' ) );
	}

	/**
	 * Registra CPT e tassonomia.
	 */
	public static function register() {
		register_post_type(
			'fc_servizio',
			array(
				'labels'       => array(
					'name'          => __( 'Servizi', 'farmacia-costa-core' ),
					'singular_name' => __( 'Servizio', 'farmacia-costa-core' ),
					'add_new_item'  => __( 'Aggiungi servizio', 'farmacia-costa-core' ),
					'edit_item'     => __( 'Modifica servizio', 'farmacia-costa-core' ),
				),
				'public'       => true,
				'menu_icon'    => 'dashicons-heart',
				'menu_position' => 20,
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
				'has_archive'  => 'i-nostri-servizi',
				'rewrite'      => array(
					'slug'       => 'servizi',
					'with_front' => false,
				),
				'show_in_rest' => true,
			)
		);

		register_taxonomy(
			'fc_categoria',
			'fc_servizio',
			array(
				'labels'            => array(
					'name'          => __( 'Categorie servizio', 'farmacia-costa-core' ),
					'singular_name' => __( 'Categoria servizio', 'farmacia-costa-core' ),
				),
				'public'            => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'rewrite'           => array(
					'slug'       => 'categoria-servizio',
					'with_front' => false,
				),
				'show_in_rest'      => true,
			)
		);

		register_post_type(
			'fc_team',
			array(
				'labels'              => array(
					'name'          => __( 'Team', 'farmacia-costa-core' ),
					'singular_name' => __( 'Membro del team', 'farmacia-costa-core' ),
					'add_new_item'  => __( 'Aggiungi membro del team', 'farmacia-costa-core' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'menu_icon'           => 'dashicons-groups',
				'menu_position'       => 21,
				'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
				'show_in_rest'        => true,
			)
		);

		// Meta esposti al blocco/pattern e al tema.
		$service_meta = array(
			'_fcc_short'     => 'string',  // Descrizione breve per le card.
			'_fcc_booking'   => 'string',  // none | required | recommended.
			'_fcc_duration'  => 'string',
			'_fcc_price'     => 'string',
			'_fcc_cta_label' => 'string',
			'_fcc_active'    => 'boolean',
			'_fcc_featured'  => 'boolean',
			'_fcc_faqs'      => 'string',  // JSON [{"q":"","a":""}].
		);
		foreach ( $service_meta as $key => $type ) {
			register_post_meta(
				'fc_servizio',
				$key,
				array(
					'type'          => $type,
					'single'        => true,
					'show_in_rest'  => true,
					'auth_callback' => static function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}

	/**
	 * Crea le categorie di servizio iniziali (idempotente).
	 */
	public static function seed_terms() {
		$terms = array(
			'Telemedicina'         => 'telemedicina',
			'Prevenzione'          => 'prevenzione',
			'Autoanalisi'          => 'autoanalisi',
			'Consulenze'           => 'consulenze',
			'Servizi al cittadino' => 'servizi-al-cittadino',
		);
		foreach ( $terms as $name => $slug ) {
			if ( ! term_exists( $slug, 'fc_categoria' ) ) {
				wp_insert_term( $name, 'fc_categoria', array( 'slug' => $slug ) );
			}
		}
	}

	/**
	 * Nasconde i servizi disattivati dalle query pubbliche (archivio, ricerca, dettaglio).
	 *
	 * @param WP_Query $query Query corrente.
	 */
	public static function hide_inactive_services( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}
		$post_type = $query->get( 'post_type' );
		$is_service_query = 'fc_servizio' === $post_type
			|| ( is_array( $post_type ) && in_array( 'fc_servizio', $post_type, true ) )
			|| $query->is_post_type_archive( 'fc_servizio' )
			|| $query->is_tax( 'fc_categoria' )
			|| $query->is_search();

		if ( ! $is_service_query ) {
			return;
		}

		$meta_query   = (array) $query->get( 'meta_query' );
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key'     => '_fcc_active',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'   => '_fcc_active',
				'value' => '1',
			),
		);
		$query->set( 'meta_query', $meta_query );

		if ( $query->is_post_type_archive( 'fc_servizio' ) || $query->is_tax( 'fc_categoria' ) ) {
			$query->set( 'orderby', 'menu_order title' );
			$query->set( 'order', 'ASC' );
			$query->set( 'posts_per_page', 50 );
		}
	}

	/**
	 * Meta box.
	 */
	public static function meta_boxes() {
		add_meta_box( 'fcc-service-details', __( 'Dettagli del servizio', 'farmacia-costa-core' ), array( __CLASS__, 'render_service_box' ), 'fc_servizio', 'normal', 'high' );
		add_meta_box( 'fcc-service-faqs', __( 'FAQ del servizio', 'farmacia-costa-core' ), array( __CLASS__, 'render_faq_box' ), 'fc_servizio', 'normal', 'default' );
		add_meta_box( 'fcc-team-details', __( 'Dettagli del membro del team', 'farmacia-costa-core' ), array( __CLASS__, 'render_team_box' ), 'fc_team', 'normal', 'high' );
	}

	/**
	 * Meta box dettagli servizio.
	 *
	 * @param WP_Post $post Post corrente.
	 */
	public static function render_service_box( $post ) {
		wp_nonce_field( 'fcc_service_meta', 'fcc_service_nonce' );
		$short    = get_post_meta( $post->ID, '_fcc_short', true );
		$booking  = get_post_meta( $post->ID, '_fcc_booking', true );
		$duration = get_post_meta( $post->ID, '_fcc_duration', true );
		$price    = get_post_meta( $post->ID, '_fcc_price', true );
		$cta      = get_post_meta( $post->ID, '_fcc_cta_label', true );
		$active   = get_post_meta( $post->ID, '_fcc_active', true );
		$featured = get_post_meta( $post->ID, '_fcc_featured', true );
		$active   = ( '' === $active ) ? '1' : $active;
		?>
		<p>
			<label for="fcc-short"><strong><?php esc_html_e( 'Descrizione breve (card e archivio)', 'farmacia-costa-core' ); ?></strong></label><br>
			<textarea id="fcc-short" name="fcc_short" rows="2" class="large-text" maxlength="200"><?php echo esc_textarea( $short ); ?></textarea>
		</p>
		<p>
			<label for="fcc-booking"><strong><?php esc_html_e( 'Prenotazione', 'farmacia-costa-core' ); ?></strong></label><br>
			<select id="fcc-booking" name="fcc_booking">
				<option value="none" <?php selected( $booking, 'none' ); ?>><?php esc_html_e( 'Non necessaria', 'farmacia-costa-core' ); ?></option>
				<option value="recommended" <?php selected( $booking, 'recommended' ); ?>><?php esc_html_e( 'Consigliata', 'farmacia-costa-core' ); ?></option>
				<option value="required" <?php selected( $booking, 'required' ); ?>><?php esc_html_e( 'Necessaria', 'farmacia-costa-core' ); ?></option>
			</select>
		</p>
		<p>
			<label for="fcc-duration"><strong><?php esc_html_e( 'Durata (facoltativa, solo se confermata)', 'farmacia-costa-core' ); ?></strong></label><br>
			<input type="text" id="fcc-duration" name="fcc_duration" class="regular-text" value="<?php echo esc_attr( $duration ); ?>" placeholder="<?php esc_attr_e( 'Es. Circa 10 minuti', 'farmacia-costa-core' ); ?>">
		</p>
		<p>
			<label for="fcc-price"><strong><?php esc_html_e( 'Prezzo (facoltativo, solo se confermato)', 'farmacia-costa-core' ); ?></strong></label><br>
			<input type="text" id="fcc-price" name="fcc_price" class="regular-text" value="<?php echo esc_attr( $price ); ?>" placeholder="<?php esc_attr_e( 'Es. 25 €', 'farmacia-costa-core' ); ?>">
		</p>
		<p>
			<label for="fcc-cta"><strong><?php esc_html_e( 'Etichetta CTA (facoltativa)', 'farmacia-costa-core' ); ?></strong></label><br>
			<input type="text" id="fcc-cta" name="fcc_cta_label" class="regular-text" value="<?php echo esc_attr( $cta ); ?>" placeholder="<?php esc_attr_e( 'Es. Prenota il tuo Holter', 'farmacia-costa-core' ); ?>">
		</p>
		<p>
			<label>
				<input type="checkbox" name="fcc_active" value="1" <?php checked( $active, '1' ); ?>>
				<strong><?php esc_html_e( 'Servizio attivo (visibile sul sito)', 'farmacia-costa-core' ); ?></strong>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="fcc_featured" value="1" <?php checked( $featured, '1' ); ?>>
				<?php esc_html_e( 'In evidenza in homepage', 'farmacia-costa-core' ); ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Meta box FAQ del servizio.
	 *
	 * @param WP_Post $post Post corrente.
	 */
	public static function render_faq_box( $post ) {
		$faqs = json_decode( (string) get_post_meta( $post->ID, '_fcc_faqs', true ), true );
		$faqs = is_array( $faqs ) ? $faqs : array();
		$rows = max( count( $faqs ) + 1, 3 );
		?>
		<p class="description"><?php esc_html_e( 'Domande e risposte mostrate nella pagina del servizio. Lascia vuote le righe non utilizzate.', 'farmacia-costa-core' ); ?></p>
		<?php for ( $i = 0; $i < $rows; $i++ ) : ?>
			<fieldset style="margin-bottom:12px;border:1px solid #dcdcde;padding:8px 12px;">
				<legend><?php echo esc_html( sprintf( /* translators: %d: numero FAQ. */ __( 'FAQ %d', 'farmacia-costa-core' ), $i + 1 ) ); ?></legend>
				<p>
					<label for="fcc-faq-q-<?php echo (int) $i; ?>"><?php esc_html_e( 'Domanda', 'farmacia-costa-core' ); ?></label><br>
					<input type="text" class="large-text" id="fcc-faq-q-<?php echo (int) $i; ?>" name="fcc_faq_q[]" value="<?php echo esc_attr( $faqs[ $i ]['q'] ?? '' ); ?>">
				</p>
				<p>
					<label for="fcc-faq-a-<?php echo (int) $i; ?>"><?php esc_html_e( 'Risposta', 'farmacia-costa-core' ); ?></label><br>
					<textarea class="large-text" rows="2" id="fcc-faq-a-<?php echo (int) $i; ?>" name="fcc_faq_a[]"><?php echo esc_textarea( $faqs[ $i ]['a'] ?? '' ); ?></textarea>
				</p>
			</fieldset>
		<?php endfor; ?>
		<?php
	}

	/**
	 * Meta box team.
	 *
	 * @param WP_Post $post Post corrente.
	 */
	public static function render_team_box( $post ) {
		wp_nonce_field( 'fcc_team_meta', 'fcc_team_nonce' );
		$role  = get_post_meta( $post->ID, '_fcc_role', true );
		$area  = get_post_meta( $post->ID, '_fcc_area', true );
		$quote = get_post_meta( $post->ID, '_fcc_quote', true );
		?>
		<p>
			<label for="fcc-role"><strong><?php esc_html_e( 'Ruolo', 'farmacia-costa-core' ); ?></strong></label><br>
			<input type="text" id="fcc-role" name="fcc_role" class="regular-text" value="<?php echo esc_attr( $role ); ?>" placeholder="<?php esc_attr_e( 'Es. Farmacista titolare', 'farmacia-costa-core' ); ?>">
		</p>
		<p>
			<label for="fcc-area"><strong><?php esc_html_e( 'Area di competenza (solo se verificata)', 'farmacia-costa-core' ); ?></strong></label><br>
			<input type="text" id="fcc-area" name="fcc_area" class="regular-text" value="<?php echo esc_attr( $area ); ?>">
		</p>
		<p>
			<label for="fcc-quote"><strong><?php esc_html_e( 'Breve frase professionale (approvata dalla persona)', 'farmacia-costa-core' ); ?></strong></label><br>
			<textarea id="fcc-quote" name="fcc_quote" rows="2" class="large-text"><?php echo esc_textarea( $quote ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Salva i meta del servizio.
	 *
	 * @param int $post_id ID del post.
	 */
	public static function save_service_meta( $post_id ) {
		if ( ! isset( $_POST['fcc_service_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fcc_service_nonce'] ), 'fcc_service_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		update_post_meta( $post_id, '_fcc_short', sanitize_textarea_field( wp_unslash( $_POST['fcc_short'] ?? '' ) ) );

		$booking = sanitize_key( wp_unslash( $_POST['fcc_booking'] ?? 'none' ) );
		if ( ! in_array( $booking, array( 'none', 'recommended', 'required' ), true ) ) {
			$booking = 'none';
		}
		update_post_meta( $post_id, '_fcc_booking', $booking );

		update_post_meta( $post_id, '_fcc_duration', sanitize_text_field( wp_unslash( $_POST['fcc_duration'] ?? '' ) ) );
		update_post_meta( $post_id, '_fcc_price', sanitize_text_field( wp_unslash( $_POST['fcc_price'] ?? '' ) ) );
		update_post_meta( $post_id, '_fcc_cta_label', sanitize_text_field( wp_unslash( $_POST['fcc_cta_label'] ?? '' ) ) );
		update_post_meta( $post_id, '_fcc_active', empty( $_POST['fcc_active'] ) ? '0' : '1' );
		update_post_meta( $post_id, '_fcc_featured', empty( $_POST['fcc_featured'] ) ? '0' : '1' );

		// FAQ: coppie domanda/risposta non vuote, salvate come JSON.
		$questions = array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['fcc_faq_q'] ?? array() ) );
		$answers   = array_map( 'sanitize_textarea_field', (array) wp_unslash( $_POST['fcc_faq_a'] ?? array() ) );
		$faqs      = array();
		foreach ( $questions as $i => $question ) {
			$answer = $answers[ $i ] ?? '';
			if ( '' !== trim( $question ) && '' !== trim( $answer ) ) {
				$faqs[] = array(
					'q' => trim( $question ),
					'a' => trim( $answer ),
				);
			}
		}
		update_post_meta( $post_id, '_fcc_faqs', wp_json_encode( $faqs, JSON_UNESCAPED_UNICODE ) );
	}

	/**
	 * Salva i meta del team.
	 *
	 * @param int $post_id ID del post.
	 */
	public static function save_team_meta( $post_id ) {
		if ( ! isset( $_POST['fcc_team_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fcc_team_nonce'] ), 'fcc_team_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, '_fcc_role', sanitize_text_field( wp_unslash( $_POST['fcc_role'] ?? '' ) ) );
		update_post_meta( $post_id, '_fcc_area', sanitize_text_field( wp_unslash( $_POST['fcc_area'] ?? '' ) ) );
		update_post_meta( $post_id, '_fcc_quote', sanitize_textarea_field( wp_unslash( $_POST['fcc_quote'] ?? '' ) ) );
	}

	/**
	 * FAQ di un servizio come array.
	 *
	 * @param int $post_id ID del servizio.
	 * @return array<int,array{q:string,a:string}>
	 */
	public static function get_faqs( $post_id ) {
		$faqs = json_decode( (string) get_post_meta( $post_id, '_fcc_faqs', true ), true );
		return is_array( $faqs ) ? $faqs : array();
	}
}

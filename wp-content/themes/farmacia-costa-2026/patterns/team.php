<?php
/**
 * Title: Il team
 * Slug: farmacia-costa/team
 * Categories: farmacia-costa
 * Inserter: no
 *
 * Mostra solo i membri reali inseriti nel CPT Team dal personale della
 * farmacia. Nessuna biografia inventata: i campi vuoti non vengono mostrati.
 *
 * @package FarmaciaCosta2026
 */

$fc_team = get_posts(
	array(
		'post_type'      => 'fc_team',
		'posts_per_page' => 24,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	)
);

if ( ! $fc_team ) {
	return;
}
?>
<!-- wp:html -->
<section class="fc-section fc-section--white" aria-labelledby="fc-team-title">
	<div class="fc-container fc-container--wide">
		<div class="fc-section__head fc-reveal">
			<p class="fc-kicker"><?php esc_html_e( 'Le persone', 'farmacia-costa-2026' ); ?></p>
			<h2 id="fc-team-title"><?php esc_html_e( 'Il team della Farmacia Costa', 'farmacia-costa-2026' ); ?></h2>
			<p><?php esc_html_e( 'Farmacisti che conosci per nome e che ti conoscono: al banco ogni giorno per consigliarti con competenza.', 'farmacia-costa-2026' ); ?></p>
		</div>
		<div class="fc-team">
			<?php foreach ( $fc_team as $fc_member ) : ?>
				<?php
				$fc_role  = (string) get_post_meta( $fc_member->ID, '_fcc_role', true );
				$fc_area  = (string) get_post_meta( $fc_member->ID, '_fcc_area', true );
				$fc_quote = (string) get_post_meta( $fc_member->ID, '_fcc_quote', true );
				?>
				<article class="fc-reveal">
					<figure>
						<?php if ( has_post_thumbnail( $fc_member ) ) : ?>
							<?php
							echo get_the_post_thumbnail(
								$fc_member,
								'medium_large',
								array(
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							);
							?>
						<?php else : ?>
							<!-- PLACEHOLDER: caricare la fotografia reale della persona come immagine in evidenza. -->
							<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/placeholder-persona.svg' ) ); ?>"
								alt="" width="600" height="600" loading="lazy" decoding="async">
						<?php endif; ?>
					</figure>
					<div class="fc-team__body">
						<h3><?php echo esc_html( get_the_title( $fc_member ) ); ?></h3>
						<?php if ( $fc_role ) : ?>
							<p class="fc-team__role"><?php echo esc_html( $fc_role ); ?></p>
						<?php endif; ?>
						<?php if ( $fc_area || $fc_quote ) : ?>
							<details>
								<summary><?php esc_html_e( 'Scopri di più', 'farmacia-costa-2026' ); ?></summary>
								<?php if ( $fc_area ) : ?>
									<p><strong><?php esc_html_e( 'Si occupa di:', 'farmacia-costa-2026' ); ?></strong> <?php echo esc_html( $fc_area ); ?></p>
								<?php endif; ?>
								<?php if ( $fc_quote ) : ?>
									<p>«<?php echo esc_html( $fc_quote ); ?>»</p>
								<?php endif; ?>
							</details>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<!-- /wp:html -->

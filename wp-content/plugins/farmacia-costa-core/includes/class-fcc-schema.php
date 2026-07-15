<?php
/**
 * Dati strutturati JSON-LD centralizzati.
 *
 * Un solo punto di emissione per evitare markup duplicato tra tema e plugin.
 * Vengono pubblicati esclusivamente dati verificati presenti nelle impostazioni:
 * gli orari entrano nello schema solo se il titolare li ha confermati,
 * le coordinate solo se compilate. Nessun markup Review/AggregateRating.
 *
 * @package FarmaciaCostaCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Emissione JSON-LD.
 */
class FCC_Schema {

	/**
	 * Aggancia gli hook.
	 */
	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'output' ), 5 );
	}

	/**
	 * Mappa giorni interni → schema.org.
	 *
	 * @return array<string,string>
	 */
	private static function day_map() {
		return array(
			'mon' => 'Monday',
			'tue' => 'Tuesday',
			'wed' => 'Wednesday',
			'thu' => 'Thursday',
			'fri' => 'Friday',
			'sat' => 'Saturday',
			'sun' => 'Sunday',
		);
	}

	/**
	 * Nodo Pharmacy con soli dati verificati.
	 *
	 * @return array<string,mixed>
	 */
	private static function pharmacy_node() {
		$node = array(
			'@type'      => 'Pharmacy',
			'@id'        => home_url( '/#pharmacy' ),
			'name'       => fcc_get( 'business_name' ),
			'url'        => home_url( '/' ),
			'telephone'  => fcc_get( 'phone_e164' ),
			'email'      => fcc_get( 'email' ),
			'address'    => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => fcc_get( 'address' ),
				'postalCode'      => fcc_get( 'cap' ),
				'addressLocality' => fcc_get( 'city' ),
				'addressRegion'   => fcc_get( 'province' ),
				'addressCountry'  => 'IT',
			),
			'areaServed' => array(
				'@type' => 'City',
				'name'  => fcc_get( 'city' ),
			),
		);

		$same_as = array_filter( array( fcc_get( 'facebook' ), fcc_get( 'instagram' ) ) );
		if ( $same_as ) {
			$node['sameAs'] = array_values( $same_as );
		}

		if ( fcc_get( 'lat' ) && fcc_get( 'lng' ) ) {
			$node['geo'] = array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => (float) fcc_get( 'lat' ),
				'longitude' => (float) fcc_get( 'lng' ),
			);
		}

		if ( fcc_hours_verified() ) {
			$specs = array();
			$map   = self::day_map();
			foreach ( (array) fcc_get( 'hours' ) as $day => $ranges ) {
				foreach ( array_filter( (array) $ranges ) as $range ) {
					list( $opens, $closes ) = array_pad( explode( '-', $range, 2 ), 2, '' );
					if ( $opens && $closes && isset( $map[ $day ] ) ) {
						$specs[] = array(
							'@type'     => 'OpeningHoursSpecification',
							'dayOfWeek' => $map[ $day ],
							'opens'     => trim( $opens ),
							'closes'    => trim( $closes ),
						);
					}
				}
			}
			if ( $specs ) {
				$node['openingHoursSpecification'] = $specs;
			}
		}

		$logo_id = get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			$logo = wp_get_attachment_image_url( $logo_id, 'full' );
			if ( $logo ) {
				$node['logo']  = $logo;
				$node['image'] = $logo;
			}
		}

		return $node;
	}

	/**
	 * Breadcrumb della richiesta corrente.
	 *
	 * @return array<string,mixed>|null
	 */
	private static function breadcrumb_node() {
		if ( is_front_page() ) {
			return null;
		}
		$items   = array();
		$items[] = array(
			'name' => __( 'Home', 'farmacia-costa-core' ),
			'url'  => home_url( '/' ),
		);

		if ( is_singular( 'fc_servizio' ) ) {
			$items[] = array(
				'name' => __( 'Servizi', 'farmacia-costa-core' ),
				'url'  => get_post_type_archive_link( 'fc_servizio' ),
			);
			$items[] = array(
				'name' => get_the_title(),
				'url'  => get_permalink(),
			);
		} elseif ( is_post_type_archive( 'fc_servizio' ) ) {
			$items[] = array(
				'name' => __( 'Servizi', 'farmacia-costa-core' ),
				'url'  => get_post_type_archive_link( 'fc_servizio' ),
			);
		} elseif ( is_tax( 'fc_categoria' ) ) {
			$items[] = array(
				'name' => __( 'Servizi', 'farmacia-costa-core' ),
				'url'  => get_post_type_archive_link( 'fc_servizio' ),
			);
			$items[] = array(
				'name' => single_term_title( '', false ),
				'url'  => get_term_link( get_queried_object() ),
			);
		} elseif ( is_singular() ) {
			$items[] = array(
				'name' => get_the_title(),
				'url'  => get_permalink(),
			);
		} else {
			return null;
		}

		$list = array();
		foreach ( $items as $i => $item ) {
			$list[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => $item['name'],
				'item'     => $item['url'],
			);
		}

		return array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $list,
		);
	}

	/**
	 * Emissione nel <head>.
	 */
	public static function output() {
		/**
		 * Consente di disattivare l'emissione (es. se un plugin SEO gestisce già lo schema).
		 *
		 * @param bool $enabled Attivo per impostazione predefinita.
		 */
		if ( ! apply_filters( 'fcc_schema_enabled', true ) ) {
			return;
		}

		$graph   = array();
		$graph[] = self::pharmacy_node();
		$graph[] = array(
			'@type'     => 'WebSite',
			'@id'       => home_url( '/#website' ),
			'url'       => home_url( '/' ),
			'name'      => get_bloginfo( 'name' ),
			'publisher' => array( '@id' => home_url( '/#pharmacy' ) ),
			'inLanguage' => get_bloginfo( 'language' ),
		);

		$breadcrumb = self::breadcrumb_node();
		if ( $breadcrumb ) {
			$graph[] = $breadcrumb;
		}

		if ( is_singular( 'fc_servizio' ) ) {
			$service = array(
				'@type'       => 'Service',
				'name'        => get_the_title(),
				'url'         => get_permalink(),
				'description' => wp_strip_all_tags( (string) get_post_meta( get_the_ID(), '_fcc_short', true ) ),
				'provider'    => array( '@id' => home_url( '/#pharmacy' ) ),
				'areaServed'  => array(
					'@type' => 'City',
					'name'  => fcc_get( 'city' ),
				),
			);
			$graph[] = $service;

			// FAQPage solo se le FAQ sono realmente visibili nella pagina.
			$faqs = FCC_CPT::get_faqs( get_the_ID() );
			if ( $faqs ) {
				$entities = array();
				foreach ( $faqs as $faq ) {
					$entities[] = array(
						'@type'          => 'Question',
						'name'           => $faq['q'],
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => $faq['a'],
						),
					);
				}
				$graph[] = array(
					'@type'      => 'FAQPage',
					'mainEntity' => $entities,
				);
			}
		}

		$payload = array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}

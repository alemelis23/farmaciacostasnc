<?php
/**
 * Plugin Name:       Farmacia Costa Core
 * Plugin URI:        https://www.farmaciacostacarbonia.com/
 * Description:       Dati aziendali centralizzati, servizi, team, form di contatto sicuro, dati strutturati e redirect per Farmacia Costa Carbonia. Indipendente dal tema attivo.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      8.0
 * Author:            Farmacia Costa
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       farmacia-costa-core
 *
 * @package FarmaciaCostaCore
 */

defined( 'ABSPATH' ) || exit;

define( 'FCC_VERSION', '1.0.0' );
define( 'FCC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FCC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once FCC_PLUGIN_DIR . 'includes/helpers.php';
require_once FCC_PLUGIN_DIR . 'includes/class-fcc-settings.php';
require_once FCC_PLUGIN_DIR . 'includes/class-fcc-cpt.php';
require_once FCC_PLUGIN_DIR . 'includes/class-fcc-form.php';
require_once FCC_PLUGIN_DIR . 'includes/class-fcc-schema.php';
require_once FCC_PLUGIN_DIR . 'includes/class-fcc-seo.php';
require_once FCC_PLUGIN_DIR . 'includes/class-fcc-redirects.php';

FCC_Settings::init();
FCC_CPT::init();
FCC_Form::init();
FCC_Schema::init();
FCC_SEO::init();
FCC_Redirects::init();

/**
 * Attivazione: registra CPT, crea le categorie di servizio e rigenera i permalink.
 */
function fcc_activate() {
	FCC_CPT::register();
	FCC_CPT::seed_terms();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'fcc_activate' );

/**
 * Disattivazione: rigenera i permalink.
 */
function fcc_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'fcc_deactivate' );

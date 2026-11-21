<?php
/**
 * HSV De Paardenvriend – thema.
 * Uiterlijk + sponsoren. Agenda, wedstrijden, startlijsten en uitslagen komen uit de plugin "HSV Wedstrijden".
 *
 * @package paardenvriend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PV_VERSION', '2.2.0' );
define( 'PV_URI', get_template_directory_uri() );

require_once get_template_directory() . '/inc/sponsoren.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/nav-walker.php';

/* ------------------------------------------------------------------
 * Basis
 * ---------------------------------------------------------------- */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 180, 'width' => 330, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'assets/fonts.css', 'editor-style.css' ) );
	add_theme_support( 'editor-color-palette', array(
		array( 'name' => 'Groen', 'slug' => 'groen', 'color' => '#4d6824' ),
		array( 'name' => 'Lime', 'slug' => 'lime', 'color' => '#a0ce4e' ),
		array( 'name' => 'Salie', 'slug' => 'salie', 'color' => '#b2bf79' ),
		array( 'name' => 'Taupe', 'slug' => 'taupe', 'color' => '#9b9074' ),
		array( 'name' => 'Donker', 'slug' => 'donker', 'color' => '#333333' ),
		array( 'name' => 'Lichtgrijs', 'slug' => 'lichtgrijs', 'color' => '#f6f6f6' ),
	) );
	register_nav_menus( array(
		'primary' => 'Hoofdmenu',
		'footer'  => 'Footermenu',
	) );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'pv-fonts', PV_URI . '/assets/fonts.css', array(), PV_VERSION );
	wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3' );
	wp_enqueue_style( 'paardenvriend', get_stylesheet_uri(), array( 'bootstrap', 'pv-fonts' ), PV_VERSION );
	wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), '5.3.3', array( 'strategy' => 'defer', 'in_footer' => true ) );
} );

/* Lettertypes vooraf laden (staan lokaal: geen Google Fonts, dus AVG-vriendelijk). */
add_action( 'wp_head', function () {
	foreach ( array( 'lora-latin-700-normal', 'pt-sans-latin-400-normal' ) as $f ) {
		printf( '<link rel="preload" href="%s/assets/fonts/%s.woff2" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( PV_URI ), esc_attr( $f ) );
	}
}, 1 );

/* ------------------------------------------------------------------
 * Koppeling met de plugin HSV Wedstrijden
 * ---------------------------------------------------------------- */
function pv_has_plugin() {
	return function_exists( 'hsv_get_wedstrijden' );
}

/* Standaardlocatie in agenda en iCal = adres van de vereniging. */
add_filter( 'hsv_default_location', function ( $loc ) {
	$a = trim( preg_replace( '/\s+/', ' ', (string) get_theme_mod( 'pv_address', "Bergerhofweg 2\n5953 NR Reuver" ) ) );
	return $a ? $a : $loc;
} );

add_action( 'admin_notices', function () {
	if ( ! pv_has_plugin() && current_user_can( 'activate_plugins' ) ) {
		echo '<div class="notice notice-warning"><p><strong>HSV De Paardenvriend-thema:</strong> activeer de plugin <em>HSV Wedstrijden</em> voor de agenda, startlijsten en uitslagen.</p></div>';
	}
} );

/* ------------------------------------------------------------------
 * Contact-helpers
 * ---------------------------------------------------------------- */
function pv_contact() {
	return array(
		'address'  => (string) get_theme_mod( 'pv_address', "Bergerhofweg 2\n5953 NR Reuver" ),
		'phone'    => (string) get_theme_mod( 'pv_phone', '077 - 474 46 78' ),
		'email'    => (string) get_theme_mod( 'pv_email', 'info@hsvdepaardenvriend.nl' ),
		'facebook' => (string) get_theme_mod( 'pv_facebook', '' ),
		'kvk'      => (string) get_theme_mod( 'pv_kvk', '' ),
	);
}

function pv_tel_href( $phone ) {
	$digits = preg_replace( '/[^\d+]/', '', $phone );
	return 'tel:' . ( 0 === strpos( $digits, '0' ) ? '+31' . substr( $digits, 1 ) : $digits );
}

add_shortcode( 'contactgegevens', function () {
	$c   = pv_contact();
	$out = '<ul class="pv-contact">';
	if ( $c['address'] ) {
		$out .= '<li>' . nl2br( esc_html( $c['address'] ) ) . '</li>';
	}
	if ( $c['phone'] ) {
		$out .= '<li><a href="' . esc_attr( pv_tel_href( $c['phone'] ) ) . '">' . esc_html( $c['phone'] ) . '</a></li>';
	}
	if ( $c['email'] ) {
		$out .= '<li><a href="mailto:' . esc_attr( antispambot( $c['email'] ) ) . '">' . esc_html( antispambot( $c['email'] ) ) . '</a></li>';
	}
	if ( $c['facebook'] ) {
		$out .= '<li><a href="' . esc_url( $c['facebook'] ) . '" target="_blank" rel="noopener">Volg ons op Facebook</a></li>';
	}
	return $out . '</ul>';
} );

/* ------------------------------------------------------------------
 * Blokstijlen in de editor
 * ---------------------------------------------------------------- */
add_action( 'init', function () {
	foreach ( array( 'core/group', 'core/paragraph', 'core/quote' ) as $block ) {
		register_block_style( $block, array( 'name' => 'kader', 'label' => 'Grijs kader met groene lijn' ) );
	}
} );

add_filter( 'excerpt_length', function () {
	return 28;
} );
add_filter( 'excerpt_more', function () {
	return '…';
} );

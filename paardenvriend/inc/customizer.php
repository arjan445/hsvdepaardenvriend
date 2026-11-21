<?php
/**
 * Customizer: verenigingsgegevens en homepage.
 *
 * @package paardenvriend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pv_default_signup_html() {
	return "<strong>Springen</strong> (oefen én KNHS) via <a href=\"https://www.equicompetition.nl\">Equicompetition</a>\n<strong>Oefendressuur</strong> via mail aan Cherida\n<strong>KNHS-dressuur</strong> via <a href=\"https://mijn.knhs.nl\">MijnKNHS</a>\n<strong>Trainen</strong> in de binnen- of buitenbaan via app aan Angelique";
}

add_action( 'customize_register', function ( $wp_customize ) {
	/* Verenigingsgegevens */
	$wp_customize->add_section( 'pv_contact', array( 'title' => 'Verenigingsgegevens', 'priority' => 30 ) );
	$contact = array(
		'pv_address'  => array( 'Adres', 'textarea', 'sanitize_textarea_field', "Bergerhofweg 2\n5953 NR Reuver" ),
		'pv_phone'    => array( 'Telefoon', 'text', 'sanitize_text_field', '077 - 474 46 78' ),
		'pv_email'    => array( 'E-mail', 'email', 'sanitize_email', 'info@hsvdepaardenvriend.nl' ),
		'pv_facebook' => array( 'Facebook-pagina (URL)', 'url', 'esc_url_raw', '' ),
		'pv_kvk'      => array( 'KVK-nummer', 'text', 'sanitize_text_field', '' ),
	);
	foreach ( $contact as $id => $f ) {
		$wp_customize->add_setting( $id, array( 'default' => $f[3], 'sanitize_callback' => $f[2] ) );
		$wp_customize->add_control( $id, array( 'label' => $f[0], 'section' => 'pv_contact', 'type' => $f[1] ) );
	}

	/* Homepage */
	$wp_customize->add_section( 'pv_home', array( 'title' => 'Homepage', 'priority' => 25 ) );
	$wp_customize->add_setting( 'pv_notice', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'pv_notice', array( 'label' => 'Mededelingsbalk bovenaan (leeg = verborgen)', 'section' => 'pv_home', 'type' => 'text' ) );
	$wp_customize->add_setting( 'pv_notice_url', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'pv_notice_url', array( 'label' => 'Link bij mededeling', 'section' => 'pv_home', 'type' => 'url' ) );

	$wp_customize->add_setting( 'pv_hero_image', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'pv_hero_image', array(
		'label'       => 'Kopfoto',
		'description' => 'Liggende foto, minimaal 1920 px breed.',
		'section'     => 'pv_home',
		'mime_type'   => 'image',
	) ) );
	$home = array(
		'pv_hero_kicker' => array( 'Regel boven de kop', 'text', 'sanitize_text_field', 'Hippische sportvereniging · Reuver' ),
		'pv_hero_title'  => array( 'Kop', 'text', 'sanitize_text_field', 'Indoorwedstrijden 2026' ),
		'pv_hero_text'   => array( 'Tekst onder de kop', 'textarea', 'sanitize_textarea_field', "Springen en dressuur voor paarden en pony's op onze accommodatie aan de Bergerhofweg." ),
		'pv_signup'      => array( 'Blok "Inschrijven" (één regel per punt, links en <strong> toegestaan)', 'textarea', 'wp_kses_post', pv_default_signup_html() ),
	);
	foreach ( $home as $id => $f ) {
		$wp_customize->add_setting( $id, array( 'default' => $f[3], 'sanitize_callback' => $f[2] ) );
		$wp_customize->add_control( $id, array( 'label' => $f[0], 'section' => 'pv_home', 'type' => $f[1] ) );
	}
	$wp_customize->add_setting( 'pv_season_image', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'pv_season_image', array(
		'label'       => 'Seizoenskalender (afbeelding)',
		'description' => 'Optioneel. Wordt op de homepage onder de wedstrijden getoond.',
		'section'     => 'pv_home',
		'mime_type'   => 'image',
	) ) );
	$wp_customize->add_setting( 'pv_sponsor_count', array( 'default' => 6, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'pv_sponsor_count', array(
		'label'       => 'Aantal sponsorlogo\'s in de sponsorbalk',
		'description' => 'Bij elk bezoek een willekeurige selectie; hoofd- en stersponsoren komen vaker voor (gewicht per categorie).',
		'section'     => 'pv_home',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 1, 'max' => 24 ),
	) );
	$wp_customize->add_setting( 'pv_home_sponsors', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'pv_home_sponsors', array(
		'label'       => 'Alleen deze sponsorcategorieën in de balk (optioneel)',
		'description' => 'Slugs, met komma\'s, bijv. hoofdsponsor,stersponsor. Leeg = alle sponsoren.',
		'section'     => 'pv_home',
		'type'        => 'text',
	) );
} );

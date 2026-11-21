<?php
/**
 * Sponsoren: logo (uitgelichte afbeelding) dat linkt naar de website, ingedeeld per categorie.
 * Categorieën: Hoofdsponsor, Stersponsor, Sponsor (uit te breiden). Volgorde per categorie instelbaar.
 *
 * Tonen: [sponsoren] (alles, per categorie) of [sponsoren categorie="hoofdsponsor"].
 * De pagina met slug "sponsoren" toont automatisch alle sponsoren onder de pagina-tekst.
 *
 * @package paardenvriend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {
	register_post_type( 'pv_sponsor', array(
		'labels'          => array(
			'name'                  => 'Sponsoren',
			'singular_name'         => 'Sponsor',
			'add_new'               => 'Nieuwe sponsor',
			'add_new_item'          => 'Nieuwe sponsor toevoegen',
			'edit_item'             => 'Sponsor bewerken',
			'search_items'          => 'Sponsoren zoeken',
			'not_found'             => 'Geen sponsoren gevonden',
			'not_found_in_trash'    => 'Geen sponsoren in de prullenbak',
			'all_items'             => 'Alle sponsoren',
			'menu_name'             => 'Sponsoren',
			'featured_image'        => 'Logo',
			'set_featured_image'    => 'Logo instellen',
			'remove_featured_image' => 'Logo verwijderen',
			'use_featured_image'    => 'Als logo gebruiken',
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_rest'    => true,
		'menu_icon'       => 'dashicons-heart',
		'menu_position'   => 6,
		'supports'        => array( 'title', 'thumbnail', 'page-attributes' ),
	) );

	register_taxonomy( 'pv_sponsorcategorie', 'pv_sponsor', array(
		'labels'            => array(
			'name'          => 'Sponsorcategorieën',
			'singular_name' => 'Sponsorcategorie',
			'add_new_item'  => 'Nieuwe categorie toevoegen',
			'edit_item'     => 'Categorie bewerken',
			'menu_name'     => 'Categorieën',
		),
		'hierarchical'      => true,
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
	) );

	register_post_meta( 'pv_sponsor', '_pv_url', array(
		'type'          => 'string',
		'single'        => true,
		'show_in_rest'  => true,
		'auth_callback' => function () {
			return current_user_can( 'edit_posts' );
		},
	) );
	register_term_meta( 'pv_sponsorcategorie', 'pv_volgorde', array( 'type' => 'integer', 'single' => true, 'show_in_rest' => true ) );
	register_term_meta( 'pv_sponsorcategorie', 'pv_formaat', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true ) );
} );

/* Standaardcategorieën bij activeren van het thema */
add_action( 'after_switch_theme', 'pv_sponsor_default_terms' );
function pv_sponsor_default_terms() {
	$defaults = array(
		'Hoofdsponsor' => array( 1, 'groot' ),
		'Stersponsor'  => array( 2, 'middel' ),
		'Sponsor'      => array( 3, 'klein' ),
	);
	foreach ( $defaults as $name => $d ) {
		if ( term_exists( $name, 'pv_sponsorcategorie' ) ) {
			continue;
		}
		$t = wp_insert_term( $name, 'pv_sponsorcategorie' );
		if ( ! is_wp_error( $t ) ) {
			update_term_meta( $t['term_id'], 'pv_volgorde', $d[0] );
			update_term_meta( $t['term_id'], 'pv_formaat', $d[1] );
		}
	}
}

/* ------------------------------------------------------------------
 * Beheer
 * ---------------------------------------------------------------- */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'pv_sponsor_url', 'Website van de sponsor', function ( $post ) {
		wp_nonce_field( 'pv_sponsor', 'pv_sponsor_nonce' );
		printf( '<input type="url" name="_pv_url" value="%s" class="widefat" placeholder="https://www.voorbeeld.nl">', esc_attr( get_post_meta( $post->ID, '_pv_url', true ) ) );
		echo '<p class="description">Het logo stel je in bij "Logo" (rechts). Kies ook een categorie. De volgorde binnen een categorie regel je met "Volgorde" bij Pagina-attributen (laag = eerst).</p>';
	}, 'pv_sponsor', 'normal', 'high' );
} );

add_action( 'save_post_pv_sponsor', function ( $post_id ) {
	if ( ! isset( $_POST['pv_sponsor_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pv_sponsor_nonce'] ), 'pv_sponsor' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['_pv_url'] ) ) {
		update_post_meta( $post_id, '_pv_url', esc_url_raw( wp_unslash( $_POST['_pv_url'] ) ) ); // phpcs:ignore
	}
} );

add_filter( 'manage_pv_sponsor_posts_columns', function ( $cols ) {
	return array_slice( $cols, 0, 1, true ) + array( 'pv_logo' => 'Logo' ) + array_slice( $cols, 1, null, true );
} );
add_action( 'manage_pv_sponsor_posts_custom_column', function ( $col, $post_id ) {
	if ( 'pv_logo' === $col ) {
		echo has_post_thumbnail( $post_id ) ? get_the_post_thumbnail( $post_id, array( 80, 50 ), array( 'style' => 'max-width:80px;height:auto' ) ) : '<span style="color:#b32d2e">Geen logo</span>';
	}
}, 10, 2 );

/* Volgorde + formaat per categorie */
add_action( 'pv_sponsorcategorie_add_form_fields', function () {
	echo '<div class="form-field"><label for="pv_volgorde">Volgorde</label><input type="number" id="pv_volgorde" name="pv_volgorde" value="10" min="0"><p>Laag nummer = hoger op de pagina.</p></div>';
	echo '<div class="form-field"><label for="pv_formaat">Grootte logo\'s</label><select id="pv_formaat" name="pv_formaat"><option value="groot">Groot</option><option value="middel">Middel</option><option value="klein" selected>Klein</option></select></div>';
} );
add_action( 'pv_sponsorcategorie_edit_form_fields', function ( $term ) {
	$f = get_term_meta( $term->term_id, 'pv_formaat', true );
	printf( '<tr class="form-field"><th><label for="pv_volgorde">Volgorde</label></th><td><input type="number" id="pv_volgorde" name="pv_volgorde" value="%d" min="0"><p class="description">Laag nummer = hoger op de pagina.</p></td></tr>', (int) get_term_meta( $term->term_id, 'pv_volgorde', true ) );
	echo '<tr class="form-field"><th><label for="pv_formaat">Grootte logo\'s</label></th><td><select id="pv_formaat" name="pv_formaat">';
	foreach ( array( 'groot' => 'Groot', 'middel' => 'Middel', 'klein' => 'Klein' ) as $k => $v ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( $f ? $f : 'klein', $k, false ), esc_html( $v ) );
	}
	echo '</select></td></tr>';
} );
$pv_save_cat = function ( $term_id ) {
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	if ( isset( $_POST['pv_volgorde'] ) ) { // phpcs:ignore
		update_term_meta( $term_id, 'pv_volgorde', absint( $_POST['pv_volgorde'] ) ); // phpcs:ignore
	}
	if ( isset( $_POST['pv_formaat'] ) && in_array( $_POST['pv_formaat'], array( 'groot', 'middel', 'klein' ), true ) ) { // phpcs:ignore
		update_term_meta( $term_id, 'pv_formaat', sanitize_key( $_POST['pv_formaat'] ) ); // phpcs:ignore
	}
};
add_action( 'created_pv_sponsorcategorie', $pv_save_cat );
add_action( 'edited_pv_sponsorcategorie', $pv_save_cat );

/* ------------------------------------------------------------------
 * Ophalen en tonen
 * ---------------------------------------------------------------- */

/**
 * Sponsoren per categorie, op volgorde.
 *
 * @param string|array $categories Slug(s); leeg = alle.
 * @return array[] [ 'term' => WP_Term|null, 'size' => groot|middel|klein, 'sponsors' => WP_Post[] ]
 */
function pv_get_sponsor_groups( $categories = '' ) {
	$terms = get_terms( array( 'taxonomy' => 'pv_sponsorcategorie', 'hide_empty' => true ) );
	$terms = is_wp_error( $terms ) ? array() : $terms;
	$want  = array_filter( array_map( 'sanitize_title', (array) $categories ) );
	if ( $want ) {
		$terms = array_values( array_filter( $terms, function ( $t ) use ( $want ) {
			return in_array( $t->slug, $want, true );
		} ) );
	}
	usort( $terms, function ( $a, $b ) {
		return (int) get_term_meta( $a->term_id, 'pv_volgorde', true ) <=> (int) get_term_meta( $b->term_id, 'pv_volgorde', true );
	} );
	$base   = array(
		'post_type'      => 'pv_sponsor',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'no_found_rows'  => true,
	);
	$groups = array();
	foreach ( $terms as $t ) {
		$posts = get_posts( $base + array( 'tax_query' => array( array( 'taxonomy' => 'pv_sponsorcategorie', 'terms' => $t->term_id, 'include_children' => false ) ) ) );
		if ( $posts ) {
			$size     = get_term_meta( $t->term_id, 'pv_formaat', true );
			$groups[] = array( 'term' => $t, 'size' => $size ? $size : 'klein', 'sponsors' => $posts );
		}
	}
	if ( ! $want ) {
		$loose = get_posts( $base + array( 'tax_query' => array( array( 'taxonomy' => 'pv_sponsorcategorie', 'operator' => 'NOT EXISTS' ) ) ) );
		if ( $loose ) {
			$groups[] = array( 'term' => null, 'size' => 'klein', 'sponsors' => $loose );
		}
	}
	return $groups;
}

function pv_sponsor_logo( $post ) {
	$url  = (string) get_post_meta( $post->ID, '_pv_url', true );
	$name = get_the_title( $post );
	$logo = has_post_thumbnail( $post )
		? get_the_post_thumbnail( $post, 'medium', array( 'alt' => $name, 'loading' => 'lazy' ) )
		: '<span class="pv-sponsor-name">' . esc_html( $name ) . '</span>';
	if ( $url ) {
		return sprintf( '<a class="pv-sponsor" href="%s" target="_blank" rel="noopener sponsored" title="%s">%s</a>', esc_url( $url ), esc_attr( $name ), $logo );
	}
	return '<span class="pv-sponsor">' . $logo . '</span>';
}

/**
 * HTML met sponsorlogo's.
 *
 * @param array $args categorie (slug of komma-lijst), koppen (bool).
 */
function pv_sponsors_html( $args = array() ) {
	$args   = wp_parse_args( $args, array( 'categorie' => '', 'koppen' => true ) );
	$groups = pv_get_sponsor_groups( $args['categorie'] ? array_map( 'trim', explode( ',', $args['categorie'] ) ) : '' );
	if ( ! $groups ) {
		return '';
	}
	$out = '<div class="pv-sponsors">';
	foreach ( $groups as $g ) {
		$out .= '<div class="pv-sponsor-group is-' . esc_attr( $g['size'] ) . '">';
		if ( $args['koppen'] && $g['term'] && count( $groups ) > 1 ) {
			$out .= '<h3 class="pv-sponsor-head">' . esc_html( $g['term']->name ) . '</h3>';
		}
		$out .= '<div class="pv-sponsor-grid">';
		foreach ( $g['sponsors'] as $s ) {
			$out .= pv_sponsor_logo( $s );
		}
		$out .= '</div></div>';
	}
	return $out . '</div>';
}

add_shortcode( 'sponsoren', function ( $atts ) {
	$a = shortcode_atts( array( 'categorie' => '', 'koppen' => 'ja' ), $atts );
	return pv_sponsors_html( array( 'categorie' => $a['categorie'], 'koppen' => 'nee' !== $a['koppen'] ) );
} );

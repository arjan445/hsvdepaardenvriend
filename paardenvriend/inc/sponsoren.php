<?php
/**
 * Sponsoren: logo (uitgelichte afbeelding) dat linkt naar de website, ingedeeld per categorie.
 * Categorieën: Hoofdsponsor, Stersponsor, Sponsor (uit te breiden), elk met een volgorde en een gewicht.
 *
 * Tonen:
 * - [sponsoren]                         alles, per categorie (pagina Sponsoren)
 * - [sponsoren categorie="hoofdsponsor"] één of meer categorieën
 * - [sponsoren aantal="6"]              willekeurige selectie van 6, gewogen per categorie
 *
 * Gewicht: hoe hoger, hoe vaker een sponsor in een willekeurige selectie verschijnt
 * (standaard Hoofdsponsor 5, Stersponsor 3, Sponsor 1). De selectie wordt in de browser
 * opnieuw gemaakt, zodat hij ook met paginacache bij elk bezoek wisselt.
 *
 * Alle logo's staan in een vierkant (1:1) vak.
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
	register_term_meta( 'pv_sponsorcategorie', 'pv_gewicht', array( 'type' => 'integer', 'single' => true, 'show_in_rest' => true ) );
} );

/* Standaardcategorieën bij activeren van het thema */
add_action( 'after_switch_theme', 'pv_sponsor_default_terms' );
function pv_sponsor_default_terms() {
	$defaults = array(
		'Hoofdsponsor' => array( 1, 5 ),
		'Stersponsor'  => array( 2, 3 ),
		'Sponsor'      => array( 3, 1 ),
	);
	foreach ( $defaults as $name => $d ) {
		if ( term_exists( $name, 'pv_sponsorcategorie' ) ) {
			continue;
		}
		$t = wp_insert_term( $name, 'pv_sponsorcategorie' );
		if ( ! is_wp_error( $t ) ) {
			update_term_meta( $t['term_id'], 'pv_volgorde', $d[0] );
			update_term_meta( $t['term_id'], 'pv_gewicht', $d[1] );
		}
	}
}

/** Gewicht van een categorie (1–10). Zonder ingestelde waarde: op naam, anders 1. */
function pv_sponsor_weight( $term ) {
	if ( ! $term ) {
		return 1;
	}
	$w = get_term_meta( $term->term_id, 'pv_gewicht', true );
	if ( '' !== $w && null !== $w ) {
		return max( 1, min( 10, (int) $w ) );
	}
	$defaults = array( 'hoofdsponsor' => 5, 'stersponsor' => 3 );
	return isset( $defaults[ $term->slug ] ) ? $defaults[ $term->slug ] : 1;
}

/* ------------------------------------------------------------------
 * Beheer
 * ---------------------------------------------------------------- */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'pv_sponsor_url', 'Website van de sponsor', function ( $post ) {
		wp_nonce_field( 'pv_sponsor', 'pv_sponsor_nonce' );
		printf( '<input type="url" name="_pv_url" value="%s" class="widefat" placeholder="https://www.voorbeeld.nl">', esc_attr( get_post_meta( $post->ID, '_pv_url', true ) ) );
		echo '<p class="description">Het logo stel je in bij "Logo" (rechts); het wordt in een vierkant vak getoond, dus een vierkant of liggend logo met weinig witruimte werkt het best. Kies ook een categorie: die bepaalt hoe vaak de sponsor in beeld komt.</p>';
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
		echo has_post_thumbnail( $post_id ) ? get_the_post_thumbnail( $post_id, array( 60, 60 ), array( 'style' => 'width:60px;height:60px;object-fit:contain;background:#fff;border:1px solid #ddd' ) ) : '<span style="color:#b32d2e">Geen logo</span>';
	}
}, 10, 2 );

/* Volgorde + gewicht per categorie */
add_filter( 'manage_edit-pv_sponsorcategorie_columns', function ( $cols ) {
	$cols['pv_gewicht'] = 'Gewicht';
	return $cols;
} );
add_filter( 'manage_pv_sponsorcategorie_custom_column', function ( $out, $col, $term_id ) {
	return 'pv_gewicht' === $col ? (string) pv_sponsor_weight( get_term( $term_id ) ) . '×' : $out;
}, 10, 3 );

add_action( 'pv_sponsorcategorie_add_form_fields', function () {
	echo '<div class="form-field"><label for="pv_volgorde">Volgorde</label><input type="number" id="pv_volgorde" name="pv_volgorde" value="10" min="0"><p>Volgorde op de pagina Sponsoren (laag = bovenaan).</p></div>';
	echo '<div class="form-field"><label for="pv_gewicht">Gewicht</label><input type="number" id="pv_gewicht" name="pv_gewicht" value="1" min="1" max="10"><p>Hoe vaak sponsoren uit deze categorie in de sponsorbalk verschijnen, vergeleken met gewicht 1. Bijvoorbeeld 5 = vijf keer zo vaak.</p></div>';
} );
add_action( 'pv_sponsorcategorie_edit_form_fields', function ( $term ) {
	printf( '<tr class="form-field"><th><label for="pv_volgorde">Volgorde</label></th><td><input type="number" id="pv_volgorde" name="pv_volgorde" value="%d" min="0"><p class="description">Volgorde op de pagina Sponsoren (laag = bovenaan).</p></td></tr>', (int) get_term_meta( $term->term_id, 'pv_volgorde', true ) );
	printf( '<tr class="form-field"><th><label for="pv_gewicht">Gewicht</label></th><td><input type="number" id="pv_gewicht" name="pv_gewicht" value="%d" min="1" max="10"><p class="description">Hoe vaak sponsoren uit deze categorie in de sponsorbalk verschijnen, vergeleken met gewicht 1. Bijvoorbeeld 5 = vijf keer zo vaak.</p></td></tr>', (int) pv_sponsor_weight( $term ) );
} );
$pv_save_cat = function ( $term_id ) {
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	if ( isset( $_POST['pv_volgorde'] ) ) { // phpcs:ignore
		update_term_meta( $term_id, 'pv_volgorde', absint( $_POST['pv_volgorde'] ) ); // phpcs:ignore
	}
	if ( isset( $_POST['pv_gewicht'] ) ) { // phpcs:ignore
		update_term_meta( $term_id, 'pv_gewicht', max( 1, min( 10, absint( $_POST['pv_gewicht'] ) ) ) ); // phpcs:ignore
	}
};
add_action( 'created_pv_sponsorcategorie', $pv_save_cat );
add_action( 'edited_pv_sponsorcategorie', $pv_save_cat );

/* ------------------------------------------------------------------
 * Ophalen
 * ---------------------------------------------------------------- */

/**
 * Sponsoren per categorie, op volgorde.
 *
 * @param string|array $categories Slug(s); leeg = alle.
 * @return array[] [ 'term' => WP_Term|null, 'weight' => int, 'sponsors' => WP_Post[] ]
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
	$seen   = array();
	foreach ( $terms as $t ) {
		$posts = get_posts( $base + array( 'tax_query' => array( array( 'taxonomy' => 'pv_sponsorcategorie', 'terms' => $t->term_id, 'include_children' => false ) ) ) );
		$posts = array_values( array_filter( $posts, function ( $p ) use ( &$seen ) {
			if ( isset( $seen[ $p->ID ] ) ) {
				return false; // Sponsor in meerdere categorieën: alleen bij de eerste.
			}
			$seen[ $p->ID ] = true;
			return true;
		} ) );
		if ( $posts ) {
			$groups[] = array( 'term' => $t, 'weight' => pv_sponsor_weight( $t ), 'sponsors' => $posts );
		}
	}
	if ( ! $want ) {
		$loose = get_posts( $base + array( 'tax_query' => array( array( 'taxonomy' => 'pv_sponsorcategorie', 'operator' => 'NOT EXISTS' ) ) ) );
		if ( $loose ) {
			$groups[] = array( 'term' => null, 'weight' => 1, 'sponsors' => $loose );
		}
	}
	return $groups;
}

/**
 * Gewogen willekeurige volgorde (Efraimidis–Spirakis): sleutel = r^(1/gewicht), hoogste eerst.
 *
 * @param array $items [ [ 'weight' => int, ... ], ... ]
 */
function pv_weighted_shuffle( $items ) {
	foreach ( $items as &$i ) {
		$r        = wp_rand( 1, PHP_INT_MAX ) / PHP_INT_MAX;
		$i['key'] = pow( $r, 1 / max( 1, $i['weight'] ) );
	}
	unset( $i );
	usort( $items, function ( $a, $b ) {
		return $b['key'] <=> $a['key'];
	} );
	return $items;
}

/* ------------------------------------------------------------------
 * Tonen
 * ---------------------------------------------------------------- */
function pv_sponsor_logo( $post, $extra = '' ) {
	$url  = (string) get_post_meta( $post->ID, '_pv_url', true );
	$name = get_the_title( $post );
	$logo = has_post_thumbnail( $post )
		? get_the_post_thumbnail( $post, 'medium', array( 'alt' => $name, 'loading' => 'lazy' ) )
		: '<span class="pv-sponsor-name">' . esc_html( $name ) . '</span>';
	if ( $url ) {
		return sprintf( '<a class="pv-sponsor" href="%s" target="_blank" rel="noopener sponsored" title="%s"%s>%s</a>', esc_url( $url ), esc_attr( $name ), $extra, $logo );
	}
	return '<span class="pv-sponsor" title="' . esc_attr( $name ) . '"' . $extra . '>' . $logo . '</span>';
}

/**
 * HTML met sponsorlogo's.
 *
 * @param array $args categorie (slug of komma-lijst), koppen (bool), aantal (int: >0 = willekeurige selectie).
 */
function pv_sponsors_html( $args = array() ) {
	$args   = wp_parse_args( $args, array( 'categorie' => '', 'koppen' => true, 'aantal' => 0 ) );
	$groups = pv_get_sponsor_groups( $args['categorie'] ? array_map( 'trim', explode( ',', $args['categorie'] ) ) : '' );
	if ( ! $groups ) {
		return '';
	}

	/* Willekeurige, gewogen selectie */
	if ( (int) $args['aantal'] > 0 ) {
		$items = array();
		foreach ( $groups as $g ) {
			foreach ( $g['sponsors'] as $s ) {
				$items[] = array( 'post' => $s, 'weight' => $g['weight'] );
			}
		}
		$count = min( (int) $args['aantal'], count( $items ) );
		$items = pv_weighted_shuffle( $items );
		wp_enqueue_script( 'pv-sponsors', PV_URI . '/assets/sponsors.js', array(), PV_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
		$out = '<div class="pv-sponsors is-random" data-aantal="' . (int) $count . '"><div class="pv-sponsor-grid">';
		foreach ( $items as $n => $i ) {
			// Niet-gekozen logo's staan verborgen klaar; het script kiest in de browser opnieuw.
			$out .= pv_sponsor_logo( $i['post'], ' data-gewicht="' . (int) $i['weight'] . '"' . ( $n >= $count ? ' hidden' : '' ) );
		}
		return $out . '</div></div>';
	}

	/* Alles, per categorie */
	$out = '<div class="pv-sponsors">';
	foreach ( $groups as $g ) {
		$out .= '<div class="pv-sponsor-group">';
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
	$a = shortcode_atts( array( 'categorie' => '', 'koppen' => 'ja', 'aantal' => 0 ), $atts );
	return pv_sponsors_html( array( 'categorie' => $a['categorie'], 'koppen' => 'nee' !== $a['koppen'], 'aantal' => (int) $a['aantal'] ) );
} );

/** Aantal logo's in de sponsorbalk (Customizer). */
function pv_sponsor_band_html() {
	return pv_sponsors_html( array(
		'categorie' => (string) get_theme_mod( 'pv_home_sponsors', '' ),
		'aantal'    => max( 1, (int) get_theme_mod( 'pv_sponsor_count', 6 ) ),
	) );
}

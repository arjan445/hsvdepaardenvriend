<?php
/**
 * Header.
 *
 * @package paardenvriend
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php if ( post_type_exists( 'hsv_wedstrijd' ) && function_exists( 'hsv_ical_url' ) ) : ?>
		<link rel="alternate" type="text/calendar" title="Agenda <?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" href="<?php echo esc_url( hsv_ical_url() ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="visually-hidden-focusable pv-skip" href="#main">Naar de inhoud</a>

<?php if ( get_theme_mod( 'pv_notice' ) ) : ?>
	<div class="pv-notice">
		<div class="container">
			<?php if ( get_theme_mod( 'pv_notice_url' ) ) : ?>
				<a href="<?php echo esc_url( get_theme_mod( 'pv_notice_url' ) ); ?>"><?php echo esc_html( get_theme_mod( 'pv_notice' ) ); ?> →</a>
			<?php else : ?>
				<?php echo esc_html( get_theme_mod( 'pv_notice' ) ); ?>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<header class="pv-header">
	<nav class="navbar navbar-expand-lg" aria-label="Hoofdmenu">
		<div class="container">
			<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php
				$logo_id = get_theme_mod( 'custom_logo' );
				if ( $logo_id ) {
					echo wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'pv-logo', 'alt' => get_bloginfo( 'name' ) ) );
				} else {
					printf( '<img class="pv-logo" src="%s" alt="%s" width="330" height="180">', esc_url( PV_URI . '/assets/img/logo-hsv.jpg' ), esc_attr( get_bloginfo( 'name' ) ) );
				}
				?>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#pvNav" aria-controls="pvNav" aria-expanded="false" aria-label="Menu openen">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
			</button>
			<div class="collapse navbar-collapse" id="pvNav">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'navbar-nav ms-auto',
					'depth'          => 2,
					'walker'         => new PV_Nav_Walker(),
					'fallback_cb'    => 'pv_fallback_menu',
				) );
				?>
			</div>
		</div>
	</nav>
</header>

<main id="main">

<?php
/**
 * Homepage, zoals de mockup.
 *
 * @package paardenvriend
 */

get_header();

$hero_id = absint( get_theme_mod( 'pv_hero_image' ) );
$season  = absint( get_theme_mod( 'pv_season_image' ) );
$signup  = (string) get_theme_mod( 'pv_signup', pv_default_signup_html() );
$events  = pv_has_plugin() ? hsv_get_wedstrijden( array( 'limit' => 4, 'only_wedstrijden' => true ) ) : array();
?>

<section class="pv-hero<?php echo $hero_id ? ' has-image' : ''; ?>">
	<?php if ( $hero_id ) : ?>
		<?php echo wp_get_attachment_image( $hero_id, 'full', false, array( 'class' => 'pv-hero-img', 'alt' => '', 'fetchpriority' => 'high', 'sizes' => '100vw' ) ); ?>
	<?php endif; ?>
	<div class="pv-hero-inner">
		<div class="container">
			<?php if ( get_theme_mod( 'pv_hero_kicker', 'Hippische sportvereniging · Reuver' ) ) : ?>
				<p class="pv-kicker"><?php echo esc_html( get_theme_mod( 'pv_hero_kicker', 'Hippische sportvereniging · Reuver' ) ); ?></p>
			<?php endif; ?>
			<h1><?php echo esc_html( get_theme_mod( 'pv_hero_title', 'Indoorwedstrijden 2026' ) ); ?></h1>
			<p class="pv-hero-text"><?php echo esc_html( get_theme_mod( 'pv_hero_text', "Springen en dressuur voor paarden en pony's op onze accommodatie aan de Bergerhofweg." ) ); ?></p>
			<div class="pv-hero-actions">
				<?php if ( pv_has_plugin() ) : ?>
					<a class="pv-btn is-lime" href="<?php echo esc_url( get_post_type_archive_link( 'hsv_wedstrijd' ) ); ?>">Bekijk de agenda</a>
				<?php endif; ?>
				<?php if ( $signup ) : ?>
					<a class="pv-btn is-outline-light" href="#inschrijven">Hoe schrijf ik in?</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<section class="container pv-home-main">
	<div class="row g-4 g-lg-5">
		<div class="col-lg-8">
			<div class="pv-section-head">
				<h2>Aankomende wedstrijden</h2>
				<?php if ( pv_has_plugin() ) : ?>
					<a class="pv-link-strong" href="<?php echo esc_url( get_post_type_archive_link( 'hsv_wedstrijd' ) ); ?>">hele agenda →</a>
				<?php endif; ?>
			</div>
			<?php if ( $events ) : ?>
				<div class="hsv"><div class="hsv-list">
					<?php foreach ( $events as $p ) { hsv_template( 'parts/wedstrijd-rij.php', array( 'post' => $p ) ); } ?>
				</div></div>
			<?php else : ?>
				<p class="pv-muted">Er staan op dit moment geen wedstrijden gepland.</p>
			<?php endif; ?>

			<?php if ( $season ) : ?>
				<figure class="pv-season">
					<?php echo wp_get_attachment_image( $season, 'large', false, array( 'alt' => trim( (string) get_post_meta( $season, '_wp_attachment_image_alt', true ) ) ? get_post_meta( $season, '_wp_attachment_image_alt', true ) : 'Seizoenskalender' ) ); ?>
				</figure>
			<?php endif; ?>
		</div>

		<aside class="col-lg-4">
			<?php if ( $signup ) : ?>
				<div class="pv-box" id="inschrijven">
					<h2>Inschrijven</h2>
					<ul>
						<?php foreach ( array_filter( array_map( 'trim', explode( "\n", $signup ) ) ) as $line ) : ?>
							<li><?php echo wp_kses_post( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			<?php if ( pv_has_plugin() ) : ?>
				<div class="pv-latest">
					<h2>Laatste uitslagen</h2>
					<?php echo do_shortcode( '[hsv_uitslagen aantal="3"]' ); ?>
					<a class="pv-link-strong" href="<?php echo esc_url( get_post_type_archive_link( 'hsv_uitslag' ) ); ?>">alle uitslagen →</a>
				</div>
			<?php endif; ?>
		</aside>
	</div>
</section>

<?php
// Tekst van de pagina die als homepage is ingesteld (optioneel).
if ( 'page' === get_option( 'show_on_front' ) ) :
	while ( have_posts() ) :
		the_post();
		if ( trim( get_the_content() ) ) :
			?>
			<section class="container pv-home-content entry-content"><?php the_content(); ?></section>
			<?php
		endif;
	endwhile;
endif;
?>

<?php $pv_sp = pv_sponsors_html( array( 'categorie' => get_theme_mod( 'pv_home_sponsors', 'hoofdsponsor,stersponsor' ), 'koppen' => false ) ); ?>
<?php if ( $pv_sp ) : ?>
	<section class="pv-sponsor-band">
		<div class="container">
			<div class="row g-4 align-items-center">
				<div class="col-lg-4">
					<h2>Onze sponsoren</h2>
					<p>Zonder hen geen wedstrijden. Dank je wel!</p>
					<?php $pv_sp_page = get_page_by_path( 'sponsoren' ); ?>
					<?php if ( $pv_sp_page ) : ?>
						<a class="pv-link-strong" href="<?php echo esc_url( get_permalink( $pv_sp_page ) ); ?>">alle sponsoren →</a>
					<?php endif; ?>
				</div>
				<div class="col-lg-8"><?php echo $pv_sp; // phpcs:ignore ?></div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();

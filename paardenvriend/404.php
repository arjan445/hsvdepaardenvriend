<?php
/**
 * Niet gevonden.
 *
 * @package paardenvriend
 */

get_header();
?>
<div class="container pv-page text-center">
	<h1>Pagina niet gevonden</h1>
	<p class="pv-muted">Deze pagina bestaat niet (meer). Misschien zoek je de agenda of de uitslagen?</p>
	<p class="pv-404-actions">
		<a class="pv-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Naar de homepage</a>
		<?php if ( pv_has_plugin() ) : ?>
			<a class="pv-btn is-outline" href="<?php echo esc_url( get_post_type_archive_link( 'hsv_wedstrijd' ) ); ?>">Agenda</a>
			<a class="pv-btn is-outline" href="<?php echo esc_url( get_post_type_archive_link( 'hsv_uitslag' ) ); ?>">Uitslagen</a>
		<?php endif; ?>
	</p>
</div>
<?php
get_footer();

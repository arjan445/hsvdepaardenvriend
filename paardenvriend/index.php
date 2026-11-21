<?php
/**
 * Nieuwsoverzicht en overige archieven.
 *
 * @package paardenvriend
 */

get_header();
?>
<div class="container pv-page">
	<header class="pv-page-head">
		<h1><?php echo is_home() ? 'Nieuws' : wp_kses_post( get_the_archive_title() ); ?></h1>
	</header>
	<?php if ( have_posts() ) : ?>
		<div class="pv-posts">
			<?php while ( have_posts() ) : the_post(); ?>
				<article class="pv-post">
					<p class="pv-kicker-dark"><?php echo esc_html( get_the_date() ); ?></p>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="pv-muted"><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( array( 'prev_text' => '‹ nieuwer', 'next_text' => 'ouder ›' ) ); ?>
	<?php else : ?>
		<p class="pv-muted">Er is hier nog niets te zien.</p>
	<?php endif; ?>
</div>
<?php
get_footer();

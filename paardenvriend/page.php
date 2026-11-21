<?php
/**
 * Pagina.
 *
 * @package paardenvriend
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<article class="container pv-page">
		<header class="pv-page-head"><h1><?php the_title(); ?></h1></header>
		<div class="entry-content pv-content"><?php the_content(); ?></div>
		<?php if ( is_page( 'sponsoren' ) ) : ?>
			<?php echo pv_sponsors_html(); // phpcs:ignore ?>
		<?php endif; ?>
	</article>
	<?php
endwhile;
get_footer();

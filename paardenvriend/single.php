<?php
/**
 * Nieuwsbericht.
 *
 * @package paardenvriend
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<article class="container pv-page">
		<header class="pv-page-head">
			<p class="pv-kicker-dark"><?php echo esc_html( get_the_date() ); ?></p>
			<h1><?php the_title(); ?></h1>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="pv-featured"><?php the_post_thumbnail( 'large' ); ?></figure>
		<?php endif; ?>
		<div class="entry-content pv-content"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;
get_footer();

<?php
/**
 * Footer.
 *
 * @package paardenvriend
 */

$c = pv_contact();
?>
</main>

<?php if ( ! is_front_page() && ! is_page( 'sponsoren' ) ) : ?>
	<?php $pv_sp = pv_sponsors_html( array( 'categorie' => get_theme_mod( 'pv_home_sponsors', 'hoofdsponsor,stersponsor' ), 'koppen' => false ) ); ?>
	<?php if ( $pv_sp ) : ?>
		<section class="pv-sponsor-band is-compact" aria-label="Sponsoren">
			<div class="container">
				<h2 class="pv-band-title">Met dank aan onze sponsoren</h2>
				<?php echo $pv_sp; // phpcs:ignore ?>
			</div>
		</section>
	<?php endif; ?>
<?php endif; ?>

<footer class="pv-footer">
	<div class="container">
		<div class="row g-4">
			<div class="col-md-5">
				<div class="pv-footer-brand"><?php bloginfo( 'name' ); ?></div>
				<?php if ( $c['address'] ) : ?>
					<p><?php echo nl2br( esc_html( $c['address'] ) ); ?></p>
				<?php endif; ?>
			</div>
			<div class="col-sm-6 col-md-3">
				<ul>
					<?php if ( $c['phone'] ) : ?><li><a href="<?php echo esc_attr( pv_tel_href( $c['phone'] ) ); ?>"><?php echo esc_html( $c['phone'] ); ?></a></li><?php endif; ?>
					<?php if ( $c['email'] ) : ?><li><a href="mailto:<?php echo esc_attr( antispambot( $c['email'] ) ); ?>"><?php echo esc_html( antispambot( $c['email'] ) ); ?></a></li><?php endif; ?>
					<?php if ( $c['facebook'] ) : ?><li><a href="<?php echo esc_url( $c['facebook'] ); ?>" target="_blank" rel="noopener">Volg ons op Facebook</a></li><?php endif; ?>
				</ul>
			</div>
			<div class="col-sm-6 col-md-4">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => false,
				) );
				?>
			</div>
		</div>
		<div class="pv-footer-bottom">
			<span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?><?php echo $c['kvk'] ? ' · KVK ' . esc_html( $c['kvk'] ) : ''; ?></span>
			<?php if ( get_privacy_policy_url() ) : ?>
				<a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">privacyverklaring</a>
			<?php endif; ?>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

<?php
/** Site footer. */
do_action( 'zola_before_footer' );
if ( ! zola_has_elementor_location( 'footer' ) && ! zola_render_elementor_layout( 'footer' ) ) :
	?>
	<footer class="site-footer">
		<div class="site-footer__main">
			<div class="site-footer__identity">
				<?php zola_site_brand( true ); ?>
			</div>
			<?php if ( get_theme_mod( 'zola_footer_show_menu', true ) ) : ?>
			<?php
			wp_nav_menu( array(
				'theme_location'       => 'footer',
				'container'            => 'nav',
				'container_aria_label' => esc_attr__( 'Footer navigation', 'zola' ),
				'menu_class'           => 'footer-menu',
				'fallback_cb'          => false,
			) );
			?>
			<?php endif; ?>
		</div>
		<div class="site-footer__bottom">
			<?php
			$copyright = trim( (string) get_theme_mod( 'zola_footer_copyright', '' ) );
			if ( $copyright ) {
				$copyright = str_replace( '{year}', wp_date( 'Y' ), $copyright );
			} else {
				$copyright = '© ' . wp_date( 'Y' ) . ' ' . get_bloginfo( 'name' );
			}
			?>
			<span><?php echo esc_html( $copyright ); ?></span>
			<?php if ( get_theme_mod( 'zola_footer_show_back_to_top', true ) ) : ?><a href="#top" class="back-to-top"><?php esc_html_e( 'Back to top', 'zola' ); ?> <span aria-hidden="true">↑</span></a><?php endif; ?>
		</div>
	</footer>
	<?php
endif;
do_action( 'zola_after_footer' );
wp_footer();
?>
</body>
</html>

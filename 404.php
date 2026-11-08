<?php get_header(); ?>
<main id="main-content" class="site-main page-main">
	<?php if ( ! zola_has_elementor_location( 'archive' ) ) : ?>
	<section class="not-found wrap"><p class="eyebrow" data-reveal>404 / <?php esc_html_e( 'Page not found', 'zola' ); ?></p><h1 data-reveal><?php esc_html_e( 'Let’s find a better direction.', 'zola' ); ?></h1><p><?php esc_html_e( 'The page may have moved or the link may be out of date.', 'zola' ); ?></p><a class="button button--accent" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'zola' ); ?><span aria-hidden="true">↗</span></a></section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>


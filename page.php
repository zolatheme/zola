<?php get_header(); ?>
<main id="main-content" class="site-main page-main">
	<?php if ( ! zola_has_elementor_location( 'single' ) ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'page-content' ); ?>>
			<?php if ( ! is_front_page() && ! get_post_meta( get_the_ID(), '_elementor_edit_mode', true ) && get_theme_mod( 'zola_page_show_title', true ) ) : ?>
				<header class="page-intro"><div class="wrap"><?php $page_eyebrow = trim( (string) get_theme_mod( 'zola_page_title_eyebrow', 'Page' ) ); ?><?php if ( $page_eyebrow ) : ?><p class="eyebrow" data-reveal><?php echo esc_html( $page_eyebrow ); ?></p><?php endif; ?><h1 data-reveal><?php echo esc_html( get_the_title() ); ?></h1></div></header>
			<?php endif; ?>
			<div class="entry-content <?php echo esc_attr( get_post_meta( get_the_ID(), '_elementor_edit_mode', true ) ? 'entry-content--builder' : 'wrap entry-content--standard' ); ?>">
				<?php the_content(); ?>
				<?php wp_link_pages( array( 'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'zola' ), 'after' => '</nav>' ) ); ?>
			</div>
		</article>
	<?php endwhile; endif; ?>
</main>
<?php get_footer(); ?>


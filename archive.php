<?php get_header(); ?>
<?php $is_portfolio_archive = zola_is_portfolio_archive(); ?>
<main id="main-content" class="site-main page-main">
	<?php if ( ! zola_has_elementor_location( 'archive' ) ) : ?>
	<?php $archive_eyebrow = $is_portfolio_archive ? get_theme_mod( 'zola_portfolio_archive_title', __( 'Portfolio', 'zola' ) ) : __( 'Archive', 'zola' ); ?>
	<section class="page-intro"><div class="wrap"><p class="eyebrow" data-reveal><?php echo esc_html( $archive_eyebrow ); ?></p><h1 data-reveal><?php echo wp_kses_post( get_the_archive_title() ); ?></h1><?php the_archive_description( '<div class="archive-description">', '</div>' ); ?></div></section>
	<div class="wrap archive-layout<?php echo zola_blog_sidebar_visible() ? ' archive-layout--with-sidebar' : ''; ?>">
		<div class="archive-layout__main">
		<?php if ( have_posts() ) : ?><div class="post-grid <?php echo esc_attr( zola_archive_layout_class() ); ?><?php echo $is_portfolio_archive ? ' post-grid--portfolio' : ''; ?>"><?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content', 'card' ); endwhile; ?></div><?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => esc_html__( 'Newer', 'zola' ), 'next_text' => esc_html__( 'Older', 'zola' ) ) ); ?>
		<?php else : ?><p><?php esc_html_e( 'Nothing published here yet.', 'zola' ); ?></p><?php endif; ?>
		</div>
		<?php zola_render_blog_sidebar(); ?>
	</div>
	<?php endif; ?>
</main>
<?php get_footer(); ?>


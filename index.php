<?php
get_header();
?>
<main id="main-content" class="site-main page-main">
	<?php if ( ! zola_has_elementor_location( 'archive' ) ) : ?>
	<section class="page-intro page-intro--compact">
		<div class="wrap">
			<p class="eyebrow" data-reveal><?php esc_html_e( 'Blog', 'zola' ); ?></p>
			<h1 data-reveal><?php echo esc_html( is_home() && get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'Latest posts', 'zola' ) ); ?></h1>
		</div>
	</section>
	<div class="wrap archive-layout<?php echo zola_blog_sidebar_visible() ? ' archive-layout--with-sidebar' : ''; ?>">
		<div class="archive-layout__main">
		<?php if ( have_posts() ) : ?>
			<div class="post-grid <?php echo esc_attr( zola_archive_layout_class() ); ?>">
				<?php $card_index = 0; while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content', 'card', array( 'prioritize_image' => 0 === $card_index ) ); $card_index++; endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => esc_html__( 'Newer', 'zola' ), 'next_text' => esc_html__( 'Older', 'zola' ) ) ); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing published here yet. Add a post to get started.', 'zola' ); ?></p>
		<?php endif; ?>
		</div>
		<?php zola_render_blog_sidebar(); ?>
	</div>
	<?php endif; ?>
</main>
<?php get_footer(); ?>


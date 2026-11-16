<?php get_header(); ?>
<main id="main-content" class="site-main page-main">
	<?php if ( ! zola_has_elementor_location( 'archive' ) ) : ?>
	<section class="page-intro"><div class="wrap"><p class="eyebrow" data-reveal><?php esc_html_e( 'Search', 'zola' ); ?></p><h1 data-reveal><?php printf( esc_html__( 'Results for “%s”', 'zola' ), esc_html( get_search_query() ) ); ?></h1></div></section>
	<div class="wrap archive-layout<?php echo zola_blog_sidebar_visible() ? ' archive-layout--with-sidebar' : ''; ?>">
		<div class="archive-layout__main">
		<?php if ( have_posts() ) : ?>
			<div class="post-grid <?php echo esc_attr( zola_archive_layout_class() ); ?>">
				<?php $card_index = 0; while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content', 'card', array( 'prioritize_image' => 0 === $card_index ) ); $card_index++; endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?><p><?php esc_html_e( 'No results yet. Try another search term.', 'zola' ); ?></p><?php get_search_form(); ?><?php endif; ?>
		</div>
		<?php zola_render_blog_sidebar(); ?>
	</div>
	<?php endif; ?>
</main>
<?php get_footer(); ?>


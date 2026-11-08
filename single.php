<?php get_header(); ?>
<main id="main-content" class="site-main page-main">
	<?php if ( ! zola_has_elementor_location( 'single' ) ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<div class="single-layout wrap<?php echo zola_blog_sidebar_visible() ? ' single-layout--with-sidebar' : ''; ?>">
		<article <?php post_class( 'single-article' ); ?>>
			<header class="single-article__header">
				<p class="eyebrow" data-reveal><?php echo esc_html( get_the_date() ); ?> <span>/</span> <?php echo esc_html( get_the_author() ); ?><?php if ( get_the_category() ) : ?> <span>/</span> <?php the_category( ', ' ); ?><?php endif; ?></p>
				<h1 data-reveal><?php echo esc_html( get_the_title() ); ?></h1>
				<?php if ( has_post_thumbnail() ) : ?><div class="single-article__image" data-reveal><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?>
			</header>
			<div class="entry-content entry-content--standard"><?php the_content(); ?><?php wp_link_pages(); ?></div>
			<footer class="single-article__footer"><?php the_tags( '<span>' . esc_html__( 'Topics:', 'zola' ) . '</span> ', ', ' ); ?></footer>
			<?php $share_url = get_permalink(); $share_title = get_the_title(); ?>
			<?php if ( zola_share_links_visible() ) : ?>
			<nav class="single-article__share" aria-label="<?php esc_attr_e( 'Share this post', 'zola' ); ?>">
				<strong><?php echo esc_html( get_theme_mod( 'zola_share_label', __( 'Share', 'zola' ) ) ); ?></strong>
				<?php if ( get_theme_mod( 'zola_share_facebook', true ) ) : ?><a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $share_url ) ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a><?php endif; ?>
				<?php if ( get_theme_mod( 'zola_share_x', true ) ) : ?><a href="<?php echo esc_url( 'https://twitter.com/intent/tweet?text=' . rawurlencode( $share_title ) . '&url=' . rawurlencode( $share_url ) ); ?>" target="_blank" rel="noopener noreferrer">X</a><?php endif; ?>
				<?php if ( get_theme_mod( 'zola_share_linkedin', true ) ) : ?><a href="<?php echo esc_url( 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $share_url ) ); ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a><?php endif; ?>
				<?php if ( get_theme_mod( 'zola_share_copy_link', true ) ) : ?><button type="button" data-copy-link="<?php echo esc_url( $share_url ); ?>"><?php esc_html_e( 'Copy link', 'zola' ); ?></button><?php endif; ?>
			</nav>
			<?php endif; ?>
			<?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; ?>
		</article>
		<?php zola_render_blog_sidebar(); ?>
		</div>
		<?php
		$related_categories = wp_get_post_categories( get_the_ID() );
		if ( $related_categories ) :
			$related_posts = new WP_Query( array(
				'category__in'        => $related_categories,
				'post__not_in'        => array( get_the_ID() ),
				'posts_per_page'      => 3,
				'ignore_sticky_posts' => true,
			) );
			if ( $related_posts->have_posts() ) :
				?>
				<section class="related-posts wrap" aria-labelledby="related-posts-title">
					<p class="eyebrow"><?php esc_html_e( 'Keep exploring', 'zola' ); ?></p>
					<h2 id="related-posts-title"><?php esc_html_e( 'Related notes.', 'zola' ); ?></h2>
					<div class="post-grid post-grid--home">
						<?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); get_template_part( 'template-parts/content', 'card' ); endwhile; ?>
					</div>
				</section>
				<?php
			endif;
			wp_reset_postdata();
		endif;
		?>
	<?php endwhile; endif; ?>
</main>
<?php get_footer(); ?>


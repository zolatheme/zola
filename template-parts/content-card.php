<article <?php post_class( 'post-card' ); ?> data-reveal>
	<a class="post-card__link" href="<?php the_permalink(); ?>">
		<div class="post-card__media">
			<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); else : ?><span class="post-card__placeholder" aria-hidden="true"><i></i><i></i><i></i></span><?php endif; ?>
			<span class="post-card__arrow" aria-hidden="true">↗</span>
		</div>
		<?php
		$is_portfolio   = in_array( get_post_type(), array( 'portfolio', 'project' ), true );
		$show_date      = $is_portfolio ? get_theme_mod( 'zola_portfolio_show_date', false ) : get_theme_mod( 'zola_blog_show_date', true );
		$show_categories = ! $is_portfolio && get_theme_mod( 'zola_blog_show_categories', true );
		$show_excerpt   = $is_portfolio || get_theme_mod( 'zola_blog_show_excerpt', true );
		$excerpt_length = $is_portfolio
			? absint( get_theme_mod( 'zola_portfolio_excerpt_length', 18 ) )
			: absint( get_theme_mod( 'zola_blog_excerpt_length', 22 ) );
		$categories    = $show_categories ? get_the_category() : array();
		$has_meta       = ( $show_date && get_the_date() ) || ! empty( $categories );
		?>
		<?php if ( $has_meta ) : ?>
			<div class="post-card__meta">
				<?php if ( $show_date ) : ?><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time><?php endif; ?>
				<?php if ( ! empty( $categories ) ) : ?><span><?php echo esc_html( $categories[0]->name ); ?></span><?php endif; ?>
			</div>
		<?php endif; ?>
		<h2><?php echo esc_html( get_the_title() ); ?></h2>
		<?php if ( $show_excerpt ) : ?><p><?php echo esc_html( wp_trim_words( get_the_excerpt(), min( 60, max( 8, $excerpt_length ) ) ) ); ?></p><?php endif; ?>
	</a>
</article>


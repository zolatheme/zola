<?php
/** Template Name: Zola Full Width
 * Template Post Type: page
 */
get_header();
?>
<main id="main-content" class="site-main site-main--full-width">
	<?php while ( have_posts() ) : the_post(); ?><article <?php post_class( 'page-content' ); ?>><div class="entry-content entry-content--builder"><?php the_content(); ?></div></article><?php endwhile; ?>
</main>
<?php get_footer(); ?>


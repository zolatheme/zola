<?php
/** Default WooCommerce wrapper. Elementor Theme Builder may replace shop templates. */
get_header();
?>
<main id="main-content" class="site-main page-main woocommerce-main">
	<div class="wrap">
		<?php $location = is_singular( 'product' ) ? 'single' : 'archive'; ?>
		<?php if ( ! zola_has_elementor_location( $location ) ) : woocommerce_content(); endif; ?>
	</div>
</main>
<?php get_footer(); ?>


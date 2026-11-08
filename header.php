<?php
/** Site header. */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body id="top" <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'zola' ); ?></a>
<?php do_action( 'zola_before_header' ); ?>
<?php zola_render_header_builder(); ?>
<?php do_action( 'zola_after_header' ); ?>

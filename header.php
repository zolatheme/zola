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
<?php
$header_class = 'site-header';
$header_cta_label = trim( (string) get_theme_mod( 'zola_header_cta_label', '' ) );
$header_cta_url = esc_url( get_theme_mod( 'zola_header_cta_url', '' ) );
$has_elementor_home = 'page' === get_option( 'show_on_front' ) && is_front_page();
$header_class .= $has_elementor_home && get_theme_mod( 'zola_transparent_header', true ) ? ' site-header--overlay' : ' site-header--solid';
$header_class .= get_theme_mod( 'zola_sticky_header', true ) ? ' site-header--sticky' : ' site-header--flow';
if ( ! zola_has_elementor_location( 'header' ) && ! zola_render_elementor_layout( 'header' ) ) :
	?>
<header class="<?php echo esc_attr( $header_class ); ?>" data-site-header>
	<?php $announcement_text = trim( (string) get_theme_mod( 'zola_announcement_text', '' ) ); ?>
	<?php if ( $announcement_text ) : ?>
		<?php $announcement_url = esc_url( get_theme_mod( 'zola_announcement_url', '' ) ); ?>
		<div class="site-announcement"><div class="site-announcement__inner"><?php if ( $announcement_url ) : ?><a href="<?php echo $announcement_url; ?>"><?php echo esc_html( $announcement_text ); ?><span aria-hidden="true">↗</span></a><?php else : ?><span><?php echo esc_html( $announcement_text ); ?></span><?php endif; ?></div></div>
	<?php endif; ?>
	<div class="site-header__inner">
		<?php zola_site_brand(); ?>
		<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">
			<span class="menu-toggle__lines" aria-hidden="true"><i></i><i></i></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'zola' ); ?></span>
		</button>
		<nav class="site-nav" id="primary-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'zola' ); ?>" data-primary-nav>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'menu',
				'fallback_cb'    => 'zola_fallback_menu',
				'depth'          => 2,
			) );
			?>
		</nav>
		<?php if ( $header_cta_label && $header_cta_url ) : ?>
			<a class="button button--small button--accent site-header__cta" href="<?php echo $header_cta_url; ?>">
				<?php echo esc_html( $header_cta_label ); ?><span aria-hidden="true">↗</span>
			</a>
		<?php endif; ?>
	</div>
</header>
<?php endif; ?>
<?php do_action( 'zola_after_header' ); ?>

<?php
/**
 * Zola functions and definitions.
 *
 * @package Zola
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ZOLA_VERSION', '1.0.0' );
define( 'ZOLA_DIR', get_template_directory() );
define( 'ZOLA_URI', get_template_directory_uri() );

require_once ZOLA_DIR . '/inc/customizer.php';
require_once ZOLA_DIR . '/inc/template-tags.php';
require_once ZOLA_DIR . '/inc/site-builder.php';
require_once ZOLA_DIR . '/inc/admin.php';

/** Theme supports and navigation locations. */
function zola_setup(): void {
	load_theme_textdomain( 'zola', ZOLA_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'custom-logo', array(
		'height'      => 96,
		'width'       => 280,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'elementor' );
	add_theme_support( 'woocommerce', array(
		'product_grid' => array(
			'default_rows'    => 3,
			'min_rows'        => 1,
			'max_rows'        => 8,
			'default_columns' => 3,
			'min_columns'     => 1,
			'max_columns'     => 4,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary' => esc_html__( 'Primary navigation', 'zola' ),
		'footer'  => esc_html__( 'Footer navigation', 'zola' ),
	) );
}
add_action( 'after_setup_theme', 'zola_setup' );

/** Register the optional blog sidebar displayed by the theme layouts. */
function zola_register_sidebars(): void {
	register_sidebar( array(
		'name'          => esc_html__( 'Blog sidebar', 'zola' ),
		'id'            => 'zola-blog-sidebar',
		'description'   => esc_html__( 'Widgets shown beside blog archives and single posts when enabled in the Blog Customizer section.', 'zola' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget__title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'zola_register_sidebars' );

/** Load theme styles and the small interaction layer. */
function zola_enqueue_assets(): void {
	wp_enqueue_style( 'zola-style', get_stylesheet_uri(), array(), ZOLA_VERSION );
	wp_enqueue_style( 'zola-components', ZOLA_URI . '/assets/css/theme.css', array( 'zola-style' ), (string) filemtime( ZOLA_DIR . '/assets/css/theme.css' ) );
	if ( is_rtl() ) {
		wp_enqueue_style( 'zola-rtl', ZOLA_URI . '/assets/css/rtl.css', array( 'zola-components' ), (string) filemtime( ZOLA_DIR . '/assets/css/rtl.css' ) );
	}
	wp_enqueue_script(
		'zola-interactions',
		ZOLA_URI . '/assets/js/theme.js',
		array(),
		(string) filemtime( ZOLA_DIR . '/assets/js/theme.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_localize_script( 'zola-interactions', 'ZolaStrings', array(
		'menuOpen'    => __( 'Open navigation', 'zola' ),
		'menuClose'   => __( 'Close navigation', 'zola' ),
		'submenuOpen' => __( 'Open submenu', 'zola' ),
		'submenuClose' => __( 'Close submenu', 'zola' ),
		'copied'      => __( 'Copied', 'zola' ),
	) );

	$accent_setting = get_theme_mod( 'zola_accent_color', '#2563eb' );
	$ink_setting    = get_theme_mod( 'zola_ink_color', '#202124' );
	$paper_setting  = get_theme_mod( 'zola_paper_color', '#ffffff' );
	$text_setting   = get_theme_mod( 'zola_text_color', '#202126' );
	$accent         = is_string( $accent_setting ) ? sanitize_hex_color( $accent_setting ) : null;
	$ink            = is_string( $ink_setting ) ? sanitize_hex_color( $ink_setting ) : null;
	$paper          = is_string( $paper_setting ) ? sanitize_hex_color( $paper_setting ) : null;
	$text           = is_string( $text_setting ) ? sanitize_hex_color( $text_setting ) : null;
	$font_choices = array(
		'sans'      => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
		'arial'     => 'Arial, Helvetica, sans-serif',
		'georgia'   => 'Georgia, "Times New Roman", serif',
		'trebuchet' => '"Trebuchet MS", sans-serif',
	);
	$font_setting = get_theme_mod( 'zola_font_family', 'sans' );
	$font_choice  = is_string( $font_setting ) ? sanitize_key( $font_setting ) : 'sans';
	$font_family = isset( $font_choices[ $font_choice ] ) ? $font_choices[ $font_choice ] : $font_choices['sans'];
	$site_width  = min( 1680, max( 960, absint( get_theme_mod( 'zola_site_width', 1320 ) ) ) );
	$content_width = min( 1100, max( 560, absint( get_theme_mod( 'zola_content_width', 780 ) ) ) );
	$button_radius = min( 20, absint( get_theme_mod( 'zola_button_radius', 3 ) ) );
	$post_columns = min( 4, max( 2, absint( get_theme_mod( 'zola_archive_columns', 3 ) ) ) );
	$portfolio_columns = min( 4, max( 2, absint( get_theme_mod( 'zola_portfolio_columns', 3 ) ) ) );
	$shop_columns = min( 4, max( 2, absint( get_theme_mod( 'zola_shop_columns', 3 ) ) ) );
	$inline = ':root{--zola-accent:' . ( $accent ? $accent : '#2563eb' ) . ';--zola-ink:' . ( $ink ? $ink : '#202124' ) . ';--zola-dark-bg:' . ( $ink ? $ink : '#202124' ) . ';--zola-paper:' . ( $paper ? $paper : '#ffffff' ) . ';--zola-text:' . ( $text ? $text : '#202126' ) . ';--zola-wrap:' . $site_width . 'px;--zola-content-width:' . $content_width . 'px;--zola-radius:' . $button_radius . 'px;--zola-post-columns:' . $post_columns . ';--zola-portfolio-columns:' . $portfolio_columns . ';--zola-shop-columns:' . $shop_columns . ';--zola-font:' . $font_family . ';}';
	wp_add_inline_style( 'zola-components', $inline );
}
add_action( 'wp_enqueue_scripts', 'zola_enqueue_assets' );

/** Keep the WooCommerce loop settings available to site owners from the Customizer. */
function zola_woocommerce_columns(): int {
	return min( 4, max( 2, absint( get_theme_mod( 'zola_shop_columns', 3 ) ) ) );
}
add_filter( 'loop_shop_columns', 'zola_woocommerce_columns' );

function zola_woocommerce_products_per_page(): int {
	$per_page = absint( get_theme_mod( 'zola_shop_per_page', 12 ) );
	return in_array( $per_page, array( 6, 9, 12, 18, 24 ), true ) ? $per_page : 12;
}
add_filter( 'loop_shop_per_page', 'zola_woocommerce_products_per_page', 20 );

/** Use Elementor Theme Builder locations when the active plugin provides them. */
function zola_register_elementor_locations( $elementor_theme_manager ) {
	if ( method_exists( $elementor_theme_manager, 'register_all_core_location' ) ) {
		$elementor_theme_manager->register_all_core_location();
	}
}
add_action( 'elementor/theme/register_locations', 'zola_register_elementor_locations' );

/** Register the focused Elementor widgets shipped with the theme. */
function zola_register_elementor_category( $elements_manager ) {
	$elements_manager->add_category( 'zola-elements', array(
		'title' => esc_html__( 'Zola', 'zola' ),
		'icon'  => 'eicon-elementor',
	) );
}
add_action( 'elementor/elements/categories_registered', 'zola_register_elementor_category' );

function zola_register_elementor_widgets( $widgets_manager ) {
	if ( ! class_exists( '\\Elementor\\Widget_Base' ) ) {
		return;
	}
	require_once ZOLA_DIR . '/inc/elementor/class-zola-hero-widget.php';
	require_once ZOLA_DIR . '/inc/elementor/class-zola-menu-widget.php';
	$widgets_manager->register( new Zola_Hero_Widget() );
	$widgets_manager->register( new Zola_Menu_Widget() );
}
add_action( 'elementor/widgets/register', 'zola_register_elementor_widgets' );

/** Keep the default content width friendly to page builders. */
function zola_content_width(): void {
	$GLOBALS['content_width'] = apply_filters( 'zola_content_width', 1240 );
}
add_action( 'after_setup_theme', 'zola_content_width', 0 );

/** Add a small class when Elementor owns the current document layout. */
function zola_body_classes( array $classes ): array {
	if ( did_action( 'elementor/loaded' ) && ( is_singular() && get_post_meta( get_queried_object_id(), '_elementor_edit_mode', true ) ) ) {
		$classes[] = 'zola-elementor-page';
	}
	if ( is_front_page() ) {
		$classes[] = 'zola-home';
	}
	if ( ! get_theme_mod( 'zola_sticky_header', true ) ) {
		$classes[] = 'zola-nonsticky-header';
	}
	$header_template_id = absint( get_theme_mod( 'zola_header_template', 0 ) );
	if ( $header_template_id && did_action( 'elementor/loaded' ) && 'publish' === get_post_status( $header_template_id ) ) {
		$classes[] = 'zola-has-elementor-header';
	}
	if ( trim( (string) get_theme_mod( 'zola_announcement_text', '' ) ) && zola_header_builder_has_module( 'announcement' ) && ! $header_template_id ) {
		$classes[] = 'zola-has-announcement';
	}
	return $classes;
}
add_filter( 'body_class', 'zola_body_classes' );



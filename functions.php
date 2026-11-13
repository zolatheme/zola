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
		'primary'   => esc_html__( 'Primary navigation', 'zola' ),
		'secondary' => esc_html__( 'Secondary navigation', 'zola' ),
		'top_bar'   => esc_html__( 'Top Bar navigation', 'zola' ),
		'footer'    => esc_html__( 'Footer navigation', 'zola' ),
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
	wp_enqueue_style( 'zola-components', ZOLA_URI . '/assets/css/theme.css', array(), (string) filemtime( ZOLA_DIR . '/assets/css/theme.css' ) );
	if ( get_stylesheet_directory() !== get_template_directory() ) {
		$child_stylesheet = get_stylesheet_directory() . '/style.css';
		if ( is_readable( $child_stylesheet ) ) {
			wp_enqueue_style( 'zola-child-style', get_stylesheet_uri(), array( 'zola-components' ), (string) filemtime( $child_stylesheet ) );
		}
	}
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
	$header_width = 'full' === zola_sanitize_header_width( get_theme_mod( 'zola_header_width', 'contained' ) )
		? 'calc(100% - var(--zola-gutter) * 2)'
		: 'min(calc(100% - var(--zola-gutter) * 2), var(--zola-wrap))';
	$header_height = zola_sanitize_header_height( get_theme_mod( 'zola_header_height', 70 ) );
	$transparent_header_height = zola_sanitize_header_height( get_theme_mod( 'zola_transparent_header_height', 70 ) );
	$header_background_setting = get_theme_mod( 'zola_header_background_color', '#ffffff' );
	$header_background = is_string( $header_background_setting ) ? sanitize_hex_color( $header_background_setting ) : null;
	$top_bar_background_setting = get_theme_mod( 'zola_top_bar_background_color', '' );
	$top_bar_background = is_string( $top_bar_background_setting ) ? sanitize_hex_color( $top_bar_background_setting ) : null;
	$top_bar_height = zola_sanitize_top_bar_height( get_theme_mod( 'zola_top_bar_height', 40 ) );
	$transparent_background_setting = get_theme_mod( 'zola_transparent_header_background_color', '' );
	$transparent_background = is_string( $transparent_background_setting ) ? sanitize_hex_color( $transparent_background_setting ) : null;
	$nav_color_setting = get_theme_mod( 'zola_header_nav_color', '' );
	$nav_color = is_string( $nav_color_setting ) ? sanitize_hex_color( $nav_color_setting ) : null;
	$nav_hover_setting = get_theme_mod( 'zola_header_nav_hover_color', '' );
	$nav_hover = is_string( $nav_hover_setting ) ? sanitize_hex_color( $nav_hover_setting ) : null;
	$icon_color_setting = get_theme_mod( 'zola_header_icon_color', '' );
	$icon_color = is_string( $icon_color_setting ) ? sanitize_hex_color( $icon_color_setting ) : null;
	$icon_hover_setting = get_theme_mod( 'zola_header_icon_hover_color', '' );
	$icon_hover = is_string( $icon_hover_setting ) ? sanitize_hex_color( $icon_hover_setting ) : null;
	$nav_size_choices = array( 'xs' => 12, 's' => 14, 'default' => 16, 'm' => 18, 'l' => 20, 'xl' => 22 );
	$nav_size_setting = sanitize_key( (string) get_theme_mod( 'zola_header_nav_size', 'default' ) );
	$nav_size = isset( $nav_size_choices[ $nav_size_setting ] ) ? $nav_size_choices[ $nav_size_setting ] : 16;
	$nav_spacing_choices = array( 'xs' => 8, 's' => 12, 'default' => 20, 'm' => 24, 'l' => 32, 'xl' => 40 );
	$nav_spacing_setting = sanitize_key( (string) get_theme_mod( 'zola_header_nav_spacing', 'default' ) );
	$nav_spacing = isset( $nav_spacing_choices[ $nav_spacing_setting ] ) ? $nav_spacing_choices[ $nav_spacing_setting ] : 20;
	$nav_height = zola_sanitize_header_nav_height( get_theme_mod( 'zola_header_nav_height', 16 ) );
	$nav_push = zola_sanitize_header_nav_push( get_theme_mod( 'zola_header_nav_push', 9 ) );
	$top_bar_nav_height = zola_sanitize_header_nav_height( get_theme_mod( 'zola_top_bar_nav_height', 16 ) );
	$top_bar_nav_color_setting = get_theme_mod( 'zola_top_bar_nav_color', '' );
	$top_bar_nav_color = is_string( $top_bar_nav_color_setting ) ? sanitize_hex_color( $top_bar_nav_color_setting ) : null;
	$top_bar_nav_hover_color_setting = get_theme_mod( 'zola_top_bar_nav_hover_color', '' );
	$top_bar_nav_hover_color = is_string( $top_bar_nav_hover_color_setting ) ? sanitize_hex_color( $top_bar_nav_hover_color_setting ) : null;
	$header_bottom_height = zola_sanitize_header_bottom_height( get_theme_mod( 'zola_header_bottom_height', 10 ) );
	$header_bottom_background_setting = get_theme_mod( 'zola_header_bottom_background_color', '' );
	$header_bottom_background = is_string( $header_bottom_background_setting ) ? sanitize_hex_color( $header_bottom_background_setting ) : null;
	$bottom_nav_size_choices = array( 'xs' => 12, 's' => 14, 'default' => 16, 'm' => 18, 'l' => 20, 'xl' => 22 );
	$bottom_nav_size_setting = sanitize_key( (string) get_theme_mod( 'zola_header_bottom_nav_size', 'default' ) );
	$bottom_nav_size = isset( $bottom_nav_size_choices[ $bottom_nav_size_setting ] ) ? $bottom_nav_size_choices[ $bottom_nav_size_setting ] : 16;
	$bottom_nav_spacing_choices = array( 'xs' => 8, 's' => 12, 'default' => 20, 'm' => 24, 'l' => 32, 'xl' => 40 );
	$bottom_nav_spacing_setting = sanitize_key( (string) get_theme_mod( 'zola_header_bottom_nav_spacing', 'default' ) );
	$bottom_nav_spacing = isset( $bottom_nav_spacing_choices[ $bottom_nav_spacing_setting ] ) ? $bottom_nav_spacing_choices[ $bottom_nav_spacing_setting ] : 20;
	$bottom_nav_height = zola_sanitize_header_nav_height( get_theme_mod( 'zola_header_bottom_nav_height', 16 ) );
	$bottom_nav_base_mode = zola_sanitize_header_text_mode( get_theme_mod( 'zola_header_bottom_nav_base_color', 'dark' ) );
	$bottom_nav_base_color = 'light' === $bottom_nav_base_mode ? '#ffffff' : 'var(--zola-text)';
	$bottom_nav_color_setting = get_theme_mod( 'zola_header_bottom_nav_color', '' );
	$bottom_nav_color = is_string( $bottom_nav_color_setting ) ? sanitize_hex_color( $bottom_nav_color_setting ) : null;
	$bottom_nav_hover_setting = get_theme_mod( 'zola_header_bottom_nav_hover_color', '' );
	$bottom_nav_hover = is_string( $bottom_nav_hover_setting ) ? sanitize_hex_color( $bottom_nav_hover_setting ) : null;
	$mobile_header_height = zola_sanitize_header_height( get_theme_mod( 'zola_mobile_header_height', 70 ) );
	$mobile_overlay_background_setting = get_theme_mod( 'zola_mobile_overlay_background_color', '#0a0a0a' );
	$mobile_overlay_background = is_string( $mobile_overlay_background_setting ) ? sanitize_hex_color( $mobile_overlay_background_setting ) : null;
	$mobile_overlay_text_mode = zola_sanitize_header_text_mode( get_theme_mod( 'zola_mobile_overlay_text_mode', 'light' ) );
	$mobile_overlay_text_color = 'light' === $mobile_overlay_text_mode ? '#ffffff' : 'var(--zola-text)';
	$inline = ':root{--zola-accent:' . ( $accent ? $accent : '#2563eb' ) . ';--zola-ink:' . ( $ink ? $ink : '#202124' ) . ';--zola-dark-bg:' . ( $ink ? $ink : '#202124' ) . ';--zola-paper:' . ( $paper ? $paper : '#ffffff' ) . ';--zola-text:' . ( $text ? $text : '#202126' ) . ';--zola-wrap:' . $site_width . 'px;--zola-content-width:' . $content_width . 'px;--zola-radius:' . $button_radius . 'px;--zola-post-columns:' . $post_columns . ';--zola-portfolio-columns:' . $portfolio_columns . ';--zola-shop-columns:' . $shop_columns . ';--zola-font:' . $font_family . ';--zola-header-width:' . $header_width . ';--zola-header-height:' . $header_height . 'px;--zola-header-background:' . ( $header_background ? $header_background : '#ffffff' ) . ';--zola-header-nav-size:' . $nav_size . 'px;--zola-header-nav-spacing:' . $nav_spacing . 'px;--zola-header-nav-height:' . $nav_height . 'px;--zola-header-nav-push:' . $nav_push . 'px;--zola-header-nav-color:' . ( $nav_color ? $nav_color : 'inherit' ) . ';--zola-header-nav-hover-color:' . ( $nav_hover ? $nav_hover : 'var(--zola-accent)' ) . ';--zola-header-icon-color:' . ( $icon_color ? $icon_color : 'inherit' ) . ';--zola-header-icon-hover-color:' . ( $icon_hover ? $icon_hover : 'var(--zola-accent)' ) . ';--zola-transparent-header-height:' . $transparent_header_height . 'px;--zola-transparent-header-background:' . ( $transparent_background ? $transparent_background : 'transparent' ) . ';}';
	$inline .= ':root{--zola-top-bar-height:' . $top_bar_height . 'px;--zola-top-bar-background:' . ( $top_bar_background ? $top_bar_background : 'var(--zola-dark-bg)' ) . ';--zola-top-bar-nav-height:' . $top_bar_nav_height . 'px;--zola-top-bar-nav-color:' . ( $top_bar_nav_color ? $top_bar_nav_color : 'inherit' ) . ';--zola-top-bar-nav-hover-color:' . ( $top_bar_nav_hover_color ? $top_bar_nav_hover_color : 'var(--zola-accent)' ) . ';}';
	$inline .= ':root{--zola-header-bottom-height:' . $header_bottom_height . 'px;--zola-header-bottom-background:' . ( $header_bottom_background ? $header_bottom_background : 'transparent' ) . ';--zola-header-bottom-nav-size:' . $bottom_nav_size . 'px;--zola-header-bottom-nav-spacing:' . $bottom_nav_spacing . 'px;--zola-header-bottom-nav-height:' . $bottom_nav_height . 'px;--zola-header-bottom-nav-color:' . ( $bottom_nav_color ? $bottom_nav_color : $bottom_nav_base_color ) . ';--zola-header-bottom-nav-hover-color:' . ( $bottom_nav_hover ? $bottom_nav_hover : 'var(--zola-accent)' ) . ';}';
	$inline .= ':root{--zola-mobile-header-height:' . $mobile_header_height . 'px;--zola-mobile-overlay-background:' . ( $mobile_overlay_background ? $mobile_overlay_background : '#0a0a0a' ) . ';--zola-mobile-overlay-color:' . $mobile_overlay_text_color . ';}';
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
	return $classes;
}
add_filter( 'body_class', 'zola_body_classes' );



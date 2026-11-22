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
require_once ZOLA_DIR . '/inc/cookie-consent.php';
require_once ZOLA_DIR . '/inc/internationalization.php';
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

/** Read and validate the typography settings used by the front end. */
function zola_get_typography_settings(): array {
	return array(
		'disable_fonts'       => wp_validate_boolean( get_theme_mod( 'zola_typography_disable_fonts', false ) ),
		'google_fonts_cdn'    => wp_validate_boolean( get_theme_mod( 'zola_typography_google_fonts_cdn', false ) ),
		'base_family'         => zola_sanitize_typography_font_family( get_theme_mod( 'zola_font_family', 'sans' ) ),
		'base_weight'         => zola_sanitize_typography_font_weight( get_theme_mod( 'zola_typography_base_font_weight', 'regular' ) ),
		'base_size'           => zola_sanitize_typography_scale( get_theme_mod( 'zola_typography_base_font_size', 100 ) ),
		'mobile_base_size'    => zola_sanitize_typography_scale( get_theme_mod( 'zola_typography_mobile_base_font_size', 100 ) ),
		'headings_family'     => zola_sanitize_typography_font_family( get_theme_mod( 'zola_typography_headlines_font_family', 'base' ) ),
		'headings_weight'     => zola_sanitize_typography_font_weight( get_theme_mod( 'zola_typography_headlines_font_weight', 'inherit' ) ),
		'navigation_family'   => zola_sanitize_typography_font_family( get_theme_mod( 'zola_typography_navigation_font_family', 'base' ) ),
		'navigation_weight'   => zola_sanitize_typography_font_weight( get_theme_mod( 'zola_typography_navigation_font_weight', 'inherit' ) ),
		'alt_family'          => zola_sanitize_typography_font_family( get_theme_mod( 'zola_typography_alt_font_family', 'base' ) ),
		'alt_weight'          => zola_sanitize_typography_font_weight( get_theme_mod( 'zola_typography_alt_font_weight', 'inherit' ) ),
		'transform_breadcrumbs' => zola_sanitize_typography_transform( get_theme_mod( 'zola_typography_transform_breadcrumbs', 'normal' ) ),
		'transform_buttons'     => zola_sanitize_typography_transform( get_theme_mod( 'zola_typography_transform_buttons', 'normal' ) ),
		'transform_navigation'  => zola_sanitize_typography_transform( get_theme_mod( 'zola_typography_transform_navigation', 'normal' ) ),
		'transform_sections'    => zola_sanitize_typography_transform( get_theme_mod( 'zola_typography_transform_section_titles', 'normal' ) ),
		'transform_widgets'     => zola_sanitize_typography_transform( get_theme_mod( 'zola_typography_transform_widget_titles', 'normal' ) ),
	);
}

function zola_typography_weight_value( string $weight ): string {
	if ( 'regular' === $weight ) {
		return '400';
	}
	return ctype_digit( $weight ) ? $weight : '';
}

function zola_typography_scale_css( int $scale ): string {
	$size = rtrim( rtrim( number_format( 16 * $scale / 100, 2, '.', '' ), '0' ), '.' );
	return $size . 'px';
}

/** Build cached CSS variables and component rules for typography controls. */
function zola_typography_inline_css( array $settings ): string {
	$signature = md5( (string) wp_json_encode( $settings ) );
	if ( ! is_customize_preview() ) {
		$cached = get_transient( 'zola_typography_inline_css' );
		if ( is_array( $cached ) && isset( $cached['signature'], $cached['css'] ) && hash_equals( $cached['signature'], $signature ) ) {
			return (string) $cached['css'];
		}
	}

	$catalog    = zola_typography_font_catalog();
	$base_family = $settings['disable_fonts'] ? $catalog['sans']['css'] : zola_typography_css_family( $settings['base_family'] );
	$heading_family = $settings['disable_fonts'] ? $base_family : zola_typography_css_family( $settings['headings_family'], $settings['base_family'] );
	$navigation_family = $settings['disable_fonts'] ? $base_family : zola_typography_css_family( $settings['navigation_family'], $settings['base_family'] );
	$alt_family = $settings['disable_fonts'] ? $base_family : zola_typography_css_family( $settings['alt_family'], $settings['base_family'] );
	$base_weight = zola_typography_weight_value( $settings['base_weight'] );
	$css = ':root{--zola-font:' . $base_family . ';--zola-headings-font:' . $heading_family . ';--zola-navigation-font:' . $navigation_family . ';--zola-alt-font:' . $alt_family . ';--zola-base-font-weight:' . ( $base_weight ? $base_weight : '400' ) . ';--zola-base-font-size:' . zola_typography_scale_css( $settings['base_size'] ) . ';--zola-transform-breadcrumbs:' . ( 'uppercase' === $settings['transform_breadcrumbs'] ? 'uppercase' : 'none' ) . ';--zola-transform-buttons:' . ( 'uppercase' === $settings['transform_buttons'] ? 'uppercase' : 'none' ) . ';--zola-transform-navigation:' . ( 'uppercase' === $settings['transform_navigation'] ? 'uppercase' : 'none' ) . ';--zola-transform-sections:' . ( 'uppercase' === $settings['transform_sections'] ? 'uppercase' : 'none' ) . ';--zola-transform-widgets:' . ( 'uppercase' === $settings['transform_widgets'] ? 'uppercase' : 'none' ) . ';}';
	$css .= 'body{font-size:var(--zola-base-font-size,16px);font-weight:var(--zola-base-font-weight,400);}';
	$css .= 'body:not(.elementor-page) :is(h1,h2,h3,h4,h5,h6,.entry-title,.page-title,.section-title,.widget-title){font-family:var(--zola-headings-font,var(--zola-font));}';
	$css .= 'body:not(.elementor-page) :is(.site-header-builder .site-nav,.site-header-builder .site-nav a,.zola-menu,.zola-menu a,.mobile-menu a){font-family:var(--zola-navigation-font,var(--zola-font));}';
	$css .= 'body:not(.elementor-page) .alt-font{font-family:var(--zola-alt-font,var(--zola-font));}';
	$heading_weight = zola_typography_weight_value( $settings['headings_weight'] );
	if ( $heading_weight ) {
		$css .= 'body:not(.elementor-page) :is(h1,h2,h3,h4,h5,h6,.entry-title,.page-title,.section-title,.widget-title){font-weight:' . $heading_weight . ';}';
	}
	$navigation_weight = zola_typography_weight_value( $settings['navigation_weight'] );
	if ( $navigation_weight ) {
		$css .= 'body:not(.elementor-page) :is(.site-header-builder .site-nav,.site-header-builder .site-nav a,.zola-menu,.zola-menu a,.mobile-menu a){font-weight:' . $navigation_weight . ';}';
	}
	$alt_weight = zola_typography_weight_value( $settings['alt_weight'] );
	if ( $alt_weight ) {
		$css .= 'body:not(.elementor-page) .alt-font{font-weight:' . $alt_weight . ';}';
	}
	$css .= 'body:not(.elementor-page) :is(.breadcrumb,.breadcrumbs,.woocommerce-breadcrumb){text-transform:var(--zola-transform-breadcrumbs,none);}';
	$css .= 'body:not(.elementor-page) :is(button,input[type=submit],.button,.wp-block-button__link,.site-header-builder__button){text-transform:var(--zola-transform-buttons,none);}';
	$css .= 'body:not(.elementor-page) :is(.site-header-builder .site-nav a,.zola-menu a,.mobile-menu a,.nav-tabs a,.tabs a,.woocommerce-tabs .tabs a){text-transform:var(--zola-transform-navigation,none);}';
	$css .= 'body:not(.elementor-page) :is(.section-title,.page-title,.entry-title){text-transform:var(--zola-transform-sections,none);}';
	$css .= 'body:not(.elementor-page) :is(.widget-title,.widget__title,.widget_block .wp-block-heading){text-transform:var(--zola-transform-widgets,none);}';
	$css .= '@media(max-width:782px){body{font-size:' . zola_typography_scale_css( $settings['mobile_base_size'] ) . ';}}';

	if ( ! is_customize_preview() ) {
		set_transient( 'zola_typography_inline_css', array( 'signature' => $signature, 'css' => $css ), DAY_IN_SECONDS );
	}
	return $css;
}

/** Enqueue selected Google Fonts only after the site owner opts in. */
function zola_enqueue_google_fonts( array $settings ): void {
	if ( $settings['disable_fonts'] || ! $settings['google_fonts_cdn'] ) {
		return;
	}
	$roles = array(
		array( 'family' => $settings['base_family'], 'weight' => $settings['base_weight'] ),
		array( 'family' => $settings['headings_family'], 'weight' => $settings['headings_weight'] ),
		array( 'family' => $settings['navigation_family'], 'weight' => $settings['navigation_weight'] ),
		array( 'family' => $settings['alt_family'], 'weight' => $settings['alt_weight'] ),
	);
	$google_weights = array();
	foreach ( $roles as $role ) {
		$family = 'base' === $role['family'] ? $settings['base_family'] : $role['family'];
		$google = zola_typography_google_family( $family );
		if ( ! $google ) {
			continue;
		}
		$weight = 'inherit' === $role['weight'] ? $settings['base_weight'] : $role['weight'];
		$weight = zola_typography_weight_value( $weight );
		$font_key = sanitize_key( str_replace( ' ', '-', strtolower( $google ) ) );
		$google_weights[ $font_key ]['family'] = $google;
		$google_weights[ $font_key ]['weights'][] = $weight ? $weight : '400';
	}
	foreach ( $google_weights as $font_key => $font ) {
		$weights = array_values( array_unique( $font['weights'] ) );
		$url = 'https://fonts.googleapis.com/css2?family=' . rawurlencode( $font['family'] ) . ':wght@' . implode( ';', $weights ) . '&display=swap';
		wp_enqueue_style( 'zola-google-font-' . $font_key, $url, array(), null );
	}
}

/** Load theme styles and the small interaction layer. */
function zola_enqueue_assets(): void {
	wp_enqueue_style( 'zola-components', ZOLA_URI . '/assets/css/theme.css', array(), (string) filemtime( ZOLA_DIR . '/assets/css/theme.css' ) );
	$typography_settings = zola_get_typography_settings();
	zola_enqueue_google_fonts( $typography_settings );
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

	$accent                   = zola_get_theme_mod_color_value( 'zola_accent_color', '#2563eb' );
	$secondary                = zola_get_theme_mod_color_value( 'zola_secondary_color', '#202124' );
	$success                  = zola_get_theme_mod_color_value( 'zola_success_color', '#198754' );
	$alert                    = zola_get_theme_mod_color_value( 'zola_alert_color', '#dc3545' );
	$ink                      = zola_get_theme_mod_color_value( 'zola_ink_color', '#202124' );
	$paper                    = zola_get_theme_mod_color_value( 'zola_paper_color', '#ffffff' );
	$text                     = zola_get_theme_mod_color_value( 'zola_text_color', '#202126' );
	$headline                 = zola_get_theme_mod_color_value( 'zola_headline_color', '#202126' );
	$divider                  = zola_get_theme_mod_color_value( 'zola_divider_color', '#dcdcde' );
	$link_color               = zola_get_theme_mod_color_value( 'zola_link_color', '#2563eb' );
	$link_hover_color         = zola_get_theme_mod_color_value( 'zola_link_hover_color', '#202124' );
	$widget_link_color        = zola_get_theme_mod_color_value( 'zola_widget_link_color', 'var(--zola-link-color, var(--zola-accent))' );
	$widget_link_hover_color  = zola_get_theme_mod_color_value( 'zola_widget_link_hover_color', 'var(--zola-link-hover-color, var(--zola-ink))' );
	$tooltip_color            = zola_get_theme_mod_color_value( 'zola_tooltip_color', '#ffffff' );
	$tooltip_background_color = zola_get_theme_mod_color_value( 'zola_tooltip_background_color', '#111827' );
	$font_family = $typography_settings['disable_fonts']
		? zola_typography_font_catalog()['sans']['css']
		: zola_typography_css_family( $typography_settings['base_family'] );
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
	$sticky_header_height = zola_sanitize_header_height( get_theme_mod( 'zola_sticky_header_height', 70 ) );
	$sticky_logo_padding = zola_sanitize_logo_padding( get_theme_mod( 'zola_sticky_logo_padding', 0 ) );
	$sticky_nav_height   = zola_sanitize_sticky_nav_height( get_theme_mod( 'zola_sticky_nav_height', 50 ) );
	$dropdown_background_setting = get_theme_mod( 'zola_dropdown_background_color', '' );
	$dropdown_background = is_string( $dropdown_background_setting ) ? sanitize_hex_color( $dropdown_background_setting ) : null;
	$dropdown_border_color_setting = get_theme_mod( 'zola_dropdown_border_color', '' );
	$dropdown_border_color = is_string( $dropdown_border_color_setting ) ? sanitize_hex_color( $dropdown_border_color_setting ) : null;
	$dropdown_content_size = zola_sanitize_dropdown_content_size( get_theme_mod( 'zola_dropdown_content_size', 100 ) );
	$dropdown_width = (int) round( 220 * $dropdown_content_size / 100 );
	$dropdown_radius = absint( zola_sanitize_dropdown_radius( get_theme_mod( 'zola_dropdown_radius', '0' ) ) );
	$dropdown_border_style = wp_validate_boolean( get_theme_mod( 'zola_dropdown_add_border', true ) ) ? 'solid' : 'none';
	$dropdown_shadow = wp_validate_boolean( get_theme_mod( 'zola_dropdown_add_shadow', true ) ) ? '0 12px 28px rgba(0,0,0,.12)' : 'none';
	$dropdown_text_mode = zola_sanitize_header_text_mode( get_theme_mod( 'zola_dropdown_text_color', 'dark' ) );
	$dropdown_text_color = 'light' === $dropdown_text_mode ? '#ffffff' : 'var(--zola-text)';
	$dropdown_hover_background = 'light' === $dropdown_text_mode ? 'rgba(255,255,255,.14)' : 'rgba(0,0,0,.08)';
	$mobile_overlay_background_setting = get_theme_mod( 'zola_mobile_overlay_background_color', '#0a0a0a' );
	$mobile_overlay_background = is_string( $mobile_overlay_background_setting ) ? sanitize_hex_color( $mobile_overlay_background_setting ) : null;
	$mobile_overlay_text_mode = zola_sanitize_header_text_mode( get_theme_mod( 'zola_mobile_overlay_text_mode', 'light' ) );
	$mobile_overlay_text_color = 'light' === $mobile_overlay_text_mode ? '#ffffff' : 'var(--zola-text)';
	$inline = ':root{--zola-accent:' . $accent . ';--zola-secondary:' . $secondary . ';--zola-success:' . $success . ';--zola-alert:' . $alert . ';--zola-ink:' . $ink . ';--zola-dark-bg:' . $ink . ';--zola-paper:' . $paper . ';--zola-text:' . $text . ';--zola-headline:' . $headline . ';--zola-divider:' . $divider . ';--zola-border:' . $divider . ';--zola-link-color:' . $link_color . ';--zola-link-hover-color:' . $link_hover_color . ';--zola-widget-link-color:' . $widget_link_color . ';--zola-widget-link-hover-color:' . $widget_link_hover_color . ';--zola-tooltip-color:' . $tooltip_color . ';--zola-tooltip-background:' . $tooltip_background_color . ';--zola-wrap:' . $site_width . 'px;--zola-content-width:' . $content_width . 'px;--zola-radius:' . $button_radius . 'px;--zola-post-columns:' . $post_columns . ';--zola-portfolio-columns:' . $portfolio_columns . ';--zola-shop-columns:' . $shop_columns . ';--zola-font:' . $font_family . ';--zola-header-width:' . $header_width . ';--zola-header-height:' . $header_height . 'px;--zola-header-background:' . ( $header_background ? $header_background : '#ffffff' ) . ';--zola-header-nav-size:' . $nav_size . 'px;--zola-header-nav-spacing:' . $nav_spacing . 'px;--zola-header-nav-height:' . $nav_height . 'px;--zola-header-nav-push:' . $nav_push . 'px;--zola-header-nav-color:' . ( $nav_color ? $nav_color : 'inherit' ) . ';--zola-header-nav-hover-color:' . ( $nav_hover ? $nav_hover : 'var(--zola-accent)' ) . ';--zola-header-icon-color:' . ( $icon_color ? $icon_color : 'inherit' ) . ';--zola-header-icon-hover-color:' . ( $icon_hover ? $icon_hover : 'var(--zola-accent)' ) . ';--zola-transparent-header-height:' . $transparent_header_height . 'px;--zola-transparent-header-background:' . ( $transparent_background ? $transparent_background : 'transparent' ) . ';}';
	$inline .= ':root{--zola-top-bar-height:' . $top_bar_height . 'px;--zola-top-bar-background:' . ( $top_bar_background ? $top_bar_background : 'var(--zola-dark-bg)' ) . ';--zola-top-bar-nav-height:' . $top_bar_nav_height . 'px;--zola-top-bar-nav-color:' . ( $top_bar_nav_color ? $top_bar_nav_color : 'inherit' ) . ';--zola-top-bar-nav-hover-color:' . ( $top_bar_nav_hover_color ? $top_bar_nav_hover_color : 'var(--zola-accent)' ) . ';}';
	$inline .= ':root{--zola-header-bottom-height:' . $header_bottom_height . 'px;--zola-header-bottom-background:' . ( $header_bottom_background ? $header_bottom_background : 'transparent' ) . ';--zola-header-bottom-nav-size:' . $bottom_nav_size . 'px;--zola-header-bottom-nav-spacing:' . $bottom_nav_spacing . 'px;--zola-header-bottom-nav-height:' . $bottom_nav_height . 'px;--zola-header-bottom-nav-color:' . ( $bottom_nav_color ? $bottom_nav_color : $bottom_nav_base_color ) . ';--zola-header-bottom-nav-hover-color:' . ( $bottom_nav_hover ? $bottom_nav_hover : 'var(--zola-accent)' ) . ';}';
	$inline .= ':root{--zola-mobile-header-height:' . $mobile_header_height . 'px;--zola-mobile-overlay-background:' . ( $mobile_overlay_background ? $mobile_overlay_background : '#0a0a0a' ) . ';--zola-mobile-overlay-color:' . $mobile_overlay_text_color . ';}';
	$inline .= ':root{--zola-sticky-header-height:' . $sticky_header_height . 'px;--zola-sticky-logo-padding:' . $sticky_logo_padding . 'px;--zola-sticky-nav-height:' . $sticky_nav_height . 'px;}';
	$inline .= ':root{--zola-dropdown-width:' . $dropdown_width . 'px;--zola-dropdown-radius:' . $dropdown_radius . 'px;--zola-dropdown-background:' . ( $dropdown_background ? $dropdown_background : 'var(--zola-surface)' ) . ';--zola-dropdown-border-color:' . ( $dropdown_border_color ? $dropdown_border_color : 'var(--zola-border)' ) . ';--zola-dropdown-border-style:' . $dropdown_border_style . ';--zola-dropdown-shadow:' . $dropdown_shadow . ';--zola-dropdown-text-color:' . $dropdown_text_color . ';--zola-dropdown-hover-background:' . $dropdown_hover_background . ';}';
	$inline .= 'body:not(.elementor-page) .site-main :is(h1,h2,h3,h4,h5,h6,.entry-title,.page-title,.section-title,.widget-title):not(.hero h1){color:var(--zola-headline,var(--zola-text));}';
	$inline .= 'body:not(.elementor-page) :is(.site-main,.site-footer) a:not(.button):not(.wp-block-button__link):not(.page-numbers){color:var(--zola-link-color,var(--zola-accent));}';
	$inline .= 'body:not(.elementor-page) :is(.site-main,.site-footer) a:not(.button):not(.wp-block-button__link):not(.page-numbers):hover,body:not(.elementor-page) :is(.site-main,.site-footer) a:not(.button):not(.wp-block-button__link):not(.page-numbers):focus-visible{color:var(--zola-link-hover-color,var(--zola-ink));}';
	$inline .= 'body:not(.elementor-page) :is(.site-main,.site-footer) .widget a:not(.button):not(.wp-block-button__link):not(.page-numbers){color:var(--zola-widget-link-color,var(--zola-link-color,var(--zola-accent)));}';
	$inline .= 'body:not(.elementor-page) :is(.site-main,.site-footer) .widget a:not(.button):not(.wp-block-button__link):not(.page-numbers):hover,body:not(.elementor-page) :is(.site-main,.site-footer) .widget a:not(.button):not(.wp-block-button__link):not(.page-numbers):focus-visible{color:var(--zola-widget-link-hover-color,var(--zola-link-hover-color,var(--zola-ink)));}';
	$inline .= ':is(hr,.wp-block-separator){border-color:var(--zola-divider,var(--zola-border));}';
	$inline .= ':is([role=tooltip],.tooltip,.tooltip-inner,.tippy-box,.tooltipster-box){color:var(--zola-tooltip-color,#fff);background-color:var(--zola-tooltip-background,#111827);}';
	$inline .= zola_typography_inline_css( $typography_settings );
	$custom_css        = zola_sanitize_custom_css( get_theme_mod( 'zola_custom_css', '' ) );
	$tablet_custom_css = zola_sanitize_custom_css( get_theme_mod( 'zola_custom_tablet_css', '' ) );
	$mobile_custom_css = zola_sanitize_custom_css( get_theme_mod( 'zola_custom_mobile_css', '' ) );
	$inline .= $custom_css;
	if ( '' !== $tablet_custom_css ) {
		$inline .= '@media (min-width:783px) and (max-width:1024px){' . $tablet_custom_css . '}';
	}
	if ( '' !== $mobile_custom_css ) {
		$inline .= '@media (max-width:782px){' . $mobile_custom_css . '}';
	}
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
	if ( ! zola_is_sticky_header_enabled() ) {
		$classes[] = 'zola-nonsticky-header';
	}
	$dropdown_link_style = zola_sanitize_dropdown_link_style( get_theme_mod( 'zola_dropdown_link_style', 'divider' ) );
	$classes[] = 'zola-dropdown-link-style--' . sanitize_html_class( $dropdown_link_style );
	$dropdown_text_style = zola_sanitize_dropdown_text_style( get_theme_mod( 'zola_dropdown_text_style', 'simple' ) );
	if ( 'uppercase' === $dropdown_text_style ) {
		$classes[] = 'zola-dropdown-text--uppercase';
	}
	if ( wp_validate_boolean( get_theme_mod( 'zola_dropdown_add_arrow', true ) ) ) {
		$classes[] = 'zola-dropdown-has-arrow';
	}
	return $classes;
}
add_filter( 'body_class', 'zola_body_classes' );



<?php
/** Theme-specific Customizer controls. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Sanitize bounded numeric settings used by theme CSS and templates. */
function zola_sanitize_site_width( mixed $value ): int {
	return min( 1680, max( 960, absint( $value ) ) );
}

function zola_sanitize_content_width( mixed $value ): int {
	return min( 1100, max( 560, absint( $value ) ) );
}

function zola_sanitize_excerpt_length( mixed $value ): int {
	return min( 60, max( 8, absint( $value ) ) );
}

function zola_sanitize_columns( mixed $value ): int {
	$value = absint( $value );
	return in_array( $value, array( 2, 3, 4 ), true ) ? $value : 3;
}

function zola_sanitize_blog_layout( mixed $value ): string {
	return in_array( $value, array( 'grid', 'list', 'cover' ), true ) ? $value : 'grid';
}

function zola_sanitize_header_width( mixed $value ): string {
	return in_array( $value, array( 'contained', 'full' ), true ) ? $value : 'contained';
}

function zola_sanitize_header_text_mode( mixed $value ): string {
	if ( 'auto' === $value ) {
		return 'dark';
	}
	return in_array( $value, array( 'light', 'dark' ), true ) ? $value : 'dark';
}

function zola_sanitize_header_nav_style( mixed $value ): string {
	$styles = array( 'plain', 'underline', 'overline', 'divider', 'pills', 'fill', 'outline', 'bold', 'compact', 'highlight', 'vertical', 'tabs', 'double', 'shadow', 'chevron' );
	return in_array( $value, $styles, true ) ? $value : 'plain';
}

function zola_header_nav_style_choices(): array {
	return array(
		'plain'     => esc_html__( 'Plain', 'zola' ),
		'divider'   => esc_html__( 'Divider', 'zola' ),
		'compact'   => esc_html__( 'Compact', 'zola' ),
		'overline'  => esc_html__( 'Overline', 'zola' ),
		'underline' => esc_html__( 'Underline', 'zola' ),
		'highlight' => esc_html__( 'Highlight', 'zola' ),
		'pills'     => esc_html__( 'Pills', 'zola' ),
		'fill'      => esc_html__( 'Fill', 'zola' ),
		'vertical'  => esc_html__( 'Vertical', 'zola' ),
		'outline'   => esc_html__( 'Outline', 'zola' ),
		'bold'      => esc_html__( 'Bold', 'zola' ),
		'tabs'      => esc_html__( 'Tabs', 'zola' ),
		'double'    => esc_html__( 'Double Line', 'zola' ),
		'shadow'    => esc_html__( 'Shadow', 'zola' ),
		'chevron'   => esc_html__( 'Chevron', 'zola' ),
	);
}

function zola_sanitize_header_nav_scale( mixed $value ): string {
	return in_array( $value, array( 'xs', 's', 'default', 'm', 'l', 'xl' ), true ) ? $value : 'default';
}

function zola_sanitize_header_height( mixed $value ): int {
	return min( 140, max( 48, absint( $value ) ) );
}

function zola_sanitize_header_nav_height( mixed $value ): int {
	return min( 40, max( 12, absint( $value ) ) );
}

function zola_sanitize_header_nav_push( mixed $value ): int {
	return min( 40, max( 0, absint( $value ) ) );
}

function zola_sanitize_top_bar_height( mixed $value ): int {
	return min( 100, max( 24, absint( $value ) ) );
}

function zola_sanitize_header_bottom_height( mixed $value ): int {
	return min( 100, max( 10, absint( $value ) ) );
}

function zola_sanitize_logo_position( mixed $value ): string {
	return in_array( $value, array( 'left', 'center' ), true ) ? $value : 'left';
}

function zola_sanitize_logo_container_width( mixed $value ): int {
	return min( 100, max( 20, absint( $value ) ) );
}

function zola_sanitize_logo_max_width( mixed $value ): string {
	if ( ! is_scalar( $value ) ) {
		return '';
	}

	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	return (string) min( 1200, max( 24, absint( $value ) ) );
}

function zola_sanitize_logo_padding( mixed $value ): int {
	return min( 80, max( 0, absint( $value ) ) );
}

function zola_sanitize_logo_link( mixed $value ): string {
	if ( ! is_scalar( $value ) ) {
		return '';
	}

	return esc_url_raw( trim( (string) $value ) );
}

/** Return the starter positions for the drag-and-drop header builder. */
function zola_header_builder_default_layout(): array {
	$desktop_rows = array(
			'top'    => array( 'left' => array(), 'center' => array( 'announcement' ), 'right' => array() ),
			'main'   => array( 'left' => array( 'logo' ), 'center' => array( 'menu' ), 'right' => array( 'button' ) ),
			'bottom' => array( 'left' => array(), 'center' => array(), 'right' => array() ),
	);
	$mobile_rows = array(
		'top'    => array( 'left' => array(), 'center' => array( 'announcement' ), 'right' => array() ),
		'main'   => array( 'left' => array( 'logo' ), 'center' => array(), 'right' => array( 'menu' ) ),
		'bottom' => array( 'left' => array(), 'center' => array(), 'right' => array() ),
	);

	return array(
		'desktop' => array( 'rows' => $desktop_rows ),
		'mobile'  => array( 'rows' => $mobile_rows ),
	);
}

/** Normalize the three rows used by a saved Header Preset. */
function zola_header_builder_make_rows( array $top = array(), array $main = array(), array $bottom = array() ): array {
	$normalize_row = static function ( array $row ): array {
		return array(
			'left'   => isset( $row['left'] ) && is_array( $row['left'] ) ? $row['left'] : array(),
			'center' => isset( $row['center'] ) && is_array( $row['center'] ) ? $row['center'] : array(),
			'right'  => isset( $row['right'] ) && is_array( $row['right'] ) ? $row['right'] : array(),
		);
	};

	return array(
		'top'    => $normalize_row( $top ),
		'main'   => $normalize_row( $main ),
		'bottom' => $normalize_row( $bottom ),
	);
}

/** Header layouts offered in the visual Header Presets gallery. */
function zola_header_builder_presets(): array {
	return array(
		array(
			'id'          => 'classic',
			'title'       => esc_html__( 'Classic', 'zola' ),
			'description' => esc_html__( 'Logo, navigation and call to action.', 'zola' ),
			'desktop'     => zola_header_builder_make_rows(
				array( 'left' => array( 'announcement' ), 'right' => array( 'social' ) ),
				array( 'left' => array( 'logo' ), 'center' => array( 'menu' ), 'right' => array( 'button' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array( 'center' => array( 'announcement' ) ),
				array( 'left' => array( 'logo' ), 'right' => array( 'menu' ) )
			),
		),
		array(
			'id'          => 'centered',
			'title'       => esc_html__( 'Centered Logo', 'zola' ),
			'description' => esc_html__( 'Place the logo in the middle with navigation below.', 'zola' ),
			'desktop'     => zola_header_builder_make_rows(
				array(),
				array( 'left' => array( 'search' ), 'center' => array( 'logo' ), 'right' => array( 'account', 'cart' ) ),
				array( 'center' => array( 'menu' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array(),
				array( 'left' => array( 'menu' ), 'center' => array( 'logo' ), 'right' => array( 'search' ) )
			),
		),
		array(
			'id'          => 'split',
			'title'       => esc_html__( 'Split Navigation', 'zola' ),
			'description' => esc_html__( 'Keep the logo at the left and navigation at the right.', 'zola' ),
			'desktop'     => zola_header_builder_make_rows(
				array( 'left' => array( 'announcement' ), 'right' => array( 'social' ) ),
				array( 'left' => array( 'logo' ), 'right' => array( 'menu' ) ),
				array( 'center' => array( 'button' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array( 'center' => array( 'announcement' ) ),
				array( 'left' => array( 'logo' ), 'right' => array( 'menu' ) ),
				array( 'right' => array( 'button' ) )
			),
		),
		array(
			'id'          => 'search',
			'title'       => esc_html__( 'Search Header', 'zola' ),
			'description' => esc_html__( 'Give the search field a central place in the header.', 'zola' ),
			'desktop'     => zola_header_builder_make_rows(
				array(),
				array( 'left' => array( 'logo' ), 'center' => array( 'search' ), 'right' => array( 'account', 'cart' ) ),
				array( 'left' => array( 'menu' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array(),
				array( 'left' => array( 'logo' ), 'right' => array( 'search', 'menu' ) ),
				array( 'center' => array( 'cart' ) )
			),
		),
		array(
			'id'          => 'store',
			'title'       => esc_html__( 'Store Header', 'zola' ),
			'description' => esc_html__( 'Show account and cart links in a slim top row.', 'zola' ),
			'desktop'     => zola_header_builder_make_rows(
				array( 'right' => array( 'account', 'cart' ) ),
				array( 'left' => array( 'logo' ), 'center' => array( 'menu' ), 'right' => array( 'search' ) ),
				array( 'left' => array( 'announcement' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array( 'center' => array( 'announcement' ) ),
				array( 'left' => array( 'logo' ), 'right' => array( 'menu' ) ),
				array( 'right' => array( 'cart' ) )
			),
		),
		array(
			'id'          => 'double',
			'title'       => esc_html__( 'Two Row Navigation', 'zola' ),
			'description' => esc_html__( 'Separate the main navigation into its own row.', 'zola' ),
			'desktop'     => zola_header_builder_make_rows(
				array(),
				array( 'left' => array( 'logo' ), 'center' => array( 'search' ), 'right' => array( 'account', 'cart' ) ),
				array( 'center' => array( 'menu' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array(),
				array( 'left' => array( 'logo' ), 'right' => array( 'menu' ) ),
				array( 'center' => array( 'search' ) )
			),
		),
		array(
			'id'          => 'action',
			'title'       => esc_html__( 'Call to Action', 'zola' ),
			'description' => esc_html__( 'Highlight an action button beside the navigation.', 'zola' ),
			'desktop'     => zola_header_builder_make_rows(
				array( 'center' => array( 'announcement' ) ),
				array( 'left' => array( 'logo' ), 'center' => array( 'menu' ), 'right' => array( 'button' ) ),
				array( 'right' => array( 'social' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array( 'center' => array( 'announcement' ) ),
				array( 'left' => array( 'logo' ), 'right' => array( 'menu' ) ),
				array( 'right' => array( 'button' ) )
			),
		),
		array(
			'id'          => 'secondary',
			'title'       => esc_html__( 'Utility Top Bar', 'zola' ),
			'description' => esc_html__( 'Keep account and cart links in a compact utility row.', 'zola' ),
			'desktop'     => zola_header_builder_make_rows(
				array( 'right' => array( 'account', 'cart' ) ),
				array( 'left' => array( 'logo' ), 'center' => array( 'menu' ), 'right' => array( 'button' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array(),
				array( 'left' => array( 'logo' ), 'right' => array( 'menu' ) ),
				array( 'center' => array( 'cart' ) )
			),
		),
		array(
			'id'          => 'brand-search',
			'title'       => esc_html__( 'Brand + Search', 'zola' ),
			'description' => esc_html__( 'Place a search field beside the logo and navigation.', 'zola' ),
			'desktop'     => zola_header_builder_make_rows(
				array(),
				array( 'left' => array( 'logo' ), 'center' => array( 'search' ), 'right' => array( 'button' ) ),
				array( 'left' => array( 'menu' ), 'right' => array( 'account', 'cart' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array(),
				array( 'left' => array( 'logo' ), 'right' => array( 'menu' ) ),
				array( 'center' => array( 'search' ) )
			),
		),
		array(
			'id'          => 'minimal',
			'title'       => esc_html__( 'Minimal', 'zola' ),
			'description' => esc_html__( 'A clean single row with only logo and navigation.', 'zola' ),
			'desktop'     => zola_header_builder_make_rows(
				array(),
				array( 'left' => array( 'logo' ), 'right' => array( 'menu' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array(),
				array( 'left' => array( 'logo' ), 'right' => array( 'menu' ) )
			),
		),
	);
}

/** Validate and normalize a saved header builder layout. */
function zola_sanitize_header_builder_layout( mixed $value ): string {
	if ( is_string( $value ) ) {
		$decoded = json_decode( wp_unslash( $value ), true );
		$value   = is_array( $decoded ) ? $decoded : array();
	}

	$allowed_modules = array( 'logo', 'menu', 'button', 'search', 'announcement', 'social', 'html_1', 'html_2', 'html_3', 'html_4', 'html_5', 'account', 'cart' );
	$defaults       = zola_header_builder_default_layout();
	$is_legacy      = isset( $value['rows'] ) && is_array( $value['rows'] );
	$layout         = array();
	foreach ( array( 'desktop', 'mobile' ) as $device ) {
		$device_rows = $is_legacy && 'desktop' === $device
			? $value['rows']
			: ( isset( $value[ $device ]['rows'] ) && is_array( $value[ $device ]['rows'] ) ? $value[ $device ]['rows'] : array() );
		$layout[ $device ] = array( 'rows' => array() );
		$used_modules      = array();
		foreach ( array( 'top', 'main', 'bottom' ) as $row ) {
			$layout[ $device ]['rows'][ $row ] = array();
			foreach ( array( 'left', 'center', 'right' ) as $column ) {
				$items = isset( $device_rows[ $row ][ $column ] ) && is_array( $device_rows[ $row ][ $column ] ) ? $device_rows[ $row ][ $column ] : $defaults[ $device ]['rows'][ $row ][ $column ];
				$layout[ $device ]['rows'][ $row ][ $column ] = array();
				foreach ( array_slice( $items, 0, 9 ) as $item ) {
					$module = is_string( $item ) ? sanitize_key( $item ) : '';
					if ( 'html' === $module ) {
						$module = 'html_1';
					}
					if ( ! in_array( $module, $allowed_modules, true ) || isset( $used_modules[ $module ] ) ) {
						continue;
					}
					$layout[ $device ]['rows'][ $row ][ $column ][] = $module;
					$used_modules[ $module ] = true;
				}
			}
		}
	}

	return (string) wp_json_encode( $layout );
}

/** Get the normalized saved header builder layout. */
function zola_get_header_builder_layout(): array {
	$saved  = get_theme_mod( 'zola_header_builder_layout', wp_json_encode( zola_header_builder_default_layout() ) );
	$layout = json_decode( zola_sanitize_header_builder_layout( $saved ), true );
	return is_array( $layout ) ? $layout : zola_header_builder_default_layout();
}

/** Check whether a header builder element is placed on a selected device layout. */
function zola_header_builder_has_module( string $module, string $device = 'desktop' ): bool {
	$layout = zola_get_header_builder_layout();
	if ( ! in_array( $device, array( 'desktop', 'mobile' ), true ) ) {
		return false;
	}
	$rows = isset( $layout[ $device ]['rows'] ) && is_array( $layout[ $device ]['rows'] ) ? $layout[ $device ]['rows'] : array();
	foreach ( array( 'top', 'main', 'bottom' ) as $row ) {
		foreach ( array( 'left', 'center', 'right' ) as $column ) {
			$items = isset( $rows[ $row ][ $column ] ) && is_array( $rows[ $row ][ $column ] ) ? $rows[ $row ][ $column ] : array();
			if ( in_array( $module, $items, true ) ) {
				return true;
			}
		}
	}
	return false;
}

/** Add a setting and its matching Customizer control from a small definition. */
function zola_add_customizer_control( WP_Customize_Manager $wp_customize, string $section_id, string $setting_id, array $control ): void {
	$wp_customize->add_setting( $setting_id, array(
		'default'           => $control['default'],
		'sanitize_callback' => $control['sanitize'],
		'transport'         => 'refresh',
	) );

	$control_args = array(
		'label'       => $control['label'],
		'description' => isset( $control['description'] ) ? $control['description'] : '',
		'section'     => $section_id,
		'type'        => $control['type'],
	);
	if ( isset( $control['choices'] ) ) {
		$control_args['choices'] = $control['choices'];
	}
	if ( isset( $control['input_attrs'] ) ) {
		$control_args['input_attrs'] = $control['input_attrs'];
	}
	if ( isset( $control['priority'] ) ) {
		$control_args['priority'] = absint( $control['priority'] );
	}

	if ( 'color' === $control['type'] ) {
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, $control_args ) );
	} else {
		$wp_customize->add_control( $setting_id, $control_args );
	}
}

/** Add a compact visual radio control for header layout choices. */
function zola_add_header_visual_control( WP_Customize_Manager $wp_customize, string $setting_id, array $control ): void {
	$wp_customize->add_setting( $setting_id, array(
		'default'           => $control['default'],
		'sanitize_callback' => $control['sanitize'],
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( new Zola_Header_Visual_Customize_Control( $wp_customize, $setting_id . '_visual_control', array(
		'settings'    => $setting_id,
		'section'     => isset( $control['section'] ) ? $control['section'] : 'zola_header',
		'label'       => $control['label'],
		'description' => isset( $control['description'] ) ? $control['description'] : '',
		'choices'     => $control['choices'],
		'visual_type' => $control['visual_type'],
		'priority'    => absint( $control['priority'] ),
	) ) );
}

/** Add a visual heading between related Header Main controls. */
function zola_add_header_group_control( WP_Customize_Manager $wp_customize, string $control_id, string $label, int $priority, string $section_id = 'zola_header' ): void {
	$wp_customize->add_control( new Zola_Header_Group_Customize_Control( $wp_customize, $control_id, array(
		'settings' => array(),
		'section'  => $section_id,
		'label'    => $label,
		'priority' => $priority,
	) ) );
}

function zola_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_panel( 'zola_header_panel', array(
		'title'    => esc_html__( 'Header', 'zola' ),
		'priority' => 30,
	) );

	$sections = array(
		'zola_header_presets' => array(
			'title'       => esc_html__( 'Presets', 'zola' ),
			'description' => esc_html__( 'Choose a layout for desktop or mobile, then arrange its elements in Header Builder.', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 5,
		),
		'zola_header' => array(
			'title'       => esc_html__( 'Header Main', 'zola' ),
			'description' => esc_html__( 'Arrange the main header layout with Header Builder. The builder opens in the preview area.', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 30,
		),
		'zola_header_top_bar' => array(
			'title'       => esc_html__( 'Top Bar', 'zola' ),
			'description' => esc_html__( 'Set the Top Bar layout, colors and navigation. Top Bar navigation settings are independent from Header Main.', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 20,
		),
		'zola_header_bottom' => array(
			'title'       => esc_html__( 'Header Bottom', 'zola' ),
			'description' => esc_html__( 'Set the bottom row layout and navigation style. Arrange its elements in Header Builder.', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 40,
		),
		'zola_header_mobile' => array(
			'title'       => esc_html__( 'Header Mobile Menu / Overlay', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 50,
		),
		'zola_header_sticky' => array(
			'title'       => esc_html__( 'Sticky Header', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 60,
		),
		'zola_header_dropdown' => array(
			'title'       => esc_html__( 'Dropdown Style', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 70,
		),
		'zola_header_buttons' => array(
			'title'       => esc_html__( 'Buttons', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 80,
		),
		'zola_header_account' => array(
			'title'       => esc_html__( 'Account', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 90,
		),
		'zola_header_cart' => array(
			'title'       => esc_html__( 'Cart', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 100,
		),
		'zola_header_search' => array(
			'title'       => esc_html__( 'Search', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 110,
		),
		'zola_header_html' => array(
			'title'       => esc_html__( 'HTML', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 120,
		),
		'zola_header_follow_icons' => array(
			'title'       => esc_html__( 'Follow Icons', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 160,
		),
		'zola_header_contact' => array(
			'title'       => esc_html__( 'Contact', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 130,
		),
		'zola_header_newsletter' => array(
			'title'       => esc_html__( 'Newsletter', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 140,
		),
		'zola_header_vertical_menu' => array(
			'title'       => esc_html__( 'Vertical Menu', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 150,
		),
		'zola_style' => array(
			'title'       => esc_html__( 'Style', 'zola' ),
			'description' => esc_html__( 'Set the shared colors, typography and button shape used across the theme.', 'zola' ),
			'priority'    => 40,
		),
		'zola_blog' => array(
			'title'       => esc_html__( 'Blog', 'zola' ),
			'description' => esc_html__( 'Choose how posts appear in blog, search and category listings.', 'zola' ),
			'priority'    => 50,
		),
		'zola_layout' => array(
			'title'       => esc_html__( 'Layout', 'zola' ),
			'description' => esc_html__( 'Set the shared site and reading widths used by theme templates.', 'zola' ),
			'priority'    => 60,
		),
		'zola_footer' => array(
			'title'       => esc_html__( 'Footer', 'zola' ),
			'description' => esc_html__( 'Build the Footer in Elementor and assign a saved template in Zola → Site Builder.', 'zola' ),
			'priority'    => 70,
		),
		'zola_pages' => array(
			'title'       => esc_html__( 'Pages', 'zola' ),
			'description' => esc_html__( 'Control the title block shown on standard WordPress pages. Elementor pages keep their own layout.', 'zola' ),
			'priority'    => 80,
		),
		'zola_portfolio' => array(
			'title'       => esc_html__( 'Portfolio', 'zola' ),
			'description' => esc_html__( 'These archive settings apply to portfolio or project post types registered by your portfolio plugin.', 'zola' ),
			'priority'    => 90,
		),
		'zola_share' => array(
			'title'       => esc_html__( 'Share', 'zola' ),
			'description' => esc_html__( 'Choose which sharing links appear on standard blog posts.', 'zola' ),
			'priority'    => 130,
		),
	);

	foreach ( $sections as $section_id => $section ) {
		$wp_customize->add_section( $section_id, $section );
	}

	$wp_customize->add_setting( 'zola_header_builder_layout', array(
		'default'           => wp_json_encode( zola_header_builder_default_layout() ),
		'sanitize_callback' => 'zola_sanitize_header_builder_layout',
		'transport'         => 'refresh',
	) );
	if ( ! class_exists( 'Zola_Header_Presets_Customize_Control' ) ) {
		class Zola_Header_Presets_Customize_Control extends WP_Customize_Control {
			public $type = 'zola-header-presets';

			public function render_content(): void {
				?>
				<div class="zola-header-presets-control">
					<div data-zola-header-presets-ui></div>
				</div>
				<?php
			}
		}
	}
	$wp_customize->add_control( new Zola_Header_Presets_Customize_Control( $wp_customize, 'zola_header_presets_control', array(
		'settings'    => 'zola_header_builder_layout',
		'section'     => 'zola_header_presets',
	) ) );
	if ( ! class_exists( 'Zola_Header_Note_Customize_Control' ) ) {
		class Zola_Header_Note_Customize_Control extends WP_Customize_Control {
			public $type = 'zola-header-note';
			public $message = '';

			public function render_content(): void {
				?><p class="description"><?php echo esc_html( $this->message ); ?></p><?php
			}
		}
	}
	if ( ! class_exists( 'Zola_Header_Group_Customize_Control' ) ) {
		class Zola_Header_Group_Customize_Control extends WP_Customize_Control {
			public $type = 'zola-header-group';

			public function render_content(): void {
				printf( '<h3 class="zola-header-group__title">%s</h3>', esc_html( $this->label ) );
			}
		}
	}
	if ( ! class_exists( 'Zola_Header_Visual_Customize_Control' ) ) {
		class Zola_Header_Visual_Customize_Control extends WP_Customize_Control {
			public $type = 'zola-header-visual';
			public $visual_type = 'nav-style';

			public function render_content(): void {
				$setting_id = $this->setting instanceof WP_Customize_Setting ? $this->setting->id : '';
				$current_value = $this->value();
				if ( 'text-color' === $this->visual_type && 'auto' === $current_value ) {
					$current_value = 'dark';
				}
				?>
				<fieldset class="zola-header-visual-choice zola-header-visual-choice--<?php echo esc_attr( $this->visual_type ); ?>" data-zola-visual-choice data-zola-setting="<?php echo esc_attr( $setting_id ); ?>">
					<legend class="customize-control-title"><?php echo esc_html( $this->label ); ?></legend>
					<?php if ( $this->description ) : ?><span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span><?php endif; ?>
					<div class="zola-header-visual-choice__options">
						<?php foreach ( $this->choices as $value => $label ) : ?>
							<label class="zola-header-visual-choice__option zola-header-visual-choice__option--<?php echo esc_attr( sanitize_html_class( $value ) ); ?>" title="<?php echo esc_attr( $label ); ?>">
								<input type="radio" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $current_value, $value ); ?> data-zola-visual-choice-input>
								<?php if ( 'scale' === $this->visual_type ) : ?>
									<span class="zola-header-visual-choice__segment"><?php echo esc_html( $label ); ?></span>
								<?php else : ?>
								<span class="zola-header-visual-choice__sample" aria-hidden="true">
									<?php if ( 'header-width' === $this->visual_type ) : ?>
										<span class="zola-header-visual-choice__page"><i></i></span>
									<?php elseif ( 'text-color' === $this->visual_type ) : ?>
										<span class="zola-header-visual-choice__abc">Abc</span>
									<?php else : ?>
										<span class="zola-header-visual-choice__nav zola-header-visual-choice__nav--<?php echo esc_attr( sanitize_html_class( $value ) ); ?>">
											NAV
										</span>
									<?php endif; ?>
								</span>
									<span class="zola-header-visual-choice__label"><?php echo esc_html( $label ); ?></span>
								<?php endif; ?>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
				<?php
			}
		}
	}
	$header_section_notes = array(
		'zola_header'          => esc_html__( 'Use the Header Builder in the preview area to add, remove and position header elements.', 'zola' ),
		'zola_header_bottom'   => esc_html__( 'Use Header Builder in the preview area to arrange elements in the bottom row.', 'zola' ),
		'zola_header_mobile'   => esc_html__( 'Choose Mobile / Tablet in Header Builder to arrange the mobile menu and header elements.', 'zola' ),
		'zola_header_dropdown' => esc_html__( 'Dropdown menus open on hover or keyboard focus on desktop, and can be opened by tap on mobile.', 'zola' ),
		'zola_header_account'  => esc_html__( 'Add or remove the Account element and choose its position in Header Builder.', 'zola' ),
		'zola_header_cart'     => esc_html__( 'Add or remove the Cart element and choose its position in Header Builder. Cart requires WooCommerce.', 'zola' ),
		'zola_header_search'   => esc_html__( 'Add or remove the Search element and choose its position in Header Builder.', 'zola' ),
		'zola_header_contact' => esc_html__( 'The Contact element is planned for a later Header Builder step.', 'zola' ),
		'zola_header_newsletter' => esc_html__( 'The Newsletter element is planned for a later Header Builder step.', 'zola' ),
		'zola_header_vertical_menu' => esc_html__( 'The Vertical Menu element is planned for a later Header Builder step.', 'zola' ),
	);
	foreach ( $header_section_notes as $section_id => $message ) {
		$control_id = $section_id . '_note';
		$wp_customize->add_control( new Zola_Header_Note_Customize_Control( $wp_customize, $control_id, array(
			'settings' => array(),
			'section'  => $section_id,
			'message'  => $message,
			'priority' => 'zola_header' === $section_id ? 1 : 10,
		) ) );
	}

	// Header Main settings are kept in this one section alongside its Header Builder.
	zola_add_header_group_control( $wp_customize, 'zola_header_layout_group', esc_html__( 'Layout', 'zola' ), 5 );
	zola_add_header_visual_control( $wp_customize, 'zola_header_width', array(
		'label'       => esc_html__( 'Header Width', 'zola' ),
		'default'     => 'contained',
		'sanitize'    => 'zola_sanitize_header_width',
		'visual_type' => 'header-width',
		'choices'     => array( 'contained' => esc_html__( 'Contained', 'zola' ), 'full' => esc_html__( 'Full Width', 'zola' ) ),
		'priority'    => 6,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_header_height', array(
		'label'      => esc_html__( 'Height', 'zola' ),
		'type'       => 'range',
		'default'    => 70,
		'sanitize'   => 'zola_sanitize_header_height',
		'input_attrs' => array( 'min' => 48, 'max' => 140, 'step' => 1 ),
		'priority'   => 7,
	) );
	zola_add_header_visual_control( $wp_customize, 'zola_header_text_mode', array(
		'label'       => esc_html__( 'Text color', 'zola' ),
		'default'     => 'dark',
		'sanitize'    => 'zola_sanitize_header_text_mode',
		'visual_type' => 'text-color',
		'choices'     => array( 'light' => esc_html__( 'Light', 'zola' ), 'dark' => esc_html__( 'Dark', 'zola' ) ),
		'priority'    => 8,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_header_background_color', array(
		'label'    => esc_html__( 'Background Color', 'zola' ),
		'type'     => 'color',
		'default'  => '#ffffff',
		'sanitize' => 'sanitize_hex_color',
		'priority' => 9,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_header_shadow', array(
		'label'    => esc_html__( 'Add Shadow', 'zola' ),
		'type'     => 'checkbox',
		'default'  => false,
		'sanitize' => 'wp_validate_boolean',
		'priority' => 10,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_header_divider', array(
		'label'    => esc_html__( 'Add Divider', 'zola' ),
		'type'     => 'checkbox',
		'default'  => true,
		'sanitize' => 'wp_validate_boolean',
		'priority' => 11,
	) );

	zola_add_header_group_control( $wp_customize, 'zola_header_navigation_group', esc_html__( 'Navigation', 'zola' ), 20 );
	zola_add_header_visual_control( $wp_customize, 'zola_header_nav_style', array(
		'label'       => esc_html__( 'Navigation Style', 'zola' ),
		'default'     => 'plain',
		'sanitize'    => 'zola_sanitize_header_nav_style',
		'visual_type' => 'nav-style',
		'choices'     => zola_header_nav_style_choices(),
		'priority'    => 21,
	) );
	$nav_scales = array(
		'xs'      => 'XS',
		's'       => 'S',
		'default' => esc_html__( 'Default', 'zola' ),
		'm'       => 'M',
		'l'       => 'L',
		'xl'      => 'XL',
	);
	zola_add_header_visual_control( $wp_customize, 'zola_header_nav_size', array(
		'label'       => esc_html__( 'Nav Size', 'zola' ),
		'default'     => 'default',
		'sanitize'    => 'zola_sanitize_header_nav_scale',
		'visual_type' => 'scale',
		'choices'     => $nav_scales,
		'priority'    => 22,
	) );
	zola_add_header_visual_control( $wp_customize, 'zola_header_nav_spacing', array(
		'label'       => esc_html__( 'Nav Spacing', 'zola' ),
		'default'     => 'default',
		'sanitize'    => 'zola_sanitize_header_nav_scale',
		'visual_type' => 'scale',
		'choices'     => $nav_scales,
		'priority'    => 23,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_header_nav_uppercase', array(
		'label'    => esc_html__( 'Uppercase', 'zola' ),
		'type'     => 'checkbox',
		'default'  => false,
		'sanitize' => 'wp_validate_boolean',
		'priority' => 24,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_header_nav_overlay', array(
		'label'       => esc_html__( 'Add overlay on hover', 'zola' ),
		'description' => esc_html__( 'Show a subtle background when a Header Main navigation item is hovered or focused.', 'zola' ),
		'type'        => 'checkbox',
		'default'     => false,
		'sanitize'    => 'wp_validate_boolean',
		'priority'    => 25,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_header_nav_height', array(
		'label'       => esc_html__( 'Nav Height', 'zola' ),
		'type'        => 'range',
		'default'     => 16,
		'sanitize'    => 'zola_sanitize_header_nav_height',
		'input_attrs' => array( 'min' => 12, 'max' => 40, 'step' => 1 ),
		'priority'    => 26,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_header_nav_push', array(
		'label'       => esc_html__( 'Nav Push', 'zola' ),
		'type'        => 'range',
		'default'     => 9,
		'sanitize'    => 'zola_sanitize_header_nav_push',
		'input_attrs' => array( 'min' => 0, 'max' => 40, 'step' => 1 ),
		'priority'    => 27,
	) );
	foreach ( array(
		'zola_header_nav_color'        => array( 'label' => esc_html__( 'Nav Color', 'zola' ), 'priority' => 28 ),
		'zola_header_nav_hover_color'  => array( 'label' => esc_html__( 'Nav Color :hover', 'zola' ), 'priority' => 29 ),
		'zola_header_icon_color'       => array( 'label' => esc_html__( 'Icons Color', 'zola' ), 'priority' => 30 ),
		'zola_header_icon_hover_color' => array( 'label' => esc_html__( 'Icons Color :hover', 'zola' ), 'priority' => 31 ),
	) as $setting_id => $color_control ) {
		zola_add_customizer_control( $wp_customize, 'zola_header', $setting_id, array(
			'label'    => $color_control['label'],
			'type'     => 'color',
			'default'  => '',
			'sanitize' => 'sanitize_hex_color',
			'priority' => $color_control['priority'],
		) );
	}

	zola_add_header_group_control( $wp_customize, 'zola_header_transparent_group', esc_html__( 'Transparent Header', 'zola' ), 40 );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_transparent_header', array(
		'label'       => esc_html__( 'Use a transparent header on the static homepage', 'zola' ),
		'description' => esc_html__( 'Use this when the first section has a dark or photographic background.', 'zola' ),
		'type'        => 'checkbox',
		'default'     => true,
		'sanitize'    => 'wp_validate_boolean',
		'priority'    => 41,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_transparent_header_height', array(
		'label'       => esc_html__( 'Height - Transparent Header', 'zola' ),
		'type'        => 'range',
		'default'     => 70,
		'sanitize'    => 'zola_sanitize_header_height',
		'input_attrs' => array( 'min' => 48, 'max' => 140, 'step' => 1 ),
		'priority'    => 42,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_transparent_header_background_color', array(
		'label'    => esc_html__( 'Transparent Header Background Color', 'zola' ),
		'type'     => 'color',
		'default'  => '',
		'sanitize' => 'sanitize_hex_color',
		'priority' => 43,
	) );
	$colors_and_style = array(
		'zola_accent_color' => array(
			'label' => esc_html__( 'Accent color', 'zola' ),
			'type' => 'color',
			'default' => '#2563eb',
			'sanitize' => 'sanitize_hex_color',
		),
		'zola_ink_color' => array(
			'label' => esc_html__( 'Dark surface color', 'zola' ),
			'type' => 'color',
			'default' => '#202124',
			'sanitize' => 'sanitize_hex_color',
		),
		'zola_paper_color' => array(
			'label' => esc_html__( 'Site background color', 'zola' ),
			'type' => 'color',
			'default' => '#ffffff',
			'sanitize' => 'sanitize_hex_color',
		),
		'zola_text_color' => array(
			'label' => esc_html__( 'Body text color', 'zola' ),
			'type' => 'color',
			'default' => '#202126',
			'sanitize' => 'sanitize_hex_color',
		),
		'zola_font_family' => array(
			'label' => esc_html__( 'Global font family', 'zola' ),
			'type' => 'select',
			'default' => 'sans',
			'sanitize' => 'sanitize_key',
			'choices' => array(
				'sans'      => esc_html__( 'System sans serif', 'zola' ),
				'arial'     => 'Arial',
				'georgia'   => 'Georgia',
				'trebuchet' => 'Trebuchet MS',
			),
		),
		'zola_button_radius' => array(
			'label'      => esc_html__( 'Button corner radius (px)', 'zola' ),
			'type'       => 'number',
			'default'    => 3,
			'sanitize'   => 'absint',
			'input_attrs' => array( 'min' => 0, 'max' => 20, 'step' => 1 ),
		),
	);
	foreach ( $colors_and_style as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_style', $setting_id, $control );
	}

	$layout_controls = array(
		'zola_site_width' => array(
			'label'      => esc_html__( 'Site width (px)', 'zola' ),
			'type'       => 'number',
			'default'    => 1320,
			'sanitize'   => 'zola_sanitize_site_width',
			'input_attrs' => array( 'min' => 960, 'max' => 1680, 'step' => 40 ),
		),
		'zola_content_width' => array(
			'label'       => esc_html__( 'Reading width (px)', 'zola' ),
			'description' => esc_html__( 'Maximum width of text in standard pages and posts.', 'zola' ),
			'type'        => 'number',
			'default'     => 780,
			'sanitize'    => 'zola_sanitize_content_width',
			'input_attrs' => array( 'min' => 560, 'max' => 1100, 'step' => 20 ),
		),
	);
	foreach ( $layout_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_layout', $setting_id, $control );
	}

	$blog_controls = array(
		'zola_archive_layout' => array(
			'label'    => esc_html__( 'Post layout', 'zola' ),
			'type'     => 'select',
			'default'  => 'grid',
			'sanitize' => 'zola_sanitize_blog_layout',
			'choices'  => array(
				'grid'  => esc_html__( 'Grid', 'zola' ),
				'list'  => esc_html__( 'List', 'zola' ),
				'cover' => esc_html__( 'Cover cards', 'zola' ),
			),
		),
		'zola_archive_columns' => array(
			'label'    => esc_html__( 'Post columns', 'zola' ),
			'type'     => 'select',
			'default'  => 3,
			'sanitize' => 'zola_sanitize_columns',
			'choices'  => array( 2 => 2, 3 => 3, 4 => 4 ),
		),
		'zola_blog_excerpt_length' => array(
			'label'       => esc_html__( 'Excerpt words', 'zola' ),
			'type'        => 'number',
			'default'     => 22,
			'sanitize'    => 'zola_sanitize_excerpt_length',
			'input_attrs' => array( 'min' => 8, 'max' => 60, 'step' => 1 ),
		),
		'zola_blog_show_date' => array(
			'label'    => esc_html__( 'Show post date', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
		),
		'zola_blog_show_categories' => array(
			'label'    => esc_html__( 'Show post categories', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
		),
		'zola_blog_show_excerpt' => array(
			'label'    => esc_html__( 'Show post excerpt', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
		),
		'zola_blog_show_sidebar' => array(
			'label'       => esc_html__( 'Show blog sidebar when it has widgets', 'zola' ),
			'description' => esc_html__( 'Add widgets in the Widgets section below, then enable this option.', 'zola' ),
			'type'        => 'checkbox',
			'default'     => false,
			'sanitize'    => 'wp_validate_boolean',
		),
	);
	foreach ( $blog_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_blog', $setting_id, $control );
	}

	$button_controls = array(
		'zola_header_cta_label' => array(
			'label'    => esc_html__( 'Button label', 'zola' ),
			'type'     => 'text',
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
		),
		'zola_header_cta_url' => array(
			'label'    => esc_html__( 'Button link', 'zola' ),
			'type'     => 'url',
			'default'  => '',
			'sanitize' => 'esc_url_raw',
		),
	);
	foreach ( $button_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_header_buttons', $setting_id, $control );
	}

	zola_add_customizer_control( $wp_customize, 'zola_header_top_bar', 'zola_top_bar_enabled', array(
		'label'    => esc_html__( 'Enable Top Bar', 'zola' ),
		'type'     => 'checkbox',
		'default'  => true,
		'sanitize' => 'wp_validate_boolean',
		'priority' => 1,
	) );
	zola_add_header_group_control( $wp_customize, 'zola_top_bar_layout_group', esc_html__( 'Layout', 'zola' ), 5, 'zola_header_top_bar' );
	zola_add_customizer_control( $wp_customize, 'zola_header_top_bar', 'zola_top_bar_height', array(
		'label'       => esc_html__( 'Height', 'zola' ),
		'type'        => 'range',
		'default'     => 40,
		'sanitize'    => 'zola_sanitize_top_bar_height',
		'input_attrs' => array( 'min' => 24, 'max' => 100, 'step' => 1 ),
		'priority'    => 6,
	) );
	zola_add_header_visual_control( $wp_customize, 'zola_top_bar_text_mode', array(
		'label'       => esc_html__( 'Text Color', 'zola' ),
		'default'     => 'light',
		'sanitize'    => 'zola_sanitize_header_text_mode',
		'visual_type' => 'text-color',
		'choices'     => array( 'light' => esc_html__( 'Light', 'zola' ), 'dark' => esc_html__( 'Dark', 'zola' ) ),
		'priority'    => 7,
		'section'     => 'zola_header_top_bar',
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header_top_bar', 'zola_top_bar_background_color', array(
		'label'    => esc_html__( 'Background Color', 'zola' ),
		'type'     => 'color',
		'default'  => '',
		'sanitize' => 'sanitize_hex_color',
		'priority' => 8,
	) );
	zola_add_header_group_control( $wp_customize, 'zola_top_bar_navigation_group', esc_html__( 'Navigation', 'zola' ), 20, 'zola_header_top_bar' );
	zola_add_header_visual_control( $wp_customize, 'zola_top_bar_nav_style', array(
		'label'       => esc_html__( 'Navigation Style', 'zola' ),
		'default'     => 'plain',
		'sanitize'    => 'zola_sanitize_header_nav_style',
		'visual_type' => 'nav-style',
		'choices'     => zola_header_nav_style_choices(),
		'priority'    => 21,
		'section'     => 'zola_header_top_bar',
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header_top_bar', 'zola_top_bar_nav_uppercase', array(
		'label'    => esc_html__( 'Uppercase', 'zola' ),
		'type'     => 'checkbox',
		'default'  => false,
		'sanitize' => 'wp_validate_boolean',
		'priority' => 22,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header_top_bar', 'zola_top_bar_nav_overlay', array(
		'label'    => esc_html__( 'Add overlay on hover', 'zola' ),
		'type'     => 'checkbox',
		'default'  => false,
		'sanitize' => 'wp_validate_boolean',
		'priority' => 23,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header_top_bar', 'zola_top_bar_nav_height', array(
		'label'       => esc_html__( 'Nav Height', 'zola' ),
		'type'        => 'range',
		'default'     => 16,
		'sanitize'    => 'zola_sanitize_header_nav_height',
		'input_attrs' => array( 'min' => 12, 'max' => 40, 'step' => 1 ),
		'priority'    => 24,
	) );
	foreach ( array(
		'zola_top_bar_nav_color'       => array( 'label' => esc_html__( 'Nav Color', 'zola' ), 'priority' => 25 ),
		'zola_top_bar_nav_hover_color' => array( 'label' => esc_html__( 'Nav Color :hover', 'zola' ), 'priority' => 26 ),
	) as $setting_id => $color_control ) {
		zola_add_customizer_control( $wp_customize, 'zola_header_top_bar', $setting_id, array(
			'label'    => $color_control['label'],
			'type'     => 'color',
			'default'  => '',
			'sanitize' => 'sanitize_hex_color',
			'priority' => $color_control['priority'],
		) );
	}

	// Header Bottom layout and navigation settings.
	zola_add_header_group_control( $wp_customize, 'zola_header_bottom_layout_group', esc_html__( 'Layout', 'zola' ), 20, 'zola_header_bottom' );
	zola_add_customizer_control( $wp_customize, 'zola_header_bottom', 'zola_header_bottom_height', array(
		'label'       => esc_html__( 'Height', 'zola' ),
		'type'        => 'range',
		'default'     => 10,
		'sanitize'    => 'zola_sanitize_header_bottom_height',
		'input_attrs' => array( 'min' => 10, 'max' => 100, 'step' => 1 ),
		'priority'    => 21,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header_bottom', 'zola_header_bottom_background_color', array(
		'label'    => esc_html__( 'Background color', 'zola' ),
		'type'     => 'color',
		'default'  => '',
		'sanitize' => 'sanitize_hex_color',
		'priority' => 22,
	) );
	zola_add_header_group_control( $wp_customize, 'zola_header_bottom_navigation_group', esc_html__( 'Navigation', 'zola' ), 30, 'zola_header_bottom' );
	zola_add_header_visual_control( $wp_customize, 'zola_header_bottom_nav_style', array(
		'label'       => esc_html__( 'Nav Style', 'zola' ),
		'default'     => 'plain',
		'sanitize'    => 'zola_sanitize_header_nav_style',
		'visual_type' => 'nav-style',
		'choices'     => zola_header_nav_style_choices(),
		'priority'    => 31,
		'section'     => 'zola_header_bottom',
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header_bottom', 'zola_header_bottom_nav_height', array(
		'label'       => esc_html__( 'Nav Height', 'zola' ),
		'type'        => 'range',
		'default'     => 16,
		'sanitize'    => 'zola_sanitize_header_nav_height',
		'input_attrs' => array( 'min' => 12, 'max' => 40, 'step' => 1 ),
		'priority'    => 32,
	) );
	$bottom_nav_scales = array(
		'xs'      => 'XS',
		's'       => 'S',
		'default' => esc_html__( 'Default', 'zola' ),
		'm'       => 'M',
		'l'       => 'L',
		'xl'      => 'XL',
	);
	zola_add_header_visual_control( $wp_customize, 'zola_header_bottom_nav_size', array(
		'label'       => esc_html__( 'Nav Size', 'zola' ),
		'default'     => 'default',
		'sanitize'    => 'zola_sanitize_header_nav_scale',
		'visual_type' => 'scale',
		'choices'     => $bottom_nav_scales,
		'priority'    => 33,
		'section'     => 'zola_header_bottom',
	) );
	zola_add_header_visual_control( $wp_customize, 'zola_header_bottom_nav_spacing', array(
		'label'       => esc_html__( 'Nav Spacing', 'zola' ),
		'default'     => 'default',
		'sanitize'    => 'zola_sanitize_header_nav_scale',
		'visual_type' => 'scale',
		'choices'     => $bottom_nav_scales,
		'priority'    => 34,
		'section'     => 'zola_header_bottom',
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header_bottom', 'zola_header_bottom_nav_uppercase', array(
		'label'    => esc_html__( 'Uppercase', 'zola' ),
		'type'     => 'checkbox',
		'default'  => false,
		'sanitize' => 'wp_validate_boolean',
		'priority' => 35,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header_bottom', 'zola_header_bottom_nav_overlay', array(
		'label'    => esc_html__( 'Add overlay on hover', 'zola' ),
		'type'     => 'checkbox',
		'default'  => false,
		'sanitize' => 'wp_validate_boolean',
		'priority' => 36,
	) );
	zola_add_header_visual_control( $wp_customize, 'zola_header_bottom_nav_base_color', array(
		'label'       => esc_html__( 'Nav Base Color', 'zola' ),
		'default'     => 'dark',
		'sanitize'    => 'zola_sanitize_header_text_mode',
		'visual_type' => 'text-color',
		'choices'     => array( 'light' => esc_html__( 'Light', 'zola' ), 'dark' => esc_html__( 'Dark', 'zola' ) ),
		'priority'    => 37,
		'section'     => 'zola_header_bottom',
	) );
	foreach ( array(
		'zola_header_bottom_nav_color'       => array( 'label' => esc_html__( 'Nav Color', 'zola' ), 'priority' => 38 ),
		'zola_header_bottom_nav_hover_color' => array( 'label' => esc_html__( 'Nav Color :hover', 'zola' ), 'priority' => 39 ),
	) as $setting_id => $color_control ) {
		zola_add_customizer_control( $wp_customize, 'zola_header_bottom', $setting_id, array(
			'label'    => $color_control['label'],
			'type'     => 'color',
			'default'  => '',
			'sanitize' => 'sanitize_hex_color',
			'priority' => $color_control['priority'],
		) );
	}

	$sticky_header_controls = array(
		'zola_sticky_header' => array(
			'label'    => esc_html__( 'Keep the header visible while scrolling', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
		),
	);
	foreach ( $sticky_header_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_header_sticky', $setting_id, $control );
	}

	$social_controls = array(
		'zola_header_social_facebook'  => array( 'label' => 'Facebook', 'type' => 'url', 'default' => '', 'sanitize' => 'esc_url_raw' ),
		'zola_header_social_instagram' => array( 'label' => 'Instagram', 'type' => 'url', 'default' => '', 'sanitize' => 'esc_url_raw' ),
		'zola_header_social_linkedin'  => array( 'label' => 'LinkedIn', 'type' => 'url', 'default' => '', 'sanitize' => 'esc_url_raw' ),
		'zola_header_social_youtube'   => array( 'label' => 'YouTube', 'type' => 'url', 'default' => '', 'sanitize' => 'esc_url_raw' ),
		'zola_header_social_x'         => array( 'label' => 'X', 'type' => 'url', 'default' => '', 'sanitize' => 'esc_url_raw' ),
	);
	foreach ( $social_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_header_follow_icons', $setting_id, $control );
	}
	for ( $block_number = 1; $block_number <= 5; $block_number++ ) {
		$setting_id = 'zola_header_custom_html_' . $block_number;
		$default    = 1 === $block_number ? (string) get_theme_mod( 'zola_header_custom_html', '' ) : '';
		zola_add_customizer_control( $wp_customize, 'zola_header_html', $setting_id, array(
			'label'       => sprintf( esc_html__( 'HTML %d', 'zola' ), $block_number ),
			'description' => esc_html__( 'Add HTML or a shortcode here. HTML is sanitized before it appears in the header.', 'zola' ),
			'type'        => 'textarea',
			'default'     => $default,
			'sanitize'    => 'wp_kses_post',
		) );
	}

	$footer_controls = array(
		'zola_footer_copyright' => array(
			'label'       => esc_html__( 'Copyright text', 'zola' ),
			'description' => esc_html__( 'Use {year} to insert the current year. Leave empty to show the site name.', 'zola' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
		),
		'zola_footer_show_menu' => array(
			'label'    => esc_html__( 'Show footer menu', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
		),
		'zola_footer_show_back_to_top' => array(
			'label'    => esc_html__( 'Show back-to-top link', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
		),
	);
	foreach ( $footer_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_footer', $setting_id, $control );
	}

	$pages_controls = array(
		'zola_page_show_title' => array(
			'label'       => esc_html__( 'Show default page title block', 'zola' ),
			'description' => esc_html__( 'Applies to standard pages. Pages built in Elementor control their own titles.', 'zola' ),
			'type'        => 'checkbox',
			'default'     => true,
			'sanitize'    => 'wp_validate_boolean',
		),
		'zola_page_title_eyebrow' => array(
			'label'    => esc_html__( 'Small label above page title', 'zola' ),
			'type'     => 'text',
			'default'  => 'Page',
			'sanitize' => 'sanitize_text_field',
		),
	);
	foreach ( $pages_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_pages', $setting_id, $control );
	}

	$portfolio_controls = array(
		'zola_portfolio_layout' => array(
			'label'    => esc_html__( 'Project layout', 'zola' ),
			'type'     => 'select',
			'default'  => 'grid',
			'sanitize' => 'zola_sanitize_blog_layout',
			'choices'  => array(
				'grid'  => esc_html__( 'Grid', 'zola' ),
				'list'  => esc_html__( 'List', 'zola' ),
				'cover' => esc_html__( 'Cover cards', 'zola' ),
			),
		),
		'zola_portfolio_columns' => array(
			'label'    => esc_html__( 'Project columns', 'zola' ),
			'type'     => 'select',
			'default'  => 3,
			'sanitize' => 'zola_sanitize_columns',
			'choices'  => array( 2 => 2, 3 => 3, 4 => 4 ),
		),
		'zola_portfolio_archive_title' => array(
			'label'    => esc_html__( 'Archive label', 'zola' ),
			'type'     => 'text',
			'default'  => 'Portfolio',
			'sanitize' => 'sanitize_text_field',
		),
		'zola_portfolio_excerpt_length' => array(
			'label'       => esc_html__( 'Project excerpt words', 'zola' ),
			'type'        => 'number',
			'default'     => 18,
			'sanitize'    => 'zola_sanitize_excerpt_length',
			'input_attrs' => array( 'min' => 8, 'max' => 60, 'step' => 1 ),
		),
		'zola_portfolio_show_date' => array(
			'label'    => esc_html__( 'Show project date', 'zola' ),
			'type'     => 'checkbox',
			'default'  => false,
			'sanitize' => 'wp_validate_boolean',
		),
	);
	foreach ( $portfolio_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_portfolio', $setting_id, $control );
	}

	$share_controls = array(
		'zola_share_enabled' => array(
			'label'    => esc_html__( 'Show sharing links on posts', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
		),
		'zola_share_label' => array(
			'label'    => esc_html__( 'Sharing label', 'zola' ),
			'type'     => 'text',
			'default'  => 'Share',
			'sanitize' => 'sanitize_text_field',
		),
		'zola_share_facebook' => array(
			'label'    => 'Facebook',
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
		),
		'zola_share_x' => array(
			'label'    => 'X',
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
		),
		'zola_share_linkedin' => array(
			'label'    => 'LinkedIn',
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
		),
		'zola_share_copy_link' => array(
			'label'    => esc_html__( 'Copy link button', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
		),
	);
	foreach ( $share_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_share', $setting_id, $control );
	}

	$footer_choices = array( 0 => esc_html__( 'Use the theme footer', 'zola' ) );
	if ( post_type_exists( 'elementor_library' ) ) {
		$elementor_templates = get_posts( array(
			'post_type'      => 'elementor_library',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );
		foreach ( $elementor_templates as $elementor_template ) {
			$footer_choices[ $elementor_template->ID ] = $elementor_template->post_title;
		}
	}
	zola_add_customizer_control( $wp_customize, 'zola_footer', 'zola_footer_template', array(
		'label'       => esc_html__( 'Elementor footer template', 'zola' ),
		'description' => esc_html__( 'Select a published Elementor template. Elementor Theme Builder footers take priority when active.', 'zola' ),
		'type'        => 'select',
		'default'     => 0,
		'sanitize'    => 'absint',
		'choices'     => $footer_choices,
	) );

	if ( class_exists( 'WooCommerce' ) ) {
		$wp_customize->add_section( 'zola_shop', array(
			'title'       => esc_html__( 'WooCommerce', 'zola' ),
			'description' => esc_html__( 'Set the product grid and number of products per shop page.', 'zola' ),
			'priority'    => 140,
		) );
		$shop_controls = array(
			'zola_shop_columns' => array(
				'label'   => esc_html__( 'Product columns', 'zola' ),
				'type'    => 'select',
				'default' => 3,
				'sanitize' => 'zola_sanitize_columns',
				'choices' => array( 2 => 2, 3 => 3, 4 => 4 ),
			),
			'zola_shop_per_page' => array(
				'label'   => esc_html__( 'Products per shop page', 'zola' ),
				'type'    => 'select',
				'default' => 12,
				'sanitize' => 'absint',
				'choices' => array( 6 => 6, 9 => 9, 12 => 12, 18 => 18, 24 => 24 ),
			),
		);
		foreach ( $shop_controls as $setting_id => $control ) {
			zola_add_customizer_control( $wp_customize, 'zola_shop', $setting_id, $control );
		}
	}
}
add_action( 'customize_register', 'zola_customize_register', 20 );

/** Place the built-in WordPress sections alongside the theme sections. */
function zola_customize_reorder_core_sections( WP_Customize_Manager $wp_customize ): void {
	$site_identity = $wp_customize->get_section( 'title_tagline' );
	if ( $site_identity ) {
		$site_identity->title    = esc_html__( 'Logo & Site Identity', 'zola' );
		$site_identity->panel    = 'zola_header_panel';
		$site_identity->priority = 10;
	}
	$core_priorities = array(
		'nav_menus'         => 105,
		'widgets'           => 110,
		'static_front_page' => 120,
		'custom_css'        => 180,
		'colors'            => 185,
		'header_image'      => 186,
		'background_image'  => 187,
	);
	foreach ( $core_priorities as $section_id => $priority ) {
		$section = $wp_customize->get_section( $section_id );
		if ( $section ) {
			$section->priority = $priority;
		}
	}
}
add_action( 'customize_register', 'zola_customize_reorder_core_sections', 200 );

/** Add theme logo controls to the native Site Identity section. */
function zola_customize_register_logo_identity_controls( WP_Customize_Manager $wp_customize ): void {
	if ( ! $wp_customize->get_section( 'title_tagline' ) ) {
		return;
	}

	$core_control_settings = array(
		'blogname'        => 10,
		'blogdescription' => 20,
		'custom_logo'     => 40,
		'site_icon'       => 110,
	);
	foreach ( $core_control_settings as $control_id => $priority ) {
		$control = $wp_customize->get_control( $control_id );
		if ( $control ) {
			$control->priority = $priority;
		}
	}

	$logo_control = $wp_customize->get_control( 'custom_logo' );
	if ( $logo_control ) {
		$logo_control->label       = esc_html__( 'Logo image', 'zola' );
		$logo_control->description = esc_html__( 'Upload the standard logo used in the header.', 'zola' );
	}

	zola_add_customizer_control( $wp_customize, 'title_tagline', 'zola_logo_show_site_info', array(
		'label'       => esc_html__( 'Display below Logo', 'zola' ),
		'description' => esc_html__( 'Show the site title and tagline beneath the logo.', 'zola' ),
		'type'        => 'checkbox',
		'default'     => false,
		'sanitize'    => 'wp_validate_boolean',
		'priority'    => 25,
	) );

	if ( ! class_exists( 'Zola_Logo_Position_Customize_Control' ) ) {
		class Zola_Logo_Position_Customize_Control extends WP_Customize_Control {
			public $type = 'zola-logo-position';

			public function render_content(): void {
				$setting_id = $this->setting instanceof WP_Customize_Setting ? $this->setting->id : '';
				?>
				<fieldset class="zola-header-visual-choice zola-header-visual-choice--logo-position" data-zola-visual-choice data-zola-setting="<?php echo esc_attr( $setting_id ); ?>">
					<legend class="customize-control-title"><?php echo esc_html( $this->label ); ?></legend>
					<div class="zola-header-visual-choice__options">
						<?php foreach ( $this->choices as $value => $label ) : ?>
							<label class="zola-header-visual-choice__option zola-header-visual-choice__option--<?php echo esc_attr( sanitize_html_class( $value ) ); ?>" title="<?php echo esc_attr( $label ); ?>">
								<input type="radio" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $this->value(), $value ); ?> data-zola-visual-choice-input>
								<span class="zola-header-visual-choice__sample" aria-hidden="true">
									<span class="zola-logo-position__mark"></span>
									<span class="zola-logo-position__links"></span>
								</span>
								<span class="zola-header-visual-choice__label"><?php echo esc_html( $label ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
				<?php
			}
		}
	}
	$wp_customize->add_setting( 'zola_logo_position', array(
		'default'           => 'left',
		'sanitize_callback' => 'zola_sanitize_logo_position',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new Zola_Logo_Position_Customize_Control( $wp_customize, 'zola_logo_position_control', array(
		'settings' => 'zola_logo_position',
		'section'  => 'title_tagline',
		'label'    => esc_html__( 'Logo position', 'zola' ),
		'choices'  => array(
			'left'   => esc_html__( 'Left', 'zola' ),
			'center' => esc_html__( 'Center', 'zola' ),
		),
		'priority' => 30,
	) ) );

	$wp_customize->add_setting( 'zola_logo_light_image', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'zola_logo_light_image_control', array(
		'settings'    => 'zola_logo_light_image',
		'section'     => 'title_tagline',
		'label'       => esc_html__( 'Logo image - Light Version', 'zola' ),
		'description' => esc_html__( 'Upload an alternative light logo for dark and transparent headers.', 'zola' ),
		'mime_type'   => 'image',
		'priority'    => 50,
	) ) );

	zola_add_customizer_control( $wp_customize, 'title_tagline', 'zola_logo_container_width', array(
		'label'       => esc_html__( 'Logo container width', 'zola' ),
		'description' => esc_html__( 'Set the width of the logo area as a percentage of its Header Builder position.', 'zola' ),
		'type'        => 'range',
		'default'     => 42,
		'sanitize'    => 'zola_sanitize_logo_container_width',
		'input_attrs' => array( 'min' => 20, 'max' => 100, 'step' => 1 ),
		'priority'    => 60,
	) );
	zola_add_customizer_control( $wp_customize, 'title_tagline', 'zola_logo_max_width', array(
		'label'       => esc_html__( 'Logo max width (px)', 'zola' ),
		'description' => esc_html__( 'Set a maximum width in pixels. Leave blank to fit the available container.', 'zola' ),
		'type'        => 'number',
		'default'     => '',
		'sanitize'    => 'zola_sanitize_logo_max_width',
		'input_attrs' => array( 'min' => 24, 'max' => 1200, 'step' => 1, 'placeholder' => esc_attr__( 'Auto', 'zola' ) ),
		'priority'    => 70,
	) );
	zola_add_customizer_control( $wp_customize, 'title_tagline', 'zola_logo_padding', array(
		'label'       => esc_html__( 'Logo Padding', 'zola' ),
		'description' => esc_html__( 'Add space around the logo in pixels.', 'zola' ),
		'type'        => 'range',
		'default'     => 0,
		'sanitize'    => 'zola_sanitize_logo_padding',
		'input_attrs' => array( 'min' => 0, 'max' => 80, 'step' => 1 ),
		'priority'    => 80,
	) );
	zola_add_customizer_control( $wp_customize, 'title_tagline', 'zola_logo_link', array(
		'label'       => esc_html__( 'Logo link', 'zola' ),
		'description' => esc_html__( 'Custom logo link. Leave blank to link to the home page.', 'zola' ),
		'type'        => 'url',
		'default'     => '',
		'sanitize'    => 'zola_sanitize_logo_link',
		'priority'    => 90,
	) );
}
add_action( 'customize_register', 'zola_customize_register_logo_identity_controls', 220 );

/** Load decorative icons for the top-level Customizer sections and panels. */
function zola_customize_controls_enqueue_styles(): void {
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style(
		'zola-customizer-controls',
		ZOLA_URI . '/assets/css/customizer-controls.css',
		array( 'customize-controls', 'dashicons' ),
		(string) filemtime( ZOLA_DIR . '/assets/css/customizer-controls.css' )
	);
	wp_enqueue_style(
		'zola-header-builder-controls',
		ZOLA_URI . '/assets/css/customizer-header-builder.css',
		array( 'zola-customizer-controls' ),
		(string) filemtime( ZOLA_DIR . '/assets/css/customizer-header-builder.css' )
	);
	wp_enqueue_script(
		'zola-header-builder-controls',
		ZOLA_URI . '/assets/js/customizer-header-builder.js',
		array( 'customize-controls' ),
		(string) filemtime( ZOLA_DIR . '/assets/js/customizer-header-builder.js' ),
		true
	);
	wp_localize_script( 'zola-header-builder-controls', 'ZolaHeaderBuilder', array(
		'rows'    => array(
			'top'    => esc_html__( 'Top Bar', 'zola' ),
			'main'   => esc_html__( 'Header Main', 'zola' ),
			'bottom' => esc_html__( 'Header Bottom', 'zola' ),
		),
		'columns' => array(
			'left'   => esc_html__( 'Left', 'zola' ),
			'center' => esc_html__( 'Center', 'zola' ),
			'right'  => esc_html__( 'Right', 'zola' ),
		),
		'devices' => array(
			'desktop' => esc_html__( 'Desktop', 'zola' ),
			'mobile'  => esc_html__( 'Mobile / Tablet', 'zola' ),
		),
		'modules' => array(
			'logo'         => esc_html__( 'Site Logo', 'zola' ),
			'menu'         => esc_html__( 'Primary Menu', 'zola' ),
			'button'       => esc_html__( 'Button', 'zola' ),
			'search'       => esc_html__( 'Search', 'zola' ),
			'announcement' => esc_html__( 'Announcement', 'zola' ),
			'social'       => esc_html__( 'Social Links', 'zola' ),
			'html_1'       => esc_html__( 'HTML 1', 'zola' ),
			'html_2'       => esc_html__( 'HTML 2', 'zola' ),
			'html_3'       => esc_html__( 'HTML 3', 'zola' ),
			'html_4'       => esc_html__( 'HTML 4', 'zola' ),
			'html_5'       => esc_html__( 'HTML 5', 'zola' ),
			'account'      => esc_html__( 'Account', 'zola' ),
			'cart'         => esc_html__( 'Cart', 'zola' ),
		),
		'headerPresets'  => zola_header_builder_presets(),
		'clearLayout'    => esc_html__( 'Clear All', 'zola' ),
		'clearConfirm'   => esc_html__( 'Clear the current device header layout?', 'zola' ),
		'builderTitle'   => esc_html__( 'Header Builder', 'zola' ),
		'currentLayout'  => esc_html__( 'Current layout', 'zola' ),
		'customLayout'   => esc_html__( 'Custom', 'zola' ),
		'presetsLabel'   => esc_html__( 'Presets', 'zola' ),
		'closeBuilder'   => esc_html__( 'Close', 'zola' ),
		'tutorial'       => esc_html__( 'Tutorial', 'zola' ),
		'tutorialText'   => esc_html__( 'Choose Desktop or Mobile / Tablet, then use Presets to select a layout for that device. You can also select a position, add an element below or drag elements between positions.', 'zola' ),
		'emptySlot'      => esc_html__( 'Drop elements here', 'zola' ),
		'noAvailable'    => esc_html__( 'All elements are in this layout.', 'zola' ),
		'settings'       => esc_html__( 'Settings', 'zola' ),
		'remove'         => esc_html__( 'Remove', 'zola' ),
		'elements'       => esc_html__( 'Add elements', 'zola' ),
		'activePosition' => esc_html__( 'Selected position', 'zola' ),
	) );
}
add_action( 'customize_controls_enqueue_scripts', 'zola_customize_controls_enqueue_styles' );

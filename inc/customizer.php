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

	$allowed_modules = array( 'logo', 'menu', 'button', 'search', 'announcement', 'social', 'html', 'account', 'cart' );
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

	if ( 'color' === $control['type'] ) {
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, $control_args ) );
	} else {
		$wp_customize->add_control( $setting_id, $control_args );
	}
}

function zola_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_panel( 'zola_header_panel', array(
		'title'    => esc_html__( 'Header', 'zola' ),
		'priority' => 30,
	) );

	$sections = array(
		'zola_header_presets' => array(
			'title'       => esc_html__( 'Header Presets', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 5,
		),
		'zola_header' => array(
			'title'       => esc_html__( 'Header Main', 'zola' ),
			'description' => esc_html__( 'Set content and display options for your single site-wide Header Builder layout.', 'zola' ),
			'panel'       => 'zola_header_panel',
			'priority'    => 10,
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

	$header_controls = array(
		'zola_header_cta_label' => array(
			'label'    => esc_html__( 'Header button label', 'zola' ),
			'type'     => 'text',
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
		),
		'zola_header_cta_url' => array(
			'label'    => esc_html__( 'Header button link', 'zola' ),
			'type'     => 'url',
			'default'  => '',
			'sanitize' => 'esc_url_raw',
		),
		'zola_announcement_text' => array(
			'label'    => esc_html__( 'Announcement bar text', 'zola' ),
			'type'     => 'text',
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
		),
		'zola_announcement_url' => array(
			'label'    => esc_html__( 'Announcement bar link', 'zola' ),
			'type'     => 'url',
			'default'  => '',
			'sanitize' => 'esc_url_raw',
		),
		'zola_sticky_header' => array(
			'label'    => esc_html__( 'Keep the header visible while scrolling', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
		),
		'zola_transparent_header' => array(
			'label'       => esc_html__( 'Use a transparent header on the static homepage', 'zola' ),
			'description' => esc_html__( 'Use this when the first section has a dark or photographic background.', 'zola' ),
			'type'        => 'checkbox',
			'default'     => true,
			'sanitize'    => 'wp_validate_boolean',
		),
	);
	foreach ( $header_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_header', $setting_id, $control );
	}

	$social_controls = array(
		'zola_header_social_facebook'  => array( 'label' => 'Facebook', 'type' => 'url', 'default' => '', 'sanitize' => 'esc_url_raw' ),
		'zola_header_social_instagram' => array( 'label' => 'Instagram', 'type' => 'url', 'default' => '', 'sanitize' => 'esc_url_raw' ),
		'zola_header_social_linkedin'  => array( 'label' => 'LinkedIn', 'type' => 'url', 'default' => '', 'sanitize' => 'esc_url_raw' ),
		'zola_header_social_youtube'   => array( 'label' => 'YouTube', 'type' => 'url', 'default' => '', 'sanitize' => 'esc_url_raw' ),
		'zola_header_social_x'         => array( 'label' => 'X', 'type' => 'url', 'default' => '', 'sanitize' => 'esc_url_raw' ),
	);
	foreach ( $social_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_header', $setting_id, $control );
	}
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_header_custom_html', array(
		'label'       => esc_html__( 'Custom HTML content', 'zola' ),
		'description' => esc_html__( 'This content is sanitized before it appears in the header.', 'zola' ),
		'type'        => 'textarea',
		'default'     => '',
		'sanitize'    => 'wp_kses_post',
	) );

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
	$core_priorities = array(
		'title_tagline'     => 15,
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
			'html'         => esc_html__( 'HTML', 'zola' ),
			'account'      => esc_html__( 'Account', 'zola' ),
			'cart'         => esc_html__( 'Cart', 'zola' ),
		),
		'headerPresets'  => zola_header_builder_presets(),
		'clearLayout'    => esc_html__( 'Clear All', 'zola' ),
		'clearConfirm'   => esc_html__( 'Clear the current device header layout?', 'zola' ),
		'builderTitle'   => esc_html__( 'Header Builder', 'zola' ),
		'currentLayout'  => esc_html__( 'Current layout', 'zola' ),
		'customLayout'   => esc_html__( 'Custom', 'zola' ),
		'presetsLabel'   => esc_html__( 'Header Presets', 'zola' ),
		'closeBuilder'   => esc_html__( 'Close', 'zola' ),
		'tutorial'       => esc_html__( 'Tutorial', 'zola' ),
		'tutorialText'   => esc_html__( 'Choose Desktop or Mobile / Tablet, then use Header Presets to select a layout for that device. You can also select a position, add an element below or drag elements between positions.', 'zola' ),
		'emptySlot'      => esc_html__( 'Drop elements here', 'zola' ),
		'noAvailable'    => esc_html__( 'All elements are in this layout.', 'zola' ),
		'settings'       => esc_html__( 'Settings', 'zola' ),
		'remove'         => esc_html__( 'Remove', 'zola' ),
		'elements'       => esc_html__( 'Add elements', 'zola' ),
		'activePosition' => esc_html__( 'Selected position', 'zola' ),
	) );
}
add_action( 'customize_controls_enqueue_scripts', 'zola_customize_controls_enqueue_styles' );

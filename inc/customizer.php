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
	$sections = array(
		'zola_header' => array(
			'title'       => esc_html__( 'Header', 'zola' ),
			'description' => esc_html__( 'Build the Header in Elementor and assign a saved template in Zola → Site Builder.', 'zola' ),
			'priority'    => 30,
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

	$elementor_choices = array( 0 => esc_html__( 'Use the theme header', 'zola' ) );
	if ( post_type_exists( 'elementor_library' ) ) {
		$elementor_templates = get_posts( array(
			'post_type'      => 'elementor_library',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );
		foreach ( $elementor_templates as $elementor_template ) {
			$elementor_choices[ $elementor_template->ID ] = $elementor_template->post_title;
		}
	}

	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_header_template', array(
		'label'       => esc_html__( 'Elementor header template', 'zola' ),
		'description' => esc_html__( 'Select a published Elementor template. Add the Zola Navigation Menu widget to display a WordPress menu. Elementor Theme Builder headers take priority when active.', 'zola' ),
		'type'        => 'select',
		'default'     => 0,
		'sanitize'    => 'absint',
		'choices'     => $elementor_choices,
	) );

	$footer_choices = $elementor_choices;
	$footer_choices[0] = esc_html__( 'Use the theme footer', 'zola' );
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
}
add_action( 'customize_controls_enqueue_scripts', 'zola_customize_controls_enqueue_styles' );

<?php
/** Theme-specific Customizer controls. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Fonts supported by the theme typography controls. */
function zola_typography_font_catalog(): array {
	return array(
		'sans'          => array( 'label' => esc_html__( 'System sans serif', 'zola' ), 'css' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif', 'google' => '' ),
		'arial'         => array( 'label' => 'Arial', 'css' => 'Arial, Helvetica, sans-serif', 'google' => '' ),
		'georgia'       => array( 'label' => 'Georgia', 'css' => 'Georgia, "Times New Roman", serif', 'google' => '' ),
		'trebuchet'     => array( 'label' => 'Trebuchet MS', 'css' => '"Trebuchet MS", sans-serif', 'google' => '' ),
		'prompt'        => array( 'label' => 'Prompt', 'css' => 'Prompt, sans-serif', 'google' => 'Prompt' ),
		'roboto'        => array( 'label' => 'Roboto', 'css' => 'Roboto, sans-serif', 'google' => 'Roboto' ),
		'open-sans'     => array( 'label' => 'Open Sans', 'css' => '"Open Sans", sans-serif', 'google' => 'Open Sans' ),
		'lato'          => array( 'label' => 'Lato', 'css' => 'Lato, sans-serif', 'google' => 'Lato' ),
		'montserrat'    => array( 'label' => 'Montserrat', 'css' => 'Montserrat, sans-serif', 'google' => 'Montserrat' ),
		'poppins'       => array( 'label' => 'Poppins', 'css' => 'Poppins, sans-serif', 'google' => 'Poppins' ),
		'noto-sans-thai' => array( 'label' => 'Noto Sans Thai', 'css' => '"Noto Sans Thai", sans-serif', 'google' => 'Noto Sans Thai' ),
	);
}

function zola_typography_font_choices( bool $allow_base = false ): array {
	$choices = array();
	if ( $allow_base ) {
		$choices['base'] = esc_html__( 'Same as Base Text Font', 'zola' );
	}
	foreach ( zola_typography_font_catalog() as $key => $font ) {
		$choices[ $key ] = $font['label'];
	}
	return $choices;
}

function zola_sanitize_typography_font_family( mixed $value ): string {
	$value   = is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
	$choices = array_keys( zola_typography_font_catalog() );
	$choices[] = 'base';
	return in_array( $value, $choices, true ) ? $value : 'sans';
}

function zola_typography_css_family( string $font, string $base_font = 'sans' ): string {
	$font  = zola_sanitize_typography_font_family( $font );
	$base_font = zola_sanitize_typography_font_family( $base_font );
	if ( 'base' === $font ) {
		$font = 'base' === $base_font ? 'sans' : $base_font;
	}
	$catalog = zola_typography_font_catalog();
	return $catalog[ $font ]['css'] ?? $catalog['sans']['css'];
}

function zola_typography_google_family( string $font ): string {
	$font    = zola_sanitize_typography_font_family( $font );
	$catalog = zola_typography_font_catalog();
	return $catalog[ $font ]['google'] ?? '';
}

function zola_typography_font_weight_choices( bool $allow_inherit = false ): array {
	$choices = array();
	if ( $allow_inherit ) {
		$choices['inherit'] = esc_html__( 'Default', 'zola' );
	}
	$choices['regular'] = esc_html__( 'regular', 'zola' );
	foreach ( array( 100, 200, 300, 400, 500, 600, 700, 800, 900 ) as $weight ) {
		$choices[ (string) $weight ] = (string) $weight;
	}
	return $choices;
}

function zola_sanitize_typography_font_weight( mixed $value ): string {
	$value   = is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
	$allowed = array( 'inherit', 'regular', '100', '200', '300', '400', '500', '600', '700', '800', '900' );
	return in_array( $value, $allowed, true ) ? $value : 'regular';
}

function zola_sanitize_typography_scale( mixed $value ): int {
	return min( 150, max( 75, absint( $value ) ) );
}

/** Reject CSS that could close the style element used to print custom rules. */
function zola_sanitize_custom_css( mixed $value ): string {
	$css = is_string( $value ) ? $value : '';
	$length = strlen( $css );

	for ( $at = strpos( $css, '<' ); false !== $at; $at = strpos( $css, '<', $at + 1 ) ) {
		$remaining = $length - $at;
		if ( 0 === substr_compare( $css, '</style', $at, min( 7, $remaining ), true ) ) {
			if ( $remaining < 8 || 1 === strspn( $css, " \t\f\r\n/>", $at + 7, 1 ) ) {
				return '';
			}
		}
	}

	return $css;
}

function zola_sanitize_typography_transform( mixed $value ): string {
	return in_array( $value, array( 'uppercase', 'normal' ), true ) ? $value : 'normal';
}

function zola_get_theme_mod_color_value( string $setting_id, string $default ): string {
	$value = get_theme_mod( $setting_id, $default );
	$color = is_string( $value ) ? sanitize_hex_color( $value ) : null;
	return $color ? $color : $default;
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

function zola_sanitize_header_button_case( mixed $value ): string {
	return in_array( $value, array( 'uppercase', 'normal' ), true ) ? $value : 'uppercase';
}

function zola_sanitize_header_button_target( mixed $value ): string {
	return in_array( $value, array( '_self', '_blank' ), true ) ? $value : '_self';
}

function zola_sanitize_header_button_rel( mixed $value ): string {
	if ( ! is_scalar( $value ) ) {
		return '';
	}

	$allowed = array( 'author', 'external', 'help', 'license', 'me', 'next', 'nofollow', 'noopener', 'noreferrer', 'prev', 'search', 'sponsored', 'tag', 'ugc' );
	$tokens  = preg_split( '/\\s+/', strtolower( trim( (string) $value ) ) );
	$tokens  = array_filter( array_map( 'sanitize_key', is_array( $tokens ) ? $tokens : array() ) );
	return implode( ' ', array_values( array_unique( array_intersect( $tokens, $allowed ) ) ) );
}

function zola_sanitize_header_button_radius( mixed $value ): int {
	return min( 99, max( 0, absint( $value ) ) );
}

function zola_sanitize_header_button_depth( mixed $value ): int {
	return min( 12, max( 0, absint( $value ) ) );
}

function zola_sanitize_header_button_icon( mixed $value ): string {
	$icons = array( 'none', 'arrow-right', 'external-link', 'phone', 'email' );
	return in_array( $value, $icons, true ) ? $value : 'none';
}

function zola_sanitize_header_button_color( mixed $value ): string {
	$colors = array( 'plain', 'primary', 'secondary', 'success', 'alert' );
	return in_array( $value, $colors, true ) ? $value : 'primary';
}

function zola_sanitize_header_button_style( mixed $value ): string {
	$styles = array( 'default', 'outline', 'underline', 'shade', 'bevel', 'gloss' );
	return in_array( $value, $styles, true ) ? $value : 'default';
}

function zola_sanitize_header_button_size( mixed $value ): string {
	return in_array( $value, array( 'xs', 's', 'default', 'm', 'l', 'xl' ), true ) ? $value : 'l';
}

function zola_sanitize_header_height( mixed $value ): int {
	return min( 140, max( 48, absint( $value ) ) );
}

function zola_sanitize_sticky_nav_height( mixed $value ): int {
	return min( 120, max( 20, absint( $value ) ) );
}

function zola_sanitize_sticky_style( mixed $value ): string {
	$styles = array( 'jump-down', 'fade', 'shrink' );
	return in_array( $value, $styles, true ) ? $value : 'jump-down';
}

function zola_sanitize_dropdown_content_size( mixed $value ): int {
	return min( 150, max( 70, absint( $value ) ) );
}

function zola_sanitize_dropdown_radius( mixed $value ): string {
	$value = is_scalar( $value ) ? (string) $value : '';
	return in_array( $value, array( '0', '3', '5', '10', '15' ), true ) ? $value : '0';
}

function zola_sanitize_dropdown_link_style( mixed $value ): string {
	$styles = array( 'highlight', 'divider', 'pill' );
	return in_array( $value, $styles, true ) ? $value : 'divider';
}

function zola_sanitize_dropdown_text_style( mixed $value ): string {
	$styles = array( 'simple', 'uppercase' );
	return in_array( $value, $styles, true ) ? $value : 'simple';
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

function zola_sanitize_mobile_menu_icon_style( mixed $value ): string {
	$styles = array( 'plain', 'circle-outline', 'circle-fill', 'square-fill', 'square-outline' );
	return in_array( $value, $styles, true ) ? $value : 'circle-fill';
}

function zola_sanitize_mobile_menu_overlay( mixed $value ): string {
	$positions = array( 'left', 'right', 'fullscreen' );
	return in_array( $value, $positions, true ) ? $value : 'fullscreen';
}

function zola_sanitize_mobile_submenu_behavior( mixed $value ): string {
	return in_array( $value, array( 'open-link', 'toggle-submenu' ), true ) ? $value : 'open-link';
}

function zola_mobile_menu_element_choices(): array {
	return array(
		'search'        => esc_html__( 'Search Form', 'zola' ),
		'main_menu'     => esc_html__( 'Main Menu', 'zola' ),
		'languages'     => esc_html__( 'Languages', 'zola' ),
		'cart'          => esc_html__( 'Cart', 'zola' ),
		'account'       => esc_html__( 'Account', 'zola' ),
		'nav_icon'      => esc_html__( 'Nav Icon', 'zola' ),
		'secondary_menu'=> esc_html__( 'Secondary Menu', 'zola' ),
		'top_bar_menu'  => esc_html__( 'Top Bar Menu', 'zola' ),
	);
}

function zola_mobile_menu_elements_default(): array {
	return array(
		'order'   => array_keys( zola_mobile_menu_element_choices() ),
		'visible' => array(
			'search'         => true,
			'main_menu'      => true,
			'languages'      => true,
			'cart'           => false,
			'account'        => false,
			'nav_icon'       => false,
			'secondary_menu' => false,
			'top_bar_menu'   => false,
		),
	);
}

function zola_sanitize_mobile_menu_elements( mixed $value ): array {
	$defaults = zola_mobile_menu_elements_default();
	if ( ! is_array( $value ) ) {
		return $defaults;
	}

	$allowed = array_keys( zola_mobile_menu_element_choices() );
	$order   = array();
	if ( isset( $value['order'] ) && is_array( $value['order'] ) ) {
		foreach ( $value['order'] as $item ) {
			if ( ! is_scalar( $item ) ) {
				continue;
			}
			$item = sanitize_key( (string) $item );
			if ( in_array( $item, $allowed, true ) && ! in_array( $item, $order, true ) ) {
				$order[] = $item;
			}
		}
	}
	$order = array_merge( $order, array_values( array_diff( $allowed, $order ) ) );

	$visible = $defaults['visible'];
	if ( isset( $value['visible'] ) && is_array( $value['visible'] ) ) {
		foreach ( $allowed as $item ) {
			if ( array_key_exists( $item, $value['visible'] ) && ( is_scalar( $value['visible'][ $item ] ) || is_bool( $value['visible'][ $item ] ) ) ) {
				$visible[ $item ] = wp_validate_boolean( $value['visible'][ $item ] );
			}
		}
	}

	return array(
		'order'   => $order,
		'visible' => $visible,
	);
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
			'top'    => array( 'left' => array(), 'center' => array(), 'right' => array() ),
			'main'   => array( 'left' => array( 'logo' ), 'center' => array( 'menu' ), 'right' => array( 'button' ) ),
			'bottom' => array( 'left' => array(), 'center' => array(), 'right' => array() ),
	);
	$mobile_rows = array(
		'top'    => array( 'left' => array(), 'center' => array(), 'right' => array() ),
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
				array( 'right' => array( 'social' ) ),
				array( 'left' => array( 'logo' ), 'center' => array( 'menu' ), 'right' => array( 'button' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array(),
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
				array( 'right' => array( 'social' ) ),
				array( 'left' => array( 'logo' ), 'right' => array( 'menu' ) ),
				array( 'center' => array( 'button' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array(),
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
				array()
			),
			'mobile'      => zola_header_builder_make_rows(
				array(),
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
				array(),
				array( 'left' => array( 'logo' ), 'center' => array( 'menu' ), 'right' => array( 'button' ) ),
				array( 'right' => array( 'social' ) )
			),
			'mobile'      => zola_header_builder_make_rows(
				array(),
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

	$allowed_modules = array( 'logo', 'menu', 'button', 'button_2', 'search', 'social', 'html_1', 'html_2', 'html_3', 'html_4', 'html_5', 'account', 'cart' );
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

function zola_add_typography_subheading_control( WP_Customize_Manager $wp_customize, string $control_id, string $label, int $priority, string $section_id = 'zola_style_typography', string $description = '' ): void {
	$wp_customize->add_control( new Zola_Typography_Subheading_Customize_Control( $wp_customize, $control_id, array(
		'settings' => array(),
		'section'  => $section_id,
		'label'    => $label,
		'description' => $description,
		'priority' => $priority,
	) ) );
}

function zola_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_panel( 'zola_header_panel', array(
		'title'    => esc_html__( 'Header', 'zola' ),
		'priority' => 30,
	) );
	$wp_customize->add_panel( 'zola_style', array(
		'title'       => esc_html__( 'Style', 'zola' ),
		'description' => esc_html__( 'Set the shared colors, typography and button shape used across the theme.', 'zola' ),
		'priority'    => 40,
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
		'zola_style_typography' => array(
			'title'    => esc_html__( 'Typography', 'zola' ),
			'panel'    => 'zola_style',
			'priority' => 10,
		),
		'zola_style_buttons' => array(
			'title'    => esc_html__( 'Buttons', 'zola' ),
			'panel'    => 'zola_style',
			'priority' => 30,
		),
		'zola_style_custom_css' => array(
			'title'    => esc_html__( 'Custom CSS', 'zola' ),
			'panel'    => 'zola_style',
			'priority' => 40,
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

	$custom_css_controls = array(
		'zola_custom_css'        => array( 'label' => esc_html__( 'Custom CSS', 'zola' ), 'priority' => 10 ),
		'zola_custom_tablet_css' => array( 'label' => esc_html__( 'Custom Tablet CSS', 'zola' ), 'priority' => 20 ),
		'zola_custom_mobile_css' => array( 'label' => esc_html__( 'Custom Mobile CSS', 'zola' ), 'priority' => 30 ),
	);
	foreach ( $custom_css_controls as $setting_id => $control ) {
		$wp_customize->add_setting( $setting_id, array(
			'default'           => '',
			'sanitize_callback' => 'zola_sanitize_custom_css',
			'transport'         => 'refresh',
			'capability'        => 'edit_theme_options',
		) );

		$control_args = array(
			'label'    => $control['label'],
			'section'  => 'zola_style_custom_css',
			'settings' => array( 'default' => $setting_id ),
			'priority' => $control['priority'],
		);
		if ( class_exists( 'WP_Customize_Code_Editor_Control' ) ) {
			$control_args['code_type'] = 'text/css';
			$wp_customize->add_control( new WP_Customize_Code_Editor_Control( $wp_customize, $setting_id, $control_args ) );
		} else {
			$control_args['type'] = 'textarea';
			$wp_customize->add_control( $setting_id, $control_args );
		}
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
	if ( ! class_exists( 'Zola_Typography_Subheading_Customize_Control' ) ) {
		class Zola_Typography_Subheading_Customize_Control extends WP_Customize_Control {
			public $type = 'zola-typography-subheading';

			public function render_content(): void {
				printf( '<h4 class="zola-typography-subheading">%s</h4>', esc_html( $this->label ) );
				if ( $this->description ) {
					printf( '<p class="description">%s</p>', wp_kses_post( $this->description ) );
				}
			}
		}
	}
	if ( ! class_exists( 'Zola_Typography_Cache_Customize_Control' ) ) {
		class Zola_Typography_Cache_Customize_Control extends WP_Customize_Control {
			public $type = 'zola-typography-cache';

			public function render_content(): void {
				?>
				<button type="button" class="button zola-typography-cache__button" data-zola-clear-typography-cache><?php esc_html_e( 'Clear typography cache', 'zola' ); ?></button>
				<p class="description zola-typography-cache__status" data-zola-typography-cache-status aria-live="polite"></p>
				<?php
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
	if ( ! class_exists( 'Zola_Header_Button_Preview_Customize_Control' ) ) {
		class Zola_Header_Button_Preview_Customize_Control extends WP_Customize_Control {
			public $type = 'zola-header-button-preview';

			public function render_content(): void {
				?>
				<div class="zola-header-button-preview">
					<span class="zola-header-button-preview__sample"><?php esc_html_e( 'Link', 'zola' ); ?></span>
				</div>
				<?php
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
				<fieldset class="zola-header-visual-choice zola-header-visual-choice--<?php echo esc_attr( $this->visual_type ); ?>" data-zola-visual-choice data-zola-setting="<?php echo esc_attr( $setting_id ); ?>" data-choice-count="<?php echo esc_attr( (string) count( $this->choices ) ); ?>">
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
									<?php elseif ( 'dropdown-link-style' === $this->visual_type ) : ?>
										<?php
										$dropdown_icon_index = array_search( $value, array( 'highlight', 'divider', 'pill' ), true );
										$dropdown_icon_index = false === $dropdown_icon_index ? 1 : $dropdown_icon_index + 1;
										?>
										<img src="<?php echo esc_url( ZOLA_URI . '/assets/images/dropdown-style-' . absint( $dropdown_icon_index ) . '.svg' ); ?>" width="72" height="52" alt="" decoding="async">
					<?php elseif ( in_array( $this->visual_type, array( 'logo-position', 'mobile-logo-position' ), true ) ) : ?>
						<span class="zola-logo-position-preview"><i></i><b></b></span>
					<?php elseif ( 'menu-icon-style' === $this->visual_type ) : ?>
						<span class="zola-mobile-menu-icon-preview zola-mobile-menu-icon-preview--<?php echo esc_attr( sanitize_html_class( $value ) ); ?>"><i></i><i></i><i></i></span>
					<?php elseif ( 'mobile-overlay' === $this->visual_type ) : ?>
						<span class="zola-mobile-overlay-preview zola-mobile-overlay-preview--<?php echo esc_attr( sanitize_html_class( $value ) ); ?>"><i></i><b></b></span>
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
	if ( ! class_exists( 'Zola_Mobile_Menu_Elements_Customize_Control' ) ) {
		class Zola_Mobile_Menu_Elements_Customize_Control extends WP_Customize_Control {
			public $type = 'zola-mobile-menu-elements';

			public function render_content(): void {
				$setting_id = $this->setting instanceof WP_Customize_Setting ? $this->setting->id : '';
				$value      = zola_sanitize_mobile_menu_elements( $this->value() );
				?>
				<fieldset class="zola-mobile-menu-elements" data-zola-mobile-menu-elements data-zola-setting="<?php echo esc_attr( $setting_id ); ?>">
					<legend class="customize-control-title"><?php echo esc_html( $this->label ); ?></legend>
					<ul class="zola-mobile-menu-elements__list">
						<?php foreach ( $value['order'] as $element_id ) : ?>
							<?php if ( ! isset( $this->choices[ $element_id ] ) ) { continue; } ?>
							<?php $is_visible = ! empty( $value['visible'][ $element_id ] ); ?>
							<li class="zola-mobile-menu-elements__item<?php echo $is_visible ? ' is-visible' : ' is-hidden'; ?>" draggable="true" tabindex="0" data-zola-mobile-element="<?php echo esc_attr( $element_id ); ?>">
								<button type="button" class="zola-mobile-menu-elements__visibility" data-zola-mobile-visibility aria-pressed="<?php echo $is_visible ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( $is_visible ? __( 'Hide %s', 'zola' ) : __( 'Show %s', 'zola' ), $this->choices[ $element_id ] ) ); ?>"><?php echo $is_visible ? esc_html__( 'Shown', 'zola' ) : esc_html__( 'Hidden', 'zola' ); ?></button>
								<span class="zola-mobile-menu-elements__name"><?php echo esc_html( $this->choices[ $element_id ] ); ?></span>
								<span class="zola-mobile-menu-elements__handle" aria-hidden="true">⠿</span>
							</li>
						<?php endforeach; ?>
					</ul>
				</fieldset>
				<?php
			}
		}
	}
	$header_section_notes = array(
		'zola_header'          => esc_html__( 'Use the Header Builder in the preview area to add, remove and position header elements.', 'zola' ),
		'zola_header_bottom'   => esc_html__( 'Use Header Builder in the preview area to arrange elements in the bottom row.', 'zola' ),
		'zola_header_mobile'   => esc_html__( 'Choose Mobile / Tablet in Header Builder to arrange the mobile menu and header elements.', 'zola' ),
		'zola_header_account'  => esc_html__( 'Add or remove the Account element and choose its position in Header Builder.', 'zola' ),
		'zola_header_cart'     => esc_html__( 'Add or remove the Cart element and choose its position in Header Builder. Cart requires WooCommerce.', 'zola' ),
		'zola_header_search'   => esc_html__( 'Add or remove the Search element and choose its position in Header Builder.', 'zola' ),
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

	// Dropdown presentation controls.
	zola_add_header_group_control( $wp_customize, 'zola_dropdown_all_group', esc_html__( 'All Dropdowns', 'zola' ), 5, 'zola_header_dropdown' );
	$dropdown_all_controls = array(
		'zola_dropdown_background_color' => array(
			'label'    => esc_html__( 'Background color', 'zola' ),
			'type'     => 'color',
			'default'  => '',
			'sanitize' => 'sanitize_hex_color',
			'priority' => 6,
		),
		'zola_dropdown_add_border' => array(
			'label'    => esc_html__( 'Add border', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
			'priority' => 7,
		),
		'zola_dropdown_border_color' => array(
			'label'    => esc_html__( 'Border Color', 'zola' ),
			'type'     => 'color',
			'default'  => '',
			'sanitize' => 'sanitize_hex_color',
			'priority' => 8,
		),
		'zola_dropdown_add_shadow' => array(
			'label'    => esc_html__( 'Add shadow', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
			'priority' => 9,
		),
		'zola_dropdown_content_size' => array(
			'label'       => esc_html__( 'Dropdown Content Size (%)', 'zola' ),
			'type'        => 'range',
			'default'     => 100,
			'sanitize'    => 'zola_sanitize_dropdown_content_size',
			'input_attrs' => array( 'min' => 70, 'max' => 150, 'step' => 1, 'data-default' => 100 ),
			'priority'    => 10,
		),
	);
	foreach ( $dropdown_all_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_header_dropdown', $setting_id, $control );
	}
	zola_add_header_visual_control( $wp_customize, 'zola_dropdown_radius', array(
		'label'       => esc_html__( 'Dropdown radius', 'zola' ),
		'default'     => '0',
		'sanitize'    => 'zola_sanitize_dropdown_radius',
		'visual_type' => 'scale',
		'choices'     => array( '0' => '0px', '3' => '3px', '5' => '5px', '10' => '10px', '15' => '15px' ),
		'priority'    => 11,
		'section'     => 'zola_header_dropdown',
	) );

	zola_add_header_group_control( $wp_customize, 'zola_dropdown_default_group', esc_html__( 'Default Dropdowns', 'zola' ), 20, 'zola_header_dropdown' );
	zola_add_customizer_control( $wp_customize, 'zola_header_dropdown', 'zola_dropdown_add_arrow', array(
		'label'       => esc_html__( 'Add arrow', 'zola' ),
		'description' => esc_html__( 'Shows a pointer from the dropdown toward its parent menu item.', 'zola' ),
		'type'        => 'checkbox',
		'default'     => true,
		'sanitize'    => 'wp_validate_boolean',
		'priority'    => 21,
	) );
	zola_add_header_visual_control( $wp_customize, 'zola_dropdown_link_style', array(
		'label'       => esc_html__( 'Link Style', 'zola' ),
		'default'     => 'divider',
		'sanitize'    => 'zola_sanitize_dropdown_link_style',
		'visual_type' => 'dropdown-link-style',
		'choices'     => array(
			'highlight' => esc_html__( 'Highlight', 'zola' ),
			'divider'   => esc_html__( 'Divider', 'zola' ),
			'pill'      => esc_html__( 'Pill', 'zola' ),
		),
		'priority'    => 22,
		'section'     => 'zola_header_dropdown',
	) );
	zola_add_header_visual_control( $wp_customize, 'zola_dropdown_text_color', array(
		'label'       => esc_html__( 'Text Color', 'zola' ),
		'default'     => 'dark',
		'sanitize'    => 'zola_sanitize_header_text_mode',
		'visual_type' => 'scale',
		'choices'     => array( 'dark' => esc_html__( 'Dark', 'zola' ), 'light' => esc_html__( 'Light', 'zola' ) ),
		'priority'    => 23,
		'section'     => 'zola_header_dropdown',
	) );
	zola_add_header_visual_control( $wp_customize, 'zola_dropdown_text_style', array(
		'label'       => esc_html__( 'Text Style', 'zola' ),
		'default'     => 'simple',
		'sanitize'    => 'zola_sanitize_dropdown_text_style',
		'visual_type' => 'scale',
		'choices'     => array( 'simple' => esc_html__( 'Simple', 'zola' ), 'uppercase' => esc_html__( 'UPPERCASE', 'zola' ) ),
		'priority'    => 24,
		'section'     => 'zola_header_dropdown',
	) );

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
		'input_attrs' => array( 'min' => 48, 'max' => 140, 'step' => 1, 'data-default' => 70 ),
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
		'input_attrs' => array( 'min' => 12, 'max' => 40, 'step' => 1, 'data-default' => 16 ),
		'priority'    => 26,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_header_nav_push', array(
		'label'       => esc_html__( 'Nav Push', 'zola' ),
		'type'        => 'range',
		'default'     => 9,
		'sanitize'    => 'zola_sanitize_header_nav_push',
		'input_attrs' => array( 'min' => 0, 'max' => 40, 'step' => 1, 'data-default' => 9 ),
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
		'input_attrs' => array( 'min' => 48, 'max' => 140, 'step' => 1, 'data-default' => 70 ),
		'priority'    => 42,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header', 'zola_transparent_header_background_color', array(
		'label'    => esc_html__( 'Transparent Header Background Color', 'zola' ),
		'type'     => 'color',
		'default'  => '',
		'sanitize' => 'sanitize_hex_color',
		'priority' => 43,
	) );
	$style_color_groups = array(
		'zola_style_colors_main_group'    => array( 'label' => esc_html__( 'Main Colors', 'zola' ), 'priority' => 5 ),
		'zola_style_colors_type_group'    => array( 'label' => esc_html__( 'Type', 'zola' ), 'priority' => 20 ),
		'zola_style_colors_links_group'   => array( 'label' => esc_html__( 'Links', 'zola' ), 'priority' => 30 ),
		'zola_style_colors_tooltip_group' => array( 'label' => esc_html__( 'Tooltip colors', 'zola' ), 'priority' => 40 ),
	);
	foreach ( $style_color_groups as $control_id => $group ) {
		zola_add_header_group_control( $wp_customize, $control_id, $group['label'], $group['priority'], 'colors' );
	}

	$style_controls = array(
		'zola_accent_color' => array(
			'label'       => esc_html__( 'Primary Color', 'zola' ),
			'description' => esc_html__( 'Change primary color.', 'zola' ),
			'type'        => 'color',
			'default'     => '#2563eb',
			'sanitize'    => 'sanitize_hex_color',
			'priority'    => 6,
		),
		'zola_secondary_color' => array(
			'label'       => esc_html__( 'Secondary Color', 'zola' ),
			'description' => esc_html__( 'Change secondary color.', 'zola' ),
			'type'        => 'color',
			'default'     => '#202124',
			'sanitize'    => 'sanitize_hex_color',
			'priority'    => 7,
		),
		'zola_success_color' => array(
			'label'       => esc_html__( 'Success Color', 'zola' ),
			'description' => esc_html__( 'Change the success color. Used for global success messages.', 'zola' ),
			'type'        => 'color',
			'default'     => '#198754',
			'sanitize'    => 'sanitize_hex_color',
			'priority'    => 8,
		),
		'zola_alert_color' => array(
			'label'       => esc_html__( 'Alert Color', 'zola' ),
			'description' => esc_html__( 'Change the alert color. Used for global error messages.', 'zola' ),
			'type'        => 'color',
			'default'     => '#dc3545',
			'sanitize'    => 'sanitize_hex_color',
			'priority'    => 9,
		),
		'zola_ink_color' => array(
			'label'       => esc_html__( 'Dark surface color', 'zola' ),
			'description' => esc_html__( 'Used for dark surfaces and button text.', 'zola' ),
			'type'        => 'color',
			'default'     => '#202124',
			'sanitize'    => 'sanitize_hex_color',
			'priority'    => 10,
		),
		'zola_paper_color' => array(
			'label'       => esc_html__( 'Site background color', 'zola' ),
			'description' => esc_html__( 'Used for the main site background.', 'zola' ),
			'type'        => 'color',
			'default'     => '#ffffff',
			'sanitize'    => 'sanitize_hex_color',
			'priority'    => 11,
		),
		'zola_text_color' => array(
			'label'       => esc_html__( 'Base Color', 'zola' ),
			'description' => esc_html__( 'Used for all normal text.', 'zola' ),
			'type'        => 'color',
			'default'     => '#202126',
			'sanitize'    => 'sanitize_hex_color',
			'priority'    => 21,
		),
		'zola_headline_color' => array(
			'label'       => esc_html__( 'Headline Color', 'zola' ),
			'description' => esc_html__( 'Used for all headlines on white backgrounds (H1, H2, H3, etc.).', 'zola' ),
			'type'        => 'color',
			'default'     => '#202126',
			'sanitize'    => 'sanitize_hex_color',
			'priority'    => 22,
		),
		'zola_divider_color' => array(
			'label'       => esc_html__( 'Divider Color', 'zola' ),
			'description' => esc_html__( 'Used for dividers and borders.', 'zola' ),
			'type'        => 'color',
			'default'     => '#dcdcde',
			'sanitize'    => 'sanitize_hex_color',
			'priority'    => 23,
		),
		'zola_link_color' => array(
			'label'    => esc_html__( 'Link Colors', 'zola' ),
			'type'     => 'color',
			'default'  => '#2563eb',
			'sanitize' => 'sanitize_hex_color',
			'priority' => 31,
		),
		'zola_link_hover_color' => array(
			'label'    => esc_html__( 'Link Colors :hover', 'zola' ),
			'type'     => 'color',
			'default'  => '#202124',
			'sanitize' => 'sanitize_hex_color',
			'priority' => 32,
		),
		'zola_widget_link_color' => array(
			'label'    => esc_html__( 'Widget Link Colors', 'zola' ),
			'type'     => 'color',
			'default'  => '',
			'sanitize' => 'sanitize_hex_color',
			'priority' => 33,
		),
		'zola_widget_link_hover_color' => array(
			'label'    => esc_html__( 'Widget Link Colors :hover', 'zola' ),
			'type'     => 'color',
			'default'  => '',
			'sanitize' => 'sanitize_hex_color',
			'priority' => 34,
		),
		'zola_tooltip_color' => array(
			'label'    => esc_html__( 'Tooltip color', 'zola' ),
			'type'     => 'color',
			'default'  => '#ffffff',
			'sanitize' => 'sanitize_hex_color',
			'priority' => 41,
		),
		'zola_tooltip_background_color' => array(
			'label'    => esc_html__( 'Tooltip background color', 'zola' ),
			'type'     => 'color',
			'default'  => '#111827',
			'sanitize' => 'sanitize_hex_color',
			'priority' => 42,
		),
		'zola_font_family' => array(
			'label'    => esc_html__( 'Font Family', 'zola' ),
			'type'     => 'select',
			'default'  => 'sans',
			'sanitize' => 'zola_sanitize_typography_font_family',
			'choices'  => zola_typography_font_choices(),
			'priority' => 22,
		),
		'zola_button_radius' => array(
			'label'      => esc_html__( 'Button corner radius (px)', 'zola' ),
			'type'       => 'number',
			'default'    => 3,
			'sanitize'   => 'absint',
			'input_attrs' => array( 'min' => 0, 'max' => 20, 'step' => 1 ),
		),
	);
	$style_control_sections = array(
		'zola_accent_color'          => 'colors',
		'zola_secondary_color'       => 'colors',
		'zola_success_color'         => 'colors',
		'zola_alert_color'           => 'colors',
		'zola_ink_color'             => 'colors',
		'zola_paper_color'           => 'colors',
		'zola_text_color'            => 'colors',
		'zola_headline_color'        => 'colors',
		'zola_divider_color'         => 'colors',
		'zola_link_color'            => 'colors',
		'zola_link_hover_color'      => 'colors',
		'zola_widget_link_color'     => 'colors',
		'zola_widget_link_hover_color' => 'colors',
		'zola_tooltip_color'         => 'colors',
		'zola_tooltip_background_color' => 'colors',
		'zola_font_family'           => 'zola_style_typography',
		'zola_button_radius'         => 'zola_style_buttons',
	);
	foreach ( $style_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, $style_control_sections[ $setting_id ], $setting_id, $control );
	}

	$typography_groups = array(
		'zola_style_typography_headlines_group'  => array( 'label' => esc_html__( 'Headlines', 'zola' ), 'priority' => 5 ),
		'zola_style_typography_base_group'       => array( 'label' => esc_html__( 'Base', 'zola' ), 'priority' => 20 ),
		'zola_style_typography_navigation_group' => array( 'label' => esc_html__( 'Navigation', 'zola' ), 'priority' => 30 ),
		'zola_style_typography_alt_group'        => array( 'label' => esc_html__( 'Alt Fonts', 'zola' ), 'priority' => 40 ),
		'zola_style_typography_transforms_group' => array( 'label' => esc_html__( 'Text Transforms', 'zola' ), 'priority' => 50 ),
	);
	foreach ( $typography_groups as $control_id => $group ) {
		zola_add_header_group_control( $wp_customize, $control_id, $group['label'], $group['priority'], 'zola_style_typography' );
	}

	$typography_subheadings = array(
		'zola_typography_headlines_font_heading'  => array( 'label' => esc_html__( 'Font', 'zola' ), 'description' => esc_html__( 'This is the font for all H1–H6 titles.', 'zola' ), 'priority' => 6 ),
		'zola_typography_base_font_heading'       => array( 'label' => esc_html__( 'Base Text Font', 'zola' ), 'priority' => 21 ),
		'zola_typography_navigation_font_heading' => array( 'label' => esc_html__( 'Font', 'zola' ), 'priority' => 31 ),
		'zola_typography_alt_font_heading'        => array( 'label' => esc_html__( 'Alt font (.alt-font)', 'zola' ), 'description' => esc_html__( 'Alt font can be selected in the Format dropdown in Text Editor.', 'zola' ), 'priority' => 41 ),
	);
	foreach ( $typography_subheadings as $control_id => $subheading ) {
		zola_add_typography_subheading_control( $wp_customize, $control_id, $subheading['label'], $subheading['priority'], 'zola_style_typography', $subheading['description'] ?? '' );
	}

	$typography_controls = array(
		'zola_typography_disable_fonts' => array(
			'label'    => esc_html__( 'Disable fonts', 'zola' ),
			'type'     => 'checkbox',
			'default'  => false,
			'sanitize' => 'wp_validate_boolean',
			'priority' => 1,
		),
		'zola_typography_google_fonts_cdn' => array(
			'label'       => esc_html__( 'Load Google Fonts from CDN', 'zola' ),
			'description' => wp_kses_post( __( 'Enabling this is not recommended due to GDPR regulations. <a href="https://developers.google.com/fonts/faq/privacy" target="_blank" rel="noopener noreferrer">Learn more</a>', 'zola' ) ),
			'type'        => 'checkbox',
			'default'     => false,
			'sanitize'    => 'wp_validate_boolean',
			'priority'    => 2,
		),
		'zola_typography_base_font_weight' => array(
			'label'    => esc_html__( 'Variant', 'zola' ),
			'type'     => 'select',
			'default'  => 'regular',
			'sanitize' => 'zola_sanitize_typography_font_weight',
			'choices'  => zola_typography_font_weight_choices(),
			'priority' => 23,
		),
		'zola_typography_base_font_size' => array(
			'label'       => esc_html__( 'Base Font Size', 'zola' ),
			'description' => esc_html__( 'Set base font size in %.', 'zola' ),
			'type'        => 'range',
			'default'     => 100,
			'sanitize'    => 'zola_sanitize_typography_scale',
			'input_attrs' => array( 'min' => 75, 'max' => 150, 'step' => 1, 'data-default' => 100 ),
			'priority'    => 24,
		),
		'zola_typography_mobile_base_font_size' => array(
			'label'       => esc_html__( 'Mobile Base Font Size', 'zola' ),
			'description' => esc_html__( 'Set mobile base font size in %.', 'zola' ),
			'type'        => 'range',
			'default'     => 100,
			'sanitize'    => 'zola_sanitize_typography_scale',
			'input_attrs' => array( 'min' => 75, 'max' => 150, 'step' => 1, 'data-default' => 100 ),
			'priority'    => 25,
		),
		'zola_typography_headlines_font_family' => array(
			'label'    => esc_html__( 'Font Family', 'zola' ),
			'type'     => 'select',
			'default'  => 'base',
			'sanitize' => 'zola_sanitize_typography_font_family',
			'choices'  => zola_typography_font_choices( true ),
			'priority' => 7,
		),
		'zola_typography_headlines_font_weight' => array(
			'label'    => esc_html__( 'Variant', 'zola' ),
			'type'     => 'select',
			'default'  => 'inherit',
			'sanitize' => 'zola_sanitize_typography_font_weight',
			'choices'  => zola_typography_font_weight_choices( true ),
			'priority' => 8,
		),
		'zola_typography_navigation_font_family' => array(
			'label'    => esc_html__( 'Font Family', 'zola' ),
			'type'     => 'select',
			'default'  => 'base',
			'sanitize' => 'zola_sanitize_typography_font_family',
			'choices'  => zola_typography_font_choices( true ),
			'priority' => 32,
		),
		'zola_typography_navigation_font_weight' => array(
			'label'    => esc_html__( 'Variant', 'zola' ),
			'type'     => 'select',
			'default'  => 'inherit',
			'sanitize' => 'zola_sanitize_typography_font_weight',
			'choices'  => zola_typography_font_weight_choices( true ),
			'priority' => 33,
		),
		'zola_typography_alt_font_family' => array(
			'label'    => esc_html__( 'Font Family', 'zola' ),
			'type'     => 'select',
			'default'  => 'base',
			'sanitize' => 'zola_sanitize_typography_font_family',
			'choices'  => zola_typography_font_choices( true ),
			'priority' => 42,
		),
		'zola_typography_alt_font_weight' => array(
			'label'    => esc_html__( 'Variant', 'zola' ),
			'type'     => 'select',
			'default'  => 'inherit',
			'sanitize' => 'zola_sanitize_typography_font_weight',
			'choices'  => zola_typography_font_weight_choices( true ),
			'priority' => 43,
		),
	);
	foreach ( $typography_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_style_typography', $setting_id, $control );
	}
	$wp_customize->add_control( new Zola_Typography_Cache_Customize_Control( $wp_customize, 'zola_typography_cache_control', array(
		'settings' => array(),
		'section'  => 'zola_style_typography',
		'priority' => 3,
	) ) );

	$typography_transforms = array(
		'zola_typography_transform_breadcrumbs'    => array( 'label' => esc_html__( 'Breadcrumbs', 'zola' ), 'priority' => 51 ),
		'zola_typography_transform_buttons'        => array( 'label' => esc_html__( 'Buttons', 'zola' ), 'priority' => 52 ),
		'zola_typography_transform_navigation'     => array( 'label' => esc_html__( 'Navigation / Tabs', 'zola' ), 'priority' => 53 ),
		'zola_typography_transform_section_titles' => array( 'label' => esc_html__( 'Section Titles', 'zola' ), 'priority' => 54 ),
		'zola_typography_transform_widget_titles'  => array( 'label' => esc_html__( 'Widget Titles', 'zola' ), 'priority' => 55 ),
	);
	foreach ( $typography_transforms as $setting_id => $transform ) {
		zola_add_header_visual_control( $wp_customize, $setting_id, array(
			'label'       => $transform['label'],
			'default'     => 'normal',
			'sanitize'    => 'zola_sanitize_typography_transform',
			'visual_type' => 'scale',
			'choices'     => array( 'uppercase' => esc_html__( 'UPPERCASE', 'zola' ), 'normal' => esc_html__( 'Normal', 'zola' ) ),
			'priority'    => $transform['priority'],
			'section'     => 'zola_style_typography',
		) );
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

	$button_instances = array(
		1 => array(
			'label_id' => 'zola_header_cta_label',
			'link_id'  => 'zola_header_cta_url',
			'prefix'   => 'zola_header_button_1_',
			'title'    => esc_html__( 'Button 1', 'zola' ),
			'text'     => 'Button 1',
			'priority' => 5,
		),
		2 => array(
			'label_id' => 'zola_header_button_2_text',
			'link_id'  => 'zola_header_button_2_link',
			'prefix'   => 'zola_header_button_2_',
			'title'    => esc_html__( 'Button 2', 'zola' ),
			'text'     => 'Button 2',
			'priority' => 35,
		),
	);
	foreach ( $button_instances as $number => $button ) {
		zola_add_header_group_control( $wp_customize, 'zola_header_button_' . $number . '_group', $button['title'], $button['priority'], 'zola_header_buttons' );
		$priority = $button['priority'] + 5;
		$visual_controls = array(
			'case' => array(
				'label'       => esc_html__( 'Letter case', 'zola' ),
				'default'     => 'uppercase',
				'sanitize'    => 'zola_sanitize_header_button_case',
				'visual_type' => 'scale',
				'choices'     => array( 'uppercase' => 'ABC', 'normal' => 'Abc' ),
			),
			'color' => array(
				'label'       => esc_html__( 'Color', 'zola' ),
				'default'     => 'primary',
				'sanitize'    => 'zola_sanitize_header_button_color',
				'visual_type' => 'scale',
				'choices'     => array(
					'plain'     => esc_html__( 'Plain', 'zola' ),
					'primary'   => esc_html__( 'Primary', 'zola' ),
					'secondary' => esc_html__( 'Secondary', 'zola' ),
					'success'   => esc_html__( 'Success', 'zola' ),
					'alert'     => esc_html__( 'Alert', 'zola' ),
				),
			),
			'style' => array(
				'label'       => esc_html__( 'Style', 'zola' ),
				'default'     => 'default',
				'sanitize'    => 'zola_sanitize_header_button_style',
				'visual_type' => 'scale',
				'choices'     => array(
					'default'   => esc_html__( 'Default', 'zola' ),
					'outline'   => esc_html__( 'Outline', 'zola' ),
					'underline' => esc_html__( 'Underline', 'zola' ),
					'shade'     => esc_html__( 'Shade', 'zola' ),
					'bevel'     => esc_html__( 'Bevel', 'zola' ),
					'gloss'     => esc_html__( 'Gloss', 'zola' ),
				),
			),
			'size' => array(
				'label'       => esc_html__( 'Size', 'zola' ),
				'default'     => 'l',
				'sanitize'    => 'zola_sanitize_header_button_size',
				'visual_type' => 'scale',
				'choices'     => array(
					'xs'      => 'XS',
					's'       => 'S',
					'default' => esc_html__( 'Default', 'zola' ),
					'm'       => 'M',
					'l'       => 'L',
					'xl'      => 'XL',
				),
			),
		);
		$definitions = array(
			array( 'kind' => 'field', 'id' => $button['label_id'], 'control' => array(
				'label' => esc_html__( 'Text', 'zola' ), 'type' => 'text', 'default' => $button['text'], 'sanitize' => 'sanitize_text_field',
			) ),
			array( 'kind' => 'visual', 'id' => $button['prefix'] . 'case', 'control' => $visual_controls['case'] ),
			array( 'kind' => 'field', 'id' => $button['link_id'], 'control' => array(
				'label' => esc_html__( 'Link', 'zola' ), 'type' => 'url', 'default' => '', 'sanitize' => 'esc_url_raw',
			) ),
			array( 'kind' => 'field', 'id' => $button['prefix'] . 'target', 'control' => array(
				'label' => esc_html__( 'Target', 'zola' ), 'type' => 'select', 'default' => '_self', 'sanitize' => 'zola_sanitize_header_button_target',
				'choices' => array( '_self' => esc_html__( 'Same window', 'zola' ), '_blank' => esc_html__( 'New window', 'zola' ) ),
			) ),
			array( 'kind' => 'field', 'id' => $button['prefix'] . 'rel', 'control' => array(
				'label' => esc_html__( 'Rel', 'zola' ), 'type' => 'text', 'default' => '', 'sanitize' => 'zola_sanitize_header_button_rel',
			) ),
			array( 'kind' => 'field', 'id' => $button['prefix'] . 'radius', 'control' => array(
				'label' => esc_html__( 'Radius (px)', 'zola' ), 'type' => 'number', 'default' => 99, 'sanitize' => 'zola_sanitize_header_button_radius',
				'input_attrs' => array( 'min' => 0, 'max' => 99, 'step' => 1 ),
			) ),
			array( 'kind' => 'field', 'id' => $button['prefix'] . 'icon', 'control' => array(
				'label' => esc_html__( 'Icon', 'zola' ), 'type' => 'select', 'default' => 'none', 'sanitize' => 'zola_sanitize_header_button_icon',
				'choices' => array(
					'none'          => esc_html__( 'None', 'zola' ),
					'arrow-right'   => esc_html__( 'Arrow right', 'zola' ),
					'external-link' => esc_html__( 'External link', 'zola' ),
					'phone'         => esc_html__( 'Phone', 'zola' ),
					'email'         => esc_html__( 'Email', 'zola' ),
				),
			) ),
			array( 'kind' => 'visual', 'id' => $button['prefix'] . 'color', 'control' => $visual_controls['color'] ),
			array( 'kind' => 'visual', 'id' => $button['prefix'] . 'style', 'control' => $visual_controls['style'] ),
			array( 'kind' => 'preview', 'id' => 'zola_header_button_' . $number . '_preview_control' ),
			array( 'kind' => 'visual', 'id' => $button['prefix'] . 'size', 'control' => $visual_controls['size'] ),
			array( 'kind' => 'field', 'id' => $button['prefix'] . 'depth', 'control' => array(
				'label' => esc_html__( 'Depth', 'zola' ), 'type' => 'range', 'default' => 0, 'sanitize' => 'zola_sanitize_header_button_depth',
				'input_attrs' => array( 'min' => 0, 'max' => 12, 'step' => 1, 'data-default' => 0 ),
			) ),
			array( 'kind' => 'field', 'id' => $button['prefix'] . 'depth_hover', 'control' => array(
				'label' => esc_html__( 'Depth :hover', 'zola' ), 'type' => 'range', 'default' => 0, 'sanitize' => 'zola_sanitize_header_button_depth',
				'input_attrs' => array( 'min' => 0, 'max' => 12, 'step' => 1, 'data-default' => 0 ),
			) ),
		);
		foreach ( $definitions as $definition ) {
			if ( 'preview' === $definition['kind'] ) {
				$wp_customize->add_control( new Zola_Header_Button_Preview_Customize_Control( $wp_customize, $definition['id'], array(
					'settings'      => array(),
					'section'       => 'zola_header_buttons',
					'priority'      => $priority++,
				) ) );
				continue;
			}
			$control = $definition['control'];
			$control['priority'] = $priority++;
			if ( 'visual' === $definition['kind'] ) {
				$control['section'] = 'zola_header_buttons';
				zola_add_header_visual_control( $wp_customize, $definition['id'], $control );
			} else {
				zola_add_customizer_control( $wp_customize, 'zola_header_buttons', $definition['id'], $control );
			}
		}
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
		'input_attrs' => array( 'min' => 24, 'max' => 100, 'step' => 1, 'data-default' => 40 ),
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
		'input_attrs' => array( 'min' => 12, 'max' => 40, 'step' => 1, 'data-default' => 16 ),
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
		'input_attrs' => array( 'min' => 10, 'max' => 100, 'step' => 1, 'data-default' => 10 ),
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
		'input_attrs' => array( 'min' => 12, 'max' => 40, 'step' => 1, 'data-default' => 16 ),
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

	// Mobile header and menu overlay controls.
	zola_add_customizer_control( $wp_customize, 'zola_header_mobile', 'zola_mobile_header_height', array(
		'label'       => esc_html__( 'Mobile Header Height', 'zola' ),
		'type'        => 'range',
		'default'     => 70,
		'sanitize'    => 'zola_sanitize_header_height',
		'input_attrs' => array( 'min' => 48, 'max' => 140, 'step' => 1, 'data-default' => 70 ),
		'priority'    => 20,
	) );
	zola_add_header_visual_control( $wp_customize, 'zola_mobile_logo_position', array(
		'label'       => esc_html__( 'Logo position', 'zola' ),
		'default'     => 'center',
		'sanitize'    => 'zola_sanitize_logo_position',
		'visual_type' => 'mobile-logo-position',
		'choices'     => array( 'left' => esc_html__( 'Left', 'zola' ), 'center' => esc_html__( 'Center', 'zola' ) ),
		'priority'    => 30,
		'section'     => 'zola_header_mobile',
	) );
	zola_add_header_visual_control( $wp_customize, 'zola_mobile_menu_icon_style', array(
		'label'       => esc_html__( 'Menu Icon Style', 'zola' ),
		'default'     => 'circle-fill',
		'sanitize'    => 'zola_sanitize_mobile_menu_icon_style',
		'visual_type' => 'menu-icon-style',
		'choices'     => array(
			'plain'          => esc_html__( 'Plain', 'zola' ),
			'circle-outline' => esc_html__( 'Circle outline', 'zola' ),
			'circle-fill'    => esc_html__( 'Circle fill', 'zola' ),
			'square-fill'    => esc_html__( 'Square fill', 'zola' ),
			'square-outline' => esc_html__( 'Square outline', 'zola' ),
		),
		'priority'    => 40,
		'section'     => 'zola_header_mobile',
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header_mobile', 'zola_mobile_menu_show_title', array(
		'label'    => esc_html__( 'Show Menu title', 'zola' ),
		'type'     => 'checkbox',
		'default'  => false,
		'sanitize' => 'wp_validate_boolean',
		'priority' => 50,
	) );
	zola_add_header_visual_control( $wp_customize, 'zola_mobile_menu_overlay', array(
		'label'       => esc_html__( 'Menu Overlay', 'zola' ),
		'default'     => 'fullscreen',
		'sanitize'    => 'zola_sanitize_mobile_menu_overlay',
		'visual_type' => 'mobile-overlay',
		'choices'     => array( 'left' => esc_html__( 'Left', 'zola' ), 'right' => esc_html__( 'Right', 'zola' ), 'fullscreen' => esc_html__( 'Fullscreen', 'zola' ) ),
		'priority'    => 60,
		'section'     => 'zola_header_mobile',
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header_mobile', 'zola_mobile_submenu_behavior', array(
		'label'       => esc_html__( 'Menu item behavior', 'zola' ),
		'description' => esc_html__( 'Choose how a menu item with a submenu responds when tapped.', 'zola' ),
		'type'        => 'radio',
		'default'     => 'open-link',
		'sanitize'    => 'zola_sanitize_mobile_submenu_behavior',
		'choices'     => array( 'open-link' => esc_html__( 'Open link', 'zola' ), 'toggle-submenu' => esc_html__( 'Toggle submenu', 'zola' ) ),
		'priority'    => 70,
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header_mobile', 'zola_mobile_overlay_top_content', array(
		'label'       => esc_html__( 'Top content', 'zola' ),
		'description' => esc_html__( 'Optional HTML or shortcode displayed above the mobile menu.', 'zola' ),
		'type'        => 'textarea',
		'default'     => '',
		'sanitize'    => 'wp_kses_post',
		'priority'    => 80,
	) );
	$wp_customize->add_setting( 'zola_mobile_menu_elements', array(
		'default'           => zola_mobile_menu_elements_default(),
		'sanitize_callback' => 'zola_sanitize_mobile_menu_elements',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new Zola_Mobile_Menu_Elements_Customize_Control( $wp_customize, 'zola_mobile_menu_elements_control', array(
		'settings' => 'zola_mobile_menu_elements',
		'section'  => 'zola_header_mobile',
		'label'    => esc_html__( 'Menu elements', 'zola' ),
		'choices'  => zola_mobile_menu_element_choices(),
		'priority' => 90,
	) ) );
	zola_add_header_visual_control( $wp_customize, 'zola_mobile_overlay_text_mode', array(
		'label'       => esc_html__( 'Overlay Color', 'zola' ),
		'default'     => 'light',
		'sanitize'    => 'zola_sanitize_header_text_mode',
		'visual_type' => 'text-color',
		'choices'     => array( 'light' => esc_html__( 'Light', 'zola' ), 'dark' => esc_html__( 'Dark', 'zola' ) ),
		'priority'    => 100,
		'section'     => 'zola_header_mobile',
	) );
	zola_add_customizer_control( $wp_customize, 'zola_header_mobile', 'zola_mobile_overlay_background_color', array(
		'label'    => esc_html__( 'Background Color', 'zola' ),
		'type'     => 'color',
		'default'  => '#0a0a0a',
		'sanitize' => 'sanitize_hex_color',
		'priority' => 110,
	) );

	$sticky_header_controls = array(
		'zola_sticky_header_height' => array(
			'label'       => esc_html__( 'Header Height on Sticky', 'zola' ),
			'type'        => 'range',
			'default'     => 70,
			'sanitize'    => 'zola_sanitize_header_height',
			'input_attrs' => array( 'min' => 48, 'max' => 140, 'step' => 1, 'data-default' => 70 ),
			'priority'    => 10,
		),
		'zola_sticky_logo_padding' => array(
			'label'       => esc_html__( 'Sticky Logo Padding', 'zola' ),
			'type'        => 'range',
			'default'     => 0,
			'sanitize'    => 'zola_sanitize_logo_padding',
			'input_attrs' => array( 'min' => 0, 'max' => 80, 'step' => 1, 'data-default' => 0 ),
			'priority'    => 20,
		),
		'zola_sticky_nav_height' => array(
			'label'       => esc_html__( 'Nav Height on Sticky', 'zola' ),
			'type'        => 'range',
			'default'     => 50,
			'sanitize'    => 'zola_sanitize_sticky_nav_height',
			'input_attrs' => array( 'min' => 20, 'max' => 120, 'step' => 1, 'data-default' => 50 ),
			'priority'    => 30,
		),
		'zola_sticky_top_bar' => array(
			'label'    => esc_html__( 'Top Bar - Sticky on Scroll', 'zola' ),
			'type'     => 'checkbox',
			'default'  => false,
			'sanitize' => 'wp_validate_boolean',
			'priority' => 40,
		),
		'zola_sticky_header_main' => array(
			'label'    => esc_html__( 'Header Main - Sticky on Scroll', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
			'priority' => 50,
		),
		'zola_sticky_header_bottom' => array(
			'label'    => esc_html__( 'Header Bottom - Sticky on Scroll', 'zola' ),
			'type'     => 'checkbox',
			'default'  => true,
			'sanitize' => 'wp_validate_boolean',
			'priority' => 60,
		),
		'zola_sticky_style' => array(
			'label'    => esc_html__( 'Sticky Style', 'zola' ),
			'type'     => 'select',
			'default'  => 'jump-down',
			'sanitize' => 'zola_sanitize_sticky_style',
			'choices'  => array(
				'jump-down' => esc_html__( 'Jump Down', 'zola' ),
				'fade'      => esc_html__( 'Fade', 'zola' ),
				'shrink'    => esc_html__( 'Shrink', 'zola' ),
			),
			'priority' => 70,
		),
		'zola_sticky_hide_on_scroll_down' => array(
			'label'    => esc_html__( 'Hide sticky when scrolling down', 'zola' ),
			'type'     => 'checkbox',
			'default'  => false,
			'sanitize' => 'wp_validate_boolean',
			'priority' => 80,
		),
	);
	foreach ( $sticky_header_controls as $setting_id => $control ) {
		zola_add_customizer_control( $wp_customize, 'zola_header_sticky', $setting_id, $control );
	}

	$wp_customize->add_setting( 'zola_sticky_logo_image', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'zola_sticky_logo_image_control', array(
		'label'     => esc_html__( 'Custom Sticky Logo', 'zola' ),
		'section'   => 'zola_header_sticky',
		'settings'  => 'zola_sticky_logo_image',
		'mime_type' => 'image',
		'priority'  => 90,
	) ) );

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

/** Clear the cached typography CSS from the Customizer button. */
function zola_ajax_clear_typography_cache(): void {
	check_ajax_referer( 'zola_clear_typography_cache', 'nonce' );
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'You are not allowed to clear this cache.', 'zola' ) ), 403 );
	}
	delete_transient( 'zola_typography_inline_css' );
	wp_send_json_success( array( 'message' => esc_html__( 'Typography cache cleared.', 'zola' ) ) );
}
add_action( 'wp_ajax_zola_clear_typography_cache', 'zola_ajax_clear_typography_cache' );

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
		'header_image'      => 186,
		'background_image'  => 187,
	);
	foreach ( $core_priorities as $section_id => $priority ) {
		$section = $wp_customize->get_section( $section_id );
		if ( $section ) {
			$section->priority = $priority;
		}
	}
	$style_subsections = array(
		'colors'     => array(
			'title'    => esc_html__( 'Colors', 'zola' ),
			'priority' => 20,
		),
	);
	foreach ( $style_subsections as $section_id => $style_section ) {
		$section = $wp_customize->get_section( $section_id );
		if ( $section ) {
			$section->title    = $style_section['title'];
			$section->panel    = 'zola_style';
			$section->priority = $style_section['priority'];
		}
	}
	$custom_css = $wp_customize->get_section( 'custom_css' );
	if ( $custom_css ) {
		$custom_css->title    = esc_html__( 'Additional CSS', 'zola' );
		$custom_css->panel    = '';
		$custom_css->priority = 999;
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

	zola_add_header_visual_control( $wp_customize, 'zola_logo_position', array(
		'label'       => esc_html__( 'Logo position', 'zola' ),
		'default'     => 'left',
		'sanitize'    => 'zola_sanitize_logo_position',
		'visual_type' => 'logo-position',
		'choices'     => array(
			'left'   => esc_html__( 'Left', 'zola' ),
			'center' => esc_html__( 'Center', 'zola' ),
		),
		'priority'    => 30,
		'section'     => 'title_tagline',
	) );

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
		'input_attrs' => array( 'min' => 20, 'max' => 100, 'step' => 1, 'data-default' => 42 ),
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
		'input_attrs' => array( 'min' => 0, 'max' => 80, 'step' => 1, 'data-default' => 0 ),
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
			'button'       => esc_html__( 'Button 1', 'zola' ),
			'button_2'     => esc_html__( 'Button 2', 'zola' ),
			'search'       => esc_html__( 'Search', 'zola' ),
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
		'resetRange'     => esc_html__( 'Reset to default', 'zola' ),
		'typographyCache' => array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'zola_clear_typography_cache' ),
			'working' => esc_html__( 'Clearing cache…', 'zola' ),
			'error'   => esc_html__( 'Could not clear the typography cache. Please try again.', 'zola' ),
		),
		'emptySlot'      => esc_html__( 'Drop elements here', 'zola' ),
		'noAvailable'    => esc_html__( 'All elements are in this layout.', 'zola' ),
		'settings'       => esc_html__( 'Settings', 'zola' ),
		'remove'         => esc_html__( 'Remove', 'zola' ),
		'elements'       => esc_html__( 'Add elements', 'zola' ),
		'activePosition' => esc_html__( 'Selected position', 'zola' ),
		'mobileMenu'       => array(
			'shown'  => esc_html__( 'Shown', 'zola' ),
			'hidden' => esc_html__( 'Hidden', 'zola' ),
			'show'   => esc_html__( 'Show %s', 'zola' ),
			'hide'   => esc_html__( 'Hide %s', 'zola' ),
		),
	) );
}
add_action( 'customize_controls_enqueue_scripts', 'zola_customize_controls_enqueue_styles' );

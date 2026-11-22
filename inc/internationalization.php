<?php
/** Translation helpers for WordPress localization and Polylang. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return Customizer values whose content can vary by language. */
function zola_polylang_theme_strings(): array {
	return array(
		'zola_header_cta_label'          => array( 'Header button 1 text', false ),
		'zola_header_button_2_text'      => array( 'Header button 2 text', false ),
		'zola_footer_copyright'          => array( 'Footer copyright text', false ),
		'zola_page_title_eyebrow'        => array( 'Page title label', false ),
		'zola_portfolio_archive_title'   => array( 'Portfolio archive label', false ),
		'zola_share_label'               => array( 'Post sharing label', false ),
		'zola_mobile_overlay_top_content' => array( 'Mobile menu top content', true ),
		'zola_header_custom_html'        => array( 'Header HTML 1 (legacy)', true ),
		'zola_header_custom_html_1'      => array( 'Header HTML 1', true ),
		'zola_header_custom_html_2'      => array( 'Header HTML 2', true ),
		'zola_header_custom_html_3'      => array( 'Header HTML 3', true ),
		'zola_header_custom_html_4'      => array( 'Header HTML 4', true ),
		'zola_header_custom_html_5'      => array( 'Header HTML 5', true ),
		'zola_cookie_notice_text'        => array(
			'Cookie notice text',
			true,
			// Keep this source in sync with zola_cookie_notice_default_text().
			'We use cookies to improve your experience. Please choose whether to accept or reject optional cookies.',
		),
		'zola_logo_link'                => array( 'Custom logo URL', false ),
		'zola_header_cta_url'            => array( 'Header button 1 URL', false ),
		'zola_header_button_2_link'      => array( 'Header button 2 URL', false ),
	);
}

/** Register saved Customizer text in Polylang's string translations table. */
function zola_register_polylang_theme_strings(): void {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}

	$theme_mods = get_theme_mods();
	if ( ! is_array( $theme_mods ) ) {
		return;
	}

	foreach ( zola_polylang_theme_strings() as $setting_id => $definition ) {
		$value = $theme_mods[ $setting_id ] ?? '';
		if ( 'zola_cookie_notice_text' === $setting_id && ! array_key_exists( $setting_id, $theme_mods ) ) {
			$value = $definition[2];
		}
		if ( ! is_string( $value ) || '' === trim( $value ) ) {
			continue;
		}

		pll_register_string( $definition[0], $value, 'Zola Theme', $definition[1] );
	}
}
add_action( 'init', 'zola_register_polylang_theme_strings', 20 );

/** Translate a registered Customizer value only on the rendered front end. */
function zola_translate_polylang_theme_string( $value ) {
	if ( ! is_string( $value ) || '' === $value || is_admin() || ! function_exists( 'pll__' ) ) {
		return $value;
	}

	$translated = pll__( $value );
	return is_string( $translated ) ? $translated : $value;
}

foreach ( array_keys( zola_polylang_theme_strings() ) as $zola_theme_string_setting ) {
	add_filter( 'theme_mod_' . $zola_theme_string_setting, 'zola_translate_polylang_theme_string', 20 );
}

/** Translate the built-in cookie copy when it is still the Customizer default. */
function zola_translate_polylang_cookie_default( $value ) {
	if ( ! is_string( $value ) || is_admin() || ! function_exists( 'pll__' ) ) {
		return $value;
	}

	$theme_mods = get_theme_mods();
	if ( is_array( $theme_mods ) && array_key_exists( 'zola_cookie_notice_text', $theme_mods ) ) {
		return $value;
	}

	$default_source = zola_polylang_theme_strings()['zola_cookie_notice_text'][2];
	$translated    = pll__( $default_source );
	return is_string( $translated ) && $default_source !== $translated ? $translated : $value;
}
add_filter( 'theme_mod_zola_cookie_notice_text', 'zola_translate_polylang_cookie_default', 21 );

/** Get the translation of a selected page or Elementor template, when available. */
function zola_get_translated_post_id( int $post_id ): int {
	if ( ! $post_id || ! function_exists( 'pll_get_post' ) ) {
		return $post_id;
	}

	$translated_id = absint( pll_get_post( $post_id ) );
	return $translated_id ? $translated_id : $post_id;
}

/** Return the current language home page when Polylang is active. */
function zola_get_language_home_url(): string {
	if ( function_exists( 'pll_home_url' ) ) {
		$language_home = pll_home_url();
		if ( is_string( $language_home ) && '' !== $language_home ) {
			return $language_home;
		}
	}

	return home_url( '/' );
}

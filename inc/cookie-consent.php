<?php
/** Cookie notice displayed on the front end. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Render the notice independently of the active footer template. */
function zola_render_cookie_notice(): void {
	if ( ! wp_validate_boolean( get_theme_mod( 'zola_cookie_notice_enabled', false ) ) ) {
		return;
	}

	$text       = get_theme_mod( 'zola_cookie_notice_text', zola_cookie_notice_default_text() );
	$text       = is_string( $text ) ? $text : '';
	$content    = wp_kses_post( do_shortcode( wp_kses_post( $text ) ) );
	$page_id    = zola_get_translated_post_id( absint( get_theme_mod( 'zola_cookie_privacy_page', get_option( 'wp_page_for_privacy_policy', 0 ) ) ) );
	$page_url   = $page_id && 'page' === get_post_type( $page_id ) && 'publish' === get_post_status( $page_id ) ? get_permalink( $page_id ) : false;
	$mode       = zola_sanitize_header_text_mode( get_theme_mod( 'zola_cookie_text_color', 'light' ) );
	$style      = zola_sanitize_cookie_button_style( get_theme_mod( 'zola_cookie_button_style', 'outline' ) );
	$background = zola_get_theme_mod_color_value( 'zola_cookie_background_color', '#202124' );
	$version    = sanitize_text_field( (string) get_theme_mod( 'zola_cookie_notice_version', '1' ) );
	$version    = '' !== $version ? $version : '1';
	?>
	<section class="zola-cookie-notice zola-cookie-notice--<?php echo esc_attr( $mode ); ?> zola-cookie-notice--<?php echo esc_attr( $style ); ?>"
		aria-label="<?php esc_attr_e( 'Cookie notice', 'zola' ); ?>"
		data-zola-cookie-notice
		data-version="<?php echo esc_attr( $version ); ?>"
		data-storage-key="<?php echo esc_attr( 'zola-cookie-consent-' . md5( home_url( '/' ) ) ); ?>"
		data-preview="<?php echo is_customize_preview() ? '1' : '0'; ?>"
		style="--zola-cookie-background: <?php echo esc_attr( $background ); ?>;"
		hidden>
		<div class="zola-cookie-notice__content"><?php echo wpautop( $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The content and shortcode output are sanitized above. ?></div>
		<div class="zola-cookie-notice__actions">
			<?php if ( $page_url ) : ?>
				<a class="zola-cookie-notice__button" href="<?php echo esc_url( $page_url ); ?>"><?php esc_html_e( 'Privacy policy', 'zola' ); ?></a>
			<?php endif; ?>
			<button class="zola-cookie-notice__button" type="button" data-zola-cookie-choice="rejected"><?php esc_html_e( 'Reject', 'zola' ); ?></button>
			<button class="zola-cookie-notice__button" type="button" data-zola-cookie-choice="accepted"><?php esc_html_e( 'Accept', 'zola' ); ?></button>
		</div>
	</section>
	<?php
}
add_action( 'wp_footer', 'zola_render_cookie_notice', 5 );

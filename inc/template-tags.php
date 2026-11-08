<?php
/** Small reusable template helpers. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Render an Elementor Theme Builder location and report whether it matched. */
function zola_has_elementor_location( string $location ): bool {
	return function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( $location );
}

/** Render a selected Elementor Library template as a global header or footer. */
function zola_render_elementor_layout( string $location ): bool {
	$template_id = absint( get_theme_mod( 'zola_' . $location . '_template', 0 ) );
	if ( ! $template_id || ! did_action( 'elementor/loaded' ) || 'publish' !== get_post_status( $template_id ) ) {
		return false;
	}
	$template = get_post( $template_id );
	if ( ! $template || 'elementor_library' !== $template->post_type || ! class_exists( '\\Elementor\\Plugin' ) ) {
		return false;
	}
	$elementor = call_user_func( array( 'Elementor\\Plugin', 'instance' ) );
	if ( ! isset( $elementor->frontend ) || ! is_callable( array( $elementor->frontend, 'get_builder_content_for_display' ) ) ) {
		return false;
	}
	$content = $elementor->frontend->get_builder_content_for_display( $template_id, true );
	if ( ! is_string( $content ) || '' === trim( $content ) ) {
		return false;
	}
	$role = 'header' === $location ? 'banner' : 'contentinfo';
	echo '<div class="zola-elementor-' . esc_attr( $location ) . '" role="' . esc_attr( $role ) . '">';
	echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor renders trusted admin-authored template markup.
	echo '</div>';
	return true;
}

function zola_fallback_menu(): void {
	echo '<ul class="menu">';
	wp_list_pages( array(
		'title_li' => '',
		'depth'    => 1,
	) );
	echo '</ul>';
}

function zola_archive_layout_class(): string {
	$portfolio_types = array( 'portfolio', 'project' );
	$archive_type    = get_query_var( 'post_type' );
	$is_portfolio    = is_post_type_archive() && (bool) array_intersect( $portfolio_types, (array) $archive_type );
	$setting_id      = $is_portfolio ? 'zola_portfolio_layout' : 'zola_archive_layout';
	$layout          = sanitize_key( get_theme_mod( $setting_id, 'grid' ) );
	return in_array( $layout, array( 'grid', 'list', 'cover' ), true ) ? 'post-grid--' . $layout : 'post-grid--grid';
}

function zola_is_portfolio_archive(): bool {
	return is_post_type_archive( 'portfolio' ) || is_post_type_archive( 'project' );
}

function zola_blog_sidebar_visible(): bool {
	return (bool) ( ! zola_is_portfolio_archive()
		&& get_theme_mod( 'zola_blog_show_sidebar', false )
		&& is_active_sidebar( 'zola-blog-sidebar' ) );
}

function zola_render_blog_sidebar(): void {
	if ( ! zola_blog_sidebar_visible() ) {
		return;
	}
	?>
	<aside class="zola-sidebar" aria-label="<?php esc_attr_e( 'Blog sidebar', 'zola' ); ?>">
		<?php dynamic_sidebar( 'zola-blog-sidebar' ); ?>
	</aside>
	<?php
}

function zola_share_links_visible(): bool {
	return (bool) ( get_theme_mod( 'zola_share_enabled', true )
		&& (
			get_theme_mod( 'zola_share_facebook', true )
			|| get_theme_mod( 'zola_share_x', true )
			|| get_theme_mod( 'zola_share_linkedin', true )
			|| get_theme_mod( 'zola_share_copy_link', true )
		) );
}

function zola_site_brand( bool $footer = false ): void {
	$brand_class = $footer ? 'site-brand site-brand--footer' : 'site-brand';
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	$site_name = get_bloginfo( 'name' );
	$tagline   = get_bloginfo( 'description' );
	?>
	<a class="<?php echo esc_attr( $brand_class ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<span class="site-brand__copy">
			<strong><?php echo esc_html( $site_name ? $site_name : 'Zola' ); ?></strong>
			<?php if ( $tagline ) : ?><small><?php echo esc_html( $tagline ); ?></small><?php endif; ?>
		</span>
	</a>
	<?php
}


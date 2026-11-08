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

/** Render the native header arranged in the Customizer Header Builder. */
function zola_render_header_builder(): void {
	$layout       = zola_get_header_builder_layout();
	$header_class = 'site-header site-header--builder';
	$is_homepage  = 'page' === get_option( 'show_on_front' ) && is_front_page();
	$header_class .= $is_homepage && get_theme_mod( 'zola_transparent_header', true ) ? ' site-header--overlay' : ' site-header--solid';
	$header_class .= get_theme_mod( 'zola_sticky_header', true ) ? ' site-header--sticky' : ' site-header--flow';

	echo '<header class="' . esc_attr( $header_class ) . '" data-site-header>';
	foreach ( array( 'desktop', 'mobile' ) as $device ) {
		$device_settings = isset( $layout[ $device ] ) && is_array( $layout[ $device ] ) ? $layout[ $device ] : array();
		$rows            = isset( $device_settings['rows'] ) && is_array( $device_settings['rows'] ) ? $device_settings['rows'] : array();
		echo '<div class="site-header-builder__device site-header-builder__device--' . esc_attr( $device ) . '">';
		foreach ( array( 'top', 'main', 'bottom' ) as $row_name ) {
			$row = isset( $rows[ $row_name ] ) && is_array( $rows[ $row_name ] ) ? $rows[ $row_name ] : array();
			$slots_markup = '';
			$row_has_content = false;
			foreach ( array( 'left', 'center', 'right' ) as $column ) {
				$items = isset( $row[ $column ] ) && is_array( $row[ $column ] ) ? $row[ $column ] : array();
				ob_start();
				foreach ( $items as $module ) {
					zola_render_header_module( (string) $module );
				}
				$slot_content = (string) ob_get_clean();
				if ( '' !== trim( $slot_content ) ) {
					$row_has_content = true;
				}
				$slots_markup .= '<div class="site-header-builder__slot site-header-builder__slot--' . esc_attr( $column ) . '">' . $slot_content . '</div>';
			}
			if ( ! $row_has_content ) {
				continue;
			}
			$row_class = 'site-header-builder__row site-header-builder__row--' . sanitize_html_class( $row_name );
			$inner_class = 'site-header-builder__inner';
			if ( 'main' === $row_name ) {
				$inner_class .= ' site-header__inner';
			}
			printf(
				'<div class="%1$s"><div class="%2$s">%3$s</div></div>',
				esc_attr( $row_class ),
				esc_attr( $inner_class ),
				$slots_markup // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Contains only sanitized output from the registered header modules.
			);
		}
		echo '</div>';
	}
	echo '</header>';
}

/** Render one supported element in the native header builder. */
function zola_render_header_module( string $module ): void {
	switch ( $module ) {
		case 'logo':
			zola_site_brand();
			break;

		case 'menu':
			$navigation_id = wp_unique_id( 'zola-primary-navigation-' );
			?>
			<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $navigation_id ); ?>">
				<span class="menu-toggle__lines" aria-hidden="true"><i></i><i></i></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'zola' ); ?></span>
			</button>
			<nav class="site-nav" id="<?php echo esc_attr( $navigation_id ); ?>" aria-label="<?php esc_attr_e( 'Primary navigation', 'zola' ); ?>" data-primary-nav>
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'fallback_cb'    => 'zola_fallback_menu',
					'depth'          => 2,
				) );
				?>
			</nav>
			<?php
			break;

		case 'button':
			$button_label = trim( (string) get_theme_mod( 'zola_header_cta_label', '' ) );
			$button_url   = esc_url( get_theme_mod( 'zola_header_cta_url', '' ) );
			if ( $button_label && $button_url ) :
				?><a class="button button--small button--accent site-header__cta" href="<?php echo $button_url; ?>"><?php echo esc_html( $button_label ); ?><span aria-hidden="true">↗</span></a><?php
			endif;
			break;

		case 'announcement':
			$announcement = trim( (string) get_theme_mod( 'zola_announcement_text', '' ) );
			if ( $announcement ) :
				$announcement_url = esc_url( get_theme_mod( 'zola_announcement_url', '' ) );
				?><div class="site-announcement__inner"><?php if ( $announcement_url ) : ?><a href="<?php echo $announcement_url; ?>"><?php echo esc_html( $announcement ); ?><span aria-hidden="true">↗</span></a><?php else : ?><span><?php echo esc_html( $announcement ); ?></span><?php endif; ?></div><?php
			endif;
			break;

		case 'search':
			echo get_search_form( array( 'echo' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress generates and escapes the search form.
			break;

		case 'social':
			$networks = array(
				'facebook'  => 'Facebook',
				'instagram' => 'Instagram',
				'linkedin'  => 'LinkedIn',
				'youtube'   => 'YouTube',
				'x'         => 'X',
			);
			$links = array();
			foreach ( $networks as $network => $label ) {
				$url = esc_url( get_theme_mod( 'zola_header_social_' . $network, '' ) );
				if ( $url ) {
					$links[] = '<a href="' . $url . '" aria-label="' . esc_attr( $label ) . '" rel="me noopener">' . esc_html( $label ) . '</a>';
				}
			}
			if ( $links ) {
				echo '<nav class="site-header-social" aria-label="' . esc_attr__( 'Social links', 'zola' ) . '">' . implode( '', $links ) . '</nav>';
			}
			break;

		case 'html':
			$custom_html = get_theme_mod( 'zola_header_custom_html', '' );
			if ( is_string( $custom_html ) && '' !== trim( $custom_html ) ) {
				echo '<div class="site-header-html">' . wp_kses_post( $custom_html ) . '</div>';
			}
			break;

		case 'account':
			$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
			if ( $account_url ) {
				echo '<a class="site-header__account" href="' . esc_url( $account_url ) . '">' . esc_html__( 'Account', 'zola' ) . '</a>';
			}
			break;

		case 'cart':
			if ( function_exists( 'wc_get_cart_url' ) ) {
				$count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
				echo '<a class="site-header__cart" href="' . esc_url( wc_get_cart_url() ) . '">' . esc_html__( 'Cart', 'zola' ) . ' <span>' . esc_html( (string) $count ) . '</span></a>';
			}
			break;
	}
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


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
	$template_id = zola_get_translated_post_id( absint( get_theme_mod( 'zola_' . $location . '_template', 0 ) ) );
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

/** Determine whether any Header Builder row should remain sticky while scrolling. */
function zola_is_sticky_header_enabled(): bool {
	$sticky_rows = array(
		'zola_sticky_top_bar'       => false,
		'zola_sticky_header_main'   => true,
		'zola_sticky_header_bottom' => true,
	);

	foreach ( $sticky_rows as $setting_id => $default ) {
		if ( 'zola_sticky_top_bar' === $setting_id && ! wp_validate_boolean( get_theme_mod( 'zola_top_bar_enabled', true ) ) ) {
			continue;
		}
		if ( wp_validate_boolean( get_theme_mod( $setting_id, $default ) ) ) {
			return true;
		}
	}

	return false;
}

/** Render the native header arranged in the Customizer Header Builder. */
function zola_render_header_builder(): void {
	$layout       = zola_get_header_builder_layout();
	$header_class = 'site-header site-header--builder';
	$is_homepage  = 'page' === get_option( 'show_on_front' ) && is_front_page();
	$header_class .= $is_homepage && get_theme_mod( 'zola_transparent_header', true ) ? ' site-header--overlay' : ' site-header--solid';
	$sticky_row_settings = array(
		'top'    => array( 'zola_sticky_top_bar', false ),
		'main'   => array( 'zola_sticky_header_main', true ),
		'bottom' => array( 'zola_sticky_header_bottom', true ),
	);
	$sticky_enabled = zola_is_sticky_header_enabled();
	$header_class .= $sticky_enabled ? ' site-header--sticky' : ' site-header--flow';
	if ( $sticky_enabled ) {
		$sticky_style  = zola_sanitize_sticky_style( get_theme_mod( 'zola_sticky_style', 'jump-down' ) );
		$header_class .= ' site-header--sticky-style-' . sanitize_html_class( $sticky_style );
		if ( wp_validate_boolean( get_theme_mod( 'zola_sticky_hide_on_scroll_down', false ) ) ) {
			$header_class .= ' site-header--hide-on-scroll-down';
		}
	}
	$header_text_mode = zola_sanitize_header_text_mode( get_theme_mod( 'zola_header_text_mode', 'dark' ) );
	if ( 'auto' !== $header_text_mode ) {
		$header_class .= ' site-header--text-' . sanitize_html_class( $header_text_mode );
	}
	if ( get_theme_mod( 'zola_header_shadow', false ) ) {
		$header_class .= ' site-header--has-shadow';
	}
	if ( ! get_theme_mod( 'zola_header_divider', true ) ) {
		$header_class .= ' site-header--no-divider';
	} else {
		$header_class .= ' site-header--has-divider';
	}

	echo '<header class="' . esc_attr( $header_class ) . '" data-site-header>';
	foreach ( array( 'desktop', 'mobile' ) as $device ) {
		$device_settings = isset( $layout[ $device ] ) && is_array( $layout[ $device ] ) ? $layout[ $device ] : array();
		$rows            = isset( $device_settings['rows'] ) && is_array( $device_settings['rows'] ) ? $device_settings['rows'] : array();
		$device_class = 'site-header-builder__device site-header-builder__device--' . sanitize_html_class( $device );
		if ( 'mobile' === $device && 'center' === zola_sanitize_logo_position( get_theme_mod( 'zola_mobile_logo_position', 'center' ) ) ) {
			$device_class .= ' site-header-builder__device--logo-center';
		}
		echo '<div class="' . esc_attr( $device_class ) . '">';
		foreach ( array( 'top', 'main', 'bottom' ) as $row_name ) {
			if ( 'top' === $row_name && ! wp_validate_boolean( get_theme_mod( 'zola_top_bar_enabled', true ) ) ) {
				continue;
			}
			$row = isset( $rows[ $row_name ] ) && is_array( $rows[ $row_name ] ) ? $rows[ $row_name ] : array();
			$slots_markup = '';
			$row_has_content = false;
			foreach ( array( 'left', 'center', 'right' ) as $column ) {
				$items = isset( $row[ $column ] ) && is_array( $row[ $column ] ) ? $row[ $column ] : array();
				ob_start();
				foreach ( $items as $module ) {
					zola_render_header_module( (string) $module, $device );
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
			if ( wp_validate_boolean( get_theme_mod( $sticky_row_settings[ $row_name ][0], $sticky_row_settings[ $row_name ][1] ) ) ) {
				$row_class .= ' site-header-builder__row--sticky-enabled';
			}
			$nav_settings_prefix = 'top' === $row_name
				? 'zola_top_bar_nav_'
				: ( 'bottom' === $row_name ? 'zola_header_bottom_nav_' : 'zola_header_nav_' );
			$nav_style = zola_sanitize_header_nav_style( get_theme_mod( $nav_settings_prefix . 'style', 'plain' ) );
			$row_class .= ' site-header-builder__row--nav-' . sanitize_html_class( $nav_style );
			if ( wp_validate_boolean( get_theme_mod( $nav_settings_prefix . 'uppercase', false ) ) ) {
				$row_class .= ' site-header-builder__row--nav-uppercase';
			}
			if ( wp_validate_boolean( get_theme_mod( $nav_settings_prefix . 'overlay', false ) ) ) {
				$row_class .= ' site-header-builder__row--nav-overlay';
			}
			if ( 'top' === $row_name ) {
				$top_bar_text_mode = zola_sanitize_header_text_mode( get_theme_mod( 'zola_top_bar_text_mode', 'light' ) );
				$row_class .= ' site-header-builder__row--text-' . sanitize_html_class( $top_bar_text_mode );
			}
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

/** Render the configured content inside the mobile menu overlay. */
function zola_render_mobile_menu_overlay_contents(): void {
	$elements    = zola_sanitize_mobile_menu_elements( get_theme_mod( 'zola_mobile_menu_elements', zola_mobile_menu_elements_default() ) );
	$top_content = get_theme_mod( 'zola_mobile_overlay_top_content', '' );

	if ( is_string( $top_content ) && '' !== trim( $top_content ) ) {
		$rendered_top_content = wp_kses_post( do_shortcode( wp_kses_post( $top_content ) ) );
		echo '<div class="zola-mobile-menu__top-content">' . $rendered_top_content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized HTML and registered shortcodes are intentionally rendered.
	}

	foreach ( $elements['order'] as $element ) {
		if ( empty( $elements['visible'][ $element ] ) ) {
			continue;
		}

		ob_start();
		switch ( $element ) {
			case 'search':
				echo get_search_form( array( 'echo' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress generates and escapes the search form.
				break;

			case 'main_menu':
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu zola-mobile-menu__list',
					'fallback_cb'    => 'zola_fallback_menu',
					'depth'          => 2,
				) );
				break;

			case 'languages':
				zola_render_mobile_languages();
				break;

			case 'cart':
			case 'account':
				zola_render_header_module( $element, 'mobile-overlay' );
				break;

			case 'nav_icon':
				printf(
					'<button class="zola-mobile-menu__close" type="button" data-zola-menu-close aria-label="%1$s"><span aria-hidden="true"></span><span aria-hidden="true"></span></button>',
					esc_attr__( 'Close menu', 'zola' )
				);
				break;

			case 'secondary_menu':
			case 'top_bar_menu':
				$location = 'secondary_menu' === $element ? 'secondary' : 'top_bar';
				$menu     = wp_nav_menu( array(
					'theme_location' => $location,
					'container'      => false,
					'menu_class'     => 'menu zola-mobile-menu__list zola-mobile-menu__list--' . sanitize_html_class( $location ),
					'fallback_cb'    => false,
					'depth'          => 2,
					'echo'           => false,
				) );
				if ( is_string( $menu ) ) {
					echo $menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress generates and escapes registered menu markup.
				}
				break;
		}
		$content = (string) ob_get_clean();
		if ( '' !== trim( $content ) ) {
			echo '<div class="zola-mobile-menu__element zola-mobile-menu__element--' . esc_attr( sanitize_html_class( $element ) ) . '">' . $content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content is generated by escaped WordPress menu helpers and the theme's sanitized renderers.
		}
	}
}

/** Render language links when WPML or Polylang is active. */
function zola_render_mobile_languages(): void {
	$languages = array();
	if ( function_exists( 'pll_the_languages' ) ) {
		$polylang_languages = pll_the_languages( array( 'raw' => 1 ) );
		if ( is_array( $polylang_languages ) ) {
			foreach ( $polylang_languages as $language ) {
				if ( is_array( $language ) ) {
					$languages[] = array(
						'url'    => $language['url'] ?? '',
						'name'   => $language['name'] ?? '',
						'code'   => $language['slug'] ?? '',
						'active' => ! empty( $language['is_current'] ) || ! empty( $language['current_lang'] ),
					);
				}
			}
		}
	} elseif ( function_exists( 'icl_get_languages' ) ) {
		$wpml_languages = icl_get_languages( 'skip_missing=0&orderby=code' );
		if ( is_array( $wpml_languages ) ) {
			foreach ( $wpml_languages as $language ) {
				if ( is_array( $language ) ) {
					$languages[] = array(
						'url'    => $language['url'] ?? '',
						'name'   => $language['native_name'] ?? ( $language['translated_name'] ?? '' ),
						'code'   => $language['language_code'] ?? '',
						'active' => ! empty( $language['active'] ),
					);
				}
			}
		}
	}

	if ( ! $languages ) {
		return;
	}
	echo '<nav class="zola-mobile-languages" aria-label="' . esc_attr__( 'Languages', 'zola' ) . '"><ul>';
	foreach ( $languages as $language ) {
		if ( empty( $language['url'] ) || empty( $language['name'] ) ) {
			continue;
		}
		printf(
			'<li><a href="%1$s" hreflang="%2$s"%3$s>%4$s</a></li>',
			esc_url( $language['url'] ),
			esc_attr( $language['code'] ),
			! empty( $language['active'] ) ? ' aria-current="page"' : '',
			esc_html( $language['name'] )
		);
	}
	echo '</ul></nav>';
}

/** Return a small, inline icon for a configured header button. */
function zola_header_button_icon_svg( string $icon ): string {
	$paths = array(
		'arrow-right'   => '<path d="M5 12h14M12 5l7 7-7 7" />',
		'external-link' => '<path d="M13 5h6v6M19 5l-9 9" /><path d="M18 13v5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" />',
		'phone'         => '<path d="M7 3h3l2 5-2 2a14 14 0 0 0 4 4l2-2 5 2v3a2 2 0 0 1-2 2A16 16 0 0 1 5 5a2 2 0 0 1 2-2Z" />',
		'email'         => '<rect x="3" y="5" width="18" height="14" rx="2" /><path d="m4 7 8 6 8-6" />',
	);
	if ( ! isset( $paths[ $icon ] ) ) {
		return '';
	}

	return '<svg class="site-header__cta-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $paths[ $icon ] . '</svg>';
}

/** Render one supported element in the native header builder. */
function zola_render_header_module( string $module, string $device = 'desktop' ): void {
	switch ( $module ) {
		case 'logo':
			zola_site_brand();
			break;

		case 'menu':
			$navigation_id = wp_unique_id( 'zola-primary-navigation-' );
			$is_mobile    = 'mobile' === $device;
			$toggle_class = 'menu-toggle';
			$nav_class    = 'site-nav';
			if ( $is_mobile ) {
				$toggle_class .= ' menu-toggle--' . sanitize_html_class( zola_sanitize_mobile_menu_icon_style( get_theme_mod( 'zola_mobile_menu_icon_style', 'circle-fill' ) ) );
				$nav_class .= ' zola-mobile-menu-overlay zola-mobile-menu-overlay--' . sanitize_html_class( zola_sanitize_mobile_menu_overlay( get_theme_mod( 'zola_mobile_menu_overlay', 'fullscreen' ) ) );
				$nav_class .= ' zola-mobile-menu-overlay--text-' . sanitize_html_class( zola_sanitize_header_text_mode( get_theme_mod( 'zola_mobile_overlay_text_mode', 'light' ) ) );
			}
			?>
			<button class="<?php echo esc_attr( $toggle_class ); ?>" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $navigation_id ); ?>">
				<span class="menu-toggle__lines" aria-hidden="true"><i></i><i></i><?php if ( $is_mobile ) : ?><i></i><?php endif; ?></span>
				<?php if ( $is_mobile && get_theme_mod( 'zola_mobile_menu_show_title', false ) ) : ?><span class="menu-toggle__title"><?php esc_html_e( 'Menu', 'zola' ); ?></span><?php endif; ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'zola' ); ?></span>
			</button>
			<nav class="<?php echo esc_attr( $nav_class ); ?>" id="<?php echo esc_attr( $navigation_id ); ?>" aria-label="<?php esc_attr_e( 'Primary navigation', 'zola' ); ?>" data-primary-nav<?php if ( $is_mobile ) : ?> data-zola-mobile-menu-overlay data-submenu-behavior="<?php echo esc_attr( zola_sanitize_mobile_submenu_behavior( get_theme_mod( 'zola_mobile_submenu_behavior', 'open-link' ) ) ); ?>"<?php endif; ?>>
				<?php
				if ( $is_mobile ) {
					zola_render_mobile_menu_overlay_contents();
				} else {
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'menu',
						'fallback_cb'    => 'zola_fallback_menu',
						'depth'          => 2,
					) );
				}
				?>
			</nav>
			<?php
			break;

		case 'button':
		case 'button_2':
			$button_number = 'button_2' === $module ? 2 : 1;
			$button_prefix = 'zola_header_button_' . $button_number . '_';
			$text_setting  = 1 === $button_number ? 'zola_header_cta_label' : $button_prefix . 'text';
			$link_setting  = 1 === $button_number ? 'zola_header_cta_url' : $button_prefix . 'link';
			$button_label  = trim( (string) get_theme_mod( $text_setting, sprintf( __( 'Button %d', 'zola' ), $button_number ) ) );
			$button_url    = esc_url( get_theme_mod( $link_setting, '' ) );
			if ( ! $button_label || ! $button_url ) {
				break;
			}

			$button_case  = zola_sanitize_header_button_case( get_theme_mod( $button_prefix . 'case', 'uppercase' ) );
			$button_color = zola_sanitize_header_button_color( get_theme_mod( $button_prefix . 'color', 'primary' ) );
			$button_style = zola_sanitize_header_button_style( get_theme_mod( $button_prefix . 'style', 'default' ) );
			$button_size  = zola_sanitize_header_button_size( get_theme_mod( $button_prefix . 'size', 'l' ) );
			$button_icon  = zola_sanitize_header_button_icon( get_theme_mod( $button_prefix . 'icon', 'none' ) );
			$button_target = zola_sanitize_header_button_target( get_theme_mod( $button_prefix . 'target', '_self' ) );
			$button_rel    = zola_sanitize_header_button_rel( get_theme_mod( $button_prefix . 'rel', '' ) );
			$button_radius = zola_sanitize_header_button_radius( get_theme_mod( $button_prefix . 'radius', 99 ) );
			$button_depth = zola_sanitize_header_button_depth( get_theme_mod( $button_prefix . 'depth', 0 ) );
			$button_hover_depth = zola_sanitize_header_button_depth( get_theme_mod( $button_prefix . 'depth_hover', 0 ) );
			$combined_hover_depth = $button_depth + $button_hover_depth;
			$button_shadow = 0 < $button_depth
				? sprintf( '0 %1$dpx %2$dpx rgba(0,0,0,.16)', $button_depth, $button_depth * 3 )
				: '0 0 0 rgba(0,0,0,0)';
			$button_hover_shadow = 0 < $combined_hover_depth
				? sprintf( '0 %1$dpx %2$dpx rgba(0,0,0,.2)', $combined_hover_depth, $combined_hover_depth * 3 )
				: '0 0 0 rgba(0,0,0,0)';
			if ( '_blank' === $button_target ) {
				$button_rel = implode( ' ', array_values( array_unique( array_merge( $button_rel ? explode( ' ', $button_rel ) : array(), array( 'noopener', 'noreferrer' ) ) ) ) );
			}
			$button_classes = array(
				'button',
				'site-header__cta',
				'site-header__cta--color-' . $button_color,
				'site-header__cta--style-' . $button_style,
				'site-header__cta--size-' . $button_size,
			);
			if ( 'uppercase' === $button_case ) {
				$button_classes[] = 'site-header__cta--uppercase';
			}
			$button_style_attribute = sprintf(
				'--zola-cta-radius:%1$dpx;--zola-cta-shadow:%2$s;--zola-cta-shadow-hover:%3$s;',
				$button_radius,
				$button_shadow,
				$button_hover_shadow
			);
			?>
			<a class="<?php echo esc_attr( implode( ' ', $button_classes ) ); ?>" href="<?php echo $button_url; ?>" target="<?php echo esc_attr( $button_target ); ?>"<?php if ( $button_rel ) : ?> rel="<?php echo esc_attr( $button_rel ); ?>"<?php endif; ?> style="<?php echo esc_attr( $button_style_attribute ); ?>">
				<span class="site-header__cta-label"><?php echo esc_html( $button_label ); ?></span><?php echo zola_header_button_icon_svg( $button_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The icon markup is selected from a fixed internal allowlist. ?>
			</a>
			<?php
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

		case 'html_1':
		case 'html_2':
		case 'html_3':
		case 'html_4':
		case 'html_5':
			$block_number = absint( substr( $module, 5 ) );
			$setting_id   = 'zola_header_custom_html_' . $block_number;
			$custom_html  = get_theme_mod( $setting_id, null );
			if ( null === $custom_html && 1 === $block_number ) {
				$custom_html = get_theme_mod( 'zola_header_custom_html', '' );
			}
			if ( is_string( $custom_html ) && '' !== trim( $custom_html ) ) {
				echo '<div class="site-header-html">' . do_shortcode( wp_kses_post( $custom_html ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized HTML and registered shortcodes are intentionally rendered.
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
	$site_name = get_bloginfo( 'name' );
	$tagline   = get_bloginfo( 'description' );
	$logo_link = trim( (string) get_theme_mod( 'zola_logo_link', '' ) );
	$logo_href = ! $footer && '' !== $logo_link ? $logo_link : zola_get_language_home_url();
	$sticky_logo_id = ! $footer ? absint( get_theme_mod( 'zola_sticky_logo_image', 0 ) ) : 0;
	if ( $sticky_logo_id && ! wp_attachment_is_image( $sticky_logo_id ) ) {
		$sticky_logo_id = 0;
	}

	if ( $footer ) {
		if ( has_custom_logo() ) {
			the_custom_logo();
			return;
		}
		$brand_class = 'site-brand site-brand--footer';
	} else {
		$logo_position = zola_sanitize_logo_position( get_theme_mod( 'zola_logo_position', 'left' ) );
		$brand_class   = 'site-brand site-brand--header site-brand--position-' . $logo_position;
	}

	if ( ! $footer && has_custom_logo() ) {
		$logo_id       = absint( get_theme_mod( 'custom_logo', 0 ) );
		$light_logo_id = absint( get_theme_mod( 'zola_logo_light_image', 0 ) );
		$header_mode   = zola_sanitize_header_text_mode( get_theme_mod( 'zola_header_text_mode', 'dark' ) );
		$is_transparent_home = 'page' === get_option( 'show_on_front' )
			&& is_front_page()
			&& get_theme_mod( 'zola_transparent_header', true );
		$use_light_logo = $light_logo_id && ( 'light' === $header_mode || $is_transparent_home );
		$show_site_info = wp_validate_boolean( get_theme_mod( 'zola_logo_show_site_info', false ) );
		$container_width = zola_sanitize_logo_container_width( get_theme_mod( 'zola_logo_container_width', 42 ) );
		$logo_padding    = zola_sanitize_logo_padding( get_theme_mod( 'zola_logo_padding', 0 ) );
		$max_width       = trim( (string) get_theme_mod( 'zola_logo_max_width', '' ) );
		$style           = sprintf(
			'--zola-logo-container-width:%1$d%%;--zola-logo-padding:%2$dpx;',
			$container_width,
			$logo_padding
		);
		if ( '' !== $max_width ) {
			$style .= sprintf( '--zola-logo-max-width:%dpx;', min( 1200, max( 24, absint( $max_width ) ) ) );
		}

		$logo_alt = trim( (string) get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) );
		if ( '' === $logo_alt ) {
			$logo_alt = $site_name ? $site_name : esc_html__( 'Site logo', 'zola' );
		}
		$render_logo = static function ( int $attachment_id, string $class, bool $eager ) use ( $logo_alt ): string {
			$attributes = array(
				'class'    => $class,
				'alt'      => $logo_alt,
				'decoding' => 'async',
				'loading'  => $eager ? 'eager' : 'lazy',
			);
			if ( $eager ) {
				$attributes['data-no-lazy']  = '1';
			}

			return (string) wp_get_attachment_image( $attachment_id, 'full', false, $attributes );
		};

		$standard_logo = $render_logo(
			$logo_id,
			'custom-logo site-brand__logo site-brand__logo--standard',
			! $use_light_logo
		);
		if ( '' !== $standard_logo ) {
			$brand_classes = $brand_class;
			$brand_classes .= $light_logo_id ? ' site-brand--has-light-logo' : '';
			$brand_classes .= $sticky_logo_id ? ' site-brand--has-sticky-logo' : '';
			printf( '<a class="%1$s" href="%2$s" style="%3$s" rel="home">', esc_attr( $brand_classes ), esc_url( $logo_href ), esc_attr( $style ) );
			echo '<span class="site-brand__logos site-brand__logos--standard">' . $standard_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Image HTML is generated by wp_get_attachment_image().
			if ( $light_logo_id ) {
				$light_logo = $render_logo(
					$light_logo_id,
					'custom-logo site-brand__logo site-brand__logo--light',
					(bool) $use_light_logo
				);
				echo $light_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Image HTML is generated by wp_get_attachment_image().
			}
			echo '</span>';
			if ( $sticky_logo_id ) {
				$sticky_logo = $render_logo(
					$sticky_logo_id,
					'custom-logo site-brand__logo site-brand__logo--sticky',
					false
				);
				echo '<span class="site-brand__logos site-brand__logos--sticky">' . $sticky_logo . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Image HTML is generated by wp_get_attachment_image().
			}
			if ( $show_site_info ) {
				echo '<span class="site-brand__copy site-brand__copy--below">';
				if ( $site_name ) {
					echo '<strong>' . esc_html( $site_name ) . '</strong>';
				}
				if ( $tagline ) {
					echo '<small>' . esc_html( $tagline ) . '</small>';
				}
				echo '</span>';
			}
			echo '</a>';
			return;
		}
	}
	$style     = '';
	if ( ! $footer ) {
		$style = sprintf(
			' style="--zola-logo-container-width:%1$d%%;--zola-logo-padding:%2$dpx;"',
			zola_sanitize_logo_container_width( get_theme_mod( 'zola_logo_container_width', 42 ) ),
			zola_sanitize_logo_padding( get_theme_mod( 'zola_logo_padding', 0 ) )
		);
	}
	?>
	<a class="<?php echo esc_attr( $brand_class . ( $sticky_logo_id ? ' site-brand--has-sticky-logo' : '' ) ); ?>" href="<?php echo esc_url( $logo_href ); ?>"<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Contains only bounded integer CSS values. ?> rel="home">
		<span class="site-brand__copy site-brand__copy--standard">
			<strong><?php echo esc_html( $site_name ? $site_name : 'Zola' ); ?></strong>
			<?php if ( $tagline ) : ?><small><?php echo esc_html( $tagline ); ?></small><?php endif; ?>
		</span>
		<?php if ( $sticky_logo_id ) : ?>
			<span class="site-brand__logos site-brand__logos--sticky">
				<?php echo wp_get_attachment_image( $sticky_logo_id, 'full', false, array( 'class' => 'custom-logo site-brand__logo site-brand__logo--sticky', 'alt' => $site_name ? $site_name : esc_html__( 'Site logo', 'zola' ), 'decoding' => 'async', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Image HTML is generated by wp_get_attachment_image(). ?>
			</span>
		<?php endif; ?>
	</a>
	<?php
}


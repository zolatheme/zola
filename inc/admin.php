<?php
/** Zola dashboard and homepage shortcuts. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function zola_add_admin_page(): void {
	add_menu_page(
		__( 'Zola', 'zola' ),
		__( 'Zola', 'zola' ),
		'edit_theme_options',
		'zola',
		'zola_render_admin_page',
		'dashicons-admin-customizer',
		3
	);
	add_submenu_page( 'zola', __( 'Theme Options', 'zola' ), __( 'Theme Options', 'zola' ), 'edit_theme_options', 'zola-theme-options', 'zola_render_theme_options_page' );
	add_submenu_page( 'zola', __( 'Header Builder', 'zola' ), __( 'Header Builder', 'zola' ), 'edit_theme_options', 'zola-header-builder', 'zola_open_header_builder' );
	add_submenu_page( 'zola', __( 'Site Builder', 'zola' ), __( 'Site Builder', 'zola' ), 'edit_theme_options', 'zola-site-builder', 'zola_render_site_builder_page' );
	add_submenu_page( 'zola', __( 'Features', 'zola' ), __( 'Features', 'zola' ), 'edit_theme_options', 'zola-features', 'zola_render_features_page' );
	add_submenu_page( 'zola', __( 'System Status', 'zola' ), __( 'System Status', 'zola' ), 'edit_theme_options', 'zola-status', 'zola_render_status_page' );
	add_submenu_page( 'zola', __( 'Help & Guides', 'zola' ), __( 'Help & Guides', 'zola' ), 'edit_theme_options', 'zola-help', 'zola_render_help_page' );
}
add_action( 'admin_menu', 'zola_add_admin_page' );

/** Open the header layout control directly in the Customizer. */
function zola_open_header_builder(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	wp_safe_redirect( admin_url( 'customize.php?autofocus%5Bsection%5D=zola_header_presets' ) );
	exit;
}

/** Load the admin-only styles for Zola pages. */
function zola_enqueue_admin_assets( string $hook_suffix ): void {
	if ( false === strpos( $hook_suffix, 'zola' ) ) {
		return;
	}
	wp_enqueue_style( 'zola-admin', ZOLA_URI . '/assets/css/admin.css', array( 'dashicons' ), ZOLA_VERSION );
}
add_action( 'admin_enqueue_scripts', 'zola_enqueue_admin_assets' );

/** Keep old Appearance page links working after moving the theme menu. */
function zola_redirect_legacy_theme_admin_pages(): void {
	if ( ! current_user_can( 'edit_theme_options' ) || ! isset( $_GET['page'] ) || ! is_string( $_GET['page'] ) ) {
		return;
	}
	$page = sanitize_key( wp_unslash( $_GET['page'] ) );
	if ( ! in_array( $page, array( 'zola', 'zola-site-builder' ), true ) ) {
		return;
	}
	wp_safe_redirect( admin_url( 'admin.php?page=' . rawurlencode( $page ) ) );
	exit;
}
add_action( 'load-themes.php', 'zola_redirect_legacy_theme_admin_pages' );

/** Show a compact hub for the controls exposed by WordPress and Elementor. */
function zola_render_theme_options_page(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$customizer_url = admin_url( 'customize.php' );
	$menus_url      = admin_url( 'nav-menus.php' );
	$templates_url  = admin_url( 'edit.php?post_type=elementor_library' );
	?>
	<div class="wrap zola-admin-page">
		<h1><?php esc_html_e( 'Theme Options', 'zola' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Open the right editor for each shared site setting.', 'zola' ); ?></p>
		<div class="zola-admin-grid">
			<section class="zola-admin-card"><h2><?php esc_html_e( 'Global Design', 'zola' ); ?></h2><p><?php esc_html_e( 'Colors, typography, widths, blog layout and fallback header/footer settings.', 'zola' ); ?></p><a class="button button-primary" href="<?php echo esc_url( $customizer_url ); ?>"><?php esc_html_e( 'Open Customizer', 'zola' ); ?></a></section>
			<section class="zola-admin-card"><h2><?php esc_html_e( 'Header & Footer', 'zola' ); ?></h2><p><?php esc_html_e( 'Create and assign global Elementor site parts, or keep the theme fallback parts.', 'zola' ); ?></p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=zola-site-builder' ) ); ?>"><?php esc_html_e( 'Open Site Builder', 'zola' ); ?></a></section>
			<section class="zola-admin-card"><h2><?php esc_html_e( 'WordPress Menus', 'zola' ); ?></h2><p><?php esc_html_e( 'Create menus here, then choose them in the Zola Navigation Menu widget or theme locations.', 'zola' ); ?></p><a class="button" href="<?php echo esc_url( $menus_url ); ?>"><?php esc_html_e( 'Manage Menus', 'zola' ); ?></a></section>
			<section class="zola-admin-card"><h2><?php esc_html_e( 'Elementor Templates', 'zola' ); ?></h2><p><?php esc_html_e( 'Create and edit Saved Templates for use across your pages and site parts.', 'zola' ); ?></p><a class="button" href="<?php echo esc_url( $templates_url ); ?>"><?php esc_html_e( 'Manage Templates', 'zola' ); ?></a></section>
		</div>
	</div>
	<?php
}

/** Describe the theme features available in this free release. */
function zola_render_features_page(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$features = array(
		__( 'Elementor page building', 'zola' ) => __( 'Build page content and layouts with Elementor. The theme does not insert a fixed business homepage.', 'zola' ),
		__( 'Editable site parts', 'zola' ) => __( 'Assign global Header and Footer Saved Templates from the Site Builder.', 'zola' ),
		__( 'Theme widgets', 'zola' ) => __( 'Use the Hero and Navigation Menu widgets in Elementor templates.', 'zola' ),
		__( 'Blog and archive layouts', 'zola' ) => __( 'Choose grid, list or cover cards, columns, excerpts, metadata and sidebar settings.', 'zola' ),
		__( 'WooCommerce support', 'zola' ) => __( 'Use the shared theme layout with WooCommerce product grids and gallery support.', 'zola' ),
		__( 'Performance and accessibility', 'zola' ) => __( 'Deferred lightweight interactions, responsive images, reduced-motion support and keyboard-friendly navigation.', 'zola' ),
	);
	?>
	<div class="wrap zola-admin-page">
		<h1><?php esc_html_e( 'Zola Features', 'zola' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Theme-wide features available before adding any demo design.', 'zola' ); ?></p>
		<div class="zola-admin-grid">
			<?php foreach ( $features as $title => $description ) : ?>
				<section class="zola-admin-card"><h2><?php echo esc_html( $title ); ?></h2><p><?php echo esc_html( $description ); ?></p></section>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/** Report the active versions and the theme's minimum requirements. */
function zola_render_status_page(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$theme            = wp_get_theme();
	$wordpress_version = get_bloginfo( 'version' );
	$elementor_version = defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : __( 'Not active', 'zola' );
	$checks = array(
		array( 'label' => __( 'Theme version', 'zola' ), 'value' => $theme->get( 'Version' ), 'status' => true ),
		array( 'label' => __( 'WordPress', 'zola' ), 'value' => $wordpress_version, 'status' => version_compare( $wordpress_version, '6.8', '>=' ) ),
		array( 'label' => __( 'PHP', 'zola' ), 'value' => PHP_VERSION, 'status' => version_compare( PHP_VERSION, '8.4', '>=' ) ),
		array( 'label' => __( 'Elementor', 'zola' ), 'value' => $elementor_version, 'status' => defined( 'ELEMENTOR_VERSION' ) ),
		array( 'label' => __( 'WooCommerce', 'zola' ), 'value' => defined( 'WC_VERSION' ) ? WC_VERSION : __( 'Not active', 'zola' ), 'status' => true ),
	);
	?>
	<div class="wrap zola-admin-page">
		<h1><?php esc_html_e( 'System Status', 'zola' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Review the theme environment and optional integrations.', 'zola' ); ?></p>
		<table class="widefat striped zola-status-table"><tbody>
			<?php foreach ( $checks as $check ) : ?>
				<tr><th scope="row"><?php echo esc_html( $check['label'] ); ?></th><td><?php echo esc_html( $check['value'] ); ?></td><td><span class="zola-status zola-status--<?php echo $check['status'] ? 'ok' : 'notice'; ?>"><?php echo $check['status'] ? esc_html__( 'Ready', 'zola' ) : esc_html__( 'Check required', 'zola' ); ?></span></td></tr>
			<?php endforeach; ?>
		</tbody></table>
	</div>
	<?php
}

/** Give site owners direct steps for the common Elementor workflows. */
function zola_render_help_page(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	?>
	<div class="wrap zola-admin-page">
		<h1><?php esc_html_e( 'Help & Guides', 'zola' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Quick steps for building and managing a Zola site.', 'zola' ); ?></p>
		<div class="zola-admin-grid">
			<section class="zola-admin-card"><h2><?php esc_html_e( 'Build a page', 'zola' ); ?></h2><p><?php esc_html_e( 'Create a page, choose the Zola Full Width template if needed, then edit its content with Elementor.', 'zola' ); ?></p><a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>"><?php esc_html_e( 'Open Pages', 'zola' ); ?></a></section>
			<section class="zola-admin-card"><h2><?php esc_html_e( 'Build a Header or Footer', 'zola' ); ?></h2><p><?php esc_html_e( 'Create a Saved Template in Elementor, design it, then assign it in the Site Builder. Elementor Theme Builder display conditions take priority when available.', 'zola' ); ?></p><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=zola-site-builder' ) ); ?>"><?php esc_html_e( 'Open Site Builder', 'zola' ); ?></a></section>
			<section class="zola-admin-card"><h2><?php esc_html_e( 'Set shared styles', 'zola' ); ?></h2><p><?php esc_html_e( 'Use the Customizer for theme colors, typography, blog layouts and fallback header/footer options. Use Elementor Site Settings for Elementor global styles.', 'zola' ); ?></p><a class="button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Open Customizer', 'zola' ); ?></a></section>
		</div>
	</div>
	<?php
}

function zola_render_admin_page(): void {
	$front_page_id = 'page' === get_option( 'show_on_front' ) ? absint( get_option( 'page_on_front' ) ) : 0;
	$front_page    = $front_page_id ? get_post( $front_page_id ) : null;
	$is_elementor     = $front_page && get_post_meta( $front_page_id, '_elementor_edit_mode', true );
	$elementor_active = did_action( 'elementor/loaded' ) > 0;
	$edit_url         = $front_page
		? admin_url( 'post.php?post=' . $front_page_id . '&action=' . ( $elementor_active ? 'elementor' : 'edit' ) )
		: ( $elementor_active ? wp_nonce_url( admin_url( 'admin-post.php?action=zola_create_homepage' ), 'zola_create_homepage' ) : admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' ) );
	$button_label     = $front_page
		? ( $elementor_active ? esc_html__( 'Edit homepage with Elementor', 'zola' ) : esc_html__( 'Edit homepage', 'zola' ) )
		: ( $elementor_active ? esc_html__( 'Create an Elementor page', 'zola' ) : esc_html__( 'Find Elementor', 'zola' ) );
	$customize_url  = admin_url( 'customize.php' );
	?>
	<div class="wrap zola-dashboard">
		<h1><?php esc_html_e( 'Zola', 'zola' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Your Elementor theme center. Build pages in Elementor and manage shared site settings here.', 'zola' ); ?></p>
		<?php if ( ! $elementor_active ) : ?>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'Install and activate Elementor to edit page layouts and site templates.', 'zola' ); ?> <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'Find Elementor', 'zola' ); ?></a></p></div>
		<?php endif; ?>

		<div class="zola-dashboard__grid">
			<section class="zola-dashboard__card zola-dashboard__card--primary">
				<h2><?php esc_html_e( 'Homepage', 'zola' ); ?></h2>
				<?php if ( $front_page ) : ?>
					<p><?php printf( esc_html__( 'Current homepage: %s', 'zola' ), '<strong>' . esc_html( get_the_title( $front_page_id ) ) . '</strong>' ); ?></p>
					<p><?php echo $is_elementor ? esc_html__( 'This page is using Elementor.', 'zola' ) : ( $elementor_active ? esc_html__( 'Open this page in Elementor to start building its content.', 'zola' ) : esc_html__( 'Activate Elementor to edit this page with Elementor.', 'zola' ) ); ?></p>
				<?php else : ?>
					<p><?php esc_html_e( 'Your site currently shows the posts feed. Create a Home page in Elementor, then assign it as the static homepage in Settings → Reading.', 'zola' ); ?></p>
				<?php endif; ?>
				<a class="button button-primary" href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html( $button_label ); ?></a>
				<?php if ( ! $front_page ) : ?> <a class="button" href="<?php echo esc_url( admin_url( 'options-reading.php' ) ); ?>"><?php esc_html_e( 'Homepage settings', 'zola' ); ?></a><?php endif; ?>
			</section>

			<section class="zola-dashboard__card">
				<h2><?php esc_html_e( 'Global design', 'zola' ); ?></h2>
				<p><?php esc_html_e( 'Set theme colors, typography, content width, button shape, blog layout and header options.', 'zola' ); ?></p>
				<a class="button" href="<?php echo esc_url( $customize_url ); ?>"><?php esc_html_e( 'Open Customizer', 'zola' ); ?></a>
			</section>

			<section class="zola-dashboard__card">
				<h2><?php esc_html_e( 'Header Builder', 'zola' ); ?></h2>
				<p><?php esc_html_e( 'Arrange your logo, menu, buttons and other elements across header rows for desktop and mobile.', 'zola' ); ?></p>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=zola-header-builder' ) ); ?>"><?php esc_html_e( 'Customize header', 'zola' ); ?></a>
			</section>

			<section class="zola-dashboard__card">
				<h2><?php esc_html_e( 'Site Builder', 'zola' ); ?></h2>
				<p><?php esc_html_e( 'Create and assign global Header and Footer layouts in Elementor. Responsive settings stay in the Elementor editor.', 'zola' ); ?></p>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=zola-site-builder' ) ); ?>"><?php esc_html_e( 'Open Site Builder', 'zola' ); ?></a>
			</section>

			<section class="zola-dashboard__card">
				<h2><?php esc_html_e( 'Elementor widgets', 'zola' ); ?></h2>
				<p><?php esc_html_e( 'Add Hero and Navigation Menu widgets from the Zola category to your Elementor templates.', 'zola' ); ?></p>
				<p class="description"><?php esc_html_e( 'Navigation Menu displays WordPress menus; Hero can connect to compatible image or slide plugins.', 'zola' ); ?></p>
			</section>
		</div>

		<p class="zola-dashboard__footer"><?php esc_html_e( 'Pages and page sections are built with Elementor. Standard WordPress posts remain available for the blog.', 'zola' ); ?></p>
	</div>
	<?php
}

/** Put an Elementor-aware homepage link in the front-end admin bar. */
function zola_admin_bar_home_link( WP_Admin_Bar $admin_bar ): void {
	if ( ! is_user_logged_in() || ! current_user_can( 'edit_theme_options' ) || ! is_front_page() ) {
		return;
	}
	$front_page_id = 'page' === get_option( 'show_on_front' ) ? absint( get_option( 'page_on_front' ) ) : 0;
	$elementor_active = did_action( 'elementor/loaded' ) > 0;
	$href          = $front_page_id
		? admin_url( 'post.php?post=' . $front_page_id . '&action=' . ( $elementor_active ? 'elementor' : 'edit' ) )
		: admin_url( 'admin.php?page=zola' );
	$title         = $front_page_id
		? ( $elementor_active ? esc_html__( 'Edit homepage with Elementor', 'zola' ) : esc_html__( 'Edit homepage', 'zola' ) )
		: esc_html__( 'Set up Elementor homepage', 'zola' );
	$admin_bar->add_node( array(
		'id'     => 'zola-homepage',
		'parent' => 'site-name',
		'title'  => $title,
		'href'   => $href,
	) );
}
add_action( 'admin_bar_menu', 'zola_admin_bar_home_link', 100 );

/** Create a draft page and send the administrator straight to Elementor. */
function zola_create_homepage(): void {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'You do not have permission to create pages.', 'zola' ) );
	}
	check_admin_referer( 'zola_create_homepage' );
	$existing_home = get_page_by_path( 'home', OBJECT, 'page' );
	if ( $existing_home && current_user_can( 'edit_post', $existing_home->ID ) ) {
		$existing_editor = did_action( 'elementor/loaded' )
			? admin_url( 'post.php?post=' . absint( $existing_home->ID ) . '&action=elementor' )
			: admin_url( 'post.php?post=' . absint( $existing_home->ID ) . '&action=edit' );
		wp_safe_redirect( $existing_editor );
		exit;
	}
	$page_id       = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'draft',
		'post_title'  => __( 'Home', 'zola' ),
		'post_content' => '',
		'post_author' => get_current_user_id(),
		'meta_input'  => array(
			'_elementor_edit_mode' => 'builder',
			'_wp_page_template'    => 'template-full-width.php',
		),
	), true );
	if ( is_wp_error( $page_id ) ) {
		wp_die( esc_html( $page_id->get_error_message() ) );
	}
	$editor_url = class_exists( '\\Elementor\\Plugin' )
		? admin_url( 'post.php?post=' . absint( $page_id ) . '&action=elementor' )
		: admin_url( 'post.php?post=' . absint( $page_id ) . '&action=edit' );
	wp_safe_redirect( $editor_url );
	exit;
}
add_action( 'admin_post_zola_create_homepage', 'zola_create_homepage' );

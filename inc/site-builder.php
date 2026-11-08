<?php
/** Elementor-powered global site part assignments. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function zola_get_elementor_templates(): array {
	if ( ! post_type_exists( 'elementor_library' ) ) {
		return array();
	}

	return get_posts( array(
		'post_type'              => 'elementor_library',
		'post_status'            => 'publish',
		'posts_per_page'         => 200,
		'orderby'                => 'title',
		'order'                  => 'ASC',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	) );
}

function zola_render_site_builder_page(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$elementor_active = class_exists( '\\Elementor\\Plugin' );
	$templates        = zola_get_elementor_templates();
	$header_id        = absint( get_theme_mod( 'zola_header_template', 0 ) );
	$footer_id        = absint( get_theme_mod( 'zola_footer_template', 0 ) );
	$template_parts   = array(
		'header' => array(
			'label'       => esc_html__( 'Header', 'zola' ),
			'description' => esc_html__( 'Build your site navigation, logo, calls to action and mobile layout in Elementor.', 'zola' ),
			'fallback'    => esc_html__( 'Use the Zola header', 'zola' ),
			'template_id' => $header_id,
		),
		'footer' => array(
			'label'       => esc_html__( 'Footer', 'zola' ),
			'description' => esc_html__( 'Build your footer content, navigation, contact details and social links in Elementor.', 'zola' ),
			'fallback'    => esc_html__( 'Use the Zola footer', 'zola' ),
			'template_id' => $footer_id,
		),
	);
	$builder_saved     = isset( $_GET['builder_saved'] ) && is_string( $_GET['builder_saved'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['builder_saved'] ) );
	$builder_error     = isset( $_GET['builder_error'] ) && is_string( $_GET['builder_error'] ) && 'invalid_template' === sanitize_key( wp_unslash( $_GET['builder_error'] ) );
	?>
	<div class="wrap zola-site-builder">
		<h1><?php esc_html_e( 'Site Builder', 'zola' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Create global site parts with Elementor, then assign them to your whole site.', 'zola' ); ?></p>

		<?php if ( $builder_saved ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Site parts saved.', 'zola' ); ?></p></div>
		<?php endif; ?>
		<?php if ( $builder_error ) : ?>
			<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Choose a published Elementor template, or use the theme part.', 'zola' ); ?></p></div>
		<?php endif; ?>

		<?php if ( ! $elementor_active ) : ?>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'Install and activate Elementor to build and assign site parts.', 'zola' ); ?> <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'Find Elementor', 'zola' ); ?></a></p></div>
		<?php else : ?>
			<div class="zola-site-builder__intro">
				<div>
					<h2><?php esc_html_e( 'Build once. Use across the site.', 'zola' ); ?></h2>
					<p><?php esc_html_e( 'Create or edit each layout in Elementor. The assigned Header and Footer appear site-wide. Elementor Theme Builder templates with matching display conditions take priority.', 'zola' ); ?></p>
				</div>
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library' ) ); ?>"><?php esc_html_e( 'Manage Elementor templates', 'zola' ); ?></a>
			</div>

			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="zola_save_site_parts">
				<?php wp_nonce_field( 'zola_save_site_parts' ); ?>
				<div class="zola-site-builder__grid">
					<?php foreach ( $template_parts as $part => $settings ) : ?>
						<section class="zola-site-builder__card">
							<div class="zola-site-builder__card-head">
								<span class="zola-site-builder__icon" aria-hidden="true"><?php echo 'header' === $part ? '↑' : '↓'; ?></span>
								<div>
									<h2><?php echo esc_html( $settings['label'] ); ?></h2>
									<p><?php echo esc_html( $settings['description'] ); ?></p>
								</div>
							</div>

							<label for="zola-<?php echo esc_attr( $part ); ?>-template"><?php esc_html_e( 'Assigned Elementor template', 'zola' ); ?></label>
							<select id="zola-<?php echo esc_attr( $part ); ?>-template" name="<?php echo esc_attr( $part ); ?>_template">
								<option value="0" <?php selected( 0, $settings['template_id'] ); ?>><?php echo esc_html( $settings['fallback'] ); ?></option>
								<?php foreach ( $templates as $template ) : ?>
									<option value="<?php echo esc_attr( $template->ID ); ?>" <?php selected( $template->ID, $settings['template_id'] ); ?>><?php echo esc_html( $template->post_title ); ?></option>
								<?php endforeach; ?>
							</select>

							<?php if ( $settings['template_id'] && 'publish' === get_post_status( $settings['template_id'] ) ) : ?>
								<p class="zola-site-builder__current">
									<?php esc_html_e( 'Currently assigned:', 'zola' ); ?>
									<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $settings['template_id'] . '&action=elementor' ) ); ?>"><?php echo esc_html( get_the_title( $settings['template_id'] ) ); ?></a>
								</p>
							<?php else : ?>
								<p class="zola-site-builder__current"><?php esc_html_e( 'No Elementor template is assigned.', 'zola' ); ?></p>
							<?php endif; ?>
							<p class="zola-site-builder__actions">
								<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library#add_new' ) ); ?>"><?php printf( esc_html__( 'Create %s in Elementor', 'zola' ), esc_html( strtolower( $settings['label'] ) ) ); ?></a>
							</p>
						</section>
					<?php endforeach; ?>
				</div>

				<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Save site parts', 'zola' ); ?></button></p>
			</form>

			<div class="zola-site-builder__note">
				<h2><?php esc_html_e( 'Design in Elementor', 'zola' ); ?></h2>
				<p><?php esc_html_e( 'Create a Section or Container template in Elementor, design it, and publish it. Return here to assign it. Use Elementor responsive controls for desktop, tablet and mobile. Add the Zola Navigation Menu widget to show a WordPress menu.', 'zola' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Open shared theme settings', 'zola' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
	<style>
		.zola-site-builder { max-width: 1120px; }
		.zola-site-builder > .description { margin: 8px 0 24px; }
		.zola-site-builder__intro { display: flex; align-items: center; justify-content: space-between; gap: 28px; margin: 20px 0; padding: 24px 28px; border: 1px solid #dcdcde; border-radius: 10px; background: #fff; }
		.zola-site-builder__intro h2 { margin: 0 0 8px; font-size: 19px; }
		.zola-site-builder__intro p { max-width: 680px; margin: 0; color: #646970; }
		.zola-site-builder__grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 18px; }
		.zola-site-builder__card { min-width: 0; padding: 24px; border: 1px solid #dcdcde; border-radius: 10px; background: #fff; }
		.zola-site-builder__card-head { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 24px; }
		.zola-site-builder__icon { width: 40px; height: 40px; display: grid; flex: 0 0 auto; place-items: center; border-radius: 8px; color: #fff; background: #20242b; font-size: 22px; }
		.zola-site-builder__card h2 { margin: 0 0 5px; font-size: 17px; }
		.zola-site-builder__card-head p { margin: 0; color: #646970; }
		.zola-site-builder__card label { display: block; margin-bottom: 7px; font-weight: 600; }
		.zola-site-builder__card select { width: 100%; max-width: none; }
		.zola-site-builder__current { min-height: 20px; margin: 12px 0 0; color: #646970; }
		.zola-site-builder__actions { margin: 16px 0 0; }
		.zola-site-builder__note { margin-top: 20px; padding: 20px 24px; border-left: 3px solid #2563eb; background: #fff; }
		.zola-site-builder__note h2 { margin: 0 0 6px; font-size: 15px; }
		.zola-site-builder__note p { max-width: 760px; margin: 0 0 10px; color: #646970; }
		@media (max-width: 782px) { .zola-site-builder__intro { align-items: flex-start; flex-direction: column; } .zola-site-builder__grid { grid-template-columns: 1fr; } }
	</style>
	<?php
}

function zola_save_site_parts(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to manage site parts.', 'zola' ) );
	}
	check_admin_referer( 'zola_save_site_parts' );

	$validated_parts = array();
	foreach ( array( 'header', 'footer' ) as $part ) {
		$field          = $part . '_template';
		$raw_template_id = isset( $_POST[ $field ] ) ? wp_unslash( $_POST[ $field ] ) : 0;
		$template_id    = is_scalar( $raw_template_id ) ? absint( $raw_template_id ) : 0;
		if ( $template_id ) {
			$template = get_post( $template_id );
			if ( ! $template || 'elementor_library' !== $template->post_type || 'publish' !== $template->post_status || ! current_user_can( 'edit_post', $template_id ) ) {
				$redirect = add_query_arg( 'builder_error', 'invalid_template', admin_url( 'admin.php?page=zola-site-builder' ) );
				wp_safe_redirect( $redirect );
				exit;
			}
		}
		$validated_parts[ $part ] = $template_id;
	}

	foreach ( $validated_parts as $part => $template_id ) {
		set_theme_mod( 'zola_' . $part . '_template', $template_id );
	}

	$redirect = add_query_arg( 'builder_saved', '1', admin_url( 'admin.php?page=zola-site-builder' ) );
	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_zola_save_site_parts', 'zola_save_site_parts' );

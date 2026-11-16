<?php
/** Elementor Hero widget for Zola. */

if ( ! defined( 'ABSPATH' ) || ! class_exists( '\\Elementor\\Widget_Base' ) ) {
	exit;
}

class Zola_Hero_Widget extends \Elementor\Widget_Base {
	public function get_name() {
		return 'zola-hero';
	}

	public function get_title() {
		return esc_html__( 'Hero', 'zola' );
	}

	public function get_icon() {
		return 'eicon-image';
	}

	public function get_categories() {
		return array( 'zola-elements' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'zola_content', array(
			'label' => esc_html__( 'Content', 'zola' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'eyebrow', array(
			'label'       => esc_html__( 'Eyebrow', 'zola' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => '',
			'label_block' => true,
		) );

		$this->add_control( 'title', array(
			'label'       => esc_html__( 'Heading', 'zola' ),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'default'     => '',
			'label_block' => true,
			'description' => esc_html__( 'You can use line breaks or simple emphasis such as <em>this</em>.', 'zola' ),
		) );

		$this->add_control( 'description', array(
			'label'       => esc_html__( 'Description', 'zola' ),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'default'     => '',
			'label_block' => true,
		) );

		$this->add_control( 'image', array(
			'label'   => esc_html__( 'Background image', 'zola' ),
			'type'    => \Elementor\Controls_Manager::MEDIA,
			'default' => array( 'url' => '' ),
		) );

		$this->add_control( 'primary_label', array(
			'label'   => esc_html__( 'Primary button label', 'zola' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '',
		) );

		$this->add_control( 'primary_link', array(
			'label'       => esc_html__( 'Primary button link', 'zola' ),
			'type'        => \Elementor\Controls_Manager::URL,
			'placeholder' => 'https://example.com/contact/',
			'default'     => array( 'url' => '' ),
		) );

		$this->add_control( 'secondary_label', array(
			'label'   => esc_html__( 'Secondary button label', 'zola' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '',
		) );

		$this->add_control( 'secondary_link', array(
			'label'       => esc_html__( 'Secondary button link', 'zola' ),
			'type'        => \Elementor\Controls_Manager::URL,
			'placeholder' => 'https://example.com/about/',
			'default'     => array( 'url' => '' ),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'zola_layout', array(
			'label' => esc_html__( 'Layout', 'zola' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'height', array(
			'label'   => esc_html__( 'Hero height', 'zola' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'full',
			'options' => array(
				'full'    => esc_html__( 'Full screen', 'zola' ),
				'large'   => esc_html__( 'Large', 'zola' ),
				'compact' => esc_html__( 'Compact', 'zola' ),
			),
		) );
		$this->add_control( 'alignment', array(
			'label'   => esc_html__( 'Content alignment', 'zola' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'left',
			'options' => array(
				'left'   => esc_html__( 'Left', 'zola' ),
				'center' => esc_html__( 'Center', 'zola' ),
				'right'  => esc_html__( 'Right', 'zola' ),
			),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'zola_performance', array(
			'label' => esc_html__( 'Performance', 'zola' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'prioritize_image', array(
			'label'        => esc_html__( 'Prioritize this image', 'zola' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'Yes', 'zola' ),
			'label_off'    => esc_html__( 'No', 'zola' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'description'  => esc_html__( 'Keep this on for the first, above-the-fold Hero. Turn it off for Hero widgets farther down the page.', 'zola' ),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'zola_style', array(
			'label' => esc_html__( 'Style', 'zola' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'title_color', array(
			'label'     => esc_html__( 'Heading color', 'zola' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => array( '{{WRAPPER}} .hero h1' => 'color: {{VALUE}};' ),
		) );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'title_typography',
			'selector' => '{{WRAPPER}} .hero h1',
		) );
		$this->add_control( 'description_color', array(
			'label'     => esc_html__( 'Description color', 'zola' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => array( '{{WRAPPER}} .hero__bottom > p' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'overlay_opacity', array(
			'label'      => esc_html__( 'Image overlay strength', 'zola' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'unitless' ),
			'range'      => array( 'unitless' => array( 'min' => 0.2, 'max' => 1, 'step' => 0.05 ) ),
			'default'    => array( 'unit' => 'unitless', 'size' => 1 ),
			'selectors'  => array( '{{WRAPPER}} .hero__media::after' => 'opacity: {{SIZE}};' ),
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$settings = is_array( $settings ) ? $settings : array();
		$image_setting = isset( $settings['image'] ) && is_array( $settings['image'] ) && isset( $settings['image']['url'] ) && is_string( $settings['image']['url'] ) ? $settings['image']['url'] : '';
		$image         = esc_url_raw( $image_setting );
		$filtered_image = apply_filters( 'zola_hero_image', $image );
		$image         = is_string( $filtered_image ) ? esc_url_raw( $filtered_image ) : '';
		$image_id      = isset( $settings['image']['id'] ) ? absint( $settings['image']['id'] ) : 0;
		if ( ! $image_id || ! wp_attachment_is_image( $image_id ) || $image !== esc_url_raw( $image_setting ) ) {
			$image_id = 0;
		}
		$height          = isset( $settings['height'] ) && in_array( $settings['height'], array( 'full', 'large', 'compact' ), true ) ? $settings['height'] : 'full';
		$alignment       = isset( $settings['alignment'] ) && in_array( $settings['alignment'], array( 'left', 'center', 'right' ), true ) ? $settings['alignment'] : 'left';
		$prioritize_image = ! isset( $settings['prioritize_image'] ) || 'yes' === $settings['prioritize_image'];
		$title_id        = 'zola-hero-title-' . sanitize_html_class( $this->get_id() );
		$eyebrow         = isset( $settings['eyebrow'] ) && is_string( $settings['eyebrow'] ) ? trim( $settings['eyebrow'] ) : '';
		$description     = isset( $settings['description'] ) && is_string( $settings['description'] ) ? trim( $settings['description'] ) : '';
		$primary_label   = isset( $settings['primary_label'] ) && is_string( $settings['primary_label'] ) ? trim( $settings['primary_label'] ) : '';
		$secondary_label = isset( $settings['secondary_label'] ) && is_string( $settings['secondary_label'] ) ? trim( $settings['secondary_label'] ) : '';
		$primary_link    = isset( $settings['primary_link'] ) && is_array( $settings['primary_link'] ) ? $settings['primary_link'] : array();
		$secondary_link  = isset( $settings['secondary_link'] ) && is_array( $settings['secondary_link'] ) ? $settings['secondary_link'] : array();
	$primary_url      = isset( $primary_link['url'] ) && is_string( $primary_link['url'] ) ? esc_url_raw( $primary_link['url'] ) : '';
	$secondary_url    = isset( $secondary_link['url'] ) && is_string( $secondary_link['url'] ) ? esc_url_raw( $secondary_link['url'] ) : '';
	$has_primary_link = '' !== $primary_url;
	$has_secondary_link = '' !== $secondary_url;
	if ( $has_primary_link ) {
		$primary_link['url'] = $primary_url;
	}
	if ( $has_secondary_link ) {
		$secondary_link['url'] = $secondary_url;
	}
		$title_content = isset( $settings['title'] ) && is_string( $settings['title'] )
			? trim( wp_kses( $settings['title'], array( 'br' => array(), 'em' => array(), 'strong' => array(), 'span' => array( 'class' => array() ) ) ) )
			: '';
		if ( ! $image && ! $eyebrow && ! $title_content && ! $description && ! ( $primary_label && $has_primary_link ) && ! ( $secondary_label && $has_secondary_link ) ) {
			return;
		}

		$this->add_render_attribute( 'primary-link', 'class', 'button button--accent' );
		$this->add_link_attributes( 'primary-link', $primary_link );
		$this->add_render_attribute( 'secondary-link', 'class', 'button button--outline-light' );
		$this->add_link_attributes( 'secondary-link', $secondary_link );
		?>
		<section class="hero hero--<?php echo esc_attr( $height ); ?> hero--align-<?php echo esc_attr( $alignment ); ?>" data-zola-hero <?php if ( $title_content ) : ?>aria-labelledby="<?php echo esc_attr( $title_id ); ?>"<?php else : ?>aria-label="<?php esc_attr_e( 'Hero section', 'zola' ); ?>"<?php endif; ?>>
			<div class="hero__media" data-zola-hero-media>
				<?php if ( $image ) : ?>
					<div class="hero__image" aria-hidden="true">
						<?php if ( $image_id ) : ?>
							<?php
							$image_attributes = array(
								'alt'      => '',
								'loading'  => $prioritize_image ? 'eager' : 'lazy',
								'decoding' => 'async',
							);
							if ( $prioritize_image ) {
								$image_attributes['fetchpriority'] = 'high';
								$image_attributes['data-no-lazy']  = '1';
							}
							echo wp_get_attachment_image( $image_id, 'full', false, $image_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress generates responsive, escaped attachment image markup.
							?>
						<?php else : ?>
							<img src="<?php echo esc_url( $image ); ?>" alt="" loading="<?php echo $prioritize_image ? 'eager' : 'lazy'; ?>" decoding="async"<?php if ( $prioritize_image ) : ?> fetchpriority="high" data-no-lazy="1"<?php endif; ?>>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php do_action( 'zola_hero_media', $image, $prioritize_image ); ?>
			</div>
			<div class="hero__content wrap">
				<div class="hero__copy">
					<?php if ( $eyebrow ) : ?><p class="eyebrow eyebrow--light" data-hero-intro><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
					<?php if ( $title_content ) : ?><h1 id="<?php echo esc_attr( $title_id ); ?>" data-hero-intro><?php echo $title_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content was passed through wp_kses() with a small allowlist above. ?></h1><?php endif; ?>
					<div class="hero__bottom" data-hero-intro>
						<?php if ( $description ) : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?>
						<?php if ( ( $primary_label && $has_primary_link ) || ( $secondary_label && $has_secondary_link ) ) : ?>
							<div class="hero__actions">
								<?php if ( $primary_label && $has_primary_link ) : ?><a <?php echo $this->get_render_attribute_string( 'primary-link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $primary_label ); ?><span aria-hidden="true">↗</span></a><?php endif; ?>
								<?php if ( $secondary_label && $has_secondary_link ) : ?><a <?php echo $this->get_render_attribute_string( 'secondary-link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $secondary_label ); ?><span aria-hidden="true">↘</span></a><?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}

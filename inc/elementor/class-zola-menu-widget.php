<?php
/** Accessible WordPress navigation widget for Elementor templates. */

if ( ! defined( 'ABSPATH' ) || ! class_exists( '\\Elementor\\Widget_Base' ) ) {
	exit;
}

class Zola_Menu_Widget extends \Elementor\Widget_Base {
	public function get_name() {
		return 'zola-menu';
	}

	public function get_title() {
		return esc_html__( 'Navigation Menu', 'zola' );
	}

	public function get_icon() {
		return 'eicon-menu-bar';
	}

	public function get_categories() {
		return array( 'zola-elements' );
	}

	protected function register_controls() {
		$menu_options = array( 0 => esc_html__( 'Use Primary navigation location', 'zola' ) );
		foreach ( wp_get_nav_menus() as $menu ) {
			$menu_options[ $menu->term_id ] = $menu->name;
		}

		$this->start_controls_section( 'zola_menu_content', array(
			'label' => esc_html__( 'Menu', 'zola' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'menu_id', array(
			'label'       => esc_html__( 'WordPress menu', 'zola' ),
			'type'        => \Elementor\Controls_Manager::SELECT,
			'options'     => $menu_options,
			'default'     => 0,
			'description' => esc_html__( 'Create and edit menus in Appearance → Menus. The Primary location is used by default.', 'zola' ),
		) );
		$this->add_control( 'aria_label', array(
			'label'       => esc_html__( 'Navigation label', 'zola' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => esc_html__( 'Primary navigation', 'zola' ),
			'label_block' => true,
		) );
		$this->add_control( 'layout', array(
			'label'   => esc_html__( 'Layout', 'zola' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'horizontal',
			'options' => array(
				'horizontal' => esc_html__( 'Horizontal', 'zola' ),
				'vertical'   => esc_html__( 'Vertical', 'zola' ),
			),
		) );
		$this->add_control( 'alignment', array(
			'label'   => esc_html__( 'Alignment', 'zola' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'left',
			'options' => array(
				'left'   => esc_html__( 'Left', 'zola' ),
				'center' => esc_html__( 'Center', 'zola' ),
				'right'  => esc_html__( 'Right', 'zola' ),
			),
		) );
		$this->add_control( 'depth', array(
			'label'   => esc_html__( 'Menu depth', 'zola' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 2,
			'options' => array( 1 => 1, 2 => 2, 3 => 3 ),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'zola_menu_style', array(
			'label' => esc_html__( 'Style', 'zola' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'menu_typography',
			'selector' => '{{WRAPPER}} .zola-menu__list a, {{WRAPPER}} .zola-menu .menu a',
		) );
		$this->add_control( 'link_color', array(
			'label'     => esc_html__( 'Link color', 'zola' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .zola-menu__list a, {{WRAPPER}} .zola-menu .menu a' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'hover_color', array(
			'label'     => esc_html__( 'Link hover color', 'zola' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .zola-menu__list a:hover, {{WRAPPER}} .zola-menu .menu a:hover' => 'color: {{VALUE}};' ),
		) );
		$this->add_control( 'item_gap', array(
			'label'      => esc_html__( 'Item spacing', 'zola' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 64 ) ),
			'default'    => array( 'unit' => 'px', 'size' => 24 ),
			'selectors'  => array( '{{WRAPPER}} .zola-menu__list, {{WRAPPER}} .zola-menu .menu' => 'gap: {{SIZE}}{{UNIT}};' ),
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$menu_id   = isset( $settings['menu_id'] ) ? absint( $settings['menu_id'] ) : 0;
		$layout    = isset( $settings['layout'] ) && in_array( $settings['layout'], array( 'horizontal', 'vertical' ), true ) ? $settings['layout'] : 'horizontal';
		$alignment = isset( $settings['alignment'] ) && in_array( $settings['alignment'], array( 'left', 'center', 'right' ), true ) ? $settings['alignment'] : 'left';
		$depth     = isset( $settings['depth'] ) ? min( 3, max( 1, absint( $settings['depth'] ) ) ) : 2;
		$label     = ! empty( $settings['aria_label'] ) ? sanitize_text_field( $settings['aria_label'] ) : esc_html__( 'Primary navigation', 'zola' );
		$widget_id = 'zola-menu-' . sanitize_html_class( $this->get_id() );
		?>
		<div class="zola-menu zola-menu--<?php echo esc_attr( $layout ); ?> zola-menu--align-<?php echo esc_attr( $alignment ); ?>" data-zola-menu>
			<button class="zola-menu__toggle" type="button" data-zola-menu-toggle aria-expanded="false" aria-controls="<?php echo esc_attr( $widget_id ); ?>-nav">
				<span class="zola-menu__icon" aria-hidden="true"><i></i><i></i></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'zola' ); ?></span>
			</button>
			<?php
			wp_nav_menu( array(
				'menu'                 => $menu_id ? $menu_id : '',
				'theme_location'       => $menu_id ? '' : 'primary',
				'container'            => 'nav',
				'container_class'      => 'zola-menu__nav',
				'container_id'         => $widget_id . '-nav',
				'container_aria_label' => $label,
				'menu_class'           => 'zola-menu__list',
				'menu_id'              => $widget_id . '-list',
				'depth'                => $depth,
				'fallback_cb'          => $menu_id ? false : 'zola_fallback_menu',
			) );
			?>
		</div>
		<?php
	}
}

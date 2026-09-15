<?php
/**
 * Elementor widgets for the homepage sections.
 *
 * The point of these is that the client can drag, reorder and retype the
 * sections himself without being able to break the parts that are not
 * draggable - the arabesque lines, the drawn stadium outline, the half-oval
 * under About. Those stay in the theme's CSS/JS and the widget just emits the
 * markup they hook onto.
 *
 * Every widget renders through the same lr_section_*() function the PHP
 * template uses, so there is exactly one implementation of each section and an
 * Elementor page and a theme-rendered page cannot drift apart.
 *
 * @package lenfant-roi-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * NB: this file is required from inside the 'elementor/widgets/register'
 * callback, never at theme load. \Elementor\Widget_Base is autoloaded lazily
 * and does not exist earlier, so declaring these classes any sooner fatals.
 * The panel category is registered from functions.php for the same reason.
 */

/**
 * Shared behaviour for every section widget.
 */
abstract class LR_Section_Widget extends \Elementor\Widget_Base {

	/**
	 * Panel category.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'smileyworld' );
	}

	/**
	 * Keywords so searching "band" or "hero" finds them.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'smileyworld', 'section', 'creche' );
	}

	/**
	 * These sections carry their own CSS/JS from the theme stylesheet.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'lr-main' );
	}

	/**
	 * Script handle.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'lr-main' );
	}
}

/**
 * Hero.
 */
class LR_Widget_Hero extends LR_Section_Widget {

	public function get_name() {
		return 'lr-hero';
	}

	public function get_title() {
		return __( 'Hero', 'lenfant-roi-child' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array( 'label' => __( 'Hero', 'lenfant-roi-child' ) )
		);

		$this->add_control(
			'note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'The photo or video, and the lettering, are still set under Appearance &rsaquo; Customize &rsaquo; Homepage layout &rsaquo; Hero - they are shared with the rest of the site.', 'lenfant-roi-child' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'   => __( 'Line under the lettering', 'lenfant-roi-child' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => lr_opt( 'hero_subtitle' ),
			)
		);
		$this->add_control(
			'footer_title',
			array(
				'label'   => __( 'Large line over the photo', 'lenfant-roi-child' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => lr_opt( 'hero_footer_title' ),
			)
		);
		$this->add_control(
			'footer_subtitle',
			array(
				'label'   => __( 'Small line over the photo', 'lenfant-roi-child' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => lr_opt( 'hero_footer_subtitle' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		echo lr_section_hero( // phpcs:ignore WordPress.Security.EscapingOutput
			array(
				'subtitle'        => $s['subtitle'],
				'footer_title'    => $s['footer_title'],
				'footer_subtitle' => $s['footer_subtitle'],
			)
		);
	}
}

/**
 * About - lettering, two paragraphs, and the half-oval beneath.
 */
class LR_Widget_About extends LR_Section_Widget {

	public function get_name() {
		return 'lr-about';
	}

	public function get_title() {
		return __( 'About', 'lenfant-roi-child' );
	}

	public function get_icon() {
		return 'eicon-text-area';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array( 'label' => __( 'About', 'lenfant-roi-child' ) )
		);

		$this->add_control(
			'image',
			array(
				'label'       => __( 'Lettering on the left', 'lenfant-roi-child' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => __( 'Usually the logo. Leave empty for the placeholder lettering.', 'lenfant-roi-child' ),
			)
		);
		$this->add_control(
			'text_1',
			array(
				'label'   => __( 'First paragraph', 'lenfant-roi-child' ),
				'type'    => \Elementor\Controls_Manager::WYSIWYG,
				'default' => lr_opt( 'about_text_1' ),
			)
		);
		$this->add_control(
			'text_2',
			array(
				'label'   => __( 'Second paragraph', 'lenfant-roi-child' ),
				'type'    => \Elementor\Controls_Manager::WYSIWYG,
				'default' => lr_opt( 'about_text_2' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		echo lr_section_about( // phpcs:ignore WordPress.Security.EscapingOutput
			array(
				'image_url' => isset( $s['image']['url'] ) ? $s['image']['url'] : '',
				'text_1'    => $s['text_1'],
				'text_2'    => $s['text_2'],
			)
		);
	}
}

/**
 * The full-bleed quotation.
 */
class LR_Widget_Quote extends LR_Section_Widget {

	public function get_name() {
		return 'lr-quote';
	}

	public function get_title() {
		return __( 'Quote', 'lenfant-roi-child' );
	}

	public function get_icon() {
		return 'eicon-blockquote';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array( 'label' => __( 'Quote', 'lenfant-roi-child' ) )
		);

		$this->add_control(
			'quote',
			array(
				'label'   => __( 'Quotation', 'lenfant-roi-child' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => lr_opt( 'quote_text' ),
			)
		);
		$this->add_control(
			'author',
			array(
				'label'   => __( 'Attributed to', 'lenfant-roi-child' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => lr_opt( 'quote_author' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		echo lr_section_quote( // phpcs:ignore WordPress.Security.EscapingOutput
			array(
				'quote'  => $s['quote'],
				'author' => $s['author'],
			)
		);
	}
}

/**
 * The repeating teal band - pages 7, 8, 9 and 11-14 of the brief.
 */
class LR_Widget_Band extends LR_Section_Widget {

	public function get_name() {
		return 'lr-band';
	}

	public function get_title() {
		return __( 'Band', 'lenfant-roi-child' );
	}

	public function get_icon() {
		return 'eicon-navigation-horizontal';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array( 'label' => __( 'Band', 'lenfant-roi-child' ) )
		);

		$this->add_control(
			'side',
			array(
				'label'       => __( 'Which edge it runs off', 'lenfant-roi-child' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'left',
				'options'     => array(
					'left'  => __( 'Left', 'lenfant-roi-child' ),
					'right' => __( 'Right', 'lenfant-roi-child' ),
				),
				'description' => __( 'The original alternates them down the page.', 'lenfant-roi-child' ),
			)
		);
		$this->add_control(
			'label',
			array(
				'label'   => __( 'Small gold label', 'lenfant-roi-child' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Horaires', 'lenfant-roi-child' ),
			)
		);
		$this->add_control(
			'icon_preset',
			array(
				'label'       => __( 'Icon', 'lenfant-roi-child' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'icon-clock.png',
				'options'     => lr_icon_choices(),
				'description' => __( 'The icons that came with the design.', 'lenfant-roi-child' ),
			)
		);
		$this->add_control(
			'icon_image',
			array(
				'label'       => __( 'Or upload your own', 'lenfant-roi-child' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => __( 'A PNG with a transparent background. Overrides the one above.', 'lenfant-roi-child' ),
			)
		);
		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Heading', 'lenfant-roi-child' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => __( 'Du lundi au vendredi', 'lenfant-roi-child' ),
			)
		);
		$this->add_control(
			'value',
			array(
				'label'       => __( 'Large line (optional)', 'lenfant-roi-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Used for the opening times. Leave empty on the other bands.', 'lenfant-roi-child' ),
			)
		);
		$this->add_control(
			'copy',
			array(
				'label' => __( 'Text inside the band', 'lenfant-roi-child' ),
				'type'  => \Elementor\Controls_Manager::WYSIWYG,
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		echo lr_section_band( // phpcs:ignore WordPress.Security.EscapingOutput
			array(
				'side'     => $s['side'],
				'label'    => $s['label'],
				'icon'     => isset( $s['icon_preset'] ) ? $s['icon_preset'] : '',
				'icon_url' => isset( $s['icon_image']['url'] ) ? $s['icon_image']['url'] : '',
				'heading'  => $s['heading'],
				'value'    => $s['value'],
				'copy'     => $s['copy'],
			)
		);
	}
}


/**
 * Brief p6 - accessibility.
 */
class LR_Widget_Access extends LR_Section_Widget {

	public function get_name() {
		return 'lr-access';
	}

	public function get_title() {
		return __( 'Accessibility', 'lenfant-roi-child' );
	}

	public function get_icon() {
		return 'eicon-map-pin';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array( 'label' => __( 'Accessibility', 'lenfant-roi-child' ) )
		);

		$this->add_control(
			'image',
			array(
				'label' => __( 'Photo', 'lenfant-roi-child' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);
		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Heading', 'lenfant-roi-child' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => lr_opt( 'access_heading' ),
			)
		);
		$this->add_control(
			'list',
			array(
				'label'       => __( 'Transport list', 'lenfant-roi-child' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 5,
				'default'     => lr_opt( 'access_list' ),
				'description' => __( 'One per line.', 'lenfant-roi-child' ),
			)
		);
		$this->add_control(
			'copy',
			array(
				'label'   => __( 'Text on the right', 'lenfant-roi-child' ),
				'type'    => \Elementor\Controls_Manager::WYSIWYG,
				'default' => wpautop( lr_opt( 'access_copy' ) ),
			)
		);
		$this->add_control(
			'card',
			array(
				'label'        => __( 'White card on teal', 'lenfant-roi-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'description'  => __( 'Stacks into one panel with the carded section above.', 'lenfant-roi-child' ),
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		echo lr_section_access( // phpcs:ignore WordPress.Security.EscapingOutput
			array(
				'image_url' => isset( $s['image']['url'] ) ? $s['image']['url'] : '',
				'heading'   => $s['heading'],
				'list'      => $s['list'],
				'copy'      => $s['copy'],
				'card'      => 'yes' === $s['card'],
			)
		);
	}
}



/**
 * Brief p5 - Google reviews.
 */
class LR_Widget_Reviews extends LR_Section_Widget {

	public function get_name() {
		return 'lr-reviews';
	}

	public function get_title() {
		return __( 'Google reviews', 'lenfant-roi-child' );
	}

	public function get_icon() {
		return 'eicon-review';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array( 'label' => __( 'Google reviews', 'lenfant-roi-child' ) )
		);

		$this->add_control(
			'image',
			array(
				'label' => __( 'Photo above the reviews', 'lenfant-roi-child' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);
		$this->add_control(
			'shortcode',
			array(
				'label'       => __( 'Reviews plugin shortcode', 'lenfant-roi-child' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => lr_opt( 'reviews_shortcode' ),
				'description' => __( 'From the reviews plugin, e.g. [trustindex data-widget-id=xxxx]. Nothing shows until your client connects their Google account.', 'lenfant-roi-child' ),
			)
		);
		$this->add_control(
			'card',
			array(
				'label'        => __( 'White card on teal', 'lenfant-roi-child' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'description'  => __( 'Stacks into one panel with the next carded section.', 'lenfant-roi-child' ),
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		echo lr_section_reviews( // phpcs:ignore WordPress.Security.EscapingOutput
			array(
				'image_url' => isset( $s['image']['url'] ) ? $s['image']['url'] : '',
				'shortcode' => $s['shortcode'],
				'card'      => 'yes' === $s['card'],
			)
		);
	}
}

/**
 * Brief p15 - monthly prices.
 */
class LR_Widget_Tarifs extends LR_Section_Widget {

	public function get_name() {
		return 'lr-tarifs';
	}

	public function get_title() {
		return __( 'Prices', 'lenfant-roi-child' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array( 'label' => __( 'Prices', 'lenfant-roi-child' ) )
		);

		$rows = __( 'One per line, written as label|price.', 'lenfant-roi-child' );

		$this->add_control( 'title', array(
			'label'   => __( 'Heading', 'lenfant-roi-child' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => lr_opt( 'tarifs_title' ),
		) );
		$this->add_control( 't1_title', array(
			'label'   => __( 'Left table - title', 'lenfant-roi-child' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => lr_opt( 'tarifs_t1_title' ),
		) );
		$this->add_control( 't1_times', array(
			'label'       => __( 'Left table - times', 'lenfant-roi-child' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => lr_opt( 'tarifs_t1_times' ),
			'description' => __( 'Two slots? Separate them with a |', 'lenfant-roi-child' ),
		) );
		$this->add_control( 't1_rows', array(
			'label'       => __( 'Left table - rows', 'lenfant-roi-child' ),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'rows'        => 6,
			'default'     => lr_opt( 'tarifs_t1_rows' ),
			'description' => $rows,
		) );
		$this->add_control( 't2_title', array(
			'label'   => __( 'Right table - title', 'lenfant-roi-child' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => lr_opt( 'tarifs_t2_title' ),
		) );
		$this->add_control( 't2_times', array(
			'label'   => __( 'Right table - times', 'lenfant-roi-child' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => lr_opt( 'tarifs_t2_times' ),
		) );
		$this->add_control( 't2_rows', array(
			'label'       => __( 'Right table - rows', 'lenfant-roi-child' ),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'rows'        => 6,
			'default'     => lr_opt( 'tarifs_t2_rows' ),
			'description' => $rows,
		) );
		$this->add_control( 'formula', array(
			'label'       => __( 'Formula circles', 'lenfant-roi-child' ),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'rows'        => 7,
			'default'     => lr_opt( 'tarifs_formula' ),
			'description' => __( 'One per line: text|the operator after it. Leave the last operator empty.', 'lenfant-roi-child' ),
		) );
		$this->add_control( 'note', array(
			'label'   => __( 'Small print', 'lenfant-roi-child' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => lr_opt( 'tarifs_note' ),
		) );
		$this->add_control( 'discount', array(
			'label'       => __( 'Discounts', 'lenfant-roi-child' ),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'default'     => lr_opt( 'tarifs_discount' ),
			'description' => __( 'One per line: value|who it applies to', 'lenfant-roi-child' ),
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		echo lr_section_tarifs( // phpcs:ignore WordPress.Security.EscapingOutput
			array(
				'title'    => $s['title'],
				't1_title' => $s['t1_title'],
				't1_times' => $s['t1_times'],
				't1_rows'  => $s['t1_rows'],
				't2_title' => $s['t2_title'],
				't2_times' => $s['t2_times'],
				't2_rows'  => $s['t2_rows'],
				'formula'  => $s['formula'],
				'note'     => $s['note'],
				'discount' => $s['discount'],
			)
		);
	}
}

/**
 * Register the widgets.
 *
 * @param \Elementor\Widgets_Manager $manager Widget manager.
 */
function lr_elementor_widgets( $manager ) {
	$manager->register( new LR_Widget_Hero() );
	$manager->register( new LR_Widget_About() );
	$manager->register( new LR_Widget_Quote() );
	$manager->register( new LR_Widget_Band() );
	$manager->register( new LR_Widget_Access() );
	$manager->register( new LR_Widget_Tarifs() );
	$manager->register( new LR_Widget_Reviews() );
}

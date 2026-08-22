<?php
/**
 * Customizer controls.
 *
 * Everything the client needs to change on the homepage without touching code
 * lives here: Appearance > Customize > "Homepage layout".
 *
 * @package lenfant-roi-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default value for every theme setting, in one place.
 *
 * Templates read through lr_opt() so a setting that has never been saved still
 * renders the same thing the demo did, rather than an empty gap.
 *
 * @return array
 */
function lr_defaults() {
	return array(
		// Header.
		'lang_1_label'         => 'FR',
		'lang_1_url'           => '#',
		'lang_2_label'         => 'EN',
		'lang_2_url'           => '#',

		// Hero.
		'hero_title_image'     => '',
		'hero_subtitle'        => 'In Luxembourg',
		'hero_media_type'      => 'image',
		'hero_image'           => '',
		'hero_video'           => '',
		'hero_video_poster'    => '',
		'hero_scroll_label'    => 'Scroll',
		'hero_footer_title'    => "Berceau de l'excellence",
		'hero_footer_subtitle' => 'For 20 years',

		// Visit tab.
		'toolbar_show'         => true,
		'toolbar_label'        => 'Schedule a visit',
		'toolbar_url'          => '#',

		// Palette.
		'color_primary'        => '#d7a33d',
		'color_secondary'      => '#02162a',
		'color_tertiary'       => '#1d1d1b',
	);
}

/**
 * Read a theme setting, falling back to its default.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function lr_opt( $key ) {
	$defaults = lr_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Register the panel, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function lr_customize_register( $wp_customize ) {
	$defaults = lr_defaults();

	$wp_customize->add_panel(
		'lr_home',
		array(
			'title'       => __( 'Homepage layout', 'lenfant-roi-child' ),
			'description' => __( 'Text, photos and colours for the homepage.', 'lenfant-roi-child' ),
			'priority'    => 20,
		)
	);

	/**
	 * Shorthand for a plain text/textarea/url control.
	 *
	 * @param string $id      Setting id.
	 * @param string $section Section id.
	 * @param string $label   Control label.
	 * @param string $type    Control type.
	 * @param string $desc    Optional description.
	 */
	$text = function ( $id, $section, $label, $type = 'text', $desc = '' ) use ( $wp_customize, $defaults ) {
		$sanitize = 'url' === $type ? 'esc_url_raw' : 'wp_kses_post';

		$wp_customize->add_setting(
			$id,
			array(
				'default'           => isset( $defaults[ $id ] ) ? $defaults[ $id ] : '',
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $label,
				'description' => $desc,
				'section'     => $section,
				'type'        => $type,
			)
		);
	};

	/* --- header ---------------------------------------------------------- */
	$wp_customize->add_section(
		'lr_header',
		array(
			'title' => __( 'Header', 'lenfant-roi-child' ),
			'panel' => 'lr_home',
		)
	);

	$wp_customize->add_setting(
		'logo_svg_replace',
		array(
			'default'           => '',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'logo_svg_replace',
			array(
				'label'       => __( 'Logo', 'lenfant-roi-child' ),
				'description' => __( 'Leave empty to keep the placeholder lettering. A PNG with a transparent background works best.', 'lenfant-roi-child' ),
				'section'     => 'lr_header',
				'mime_type'   => 'image',
			)
		)
	);

	$text( 'lang_1_label', 'lr_header', __( 'Language 1 label', 'lenfant-roi-child' ) );
	$text( 'lang_1_url', 'lr_header', __( 'Language 1 link', 'lenfant-roi-child' ), 'url' );
	$text( 'lang_2_label', 'lr_header', __( 'Language 2 label', 'lenfant-roi-child' ) );
	$text( 'lang_2_url', 'lr_header', __( 'Language 2 link', 'lenfant-roi-child' ), 'url' );

	/* --- hero ------------------------------------------------------------ */
	$wp_customize->add_section(
		'lr_hero',
		array(
			'title' => __( 'Hero', 'lenfant-roi-child' ),
			'panel' => 'lr_home',
		)
	);

	$wp_customize->add_setting(
		'hero_title_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'hero_title_image',
			array(
				'label'       => __( 'Title lettering', 'lenfant-roi-child' ),
				'description' => __( 'Leave empty to keep the placeholder lettering.', 'lenfant-roi-child' ),
				'section'     => 'lr_hero',
				'mime_type'   => 'image',
			)
		)
	);

	$text( 'hero_subtitle', 'lr_hero', __( 'Subtitle under the title', 'lenfant-roi-child' ) );

	$wp_customize->add_setting(
		'hero_media_type',
		array(
			'default'           => 'image',
			'sanitize_callback' => function ( $value ) {
				return in_array( $value, array( 'image', 'video' ), true ) ? $value : 'image';
			},
		)
	);
	$wp_customize->add_control(
		'hero_media_type',
		array(
			'label'   => __( 'Hero shows', 'lenfant-roi-child' ),
			'section' => 'lr_hero',
			'type'    => 'radio',
			'choices' => array(
				'image' => __( 'A photo', 'lenfant-roi-child' ),
				'video' => __( 'A video', 'lenfant-roi-child' ),
			),
		)
	);

	$wp_customize->add_setting(
		'hero_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'hero_image',
			array(
				'label'       => __( 'Hero photo', 'lenfant-roi-child' ),
				'description' => __( 'Wide shot, at least 2000px across.', 'lenfant-roi-child' ),
				'section'     => 'lr_hero',
				'mime_type'   => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'hero_video',
		array(
			'default'           => '',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'hero_video',
			array(
				'label'       => __( 'Hero video', 'lenfant-roi-child' ),
				'description' => __( 'MP4. Keep it under about 5 MB or the page will load slowly.', 'lenfant-roi-child' ),
				'section'     => 'lr_hero',
				'mime_type'   => 'video',
			)
		)
	);

	$wp_customize->add_setting(
		'hero_video_poster',
		array(
			'default'           => '',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'hero_video_poster',
			array(
				'label'       => __( 'Video still frame', 'lenfant-roi-child' ),
				'description' => __( 'Shown while the video loads.', 'lenfant-roi-child' ),
				'section'     => 'lr_hero',
				'mime_type'   => 'image',
			)
		)
	);

	$text( 'hero_scroll_label', 'lr_hero', __( 'Scroll label', 'lenfant-roi-child' ) );
	$text( 'hero_footer_title', 'lr_hero', __( 'Line over the photo', 'lenfant-roi-child' ) );
	$text( 'hero_footer_subtitle', 'lr_hero', __( 'Second line over the photo', 'lenfant-roi-child' ) );

	/* --- visit tab -------------------------------------------------------- */
	$wp_customize->add_section(
		'lr_toolbar',
		array(
			'title' => __( 'Visit tab', 'lenfant-roi-child' ),
			'panel' => 'lr_home',
		)
	);

	$wp_customize->add_setting(
		'toolbar_show',
		array(
			'default'           => true,
			'sanitize_callback' => function ( $value ) {
				return (bool) $value;
			},
		)
	);
	$wp_customize->add_control(
		'toolbar_show',
		array(
			'label'   => __( 'Show the gold tab', 'lenfant-roi-child' ),
			'section' => 'lr_toolbar',
			'type'    => 'checkbox',
		)
	);
	$text( 'toolbar_label', 'lr_toolbar', __( 'Tab text', 'lenfant-roi-child' ) );
	$text( 'toolbar_url', 'lr_toolbar', __( 'Tab link', 'lenfant-roi-child' ), 'url' );

	/* --- palette ---------------------------------------------------------- */
	$wp_customize->add_section(
		'lr_palette',
		array(
			'title'       => __( 'Colours', 'lenfant-roi-child' ),
			'description' => __( 'These feed every part of the layout at once.', 'lenfant-roi-child' ),
			'panel'       => 'lr_home',
		)
	);

	foreach ( array(
		'color_primary'   => __( 'Gold', 'lenfant-roi-child' ),
		'color_secondary' => __( 'Navy', 'lenfant-roi-child' ),
		'color_tertiary'  => __( 'Body text', 'lenfant-roi-child' ),
	) as $id => $label ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $defaults[ $id ],
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$id,
				array(
					'label'   => $label,
					'section' => 'lr_palette',
				)
			)
		);
	}
}
add_action( 'customize_register', 'lr_customize_register' );

/**
 * Emit the three palette settings as CSS custom properties.
 *
 * Only the values that differ from the stylesheet defaults are printed, so an
 * untouched site ships no extra CSS at all.
 */
function lr_palette_css() {
	$defaults = lr_defaults();
	$rules    = array();

	$map = array(
		'color_primary'   => '--color-primary',
		'color_secondary' => '--color-secondary',
		'color_tertiary'  => '--color-tertiary',
	);

	foreach ( $map as $mod => $prop ) {
		$value = lr_opt( $mod );
		if ( $value && $value !== $defaults[ $mod ] ) {
			$rules[] = $prop . ':' . $value;
		}
	}

	if ( ! $rules ) {
		return;
	}

	wp_add_inline_style( 'lr-main', ':root{' . implode( ';', $rules ) . '}' );
}
// Priority 30, not 20: this file is required before functions.php registers
// lr_enqueue, so at 20 it would run first and have no lr-main handle to attach to.
add_action( 'wp_enqueue_scripts', 'lr_palette_css', 30 );

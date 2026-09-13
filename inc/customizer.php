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

		// About.
		'about_image'          => '',
		'about_text_1'         => 'Every child has the <strong>inner potential</strong> for remarkable growth and development. By fostering their <strong>personal awakening</strong> in a <strong>safe and nurturing environment</strong> that respects their individual freedoms, we prepare children to become well-rounded and responsible adults. The educator is a guide, supporting the child&rsquo;s journey towards self-discovery and autonomous growth.',
		'about_text_2'         => 'L&rsquo;Enfant Roi provides a <strong>stimulating atmosphere</strong> for your child to explore their unique sensibilities, tailored to their individual characteristics and both their psychological and physical needs.',

		// Quote (brief p4). Wording is the client's own, kept verbatim.
		'quote_text'           => '&laquo;&nbsp;L&rsquo;enfant n&rsquo;est pas un vase que l&rsquo;on remplit, mais une source que l&rsquo;on laisse jaillir.&nbsp;&raquo;',
		'quote_author'         => 'Maria Montessori',

		// Hours (brief p7).
		'hours_label'          => 'Horaires',
		'hours_heading'        => 'Du lundi au vendredi',
		'hours_value'          => '7:00-19:00',
		'hours_copy'           => 'Ouverte de 7h &agrave; 19h, SmileyWorld s&rsquo;adapte aux besoins de chaque famille et aux diff&eacute;rents rythmes de travail. Des horaires pens&eacute;s pour vous offrir plus de flexibilit&eacute; au quotidien, avec la tranquillit&eacute; de pouvoir toujours compter sur nous.',

		// Closures (brief p8).
		'holidays_label'       => 'Vacances',
		'holidays_heading'     => 'Seulement 3 semaines de fermeture par an',
		'holidays_copy'        => 'La cr&egrave;che ferme les deux premi&egrave;res semaines du mois d&rsquo;ao&ucirc;t, ainsi qu&rsquo;une semaine entre No&euml;l et le Nouvel An, non factur&eacute;es.

Notre calendrier est pens&eacute; pour s&rsquo;adapter au mieux au rythme et au quotidien de chaque famille.',

		// Flexibility (brief p9).
		'flex_label'           => 'Flexibilit&eacute;',
		'flex_heading'         => 'Une arriv&eacute;e et un d&eacute;part serein',
		'flex_copy'            => 'Nous n&rsquo;imposons aucun horaire fixe d&rsquo;arriv&eacute;e ou de d&eacute;part&nbsp;: vous organisez les journ&eacute;es de votre enfant en toute libert&eacute;.

Gr&acirc;ce &agrave; nos formules flexibles &agrave; la journ&eacute;e ou &agrave; la demi-journ&eacute;e.',

		// Menu panel buttons (brief: the original has Jobs / FAQ / Contact).
		'menu_btn_1_label'     => 'Jobs',
		'menu_btn_1_url'       => '',
		'menu_btn_2_label'     => 'FAQ',
		'menu_btn_2_url'       => '',
		'menu_btn_3_label'     => 'Contact',
		'menu_btn_3_url'       => '',

		'toolbar_show'         => true,
		'toolbar_label'        => 'Schedule a visit',
		'toolbar_url'          => '#',

		// Palette.
		// Must stay in step with main.css :root - lr_palette_css() prints nothing
		// when a setting equals its default, so a mismatch here would quietly
		// offer the old Lenfant-Roi gold as "Default" in the colour picker.
		'color_primary'        => '#c39a5b',
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

	/* --- menu panel buttons ----------------------------------------------- */
	$wp_customize->add_section(
		'lr_menu_buttons',
		array(
			'title'       => __( 'Menu buttons', 'lenfant-roi-child' ),
			'description' => __( 'The three pill buttons under the links in the slide-out menu. Clear a label to remove that button.', 'lenfant-roi-child' ),
			'panel'       => 'lr_home',
		)
	);
	foreach ( array(
		1 => __( 'First button (dark)', 'lenfant-roi-child' ),
		2 => __( 'Second button (outlined)', 'lenfant-roi-child' ),
		3 => __( 'Third button (gold)', 'lenfant-roi-child' ),
	) as $lr_n => $lr_title ) {
		$text( 'menu_btn_' . $lr_n . '_label', 'lr_menu_buttons', $lr_title );
		$text( 'menu_btn_' . $lr_n . '_url', 'lr_menu_buttons', __( '...its link', 'lenfant-roi-child' ), 'url' );
	}

	/* --- about ------------------------------------------------------------ */
	$wp_customize->add_section(
		'lr_about',
		array(
			'title'       => __( 'About section', 'lenfant-roi-child' ),
			'description' => __( 'The lettering and the two paragraphs under the hero.', 'lenfant-roi-child' ),
			'panel'       => 'lr_home',
		)
	);

	$wp_customize->add_setting(
		'about_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'about_image',
			array(
				'label'       => __( 'Lettering on the left', 'lenfant-roi-child' ),
				'description' => __( 'Usually the logo. Leave empty to keep the placeholder lettering.', 'lenfant-roi-child' ),
				'section'     => 'lr_about',
				'mime_type'   => 'image',
			)
		)
	);

	$text(
		'about_text_1',
		'lr_about',
		__( 'First paragraph', 'lenfant-roi-child' ),
		'textarea',
		// Escaped on purpose - the description is printed as HTML, so real tags
		// here would render as bold text instead of showing him what to type.
		__( 'Wrap words in &lt;strong&gt; ... &lt;/strong&gt; to make them bold.', 'lenfant-roi-child' )
	);
	$text( 'about_text_2', 'lr_about', __( 'Second paragraph', 'lenfant-roi-child' ), 'textarea' );

	/* --- quote (brief p4) ------------------------------------------------- */
	$wp_customize->add_section(
		'lr_quote',
		array(
			'title'       => __( 'Quote', 'lenfant-roi-child' ),
			'description' => __( 'The teal band with the Montessori quotation. Clear the quote to remove the whole section.', 'lenfant-roi-child' ),
			'panel'       => 'lr_home',
		)
	);
	$text( 'quote_text', 'lr_quote', __( 'Quotation', 'lenfant-roi-child' ), 'textarea' );
	$text( 'quote_author', 'lr_quote', __( 'Attributed to', 'lenfant-roi-child' ) );

	/* --- the three bands (brief p7, p8, p9) ------------------------------- */
	$bands = array(
		'lr_hours'    => array(
			'title'  => __( 'Opening hours', 'lenfant-roi-child' ),
			'prefix' => 'hours',
			'value'  => __( 'Large line (the times)', 'lenfant-roi-child' ),
		),
		'lr_holidays' => array(
			'title'  => __( 'Closures', 'lenfant-roi-child' ),
			'prefix' => 'holidays',
		),
		'lr_flex'     => array(
			'title'  => __( 'Flexibility', 'lenfant-roi-child' ),
			'prefix' => 'flex',
		),
	);

	foreach ( $bands as $section_id => $band ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title'       => $band['title'],
				'description' => __( 'Clearing both the heading and the text removes this section from the page.', 'lenfant-roi-child' ),
				'panel'       => 'lr_home',
			)
		);

		$text( $band['prefix'] . '_label', $section_id, __( 'Small gold label', 'lenfant-roi-child' ) );
		$text( $band['prefix'] . '_heading', $section_id, __( 'Heading', 'lenfant-roi-child' ), 'textarea' );

		if ( isset( $band['value'] ) ) {
			$text( $band['prefix'] . '_value', $section_id, $band['value'] );
		}

		$text(
			$band['prefix'] . '_copy',
			$section_id,
			__( 'Text below the band', 'lenfant-roi-child' ),
			'textarea',
			__( 'Leave a blank line between paragraphs.', 'lenfant-roi-child' )
		);
	}

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

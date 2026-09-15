<?php
/**
 * L'Enfant Roi Layout - child theme of Hello Elementor.
 *
 * @package lenfant-roi-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LR_VERSION', '1.10.0' );

require_once get_stylesheet_directory() . '/inc/svg.php';
require_once get_stylesheet_directory() . '/inc/customizer.php';
require_once get_stylesheet_directory() . '/inc/sections.php';

/**
 * Elementor widgets, only when Elementor is actually running.
 *
 * Two ordering traps here, both hit while building this:
 *
 * 1. 'elementor/loaded' fires during plugins_loaded, which is BEFORE a theme's
 *    functions.php runs. Hooking it here would register a callback for an event
 *    that has already happened, so did_action() is the check to use.
 * 2. Even then, \Elementor\Widget_Base is autoloaded lazily and does NOT exist
 *    yet at this point - requiring the classes here fatals. They are pulled in
 *    inside the register callback, by which time the base class is loaded.
 */
if ( did_action( 'elementor/loaded' ) ) {
	add_action( 'elementor/elements/categories_registered', 'lr_elementor_category' );
	add_action( 'elementor/widgets/register', 'lr_elementor_boot' );
}

/**
 * Give the sections their own panel category instead of burying them in General.
 *
 * Deliberately kept out of inc/elementor.php: it touches no Elementor class, so
 * it can run without the file being loaded.
 *
 * @param \Elementor\Elements_Manager $manager Elements manager.
 */
function lr_elementor_category( $manager ) {
	$manager->add_category(
		'smileyworld',
		array(
			'title' => __( 'SmileyWorld', 'lenfant-roi-child' ),
			'icon'  => 'eicon-favorite',
		)
	);
}

/**
 * Load the widget classes and register them.
 *
 * @param \Elementor\Widgets_Manager $manager Widgets manager.
 */
function lr_elementor_boot( $manager ) {
	require_once get_stylesheet_directory() . '/inc/elementor.php';
	lr_elementor_widgets( $manager );
}

/**
 * Theme supports and menu locations.
 */
function lr_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );

	register_nav_menus(
		array(
			'lr_primary' => __( 'Main menu (opens behind the round button)', 'lenfant-roi-child' ),
		)
	);
}
add_action( 'after_setup_theme', 'lr_setup' );

/**
 * Styles and scripts.
 *
 * Hello Elementor's own stylesheet is left in place - it is a light reset and
 * the layout is built on top of it.
 */
function lr_enqueue() {
	// Fonts. Poppins is the original's own face; the other two are free stand-ins
	// for Caslon CP and Din Medium, which are licensed and cannot be copied.
	wp_enqueue_style(
		'lr-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Caslon+Display&family=Archivo:wght@400;500&family=Playfair+Display:wght@400;500;600;700;800;900&display=swap',
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google serves its own cache key.
	);

	wp_enqueue_style(
		'lr-main',
		get_stylesheet_directory_uri() . '/assets/css/main.css',
		array( 'lr-fonts' ),
		LR_VERSION
	);

	// The client's own stylesheet. Loaded last so it wins, and deploy.py never
	// overwrites it - see the header comment in the file itself.
	//
	// Versioned by its own mtime rather than LR_VERSION: he edits this file, and
	// a theme-wide constant would leave his change sitting behind a stale cache
	// until I happened to ship a release.
	$custom = get_stylesheet_directory() . '/assets/css/custom.css';
	if ( file_exists( $custom ) ) {
		wp_enqueue_style(
			'lr-custom',
			get_stylesheet_directory_uri() . '/assets/css/custom.css',
			array( 'lr-main' ),
			(string) filemtime( $custom )
		);
	}

	wp_enqueue_script(
		'lr-main',
		get_stylesheet_directory_uri() . '/assets/js/main.js',
		array(),
		LR_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'lr_enqueue', 20 );

/**
 * Preconnect to the font hosts so the first paint is not held up by DNS.
 *
 * @param array  $urls           Resource hints.
 * @param string $relation_type  Hint type.
 * @return array
 */
function lr_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'lr_resource_hints', 10, 2 );

/**
 * Preload the hero photo.
 *
 * The hero image is the largest thing above the fold; without this the browser
 * only discovers it after the stylesheet has parsed.
 */
function lr_preload_hero() {
	if ( ! is_front_page() || 'image' !== lr_opt( 'hero_media_type' ) ) {
		return;
	}

	$src = lr_hero_image_src();
	if ( ! $src ) {
		return;
	}

	printf(
		'<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n",
		esc_url( $src )
	);
}
add_action( 'wp_head', 'lr_preload_hero', 2 );

<?php
/**
 * L'Enfant Roi Layout - child theme of Hello Elementor.
 *
 * @package lenfant-roi-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LR_VERSION', '1.1.0' );

require_once get_stylesheet_directory() . '/inc/svg.php';
require_once get_stylesheet_directory() . '/inc/customizer.php';
require_once get_stylesheet_directory() . '/inc/sections.php';

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
		'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Caslon+Display&family=Archivo:wght@400;500&display=swap',
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google serves its own cache key.
	);

	wp_enqueue_style(
		'lr-main',
		get_stylesheet_directory_uri() . '/assets/css/main.css',
		array( 'lr-fonts' ),
		LR_VERSION
	);

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

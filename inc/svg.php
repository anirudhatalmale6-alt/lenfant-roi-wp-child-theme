<?php
/**
 * Inline SVG helper.
 *
 * The brand marks are inlined rather than used as <img> so their strokes and
 * fills can pick up the theme colours through currentColor / var(--color-*).
 * That is how the original site does it, and it is why the gold in the hero
 * lettering changes when you change the palette.
 *
 * @package lenfant-roi-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read one of the theme's SVG files and return its markup.
 *
 * @param string $name File name without the extension, e.g. 'logo'.
 * @return string Raw SVG markup, or an empty string if the file is missing.
 */
function lr_svg( $name ) {
	static $cache = array();

	$name = preg_replace( '/[^a-z0-9\-]/', '', strtolower( $name ) );

	if ( isset( $cache[ $name ] ) ) {
		return $cache[ $name ];
	}

	$file = get_stylesheet_directory() . '/assets/svg/' . $name . '.svg';

	// A missing file must not take the page down - render nothing instead.
	$cache[ $name ] = file_exists( $file ) ? file_get_contents( $file ) : '';

	return $cache[ $name ];
}

/**
 * Echo an inline SVG.
 *
 * @param string $name File name without the extension.
 */
function lr_the_svg( $name ) {
	echo lr_svg( $name ); // phpcs:ignore WordPress.Security.EscapingOutput -- trusted theme files.
}

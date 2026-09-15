<?php
/**
 * Homepage.
 *
 * If the page set as the front page has its own content (an Elementor layout,
 * for instance) that content wins and the theme stays out of the way. Only an
 * empty front page gets the built-in hero.
 *
 * @package lenfant-roi-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$lr_front_id      = (int) get_option( 'page_on_front' );
$lr_has_own_build = $lr_front_id
	&& ( trim( (string) get_post_field( 'post_content', $lr_front_id ) ) !== ''
		|| get_post_meta( $lr_front_id, '_elementor_edit_mode', true ) );

if ( $lr_has_own_build ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	}
} else {
	// Brief order: hero, about, quote, then the three bands.
	echo lr_section_hero();     // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_about();    // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_quote();    // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_hours();    // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_holidays(); // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_flex();     // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_access();   // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_tarifs();   // phpcs:ignore WordPress.Security.EscapingOutput
}

get_footer();

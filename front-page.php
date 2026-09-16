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
	echo lr_section_reviews();  // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_access();   // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_family();   // phpcs:ignore WordPress.Security.EscapingOutput
	// Pages 11-14: the panels the icons above open. Each carries a panel id.
	echo lr_section_band( array( 'panel' => 'bilinguisme', 'side' => 'left', 'label' => 'Bilinguisme', 'icon' => 'icon-globe-circle.png', 'heading' => 'Grandir en deux langues, c&rsquo;est s&rsquo;ouvrir au monde d&egrave;s le plus jeune &acirc;ge.', 'copy' => 'Au quotidien, les enfants sont immerg&eacute;s en <strong>fran&ccedil;ais</strong> et en <strong>anglais</strong>, &agrave; travers les jeux, les activit&eacute;s et les &eacute;changes avec notre &eacute;quipe anglophone. Une immersion naturelle, douce et adapt&eacute;e au rythme de chaque enfant.' ) ); // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_band( array( 'panel' => 'tout-inclus', 'side' => 'left', 'label' => 'Tout inclus', 'icon' => 'icon-tick-circle.png', 'heading' => 'Profitez d&rsquo;un quotidien plus simple et serein gr&acirc;ce &agrave; notre formule tout inclus.', 'copy' => '<strong>Les couches, les soins</strong> ainsi que tous <strong>les repas</strong> et <strong>pur&eacute;es</strong> maison sont fournis, des b&eacute;b&eacute;s aux plus grands. Tout est pens&eacute; pour vous faciliter le quotidien, sans contrainte logistique.' ) ); // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_band( array( 'panel' => 'app-famille', 'side' => 'left', 'label' => 'App Famille', 'icon' => 'icon-badge.png', 'heading' => 'Restez connect&eacute;s au quotidien de votre enfant, o&ugrave; que vous soyez.', 'copy' => 'Gr&acirc;ce &agrave; notre application, retrouvez en un coup d&rsquo;&oelig;il <strong>ses repas, ses siestes, ses changes et ses activit&eacute;s</strong>. &Eacute;changez facilement avec notre &eacute;quipe, d&eacute;couvrez des photos et vid&eacute;os de sa journ&eacute;e, et g&eacute;rez simplement vos <strong>factures</strong> et votre planning.' ) ); // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_band( array( 'panel' => 'alimentation', 'side' => 'left', 'label' => 'Alimentation adapt&eacute;e', 'icon' => 'icon-chef-circle.png', 'heading' => 'Des repas pens&eacute;s pour &eacute;veiller les papilles et bien grandir.', 'copy' => 'Du <strong>d&eacute;jeuner au go&ucirc;ter</strong>, en passant par les collations et les <strong>pur&eacute;es</strong> des plus petits, tout est fourni &agrave; la cr&egrave;che. Nous travaillons avec un traiteur certifi&eacute; &laquo;&nbsp;<strong>Fait Maison</strong>&nbsp;&raquo; et labellis&eacute; <strong>Ecocook</strong>, qui privil&eacute;gie des fruits, l&eacute;gumes et produits frais de qualit&eacute;.' ) ); // phpcs:ignore WordPress.Security.EscapingOutput
	echo lr_section_tarifs();   // phpcs:ignore WordPress.Security.EscapingOutput
}

get_footer();

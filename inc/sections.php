<?php
/**
 * Homepage sections.
 *
 * Each section is a function so it can be rendered from front-page.php or from
 * a shortcode - the shortcode is what lets these blocks be dropped into an
 * Elementor page later without the markup being duplicated.
 *
 * @package lenfant-roi-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The gold guide lines that make up the arabesque motif.
 *
 * Each line is a plain 1px div the full height of the block, rotated by CSS.
 * Markup rather than an SVG is what lets every one of them draw itself along
 * its own length.
 *
 * Not every block carries the same lines. The hero uses both pairs of
 * diagonals; the blocks below it drop the upper pair and add a centre
 * vertical instead. Both sets are read off the original.
 *
 * @param string $variant 'hero' or 'section'.
 * @return string
 */
function lr_arabesque( $variant = 'hero' ) {
	$keys = 'hero' === $variant
		? array( 'l1', 'l2', 'l3', 'l4', 'l5', 'r1', 'r2', 'r3', 'r4', 'r5' )
		: array( 'm1', 'l1', 'l2', 'l3', 'l5', 'r1', 'r2', 'r3', 'r5' );

	$out = '<div class="section__arabesque arabesque" aria-hidden="true">';
	foreach ( $keys as $key ) {
		$out .= '<div class="arabesque__line arabesque__line-' . $key . '"></div>';
	}
	$out .= '</div>';

	return $out;
}

/**
 * Resolve the hero photo URL.
 *
 * Falls back to the placeholder shipped with the theme so a fresh install still
 * renders the design instead of an empty navy block.
 *
 * @return string
 */
function lr_hero_image_src() {
	$id = (int) lr_opt( 'hero_image' );

	if ( $id ) {
		$src = wp_get_attachment_image_url( $id, 'full' );
		if ( $src ) {
			return $src;
		}
	}

	return get_stylesheet_directory_uri() . '/assets/img/hero-placeholder.jpg';
}

/**
 * The hero media element - photo or looping video.
 *
 * @return string
 */
function lr_hero_media() {
	if ( 'video' === lr_opt( 'hero_media_type' ) ) {
		$video_id = (int) lr_opt( 'hero_video' );
		$src      = $video_id ? wp_get_attachment_url( $video_id ) : '';

		if ( $src ) {
			$poster_id = (int) lr_opt( 'hero_video_poster' );
			$poster    = $poster_id ? wp_get_attachment_image_url( $poster_id, 'full' ) : '';

			return sprintf(
				'<video src="%s"%s autoplay muted loop playsinline preload="metadata"></video>',
				esc_url( $src ),
				$poster ? ' poster="' . esc_url( $poster ) . '"' : ''
			);
		}
		// No video picked yet - fall through to the photo rather than render a hole.
	}

	$id = (int) lr_opt( 'hero_image' );

	if ( $id ) {
		return wp_get_attachment_image(
			$id,
			'full',
			false,
			array(
				'alt'            => '',
				'fetchpriority'  => 'high',
				'decoding'       => 'async',
			)
		);
	}

	return sprintf(
		'<img src="%s" alt="" width="2304" height="636" fetchpriority="high" decoding="async">',
		esc_url( get_stylesheet_directory_uri() . '/assets/img/hero-placeholder.jpg' )
	);
}

/**
 * The hero title block - lettering plus the tracked subtitle beneath it.
 *
 * @return string
 */
function lr_hero_title() {
	$id = (int) lr_opt( 'hero_title_image' );

	if ( $id ) {
		$lettering = wp_get_attachment_image(
			$id,
			'full',
			false,
			array(
				'class' => 'section__title__image',
				'alt'   => get_bloginfo( 'name' ),
			)
		);
	} else {
		$lettering = lr_svg( 'hero-title' );
	}

	$subtitle = lr_opt( 'hero_subtitle' );

	return '<h1 class="section__title" data-animation="reveal-text">'
		. $lettering
		. ( $subtitle ? '<span class="section__subtitle">' . wp_kses_post( $subtitle ) . '</span>' : '' )
		. '</h1>';
}

/**
 * Render the hero section.
 *
 * @return string
 */
function lr_section_hero() {
	ob_start();
	?>
	<header class="section-hero-page-home" id="hero-home">
		<div class="section__wrapper">

			<?php echo lr_arabesque(); // phpcs:ignore WordPress.Security.EscapingOutput ?>

			<div class="section__header">
				<?php echo lr_hero_title(); // phpcs:ignore WordPress.Security.EscapingOutput ?>

				<i class="section__mark" data-animation="reveal" data-delay="200">
					<?php lr_the_svg( 'crown' ); ?>
				</i>

				<a class="section__scrollto" href="#more" data-animation="reveal" data-delay="400">
					<i class="icon-primary"><?php lr_the_svg( 'scroll-arrow' ); ?></i>
					<span><?php echo esc_html( lr_opt( 'hero_scroll_label' ) ); ?></span>
				</a>
			</div>

			<div class="section__main" data-animation="reveal" data-delay="300">
				<div class="section__media">
					<?php echo lr_hero_media(); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				</div>

				<div class="section__footer" id="more">
					<p class="section__footer__title"><?php echo wp_kses_post( lr_opt( 'hero_footer_title' ) ); ?></p>
					<p class="section__footer__subtitle"><?php echo wp_kses_post( lr_opt( 'hero_footer_subtitle' ) ); ?></p>
					<i class="section__icon"><?php lr_the_svg( 'divider' ); ?></i>
				</div>
			</div>

		</div>
	</header>
	<?php
	return ob_get_clean();
}
add_shortcode( 'lr_hero', 'lr_section_hero' );

/**
 * The lettering at the left of the about section.
 *
 * @return string
 */
function lr_about_lettering() {
	$id = (int) lr_opt( 'about_image' );

	if ( $id ) {
		return wp_get_attachment_image(
			$id,
			'full',
			false,
			array(
				'class' => 'section__title__image',
				'alt'   => get_bloginfo( 'name' ),
			)
		);
	}

	return lr_svg( 'wordmark' );
}

/**
 * Render the about section.
 *
 * The lettering and both paragraphs come from the Customizer, so the demo
 * wording is only ever a default - editing it never touches a file.
 *
 * @return string
 */
function lr_section_about() {
	$paragraphs = array(
		array( lr_opt( 'about_text_1' ), 150 ),
		array( lr_opt( 'about_text_2' ), 250 ),
	);

	ob_start();
	?>
	<section class="section-custom-about" id="about">
		<?php echo lr_arabesque( 'section' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		<div class="section__wrapper container">

			<div class="section__header">
				<h2 class="section__title" data-animation="reveal" data-delay="100">
					<?php echo lr_about_lettering(); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				</h2>
			</div>

			<div class="section__main">
				<?php foreach ( $paragraphs as $paragraph ) : ?>
					<?php
					list( $copy, $delay ) = $paragraph;
					if ( '' === trim( wp_strip_all_tags( (string) $copy ) ) ) {
						// An emptied field should remove the paragraph, not leave a gap.
						continue;
					}
					?>
					<p data-animation="reveal" data-delay="<?php echo esc_attr( $delay ); ?>">
						<?php echo wp_kses_post( $copy ); ?>
					</p>
				<?php endforeach; ?>
			</div>

		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'lr_about', 'lr_section_about' );

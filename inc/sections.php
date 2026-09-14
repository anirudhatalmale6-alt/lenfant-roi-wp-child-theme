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
 * The gold stadium outline that draws itself as the block scrolls in.
 *
 * Copied from the original's CSR block rather than approximated: one <rect>
 * 1500x500 at (1,1) with rx/ry 250, stroked in currentColor at 1px, inside a
 * viewBox of 1501x501. The extra pixel is what stops a 1px stroke being clipped
 * in half by the viewBox edge.
 *
 * preserveAspectRatio="none" lets the stadium stretch to whatever the block's
 * proportions are - the original's is 3:1, ours are not.
 *
 * @return string
 */
function lr_outline() {
	return '<div class="outline" aria-hidden="true">'
		. '<svg class="outline__svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1501 501"'
		. ' preserveAspectRatio="none" focusable="false">'
		. '<rect class="outline__shape" fill="none" stroke="currentColor"'
		. ' x="1" y="1" width="1500" height="500" rx="250" ry="250"></rect>'
		. '</svg></div>';
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
function lr_hero_title( $subtitle = null ) {
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

	if ( null === $subtitle ) {
		$subtitle = lr_opt( 'hero_subtitle' );
	}

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
function lr_section_hero( $args = array() ) {
	$a = wp_parse_args(
		$args,
		array(
			'subtitle'        => lr_opt( 'hero_subtitle' ),
			'footer_title'    => lr_opt( 'hero_footer_title' ),
			'footer_subtitle' => lr_opt( 'hero_footer_subtitle' ),
		)
	);

	ob_start();
	?>
	<header class="section-hero-page-home" id="hero-home">
		<div class="section__wrapper">

			<?php echo lr_arabesque(); // phpcs:ignore WordPress.Security.EscapingOutput ?>

			<div class="section__header">
				<?php echo lr_hero_title( $a['subtitle'] ); // phpcs:ignore WordPress.Security.EscapingOutput ?>

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
					<p class="section__footer__title"><?php echo wp_kses_post( $a['footer_title'] ); ?></p>
					<p class="section__footer__subtitle"><?php echo wp_kses_post( $a['footer_subtitle'] ); ?></p>
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
function lr_about_lettering( $url = '' ) {
	if ( $url ) {
		return sprintf(
			'<img class="section__title__image" src="%s" alt="%s">',
			esc_url( $url ),
			esc_attr( get_bloginfo( 'name' ) )
		);
	}

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
 * Strip one redundant wrapping <p> from a value.
 *
 * Elementor's WYSIWYG control hands back "<p>text</p>", and the About section
 * puts its own <p data-animation> around each paragraph. Nested <p> is invalid,
 * so the browser closes the outer one early and the entrance animation ends up
 * attached to an empty element while the text sits outside it. Only unwraps
 * when the value is a SINGLE paragraph - multi-paragraph HTML is left alone.
 *
 * @param string $html Raw value.
 * @return string
 */
function lr_unwrap_p( $html ) {
	$trimmed = trim( (string) $html );

	if ( 0 !== stripos( $trimmed, '<p>' ) || substr( strtolower( $trimmed ), -4 ) !== '</p>' ) {
		return $html;
	}
	$inner = substr( $trimmed, 3, -4 );

	// More than one paragraph in there - leave it be.
	if ( false !== stripos( $inner, '<p' ) ) {
		return $html;
	}

	return $inner;
}

/**
 * Render the about section.
 *
 * The lettering and both paragraphs come from the Customizer, so the demo
 * wording is only ever a default - editing it never touches a file.
 *
 * @return string
 */
function lr_section_about( $args = array() ) {
	$a = wp_parse_args(
		$args,
		array(
			'image_url' => '',
			'text_1'    => lr_opt( 'about_text_1' ),
			'text_2'    => lr_opt( 'about_text_2' ),
		)
	);

	$paragraphs = array(
		array( lr_unwrap_p( $a['text_1'] ), 150 ),
		array( lr_unwrap_p( $a['text_2'] ), 250 ),
	);

	ob_start();
	?>
	<section class="section-custom-about" id="about">
		<?php echo lr_arabesque( 'section' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		<div class="section__wrapper container">

			<div class="section__header">
				<h2 class="section__title" data-animation="reveal" data-delay="100">
					<?php echo lr_about_lettering( $a['image_url'] ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
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

/**
 * Brief p4 - the Montessori quote, full-bleed teal.
 *
 * @return string
 */
function lr_section_quote( $args = array() ) {
	$a = wp_parse_args(
		$args,
		array(
			'quote'  => lr_opt( 'quote_text' ),
			'author' => lr_opt( 'quote_author' ),
		)
	);

	$quote  = $a['quote'];
	$author = $a['author'];

	if ( '' === trim( wp_strip_all_tags( (string) $quote ) ) ) {
		return '';
	}

	ob_start();
	?>
	<section class="section-quote" id="citation">
		<?php echo lr_arabesque( 'section' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		<div class="section__wrapper container">
			<blockquote class="section-quote__text" data-animation="reveal" data-delay="100">
				<?php echo wp_kses_post( $quote ); ?>
			</blockquote>
			<?php if ( '' !== trim( wp_strip_all_tags( (string) $author ) ) ) : ?>
				<p class="section-quote__author" data-animation="reveal" data-delay="250">
					<?php echo wp_kses_post( $author ); ?>
				</p>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * The repeating teal band - brief pages 7, 8, 9, and later 11-14.
 *
 * Every one of those slides is this same shape: a teal panel bleeding off one
 * edge and rounded on the other, a small gold eyebrow, an outline icon, a white
 * heading, then body copy under the band closed off by a gold rule. Only the
 * side and the wording change, so it is built once and called with arguments.
 *
 * @param array $args {
 *     @type string $id      Section id / anchor.
 *     @type string $side    'left' (bleeds left, rounded right) or 'right'.
 *     @type string $label   Small gold eyebrow.
 *     @type string $icon    Filename in assets/img, or '' for none.
 *     @type string $heading White heading inside the band.
 *     @type string $value   Optional larger line under the heading.
 *     @type string $copy    Body copy below the band.
 * }
 * @return string
 */
function lr_section_band( $args ) {
	$a = wp_parse_args(
		$args,
		array(
			'id'      => '',
			'side'    => 'left',
			'label'   => '',
			'icon'     => '',
			'icon_url' => '',
			'heading' => '',
			'value'   => '',
			'copy'    => '',
		)
	);

	// A band with neither heading nor copy is an empty teal slab - drop it.
	if ( '' === trim( wp_strip_all_tags( $a['heading'] . $a['copy'] ) ) ) {
		return '';
	}

	$side = ( 'right' === $a['side'] ) ? 'right' : 'left';

	ob_start();
	?>
	<section class="section-band section-band--<?php echo esc_attr( $side ); ?>"
		<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>

		<?php echo lr_arabesque( 'section' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>

		<?php
		// The outline bleeds off one edge, so only half the stadium is on screen -
		// that is the "half oval" on the original. Everything else sits inside it.
		?>
		<div class="section-band__stage" data-draw>
			<?php echo lr_outline(); // phpcs:ignore WordPress.Security.EscapingOutput ?>

			<div class="section-band__inner container">

				<div class="section-band__head">
					<div class="section-band__aside">
						<?php if ( '' !== $a['label'] ) : ?>
							<p class="section-band__label" data-animation="reveal" data-delay="100"><?php echo wp_kses_post( $a['label'] ); ?></p>
						<?php endif; ?>
						<?php if ( '' !== $a['icon'] ) : ?>
							<img class="section-band__icon" alt="" data-animation="reveal" data-delay="150"
								src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/' . $a['icon'] ); ?>">
						<?php endif; ?>
					</div>

					<div class="section-band__main">
						<?php if ( '' !== $a['heading'] ) : ?>
							<h2 class="section-band__heading" data-animation="reveal" data-delay="150"><?php echo wp_kses_post( $a['heading'] ); ?></h2>
						<?php endif; ?>
						<?php if ( '' !== $a['value'] ) : ?>
							<p class="section-band__value" data-animation="reveal" data-delay="200"><?php echo wp_kses_post( $a['value'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( '' !== trim( wp_strip_all_tags( $a['copy'] ) ) ) : ?>
					<div class="section-band__copy" data-animation="reveal" data-delay="250">
						<?php echo wpautop( wp_kses_post( $a['copy'] ) ); ?>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Brief p7 - opening hours.
 *
 * @return string
 */
function lr_section_hours() {
	return lr_section_band(
		array(
			'id'      => 'horaires',
			'side'    => 'left',
			'label'   => lr_opt( 'hours_label' ),
			'icon'    => 'icon-clock.png',
			'heading' => lr_opt( 'hours_heading' ),
			'value'   => lr_opt( 'hours_value' ),
			'copy'    => lr_opt( 'hours_copy' ),
		)
	);
}

/**
 * Brief p8 - closures. The only one that bleeds off the right edge.
 *
 * @return string
 */
function lr_section_holidays() {
	return lr_section_band(
		array(
			'id'      => 'vacances',
			'side'    => 'right',
			'label'   => lr_opt( 'holidays_label' ),
			'icon'    => 'icon-calendar.png',
			'heading' => lr_opt( 'holidays_heading' ),
			'copy'    => lr_opt( 'holidays_copy' ),
		)
	);
}

/**
 * Brief p9 - flexible drop-off and pick-up.
 *
 * @return string
 */
function lr_section_flex() {
	return lr_section_band(
		array(
			'id'      => 'flexibilite',
			'side'    => 'left',
			'label'   => lr_opt( 'flex_label' ),
			'icon'    => 'icon-handshake.png',
			'heading' => lr_opt( 'flex_heading' ),
			'copy'    => lr_opt( 'flex_copy' ),
		)
	);
}
add_shortcode( 'lr_about', 'lr_section_about' );

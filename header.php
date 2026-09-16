<?php
/**
 * Site header.
 *
 * Replaces Hello Elementor's header entirely: fixed bar, logo, language pair,
 * round menu button, and the full-screen menu it opens. The bar tucks itself
 * away while you scroll down and comes back on the way up - that behaviour
 * lives in assets/js/main.js.
 *
 * @package lenfant-roi-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lr_logo_id = (int) get_theme_mod( 'logo_svg_replace', 0 );
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
// Brief p1: the logo sits centred over the page and slides aside after two
// seconds. Rendered before everything so it covers the page from first paint -
// injecting it later would let the page flash first, which is the opposite of
// the effect. hidden by default so a visitor without JS never sees it at all.
if ( lr_opt( 'splash_show' ) ) :
	$lr_splash_logo = (int) get_theme_mod( 'logo_svg_replace', 0 );
	?>
	<div class="splash" id="lr-splash" data-hold="<?php echo esc_attr( (int) lr_opt( 'splash_seconds' ) ); ?>"<?php echo lr_opt( 'splash_once' ) ? ' data-once' : ''; ?> hidden>
		<div class="splash__logo">
			<?php
			if ( $lr_splash_logo ) {
				echo wp_get_attachment_image( $lr_splash_logo, 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) );
			} else {
				lr_the_svg( 'logo' );
			}
			?>
		</div>
	</div>
<?php endif; ?>

<a class="skip-link visually-hidden" href="#content"><?php esc_html_e( 'Skip to content', 'lenfant-roi-child' ); ?></a>

<header class="page__header">
	<a class="header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
		<?php
		if ( $lr_logo_id ) {
			echo wp_get_attachment_image( $lr_logo_id, 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) );
		} else {
			lr_the_svg( 'logo' );
		}
		?>
	</a>

	<nav class="header__nav">
		<?php
		$lr_langs = array();
		foreach ( array( 1, 2 ) as $lr_i ) {
			$lr_label = lr_opt( 'lang_' . $lr_i . '_label' );
			if ( $lr_label ) {
				$lr_langs[] = array( $lr_label, lr_opt( 'lang_' . $lr_i . '_url' ) );
			}
		}

		if ( $lr_langs ) :
			?>
			<div class="lang">
				<?php foreach ( $lr_langs as $lr_index => $lr_lang ) : ?>
					<a href="<?php echo esc_url( $lr_lang[1] ? $lr_lang[1] : '#' ); ?>"<?php echo 1 === $lr_index ? ' class="is-active"' : ''; ?>><?php echo esc_html( $lr_lang[0] ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<button class="burger" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'lenfant-roi-child' ); ?>" aria-expanded="false" aria-controls="lr-menu">
			<span></span><span></span><span></span>
		</button>
	</nav>
</header>

<?php
// The original is a white panel down the right-hand side with the rest of the
// page dimmed behind it - not a full-screen overlay. 600px of a 1280 viewport.
?>
<div class="menu-scrim" id="lr-menu-scrim" hidden></div>

<div class="menu-panel" id="lr-menu" hidden>
	<div class="menu-panel__inner">
		<?php
		if ( has_nav_menu( 'lr_primary' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'lr_primary',
					'container'      => 'nav',
					'menu_class'     => 'menu-panel__list',
					'depth'          => 2,
				)
			);
		} else {
			// Nothing assigned yet - say so on screen rather than open an empty panel.
			echo '<p class="menu-panel__empty">'
				. esc_html__( 'Assign a menu under Appearance > Menus to the "Main menu" location.', 'lenfant-roi-child' )
				. '</p>';
		}

		// The three pill buttons under the links. Each one disappears if its
		// label is cleared, rather than rendering an empty pill.
		$lr_buttons = array(
			array( 'menu_btn_1_label', 'menu_btn_1_url', 'is-dark' ),
			array( 'menu_btn_2_label', 'menu_btn_2_url', 'is-outline' ),
			array( 'menu_btn_3_label', 'menu_btn_3_url', 'is-gold' ),
		);
		$lr_rendered = array();
		foreach ( $lr_buttons as $lr_btn ) {
			$lr_label = lr_opt( $lr_btn[0] );
			if ( '' === trim( wp_strip_all_tags( (string) $lr_label ) ) ) {
				continue;
			}
			$lr_url        = lr_opt( $lr_btn[1] );
			$lr_rendered[] = sprintf(
				'<a class="menu-panel__btn %1$s" href="%2$s">%3$s</a>',
				esc_attr( $lr_btn[2] ),
				esc_url( $lr_url ? $lr_url : '#' ),
				esc_html( $lr_label )
			);
		}
		if ( $lr_rendered ) {
			echo '<div class="menu-panel__actions">' . implode( '', $lr_rendered ) . '</div>'; // phpcs:ignore WordPress.Security.EscapingOutput
		}
		?>
	</div>
</div>

<main id="content">

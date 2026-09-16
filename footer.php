<?php
/**
 * Site footer.
 *
 * Carries the fixed gold "Schedule a visit" tab. The page footer proper is a
 * later milestone - nothing is rendered for it yet.
 *
 * @package lenfant-roi-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<?php if ( lr_opt( 'toolbar_show' ) && lr_opt( 'toolbar_label' ) ) : ?>
	<div class="toolbar">
		<a class="toolbar__link" href="<?php echo esc_url( lr_opt( 'toolbar_url' ) ? lr_opt( 'toolbar_url' ) : '#' ); ?>">
			<span><?php echo esc_html( lr_opt( 'toolbar_label' ) ); ?></span>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
				<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/>
				<circle cx="12" cy="10" r="2.6"/>
			</svg>
		</a>
	</div>
<?php endif; ?>

<?php
// Brief p1: "we want it on the bottom of the page, always". Rendered at the
// foot rather than pinned to the viewport - a fixed bar eats a third of a
// phone screen, and he asked me to choose.
$lr_slogan = lr_opt( 'slogan_title' );
$lr_since  = lr_opt( 'slogan_since' );
if ( '' !== trim( wp_strip_all_tags( (string) $lr_slogan . (string) $lr_since ) ) ) :
	?>
	<div class="slogan">
		<div class="container">
			<?php if ( '' !== trim( wp_strip_all_tags( (string) $lr_slogan ) ) ) : ?>
				<p class="slogan__title"><?php echo wp_kses_post( $lr_slogan ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== trim( wp_strip_all_tags( (string) $lr_since ) ) ) : ?>
				<p class="slogan__since"><span><?php echo wp_kses_post( $lr_since ); ?></span></p>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>

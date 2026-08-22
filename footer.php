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

<?php wp_footer(); ?>
</body>
</html>

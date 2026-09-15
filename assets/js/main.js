/* L'Enfant Roi Layout - front-end behaviour.
   Four things: the scroll-linked media reveal, in-view entrance animations,
   the header tucking away, and the full-screen menu.
   No library, no jQuery. */

(function () {
	'use strict';

	var root = document.documentElement;
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* Inside the Elementor editor nothing may stay hidden waiting to be scrolled
	   into view: the preview is an iframe that does not scroll the way the real
	   page does, and every edit re-renders the widget into fresh DOM nodes that
	   the observer set up at page load has never seen. Either way the content
	   would sit at opacity 0 and the client sees an empty canvas - which is
	   exactly what happened. In the editor we simply show everything. */
	function inEditor() {
		return !!(
			( window.elementorFrontend && elementorFrontend.isEditMode && elementorFrontend.isEditMode() ) ||
			document.body.classList.contains( 'elementor-editor-active' )
		);
	}

	function revealAll( scope ) {
		var els = ( scope || document ).querySelectorAll( '[data-animation]' );
		Array.prototype.forEach.call( els, function ( el ) { el.classList.add( 'is-inview' ); } );

		var lines = ( scope || document ).querySelectorAll( '.arabesque' );
		Array.prototype.forEach.call( lines, function ( el ) { el.classList.add( 'is-inview' ); } );

		var shapes = ( scope || document ).querySelectorAll( '.outline__shape' );
		Array.prototype.forEach.call( shapes, function ( el ) { el.style.strokeDasharray = 'none'; } );
	}

	/* --- 1. scroll-linked hero media -------------------------------------- */
	var media = document.querySelector('.section-hero-page-home .section__media');

	function updateMedia() {
		if (!media) return;
		/* In the editor the inline style would win over the stylesheet's
		   editor override, so set it open here rather than leaving the photo
		   clipped to a sliver on his canvas. */
		if (inEditor()) {
			media.style.setProperty('--media-progress', '1');
			return;
		}
		var rect = media.getBoundingClientRect();
		/* Progress runs 0 -> 1 as the block travels up the viewport.
		   The two ratios are measured off the original: the circle is still
		   closed when the block sits at 0.66vh, and fully open once its top
		   has passed 0.25vh above the fold. */
		var start = window.innerHeight * 0.66;
		var end = window.innerHeight * -0.25;
		var p = (start - rect.top) / (start - end);
		p = Math.max(0, Math.min(1, p));
		media.style.setProperty('--media-progress', p.toFixed(4));
	}

	/* --- 1a. the stadium outline draws itself as you scroll ---------------- */
	/* The original marks these `data-scrub="draw"`, and it really is scrubbed:
	   held still it does not move, so this is not a loop like the arabesque.
	   It animates stroke-dasharray - a constant total whose split shifts - and
	   leaves stroke-dashoffset at 0 throughout. Measured, not assumed. */
	var outlines = document.querySelectorAll('[data-draw] .outline__shape');

	function updateOutlines() {
		Array.prototype.forEach.call(outlines, function (shape) {
			var host = shape.closest('[data-draw]');
			var rect = host.getBoundingClientRect();
			var start = window.innerHeight * 0.95;
			var end = window.innerHeight * 0.30;
			var p = (start - rect.top) / (start - end);
			p = Math.max(0, Math.min(1, p));

			var len = shape.__len || (shape.__len = shape.getTotalLength());
			if (p >= 1) {
				shape.style.strokeDasharray = 'none';
			} else {
				shape.style.strokeDasharray = (len * p).toFixed(2) + ' ' + (len * (1 - p)).toFixed(2);
			}
		});
	}

	/* --- 1b. arabesque blocks only run while they are on screen ------------ */
	/* Ten lines animating forever in a section nobody is looking at is work the
	   browser does not need to do. This observer toggles both ways, unlike the
	   entrance one below which fires once and lets go. */
	function initArabesque() {
		var blocks = document.querySelectorAll('.arabesque');
		if (!blocks.length) return;
		if (inEditor() || reduced || !('IntersectionObserver' in window)) {
			Array.prototype.forEach.call(blocks, function (el) { el.classList.add('is-inview'); });
			return;
		}
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				entry.target.classList.toggle('is-inview', entry.isIntersecting);
			});
		}, { rootMargin: '200px 0px' });
		Array.prototype.forEach.call(blocks, function (el) { io.observe(el); });
	}

	/* --- 2. in-view entrance ---------------------------------------------- */
	function initInView() {
		var targets = document.querySelectorAll('[data-animation]');
		if (inEditor() || reduced || !('IntersectionObserver' in window)) {
			Array.prototype.forEach.call(targets, function (el) { el.classList.add('is-inview'); });
			document.body.classList.add('is-inview');
			return;
		}
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				var el = entry.target;
				var delay = parseInt(el.getAttribute('data-delay') || '0', 10);
				setTimeout(function () { el.classList.add('is-inview'); }, delay);
				io.unobserve(el);
			});
		}, { rootMargin: '0px 0px -12% 0px', threshold: 0.05 });

		Array.prototype.forEach.call(targets, function (el) { io.observe(el); });
	}

	/* --- 3. header tucks away on the way down ----------------------------- */
	var header = document.querySelector('.page__header');
	var lastY = window.pageYOffset;

	function updateHeader() {
		if (!header) return;
		var y = window.pageYOffset;
		var down = y > lastY;
		/* While the menu is open the bar has to stay put - it carries the
		   close button. */
		if (!document.body.classList.contains('menu-is-open')) {
			if (down && y > 160) header.classList.add('is-hidden');
			else if (!down) header.classList.remove('is-hidden');
		}
		lastY = y;
	}

	/* --- 4. the menu panel ------------------------------------------------- */
	var burger = document.querySelector('.burger');
	var panel = document.getElementById('lr-menu');
	var scrim = document.getElementById('lr-menu-scrim');

	function setMenu(open) {
		if (!burger || !panel) return;
		burger.classList.toggle('is-open', open);
		burger.setAttribute('aria-expanded', open ? 'true' : 'false');
		document.body.classList.toggle('menu-is-open', open);

		if (open) {
			panel.hidden = false;
			if (scrim) scrim.hidden = false;
			/* One frame between removing [hidden] and adding the class, or the
			   panel has nothing to slide from. */
			requestAnimationFrame(function () {
				panel.classList.add('is-open');
				if (scrim) scrim.classList.add('is-open');
			});
			if (header) header.classList.remove('is-hidden');
		} else {
			panel.classList.remove('is-open');
			if (scrim) scrim.classList.remove('is-open');
			setTimeout(function () {
				if (!panel.classList.contains('is-open')) {
					panel.hidden = true;
					if (scrim) scrim.hidden = true;
				}
			}, 450);
		}
	}

	if (burger && panel) {
		burger.addEventListener('click', function () {
			setMenu(!burger.classList.contains('is-open'));
		});
		panel.addEventListener('click', function (e) {
			if (e.target.tagName === 'A') setMenu(false);
		});
		/* Clicking the dimmed page closes it, which is what the original does. */
		if (scrim) scrim.addEventListener('click', function () { setMenu(false); });
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') setMenu(false);
		});
	}

	/* --- 5. rAF-throttled scroll ------------------------------------------ */
	var ticking = false;
	function onScroll() {
		if (ticking) return;
		ticking = true;
		requestAnimationFrame(function () { updateMedia(); updateOutlines(); updateHeader(); ticking = false; });
	}

	root.classList.remove('no-js');
	initArabesque();
	initInView();
	updateMedia();
	/* Reduced motion, and the editor: draw them once, complete, rather than
	   tying them to scroll. */
	if (reduced || inEditor()) {
		Array.prototype.forEach.call(outlines, function (s) { s.style.strokeDasharray = 'none'; });
	} else {
		updateOutlines();
	}
	/* Editor state is applied at every point it could become knowable, because
	   the order is not guaranteed: this script runs in the footer, Elementor's
	   own frontend script loads after it, and the body class may be present
	   from the server or not. Re-applying is cheap and idempotent; getting it
	   wrong leaves the client staring at a blank canvas. */
	function applyEditorState() {
		if (!inEditor()) return;
		if (media) media.style.setProperty('--media-progress', '1');
		revealAll();
	}

	applyEditorState();
	document.addEventListener('DOMContentLoaded', applyEditorState);
	window.addEventListener('load', applyEditorState);

	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll, { passive: true });

	/* Every edit re-renders a widget into brand new nodes. This fires for each
	   one, so the replacements get revealed too instead of vanishing the moment
	   he changes a word. */
	window.addEventListener('elementor/frontend/init', applyEditorState);

	if (window.elementorFrontend && elementorFrontend.hooks) {
		elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
			applyEditorState();
			var el = $scope && $scope[0] ? $scope[0] : null;
			if (!el) return;
			if (inEditor()) {
				revealAll(el);
			}
		});
	}
})();

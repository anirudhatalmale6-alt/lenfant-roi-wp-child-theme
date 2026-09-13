/* L'Enfant Roi Layout - front-end behaviour.
   Four things: the scroll-linked media reveal, in-view entrance animations,
   the header tucking away, and the full-screen menu.
   No library, no jQuery. */

(function () {
	'use strict';

	var root = document.documentElement;
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* --- 1. scroll-linked hero media -------------------------------------- */
	var media = document.querySelector('.section-hero-page-home .section__media');

	function updateMedia() {
		if (!media) return;
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
		if (reduced || !('IntersectionObserver' in window)) {
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
		if (reduced || !('IntersectionObserver' in window)) {
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

	/* --- 4. full-screen menu ---------------------------------------------- */
	var burger = document.querySelector('.burger');
	var overlay = document.getElementById('lr-menu');

	function setMenu(open) {
		if (!burger || !overlay) return;
		burger.classList.toggle('is-open', open);
		burger.setAttribute('aria-expanded', open ? 'true' : 'false');
		document.body.classList.toggle('menu-is-open', open);
		if (open) {
			overlay.hidden = false;
			/* One frame between removing [hidden] and adding the class, or the
			   transition has nothing to animate from. */
			requestAnimationFrame(function () { overlay.classList.add('is-open'); });
			if (header) header.classList.remove('is-hidden');
		} else {
			overlay.classList.remove('is-open');
			setTimeout(function () {
				if (!overlay.classList.contains('is-open')) overlay.hidden = true;
			}, 450);
		}
	}

	if (burger && overlay) {
		burger.addEventListener('click', function () {
			setMenu(!burger.classList.contains('is-open'));
		});
		overlay.addEventListener('click', function (e) {
			if (e.target.tagName === 'A') setMenu(false);
		});
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
	/* Reduced motion: draw them once, complete, rather than tying them to scroll. */
	if (reduced) {
		Array.prototype.forEach.call(outlines, function (s) { s.style.strokeDasharray = 'none'; });
	} else {
		updateOutlines();
	}
	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll, { passive: true });
})();

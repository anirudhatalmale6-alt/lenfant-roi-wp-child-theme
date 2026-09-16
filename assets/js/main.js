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

	/* --- 1c. join stacked white cards into one panel ----------------------- */
	/* The CSS does this with sibling selectors, which is correct on the
	   theme-rendered page. In Elementor it CANNOT work: every widget sits in
	   its own .elementor-widget-container, so two carded sections are never
	   adjacent siblings, and the container's flex gap leaves a strip of teal
	   between them. That is exactly what the client was seeing.

	   So the run is worked out from geometry instead of from the DOM shape,
	   which holds however the sections are wrapped. */
	function joinCards() {
		var cards = Array.prototype.slice.call( document.querySelectorAll( '.section--card' ) );
		if ( !cards.length ) return;

		/* Undo any previous pass before measuring, or the second run measures
		   gaps this function already closed. */
		cards.forEach( function ( card ) {
			var inner = card.querySelector( '.card__inner' );
			if ( inner ) inner.style.borderRadius = '';
			( card.closest( '.elementor-widget' ) || card ).style.marginTop = '';
		} );

		var runs = [];
		var run = [ cards[0] ];

		for ( var i = 1; i < cards.length; i++ ) {
			var prev = cards[ i - 1 ].getBoundingClientRect();
			var here = cards[ i ].getBoundingClientRect();
			/* Small gap - Elementor's flex gap is 20px by default - means the
			   two belong to the same panel. A real section between them puts
			   hundreds of pixels here. */
			if ( here.top - prev.bottom <= 60 ) {
				run.push( cards[ i ] );
			} else {
				runs.push( run );
				run = [ cards[ i ] ];
			}
		}
		runs.push( run );

		runs.forEach( function ( group ) {
			group.forEach( function ( card, index ) {
				var inner = card.querySelector( '.card__inner' );
				if ( !inner ) return;

				var first = 0 === index;
				var last  = index === group.length - 1;
				var r     = getComputedStyle( document.documentElement )
					.getPropertyValue( '--card-radius' ).trim() || '48px';

				inner.style.borderRadius = first && last ? ''
					: ( first ? r + ' ' + r + ' 0 0'
					: ( last ? '0 0 ' + r + ' ' + r : '0' ) );

				if ( !first ) {
					/* Close the gap on the element the flex gap applies to,
					   not on the section inside it. */
					var target = card.closest( '.elementor-widget' ) || card;
					var gap = card.getBoundingClientRect().top -
						group[ index - 1 ].getBoundingClientRect().bottom;
					if ( gap > 0 ) target.style.marginTop = ( -gap ) + 'px';
				}
			} );
		} );
	}

	/* --- 1d. the four icons open the panels below -------------------------- */
	/* Buttons, not hover: there is no hover on a phone and the panels would be
	   unreachable there. Without JS the panels have no [hidden] applied by this
	   code and stay visible, so the content is never lost. */
	function initPanels() {
		var buttons = document.querySelectorAll('.family__btn[data-opens]');
		if (!buttons.length) return;

		function panelFor(id) { return document.getElementById(id); }

		/* Close everything first so only one is ever open - the panels are full
		   width sections and two at once pushes the page around confusingly. */
		function closeAll() {
			Array.prototype.forEach.call(buttons, function (b) {
				var panel = panelFor(b.getAttribute('data-opens'));
				b.classList.remove('is-open');
				b.setAttribute('aria-expanded', 'false');
				if (panel) { panel.hidden = true; panel.classList.remove('is-open'); }
			});
		}

		Array.prototype.forEach.call(buttons, function (btn) {
			btn.addEventListener('click', function () {
				var id = btn.getAttribute('data-opens');
				var panel = panelFor(id);
				if (!panel) return;

				var wasOpen = btn.classList.contains('is-open');
				closeAll();

				if (wasOpen) return;      /* second click closes it */

				panel.hidden = false;
				/* One frame so the transition has a starting height to run from. */
				requestAnimationFrame(function () {
					panel.classList.add('is-open');
					revealAll(panel);
					joinCards();
				});
				btn.classList.add('is-open');
				btn.setAttribute('aria-expanded', 'true');

				panel.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth', block: 'start' });
			});
		});

		if (inEditor()) {
			/* In the editor he needs to see the panels to edit them. */
			Array.prototype.forEach.call(document.querySelectorAll('[data-panel]'), function (p) {
				p.hidden = false;
			});
		}
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
	initPanels();
	joinCards();
	document.addEventListener('DOMContentLoaded', function () { applyEditorState(); joinCards(); });
	window.addEventListener('load', function () { applyEditorState(); joinCards(); });

	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll, { passive: true });
	/* Card heights change with the viewport, so the joins are re-measured. */
	window.addEventListener('resize', joinCards, { passive: true });

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
			joinCards();
		});
	}
})();

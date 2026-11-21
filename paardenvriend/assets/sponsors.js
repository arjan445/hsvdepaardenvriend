/* Sponsorbalk: bij elk bezoek een nieuwe gewogen willekeurige selectie (werkt ook met paginacache). */
(function () {
	'use strict';
	document.querySelectorAll('.pv-sponsors.is-random').forEach(function (box) {
		var grid = box.querySelector('.pv-sponsor-grid');
		var count = parseInt(box.getAttribute('data-aantal'), 10) || 6;
		var items = Array.prototype.slice.call(grid.querySelectorAll('.pv-sponsor'));
		// Efraimidis–Spirakis: sleutel = r^(1/gewicht), hoogste sleutels winnen.
		items.map(function (el) {
			var w = Math.max(1, parseFloat(el.getAttribute('data-gewicht')) || 1);
			return { el: el, key: Math.pow(Math.random(), 1 / w) };
		}).sort(function (a, b) {
			return b.key - a.key;
		}).forEach(function (x, i) {
			x.el.hidden = i >= count;
			grid.appendChild(x.el);
		});
	});
})();

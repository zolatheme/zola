/**
 * Small, dependency-free interactions shared by the theme.
 *
 * @package Zola
 */
(function () {
	'use strict';

	const root = document.documentElement;
	const strings = window.ZolaStrings || {};
	root.classList.add('has-js');

	const menuToggles = Array.from(document.querySelectorAll('.menu-toggle, [data-zola-menu-toggle]'));
	const openMenus = new Set();

	function setMenuOpen(toggle, menu, isOpen) {
		toggle.setAttribute('aria-expanded', String(isOpen));
		menu.classList.toggle('is-open', isOpen);
		openMenus.add({ toggle: toggle, menu: menu });
		if (!isOpen) {
			for (const entry of openMenus) {
				if (entry.toggle === toggle) {
					openMenus.delete(entry);
				}
			}
		}
		const label = toggle.querySelector('.screen-reader-text');
		if (label) {
			label.textContent = isOpen ? (strings.menuClose || 'Close navigation') : (strings.menuOpen || 'Open navigation');
		}
	}

	menuToggles.forEach(function (toggle) {
		const controlledId = toggle.getAttribute('aria-controls');
		const menu = controlledId ? document.getElementById(controlledId) : null;
		if (!menu) {
			return;
		}

		toggle.addEventListener('click', function () {
			setMenuOpen(toggle, menu, toggle.getAttribute('aria-expanded') !== 'true');
		});
	});

	function closeMenus(restoreFocus) {
		for (const entry of Array.from(openMenus)) {
			setMenuOpen(entry.toggle, entry.menu, false);
			if (restoreFocus) {
				entry.toggle.focus();
			}
		}
	}

	document.addEventListener('click', function (event) {
		for (const entry of Array.from(openMenus)) {
			if (!entry.menu.contains(event.target) && !entry.toggle.contains(event.target)) {
				setMenuOpen(entry.toggle, entry.menu, false);
			}
		}
	});

	document.addEventListener('keydown', function (event) {
		if ('Escape' === event.key) {
			const expandedSubmenu = document.querySelector('[data-zola-submenu-toggle][aria-expanded="true"]');
			if (expandedSubmenu) {
				setSubmenuOpen(expandedSubmenu, false, true);
				return;
			}
			closeMenus(true);
		}
	});

	let submenuIndex = 0;
	const submenuToggles = [];

	function setSubmenuOpen(toggle, isOpen, restoreFocus) {
		const item = toggle.closest('.menu-item-has-children');
		const submenuId = toggle.getAttribute('aria-controls');
		const submenu = submenuId ? document.getElementById(submenuId) : null;
		if (!item || !submenu) {
			return;
		}
		item.classList.toggle('is-submenu-open', isOpen);
		toggle.setAttribute('aria-expanded', String(isOpen));
		const link = Array.from(item.children).find(function (child) {
			return child.matches('a');
		});
		if (link) {
			toggle.setAttribute('aria-label', (isOpen ? (strings.submenuClose || 'Close submenu') : (strings.submenuOpen || 'Open submenu')) + ': ' + link.textContent.trim());
		}
		if (restoreFocus) {
			toggle.focus();
		}
	}

	document.querySelectorAll('.site-nav, .zola-menu').forEach(function (navigation) {
		navigation.querySelectorAll('li.menu-item-has-children').forEach(function (item) {
			const submenu = Array.from(item.children).find(function (child) {
				return child.matches('ul.sub-menu, ul.children');
			});
			const link = Array.from(item.children).find(function (child) {
				return child.matches('a');
			});
			if (!submenu || !link) {
				return;
			}

			do {
				submenuIndex += 1;
				submenu.id = 'zola-submenu-' + submenuIndex;
			} while (document.getElementById(submenu.id) !== submenu);

			const toggle = document.createElement('button');
			toggle.type = 'button';
			toggle.className = navigation.matches('.site-nav') ? 'site-nav__submenu-toggle' : 'zola-menu__submenu-toggle';
			toggle.setAttribute('data-zola-submenu-toggle', '');
			toggle.setAttribute('aria-expanded', 'false');
			toggle.setAttribute('aria-controls', submenu.id);
			toggle.setAttribute('aria-label', (strings.submenuOpen || 'Open submenu') + ': ' + link.textContent.trim());
			const icon = document.createElement('span');
			icon.setAttribute('aria-hidden', 'true');
			icon.textContent = '⌄';
			toggle.appendChild(icon);
			link.insertAdjacentElement('afterend', toggle);
			submenuToggles.push(toggle);
		});
	});

	submenuToggles.forEach(function (toggle) {
		toggle.addEventListener('click', function () {
			const isOpen = toggle.getAttribute('aria-expanded') !== 'true';
			setSubmenuOpen(toggle, isOpen, false);
		});
	});

	const header = document.querySelector('[data-site-header]');
	if (header) {
		let frame = 0;
		const updateHeader = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 16);
			frame = 0;
		};
		window.addEventListener('scroll', function () {
			if (!frame) {
				frame = window.requestAnimationFrame(updateHeader);
			}
		}, { passive: true });
		updateHeader();
	}

	document.querySelectorAll('[data-copy-link]').forEach(function (button) {
		button.addEventListener('click', function () {
			const value = button.getAttribute('data-copy-link');
			if (!value || !navigator.clipboard || !navigator.clipboard.writeText) {
				return;
			}
			navigator.clipboard.writeText(value).then(function () {
				const originalLabel = button.textContent;
				button.textContent = strings.copied || 'Copied';
				window.setTimeout(function () {
					button.textContent = originalLabel;
				}, 1800);
			}).catch(function () {});
		});
	});

	const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	if (!reduceMotion && 'IntersectionObserver' in window) {
		const revealItems = document.querySelectorAll('[data-reveal], [data-hero-intro]');
		if (revealItems.length) {
			const observer = new IntersectionObserver(function (entries, currentObserver) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						currentObserver.unobserve(entry.target);
					}
				});
			}, { rootMargin: '0px 0px -48px 0px', threshold: 0.08 });
			revealItems.forEach(function (item) {
				observer.observe(item);
			});
			root.classList.add('has-motion');
		}
	}
})();

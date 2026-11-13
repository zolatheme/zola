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

	function syncMobileOverlayPosition(menu) {
		if (!menu.matches('.zola-mobile-menu-overlay')) {
			return;
		}
		const mainRow = menu.closest('.site-header-builder__row--main');
		if (mainRow) {
			menu.style.setProperty('--zola-mobile-overlay-top', Math.max(0, mainRow.getBoundingClientRect().bottom) + 'px');
		}
	}

	function setMenuOpen(toggle, menu, isOpen) {
		if (isOpen) {
			syncMobileOverlayPosition(menu);
		}
		toggle.setAttribute('aria-expanded', String(isOpen));
		menu.setAttribute('aria-hidden', String(!isOpen));
		menu.classList.toggle('is-open', isOpen);
		if (menu.matches('.zola-mobile-menu-overlay')) {
			document.body.classList.toggle('zola-mobile-menu-is-open', isOpen);
		}
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
		menu.setAttribute('aria-hidden', 'true');

		toggle.addEventListener('click', function () {
			const isOpen = toggle.getAttribute('aria-expanded') !== 'true';
			setMenuOpen(toggle, menu, isOpen);
			if (isOpen && menu.matches('.zola-mobile-menu-overlay')) {
				const firstControl = menu.querySelector('a[href], button, input:not([type="hidden"])');
				if (firstControl) {
					firstControl.focus({ preventScroll: true });
				}
			}
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

	const desktopViewport = window.matchMedia && window.matchMedia('(min-width: 783px)');
	window.addEventListener('resize', function () {
		for (const entry of openMenus) {
			syncMobileOverlayPosition(entry.menu);
		}
	}, { passive: true });
	if (desktopViewport) {
		const closeMobileOverlays = function (event) {
			if (!event.matches) {
				return;
			}
			for (const entry of Array.from(openMenus)) {
				if (entry.menu.matches('.zola-mobile-menu-overlay')) {
					setMenuOpen(entry.toggle, entry.menu, false);
				}
			}
		};
		if (desktopViewport.addEventListener) {
			desktopViewport.addEventListener('change', closeMobileOverlays);
		} else if (desktopViewport.addListener) {
			desktopViewport.addListener(closeMobileOverlays);
		}
	}

	document.addEventListener('click', function (event) {
		const closeButton = event.target.closest('[data-zola-menu-close]');
		if (closeButton) {
			const menu = closeButton.closest('.zola-mobile-menu-overlay');
			if (menu) {
				const controlledId = menu.id;
				const toggle = menuToggles.find(function (item) {
					return item.getAttribute('aria-controls') === controlledId;
				});
				if (toggle) {
					setMenuOpen(toggle, menu, false);
					toggle.focus();
				}
			}
			return;
		}
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
			return;
		}
		if ('Tab' === event.key) {
			const activeOverlay = Array.from(openMenus).find(function (entry) {
				return entry.menu.matches('.zola-mobile-menu-overlay')
					&& entry.menu.classList.contains('is-open')
					&& entry.menu.querySelector('[data-zola-menu-close]');
			});
			if (!activeOverlay) {
				return;
			}
			const focusable = Array.from(activeOverlay.menu.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')).filter(function (element) {
				return element.getClientRects().length > 0;
			});
			if (!focusable.length) {
				event.preventDefault();
				return;
			}
			const first = focusable[0];
			const last = focusable[focusable.length - 1];
			if (event.shiftKey && (document.activeElement === first || !activeOverlay.menu.contains(document.activeElement))) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && (document.activeElement === last || !activeOverlay.menu.contains(document.activeElement))) {
				event.preventDefault();
				first.focus();
			}
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

	document.querySelectorAll('.site-nav[data-submenu-behavior="toggle-submenu"]').forEach(function (navigation) {
		navigation.addEventListener('click', function (event) {
			if (window.innerWidth > 782) {
				return;
			}
			const link = event.target.closest('.menu-item-has-children > a');
			const item = link ? link.closest('.menu-item-has-children') : null;
			const toggle = item ? item.querySelector(':scope > [data-zola-submenu-toggle]') : null;
			if (toggle) {
				event.preventDefault();
				setSubmenuOpen(toggle, !item.classList.contains('is-submenu-open'), false);
			}
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
			const viewportHeight = window.innerHeight || root.clientHeight;
			const deferredRevealItems = [];
			revealItems.forEach(function (item) {
				const bounds = item.getBoundingClientRect();
				if (bounds.top < viewportHeight && bounds.bottom > 0) {
					item.classList.add('is-visible');
				} else {
					deferredRevealItems.push(item);
				}
			});

			const observer = new IntersectionObserver(function (entries, currentObserver) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						currentObserver.unobserve(entry.target);
					}
				});
			}, { rootMargin: '0px 0px -48px 0px', threshold: 0.08 });
			deferredRevealItems.forEach(function (item) {
				observer.observe(item);
			});
			root.classList.add('has-motion');
		}
	}
})();

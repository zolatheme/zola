(function (wp) {
	'use strict';

	if (!wp || !wp.customize) {
		return;
	}

	const strings = window.ZolaHeaderBuilder || {};
	const moduleNames = strings.modules || {};
	const headerPresets = Array.isArray(strings.headerPresets) ? strings.headerPresets : [];
	const rowNames = strings.rows || {};
	const columnNames = strings.columns || {};
	const moduleOrder = Object.keys(moduleNames);
	let activeBuilderDevice = 'desktop';
	const settingsSections = {
		logo: { type: 'section', id: 'title_tagline' },
		menu: { type: 'panel', id: 'nav_menus' },
		button: { type: 'section', id: 'zola_header' },
		announcement: { type: 'section', id: 'zola_header' },
		social: { type: 'section', id: 'zola_header' },
		html: { type: 'section', id: 'zola_header' },
		account: { type: 'section', id: 'zola_header' },
		cart: { type: 'section', id: 'zola_header' }
	};

	function emptyRows() {
		return {
			top: { left: [], center: [], right: [] },
			main: { left: [], center: [], right: [] },
			bottom: { left: [], center: [], right: [] }
		};
	}

	function emptyLayout() {
		return {
			desktop: { rows: {
				top: { left: [], center: ['announcement'], right: [] },
				main: { left: ['logo'], center: ['menu'], right: ['button'] },
				bottom: { left: [], center: [], right: [] }
			} },
			mobile: { rows: {
				top: { left: [], center: ['announcement'], right: [] },
				main: { left: ['logo'], center: [], right: ['menu'] },
				bottom: { left: [], center: [], right: [] }
			} }
		};
	}

	function normalizeLayout(value) {
		let parsed = value;
		if (typeof parsed === 'string') {
			try {
				parsed = JSON.parse(parsed);
			} catch (error) {
				parsed = null;
			}
		}

		const fallback = emptyLayout();
		const legacyRows = parsed && parsed.rows ? parsed.rows : null;
		['desktop', 'mobile'].forEach(function (device) {
			const rows = legacyRows && 'desktop' === device ? legacyRows : (parsed && parsed[device] && parsed[device].rows ? parsed[device].rows : {});
			const used = new Set();
			['top', 'main', 'bottom'].forEach(function (row) {
				['left', 'center', 'right'].forEach(function (column) {
					const items = rows[row] && Array.isArray(rows[row][column]) ? rows[row][column] : fallback[device].rows[row][column];
					fallback[device].rows[row][column] = items.filter(function (module) {
						if (!moduleNames[module] || used.has(module)) {
							return false;
						}
						used.add(module);
						return true;
					});
				});
			});
		});
		return fallback;
	}

	function removeModule(layout, module, device) {
		['top', 'main', 'bottom'].forEach(function (row) {
			['left', 'center', 'right'].forEach(function (column) {
				layout[device].rows[row][column] = layout[device].rows[row][column].filter(function (item) {
					return item !== module;
				});
			});
		});
	}

	function createPresetPreview(preset, device) {
		const preview = document.createElement('div');
		preview.className = 'zola-header-preset__preview zola-header-preset__preview--' + device + ' zola-header-preset__preview--' + preset.id;
		preview.setAttribute('aria-hidden', 'true');
		const rows = preset[device] || {};
		const moduleLabels = {
			logo: 'Logo', menu: 'HOME   LINK', button: 'SIGN UP', search: '⌕',
			announcement: 'TOP BAR', social: '●  ●  ●', html: 'INFO', account: 'ACCOUNT', cart: 'CART  3'
		};
		['top', 'main', 'bottom'].forEach(function (rowName) {
			const rowData = rows[rowName] || {};
			const modulesInRow = ['left', 'center', 'right'].some(function (column) {
				return Array.isArray(rowData[column]) && rowData[column].length;
			});
			if (!modulesInRow) {
				return;
			}
			const row = document.createElement('div');
			row.className = 'zola-header-preset__row zola-header-preset__row--' + rowName;
			['left', 'center', 'right'].forEach(function (column) {
				const slot = document.createElement('div');
				slot.className = 'zola-header-preset__slot zola-header-preset__slot--' + column;
				(Array.isArray(rowData[column]) ? rowData[column] : []).forEach(function (module) {
					const item = document.createElement('span');
					item.className = 'zola-header-preset__item zola-header-preset__item--' + module;
					item.textContent = moduleLabels[module] || moduleNames[module] || module;
					slot.appendChild(item);
				});
				row.appendChild(slot);
			});
			preview.appendChild(row);
		});
		return preview;
	}

	function setupPresetsControl(control) {
		const root = control.container.find('[data-zola-header-presets-ui]').get(0);
		const layoutSetting = control.setting || wp.customize('zola_header_builder_layout');
		if (!root || !layoutSetting) {
			return;
		}

		let layout = normalizeLayout(layoutSetting.get());

		function makeButton(label, className, attributes) {
			const button = document.createElement('button');
			button.type = 'button';
			button.className = className;
			button.textContent = label;
			Object.keys(attributes || {}).forEach(function (key) {
				button.setAttribute(key, attributes[key]);
			});
			return button;
		}

		function applyPreset(preset) {
			if (!preset || !preset[activeBuilderDevice]) {
				return;
			}
			layout = normalizeLayout(layoutSetting.get());
			layout[activeBuilderDevice].rows = JSON.parse(JSON.stringify(preset[activeBuilderDevice]));
			layoutSetting.set(JSON.stringify(layout));
			layout = normalizeLayout(layoutSetting.get());
			render();
		}

		function render() {
			root.replaceChildren();
			const devices = document.createElement('div');
			devices.className = 'zola-header-presets-control__devices';
			['desktop', 'mobile'].forEach(function (device) {
				const button = makeButton((strings.devices || {})[device] || device, 'button ' + (activeBuilderDevice === device ? 'button-primary' : 'button-secondary'), { 'data-zola-preset-device': device });
				button.setAttribute('aria-pressed', activeBuilderDevice === device ? 'true' : 'false');
				devices.appendChild(button);
			});
			root.appendChild(devices);

			const builder = document.createElement('section');
			builder.className = 'zola-header-presets-control__builder';
			const builderHeader = document.createElement('div');
			builderHeader.className = 'zola-header-presets-control__builder-header';
			const builderTitle = document.createElement('h3');
			builderTitle.textContent = strings.builderTitle || 'Header Builder';
			builderHeader.appendChild(builderTitle);
			const selectedPreset = headerPresets.find(function (preset) {
				return JSON.stringify(layout[activeBuilderDevice].rows) === JSON.stringify(preset[activeBuilderDevice]);
			});
			const builderStatus = document.createElement('span');
			builderStatus.className = 'zola-header-presets-control__builder-status';
			builderStatus.textContent = selectedPreset ? selectedPreset.title : (strings.customLayout || 'Custom');
			builderHeader.appendChild(builderStatus);
			const openBuilder = makeButton(strings.openBuilder || 'Open Header Builder', 'button button-secondary zola-header-presets-control__open-builder', { 'data-zola-open-full-builder': 'true' });
			builderHeader.appendChild(openBuilder);
			builder.appendChild(builderHeader);

			const builderRows = document.createElement('div');
			builderRows.className = 'zola-header-presets-control__builder-rows';
			['top', 'main', 'bottom'].forEach(function (rowName) {
				const row = document.createElement('div');
				row.className = 'zola-header-presets-control__builder-row';
				const rowTitle = document.createElement('span');
				rowTitle.className = 'zola-header-presets-control__builder-row-title';
				rowTitle.textContent = rowNames[rowName] || rowName;
				row.appendChild(rowTitle);
				const columns = document.createElement('div');
				columns.className = 'zola-header-presets-control__builder-columns';
				['left', 'center', 'right'].forEach(function (columnName) {
					const slot = document.createElement('div');
					slot.className = 'zola-header-presets-control__builder-slot zola-header-presets-control__builder-slot--' + columnName;
					const modules = layout[activeBuilderDevice].rows[rowName][columnName];
					if (modules.length) {
						modules.forEach(function (module) {
							const chip = document.createElement('span');
							chip.className = 'zola-header-presets-control__builder-chip';
							chip.textContent = moduleNames[module] || module;
							slot.appendChild(chip);
						});
					} else {
						slot.classList.add('is-empty');
						slot.textContent = '—';
					}
					columns.appendChild(slot);
				});
				row.appendChild(columns);
				builderRows.appendChild(row);
			});
			builder.appendChild(builderRows);
			root.appendChild(builder);

			const gallery = document.createElement('div');
			gallery.className = 'zola-header-presets-control__grid';
			headerPresets.forEach(function (preset) {
				const isActive = JSON.stringify(layout[activeBuilderDevice].rows) === JSON.stringify(preset[activeBuilderDevice]);
				const card = makeButton('', 'zola-header-preset-picker' + (isActive ? ' is-active' : ''), { 'data-zola-apply-preset': preset.id });
				card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
				card.setAttribute('aria-label', (preset.title || preset.id) + ': ' + (preset.description || ''));
				card.appendChild(createPresetPreview(preset, activeBuilderDevice));
				gallery.appendChild(card);
			});
			root.appendChild(gallery);
		}

		root.addEventListener('click', function (event) {
			const openBuilderButton = event.target.closest('[data-zola-open-full-builder]');
			if (openBuilderButton) {
				document.dispatchEvent(new CustomEvent('zola-open-header-builder', { detail: { trigger: openBuilderButton } }));
				return;
			}

			const deviceButton = event.target.closest('[data-zola-preset-device]');
			if (deviceButton) {
				activeBuilderDevice = deviceButton.dataset.zolaPresetDevice;
				layout = normalizeLayout(layoutSetting.get());
				render();
				document.dispatchEvent(new CustomEvent('zola-header-builder-device-change', { detail: { device: activeBuilderDevice } }));
				return;
			}
			const presetButton = event.target.closest('[data-zola-apply-preset]');
			if (presetButton) {
				const preset = headerPresets.find(function (item) { return item.id === presetButton.dataset.zolaApplyPreset; });
				applyPreset(preset);
			}
		});

		layoutSetting.bind(function (value) {
			layout = normalizeLayout(value);
			render();
		});
		document.addEventListener('zola-header-builder-device-change', function (event) {
			if (event.detail && ['desktop', 'mobile'].includes(event.detail.device) && activeBuilderDevice !== event.detail.device) {
				activeBuilderDevice = event.detail.device;
				render();
			}
		});
		render();
	}

	function setupControl(control) {
		const root = control.container.find('[data-zola-header-builder-ui]').get(0);
		if (!root || !control.setting) {
			return;
		}

		let layout = normalizeLayout(control.setting.get());
		let activeDevice = activeBuilderDevice;
		let activePosition = { row: 'main', column: 'left' };
		let draggedModule = '';
		let overlay = null;
		let lastFocusTrigger = null;
		let presetsOpen = false;
		let tutorialOpen = false;

		function save() {
			control.setting.set(JSON.stringify(layout));
		}

		function makeButton(label, className, attributes) {
			const button = document.createElement('button');
			button.type = 'button';
			button.className = className;
			button.textContent = label;
			Object.keys(attributes || {}).forEach(function (key) {
				button.setAttribute(key, attributes[key]);
			});
			return button;
		}

		function closeOverlay(restoreFocus) {
			if (overlay) {
				overlay.remove();
				overlay = null;
			}
			document.body.classList.remove('zola-header-builder-is-open');
			if (restoreFocus) {
				const focusTarget = lastFocusTrigger && lastFocusTrigger.isConnected ? lastFocusTrigger : root.querySelector('[data-zola-open-builder]');
				if (focusTarget) {
					focusTarget.focus();
				}
			}
		}

		function focusModuleSettings(module) {
			const setting = settingsSections[module];
			closeOverlay(false);
			if (!setting) {
				return;
			}
			window.requestAnimationFrame(function () {
				const section = wp.customize[setting.type](setting.id);
				if (section && typeof section.focus === 'function') {
					section.focus();
				}
			});
		}

		function render() {
			if (!overlay) {
				return;
			}
			overlay.replaceChildren();

			const toolbar = document.createElement('div');
			toolbar.className = 'zola-header-builder__toolbar';
			const titleGroup = document.createElement('div');
			titleGroup.className = 'zola-header-builder__title-group';
			const title = document.createElement('h2');
			title.className = 'zola-header-builder__title';
			title.textContent = strings.builderTitle || 'Header Builder';
			titleGroup.appendChild(title);
			const selectedPreset = headerPresets.find(function (preset) {
				return JSON.stringify(layout[activeDevice].rows) === JSON.stringify(preset[activeDevice]);
			});
			const currentLayout = document.createElement('span');
			currentLayout.className = 'zola-header-builder__current-preset';
			currentLayout.textContent = (strings.currentLayout || 'Current layout') + ': ' + (selectedPreset ? selectedPreset.title : (strings.customLayout || 'Custom'));
			titleGroup.appendChild(currentLayout);
			toolbar.appendChild(titleGroup);

			const deviceTabs = document.createElement('div');
			deviceTabs.className = 'zola-header-builder__devices';
			['desktop', 'mobile'].forEach(function (device) {
				const tab = makeButton((strings.devices || {})[device] || device, 'zola-header-builder__device-button' + (activeDevice === device ? ' is-active' : ''), { 'data-zola-device': device });
				tab.setAttribute('aria-pressed', activeDevice === device ? 'true' : 'false');
				deviceTabs.appendChild(tab);
			});
			toolbar.appendChild(deviceTabs);

			const actions = document.createElement('div');
			actions.className = 'zola-header-builder__actions';
			actions.appendChild(makeButton(strings.presetsLabel || 'Header Presets', 'zola-header-builder__action', { 'data-zola-toggle-presets': 'true', 'aria-expanded': presetsOpen ? 'true' : 'false' }));
			actions.appendChild(makeButton(strings.tutorial || 'Tutorial', 'zola-header-builder__action', { 'data-zola-toggle-tutorial': 'true', 'aria-expanded': tutorialOpen ? 'true' : 'false' }));
			actions.appendChild(makeButton(strings.clearLayout || 'Clear All', 'zola-header-builder__action', { 'data-zola-clear': 'true' }));
			actions.appendChild(makeButton('× ' + (strings.closeBuilder || 'Close'), 'zola-header-builder__action', { 'data-zola-close': 'true' }));
			toolbar.appendChild(actions);
			overlay.appendChild(toolbar);

			if (presetsOpen) {
				const gallery = document.createElement('section');
				gallery.className = 'zola-header-preset-gallery';
				gallery.setAttribute('aria-label', strings.presetsLabel || 'Header Presets');
				const galleryHeader = document.createElement('div');
				galleryHeader.className = 'zola-header-preset-gallery__header';
				const galleryTitle = document.createElement('h3');
				galleryTitle.textContent = strings.presetsLabel || 'Header Presets';
				galleryHeader.appendChild(galleryTitle);
				galleryHeader.appendChild(makeButton('×', 'zola-header-preset-gallery__close', { 'data-zola-close-presets': 'true', 'aria-label': strings.closeBuilder || 'Close' }));
				gallery.appendChild(galleryHeader);
				const galleryGrid = document.createElement('div');
				galleryGrid.className = 'zola-header-preset-gallery__grid';
				headerPresets.forEach(function (preset) {
					const card = makeButton('', 'zola-header-preset', { 'data-zola-preset': preset.id });
					card.setAttribute('aria-label', (preset.title || preset.id) + ': ' + (preset.description || ''));
					card.appendChild(createPresetPreview(preset, activeDevice));
					const cardTitle = document.createElement('span');
					cardTitle.className = 'zola-header-preset__title';
					cardTitle.textContent = preset.title || preset.id;
					card.appendChild(cardTitle);
					const cardDescription = document.createElement('span');
					cardDescription.className = 'zola-header-preset__description';
					cardDescription.textContent = preset.description || '';
					card.appendChild(cardDescription);
					galleryGrid.appendChild(card);
				});
				gallery.appendChild(galleryGrid);
				overlay.appendChild(gallery);
			}

			if (tutorialOpen) {
				const tutorial = document.createElement('p');
				tutorial.className = 'zola-header-builder__tutorial';
				tutorial.textContent = strings.tutorialText || 'Choose a device, select a position, then add or drag elements.';
				overlay.appendChild(tutorial);
			}

			const rows = document.createElement('div');
			rows.className = 'zola-header-builder__rows';
			['top', 'main', 'bottom'].forEach(function (rowName) {
				const row = document.createElement('section');
				row.className = 'zola-header-builder__row zola-header-builder__row--' + rowName;
				const heading = document.createElement('h3');
				heading.className = 'zola-header-builder__row-label';
				heading.textContent = rowNames[rowName] || rowName;
				row.appendChild(heading);

				const columns = document.createElement('div');
				columns.className = 'zola-header-builder__columns';
				['left', 'center', 'right'].forEach(function (columnName) {
					const slot = document.createElement('div');
					slot.className = 'zola-header-builder__slot';
					slot.dataset.row = rowName;
					slot.dataset.column = columnName;
					slot.setAttribute('role', 'group');
					slot.setAttribute('aria-label', (rowNames[rowName] || rowName) + ' / ' + (columnNames[columnName] || columnName));
					if (activePosition.row === rowName && activePosition.column === columnName) {
						slot.classList.add('is-active');
					}

					const slotLabel = document.createElement('span');
					slotLabel.className = 'zola-header-builder__slot-label';
					slotLabel.textContent = columnNames[columnName] || columnName;
					slot.appendChild(slotLabel);

					const items = document.createElement('div');
					items.className = 'zola-header-builder__items';
					const modules = layout[activeDevice].rows[rowName][columnName];
					if (!modules.length) {
						const empty = document.createElement('span');
						empty.className = 'zola-header-builder__empty';
						empty.textContent = strings.emptySlot || 'Drop elements here';
						items.appendChild(empty);
					}
					modules.forEach(function (module) {
						const chip = document.createElement('div');
						chip.className = 'zola-header-builder__item';
						chip.draggable = true;
						chip.dataset.zolaItem = module;
						const chipLabel = document.createElement('span');
						chipLabel.className = 'zola-header-builder__item-label';
						chipLabel.textContent = moduleNames[module];
						chip.appendChild(chipLabel);
						if (settingsSections[module]) {
							chip.appendChild(makeButton('⚙', 'zola-header-builder__settings', { 'data-zola-settings': module, 'aria-label': (strings.settings || 'Settings') + ': ' + moduleNames[module], 'title': strings.settings || 'Settings' }));
						}
						chip.appendChild(makeButton('×', 'zola-header-builder__remove', { 'data-zola-remove': module, 'aria-label': (strings.remove || 'Remove') + ' ' + moduleNames[module], 'title': strings.remove || 'Remove' }));
						items.appendChild(chip);
					});
					slot.appendChild(items);
					columns.appendChild(slot);
				});
				row.appendChild(columns);
				rows.appendChild(row);
			});
			overlay.appendChild(rows);

			const inventory = document.createElement('div');
			inventory.className = 'zola-header-builder__inventory';
			const inventoryTitle = document.createElement('h3');
			inventoryTitle.className = 'zola-header-builder__inventory-title';
			inventoryTitle.textContent = strings.elements || 'Add elements';
			inventory.appendChild(inventoryTitle);
			const available = document.createElement('div');
			available.className = 'zola-header-builder__available';
			const assigned = new Set();
			['top', 'main', 'bottom'].forEach(function (row) {
				['left', 'center', 'right'].forEach(function (column) {
					layout[activeDevice].rows[row][column].forEach(function (module) { assigned.add(module); });
				});
			});
			let availableCount = 0;
			moduleOrder.forEach(function (module) {
				if (!assigned.has(module)) {
					available.appendChild(makeButton(moduleNames[module], 'zola-header-builder__available-item', { 'data-zola-add': module, 'title': moduleNames[module] }));
					availableCount += 1;
				}
			});
			if (!availableCount) {
				const allAdded = document.createElement('span');
				allAdded.className = 'zola-header-builder__empty-inventory';
				allAdded.textContent = strings.noAvailable || 'All elements are in this layout.';
				available.appendChild(allAdded);
			}
			inventory.appendChild(available);
			overlay.appendChild(inventory);
		}

		function openOverlay(trigger) {
			if (overlay) {
				return;
			}
			lastFocusTrigger = trigger || document.activeElement;
			overlay = document.createElement('section');
			overlay.className = 'zola-header-builder__overlay';
			overlay.setAttribute('role', 'dialog');
			overlay.setAttribute('aria-modal', 'true');
			overlay.setAttribute('aria-label', strings.builderTitle || 'Header Builder');
			document.body.appendChild(overlay);
			document.body.classList.add('zola-header-builder-is-open');
			overlay.addEventListener('keydown', function (event) {
				if ('Escape' === event.key) {
					closeOverlay(true);
				}
			});
			overlay.addEventListener('click', handleClick);
			overlay.addEventListener('dragstart', handleDragStart);
			overlay.addEventListener('dragover', handleDragOver);
			overlay.addEventListener('dragleave', handleDragLeave);
			overlay.addEventListener('drop', handleDrop);
			render();
			const closeButton = overlay.querySelector('[data-zola-close]');
			if (closeButton) {
				closeButton.focus();
			}
		}

		function setPreset(presetId) {
			const preset = headerPresets.find(function (item) { return item.id === presetId; });
			if (!preset || !preset[activeDevice]) {
				return;
			}
			layout[activeDevice].rows = JSON.parse(JSON.stringify(preset[activeDevice]));
			activePosition = { row: 'main', column: 'left' };
			presetsOpen = false;
			render();
			save();
		}

		function handleClick(event) {
			const deviceButton = event.target.closest('[data-zola-device]');
			if (deviceButton) {
				activeDevice = deviceButton.dataset.zolaDevice;
				activeBuilderDevice = activeDevice;
				activePosition = { row: 'main', column: 'left' };
				render();
				document.dispatchEvent(new CustomEvent('zola-header-builder-device-change', { detail: { device: activeDevice } }));
				return;
			}

			if (event.target.closest('[data-zola-close]')) {
				closeOverlay(true);
				return;
			}

			if (event.target.closest('[data-zola-close-presets]')) {
				presetsOpen = false;
				render();
				return;
			}

			if (event.target.closest('[data-zola-toggle-presets]')) {
				presetsOpen = !presetsOpen;
				tutorialOpen = false;
				render();
				return;
			}

			if (event.target.closest('[data-zola-toggle-tutorial]')) {
				tutorialOpen = !tutorialOpen;
				presetsOpen = false;
				render();
				return;
			}

			if (event.target.closest('[data-zola-clear]')) {
				if (window.confirm(strings.clearConfirm || 'Clear the current device header layout?')) {
					layout[activeDevice].rows = emptyRows();
					render();
					save();
				}
				return;
			}

			const presetButton = event.target.closest('[data-zola-preset]');
			if (presetButton) {
				setPreset(presetButton.dataset.zolaPreset);
				return;
			}

			const settingsButton = event.target.closest('[data-zola-settings]');
			if (settingsButton) {
				focusModuleSettings(settingsButton.dataset.zolaSettings);
				return;
			}

			const removeButton = event.target.closest('[data-zola-remove]');
			if (removeButton) {
				removeModule(layout, removeButton.dataset.zolaRemove, activeDevice);
				render();
				save();
				return;
			}

			const addButton = event.target.closest('[data-zola-add]');
			if (addButton) {
				const module = addButton.dataset.zolaAdd;
				removeModule(layout, module, activeDevice);
				layout[activeDevice].rows[activePosition.row][activePosition.column].push(module);
				render();
				save();
				return;
			}

			const slot = event.target.closest('[data-row][data-column]');
			if (slot) {
				activePosition = { row: slot.dataset.row, column: slot.dataset.column };
				render();
			}
		}

		function handleDragStart(event) {
			const item = event.target.closest('[data-zola-item]');
			if (item) {
				draggedModule = item.dataset.zolaItem;
				event.dataTransfer.effectAllowed = 'move';
				event.dataTransfer.setData('text/plain', draggedModule);
			}
		}

		function handleDragOver(event) {
			const slot = event.target.closest('[data-row][data-column]');
			if (slot) {
				event.preventDefault();
				slot.classList.add('is-drop-target');
			}
		}

		function handleDragLeave(event) {
			const slot = event.target.closest('[data-row][data-column]');
			if (slot && !slot.contains(event.relatedTarget)) {
				slot.classList.remove('is-drop-target');
			}
		}

		function handleDrop(event) {
			const slot = event.target.closest('[data-row][data-column]');
			if (!slot) {
				return;
			}
			event.preventDefault();
			slot.classList.remove('is-drop-target');
			const module = event.dataTransfer.getData('text/plain') || draggedModule;
			if (!moduleNames[module]) {
				return;
			}
			removeModule(layout, module, activeDevice);
			layout[activeDevice].rows[slot.dataset.row][slot.dataset.column].push(module);
			activePosition = { row: slot.dataset.row, column: slot.dataset.column };
			draggedModule = '';
			render();
			save();
		}

		const launchButton = makeButton(strings.openBuilder || 'Open Header Builder', 'button button-primary zola-header-builder__launch', { 'data-zola-open-builder': 'true' });
		const launchHelp = document.createElement('p');
		launchHelp.className = 'zola-header-builder__launch-help';
		launchHelp.textContent = strings.dragHint || 'Open the builder to arrange header elements across rows.';
		root.appendChild(launchButton);
		root.appendChild(launchHelp);
		launchButton.addEventListener('click', function (event) { openOverlay(event.currentTarget); });
		document.addEventListener('zola-open-header-builder', function (event) {
			openOverlay(event.detail ? event.detail.trigger : null);
		});

		control.setting.bind(function (value) {
			const nextLayout = normalizeLayout(value);
			if (JSON.stringify(nextLayout) !== JSON.stringify(layout)) {
				layout = nextLayout;
				render();
			}
		});
		document.addEventListener('zola-header-builder-device-change', function (event) {
			if (event.detail && ['desktop', 'mobile'].includes(event.detail.device) && activeDevice !== event.detail.device) {
				activeDevice = event.detail.device;
				if (overlay) {
					render();
				}
			}
		});
	}

	function updateBuilderAccent(value) {
		if (typeof value !== 'string') {
			return;
		}

		let hex = value.trim();
		if (/^#[\da-f]{3}$/i.test(hex)) {
			hex = '#' + hex.slice(1).split('').map(function (digit) { return digit + digit; }).join('');
		}
		if (!/^#[\da-f]{6}$/i.test(hex)) {
			return;
		}

		const channels = hex.slice(1).match(/[\da-f]{2}/gi).map(function (channel) {
			return parseInt(channel, 16) / 255;
		});
		const linearChannels = channels.map(function (channel) {
			return channel <= 0.04045 ? channel / 12.92 : Math.pow((channel + 0.055) / 1.055, 2.4);
		});
		const luminance = (0.2126 * linearChannels[0]) + (0.7152 * linearChannels[1]) + (0.0722 * linearChannels[2]);
		const root = document.documentElement;

		root.style.setProperty('--zola-builder-accent', hex);
		root.style.setProperty('--zola-builder-accent-rgb', channels.map(function (channel) {
			return Math.round(channel * 255);
		}).join(', '));
		root.style.setProperty('--zola-builder-accent-contrast', luminance > 0.179 ? '#1d2327' : '#fff');
	}

	wp.customize.bind('ready', function () {
		const accentSetting = wp.customize('zola_accent_color');
		if (accentSetting) {
			updateBuilderAccent(accentSetting.get());
			accentSetting.bind(updateBuilderAccent);
		}

		const presetsControl = wp.customize.control('zola_header_presets_control');
		if (presetsControl) {
			setupPresetsControl(presetsControl);
		}
		const control = wp.customize.control('zola_header_builder_layout');
		if (control) {
			setupControl(control);
		}
	});
}(window.wp));

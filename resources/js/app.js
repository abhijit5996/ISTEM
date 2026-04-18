import './bootstrap';

const docEl = document.documentElement;
const bodyEl = document.body;

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (bodyEl) {
	window.requestAnimationFrame(() => {
		bodyEl.classList.add('page-ready');
	});
}

const startPageLeaveTransition = (navigate) => {
	if (!bodyEl || prefersReducedMotion) {
		navigate();
		return;
	}

	if (bodyEl.classList.contains('page-leaving')) {
		return;
	}

	bodyEl.classList.add('page-leaving');
	window.setTimeout(() => {
		navigate();
	}, 180);
};

const applyTheme = (theme) => {
	if (theme === 'dark') {
		docEl.classList.add('dark');
	} else {
		docEl.classList.remove('dark');
	}
};

const savedTheme = localStorage.getItem('istem-theme');
if (savedTheme) {
	applyTheme(savedTheme);
}

document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
	toggle.addEventListener('click', () => {
		const isDark = docEl.classList.contains('dark');
		const next = isDark ? 'light' : 'dark';
		localStorage.setItem('istem-theme', next);
		applyTheme(next);
	});
});

document.querySelectorAll('[data-toast]').forEach((toast, index) => {
	toast.classList.add('fade-in-up');
	window.setTimeout(() => {
		toast.style.opacity = '0';
		toast.style.transform = 'translateY(-6px)';
		toast.style.transition = 'all 280ms ease';
		window.setTimeout(() => toast.remove(), 320);
	}, 3600 + index * 300);
});

const bellButton = document.querySelector('[data-notification-toggle]');
const notificationMenus = document.querySelectorAll('[data-notification-dropdown]');

if (bellButton && notificationMenus.length) {
	bellButton.addEventListener('click', () => {
		notificationMenus.forEach((menu) => menu.classList.toggle('hidden'));
	});

	document.addEventListener('click', (event) => {
		if (bellButton.contains(event.target)) {
			return;
		}

		notificationMenus.forEach((menu) => {
			if (!menu.contains(event.target)) {
				menu.classList.add('hidden');
			}
		});
	});
}

document.querySelectorAll('[data-open-modal]').forEach((trigger) => {
	trigger.addEventListener('click', () => {
		const templateId = trigger.getAttribute('data-open-modal');
		const template = document.getElementById(templateId);
		const modal = document.querySelector('[data-global-modal]');
		const modalContent = document.querySelector('[data-global-modal-content]');

		if (!template || !modal || !modalContent) {
			return;
		}

		modalContent.innerHTML = template.innerHTML;
		modal.classList.remove('hidden');
		modal.classList.add('flex');
		if (window.lucide) {
			window.lucide.createIcons();
		}
	});
});

const globalModal = document.querySelector('[data-global-modal]');
if (globalModal) {
	globalModal.addEventListener('click', (event) => {
		if (event.target === globalModal || event.target.closest('[data-close-modal]')) {
			globalModal.classList.add('hidden');
			globalModal.classList.remove('flex');
		}
	});
}

if (window.lucide) {
	window.lucide.createIcons();
}

const navMenuToggle = document.querySelector('[data-nav-menu-toggle]');
const navMobileMenu = document.querySelector('[data-nav-mobile-menu]');
const navSearchToggle = document.querySelector('[data-nav-search-toggle]');
const navMobileSearch = document.querySelector('[data-nav-mobile-search]');
const profileToggle = document.querySelector('[data-profile-menu-toggle]');
const profileDropdown = document.querySelector('[data-profile-dropdown]');
const profileWrap = document.querySelector('[data-profile-menu-wrap]');
const scrollTopButton = document.querySelector('[data-scroll-top]');
const mainHeader = document.querySelector('[data-main-header]');

if (navMenuToggle && navMobileMenu) {
	const closeMobileSearch = () => {
		if (!navMobileSearch || !navSearchToggle) {
			return;
		}
		navMobileSearch.classList.add('hidden');
		navSearchToggle.setAttribute('aria-expanded', 'false');
	};

	const closeNavMenu = () => {
		navMobileMenu.classList.add('hidden');
		navMenuToggle.setAttribute('aria-expanded', 'false');
	};

	const openNavMenu = () => {
		navMobileMenu.classList.remove('hidden');
		navMenuToggle.setAttribute('aria-expanded', 'true');
		closeMobileSearch();
	};

	navMenuToggle.addEventListener('click', () => {
		const isOpen = !navMobileMenu.classList.contains('hidden');
		if (isOpen) {
			closeNavMenu();
		} else {
			openNavMenu();
		}
	});

	navMobileMenu.querySelectorAll('a').forEach((link) => {
		link.addEventListener('click', () => {
			closeNavMenu();
		});
	});

	if (navSearchToggle && navMobileSearch) {
		const openMobileSearch = () => {
			navMobileSearch.classList.remove('hidden');
			navSearchToggle.setAttribute('aria-expanded', 'true');
			closeNavMenu();
		};

		navSearchToggle.addEventListener('click', () => {
			const isOpen = !navMobileSearch.classList.contains('hidden');
			if (isOpen) {
				closeMobileSearch();
			} else {
				openMobileSearch();
			}
		});
	}

	document.addEventListener('click', (event) => {
		if (
			navMenuToggle.contains(event.target) ||
			navMobileMenu.contains(event.target) ||
			(navSearchToggle && navSearchToggle.contains(event.target)) ||
			(navMobileSearch && navMobileSearch.contains(event.target))
		) {
			return;
		}

		closeNavMenu();
		closeMobileSearch();
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closeNavMenu();
			closeMobileSearch();
		}
	});
}

if (profileToggle && profileDropdown && profileWrap) {
	const closeProfileMenu = () => {
		profileDropdown.classList.add('hidden');
		profileDropdown.classList.remove('open');
	};

	const openProfileMenu = () => {
		profileDropdown.classList.remove('hidden');
		window.requestAnimationFrame(() => {
			profileDropdown.classList.add('open');
		});
	};

	profileToggle.addEventListener('click', () => {
		const isOpen = profileDropdown.classList.contains('open');
		if (isOpen) {
			closeProfileMenu();
		} else {
			openProfileMenu();
		}
	});

	document.addEventListener('click', (event) => {
		if (!profileWrap.contains(event.target)) {
			closeProfileMenu();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closeProfileMenu();
		}
	});
}

document.querySelectorAll('[data-filter-toggle]').forEach((toggle) => {
	const targetId = toggle.getAttribute('data-filter-target')
	if (!targetId) {
		return
	}

	const panel = document.getElementById(targetId)
	if (!panel) {
		return
	}

	toggle.addEventListener('click', () => {
		const isExpanded = toggle.getAttribute('aria-expanded') === 'true'
		panel.classList.toggle('hidden', isExpanded)
		toggle.setAttribute('aria-expanded', isExpanded ? 'false' : 'true')
	})
})

const handleScrolledUI = () => {
	const isScrolled = window.scrollY > 24;

	if (mainHeader) {
		mainHeader.classList.toggle('header-shrink', isScrolled);
	}

	if (scrollTopButton) {
		scrollTopButton.classList.toggle('hidden', !isScrolled);
	}
};

handleScrolledUI();
window.addEventListener('scroll', handleScrolledUI, { passive: true });

if (scrollTopButton) {
	scrollTopButton.addEventListener('click', () => {
		window.scrollTo({ top: 0, behavior: 'smooth' });
	});
}

const stepperRoot = document.querySelector('[data-stepper]');
const stepperForm = document.querySelector('[data-stepper-form]');

if (stepperRoot && stepperForm) {
	const panels = Array.from(stepperForm.querySelectorAll('[data-step-panel]'));
	const indicators = Array.from(stepperRoot.querySelectorAll('[data-step-indicator]'));
	let currentStep = 1;

	const renderStep = () => {
		panels.forEach((panel) => {
			const panelStep = Number(panel.getAttribute('data-step-panel'));
			panel.classList.toggle('hidden', panelStep !== currentStep);
		});

		indicators.forEach((indicator) => {
			const indicatorStep = Number(indicator.getAttribute('data-step-indicator'));
			indicator.classList.toggle('bg-slate-900', indicatorStep === currentStep);
			indicator.classList.toggle('text-white', indicatorStep === currentStep);
			indicator.classList.toggle('dark:bg-cyan-500', indicatorStep === currentStep);
			indicator.classList.toggle('dark:text-slate-950', indicatorStep === currentStep);
		});
	};

	stepperForm.querySelectorAll('[data-step-next]').forEach((button) => {
		button.addEventListener('click', () => {
			currentStep = Math.min(3, currentStep + 1);
			renderStep();
		});
	});

	stepperForm.querySelectorAll('[data-step-prev]').forEach((button) => {
		button.addEventListener('click', () => {
			currentStep = Math.max(1, currentStep - 1);
			renderStep();
		});
	});

	renderStep();
}

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''

const debounce = (fn, wait = 300) => {
	let timer = null
	return (...args) => {
		window.clearTimeout(timer)
		timer = window.setTimeout(() => fn(...args), wait)
	}
}

const ajaxFetch = async (url, options = {}) => {
	const response = await fetch(url, {
		credentials: 'same-origin',
		headers: {
			'X-Requested-With': 'XMLHttpRequest',
			'X-CSRF-TOKEN': csrfToken,
			Accept: 'application/json',
			...(options.headers || {}),
		},
		...options,
	})

	const contentType = response.headers.get('content-type') || ''
	const payload = contentType.includes('application/json') ? await response.json() : null

	if (!response.ok || (payload && payload.success === false)) {
		const message = payload?.message || `Request failed with status ${response.status}`
		throw new Error(message)
	}

	return payload
}

const showRuntimeToast = (message, type = 'success') => {
	const toast = document.createElement('div')
	toast.className = `toast ${type === 'error' ? 'toast-error' : 'toast-success'} fade-in-up fixed right-4 top-20 z-[95]`
	toast.textContent = message
	document.body.appendChild(toast)
	window.setTimeout(() => {
		toast.style.opacity = '0'
		toast.style.transform = 'translateY(-8px)'
		toast.style.transition = 'all 240ms ease'
		window.setTimeout(() => toast.remove(), 260)
	}, 2500)
}

const normalizeCurrencyToINR = (root = document) => {
	const selectors = [
		'.product-card-price-current',
		'.product-card-price-old',
		'.promo-card-price',
	]

	selectors.forEach((selector) => {
		root.querySelectorAll(selector).forEach((node) => {
			const text = (node.textContent || '').trim()
			if (!text.includes('$')) {
				return
			}

			const amount = Number.parseFloat(text.replace(/[^\d.]/g, ''))
			if (Number.isNaN(amount)) {
				node.textContent = text.replace(/\$/g, '₹')
				return
			}

			const inr = new Intl.NumberFormat('en-IN', {
				style: 'currency',
				currency: 'INR',
				minimumFractionDigits: 2,
				maximumFractionDigits: 2,
			}).format(amount)

			if (selector === '.promo-card-price') {
				node.textContent = `Start From ${inr}`
			} else {
				node.textContent = inr
			}
		})
	})
}

const updateCountBadge = (selector, value) => {
	const badge = document.querySelector(selector)
	if (!badge) {
		return
	}

	badge.textContent = String(value)
	badge.classList.toggle('hidden', !value)
	if (value) {
		badge.classList.add('inline-flex')
	}
}

const updateLocationDropdowns = (locations) => {
	document.querySelectorAll('[data-location-dynamic]').forEach((select) => {
		const currentValue = select.value
		const firstOption = select.querySelector('option')
		const firstLabel = firstOption?.textContent || 'All Locations'

		select.innerHTML = ''
		const allOption = document.createElement('option')
		allOption.value = ''
		allOption.textContent = firstLabel
		select.appendChild(allOption)

		locations.forEach((location) => {
			const option = document.createElement('option')
			option.value = location
			option.textContent = location
			if (location === currentValue) {
				option.selected = true
			}
			select.appendChild(option)
		})
	})
}

const renderNotifications = (items = []) => {
	const html = items.length
		? items
				.map(
					(item) => `
				<article class="rounded-xl border border-slate-200 p-3 text-sm dark:border-slate-700 ${item.read ? '' : 'bg-cyan-50/60 dark:bg-cyan-900/20'}">
					<p class="font-semibold">${item.title}</p>
					<p class="text-slate-500 dark:text-slate-400">${item.message}</p>
					<p class="mt-1 text-[11px] text-slate-400">${item.timestamp || ''}</p>
				</article>`,
				)
				.join('')
		: '<p class="rounded-xl border border-slate-200 p-3 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">No notifications.</p>'

	document.querySelectorAll('[data-notification-list]').forEach((node) => {
		node.innerHTML = html
	})
}

normalizeCurrencyToINR(document)

const refreshBootstrapData = async () => {
	try {
		const payload = await ajaxFetch('/ajax/bootstrap')
		const counts = payload?.data?.counts || {}
		updateCountBadge('[data-nav-bag-count]', counts.bag || 0)
		updateCountBadge('[data-nav-favorites-count]', counts.favorites || 0)
		updateCountBadge('[data-nav-notification-count]', counts.notifications || 0)

		if (payload?.data?.user) {
			const nameNode = document.querySelector('[data-profile-name]')
			const emailNode = document.querySelector('[data-profile-email]')
			if (nameNode) nameNode.textContent = payload.data.user.name || ''
			if (emailNode) emailNode.textContent = payload.data.user.email || ''
		}

		if (Array.isArray(payload?.data?.locations)) {
			updateLocationDropdowns(payload.data.locations)
		}
	} catch {
		// Keep SSR state if AJAX bootstrap fails.
	}
}

const refreshNotifications = async () => {
	try {
		const payload = await ajaxFetch('/ajax/notifications')
		const items = payload?.data?.items || []
		const unread = payload?.data?.unread || 0
		renderNotifications(items)
		updateCountBadge('[data-nav-notification-count]', unread)
	} catch {
		// Notification polling is best effort.
	}
}

const markNotificationsRead = async () => {
	try {
		await ajaxFetch('/ajax/notifications/mark-read', {
			method: 'POST',
		})
		updateCountBadge('[data-nav-notification-count]', 0)
		renderNotifications(
			Array.from(document.querySelectorAll('[data-notification-list] article')).map(() => ({
				read: true,
				title: 'Notification',
				message: 'Marked as read',
			})),
		)
	} catch (error) {
		showRuntimeToast(error.message, 'error')
	}
}

document.querySelectorAll('[data-notifications-mark-read]').forEach((button) => {
	button.addEventListener('click', markNotificationsRead)
})

const navbarSearchInput = document.querySelector('form[action$="/instruments"] input[name="search"]')
const navbarSuggestions = document.getElementById('navbar-search-suggestions')

if (navbarSearchInput && navbarSuggestions) {
	const updateSuggestions = debounce(async () => {
		const q = navbarSearchInput.value.trim()
		if (!q) {
			navbarSuggestions.innerHTML = ''
			return
		}

		try {
			const payload = await ajaxFetch(`/ajax/search-suggestions?q=${encodeURIComponent(q)}`)
			const suggestions = payload?.data?.suggestions || []
			navbarSuggestions.innerHTML = suggestions.map((item) => `<option value="${item}"></option>`).join('')
		} catch {
			// Ignore transient suggestion failures.
		}
	}, 300)

	navbarSearchInput.addEventListener('input', updateSuggestions)
}

document.addEventListener('submit', async (event) => {
	const logoutForm = event.target.closest('form[action$="/logout"]')
	if (logoutForm && logoutForm.action.includes('/logout') && !logoutForm.action.includes('/admin/logout')) {
		event.preventDefault()
		try {
			await ajaxFetch('/ajax/auth/logout', { method: 'POST' })
			showRuntimeToast('Logged out successfully.')
			window.location.assign('/')
		} catch (error) {
			showRuntimeToast(error.message, 'error')
		}
		return
	}

	const authForm = event.target.closest('[data-ajax-auth]')
	if (authForm) {
		event.preventDefault()
		const formData = new FormData(authForm)
		const mode = authForm.getAttribute('data-ajax-auth')

		try {
			const endpoint = mode === 'signup' ? '/ajax/auth/signup' : '/ajax/auth/login'
			const payload = await ajaxFetch(endpoint, {
				method: 'POST',
				body: formData,
			})

			if (mode === 'signup') {
				showRuntimeToast(payload?.message || 'Signup complete. Verify OTP to continue.')
				window.location.assign('/verify-otp')
			} else {
				if (payload?.data?.token) {
					localStorage.setItem('istem-api-token', payload.data.token)
				}
				showRuntimeToast(payload?.message || 'Login successful.')
				window.location.assign('/')
			}
		} catch (error) {
			showRuntimeToast(error.message, 'error')
		}
		return
	}

	const bookingForm = event.target.closest('[data-ajax-booking-submit]')
	if (bookingForm) {
		event.preventDefault()
		const formData = new FormData(bookingForm)
		try {
			const payload = await ajaxFetch('/ajax/booking/submit', {
				method: 'POST',
				body: formData,
			})
			showRuntimeToast(payload?.message || 'Booking submitted.')
			window.location.assign('/my-bookings')
		} catch (error) {
			showRuntimeToast(error.message, 'error')
		}
		return
	}

	const addBagForm = event.target.closest('[data-ajax-bag-add]')
	if (addBagForm) {
		event.preventDefault()
		const id = addBagForm.getAttribute('data-ajax-bag-add')
		const formData = new FormData(addBagForm)
		try {
			await ajaxFetch(`/ajax/bag/add/${id}`, { method: 'POST', body: formData })
			await refreshBootstrapData()
			showRuntimeToast('Added to bag.')
		} catch (error) {
			showRuntimeToast(error.message, 'error')
		}
		return
	}

	const removeBagForm = event.target.closest('[data-ajax-bag-remove]')
	if (removeBagForm) {
		event.preventDefault()
		const id = removeBagForm.getAttribute('data-ajax-bag-remove')
		try {
			await ajaxFetch(`/ajax/bag/remove/${id}`, { method: 'POST' })
			removeBagForm.closest('tr')?.remove()
			await refreshBootstrapData()
			showRuntimeToast('Removed from bag.')
		} catch (error) {
			showRuntimeToast(error.message, 'error')
		}
		return
	}

	const favoriteForm = event.target.closest('[data-ajax-favorite]')
	if (favoriteForm) {
		event.preventDefault()
		const id = favoriteForm.getAttribute('data-ajax-favorite')
		try {
			const payload = await ajaxFetch(`/ajax/favorites/toggle/${id}`, { method: 'POST' })
			await refreshBootstrapData()
			if (favoriteForm.closest('.panel') && window.location.pathname.includes('/favorites') && payload?.data?.favorite === false) {
				favoriteForm.closest('.panel')?.remove()
			}
			showRuntimeToast(payload?.message || 'Favorites updated.')
		} catch (error) {
			showRuntimeToast(error.message, 'error')
		}
		return
	}

	const queueForm = event.target.closest('[data-ajax-queue-join]')
	if (queueForm) {
		event.preventDefault()
		const id = queueForm.getAttribute('data-ajax-queue-join')
		const formData = new FormData(queueForm)
		try {
			await ajaxFetch(`/ajax/queue/join/${id}`, { method: 'POST', body: formData })
			showRuntimeToast('Joined queue successfully.')
		} catch (error) {
			showRuntimeToast(error.message, 'error')
		}
		return
	}
})

document.querySelectorAll('[data-ajax-bag-add], [data-ajax-queue-join]').forEach((form) => {
	const instrumentId = form.getAttribute('data-ajax-bag-add') || form.getAttribute('data-ajax-queue-join')
	const startInput = form.querySelector('input[name="start_date"]')
	const endInput = form.querySelector('input[name="end_date"]')

	if (!instrumentId || !startInput || !endInput) {
		return
	}

	const feedback = document.createElement('p')
	feedback.className = 'text-xs text-slate-500 dark:text-slate-400'
	feedback.textContent = 'Choose dates to check availability.'
	form.appendChild(feedback)

	const checkAvailability = debounce(async () => {
		if (!startInput.value || !endInput.value) {
			feedback.textContent = 'Choose dates to check availability.'
			feedback.className = 'text-xs text-slate-500 dark:text-slate-400'
			return
		}

		try {
			const payload = await ajaxFetch(`/ajax/instruments/${instrumentId}/availability?start_date=${encodeURIComponent(startInput.value)}&end_date=${encodeURIComponent(endInput.value)}`)
			const available = payload?.data?.available
			feedback.textContent = available ? 'Slot available for selected range.' : 'Selected range has booking conflicts.'
			feedback.className = available
				? 'text-xs text-emerald-600 dark:text-emerald-300'
				: 'text-xs text-rose-600 dark:text-rose-300'
		} catch {
			feedback.textContent = 'Unable to verify availability right now.'
			feedback.className = 'text-xs text-amber-600 dark:text-amber-300'
		}
	}, 300)

	startInput.addEventListener('change', checkAvailability)
	endInput.addEventListener('change', checkAvailability)
})

const listingForm = document.querySelector('[data-ajax-listing-form]')
const listingGrid = document.querySelector('[data-ajax-listing-grid]')
const listingSummary = document.querySelector('[data-listing-summary]')
const listingPagination = document.querySelector('[data-ajax-listing-pagination]')
const listingLoading = document.querySelector('[data-ajax-listing-loading]')
const listingSort = document.querySelector('[data-ajax-listing-sort]')

if (listingForm && listingGrid && listingSummary && listingPagination) {
	const applyListingFromHtml = (htmlText) => {
		const parser = new DOMParser()
		const doc = parser.parseFromString(htmlText, 'text/html')
		const newGrid = doc.querySelector('[data-ajax-listing-grid]')
		const newSummary = doc.querySelector('[data-listing-summary]')
		const newPagination = doc.querySelector('[data-ajax-listing-pagination]')

		if (newGrid) {
			listingGrid.innerHTML = newGrid.innerHTML
			normalizeCurrencyToINR(listingGrid)
		}
		if (newSummary) {
			listingSummary.textContent = newSummary.textContent
		}
		if (newPagination) {
			listingPagination.innerHTML = newPagination.innerHTML
		}

		if (window.lucide) {
			window.lucide.createIcons()
		}

		normalizeCurrencyToINR(document)
	}

	const fetchListing = async (params) => {
		if (listingLoading) {
			listingLoading.classList.remove('hidden')
			listingLoading.classList.add('flex')
		}

		const url = `${window.location.pathname}?${params.toString()}`
		const response = await fetch(url, {
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
			},
			credentials: 'same-origin',
		})
		const htmlText = await response.text()
		applyListingFromHtml(htmlText)
		window.history.replaceState({}, '', url)

		if (listingLoading) {
			listingLoading.classList.add('hidden')
			listingLoading.classList.remove('flex')
		}
	}

	const submitListing = debounce(async () => {
		const params = new URLSearchParams(new FormData(listingForm))
		if (listingSort?.value) {
			params.set('sort', listingSort.value)
		}
		await fetchListing(params)
	}, 300)

	listingForm.addEventListener('submit', (event) => {
		event.preventDefault()
		submitListing()
	})

	listingForm.addEventListener('change', submitListing)
	listingForm.querySelector('input[name="search"]')?.addEventListener('input', submitListing)
	listingSort?.addEventListener('change', submitListing)

	listingPagination.addEventListener('click', async (event) => {
		const anchor = event.target.closest('a')
		if (!anchor || !anchor.href) {
			return
		}

		event.preventDefault()
		const url = new URL(anchor.href)
		await fetchListing(url.searchParams)
	})
}

window.setInterval(refreshNotifications, 8000)
window.setInterval(refreshBootstrapData, 10000)

refreshBootstrapData()
refreshNotifications()

document.querySelectorAll('a[href]').forEach((anchor) => {
	anchor.addEventListener('click', (event) => {
		const href = anchor.getAttribute('href');
		if (!href || href.startsWith('#')) {
			return;
		}

		if (
			anchor.hasAttribute('download') ||
			anchor.getAttribute('target') === '_blank' ||
			event.defaultPrevented ||
			event.metaKey ||
			event.ctrlKey ||
			event.shiftKey ||
			event.altKey
		) {
			return;
		}

		const url = new URL(anchor.href, window.location.origin);
		if (url.origin !== window.location.origin) {
			return;
		}

		event.preventDefault();
		startPageLeaveTransition(() => {
			window.location.assign(url.toString());
		});
	});
});

document.querySelectorAll('form').forEach((form) => {
	form.addEventListener('submit', () => {
		if (prefersReducedMotion || !bodyEl) {
			return;
		}

		bodyEl.classList.add('page-leaving');
	});
});

const todayIso = new Date().toISOString().slice(0, 10);

document.querySelectorAll('form').forEach((form) => {
	const startDateInput = form.querySelector('input[name="start_date"][type="date"]');
	const endDateInput = form.querySelector('input[name="end_date"][type="date"]');

	if (!startDateInput || !endDateInput) {
		return;
	}

	const syncDateBounds = () => {
		const startDate = startDateInput.value || todayIso;
		const effectiveMin = startDate > todayIso ? startDate : todayIso;
		endDateInput.min = effectiveMin;

		if (endDateInput.value && endDateInput.value < effectiveMin) {
			endDateInput.value = effectiveMin;
		}
	};

	startDateInput.min = todayIso;
	if (!startDateInput.value || startDateInput.value < todayIso) {
		startDateInput.value = todayIso;
	}

	syncDateBounds();
	startDateInput.addEventListener('change', syncDateBounds);
});

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
const profileToggle = document.querySelector('[data-profile-menu-toggle]');
const profileDropdown = document.querySelector('[data-profile-dropdown]');
const profileWrap = document.querySelector('[data-profile-menu-wrap]');
const scrollTopButton = document.querySelector('[data-scroll-top]');
const mainHeader = document.querySelector('[data-main-header]');

if (navMenuToggle && navMobileMenu) {
	const closeNavMenu = () => {
		navMobileMenu.classList.add('hidden');
		navMenuToggle.setAttribute('aria-expanded', 'false');
	};

	const openNavMenu = () => {
		navMobileMenu.classList.remove('hidden');
		navMenuToggle.setAttribute('aria-expanded', 'true');
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

	document.addEventListener('click', (event) => {
		if (navMenuToggle.contains(event.target) || navMobileMenu.contains(event.target)) {
			return;
		}

		closeNavMenu();
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closeNavMenu();
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

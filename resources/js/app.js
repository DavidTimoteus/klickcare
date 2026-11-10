import './bootstrap';

/**
 * KlickCare - Interactive Precision UI System
 * Zero AI-Slop: High-performance micro-interactions & clinical UX
 */

document.addEventListener('DOMContentLoaded', () => {
    initAnimatedCounters();
    initPoliFilterTabs();
    initClinicOperatingStatus();
    initNavbarEnhancements();
    initScrollSpy();
});

/**
 * 1. Animated Count-Up on Scroll
 * Smoothly animates numerical operational metrics when scrolled into view.
 */
function initAnimatedCounters() {
    const counterElements = document.querySelectorAll('[data-counter-value]');
    if (!counterElements.length) return;

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const targetValue = parseInt(el.getAttribute('data-counter-value'), 10) || 0;
                animateNumber(el, 0, targetValue, 1100);
                obs.unobserve(el);
            }
        });
    }, { threshold: 0.2 });

    counterElements.forEach(el => observer.observe(el));
}

function animateNumber(element, start, end, duration) {
    if (end === 0) {
        element.textContent = '0';
        return;
    }
    const startTime = performance.now();

    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // Ease-out cubic
        const easeOut = 1 - Math.pow(1 - progress, 3);
        const currentVal = Math.round(start + (end - start) * easeOut);
        element.textContent = currentVal.toLocaleString('id-ID');

        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = end.toLocaleString('id-ID');
        }
    }

    requestAnimationFrame(updateCounter);
}

/**
 * 2. Interactive Poli Category Tabs (Silky-Smooth Cross-Fade Transition)
 * Native Bootstrap Pill navigation enhanced with hardware-accelerated cubic-bezier transitions.
 */
function initPoliFilterTabs() {
    const tabs = document.querySelectorAll('#poliFilterTabs button[data-bs-toggle="pill"]');
    if (!tabs.length) return;

    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', (event) => {
            const targetSelector = event.target.getAttribute('data-bs-target');
            const targetPane = targetSelector ? document.querySelector(targetSelector) : null;
            if (targetPane) {
                targetPane.style.opacity = '0';
                targetPane.style.transform = 'translateY(8px) scale(0.995)';
                requestAnimationFrame(() => {
                    targetPane.style.transition = 'opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1)';
                    targetPane.style.opacity = '1';
                    targetPane.style.transform = 'translateY(0) scale(1)';
                });
            }
        });
    });
}

/**
 * 3. Dynamic Clinic Operating Hours Status
 * Evaluates real-time clock to show live status badge.
 */
function initClinicOperatingStatus() {
    const statusTextEl = document.getElementById('kcLiveStatusText');
    const pulseDot = document.querySelector('.kc-status-badge .kc-pulse-dot');
    if (!statusTextEl) return;

    const now = new Date();
    // Convert to WIB (UTC+7)
    const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
    const wib = new Date(utc + (3600000 * 7));

    const day = wib.getDay(); // 0 = Sun, 5 = Fri, 6 = Sat
    const hour = wib.getHours();
    const minute = wib.getMinutes();
    const timeInMins = hour * 60 + minute;

    let isOpen = false;
    let message = 'Poliklinik Buka Hari Ini · 08:00 - 15:30 WIB';

    if (day >= 1 && day <= 4) { // Sen - Kam
        if (timeInMins >= 8 * 60 && timeInMins <= 15 * 60 + 30) {
            isOpen = true;
            message = 'Poliklinik Sedang Melayani Pasien · Buka s/d 15:30 WIB';
        } else {
            message = 'Pelayanan Offline Tutup · Pendaftaran Antrian Online Buka 24 Jam';
        }
    } else if (day === 5) { // Jum
        if ((timeInMins >= 8 * 60 && timeInMins <= 11 * 60 + 30) || (timeInMins >= 13 * 60 && timeInMins <= 15 * 60)) {
            isOpen = true;
            message = 'Poliklinik Sedang Melayani Pasien · Buka s/d 15:00 WIB';
        } else {
            message = 'Pelayanan Offline Tutup · Pendaftaran Antrian Online Buka 24 Jam';
        }
    } else {
        message = 'Pelayanan Offline Libur · Pendaftaran Antrian Online Buka 24 Jam';
    }

    statusTextEl.textContent = message;
    if (pulseDot) {
        pulseDot.style.backgroundColor = isOpen ? '#10b981' : '#f59e0b';
    }
}

/**
 * 4. Navbar Scroll Glassmorphism & Auto-Collapse on Mobile
 */
function initNavbarEnhancements() {
    const navbar = document.querySelector('.kc-navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('kc-navbar-scrolled');
            } else {
                navbar.classList.remove('kc-navbar-scrolled');
            }
        });
    }

    // Auto-collapse mobile navbar menu when clicking internal anchor links
    const navLinks = document.querySelectorAll('#kcNavbarContent .kc-nav-link');
    const navCollapse = document.getElementById('kcNavbarContent');
    if (navCollapse && typeof bootstrap !== 'undefined') {
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992 && navCollapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
            });
        });
    }
}

/**
 * 5. Active ScrollSpy for Homepage Sections
 * Automatically synchronizes active navbar link with current viewport section.
 */
function initScrollSpy() {
    const layananSection = document.getElementById('layanan-poli');
    const alurSection = document.getElementById('alur-periksa');
    const navBeranda = document.getElementById('navBeranda');
    const navLayanan = document.getElementById('navLayananPoli');
    const navAlur = document.getElementById('navAlurPeriksa');

    if (!layananSection || !alurSection || !navBeranda || !navLayanan || !navAlur) return;

    function onScroll() {
        const scrollPos = window.scrollY + 120; // 120px offset for sticky navbar
        const layananTop = layananSection.offsetTop;
        const alurTop = alurSection.offsetTop;

        if (scrollPos >= alurTop) {
            navBeranda.classList.remove('active');
            navLayanan.classList.remove('active');
            navAlur.classList.add('active');
        } else if (scrollPos >= layananTop) {
            navBeranda.classList.remove('active');
            navLayanan.classList.add('active');
            navAlur.classList.remove('active');
        } else {
            navBeranda.classList.add('active');
            navLayanan.classList.remove('active');
            navAlur.classList.remove('active');
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

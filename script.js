/**
 * script.js — Website Desa Sejahtera
 * Handles: Navbar scroll, mobile menu, active nav, back-to-top, form submit, smooth scroll
 */

(function () {
  'use strict';

  /* ─── Selectors ─────────────────────────────────────────────── */
  const header      = document.getElementById('header');
  const hamburger   = document.getElementById('hamburger');
  const navMenu     = document.getElementById('navMenu');
  const navLinks    = document.querySelectorAll('.nav-link');
  const backToTop   = document.getElementById('backToTop');
  const kontakForm  = document.getElementById('kontakForm');

  /* ─── Navbar: scroll shadow & sticky ────────────────────────── */
  function onScroll() {
    // Add shadow on scroll
    if (window.scrollY > 10) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }

    // Back-to-top visibility
    if (window.scrollY > 400) {
      backToTop.classList.add('visible');
    } else {
      backToTop.classList.remove('visible');
    }

    // Active nav link based on scroll position
    updateActiveNav();
  }

  window.addEventListener('scroll', onScroll, { passive: true });

  /* ─── Active Nav on Scroll ───────────────────────────────────── */
  function updateActiveNav() {
    const sections = document.querySelectorAll('section[id]');
    let current = '';

    sections.forEach(section => {
      const sectionTop    = section.offsetTop - 100;
      const sectionHeight = section.offsetHeight;
      if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
        current = section.getAttribute('id');
      }
    });

    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href') === `#${current}`) {
        link.classList.add('active');
      }
    });
  }

  /* ─── Mobile Menu Toggle ─────────────────────────────────────── */
  hamburger.addEventListener('click', function () {
    const isOpen = navMenu.classList.toggle('open');
    hamburger.classList.toggle('open', isOpen);
    hamburger.setAttribute('aria-expanded', String(isOpen));
    document.body.style.overflow = isOpen ? 'hidden' : '';
  });

  // Close menu when a link is clicked
  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      navMenu.classList.remove('open');
      hamburger.classList.remove('open');
      hamburger.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    });
  });

  // Close menu on outside click
  document.addEventListener('click', function (e) {
    if (!header.contains(e.target) && navMenu.classList.contains('open')) {
      navMenu.classList.remove('open');
      hamburger.classList.remove('open');
      hamburger.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }
  });

  /* ─── Back to Top ────────────────────────────────────────────── */
  backToTop.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  /* ─── Smooth Scroll for Anchor Links ────────────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href === '#') return;
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        const headerHeight = header.offsetHeight;
        const targetTop    = target.getBoundingClientRect().top + window.scrollY - headerHeight - 10;
        window.scrollTo({ top: targetTop, behavior: 'smooth' });
      }
    });
  });

  /* ─── Contact Form Submit ─────────────────────────────────────── */
  if (kontakForm) {
    kontakForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const nama     = document.getElementById('nama').value.trim();
      const email    = document.getElementById('email').value.trim();

      if (!nama || !email) {
        showToast('Nama dan email wajib diisi.', 'error');
        return;
      }

      if (!isValidEmail(email)) {
        showToast('Format email tidak valid.', 'error');
        return;
      }

      // Simulate sending (replace with actual API call)
      const submitBtn = kontakForm.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

      setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Pesan';
        kontakForm.reset();
        showToast('Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.', 'success');
      }, 1800);
    });
  }

  /* ─── Helper: Validate Email ─────────────────────────────────── */
  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  /* ─── Toast Notification ─────────────────────────────────────── */
  function showToast(message, type = 'default') {
    // Remove existing toast
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast${type === 'success' ? ' success' : ''}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
      requestAnimationFrame(() => toast.classList.add('show'));
    });

    // Auto remove after 4s
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 400);
    }, 4000);
  }

  /* ─── Intersection Observer: Fade-in Sections ────────────────── */
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('in-view');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  // Add fade-in-up animation to cards
  document.querySelectorAll(
    '.profil-card, .layanan-card, .berita-card, .galeri-item, .kontak-info-card, .kontak-form-wrapper'
  ).forEach((el, i) => {
    el.style.opacity    = '0';
    el.style.transform  = 'translateY(30px)';
    el.style.transition = `opacity 0.6s ease ${i * 0.07}s, transform 0.6s ease ${i * 0.07}s`;
    observer.observe(el);
  });

  // When in view, reset styles
  const viewObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity   = '1';
        entry.target.style.transform = 'translateY(0)';
        viewObserver.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll(
    '.profil-card, .layanan-card, .berita-card, .galeri-item, .kontak-info-card, .kontak-form-wrapper'
  ).forEach(el => viewObserver.observe(el));

  /* ─── Map Placeholder Click ──────────────────────────────────── */
  const mapPlaceholder = document.querySelector('.map-placeholder');
  if (mapPlaceholder) {
    mapPlaceholder.addEventListener('click', () => {
      window.open('https://maps.google.com/?q=Desa+Sejahtera+Maju+Jaya', '_blank', 'noopener,noreferrer');
    });
    mapPlaceholder.setAttribute('role', 'button');
    mapPlaceholder.setAttribute('tabindex', '0');
    mapPlaceholder.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') mapPlaceholder.click();
    });
  }

  /* ─── Init ───────────────────────────────────────────────────── */
  onScroll(); // Run once on load to set initial state

})();

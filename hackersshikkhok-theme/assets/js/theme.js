/**
 * HackersShikkhok Theme Core UI Engine (v4.1.0)
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * Mobile Navigation Drawer, Bottom 5-Tab Bar, Scroll-to-Top, Interactive Components
 */
(function () {
  'use strict';

  function initMobileDrawer() {
    const toggleBtn = document.getElementById('hs-mobile-menu-btn');
    const drawer = document.getElementById('hs-mobile-drawer');
    const overlay = document.getElementById('hs-drawer-overlay');
    const closeBtn = document.getElementById('hs-drawer-close-btn');

    if (!toggleBtn || !drawer || !overlay) {
      return;
    }

    function openDrawer() {
      drawer.classList.add('hs-drawer-open');
      overlay.classList.add('hs-overlay-active');
      toggleBtn.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
      drawer.classList.remove('hs-drawer-open');
      overlay.classList.remove('hs-overlay-active');
      toggleBtn.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }

    toggleBtn.addEventListener('click', function (e) {
      e.preventDefault();
      if (drawer.classList.contains('hs-drawer-open')) {
        closeDrawer();
      } else {
        openDrawer();
      }
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', function (e) {
        e.preventDefault();
        closeDrawer();
      });
    }

    overlay.addEventListener('click', function (e) {
      e.preventDefault();
      closeDrawer();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && drawer.classList.contains('hs-drawer-open')) {
        closeDrawer();
      }
    });
  }

  function initScrollTop() {
    const scrollBtn = document.querySelector('[data-hs-scroll-top]');
    if (scrollBtn) {
      window.addEventListener('scroll', function () {
        if (window.scrollY > 300) {
          scrollBtn.style.opacity = '1';
          scrollBtn.style.pointerEvents = 'auto';
        } else {
          scrollBtn.style.opacity = '0';
          scrollBtn.style.pointerEvents = 'none';
        }
      });

      scrollBtn.addEventListener('click', function (e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }
  }

  function initBottomTabBar() {
    const currentPath = window.location.pathname.replace(/\/$/, '') || '/';
    const tabItems = document.querySelectorAll('.hs-bottom-5tab-bar .hs-tab-item');
    tabItems.forEach(function (tab) {
      const href = tab.getAttribute('href');
      if (href) {
        try {
          const tabPath = new URL(href, window.location.origin).pathname.replace(/\/$/, '') || '/';
          if (tabPath === currentPath) {
            tab.classList.add('hs-tab-active');
          }
        } catch (e) {}
      }
    });
  }

  function initToolsFilter() {
    const searchInput = document.getElementById('hs-tool-live-search');
    const toolCards = document.querySelectorAll('.hs-tools-grid .hs-tool-box');
    const countBadge = document.getElementById('hs-tool-search-count');
    const filterChips = document.querySelectorAll('.hs-filter-chip');
    const centerChips = document.querySelectorAll('.hs-center-chip');

    if (!searchInput && toolCards.length === 0) {
      return;
    }

    let activeCategory = 'all';
    let activeCenter = 'all';
    let searchQuery = '';

    function filterTools() {
      let visibleCount = 0;
      toolCards.forEach(function (card) {
        const name = (card.getAttribute('data-tool-name') || '').toLowerCase();
        const desc = (card.getAttribute('data-tool-desc') || '').toLowerCase();
        const cat = card.getAttribute('data-tool-cat') || '';
        const center = card.getAttribute('data-tool-center') || '';

        const matchesSearch = !searchQuery || name.includes(searchQuery) || desc.includes(searchQuery);
        const matchesCategory = activeCategory === 'all' || cat.toLowerCase().includes(activeCategory.toLowerCase());
        const matchesCenter = activeCenter === 'all' || center === activeCenter;

        if (matchesSearch && matchesCategory && matchesCenter) {
          card.style.display = '';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      if (countBadge) {
        countBadge.textContent = visibleCount + ' Tools Found';
      }
    }

    if (searchInput) {
      searchInput.addEventListener('input', function (e) {
        searchQuery = e.target.value.toLowerCase().trim();
        filterTools();
      });
    }

    filterChips.forEach(function (chip) {
      chip.addEventListener('click', function () {
        filterChips.forEach(function (c) { c.classList.remove('hs-filter-active'); });
        chip.classList.add('hs-filter-active');
        activeCategory = chip.getAttribute('data-cat') || 'all';
        filterTools();
      });
    });

    centerChips.forEach(function (chip) {
      chip.addEventListener('click', function () {
        const selected = chip.getAttribute('data-center') || 'all';
        if (activeCenter === selected) {
          activeCenter = 'all';
          chip.style.opacity = '1';
        } else {
          centerChips.forEach(function (c) { c.style.opacity = '0.6'; });
          activeCenter = selected;
          chip.style.opacity = '1';
        }
        filterTools();
      });
    });
  }

  function initAuthModal() {
    const backdrop = document.getElementById('hs-auth-modal-backdrop');
    if (!backdrop) return;

    // Attach to all .hs-auth-trigger buttons (e.g., mobile nav or inline links)
    document.querySelectorAll('.hs-auth-trigger').forEach(function (btn) {
      if (btn.getAttribute('onclick')) return; // Avoid double trigger when inline handler exists
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        const action = btn.getAttribute('data-auth-action') || 'login';
        if (typeof window.openHsAuthModal === 'function') {
          window.openHsAuthModal(action);
        }
      });
    });

    backdrop.addEventListener('click', function (e) {
      if (e.target === backdrop && typeof window.closeHsAuthModal === 'function') {
        window.closeHsAuthModal();
      }
    });

    // Check URL query param ?auth=login or ?auth=register
    const urlParams = new URLSearchParams(window.location.search);
    const authParam = urlParams.get('auth');
    if ((authParam === 'login' || authParam === 'register') && typeof window.openHsAuthModal === 'function') {
      window.openHsAuthModal(authParam);
    }
  }

  function initLiveWalletSync() {
    const savedBdt = localStorage.getItem('hs_user_bdt');
    const headerBdt = document.getElementById('hs-header-bdt');
    if (savedBdt && headerBdt) {
      headerBdt.textContent = 'BDT ' + parseFloat(savedBdt).toFixed(2);
    }
  }

  function initNotificationDropdown() {
    const trigger = document.getElementById('hs-notify-trigger');
    const dropdown = document.getElementById('hs-notify-dropdown');
    if (!trigger || !dropdown) return;

    trigger.addEventListener('click', function (e) {
      e.stopPropagation();
      const isVisible = dropdown.style.display === 'block';
      dropdown.style.display = isVisible ? 'none' : 'block';
    });

    document.addEventListener('click', function (e) {
      if (dropdown && !dropdown.contains(e.target) && e.target !== trigger) {
        dropdown.style.display = 'none';
      }
    });
  }

  function initLanguageSwitcherGlobal() {
    const langButtons = document.querySelectorAll('.hs-lang-btn');
    if (!langButtons.length) return;

    const savedLang = localStorage.getItem('hs_user_lang') || 'en';

    function setLanguage(lang) {
      localStorage.setItem('hs_user_lang', lang);
      document.cookie = 'hs_lang=' + lang + ';path=/;max-age=' + (60 * 60 * 24 * 365);

      langButtons.forEach(function (btn) {
        const bl = btn.getAttribute('data-lang');
        if (bl === lang) {
          btn.classList.add('is-active');
        } else {
          btn.classList.remove('is-active');
        }
      });

      if (window.HS_I18N && typeof window.HS_I18N.setLanguage === 'function') {
        window.HS_I18N.setLanguage(lang);
      }
    }

    setLanguage(savedLang);

    langButtons.forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        const target = btn.getAttribute('data-lang');
        if (target) {
          setLanguage(target);
        }
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initMobileDrawer();
    initScrollTop();
    initBottomTabBar();
    initToolsFilter();
    initAuthModal();
    initLiveWalletSync();
    initNotificationDropdown();
    initLanguageSwitcherGlobal();
    initLikeHandler();
  });

  function initLikeHandler() {
    var ajaxUrl = (typeof window.hsThemeData !== 'undefined' && window.hsThemeData.ajax_url) ? window.hsThemeData.ajax_url : '/wp-admin/admin-ajax.php';
    var nonce = (typeof window.hsThemeData !== 'undefined' && window.hsThemeData.ajax_nonce) ? window.hsThemeData.ajax_nonce : '';

    document.querySelectorAll('.hs-like-btn').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        var postId = this.dataset.postId;
        if (!postId) return;
        var formData = new FormData();
        formData.append('action', 'hs_like_post');
        formData.append('post_id', postId);
        formData.append('nonce', nonce);

        fetch(ajaxUrl, {
          method: 'POST',
          body: formData
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data && data.success && data.data) {
            var countEl = btn.querySelector('.hs-likes-count');
            if (countEl) countEl.textContent = data.data.likes;
            if (typeof data.data.is_liked !== 'undefined') {
              btn.classList.toggle('is-liked', data.data.is_liked);
            } else {
              btn.classList.add('is-liked');
            }
            var statEl = document.getElementById('hs-likes-stat');
            if (statEl) statEl.textContent = data.data.likes;
          }
        })
        .catch(function () {});
      });
    });
  }
})();



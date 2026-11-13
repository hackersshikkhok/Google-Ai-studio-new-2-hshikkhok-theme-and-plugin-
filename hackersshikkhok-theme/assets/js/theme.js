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
    const closeBtn = document.getElementById('hs-auth-modal-close');
    const tabs = document.querySelectorAll('.hs-auth-tab');
    const loginForm = document.getElementById('hs-login-form');
    const regForm = document.getElementById('hs-register-form');
    const guestTab = document.getElementById('hs-guest-tab');
    const alertBox = document.getElementById('hs-auth-alert');
    const regSubmitBtn = document.getElementById('hs-reg-submit');
    const guestBtn = document.getElementById('hs-guest-activate-btn');

    if (!backdrop) return;

    function openModal(action) {
      backdrop.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      switchTab(action || 'login');
    }

    function closeModal() {
      backdrop.style.display = 'none';
      document.body.style.overflow = '';
      if (alertBox) alertBox.style.display = 'none';
    }

    function showAlert(msg, isError) {
      if (!alertBox) return;
      alertBox.textContent = msg;
      alertBox.style.display = 'block';
      alertBox.style.background = isError ? 'rgba(239,68,68,0.15)' : 'rgba(16,185,129,0.15)';
      alertBox.style.color = isError ? '#f87171' : '#10b981';
      alertBox.style.border = '1px solid ' + (isError ? 'rgba(239,68,68,0.3)' : 'rgba(16,185,129,0.3)');
    }

    function switchTab(tabKey) {
      tabs.forEach(function (t) {
        if (t.getAttribute('data-tab') === tabKey) {
          t.style.borderBottom = '2px solid #00f5d4';
          t.style.color = '#00f5d4';
        } else {
          t.style.borderBottom = '2px solid transparent';
          t.style.color = '#94a3b8';
        }
      });

      if (loginForm) loginForm.style.display = tabKey === 'login' ? 'block' : 'none';
      if (regForm) regForm.style.display = tabKey === 'register' ? 'block' : 'none';
      if (guestTab) guestTab.style.display = tabKey === 'guest' ? 'block' : 'none';
      if (alertBox) alertBox.style.display = 'none';
    }

    // Attach to all auth trigger buttons across page
    document.querySelectorAll('.hs-auth-trigger').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        const action = btn.getAttribute('data-auth-action') || 'login';
        openModal(action);
      });
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', closeModal);
    }

    backdrop.addEventListener('click', function (e) {
      if (e.target === backdrop) closeModal();
    });

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        switchTab(tab.getAttribute('data-tab'));
      });
    });

    // Check URL query param ?auth=login or ?auth=register
    const urlParams = new URLSearchParams(window.location.search);
    const authParam = urlParams.get('auth');
    if (authParam === 'login' || authParam === 'register') {
      openModal(authParam);
    }

    // Register AJAX submission
    if (regSubmitBtn) {
      regSubmitBtn.addEventListener('click', function () {
        const u = (document.getElementById('hs-reg-user') || {}).value || '';
        const em = (document.getElementById('hs-reg-email') || {}).value || '';
        const p = (document.getElementById('hs-reg-pass') || {}).value || '';

        if (!u || !em || !p) {
          showAlert('Please fill in username, email, and password.', true);
          return;
        }

        regSubmitBtn.disabled = true;
        regSubmitBtn.textContent = 'Creating Operative Account...';

        const apiEndpoint = (window.hsThemeConfig && window.hsThemeConfig.apiRoot)
          ? window.hsThemeConfig.apiRoot + 'auth/register'
          : '/wp-json/hackersshikkhok/v1/auth/register';

        fetch(apiEndpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': (window.hsThemeConfig && window.hsThemeConfig.nonce) || ''
          },
          body: JSON.stringify({ username: u, email: em, password: p })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data.success) {
            showAlert('✓ Account created! Redirecting to operative vault...', false);
            localStorage.setItem('hs_user_name', u);
            localStorage.setItem('hs_user_bdt', '50.00');
            localStorage.setItem('hs_user_xp', '200');
            setTimeout(function () {
              window.location.href = data.redirect_url || '/dashboard/';
            }, 1200);
          } else {
            showAlert(data.message || 'Registration failed. Please try a different username.', true);
            regSubmitBtn.disabled = false;
            regSubmitBtn.textContent = 'Create Free Operative Account ➔';
          }
        })
        .catch(function () {
          // Client graceful fallback
          localStorage.setItem('hs_user_name', u);
          localStorage.setItem('hs_user_bdt', '50.00');
          localStorage.setItem('hs_user_xp', '200');
          showAlert('✓ Operative profile created locally. Welcome!', false);
          setTimeout(function () {
            window.location.href = '/dashboard/';
          }, 1000);
        });
      });
    }

    // Guest Operative Demo Activation
    if (guestBtn) {
      guestBtn.addEventListener('click', function () {
        localStorage.setItem('hs_user_name', 'Guest Operative');
        localStorage.setItem('hs_user_bdt', '50.00');
        localStorage.setItem('hs_user_xp', '200');
        showAlert('✓ Guest operative session activated with ৳50.00 BDT!', false);
        const headerBdt = document.getElementById('hs-header-bdt');
        if (headerBdt) headerBdt.textContent = 'BDT 50.00';
        setTimeout(function () {
          closeModal();
          window.location.href = '/dashboard/';
        }, 800);
      });
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
          btn.style.color = '#00f5d4';
          btn.style.fontWeight = '800';
          btn.style.background = 'rgba(0, 245, 212, 0.2)';
        } else {
          btn.style.color = '#94a3b8';
          btn.style.fontWeight = '500';
          btn.style.background = 'transparent';
        }
      });

      // Invoke i18n engine if loaded
      if (window.HS_I18N && typeof window.HS_I18N.setLanguage === 'function') {
        window.HS_I18N.setLanguage(lang);
      }

      // If switching to Bangla or Hindi, set google translate cookie & trigger translation
      if (lang === 'bn' || lang === 'hi' || lang === 'es' || lang === 'ar') {
        document.cookie = 'googtrans=/en/' + lang + ';path=/;domain=' + window.location.hostname;
        document.cookie = 'googtrans=/en/' + lang + ';path=/';
        
        // Load Google Translate script dynamically if not yet present
        if (!document.getElementById('google-translate-script')) {
          const s = document.createElement('script');
          s.id = 'google-translate-script';
          s.src = '//translate.google.com/translate_a/element.js?cb=hsGoogleTranslateInit';
          document.head.appendChild(s);
          window.hsGoogleTranslateInit = function () {
            new window.google.translate.TranslateElement({
              pageLanguage: 'en',
              includedLanguages: 'bn,hi,ar,es,en',
              autoDisplay: false
            }, 'google_translate_element');
          };
        }
      } else if (lang === 'en') {
        document.cookie = 'googtrans=;path=/;expires=Thu, 01 Jan 1970 00:00:00 UTC;';
        document.cookie = 'googtrans=;path=/;domain=' + window.location.hostname + ';expires=Thu, 01 Jan 1970 00:00:00 UTC;';
      }
    }

    setLanguage(savedLang);

    langButtons.forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        const target = btn.getAttribute('data-lang');
        if (target) {
          setLanguage(target);
          setTimeout(function () {
            window.location.reload();
          }, 350);
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
    document.querySelectorAll('.hs-like-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const postId = this.dataset.postId;
        const formData = new FormData();
        formData.append('action', 'hs_like_post');
        formData.append('post_id', postId);
        
        fetch(hsThemeData.ajax_url, {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                this.querySelector('.hs-likes-count').textContent = data.data.likes;
            }
        });
      });
    });
  }
})();



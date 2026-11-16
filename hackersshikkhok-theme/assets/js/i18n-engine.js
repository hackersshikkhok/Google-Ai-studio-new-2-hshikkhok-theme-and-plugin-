/**
 * HackersShikkhok Frontend English Dictionary Engine (v5.0.3)
 * Official Brand: HackersShikkhok (https://hackersshikkhok.com)
 */
(function () {
  'use strict';

  const translations = {
    en: {
      lang_name: 'English',
      live_radar: 'LIVE RADAR',
      live_marquee: 'HackersShikkhok (HackersShikkhok.com) — 28 Platform Centers, 18 AI Autopilots, Creator Studio & 2,024+ Tools are Active!',
      nav_home: 'Home',
      nav_academy: '🎓 Academy',
      nav_centers: '🚀 Platform Center',
      nav_app_studio: '📱 App Studio',
      nav_tools: '⚡ 2,024+ Tools',
      nav_cyber_cmd: '💻 Cyber Command',
      btn_register: 'Register',
      btn_signin: 'Sign In',
      hero_badge: 'CYBER DEFENSE ACADEMY & 2,024+ TOOL LAB',
      hero_title: 'Master Defensive Cybersecurity & Full-Stack Engineering',
      hero_sub: 'HackersShikkhok is the premier open-access cyber defense academy, production code repository, and interactive 2,024+ client-side developer toolbox.',
      btn_explore_academy: '🎓 Explore Academy Courses',
      btn_platform_center: '🚀 Platform Center & App Studio',
      btn_browse_tools: '⚡ Browse 2,024+ Tools',
      sec_faculties_title: '🛡️ 8 Specialized Engineering Faculties',
      sec_faculties_sub: 'From foundational systems programming to advanced cyber defense operations.',
      view_all_courses: 'View All Courses →',
      sec_tools_title: '⚡ Essential Developer & Security Tools',
      sec_tools_sub: '100% Client-Side, privacy-preserving, and instant in-browser execution.',
      full_tools_dir: 'Full 2,024+ Tools Directory →',
      btn_launch_tool: 'Launch Tool',
      yt_official: 'Official YouTube Channel',
      yt_title: 'HackersShikkhok Official Video Resources',
      yt_sub: 'Every academy course is accompanied by hands-on practical video walkthroughs and live demonstrations. Subscribe to join our community.',
      btn_sub_yt: '▶ Subscribe to YouTube Channel',
      search_placeholder: 'Search courses, tools, commands, tutorials...',
      footer_about_title: 'About HackersShikkhok',
      footer_about_desc: 'The ultimate premier destination for Defensive Cybersecurity, Ethical Hacking, Full-Stack Engineering, and 2,024+ Developer Tools.',
      footer_faculties_title: 'Academic Faculties',
      footer_platform_title: 'Platform Centers',
      footer_legal_title: 'Trust & Governance'
    }
  };

  function getSavedLang() {
    return 'en';
  }

  function applyLanguage() {
    const lang = 'en';
    try {
      localStorage.setItem('hs_user_lang', 'en');
      document.cookie = 'hs_lang=en;path=/;max-age=31536000';
    } catch (e) {}

    const html = document.documentElement;
    html.setAttribute('lang', lang);
    html.removeAttribute('dir');
    document.body.classList.remove('hs-rtl');

    const dict = translations.en;

    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.getAttribute('data-i18n');
      if (dict[key]) {
        if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
          el.setAttribute('placeholder', dict[key]);
        } else {
          el.textContent = dict[key];
        }
      }
    });
  }

  window.HS_I18N = {
    setLanguage: applyLanguage,
    getLanguage: getSavedLang,
    getTranslations: () => translations
  };

  document.addEventListener('DOMContentLoaded', () => {
    const current = getSavedLang();
    applyLanguage(current);

    // Listen to clicks on language switcher buttons
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.hs-lang-btn');
      if (btn) {
        e.preventDefault();
        const targetLang = btn.getAttribute('data-lang');
        if (targetLang) {
          applyLanguage(targetLang);
        }
      }
    });
  });
})();

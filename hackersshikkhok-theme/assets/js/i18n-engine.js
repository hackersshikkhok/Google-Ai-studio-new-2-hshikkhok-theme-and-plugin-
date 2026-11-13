/**
 * HackersShikkhok Live Multi-Language & Internationalization Engine (v4.1.0)
 * Official Brand: HackersShikkhok (https://hackersshikkhok.com)
 * Supports: English (Default), Bengali (বাংলা), Hindi (हिन्दी), Arabic (العربية - RTL), Spanish (Español)
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
      search_placeholder: '🔍 Search courses, tools, commands, tutorials...',
      footer_about_title: 'About HackersShikkhok',
      footer_about_desc: 'The ultimate premier destination for Defensive Cybersecurity, Ethical Hacking, Full-Stack Engineering, and 2,024+ Developer Tools.',
      footer_faculties_title: 'Academic Faculties',
      footer_platform_title: 'Platform Centers',
      footer_legal_title: 'Trust & Governance'
    },
    bn: {
      lang_name: 'বাংলা',
      live_radar: 'লাইভ রাডার',
      live_marquee: 'হ্যাকারস শিক্ষক (HackersShikkhok.com) — ২৮টি প্ল্যাটফর্ম সেন্টার, ১৮টি এআই অটো-পাইলট এবং ২,০২৪+ সিকিউরিটি টুলস সক্রিয়!',
      nav_home: 'হোম',
      nav_academy: '🎓 একাডেমি',
      nav_centers: '🚀 প্ল্যাটফর্ম সেন্টার',
      nav_app_studio: '📱 অ্যাপ স্টুডিও',
      nav_tools: '⚡ ২,০২৪+ টুলস',
      nav_cyber_cmd: '💻 সাইবার কমান্ড',
      btn_register: 'রেজিস্টার',
      btn_signin: 'লগইন',
      hero_badge: 'সাইবার ডিফেন্স একাডেমি ও ২,০২৪+ টুল ল্যাব',
      hero_title: 'ডিফেন্সিভ সাইবার সিকিউরিটি ও ফুল-স্ট্যাক ইঞ্জিনিয়ারিং শিখুন',
      hero_sub: 'হ্যাকারস শিক্ষক হলো বাংলাদেশের এক নম্বর ওপেন-অ্যাক্সেস সাইবার ডিফেন্স একাডেমি, কোড লাইব্রেরি এবং ইন্টারেক্টিভ ২,০২৪+ ডেভেলপার টুলবক্স।',
      btn_explore_academy: '🎓 একাডেমি কোর্সসমূহ দেখুন',
      btn_platform_center: '🚀 প্ল্যাটফর্ম সেন্টার ও অ্যাপ স্টুডিও',
      btn_browse_tools: '⚡ ২,০২৪+ টুলস ব্রাউজ করুন',
      sec_faculties_title: '🛡️ ৮টি বিশেষায়িত ইঞ্জিনিয়ারিং ফ্যাকাল্টি',
      sec_faculties_sub: 'সিস্টেমস প্রোগ্রামিং থেকে শুরু করে অ্যাডভান্সড সাইবার ডিফেন্স অপারেশন পর্যন্ত।',
      view_all_courses: 'সকল কোর্স দেখুন →',
      sec_tools_title: '⚡ প্রয়োজনীয় ডেভেলপার ও সিকিউরিটি টুলস',
      sec_tools_sub: '১০০% ক্লায়েন্ট-সাইড, প্রাইভেসি-সুরক্ষিত ও ব্রাউজারে ইনস্ট্যান্ট রানটাইম।',
      full_tools_dir: 'সম্পূর্ণ ২,০২৪+ টুল ডিরেক্টরি →',
      btn_launch_tool: 'টুল চালু করুন',
      yt_official: 'অফিসিয়াল ইউটিউব চ্যানেল',
      yt_title: 'হ্যাকারস শিক্ষক অফিসিয়াল ভিডিও রিসোর্স',
      yt_sub: 'আমাদের প্রতিটি একাডেমি কোর্সের সাথে রয়েছে প্র্যাকটিক্যাল হ্যান্ডস-অন ভিডিও গাইড এবং লাইভ ডেমো। আমাদের চ্যানেলে যুক্ত থাকুন।',
      btn_sub_yt: '▶ ইউটিউব চ্যানেলে সাবস্ক্রাইব করুন',
      search_placeholder: '🔍 কোর্স, টুলস, কোড, টিউটোরিয়াল সার্চ করুন...',
      footer_about_title: 'হ্যাকারস শিক্ষক সম্পর্কে',
      footer_about_desc: 'ডিফেন্সিভ সাইবার সিকিউরিটি, এথিক্যাল হ্যাকিং, সফটওয়্যার ডেভেলপমেন্ট এবং ২,০২৪+ ক্লায়েন্ট-সাইড টুলসের প্রিমিয়ার এডুকেশনাল হাব।',
      footer_faculties_title: 'একাডেমিক ফ্যাকাল্টি',
      footer_platform_title: 'প্ল্যাটফর্ম সেন্টারসমূহ',
      footer_legal_title: 'আইনি শর্তাবলী ও ট্রাস্ট'
    },
    hi: {
      lang_name: 'हिन्दी',
      live_radar: 'लाइव रडार',
      live_marquee: 'हैकर्स शिक्षक (HackersShikkhok.com) — 28 प्लेटफॉर्म सेंटर, 18 एआई ऑटोपायलट और 2,024+ टूल्स सक्रिय हैं!',
      nav_home: 'होम',
      nav_academy: '🎓 अकादमी',
      nav_centers: '🚀 प्लेटफॉर्म सेंटर',
      nav_app_studio: '📱 ऐप स्टूडियो',
      nav_tools: '⚡ 2,024+ टूल्स',
      nav_cyber_cmd: '💻 साइबर कमांड',
      btn_register: 'रजिस्टर',
      btn_signin: 'लॉग इन',
      hero_badge: 'साइबर डिफेंस अकादमी और 2,024+ टूल लैब',
      hero_title: 'डिफेंसिव साइबर सुरक्षा और फुल-स्टैक इंजीनियरिंग में महारत हासिल करें',
      hero_sub: 'हैकर्स शिक्षक प्रमुख ओपन-एक्सेस साइबर डिफेंस अकादमी और इंटरैक्टिव 2,024+ डेवलपर टूलबॉक्स है।',
      btn_explore_academy: '🎓 अकादमी कोर्स देखें',
      btn_platform_center: '🚀 प्लेटफॉर्म सेंटर और ऐप स्टूडियो',
      btn_browse_tools: '⚡ 2,024+ टूल्स ब्राउज़ करें',
      sec_faculties_title: '🛡️ 8 विशेष इंजीनियरिंग संकाय',
      sec_faculties_sub: 'फाउंडेशनल सिस्टम्स प्रोग्रामिंग से लेकर उन्नत साइबर सुरक्षा संचालन तक।',
      view_all_courses: 'सभी कोर्स देखें →',
      sec_tools_title: '⚡ आवश्यक डेवलपर और सुरक्षा टूल्स',
      sec_tools_sub: '100% क्लाइंट-साइड, गोपनीयता-संरक्षित और ब्राउज़र में तत्काल निष्पादन।',
      full_tools_dir: 'संपूर्ण 2,024+ टूल निर्देशिका →',
      btn_launch_tool: 'टूल लॉन्च करें',
      yt_official: 'आधिकारिक यूट्यूब चैनल',
      yt_title: 'हैकर्स शिक्षक आधिकारिक वीडियो संसाधन',
      yt_sub: 'प्रत्येक अकादमी कोर्स के साथ व्यावहारिक वीडियो वॉकथ्रू शामिल हैं।',
      btn_sub_yt: '▶ यूट्यूब चैनल सब्सक्राइब करें',
      search_placeholder: '🔍 कोर्स, टूल्स, कोड, ट्यूटोरियल खोजें...',
      footer_about_title: 'हैकर्स शिक्षक के बारे में',
      footer_about_desc: 'साइबर सुरक्षा शिक्षा और 2,024+ डेवलपर टूल्स का प्रमुख गंतव्य।',
      footer_faculties_title: 'अकादमिक संकाय',
      footer_platform_title: 'प्लेटफॉर्म सेंटर',
      footer_legal_title: 'कानूनी और विश्वास'
    },
    ar: {
      lang_name: 'العربية',
      live_radar: 'رادار حي',
      live_marquee: 'هاكرز شيكوك (HackersShikkhok.com) — 28 مركز منصة، 18 طيار آلي ذكي، و 2,024+ أداة برمجية نشطة!',
      nav_home: 'الرئيسية',
      nav_academy: '🎓 الأكاديمية',
      nav_centers: '🚀 مركز المنصة',
      nav_app_studio: '📱 استوديو التطبيقات',
      nav_tools: '⚡ 2,024+ أداة',
      nav_cyber_cmd: '💻 قيادة السايبر',
      btn_register: 'تسجيل حساب',
      btn_signin: 'تسجيل الدخول',
      hero_badge: 'أكاديمية الدفاع السيبراني ومختبر الأدوات 2,024+',
      hero_title: 'احتراف الأمن السيبراني الدفاعي وهندسة البرمجيات المتكاملة',
      hero_sub: 'هاكرز شيكوك هي الأكاديمية الرائدة ومستودع الأكواد المفتوحة وصندوق أدوات المطورين التفاعلي.',
      btn_explore_academy: '🎓 استكشف دورات الأكاديمية',
      btn_platform_center: '🚀 مركز المنصة واستوديو التطبيقات',
      btn_browse_tools: '⚡ تصفح 2,024+ أداة',
      sec_faculties_title: '🛡️ 8 كليات هندسية متخصصة',
      sec_faculties_sub: 'من برمجة الأنظمة الأساسية إلى عمليات الدفاع السيبراني المتقدمة.',
      view_all_courses: 'عرض جميع الدورات ←',
      sec_tools_title: '⚡ أدوات المطورين والأمن الأساسية',
      sec_tools_sub: '100% تنفيذ في المتصفح، يحافظ على الخصوصية ومباشر.',
      full_tools_dir: 'دليل الأدوات الكامل 2,024+ ←',
      btn_launch_tool: 'تشغيل الأداة',
      yt_official: 'قناة يوتيوب الرسمية',
      yt_title: 'موارد الفيديو الرسمية لهاكرز شيكوك',
      yt_sub: 'كل دورة تدريبية مصحوبة بشروحات عملية بالفيديو وتطبيقات حية.',
      btn_sub_yt: '▶ اشترك في قناة اليوتيوب',
      search_placeholder: '🔍 ابحث عن الدورات، الأدوات، الأكواد، الشروحات...',
      footer_about_title: 'عن هاكرز شيكوك',
      footer_about_desc: 'الوجهة الأولى الرائدة لتعليم الأمن السيبراني وأدوات المطورين البرمجية.',
      footer_faculties_title: 'الكليات الأكاديمية',
      footer_platform_title: 'مراكز المنصة',
      footer_legal_title: 'الحوكمة القانونية والأمان'
    },
    es: {
      lang_name: 'Español',
      live_radar: 'RADAR EN VIVO',
      live_marquee: 'HackersShikkhok (HackersShikkhok.com) — ¡28 Centros de Plataforma, 18 Pilotos IA y 2,024+ Herramientas Activas!',
      nav_home: 'Inicio',
      nav_academy: '🎓 Academia',
      nav_centers: '🚀 Centro de Plataforma',
      nav_app_studio: '📱 Estudio de Apps',
      nav_tools: '⚡ 2,024+ Herramientas',
      nav_cyber_cmd: '💻 Comando Ciber',
      btn_register: 'Registrarse',
      btn_signin: 'Iniciar Sesión',
      hero_badge: 'ACADEMIA DE CIBERDEFENSA Y LABORATORIO DE 2,024+ HERRAMIENTAS',
      hero_title: 'Domine la Ciberseguridad Defensiva y la Ingeniería Full-Stack',
      hero_sub: 'HackersShikkhok es la academia líder de ciberdefensa de código abierto y caja de herramientas interactiva.',
      btn_explore_academy: '🎓 Explorar Cursos',
      btn_platform_center: '🚀 Centro de Plataforma y Estudio',
      btn_browse_tools: '⚡ Explorar 2,024+ Herramientas',
      sec_faculties_title: '🛡️ 8 Facultades Especializadas de Ingeniería',
      sec_faculties_sub: 'Desde programación de sistemas hasta operaciones avanzadas de ciberdefensa.',
      view_all_courses: 'Ver Todos los Cursos →',
      sec_tools_title: '⚡ Herramientas Esenciales de Desarrollo y Seguridad',
      sec_tools_sub: '100% en el cliente, respetuoso con la privacidad y ejecución instantánea.',
      full_tools_dir: 'Directorio Completo de 2,024+ Herramientas →',
      btn_launch_tool: 'Iniciar Herramienta',
      yt_official: 'Canal Oficial de YouTube',
      yt_title: 'Recursos en Video Oficiales de HackersShikkhok',
      yt_sub: 'Cada curso incluye demostraciones prácticas y paso a paso en video.',
      btn_sub_yt: '▶ Suscribirse en YouTube',
      search_placeholder: '🔍 Buscar cursos, herramientas, código...',
      footer_about_title: 'Acerca de HackersShikkhok',
      footer_about_desc: 'La plataforma definitiva para educación en ciberseguridad defensiva y herramientas de desarrollo.',
      footer_faculties_title: 'Facultades Académicas',
      footer_platform_title: 'Centros de Plataforma',
      footer_legal_title: 'Confianza y Legalidad'
    }
  };

  function getSavedLang() {
    try {
      const stored = localStorage.getItem('hs_user_lang');
      if (stored && translations[stored]) return stored;
      const match = document.cookie.match(/hs_lang=([a-z]{2})/);
      if (match && translations[match[1]]) return match[1];
    } catch (e) {}
    return 'en';
  }

  function applyLanguage(lang) {
    if (!translations[lang]) lang = 'en';
    try {
      localStorage.setItem('hs_user_lang', lang);
      document.cookie = 'hs_lang=' + lang + ';path=/;max-age=31536000';
    } catch (e) {}

    const html = document.documentElement;
    html.setAttribute('lang', lang);

    // RTL handling for Arabic
    if (lang === 'ar') {
      html.setAttribute('dir', 'rtl');
      document.body.classList.add('hs-rtl');
    } else {
      html.removeAttribute('dir');
      document.body.classList.remove('hs-rtl');
    }

    const dict = translations[lang];

    // Update all elements with data-i18n
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

    // Update active highlight in language switchers
    document.querySelectorAll('.hs-lang-btn').forEach(btn => {
      const blang = btn.getAttribute('data-lang');
      if (blang === lang) {
        btn.style.color = '#00f5d4';
        btn.style.fontWeight = '800';
        btn.style.background = 'rgba(0, 245, 212, 0.15)';
      } else {
        btn.style.color = '#94a3b8';
        btn.style.fontWeight = '500';
        btn.style.background = 'transparent';
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

import React, { useState } from 'react';
import {
  THEME_SCREENSHOT_IMG,
  PLUGIN_BANNER_IMG,
  PLUGIN_ICON_IMG
} from '../data/wpVirtualFiles';
import {
  Download,
  CheckCircle2,
  Info,
  X,
  Eye,
  Terminal,
  Database,
  Shield,
  Layers,
  Sparkles,
  ExternalLink,
  Laptop,
  Smartphone,
  Tablet,
  Check,
  Code
} from 'lucide-react';

interface WpInstalledPreviewShowcaseProps {
  onDownloadPackage: (pkg: 'theme' | 'core' | 'docs' | 'all') => void;
  themeFileCount: number;
  coreFileCount: number;
}

type ModalMode = 'theme-preview' | 'theme-details' | 'plugin-preview' | 'plugin-details' | null;

export const WpInstalledPreviewShowcase: React.FC<WpInstalledPreviewShowcaseProps> = ({
  onDownloadPackage,
  themeFileCount,
  coreFileCount
}) => {
  const [modalMode, setModalMode] = useState<ModalMode>(null);
  const [deviceMode, setDeviceMode] = useState<'desktop' | 'tablet' | 'mobile'>('desktop');
  const [activeTab, setActiveTab] = useState<'preview' | 'files'>('preview');

  // Simulated Terminal State for Live Theme Preview
  const [termInput, setTermInput] = useState('');
  const [termOutput, setTermOutput] = useState<string[]>([
    'HackersShikkhok Cyber Shell v4.0.0 [Theme Engine Native]',
    'Type "help", "scan", "status", or "ctf" to test live command simulation.'
  ]);

  // Simulated Plugin Admin State for Live Plugin Preview
  const [simulatedFlagInput, setSimulatedFlagInput] = useState('');
  const [simulatedFlagResult, setSimulatedFlagResult] = useState<string | null>(null);

  const handleRunTermCommand = (e: React.FormEvent) => {
    e.preventDefault();
    const cmd = termInput.trim().toLowerCase();
    if (!cmd) return;

    let res = `> ${termInput}`;
    let extra = '';

    if (cmd === 'help') {
      extra = 'Commands: help, scan, status, ctf, clear, info';
    } else if (cmd === 'scan') {
      extra = '[✓] 21 DB Tables verified. Prepared SQL active. Nonce checks enabled. Zero XSS detected.';
    } else if (cmd === 'status') {
      extra = '[✓] Theme: HackersShikkhok 2040 Cyber & Coding Hub Theme (v4.0.0) — Active & Protected.';
    } else if (cmd === 'ctf') {
      extra = '[FLAG] Test challenge: HS{WP_CYBER_2040_SECURE_KERNEL}';
    } else if (cmd === 'clear') {
      setTermOutput(['HackersShikkhok Cyber Shell cleared.']);
      setTermInput('');
      return;
    } else {
      extra = `Command not recognized: "${cmd}". Type "help" for instructions.`;
    }

    setTermOutput((prev) => [...prev, res, extra]);
    setTermInput('');
  };

  const handleTestFlag = (e: React.FormEvent) => {
    e.preventDefault();
    if (simulatedFlagInput.trim().toLowerCase() === 'hs{wp_cyber_2040_secure_kernel}') {
      setSimulatedFlagResult('success: ফ্ল্যাগ সঠিক! +500 XP ও ৳৫০.০০ ওয়ালেটে যুক্ত হয়েছে (hash_equals verified)।');
    } else {
      setSimulatedFlagResult('error: ভুল ফ্ল্যাগ! ক্রিপ্টোগ্রাফিক হ্যাশ মেলেনি। পুনরায় চেষ্টা করুন।');
    }
  };

  return (
    <section className="bg-[#0b1120] border border-[#00f5d4]/30 rounded-2xl p-5 md:p-6 space-y-6">
      {/* Header */}
      <div className="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-slate-800">
        <div>
          <div className="text-xs font-mono text-[#00f5d4] tracking-wider uppercase">
            WORDPRESS ADMIN APPEARANCE &amp; PLUGINS PREVIEW · BRAND: HackersShikkhok
          </div>
          <h2 className="text-xl md:text-2xl font-bold text-white mt-1">
            ওয়ার্ডপ্রেস থিম ও কোর প্লাগইন লাইভ প্রিভিউ ও প্যাকেজ ইন্সটলার
          </h2>
          <p className="text-xs text-slate-300 mt-1 max-w-3xl leading-relaxed">
            WordPress Appearance &gt; Themes এবং Plugins মেন্যুতে যেভাবে প্রদর্শিত হয়। অফিসিয়াল{' '}
            <code className="text-[#00f5d4]">screenshot.png (1200×900)</code>,{' '}
            <code className="text-[#00f5d4]">icon-256x256.png</code> ও{' '}
            <code className="text-[#00f5d4]">banner-772x250.png</code> সহ ফুল ড্যাশবোর্ড ইন্টিগ্রেশন।
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-2.5">
          <button
            onClick={() => onDownloadPackage('theme')}
            className="px-4 py-2.5 rounded-xl bg-[#00f5d4] hover:bg-[#00f5d4]/90 text-[#050811] font-extrabold text-xs flex items-center gap-2 shadow-[0_0_20px_rgba(0,245,212,0.3)] cursor-pointer transition-all"
          >
            <Download className="w-4 h-4" />
            <span>🛡️ Download Theme Zip (hackersshikkhok-theme.zip)</span>
          </button>
          <button
            onClick={() => onDownloadPackage('core')}
            className="px-4 py-2.5 rounded-xl bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-extrabold text-xs flex items-center gap-2 shadow-[0_0_20px_rgba(124,58,237,0.35)] cursor-pointer transition-all"
          >
            <Download className="w-4 h-4" />
            <span>⚙️ Download Plugin Zip (hackersshikkhok-core.zip)</span>
          </button>
        </div>
      </div>

      {/* Two-Column WordPress Admin Preview Cards */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* 1. WORDPRESS THEME PREVIEW CARD */}
        <div className="bg-[#050811] border border-[#00f5d4]/30 rounded-2xl overflow-hidden flex flex-col justify-between shadow-lg">
          <div>
            <div className="px-4 py-2.5 bg-slate-900/90 border-b border-slate-800 flex items-center justify-between text-xs">
              <span className="font-mono text-[#00f5d4] font-semibold">
                WordPress Admin → Appearance → Themes (screenshot.png)
              </span>
              <span className="text-emerald-400 font-mono text-[11px] flex items-center gap-1">
                <CheckCircle2 className="w-3.5 h-3.5" /> Active Theme
              </span>
            </div>

            <div className="relative aspect-[4/3] w-full bg-[#0b1120] overflow-hidden border-b border-slate-800 group">
              <img
                src={THEME_SCREENSHOT_IMG}
                alt="HackersShikkhok 2040 Cyber & Coding Hub Theme screenshot.png"
                referrerPolicy="no-referrer"
                className="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-300"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-[#050811] via-[#050811]/65 to-transparent flex flex-col justify-end p-5">
                <div className="text-[11px] font-mono text-[#00f5d4] tracking-wider uppercase">
                  OFFICIAL WORDPRESS THEME · v4.0.0 · 1200×900 SCREENSHOT.PNG
                </div>
                <h3 className="text-lg sm:text-xl font-extrabold text-white mt-0.5">
                  HackersShikkhok 2040 Cyber &amp; Coding Hub Theme
                </h3>
                <p className="text-xs text-slate-200 mt-1">
                  Developed by <strong className="text-[#00f5d4]">HackersShikkhok</strong> ·{' '}
                  <a
                    href="https://hackersshikkhok.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="underline text-[#00f5d4]"
                  >
                    https://hackersshikkhok.com
                  </a>
                </p>
              </div>
            </div>

            <div className="p-5 space-y-3 text-xs">
              <div className="flex flex-wrap items-center justify-between gap-2">
                <div>
                  <span className="text-slate-400">Theme Name: </span>
                  <strong className="text-white">HackersShikkhok 2040 Cyber &amp; Coding Hub Theme</strong>
                </div>
                <span className="font-mono text-[#00f5d4]">Version 4.0.0</span>
              </div>

              <div className="text-slate-300 leading-relaxed bg-[#0b1120] p-3.5 rounded-xl border border-slate-800/90">
                <strong className="text-[#00f5d4] block mb-1">থিম বর্ণনা (Theme Description):</strong>
                HackersShikkhok (HackersShikkhok.com | YouTube: @HackersShikkhok)-এর অফিসিয়াল প্রিমিয়াম সাইবার ও কোডিং হাব থিম (v4.0.0)। থিমটিতে রয়েছে ফুল রেসপন্সিভ সাইবারপাঙ্ক LMS একাডেমি স্টেজ (৩-কলাম লেআউট), লাইভ ওয়েব টার্মিনাল সিমুলেটর, ডাইনামিক ভেক্টর SVG আর্কিটেকচার জেনারেটর, ২০২৪+ ডেভেলপার টুলস সেন্টার, ডার্ক গ্লাস মরফিজম ইন্টারফেস এবং সম্পূর্ণ এসইও-অপ্টিমাইজড টেমপ্লেট হায়ারার্কি।
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] font-mono text-slate-300 pt-1">
                <div className="bg-[#0b1120] px-3 py-2 rounded-lg border border-slate-800">
                  <span className="text-slate-400 block">Author:</span>
                  <strong className="text-white">Developed by HackersShikkhok</strong>
                </div>
                <div className="bg-[#0b1120] px-3 py-2 rounded-lg border border-slate-800">
                  <span className="text-slate-400 block">Theme &amp; Download URI:</span>
                  <a
                    href="https://hackersshikkhok.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-[#00f5d4] hover:underline font-bold"
                  >
                    https://hackersshikkhok.com
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div className="px-5 py-3.5 bg-slate-900/60 border-t border-slate-800 flex flex-wrap items-center justify-between gap-2">
            <div className="flex items-center gap-2">
              <button
                onClick={() => setModalMode('theme-preview')}
                className="px-3.5 py-2 rounded-lg bg-[#00f5d4]/20 hover:bg-[#00f5d4]/30 border border-[#00f5d4]/40 text-[#00f5d4] text-xs font-bold flex items-center gap-1.5 cursor-pointer transition-all"
              >
                <Eye className="w-3.5 h-3.5" />
                <span>লাইভ থিম প্রিভিউ ও ডেমো</span>
              </button>
              <button
                onClick={() => setModalMode('theme-details')}
                className="px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 cursor-pointer"
              >
                <Info className="w-3.5 h-3.5 text-slate-400" />
                <span>বিস্তারিত</span>
              </button>
            </div>
            <button
              onClick={() => onDownloadPackage('theme')}
              className="px-4 py-2 rounded-lg bg-[#00f5d4] hover:bg-[#00f5d4]/90 text-[#050811] font-extrabold text-xs flex items-center gap-1.5 cursor-pointer"
            >
              <Download className="w-3.5 h-3.5" />
              <span>Download hackersshikkhok-theme.zip ({themeFileCount + 2} files)</span>
            </button>
          </div>
        </div>

        {/* 2. WORDPRESS CORE PLUGIN PREVIEW CARD */}
        <div className="bg-[#050811] border border-[#7c3aed]/40 rounded-2xl overflow-hidden flex flex-col justify-between shadow-lg">
          <div>
            <div className="px-4 py-2.5 bg-slate-900/90 border-b border-slate-800 flex items-center justify-between text-xs">
              <span className="font-mono text-purple-300 font-semibold">
                WordPress Admin → Plugins → Installed Plugins
              </span>
              <span className="text-emerald-400 font-mono text-[11px] flex items-center gap-1">
                <CheckCircle2 className="w-3.5 h-3.5" /> Active Plugin
              </span>
            </div>

            <div className="relative aspect-[16/9] w-full bg-[#0b1120] overflow-hidden border-b border-slate-800 group">
              <img
                src={PLUGIN_BANNER_IMG}
                alt="HackersShikkhok Core Engine Plugin Banner"
                referrerPolicy="no-referrer"
                className="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-300"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-[#050811] via-[#050811]/70 to-transparent flex items-end p-5 gap-4">
                <img
                  src={PLUGIN_ICON_IMG}
                  alt="HackersShikkhok Core Plugin 256x256 Icon"
                  referrerPolicy="no-referrer"
                  className="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl border-2 border-[#00f5d4] shadow-[0_0_20px_rgba(0,245,212,0.4)] object-cover shrink-0"
                />
                <div>
                  <div className="text-[11px] font-mono text-[#00f5d4] tracking-wider uppercase">
                    CORE ENGINE PLUGIN · v4.0.0 · ICON + BANNER EMBEDDED
                  </div>
                  <h3 className="text-lg sm:text-xl font-extrabold text-white mt-0.5">
                    HackersShikkhok Core Engine (v4.0 Complete Ecosystem)
                  </h3>
                  <p className="text-xs text-slate-200 mt-1">
                    Developed by <strong className="text-[#00f5d4]">HackersShikkhok</strong> ·{' '}
                    <a
                      href="https://hackersshikkhok.com"
                      target="_blank"
                      rel="noopener noreferrer"
                      className="underline text-[#00f5d4]"
                    >
                      https://hackersshikkhok.com
                    </a>
                  </p>
                </div>
              </div>
            </div>

            <div className="p-5 space-y-3 text-xs">
              <div className="flex flex-wrap items-center justify-between gap-2">
                <div>
                  <span className="text-slate-400">Plugin Name: </span>
                  <strong className="text-white">HackersShikkhok Core Engine</strong>
                </div>
                <span className="font-mono text-purple-300">Requires PHP 8.2+ · WP 6.4+</span>
              </div>

              <div className="text-slate-300 leading-relaxed bg-[#0b1120] p-3.5 rounded-xl border border-slate-800/90">
                <strong className="text-[#00f5d4] block mb-1">প্লাগইন বর্ণনা (Plugin Description):</strong>
                HackersShikkhok কোর বিজনেস লজিক ইঞ্জিন (v4.0.0)। এতে রয়েছে dbDelta ভিত্তিক ২১টি কাস্টম ডাটাবেস টেবিল স্কিমা, ক্রিপ্টোগ্রাফিক CTF ফ্ল্যাগ ভ্যালিডেশন ইঞ্জিন, ২৮টি প্ল্যাটফর্ম সেন্টার এন্ডপয়েন্ট, মনিটারি ওয়ালেট (BDT) ও রিওয়ার্ড পয়েন্ট লেজার, জেমিনি AI অটোপাইলট কনটেন্ট ক্রাফটার, এবং এন্টারপ্রাইজ সিকিউরিটি (Prepared Statements, Nonces &amp; Capabilities)।
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] font-mono text-slate-300 pt-1">
                <div className="bg-[#0b1120] px-3 py-2 rounded-lg border border-slate-800">
                  <span className="text-slate-400 block">Developer Credit:</span>
                  <strong className="text-white">Developed by HackersShikkhok</strong>
                </div>
                <div className="bg-[#0b1120] px-3 py-2 rounded-lg border border-slate-800">
                  <span className="text-slate-400 block">Plugin &amp; Download Link:</span>
                  <a
                    href="https://hackersshikkhok.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-[#00f5d4] hover:underline font-bold"
                  >
                    https://hackersshikkhok.com
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div className="px-5 py-3.5 bg-slate-900/60 border-t border-slate-800 flex flex-wrap items-center justify-between gap-2">
            <div className="flex items-center gap-2">
              <button
                onClick={() => setModalMode('plugin-preview')}
                className="px-3.5 py-2 rounded-lg bg-[#7c3aed]/20 hover:bg-[#7c3aed]/30 border border-[#7c3aed]/40 text-purple-300 text-xs font-bold flex items-center gap-1.5 cursor-pointer transition-all"
              >
                <Eye className="w-3.5 h-3.5" />
                <span>লাইভ প্লাগইন প্যানেল প্রিভিউ</span>
              </button>
              <button
                onClick={() => setModalMode('plugin-details')}
                className="px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 cursor-pointer"
              >
                <Info className="w-3.5 h-3.5 text-slate-400" />
                <span>বিস্তারিত</span>
              </button>
            </div>
            <button
              onClick={() => onDownloadPackage('core')}
              className="px-4 py-2 rounded-lg bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-extrabold text-xs flex items-center gap-1.5 cursor-pointer"
            >
              <Download className="w-3.5 h-3.5" />
              <span>Download hackersshikkhok-core.zip ({coreFileCount + 2} files)</span>
            </button>
          </div>
        </div>
      </div>

      {/* =========================================================================
          INTERACTIVE FULL PREVIEW & DETAILS MODAL
      ========================================================================== */}
      {modalMode && (
        <div className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-5 overflow-y-auto">
          <div className="bg-[#0b1120] border-2 border-[#00f5d4]/50 rounded-2xl max-w-5xl w-full overflow-hidden shadow-2xl max-h-[92vh] flex flex-col">
            {/* Modal Header */}
            <div className="px-5 py-4 bg-[#050811] border-b border-slate-800 flex flex-wrap items-center justify-between gap-3">
              <div className="flex items-center gap-3">
                <img
                  src={PLUGIN_ICON_IMG}
                  alt="HackersShikkhok Icon"
                  className="w-10 h-10 rounded-xl border border-[#00f5d4]/60 object-cover"
                />
                <div>
                  <span className="text-[10px] font-mono text-[#00f5d4] uppercase tracking-wider">
                    {modalMode.startsWith('theme') ? 'WORDPRESS THEME ECOSYSTEM' : 'WORDPRESS CORE PLUGIN ECOSYSTEM'}
                  </span>
                  <h3 className="text-base sm:text-lg font-bold text-white">
                    {modalMode.startsWith('theme')
                      ? 'HackersShikkhok 2040 Cyber & Coding Hub Theme (v4.0.0)'
                      : 'HackersShikkhok Core Engine Plugin (v4.0.0)'}
                  </h3>
                </div>
              </div>

              {/* Mode Switcher & Close */}
              <div className="flex items-center gap-2">
                <div className="flex items-center rounded-lg bg-slate-900 border border-slate-800 p-1">
                  <button
                    onClick={() => setActiveTab('preview')}
                    className={`px-3 py-1 rounded text-xs font-semibold transition-colors cursor-pointer ${
                      activeTab === 'preview' ? 'bg-[#00f5d4] text-[#050811]' : 'text-slate-400 hover:text-white'
                    }`}
                  >
                    লাইভ প্রিভিউ
                  </button>
                  <button
                    onClick={() => setActiveTab('files')}
                    className={`px-3 py-1 rounded text-xs font-semibold transition-colors cursor-pointer ${
                      activeTab === 'files' ? 'bg-[#00f5d4] text-[#050811]' : 'text-slate-400 hover:text-white'
                    }`}
                  >
                    ফাইল ও স্পেক্স
                  </button>
                </div>

                <button
                  onClick={() => setModalMode(null)}
                  className="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white cursor-pointer ml-2"
                >
                  <X className="w-5 h-5" />
                </button>
              </div>
            </div>

            {/* Modal Body */}
            <div className="flex-1 overflow-y-auto p-5 space-y-5">
              {/* TAB 1: LIVE INTERACTIVE PREVIEW */}
              {activeTab === 'preview' && (
                <div className="space-y-4">
                  {/* Device Size Switcher */}
                  <div className="flex items-center justify-between bg-[#050811] px-4 py-2.5 rounded-xl border border-slate-800">
                    <span className="text-xs font-mono text-slate-300 flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                      {modalMode.startsWith('theme')
                        ? 'লাইভ থিম স্টেজ সিমুলেটর (page-academy.php 3-কলাম লেআউট)'
                        : 'লাইভ প্লাগইন অ্যাডমিন কন্ট্রোল প্যানেল (WP-Admin Dashboard Engine)'}
                    </span>

                    <div className="flex items-center gap-1.5">
                      <button
                        onClick={() => setDeviceMode('desktop')}
                        className={`p-1.5 rounded-lg text-xs cursor-pointer ${
                          deviceMode === 'desktop' ? 'bg-[#00f5d4] text-[#050811]' : 'text-slate-400 hover:text-white'
                        }`}
                        title="Desktop View (100%)"
                      >
                        <Laptop className="w-4 h-4" />
                      </button>
                      <button
                        onClick={() => setDeviceMode('tablet')}
                        className={`p-1.5 rounded-lg text-xs cursor-pointer ${
                          deviceMode === 'tablet' ? 'bg-[#00f5d4] text-[#050811]' : 'text-slate-400 hover:text-white'
                        }`}
                        title="Tablet View (768px)"
                      >
                        <Tablet className="w-4 h-4" />
                      </button>
                      <button
                        onClick={() => setDeviceMode('mobile')}
                        className={`p-1.5 rounded-lg text-xs cursor-pointer ${
                          deviceMode === 'mobile' ? 'bg-[#00f5d4] text-[#050811]' : 'text-slate-400 hover:text-white'
                        }`}
                        title="Mobile View (375px)"
                      >
                        <Smartphone className="w-4 h-4" />
                      </button>
                    </div>
                  </div>

                  {/* Stage Simulator Container */}
                  <div className="flex justify-center bg-[#050811]/60 p-2 sm:p-4 rounded-2xl border border-slate-800">
                    <div
                      className={`transition-all duration-300 rounded-xl overflow-hidden border border-[#00f5d4]/30 bg-[#050811] shadow-2xl ${
                        deviceMode === 'desktop'
                          ? 'w-full'
                          : deviceMode === 'tablet'
                          ? 'w-[768px] max-w-full'
                          : 'w-[375px] max-w-full'
                      }`}
                    >
                      {/* Simulated Browser Bar */}
                      <div className="px-3 py-2 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-xs font-mono">
                        <div className="flex items-center gap-1.5">
                          <span className="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                          <span className="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                          <span className="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        </div>
                        <span className="text-slate-400 text-[11px] truncate px-2">
                          https://hackersshikkhok.com/
                          {modalMode.startsWith('theme') ? 'academy/page-academy.php' : 'wp-admin/admin.php?page=hs-core'}
                        </span>
                        <span className="text-emerald-400 text-[10px]">200 OK</span>
                      </div>

                      {/* THEME LIVE CONTENT */}
                      {modalMode.startsWith('theme') ? (
                        <div className="p-4 sm:p-6 space-y-5 text-xs text-slate-200">
                          {/* Theme Cyber Header */}
                          <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                            <div className="flex items-center gap-2">
                              <span className="px-2 py-0.5 rounded bg-[#00f5d4] text-[#050811] font-extrabold text-xs">
                                HS-2040
                              </span>
                              <strong className="text-white text-sm">HackersShikkhok Academy</strong>
                            </div>
                            <div className="hidden sm:flex items-center gap-3 text-slate-300 text-xs">
                              <span className="text-[#00f5d4] cursor-pointer">কোর্সসমূহ</span>
                              <span className="hover:text-white cursor-pointer">টার্মিনাল ল্যাব</span>
                              <span className="hover:text-white cursor-pointer">টুলস</span>
                              <span className="px-2.5 py-1 rounded bg-[#7c3aed] text-white font-bold cursor-pointer">
                                ৳ ওয়ালেট: ৫০.০
                              </span>
                            </div>
                          </div>

                          {/* 3-Column Cyberpunk Stage */}
                          <div className="grid grid-cols-1 md:grid-cols-12 gap-4">
                            {/* Col 1: Curriculum */}
                            <div className="md:col-span-3 bg-[#0b1120] border border-slate-800 rounded-xl p-3 space-y-2">
                              <div className="text-[11px] font-mono text-[#00f5d4] font-bold uppercase">
                                কারিকুলাম লেসনস
                              </div>
                              <div className="space-y-1.5 text-[11px]">
                                <div className="p-2 rounded-lg bg-[#00f5d4]/15 border border-[#00f5d4]/40 text-white font-semibold flex items-center justify-between">
                                  <span>১. সাইবার আর্কিটেকচার</span>
                                  <Check className="w-3.5 h-3.5 text-[#00f5d4]" />
                                </div>
                                <div className="p-2 rounded-lg bg-slate-900 text-slate-300 hover:text-white cursor-pointer flex items-center justify-between">
                                  <span>২. Prepared SQL কোয়েরি</span>
                                  <span className="text-[10px] text-slate-500">Free</span>
                                </div>
                                <div className="p-2 rounded-lg bg-slate-900 text-slate-300 hover:text-white cursor-pointer flex items-center justify-between">
                                  <span>৩. CTF রিভার্স ইঞ্জিনিয়ারিং</span>
                                  <span className="text-[10px] text-purple-400">Pro</span>
                                </div>
                              </div>
                            </div>

                            {/* Col 2: Center Interactive Terminal & Stage */}
                            <div className="md:col-span-6 space-y-3">
                              <div className="bg-[#0b1120] border border-slate-800 rounded-xl p-4">
                                <div className="text-sm font-bold text-white">লেসন ১: সাইবারপাঙ্ক আর্কিটেকচার স্টেজ</div>
                                <p className="text-slate-300 text-[11px] mt-1 leading-relaxed">
                                  HackersShikkhok থিমের মূল বৈশিষ্ট্য হলো শিক্ষার্থীদের হ্যান্ডস-অন লার্নিং অভিজ্ঞতা। নিচে দেওয়া ওয়েব টার্মিনালে ইন্টারঅ্যাক্টিভ কমান্ড রান করুন:
                                </p>
                              </div>

                              {/* Live Web Console Simulator */}
                              <div className="bg-black/90 border border-[#00f5d4]/40 rounded-xl p-3 font-mono text-xs">
                                <div className="flex items-center justify-between text-[11px] text-[#00f5d4] border-b border-slate-800 pb-1.5 mb-2">
                                  <span className="flex items-center gap-1">
                                    <Terminal className="w-3.5 h-3.5" /> root@hackersshikkhok:~#
                                  </span>
                                  <span className="text-[10px] text-slate-400">Live Simulated Shell</span>
                                </div>
                                <div className="space-y-1 text-emerald-400 text-[11px] max-h-36 overflow-y-auto mb-2">
                                  {termOutput.map((msg, i) => (
                                    <div key={i}>{msg}</div>
                                  ))}
                                </div>
                                <form onSubmit={handleRunTermCommand} className="flex items-center gap-2">
                                  <span className="text-[#00f5d4]">&gt;</span>
                                  <input
                                    type="text"
                                    value={termInput}
                                    onChange={(e) => setTermInput(e.target.value)}
                                    placeholder="Type 'scan', 'status', 'help'..."
                                    className="flex-1 bg-transparent text-white border-0 outline-none text-xs"
                                  />
                                  <button
                                    type="submit"
                                    className="px-2 py-0.5 rounded bg-[#00f5d4] text-black text-[10px] font-bold cursor-pointer"
                                  >
                                    Run
                                  </button>
                                </form>
                              </div>
                            </div>

                            {/* Col 3: Tools & Resources */}
                            <div className="md:col-span-3 bg-[#0b1120] border border-slate-800 rounded-xl p-3 space-y-2.5">
                              <div className="text-[11px] font-mono text-purple-400 font-bold uppercase">
                                ডেভেলপার টুলস ও রিসোর্স
                              </div>
                              <div className="space-y-2 text-[11px]">
                                <div className="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">
                                  <span className="text-white font-semibold block">⚡ Dynamic SVG Generator</span>
                                  <span className="text-[10px] text-slate-400">Red Callout Arrows (#ff3366)</span>
                                </div>
                                <div className="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">
                                  <span className="text-white font-semibold block">🛠️ 520+ Tool Processors</span>
                                  <span className="text-[10px] text-slate-400">JSON, CSS, Hash, Enc/Dec</span>
                                </div>
                                <div className="p-2 rounded bg-slate-900 border border-slate-800 text-slate-300">
                                  <span className="text-white font-semibold block">🎓 লার্নিং ব্যাজ ও পয়েন্ট</span>
                                  <span className="text-[10px] text-emerald-400">100% Gamified LMS</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      ) : (
                        /* PLUGIN LIVE CONTENT */
                        <div className="p-4 sm:p-6 space-y-5 text-xs text-slate-200">
                          {/* WP-Admin Banner */}
                          <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                            <div className="flex items-center gap-2">
                              <span className="px-2 py-0.5 rounded bg-[#7c3aed] text-white font-extrabold text-xs">
                                WP-CORE
                              </span>
                              <strong className="text-white text-sm">HackersShikkhok Core Engine Control Center</strong>
                            </div>
                            <span className="text-emerald-400 text-xs font-mono font-semibold">
                              ● Active &amp; Shielded (PHP 8.2+)
                            </span>
                          </div>

                          {/* Plugin Metric Cards */}
                          <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div className="bg-[#0b1120] p-3 rounded-xl border border-slate-800">
                              <span className="text-[10px] font-mono text-slate-400 block">DB DELTA TABLES</span>
                              <strong className="text-base text-[#00f5d4]">21 Tables</strong>
                              <span className="text-[10px] text-emerald-400 block mt-0.5">All Created &amp; Synced</span>
                            </div>
                            <div className="bg-[#0b1120] p-3 rounded-xl border border-slate-800">
                              <span className="text-[10px] font-mono text-slate-400 block">REST ENDPOINTS</span>
                              <strong className="text-base text-purple-400">28 Routes</strong>
                              <span className="text-[10px] text-slate-300 block mt-0.5">/wp-json/hackersshikkhok/v1</span>
                            </div>
                            <div className="bg-[#0b1120] p-3 rounded-xl border border-slate-800">
                              <span className="text-[10px] font-mono text-slate-400 block">NONCE GUARD</span>
                              <strong className="text-base text-emerald-400">Enabled</strong>
                              <span className="text-[10px] text-slate-300 block mt-0.5">wp_verify_nonce()</span>
                            </div>
                            <div className="bg-[#0b1120] p-3 rounded-xl border border-slate-800">
                              <span className="text-[10px] font-mono text-slate-400 block">AI AUTOPILOT</span>
                              <strong className="text-base text-amber-400">Ready</strong>
                              <span className="text-[10px] text-slate-300 block mt-0.5">Gemini 3-Stage Gate</span>
                            </div>
                          </div>

                          {/* Interactive CTF Flag Verification Engine Simulator */}
                          <div className="bg-[#0b1120] p-4 rounded-xl border border-slate-800 space-y-3">
                            <div className="flex items-center justify-between">
                              <span className="font-bold text-white flex items-center gap-2">
                                <Shield className="w-4 h-4 text-[#00f5d4]" />
                                CTF ফ্ল্যাগ ভ্যালিডেশন সিমুলেটর (Cryptographic hash_equals)
                              </span>
                              <span className="text-[10px] font-mono text-slate-400">
                                Test Flag: HS&#123;WP_CYBER_2040_SECURE_KERNEL&#125;
                              </span>
                            </div>

                            <form onSubmit={handleTestFlag} className="flex gap-2">
                              <input
                                type="text"
                                value={simulatedFlagInput}
                                onChange={(e) => setSimulatedFlagInput(e.target.value)}
                                placeholder="Paste flag e.g. HS{WP_CYBER_2040_SECURE_KERNEL}"
                                className="flex-1 px-3 py-2 rounded-lg bg-[#050811] border border-slate-700 text-white font-mono text-xs focus:border-[#00f5d4] outline-none"
                              />
                              <button
                                type="submit"
                                className="px-4 py-2 rounded-lg bg-[#00f5d4] text-[#050811] font-bold text-xs cursor-pointer"
                              >
                                ফ্ল্যাগ যাচাই করুন
                              </button>
                            </form>

                            {simulatedFlagResult && (
                              <div
                                className={`p-2.5 rounded-lg text-xs font-semibold ${
                                  simulatedFlagResult.startsWith('success')
                                    ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40'
                                    : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'
                                }`}
                              >
                                {simulatedFlagResult}
                              </div>
                            )}
                          </div>
                        </div>
                      )}
                    </div>
                  </div>
                </div>
              )}

              {/* TAB 2: FILES & TECHNICAL METADATA */}
              {activeTab === 'files' && (
                <div className="space-y-4">
                  <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-[#050811] p-3.5 rounded-xl border border-slate-800 font-mono text-xs">
                    <div>
                      <span className="text-slate-400 block text-[10px]">DEVELOPED BY</span>
                      <strong className="text-[#00f5d4]">HackersShikkhok</strong>
                    </div>
                    <div>
                      <span className="text-slate-400 block text-[10px]">VERSION</span>
                      <strong className="text-white">4.0.0 (Master Release)</strong>
                    </div>
                    <div>
                      <span className="text-slate-400 block text-[10px]">COMPATIBILITY</span>
                      <strong className="text-white">WordPress 6.4+ · PHP 8.2+</strong>
                    </div>
                    <div>
                      <span className="text-slate-400 block text-[10px]">TOTAL FILES</span>
                      <strong className="text-emerald-400">
                        {modalMode.startsWith('theme') ? `${themeFileCount + 2} Files` : `${coreFileCount + 2} Files`}
                      </strong>
                    </div>
                  </div>

                  <div className="bg-[#050811] p-4 rounded-xl border border-slate-800 space-y-2 text-xs leading-relaxed">
                    <h4 className="text-sm font-bold text-white flex items-center gap-2">
                      <Code className="w-4 h-4 text-[#00f5d4]" />
                      আর্কিটেকচার স্পেসিফিকেশন ও সিকিউরিটি কনস্ট্রাক্ট:
                    </h4>
                    <ul className="list-disc pl-5 space-y-1.5 text-slate-300">
                      <li>
                        <strong>Layered Separation:</strong> থিম শুধুমাত্র প্রেজেন্টেশন ও টেমপ্লেট হায়ারার্কির জন্য দায়ী। থিম ডিঅ্যাক্টিভেট করলেও প্লাগইনের কোন বিজনেস ডাটা, ওয়ালেট ব্যালেন্স বা কোর্স প্রগ্রেস নষ্ট হবে না।
                      </li>
                      <li>
                        <strong>Prepared Statements:</strong> সমস্ত ডাটাবেস কুয়েরিতে <code className="text-[#00f5d4]">$wpdb-&gt;prepare()</code> বাধ্যতামূলকভাবে কার্যকর করা হয়েছে।
                      </li>
                      <li>
                        <strong>Strict Nonces:</strong> প্রতিটি ফর্ম ও AJAX রিকোয়েস্টে <code className="text-[#00f5d4]">wp_verify_nonce()</code> যাচাই করা হয়।
                      </li>
                      <li>
                        <strong>Zero eval():</strong> কোন রানটাইম আনসেফ কোড এক্সিকিউশন রাখা হয়নি। সমস্ত ইউজার ইনপুট কঠোরভাবে স্যানিটাইজড।
                      </li>
                    </ul>
                  </div>
                </div>
              )}
            </div>

            {/* Modal Footer */}
            <div className="px-5 py-3.5 bg-[#050811] border-t border-slate-800 flex flex-wrap items-center justify-between gap-3">
              <span className="text-xs text-slate-400">
                Official Brand:{' '}
                <a
                  href="https://hackersshikkhok.com"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="text-[#00f5d4] underline"
                >
                  HackersShikkhok.com
                </a>
              </span>

              <div className="flex items-center gap-2">
                <button
                  onClick={() => setModalMode(null)}
                  className="px-4 py-2 rounded-lg bg-slate-800 text-slate-300 font-semibold text-xs cursor-pointer hover:bg-slate-700 transition-colors"
                >
                  বন্ধ করুন
                </button>
                <button
                  onClick={() => {
                    onDownloadPackage(modalMode.startsWith('theme') ? 'theme' : 'core');
                    setModalMode(null);
                  }}
                  className="px-4 py-2 rounded-lg bg-[#00f5d4] text-[#050811] font-extrabold text-xs flex items-center gap-1.5 cursor-pointer shadow-[0_0_15px_rgba(0,245,212,0.3)] hover:bg-[#00f5d4]/90 transition-all"
                >
                  <Download className="w-4 h-4" />
                  {modalMode.startsWith('theme')
                    ? 'Download hackersshikkhok-theme.zip'
                    : 'Download hackersshikkhok-core.zip'}
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </section>
  );
};
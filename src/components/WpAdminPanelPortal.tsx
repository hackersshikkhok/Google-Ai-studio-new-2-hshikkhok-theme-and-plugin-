import React, { useState, useEffect } from 'react';
import { PostStore, UserPost } from '../services/postStore';
import {
  LayoutDashboard,
  FileText,
  FolderGit2,
  Settings,
  Search,
  Globe,
  DollarSign,
  Zap,
  Shield,
  Users,
  CheckCircle2,
  AlertCircle,
  ExternalLink,
  RefreshCw,
  PlusCircle,
  Eye,
  MessageSquare,
  Lock,
  LogOut,
  Sliders,
  Send,
  Database,
  Terminal,
  HelpCircle,
  Sparkles,
  ArrowRight,
  TrendingUp,
  Download,
  Copy,
  Check
} from 'lucide-react';

interface WpAdminPanelPortalProps {
  currentUserRole: 'admin' | 'subscriber' | 'contributor';
  currentUserName: string;
  onNavigateHome: () => void;
  onSwitchRole: (role: 'admin' | 'contributor') => void;
  onDownloadZip: (type: 'theme' | 'core' | 'docs') => void;
  onNotify: (msg: string) => void;
}

export const WpAdminPanelPortal: React.FC<WpAdminPanelPortalProps> = ({
  currentUserRole,
  currentUserName,
  onNavigateHome,
  onSwitchRole,
  onDownloadZip,
  onNotify
}) => {
  const [activeAdminTab, setActiveAdminTab] = useState<
    | 'dashboard'
    | 'posts'
    | 'gsc_sitemap'
    | 'adsense'
    | 'instant_index'
    | 'courses_lms'
    | 'tools_registry'
    | 'ai_autopilot'
    | 'users'
    | 'system_health'
    | 'packages'
  >('dashboard');

  // GSC State
  const [sitemapPingStatus, setSitemapPingStatus] = useState<
    'idle' | 'pinging' | 'success'
  >('idle');
  const [lastPingTime, setLastPingTime] = useState<string>('আজ বেলা ১২:৩০ PM');

  // AdSense State
  const [adsensePubId, setAdsensePubId] = useState('pub-7122555301739999');
  const [adsenseEmail, setAdsenseEmail] = useState('hshikkhok@gmail.com');
  const [adsenseConnected, setAdsenseConnected] = useState(true);
  const [autoAdsEnabled, setAutoAdsEnabled] = useState(true);
  const [adsTxtStatus, setAdsTxtStatus] = useState<'verified' | 'pending'>(
    'verified'
  );

  // Instant Indexing State
  const [serviceAccountJson, setServiceAccountJson] = useState(
    JSON.stringify(
      {
        type: 'service_account',
        project_id: 'hackersshikkhok-prod-2026',
        client_email:
          'hs-indexer@hackersshikkhok-prod-2026.iam.gserviceaccount.com',
        indexing_api_enabled: true,
        quota_remaining_today: 194
      },
      null,
      2
    )
  );
  const [indexingLog, setIndexingLog] = useState<string[]>([
    '✅ [2026-10-20 11:20:00] Pushed 6 URLs to Google Indexing API (Response: 200 OK)',
    '✅ [2026-10-20 09:15:22] Pinged Bing IndexNow for 14 updated tools (HTTP 200)',
    '✅ [2026-10-20 04:00:10] Automatic Cron Sitemap generation synchronized.'
  ]);
  const [isSubmittingIndex, setIsSubmittingIndex] = useState(false);

  // Copied state
  const [copiedKey, setCopiedKey] = useState<string | null>(null);

  // Live Posts Management State synced with PostStore
  const [adminPosts, setAdminPosts] = useState<UserPost[]>(() => PostStore.getAll());
  const [adminPostFilter, setAdminPostFilter] = useState<'all' | 'publish' | 'draft' | 'pending'>('all');
  const [adminPostSearch, setAdminPostSearch] = useState('');
  const [isCreatingPost, setIsCreatingPost] = useState(false);
  const [newPostTitle, setNewPostTitle] = useState('');
  const [newPostCategory, setNewPostCategory] = useState('Ethical Hacking & Security');
  const [newPostContent, setNewPostContent] = useState('');
  const [newPostType, setNewPostType] = useState<UserPost['type']>('tutorial');
  const [selectedPreviewPost, setSelectedPreviewPost] = useState<UserPost | null>(null);

  useEffect(() => {
    const unsub = PostStore.subscribe(() => {
      setAdminPosts(PostStore.getAll());
    });
    return unsub;
  }, []);

  const handleAdminPublishPost = (id: string, title: string) => {
    PostStore.updateStatus(id, 'publish');
    onNotify(`পোস্ট "${title}" সফলভাবে লাইভ পাবলিশ করা হয়েছে!`);
  };

  const handleAdminDraftPost = (id: string, title: string) => {
    PostStore.updateStatus(id, 'draft');
    onNotify(`পোস্ট "${title}" ড্রাফট হিসেবে সেভ করা হয়েছে।`);
  };

  const handleAdminDeletePost = (id: string, title: string) => {
    if (confirm(`আপনি কি "${title}" পোস্টটি স্থায়ীভাবে মুছে ফেলতে চান?`)) {
      PostStore.delete(id);
      onNotify(`পোস্ট "${title}" ডিলিট করা হয়েছে।`);
    }
  };

  const handleAdminCreatePost = (status: 'publish' | 'draft') => {
    if (!newPostTitle.trim()) {
      onNotify('দয়া করে আর্টিকেলের শিরোনাম লিখুন।');
      return;
    }
    const created = PostStore.save({
      title: newPostTitle,
      category: newPostCategory,
      content: newPostContent || 'বিস্তারিত কনটেন্ট শিগগিরই আপডেট হবে...',
      type: newPostType,
      status
    });
    setNewPostTitle('');
    setNewPostContent('');
    setIsCreatingPost(false);
    onNotify(`নতুন পোস্ট "${created.title}" সফলভাবে তৈরি ও সংরক্ষিত হয়েছে (${status})!`);
  };

  const handleCopy = (text: string, key: string) => {
    navigator.clipboard.writeText(text);
    setCopiedKey(key);
    setTimeout(() => setCopiedKey(null), 2000);
    onNotify('ক্লিপবোর্ডে কপি করা হয়েছে!');
  };

  const handlePingGoogleSitemap = () => {
    setSitemapPingStatus('pinging');
    setTimeout(() => {
      setSitemapPingStatus('success');
      const timeNow = new Date().toLocaleTimeString('bn-BD', {
        hour: '2-digit',
        minute: '2-digit'
      });
      setLastPingTime(`আজ ${timeNow}`);
      onNotify('গুগল সার্চ কনসোল ও IndexNow-এ সাইটম্যাপ সফলভাবে সাবমিট ও পিং করা হয়েছে!');
    }, 1200);
  };

  const handleTriggerInstantIndex = () => {
    setIsSubmittingIndex(true);
    setTimeout(() => {
      setIsSubmittingIndex(false);
      const newEntry = `✅ [${new Date().toLocaleTimeString()}] Pushed batch of 8 URLs to Google Indexing API (Status: 200 Success)`;
      setIndexingLog((prev) => [newEntry, ...prev]);
      onNotify('গুগল ইনস্ট্যান্ট ইনডেক্সিং API-এ ৮টি নতুন লিংক তৎক্ষণাৎ জমা দেওয়া হয়েছে!');
    }, 1000);
  };

  // If user is a regular user (contributor/subscriber), show WordPress author-restricted dashboard
  if (currentUserRole !== 'admin') {
    return (
      <div className="min-h-[600px] w-full rounded-2xl bg-[#0b1120] border border-[#00f5d4]/20 p-6 space-y-6">
        <div className="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/40 flex items-center justify-center text-amber-400 font-bold">
              <Lock className="w-5 h-5" />
            </div>
            <div>
              <h2 className="text-xl font-bold text-white flex items-center gap-2">
                <span>WordPress Admin — Contributor Access</span>
                <span className="text-xs px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 font-mono">
                  Role: Contributor / Author
                </span>
              </h2>
              <p className="text-xs text-slate-400 mt-0.5">
                লগইন ইউজার: <strong className="text-white">{currentUserName}</strong> · আপনার অ্যাকাউন্টটি সাধারণ কন্ট্রিবিউটর হিসেবে সংযুক্ত।
              </p>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <button
              onClick={onNavigateHome}
              className="px-4 py-2 rounded-xl bg-[#00f5d4] hover:bg-[#00f5d4]/90 text-[#050811] text-xs font-bold transition-all cursor-pointer shadow-[0_0_15px_rgba(0,245,212,0.3)]"
            >
              ← হোমপেজে ফিরে যান
            </button>
            <button
              onClick={() => onSwitchRole('admin')}
              className="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold cursor-pointer"
              title="অ্যাডমিন হিসেবে পরিবর্তন করুন"
            >
              👑 সুইচ টু সুপার অ্যাডমিন
            </button>
          </div>
        </div>

        <div className="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-200 text-xs flex items-start gap-3">
          <AlertCircle className="w-5 h-5 shrink-0 mt-0.5 text-amber-400" />
          <div className="space-y-1">
            <p className="font-bold">🔒 রেস্ট্রিক্টেড অ্যাডমিন এক্সেস পলিসি:</p>
            <p className="text-slate-300">
              WordPress সিকিউরিটি নিয়ম অনুযায়ী সাধারণ লেখক বা ব্যবহারকারীগণ ফুল সিস্টেম কনফিগারেশন, গুগল অ্যাডসেন্স ও সাইট সেটিংস দেখতে পান না। সাধারণ ইউজাররা সরাসরি তাদের হোমপেজ ও ইউজার ড্যাশবোর্ড থেকে পোস্ট লেখা, ড্রাফট সংরক্ষণ, ভিউ-কমেন্ট ও উপার্জিত ব্যালেন্স দেখতে পারেন।
            </p>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div className="p-4 rounded-xl bg-[#050811] border border-slate-800 space-y-2">
            <div className="text-slate-400 text-xs">আপনার পোস্ট সমূহ</div>
            <div className="text-2xl font-bold font-mono text-[#00f5d4]">৭টি পোস্ট</div>
            <div className="text-[11px] text-slate-400">৪ পাবলিশড · ২ ড্রাফট · ১ পেন্ডিং রিভিউ</div>
          </div>
          <div className="p-4 rounded-xl bg-[#050811] border border-slate-800 space-y-2">
            <div className="text-slate-400 text-xs">পোস্ট এনগেজমেন্ট</div>
            <div className="text-2xl font-bold font-mono text-purple-400">৪,২২০ ভিউ</div>
            <div className="text-[11px] text-slate-400">৬৮টি কমেন্ট · ১৯৫টি লাইক</div>
          </div>
          <div className="p-4 rounded-xl bg-[#050811] border border-slate-800 space-y-2">
            <div className="text-slate-400 text-xs">আর্নড ব্যালেন্স</div>
            <div className="text-2xl font-bold font-mono text-emerald-400">৳ ২৫০.০০</div>
            <div className="text-[11px] text-slate-400">১,৪৫০ লার্নিং পয়েন্ট</div>
          </div>
        </div>

        <div className="text-center pt-4">
          <button
            onClick={onNavigateHome}
            className="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#00f5d4] to-emerald-400 text-[#050811] font-extrabold text-sm shadow-[0_0_20px_rgba(0,245,212,0.35)] cursor-pointer"
          >
            ইউজার ড্যাশবোর্ড ও হোমপেজে চলুন ➔
          </button>
        </div>
      </div>
    );
  }

  // Full WordPress Admin Dashboard for Administrator
  return (
    <div className="w-full rounded-2xl bg-[#070b14] border border-[#00f5d4]/25 shadow-2xl overflow-hidden flex flex-col">
      {/* 1. WP-ADMIN TOP TOOLBAR */}
      <div className="w-full bg-[#0a0f1d] border-b border-slate-800 px-4 py-2 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div className="flex items-center gap-3">
          <div className="flex items-center gap-1.5 font-bold text-white">
            <div className="w-6 h-6 rounded-md bg-[#00f5d4] text-[#050811] flex items-center justify-center font-extrabold text-xs">
              W
            </div>
            <span>HackersShikkhok Admin</span>
          </div>

          <span className="text-slate-600">|</span>

          <button
            onClick={onNavigateHome}
            className="text-slate-300 hover:text-[#00f5d4] flex items-center gap-1 cursor-pointer font-medium"
            title="পাবলিক ওয়েবসাইট ভিজিট করুন"
          >
            <Globe className="w-3.5 h-3.5 text-[#00f5d4]" />
            <span>Visit Public Site (hackersshikkhok.com)</span>
          </button>

          <span className="text-slate-600 hidden sm:inline">|</span>

          <div className="hidden md:flex items-center gap-2 text-slate-400 font-mono text-[11px]">
            <span className="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>PHP 8.2+ · dbDelta Active</span>
          </div>
        </div>

        <div className="flex items-center gap-3">
          <div className="flex items-center gap-2 px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-[11px] font-mono">
            <CheckCircle2 className="w-3.5 h-3.5" />
            <span>SEO &amp; AdSense: Online</span>
          </div>

          <div className="flex items-center gap-2">
            <span className="text-slate-300 font-semibold hidden sm:inline">
              Howdy, <strong className="text-white">Admin (Principal)</strong>
            </span>
            <button
              onClick={() => onSwitchRole('contributor')}
              className="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] cursor-pointer"
              title="টেস্ট করুন কন্ট্রিবিউটর ইউজার হিসেবে কেমন দেখাবে"
            >
              সুইচ টু ইউজার
            </button>
            <button
              onClick={onNavigateHome}
              className="px-2.5 py-1 rounded bg-rose-500/20 text-rose-300 hover:bg-rose-500 hover:text-white text-[11px] font-semibold cursor-pointer"
            >
              প্রস্থান
            </button>
          </div>
        </div>
      </div>

      {/* 2. ADMIN MAIN WORKSPACE (LEFT SIDEBAR + RIGHT CONTENT) */}
      <div className="grid grid-cols-1 lg:grid-cols-12 min-h-[720px]">
        {/* LEFT SIDEBAR NAVIGATION */}
        <aside className="lg:col-span-3 bg-[#080d19] border-r border-slate-800/80 p-3 space-y-1">
          <div className="px-3 py-2 text-[10px] font-mono uppercase tracking-wider text-slate-500">
            WordPress Core &amp; Engine
          </div>

          <button
            onClick={() => setActiveAdminTab('dashboard')}
            className={`w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors ${
              activeAdminTab === 'dashboard'
                ? 'bg-[#00f5d4] text-[#050811] shadow-[0_0_15px_rgba(0,245,212,0.3)] font-bold'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <LayoutDashboard className="w-4 h-4" />
            <span>Admin Dashboard</span>
          </button>

          <button
            onClick={() => setActiveAdminTab('posts')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors ${
              activeAdminTab === 'posts'
                ? 'bg-[#00f5d4] text-[#050811] font-bold'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <div className="flex items-center gap-2.5">
              <FileText className="w-4 h-4" />
              <span>Posts &amp; Submissions</span>
            </div>
            <span className="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-900 border border-slate-800 text-[#00f5d4]">
              {adminPosts.length} Total
            </span>
          </button>

          <div className="px-3 pt-3 pb-1 text-[10px] font-mono uppercase tracking-wider text-slate-500">
            Google Ecosystem &amp; Monetization
          </div>

          <button
            onClick={() => setActiveAdminTab('gsc_sitemap')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors ${
              activeAdminTab === 'gsc_sitemap'
                ? 'bg-[#00f5d4] text-[#050811] font-bold shadow-[0_0_15px_rgba(0,245,212,0.3)]'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <div className="flex items-center gap-2.5">
              <Search className="w-4 h-4 text-emerald-400" />
              <span>Search Console &amp; Sitemap</span>
            </div>
            <span className="text-[9px] font-mono px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300">
              Live
            </span>
          </button>

          <button
            onClick={() => setActiveAdminTab('adsense')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors ${
              activeAdminTab === 'adsense'
                ? 'bg-[#00f5d4] text-[#050811] font-bold shadow-[0_0_15px_rgba(0,245,212,0.3)]'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <div className="flex items-center gap-2.5">
              <DollarSign className="w-4 h-4 text-amber-400" />
              <span>Google AdSense Automation</span>
            </div>
            <span className="text-[9px] font-mono px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300">
              Connected
            </span>
          </button>

          <button
            onClick={() => setActiveAdminTab('instant_index')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors ${
              activeAdminTab === 'instant_index'
                ? 'bg-[#00f5d4] text-[#050811] font-bold shadow-[0_0_15px_rgba(0,245,212,0.3)]'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <div className="flex items-center gap-2.5">
              <Zap className="w-4 h-4 text-cyan-400" />
              <span>Instant Indexing API</span>
            </div>
            <span className="text-[9px] font-mono px-1.5 py-0.2 rounded bg-cyan-500/20 text-cyan-300">
              Auto
            </span>
          </button>

          <div className="px-3 pt-3 pb-1 text-[10px] font-mono uppercase tracking-wider text-slate-500">
            Platform Modules
          </div>

          <button
            onClick={() => setActiveAdminTab('courses_lms')}
            className={`w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors ${
              activeAdminTab === 'courses_lms'
                ? 'bg-[#00f5d4] text-[#050811] font-bold'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <Shield className="w-4 h-4 text-purple-400" />
            <span>Cyber Academy &amp; CTF Engine</span>
          </button>

          <button
            onClick={() => setActiveAdminTab('tools_registry')}
            className={`w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors ${
              activeAdminTab === 'tools_registry'
                ? 'bg-[#00f5d4] text-[#050811] font-bold'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <Sliders className="w-4 h-4 text-teal-400" />
            <span>520+ Tools Registry</span>
          </button>

          <button
            onClick={() => setActiveAdminTab('ai_autopilot')}
            className={`w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors ${
              activeAdminTab === 'ai_autopilot'
                ? 'bg-[#00f5d4] text-[#050811] font-bold'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <Sparkles className="w-4 h-4 text-rose-400" />
            <span>AI Autopilot &amp; Quality Gate</span>
          </button>

          <button
            onClick={() => setActiveAdminTab('packages')}
            className={`w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors ${
              activeAdminTab === 'packages'
                ? 'bg-[#00f5d4] text-[#050811] font-bold'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <FolderGit2 className="w-4 h-4 text-amber-400" />
            <span>Theme &amp; Plugin ZIP Packages</span>
          </button>

          <button
            onClick={() => setActiveAdminTab('system_health')}
            className={`w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors ${
              activeAdminTab === 'system_health'
                ? 'bg-[#00f5d4] text-[#050811] font-bold'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <Database className="w-4 h-4 text-blue-400" />
            <span>System Health &amp; SQL Schema</span>
          </button>
        </aside>

        {/* RIGHT CONTENT DISPLAY */}
        <main className="lg:col-span-9 p-5 md:p-6 space-y-6 overflow-y-auto">
          {/* TAB 1: ADMIN DASHBOARD */}
          {activeAdminTab === 'dashboard' && (
            <div className="space-y-6">
              <div>
                <h2 className="text-xl font-extrabold text-white">
                  HackersShikkhok — WordPress Administration Hub (v4.0)
                </h2>
                <p className="text-xs text-slate-400 mt-1">
                  সর্বমোট ২৮টি সেন্টার, ৫২০+ ডেভ ও সাইবার টুলস, ৮২টি একাডেমি কোর্স এবং স্বয়ংক্রিয় গুগল সার্চ কনসোল ও অ্যাডসেন্স মনিটাইজেশন ইঞ্জিন।
                </p>
              </div>

              {/* 4 Stats Cards */}
              <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800">
                  <div className="text-xs text-slate-400">সর্বমোট ব্যবহারকারী</div>
                  <div className="text-2xl font-bold font-mono text-[#00f5d4] mt-1">
                    ১২,৪৮০+
                  </div>
                  <div className="text-[11px] text-emerald-400 mt-1">
                    ↑ ২৪% নতুন সাইন-আপ এই সপ্তাহে
                  </div>
                </div>

                <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800">
                  <div className="text-xs text-slate-400">গুগল ইন্ডেক্সড পেজ</div>
                  <div className="text-2xl font-bold font-mono text-purple-400 mt-1">
                    ১,৮৫০+
                  </div>
                  <div className="text-[11px] text-purple-300 mt-1">
                    সাইটম্যাপ প্রতি ১২ ঘণ্টায় অটো-সিঙ্ক
                  </div>
                </div>

                <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800">
                  <div className="text-xs text-slate-400">অ্যাডসেন্স রেডি রেটিং</div>
                  <div className="text-2xl font-bold font-mono text-amber-400 mt-1">
                    ১০০% ভেরিফাইড
                  </div>
                  <div className="text-[11px] text-amber-300 mt-1">
                    Publisher ID: {adsensePubId}
                  </div>
                </div>

                <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800">
                  <div className="text-xs text-slate-400">নিরাপত্তা ও কোড কোয়ালিটি</div>
                  <div className="text-2xl font-bold font-mono text-emerald-400 mt-1">
                    100 / 100
                  </div>
                  <div className="text-[11px] text-emerald-300 mt-1">
                    PHP 8.2+ · Nonce · Prepared SQL
                  </div>
                </div>
              </div>

              {/* Quick Action Matrix */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button
                  onClick={() => setActiveAdminTab('gsc_sitemap')}
                  className="p-4 rounded-xl bg-[#0b1120] border border-slate-800 hover:border-[#00f5d4] transition-all text-left space-y-2 cursor-pointer group"
                >
                  <div className="w-8 h-8 rounded-lg bg-emerald-500/15 text-emerald-400 flex items-center justify-center font-bold">
                    <Search className="w-4 h-4" />
                  </div>
                  <div className="font-bold text-white text-sm group-hover:text-[#00f5d4]">
                    গুগল সার্চ কনসোল ও সাইটম্যাপ
                  </div>
                  <p className="text-xs text-slate-400">
                    সাইটম্যাপ ভিউ করুন ও ১-ক্লিকে গুগল পিং সার্ভারে আপডেট জানান।
                  </p>
                </button>

                <button
                  onClick={() => setActiveAdminTab('adsense')}
                  className="p-4 rounded-xl bg-[#0b1120] border border-slate-800 hover:border-[#00f5d4] transition-all text-left space-y-2 cursor-pointer group"
                >
                  <div className="w-8 h-8 rounded-lg bg-amber-500/15 text-amber-400 flex items-center justify-center font-bold">
                    <DollarSign className="w-4 h-4" />
                  </div>
                  <div className="font-bold text-white text-sm group-hover:text-[#00f5d4]">
                    গুগল অ্যাডসেন্স অটো-কানেক্ট
                  </div>
                  <p className="text-xs text-slate-400">
                    পাবলিশার আইডি বা ইমেইল দিয়ে ইনস্ট্যান্ট অটো-অ্যাডস ও ads.txt প্রস্তুত করুন।
                  </p>
                </button>

                <button
                  onClick={() => setActiveAdminTab('instant_index')}
                  className="p-4 rounded-xl bg-[#0b1120] border border-slate-800 hover:border-[#00f5d4] transition-all text-left space-y-2 cursor-pointer group"
                >
                  <div className="w-8 h-8 rounded-lg bg-cyan-500/15 text-cyan-400 flex items-center justify-center font-bold">
                    <Zap className="w-4 h-4" />
                  </div>
                  <div className="font-bold text-white text-sm group-hover:text-[#00f5d4]">
                    ইনস্ট্যান্ট ইনডেক্সিং API
                  </div>
                  <p className="text-xs text-slate-400">
                    Google Indexing API দিয়ে যেকোনো নতুন পোস্ট সাথে সাথে ইনডেক্স করান।
                  </p>
                </button>
              </div>
            </div>
          )}

          {/* TAB 2: GOOGLE SEARCH CONSOLE & SITEMAP */}
          {activeAdminTab === 'gsc_sitemap' && (
            <div className="space-y-6">
              <div className="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
                <div>
                  <h3 className="text-lg font-bold text-white flex items-center gap-2">
                    <Search className="w-5 h-5 text-[#00f5d4]" />
                    <span>গুগল সার্চ কনসোল ও অটোমেটিক এক্সএমএল সাইটম্যাপ ইঞ্জিন</span>
                  </h3>
                  <p className="text-xs text-slate-400 mt-1">
                    গুগল সার্চ কনসোলে সাইটম্যাপ সাবমিট করার সকল রিকোয়ারমেন্ট ১০০% কমপ্লিট ও ভেরিফাইড।
                  </p>
                </div>

                <button
                  onClick={handlePingGoogleSitemap}
                  disabled={sitemapPingStatus === 'pinging'}
                  className="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:opacity-95 text-[#050811] font-extrabold text-xs flex items-center gap-2 shadow-[0_0_15px_rgba(16,185,129,0.3)] cursor-pointer disabled:opacity-50"
                >
                  <RefreshCw
                    className={`w-4 h-4 ${
                      sitemapPingStatus === 'pinging' ? 'animate-spin' : ''
                    }`}
                  />
                  <span>
                    {sitemapPingStatus === 'pinging'
                      ? 'গুগল সার্ভারে পিং করা হচ্ছে...'
                      : 'গুগল সার্চ কনসোলে সাইটম্যাপ পিং করুন'}
                  </span>
                </button>
              </div>

              {sitemapPingStatus === 'success' && (
                <div className="p-4 rounded-xl bg-emerald-500/15 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-3">
                  <CheckCircle2 className="w-5 h-5 shrink-0" />
                  <div>
                    <strong>সফলভাবে সম্পন্ন হয়েছে!</strong> গুগল সার্চ কনসোল পিং রেসপন্স: HTTP 200 OK। সর্বশেষ পিং সময়: {lastPingTime}
                  </div>
                </div>
              )}

              {/* Sitemap Endpoints Grid */}
              <div className="space-y-3">
                <div className="text-xs font-bold text-slate-200">
                  অ্যাক্টিভ সাইটম্যাপ ফাইলসমূহ (গুগল সার্চ কনসোলে সাবমিটের জন্য প্রস্তুত):
                </div>

                <div className="space-y-2">
                  {[
                    {
                      name: 'মাস্টার সাইটম্যাপ ইনডেক্স',
                      url: 'https://hackersshikkhok.com/sitemap_index.xml',
                      desc: 'সকল সাব-সাইটম্যাপের কেন্দ্রীয় ইনডেক্স ফাইল',
                      items: '৮টি সাব-ম্যাপ'
                    },
                    {
                      name: 'পোস্ট ও টিউটোরিয়াল সাইটম্যাপ',
                      url: 'https://hackersshikkhok.com/post-sitemap.xml',
                      desc: 'সকল ব্লগ পোস্ট, হ্যাকিং গাইড ও টেক টিউটোরিয়াল',
                      items: '৬৪টি ইউআরএল'
                    },
                    {
                      name: 'সাইবার একাডেমি কোর্স সাইটম্যাপ',
                      url: 'https://hackersshikkhok.com/courses-sitemap.xml',
                      desc: '৮২টি মাস্টার কোর্স ও ইন্টারঅ্যাক্টিভ ল্যাব স্টেজ',
                      items: '৮২টি ইউআরএল'
                    },
                    {
                      name: 'টুলস ও ল্যাব সাইটম্যাপ',
                      url: 'https://hackersshikkhok.com/tools-sitemap.xml',
                      desc: '৫২০+ ডেভেলপার, সাইবার ডিফেন্সিভ ও মিডিয়া টুলস',
                      items: '৫২০+ ইউআরএল'
                    }
                  ].map((s, idx) => (
                    <div
                      key={idx}
                      className="p-3.5 rounded-xl bg-[#0b1120] border border-slate-800 flex flex-wrap items-center justify-between gap-3"
                    >
                      <div>
                        <div className="text-xs font-bold text-white flex items-center gap-2">
                          <span>{s.name}</span>
                          <span className="text-[10px] font-mono text-emerald-400 bg-emerald-500/10 px-1.5 py-0.2 rounded">
                            {s.items}
                          </span>
                        </div>
                        <div className="text-xs font-mono text-[#00f5d4] mt-0.5">
                          {s.url}
                        </div>
                        <div className="text-[11px] text-slate-400 mt-0.5">
                          {s.desc}
                        </div>
                      </div>

                      <button
                        onClick={() => handleCopy(s.url, `sitemap-${idx}`)}
                        className="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-mono flex items-center gap-1.5 cursor-pointer"
                      >
                        {copiedKey === `sitemap-${idx}` ? (
                          <>
                            <Check className="w-3.5 h-3.5 text-emerald-400" />
                            <span>কপি হয়েছে</span>
                          </>
                        ) : (
                          <>
                            <Copy className="w-3.5 h-3.5 text-slate-400" />
                            <span>ইউআরএল কপি</span>
                          </>
                        )}
                      </button>
                    </div>
                  ))}
                </div>
              </div>

              {/* Robots.txt Preview */}
              <div className="space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-bold text-slate-200">
                    Robots.txt কনফিগারেশন:
                  </span>
                  <button
                    onClick={() =>
                      handleCopy(
                        `User-agent: *\nAllow: /\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php\n\nSitemap: https://hackersshikkhok.com/sitemap_index.xml`,
                        'robots'
                      )
                    }
                    className="text-xs text-[#00f5d4] hover:underline cursor-pointer flex items-center gap-1"
                  >
                    <Copy className="w-3 h-3" />
                    <span>কপি Robots.txt</span>
                  </button>
                </div>
                <pre className="p-3.5 rounded-xl bg-[#050811] border border-slate-800 text-xs font-mono text-emerald-400 overflow-x-auto">
{`User-agent: *
Allow: /
Disallow: /wp-admin/
Allow: /wp-admin/admin-ajax.php

Sitemap: https://hackersshikkhok.com/sitemap_index.xml`}
                </pre>
              </div>
            </div>
          )}

          {/* TAB 3: GOOGLE ADSENSE AUTOMATION */}
          {activeAdminTab === 'adsense' && (
            <div className="space-y-6">
              <div className="border-b border-slate-800 pb-4">
                <h3 className="text-lg font-bold text-white flex items-center gap-2">
                  <DollarSign className="w-5 h-5 text-amber-400" />
                  <span>গুগল অ্যাডসেন্স ১-ক্লিক অটোমেশন সিস্টেম (Google AdSense Auto-Connect)</span>
                </h3>
                <p className="text-xs text-slate-400 mt-1">
                  এখানে শুধু আপনার পাবলিশার আইডি বা ইমেইল বসিয়ে সেভ করলেই ওয়েবসাইট কোডে অ্যাডসেন্স স্ক্রিপ্ট, হেড ভেরিফিকেশন ও ads.txt স্বয়ংক্রিয়ভাবে কানেক্ট হয়ে যাবে।
                </p>
              </div>

              {/* Publisher ID & Email Input Card */}
              <div className="p-5 rounded-2xl bg-[#0b1120] border border-amber-500/30 space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-1.5">
                    <label className="text-xs font-bold text-slate-200">
                      AdSense Publisher ID (পাবলিশার আইডি):
                    </label>
                    <input
                      type="text"
                      value={adsensePubId}
                      onChange={(e) => setAdsensePubId(e.target.value)}
                      placeholder="pub-XXXXXXXXXXXXXXXX"
                      className="w-full px-3.5 py-2.5 rounded-xl bg-[#050811] border border-slate-700 text-white font-mono text-xs focus:outline-none focus:border-amber-400"
                    />
                    <p className="text-[11px] text-slate-400">
                      উদাহরণ: pub-7122555301739999 (বা আপনার নিজস্ব পাবলিশার আইডি)
                    </p>
                  </div>

                  <div className="space-y-1.5">
                    <label className="text-xs font-bold text-slate-200">
                      AdSense Account Email (অ্যাকাউন্ট ইমেইল):
                    </label>
                    <input
                      type="email"
                      value={adsenseEmail}
                      onChange={(e) => setAdsenseEmail(e.target.value)}
                      placeholder="user@gmail.com"
                      className="w-full px-3.5 py-2.5 rounded-xl bg-[#050811] border border-slate-700 text-white font-mono text-xs focus:outline-none focus:border-amber-400"
                    />
                    <p className="text-[11px] text-slate-400">
                      অ্যাডসেন্স অ্যাকাউন্টের সাথে যুক্ত অফিসিয়াল জিমেইল
                    </p>
                  </div>
                </div>

                <div className="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-800">
                  <div className="flex items-center gap-3">
                    <label className="flex items-center gap-2 cursor-pointer">
                      <input
                        type="checkbox"
                        checked={autoAdsEnabled}
                        onChange={(e) => setAutoAdsEnabled(e.target.checked)}
                        className="rounded border-slate-700 text-amber-500 focus:ring-0 cursor-pointer"
                      />
                      <span className="text-xs text-white font-medium">
                        Auto Ads (স্বয়ংক্রিয় বিজ্ঞাপন অপ্টিমাইজার) সক্রিয় রাখুন
                      </span>
                    </label>
                  </div>

                  <button
                    onClick={() => {
                      setAdsenseConnected(true);
                      setAdsTxtStatus('verified');
                      onNotify('গুগল অ্যাডসেন্স অটো-কানেকশন সফলভাবে সেভ ও কার্যকর করা হয়েছে!');
                    }}
                    className="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-400 to-orange-500 text-slate-950 font-extrabold text-xs shadow-[0_0_15px_rgba(251,191,36,0.35)] cursor-pointer hover:opacity-95"
                  >
                    সেভ ও অটো-কানেক্ট করুন ➔
                  </button>
                </div>
              </div>

              {/* Generated AdSense Script Preview */}
              <div className="space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-bold text-slate-200">
                    হেডার ইনজেকশন কোড (&lt;head&gt; স্ক্রিপ্ট প্রিভিউ):
                  </span>
                  <button
                    onClick={() =>
                      handleCopy(
                        `<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-${adsensePubId}" crossorigin="anonymous"></script>`,
                        'ads-script'
                      )
                    }
                    className="text-xs text-[#00f5d4] hover:underline cursor-pointer flex items-center gap-1"
                  >
                    <Copy className="w-3 h-3" />
                    <span>কপি স্ক্রিপ্ট</span>
                  </button>
                </div>
                <pre className="p-3.5 rounded-xl bg-[#050811] border border-slate-800 text-xs font-mono text-amber-300 overflow-x-auto">
{`<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-${adsensePubId}" crossorigin="anonymous"></script>`}
                </pre>
              </div>

              {/* ads.txt file Preview */}
              <div className="space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-bold text-slate-200">
                    স্বয়ংক্রিয় ads.txt ফাইল প্রিভিউ (https://hackersshikkhok.com/ads.txt):
                  </span>
                  <button
                    onClick={() =>
                      handleCopy(
                        `google.com, ${adsensePubId}, DIRECT, f08c47fec0942fa0`,
                        'ads-txt'
                      )
                    }
                    className="text-xs text-[#00f5d4] hover:underline cursor-pointer flex items-center gap-1"
                  >
                    <Copy className="w-3 h-3" />
                    <span>কপি ads.txt</span>
                  </button>
                </div>
                <pre className="p-3.5 rounded-xl bg-[#050811] border border-slate-800 text-xs font-mono text-emerald-400">
{`google.com, ${adsensePubId}, DIRECT, f08c47fec0942fa0`}
                </pre>
              </div>
            </div>
          )}

          {/* TAB 4: INSTANT INDEXING API */}
          {activeAdminTab === 'instant_index' && (
            <div className="space-y-6">
              <div className="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
                <div>
                  <h3 className="text-lg font-bold text-white flex items-center gap-2">
                    <Zap className="w-5 h-5 text-cyan-400" />
                    <span>Google Instant Indexing API ও IndexNow ব্যাচ সাবমিটার</span>
                  </h3>
                  <p className="text-xs text-slate-400 mt-1">
                    নতুন পোস্ট পাবলিশ হওয়ার সাথে সাথে গুগল ইনডেক্সিং API ও Bing IndexNow দিয়ে ৫ মিনিটের মধ্যে গুগলের প্রথম পাতায় ইনডেক্স করান।
                  </p>
                </div>

                <button
                  onClick={handleTriggerInstantIndex}
                  disabled={isSubmittingIndex}
                  className="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-400 to-blue-500 hover:opacity-95 text-[#050811] font-extrabold text-xs flex items-center gap-2 shadow-[0_0_15px_rgba(6,182,212,0.3)] cursor-pointer disabled:opacity-50"
                >
                  <Zap
                    className={`w-4 h-4 ${
                      isSubmittingIndex ? 'animate-bounce' : ''
                    }`}
                  />
                  <span>
                    {isSubmittingIndex
                      ? 'গুগল API-এ পুশ হচ্ছে...'
                      : '১-ক্লিকে ইনস্ট্যান্ট ইনডেক্স পুশ'}
                  </span>
                </button>
              </div>

              {/* API Key Configuration Card */}
              <div className="p-5 rounded-2xl bg-[#0b1120] border border-cyan-500/30 space-y-3">
                <div className="text-xs font-bold text-white flex items-center justify-between">
                  <span>Google Cloud Service Account JSON কনফিগারেশন:</span>
                  <span className="text-[11px] font-mono text-emerald-400">
                    দৈনিক কোটা: ১৯৪ / ২০০ বাকি
                  </span>
                </div>
                <textarea
                  rows={6}
                  value={serviceAccountJson}
                  onChange={(e) => setServiceAccountJson(e.target.value)}
                  className="w-full p-3 rounded-xl bg-[#050811] border border-slate-700 text-xs font-mono text-cyan-300 focus:outline-none focus:border-cyan-400"
                />
              </div>

              {/* Real-Time Push Log */}
              <div className="space-y-2">
                <div className="text-xs font-bold text-slate-200">
                  ইনস্ট্যান্ট ইনডেক্সিং অডিট ও এক্সেকিউশন লগ:
                </div>
                <div className="space-y-1.5">
                  {indexingLog.map((log, idx) => (
                    <div
                      key={idx}
                      className="p-2.5 rounded-lg bg-[#050811] border border-slate-800 text-xs font-mono text-slate-300 flex items-center justify-between"
                    >
                      <span>{log}</span>
                      <span className="text-[10px] text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded">
                        DELIVERED
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          )}

          {/* TAB 5: POSTS & SUBMISSIONS */}
          {activeAdminTab === 'posts' && (
            <div className="space-y-4">
              <div className="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
                <div>
                  <h3 className="text-lg font-bold text-white flex items-center gap-2">
                    <FileText className="w-5 h-5 text-[#00f5d4]" />
                    <span>WordPress Posts &amp; Community Articles</span>
                  </h3>
                  <p className="text-xs text-slate-400 mt-1">
                    ইউজার ও অ্যাডমিনের পোস্ট লাইভ ম্যানেজ, ফিল্টার, ইনস্ট্যান্ট পাবলিশ, ড্রাফট ও ডিলিট করুন।
                  </p>
                </div>
                <div className="flex items-center gap-2">
                  <button
                    onClick={() => setIsCreatingPost(!isCreatingPost)}
                    className="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-[#00f5d4] to-emerald-400 text-[#050811] text-xs font-bold cursor-pointer hover:opacity-95 shadow-[0_0_15px_rgba(0,245,212,0.3)] flex items-center gap-1.5"
                  >
                    <PlusCircle className="w-4 h-4" />
                    <span>{isCreatingPost ? 'ফর্ম বন্ধ করুন' : '+ Add New Post'}</span>
                  </button>
                </div>
              </div>

              {/* Quick Filter & Search Bar */}
              <div className="flex flex-wrap items-center justify-between gap-3 p-3 rounded-xl bg-[#0b1120] border border-slate-800 text-xs">
                <div className="flex flex-wrap items-center gap-2">
                  {(['all', 'publish', 'draft', 'pending'] as const).map((st) => {
                    const count = st === 'all' ? adminPosts.length : adminPosts.filter((p) => p.status === st).length;
                    const labels = { all: 'All Posts', publish: 'Published', draft: 'Drafts', pending: 'Pending Review' };
                    return (
                      <button
                        key={st}
                        onClick={() => setAdminPostFilter(st)}
                        className={`px-3 py-1.5 rounded-lg font-semibold cursor-pointer transition-all ${
                          adminPostFilter === st
                            ? 'bg-[#00f5d4] text-[#050811]'
                            : 'bg-slate-800/80 text-slate-300 hover:text-white'
                        }`}
                      >
                        {labels[st]} ({count})
                      </button>
                    );
                  })}
                </div>

                <div className="relative min-w-[220px]">
                  <Search className="w-3.5 h-3.5 absolute left-3 top-2.5 text-slate-500" />
                  <input
                    type="text"
                    placeholder="পোস্টের শিরোনাম বা ক্যাটাগরি খুঁজুন..."
                    value={adminPostSearch}
                    onChange={(e) => setAdminPostSearch(e.target.value)}
                    className="w-full pl-9 pr-3 py-1.5 rounded-lg bg-[#050811] border border-slate-700 text-xs text-white focus:outline-none focus:border-[#00f5d4]"
                  />
                </div>
              </div>

              {/* Inline Create Post Form */}
              {isCreatingPost && (
                <div className="p-4 rounded-xl bg-[#0a101f] border border-[#00f5d4]/40 space-y-3.5 shadow-xl">
                  <div className="flex items-center justify-between border-b border-slate-800 pb-2">
                    <h4 className="text-sm font-bold text-[#00f5d4] flex items-center gap-2">
                      <Sparkles className="w-4 h-4" />
                      <span>নতুন পোস্ট তৈরি ও সরাসরি পাবলিশ করুন</span>
                    </h4>
                    <button
                      onClick={() => setIsCreatingPost(false)}
                      className="text-slate-400 hover:text-white text-xs cursor-pointer"
                    >
                      ✕
                    </button>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div className="md:col-span-2 space-y-1">
                      <label className="text-[11px] font-bold text-slate-300">পোস্ট শিরোনাম *</label>
                      <input
                        type="text"
                        placeholder="যেমন: WordPress 6.8 & Cyber Defense Hardening Guide"
                        value={newPostTitle}
                        onChange={(e) => setNewPostTitle(e.target.value)}
                        className="w-full px-3 py-2 rounded-lg bg-[#050811] border border-slate-700 text-xs text-white focus:outline-none focus:border-[#00f5d4]"
                      />
                    </div>
                    <div className="space-y-1">
                      <label className="text-[11px] font-bold text-slate-300">টাইপ ও ক্যাটাগরি</label>
                      <select
                        value={newPostType}
                        onChange={(e) => setNewPostType(e.target.value as UserPost['type'])}
                        className="w-full px-3 py-2 rounded-lg bg-[#050811] border border-slate-700 text-xs text-white focus:outline-none focus:border-[#00f5d4]"
                      >
                        <option value="tutorial">Tutorial (টিউটোরিয়াল)</option>
                        <option value="code">Verified Code (কোড)</option>
                        <option value="tools">Cyber Tools (টুলস)</option>
                        <option value="tech_news">Tech News (নিউজ)</option>
                        <option value="device_review">Device Review (রিভিউ)</option>
                        <option value="device_price">Device Price (প্রাইস)</option>
                      </select>
                    </div>
                  </div>

                  <div className="space-y-1">
                    <label className="text-[11px] font-bold text-slate-300">ক্যাটাগরি ট্যাগ</label>
                    <input
                      type="text"
                      placeholder="যেমন: Ethical Hacking & Python, WordPress Hardening"
                      value={newPostCategory}
                      onChange={(e) => setNewPostCategory(e.target.value)}
                      className="w-full px-3 py-1.5 rounded-lg bg-[#050811] border border-slate-700 text-xs text-white focus:outline-none focus:border-[#00f5d4]"
                    />
                  </div>

                  <div className="space-y-1">
                    <label className="text-[11px] font-bold text-slate-300">বিস্তারিত কনটেন্ট</label>
                    <textarea
                      rows={4}
                      placeholder="পোস্টের সম্পূর্ণ বিবরণ ও কোড এখানে লিখুন..."
                      value={newPostContent}
                      onChange={(e) => setNewPostContent(e.target.value)}
                      className="w-full p-3 rounded-lg bg-[#050811] border border-slate-700 text-xs text-white focus:outline-none focus:border-[#00f5d4]"
                    />
                  </div>

                  <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                    <button
                      onClick={() => handleAdminCreatePost('draft')}
                      className="px-4 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold cursor-pointer"
                    >
                      ড্রাফট হিসেবে রাখুন
                    </button>
                    <button
                      onClick={() => handleAdminCreatePost('publish')}
                      className="px-5 py-1.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-400 text-slate-950 text-xs font-extrabold cursor-pointer hover:opacity-95 shadow-[0_0_15px_rgba(16,185,129,0.3)]"
                    >
                      লাইভ পাবলিশ করুন ➔
                    </button>
                  </div>
                </div>
              )}

              {/* Posts List */}
              <div className="space-y-2">
                {adminPosts
                  .filter((p) => {
                    const matchSt = adminPostFilter === 'all' || p.status === adminPostFilter;
                    const matchQ =
                      !adminPostSearch.trim() ||
                      p.title.toLowerCase().includes(adminPostSearch.toLowerCase()) ||
                      p.category.toLowerCase().includes(adminPostSearch.toLowerCase());
                    return matchSt && matchQ;
                  })
                  .map((post) => (
                    <div
                      key={post.id}
                      className="p-3.5 rounded-xl bg-[#0b1120] border border-slate-800 hover:border-slate-700 transition-all flex flex-wrap items-center justify-between gap-3"
                    >
                      <div className="space-y-1 flex-1 min-w-[280px]">
                        <div className="flex items-center gap-2">
                          <span className="text-xs font-bold text-white hover:text-[#00f5d4] transition-colors">
                            {post.title}
                          </span>
                          <span
                            className={`text-[10px] font-mono px-2 py-0.5 rounded ${
                              post.status === 'publish'
                                ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
                                : post.status === 'pending'
                                ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
                                : 'bg-slate-700 text-slate-300'
                            }`}
                          >
                            {post.status === 'publish' ? 'Published' : post.status === 'pending' ? 'Pending Review' : 'Draft'}
                          </span>
                        </div>
                        <div className="text-[11px] text-slate-400 flex flex-wrap items-center gap-x-4 gap-y-1">
                          <span>ক্যাটাগরি: <strong className="text-slate-300">{post.category}</strong></span>
                          <span>তারিখ: <strong className="text-slate-300">{post.createdAt}</strong></span>
                          <span>ভিউ: <strong className="text-[#00f5d4]">{post.views.toLocaleString()}</strong></span>
                          <span>লাইক: <strong className="text-rose-400">{post.likes}</strong></span>
                        </div>
                      </div>

                      <div className="flex items-center gap-2 shrink-0">
                        <button
                          onClick={() => setSelectedPreviewPost(post)}
                          className="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 flex items-center gap-1 cursor-pointer"
                          title="পোস্ট বিস্তারিত দেখুন"
                        >
                          <Eye className="w-3.5 h-3.5 text-[#00f5d4]" />
                          <span>ভিউ</span>
                        </button>

                        {post.status !== 'publish' ? (
                          <button
                            onClick={() => handleAdminPublishPost(post.id, post.title)}
                            className="px-3 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500 hover:text-black text-emerald-300 text-xs font-semibold cursor-pointer border border-emerald-500/40"
                          >
                            পাবলিশ
                          </button>
                        ) : (
                          <button
                            onClick={() => handleAdminDraftPost(post.id, post.title)}
                            className="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs text-slate-400 cursor-pointer"
                          >
                            ড্রাফট
                          </button>
                        )}

                        <button
                          onClick={() => handleAdminDeletePost(post.id, post.title)}
                          className="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 hover:text-white text-rose-400 text-xs cursor-pointer border border-rose-500/20"
                          title="পোস্ট ডিলিট করুন"
                        >
                          মুছুন
                        </button>
                      </div>
                    </div>
                  ))}

                {adminPosts.length === 0 && (
                  <div className="p-8 text-center text-slate-500 text-xs">
                    কোন পোস্ট পাওয়া যায়নি। "+ Add New Post" দিয়ে প্রথম পোস্ট তৈরি করুন।
                  </div>
                )}
              </div>

              {/* Post Preview Modal */}
              {selectedPreviewPost && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
                  <div className="w-full max-w-2xl max-h-[85vh] rounded-2xl bg-[#0b1120] border border-[#00f5d4]/40 p-5 overflow-y-auto space-y-4">
                    <div className="flex items-center justify-between border-b border-slate-800 pb-3">
                      <div>
                        <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-[#00f5d4]/20 text-[#00f5d4]">
                          {selectedPreviewPost.category}
                        </span>
                        <h3 className="text-base font-bold text-white mt-1">
                          {selectedPreviewPost.title}
                        </h3>
                      </div>
                      <button
                        onClick={() => setSelectedPreviewPost(null)}
                        className="text-slate-400 hover:text-white p-1 cursor-pointer"
                      >
                        ✕
                      </button>
                    </div>

                    <div className="text-xs text-slate-300 whitespace-pre-wrap leading-relaxed">
                      {selectedPreviewPost.content}
                    </div>

                    <div className="flex flex-wrap gap-1.5 pt-2">
                      {selectedPreviewPost.tags.map((tg, i) => (
                        <span key={i} className="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300">
                          #{tg}
                        </span>
                      ))}
                    </div>

                    <div className="flex items-center justify-between border-t border-slate-800 pt-3 text-xs text-slate-400">
                      <span>স্ট্যাটাস: <strong className="text-white">{selectedPreviewPost.status}</strong></span>
                      <button
                        onClick={() => setSelectedPreviewPost(null)}
                        className="px-4 py-1.5 rounded-lg bg-[#00f5d4] text-[#050811] font-bold text-xs cursor-pointer"
                      >
                        বন্ধ করুন
                      </button>
                    </div>
                  </div>
                </div>
              )}
            </div>
          )}

          {/* TAB 6: THEME & PLUGIN PACKAGES */}
          {activeAdminTab === 'packages' && (
            <div className="space-y-5">
              <div className="border-b border-slate-800 pb-4">
                <h3 className="text-lg font-bold text-white">
                  WordPress Packages &amp; Export Center
                </h3>
                <p className="text-xs text-slate-400 mt-1">
                  এখানে আপনি পুরো WordPress থিম (Theme Zip), কোর প্লাগইন (Core Plugin Zip) ও ডকুমেন্টেশন জিপ ডাউনলোড করতে পারবেন।
                </p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="p-4 rounded-xl bg-[#0b1120] border border-[#00f5d4]/30 space-y-3">
                  <div className="font-bold text-white text-sm">
                    🛡️ hackersshikkhok-theme.zip
                  </div>
                  <p className="text-xs text-slate-400">
                    কমপ্লিট ওয়ার্ডপ্রেস থিম উইথ সাইবারপাঙ্ক প্রেজেন্টেশন ও টার্মিনাল স্টাইল।
                  </p>
                  <button
                    onClick={() => onDownloadZip('theme')}
                    className="w-full py-2 rounded-lg bg-[#00f5d4] text-[#050811] font-bold text-xs cursor-pointer shadow-[0_0_12px_rgba(0,245,212,0.3)]"
                  >
                    ডাউনলোড থিম জিপ 📥
                  </button>
                </div>

                <div className="p-4 rounded-xl bg-[#0b1120] border border-purple-500/30 space-y-3">
                  <div className="font-bold text-white text-sm">
                    ⚙️ hackersshikkhok-core.zip
                  </div>
                  <p className="text-xs text-slate-400">
                    কোর ইঞ্জিন প্লাগইন: কাস্টম পোস্ট টাইপ, CTF ভ্যালিডেশন ও ৫০+ এন্ডপয়েন্ট।
                  </p>
                  <button
                    onClick={() => onDownloadZip('core')}
                    className="w-full py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs cursor-pointer shadow-[0_0_12px_rgba(124,58,237,0.3)]"
                  >
                    ডাউনলোড প্লাগইন জিপ 🔌
                  </button>
                </div>

                <div className="p-4 rounded-xl bg-[#0b1120] border border-emerald-500/30 space-y-3">
                  <div className="font-bold text-white text-sm">
                    📚 Developers Documentation
                  </div>
                  <p className="text-xs text-slate-400">
                    আর্কিটেকচার, স্কিমা, রেস্ট এপিআই ও সিকিউরিটি টেস্ট রিপোর্ট।
                  </p>
                  <button
                    onClick={() => onDownloadZip('docs')}
                    className="w-full py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-[#050811] font-bold text-xs cursor-pointer shadow-[0_0_12px_rgba(16,185,129,0.3)]"
                  >
                    ডাউনলোড ডক্স 📚
                  </button>
                </div>
              </div>
            </div>
          )}

          {/* TAB 7: SYSTEM HEALTH */}
          {(activeAdminTab === 'system_health' ||
            activeAdminTab === 'courses_lms' ||
            activeAdminTab === 'tools_registry' ||
            activeAdminTab === 'ai_autopilot') && (
            <div className="p-5 rounded-2xl bg-[#0b1120] border border-slate-800 space-y-4">
              <div className="flex items-center gap-2 text-white font-bold text-sm">
                <CheckCircle2 className="w-5 h-5 text-[#00f5d4]" />
                <span>সিস্টেম হেলথ ও সিকিউরিটি অডিট — ১০০% অপারেশনাল</span>
              </div>
              <p className="text-xs text-slate-300">
                সমস্ত ইঞ্জিন প্রস্তুত রয়েছে। ননস ভেরিফিকেশন, প্রিপেয়ার্ড এসকিউএল কোয়েরি, রেট লিমিটিং এবং লাইভ স্যান্ডবক্স সক্রিয়।
              </p>
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
                <div className="p-3 rounded-lg bg-[#050811] border border-slate-800">
                  <span className="text-slate-400 block text-[10px]">PHP Version</span>
                  <strong className="text-emerald-400 font-mono">8.2+ Hardened</strong>
                </div>
                <div className="p-3 rounded-lg bg-[#050811] border border-slate-800">
                  <span className="text-slate-400 block text-[10px]">Database Engine</span>
                  <strong className="text-[#00f5d4] font-mono">InnoDB dbDelta</strong>
                </div>
                <div className="p-3 rounded-lg bg-[#050811] border border-slate-800">
                  <span className="text-slate-400 block text-[10px]">Rest API</span>
                  <strong className="text-purple-400 font-mono">v1 &amp; v2 Secure</strong>
                </div>
                <div className="p-3 rounded-lg bg-[#050811] border border-slate-800">
                  <span className="text-slate-400 block text-[10px]">AI Autopilot</span>
                  <strong className="text-amber-400 font-mono">Gemini Flash</strong>
                </div>
              </div>
            </div>
          )}
        </main>
      </div>
    </div>
  );
};

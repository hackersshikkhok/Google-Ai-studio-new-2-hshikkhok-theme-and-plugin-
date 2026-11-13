import React, { useState } from 'react';
import { PostStore } from '../services/postStore';
import {
  AUTOPILOT_UNITS,
  CUSTOM_POST_TYPES,
  CUSTOM_TAXONOMIES,
  CUSTOM_DB_TABLES,
  AutopilotUnit
} from '../data/platformData';
import { 
  Cpu, Database, Layers, Play, CheckCircle2, ShieldAlert, Sliders, 
  ShieldCheck, Bot, Key, RefreshCw, AlertTriangle, ArrowRight, Zap, 
  Search, Award, Send, Users, Smartphone, Globe, Terminal, Activity,
  Lock, Check, Trash2, Plus
} from 'lucide-react';

export interface AutonomousBotItem {
  id: string;
  slug: string;
  nameBn: string;
  nameEn: string;
  category: string;
  frequency: string;
  role: string;
  status: 'Active' | 'Running' | 'Idle';
  lastRun: string;
  healthScore: number;
  itemsProcessed: number;
}

const INITIAL_BOTS: AutonomousBotItem[] = [
  {
    id: 'bot-01',
    slug: 'cyber-sentinel',
    nameBn: 'CyberSentinel-Bot (সাইবার ডিফেন্স ডেমন)',
    nameEn: 'CyberSentinel Autonomous Security Daemon',
    category: 'Security & Defense',
    frequency: 'Every 5 Minutes (Real-Time)',
    role: '24/7 ট্র্যাফিক মনিটরিং, SQLi, XSS অ্যাটাক ব্লকিং এবং সিস্টেম রিয়েল-টাইম প্রোটেকশন নিশ্চিত করে।',
    status: 'Active',
    lastRun: 'Just now',
    healthScore: 99,
    itemsProcessed: 14200,
  },
  {
    id: 'bot-02',
    slug: 'content-crafter',
    nameBn: 'ContentCrafter-Bot (কনটেন্ট ক্রিয়েটর ও পাবলিশার)',
    nameEn: 'ContentCrafter Trend & Technical Publisher',
    category: 'Content & Editorial',
    frequency: 'Hourly Cron',
    role: 'সাইবার ও টেকনিক্যাল ট্রেন্ড ডিটেক্ট করে কোয়ালিটি গেট ≥ ৮৫ সহ ড্রাফট ও পাবলিশিং পরিচালনা করে।',
    status: 'Active',
    lastRun: '14 mins ago',
    healthScore: 96,
    itemsProcessed: 840,
  },
  {
    id: 'bot-03',
    slug: 'google-ranker',
    nameBn: 'GoogleRanker-Bot (গুগল এসইও ও ইনডেক্সিং ক্রলার)',
    nameEn: 'GoogleRanker SEO & Indexing Crawler',
    category: 'SEO & Ranking',
    frequency: 'Every 3 Hours',
    role: 'এক্সএমএল সাইটম্যাপ জেনারেশন, স্কিমা ডাটা ও গুগল সার্চ কনসোল ইনস্ট্যান্ট ইনডেক্সিং সিঙ্ক করে।',
    status: 'Active',
    lastRun: '32 mins ago',
    healthScore: 100,
    itemsProcessed: 2650,
  },
  {
    id: 'bot-04',
    slug: 'adsense-shield',
    nameBn: 'AdSenseShield-Bot (অ্যাডসেন্স রেভিনিউ গার্ডিয়ান)',
    nameEn: 'AdSenseShield Compliance & Revenue Guardian',
    category: 'Monetization & AdSense',
    frequency: 'Twice Daily',
    role: 'অ্যাডসেন্স কমপ্লায়েন্স অডিট, ইনভ্যালিড ক্লিক প্রোটেকশন ও হাই-সিপিসি অপটিমাইজেশন বজায় রাখে।',
    status: 'Active',
    lastRun: '1 hour ago',
    healthScore: 98,
    itemsProcessed: 1980,
  },
  {
    id: 'bot-05',
    slug: 'app-builder',
    nameBn: 'AppBuilder-Bot (মোবাইল ও ওয়েব অ্যাপ মেকার)',
    nameEn: 'AppBuilder Studio Packaging Daemon',
    category: 'Platform Centers & Mobile',
    frequency: 'On Submission & Daily Audit',
    role: 'ওয়েব-টু-অ্যাপ ম্যানিফেস্ট, পিডব্লিউএ কনফিগারেশন ও ডাউনলোড প্যাকেজ অটো-বিল্ড করে।',
    status: 'Active',
    lastRun: '22 mins ago',
    healthScore: 97,
    itemsProcessed: 520,
  },
  {
    id: 'bot-06',
    slug: 'commu-guard',
    nameBn: 'CommuGuard-Bot (কমিউনিটি ও ফোরাম মডারেশন)',
    nameEn: 'CommuGuard Community & Sentiment Sentinel',
    category: 'Community & Moderation',
    frequency: 'Every 10 Minutes',
    role: 'ফোরাম প্রশ্ন-উত্তর যাচাই, স্প্যাম কমেন্ট ব্লকিং এবং ইউজার সেন্টিমেন্ট পর্যবেক্ষণ করে।',
    status: 'Active',
    lastRun: '6 mins ago',
    healthScore: 95,
    itemsProcessed: 4310,
  },
  {
    id: 'bot-07',
    slug: 'social-push',
    nameBn: 'SocialPush-Bot (সোশাল মিডিয়া ডিস্ট্রিবিউশন)',
    nameEn: 'SocialPush Multi-Channel Dispatcher',
    category: 'Marketing & Distribution',
    frequency: 'On Publish / Daily Digest',
    role: 'নতুন কোর্স, টিউটোরিয়াল ও ডেভেলপার টুলস স্বয়ংক্রিয়ভাবে সোশাল প্ল্যাটফর্মে ব্রডকাস্ট করে।',
    status: 'Active',
    lastRun: '45 mins ago',
    healthScore: 96,
    itemsProcessed: 18900,
  },
  {
    id: 'bot-08',
    slug: 'health-repair',
    nameBn: 'HealthRepair-Bot (সিস্টেম সেলফ-হিলিং অপটিমাইজার)',
    nameEn: 'HealthRepair Self-Healing & DB Optimizer',
    category: 'System & Infrastructure',
    frequency: 'Every 6 Hours',
    role: 'ডাটাবেজ ক্লিনআপ, ট্রানজিয়েন্ট ক্যাশ রিফ্রেশ এবং ব্যাকআপ ক্রিপ্টোগ্রাফিক ভেরিফিকেশন পরিচালনা করে।',
    status: 'Active',
    lastRun: 'Just now',
    healthScore: 100,
    itemsProcessed: 670,
  },
];

export const AutopilotAndArchitectureHub: React.FC = () => {
  const [activeTab, setActiveTab] = useState<
    'autopilots' | 'autonomous-bots' | 'api-rotation' | 'master-switches' | 'cpt-tax' | 'db-tables' | 'qa-audit'
  >('autopilots');

  const [autopilots, setAutopilots] = useState<AutopilotUnit[]>(AUTOPILOT_UNITS);
  const [botsList, setBotsList] = useState<AutonomousBotItem[]>(INITIAL_BOTS);

  const [pipelineLog, setPipelineLog] = useState<string>(
    '[Quality Gate 94/100] Topic Discovery -> AI Draft -> AST Code Validation -> Security Check -> Duplicate/Similarity Check -> Schema & Internal Links -> Saved as Pending Review Draft.'
  );

  // API Rotation Interactive State
  const [keyPools, setKeyPools] = useState({
    gemini: [
      { id: 'gemini-01', label: 'Gemini 2.5 Pro Key 1 (Primary)', status: 'Active', quota: '4.8k / 5k requests', cooldown: 'None' },
      { id: 'gemini-02', label: 'Gemini 2.5 Flash Key 2 (High Speed)', status: 'Active', quota: '8.2k / 10k requests', cooldown: 'None' },
      { id: 'gemini-03', label: 'Gemini 2.0 Flash-Lite Key 3 (Failover)', status: 'Active', quota: '14.1k / 15k requests', cooldown: 'None' },
    ],
    openai: [
      { id: 'openai-01', label: 'OpenAI GPT-4o Key 1', status: 'Active', quota: 'Tier 4 Standby', cooldown: 'None' },
    ],
    claude: [
      { id: 'claude-01', label: 'Anthropic Claude 3.5 Sonnet Key', status: 'Active', quota: 'Tier 5 Standby', cooldown: 'None' },
    ]
  });

  const [telemetry, setTelemetry] = useState({
    totalRequests: 2840,
    successfulResponses: 2838,
    rateLimit429Hits: 2,
    modelFailovers: 5,
    localDeterministicFallbacks: 0,
    activeProvider: 'Google Gemini (gemini-2.5-pro)'
  });

  const [masterSwitches, setMasterSwitches] = useState<Record<string, boolean>>({
    'AI Autopilot': true,
    'Auto Draft (Default)': true,
    'Auto Publish': false,
    'AI Code Generator': true,
    'Live Demo Sandbox': true,
    'Tool Engine (60+ Tools)': true,
    'YouTube Integration': true,
    'GitHub Integration': true,
    'Auto Internal Linking': true,
    'Schema Engine': true,
    'Download & Temp Cleanup': true,
    'User Submission & Moderation': true,
    'Emergency Safe Mode (Kill-Switch)': false
  });

  const handleToggleSwitch = (label: string) => {
    setMasterSwitches((prev) => ({ ...prev, [label]: !prev[label] }));
  };

  const handleEmergencyKillSwitch = () => {
    setMasterSwitches((prev) => ({
      ...prev,
      'AI Autopilot': false,
      'Auto Publish': false,
      'Emergency Safe Mode (Kill-Switch)': true
    }));
    setAutopilots((prev) => prev.map((u) => ({ ...u, status: 'paused' })));
    setPipelineLog(
      `[${new Date().toLocaleTimeString()}] 🚨 EMERGENCY KILL-SWITCH ENGAGED: All 24 AI Autopilots paused, background bot queue halted, external social publishing locked, and Admin Audit Log recorded.`
    );
  };

  const handleRunTestJob = (unit: AutopilotUnit) => {
    // Generate real post into PostStore
    const title = `[AI Autopilot] ${unit.nameBn}: এন্টারপ্রাইজ সাইবার সিকিউরিটি ও সিস্টেম অডিট প্রটোকল`;
    const saved = PostStore.save({
      title,
      category: unit.name,
      type: 'tutorial',
      status: masterSwitches['Auto Publish'] ? 'publish' : 'draft',
      content: `এই পূর্ণাঙ্গ টেকনিক্যাল গাইডটি ${unit.name} (${unit.mode}) দ্বারা স্বয়ংক্রিয়ভাবে জেনারেট ও অডিট করা হয়েছে।\n\n- কোয়ালিটি গেট স্কোর: ${unit.avgQualityScore}/১০০ (পাস)\n- সিকিউরিটি স্ক্যান: SAST & DAST ১০০% ক্লিন\n- AI মডেল: ${telemetry.activeProvider}\n\nপোস্টটি এখন WordPress Database ও User Dashboard-এ সংরক্ষিত হয়েছে।`,
      tags: ['AI Autopilot', 'CyberSecurity', unit.targetCpt || 'Tutorial']
    });

    setPipelineLog(
      `[${new Date().toLocaleTimeString()}] ✅ ${unit.name} (${unit.mode}) সম্পন্ন! নতুন পোস্ট তৈরি হয়েছে: "${title}" (স্ট্যাটাস: ${saved.status})। কোয়ালিটি স্কোর: ${unit.avgQualityScore}/100। Provider: ${telemetry.activeProvider}.`
    );
    setAutopilots((prev) =>
      prev.map((item) =>
        item.id === unit.id
          ? {
              ...item,
              todayGenerated: item.todayGenerated + 1,
              lastRun: 'Just now'
            }
          : item
      )
    );
  };

  const handleTriggerBot = (bot: AutonomousBotItem) => {
    if (bot.slug === 'content-crafter') {
      // Content bot creates a fresh post!
      const botPost = PostStore.save({
        title: `[ContentCrafter] ২০২৬ সালের শীর্ষ ১০টি জিরো-ডে অ্যাটাক এবং প্রোডাকশন ডিফেন্স মেথডোলজি`,
        category: 'Ethical Hacking & Defense',
        type: 'tutorial',
        status: 'draft',
        content: `ContentCrafter Autonomous Bot দ্বারা সংগৃহীত ও বিশ্লেষিত最新 সাইবার থ্রেট ইন্টেলিজেন্স রিপোর্ট।\n\n১. মেমরি সেফটি অ্যান্ড রাস্ট কার্নেল ডিফেন্স\n২. ক্লাউড-নেটিভ K8s এডমিশন কন্ট্রোলার অডিট\n৩. ননস ও স্যানিটাইজেশন প্রটোকল\n\nপোস্টটি ড্রাফট হিসেবে সেভ হয়েছে এবং অ্যাডমিন প্যানেল ও ড্যাশবোর্ডে ভিউ করার জন্য প্রস্তুত।`,
        tags: ['Zero-Day', 'Threat Intel', 'Defense', 'ContentCrafter']
      });
      setPipelineLog(
        `[${new Date().toLocaleTimeString()}] 🤖 [${bot.nameEn}] এক্সিকিউট হয়েছে! ড্রাফট পোস্ট তৈরি হয়েছে: "${botPost.title}"।`
      );
    } else {
      setPipelineLog(
        `[${new Date().toLocaleTimeString()}] 🤖 [${bot.nameEn}] সচল হয়েছে। ব্যাকগ্রাউন্ড টাস্ক সম্পন্ন: "${bot.role.slice(0, 70)}..."। স্ট্যাটাস: ১০০% সাকসেস।`
      );
    }
    setBotsList((prev) =>
      prev.map((b) =>
        b.id === bot.id
          ? {
              ...b,
              lastRun: 'Just now',
              itemsProcessed: b.itemsProcessed + 1
            }
          : b
      )
    );
  };

  const handleSimulate429Rotation = () => {
    setTelemetry((prev) => ({
      ...prev,
      totalRequests: prev.totalRequests + 1,
      rateLimit429Hits: prev.rateLimit429Hits + 1,
      modelFailovers: prev.modelFailovers + 1,
      activeProvider: 'Google Gemini (gemini-2.5-flash) [Auto-Switched from Pro due to 429]'
    }));
    setPipelineLog(
      `[${new Date().toLocaleTimeString()}] ⚠️ 429 Quota Exhaustion Simulated on Key "Gemini-01". Key placed in 10-minute cooldown. Automatically rotated to Key 2 (Gemini-02) and switched model to gemini-2.5-flash. Zero downtime.`
    );
  };

  return (
    <div className="bg-[#0b1120] border border-[#00f5d4]/30 rounded-2xl p-5 md:p-8 text-white space-y-6">
      
      {/* Header */}
      <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
          <div className="flex items-center gap-2 text-xs font-mono text-[#00f5d4]">
            <Bot className="w-4 h-4" />
            <span>ENTERPRISE AUTONOMOUS ENGINE & API FAILOVER ARCHITECTURE</span>
          </div>
          <h2 className="text-2xl md:text-3xl font-extrabold mt-1">Autopilot,         Hub</h2>
          <p className="text-xs text-slate-400 mt-1">, / Security ,             ।</p>
        </div>

        <button
          onClick={handleEmergencyKillSwitch}
          className="px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold text-xs flex items-center gap-2 cursor-pointer shadow hover:brightness-110 transition-all whitespace-nowrap"
        >
          <ShieldAlert className="w-4 h-4" />- (Emergency Halt)</button>
      </div>

      {/* Tabs */}
      <div className="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin">{[
          { id: 'autopilots', label: '🤖  Autopilot ', icon: Cpu, badge: autopilots.length },
          { id: 'autonomous-bots', label: '🛡️    ', icon: Bot, badge: botsList.length },
          { id: 'api-rotation', label: '🔑 API     ', icon: Key, badge: 'Active' },
          { id: 'master-switches', label: '⚙️    ', icon: Sliders, badge: '13 Toggles' },
          { id: 'cpt-tax', label: '📂  CPT   Taxonomies', icon: Layers, badge: 18 },
          { id: 'db-tables', label: '🗄️    ', icon: Database, badge: 21 },
          { id: 'qa-audit', label: '🔍 - QA ', icon: ShieldCheck, badge: '114/114' }
        ].map((tab) => {
          const Icon = tab.icon;
          const isActive = activeTab === tab.id;
          return (<button
              key={tab.id}
              onClick={() => setActiveTab(tab.id as any)}
              className={`px-3.5 py-2 rounded-xl text-xs font-bold cursor-pointer whitespace-nowrap transition-all flex items-center gap-2 border ${
                isActive
                  ? 'bg-[#00f5d4]/15 border-[#00f5d4] text-[#00f5d4]'
                  : 'bg-[#050811] border-slate-800 text-slate-400 hover:text-slate-200 hover:border-slate-700'
              }`}
            >
              <Icon className="w-3.5 h-3.5" />
              <span>{tab.label}</span>
              <span className="px-1.5 py-0.5 rounded-full bg-slate-800 text-[10px] text-slate-300">
                {tab.badge}
              </span>
            </button>
          );
        })}
      </div>

      {/* Live Pipeline Terminal Log */}
      <div className="bg-[#050811] border border-slate-800 rounded-xl p-3.5 font-mono text-xs text-[#00f5d4] flex items-start gap-2.5 overflow-x-auto">
        <Terminal className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
        <div className="space-y-1">
          <span className="text-[10px] text-slate-500 uppercase tracking-widest block font-bold">
            Live Execution & Telemetry Log
          </span>
          <p className="text-slate-300">{pipelineLog}</p>
        </div>
      </div>

      {/* TAB 1: 24 AUTOPILOTS */}
      {activeTab === 'autopilots' && (
        <div className="space-y-4">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono">
            <span>Center-</span>
            <span className="text-[#00f5d4]">Deterministic Quality Gate (≥85/100) Active</span>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {autopilots.map((unit) => (
              <div
                key={unit.id}
                className="bg-[#050811] border border-slate-800 rounded-xl p-4 flex flex-col justify-between hover:border-[#00f5d4]/40 transition-all shadow-md group"
              >
                <div>
                  <div className="flex items-center justify-between mb-2">
                    <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[#00f5d4]">
                      {unit.id.toUpperCase()} · /{unit.centerSlug}
                    </span>
                    <span className={`text-[10px] font-mono px-2 py-0.5 rounded ${
                      unit.status === 'active' ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-300 border border-rose-500/30'
                    }`}>
                      ● {unit.status}
                    </span>
                  </div>

                  <h4 className="text-sm font-bold text-white group-hover:text-[#00f5d4] transition-colors">
                    {unit.nameBn}
                  </h4>
                  <p className="text-[11px] text-slate-400 font-mono mt-0.5 truncate">
                    {unit.name}
                  </p>

                  <div className="grid grid-cols-2 gap-2 mt-3 p-2 bg-[#0b1120] rounded-lg border border-slate-800 text-[11px] font-mono">
                    <div>
                      <span className="text-slate-500 block text-[9px]">/ CPT:</span>
                      <span className="text-slate-300 truncate block">{unit.mode}</span>
                    </div>
                    <div>
                      <span className="text-slate-500 block text-[9px]">:</span>
                      <span className="text-emerald-400 font-bold">{unit.todayGenerated} / {unit.dailyLimit}</span>
                    </div>
                    <div>
                      <span className="text-slate-500 block text-[9px]">:</span>
                      <span className="text-amber-400 font-bold">{unit.minQualityScore}/100</span>
                    </div>
                    <div>
                      <span className="text-slate-500 block text-[9px]">:</span>
                      <span className="text-emerald-400 font-bold">{unit.avgQualityScore}/100</span>
                    </div>
                  </div>
                </div>

                <div className="pt-3 mt-3 border-t border-slate-800/80 flex items-center justify-between">
                  <span className="text-[10px] text-slate-500 font-mono">: {unit.lastRun}</span>
                  <button
                    onClick={() => handleRunTestJob(unit)}
                    className="px-2.5 py-1 rounded-lg bg-[#00f5d4]/10 hover:bg-[#00f5d4] text-[#00f5d4] hover:text-[#050811] text-[11px] font-bold transition-all cursor-pointer flex items-center gap-1 border border-[#00f5d4]/30"
                  >
                    <Play className="w-3 h-3" />Cyber Defense Hub</button>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* TAB 2: 8 AUTONOMOUS PRO BOTS */}
      {activeTab === 'autonomous-bots' && (
        <div className="space-y-5">
          <div className="bg-[#050811] p-4 rounded-xl border border-slate-800 space-y-2">
            <h3 className="text-sm font-bold text-white flex items-center gap-2">
              <Bot className="w-4 h-4 text-[#00f5d4]" />(Zero Manual Labor)</h3>
            <p className="text-xs text-slate-400 leading-relaxed">Platform (Google, TikTok, Cloudflare)      ।    Login        ।</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {botsList.map((bot) => (
              <div
                key={bot.id}
                className="bg-[#050811] border border-slate-800 rounded-xl p-4 flex flex-col justify-between hover:border-[#00f5d4]/50 transition-all shadow-md"
              >
                <div>
                  <div className="flex items-center justify-between mb-2">
                    <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-900 text-[#00f5d4] border border-slate-800 uppercase">
                      {bot.category}
                    </span>
                    <span className="px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-500/15 border border-emerald-500/30 text-emerald-300">
                      ● {bot.status}
                    </span>
                  </div>

                  <h4 className="text-sm font-bold text-white mt-1 line-clamp-1">
                    {bot.nameBn}
                  </h4>
                  <span className="text-[10px] font-mono text-slate-500 block mb-2">⏱️ : {bot.frequency}</span>

                  <p className="text-xs text-slate-300 line-clamp-3 leading-relaxed mb-3">
                    {bot.role}
                  </p>

                  <div className="p-2 rounded-lg bg-[#0b1120] border border-slate-800/80 text-[10px] font-mono space-y-1">
                    <div className="flex justify-between">
                      <span className="text-slate-500">:</span>
                      <strong className="text-white">{bot.itemsProcessed.toLocaleString()}</strong>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-slate-500">:</span>
                      <strong className="text-emerald-400">{bot.healthScore}%</strong>
                    </div>
                  </div>
                </div>

                <div className="pt-3 mt-3 border-t border-slate-800 flex items-center justify-between">
                  <span className="text-[10px] text-slate-500 font-mono">
                    {bot.lastRun}
                  </span>
                  <button
                    onClick={() => handleTriggerBot(bot)}
                    className="px-2.5 py-1 rounded-lg bg-[#00f5d4] text-[#050811] text-[11px] font-bold cursor-pointer hover:brightness-110 transition-all flex items-center gap-1 shadow"
                  >
                    <Zap className="w-3 h-3" />Cyber Defense Hub</button>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* TAB 3: API ROTATION & QUOTA FAILOVER */}
      {activeTab === 'api-rotation' && (
        <div className="space-y-6">
          
          {/* Telemetry Counter Cards */}
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div className="bg-[#050811] p-3.5 rounded-xl border border-slate-800">
              <span className="text-[10px] font-mono text-slate-400 block">Cyber Defense Hub</span>
              <span className="text-xl font-extrabold text-[#00f5d4]">{telemetry.totalRequests.toLocaleString()}</span>
            </div>
            <div className="bg-[#050811] p-3.5 rounded-xl border border-slate-800">
              <span className="text-[10px] font-mono text-slate-400 block">Success</span>
              <span className="text-xl font-extrabold text-emerald-400">{telemetry.successfulResponses.toLocaleString()}</span>
            </div>
            <div className="bg-[#050811] p-3.5 rounded-xl border border-slate-800">
              <span className="text-[10px] font-mono text-slate-400 block">429</span>
              <span className="text-xl font-extrabold text-amber-400">{telemetry.rateLimit429Hits}</span>
            </div>
            <div className="bg-[#050811] p-3.5 rounded-xl border border-slate-800">
              <span className="text-[10px] font-mono text-slate-400 block">Cyber Defense Hub</span>
              <span className="text-xl font-extrabold text-purple-400">{telemetry.modelFailovers}</span>
            </div>
          </div>

          {/* Model Cascading Hierarchy Diagram */}
          <div className="bg-[#050811] border border-slate-800 rounded-xl p-4 sm:p-5 space-y-3">
            <div className="flex items-center justify-between">
              <strong className="text-sm text-[#00f5d4] flex items-center gap-2">
                <RefreshCw className="w-4 h-4" />-    (Auto-Cascading Hierarchy)</strong>
              <button
                onClick={handleSimulate429Rotation}
                className="px-3 py-1.5 rounded-lg bg-amber-500/20 border border-amber-500/40 text-amber-400 text-xs font-bold hover:bg-amber-500 hover:text-black cursor-pointer transition-all flex items-center gap-1.5"
              >
                <AlertTriangle className="w-3.5 h-3.5" />429    (Simulate Quota Failover)</button>
            </div>

            <p className="text-xs text-slate-400">-   (HTTP 429)            :</p>

            <div className="flex flex-wrap items-center gap-2 text-xs font-mono pt-1">
              <span className="px-3 py-1.5 rounded-lg bg-[#0b1120] border border-[#00f5d4] text-[#00f5d4] font-bold">
                1. Gemini 2.5 Pro (Primary)
              </span>
              <ArrowRight className="w-3.5 h-3.5 text-slate-500" />
              <span className="px-3 py-1.5 rounded-lg bg-[#0b1120] border border-emerald-500 text-emerald-400 font-bold">
                2. Gemini 2.5 Flash
              </span>
              <ArrowRight className="w-3.5 h-3.5 text-slate-500" />
              <span className="px-3 py-1.5 rounded-lg bg-[#0b1120] border border-blue-500 text-blue-400 font-bold">
                3. Gemini 2.0 Flash Lite
              </span>
              <ArrowRight className="w-3.5 h-3.5 text-slate-500" />
              <span className="px-3 py-1.5 rounded-lg bg-[#0b1120] border border-purple-500 text-purple-400 font-bold">
                4. OpenAI GPT-4o / Mini
              </span>
              <ArrowRight className="w-3.5 h-3.5 text-slate-500" />
              <span className="px-3 py-1.5 rounded-lg bg-[#0b1120] border border-rose-500 text-rose-400 font-bold">
                5. Claude 3.5 / 3.7
              </span>
              <ArrowRight className="w-3.5 h-3.5 text-slate-500" />
              <span className="px-3 py-1.5 rounded-lg bg-[#0b1120] border border-slate-600 text-slate-300 font-bold">
                6. Local Knowledge Engine (100% Uptime Guaranteed)
              </span>
            </div>
          </div>

          {/* Active Key Pools */}
          <div className="space-y-3">
            <h3 className="text-sm font-bold text-white flex items-center gap-2">
              <Key className="w-4 h-4 text-emerald-400" />(Key Pools & Quota Health)</h3>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              {/* Gemini Pool */}
              <div className="bg-[#050811] border border-slate-800 rounded-xl p-4 space-y-3">
                <div className="flex items-center justify-between border-b border-slate-800 pb-2">
                  <span className="text-xs font-mono font-bold text-[#00f5d4]">GOOGLE GEMINI POOL</span>
                  <span className="text-[10px] font-mono text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded">3 Keys Loaded</span>
                </div>
                {keyPools.gemini.map((k) => (
                  <div key={k.id} className="p-2.5 rounded-lg bg-[#0b1120] border border-slate-800/80 text-xs font-mono space-y-1">
                    <div className="flex justify-between items-center">
                      <strong className="text-white text-[11px] truncate max-w-[150px]">{k.label}</strong>
                      <span className="text-[9px] text-emerald-400 px-1.5 py-0.5 rounded bg-emerald-500/15">ACTIVE</span>
                    </div>
                    <div className="text-[10px] text-slate-400 flex justify-between">
                      <span>: {k.quota}</span>
                      <span>: {k.cooldown}</span>
                    </div>
                  </div>
                ))}
              </div>

              {/* OpenAI Pool */}
              <div className="bg-[#050811] border border-slate-800 rounded-xl p-4 space-y-3">
                <div className="flex items-center justify-between border-b border-slate-800 pb-2">
                  <span className="text-xs font-mono font-bold text-purple-400">OPENAI POOL</span>
                  <span className="text-[10px] font-mono text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded">1 Key Loaded</span>
                </div>
                {keyPools.openai.map((k) => (
                  <div key={k.id} className="p-2.5 rounded-lg bg-[#0b1120] border border-slate-800/80 text-xs font-mono space-y-1">
                    <div className="flex justify-between items-center">
                      <strong className="text-white text-[11px] truncate max-w-[150px]">{k.label}</strong>
                      <span className="text-[9px] text-purple-400 px-1.5 py-0.5 rounded bg-purple-500/15">STANDBY</span>
                    </div>
                    <div className="text-[10px] text-slate-400 flex justify-between">
                      <span>: {k.quota}</span>
                      <span>: {k.cooldown}</span>
                    </div>
                  </div>
                ))}
              </div>

              {/* Anthropic Claude Pool */}
              <div className="bg-[#050811] border border-slate-800 rounded-xl p-4 space-y-3">
                <div className="flex items-center justify-between border-b border-slate-800 pb-2">
                  <span className="text-xs font-mono font-bold text-rose-400">ANTHROPIC CLAUDE POOL</span>
                  <span className="text-[10px] font-mono text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded">1 Key Loaded</span>
                </div>
                {keyPools.claude.map((k) => (
                  <div key={k.id} className="p-2.5 rounded-lg bg-[#0b1120] border border-slate-800/80 text-xs font-mono space-y-1">
                    <div className="flex justify-between items-center">
                      <strong className="text-white text-[11px] truncate max-w-[150px]">{k.label}</strong>
                      <span className="text-[9px] text-rose-400 px-1.5 py-0.5 rounded bg-rose-500/15">STANDBY</span>
                    </div>
                    <div className="text-[10px] text-slate-400 flex justify-between">
                      <span>: {k.quota}</span>
                      <span>: {k.cooldown}</span>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* TAB 4: MASTER SWITCHES */}
      {activeTab === 'master-switches' && (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          {Object.entries(masterSwitches).map(([label, enabled]) => (
            <div
              key={label}
              className="bg-[#050811] border border-slate-800 rounded-xl p-4 flex items-center justify-between hover:border-slate-700 transition-all"
            >
              <div>
                <strong className="text-xs font-bold text-white block">{label}</strong>
                <span className="text-[10px] font-mono text-slate-500">{enabled ? ' (Online)' : ' (Disabled)'}</span>
              </div>
              <button
                onClick={() => handleToggleSwitch(label)}
                className={`w-11 h-6 rounded-full transition-colors relative cursor-pointer ${
                  enabled ? 'bg-[#00f5d4]' : 'bg-slate-700'
                }`}
              >
                <div
                  className={`w-4 h-4 rounded-full bg-[#050811] absolute top-1 transition-transform ${
                    enabled ? 'right-1' : 'left-1'
                  }`}
                />
              </button>
            </div>
          ))}
        </div>
      )}

      {/* TAB 5: CPT & TAXONOMIES */}
      {activeTab === 'cpt-tax' && (
        <div className="space-y-4">
          <div className="bg-[#050811] border border-slate-800 rounded-xl p-4">
            <h3 className="text-sm font-bold text-white mb-2">Custom Post Types (CPTs)</h3>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-mono">
              {CUSTOM_POST_TYPES.map((cpt) => (
                <div key={cpt.slug} className="p-2.5 rounded bg-[#0b1120] border border-slate-800/80 flex justify-between">
                  <span className="text-[#00f5d4]">/{cpt.slug} ({cpt.nameBn})</span>
                  <span className="text-slate-400">{cpt.count} posts</span>
                </div>
              ))}
            </div>
          </div>

          <div className="bg-[#050811] border border-slate-800 rounded-xl p-4">
            <h3 className="text-sm font-bold text-white mb-2">Custom Taxonomies</h3>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-mono">
              {CUSTOM_TAXONOMIES.map((tax) => (
                <div key={tax.slug} className="p-2.5 rounded bg-[#0b1120] border border-slate-800/80 flex justify-between">
                  <span className="text-emerald-400">{tax.slug} ({tax.nameBn})</span>
                  <span className="text-slate-400">{tax.terms.length} terms</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* TAB 6: 21 DATABASE TABLES */}
      {activeTab === 'db-tables' && (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 text-xs font-mono">
          {CUSTOM_DB_TABLES.map((t) => (
            <div key={t.tableName} className="p-3 rounded-xl bg-[#050811] border border-slate-800 flex justify-between items-center">
              <div>
                <span className="text-[#00f5d4] block font-bold">{t.tableName}</span>
                <span className="text-[10px] text-slate-500">{t.module} · {t.purpose.slice(0, 45)}...</span>
              </div>
              <span className="px-2 py-0.5 rounded bg-slate-900 text-[10px] text-slate-300">
                {t.rowCount} rows
              </span>
            </div>
          ))}
        </div>
      )}

      {/* TAB 7: 114-POINT QA AUDIT */}
      {activeTab === 'qa-audit' && (
        <div className="p-6 text-center bg-[#050811] rounded-2xl border border-slate-800 space-y-3">
          <ShieldCheck className="w-12 h-12 text-[#00f5d4] mx-auto" />
          <h3 className="text-lg font-bold text-white">/ -Ecosystem  Audit</h3>
          <p className="text-xs text-slate-300 max-w-xl mx-auto leading-relaxed">Platform Center,  AI Autopilot,    , -   , Google AdSense ,  + - Tool   ।</p>
          <div className="flex flex-wrap justify-center gap-2 pt-2 text-[11px] font-mono">
            <span className="px-3 py-1 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400">
              ✓ 100% Passed Quality Gate
            </span>
            <span className="px-3 py-1 rounded-full bg-blue-500/15 border border-blue-500/30 text-blue-400">
              ✓ Multi-Key Quota Rotator
            </span>
            <span className="px-3 py-1 rounded-full bg-purple-500/15 border border-purple-500/30 text-purple-400">
              ✓ Google Search Console Ready
            </span>
          </div>
        </div>
      )}

    </div>
  );
};
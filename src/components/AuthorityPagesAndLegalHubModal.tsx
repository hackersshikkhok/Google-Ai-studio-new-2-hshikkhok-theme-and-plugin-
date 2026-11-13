import React, { useState } from 'react';
import {
  Shield, FileText, Lock, AlertTriangle, Scale, CheckCircle,
  ExternalLink, Copy, Check, Printer, HelpCircle, Briefcase,
  Users, Bot, Cpu, Compass, ArrowRight, Download, Mail, Phone,
  Search, X, Sparkles, Server, Globe, Star
} from 'lucide-react';

export type AuthorityPageKey =
  | 'privacy'
  | 'terms'
  | 'disclaimer'
  | 'dmca'
  | 'services'
  | 'advisory'
  | 'llms-txt'
  | 'sitemap'
  | 'tools-expansion-plan';

interface AuthorityPagesAndLegalHubModalProps {
  initialPageKey?: AuthorityPageKey;
  onClose: () => void;
  onOpenAppMaker?: () => void;
  onOpenToolsLab?: () => void;
}

export const AuthorityPagesAndLegalHubModal: React.FC<AuthorityPagesAndLegalHubModalProps> = ({
  initialPageKey = 'privacy',
  onClose,
  onOpenAppMaker,
  onOpenToolsLab
}) => {
  const [activePage, setActivePage] = useState<AuthorityPageKey>(initialPageKey);
  const [activeCategory, setActiveCategory] = useState<'all' | 'legal' | 'services' | 'seo-ai' | 'tools-roadmap'>('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [copiedLink, setCopiedLink] = useState(false);
  const [copiedText, setCopiedText] = useState(false);

  // Quick consultation state
  const [serviceName, setServiceName] = useState('');
  const [clientEmail, setClientEmail] = useState('');
  const [clientMessage, setClientMessage] = useState('');
  const [serviceSubmitted, setServiceSubmitted] = useState(false);

  const pagesConfig: Record<
    AuthorityPageKey,
    {
      title: string;
      banglaTitle: string;
      category: 'legal' | 'services' | 'seo-ai' | 'tools-roadmap';
      icon: any;
      badge: string;
      badgeColor: string;
      summary: string;
    }
  >= {
    privacy: {
      title: 'Privacy Policy & Zero-Telemetry Charter',
      banglaTitle: '     ',
      category: 'legal',
      icon: Lock,
      badge: 'AdSense / GDPR',
      badgeColor: 'border-emerald-500/40 text-emerald-400 bg-emerald-500/10',
      summary: '      । -    ।'
    },
    terms: {
      title: 'Terms of Service & White-Hat Ethical Charter',
      banglaTitle: '   -  ',
      category: 'legal',
      icon: Scale,
      badge: 'Legal Terms',
      badgeColor: 'border-purple-500/40 text-purple-400 bg-purple-500/10',
      summary: ' Cyber  ,     Defense Security ।'
    },
    disclaimer: {
      title: 'Legal Disclaimer & Non-Liability Notice',
      banglaTitle: '    ',
      category: 'legal',
      icon: AlertTriangle,
      badge: 'Defensive Only',
      badgeColor: 'border-rose-500/40 text-rose-400 bg-rose-500/10',
      summary: '     -    ।'
    },
    dmca: {
      title: 'DMCA & Intellectual Property Protocol',
      banglaTitle: 'DMCA    ',
      category: 'legal',
      icon: Shield,
      badge: 'Copyright Safe',
      badgeColor: 'border-cyan-500/40 text-cyan-400 bg-cyan-500/10',
      summary: '           ।'
    },
    services: {
      title: 'Cyber Defense & Enterprise Services Hub',
      banglaTitle: ' Cyber Security  VAPT  Hub',
      category: 'services',
      icon: Briefcase,
      badge: 'High Value B2B',
      badgeColor: 'border-amber-500/40 text-amber-400 bg-amber-500/10',
      summary: 'VAPT Audit,   ,      Security ।'
    },
    advisory: {
      title: 'Strategic Advisory & Cyber Mentorship Council',
      banglaTitle: ' , --   ',
      category: 'services',
      icon: Users,
      badge: 'Expert Advisory',
      badgeColor: 'border-blue-500/40 text-blue-400 bg-blue-500/10',
      summary: '  ,  Cyber  Audit   ।'
    },
    'llms-txt': {
      title: 'AI Grounding & LLM Knowledge Graph (llms.txt)',
      banglaTitle: '    Engine  ',
      category: 'seo-ai',
      icon: Bot,
      badge: 'ChatGPT / Perplexity',
      badgeColor: 'border-teal-500/40 text-teal-400 bg-teal-500/10',
      summary: 'ChatGPT, Gemini, Perplexity  Claude-  -     ।'
    },
    sitemap: {
      title: 'HTML Master Sitemap & Architecture Directory',
      banglaTitle: '     ',
      category: 'seo-ai',
      icon: Compass,
      badge: 'Full Directory',
      badgeColor: 'border-indigo-500/40 text-indigo-400 bg-indigo-500/10',
      summary: '  , Hub, Course,       ।'
    },
    'tools-expansion-plan': {
      title: '10,000+ Tools Scalability Architecture Blueprint',
      banglaTitle: ',+  Tool -  ',
      category: 'tools-roadmap',
      icon: Cpu,
      badge: '10K Architecture',
      badgeColor: 'border-pink-500/40 text-pink-400 bg-pink-500/10',
      summary: '-       Tool    ।'
    }
  };

  const handleCopyLink = () => {
    const url = `${window.location.origin}/?page=${activePage}`;
    navigator.clipboard.writeText(url);
    setCopiedLink(true);
    setTimeout(() => setCopiedLink(false), 2000);
  };

  const handleCopyContent = () => {
    const el = document.getElementById('authority-page-content');
    if (el) {
      navigator.clipboard.writeText(el.innerText);
      setCopiedText(true);
      setTimeout(() => setCopiedText(false), 2000);
    }
  };

  const handlePrint = () => {
    window.print();
  };

  const filteredPageKeys = (Object.keys(pagesConfig) as AuthorityPageKey[]).filter((key) => {
    const item = pagesConfig[key];
    const matchesCategory = activeCategory === 'all' || item.category === activeCategory;
    const matchesSearch =
      item.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
      item.banglaTitle.toLowerCase().includes(searchQuery.toLowerCase()) ||
      item.summary.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesCategory && matchesSearch;
  });

  return (<div className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-2 sm:p-4 overflow-y-auto">
      <div className="relative w-full max-w-6xl bg-[#070b14] border-2 border-[#00f5d4]/40 rounded-3xl shadow-[0_0_60px_rgba(0,245,212,0.25)] flex flex-col max-h-[92vh] overflow-hidden">
        
        {/* Top Header Bar */}
        <div className="px-5 py-4 border-b border-slate-800 bg-gradient-to-r from-[#0b1120] via-[#070b14] to-[#111827] flex items-center justify-between gap-3 shrink-0">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-[#00f5d4]/10 border border-[#00f5d4]/30 flex items-center justify-center text-[#00f5d4]">
              <Shield className="w-5 h-5" />
            </div>
            <div>
              <h2 className="text-base sm:text-lg font-extrabold text-white flex items-center gap-2">HackersShikkhok —  ,     Hub<span className="text-[10px] font-mono px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                  AdSense &amp; AI Verified
                </span>
              </h2>
              <p className="text-xs text-slate-400 hidden sm:block">Engine,   (ChatGPT/Perplexity),        Center</p>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <button
              onClick={handlePrint}
              title="PDF"
              className="p-2 rounded-xl bg-slate-900 border border-slate-750 text-slate-300 hover:text-white cursor-pointer hover:bg-slate-800 transition-colors"
            >
              <Printer className="w-4 h-4" />
            </button>
            <button
              onClick={onClose}
              className="p-2 rounded-xl bg-slate-900 border border-slate-750 text-slate-400 hover:text-rose-400 cursor-pointer hover:bg-slate-800 transition-colors"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Main 2-Column Body */}
        <div className="grid grid-cols-1 md:grid-cols-12 flex-1 overflow-hidden">
          
          {/* Left Navigation Sidebar (4 cols) */}
          <div className="md:col-span-4 border-r border-slate-800/80 bg-[#060911]/90 flex flex-col p-3.5 space-y-3 overflow-y-auto">
            
            {/* Search Box */}
            <div className="relative">
              <Search className="w-3.5 h-3.5 absolute left-3 top-3 text-slate-500" />
              <input
                type="text"
                placeholder="..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-8 pr-3 py-2 bg-[#090d19] border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#00f5d4]/50"
              />
            </div>

            {/* Category Tabs */}
            <div className="flex flex-wrap gap-1 border-b border-slate-850 pb-2">{[
                { id: 'all', label: '' },
                { id: 'legal', label: 'AdSense  ' },
                { id: 'services', label: '  ' },
                { id: 'seo-ai', label: '  ' },
                { id: 'tools-roadmap', label: 'K Tool' },
              ].map((tab) => (<button
                  key={tab.id}
                  onClick={() => setActiveCategory(tab.id as any)}
                  className={`px-2.5 py-1 text-[11px] font-bold rounded-lg transition-colors cursor-pointer ${
                    activeCategory === tab.id
                      ? 'bg-[#00f5d4] text-[#050811]'
                      : 'bg-slate-900/80 text-slate-400 hover:text-white'
                  }`}
                >
                  {tab.label}
                </button>
              ))}
            </div>

            {/* Pages Navigation List */}
            <div className="space-y-1.5 flex-1 overflow-y-auto pr-1">
              {filteredPageKeys.map((key) => {
                const item = pagesConfig[key];
                const IconComponent = item.icon;
                const isSelected = activePage === key;

                return (
                  <button
                    key={key}
                    onClick={() => setActivePage(key)}
                    className={`w-full text-left p-2.5 rounded-xl border transition-all cursor-pointer flex items-start gap-2.5 ${
                      isSelected
                        ? 'bg-gradient-to-r from-[#00f5d4]/15 via-[#7c3aed]/10 to-transparent border-[#00f5d4]/50 text-white shadow-[0_0_15px_rgba(0,245,212,0.15)]'
                        : 'bg-[#0a0f1d]/50 border-slate-850/60 text-slate-400 hover:text-white hover:bg-[#0c1326]'
                    }`}
                  >
                    <div
                      className={`p-2 rounded-lg mt-0.5 shrink-0 ${
                        isSelected
                          ? 'bg-[#00f5d4] text-[#050811]'
                          : 'bg-slate-900 text-slate-400'
                      }`}
                    >
                      <IconComponent className="w-4 h-4" />
                    </div>

                    <div className="flex-1 min-w-0">
                      <div className="flex items-center justify-between gap-1 mb-0.5">
                        <span className="text-xs font-bold truncate text-white">
                          {item.banglaTitle}
                        </span>
                        <span
                          className={`text-[9px] font-mono px-1.5 py-0.2 rounded border uppercase whitespace-nowrap ${item.badgeColor}`}
                        >
                          {item.badge}
                        </span>
                      </div>
                      <p className="text-[11px] text-slate-400 line-clamp-1">
                        {item.title}
                      </p>
                    </div>
                  </button>
                );
              })}
            </div>

            {/* Bottom Quick Hub Buttons */}
            <div className="pt-2 border-t border-slate-800 space-y-1.5 shrink-0">
              <button
                onClick={() => {
                  onClose();
                  if (onOpenAppMaker) onOpenAppMaker();
                }}
                className="w-full py-2 px-3 rounded-xl bg-gradient-to-r from-[#f59e0b] to-[#ea580c] text-[#050811] text-xs font-black flex items-center justify-center gap-1.5 cursor-pointer shadow-md hover:brightness-110"
              >
                <span>📱</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>

              <button
                onClick={() => {
                  onClose();
                  if (onOpenToolsLab) onOpenToolsLab();
                }}
                className="w-full py-2 px-3 rounded-xl bg-slate-900 border border-[#00f5d4]/40 text-[#00f5d4] text-xs font-bold flex items-center justify-center gap-1.5 cursor-pointer hover:bg-slate-850"
              >
                <span>🧪 + Tool Lab</span>
              </button>
            </div>
          </div>

          {/* Right Content Area (8 cols) */}
          <div className="md:col-span-8 bg-[#070b14] flex flex-col overflow-y-auto">
            
            {/* Page Header Bar */}
            <div className="p-5 border-b border-slate-800 bg-gradient-to-r from-[#0b1120] to-[#070b14] flex flex-wrap items-center justify-between gap-3">
              <div>
                <div className="flex items-center gap-2 mb-1">
                  <span className={`text-[10px] font-mono px-2 py-0.5 rounded border uppercase ${pagesConfig[activePage].badgeColor}`}>
                    {pagesConfig[activePage].badge}
                  </span>
                  <span className="text-xs text-slate-400 font-mono">
                    Canonical: hackersshikkhok.com/{activePage}/
                  </span>
                </div>
                <h1 className="text-xl sm:text-2xl font-black text-white">
                  {pagesConfig[activePage].banglaTitle}
                </h1>
                <p className="text-xs text-slate-400">
                  {pagesConfig[activePage].title}
                </p>
              </div>

              {/* Action Buttons */}
              <div className="flex items-center gap-2">
                <button
                  onClick={handleCopyLink}
                  className="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-750 text-xs font-bold text-slate-300 hover:text-white flex items-center gap-1.5 cursor-pointer"
                >
                  {copiedLink ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5" />}
                  <span>{copiedLink ? ' !' : ' '}</span>
                </button>

                <button
                  onClick={handleCopyContent}
                  className="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-750 text-xs font-bold text-[#00f5d4] hover:bg-slate-850 flex items-center gap-1.5 cursor-pointer"
                >
                  {copiedText ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <FileText className="w-3.5 h-3.5" />}
                  <span>{copiedText ? ' !' : ' '}</span>
                </button>
              </div>
            </div>

            {/* Dynamic Content Renderer */}
            <div id="authority-page-content" className="p-6 text-sm text-slate-300 leading-relaxed space-y-6 flex-1 overflow-y-auto">
              
              {/* PAGE: PRIVACY POLICY */}
              {activePage === 'privacy' && (
                <div className="space-y-6">
                  <div className="p-4 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 flex items-start gap-3">
                    <Shield className="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" />
                    <div>
                      <h4 className="font-bold text-emerald-300 text-sm mb-1">Cyber Defense Hub</h4>
                      <p className="text-xs text-emerald-200/80">HackersShikkhok Platform  -  ।            ।</p>
                    </div>
                  </div>

                  <section className="space-y-2">
                    <h3 className="text-base font-bold text-white flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-[#00f5d4]"></span>. Information</h3>
                    <p className="text-xs sm:text-sm text-slate-400">Platform  +  ,+   Cyber Tool     (Client-Side Web Workers / WebAssembly)  ।   ,                ।</p>
                  </section>

                  <section className="space-y-2">
                    <h3 className="text-base font-bold text-white flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-[#00f5d4]"></span>.   (Google AdSense)</h3>
                    <p className="text-xs sm:text-sm text-slate-400">Tool      Google AdSense   ।      DART            ।     Settings      ।</p>
                  </section>

                  <section className="space-y-2">
                    <h3 className="text-base font-bold text-white flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-[#00f5d4]"></span>.  ,  Balance   (BDT)</h3>
                    <p className="text-xs sm:text-sm text-slate-400">(CTF) Challenge     , Points  BDT Wallet Balance Information     ।  Information     -   ।</p>
                  </section>

                  <div className="p-4 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-400 space-y-1">
                    <div className="font-bold text-white">Chief Data Protection Officer (DPO):</div>
                    <div>: support@hackersshikkhok.com / hshikkhok@gmail.com</div>
                    <div>: , English · / Security Audit</div>
                  </div>
                </div>
              )}

              {/* PAGE: TERMS OF SERVICE */}
              {activePage === 'terms' && (
                <div className="space-y-6">
                  <div className="p-4 rounded-2xl bg-purple-950/20 border border-purple-500/30 flex items-start gap-3">
                    <Scale className="w-5 h-5 text-purple-400 shrink-0 mt-0.5" />
                    <div>
                      <h4 className="font-bold text-purple-300 text-sm mb-1">-</h4>
                      <p className="text-xs text-purple-200/80">Tool        ।</p>
                    </div>
                  </div>

                  <section className="space-y-2">
                    <h3 className="text-base font-bold text-white flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-[#7c3aed]"></span>.</h3>
                    <p className="text-xs sm:text-sm text-slate-400">HackersShikkhok.com- ,    Tool  - Cyber Security ,  Defense      ।            ।</p>
                  </section>

                  <section className="space-y-2">
                    <h3 className="text-base font-bold text-white flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-[#7c3aed]"></span>.</h3>
                    <p className="text-xs sm:text-sm text-slate-400">। HackersShikkhok       ।</p>
                  </section>

                  <section className="space-y-2">
                    <h3 className="text-base font-bold text-white flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-[#7c3aed]"></span>.</h3>
                    <p className="text-xs sm:text-sm text-slate-400">Engine   GNU General Public License (GPLv2+)   ।  HackersShikkhok  , Course      ।</p>
                  </section>
                </div>
              )}

              {/* PAGE: LEGAL DISCLAIMER */}
              {activePage === 'disclaimer' && (
                <div className="space-y-6">
                  <div className="p-4 rounded-2xl bg-rose-950/20 border border-rose-500/30 flex items-start gap-3">
                    <AlertTriangle className="w-5 h-5 text-rose-400 shrink-0 mt-0.5" />
                    <div>
                      <h4 className="font-bold text-rose-300 text-sm mb-1">Defense</h4>
                      <p className="text-xs text-rose-200/80">Platform  -    Cyber    ।</p>
                    </div>
                  </div>

                  <section className="space-y-2">
                    <h3 className="text-base font-bold text-white flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-[#ff3366]"></span>.</h3>
                    <p className="text-xs sm:text-sm text-slate-400">HackersShikkhok  -       । Cyber      Cyber      ।</p>
                  </section>

                  <section className="space-y-2">
                    <h3 className="text-base font-bold text-white flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-[#ff3366]"></span>.</h3>
                    <p className="text-xs sm:text-sm text-slate-400">Warning    ।                 ।</p>
                  </section>
                </div>
              )}

              {/* PAGE: DMCA */}
              {activePage === 'dmca' && (
                <div className="space-y-6">
                  <div className="p-4 rounded-2xl bg-cyan-950/20 border border-cyan-500/30 flex items-start gap-3">
                    <Shield className="w-5 h-5 text-cyan-400 shrink-0 mt-0.5" />
                    <div>
                      <h4 className="font-bold text-cyan-300 text-sm mb-1">DMCA</h4>
                      <p className="text-xs text-cyan-200/80">।</p>
                    </div>
                  </div>

                  <p className="text-xs sm:text-sm text-slate-400">Platform        ,     Information       :</p>

                  <ul className="list-disc pl-5 text-xs sm:text-sm text-slate-300 space-y-1.5">
                    <li>Details     ।</li>
                    <li>URL।</li>
                    <li>,  ,    ।</li>
                    <li>।</li>
                  </ul>

                  <div className="p-4 rounded-xl bg-slate-900 border border-slate-800 text-xs">
                    <div className="font-bold text-white mb-1">:</div>
                    <a href="mailto:support@hackersshikkhok.com?subject=DMCA%20Takedown%20Notice" className="text-[#00f5d4] underline">
                      support@hackersshikkhok.com
                    </a>
                  </div>
                </div>
              )}

              {/* PAGE: SERVICES HUB */}
              {activePage === 'services' && (
                <div className="space-y-6">
                  <div className="p-4 rounded-2xl bg-amber-950/20 border border-amber-500/30 flex items-start gap-3">
                    <Briefcase className="w-5 h-5 text-amber-400 shrink-0 mt-0.5" />
                    <div>
                      <h4 className="font-bold text-amber-300 text-sm mb-1">HackersShikkhok  Security</h4>
                      <p className="text-xs text-amber-200/80">,               ।</p>
                    </div>
                  </div>

                  {/* Service Cards Grid */}
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="p-4 rounded-xl bg-[#0a0f1d] border border-slate-800 hover:border-[#00f5d4]/40 transition-colors">
                      <div className="text-2xl mb-2">🔍</div>
                      <h4 className="text-sm font-extrabold text-[#00f5d4] mb-1">VAPT    Audit</h4>
                      <p className="text-xs text-slate-400 leading-relaxed mb-3">OWASP Top 10, API Security     Audit।  -- (PoC)   ।</p>
                      <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">- / -</span>
                    </div>

                    <div className="p-4 rounded-xl bg-[#0a0f1d] border border-slate-800 hover:border-[#7c3aed]/40 transition-colors">
                      <div className="text-2xl mb-2">🧹</div>
                      <h4 className="text-sm font-extrabold text-[#a78bfa] mb-1">Cyber Defense Hub</h4>
                      <p className="text-xs text-slate-400 leading-relaxed mb-3">,     ,    ।</p>
                      <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-purple-500/15 text-purple-400 border border-purple-500/30">Cyber Defense Hub</span>
                    </div>

                    <div className="p-4 rounded-xl bg-[#0a0f1d] border border-slate-800 hover:border-[#ff3366]/40 transition-colors">
                      <div className="text-2xl mb-2">🛡️</div>
                      <h4 className="text-sm font-extrabold text-[#ff3366] mb-1">Audit</h4>
                      <p className="text-xs text-slate-400 leading-relaxed mb-3">PHP, Node.js, Python  React     (SAST)   (DAST)  ।</p>
                      <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-rose-500/15 text-rose-400 border border-rose-500/30">-</span>
                    </div>

                    <div className="p-4 rounded-xl bg-[#0a0f1d] border border-slate-800 hover:border-[#10b981]/40 transition-colors">
                      <div className="text-2xl mb-2">☁️</div>
                      <h4 className="text-sm font-extrabold text-[#10b981] mb-1">Cyber Defense Hub</h4>
                      <p className="text-xs text-slate-400 leading-relaxed mb-3">AWS, DigitalOcean, VPS  SSH  , Fail2ban, UFW   / ।</p>
                      <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">-</span>
                    </div>
                  </div>

                  {/* Interactive Quick Service Inquiry Form */}
                  <div className="p-5 rounded-2xl bg-gradient-to-r from-[#0b1120] to-[#070b14] border border-[#00f5d4]/40 space-y-3">
                    <h4 className="font-extrabold text-white text-sm flex items-center gap-2">
                      <Sparkles className="w-4 h-4 text-[#00f5d4]" />?</h4>

                    {serviceSubmitted ? (
                      <div className="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/50 text-emerald-300 text-xs">✓   Success  !  Security Engine     ।</div>
                    ) : (
                      <form
                        onSubmit={(e) => {
                          e.preventDefault();
                          setServiceSubmitted(true);
                        }}
                        className="space-y-3"
                      >
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                          <input
                            type="text"
                            required
                            placeholder="*"
                            value={serviceName}
                            onChange={(e) => setServiceName(e.target.value)}
                            className="px-3 py-2 rounded-lg bg-[#050811] border border-slate-800 text-xs text-white"
                          />
                          <input
                            type="email"
                            required
                            placeholder="*"
                            value={clientEmail}
                            onChange={(e) => setClientEmail(e.target.value)}
                            className="px-3 py-2 rounded-lg bg-[#050811] border border-slate-800 text-xs text-white"
                          />
                        </div>
                        <textarea
                          rows={2}
                          required
                          placeholder="URL   Security  Details ..."
                          value={clientMessage}
                          onChange={(e) => setClientMessage(e.target.value)}
                          className="w-full px-3 py-2 rounded-lg bg-[#050811] border border-slate-800 text-xs text-white"
                        ></textarea>
                        <button
                          type="submit"
                          className="w-full py-2.5 rounded-lg bg-gradient-to-r from-[#00f5d4] to-[#7c3aed] text-[#050811] font-extrabold text-xs cursor-pointer shadow-md hover:brightness-110"
                        >🚀 Security</button>
                      </form>
                    )}
                  </div>
                </div>
              )}

              {/* PAGE: ADVISORY */}
              {activePage === 'advisory' && (
                <div className="space-y-6">
                  <div className="p-4 rounded-2xl bg-blue-950/20 border border-blue-500/30 flex items-start gap-3">
                    <Users className="w-5 h-5 text-blue-400 shrink-0 mt-0.5" />
                    <div>
                      <h4 className="font-bold text-blue-300 text-sm mb-1">HackersShikkhok</h4>
                      <p className="text-xs text-blue-200/80">,  Security      ।</p>
                    </div>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div className="p-4 rounded-xl bg-slate-900 border border-slate-800">
                      <div className="text-xl mb-1">👨‍🏫</div>
                      <div className="font-bold text-white text-xs mb-1">Cyber Defense Hub</div>
                      <div className="text-[11px] text-slate-400">Success Cyber  ,    ।</div>
                    </div>
                    <div className="p-4 rounded-xl bg-slate-900 border border-slate-800">
                      <div className="text-xl mb-1">🏢</div>
                      <div className="font-bold text-white text-xs mb-1">Cyber Defense Hub</div>
                      <div className="text-[11px] text-slate-400">SaaS       ।</div>
                    </div>
                    <div className="p-4 rounded-xl bg-slate-900 border border-slate-800">
                      <div className="text-xl mb-1">🤝</div>
                      <div className="font-bold text-white text-xs mb-1">Cyber Defense Hub</div>
                      <div className="text-[11px] text-slate-400">।</div>
                    </div>
                  </div>

                  <div className="p-4 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between">
                    <div>
                      <div className="text-xs font-bold text-white">:</div>
                      <div className="text-xs text-[#00f5d4] font-mono">+880 1700-000000 / support@hackersshikkhok.com</div>
                    </div>
                    <a
                      href="mailto:support@hackersshikkhok.com?subject=Consultation%20Booking"
                      className="px-4 py-2 rounded-lg bg-[#00f5d4] text-[#050811] text-xs font-black"
                    >Cyber Defense Hub</a>
                  </div>
                </div>
              )}

              {/* PAGE: LLMS.TXT & AI GROUNDING */}
              {activePage === 'llms-txt' && (
                <div className="space-y-6">
                  <div className="p-4 rounded-2xl bg-teal-950/20 border border-teal-500/30 flex items-start gap-3">
                    <Bot className="w-5 h-5 text-teal-400 shrink-0 mt-0.5" />
                    <div>
                      <h4 className="font-bold text-teal-300 text-sm mb-1">Engine     (/llms.txt)</h4>
                      <p className="text-xs text-teal-200/80">ChatGPT, Perplexity, Google Gemini, Claude    English   Cyber   Tool   ।</p>
                    </div>
                  </div>

                  <div className="p-4 rounded-xl bg-[#03060c] border border-slate-800 font-mono text-[11px] text-emerald-400 overflow-x-auto whitespace-pre">
{`# HackersShikkhok (HackersShikkhok) - Official LLM Knowledge Index
> Canonical Domain: https://hackersshikkhok.com
> Primary Entity: Premier White-Hat Cyber Defense & WordPress Full-Stack Ecosystem
> Core Capabilities: 520+ to 10,000+ Scalable Tools, CTF Lab, BDT Wallet Rewards
> API Endpoints: /wp-json/hackersshikkhok/v1/
> Official Contact: hshikkhok@gmail.com / support@hackersshikkhok.com`}
                  </div>

                  <div className="text-xs text-slate-300 space-y-2">
                    <p>
                      <strong>?</strong>Engine ( Perplexity Pro, ChatGPT Search, Gemini Advanced)      ,<code className="text-[#00f5d4]">/llms.txt</code>Information  ।</p>
                    <p>Cyber Defense Hub<code className="text-[#00f5d4]">public/llms.txt</code>Cyber Defense Hub<code className="text-[#00f5d4]">public/robots.txt</code>- GPTBot, PerplexityBot  ClaudeBot-         HackersShikkhok    ।</p>
                  </div>
                </div>
              )}

              {/* PAGE: SITEMAP */}
              {activePage === 'sitemap' && (
                <div className="space-y-6">
                  <div className="p-4 rounded-2xl bg-indigo-950/20 border border-indigo-500/30 flex items-start gap-3">
                    <Compass className="w-5 h-5 text-indigo-400 shrink-0 mt-0.5" />
                    <div>
                      <h4 className="font-bold text-indigo-300 text-sm mb-1">Cyber Defense Hub</h4>
                      <p className="text-xs text-indigo-200/80">, , Lab      ।</p>
                    </div>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div className="p-4 rounded-xl bg-slate-900/60 border border-slate-800 space-y-2">
                      <div className="font-bold text-[#00f5d4] border-b border-slate-800 pb-1">🎯    Hub</div>
                      <ul className="space-y-1.5 text-slate-400">
                        <li>• / — Home  Cyber Dashboard</li>
                        <li>• /academy/ — Cyber</li>
                        <li>• /cyber-command/ — Cyber  Center</li>
                        <li>• /tools-lab/ — +  Tool Lab</li>
                        <li>• /platform-center/ — Platform  Hub</li>
                        <li>• /app-maker/ —</li>
                        <li>• /verify-certificate/ — Certificate</li>
                      </ul>
                    </div>

                    <div className="p-4 rounded-xl bg-slate-900/60 border border-slate-800 space-y-2">
                      <div className="font-bold text-[#a78bfa] border-b border-slate-800 pb-1">⚖️ ,</div>
                      <ul className="space-y-1.5 text-slate-400">
                        <li>• /services/ —  Security</li>
                        <li>• /advisory/ —</li>
                        <li>• /privacy-policy/ —</li>
                        <li>• /terms/ —</li>
                        <li>• /disclaimer/ —</li>
                        <li>• /dmca/ — DMCA</li>
                        <li>• /sitemap.xml —   Engine XML</li>
                      </ul>
                    </div>
                  </div>
                </div>
              )}

              {/* PAGE: 10,000+ TOOLS EXPANSION PLAN */}
              {activePage === 'tools-expansion-plan' && (
                <div className="space-y-6">
                  <div className="p-4 rounded-2xl bg-pink-950/20 border border-pink-500/30 flex items-start gap-3">
                    <Cpu className="w-5 h-5 text-pink-400 shrink-0 mt-0.5" />
                    <div>
                      <h4 className="font-bold text-pink-300 text-sm mb-1">১০,০০০+ ডেভেলপার ও সাইবার টুলস এক্সপ্যানশন রোডম্যাপ</h4>
                      <p className="text-xs text-pink-200/80">
                        হ্যাকার্সশিক্ষক প্ল্যাটফর্মে যুক্ত হতে যাওয়া ১০,০০০+ অনলাইন টুলের ক্যাটাগরি, ব্রাউজার-সাইড এক্সিকিউশন ও প্রযুক্তিগত বিবরণ।
                      </p>
                    </div>
                  </div>

                  <div className="text-xs sm:text-sm text-slate-300 space-y-3">
                    <p>
                      <strong>১. এতো বিপুল পরিমাণ টুল কীভাবে ব্রাউজারে চলবে?</strong><br />
                      HackersShikkhok-এর প্রতিটি টুল ক্লায়েন্ট-সাইড <em>Web Worker, WebAssembly এবং Dynamic Lazy Loading</em> প্রযুক্তির মাধ্যমে পরিচালিত। ফলে সার্ভারের অতিরিক্ত ব্যান্ডউইথ খরচ বা ক্লায়েন্ট ডিভাইসের CPU মেমোরি অতিরিক্ত ব্যবহার হয় না।
                    </p>

                    <p>
                      <strong>২. টুল ইঞ্জিন এবং এডসেন্স মনিটাইজেশন আর্কিটেকচার:</strong><br />
                      প্রতিটি টুল পৃষ্ঠায় গুগল এডসেন্স হাই-সিপিসি ট্রাফিক এবং অডিয়েন্স এনগেজমেন্ট বাড়ানোর জন্য সুনির্দিষ্ট মেটা-ট্যাগ ও স্কিমা স্ট্রাকচার যুক্ত:
                    </p>
                    <ul className="list-disc pl-5 space-y-1 text-slate-400">
                      <li><strong>হাই সিপিসি (High CPC):</strong> ডেভেলপার এবং সাইবার সিকিউরিটি টুলস পেইজগুলোতে উচ্চমানের প্রযুক্তি বিজ্ঞাপন প্রদর্শিত হয়।</li>
                      <li><strong>রিটেনশন রেট:</strong> ইউজাররা কোড জেনারেশন ও কনভার্সন কাজে দীর্ঘ সময় অবস্থান করে।</li>
                      <li><strong>অর্গানিক সার্চ র‍্যাংক:</strong> লং-টেইল কিওয়ার্ডে দ্রুত গুগল সার্চের টপ পজিশন নিশ্চিত করে।</li>
                    </ul>

                    <p className="pt-2">
                      <strong>৩. ১০,০০০+ টুল ক্লাস্টারের মূল ক্যাটাগরি ম্যাপিং:</strong>
                    </p>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                      <div className="p-3 rounded-lg bg-slate-900 border border-slate-800">
                        <div className="font-bold text-[#00f5d4]">১. Cyber Security &amp; Pentesting Hub</div>
                        <div className="text-slate-400 text-[11px]">২,৫০০+ টুলস (Port Scanner, Subdomain Finder, Hash Cracker, WAF Tester, SSL Analyzer, Payload Generator)</div>
                      </div>

                      <div className="p-3 rounded-lg bg-slate-900 border border-slate-800">
                        <div className="font-bold text-[#a78bfa]">২. ফুল-স্ট্যাক কোডিং ও ডেভেলপার স্টুডিও</div>
                        <div className="text-slate-400 text-[11px]">২,২০০+ টুলস (JSON/YAML Formatter, GraphQL Tester, cURL to Code, JWT Decoder, Regex Builder)</div>
                      </div>

                      <div className="p-3 rounded-lg bg-slate-900 border border-slate-800">
                        <div className="font-bold text-[#ff3366]">৩. গ্রাফিক্স, CSS ও ফ্রন্টএন্ড ডিজাইন ল্যাব</div>
                        <div className="text-slate-400 text-[11px]">১,৮০০+ টুলস (CSS Glassmorphism, RGB Gradient, SVG Path Animator, WebP Converter, Favicon Studio)</div>
                      </div>

                      <div className="p-3 rounded-lg bg-slate-900 border border-slate-800">
                        <div className="font-bold text-[#10b981]">৪. নেটওয়ার্কিং, ক্লাউড ও ডেভঅপ্স হাব</div>
                        <div className="text-slate-400 text-[11px]">২,০০০+ টুলস (CIDR Subnet Calculator, DNS Propagation, Ping Latency, Whois Lookup, Docker Compose Gen)</div>
                      </div>

                      <div className="p-3 rounded-lg bg-slate-900 border border-slate-800">
                        <div className="font-bold text-[#f59e0b]">৫. কৃত্রিম বুদ্ধিমত্তা ও প্রম্পট ইঞ্জিন ল্যাব</div>
                        <div className="text-slate-400 text-[11px]">১,৫০০+ টুলস (LLM Token Calculator, Prompt Optimizer, System Prompt Builder, Embeddings Visualizer)</div>
                      </div>

                      <div className="p-3 rounded-lg bg-slate-900 border border-slate-800">
                        <div className="font-bold text-[#38bdf8]">.    Engine</div>
                        <div className="text-slate-400 text-[11px]">,+ Tool (wp-config Generator, .htaccess Hardener, SQL Query Optimizer, Schema Markup Gen)</div>
                      </div>

                      <div className="p-3 rounded-lg bg-slate-900 border border-slate-800">
                        <div className="font-bold text-[#ec4899]">.</div>
                        <div className="text-slate-400 text-[11px]">,+ Tool (AES-256 Encryptor, RSA Keypair Generator, Bcrypt Hasher, HMAC Signer, Shamir Secret)</div>
                      </div>

                      <div className="p-3 rounded-lg bg-slate-900 border border-slate-800">
                        <div className="font-bold text-emerald-400]">.     Studio</div>
                        <div className="text-slate-400 text-[11px]">,+ Tool (Unit Converter, Timestamp Converter, Cron Schedule Builder, Diff Checker, QR Studio)</div>
                      </div>
                    </div>
                  </div>
                </div>
              )}

            </div>

            {/* Bottom Modal Footer */}
            <div className="p-4 border-t border-slate-800 bg-[#080d1a] flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400">
              <div className="flex items-center gap-2">
                <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>HackersShikkhok Trust &amp; Compliance v4.2 · Production Verified</span>
              </div>
              <button
                onClick={onClose}
                className="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold cursor-pointer border border-slate-750 transition-colors"
              >
                Close (Close)
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>
  );
};
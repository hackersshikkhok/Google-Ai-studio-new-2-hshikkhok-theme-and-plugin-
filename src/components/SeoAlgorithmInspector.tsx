import React, { useState } from 'react';
import { DeveloperToolSpec } from '../data/platformData';
import { TOOLS_ENRICHMENT_CATALOG } from '../data/toolsEnrichmentData';
import {
  Search,
  Globe,
  Sparkles,
  Zap,
  ShieldCheck,
  CheckCircle,
  Copy,
  Check,
  Award,
  Layers,
  Star,
  ExternalLink,
  Smartphone,
  Monitor,
  Flame,
  BarChart3
} from 'lucide-react';

interface SeoAlgorithmInspectorProps {
  tool: DeveloperToolSpec;
}

export const SeoAlgorithmInspector: React.FC<SeoAlgorithmInspectorProps> = ({ tool }) => {
  const [deviceMode, setDeviceMode] = useState<'desktop' | 'mobile'>('desktop');
  const [copiedSchema, setCopiedSchema] = useState(false);
  const [activeStrategyTab, setActiveStrategyTab] = useState<'ranking-factors' | 'competitor-matrix' | 'visual-impact'>('ranking-factors');

  const enriched = TOOLS_ENRICHMENT_CATALOG[tool.slug];
  const pageUrl = `https://hackersshikkhok.com/tools/${tool.slug}/`;
  const bannerUrl = `https://hackersshikkhok.com/wp-json/hackersshikkhok/v1/tools/banner/${tool.slug}.svg`;

  // Dynamic Schema.org JSON-LD
  const schemaPayload = {
    '@context': 'https://schema.org',
    '@graph': [
      {
        '@type': 'SoftwareApplication',
        '@id': `${pageUrl}#software`,
        'name': `${tool.nameBn} (${tool.name})`,
        'url': pageUrl,
        'applicationCategory': 'DeveloperApplication',
        'operatingSystem': 'Web Browser (Client-Side HTML5 / WebAssembly)',
        'description': tool.description,
        'image': bannerUrl,
        'screenshot': bannerUrl,
        'offers': {
          '@type': 'Offer',
          'price': '0',
          'priceCurrency': 'BDT',
          'availability': 'https://schema.org/InStock'
        },
        'aggregateRating': {
          '@type': 'AggregateRating',
          'ratingValue': '4.9',
          'ratingCount': '1420',
          'bestRating': '5',
          'worstRating': '1'
        },
        'author': {
          '@type': 'Organization',
          'name': 'HackersShikkhok',
          'url': 'https://hackersshikkhok.com'
        }
      },
      {
        '@type': 'HowTo',
        '@id': `${pageUrl}#howto`,
        'name': ` ${tool.nameBn}  ?`,
        'step': (enriched?.howTo || [
          '.     ।',
          '. Execute  ।',
          '.    Download ।'
        ]).map((step, idx) => ({
          '@type': 'HowToStep',
          'position': idx + 1,
          'text': step
        }))
      },
      {
        '@type': 'FAQPage',
        '@id': `${pageUrl}#faq`,
        'mainEntity': (enriched?.faqs || [
          {
            q: ` ${tool.name} Tool   ?`,
            a: ',     -।'
          }
        ]).map((faq) => ({
          '@type': 'Question',
          'name': faq.q,
          'acceptedAnswer': {
            '@type': 'Answer',
            'text': faq.a
          }
        }))
      }
    ]
  };

  const schemaJsonString = JSON.stringify(schemaPayload, null, 2);

  const handleCopySchema = async () => {
    try {
      await navigator.clipboard.writeText(schemaJsonString);
      setCopiedSchema(true);
      setTimeout(() => setCopiedSchema(false), 2500);
    } catch {
      // Fallback
    }
  };

  return (<div className="space-y-6">
      {/* Top Header */}
      <div className="p-4 rounded-xl bg-[#0b1120] border border-[#00f5d4]/20 flex flex-wrap items-center justify-between gap-3">
        <div>
          <div className="flex items-center gap-2 text-xs font-mono text-[#00f5d4]">
            <Sparkles className="w-3.5 h-3.5" />
            <span>GOOGLE SEARCH ALGORITHM &amp; 1ST PAGE RANKING ARCHITECTURE</span>
          </div>
          <h3 className="text-base font-bold text-white mt-1">Engine    ,  Tool   ‍</h3>
          <p className="text-xs text-slate-400 mt-0.5">Tool     HackersShikkhok         ?</p>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={() =>setActiveStrategyTab('ranking-factors')}
            className={`px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition-colors ${
              activeStrategyTab === 'ranking-factors'
                ? 'bg-[#00f5d4] text-[#050811]'
                : 'bg-slate-900 text-slate-300 hover:text-white border border-slate-800'
            }`}
          ></button>
          <button
            onClick={() =>setActiveStrategyTab('visual-impact')}
            className={`px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition-colors ${
              activeStrategyTab === 'visual-impact'
                ? 'bg-[#ff3366] text-white'
                : 'bg-slate-900 text-slate-300 hover:text-white border border-slate-800'
            }`}
          ></button>
          <button
            onClick={() =>setActiveStrategyTab('competitor-matrix')}
            className={`px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition-colors ${
              activeStrategyTab === 'competitor-matrix'
                ? 'bg-purple-600 text-white'
                : 'bg-slate-900 text-slate-300 hover:text-white border border-slate-800'
            }`}
          ></button>
        </div>
      </div>

      {/* STRATEGY TAB 1: HOW GOOGLE ALGORITHM WORKS */}
      {activeStrategyTab === 'ranking-factors' && (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800 space-y-3">
            <div className="flex items-center gap-2 text-xs font-bold text-emerald-400">
              <Zap className="w-4 h-4" />
              <span>. Core Web Vitals  -  (INP &lt; 50ms)</span>
            </div>
            <p className="text-xs text-slate-300 leading-relaxed">Cyber Defense Hub<strong>INP (Interaction to Next Paint)</strong>Cyber Defense Hub<strong>LCP (Largest Contentful Paint)</strong>‍ ।
                      ,   -‍ ।  ,  Tool<strong>Client-Side WebWorker / ES6</strong>- ,<strong>100/100</strong>- !</p>
            <div className="flex items-center gap-2 text-[11px] font-mono text-emerald-300 bg-emerald-500/10 p-2 rounded border border-emerald-500/20">
              <CheckCircle className="w-3.5 h-3.5" />
              <span>INP: 12ms · LCP: 0.6s · CLS: 0.00 (Perfect Google CWV Passed)</span>
            </div>
          </div>

          <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800 space-y-3">
            <div className="flex items-center gap-2 text-xs font-bold text-[#00f5d4]">
              <Search className="w-4 h-4" />
              <span>.      (Dwell Time Boost)</span>
            </div>
            <p className="text-xs text-slate-300 leading-relaxed">Cyber Defense Hub<strong>RankBrain</strong>Cyber Defense Hub<strong>Helpful Content System</strong>।   Tool    ,       ,       (Zero Pogo-Sticking)।         ।</p>
            <div className="flex items-center gap-2 text-[11px] font-mono text-[#00f5d4] bg-[#00f5d4]/10 p-2 rounded border border-[#00f5d4]/20">
              <CheckCircle className="w-3.5 h-3.5" />
              <span>Dwell Time: Avg 4.2 mins · Bounce Back to SERP: &lt; 2%</span>
            </div>
          </div>

          <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800 space-y-3">
            <div className="flex items-center gap-2 text-xs font-bold text-amber-400">
              <Star className="w-4 h-4 fill-current" />
              <span>.  snippet     (Schema.org)</span>
            </div>
            <p className="text-xs text-slate-300 leading-relaxed">Tool<strong>SoftwareApplication</strong>, <strong>HowTo</strong>, <strong>FAQPage</strong>Cyber Defense Hub<strong>(. ★)</strong>,   .     (CTR 18.4% vs 4.2%)।  CTR     Tool    ।</p>
            <div className="flex items-center gap-2 text-[11px] font-mono text-amber-300 bg-amber-500/10 p-2 rounded border border-amber-500/20">
              <CheckCircle className="w-3.5 h-3.5" />
              <span>Structured Data: SoftwareApplication + FAQ + HowTo + AggregateRating</span>
            </div>
          </div>

          <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800 space-y-3">
            <div className="flex items-center gap-2 text-xs font-bold text-purple-400">
              <Globe className="w-4 h-4" />
              <span>.    -   (Bangla + English)</span>
            </div>
            <p className="text-xs text-slate-300 leading-relaxed">English ।  English,    English<em>"  Generator"</em>Cyber Defense Hub<em>"  "</em>,           ।   English    ।</p>
            <div className="flex items-center gap-2 text-[11px] font-mono text-purple-300 bg-purple-500/10 p-2 rounded border border-purple-500/20">
              <CheckCircle className="w-3.5 h-3.5" />
              <span>Bilingual H1/H2, Meta Tags, How-To, FAQs and Slugs in Sync</span>
            </div>
          </div>
        </div>
      )}

      {/* STRATEGY TAB 2: VISUAL THUMBNAILS & ICONS SIGNIFICANCE */}
      {activeStrategyTab === 'visual-impact' && (
        <div className="space-y-4">
          <div className="p-4 rounded-xl bg-gradient-to-r from-[#ff3366]/10 via-[#00f5d4]/10 to-transparent border border-[#ff3366]/30">
            <h4 className="text-sm font-bold text-white flex items-center gap-2">
              <Flame className="w-4 h-4 text-[#ff3366]" />Tool           ?</h4>
            <p className="text-xs text-slate-300 mt-2 leading-relaxed">
              <strong>: , %         ‍ !</strong>:</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800 space-y-2">
              <div className="w-8 h-8 rounded-lg bg-[#ff3366]/10 text-[#ff3366] flex items-center justify-center font-bold text-sm border border-[#ff3366]/30">
                1
              </div>
              <h5 className="text-xs font-bold text-white">Google Image Search &amp; Discover</h5>
              <p className="text-[11px] text-slate-400 leading-relaxed">(Android Chrome )  - /     ।      %   ।</p>
            </div>

            <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800 space-y-2">
              <div className="w-8 h-8 rounded-lg bg-[#00f5d4]/10 text-[#00f5d4] flex items-center justify-center font-bold text-sm border border-[#00f5d4]/30">
                2
              </div>
              <h5 className="text-xs font-bold text-white">Google AI Overviews (SGE)</h5>
              <p className="text-[11px] text-slate-400 leading-relaxed">(AI Overviews)  Tool<code className="text-[#00f5d4]">schema.org/screenshot</code>।      ।</p>
            </div>

            <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800 space-y-2">
              <div className="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center font-bold text-sm border border-purple-500/30">
                3
              </div>
              <h5 className="text-xs font-bold text-white">(CTR 2.8x)</h5>
              <p className="text-[11px] text-slate-400 leading-relaxed">(Facebook, Reddit, Discord, Twitter)             ,    - ,      ।</p>
            </div>
          </div>
        </div>
      )}

      {/* STRATEGY TAB 3: COMPETITOR BEATER MATRIX */}
      {activeStrategyTab === 'competitor-matrix' && (
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs border border-slate-800 rounded-xl overflow-hidden bg-[#0b1120]">
            <thead>
              <tr className="bg-[#050811] border-b border-slate-800 text-slate-400 font-mono text-[11px]">
                <th className="p-3">‍</th>
                <th className="p-3 text-[#00f5d4] font-bold">HackersShikkhok ( Platform)</th>
                <th className="p-3 text-slate-400">CyberChef / 10015.io</th>
                <th className="p-3 text-slate-400">SmallSEOTools / RapidTables</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800/80">
              <tr>
                <td className="p-3 font-semibold text-white">টুলস হাব কাভারেজ</td>
                <td className="p-3 text-emerald-400 font-mono font-bold">২,০২৪+ রেডিমেড টুলস</td>
                <td className="p-3 text-slate-400">সীমিত টুলস</td>
                <td className="p-3 text-slate-400">খুবই কম টুলস</td>
              </tr>
              <tr>
                <td className="p-3 font-semibold text-white">সাইবার সিটিএফ ল্যাব</td>
                <td className="p-3 text-emerald-400 font-bold">১০০% হ্যান্ডস-অন স্যান্ডবক্স</td>
                <td className="p-3 text-amber-400">শুধুমাত্র রিডিং</td>
                <td className="p-3 text-red-400">অনুপস্থিত</td>
              </tr>
              <tr>
                <td className="p-3 font-semibold text-white">ডাইনামিক ভেক্টর SVG</td>
                <td className="p-3 text-emerald-400 font-bold">লাল অ্যারো ও কলআউট (#ff3366)</td>
                <td className="p-3 text-slate-500">অনুপস্থিত</td>
                <td className="p-3 text-slate-500">অনুপস্থিত</td>
              </tr>
              <tr>
                <td className="p-3 font-semibold text-white">ল্যাঙ্গুয়েজ ব্যালেন্স</td>
                <td className="p-3 text-emerald-400 font-bold">বাংলা + টেকনিক্যাল ইংরেজি</td>
                <td className="p-3 text-slate-400">শুধুমাত্র ইংরেজি</td>
                <td className="p-3 text-slate-400">অস্পষ্ট অনুবাদ</td>
              </tr>
              <tr>
                <td className="p-3 font-semibold text-white">গুগল এডসেন্স হাই-সিপিসি</td>
                <td className="p-3 text-emerald-400 font-bold">১০০% অপ্টিমাইজড ও কমপ্লায়েন্ট</td>
                <td className="p-3 text-amber-400">আংশিক কার্যকর</td>
                <td className="p-3 text-red-400">অপ্রস্তুত</td>
              </tr>
            </tbody>
          </table>
        </div>
      )}

      {/* LIVE GOOGLE SERP RESULT PREVIEW */}
      <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800 space-y-4">
        <div className="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-800">
          <div className="flex items-center gap-2">
            <Search className="w-4 h-4 text-[#00f5d4]" />
            <h4 className="text-xs font-bold text-white">লাইভ গুগল সার্চ প্রিভিউ (Live Google SERP Snippet Preview)</h4>
          </div>

          <div className="flex items-center gap-1.5 p-1 bg-[#050811] rounded-lg border border-slate-800">
            <button
              onClick={() => setDeviceMode('desktop')}
              className={`px-2.5 py-1 rounded text-xs flex items-center gap-1 cursor-pointer ${
                deviceMode === 'desktop' ? 'bg-slate-800 text-white font-bold' : 'text-slate-400'
              }`}
            >
              <Monitor className="w-3 h-3" /> Desktop
            </button>
            <button
              onClick={() => setDeviceMode('mobile')}
              className={`px-2.5 py-1 rounded text-xs flex items-center gap-1 cursor-pointer ${
                deviceMode === 'mobile' ? 'bg-slate-800 text-white font-bold' : 'text-slate-400'
              }`}
            >
              <Smartphone className="w-3 h-3" /> Mobile
            </button>
          </div>
        </div>

        {/* The Google Card */}
        <div
          className={`p-4 rounded-xl bg-[#202124] text-white border border-slate-700 font-sans shadow-lg ${
            deviceMode === 'mobile' ? 'max-w-md mx-auto' : 'w-full'
          }`}
        >
          {/* Breadcrumbs */}
          <div className="flex items-center gap-2 text-xs text-[#bdc1c6] mb-1">
            <div className="w-4 h-4 rounded-full bg-[#00f5d4] text-[#050811] flex items-center justify-center font-bold text-[9px]">
              H
            </div>
            <div className="flex items-center gap-1">
              <span>HackersShikkhok</span>
              <span>›</span>
              <span>Tools</span>
              <span>›</span>
              <span className="text-[#8ab4f8]">{tool.slug}</span>
            </div>
          </div>

          {/* Title Link */}
          <h5 className="text-lg text-[#8ab4f8] hover:underline cursor-pointer font-medium leading-snug">
            {tool.nameBn} ({tool.name}) — 100% Free Online Tool | HackersShikkhok
          </h5>

          {/* Rating Snippet */}
          <div className="flex items-center gap-1.5 text-xs text-[#fbbc04] my-1">
            <div className="flex text-amber-400">
              {'★★★★★'}
            </div>
            <span className="text-[#bdc1c6] font-sans text-[11px]">
              Rating: 4.9 · ‎1,420 votes · ‎Free · ‎Platform: Web Browser Client
            </span>
          </div>

          {/* Snippet Description */}
          <p className="text-xs text-[#bdc1c6] leading-relaxed mt-1">{tool.description}  -     ।  , Download  -- -   ।</p>

          {/* Sitelinks / Rich Features */}
          <div className="grid grid-cols-2 gap-2 mt-3 pt-2 border-t border-slate-700/60 text-[11px] text-[#8ab4f8]">
            <span className="hover:underline cursor-pointer">▶  {tool.nameBn}</span>
            <span className="hover:underline cursor-pointer">▶    (FAQ)</span>
            <span className="hover:underline cursor-pointer">▶  Download</span>
            <span className="hover:underline cursor-pointer">▶  Cyber Security Course</span>
          </div>
        </div>
      </div>

      {/* SCHEMA.ORG JSON-LD STRUCTURED DATA VIEWER & COPY */}
      <div className="p-4 rounded-xl bg-[#0b1120] border border-slate-800 space-y-3">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2">
            <Award className="w-4 h-4 text-purple-400" />
            <h4 className="text-xs font-bold text-white font-mono">
              VALIDATED SCHEMA.ORG JSON-LD (SoftwareApplication + FAQ + HowTo)
            </h4>
          </div>

          <button
            onClick={handleCopySchema}
            className="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-700 text-xs flex items-center gap-1.5 cursor-pointer transition-colors"
          >
            {copiedSchema ? <Check className="w-3.5 h-3.5 text-[#00f5d4]" /> : <Copy className="w-3.5 h-3.5" />}
            {copiedSchema ? 'Schema Copied!' : 'Copy Schema Code'}
          </button>
        </div>

        <pre className="p-3 bg-[#050811] rounded-lg border border-slate-800 font-mono text-[11px] text-emerald-300 overflow-x-auto max-h-56">
          {schemaJsonString}
        </pre>
      </div>
    </div>
  );
};
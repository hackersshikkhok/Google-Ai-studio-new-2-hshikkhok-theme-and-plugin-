import React, { useState } from 'react';
import { DeveloperToolSpec } from '../data/platformData';
import { Download, Copy, Check, Eye, Shield, Sparkles, Layers, ArrowRight } from 'lucide-react';

interface ToolVisualBannerProps {
  tool: DeveloperToolSpec;
}

export const ToolVisualBanner: React.FC<ToolVisualBannerProps> = ({ tool }) => {
  const [copiedSvg, setCopiedSvg] = useState(false);

  // Generate dynamic SVG source
  const getSvgContent = (): string => {
    const inputSample = tool.inputFormats || 'Raw Text / Buffer';
    const outputSample = tool.outputFormats || 'Decoded / Clean Text';
    const location = tool.processingLocation || '100% Browser Client Sandbox';
    const rating = '4.9 ★ (1,420 Reviews)';
    const speed = '< 15ms Zero Latency';

    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" width="100%" height="100%" style="background:#050811; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">
  <defs>
    <!-- Background Gradients -->
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#050811" />
      <stop offset="50%" stop-color="#0b1120" />
      <stop offset="100%" stop-color="#050811" />
    </linearGradient>

    <!-- Neon Glow Filter -->
    <filter id="neonGlowRed" x="-20%" y="-20%" width="140%" height="140%">
      <feGaussianBlur stdDeviation="6" result="blur" />
      <feMerge>
        <feMergeNode in="blur" />
        <feMergeNode in="SourceGraphic" />
      </feMerge>
    </filter>

    <filter id="neonGlowCyan" x="-20%" y="-20%" width="140%" height="140%">
      <feGaussianBlur stdDeviation="4" result="blur" />
      <feMerge>
        <feMergeNode in="blur" />
        <feMergeNode in="SourceGraphic" />
      </feMerge>
    </filter>

    <!-- Arrow Marker -->
    <marker id="redArrow" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#ff3366" />
    </marker>
  </defs>

  <!-- Background Base -->
  <rect width="1200" height="630" fill="url(#bgGrad)" />

  <!-- Cyber Grid Lines -->
  <g stroke="#00f5d4" stroke-opacity="0.05" stroke-width="1">
    <line x1="0" y1="105" x2="1200" y2="105" />
    <line x1="0" y1="210" x2="1200" y2="210" />
    <line x1="0" y1="315" x2="1200" y2="315" />
    <line x1="0" y1="420" x2="1200" y2="420" />
    <line x1="0" y1="525" x2="1200" y2="525" />
    <line x1="200" y1="0" x2="200" y2="630" />
    <line x1="400" y1="0" x2="400" y2="630" />
    <line x1="600" y1="0" x2="600" y2="630" />
    <line x1="800" y1="0" x2="800" y2="630" />
    <line x1="1000" y1="0" x2="1000" y2="630" />
  </g>

  <!-- Outer Border Cyber Frame -->
  <rect x="25" y="25" width="1150" height="580" rx="16" fill="none" stroke="#00f5d4" stroke-width="1.5" stroke-opacity="0.3" />
  <circle cx="25" cy="25" r="4" fill="#00f5d4" />
  <circle cx="1175" cy="25" r="4" fill="#00f5d4" />
  <circle cx="25" cy="605" r="4" fill="#00f5d4" />
  <circle cx="1175" cy="605" r="4" fill="#00f5d4" />

  <!-- Top Brand Bar -->
  <g transform="translate(60, 65)">
    <rect x="0" y="0" width="160" height="28" rx="6" fill="#00f5d4" fill-opacity="0.12" stroke="#00f5d4" stroke-width="1" />
    <text x="80" y="18" fill="#00f5d4" font-size="12" font-weight="bold" text-anchor="middle">HackersShikkhok LAB</text>

    <rect x="175" y="0" width="190" height="28" rx="6" fill="#ff3366" fill-opacity="0.12" stroke="#ff3366" stroke-width="1" />
    <text x="270" y="18" fill="#ff3366" font-size="12" font-weight="bold" text-anchor="middle">OFFLINE-FIRST ENCLAVE</text>

    <!-- Watermark / Category -->
    <text x="1080" y="19" fill="#94a3b8" font-size="12" text-anchor="end">CATEGORY: ${tool.category.toUpperCase()} | TOOL #2026-PRO</text>
  </g>

  <!-- Tool Title Header -->
  <g transform="translate(60, 140)">
    <text x="0" y="0" fill="#ffffff" font-size="34" font-weight="900" letter-spacing="-0.5">${tool.nameBn}</text>
    <text x="0" y="32" fill="#00f5d4" font-size="20" font-weight="bold" filter="url(#neonGlowCyan)">${tool.name} · Live Utility Engine</text>
    <text x="0" y="60" fill="#94a3b8" font-size="14">${tool.description.slice(0, 110)}...</text>
  </g>

  <!-- 3-STAGE INTERACTIVE VECTOR PIPELINE -->
  <g transform="translate(60, 260)">
    
    <!-- STAGE 1: INGESTION BOX -->
    <g transform="translate(0, 0)">
      <rect x="0" y="0" width="280" height="180" rx="12" fill="#0b1120" stroke="#334155" stroke-width="1.5" />
      <rect x="12" y="12" width="70" height="22" rx="4" fill="#ff3366" fill-opacity="0.2" stroke="#ff3366" stroke-width="1" />
      <text x="47" y="27" fill="#ff3366" font-size="10" font-weight="bold" text-anchor="middle">STEP 1</text>
      <text x="95" y="27" fill="#94a3b8" font-size="12" font-weight="bold">INPUT PAYLOAD</text>

      <line x1="12" y1="46" x2="268" y2="46" stroke="#1e293b" stroke-width="1" />

      <text x="18" y="75" fill="#f8fafc" font-size="13" font-weight="bold">Format: ${inputSample}</text>
      <text x="18" y="100" fill="#64748b" font-size="11">Max Safe: ${tool.maxFileSize || '10 MB Client'}</text>
      <text x="18" y="122" fill="#64748b" font-size="11">Sanitization: Strict UTF-8</text>
      <text x="18" y="152" fill="#38bdf8" font-size="11">● Memory Ingestion Ready</text>
    </g>

    <!-- BRIGHT RED CALLOUT ARROW 1 (Input to Processor) -->
    <path d="M 290 90 L 395 90" stroke="#ff3366" stroke-width="4" marker-end="url(#redArrow)" filter="url(#neonGlowRed)" />
    <!-- Callout Badge -->
    <rect x="305" y="55" width="80" height="20" rx="4" fill="#ff3366" />
    <text x="345" y="69" fill="#ffffff" font-size="10" font-weight="bold" text-anchor="middle">INSPECT</text>

    <!-- STAGE 2: PROCESSING ENGINE -->
    <g transform="translate(410, 0)">
      <rect x="0" y="0" width="310" height="180" rx="12" fill="#071322" stroke="#00f5d4" stroke-width="2" filter="url(#neonGlowCyan)" />
      <rect x="12" y="12" width="70" height="22" rx="4" fill="#00f5d4" fill-opacity="0.2" stroke="#00f5d4" stroke-width="1" />
      <text x="47" y="27" fill="#00f5d4" font-size="10" font-weight="bold" text-anchor="middle">STEP 2</text>
      <text x="95" y="27" fill="#00f5d4" font-size="12" font-weight="bold">PROCESSING CORE</text>

      <line x1="12" y1="46" x2="298" y2="46" stroke="#00f5d4" stroke-opacity="0.3" stroke-width="1" />

      <text x="18" y="75" fill="#ffffff" font-size="13" font-weight="bold">Engine: ${location}</text>
      <text x="18" y="100" fill="#a7f3d0" font-size="11">Latency: ${speed}</text>
      <text x="18" y="122" fill="#94a3b8" font-size="11">Zero Server Roundtrip Leak</text>
      <text x="18" y="152" fill="#10b981" font-size="11">● WebAssembly / Native ES6</text>
    </g>

    <!-- BRIGHT RED CALLOUT ARROW 2 (Processor to Output) -->
    <path d="M 730 90 L 835 90" stroke="#ff3366" stroke-width="4" marker-end="url(#redArrow)" filter="url(#neonGlowRed)" />
    <!-- Callout Badge -->
    <rect x="745" y="55" width="80" height="20" rx="4" fill="#ff3366" />
    <text x="785" y="69" fill="#ffffff" font-size="10" font-weight="bold" text-anchor="middle">VERIFY</text>

    <!-- STAGE 3: OUTPUT / EXPORT BOX -->
    <g transform="translate(850, 0)">
      <rect x="0" y="0" width="230" height="180" rx="12" fill="#0b1120" stroke="#334155" stroke-width="1.5" />
      <rect x="12" y="12" width="70" height="22" rx="4" fill="#10b981" fill-opacity="0.2" stroke="#10b981" stroke-width="1" />
      <text x="47" y="27" fill="#10b981" font-size="10" font-weight="bold" text-anchor="middle">STEP 3</text>
      <text x="95" y="27" fill="#94a3b8" font-size="12" font-weight="bold">RESULT OUTPUT</text>

      <line x1="12" y1="46" x2="218" y2="46" stroke="#1e293b" stroke-width="1" />

      <text x="18" y="75" fill="#f8fafc" font-size="13" font-weight="bold">Format: ${outputSample}</text>
      <text x="18" y="100" fill="#64748b" font-size="11">Instant Clipboard Copy</text>
      <text x="18" y="122" fill="#64748b" font-size="11">Direct File Download</text>
      <text x="18" y="152" fill="#10b981" font-size="11">✔ Sanitized Result</text>
    </g>
  </g>

  <!-- Bottom Badges / Trust Metadata -->
  <g transform="translate(60, 520)">
    <!-- Trust Badges -->
    <rect x="0" y="0" width="240" height="32" rx="6" fill="#0f172a" stroke="#334155" stroke-width="1" />
    <text x="120" y="20" fill="#cbd5e1" font-size="11" text-anchor="middle">100% PRIVATE CLIENT EXECUTION</text>

    <rect x="255" y="0" width="220" height="32" rx="6" fill="#0f172a" stroke="#334155" stroke-width="1" />
    <text x="365" y="20" fill="#38bdf8" font-size="11" text-anchor="middle">GOOGLE ADSENSE &amp; SEO SAFE</text>

    <rect x="490" y="0" width="210" height="32" rx="6" fill="#0f172a" stroke="#334155" stroke-width="1" />
    <text x="595" y="20" fill="#facc15" font-size="11" text-anchor="middle">${rating}</text>

    <!-- Domain Signature -->
    <text x="1080" y="22" fill="#00f5d4" font-size="13" font-weight="bold" text-anchor="end">HackersShikkhok.com · 2,024 Pro Tools</text>
  </g>
</svg>`;
  };

  const handleDownloadSvg = () => {
    const svgData = getSvgContent();
    const blob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${tool.slug}-architecture-diagram.svg`;
    a.click();
    URL.revokeObjectURL(url);
  };

  const handleCopySvg = async () => {
    try {
      await navigator.clipboard.writeText(getSvgContent());
      setCopiedSvg(true);
      setTimeout(() => setCopiedSvg(false), 2500);
    } catch {
      // Fallback
    }
  };

  return (
    <div className="space-y-4">
      {/* Banner Actions Bar */}
      <div className="flex flex-wrap items-center justify-between gap-3 p-3 rounded-xl bg-[#0b1120] border border-slate-800">
        <div className="flex items-center gap-2">
          <div className="p-1.5 rounded-lg bg-[#ff3366]/10 text-[#ff3366] border border-[#ff3366]/30">
            <Sparkles className="w-4 h-4" />
          </div>
          <div>
            <h4 className="text-xs font-bold text-white">Cyber Defense Hub</h4>
            <p className="text-[11px] text-slate-400">(<code className="text-[#ff3366]">#ff3366</code>), -    /</p>
          </div>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={handleCopySvg}
            className="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-700 text-xs flex items-center gap-1.5 cursor-pointer transition-colors"
          >
            {copiedSvg ? <Check className="w-3.5 h-3.5 text-[#00f5d4]" /> : <Copy className="w-3.5 h-3.5" />}
            {copiedSvg ? 'SVG  Copied' : 'Copy SVG'}</button>
          <button
            onClick={handleDownloadSvg}
            className="px-3 py-1.5 rounded-lg bg-[#ff3366] hover:bg-[#ff3366]/90 text-white font-bold text-xs flex items-center gap-1.5 cursor-pointer transition-colors"
          >
            <Download className="w-3.5 h-3.5" />
            Download SVG Banner
          </button>
        </div>
      </div>

      {/* Live SVG Vector Rendering Canvas */}
      <div className="relative rounded-xl overflow-hidden border border-slate-800 bg-[#050811] shadow-2xl">
        <div
          className="w-full aspect-[1200/630] flex items-center justify-center"
          dangerouslySetInnerHTML={{ __html: getSvgContent() }}
        />
        
        {/* Overlay Badges */}
        <div className="absolute bottom-3 left-3 right-3 flex items-center justify-between pointer-events-none text-[10px] font-mono">
          <span className="px-2 py-1 rounded bg-black/80 backdrop-blur border border-slate-700 text-slate-300">
            1200 × 630 OpenGraph / Twitter Card / Google Image / Schema.org Standard
          </span>
          <span className="px-2 py-1 rounded bg-[#ff3366]/20 border border-[#ff3366]/50 text-[#ff3366] font-bold">
            Red Callout System Active
          </span>
        </div>
      </div>

      {/* Visual Learning Context & SEO Significance */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
        <div className="p-3 rounded-lg bg-[#0b1120] border border-slate-800">
          <div className="font-bold text-[#00f5d4] flex items-center gap-1.5 mb-1">
            <Eye className="w-3.5 h-3.5" />Cyber Defense Hub</div>
          <p className="text-[11px] text-slate-400 leading-relaxed">Cyber Defense Hub<code className="text-[#00f5d4]">schema.org/image</code>,    -%  ।</p>
        </div>

        <div className="p-3 rounded-lg bg-[#0b1120] border border-slate-800">
          <div className="font-bold text-[#ff3366] flex items-center gap-1.5 mb-1">
            <ArrowRight className="w-3.5 h-3.5" />(#ff3366)</div>
          <p className="text-[11px] text-slate-400 leading-relaxed">Tool ,      ।   Dwell Time    Bounce Rate   ।</p>
        </div>

        <div className="p-3 rounded-lg bg-[#0b1120] border border-slate-800">
          <div className="font-bold text-purple-400 flex items-center gap-1.5 mb-1">
            <Shield className="w-3.5 h-3.5" />(CTR 2.8x)</div>
          <p className="text-[11px] text-slate-400 leading-relaxed">, /X   Tool         ,       .    ।</p>
        </div>
      </div>
    </div>
  );
};
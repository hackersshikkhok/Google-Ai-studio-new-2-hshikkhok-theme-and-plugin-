import React, { useState } from 'react';
import {
  ShieldAlert,
  ShieldCheck,
  Terminal,
  Activity,
  Globe,
  FileText,
  Clock,
  Layers,
  Code2,
  Copy,
  Check,
  Download,
  AlertTriangle,
  Play,
  RotateCw,
  Zap,
  Sliders,
  DollarSign,
  Bell,
  Scale,
  Sparkles,
  ExternalLink,
  ChevronRight,
  Send
} from 'lucide-react';

interface SecurityFinding {
  id: number;
  key: string;
  severity: 'critical' | 'high' | 'medium' | 'low' | 'info';
  cvss: number;
  category: string;
  titleEn: string;
  titleBn: string;
  descBn: string;
  impactBn: string;
  remediationBn: string;
  codeSnippetNginx?: string;
  codeSnippetApache?: string;
  codeSnippetPhp?: string;
  codeSnippetCloudflare?: string;
  codeSnippetWp?: string;
  status: 'detected' | 'in_review' | 'remediated';
}

interface ScopeAsset {
  id: string;
  url: string;
  org: string;
  verified: boolean;
  health: number;
  lastAudit: string;
  token: string;
}

const SEED_FINDINGS: SecurityFinding[] = [
  {
    id: 1,
    key: 'missing_hsts',
    severity: 'medium',
    cvss: 6.5,
    category: 'security_headers',
    titleEn: 'Strict-Transport-Security (HSTS) Missing',
    titleBn: 'HTTP Strict Transport Security (HSTS)',
    descBn: 'HTTPS      ।  SSL-   ।',
    impactBn: 'HTTP-      ।',
    remediationBn: 'Nginx, Apache  Cloudflare   Strict-Transport-Security   ।',
    codeSnippetNginx: 'add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;',
    codeSnippetApache: '<IfModule mod_headers.c>\n  Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"\n</IfModule>',
    codeSnippetPhp: 'header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");',
    codeSnippetCloudflare: '// Cloudflare Rules -> Transform Rules -> Modify Response Header:\nSet "Strict-Transport-Security" = "max-age=31536000; includeSubDomains; preload"',
    codeSnippetWp: "add_action('send_headers', function() {\n  header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');\n});",
    status: 'detected'
  },
  {
    id: 2,
    key: 'missing_csp',
    severity: 'high',
    cvss: 7.2,
    category: 'security_headers',
    titleEn: 'Content-Security-Policy (CSP) Missing',
    titleBn: 'Content Security Policy (CSP)   (XSS )',
    descBn: 'CSP             ।',
    impactBn: '- cross-site scripting (XSS)           ।',
    remediationBn: '  script-src  default-src    CSP  ।',
    codeSnippetNginx: 'add_header Content-Security-Policy "default-src \'self\'; script-src \'self\' https://cdn.jsdelivr.net; object-src \'none\';" always;',
    codeSnippetApache: '<IfModule mod_headers.c>\n  Header always set Content-Security-Policy "default-src \'self\'; script-src \'self\' https://cdn.jsdelivr.net; object-src \'none\';"\n</IfModule>',
    codeSnippetPhp: 'header("Content-Security-Policy: default-src \'self\'; script-src \'self\' https://cdn.jsdelivr.net; object-src \'none\';");',
    codeSnippetCloudflare: '// Cloudflare WAF -> Header Rewrite:\nSet "Content-Security-Policy" = "default-src \'self\'; object-src \'none\';"',
    codeSnippetWp: "add_action('send_headers', function() {\n  header(\"Content-Security-Policy: default-src 'self'; object-src 'none';\");\n});",
    status: 'detected'
  },
  {
    id: 3,
    key: 'weak_dmarc_policy',
    severity: 'medium',
    cvss: 6.1,
    category: 'dns_email',
    titleEn: 'Weak DMARC Email Anti-Spoofing Policy',
    titleBn: 'DMARC -    p=none  ',
    descBn: '  DMARC             ।',
    impactBn: '    Execute   ।',
    remediationBn: '  DMARC  p=reject   ।',
    codeSnippetNginx: '# DNS TXT Record for _dmarc.yourdomain.com:\nv=DMARC1; p=reject; sp=reject; pct=100; rua=mailto:dmarc@yourdomain.com;',
    codeSnippetApache: '# Managed via DNS TXT Record:\n_dmarc IN TXT "v=DMARC1; p=reject; sp=reject; pct=100;"',
    codeSnippetPhp: '// Ensure outgoing SMTP envelopes strictly match SPF/DKIM align headers.',
    codeSnippetCloudflare: '// Cloudflare DNS Management:\nAdd TXT Record -> Name: _dmarc -> Value: "v=DMARC1; p=reject; pct=100;"',
    codeSnippetWp: "// Configure WP Mail SMTP plugin to enforce SPF/DKIM authenticated sender identity.",
    status: 'detected'
  },
  {
    id: 4,
    key: 'wp_user_enumeration',
    severity: 'low',
    cvss: 3.8,
    category: 'cve_vulnerability',
    titleEn: 'REST API Author Enumeration Exposed',
    titleBn: 'REST API    ',
    descBn: '/wp-json/wp/v2/users Points       ।',
    impactBn: '        -  ।',
    remediationBn: 'Login    /wp-json/wp/v2/users Points   ।',
    codeSnippetNginx: 'location ~ ^/wp-json/wp/v2/users {\n  if ($http_cookie !~* "wordpress_logged_in") {\n    return 403;\n  }\n}',
    codeSnippetApache: 'RewriteCond %{REQUEST_URI} ^/wp-json/wp/v2/users\nRewriteCond %{HTTP_COOKIE} !wordpress_logged_in\nRewriteRule .* - [F,L]',
    codeSnippetPhp: "add_filter( 'rest_endpoints', function( $endpoints ) {\n  if ( ! is_user_logged_in() && isset( $endpoints['/wp/v2/users'] ) ) {\n    unset( $endpoints['/wp/v2/users'] );\n  }\n  return $endpoints;\n});",
    codeSnippetCloudflare: '// Cloudflare WAF Expression:\n(http.request.uri.path eq "/wp-json/wp/v2/users" and not http.cookie contains "wordpress_logged_in") -> Action: Block',
    codeSnippetWp: "add_filter( 'rest_endpoints', function( $endpoints ) {\n  if ( ! is_user_logged_in() && isset( $endpoints['/wp/v2/users'] ) ) {\n    unset( $endpoints['/wp/v2/users'] );\n  }\n  return $endpoints;\n});",
    status: 'in_review'
  }
];

export const CyberCommandCenterPortal: React.FC = () => {
  const [activeTab, setActiveTab] = useState<string>('overview');
  const [targetUrl, setTargetUrl] = useState<string>('https://hackersshikkhok.com');
  const [isScanning, setIsScanning] = useState<boolean>(false);
  const [scanProgress, setScanProgress] = useState<number>(0);
  const [scanStatus, setScanStatus] = useState<string>('');
  const [healthScore, setHealthScore] = useState<number>(88);
  const [findings, setFindings] = useState<SecurityFinding[]>(SEED_FINDINGS);
  const [filterSev, setFilterSev] = useState<string>('all');
  const [terminalLogs, setTerminalLogs] = useState<string[]>([
    '[INIT] HackersShikkhok Cyber Command Engine v2.0 Active.',
    '[READY] Listening for Target Dispatch on /cyber-command/ gateway.',
    '[DAEMON] Micro-batch probe scheduler synchronized with C2 socket.'
  ]);
  const [cliInput, setCliInput] = useState<string>('');
  const [selectedPatchKey, setSelectedPatchKey] = useState<string>('missing_hsts');
  const [patchServer, setPatchServer] = useState<'nginx' | 'apache' | 'php' | 'cloudflare' | 'wp'>('nginx');
  const [copiedCode, setCopiedCode] = useState<boolean>(false);

  // Targets State
  const [targetList, setTargetList] = useState<ScopeAsset[]>([
    {
      id: 'target-1',
      url: 'https://hackersshikkhok.com',
      org: 'HackersShikkhok Core Portal',
      verified: true,
      health: 88,
      lastAudit: 'Today (Live)',
      token: 'hs-verify-88c91a0f'
    },
    {
      id: 'target-2',
      url: 'https://api.hackersshikkhok.com',
      org: 'HackersShikkhok Microservice Gateway',
      verified: true,
      health: 94,
      lastAudit: 'Yesterday',
      token: 'hs-verify-31d04b8e'
    }
  ]);
  const [newAssetUrl, setNewAssetUrl] = useState('');
  const [newAssetOrg, setNewAssetOrg] = useState('');

  // Red Team Form State
  const [rtCompany, setRtCompany] = useState('');
  const [rtEmail, setRtEmail] = useState('');
  const [rtScope, setRtScope] = useState('');
  const [rtSubmitted, setRtSubmitted] = useState(false);

  // Webhooks State
  const [webhookUrl, setWebhookUrl] = useState('https://hooks.slack.com/services/DEMO/CYBER/ALERT');
  const [webhookPlatform, setWebhookPlatform] = useState<'slack' | 'discord' | 'teams' | 'custom'>('slack');
  const [webhookStatus, setWebhookStatus] = useState<string | null>(null);

  // RoE & Legal Form State
  const [roeOrg, setRoeOrg] = useState('Standard Enterprise Corp Ltd.');
  const [roeSigner, setRoeSigner] = useState('Tanvir Ahmed');
  const [roeTitle, setRoeTitle] = useState('Chief Information Security Officer');
  const [roeSigned, setRoeSigned] = useState(false);

  // Wallet and Plan State
  const [walletBalance, setWalletBalance] = useState<number>(2450.00);
  const [activePlan, setActivePlan] = useState<'free' | 'starter' | 'business' | 'enterprise'>('business');

  const triggerAuditScan = () => {
    setIsScanning(true);
    setScanProgress(10);
    setScanStatus('Initializing TLS Handshake & Cipher Probes...');
    addLog(`[DISPATCH] Target Scope: ${targetUrl} [Profile: Full Passive Audit]`);

    let p = 10;
    const timer = setInterval(() => {
      p += 15;
      if (p <= 90) {
        setScanProgress(p);
        if (p === 25) {
          setScanStatus('Verifying TLSv1.3 Negotiation & Expiry Date...');
          addLog('[TLS] Handshake complete. Cipher strength: 256-bit AES-GCM (Optimal)');
        } else if (p === 50) {
          setScanStatus('Probing HTTP Security Headers (HSTS, CSP, XFO, Permissions-Policy)...');
          addLog('[HEADERS] Scanning 14 HTTP response parameters...');
        } else if (p === 70) {
          setScanStatus('Testing Non-Exposure of Sensitive Files (.env, .git, backups)...');
          addLog('[EXPOSURE] Probing /.env, /.git/HEAD, /wp-config.php.bak -> 404/403 Denied (Safe)');
        } else if (p === 85) {
          setScanStatus('Evaluating DNS Anti-Spoofing & DMARC Enforcement...');
          addLog('[DNS] Querying _dmarc record -> v=DMARC1 detected.');
        }
      } else {
        clearInterval(timer);
        setScanProgress(100);
        setScanStatus('Audit Completed! Health Score Calculated.');
        setIsScanning(false);
        setHealthScore(88);
        addLog('[COMPLETE] Audit successfully finalized in 1,240ms. Health Grade: A- (88/100).');
      }
    }, 280);
  };

  const addLog = (msg: string) => {
    const time = new Date().toTimeString().split(' ')[0];
    setTerminalLogs(prev => [...prev.slice(-35), `[${time}] ${msg}`]);
  };

  const handleCliSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!cliInput.trim()) return;
    const cmd = cliInput.trim();
    addLog(`> ${cmd}`);
    setCliInput('');

    if (cmd === 'help') {
      addLog('Available Commands: scan <url>, check-hsts, status, cve-lookup, list-targets, clear');
    } else if (cmd.startsWith('scan')) {
      const parts = cmd.split(' ');
      if (parts[1]) setTargetUrl(parts[1]);
      triggerAuditScan();
    } else if (cmd === 'check-hsts') {
      addLog('[HSTS-PROBE] Strict-Transport-Security: MISSING. Risk: SSL-Stripping.');
    } else if (cmd === 'status') {
      addLog(`[STATUS] Target: ${targetUrl} | Health: ${healthScore}/100 | Active Plan: ${activePlan.toUpperCase()}`);
    } else if (cmd === 'cve-lookup') {
      addLog('[CVE] Querying NVD Database... Zero Critical unpatched core CVEs detected.');
    } else if (cmd === 'clear') {
      setTerminalLogs(['[CLI] Console cleared. Type "help" for command matrix.']);
    } else {
      addLog(`Unknown command: "${cmd}". Type "help" for available commands.`);
    }
  };

  const handleAddAsset = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newAssetUrl.trim()) return;
    const newTarget: ScopeAsset = {
      id: `target-${Date.now()}`,
      url: newAssetUrl.trim(),
      org: newAssetOrg.trim() || 'Custom Authorized Asset',
      verified: true,
      health: 85,
      lastAudit: 'Just Added',
      token: `hs-verify-${Math.random().toString(36).substring(2, 9)}`
    };
    setTargetList(prev => [...prev, newTarget]);
    setTargetUrl(newTarget.url);
    setNewAssetUrl('');
    setNewAssetOrg('');
    addLog(`[SCOPE] New authorized asset registered: ${newTarget.url}`);
    alert(` "${newTarget.url}" Success    !`);
  };

  const handleTestWebhook = () => {
    addLog(`[WEBHOOK] Dispatching test ping to ${webhookUrl} via ${webhookPlatform.toUpperCase()} adapter...`);
    setWebhookStatus('Dispatching payload...');
    setTimeout(() => {
      setWebhookStatus('Success: HTTP 200 OK — Alert payload acknowledged by remote receiver.');
      addLog('[WEBHOOK] Remote endpoint returned 200 OK. Signature HMAC verified.');
    }, 700);
  };

  const handleSignRoe = (e: React.FormEvent) => {
    e.preventDefault();
    setRoeSigned(true);
    addLog(`[ROE] Legal bilateral authorization signed by ${roeSigner} (${roeOrg}).`);
    alert(`! Rules of Engagement   -  Success  ।  : HS-ROE-${Math.random().toString(36).substring(2, 8).toUpperCase()}`);
  };

  const handleUpgradePlan = (plan: 'starter' | 'business' | 'enterprise', cost: number) => {
    if (walletBalance< cost) {
      alert(` Wallet  Balance  ( BDT ${cost},  BDT ${walletBalance})।   Wallet  ।`);
      return;
    }
    if (confirm(`  BDT ${cost} BDT  ${plan.toUpperCase()} Security    ?`)) {
      setWalletBalance(prev =>prev - cost);
      setActivePlan(plan);
      addLog(`[BILLING] Upgraded to ${plan.toUpperCase()} Plan. BDT ${cost} deducted from wallet.`);
      alert(`!  ${plan.toUpperCase()} Security  Success  ।`);
    }
  };

  const toggleFindingStatus = (id: number) => {
    setFindings(prev => prev.map(f => {
      if (f.id === id) {
        const nextStatus = f.status ==='detected' ? 'in_review' : f.status === 'in_review' ? 'remediated' : 'detected';
        addLog(`[LIFECYCLE] Finding #${f.id} status changed to "${nextStatus.toUpperCase()}".`);
        return { ...f, status: nextStatus };
      }
      return f;
    }));
  };

  const filteredFindings = findings.filter(f => {
    if (filterSev === 'all') return true;
    return f.severity === filterSev;
  });

  const getRemediationSnippet = () => {
    const f = findings.find(x => x.key === selectedPatchKey) || findings[0];
    if (patchServer === 'nginx') return f.codeSnippetNginx || 'add_header Strict-Transport-Security "max-age=31536000;" always;';
    if (patchServer === 'apache') return f.codeSnippetApache || 'Header always set Strict-Transport-Security "max-age=31536000;"';
    if (patchServer === 'php') return f.codeSnippetPhp || 'header("Strict-Transport-Security: max-age=31536000;");';
    if (patchServer === 'cloudflare') return f.codeSnippetCloudflare || '// Cloudflare Header Rewrite Rule';
    return f.codeSnippetWp || "add_action('send_headers', function() { header('Strict-Transport-Security: max-age=31536000;'); });";
  };

  const copyPatch = () => {
    navigator.clipboard.writeText(getRemediationSnippet());
    setCopiedCode(true);
    setTimeout(() => setCopiedCode(false), 2000);
  };

  return (<div className="bg-[#030712] text-slate-100 rounded-2xl border border-[#00f5d4]/30 overflow-hidden shadow-2xl font-mono">
      {/* Tactical HUD Header */}
      <div className="bg-[#070d1a] px-6 py-4 border-b border-[#00f5d4]/20 flex flex-wrap items-center justify-between gap-4">
        <div className="flex items-center gap-3">
          <span className="w-3.5 h-3.5 rounded-full bg-[#00f5d4] animate-ping" />
          <div>
            <h1 className="text-base font-extrabold tracking-wider text-white flex items-center gap-2">
              <ShieldAlert className="w-5 h-5 text-[#00f5d4]" />
              HackersShikkhok // B2B CYBER COMMAND &amp; SECURITY PORTAL
            </h1>
            <p className="text-[11px] text-slate-400">
              14-Module Enterprise Cyber Defense Suite · Vulnerability Lifecycle · Commercial SaaS
            </p>
          </div>
        </div>
        <div className="flex items-center gap-6 text-xs">
          <div>
            <span className="text-slate-500 block text-[10px]">SUBSCRIPTION</span>
            <span className="text-[#00f5d4] font-bold uppercase">● {activePlan} TIER</span>
          </div>
          <div>
            <span className="text-slate-500 block text-[10px]">WALLET BALANCE</span>
            <span className="text-[#10b981] font-bold">BDT {walletBalance.toLocaleString('en-US', { minimumFractionDigits: 2 })} BDT</span>
          </div>
          <div>
            <span className="text-slate-500 block text-[10px]">CURRENT TARGET</span>
            <span className="text-white font-bold">{targetUrl}</span>
          </div>
        </div>
      </div>

      {/* Main Layout Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-4 p-6 gap-6">
        {/* Module Nav Sidebar: All 14 Modules */}
        <div className="space-y-1 bg-[#070d1a]/90 p-3 rounded-xl border border-slate-800 h-fit">
          <div className="text-[10px] font-bold text-slate-500 px-3 py-1.5 border-b border-slate-800 tracking-wider">
            14 ENTERPRISE MODULES
          </div>
          <button
            onClick={() => setActiveTab('overview')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'overview' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <Activity className="w-3.5 h-3.5 text-[#00f5d4]" /> 1. Threat Radar &amp; Overview
          </button>
          <button
            onClick={() => setActiveTab('targets')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'targets' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <Globe className="w-3.5 h-3.5 text-sky-400" /> 2. Assets &amp; Scope Manager
          </button>
          <button
            onClick={() => setActiveTab('scanner')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'scanner' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <Zap className="w-3.5 h-3.5 text-yellow-400" /> 3. Automated Web Auditor
          </button>
          <button
            onClick={() => setActiveTab('findings')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'findings' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <Layers className="w-3.5 h-3.5 text-rose-400" /> 4. Vulnerabilities Ledger
          </button>
          <button
            onClick={() => setActiveTab('remediation')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'remediation' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <Code2 className="w-3.5 h-3.5 text-emerald-400" /> 5. AI Code Patches
          </button>
          <button
            onClick={() => setActiveTab('executive')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'executive' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <DollarSign className="w-3.5 h-3.5 text-amber-400" /> 6. Business Impact Matrix
          </button>
          <button
            onClick={() => setActiveTab('pdf')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'pdf' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <FileText className="w-3.5 h-3.5 text-purple-400" /> 7. Executive PDF Report
          </button>
          <button
            onClick={() => setActiveTab('csm')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'csm' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <Clock className="w-3.5 h-3.5 text-cyan-400" /> 8. Continuous Monitoring
          </button>
          <button
            onClick={() => setActiveTab('redteam')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'redteam' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <ShieldAlert className="w-3.5 h-3.5 text-red-500" /> 9. Red Team Penetration
          </button>
          <button
            onClick={() => setActiveTab('compliance')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'compliance' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <ShieldCheck className="w-3.5 h-3.5 text-teal-400" /> 10. Compliance (OWASP/ISO)
          </button>
          <button
            onClick={() => setActiveTab('webhooks')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'webhooks' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <Bell className="w-3.5 h-3.5 text-indigo-400" /> 11. Alerts &amp; Webhooks
          </button>
          <button
            onClick={() => setActiveTab('billing')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'billing' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <Sliders className="w-3.5 h-3.5 text-lime-400" /> 12. B2B Subscriptions
          </button>
          <button
            onClick={() => setActiveTab('terminal')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'terminal' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <Terminal className="w-3.5 h-3.5 text-pink-400" /> 13. Live Terminal &amp; C2 CLI
          </button>
          <button
            onClick={() => setActiveTab('guidelines')}
            className={`w-full text-left px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 ${
              activeTab === 'guidelines' ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40' : 'text-slate-400 hover:text-white'
            }`}
          >
            <Scale className="w-3.5 h-3.5 text-amber-300" /> 14. Rules of Engagement &amp; RoE
          </button>
        </div>

        {/* Command Stage Content Area */}
        <div className="lg:col-span-3 space-y-6">

          {/* TAB 1: OVERVIEW & THREAT RADAR */}
          {activeTab === 'overview' && (
            <div className="space-y-6">
              {/* Scorecard Header */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="p-5 rounded-xl bg-[#070d1a] border border-[#00f5d4]/30 relative overflow-hidden">
                  <div className="text-[10px] text-slate-400 uppercase tracking-widest font-bold">
                    SECURITY HEALTH INDEX
                  </div>
                  <div className="text-4xl font-black text-[#00f5d4] mt-2 flex items-baseline gap-2">
                    {healthScore}<span className="text-xs text-slate-500 font-normal">/100</span>
                  </div>
                  <div className="text-xs text-[#10b981] font-bold mt-1">GRADE: A- (HARDENED)</div>
                  <p className="text-[11px] text-slate-400 mt-2">।     ।</p>
                </div>

                <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-2">
                  <div className="text-[10px] text-slate-400 uppercase tracking-widest font-bold">
                    ACTIVE VULNERABILITIES
                  </div>
                  <div className="space-y-1 text-xs">
                    <div className="flex justify-between items-center py-0.5">
                      <span className="text-red-400 font-bold">CRITICAL (CVSS 9.0+)</span>
                      <span className="px-2 py-0.5 rounded bg-red-950/60 text-red-300 font-bold">0</span>
                    </div>
                    <div className="flex justify-between items-center py-0.5">
                      <span className="text-orange-400 font-bold">HIGH (CVSS 7.0-8.9)</span>
                      <span className="px-2 py-0.5 rounded bg-orange-950/60 text-orange-300 font-bold">1</span>
                    </div>
                    <div className="flex justify-between items-center py-0.5">
                      <span className="text-yellow-400 font-bold">MEDIUM (CVSS 4.0-6.9)</span>
                      <span className="px-2 py-0.5 rounded bg-yellow-950/60 text-yellow-300 font-bold">2</span>
                    </div>
                    <div className="flex justify-between items-center py-0.5">
                      <span className="text-sky-400 font-bold">LOW / INFORMATIONAL</span>
                      <span className="px-2 py-0.5 rounded bg-sky-950/60 text-sky-300 font-bold">1</span>
                    </div>
                  </div>
                </div>

                <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 flex flex-col justify-between">
                  <div>
                    <div className="text-[10px] text-slate-400 uppercase tracking-widest font-bold">
                      ACTIVE SCOPE ASSET
                    </div>
                    <div className="text-sm font-bold text-white mt-1 truncate">{targetUrl}</div>
                    <div className="text-[11px] text-[#10b981] font-bold mt-1">✓ OWNERSHIP VERIFIED</div>
                  </div>
                  <button
                    onClick={() => { setActiveTab('scanner'); triggerAuditScan(); }}
                    className="w-full mt-4 py-2.5 rounded-lg bg-[#00f5d4] text-[#030712] text-xs font-black hover:bg-[#5efce8] transition flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-[#00f5d4]/20"
                  >
                    <Play className="w-4 h-4 fill-current" /> LAUNCH INSTANT AUDIT
                  </button>
                </div>
              </div>

              {/* Dynamic Vector SVG Threat Surface Diagram with Red Callout Arrows */}
              <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-4">
                <div className="flex flex-wrap items-center justify-between gap-2">
                  <div>
                    <h3 className="text-sm font-bold text-white flex items-center gap-2">
                      <ShieldCheck className="w-4 h-4 text-[#00f5d4]" />
                      Dynamic Vector Threat Surface &amp; Blast Radius Topology
                    </h3>
                    <p className="text-xs text-slate-400">,      -  ।</p>
                  </div>
                  <span className="px-2.5 py-1 rounded bg-[#00f5d4]/10 border border-[#00f5d4]/30 text-[#00f5d4] text-[10px] font-bold">
                    SVG BLAST RADIUS
                  </span>
                </div>

                {/* SVG Visual Learning Diagram */}
                <div className="bg-[#02050c] p-4 rounded-xl border border-slate-800 overflow-x-auto">
                  <svg viewBox="0 0 760 260" className="w-full max-w-full h-auto min-w-[650px] font-mono text-[11px]">
                    <defs>
                      <marker id="arrow-red" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                        <path d="M 0 1 L 10 5 L 0 9 z" fill="#ff3366" />
                      </marker>
                      <marker id="arrow-cyan" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                        <path d="M 0 1 L 10 5 L 0 9 z" fill="#00f5d4" />
                      </marker>
                    </defs>

                    {/* Step 1: Internet Threat Actor */}
                    <rect x="20" y="30" width="160" height="90" rx="10" fill="#0b1329" stroke="#334155" strokeWidth="1.5" />
                    <rect x="30" y="40" width="55" height="18" rx="4" fill="#3b82f6" />
                    <text x="57" y="53" fill="#ffffff" fontWeight="bold" textAnchor="middle" fontSize="9">STEP 1</text>
                    <text x="30" y="75" fill="#f8fafc" fontWeight="bold">External Attack Surface</text>
                    <text x="30" y="93" fill="#94a3b8" fontSize="10">Public DNS &amp; Web Recon</text>
                    <text x="30" y="108" fill="#38bdf8" fontSize="9">Port 80/443 Ingress</text>

                    {/* Arrow 1 -> Step 2 */}
                    <line x1="180" y1="75" x2="260" y2="75" stroke="#ff3366" strokeWidth="2.5" strokeDasharray="5,4" markerEnd="url(#arrow-red)" />
                    <text x="220" y="65" fill="#ff3366" fontWeight="bold" textAnchor="middle" fontSize="9">PROBE</text>

                    {/* Step 2: Edge WAF & Header Filtering */}
                    <rect x="270" y="30" width="180" height="90" rx="10" fill="#0b1329" stroke="#ff3366" strokeWidth="2" />
                    <rect x="280" y="40" width="55" height="18" rx="4" fill="#ff3366" />
                    <text x="307" y="53" fill="#ffffff" fontWeight="bold" textAnchor="middle" fontSize="9">STEP 2</text>
                    <text x="280" y="75" fill="#ffffff" fontWeight="bold">HTTP Headers &amp; TLS</text>
                    <text x="280" y="93" fill="#ff3366" fontSize="10">⚠️ HSTS &amp; CSP Missing</text>
                    <text x="280" y="108" fill="#94a3b8" fontSize="9">TLS 1.3 Active (Pass)</text>

                    {/* Red Callout Warning Label */}
                    <rect x="280" y="135" width="160" height="32" rx="6" fill="#450a0a" stroke="#ff3366" strokeWidth="1" />
                    <text x="360" y="155" fill="#fecaca" fontWeight="bold" textAnchor="middle" fontSize="10">⚠️ VULNERABILITY TARGET</text>

                    {/* Arrow 2 -> Step 3 */}
                    <line x1="450" y1="75" x2="530" y2="75" stroke="#ff3366" strokeWidth="2.5" markerEnd="url(#arrow-red)" />
                    <text x="490" y="65" fill="#ff3366" fontWeight="bold" textAnchor="middle" fontSize="9">BYPASS RISK</text>

                    {/* Step 3: Core Application & WP Database */}
                    <rect x="540" y="30" width="180" height="90" rx="10" fill="#0b1329" stroke="#10b981" strokeWidth="1.5" />
                    <rect x="550" y="40" width="55" height="18" rx="4" fill="#10b981" />
                    <text x="577" y="53" fill="#030712" fontWeight="bold" textAnchor="middle" fontSize="9">STEP 3</text>
                    <text x="550" y="75" fill="#ffffff" fontWeight="bold">Core Backend &amp; DB</text>
                    <text x="550" y="93" fill="#34d399" fontSize="10">✓ .env &amp; .git Protected</text>
                    <text x="550" y="108" fill="#94a3b8" fontSize="9">WP REST API Hardened</text>

                    {/* Hardening AI Patch Arrow (Step 4) */}
                    <line x1="360" y1="120" x2="360" y2="200" stroke="#00f5d4" strokeWidth="2" strokeDasharray="4,4" markerEnd="url(#arrow-cyan)" />
                    <rect x="250" y="200" width="220" height="42" rx="8" fill="#022c22" stroke="#00f5d4" strokeWidth="1.5" />
                    <text x="360" y="218" fill="#00f5d4" fontWeight="bold" textAnchor="middle" fontSize="10">STEP 4: AI REMEDIATION PATCH</text>
                    <text x="360" y="233" fill="#6ee7b7" textAnchor="middle" fontSize="9">Inject Strict-Transport-Security &amp; CSP</text>
                  </svg>
                </div>
              </div>
            </div>
          )}

          {/* TAB 2: ASSETS & SCOPE MANAGER */}
          {activeTab === 'targets' && (
            <div className="space-y-6">
              <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-4">
                <div>
                  <h3 className="text-sm font-bold text-white flex items-center gap-2">
                    <Globe className="w-4 h-4 text-[#00f5d4]" /> Register Corporate Asset to Authorized Scope
                  </h3>
                  <p className="text-xs text-slate-400">(DNS TXT / HTML Meta Tag)।</p>
                </div>

                <form onSubmit={handleAddAsset} className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label className="block text-slate-400 text-xs mb-1">Target Domain / URL</label>
                    <input
                      type="url"
                      required
                      value={newAssetUrl}
                      onChange={(e) => setNewAssetUrl(e.target.value)}
                      placeholder="https://portal.yourcompany.com"
                      className="w-full bg-[#030712] border border-slate-700 px-3 py-2 rounded-lg text-white text-xs"
                    />
                  </div>
                  <div>
                    <label className="block text-slate-400 text-xs mb-1">Organization / Brand</label>
                    <input
                      type="text"
                      required
                      value={newAssetOrg}
                      onChange={(e) => setNewAssetOrg(e.target.value)}
                      placeholder="Standard Bank Ltd."
                      className="w-full bg-[#030712] border border-slate-700 px-3 py-2 rounded-lg text-white text-xs"
                    />
                  </div>
                  <div className="flex items-end">
                    <button
                      type="submit"
                      className="w-full py-2 bg-[#00f5d4] text-[#030712] rounded-lg text-xs font-black hover:bg-[#5efce8] transition cursor-pointer"
                    >
                      + ADD ASSET TO SCOPE
                    </button>
                  </div>
                </form>
              </div>

              {/* Target Inventory */}
              <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-3">
                <div className="text-xs font-bold text-white">Verified Target Scope Inventory</div>
                <div className="overflow-x-auto">
                  <table className="w-full text-left text-xs">
                    <thead>
                      <tr className="border-b border-slate-800 text-slate-400">
                        <th className="pb-2">Target Asset</th>
                        <th className="pb-2">Organization</th>
                        <th className="pb-2">Status</th>
                        <th className="pb-2">Health Index</th>
                        <th className="pb-2">DNS Verification Token</th>
                        <th className="pb-2">Action</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-800/60">
                      {targetList.map((asset) => (
                        <tr key={asset.id} className="text-slate-300">
                          <td className="py-2.5 font-bold text-white">{asset.url}</td>
                          <td className="py-2.5">{asset.org}</td>
                          <td className="py-2.5">
                            <span className="px-2 py-0.5 rounded bg-emerald-950/80 text-emerald-300 font-bold text-[10px]">
                              VERIFIED
                            </span>
                          </td>
                          <td className="py-2.5 font-bold text-[#00f5d4]">{asset.health}/100</td>
                          <td className="py-2.5 font-mono text-[10px] text-slate-400">{asset.token}</td>
                          <td className="py-2.5">
                            <button
                              onClick={() => { setTargetUrl(asset.url); setActiveTab('scanner'); }}
                              className="px-2.5 py-1 rounded bg-slate-800 hover:bg-[#00f5d4] hover:text-[#030712] text-[10px] font-bold transition"
                            >
                              Audit Now
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          )}

          {/* TAB 3: AUTOMATED WEB AUDITOR */}
          {activeTab === 'scanner' && (
            <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-5">
              <div className="flex flex-wrap items-center justify-between gap-4">
                <div>
                  <h3 className="text-sm font-bold text-white flex items-center gap-2">
                    <Zap className="w-4 h-4 text-yellow-400" />
                    Automated Zero-Impact Passive Web Auditor
                  </h3>
                  <p className="text-xs text-slate-400">-   : SSL/TLS , ,      ।</p>
                </div>
                <div className="flex items-center gap-2">
                  <input
                    type="url"
                    value={targetUrl}
                    onChange={(e) => setTargetUrl(e.target.value)}
                    className="bg-[#030712] border border-slate-700 px-3 py-1.5 rounded-lg text-white text-xs w-64"
                  />
                  <button
                    onClick={triggerAuditScan}
                    disabled={isScanning}
                    className="px-4 py-2 rounded-lg bg-[#00f5d4] text-[#030712] text-xs font-black hover:bg-[#5efce8] transition disabled:opacity-50 cursor-pointer flex items-center gap-2"
                  >
                    {isScanning ? <RotateCw className="w-3.5 h-3.5 animate-spin" /> : <Play className="w-3.5 h-3.5 fill-current" />}
                    {isScanning ? 'AUDITING...' : 'DISPATCH AUDIT'}
                  </button>
                </div>
              </div>

              {/* Progress Bar */}
              {isScanning && (
                <div className="space-y-1.5 p-3 rounded-lg bg-[#02050c] border border-[#00f5d4]/20">
                  <div className="flex justify-between text-xs">
                    <span className="text-[#00f5d4]">{scanStatus}</span>
                    <span className="text-white font-bold">{scanProgress}%</span>
                  </div>
                  <div className="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                    <div
                      className="h-full bg-gradient-to-r from-[#00f5d4] to-emerald-400 transition-all duration-300"
                      style={{ width: `${scanProgress}%` }}
                    />
                  </div>
                </div>
              )}

              {/* Terminal Screen for Auditor */}
              <div className="bg-[#020408] border border-[#00f5d4]/20 rounded-xl p-4 text-xs font-mono h-64 overflow-y-auto space-y-1">
                {terminalLogs.map((log, i) => (
                  <div key={i} className="text-slate-300 leading-relaxed">
                    {log}
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* TAB 4: VULNERABILITY FINDINGS LEDGER */}
          {activeTab === 'findings' && (
            <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-4">
              <div className="flex flex-wrap items-center justify-between gap-3">
                <div>
                  <h3 className="text-sm font-bold text-white flex items-center gap-2">
                    <Layers className="w-4 h-4 text-rose-400" /> Vulnerability Lifecycle &amp; Findings Ledger
                  </h3>
                  <p className="text-xs text-slate-400">, CVSS v3.1  ,    ।</p>
                </div>
                {/* Filter Pills */}
                <div className="flex gap-1.5 text-[11px]">
                  {['all', 'critical', 'high', 'medium', 'low'].map(s => (
                    <button
                      key={s}
                      onClick={() => setFilterSev(s)}
                      className={`px-2.5 py-1 rounded uppercase font-bold transition ${
                        filterSev === s ? 'bg-[#00f5d4] text-[#030712]' : 'bg-slate-800 text-slate-400 hover:text-white'
                      }`}
                    >
                      {s}
                    </button>
                  ))}
                </div>
              </div>

              {/* Findings List */}
              <div className="space-y-3">
                {filteredFindings.map((f) => (
                  <div key={f.id} className="p-4 rounded-xl bg-[#030712] border border-slate-800 space-y-2">
                    <div className="flex flex-wrap items-center justify-between gap-2">
                      <div className="flex items-center gap-2">
                        <span className={`px-2 py-0.5 rounded text-[10px] font-bold uppercase ${
                          f.severity === 'high' ? 'bg-orange-950 text-orange-400 border border-orange-800' :
                          f.severity === 'medium' ? 'bg-yellow-950 text-yellow-400 border border-yellow-800' :
                          'bg-sky-950 text-sky-400 border border-sky-800'
                        }`}>
                          {f.severity} · CVSS {f.cvss}
                        </span>
                        <h4 className="text-xs font-bold text-white">{f.titleBn}</h4>
                      </div>
                      <div className="flex items-center gap-2">
                        <span className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                          f.status === 'remediated' ? 'bg-emerald-950 text-emerald-400' :
                          f.status === 'in_review' ? 'bg-amber-950 text-amber-400' : 'bg-red-950 text-red-400'
                        }`}>
                          {f.status.toUpperCase()}
                        </span>
                        <button
                          onClick={() => toggleFindingStatus(f.id)}
                          className="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-[10px] text-slate-300 transition"
                        >
                          Change Status
                        </button>
                      </div>
                    </div>
                    <p className="text-xs text-slate-300">{f.descBn}</p>
                    <div className="text-[11px] text-slate-400 bg-[#070d1a] p-2 rounded border border-slate-800/80">
                      <strong>:</strong> {f.impactBn}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* TAB 5: AI CODE PATCHES */}
          {activeTab === 'remediation' && (
            <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-4">
              <div>
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <Code2 className="w-4 h-4 text-emerald-400" /> AI Code Remediation &amp; Patch Generator
                </h3>
                <p className="text-xs text-slate-400">Nginx, Apache (.htaccess), Cloudflare WAF  PHP  -   ।</p>
              </div>

              <div className="flex flex-wrap gap-2 items-center justify-between">
                <select
                  value={selectedPatchKey}
                  onChange={(e) => setSelectedPatchKey(e.target.value)}
                  className="bg-[#030712] border border-slate-700 px-3 py-1.5 rounded-lg text-white text-xs"
                >
                  {findings.map(f => (
                    <option key={f.key} value={f.key}>{f.titleEn} (CVSS {f.cvss})</option>
                  ))}
                </select>

                <div className="flex gap-1.5">
                  {(['nginx', 'apache', 'php', 'cloudflare', 'wp'] as const).map(srv => (
                    <button
                      key={srv}
                      onClick={() => setPatchServer(srv)}
                      className={`px-2.5 py-1 rounded text-xs uppercase font-bold transition ${
                        patchServer === srv ? 'bg-[#00f5d4] text-[#030712]' : 'bg-slate-800 text-slate-400 hover:text-white'
                      }`}
                    >
                      {srv}
                    </button>
                  ))}
                </div>
              </div>

              {/* Code Snippet Box */}
              <div className="relative bg-[#02050c] p-4 rounded-xl border border-slate-800">
                <pre className="text-xs font-mono text-[#00f5d4] overflow-x-auto whitespace-pre-wrap leading-relaxed">
                  {getRemediationSnippet()}
                </pre>
                <button
                  onClick={copyPatch}
                  className="absolute top-3 right-3 px-3 py-1 rounded bg-slate-800 hover:bg-[#00f5d4] hover:text-[#030712] text-xs font-bold transition flex items-center gap-1.5 text-white"
                >
                  {copiedCode ? <Check className="w-3.5 h-3.5" /> : <Copy className="w-3.5 h-3.5" />}
                  {copiedCode ? 'COPIED!' : 'COPY CODE'}
                </button>
              </div>
            </div>
          )}

          {/* TAB 6: BUSINESS IMPACT MATRIX */}
          {activeTab === 'executive' && (
            <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-6">
              <div>
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <DollarSign className="w-4 h-4 text-amber-400" /> Executive Business Impact &amp; Financial Exposure
                </h3>
                <p className="text-xs text-slate-400">- ,          ।</p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="p-5 rounded-xl bg-[#030712] border border-red-950/60 space-y-2">
                  <div className="text-[10px] text-slate-400 font-bold uppercase">ESTIMATED FINANCIAL BREACH LIABILITY</div>
                  <div className="text-3xl font-black text-[#ff3366]">BDT ,, - BDT ,,</div>
                  <p className="text-xs text-slate-300">Cyber    ।</p>
                </div>

                <div className="p-5 rounded-xl bg-[#030712] border border-amber-950/60 space-y-2">
                  <div className="text-[10px] text-slate-400 font-bold uppercase">BRAND &amp; CUSTOMER TRUST RISK</div>
                  <div className="text-3xl font-black text-amber-400">HIGH VULNERABILITY</div>
                  <p className="text-xs text-slate-300">-        ।</p>
                </div>
              </div>
            </div>
          )}

          {/* TAB 7: EXECUTIVE PDF REPORT */}
          {activeTab === 'pdf' && (
            <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 text-center space-y-4 py-12">
              <div className="w-16 h-16 rounded-2xl bg-[#00f5d4]/10 border border-[#00f5d4] flex items-center justify-center mx-auto text-[#00f5d4]">
                <FileText className="w-8 h-8" />
              </div>
              <h3 className="text-lg font-bold text-white">Executive Cyber Audit PDF Report Ready</h3>
              <p className="text-xs text-slate-400 max-w-md mx-auto">HackersShikkhok  ,  ,       - ।</p>
              <button
                onClick={() => window.print()}
                className="px-6 py-2.5 rounded-lg bg-[#00f5d4] text-[#030712] font-black text-xs hover:bg-[#5efce8] transition flex items-center gap-2 mx-auto cursor-pointer"
              >
                <Download className="w-4 h-4" /> PRINT / EXPORT PDF REPORT
              </button>
            </div>
          )}

          {/* TAB 8: CONTINUOUS SECURITY MONITORING */}
          {activeTab === 'csm' && (
            <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-4">
              <div>
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <Clock className="w-4 h-4 text-cyan-400" /> Continuous Security Monitoring (CSM)
                </h3>
                <p className="text-xs text-slate-400">Audit:  CVE      ।</p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-slate-400 text-xs mb-1">Scan Frequency</label>
                  <select className="w-full bg-[#030712] border border-slate-700 px-3 py-2 rounded-lg text-white text-xs">
                    <option value="daily">Daily Continuous Surface Probe</option>
                    <option value="weekly">Weekly Full Security Audit (Recommended)</option>
                    <option value="monthly">Monthly Compliance Health Audit</option>
                  </select>
                </div>
                <div>
                  <label className="block text-slate-400 text-xs mb-1">Alert Notification Email</label>
                  <input
                    type="email"
                    defaultValue="security@yourcompany.com"
                    className="w-full bg-[#030712] border border-slate-700 px-3 py-2 rounded-lg text-white text-xs"
                  />
                </div>
              </div>
              <button
                onClick={() =>alert('   Success   ।')}
                className="px-4 py-2 rounded-lg bg-[#00f5d4] text-[#030712] font-bold text-xs hover:bg-[#5efce8] transition cursor-pointer"
              >
                Save Monitoring Schedule</button>
            </div>
          )}

          {/* TAB 9: RED TEAM PENETRATION */}
          {activeTab === 'redteam' && (
            <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-4">
              <div>
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <ShieldAlert className="w-4 h-4 text-red-500" /> Custom Red Team Penetration Testing Scoping
                </h3>
                <p className="text-xs text-slate-400">,    Audit  Execute  ।</p>
              </div>

              {rtSubmitted ? (
                <div className="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/40 text-emerald-300 text-xs">✓     Success  !  Security      ।</div>
              ) : (
                <form
                  onSubmit={(e) => { e.preventDefault(); setRtSubmitted(true); addLog(`[REDTEAM] Engagement scope submitted for ${rtCompany}`); }}
                  className="space-y-3 text-xs"
                >
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-slate-400 mb-1">Organization / Brand Name</label>
                      <input
                        type="text"
                        required
                        value={rtCompany}
                        onChange={(e) => setRtCompany(e.target.value)}
                        placeholder="Apex FinTech Corp"
                        className="w-full bg-[#030712] border border-slate-700 px-3 py-2 rounded-lg text-white"
                      />
                    </div>
                    <div>
                      <label className="block text-slate-400 mb-1">CISO / Authorized Email</label>
                      <input
                        type="email"
                        required
                        value={rtEmail}
                        onChange={(e) => setRtEmail(e.target.value)}
                        placeholder="ciso@apex.com"
                        className="w-full bg-[#030712] border border-slate-700 px-3 py-2 rounded-lg text-white"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-slate-400 mb-1">Authorized Target Scope (Domains, APIs, APK)</label>
                    <textarea
                      required
                      rows={3}
                      value={rtScope}
                      onChange={(e) => setRtScope(e.target.value)}
                      placeholder="https://api.apex.com, Mobile APK package com.apex.app, Core payment flow"
                      className="w-full bg-[#030712] border border-slate-700 px-3 py-2 rounded-lg text-white"
                    />
                  </div>
                  <button
                    type="submit"
                    className="px-5 py-2 rounded-lg bg-[#00f5d4] text-[#030712] font-black cursor-pointer hover:bg-[#5efce8] transition"
                  >
                    Submit Official Red Team Scope
                  </button>
                </form>
              )}
            </div>
          )}

          {/* TAB 10: COMPLIANCE MATRIX */}
          {activeTab === 'compliance' && (
            <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-4">
              <div>
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <ShieldCheck className="w-4 h-4 text-teal-400" /> Global Security Compliance Matrix
                </h3>
                <p className="text-xs text-slate-400">,    English Cyber       ।</p>
              </div>

              <div className="space-y-3 text-xs">
                <div className="p-4 rounded-xl bg-[#030712] border border-slate-800 space-y-2">
                  <div className="flex justify-between items-center font-bold">
                    <span className="text-white">OWASP Top 10 (Web Application Security)</span>
                    <span className="text-[#00f5d4]">85% PASSED</span>
                  </div>
                  <div className="text-slate-400 space-y-1">
                    <div>✓ A01: Broken Access Control (Protected)</div>
                    <div>✓ A02: Cryptographic Failures (TLSv1.3 Active)</div>
                    <div className="text-amber-400">⚠️ A05: Security Misconfiguration (HSTS/CSP Missing)</div>
                  </div>
                </div>

                <div className="p-4 rounded-xl bg-[#030712] border border-slate-800 space-y-2">
                  <div className="flex justify-between items-center font-bold">
                    <span className="text-white">Bangladesh Cyber Security Act 2023 Guidelines</span>
                    <span className="text-[#10b981]">90% COMPLIANT</span>
                  </div>
                  <div className="text-slate-400 space-y-1">
                    <div>✓ Section 17: Critical Information Infrastructure Defense</div>
                    <div>✓ Section 19: Unauthorized Access Prevention</div>
                    <div className="text-amber-400">⚠️ Section 23: Email Anti-Spoofing &amp; DMARC Policy Active</div>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* TAB 11: ALERTS & WEBHOOKS */}
          {activeTab === 'webhooks' && (
            <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-4">
              <div>
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <Bell className="w-4 h-4 text-indigo-400" /> Security Webhooks &amp; Instant Incident Dispatcher
                </h3>
                <p className="text-xs text-slate-400">(Slack),    /   ।</p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-slate-400 text-xs mb-1">Webhook Platform</label>
                  <select
                    value={webhookPlatform}
                    onChange={(e: any) => setWebhookPlatform(e.target.value)}
                    className="w-full bg-[#030712] border border-slate-700 px-3 py-2 rounded-lg text-white text-xs"
                  >
                    <option value="slack">Slack Incoming Webhook</option>
                    <option value="discord">Discord Channel Webhook</option>
                    <option value="teams">Microsoft Teams Webhook</option>
                    <option value="custom">Custom SIEM / JSON HTTP Post</option>
                  </select>
                </div>
                <div className="md:col-span-2">
                  <label className="block text-slate-400 text-xs mb-1">Target Webhook URL</label>
                  <input
                    type="url"
                    value={webhookUrl}
                    onChange={(e) => setWebhookUrl(e.target.value)}
                    placeholder="https://hooks.slack.com/services/..."
                    className="w-full bg-[#030712] border border-slate-700 px-3 py-2 rounded-lg text-white text-xs"
                  />
                </div>
              </div>

              <div className="flex items-center gap-4">
                <button
                  onClick={handleTestWebhook}
                  className="px-4 py-2 bg-[#00f5d4] text-[#030712] font-black text-xs rounded-lg hover:bg-[#5efce8] transition cursor-pointer"
                >
                  DISPATCH TEST PAYLOAD
                </button>
                {webhookStatus && <span className="text-xs text-[#00f5d4] font-mono">{webhookStatus}</span>}
              </div>
            </div>
          )}

          {/* TAB 12: B2B SUBSCRIPTIONS */}
          {activeTab === 'billing' && (
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 text-center space-y-3">
                <div className="text-xs text-slate-400 font-bold">STARTER</div>
                <div className="text-2xl font-black text-white">BDT ,<span className="text-xs text-slate-500">/</span></div>
                <ul className="text-xs text-slate-400 space-y-1.5 text-left">
                  <li>•</li>
                  <li>•  -Audit</li>
                  <li>• SSL &amp;</li>
                  <li>•  Report Download</li>
                </ul>
                <button
                  onClick={() => handleUpgradePlan('starter', 1500)}
                  className={`w-full py-2 rounded-lg text-xs font-bold transition ${
                    activePlan === 'starter' ? 'bg-[#00f5d4] text-[#030712]' : 'border border-[#00f5d4] text-[#00f5d4] hover:bg-[#00f5d4] hover:text-[#030712]'
                  }`}
                >
                  {activePlan === 'starter' ? 'CURRENT PLAN' : 'Select Starter'}
                </button>
              </div>

              <div className="p-5 rounded-xl bg-[#070d1a] border border-[#00f5d4] text-center space-y-3 relative shadow-lg shadow-[#00f5d4]/10">
                <span className="px-2 py-0.5 rounded bg-[#00f5d4] text-[#030712] text-[10px] font-bold uppercase">POPULAR</span>
                <div className="text-xs text-[#00f5d4] font-bold">BUSINESS</div>
                <div className="text-2xl font-black text-white">BDT ,<span className="text-xs text-slate-500">/</span></div>
                <ul className="text-xs text-slate-400 space-y-1.5 text-left">
                  <li>•</li>
                  <li>•</li>
                  <li>• CVE</li>
                  <li>• AI</li>
                  <li>•     Audit</li>
                </ul>
                <button
                  onClick={() => handleUpgradePlan('business', 4500)}
                  className={`w-full py-2 rounded-lg text-xs font-black transition ${
                    activePlan === 'business' ? 'bg-[#00f5d4] text-[#030712]' : 'bg-[#00f5d4] text-[#030712] hover:bg-[#5efce8]'
                  }`}
                >
                  {activePlan === 'business' ? 'CURRENT ACTIVE PLAN' : 'Select Business'}
                </button>
              </div>

              <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 text-center space-y-3">
                <div className="text-xs text-slate-400 font-bold">ENTERPRISE</div>
                <div className="text-2xl font-black text-white">BDT ,<span className="text-xs text-slate-500">/</span></div>
                <ul className="text-xs text-slate-400 space-y-1.5 text-left">
                  <li>•</li>
                  <li>• /</li>
                  <li>•</li>
                  <li>• SLA</li>
                  <li>•  CISO</li>
                </ul>
                <button
                  onClick={() => handleUpgradePlan('enterprise', 15000)}
                  className={`w-full py-2 rounded-lg text-xs font-bold transition ${
                    activePlan === 'enterprise' ? 'bg-[#00f5d4] text-[#030712]' : 'border border-[#00f5d4] text-[#00f5d4] hover:bg-[#00f5d4] hover:text-[#030712]'
                  }`}
                >
                  {activePlan === 'enterprise' ? 'CURRENT PLAN' : 'Select Enterprise'}
                </button>
              </div>
            </div>
          )}

          {/* TAB 13: TERMINAL & C2 CLI */}
          {activeTab === 'terminal' && (
            <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-3">
              <div className="flex justify-between items-center text-xs">
                <span className="text-white font-bold flex items-center gap-2">
                  <Terminal className="w-4 h-4 text-pink-400" />
                  Live C2 Security Daemon Logs &amp; Interactive CLI
                </span>
                <span className="text-[#00f5d4] animate-pulse">STREAMING LIVE</span>
              </div>
              <div className="bg-[#020408] border border-[#00f5d4]/20 rounded-xl p-4 text-xs font-mono h-80 overflow-y-auto space-y-1">
                {terminalLogs.map((log, i) => (
                  <div key={i} className="text-slate-300">
                    {log}
                  </div>
                ))}
              </div>
              <form onSubmit={handleCliSubmit} className="flex gap-2">
                <span className="text-[#00f5d4] font-bold text-sm py-1.5">&gt;</span>
                <input
                  type="text"
                  value={cliInput}
                  onChange={(e) => setCliInput(e.target.value)}
                  placeholder="Type 'help', 'scan <url>', 'check-hsts', 'status'..."
                  className="flex-1 bg-[#020408] border border-slate-800 px-3 py-1.5 rounded-lg text-white text-xs font-mono"
                />
                <button
                  type="submit"
                  className="px-4 py-1.5 rounded-lg bg-[#00f5d4] text-[#030712] font-bold text-xs hover:bg-[#5efce8]"
                >
                  EXECUTE
                </button>
              </form>
            </div>
          )}

          {/* TAB 14: RULES OF ENGAGEMENT & LEGAL CONTRACT */}
          {activeTab === 'guidelines' && (
            <div className="p-5 rounded-xl bg-[#070d1a] border border-slate-800 space-y-5">
              <div>
                <h3 className="text-sm font-bold text-white flex items-center gap-2">
                  <Scale className="w-4 h-4 text-amber-300" /> Rules of Engagement (RoE) &amp; Legal Authorization Charter
                </h3>
                <p className="text-xs text-slate-400">,     .% -  ।</p>
              </div>

              <div className="p-4 rounded-xl bg-[#030712] border border-slate-800 space-y-2 text-xs text-slate-300">
                <h4 className="font-bold text-white">100% Responsible &amp; Lawful Security Testing Standard</h4>
                <p className="leading-relaxed">HackersShikkhok        । 
                    -   (DDoS)     ।<strong>English Cyber</strong>Cyber Defense Hub<strong>OWASP WSTG v4.2</strong>।</p>
              </div>

              {roeSigned ? (
                <div className="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/40 text-emerald-300 text-xs space-y-2">
                  <div className="font-bold">✓  RoE  -  (M-NDA)  !</div>
                  <div>: {roeSigner} ({roeTitle}) · : {roeOrg}</div>
                  <div className="text-[11px] text-slate-400 font-mono">Certificate : HS-ROE-VERIFIED-2026</div>
                </div>
              ) : (
                <form onSubmit={handleSignRoe} className="space-y-3 text-xs">
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label className="block text-slate-400 mb-1">Corporate Organization Name</label>
                      <input
                        type="text"
                        required
                        value={roeOrg}
                        onChange={(e) => setRoeOrg(e.target.value)}
                        className="w-full bg-[#030712] border border-slate-700 px-3 py-2 rounded-lg text-white"
                      />
                    </div>
                    <div>
                      <label className="block text-slate-400 mb-1">Authorized Signer Name</label>
                      <input
                        type="text"
                        required
                        value={roeSigner}
                        onChange={(e) => setRoeSigner(e.target.value)}
                        className="w-full bg-[#030712] border border-slate-700 px-3 py-2 rounded-lg text-white"
                      />
                    </div>
                    <div>
                      <label className="block text-slate-400 mb-1">Signer Official Title</label>
                      <input
                        type="text"
                        required
                        value={roeTitle}
                        onChange={(e) => setRoeTitle(e.target.value)}
                        className="w-full bg-[#030712] border border-slate-700 px-3 py-2 rounded-lg text-white"
                      />
                    </div>
                  </div>
                  <button
                    type="submit"
                    className="px-5 py-2.5 rounded-lg bg-[#00f5d4] text-[#030712] font-black cursor-pointer hover:bg-[#5efce8] transition"
                  >
                    EXECUTE LEGAL ROE &amp; DOWNLOAD CERTIFICATE
                  </button>
                </form>
              )}
            </div>
          )}

        </div>
      </div>
    </div>
  );
};
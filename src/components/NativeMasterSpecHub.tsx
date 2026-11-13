import React, { useState } from 'react';
import {
  FileEdit,
  Globe,
  BarChart3,
  Mail,
  ShieldCheck,
  LayoutGrid,
  CheckCircle2,
  Copy,
  Check,
  Send,
  Eye,
  Lock,
  KeyRound,
  Sparkles,
  BookOpen,
  Terminal,
  AlertTriangle,
  Search,
  Code2,
  Wrench,
  Play
} from 'lucide-react';

interface EmailTemplateSpec {
  id: string;
  nameBn: string;
  subject: string;
  recipientRole: string;
  triggerHook: string;
  ctaLabel: string;
  ctaUrl: string;
  bodyHtml: string;
}

const BRANDED_EMAIL_TEMPLATES: EmailTemplateSpec[] = [
  {
    id: 'welcome',
    nameBn: '.   (Welcome Email)',
    subject: 'HackersShikkhok (HackersShikkhok.com)-!',
    recipientRole: 'New Member',
    triggerHook: 'user_register',
    ctaLabel: 'Dashboard',
    ctaUrl: 'https://hackersshikkhok.com/dashboard/',
    bodyHtml:
      '! <strong>HackersShikkhok (HackersShikkhok.com)</strong>Cyber Security, , +  Tool    Lab  ।  Profile, Wallet Points     ।'
  },
  {
    id: 'verify_email',
    nameBn: '.   (Email Verification)',
    subject: '[HackersShikkhok]     ',
    recipientRole: 'Unverified User',
    triggerHook: 'hs_core_send_email_verification',
    ctaLabel: '  ',
    ctaUrl: 'https://hackersshikkhok.com/auth/verify-email/?token=hs_live_token_99481',
    bodyHtml:
      ' HackersShikkhok    (, ,    Wallet)          ।       ।'
  },
  {
    id: 'password_reset',
    nameBn: '.   (Forgot / Reset Password)',
    subject: '[HackersShikkhok]   ',
    recipientRole: 'Account Owner',
    triggerHook: 'retrieve_password_message',
    ctaLabel: '   ',
    ctaUrl: 'https://hackersshikkhok.com/auth/reset-password/?key=hs_rst_8821',
    bodyHtml:
      '        ।      ,          ।'
  },
  {
    id: 'security_alert',
    nameBn: '. Security    Login  (Security Alert)',
    subject: '⚠️ [Security Alert]      Login  ',
    recipientRole: 'Account Owner',
    triggerHook: 'wp_login',
    ctaLabel: '   ',
    ctaUrl: 'https://hackersshikkhok.com/dashboard/security/',
    bodyHtml:
      ', English (Chrome 132 / Windows 11)   HackersShikkhok  Login  ।          Passkey / 2FA  ।'
  },
  {
    id: 'new_follower',
    nameBn: '.    (New Follower)',
    subject: '🎉 Cyber  @ तान  HackersShikkhok-  !',
    recipientRole: 'Creator / Member',
    triggerHook: 'hs_core_user_followed',
    ctaLabel: 'Profile View',
    ctaUrl: 'https://hackersshikkhok.com/members/tanvir/',
    bodyHtml:
      '!      Cyber    ।        ।'
  },
  {
    id: 'comment_reply',
    nameBn: '.     (Comment / Reply)',
    subject: '💬        ',
    recipientRole: 'Post Author / Commenter',
    triggerHook: 'comment_post',
    ctaLabel: '  ',
    ctaUrl: 'https://hackersshikkhok.com/tutorials/owasp-xss-defense/#comment-402',
    bodyHtml:
      '<strong>"OWASP Top 10 & WordPress Nonce Security"</strong>।  -   ।'
  },
  {
    id: 'private_message',
    nameBn: '.    (Direct Message)',
    subject: '✉️       ',
    recipientRole: 'Member',
    triggerHook: 'hs_core_private_message_sent',
    ctaLabel: '  ',
    ctaUrl: 'https://hackersshikkhok.com/messages/',
    bodyHtml:
      ' HackersShikkhok        ।          ।'
  },
  {
    id: 'course_certificate',
    nameBn: '. Course   Certificate (Course & Certificate)',
    subject: '🎓 !  Cyber Defense Course Certificate ',
    recipientRole: 'Student',
    triggerHook: 'hs_core_course_completed',
    ctaLabel: 'Certificate Download ',
    ctaUrl: 'https://hackersshikkhok.com/academy/certificates/HS-2026-9941/',
    bodyHtml:
      ' Success<strong>"Ethical Hacking & Defensive Security Lab"</strong>Course  Module    ।   Certificate :<code>HS-2026-9941</code>।'
  },
  {
    id: 'wallet_receipt',
    nameBn: '. Wallet, Points    (Wallet / Order Receipt)',
    subject: '💳 [Receipt]  Wallet      ',
    recipientRole: 'Customer / Creator',
    triggerHook: 'hs_core_wallet_ledger_entry',
    ctaLabel: 'Wallet  View',
    ctaUrl: 'https://hackersshikkhok.com/dashboard/wallet/',
    bodyHtml:
      ' HackersShikkhok Wallet<strong>+250 XP Points</strong>(Ref:<code>#TXN-88412</code>) Success   ।'
  },
  {
    id: 'admin_autopilot',
    nameBn: '.      (Admin & Autopilot Alert)',
    subject: '🛡️ [Admin Digest] AI Autopilot Quality Gate & Security Health Report',
    recipientRole: 'Administrator',
    triggerHook: 'hs_core_autopilot_admin_digest',
    ctaLabel: ' Center ',
    ctaUrl: 'https://hackersshikkhok.com/wp-admin/admin.php?page=hs-control-center',
    bodyHtml:
      '   Report:     (Score ≥ 88/100)  ,     ।  Security   ।'
  }
];

interface AdSlotConfig {
  id: string;
  nameBn: string;
  location: string;
  reservedHeightPx: number;
  deviceTarget: 'both' | 'desktop' | 'mobile';
  enabled: boolean;
}

export const NativeMasterSpecHub: React.FC = () => {
  const [activeTab, setActiveTab] = useState<
    'classic_seo_sitemap' | 'google_adsense_ga4' | 'branded_email' | 'auth_cards_reader'
  >('classic_seo_sitemap');

  // Tab 1: Native Classic Editor + SEO + Schema + Sitemap state
  const [postTitle, setPostTitle] = useState(
    'WordPress Nonce, Capability Check  Prepared SQL   Security '
  );
  const [postSlug, setPostSlug] = useState('wordpress-nonce-capability-prepared-sql-security');
  const [classicMode, setClassicMode] = useState<'visual' | 'text'>('visual');
  const [postContent, setPostContent] = useState(
    `<h2>.    Security ?</h2>\n<p>Cyber Defense Hub<code>wp_verify_nonce()</code>, <code>current_user_can()</code>Cyber Defense Hub<code>$wpdb->prepare()</code>।</p>\n<h3>?</h3>\n<pre><code>if ( ! current_user_can( 'manage_options' ) ) {\n    wp_die( 'Unauthorized' );\n}</code></pre>`
  );
  const [seoTitle, setSeoTitle] = useState(
    'WordPress Nonce  Prepared SQL  — HackersShikkhok'
  );
  const [seoDesc, setSeoDesc] = useState(
    '   PHP 8.2+       XSS, CSRF  SQL Injection     ।'
  );
  const [canonicalUrl, setCanonicalUrl] = useState(
    'https://hackersshikkhok.com/tutorials/wordpress-nonce-capability-prepared-sql-security/'
  );
  const [robotsDirective, setRobotsDirective] = useState<'index, follow' | 'noindex, nofollow'>(
    'index, follow'
  );
  const [schemaType, setSchemaType] = useState<
    'TechArticle' | 'SoftwareApplication' | 'Course' | 'FAQPage' | 'HowTo'
  >('TechArticle');
  const [relatedToolSlug, setRelatedToolSlug] = useState('wp-security-headers-generator');
  const [relatedCodeSlug, setRelatedCodeSlug] = useState('wp-rest-permission-callback-snippet');
  const [relatedYoutubeId, setRelatedYoutubeId] = useState('dQw4w9WgXcQ');
  const [metaboxSavedToast, setMetaboxSavedToast] = useState(false);

  // Tab 2: Google Search Console, GA4 & AdSense state
  const [gscToken, setGscToken] = useState('hs_google_site_verification_9948271625_live');
  const [ga4Id, setGa4Id] = useState('G-HS2040CYBR');
  const [excludeAdminGa4, setExcludeAdminGa4] = useState(true);
  const [adsMasterEnabled, setAdsMasterEnabled] = useState(true);
  const [adsensePubId, setAdsensePubId] = useState('ca-pub-8849201948271625');
  const [adsTxtContent, setAdsTxtContent] = useState(
    'google.com, pub-8849201948271625, DIRECT, f08c47fec0942fa0'
  );
  const [adSlots, setAdSlots] = useState<AdSlotConfig[]>([
    {
      id: 'header_leaderboard',
      nameBn: '   (Header Banner)',
      location: 'Below Top Nav (Public Pages Only)',
      reservedHeightPx: 90,
      deviceTarget: 'both',
      enabled: true
    },
    {
      id: 'before_article',
      nameBn: '  (Before Content)',
      location: 'Before Single Post Body',
      reservedHeightPx: 250,
      deviceTarget: 'both',
      enabled: true
    },
    {
      id: 'in_article_p3',
      nameBn: '  (After Paragraph 3)',
      location: 'Auto-injected after 3rd</p> (Never inside <pre>/<code>)',
      reservedHeightPx: 250,
      deviceTarget: 'both',
      enabled: true
    },
    {
      id: 'after_code_demo',
      nameBn: '     (24px Safe Margin)',
      location: 'Below Sandbox / Code Editor (Copy-button safe)',
      reservedHeightPx: 250,
      deviceTarget: 'desktop',
      enabled: true
    },
    {
      id: 'sidebar_sticky',
      nameBn: '    (Sidebar Slot)',
      location: 'Right Sidebar Contextual Slot',
      reservedHeightPx: 280,
      deviceTarget: 'desktop',
      enabled: true
    },
    {
      id: 'footer_banner',
      nameBn: '   (Above Footer)',
      location: 'Above Global Footer',
      reservedHeightPx: 90,
      deviceTarget: 'both',
      enabled: false
    }
  ]);

  // Tab 3: Branded Email state
  const [selectedEmailId, setSelectedEmailId] = useState<string>('welcome');
  const [testEmailRecipient, setTestEmailRecipient] = useState('hshikkhok@gmail.com');
  const [emailSentLog, setEmailSentLog] = useState<string[]>([]);

  // Tab 4: Branded Auth Portal + 5 Card Variants + Single Reader state
  const [authView, setAuthView] = useState<
    'login' | 'register' | 'forgot' | 'verify' | 'passkey'
  >('login');
  const [authFeedback, setAuthFeedback] = useState<string | null>(null);
  const [copiedTerminal, setCopiedTerminal] = useState(false);
  const [readerLiked, setReaderLiked] = useState(false);
  const [readerSaved, setReaderSaved] = useState(false);

  const selectedEmail =
    BRANDED_EMAIL_TEMPLATES.find((t) => t.id === selectedEmailId) || BRANDED_EMAIL_TEMPLATES[0];

  const toggleAdSlot = (id: string) => {
    setAdSlots((prev) =>
      prev.map((slot) => (slot.id === id ? { ...slot, enabled: !slot.enabled } : slot))
    );
  };

  const handleDispatchTestEmail = () => {
    const timestamp = new Date().toLocaleTimeString('bn-BD');
    setEmailSentLog((prev) => [
      `[${timestamp}] Sent "${selectedEmail.subject}" from admin@hackersshikkhok.com to ${testEmailRecipient} (Status: 200 OK · HTML Branded)`,
      ...prev.slice(0, 6)
    ]);
  };

  return (
    <section
      id="native-master-spec-hub"
      className="mx-auto max-w-[1440px] px-4 py-8 sm:px-6 border-t border-slate-800/80"
    >
      {/* Section Header */}
      <div className="mb-6 rounded-2xl border border-[#00f5d4]/40 bg-gradient-to-r from-[#070d1f] via-[#0b132b] to-[#130d26] p-5 sm:p-6 shadow-xl">
        <div className="flex flex-wrap items-center justify-between gap-4">
          <div>
            <div className="inline-flex items-center gap-2 rounded-full border border-[#00f5d4]/40 bg-[#00f5d4]/10 px-3 py-1 text-xs font-bold text-[#00f5d4]">
              <Sparkles className="h-3.5 w-3.5" />
              FINAL MASTER SPECIFICATION · ZERO THIRD-PARTY PLUGIN DEPENDENCY
            </div>
            <h2 className="mt-2 text-2xl font-extrabold text-white sm:text-3xl">🛡️   Engine,  UI/UX, SEO, AdSense, GA4</h2>
            <p className="mt-1 text-sm text-slate-300">Rank Math, Yoast, Site Kit  Classic Editor   % {' '}<code className="text-[#00f5d4] font-mono">hackersshikkhok-core</code>{' '}<code className="text-[#a855f7] font-mono">hackersshikkhok-theme</code>-  -
               ।</p>
          </div>

          <div className="flex flex-wrap items-center gap-2 text-xs">
            <span className="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-1.5 font-mono font-bold text-emerald-300">
              Sender: admin@hackersshikkhok.com
            </span>
            <span className="rounded-lg border border-cyan-500/40 bg-cyan-500/10 px-3 py-1.5 font-mono font-bold text-cyan-300">
              Single-Emit Schema Lock: ACTIVE
            </span>
          </div>
        </div>

        {/* Navigation Tabs */}
        <div className="mt-5 flex flex-wrap gap-2 border-t border-slate-800/90 pt-4">
          <button
            onClick={() => setActiveTab('classic_seo_sitemap')}
            className={`inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition ${
              activeTab === 'classic_seo_sitemap'
                ? 'bg-[#00f5d4] text-slate-950 shadow-lg shadow-[#00f5d4]/20'
                : 'border border-slate-700 bg-slate-900/90 text-slate-300 hover:border-[#00f5d4]/40'
            }`}
          >
            <FileEdit className="h-4 w-4" />.   , SEO, Schema  Sitemap</button>

          <button
            onClick={() => setActiveTab('google_adsense_ga4')}
            className={`inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition ${
              activeTab === 'google_adsense_ga4'
                ? 'bg-[#00f5d4] text-slate-950 shadow-lg shadow-[#00f5d4]/20'
                : 'border border-slate-700 bg-slate-900/90 text-slate-300 hover:border-[#00f5d4]/40'
            }`}
          >
            <BarChart3 className="h-4 w-4" />.  Google Search Console, GA4  AdSense Engine</button>

          <button
            onClick={() => setActiveTab('branded_email')}
            className={`inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition ${
              activeTab === 'branded_email'
                ? 'bg-[#00f5d4] text-slate-950 shadow-lg shadow-[#00f5d4]/20'
                : 'border border-slate-700 bg-slate-900/90 text-slate-300 hover:border-[#00f5d4]/40'
            }`}
          >
            <Mail className="h-4 w-4" />.   Engine (admin@hackersshikkhok.com)</button>

          <button
            onClick={() => setActiveTab('auth_cards_reader')}
            className={`inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition ${
              activeTab === 'auth_cards_reader'
                ? 'bg-[#00f5d4] text-slate-950 shadow-lg shadow-[#00f5d4]/20'
                : 'border border-slate-700 bg-slate-900/90 text-slate-300 hover:border-[#00f5d4]/40'
            }`}
          >
            <LayoutGrid className="h-4 w-4" />.  Auth ,       UX</button>
        </div>
      </div>

      {/* TAB 1: NATIVE CLASSIC EDITOR + SEO + SCHEMA + SITEMAP */}
      {activeTab === 'classic_seo_sitemap' && (
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
          {/* Left 7 Cols: Native Classic Editor & Structured Metabox */}
          <div className="lg:col-span-7 space-y-5 rounded-2xl border border-slate-800 bg-[#0b1120] p-5">
            <div className="flex flex-wrap items-center justify-between gap-2 border-b border-slate-800 pb-3">
              <div>
                <span className="text-xs font-mono uppercase text-[#00f5d4]">
                  NativeClassicEditorEngine.php · Zero Gutenberg Bloat
                </span>
                <h3 className="text-lg font-bold text-white">SEO/Schema</h3>
              </div>
              <div className="flex items-center gap-1 rounded-lg border border-slate-700 bg-slate-900 p-1 text-xs">
                <button
                  onClick={() => setClassicMode('visual')}
                  className={`rounded px-2.5 py-1 font-bold ${
                    classicMode === 'visual'
                      ? 'bg-[#00f5d4] text-slate-950'
                      : 'text-slate-400 hover:text-white'
                  }`}
                >
                  Visual
                </button>
                <button
                  onClick={() => setClassicMode('text')}
                  className={`rounded px-2.5 py-1 font-bold font-mono ${
                    classicMode === 'text'
                      ? 'bg-[#00f5d4] text-slate-950'
                      : 'text-slate-400 hover:text-white'
                  }`}
                >
                  Text / HTML
                </button>
              </div>
            </div>

            <div className="space-y-3">
              <div>
                <label className="block text-xs font-bold text-slate-300 mb-1">(Post Title — H1)</label>
                <input
                  type="text"
                  value={postTitle}
                  onChange={(e) => setPostTitle(e.target.value)}
                  className="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2 text-sm font-bold text-white focus:border-[#00f5d4] focus:outline-none"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-300 mb-1">(Clean SEO Slug)</label>
                <div className="flex items-center rounded-xl border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs font-mono text-slate-400">
                  <span>https://hackersshikkhok.com/tutorials/</span>
                  <input
                    type="text"
                    value={postSlug}
                    onChange={(e) => setPostSlug(e.target.value)}
                    className="ml-1 flex-1 bg-transparent text-[#00f5d4] focus:outline-none"
                  />
                  <span>/</span>
                </div>
              </div>

              {/* Classic Toolbar + Content Area */}
              <div className="rounded-xl border border-slate-700 bg-slate-950 overflow-hidden">
                <div className="flex flex-wrap items-center gap-1.5 border-b border-slate-800 bg-slate-900 px-3 py-2 text-xs text-slate-300">
                  <span className="rounded bg-slate-800 px-2 py-0.5 font-bold">B</span>
                  <span className="rounded bg-slate-800 px-2 py-0.5 italic">I</span>
                  <span className="rounded bg-slate-800 px-2 py-0.5 font-mono">H2</span>
                  <span className="rounded bg-slate-800 px-2 py-0.5 font-mono">H3</span>
                  <span className="rounded bg-slate-800 px-2 py-0.5 font-mono">&lt;code&gt;</span>
                  <span className="rounded bg-[#00f5d4]/15 text-[#00f5d4] px-2 py-0.5 font-bold">
                    + Callout Box
                  </span>
                  <span className="rounded bg-purple-500/20 text-purple-300 px-2 py-0.5 font-bold">
                    + Live Demo Shortcode
                  </span>
                </div>
                <textarea
                  rows={5}
                  value={postContent}
                  onChange={(e) => setPostContent(e.target.value)}
                  className="w-full bg-slate-950 p-3 text-xs font-mono text-slate-200 focus:outline-none"
                />
              </div>

              {/* Native SEO, Schema & Related Binding Metabox */}
              <div className="rounded-xl border border-[#00f5d4]/30 bg-slate-950/90 p-4 space-y-3">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-extrabold text-[#00f5d4]">
                    ⚙️ HackersShikkhok — Native SEO, Schema & Related Resource Metabox
                  </span>
                  <span className="rounded bg-emerald-500/15 px-2 py-0.5 text-[11px] font-bold text-emerald-300">
                    SEO Score: 96 / 100
                  </span>
                </div>

                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                  <div>
                    <label className="block text-[11px] font-bold text-slate-400 mb-1">
                      SEO Title ({seoTitle.length}/60 chars)
                    </label>
                    <input
                      type="text"
                      value={seoTitle}
                      onChange={(e) => setSeoTitle(e.target.value)}
                      className="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-1.5 text-xs text-white"
                    />
                  </div>
                  <div>
                    <label className="block text-[11px] font-bold text-slate-400 mb-1">
                      Schema.org Type (Single-Emit JSON-LD)
                    </label>
                    <select
                      value={schemaType}
                      onChange={(e) => setSchemaType(e.target.value as typeof schemaType)}
                      className="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-1.5 text-xs text-[#00f5d4] font-bold"
                    >
                      <option value="TechArticle">TechArticle (/Cyber )</option>
                      <option value="SoftwareApplication">SoftwareApplication (Tool)</option>
                      <option value="Course">Course ( Course)</option>
                      <option value="FAQPage">FAQPage ()</option>
                      <option value="HowTo">HowTo (-- )</option>
                    </select>
                  </div>
                </div>

                <div>
                  <label className="block text-[11px] font-bold text-slate-400 mb-1">
                    Meta Description ({seoDesc.length}/160 chars)
                  </label>
                  <textarea
                    rows={2}
                    value={seoDesc}
                    onChange={(e) => setSeoDesc(e.target.value)}
                    className="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-1.5 text-xs text-slate-200"
                  />
                </div>

                <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
                  <div>
                    <label className="block text-[11px] font-bold text-slate-400 mb-1">
                      Related Tool Slug
                    </label>
                    <input
                      type="text"
                      value={relatedToolSlug}
                      onChange={(e) => setRelatedToolSlug(e.target.value)}
                      className="w-full rounded-lg border border-slate-700 bg-slate-900 px-2.5 py-1.5 text-xs font-mono text-cyan-300"
                    />
                  </div>
                  <div>
                    <label className="block text-[11px] font-bold text-slate-400 mb-1">
                      Related Code Snippet
                    </label>
                    <input
                      type="text"
                      value={relatedCodeSlug}
                      onChange={(e) => setRelatedCodeSlug(e.target.value)}
                      className="w-full rounded-lg border border-slate-700 bg-slate-900 px-2.5 py-1.5 text-xs font-mono text-purple-300"
                    />
                  </div>
                  <div>
                    <label className="block text-[11px] font-bold text-slate-400 mb-1">
                      YouTube Video ID
                    </label>
                    <input
                      type="text"
                      value={relatedYoutubeId}
                      onChange={(e) => setRelatedYoutubeId(e.target.value)}
                      className="w-full rounded-lg border border-slate-700 bg-slate-900 px-2.5 py-1.5 text-xs font-mono text-rose-300"
                    />
                  </div>
                </div>

                <div className="flex flex-wrap items-center justify-between gap-2 pt-2">
                  <div className="flex items-center gap-2 text-xs">
                    <span className="text-slate-400">Robots Directive:</span>
                    <button
                      onClick={() =>
                        setRobotsDirective(
                          robotsDirective === 'index, follow'
                            ? 'noindex, nofollow'
                            : 'index, follow'
                        )
                      }
                      className={`rounded-md px-2.5 py-1 font-mono font-bold ${
                        robotsDirective === 'index, follow'
                          ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40'
                          : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'
                      }`}
                    >
                      {robotsDirective}
                    </button>
                  </div>

                  <button
                    onClick={() => {
                      setMetaboxSavedToast(true);
                      setTimeout(() => setMetaboxSavedToast(false), 2500);
                    }}
                    className="inline-flex items-center gap-1.5 rounded-lg bg-[#00f5d4] px-4 py-1.5 text-xs font-extrabold text-slate-950 hover:bg-[#00f5d4]/90"
                  >
                    <CheckCircle2 className="h-3.5 w-3.5" />Cyber Defense Hub</button>
                </div>

                {metaboxSavedToast && (
                  <div className="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs font-bold text-emerald-300">✓ Native Classic Post, Single-Emit JSON-LD Schema ({schemaType})  Related
                    Bindings  !</div>
                )}
              </div>
            </div>
          </div>

          {/* Right 5 Cols: Google SERP Preview, Sitemap & Robots.txt Inspector */}
          <div className="lg:col-span-5 space-y-5">
            {/* Live Google SERP Preview */}
            <div className="rounded-2xl border border-slate-800 bg-[#0b1120] p-5">
              <div className="flex items-center justify-between mb-3">
                <span className="text-xs font-bold uppercase tracking-wider text-[#00f5d4]">🔍       (SERP & OG Card)</span>
                <span className="text-[11px] font-mono text-slate-400">Single-Emit Verified</span>
              </div>
              <div className="rounded-xl border border-slate-800 bg-slate-950 p-4 space-y-1.5">
                <div className="flex items-center gap-2 text-xs text-slate-400">
                  <span className="inline-block h-4 w-4 rounded-full bg-[#00f5d4]/20 text-center text-[10px] font-bold text-[#00f5d4]">
                    H
                  </span>
                  <span>hackersshikkhok.com</span>
                  <span>› tutorials › {postSlug.slice(0, 28)}...</span>
                </div>
                <div className="text-base font-bold text-blue-400 hover:underline cursor-pointer">
                  {seoTitle}
                </div>
                <p className="text-xs text-slate-300 leading-relaxed">{seoDesc}</p>
                <div className="pt-2 flex flex-wrap gap-1.5 text-[10px] font-mono text-emerald-300">
                  <span className="rounded bg-emerald-500/10 px-2 py-0.5 border border-emerald-500/30">
                    Schema: @type/{schemaType}
                  </span>
                  <span className="rounded bg-cyan-500/10 px-2 py-0.5 border border-cyan-500/30 text-cyan-300">
                    Canonical: Valid
                  </span>
                  <span className="rounded bg-purple-500/10 px-2 py-0.5 border border-purple-500/30 text-purple-300">
                    CLS Image: width/height enforced
                  </span>
                </div>
              </div>
            </div>

            {/* Native XML Sitemap & Indexation Control Inspector */}
            <div className="rounded-2xl border border-slate-800 bg-[#0b1120] p-5 space-y-3">
              <div className="flex items-center justify-between">
                <span className="text-xs font-bold text-white">🗺️  XML Sitemap (`/sitemap_index.xml`)  Indexation Control</span>
                <span className="rounded bg-[#00f5d4]/15 px-2 py-0.5 text-[11px] font-mono text-[#00f5d4]">
                  Auto-Updated
                </span>
              </div>
              <pre className="overflow-x-auto rounded-xl border border-slate-800 bg-slate-950 p-3 text-[11px] font-mono text-cyan-300 leading-relaxed">
{`<!-- Generated natively by HackersShikkhok Core (NativeSeoAndSitemapEngine.php) -->
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap><loc>https://hackersshikkhok.com/post-sitemap.xml</loc></sitemap>
  <sitemap><loc>https://hackersshikkhok.com/tutorials-sitemap.xml</loc></sitemap>
  <sitemap><loc>https://hackersshikkhok.com/tools-sitemap.xml</loc></sitemap>
  <sitemap><loc>https://hackersshikkhok.com/code-sitemap.xml</loc></sitemap>
  <sitemap><loc>https://hackersshikkhok.com/cyber-sitemap.xml</loc></sitemap>
  <sitemap><loc>https://hackersshikkhok.com/courses-sitemap.xml</loc></sitemap>
</sitemapindex>`}
              </pre>
              <div className="rounded-xl border border-slate-800 bg-slate-950 p-3 text-xs space-y-1">
                <div className="font-bold text-slate-200">(Auto `noindex, nofollow`):</div>
                <p className="text-slate-400 font-mono text-[11px]">
                  ✓ /wp-admin/ · ✓ /dashboard/ · ✓ /messages/ · ✓ /notifications/ · ✓ /auth/* · ✓
                  /?s=* (Empty Search)
                </p>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* TAB 2: NATIVE GOOGLE SEARCH CONSOLE, GA4 & ADSENSE ENGINE */}
      {activeTab === 'google_adsense_ga4' && (
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
          {/* Left 5 Cols: Search Console & GA4 Native Controls */}
          <div className="lg:col-span-5 space-y-5 rounded-2xl border border-slate-800 bg-[#0b1120] p-5">
            <div>
              <span className="text-xs font-mono text-[#00f5d4]">
                NativeGoogleAndAdsEngine.php · Zero Site-Kit Bloat
              </span>
              <h3 className="text-lg font-bold text-white mt-0.5">GA4  Engine</h3>
            </div>

            <div className="space-y-3 rounded-xl border border-slate-800 bg-slate-950 p-4">
              <label className="block text-xs font-bold text-slate-200">. Google Search Console Verification Token</label>
              <input
                type="text"
                value={gscToken}
                onChange={(e) => setGscToken(e.target.value)}
                className="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-xs font-mono text-[#00f5d4]"
              />
              <div className="text-[11px] font-mono text-slate-400">
                Emits: <code>&lt;meta name="google-site-verification" content="{gscToken}" /&gt;</code>
              </div>
            </div>

            <div className="space-y-3 rounded-xl border border-slate-800 bg-slate-950 p-4">
              <div className="flex items-center justify-between">
                <label className="text-xs font-bold text-slate-200">. Native Google Analytics 4 (GA4 Measurement ID)</label>
                <span className="rounded bg-emerald-500/15 px-2 py-0.5 text-[10px] font-mono font-bold text-emerald-300">
                  Single-Script Lock
                </span>
              </div>
              <input
                type="text"
                value={ga4Id}
                onChange={(e) => setGa4Id(e.target.value)}
                className="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-xs font-mono text-cyan-300"
              />
              <label className="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                <input
                  type="checkbox"
                  checked={excludeAdminGa4}
                  onChange={(e) =>setExcludeAdminGa4(e.target.checked)}
                  className="rounded border-slate-600"
                />
                         (Clean Analytics Data)</label>
            </div>

            <div className="space-y-2 rounded-xl border border-slate-800 bg-slate-950 p-4">
              <div className="flex items-center justify-between">
                <label className="text-xs font-bold text-slate-200">. Native `/ads.txt` Publisher Verification</label>
                <span className="text-[11px] font-mono text-[#00f5d4]">Valid Syntax</span>
              </div>
              <textarea
                rows={2}
                value={adsTxtContent}
                onChange={(e) => setAdsTxtContent(e.target.value)}
                className="w-full rounded-lg border border-slate-700 bg-slate-900 p-2.5 text-xs font-mono text-emerald-300"
              />
            </div>
          </div>

          {/* Right 7 Cols: CLS-Safe AdSense & Sponsor Slot Manager */}
          <div className="lg:col-span-7 space-y-4 rounded-2xl border border-slate-800 bg-[#0b1120] p-5">
            <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 pb-3">
              <div>
                <h3 className="text-lg font-bold text-white">AdSense  CLS-Safe</h3>
                <p className="text-xs text-slate-400">Login, Register, 404,         
                   ।</p>
              </div>
              <button
                onClick={() => setAdsMasterEnabled(!adsMasterEnabled)}
                className={`rounded-xl px-4 py-2 text-xs font-extrabold transition ${
                  adsMasterEnabled
                    ? 'bg-emerald-500 text-slate-950'
                    : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'
                }`}
              >
                Master Ads Switch: {adsMasterEnabled ? 'ACTIVE (ON)' : 'DISABLED (OFF)'}
              </button>
            </div>

            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
              {adSlots.map((slot) => (
                <div
                  key={slot.id}
                  className="rounded-xl border border-slate-800 bg-slate-950 p-3.5 flex flex-col justify-between gap-2"
                >
                  <div className="flex items-start justify-between gap-2">
                    <div>
                      <div className="text-xs font-bold text-white">{slot.nameBn}</div>
                      <div className="text-[11px] text-slate-400 mt-0.5">{slot.location}</div>
                    </div>
                    <button
                      onClick={() => toggleAdSlot(slot.id)}
                      className={`rounded-lg px-2.5 py-1 text-[11px] font-mono font-bold ${
                        slot.enabled && adsMasterEnabled
                          ? 'bg-[#00f5d4]/20 text-[#00f5d4] border border-[#00f5d4]/40'
                          : 'bg-slate-800 text-slate-400'
                      }`}
                    >
                      {slot.enabled && adsMasterEnabled ? 'ENABLED' : 'OFF'}
                    </button>
                  </div>
                  <div className="flex items-center justify-between border-t border-slate-800/80 pt-2 text-[11px] font-mono text-slate-400">
                    <span>CLS Reserved: min-h-[{slot.reservedHeightPx}px]</span>
                    <span className="uppercase text-cyan-300">{slot.deviceTarget}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* TAB 3: NATIVE BRANDED EMAIL ENGINE (admin@hackersshikkhok.com) */}
      {activeTab === 'branded_email' && (
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-12">
          {/* Left 5 Cols: 10 Transactional Templates Selector */}
          <div className="lg:col-span-5 space-y-3 rounded-2xl border border-slate-800 bg-[#0b1120] p-5">
            <div className="border-b border-slate-800 pb-3">
              <span className="text-xs font-mono text-[#00f5d4]">
                NativeBrandedEmailEngine.php · Sender: admin@hackersshikkhok.com
              </span>
              <h3 className="text-lg font-bold text-white mt-0.5">HTML</h3>
            </div>

            <div className="space-y-1.5 max-h-[430px] overflow-y-auto pr-1">
              {BRANDED_EMAIL_TEMPLATES.map((tpl) => (
                <button
                  key={tpl.id}
                  onClick={() => setSelectedEmailId(tpl.id)}
                  className={`w-full text-left rounded-xl px-3.5 py-2.5 text-xs transition flex items-center justify-between ${
                    selectedEmailId === tpl.id
                      ? 'bg-[#00f5d4]/15 border border-[#00f5d4] text-white font-bold'
                      : 'border border-slate-800 bg-slate-950 text-slate-300 hover:border-slate-700'
                  }`}
                >
                  <span>{tpl.nameBn}</span>
                  <span className="font-mono text-[10px] text-[#00f5d4]">{tpl.triggerHook}</span>
                </button>
              ))}
            </div>

            {/* Test Dispatch Bar */}
            <div className="pt-2 border-t border-slate-800 space-y-2">
              <label className="block text-xs font-bold text-slate-300">(Test Dispatch Simulation):</label>
              <div className="flex gap-2">
                <input
                  type="email"
                  value={testEmailRecipient}
                  onChange={(e) => setTestEmailRecipient(e.target.value)}
                  className="flex-1 rounded-lg border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs text-white"
                />
                <button
                  onClick={handleDispatchTestEmail}
                  className="inline-flex items-center gap-1.5 rounded-lg bg-[#00f5d4] px-3.5 py-1.5 text-xs font-extrabold text-slate-950 hover:bg-[#00f5d4]/90"
                >
                  <Send className="h-3.5 w-3.5" />
                  Send Test
                </button>
              </div>
              {emailSentLog.length > 0 && (
                <div className="rounded-lg border border-emerald-500/30 bg-slate-950 p-2.5 text-[11px] font-mono text-emerald-300 space-y-1">
                  {emailSentLog.map((log, i) => (
                    <div key={i}>✓ {log}</div>
                  ))}
                </div>
              )}
            </div>
          </div>

          {/* Right 7 Cols: Live Branded HTML Email Preview */}
          <div className="lg:col-span-7 rounded-2xl border border-slate-800 bg-[#0b1120] p-5 space-y-4">
            <div className="flex flex-wrap items-center justify-between gap-2 border-b border-slate-800 pb-3 text-xs">
              <div>
                <div className="text-slate-400">
                  From:{' '}
                  <strong className="text-[#00f5d4]">
                    HackersShikkhok &lt;admin@hackersshikkhok.com&gt;
                  </strong>
                </div>
                <div className="text-white font-bold mt-0.5">Subject: {selectedEmail.subject}</div>
              </div>
              <span className="rounded-lg border border-purple-500/40 bg-purple-500/10 px-2.5 py-1 font-mono text-[11px] text-purple-300">
                Hook: {selectedEmail.triggerHook}
              </span>
            </div>

            {/* Rendered Branded Email Box */}
            <div className="rounded-2xl border-2 border-[#00f5d4] bg-[#050811] overflow-hidden shadow-2xl">
              <div className="border-b border-slate-800 bg-[#0b1120] px-6 py-4 flex items-center justify-between">
                <div className="flex items-center gap-2.5">
                  <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-[#00f5d4]/20 border border-[#00f5d4] font-mono font-extrabold text-[#00f5d4]">
                    HS
                  </span>
                  <div>
                    <div className="text-base font-extrabold text-[#00f5d4]">HackersShikkhok</div>
                    <div className="text-[11px] text-slate-400">
                      HackersShikkhok.com · YouTube: @HackersShikkhok
                    </div>
                  </div>
                </div>
                <span className="rounded bg-slate-900 px-2.5 py-1 text-[11px] font-mono text-slate-300">
                  {selectedEmail.recipientRole}
                </span>
              </div>

              <div className="p-6 space-y-4">
                <h4 className="text-lg font-extrabold text-white">{selectedEmail.subject}</h4>
                <div
                  className="text-sm text-slate-300 leading-relaxed"
                  dangerouslySetInnerHTML={{ __html: selectedEmail.bodyHtml }}
                />
                <div className="pt-2">
                  <a
                    href="#native-master-spec-hub"
                    onClick={(e) => {
                      e.preventDefault();
                      handleDispatchTestEmail();
                    }}
                    className="inline-block rounded-xl bg-[#00f5d4] px-5 py-2.5 text-xs font-extrabold text-slate-950 shadow-lg shadow-[#00f5d4]/25"
                  >
                    {selectedEmail.ctaLabel} →
                  </a>
                </div>
              </div>

              <div className="border-t border-slate-800 bg-[#0b1120] px-6 py-3 text-[11px] text-slate-400 flex flex-wrap items-center justify-between gap-2">
                <span>© 2026 HackersShikkhok (https://hackersshikkhok.com)</span>
                <span>Official Sender: admin@hackersshikkhok.com · Privacy & Security</span>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* TAB 4: BRANDED AUTH PORTAL + 5 CARD VARIANTS + SINGLE ARTICLE READER UX */}
      {activeTab === 'auth_cards_reader' && (
        <div className="space-y-8">
          {/* 4A. Branded Auth Portal (Replaces wp-login.php) */}
          <div className="grid grid-cols-1 gap-6 lg:grid-cols-12 rounded-2xl border border-slate-800 bg-[#0b1120] p-5">
            <div className="lg:col-span-5 space-y-4">
              <span className="text-xs font-mono text-[#00f5d4]">
                NativeBrandedAuthEngine.php · Replaces Default wp-login.php
              </span>
              <h3 className="text-xl font-extrabold text-white">🔐</h3>
              <p className="text-xs text-slate-300 leading-relaxed">Cyber Defense Hub<code>wp-login.php</code>। Login, Register,  ,    Passkey/2FA  
                Cyber-   ।</p>
              <div className="flex flex-wrap gap-2">{(
                  [
                    { id: 'login', label: '. Login (/auth/login)' },
                    { id: 'register', label: '. Register (/auth/register)' },
                    { id: 'forgot', label: '.  ' },
                    { id: 'verify', label: '.  ' },
                    { id: 'passkey', label: '. Passkey / 2FA' }
                  ] as const
                ).map((tab) => (<button
                    key={tab.id}
                    onClick={() => {
                      setAuthView(tab.id);
                      setAuthFeedback(null);
                    }}
                    className={`rounded-lg px-3 py-1.5 text-xs font-bold transition ${
                      authView === tab.id
                        ? 'bg-[#00f5d4] text-slate-950'
                        : 'border border-slate-700 bg-slate-900 text-slate-300'
                    }`}
                  >
                    {tab.label}
                  </button>
                ))}
              </div>
            </div>

            <div className="lg:col-span-7">
              <div className="rounded-2xl border border-[#00f5d4]/40 bg-slate-950 p-5 space-y-4">
                <div className="flex items-center justify-between border-b border-slate-800 pb-3">
                  <div className="flex items-center gap-2">
                    <Lock className="h-4 w-4 text-[#00f5d4]" />
                    <span className="text-sm font-extrabold text-white">HackersShikkhok —{' '}
                      {authView === 'login'
                        ? ' Login '
                        : authView === 'register'
                        ? '  Register'
                        : authView === 'forgot'
                        ? '  (admin@hackersshikkhok.com)'
                        : authView === 'verify'
                        ? ' / '
                        : 'WebAuthn Passkey  2FA Security'}</span>
                  </div>
                  <span className="text-[11px] font-mono text-emerald-300">
                    CSRF Nonce + Rate Limit Active
                  </span>
                </div>

                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                  <div>
                    <label className="block text-xs text-slate-400 mb-1">Cyber Defense Hub</label>
                    <input
                      type="text"
                      defaultValue="member@hackersshikkhok.com"
                      className="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-xs text-white"
                    />
                  </div>
                  {authView !== 'forgot' && authView !== 'verify' && (
                    <div>
                      <label className="block text-xs text-slate-400 mb-1">/ Security</label>
                      <input
                        type="password"
                        defaultValue="••••••••••••"
                        className="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-xs text-white"
                      />
                    </div>
                  )}
                </div>

                <div className="flex flex-wrap items-center justify-between gap-2 pt-1">
                  <button
                    onClick={() =>setAuthFeedback(
                        `✓ [${authView.toUpperCase()}] Success   — Nonce  Rate-Limit !`
                      )
                    }
                    className="inline-flex items-center gap-1.5 rounded-xl bg-[#00f5d4] px-4 py-2 text-xs font-extrabold text-slate-950"
                  ><KeyRound className="h-3.5 w-3.5" />{authView === 'login'
                      ? ' Login '
                      : authView === 'register'
                      ? '  '
                      : authView === 'forgot'
                      ? '  '
                      : authView === 'verify'
                      ? '  '
                      : 'Passkey  '}</button>
                  <span className="text-[11px] text-slate-400">Cyber Defense Hub</span>
                </div>

                {authFeedback && (
                  <div className="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs font-bold text-emerald-300">
                    {authFeedback}
                  </div>
                )}
              </div>
            </div>
          </div>

          {/* 4B. 5 Distinct Content Card Variants Showcase (Section 9) */}
          <div className="rounded-2xl border border-slate-800 bg-[#0b1120] p-5 space-y-4">
            <div>
              <span className="text-xs font-mono text-[#00f5d4]">
                SECTION 9 · 5 DISTINCT CARD VARIANTS ARCHITECTURE
              </span>
              <h3 className="text-xl font-extrabold text-white mt-0.5">🎴       (Standard, Featured, Tool, Video  Compact)</h3>
            </div>

            <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
              {/* Variant 1: Standard Article Card */}
              <div className="rounded-xl border border-slate-800 bg-slate-950 p-4 flex flex-col justify-between">
                <div>
                  <span className="inline-block rounded bg-cyan-500/15 px-2 py-0.5 text-[10px] font-bold text-cyan-300">
                    1. Standard Post Card
                  </span>
                  <h4 className="mt-2 text-sm font-bold text-white line-clamp-2">Linux UFW Firewall  SSH Hardening</h4>
                  <p className="mt-1 text-xs text-slate-400 line-clamp-2">-   ।</p>
                </div>
                <div className="mt-3 pt-2 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
                  <span>⏱️</span>
                  <span className="text-[#00f5d4] font-bold">→</span>
                </div>
              </div>

              {/* Variant 2: Featured Hero Card */}
              <div className="rounded-xl border-2 border-[#00f5d4]/60 bg-gradient-to-br from-slate-950 to-[#0d1f2d] p-4 flex flex-col justify-between">
                <div>
                  <span className="inline-block rounded bg-[#00f5d4] px-2 py-0.5 text-[10px] font-extrabold text-slate-950">
                    2. Featured Hero Card
                  </span>
                  <h4 className="mt-2 text-sm font-extrabold text-white">OWASP Top 10 (2026) —  English Defense</h4>
                  <p className="mt-1 text-xs text-slate-300">Lab ,     - ।</p>
                </div>
                <div className="mt-3 pt-2 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-[#00f5d4] font-bold">
                  <span>★ Editor’s Choice</span>
                  <span>Lab  →</span>
                </div>
              </div>

              {/* Variant 3: Tool Utility Card */}
              <div className="rounded-xl border border-purple-500/40 bg-slate-950 p-4 flex flex-col justify-between">
                <div>
                  <span className="inline-flex items-center gap-1 rounded bg-purple-500/20 px-2 py-0.5 text-[10px] font-bold text-purple-300">
                    <Wrench className="h-3 w-3" /> 3. Tool Card
                  </span>
                  <h4 className="mt-2 text-sm font-bold text-white">SHA-256 / JWT & Base64 Security</h4>
                  <p className="mt-1 text-xs text-slate-400">% -     ।</p>
                </div>
                <div className="mt-3 pt-2 border-t border-slate-800 flex items-center justify-between text-[11px] text-purple-300 font-bold">
                  <span>⚡ Client-Side</span>
                  <span>Launch Tool →</span>
                </div>
              </div>

              {/* Variant 4: Video / YouTube Card */}
              <div className="rounded-xl border border-rose-500/40 bg-slate-950 p-4 flex flex-col justify-between">
                <div>
                  <span className="inline-flex items-center gap-1 rounded bg-rose-500/20 px-2 py-0.5 text-[10px] font-bold text-rose-300">
                    <Play className="h-3 w-3" /> 4. Video Card
                  </span>
                  <h4 className="mt-2 text-sm font-bold text-white">@HackersShikkhok —</h4>
                  <p className="mt-1 text-xs text-slate-400">+   +  ।</p>
                </div>
                <div className="mt-3 pt-2 border-t border-slate-800 flex items-center justify-between text-[11px] text-rose-300 font-bold">
                  <span>▶️ २४:</span>
                  <span>Watch Now →</span>
                </div>
              </div>

              {/* Variant 5: Compact List Card */}
              <div className="rounded-xl border border-slate-800 bg-slate-950 p-4 flex flex-col justify-between">
                <div>
                  <span className="inline-block rounded bg-amber-500/20 px-2 py-0.5 text-[10px] font-bold text-amber-300">
                    5. Compact Trending List
                  </span>
                  <ul className="mt-2 space-y-1.5 text-xs text-slate-300">
                    <li className="truncate hover:text-[#00f5d4]">#1 wp-config.php Security</li>
                    <li className="truncate hover:text-[#00f5d4]">#2 Python   Defense Lab</li>
                    <li className="truncate hover:text-[#00f5d4]">
                      #3 CSS Neon Glassmorphism Generator
                    </li>
                  </ul>
                </div>
                <div className="mt-3 pt-2 border-t border-slate-800 text-[11px] text-amber-300 font-bold">→</div>
              </div>
            </div>
          </div>

          {/* 4C. Complete Single Post / Article Reader Experience (Section 10) */}
          <div className="rounded-2xl border border-slate-800 bg-[#0b1120] p-5 sm:p-6 space-y-5">
            <div className="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-400 border-b border-slate-800 pb-3">
              <div className="font-mono">
                Home › <span className="text-[#00f5d4]">Cyber Security</span>›</div>
              <div className="flex items-center gap-3">
                <span>:</span>
                <span>•</span>
                <span className="text-[#00f5d4] font-bold">⏱️  :</span>
              </div>
            </div>

            <h3 className="text-xl sm:text-2xl font-extrabold text-white leading-snug">WordPress Core Plugin  REST API : Nonce, Capability Check  Prepared SQL</h3>

            {/* Key Takeaways + Auto Table of Contents */}
            <div className="grid grid-cols-1 gap-4 lg:grid-cols-12">
              <div className="lg:col-span-7 rounded-xl border border-[#00f5d4]/30 bg-slate-950 p-4 space-y-2">
                <div className="text-xs font-extrabold text-[#00f5d4] uppercase">📌   (Key Takeaways)</div>
                <ul className="list-disc pl-5 text-xs text-slate-300 space-y-1.5">
                  <li>SQL    ; {' '}<code className="text-[#00f5d4]">$wpdb-&gt;prepare()</code>।</li>
                  <li>REST API<code className="text-[#00f5d4]">permission_callback</code>{' '}
                     AJAX<code className="text-[#00f5d4]">check_ajax_referer()</code>{' '}
                    ।</li>
                  <li>{' '}<code className="text-[#00f5d4]">esc_html()</code>,{' '}
                    <code className="text-[#00f5d4]">esc_attr()</code>{' '}<code className="text-[#00f5d4]">esc_url()</code>।</li>
                </ul>
              </div>

              <div className="lg:col-span-5 rounded-xl border border-slate-800 bg-slate-950 p-4 space-y-2">
                <div className="text-xs font-extrabold text-white">📑     (Auto TOC)</div>
                <ol className="text-xs text-slate-300 space-y-1 font-mono">
                  <li className="text-[#00f5d4]">.  REST API  Nonce  ?</li>
                  <li>. Terminal  WP-CLI Security Audit</li>
                  <li>.   Security</li>
                  <li>.</li>
                </ol>
              </div>
            </div>

            {/* Terminal Command Block + Callout Box */}
            <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
              <div className="rounded-xl border border-slate-800 bg-slate-950 p-4 space-y-2">
                <div className="flex items-center justify-between">
                  <span className="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-[#00f5d4]">
                    <Terminal className="h-3.5 w-3.5" /> WP-CLI Security Check Command
                  </span>
                  <button
                    onClick={() => {
                      navigator.clipboard?.writeText(
                        'wp plugin verify-checksums --all && wp core verify-checksums'
                      );
                      setCopiedTerminal(true);
                      setTimeout(() => setCopiedTerminal(false), 2000);
                    }}
                    className="inline-flex items-center gap-1 rounded bg-slate-800 px-2.5 py-1 text-[11px] font-bold text-slate-200 hover:bg-[#00f5d4] hover:text-slate-950"
                  >
                    {copiedTerminal ? <Check className="h-3 w-3" /> : <Copy className="h-3 w-3" />}
                    {copiedTerminal ? 'Copied!' : 'Copy Command'}
                  </button>
                </div>
                <pre className="rounded-lg bg-[#050811] p-3 text-xs font-mono text-emerald-300 overflow-x-auto">
                  $ wp plugin verify-checksums --all &amp;&amp; wp core verify-checksums
                </pre>
              </div>

              <div className="rounded-xl border border-amber-500/40 bg-amber-500/10 p-4 flex items-start gap-3">
                <AlertTriangle className="h-5 w-5 text-amber-400 shrink-0 mt-0.5" />
                <div className="text-xs text-amber-100 space-y-1">
                  <div className="font-extrabold text-amber-300">⚠️ Security  (Defensive Security Notice)</div>
                  <p className="leading-relaxed">PHP  Python {' '}<code>eval()</code>Cyber Defense Hub<code>shell_exec()</code>। 
                        ।</p>
                </div>
              </div>
            </div>

            {/* Author Card + Engagement Bar */}
            <div className="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-slate-800 bg-slate-950 p-4">
              <div className="flex items-center gap-3">
                <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-[#00f5d4]/15 border border-[#00f5d4] font-mono font-extrabold text-[#00f5d4]">
                  HS
                </div>
                <div>
                  <div className="text-sm font-extrabold text-white">
                    HackersShikkhok (Official Editorial Team)
                  </div>
                  <div className="text-xs text-slate-400">
                    Cybersecurity & WordPress Architecture · https://hackersshikkhok.com
                  </div>
                </div>
              </div>

              <div className="flex flex-wrap items-center gap-2">
                <button
                  onClick={() => setReaderLiked(!readerLiked)}
                  className={`rounded-lg px-3 py-1.5 text-xs font-bold transition ${
                    readerLiked
                      ? 'bg-[#00f5d4] text-slate-950'
                      : 'border border-slate-700 bg-slate-900 text-slate-300'
                  }`}
                >
                  👍 Helpful ({readerLiked ? 149 : 148})
                </button>
                <button
                  onClick={() =>setReaderSaved(!readerSaved)}
                  className={`rounded-lg px-3 py-1.5 text-xs font-bold transition ${
                    readerSaved
                      ? 'bg-purple-500 text-white'
                      : 'border border-slate-700 bg-slate-900 text-slate-300'
                  }`}
                >
                  🔖 {readerSaved ? '  ' : ' '}</button>
              </div>
            </div>
          </div>
        </div>
      )}
    </section>
  );
};
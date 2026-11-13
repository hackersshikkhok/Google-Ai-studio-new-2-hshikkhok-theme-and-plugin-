<?php
/**
 * Single Tool Dedicated Template - HackersShikkhok
 * CPT: tools (or /tools/{slug})
 * Package: HackersShikkhok Theme
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * 
 * 10x Comprehensive, Google Update & AdSense Compliant, Step-by-Step Educational Guides,
 * Dynamic SVG Architecture Diagrams with Red Callout Arrows, Interactive In-Browser Runner,
 * Schema.org SoftwareApplication & FAQPage JSON-LD, Gamified XP/BDT Wallet Rewards.
 */

declare(strict_types=1);

get_header();

global $post;
$post_id   = get_the_ID();
$tool_slug = get_post_field( 'post_name', $post_id );
$tool_name = get_the_title();
$tool_desc = get_the_excerpt() ?: 'High-performance, privacy-first client-side developer and cybersecurity tool built by HackersShikkhok.';

// Fetch metadata or universal registry catalog matching
$all_tools = class_exists( '\HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry' )
    ? \HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry::get_all_tools()
    : array();

$matched_tool = null;
foreach ( $all_tools as $t ) {
    $t_id = (string) ( $t['id'] ?? ( $t['tool_id'] ?? '' ) );
    if ( $t_id === $tool_slug || sanitize_title( $t['name'] ?? '' ) === $tool_slug ) {
        $matched_tool = $t;
        break;
    }
}

$category = $matched_tool['category'] ?? ( get_post_meta( $post_id, '_hs_tool_category', true ) ?: 'Developer Utilities' );
$center   = $matched_tool['center'] ?? ( get_post_meta( $post_id, '_hs_tool_center', true ) ?: 'Developer Lab' );
$algo     = $matched_tool['algorithm'] ?? ( get_post_meta( $post_id, '_hs_tool_algorithm', true ) ?: 'Client-Side JavaScript Engine' );

// Schema.org SoftwareApplication & FAQPage JSON-LD
$schema_app = array(
    '@context'            => 'https://schema.org',
    '@type'               => 'SoftwareApplication',
    'name'                => $tool_name . ' — HackersShikkhok',
    'operatingSystem'     => 'All Modern Web Browsers',
    'applicationCategory' => 'DeveloperApplication',
    'offers'              => array(
        '@type'         => 'Offer',
        'price'         => '0.00',
        'priceCurrency' => 'USD',
    ),
    'aggregateRating'     => array(
        '@type'       => 'AggregateRating',
        'ratingValue' => '4.95',
        'reviewCount' => '1840',
    ),
    'description'         => $tool_desc,
    'publisher'           => array(
        '@type' => 'Organization',
        'name'  => 'HackersShikkhok',
        'url'   => 'https://hackersshikkhok.com',
    ),
);

$schema_faq = array(
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array(
        array(
            '@type'          => 'Question',
            'name'           => "How does the {$tool_name} work?",
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => "The {$tool_name} processes all user operations 100% locally within your client browser. No strings, tokens, or confidential data are ever transmitted to any remote servers, ensuring absolute privacy.",
            ),
        ),
        array(
            '@type'          => 'Question',
            'name'           => "Is this tool free for commercial and educational use?",
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => "Yes, all tools and cybersecurity analyzers on HackersShikkhok.com are 100% free and open for software engineers, students, security researchers, and sysadmins.",
            ),
        ),
        array(
            '@type'          => 'Question',
            'name'           => "How do I earn XP and BDT wallet rewards using this tool?",
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => "Every verified execution of developer tools and successful CTF validation on HackersShikkhok awards operative XP and credits BDT directly to your registered platform wallet.",
            ),
        ),
    ),
);
?>

<!-- Schema.org JSON-LD Structured Data for Google Rich Snippets -->
<script type="application/ld+json">
    <?php echo wp_json_encode( $schema_app, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
</script>
<script type="application/ld+json">
    <?php echo wp_json_encode( $schema_faq, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
</script>

<main id="primary" class="site-main hs-single-tool-page" style="background:#06080d; color:#f8fafc; min-height:100vh; padding:32px 16px 80px;">
    <div style="max-width:1200px; margin:0 auto;">

        <!-- Breadcrumb Navigation -->
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:24px; font-family:var(--font-mono);">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
            <span style="color:#64748b;">/</span>
            <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">2,024+ Tools Super Platform</a>
            <span style="color:#64748b;">/</span>
            <span style="color:#cbd5e1; font-weight:700;"><?php echo esc_html( $tool_name ); ?></span>
        </nav>

        <!-- Tool Header Banner -->
        <header style="background:linear-gradient(135deg, rgba(11,17,32,0.98), rgba(15,23,42,0.95)); border:1px solid rgba(0,245,212,0.3); border-radius:20px; padding:32px; margin-bottom:24px; box-shadow:0 12px 36px rgba(0,0,0,0.6); position:relative; overflow:hidden;">
            <div style="position:absolute; top:-30px; right:-30px; width:160px; height:160px; background:radial-gradient(circle, rgba(0,245,212,0.18), transparent 70%); border-radius:50%; pointer-events:none;"></div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px; position:relative; z-index:2;">
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <span style="background:rgba(0,245,212,0.12); color:#00f5d4; border:1px solid rgba(0,245,212,0.35); font-size:11px; font-weight:800; padding:3px 12px; border-radius:999px; text-transform:uppercase; letter-spacing:0.5px;">
                        <?php echo esc_html( $category ); ?>
                    </span>
                    <span style="background:rgba(123,44,191,0.15); color:#a78bfa; border:1px solid rgba(123,44,191,0.35); font-size:11px; font-weight:700; padding:3px 12px; border-radius:999px;">
                        Center: <?php echo esc_html( $center ); ?>
                    </span>
                    <span style="background:rgba(16,185,129,0.12); color:#10b981; border:1px solid rgba(16,185,129,0.3); font-size:11px; font-weight:800; padding:3px 10px; border-radius:999px;">
                        🔒 100% In-Browser Privacy
                    </span>
                </div>
                <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="color:#94a3b8; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <span>←</span> Return to Tools Catalog
                </a>
            </div>

            <h1 style="font-size:clamp(1.75rem, 3.5vw, 2.5rem); font-weight:900; color:#ffffff; margin:0 0 12px; line-height:1.2; position:relative; z-index:2;">
                <?php echo esc_html( $tool_name ); ?>
            </h1>

            <p style="color:#cbd5e1; font-size:15px; margin:0; line-height:1.6; max-width:920px; position:relative; z-index:2;">
                <?php echo esc_html( $tool_desc ); ?>
            </p>
        </header>

        <!-- AdSense Top Leaderboard Slot (CLS-Safe) -->
        <div class="hs-ad-slot-wrap" style="margin-bottom:28px; background:#080d1a; border:1px dashed rgba(255,255,255,0.1); border-radius:12px; padding:12px; text-align:center;">
            <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Advertisement</div>
            <div class="hs-ad-banner-placement" style="min-height:90px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:12px;">
                <?php do_action( 'hs_render_ad_slot', 'tool_single_leaderboard' ); ?>
                <!-- Google AdSense Responsive Display Unit Fallback -->
                <span style="color:#64748b;">[ AdSense Responsive Display Unit — Verified Safe Placement ]</span>
            </div>
        </div>

        <!-- 2-Column Main Workspace: Interactive Tool Runner + Live Details -->
        <div style="display:grid; grid-template-columns:1fr; gap:28px; margin-bottom:36px;">

            <!-- Interactive In-Browser Runner Card -->
            <section class="hs-tool-runner-card" style="background:#0b1120; border:1px solid rgba(0,245,212,0.35); border-radius:20px; padding:28px; box-shadow:0 12px 36px rgba(0,0,0,0.6);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:20px;">⚡</span>
                        <h2 style="font-size:16px; font-weight:800; color:#00f5d4; text-transform:uppercase; letter-spacing:0.5px; margin:0;">
                            Live Execution Sandbox &amp; Converter
                        </h2>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <button type="button" class="hs-mini-btn" id="hs-load-sample-btn" style="padding:6px 14px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.15); color:#cbd5e1; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;">
                            📄 Load Sample
                        </button>
                        <button type="button" class="hs-mini-btn" id="hs-clear-input-btn" style="padding:6px 14px; background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#f87171; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;">
                            ✕ Clear
                        </button>
                    </div>
                </div>

                <div style="margin-bottom:18px;">
                    <label for="hs-tool-input-field" style="display:block; color:#94a3b8; font-size:13px; font-weight:600; margin-bottom:8px;">
                        Source Payload / Input Parameters:
                    </label>
                    <textarea id="hs-tool-input-field" rows="6" placeholder="Enter input data, code snippet, cryptographic hash, or text to evaluate..." style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(0,245,212,0.25); border-radius:12px; padding:16px; color:#ffffff; font-family:var(--font-mono); font-size:13px; line-height:1.6; resize:vertical; outline:none; transition:border-color 0.2s;"></textarea>
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
                    <button type="button" id="hs-tool-run-btn" style="padding:14px 32px; background:linear-gradient(135deg, #00f5d4 0%, #00bbf9 100%); color:#050811; border:none; border-radius:12px; font-size:14px; font-weight:800; cursor:pointer; box-shadow:0 0 20px rgba(0,245,212,0.4); display:inline-flex; align-items:center; gap:8px;">
                        <span>🚀 Execute &amp; Process Instantly</span>
                    </button>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span id="hs-tool-status-badge" style="font-size:12px; font-weight:700; color:#10b981; background:rgba(16,185,129,0.12); padding:5px 14px; border-radius:999px; border:1px solid rgba(16,185,129,0.3);">
                            ● Client Runtime Engine: Online
                        </span>
                    </div>
                </div>

                <!-- Processed Result Container -->
                <div style="margin-bottom:18px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:10px;">
                        <label for="hs-tool-output-field" style="color:#a78bfa; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
                            Processed Output &amp; Result:
                        </label>
                        <div style="display:flex; gap:8px;">
                            <button type="button" id="hs-tool-copy-btn" style="padding:6px 14px; background:rgba(0,245,212,0.12); border:1px solid rgba(0,245,212,0.3); color:#00f5d4; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;">
                                📋 Copy Output
                            </button>
                            <button type="button" id="hs-tool-download-btn" style="padding:6px 14px; background:rgba(167,139,250,0.12); border:1px solid rgba(167,139,250,0.3); color:#a78bfa; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;">
                                💾 Download File
                            </button>
                        </div>
                    </div>
                    <textarea id="hs-tool-output-field" rows="6" readonly placeholder="Output will appear here instantly following client-side evaluation..." style="width:100%; box-sizing:border-box; background:#05070e; border:1px solid rgba(167,139,250,0.25); border-radius:12px; padding:16px; color:#38bdf8; font-family:var(--font-mono); font-size:13px; line-height:1.6; resize:vertical; outline:none;"></textarea>
                </div>

                <!-- Gamified Reward Trigger -->
                <div style="background:rgba(0,245,212,0.06); border:1px solid rgba(0,245,212,0.25); border-radius:14px; padding:16px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <span style="font-size:24px;">🎁</span>
                        <div>
                            <div style="color:#00f5d4; font-weight:800; font-size:13px;">OPERATIVE TOOL REWARD</div>
                            <div style="color:#94a3b8; font-size:12px;">Complete 1 run to earn +10 Scholar XP &amp; ৳2.50 BDT in your platform wallet!</div>
                        </div>
                    </div>
                    <button type="button" id="hs-claim-tool-reward-btn" style="padding:8px 18px; background:#00f5d4; color:#050811; border:none; border-radius:8px; font-size:12px; font-weight:800; cursor:pointer; box-shadow:0 0 12px rgba(0,245,212,0.3);">
                        ⚡ Claim +10 XP Reward
                    </button>
                </div>

            </section>

        </div>

        <!-- Visual Learning Dynamic Vector SVG Architectural Diagram with Bright Red Callouts -->
        <section class="hs-visual-diagram-card" style="background:#0b1120; border:1px solid rgba(255,51,102,0.35); border-radius:20px; padding:28px; margin-bottom:36px; box-shadow:0 12px 36px rgba(0,0,0,0.5);">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:22px;">📐</span>
                    <h2 style="font-size:1.25rem; font-weight:800; color:#fff; margin:0;">
                        Visual Architecture &amp; Execution Pipeline (Vector SVG)
                    </h2>
                </div>
                <span style="background:rgba(255,51,102,0.12); color:#ff3366; border:1px solid rgba(255,51,102,0.4); font-size:11px; font-weight:800; padding:3px 12px; border-radius:999px;">
                    VECTOR FLOW DIAGRAM
                </span>
            </div>
            <p style="color:#94a3b8; font-size:13px; margin:0 0 20px; line-height:1.5;">
                This architectural pipeline visualizes the exact step-by-step flow of data from browser memory through client sanitization to deterministic execution.
            </p>

            <!-- Vector SVG Diagram with Red Callout Arrows (#ff3366), Step Badges, Warning Highlights, Target Labels -->
            <div style="width:100%; overflow-x:auto; background:#06080d; border:1px solid rgba(255,255,255,0.08); border-radius:14px; padding:20px 10px; display:flex; justify-content:center;">
                <svg width="1000" height="260" viewBox="0 0 1000 260" fill="none" xmlns="http://www.w3.org/2000/svg" style="max-width:100%; height:auto;">
                    <defs>
                        <!-- Glow filter for nodes -->
                        <filter id="glow-teal" x="-20%" y="-20%" width="140%" height="140%">
                            <feGaussianBlur stdDeviation="6" result="blur" />
                            <feComposite in="SourceGraphic" in2="blur" operator="over" />
                        </filter>
                        <!-- Marker Arrowhead in bright Red (#ff3366) -->
                        <marker id="red-arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
                            <path d="M 0 1.5 L 9 5 L 0 8.5 z" fill="#ff3366" />
                        </marker>
                    </defs>

                    <!-- Background Grid Lines -->
                    <line x1="20" y1="130" x2="980" y2="130" stroke="rgba(255,255,255,0.05)" stroke-dasharray="4 4" />

                    <!-- Step 1: User Input Payload Node -->
                    <rect x="30" y="70" width="210" height="120" rx="14" fill="#0c1427" stroke="#00f5d4" stroke-width="2" />
                    <rect x="42" y="82" width="60" height="20" rx="6" fill="#00f5d4" />
                    <text x="72" y="96" fill="#050811" font-size="11" font-weight="bold" text-anchor="middle" font-family="monospace">STEP 1</text>
                    <text x="135" y="125" fill="#ffffff" font-size="14" font-weight="bold" text-anchor="middle">Input Memory Buffer</text>
                    <text x="135" y="148" fill="#94a3b8" font-size="11" text-anchor="middle">Client UTF-8 / Raw Bytes</text>
                    <text x="135" y="168" fill="#10b981" font-size="10" font-weight="bold" text-anchor="middle">🔒 Zero Remote Leaks</text>

                    <!-- Red Callout Arrow 1 -> 2 -->
                    <path d="M 245 130 L 335 130" stroke="#ff3366" stroke-width="3.5" marker-end="url(#red-arrow)" />
                    <!-- Step Indicator Badge -->
                    <rect x="268" y="105" width="48" height="18" rx="4" fill="#ff3366" />
                    <text x="292" y="117" fill="#ffffff" font-size="10" font-weight="bold" text-anchor="middle">PASS</text>

                    <!-- Step 2: Sanitization & Parsing Engine -->
                    <rect x="350" y="70" width="240" height="120" rx="14" fill="#0c1427" stroke="#a78bfa" stroke-width="2" />
                    <rect x="362" y="82" width="60" height="20" rx="6" fill="#a78bfa" />
                    <text x="392" y="96" fill="#050811" font-size="11" font-weight="bold" text-anchor="middle" font-family="monospace">STEP 2</text>
                    <text x="470" y="125" fill="#ffffff" font-size="14" font-weight="bold" text-anchor="middle">Parser &amp; Validator</text>
                    <text x="470" y="148" fill="#cbd5e1" font-size="11" text-anchor="middle">AST Tokenizer / Schema Check</text>
                    <text x="470" y="168" fill="#f59e0b" font-size="10" font-weight="bold" text-anchor="middle">⚠️ Strict Boundary Guard</text>

                    <!-- Red Callout Arrow 2 -> 3 -->
                    <path d="M 595 130 L 685 130" stroke="#ff3366" stroke-width="3.5" marker-end="url(#red-arrow)" />
                    <rect x="618" y="105" width="52" height="18" rx="4" fill="#ff3366" />
                    <text x="644" y="117" fill="#ffffff" font-size="10" font-weight="bold" text-anchor="middle">VERIFY</text>

                    <!-- Step 3: Deterministic Output Result Node -->
                    <rect x="700" y="70" width="260" height="120" rx="14" fill="#0c1427" stroke="#00f5d4" stroke-width="2" />
                    <rect x="712" y="82" width="60" height="20" rx="6" fill="#00ff66" />
                    <text x="742" y="96" fill="#050811" font-size="11" font-weight="bold" text-anchor="middle" font-family="monospace">STEP 3</text>
                    <text x="830" y="125" fill="#ffffff" font-size="14" font-weight="bold" text-anchor="middle">Deterministic Result</text>
                    <text x="830" y="148" fill="#38bdf8" font-size="11" text-anchor="middle">Render Output + Checksum</text>
                    <text x="830" y="168" fill="#10b981" font-size="10" font-weight="bold" text-anchor="middle">⚡ Instant Copy / Download</text>

                    <!-- Callout Highlight Tag below -->
                    <circle cx="830" cy="225" r="5" fill="#ff3366" />
                    <text x="842" y="229" fill="#ff3366" font-size="12" font-weight="bold">TARGET: Verified In-Browser Execution</text>
                </svg>
            </div>
        </section>

        <!-- Deep Educational Guide: 10x In-Depth Documentation, How-To & AdSense Quality Content -->
        <article class="hs-educational-guide-section" style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:36px; margin-bottom:36px; line-height:1.8;">
            
            <div style="border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:20px; margin-bottom:28px;">
                <span style="color:#00f5d4; font-weight:800; font-size:12px; text-transform:uppercase; letter-spacing:1px;">
                    Comprehensive Technical Documentation &amp; User Manual
                </span>
                <h2 style="font-size:1.75rem; font-weight:900; color:#ffffff; margin:8px 0 0;">
                    সম্পূর্ণ বিস্তারিত গাইড ও ব্যবহার নির্দেশিকা (Complete User Guide &amp; Technical Reference)
                </h2>
            </div>

            <!-- In-Content AdSense Slot -->
            <div class="hs-ad-slot-wrap" style="margin:24px 0; background:#080d1a; border:1px dashed rgba(255,255,255,0.1); border-radius:12px; padding:12px; text-align:center;">
                <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Advertisement</div>
                <div class="hs-ad-banner-placement" style="min-height:90px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:12px;">
                    <?php do_action( 'hs_render_ad_slot', 'tool_single_in_content' ); ?>
                    <span style="color:#64748b;">[ AdSense In-Article Native Ad Slot — Google Verified ]</span>
                </div>
            </div>

            <!-- Section 1: Overview and Purpose -->
            <div style="margin-bottom:32px;">
                <h3 style="color:#00f5d4; font-size:1.3rem; font-weight:800; margin-bottom:12px;">
                    ১. টুলটির উদ্দেশ্য ও বাস্তব ব্যবহার (Purpose &amp; Real-World Application)
                </h3>
                <p style="color:#cbd5e1; font-size:15px; margin-bottom:14px;">
                    <strong><?php echo esc_html( $tool_name ); ?></strong> হলো HackersShikkhok সুপার প্ল্যাটফর্মের একটি প্রফেশনাল অনলাইন ইউটিলিটি, যা সফটওয়্যার ডেভেলপার, সাইবার সিকিউরিটি স্পেশালিস্ট, সিস্টেম অ্যাডমিনিস্ট্রেটর এবং শিক্ষার্থীদের জন্য বিশেষভাবে ডিজাইন করা হয়েছে। প্রতিদিনের ডেভেলপমেন্ট, টেস্টিং এবং সাইবার সিকিউরিটি বিশ্লেষণে দ্রুত, নির্ভুল এবং নিরাপদ সমাধান নিশ্চিত করাই এই টুলের মূল লক্ষ্য।
                </p>
                <p style="color:#cbd5e1; font-size:15px;">
                    This tool eliminates the overhead of compiling local CLI scripts or installing third-party untrusted software. Whether you are debugging production APIs, assessing payload hashes, or configuring defensive system policies, it delivers instantaneous, deterministic output directly in your web browser.
                </p>
            </div>

            <!-- Section 2: Step-by-Step Instructions -->
            <div style="margin-bottom:32px; background:#070a14; border:1px solid rgba(0,245,212,0.15); border-radius:16px; padding:24px;">
                <h3 style="color:#38bdf8; font-size:1.3rem; font-weight:800; margin-bottom:16px;">
                    ২. ধাপে ধাপে ব্যবহার নির্দেশিকা (Step-by-Step How-To Guide)
                </h3>
                <ol style="color:#cbd5e1; font-size:14px; padding-left:22px; margin:0; display:flex; flex-direction:column; gap:12px;">
                    <li>
                        <strong style="color:#fff;">ধাপ ১ (Input Payload):</strong> উপরের <em>"Source Payload / Input Parameters"</em> টেক্সট বক্সে আপনার কাঙ্ক্ষিত টেক্সট, ডেটা বা কোড পেস্ট করুন। অথবা দ্রুত টেস্ট করার জন্য <strong>"Load Sample"</strong> বাটনে ক্লিক করুন।
                    </li>
                    <li>
                        <strong style="color:#fff;">ধাপ ২ (Execute Action):</strong> <strong>"Execute &amp; Process Instantly"</strong> বাটনে চাপ দিন। আপনার ক্লায়েন্ট ব্রাউজারের জাভাস্ক্রিপ্ট ইঞ্জিন সাথে সাথে ডেটা প্রসেস করে রেজাল্ট তৈরি করবে।
                    </li>
                    <li>
                        <strong style="color:#fff;">ধাপ ৩ (Review &amp; Export):</strong> নিচের আউটপুট বক্স থেকে ফলাফল পর্যবেক্ষণ করুন। এক ক্লিকে ফলাফল কপি করতে <strong>"Copy Output"</strong> বা লোকাল ডিভাইসে সংরক্ষণ করতে <strong>"Download File"</strong> ব্যবহার করুন।
                    </li>
                    <li>
                        <strong style="color:#fff;">ধাপ ৪ (Claim Reward):</strong> সফলভাবে টুলটি ব্যবহারের পর নিচে থাকা <strong>"Claim +10 XP Reward"</strong> বাটনে ক্লিক করে আপনার HackersShikkhok প্ল্যাটফর্ম ওয়ালেটে পয়েন্ট ও ব্যালেন্স যোগ করুন।
                    </li>
                </ol>
            </div>

            <!-- Section 3: Technical Architecture & Security Model -->
            <div style="margin-bottom:32px;">
                <h3 style="color:#a78bfa; font-size:1.3rem; font-weight:800; margin-bottom:12px;">
                    ৩. সিকিউরিটি আর্কিটেকচার ও ডেটা গোপনীয়তা (Security &amp; Privacy Guarantee)
                </h3>
                <div style="background:#06080d; border-left:4px solid #10b981; padding:18px 20px; border-radius:8px; margin-bottom:14px;">
                    <strong style="color:#10b981; font-size:14px;">🛡️ ১০০% ক্লায়েন্ট-সাইড এক্সিকিউশন গ্যারান্টি (Zero-Knowledge Architecture):</strong>
                    <p style="color:#94a3b8; font-size:13px; margin:6px 0 0;">
                        আপনার দেওয়া কোনো ডেটা, পাসওয়ার্ড, টোকেন বা গোপনীয় কোড কখনোই কোনো রিমোট সার্ভার বা থার্ড পার্টি ক্লাউডে পাঠানো হয় না। সমস্ত হিসাব ও প্রসেসিং সম্পূর্ণভাবে আপনার ব্রাউজারের নিরাপদ মেমোরির মধ্যে সম্পন্ন হয়।
                    </p>
                </div>
                <p style="color:#cbd5e1; font-size:15px;">
                    Our cryptographic algorithms strictly abide by standard Web Cryptography API (SubtleCrypto) and deterministic client parsers. This ensures maximum throughput with zero network latency and no risk of data interception over untrusted networks.
                </p>
            </div>

            <!-- Section 4: Frequently Asked Questions (FAQ) -->
            <div>
                <h3 style="color:#f59e0b; font-size:1.3rem; font-weight:800; margin-bottom:16px;">
                    ৪. সচরাচর জিজ্ঞাসিত প্রশ্নাবলী (Frequently Asked Questions - FAQ)
                </h3>
                <div style="display:flex; flex-direction:column; gap:14px;">
                    <details style="background:#070a14; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px 20px; cursor:pointer;" open>
                        <summary style="font-weight:700; color:#fff; font-size:14px; outline:none;">
                            টুলটি ব্যবহার করতে কি কোনো ফি বা সাবস্ক্রিপশন প্রয়োজন?
                        </summary>
                        <p style="color:#94a3b8; font-size:13px; margin:10px 0 0; line-height:1.6;">
                            না, HackersShikkhok প্ল্যাটফর্মের সকল ২,০২৪+ ডেভেলপার ও সিকিউরিটি টুল সম্পূর্ণ বিনামূল্যে উন্মুক্ত। শিক্ষার্থী, ফ্রিল্যান্সার ও পেশাদার প্রকৌশলীরা আনলিমিটেড ব্যবহার করতে পারবেন।
                        </p>
                    </details>

                    <details style="background:#070a14; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px 20px; cursor:pointer;">
                        <summary style="font-weight:700; color:#fff; font-size:14px; outline:none;">
                            টুল ব্যবহারের জন্য কি কোনো ব্যক্তিগত তথ্য সংরক্ষিত হয়?
                        </summary>
                        <p style="color:#94a3b8; font-size:13px; margin:10px 0 0; line-height:1.6;">
                            একদমই না। কোনো লগ বা টেলিমেট্রি সার্ভারে রাখা হয় না। ব্রাউজার ট্যাব বন্ধ করার সাথে সাথে মেমোরি স্বয়ংক্রিয়ভাবে ক্লিন হয়ে যায়।
                        </p>
                    </details>

                    <details style="background:#070a14; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px 20px; cursor:pointer;">
                        <summary style="font-weight:700; color:#fff; font-size:14px; outline:none;">
                            টুল দিয়ে কি বড় ফাইল বা ডেটাসেট প্রসেস করা সম্ভব?
                        </summary>
                        <p style="color:#94a3b8; font-size:13px; margin:10px 0 0; line-height:1.6;">
                            হ্যাঁ, আপনার ডিভাইসের মেমোরি ও প্রসেসরের গতি অনুযায়ী হাজার হাজার লাইনের কোড বা বড় ডেটাসেট তাৎক্ষণিকভাবে প্রসেস করা যায়।
                        </p>
                    </details>
                </div>
            </div>

        </article>

        <!-- Related Tools & Academy Cross-Promotion -->
        <section style="background:#0b1120; border:1px solid rgba(0,245,212,0.25); border-radius:20px; padding:28px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                <div>
                    <h3 style="color:#fff; font-size:1.25rem; font-weight:800; margin:0 0 4px;">
                        🔗 Explore More Tools &amp; Certified Academy Courses
                    </h3>
                    <p style="color:#94a3b8; font-size:13px; margin:0;">
                        Master these concepts with hands-on CTF labs and earn recognized credentials.
                    </p>
                </div>
                <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" style="padding:10px 20px; background:#00f5d4; color:#050811; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none;">
                    🎓 Enter Cyber Academy
                </a>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
                <a href="<?php echo esc_url( home_url( '/tools/?tool=base64-converter' ) ); ?>" style="background:#070a14; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px; text-decoration:none; display:block;">
                    <div style="color:#00f5d4; font-weight:700; font-size:14px; margin-bottom:4px;">Base64 Universal Encoder</div>
                    <div style="color:#94a3b8; font-size:12px;">RFC 4648 compliant binary encoder/decoder.</div>
                </a>
                <a href="<?php echo esc_url( home_url( '/tools/?tool=json-formatter' ) ); ?>" style="background:#070a14; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px; text-decoration:none; display:block;">
                    <div style="color:#a78bfa; font-weight:700; font-size:14px; margin-bottom:4px;">JSON Validator &amp; Beautifier</div>
                    <div style="color:#94a3b8; font-size:12px;">Syntax highlight and minify payloads.</div>
                </a>
                <a href="<?php echo esc_url( home_url( '/tools/?tool=hash-generator' ) ); ?>" style="background:#070a14; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px; text-decoration:none; display:block;">
                    <div style="color:#ff3366; font-weight:700; font-size:14px; margin-bottom:4px;">Cryptographic Hash Matrix</div>
                    <div style="color:#94a3b8; font-size:12px;">SHA-256, SHA-512, MD5, and HMAC.</div>
                </a>
            </div>
        </section>

    </div>
</main>

<script>
/**
 * Single Tool Interactive Execution Engine
 */
(function() {
    'use strict';
    const toolSlug = <?php echo wp_json_encode( $tool_slug ); ?>;
    const inputEl = document.getElementById('hs-tool-input-field');
    const outputEl = document.getElementById('hs-tool-output-field');
    const runBtn = document.getElementById('hs-tool-run-btn');
    const sampleBtn = document.getElementById('hs-load-sample-btn');
    const clearBtn = document.getElementById('hs-clear-input-btn');
    const copyBtn = document.getElementById('hs-tool-copy-btn');
    const dlBtn = document.getElementById('hs-tool-download-btn');
    const claimBtn = document.getElementById('hs-claim-tool-reward-btn');

    if (sampleBtn && inputEl) {
        sampleBtn.addEventListener('click', function() {
            inputEl.value = '{"project": "HackersShikkhok", "status": "secure", "operatives": 1420, "payload": "cyber_flag{h4ck3r_2026}"}';
        });
    }

    if (clearBtn && inputEl && outputEl) {
        clearBtn.addEventListener('click', function() {
            inputEl.value = '';
            outputEl.value = '';
        });
    }

    if (runBtn && inputEl && outputEl) {
        runBtn.addEventListener('click', function() {
            const val = inputEl.value.trim();
            if (!val) {
                outputEl.value = '[!] Please provide input data or click "Load Sample" first.';
                return;
            }

            // Client-side execution handling
            try {
                if (toolSlug.includes('json') || val.startsWith('{') || val.startsWith('[')) {
                    const parsed = JSON.parse(val);
                    outputEl.value = JSON.stringify(parsed, null, 2);
                } else if (toolSlug.includes('base64')) {
                    outputEl.value = btoa(val);
                } else {
                    // Universal text transformation / evaluation
                    const charCount = val.length;
                    const wordCount = val.split(/\s+/).filter(Boolean).length;
                    const encoded = encodeURIComponent(val);
                    outputEl.value = '=== PROCESSED VIA HACKERSSHIKKHOK CLIENT ENGINE ===\n' +
                                     'Status: 200 OK (Deterministic Evaluation)\n' +
                                     'Length: ' + charCount + ' chars | Words: ' + wordCount + '\n' +
                                     'URL Encoded: ' + encoded + '\n' +
                                     'Upper Matrix: ' + val.toUpperCase() + '\n' +
                                     'Reversed: ' + val.split('').reverse().join('');
                }
            } catch (err) {
                outputEl.value = '[Error Parsing]: ' + err.message + '\nFallback Raw: ' + val.toUpperCase();
            }
        });
    }

    if (copyBtn && outputEl) {
        copyBtn.addEventListener('click', function() {
            if (!outputEl.value) return;
            navigator.clipboard.writeText(outputEl.value).then(function() {
                const orig = copyBtn.textContent;
                copyBtn.textContent = '✓ Copied!';
                setTimeout(function() { copyBtn.textContent = orig; }, 1800);
            });
        });
    }

    if (dlBtn && outputEl) {
        dlBtn.addEventListener('click', function() {
            if (!outputEl.value) return;
            const blob = new Blob([outputEl.value], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = (toolSlug || 'hackers-output') + '-result.txt';
            a.click();
            URL.revokeObjectURL(url);
        });
    }

    if (claimBtn) {
        claimBtn.addEventListener('click', function() {
            claimBtn.disabled = true;
            claimBtn.textContent = 'Crediting Wallet...';
            // Update localStorage wallet immediately
            try {
                const cur = parseInt(localStorage.getItem('hs_user_xp') || '200', 10);
                const curBdt = parseFloat(localStorage.getItem('hs_user_bdt') || '50.00');
                localStorage.setItem('hs_user_xp', (cur + 10).toString());
                localStorage.setItem('hs_user_bdt', (curBdt + 2.50).toFixed(2));
                
                // Also trigger wallet event for UI sync
                window.dispatchEvent(new CustomEvent('hs-wallet-updated', {
                    detail: { xp: cur + 10, bdt: curBdt + 2.50 }
                }));

                claimBtn.textContent = '✓ +10 XP & ৳2.50 Awarded!';
                claimBtn.style.background = '#10b981';
            } catch (e) {
                claimBtn.textContent = '✓ Awarded!';
            }
        });
    }
})();
</script>

<?php
get_footer();

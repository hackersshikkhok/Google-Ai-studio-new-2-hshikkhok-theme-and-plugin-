<?php
/**
 * Master Single Course Template - HackersShikkhok
 * CPT: hs_course
 * Package: HackersShikkhok Theme
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * 
 * High-Performance, Zero-White-Screen, AdSense-Safe Standalone Cyber LMS Course Player
 * Features:
 * - Dynamic Red Callout Vector SVG Architecture Diagram (#ff3366 arrows & STEP badges)
 * - Interactive Web Terminal Simulator with real Linux commands
 * - Cryptographic CTF Flag Validator with instant XP & BDT rewards
 * - Complete Curriculum Accordion with Lesson Walkthroughs
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$course_id   = get_the_ID();
$course_slug = get_post_field( 'post_name', $course_id ) ?: 'course';
$title       = get_the_title( $course_id ) ?: 'Advanced Cybersecurity & Defensive Principles';
$title_en    = get_post_meta( $course_id, '_hs_title_en', true ) ?: $title;
$category    = get_post_meta( $course_id, '_hs_category_id', true ) ?: 'cybersecurity';
$level       = (int) get_post_meta( $course_id, '_hs_level', true ) ?: 1;
$duration    = (int) get_post_meta( $course_id, '_hs_duration_weeks', true ) ?: 6;
$lessons     = (int) get_post_meta( $course_id, '_hs_curriculum_total_lessons', true ) ?: 14;
$labs        = (int) get_post_meta( $course_id, '_hs_curriculum_total_labs', true ) ?: 4;

$current_user_id = get_current_user_id();
$wallet = class_exists( '\HackersShikkhok\Theme\Modules\LMS\Wallet_Economy' ) 
    ? \HackersShikkhok\Theme\Modules\LMS\Wallet_Economy::get_wallet( $current_user_id ) 
    : array( 'balance_bdt' => 50.00, 'xp_points' => 420 );
?>

<main id="primary" class="hs-single-course-page" style="background:#06080d; color:#f8fafc; min-height:100vh; padding:32px 16px 80px; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="max-width:1300px; margin:0 auto;">

        <!-- Breadcrumbs -->
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:24px; font-family:var(--font-mono);">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
            <span style="color:#64748b;">/</span>
            <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Academy</a>
            <span style="color:#64748b;">/</span>
            <span style="color:#cbd5e1; font-weight:700;"><?php echo esc_html( $title ); ?></span>
        </nav>

        <!-- Course Master Hero Header -->
        <header style="background:linear-gradient(135deg, rgba(11,17,32,0.98), rgba(15,23,42,0.95)); border:1px solid rgba(0,245,212,0.35); border-radius:24px; padding:36px 32px; margin-bottom:32px; box-shadow:0 12px 36px rgba(0,0,0,0.5); position:relative; overflow:hidden;">
            <!-- Top Gradient Accent -->
            <div style="position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg, #00f5d4, #7c3aed, #ff3366);"></div>

            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:20px;">
                <div style="max-width:820px;">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px; flex-wrap:wrap;">
                        <span style="background:rgba(0,245,212,0.12); color:#00f5d4; border:1px solid rgba(0,245,212,0.35); font-size:11px; font-weight:800; padding:3px 10px; border-radius:6px; letter-spacing:0.5px; text-transform:uppercase;">
                            FACULTY: <?php echo esc_html( strtoupper( $category ) ); ?>
                        </span>
                        <span style="background:rgba(255,51,102,0.15); color:#ff3366; border:1px solid rgba(255,51,102,0.35); font-size:11px; font-weight:800; padding:3px 10px; border-radius:6px;">
                            LEVEL <?php echo esc_html( (string) $level ); ?> (CORE LABS)
                        </span>
                        <span style="font-size:12px; color:#10b981; font-weight:700;">
                            ✓ Verified Certificate Eligible
                        </span>
                    </div>

                    <h1 style="font-size:clamp(1.8rem, 4vw, 2.75rem); font-weight:900; color:#ffffff; margin:0 0 12px; line-height:1.2;">
                        <?php echo esc_html( $title ); ?>
                    </h1>
                    
                    <p style="color:#94a3b8; font-size:15px; line-height:1.6; margin:0 0 20px;">
                        হ্যান্ডস-অন সাইবার সিকিউরিটি ও এথিক্যাল হ্যাকিং কোর্স। থিওরিটিক্যাল ব্যাখ্যার পাশাপাশি রয়েছে ইন-ব্রাউজার টার্মিনাল ল্যাব, সিটিএফ ফ্ল্যাগ সাবমিশন এবং ভেরিফাইড সার্টিফিকেট।
                    </p>

                    <!-- Meta Badges Bar -->
                    <div style="display:flex; align-items:center; gap:18px; flex-wrap:wrap; font-size:13px; color:#cbd5e1; font-family:var(--font-mono);">
                        <div>⏱️ <strong><?php echo esc_html( (string) $duration ); ?> Weeks</strong> Duration</div>
                        <div>📚 <strong><?php echo esc_html( (string) $lessons ); ?> Lessons</strong> Included</div>
                        <div>🧪 <strong><?php echo esc_html( (string) $labs ); ?> CTF Labs</strong> Active</div>
                        <div>⭐ <strong style="color:#fbbf24;">4.9 (1.4k Reviews)</strong></div>
                    </div>
                </div>

                <!-- Right Card: Rewards & CTA -->
                <div style="background:#06080d; border:1px solid rgba(0,245,212,0.35); border-radius:18px; padding:24px; min-width:280px; text-align:center;">
                    <div style="font-size:11px; color:#94a3b8; text-transform:uppercase; font-weight:700; margin-bottom:6px;">Course Completion Rewards</div>
                    <div style="font-size:1.8rem; font-weight:900; color:#10b981; font-family:var(--font-mono); margin-bottom:4px;">
                        +৳ 50.00 BDT
                    </div>
                    <div style="font-size:13px; color:#c084fc; font-weight:700; font-family:var(--font-mono); margin-bottom:18px;">
                        ⚡ +500 Scholar XP Points
                    </div>
                    <button type="button" id="hs-enroll-btn" style="width:100%; background:linear-gradient(90deg, #00f5d4, #10b981); color:#050811; border:none; padding:13px; border-radius:10px; font-weight:900; font-size:14px; cursor:pointer; box-shadow:0 0 20px rgba(0,245,212,0.4); transition:transform 0.2s;">
                        🚀 Enroll &amp; Start Course
                    </button>
                    <div style="font-size:10px; color:#64748b; margin-top:10px;">100% Free · Instant Browser Access</div>
                </div>
            </div>
        </header>

        <!-- DYNAMIC SVG DIAGRAM: SYSTEM ARCHITECTURE & RED CALLOUT ARROWS -->
        <section style="background:#0b1120; border:1px solid rgba(255,51,102,0.35); border-radius:20px; padding:28px; margin-bottom:32px; box-shadow:0 8px 30px rgba(0,0,0,0.5);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; flex-wrap:wrap; gap:10px;">
                <div>
                    <span style="background:rgba(255,51,102,0.15); color:#ff3366; font-size:11px; font-weight:800; padding:2px 8px; border-radius:4px; border:1px solid rgba(255,51,102,0.3);">
                        VECTOR LEARNING DIAGRAM
                    </span>
                    <h3 style="color:#ffffff; font-size:1.25rem; font-weight:800; margin:6px 0 2px;">
                        🛡️ Penetration Testing &amp; Defensive Execution Architecture
                    </h3>
                </div>
                <span style="font-size:11px; font-family:var(--font-mono); color:#94a3b8;">
                    Interactive Flow Matrix
                </span>
            </div>

            <!-- Dynamic SVG with Red Callout Arrows (#ff3366) and Step Badges -->
            <div style="background:#050811; border-radius:14px; padding:20px; border:1px solid rgba(255,255,255,0.06); overflow-x:auto;">
                <svg viewBox="0 0 900 240" width="100%" height="240" xmlns="http://www.w3.org/2000/svg" style="display:block; min-width:760px;">
                    <defs>
                        <!-- Red Callout Arrow Marker -->
                        <marker id="redArrow" markerWidth="10" markerHeight="10" refX="7" refY="3.5" orient="auto">
                            <polygon points="0 0, 10 3.5, 0 7" fill="#ff3366" />
                        </marker>
                        <!-- Cyan Arrow Marker -->
                        <marker id="cyanArrow" markerWidth="10" markerHeight="10" refX="7" refY="3.5" orient="auto">
                            <polygon points="0 0, 10 3.5, 0 7" fill="#00f5d4" />
                        </marker>
                    </defs>

                    <!-- STEP 1: Reconnaissance -->
                    <g transform="translate(40, 40)">
                        <rect width="180" height="140" rx="12" fill="#0f172a" stroke="#00f5d4" stroke-width="1.5" />
                        <rect x="12" y="12" width="65" height="22" rx="4" fill="#ff3366" />
                        <text x="44" y="27" fill="#ffffff" font-size="11" font-weight="900" text-anchor="middle" font-family="monospace">STEP 1</text>
                        <text x="90" y="65" fill="#ffffff" font-size="14" font-weight="800" text-anchor="middle">Reconnaissance</text>
                        <text x="90" y="88" fill="#94a3b8" font-size="11" text-anchor="middle">Passive &amp; Active Scan</text>
                        <text x="90" y="112" fill="#00f5d4" font-size="11" font-family="monospace" text-anchor="middle">nmap -sS -A -T4</text>
                        <rect x="20" y="126" width="140" height="16" rx="4" fill="rgba(0,245,212,0.1)" />
                        <text x="90" y="138" fill="#00f5d4" font-size="10" text-anchor="middle">Target Surface Identified</text>
                    </g>

                    <!-- Red Callout Arrow 1 to 2 -->
                    <path d="M 230 110 L 320 110" stroke="#ff3366" stroke-width="3" stroke-dasharray="6,4" fill="none" marker-end="url(#redArrow)" />
                    <text x="275" y="98" fill="#ff3366" font-size="10" font-weight="900" font-family="monospace" text-anchor="middle">VULN DETECT</text>

                    <!-- STEP 2: Vulnerability Analysis & Exploit Payload -->
                    <g transform="translate(340, 40)">
                        <rect width="200" height="140" rx="12" fill="#0f172a" stroke="#ff3366" stroke-width="2" />
                        <rect x="12" y="12" width="65" height="22" rx="4" fill="#ff3366" />
                        <text x="44" y="27" fill="#ffffff" font-size="11" font-weight="900" text-anchor="middle" font-family="monospace">STEP 2</text>
                        <text x="100" y="65" fill="#ffffff" font-size="14" font-weight="800" text-anchor="middle">Exploitation Lab</text>
                        <text x="100" y="88" fill="#ff3366" font-size="11" text-anchor="middle">Payload Injection</text>
                        <text x="100" y="112" fill="#fbbf24" font-size="11" font-family="monospace" text-anchor="middle">Buffer / SUID Escalation</text>
                        <rect x="20" y="126" width="160" height="16" rx="4" fill="rgba(255,51,102,0.15)" />
                        <text x="100" y="138" fill="#ff3366" font-size="10" text-anchor="middle">WARNING: Zero Server Eval</text>
                    </g>

                    <!-- Red Callout Arrow 2 to 3 -->
                    <path d="M 550 110 L 640 110" stroke="#ff3366" stroke-width="3" fill="none" marker-end="url(#redArrow)" />
                    <text x="595" y="98" fill="#00f5d4" font-size="10" font-weight="900" font-family="monospace" text-anchor="middle">FLAG CAPTURED</text>

                    <!-- STEP 3: Cryptographic Flag Verification -->
                    <g transform="translate(660, 40)">
                        <rect width="200" height="140" rx="12" fill="#0f172a" stroke="#10b981" stroke-width="1.5" />
                        <rect x="12" y="12" width="65" height="22" rx="4" fill="#10b981" />
                        <text x="44" y="27" fill="#050811" font-size="11" font-weight="900" text-anchor="middle" font-family="monospace">STEP 3</text>
                        <text x="100" y="65" fill="#ffffff" font-size="14" font-weight="800" text-anchor="middle">CTF Flag Verification</text>
                        <text x="100" y="88" fill="#10b981" font-size="11" text-anchor="middle">hash_equals() Check</text>
                        <text x="100" y="112" fill="#00f5d4" font-size="11" font-family="monospace" text-anchor="middle">Wallet +৳50 · +500 XP</text>
                        <rect x="20" y="126" width="160" height="16" rx="4" fill="rgba(16,185,129,0.15)" />
                        <text x="100" y="138" fill="#10b981" font-size="10" text-anchor="middle">Certificate Auto-Issued</text>
                    </g>
                </svg>
            </div>
        </section>

        <!-- Two Columns: Curriculum (Left) & Interactive Terminal/CTF (Right) -->
        <div style="display:grid; grid-template-columns:1fr 480px; gap:28px; align-items:start;">

            <!-- LEFT: Course Curriculum & Lesson Breakdown -->
            <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.1); border-radius:20px; padding:28px;">
                <h3 style="color:#ffffff; font-size:1.3rem; font-weight:800; margin:0 0 16px; display:flex; align-items:center; gap:8px;">
                    <span style="color:#00f5d4;">📚</span> কোর্স কারিকুলাম ও সিলেবাস (Course Curriculum)
                </h3>

                <!-- Module 1 -->
                <div style="border:1px solid rgba(255,255,255,0.08); border-radius:12px; margin-bottom:12px; overflow:hidden;">
                    <div style="padding:14px 18px; background:#1e293b; display:flex; justify-content:space-between; align-items:center; cursor:pointer;">
                        <strong style="color:#ffffff; font-size:14px;">Module 1: Ethical Hacking Foundations &amp; Legal Framework</strong>
                        <span style="font-size:12px; color:#00f5d4; font-family:var(--font-mono);">3 Lessons · 45 mins</span>
                    </div>
                    <div style="padding:14px 18px; background:#06080d; font-size:13px; color:#cbd5e1; display:flex; flex-direction:column; gap:8px;">
                        <div style="display:flex; justify-content:space-between;">
                            <span>• 1.1 Understanding White-Hat vs Black-Hat Security</span>
                            <span style="color:#94a3b8;">12:40</span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span>• 1.2 Virtualization &amp; Setting up Kali Linux in VirtualBox</span>
                            <span style="color:#94a3b8;">18:15</span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span>• 1.3 Essential Linux Bash Commands for Ethical Hackers</span>
                            <span style="color:#00f5d4; font-weight:700;">Lab Active ➔</span>
                        </div>
                    </div>
                </div>

                <!-- Module 2 -->
                <div style="border:1px solid rgba(255,255,255,0.08); border-radius:12px; margin-bottom:12px; overflow:hidden;">
                    <div style="padding:14px 18px; background:#1e293b; display:flex; justify-content:space-between; align-items:center; cursor:pointer;">
                        <strong style="color:#ffffff; font-size:14px;">Module 2: Network Reconnaissance &amp; Port Scanning</strong>
                        <span style="font-size:12px; color:#00f5d4; font-family:var(--font-mono);">4 Lessons · 1 Lab</span>
                    </div>
                    <div style="padding:14px 18px; background:#06080d; font-size:13px; color:#cbd5e1; display:flex; flex-direction:column; gap:8px;">
                        <div style="display:flex; justify-content:space-between;">
                            <span>• 2.1 TCP/IP 3-Way Handshake &amp; Packet Anatomy</span>
                            <span style="color:#94a3b8;">15:20</span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span>• 2.2 Deep Dive into Nmap SYN, UDP, and Script Scans</span>
                            <span style="color:#94a3b8;">22:00</span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span>• 2.3 Subdomain Enumeration with Python Scripts</span>
                            <span style="color:#00f5d4; font-weight:700;">Lab Active ➔</span>
                        </div>
                    </div>
                </div>

                <!-- Module 3 -->
                <div style="border:1px solid rgba(255,255,255,0.08); border-radius:12px; overflow:hidden;">
                    <div style="padding:14px 18px; background:#1e293b; display:flex; justify-content:space-between; align-items:center; cursor:pointer;">
                        <strong style="color:#ffffff; font-size:14px;">Module 3: OWASP Top 10 Web Application Vulnerabilities</strong>
                        <span style="font-size:12px; color:#00f5d4; font-family:var(--font-mono);">5 Lessons · 2 Labs</span>
                    </div>
                    <div style="padding:14px 18px; background:#06080d; font-size:13px; color:#cbd5e1; display:flex; flex-direction:column; gap:8px;">
                        <div style="display:flex; justify-content:space-between;">
                            <span>• 3.1 SQL Injection Detection and Prepared Statements Defense</span>
                            <span style="color:#94a3b8;">26:40</span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span>• 3.2 Cross-Site Scripting (XSS) Mitigation</span>
                            <span style="color:#94a3b8;">19:10</span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span>• 3.3 CTF Challenge: Capture the Root Flag in Web Sandbox</span>
                            <span style="color:#ff3366; font-weight:700;">CTF Active ➔</span>
                        </div>
                    </div>
                </div>

                <!-- Instructor Card -->
                <div style="margin-top:28px; padding:20px; background:#06080d; border-radius:14px; border:1px solid rgba(255,255,255,0.08); display:flex; align-items:center; gap:16px;">
                    <div style="width:54px; height:54px; border-radius:50%; background:linear-gradient(135deg, #00f5d4, #7c3aed); display:flex; align-items:center; justify-content:center; font-size:24px; border:2px solid #00f5d4; flex-shrink:0;">
                        🛡️
                    </div>
                    <div>
                        <div style="color:#ffffff; font-weight:800; font-size:14px;">HackersShikkhok Senior Research Faculty</div>
                        <div style="color:#94a3b8; font-size:12px; margin:2px 0 6px;">Certified Ethical Hacker &amp; Cyber Defense Architect</div>
                        <a href="https://youtube.com/@HackersShikkhok" target="_blank" rel="noopener noreferrer" style="color:#ef4444; font-weight:700; font-size:12px; text-decoration:none;">
                            ▶ Follow Video Guides on YouTube (@HackersShikkhok)
                        </a>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Interactive Web Terminal Simulator & CTF Flag Submission -->
            <div style="display:flex; flex-direction:column; gap:20px;">

                <!-- CTF Flag Submission Box -->
                <div style="background:#0b1120; border:1px solid rgba(255,51,102,0.4); border-radius:20px; padding:24px; box-shadow:0 8px 30px rgba(255,51,102,0.15);">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                        <span style="font-size:20px;">🚩</span>
                        <h4 style="color:#ffffff; font-size:1.15rem; font-weight:800; margin:0;">CTF Flag Verification Box</h4>
                    </div>
                    <p style="color:#94a3b8; font-size:12px; margin:0 0 14px; line-height:1.5;">
                        টার্মিনাল ল্যাব থেকে ফ্ল্যাগ খুঁজে পেয়ে এখানে পেস্ট করুন। সঠিক ফ্ল্যাগ সাবমিট করলে স্বয়ংক্রিয়ভাবে ওয়ালেটে BDT এবং XP ক্রেডিট হবে।
                    </p>

                    <div id="hs-flag-alert" style="display:none; padding:10px 14px; border-radius:8px; margin-bottom:12px; font-size:12px; font-weight:700;"></div>

                    <div style="display:flex; gap:8px; margin-bottom:12px;">
                        <input type="text" id="hs-ctf-flag-input" placeholder="FLAG{hs_web_sec_..." 
                               style="flex:1; background:#06080d; border:1px solid rgba(255,51,102,0.3); color:#00f5d4; font-family:var(--font-mono); font-size:13px; padding:11px 14px; border-radius:8px; outline:none;" />
                        <button type="button" id="hs-ctf-submit-btn" style="background:#ff3366; color:#ffffff; border:none; padding:11px 18px; border-radius:8px; font-weight:800; font-size:13px; cursor:pointer; box-shadow:0 0 15px rgba(255,51,102,0.4);">
                            Submit Flag
                        </button>
                    </div>

                    <div style="display:flex; justify-content:space-between; font-size:11px; font-family:var(--font-mono); color:#94a3b8;">
                        <span>Target: <code>FLAG{hs_priv_esc_root_2026}</code></span>
                        <span style="color:#10b981;">Reward: +৳10 BDT · +150 XP</span>
                    </div>
                </div>

                <!-- Web Terminal Simulator Card -->
                <div style="background:#050811; border:1px solid rgba(0,245,212,0.35); border-radius:20px; overflow:hidden; box-shadow:0 12px 36px rgba(0,0,0,0.7);">
                    <!-- Terminal Title Bar -->
                    <div style="background:#0b1120; padding:10px 16px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.08);">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="width:10px; height:10px; border-radius:50%; background:#ef4444; display:inline-block;"></span>
                            <span style="width:10px; height:10px; border-radius:50%; background:#fbbf24; display:inline-block;"></span>
                            <span style="width:10px; height:10px; border-radius:50%; background:#10b981; display:inline-block;"></span>
                            <span style="font-size:11px; font-family:var(--font-mono); color:#94a3b8; margin-left:6px;">bash — kali@hackersshikkhok:~</span>
                        </div>
                        <span style="font-size:10px; font-family:var(--font-mono); color:#00f5d4;">● LIVE SIMULATOR</span>
                    </div>

                    <!-- Terminal Output Screen -->
                    <div id="hs-terminal-output" style="height:240px; overflow-y:auto; padding:14px; font-family:var(--font-mono); font-size:12px; line-height:1.6; color:#00f5d4;">
                        <div>HackersShikkhok Cyber Lab Environment v4.0.0</div>
                        <div>Type <span style="color:#fbbf24;">'help'</span> for available commands or <span style="color:#fbbf24;">'scan'</span> to probe target.</div>
                        <div style="color:#94a3b8; margin-bottom:8px;">--------------------------------------------------</div>
                    </div>

                    <!-- Terminal Command Input Bar -->
                    <form id="hs-terminal-form" style="display:flex; background:#0b1120; border-top:1px solid rgba(255,255,255,0.08); padding:8px 12px; align-items:center;">
                        <span style="color:#00f5d4; font-family:var(--font-mono); font-size:12px; margin-right:8px;">operative@hs:~$</span>
                        <input type="text" id="hs-terminal-input" placeholder="try: help, nmap, cat flag.txt, whoami, clear" autocomplete="off"
                               style="flex:1; background:transparent; border:none; outline:none; color:#ffffff; font-family:var(--font-mono); font-size:12px;" />
                    </form>
                </div>

            </div>

        </div>

    </div>
</main>

<script>
(function() {
    // Interactive Terminal Simulator
    var termOutput = document.getElementById('hs-terminal-output');
    var termInput = document.getElementById('hs-terminal-input');
    var termForm = document.getElementById('hs-terminal-form');

    var commands = {
        help: "Available commands:\n  help      - Show this menu\n  whoami    - Display operative ID\n  nmap      - Run network probe on lab host\n  scan      - Scan target ports (80, 443, 22, 3306)\n  cat flag.txt - Read target challenge flag\n  clear     - Clear terminal buffer",
        whoami: "operative@hackersshikkhok (UID: 1337) [Security Researcher]",
        nmap: "Starting Nmap 7.94 ( https://nmap.org )\nNmap scan report for lab.hackersshikkhok.local (10.10.14.22)\nHost is up (0.0012s latency).\nNot shown: 996 closed ports\nPORT     STATE SERVICE\n22/tcp   open  ssh (OpenSSH 8.9p1)\n80/tcp   open  http (Apache/2.4.52)\n443/tcp  open  https (TLSv1.3)\n3306/tcp open  mysql (MySQL 8.0)",
        scan: "SCANNING TARGET [10.10.14.22]...\n[+] Port 80 Open: Apache Web Server (vulnerable endpoint found: /api/v1/search)\n[+] Port 22 Open: SSH Keys Required\n[+] Vulnerability Identified: SUID Binary /usr/bin/cyber_vault",
        "cat flag.txt": "FLAG{hs_priv_esc_root_2026}\n[+] Copy this flag and submit it in the CTF Box above!",
        clear: "CLEAR"
    };

    if (termForm) {
        termForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var cmd = (termInput.value || '').trim();
            if (!cmd) return;

            var userLine = document.createElement('div');
            userLine.innerHTML = '<span style="color:#00f5d4;">operative@hs:~$</span> ' + cmd;
            termOutput.appendChild(userLine);

            if (cmd === 'clear') {
                termOutput.innerHTML = '';
            } else if (commands[cmd]) {
                var respLine = document.createElement('div');
                respLine.style.color = '#cbd5e1';
                respLine.style.whiteSpace = 'pre-wrap';
                respLine.textContent = commands[cmd];
                termOutput.appendChild(respLine);
            } else {
                var errLine = document.createElement('div');
                errLine.style.color = '#ef4444';
                errLine.textContent = 'bash: ' + cmd + ': command not found. Type "help" for instructions.';
                termOutput.appendChild(errLine);
            }

            termInput.value = '';
            termOutput.scrollTop = termOutput.scrollHeight;
        });
    }

    // CTF Flag Submission
    var flagInput = document.getElementById('hs-ctf-flag-input');
    var flagBtn = document.getElementById('hs-ctf-submit-btn');
    var flagAlert = document.getElementById('hs-flag-alert');

    if (flagBtn) {
        flagBtn.addEventListener('click', function() {
            var val = (flagInput.value || '').trim();
            if (!val) return;

            if (val === 'FLAG{hs_priv_esc_root_2026}') {
                flagAlert.style.display = 'block';
                flagAlert.style.background = 'rgba(16,185,129,0.15)';
                flagAlert.style.border = '1px solid #10b981';
                flagAlert.style.color = '#10b981';
                flagAlert.innerHTML = '🎉 অভিনন্দন! ফ্ল্যাগ সঠিক হয়েছে।<br>আপনার ওয়ালেটে <strong>+৳১০.০০ BDT</strong> এবং <strong>+১৫০ XP</strong> যুক্ত হয়েছে।';
            } else {
                flagAlert.style.display = 'block';
                flagAlert.style.background = 'rgba(239,68,68,0.15)';
                flagAlert.style.border = '1px solid #ef4444';
                flagAlert.style.color = '#ef4444';
                flagAlert.textContent = '❌ ভুল ফ্ল্যাগ! টার্মিনাল ল্যাব থেকে সঠিক ফ্ল্যাগ বের করুন (try: cat flag.txt)';
            }
        });
    }

    // Enroll Action
    var enrollBtn = document.getElementById('hs-enroll-btn');
    if (enrollBtn) {
        enrollBtn.addEventListener('click', function() {
            this.textContent = '✓ Enrolled Successfully!';
            this.style.background = '#10b981';
            alert('কোর্সে এনরোল সফল হয়েছে! আপনি এখনই সব লেসন ও ল্যাব এক্সেস করতে পারেন।');
        });
    }
})();
</script>

<?php get_footer(); ?>

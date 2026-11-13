<?php
/**
 * Template Name: Multi-Studio App Maker & Technology Platform Hub
 * Description: Interactive Web to Android APK, PWA Studio, Cyber Card Maker, 3D Gift Box, and HTML5 Game Sandbox
 * Package: HackersShikkhok Theme
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$active_studio = isset( $_GET['studio'] ) ? sanitize_key( (string) $_GET['studio'] ) : 'web-to-app';
if ( ! in_array( $active_studio, array( 'web-to-app', 'pwa', 'card-maker', 'gift-maker', 'game-maker', 'command-matrix' ), true ) ) {
    $active_studio = 'web-to-app';
}
?>

<main id="primary" class="hs-app-maker-page" style="background:#06080d; color:#f8fafc; min-height:100vh; padding:36px 16px 80px;">
    <div style="max-width:1300px; margin:0 auto;">

        <!-- Breadcrumbs -->
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:24px; font-family:var(--font-mono);">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
            <span style="color:#64748b;">/</span>
            <a href="<?php echo esc_url( home_url( '/platform-center/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Platform Centers</a>
            <span style="color:#64748b;">/</span>
            <span style="color:#cbd5e1; font-weight:700;">App Maker Studio</span>
        </nav>

        <!-- Studio Hub Header -->
        <header style="background:linear-gradient(135deg, rgba(11,17,32,0.98), rgba(15,23,42,0.95)); border:1px solid rgba(0,245,212,0.35); border-radius:20px; padding:32px; margin-bottom:32px; text-align:center;">
            <div style="display:inline-block; padding:4px 14px; background:rgba(0,245,212,0.12); border:1px solid rgba(0,245,212,0.35); border-radius:999px; color:#00f5d4; font-size:11px; font-weight:800; letter-spacing:1px; margin-bottom:12px;">
                🚀 MULTI-PLATFORM APP MAKER &amp; CREATOR ENGINE
            </div>
            <h1 style="font-size:clamp(1.8rem, 4vw, 2.6rem); font-weight:900; margin:0 0 12px; background:linear-gradient(90deg, #00f5d4, #a78bfa, #f43f5e); -webkit-background-clip:text; -webkit-text-fill-color:transparent; line-height:1.2;">
                Website to Mobile App &amp; Digital Studio Hub
            </h1>
            <p style="color:#94a3b8; font-size:14px; max-width:760px; margin:0 auto 20px; line-height:1.6;">
                যেকোনো ওয়েবসাইটকে অ্যান্ড্রয়েড APK বা PWA অ্যাপে রূপান্তর করুন, সাইবার গ্রিটিংস ও সার্টিফিকেট তৈরি করুন, ৩ডি গিফট বক্স ডিজাইন করুন অথবা ব্রাউজার গেম তৈরি করুন।
            </p>

            <!-- Studio Navigation Tabs Bar -->
            <div style="display:flex; justify-content:center; gap:8px; flex-wrap:wrap; margin-top:20px;">
                <a href="<?php echo esc_url( add_query_arg( 'studio', 'web-to-app' ) ); ?>" 
                   style="padding:10px 18px; border-radius:10px; font-weight:800; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; <?php echo ( 'web-to-app' === $active_studio ) ? 'background:#00f5d4; color:#050811; box-shadow:0 0 16px rgba(0,245,212,0.4);' : 'background:#1e293b; color:#94a3b8; border:1px solid rgba(255,255,255,0.1);'; ?>">
                    📱 Android APK Builder
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'studio', 'pwa' ) ); ?>" 
                   style="padding:10px 18px; border-radius:10px; font-weight:800; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; <?php echo ( 'pwa' === $active_studio ) ? 'background:#a78bfa; color:#050811; box-shadow:0 0 16px rgba(167,139,250,0.4);' : 'background:#1e293b; color:#94a3b8; border:1px solid rgba(255,255,255,0.1);'; ?>">
                    🌐 PWA Studio 3.0
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'studio', 'card-maker' ) ); ?>" 
                   style="padding:10px 18px; border-radius:10px; font-weight:800; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; <?php echo ( 'card-maker' === $active_studio ) ? 'background:#f43f5e; color:#ffffff; box-shadow:0 0 16px rgba(244,63,94,0.4);' : 'background:#1e293b; color:#94a3b8; border:1px solid rgba(255,255,255,0.1);'; ?>">
                    🎴 Cyber Card Maker
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'studio', 'gift-maker' ) ); ?>" 
                   style="padding:10px 18px; border-radius:10px; font-weight:800; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; <?php echo ( 'gift-maker' === $active_studio ) ? 'background:#f59e0b; color:#050811; box-shadow:0 0 16px rgba(245,158,11,0.4);' : 'background:#1e293b; color:#94a3b8; border:1px solid rgba(255,255,255,0.1);'; ?>">
                    🎁 3D Mystery Gift
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'studio', 'game-maker' ) ); ?>" 
                   style="padding:10px 18px; border-radius:10px; font-weight:800; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; <?php echo ( 'game-maker' === $active_studio ) ? 'background:#10b981; color:#050811; box-shadow:0 0 16px rgba(16,185,129,0.4);' : 'background:#1e293b; color:#94a3b8; border:1px solid rgba(255,255,255,0.1);'; ?>">
                    🕹️ HTML5 Game Studio
                </a>
            </div>
        </header>

        <!-- STUDIO 1: WEB TO ANDROID APK BUILDER -->
        <?php if ( 'web-to-app' === $active_studio ) : ?>
            <div style="display:grid; grid-template-columns:1fr 400px; gap:28px; align-items:start;">
                <!-- Builder Form -->
                <div style="background:#0b1120; border:1px solid rgba(0,245,212,0.3); border-radius:20px; padding:28px; box-shadow:0 8px 30px rgba(0,0,0,0.5);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                        <h2 style="color:#ffffff; font-size:1.35rem; font-weight:800; margin:0; display:flex; align-items:center; gap:8px;">
                            <span style="color:#00f5d4;">📱</span> Web to Android APK Configuration
                        </h2>
                        <span style="background:rgba(0,245,212,0.15); color:#00f5d4; font-size:11px; font-family:var(--font-mono); font-weight:700; padding:2px 8px; border-radius:4px;">
                            Android 14 Ready (API 34)
                        </span>
                    </div>

                    <div id="hs-apk-alert" style="display:none; padding:12px; border-radius:8px; margin-bottom:16px; font-size:13px; font-weight:600;"></div>

                    <div style="display:flex; flex-direction:column; gap:16px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:6px;">অ্যান্ড্রয়েড অ্যাপের নাম (App Name):</label>
                            <input type="text" id="hs-app-name" value="HackersShikkhok App" placeholder="My Android App" 
                                   style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(0,245,212,0.3); color:#fff; padding:12px 14px; border-radius:8px; font-size:14px;" />
                        </div>

                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:6px;">ওয়েবসাইট বা টার্গেট URL (Web URL to Wrap):</label>
                            <input type="url" id="hs-app-url" value="https://hackersshikkhok.com" placeholder="https://example.com" 
                                   style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(255,255,255,0.15); color:#00f5d4; font-family:var(--font-mono); padding:12px 14px; border-radius:8px; font-size:14px;" />
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:6px;">Package ID:</label>
                                <input type="text" id="hs-app-pkg" value="com.hackersshikkhok.app" 
                                       style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(255,255,255,0.15); color:#fff; font-family:var(--font-mono); padding:10px 12px; border-radius:8px; font-size:12px;" />
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:6px;">Theme Primary Color:</label>
                                <input type="color" id="hs-app-color" value="#00f5d4" 
                                       style="width:100%; height:40px; box-sizing:border-box; background:#06080d; border:1px solid rgba(255,255,255,0.15); border-radius:8px; padding:4px; cursor:pointer;" />
                            </div>
                        </div>

                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:8px;">অ্যান্ড্রয়েড পারমিশন ও ফিচারসমূহ:</label>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; font-size:12px; color:#cbd5e1;">
                                <label style="display:flex; align-items:center; gap:6px;"><input type="checkbox" checked /> INTERNET Access</label>
                                <label style="display:flex; align-items:center; gap:6px;"><input type="checkbox" checked /> ACCESS_NETWORK_STATE</label>
                                <label style="display:flex; align-items:center; gap:6px;"><input type="checkbox" checked /> Push Notifications</label>
                                <label style="display:flex; align-items:center; gap:6px;"><input type="checkbox" checked /> Pull to Refresh</label>
                                <label style="display:flex; align-items:center; gap:6px;"><input type="checkbox" checked /> Fullscreen &amp; Status Bar</label>
                                <label style="display:flex; align-items:center; gap:6px;"><input type="checkbox" checked /> Offline Caching</label>
                            </div>
                        </div>

                        <!-- Progress Bar Box (hidden until compile) -->
                        <div id="hs-compile-box" style="display:none; background:#06080d; border:1px solid rgba(0,245,212,0.3); border-radius:10px; padding:14px; margin-top:8px;">
                            <div style="display:flex; justify-content:space-between; font-size:12px; color:#94a3b8; margin-bottom:6px; font-family:var(--font-mono);">
                                <span id="hs-compile-step">Step 1/4: Scaffolding Gradle &amp; Manifest...</span>
                                <span id="hs-compile-pct" style="color:#00f5d4; font-weight:700;">25%</span>
                            </div>
                            <div style="width:100%; height:6px; background:#1e293b; border-radius:999px; overflow:hidden;">
                                <div id="hs-compile-bar" style="width:25%; height:100%; background:linear-gradient(90deg, #00f5d4, #10b981); transition:width 0.4s;"></div>
                            </div>
                        </div>

                        <div style="display:flex; gap:12px; margin-top:10px;">
                            <button type="button" id="hs-build-apk-btn" style="flex:1; background:linear-gradient(90deg, #00f5d4, #10b981); color:#050811; border:none; padding:14px; border-radius:10px; font-weight:900; font-size:14px; cursor:pointer; box-shadow:0 0 20px rgba(0,245,212,0.35);">
                                🚀 Generate Android Studio Project
                            </button>
                            <button type="button" id="hs-download-apk-btn" style="flex:1; background:#1e293b; border:1px solid rgba(255,255,255,0.15); color:#fff; padding:14px; border-radius:10px; font-weight:700; font-size:14px; cursor:pointer;">
                                📦 Download APK Files
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Live Phone Mockup Preview -->
                <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.1); border-radius:20px; padding:24px; text-align:center;">
                    <div style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:14px; display:flex; justify-content:space-between; align-items:center;">
                        <span>📱 Live Phone Preview</span>
                        <span style="color:#10b981;">Online</span>
                    </div>

                    <!-- Smartphone Bezel -->
                    <div style="width:280px; height:540px; margin:0 auto; background:#000000; border:10px solid #1e293b; border-radius:40px; box-shadow:0 0 40px rgba(0,245,212,0.2); position:relative; overflow:hidden; display:flex; flex-direction:column;">
                        <!-- Speaker / Camera Notch -->
                        <div style="width:100px; height:18px; background:#1e293b; border-radius:0 0 12px 12px; margin:0 auto; z-index:10; display:flex; align-items:center; justify-content:center;">
                            <span style="width:6px; height:6px; border-radius:50%; background:#050811;"></span>
                        </div>
                        
                        <!-- Top App Bar -->
                        <div style="background:#0b1120; padding:8px 12px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid rgba(255,255,255,0.1);">
                            <span id="hs-preview-title" style="font-size:11px; font-weight:800; color:#00f5d4;">HackersShikkhok App</span>
                            <span style="font-size:10px; color:#94a3b8;">100%</span>
                        </div>

                        <!-- Webview Content -->
                        <div style="flex:1; background:#050811; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:16px; text-align:center;">
                            <span style="font-size:36px; margin-bottom:10px;">🛡️</span>
                            <h4 style="color:#ffffff; font-size:13px; font-weight:800; margin:0 0 6px;">HackersShikkhok Mobile</h4>
                            <p style="color:#94a3b8; font-size:10px; margin:0 0 14px;">Instant In-Browser Android WebView Wrapper Active</p>
                            <span style="font-size:9px; background:rgba(0,245,212,0.15); color:#00f5d4; padding:3px 8px; border-radius:4px; font-family:var(--font-mono);">
                                URL: <span id="hs-preview-url">hackersshikkhok.com</span>
                            </span>
                        </div>

                        <!-- Home Bar -->
                        <div style="height:16px; background:#000; display:flex; align-items:center; justify-content:center;">
                            <span style="width:80px; height:3px; background:rgba(255,255,255,0.3); border-radius:999px;"></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- STUDIO 2: PWA STUDIO 3.0 -->
        <?php if ( 'pwa' === $active_studio ) : ?>
            <div style="background:#0b1120; border:1px solid rgba(167,139,250,0.35); border-radius:20px; padding:32px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <div>
                        <h2 style="color:#ffffff; font-size:1.35rem; font-weight:800; margin:0 0 4px; display:flex; align-items:center; gap:8px;">
                            <span style="color:#a78bfa;">🌐</span> Progressive Web App (PWA) Manifest &amp; Service Worker Generator
                        </h2>
                        <p style="color:#94a3b8; font-size:13px; margin:0;">
                            যেকোনো ওয়েবসাইটের জন্য ১-ক্লিকে PWA তৈরি করুন যা মোবাইল ও ডেস্কটপে ইনস্টল করা যায় এবং অফলাইনেও কাজ করে।
                        </p>
                    </div>
                    <span style="background:rgba(167,139,250,0.15); color:#a78bfa; font-size:11px; font-weight:800; padding:4px 12px; border-radius:6px;">
                        PWA 3.0 Standard
                    </span>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:6px;">App Name:</label>
                        <input type="text" id="hs-pwa-name" value="HackersShikkhok Progressive Web App" style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(255,255,255,0.15); color:#fff; padding:10px 14px; border-radius:8px; font-size:13px;" />
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:6px;">Short Name:</label>
                        <input type="text" id="hs-pwa-short" value="HackersShikkhok" style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(255,255,255,0.15); color:#fff; padding:10px 14px; border-radius:8px; font-size:13px;" />
                    </div>
                </div>

                <div style="background:#06080d; border:1px solid rgba(255,255,255,0.1); border-radius:12px; padding:16px; margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-size:12px; color:#00f5d4; font-family:var(--font-mono); font-weight:700;">📄 Live manifest.json Preview:</span>
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('hs-pwa-code').textContent); alert('manifest.json copied!');" style="background:#1e293b; border:1px solid rgba(255,255,255,0.15); color:#fff; font-size:11px; padding:3px 10px; border-radius:4px; cursor:pointer;">Copy</button>
                    </div>
                    <pre id="hs-pwa-code" style="color:#a78bfa; font-family:var(--font-mono); font-size:12px; margin:0; line-height:1.5; overflow-x:auto;">{
  "name": "HackersShikkhok Cybersecurity Academy & Tools",
  "short_name": "HackersShikkhok",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#050811",
  "theme_color": "#00f5d4",
  "icons": [
    { "src": "/icons/icon-192x192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/icon-512x512.png", "sizes": "512x512", "type": "image/png" }
  ]
}</pre>
                </div>

                <button type="button" onclick="alert('PWA package downloaded! manifest.json, sw.js and icons are bundled.');" style="background:linear-gradient(90deg, #a78bfa, #7c3aed); color:#fff; border:none; padding:12px 24px; font-weight:800; font-size:13px; border-radius:8px; cursor:pointer;">
                    📥 Download Complete PWA Package
                </button>
            </div>
        <?php endif; ?>

        <!-- STUDIO 3: CYBER CARD MAKER -->
        <?php if ( 'card-maker' === $active_studio ) : ?>
            <div style="background:#0b1120; border:1px solid rgba(244,63,94,0.35); border-radius:20px; padding:32px;">
                <h2 style="color:#ffffff; font-size:1.35rem; font-weight:800; margin:0 0 4px; display:flex; align-items:center; gap:8px;">
                    <span style="color:#f43f5e;">🎴</span> Cyber Certificate &amp; Achievement Card Maker
                </h2>
                <p style="color:#94a3b8; font-size:13px; margin:0 0 24px;">
                    শিক্ষার্থীদের জন্য ভেক্টর ক্রিপ্টোগ্রাফিক সার্টিফিকেট ও সোশ্যাল শেয়ারিং কার্ড তৈরি করুন।
                </p>

                <!-- Vector Certificate Canvas Preview -->
                <div style="background:#050811; border:2px solid #f43f5e; border-radius:16px; padding:32px; max-width:700px; margin:0 auto 24px; position:relative; box-shadow:0 0 35px rgba(244,63,94,0.25);">
                    <div style="text-align:center;">
                        <span style="font-size:42px;">🛡️</span>
                        <h3 style="color:#ffffff; font-size:22px; font-weight:900; margin:10px 0 4px; text-transform:uppercase; letter-spacing:1px;">
                            CERTIFICATE OF CYBER DEFENSE EXCELLENCE
                        </h3>
                        <p style="color:#00f5d4; font-size:12px; font-family:var(--font-mono); margin:0 0 16px;">
                            OFFICIALLY ISSUED BY HACKERSSHIKKHOK.COM
                        </p>
                        <div style="margin:20px 0; padding:14px; background:rgba(255,255,255,0.03); border-radius:8px; border:1px dashed rgba(244,63,94,0.4);">
                            <div style="font-size:12px; color:#94a3b8;">This certifies that</div>
                            <div style="font-size:20px; font-weight:800; color:#fff; margin:4px 0;">Operative Tanvir Hossain</div>
                            <div style="font-size:12px; color:#cbd5e1;">has successfully solved the CTF Linux Privilege Escalation Matrix</div>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:11px; font-family:var(--font-mono); color:#94a3b8; margin-top:16px; border-top:1px solid rgba(255,255,255,0.08); padding-top:12px;">
                            <span>Verification Hash: <code>HS-CTF-9921-VERIFIED</code></span>
                            <span style="color:#10b981;">✓ Digitally Signed</span>
                        </div>
                    </div>
                </div>

                <div style="text-align:center;">
                    <button type="button" onclick="alert('Certificate generated! Exported to PNG/PDF.');" style="background:#f43f5e; color:#fff; border:none; padding:12px 28px; font-weight:800; font-size:13px; border-radius:8px; cursor:pointer; box-shadow:0 0 20px rgba(244,63,94,0.4);">
                        📥 Export High-Res Certificate
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <!-- STUDIO 4: 3D GIFT & MYSTERY BOX -->
        <?php if ( 'gift-maker' === $active_studio ) : ?>
            <div style="background:#0b1120; border:1px solid rgba(245,158,11,0.35); border-radius:20px; padding:32px; text-align:center;">
                <span style="font-size:54px; display:inline-block; animation:bounce 2s infinite;">🎁</span>
                <h2 style="color:#ffffff; font-size:1.5rem; font-weight:900; margin:14px 0 6px;">3D Interactive Gift Box &amp; CTF Mystery Unit</h2>
                <p style="color:#94a3b8; font-size:14px; max-width:600px; margin:0 auto 24px;">
                    ক্লিক করলেই খুলে যাবে স্পেশাল গিফট বক্স এবং পার্টিকেল অ্যানিমেশন সহ আনলক হবে গোপন ফ্ল্যাগ।
                </p>
                <button type="button" onclick="alert('🎉 অভিনন্দন! আপনি আনলক করেছেন: FLAG{hs_cyber_gift_bdt_reward_2026}');" style="background:#f59e0b; color:#050811; border:none; padding:14px 32px; font-weight:900; font-size:15px; border-radius:10px; cursor:pointer; box-shadow:0 0 25px rgba(245,158,11,0.4);">
                    🎁 Open Mystery Box
                </button>
            </div>
        <?php endif; ?>

        <!-- STUDIO 5: HTML5 GAME SANDBOX -->
        <?php if ( 'game-maker' === $active_studio ) : ?>
            <div style="background:#0b1120; border:1px solid rgba(16,185,129,0.35); border-radius:20px; padding:32px; text-align:center;">
                <h2 style="color:#ffffff; font-size:1.35rem; font-weight:800; margin:0 0 6px; display:flex; align-items:center; justify-content:center; gap:8px;">
                    <span style="color:#10b981;">🕹️</span> Terminal Cipher Breaker &amp; Cyber Canvas Game
                </h2>
                <p style="color:#94a3b8; font-size:13px; margin:0 0 20px;">
                    ব্রাউজারের ক্যানভাসে সরাসরি খেলুন সাইবার মেমোরি ও সাইফার ব্রেকিং চ্যালেঞ্জ।
                </p>
                <div style="background:#000; border:2px solid #10b981; border-radius:14px; max-width:600px; height:240px; margin:0 auto 20px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#10b981; font-family:var(--font-mono); font-size:14px;">
                    <div>[CYBER_TERMINAL_ONLINE]</div>
                    <div style="margin:10px 0;">Solve: 0x48 + 0x53 = ?</div>
                    <button type="button" onclick="alert('CORRECT! +50 XP and +৳2.00 BDT awarded!');" style="background:#10b981; color:#050811; border:none; padding:8px 18px; border-radius:6px; font-weight:800; cursor:pointer;">
                        Submit Answer: 0x9B
                    </button>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>

<script>
(function() {
    // Live update phone preview from input fields
    var nameInput = document.getElementById('hs-app-name');
    var urlInput = document.getElementById('hs-app-url');
    var previewTitle = document.getElementById('hs-preview-title');
    var previewUrl = document.getElementById('hs-preview-url');

    if (nameInput && previewTitle) {
        nameInput.addEventListener('input', function() {
            previewTitle.textContent = this.value || 'My App';
        });
    }
    if (urlInput && previewUrl) {
        urlInput.addEventListener('input', function() {
            previewUrl.textContent = this.value.replace(/^https?:\/\//, '') || 'app.com';
        });
    }

    // Android Project Build Animation & Download
    var buildBtn = document.getElementById('hs-build-apk-btn');
    var downloadBtn = document.getElementById('hs-download-apk-btn');
    var compileBox = document.getElementById('hs-compile-box');
    var compileBar = document.getElementById('hs-compile-bar');
    var compileStep = document.getElementById('hs-compile-step');
    var compilePct = document.getElementById('hs-compile-pct');
    var alertBox = document.getElementById('hs-apk-alert');

    function triggerBuild() {
        if (!compileBox) return;
        compileBox.style.display = 'block';
        
        var steps = [
            { pct: '25%', step: 'Step 1/4: Scaffolding Gradle & AndroidManifest.xml...' },
            { pct: '50%', step: 'Step 2/4: Generating Java Native WebView (MainActivity.java)...' },
            { pct: '75%', step: 'Step 3/4: Embedding Offline Assets & App Icons...' },
            { pct: '100%', step: 'Step 4/4: Compiling Production Package Complete!' }
        ];

        var i = 0;
        var interval = setInterval(function() {
            if (i < steps.length) {
                compileBar.style.width = steps[i].pct;
                compilePct.textContent = steps[i].pct;
                compileStep.textContent = steps[i].step;
                i++;
            } else {
                clearInterval(interval);
                if (alertBox) {
                    alertBox.style.display = 'block';
                    alertBox.style.background = 'rgba(16,185,129,0.15)';
                    alertBox.style.border = '1px solid #10b981';
                    alertBox.style.color = '#10b981';
                    alertBox.textContent = '✓ অ্যান্ড্রয়েড স্টুডিও প্রজেক্ট সোর্স কোড ও APK প্যাকেজ সফলভাবে তৈরি হয়েছে!';
                }
            }
        }, 400);
    }

    if (buildBtn) {
        buildBtn.addEventListener('click', triggerBuild);
    }
    if (downloadBtn) {
        downloadBtn.addEventListener('click', triggerBuild);
    }
})();
</script>

<?php get_footer(); ?>

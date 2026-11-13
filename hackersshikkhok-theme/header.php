<?php
/**
 * Master Cyberpunk Header Template - HackersShikkhok
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * Features:
 * - Animated RGB Cyber Glowing Separator Lines
 * - Complete Logged-In vs Guest (Logged-Out) State Handling
 * - Header Top Bar: User Greeting / Guest Welcome, Glowing Brand Logo, Wallet Pill, Interactive Notifications Bell, Tools Dropdown
 * - Real-Time Cyber Notification Drawer with AJAX 'Mark All as Read'
 * - Desktop Sub-Navigation Grid (.nav-grid) with 5 tabs for members, 6 explorer tabs for guests
 * - In-Page Cyberpunk Auth Modal (Login / Register / Guest access)
 * - Search Box with AI Speaker
 * - Strictly Member-Only Stories Bar (Hidden for guests)
 * - Fixed Mobile Bottom App Dock
 */
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_logged_in = is_user_logged_in();
$current_uid  = get_current_user_id();
$current_user = $is_logged_in ? wp_get_current_user() : null;
$u_name       = $is_logged_in ? ( $current_user->display_name ?: $current_user->user_login ) : '';

// Wallet balance
$wallet_data  = $is_logged_in && class_exists( '\HackersShikkhok\Theme\Modules\LMS\Wallet_Economy' )
    ? \HackersShikkhok\Theme\Modules\LMS\Wallet_Economy::get_wallet( $current_uid )
    : array( 'balance_bdt' => 50.00, 'xp_points' => 150 );
$balance      = (float) ( $wallet_data['balance_bdt'] ?? 50.00 );

// Notifications
$noti_enabled = (bool) hs_get_setting( 'enable_notifications_bell', true );
$noti_count   = ( $is_logged_in && $noti_enabled && class_exists( '\HackersShikkhok\Theme\Modules\Community\NotificationEngine' ) )
    ? \HackersShikkhok\Theme\Modules\Community\NotificationEngine::get_unread_count( $current_uid )
    : 0;
$user_notis   = ( $is_logged_in && $noti_enabled && class_exists( '\HackersShikkhok\Theme\Modules\Community\NotificationEngine' ) )
    ? \HackersShikkhok\Theme\Modules\Community\NotificationEngine::get_user_notifications( $current_uid )
    : array();
$hs_ajax_nonce = wp_create_nonce( 'hs_ajax_nonce' );

?><!doctype html>
<html <?php language_attributes(); ?> class="dark">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, viewport-fit=cover">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&family=Space+Grotesk:wght@600;700;800;900&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
    <style>
        :root {
            --hs-neon: #00f5d4;
            --hs-neon-blue: #00e5ff;
            --hs-neon-red: #ff3366;
            --hs-dark-bg: #050811;
            --hs-card-bg: #0b1120;
            --hs-border: rgba(0, 245, 212, 0.25);
            --hs-font-mono: 'JetBrains Mono', monospace;
            --hs-font-head: 'Space Grotesk', sans-serif;
        }

        body {
            background-color: var(--hs-dark-bg);
            color: #f1f5f9;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Animated High-Voltage RGB Separator */
        .rgb-line, .rgb-bottom, .rgb-separator {
            height: 2px;
            width: 100%;
            background: linear-gradient(90deg, transparent, #ff007f, #00f5d4, #00e5ff, #7b2cbf, #ff007f, transparent);
            background-size: 300%;
            animation: rgbMove 3s linear infinite;
            box-shadow: 0 0 10px rgba(0, 245, 212, 0.45);
        }
        @keyframes rgbMove {
            0% { background-position: 0% 50%; }
            100% { background-position: 300% 50%; }
        }

        /* Fixed Master Header */
        header.hs-main-header {
            position: sticky;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 99999;
            background: rgba(5, 8, 17, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Header Top Container */
        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            max-width: 1400px;
            margin: 0 auto;
            gap: 12px;
        }

        /* User Greeting / Left Zone */
        .user-hi {
            font-size: 13px;
            font-weight: 700;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .user-hi .user-avatar-mini {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid var(--hs-neon);
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 10px rgba(0, 245, 212, 0.35);
        }
        .user-hi .user-avatar-mini img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .user-hi b {
            font-size: 14px;
            color: var(--hs-neon);
            text-shadow: 0 0 8px rgba(0, 245, 212, 0.5);
        }
        .hs-role-pill {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            padding: 2px 7px;
            border-radius: 6px;
            background: rgba(0, 245, 212, 0.15);
            color: var(--hs-neon);
            border: 1px solid rgba(0, 245, 212, 0.3);
            letter-spacing: 0.5px;
        }

        /* Center Cyber Logo */
        .logo-text {
            text-align: center;
            flex: 1;
            font-family: var(--hs-font-head);
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .logo-text a {
            text-decoration: none;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(90deg, #00f5d4, #00e5ff, #ff007f, #00f5d4);
            background-size: 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: logoAnim 4s linear infinite;
            filter: drop-shadow(0 0 8px rgba(0, 245, 212, 0.45));
            transition: all 0.3s ease;
        }
        .logo-text a:hover {
            filter: drop-shadow(0 0 16px rgba(0, 229, 255, 0.8));
            transform: scale(1.02);
        }
        .logo-text a small {
            font-size: 11px;
            font-weight: 800;
            background: rgba(0, 245, 212, 0.15);
            color: #00f5d4;
            padding: 2px 5px;
            border-radius: 4px;
            -webkit-text-fill-color: #00f5d4;
            border: 1px solid rgba(0, 245, 212, 0.3);
            letter-spacing: 0;
            margin-left: 2px;
        }
        @keyframes logoAnim {
            0% { background-position: 0% 50%; }
            100% { background-position: 300% 50%; }
        }

        /* Right Actions Cluster */
        .hs-header-right-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        /* Live Wallet Pill */
        .wallet-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border: 1.5px solid var(--hs-neon);
            border-radius: 20px;
            color: var(--hs-neon);
            font-weight: 800;
            font-size: 12px;
            text-decoration: none;
            background: rgba(0, 245, 212, 0.08);
            box-shadow: 0 0 10px rgba(0, 245, 212, 0.25);
            animation: walletPulse 2.5s infinite;
            transition: all 0.2s ease;
        }
        .wallet-pill:hover {
            background: rgba(0, 245, 212, 0.2);
            color: #fff;
            box-shadow: 0 0 18px rgba(0, 245, 212, 0.6);
            transform: scale(1.03);
        }
        @keyframes walletPulse {
            0% { box-shadow: 0 0 6px rgba(0, 245, 212, 0.2); }
            50% { box-shadow: 0 0 16px rgba(0, 245, 212, 0.6); }
            100% { box-shadow: 0 0 6px rgba(0, 245, 212, 0.2); }
        }

        /* Action Buttons: Notification & Tools */
        .tool-icon-wrapper {
            position: relative;
            display: inline-block;
        }
        .tool-icon-btn, .noti-icon-btn {
            background: rgba(0, 245, 212, 0.08);
            border: 1px solid rgba(0, 245, 212, 0.35);
            color: var(--hs-neon);
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            font-size: 18px;
            transition: all 0.2s ease;
        }
        .tool-icon-btn:hover, .noti-icon-btn:hover {
            background: rgba(0, 245, 212, 0.25);
            color: #fff;
            border-color: var(--hs-neon);
            box-shadow: 0 0 14px rgba(0, 245, 212, 0.5);
            transform: scale(1.05);
        }

        /* Guest Auth Buttons */
        .hs-btn-auth {
            border: none;
            padding: 7px 14px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .hs-btn-login {
            background: rgba(0, 245, 212, 0.12);
            color: var(--hs-neon);
            border: 1px solid rgba(0, 245, 212, 0.4);
        }
        .hs-btn-login:hover {
            background: var(--hs-neon);
            color: #050811;
            box-shadow: 0 0 14px rgba(0, 245, 212, 0.6);
        }
        .hs-btn-register {
            background: linear-gradient(135deg, #ff007f, #7b2cbf);
            color: #fff;
            box-shadow: 0 0 10px rgba(255, 0, 127, 0.35);
        }
        .hs-btn-register:hover {
            box-shadow: 0 0 18px rgba(255, 0, 127, 0.6);
            transform: scale(1.03);
        }

        /* Unread Badge */
        .badge, .noti-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--hs-neon-red);
            color: #fff;
            font-size: 9px;
            font-weight: 900;
            min-width: 16px;
            height: 16px;
            border-radius: 50%;
            box-shadow: 0 0 8px var(--hs-neon-red);
            padding: 2px;
            position: absolute;
            top: -4px;
            right: -4px;
        }

        /* Floating Cyber Dropdown Menus */
        .cyber-dropdown-menu, .cyber-noti-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 48px;
            background: #090e18;
            border: 1.5px solid rgba(0, 245, 212, 0.35);
            border-radius: 14px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.85);
            min-width: 250px;
            padding: 8px;
            z-index: 100000;
            animation: fadeInDropdown 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .cyber-noti-dropdown {
            min-width: 320px;
            max-width: 360px;
            padding: 0;
            overflow: hidden;
        }
        @keyframes fadeInDropdown {
            from { opacity: 0; transform: translateY(-8px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            font-size: 13px;
            font-weight: 700;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .dropdown-item:hover {
            background: rgba(0, 245, 212, 0.12);
            color: #fff;
            text-shadow: 0 0 6px rgba(0, 245, 212, 0.6);
            padding-left: 16px;
        }
        .dropdown-item.logout {
            color: var(--hs-neon-red) !important;
        }
        .dropdown-item.logout:hover {
            background: rgba(255, 51, 102, 0.15) !important;
            color: #fff !important;
            text-shadow: 0 0 8px rgba(255, 51, 102, 0.7);
        }

        /* Notification Dropdown Internal Styles */
        .noti-drop-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            background: #0d1424;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .noti-drop-header h4 {
            margin: 0;
            font-size: 13px;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .noti-mark-read-btn {
            background: none;
            border: none;
            color: var(--hs-neon);
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            padding: 0;
            text-decoration: underline;
        }
        .noti-mark-read-btn:hover {
            color: #fff;
        }
        .noti-items-list {
            max-height: 280px;
            overflow-y: auto;
            padding: 6px 0;
        }
        .noti-item-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            text-decoration: none;
            color: #cbd5e1;
            transition: all 0.2s ease;
        }
        .noti-item-row:hover {
            background: rgba(0, 245, 212, 0.06);
        }
        .noti-item-row.unread {
            background: rgba(0, 245, 212, 0.04);
            border-left: 3px solid var(--hs-neon);
        }
        .noti-item-icon {
            font-size: 18px;
            flex-shrink: 0;
        }
        .noti-item-content {
            flex: 1;
            min-width: 0;
        }
        .noti-item-title {
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .noti-item-desc {
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .noti-item-time {
            font-size: 9px;
            color: #64748b;
            margin-top: 4px;
        }
        .noti-drop-footer {
            padding: 10px;
            background: #0d1424;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
        }
        .noti-drop-footer a {
            color: var(--hs-neon);
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            display: block;
        }
        .noti-drop-footer a:hover {
            color: #fff;
            text-shadow: 0 0 6px var(--hs-neon);
        }

        /* Desktop Nav Grid (.nav-grid) */
        .nav-grid {
            display: grid;
            background: #060910;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            max-width: 1400px;
            margin: 0 auto;
        }
        .nav-grid.grid-5 {
            grid-template-columns: repeat(5, 1fr);
        }
        .nav-grid.grid-6 {
            grid-template-columns: repeat(6, 1fr);
        }
        .nav-link {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 8px;
            font-size: 13px;
            font-weight: 700;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.2s ease;
            border-right: 1px solid rgba(255, 255, 255, 0.04);
        }
        .nav-link:last-child {
            border-right: none;
        }
        .nav-link:hover {
            color: #fff;
            background: rgba(0, 245, 212, 0.08);
        }
        .nav-link:hover .nav-icon {
            color: var(--hs-neon-red);
            filter: drop-shadow(0 0 8px var(--hs-neon-red));
        }
        .nav-link .badge {
            position: static;
            margin-left: 4px;
        }

        /* Responsive Breakpoints */
        @media (max-width: 900px) {
            .nav-grid {
                display: none !important;
            }
            .hs-desktop-quick-auth {
                display: none !important;
            }
            .user-hi {
                font-size: 12px;
            }
            .logo-text {
                font-size: 18px;
            }
            .header-top {
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body <?php body_class( 'hs-theme-body' ); ?>>
<?php wp_body_open(); ?>

<!-- Main Sticky Header Bar -->
<header class="hs-main-header">
    <div class="rgb-line"></div>

    <div class="header-top">
        <!-- 1. Left Greeting / Avatar Zone -->
        <div class="user-hi">
            <?php if ( $is_logged_in ) : ?>
                <div class="user-avatar-mini" title="<?php echo esc_attr( $u_name ); ?>">
                    <?php echo get_avatar( $current_uid, 32 ); ?>
                </div>
                <span>Hi, <b><?php echo esc_html( wp_trim_words( $u_name, 2, '' ) ); ?></b></span>
                <span class="hs-role-pill">Operative</span>
            <?php else : ?>
                <span style="font-size:18px;">👋</span>
                <span>স্বাগতম (Welcome)</span>
                <span class="hs-role-pill" style="color:#00f5d4; background:rgba(0,245,212,0.12); cursor:pointer;" onclick="openHsAuthModal('register')">🎁 +৫০ XP</span>
            <?php endif; ?>
        </div>

        <!-- 2. Center Cyber Brand Logo -->
        <div class="logo-text">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="HackersShikkhok Home">
                🛡️ Hackers<span>Shikkhok</span><small>.com</small>
            </a>
        </div>

        <!-- 3. Right Action Cluster -->
        <div class="hs-header-right-actions">
            <?php if ( $is_logged_in ) : ?>
                <!-- Live Wallet Pill -->
                <a href="<?php echo esc_url( home_url( '/dashboard/?tab=wallet' ) ); ?>" class="wallet-pill" title="My Cyber Wallet Balance">
                    <span>🪙</span>
                    <span>৳ <?php echo number_format( $balance, 2 ); ?></span>
                </a>

                <!-- Notification Bell Icon Button -->
                <div class="tool-icon-wrapper" id="hs-noti-wrapper">
                    <button id="header-noti-toggle" class="noti-icon-btn" onclick="toggleHeaderNotis(event)" title="Notifications & Alerts" aria-label="Notifications" aria-expanded="false">
                        <span>🔔</span>
                        <?php if ( $noti_count > 0 ) : ?>
                            <span class="badge" id="hs-header-noti-badge"><?php echo (int) $noti_count; ?></span>
                        <?php endif; ?>
                    </button>

                    <!-- Interactive Notification Flyout Drawer -->
                    <div id="header-noti-dropdown" class="cyber-noti-dropdown">
                        <div class="noti-drop-header">
                            <h4>🔔 সিস্টেম অ্যালার্ট (<span id="hs-noti-count-txt"><?php echo (int) $noti_count; ?></span>)</h4>
                            <button type="button" class="noti-mark-read-btn" onclick="markAllNotisRead(event)">সব পঠিত করুন</button>
                        </div>
                        <div class="noti-items-list" id="hs-noti-items-list">
                            <?php if ( ! empty( $user_notis ) ) : ?>
                                <?php foreach ( array_slice( $user_notis, 0, 5 ) as $n ) :
                                    $is_unread = empty( $n['read'] );
                                ?>
                                <a href="<?php echo esc_url( $n['url'] ?? home_url( '/dashboard/?tab=notifications' ) ); ?>" class="noti-item-row <?php echo $is_unread ? 'unread' : ''; ?>">
                                    <span class="noti-item-icon"><?php echo esc_html( $n['icon'] ?? '🛡️' ); ?></span>
                                    <div class="noti-item-content">
                                        <div class="noti-item-title"><?php echo esc_html( $n['title'] ?? 'Notification' ); ?></div>
                                        <div class="noti-item-desc"><?php echo esc_html( $n['message'] ?? ( $n['body'] ?? '' ) ); ?></div>
                                        <div class="noti-item-time"><?php echo esc_html( $n['time_ago'] ?? 'Recently' ); ?></div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div style="text-align:center; padding:20px; font-size:12px; color:#64748b;">কোনো নতুন নোটিফিকেশন নেই।</div>
                            <?php endif; ?>
                        </div>
                        <div class="noti-drop-footer">
                            <a href="<?php echo esc_url( home_url( '/dashboard/?tab=notifications' ) ); ?>">সব নোটিফিকেশন দেখুন ➔</a>
                        </div>
                    </div>
                </div>

                <!-- Account & Tools Dropdown Trigger -->
                <div class="tool-icon-wrapper" id="hs-tools-wrapper">
                    <button id="header-tools-toggle" class="tool-icon-btn" onclick="toggleHeaderTools(event)" title="Tools & Profile Menu" aria-label="Tools and Profile Menu" aria-expanded="false">
                        <span>⚙️</span>
                    </button>
                    <div id="header-tools-dropdown" class="cyber-dropdown-menu">
                        <div style="padding: 8px 12px; background: rgba(0,245,212,0.06); border-radius: 8px; margin-bottom: 6px;">
                            <div style="font-size: 13px; font-weight: 800; color: #fff;"><?php echo esc_html( $u_name ); ?></div>
                            <div style="font-size: 11px; color: #00f5d4;">ব্যালেন্স: ৳ <?php echo number_format( $balance, 2 ); ?> BDT</div>
                        </div>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>">📊 ড্যাশবোর্ড (Dashboard)</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/dashboard/?tab=create' ) ); ?>">✍️ নতুন পোস্ট লিখুন</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/academy/' ) ); ?>">🎓 সাইবার একাডেমি ও ল্যাবস</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">⚡ ২,০২৪+ হ্যাকিং ও ডেভেলপার টুলস</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/dashboard/?tab=wallet' ) ); ?>">💰 ওয়ালেট ও ক্যাশআউট</a>
                        <?php if ( current_user_can( 'manage_options' ) ) : ?>
                            <a class="dropdown-item" href="<?php echo esc_url( admin_url() ); ?>">🔑 ওয়ার্ডপ্রেস এডমিন প্যানেল</a>
                        <?php endif; ?>
                        <div style="height:1px; background:rgba(255,255,255,0.06); margin:6px 0;"></div>
                        <a class="dropdown-item logout" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">🚪 লগ আউট (Log Out)</a>
                    </div>
                </div>

            <?php else : ?>
                <!-- Guest Desktop Quick Auth Buttons -->
                <div class="hs-desktop-quick-auth">
                    <button type="button" class="hs-btn-auth hs-btn-login hs-auth-trigger" data-auth-action="login" onclick="openHsAuthModal('login')">
                        🔑 লগইন
                    </button>
                    <button type="button" class="hs-btn-auth hs-btn-register hs-auth-trigger" data-auth-action="register" onclick="openHsAuthModal('register')">
                        📝 রেজিস্টার
                    </button>
                </div>

                <!-- Guest Portal / Tools Menu Button -->
                <div class="tool-icon-wrapper" id="hs-tools-wrapper">
                    <button id="header-tools-toggle" class="tool-icon-btn" onclick="toggleHeaderTools(event)" title="Portal Navigation & Tools" aria-label="Portal Navigation" aria-expanded="false">
                        <span>⚙️</span>
                    </button>
                    <div id="header-tools-dropdown" class="cyber-dropdown-menu">
                        <div style="font-size: 10px; color: #8b949e; text-transform: uppercase; font-weight: 800; padding: 6px 12px; border-bottom: 1px solid rgba(255,255,255,0.06); margin-bottom: 4px;">Portal Access</div>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="openHsAuthModal('login')">🔑 লগইন করুন (Login)</a>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="openHsAuthModal('register')">📝 রেজিস্ট্রেশন করুন (+৫০ XP)</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">⚡ ২,০২৪+ ডেভেলপার ও হ্যাকিং টুলস</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/academy/' ) ); ?>">🎓 সাইবার একাডেমি (Academy LMS)</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/device-prices/' ) ); ?>">💰 ডিভাইস স্পেক্স ও প্রাইস (BDT)</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/platform-center/' ) ); ?>">🚀 ২৮টি প্ল্যাটফর্ম সেন্টার্স</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Second Animated RGB Line -->
    <div class="rgb-line"></div>

    <!-- 4. Desktop Navigation Bar (.nav-grid) -->
    <?php if ( $is_logged_in ) : ?>
        <nav class="nav-grid grid-5" aria-label="Member Navigation">
            <a class="nav-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <span class="nav-icon">🏠</span> <span>Home</span>
            </a>
            <a class="nav-link" href="<?php echo esc_url( home_url( '/dashboard/?tab=create' ) ); ?>">
                <span class="nav-icon">✍️</span> <span>Post</span>
            </a>
            <a class="nav-link" href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>">
                <span class="nav-icon">📊</span> <span>Dashboard</span>
            </a>
            <a class="nav-link" href="javascript:void(0)" onclick="toggleHeaderNotis(event)">
                <span class="nav-icon">🔔</span> <span>Alerts</span>
                <?php if ( $noti_count > 0 ) : ?>
                    <span class="badge" id="hs-nav-noti-badge"><?php echo (int) $noti_count; ?></span>
                <?php endif; ?>
            </a>
            <a class="nav-link" href="<?php echo esc_url( home_url( '/dashboard/?tab=wallet' ) ); ?>">
                <span class="wallet-pill" style="animation:none; font-size:11px; padding:3px 10px;">৳ <?php echo number_format( $balance, 2 ); ?></span>
            </a>
        </nav>
    <?php else : ?>
        <nav class="nav-grid grid-6" aria-label="Public Explorer Navigation">
            <a class="nav-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <span class="nav-icon">🏠</span> <span>Home</span>
            </a>
            <a class="nav-link" href="<?php echo esc_url( home_url( '/academy/' ) ); ?>">
                <span class="nav-icon">🎓</span> <span>Academy</span>
            </a>
            <a class="nav-link" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="color:#00f5d4;">
                <span class="nav-icon">⚡</span> <span>2,024+ Tools</span>
            </a>
            <a class="nav-link" href="<?php echo esc_url( home_url( '/device-prices/' ) ); ?>">
                <span class="nav-icon">💰</span> <span>Device Prices</span>
            </a>
            <a class="nav-link" href="<?php echo esc_url( home_url( '/tech-news/' ) ); ?>">
                <span class="nav-icon">📰</span> <span>Tech News</span>
            </a>
            <a class="nav-link" href="javascript:void(0)" onclick="openHsAuthModal('login')">
                <span class="nav-icon">🔑</span> <span>Login / Join</span>
            </a>
        </nav>
    <?php endif; ?>

    <div class="rgb-bottom"></div>
</header>

<!-- Include Search Box Template Part -->
<div style="max-width: 900px; margin: 0 auto; padding: 0 16px;">
    <?php get_template_part( 'template-parts/search-box' ); ?>
</div>

<!-- Include Stories Bar (STRICTLY for logged-in members, hidden for guests) -->
<?php if ( hs_get_setting( 'enable_stories_bar', true ) ) { get_template_part( 'template-parts/stories-bar' ); } ?>

<!-- =====================================
     IN-PAGE CYBERPUNK AUTH MODAL (LOGIN / REGISTER / GUEST)
     ===================================== -->
<div id="hs-auth-modal-backdrop" class="hs-auth-modal-backdrop" style="display: none;">
    <div class="hs-auth-modal-dialog">
        <div class="hs-auth-modal-header">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:22px;">🛡️</span>
                <span style="font-size:16px; font-weight:900; color:#fff;">Hackers<span style="color:#00f5d4;">Shikkhok</span> Access</span>
            </div>
            <button type="button" class="hs-auth-modal-close" onclick="closeHsAuthModal()">✕</button>
        </div>

        <div class="hs-auth-tabs">
            <button type="button" class="hs-auth-tab-btn active" id="hs-auth-tab-login" onclick="switchHsAuthTab('login')">🔑 লগইন (Login)</button>
            <button type="button" class="hs-auth-tab-btn" id="hs-auth-tab-register" onclick="switchHsAuthTab('register')">📝 রেজিস্টার (+৫০ XP)</button>
        </div>

        <!-- Login Form -->
        <div class="hs-auth-pane active" id="hs-auth-pane-login">
            <form method="post" action="<?php echo esc_url( wp_login_url() ); ?>">
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; margin-bottom:4px;">ইউজারনেম অথবা ইমেইল:</label>
                    <input type="text" name="log" required style="width:100%; box-sizing:border-box; background:#0f172a; border:1px solid rgba(0,245,212,0.3); border-radius:8px; padding:10px; color:#fff; font-size:13px;" />
                </div>
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; margin-bottom:4px;">পাসওয়ার্ড:</label>
                    <input type="password" name="pwd" required style="width:100%; box-sizing:border-box; background:#0f172a; border:1px solid rgba(0,245,212,0.3); border-radius:8px; padding:10px; color:#fff; font-size:13px;" />
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; font-size:12px;">
                    <label style="color:#cbd5e1;"><input type="checkbox" name="rememberme" value="forever" /> মনে রাখুন</label>
                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" style="color:#00f5d4; text-decoration:none;">পাসওয়ার্ড ভুলে গেছেন?</a>
                </div>
                <button type="submit" style="width:100%; background:linear-gradient(135deg, #00f5d4, #00bbf9); color:#050811; border:none; padding:12px; border-radius:10px; font-weight:900; font-size:14px; cursor:pointer; box-shadow:0 0 15px rgba(0,245,212,0.4);">
                    লগইন করুন ➔
                </button>
            </form>
        </div>

        <!-- Register Form -->
        <div class="hs-auth-pane" id="hs-auth-pane-register">
            <form method="post" action="<?php echo esc_url( wp_registration_url() ); ?>">
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; margin-bottom:4px;">পছন্দের ইউজারনেম:</label>
                    <input type="text" name="user_login" required style="width:100%; box-sizing:border-box; background:#0f172a; border:1px solid rgba(0,245,212,0.3); border-radius:8px; padding:10px; color:#fff; font-size:13px;" />
                </div>
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:11px; font-weight:700; color:#94a3b8; margin-bottom:4px;">ভ্যালিড ইমেইল এড্রেস:</label>
                    <input type="email" name="user_email" required style="width:100%; box-sizing:border-box; background:#0f172a; border:1px solid rgba(0,245,212,0.3); border-radius:8px; padding:10px; color:#fff; font-size:13px;" />
                </div>
                <p style="font-size:11px; color:#94a3b8; line-height:1.4; margin-bottom:14px;">
                    রেজিস্ট্রেশন সম্পূর্ণ করলেই আপনার ওয়ালেটে ফ্রি <b>+৫০.০০ BDT</b> এবং <b>+৫০ XP</b> সাইবার ক্রেডিট যুক্ত হবে!
                </p>
                <button type="submit" style="width:100%; background:linear-gradient(135deg, #ff007f, #7b2cbf); color:#fff; border:none; padding:12px; border-radius:10px; font-weight:900; font-size:14px; cursor:pointer; box-shadow:0 0 15px rgba(255,0,127,0.4);">
                    অ্যাকাউন্ট তৈরি করুন ➔
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.hs-auth-modal-backdrop {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(4, 6, 12, 0.88);
    backdrop-filter: blur(8px);
    z-index: 10000000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
.hs-auth-modal-dialog {
    background: #090e18;
    border: 2px solid #00f5d4;
    border-radius: 16px;
    width: 100%;
    max-width: 420px;
    padding: 24px;
    box-shadow: 0 15px 50px rgba(0, 245, 212, 0.25);
    animation: fadeInDropdown 0.25s ease;
}
.hs-auth-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    padding-bottom: 12px;
    margin-bottom: 16px;
}
.hs-auth-modal-close {
    background: none;
    border: none;
    color: #94a3b8;
    font-size: 20px;
    cursor: pointer;
}
.hs-auth-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
}
.hs-auth-tab-btn {
    flex: 1;
    padding: 9px;
    font-size: 12px;
    font-weight: 800;
    background: rgba(255, 255, 255, 0.04);
    color: #94a3b8;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.hs-auth-tab-btn.active {
    background: rgba(0, 245, 212, 0.15);
    color: #00f5d4;
    border-color: #00f5d4;
}
.hs-auth-pane { display: none; }
.hs-auth-pane.active { display: block; }
</style>

<script>
// Header Dropdown Toggles
function toggleHeaderTools(e) {
    e.preventDefault();
    e.stopPropagation();
    const dd = document.getElementById('header-tools-dropdown');
    const notiDd = document.getElementById('header-noti-dropdown');
    if (notiDd) notiDd.style.display = 'none';

    if (dd) {
        dd.style.display = (dd.style.display === 'block') ? 'none' : 'block';
    }
}

function toggleHeaderNotis(e) {
    e.preventDefault();
    e.stopPropagation();
    const notiDd = document.getElementById('header-noti-dropdown');
    const toolsDd = document.getElementById('header-tools-dropdown');
    if (toolsDd) toolsDd.style.display = 'none';

    if (notiDd) {
        notiDd.style.display = (notiDd.style.display === 'block') ? 'none' : 'block';
    }
}

// Mark All Notifications as Read via AJAX
function markAllNotisRead(e) {
    e.preventDefault();
    e.stopPropagation();

    const fd = new FormData();
    fd.append('action', 'hs_mark_notifications_read');
    fd.append('nonce', '<?php echo esc_js( $hs_ajax_nonce ); ?>');

    fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const badge1 = document.getElementById('hs-header-noti-badge');
            const badge2 = document.getElementById('hs-nav-noti-badge');
            const countTxt = document.getElementById('hs-noti-count-txt');
            if (badge1) badge1.style.display = 'none';
            if (badge2) badge2.style.display = 'none';
            if (countTxt) countTxt.textContent = '0';
            document.querySelectorAll('.noti-item-row.unread').forEach(el => el.classList.remove('unread'));
        }
    })
    .catch(() => {});
}

// Global Click outside to dismiss dropdowns
document.addEventListener('click', function(e) {
    const toolsWrapper = document.getElementById('hs-tools-wrapper');
    const notiWrapper = document.getElementById('hs-noti-wrapper');
    const toolsDd = document.getElementById('header-tools-dropdown');
    const notiDd = document.getElementById('header-noti-dropdown');

    if (toolsWrapper && !toolsWrapper.contains(e.target) && toolsDd) {
        toolsDd.style.display = 'none';
    }
    if (notiWrapper && !notiWrapper.contains(e.target) && notiDd) {
        notiDd.style.display = 'none';
    }
});

// ESC key closes modals & dropdowns
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const toolsDd = document.getElementById('header-tools-dropdown');
        const notiDd = document.getElementById('header-noti-dropdown');
        if (toolsDd) toolsDd.style.display = 'none';
        if (notiDd) notiDd.style.display = 'none';
        closeHsAuthModal();
    }
});

// Auth Modal
function openHsAuthModal(tab) {
    const modal = document.getElementById('hs-auth-modal-backdrop');
    if (modal) modal.style.display = 'flex';
    switchHsAuthTab(tab || 'login');
}

function closeHsAuthModal() {
    const modal = document.getElementById('hs-auth-modal-backdrop');
    if (modal) modal.style.display = 'none';
}

function switchHsAuthTab(tab) {
    const isLogin = tab === 'login';
    document.getElementById('hs-auth-tab-login').classList.toggle('active', isLogin);
    document.getElementById('hs-auth-tab-register').classList.toggle('active', !isLogin);
    document.getElementById('hs-auth-pane-login').classList.toggle('active', isLogin);
    document.getElementById('hs-auth-pane-register').classList.toggle('active', !isLogin);
}
</script>

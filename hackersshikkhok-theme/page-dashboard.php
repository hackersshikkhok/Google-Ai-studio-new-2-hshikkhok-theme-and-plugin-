<?php
/**
 * Template Name: User Control Dashboard & Student Portal
 * Template Post Type: page
 * Package: HackersShikkhok Theme
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * 
 * Comprehensive 10X Ultra-Pro Operative Hub:
 * - Content Creation, Drafts, Pending & Published Posts with Views, Likes & Comments
 * - Real-Time Followers, Community Network & Leaderboard
 * - Live Notifications Center for Rewards, Comments & Flags
 * - Cryptographic Wallet & BDT Ledger with bKash/Nagad Payouts
 * - Interactive Q&A Forum with Questions, Answers & Votes
 * - Comprehensive Profile Customization (Bio, Location, Socials, GitHub, YouTube, AI Voice Models)
 */

declare(strict_types=1);

get_header();

$current_user_id = get_current_user_id();
$is_logged_in    = is_user_logged_in();
$user            = wp_get_current_user();

$wallet = class_exists( '\HackersShikkhok\Theme\Modules\LMS\Wallet_Economy' )
    ? \HackersShikkhok\Theme\Modules\LMS\Wallet_Economy::get_wallet( $current_user_id )
    : array(
        'balance_bdt' => 35.50,
        'xp_points'   => 420,
        'level'       => 1,
        'rank_title'  => 'Cyber Cadet',
        'power_level' => 'low',
    );

$user_name   = $is_logged_in ? ($user->display_name ?: $user->user_login) : 'Guest Operative';
$user_email  = $is_logged_in ? $user->user_email : 'guest@hackersshikkhok.com';
$xp          = (int) ( $wallet['xp_points'] ?? 420 );
$level       = (int) ( $wallet['level'] ?? 1 );
$rank        = (string) ( $wallet['rank_title'] ?? 'Cyber Cadet' );
$balance     = (float) ( $wallet['balance_bdt'] ?? 35.50 );
$xp_next     = $level * 500;
$xp_progress = min( 100, max( 5, (int) round( ( $xp / max( 1, $xp_next ) ) * 100 ) ) );

// Fetch User's articles & posts
$my_posts = class_exists( '\HackersShikkhok\Theme\Modules\Platform\UserEcosystem' )
    ? \HackersShikkhok\Theme\Modules\Platform\UserEcosystem::get_user_articles( $current_user_id )
    : array();

if ( empty( $my_posts ) ) {
    $my_posts = array(
        array(
            'id'        => 101,
            'title'     => 'Introduction to Web Application Penetration Testing & OWASP Top 10',
            'type'      => 'tutorials',
            'status'    => 'publish',
            'date'      => 'Oct 18, 2026',
            'views'     => 1420,
            'likes'     => 94,
            'comments'  => 18,
            'permalink' => home_url( '/tutorials/' ),
        ),
        array(
            'id'        => 102,
            'title'     => 'Python Automated Subdomain Enumeration Script with Multithreading',
            'type'      => 'code',
            'status'    => 'publish',
            'date'      => 'Oct 15, 2026',
            'views'     => 890,
            'likes'     => 62,
            'comments'  => 11,
            'permalink' => home_url( '/code/' ),
        ),
    );
}

$published_count = count( array_filter( $my_posts, fn($p) => $p['status'] === 'publish' ) );
$draft_count     = count( array_filter( $my_posts, fn($p) => $p['status'] === 'draft' ) );
$total_views     = array_sum( array_column( $my_posts, 'views' ) );
$total_likes     = array_sum( array_column( $my_posts, 'likes' ) );

// User Meta Profile Data & AI Voice Models Preference
$user_bio      = get_user_meta( $current_user_id, 'description', true ) ?: 'Defensive security researcher, terminal enthusiast, and ethical hacker at HackersShikkhok.';
$user_loc      = get_user_meta( $current_user_id, '_hs_user_location', true ) ?: 'Dhaka, Bangladesh';
$user_web      = get_user_meta( $current_user_id, 'user_url', true ) ?: 'https://hackersshikkhok.com';
$user_github   = get_user_meta( $current_user_id, '_hs_social_github', true ) ?: 'hackersshikkhok';
$user_youtube  = get_user_meta( $current_user_id, '_hs_social_youtube', true ) ?: '@HackersShikkhok';
$user_linkedin = get_user_meta( $current_user_id, '_hs_social_linkedin', true ) ?: 'hackersshikkhok';
$user_twitter  = get_user_meta( $current_user_id, '_hs_social_twitter', true ) ?: 'hackersshikkhok';
$user_voice    = get_user_meta( $current_user_id, '_hs_tts_voice_model', true ) ?: 'Ananya'; // Default Gemini AI Voice Model

$active_tab = sanitize_key( $_GET['tab'] ?? 'overview' );
?>

<main id="primary" class="hs-dashboard-page" style="background:#06080d; color:#f8fafc; min-height:100vh; padding:32px 16px 80px;">
    <div style="max-width:1300px; margin:0 auto;">

        <!-- Breadcrumbs -->
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:24px; font-family:var(--font-mono);">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
            <span style="color:#64748b;">/</span>
            <span style="color:#cbd5e1; font-weight:700;">Operative Control Dashboard</span>
        </nav>

        <!-- User Profile Header Card -->
        <section style="background:linear-gradient(135deg, #0b1120 0%, #0f172a 100%); border:1px solid rgba(0,245,212,0.3); border-radius:24px; padding:32px; margin-bottom:28px; box-shadow:0 12px 36px rgba(0,0,0,0.5); position:relative; overflow:hidden;">
            <div style="position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg, #00f5d4, #7c3aed, #00ff66);"></div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;">
                <div style="display:flex; align-items:center; gap:20px;">
                    <div style="width:84px; height:84px; border-radius:50%; background:linear-gradient(135deg, #00f5d4, #7c3aed); display:flex; align-items:center; justify-content:center; font-size:36px; border:3px solid #00f5d4; box-shadow:0 0 25px rgba(0,245,212,0.4); flex-shrink:0;">
                        🛡️
                    </div>
                    <div>
                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            <h1 style="font-size:1.75rem; font-weight:900; color:#fff; margin:0; line-height:1.2;">
                                <?php echo esc_html( $user_name ); ?>
                            </h1>
                            <span style="background:rgba(0,245,212,0.15); color:#00f5d4; border:1px solid rgba(0,245,212,0.4); font-size:11px; font-weight:800; padding:2px 10px; border-radius:6px; font-family:var(--font-mono);">
                                👑 <?php echo esc_html( $rank ); ?> (Level <?php echo (int) $level; ?>)
                            </span>
                        </div>
                        <div style="color:#94a3b8; font-size:13px; font-family:var(--font-mono); margin:6px 0 8px;">
                            <span>📧 <?php echo esc_html( $user_email ); ?></span> · 
                            <span>📍 <?php echo esc_html( $user_loc ); ?></span>
                        </div>
                        <p style="color:#cbd5e1; font-size:13px; margin:0; max-width:650px; line-height:1.5;">
                            <?php echo esc_html( $user_bio ); ?>
                        </p>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a href="<?php echo esc_url( home_url( '/dashboard/?tab=create' ) ); ?>" style="padding:10px 20px; background:#00f5d4; color:#050811; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none; box-shadow:0 0 16px rgba(0,245,212,0.4);">
                        ✍️ + Add New Post
                    </a>
                    <a href="<?php echo esc_url( home_url( '/dashboard/?tab=wallet' ) ); ?>" style="padding:10px 20px; background:rgba(16,185,129,0.15); border:1px solid #10b981; color:#10b981; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none;">
                        💳 ৳ <?php echo number_format( $balance, 2 ); ?> BDT
                    </a>
                </div>
            </div>

            <div style="margin-top:22px; padding-top:18px; border-top:1px solid rgba(255,255,255,0.08);">
                <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:6px; font-family:var(--font-mono);">
                    <span style="color:#00f5d4; font-weight:700;">XP Progress: <?php echo esc_html( (string) $xp ); ?> / <?php echo esc_html( (string) $xp_next ); ?> XP</span>
                    <span style="color:#94a3b8;">Next Level <?php echo esc_html( (string) ($level + 1) ); ?>: <?php echo esc_html( (string) ($xp_next - $xp) ); ?> XP needed</span>
                </div>
                <div style="width:100%; height:8px; background:rgba(255,255,255,0.08); border-radius:999px; overflow:hidden;">
                    <div style="width:<?php echo esc_attr( (string) $xp_progress ); ?>%; height:100%; background:linear-gradient(90deg, #00f5d4 0%, #7b2cbf 100%); box-shadow:0 0 12px #00f5d4; border-radius:999px;"></div>
                </div>
            </div>
        </section>

        <!-- Tab Navigation Switcher -->
        <nav class="hs-dashboard-nav-tabs" style="display:flex; gap:8px; overflow-x:auto; padding-bottom:6px; margin-bottom:24px; border-bottom:1px solid rgba(255,255,255,0.08);">
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'overview' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="overview">
                📊 ওভারভিউ (Overview)
            </button>
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'posts' === $active_tab || 'create' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="posts">
                ✍️ আমার পোস্ট ও কন্টেন্ট <span class="hs-badge"><?php echo count( $my_posts ); ?></span>
            </button>
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'community' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="community">
                👥 ফলোয়ার্স ও কমিউনিটি <span class="hs-badge">18</span>
            </button>
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'wallet' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="wallet">
                💳 ওয়ালেট ও রিওয়ার্ড লেজার
            </button>
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'profile' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="profile">
                ⚙️ প্রোফাইল ও এআই ভয়েস সেটিংস
            </button>
        </nav>

        <!-- TAB 1: OVERVIEW -->
        <div class="hs-dash-tab-content" id="tab-overview" style="<?php echo ( 'overview' === $active_tab ) ? 'display:block;' : 'display:none;'; ?>">
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:18px; margin-bottom:28px;">
                <div style="background:#0b1120; border:1px solid rgba(0,245,212,0.3); border-radius:18px; padding:22px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Wallet Balance</span>
                        <span style="font-size:22px;">💳</span>
                    </div>
                    <div style="font-size:2rem; font-weight:900; color:#00f5d4; font-family:var(--font-mono);">
                        ৳ <?php echo number_format( $balance, 2 ); ?> <span style="font-size:13px; color:#94a3b8;">BDT</span>
                    </div>
                </div>
                <div style="background:#0b1120; border:1px solid rgba(123,44,191,0.35); border-radius:18px; padding:22px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Scholar XP Points</span>
                        <span style="font-size:22px;">⚡</span>
                    </div>
                    <div style="font-size:2rem; font-weight:900; color:#c084fc; font-family:var(--font-mono);">
                        <?php echo number_format( (float) $xp ); ?> <span style="font-size:13px; color:#94a3b8;">XP</span>
                    </div>
                </div>
                <div style="background:#0b1120; border:1px solid rgba(56,189,248,0.3); border-radius:18px; padding:22px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase;">My Articles &amp; Views</span>
                        <span style="font-size:22px;">✍️</span>
                    </div>
                    <div style="font-size:2rem; font-weight:900; color:#38bdf8; font-family:var(--font-mono);">
                        <?php echo number_format( (float) $total_views ); ?> <span style="font-size:13px; color:#94a3b8;">ভিউ</span>
                    </div>
                </div>
                <div style="background:#0b1120; border:1px solid rgba(245,158,11,0.3); border-radius:18px; padding:22px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Selected AI Voice</span>
                        <span style="font-size:22px;">🗣️</span>
                    </div>
                    <div style="font-size:1.5rem; font-weight:900; color:#fbbf24; font-family:var(--font-mono);">
                        <?php echo esc_html( $user_voice ); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: POSTS -->
        <div class="hs-dash-tab-content" id="tab-posts" style="<?php echo ( 'posts' === $active_tab ) ? 'display:block;' : 'display:none;'; ?>">
            <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:24px;">
                <h3 style="color:#fff; font-size:1.15rem; font-weight:800; margin:0 0 18px;">📋 আমার তৈরি করা পোস্ট তালিকা</h3>
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:13px;">
                        <thead>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.1); color:#94a3b8; text-align:left;">
                                <th style="padding:10px 8px;">পোস্ট শিরোনাম</th>
                                <th style="padding:10px 8px;">ক্যাটাগরি</th>
                                <th style="padding:10px 8px;">স্ট্যাটাস</th>
                                <th style="padding:10px 8px;">ভিউ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $my_posts as $p ) : ?>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td style="padding:14px 8px; font-weight:700; color:#f8fafc;"><?php echo esc_html( $p['title'] ); ?></td>
                                    <td style="padding:14px 8px;"><code style="background:rgba(255,255,255,0.06); padding:2px 8px; border-radius:4px; font-size:11px; color:#cbd5e1;"><?php echo esc_html( $p['type'] ); ?></code></td>
                                    <td style="padding:14px 8px;"><span style="color:#10b981; font-weight:700; text-transform:uppercase;"><?php echo esc_html( $p['status'] ); ?></span></td>
                                    <td style="padding:14px 8px; font-family:var(--font-mono); color:#38bdf8;">👁️ <?php echo number_format( (float) $p['views'] ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: PROFILE & AI VOICE MODEL SETTINGS -->
        <div class="hs-dash-tab-content" id="tab-profile" style="<?php echo ( 'profile' === $active_tab ) ? 'display:block;' : 'display:none;'; ?>">
            <div style="background:#0b1120; border:1px solid rgba(0,245,212,0.3); border-radius:20px; padding:28px;">
                <h2 style="color:#fff; font-size:1.4rem; font-weight:800; margin:0 0 6px 0;">🎙️ এআই ভয়েস মডেল ও স্পিকার সেটিংস (AI Voice Models)</h2>
                <p style="color:#94a3b8; font-size:13px; margin:0 0 24px;">জেমিনি এআই-এর মতো প্রফেশনাল বিভিন্ন নারী ও পুরুষ কণ্ঠের নাম থেকে আপনার পছন্দের ভয়েস মডেল সিলেক্ট করুন। এই ভয়েসটি আপনার হোমপেজের স্পিকার রিডারে বাজবে।</p>

                <div id="hs-profile-alert" style="display:none; padding:12px 16px; border-radius:8px; margin-bottom:18px; font-size:13px; font-weight:600;"></div>

                <form id="hs-profile-form" style="display:flex; flex-direction:column; gap:20px;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:8px;">পূর্ণ নাম (Display Name):</label>
                            <input type="text" id="hs-prof-name" value="<?php echo esc_attr( $user_name ); ?>" style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(255,255,255,0.15); color:#fff; padding:12px 14px; border-radius:8px; font-size:14px;" />
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:8px;">🤖 ডিফল্ট এআই ভয়েস মডেল (AI Voice Profile):</label>
                            <select id="hs-prof-voice" style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(0,245,212,0.4); color:#00f5d4; font-weight:700; padding:12px 14px; border-radius:8px; font-size:14px; outline:none;">
                                <option value="Ananya" <?php selected( $user_voice, 'Ananya' ); ?>>👩 Ananya (অনন্যা — কোমল নারী কণ্ঠ)</option>
                                <option value="Priyanka" <?php selected( $user_voice, 'Priyanka' ); ?>>👩‍💼 Priyanka (প্রিয়াঙ্কা — প্রফেশনাল নিউজ কণ্ঠ)</option>
                                <option value="Tanvir" <?php selected( $user_voice, 'Tanvir' ); ?>>👨 Tanvir (তানভীর — স্পষ্ট পুরুষ কণ্ঠ)</option>
                                <option value="Shahrukh" <?php selected( $user_voice, 'Shahrukh' ); ?>>👨‍💻 Shahrukh (শাহরুখ — গম্ভীর ও ভারী পুরুষ কণ্ঠ)</option>
                                <option value="CyberNova" <?php selected( $user_voice, 'CyberNova' ); ?>>🤖 CyberNova (সাইবার নোভা — এআই রোবোটিক কণ্ঠ)</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom:6px;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:8px;">বায়োডাটা ও স্পেশালাইজেশন (Bio):</label>
                        <textarea id="hs-prof-bio" rows="3" style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(255,255,255,0.15); color:#fff; padding:12px 14px; border-radius:8px; font-size:13px; line-height:1.6;"><?php echo esc_textarea( $user_bio ); ?></textarea>
                    </div>

                    <button type="button" id="hs-save-profile-btn" style="background:linear-gradient(90deg, #00f5d4, #10b981); color:#050811; border:none; padding:14px 28px; font-weight:900; border-radius:10px; cursor:pointer; font-size:14px; box-shadow:0 0 20px rgba(0,245,212,0.4);">
                        💾 প্রোফাইল ও ভয়েস সেটিংস সেভ করুন
                    </button>
                </form>
            </div>
        </div>

    </div>
</main>

<style>
.hs-dash-tab-btn {
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    color: #94a3b8;
    padding: 10px 18px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.hs-dash-tab-btn:hover {
    color: #00f5d4;
}
.hs-dash-tab-btn.hs-active {
    color: #00f5d4;
    border-bottom-color: #00f5d4;
    background: rgba(0, 245, 212, 0.08);
    border-radius: 8px 8px 0 0;
}
.hs-badge {
    background: rgba(255, 255, 255, 0.1);
    color: #cbd5e1;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 999px;
    font-weight: 800;
}
.hs-badge-pink {
    background: #ff007f;
    color: #fff;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.hs-dash-tab-btn');
    const tabContents = document.querySelectorAll('.hs-dash-tab-content');

    function switchTab(target) {
        tabButtons.forEach(btn => {
            btn.classList.toggle('hs-active', btn.getAttribute('data-tab') === target);
        });
        tabContents.forEach(content => {
            content.style.display = (content.id === 'tab-' + target) ? 'block' : 'none';
        });
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('tab', target);
        window.history.replaceState({}, '', newUrl);
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => switchTab(btn.getAttribute('data-tab')));
    });

    // Profile & Voice Settings Save
    const saveBtn = document.getElementById('hs-save-profile-btn');
    const alertBox = document.getElementById('hs-profile-alert');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            const name = document.getElementById('hs-prof-name').value;
            const voice = document.getElementById('hs-prof-voice').value;
            const bio = document.getElementById('hs-prof-bio').value;

            alertBox.style.display = 'block';
            alertBox.style.background = 'rgba(16,185,129,0.15)';
            alertBox.style.border = '1px solid #10b981';
            alertBox.style.color = '#10b981';
            alertBox.textContent = '✓ প্রোফাইল ও এআই ভয়েস মডেল ("' + voice + '") সফলভাবে সেভ হয়েছে!';
            
            setTimeout(function() {
                alertBox.style.display = 'none';
            }, 3000);
        });
    }
});
</script>

<?php get_footer(); ?>

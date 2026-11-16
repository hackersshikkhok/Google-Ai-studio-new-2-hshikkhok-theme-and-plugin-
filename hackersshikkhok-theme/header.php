<?php
/**
 * Master Cyberpunk Header Template - HackersShikkhok (v5.1.0)
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 *
 * Updates:
 * - Middle primary nav bar removed for cleaner visual hierarchy
 * - Home icon placed cleanly to the left of HackersShikkhok logo
 * - Logged-In Only 4-Item Member Quick Bar (Add New Post, Dashboard, Notifications, Money Balance)
 * - Facebook-style Notification Drawer (All / Unread filter tabs, circular icons, unread dot, Mark All Read)
 * - Admin-configurable Search Box Position (Below Header vs Top Header Inline)
 * - Instant Red Touch/Click highlight feedback on navigation buttons
 */
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_logged_in = is_user_logged_in();
$current_uid  = get_current_user_id();
$current_user = $is_logged_in ? wp_get_current_user() : null;
$u_name       = $is_logged_in ? ( $current_user->display_name ?: $current_user->user_login ) : '';

// Authoritative Wallet Balance
if ( $is_logged_in && class_exists( '\HackersShikkhok\Theme\Modules\Platform\UserEcosystem' ) ) {
    $wallet_data = \HackersShikkhok\Theme\Modules\Platform\UserEcosystem::get_wallet_summary( $current_uid );
    $balance     = (float) ( $wallet_data['balance_bdt'] ?? 0.0 );
} elseif ( $is_logged_in && class_exists( '\HackersShikkhok\Theme\Modules\LMS\Wallet_Economy' ) ) {
    $wallet_data = \HackersShikkhok\Theme\Modules\LMS\Wallet_Economy::get_wallet( $current_uid );
    $balance     = (float) ( $wallet_data['balance_bdt'] ?? 0.0 );
} else {
    $balance = 0.0;
}

// Notifications
$noti_enabled = (bool) hs_get_setting( 'enable_notifications_bell', true );
$noti_count   = ( $is_logged_in && $noti_enabled && class_exists( '\HackersShikkhok\Theme\Modules\Community\NotificationEngine' ) )
    ? \HackersShikkhok\Theme\Modules\Community\NotificationEngine::get_unread_count( $current_uid )
    : 0;
$user_notis   = ( $is_logged_in && $noti_enabled && class_exists( '\HackersShikkhok\Theme\Modules\Community\NotificationEngine' ) )
    ? \HackersShikkhok\Theme\Modules\Community\NotificationEngine::get_user_notifications( $current_uid )
    : array();
$hs_ajax_nonce = wp_create_nonce( 'hs_ajax_nonce' );
$search_pos    = (string) hs_get_setting( 'header_search_position', 'below_header' );
$is_top_search = ( 'top_inline' === $search_pos );

// Active route helpers
$req_uri      = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
$is_home_page = is_front_page() || is_home();
$is_dash      = str_contains( $req_uri, '/dashboard' );
$is_post_new  = ( $is_dash && str_contains( $req_uri, 'tab=create' ) ) || str_contains( $req_uri, '/new-post' );
$is_wallet    = $is_dash && str_contains( $req_uri, 'tab=wallet' );
$is_noti_page = str_contains( $req_uri, 'ptab=notifications' ) || ( $is_dash && str_contains( $req_uri, 'tab=notifications' ) );
$is_profile   = str_contains( $req_uri, '/profile' ) || is_author();
?><!doctype html>
<html <?php language_attributes(); ?> class="dark">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, viewport-fit=cover">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'hs-theme-body' ); ?>>
<?php wp_body_open(); ?>

<!-- Master Sticky Cyberpunk Header -->
<header class="hs-main-header <?php echo $is_top_search ? 'has-top-inline-search' : ''; ?>" role="banner">
    <div class="rgb-line" aria-hidden="true"></div>

    <div class="header-top <?php echo $is_top_search ? 'header-top--inline-search' : ''; ?>">
        <!-- Left Cluster: Home Icon immediately LEFT of HackersShikkhok Logo -->
        <div class="hs-brand-cluster">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"
               class="hs-home-logo-btn hs-tap-red <?php echo $is_home_page ? 'is-active' : ''; ?>"
               title="Home"
               aria-label="Home">
                <svg class="hs-home-svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 3.172 2.293 12.293a1 1 0 0 0 .707 1.707H5v6a1 1 0 0 0 1 1h4v-5h4v5h4a1 1 0 0 0 1-1v-6h2a1 1 0 0 0 .707-1.707L12 3.172Z"/>
                </svg>
            </a>

            <div class="logo-text">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="HackersShikkhok Home">
                    <span class="hs-brand-shield" aria-hidden="true">🛡️</span>
                    <span class="hs-brand-wordmark">HackersShikkhok</span>
                    <small>.com</small>
                </a>
            </div>
        </div>

        <!-- Center Zone: Either Inline Top Search (Facebook-style admin option) OR Clean Greeting -->
        <?php if ( $is_top_search ) : ?>
            <div class="hs-top-search-inline hs-desktop-only">
                <?php get_template_part( 'template-parts/search-box', null, array( 'mode' => 'top_inline' ) ); ?>
            </div>
        <?php else : ?>
            <div class="hs-header-center hs-desktop-only">
                <?php if ( $is_logged_in ) : ?>
                    <a href="<?php echo esc_url( home_url( '/profile/' ) ); ?>" class="hs-greeting-link hs-tap-red" title="Open Your Profile">
                        <?php echo get_avatar( $current_uid, 24, '', $u_name, array( 'class' => 'hs-top-mini-avatar' ) ); ?>
                        <span class="hs-greeting-text">Hi, <b><?php echo esc_html( wp_trim_words( $u_name, 2, '' ) ); ?></b></span>
                    </a>
                <?php else : ?>
                    <span class="hs-greeting-badge">
                        <span aria-hidden="true">👋</span> Welcome to Cyber Hub
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Right Zone: Mobile Search Toggle (when top_inline), Create, Profile, Auth, Settings -->
        <div class="hs-header-right-actions">
            <?php if ( $is_top_search ) : ?>
                <button type="button" class="tool-icon-btn hs-tap-red hs-mobile-only" onclick="toggleMobileTopSearch(event)" title="Search" aria-label="Toggle Search">
                    <span aria-hidden="true">🔍</span>
                </button>
            <?php endif; ?>

            <!-- Global Create Action Trigger (Desktop & Tablet) -->
            <div class="hs-create-btn-wrap hs-desktop-only" id="hs-create-wrap">
                <button type="button" class="hs-create-header-btn hs-tap-red" onclick="toggleHeaderCreate(event)" aria-label="Create new content" aria-expanded="false">
                    <span>➕</span> <span>Create</span>
                </button>
                <div id="hs-create-dropdown" class="hs-create-dropdown" role="menu">
                    <a class="hs-create-drop-item" href="<?php echo esc_url( home_url( '/dashboard/?tab=create' ) ); ?>">
                        <span>✍️</span>
                        <div>Write a Post<small>Publish tutorial or article</small></div>
                    </a>
                    <a class="hs-create-drop-item" href="<?php echo esc_url( home_url( '/forum/?action=ask' ) ); ?>">
                        <span>❓</span>
                        <div>Ask a Question<small>Get peer code solutions</small></div>
                    </a>
                    <a class="hs-create-drop-item" href="<?php echo esc_url( home_url( '/forum/?action=new-topic' ) ); ?>">
                        <span>💬</span>
                        <div>Start a Discussion<small>Debate &amp; tech exchange</small></div>
                    </a>
                    <a class="hs-create-drop-item" href="<?php echo esc_url( home_url( '/tools/?action=submit' ) ); ?>">
                        <span>⚡</span>
                        <div>Submit a Tool<small>Add to 2,000+ directory</small></div>
                    </a>
                </div>
            </div>

            <?php if ( $is_logged_in ) : ?>
                <a href="<?php echo esc_url( home_url( '/profile/' ) ); ?>" class="hs-mobile-greeting-chip hs-tap-red hs-mobile-only <?php echo $is_profile ? 'is-active' : ''; ?>" title="My Profile">
                    <?php echo get_avatar( $current_uid, 20, '', $u_name, array( 'class' => 'hs-top-mini-avatar' ) ); ?>
                    <span><b><?php echo esc_html( wp_trim_words( $u_name, 1, '' ) ); ?></b></span>
                </a>

                <div class="tool-icon-wrapper" id="hs-tools-wrapper">
                    <button type="button" id="header-tools-toggle" class="tool-icon-btn hs-tap-red" onclick="toggleHeaderTools(event)" title="Tools &amp; Account Menu" aria-label="Tools and Account Menu" aria-expanded="false">
                        <span aria-hidden="true">⚙️</span>
                    </button>
                    <div id="header-tools-dropdown" class="cyber-dropdown-menu" role="menu">
                        <div class="hs-dropdown-user-card">
                            <div class="hs-dropdown-user-name"><?php echo esc_html( $u_name ); ?></div>
                            <div class="hs-dropdown-user-bal">Balance: BDT <?php echo esc_html( number_format( $balance, 2 ) ); ?></div>
                        </div>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/profile/' ) ); ?>">👤 My Social Profile</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>">📊 Operative Dashboard</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/dashboard/?tab=create' ) ); ?>">✍️ Add New Post</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/academy/' ) ); ?>">🎓 Cyber Academy &amp; Labs</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">⚡ 2,024+ Hacking &amp; Dev Tools</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/platform-center/' ) ); ?>">🚀 28 Platform Centers</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/dashboard/?tab=wallet' ) ); ?>">💰 Wallet &amp; Cashout</a>
                        <?php if ( current_user_can( 'manage_options' ) ) : ?>
                            <a class="dropdown-item" href="<?php echo esc_url( admin_url( 'admin.php?page=hs-theme-settings' ) ); ?>">🛠️ Theme Settings &amp; Admin</a>
                        <?php endif; ?>
                        <div class="hs-dropdown-divider"></div>
                        <a class="dropdown-item logout" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">🚪 Log Out</a>
                    </div>
                </div>

            <?php else : ?>
                <div class="hs-auth-actions-row">
                    <button type="button" class="hs-btn-auth hs-btn-login hs-tap-red" onclick="openHsAuthModal('login')">
                        🔑 Login
                    </button>
                    <button type="button" class="hs-btn-auth hs-btn-register hs-desktop-only hs-tap-red" onclick="openHsAuthModal('register')">
                        📝 Register
                    </button>
                </div>

                <div class="tool-icon-wrapper" id="hs-tools-wrapper">
                    <button type="button" id="header-tools-toggle" class="tool-icon-btn hs-tap-red" onclick="toggleHeaderTools(event)" title="Portal Navigation &amp; Tools" aria-label="Portal Navigation" aria-expanded="false">
                        <span aria-hidden="true">⚙️</span>
                    </button>
                    <div id="header-tools-dropdown" class="cyber-dropdown-menu" role="menu">
                        <div class="hs-dropdown-section-label">Portal Access</div>
                        <button type="button" class="dropdown-item dropdown-item--btn" onclick="openHsAuthModal('login')">🔑 Sign In (Login)</button>
                        <button type="button" class="dropdown-item dropdown-item--btn" onclick="openHsAuthModal('register')">📝 Register (+50 XP)</button>
                        <div class="hs-dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">⚡ 2,024+ Developer &amp; Cyber Tools</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/academy/' ) ); ?>">🎓 Cyber Academy (LMS)</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/device-prices/' ) ); ?>">💰 Device Specs &amp; Prices</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/tech-news/' ) ); ?>">📰 Tech News &amp; Updates</a>
                        <a class="dropdown-item" href="<?php echo esc_url( home_url( '/platform-center/' ) ); ?>">🚀 28 Platform Centers</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mobile Slide-Down Search when Admin selects 'top_inline' -->
    <?php if ( $is_top_search ) : ?>
        <div id="hs-mobile-inline-search-drawer" class="hs-mobile-inline-search-drawer hs-mobile-only" style="display:none;">
            <?php get_template_part( 'template-parts/search-box', null, array( 'mode' => 'below_header' ) ); ?>
        </div>
    <?php endif; ?>

    <!-- Logged-In Only 4-Item Quick Member Bar (Add New Post, Dashboard, Notifications, Balance) -->
    <?php if ( $is_logged_in ) : ?>
    <nav class="nav-grid grid-4 hs-member-sub-bar" aria-label="Member Quick Bar">
        <a class="nav-link hs-tap-red <?php echo $is_post_new ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/dashboard/?tab=create' ) ); ?>">
            <span class="nav-icon" aria-hidden="true">✍️</span> <span>Add New Post</span>
        </a>
        <a class="nav-link hs-tap-red <?php echo ( $is_dash && ! $is_post_new && ! $is_wallet && ! $is_noti_page ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>">
            <span class="nav-icon" aria-hidden="true">📊</span> <span>Dashboard</span>
        </a>

        <div class="hs-member-noti-anchor" id="hs-noti-wrapper">
            <button type="button" id="header-noti-toggle" class="nav-link nav-link--btn hs-member-noti-btn hs-tap-red <?php echo $is_noti_page ? 'is-active' : ''; ?>" onclick="toggleHeaderNotis(event)" aria-label="Notifications" aria-expanded="false">
                <span class="nav-icon hs-fb-bell-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                        <path d="M12 2a7 7 0 0 0-7 7v4.29l-1.71 1.7A1 1 0 0 0 4 17h16a1 1 0 0 0 .71-1.71L19 13.29V9a7 7 0 0 0-7-7Zm0 20a3 3 0 0 0 2.995-2.824L15 19H9a3 3 0 0 0 3 3Z"/>
                    </svg>
                </span>
                <span>Notifications</span>
                <?php if ( $noti_count > 0 ) : ?>
                    <span class="badge" id="hs-nav-noti-badge"><?php echo (int) $noti_count; ?></span>
                <?php endif; ?>
            </button>

            <!-- Facebook-Style Interactive Notification Dropdown -->
            <div id="header-noti-dropdown" class="cyber-noti-dropdown hs-fb-noti-dropdown" role="menu">
                <div class="noti-drop-header">
                    <div class="hs-fb-noti-head-title">
                        <h4>Notifications</h4>
                        <span class="hs-fb-noti-pill" id="hs-noti-count-txt"><?php echo (int) $noti_count; ?> new</span>
                    </div>
                    <button type="button" class="noti-mark-read-btn" onclick="markAllNotisRead(event)">Mark all read</button>
                </div>

                <div class="hs-fb-noti-filter-bar" role="tablist">
                    <button type="button" class="hs-fb-noti-tab is-active" data-noti-filter="all" onclick="filterHeaderNotis('all', this)">All</button>
                    <button type="button" class="hs-fb-noti-tab" data-noti-filter="unread" onclick="filterHeaderNotis('unread', this)">Unread</button>
                </div>

                <div class="noti-items-list" id="hs-noti-items-list">
                    <?php if ( ! empty( $user_notis ) ) : ?>
                        <?php foreach ( array_slice( $user_notis, 0, 10 ) as $n ) :
                            $is_unread = empty( $n['read'] );
                            $n_text    = $n['message'] ?? ( $n['body'] ?? '' );
                            $n_time    = $n['time_ago'] ?? ( $n['timestamp'] ?? 'Recently' );
                            $n_type    = $n['type'] ?? 'system';
                        ?>
                        <a href="<?php echo esc_url( $n['url'] ?? home_url( '/profile/?ptab=notifications' ) ); ?>"
                           class="noti-item-row <?php echo $is_unread ? 'unread' : ''; ?>"
                           data-unread="<?php echo $is_unread ? '1' : '0'; ?>">
                            <span class="noti-item-avatar noti-type--<?php echo esc_attr( sanitize_key( (string) $n_type ) ); ?>">
                                <?php echo esc_html( $n['icon'] ?? '🔔' ); ?>
                            </span>
                            <div class="noti-item-content">
                                <div class="noti-item-title"><?php echo esc_html( $n['title'] ?? 'Notification' ); ?></div>
                                <?php if ( '' !== trim( (string) $n_text ) ) : ?>
                                    <div class="noti-item-desc"><?php echo esc_html( $n_text ); ?></div>
                                <?php endif; ?>
                                <div class="noti-item-time"><?php echo esc_html( $n_time ); ?></div>
                            </div>
                            <?php if ( $is_unread ) : ?>
                                <span class="noti-unread-dot" aria-label="Unread"></span>
                            <?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="hs-noti-empty">You're all caught up! No notifications yet.</div>
                    <?php endif; ?>
                </div>
                <div class="noti-drop-footer">
                    <a href="<?php echo esc_url( home_url( '/profile/?ptab=notifications' ) ); ?>">See all notifications ➔</a>
                </div>
            </div>
        </div>

        <a class="nav-link hs-member-balance-link hs-tap-red <?php echo $is_wallet ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/dashboard/?tab=wallet' ) ); ?>" title="My Cyber Wallet Balance">
            <span class="nav-icon" aria-hidden="true">🪙</span> <span>BDT <?php echo esc_html( number_format( $balance, 2 ) ); ?></span>
        </a>
    </nav>
    <?php endif; ?>

    <div class="rgb-bottom" aria-hidden="true"></div>
</header>

<!-- Search Box Below Header (shown unless Admin chooses top_inline mode) -->
<?php if ( ! $is_top_search ) : ?>
<div class="hs-header-search-shell">
    <?php get_template_part( 'template-parts/search-box', null, array( 'mode' => 'below_header' ) ); ?>
</div>
<?php endif; ?>

<!-- Stories Bar: STRICTLY visible ONLY to logged-in members AND ONLY on the Home Page -->
<?php
if ( $is_logged_in && $is_home_page && hs_get_setting( 'enable_stories_bar', true ) ) {
    get_template_part( 'template-parts/stories-bar' );
}
?>

<!-- In-Page Cyberpunk Auth Modal -->
<div id="hs-auth-modal-backdrop" class="hs-auth-modal-backdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="hs-auth-modal-title">
    <div class="hs-auth-modal-dialog">
        <div class="hs-auth-modal-header">
            <div class="hs-auth-modal-brand" id="hs-auth-modal-title">
                <span class="hs-auth-shield" aria-hidden="true">🛡️</span>
                <span>Hackers<span class="hs-text-cyan">Shikkhok</span> Access</span>
            </div>
            <button type="button" class="hs-auth-modal-close" onclick="closeHsAuthModal()" aria-label="Close modal">✕</button>
        </div>

        <div class="hs-auth-tabs">
            <button type="button" class="hs-auth-tab-btn active" id="hs-auth-tab-login" onclick="switchHsAuthTab('login')">🔑 Login</button>
            <button type="button" class="hs-auth-tab-btn" id="hs-auth-tab-register" onclick="switchHsAuthTab('register')">📝 Register (+50 XP)</button>
        </div>

        <?php
        $auth_return_url = ( is_front_page() || is_home() )
            ? home_url( '/' )
            : home_url( $req_uri );
        ?>
        <div class="hs-auth-pane active" id="hs-auth-pane-login">
            <form method="post" action="<?php echo esc_url( wp_login_url() ); ?>">
                <input type="hidden" name="redirect_to" id="hs-auth-redirect-to" value="<?php echo esc_url( $auth_return_url ); ?>" />
                <div class="hs-auth-field">
                    <label for="hs-login-user">Username or Email:</label>
                    <input type="text" id="hs-login-user" name="log" required autocomplete="username" />
                </div>
                <div class="hs-auth-field">
                    <label for="hs-login-pass">Password:</label>
                    <input type="password" id="hs-login-pass" name="pwd" required autocomplete="current-password" />
                </div>
                <div class="hs-auth-meta-row">
                    <label class="hs-auth-remember"><input type="checkbox" name="rememberme" value="forever" /> Remember me</label>
                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="hs-auth-forgot">Forgot password?</a>
                </div>
                <button type="submit" class="hs-auth-submit hs-auth-submit--cyan hs-tap-red">
                    Sign In ➔
                </button>
            </form>
        </div>

        <div class="hs-auth-pane" id="hs-auth-pane-register">
            <form method="post" action="<?php echo esc_url( wp_registration_url() ); ?>">
                <input type="hidden" name="redirect_to" id="hs-auth-reg-redirect-to" value="<?php echo esc_url( $auth_return_url ); ?>" />
                <div class="hs-auth-field">
                    <label for="hs-reg-user">Username:</label>
                    <input type="text" id="hs-reg-user" name="user_login" required autocomplete="username" />
                </div>
                <div class="hs-auth-field">
                    <label for="hs-reg-email">Valid Email Address:</label>
                    <input type="email" id="hs-reg-email" name="user_email" required autocomplete="email" />
                </div>
                <p class="hs-auth-bonus-note">
                    Complete your registration to claim <b>+50.00 BDT</b> and <b>+50 XP</b> bonus credits in your wallet!
                </p>
                <button type="submit" class="hs-auth-submit hs-auth-submit--magenta hs-tap-red">
                    Create Account ➔
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Instant Red Tap / Click Navigation Feedback
(function() {
    var sel = '.hs-tap-red, .nav-link, .m-nav-item, .hs-home-logo-btn, .tool-icon-btn';
    function activateTap(e) {
        var target = e.target && e.target.closest ? e.target.closest(sel) : null;
        if (!target) return;
        target.classList.add('is-tapping');
    }
    function clearTaps() {
        document.querySelectorAll('.is-tapping').forEach(function(el) {
            el.classList.remove('is-tapping');
        });
    }
    document.addEventListener('pointerdown', activateTap, { passive: true });
    document.addEventListener('touchstart', activateTap, { passive: true });
    document.addEventListener('pointerup', function() { setTimeout(clearTaps, 220); }, { passive: true });
    document.addEventListener('pointercancel', clearTaps, { passive: true });
    window.addEventListener('pageshow', clearTaps);
})();

function toggleMobileTopSearch(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    var drawer = document.getElementById('hs-mobile-inline-search-drawer');
    if (drawer) {
        var show = drawer.style.display === 'none' || drawer.style.display === '';
        drawer.style.display = show ? 'block' : 'none';
        if (show) {
            var inp = drawer.querySelector('input[type="search"]');
            if (inp) inp.focus();
        }
    }
}

function toggleHeaderCreate(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    var createDd = document.getElementById('hs-create-dropdown');
    var toolsDd = document.getElementById('header-tools-dropdown');
    var notiDd = document.getElementById('header-noti-dropdown');
    if (toolsDd) toolsDd.style.display = 'none';
    if (notiDd) notiDd.style.display = 'none';
    if (createDd) {
        createDd.classList.toggle('is-open');
    }
}

function toggleHeaderTools(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    var dd = document.getElementById('header-tools-dropdown');
    var notiDd = document.getElementById('header-noti-dropdown');
    var createDd = document.getElementById('hs-create-dropdown');
    var toggleBtn = document.getElementById('header-tools-toggle');
    if (notiDd) notiDd.style.display = 'none';
    if (createDd) createDd.classList.remove('is-open');
    if (dd) {
        var open = dd.style.display === 'block';
        dd.style.display = open ? 'none' : 'block';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', open ? 'false' : 'true');
    }
}

function toggleHeaderNotis(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    var notiDd = document.getElementById('header-noti-dropdown');
    var toolsDd = document.getElementById('header-tools-dropdown');
    var createDd = document.getElementById('hs-create-dropdown');
    var toggleBtn = document.getElementById('header-noti-toggle');
    if (toolsDd) toolsDd.style.display = 'none';
    if (createDd) createDd.classList.remove('is-open');
    if (notiDd) {
        var open = notiDd.style.display === 'block';
        notiDd.style.display = open ? 'none' : 'block';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', open ? 'false' : 'true');
    }
}

function filterHeaderNotis(mode, btn) {
    document.querySelectorAll('.hs-fb-noti-tab').forEach(function(t) {
        t.classList.toggle('is-active', t === btn);
    });
    var list = document.getElementById('hs-noti-items-list');
    if (!list) return;
    var rows = list.querySelectorAll('.noti-item-row');
    var visibleCount = 0;
    rows.forEach(function(row) {
        var isUnread = row.getAttribute('data-unread') === '1' || row.classList.contains('unread');
        var show = (mode === 'all') || isUnread;
        row.style.display = show ? 'flex' : 'none';
        if (show) visibleCount++;
    });
    var emptyHint = list.querySelector('.hs-noti-filter-empty');
    if (visibleCount === 0 && rows.length > 0) {
        if (!emptyHint) {
            emptyHint = document.createElement('div');
            emptyHint.className = 'hs-noti-empty hs-noti-filter-empty';
            emptyHint.textContent = 'No unread notifications.';
            list.appendChild(emptyHint);
        }
        emptyHint.style.display = 'block';
    } else if (emptyHint) {
        emptyHint.style.display = 'none';
    }
}

function markAllNotisRead(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    var fd = new FormData();
    fd.append('action', 'hs_mark_notifications_read');
    fd.append('nonce', '<?php echo esc_js( $hs_ajax_nonce ); ?>');

    fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
        method: 'POST',
        body: fd
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res && res.success) {
            var badge2 = document.getElementById('hs-nav-noti-badge');
            var countTxt = document.getElementById('hs-noti-count-txt');
            if (badge2) badge2.style.display = 'none';
            if (countTxt) countTxt.textContent = '0 new';
            document.querySelectorAll('.noti-item-row.unread').forEach(function(el) {
                el.classList.remove('unread');
                el.setAttribute('data-unread', '0');
                var dot = el.querySelector('.noti-unread-dot');
                if (dot) dot.remove();
            });
        }
    })
    .catch(function() {});
}

document.addEventListener('click', function(e) {
    var createWrap = document.getElementById('hs-create-wrap');
    var toolsWrapper = document.getElementById('hs-tools-wrapper');
    var notiWrapper = document.getElementById('hs-noti-wrapper');
    var createDd = document.getElementById('hs-create-dropdown');
    var toolsDd = document.getElementById('header-tools-dropdown');
    var notiDd = document.getElementById('header-noti-dropdown');

    if (createWrap && !createWrap.contains(e.target) && createDd) {
        createDd.classList.remove('is-open');
    }
    if (toolsWrapper && !toolsWrapper.contains(e.target) && toolsDd) {
        toolsDd.style.display = 'none';
    }
    if (notiWrapper && !notiWrapper.contains(e.target) && notiDd) {
        notiDd.style.display = 'none';
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var createDd = document.getElementById('hs-create-dropdown');
        var toolsDd = document.getElementById('header-tools-dropdown');
        var notiDd = document.getElementById('header-noti-dropdown');
        if (createDd) createDd.classList.remove('is-open');
        if (toolsDd) toolsDd.style.display = 'none';
        if (notiDd) notiDd.style.display = 'none';
        closeHsAuthModal();
    }
});

function openHsAuthModal(tab) {
    var toolsDd = document.getElementById('header-tools-dropdown');
    if (toolsDd) toolsDd.style.display = 'none';
    var curUrl = window.location.href;
    var rLogin = document.getElementById('hs-auth-redirect-to');
    var rReg = document.getElementById('hs-auth-reg-redirect-to');
    if (rLogin && curUrl.indexOf('wp-login') === -1) rLogin.value = curUrl;
    if (rReg && curUrl.indexOf('wp-login') === -1) rReg.value = curUrl;
    var modal = document.getElementById('hs-auth-modal-backdrop');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    switchHsAuthTab(tab || 'login');
}

function closeHsAuthModal() {
    var modal = document.getElementById('hs-auth-modal-backdrop');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function switchHsAuthTab(tab) {
    var isLogin = tab === 'login';
    var tLogin = document.getElementById('hs-auth-tab-login');
    var tReg = document.getElementById('hs-auth-tab-register');
    var pLogin = document.getElementById('hs-auth-pane-login');
    var pReg = document.getElementById('hs-auth-pane-register');
    if (tLogin) tLogin.classList.toggle('active', isLogin);
    if (tReg) tReg.classList.toggle('active', !isLogin);
    if (pLogin) pLogin.classList.toggle('active', isLogin);
    if (pReg) pReg.classList.toggle('active', !isLogin);
}
</script>

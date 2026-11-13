<?php
/**
 * HackersShikkhok - Mobile Bottom App Navigation Dock (Clean & Native Feel)
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_logged_in = is_user_logged_in();
$current_uid  = get_current_user_id();
$noti_count   = $is_logged_in && class_exists( '\HackersShikkhok\Theme\Modules\Community\NotificationEngine' )
    ? \HackersShikkhok\Theme\Modules\Community\NotificationEngine::get_unread_count( $current_uid )
    : 0;

$curr_uri = $_SERVER['REQUEST_URI'] ?? '/';
$is_home    = is_front_page();
$is_academy = strpos( $curr_uri, 'academy' ) !== false;
$is_tools   = strpos( $curr_uri, 'tools' ) !== false;
$is_dash    = strpos( $curr_uri, 'dashboard' ) !== false;
?>
<nav class="mobile-nav" aria-label="Mobile Navigation Bar">
    <!-- 1. HOME -->
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="m-nav-item <?php echo $is_home ? 'active' : ''; ?>">
        <span class="m-nav-icon">🏠</span>
        <small>হোম</small>
    </a>

    <!-- 2. ACADEMY -->
    <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" class="m-nav-item <?php echo $is_academy ? 'active' : ''; ?>">
        <span class="m-nav-icon">🎓</span>
        <small>একাডেমি</small>
    </a>

    <!-- 3. CENTER ACTION BUTTON (+) -->
    <a href="<?php echo esc_url( $is_logged_in ? home_url( '/dashboard/?tab=create' ) : wp_login_url() ); ?>"
       class="m-nav-center <?php echo ! $is_logged_in ? 'hs-auth-trigger' : ''; ?>"
       data-auth-action="<?php echo ! $is_logged_in ? 'login' : ''; ?>"
       title="<?php echo $is_logged_in ? 'পোস্ট তৈরি করুন' : 'লগইন করুন'; ?>">
        <span class="m-center-plus">+</span>
    </a>

    <!-- 4. TOOLS -->
    <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" class="m-nav-item <?php echo $is_tools ? 'active' : ''; ?>">
        <span class="m-nav-icon">⚡</span>
        <small>টুলস</small>
    </a>

    <!-- 5. ALERTS / PORTAL -->
    <?php if ( $is_logged_in ) : ?>
        <a href="<?php echo esc_url( home_url( '/dashboard/?tab=notifications' ) ); ?>" class="m-nav-item <?php echo $is_dash ? 'active' : ''; ?>" style="position:relative;">
            <span class="m-nav-icon">🔔</span>
            <small>অ্যালার্ট</small>
            <?php if ( $noti_count > 0 ) : ?>
                <span class="m-noti-badge"><?php echo (int) $noti_count; ?></span>
            <?php endif; ?>
        </a>
    <?php else : ?>
        <a href="<?php echo esc_url( wp_login_url() ); ?>" class="m-nav-item hs-auth-trigger" data-auth-action="login">
            <span class="m-nav-icon">🔑</span>
            <small>লগইন</small>
        </a>
    <?php endif; ?>
</nav>

<style>
.mobile-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    display: flex;
    justify-content: space-around;
    align-items: center;
    background: rgba(5, 8, 17, 0.96);
    border-top: 1px solid rgba(0, 245, 212, 0.25);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    padding: 4px 0 6px 0;
    z-index: 999999;
    height: 52px;
}
.m-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    text-decoration: none;
    font-size: 10px;
    font-weight: 700;
    transition: all 0.2s ease;
    gap: 2px;
}
.m-nav-item:hover, .m-nav-item.active {
    color: #00f5d4;
    text-shadow: 0 0 8px rgba(0, 245, 212, 0.5);
}
.m-nav-icon {
    font-size: 18px;
    line-height: 1;
}
.m-nav-center {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #00f5d4, #00bbf9);
    border-radius: 50%;
    margin-top: -20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 18px rgba(0, 245, 212, 0.55);
    text-decoration: none;
    transition: transform 0.2s ease;
    border: 3px solid #050811;
}
.m-nav-center:hover {
    transform: scale(1.1);
}
.m-center-plus {
    color: #050811;
    font-size: 24px;
    font-weight: 900;
    line-height: 1;
}
.m-noti-badge {
    position: absolute;
    top: -2px;
    right: 4px;
    background: #ff007f;
    color: #fff;
    font-size: 9px;
    font-weight: 900;
    min-width: 15px;
    height: 15px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 8px #ff007f;
}
@media (min-width: 901px) {
    .mobile-nav {
        display: none !important;
    }
}
</style>

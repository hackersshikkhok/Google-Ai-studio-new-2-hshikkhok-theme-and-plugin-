<?php
/**
 * HackersShikkhok - Mobile Bottom App Navigation Dock (PART 10 Specification)
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 *
 * Slots:
 * 1. Home
 * 2. Explore
 * 3. Create (+ Action Bottom Sheet: Write a Post, Ask a Question, Start a Discussion, Submit a Tool)
 * 4. Community (Q&A & Discussions)
 * 5. Profile (Avatar / Login)
 */
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_logged_in = is_user_logged_in();
$current_uid  = get_current_user_id();
$noti_count   = $is_logged_in && class_exists( '\HackersShikkhok\Theme\Modules\Community\NotificationEngine' )
    ? \HackersShikkhok\Theme\Modules\Community\NotificationEngine::get_unread_count( $current_uid )
    : 0;

$curr_uri   = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
$is_home    = is_front_page() || is_home();
$is_explore = str_contains( $curr_uri, '/explore' ) || str_contains( $curr_uri, '/platform-center' ) || str_contains( $curr_uri, '/center' ) || str_contains( $curr_uri, '/tools' ) || str_contains( $curr_uri, '/academy' );
$is_comm    = str_contains( $curr_uri, '/forum' ) || str_contains( $curr_uri, '/community' ) || str_contains( $curr_uri, '/qa' ) || is_post_type_archive( 'hs_question' ) || is_singular( 'hs_question' );
$is_profile = str_contains( $curr_uri, '/profile' ) || is_author() || $is_dash;
?>
<!-- Mobile Bottom Navigation Dock (PART 10 Standard) -->
<nav class="mobile-nav" aria-label="Mobile Navigation Bar">
    <!-- 1. Home -->
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="m-nav-item hs-tap-red <?php echo $is_home ? 'active' : ''; ?>">
        <span class="m-nav-icon" aria-hidden="true">🏠</span>
        <small>Home</small>
    </a>

    <!-- 2. Explore -->
    <a href="<?php echo esc_url( home_url( '/platform-center/' ) ); ?>" class="m-nav-item hs-tap-red <?php echo ( $is_explore && ! $is_home ) ? 'active' : ''; ?>">
        <span class="m-nav-icon" aria-hidden="true">🧭</span>
        <small>Explore</small>
    </a>

    <!-- 3. Center Create Action Button -->
    <button type="button" class="m-nav-center m-nav-center--btn hs-tap-red" onclick="openHsCreateSheet(event)" title="Create New Content" aria-label="Create New Content">
        <span class="m-center-ai-icon" aria-hidden="true">➕</span>
    </button>

    <!-- 4. Community -->
    <a href="<?php echo esc_url( home_url( '/forum/' ) ); ?>" class="m-nav-item hs-tap-red <?php echo $is_comm ? 'active' : ''; ?>">
        <span class="m-nav-icon" aria-hidden="true">💬</span>
        <small>Community</small>
    </a>

    <!-- 5. Profile / Login -->
    <?php if ( $is_logged_in ) : ?>
        <a href="<?php echo esc_url( home_url( '/profile/' ) ); ?>" class="m-nav-item m-nav-profile hs-tap-red <?php echo $is_profile ? 'active' : ''; ?>" title="My Profile">
            <span class="m-nav-avatar-wrap">
                <?php echo get_avatar( $current_uid, 24 ); ?>
                <?php if ( $noti_count > 0 ) : ?>
                    <span class="m-noti-badge"><?php echo (int) $noti_count; ?></span>
                <?php endif; ?>
            </span>
            <small>Profile</small>
        </a>
    <?php else : ?>
        <button type="button" class="m-nav-item m-nav-item--btn hs-tap-red" onclick="openHsAuthModal('login')" title="Profile / Login">
            <span class="m-nav-icon" aria-hidden="true">👤</span>
            <small>Profile</small>
        </button>
    <?php endif; ?>
</nav>

<!-- Mobile Create Action Sheet & Backdrop (PART 10 Specification) -->
<div id="hs-create-sheet-backdrop" class="hs-create-sheet-backdrop" onclick="closeHsCreateSheet()"></div>

<div id="hs-create-action-sheet" class="hs-create-action-sheet" role="dialog" aria-modal="true" aria-labelledby="hs-sheet-title">
    <div class="hs-sheet-header">
        <h3 class="hs-sheet-title" id="hs-sheet-title">
            <span>➕</span> <span>Create Content &amp; Tools</span>
        </h3>
        <button type="button" class="hs-sheet-close-btn" onclick="closeHsCreateSheet()" aria-label="Close">✕</button>
    </div>

    <div class="hs-sheet-grid">
        <!-- 1. Write a Post -->
        <a href="<?php echo esc_url( home_url( '/dashboard/?tab=create' ) ); ?>" class="hs-sheet-item" onclick="closeHsCreateSheet()">
            <span class="hs-sheet-icon">✍️</span>
            <div class="hs-sheet-text">
                <h4>Write a Post</h4>
                <p>Publish an article, code guide, tutorial, or hardware review</p>
            </div>
        </a>

        <!-- 2. Ask a Question -->
        <a href="<?php echo esc_url( home_url( '/forum/?action=ask' ) ); ?>" class="hs-sheet-item" onclick="closeHsCreateSheet()">
            <span class="hs-sheet-icon">❓</span>
            <div class="hs-sheet-text">
                <h4>Ask a Question</h4>
                <p>Get verified solutions from cybersecurity &amp; coding mentors</p>
            </div>
        </a>

        <!-- 3. Start a Discussion -->
        <a href="<?php echo esc_url( home_url( '/forum/?action=new-topic' ) ); ?>" class="hs-sheet-item" onclick="closeHsCreateSheet()">
            <span class="hs-sheet-icon">💬</span>
            <div class="hs-sheet-text">
                <h4>Start a Discussion</h4>
                <p>Exchange ideas, discuss technology news, and connect</p>
            </div>
        </a>

        <!-- 4. Submit a Tool -->
        <a href="<?php echo esc_url( home_url( '/tools/?action=submit' ) ); ?>" class="hs-sheet-item" onclick="closeHsCreateSheet()">
            <span class="hs-sheet-icon">⚡</span>
            <div class="hs-sheet-text">
                <h4>Submit a Tool</h4>
                <p>Add a client-side security or developer tool to 2,024+ directory</p>
            </div>
        </a>
    </div>
</div>

<script>
function openHsCreateSheet(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    var sheet = document.getElementById('hs-create-action-sheet');
    var backdrop = document.getElementById('hs-create-sheet-backdrop');
    if (sheet && backdrop) {
        backdrop.style.display = 'block';
        sheet.style.display = 'block';
        setTimeout(function() {
            sheet.classList.add('is-open');
        }, 10);
    }
}

function closeHsCreateSheet() {
    var sheet = document.getElementById('hs-create-action-sheet');
    var backdrop = document.getElementById('hs-create-sheet-backdrop');
    if (sheet && backdrop) {
        sheet.classList.remove('is-open');
        setTimeout(function() {
            sheet.style.display = 'none';
            backdrop.style.display = 'none';
        }, 300);
    }
}
</script>

<?php
/**
 * Template Name: User Control Dashboard & Student Portal
 * Template Post Type: page
 * Package: HackersShikkhok Theme
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 *
 * 100% Real-Data 8-Tab Operative Dashboard:
 * 1. Overview & Live Activity Timeline
 * 2. My Posts (Live status + Edit in /new-post/)
 * 3. My Q&A & Submitted Answers
 * 4. My Learning (Courses Progress & Verified Certificates)
 * 5. Bookmarks & Saved Tools (Live Remove toggle)
 * 6. Notifications & Alerts (Mark All Read)
 * 7. Cryptographic Wallet Ledger & Cashout Request
 * 8. Profile Settings, Earned Badges & AI Voice
 */

declare(strict_types=1);

get_header();

global $wpdb;
$current_user_id = get_current_user_id();
$is_logged_in    = is_user_logged_in();
$user            = wp_get_current_user();
$rest_nonce      = wp_create_nonce( 'wp_rest' );
$rest_base       = esc_url_raw( rest_url( 'hackersshikkhok/v1' ) );
$login_url       = wp_login_url( home_url( '/dashboard/' ) );
$register_url    = wp_registration_url();

if ( ! $is_logged_in ) : ?>
    <main id="primary" class="hs-dashboard-page" style="background:#080D17; color:#E8EEF7; min-height:80vh; padding:48px 16px;">
        <div style="max-width:620px; margin:0 auto; background:#101827; border:1px solid rgba(32,220,197,0.3); border-radius:20px; padding:36px; text-align:center;">
            <div style="font-size:44px; margin-bottom:12px;">🛡️</div>
            <h1 style="margin:0 0 10px; font-size:1.8rem; color:#E8EEF7;">Operative Dashboard</h1>
            <p style="color:#929FB2; font-size:14px; line-height:1.6; margin:0 0 24px;">
                Sign in to manage your published articles, Q&amp;A topics &amp; answers, enrolled Academy courses, bookmarks, notifications, wallet ledger, and researcher profile.
            </p>
            <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
                <a href="<?php echo esc_url( $login_url ); ?>" style="padding:12px 26px; background:#20DCC5; color:#080D17; font-weight:800; border-radius:10px; text-decoration:none;">Sign In →</a>
                <a href="<?php echo esc_url( $register_url ); ?>" style="padding:12px 24px; background:#172435; border:1px solid rgba(255,255,255,0.14); color:#E8EEF7; font-weight:700; border-radius:10px; text-decoration:none;">Create Account</a>
            </div>
        </div>
    </main>
    <?php
    get_footer();
    return;
endif;

$wallet_summary = class_exists( '\HackersShikkhok\Theme\Modules\Platform\UserEcosystem' )
    ? \HackersShikkhok\Theme\Modules\Platform\UserEcosystem::get_wallet_summary( $current_user_id )
    : array( 'balance_bdt' => 0.0, 'points' => 0 );

$xp      = (int) ( $wallet_summary['points'] ?? 0 );
$balance = (float) ( $wallet_summary['balance_bdt'] ?? 0.0 );
$level   = max( 1, (int) floor( $xp / 500 ) + 1 );
$ranks   = array(
    1 => 'Cyber Cadet',
    2 => 'Packet Analyst',
    3 => 'Network Defender',
    4 => 'Bug Bounty Scout',
    5 => 'Security Specialist',
    6 => 'Lead Threat Hunter',
    7 => 'Principal Cyber Architect',
);
$rank        = $ranks[ min( $level, 7 ) ] ?? 'Principal Cyber Architect';
$xp_next     = $level * 500;
$xp_progress = min( 100, max( 2, (int) round( ( $xp / max( 1, $xp_next ) ) * 100 ) ) );

$user_name    = $user->display_name ?: $user->user_login;
$user_email   = $user->user_email;
$user_bio     = (string) get_user_meta( $current_user_id, 'description', true );
$user_loc     = (string) get_user_meta( $current_user_id, '_hs_user_location', true );
$user_web     = (string) ( $user->user_url ?: '' );
$user_github  = (string) get_user_meta( $current_user_id, '_hs_social_github', true );
$user_twitter = (string) get_user_meta( $current_user_id, '_hs_social_twitter', true );
$user_youtube = (string) get_user_meta( $current_user_id, '_hs_social_youtube', true );
$user_linkedin= (string) get_user_meta( $current_user_id, '_hs_social_linkedin', true );
$user_voice   = (string) ( get_user_meta( $current_user_id, 'hs_ai_voice', true ) ?: get_user_meta( $current_user_id, '_hs_tts_voice_model', true ) ?: 'Ananya' );

// 1. Real My Posts
$my_posts = class_exists( '\HackersShikkhok\Theme\Modules\Platform\UserEcosystem' )
    ? \HackersShikkhok\Theme\Modules\Platform\UserEcosystem::get_user_articles( $current_user_id )
    : array();

// 2. Real My Q&A Topics + My Submitted Answers
$q_posts = get_posts( array(
    'author'         => $current_user_id,
    'post_type'      => 'hs_question',
    'post_status'    => array( 'publish', 'pending', 'draft' ),
    'posts_per_page' => 30,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
$my_questions = array();
$solved_topics_count = 0;
foreach ( $q_posts as $qp ) {
    $is_solved = (bool) get_post_meta( $qp->ID, '_hs_question_solved', true ) || (bool) get_post_meta( $qp->ID, '_hs_accepted_answer_id', true );
    if ( $is_solved ) {
        $solved_topics_count++;
    }
    $my_questions[] = array(
        'id'        => $qp->ID,
        'title'     => $qp->post_title,
        'type'      => get_post_meta( $qp->ID, '_hs_question_type', true ) ?: 'question',
        'category'  => get_post_meta( $qp->ID, '_hs_question_category', true ) ?: 'General',
        'solved'    => $is_solved,
        'answers'   => (int) get_comments_number( $qp->ID ),
        'votes'     => (int) get_post_meta( $qp->ID, '_hs_question_votes', true ),
        'date'      => get_the_date( 'M d, Y', $qp->ID ),
        'ts'        => strtotime( $qp->post_date_gmt ?: $qp->post_date ),
        'permalink' => get_permalink( $qp->ID ),
    );
}

$my_answers = get_comments( array(
    'user_id'   => $current_user_id,
    'post_type' => 'hs_question',
    'status'    => 'approve',
    'number'    => 25,
    'orderby'   => 'comment_date',
    'order'     => 'DESC',
) );
$accepted_answers_count = 0;
foreach ( $my_answers as $ans ) {
    if ( get_comment_meta( (int) $ans->comment_ID, '_hs_is_accepted_solution', true ) ) {
        $accepted_answers_count++;
    }
}

// 3. Real My Courses & Certificates
$my_courses = array();
$enrolled_meta = get_user_meta( $current_user_id, '_hs_enrolled_courses', true );
$enrolled_meta = is_array( $enrolled_meta ) ? array_map( 'intval', $enrolled_meta ) : array();
$prog_table    = $wpdb->prefix . 'hs_courses_progress';
$prog_rows_map = array();
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $prog_table ) ) === $prog_table ) {
    $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$prog_table} WHERE user_id = %d ORDER BY last_activity DESC", $current_user_id ), ARRAY_A );
    foreach ( (array) $rows as $r ) {
        $cid = (int) $r['course_id'];
        $prog_rows_map[ $cid ] = $r;
        if ( ! in_array( $cid, $enrolled_meta, true ) ) {
            $enrolled_meta[] = $cid;
        }
    }
}
foreach ( $enrolled_meta as $cid ) {
    if ( $cid <= 0 ) {
        continue;
    }
    $cpost = get_post( $cid );
    if ( ! $cpost ) {
        continue;
    }
    $row      = $prog_rows_map[ $cid ] ?? null;
    $done_arr = $row ? json_decode( (string) ( $row['completed_lessons'] ?? '[]' ), true ) : array();
    $done_cnt = is_array( $done_arr ) ? count( $done_arr ) : 0;
    $total_l  = max( 1, (int) get_post_meta( $cid, '_hs_curriculum_total_lessons', true ) ?: 10 );
    $pct      = $row ? (int) $row['overall_percent'] : 0;
    $my_courses[] = array(
        'id'       => $cid,
        'title'    => $cpost->post_title,
        'level'    => 'Level ' . ( (int) get_post_meta( $cid, '_hs_level', true ) ?: 1 ),
        'progress' => $pct,
        'lessons'  => $done_cnt . ' of ' . $total_l . ' completed',
        'url'      => get_permalink( $cid ) ?: home_url( '/academy/?course_id=' . urlencode( $cpost->post_name ) ),
        'last_ts'  => $row && ! empty( $row['last_activity'] ) ? strtotime( $row['last_activity'] ) : 0,
    );
}

$my_certificates = array();
$certs_table     = $wpdb->prefix . 'hs_certificates';
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $certs_table ) ) === $certs_table ) {
    $my_certificates = $wpdb->get_results(
        $wpdb->prepare( "SELECT * FROM {$certs_table} WHERE recipient_user_id = %d ORDER BY issued_at DESC", $current_user_id ),
        ARRAY_A
    ) ?: array();
}

// 4. Real Bookmarks
$raw_bookmarks = get_user_meta( $current_user_id, '_hs_user_bookmarks', true );
$my_bookmarks  = is_array( $raw_bookmarks ) ? array_values( $raw_bookmarks ) : array();

// 5. Real Notifications
$raw_notifs = get_user_meta( $current_user_id, '_hs_user_notifications', true );
$my_notifs  = is_array( $raw_notifs ) ? array_values( $raw_notifs ) : array();
$unread_notifs = count( array_filter( $my_notifs, static fn( $n ) => empty( $n['read'] ) ) );

// 6. Real Wallet Ledger History
$ledger_rows  = array();
$ledger_table = $wpdb->prefix . 'hs_wallet_ledger';
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $ledger_table ) ) === $ledger_table ) {
    $ledger_rows = $wpdb->get_results(
        $wpdb->prepare( "SELECT * FROM {$ledger_table} WHERE user_id = %d ORDER BY tx_id DESC LIMIT 25", $current_user_id ),
        ARRAY_A
    ) ?: array();
}

// 7. Real Chronological Activity Feed
$activity_feed = array();
foreach ( array_slice( $my_posts, 0, 5 ) as $mp ) {
    $activity_feed[] = array(
        'icon' => '✍️',
        'text' => 'Article (' . strtoupper( (string) $mp['status'] ) . '): ' . $mp['title'],
        'date' => (string) $mp['date'],
        'ts'   => strtotime( (string) $mp['date'] ) ?: 0,
        'url'  => (string) ( $mp['permalink'] ?? '#' ),
    );
}
foreach ( array_slice( $my_questions, 0, 5 ) as $mq ) {
    $activity_feed[] = array(
        'icon' => 'discussion' === $mq['type'] ? '💬' : '❓',
        'text' => ( 'discussion' === $mq['type'] ? 'Started Discussion: ' : 'Asked Question: ' ) . $mq['title'],
        'date' => $mq['date'],
        'ts'   => $mq['ts'],
        'url'  => $mq['permalink'],
    );
}
foreach ( array_slice( $my_answers, 0, 5 ) as $ma ) {
    $activity_feed[] = array(
        'icon' => '✓',
        'text' => 'Answered: ' . get_the_title( (int) $ma->comment_post_ID ),
        'date' => gmdate( 'M d, Y', strtotime( $ma->comment_date ) ),
        'ts'   => strtotime( $ma->comment_date ),
        'url'  => get_permalink( (int) $ma->comment_post_ID ),
    );
}
foreach ( array_slice( $ledger_rows, 0, 5 ) as $lr ) {
    $activity_feed[] = array(
        'icon' => '⚡',
        'text' => (string) ( $lr['description'] ?? 'Wallet transaction' ) . ( (int) $lr['points_delta'] > 0 ? ' (+' . (int) $lr['points_delta'] . ' XP)' : '' ),
        'date' => gmdate( 'M d, Y', strtotime( (string) $lr['created_at'] ) ),
        'ts'   => strtotime( (string) $lr['created_at'] ),
        'url'  => home_url( '/dashboard/?tab=wallet' ),
    );
}
usort( $activity_feed, static fn( $a, $b ) => ( $b['ts'] ?? 0 ) <=> ( $a['ts'] ?? 0 ) );
$activity_feed = array_slice( $activity_feed, 0, 12 );

// Earned Badges
$earned_badges = array();
if ( count( $my_posts ) >= 1 ) {
    $earned_badges[] = array( 'icon' => '✍️', 'name' => 'Published Author', 'desc' => 'Published an article or tutorial' );
}
if ( count( $my_answers ) >= 1 || $accepted_answers_count >= 1 ) {
    $earned_badges[] = array( 'icon' => '🛡️', 'name' => 'Community Solver', 'desc' => 'Contributed answers in Q&A' );
}
if ( count( $my_courses ) >= 1 ) {
    $earned_badges[] = array( 'icon' => '🎓', 'name' => 'Academy Scholar', 'desc' => 'Enrolled in Cyber Academy courses' );
}
if ( $xp >= 500 ) {
    $earned_badges[] = array( 'icon' => '⚡', 'name' => 'Rank Tier ' . $level, 'desc' => 'Surpassed 500+ Scholar XP' );
}

$active_tab = sanitize_key( $_GET['tab'] ?? 'overview' );
if ( 'create' === $active_tab ) {
    $active_tab = 'posts';
}
$valid_tabs = array( 'overview', 'posts', 'qa', 'courses', 'bookmarks', 'notifications', 'wallet', 'profile' );
if ( ! in_array( $active_tab, $valid_tabs, true ) ) {
    $active_tab = 'overview';
}
?>

<main id="primary" class="hs-dashboard-page" style="background:#080D17; color:#E8EEF7; min-height:100vh; padding:32px 16px 80px;">
    <div style="max-width:1300px; margin:0 auto;">

        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#929FB2; margin-bottom:20px; font-family:var(--font-mono);">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#20DCC5; text-decoration:none;">Home</a>
            <span>/</span>
            <span style="color:#E8EEF7; font-weight:700;">Operative Dashboard</span>
        </nav>

        <!-- Profile Header Card -->
        <section style="background:linear-gradient(135deg, #101827 0%, #172435 100%); border:1px solid rgba(32,220,197,0.3); border-radius:24px; padding:28px; margin-bottom:24px; position:relative; overflow:hidden;">
            <div style="position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg, #20DCC5, #8B7CFF, #EABF57);"></div>

            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;">
                <div style="display:flex; align-items:center; gap:18px; flex-wrap:wrap;">
                    <?php echo get_avatar( $current_user_id, 78, '', $user_name, array( 'style' => 'border-radius:50%; border:3px solid #20DCC5; flex-shrink:0;' ) ); ?>
                    <div>
                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            <h1 style="font-size:1.7rem; font-weight:900; color:#E8EEF7; margin:0; line-height:1.2;">
                                <?php echo esc_html( $user_name ); ?>
                            </h1>
                            <span style="background:rgba(32,220,197,0.15); color:#20DCC5; border:1px solid rgba(32,220,197,0.4); font-size:11px; font-weight:800; padding:2px 10px; border-radius:6px; font-family:var(--font-mono);">
                                👑 <?php echo esc_html( $rank ); ?> (Lv.<?php echo (int) $level; ?>)
                            </span>
                            <span class="hs-tabular" style="background:rgba(234,191,87,0.15); color:#EABF57; border:1px solid rgba(234,191,87,0.4); font-size:11px; font-weight:800; padding:2px 10px; border-radius:6px; font-family:var(--font-mono);">
                                ⚡ <?php echo number_format( (float) $xp ); ?> XP
                            </span>
                        </div>
                        <div style="color:#929FB2; font-size:13px; font-family:var(--font-mono); margin:6px 0 8px;">
                            <span>📧 <?php echo esc_html( $user_email ); ?></span>
                            <?php if ( $user_loc ) : ?> · <span>📍 <?php echo esc_html( $user_loc ); ?></span><?php endif; ?>
                        </div>
                        <p style="color:#E8EEF7; font-size:13px; margin:0; max-width:640px; line-height:1.5;">
                            <?php echo esc_html( $user_bio ?: 'Set your specialization and bio in the Profile & Badges tab.' ); ?>
                        </p>
                    </div>
                </div>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a href="<?php echo esc_url( home_url( '/new-post/' ) ); ?>" style="padding:10px 18px; background:#20DCC5; color:#080D17; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none;">
                        ✍️ Write Post
                    </a>
                    <a href="<?php echo esc_url( home_url( '/forum/?action=ask' ) ); ?>" style="padding:10px 18px; background:rgba(139,124,255,0.15); border:1px solid #8B7CFF; color:#8B7CFF; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none;">
                        ❓ Ask Question
                    </a>
                    <a href="<?php echo esc_url( home_url( '/profile/' ) ); ?>" style="padding:10px 16px; background:#080D17; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; font-weight:700; font-size:13px; border-radius:10px; text-decoration:none;">
                        👤 Social Profile
                    </a>
                </div>
            </div>

            <div style="margin-top:20px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.08);">
                <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:6px; font-family:var(--font-mono);">
                    <span style="color:#20DCC5; font-weight:700;">XP Progress: <?php echo (int) $xp; ?> / <?php echo (int) $xp_next; ?> XP</span>
                    <span style="color:#929FB2;">Level <?php echo (int) ( $level + 1 ); ?> in <?php echo max( 0, $xp_next - $xp ); ?> XP</span>
                </div>
                <div style="width:100%; height:8px; background:rgba(255,255,255,0.08); border-radius:999px; overflow:hidden;">
                    <div style="width:<?php echo (int) $xp_progress; ?>%; height:100%; background:linear-gradient(90deg, #20DCC5 0%, #8B7CFF 100%); border-radius:999px;"></div>
                </div>
            </div>
        </section>

        <!-- 8-Tab Navigation -->
        <nav class="hs-dashboard-nav-tabs" style="display:flex; gap:6px; overflow-x:auto; padding-bottom:6px; margin-bottom:24px; border-bottom:1px solid rgba(255,255,255,0.08);">
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'overview' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="overview">📊 Overview</button>
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'posts' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="posts">✍️ My Posts <span class="hs-badge"><?php echo count( $my_posts ); ?></span></button>
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'qa' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="qa">❓ My Q&amp;A <span class="hs-badge"><?php echo count( $my_questions ) + count( $my_answers ); ?></span></button>
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'courses' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="courses">🎓 My Learning <span class="hs-badge"><?php echo count( $my_courses ); ?></span></button>
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'bookmarks' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="bookmarks">🔖 Bookmarks <span class="hs-badge" id="hs-bkm-tab-count"><?php echo count( $my_bookmarks ); ?></span></button>
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'notifications' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="notifications">🔔 Notifications <span class="hs-badge" id="hs-notif-tab-count"><?php echo (int) $unread_notifs; ?></span></button>
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'wallet' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="wallet">💳 Wallet &amp; Cashout</button>
            <button type="button" class="hs-dash-tab-btn <?php echo ( 'profile' === $active_tab ) ? 'hs-active' : ''; ?>" data-tab="profile">⚙️ Profile &amp; Badges</button>
        </nav>

        <!-- TAB 1: OVERVIEW -->
        <div class="hs-dash-tab-content" id="tab-overview" style="<?php echo ( 'overview' === $active_tab ) ? 'display:block;' : 'display:none;'; ?>">
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:24px;">
                <div style="background:#101827; border:1px solid rgba(32,220,197,0.3); border-radius:16px; padding:20px;">
                    <span style="font-size:11px; font-weight:700; color:#929FB2; text-transform:uppercase;">Wallet Balance</span>
                    <div class="hs-tabular" style="font-size:1.8rem; font-weight:900; color:#20DCC5; margin-top:6px;">BDT <?php echo number_format( $balance, 2 ); ?></div>
                </div>
                <div style="background:#101827; border:1px solid rgba(139,124,255,0.35); border-radius:16px; padding:20px;">
                    <span style="font-size:11px; font-weight:700; color:#929FB2; text-transform:uppercase;">Scholar XP</span>
                    <div class="hs-tabular" style="font-size:1.8rem; font-weight:900; color:#8B7CFF; margin-top:6px;"><?php echo number_format( (float) $xp ); ?></div>
                </div>
                <div style="background:#101827; border:1px solid rgba(234,191,87,0.3); border-radius:16px; padding:20px;">
                    <span style="font-size:11px; font-weight:700; color:#929FB2; text-transform:uppercase;">Published &amp; Draft Posts</span>
                    <div class="hs-tabular" style="font-size:1.8rem; font-weight:900; color:#EABF57; margin-top:6px;"><?php echo count( $my_posts ); ?></div>
                </div>
                <div style="background:#101827; border:1px solid rgba(32,220,197,0.3); border-radius:16px; padding:20px;">
                    <span style="font-size:11px; font-weight:700; color:#929FB2; text-transform:uppercase;">Accepted Q&amp;A Solutions</span>
                    <div class="hs-tabular" style="font-size:1.8rem; font-weight:900; color:#20DCC5; margin-top:6px;"><?php echo (int) ( $accepted_answers_count + $solved_topics_count ); ?></div>
                </div>
            </div>

            <div style="background:#101827; border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:24px;">
                <h3 style="color:#E8EEF7; font-size:1.15rem; font-weight:800; margin:0 0 16px;">📈 Recent Real Activity</h3>
                <?php if ( empty( $activity_feed ) ) : ?>
                    <p style="color:#929FB2; font-size:13px; margin:0;">No platform activity recorded yet. Start by publishing an article, answering a forum question, or completing an Academy lesson!</p>
                <?php else : ?>
                    <div style="display:flex; flex-direction:column; gap:10px; font-size:13px;">
                        <?php foreach ( $activity_feed as $item ) : ?>
                            <a href="<?php echo esc_url( $item['url'] ); ?>" style="padding:12px 14px; background:#172435; border-radius:10px; display:flex; justify-content:space-between; align-items:center; gap:12px; text-decoration:none; color:#E8EEF7;">
                                <span><?php echo esc_html( $item['icon'] . ' ' . $item['text'] ); ?></span>
                                <span style="color:#929FB2; font-size:12px; flex-shrink:0;"><?php echo esc_html( $item['date'] ); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB 2: MY POSTS -->
        <div class="hs-dash-tab-content" id="tab-posts" style="<?php echo ( 'posts' === $active_tab ) ? 'display:block;' : 'display:none;'; ?>">
            <div style="background:#101827; border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:24px;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
                    <h3 style="color:#E8EEF7; font-size:1.15rem; font-weight:800; margin:0;">📋 My Articles &amp; Tutorials</h3>
                    <a href="<?php echo esc_url( home_url( '/new-post/' ) ); ?>" style="padding:9px 18px; background:#20DCC5; color:#080D17; font-weight:800; font-size:12px; border-radius:8px; text-decoration:none;">+ Write New Post</a>
                </div>
                <?php if ( empty( $my_posts ) ) : ?>
                    <div style="background:#172435; border-radius:14px; padding:28px; text-align:center;">
                        <p style="color:#929FB2; margin:0 0 14px;">You haven't published or saved any articles yet.</p>
                        <a href="<?php echo esc_url( home_url( '/new-post/' ) ); ?>" style="padding:10px 20px; background:#20DCC5; color:#080D17; font-weight:800; border-radius:8px; text-decoration:none;">Write Your First Article →</a>
                    </div>
                <?php else : ?>
                    <div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse:collapse; font-size:13px;">
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.1); color:#929FB2; text-align:left;">
                                    <th style="padding:10px 8px;">Title</th>
                                    <th style="padding:10px 8px;">Type</th>
                                    <th style="padding:10px 8px;">Status</th>
                                    <th style="padding:10px 8px;">Views</th>
                                    <th style="padding:10px 8px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $my_posts as $p ) : ?>
                                    <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                        <td style="padding:12px 8px; font-weight:700; color:#E8EEF7;"><?php echo esc_html( $p['title'] ); ?></td>
                                        <td style="padding:12px 8px;"><code style="background:rgba(255,255,255,0.06); padding:2px 8px; border-radius:4px; font-size:11px;"><?php echo esc_html( $p['type'] ); ?></code></td>
                                        <td style="padding:12px 8px;"><span style="color:#20DCC5; font-weight:700; text-transform:uppercase;"><?php echo esc_html( $p['status'] ); ?></span></td>
                                        <td style="padding:12px 8px;" class="hs-tabular"><?php echo number_format( (float) $p['views'] ); ?></td>
                                        <td style="padding:12px 8px; white-space:nowrap;">
                                            <a href="<?php echo esc_url( $p['permalink'] ?? '#' ); ?>" style="color:#20DCC5; text-decoration:none; font-weight:700; margin-right:10px;">View</a>
                                            <a href="<?php echo esc_url( home_url( '/new-post/?edit=' . (int) $p['id'] ) ); ?>" style="color:#8B7CFF; text-decoration:none; font-weight:700;">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB 3: MY Q&A & ANSWERS -->
        <div class="hs-dash-tab-content" id="tab-qa" style="<?php echo ( 'qa' === $active_tab ) ? 'display:block;' : 'display:none;'; ?>">
            <div style="display:flex; flex-direction:column; gap:20px;">
                <div style="background:#101827; border:1px solid rgba(139,124,255,0.3); border-radius:20px; padding:24px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:16px;">
                        <h3 style="color:#E8EEF7; font-size:1.15rem; font-weight:800; margin:0;">❓ My Questions &amp; Discussions (<?php echo count( $my_questions ); ?>)</h3>
                        <a href="<?php echo esc_url( home_url( '/forum/?action=ask' ) ); ?>" style="padding:8px 16px; background:#8B7CFF; color:#fff; font-weight:800; font-size:12px; border-radius:8px; text-decoration:none;">+ Ask Question</a>
                    </div>
                    <?php if ( empty( $my_questions ) ) : ?>
                        <p style="color:#929FB2; font-size:13px; margin:0;">You haven't asked a question or started a discussion yet.</p>
                    <?php else : ?>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <?php foreach ( $my_questions as $q ) : ?>
                                <div style="background:#172435; border-radius:12px; padding:14px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                                    <div>
                                        <div style="display:flex; gap:8px; align-items:center; margin-bottom:6px; flex-wrap:wrap;">
                                            <span style="font-size:11px; color:#8B7CFF; font-family:var(--font-mono); background:rgba(139,124,255,0.14); padding:2px 8px; border-radius:4px;"><?php echo esc_html( strtoupper( $q['type'] ) ); ?></span>
                                            <span style="font-size:11px; color:#929FB2;"><?php echo esc_html( $q['category'] ); ?></span>
                                            <?php if ( $q['solved'] ) : ?><span style="font-size:11px; color:#20DCC5; font-weight:800;">✓ Solved</span><?php endif; ?>
                                        </div>
                                        <h4 style="margin:0; font-size:15px; color:#E8EEF7;"><?php echo esc_html( $q['title'] ); ?></h4>
                                    </div>
                                    <div style="display:flex; gap:14px; align-items:center; font-size:12px; color:#929FB2;">
                                        <span>▲ <?php echo (int) $q['votes']; ?></span>
                                        <span>💬 <?php echo (int) $q['answers']; ?></span>
                                        <a href="<?php echo esc_url( $q['permalink'] ); ?>" style="color:#20DCC5; font-weight:700; text-decoration:none;">Open →</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="background:#101827; border:1px solid rgba(32,220,197,0.25); border-radius:20px; padding:24px;">
                    <h3 style="color:#E8EEF7; font-size:1.15rem; font-weight:800; margin:0 0 14px;">💡 My Submitted Answers (<?php echo count( $my_answers ); ?>)</h3>
                    <?php if ( empty( $my_answers ) ) : ?>
                        <p style="color:#929FB2; font-size:13px; margin:0;">No answers posted yet. Help fellow engineers in the <a href="<?php echo esc_url( home_url( '/forum/' ) ); ?>" style="color:#20DCC5;">Q&amp;A Forum</a> to earn XP!</p>
                    <?php else : ?>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <?php foreach ( $my_answers as $ans ) :
                                $is_acc = (bool) get_comment_meta( (int) $ans->comment_ID, '_hs_is_accepted_solution', true );
                            ?>
                                <a href="<?php echo esc_url( get_comment_link( $ans ) ); ?>" style="background:#172435; border-radius:12px; padding:14px; text-decoration:none; color:#E8EEF7; display:block;">
                                    <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:6px;">
                                        <strong style="color:#20DCC5;"><?php echo esc_html( get_the_title( (int) $ans->comment_post_ID ) ); ?></strong>
                                        <span><?php echo $is_acc ? '<strong style="color:#20DCC5;">✓ Accepted Solution</strong>' : esc_html( gmdate( 'M d, Y', strtotime( $ans->comment_date ) ) ); ?></span>
                                    </div>
                                    <p style="margin:0; font-size:13px; color:#929FB2;"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $ans->comment_content ), 24 ) ); ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- TAB 4: MY LEARNING & CERTIFICATES -->
        <div class="hs-dash-tab-content" id="tab-courses" style="<?php echo ( 'courses' === $active_tab ) ? 'display:block;' : 'display:none;'; ?>">
            <div style="display:flex; flex-direction:column; gap:20px;">
                <div style="background:#101827; border:1px solid rgba(234,191,87,0.3); border-radius:20px; padding:24px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:18px;">
                        <h3 style="color:#E8EEF7; font-size:1.15rem; font-weight:800; margin:0;">🎓 Enrolled Courses</h3>
                        <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" style="padding:8px 16px; background:#EABF57; color:#080D17; font-weight:800; font-size:12px; border-radius:8px; text-decoration:none;">Explore Academy</a>
                    </div>
                    <?php if ( empty( $my_courses ) ) : ?>
                        <p style="color:#929FB2; font-size:13px; margin:0;">You are not enrolled in any courses yet. Browse the <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" style="color:#20DCC5;">Cyber Academy</a> to start learning.</p>
                    <?php else : ?>
                        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px;">
                            <?php foreach ( $my_courses as $c ) : ?>
                                <div style="background:#172435; border-radius:14px; padding:18px;">
                                    <span style="font-size:11px; color:#EABF57; font-family:var(--font-mono); font-weight:800;"><?php echo esc_html( $c['level'] ); ?></span>
                                    <h4 style="color:#E8EEF7; font-size:1.05rem; margin:8px 0 10px;"><?php echo esc_html( $c['title'] ); ?></h4>
                                    <div style="display:flex; justify-content:space-between; font-size:12px; color:#929FB2; margin-bottom:6px;">
                                        <span><?php echo (int) $c['progress']; ?>% Complete</span>
                                        <span><?php echo esc_html( $c['lessons'] ); ?></span>
                                    </div>
                                    <div style="width:100%; height:6px; background:rgba(255,255,255,0.08); border-radius:999px; overflow:hidden; margin-bottom:14px;">
                                        <div style="width:<?php echo (int) $c['progress']; ?>%; height:100%; background:#EABF57;"></div>
                                    </div>
                                    <a href="<?php echo esc_url( $c['url'] ); ?>" style="display:inline-block; padding:8px 14px; background:#EABF57; color:#080D17; font-weight:800; font-size:12px; border-radius:8px; text-decoration:none;">Resume Course →</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="background:#101827; border:1px solid rgba(32,220,197,0.3); border-radius:20px; padding:24px;">
                    <h3 style="color:#E8EEF7; font-size:1.15rem; font-weight:800; margin:0 0 14px;">📜 Earned Certificates (<?php echo count( $my_certificates ); ?>)</h3>
                    <?php if ( empty( $my_certificates ) ) : ?>
                        <p style="color:#929FB2; font-size:13px; margin:0;">Complete 100% of a course's lessons and pass its module quiz to earn a cryptographically signed certificate.</p>
                    <?php else : ?>
                        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:14px;">
                            <?php foreach ( $my_certificates as $cert ) : ?>
                                <div style="background:#172435; border:1px solid rgba(32,220,197,0.28); border-radius:12px; padding:16px;">
                                    <div style="color:#20DCC5; font-size:11px; font-family:var(--font-mono); font-weight:800;"><?php echo esc_html( (string) $cert['cert_code'] ); ?></div>
                                    <h4 style="color:#E8EEF7; margin:6px 0; font-size:15px;"><?php echo esc_html( (string) $cert['course_title_snapshot'] ); ?></h4>
                                    <div style="color:#929FB2; font-size:12px; margin-bottom:10px;">Issued <?php echo esc_html( gmdate( 'M d, Y', strtotime( (string) $cert['issued_at'] ) ) ); ?></div>
                                    <a href="<?php echo esc_url( home_url( '/verify-certificate/?cert_id=' . urlencode( (string) $cert['cert_code'] ) ) ); ?>" style="color:#20DCC5; font-size:12px; font-weight:700; text-decoration:none;">Verify Credential →</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- TAB 5: BOOKMARKS -->
        <div class="hs-dash-tab-content" id="tab-bookmarks" style="<?php echo ( 'bookmarks' === $active_tab ) ? 'display:block;' : 'display:none;'; ?>">
            <div style="background:#101827; border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:24px;">
                <h3 style="color:#E8EEF7; font-size:1.15rem; font-weight:800; margin:0 0 18px;">🔖 Saved Articles, Courses &amp; Bookmarked Tools</h3>
                <?php if ( empty( $my_bookmarks ) ) : ?>
                    <p style="color:#929FB2; font-size:13px; margin:0;">No saved items yet. Bookmark tools in the <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="color:#20DCC5;">Tools Directory</a> or courses in the <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" style="color:#20DCC5;">Academy</a>.</p>
                <?php else : ?>
                    <div id="hs-dash-bookmarks-list" style="display:flex; flex-direction:column; gap:12px;">
                        <?php foreach ( $my_bookmarks as $bm ) : ?>
                            <div class="hs-dash-bm-row" data-item-id="<?php echo esc_attr( (string) ( $bm['item_id'] ?? '' ) ); ?>" style="background:#172435; border-radius:12px; padding:14px 16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                                <div>
                                    <span style="font-size:10px; color:#20DCC5; font-family:var(--font-mono); font-weight:800; text-transform:uppercase;"><?php echo esc_html( (string) ( $bm['item_type'] ?? 'saved' ) ); ?></span>
                                    <h4 style="margin:4px 0 0; color:#E8EEF7; font-size:15px;"><?php echo esc_html( (string) ( $bm['title'] ?? 'Saved Item' ) ); ?></h4>
                                </div>
                                <div style="display:flex; gap:12px; align-items:center;">
                                    <a href="<?php echo esc_url( (string) ( $bm['url'] ?? '#' ) ); ?>" style="color:#20DCC5; font-weight:700; text-decoration:none; font-size:13px;">Open →</a>
                                    <button type="button" class="hs-remove-bookmark-btn" data-id="<?php echo esc_attr( (string) ( $bm['item_id'] ?? '' ) ); ?>" style="background:rgba(244,63,94,0.12); border:1px solid rgba(244,63,94,0.3); color:#fb7185; border-radius:6px; padding:5px 10px; font-size:11px; font-weight:700; cursor:pointer;">Remove</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB 6: NOTIFICATIONS -->
        <div class="hs-dash-tab-content" id="tab-notifications" style="<?php echo ( 'notifications' === $active_tab ) ? 'display:block;' : 'display:none;'; ?>">
            <div style="background:#101827; border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:24px;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:18px;">
                    <h3 style="color:#E8EEF7; font-size:1.15rem; font-weight:800; margin:0;">🔔 Platform Notifications</h3>
                    <button type="button" id="hs-mark-notifs-read-btn" style="padding:8px 14px; background:#172435; border:1px solid #20DCC5; color:#20DCC5; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;">✓ Mark All Read</button>
                </div>
                <?php if ( empty( $my_notifs ) ) : ?>
                    <p style="color:#929FB2; font-size:13px; margin:0;">No new notifications at the moment.</p>
                <?php else : ?>
                    <div id="hs-dash-notifs-list" style="display:flex; flex-direction:column; gap:10px;">
                        <?php foreach ( $my_notifs as $nf ) :
                            $is_unread = empty( $nf['read'] );
                            $nf_time   = $nf['time_ago'] ?? ( $nf['timestamp'] ?? 'Recently' );
                            $nf_body   = $nf['message'] ?? ( $nf['body'] ?? '' );
                            $nf_icon   = $nf['icon'] ?? '🔔';
                        ?>
                            <a href="<?php echo esc_url( (string) ( $nf['url'] ?? '#' ) ); ?>" class="hs-dash-notif-row" style="display:flex; align-items:flex-start; gap:12px; text-decoration:none; background:#172435; border-left:3px solid <?php echo $is_unread ? '#20DCC5' : 'transparent'; ?>; border-radius:10px; padding:14px;">
                                <span style="width:36px; height:36px; border-radius:50%; background:rgba(32,220,197,0.12); display:inline-flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;"><?php echo esc_html( (string) $nf_icon ); ?></span>
                                <div style="flex:1; min-width:0;">
                                    <div style="display:flex; justify-content:space-between; gap:8px; font-size:13px; margin-bottom:4px;">
                                        <strong style="color:#E8EEF7;"><?php echo esc_html( (string) ( $nf['title'] ?? 'Alert' ) ); ?></strong>
                                        <span style="color:#929FB2; font-size:11px; flex-shrink:0;"><?php echo esc_html( (string) $nf_time ); ?></span>
                                    </div>
                                    <p style="margin:0; color:#929FB2; font-size:12px; line-height:1.45;"><?php echo esc_html( (string) $nf_body ); ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB 7: WALLET LEDGER & CASHOUT -->
        <div class="hs-dash-tab-content" id="tab-wallet" style="<?php echo ( 'wallet' === $active_tab ) ? 'display:block;' : 'display:none;'; ?>">
            <div style="background:#101827; border:1px solid rgba(32,220,197,0.3); border-radius:20px; padding:26px;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:22px;">
                    <div>
                        <h2 style="color:#E8EEF7; font-size:1.35rem; font-weight:800; margin:0 0 4px;">💳 Wallet Ledger &amp; Payouts</h2>
                        <p style="color:#929FB2; font-size:13px; margin:0;">Authoritative SQL-backed balance earned from tutorials, accepted solutions, tools, and CTF bounties.</p>
                    </div>
                    <div style="background:#172435; padding:12px 20px; border-radius:14px; border:1px solid #20DCC5;">
                        <span style="font-size:11px; color:#929FB2; text-transform:uppercase; font-weight:700; display:block;">Available Balance</span>
                        <span id="hs-wallet-live-balance" class="hs-tabular" style="font-size:1.7rem; font-weight:900; color:#20DCC5;">BDT <?php echo number_format( $balance, 2 ); ?></span>
                    </div>
                </div>

                <!-- Withdrawal Form -->
                <form id="hs-withdraw-form" style="background:#172435; border-radius:14px; padding:18px; margin-bottom:22px;">
                    <h4 style="color:#E8EEF7; font-size:14px; margin:0 0 12px;">💸 Request Cashout (Minimum BDT 100.00)</h4>
                    <div class="hs-dash-2col" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:12px;">
                        <select id="hs-wdr-method" style="padding:10px; background:#080D17; border:1px solid rgba(255,255,255,0.14); color:#E8EEF7; border-radius:8px;">
                            <option value="bKash">bKash</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Rocket">Rocket</option>
                        </select>
                        <input type="text" id="hs-wdr-number" required placeholder="Wallet Number (e.g. 017XXXXXXXX)" style="padding:10px; background:#080D17; border:1px solid rgba(255,255,255,0.14); color:#E8EEF7; border-radius:8px;" />
                        <input type="number" id="hs-wdr-amount" min="100" step="1" required placeholder="Amount (BDT)" style="padding:10px; background:#080D17; border:1px solid rgba(255,255,255,0.14); color:#E8EEF7; border-radius:8px;" />
                    </div>
                    <button type="submit" style="min-height:42px; padding:10px 20px; background:#20DCC5; color:#080D17; font-weight:800; border:none; border-radius:8px; cursor:pointer; font-size:13px;">Submit Withdrawal Request</button>
                    <div id="hs-wdr-msg" style="display:none; margin-top:10px; font-size:13px; font-weight:700;"></div>
                </form>

                <!-- Ledger Table -->
                <h4 style="color:#E8EEF7; font-size:14px; margin:0 0 12px;">📜 Recent Ledger Transactions</h4>
                <?php if ( empty( $ledger_rows ) ) : ?>
                    <p style="color:#929FB2; font-size:13px; margin:0;">No wallet transactions yet.</p>
                <?php else : ?>
                    <div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse:collapse; font-size:12px;">
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.1); color:#929FB2; text-align:left;">
                                    <th style="padding:8px;">Date</th>
                                    <th style="padding:8px;">Type</th>
                                    <th style="padding:8px;">Description</th>
                                    <th style="padding:8px;">BDT</th>
                                    <th style="padding:8px;">XP</th>
                                    <th style="padding:8px;">Balance After</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $ledger_rows as $lr ) : ?>
                                    <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                        <td style="padding:10px 8px; color:#929FB2;"><?php echo esc_html( gmdate( 'M d, Y', strtotime( (string) $lr['created_at'] ) ) ); ?></td>
                                        <td style="padding:10px 8px; color:#8B7CFF; text-transform:uppercase; font-weight:700;"><?php echo esc_html( (string) $lr['tx_type'] ); ?></td>
                                        <td style="padding:10px 8px; color:#E8EEF7;"><?php echo esc_html( (string) $lr['description'] ); ?></td>
                                        <td style="padding:10px 8px; color:#20DCC5;" class="hs-tabular"><?php echo number_format( (float) $lr['amount_bdt'], 2 ); ?></td>
                                        <td style="padding:10px 8px; color:#EABF57;" class="hs-tabular">+<?php echo (int) $lr['points_delta']; ?></td>
                                        <td style="padding:10px 8px; color:#E8EEF7;" class="hs-tabular"><?php echo number_format( (float) $lr['balance_after'], 2 ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB 8: PROFILE & BADGES -->
        <div class="hs-dash-tab-content" id="tab-profile" style="<?php echo ( 'profile' === $active_tab ) ? 'display:block;' : 'display:none;'; ?>">
            <div style="background:#101827; border:1px solid rgba(32,220,197,0.3); border-radius:20px; padding:26px;">
                <h2 style="color:#E8EEF7; font-size:1.35rem; font-weight:800; margin:0 0 6px;">⚙️ Profile, Badges &amp; AI Voice</h2>
                <p style="color:#929FB2; font-size:13px; margin:0 0 20px;">Manage your public researcher profile, social links, and preferred voice reader.</p>

                <?php if ( ! empty( $earned_badges ) ) : ?>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(190px, 1fr)); gap:12px; margin-bottom:24px;">
                        <?php foreach ( $earned_badges as $bd ) : ?>
                            <div style="background:#172435; border:1px solid rgba(32,220,197,0.28); border-radius:12px; padding:12px; display:flex; align-items:center; gap:10px;">
                                <span style="font-size:24px;"><?php echo esc_html( $bd['icon'] ); ?></span>
                                <div>
                                    <div style="color:#E8EEF7; font-weight:800; font-size:13px;"><?php echo esc_html( $bd['name'] ); ?></div>
                                    <div style="color:#929FB2; font-size:11px;"><?php echo esc_html( $bd['desc'] ); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div id="hs-profile-alert" style="display:none; padding:12px 16px; border-radius:8px; margin-bottom:18px; font-size:13px; font-weight:600;"></div>

                <form id="hs-profile-form" style="display:flex; flex-direction:column; gap:16px;">
                    <div class="hs-dash-2col" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#E8EEF7; margin-bottom:6px;">Display Name:</label>
                            <input type="text" id="hs-prof-name" value="<?php echo esc_attr( $user_name ); ?>" style="width:100%; box-sizing:border-box; background:#172435; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:11px 14px; border-radius:8px; font-size:14px;" />
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#E8EEF7; margin-bottom:6px;">Location / Region:</label>
                            <input type="text" id="hs-prof-loc" value="<?php echo esc_attr( $user_loc ); ?>" style="width:100%; box-sizing:border-box; background:#172435; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:11px 14px; border-radius:8px; font-size:14px;" />
                        </div>
                    </div>

                    <div class="hs-dash-2col" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#E8EEF7; margin-bottom:6px;">Website URL:</label>
                            <input type="url" id="hs-prof-web" value="<?php echo esc_attr( $user_web ); ?>" placeholder="https://..." style="width:100%; box-sizing:border-box; background:#172435; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:11px 14px; border-radius:8px; font-size:14px;" />
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#E8EEF7; margin-bottom:6px;">AI Voice Persona:</label>
                            <select id="hs-prof-voice" style="width:100%; box-sizing:border-box; background:#172435; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:11px 14px; border-radius:8px; font-size:14px;">
                                <option value="Ananya" <?php selected( $user_voice, 'Ananya' ); ?>>Ananya (Natural Reader)</option>
                                <option value="Aarav" <?php selected( $user_voice, 'Aarav' ); ?>>Aarav (Technical Host)</option>
                                <option value="Aria" <?php selected( $user_voice, 'Aria' ); ?>>Aria (English Studio)</option>
                            </select>
                        </div>
                    </div>

                    <div class="hs-dash-2col" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#E8EEF7; margin-bottom:6px;">GitHub Username / URL:</label>
                            <input type="text" id="hs-prof-github" value="<?php echo esc_attr( $user_github ); ?>" style="width:100%; box-sizing:border-box; background:#172435; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:11px 14px; border-radius:8px; font-size:14px;" />
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#E8EEF7; margin-bottom:6px;">Twitter / X Handle:</label>
                            <input type="text" id="hs-prof-twitter" value="<?php echo esc_attr( $user_twitter ); ?>" style="width:100%; box-sizing:border-box; background:#172435; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:11px 14px; border-radius:8px; font-size:14px;" />
                        </div>
                    </div>

                    <div class="hs-dash-2col" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#E8EEF7; margin-bottom:6px;">YouTube Channel:</label>
                            <input type="text" id="hs-prof-youtube" value="<?php echo esc_attr( $user_youtube ); ?>" style="width:100%; box-sizing:border-box; background:#172435; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:11px 14px; border-radius:8px; font-size:14px;" />
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#E8EEF7; margin-bottom:6px;">LinkedIn Profile:</label>
                            <input type="text" id="hs-prof-linkedin" value="<?php echo esc_attr( $user_linkedin ); ?>" style="width:100%; box-sizing:border-box; background:#172435; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:11px 14px; border-radius:8px; font-size:14px;" />
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#E8EEF7; margin-bottom:6px;">Bio &amp; Specialization:</label>
                        <textarea id="hs-prof-bio" rows="3" style="width:100%; box-sizing:border-box; background:#172435; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:12px 14px; border-radius:8px; font-size:13px; line-height:1.6;"><?php echo esc_textarea( $user_bio ); ?></textarea>
                    </div>

                    <button type="button" id="hs-save-profile-btn" style="min-height:46px; background:#20DCC5; color:#080D17; border:none; padding:12px 26px; font-weight:900; border-radius:10px; cursor:pointer; font-size:14px; align-self:flex-start;">
                        💾 Save Profile Settings
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
    color: #929FB2;
    padding: 10px 14px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.hs-dash-tab-btn:hover { color: #20DCC5; }
.hs-dash-tab-btn.hs-active {
    color: #20DCC5;
    border-bottom-color: #20DCC5;
    background: rgba(32, 220, 197, 0.08);
    border-radius: 8px 8px 0 0;
}
.hs-badge {
    background: rgba(255, 255, 255, 0.1);
    color: #E8EEF7;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 999px;
    font-weight: 800;
}
@media (max-width: 900px) {
    .hs-dash-2col {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const restBase = '<?php echo esc_js( $rest_base ); ?>';
    const nonce    = '<?php echo esc_js( $rest_nonce ); ?>';

    const tabButtons  = document.querySelectorAll('.hs-dash-tab-btn');
    const tabContents = document.querySelectorAll('.hs-dash-tab-content');

    function switchTab(target) {
        tabButtons.forEach(btn => btn.classList.toggle('hs-active', btn.getAttribute('data-tab') === target));
        tabContents.forEach(c => { c.style.display = (c.id === 'tab-' + target) ? 'block' : 'none'; });
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('tab', target);
        window.history.replaceState({}, '', newUrl);
    }

    tabButtons.forEach(btn => btn.addEventListener('click', () => switchTab(btn.getAttribute('data-tab'))));

    // Save Profile Settings to real WP REST API
    const saveBtn  = document.getElementById('hs-save-profile-btn');
    const alertBox = document.getElementById('hs-profile-alert');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';
            fetch(restBase + '/user/profile/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({
                    display_name: document.getElementById('hs-prof-name').value.trim(),
                    location:     document.getElementById('hs-prof-loc').value.trim(),
                    website:      document.getElementById('hs-prof-web').value.trim(),
                    ai_voice:     document.getElementById('hs-prof-voice').value,
                    github:       document.getElementById('hs-prof-github').value.trim(),
                    twitter:      document.getElementById('hs-prof-twitter').value.trim(),
                    youtube:      document.getElementById('hs-prof-youtube').value.trim(),
                    linkedin:     document.getElementById('hs-prof-linkedin').value.trim(),
                    bio:          document.getElementById('hs-prof-bio').value.trim()
                })
            })
            .then(r => r.json())
            .then(d => {
                alertBox.style.display = 'block';
                alertBox.style.background = d.success ? 'rgba(32,220,197,0.15)' : 'rgba(244,63,94,0.15)';
                alertBox.style.border = d.success ? '1px solid #20DCC5' : '1px solid #f43f5e';
                alertBox.style.color = d.success ? '#20DCC5' : '#fb7185';
                alertBox.textContent = d.message || 'Update complete.';
            })
            .catch(() => {
                alertBox.style.display = 'block';
                alertBox.textContent = 'Network error saving profile.';
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = '💾 Save Profile Settings';
            });
        });
    }

    // Bookmark Removal
    document.querySelectorAll('.hs-remove-bookmark-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = btn.getAttribute('data-id');
            fetch(restBase + '/library/bookmarks/toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ item_id: itemId })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    const row = btn.closest('.hs-dash-bm-row');
                    if (row) row.remove();
                    const cnt = document.getElementById('hs-bkm-tab-count');
                    if (cnt) cnt.textContent = d.total_saved;
                }
            });
        });
    });

    // Mark All Notifications Read
    const markReadBtn = document.getElementById('hs-mark-notifs-read-btn');
    if (markReadBtn) {
        markReadBtn.addEventListener('click', function() {
            fetch(restBase + '/notifications/mark-read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    const cnt = document.getElementById('hs-notif-tab-count');
                    if (cnt) cnt.textContent = '0';
                    document.querySelectorAll('.hs-dash-notif-row').forEach(el => {
                        el.style.borderLeftColor = 'transparent';
                    });
                }
            });
        });
    }

    // Cashout Form
    const wdrForm = document.getElementById('hs-withdraw-form');
    const wdrMsg  = document.getElementById('hs-wdr-msg');
    if (wdrForm) {
        wdrForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(restBase + '/user/withdraw', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({
                    method: document.getElementById('hs-wdr-method').value,
                    account_number: document.getElementById('hs-wdr-number').value.trim(),
                    amount: parseFloat(document.getElementById('hs-wdr-amount').value)
                })
            })
            .then(r => r.json())
            .then(d => {
                if (wdrMsg) {
                    wdrMsg.style.display = 'block';
                    wdrMsg.style.color = d.success ? '#20DCC5' : '#fb7185';
                    wdrMsg.textContent = d.message || 'Done.';
                }
                if (d.success && typeof d.balance !== 'undefined') {
                    const balEl = document.getElementById('hs-wallet-live-balance');
                    if (balEl) balEl.textContent = 'BDT ' + Number(d.balance).toFixed(2);
                }
            });
        });
    }
});
</script>

<?php get_footer(); ?>

<?php
/**
 * Facebook-Style Social Profile Template (Own Profile & Privacy-Filtered Public Profile)
 * Serves /profile/, /my-profile/, and /author/{username}/
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$author_obj = get_queried_object();
$aid        = ( $author_obj instanceof WP_User ) ? (int) $author_obj->ID : (int) get_query_var( 'author' );
if ( $aid <= 0 && is_user_logged_in() ) {
    $aid = get_current_user_id();
}

$user_info = get_userdata( $aid );
if ( ! $user_info ) : ?>
    <main id="primary" class="hs-fb-profile-page" style="padding:48px 16px;">
        <div class="hs-fb-card" style="max-width:560px; margin:0 auto; text-align:center; padding:34px;">
            <h1 style="margin:0 0 10px; color:#fff;">Operative Not Found</h1>
            <p style="color:#929FB2; margin:0 0 18px;">This profile could not be located.</p>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hs-fb-btn hs-fb-btn--primary">Back to Home</a>
        </div>
    </main>
    <?php
    get_footer();
    return;
endif;

$viewer_id    = get_current_user_id();
$is_own       = ( $viewer_id > 0 && $viewer_id === $aid );
$name         = $user_info->display_name ?: $user_info->user_login;
$bio          = (string) get_user_meta( $aid, 'description', true );
$status_note  = (string) get_user_meta( $aid, '_hs_profile_status_note', true );
$cover_url    = (string) get_user_meta( $aid, '_hs_cover_photo_url', true );
$location     = (string) get_user_meta( $aid, '_hs_user_location', true );
$hometown     = (string) get_user_meta( $aid, '_hs_user_hometown', true );
$relationship = (string) get_user_meta( $aid, '_hs_user_relationship', true );
$birthday     = (string) get_user_meta( $aid, '_hs_user_birthday', true );
$work_title   = (string) get_user_meta( $aid, '_hs_work_title', true );
$work_company = (string) get_user_meta( $aid, '_hs_work_company', true );
$work_since   = (string) get_user_meta( $aid, '_hs_work_since', true );
$edu_school   = (string) get_user_meta( $aid, '_hs_education_school', true );
$edu_degree   = (string) get_user_meta( $aid, '_hs_education_degree', true );
$edu_year     = (string) get_user_meta( $aid, '_hs_education_year', true );
$phone        = (string) get_user_meta( $aid, '_hs_contact_phone', true );
$public_email = (string) get_user_meta( $aid, '_hs_contact_email', true );

$github       = (string) get_user_meta( $aid, '_hs_social_github', true );
$twitter      = (string) get_user_meta( $aid, '_hs_social_twitter', true );
$youtube      = (string) get_user_meta( $aid, '_hs_social_youtube', true );
$linkedin     = (string) get_user_meta( $aid, '_hs_social_linkedin', true );
$website      = (string) $user_info->user_url;

$xp = (int) get_user_meta( $aid, '_hs_learning_xp', true );
if ( class_exists( '\HackersShikkhok\Theme\Modules\Platform\UserEcosystem' ) ) {
    $ws = \HackersShikkhok\Theme\Modules\Platform\UserEcosystem::get_wallet_summary( $aid );
    $xp = max( $xp, (int) ( $ws['points'] ?? 0 ) );
}

$rank = 'Cyber Cadet';
if ( $xp >= 2000 ) {
    $rank = 'Principal Architect';
} elseif ( $xp >= 800 ) {
    $rank = 'Senior Researcher';
} elseif ( $xp >= 250 ) {
    $rank = 'Cyber Operative';
}

$default_privacy = class_exists( '\HackersShikkhok\Theme\Modules\Platform\UserEcosystem' )
    ? \HackersShikkhok\Theme\Modules\Platform\UserEcosystem::get_default_privacy()
    : array();
$saved_privacy = get_user_meta( $aid, '_hs_profile_privacy', true );
$privacy       = array_merge( $default_privacy, is_array( $saved_privacy ) ? $saved_privacy : array() );

$can_view = static function ( string $key ) use ( $aid, $viewer_id, $privacy ): bool {
    $vis = (string) ( $privacy[ $key ] ?? 'public' );
    if ( class_exists( '\HackersShikkhok\Theme\Modules\Platform\UserEcosystem' ) ) {
        return \HackersShikkhok\Theme\Modules\Platform\UserEcosystem::can_view_field( $aid, $viewer_id, $vis );
    }
    return ( 'public' === $vis ) || ( $viewer_id > 0 && $viewer_id === $aid );
};

$vis_badge = static function ( string $key ) use ( $privacy, $is_own ): string {
    if ( ! $is_own ) {
        return '';
    }
    $vis = (string) ( $privacy[ $key ] ?? 'public' );
    if ( 'private' === $vis ) {
        return '<span class="hs-fb-vis-tag is-private">🔒 Only Me</span>';
    }
    if ( 'followers' === $vis ) {
        return '<span class="hs-fb-vis-tag is-followers">👥 Followers</span>';
    }
    return '<span class="hs-fb-vis-tag">🌐 Public</span>';
};

// Following & Followers count
$owner_following = get_user_meta( $aid, '_hs_following_ids', true );
$owner_following = is_array( $owner_following ) ? array_values( array_unique( array_filter( array_map( 'intval', $owner_following ) ) ) ) : array();
$following_count = count( $owner_following );

global $wpdb;
$followers_count = (int) $wpdb->get_var(
    $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = '_hs_following_ids' AND meta_value LIKE %s",
        '%i:' . $aid . ';%'
    )
);

$viewer_following = $viewer_id > 0 ? get_user_meta( $viewer_id, '_hs_following_ids', true ) : array();
$is_following     = is_array( $viewer_following ) && in_array( $aid, array_map( 'intval', $viewer_following ), true );

// Queries for Articles (Published + Own Drafts + Own Trash), Q&A, Answers
$post_types_pool = array( 'post', 'tutorials', 'code', 'tools', 'device_review', 'device_price', 'tech_news' );

$pub_q = new WP_Query( array(
    'author'         => $aid,
    'post_type'      => $post_types_pool,
    'post_status'    => 'publish',
    'posts_per_page' => 20,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

$drafts_q = $is_own ? new WP_Query( array(
    'author'         => $aid,
    'post_type'      => $post_types_pool,
    'post_status'    => array( 'draft', 'pending' ),
    'posts_per_page' => 30,
    'orderby'        => 'modified',
    'order'          => 'DESC',
) ) : null;

$trash_q = $is_own ? new WP_Query( array(
    'author'         => $aid,
    'post_type'      => $post_types_pool,
    'post_status'    => 'trash',
    'posts_per_page' => 30,
    'orderby'        => 'modified',
    'order'          => 'DESC',
) ) : null;

$questions_q = new WP_Query( array(
    'author'         => $aid,
    'post_type'      => 'hs_question',
    'post_status'    => 'publish',
    'posts_per_page' => 15,
) );

$answers_list = get_comments( array(
    'user_id'   => $aid,
    'post_type' => 'hs_question',
    'status'    => 'approve',
    'number'    => 15,
) );

$badges = array();
if ( $pub_q->found_posts >= 1 ) {
    $badges[] = '✍️ Published Author';
}
if ( $questions_q->found_posts >= 1 ) {
    $badges[] = '💬 Forum Contributor';
}
if ( count( $answers_list ) >= 1 ) {
    $badges[] = '💡 Problem Solver';
}
if ( $xp >= 500 ) {
    $badges[] = '⚡ ' . number_format( $xp ) . ' XP Veteran';
}

$my_notifs   = array();
$unread_cnt  = 0;
$inbox_msgs  = array();
if ( $is_own ) {
    $my_notifs  = class_exists( '\HackersShikkhok\Theme\Modules\Community\NotificationEngine' )
        ? \HackersShikkhok\Theme\Modules\Community\NotificationEngine::get_user_notifications( $aid )
        : array();
    $unread_cnt = count( array_filter( $my_notifs, static fn( $n ) => empty( $n['read'] ) ) );
    $raw_inbox  = get_user_meta( $aid, '_hs_inbox_messages', true );
    $inbox_msgs = is_array( $raw_inbox ) ? $raw_inbox : array();
}

$active_ptab = sanitize_key( $_GET['ptab'] ?? 'posts' );
$valid_tabs  = $is_own
    ? array( 'posts', 'drafts', 'trash', 'notifications', 'about' )
    : array( 'posts', 'questions', 'answers', 'about' );
if ( ! in_array( $active_ptab, $valid_tabs, true ) ) {
    $active_ptab = 'posts';
}

$rest_nonce = wp_create_nonce( 'wp_rest' );
$rest_base  = esc_url_raw( rest_url( 'hackersshikkhok/v1' ) );
$share_url  = get_author_posts_url( $aid );
?>

<main id="primary" class="hs-fb-profile-page">
    <!-- Top Cover + Avatar + Tab Header -->
    <section class="hs-fb-cover-shell">
        <div class="hs-fb-cover-inner">
            <div class="hs-fb-cover-banner" id="hs-fb-cover-wrap">
                <?php if ( ! empty( $cover_url ) ) : ?>
                    <img id="hs-fb-cover-img" src="<?php echo esc_url( $cover_url ); ?>" alt="<?php echo esc_attr( $name ); ?> cover photo" />
                <?php else : ?>
                    <img id="hs-fb-cover-img" src="" alt="" style="display:none;" />
                <?php endif; ?>

                <?php if ( $is_own ) : ?>
                    <input type="file" id="hs-upload-cover-input" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;" />
                    <button type="button" class="hs-fb-cover-upload-btn" onclick="document.getElementById('hs-upload-cover-input').click()">
                        📷 <?php echo empty( $cover_url ) ? 'Add Cover Photo' : 'Edit Cover Photo'; ?>
                    </button>
                <?php endif; ?>
            </div>

            <div class="hs-fb-identity-row">
                <div class="hs-fb-identity-left">
                    <div class="hs-fb-avatar-holder">
                        <?php if ( '' !== trim( $status_note ) ) : ?>
                            <div class="hs-fb-status-bubble"
                                <?php echo $is_own ? 'onclick="openProfileModal(\'edit\')" title="Click to update your status note"' : ''; ?>>
                                💭 <?php echo esc_html( $status_note ); ?>
                            </div>
                        <?php elseif ( $is_own ) : ?>
                            <div class="hs-fb-status-bubble" onclick="openProfileModal('edit')" title="Share what you are thinking">
                                💭 Add note...
                            </div>
                        <?php endif; ?>

                        <img id="hs-fb-avatar-img"
                             src="<?php echo esc_url( get_avatar_url( $aid, array( 'size' => 160 ) ) ); ?>"
                             alt="<?php echo esc_attr( $name ); ?>"
                             class="hs-fb-avatar-img" />

                        <?php if ( $is_own ) : ?>
                            <input type="file" id="hs-upload-avatar-input" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;" />
                            <button type="button" class="hs-fb-avatar-cam-btn" onclick="document.getElementById('hs-upload-avatar-input').click()" title="Update Profile Photo" aria-label="Update Profile Photo">
                                📷
                            </button>
                        <?php else : ?>
                            <span class="hs-fb-active-dot" title="Community Operative"></span>
                        <?php endif; ?>
                    </div>

                    <div class="hs-fb-identity-meta">
                        <h1>
                            <span><?php echo esc_html( $name ); ?></span>
                            <span class="hs-fb-verified-tick" title="Verified Member">✓</span>
                            <span style="padding:3px 10px; border-radius:999px; background:rgba(139,124,255,0.16); color:#a78bfa; font-size:11px; font-weight:800;">
                                <?php echo esc_html( $rank ); ?>
                            </span>
                        </h1>
                        <div class="hs-fb-stats-inline">
                            <span><b><?php echo (int) $followers_count; ?></b> Followers</span>
                            <span>·</span>
                            <span><b><?php echo (int) $following_count; ?></b> Following</span>
                            <span>·</span>
                            <span><b><?php echo (int) $pub_q->found_posts; ?></b> Posts</span>
                            <span>·</span>
                            <span class="hs-tabular"><b style="color:#20DCC5;"><?php echo number_format( $xp ); ?></b> XP</span>
                        </div>
                        <?php if ( '' !== trim( $bio ) && $can_view( 'bio' ) ) : ?>
                            <p style="margin:0; font-size:13.5px; color:#cbd5e1; max-width:560px; line-height:1.45;">
                                <?php echo esc_html( $bio ); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="hs-fb-actions-row">
                    <?php if ( $is_own ) : ?>
                        <a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="hs-fb-btn hs-tap-red">📊 Dashboard</a>
                        <a href="<?php echo esc_url( home_url( '/new-post/' ) ); ?>" class="hs-fb-btn hs-fb-btn--primary hs-tap-red">➕ Create Post</a>
                        <button type="button" class="hs-fb-btn" onclick="openProfileModal('edit')">✏️ Edit Profile &amp; Privacy</button>
                    <?php else : ?>
                        <?php if ( $viewer_id > 0 ) : ?>
                            <button type="button"
                                    id="hs-author-follow-btn"
                                    class="hs-fb-btn <?php echo $is_following ? '' : 'hs-fb-btn--primary'; ?>"
                                    onclick="hsToggleProfileFollow(<?php echo $aid; ?>)">
                                <?php echo $is_following ? '✓ Following' : '+ Follow'; ?>
                            </button>
                            <button type="button" class="hs-fb-btn hs-fb-btn--blue" onclick="openProfileModal('message')">💬 Message</button>
                        <?php else : ?>
                            <button type="button" class="hs-fb-btn hs-fb-btn--primary" onclick="openHsAuthModal('login')">+ Follow</button>
                            <button type="button" class="hs-fb-btn hs-fb-btn--blue" onclick="openHsAuthModal('login')">💬 Message</button>
                        <?php endif; ?>
                    <?php endif; ?>
                    <button type="button" class="hs-fb-btn" onclick="hsShareProfile('<?php echo esc_js( $share_url ); ?>')">🔗 Share</button>
                </div>
            </div>

            <!-- Facebook-Style Navigation Tab Bar -->
            <nav class="hs-fb-tabs-bar" id="hs-fb-profile-tabs" aria-label="Profile Sections">
                <button type="button" class="hs-fb-tab-btn <?php echo 'posts' === $active_ptab ? 'is-active' : ''; ?>" data-ptab="posts">
                    Posts <span class="hs-fb-tab-count"><?php echo (int) $pub_q->found_posts; ?></span>
                </button>
                <?php if ( $is_own ) : ?>
                    <button type="button" class="hs-fb-tab-btn <?php echo 'drafts' === $active_ptab ? 'is-active' : ''; ?>" data-ptab="drafts">
                        Drafts <span class="hs-fb-tab-count" id="hs-tab-drafts-cnt"><?php echo $drafts_q ? (int) $drafts_q->found_posts : 0; ?></span>
                    </button>
                    <button type="button" class="hs-fb-tab-btn <?php echo 'trash' === $active_ptab ? 'is-active' : ''; ?>" data-ptab="trash">
                        Trash <span class="hs-fb-tab-count" id="hs-tab-trash-cnt"><?php echo $trash_q ? (int) $trash_q->found_posts : 0; ?></span>
                    </button>
                    <button type="button" class="hs-fb-tab-btn <?php echo 'notifications' === $active_ptab ? 'is-active' : ''; ?>" data-ptab="notifications">
                        Notifications <span class="hs-fb-tab-count" id="hs-tab-notis-cnt"><?php echo (int) $unread_cnt; ?></span>
                    </button>
                    <button type="button" class="hs-fb-tab-btn <?php echo 'about' === $active_ptab ? 'is-active' : ''; ?>" data-ptab="about">
                        About &amp; Privacy
                    </button>
                <?php else : ?>
                    <button type="button" class="hs-fb-tab-btn <?php echo 'questions' === $active_ptab ? 'is-active' : ''; ?>" data-ptab="questions">
                        Q&amp;A Topics <span class="hs-fb-tab-count"><?php echo (int) $questions_q->found_posts; ?></span>
                    </button>
                    <button type="button" class="hs-fb-tab-btn <?php echo 'answers' === $active_ptab ? 'is-active' : ''; ?>" data-ptab="answers">
                        Answers <span class="hs-fb-tab-count"><?php echo count( $answers_list ); ?></span>
                    </button>
                    <button type="button" class="hs-fb-tab-btn <?php echo 'about' === $active_ptab ? 'is-active' : ''; ?>" data-ptab="about">
                        About
                    </button>
                <?php endif; ?>
            </nav>
        </div>
    </section>

    <div class="hs-fb-body-wrap">

        <!-- PANE 1: POSTS TIMELINE (2-Column Intro + Feed) -->
        <section id="hs-ppane-posts" class="hs-fb-pane" style="<?php echo 'posts' === $active_ptab ? 'display:block;' : 'display:none;'; ?>">
            <div class="hs-fb-2col">
                <!-- Left Column: Intro + Highlights + Connections -->
                <aside>
                    <div class="hs-fb-card">
                        <div class="hs-fb-card-head">
                            <h3>Intro</h3>
                            <?php if ( $is_own ) : ?>
                                <button type="button" class="hs-fb-mini-btn" onclick="openProfileModal('edit')">Edit</button>
                            <?php endif; ?>
                        </div>
                        <div class="hs-fb-info-list">
                            <?php if ( ( '' !== $work_title || '' !== $work_company ) && $can_view( 'work' ) ) : ?>
                                <div class="hs-fb-info-row">
                                    <div class="hs-fb-info-main">
                                        <span>💼</span>
                                        <div>
                                            <b><?php echo esc_html( $work_title ?: 'Specialist' ); ?></b>
                                            <?php echo $work_company ? ' at <b>' . esc_html( $work_company ) . '</b>' : ''; ?>
                                            <?php echo $work_since ? '<div style="font-size:11px; color:#929FB2;">Since ' . esc_html( $work_since ) . '</div>' : ''; ?>
                                        </div>
                                    </div>
                                    <?php echo $vis_badge( 'work' ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( ( '' !== $edu_school || '' !== $edu_degree ) && $can_view( 'education' ) ) : ?>
                                <div class="hs-fb-info-row">
                                    <div class="hs-fb-info-main">
                                        <span>🎓</span>
                                        <div>
                                            <?php echo $edu_degree ? esc_html( $edu_degree ) . ' at ' : 'Studied at '; ?>
                                            <b><?php echo esc_html( $edu_school ?: 'University' ); ?></b>
                                            <?php echo $edu_year ? '<div style="font-size:11px; color:#929FB2;">Class of ' . esc_html( $edu_year ) . '</div>' : ''; ?>
                                        </div>
                                    </div>
                                    <?php echo $vis_badge( 'education' ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( '' !== $location && $can_view( 'location' ) ) : ?>
                                <div class="hs-fb-info-row">
                                    <div class="hs-fb-info-main"><span>📍</span><div>Lives in <b><?php echo esc_html( $location ); ?></b></div></div>
                                    <?php echo $vis_badge( 'location' ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( '' !== $hometown && $can_view( 'hometown' ) ) : ?>
                                <div class="hs-fb-info-row">
                                    <div class="hs-fb-info-main"><span>🏡</span><div>From <b><?php echo esc_html( $hometown ); ?></b></div></div>
                                    <?php echo $vis_badge( 'hometown' ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( '' !== $relationship && $can_view( 'relationship' ) ) : ?>
                                <div class="hs-fb-info-row">
                                    <div class="hs-fb-info-main"><span>❤️</span><div><b><?php echo esc_html( $relationship ); ?></b></div></div>
                                    <?php echo $vis_badge( 'relationship' ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( '' !== $birthday && $can_view( 'birthday' ) ) : ?>
                                <div class="hs-fb-info-row">
                                    <div class="hs-fb-info-main"><span>🎂</span><div>Born <b><?php echo esc_html( $birthday ); ?></b></div></div>
                                    <?php echo $vis_badge( 'birthday' ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( '' !== $website && $can_view( 'links' ) ) : ?>
                                <div class="hs-fb-info-row">
                                    <div class="hs-fb-info-main">
                                        <span>🔗</span>
                                        <a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener" style="color:#20DCC5; word-break:break-all;"><?php echo esc_html( preg_replace( '#^https?://#', '', $website ) ); ?></a>
                                    </div>
                                    <?php echo $vis_badge( 'links' ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( $is_own && '' === $work_title && '' === $edu_school && '' === $location ) : ?>
                                <p style="margin:0; color:#929FB2; font-size:12.5px;">Add your work, education, hometown, and privacy rules in <b>Edit Profile &amp; Privacy</b>.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ( ! empty( $badges ) ) : ?>
                    <div class="hs-fb-card">
                        <div class="hs-fb-card-head">
                            <h3>Highlights &amp; Badges</h3>
                        </div>
                        <div style="display:flex; flex-wrap:wrap; gap:8px;">
                            <?php foreach ( $badges as $bd ) : ?>
                                <span style="padding:5px 10px; border-radius:8px; background:#080D17; border:1px solid rgba(32,220,197,0.28); font-size:11.5px; color:#20DCC5; font-weight:700;">
                                    <?php echo esc_html( $bd ); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="hs-fb-card">
                        <div class="hs-fb-card-head">
                            <h3>Following (<?php echo (int) $following_count; ?>)</h3>
                        </div>
                        <?php if ( ! empty( $owner_following ) ) : ?>
                            <div class="hs-fb-friends-grid">
                                <?php foreach ( array_slice( $owner_following, 0, 6 ) as $f_uid ) :
                                    $fu = get_userdata( $f_uid );
                                    if ( ! $fu ) continue;
                                    $fname = $fu->display_name ?: $fu->user_login;
                                ?>
                                    <a href="<?php echo esc_url( get_author_posts_url( $f_uid ) ); ?>" class="hs-fb-friend-tile">
                                        <img src="<?php echo esc_url( get_avatar_url( $f_uid, array( 'size' => 110 ) ) ); ?>" alt="<?php echo esc_attr( $fname ); ?>" />
                                        <span><?php echo esc_html( $fname ); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <p style="margin:0; color:#929FB2; font-size:12.5px;">Not following any operatives yet.</p>
                        <?php endif; ?>
                    </div>
                </aside>

                <!-- Right Column: Composer + Feed -->
                <div>
                    <?php if ( $is_own ) : ?>
                    <div class="hs-fb-card">
                        <div class="hs-fb-composer-bar">
                            <img src="<?php echo esc_url( get_avatar_url( $aid, array( 'size' => 48 ) ) ); ?>" alt="" style="width:42px; height:42px; border-radius:50%; object-fit:cover; border:1px solid #20DCC5;" />
                            <a href="<?php echo esc_url( home_url( '/new-post/' ) ); ?>" class="hs-fb-composer-input-link">
                                What's on your mind, <?php echo esc_html( wp_trim_words( $name, 1, '' ) ); ?>? Write an article or tutorial...
                            </a>
                        </div>
                        <div style="display:flex; gap:8px; margin-top:12px; padding-top:12px; border-top:1px solid rgba(255,255,255,0.06); flex-wrap:wrap;">
                            <a href="<?php echo esc_url( home_url( '/new-post/' ) ); ?>" class="hs-fb-mini-btn hs-fb-mini-btn--ok">✍️ Write Article</a>
                            <a href="<?php echo esc_url( home_url( '/forum/?action=ask' ) ); ?>" class="hs-fb-mini-btn">❓ Ask Question</a>
                            <a href="<?php echo esc_url( home_url( '/tools/?action=submit' ) ); ?>" class="hs-fb-mini-btn">⚡ Submit Tool</a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ( $pub_q->have_posts() ) : ?>
                        <?php while ( $pub_q->have_posts() ) : $pub_q->the_post();
                            $pid   = (int) get_the_ID();
                            $views = (int) ( get_post_meta( $pid, 'hs_views_count', true ) ?: get_post_meta( $pid, '_hs_views_count', true ) );
                            $likes = (int) ( get_post_meta( $pid, 'hs_likes_count', true ) ?: get_post_meta( $pid, '_hs_likes_count', true ) );
                        ?>
                        <article class="hs-fb-post-card" id="hs-prof-post-<?php echo $pid; ?>">
                            <div class="hs-fb-post-head">
                                <div class="hs-fb-post-author">
                                    <img src="<?php echo esc_url( get_avatar_url( $aid, array( 'size' => 48 ) ) ); ?>" alt="" />
                                    <div>
                                        <div style="font-weight:800; font-size:14px; color:#fff;"><?php echo esc_html( $name ); ?></div>
                                        <div style="font-size:11.5px; color:#929FB2;">
                                            <?php echo esc_html( get_the_date( 'M d, Y' ) ); ?> ·
                                            <span style="color:#20DCC5; text-transform:uppercase; font-weight:700;"><?php echo esc_html( get_post_type() ); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <?php if ( $is_own ) : ?>
                                <div class="hs-fb-post-manage">
                                    <a href="<?php echo esc_url( home_url( '/new-post/?edit=' . $pid ) ); ?>" class="hs-fb-mini-btn">✏️ Edit</a>
                                    <button type="button" class="hs-fb-mini-btn hs-fb-mini-btn--danger" onclick="hsPostAction(<?php echo $pid; ?>, 'trash', this)">🗑️ Trash</button>
                                </div>
                                <?php endif; ?>
                            </div>

                            <h3 style="margin:0 0 8px; font-size:1.12rem; line-height:1.35;">
                                <a href="<?php the_permalink(); ?>" style="color:#ffffff; text-decoration:none;"><?php the_title(); ?></a>
                            </h3>
                            <p style="margin:0 0 12px; color:#cbd5e1; font-size:13.5px; line-height:1.55;">
                                <?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ?: get_the_content() ), 32, '...' ) ); ?>
                            </p>

                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>" style="display:block; margin-bottom:12px; border-radius:12px; overflow:hidden; border:1px solid rgba(255,255,255,0.08);">
                                    <?php the_post_thumbnail( 'medium_large', array( 'style' => 'width:100%; max-height:320px; object-fit:cover;' ) ); ?>
                                </a>
                            <?php endif; ?>

                            <div style="display:flex; justify-content:space-between; align-items:center; padding-top:10px; border-top:1px solid rgba(255,255,255,0.06); font-size:12px; color:#929FB2;">
                                <div style="display:flex; gap:14px;">
                                    <span>👁️ <?php echo number_format( $views ); ?></span>
                                    <span>❤️ <?php echo number_format( $likes ); ?></span>
                                    <span>💬 <?php echo (int) get_comments_number(); ?></span>
                                </div>
                                <a href="<?php the_permalink(); ?>" style="color:#20DCC5; font-weight:800; text-decoration:none;">Read Full Post ➔</a>
                            </div>
                        </article>
                        <?php endwhile; wp_reset_postdata(); ?>
                    <?php else : ?>
                        <div class="hs-fb-card" style="text-align:center; padding:32px;">
                            <p style="margin:0 0 12px; color:#929FB2;">No published posts yet.</p>
                            <?php if ( $is_own ) : ?>
                                <a href="<?php echo esc_url( home_url( '/new-post/' ) ); ?>" class="hs-fb-btn hs-fb-btn--primary">Create Your First Post</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php if ( $is_own ) : ?>
        <!-- PANE 2 (Own): DRAFTS -->
        <section id="hs-ppane-drafts" class="hs-fb-pane" style="<?php echo 'drafts' === $active_ptab ? 'display:block;' : 'display:none;'; ?>">
            <div class="hs-fb-card">
                <div class="hs-fb-card-head">
                    <h3>📝 Saved Drafts &amp; Pending Review</h3>
                    <a href="<?php echo esc_url( home_url( '/new-post/' ) ); ?>" class="hs-fb-mini-btn hs-fb-mini-btn--ok">+ New Draft</a>
                </div>
                <?php if ( $drafts_q && $drafts_q->have_posts() ) : ?>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <?php while ( $drafts_q->have_posts() ) : $drafts_q->the_post();
                            $dpid = (int) get_the_ID();
                        ?>
                            <div class="hs-fb-post-card" id="hs-prof-post-<?php echo $dpid; ?>" style="margin-bottom:0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                                <div>
                                    <span style="font-size:10.5px; color:#EABF57; font-weight:800; text-transform:uppercase;"><?php echo esc_html( get_post_status() ); ?> · <?php echo esc_html( get_post_type() ); ?></span>
                                    <h4 style="margin:4px 0 2px; font-size:15px; color:#fff;"><?php the_title(); ?></h4>
                                    <div style="font-size:11.5px; color:#929FB2;">Last updated <?php echo esc_html( get_the_modified_date( 'M d, Y H:i' ) ); ?></div>
                                </div>
                                <div class="hs-fb-post-manage">
                                    <a href="<?php echo esc_url( home_url( '/new-post/?edit=' . $dpid ) ); ?>" class="hs-fb-mini-btn">✏️ Edit</a>
                                    <button type="button" class="hs-fb-mini-btn hs-fb-mini-btn--ok" onclick="hsPostAction(<?php echo $dpid; ?>, 'publish', this)">🚀 Publish Live</button>
                                    <button type="button" class="hs-fb-mini-btn hs-fb-mini-btn--danger" onclick="hsPostAction(<?php echo $dpid; ?>, 'trash', this)">🗑️ Trash</button>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                <?php else : ?>
                    <p style="margin:0; color:#929FB2; font-size:13px;">You have no drafts saved right now.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- PANE 3 (Own): TRASH -->
        <section id="hs-ppane-trash" class="hs-fb-pane" style="<?php echo 'trash' === $active_ptab ? 'display:block;' : 'display:none;'; ?>">
            <div class="hs-fb-card">
                <div class="hs-fb-card-head">
                    <h3>🗑️ Trashed Posts</h3>
                </div>
                <?php if ( $trash_q && $trash_q->have_posts() ) : ?>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <?php while ( $trash_q->have_posts() ) : $trash_q->the_post();
                            $tpid = (int) get_the_ID();
                        ?>
                            <div class="hs-fb-post-card" id="hs-prof-post-<?php echo $tpid; ?>" style="margin-bottom:0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                                <div>
                                    <span style="font-size:10.5px; color:#fda4af; font-weight:800; text-transform:uppercase;">In Trash · <?php echo esc_html( get_post_type() ); ?></span>
                                    <h4 style="margin:4px 0 2px; font-size:15px; color:#fff;"><?php the_title(); ?></h4>
                                </div>
                                <div class="hs-fb-post-manage">
                                    <button type="button" class="hs-fb-mini-btn hs-fb-mini-btn--ok" onclick="hsPostAction(<?php echo $tpid; ?>, 'restore', this)">♻️ Restore to Drafts</button>
                                    <button type="button" class="hs-fb-mini-btn hs-fb-mini-btn--danger" onclick="hsConfirmPermanentDelete(<?php echo $tpid; ?>, this)">❌ Delete Forever</button>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                <?php else : ?>
                    <p style="margin:0; color:#929FB2; font-size:13px;">Trash is empty.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- PANE 4 (Own): NOTIFICATIONS & INBOX -->
        <section id="hs-ppane-notifications" class="hs-fb-pane" style="<?php echo 'notifications' === $active_ptab ? 'display:block;' : 'display:none;'; ?>">
            <div class="hs-fb-card">
                <div class="hs-fb-card-head">
                    <h3>🔔 Notifications</h3>
                    <button type="button" class="hs-fb-mini-btn hs-fb-mini-btn--ok" onclick="hsMarkProfileNotisRead()">✓ Mark All Read</button>
                </div>
                <?php if ( ! empty( $my_notifs ) ) : ?>
                    <div style="display:flex; flex-direction:column; gap:8px;" id="hs-prof-notifs-list">
                        <?php foreach ( $my_notifs as $nf ) :
                            $is_unr = empty( $nf['read'] );
                            $nf_msg = $nf['message'] ?? ( $nf['body'] ?? '' );
                            $nf_ts  = $nf['time_ago'] ?? ( $nf['timestamp'] ?? 'Recently' );
                        ?>
                            <a href="<?php echo esc_url( (string) ( $nf['url'] ?? '#' ) ); ?>"
                               class="noti-item-row <?php echo $is_unr ? 'unread' : ''; ?>"
                               style="border-radius:12px; border:1px solid rgba(255,255,255,0.06);">
                                <span class="noti-item-avatar"><?php echo esc_html( (string) ( $nf['icon'] ?? '🔔' ) ); ?></span>
                                <div class="noti-item-content">
                                    <div class="noti-item-title"><?php echo esc_html( (string) ( $nf['title'] ?? 'Alert' ) ); ?></div>
                                    <?php if ( '' !== trim( (string) $nf_msg ) ) : ?>
                                        <div class="noti-item-desc"><?php echo esc_html( (string) $nf_msg ); ?></div>
                                    <?php endif; ?>
                                    <div class="noti-item-time"><?php echo esc_html( (string) $nf_ts ); ?></div>
                                </div>
                                <?php if ( $is_unr ) : ?><span class="noti-unread-dot"></span><?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p style="margin:0; color:#929FB2; font-size:13px;">No notifications right now.</p>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $inbox_msgs ) ) : ?>
            <div class="hs-fb-card">
                <div class="hs-fb-card-head">
                    <h3>💬 Direct Messages Inbox</h3>
                </div>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <?php foreach ( $inbox_msgs as $im ) : ?>
                        <div style="background:#080D17; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:12px 14px;">
                            <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                                <strong style="color:#20DCC5;"><?php echo esc_html( (string) ( $im['from_name'] ?? 'Operative' ) ); ?> · <?php echo esc_html( (string) ( $im['subject'] ?? 'Message' ) ); ?></strong>
                                <span style="color:#64748b;"><?php echo esc_html( (string) ( $im['sent_at'] ?? '' ) ); ?></span>
                            </div>
                            <p style="margin:0; color:#cbd5e1; font-size:13px;"><?php echo esc_html( (string) ( $im['message'] ?? '' ) ); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </section>
        <?php else : ?>
        <!-- PANE 2 (Public): Q&A TOPICS -->
        <section id="hs-ppane-questions" class="hs-fb-pane" style="<?php echo 'questions' === $active_ptab ? 'display:block;' : 'display:none;'; ?>">
            <div class="hs-fb-card">
                <div class="hs-fb-card-head"><h3>❓ Q&amp;A Topics</h3></div>
                <?php if ( $questions_q->have_posts() ) : ?>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <?php while ( $questions_q->have_posts() ) : $questions_q->the_post(); ?>
                            <a href="<?php the_permalink(); ?>" style="background:#080D17; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:14px 16px; text-decoration:none; display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                                <strong style="color:#E8EEF7; font-size:14.5px;"><?php the_title(); ?></strong>
                                <span style="color:#20DCC5; font-size:12px; font-weight:700;">View Thread ➔</span>
                            </a>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                <?php else : ?>
                    <p style="margin:0; color:#929FB2; font-size:13px;">No forum topics posted yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- PANE 3 (Public): ANSWERS -->
        <section id="hs-ppane-answers" class="hs-fb-pane" style="<?php echo 'answers' === $active_ptab ? 'display:block;' : 'display:none;'; ?>">
            <div class="hs-fb-card">
                <div class="hs-fb-card-head"><h3>💡 Community Answers</h3></div>
                <?php if ( ! empty( $answers_list ) ) : ?>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <?php foreach ( $answers_list as $ans ) : ?>
                            <div style="background:#080D17; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:14px 16px;">
                                <div style="font-size:12px; color:#20DCC5; margin-bottom:5px; font-weight:700;">
                                    On: <a href="<?php echo esc_url( (string) get_permalink( (int) $ans->comment_post_ID ) ); ?>" style="color:#20DCC5;"><?php echo esc_html( get_the_title( (int) $ans->comment_post_ID ) ); ?></a>
                                </div>
                                <p style="margin:0; color:#cbd5e1; font-size:13px;"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $ans->comment_content ), 34, '...' ) ); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p style="margin:0; color:#929FB2; font-size:13px;">No forum answers posted yet.</p>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- PANE 5 (Shared): ABOUT & PRIVACY-FILTERED DETAILS -->
        <section id="hs-ppane-about" class="hs-fb-pane" style="<?php echo 'about' === $active_ptab ? 'display:block;' : 'display:none;'; ?>">
            <div class="hs-fb-2col">
                <div class="hs-fb-card">
                    <div class="hs-fb-card-head">
                        <h3>Personal Details</h3>
                        <?php if ( $is_own ) : ?><button type="button" class="hs-fb-mini-btn" onclick="openProfileModal('edit')">Edit</button><?php endif; ?>
                    </div>
                    <div class="hs-fb-info-list">
                        <?php if ( $can_view( 'location' ) ) : ?>
                            <div class="hs-fb-info-row">
                                <div class="hs-fb-info-main"><span>📍</span><div><b>Location:</b> <?php echo esc_html( $location ?: 'Not specified' ); ?></div></div>
                                <?php echo $vis_badge( 'location' ); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( $can_view( 'hometown' ) && ( $is_own || '' !== $hometown ) ) : ?>
                            <div class="hs-fb-info-row">
                                <div class="hs-fb-info-main"><span>🏡</span><div><b>Hometown:</b> <?php echo esc_html( $hometown ?: 'Not specified' ); ?></div></div>
                                <?php echo $vis_badge( 'hometown' ); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( $can_view( 'relationship' ) && ( $is_own || '' !== $relationship ) ) : ?>
                            <div class="hs-fb-info-row">
                                <div class="hs-fb-info-main"><span>❤️</span><div><b>Relationship:</b> <?php echo esc_html( $relationship ?: 'Not specified' ); ?></div></div>
                                <?php echo $vis_badge( 'relationship' ); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( $can_view( 'birthday' ) && ( $is_own || '' !== $birthday ) ) : ?>
                            <div class="hs-fb-info-row">
                                <div class="hs-fb-info-main"><span>🎂</span><div><b>Birthday:</b> <?php echo esc_html( $birthday ?: 'Not specified' ); ?></div></div>
                                <?php echo $vis_badge( 'birthday' ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <div class="hs-fb-card">
                        <div class="hs-fb-card-head"><h3>Work &amp; Education</h3></div>
                        <div class="hs-fb-info-list">
                            <?php if ( $can_view( 'work' ) ) : ?>
                                <div class="hs-fb-info-row">
                                    <div class="hs-fb-info-main"><span>💼</span><div><b>Work:</b> <?php echo esc_html( trim( $work_title . ( $work_company ? ' at ' . $work_company : '' ) ) ?: 'Not added yet' ); ?></div></div>
                                    <?php echo $vis_badge( 'work' ); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ( $can_view( 'education' ) ) : ?>
                                <div class="hs-fb-info-row">
                                    <div class="hs-fb-info-main"><span>🎓</span><div><b>Education:</b> <?php echo esc_html( trim( $edu_degree . ( $edu_school ? ' — ' . $edu_school : '' ) ) ?: 'Not added yet' ); ?></div></div>
                                    <?php echo $vis_badge( 'education' ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="hs-fb-card">
                        <div class="hs-fb-card-head"><h3>Links &amp; Contact Info</h3></div>
                        <div class="hs-fb-info-list">
                            <?php if ( $can_view( 'links' ) ) : ?>
                                <div class="hs-fb-info-row">
                                    <div class="hs-fb-info-main" style="flex-wrap:wrap; gap:8px;">
                                        <?php if ( $website ) : ?><a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener" class="hs-fb-mini-btn">🌐 Website</a><?php endif; ?>
                                        <?php if ( $github ) : ?><a href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener" class="hs-fb-mini-btn">💻 GitHub</a><?php endif; ?>
                                        <?php if ( $twitter ) : ?><a href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener" class="hs-fb-mini-btn">𝕏 Twitter</a><?php endif; ?>
                                        <?php if ( $youtube ) : ?><a href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener" class="hs-fb-mini-btn">📺 YouTube</a><?php endif; ?>
                                        <?php if ( $linkedin ) : ?><a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener" class="hs-fb-mini-btn">💼 LinkedIn</a><?php endif; ?>
                                        <?php if ( ! $website && ! $github && ! $twitter && ! $youtube && ! $linkedin ) : ?><span style="color:#929FB2;">No social links added.</span><?php endif; ?>
                                    </div>
                                    <?php echo $vis_badge( 'links' ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( $can_view( 'phone' ) && ( $is_own || '' !== $phone ) ) : ?>
                                <div class="hs-fb-info-row">
                                    <div class="hs-fb-info-main"><span>📞</span><div><b>Phone:</b> <?php echo esc_html( $phone ?: 'Not set' ); ?></div></div>
                                    <?php echo $vis_badge( 'phone' ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( $can_view( 'email' ) && ( $is_own || '' !== $public_email ) ) : ?>
                                <div class="hs-fb-info-row">
                                    <div class="hs-fb-info-main"><span>📧</span><div><b>Public Email:</b> <?php echo esc_html( $public_email ?: 'Not set' ); ?></div></div>
                                    <?php echo $vis_badge( 'email' ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</main>

<?php if ( $is_own ) : ?>
<!-- Edit Profile & Privacy Modal -->
<div id="hs-fb-edit-modal" class="hs-fb-modal-backdrop" role="dialog" aria-modal="true">
    <div class="hs-fb-modal-dialog">
        <div class="hs-fb-card-head" style="border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:12px;">
            <h3>✏️ Edit Profile &amp; Field Privacy</h3>
            <button type="button" class="hs-fb-mini-btn" onclick="closeProfileModal('edit')">✕ Close</button>
        </div>

        <form id="hs-fb-edit-form" style="display:flex; flex-direction:column; gap:12px;">
            <div class="hs-fb-form-grid">
                <div class="hs-fb-field">
                    <label>Display Name</label>
                    <input type="text" id="ep-name" value="<?php echo esc_attr( $name ); ?>" />
                </div>
                <div class="hs-fb-field">
                    <label>Thinking About... (Status Note)</label>
                    <input type="text" id="ep-status" maxlength="48" placeholder="Building security tools..." value="<?php echo esc_attr( $status_note ); ?>" />
                </div>
            </div>

            <div class="hs-fb-field">
                <label>
                    <span>Bio / Intro</span>
                    <select id="pv-bio" style="width:auto; padding:2px 6px; font-size:11px;">
                        <option value="public" <?php selected( $privacy['bio'] ?? 'public', 'public' ); ?>>🌐 Public</option>
                        <option value="followers" <?php selected( $privacy['bio'] ?? 'public', 'followers' ); ?>>👥 Followers</option>
                        <option value="private" <?php selected( $privacy['bio'] ?? 'public', 'private' ); ?>>🔒 Only Me</option>
                    </select>
                </label>
                <textarea id="ep-bio" rows="2"><?php echo esc_textarea( $bio ); ?></textarea>
            </div>

            <div class="hs-fb-form-grid">
                <div class="hs-fb-field">
                    <label>
                        <span>Work Role / Title</span>
                        <select id="pv-work" style="width:auto; padding:2px 6px; font-size:11px;">
                            <option value="public" <?php selected( $privacy['work'] ?? 'public', 'public' ); ?>>🌐 Public</option>
                            <option value="followers" <?php selected( $privacy['work'] ?? 'public', 'followers' ); ?>>👥 Followers</option>
                            <option value="private" <?php selected( $privacy['work'] ?? 'public', 'private' ); ?>>🔒 Only Me</option>
                        </select>
                    </label>
                    <input type="text" id="ep-work-title" placeholder="Security Researcher" value="<?php echo esc_attr( $work_title ); ?>" />
                </div>
                <div class="hs-fb-field">
                    <label>Company / Organization</label>
                    <input type="text" id="ep-work-company" placeholder="HackerShikkhok" value="<?php echo esc_attr( $work_company ); ?>" />
                </div>
            </div>

            <div class="hs-fb-form-grid">
                <div class="hs-fb-field">
                    <label>
                        <span>Education (School / University)</span>
                        <select id="pv-education" style="width:auto; padding:2px 6px; font-size:11px;">
                            <option value="public" <?php selected( $privacy['education'] ?? 'public', 'public' ); ?>>🌐 Public</option>
                            <option value="followers" <?php selected( $privacy['education'] ?? 'public', 'followers' ); ?>>👥 Followers</option>
                            <option value="private" <?php selected( $privacy['education'] ?? 'public', 'private' ); ?>>🔒 Only Me</option>
                        </select>
                    </label>
                    <input type="text" id="ep-edu-school" value="<?php echo esc_attr( $edu_school ); ?>" />
                </div>
                <div class="hs-fb-field">
                    <label>Degree / Field</label>
                    <input type="text" id="ep-edu-degree" placeholder="B.Sc. in CSE" value="<?php echo esc_attr( $edu_degree ); ?>" />
                </div>
            </div>

            <div class="hs-fb-form-grid">
                <div class="hs-fb-field">
                    <label>
                        <span>Current City / Location</span>
                        <select id="pv-location" style="width:auto; padding:2px 6px; font-size:11px;">
                            <option value="public" <?php selected( $privacy['location'] ?? 'public', 'public' ); ?>>🌐 Public</option>
                            <option value="followers" <?php selected( $privacy['location'] ?? 'public', 'followers' ); ?>>👥 Followers</option>
                            <option value="private" <?php selected( $privacy['location'] ?? 'public', 'private' ); ?>>🔒 Only Me</option>
                        </select>
                    </label>
                    <input type="text" id="ep-loc" value="<?php echo esc_attr( $location ); ?>" />
                </div>
                <div class="hs-fb-field">
                    <label>
                        <span>Hometown</span>
                        <select id="pv-hometown" style="width:auto; padding:2px 6px; font-size:11px;">
                            <option value="public" <?php selected( $privacy['hometown'] ?? 'public', 'public' ); ?>>🌐 Public</option>
                            <option value="followers" <?php selected( $privacy['hometown'] ?? 'public', 'followers' ); ?>>👥 Followers</option>
                            <option value="private" <?php selected( $privacy['hometown'] ?? 'public', 'private' ); ?>>🔒 Only Me</option>
                        </select>
                    </label>
                    <input type="text" id="ep-hometown" value="<?php echo esc_attr( $hometown ); ?>" />
                </div>
            </div>

            <div class="hs-fb-form-grid">
                <div class="hs-fb-field">
                    <label>
                        <span>Relationship Status</span>
                        <select id="pv-relationship" style="width:auto; padding:2px 6px; font-size:11px;">
                            <option value="public" <?php selected( $privacy['relationship'] ?? 'followers', 'public' ); ?>>🌐 Public</option>
                            <option value="followers" <?php selected( $privacy['relationship'] ?? 'followers', 'followers' ); ?>>👥 Followers</option>
                            <option value="private" <?php selected( $privacy['relationship'] ?? 'followers', 'private' ); ?>>🔒 Only Me</option>
                        </select>
                    </label>
                    <input type="text" id="ep-rel" placeholder="Single / Married / ..." value="<?php echo esc_attr( $relationship ); ?>" />
                </div>
                <div class="hs-fb-field">
                    <label>
                        <span>Birthday</span>
                        <select id="pv-birthday" style="width:auto; padding:2px 6px; font-size:11px;">
                            <option value="public" <?php selected( $privacy['birthday'] ?? 'followers', 'public' ); ?>>🌐 Public</option>
                            <option value="followers" <?php selected( $privacy['birthday'] ?? 'followers', 'followers' ); ?>>👥 Followers</option>
                            <option value="private" <?php selected( $privacy['birthday'] ?? 'followers', 'private' ); ?>>🔒 Only Me</option>
                        </select>
                    </label>
                    <input type="date" id="ep-bday" value="<?php echo esc_attr( $birthday ); ?>" />
                </div>
            </div>

            <div class="hs-fb-form-grid">
                <div class="hs-fb-field">
                    <label>
                        <span>Phone Number</span>
                        <select id="pv-phone" style="width:auto; padding:2px 6px; font-size:11px;">
                            <option value="public" <?php selected( $privacy['phone'] ?? 'private', 'public' ); ?>>🌐 Public</option>
                            <option value="followers" <?php selected( $privacy['phone'] ?? 'private', 'followers' ); ?>>👥 Followers</option>
                            <option value="private" <?php selected( $privacy['phone'] ?? 'private', 'private' ); ?>>🔒 Only Me</option>
                        </select>
                    </label>
                    <input type="text" id="ep-phone" value="<?php echo esc_attr( $phone ); ?>" />
                </div>
                <div class="hs-fb-field">
                    <label>
                        <span>Public Email</span>
                        <select id="pv-email" style="width:auto; padding:2px 6px; font-size:11px;">
                            <option value="public" <?php selected( $privacy['email'] ?? 'private', 'public' ); ?>>🌐 Public</option>
                            <option value="followers" <?php selected( $privacy['email'] ?? 'private', 'followers' ); ?>>👥 Followers</option>
                            <option value="private" <?php selected( $privacy['email'] ?? 'private', 'private' ); ?>>🔒 Only Me</option>
                        </select>
                    </label>
                    <input type="email" id="ep-email" value="<?php echo esc_attr( $public_email ); ?>" />
                </div>
            </div>

            <div class="hs-fb-form-grid">
                <div class="hs-fb-field">
                    <label>
                        <span>Website</span>
                        <select id="pv-links" style="width:auto; padding:2px 6px; font-size:11px;">
                            <option value="public" <?php selected( $privacy['links'] ?? 'public', 'public' ); ?>>🌐 Public</option>
                            <option value="followers" <?php selected( $privacy['links'] ?? 'public', 'followers' ); ?>>👥 Followers</option>
                            <option value="private" <?php selected( $privacy['links'] ?? 'public', 'private' ); ?>>🔒 Only Me</option>
                        </select>
                    </label>
                    <input type="url" id="ep-web" value="<?php echo esc_attr( $website ); ?>" />
                </div>
                <div class="hs-fb-field">
                    <label>GitHub URL</label>
                    <input type="text" id="ep-github" value="<?php echo esc_attr( $github ); ?>" />
                </div>
            </div>

            <div id="hs-fb-edit-feedback" style="display:none; padding:10px; border-radius:8px; font-size:12.5px; font-weight:700;"></div>

            <button type="submit" id="hs-fb-edit-submit" class="hs-fb-btn hs-fb-btn--primary" style="justify-content:center;">
                💾 Save Profile &amp; Privacy
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ( ! $is_own && $viewer_id > 0 ) : ?>
<!-- Direct Message Modal -->
<div id="hs-fb-message-modal" class="hs-fb-modal-backdrop" role="dialog" aria-modal="true">
    <div class="hs-fb-modal-dialog" style="max-width:460px;">
        <div class="hs-fb-card-head" style="border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:12px;">
            <h3>💬 Message <?php echo esc_html( $name ); ?></h3>
            <button type="button" class="hs-fb-mini-btn" onclick="closeProfileModal('message')">✕ Close</button>
        </div>
        <form id="hs-fb-msg-form" style="display:flex; flex-direction:column; gap:12px;">
            <div class="hs-fb-field">
                <label>Subject</label>
                <input type="text" id="msg-subject" value="Hello from HackersShikkhok" required />
            </div>
            <div class="hs-fb-field">
                <label>Message</label>
                <textarea id="msg-body" rows="4" required placeholder="Write your message..."></textarea>
            </div>
            <div id="hs-fb-msg-feedback" style="display:none; padding:10px; border-radius:8px; font-size:12.5px; font-weight:700;"></div>
            <button type="submit" id="hs-fb-msg-submit" class="hs-fb-btn hs-fb-btn--blue" style="justify-content:center;">
                Send Message ➔
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var restBase = '<?php echo esc_js( $rest_base ); ?>';
    var nonce    = '<?php echo esc_js( $rest_nonce ); ?>';

    // Tab switcher with URL state sync
    var tabs = document.querySelectorAll('#hs-fb-profile-tabs [data-ptab]');
    tabs.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var target = btn.getAttribute('data-ptab');
            tabs.forEach(function(b) { b.classList.toggle('is-active', b === btn); });
            document.querySelectorAll('.hs-fb-pane').forEach(function(p) {
                p.style.display = (p.id === 'hs-ppane-' + target) ? 'block' : 'none';
            });
            try {
                var u = new URL(window.location.href);
                u.searchParams.set('ptab', target);
                window.history.replaceState({}, '', u);
            } catch (e) {}
        });
    });

    // Cover & Avatar Upload Handlers
    function bindMediaUpload(inputId, mediaType, previewImgId) {
        var inp = document.getElementById(inputId);
        if (!inp) return;
        inp.addEventListener('change', function() {
            if (!inp.files || !inp.files[0]) return;
            var fd = new FormData();
            fd.append('file', inp.files[0]);
            fd.append('media_type', mediaType);
            hsShowToast('Uploading ' + mediaType + ' image...', false);
            fetch(restBase + '/user/profile/media', {
                method: 'POST',
                headers: { 'X-WP-Nonce': nonce },
                body: fd
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d && d.success && d.url) {
                    var img = document.getElementById(previewImgId);
                    if (img) {
                        img.src = d.url;
                        img.style.display = 'block';
                    }
                    hsShowToast(d.message || 'Image updated!', false);
                } else {
                    hsShowToast((d && d.message) ? d.message : 'Upload failed.', true);
                }
            })
            .catch(function() {
                hsShowToast('Network error uploading image.', true);
            });
        });
    }
    bindMediaUpload('hs-upload-cover-input', 'cover', 'hs-fb-cover-img');
    bindMediaUpload('hs-upload-avatar-input', 'avatar', 'hs-fb-avatar-img');

    // Edit Profile & Privacy Form
    var editForm = document.getElementById('hs-fb-edit-form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var submitBtn = document.getElementById('hs-fb-edit-submit');
            var fb = document.getElementById('hs-fb-edit-feedback');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            fetch(restBase + '/user/profile/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({
                    display_name:        document.getElementById('ep-name').value.trim(),
                    status_note:         document.getElementById('ep-status').value.trim(),
                    bio:                 document.getElementById('ep-bio').value.trim(),
                    work_title:          document.getElementById('ep-work-title').value.trim(),
                    work_company:        document.getElementById('ep-work-company').value.trim(),
                    education_school:    document.getElementById('ep-edu-school').value.trim(),
                    education_degree:    document.getElementById('ep-edu-degree').value.trim(),
                    location:            document.getElementById('ep-loc').value.trim(),
                    hometown:            document.getElementById('ep-hometown').value.trim(),
                    relationship_status: document.getElementById('ep-rel').value.trim(),
                    birthday:            document.getElementById('ep-bday').value.trim(),
                    phone:               document.getElementById('ep-phone').value.trim(),
                    public_email:        document.getElementById('ep-email').value.trim(),
                    website:             document.getElementById('ep-web').value.trim(),
                    github:              document.getElementById('ep-github').value.trim(),
                    privacy_settings: {
                        bio:          document.getElementById('pv-bio').value,
                        work:         document.getElementById('pv-work').value,
                        education:    document.getElementById('pv-education').value,
                        location:     document.getElementById('pv-location').value,
                        hometown:     document.getElementById('pv-hometown').value,
                        relationship: document.getElementById('pv-relationship').value,
                        birthday:     document.getElementById('pv-birthday').value,
                        phone:        document.getElementById('pv-phone').value,
                        email:        document.getElementById('pv-email').value,
                        links:        document.getElementById('pv-links').value
                    }
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                fb.style.display = 'block';
                fb.style.background = d.success ? 'rgba(32,220,197,0.14)' : 'rgba(244,63,94,0.14)';
                fb.style.color = d.success ? '#20DCC5' : '#fda4af';
                fb.textContent = d.message || 'Updated!';
                if (d.success) {
                    setTimeout(function() { window.location.reload(); }, 650);
                }
            })
            .catch(function() {
                fb.style.display = 'block';
                fb.textContent = 'Network error saving profile.';
            })
            .finally(function() {
                submitBtn.disabled = false;
                submitBtn.textContent = '💾 Save Profile & Privacy';
            });
        });
    }

    // Message Form
    var msgForm = document.getElementById('hs-fb-msg-form');
    if (msgForm) {
        msgForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var submitBtn = document.getElementById('hs-fb-msg-submit');
            var fb = document.getElementById('hs-fb-msg-feedback');
            submitBtn.disabled = true;
            fetch(restBase + '/user/profile/message', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({
                    recipient_id: <?php echo (int) $aid; ?>,
                    subject: document.getElementById('msg-subject').value.trim(),
                    message: document.getElementById('msg-body').value.trim()
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                fb.style.display = 'block';
                fb.style.background = d.success ? 'rgba(32,220,197,0.14)' : 'rgba(244,63,94,0.14)';
                fb.style.color = d.success ? '#20DCC5' : '#fda4af';
                fb.textContent = d.message || 'Done';
                if (d.success) {
                    document.getElementById('msg-body').value = '';
                    setTimeout(function() { closeProfileModal('message'); }, 900);
                }
            })
            .finally(function() {
                submitBtn.disabled = false;
            });
        });
    }
});

function hsShowToast(msg, isErr) {
    var old = document.getElementById('hs-prof-toast');
    if (old) old.remove();
    var div = document.createElement('div');
    div.id = 'hs-prof-toast';
    div.className = 'hs-toast-banner' + (isErr ? ' is-error' : '');
    div.textContent = msg;
    document.body.appendChild(div);
    setTimeout(function() { if (div && div.parentNode) div.remove(); }, 3200);
}

function openProfileModal(kind) {
    var m = document.getElementById('hs-fb-' + kind + '-modal');
    if (m) {
        m.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeProfileModal(kind) {
    var m = document.getElementById('hs-fb-' + kind + '-modal');
    if (m) {
        m.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function hsToggleProfileFollow(aid) {
    fetch('<?php echo esc_js( $rest_base ); ?>/user/follow', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': '<?php echo esc_js( $rest_nonce ); ?>' },
        body: JSON.stringify({ target_user_id: aid })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        var btn = document.getElementById('hs-author-follow-btn');
        if (btn && res && res.success) {
            btn.textContent = res.followed ? '✓ Following' : '+ Follow';
            btn.classList.toggle('hs-fb-btn--primary', !res.followed);
            hsShowToast(res.message || 'Updated', false);
        }
    });
}

function hsShareProfile(url) {
    if (navigator.share) {
        navigator.share({ title: document.title, url: url }).catch(function() {});
    } else if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function() {
            hsShowToast('Profile link copied to clipboard!', false);
        });
    } else {
        hsShowToast('Share link: ' + url, false);
    }
}

function hsPostAction(pid, action, btn) {
    if (btn) btn.disabled = true;
    fetch('<?php echo esc_js( $rest_base ); ?>/user/post/action', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': '<?php echo esc_js( $rest_nonce ); ?>' },
        body: JSON.stringify({ post_id: pid, action: action })
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d && d.success) {
            var card = document.getElementById('hs-prof-post-' + pid);
            if (card) card.remove();
            hsShowToast(d.message || 'Updated!', false);
        } else {
            if (btn) btn.disabled = false;
            hsShowToast((d && d.message) ? d.message : 'Action failed.', true);
        }
    });
}

function hsConfirmPermanentDelete(pid, btn) {
    if (!btn.getAttribute('data-armed')) {
        btn.setAttribute('data-armed', '1');
        btn.textContent = 'Confirm Delete';
        btn.style.background = '#DC2626';
        btn.style.color = '#ffffff';
        return;
    }
    hsPostAction(pid, 'delete_permanent', btn);
}

function hsMarkProfileNotisRead() {
    fetch('<?php echo esc_js( $rest_base ); ?>/notifications/mark-read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': '<?php echo esc_js( $rest_nonce ); ?>' }
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d && d.success) {
            var c1 = document.getElementById('hs-tab-notis-cnt');
            var c2 = document.getElementById('hs-nav-noti-badge');
            if (c1) c1.textContent = '0';
            if (c2) c2.style.display = 'none';
            document.querySelectorAll('#hs-prof-notifs-list .noti-item-row.unread').forEach(function(el) {
                el.classList.remove('unread');
                var dot = el.querySelector('.noti-unread-dot');
                if (dot) dot.remove();
            });
            hsShowToast('All notifications marked read.', false);
        }
    });
}
</script>

<?php
get_footer();

<?php
/**
 * Template Name: Community Member Directory
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$search_q   = isset( $_GET['mq'] ) ? sanitize_text_field( wp_unslash( $_GET['mq'] ) ) : '';
$sort_by    = isset( $_GET['sort'] ) ? sanitize_key( (string) wp_unslash( $_GET['sort'] ) ) : 'xp';
$current_id = get_current_user_id();
$following  = $current_id > 0 ? get_user_meta( $current_id, '_hs_following_ids', true ) : array();
$following  = is_array( $following ) ? array_map( 'intval', $following ) : array();
$rest_nonce = wp_create_nonce( 'wp_rest' );

$user_args = array(
    'number' => 60,
);
if ( '' !== $search_q ) {
    $user_args['search']         = '*' . $search_q . '*';
    $user_args['search_columns'] = array( 'user_login', 'display_name' );
}
$raw_users = get_users( $user_args );

$members = array();
foreach ( $raw_users as $u ) {
    $uid       = (int) $u->ID;
    $xp        = (int) get_user_meta( $uid, '_hs_learning_xp', true );
    $posts_cnt = (int) count_user_posts( $uid, array( 'post', 'tutorials', 'code', 'tools', 'hs_question' ), true );
    $ans_cnt   = (int) get_comments( array( 'user_id' => $uid, 'count' => true, 'status' => 'approve' ) );
    $bio       = (string) get_user_meta( $uid, 'description', true );
    $loc       = (string) get_user_meta( $uid, '_hs_user_location', true );

    $rank_title = 'Apprentice';
    if ( $xp >= 2000 ) $rank_title = 'Principal Architect';
    elseif ( $xp >= 800 ) $rank_title = 'Senior Researcher';
    elseif ( $xp >= 250 ) $rank_title = 'Cyber Operative';

    $members[] = array(
        'id'         => $uid,
        'name'       => $u->display_name ?: $u->user_login,
        'login'      => $u->user_login,
        'avatar'     => get_avatar_url( $uid, array( 'size' => 72 ) ),
        'url'        => get_author_posts_url( $uid ),
        'xp'         => $xp,
        'posts'      => $posts_cnt,
        'answers'    => $ans_cnt,
        'rank'       => $rank_title,
        'bio'        => $bio ?: 'HackersShikkhok contributor & security learner.',
        'location'   => $loc,
        'registered' => strtotime( $u->user_registered ),
    );
}

usort( $members, static function ( array $a, array $b ) use ( $sort_by ): int {
    if ( 'posts' === $sort_by ) return $b['posts'] <=> $a['posts'];
    if ( 'answers' === $sort_by ) return $b['answers'] <=> $a['answers'];
    if ( 'newest' === $sort_by ) return $b['registered'] <=> $a['registered'];
    return $b['xp'] <=> $a['xp'];
} );
?>

<main id="primary" class="site-main" style="background:#080D17; color:#E8EEF7; min-height:100vh; padding:28px 16px 90px;">
    <div style="max-width:1320px; margin:0 auto;">

        <header style="background:#101827; border:1px solid rgba(32,220,197,0.28); border-radius:18px; padding:26px; margin-bottom:22px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:18px;">
            <div>
                <span style="font-size:11px; font-weight:800; color:#20DCC5; background:rgba(32,220,197,0.12); padding:3px 10px; border-radius:6px; font-family:var(--font-mono);">👥 COMMUNITY DIRECTORY</span>
                <h1 style="font-size:clamp(1.5rem, 3vw, 2.2rem); font-weight:900; color:#E8EEF7; margin:10px 0 6px;">Researchers, Contributors &amp; Mentors</h1>
                <p style="color:#929FB2; font-size:13px; margin:0;">Discover security analysts, full-stack authors, and top Q&amp;A problem solvers.</p>
            </div>

            <form method="get" action="<?php echo esc_url( home_url( '/members/' ) ); ?>" style="display:flex; gap:8px; flex-wrap:wrap;">
                <input type="search" name="mq" value="<?php echo esc_attr( $search_q ); ?>" placeholder="Search members..." style="background:#080D17; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:9px 14px; border-radius:8px; font-size:13px; min-width:210px;" />
                <input type="hidden" name="sort" value="<?php echo esc_attr( $sort_by ); ?>" />
                <button type="submit" style="padding:9px 18px; background:#20DCC5; color:#080D17; font-weight:800; border:none; border-radius:8px; cursor:pointer; font-size:13px;">Search</button>
            </form>
        </header>

        <div class="hs-forum-tabs" style="margin-bottom:22px;">
            <a href="<?php echo esc_url( add_query_arg( array( 'sort' => 'xp', 'mq' => $search_q ?: false ) ) ); ?>" class="hs-forum-tab-btn <?php echo 'xp' === $sort_by ? 'is-active' : ''; ?>">⚡ Top XP</a>
            <a href="<?php echo esc_url( add_query_arg( array( 'sort' => 'posts', 'mq' => $search_q ?: false ) ) ); ?>" class="hs-forum-tab-btn <?php echo 'posts' === $sort_by ? 'is-active' : ''; ?>">📝 Most Articles</a>
            <a href="<?php echo esc_url( add_query_arg( array( 'sort' => 'answers', 'mq' => $search_q ?: false ) ) ); ?>" class="hs-forum-tab-btn <?php echo 'answers' === $sort_by ? 'is-active' : ''; ?>">💬 Most Answers</a>
            <a href="<?php echo esc_url( add_query_arg( array( 'sort' => 'newest', 'mq' => $search_q ?: false ) ) ); ?>" class="hs-forum-tab-btn <?php echo 'newest' === $sort_by ? 'is-active' : ''; ?>">🆕 Newest Members</a>
        </div>

        <?php if ( empty( $members ) ) : ?>
            <div style="background:#101827; border:1px dashed rgba(255,255,255,0.12); border-radius:16px; padding:40px; text-align:center; color:#929FB2;">
                No matching members found for "<?php echo esc_html( $search_q ); ?>".
            </div>
        <?php else : ?>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(min(100%, 290px), 1fr)); gap:18px;">
                <?php foreach ( $members as $m ) :
                    $is_fol = in_array( $m['id'], $following, true );
                ?>
                    <article style="background:#101827; border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:20px; display:flex; flex-direction:column; justify-content:space-between; gap:14px;">
                        <div>
                            <div style="display:flex; align-items:center; gap:14px; margin-bottom:12px;">
                                <img src="<?php echo esc_url( $m['avatar'] ); ?>" alt="<?php echo esc_attr( $m['name'] ); ?>" style="width:54px; height:54px; border-radius:50%; border:2px solid #20DCC5; object-fit:cover; flex-shrink:0;" loading="lazy" />
                                <div style="min-width:0;">
                                    <h3 style="margin:0 0 3px; font-size:1.05rem; font-weight:800;">
                                        <a href="<?php echo esc_url( $m['url'] ); ?>" style="color:#E8EEF7; text-decoration:none;"><?php echo esc_html( $m['name'] ); ?></a>
                                    </h3>
                                    <span style="font-size:11px; color:#8B7CFF; font-weight:700;"><?php echo esc_html( $m['rank'] ); ?></span>
                                </div>
                            </div>
                            <p style="color:#929FB2; font-size:12.5px; line-height:1.5; margin:0 0 12px;">
                                <?php echo esc_html( wp_trim_words( $m['bio'], 18, '...' ) ); ?>
                            </p>
                        </div>

                        <div>
                            <div class="hs-tabular" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:8px; background:#080D17; border-radius:10px; padding:10px; text-align:center; font-size:11px; margin-bottom:12px;">
                                <div><strong style="display:block; color:#20DCC5; font-size:14px;"><?php echo number_format( $m['xp'] ); ?></strong><span style="color:#929FB2;">XP</span></div>
                                <div><strong style="display:block; color:#E8EEF7; font-size:14px;"><?php echo (int) $m['posts']; ?></strong><span style="color:#929FB2;">Posts</span></div>
                                <div><strong style="display:block; color:#EABF57; font-size:14px;"><?php echo (int) $m['answers']; ?></strong><span style="color:#929FB2;">Answers</span></div>
                            </div>

                            <div style="display:flex; gap:8px;">
                                <a href="<?php echo esc_url( $m['url'] ); ?>" style="flex:1; text-align:center; padding:8px; background:#172435; color:#E8EEF7; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none;">Profile</a>
                                <?php if ( $current_id > 0 && $current_id !== $m['id'] ) : ?>
                                    <button type="button" onclick="hsMemberFollow(<?php echo (int) $m['id']; ?>, this)" style="flex:1; padding:8px; border-radius:8px; border:1px solid rgba(32,220,197,0.35); background:<?php echo $is_fol ? '#20DCC5' : 'transparent'; ?>; color:<?php echo $is_fol ? '#080D17' : '#20DCC5'; ?>; font-size:12px; font-weight:700; cursor:pointer;">
                                        <?php echo $is_fol ? '✓ Following' : '+ Follow'; ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
function hsMemberFollow(targetId, btn) {
    fetch('/wp-json/hackersshikkhok/v1/user/follow', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': '<?php echo esc_js( $rest_nonce ); ?>' },
        body: JSON.stringify({ target_user_id: targetId })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res && res.success) {
            btn.textContent = res.followed ? '✓ Following' : '+ Follow';
            btn.style.background = res.followed ? '#20DCC5' : 'transparent';
            btn.style.color = res.followed ? '#080D17' : '#20DCC5';
        }
    });
}
</script>

<?php
get_footer();

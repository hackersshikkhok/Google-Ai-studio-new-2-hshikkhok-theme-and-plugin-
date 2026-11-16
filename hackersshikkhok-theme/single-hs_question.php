<?php
/**
 * Single Question & Discussion Template (hs_question)
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();
    $qid            = (int) get_the_ID();
    $author_id      = (int) get_the_author_meta( 'ID' );
    $author_name    = get_the_author();
    $q_cat          = (string) ( get_post_meta( $qid, '_hs_question_category', true ) ?: 'General Security' );
    $q_type         = (string) ( get_post_meta( $qid, '_hs_question_type', true ) ?: 'question' );
    $q_votes        = (int) ( get_post_meta( $qid, '_hs_question_votes', true ) ?: 1 );
    $accepted_id    = (int) get_post_meta( $qid, '_hs_accepted_answer_id', true );
    $is_solved      = ( $accepted_id > 0 ) || ( (bool) get_post_meta( $qid, '_hs_question_solved', true ) );
    $followers_cnt  = (int) get_post_meta( $qid, '_hs_question_followers', true );
    $views_cnt      = (int) get_post_meta( $qid, 'hs_views_count', true );
    $uid            = get_current_user_id();
    $is_logged_in   = is_user_logged_in();
    $can_accept     = $is_logged_in && ( $uid === $author_id || current_user_can( 'manage_options' ) || current_user_can( 'hs_moderate_forum' ) );
    $followed_qs    = $is_logged_in ? get_user_meta( $uid, '_hs_followed_questions', true ) : array();
    $is_following   = is_array( $followed_qs ) && in_array( $qid, array_map( 'intval', $followed_qs ), true );
    $rest_nonce     = wp_create_nonce( 'wp_rest' );

    $comments = get_comments( array(
        'post_id' => $qid,
        'status'  => 'approve',
        'orderby' => 'comment_date',
        'order'   => 'ASC',
    ) );
    // Put accepted solution first
    if ( $accepted_id > 0 && ! empty( $comments ) ) {
        usort( $comments, static function ( $a, $b ) use ( $accepted_id ): int {
            if ( (int) $a->comment_ID === $accepted_id ) return -1;
            if ( (int) $b->comment_ID === $accepted_id ) return 1;
            $va = (int) get_comment_meta( (int) $a->comment_ID, '_hs_answer_votes', true );
            $vb = (int) get_comment_meta( (int) $b->comment_ID, '_hs_answer_votes', true );
            return $vb <=> $va;
        } );
    }
?>

<main id="primary" class="site-main" style="background:#080D17; color:#E8EEF7; min-height:100vh; padding:28px 16px 90px;">
    <div style="max-width:1060px; margin:0 auto;">

        <!-- Breadcrumb & Back -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:18px; font-size:12px;">
            <a href="<?php echo esc_url( home_url( '/forum/' ) ); ?>" style="color:#20DCC5; text-decoration:none; font-weight:700;">← Back to Community Q&amp;A</a>
            <div style="display:flex; gap:8px; align-items:center;">
                <span style="padding:3px 10px; border-radius:6px; background:rgba(32,220,197,0.12); color:#20DCC5; font-weight:700;"><?php echo esc_html( $q_cat ); ?></span>
                <span style="padding:3px 10px; border-radius:6px; background:rgba(139,124,255,0.15); color:#8B7CFF; font-weight:700;"><?php echo 'discussion' === $q_type ? '💬 Discussion' : '❓ Question'; ?></span>
                <?php if ( $is_solved ) : ?>
                    <span style="padding:3px 10px; border-radius:6px; background:rgba(16,185,129,0.15); color:#10b981; font-weight:800;">✓ Solved</span>
                <?php endif; ?>
            </div>
        </div>

        <div id="hs-qa-toast" style="display:none; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:13px; font-weight:700;"></div>

        <!-- Question Card -->
        <article style="background:#101827; border:1px solid <?php echo $is_solved ? 'rgba(16,185,129,0.4)' : 'rgba(255,255,255,0.1)'; ?>; border-radius:18px; padding:24px; margin-bottom:26px;">
            <div style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;">
                <!-- Vote Column -->
                <div style="display:flex; flex-direction:column; align-items:center; gap:6px; background:#080D17; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:10px 12px; min-width:56px;">
                    <button type="button" onclick="hsQaVote('question', <?php echo $qid; ?>, 'up', this)" style="background:none; border:none; color:#20DCC5; font-size:16px; cursor:pointer; padding:4px 8px;" title="Upvote">▲</button>
                    <strong id="hs-q-vote-count" class="hs-tabular" style="font-size:18px; color:#E8EEF7;"><?php echo esc_html( (string) $q_votes ); ?></strong>
                    <button type="button" onclick="hsQaVote('question', <?php echo $qid; ?>, 'down', this)" style="background:none; border:none; color:#929FB2; font-size:16px; cursor:pointer; padding:4px 8px;" title="Downvote">▼</button>
                </div>

                <!-- Question Body -->
                <div style="flex:1; min-width:260px;">
                    <h1 style="font-size:clamp(1.4rem, 3vw, 1.95rem); font-weight:900; color:#E8EEF7; margin:0 0 12px; line-height:1.3;">
                        <?php the_title(); ?>
                    </h1>

                    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; font-size:12px; color:#929FB2; margin-bottom:18px; padding-bottom:14px; border-bottom:1px solid rgba(255,255,255,0.08);">
                        <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" style="display:inline-flex; align-items:center; gap:7px; color:#E8EEF7; text-decoration:none; font-weight:700;">
                            <img src="<?php echo esc_url( get_avatar_url( $author_id, array( 'size' => 28 ) ) ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" style="width:24px; height:24px; border-radius:50%; object-fit:cover;" />
                            <span><?php echo esc_html( $author_name ); ?></span>
                        </a>
                        <span>• <?php echo esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?> ago</span>
                        <span class="hs-tabular">• 👁️ <?php echo number_format( $views_cnt ); ?> views</span>
                        <span class="hs-tabular">• 💬 <?php echo count( $comments ); ?> answers</span>
                    </div>

                    <div class="hs-article-body" style="color:#cbd5e1; font-size:15px; line-height:1.75; margin-bottom:20px;">
                        <?php the_content(); ?>
                    </div>

                    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                        <button type="button" id="hs-follow-q-btn" onclick="hsFollowQuestion(<?php echo $qid; ?>)" style="padding:8px 14px; border-radius:8px; border:1px solid rgba(32,220,197,0.35); background:<?php echo $is_following ? '#20DCC5' : '#172435'; ?>; color:<?php echo $is_following ? '#080D17' : '#20DCC5'; ?>; font-weight:700; font-size:12px; cursor:pointer;">
                            <?php echo $is_following ? '✓ Following Thread' : '🔔 Follow Thread'; ?> (<span id="hs-q-followers"><?php echo (int) $followers_cnt; ?></span>)
                        </button>
                        <button type="button" onclick="hsToggleReportModal(true)" style="padding:8px 14px; border-radius:8px; border:1px solid rgba(244,63,94,0.3); background:#172435; color:#fda4af; font-weight:700; font-size:12px; cursor:pointer;">
                            🚩 Report
                        </button>
                    </div>
                </div>
            </div>
        </article>

        <!-- Answers Section -->
        <section style="margin-bottom:28px;">
            <h2 style="font-size:1.25rem; font-weight:800; color:#E8EEF7; margin:0 0 16px;">
                💬 Community Answers &amp; Responses (<?php echo count( $comments ); ?>)
            </h2>

            <?php if ( empty( $comments ) ) : ?>
                <div style="background:#101827; border:1px dashed rgba(255,255,255,0.12); border-radius:14px; padding:26px; text-align:center; color:#929FB2;">
                    No answers posted yet. Share your solution below and earn +10 XP (+25 XP if accepted)!
                </div>
            <?php else : ?>
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <?php foreach ( $comments as $c ) :
                        $cid       = (int) $c->comment_ID;
                        $c_uid     = (int) $c->user_id;
                        $c_votes   = (int) get_comment_meta( $cid, '_hs_answer_votes', true );
                        $is_acc    = ( $cid === $accepted_id );
                    ?>
                        <article id="answer-<?php echo $cid; ?>" style="background:#101827; border:1px solid <?php echo $is_acc ? '#10b981' : 'rgba(255,255,255,0.08)'; ?>; border-radius:14px; padding:20px; <?php echo $is_acc ? 'box-shadow:0 0 20px rgba(16,185,129,0.15);' : ''; ?>">
                            <?php if ( $is_acc ) : ?>
                                <div style="display:inline-block; background:rgba(16,185,129,0.16); color:#10b981; border:1px solid rgba(16,185,129,0.4); font-size:11px; font-weight:800; padding:3px 10px; border-radius:6px; margin-bottom:12px;">
                                    ✓ ACCEPTED SOLUTION
                                </div>
                            <?php endif; ?>

                            <div style="display:flex; gap:16px; align-items:flex-start; flex-wrap:wrap;">
                                <div style="display:flex; flex-direction:column; align-items:center; gap:4px; background:#080D17; border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:8px 10px; min-width:48px;">
                                    <button type="button" onclick="hsQaVote('comment', <?php echo $cid; ?>, 'up', this)" style="background:none; border:none; color:#20DCC5; cursor:pointer; font-size:14px;">▲</button>
                                    <strong id="hs-ans-vote-<?php echo $cid; ?>" class="hs-tabular" style="font-size:15px; color:#E8EEF7;"><?php echo (int) $c_votes; ?></strong>
                                    <button type="button" onclick="hsQaVote('comment', <?php echo $cid; ?>, 'down', this)" style="background:none; border:none; color:#929FB2; cursor:pointer; font-size:14px;">▼</button>
                                </div>

                                <div style="flex:1; min-width:240px;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:10px;">
                                        <div style="display:flex; align-items:center; gap:8px; font-size:12px;">
                                            <img src="<?php echo esc_url( get_avatar_url( $c_uid > 0 ? $c_uid : $c->comment_author_email, array( 'size' => 28 ) ) ); ?>" alt="" style="width:24px; height:24px; border-radius:50%;" />
                                            <strong style="color:#E8EEF7;"><?php echo esc_html( $c->comment_author ); ?></strong>
                                            <span style="color:#929FB2;">• <?php echo esc_html( human_time_diff( strtotime( $c->comment_date_gmt ), time() ) ); ?> ago</span>
                                        </div>

                                        <?php if ( $can_accept && ! $is_acc ) : ?>
                                            <button type="button" onclick="hsAcceptSolution(<?php echo $qid; ?>, <?php echo $cid; ?>)" style="padding:5px 12px; border-radius:6px; border:1px solid #10b981; background:rgba(16,185,129,0.12); color:#10b981; font-size:11px; font-weight:800; cursor:pointer;">
                                                ✓ Mark as Accepted Solution
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <div style="color:#cbd5e1; font-size:14px; line-height:1.7;">
                                        <?php echo wpautop( wp_kses_post( $c->comment_content ) ); ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Post Answer Box -->
        <section style="background:#101827; border:1px solid rgba(32,220,197,0.3); border-radius:16px; padding:24px;">
            <h3 style="font-size:1.1rem; font-weight:800; color:#E8EEF7; margin:0 0 12px;">✍️ Your Answer / Contribution</h3>
            <?php if ( $is_logged_in ) : ?>
                <form onsubmit="hsSubmitAnswer(event, <?php echo $qid; ?>)">
                    <textarea id="hs-answer-input" rows="5" required placeholder="Write a clear technical explanation, steps, or code snippet..." style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(255,255,255,0.14); border-radius:10px; padding:12px 14px; color:#E8EEF7; font-size:14px; margin-bottom:12px;"></textarea>
                    <button type="submit" id="hs-answer-submit-btn" style="padding:10px 22px; background:#20DCC5; color:#080D17; font-weight:800; border:none; border-radius:8px; cursor:pointer; font-size:13px;">
                        🚀 Submit Answer (+10 XP)
                    </button>
                </form>
            <?php else : ?>
                <p style="color:#929FB2; font-size:13px; margin:0 0 14px;">Sign in to post an answer, vote, or earn contributor reputation points.</p>
                <a href="<?php echo esc_url( wp_login_url( get_permalink( $qid ) ) ); ?>" style="display:inline-block; padding:9px 18px; background:#20DCC5; color:#080D17; font-weight:800; border-radius:8px; text-decoration:none; font-size:13px;">🔑 Sign In to Answer</a>
            <?php endif; ?>
        </section>

    </div>
</main>

<!-- Report Modal -->
<div id="hs-qa-report-modal" style="display:none; position:fixed; inset:0; background:rgba(8,13,23,0.85); z-index:9999; align-items:center; justify-content:center; padding:16px;">
    <div style="background:#101827; border:1px solid rgba(244,63,94,0.45); border-radius:14px; padding:22px; width:min(94vw, 440px); color:#E8EEF7;">
        <h3 style="margin:0 0 10px; font-size:16px;">🚩 Report Topic</h3>
        <select id="hs-qa-report-reason" style="width:100%; background:#080D17; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:8px; border-radius:6px; margin-bottom:10px;">
            <option value="Spam or Promotion">Spam or Promotion</option>
            <option value="Abusive or Unsafe Content">Abusive or Unsafe Content</option>
            <option value="Duplicate Question">Duplicate Question</option>
            <option value="Misleading Technical Info">Misleading Technical Info</option>
        </select>
        <textarea id="hs-qa-report-details" rows="3" placeholder="Additional details..." style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:8px; border-radius:6px; margin-bottom:12px;"></textarea>
        <div style="display:flex; justify-content:flex-end; gap:8px;">
            <button type="button" onclick="hsToggleReportModal(false)" style="padding:7px 14px; background:#172435; border:none; color:#E8EEF7; border-radius:6px; cursor:pointer;">Cancel</button>
            <button type="button" onclick="hsSubmitTopicReport(<?php echo $qid; ?>)" style="padding:7px 14px; background:#f43f5e; border:none; color:#fff; font-weight:700; border-radius:6px; cursor:pointer;">Submit Report</button>
        </div>
    </div>
</div>

<script>
var hsQaNonce = '<?php echo esc_js( $rest_nonce ); ?>';

function hsShowQaToast(msg, isErr) {
    var box = document.getElementById('hs-qa-toast');
    if (!box) return;
    box.style.display = 'block';
    box.style.background = isErr ? 'rgba(244,63,94,0.15)' : 'rgba(32,220,197,0.15)';
    box.style.border = '1px solid ' + (isErr ? '#f43f5e' : '#20DCC5');
    box.style.color = isErr ? '#fecdd3' : '#20DCC5';
    box.textContent = msg;
}

function hsQaVote(type, targetId, dir) {
    fetch('/wp-json/hackersshikkhok/v1/forum/vote', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': hsQaNonce },
        body: JSON.stringify({ target_type: type, target_id: targetId, direction: dir })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res && res.success) {
            var el = document.getElementById(type === 'question' ? 'hs-q-vote-count' : ('hs-ans-vote-' + targetId));
            if (el) el.textContent = res.votes;
        } else {
            hsShowQaToast((res && res.message) ? res.message : 'Please sign in to vote.', true);
        }
    })
    .catch(function() { hsShowQaToast('Sign in required to vote.', true); });
}

function hsAcceptSolution(qid, cid) {
    fetch('/wp-json/hackersshikkhok/v1/forum/accept-solution', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': hsQaNonce },
        body: JSON.stringify({ question_id: qid, comment_id: cid })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res && res.success) {
            hsShowQaToast(res.message, false);
            setTimeout(function() { window.location.reload(); }, 900);
        } else {
            hsShowQaToast((res && res.message) ? res.message : 'Could not accept solution.', true);
        }
    });
}

function hsFollowQuestion(qid) {
    fetch('/wp-json/hackersshikkhok/v1/forum/follow-question', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': hsQaNonce },
        body: JSON.stringify({ question_id: qid })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res && res.success) {
            var btn = document.getElementById('hs-follow-q-btn');
            if (btn) {
                btn.style.background = res.following ? '#20DCC5' : '#172435';
                btn.style.color = res.following ? '#080D17' : '#20DCC5';
                btn.innerHTML = (res.following ? '✓ Following Thread' : '🔔 Follow Thread') + ' (<span id="hs-q-followers">' + res.followers + '</span>)';
            }
            hsShowQaToast(res.message, false);
        } else {
            hsShowQaToast('Sign in required to follow threads.', true);
        }
    });
}

function hsSubmitAnswer(e, qid) {
    e.preventDefault();
    var txt = document.getElementById('hs-answer-input');
    var btn = document.getElementById('hs-answer-submit-btn');
    if (!txt || !txt.value.trim()) return;
    btn.disabled = true;
    btn.textContent = 'Posting...';

    fetch('/wp-json/hackersshikkhok/v1/forum/answer', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': hsQaNonce },
        body: JSON.stringify({ question_id: qid, content: txt.value.trim() })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        btn.disabled = false;
        btn.textContent = '🚀 Submit Answer (+10 XP)';
        if (res && res.success) {
            hsShowQaToast(res.message, false);
            setTimeout(function() { window.location.reload(); }, 800);
        } else {
            hsShowQaToast((res && res.message) ? res.message : 'Unable to post answer.', true);
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.textContent = '🚀 Submit Answer (+10 XP)';
        hsShowQaToast('Network error submitting answer.', true);
    });
}

function hsToggleReportModal(show) {
    var m = document.getElementById('hs-qa-report-modal');
    if (m) m.style.display = show ? 'flex' : 'none';
}

function hsSubmitTopicReport(qid) {
    var reason = document.getElementById('hs-qa-report-reason').value;
    var details = document.getElementById('hs-qa-report-details').value;
    fetch('/wp-json/hackersshikkhok/v1/forum/report', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': hsQaNonce },
        body: JSON.stringify({ question_id: qid, reason: reason, details: details })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        hsToggleReportModal(false);
        hsShowQaToast((res && res.message) ? res.message : 'Report submitted.', !(res && res.success));
    });
}
</script>

<?php
endwhile;
get_footer();

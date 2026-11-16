<?php
/**
 * Community Q&A Forum Archive Template (hs_question)
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */
declare(strict_types=1);

get_header();

$is_logged_in = is_user_logged_in();
$active_cat   = isset( $_GET['qcat'] ) ? sanitize_text_field( wp_unslash( $_GET['qcat'] ) ) : 'all';
$active_tab   = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'all';
$search_term  = isset( $_GET['qq'] ) ? sanitize_text_field( wp_unslash( $_GET['qq'] ) ) : '';
$action_mode  = isset( $_GET['action'] ) ? sanitize_key( (string) wp_unslash( $_GET['action'] ) ) : '';
$paged        = max( 1, (int) get_query_var( 'paged' ) );

$args = array(
    'post_type'      => 'hs_question',
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
);

if ( '' !== $search_term ) {
    $args['s'] = $search_term;
}

$meta_queries = array();

if ( 'all' !== $active_cat && '' !== $active_cat ) {
    $meta_queries[] = array(
        'key'     => '_hs_question_category',
        'value'   => $active_cat,
        'compare' => '=',
    );
}

if ( 'solved' === $active_tab ) {
    $meta_queries[] = array(
        'key'     => '_hs_question_solved',
        'value'   => '1',
        'compare' => '=',
    );
} elseif ( 'discussions' === $active_tab ) {
    $meta_queries[] = array(
        'key'     => '_hs_question_type',
        'value'   => 'discussion',
        'compare' => '=',
    );
}

if ( ! empty( $meta_queries ) ) {
    if ( count( $meta_queries ) > 1 ) {
        $meta_queries['relation'] = 'AND';
    }
    $args['meta_query'] = $meta_queries;
}

if ( 'unanswered' === $active_tab ) {
    $args['comment_count'] = array(
        'value'   => 0,
        'compare' => '=',
    );
}

$forum_query = new \WP_Query( $args );
$categories  = array(
    'all'              => 'All Questions',
    'General Security' => '🛡️ Cybersecurity',
    'Web Development'  => '💻 Web Dev',
    'WordPress'        => '⚡ WordPress',
    'CTF & Labs'       => '🚩 CTF & Labs',
    'Tools & Code'     => '🛠️ Tools & Code',
);
$rest_q_url = esc_url_raw( rest_url( 'hackersshikkhok/v1/user/question/create' ) );
$rest_nonce = wp_create_nonce( 'wp_rest' );
?>

<main id="primary" class="site-main hs-forum-page">
    <div class="hs-forum-shell">

        <!-- Forum Header -->
        <header class="hs-forum-hero">
            <div class="hs-forum-hero-text">
                <span class="hs-forum-badge">💬 COMMUNITY Q&amp;A FORUM</span>
                <h1>Questions &amp; Answers Forum (Q&amp;A)</h1>
                <p>Ask questions about cybersecurity, coding, WordPress, or CTF labs and get verified answers from the community.</p>
            </div>
            <div class="hs-forum-hero-actions" style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="<?php echo esc_url( home_url( '/members/' ) ); ?>" class="hs-btn-secondary">👥 Members</a>
                <?php if ( $is_logged_in ) : ?>
                    <button type="button" class="hs-btn-primary" onclick="toggleHsAskBox(true, 'question')">➕ Ask Question (+2 BDT &amp; +50 XP)</button>
                    <button type="button" class="hs-btn-secondary" onclick="toggleHsAskBox(true, 'discussion')">💬 Start Discussion</button>
                <?php else : ?>
                    <button type="button" class="hs-btn-primary" onclick="openHsAuthModal('login')">🔑 Login to Ask Question</button>
                <?php endif; ?>
            </div>
        </header>

        <!-- Ask Question / Discussion Composer (Member-only) -->
        <?php if ( $is_logged_in ) :
            $open_composer = in_array( $action_mode, array( 'ask', 'new-topic' ), true );
            $init_qtype    = ( 'new-topic' === $action_mode ) ? 'discussion' : 'question';
        ?>
        <section id="hs-forum-ask-box" class="hs-forum-ask-box" style="display:<?php echo $open_composer ? 'block' : 'none'; ?>;">
            <div class="hs-forum-ask-head">
                <h3 id="hs-forum-ask-heading"><?php echo 'discussion' === $init_qtype ? '💬 Start a Community Discussion' : '✍️ Ask a Technical Question'; ?></h3>
                <button type="button" class="hs-forum-ask-close" onclick="toggleHsAskBox(false)" aria-label="Close">✕</button>
            </div>
            <div id="hs-forum-ask-feedback" class="hs-forum-feedback" style="display:none;"></div>
            <form id="hs-forum-ask-form" onsubmit="submitHsForumQuestion(event)">
                <div class="hs-forum-form-row">
                    <div class="hs-forum-field hs-forum-field--grow">
                        <label for="hs-q-title">Title</label>
                        <input type="text" id="hs-q-title" required placeholder="e.g., How do Prepared Statements prevent SQL Injection?" />
                    </div>
                    <div class="hs-forum-field">
                        <label for="hs-q-type">Topic Type</label>
                        <select id="hs-q-type">
                            <option value="question" <?php selected( $init_qtype, 'question' ); ?>>❓ Q&amp;A Question</option>
                            <option value="discussion" <?php selected( $init_qtype, 'discussion' ); ?>>💬 General Discussion</option>
                        </select>
                    </div>
                    <div class="hs-forum-field">
                        <label for="hs-q-cat">Category</label>
                        <select id="hs-q-cat">
                            <option value="General Security">🛡️ Cybersecurity</option>
                            <option value="Web Development">💻 Web Dev</option>
                            <option value="WordPress">⚡ WordPress</option>
                            <option value="CTF & Labs">🚩 CTF &amp; Labs</option>
                            <option value="Tools & Code">🛠️ Tools &amp; Code</option>
                        </select>
                    </div>
                </div>
                <div class="hs-forum-field">
                    <label for="hs-q-details">Detailed Description</label>
                    <textarea id="hs-q-details" rows="4" required placeholder="Describe your problem, environment, or discussion topic in detail..."></textarea>
                </div>
                <button type="submit" id="hs-q-submit-btn" class="hs-btn-primary">🚀 Publish Topic</button>
            </form>
        </section>
        <?php endif; ?>

        <!-- Mode Tabs: Questions vs Discussions vs Solved vs Unanswered (PART 6 Specification) -->
        <div class="hs-forum-tabs">
            <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'all', 'qcat' => $active_cat, 'qq' => $search_term ?: false ), home_url( '/forum/' ) ) ); ?>" class="hs-forum-tab-btn <?php echo ( 'all' === $active_tab ) ? 'is-active' : ''; ?>">
                📋 All Topics
            </a>
            <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'discussions', 'qcat' => $active_cat, 'qq' => $search_term ?: false ), home_url( '/forum/' ) ) ); ?>" class="hs-forum-tab-btn <?php echo ( 'discussions' === $active_tab ) ? 'is-active' : ''; ?>">
                💬 General Discussions
            </a>
            <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'solved', 'qcat' => $active_cat, 'qq' => $search_term ?: false ), home_url( '/forum/' ) ) ); ?>" class="hs-forum-tab-btn <?php echo ( 'solved' === $active_tab ) ? 'is-active' : ''; ?>">
                ✓ Solved Questions
            </a>
            <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'unanswered', 'qcat' => $active_cat, 'qq' => $search_term ?: false ), home_url( '/forum/' ) ) ); ?>" class="hs-forum-tab-btn <?php echo ( 'unanswered' === $active_tab ) ? 'is-active' : ''; ?>">
                ❓ Unanswered
            </a>
        </div>

        <!-- Filter & Search Bar -->
        <div class="hs-forum-toolbar">
            <div class="hs-forum-cats">
                <?php foreach ( $categories as $cat_key => $cat_label ) :
                    $cat_url = add_query_arg( array(
                        'qcat' => $cat_key,
                        'qq'   => $search_term ?: false,
                    ), home_url( '/forum/' ) );
                ?>
                    <a href="<?php echo esc_url( $cat_url ); ?>" class="hs-forum-cat-pill <?php echo $active_cat === $cat_key ? 'is-active' : ''; ?>">
                        <?php echo esc_html( $cat_label ); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <form method="get" action="<?php echo esc_url( home_url( '/forum/' ) ); ?>" class="hs-forum-search">
                <?php if ( 'all' !== $active_cat ) : ?>
                    <input type="hidden" name="qcat" value="<?php echo esc_attr( $active_cat ); ?>" />
                <?php endif; ?>
                <input type="search" name="qq" value="<?php echo esc_attr( $search_term ); ?>" placeholder="Search forum questions..." />
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Questions List -->
        <section class="hs-forum-list">
            <?php if ( $forum_query->have_posts() ) : ?>
                <?php while ( $forum_query->have_posts() ) :
                    $forum_query->the_post();
                    $qid      = get_the_ID();
                    $q_cat    = (string) ( get_post_meta( $qid, '_hs_question_category', true ) ?: 'General Security' );
                    $q_type   = (string) ( get_post_meta( $qid, '_hs_question_type', true ) ?: 'question' );
                    $q_votes  = (int) ( get_post_meta( $qid, '_hs_question_votes', true ) ?: 1 );
                    $solved   = ( (int) get_post_meta( $qid, '_hs_accepted_answer_id', true ) > 0 ) || ( (bool) get_post_meta( $qid, '_hs_question_solved', true ) );
                    $ans_cnt  = (int) get_comments_number( $qid );
                    $author   = get_the_author();
                ?>
                <article class="hs-forum-card <?php echo $solved ? 'is-solved' : ''; ?>">
                    <div class="hs-forum-stats-col">
                        <div class="hs-forum-stat-box">
                            <strong><?php echo $q_votes; ?></strong>
                            <span>Votes</span>
                        </div>
                        <div class="hs-forum-stat-box <?php echo $solved ? 'is-solved-box' : ''; ?>">
                            <strong><?php echo $ans_cnt; ?></strong>
                            <span>Answers</span>
                        </div>
                    </div>

                    <div class="hs-forum-body-col">
                        <div class="hs-forum-meta-top">
                            <span class="hs-forum-cat-tag"><?php echo esc_html( $q_cat ); ?></span>
                            <span style="font-size:10px; font-weight:800; padding:2px 7px; border-radius:4px; background:rgba(139,124,255,0.14); color:#8B7CFF;">
                                <?php echo 'discussion' === $q_type ? '💬 Discussion' : '❓ Question'; ?>
                            </span>
                            <?php if ( $solved ) : ?>
                                <span class="hs-forum-solved-tag">✓ Solved</span>
                            <?php endif; ?>
                            <span class="hs-forum-time"><?php echo esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?> ago · <?php echo esc_html( $author ); ?></span>
                        </div>

                        <h2 class="hs-forum-q-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>

                        <p class="hs-forum-q-excerpt">
                            <?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_content() ), 26, '...' ) ); ?>
                        </p>
                    </div>
                </article>
                <?php endwhile; ?>

                <div class="hs-pagination-wrap">
                    <?php
                    echo wp_kses_post( (string) paginate_links( array(
                        'total'   => $forum_query->max_num_pages,
                        'current' => $paged,
                    ) ) );
                    ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="hs-no-posts-card hs-cyber-card">
                    <span style="font-size:34px;">💬</span>
                    <h3 style="color:#00f5d4;margin:10px 0;">No questions posted yet.</h3>
                    <p style="color:#94a3b8;font-size:13px;max-width:480px;margin:0 auto 16px;">
                        Start a community discussion by posting the first question and earn +2 BDT &amp; +50 XP rewards!
                    </p>
                    <?php if ( $is_logged_in ) : ?>
                        <button type="button" class="hs-btn-primary" onclick="toggleHsAskBox(true)">➕ Ask First Question</button>
                    <?php else : ?>
                        <button type="button" class="hs-btn-primary" onclick="openHsAuthModal('login')">🔑 Login to Ask Question</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>
</main>

<script>
function toggleHsAskBox(show, mode) {
    var box = document.getElementById('hs-forum-ask-box');
    var typeEl = document.getElementById('hs-q-type');
    var headEl = document.getElementById('hs-forum-ask-heading');
    if (typeEl && mode) {
        typeEl.value = mode;
    }
    if (headEl && mode) {
        headEl.textContent = (mode === 'discussion') ? '💬 Start a Community Discussion' : '✍️ Ask a Technical Question';
    }
    if (box) {
        box.style.display = show ? 'block' : 'none';
        if (show) box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function submitHsForumQuestion(e) {
    e.preventDefault();
    var titleEl = document.getElementById('hs-q-title');
    var typeEl = document.getElementById('hs-q-type');
    var catEl = document.getElementById('hs-q-cat');
    var detailsEl = document.getElementById('hs-q-details');
    var btn = document.getElementById('hs-q-submit-btn');
    var fb = document.getElementById('hs-forum-ask-feedback');
    if (!titleEl || !detailsEl) return;

    btn.disabled = true;
    btn.textContent = '⏳ Publishing...';

    fetch('<?php echo esc_js( $rest_q_url ); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': '<?php echo esc_js( $rest_nonce ); ?>'
        },
        body: JSON.stringify({
            title: titleEl.value.trim(),
            question_type: typeEl ? typeEl.value : 'question',
            category: catEl ? catEl.value : 'General Security',
            details: detailsEl.value.trim()
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (fb) {
            fb.style.display = 'block';
            fb.textContent = (res && res.message) ? res.message : 'Question published!';
            fb.className = 'hs-forum-feedback ' + (res && res.success ? 'is-ok' : 'is-err');
        }
        if (res && res.success) {
            setTimeout(function() {
                window.location.href = res.permalink || window.location.href;
            }, 900);
        }
    })
    .catch(function() {
        if (fb) {
            fb.style.display = 'block';
            fb.className = 'hs-forum-feedback is-err';
            fb.textContent = 'Network error occurred. Please try again.';
        }
    })
    .finally(function() {
        btn.disabled = false;
        btn.textContent = '🚀 Publish Question';
    });
}
</script>

<?php
get_footer();

<?php
/**
 * Master Single Course Template - HackersShikkhok
 * CPT: hs_course
 * Dynamic Curriculum Loader, Instructor Profile, Persistent Enrollment & Progress,
 * Assignment Submission, Server-Validated Web Terminal & CTF Flag Verification.
 */
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

global $wpdb;
$course_id       = get_the_ID();
$course_slug     = get_post_field( 'post_name', $course_id ) ?: 'course';
$title           = get_the_title( $course_id ) ?: 'Advanced Cybersecurity & Defensive Principles';
$category        = get_post_meta( $course_id, '_hs_category_id', true ) ?: 'cybersecurity';
$level           = (int) get_post_meta( $course_id, '_hs_level', true ) ?: 1;
$duration        = (int) get_post_meta( $course_id, '_hs_duration_weeks', true ) ?: 6;
$lessons_count   = (int) get_post_meta( $course_id, '_hs_curriculum_total_lessons', true ) ?: 12;
$labs_count      = (int) get_post_meta( $course_id, '_hs_curriculum_total_labs', true ) ?: 3;
$price_bdt       = (float) get_post_meta( $course_id, '_hs_price_bdt', true );
$author_id       = (int) get_post_field( 'post_author', $course_id );
$author_name     = get_the_author_meta( 'display_name', $author_id ) ?: 'HackersShikkhok Faculty';
$author_bio      = get_the_author_meta( 'description', $author_id ) ?: 'Cybersecurity Architect & Defensive Engineering Instructor';
$author_url      = get_author_posts_url( $author_id );

$current_user_id = get_current_user_id();
$is_logged_in    = is_user_logged_in();
$rest_nonce      = wp_create_nonce( 'wp_rest' );
$rest_base       = esc_url_raw( rest_url( 'hackersshikkhok/v1' ) );
$login_url       = wp_login_url( get_permalink( $course_id ) ?: home_url( '/academy/' ) );

// Load dynamic modules from _hs_modules_json or Course_Manager
$modules = array();
$raw_mods = get_post_meta( $course_id, '_hs_modules_json', true );
if ( ! empty( $raw_mods ) ) {
    $decoded = json_decode( (string) $raw_mods, true );
    if ( is_array( $decoded ) ) {
        $modules = $decoded;
    }
}
if ( empty( $modules ) && class_exists( '\HackersShikkhok\Theme\Modules\LMS\Course_Manager' ) ) {
    $cm_course = \HackersShikkhok\Theme\Modules\LMS\Course_Manager::get_course_by_slug( $course_slug );
    if ( $cm_course && ! empty( $cm_course['curriculum'] ) ) {
        foreach ( $cm_course['curriculum'] as $m_idx => $cm ) {
            $modules[] = array(
                'moduleNumber'  => $m_idx + 1,
                'moduleTitleEn' => $cm['title'] ?? 'Module ' . ( $m_idx + 1 ),
                'lessons'       => array_map( static fn( $l ) => array(
                    'id'       => (string) ( $l['slug'] ?? $l['id'] ),
                    'titleEn'  => (string) ( $l['title'] ?? 'Lesson' ),
                    'duration' => ( (int) ( $l['duration_mins'] ?? 15 ) ) . 'm',
                    'type'     => ! empty( $l['svg_diagram_key'] ) ? 'Lab' : 'Video',
                ), (array) ( $cm['lessons'] ?? array() ) ),
            );
        }
    }
}
if ( empty( $modules ) ) {
    $modules = array(
        array(
            'moduleNumber'  => 1,
            'moduleTitleEn' => 'Module 1: Security Foundations & Architecture',
            'lessons'       => array(
                array( 'id' => 'm1_l1', 'titleEn' => '1.1 Core Threat Modeling & Defensive Principles', 'duration' => '14m', 'type' => 'Video' ),
                array( 'id' => 'm1_l2', 'titleEn' => '1.2 Sandboxed Linux Terminal & Reconnaissance', 'duration' => '18m', 'type' => 'Lab' ),
            ),
        ),
        array(
            'moduleNumber'  => 2,
            'moduleTitleEn' => 'Module 2: OWASP Mitigations & CTF Verification',
            'lessons'       => array(
                array( 'id' => 'm2_l1', 'titleEn' => '2.1 Parameterized Queries & Zero-Trust Auth', 'duration' => '22m', 'type' => 'Video' ),
                array( 'id' => 'm2_l2', 'titleEn' => '2.2 Practical CTF Lab: Capture Root Token', 'duration' => '25m', 'type' => 'CTF Lab' ),
            ),
        ),
    );
}

// Persistent enrollment & lesson completion lookup
$is_enrolled       = false;
$completed_lessons = array();
$overall_percent   = 0;
if ( $is_logged_in ) {
    $enrolled_meta = get_user_meta( $current_user_id, '_hs_enrolled_courses', true );
    if ( is_array( $enrolled_meta ) && in_array( $course_id, array_map( 'intval', $enrolled_meta ), true ) ) {
        $is_enrolled = true;
    }
    $prog_table = $wpdb->prefix . 'hs_courses_progress';
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $prog_table ) ) === $prog_table ) {
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT completed_lessons, overall_percent FROM {$prog_table} WHERE user_id = %d AND course_id = %d LIMIT 1", $current_user_id, $course_id ), ARRAY_A );
        if ( $row ) {
            $is_enrolled = true;
            $dec = json_decode( (string) $row['completed_lessons'], true );
            if ( is_array( $dec ) ) {
                $completed_lessons = $dec;
            }
            $overall_percent = (int) $row['overall_percent'];
        }
    }
}
?>

<style>
.hs-course-workspace-grid {
    display: grid;
    grid-template-columns: 1fr 460px;
    gap: 28px;
    align-items: start;
}
@media (max-width: 960px) {
    .hs-course-workspace-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<main id="primary" class="hs-single-course-page" style="background:#080D17; color:#E8EEF7; min-height:100vh; padding:32px 16px 80px;">
    <div style="max-width:1300px; margin:0 auto;">

        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#929FB2; margin-bottom:24px; font-family:var(--font-mono); flex-wrap:wrap;">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#20DCC5; text-decoration:none;">Home</a>
            <span>/</span>
            <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" style="color:#20DCC5; text-decoration:none;">Academy</a>
            <span>/</span>
            <span style="color:#E8EEF7; font-weight:700;"><?php echo esc_html( $title ); ?></span>
        </nav>

        <!-- Hero Card -->
        <header style="background:linear-gradient(135deg, #101827, #172435); border:1px solid rgba(32,220,197,0.35); border-radius:24px; padding:32px; margin-bottom:28px; position:relative; overflow:hidden;">
            <div style="position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg, #20DCC5, #8B7CFF, #EABF57);"></div>

            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:24px;">
                <div style="max-width:780px;">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px; flex-wrap:wrap;">
                        <span style="background:rgba(32,220,197,0.12); color:#20DCC5; border:1px solid rgba(32,220,197,0.35); font-size:11px; font-weight:800; padding:3px 10px; border-radius:6px; text-transform:uppercase;">
                            FACULTY: <?php echo esc_html( strtoupper( $category ) ); ?>
                        </span>
                        <span style="background:rgba(139,124,255,0.15); color:#8B7CFF; border:1px solid rgba(139,124,255,0.35); font-size:11px; font-weight:800; padding:3px 10px; border-radius:6px;">
                            LEVEL <?php echo esc_html( (string) $level ); ?>
                        </span>
                        <span style="font-size:12px; color:#20DCC5; font-weight:700;">
                            <?php echo $price_bdt > 0 ? esc_html( 'BDT ' . number_format( $price_bdt, 0 ) ) : '✓ Free Access · Certificate Eligible'; ?>
                        </span>
                    </div>

                    <h1 style="font-size:clamp(1.7rem, 3.8vw, 2.5rem); font-weight:900; color:#E8EEF7; margin:0 0 12px; line-height:1.2;">
                        <?php echo esc_html( $title ); ?>
                    </h1>
                    <p style="color:#929FB2; font-size:15px; line-height:1.6; margin:0 0 18px;">
                        <?php echo esc_html( get_the_excerpt() ?: 'Hands-on cybersecurity and engineering course featuring structured modules, sandboxed terminal labs, assignments, and HMAC-verified certification.' ); ?>
                    </p>

                    <div style="display:flex; align-items:center; gap:18px; flex-wrap:wrap; font-size:13px; color:#E8EEF7; font-family:var(--font-mono);">
                        <span>⏱️ <?php echo (int) $duration; ?> Weeks</span>
                        <span>📚 <?php echo (int) $lessons_count; ?> Lessons</span>
                        <span>🧪 <?php echo (int) $labs_count; ?> CTF Labs</span>
                        <span>📊 Progress: <strong id="hs-hero-prog-pct"><?php echo (int) $overall_percent; ?>%</strong></span>
                    </div>
                </div>

                <!-- Right Enrollment Card -->
                <div style="background:#080D17; border:1px solid rgba(32,220,197,0.35); border-radius:18px; padding:22px; min-width:260px; flex:0 1 300px; text-align:center;">
                    <div style="font-size:11px; color:#929FB2; text-transform:uppercase; font-weight:700; margin-bottom:6px;">Course Completion Rewards</div>
                    <div style="font-size:1.6rem; font-weight:900; color:#20DCC5; font-family:var(--font-mono); margin-bottom:4px;">+500 Scholar XP</div>
                    <div style="font-size:12px; color:#EABF57; font-weight:700; margin-bottom:16px;">Verified HMAC Certificate on 100% + Quiz</div>

                    <?php if ( $is_logged_in ) : ?>
                        <button type="button" id="hs-enroll-btn" data-enrolled="<?php echo $is_enrolled ? '1' : '0'; ?>" style="width:100%; min-height:44px; background:<?php echo $is_enrolled ? '#10b981' : '#20DCC5'; ?>; color:#080D17; border:none; padding:12px; border-radius:10px; font-weight:900; font-size:14px; cursor:pointer;">
                            <?php echo $is_enrolled ? '✓ Enrolled in Course' : '🚀 Enroll & Track Progress'; ?>
                        </button>
                    <?php else : ?>
                        <a href="<?php echo esc_url( $login_url ); ?>" style="display:block; width:100%; box-sizing:border-box; padding:12px; background:#20DCC5; color:#080D17; border-radius:10px; font-weight:900; font-size:14px; text-decoration:none;">
                            Sign In to Enroll →
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo esc_url( home_url( '/academy/?course_id=' . urlencode( $course_slug ) ) ); ?>" style="display:block; margin-top:10px; color:#20DCC5; font-size:12px; font-weight:700; text-decoration:none;">
                        Open Full 3-Column LMS Player ↗
                    </a>
                </div>
            </div>
        </header>

        <!-- Dynamic Architecture SVG Diagram -->
        <section style="background:#101827; border:1px solid rgba(139,124,255,0.3); border-radius:20px; padding:24px; margin-bottom:28px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
                <h3 style="color:#E8EEF7; font-size:1.15rem; font-weight:800; margin:0;">🛡️ Defensive Execution Architecture Flow</h3>
                <span style="font-size:11px; color:#8B7CFF; font-family:var(--font-mono);">Server-Validated CTF Pipeline</span>
            </div>
            <div style="background:#080D17; border-radius:12px; padding:14px; border:1px solid rgba(255,255,255,0.06); overflow-x:auto;">
                <?php echo class_exists( '\HackersShikkhok\Theme\Modules\LMS\SVG_Annotator' )
                    ? \HackersShikkhok\Theme\Modules\LMS\SVG_Annotator::render_diagram( 'sqli_prevention' )
                    : '<p style="color:#929FB2;margin:0;">Interactive SVG workflow enabled in Academy Player.</p>'; ?>
            </div>
        </section>

        <!-- Two Columns: Syllabus & Instructor (Left) + Sandbox Terminal & CTF & Assignment (Right) -->
        <div class="hs-course-workspace-grid">

            <!-- LEFT: Dynamic Syllabus & Instructor -->
            <div style="display:flex; flex-direction:column; gap:24px;">
                <section style="background:#101827; border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:26px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:18px;">
                        <h3 style="color:#E8EEF7; font-size:1.25rem; font-weight:800; margin:0;">📚 Dynamic Course Curriculum</h3>
                        <span id="hs-syllabus-prog-pill" style="background:rgba(32,220,197,0.12); color:#20DCC5; border:1px solid rgba(32,220,197,0.3); font-size:12px; font-weight:800; padding:4px 12px; border-radius:999px;">
                            <?php echo (int) $overall_percent; ?>% Completed
                        </span>
                    </div>

                    <?php foreach ( $modules as $m_idx => $mod ) :
                        $m_title = (string) ( $mod['moduleTitleEn'] ?? ( $mod['moduleTitleBn'] ?? ( 'Module ' . ( $m_idx + 1 ) ) ) );
                        $mod_lessons = (array) ( $mod['lessons'] ?? array() );
                    ?>
                        <div style="border:1px solid rgba(255,255,255,0.08); border-radius:12px; margin-bottom:14px; overflow:hidden;">
                            <div style="padding:14px 18px; background:#172435; display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                                <strong style="color:#E8EEF7; font-size:14px;"><?php echo esc_html( $m_title ); ?></strong>
                                <span style="font-size:12px; color:#20DCC5; font-family:var(--font-mono);"><?php echo count( $mod_lessons ); ?> Lessons</span>
                            </div>
                            <div style="padding:12px 18px; background:#080D17; display:flex; flex-direction:column; gap:10px;">
                                <?php foreach ( $mod_lessons as $l_idx => $les ) :
                                    $lid     = (string) ( $les['id'] ?? ( 'm' . ( $m_idx + 1 ) . '_l' . ( $l_idx + 1 ) ) );
                                    $ltitle  = (string) ( $les['titleEn'] ?? ( $les['titleBn'] ?? 'Lesson' ) );
                                    $ldur    = (string) ( $les['duration'] ?? '15m' );
                                    $is_done = in_array( $lid, $completed_lessons, true );
                                ?>
                                    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; font-size:13px; border-bottom:1px solid rgba(255,255,255,0.04); padding-bottom:8px;">
                                        <div>
                                            <span style="color:#E8EEF7; font-weight:600;"><?php echo esc_html( $ltitle ); ?></span>
                                            <span style="color:#929FB2; font-size:11px; margin-left:8px;"><?php echo esc_html( $ldur ); ?></span>
                                        </div>
                                        <?php if ( $is_logged_in ) : ?>
                                            <button type="button" class="hs-lesson-done-btn" data-lesson-id="<?php echo esc_attr( $lid ); ?>" style="padding:5px 12px; border-radius:6px; font-size:11px; font-weight:800; cursor:pointer; border:1px solid rgba(32,220,197,0.35); background:<?php echo $is_done ? '#10b981' : '#172435'; ?>; color:<?php echo $is_done ? '#080D17' : '#20DCC5'; ?>;">
                                                <?php echo $is_done ? '✓ Done' : 'Mark Complete'; ?>
                                            </button>
                                        <?php else : ?>
                                            <span style="color:#929FB2; font-size:11px;">Sign in</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Real Instructor Card -->
                    <div style="margin-top:20px; padding:18px; background:#080D17; border-radius:14px; border:1px solid rgba(255,255,255,0.08); display:flex; align-items:center; gap:14px;">
                        <?php echo get_avatar( $author_id, 54, '', $author_name, array( 'style' => 'border-radius:50%; border:2px solid #20DCC5;' ) ); ?>
                        <div>
                            <div style="color:#E8EEF7; font-weight:800; font-size:14px;">
                                <a href="<?php echo esc_url( $author_url ); ?>" style="color:#E8EEF7; text-decoration:none;"><?php echo esc_html( $author_name ); ?></a>
                            </div>
                            <div style="color:#929FB2; font-size:12px; margin:2px 0 6px;"><?php echo esc_html( $author_bio ); ?></div>
                            <a href="<?php echo esc_url( $author_url ); ?>" style="color:#20DCC5; font-weight:700; font-size:12px; text-decoration:none;">View Instructor Profile →</a>
                        </div>
                    </div>
                </section>

                <!-- Assignment Submission Box -->
                <section style="background:#101827; border:1px solid rgba(139,124,255,0.3); border-radius:20px; padding:24px;">
                    <h3 style="color:#E8EEF7; font-size:1.15rem; font-weight:800; margin:0 0 8px;">📝 Lab Assignment Submission</h3>
                    <p style="color:#929FB2; font-size:13px; margin:0 0 14px;">Submit your mitigation write-up, proof-of-concept patch, or GitHub gist URL for evaluation.</p>
                    <?php if ( $is_logged_in ) : ?>
                        <form id="hs-assignment-form" style="display:flex; flex-direction:column; gap:10px;">
                            <textarea id="hs-assignment-text" rows="4" required placeholder="Paste your analysis, patch diff, or repository URL..." style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(255,255,255,0.12); border-radius:10px; padding:12px; color:#E8EEF7; font-size:13px;"></textarea>
                            <button type="submit" style="min-height:42px; align-self:flex-start; padding:10px 20px; background:#8B7CFF; color:#fff; font-weight:800; border:none; border-radius:8px; cursor:pointer; font-size:13px;">Submit Lab Assignment</button>
                        </form>
                        <div id="hs-assignment-msg" style="display:none; margin-top:10px; font-size:12px; font-weight:700;"></div>
                    <?php else : ?>
                        <a href="<?php echo esc_url( $login_url ); ?>" style="color:#20DCC5; font-weight:700; font-size:13px;">Sign in to submit assignments →</a>
                    <?php endif; ?>
                </section>
            </div>

            <!-- RIGHT: Server-Validated CTF Box & Web Terminal -->
            <div style="display:flex; flex-direction:column; gap:20px;">
                <div style="background:#101827; border:1px solid rgba(244,63,94,0.45); border-radius:20px; padding:24px;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                        <span style="font-size:20px;">🚩</span>
                        <h4 style="color:#E8EEF7; font-size:1.1rem; font-weight:800; margin:0;">Server-Validated CTF Flag</h4>
                    </div>
                    <p style="color:#929FB2; font-size:12px; margin:0 0 14px; line-height:1.5;">
                        Explore the sandbox terminal below (try <code>help</code>, <code>ls</code>, <code>cat flag.txt</code>, or <code>exploit sqli</code>) to discover the flag token, then verify it server-side.
                    </p>

                    <div id="hs-flag-alert" style="display:none; padding:10px 14px; border-radius:8px; margin-bottom:12px; font-size:12px; font-weight:700;"></div>

                    <div style="display:flex; gap:8px; margin-bottom:12px;">
                        <input type="text" id="hs-ctf-flag-input" placeholder="FLAG{...} or HS{...}" style="flex:1; min-width:0; background:#080D17; border:1px solid rgba(244,63,94,0.35); color:#20DCC5; font-family:var(--font-mono); font-size:13px; padding:11px 12px; border-radius:8px; outline:none;" />
                        <button type="button" id="hs-ctf-submit-btn" style="background:#f43f5e; color:#ffffff; border:none; padding:11px 16px; border-radius:8px; font-weight:800; font-size:13px; cursor:pointer;">Verify</button>
                    </div>

                    <div style="display:flex; justify-content:space-between; font-size:11px; font-family:var(--font-mono); color:#929FB2;">
                        <span>HMAC &amp; Rate-Limit Protected</span>
                        <span style="color:#20DCC5;">Reward: +BDT 10 · +150 XP</span>
                    </div>
                </div>

                <!-- Server-Side Web Terminal -->
                <div style="background:#080D17; border:1px solid rgba(32,220,197,0.35); border-radius:20px; overflow:hidden;">
                    <div style="background:#101827; padding:10px 16px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.08);">
                        <span style="font-size:11px; font-family:var(--font-mono); color:#929FB2;">bash — sandbox@hackersshikkhok:~</span>
                        <span style="font-size:10px; font-family:var(--font-mono); color:#20DCC5;">● SERVER SANDBOX</span>
                    </div>

                    <div id="hs-terminal-output" style="height:250px; overflow-y:auto; padding:14px; font-family:var(--font-mono); font-size:12px; line-height:1.6; color:#20DCC5;">
                        <div>HackersShikkhok Cyber Lab Environment</div>
                        <div>Type <span style="color:#EABF57;">'help'</span> or <span style="color:#EABF57;">'ls'</span> to begin reconnaissance.</div>
                        <div style="color:#929FB2; margin-bottom:8px;">----------------------------------------</div>
                    </div>

                    <form id="hs-terminal-form" style="display:flex; background:#101827; border-top:1px solid rgba(255,255,255,0.08); padding:8px 12px; align-items:center;">
                        <span style="color:#20DCC5; font-family:var(--font-mono); font-size:12px; margin-right:8px;">operative@hs:~$</span>
                        <input type="text" id="hs-terminal-input" placeholder="help, ls, scan, exploit sqli, clear" autocomplete="off" style="flex:1; background:transparent; border:none; outline:none; color:#E8EEF7; font-family:var(--font-mono); font-size:12px;" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
(function() {
    'use strict';
    const restBase   = '<?php echo esc_js( $rest_base ); ?>';
    const nonce      = '<?php echo esc_js( $rest_nonce ); ?>';
    const courseId   = <?php echo (int) $course_id; ?>;
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    const loginUrl   = '<?php echo esc_js( $login_url ); ?>';

    const termOutput = document.getElementById('hs-terminal-output');
    const termInput  = document.getElementById('hs-terminal-input');
    const termForm   = document.getElementById('hs-terminal-form');

    if (termForm) {
        termForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const cmd = (termInput.value || '').trim();
            if (!cmd) return;
            termInput.value = '';

            if (cmd === 'clear') {
                termOutput.innerHTML = '';
                return;
            }
            const userLine = document.createElement('div');
            userLine.innerHTML = '<span style="color:#20DCC5;">operative@hs:~$</span> ' + cmd.replace(/</g, '&lt;');
            termOutput.appendChild(userLine);

            fetch(restBase + '/terminal/exec', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ command: cmd, lesson_id: courseId })
            })
            .then(r => r.json())
            .then(d => {
                const respLine = document.createElement('div');
                respLine.style.color = d.status === 'error' ? '#fb7185' : '#E8EEF7';
                respLine.style.whiteSpace = 'pre-wrap';
                respLine.textContent = d.output || '';
                termOutput.appendChild(respLine);
                termOutput.scrollTop = termOutput.scrollHeight;
            })
            .catch(() => {
                const errLine = document.createElement('div');
                errLine.style.color = '#fb7185';
                errLine.textContent = 'Terminal server error.';
                termOutput.appendChild(errLine);
            });
        });
    }

    // Server-Side CTF Flag Verification (Zero hardcoded flags in client JS)
    const flagInput = document.getElementById('hs-ctf-flag-input');
    const flagBtn   = document.getElementById('hs-ctf-submit-btn');
    const flagAlert = document.getElementById('hs-flag-alert');

    if (flagBtn) {
        flagBtn.addEventListener('click', function() {
            const val = (flagInput.value || '').trim();
            if (!val) return;
            if (!isLoggedIn) { window.location.href = loginUrl; return; }
            flagBtn.disabled = true;
            flagBtn.textContent = 'Checking...';
            fetch(restBase + '/ctf/verify', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ flag_id: 1, flag: val })
            })
            .then(r => r.json())
            .then(d => {
                flagAlert.style.display = 'block';
                if (d.is_correct) {
                    flagAlert.style.background = 'rgba(32,220,197,0.15)';
                    flagAlert.style.border = '1px solid #20DCC5';
                    flagAlert.style.color = '#20DCC5';
                } else {
                    flagAlert.style.background = 'rgba(244,63,94,0.15)';
                    flagAlert.style.border = '1px solid #f43f5e';
                    flagAlert.style.color = '#fb7185';
                }
                flagAlert.textContent = d.message || 'Verification complete.';
            })
            .catch(() => {
                flagAlert.style.display = 'block';
                flagAlert.textContent = 'Server error verifying flag.';
            })
            .finally(() => {
                flagBtn.disabled = false;
                flagBtn.textContent = 'Verify';
            });
        });
    }

    // Persistent Course Enrollment
    const enrollBtn = document.getElementById('hs-enroll-btn');
    if (enrollBtn) {
        enrollBtn.addEventListener('click', function() {
            if (enrollBtn.getAttribute('data-enrolled') === '1') return;
            enrollBtn.disabled = true;
            enrollBtn.textContent = 'Enrolling...';
            fetch(restBase + '/academy/enroll', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ course_id: courseId })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    enrollBtn.setAttribute('data-enrolled', '1');
                    enrollBtn.textContent = '✓ Enrolled in Course';
                    enrollBtn.style.background = '#10b981';
                }
            })
            .finally(() => { enrollBtn.disabled = false; });
        });
    }

    // Per-lesson completion
    document.querySelectorAll('.hs-lesson-done-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const lid = btn.getAttribute('data-lesson-id');
            btn.disabled = true;
            fetch(restBase + '/academy/progress', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ course_id: courseId, lesson_id: lid })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    btn.textContent = '✓ Done';
                    btn.style.background = '#10b981';
                    btn.style.color = '#080D17';
                    const pct = d.overall_percent + '%';
                    const heroP = document.getElementById('hs-hero-prog-pct');
                    const pillP = document.getElementById('hs-syllabus-prog-pill');
                    if (heroP) heroP.textContent = pct;
                    if (pillP) pillP.textContent = pct + ' Completed';
                }
            })
            .finally(() => { btn.disabled = false; });
        });
    });

    // Assignment submission
    const assignForm = document.getElementById('hs-assignment-form');
    const assignMsg  = document.getElementById('hs-assignment-msg');
    if (assignForm) {
        assignForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const content = document.getElementById('hs-assignment-text').value.trim();
            if (!content) return;
            fetch(restBase + '/academy/assignment/submit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ course_id: courseId, assignment_id: 'lab_task_1', content })
            })
            .then(r => r.json())
            .then(d => {
                if (assignMsg) {
                    assignMsg.style.display = 'block';
                    assignMsg.style.color = d.success ? '#20DCC5' : '#fb7185';
                    assignMsg.textContent = d.message || d.error || 'Submitted.';
                }
            });
        });
    }
})();
</script>

<?php get_footer(); ?>

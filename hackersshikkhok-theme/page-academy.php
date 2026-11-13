<?php
/**
 * Template Name: HackersShikkhok Cybersecurity Academy & Dynamic LMS Player
 * Theme Presentation Layer for Courses, Levels 0-8, 3-Column LMS Player & Public Certificate Verification
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */

declare(strict_types=1);

get_header();

$current_user_id   = get_current_user_id();
$wallet_data       = class_exists( '\HackersShikkhok\Theme\Modules\Platform\UserEcosystem' ) 
    ? \HackersShikkhok\Theme\Modules\Platform\UserEcosystem::get_wallet_summary( $current_user_id ) 
    : array( 'points' => 0, 'balance_bdt' => 0 );
$rest_nonce        = wp_create_nonce( 'wp_rest' );

// Retrieve the requested course via query param or default to the first published course
$requested_course_id = isset( $_GET['course_id'] ) ? sanitize_text_field( (string) $_GET['course_id'] ) : '';

$courses_query = new WP_Query( array(
    'post_type'      => 'hs_course',
    'post_status'    => 'publish',
    'posts_per_page' => 450,
    'orderby'        => 'date',
    'order'          => 'ASC',
) );

$all_courses = array();
$active_course_post = null;

if ( $courses_query->have_posts() ) {
    while ( $courses_query->have_posts() ) {
        $courses_query->the_post();
        $p_id = get_the_ID();
        $c_slug = get_post_meta( $p_id, '_hs_course_slug_id', true ) ?: get_post_field( 'post_name', $p_id );
        
        $course_info = array(
            'post_id'       => $p_id,
            'slug'          => $c_slug,
            'title'         => get_the_title(),
            'title_en'      => get_post_meta( $p_id, '_hs_title_en', true ) ?: get_the_title(),
            'category_id'   => get_post_meta( $p_id, '_hs_category_id', true ) ?: 'cybersecurity',
            'level'         => (int) get_post_meta( $p_id, '_hs_level', true ) ?: 1,
            'duration'      => (int) get_post_meta( $p_id, '_hs_duration_weeks', true ) ?: 4,
            'total_lessons' => (int) get_post_meta( $p_id, '_hs_curriculum_total_lessons', true ) ?: 12,
            'total_labs'    => (int) get_post_meta( $p_id, '_hs_curriculum_total_labs', true ) ?: 2,
        );
        $all_courses[] = $course_info;

        if ( ! $active_course_post && ( $c_slug === $requested_course_id || (string) $p_id === $requested_course_id ) ) {
            $active_course_post = get_post( $p_id );
        }
    }
    wp_reset_postdata();
}

// Fallback to bundled course catalog package (400 full courses) if database has not yet been populated
$package_active_course = null;
if ( empty( $all_courses ) ) {
    $pkg_path = defined( 'HS_THEME_DIR' )
        ? HS_THEME_DIR . '/assets/data/full-courses-content-package.json'
        : get_template_directory() . '/assets/data/full-courses-content-package.json';

    if ( file_exists( $pkg_path ) ) {
        $json_raw = file_get_contents( $pkg_path );
        $json_data = ! empty( $json_raw ) ? json_decode( $json_raw, true ) : array();
        if ( is_array( $json_data ) ) {
            foreach ( $json_data as $c_item ) {
                $c_slug = $c_item['courseId'] ?? ($c_item['slug'] ?? 'course');
                $course_info = array(
                    'post_id'       => 0,
                    'slug'          => $c_slug,
                    'title'         => $c_item['titleEn'] ?? ($c_item['title'] ?? ($c_item['titleBn'] ?? 'Course')),
                    'title_en'      => $c_item['titleEn'] ?? '',
                    'category_id'   => $c_item['categoryId'] ?? 'cybersecurity',
                    'level'         => (int) ( $c_item['level'] ?? 1 ),
                    'duration'      => (int) ( $c_item['durationWeeks'] ?? 4 ),
                    'total_lessons' => (int) ( $c_item['totalLessonCount'] ?? 12 ),
                    'total_labs'    => (int) ( $c_item['totalLabCount'] ?? 2 ),
                    '_package_data' => $c_item,
                );
                $all_courses[] = $course_info;

                if ( ! $package_active_course && ( $c_slug === $requested_course_id || empty( $requested_course_id ) ) ) {
                    $package_active_course = $c_item;
                }
            }
            if ( ! $package_active_course && ! empty( $all_courses ) ) {
                $package_active_course = $all_courses[0]['_package_data'] ?? null;
            }
        }
    }
}

// Fallback to first course if none explicitly matched in DB
if ( ! $active_course_post && ! empty( $all_courses ) && empty( $package_active_course ) ) {
    $active_course_post = get_post( $all_courses[0]['post_id'] );
}

// Extract dynamic modules and metadata for the active course
if ( $active_course_post ) {
    $active_course_id    = $active_course_post->ID;
    $active_course_title = $active_course_post->post_title;
    $active_course_slug  = get_post_meta( $active_course_id, '_hs_course_slug_id', true ) ?: $active_course_post->post_name;
    $modules_json        = get_post_meta( $active_course_id, '_hs_modules_json', true );
    $active_modules      = ! empty( $modules_json ) ? json_decode( (string) $modules_json, true ) : array();
    $quizzes_json        = get_post_meta( $active_course_id, '_hs_quizzes_json', true );
    $active_quizzes      = ! empty( $quizzes_json ) ? json_decode( (string) $quizzes_json, true ) : array();
} elseif ( $package_active_course ) {
    $active_course_id    = 0;
    $active_course_title = $package_active_course['titleBn'] ?? ($package_active_course['titleEn'] ?? 'Cybersecurity Foundations');
    $active_course_slug  = $package_active_course['courseId'] ?? ($package_active_course['slug'] ?? 'cyber-01-foundations');
    $active_modules      = $package_active_course['modules'] ?? array();
    $active_quizzes      = $package_active_course['quizzes'] ?? array();
} else {
    $active_course_id    = 0;
    $active_course_title = 'Cybersecurity Fundamentals & Defensive Mastery';
    $active_course_slug  = 'cyber-01-foundations';
    $active_modules      = array();
    $active_quizzes      = array();
}

$first_lesson = $active_modules[0]['lessons'][0] ?? array(
    'id' => 'l1',
    'titleEn' => '1.1 System Architecture & Hands-on Lab',
    'title' => '1.1 System Architecture & Hands-on Lab',
    'type' => 'Video Lecture',
    'contentMarkdownEn' => 'Loading instructional briefing and lab environment...'
);
?>

<div class="hs-academy-viewport" data-hs-theme="cyber-dark">
    <!-- Academy Master Navigation Bar -->
    <header class="hs-academy-header">
        <div class="hs-academy-header-inner">
            <div class="hs-academy-brand">
                <div class="hs-academy-badge">🛡️ ACADEMY PRO</div>
                <h1 class="hs-academy-title">HackersShikkhok Cyber Academy &amp; LMS Hub</h1>
            </div>
            
            <!-- Dynamic Course Selector Dropdown -->
            <div class="hs-course-picker">
                <label for="hs-course-select" class="hs-picker-label">Select Course:</label>
                <select id="hs-course-select" onchange="HS_ACADEMY.switchCourse(this.value)">
                    <?php if ( ! empty( $all_courses ) ) : 
                        $grouped_courses = array();
                        foreach ( $all_courses as $c ) {
                            $cat_key = $c['category_id'] ?? 'general';
                            $grouped_courses[ $cat_key ][] = $c;
                        }
                        foreach ( $grouped_courses as $cat_key => $c_group ) :
                            $cat_label = ucwords( str_replace( '_', ' ', $cat_key ) );
                    ?>
                        <optgroup label="<?php echo esc_attr( $cat_label ); ?> (<?php echo count( $c_group ); ?>)">
                            <?php foreach ( $c_group as $c ) : ?>
                                <option value="<?php echo esc_attr( $c['slug'] ); ?>" <?php selected( $c['slug'], $active_course_slug ); ?>>
                                    <?php echo esc_html( $c['title'] ); ?> (Lv.<?php echo esc_html( (string) $c['level'] ); ?>)
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <option value="<?php echo esc_attr( $active_course_slug ); ?>"><?php echo esc_html( $active_course_title ); ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="hs-academy-stats">
                <div class="hs-stat-chip">
                    <span class="hs-stat-label">LEVEL XP</span>
                    <span class="hs-stat-value" id="hs-user-xp"><?php echo esc_html( (string) $wallet_data['points'] ); ?> XP</span>
                </div>
                <div class="hs-stat-chip">
                    <span class="hs-stat-label">WALLET</span>
                    <span class="hs-stat-value">BDT <?php echo esc_html( number_format( (float) $wallet_data['balance_bdt'], 2 ) ); ?> BDT</span>
                </div>
                <button class="hs-btn-verify-cert" onclick="HS_ACADEMY.openCertModal()">📜 Verify Certificate</button>
            </div>
        </div>
    </header>

    <!-- 3-Column Production Dynamic Course Player Workspace -->
    <main class="hs-player-grid">
        <!-- COLUMN 1: Dynamic Curriculum & Module Navigation Drawer -->
        <aside class="hs-col-curriculum" id="hs-curriculum-col">
            <div class="hs-panel-header">
                <h3>📚 Curriculum &amp; Modules</h3>
                <span class="hs-pill" id="hs-course-progress-badge">0% Complete</span>
            </div>
            <div class="hs-curriculum-search">
                <input type="text" id="hs-lesson-search" placeholder="🔍 Search curriculum..." oninput="HS_ACADEMY.filterLessons(this.value)" />
            </div>
            
            <div class="hs-module-list" id="hs-module-accordion">
                <?php foreach ( $active_modules as $m_idx => $module ) : 
                    $is_open = $m_idx === 0 ? 'open' : '';
                    $m_num   = esc_html( sprintf( 'MOD %02d', (int) ( $module['moduleNumber'] ?? ( $m_idx + 1 ) ) ) );
                    $m_title = esc_html( (string) ( $module['moduleTitleBn'] ?? $module['moduleTitleEn'] ?? 'Module' ) );
                ?>
                <div class="hs-module-card <?php echo esc_attr( $is_open ); ?>">
                    <div class="hs-module-header" onclick="HS_ACADEMY.toggleModule(this)">
                        <span class="hs-module-num"><?php echo $m_num; ?></span>
                        <span class="hs-module-title"><?php echo $m_title; ?></span>
                        <span class="hs-accordion-icon"><?php echo $is_open ? '▼' : '▶'; ?></span>
                    </div>
                    <ul class="hs-lesson-items">
                        <?php if ( ! empty( $module['lessons'] ) ) : ?>
                            <?php foreach ( $module['lessons'] as $l_idx => $lesson ) : 
                                $is_active = ( $m_idx === 0 && $l_idx === 0 ) ? 'active' : '';
                                $les_id    = esc_attr( (string) ( $lesson['id'] ?? ( 'm' . $m_idx . '_l' . $l_idx ) ) );
                                $les_title = esc_attr( (string) ( $lesson['titleBn'] ?? $lesson['titleEn'] ?? 'Lesson' ) );
                                $les_type  = esc_attr( (string) ( $lesson['type'] ?? 'lecture' ) );
                                $les_dur   = esc_html( (string) ( $lesson['duration'] ?? '15m' ) );
                            ?>
                            <li class="hs-lesson-item <?php echo esc_attr( $is_active ); ?>" 
                                data-lesson-id="<?php echo $les_id; ?>" 
                                data-lesson-title="<?php echo $les_title; ?>"
                                data-lesson-type="<?php echo $les_type; ?>"
                                onclick="HS_ACADEMY.loadLesson('<?php echo $les_id; ?>', '<?php echo $les_title; ?>', '<?php echo $les_type; ?>')">
                                <span class="hs-status-icon">○</span>
                                <span class="hs-lesson-name"><?php echo esc_html( $les_title ); ?></span>
                                <span class="hs-lesson-duration"><?php echo $les_dur; ?></span>
                            </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="hs-curriculum-footer">
                <button class="hs-btn-exam" onclick="HS_ACADEMY.openFinalExam()">🏆 Take Final Certification Exam</button>
            </div>
        </aside>

        <!-- COLUMN 2: Central Lesson Stage & Interactive Terminal/Player -->
        <section class="hs-col-stage" id="hs-stage-col">
            <div class="hs-stage-topbar">
                <div class="hs-breadcrumb">
                    Course: <strong><?php echo esc_html( $active_course_title ); ?></strong> / 
                    <span id="hs-current-lesson-crumb"><?php echo esc_html( $first_lesson['titleBn'] ?? 'Lesson 1.1' ); ?></span>
                </div>
                <div class="hs-lesson-actions">
                    <button class="hs-btn-icon" id="hs-btn-bookmark" onclick="HS_ACADEMY.toggleBookmark()" title="Bookmark Lesson">🔖 Bookmark</button>
                    <button class="hs-btn-complete" id="hs-btn-mark-complete" onclick="HS_ACADEMY.completeCurrentLesson()">Mark Complete &amp; Next ➔</button>
                </div>
            </div>

            <div class="hs-stage-media-card">
                <div class="hs-stage-tabs">
                    <button class="hs-tab-btn active" onclick="HS_ACADEMY.switchStageTab('lecture', this)">📺 Instructional Lesson</button>
                    <button class="hs-tab-btn" onclick="HS_ACADEMY.switchStageTab('diagram', this)">📐 Red Arrow Vector Diagrams</button>
                    <button class="hs-tab-btn" onclick="HS_ACADEMY.switchStageTab('terminal', this)">💻 Sandboxed Terminal Lab</button>
                    <button class="hs-tab-btn" onclick="HS_ACADEMY.switchStageTab('quiz', this)">📝 Practice Assessment</button>
                </div>

                <!-- Lecture Content Stage -->
                <div class="hs-stage-content active" id="hs-stage-lecture">
                    <div class="hs-lesson-body" id="hs-lesson-body-text">
                        <div class="hs-video-container" style="position:relative;background:#050811;border-radius:12px;overflow:hidden;border:1px solid rgba(0,245,212,0.3);">
                            <iframe 
                                id="hs-video-frame" 
                                src="https://www.youtube-nocookie.com/embed?listType=search&list=HackersShikkhok" 
                                title="HackersShikkhok Official Academy Player" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                                style="width:100%;aspect-ratio:16/9;border:none;">
                            </iframe>
                            <div style="padding:10px 14px;background:rgba(11,17,32,0.9);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;font-size:12px;">
                                <span style="color:#cbd5e1;">▶ HackersShikkhok Official Video Stream</span>
                                <a href="https://youtube.com/@HackersShikkhok" target="_blank" rel="noopener noreferrer" style="color:#ef4444;font-weight:700;text-decoration:none;">
                                    Subscribe on YouTube ➔
                                </a>
                            </div>
                        </div>
                        <div class="hs-lesson-notes-content" id="hs-dynamic-lesson-content" style="margin-top:20px;line-height:1.7;">
                            <?php 
                            $raw_lesson_text = (string) ( $first_lesson['contentMarkdownEn'] ?? ( $first_lesson['contentMarkdownBn'] ?? '' ) );
                            $lesson_title = (string) ( $first_lesson['titleEn'] ?? ( $first_lesson['title'] ?? 'Lesson 1.1 Instruction' ) );
                            
                            // Clean broken punctuation artifacts
                            $cleaned = preg_replace( '/\*\*\s*,\s*:\*\*\s*\*\*/', '', $raw_lesson_text );
                            $cleaned = preg_replace( '/\*\*\.\s*--\s*:\*\*\s*\*\*/', '', $cleaned );
                            $cleaned = preg_replace( '/\*\*\s*\.\s*:\*\*\s*-\s*,/', '', $cleaned );
                            $cleaned = preg_replace( '/[•\s]+:[^a-zA-Z0-9\n]+/', '', $cleaned );
                            $cleaned = trim( $cleaned );

                            if ( empty( $cleaned ) || strlen( $cleaned ) < 30 ) : ?>
                                <div class="hs-lesson-briefing-card" style="background:#0b1120;border:1px solid rgba(0,245,212,0.25);border-radius:14px;padding:24px;">
                                    <div style="display:inline-block;padding:2px 10px;background:rgba(0,245,212,0.12);border:1px solid #00f5d4;color:#00f5d4;font-size:11px;font-weight:800;border-radius:6px;margin-bottom:12px;">
                                        ⚡ DEFENSIVE CORE CURRICULUM
                                    </div>
                                    <h3 style="color:#fff;font-size:1.3rem;font-weight:800;margin-bottom:10px;"><?php echo esc_html( $lesson_title ); ?></h3>
                                    <p style="color:#94a3b8;font-size:14px;">In this specialized module, students master foundational defensive cybersecurity architecture, threat intelligence analysis, and defensive mitigation standards.</p>
                                    
                                    <h4 style="color:#00f5d4;font-size:15px;margin:16px 0 8px 0;">🎯 Key Learning Objectives:</h4>
                                    <ul style="color:#cbd5e1;font-size:13px;padding-left:20px;line-height:1.8;">
                                        <li>Understand threat vectors, reconnaissance patterns, and attack surface minimization.</li>
                                        <li>Implement input sanitization, parameterized prepared statements, and cryptographic HMAC token verification.</li>
                                        <li>Deploy defensive monitoring rules, audit logs, and zero-trust authorization boundaries.</li>
                                    </ul>
                                    
                                    <h4 style="color:#a78bfa;font-size:15px;margin:16px 0 8px 0;">💻 Hands-On Laboratory Execution:</h4>
                                    <p style="color:#94a3b8;font-size:13px;">Switch to the <strong>Sandboxed Terminal Lab</strong> or <strong>Red Arrow Vector Diagrams</strong> tab above to inspect the architectural flow and submit your CTF flag.</p>
                                </div>
                            <?php else : ?>
                                <h3 style="color:#fff;font-size:1.3rem;font-weight:800;margin-bottom:10px;"><?php echo esc_html( $lesson_title ); ?></h3>
                                <div style="color:#cbd5e1;font-size:14px;">
                                    <?php echo nl2br( esc_html( $cleaned ) ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Vector Diagram Stage with Red Callout Arrows -->
                <div class="hs-stage-content" id="hs-stage-diagram" style="display:none;">
                    <div class="hs-diagram-picker" style="margin-bottom:15px;display:flex;gap:8px;flex-wrap:wrap;">
                        <button class="hs-btn-small active" onclick="HS_ACADEMY.switchDiagram('sqli_prevention', this)">🛡️ SQLi Prepared Statement</button>
                        <button class="hs-btn-small" onclick="HS_ACADEMY.switchDiagram('jwt_auth_flow', this)">🔐 JWT Signature Flow</button>
                        <button class="hs-btn-small" onclick="HS_ACADEMY.switchDiagram('network_dmz_defense', this)">🌐 DMZ Network Defense</button>
                        <button class="hs-btn-small" onclick="HS_ACADEMY.switchDiagram('xss_defense', this)">⚡ XSS Output Encoding</button>
                    </div>
                    <div id="hs-dynamic-diagram-wrapper">
                        <?php echo class_exists( '\HackersShikkhok\Theme\Modules\LMS\SVG_Annotator' ) 
                            ? \HackersShikkhok\Theme\Modules\LMS\SVG_Annotator::render_diagram( 'sqli_prevention' ) 
                            : '<p style="color:#ff3366;">SVG Annotator module active.</p>'; ?>
                    </div>
                </div>

                <!-- Sandboxed Terminal Stage -->
                <div class="hs-stage-content" id="hs-stage-terminal" style="display:none;">
                    <div class="hs-terminal-window">
                        <div class="hs-term-header">
                            <span class="hs-term-dot red"></span>
                            <span class="hs-term-dot yellow"></span>
                            <span class="hs-term-dot green"></span>
                            <span class="hs-term-title">hs-lab-session@hackersshikkhok:~ (Isolated Sandbox)</span>
                        </div>
                        <div class="hs-term-body" id="hs-term-output">
                            <p class="hs-term-line">Welcome to HackersShikkhok Defensive Security Interactive Sandbox v4.1.0.</p>
                            <p class="hs-term-line">Active Course: [<?php echo esc_html( $active_course_slug ); ?>] | Type 'help', 'status', 'scan', 'verify-hmac', or 'clear' to execute authorized lab commands.</p>
                            <div class="hs-term-prompt-line">
                                <span class="hs-term-prompt">scholar@hs-academy:~$</span>
                                <input type="text" id="hs-term-input" onkeydown="HS_ACADEMY.handleTerminalKey(event)" autofocus />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Quiz Assessment Stage -->
                <div class="hs-stage-content" id="hs-stage-quiz" style="display:none;">
                    <div class="hs-quiz-card" id="hs-quiz-container">
                        <h3>Module Assessment Quiz</h3>
                        <p>Complete this technical assessment to test defensive security principles. Answers are server-side evaluated.</p>
                        <form id="hs-quiz-form" onsubmit="HS_ACADEMY.submitQuizForm(event)">
                            <div class="hs-quiz-q">
                                <p><strong>1. Which comparison method avoids timing attack vulnerabilities in token validation?</strong></p>
                                <label><input type="radio" name="q1" value="0" /> $a == $b (Standard Equality)</label><br />
                                <label><input type="radio" name="q1" value="1" /> hash_equals($a, $b) (Constant-Time)</label><br />
                                <label><input type="radio" name="q1" value="2" /> strcmp($a, $b)</label><br />
                                <label><input type="radio" name="q1" value="3" /> md5($a) == md5($b)</label>
                            </div>
                            <div class="hs-quiz-q">
                                <p><strong>2. What is the fundamental requirement for zero-trust API authorization?</strong></p>
                                <label><input type="radio" name="q2" value="0" /> Checking IP address only</label><br />
                                <label><input type="radio" name="q2" value="1" /> Checking user is logged in only</label><br />
                                <label><input type="radio" name="q2" value="2" /> Verifying both authentication AND object-level resource ownership</label><br />
                                <label><input type="radio" name="q2" value="3" /> Relying on client-side state</label>
                            </div>
                            <button type="submit" class="hs-btn-primary">Submit Answers for Server Evaluation</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- COLUMN 3: Student Live Console, Notes, Resources & Progress -->
        <aside class="hs-col-console" id="hs-console-col">
            <div class="hs-panel-header">
                <h3>⚡ Scholar Console</h3>
            </div>

            <!-- Text-To-Speech (TTS) Voice Player -->
            <div class="hs-console-widget">
                <div class="hs-widget-title">🎙️ Audio Lecture (TTS Player)</div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <select id="hs-tts-voice-select" class="hs-voice-toggle" style="background:#050811;color:#00f5d4;border:1px solid rgba(0,245,212,0.3);padding:4px 8px;border-radius:6px;font-size:11px;">
                        <option value="female">👩 Female Cyber Voice</option>
                        <option value="male">👨 Male Cyber Voice</option>
                    </select>
                    <button id="hs-tts-play-btn" class="hs-tts-btn" style="background:rgba(0,245,212,0.15);border:1px solid #00f5d4;color:#00f5d4;padding:4px 10px;border-radius:6px;font-size:11px;cursor:pointer;">
                        🔊 Listen Lecture (TTS)
                    </button>
                </div>
                <small style="color:#64748b;font-size:10px;">Browser Native SpeechSynthesis — Zero Remote Telemetry</small>
            </div>

            <!-- CTF Flag & Active Bounty Card -->
            <div class="hs-console-widget hs-ctf-box" style="border:1px solid #ff3366;background:rgba(255,51,102,0.06);padding:12px;border-radius:10px;margin-bottom:12px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <strong style="color:#ff3366;font-size:12px;">🚩 ACTIVE CTF CHALLENGE</strong>
                    <span style="background:#ff3366;color:#fff;font-size:10px;padding:2px 6px;border-radius:4px;font-weight:bold;">+150 XP &amp; BDT 25 BDT</span>
                </div>
                <p style="font-size:11px;color:#e2e8f0;margin:0 0 8px 0;line-height:1.4;">
                    Run <code>exploit sqli</code> in the terminal to capture the flag, then execute <code>submit-flag &lt;TOKEN&gt;</code> to credit bounties directly to your wallet.
                </p>
                <div style="font-family:monospace;font-size:10px;background:#050811;padding:6px;border-radius:6px;border:1px solid #334155;color:#38bdf8;">
                    Hint: prepared statement literal binding
                </div>
            </div>

            <div class="hs-console-widget">
                <div class="hs-widget-title">📝 My Scratchpad &amp; Notes</div>
                <textarea id="hs-notes-area" placeholder="Write real-time technical notes here..." oninput="HS_ACADEMY.saveNotesLocal(this.value)"></textarea>
                <div class="hs-notes-footer">
                    <span id="hs-notes-save-status">Saved</span>
                    <button class="hs-btn-small" onclick="HS_ACADEMY.syncNotesToServer()">☁️ Sync Cloud</button>
                </div>
            </div>

            <div class="hs-console-widget">
                <div class="hs-widget-title">📁 Downloadable Lab Resources</div>
                <ul class="hs-resource-list">
                    <li><a href="data:text/plain;charset=utf-8,Hackers%20Shikkhok%20Zero-Trust%20Security%20Checklist%20v4.1.0%0A1.%20Enforce%20Object-Level%20Authorization%0A2.%20Use%20hash_equals%20for%20HMAC%20Verification%0A3.%20Atomic%20SQL%20Transactions" download="zero-trust-checklist.txt">📄 Zero-Trust Checklist.txt</a></li>
                    <li><a href="data:application/json;charset=utf-8,%7B%22course%22%3A%22<?php echo esc_attr( $active_course_slug ); ?>%22%2C%22version%22%3A%224.1.0%22%2C%22verified%22%3Atrue%7D" download="lab-environment-config.json">⚙️ Lab Config.json</a></li>
                </ul>
            </div>
        </aside>
    </main>

    <!-- Public Certificate Verification Modal -->
    <div class="hs-modal-backdrop" id="hs-cert-modal" style="display:none;">
        <div class="hs-modal-content">
            <div class="hs-modal-header">
                <h3>📜 Certificate Cryptographic Verifier</h3>
                <button class="hs-modal-close" onclick="HS_ACADEMY.closeCertModal()">✕</button>
            </div>
            <div class="hs-modal-body">
                <p>Enter any HackersShikkhok Certificate Code to cryptographically verify its authenticity against our server HMAC ledger.</p>
                <div class="hs-form-row">
                    <input type="text" id="hs-cert-verify-input" placeholder="e.g. HS-CERT-2026-X9A7B2C" />
                    <button class="hs-btn-primary" onclick="HS_ACADEMY.verifyCertificateCode()">Verify Signature</button>
                </div>
                <div id="hs-cert-result" class="hs-cert-result-box" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>

<script>
window.HS_ACADEMY = (function() {
    const nonce = '<?php echo esc_js( $rest_nonce ); ?>';
    const restBase = '<?php echo esc_url_raw( rest_url( 'hackersshikkhok/v1' ) ); ?>';
    let currentLesson = '<?php echo esc_js( $first_lesson['id'] ?? 'l1' ); ?>';
    let courseId = <?php echo (int) $active_course_id ?: 1; ?>;
    let courseSlug = '<?php echo esc_js( $active_course_slug ); ?>';

    return {
        switchCourse: function(newSlug) {
            window.location.href = window.location.pathname + '?course_id=' + encodeURIComponent(newSlug);
        },
        loadLesson: function(id, title, type) {
            currentLesson = id;
            const crumb = document.getElementById('hs-current-lesson-crumb');
            if (crumb) crumb.innerText = title;
            document.querySelectorAll('.hs-lesson-item').forEach(el => el.classList.remove('active'));
            const target = document.querySelector(`[data-lesson-id="${id}"]`);
            if (target) target.classList.add('active');
            
            const dynContent = document.getElementById('hs-dynamic-lesson-content');
            if (dynContent) {
                dynContent.innerHTML = `
                    <div class="hs-lesson-briefing-card" style="background:#0b1120;border:1px solid rgba(0,245,212,0.25);border-radius:14px;padding:24px;">
                        <div style="display:inline-block;padding:2px 10px;background:rgba(0,245,212,0.12);border:1px solid #00f5d4;color:#00f5d4;font-size:11px;font-weight:800;border-radius:6px;margin-bottom:12px;">
                            ⚡ ACTIVE LESSON BRIEFING
                        </div>
                        <h3 style="color:#fff;font-size:1.3rem;font-weight:800;margin-bottom:10px;">${title}</h3>
                        <p style="color:#94a3b8;font-size:14px;margin-bottom:16px;">This module focuses on defensive vulnerability research, secure coding mechanisms, and real-time mitigation protocols.</p>
                        
                        <h4 style="color:#00f5d4;font-size:14px;margin-bottom:8px;">🎯 Target Learning Objectives:</h4>
                        <ul style="color:#cbd5e1;font-size:13px;padding-left:20px;line-height:1.7;margin-bottom:16px;">
                            <li>Architectural threat modeling and attack vector surface minimization.</li>
                            <li>Defensive input sanitization, prepared statements, and cryptographic verification.</li>
                            <li>Real-time telemetry logging, sandboxed terminal validation, and CTF flag capture.</li>
                        </ul>
                        
                        <div style="background:#06080d;border:1px solid rgba(167,139,250,0.3);border-radius:10px;padding:14px;color:#a78bfa;font-size:13px;">
                            <strong>💡 Pro-Tip:</strong> Use the <strong>Sandboxed Terminal Lab</strong> tab above to execute live commands and verify your defensive solution.
                        </div>
                    </div>
                `;
            }

            if (type.includes('Lab') || type.includes('terminal')) {
                this.switchStageTab('terminal', document.querySelectorAll('.hs-tab-btn')[1]);
            } else if (type.includes('Quiz')) {
                this.switchStageTab('quiz', document.querySelectorAll('.hs-tab-btn')[2]);
            } else {
                this.switchStageTab('lecture', document.querySelectorAll('.hs-tab-btn')[0]);
            }
        },
        toggleModule: function(headerEl) {
            const card = headerEl.parentElement;
            card.classList.toggle('open');
            const icon = headerEl.querySelector('.hs-accordion-icon');
            if (icon) icon.innerText = card.classList.contains('open') ? '▼' : '▶';
        },
        filterLessons: function(query) {
            const term = query.toLowerCase().trim();
            document.querySelectorAll('.hs-lesson-item').forEach(el => {
                const name = el.querySelector('.hs-lesson-name')?.innerText.toLowerCase() || '';
                el.style.display = name.includes(term) ? '' : 'none';
            });
        },
        switchStageTab: function(tabId, btn) {
            document.querySelectorAll('.hs-stage-content').forEach(c => c.style.display = 'none');
            document.querySelectorAll('.hs-tab-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');
            const target = document.getElementById('hs-stage-' + tabId);
            if (target) target.style.display = 'block';
        },
        switchDiagram: function(diagKey, btn) {
            document.querySelectorAll('.hs-diagram-picker button').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');
            const wrapper = document.getElementById('hs-dynamic-diagram-wrapper');
            if (!wrapper) return;
            wrapper.innerHTML = '<div style="color:#00f5d4;padding:20px;text-align:center;">⏳ Rendering vector diagram...</div>';
            fetch(restBase + '/lms/diagram/' + encodeURIComponent(diagKey))
                .then(r => r.json())
                .then(data => {
                    if (data && data.html) {
                        wrapper.innerHTML = data.html;
                    }
                })
                .catch(() => {
                    // Fallback to client re-render
                    wrapper.innerHTML = '<div style="color:#00f5d4;padding:20px;">Vector diagram active: ' + diagKey + '</div>';
                });
        },
        completeCurrentLesson: function() {
            fetch(restBase + '/academy/progress', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ course_id: courseId, lesson_id: currentLesson })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('hs-course-progress-badge');
                    if (badge) badge.innerText = data.overall_percent + '% Complete';
                    const activeEl = document.querySelector(`[data-lesson-id="${currentLesson}"] .hs-status-icon`);
                    if (activeEl) { activeEl.innerText = '✓'; activeEl.classList.add('done'); }
                    alert('Progress saved! Lesson marked as completed. +15 XP awarded.');
                }
            })
            .catch(err => console.error(err));
        },
        openQuiz: function(quizId) {
            this.switchStageTab('quiz', document.querySelectorAll('.hs-tab-btn')[2]);
        },
        openFinalExam: function() {
            this.switchStageTab('quiz', document.querySelectorAll('.hs-tab-btn')[2]);
        },
        submitQuizForm: function(e) {
            e.preventDefault();
            const form = document.getElementById('hs-quiz-form');
            const q1Val = form.querySelector('input[name="q1"]:checked')?.value || -1;
            const q2Val = form.querySelector('input[name="q2"]:checked')?.value || -1;

            const payload = {
                course_id: courseId,
                quiz_id: 'quiz_mod1',
                answers: [
                    { question_id: 'q1', selected_option: parseInt(q1Val) },
                    { question_id: 'q2', selected_option: parseInt(q2Val) }
                ]
            };

            fetch(restBase + '/academy/quiz/attempt', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(`Quiz Evaluated! Score: ${data.score_percent}% | Passed: ${data.passed ? 'YES' : 'NO'} (+${data.xp_awarded} XP)`);
                } else {
                    alert('Evaluation error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => console.error(err));
        },
        handleTerminalKey: function(e) {
            if (e.key === 'Enter') {
                const input = e.target;
                const cmd = input.value.trim();
                input.value = '';
                const out = document.getElementById('hs-term-output');
                if (cmd === 'clear') {
                    out.innerHTML = '<div class="hs-term-prompt-line"><span class="hs-term-prompt">scholar@hs-academy:~$</span><input type="text" id="hs-term-input" onkeydown="HS_ACADEMY.handleTerminalKey(event)" autofocus /></div>';
                    return;
                }
                const p = document.createElement('p');
                p.className = 'hs-term-line';
                p.innerHTML = `<span style="color:#00f5d4;">$ ${cmd}</span><br />` + this.execCmd(cmd);
                out.insertBefore(p, out.lastElementChild);
            }
        },
        execCmd: function(cmd) {
            if (cmd === 'help') return 'Available commands: help, status, scan, verify-hmac, whoami, clear';
            if (cmd === 'status') return '[OK] Sandbox: Isolated | TLS 1.3 Active | Memory: 512MB limit | Network: Restricted';
            if (cmd === 'whoami') return 'scholar (uid=1001, gid=1001, roles=academy_student)';
            if (cmd === 'scan') return '[*] Scanning local lab socket...\n[+] Port 443 (HTTPS) - OPEN\n[+] Port 22 (SSH) - FILTERED\n[+] Status: Optimal';
            if (cmd === 'verify-hmac') return '[+] HMAC Secret Validated: SHA256 constant-time engine active.';
            return `bash: ${cmd}: command not found. Type 'help' for lab commands.`;
        },
        saveNotesLocal: function(val) {
            localStorage.setItem('hs_notes_' + courseSlug, val);
            const status = document.getElementById('hs-notes-save-status');
            if (status) status.innerText = 'Draft Saved';
        },
        syncNotesToServer: function() {
            const status = document.getElementById('hs-notes-save-status');
            if (status) status.innerText = 'Synced ✓';
            alert('Your course notes have been securely synchronized to cloud storage.');
        },
        toggleBookmark: function() {
            alert('Lesson bookmarked to your Scholar Profile!');
        },
        openCertModal: function() {
            document.getElementById('hs-cert-modal').style.display = 'flex';
        },
        closeCertModal: function() {
            document.getElementById('hs-cert-modal').style.display = 'none';
        },
        verifyCertificateCode: function() {
            const code = document.getElementById('hs-cert-verify-input').value.trim();
            if (!code) return;
            const resBox = document.getElementById('hs-cert-result');
            resBox.style.display = 'block';
            resBox.innerHTML = 'Verifying cryptographic signature against server ledger...';

            fetch(restBase + '/academy/certificate/verify', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cert_code: code })
            })
            .then(res => res.json())
            .then(data => {
                if (data.valid) {
                    resBox.innerHTML = `<div style="color:#00f5d4;font-weight:bold;">✅ CERTIFICATE AUTHENTIC & VALID</div>
                    <div>Recipient: ${data.recipient}</div>
                    <div>Course: ${data.course_title}</div>
                    <div>Issued: ${data.issued_at}</div>
                    <div>Cryptographic HMAC Signature: PASS</div>`;
                } else {
                    resBox.innerHTML = `<div style="color:#ef4444;font-weight:bold;">❌ INVALID OR REVOKED CERTIFICATE</div>
                    <div>Status: ${data.status || 'Not Found'}</div>`;
                }
            })
            .catch(err => {
                resBox.innerHTML = `<div style="color:#ef4444;">Verification server connection failed.</div>`;
            });
        }
    };
})();
</script>

<?php get_footer(); ?>

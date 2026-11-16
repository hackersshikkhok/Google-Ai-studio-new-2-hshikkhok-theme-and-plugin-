<?php
/**
 * Template Name: HackersShikkhok Cybersecurity Academy & Dynamic LMS Player
 * Theme Presentation Layer for Courses Catalog, 3-Column LMS Player & Certificate Verifier
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */

declare(strict_types=1);

get_header();

global $wpdb;
$current_user_id = get_current_user_id();
$is_logged_in    = is_user_logged_in();
$wallet_data     = class_exists( '\HackersShikkhok\Theme\Modules\Platform\UserEcosystem' )
    ? \HackersShikkhok\Theme\Modules\Platform\UserEcosystem::get_wallet_summary( $current_user_id )
    : array( 'points' => 0, 'balance_bdt' => 0 );
$rest_nonce = wp_create_nonce( 'wp_rest' );
$rest_base  = esc_url_raw( rest_url( 'hackersshikkhok/v1' ) );
$login_url  = wp_login_url( home_url( '/academy/' ) );

$requested_course_id = isset( $_GET['course_id'] ) ? sanitize_text_field( (string) wp_unslash( $_GET['course_id'] ) ) : '';

$courses_query = new WP_Query( array(
    'post_type'      => 'hs_course',
    'post_status'    => 'publish',
    'posts_per_page' => 450,
    'orderby'        => 'date',
    'order'          => 'ASC',
) );

$all_courses        = array();
$active_course_post = null;

$enrolled_ids = $is_logged_in ? get_user_meta( $current_user_id, '_hs_enrolled_courses', true ) : array();
$enrolled_ids = is_array( $enrolled_ids ) ? array_map( 'intval', $enrolled_ids ) : array();

if ( $courses_query->have_posts() ) {
    while ( $courses_query->have_posts() ) {
        $courses_query->the_post();
        $p_id   = get_the_ID();
        $c_slug = get_post_meta( $p_id, '_hs_course_slug_id', true ) ?: get_post_field( 'post_name', $p_id );
        $price  = (float) get_post_meta( $p_id, '_hs_price_bdt', true );

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
            'pricing'       => $price > 0 ? 'premium' : 'free',
            'enrolled'      => in_array( $p_id, $enrolled_ids, true ),
            'permalink'     => get_permalink( $p_id ),
        );
        $all_courses[] = $course_info;

        if ( ! $active_course_post && ( $c_slug === $requested_course_id || (string) $p_id === $requested_course_id ) ) {
            $active_course_post = get_post( $p_id );
        }
    }
    wp_reset_postdata();
}

$package_active_course = null;
if ( empty( $all_courses ) ) {
    $pkg_path = defined( 'HS_THEME_DIR' )
        ? HS_THEME_DIR . '/assets/data/full-courses-content-package.json'
        : get_template_directory() . '/assets/data/full-courses-content-package.json';

    if ( file_exists( $pkg_path ) ) {
        $json_raw  = file_get_contents( $pkg_path );
        $json_data = ! empty( $json_raw ) ? json_decode( $json_raw, true ) : array();
        if ( is_array( $json_data ) ) {
            foreach ( $json_data as $c_item ) {
                $c_slug = $c_item['courseId'] ?? ( $c_item['slug'] ?? 'course' );
                $price  = (float) ( $c_item['priceBdt'] ?? 0 );
                $course_info = array(
                    'post_id'       => 0,
                    'slug'          => $c_slug,
                    'title'         => $c_item['titleEn'] ?? ( $c_item['title'] ?? ( $c_item['titleBn'] ?? 'Course' ) ),
                    'title_en'      => $c_item['titleEn'] ?? '',
                    'category_id'   => $c_item['categoryId'] ?? 'cybersecurity',
                    'level'         => (int) ( $c_item['level'] ?? 1 ),
                    'duration'      => (int) ( $c_item['durationWeeks'] ?? 4 ),
                    'total_lessons' => (int) ( $c_item['totalLessonCount'] ?? 12 ),
                    'total_labs'    => (int) ( $c_item['totalLabCount'] ?? 2 ),
                    'pricing'       => $price > 0 ? 'premium' : 'free',
                    'enrolled'      => false,
                    'permalink'     => home_url( '/academy/?course_id=' . urlencode( $c_slug ) ),
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

if ( ! $active_course_post && ! empty( $all_courses ) && empty( $package_active_course ) ) {
    $active_course_post = get_post( $all_courses[0]['post_id'] );
}

if ( $active_course_post ) {
    $active_course_id    = (int) $active_course_post->ID;
    $active_course_title = $active_course_post->post_title;
    $active_course_slug  = get_post_meta( $active_course_id, '_hs_course_slug_id', true ) ?: $active_course_post->post_name;
    $modules_json        = get_post_meta( $active_course_id, '_hs_modules_json', true );
    $active_modules      = ! empty( $modules_json ) ? json_decode( (string) $modules_json, true ) : array();
    $quizzes_json        = get_post_meta( $active_course_id, '_hs_quizzes_json', true );
    $active_quizzes      = ! empty( $quizzes_json ) ? json_decode( (string) $quizzes_json, true ) : array();
} elseif ( $package_active_course ) {
    $active_course_id    = 0;
    $active_course_title = $package_active_course['titleEn'] ?? ( $package_active_course['titleBn'] ?? 'Cybersecurity Foundations' );
    $active_course_slug  = $package_active_course['courseId'] ?? ( $package_active_course['slug'] ?? 'cyber-01-foundations' );
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
    'id'                => 'l1',
    'titleEn'           => '1.1 System Architecture & Hands-on Lab',
    'title'             => '1.1 System Architecture & Hands-on Lab',
    'type'              => 'Video Lecture',
    'contentMarkdownEn' => 'Review the structured security walkthrough, explore the SVG flow diagrams, and run defensive verification checks in the sandbox terminal.',
);

// Load persisted progress, notes, and bookmark status
$completed_lessons = array();
$overall_percent   = 0;
if ( $is_logged_in && $active_course_id > 0 ) {
    $prog_table = $wpdb->prefix . 'hs_courses_progress';
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $prog_table ) ) === $prog_table ) {
        $prog_row = $wpdb->get_row( $wpdb->prepare( "SELECT completed_lessons, overall_percent FROM {$prog_table} WHERE user_id = %d AND course_id = %d LIMIT 1", $current_user_id, $active_course_id ), ARRAY_A );
        if ( $prog_row ) {
            $decoded = json_decode( (string) $prog_row['completed_lessons'], true );
            if ( is_array( $decoded ) ) {
                $completed_lessons = $decoded;
            }
            $overall_percent = (int) $prog_row['overall_percent'];
        }
    }
}

$saved_notes_map = $is_logged_in ? get_user_meta( $current_user_id, '_hs_academy_notes', true ) : array();
$saved_note_text = ( is_array( $saved_notes_map ) && isset( $saved_notes_map[ sanitize_key( $active_course_slug ) ] ) ) ? (string) $saved_notes_map[ sanitize_key( $active_course_slug ) ] : '';

$user_bookmarks   = $is_logged_in ? get_user_meta( $current_user_id, '_hs_user_bookmarks', true ) : array();
$lesson_bkm_id    = 'course_' . sanitize_key( $active_course_slug );
$is_bookmarked    = is_array( $user_bookmarks ) && isset( $user_bookmarks[ $lesson_bkm_id ] );

$catalog_categories = array();
foreach ( $all_courses as $c ) {
    $ck = (string) ( $c['category_id'] ?? 'cybersecurity' );
    $catalog_categories[ $ck ] = ucwords( str_replace( array( '_', '-' ), ' ', $ck ) );
}
?>

<style>
@media (max-width: 960px) {
    .hs-player-grid {
        display: flex !important;
        flex-direction: column !important;
    }
    .hs-col-curriculum, .hs-col-stage, .hs-col-console {
        width: 100% !important;
        max-width: 100% !important;
    }
}
.hs-catalog-strip-card {
    background: #101827;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 12px 14px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    transition: border-color .2s;
}
.hs-catalog-strip-card:hover {
    border-color: rgba(32,220,197,0.45);
}
</style>

<div class="hs-academy-viewport" data-hs-theme="cyber-dark">
    <header class="hs-academy-header">
        <div class="hs-academy-header-inner">
            <div class="hs-academy-brand">
                <div class="hs-academy-badge">🛡️ ACADEMY PRO</div>
                <h1 class="hs-academy-title">HackersShikkhok Cyber Academy &amp; LMS Hub</h1>
            </div>

            <div class="hs-course-picker">
                <label for="hs-course-select" class="hs-picker-label">Active Course:</label>
                <select id="hs-course-select" onchange="HS_ACADEMY.switchCourse(this.value)">
                    <?php if ( ! empty( $all_courses ) ) :
                        $grouped_courses = array();
                        foreach ( $all_courses as $c ) {
                            $grouped_courses[ $c['category_id'] ?? 'general' ][] = $c;
                        }
                        foreach ( $grouped_courses as $cat_key => $c_group ) :
                            $cat_label = ucwords( str_replace( array( '_', '-' ), ' ', $cat_key ) );
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
                    <span class="hs-stat-value hs-tabular" id="hs-user-xp"><?php echo esc_html( number_format( (float) $wallet_data['points'] ) ); ?> XP</span>
                </div>
                <div class="hs-stat-chip">
                    <span class="hs-stat-label">WALLET</span>
                    <span class="hs-stat-value hs-tabular">BDT <?php echo esc_html( number_format( (float) $wallet_data['balance_bdt'], 2 ) ); ?></span>
                </div>
                <button type="button" class="hs-btn-verify-cert" onclick="HS_ACADEMY.openCertModal()">📜 Verify Certificate</button>
            </div>
        </div>
    </header>

    <!-- Course Directory Filter Strip -->
    <section style="max-width:1400px; margin:16px auto 0; padding:0 16px;">
        <div style="background:#101827; border:1px solid rgba(32,220,197,0.2); border-radius:16px; padding:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:12px;">
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <strong style="color:#E8EEF7; font-size:14px;">🎓 Course Catalog (<?php echo count( $all_courses ); ?>)</strong>
                    <button type="button" class="hs-mini-btn hs-cat-filter-btn hs-filter-active" data-pricing="all" onclick="HS_ACADEMY.setPricingFilter('all', this)">All</button>
                    <button type="button" class="hs-mini-btn hs-cat-filter-btn" data-pricing="free" onclick="HS_ACADEMY.setPricingFilter('free', this)">🆓 Free</button>
                    <button type="button" class="hs-mini-btn hs-cat-filter-btn" data-pricing="premium" onclick="HS_ACADEMY.setPricingFilter('premium', this)">⭐ Premium</button>
                    <?php if ( $is_logged_in ) : ?>
                        <button type="button" class="hs-mini-btn hs-cat-filter-btn" data-pricing="enrolled" onclick="HS_ACADEMY.setPricingFilter('enrolled', this)">📚 My Learning</button>
                    <?php endif; ?>
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                    <select id="hs-catalog-cat-select" onchange="HS_ACADEMY.setCategoryFilter(this.value)" style="background:#080D17; color:#E8EEF7; border:1px solid rgba(255,255,255,0.12); border-radius:8px; padding:7px 10px; font-size:12px;">
                        <option value="all">All Faculties</option>
                        <?php foreach ( $catalog_categories as $ck => $cl ) : ?>
                            <option value="<?php echo esc_attr( $ck ); ?>"><?php echo esc_html( $cl ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" id="hs-catalog-search" placeholder="🔍 Search courses..." oninput="HS_ACADEMY.filterCatalog()" style="background:#080D17; color:#E8EEF7; border:1px solid rgba(255,255,255,0.12); border-radius:8px; padding:7px 12px; font-size:12px;" />
                    <button type="button" id="hs-toggle-catalog-grid" class="hs-mini-btn" onclick="HS_ACADEMY.toggleCatalogGrid()">Browse Cards ▾</button>
                </div>
            </div>

            <div id="hs-catalog-grid-drawer" style="display:none; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); gap:12px; margin-top:12px;">
                <?php foreach ( array_slice( $all_courses, 0, 24 ) as $c ) : ?>
                    <div class="hs-catalog-strip-card" data-cat="<?php echo esc_attr( $c['category_id'] ); ?>" data-pricing="<?php echo esc_attr( $c['pricing'] ); ?>" data-enrolled="<?php echo $c['enrolled'] ? '1' : '0'; ?>" data-title="<?php echo esc_attr( strtolower( $c['title'] ) ); ?>">
                        <div style="display:flex; justify-content:space-between; font-size:10px; font-family:var(--font-mono);">
                            <span style="color:#20DCC5;"><?php echo esc_html( strtoupper( $c['category_id'] ) ); ?></span>
                            <span style="color:#EABF57;">Lv.<?php echo (int) $c['level']; ?> · <?php echo esc_html( strtoupper( $c['pricing'] ) ); ?></span>
                        </div>
                        <div style="color:#E8EEF7; font-size:13px; font-weight:700; line-height:1.35;"><?php echo esc_html( $c['title'] ); ?></div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:auto; padding-top:6px; font-size:11px; color:#929FB2;">
                            <span><?php echo (int) $c['total_lessons']; ?> Lessons</span>
                            <a href="<?php echo esc_url( home_url( '/academy/?course_id=' . urlencode( $c['slug'] ) ) ); ?>" style="color:#20DCC5; font-weight:800; text-decoration:none;">Open →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 3-Column LMS Player Workspace -->
    <main class="hs-player-grid">
        <!-- COLUMN 1: Curriculum & Modules -->
        <aside class="hs-col-curriculum" id="hs-curriculum-col">
            <div class="hs-panel-header">
                <h3>📚 Curriculum &amp; Modules</h3>
                <span class="hs-pill" id="hs-course-progress-badge"><?php echo (int) $overall_percent; ?>% Complete</span>
            </div>
            <div class="hs-curriculum-search">
                <input type="text" id="hs-lesson-search" placeholder="🔍 Search curriculum..." oninput="HS_ACADEMY.filterLessons(this.value)" />
            </div>

            <div class="hs-module-list" id="hs-module-accordion">
                <?php foreach ( $active_modules as $m_idx => $module ) :
                    $is_open = $m_idx === 0 ? 'open' : '';
                    $m_num   = esc_html( sprintf( 'MOD %02d', (int) ( $module['moduleNumber'] ?? ( $m_idx + 1 ) ) ) );
                    $m_title = esc_html( (string) ( $module['moduleTitleEn'] ?? ( $module['moduleTitleBn'] ?? 'Module' ) ) );
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
                                $les_title = (string) ( $lesson['titleEn'] ?? ( $lesson['titleBn'] ?? 'Lesson' ) );
                                $les_type  = (string) ( $lesson['type'] ?? 'lecture' );
                                $les_dur   = esc_html( (string) ( $lesson['duration'] ?? '15m' ) );
                                $is_done   = in_array( $les_id, $completed_lessons, true );
                            ?>
                            <li class="hs-lesson-item <?php echo esc_attr( $is_active ); ?>"
                                data-lesson-id="<?php echo $les_id; ?>"
                                data-lesson-title="<?php echo esc_attr( $les_title ); ?>"
                                data-lesson-type="<?php echo esc_attr( $les_type ); ?>"
                                onclick="HS_ACADEMY.loadLesson('<?php echo $les_id; ?>', '<?php echo esc_js( $les_title ); ?>', '<?php echo esc_js( $les_type ); ?>')">
                                <span class="hs-status-icon <?php echo $is_done ? 'done' : ''; ?>"><?php echo $is_done ? '✓' : '○'; ?></span>
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
                <button type="button" class="hs-btn-exam" onclick="HS_ACADEMY.openFinalExam()">🏆 Take Final Certification Exam</button>
            </div>
        </aside>

        <!-- COLUMN 2: Central Lesson Stage -->
        <section class="hs-col-stage" id="hs-stage-col">
            <div class="hs-stage-topbar">
                <div class="hs-breadcrumb">
                    Course: <strong><?php echo esc_html( $active_course_title ); ?></strong> /
                    <span id="hs-current-lesson-crumb"><?php echo esc_html( $first_lesson['titleEn'] ?? ( $first_lesson['title'] ?? 'Lesson 1.1' ) ); ?></span>
                </div>
                <div class="hs-lesson-actions">
                    <button type="button" class="hs-btn-icon" id="hs-btn-bookmark" onclick="HS_ACADEMY.toggleBookmark()">
                        <?php echo $is_bookmarked ? '★ Bookmarked' : '🔖 Bookmark'; ?>
                    </button>
                    <button type="button" class="hs-btn-complete" id="hs-btn-mark-complete" onclick="HS_ACADEMY.completeCurrentLesson()">Mark Complete ✓</button>
                </div>
            </div>

            <div class="hs-stage-media-card">
                <div class="hs-stage-tabs">
                    <button type="button" class="hs-tab-btn active" data-stage-tab="lecture" onclick="HS_ACADEMY.switchStageTab('lecture', this)">📺 Instructional Lesson</button>
                    <button type="button" class="hs-tab-btn" data-stage-tab="diagram" onclick="HS_ACADEMY.switchStageTab('diagram', this)">📐 Vector Diagrams</button>
                    <button type="button" class="hs-tab-btn" data-stage-tab="terminal" onclick="HS_ACADEMY.switchStageTab('terminal', this)">💻 Sandboxed Lab</button>
                    <button type="button" class="hs-tab-btn" data-stage-tab="quiz" onclick="HS_ACADEMY.switchStageTab('quiz', this)">📝 Practice Quiz</button>
                </div>

                <!-- Lecture Content Stage -->
                <div class="hs-stage-content active" id="hs-stage-lecture">
                    <div class="hs-lesson-body" id="hs-lesson-body-text">
                        <div class="hs-video-container" style="position:relative;background:#080D17;border-radius:12px;overflow:hidden;border:1px solid rgba(32,220,197,0.3);">
                            <iframe
                                id="hs-video-frame"
                                src="https://www.youtube-nocookie.com/embed?listType=search&list=HackersShikkhok"
                                title="HackersShikkhok Official Academy Player"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                style="width:100%;aspect-ratio:16/9;border:none;">
                            </iframe>
                        </div>
                        <div class="hs-lesson-notes-content" id="hs-dynamic-lesson-content" style="margin-top:20px;line-height:1.7;">
                            <?php
                            $raw_lesson_text = (string) ( $first_lesson['contentMarkdownEn'] ?? ( $first_lesson['contentMarkdownBn'] ?? '' ) );
                            $lesson_title    = (string) ( $first_lesson['titleEn'] ?? ( $first_lesson['title'] ?? 'Lesson 1.1 Instruction' ) );
                            $cleaned         = trim( $raw_lesson_text );
                            ?>
                            <div class="hs-lesson-briefing-card" style="background:#101827;border:1px solid rgba(32,220,197,0.25);border-radius:14px;padding:24px;">
                                <div style="display:inline-block;padding:2px 10px;background:rgba(32,220,197,0.12);border:1px solid #20DCC5;color:#20DCC5;font-size:11px;font-weight:800;border-radius:6px;margin-bottom:12px;">
                                    ⚡ DEFENSIVE CORE CURRICULUM
                                </div>
                                <h3 style="color:#E8EEF7;font-size:1.3rem;font-weight:800;margin-bottom:10px;"><?php echo esc_html( $lesson_title ); ?></h3>
                                <div style="color:#929FB2;font-size:14px;"><?php echo nl2br( esc_html( $cleaned ) ); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vector Diagrams Stage -->
                <div class="hs-stage-content" id="hs-stage-diagram" style="display:none;">
                    <div class="hs-diagram-picker" style="margin-bottom:15px;display:flex;gap:8px;flex-wrap:wrap;">
                        <button type="button" class="hs-btn-small active" onclick="HS_ACADEMY.switchDiagram('sqli_prevention', this)">🛡️ SQLi Prepared Statement</button>
                        <button type="button" class="hs-btn-small" onclick="HS_ACADEMY.switchDiagram('jwt_auth_flow', this)">🔐 JWT Signature Flow</button>
                        <button type="button" class="hs-btn-small" onclick="HS_ACADEMY.switchDiagram('network_dmz_defense', this)">🌐 DMZ Network Defense</button>
                        <button type="button" class="hs-btn-small" onclick="HS_ACADEMY.switchDiagram('xss_defense', this)">⚡ XSS Output Encoding</button>
                    </div>
                    <div id="hs-dynamic-diagram-wrapper">
                        <?php echo class_exists( '\HackersShikkhok\Theme\Modules\LMS\SVG_Annotator' )
                            ? \HackersShikkhok\Theme\Modules\LMS\SVG_Annotator::render_diagram( 'sqli_prevention' )
                            : '<p style="color:#E8EEF7;">Diagram module active.</p>'; ?>
                    </div>
                </div>

                <!-- Server-Validated Sandboxed Terminal Stage -->
                <div class="hs-stage-content" id="hs-stage-terminal" style="display:none;">
                    <div class="hs-terminal-window">
                        <div class="hs-term-header">
                            <span class="hs-term-dot red"></span>
                            <span class="hs-term-dot yellow"></span>
                            <span class="hs-term-dot green"></span>
                            <span class="hs-term-title">hs-lab-session@hackersshikkhok:~ (Server-Validated Sandbox)</span>
                        </div>
                        <div class="hs-term-body" id="hs-term-output">
                            <p class="hs-term-line">HackersShikkhok Defensive Security Interactive Sandbox.</p>
                            <p class="hs-term-line">Active Course: [<?php echo esc_html( $active_course_slug ); ?>] | Type 'help', 'status', 'scan', 'ls', 'hint', or 'clear'.</p>
                            <div class="hs-term-prompt-line">
                                <span class="hs-term-prompt">scholar@hs-academy:~$</span>
                                <input type="text" id="hs-term-input" onkeydown="HS_ACADEMY.handleTerminalKey(event)" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Quiz Assessment Stage -->
                <div class="hs-stage-content" id="hs-stage-quiz" style="display:none;">
                    <div class="hs-quiz-card" id="hs-quiz-container">
                        <h3>Module Assessment Quiz</h3>
                        <p>Complete this technical assessment. Answers are graded server-side.</p>
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
                        <div id="hs-quiz-result-box" style="display:none; margin-top:14px; padding:12px; border-radius:10px;"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- COLUMN 3: Scholar Console & Cloud Notes -->
        <aside class="hs-col-console" id="hs-console-col">
            <div class="hs-panel-header">
                <h3>⚡ Scholar Console</h3>
            </div>

            <div class="hs-console-widget">
                <div class="hs-widget-title">🎙️ Audio Lecture (Browser TTS)</div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <select id="hs-tts-voice-select" style="background:#080D17;color:#20DCC5;border:1px solid rgba(32,220,197,0.3);padding:6px 8px;border-radius:6px;font-size:11px;">
                        <option value="female">👩 Female Voice</option>
                        <option value="male">👨 Male Voice</option>
                    </select>
                    <button type="button" id="hs-tts-play-btn" onclick="HS_ACADEMY.speakCurrentLesson()" style="background:rgba(32,220,197,0.15);border:1px solid #20DCC5;color:#20DCC5;padding:6px 10px;border-radius:6px;font-size:11px;cursor:pointer;">
                        🔊 Listen
                    </button>
                </div>
            </div>

            <div class="hs-console-widget hs-ctf-box" style="border:1px solid rgba(244,63,94,0.45);background:rgba(244,63,94,0.06);padding:12px;border-radius:10px;margin-bottom:12px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <strong style="color:#fb7185;font-size:12px;">🚩 ACTIVE CTF LAB</strong>
                    <span style="background:#f43f5e;color:#fff;font-size:10px;padding:2px 6px;border-radius:4px;font-weight:bold;">+150 XP &amp; BDT 10</span>
                </div>
                <p style="font-size:11px;color:#E8EEF7;margin:0 0 8px 0;line-height:1.4;">
                    Open the <strong>Sandboxed Lab</strong> tab and run <code>exploit sqli</code> or <code>cat flag.txt</code>, then execute <code>submit-flag &lt;TOKEN&gt;</code>.
                </p>
            </div>

            <div class="hs-console-widget">
                <div class="hs-widget-title">📝 My Cloud Notes</div>
                <textarea id="hs-notes-area" placeholder="Write study notes here..." oninput="HS_ACADEMY.saveNotesLocal(this.value)"><?php echo esc_textarea( $saved_note_text ); ?></textarea>
                <div class="hs-notes-footer">
                    <span id="hs-notes-save-status"><?php echo $saved_note_text !== '' ? 'Synced ✓' : 'Ready'; ?></span>
                    <button type="button" class="hs-btn-small" onclick="HS_ACADEMY.syncNotesToServer()">☁️ Sync Cloud</button>
                </div>
            </div>

            <div class="hs-console-widget">
                <div class="hs-widget-title">📁 Lab Resources</div>
                <ul class="hs-resource-list">
                    <li><a href="data:text/plain;charset=utf-8,Hackers%20Shikkhok%20Zero-Trust%20Checklist%0A1.%20Object-Level%20Auth%0A2.%20Constant-time%20HMAC%20Verification%0A3.%20Parameterized%20Queries" download="zero-trust-checklist.txt">📄 Zero-Trust Checklist.txt</a></li>
                </ul>
            </div>
        </aside>
    </main>

    <!-- Public Certificate Verification Modal -->
    <div class="hs-modal-backdrop" id="hs-cert-modal" style="display:none;">
        <div class="hs-modal-content">
            <div class="hs-modal-header">
                <h3>📜 Certificate Cryptographic Verifier</h3>
                <button type="button" class="hs-modal-close" onclick="HS_ACADEMY.closeCertModal()">✕</button>
            </div>
            <div class="hs-modal-body">
                <p>Enter any HackersShikkhok Certificate Code to verify its HMAC-SHA256 authenticity against our ledger.</p>
                <div class="hs-form-row">
                    <input type="text" id="hs-cert-verify-input" placeholder="e.g. HS-CERT-2026-X9A7B2C" />
                    <button type="button" class="hs-btn-primary" onclick="HS_ACADEMY.verifyCertificateCode()">Verify</button>
                </div>
                <div id="hs-cert-result" class="hs-cert-result-box" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>

<script>
window.HS_ACADEMY = (function() {
    const nonce      = '<?php echo esc_js( $rest_nonce ); ?>';
    const restBase   = '<?php echo esc_js( $rest_base ); ?>';
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    const loginUrl   = '<?php echo esc_js( $login_url ); ?>';
    let currentLesson = '<?php echo esc_js( $first_lesson['id'] ?? 'l1' ); ?>';
    let courseId      = <?php echo (int) $active_course_id; ?>;
    let courseSlug    = '<?php echo esc_js( $active_course_slug ); ?>';
    let courseTitle   = '<?php echo esc_js( $active_course_title ); ?>';
    let pricingFilter = 'all';

    function toast(msg, isErr) {
        let el = document.getElementById('hs-acad-toast');
        if (!el) {
            el = document.createElement('div');
            el.id = 'hs-acad-toast';
            el.style.cssText = 'position:fixed;bottom:80px;right:20px;z-index:10000;padding:12px 18px;border-radius:10px;font-size:13px;font-weight:700;box-shadow:0 10px 30px rgba(0,0,0,0.5);';
            document.body.appendChild(el);
        }
        el.style.background = isErr ? '#4c0519' : '#064e3b';
        el.style.border = isErr ? '1px solid #f43f5e' : '1px solid #20DCC5';
        el.style.color = '#E8EEF7';
        el.textContent = msg;
        el.style.display = 'block';
        clearTimeout(el._t);
        el._t = setTimeout(() => { el.style.display = 'none'; }, 3500);
    }

    function escapeHtml(s) {
        return String(s || '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    // Restore localStorage draft if server field was empty
    document.addEventListener('DOMContentLoaded', function() {
        const area = document.getElementById('hs-notes-area');
        if (area && !area.value.trim()) {
            const draft = localStorage.getItem('hs_notes_' + courseSlug);
            if (draft) area.value = draft;
        }
    });

    return {
        switchCourse: function(newSlug) {
            window.location.href = window.location.pathname + '?course_id=' + encodeURIComponent(newSlug);
        },
        toggleCatalogGrid: function() {
            const d = document.getElementById('hs-catalog-grid-drawer');
            if (!d) return;
            d.style.display = (d.style.display === 'none' || !d.style.display) ? 'grid' : 'none';
        },
        setPricingFilter: function(val, btn) {
            pricingFilter = val;
            document.querySelectorAll('.hs-cat-filter-btn').forEach(b => b.classList.remove('hs-filter-active'));
            if (btn) btn.classList.add('hs-filter-active');
            const d = document.getElementById('hs-catalog-grid-drawer');
            if (d && d.style.display === 'none') d.style.display = 'grid';
            this.filterCatalog();
        },
        setCategoryFilter: function() {
            const d = document.getElementById('hs-catalog-grid-drawer');
            if (d && d.style.display === 'none') d.style.display = 'grid';
            this.filterCatalog();
        },
        filterCatalog: function() {
            const cat = (document.getElementById('hs-catalog-cat-select')?.value || 'all').toLowerCase();
            const q   = (document.getElementById('hs-catalog-search')?.value || '').trim().toLowerCase();
            document.querySelectorAll('.hs-catalog-strip-card').forEach(card => {
                const cCat = (card.getAttribute('data-cat') || '').toLowerCase();
                const cPrc = card.getAttribute('data-pricing') || 'free';
                const cEnr = card.getAttribute('data-enrolled') === '1';
                const cTit = card.getAttribute('data-title') || '';
                let ok = true;
                if (pricingFilter === 'free' && cPrc !== 'free') ok = false;
                if (pricingFilter === 'premium' && cPrc !== 'premium') ok = false;
                if (pricingFilter === 'enrolled' && !cEnr) ok = false;
                if (cat !== 'all' && cCat !== cat) ok = false;
                if (q && !cTit.includes(q) && !cCat.includes(q)) ok = false;
                card.style.display = ok ? 'flex' : 'none';
            });
        },
        loadLesson: function(id, title, type) {
            currentLesson = id;
            const crumb = document.getElementById('hs-current-lesson-crumb');
            if (crumb) crumb.textContent = title;
            document.querySelectorAll('.hs-lesson-item').forEach(el => el.classList.remove('active'));
            const target = document.querySelector(`[data-lesson-id="${id}"]`);
            if (target) target.classList.add('active');

            const dynContent = document.getElementById('hs-dynamic-lesson-content');
            if (dynContent) {
                dynContent.innerHTML = `
                    <div class="hs-lesson-briefing-card" style="background:#101827;border:1px solid rgba(32,220,197,0.25);border-radius:14px;padding:24px;">
                        <div style="display:inline-block;padding:2px 10px;background:rgba(32,220,197,0.12);border:1px solid #20DCC5;color:#20DCC5;font-size:11px;font-weight:800;border-radius:6px;margin-bottom:12px;">⚡ ACTIVE LESSON</div>
                        <h3 style="color:#E8EEF7;font-size:1.3rem;font-weight:800;margin-bottom:10px;">${escapeHtml(title)}</h3>
                        <p style="color:#929FB2;font-size:14px;margin-bottom:14px;">Inspect the flow diagrams, execute sandbox lab verification commands, and submit your completion progress.</p>
                    </div>`;
            }

            const lowType = String(type || '').toLowerCase();
            if (lowType.includes('lab') || lowType.includes('terminal')) {
                this.switchStageTab('terminal', document.querySelector('.hs-tab-btn[data-stage-tab="terminal"]'));
            } else if (lowType.includes('quiz') || lowType.includes('exam')) {
                this.switchStageTab('quiz', document.querySelector('.hs-tab-btn[data-stage-tab="quiz"]'));
            } else {
                this.switchStageTab('lecture', document.querySelector('.hs-tab-btn[data-stage-tab="lecture"]'));
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
            wrapper.innerHTML = '<div style="color:#20DCC5;padding:20px;text-align:center;">⏳ Rendering vector diagram...</div>';
            fetch(restBase + '/lms/diagram/' + encodeURIComponent(diagKey))
                .then(r => r.json())
                .then(data => {
                    if (data && data.html) wrapper.innerHTML = data.html;
                })
                .catch(() => {
                    wrapper.innerHTML = '<div style="color:#20DCC5;padding:20px;">Diagram: ' + escapeHtml(diagKey) + '</div>';
                });
        },
        completeCurrentLesson: function() {
            if (!isLoggedIn) { window.location.href = loginUrl; return; }
            fetch(restBase + '/academy/progress', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ course_id: courseId || 1, lesson_id: currentLesson })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('hs-course-progress-badge');
                    if (badge) badge.innerText = data.overall_percent + '% Complete';
                    const activeEl = document.querySelector(`[data-lesson-id="${currentLesson}"] .hs-status-icon`);
                    if (activeEl) { activeEl.innerText = '✓'; activeEl.classList.add('done'); }
                    toast('✓ Progress saved! +15 XP awarded.');
                } else {
                    toast(data.error || 'Could not record progress.', true);
                }
            })
            .catch(() => toast('Network error saving progress.', true));
        },
        openFinalExam: function() {
            this.switchStageTab('quiz', document.querySelector('.hs-tab-btn[data-stage-tab="quiz"]'));
        },
        submitQuizForm: function(e) {
            e.preventDefault();
            if (!isLoggedIn) { window.location.href = loginUrl; return; }
            const form = document.getElementById('hs-quiz-form');
            const q1Val = form.querySelector('input[name="q1"]:checked')?.value || -1;
            const q2Val = form.querySelector('input[name="q2"]:checked')?.value || -1;
            fetch(restBase + '/academy/quiz/attempt', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({
                    course_id: courseId || 1,
                    quiz_id: 'quiz_mod1',
                    answers: [
                        { question_id: 'q1', selected_option: parseInt(q1Val, 10) },
                        { question_id: 'q2', selected_option: parseInt(q2Val, 10) }
                    ]
                })
            })
            .then(res => res.json())
            .then(data => {
                const box = document.getElementById('hs-quiz-result-box');
                if (box) {
                    box.style.display = 'block';
                    box.style.background = data.passed ? 'rgba(32,220,197,0.12)' : 'rgba(244,63,94,0.12)';
                    box.style.border = data.passed ? '1px solid #20DCC5' : '1px solid #f43f5e';
                    box.innerHTML = data.success
                        ? `<strong>Score: ${data.score_percent}%</strong> · ${data.passed ? 'PASSED ✓' : 'Try Again'} (+${data.xp_awarded} XP)`
                        : escapeHtml(data.error || 'Evaluation failed');
                }
                toast(data.passed ? `✓ Quiz Passed! +${data.xp_awarded} XP` : 'Quiz evaluated.');
            })
            .catch(() => toast('Network error grading quiz.', true));
        },
        handleTerminalKey: function(e) {
            if (e.key !== 'Enter') return;
            const input = e.target;
            const cmd   = input.value.trim();
            input.value = '';
            const out = document.getElementById('hs-term-output');
            if (!cmd) return;
            if (cmd === 'clear') {
                out.innerHTML = '<div class="hs-term-prompt-line"><span class="hs-term-prompt">scholar@hs-academy:~$</span><input type="text" id="hs-term-input" onkeydown="HS_ACADEMY.handleTerminalKey(event)" /></div>';
                document.getElementById('hs-term-input')?.focus();
                return;
            }
            const p = document.createElement('p');
            p.className = 'hs-term-line';
            p.innerHTML = `<span style="color:#20DCC5;">$ ${escapeHtml(cmd)}</span><br />Running in sandbox...`;
            out.insertBefore(p, out.lastElementChild);

            fetch(restBase + '/terminal/exec', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ command: cmd, lesson_id: 1 })
            })
            .then(r => r.json())
            .then(d => {
                p.innerHTML = `<span style="color:#20DCC5;">$ ${escapeHtml(cmd)}</span><br /><pre style="white-space:pre-wrap;margin:4px 0 0;font-family:inherit;">${escapeHtml(d.output || '')}</pre>`;
                out.scrollTop = out.scrollHeight;
            })
            .catch(() => {
                p.innerHTML = `<span style="color:#20DCC5;">$ ${escapeHtml(cmd)}</span><br />Sandbox server unreachable.`;
            });
        },
        speakCurrentLesson: function() {
            if (!('speechSynthesis' in window)) {
                toast('Browser speech synthesis unavailable.', true);
                return;
            }
            window.speechSynthesis.cancel();
            const txt = document.getElementById('hs-dynamic-lesson-content')?.innerText || courseTitle;
            const utt = new SpeechSynthesisUtterance(txt.slice(0, 1200));
            window.speechSynthesis.speak(utt);
            toast('🔊 Reading lesson aloud...');
        },
        saveNotesLocal: function(val) {
            localStorage.setItem('hs_notes_' + courseSlug, val);
            const status = document.getElementById('hs-notes-save-status');
            if (status) status.innerText = 'Draft (unsynced)';
        },
        syncNotesToServer: function() {
            if (!isLoggedIn) { window.location.href = loginUrl; return; }
            const notes  = document.getElementById('hs-notes-area')?.value || '';
            const status = document.getElementById('hs-notes-save-status');
            if (status) status.innerText = 'Syncing...';
            fetch(restBase + '/academy/notes/sync', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ course_slug: courseSlug, notes: notes })
            })
            .then(r => r.json())
            .then(d => {
                if (status) status.innerText = d.success ? 'Synced ✓' : 'Sync failed';
                toast(d.message || 'Notes synchronized!', !d.success);
            })
            .catch(() => {
                if (status) status.innerText = 'Error';
                toast('Could not sync notes to server.', true);
            });
        },
        toggleBookmark: function() {
            if (!isLoggedIn) { window.location.href = loginUrl; return; }
            const btn = document.getElementById('hs-btn-bookmark');
            fetch(restBase + '/library/bookmarks/toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({
                    item_id: 'course_' + courseSlug,
                    item_type: 'course',
                    title: courseTitle,
                    url: window.location.href
                })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    if (btn) btn.textContent = d.is_bookmarked ? '★ Bookmarked' : '🔖 Bookmark';
                    toast(d.is_bookmarked ? '✓ Course bookmarked to My Library!' : 'Removed from Bookmarks.');
                }
            })
            .catch(() => toast('Bookmark request failed.', true));
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
                body: JSON.stringify({ cert_id: code, cert_code: code })
            })
            .then(res => res.json())
            .then(data => {
                if (data.valid) {
                    const rec = escapeHtml(data.recipient || data.recipient_display_name || '');
                    const crs = escapeHtml(data.course_title || data.course_title_snapshot || '');
                    const iss = escapeHtml(data.issued_at || '');
                    resBox.innerHTML = `<div style="color:#20DCC5;font-weight:bold;">✅ CERTIFICATE AUTHENTIC &amp; VALID</div>
                    <div>Recipient: ${rec}</div>
                    <div>Course: ${crs}</div>
                    <div>Issued: ${iss}</div>
                    <div>HMAC-SHA256 Signature: VERIFIED</div>
                    <div style="margin-top:8px;"><a href="<?php echo esc_url( home_url( '/verify-certificate/?cert_id=' ) ); ?>${encodeURIComponent(code)}" style="color:#20DCC5;font-weight:700;">Open Official Verification Page →</a></div>`;
                } else {
                    resBox.innerHTML = `<div style="color:#f43f5e;font-weight:bold;">❌ INVALID OR REVOKED CERTIFICATE</div>
                    <div>Status: ${escapeHtml(data.message || data.status || 'Not Found')}</div>`;
                }
            })
            .catch(() => {
                resBox.innerHTML = '<div style="color:#f43f5e;">Verification server connection failed.</div>';
            });
        }
    };
})();
</script>

<?php get_footer(); ?>

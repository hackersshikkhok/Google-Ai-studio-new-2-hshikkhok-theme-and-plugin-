<?php
/**
 * Master Front Page Template - HackersShikkhok (v5.0.4)
 * Brand: HackersShikkhok (https://hackersshikkhok.com | YouTube: @HackersShikkhok)
 *
 * Architecture: All-in-One Technology Content, Community, Tools & Learning Ecosystem
 * 4 Equal Pillars:
 * 1. Content Sharing Platform (Posts, Tutorials, Reviews, Code)
 * 2. Community & Q&A Platform (Discussions, Q&A, Solved Questions, Leaderboard)
 * 3. 2,000+ Technology Tools Directory (Web Dev, Security, Hardware, Calculators)
 * 4. Cyber Academy LMS (Courses, Lessons, CTF Challenges, Certificates)
 */

declare(strict_types=1);

get_header();

// Settings & Config
$feat_cat       = (string) hs_get_setting( 'featured_category', 'notice' );
$w_likes        = (float) hs_get_setting( 'popular_weight_likes', 1.0 );
$w_comms        = (float) hs_get_setting( 'popular_weight_comments', 1.5 );
$w_views        = (float) hs_get_setting( 'popular_weight_views', 0.5 );
$pop_cnt        = max( 1, (int) hs_get_setting( 'popular_posts_count', 6 ) );
$lat_cnt        = max( 1, (int) hs_get_setting( 'latest_posts_count', 8 ) );
$trend_cnt      = max( 1, (int) hs_get_setting( 'trending_posts_count', 6 ) );
$qa_cnt         = max( 1, (int) hs_get_setting( 'qa_showcase_count', 4 ) );
$courses_cnt    = max( 1, (int) hs_get_setting( 'academy_courses_count', 4 ) );
$tutorials_cnt  = max( 1, (int) hs_get_setting( 'tutorials_count', 4 ) );
$voice_active   = (bool) hs_get_setting( 'enable_ai_voice_reader', true );

// Section Switches
$show_shortcuts = (bool) hs_get_setting( 'home_show_hero_shortcuts', true );
$show_trending  = (bool) hs_get_setting( 'home_show_trending_slider', true );
$show_notices   = (bool) hs_get_setting( 'home_show_featured_notices', true );
$show_cats      = (bool) hs_get_setting( 'home_show_categories', true );
$show_latest    = (bool) hs_get_setting( 'home_show_latest_feed', true );
$show_popular   = (bool) hs_get_setting( 'home_show_popular_posts', true );
$show_qa        = (bool) hs_get_setting( 'home_show_qa_showcase', true );
$show_tools     = (bool) hs_get_setting( 'home_show_featured_tools', true );
$show_gateway   = (bool) hs_get_setting( 'home_show_tools_gateway', true );
$show_courses   = (bool) hs_get_setting( 'home_show_academy_courses', true );
$show_tutorials = (bool) hs_get_setting( 'home_show_tutorials', true );
$show_video     = (bool) hs_get_setting( 'home_show_video_hub', true );
$show_leader    = (bool) hs_get_setting( 'home_show_community_leaderboard', true );

$curated_raw    = (string) hs_get_setting( 'home_curated_post_ids', '' );
$curated_ids    = array_values( array_filter( array_map( 'intval', explode( ',', $curated_raw ) ), static fn( int $id ): bool => $id > 0 ) );
$default_order  = array(
    'hero_shortcuts', 'trending_slider', 'featured_notices', 'categories',
    'latest_feed', 'popular_posts', 'qa_showcase', 'featured_tools',
    'tools_gateway', 'academy_courses', 'tutorials', 'video_hub', 'community_leaderboard',
);
$section_order  = hs_get_setting( 'home_section_order', $default_order );
if ( ! is_array( $section_order ) || empty( $section_order ) ) {
    $section_order = $default_order;
}

// 1. Featured Notices Query (Curated IDs first, then Category Cached)
$featured_ids = hs_cache_remember(
    'front_featured_ids_' . $feat_cat . '_' . md5( $curated_raw ),
    static function () use ( $feat_cat, $curated_ids ): array {
        $ids = $curated_ids;
        if ( count( $ids ) < 4 ) {
            $cat_ids = get_posts( array(
                'category_name'          => $feat_cat,
                'posts_per_page'         => 4,
                'post_status'            => 'publish',
                'fields'                 => 'ids',
                'post__not_in'           => $ids,
                'no_found_rows'          => true,
                'update_post_term_cache' => false,
            ) );
            $ids = array_merge( $ids, array_map( 'intval', $cat_ids ) );
        }
        if ( empty( $ids ) ) {
            $ids = get_posts( array(
                'posts_per_page'         => 4,
                'post_status'            => 'publish',
                'fields'                 => 'ids',
                'no_found_rows'          => true,
                'update_post_term_cache' => false,
            ) );
        }
        return array_slice( array_values( array_unique( array_map( 'intval', $ids ) ) ), 0, 4 );
    }
);
$featured_query = new \WP_Query( array(
    'post__in'            => ! empty( $featured_ids ) ? $featured_ids : array( 0 ),
    'orderby'             => 'post__in',
    'posts_per_page'      => 4,
    'post_status'         => 'publish',
    'no_found_rows'       => true,
    'ignore_sticky_posts' => true,
) );

// 2. Trending / Popular Posts Query (Cached by Engagement)
$popular_ids = hs_cache_remember(
    'front_popular_ids_' . $pop_cnt,
    static function () use ( $w_likes, $w_comms, $w_views, $pop_cnt ): array {
        $candidate_ids = get_posts( array(
            'posts_per_page'         => 35,
            'post_status'            => 'publish',
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'update_post_term_cache' => false,
        ) );
        if ( empty( $candidate_ids ) ) {
            return array();
        }
        update_meta_cache( 'post', $candidate_ids );
        $scores = array();
        foreach ( $candidate_ids as $pid ) {
            $pid = (int) $pid;
            $scores[ $pid ] = ( (int) get_post_meta( $pid, 'hs_likes_count', true ) * $w_likes )
                + ( (int) get_comments_number( $pid ) * $w_comms )
                + ( (int) get_post_meta( $pid, 'hs_views_count', true ) * $w_views );
        }
        arsort( $scores );
        return array_slice( array_keys( $scores ), 0, $pop_cnt );
    }
);
$popular_posts = ! empty( $popular_ids )
    ? get_posts( array(
        'post__in'       => $popular_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => $pop_cnt,
        'post_status'    => 'publish',
        'no_found_rows'  => true,
    ) )
    : array();

// 3. Trending Slider Posts (High Velocity)
$trending_posts = ! empty( $popular_ids )
    ? array_slice( $popular_posts, 0, $trend_cnt )
    : array();

// 4. Latest Posts Query (Cached)
$latest_ids = hs_cache_remember(
    'front_latest_ids_' . $lat_cnt,
    static function () use ( $lat_cnt ): array {
        $ids = get_posts( array(
            'posts_per_page'         => $lat_cnt,
            'orderby'                => 'date',
            'order'                  => 'DESC',
            'post_status'            => 'publish',
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'update_post_term_cache' => false,
        ) );
        return array_map( 'intval', $ids );
    }
);
$latest_query = new \WP_Query( array(
    'post__in'            => ! empty( $latest_ids ) ? $latest_ids : array( 0 ),
    'orderby'             => 'post__in',
    'posts_per_page'      => $lat_cnt,
    'post_status'         => 'publish',
    'no_found_rows'       => true,
    'ignore_sticky_posts' => true,
) );

// 5. Community Q&A Questions Query
$qa_questions = array();
if ( $show_qa ) {
    $qa_query = new \WP_Query( array(
        'post_type'      => 'hs_question',
        'posts_per_page' => $qa_cnt,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ) );
    $qa_questions = $qa_query->posts;
}

// 6. Featured Tools
$featured_tools = array();
if ( $show_tools && class_exists( '\HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry' ) ) {
    $featured_tools = hs_cache_remember(
        'front_featured_tools_8',
        static fn(): array => array_slice( \HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry::get_all_tools(), 0, 8 ),
        3600
    );
}

// 7. Academy Courses
$academy_courses = array();
if ( $show_courses && class_exists( '\HackersShikkhok\Theme\Modules\LMS\Course_Manager' ) ) {
    $academy_courses = hs_cache_remember(
        'front_academy_courses_' . $courses_cnt,
        static fn(): array => array_slice( \HackersShikkhok\Theme\Modules\LMS\Course_Manager::get_courses(), 0, $courses_cnt ),
        3600
    );
}

// 8. Latest Tutorials & Guides Query
$tutorials_query = null;
if ( $show_tutorials ) {
    $tutorials_query = new \WP_Query( array(
        'post_type'      => array( 'tutorials', 'post' ),
        'posts_per_page' => $tutorials_cnt,
        'post_status'    => 'publish',
        'no_found_rows'  => true,
    ) );
}

$current_user_id = get_current_user_id();
$saved_voice     = $current_user_id > 0 ? ( get_user_meta( $current_user_id, 'hs_ai_voice', true ) ?: 'Ananya' ) : 'Ananya';
?>

<main id="primary" class="site-main hs-front-page">

    <!-- 02. HERO SECTION: 4 Core Pillars Architecture & Global Search -->
    <section class="hs-hero-section">
        <div class="hs-hero-bg-grid" aria-hidden="true"></div>
        <div class="hs-hero-orb hs-hero-orb--cyan" aria-hidden="true"></div>
        <div class="hs-hero-orb hs-hero-orb--violet" aria-hidden="true"></div>

        <div class="hs-hero-inner">
            <div class="hs-hero-top-row">
                <div class="hs-hero-badge">
                    <span class="hs-live-dot" aria-hidden="true"></span>
                    <span>ALL-IN-ONE TECHNOLOGY PLATFORM</span>
                </div>

                <?php if ( $voice_active ) : ?>
                <button
                    type="button"
                    id="hs-fullpage-speak-btn"
                    class="hs-hero-speaker-btn"
                    data-voice="<?php echo esc_attr( (string) $saved_voice ); ?>"
                    title="Listen to Page"
                    aria-label="Listen to Page"
                    aria-pressed="false"
                >
                    <svg class="hs-hero-speaker-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                        <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
                    </svg>
                    <span class="hs-soundwave" id="hs-hero-soundwave" aria-hidden="true">
                        <span class="hs-bar-w"></span>
                        <span class="hs-bar-w"></span>
                        <span class="hs-bar-w"></span>
                    </span>
                </button>
                <?php endif; ?>
            </div>

            <h1 class="hs-hero-title">
                Explore. Learn. Share. Connect.
            </h1>

            <p class="hs-hero-sub">
                One platform, four powerful experiences: Content Sharing, Technical Community Q&amp;A, 2,024+ In-Browser Tools, and Cyber Defense Academy.
            </p>

            <!-- 4 Core Pillar Shortcut Gateway Cards -->
            <?php if ( $show_shortcuts ) : ?>
            <div class="hs-hero-pillars-grid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 240px), 1fr)); gap:16px; margin:32px 0 24px; text-align:left;">
                <!-- 1. Content Pillar -->
                <a href="#hs-content-feed-sec" class="hs-pillar-card" style="background:rgba(11,17,32,0.85); border:1px solid rgba(0,245,212,0.3); border-radius:16px; padding:20px; text-decoration:none; display:flex; flex-direction:column; justify-content:space-between; transition:transform 0.2s, border-color 0.2s; box-shadow:0 8px 24px rgba(0,0,0,0.4);">
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <span style="font-size:26px;">📝</span>
                            <span style="font-size:10px; font-weight:800; color:#00f5d4; background:rgba(0,245,212,0.12); padding:2px 8px; border-radius:999px;">CONTENT</span>
                        </div>
                        <h3 style="color:#ffffff; font-size:1.1rem; font-weight:800; margin:0 0 4px;">Articles &amp; Guides</h3>
                        <p style="color:#94a3b8; font-size:12px; margin:0; line-height:1.5;">Tutorials, device specs, code repositories &amp; tech analysis.</p>
                    </div>
                    <span style="color:#00f5d4; font-size:12px; font-weight:700; margin-top:14px;">Browse Posts ➔</span>
                </a>

                <!-- 2. Community Pillar -->
                <a href="<?php echo esc_url( home_url( '/forum/' ) ); ?>" class="hs-pillar-card" style="background:rgba(11,17,32,0.85); border:1px solid rgba(245,158,11,0.3); border-radius:16px; padding:20px; text-decoration:none; display:flex; flex-direction:column; justify-content:space-between; transition:transform 0.2s, border-color 0.2s; box-shadow:0 8px 24px rgba(0,0,0,0.4);">
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <span style="font-size:26px;">💬</span>
                            <span style="font-size:10px; font-weight:800; color:#fbbf24; background:rgba(245,158,11,0.12); padding:2px 8px; border-radius:999px;">COMMUNITY</span>
                        </div>
                        <h3 style="color:#ffffff; font-size:1.1rem; font-weight:800; margin:0 0 4px;">Q&amp;A &amp; Discussions</h3>
                        <p style="color:#94a3b8; font-size:12px; margin:0; line-height:1.5;">Ask technical questions, get solved answers &amp; earn reputation.</p>
                    </div>
                    <span style="color:#fbbf24; font-size:12px; font-weight:700; margin-top:14px;">Join Discussion ➔</span>
                </a>

                <!-- 3. Tools Pillar -->
                <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" class="hs-pillar-card" style="background:rgba(11,17,32,0.85); border:1px solid rgba(16,185,129,0.3); border-radius:16px; padding:20px; text-decoration:none; display:flex; flex-direction:column; justify-content:space-between; transition:transform 0.2s, border-color 0.2s; box-shadow:0 8px 24px rgba(0,0,0,0.4);">
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <span style="font-size:26px;">⚡</span>
                            <span style="font-size:10px; font-weight:800; color:#10b981; background:rgba(16,185,129,0.12); padding:2px 8px; border-radius:999px;">2,024+ TOOLS</span>
                        </div>
                        <h3 style="color:#ffffff; font-size:1.1rem; font-weight:800; margin:0 0 4px;">Developer Tools Lab</h3>
                        <p style="color:#94a3b8; font-size:12px; margin:0; line-height:1.5;">100% Client-side sandbox, cryptography, web dev &amp; network utilities.</p>
                    </div>
                    <span style="color:#10b981; font-size:12px; font-weight:700; margin-top:14px;">Launch Tools ➔</span>
                </a>

                <!-- 4. Academy Pillar -->
                <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" class="hs-pillar-card" style="background:rgba(11,17,32,0.85); border:1px solid rgba(167,139,250,0.3); border-radius:16px; padding:20px; text-decoration:none; display:flex; flex-direction:column; justify-content:space-between; transition:transform 0.2s, border-color 0.2s; box-shadow:0 8px 24px rgba(0,0,0,0.4);">
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <span style="font-size:26px;">🎓</span>
                            <span style="font-size:10px; font-weight:800; color:#a78bfa; background:rgba(167,139,250,0.12); padding:2px 8px; border-radius:999px;">ACADEMY</span>
                        </div>
                        <h3 style="color:#ffffff; font-size:1.1rem; font-weight:800; margin:0 0 4px;">Courses &amp; CTF Labs</h3>
                        <p style="color:#94a3b8; font-size:12px; margin:0; line-height:1.5;">Defensive cybersecurity, programming, video lessons &amp; certificates.</p>
                    </div>
                    <span style="color:#a78bfa; font-size:12px; font-weight:700; margin-top:14px;">Explore Courses ➔</span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Authority Metrics Strip -->
            <div class="hs-hero-stats-strip">
                <div class="hs-hero-stat">
                    <span class="hs-stat-num">2,024+</span>
                    <span class="hs-stat-label">Browser Security Tools</span>
                </div>
                <div class="hs-hero-stat">
                    <span class="hs-stat-num">28</span>
                    <span class="hs-stat-label">Platform Centers</span>
                </div>
                <div class="hs-hero-stat">
                    <span class="hs-stat-num">8</span>
                    <span class="hs-stat-label">Engineering Faculties</span>
                </div>
                <div class="hs-hero-stat">
                    <span class="hs-stat-num">100%</span>
                    <span class="hs-stat-label">Client-Side Sandbox</span>
                </div>
            </div>
        </div>
    </section>

    <?php
    $hs_sections = array();

    // TRENDING POSTS SLIDER
    $hs_sections['trending_slider'] = static function () use ( $show_trending, $trending_posts ): void {
        if ( ! $show_trending || empty( $trending_posts ) ) {
            return;
        }
        ?>
        <section class="hs-section-container hs-trending-section">
            <div class="hs-section-header hs-section-header--amber">
                <div>
                    <h2 class="hs-section-title hs-title-amber">🔥 Trending Across HackersShikkhok</h2>
                    <p class="hs-section-sub">Highest engagement and community read velocity this week.</p>
                </div>
                <span class="hs-section-pill hs-section-pill--amber">High Velocity</span>
            </div>

            <div class="hs-trending-slider-wrap" style="display:flex; gap:18px; overflow-x:auto; padding-bottom:14px; scroll-snap-type:x mandatory; -webkit-overflow-scrolling:touch;">
                <?php $rank = 1; foreach ( $trending_posts as $post ) : setup_postdata( $post );
                    $p_id   = (int) $post->ID;
                    $likes  = (int) get_post_meta( $p_id, 'hs_likes_count', true );
                    $views  = (int) get_post_meta( $p_id, 'hs_views_count', true );
                    $cats   = get_the_category( $p_id );
                    $cat_nm = ! empty( $cats ) ? $cats[0]->name : 'Trending';
                    $auth   = get_userdata( (int) $post->post_author );
                ?>
                    <article class="hs-trending-card" style="flex:0 0 min(82vw, 320px); background:#101827; border:1px solid rgba(234,191,87,0.32); border-radius:18px; padding:20px; display:flex; flex-direction:column; justify-content:space-between; position:relative; scroll-snap-align:start; box-shadow:0 8px 24px rgba(0,0,0,0.45);">
                        <div class="hs-tabular" style="position:absolute; top:14px; right:14px; width:30px; height:30px; border-radius:50%; background:linear-gradient(135deg, #EABF57, #d97706); color:#080D17; font-weight:900; display:flex; align-items:center; justify-content:center; font-size:12px;">
                            #<?php echo (int) $rank++; ?>
                        </div>

                        <div>
                            <span style="font-size:11px; font-weight:800; color:#EABF57; text-transform:uppercase; letter-spacing:0.5px;"><?php echo esc_html( $cat_nm ); ?></span>
                            <h3 style="font-size:1.12rem; font-weight:800; color:#E8EEF7; margin:8px 0 10px; line-height:1.35;">
                                <a href="<?php echo esc_url( (string) get_permalink( $p_id ) ); ?>" style="color:#E8EEF7; text-decoration:none;">
                                    <?php echo esc_html( get_the_title( $p_id ) ); ?>
                                </a>
                            </h3>
                            <p style="color:#929FB2; font-size:13px; line-height:1.5; margin:0 0 16px;">
                                <?php echo esc_html( wp_trim_words( get_the_excerpt( $p_id ), 14, '...' ) ); ?>
                            </p>
                        </div>

                        <div class="hs-tabular" style="display:flex; justify-content:space-between; align-items:center; padding-top:12px; border-top:1px solid rgba(255,255,255,0.06); font-size:12px; color:#cbd5e1;">
                            <span>👤 <?php echo esc_html( $auth ? $auth->display_name : 'Contributor' ); ?></span>
                            <div style="display:flex; gap:10px;">
                                <span>❤️ <?php echo esc_html( number_format( $likes ) ); ?></span>
                                <span>👁️ <?php echo esc_html( number_format( $views ) ); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
        </section>
        <?php
    };

    // FEATURED NOTICES
    $hs_sections['featured_notices'] = static function () use ( $show_notices, $featured_query ): void {
        if ( ! $show_notices ) {
            return;
        }
        ?>
        <section class="hs-section-container">
            <div class="hs-section-header hs-section-header--cyan">
                <div>
                    <h2 class="hs-section-title hs-title-cyan">🚨 Featured Notices &amp; Announcements</h2>
                    <p class="hs-section-sub">Important system alerts, security advisories, and official curated bulletins.</p>
                </div>
                <span class="hs-section-pill hs-section-pill--cyan">Official</span>
            </div>

            <div class="hs-post-grid">
                <?php
                if ( $featured_query->have_posts() ) {
                    while ( $featured_query->have_posts() ) {
                        $featured_query->the_post();
                        get_template_part( 'template-parts/featured_post' );
                    }
                    wp_reset_postdata();
                } else {
                    echo '<div class="hs-empty-state">No featured notices published yet.</div>';
                }
                ?>
            </div>
        </section>
        <?php
    };

    // CATEGORIES & FACULTIES
    $hs_sections['categories'] = static function () use ( $show_cats ): void {
        if ( ! $show_cats ) {
            return;
        }
        $cat_list = array(
            array( 'name' => 'Cybersecurity & CTF', 'slug' => 'cybersecurity', 'icon' => '🛡️', 'color' => '#20DCC5', 'desc' => 'Ethical hacking, pentesting & defensive protocols.' ),
            array( 'name' => 'Web Development', 'slug' => 'web-dev', 'icon' => '🌐', 'color' => '#38bdf8', 'desc' => 'React, Next.js, Node.js & modern full-stack engines.' ),
            array( 'name' => 'Programming & Code', 'slug' => 'programming', 'icon' => '💻', 'color' => '#8B7CFF', 'desc' => 'Python, Go, C++, algorithms & code libraries.' ),
            array( 'name' => 'AI & Machine Learning', 'slug' => 'ai-ml', 'icon' => '🤖', 'color' => '#f43f5e', 'desc' => 'LLM engineering, neural nets & autonomous agents.' ),
            array( 'name' => 'Android & Mobile', 'slug' => 'android', 'icon' => '📱', 'color' => '#10b981', 'desc' => 'APK decompilation, Android security & mobile tools.' ),
            array( 'name' => 'Cloud & DevOps', 'slug' => 'cloud-devops', 'icon' => '☁️', 'color' => '#EABF57', 'desc' => 'Docker, Kubernetes, Linux servers & CI/CD automation.' ),
            array( 'name' => 'Networking & Protocols', 'slug' => 'networking', 'icon' => '📡', 'color' => '#06b6d4', 'desc' => 'Subnets, DNS, packet inspection & firewall defenses.' ),
            array( 'name' => 'Hardware & Devices', 'slug' => 'hardware', 'icon' => '⚙️', 'color' => '#f97316', 'desc' => 'Device teardowns, verified specs & benchmark pricing.' ),
        );
        ?>
        <section class="hs-section-container">
            <div class="hs-section-header hs-section-header--cyan">
                <div>
                    <h2 class="hs-section-title">📂 Explore Knowledge Faculties &amp; Disciplines</h2>
                    <p class="hs-section-sub">Curated technical domains, structured learning tracks, and specialized repositories.</p>
                </div>
                <a href="<?php echo esc_url( home_url( '/categories/' ) ); ?>" class="hs-section-link">All Categories &rarr;</a>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(min(100%, 260px), 1fr)); gap:18px;">
                <?php foreach ( $cat_list as $c ) : ?>
                    <a href="<?php echo esc_url( home_url( '/category/' . $c['slug'] . '/' ) ); ?>" class="hs-cat-box" style="background:#101827; border:1px solid rgba(255,255,255,0.08); border-top:3px solid <?php echo esc_attr( $c['color'] ); ?>; border-radius:16px; padding:20px; text-decoration:none; display:flex; flex-direction:column; justify-content:space-between; transition:transform 0.2s, border-color 0.2s;">
                        <div>
                            <div style="font-size:28px; margin-bottom:10px;"><?php echo esc_html( $c['icon'] ); ?></div>
                            <h3 style="color:#E8EEF7; font-size:1.1rem; font-weight:800; margin:0 0 6px;"><?php echo esc_html( $c['name'] ); ?></h3>
                            <p style="color:#929FB2; font-size:12px; margin:0; line-height:1.5;"><?php echo esc_html( $c['desc'] ); ?></p>
                        </div>
                        <span style="color:<?php echo esc_attr( $c['color'] ); ?>; font-size:12px; font-weight:700; margin-top:14px;">Explore Faculty ➔</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    };

    // LATEST POSTS FEED WITH CATEGORY FILTER TABS
    $hs_sections['latest_feed'] = static function () use ( $show_latest, $latest_query ): void {
        if ( ! $show_latest ) {
            return;
        }
        $filter_cats = get_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 6, 'hide_empty' => true ) );
        ?>
        <section id="hs-content-feed-sec" class="hs-section-container">
            <div class="hs-section-header hs-section-header--emerald">
                <div>
                    <h2 class="hs-section-title hs-title-emerald">⚡ Latest Community Articles &amp; Content</h2>
                    <p class="hs-section-sub">Freshly published security research, coding guides, and tutorials.</p>
                </div>
                <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                    <a href="<?php echo esc_url( home_url( '/new-post/' ) ); ?>" style="padding:6px 12px; background:rgba(32,220,197,0.12); border:1px solid rgba(32,220,197,0.35); color:#20DCC5; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none;">+ Write Article</a>
                </div>
            </div>

            <div class="hs-forum-tabs" id="hs-latest-filter-tabs">
                <button type="button" class="hs-forum-tab-btn is-active" data-cat-filter="all">All Latest</button>
                <?php foreach ( $filter_cats as $fc ) : ?>
                    <button type="button" class="hs-forum-tab-btn" data-cat-filter="<?php echo esc_attr( $fc->slug ); ?>"><?php echo esc_html( $fc->name ); ?></button>
                <?php endforeach; ?>
            </div>

            <div class="hs-post-grid" id="hs-latest-feed-grid">
                <?php
                if ( $latest_query->have_posts() ) {
                    while ( $latest_query->have_posts() ) {
                        $latest_query->the_post();
                        $p_cats = get_the_category( get_the_ID() );
                        $c_slugs = ! empty( $p_cats ) ? implode( ',', wp_list_pluck( $p_cats, 'slug' ) ) : 'uncategorized';
                        echo '<div class="hs-latest-feed-item" data-cats="' . esc_attr( $c_slugs ) . '">';
                        get_template_part( 'template-parts/latest_post' );
                        echo '</div>';
                    }
                    wp_reset_postdata();
                } else {
                    echo '<div class="hs-empty-state">No latest articles found.</div>';
                }
                ?>
            </div>
        </section>
        <?php
    };

    // POPULAR POSTS
    $hs_sections['popular_posts'] = static function () use ( $show_popular, $popular_posts ): void {
        if ( ! $show_popular || empty( $popular_posts ) ) {
            return;
        }
        ?>
        <section class="hs-section-container">
            <div class="hs-section-header hs-section-header--amber">
                <div>
                    <h2 class="hs-section-title hs-title-amber">🔥 Most Engaged Discussions &amp; Articles</h2>
                    <p class="hs-section-sub">Ranked by engagement score (likes, discussion comments, and verified views).</p>
                </div>
                <span class="hs-section-pill hs-section-pill--amber">Community Ranked</span>
            </div>

            <div class="hs-post-grid">
                <?php
                global $post;
                foreach ( $popular_posts as $post ) {
                    setup_postdata( $post );
                    get_template_part( 'template-parts/popular_post' );
                }
                wp_reset_postdata();
                ?>
            </div>
        </section>
        <?php
    };

    // COMMUNITY Q&A SHOWCASE
    $hs_sections['qa_showcase'] = static function () use ( $show_qa, $qa_questions ): void {
        if ( ! $show_qa || empty( $qa_questions ) ) {
            return;
        }
        ?>
        <section class="hs-section-container">
            <div class="hs-section-header hs-section-header--cyan">
                <div>
                    <h2 class="hs-section-title">💬 Technical Q&amp;A &amp; Problem Solving Hub</h2>
                    <p class="hs-section-sub">Direct troubleshooting, verified solutions, and peer code debugging.</p>
                </div>
                <a href="<?php echo esc_url( home_url( '/forum/' ) ); ?>" class="hs-section-link">Open Q&amp;A Forum ➔</a>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(min(100%, 320px), 1fr)); gap:18px;">
                <?php foreach ( $qa_questions as $q ) :
                    $q_id      = (int) $q->ID;
                    $votes     = (int) get_post_meta( $q_id, '_hs_question_votes', true ) ?: 1;
                    $answers   = (int) get_comments_number( $q_id );
                    $is_solved = (bool) get_post_meta( $q_id, '_hs_question_solved', true );
                    $cat       = (string) ( get_post_meta( $q_id, '_hs_question_category', true ) ?: 'Technical' );
                ?>
                    <article style="background:#101827; border:1px solid rgba(32,220,197,0.25); border-radius:16px; padding:20px; display:flex; flex-direction:column; justify-content:space-between; box-shadow:0 6px 20px rgba(0,0,0,0.4);">
                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                <span style="font-size:11px; font-weight:800; color:#20DCC5; background:rgba(32,220,197,0.1); padding:2px 8px; border-radius:4px; font-family:var(--font-mono);">
                                    <?php echo esc_html( $cat ); ?>
                                </span>
                                <?php if ( $is_solved ) : ?>
                                    <span style="font-size:11px; font-weight:800; color:#10b981; background:rgba(16,185,129,0.12); padding:2px 8px; border-radius:4px;">✓ Solved</span>
                                <?php else : ?>
                                    <span style="font-size:11px; color:#EABF57; font-family:var(--font-mono);">Open Discussion</span>
                                <?php endif; ?>
                            </div>

                            <h3 style="font-size:1.15rem; font-weight:800; color:#E8EEF7; margin:0 0 8px; line-height:1.35;">
                                <a href="<?php echo esc_url( (string) get_permalink( $q_id ) ); ?>" style="color:#E8EEF7; text-decoration:none;">
                                    <?php echo esc_html( get_the_title( $q_id ) ); ?>
                                </a>
                            </h3>
                        </div>

                        <div class="hs-tabular" style="display:flex; justify-content:space-between; align-items:center; padding-top:14px; border-top:1px solid rgba(255,255,255,0.06); font-size:12px; color:#929FB2;">
                            <div style="display:flex; gap:12px;">
                                <span title="Votes">▲ <b><?php echo esc_html( (string) $votes ); ?></b> votes</span>
                                <span title="Answers">💬 <b><?php echo esc_html( (string) $answers ); ?></b> answers</span>
                            </div>
                            <a href="<?php echo esc_url( (string) get_permalink( $q_id ) ); ?>" style="color:#20DCC5; font-weight:700; text-decoration:none;">View &amp; Answer ➔</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    };

    // FEATURED TOOLS
    $hs_sections['featured_tools'] = static function () use ( $show_tools, $featured_tools ): void {
        if ( ! $show_tools || empty( $featured_tools ) ) {
            return;
        }
        ?>
        <section class="hs-section-container">
            <div class="hs-tools-showcase-shell">
                <div class="hs-section-header hs-section-header--cyan">
                    <div>
                        <h2 class="hs-section-title">⚡ Essential In-Browser Security &amp; Developer Tools</h2>
                        <p class="hs-section-sub">100% Client-Side execution, zero server uploads, privacy-preserving utilities.</p>
                    </div>
                    <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" class="hs-section-link">
                        Full 2,024+ Tools Directory &rarr;
                    </a>
                </div>

                <div class="hs-tools-preview-grid">
                    <?php foreach ( $featured_tools as $tool ) : ?>
                        <article class="hs-cyber-card hs-tool-showcase-card">
                            <div>
                                <span class="hs-card-badge"><?php echo esc_html( (string) ( $tool['category'] ?? 'Tool' ) ); ?></span>
                                <h3 class="hs-card-title"><?php echo esc_html( (string) ( $tool['name'] ?? 'Developer Tool' ) ); ?></h3>
                                <p class="hs-card-desc"><?php echo esc_html( (string) ( $tool['short_desc'] ?? '' ) ); ?></p>
                            </div>
                            <a href="<?php echo esc_url( home_url( '/tools/?tool=' . rawurlencode( (string) ( $tool['id'] ?? ( $tool['tool_id'] ?? '' ) ) ) ) ); ?>" class="hs-tool-launch-link">
                                Launch Tool &rarr;
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    };

    // TOOLS GATEWAY
    $hs_sections['tools_gateway'] = static function () use ( $show_gateway ): void {
        if ( ! $show_gateway ) {
            return;
        }
        ?>
        <section class="hs-section-container">
            <div style="background:linear-gradient(135deg, rgba(32,220,197,0.12), rgba(139,124,255,0.16)); border:1px solid rgba(32,220,197,0.3); border-radius:20px; padding:32px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;">
                <div style="max-width:680px;">
                    <span style="font-size:11px; font-weight:800; color:#20DCC5; text-transform:uppercase; letter-spacing:1px; background:rgba(32,220,197,0.1); padding:3px 12px; border-radius:999px;">
                        Massive Client-Side Directory
                    </span>
                    <h3 style="color:#E8EEF7; font-size:1.55rem; font-weight:900; margin:10px 0 6px;">
                        Looking for a Specific Calculator, Encoder, or Inspector?
                    </h3>
                    <p style="color:#929FB2; font-size:14px; margin:0; line-height:1.6;">
                        Explore 38 Master Centers covering Web Dev, Cryptography, JSON/XML, Image Optimization, Networking, Resistor Decoders, and Hardware latency inspectors.
                    </p>
                </div>
                <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="padding:14px 28px; background:#20DCC5; color:#080D17; font-weight:800; font-size:14px; border-radius:12px; text-decoration:none; box-shadow:0 0 20px rgba(32,220,197,0.35); white-space:nowrap;">
                    Explore All 2,024+ Tools ➔
                </a>
            </div>
        </section>
        <?php
    };

    // ACADEMY COURSES
    $hs_sections['academy_courses'] = static function () use ( $show_courses, $academy_courses ): void {
        if ( ! $show_courses || empty( $academy_courses ) ) {
            return;
        }
        ?>
        <section class="hs-section-container">
            <div class="hs-section-header hs-section-header--cyan">
                <div>
                    <h2 class="hs-section-title">🎓 Cyber Defense Academy &amp; Interactive LMS</h2>
                    <p class="hs-section-sub">Structured learning paths, CTF flag challenges, syllabus modules, and verified certificates.</p>
                </div>
                <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" class="hs-section-link">All Courses &rarr;</a>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(min(100%, 300px), 1fr)); gap:20px;">
                <?php foreach ( $academy_courses as $c ) :
                    $c_slug    = (string) ( $c['slug'] ?? 'course' );
                    $c_title   = (string) ( $c['title'] ?? 'Cybersecurity Defense Track' );
                    $c_desc    = (string) ( $c['description'] ?? 'Comprehensive course curriculum with CTF challenges and hands-on modules.' );
                    $c_level   = (string) ( $c['level'] ?? 'Intermediate' );
                    $c_lessons = count( $c['lessons'] ?? array() ) ?: 12;
                ?>
                    <article style="background:#101827; border:1px solid rgba(139,124,255,0.32); border-radius:18px; padding:22px; display:flex; flex-direction:column; justify-content:space-between; box-shadow:0 8px 24px rgba(0,0,0,0.4);">
                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                                <span style="font-size:11px; font-weight:800; color:#8B7CFF; background:rgba(139,124,255,0.12); padding:3px 10px; border-radius:999px;">
                                    <?php echo esc_html( $c_level ); ?>
                                </span>
                                <span class="hs-tabular" style="font-size:12px; color:#cbd5e1;">
                                    📚 <?php echo esc_html( (string) $c_lessons ); ?> Lessons
                                </span>
                            </div>

                            <h3 style="font-size:1.18rem; font-weight:800; color:#E8EEF7; margin:0 0 10px; line-height:1.35;">
                                <a href="<?php echo esc_url( home_url( '/academy/?course=' . rawurlencode( $c_slug ) ) ); ?>" style="color:#E8EEF7; text-decoration:none;">
                                    <?php echo esc_html( $c_title ); ?>
                                </a>
                            </h3>

                            <p style="color:#929FB2; font-size:13px; line-height:1.6; margin:0 0 18px;">
                                <?php echo esc_html( wp_trim_words( $c_desc, 18, '...' ) ); ?>
                            </p>
                        </div>

                        <div style="padding-top:14px; border-top:1px solid rgba(255,255,255,0.06); display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:12px; color:#10b981; font-weight:800;">FREE ENROLLMENT</span>
                            <a href="<?php echo esc_url( home_url( '/academy/?course=' . rawurlencode( $c_slug ) ) ); ?>" style="padding:6px 14px; background:rgba(139,124,255,0.15); border:1px solid #8B7CFF; color:#8B7CFF; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none;">
                                Start Learning ➔
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    };

    // TUTORIALS
    $hs_sections['tutorials'] = static function () use ( $show_tutorials, $tutorials_query ): void {
        if ( ! $show_tutorials || ! $tutorials_query || ! $tutorials_query->have_posts() ) {
            return;
        }
        ?>
        <section class="hs-section-container">
            <div class="hs-section-header hs-section-header--cyan">
                <div>
                    <h2 class="hs-section-title">💻 Latest Tutorials &amp; Engineering Guides</h2>
                    <p class="hs-section-sub">Step-by-step implementation walkthroughs, code architecture breakdowns, and ethical hacking exercises.</p>
                </div>
                <a href="<?php echo esc_url( home_url( '/category/tutorials/' ) ); ?>" class="hs-section-link">All Tutorials &rarr;</a>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(min(100%, 280px), 1fr)); gap:18px;">
                <?php while ( $tutorials_query->have_posts() ) : $tutorials_query->the_post();
                    $tut_id    = get_the_ID();
                    $words     = str_word_count( wp_strip_all_tags( get_the_content() ) );
                    $read_min  = max( 1, (int) ceil( $words / 200 ) );
                    $tut_cats  = get_the_category( $tut_id );
                    $tut_cat   = ! empty( $tut_cats ) ? $tut_cats[0]->name : 'Tutorial';
                ?>
                    <article class="hs-tutorial-card">
                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                <span style="font-size:11px; font-weight:800; color:#20DCC5; background:rgba(32,220,197,0.12); padding:2px 8px; border-radius:4px; font-family:var(--font-mono);">
                                    📚 <?php echo esc_html( $tut_cat ); ?>
                                </span>
                                <span class="hs-tabular" style="font-size:11px; color:#929FB2;">
                                    ⏱️ <?php echo (int) $read_min; ?> min read
                                </span>
                            </div>

                            <h3 style="font-size:1.15rem; font-weight:800; color:#E8EEF7; margin:0 0 8px; line-height:1.35;">
                                <a href="<?php the_permalink(); ?>" style="color:#E8EEF7; text-decoration:none;">
                                    <?php the_title(); ?>
                                </a>
                            </h3>

                            <p style="color:#929FB2; font-size:13px; line-height:1.5; margin:0 0 16px;">
                                <?php echo esc_html( wp_trim_words( get_the_excerpt(), 15, '...' ) ); ?>
                            </p>
                        </div>

                        <div style="padding-top:12px; border-top:1px solid rgba(255,255,255,0.06); display:flex; justify-content:space-between; align-items:center; font-size:12px; font-family:var(--font-mono); color:#cbd5e1;">
                            <span>👤 <?php the_author(); ?></span>
                            <a href="<?php the_permalink(); ?>" style="color:#20DCC5; font-weight:700; text-decoration:none;">
                                Read Guide ➔
                            </a>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </section>
        <?php
    };

    // VIDEO LEARNING HUB
    $hs_sections['video_hub'] = static function () use ( $show_video ): void {
        if ( ! $show_video || ! hs_is_module_enabled( 'mod_youtube' ) || ! class_exists( '\HackersShikkhok\Theme\Modules\Platform\YouTubeManager' ) ) {
            return;
        }
        echo '<section class="hs-section-container">' . \HackersShikkhok\Theme\Modules\Platform\YouTubeManager::render_channel_hub() . '</section>';
    };

    // COMMUNITY LEADERBOARD
    $hs_sections['community_leaderboard'] = static function () use ( $show_leader ): void {
        if ( ! $show_leader ) {
            return;
        }
        $top_users = get_users( array(
            'number'  => 4,
            'orderby' => 'post_count',
            'order'   => 'DESC',
        ) );
        ?>
        <section class="hs-section-container">
            <div class="hs-section-header hs-section-header--cyan">
                <div>
                    <h2 class="hs-section-title">🏆 Top Contributing Operatives</h2>
                    <p class="hs-section-sub">Leading cybersecurity researchers, tool authors, and community mentors.</p>
                </div>
                <a href="<?php echo esc_url( home_url( '/members/' ) ); ?>" class="hs-section-link">Member Directory ➔</a>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(min(100%, 260px), 1fr)); gap:18px;">
                <?php foreach ( $top_users as $u ) :
                    $u_id    = (int) $u->ID;
                    $u_name  = $u->display_name ?: $u->user_login;
                    $u_posts = (int) count_user_posts( $u_id, array( 'post', 'tutorials', 'tools', 'hs_question' ) );
                    $u_xp    = (int) get_user_meta( $u_id, '_hs_learning_xp', true );
                ?>
                    <div style="background:#101827; border:1px solid rgba(32,220,197,0.22); border-radius:16px; padding:20px; display:flex; align-items:center; gap:16px;">
                        <img src="<?php echo esc_url( get_avatar_url( $u_id, array( 'size' => 54 ) ) ); ?>" alt="<?php echo esc_attr( $u_name ); ?>" style="width:50px; height:50px; border-radius:50%; border:2px solid #20DCC5; object-fit:cover;" loading="lazy" />
                        <div>
                            <h4 style="margin:0 0 4px; font-size:1rem; font-weight:800; color:#E8EEF7;">
                                <a href="<?php echo esc_url( get_author_posts_url( $u_id ) ); ?>" style="color:#E8EEF7; text-decoration:none;">
                                    <?php echo esc_html( $u_name ); ?>
                                </a>
                            </h4>
                            <div class="hs-tabular" style="font-size:12px; color:#20DCC5;">
                                ⚡ <?php echo esc_html( number_format( $u_xp ) ); ?> XP · <?php echo esc_html( (string) $u_posts ); ?> Posts
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    };

    // Execute sections in configured admin order
    foreach ( $section_order as $sec_key ) {
        if ( 'hero_shortcuts' !== $sec_key && isset( $hs_sections[ $sec_key ] ) ) {
            $hs_sections[ $sec_key ]();
        }
    }
    ?>

    <!-- Google AdSense Responsive Display Placement -->
    <div class="hs-section-container" style="margin-top:20px; margin-bottom:40px;">
        <div class="hs-ad-slot-wrap" style="background:#080D17; border:1px dashed rgba(255,255,255,0.1); border-radius:14px; padding:12px; text-align:center;">
            <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Advertisement</div>
            <div class="hs-ad-banner-placement" style="min-height:90px; display:flex; align-items:center; justify-content:center; color:#929FB2; font-size:12px;">
                <?php do_action( 'hs_render_ad_slot', 'homepage_footer_banner' ); ?>
                <span style="color:#64748b;">[ Google AdSense Responsive Display Placement — Verified Unit ]</span>
            </div>
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var filterTabs = document.getElementById('hs-latest-filter-tabs');
    if (filterTabs) {
        filterTabs.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-cat-filter]');
            if (!btn) return;
            var filter = btn.getAttribute('data-cat-filter');
            filterTabs.querySelectorAll('[data-cat-filter]').forEach(function(b) { b.classList.remove('is-active'); });
            btn.classList.add('is-active');
            document.querySelectorAll('#hs-latest-feed-grid .hs-latest-feed-item').forEach(function(item) {
                var cats = (item.getAttribute('data-cats') || '').split(',');
                item.style.display = (filter === 'all' || cats.indexOf(filter) !== -1) ? '' : 'none';
            });
        });
    }
});
</script>

<!-- Full-Page TTS Reader Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var speakBtn = document.getElementById('hs-fullpage-speak-btn');
    var soundwave = document.getElementById('hs-hero-soundwave');

    if (speakBtn && 'speechSynthesis' in window) {
        var isSpeaking = false;

        speakBtn.addEventListener('click', function() {
            if (isSpeaking) {
                window.speechSynthesis.cancel();
                isSpeaking = false;
                if (soundwave) soundwave.classList.remove('is-playing');
                speakBtn.classList.remove('is-speaking');
                speakBtn.setAttribute('aria-pressed', 'false');
                return;
            }

            var headings = Array.from(document.querySelectorAll('main h1, main h2, main h3')).map(function(el) { return el.innerText; }).join('. ');
            var paragraphs = Array.from(document.querySelectorAll('main p')).map(function(el) { return el.innerText; }).slice(0, 10).join('. ');
            var fullText = 'Welcome to HackersShikkhok. ' + headings + '. ' + paragraphs;

            var utterance = new SpeechSynthesisUtterance(fullText);
            utterance.rate = 1.0;
            utterance.pitch = 1.0;

            var voices = window.speechSynthesis.getVoices();
            var selVoice = voices.find(function(v) { return v.lang.indexOf('en') !== -1; });
            if (selVoice) utterance.voice = selVoice;

            utterance.onstart = function() {
                isSpeaking = true;
                if (soundwave) soundwave.classList.add('is-playing');
                speakBtn.classList.add('is-speaking');
                speakBtn.setAttribute('aria-pressed', 'true');
            };

            utterance.onend = function() {
                isSpeaking = false;
                if (soundwave) soundwave.classList.remove('is-playing');
                speakBtn.classList.remove('is-speaking');
                speakBtn.setAttribute('aria-pressed', 'false');
            };

            utterance.onerror = utterance.onend;

            window.speechSynthesis.speak(utterance);
        });
    }
});
</script>

<?php
get_footer();

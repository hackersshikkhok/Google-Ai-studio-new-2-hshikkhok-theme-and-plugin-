<?php
/**
 * Front Page Template - HackersShikkhok
 * Brand: HackersShikkhok (https://hackersshikkhok.com | YouTube: @HackersShikkhok)
 * Features: Featured (Notice) Posts, Popular Posts (Ranked by Likes/Comments/Views), Latest Posts, Full-Page AI Voice Speaker Reader
 */
declare(strict_types=1);

get_header();

// Fetch settings from Unified Theme Settings Panel
$feat_cat     = (string) hs_get_setting( 'featured_category', 'notice' );
$w_likes      = (float) hs_get_setting( 'popular_weight_likes', 1.0 );
$w_comms      = (float) hs_get_setting( 'popular_weight_comments', 1.5 );
$w_views      = (float) hs_get_setting( 'popular_weight_views', 0.5 );
$pop_cnt      = max( 1, (int) hs_get_setting( 'popular_posts_count', 6 ) );
$lat_cnt      = max( 1, (int) hs_get_setting( 'latest_posts_count', 8 ) );
$voice_active = (bool) hs_get_setting( 'enable_ai_voice_reader', true );

// 1. Featured Posts IDs (Cached)
$featured_ids = hs_cache_remember(
    'front_featured_ids_' . $feat_cat,
    static function () use ( $feat_cat ): array {
        $ids = get_posts( array(
            'category_name'          => $feat_cat,
            'posts_per_page'         => 4,
            'post_status'            => 'publish',
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'update_post_term_cache' => false,
        ) );
        if ( empty( $ids ) ) {
            $ids = get_posts( array(
                'posts_per_page'         => 4,
                'post_status'            => 'publish',
                'fields'                 => 'ids',
                'no_found_rows'          => true,
                'update_post_term_cache' => false,
            ) );
        }
        return array_map( 'intval', $ids );
    }
);

$featured_query = new \WP_Query( array(
    'post__in'               => ! empty( $featured_ids ) ? $featured_ids : array( 0 ),
    'orderby'                => 'post__in',
    'posts_per_page'         => 4,
    'post_status'            => 'publish',
    'no_found_rows'          => true,
    'ignore_sticky_posts'    => true,
) );

// 2. Popular Posts IDs (Cached & Ranked without N+1 meta queries)
$popular_ids = hs_cache_remember(
    'front_popular_ids_' . $pop_cnt,
    static function () use ( $w_likes, $w_comms, $w_views, $pop_cnt ): array {
        $candidate_ids = get_posts( array(
            'posts_per_page'         => 30,
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
        'post__in'               => $popular_ids,
        'orderby'                => 'post__in',
        'posts_per_page'         => $pop_cnt,
        'post_status'            => 'publish',
        'no_found_rows'          => true,
    ) )
    : array();

if ( ! empty( $popular_ids ) ) {
    update_meta_cache( 'post', $popular_ids );
}

// 3. Latest Posts IDs (Cached)
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

// Tools & Centers (Lazy-loaded & cached)
$featured_tools = array();
if ( hs_is_module_enabled( 'mod_tools_engine' ) && class_exists( '\HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry' ) ) {
    $featured_tools = hs_cache_remember(
        'front_featured_tools_8',
        static fn(): array => array_slice( \HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry::get_all_tools(), 0, 8 ),
        3600
    );
}

$current_user_id = get_current_user_id();
$saved_voice     = $current_user_id > 0 ? ( get_user_meta( $current_user_id, 'hs_ai_voice', true ) ?: 'Ananya' ) : 'Ananya';
?>

<main id="primary" class="site-main hs-front-page">

    <!-- Hero Section with Full-Page AI Voice Reader Control -->
    <section class="hs-hero-section" style="position:relative; text-align:center; padding:50px 20px;">
        <div class="hs-hero-badge" style="display:inline-block; background:rgba(0,245,212,0.15); color:#00f5d4; border:1px solid rgba(0,245,212,0.4); padding:6px 16px; border-radius:999px; font-weight:800; font-size:12px; margin-bottom:16px;">
            CYBER DEFENSE ACADEMY &amp; 2,024+ TOOL LAB
        </div>
        <h1 class="hs-hero-title" style="font-size:2.8rem; font-weight:900; color:#fff; margin:0 0 16px 0; line-height:1.2;">
            Master Defensive Cybersecurity &amp; Full-Stack Engineering
        </h1>
        <p class="hs-hero-sub" style="max-width:800px; margin:0 auto 24px auto; color:#94a3b8; font-size:1rem; line-height:1.6;">
            HackersShikkhok is the premier open-access cyber defense academy, production code repository, and interactive 2,024+ client-side developer toolbox.
        </p>

        <?php if ( $voice_active ) : ?>
        <!-- Full-Page AI Voice Reader Bar -->
        <div class="hs-fullpage-reader-banner" style="max-width:600px; margin:0 auto 30px auto; background:rgba(11,17,32,0.9); border:1px solid rgba(0,245,212,0.5); border-radius:14px; padding:12px 20px; display:flex; align-items:center; justify-content:space-between; box-shadow:0 0 30px rgba(0,245,212,0.2);">
            <div style="display:flex; align-items:center; gap:10px;">
                <span style="font-size:22px;">🤖</span>
                <div style="text-align:left;">
                    <div style="color:#fff; font-weight:800; font-size:13px;">AI Voice Master Reader</div>
                    <div style="color:#00f5d4; font-size:11px; font-weight:600;">Active Persona: <span id="hs-hero-voice-name"><?php echo esc_html( $saved_voice ); ?></span></div>
                </div>
            </div>
            <button type="button" id="hs-fullpage-speak-btn" data-voice="<?php echo esc_attr( $saved_voice ); ?>" style="background:linear-gradient(135deg, #00f5d4, #00bbf9); color:#050811; border:none; padding:10px 20px; border-radius:10px; font-weight:900; font-size:13px; cursor:pointer; display:flex; align-items:center; gap:8px; box-shadow:0 0 15px rgba(0,245,212,0.4); transition:all 0.2s;">
                <span style="font-size:16px;">🔊</span>
                <span id="hs-read-btn-text">Listen All Page</span>
                <span class="hs-soundwave" id="hs-hero-soundwave" style="display:none; align-items:center; gap:2px; height:14px;">
                    <span class="hs-bar-w"></span>
                    <span class="hs-bar-w"></span>
                    <span class="hs-bar-w"></span>
                    <span class="hs-bar-w"></span>
                </span>
            </button>
        </div>
        <?php endif; ?>

        <div class="hs-hero-cta" style="display:flex; justify-content:center; gap:14px; flex-wrap:wrap;">
            <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" class="hs-btn-primary">🎓 Explore Academy Courses</a>
            <a href="<?php echo esc_url( home_url( '/platform-center/' ) ); ?>" class="hs-btn-primary" style="background:linear-gradient(135deg, #10b981 0%, #06b6d4 100%); border-color:#10b981; color:#fff;">🚀 Platform Center</a>
            <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" class="hs-btn-secondary">⚡ Browse 2,024+ Tools</a>
        </div>
    </section>

    <!-- SECTION 1: FEATURED POSTS (NOTICE) - Cyan/Purple Theme -->
    <section class="hs-section-container" style="max-width:1400px; margin:40px auto; padding:0 20px;">
        <div class="hs-section-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid rgba(0,245,212,0.2); padding-bottom:12px;">
            <div>
                <h2 class="hs-section-title" style="color:#00f5d4; font-size:1.6rem; font-weight:900; margin:0 0 4px 0;">🚨 Featured Notices &amp; Announcements</h2>
                <p style="color:#94a3b8; font-size:13px; margin:0;">Important announcements, official cyber alerts, and featured editorial guides.</p>
            </div>
            <span style="background:rgba(0,245,212,0.15); color:#00f5d4; border:1px solid rgba(0,245,212,0.3); padding:4px 12px; border-radius:8px; font-size:12px; font-weight:800;">Admin Managed</span>
        </div>

        <div class="hs-card-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
            <?php 
            if ( $featured_query->have_posts() ) {
                while ( $featured_query->have_posts() ) {
                    $featured_query->the_post();
                    get_template_part( 'template-parts/featured_post' );
                }
                wp_reset_postdata();
            } else {
                echo '<p style="color:#94a3b8;">No featured posts found.</p>';
            }
            ?>
        </div>
    </section>

    <!-- SECTION 2: POPULAR POSTS (RANKED BY LIKES, COMMENTS, VIEWS) - Amber/Gold Theme -->
    <section class="hs-section-container" style="max-width:1400px; margin:50px auto; padding:0 20px;">
        <div class="hs-section-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid rgba(234,179,8,0.2); padding-bottom:12px;">
            <div>
                <h2 class="hs-section-title" style="color:#eab308; font-size:1.6rem; font-weight:900; margin:0 0 4px 0;">🔥 Popular &amp; Trending Posts</h2>
                <p style="color:#94a3b8; font-size:13px; margin:0;">Automatically ranked by highest engagement (likes, comments, and views).</p>
            </div>
            <span style="background:rgba(234,179,8,0.15); color:#eab308; border:1px solid rgba(234,179,8,0.3); padding:4px 12px; border-radius:8px; font-size:12px; font-weight:800;">Auto-Ranked</span>
        </div>

        <div class="hs-card-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
            <?php 
            if ( ! empty( $popular_posts ) ) {
                foreach ( $popular_posts as $post ) {
                    setup_postdata( $post );
                    get_template_part( 'template-parts/popular_post' );
                }
                wp_reset_postdata();
            } else {
                echo '<p style="color:#94a3b8;">No popular posts found.</p>';
            }
            ?>
        </div>
    </section>

    <!-- SECTION 3: LATEST POSTS (CHRONOLOGICAL) - Emerald/Teal Theme -->
    <section class="hs-section-container" style="max-width:1400px; margin:50px auto; padding:0 20px;">
        <div class="hs-section-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid rgba(16,185,129,0.2); padding-bottom:12px;">
            <div>
                <h2 class="hs-section-title" style="color:#10b981; font-size:1.6rem; font-weight:900; margin:0 0 4px 0;">⚡ Latest Cyber Tutorials &amp; Articles</h2>
                <p style="color:#94a3b8; font-size:13px; margin:0;">Freshly published guides, penetration testing tutorials, and code repositories.</p>
            </div>
            <span style="background:rgba(16,185,129,0.15); color:#10b981; border:1px solid rgba(16,185,129,0.3); padding:4px 12px; border-radius:8px; font-size:12px; font-weight:800;">Chronological</span>
        </div>

        <div class="hs-card-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
            <?php 
            if ( $latest_query->have_posts() ) {
                while ( $latest_query->have_posts() ) {
                    $latest_query->the_post();
                    get_template_part( 'template-parts/latest_post' );
                }
                wp_reset_postdata();
            } else {
                echo '<p style="color:#94a3b8;">No latest posts found.</p>';
            }
            ?>
        </div>
    </section>

    <?php if ( ! empty( $featured_tools ) ) : ?>
    <!-- 2,024+ Tools Center Preview -->
    <section class="hs-section-container" style="max-width:1400px; margin:50px auto; padding:30px 20px; background:rgba(11,17,32,0.4); border-radius:24px; border:1px solid rgba(0,245,212,0.3);">
        <div class="hs-section-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div>
                <h2 class="hs-section-title" style="color:#fff; font-size:1.5rem; font-weight:900;">⚡ Essential Developer &amp; Security Tools</h2>
                <p style="color:#94a3b8; font-size:14px; margin-top:4px;">100% Client-Side, privacy-preserving, and instant in-browser execution.</p>
            </div>
            <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="color:#00f5d4; text-decoration:none; font-size:14px; font-weight:700;">Full 2,024+ Tools Directory &rarr;</a>
        </div>

        <div class="hs-card-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:16px;">
            <?php foreach ( $featured_tools as $tool ) : ?>
                <div class="hs-cyber-card" style="background:#050811; border:1px solid rgba(0,245,212,0.3); border-radius:14px; padding:18px;">
                    <span class="hs-card-badge" style="background:rgba(0,245,212,0.15); color:#00f5d4; padding:2px 8px; border-radius:6px; font-size:10px; font-weight:800;"><?php echo esc_html( $tool['category'] ?? 'Tool' ); ?></span>
                    <h3 class="hs-card-title" style="color:#fff; font-size:1.1rem; font-weight:800; margin:10px 0 6px 0;"><?php echo esc_html( $tool['name'] ?? 'Tool' ); ?></h3>
                    <p class="hs-card-desc" style="color:#94a3b8; font-size:0.85rem; line-height:1.5; margin:0 0 14px 0;"><?php echo esc_html( $tool['short_desc'] ?? '' ); ?></p>
                    <a href="<?php echo esc_url( home_url( '/tools/?tool=' . urlencode( (string) ( $tool['id'] ?? ( $tool['tool_id'] ?? '' ) ) ) ) ); ?>" class="hs-btn-secondary" style="display:block; text-align:center; padding:8px 16px; font-size:13px; background:rgba(0,245,212,0.1); color:#00f5d4; border:1px solid rgba(0,245,212,0.3); border-radius:8px; text-decoration:none; font-weight:700;">Launch Tool</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- YouTube & Community Section -->
    <?php if ( hs_is_module_enabled( 'mod_youtube' ) && class_exists( '\HackersShikkhok\Theme\Modules\Platform\YouTubeManager' ) ) : ?>
    <section class="hs-section-container" style="max-width:1400px; margin:50px auto; padding:0 20px;">
        <?php echo \HackersShikkhok\Theme\Modules\Platform\YouTubeManager::render_channel_hub(); ?>
    </section>
    <?php endif; ?>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const speakBtn = document.getElementById('hs-fullpage-speak-btn');
    const soundwave = document.getElementById('hs-hero-soundwave');
    const btnText = document.getElementById('hs-read-btn-text');

    if (speakBtn && 'speechSynthesis' in window) {
        let isSpeaking = false;
        let utterance = null;

        speakBtn.addEventListener('click', function() {
            if (isSpeaking) {
                window.speechSynthesis.cancel();
                isSpeaking = false;
                if (soundwave) soundwave.style.display = 'none';
                if (btnText) btnText.textContent = 'Listen All Page';
                speakBtn.style.borderColor = 'rgba(0,245,212,0.5)';
                return;
            }

            // Extract all readable page content
            const headings = Array.from(document.querySelectorAll('h1, h2, h3')).map(el => el.innerText).join('. ');
            const paragraphs = Array.from(document.querySelectorAll('p')).map(el => el.innerText).slice(0, 10).join('. ');
            const fullText = "স্বাগতম হ্যাকার শিক্ষক ডট কম। " + headings + ". " + paragraphs;

            utterance = new SpeechSynthesisUtterance(fullText);
            utterance.rate = 1.0;
            utterance.pitch = 1.0;

            const voices = window.speechSynthesis.getVoices();
            const voiceName = speakBtn.getAttribute('data-voice') || 'Ananya';
            let selVoice = voices.find(v => v.lang.includes('bn') || v.lang.includes('en'));
            if (selVoice) utterance.voice = selVoice;

            utterance.onstart = function() {
                isSpeaking = true;
                if (soundwave) soundwave.style.display = 'inline-flex';
                if (btnText) btnText.textContent = 'Stop Reading';
                speakBtn.style.borderColor = '#ff007f';
            };

            utterance.onend = function() {
                isSpeaking = false;
                if (soundwave) soundwave.style.display = 'none';
                if (btnText) btnText.textContent = 'Listen All Page';
                speakBtn.style.borderColor = 'rgba(0,245,212,0.5)';
            };

            window.speechSynthesis.speak(utterance);
        });
    }
});
</script>

<?php
get_footer();

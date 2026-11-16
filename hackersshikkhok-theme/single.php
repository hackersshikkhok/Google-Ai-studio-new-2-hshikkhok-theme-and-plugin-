<?php
/**
 * Master Cyberpunk Single Post Template - HackersShikkhok (v5.0.4)
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 *
 * Features:
 * - 100% English Typography & UI (Zero non-English text)
 * - Authority Breadcrumbs, Reading Time, Live Views & Likes
 * - Interactive Sticky / Floating Engagement Action Dock (Like, Bookmark, Views, Comments, Share, Report)
 * - Dynamic Collapsible Table of Contents (ToC)
 * - Enriched Code Blocks with Language Badges & 1-Click Clipboard Copy
 * - Policy-Compliant Google AdSense Placements (Top, Mid-Article, Bottom)
 * - Full Author Profile Showcase with Social Handles & Follow Action
 * - Related Articles Recommendation Grid
 * - Content Moderation & Reporting Modal Dialog
 */

declare(strict_types=1);

get_header();

while ( have_posts() ) : the_post();
    $post_id     = (int) get_the_ID();
    $author_id   = (int) get_the_author_meta( 'ID' );
    $author_name = get_the_author();
    $author_bio  = get_the_author_meta( 'description' ) ?: 'Cybersecurity researcher, full-stack engineer, and contributing author at HackersShikkhok.';
    $categories  = get_the_category();
    $primary_cat = ! empty( $categories ) ? $categories[0] : null;
    $tags        = get_the_tags();

    // Increment views safely with deduplication
    \HackersShikkhok\Theme\Modules\Platform\MetricsManager::increment_views( $post_id );
    $views_count = \HackersShikkhok\Theme\Modules\Platform\MetricsManager::get_views( $post_id );
    $likes_count = \HackersShikkhok\Theme\Modules\Platform\MetricsManager::get_likes( $post_id );
    $comm_count  = (int) get_comments_number( $post_id );

    // Reading time calculation
    $word_count   = str_word_count( wp_strip_all_tags( get_the_content() ) );
    $reading_time = max( 1, (int) round( $word_count / 200 ) );

    // Settings checks
    $enable_toc         = (bool) hs_get_setting( 'enable_toc', true );
    $enable_dock        = (bool) hs_get_setting( 'enable_sticky_action_bar', true );
    $enable_author_box  = (bool) hs_get_setting( 'enable_author_box', true );
    $enable_related     = (bool) hs_get_setting( 'enable_related_posts', true );
    $related_count      = (int) hs_get_setting( 'related_posts_count', 3 );
    $enable_code_copy   = (bool) hs_get_setting( 'enable_code_copy_button', true );
    $enable_report      = (bool) hs_get_setting( 'enable_report_modal', true );
    $enable_mid_ad      = (bool) hs_get_setting( 'enable_mid_article_ad', true );

    // Check if user has bookmarked or liked
    $user_id       = get_current_user_id();
    $is_liked      = false;
    $is_bookmarked = false;
    if ( $user_id > 0 ) {
        $user_likes      = (array) get_user_meta( $user_id, '_hs_liked_posts', true );
        $is_liked        = in_array( $post_id, $user_likes, true );
        $user_bookmarks  = (array) get_user_meta( $user_id, '_hs_user_bookmarks', true );
        $is_bookmarked   = isset( $user_bookmarks[ (string) $post_id ] );
    } else {
        $is_liked = isset( $_COOKIE[ 'hs_liked_' . $post_id ] );
    }

    $pub_date = get_the_date( 'M d, Y' );
    $mod_date = get_the_modified_date( 'M d, Y' );
    $post_url = get_permalink();
    $hs_ajax_nonce = wp_create_nonce( 'hs_ajax_nonce' );
?>

<main id="primary" class="site-main hs-single-post-view" style="background:#06080d; color:#f8fafc; min-height:100vh; padding:32px 16px 80px;">
    <div style="max-width:1140px; margin:0 auto;">

        <!-- 1. Breadcrumbs Navigation -->
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:24px; font-family:var(--font-mono); flex-wrap:wrap;">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
            <span style="color:#64748b;">/</span>
            <?php if ( $primary_cat ) : ?>
                <a href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>" style="color:#00f5d4; text-decoration:none;">
                    <?php echo esc_html( $primary_cat->name ); ?>
                </a>
                <span style="color:#64748b;">/</span>
            <?php else : ?>
                <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Articles</a>
                <span style="color:#64748b;">/</span>
            <?php endif; ?>
            <span style="color:#cbd5e1; font-weight:700;"><?php echo esc_html( wp_trim_words( get_the_title(), 8, '...' ) ); ?></span>
        </nav>

        <article id="post-<?php the_ID(); ?>" <?php post_class( 'hs-article-card' ); ?> style="background:linear-gradient(135deg, rgba(11,17,32,0.98), rgba(15,23,42,0.95)); border:1px solid rgba(0,245,212,0.25); border-radius:24px; padding:clamp(20px, 4vw, 44px); box-shadow:0 16px 48px rgba(0,0,0,0.6); position:relative;">

            <!-- 2. Article Header -->
            <header class="hs-article-header" style="margin-bottom:32px;">
                <?php if ( $primary_cat ) : ?>
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px; flex-wrap:wrap;">
                        <a href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>" style="display:inline-block; background:rgba(0,245,212,0.12); color:#00f5d4; border:1px solid rgba(0,245,212,0.35); font-size:11px; font-weight:800; padding:4px 14px; border-radius:999px; text-transform:uppercase; letter-spacing:0.8px; text-decoration:none;">
                            📁 <?php echo esc_html( $primary_cat->name ); ?>
                        </a>
                        <span style="font-size:12px; color:#94a3b8; font-family:var(--font-mono);">
                            ⏱️ <?php echo esc_html( (string) $reading_time ); ?> min read
                        </span>
                    </div>
                <?php endif; ?>

                <h1 style="color:#ffffff; font-size:clamp(1.85rem, 4.5vw, 3rem); font-weight:900; line-height:1.2; margin:0 0 18px; text-wrap:balance;">
                    <?php the_title(); ?>
                </h1>

                <?php if ( has_excerpt() ) : ?>
                    <p style="color:#94a3b8; font-size:clamp(1rem, 2vw, 1.2rem); line-height:1.6; margin:0 0 22px; max-width:85ch;">
                        <?php echo esc_html( get_the_excerpt() ); ?>
                    </p>
                <?php endif; ?>

                <!-- Comprehensive Meta Line with Author Profile -->
                <div class="hs-article-meta-bar" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; padding:16px 20px; background:#060a15; border:1px solid rgba(255,255,255,0.06); border-radius:14px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" style="text-decoration:none; display:flex; align-items:center; gap:10px;">
                            <img src="<?php echo esc_url( get_avatar_url( $author_id, array( 'size' => 42 ) ) ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" style="width:40px; height:40px; border-radius:50%; border:2px solid #00f5d4; object-fit:cover;" loading="lazy" />
                            <div>
                                <div style="color:#ffffff; font-weight:800; font-size:14px; line-height:1.2;">
                                    <?php echo esc_html( $author_name ); ?>
                                </div>
                                <div style="color:#00f5d4; font-size:11px; font-family:var(--font-mono); margin-top:2px;">
                                    Operative Level 10
                                </div>
                            </div>
                        </a>
                    </div>

                    <div style="display:flex; align-items:center; gap:16px; font-size:12px; color:#94a3b8; font-family:var(--font-mono); flex-wrap:wrap;">
                        <span title="Published Date">📅 <?php echo esc_html( $pub_date ); ?></span>
                        <?php if ( $mod_date !== $pub_date ) : ?>
                            <span title="Last Updated">🔄 <?php echo esc_html( $mod_date ); ?></span>
                        <?php endif; ?>
                        <span title="Total Views">👁️ <b id="hs-views-stat"><?php echo esc_html( number_format( $views_count ) ); ?></b> views</span>
                        <span title="Total Likes">❤️ <b id="hs-likes-stat"><?php echo esc_html( number_format( $likes_count ) ); ?></b> likes</span>
                        <a href="#comments" style="color:#94a3b8; text-decoration:none;" title="Comments">💬 <b><?php echo esc_html( (string) $comm_count ); ?></b></a>
                    </div>
                </div>
            </header>

            <!-- 3. Featured Image Showcase -->
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="hs-featured-media-frame" style="margin-bottom:36px; border-radius:18px; overflow:hidden; border:1px solid rgba(0,245,212,0.3); box-shadow:0 12px 36px rgba(0,0,0,0.6); position:relative;">
                    <?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:auto; display:block; aspect-ratio:16/9; object-fit:cover;' ) ); ?>
                </div>
            <?php endif; ?>

            <!-- 4. Google AdSense Responsive Top Banner -->
            <div class="hs-ad-slot-wrap" style="margin-bottom:32px; background:#080d1a; border:1px dashed rgba(255,255,255,0.1); border-radius:14px; padding:12px; text-align:center;">
                <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Advertisement</div>
                <div class="hs-ad-banner-placement" style="min-height:90px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:12px;">
                    <?php do_action( 'hs_render_ad_slot', 'single_article_top' ); ?>
                    <span style="color:#64748b;">[ Google AdSense Responsive Display Unit — Verified Placement ]</span>
                </div>
            </div>

            <!-- 5. Interactive Sticky / Floating Engagement Dock -->
            <?php if ( $enable_dock ) : ?>
            <div class="hs-single-action-dock" style="display:flex; justify-content:space-between; align-items:center; background:#080e1e; border:1px solid rgba(0,245,212,0.3); border-radius:14px; padding:12px 18px; margin-bottom:32px; flex-wrap:wrap; gap:12px;">
                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <!-- Like Button -->
                    <button type="button" class="hs-dock-btn hs-like-btn <?php echo $is_liked ? 'is-liked' : ''; ?>" data-post-id="<?php echo $post_id; ?>" title="Like this article" aria-label="Like article" style="background:rgba(255,51,102,0.1); border:1px solid <?php echo $is_liked ? '#ff3366' : 'rgba(255,51,102,0.3)'; ?>; color:#ff3366; padding:8px 16px; border-radius:10px; font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:6px; cursor:pointer; transition:transform 0.15s, background 0.15s;">
                        <span>❤️</span> <span class="hs-likes-count"><?php echo esc_html( (string) $likes_count ); ?></span> Likes
                    </button>

                    <!-- Bookmark Button -->
                    <button type="button" class="hs-dock-btn hs-bookmark-btn <?php echo $is_bookmarked ? 'is-bookmarked' : ''; ?>" data-post-id="<?php echo $post_id; ?>" title="Bookmark to Library" aria-label="Bookmark article" style="background:rgba(0,245,212,0.1); border:1px solid <?php echo $is_bookmarked ? '#00f5d4' : 'rgba(0,245,212,0.3)'; ?>; color:#00f5d4; padding:8px 16px; border-radius:10px; font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:6px; cursor:pointer; transition:transform 0.15s, background 0.15s;">
                        <span>🔖</span> <span class="hs-bookmark-txt"><?php echo $is_bookmarked ? 'Saved' : 'Bookmark'; ?></span>
                    </button>

                    <!-- Jump to Comments -->
                    <a href="#comments" class="hs-dock-btn" title="Jump to Discussion" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#cbd5e1; padding:8px 16px; border-radius:10px; font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
                        <span>💬</span> <?php echo esc_html( (string) $comm_count ); ?> Comments
                    </a>
                </div>

                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <!-- Share Dropdown Button -->
                    <div class="hs-share-menu-wrap" style="position:relative;">
                        <button type="button" class="hs-dock-btn" onclick="toggleHsShareMenu(event)" title="Share Article" style="background:#00f5d4; color:#050811; border:none; padding:8px 18px; border-radius:10px; font-weight:800; font-size:13px; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                            <span>🔗</span> Share
                        </button>
                        <div id="hs-share-dropdown" style="display:none; position:absolute; right:0; top:calc(100% + 8px); background:#0b1120; border:1px solid rgba(0,245,212,0.35); border-radius:12px; padding:10px; min-width:180px; box-shadow:0 12px 32px rgba(0,0,0,0.7); z-index:50;">
                            <a href="https://api.whatsapp.com/send?text=<?php echo rawurlencode( get_the_title() . ' ' . $post_url ); ?>" target="_blank" rel="noopener noreferrer" style="display:flex; align-items:center; gap:8px; padding:8px 12px; color:#f8fafc; font-size:13px; text-decoration:none; border-radius:6px; transition:background 0.15s;">
                                <span>📱</span> WhatsApp
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=<?php echo rawurlencode( get_the_title() ); ?>&url=<?php echo rawurlencode( $post_url ); ?>&via=HackersShikkhok" target="_blank" rel="noopener noreferrer" style="display:flex; align-items:center; gap:8px; padding:8px 12px; color:#f8fafc; font-size:13px; text-decoration:none; border-radius:6px; transition:background 0.15s;">
                                <span>🐦</span> Twitter / X
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( $post_url ); ?>" target="_blank" rel="noopener noreferrer" style="display:flex; align-items:center; gap:8px; padding:8px 12px; color:#f8fafc; font-size:13px; text-decoration:none; border-radius:6px; transition:background 0.15s;">
                                <span>📘</span> Facebook
                            </a>
                            <a href="https://t.me/share/url?url=<?php echo rawurlencode( $post_url ); ?>&text=<?php echo rawurlencode( get_the_title() ); ?>" target="_blank" rel="noopener noreferrer" style="display:flex; align-items:center; gap:8px; padding:8px 12px; color:#f8fafc; font-size:13px; text-decoration:none; border-radius:6px; transition:background 0.15s;">
                                <span>✈️</span> Telegram
                            </a>
                            <button type="button" onclick="copyHsPostLink(event, '<?php echo esc_js( $post_url ); ?>')" style="width:100%; text-align:left; background:none; border:none; display:flex; align-items:center; gap:8px; padding:8px 12px; color:#00f5d4; font-size:13px; font-weight:700; cursor:pointer; border-radius:6px;">
                                <span>📋</span> <span id="hs-copy-link-txt">Copy Link</span>
                            </button>
                        </div>
                    </div>

                    <!-- Report Content Trigger -->
                    <?php if ( $enable_report ) : ?>
                    <button type="button" onclick="openHsReportModal()" class="hs-dock-btn" title="Report Incorrect Information or Security Violation" style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); color:#94a3b8; padding:8px 12px; border-radius:10px; font-size:12px; cursor:pointer;" aria-label="Report article">
                        ⚠️ Report
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- 6. Collapsible Table of Contents (ToC) -->
            <?php if ( $enable_toc ) : ?>
            <div id="hs-toc-container" class="hs-toc-box" style="display:none; background:#070b16; border:1px solid rgba(0,245,212,0.22); border-radius:16px; padding:20px 24px; margin-bottom:36px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; cursor:pointer;" onclick="toggleHsToc()">
                    <div style="display:flex; align-items:center; gap:8px; color:#00f5d4; font-weight:800; font-size:14px; text-transform:uppercase; letter-spacing:0.8px;">
                        <span>📑</span> Table of Contents
                    </div>
                    <span id="hs-toc-toggle-icon" style="color:#94a3b8; font-size:13px; font-family:var(--font-mono);">[ Collapse − ]</span>
                </div>
                <nav id="hs-toc-list" aria-label="Table of Contents" style="font-size:14px; line-height:1.7; color:#cbd5e1;"></nav>
            </div>
            <?php endif; ?>

            <!-- 7. Main Article Content Container -->
            <div class="entry-content hs-article-body" style="color:#e2e8f0; font-size:17px; line-height:1.85; max-width:76ch; margin:0 auto;">
                <?php
                the_content();

                wp_link_pages( array(
                    'before' => '<div class="page-links" style="margin-top:28px; padding:16px; background:#070a14; border-radius:10px; font-weight:700;">' . esc_html__( 'Pages:', 'hackersshikkhok-theme' ),
                    'after'  => '</div>',
                ) );
                ?>
            </div>

            <!-- 8. Mid-Article / Content Bottom AdSense Unit -->
            <div class="hs-ad-slot-wrap" style="margin:36px 0; background:#080d1a; border:1px dashed rgba(255,255,255,0.1); border-radius:14px; padding:12px; text-align:center;">
                <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Advertisement</div>
                <div class="hs-ad-banner-placement" style="min-height:90px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:12px;">
                    <?php do_action( 'hs_render_ad_slot', 'single_article_bottom' ); ?>
                    <span style="color:#64748b;">[ Google AdSense Native In-Article Display Unit — Verified Placement ]</span>
                </div>
            </div>

            <!-- 9. Tags Cloud -->
            <?php if ( ! empty( $tags ) ) : ?>
                <div class="hs-article-tags-wrap" style="margin-top:36px; padding-top:24px; border-top:1px solid rgba(255,255,255,0.08); display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
                    <span style="font-size:13px; color:#94a3b8; font-weight:700; font-family:var(--font-mono);">🏷️ Topics:</span>
                    <?php foreach ( $tags as $tag ) : ?>
                        <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" style="font-size:12px; background:rgba(255,255,255,0.04); color:#cbd5e1; padding:4px 14px; border-radius:8px; border:1px solid rgba(255,255,255,0.08); text-decoration:none; font-family:var(--font-mono); transition:border-color 0.15s, color 0.15s;">
                            #<?php echo esc_html( $tag->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- 10. Author Profile Showcase Card -->
            <?php if ( $enable_author_box ) :
                $author_github   = get_user_meta( $author_id, '_hs_social_github', true );
                $author_twitter  = get_user_meta( $author_id, '_hs_social_twitter', true );
                $author_website  = get_the_author_meta( 'user_url', $author_id );
            ?>
            <div class="hs-author-showcase-box" style="margin-top:40px; padding:28px; background:#070b16; border:1px solid rgba(0,245,212,0.25); border-radius:18px; display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;">
                <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" style="text-decoration:none; flex-shrink:0;">
                    <img src="<?php echo esc_url( get_avatar_url( $author_id, array( 'size' => 72 ) ) ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" style="width:64px; height:64px; border-radius:50%; border:3px solid #00f5d4; object-fit:cover;" loading="lazy" />
                </a>
                <div style="flex:1; min-width:240px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:8px;">
                        <div>
                            <span style="font-size:11px; font-weight:800; color:#10b981; background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.3); padding:2px 10px; border-radius:999px; text-transform:uppercase; letter-spacing:0.5px;">Staff Author</span>
                            <h3 style="margin:4px 0 0; font-size:1.25rem; font-weight:800; color:#ffffff;">
                                <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" style="color:#ffffff; text-decoration:none;">
                                    <?php echo esc_html( $author_name ); ?>
                                </a>
                            </h3>
                        </div>

                        <button type="button" onclick="toggleAuthorFollow(event, <?php echo $author_id; ?>)" class="hs-follow-btn" id="hs-follow-author-btn" style="padding:6px 16px; border-radius:8px; background:rgba(0,245,212,0.12); border:1px solid #00f5d4; color:#00f5d4; font-size:12px; font-weight:700; cursor:pointer;">
                            + Follow Operative
                        </button>
                    </div>

                    <p style="color:#94a3b8; font-size:14px; line-height:1.6; margin:0 0 14px;">
                        <?php echo esc_html( $author_bio ); ?>
                    </p>

                    <div style="display:flex; gap:12px; align-items:center; font-size:13px; font-family:var(--font-mono); flex-wrap:wrap;">
                        <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" style="color:#00f5d4; text-decoration:none; font-weight:700;">
                            View all articles by <?php echo esc_html( $author_name ); ?> ➔
                        </a>
                        <?php if ( ! empty( $author_github ) ) : ?>
                            <a href="https://github.com/<?php echo esc_attr( $author_github ); ?>" target="_blank" rel="noopener noreferrer" style="color:#cbd5e1; text-decoration:none;">GitHub</a>
                        <?php endif; ?>
                        <?php if ( ! empty( $author_twitter ) ) : ?>
                            <a href="https://twitter.com/<?php echo esc_attr( $author_twitter ); ?>" target="_blank" rel="noopener noreferrer" style="color:#cbd5e1; text-decoration:none;">Twitter</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 11. Related Articles Recommendation Grid -->
            <?php
            if ( $enable_related ) :
                $related_query_args = array(
                    'post__not_in'        => array( $post_id ),
                    'posts_per_page'      => $related_count,
                    'post_status'         => 'publish',
                    'ignore_sticky_posts' => true,
                    'no_found_rows'       => true,
                );
                if ( $primary_cat ) {
                    $related_query_args['cat'] = $primary_cat->term_id;
                }
                $related_query = new \WP_Query( $related_query_args );

                if ( $related_query->have_posts() ) :
            ?>
            <section class="hs-related-articles-section" style="margin-top:48px; padding-top:32px; border-top:1px solid rgba(255,255,255,0.08);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                    <div>
                        <div style="font-size:11px; font-weight:800; color:#00f5d4; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Contextual Deep Dive</div>
                        <h2 style="font-size:1.4rem; font-weight:800; color:#ffffff; margin:0;">Related Guides &amp; Tutorials</h2>
                    </div>
                    <?php if ( $primary_cat ) : ?>
                        <a href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>" style="font-size:12px; color:#00f5d4; font-weight:700; text-decoration:none;">
                            Browse category archive ➔
                        </a>
                    <?php endif; ?>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(min(100%, 300px), 1fr)); gap:20px;">
                    <?php while ( $related_query->have_posts() ) : $related_query->the_post();
                        $r_id      = get_the_ID();
                        $r_words   = str_word_count( wp_strip_all_tags( get_the_content() ) );
                        $r_time    = max( 1, (int) round( $r_words / 200 ) );
                        $r_likes   = (int) get_post_meta( $r_id, 'hs_likes_count', true );
                    ?>
                        <article class="hs-related-card" style="background:#070b16; border:1px solid rgba(255,255,255,0.08); border-radius:16px; overflow:hidden; display:flex; flex-direction:column; justify-content:space-between; transition:transform 0.2s, border-color 0.2s;">
                            <div>
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>" style="display:block; height:160px; overflow:hidden;">
                                        <?php the_post_thumbnail( 'medium', array( 'style' => 'width:100%; height:100%; object-fit:cover; transition:transform 0.3s;' ) ); ?>
                                    </a>
                                <?php endif; ?>
                                <div style="padding:18px;">
                                    <div style="font-size:11px; color:#94a3b8; font-family:var(--font-mono); margin-bottom:8px;">
                                        ⏱️ <?php echo esc_html( (string) $r_time ); ?> min read · ❤️ <?php echo esc_html( (string) $r_likes ); ?>
                                    </div>
                                    <h3 style="font-size:1.05rem; font-weight:800; color:#ffffff; margin:0 0 8px; line-height:1.4;">
                                        <a href="<?php the_permalink(); ?>" style="color:#ffffff; text-decoration:none;">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                    <p style="color:#94a3b8; font-size:13px; margin:0; line-height:1.5;">
                                        <?php echo esc_html( wp_trim_words( get_the_excerpt(), 14, '...' ) ); ?>
                                    </p>
                                </div>
                            </div>
                            <div style="padding:12px 18px; border-top:1px solid rgba(255,255,255,0.06); display:flex; justify-content:space-between; align-items:center;">
                                <span style="font-size:12px; color:#cbd5e1;"><?php echo esc_html( get_the_author() ); ?></span>
                                <a href="<?php the_permalink(); ?>" style="font-size:12px; color:#00f5d4; font-weight:700; text-decoration:none;">Read ➔</a>
                            </div>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </section>
            <?php endif; endif; ?>

            <!-- 12. Interactive Comments & Discussion -->
            <?php
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
            ?>

        </article>
    </div>
</main>

<!-- 13. Interactive Content Moderation & Reporting Modal -->
<?php if ( $enable_report ) : ?>
<div id="hs-report-modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); backdrop-filter:blur(6px); z-index:99999; align-items:center; justify-content:center; padding:16px;">
    <div style="background:#0b1120; border:1px solid rgba(255,51,102,0.4); border-radius:20px; max-width:480px; width:100%; padding:28px; box-shadow:0 16px 48px rgba(0,0,0,0.8); position:relative;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <div style="display:flex; align-items:center; gap:8px; color:#ff3366; font-size:16px; font-weight:800;">
                <span>⚠️</span> Report Article Content
            </div>
            <button type="button" onclick="closeHsReportModal()" style="background:none; border:none; color:#94a3b8; font-size:20px; cursor:pointer;" aria-label="Close modal">✕</button>
        </div>

        <p style="color:#94a3b8; font-size:13px; margin:0 0 18px; line-height:1.5;">
            Our security and moderation operatives review every reported article to maintain strict technical standards and compliance.
        </p>

        <form id="hs-report-form" onsubmit="submitHsReport(event, <?php echo $post_id; ?>)">
            <div style="margin-bottom:16px;">
                <label style="display:block; color:#cbd5e1; font-size:12px; font-weight:700; margin-bottom:8px;">Reason for Report:</label>
                <div style="display:flex; flex-direction:column; gap:8px; font-size:13px; color:#f8fafc;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="radio" name="hs_report_reason" value="Spam or Commercial Solicitation" checked />
                        Spam or commercial advertising
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="radio" name="hs_report_reason" value="False or Inaccurate Technical Info" />
                        False or inaccurate technical instructions
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="radio" name="hs_report_reason" value="Broken Code or Security Vulnerability" />
                        Broken code or hazardous exploit code
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="radio" name="hs_report_reason" value="Plagiarism or Copyright Infringement" />
                        Plagiarism or unauthorized copy
                    </label>
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label for="hs-report-details" style="display:block; color:#cbd5e1; font-size:12px; font-weight:700; margin-bottom:6px;">Additional Context (Optional):</label>
                <textarea id="hs-report-details" rows="3" placeholder="Provide additional details or link to original source..." style="width:100%; background:#060a15; border:1px solid rgba(255,255,255,0.1); border-radius:10px; color:#fff; padding:10px; font-size:13px; resize:vertical; font-family:inherit;"></textarea>
            </div>

            <div id="hs-report-feedback" style="display:none; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:16px;"></div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeHsReportModal()" style="padding:8px 18px; border-radius:8px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#cbd5e1; font-size:13px; cursor:pointer;">
                    Cancel
                </button>
                <button type="submit" id="hs-report-submit-btn" style="padding:8px 20px; border-radius:8px; background:#ff3366; border:none; color:#ffffff; font-size:13px; font-weight:800; cursor:pointer;">
                    Submit Report
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- 14. Single Article Client-Side Engine (ToC, Code Copy, Share, Follow) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Enrich Code Blocks with Terminal Bar & 1-Click Copy
    var codeBlocks = document.querySelectorAll('.hs-article-body pre');
    codeBlocks.forEach(function(pre, idx) {
        var code = pre.querySelector('code');
        var codeText = code ? code.innerText : pre.innerText;
        
        // Detect language class (e.g. language-python, language-php, etc.)
        var lang = 'CODE';
        if (code && code.className) {
            var match = code.className.match(/language-([a-z0-9]+)/i);
            if (match) lang = match[1].toUpperCase();
        }

        var wrapper = document.createElement('div');
        wrapper.className = 'hs-code-block-wrapper';
        wrapper.style.cssText = 'position:relative; margin:24px 0; border-radius:14px; overflow:hidden; border:1px solid rgba(0,245,212,0.25); background:#050811; box-shadow:0 8px 24px rgba(0,0,0,0.5);';

        var header = document.createElement('div');
        header.className = 'hs-code-header';
        header.style.cssText = 'display:flex; justify-content:space-between; align-items:center; background:#0a0f1d; padding:8px 16px; border-bottom:1px solid rgba(255,255,255,0.06); font-family:var(--font-mono); font-size:12px; color:#94a3b8; user-select:none;';
        
        header.innerHTML = '<div style="display:flex; align-items:center; gap:8px;">' +
            '<span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#ff5f56;"></span>' +
            '<span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#ffbd2e;"></span>' +
            '<span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#27c93f;"></span>' +
            '<span style="margin-left:6px; color:#00f5d4; font-weight:700;">' + lang + '</span>' +
            '</div>';

        var copyBtn = document.createElement('button');
        copyBtn.type = 'button';
        copyBtn.className = 'hs-code-copy-btn';
        copyBtn.innerHTML = '📋 Copy Code';
        copyBtn.style.cssText = 'background:rgba(0,245,212,0.1); border:1px solid rgba(0,245,212,0.3); color:#00f5d4; font-size:11px; font-weight:700; padding:3px 10px; border-radius:6px; cursor:pointer; font-family:inherit; transition:background 0.15s;';
        
        copyBtn.addEventListener('click', function() {
            navigator.clipboard.writeText(codeText).then(function() {
                copyBtn.innerHTML = '✓ Copied!';
                copyBtn.style.background = '#00f5d4';
                copyBtn.style.color = '#050811';
                setTimeout(function() {
                    copyBtn.innerHTML = '📋 Copy Code';
                    copyBtn.style.background = 'rgba(0,245,212,0.1)';
                    copyBtn.style.color = '#00f5d4';
                }, 2200);
            });
        });

        header.appendChild(copyBtn);
        pre.parentNode.insertBefore(wrapper, pre);
        wrapper.appendChild(header);
        wrapper.appendChild(pre);
        pre.style.margin = '0';
        pre.style.padding = '18px';
        pre.style.background = 'transparent';
    });

    // 2. Dynamic Table of Contents (ToC)
    var headings = document.querySelectorAll('.hs-article-body h2, .hs-article-body h3');
    var tocContainer = document.getElementById('hs-toc-container');
    var tocList = document.getElementById('hs-toc-list');
    
    if (headings.length >= 2 && tocContainer && tocList) {
        var ol = document.createElement('ol');
        ol.style.cssText = 'margin:0; padding-left:20px;';

        headings.forEach(function(h, i) {
            var id = h.id || ('hs-section-' + (i + 1));
            h.id = id;

            var li = document.createElement('li');
            li.style.cssText = (h.tagName.toLowerCase() === 'h3') ? 'margin-left:16px; margin-top:4px;' : 'margin-top:6px; font-weight:600;';

            var a = document.createElement('a');
            a.href = '#' + id;
            a.textContent = h.innerText.replace(/[#¶]/g, '').trim();
            a.style.cssText = 'color:#cbd5e1; text-decoration:none; transition:color 0.15s;';
            a.addEventListener('mouseenter', function() { a.style.color = '#00f5d4'; });
            a.addEventListener('mouseleave', function() { a.style.color = '#cbd5e1'; });

            li.appendChild(a);
            ol.appendChild(li);
        });

        tocList.appendChild(ol);
        tocContainer.style.display = 'block';
    }

    // 3. Interactive Bookmark Button AJAX Handler
    var bmarkBtn = document.querySelector('.hs-bookmark-btn');
    if (bmarkBtn) {
        bmarkBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var pid = this.dataset.postId;
            var txt = this.querySelector('.hs-bookmark-txt');
            var btn = this;

            var fd = new FormData();
            fd.append('action', 'hs_bookmark_post');
            fd.append('post_id', pid);
            fd.append('nonce', '<?php echo esc_js( $hs_ajax_nonce ); ?>');

            fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
                method: 'POST',
                body: fd
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res && res.success) {
                    var isB = res.data && res.data.is_bookmarked;
                    btn.classList.toggle('is-bookmarked', isB);
                    if (txt) txt.textContent = isB ? 'Saved' : 'Bookmark';
                }
            })
            .catch(function() {});
        });
    }
});

// Table of Contents Toggle
function toggleHsToc() {
    var list = document.getElementById('hs-toc-list');
    var icon = document.getElementById('hs-toc-toggle-icon');
    if (!list) return;
    var isHidden = list.style.display === 'none';
    list.style.display = isHidden ? 'block' : 'none';
    if (icon) icon.textContent = isHidden ? '[ Collapse − ]' : '[ Expand + ]';
}

// Share Dropdown Toggle & Outside Click
function toggleHsShareMenu(e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    var dd = document.getElementById('hs-share-dropdown');
    if (dd) {
        dd.style.display = dd.style.display === 'block' ? 'none' : 'block';
    }
}

document.addEventListener('click', function(e) {
    var dd = document.getElementById('hs-share-dropdown');
    if (dd && !e.target.closest('.hs-share-menu-wrap')) {
        dd.style.display = 'none';
    }
});

// Copy Post Link Helper
function copyHsPostLink(e, url) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    navigator.clipboard.writeText(url).then(function() {
        var txt = document.getElementById('hs-copy-link-txt');
        if (txt) txt.textContent = '✓ Link Copied!';
        setTimeout(function() {
            if (txt) txt.textContent = 'Copy Link';
            var dd = document.getElementById('hs-share-dropdown');
            if (dd) dd.style.display = 'none';
        }, 1800);
    });
}

// Report Modal Handlers
function openHsReportModal() {
    var m = document.getElementById('hs-report-modal-backdrop');
    if (m) {
        m.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeHsReportModal() {
    var m = document.getElementById('hs-report-modal-backdrop');
    if (m) {
        m.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function submitHsReport(e, postId) {
    e.preventDefault();
    var sel = document.querySelector('input[name="hs_report_reason"]:checked');
    var reason = sel ? sel.value : 'General Report';
    var details = document.getElementById('hs-report-details').value;
    var fb = document.getElementById('hs-report-feedback');
    var btn = document.getElementById('hs-report-submit-btn');

    if (btn) btn.disabled = true;

    var fd = new FormData();
    fd.append('action', 'hs_report_post');
    fd.append('post_id', postId);
    fd.append('reason', reason);
    fd.append('details', details);
    fd.append('nonce', '<?php echo esc_js( $hs_ajax_nonce ); ?>');

    fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
        method: 'POST',
        body: fd
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (fb) {
            fb.style.display = 'block';
            if (res && res.success) {
                fb.style.background = 'rgba(16,185,129,0.15)';
                fb.style.border = '1px solid #10b981';
                fb.style.color = '#10b981';
                fb.textContent = res.data && res.data.message ? res.data.message : 'Report submitted successfully.';
                setTimeout(function() { closeHsReportModal(); }, 2400);
            } else {
                fb.style.background = 'rgba(255,51,102,0.15)';
                fb.style.border = '1px solid #ff3366';
                fb.style.color = '#ff3366';
                fb.textContent = res && res.data && res.data.message ? res.data.message : 'Failed to submit report.';
                if (btn) btn.disabled = false;
            }
        }
    })
    .catch(function() {
        if (btn) btn.disabled = false;
    });
}

// Author Follow Toggle
function toggleAuthorFollow(e, authorId) {
    if (e) e.preventDefault();
    var btn = document.getElementById('hs-follow-author-btn');
    if (!btn) return;

    var fd = new FormData();
    fd.append('action', 'hs_toggle_follow');
    fd.append('target_user_id', authorId);
    fd.append('nonce', '<?php echo esc_js( $hs_ajax_nonce ); ?>');

    fetch('/wp-json/hackersshikkhok/v1/user/follow', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>'
        },
        body: JSON.stringify({ target_user_id: authorId })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res && res.success) {
            btn.textContent = res.followed ? '✓ Following Operative' : '+ Follow Operative';
            btn.style.background = res.followed ? '#00f5d4' : 'rgba(0,245,212,0.12)';
            btn.style.color = res.followed ? '#050811' : '#00f5d4';
        }
    })
    .catch(function() {});
}
</script>

<?php
endwhile;

get_footer();

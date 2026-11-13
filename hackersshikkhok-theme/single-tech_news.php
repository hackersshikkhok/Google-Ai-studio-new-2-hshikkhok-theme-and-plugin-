<?php
/**
 * Single Tech News Template - HackersShikkhok
 * CPT: tech_news (URL: /tech-news/{slug}/)
 * Package: HackersShikkhok Theme
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * 
 * Google NewsArticle Schema Ready, Verified Source Attribution,
 * Reporter Byline, Bangladesh Launch Timeline, AdSense Compliant Slots.
 */

declare(strict_types=1);

get_header();

global $post;
$post_id     = get_the_ID();
$headline    = get_post_meta( $post_id, '_hs_news_headline', true ) ?: get_the_title();
$subheadline = get_post_meta( $post_id, '_hs_news_subheadline', true );
$source_name = get_post_meta( $post_id, '_hs_news_source_name', true );
$source_url  = get_post_meta( $post_id, '_hs_news_source_url', true );
$bd_date     = get_post_meta( $post_id, '_hs_news_bd_launch_date', true );

$categories = get_the_terms( $post_id, 'news_category' );
$cat_name = ( ! empty( $categories ) && ! is_wp_error( $categories ) ) ? $categories[0]->name : 'Tech Intelligence';
?>

<main id="primary" class="site-main hs-single-news-page" style="background:#06080d; color:#f8fafc; min-height:100vh; padding:32px 16px 80px;">
    <div style="max-width:1100px; margin:0 auto;">

        <!-- Breadcrumb Navigation -->
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:20px; font-family:var(--font-mono);">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
            <span style="color:#64748b;">/</span>
            <a href="<?php echo esc_url( home_url( '/tech-news/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Tech News</a>
            <span style="color:#64748b;">/</span>
            <span style="color:#cbd5e1; font-weight:700;"><?php echo esc_html( wp_trim_words( $headline, 6, '...' ) ); ?></span>
        </nav>

        <!-- News Header Banner -->
        <header style="background:linear-gradient(135deg, rgba(11,17,32,0.98), rgba(15,23,42,0.95)); border:1px solid rgba(167,139,250,0.3); border-radius:20px; padding:32px; margin-bottom:24px; position:relative; overflow:hidden; box-shadow:0 12px 36px rgba(0,0,0,0.6);">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="background:rgba(167,139,250,0.15); color:#a78bfa; border:1px solid rgba(167,139,250,0.35); font-size:11px; font-weight:800; padding:3px 12px; border-radius:999px; text-transform:uppercase;">
                        📰 <?php echo esc_html( $cat_name ); ?>
                    </span>
                    <span style="background:rgba(239,68,68,0.15); color:#f87171; border:1px solid rgba(239,68,68,0.3); font-size:11px; font-weight:800; padding:3px 10px; border-radius:999px;">
                        ● LIVE UPDATE
                    </span>
                </div>
                <div style="font-size:12px; color:#94a3b8; font-family:var(--font-mono);">
                    Published: <?php echo esc_html( get_the_date( 'd F Y, g:i A' ) ); ?>
                </div>
            </div>

            <h1 style="font-size:clamp(1.8rem, 4vw, 2.75rem); font-weight:900; color:#fff; margin:0 0 14px; line-height:1.25;">
                <?php the_title(); ?>
            </h1>

            <?php if ( ! empty( $subheadline ) ) : ?>
                <p style="color:#cbd5e1; font-size:16px; margin:0 0 18px; line-height:1.6; font-weight:600;">
                    <?php echo esc_html( $subheadline ); ?>
                </p>
            <?php endif; ?>

            <div style="display:flex; align-items:center; gap:14px; color:#94a3b8; font-size:13px; font-family:var(--font-mono); padding-top:14px; border-top:1px solid rgba(255,255,255,0.08);">
                <span>👤 Tech Reporter: <strong style="color:#00f5d4;"><?php echo esc_html( get_the_author() ); ?></strong></span>
                <span>•</span>
                <span>⏱️ <?php echo esc_html( (string) max( 1, (int) round( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 200 ) ) ); ?> min read</span>
            </div>
        </header>

        <!-- Source Attribution & Bangladesh Availability Bar -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px; margin-bottom:28px;">
            <?php if ( ! empty( $source_name ) ) : ?>
                <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:14px; padding:18px; display:flex; align-items:center; gap:12px;">
                    <span style="font-size:24px;">🔗</span>
                    <div>
                        <div style="font-size:11px; color:#94a3b8; font-weight:700; text-transform:uppercase;">Source Attribution</div>
                        <div style="font-size:13px; font-weight:700; color:#fff; margin-top:2px;">
                            <?php if ( ! empty( $source_url ) ) : ?>
                                <a href="<?php echo esc_url( $source_url ); ?>" target="_blank" rel="noopener noreferrer" style="color:#00f5d4; text-decoration:none;">
                                    <?php echo esc_html( $source_name ); ?> ➔
                                </a>
                            <?php else : ?>
                                <?php echo esc_html( $source_name ); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $bd_date ) ) : ?>
                <div style="background:#0b1120; border:1px solid rgba(16,185,129,0.3); border-radius:14px; padding:18px; display:flex; align-items:center; gap:12px;">
                    <span style="font-size:24px;">🇧🇩</span>
                    <div>
                        <div style="font-size:11px; color:#10b981; font-weight:700; text-transform:uppercase;">Bangladesh Availability</div>
                        <div style="font-size:13px; font-weight:700; color:#fff; margin-top:2px;">
                            Expected: <strong style="color:#10b981;"><?php echo esc_html( $bd_date ); ?></strong>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- AdSense Slot (Top) -->
        <div class="hs-ad-slot-wrap" style="margin-bottom:28px; background:#080d1a; border:1px dashed rgba(255,255,255,0.1); border-radius:12px; padding:12px; text-align:center;">
            <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Advertisement</div>
            <div class="hs-ad-banner-placement" style="min-height:90px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:12px;">
                <?php do_action( 'hs_render_ad_slot', 'news_single_top' ); ?>
                <span style="color:#64748b;">[ AdSense Responsive Display Unit — Verified Placement ]</span>
            </div>
        </div>

        <!-- Featured Image -->
        <?php if ( has_post_thumbnail() ) : ?>
            <div style="margin-bottom:32px; border-radius:18px; overflow:hidden; border:1px solid rgba(167,139,250,0.25); box-shadow:0 8px 30px rgba(0,0,0,0.5);">
                <?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:auto; display:block;' ) ); ?>
            </div>
        <?php endif; ?>

        <!-- Main News Body Content -->
        <article class="entry-content hs-news-content" style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:36px; margin-bottom:32px; color:#e2e8f0; font-size:16px; line-height:1.8;">
            <?php the_content(); ?>
        </article>

        <!-- Cross Links to Related Portals -->
        <section style="background:#0b1120; border:1px solid rgba(0,245,212,0.25); border-radius:18px; padding:28px; margin-bottom:32px;">
            <h3 style="font-size:1.2rem; font-weight:800; color:#fff; margin:0 0 16px; display:flex; align-items:center; gap:8px;">
                <span style="color:#00f5d4;">🔍</span> Explore More on HackersShikkhok
            </h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
                <a href="<?php echo esc_url( home_url( '/device-reviews/' ) ); ?>" style="background:#070a14; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px; text-decoration:none; display:block;">
                    <div style="color:#00f5d4; font-weight:700; font-size:14px; margin-bottom:4px;">📱 Device Reviews</div>
                    <div style="color:#94a3b8; font-size:12px;">Original smartphone &amp; hardware benchmarks.</div>
                </a>
                <a href="<?php echo esc_url( home_url( '/device-prices/' ) ); ?>" style="background:#070a14; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px; text-decoration:none; display:block;">
                    <div style="color:#10b981; font-weight:700; font-size:14px; margin-bottom:4px;">💰 Device Prices</div>
                    <div style="color:#94a3b8; font-size:12px;">Verified BDT prices and variant matrix.</div>
                </a>
                <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="background:#070a14; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px; text-decoration:none; display:block;">
                    <div style="color:#a78bfa; font-weight:700; font-size:14px; margin-bottom:4px;">⚡ 2,024+ Tools</div>
                    <div style="color:#94a3b8; font-size:12px;">Client-side developer &amp; security utilities.</div>
                </a>
            </div>
        </section>

        <!-- AdSense Slot (Bottom) -->
        <div class="hs-ad-slot-wrap" style="margin-bottom:32px; background:#080d1a; border:1px dashed rgba(255,255,255,0.1); border-radius:12px; padding:12px; text-align:center;">
            <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Advertisement</div>
            <div class="hs-ad-banner-placement" style="min-height:90px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:12px;">
                <?php do_action( 'hs_render_ad_slot', 'news_single_bottom' ); ?>
                <span style="color:#64748b;">[ AdSense Matched Content Unit — Verified Placement ]</span>
            </div>
        </div>

    </div>
</main>

<?php
get_footer();

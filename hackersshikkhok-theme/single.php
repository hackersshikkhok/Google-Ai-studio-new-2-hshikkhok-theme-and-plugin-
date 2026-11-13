<?php
/**
 * Single Post Template - HackersShikkhok
 * Theme: HackersShikkhok 2040 Cyber & Coding Hub Theme
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */

declare(strict_types=1);

get_header();
?>

<main id="primary" class="site-main hs-page-container" style="max-width:1100px; margin:0 auto; padding:40px 16px;">
    <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'hs-page-card' ); ?> style="background:#0b1120; border:1px solid rgba(0,245,212,0.22); border-radius:20px; padding:36px; box-shadow:0 12px 36px rgba(0,0,0,0.5);">
            
            <!-- Breadcrumb Navigation -->
            <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="font-size:13px; color:#94a3b8; margin-bottom:20px; display:flex; align-items:center; gap:8px; font-family:var(--font-mono);">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
                <span style="color:#64748b;">/</span>
                <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Articles &amp; Guides</a>
                <span style="color:#64748b;">/</span>
                <span style="color:#cbd5e1; font-weight:700;"><?php the_title(); ?></span>
            </nav>

            <!-- Article Header -->
            <header class="hs-article-header" style="margin-bottom:28px;">
                <?php
                $categories = get_the_category();
                if ( ! empty( $categories ) ) :
                ?>
                    <span class="hs-card-badge" style="display:inline-block; background:rgba(0,245,212,0.12); color:#00f5d4; border:1px solid rgba(0,245,212,0.35); font-size:11px; font-weight:800; padding:3px 12px; border-radius:999px; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">
                        <?php echo esc_html( $categories[0]->name ); ?>
                    </span>
                <?php endif; ?>

                <h1 style="color:#ffffff; font-size:clamp(1.75rem, 4vw, 2.75rem); font-weight:900; line-height:1.25; margin:12px 0 16px;">
                    <?php the_title(); ?>
                </h1>

                <div class="hs-article-meta" style="display:flex; flex-wrap:wrap; align-items:center; gap:12px; color:#94a3b8; font-size:13px; font-family:var(--font-mono);">
                    <span>👤 Author: <strong style="color:#f8fafc;"><?php echo esc_html( get_the_author() ); ?></strong></span>
                    <span>•</span>
                    <span>📅 Published: <?php echo esc_html( get_the_date( 'd F Y' ) ); ?></span>
                    <span>•</span>
                    <span>⏱️ Reading Time: <?php echo esc_html( (string) max( 1, (int) round( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 200 ) ) ); ?> mins</span>
                </div>
            </header>

            <!-- Featured Thumbnail -->
            <?php if ( has_post_thumbnail() ) : ?>
                <div style="margin-bottom:32px; border-radius:16px; overflow:hidden; border:1px solid rgba(0,245,212,0.25); box-shadow:0 8px 30px rgba(0,0,0,0.5);">
                    <?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:auto; display:block;' ) ); ?>
                </div>
            <?php endif; ?>

            <!-- AdSense Top Leaderboard -->
            <div class="hs-ad-slot-wrap" style="margin-bottom:28px; background:#080d1a; border:1px dashed rgba(255,255,255,0.1); border-radius:12px; padding:12px; text-align:center;">
                <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Advertisement</div>
                <div class="hs-ad-banner-placement" style="min-height:90px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:12px;">
                    <?php do_action( 'hs_render_ad_slot', 'single_article_top' ); ?>
                    <span style="color:#64748b;">[ AdSense Responsive Display Unit — Verified Placement ]</span>
                </div>
            </div>

            <!-- Article Content -->
            <div class="entry-content" style="color:#e2e8f0; font-size:16px; line-height:1.8;">
                <?php
                the_content();

                wp_link_pages( array(
                    'before' => '<div class="page-links" style="margin-top:24px;">' . esc_html__( 'Pages:', 'hackersshikkhok-theme' ),
                    'after'  => '</div>',
                ) );
                ?>
            </div>

            <!-- Tags -->
            <?php
            $tags = get_the_tags();
            if ( ! empty( $tags ) ) :
            ?>
                <div style="margin-top:36px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.08); display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
                    <span style="font-size:13px; color:#94a3b8; font-weight:700;">🏷️ Tags:</span>
                    <?php foreach ( $tags as $tag ) : ?>
                        <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" style="font-size:12px; background:rgba(255,255,255,0.05); color:#cbd5e1; padding:4px 12px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); text-decoration:none;">
                            #<?php echo esc_html( $tag->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Author Box -->
            <div style="margin-top:40px; padding:24px; background:#070a14; border:1px solid rgba(0,245,212,0.2); border-radius:16px; display:flex; gap:18px; align-items:center;">
                <div style="width:60px; height:60px; border-radius:50%; background:linear-gradient(135deg, #00f5d4 0%, #7b2cbf 100%); color:#050811; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:800; flex-shrink:0;">
                    <?php echo esc_html( mb_substr( get_the_author(), 0, 1 ) ); ?>
                </div>
                <div>
                    <div style="color:#00f5d4; font-weight:800; font-size:15px;">Author: <?php echo esc_html( get_the_author() ); ?></div>
                    <div style="color:#94a3b8; font-size:13px; margin-top:4px; line-height:1.5;">
                        Cybersecurity researcher, senior full-stack engineer and author at HackersShikkhok — empowering the next generation of ethical hackers and programmers.
                    </div>
                </div>
            </div>

            <!-- Comments Area -->
            <?php
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
            ?>
        </article>
    <?php endwhile; ?>
</main>

<?php
get_footer();

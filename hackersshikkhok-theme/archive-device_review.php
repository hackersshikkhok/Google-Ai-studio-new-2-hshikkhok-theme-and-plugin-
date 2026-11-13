<?php
/**
 * Archive Device Review Template - HackersShikkhok
 * URL: /device-reviews/
 * Package: HackersShikkhok Theme
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */

declare(strict_types=1);

get_header();

$brands = get_terms( array(
    'taxonomy'   => 'device_brand',
    'hide_empty' => false,
) );
?>

<main id="primary" class="site-main hs-archive-reviews-page" style="background:#06080d; color:#f8fafc; min-height:100vh; padding:40px 16px 80px;">
    <div style="max-width:1280px; margin:0 auto;">

        <!-- Breadcrumbs -->
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:20px; font-family:var(--font-mono);">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
            <span style="color:#64748b;">/</span>
            <span style="color:#cbd5e1; font-weight:700;">Device Reviews</span>
        </nav>

        <!-- Archive Header -->
        <header style="background:linear-gradient(135deg, rgba(11,17,32,0.98), rgba(15,23,42,0.95)); border:1px solid rgba(0,245,212,0.3); border-radius:20px; padding:36px; margin-bottom:32px; text-align:center; position:relative; overflow:hidden;">
            <div style="display:inline-block; padding:4px 16px; border-radius:999px; background:rgba(0,245,212,0.1); border:1px solid rgba(0,245,212,0.3); color:#00f5d4; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">
                📱 EXPERT HARDWARE TESTING &amp; BENCHMARKS
            </div>
            <h1 style="font-size:clamp(1.8rem, 4vw, 2.75rem); font-weight:900; color:#fff; margin:0 0 12px;">
                Smartphones, Laptops &amp; Cyber Device Reviews
            </h1>
            <p style="color:#94a3b8; font-size:14px; max-width:760px; margin:0 auto 20px; line-height:1.6;">
                Unbiased, hands-on reviews of modern consumer electronics, developer workstations, and security hardware. Real benchmarks, camera samples, battery tests, pros and cons.
            </p>
        </header>

        <!-- Brand Filter Chips -->
        <?php if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) : ?>
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:28px;">
                <span style="font-size:12px; font-weight:700; color:#94a3b8; display:flex; align-items:center; margin-right:4px;">Filter by Brand:</span>
                <a href="<?php echo esc_url( home_url( '/device-reviews/' ) ); ?>" style="padding:6px 14px; border-radius:8px; background:rgba(0,245,212,0.15); border:1px solid #00f5d4; color:#00f5d4; font-size:12px; font-weight:700; text-decoration:none;">All Brands</a>
                <?php foreach ( $brands as $b ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $b ) ); ?>" style="padding:6px 14px; border-radius:8px; background:#0b1120; border:1px solid rgba(255,255,255,0.08); color:#cbd5e1; font-size:12px; font-weight:600; text-decoration:none;">
                        <?php echo esc_html( $b->name ); ?> (<?php echo (int) $b->count; ?>)
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Reviews Grid -->
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:22px; margin-bottom:40px;">
            <?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); 
                    $p_id = get_the_ID();
                    $dev_name = get_post_meta( $p_id, '_hs_device_name', true ) ?: get_the_title();
                    $score    = get_post_meta( $p_id, '_hs_score_overall', true ) ?: '8.8';
                    $verdict  = get_post_meta( $p_id, '_hs_review_verdict', true ) ?: wp_trim_words( get_the_excerpt(), 18 );
                ?>
                    <article style="background:#0b1120; border:1px solid rgba(0,245,212,0.2); border-radius:18px; padding:24px; display:flex; flex-direction:column; justify-content:space-between; box-shadow:0 8px 24px rgba(0,0,0,0.4); transition:transform 0.2s, border-color 0.2s;" class="hs-tool-box-card">
                        <div>
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div style="margin:-24px -24px 18px -24px; height:180px; overflow:hidden; border-top-left-radius:18px; border-top-right-radius:18px;">
                                    <?php the_post_thumbnail( 'medium_large', array( 'style' => 'width:100%; height:100%; object-fit:cover;' ) ); ?>
                                </div>
                            <?php endif; ?>

                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <span style="font-size:11px; font-weight:800; color:#00f5d4; background:rgba(0,245,212,0.1); border:1px solid rgba(0,245,212,0.3); padding:2px 8px; border-radius:6px;">
                                    REVIEW
                                </span>
                                <span style="font-size:12px; font-weight:900; color:#00f5d4; font-family:var(--font-mono); background:#06080d; padding:2px 8px; border-radius:6px; border:1px solid rgba(0,245,212,0.25);">
                                    ⭐ <?php echo esc_html( (string) $score ); ?>/10
                                </span>
                            </div>

                            <h2 style="font-size:1.15rem; font-weight:800; color:#fff; margin:0 0 8px; line-height:1.3;">
                                <a href="<?php the_permalink(); ?>" style="color:#fff; text-decoration:none;">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <p style="color:#94a3b8; font-size:13px; line-height:1.5; margin:0 0 16px;">
                                <?php echo esc_html( $verdict ); ?>
                            </p>
                        </div>

                        <div style="padding-top:14px; border-top:1px solid rgba(255,255,255,0.06); display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:11px; color:#64748b;"><?php echo esc_html( get_the_date( 'M Y' ) ); ?></span>
                            <a href="<?php the_permalink(); ?>" style="font-size:12px; font-weight:800; color:#00f5d4; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                Read Full Review ➔
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <div style="grid-column:1/-1; background:#0b1120; border:1px dashed rgba(255,255,255,0.1); border-radius:14px; padding:32px; text-align:center; color:#94a3b8;">
                    <p style="font-size:15px; margin:0 0 12px;">No published device reviews found yet.</p>
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=device_review' ) ); ?>" style="color:#00f5d4; font-weight:700;">+ Publish First Review in Dashboard</a>
                </div>
            <?php endif; ?>
        </div>

        <?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>

    </div>
</main>

<?php
get_footer();

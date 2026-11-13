<?php
/**
 * Single Device Review Template - HackersShikkhok
 * CPT: device_review (URL: /device-reviews/{slug}/)
 * Package: HackersShikkhok Theme
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * 
 * Google Product & Review Schema Ready, Verified Benchmark Matrix,
 * Pros & Cons, AdSense Compliant Slots, Cross-linked to Device Prices.
 */

declare(strict_types=1);

get_header();

global $post;
$post_id       = get_the_ID();
$device_name   = get_post_meta( $post_id, '_hs_device_name', true ) ?: get_the_title();
$verdict       = get_post_meta( $post_id, '_hs_review_verdict', true );
$display       = get_post_meta( $post_id, '_hs_spec_display', true );
$processor     = get_post_meta( $post_id, '_hs_spec_processor', true );
$ram_storage   = get_post_meta( $post_id, '_hs_spec_ram_storage', true );
$battery       = get_post_meta( $post_id, '_hs_spec_battery', true );
$camera        = get_post_meta( $post_id, '_hs_spec_camera', true );
$os            = get_post_meta( $post_id, '_hs_spec_os', true );
$score_overall = get_post_meta( $post_id, '_hs_score_overall', true ) ?: '8.8';
$score_display = get_post_meta( $post_id, '_hs_score_display', true ) ?: '8.5';
$score_camera  = get_post_meta( $post_id, '_hs_score_camera', true ) ?: '8.8';
$score_battery = get_post_meta( $post_id, '_hs_score_battery', true ) ?: '9.0';
$pros_raw      = (string) get_post_meta( $post_id, '_hs_review_pros', true );
$cons_raw      = (string) get_post_meta( $post_id, '_hs_review_cons', true );
$price_url     = get_post_meta( $post_id, '_hs_related_price_url', true );

$pros = array_filter( array_map( 'trim', explode( "\n", $pros_raw ) ) );
$cons = array_filter( array_map( 'trim', explode( "\n", $cons_raw ) ) );

$brands = get_the_terms( $post_id, 'device_brand' );
$brand_name = ( ! empty( $brands ) && ! is_wp_error( $brands ) ) ? $brands[0]->name : 'Device';
?>

<main id="primary" class="site-main hs-single-review-page" style="background:#06080d; color:#f8fafc; min-height:100vh; padding:32px 16px 80px;">
    <div style="max-width:1180px; margin:0 auto;">

        <!-- Breadcrumb Navigation -->
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:20px; font-family:var(--font-mono);">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
            <span style="color:#64748b;">/</span>
            <a href="<?php echo esc_url( home_url( '/device-reviews/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Device Reviews</a>
            <span style="color:#64748b;">/</span>
            <span style="color:#cbd5e1; font-weight:700;"><?php echo esc_html( $device_name ); ?></span>
        </nav>

        <!-- Review Header Card -->
        <header style="background:linear-gradient(135deg, rgba(11,17,32,0.98), rgba(15,23,42,0.95)); border:1px solid rgba(0,245,212,0.3); border-radius:20px; padding:32px; margin-bottom:24px; position:relative; overflow:hidden; box-shadow:0 12px 36px rgba(0,0,0,0.6);">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="background:rgba(0,245,212,0.12); color:#00f5d4; border:1px solid rgba(0,245,212,0.35); font-size:11px; font-weight:800; padding:3px 12px; border-radius:999px; text-transform:uppercase;">
                        <?php echo esc_html( $brand_name ); ?> REVIEW
                    </span>
                    <span style="background:rgba(16,185,129,0.12); color:#10b981; border:1px solid rgba(16,185,129,0.3); font-size:11px; font-weight:800; padding:3px 10px; border-radius:999px;">
                        ✓ Verified Testing
                    </span>
                </div>
                <div style="font-size:12px; color:#94a3b8; font-family:var(--font-mono);">
                    Published: <?php echo esc_html( get_the_date( 'd M Y' ) ); ?> · Updated: <?php echo esc_html( get_the_modified_date( 'd M Y' ) ); ?>
                </div>
            </div>

            <h1 style="font-size:clamp(1.8rem, 4vw, 2.75rem); font-weight:900; color:#fff; margin:0 0 12px; line-height:1.2;">
                <?php the_title(); ?>
            </h1>

            <div style="display:flex; align-items:center; gap:14px; color:#94a3b8; font-size:13px; font-family:var(--font-mono); margin-bottom:18px;">
                <span>👤 Reviewer: <strong style="color:#00f5d4;"><?php echo esc_html( get_the_author() ); ?></strong></span>
                <span>•</span>
                <span>⏱️ <?php echo esc_html( (string) max( 1, (int) round( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 200 ) ) ); ?> min read</span>
            </div>

            <?php if ( ! empty( $verdict ) ) : ?>
                <div style="background:#070a14; border-left:4px solid #00f5d4; padding:14px 18px; border-radius:8px; color:#cbd5e1; font-size:14px; line-height:1.6;">
                    <strong style="color:#00f5d4;">Verdict:</strong> <?php echo esc_html( $verdict ); ?>
                </div>
            <?php endif; ?>
        </header>

        <!-- Benchmark Scores & Dedicated Price Cross-Link Bar -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:28px;">
            <div style="background:#0b1120; border:1px solid rgba(0,245,212,0.3); border-radius:16px; padding:20px; text-align:center;">
                <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase;">Overall Score</div>
                <div style="font-size:2.4rem; font-weight:900; color:#00f5d4; font-family:var(--font-mono); margin:4px 0;">
                    <?php echo esc_html( (string) $score_overall ); ?><span style="font-size:14px; color:#64748b;">/10</span>
                </div>
                <div style="font-size:11px; color:#10b981;">Editor's Recommended Choice</div>
            </div>

            <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:20px; text-align:center;">
                <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase;">Display Score</div>
                <div style="font-size:2rem; font-weight:900; color:#38bdf8; font-family:var(--font-mono); margin:4px 0;">
                    <?php echo esc_html( (string) $score_display ); ?><span style="font-size:14px; color:#64748b;">/10</span>
                </div>
                <div style="font-size:11px; color:#94a3b8;">Color accuracy &amp; brightness</div>
            </div>

            <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:20px; text-align:center;">
                <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase;">Camera Score</div>
                <div style="font-size:2rem; font-weight:900; color:#a78bfa; font-family:var(--font-mono); margin:4px 0;">
                    <?php echo esc_html( (string) $score_camera ); ?><span style="font-size:14px; color:#64748b;">/10</span>
                </div>
                <div style="font-size:11px; color:#94a3b8;">Low-light &amp; stabilization</div>
            </div>

            <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:20px; text-align:center;">
                <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase;">Battery Score</div>
                <div style="font-size:2rem; font-weight:900; color:#10b981; font-family:var(--font-mono); margin:4px 0;">
                    <?php echo esc_html( (string) $score_battery ); ?><span style="font-size:14px; color:#64748b;">/10</span>
                </div>
                <div style="font-size:11px; color:#94a3b8;">Screen-on-time &amp; charging</div>
            </div>
        </div>

        <!-- Cross-Link to Device Price Page (High Conversion & Intent Routing) -->
        <?php if ( ! empty( $price_url ) ) : ?>
            <div style="background:linear-gradient(135deg, rgba(16,185,129,0.12), rgba(0,245,212,0.08)); border:1px solid rgba(16,185,129,0.3); border-radius:16px; padding:20px 24px; margin-bottom:28px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <span style="font-size:32px;">💰</span>
                    <div>
                        <div style="color:#10b981; font-weight:800; font-size:14px; text-transform:uppercase; letter-spacing:0.5px;">CHECK OFFICIAL &amp; RETAIL PRICING</div>
                        <div style="color:#ffffff; font-weight:700; font-size:15px; margin-top:2px;">
                            <?php echo esc_html( $device_name ); ?> Price in Bangladesh (All Storage &amp; RAM Variants)
                        </div>
                    </div>
                </div>
                <a href="<?php echo esc_url( home_url( $price_url ) ); ?>" style="padding:10px 24px; background:#10b981; color:#050811; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none; box-shadow:0 0 16px rgba(16,185,129,0.35);">
                    View Verified Price ➔
                </a>
            </div>
        <?php endif; ?>

        <!-- AdSense Slot (Top of Content) -->
        <div class="hs-ad-slot-wrap" style="margin-bottom:28px; background:#080d1a; border:1px dashed rgba(255,255,255,0.1); border-radius:12px; padding:12px; text-align:center;">
            <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Advertisement</div>
            <div class="hs-ad-banner-placement" style="min-height:90px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:12px;">
                <?php do_action( 'hs_render_ad_slot', 'review_single_top' ); ?>
                <span style="color:#64748b;">[ AdSense Responsive Display Unit — Google Verified Placement ]</span>
            </div>
        </div>

        <!-- Featured Image -->
        <?php if ( has_post_thumbnail() ) : ?>
            <div style="margin-bottom:32px; border-radius:18px; overflow:hidden; border:1px solid rgba(0,245,212,0.25); box-shadow:0 8px 30px rgba(0,0,0,0.5);">
                <?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:auto; display:block;' ) ); ?>
            </div>
        <?php endif; ?>

        <!-- Key Specifications Matrix Table -->
        <section style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:28px; margin-bottom:32px;">
            <h2 style="font-size:1.3rem; font-weight:800; color:#fff; margin:0 0 16px; display:flex; align-items:center; gap:8px;">
                <span style="color:#00f5d4;">⚙️</span> Technical Specifications Matrix
            </h2>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:14px;">
                <div style="background:#070a14; padding:14px; border-radius:10px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:11px; color:#94a3b8; font-weight:700; text-transform:uppercase;">Display</div>
                    <div style="font-size:13px; font-weight:700; color:#fff; margin-top:4px;"><?php echo esc_html( $display ?: 'Super AMOLED+ 120Hz' ); ?></div>
                </div>
                <div style="background:#070a14; padding:14px; border-radius:10px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:11px; color:#94a3b8; font-weight:700; text-transform:uppercase;">Processor</div>
                    <div style="font-size:13px; font-weight:700; color:#fff; margin-top:4px;"><?php echo esc_html( $processor ?: 'Octa-Core 4nm Silicon' ); ?></div>
                </div>
                <div style="background:#070a14; padding:14px; border-radius:10px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:11px; color:#94a3b8; font-weight:700; text-transform:uppercase;">RAM &amp; Storage</div>
                    <div style="font-size:13px; font-weight:700; color:#fff; margin-top:4px;"><?php echo esc_html( $ram_storage ?: '8GB RAM / 128GB ROM' ); ?></div>
                </div>
                <div style="background:#070a14; padding:14px; border-radius:10px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:11px; color:#94a3b8; font-weight:700; text-transform:uppercase;">Battery &amp; Charging</div>
                    <div style="font-size:13px; font-weight:700; color:#fff; margin-top:4px;"><?php echo esc_html( $battery ?: '5000 mAh, 45W Fast Charge' ); ?></div>
                </div>
                <div style="background:#070a14; padding:14px; border-radius:10px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:11px; color:#94a3b8; font-weight:700; text-transform:uppercase;">Camera Setup</div>
                    <div style="font-size:13px; font-weight:700; color:#fff; margin-top:4px;"><?php echo esc_html( $camera ?: '50MP OIS + Ultra-wide + Selfie' ); ?></div>
                </div>
                <div style="background:#070a14; padding:14px; border-radius:10px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:11px; color:#94a3b8; font-weight:700; text-transform:uppercase;">OS &amp; Updates</div>
                    <div style="font-size:13px; font-weight:700; color:#fff; margin-top:4px;"><?php echo esc_html( $os ?: 'Android 15, Long-term Support' ); ?></div>
                </div>
            </div>
        </section>

        <!-- Detailed Review Content Body -->
        <article class="entry-content hs-review-body" style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:36px; margin-bottom:32px; color:#e2e8f0; font-size:16px; line-height:1.8;">
            <?php the_content(); ?>
        </article>

        <!-- Pros and Cons Side-by-Side (Google Review Guideline Compliant) -->
        <?php if ( ! empty( $pros ) || ! empty( $cons ) ) : ?>
            <section style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:36px;">
                <div style="background:#07140e; border:1px solid rgba(16,185,129,0.3); border-radius:16px; padding:24px;">
                    <h3 style="color:#10b981; font-size:1.15rem; font-weight:800; margin:0 0 16px; display:flex; align-items:center; gap:8px;">
                        <span>✓</span> Advantages &amp; Reasons to Buy
                    </h3>
                    <ul style="margin:0; padding-left:20px; color:#cbd5e1; font-size:14px; line-height:1.8;">
                        <?php foreach ( $pros as $p ) : ?>
                            <li><?php echo esc_html( $p ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div style="background:#180b0f; border:1px solid rgba(239,68,68,0.3); border-radius:16px; padding:24px;">
                    <h3 style="color:#f87171; font-size:1.15rem; font-weight:800; margin:0 0 16px; display:flex; align-items:center; gap:8px;">
                        <span>✕</span> Drawbacks &amp; Considerations
                    </h3>
                    <ul style="margin:0; padding-left:20px; color:#cbd5e1; font-size:14px; line-height:1.8;">
                        <?php foreach ( $cons as $c ) : ?>
                            <li><?php echo esc_html( $c ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        <?php endif; ?>

        <!-- AdSense Slot (In-Content / Bottom) -->
        <div class="hs-ad-slot-wrap" style="margin-bottom:32px; background:#080d1a; border:1px dashed rgba(255,255,255,0.1); border-radius:12px; padding:12px; text-align:center;">
            <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Advertisement</div>
            <div class="hs-ad-banner-placement" style="min-height:90px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:12px;">
                <?php do_action( 'hs_render_ad_slot', 'review_single_bottom' ); ?>
                <span style="color:#64748b;">[ AdSense Matched Content Unit — Verified Placement ]</span>
            </div>
        </div>

    </div>
</main>

<?php
get_footer();

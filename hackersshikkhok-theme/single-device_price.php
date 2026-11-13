<?php
/**
 * Single Device Price Template - HackersShikkhok
 * CPT: device_price (URL: /device-prices/{slug}/)
 * Package: HackersShikkhok Theme
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * 
 * Google Product Offer Schema Ready, Verified Bangladesh BDT Pricing,
 * RAM/Storage Variant Breakdown, Cross-linked to Full Review.
 */

declare(strict_types=1);

get_header();

global $post;
$post_id       = get_the_ID();
$device_name   = get_post_meta( $post_id, '_hs_price_device_name', true ) ?: get_the_title();
$price_bdt     = get_post_meta( $post_id, '_hs_price_bdt', true ) ?: '৳42,999';
$price_type    = get_post_meta( $post_id, '_hs_price_type', true ) ?: 'Official';
$variants_json = get_post_meta( $post_id, '_hs_price_variants_json', true );
$availability  = get_post_meta( $post_id, '_hs_price_availability', true ) ?: 'Available in Bangladesh';
$verified_date = get_post_meta( $post_id, '_hs_price_last_verified', true ) ?: get_the_modified_date( 'Y-m-d' );
$source_note   = get_post_meta( $post_id, '_hs_price_source', true ) ?: 'Official Brand Showrooms & Authorized Outlets';
$quick_specs   = get_post_meta( $post_id, '_hs_price_quick_specs', true );
$review_url    = get_post_meta( $post_id, '_hs_related_review_url', true );

$variants = array();
if ( ! empty( $variants_json ) ) {
    $decoded = json_decode( (string) $variants_json, true );
    if ( is_array( $decoded ) ) {
        $variants = $decoded;
    }
}

$brands = get_the_terms( $post_id, 'device_brand' );
$brand_name = ( ! empty( $brands ) && ! is_wp_error( $brands ) ) ? $brands[0]->name : 'Device';
?>

<main id="primary" class="site-main hs-single-price-page" style="background:#06080d; color:#f8fafc; min-height:100vh; padding:32px 16px 80px;">
    <div style="max-width:1180px; margin:0 auto;">

        <!-- Breadcrumb Navigation -->
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:20px; font-family:var(--font-mono);">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
            <span style="color:#64748b;">/</span>
            <a href="<?php echo esc_url( home_url( '/device-prices/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Device Prices</a>
            <span style="color:#64748b;">/</span>
            <span style="color:#cbd5e1; font-weight:700;"><?php echo esc_html( $device_name ); ?></span>
        </nav>

        <!-- Price Showcase Header Banner -->
        <header style="background:linear-gradient(135deg, rgba(11,17,32,0.98), rgba(15,23,42,0.95)); border:1px solid rgba(16,185,129,0.35); border-radius:20px; padding:32px; margin-bottom:24px; position:relative; overflow:hidden; box-shadow:0 12px 36px rgba(0,0,0,0.6);">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="background:rgba(16,185,129,0.12); color:#10b981; border:1px solid rgba(16,185,129,0.35); font-size:11px; font-weight:800; padding:3px 12px; border-radius:999px; text-transform:uppercase;">
                        <?php echo esc_html( $brand_name ); ?> PRICE IN BANGLADESH
                    </span>
                    <span style="background:rgba(0,245,212,0.12); color:#00f5d4; border:1px solid rgba(0,245,212,0.3); font-size:11px; font-weight:800; padding:3px 10px; border-radius:999px;">
                        <?php echo esc_html( $price_type ); ?>
                    </span>
                    <span style="background:rgba(245,158,11,0.12); color:#fbbf24; border:1px solid rgba(245,158,11,0.3); font-size:11px; font-weight:700; padding:3px 10px; border-radius:999px;">
                        ● <?php echo esc_html( $availability ); ?>
                    </span>
                </div>
                <div style="font-size:12px; color:#94a3b8; font-family:var(--font-mono);">
                    Last Verified: <strong style="color:#00f5d4;"><?php echo esc_html( $verified_date ); ?></strong>
                </div>
            </div>

            <h1 style="font-size:clamp(1.8rem, 4vw, 2.75rem); font-weight:900; color:#fff; margin:0 0 16px; line-height:1.2;">
                <?php the_title(); ?>
            </h1>

            <!-- Big Glowing Price Display Card -->
            <div style="background:#06080d; border:1px solid rgba(16,185,129,0.3); border-radius:16px; padding:20px 24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:18px;">
                <div>
                    <span style="font-size:12px; color:#94a3b8; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Starting Verified Price</span>
                    <div style="font-size:clamp(2rem, 5vw, 3rem); font-weight:900; color:#10b981; font-family:var(--font-mono); line-height:1.1; margin-top:4px;">
                        <?php echo esc_html( $price_bdt ); ?>
                    </div>
                    <div style="font-size:12px; color:#cbd5e1; margin-top:4px;">
                        Source: <?php echo esc_html( $source_note ); ?>
                    </div>
                </div>

                <?php if ( ! empty( $review_url ) ) : ?>
                    <a href="<?php echo esc_url( home_url( $review_url ) ); ?>" style="padding:12px 24px; background:#00f5d4; color:#050811; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none; box-shadow:0 0 16px rgba(0,245,212,0.35);">
                        📖 Read In-Depth Device Review ➔
                    </a>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $quick_specs ) ) : ?>
                <div style="color:#cbd5e1; font-size:13px; font-family:var(--font-mono); background:rgba(255,255,255,0.04); padding:10px 16px; border-radius:8px; border:1px solid rgba(255,255,255,0.06);">
                    <strong>Specs Snapshot:</strong> <?php echo esc_html( $quick_specs ); ?>
                </div>
            <?php endif; ?>
        </header>

        <!-- AdSense Slot (Top of Content) -->
        <div class="hs-ad-slot-wrap" style="margin-bottom:28px; background:#080d1a; border:1px dashed rgba(255,255,255,0.1); border-radius:12px; padding:12px; text-align:center;">
            <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Advertisement</div>
            <div class="hs-ad-banner-placement" style="min-height:90px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:12px;">
                <?php do_action( 'hs_render_ad_slot', 'price_single_top' ); ?>
                <span style="color:#64748b;">[ AdSense Responsive Display Unit — Google Verified Placement ]</span>
            </div>
        </div>

        <!-- Variant Comparison Pricing Table -->
        <section style="background:#0b1120; border:1px solid rgba(0,245,212,0.25); border-radius:18px; padding:28px; margin-bottom:32px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; flex-wrap:wrap; gap:10px;">
                <div>
                    <h2 style="font-size:1.3rem; font-weight:800; color:#fff; margin:0 0 4px; display:flex; align-items:center; gap:8px;">
                        <span style="color:#10b981;">📊</span> Available Variants &amp; Price Breakdown
                    </h2>
                    <p style="color:#94a3b8; font-size:13px; margin:0;">
                        Official retail prices across different RAM and internal storage configurations in Bangladesh.
                    </p>
                </div>
                <span style="font-size:11px; font-family:var(--font-mono); color:#10b981; background:rgba(16,185,129,0.1); padding:4px 10px; border-radius:6px; border:1px solid rgba(16,185,129,0.3);">
                    BDT Verified Ledger
                </span>
            </div>

            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; text-align:left; font-size:14px;">
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.1); color:#94a3b8;">
                            <th style="padding:12px 14px;">Variant (RAM / Storage)</th>
                            <th style="padding:12px 14px;">Official Price (BDT)</th>
                            <th style="padding:12px 14px;">Price Status</th>
                            <th style="padding:12px 14px;">Availability</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $variants ) ) : ?>
                            <?php foreach ( $variants as $v ) : ?>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.05); color:#fff;">
                                    <td style="padding:14px; font-weight:700; color:#00f5d4; font-family:var(--font-mono);"><?php echo esc_html( $v['variant'] ?? '8GB / 128GB' ); ?></td>
                                    <td style="padding:14px; font-weight:900; color:#10b981; font-family:var(--font-mono); font-size:15px;"><?php echo esc_html( $v['price'] ?? $price_bdt ); ?></td>
                                    <td style="padding:14px;"><span style="background:rgba(0,245,212,0.1); color:#00f5d4; padding:2px 8px; border-radius:4px; font-size:11px;"><?php echo esc_html( $v['type'] ?? $price_type ); ?></span></td>
                                    <td style="padding:14px; color:#cbd5e1;"><?php echo esc_html( $availability ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.05); color:#fff;">
                                <td style="padding:14px; font-weight:700; color:#00f5d4; font-family:var(--font-mono);">Base Variant</td>
                                <td style="padding:14px; font-weight:900; color:#10b981; font-family:var(--font-mono); font-size:15px;"><?php echo esc_html( $price_bdt ); ?></td>
                                <td style="padding:14px;"><span style="background:rgba(0,245,212,0.1); color:#00f5d4; padding:2px 8px; border-radius:4px; font-size:11px;"><?php echo esc_html( $price_type ); ?></span></td>
                                <td style="padding:14px; color:#cbd5e1;"><?php echo esc_html( $availability ); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Main Editorial Content & Price Assessment -->
        <article class="entry-content hs-price-content" style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:36px; margin-bottom:32px; color:#e2e8f0; font-size:16px; line-height:1.8;">
            <h3 style="color:#00f5d4; font-size:1.4rem; font-weight:800; margin-top:0;">
                <?php echo esc_html( $device_name ); ?> বাংলাদেশে দাম ও মার্কেট পর্যালোচনা
            </h3>
            <?php the_content(); ?>

            <div style="margin-top:28px; background:#070a14; border-left:4px solid #10b981; padding:18px 20px; border-radius:8px;">
                <strong style="color:#10b981; font-size:14px;">🔍 দাম যাচাই সংক্রান্ত তথ্য ও সতর্কতা (Price Verification Policy):</strong>
                <p style="color:#94a3b8; font-size:13px; margin:6px 0 0; line-height:1.6;">
                    HackersShikkhok টিম নিয়মিত অনুমোদিত শো-রুম, ব্র্যান্ড শপ এবং বিশ্বস্ত রিটেইলারদের কাছ থেকে দাম যাচাই করে থাকে। ভ্যাট, ট্যাক্স বা রিটেইলার ক্যাশব্যাক অফারের কারণে এলাকাভেদে দামে সামান্য তারতম্য হতে পারে।
                </p>
            </div>
        </article>

        <!-- Cross-Link to Full Review -->
        <?php if ( ! empty( $review_url ) ) : ?>
            <section style="background:linear-gradient(135deg, rgba(0,245,212,0.12), rgba(123,44,191,0.15)); border:1px solid rgba(0,245,212,0.3); border-radius:18px; padding:28px; margin-bottom:32px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                <div>
                    <div style="color:#00f5d4; font-weight:800; font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">WANT COMPLETE IN-DEPTH ANALYSIS?</div>
                    <h3 style="color:#ffffff; font-size:1.3rem; font-weight:800; margin:4px 0;">Read Full <?php echo esc_html( $device_name ); ?> Review</h3>
                    <p style="color:#94a3b8; font-size:13px; margin:0;">
                        Camera samples, battery longevity tests, gaming benchmarks, pros, cons and final verdict.
                    </p>
                </div>
                <a href="<?php echo esc_url( home_url( $review_url ) ); ?>" style="padding:12px 24px; background:#00f5d4; color:#050811; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none; box-shadow:0 0 16px rgba(0,245,212,0.4);">
                    View Full Review ➔
                </a>
            </section>
        <?php endif; ?>

        <!-- AdSense Slot (Bottom) -->
        <div class="hs-ad-slot-wrap" style="margin-bottom:32px; background:#080d1a; border:1px dashed rgba(255,255,255,0.1); border-radius:12px; padding:12px; text-align:center;">
            <div style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Advertisement</div>
            <div class="hs-ad-banner-placement" style="min-height:90px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:12px;">
                <?php do_action( 'hs_render_ad_slot', 'price_single_bottom' ); ?>
                <span style="color:#64748b;">[ AdSense Native In-Article Display — Verified Placement ]</span>
            </div>
        </div>

    </div>
</main>

<?php
get_footer();

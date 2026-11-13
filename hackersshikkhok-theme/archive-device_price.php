<?php
/**
 * Archive Device Price Template - HackersShikkhok
 * URL: /device-prices/
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

<main id="primary" class="site-main hs-archive-prices-page" style="background:#06080d; color:#f8fafc; min-height:100vh; padding:40px 16px 80px;">
    <div style="max-width:1280px; margin:0 auto;">

        <!-- Breadcrumbs -->
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:20px; font-family:var(--font-mono);">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
            <span style="color:#64748b;">/</span>
            <span style="color:#cbd5e1; font-weight:700;">Device Prices</span>
        </nav>

        <!-- Archive Header -->
        <header style="background:linear-gradient(135deg, rgba(11,17,32,0.98), rgba(15,23,42,0.95)); border:1px solid rgba(16,185,129,0.35); border-radius:20px; padding:36px; margin-bottom:32px; text-align:center; position:relative; overflow:hidden;">
            <div style="display:inline-block; padding:4px 16px; border-radius:999px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); color:#10b981; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">
                💰 VERIFIED BANGLADESH BDT PRICING DIRECTORY
            </div>
            <h1 style="font-size:clamp(1.8rem, 4vw, 2.75rem); font-weight:900; color:#fff; margin:0 0 12px;">
                Mobile &amp; Hardware Prices in Bangladesh
            </h1>
            <p style="color:#94a3b8; font-size:14px; max-width:760px; margin:0 auto 20px; line-height:1.6;">
                Check latest verified prices, official vs retail market variants, RAM and storage options, and availability for all popular smartphone and tech devices in Bangladesh.
            </p>
        </header>

        <!-- Brand Filter Chips -->
        <?php if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) : ?>
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:28px;">
                <span style="font-size:12px; font-weight:700; color:#94a3b8; display:flex; align-items:center; margin-right:4px;">Filter by Brand:</span>
                <a href="<?php echo esc_url( home_url( '/device-prices/' ) ); ?>" style="padding:6px 14px; border-radius:8px; background:rgba(16,185,129,0.15); border:1px solid #10b981; color:#10b981; font-size:12px; font-weight:700; text-decoration:none;">All Brands</a>
                <?php foreach ( $brands as $b ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $b ) ); ?>" style="padding:6px 14px; border-radius:8px; background:#0b1120; border:1px solid rgba(255,255,255,0.08); color:#cbd5e1; font-size:12px; font-weight:600; text-decoration:none;">
                        <?php echo esc_html( $b->name ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Prices Grid -->
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px; margin-bottom:40px;">
            <?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); 
                    $p_id = get_the_ID();
                    $dev_name   = get_post_meta( $p_id, '_hs_price_device_name', true ) ?: get_the_title();
                    $price_bdt  = get_post_meta( $p_id, '_hs_price_bdt', true ) ?: '৳42,999';
                    $price_type = get_post_meta( $p_id, '_hs_price_type', true ) ?: 'Official';
                    $avail      = get_post_meta( $p_id, '_hs_price_availability', true ) ?: 'In Stock';
                ?>
                    <article style="background:#0b1120; border:1px solid rgba(16,185,129,0.25); border-radius:16px; padding:20px; display:flex; flex-direction:column; justify-content:space-between; box-shadow:0 6px 20px rgba(0,0,0,0.4);" class="hs-tool-box-card">
                        <div>
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div style="margin:-20px -20px 16px -20px; height:150px; overflow:hidden; border-top-left-radius:16px; border-top-right-radius:16px;">
                                    <?php the_post_thumbnail( 'medium', array( 'style' => 'width:100%; height:100%; object-fit:cover;' ) ); ?>
                                </div>
                            <?php endif; ?>

                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <span style="font-size:10px; font-weight:800; color:#10b981; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); padding:2px 8px; border-radius:4px;">
                                    <?php echo esc_html( $price_type ); ?>
                                </span>
                                <span style="font-size:11px; color:#fbbf24; font-weight:700;">● <?php echo esc_html( $avail ); ?></span>
                            </div>

                            <h2 style="font-size:1.1rem; font-weight:800; color:#fff; margin:0 0 10px; line-height:1.3;">
                                <a href="<?php the_permalink(); ?>" style="color:#fff; text-decoration:none;">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <div style="font-size:1.6rem; font-weight:900; color:#10b981; font-family:var(--font-mono); margin-bottom:12px;">
                                <?php echo esc_html( $price_bdt ); ?>
                            </div>
                        </div>

                        <div style="padding-top:12px; border-top:1px solid rgba(255,255,255,0.06); display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:11px; color:#64748b;">Verified BDT</span>
                            <a href="<?php the_permalink(); ?>" style="font-size:12px; font-weight:800; color:#10b981; text-decoration:none;">
                                View Details ➔
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <div style="grid-column:1/-1; background:#0b1120; border:1px dashed rgba(255,255,255,0.1); border-radius:14px; padding:32px; text-align:center; color:#94a3b8;">
                    <p style="font-size:15px; margin:0 0 12px;">No published price listings found yet.</p>
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=device_price' ) ); ?>" style="color:#10b981; font-weight:700;">+ Add New Price Page in Dashboard</a>
                </div>
            <?php endif; ?>
        </div>

        <?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>

    </div>
</main>

<?php
get_footer();

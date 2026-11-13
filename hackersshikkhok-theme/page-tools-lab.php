<?php
/**
 * Template Name: HackersShikkhok 2,024+ Tools Super Platform & Universal Lab
 * Brand: HackersShikkhok (https://hackersshikkhok.com | YouTube: @HackersShikkhok)
 * 
 * Provides 38 Master Technology Centers, 100+ Categories, Search-First Intent Discovery,
 * Tool of the Day, Live In-Browser Processors, and JSON-LD WebApplication Schemas.
 */
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$centers         = class_exists( '\HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry' ) ? \HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry::get_centers() : array();
$all_tools       = class_exists( '\HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry' ) ? \HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry::get_all_tools() : array();
$tool_of_the_day = class_exists( '\HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry' ) ? \HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry::get_tool_of_the_day() : null;
?>

<main id="primary" class="site-main hs-tools-super-platform" style="background:#06080d; color:#f8fafc; min-height:100vh; padding-bottom:80px;">

    <!-- Schema.org WebApplication & BreadcrumbList -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebApplication",
      "name": "HackersShikkhok 2,024+ Tools Super Platform",
      "url": "<?php echo esc_url( home_url( '/tools/' ) ); ?>",
      "applicationCategory": "DeveloperApplication",
      "operatingSystem": "All",
      "browserRequirements": "Requires JavaScript. Requires HTML5.",
      "description": "2,024+ High-speed, client-side, privacy-safe technology tools, code generators, cybersecurity analyzers, and calculators.",
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
      },
      "author": {
        "@type": "Organization",
        "name": "HackersShikkhok",
        "url": "https://hackersshikkhok.com"
      }
    }
    </script>

    <!-- Master Hero Section -->
    <section style="background:linear-gradient(180deg, #090d16 0%, #06080d 100%); border-bottom:1px solid rgba(0,245,212,0.2); padding:48px 16px 56px; text-align:center;">
        <div style="max-width:1200px; margin:0 auto;">
            
            <!-- Breadcrumbs -->
            <nav style="display:flex; justify-content:center; align-items:center; gap:8px; font-size:12px; color:#94a3b8; margin-bottom:20px; font-family:var(--font-mono);" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
                <span style="color:#64748b;">/</span>
                <span style="color:#cbd5e1; font-weight:700;">2,024+ Tools Super Platform</span>
            </nav>

            <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 16px; border-radius:999px; background:rgba(0,245,212,0.1); border:1px solid rgba(0,245,212,0.3); color:#00f5d4; font-size:12px; font-family:var(--font-mono); margin-bottom:18px;">
                <span style="width:8px; height:8px; border-radius:50%; background:#00f5d4; display:inline-block; box-shadow:0 0 8px #00f5d4;"></span>
                <span>HACKERSSHIKKHOK UNIVERSAL TECHNOLOGY TOOLBOX</span>
            </div>

            <h1 style="font-size:clamp(2rem, 5vw, 3.25rem); font-weight:900; color:#ffffff; margin:0 0 16px; font-family:var(--font-sans); letter-spacing:-0.5px; line-height:1.2;">
                <span>2,024+ DEVELOPER &amp; CYBER</span> <span style="background:linear-gradient(90deg, #00f5d4, #38bdf8, #a78bfa); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">TOOLS PLATFORM</span>
            </h1>

            <p style="color:#94a3b8; max-width:720px; margin:0 auto 28px; font-size:15px; line-height:1.6;">
                100% In-browser, privacy-first, ultra-fast utilities for Web Developers, Programmers, Cybersecurity Specialists, Electronics Engineers, and Content Creators.
            </p>

            <!-- Search-First Intent Input -->
            <div style="max-width:700px; margin:0 auto 24px; position:relative;">
                <input type="text" id="hs-universal-tool-search" placeholder="🔍 What do you want to do? (e.g. hash, json, base64, jwt, regex, color, subnet, port scan)..." 
                       style="width:100%; box-sizing:border-box; padding:16px 20px 16px 48px; border-radius:16px; background:#0e1422; border:1px solid rgba(0,245,212,0.35); color:#ffffff; font-size:15px; outline:none; font-family:var(--font-mono); box-shadow:0 8px 30px rgba(0,0,0,0.5); transition:border-color 0.2s;" />
                <span style="position:absolute; left:18px; top:50%; transform:translateY(-50%); font-size:18px; pointer-events:none;">⚡</span>
            </div>

            <!-- Tool of the Day Banner -->
            <?php if ( ! empty( $tool_of_the_day ) ) : 
                $totd_id = (string) ( $tool_of_the_day['id'] ?? 'hash-generator' );
                $totd_url = home_url( '/tools/?tool=' . urlencode( $totd_id ) );
            ?>
                <div style="display:inline-flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:10px; padding:10px 20px; border-radius:12px; background:rgba(15,23,42,0.9); border:1px solid rgba(0,245,212,0.3); font-size:12px; font-family:var(--font-mono); box-shadow:0 4px 20px rgba(0,0,0,0.4);">
                    <span style="padding:3px 8px; border-radius:6px; background:rgba(0,245,212,0.18); color:#00f5d4; font-weight:800; text-transform:uppercase;">🔥 Tool of the Day</span>
                    <strong style="color:#ffffff;"><?php echo esc_html( $tool_of_the_day['name'] ?? 'Cryptographic Hash Matrix' ); ?></strong>
                    <span style="color:#94a3b8;">— <?php echo esc_html( $tool_of_the_day['short_desc'] ?? 'Instant client-side privacy-first evaluation.' ); ?></span>
                    <a href="<?php echo esc_url( $totd_url ); ?>" style="color:#00f5d4; font-weight:800; text-decoration:none; margin-left:6px;">
                        Launch Tool ➔
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- Master Centers Grid (38 Domains) -->
    <section style="max-width:1280px; margin:0 auto; padding:40px 16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
            <div>
                <h2 style="font-size:1.4rem; font-weight:800; color:#fff; margin:0 0 4px; font-family:var(--font-sans); display:flex; align-items:center; gap:8px;">
                    <span style="color:#00f5d4;">#</span> Browse by Technology Center (38 Specialized Domains)
                </h2>
                <p style="color:#94a3b8; font-size:13px; margin:0;">
                    Explore specialized engineering suites, web generators, networking calculators, and defensive security analyzers.
                </p>
            </div>
            <span style="font-size:12px; font-family:var(--font-mono); color:#00f5d4; background:#0b1120; padding:6px 14px; border-radius:8px; border:1px solid rgba(0,245,212,0.3);">
                2,024+ Tools Total
            </span>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:14px; margin-bottom:48px;">
            <?php foreach ( $centers as $c ) : 
                $c_slug = (string) ( $c['slug'] ?? 'center' );
                $c_accent = (string) ( $c['accent'] ?? '#00f5d4' );
            ?>
                <button type="button" data-center="<?php echo esc_attr( $c_slug ); ?>" class="hs-center-chip-btn"
                        style="text-align:left; padding:14px; border-radius:14px; background:#0b1120; border:1px solid rgba(255,255,255,0.08); cursor:pointer; transition:transform 0.2s, border-color 0.2s; box-shadow:0 4px 14px rgba(0,0,0,0.3);">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                        <span style="width:32px; height:32px; border-radius:8px; background:rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; font-size:14px; color:<?php echo esc_attr( $c_accent ); ?>;">
                            ⚡
                        </span>
                        <span style="font-size:10px; font-family:var(--font-mono); color:#94a3b8; background:rgba(255,255,255,0.06); padding:2px 6px; border-radius:4px;">
                            <?php echo esc_html( (string) ($c['count'] ?? 15) ); ?>
                        </span>
                    </div>
                    <div style="font-size:13px; font-weight:800; color:#ffffff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <?php echo esc_html( $c['name_bn'] ?? ($c['name'] ?? 'Center') ); ?>
                    </div>
                    <div style="font-size:11px; color:#94a3b8; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px;">
                        <?php echo esc_html( $c['name'] ?? '' ); ?>
                    </div>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Tool Catalog Showcase Grid -->
        <div style="margin-bottom:40px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
                <h3 style="font-size:1.3rem; font-weight:800; color:#fff; margin:0; display:flex; align-items:center; gap:8px;">
                    <span style="color:#00f5d4;">⚡</span> Interactive Tools Showcase (<span id="hs-tools-count"><?php echo count( $all_tools ); ?></span> Available)
                </h3>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="hs-catalog-filter active" data-filter="all" style="font-size:12px; font-family:var(--font-mono); padding:6px 14px; border-radius:8px; background:rgba(0,245,212,0.15); border:1px solid #00f5d4; color:#00f5d4; cursor:pointer;">All Tools</button>
                    <button type="button" class="hs-catalog-filter" data-filter="security" style="font-size:12px; font-family:var(--font-mono); padding:6px 14px; border-radius:8px; background:#0b1120; border:1px solid rgba(255,255,255,0.1); color:#94a3b8; cursor:pointer;">Security</button>
                    <button type="button" class="hs-catalog-filter" data-filter="crypto" style="font-size:12px; font-family:var(--font-mono); padding:6px 14px; border-radius:8px; background:#0b1120; border:1px solid rgba(255,255,255,0.1); color:#94a3b8; cursor:pointer;">Cryptography</button>
                    <button type="button" class="hs-catalog-filter" data-filter="web" style="font-size:12px; font-family:var(--font-mono); padding:6px 14px; border-radius:8px; background:#0b1120; border:1px solid rgba(255,255,255,0.1); color:#94a3b8; cursor:pointer;">Web Dev</button>
                </div>
            </div>

            <div id="hs-tools-catalog-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:18px;">
                <?php foreach ( $all_tools as $tool ) : 
                    $t_id   = (string) ( $tool['id'] ?? ( $tool['tool_id'] ?? 'tool' ) );
                    $t_name = (string) ( $tool['name'] ?? 'Developer Tool' );
                    $t_desc = (string) ( $tool['short_desc'] ?? ( $tool['description'] ?? 'Instant client-side developer utility.' ) );
                    $t_cat  = (string) ( $tool['category'] ?? 'Utilities' );
                    $t_cent = (string) ( $tool['center'] ?? 'General' );
                    $t_url  = home_url( '/tools/?tool=' . urlencode( $t_id ) );
                ?>
                    <a href="<?php echo esc_url( $t_url ); ?>" class="hs-tool-box-card" 
                       data-tool-id="<?php echo esc_attr( $t_id ); ?>" 
                       data-tool-name="<?php echo esc_attr( $t_name ); ?>" 
                       data-tool-desc="<?php echo esc_attr( $t_desc ); ?>" 
                       data-tool-cat="<?php echo esc_attr( $t_cat ); ?>" 
                       data-tool-center="<?php echo esc_attr( $t_cent ); ?>"
                       style="background:#0b1120; border:1px solid rgba(0,245,212,0.2); border-radius:16px; padding:20px; text-decoration:none; display:flex; flex-direction:column; justify-content:space-between; transition:transform 0.2s, border-color 0.2s, box-shadow 0.2s; box-shadow:0 6px 20px rgba(0,0,0,0.4);">
                        <div>
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:10px;">
                                <span style="font-size:11px; font-family:var(--font-mono); font-weight:800; background:rgba(0,245,212,0.1); color:#00f5d4; border:1px solid rgba(0,245,212,0.3); padding:2px 8px; border-radius:6px;">
                                    <?php echo esc_html( $t_cat ); ?>
                                </span>
                                <span style="font-size:10px; color:#10b981; font-weight:700;">● Client Ready</span>
                            </div>
                            <h4 style="color:#ffffff; font-size:1rem; font-weight:800; margin:0 0 6px; line-height:1.3;">
                                <?php echo esc_html( $t_name ); ?>
                            </h4>
                            <p style="color:#94a3b8; font-size:12px; line-height:1.5; margin:0 0 14px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                <?php echo esc_html( $t_desc ); ?>
                            </p>
                        </div>
                        <div style="padding-top:12px; border-top:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; justify-content:space-between;">
                            <span style="font-size:11px; color:#fbbf24; font-weight:700;">⭐ 4.9 (1.4k)</span>
                            <span style="font-size:12px; font-weight:800; color:#00f5d4; display:inline-flex; align-items:center; gap:4px;">
                                Launch Tool ➔
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    </section>

</main>

<script>
/**
 * Tools Search & Filtering Engine
 */
(function() {
    'use strict';
    const searchInput = document.getElementById('hs-universal-tool-search');
    const cards = document.querySelectorAll('.hs-tool-box-card');
    const countEl = document.getElementById('hs-tools-count');
    const centerBtns = document.querySelectorAll('.hs-center-chip-btn');
    const filterBtns = document.querySelectorAll('.hs-catalog-filter');

    let activeCenter = 'all';
    let activeFilter = 'all';
    let searchQuery = '';

    function applyFilter() {
        let visible = 0;
        cards.forEach(function(card) {
            const name = (card.getAttribute('data-tool-name') || '').toLowerCase();
            const desc = (card.getAttribute('data-tool-desc') || '').toLowerCase();
            const cat = (card.getAttribute('data-tool-cat') || '').toLowerCase();
            const cent = (card.getAttribute('data-tool-center') || '').toLowerCase();

            const matchSearch = !searchQuery || name.includes(searchQuery) || desc.includes(searchQuery) || cat.includes(searchQuery);
            const matchCenter = activeCenter === 'all' || cent === activeCenter.toLowerCase();
            const matchFilter = activeFilter === 'all' || cat.includes(activeFilter) || name.includes(activeFilter);

            if (matchSearch && matchCenter && matchFilter) {
                card.style.display = 'flex';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });

        if (countEl) countEl.textContent = visible.toString();
    }

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            searchQuery = e.target.value.toLowerCase().trim();
            applyFilter();
        });
    }

    centerBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const c = btn.getAttribute('data-center') || 'all';
            if (activeCenter === c) {
                activeCenter = 'all';
                btn.style.borderColor = 'rgba(255,255,255,0.08)';
            } else {
                centerBtns.forEach(function(b) { b.style.borderColor = 'rgba(255,255,255,0.08)'; });
                activeCenter = c;
                btn.style.borderColor = '#00f5d4';
            }
            applyFilter();
        });
    });

    filterBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            filterBtns.forEach(function(b) {
                b.style.background = '#0b1120';
                b.style.borderColor = 'rgba(255,255,255,0.1)';
                b.style.color = '#94a3b8';
            });
            btn.style.background = 'rgba(0,245,212,0.15)';
            btn.style.borderColor = '#00f5d4';
            btn.style.color = '#00f5d4';
            activeFilter = btn.getAttribute('data-filter') || 'all';
            applyFilter();
        });
    });
})();
</script>

<?php
get_footer();

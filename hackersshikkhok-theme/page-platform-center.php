<?php
/**
 * Template Name: Platform Center & Creator Studio Hub
 * Package: HackersShikkhok Theme
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * 
 * Master Hub for 38 Technology Centers and 6 Creator Studios:
 * Web to APK, PWA Studio, Cyber Card Maker, 3D Gift Unit, Canvas Games, Cyber Command Matrix.
 */

declare(strict_types=1);

get_header();

$centers = class_exists( '\HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry' )
    ? \HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry::get_centers()
    : array();

$studios = class_exists( '\HackersShikkhok\Theme\Modules\Platform\PlatformHubEngine' )
    ? \HackersShikkhok\Theme\Modules\Platform\PlatformHubEngine::get_studios_data()
    : array();
?>

<main id="primary" class="hs-platform-center-page" style="background:#06080d; color:#f8fafc; min-height:100vh; padding:40px 16px 80px;">
    <div style="max-width:1280px; margin:0 auto;">

        <!-- Breadcrumb Navigation -->
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:24px; font-family:var(--font-mono);">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
            <span style="color:#64748b;">/</span>
            <span style="color:#cbd5e1; font-weight:700;">Platform Centers &amp; Creator Studios Hub</span>
        </nav>

        <!-- Hero Header -->
        <header style="background:linear-gradient(135deg, rgba(11,17,32,0.98), rgba(15,23,42,0.95)); border:1px solid rgba(0,245,212,0.3); border-radius:20px; padding:36px; margin-bottom:36px; text-align:center; position:relative; overflow:hidden; box-shadow:0 12px 36px rgba(0,0,0,0.5);">
            <div style="display:inline-block; padding:6px 18px; border-radius:999px; background:rgba(0,245,212,0.12); border:1px solid rgba(0,245,212,0.35); color:#00f5d4; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">
                🚀 38 Specialized Centers &amp; Autonomous Creator Studios
            </div>
            <h1 style="font-size:clamp(2rem, 4.5vw, 3rem); font-weight:900; margin:0 0 16px; background:linear-gradient(90deg, #00f5d4, #a78bfa, #ff3366); -webkit-background-clip:text; -webkit-text-fill-color:transparent; line-height:1.2;">
                Platform Center &amp; Creator Studio Hub
            </h1>
            <p style="color:#94a3b8; font-size:15px; max-width:800px; margin:0 auto 20px; line-height:1.6;">
                Welcome to HackersShikkhok's decentralized engineering ecosystem. Each platform center is dedicated to a distinct technical discipline — from Android APK and PWA creation to cybersecurity radars and 3D interactive graphics.
            </p>
            <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
                <a href="#creator-studios" style="padding:10px 22px; background:#00f5d4; color:#050811; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none; box-shadow:0 0 16px rgba(0,245,212,0.35);">
                    🛠️ Explore 6 Creator Studios
                </a>
                <a href="#technology-centers" style="padding:10px 22px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.15); color:#fff; font-weight:700; font-size:13px; border-radius:10px; text-decoration:none;">
                    🌐 Browse 38 Centers
                </a>
            </div>
        </header>

        <!-- Section 1: 6 Autonomous Creator Studios -->
        <section id="creator-studios" style="margin-bottom:48px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                <div>
                    <h2 style="font-size:1.5rem; font-weight:800; color:#fff; margin:0 0 4px; display:flex; align-items:center; gap:8px;">
                        <span style="color:#00f5d4;">🛠️</span> Multi-Platform Creator Studios
                    </h2>
                    <p style="color:#94a3b8; font-size:13px; margin:0;">
                        Build real applications, digital assets, and interactive systems directly inside your browser.
                    </p>
                </div>
                <span style="font-size:12px; font-weight:700; color:#10b981; background:rgba(16,185,129,0.12); padding:4px 12px; border-radius:999px; border:1px solid rgba(16,185,129,0.3);">
                    ● 6 Active Web Studios
                </span>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:22px;">
                <?php foreach ( $studios as $s ) : 
                    $s_color = (string) ( $s['color'] ?? '#00f5d4' );
                ?>
                    <div class="hs-studio-feature-card" style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-top:3px solid <?php echo esc_attr( $s_color ); ?>; border-radius:18px; padding:26px; display:flex; flex-direction:column; justify-content:space-between; box-shadow:0 8px 24px rgba(0,0,0,0.4); transition:transform 0.2s, border-color 0.2s;">
                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                                <span style="font-size:36px; filter:drop-shadow(0 0 10px <?php echo esc_attr( $s_color ); ?>);"><?php echo esc_html( $s['icon'] ); ?></span>
                                <span style="background:rgba(255,255,255,0.06); color:<?php echo esc_attr( $s_color ); ?>; border:1px solid <?php echo esc_attr( $s_color ); ?>; font-size:11px; font-weight:800; padding:3px 10px; border-radius:6px; letter-spacing:0.5px;">
                                    <?php echo esc_html( $s['badge'] ); ?>
                                </span>
                            </div>
                            <h3 style="color:#ffffff; font-size:1.25rem; font-weight:800; margin:0 0 6px; line-height:1.3;">
                                <?php echo esc_html( $s['title'] ); ?>
                            </h3>
                            <div style="color:<?php echo esc_attr( $s_color ); ?>; font-size:13px; font-weight:700; margin-bottom:12px;">
                                <?php echo esc_html( $s['title_bn'] ); ?>
                            </div>
                            <p style="color:#94a3b8; font-size:13px; line-height:1.6; margin:0 0 24px;">
                                <?php echo esc_html( $s['description'] ); ?>
                            </p>
                        </div>
                        <a href="<?php echo esc_url( $s['url'] ); ?>" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:12px 20px; background:<?php echo esc_attr( $s_color ); ?>; color:#050811; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none; box-shadow:0 4px 16px rgba(0,0,0,0.4);">
                            <span><?php echo esc_html( $s['btn_text'] ); ?></span>
                            <span>➔</span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Section 2: 38 Specialized Technology Centers -->
        <section id="technology-centers" style="background:#0b1120; border:1px solid rgba(0,245,212,0.25); border-radius:20px; padding:32px; margin-bottom:48px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
                <div>
                    <h2 style="font-size:1.4rem; font-weight:800; color:#fff; margin:0 0 4px; display:flex; align-items:center; gap:8px;">
                        <span style="color:#00f5d4;">🌐</span> 38 Master Technology Centers
                    </h2>
                    <p style="color:#94a3b8; font-size:13px; margin:0;">
                        Deep-dive into individual engineering disciplines with dedicated tools, simulators, and calculators.
                    </p>
                </div>
                <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="color:#00f5d4; font-weight:700; font-size:13px; text-decoration:none;">
                    View All 2,024+ Tools ➔
                </a>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:16px;">
                <?php foreach ( $centers as $c ) : 
                    $c_slug = (string) ( $c['slug'] ?? 'center' );
                    $c_accent = (string) ( $c['accent'] ?? '#00f5d4' );
                    $c_tools_url = home_url( '/tools/?center=' . urlencode( $c_slug ) );
                ?>
                    <a href="<?php echo esc_url( $c_tools_url ); ?>" style="background:#06080d; border:1px solid rgba(255,255,255,0.06); border-radius:14px; padding:18px; text-decoration:none; display:flex; align-items:center; justify-content:space-between; transition:transform 0.2s, border-color 0.2s;" class="hs-center-link-box">
                        <div style="display:flex; align-items:center; gap:14px;">
                            <span style="width:40px; height:40px; border-radius:10px; background:rgba(255,255,255,0.04); display:flex; align-items:center; justify-content:center; font-size:18px; color:<?php echo esc_attr( $c_accent ); ?>; border:1px solid <?php echo esc_attr( $c_accent ); ?>;">
                                ⚡
                            </span>
                            <div>
                                <h4 style="color:#ffffff; font-size:14px; font-weight:800; margin:0 0 2px; line-height:1.2;">
                                    <?php echo esc_html( $c['name_bn'] ?? ($c['name'] ?? 'Center') ); ?>
                                </h4>
                                <div style="color:#94a3b8; font-size:11px;">
                                    <?php echo esc_html( $c['name'] ?? '' ); ?>
                                </div>
                            </div>
                        </div>
                        <span style="font-size:11px; font-family:var(--font-mono); font-weight:800; background:rgba(0,245,212,0.1); color:#00f5d4; padding:3px 8px; border-radius:6px; border:1px solid rgba(0,245,212,0.25);">
                            <?php echo esc_html( (string) ($c['count'] ?? 15) ); ?> Tools
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Cross Link: Clear Separation from Cyber Academy -->
        <section style="background:linear-gradient(135deg, rgba(123,44,191,0.15), rgba(0,245,212,0.1)); border:1px solid rgba(167,139,250,0.3); border-radius:18px; padding:28px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
            <div>
                <div style="color:#a78bfa; font-weight:800; font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">ACADEMIC FACULTY &amp; LMS HUB</div>
                <h3 style="color:#ffffff; font-size:1.3rem; font-weight:800; margin:4px 0;">Looking for Structured Courses &amp; Certifications?</h3>
                <p style="color:#94a3b8; font-size:13px; margin:0;">
                    Visit the dedicated HackersShikkhok Cyber Academy for 8 engineering faculties, live CTF terminal flags, and cryptographic certificates.
                </p>
            </div>
            <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" style="padding:12px 24px; background:#00f5d4; color:#050811; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none; box-shadow:0 0 16px rgba(0,245,212,0.4);">
                Go to Cyber Academy ➔
            </a>
        </section>

    </div>
</main>

<?php
get_footer();

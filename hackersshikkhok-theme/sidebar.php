<?php
/**
 * Master Cyber Sidebar Template - HackersShikkhok
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */
declare(strict_types=1);

$categories = get_categories( array(
    'orderby'    => 'count',
    'order'      => 'DESC',
    'number'     => 10,
    'hide_empty' => false,
) );

$sidebar_tools = array();
if ( hs_is_module_enabled( 'mod_tools_engine' ) && class_exists( '\HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry' ) ) {
    $sidebar_tools = hs_cache_remember(
        'sidebar_featured_tools_6',
        static fn(): array => array_slice( \HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry::get_all_tools(), 0, 6 ),
        3600
    );
}
?>
<aside class="hs-site-sidebar" aria-label="Cyber Sidebar">

    <!-- Search Widget -->
    <div class="hs-sidebar-widget hs-cyber-card">
        <h3 class="hs-widget-title">🔍 Quick Search</h3>
        <form role="search" method="get" class="hs-sidebar-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <div class="hs-search-input-group">
                <input type="search" name="s" class="hs-sidebar-search-input" placeholder="Search cyber tutorials, tools, code..." value="<?php echo get_search_query(); ?>" />
                <button type="submit" class="hs-sidebar-search-btn">Go</button>
            </div>
        </form>
    </div>

    <!-- 8 Specialized Engineering Faculties -->
    <div class="hs-sidebar-widget hs-cyber-card">
        <h3 class="hs-widget-title">🛡️ 8 Master Faculties</h3>
        <ul class="hs-sidebar-faculties-list">
            <li>
                <a href="<?php echo esc_url( home_url( '/academy/?cat=cybersecurity' ) ); ?>">
                    <span class="hs-fac-icon">🛡️</span>
                    <span class="hs-fac-title">Cybersecurity &amp; Ethical Hacking</span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/academy/?cat=webdev' ) ); ?>">
                    <span class="hs-fac-icon">💻</span>
                    <span class="hs-fac-title">Full-Stack Web &amp; Software</span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/academy/?cat=wordpress' ) ); ?>">
                    <span class="hs-fac-icon">⚡</span>
                    <span class="hs-fac-title">WordPress Engineering &amp; Core</span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/academy/?cat=ai_data' ) ); ?>">
                    <span class="hs-fac-icon">🤖</span>
                    <span class="hs-fac-title">AI, Data Science &amp; Autopilot</span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/academy/?cat=freelancing' ) ); ?>">
                    <span class="hs-fac-icon">💼</span>
                    <span class="hs-fac-title">Freelancing &amp; Remote Careers</span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/academy/?cat=design_media' ) ); ?>">
                    <span class="hs-fac-icon">🎨</span>
                    <span class="hs-fac-title">UI/UX Design Systems &amp; Media</span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/academy/?cat=hardware_iot' ) ); ?>">
                    <span class="hs-fac-icon">📡</span>
                    <span class="hs-fac-title">Hardware, Networking &amp; IoT</span>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url( home_url( '/academy/?cat=marketing_business' ) ); ?>">
                    <span class="hs-fac-icon">📈</span>
                    <span class="hs-fac-title">Technical SEO &amp; Growth</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Essential 2,024+ Tools Quick Access -->
    <?php if ( ! empty( $sidebar_tools ) ) : ?>
        <div class="hs-sidebar-widget hs-cyber-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <h3 class="hs-widget-title" style="margin-bottom:0;">⚡ Popular Tools</h3>
                <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>" style="color:#00f5d4;font-size:11px;font-weight:700;text-decoration:none;">View All 2,024+ &rarr;</a>
            </div>
            <ul class="hs-sidebar-tools-list">
                <?php foreach ( $sidebar_tools as $t ) : ?>
                    <li>
                        <a href="<?php echo esc_url( home_url( '/tools/?tool=' . urlencode( (string) ( $t['id'] ?? ( $t['tool_id'] ?? '' ) ) ) ) ); ?>">
                            <span class="hs-tool-bullet">⚡</span>
                            <span class="hs-tool-name"><?php echo esc_html( $t['name'] ?? 'Developer Tool' ); ?></span>
                            <span class="hs-tool-tag"><?php echo esc_html( $t['category'] ?? 'Tool' ); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Academy Course Promo Card -->
    <div class="hs-sidebar-widget hs-cyber-card hs-academy-promo-card">
        <span class="hs-promo-badge">🎓 CYBER ACADEMY</span>
        <h4 class="hs-promo-title">Learn Hands-On Ethical Hacking</h4>
        <p class="hs-promo-desc">Master defensive cybersecurity, Linux terminal operations, and CTF challenges with verifiable digital certificates.</p>
        <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" class="hs-btn-primary" style="display:block;text-align:center;padding:10px 16px;font-size:12px;">
            Explore Academy Courses &rarr;
        </a>
    </div>

    <!-- YouTube Channel Subscribe Card -->
    <div class="hs-sidebar-widget hs-cyber-card hs-yt-promo-card">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
            <span style="font-size:22px;color:#ef4444;">▶</span>
            <strong style="color:#fff;font-size:13px;">Official YouTube Channel</strong>
        </div>
        <p style="color:#94a3b8;font-size:12px;margin-bottom:12px;">Subscribe to @HackersShikkhok for practical video walkthroughs and security tutorials.</p>
        <a href="https://youtube.com/@HackersShikkhok" target="_blank" rel="noopener noreferrer" class="hs-yt-sub-btn">
            Subscribe @HackersShikkhok
        </a>
    </div>

    <!-- Dynamic WordPress Widgets (if any) -->
    <?php if ( is_active_sidebar( 'cyber-primary-sidebar' ) ) : ?>
        <?php dynamic_sidebar( 'cyber-primary-sidebar' ); ?>
    <?php endif; ?>

</aside>

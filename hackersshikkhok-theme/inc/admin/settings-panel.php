<?php
/**
 * Unified Tabbed WordPress Admin Settings Panel for HackersShikkhok.
 * Centralizes every migrated theme module, UI preset, AI API key, SEO/AdSense setting,
 * security limit, and performance/cache control using WordPress Settings API + Nonces + manage_options.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_menu', static function (): void {
    add_menu_page(
        __( 'HackersShikkhok Theme & System Settings', 'hackersshikkhok-theme' ),
        __( 'HS Theme Settings', 'hackersshikkhok-theme' ),
        'manage_options',
        'hs-theme-settings',
        'hs_render_unified_settings_page',
        'dashicons-admin-generic',
        2
    );
}, 5 );

add_action( 'admin_init', static function (): void {
    register_setting(
        'hs_theme_master_group',
        HS_MASTER_SETTINGS_OPTION,
        array(
            'type'              => 'array',
            'sanitize_callback' => 'hs_sanitize_master_settings',
            'default'           => hs_get_default_settings(),
        )
    );
} );

/**
 * Strict sanitization callback preserving values from tabs not currently submitted.
 */
function hs_sanitize_master_settings( mixed $input ): array {
    if ( ! current_user_can( 'manage_options' ) ) {
        return hs_get_all_settings();
    }

    $current  = hs_get_all_settings( true );
    $defaults = hs_get_default_settings();
    $raw      = is_array( $input ) ? $input : array();
    $tab      = isset( $raw['_active_tab'] ) ? sanitize_key( (string) $raw['_active_tab'] ) : 'general';

    $tab_checkboxes = array(
        'general'     => array( 'enable_ai_voice_reader', 'enable_stories_bar', 'enable_notifications_bell' ),
        'modules'     => array(
            'mod_tools_engine', 'mod_css_rgb_lab', 'mod_live_demo', 'mod_code_library',
            'mod_academy_lms', 'mod_ctf_engine', 'mod_wallet_economy', 'mod_post_pay_counter',
            'mod_community_social', 'mod_security_saas', 'mod_device_games_lab', 'mod_platform_centers',
            'mod_youtube', 'mod_github', 'mod_downloads', 'mod_user_submissions',
        ),
        'ai'          => array( 'emergency_kill_switch', 'enable_ai_autopilot', 'enable_ai_tutor' ),
        'seo'         => array( 'enable_schema_jsonld', 'enable_og_meta', 'enable_xml_sitemap', 'enable_auto_linking', 'enable_auto_ads_txt' ),
        'security'    => array( 'enable_content_lock', 'enable_video_shield', 'enable_branded_auth', 'enable_branded_email', 'enable_passkey_geo' ),
        'performance' => array( 'enable_transient_cache', 'conditional_assets_only', 'defer_non_critical_js', 'disable_wp_emojis' ),
    );

    $updated = $current;

    // Reset only the checkboxes belonging to the submitted tab, then set checked ones to 1
    foreach ( $tab_checkboxes[ $tab ] ?? array() as $cb_key ) {
        $updated[ $cb_key ] = isset( $raw[ $cb_key ] ) ? 1 : 0;
    }

    // Text, Select, Color & Numeric fields
    $text_fields = array(
        'brand_name', 'brand_tagline', 'color_preset', 'featured_category',
        'autopilot_publishing_mode', 'gsc_verification_token', 'indexnow_api_key',
        'adsense_publisher_id', 'gemini_api_key', 'openai_api_key', 'claude_api_key',
    );
    foreach ( $text_fields as $key ) {
        if ( array_key_exists( $key, $raw ) ) {
            $updated[ $key ] = sanitize_text_field( wp_unslash( (string) $raw[ $key ] ) );
        }
    }

    foreach ( array( 'primary_color', 'secondary_color', 'accent_color' ) as $color_key ) {
        if ( array_key_exists( $color_key, $raw ) ) {
            $hex = sanitize_hex_color( wp_unslash( (string) $raw[ $color_key ] ) );
            $updated[ $color_key ] = $hex ?: (string) $defaults[ $color_key ];
        }
    }

    foreach ( array( 'popular_weight_likes', 'popular_weight_comments', 'popular_weight_views' ) as $float_key ) {
        if ( array_key_exists( $float_key, $raw ) ) {
            $updated[ $float_key ] = max( 0.0, (float) $raw[ $float_key ] );
        }
    }

    $int_bounds = array(
        'popular_posts_count'    => array( 1, 24 ),
        'latest_posts_count'     => array( 1, 30 ),
        'autopilot_min_quality'  => array( 50, 100 ),
        'autopilot_max_retries'  => array( 0, 5 ),
        'ai_daily_request_limit' => array( 10, 10000 ),
        'rest_rate_limit_rpm'    => array( 10, 5000 ),
        'cache_ttl_seconds'      => array( 60, 86400 ),
    );
    foreach ( $int_bounds as $int_key => list( $min, $max ) ) {
        if ( array_key_exists( $int_key, $raw ) ) {
            $updated[ $int_key ] = max( $min, min( $max, (int) $raw[ $int_key ] ) );
        }
    }

    hs_sync_legacy_options( $updated );
    hs_purge_all_caches();

    return $updated;
}

// Handle maintenance POST actions (Purge Cache / One-Time Page Sync) securely
add_action( 'admin_post_hs_maintenance_action', static function (): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Unauthorized access.', 'hackersshikkhok-theme' ) );
    }
    check_admin_referer( 'hs_maintenance_nonce_action', 'hs_maintenance_nonce' );

    $action_type = isset( $_POST['hs_action'] ) ? sanitize_key( wp_unslash( (string) $_POST['hs_action'] ) ) : '';
    $notice      = 'done';

    if ( 'purge_cache' === $action_type ) {
        hs_purge_all_caches();
        $notice = 'cache_purged';
    } elseif ( 'sync_pages' === $action_type ) {
        hs_run_one_time_theme_provisioning( true );
        $notice = 'pages_synced';
    }

    wp_safe_redirect(
        add_query_arg(
            array(
                'page'      => 'hs-theme-settings',
                'tab'       => 'performance',
                'hs_notice' => $notice,
            ),
            admin_url( 'admin.php' )
        )
    );
    exit;
} );

/**
 * Render the Unified 6-Tab Settings Interface.
 */
function hs_render_unified_settings_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Unauthorized access.', 'hackersshikkhok-theme' ) );
    }

    $tabs = array(
        'general'     => '🎨 General & Homepage',
        'modules'     => '🧩 Modules & Features',
        'ai'          => '🤖 AI & API Keys',
        'seo'         => '📡 SEO, Sitemaps & AdSense',
        'security'    => '🛡️ Security, Auth & Limits',
        'performance' => '⚡ Performance & Cache',
    );

    $active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( (string) $_GET['tab'] ) ) : 'general';
    if ( ! isset( $tabs[ $active_tab ] ) ) {
        $active_tab = 'general';
    }

    $s      = hs_get_all_settings( true );
    $notice = isset( $_GET['hs_notice'] ) ? sanitize_key( wp_unslash( (string) $_GET['hs_notice'] ) ) : '';
    ?>
    <div class="wrap hs-admin-shell">
        <div class="hs-admin-hero">
            <div>
                <h1>🛡️ HackersShikkhok Permanent Theme &amp; System Settings</h1>
                <p>Centralized control hub for all modular theme features, AI engines, SEO/AdSense automation, security policies, and caching. Core Plugin remains ultra-lightweight (CPTs, Taxonomies, DB Schema &amp; Crons only).</p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
                    <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=hs-control-center' ) ); ?>">🛡️ Control Overview</a>
                    <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=hs-autopilot-bots' ) ); ?>">🤖 Autopilots &amp; Bots</a>
                    <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=hs-api-rotator' ) ); ?>">🔑 API Rotation</a>
                    <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=hs-ppc-calculator' ) ); ?>">💰 PPC Payouts</a>
                </div>
            </div>
            <span class="hs-admin-badge">Theme v<?php echo esc_html( HS_THEME_VERSION ); ?> · PHP <?php echo esc_html( PHP_VERSION ); ?></span>
        </div>

        <?php if ( 'cache_purged' === $notice ) : ?>
            <div class="notice notice-success is-dismissible"><p>✅ All theme transients and object caches have been purged.</p></div>
        <?php elseif ( 'pages_synced' === $notice ) : ?>
            <div class="notice notice-success is-dismissible"><p>✅ Permanent platform pages, legal pages, and rewrite rules synchronized.</p></div>
        <?php endif; ?>

        <?php settings_errors(); ?>

        <nav class="hs-admin-tabs" aria-label="Settings Tabs">
            <?php foreach ( $tabs as $slug => $label ) : ?>
                <a class="hs-admin-tab <?php echo $active_tab === $slug ? 'is-active' : ''; ?>"
                   href="<?php echo esc_url( admin_url( 'admin.php?page=hs-theme-settings&tab=' . $slug ) ); ?>">
                    <?php echo esc_html( $label ); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <form method="post" action="options.php">
            <?php settings_fields( 'hs_theme_master_group' ); ?>
            <input type="hidden" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[_active_tab]" value="<?php echo esc_attr( $active_tab ); ?>" />

            <?php if ( 'general' === $active_tab ) : ?>
                <div class="hs-admin-card">
                    <h2>Brand Identity, UI Colors &amp; Homepage Ranking</h2>
                    <div class="hs-field-grid">
                        <div class="hs-field-box">
                            <label for="hs_brand_name">Brand Name</label>
                            <input type="text" id="hs_brand_name" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[brand_name]" value="<?php echo esc_attr( (string) $s['brand_name'] ); ?>" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_brand_tagline">Global SEO Tagline</label>
                            <input type="text" id="hs_brand_tagline" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[brand_tagline]" value="<?php echo esc_attr( (string) $s['brand_tagline'] ); ?>" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_color_preset">Theme Visual Preset</label>
                            <select id="hs_color_preset" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[color_preset]">
                                <?php foreach ( array( 'cyber_cyan' => 'Cyber Cyan (Default)', 'neon_purple' => 'Neon Purple Matrix', 'emerald_terminal' => 'Emerald Terminal', 'crimson_shield' => 'Crimson Red Team' ) as $val => $lbl ) : ?>
                                    <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $s['color_preset'], $val ); ?>><?php echo esc_html( $lbl ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_primary_color">Primary Neon Color</label>
                            <input type="color" id="hs_primary_color" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[primary_color]" value="<?php echo esc_attr( (string) $s['primary_color'] ); ?>" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_secondary_color">Secondary Color</label>
                            <input type="color" id="hs_secondary_color" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[secondary_color]" value="<?php echo esc_attr( (string) $s['secondary_color'] ); ?>" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_accent_color">Accent Color</label>
                            <input type="color" id="hs_accent_color" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[accent_color]" value="<?php echo esc_attr( (string) $s['accent_color'] ); ?>" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_featured_category">Featured Section Category Slug</label>
                            <input type="text" id="hs_featured_category" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[featured_category]" value="<?php echo esc_attr( (string) $s['featured_category'] ); ?>" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_popular_posts_count">Popular Posts Count</label>
                            <input type="number" min="1" max="24" id="hs_popular_posts_count" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[popular_posts_count]" value="<?php echo esc_attr( (string) $s['popular_posts_count'] ); ?>" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_latest_posts_count">Latest Posts Count</label>
                            <input type="number" min="1" max="30" id="hs_latest_posts_count" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[latest_posts_count]" value="<?php echo esc_attr( (string) $s['latest_posts_count'] ); ?>" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_w_likes">Popular Score Weight: Likes</label>
                            <input type="number" step="0.1" min="0" id="hs_w_likes" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[popular_weight_likes]" value="<?php echo esc_attr( (string) $s['popular_weight_likes'] ); ?>" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_w_comms">Popular Score Weight: Comments</label>
                            <input type="number" step="0.1" min="0" id="hs_w_comms" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[popular_weight_comments]" value="<?php echo esc_attr( (string) $s['popular_weight_comments'] ); ?>" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_w_views">Popular Score Weight: Views</label>
                            <input type="number" step="0.1" min="0" id="hs_w_views" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[popular_weight_views]" value="<?php echo esc_attr( (string) $s['popular_weight_views'] ); ?>" />
                        </div>
                    </div>
                    <div class="hs-toggle-grid">
                        <?php
                        hs_render_toggle_card( 'enable_ai_voice_reader', 'AI Voice Master Reader', 'Shows full-page speech synthesis bar in homepage hero.', $s );
                        hs_render_toggle_card( 'enable_stories_bar', '24h Cyber Stories Bar', 'Displays ephemeral member stories bar in header.', $s );
                        hs_render_toggle_card( 'enable_notifications_bell', 'Real-Time Notification Bell', 'Enables live AJAX notification drawer in header.', $s );
                        ?>
                    </div>
                </div>

            <?php elseif ( 'modules' === $active_tab ) : ?>
                <div class="hs-admin-card">
                    <h2>Modular Feature Switches (Disable Unused Modules for Zero Overhead)</h2>
                    <div class="hs-toggle-grid">
                        <?php
                        $modules_meta = array(
                            'mod_tools_engine'     => array( '2,024+ Developer Tools Engine', 'Universal tool directory, execution engine & conditional assets.' ),
                            'mod_css_rgb_lab'      => array( 'CSS / RGB Neon Lab', 'Interactive CSS gradient, shadow & neon generators.' ),
                            'mod_live_demo'        => array( 'Live Sandbox Preview', 'Sandboxed HTML/CSS/JS live demo runner.' ),
                            'mod_code_library'     => array( 'Code Library & Editor', 'Syntax highlighted snippets & code validator.' ),
                            'mod_academy_lms'      => array( 'Cyber Academy LMS', 'Courses, lessons, progress tracking, certificates & SVG diagrams.' ),
                            'mod_ctf_engine'       => array( 'CTF Flag & Lab Engine', 'Capture-the-Flag verification & XP/BDT rewards.' ),
                            'mod_wallet_economy'   => array( 'Wallet & Payment Webhooks', 'BDT ledger, XP ranks, bKash/Nagad/SSLCommerz HMAC webhooks.' ),
                            'mod_post_pay_counter' => array( 'PostPay Author Earnings', 'Author view/engagement payout calculations.' ),
                            'mod_community_social' => array( 'Universal Social & Stories', 'Follow, like, bookmarks, Q&A forum, gamification & stories.' ),
                            'mod_security_saas'    => array( 'Cyber Command & Security SaaS', 'Passive auditor, CVE intelligence & red team portal.' ),
                            'mod_device_games_lab' => array( 'Device, IoT & Games Lab', 'Hardware/IoT telemetry, CNC lab & interactive browser games.' ),
                            'mod_platform_centers' => array( '28 Platform Centers & App Maker', 'Multi-studio creator hub & generated app CPT integration.' ),
                            'mod_youtube'          => array( 'YouTube Academy Companion', '@HackersShikkhok channel hub & post companion video box.' ),
                            'mod_github'           => array( 'GitHub Sync Integration', 'Repository metadata & snippet integration.' ),
                            'mod_downloads'        => array( 'Secure Download Manager', 'Protected asset & tool package delivery.' ),
                            'mod_user_submissions' => array( 'User Content Submissions', 'Frontend contributor post & tool submission flow.' ),
                        );
                        foreach ( $modules_meta as $key => list( $title, $desc ) ) {
                            hs_render_toggle_card( $key, $title, $desc, $s );
                        }
                        ?>
                    </div>
                </div>

            <?php elseif ( 'ai' === $active_tab ) : ?>
                <div class="hs-admin-card">
                    <h2>AI Engines, API Keys &amp; Autopilot Governance</h2>
                    <div class="hs-danger-zone">
                        <?php hs_render_toggle_card( 'emergency_kill_switch', '🚨 Global Emergency Kill Switch', 'Immediately halts all AI Autopilots, AI Tutor & automated content generation.', $s ); ?>
                    </div>
                    <div class="hs-toggle-grid" style="margin-bottom:20px;">
                        <?php
                        hs_render_toggle_card( 'enable_ai_autopilot', 'AI Autopilot Pipeline', 'Enables automated curriculum, article & quality-gated content generation.', $s );
                        hs_render_toggle_card( 'enable_ai_tutor', 'AI Cyber Tutor REST Assistant', 'Enables interactive AI tutor endpoint in Academy & Tools.', $s );
                        ?>
                    </div>
                    <div class="hs-field-grid">
                        <div class="hs-field-box">
                            <label for="hs_gemini_key">Gemini API Key</label>
                            <input type="password" id="hs_gemini_key" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[gemini_api_key]" value="<?php echo esc_attr( (string) $s['gemini_api_key'] ); ?>" autocomplete="off" />
                            <small>Stored securely in wp_options; never exposed in frontend HTML.</small>
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_openai_key">OpenAI API Key (Fallback)</label>
                            <input type="password" id="hs_openai_key" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[openai_api_key]" value="<?php echo esc_attr( (string) $s['openai_api_key'] ); ?>" autocomplete="off" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_claude_key">Anthropic Claude API Key (Fallback)</label>
                            <input type="password" id="hs_claude_key" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[claude_api_key]" value="<?php echo esc_attr( (string) $s['claude_api_key'] ); ?>" autocomplete="off" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_pub_mode">Autopilot Publishing Mode</label>
                            <select id="hs_pub_mode" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[autopilot_publishing_mode]">
                                <?php foreach ( array( 'draft_first' => 'Draft First (Safest)', 'review_required' => 'Pending Review', 'auto_publish' => 'Auto Publish if Quality Gate Passes' ) as $val => $lbl ) : ?>
                                    <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $s['autopilot_publishing_mode'], $val ); ?>><?php echo esc_html( $lbl ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_min_qual">Minimum Quality Gate Score (50–100)</label>
                            <input type="number" min="50" max="100" id="hs_min_qual" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[autopilot_min_quality]" value="<?php echo esc_attr( (string) $s['autopilot_min_quality'] ); ?>" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_ai_limit">Daily AI Request Cap</label>
                            <input type="number" min="10" max="10000" id="hs_ai_limit" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[ai_daily_request_limit]" value="<?php echo esc_attr( (string) $s['ai_daily_request_limit'] ); ?>" />
                        </div>
                    </div>
                </div>

            <?php elseif ( 'seo' === $active_tab ) : ?>
                <div class="hs-admin-card">
                    <h2>Technical SEO, Sitemaps, Instant Indexing &amp; AdSense</h2>
                    <div class="hs-toggle-grid" style="margin-bottom:20px;">
                        <?php
                        hs_render_toggle_card( 'enable_schema_jsonld', 'Schema.org JSON-LD', 'Outputs structured WebSite, SoftwareApplication & Course schemas.', $s );
                        hs_render_toggle_card( 'enable_og_meta', 'OpenGraph & Meta Descriptions', 'Outputs clean social cards and dynamic descriptions in wp_head.', $s );
                        hs_render_toggle_card( 'enable_xml_sitemap', 'Native XML Sitemaps & Indexing', 'Enables multilingual XML sitemaps and IndexNow/GSC ping engine.', $s );
                        hs_render_toggle_card( 'enable_auto_linking', 'Internal Auto-Linker', 'Automatically links relevant tutorials, tools, and courses.', $s );
                        hs_render_toggle_card( 'enable_auto_ads_txt', 'Auto /ads.txt Endpoint', 'Serves verified AdSense publisher line at /ads.txt automatically.', $s );
                        ?>
                    </div>
                    <div class="hs-field-grid">
                        <div class="hs-field-box">
                            <label for="hs_gsc_token">Google Search Console Verification Token</label>
                            <input type="text" id="hs_gsc_token" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[gsc_verification_token]" value="<?php echo esc_attr( (string) $s['gsc_verification_token'] ); ?>" placeholder="google-site-verification token" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_indexnow_key">IndexNow API Key</label>
                            <input type="text" id="hs_indexnow_key" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[indexnow_api_key]" value="<?php echo esc_attr( (string) $s['indexnow_api_key'] ); ?>" />
                        </div>
                        <div class="hs-field-box">
                            <label for="hs_adsense_id">Google AdSense Publisher ID</label>
                            <input type="text" id="hs_adsense_id" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[adsense_publisher_id]" value="<?php echo esc_attr( (string) $s['adsense_publisher_id'] ); ?>" placeholder="pub-xxxxxxxxxxxxxxxx" />
                        </div>
                    </div>
                </div>

            <?php elseif ( 'security' === $active_tab ) : ?>
                <div class="hs-admin-card">
                    <h2>Security Hardening, Branded Auth &amp; API Rate Limits</h2>
                    <div class="hs-toggle-grid" style="margin-bottom:20px;">
                        <?php
                        hs_render_toggle_card( 'enable_content_lock', 'Content Protection Lock', 'Protects premium academy & course materials.', $s );
                        hs_render_toggle_card( 'enable_video_shield', 'Video Stream Shield', 'Secures course video embeds and token verification.', $s );
                        hs_render_toggle_card( 'enable_branded_auth', 'Native Branded Auth Pages', 'Customizes WordPress login & registration UI.', $s );
                        hs_render_toggle_card( 'enable_branded_email', 'Native Branded HTML Emails', 'Wraps transactional WordPress emails in HackersShikkhok template.', $s );
                        hs_render_toggle_card( 'enable_passkey_geo', 'Passkey & Geo Security', 'Enables WebAuthn passkeys & Bangladesh 64-district geo profile mapping.', $s );
                        ?>
                    </div>
                    <div class="hs-field-grid">
                        <div class="hs-field-box">
                            <label for="hs_rate_limit">REST API Rate Limit (Requests / Minute)</label>
                            <input type="number" min="10" max="5000" id="hs_rate_limit" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[rest_rate_limit_rpm]" value="<?php echo esc_attr( (string) $s['rest_rate_limit_rpm'] ); ?>" />
                        </div>
                    </div>
                </div>

            <?php elseif ( 'performance' === $active_tab ) : ?>
                <div class="hs-admin-card">
                    <h2>Speed, Transient Caching &amp; Core Web Vitals</h2>
                    <div class="hs-toggle-grid" style="margin-bottom:20px;">
                        <?php
                        hs_render_toggle_card( 'enable_transient_cache', 'Transient Query Caching', 'Caches homepage queries, popular scores, and tool catalogs for ultra-fast TTFB.', $s );
                        hs_render_toggle_card( 'conditional_assets_only', 'Strict Conditional Asset Loading', 'Enqueues Tools, Code, Live Demo & LMS CSS/JS only on active routes.', $s );
                        hs_render_toggle_card( 'defer_non_critical_js', 'Defer Non-Critical JavaScript', 'Adds HTML defer strategy to reduce render-blocking JS.', $s );
                        hs_render_toggle_card( 'disable_wp_emojis', 'Disable Core WP Emoji Bloat', 'Removes extra emoji JS/CSS requests on every page.', $s );
                        ?>
                    </div>
                    <div class="hs-field-grid">
                        <div class="hs-field-box">
                            <label for="hs_cache_ttl">Transient Cache TTL (Seconds)</label>
                            <input type="number" min="60" max="86400" id="hs_cache_ttl" name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION ); ?>[cache_ttl_seconds]" value="<?php echo esc_attr( (string) $s['cache_ttl_seconds'] ); ?>" />
                            <small>Recommended: 300 to 1800 seconds. Automatically purged on post save.</small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php submit_button( '💾 Save Settings' ); ?>
        </form>

        <?php if ( 'performance' === $active_tab ) : ?>
            <div class="hs-admin-card">
                <h2>One-Click Maintenance Actions</h2>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                        <input type="hidden" name="action" value="hs_maintenance_action" />
                        <input type="hidden" name="hs_action" value="purge_cache" />
                        <?php wp_nonce_field( 'hs_maintenance_nonce_action', 'hs_maintenance_nonce' ); ?>
                        <button type="submit" class="button button-secondary">🧹 Purge All Theme Transients &amp; Cache</button>
                    </form>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                        <input type="hidden" name="action" value="hs_maintenance_action" />
                        <input type="hidden" name="hs_action" value="sync_pages" />
                        <?php wp_nonce_field( 'hs_maintenance_nonce_action', 'hs_maintenance_nonce' ); ?>
                        <button type="submit" class="button button-primary">🔄 Sync Permanent Pages &amp; Flush Rewrite Rules</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function hs_render_toggle_card( string $key, string $title, string $desc, array $settings ): void {
    $checked = ! empty( $settings[ $key ] );
    $id      = 'hs_toggle_' . $key;
    ?>
    <div class="hs-toggle-item">
        <div>
            <label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
            <small><?php echo esc_html( $desc ); ?></small>
        </div>
        <input type="checkbox"
               id="<?php echo esc_attr( $id ); ?>"
               name="<?php echo esc_attr( HS_MASTER_SETTINGS_OPTION . '[' . $key . ']' ); ?>"
               value="1"
               <?php checked( $checked ); ?> />
    </div>
    <?php
}

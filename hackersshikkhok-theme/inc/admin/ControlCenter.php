<?php
declare(strict_types=1);

namespace HackersShikkhok\Theme\Admin;

use HackersShikkhok\Theme\Core\Settings;
use HackersShikkhok\Theme\Core\SystemHealth;
use HackersShikkhok\Theme\Modules\AI\BotEcosystem;
use HackersShikkhok\Theme\Modules\AI\ApiRotationManager;
use HackersShikkhok\Theme\Modules\AI\AutopilotRegistry;

final class ControlCenter {
    public static function register(): void {
        add_action( 'admin_menu', array( self::class, 'register_menus' ) );
    }

    public static function register_menus(): void {
        add_submenu_page( 'hs-theme-settings', 'Control Center Overview', '🛡️ Control Overview', 'manage_options', 'hs-control-center', array( self::class, 'render_dashboard' ) );
        add_submenu_page( 'hs-theme-settings', 'Autopilots & Bots', '🤖 Autopilots & Bots', 'manage_options', 'hs-autopilot-bots', array( self::class, 'render_autopilot_bots_screen' ) );
        add_submenu_page( 'hs-theme-settings', 'API Rotation & Quota Guard', '🔑 API Rotation & Quota Guard', 'manage_options', 'hs-api-rotator', array( self::class, 'render_api_rotator_screen' ) );
        add_submenu_page( 'hs-theme-settings', 'Homepage Posts Manager', '📰 Homepage Posts & Ranking', 'manage_options', 'hs-homepage-posts', array( self::class, 'render_homepage_posts_screen' ) );
        add_submenu_page( 'hs-theme-settings', 'Performance & Cache Manager', '⚡ Performance & Cache', 'manage_options', 'hs-performance-cache', array( self::class, 'render_performance_screen' ) );
    }

    public static function render_dashboard(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'hackersshikkhok-theme' ) );
        }
        $switches   = Settings::get_master_switches();
        $banner_url = esc_url( HS_THEME_URI . '/assets/images/banner-772x250.png' );
        ?>
        <div class="wrap hs-admin-wrap" style="max-width:1200px;">
            <div style="background:#0b1120;color:#e2e8f0;padding:24px;border-radius:14px;border:1px solid #00f5d4;margin-bottom:20px;">
                <img src="<?php echo $banner_url; ?>" alt="HackersShikkhok Banner" style="max-width:100%;height:auto;border-radius:10px;margin-bottom:14px;display:block;" onerror="this.style.display='none'" />
                <h1 style="color:#ffffff;margin:0 0 8px;"><?php echo esc_html__( 'HackersShikkhok Master Control Center (v5.0.0)', 'hackersshikkhok-theme' ); ?></h1>
                <p style="color:#94a3b8;margin:0 0 10px;"><?php echo esc_html__( 'Developed by HackersShikkhok — Official Website & Documentation: https://hackersshikkhok.com', 'hackersshikkhok-core' ); ?></p>
                <p style="color:#00f5d4;margin:0;font-family:monospace;">28 Platform Centers · 24 AI Autopilots · 8 Autonomous Pro Daemons · Multi-Provider Key Rotation &amp; Quota Guard</p>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(260px, 1fr));gap:16px;">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=hs-search-console-sitemaps' ) ); ?>" style="text-decoration:none;">
                    <div style="background:#0b1120;border:1px solid #38bdf8;border-radius:12px;padding:18px;color:#fff;transition:0.2s;">
                        <span style="font-size:24px;">📡</span>
                        <h3 style="color:#38bdf8;margin:6px 0;">Search Console &amp; Sitemaps</h3>
                        <p style="font-size:12px;color:#94a3b8;margin:0;">/sitemap_index.xml, /sitemap.xml, site verification tokens, and 1-click search engine ping.</p>
                    </div>
                </a>

                <a href="<?php echo esc_url( admin_url( 'admin.php?page=hs-adsense-automation' ) ); ?>" style="text-decoration:none;">
                    <div style="background:#0b1120;border:1px solid #eab308;border-radius:12px;padding:18px;color:#fff;transition:0.2s;">
                        <span style="font-size:24px;">💰</span>
                        <h3 style="color:#eab308;margin:6px 0;">Google AdSense Automation</h3>
                        <p style="font-size:12px;color:#94a3b8;margin:0;">1-click auto-connect with Publisher ID, automatic /ads.txt generation, and pre-approval audit.</p>
                    </div>
                </a>

                <a href="<?php echo esc_url( admin_url( 'admin.php?page=hs-instant-indexing' ) ); ?>" style="text-decoration:none;">
                    <div style="background:#0b1120;border:1px solid #f43f5e;border-radius:12px;padding:18px;color:#fff;transition:0.2s;">
                        <span style="font-size:24px;">⚡</span>
                        <h3 style="color:#f43f5e;margin:6px 0;">Instant Google Indexing</h3>
                        <p style="font-size:12px;color:#94a3b8;margin:0;">Google Indexing API &amp; IndexNow protocol for zero-wait crawling on new post publication.</p>
                    </div>
                </a>

                <a href="<?php echo esc_url( admin_url( 'admin.php?page=hs-autopilot-bots' ) ); ?>" style="text-decoration:none;">
                    <div style="background:#0b1120;border:1px solid #00f5d4;border-radius:12px;padding:18px;color:#fff;transition:0.2s;">
                        <span style="font-size:24px;">🤖</span>
                        <h3 style="color:#00f5d4;margin:6px 0;">24 Autopilots &amp; 8 Daemons</h3>
                        <p style="font-size:12px;color:#94a3b8;margin:0;">Manage cyber defense sentinels, automated curriculum generators, and quality gates.</p>
                    </div>
                </a>

                <a href="<?php echo esc_url( admin_url( 'admin.php?page=hs-api-rotator' ) ); ?>" style="text-decoration:none;">
                    <div style="background:#0b1120;border:1px solid #10b981;border-radius:12px;padding:18px;color:#fff;transition:0.2s;">
                        <span style="font-size:24px;">🔑</span>
                        <h3 style="color:#10b981;margin:6px 0;">API Rotation &amp; Quota Guard</h3>
                        <p style="font-size:12px;color:#94a3b8;margin:0;">Gemini 2.5, Claude, GPT-4o multi-key rotation, 429 auto-cooldown, and cascading failover.</p>
                    </div>
                </a>
            </div>

            <h3 style="color:#fff;margin:24px 0 12px;">Master System Feature Switches (Feature Toggles)</h3>
            <div class="hs-admin-grid" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(260px, 1fr));gap:10px;">
                <?php foreach ( $switches as $key => $enabled ) : ?>
                    <div class="hs-switch-card" style="background:#0b1120;color:#fff;padding:12px 16px;border-radius:10px;border:1px solid #1e293b;display:flex;justify-content:space-between;align-items:center;">
                        <strong style="font-size:12px;"><?php echo esc_html( strtoupper( str_replace( '_', ' ', $key ) ) ); ?></strong>
                        <span style="color:<?php echo $enabled ? '#00f5d4' : '#f43f5e'; ?>;font-weight:700;font-size:12px;padding:2px 8px;border-radius:6px;background:rgba(0,245,212,0.1);"><?php echo $enabled ? 'ACTIVE' : 'OFF'; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render Autopilot and Autonomous Bots Management Screen
     */
    public static function render_autopilot_bots_screen(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'hackersshikkhok-core' ) );
        }
        $bots = BotEcosystem::get_all_bots();
        $autopilots = AutopilotRegistry::get_all_units();
        $logs = BotEcosystem::get_logs();
        ?>
        <div class="wrap hs-admin-wrap" style="max-width:1200px;color:#e2e8f0;">
            <div style="background:#0b1120;padding:20px;border-radius:12px;border:1px solid #00f5d4;margin-bottom:20px;">
                <h1 style="color:#fff;margin:0 0 6px;">🤖 8 Autonomous Pro Daemons &amp; 24 AI Autopilot Pipelines</h1>
                <p style="color:#94a3b8;font-size:13px;margin:0;">
                    Zero-maintenance autonomous operations: continuous threat sentinels, automated curriculum ingestion, and AdSense policy protection.
                </p>
            </div>

            <!-- SECTION 1: 8 AUTONOMOUS BOTS -->
            <h2 style="color:#00f5d4;margin-bottom:12px;">🛡️ 8 Master Autonomous Daemons (Background Workers)</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:16px;margin-bottom:30px;">
                <?php foreach ( $bots as $bot ) : ?>
                    <div style="background:#0b1120;border:1px solid #1e293b;border-radius:12px;padding:16px;display:flex;flex-direction:column;justify-content:space-between;">
                        <div>
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                <strong style="color:#00f5d4;font-size:14px;"><?php echo esc_html( $bot['nameEn'] ?? ($bot['name'] ?? 'Autonomous Daemon') ); ?></strong>
                                <span style="font-size:10px;padding:2px 6px;border-radius:4px;background:#10b981/20;color:#10b981;font-weight:700;">● <?php echo esc_html( $bot['status'] ); ?></span>
                            </div>
                            <span style="font-size:11px;color:#64748b;font-family:monospace;display:block;margin-bottom:6px;">⏱️ Schedule: <?php echo esc_html( $bot['frequency'] ); ?></span>
                            <p style="font-size:12px;color:#cbd5e1;line-height:1.5;margin:0 0 10px;"><?php echo esc_html( $bot['roleEn'] ?? ($bot['role'] ?? 'Background autonomous daemon.') ); ?></p>
                        </div>
                        <div style="border-top:1px solid #1e293b;padding-top:10px;display:flex;justify-content:space-between;align-items:center;font-size:11px;font-family:monospace;">
                            <span style="color:#94a3b8;">Processed: <strong style="color:#fff;"><?php echo number_format( (int) $bot['itemsProcessed'] ); ?></strong></span>
                            <span style="color:#00f5d4;">Last Run: <?php echo esc_html( $bot['lastRun'] ); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- SECTION 2: 24 AUTOPILOTS TABLE -->
            <h2 style="color:#10b981;margin-bottom:12px;">⚡ 24 AI Autopilot &amp; Content Pipelines</h2>
            <div style="background:#0b1120;border:1px solid #1e293b;border-radius:12px;overflow:hidden;margin-bottom:30px;">
                <table style="width:100%;border-collapse:collapse;font-size:12px;text-align:left;">
                    <thead>
                        <tr style="background:#050811;color:#00f5d4;border-bottom:1px solid #1e293b;">
                            <th style="padding:10px 14px;">ID / Name</th>
                            <th style="padding:10px 14px;">Platform Center</th>
                            <th style="padding:10px 14px;">Publishing Mode</th>
                            <th style="padding:10px 14px;">Min Quality Score</th>
                            <th style="padding:10px 14px;">Daily Limit</th>
                            <th style="padding:10px 14px;">Output Generation</th>
                            <th style="padding:10px 14px;">Search Engine Grounding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $autopilots as $ap ) : ?>
                            <tr style="border-bottom:1px solid #1e293b;color:#cbd5e1;">
                                <td style="padding:10px 14px;font-weight:700;color:#fff;">
                                    <?php echo esc_html( strtoupper( $ap['id'] ) ); ?>: <?php echo esc_html( $ap['nameEn'] ?? ($ap['name'] ?? 'Autopilot Engine') ); ?>
                                </td>
                                <td style="padding:10px 14px;font-family:monospace;color:#00f5d4;">
                                    /<?php echo esc_html( $ap['centerSlug'] ); ?>
                                </td>
                                <td style="padding:10px 14px;">
                                    <span style="padding:2px 6px;border-radius:4px;background:#1e293b;color:#a78bfa;font-family:monospace;"><?php echo esc_html( $ap['mode'] ); ?></span>
                                </td>
                                <td style="padding:10px 14px;font-weight:700;color:#10b981;">
                                    <?php echo esc_html( (string) $ap['minQualityScore'] ); ?>/100
                                </td>
                                <td style="padding:10px 14px;">
                                    <?php echo esc_html( (string) $ap['dailyLimit'] ); ?> / day
                                </td>
                                <td style="padding:10px 14px;">
                                    <?php echo esc_html( $ap['outputType'] ); ?>
                                </td>
                                <td style="padding:10px 14px;color:#94a3b8;">
                                    <?php echo esc_html( $ap['googleSync'] ); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- SECTION 3: RECENT BOT ACTIVITY AUDIT LOG -->
            <h2 style="color:#f59e0b;margin-bottom:12px;">📋 Daemon Execution &amp; Audit Telemetry</h2>
            <div style="background:#050811;border:1px solid #1e293b;border-radius:12px;padding:14px;max-height:280px;overflow-y:auto;font-family:monospace;font-size:12px;">
                <?php if ( empty( $logs ) ) : ?>
                    <div style="color:#64748b;">Autonomous daemon telemetry and audit logs will stream here.</div>
                <?php else : ?>
                    <?php foreach ( $logs as $l ) : ?>
                        <div style="padding:6px 0;border-bottom:1px solid #111827;display:flex;gap:12px;">
                            <span style="color:#64748b;">[<?php echo esc_html( $l['timestamp'] ); ?>]</span>
                            <strong style="color:#00f5d4;"><?php echo esc_html( $l['bot_name'] ); ?>:</strong>
                            <span style="color:#e2e8f0;"><?php echo esc_html( $l['details'] ); ?></span>
                            <span style="color:#a78bfa;margin-left:auto;"><?php echo esc_html( (string) $l['duration_ms'] ); ?>ms</span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render API Key Pool, Rotation & Model Failover Screen
     */
    public static function render_api_rotator_screen(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'hackersshikkhok-core' ) );
        }
        $pools = ApiRotationManager::get_key_pools();
        $telemetry = ApiRotationManager::get_telemetry();
        ?>
        <div class="wrap hs-admin-wrap" style="max-width:1200px;color:#e2e8f0;">
            <div style="background:#0b1120;padding:20px;border-radius:12px;border:1px solid #10b981;margin-bottom:20px;">
                <h1 style="color:#fff;margin:0 0 6px;">🔑 AI API Key Rotation, Quota Guard &amp; Model Failover Hub</h1>
                <p style="color:#94a3b8;font-size:13px;margin:0;">
                    Automatic fallback across keys on HTTP 429 rate-limiting, and zero-downtime cascading failover across AI model tiers and cloud providers.
                </p>
            </div>

            <!-- LIVE TELEMETRY COUNTERS -->
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));gap:14px;margin-bottom:24px;">
                <div style="background:#0b1120;border:1px solid #1e293b;padding:14px;border-radius:10px;">
                    <span style="font-size:11px;color:#94a3b8;display:block;">Total Dispatched Requests</span>
                    <strong style="font-size:22px;color:#00f5d4;"><?php echo number_format( (int) $telemetry['total_requests'] ); ?></strong>
                </div>
                <div style="background:#0b1120;border:1px solid #1e293b;padding:14px;border-radius:10px;">
                    <span style="font-size:11px;color:#94a3b8;display:block;">Successful Responses</span>
                    <strong style="font-size:22px;color:#10b981;"><?php echo number_format( (int) $telemetry['successful_requests'] ); ?></strong>
                </div>
                <div style="background:#0b1120;border:1px solid #1e293b;padding:14px;border-radius:10px;">
                    <span style="font-size:11px;color:#94a3b8;display:block;">429 Rate-Limit Rotations</span>
                    <strong style="font-size:22px;color:#f59e0b;"><?php echo number_format( (int) $telemetry['rate_limit_hits'] ); ?></strong>
                </div>
                <div style="background:#0b1120;border:1px solid #1e293b;padding:14px;border-radius:10px;">
                    <span style="font-size:11px;color:#94a3b8;display:block;">Model / Provider Failovers</span>
                    <strong style="font-size:22px;color:#a78bfa;"><?php echo number_format( (int) $telemetry['model_failovers'] ); ?></strong>
                </div>
            </div>

            <!-- HIERARCHICAL FAILOVER DIAGRAM -->
            <div style="background:#050811;border:1px solid #1e293b;padding:16px;border-radius:12px;margin-bottom:24px;">
                <strong style="color:#00f5d4;display:block;margin-bottom:8px;">🔄 Cascading AI Provider &amp; Model Failover Hierarchy:</strong>
                <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;font-size:12px;font-family:monospace;">
                    <span style="background:#0b1120;border:1px solid #00f5d4;color:#00f5d4;padding:4px 10px;border-radius:6px;">1. Gemini 2.5 Pro (Primary)</span>
                    <span>➔</span>
                    <span style="background:#0b1120;border:1px solid #10b981;color:#10b981;padding:4px 10px;border-radius:6px;">2. Gemini 2.5 Flash</span>
                    <span>➔</span>
                    <span style="background:#0b1120;border:1px solid #3b82f6;color:#3b82f6;padding:4px 10px;border-radius:6px;">3. Gemini 2.0 Flash Lite</span>
                    <span>➔</span>
                    <span style="background:#0b1120;border:1px solid #7c3aed;color:#7c3aed;padding:4px 10px;border-radius:6px;">4. OpenAI GPT-4o / Mini</span>
                    <span>➔</span>
                    <span style="background:#0b1120;border:1px solid #f43f5e;color:#f43f5e;padding:4px 10px;border-radius:6px;">5. Claude 3.5 / 3.7</span>
                    <span>➔</span>
                    <span style="background:#0b1120;border:1px solid #eab308;color:#eab308;padding:4px 10px;border-radius:6px;">6. Groq / DeepSeek</span>
                    <span>➔</span>
                    <span style="background:#0b1120;border:1px solid #64748b;color:#e2e8f0;padding:4px 10px;border-radius:6px;">7. Local Knowledge Engine (100% Uptime)</span>
                </div>
            </div>

            <!-- KEY POOLS PER PROVIDER -->
            <h2 style="color:#fff;margin-bottom:12px;">🔑 Active API Key Pools per Provider</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:16px;">
                <?php foreach ( $pools as $provider => $pool_data ) : ?>
                    <div style="background:#0b1120;border:1px solid #1e293b;border-radius:12px;padding:16px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;border-bottom:1px solid #1e293b;padding-bottom:8px;">
                            <strong style="color:#00f5d4;font-size:15px;text-transform:uppercase;"><?php echo esc_html( $provider ); ?> KEY POOL</strong>
                            <span style="font-size:11px;font-family:monospace;color:#94a3b8;"><?php echo count( $pool_data['keys'] ?? array() ); ?> Keys Loaded</span>
                        </div>

                        <?php if ( empty( $pool_data['keys'] ) ) : ?>
                            <p style="font-size:12px;color:#64748b;margin:0;">No external keys loaded for this provider. Configure environment variables or credentials.</p>
                        <?php else : ?>
                            <div style="display:flex;flex-direction:column;gap:8px;">
                                <?php foreach ( $pool_data['keys'] as $k ) : ?>
                                    <div style="background:#050811;border:1px solid #1e293b;border-radius:8px;padding:8px 12px;font-size:12px;display:flex;justify-content:space-between;align-items:center;">
                                        <div>
                                            <strong style="color:#fff;display:block;"><?php echo esc_html( $k['label'] ); ?></strong>
                                            <span style="font-family:monospace;color:#64748b;font-size:11px;">
                                                ****<?php echo esc_html( substr( (string) $k['key'], -4 ) ); ?> · Invocations: <?php echo esc_html( (string) ( $k['usage_count'] ?? 0 ) ); ?>
                                            </span>
                                        </div>
                                        <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:4px;background:<?php echo ( $k['status'] === 'active' ) ? 'rgba(16,185,129,0.2)' : 'rgba(244,63,94,0.2)'; ?>;color:<?php echo ( $k['status'] === 'active' ) ? '#10b981' : '#f43f5e'; ?>;">
                                            <?php echo esc_html( strtoupper( $k['status'] ) ); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    public static function render_homepage_posts_screen(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'hackersshikkhok-core' ) );
        }

        if ( isset( $_POST['hs_save_homepage_settings'] ) && check_admin_referer( 'hs_homepage_settings_nonce' ) ) {
            $feat_cat_val = sanitize_text_field( wp_unslash( $_POST['hs_featured_category'] ?? 'notice' ) );
            $w_likes_val  = floatval( $_POST['hs_popular_weight_likes'] ?? 1.0 );
            $w_comms_val  = floatval( $_POST['hs_popular_weight_comments'] ?? 1.5 );
            $w_views_val  = floatval( $_POST['hs_popular_weight_views'] ?? 0.5 );
            $pop_cnt_val  = intval( $_POST['hs_popular_posts_count'] ?? 6 );
            $lat_cnt_val  = intval( $_POST['hs_latest_posts_count'] ?? 8 );

            update_option( 'hs_featured_category', $feat_cat_val );
            update_option( 'hs_popular_weight_likes', $w_likes_val );
            update_option( 'hs_popular_weight_comments', $w_comms_val );
            update_option( 'hs_popular_weight_views', $w_views_val );
            update_option( 'hs_popular_posts_count', $pop_cnt_val );
            update_option( 'hs_latest_posts_count', $lat_cnt_val );

            if ( function_exists( 'hs_get_all_settings' ) && defined( 'HS_MASTER_SETTINGS_OPTION' ) ) {
                $master = hs_get_all_settings( true );
                $master['featured_category']       = $feat_cat_val;
                $master['popular_weight_likes']    = $w_likes_val;
                $master['popular_weight_comments'] = $w_comms_val;
                $master['popular_weight_views']    = $w_views_val;
                $master['popular_posts_count']     = $pop_cnt_val;
                $master['latest_posts_count']      = $lat_cnt_val;
                update_option( HS_MASTER_SETTINGS_OPTION, $master );
            }
            if ( function_exists( 'hs_purge_all_caches' ) ) {
                hs_purge_all_caches();
            }

            echo '<div class="notice notice-success is-dismissible"><p><strong>✓ Homepage posts ranking &amp; display settings saved successfully!</strong></p></div>';
        }

        $feat_cat = get_option( 'hs_featured_category', 'notice' );
        $w_likes  = get_option( 'hs_popular_weight_likes', 1.0 );
        $w_comms  = get_option( 'hs_popular_weight_comments', 1.5 );
        $w_views  = get_option( 'hs_popular_weight_views', 0.5 );
        $pop_cnt  = get_option( 'hs_popular_posts_count', 6 );
        $lat_cnt  = get_option( 'hs_latest_posts_count', 8 );
        ?>
        <div class="wrap hs-admin-wrap" style="max-width:1000px; color:#e2e8f0;">
            <div style="background:#0b1120; padding:24px; border-radius:14px; border:1px solid #00f5d4; margin-bottom:20px;">
                <h1 style="color:#fff; margin:0 0 8px;">📰 Homepage Posts, Notice Category &amp; Ranking Manager</h1>
                <p style="color:#94a3b8; margin:0; font-size:13px;">Configure Featured Notice category, Popular posts automatic ranking formula (likes, comments, views weights), and display counts for Homepage sections.</p>
            </div>

            <form method="post" action="" style="background:#0b1120; padding:24px; border-radius:14px; border:1px solid #1e293b;">
                <?php wp_nonce_field( 'hs_homepage_settings_nonce' ); ?>
                
                <table class="form-table" style="color:#fff;">
                    <tr>
                        <th scope="row"><label for="hs_featured_category" style="color:#00f5d4; font-weight:700;">Featured Posts Category (Notice):</label></th>
                        <td>
                            <input type="text" name="hs_featured_category" id="hs_featured_category" value="<?php echo esc_attr( $feat_cat ); ?>" style="width:100%; max-width:400px; background:#050811; border:1px solid #38bdf8; color:#fff; padding:8px 12px; border-radius:6px;" />
                            <p class="description" style="color:#94a3b8; font-size:12px;">Category slug or ID for Featured Notice posts displayed on the home page.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label style="color:#eab308; font-weight:700;">Popular Posts Ranking Formula Weights:</label></th>
                        <td>
                            <div style="display:flex; gap:16px; align-items:center;">
                                <div>
                                    <label style="display:block; font-size:11px; color:#94a3b8;">Likes Weight</label>
                                    <input type="number" step="0.1" name="hs_popular_weight_likes" value="<?php echo esc_attr( (string) $w_likes ); ?>" style="width:80px; background:#050811; border:1px solid #eab308; color:#fff; padding:6px; border-radius:6px;" />
                                </div>
                                <div>
                                    <label style="display:block; font-size:11px; color:#94a3b8;">Comments Weight</label>
                                    <input type="number" step="0.1" name="hs_popular_weight_comments" value="<?php echo esc_attr( (string) $w_comms ); ?>" style="width:80px; background:#050811; border:1px solid #eab308; color:#fff; padding:6px; border-radius:6px;" />
                                </div>
                                <div>
                                    <label style="display:block; font-size:11px; color:#94a3b8;">Views Weight</label>
                                    <input type="number" step="0.1" name="hs_popular_weight_views" value="<?php echo esc_attr( (string) $w_views ); ?>" style="width:80px; background:#050811; border:1px solid #eab308; color:#fff; padding:6px; border-radius:6px;" />
                                </div>
                            </div>
                            <p class="description" style="color:#94a3b8; font-size:12px; margin-top:6px;">Popular posts score = (Likes × Weight) + (Comments × Weight) + (Views × Weight).</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hs_popular_posts_count" style="color:#eab308; font-weight:700;">Popular Posts Count:</label></th>
                        <td>
                            <input type="number" name="hs_popular_posts_count" id="hs_popular_posts_count" value="<?php echo esc_attr( (string) $pop_cnt ); ?>" style="width:100px; background:#050811; border:1px solid #eab308; color:#fff; padding:8px 12px; border-radius:6px;" />
                            <p class="description" style="color:#94a3b8; font-size:12px;">Number of top popular posts to show on the homepage.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hs_latest_posts_count" style="color:#10b981; font-weight:700;">Latest Posts Count:</label></th>
                        <td>
                            <input type="number" name="hs_latest_posts_count" id="hs_latest_posts_count" value="<?php echo esc_attr( (string) $lat_cnt ); ?>" style="width:100px; background:#050811; border:1px solid #10b981; color:#fff; padding:8px 12px; border-radius:6px;" />
                            <p class="description" style="color:#94a3b8; font-size:12px;">Number of chronological latest posts to show on the homepage.</p>
                        </td>
                    </tr>
                </table>

                <p class="submit" style="margin-top:24px;">
                    <input type="submit" name="hs_save_homepage_settings" id="hs_save_homepage_settings" class="button button-primary" value="Save Homepage Configuration" style="background:#00f5d4; color:#050811; font-weight:800; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;" />
                </p>
            </form>
        </div>
        <?php
    }

    public static function render_performance_screen(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'hackersshikkhok-core' ) );
        }

        $msg = '';
        if ( isset( $_POST['hs_purge_cache'] ) && check_admin_referer( 'hs_purge_cache_nonce' ) ) {
            \HackersShikkhok\Theme\Core\CacheCompatibilityManager::purge_all();
            $msg = '<div class="notice notice-success is-dismissible"><p><strong>✓ All site caches, transients, and object cache purged successfully!</strong></p></div>';
        }
        ?>
        <div class="wrap hs-admin-wrap" style="max-width:800px; color:#e2e8f0;">
            <div style="background:#0b1120; padding:24px; border-radius:14px; border:1px solid #00f5d4; margin-bottom:20px;">
                <h1 style="color:#fff; margin:0 0 8px;">⚡ Performance &amp; Cache Manager</h1>
                <p style="color:#94a3b8; margin:0; font-size:13px;">Manage site caching and purge performance data manually.</p>
            </div>
            <?php echo $msg; ?>
            <div style="background:#0b1120; padding:24px; border-radius:14px; border:1px solid #1e293b; text-align:center;">
                <form method="post" action="">
                    <?php wp_nonce_field( 'hs_purge_cache_nonce' ); ?>
                    <h2 style="color:#fff;">Purge All Caches</h2>
                    <p style="color:#94a3b8; margin-bottom:24px;">Clear all transients, object cache, and temporary cached fragments for the entire site.</p>
                    <input type="submit" name="hs_purge_cache" class="button button-primary button-hero" value="Purge Site Cache Now" style="background:#f43f5e; border-color:#f43f5e; color:#fff; font-weight:800; padding:12px 24px; border-radius:8px; cursor:pointer;" />
                </form>
            </div>
        </div>
        <?php
    }
}

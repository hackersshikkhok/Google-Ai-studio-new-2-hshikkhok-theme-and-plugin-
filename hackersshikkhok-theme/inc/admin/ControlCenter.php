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
        add_submenu_page( 'hs-theme-settings', 'Moderation & Reports Queue', '🚩 Moderation & Reports', 'manage_options', 'hs-moderation-queue', array( self::class, 'render_moderation_screen' ) );
        add_submenu_page( 'hs-theme-settings', 'Platform Analytics Overview', '📊 Platform Analytics', 'manage_options', 'hs-platform-analytics', array( self::class, 'render_analytics_screen' ) );
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

        global $wpdb;
        $cleanup_msg = '';

        if ( isset( $_POST['hs_kill_autopilot_crons'] ) && check_admin_referer( 'hs_cleanup_autopilot_nonce' ) ) {
            wp_clear_scheduled_hook( 'hs_daily_autopilot_batch_event' );
            wp_clear_scheduled_hook( 'hs_core_autopilot_cron_tick' );
            update_option( 'hs_enable_ai_autopilot', false );
            update_option( 'hs_autopilot_enabled', false );
            $master = get_option( 'hs_theme_master_settings', array() );
            if ( is_array( $master ) ) {
                $master['enable_ai_autopilot'] = 0;
                update_option( 'hs_theme_master_settings', $master );
            }
            $cleanup_msg = '<div class="notice notice-success is-dismissible"><p><strong>✓ All automated post-generation crons cleared &amp; disabled!</strong></p></div>';
        }

        if ( isset( $_POST['hs_trash_thin_autoposts'] ) && check_admin_referer( 'hs_cleanup_autopilot_nonce' ) ) {
            $batch_ids = get_posts( array(
                'post_type'      => array( 'post', 'tutorials', 'hs_question' ),
                'post_status'    => array( 'publish', 'draft', 'pending' ),
                'posts_per_page' => 250,
                'fields'         => 'ids',
                'meta_query'     => array(
                    array(
                        'key'     => '_hs_ai_generated',
                        'compare' => 'EXISTS',
                    ),
                ),
            ) );
            $trashed_count = 0;
            foreach ( $batch_ids as $pid ) {
                if ( ! has_post_thumbnail( (int) $pid ) ) {
                    wp_trash_post( (int) $pid );
                    $trashed_count++;
                }
            }
            $cleanup_msg = '<div class="notice notice-success is-dismissible"><p><strong>✓ Moved ' . (int) $trashed_count . ' auto-generated thin/no-thumbnail posts to Trash (you can review or empty Trash in Posts → Trash).</strong></p></div>';
        }

        $ai_no_thumb_count = (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_hs_ai_generated'
             LEFT JOIN {$wpdb->postmeta} pt ON p.ID = pt.post_id AND pt.meta_key = '_thumbnail_id'
             WHERE p.post_status IN ('publish','draft','pending') AND pt.meta_id IS NULL"
        );

        $bots = BotEcosystem::get_all_bots();
        $autopilots = AutopilotRegistry::get_all_units();
        $logs = BotEcosystem::get_logs();
        ?>
        <div class="wrap hs-admin-wrap" style="max-width:1200px;color:#e2e8f0;">
            <?php echo $cleanup_msg; ?>

            <div style="background:#0b1120;padding:20px;border-radius:12px;border:1px solid #f43f5e;margin-bottom:20px;">
                <h2 style="color:#fb7185;margin:0 0 8px;">🛑 Auto-Post Spam Protection &amp; Cleanup Utility</h2>
                <p style="color:#94a3b8;font-size:13px;margin:0 0 14px;">
                    Hourly automated thin/duplicate posting is now disabled in code. Detected <strong style="color:#fff;"><?php echo number_format( $ai_no_thumb_count ); ?></strong> AI-generated posts without featured images currently in Publish/Draft/Pending.
                </p>
                <form method="post" style="display:flex;gap:12px;flex-wrap:wrap;">
                    <?php wp_nonce_field( 'hs_cleanup_autopilot_nonce' ); ?>
                    <button type="submit" name="hs_kill_autopilot_crons" value="1" class="button button-secondary">⛔ Clear &amp; Stop All Autopilot Crons</button>
                    <?php if ( $ai_no_thumb_count > 0 ) : ?>
                        <button type="submit" name="hs_trash_thin_autoposts" value="1" class="button button-primary" style="background:#ef4444;border-color:#ef4444;">🗑️ Move Thin Auto-Posts to Trash (Batch 250)</button>
                    <?php endif; ?>
                </form>
            </div>

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

        $section_labels = array(
            'hero_shortcuts'        => '01. 4 Core Pillars Hero Shortcuts',
            'trending_slider'       => '02. Trending Posts Slider',
            'featured_notices'      => '03. Featured Notices & Bulletins',
            'categories'            => '04. Knowledge Faculties & Categories',
            'latest_feed'           => '05. Latest Posts Feed & Filter Tabs',
            'popular_posts'         => '06. Popular & High Engagement Posts',
            'qa_showcase'           => '07. Community Q&A Showcase',
            'featured_tools'        => '08. Essential Featured Tools',
            'tools_gateway'         => '09. Explore 2,024+ Tools Gateway',
            'academy_courses'       => '10. Featured Academy Courses',
            'tutorials'             => '11. Latest Tutorials & Guides',
            'video_hub'             => '12. Video Learning Center',
            'community_leaderboard' => '13. Top Contributors Leaderboard',
        );

        if ( isset( $_POST['hs_save_homepage_settings'] ) && check_admin_referer( 'hs_homepage_settings_nonce' ) ) {
            $feat_cat_val    = sanitize_text_field( wp_unslash( $_POST['hs_featured_category'] ?? 'notice' ) );
            $curated_ids_val = sanitize_text_field( wp_unslash( $_POST['hs_curated_post_ids'] ?? '' ) );
            $w_likes_val     = floatval( $_POST['hs_popular_weight_likes'] ?? 1.0 );
            $w_comms_val     = floatval( $_POST['hs_popular_weight_comments'] ?? 1.5 );
            $w_views_val     = floatval( $_POST['hs_popular_weight_views'] ?? 0.5 );
            $pop_cnt_val     = intval( $_POST['hs_popular_posts_count'] ?? 6 );
            $lat_cnt_val     = intval( $_POST['hs_latest_posts_count'] ?? 8 );

            $raw_order   = isset( $_POST['hs_section_order'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['hs_section_order'] ) ) : '';
            $order_parts = array_filter( array_map( 'sanitize_key', explode( ',', $raw_order ) ) );
            $clean_order = array();
            foreach ( $order_parts as $sk ) {
                if ( isset( $section_labels[ $sk ] ) && ! in_array( $sk, $clean_order, true ) ) {
                    $clean_order[] = $sk;
                }
            }
            foreach ( array_keys( $section_labels ) as $sk ) {
                if ( ! in_array( $sk, $clean_order, true ) ) {
                    $clean_order[] = $sk;
                }
            }

            update_option( 'hs_featured_category', $feat_cat_val );
            update_option( 'hs_popular_weight_likes', $w_likes_val );
            update_option( 'hs_popular_weight_comments', $w_comms_val );
            update_option( 'hs_popular_weight_views', $w_views_val );
            update_option( 'hs_popular_posts_count', $pop_cnt_val );
            update_option( 'hs_latest_posts_count', $lat_cnt_val );

            if ( function_exists( 'hs_get_all_settings' ) && defined( 'HS_MASTER_SETTINGS_OPTION' ) ) {
                $master = hs_get_all_settings( true );
                $master['featured_category']       = $feat_cat_val;
                $master['home_curated_post_ids']   = $curated_ids_val;
                $master['home_section_order']      = $clean_order;
                $master['popular_weight_likes']    = $w_likes_val;
                $master['popular_weight_comments'] = $w_comms_val;
                $master['popular_weight_views']    = $w_views_val;
                $master['popular_posts_count']     = $pop_cnt_val;
                $master['latest_posts_count']      = $lat_cnt_val;
                foreach ( array_keys( $section_labels ) as $sk ) {
                    $master[ 'home_show_' . $sk ] = isset( $_POST[ 'hs_show_' . $sk ] ) ? 1 : 0;
                }
                update_option( HS_MASTER_SETTINGS_OPTION, $master );
            }
            if ( function_exists( 'hs_purge_all_caches' ) ) {
                hs_purge_all_caches();
            }

            echo '<div class="notice notice-success is-dismissible"><p><strong>✓ Homepage section order, curated posts &amp; ranking settings saved successfully!</strong></p></div>';
        }

        $master      = function_exists( 'hs_get_all_settings' ) ? hs_get_all_settings( true ) : array();
        $feat_cat    = get_option( 'hs_featured_category', 'notice' );
        $curated_ids = (string) ( $master['home_curated_post_ids'] ?? '' );
        $w_likes     = get_option( 'hs_popular_weight_likes', 1.0 );
        $w_comms     = get_option( 'hs_popular_weight_comments', 1.5 );
        $w_views     = get_option( 'hs_popular_weight_views', 0.5 );
        $pop_cnt     = get_option( 'hs_popular_posts_count', 6 );
        $lat_cnt     = get_option( 'hs_latest_posts_count', 8 );
        $saved_order = ! empty( $master['home_section_order'] ) && is_array( $master['home_section_order'] )
            ? $master['home_section_order']
            : array_keys( $section_labels );
        ?>
        <div class="wrap hs-admin-wrap" style="max-width:1050px; color:#e2e8f0;">
            <div style="background:#0b1120; padding:24px; border-radius:14px; border:1px solid #00f5d4; margin-bottom:20px;">
                <h1 style="color:#fff; margin:0 0 8px;">📰 Homepage Layout, Curated Posts &amp; Ranking Manager</h1>
                <p style="color:#94a3b8; margin:0; font-size:13px;">Reorder homepage sections dynamically, pin curated editorial Post IDs, toggle individual sections, and tune engagement ranking weights.</p>
            </div>

            <form method="post" action="" style="background:#0b1120; padding:24px; border-radius:14px; border:1px solid #1e293b;">
                <?php wp_nonce_field( 'hs_homepage_settings_nonce' ); ?>
                <input type="hidden" name="hs_section_order" id="hs-section-order-input" value="<?php echo esc_attr( implode( ',', $saved_order ) ); ?>" />

                <table class="form-table" style="color:#fff;">
                    <tr>
                        <th scope="row"><label for="hs_featured_category" style="color:#00f5d4; font-weight:700;">Featured Posts Category (Notice):</label></th>
                        <td>
                            <input type="text" name="hs_featured_category" id="hs_featured_category" value="<?php echo esc_attr( (string) $feat_cat ); ?>" style="width:100%; max-width:400px; background:#050811; border:1px solid #38bdf8; color:#fff; padding:8px 12px; border-radius:6px;" />
                            <p class="description" style="color:#94a3b8; font-size:12px;">Category slug for Featured Notices displayed on the home page.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hs_curated_post_ids" style="color:#00f5d4; font-weight:700;">Pinned Curated Post IDs (Optional):</label></th>
                        <td>
                            <input type="text" name="hs_curated_post_ids" id="hs_curated_post_ids" value="<?php echo esc_attr( $curated_ids ); ?>" placeholder="e.g. 142, 198, 205" style="width:100%; max-width:400px; background:#050811; border:1px solid #38bdf8; color:#fff; padding:8px 12px; border-radius:6px;" />
                            <p class="description" style="color:#94a3b8; font-size:12px;">Comma-separated Post IDs to force-pin in the Featured Notices showcase before fallback category posts.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label style="color:#eab308; font-weight:700;">Popular Posts Ranking Formula Weights:</label></th>
                        <td>
                            <div style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
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
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hs_popular_posts_count" style="color:#eab308; font-weight:700;">Popular / Latest Counts:</label></th>
                        <td>
                            <div style="display:flex; gap:16px; flex-wrap:wrap;">
                                <label style="color:#94a3b8; font-size:12px;">Popular: <input type="number" name="hs_popular_posts_count" id="hs_popular_posts_count" value="<?php echo esc_attr( (string) $pop_cnt ); ?>" style="width:80px; background:#050811; border:1px solid #eab308; color:#fff; padding:6px; border-radius:6px;" /></label>
                                <label style="color:#94a3b8; font-size:12px;">Latest: <input type="number" name="hs_latest_posts_count" id="hs_latest_posts_count" value="<?php echo esc_attr( (string) $lat_cnt ); ?>" style="width:80px; background:#050811; border:1px solid #10b981; color:#fff; padding:6px; border-radius:6px;" /></label>
                            </div>
                        </td>
                    </tr>
                </table>

                <h3 style="color:#00f5d4; margin:24px 0 10px;">🔀 Homepage Section Order &amp; Visibility</h3>
                <div id="hs-section-order-list" style="display:flex; flex-direction:column; gap:8px; max-width:720px;">
                    <?php foreach ( $saved_order as $sk ) :
                        if ( ! isset( $section_labels[ $sk ] ) ) continue;
                        $enabled = ! isset( $master[ 'home_show_' . $sk ] ) || ! empty( $master[ 'home_show_' . $sk ] );
                    ?>
                        <div class="hs-order-row" data-sec="<?php echo esc_attr( $sk ); ?>" style="background:#050811; border:1px solid #1e293b; border-radius:10px; padding:10px 14px; display:flex; align-items:center; justify-content:space-between; gap:12px;">
                            <label style="display:flex; align-items:center; gap:10px; color:#e2e8f0; font-weight:700; font-size:13px; cursor:pointer;">
                                <input type="checkbox" name="hs_show_<?php echo esc_attr( $sk ); ?>" value="1" <?php checked( $enabled ); ?> />
                                <span><?php echo esc_html( $section_labels[ $sk ] ); ?></span>
                            </label>
                            <div style="display:flex; gap:6px;">
                                <button type="button" class="button hs-move-up" title="Move Up">↑</button>
                                <button type="button" class="button hs-move-down" title="Move Down">↓</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <p class="submit" style="margin-top:24px;">
                    <input type="submit" name="hs_save_homepage_settings" id="hs_save_homepage_settings" class="button button-primary" value="Save Homepage Configuration" style="background:#00f5d4; color:#050811; font-weight:800; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;" />
                </p>
            </form>
        </div>
        <script>
        (function(){
            var list = document.getElementById('hs-section-order-list');
            var hidden = document.getElementById('hs-section-order-input');
            if (!list || !hidden) return;
            function syncOrder(){
                var keys = Array.from(list.querySelectorAll('.hs-order-row')).map(function(el){ return el.getAttribute('data-sec'); });
                hidden.value = keys.join(',');
            }
            list.addEventListener('click', function(e){
                var row = e.target.closest('.hs-order-row');
                if (!row) return;
                if (e.target.classList.contains('hs-move-up') && row.previousElementSibling) {
                    list.insertBefore(row, row.previousElementSibling);
                    syncOrder();
                } else if (e.target.classList.contains('hs-move-down') && row.nextElementSibling) {
                    list.insertBefore(row.nextElementSibling, row);
                    syncOrder();
                }
            });
        })();
        </script>
        <?php
    }

    public static function render_moderation_screen(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'hackersshikkhok-theme' ) );
        }

        $queue = get_option( '_hs_reported_content_queue', array() );
        if ( ! is_array( $queue ) ) {
            $queue = array();
        }

        if ( isset( $_POST['hs_mod_action'], $_POST['hs_mod_index'] ) && check_admin_referer( 'hs_moderation_nonce' ) ) {
            $idx    = (int) $_POST['hs_mod_index'];
            $action = sanitize_key( (string) $_POST['hs_mod_action'] );
            if ( isset( $queue[ $idx ] ) ) {
                $target_post_id = (int) ( $queue[ $idx ]['post_id'] ?? 0 );
                if ( 'trash_post' === $action && $target_post_id > 0 ) {
                    wp_trash_post( $target_post_id );
                    $queue[ $idx ]['status'] = 'trashed';
                } elseif ( 'resolve' === $action ) {
                    $queue[ $idx ]['status'] = 'resolved';
                } elseif ( 'dismiss' === $action ) {
                    unset( $queue[ $idx ] );
                    $queue = array_values( $queue );
                }
                update_option( '_hs_reported_content_queue', $queue );
                echo '<div class="notice notice-success is-dismissible"><p><strong>✓ Moderation queue updated.</strong></p></div>';
            }
        }
        ?>
        <div class="wrap hs-admin-wrap" style="max-width:1150px; color:#e2e8f0;">
            <div style="background:#0b1120; padding:22px; border-radius:14px; border:1px solid #f43f5e; margin-bottom:20px;">
                <h1 style="color:#fff; margin:0 0 6px;">🚩 Content Moderation &amp; Reported Items Queue</h1>
                <p style="color:#94a3b8; margin:0; font-size:13px;">Review articles, Q&amp;A topics, and developer tools flagged by the community.</p>
            </div>

            <div style="background:#0b1120; border:1px solid #1e293b; border-radius:12px; padding:20px;">
                <?php if ( empty( $queue ) ) : ?>
                    <p style="color:#10b981; font-weight:700; margin:0;">✓ All clear! No pending moderation reports in the queue.</p>
                <?php else : ?>
                    <table style="width:100%; border-collapse:collapse; font-size:13px; text-align:left;">
                        <thead>
                            <tr style="border-bottom:1px solid #1e293b; color:#00f5d4;">
                                <th style="padding:10px;">Type / Target</th>
                                <th style="padding:10px;">Reason &amp; Details</th>
                                <th style="padding:10px;">Reporter</th>
                                <th style="padding:10px;">Status</th>
                                <th style="padding:10px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $queue as $idx => $item ) :
                                $pid = (int) ( $item['post_id'] ?? 0 );
                                $st  = (string) ( $item['status'] ?? 'open' );
                            ?>
                                <tr style="border-bottom:1px solid #1e293b; color:#cbd5e1;">
                                    <td style="padding:10px;">
                                        <strong><?php echo esc_html( (string) ( $item['target_label'] ?? ( $pid ? get_the_title( $pid ) : 'Content #' . $pid ) ) ); ?></strong>
                                        <?php if ( $pid > 0 ) : ?>
                                            <div><a href="<?php echo esc_url( (string) get_permalink( $pid ) ); ?>" target="_blank" style="color:#38bdf8; font-size:11px;">View Live ↗</a></div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:10px;">
                                        <span style="color:#f59e0b; font-weight:700;"><?php echo esc_html( (string) ( $item['reason'] ?? 'Flagged' ) ); ?></span>
                                        <div style="color:#94a3b8; font-size:12px;"><?php echo esc_html( (string) ( $item['details'] ?? '' ) ); ?></div>
                                    </td>
                                    <td style="padding:10px; font-family:monospace;">User #<?php echo (int) ( $item['user_id'] ?? 0 ); ?><br><small><?php echo esc_html( (string) ( $item['reported_at'] ?? '' ) ); ?></small></td>
                                    <td style="padding:10px;"><span style="padding:2px 8px; border-radius:4px; background:#1e293b; color:#00f5d4; font-size:11px;"><?php echo esc_html( strtoupper( $st ) ); ?></span></td>
                                    <td style="padding:10px;">
                                        <form method="post" style="display:flex; gap:6px; flex-wrap:wrap;">
                                            <?php wp_nonce_field( 'hs_moderation_nonce' ); ?>
                                            <input type="hidden" name="hs_mod_index" value="<?php echo (int) $idx; ?>" />
                                            <button type="submit" name="hs_mod_action" value="resolve" class="button button-small">✓ Resolve</button>
                                            <?php if ( $pid > 0 ) : ?>
                                                <button type="submit" name="hs_mod_action" value="trash_post" class="button button-small" style="color:#f43f5e;">🗑 Trash Post</button>
                                            <?php endif; ?>
                                            <button type="submit" name="hs_mod_action" value="dismiss" class="button button-small">✕ Dismiss</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    public static function render_analytics_screen(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'hackersshikkhok-theme' ) );
        }
        global $wpdb;

        $posts_count     = (int) wp_count_posts( 'post' )->publish + (int) wp_count_posts( 'tutorials' )->publish;
        $pending_posts   = (int) wp_count_posts( 'post' )->pending + (int) wp_count_posts( 'tools' )->pending;
        $qa_count        = (int) wp_count_posts( 'hs_question' )->publish;
        $courses_count   = (int) wp_count_posts( 'hs_course' )->publish;
        $users_count     = (int) ( count_users()['total_users'] ?? 0 );
        $comments_count  = (int) wp_count_comments()->approved;
        $certs_table     = $wpdb->prefix . 'hs_certificates';
        $certs_issued    = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $certs_table ) ) ? (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$certs_table}" ) : 0;
        $tools_count     = class_exists( '\\HackersShikkhok\\Theme\\Modules\\Tools\\UniversalToolRegistry' )
            ? count( \HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry::get_all_tools() )
            : 2024;
        ?>
        <div class="wrap hs-admin-wrap" style="max-width:1150px; color:#e2e8f0;">
            <div style="background:#0b1120; padding:22px; border-radius:14px; border:1px solid #00f5d4; margin-bottom:20px;">
                <h1 style="color:#fff; margin:0 0 6px;">📊 HackersShikkhok Platform Analytics &amp; Quick Admin</h1>
                <p style="color:#94a3b8; margin:0; font-size:13px;">Live database counts across Articles, Q&amp;A Forum, Tools Directory, Academy LMS &amp; Members.</p>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:14px; margin-bottom:24px;">
                <div style="background:#0b1120; border:1px solid #1e293b; border-radius:12px; padding:16px;"><span style="color:#94a3b8; font-size:12px;">Published Posts &amp; Tutorials</span><strong style="display:block; font-size:26px; color:#00f5d4;"><?php echo number_format( $posts_count ); ?></strong></div>
                <div style="background:#0b1120; border:1px solid #1e293b; border-radius:12px; padding:16px;"><span style="color:#94a3b8; font-size:12px;">Q&amp;A Topics Published</span><strong style="display:block; font-size:26px; color:#fbbf24;"><?php echo number_format( $qa_count ); ?></strong></div>
                <div style="background:#0b1120; border:1px solid #1e293b; border-radius:12px; padding:16px;"><span style="color:#94a3b8; font-size:12px;">Registered Tools</span><strong style="display:block; font-size:26px; color:#10b981;"><?php echo number_format( $tools_count ); ?>+</strong></div>
                <div style="background:#0b1120; border:1px solid #1e293b; border-radius:12px; padding:16px;"><span style="color:#94a3b8; font-size:12px;">Academy Courses &amp; Certs</span><strong style="display:block; font-size:26px; color:#a78bfa;"><?php echo number_format( $courses_count ); ?> / <?php echo number_format( $certs_issued ); ?></strong></div>
                <div style="background:#0b1120; border:1px solid #1e293b; border-radius:12px; padding:16px;"><span style="color:#94a3b8; font-size:12px;">Registered Members</span><strong style="display:block; font-size:26px; color:#38bdf8;"><?php echo number_format( $users_count ); ?></strong></div>
                <div style="background:#0b1120; border:1px solid #1e293b; border-radius:12px; padding:16px;"><span style="color:#94a3b8; font-size:12px;">Pending Submissions / Comments</span><strong style="display:block; font-size:26px; color:#f43f5e;"><?php echo number_format( $pending_posts ); ?> / <?php echo number_format( $comments_count ); ?></strong></div>
            </div>

            <div style="background:#0b1120; border:1px solid #1e293b; border-radius:12px; padding:20px;">
                <h2 style="color:#fff; margin:0 0 14px;">⚡ Direct Content &amp; Ecosystem Management Links</h2>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>">Manage Posts</a>
                    <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=hs_question' ) ); ?>">Manage Q&amp;A</a>
                    <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=tools' ) ); ?>">Manage Pending Tools</a>
                    <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=hs_course' ) ); ?>">Manage Courses</a>
                    <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'edit-comments.php' ) ); ?>">Comments &amp; Reviews</a>
                    <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'users.php' ) ); ?>">Manage Members</a>
                    <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=category' ) ); ?>">Categories</a>
                    <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=hs-moderation-queue' ) ); ?>">Moderation Queue</a>
                </div>
            </div>
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

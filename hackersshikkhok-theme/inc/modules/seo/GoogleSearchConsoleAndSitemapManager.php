<?php
/**
 * HackersShikkhok — Google Search Console & XML Sitemap Command Center
 * Package: HackersShikkhok Core Plugin
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * 
 * Provides:
 * - Google Site Verification HTML Meta Tag injection
 * - Bing Webmaster Tools Verification Tag injection
 * - Real-time XML Sitemap Index & Child endpoints (/sitemap_index.xml, /sitemap.xml)
 * - 1-Click Sitemap Ping to Google Search Console and Bing
 * - Native robots.txt with automated Sitemap directives
 * - Live Google Search Console Submission Checklist
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\SEO;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class GoogleSearchConsoleAndSitemapManager {

    public const OPTION_GSC_TOKEN    = 'hs_gsc_verification_token';
    public const OPTION_BING_TOKEN   = 'hs_bing_verification_token';
    public const OPTION_PING_HISTORY = 'hs_sitemap_ping_history';

    public static function register(): void {
        add_action( 'wp_head', array( self::class, 'render_verification_meta_tags' ), 1 );
        add_filter( 'robots_txt', array( self::class, 'filter_robots_txt' ), 10, 2 );

        if ( is_admin() ) {
            add_action( 'admin_menu', array( self::class, 'register_admin_menu' ), 20 );
            add_action( 'admin_init', array( self::class, 'handle_admin_actions' ) );
        }
    }

    public static function register_admin_menu(): void {
        add_submenu_page(
            'hs-control-center',
            __( 'Google Search Console & Sitemaps', 'hackersshikkhok-core' ),
            __( '📡 Search Console & Sitemaps', 'hackersshikkhok-core' ),
            'manage_options',
            'hs-search-console-sitemaps',
            array( self::class, 'render_admin_screen' )
        );
    }

    public static function render_verification_meta_tags(): void {
        if ( is_admin() ) {
            return;
        }

        $gsc_token = trim( (string) get_option( self::OPTION_GSC_TOKEN, '' ) );
        if ( '' !== $gsc_token ) {
            // Support either raw token string or complete <meta> tag pasted by user
            if ( preg_match( '/content=["\']([^"\']+)["\']/i', $gsc_token, $matches ) ) {
                $gsc_token = $matches[1];
            }
            echo '<meta name="google-site-verification" content="' . esc_attr( $gsc_token ) . '" />' . "\n";
        }

        $bing_token = trim( (string) get_option( self::OPTION_BING_TOKEN, '' ) );
        if ( '' !== $bing_token ) {
            if ( preg_match( '/content=["\']([^"\']+)["\']/i', $bing_token, $matches ) ) {
                $bing_token = $matches[1];
            }
            echo '<meta name="msvalidate.01" content="' . esc_attr( $bing_token ) . '" />' . "\n";
        }
    }

    public static function filter_robots_txt( string $output, bool $public ): string {
        if ( ! $public ) {
            return "User-agent: *\nDisallow: /\n";
        }

        $sitemap_index = esc_url( home_url( '/sitemap_index.xml' ) );
        $sitemap_std   = esc_url( home_url( '/sitemap.xml' ) );

        $lines = array(
            '# HackersShikkhok High-Performance Robots.txt',
            'User-agent: *',
            'Allow: /',
            'Disallow: /wp-admin/',
            'Allow: /wp-admin/admin-ajax.php',
            'Disallow: /dashboard/',
            'Disallow: /messages/',
            'Disallow: /?s=',
            '',
            '# Search Console & Sitemaps',
            'Sitemap: ' . $sitemap_index,
            'Sitemap: ' . $sitemap_std,
        );

        return implode( "\n", $lines ) . "\n";
    }

    public static function handle_admin_actions(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Save Settings
        if ( isset( $_POST['hs_save_gsc_settings'] ) && check_admin_referer( 'hs_gsc_settings_nonce' ) ) {
            $gsc_token  = sanitize_text_field( wp_unslash( $_POST['gsc_token'] ?? '' ) );
            $bing_token = sanitize_text_field( wp_unslash( $_POST['bing_token'] ?? '' ) );

            update_option( self::OPTION_GSC_TOKEN, $gsc_token );
            update_option( self::OPTION_BING_TOKEN, $bing_token );

            flush_rewrite_rules( false );

            wp_safe_redirect( add_query_arg( array( 'page' => 'hs-search-console-sitemaps', 'status' => 'saved' ), admin_url( 'admin.php' ) ) );
            exit;
        }

        // Ping Sitemaps
        if ( isset( $_POST['hs_ping_search_engines'] ) && check_admin_referer( 'hs_gsc_ping_nonce' ) ) {
            $engine = sanitize_text_field( wp_unslash( $_POST['target_engine'] ?? 'all' ) );
            $res = self::ping_search_engines( $engine );

            wp_safe_redirect( add_query_arg( array( 'page' => 'hs-search-console-sitemaps', 'status' => 'pinged', 'msg' => urlencode( $res['message'] ) ), admin_url( 'admin.php' ) ) );
            exit;
        }
    }

    public static function ping_search_engines( string $engine = 'all' ): array {
        $sitemap_url = home_url( '/sitemap_index.xml' );
        $results     = array();

        if ( 'all' === $engine || 'google' === $engine ) {
            $g_url = 'https://www.google.com/ping?sitemap=' . urlencode( $sitemap_url );
            $response = wp_remote_get( $g_url, array( 'timeout' => 8, 'user-agent' => 'HackersShikkhok-Sitemap-Ping/4.0' ) );
            $code = is_wp_error( $response ) ? 500 : wp_remote_retrieve_response_code( $response );
            $results['google'] = ( 200 === $code ) ? 'OK (200)' : 'HTTP ' . $code;
        }

        if ( 'all' === $engine || 'bing' === $engine ) {
            $b_url = 'https://www.bing.com/ping?sitemap=' . urlencode( $sitemap_url );
            $response = wp_remote_get( $b_url, array( 'timeout' => 8, 'user-agent' => 'HackersShikkhok-Sitemap-Ping/4.0' ) );
            $code = is_wp_error( $response ) ? 500 : wp_remote_retrieve_response_code( $response );
            $results['bing'] = ( 200 === $code ) ? 'OK (200)' : 'HTTP ' . $code;
        }

        // Record history
        $history = get_option( self::OPTION_PING_HISTORY, array() );
        array_unshift( $history, array(
            'time'    => current_time( 'mysql' ),
            'results' => $results,
        ) );
        $history = array_slice( $history, 0, 15 );
        update_option( self::OPTION_PING_HISTORY, $history );

        return array(
            'success' => true,
            'message' => 'Sitemaps successfully submitted to ' . implode( ', ', array_keys( $results ) ) . '!',
            'results' => $results,
        );
    }

    public static function render_admin_screen(): void {
        $gsc_token  = (string) get_option( self::OPTION_GSC_TOKEN, '' );
        $bing_token = (string) get_option( self::OPTION_BING_TOKEN, '' );
        $sitemaps   = MultilingualSitemapEngine::get_all_sitemap_urls();
        $history    = get_option( self::OPTION_PING_HISTORY, array() );

        $status = sanitize_text_field( $_GET['status'] ?? '' );
        $msg    = sanitize_text_field( $_GET['msg'] ?? '' );
        ?>
        <div class="wrap" style="max-width:1180px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">

            <!-- Header Banner -->
            <div style="background:linear-gradient(135deg, #06080d 0%, #0b1120 100%); border:1px solid rgba(0,245,212,0.4); border-radius:16px; padding:28px; margin-bottom:24px; color:#f8fafc; box-shadow:0 12px 30px rgba(0,0,0,0.5);">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                    <div>
                        <div style="display:inline-flex; align-items:center; gap:8px; padding:4px 12px; border-radius:999px; background:rgba(0,245,212,0.12); border:1px solid rgba(0,245,212,0.3); color:#00f5d4; font-size:11px; font-weight:800; text-transform:uppercase; margin-bottom:8px;">
                            <span>●</span> GOOGLE SEARCH CONSOLE COMMAND CENTER
                        </div>
                        <h1 style="color:#ffffff; margin:0 0 8px; font-size:26px; font-weight:800;">Google Search Console &amp; XML Sitemaps Manager</h1>
                        <p style="color:#94a3b8; margin:0; font-size:14px; max-width:750px;">
                            আপনার সাইটম্যাপ গুগল সার্চ কনসোলে সাবমিট করা, সাইট ভেরিফিকেশন মেটা ট্যাগ যুক্ত করা, এবং নতুন কনটেন্টকে সার্চ ইঞ্জিনে তাৎক্ষণিক পিং করার স্বয়ংক্রিয় কমান্ড প্যানেল।
                        </p>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <a href="https://search.google.com/search-console" target="_blank" rel="noopener noreferrer" style="background:#00f5d4; color:#050811; font-weight:800; font-size:13px; padding:10px 18px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                            <span>Google Search Console খুলুন ↗</span>
                        </a>
                    </div>
                </div>
            </div>

            <?php if ( 'saved' === $status ) : ?>
                <div style="background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.4); color:#10b981; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:600;">
                    ✓ সেটিংস সফলভাবে সংরক্ষিত হয়েছে এবং সাইটম্যাপ রিরাইট রুলস রিফ্রেশ করা হয়েছে!
                </div>
            <?php elseif ( 'pinged' === $status && ! empty( $msg ) ) : ?>
                <div style="background:rgba(0,245,212,0.15); border:1px solid rgba(0,245,212,0.4); color:#00f5d4; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:600;">
                    ⚡ <?php echo esc_html( $msg ); ?>
                </div>
            <?php endif; ?>

            <div style="display:grid; grid-template-columns:1.6fr 1fr; gap:24px;">

                <!-- Left Column: Sitemaps Directory & Search Engine Ping -->
                <div>
                    <!-- Active Sitemaps Directory -->
                    <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:14px; padding:24px; margin-bottom:24px; color:#f8fafc;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                            <div>
                                <h2 style="color:#ffffff; font-size:17px; margin:0 0 4px; font-weight:700;">📡 সক্রিয় XML সাইটম্যাপ তালিকা (Search Console Ready)</h2>
                                <p style="color:#94a3b8; font-size:13px; margin:0;">নিচের মাস্টার ইনডেক্স ইউআরএলটি গুগল সার্চ কনসোলে সাবমিট করুন:</p>
                            </div>
                        </div>

                        <!-- Primary Master URL Callout -->
                        <div style="background:rgba(0,245,212,0.06); border:1px solid rgba(0,245,212,0.3); border-radius:10px; padding:16px; margin-bottom:18px;">
                            <div style="font-size:11px; color:#00f5d4; font-weight:800; text-transform:uppercase; margin-bottom:6px;">
                                🌟 Primary Master Sitemap URL (গুগল সার্চ কনসোলে এই একটি সাবমিট করলেই যথেষ্ট):
                            </div>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <input type="text" readonly value="<?php echo esc_url( home_url( '/sitemap_index.xml' ) ); ?>" id="hs-master-sitemap-url" style="width:100%; background:#06080d; border:1px solid rgba(0,245,212,0.3); color:#00f5d4; font-family:monospace; font-size:13px; padding:10px 14px; border-radius:8px;" />
                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('hs-master-sitemap-url').value); alert('Copied to clipboard!');" style="background:#00f5d4; color:#050811; border:none; padding:10px 14px; font-weight:700; border-radius:8px; cursor:pointer; flex-shrink:0;">
                                    কপি করুন
                                </button>
                                <a href="<?php echo esc_url( home_url( '/sitemap_index.xml' ) ); ?>" target="_blank" style="background:rgba(255,255,255,0.08); color:#cbd5e1; border:1px solid rgba(255,255,255,0.15); padding:10px 14px; font-weight:600; font-size:13px; border-radius:8px; text-decoration:none; flex-shrink:0;">
                                    ভিউ ↗
                                </a>
                            </div>
                        </div>

                        <!-- All Child Sitemaps List -->
                        <table style="width:100%; border-collapse:collapse; font-size:13px;">
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.1); text-align:left; color:#94a3b8;">
                                    <th style="padding:10px 6px;">সাইটম্যাপ সেকশন</th>
                                    <th style="padding:10px 6px;">ইউআরএল (Endpoints)</th>
                                    <th style="padding:10px 6px; text-align:right;">অ্যাকশন</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $sitemaps as $label => $url ) : ?>
                                    <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                        <td style="padding:12px 6px; font-weight:600; color:#e2e8f0;"><?php echo esc_html( $label ); ?></td>
                                        <td style="padding:12px 6px;">
                                            <code style="background:rgba(0,0,0,0.4); color:#38bdf8; padding:3px 8px; border-radius:4px; font-size:12px;"><?php echo esc_html( $url ); ?></code>
                                        </td>
                                        <td style="padding:12px 6px; text-align:right;">
                                            <a href="<?php echo esc_url( $url ); ?>" target="_blank" style="color:#00f5d4; text-decoration:none; font-weight:600; font-size:12px;">টেস্ট ↗</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Instant Ping Form -->
                    <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:14px; padding:24px; color:#f8fafc;">
                        <h2 style="color:#ffffff; font-size:17px; margin:0 0 6px; font-weight:700;">⚡ ইনস্ট্যান্ট সাইটম্যাপ পিং (Notify Google &amp; Bing)</h2>
                        <p style="color:#94a3b8; font-size:13px; margin:0 0 16px;">
                            নতুন কোনো টিউটোরিয়াল, টুলস বা কোর্স পাবলিশ করার পর সার্চ ইঞ্জিনগুলোকে এক ক্লিকে সাইটম্যাপ রি-ক্রল করার নোটিফিকেশন পাঠান।
                        </p>

                        <form method="post" style="display:flex; gap:12px; align-items:center;">
                            <?php wp_nonce_field( 'hs_gsc_ping_nonce' ); ?>
                            <select name="target_engine" style="background:#06080d; border:1px solid rgba(255,255,255,0.2); color:#ffffff; padding:10px 14px; border-radius:8px;">
                                <option value="all">গুগল ও বিং উভয়কেই পিং করুন (Google &amp; Bing)</option>
                                <option value="google">শুধুমাত্র গুগল সার্চ কনসোল (Google)</option>
                                <option value="bing">শুধুমাত্র বিং ওয়েবমাস্টার (Bing)</option>
                            </select>
                            <button type="submit" name="hs_ping_search_engines" value="1" style="background:linear-gradient(90deg, #00f5d4, #38bdf8); color:#050811; border:none; padding:11px 22px; font-weight:800; border-radius:8px; cursor:pointer;">
                                🚀 ইনস্ট্যান্ট পিং পাঠান
                            </button>
                        </form>

                        <?php if ( ! empty( $history ) ) : ?>
                            <div style="margin-top:18px; padding-top:14px; border-top:1px solid rgba(255,255,255,0.08);">
                                <strong style="font-size:12px; color:#94a3b8; text-transform:uppercase;">সর্বশেষ পিং হিস্টোরি:</strong>
                                <ul style="margin:8px 0 0; padding-left:16px; font-size:12px; color:#cbd5e1; font-family:monospace;">
                                    <?php foreach ( array_slice( $history, 0, 3 ) as $h ) : ?>
                                        <li><?php echo esc_html( $h['time'] ); ?> — <?php echo esc_html( wp_json_encode( $h['results'] ) ); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column: Verification Tokens & Setup Checklist -->
                <div>
                    <!-- Verification Tokens Card -->
                    <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:14px; padding:24px; margin-bottom:24px; color:#f8fafc;">
                        <h2 style="color:#ffffff; font-size:17px; margin:0 0 6px; font-weight:700;">🔑 ডোমেইন ও সার্চ কনসোল ভেরিফিকেশন</h2>
                        <p style="color:#94a3b8; font-size:13px; margin:0 0 16px;">
                            গুগল সার্চ কনসোল থেকে প্রাপ্ত HTML Tag বা মেটা ট্যাগ কোডটি এখানে পেস্ট করুন। এটি স্বয়ংক্রিয়ভাবে আপনার সাইটের <code>&lt;head&gt;</code>-এ সংযুক্ত হয়ে যাবে।
                        </p>

                        <form method="post">
                            <?php wp_nonce_field( 'hs_gsc_settings_nonce' ); ?>

                            <div style="margin-bottom:16px;">
                                <label style="display:block; font-size:13px; font-weight:600; color:#cbd5e1; margin-bottom:6px;">
                                    Google Site Verification Token:
                                </label>
                                <input type="text" name="gsc_token" value="<?php echo esc_attr( $gsc_token ); ?>" placeholder="e.g. 4vE9f-mE3aR... বা সম্পূর্ণ <meta> কোড" style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(0,245,212,0.3); color:#ffffff; padding:10px 14px; border-radius:8px; font-size:13px; font-family:monospace;" />
                                <span style="font-size:11px; color:#64748b; margin-top:4px; display:block;">গুগল সার্চ কনসোল ➔ Settings ➔ Ownership verification ➔ HTML tag থেকে নিন।</span>
                            </div>

                            <div style="margin-bottom:20px;">
                                <label style="display:block; font-size:13px; font-weight:600; color:#cbd5e1; margin-bottom:6px;">
                                    Bing Webmaster Verification Token:
                                </label>
                                <input type="text" name="bing_token" value="<?php echo esc_attr( $bing_token ); ?>" placeholder="e.g. 8D5E63A7C159..." style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(255,255,255,0.15); color:#ffffff; padding:10px 14px; border-radius:8px; font-size:13px; font-family:monospace;" />
                            </div>

                            <button type="submit" name="hs_save_gsc_settings" value="1" style="width:100%; background:#00f5d4; color:#050811; border:none; padding:12px; font-weight:800; border-radius:8px; cursor:pointer; font-size:14px;">
                                💾 ভেরিফিকেশন সেটিংস সেভ করুন
                            </button>
                        </form>
                    </div>

                    <!-- Search Console Step-by-Step Bangla Checklist -->
                    <div style="background:#0b1120; border:1px solid rgba(0,245,212,0.25); border-radius:14px; padding:24px; color:#f8fafc;">
                        <h3 style="color:#00f5d4; font-size:15px; margin:0 0 12px; font-weight:800; text-transform:uppercase;">
                            📋 সার্চ কনসোলে সাবমিট করার ৩টি সহজ ধাপ:
                        </h3>

                        <ol style="margin:0; padding-left:18px; font-size:13px; line-height:1.7; color:#cbd5e1;">
                            <li style="margin-bottom:10px;">
                                <strong>ধাপ ১: প্রপার্টি ভেরিফাই করুন:</strong><br />
                                <a href="https://search.google.com/search-console" target="_blank" style="color:#38bdf8;">Google Search Console</a>-এ আপনার সাইটের URL Prefix (<code>https://hackersshikkhok.com</code>) দিয়ে "HTML Tag" অপশনটি সিলেক্ট করে কোডটি উপরের বক্সে বসিয়ে সেভ করুন।
                            </li>
                            <li style="margin-bottom:10px;">
                                <strong>ধাপ ২: সাইটম্যাপ সাবমিট করুন:</strong><br />
                                সার্চ কনসোলের বাম পাশের মেনু থেকে <strong>Sitemaps</strong>-এ যান। সেখানে ইনপুট বক্সে লিখুন <code>sitemap_index.xml</code> এবং <strong>Submit</strong> বাটনে ক্লিক করুন।
                            </li>
                            <li style="margin-bottom:6px;">
                                <strong>ধাপ ৩: Robots.txt চেক করুন:</strong><br />
                                আপনার সাইটের <a href="<?php echo esc_url( home_url( '/robots.txt' ) ); ?>" target="_blank" style="color:#00f5d4;">robots.txt</a> ফাইলটিতে অটোম্যাটিক্যালি সাইটম্যাপ লিঙ্ক সংযুক্ত রয়েছে।
                            </li>
                        </ol>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }
}

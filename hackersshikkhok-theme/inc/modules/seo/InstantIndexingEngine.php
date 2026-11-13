<?php
/**
 * HackersShikkhok — Instant Google Indexing & IndexNow API Suite
 * Package: HackersShikkhok Core Plugin
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * 
 * Features:
 * - Google Indexing API (Service Account OAuth2 with native PHP openssl_sign RSA-SHA256 JWT)
 * - Auto-Notify Google on post publish, update, and delete
 * - IndexNow protocol integration for Bing, Yandex, Naver & Seznam with virtual /{key}.txt
 * - Manual 1-Click Instant Indexing form for any URL
 * - Live submission logs & HTTP response tracking
 * - Step-by-step setup guide for Google Cloud Service Account
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\SEO;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class InstantIndexingEngine {

    public const OPTION_SERVICE_ACCOUNT = 'hs_google_service_account_json';
    public const OPTION_AUTO_INDEX      = 'hs_instant_auto_index_enabled';
    public const OPTION_INDEXNOW_KEY    = 'hs_indexnow_api_key';
    public const OPTION_INDEX_LOGS      = 'hs_instant_indexing_logs';

    public static function register(): void {
        add_action( 'init', array( self::class, 'register_indexnow_verification_route' ) );
        add_action( 'template_redirect', array( self::class, 'serve_indexnow_key_file' ), 1 );
        add_action( 'transition_post_status', array( self::class, 'on_post_status_transition' ), 10, 3 );

        if ( is_admin() ) {
            add_action( 'admin_menu', array( self::class, 'register_admin_menu' ), 22 );
            add_action( 'admin_init', array( self::class, 'handle_admin_actions' ) );
        }
    }

    public static function register_admin_menu(): void {
        add_submenu_page(
            'hs-control-center',
            __( 'Google Instant Indexing', 'hackersshikkhok-core' ),
            __( '⚡ Instant Indexing', 'hackersshikkhok-core' ),
            'manage_options',
            'hs-instant-indexing',
            array( self::class, 'render_admin_screen' )
        );
    }

    public static function get_indexnow_key(): string {
        $key = (string) get_option( self::OPTION_INDEXNOW_KEY, '' );
        if ( empty( $key ) || strlen( $key ) < 32 ) {
            $key = bin2hex( random_bytes( 16 ) );
            update_option( self::OPTION_INDEXNOW_KEY, $key );
        }
        return $key;
    }

    public static function register_indexnow_verification_route(): void {
        $key = self::get_indexnow_key();
        add_rewrite_rule( '^(' . preg_quote( $key, '/' ) . ')\.txt$', 'index.php?hs_indexnow_verify=1', 'top' );
    }

    public static function serve_indexnow_key_file(): void {
        $key = self::get_indexnow_key();
        $uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
        $path = trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );

        if ( $path === $key . '.txt' || '1' === (string) get_query_var( 'hs_indexnow_verify' ) ) {
            if ( ob_get_level() > 0 ) {
                ob_clean();
            }
            header( 'Content-Type: text/plain; charset=utf-8' );
            header( 'X-Robots-Tag: noindex' );
            echo esc_html( $key );
            exit;
        }
    }

    /**
     * Automatic Instant Indexing hook on post publish/update
     */
    public static function on_post_status_transition( string $new_status, string $old_status, \WP_Post $post ): void {
        $auto_enabled = ( '1' === (string) get_option( self::OPTION_AUTO_INDEX, '1' ) );
        if ( ! $auto_enabled || wp_is_post_revision( $post ) || wp_is_post_autosave( $post ) ) {
            return;
        }

        $supported_cpts = array( 'post', 'tutorials', 'tools', 'hs_course', 'device_review', 'device_price', 'tech_news', 'code', 'projects', 'cyber' );
        if ( ! in_array( $post->post_type, $supported_cpts, true ) ) {
            return;
        }

        $url = get_permalink( $post->ID );
        if ( empty( $url ) ) {
            return;
        }

        if ( 'publish' === $new_status ) {
            // Send update notification
            self::send_to_google_indexing_api( $url, 'URL_UPDATED' );
            self::send_to_indexnow( array( $url ) );
        } elseif ( 'publish' === $old_status && 'publish' !== $new_status ) {
            // Post was unpublished/deleted
            self::send_to_google_indexing_api( $url, 'URL_DELETED' );
        }
    }

    /**
     * Generate Google OAuth2 Access Token from Service Account JSON using native OpenSSL
     */
    public static function get_google_access_token(): array {
        $json_raw = trim( (string) get_option( self::OPTION_SERVICE_ACCOUNT, '' ) );
        if ( empty( $json_raw ) ) {
            return array( 'success' => false, 'error' => 'Service Account JSON is not configured in settings.' );
        }

        $sa = json_decode( $json_raw, true );
        if ( ! is_array( $sa ) || empty( $sa['client_email'] ) || empty( $sa['private_key'] ) ) {
            return array( 'success' => false, 'error' => 'Invalid Service Account JSON format. Must contain client_email and private_key.' );
        }

        // Check cached transient token
        $transient_key = 'hs_google_indexing_token_' . md5( $sa['client_email'] );
        $cached_token  = get_transient( $transient_key );
        if ( ! empty( $cached_token ) ) {
            return array( 'success' => true, 'token' => $cached_token );
        }

        // 1. Build JWT Header & Payload
        $header  = array( 'alg' => 'RS256', 'typ' => 'JWT' );
        $now     = time();
        $payload = array(
            'iss'   => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/indexing',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'exp'   => $now + 3600,
            'iat'   => $now,
        );

        $b64_header  = rtrim( strtr( base64_encode( (string) wp_json_encode( $header ) ), '+/', '-_' ), '=' );
        $b64_payload = rtrim( strtr( base64_encode( (string) wp_json_encode( $payload ) ), '+/', '-_' ), '=' );
        $signing_input = $b64_header . '.' . $b64_payload;

        $private_key = openssl_pkey_get_private( $sa['private_key'] );
        if ( ! $private_key ) {
            return array( 'success' => false, 'error' => 'OpenSSL failed to parse private_key. Please check RSA private key format.' );
        }

        $signature = '';
        $signed = openssl_sign( $signing_input, $signature, $private_key, OPENSSL_ALGO_SHA256 );
        if ( ! $signed ) {
            return array( 'success' => false, 'error' => 'OpenSSL failed to sign JWT.' );
        }

        $b64_sig = rtrim( strtr( base64_encode( $signature ), '+/', '-_' ), '=' );
        $jwt = $signing_input . '.' . $b64_sig;

        // 2. Exchange JWT for Google OAuth2 Bearer Token
        $response = wp_remote_post( 'https://oauth2.googleapis.com/token', array(
            'headers' => array( 'Content-Type' => 'application/x-www-form-urlencoded' ),
            'body'    => array(
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ),
            'timeout' => 12,
        ) );

        if ( is_wp_error( $response ) ) {
            return array( 'success' => false, 'error' => $response->get_error_message() );
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( 200 !== $code || empty( $body['access_token'] ) ) {
            $err_desc = $body['error_description'] ?? ( $body['error'] ?? 'OAuth token exchange failed (HTTP ' . $code . ')' );
            return array( 'success' => false, 'error' => $err_desc );
        }

        $access_token = (string) $body['access_token'];
        set_transient( $transient_key, $access_token, 3000 ); // Cache for 50 minutes

        return array( 'success' => true, 'token' => $access_token );
    }

    /**
     * Dispatch URL Notification to Google Indexing API
     */
    public static function send_to_google_indexing_api( string $url, string $type = 'URL_UPDATED' ): array {
        $token_data = self::get_google_access_token();
        if ( ! $token_data['success'] ) {
            self::log_submission( 'Google Indexing API', $url, 400, 'Token Error: ' . $token_data['error'] );
            return $token_data;
        }

        $api_endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';
        $payload = array(
            'url'  => esc_url_raw( $url ),
            'type' => $type,
        );

        $response = wp_remote_post( $api_endpoint, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $token_data['token'],
                'Content-Type'  => 'application/json',
            ),
            'body'    => (string) wp_json_encode( $payload ),
            'timeout' => 12,
        ) );

        if ( is_wp_error( $response ) ) {
            $msg = $response->get_error_message();
            self::log_submission( 'Google Indexing API', $url, 500, $msg );
            return array( 'success' => false, 'error' => $msg );
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        $is_success = ( 200 === $code );
        $msg = $is_success ? 'Successfully notified Google (' . $type . ')' : ( $body['error']['message'] ?? 'HTTP ' . $code );

        self::log_submission( 'Google Indexing API', $url, $code, $msg );

        return array(
            'success' => $is_success,
            'code'    => $code,
            'message' => $msg,
            'data'    => $body,
        );
    }

    /**
     * Dispatch URL list to IndexNow (Bing, Yandex)
     */
    public static function send_to_indexnow( array $urls ): array {
        $key      = self::get_indexnow_key();
        $host     = (string) wp_parse_url( home_url(), PHP_URL_HOST );
        $key_loc  = home_url( '/' . $key . '.txt' );

        $payload = array(
            'host'        => $host,
            'key'         => $key,
            'keyLocation' => $key_loc,
            'urlList'     => array_values( array_map( 'esc_url_raw', $urls ) ),
        );

        $response = wp_remote_post( 'https://api.indexnow.org/indexnow', array(
            'headers' => array( 'Content-Type' => 'application/json; charset=utf-8' ),
            'body'    => (string) wp_json_encode( $payload ),
            'timeout' => 10,
        ) );

        $code = is_wp_error( $response ) ? 500 : wp_remote_retrieve_response_code( $response );
        $success = in_array( $code, array( 200, 202 ), true );
        $msg = $success ? 'URLs submitted to IndexNow (Bing/Yandex)' : 'IndexNow HTTP ' . $code;

        foreach ( $urls as $u ) {
            self::log_submission( 'IndexNow (Bing)', $u, $code, $msg );
        }

        return array( 'success' => $success, 'code' => $code, 'message' => $msg );
    }

    private static function log_submission( string $engine, string $url, int $code, string $message ): void {
        $logs = get_option( self::OPTION_INDEX_LOGS, array() );
        array_unshift( $logs, array(
            'time'    => current_time( 'mysql' ),
            'engine'  => $engine,
            'url'     => $url,
            'code'    => $code,
            'message' => $message,
        ) );
        $logs = array_slice( $logs, 0, 25 );
        update_option( self::OPTION_INDEX_LOGS, $logs );
    }

    public static function handle_admin_actions(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // 1. Save Settings
        if ( isset( $_POST['hs_save_instant_settings'] ) && check_admin_referer( 'hs_instant_nonce' ) ) {
            $sa_json = trim( (string) wp_unslash( $_POST['service_account_json'] ?? '' ) );
            $auto    = isset( $_POST['auto_index_enabled'] ) ? '1' : '0';

            update_option( self::OPTION_SERVICE_ACCOUNT, $sa_json );
            update_option( self::OPTION_AUTO_INDEX, $auto );

            delete_transient( 'hs_google_indexing_token_' );
            flush_rewrite_rules( false );

            wp_safe_redirect( add_query_arg( array( 'page' => 'hs-instant-indexing', 'status' => 'saved' ), admin_url( 'admin.php' ) ) );
            exit;
        }

        // 2. Manual Instant Submission
        if ( isset( $_POST['hs_manual_submit_url'] ) && check_admin_referer( 'hs_instant_manual_nonce' ) ) {
            $url  = sanitize_text_field( wp_unslash( $_POST['submit_url'] ?? '' ) );
            $type = sanitize_text_field( wp_unslash( $_POST['submit_type'] ?? 'URL_UPDATED' ) );

            if ( empty( $url ) ) {
                $url = home_url( '/' );
            }

            $g_res = self::send_to_google_indexing_api( $url, $type );
            $b_res = self::send_to_indexnow( array( $url ) );

            $feedback = 'Google: ' . ( $g_res['success'] ? '✓ Success (HTTP 200)' : '✗ ' . ( $g_res['error'] ?? 'Failed' ) )
                      . ' | Bing/IndexNow: ' . ( $b_res['success'] ? '✓ Success' : 'HTTP ' . $b_res['code'] );

            wp_safe_redirect( add_query_arg( array( 'page' => 'hs-instant-indexing', 'status' => 'submitted', 'msg' => urlencode( $feedback ) ), admin_url( 'admin.php' ) ) );
            exit;
        }
    }

    public static function render_admin_screen(): void {
        $sa_json  = (string) get_option( self::OPTION_SERVICE_ACCOUNT, '' );
        $auto_on  = ( '1' === (string) get_option( self::OPTION_AUTO_INDEX, '1' ) );
        $index_key= self::get_indexnow_key();
        $logs     = get_option( self::OPTION_INDEX_LOGS, array() );

        $status = sanitize_text_field( $_GET['status'] ?? '' );
        $msg    = sanitize_text_field( $_GET['msg'] ?? '' );

        $has_sa = ! empty( $sa_json );
        $parsed_sa = $has_sa ? json_decode( $sa_json, true ) : null;
        $client_email = $parsed_sa['client_email'] ?? '';
        ?>
        <div class="wrap" style="max-width:1180px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">

            <!-- Header Banner -->
            <div style="background:linear-gradient(135deg, #06080d 0%, #0b1120 100%); border:1px solid rgba(0,245,212,0.4); border-radius:16px; padding:28px; margin-bottom:24px; color:#f8fafc; box-shadow:0 12px 30px rgba(0,0,0,0.5);">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                    <div>
                        <div style="display:inline-flex; align-items:center; gap:8px; padding:4px 12px; border-radius:999px; background:rgba(0,245,212,0.12); border:1px solid rgba(0,245,212,0.3); color:#00f5d4; font-size:11px; font-weight:800; text-transform:uppercase; margin-bottom:8px;">
                            <span>●</span> GOOGLE INDEXING API &amp; INDEXNOW HIGH-SPEED ENGINE
                        </div>
                        <h1 style="color:#ffffff; margin:0 0 8px; font-size:26px; font-weight:800;">Google Instant Indexing &amp; Real-Time Discovery</h1>
                        <p style="color:#94a3b8; margin:0; font-size:14px; max-width:760px;">
                            যেকোনো নতুন পোস্ট, টিউটোরিয়াল, টুল বা কোর্স পাবলিশ করার সাথে সাথে গুগলকে সরাসরি <strong>Google Indexing API</strong>-র মাধ্যমে নোটিফাই করুন, যাতে সাধারণ ক্রলিংয়ের জন্য ঘণ্টার পর ঘণ্টা বা দিনের পর দিন অপেক্ষা করতে না হয়।
                        </p>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <span style="background:<?php echo $has_sa ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)'; ?>; border:1px solid <?php echo $has_sa ? '#10b981' : '#ef4444'; ?>; color:<?php echo $has_sa ? '#10b981' : '#f87171'; ?>; font-weight:800; font-size:12px; padding:8px 14px; border-radius:8px;">
                            <?php echo $has_sa ? '● GOOGLE API ACTIVE' : '○ API KEY REQUIRED'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if ( 'saved' === $status ) : ?>
                <div style="background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.4); color:#10b981; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:600;">
                    ✓ গুগল সার্ভিস একাউন্ট কি ও ইনস্ট্যান্ট ইনডেক্সিং সেটিংস সফলভাবে সংরক্ষিত হয়েছে!
                </div>
            <?php elseif ( 'submitted' === $status && ! empty( $msg ) ) : ?>
                <div style="background:rgba(0,245,212,0.15); border:1px solid rgba(0,245,212,0.4); color:#00f5d4; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:600;">
                    ⚡ ফলাফল: <?php echo esc_html( $msg ); ?>
                </div>
            <?php endif; ?>

            <div style="display:grid; grid-template-columns:1.6fr 1fr; gap:24px;">

                <!-- Left Column: Manual Instant Indexer & API Configuration -->
                <div>
                    <!-- Manual Submission Console -->
                    <div style="background:#0b1120; border:1px solid rgba(0,245,212,0.25); border-radius:14px; padding:24px; margin-bottom:24px; color:#f8fafc;">
                        <h2 style="color:#ffffff; font-size:18px; margin:0 0 8px; font-weight:700;">
                            ⚡ ১-ক্লিক ইনস্ট্যান্ট ইনডেক্স কনসোল (Submit Any URL)
                        </h2>
                        <p style="color:#94a3b8; font-size:13px; margin:0 0 16px;">
                            যেকোনো ইউআরএল লিখে নিচের বাটনে ক্লিক করলেই স্বয়ংক্রিয়ভাবে গুগল ইনডেক্সিং এপিআই এবং বিং ইনডেক্সনাউ-এ রিকোয়েস্ট চলে যাবে।
                        </p>

                        <form method="post">
                            <?php wp_nonce_field( 'hs_instant_manual_nonce' ); ?>

                            <div style="margin-bottom:14px;">
                                <label style="display:block; font-size:13px; font-weight:600; color:#cbd5e1; margin-bottom:6px;">
                                    টার্গেট পেজ ইউআরএল:
                                </label>
                                <input type="url" name="submit_url" value="<?php echo esc_url( home_url( '/' ) ); ?>" style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(0,245,212,0.3); color:#00f5d4; padding:12px 14px; border-radius:8px; font-size:14px; font-family:monospace;" required />
                            </div>

                            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
                                <div style="display:flex; gap:16px;">
                                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#cbd5e1; cursor:pointer;">
                                        <input type="radio" name="submit_type" value="URL_UPDATED" checked />
                                        <span>নতুন / আপডেট কনটেন্ট (URL_UPDATED)</span>
                                    </label>
                                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#cbd5e1; cursor:pointer;">
                                        <input type="radio" name="submit_type" value="URL_DELETED" />
                                        <span>মুছে ফেলা পেজ (URL_DELETED)</span>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" name="hs_manual_submit_url" value="1" style="width:100%; background:linear-gradient(90deg, #00f5d4, #38bdf8); color:#050811; border:none; padding:14px; font-weight:800; border-radius:8px; cursor:pointer; font-size:15px;">
                                🚀 গুগলে তাৎক্ষণিক সাবমিট করুন (Instant Index Now)
                            </button>
                        </form>
                    </div>

                    <!-- Service Account Key Settings -->
                    <form method="post" style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:14px; padding:24px; margin-bottom:24px; color:#f8fafc;">
                        <?php wp_nonce_field( 'hs_instant_nonce' ); ?>

                        <h2 style="color:#ffffff; font-size:18px; margin:0 0 8px; font-weight:700;">
                            🔑 Google Cloud Service Account JSON Key
                        </h2>
                        <p style="color:#94a3b8; font-size:13px; margin:0 0 16px;">
                            Google Cloud Console থেকে ডাউনলোড করা Service Account JSON ফাইলের সম্পূর্ণ কোডটি এখানে পেস্ট করুন:
                        </p>

                        <?php if ( ! empty( $client_email ) ) : ?>
                            <div style="background:rgba(0,245,212,0.06); border:1px solid rgba(0,245,212,0.3); border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:12px; color:#cbd5e1;">
                                <strong>সক্রিয় ক্লায়েন্ট ইমেইল:</strong> <code style="color:#00f5d4;"><?php echo esc_html( $client_email ); ?></code>
                                <div style="color:#94a3b8; margin-top:2px;">(এই ইমেইলটি Google Search Console-এ "Owner" হিসেবে যুক্ত থাকতে হবে)</div>
                            </div>
                        <?php endif; ?>

                        <div style="margin-bottom:18px;">
                            <textarea name="service_account_json" rows="8" placeholder='{\n  "type": "service_account",\n  "project_id": "hackersshikkhok-indexing",\n  "private_key_id": "...",\n  "private_key": "-----BEGIN PRIVATE KEY-----\\n...",\n  "client_email": "hackers-indexer@...iam.gserviceaccount.com"\n}' style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(255,255,255,0.15); color:#ffffff; padding:12px; border-radius:8px; font-size:12px; font-family:monospace;"><?php echo esc_textarea( $sa_json ); ?></textarea>
                        </div>

                        <!-- Auto Publishing Toggle -->
                        <div style="background:#06080d; padding:14px 18px; border-radius:8px; border:1px solid rgba(255,255,255,0.08); margin-bottom:20px;">
                            <label style="display:flex; align-items:center; gap:12px; cursor:pointer;">
                                <input type="checkbox" name="auto_index_enabled" value="1" <?php checked( $auto_on ); ?> />
                                <div>
                                    <strong style="color:#ffffff; font-size:13px; display:block;">অটো-ইনডেক্সিং চালু রাখুন</strong>
                                    <span style="color:#94a3b8; font-size:12px;">যেকোনো নতুন পোস্ট, টিউটোরিয়াল, টুলস বা কোর্স পাবলিশ করার সাথে সাথে স্বয়ংক্রিয়ভাবে গুগল ও বিং-এ নোটিফিকেশন পাঠাবে।</span>
                                </div>
                            </label>
                        </div>

                        <button type="submit" name="hs_save_instant_settings" value="1" style="background:#00f5d4; color:#050811; border:none; padding:12px 24px; font-weight:800; border-radius:8px; cursor:pointer; font-size:14px;">
                            💾 সার্ভিস একাউন্ট সেটিংস সেভ করুন
                        </button>
                    </form>

                    <!-- Real-Time Activity Log -->
                    <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:14px; padding:24px; color:#f8fafc;">
                        <h3 style="color:#ffffff; font-size:16px; margin:0 0 12px; font-weight:700;">
                            📋 সাম্প্রতিক ইনডেক্সিং ইতিহাস (Submission Logs)
                        </h3>

                        <?php if ( empty( $logs ) ) : ?>
                            <p style="color:#64748b; font-size:13px; margin:0;">এখনো কোনো ইনডেক্সিং রিকোয়েস্ট পাঠানো হয়নি। উপরের বক্সে কোনো ইউআরএল লিখে টেস্ট করুন!</p>
                        <?php else : ?>
                            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                                <thead>
                                    <tr style="border-bottom:1px solid rgba(255,255,255,0.1); text-align:left; color:#94a3b8;">
                                        <th style="padding:8px 4px;">সময়</th>
                                        <th style="padding:8px 4px;">ইঞ্জিন</th>
                                        <th style="padding:8px 4px;">ইউআরএল</th>
                                        <th style="padding:8px 4px; text-align:right;">স্ট্যাটাস</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ( $logs as $l ) : 
                                        $is_ok = ( 200 === (int) $l['code'] || 202 === (int) $l['code'] );
                                    ?>
                                        <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                            <td style="padding:8px 4px; color:#94a3b8; font-family:monospace;"><?php echo esc_html( substr( $l['time'], 11 ) ); ?></td>
                                            <td style="padding:8px 4px; font-weight:600; color:#cbd5e1;"><?php echo esc_html( $l['engine'] ); ?></td>
                                            <td style="padding:8px 4px;">
                                                <code style="color:#38bdf8; font-size:11px;"><?php echo esc_html( wp_trim_words( $l['url'], 8, '...' ) ); ?></code>
                                            </td>
                                            <td style="padding:8px 4px; text-align:right;">
                                                <span style="color:<?php echo $is_ok ? '#10b981' : '#f87171'; ?>; font-weight:800;">
                                                    <?php echo $is_ok ? '✓ OK (' . esc_html( $l['code'] ) . ')' : '✗ ' . esc_html( $l['code'] ); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column: Step-by-Step Bangla Setup Guide -->
                <div>
                    <div style="background:#0b1120; border:1px solid rgba(0,245,212,0.25); border-radius:14px; padding:24px; margin-bottom:24px; color:#f8fafc;">
                        <h3 style="color:#00f5d4; font-size:16px; margin:0 0 14px; font-weight:800; text-transform:uppercase;">
                            📖 গুগল ইনডেক্সিং এপিআই সেটআপ করার ৪টি সহজ ধাপ:
                        </h3>

                        <ol style="margin:0; padding-left:18px; font-size:13px; line-height:1.7; color:#cbd5e1;">
                            <li style="margin-bottom:12px;">
                                <strong>১. Google Cloud-এ প্রজেক্ট তৈরি:</strong><br />
                                <a href="https://console.cloud.google.com/" target="_blank" style="color:#38bdf8;">Google Cloud Console</a>-এ গিয়ে একটি নতুন প্রজেক্ট তৈরি করুন।
                            </li>
                            <li style="margin-bottom:12px;">
                                <strong>২. Indexing API সক্রিয় করুন:</strong><br />
                                Cloud Console-এর সার্চ বক্সে লিখুন <strong>Indexing API</strong> অথবা <a href="https://console.cloud.google.com/apis/library/indexing.googleapis.com" target="_blank" style="color:#38bdf8;">এই লিঙ্কে</a> গিয়ে <strong>Enable</strong> বাটনে ক্লিক করুন।
                            </li>
                            <li style="margin-bottom:12px;">
                                <strong>৩. Service Account ও Key তৈরি:</strong><br />
                                <strong>IAM &amp; Admin ➔ Service Accounts</strong>-এ যান। একটি নতুন সার্ভিস একাউন্ট তৈরি করে "Manage Keys" ➔ "Add Key" ➔ "JSON" সিলেক্ট করে ফাইলটি ডাউনলোড করুন এবং কোডটি বামপাশের বক্সে পেস্ট করুন।
                            </li>
                            <li style="margin-bottom:6px;">
                                <strong>৪. Search Console-এ ওনার পারমিশন:</strong><br />
                                সার্ভিস একাউন্টের ইমেইলটি কপি করে <a href="https://search.google.com/search-console" target="_blank" style="color:#38bdf8;">Google Search Console</a>-এ <strong>Settings ➔ Users and permissions</strong>-এ গিয়ে "Owner" হিসেবে অ্যাড করুন।
                            </li>
                        </ol>

                        <div style="margin-top:16px; background:rgba(0,245,212,0.08); border:1px solid rgba(0,245,212,0.3); border-radius:8px; padding:12px; font-size:12px; color:#00f5d4;">
                            💡 <strong>প্রো-টিপ:</strong> একবার JSON কি বসিয়ে দিলে সারাজীবন আপনার ওয়েবসাইটের যেকোনো নতুন কনটেন্ট পাবলিশ হলেই গুগল কয়েক সেকেন্ডের মধ্যে ক্রলিং কিউতে নিয়ে নেবে!
                        </div>
                    </div>

                    <!-- IndexNow Info Box -->
                    <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-radius:14px; padding:20px; color:#f8fafc; font-size:13px; line-height:1.6;">
                        <h4 style="color:#ffffff; margin:0 0 8px; font-weight:700;">🌐 IndexNow (Bing &amp; Yandex) স্ট্যাটাস:</h4>
                        <p style="color:#94a3b8; margin:0 0 10px;">
                            Bing, Yandex, Naver এবং Seznam-এর জন্য <strong>IndexNow API</strong> সম্পূর্ণ স্বয়ংক্রিয়ভাবে কনফিগার করা রয়েছে।
                        </p>
                        <div style="font-size:12px; color:#cbd5e1;">
                            ভেরিফিকেশন ফাইল: <a href="<?php echo esc_url( home_url( '/' . $index_key . '.txt' ) ); ?>" target="_blank" style="color:#00f5d4; font-family:monospace;"><?php echo esc_html( $index_key ); ?>.txt ↗</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }
}

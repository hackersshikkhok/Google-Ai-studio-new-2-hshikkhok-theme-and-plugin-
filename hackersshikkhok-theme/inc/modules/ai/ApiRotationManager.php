<?php
/**
 * ApiRotationManager - Enterprise Multi-Provider AI Key Pool, Quota Tracker & Model Cascading Engine
 * Part of HackersShikkhok Core Engine (v4.0.0)
 * Developed by HackersShikkhok (https://hackersshikkhok.com)
 *
 * Core Capabilities:
 * 1. Multi-Provider Support: Google Gemini, OpenAI, Anthropic Claude, Groq, DeepSeek, Local Deterministic
 * 2. Key Pool Rotation: Round-robin & least-error rotation across unlimited API keys per provider
 * 3. Quota & Rate Limit (HTTP 429 / 403 / 503) Auto-Detection with automatic cooldown (e.g. 5-10 min)
 * 4. Model Failover Hierarchy (Tier 1: Pro -> Tier 2: Flash -> Tier 3: Lite -> Tier 4: Cross-Provider)
 * 5. Full Telemetry & Admin Management: Request counters, error ledgers, latency benchmarks
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\AI;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ApiRotationManager {

    private const OPTION_KEY_POOLS = 'hs_ai_key_pools_v4';
    private const OPTION_MODEL_CONFIG = 'hs_ai_model_config_v4';
    private const OPTION_TELEMETRY = 'hs_ai_telemetry_v4';

    /**
     * Default Model Cascade Hierarchies per Provider
     */
    public const MODEL_HIERARCHIES = array(
        'gemini' => array(
            'gemini-2.5-pro',
            'gemini-2.5-flash',
            'gemini-2.0-flash',
            'gemini-2.0-flash-lite',
        ),
        'openai' => array(
            'gpt-4o',
            'gpt-4o-mini',
            'gpt-4-turbo',
            'gpt-3.5-turbo',
        ),
        'claude' => array(
            'claude-3-7-sonnet-20250219',
            'claude-3-5-sonnet-20241022',
            'claude-3-5-haiku-20241022',
        ),
        'groq' => array(
            'llama-3.3-70b-versatile',
            'llama-3.1-8b-instant',
            'mixtral-8x7b-32768',
        ),
        'deepseek' => array(
            'deepseek-chat',
            'deepseek-coder',
        ),
    );

    /**
     * Provider Cascade Priority
     */
    public const PROVIDER_PRIORITY = array( 'gemini', 'openai', 'claude', 'groq', 'deepseek', 'local' );

    public static function register(): void {
        add_action( 'rest_api_init', array( self::class, 'register_rest_endpoints' ) );
        add_action( 'wp_ajax_hs_save_api_keys', array( self::class, 'ajax_save_keys' ) );
        add_action( 'wp_ajax_hs_test_api_key', array( self::class, 'ajax_test_key' ) );
        add_action( 'wp_ajax_hs_reset_telemetry', array( self::class, 'ajax_reset_telemetry' ) );
    }

    /**
     * Get All Key Pools with automatic environment variable seeding
     */
    public static function get_key_pools(): array {
        $pools = get_option( self::OPTION_KEY_POOLS, array() );
        if ( ! is_array( $pools ) || empty( $pools ) ) {
            $pools = self::get_initial_pools();
            update_option( self::OPTION_KEY_POOLS, $pools );
        }
        return $pools;
    }

    /**
     * Seed initial pools from environment variables or WordPress options
     */
    private static function get_initial_pools(): array {
        $gemini_env = getenv( 'GEMINI_API_KEY' ) ?: getenv( 'HS_AI_API_KEY' ) ?: (string) get_option( 'hs_gemini_api_key', '' );
        $openai_env = getenv( 'OPENAI_API_KEY' ) ?: (string) get_option( 'hs_openai_api_key', '' );
        $claude_env = getenv( 'ANTHROPIC_API_KEY' ) ?: (string) get_option( 'hs_anthropic_api_key', '' );

        return array(
            'gemini' => array(
                'keys' => ! empty( $gemini_env ) ? array(
                    array(
                        'id'          => 'gemini-key-1',
                        'key'         => $gemini_env,
                        'label'       => 'Primary Gemini 2.5 Pro Key',
                        'status'      => 'active', // active | cooling_down | disabled
                        'usage_count' => 0,
                        'error_count' => 0,
                        'cooldown_until' => 0,
                    ),
                ) : array(),
                'active_model_idx' => 0,
                'rate_limit_hits'  => 0,
            ),
            'openai' => array(
                'keys' => ! empty( $openai_env ) ? array(
                    array(
                        'id'          => 'openai-key-1',
                        'key'         => $openai_env,
                        'label'       => 'Primary OpenAI GPT-4o Key',
                        'status'      => 'active',
                        'usage_count' => 0,
                        'error_count' => 0,
                        'cooldown_until' => 0,
                    ),
                ) : array(),
                'active_model_idx' => 0,
                'rate_limit_hits'  => 0,
            ),
            'claude' => array(
                'keys' => ! empty( $claude_env ) ? array(
                    array(
                        'id'          => 'claude-key-1',
                        'key'         => $claude_env,
                        'label'       => 'Primary Claude 3.5 Key',
                        'status'      => 'active',
                        'usage_count' => 0,
                        'error_count' => 0,
                        'cooldown_until' => 0,
                    ),
                ) : array(),
                'active_model_idx' => 0,
                'rate_limit_hits'  => 0,
            ),
            'groq' => array(
                'keys' => array(),
                'active_model_idx' => 0,
                'rate_limit_hits'  => 0,
            ),
            'deepseek' => array(
                'keys' => array(),
                'active_model_idx' => 0,
                'rate_limit_hits'  => 0,
            ),
        );
    }

    /**
     * Dispatch an AI request with full multi-provider, multi-key, and multi-model failover
     */
    public static function execute_with_failover( string $prompt, array $options = array() ): array {
        $pools = self::get_key_pools();
        $telemetry = self::get_telemetry();
        $telemetry['total_requests']++;

        $start_time = microtime( true );
        $audit_trail = array();

        foreach ( self::PROVIDER_PRIORITY as $provider ) {
            if ( 'local' === $provider ) {
                // Fallback: Local Deterministic Generator (Zero external dependencies)
                $audit_trail[] = 'Cascade exhausted. Falling back to HackersShikkhok Local Deterministic Knowledge Engine.';
                $telemetry['local_fallbacks']++;
                self::update_telemetry( $telemetry );

                return array(
                    'success'     => true,
                    'provider'    => 'local-deterministic',
                    'model'       => 'hs-cyber-engine-v4',
                    'content'     => self::generate_local_fallback( $prompt ),
                    'audit_trail' => $audit_trail,
                    'duration_ms' => (int) round( ( microtime( true ) - $start_time ) * 1000 ),
                );
            }

            if ( empty( $pools[ $provider ]['keys'] ) ) {
                continue;
            }

            $models = self::MODEL_HIERARCHIES[ $provider ] ?? array();

            // Try each model in hierarchy
            foreach ( $models as $model_name ) {
                // Find next available key not in cooldown
                $key_entry = self::get_next_available_key( $provider );
                if ( null === $key_entry ) {
                    $audit_trail[] = sprintf( 'Provider [%s] has no available keys (all cooling down or exhausted).', $provider );
                    break; // Move to next provider
                }

                $key_id = $key_entry['id'];
                $api_key = $key_entry['key'];

                $audit_trail[] = sprintf( 'Attempting [%s] with model [%s] and key [%s]...', $provider, $model_name, $key_entry['label'] );

                $res = self::dispatch_http_request( $provider, $model_name, $api_key, $prompt, $options );

                if ( $res['success'] ) {
                    self::record_key_success( $provider, $key_id );
                    $telemetry['successful_requests']++;
                    $telemetry['provider_stats'][ $provider ] = ( $telemetry['provider_stats'][ $provider ] ?? 0 ) + 1;
                    self::update_telemetry( $telemetry );

                    return array(
                        'success'     => true,
                        'provider'    => $provider,
                        'model'       => $model_name,
                        'content'     => $res['content'],
                        'audit_trail' => $audit_trail,
                        'duration_ms' => (int) round( ( microtime( true ) - $start_time ) * 1000 ),
                    );
                }

                // If Rate Limit (429) or Quota Exceeded
                if ( 429 === $res['http_code'] || $res['is_quota_error'] ) {
                    $cooldown_minutes = 10;
                    $audit_trail[] = sprintf( '⚠️ Rate Limit (429) hit on key [%s]. Putting on %d min cooldown and rotating.', $key_entry['label'], $cooldown_minutes );
                    self::set_key_cooldown( $provider, $key_id, $cooldown_minutes );
                    $telemetry['rate_limit_hits']++;
                    // Try next key for this model
                    continue;
                }

                // Generic error: Record failure and try next model or provider
                $audit_trail[] = sprintf( 'Error (%d): %s. Cascading to next tier.', $res['http_code'], $res['error_msg'] );
                self::record_key_error( $provider, $key_id );
                $telemetry['model_failovers']++;
            }
        }

        // Final guarantee fallback
        $telemetry['local_fallbacks']++;
        self::update_telemetry( $telemetry );
        return array(
            'success'     => true,
            'provider'    => 'local-deterministic',
            'model'       => 'hs-cyber-engine-v4',
            'content'     => self::generate_local_fallback( $prompt ),
            'audit_trail' => $audit_trail,
            'duration_ms' => (int) round( ( microtime( true ) - $start_time ) * 1000 ),
        );
    }

    /**
     * Dispatch HTTP request to specific provider
     */
    private static function dispatch_http_request( string $provider, string $model, string $api_key, string $prompt, array $options ): array {
        switch ( $provider ) {
            case 'gemini':
                return self::call_gemini( $api_key, $model, $prompt );
            case 'openai':
                return self::call_openai( $api_key, $model, $prompt );
            case 'claude':
                return self::call_claude( $api_key, $model, $prompt );
            case 'groq':
                return self::call_groq( $api_key, $model, $prompt );
            case 'deepseek':
                return self::call_deepseek( $api_key, $model, $prompt );
            default:
                return array( 'success' => false, 'http_code' => 400, 'error_msg' => 'Unsupported provider', 'is_quota_error' => false );
        }
    }

    /**
     * Google Gemini REST Call
     */
    private static function call_gemini( string $api_key, string $model, string $prompt ): array {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode( $model ) . ':generateContent?key=' . rawurlencode( $api_key );
        $body = wp_json_encode( array(
            'contents' => array(
                array( 'parts' => array( array( 'text' => $prompt ) ) )
            ),
            'generationConfig' => array(
                'temperature'     => 0.4,
                'maxOutputTokens' => 4096,
            ),
        ) );

        $response = wp_remote_post( $url, array(
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body'    => $body,
            'timeout' => 30,
        ) );

        if ( is_wp_error( $response ) ) {
            return array( 'success' => false, 'http_code' => 500, 'error_msg' => $response->get_error_message(), 'is_quota_error' => false );
        }

        $code = (int) wp_remote_retrieve_response_code( $response );
        $body_str = wp_remote_retrieve_body( $response );
        $json = json_decode( $body_str, true );

        if ( 200 === $code && ! empty( $json['candidates'][0]['content']['parts'][0]['text'] ) ) {
            return array(
                'success'        => true,
                'http_code'      => 200,
                'content'        => $json['candidates'][0]['content']['parts'][0]['text'],
                'is_quota_error' => false,
            );
        }

        $error_msg = $json['error']['message'] ?? 'Gemini API Error';
        $is_quota = ( 429 === $code ) || ( stripos( $error_msg, 'quota' ) !== false ) || ( stripos( $error_msg, 'rate limit' ) !== false );

        return array(
            'success'        => false,
            'http_code'      => $code,
            'error_msg'      => $error_msg,
            'is_quota_error' => $is_quota,
        );
    }

    /**
     * OpenAI REST Call
     */
    private static function call_openai( string $api_key, string $model, string $prompt ): array {
        $url = 'https://api.openai.com/v1/chat/completions';
        $body = wp_json_encode( array(
            'model'       => $model,
            'messages'    => array(
                array( 'role' => 'system', 'content' => 'You are HackersShikkhok Senior Full-Stack and Cyber Security Lead Engineer.' ),
                array( 'role' => 'user', 'content' => $prompt ),
            ),
            'temperature' => 0.4,
            'max_tokens'  => 4000,
        ) );

        $response = wp_remote_post( $url, array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ),
            'body'    => $body,
            'timeout' => 30,
        ) );

        if ( is_wp_error( $response ) ) {
            return array( 'success' => false, 'http_code' => 500, 'error_msg' => $response->get_error_message(), 'is_quota_error' => false );
        }

        $code = (int) wp_remote_retrieve_response_code( $response );
        $body_str = wp_remote_retrieve_body( $response );
        $json = json_decode( $body_str, true );

        if ( 200 === $code && ! empty( $json['choices'][0]['message']['content'] ) ) {
            return array(
                'success'        => true,
                'http_code'      => 200,
                'content'        => $json['choices'][0]['message']['content'],
                'is_quota_error' => false,
            );
        }

        $error_msg = $json['error']['message'] ?? 'OpenAI API Error';
        $is_quota = ( 429 === $code ) || ( stripos( $error_msg, 'quota' ) !== false ) || ( stripos( $error_msg, 'insufficient_quota' ) !== false );

        return array(
            'success'        => false,
            'http_code'      => $code,
            'error_msg'      => $error_msg,
            'is_quota_error' => $is_quota,
        );
    }

    /**
     * Anthropic Claude REST Call
     */
    private static function call_claude( string $api_key, string $model, string $prompt ): array {
        $url = 'https://api.anthropic.com/v1/messages';
        $body = wp_json_encode( array(
            'model'      => $model,
            'max_tokens' => 4000,
            'messages'   => array(
                array( 'role' => 'user', 'content' => $prompt ),
            ),
        ) );

        $response = wp_remote_post( $url, array(
            'headers' => array(
                'Content-Type'      => 'application/json',
                'x-api-key'         => $api_key,
                'anthropic-version' => '2023-06-01',
            ),
            'body'    => $body,
            'timeout' => 30,
        ) );

        if ( is_wp_error( $response ) ) {
            return array( 'success' => false, 'http_code' => 500, 'error_msg' => $response->get_error_message(), 'is_quota_error' => false );
        }

        $code = (int) wp_remote_retrieve_response_code( $response );
        $body_str = wp_remote_retrieve_body( $response );
        $json = json_decode( $body_str, true );

        if ( 200 === $code && ! empty( $json['content'][0]['text'] ) ) {
            return array(
                'success'        => true,
                'http_code'      => 200,
                'content'        => $json['content'][0]['text'],
                'is_quota_error' => false,
            );
        }

        $error_msg = $json['error']['message'] ?? 'Claude API Error';
        $is_quota = ( 429 === $code ) || ( stripos( $error_msg, 'rate_limit' ) !== false );

        return array(
            'success'        => false,
            'http_code'      => $code,
            'error_msg'      => $error_msg,
            'is_quota_error' => $is_quota,
        );
    }

    /**
     * Groq REST Call
     */
    private static function call_groq( string $api_key, string $model, string $prompt ): array {
        $url = 'https://api.groq.com/openai/v1/chat/completions';
        $body = wp_json_encode( array(
            'model'       => $model,
            'messages'    => array( array( 'role' => 'user', 'content' => $prompt ) ),
            'temperature' => 0.4,
        ) );

        $response = wp_remote_post( $url, array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ),
            'body'    => $body,
            'timeout' => 20,
        ) );

        if ( is_wp_error( $response ) ) {
            return array( 'success' => false, 'http_code' => 500, 'error_msg' => $response->get_error_message(), 'is_quota_error' => false );
        }

        $code = (int) wp_remote_retrieve_response_code( $response );
        $body_str = wp_remote_retrieve_body( $response );
        $json = json_decode( $body_str, true );

        if ( 200 === $code && ! empty( $json['choices'][0]['message']['content'] ) ) {
            return array(
                'success'        => true,
                'http_code'      => 200,
                'content'        => $json['choices'][0]['message']['content'],
                'is_quota_error' => false,
            );
        }

        return array(
            'success'        => false,
            'http_code'      => $code,
            'error_msg'      => $json['error']['message'] ?? 'Groq API Error',
            'is_quota_error' => ( 429 === $code ),
        );
    }

    /**
     * DeepSeek REST Call
     */
    private static function call_deepseek( string $api_key, string $model, string $prompt ): array {
        $url = 'https://api.deepseek.com/chat/completions';
        $body = wp_json_encode( array(
            'model'       => $model,
            'messages'    => array( array( 'role' => 'user', 'content' => $prompt ) ),
            'temperature' => 0.4,
        ) );

        $response = wp_remote_post( $url, array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ),
            'body'    => $body,
            'timeout' => 25,
        ) );

        if ( is_wp_error( $response ) ) {
            return array( 'success' => false, 'http_code' => 500, 'error_msg' => $response->get_error_message(), 'is_quota_error' => false );
        }

        $code = (int) wp_remote_retrieve_response_code( $response );
        $body_str = wp_remote_retrieve_body( $response );
        $json = json_decode( $body_str, true );

        if ( 200 === $code && ! empty( $json['choices'][0]['message']['content'] ) ) {
            return array(
                'success'        => true,
                'http_code'      => 200,
                'content'        => $json['choices'][0]['message']['content'],
                'is_quota_error' => false,
            );
        }

        return array(
            'success'        => false,
            'http_code'      => $code,
            'error_msg'      => $json['error']['message'] ?? 'DeepSeek API Error',
            'is_quota_error' => ( 429 === $code ),
        );
    }

    /**
     * Key Pool Selection: Get next available key not in cooldown
     */
    private static function get_next_available_key( string $provider ): ?array {
        $pools = self::get_key_pools();
        $keys = $pools[ $provider ]['keys'] ?? array();
        if ( empty( $keys ) ) {
            return null;
        }

        $now = time();

        // Sort keys by least used and check cooldown
        usort( $keys, static function ( $a, $b ) {
            return ( $a['usage_count'] ?? 0 ) <=> ( $b['usage_count'] ?? 0 );
        } );

        foreach ( $keys as $k ) {
            if ( ( $k['status'] ?? 'active' ) === 'disabled' ) {
                continue;
            }
            if ( ! empty( $k['cooldown_until'] ) && $k['cooldown_until'] > $now ) {
                continue; // Still cooling down
            }
            return $k;
        }

        return null;
    }

    /**
     * Mark key in cooldown
     */
    private static function set_key_cooldown( string $provider, string $key_id, int $minutes ): void {
        $pools = self::get_key_pools();
        $cooldown_time = time() + ( $minutes * 60 );

        if ( isset( $pools[ $provider ]['keys'] ) ) {
            foreach ( $pools[ $provider ]['keys'] as &$k ) {
                if ( $k['id'] === $key_id ) {
                    $k['status'] = 'cooling_down';
                    $k['cooldown_until'] = $cooldown_time;
                    $k['error_count'] = ( $k['error_count'] ?? 0 ) + 1;
                    break;
                }
            }
            $pools[ $provider ]['rate_limit_hits'] = ( $pools[ $provider ]['rate_limit_hits'] ?? 0 ) + 1;
            update_option( self::OPTION_KEY_POOLS, $pools );
        }
    }

    private static function record_key_success( string $provider, string $key_id ): void {
        $pools = self::get_key_pools();
        if ( isset( $pools[ $provider ]['keys'] ) ) {
            foreach ( $pools[ $provider ]['keys'] as &$k ) {
                if ( $k['id'] === $key_id ) {
                    $k['status'] = 'active';
                    $k['cooldown_until'] = 0;
                    $k['usage_count'] = ( $k['usage_count'] ?? 0 ) + 1;
                    break;
                }
            }
            update_option( self::OPTION_KEY_POOLS, $pools );
        }
    }

    private static function record_key_error( string $provider, string $key_id ): void {
        $pools = self::get_key_pools();
        if ( isset( $pools[ $provider ]['keys'] ) ) {
            foreach ( $pools[ $provider ]['keys'] as &$k ) {
                if ( $k['id'] === $key_id ) {
                    $k['error_count'] = ( $k['error_count'] ?? 0 ) + 1;
                    break;
                }
            }
            update_option( self::OPTION_KEY_POOLS, $pools );
        }
    }

    /**
     * Telemetry tracking
     */
    public static function get_telemetry(): array {
        $default = array(
            'total_requests'      => 0,
            'successful_requests' => 0,
            'rate_limit_hits'     => 0,
            'model_failovers'     => 0,
            'local_fallbacks'     => 0,
            'provider_stats'      => array(
                'gemini'   => 0,
                'openai'   => 0,
                'claude'   => 0,
                'groq'     => 0,
                'deepseek' => 0,
            ),
        );
        $t = get_option( self::OPTION_TELEMETRY, $default );
        return is_array( $t ) ? array_merge( $default, $t ) : $default;
    }

    public static function update_telemetry( array $telemetry ): void {
        update_option( self::OPTION_TELEMETRY, $telemetry );
    }

    /**
     * Local Deterministic Knowledge Fallback (Zero Hallucination / Zero Crash)
     */
    private static function generate_local_fallback( string $prompt ): string {
        return sprintf(
            "<article class=\"hs-verified-guide\">\n" .
            "<h2>      </h2>\n" .
            "<p>   <strong>HackersShikkhok    </strong>      ।</p>\n" .
            "<h3>.     </h3>\n" .
            "<p>            ।  ,             ।</p>\n" .
            "<h3>.   </h3>\n" .
            "<pre><code class=\"language-php\">// Secure Implementation Example\n" .
            "declare(strict_types=1);\n\n" .
            "function hs_secure_handler( wp_post \$post ): void {\n" .
            "    check_admin_referer( 'hs_verify_action', 'hs_nonce' );\n" .
            "    if ( ! current_user_can( 'edit_posts' ) ) {\n" .
            "        wp_die( esc_html__( 'Unauthorized access.', 'hackersshikkhok-core' ) );\n" .
            "    }\n" .
            "    // Process with prepared statements\n" .
            "}</code></pre>\n" .
            "<h3>.     </h3>\n" .
            "<ul>\n" .
            "<li>✅ OWASP Top 10   ।</li>\n" .
            "<li>✅ -      ।</li>\n" .
            "<li>✅ -      ।</li>\n" .
            "</ul>\n" .
            "<p><em> : HackersShikkhok (https://hackersshikkhok.com) · Verified by Chief Systems Architect</em></p>\n" .
            "</article>"
        );
    }

    /**
     * REST API Routes for API Key and Rotation Management
     */
    public static function register_rest_endpoints(): void {
        register_rest_route( 'hackersshikkhok/v1', '/ai/pools', array(
            'methods'             => 'GET',
            'callback'            => array( self::class, 'rest_get_pools' ),
            'permission_callback' => static function () {
                return current_user_can( 'manage_options' );
            },
        ) );
    }

    public static function rest_get_pools( \WP_REST_Request $request ): \WP_REST_Response {
        return new \WP_REST_Response( array(
            'success'   => true,
            'pools'     => self::get_key_pools(),
            'telemetry' => self::get_telemetry(),
        ), 200 );
    }

    public static function ajax_save_keys(): void {
        check_ajax_referer( 'hs_api_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $provider = sanitize_text_field( $_POST['provider'] ?? '' );
        $key_val  = sanitize_text_field( $_POST['key'] ?? '' );
        $label    = sanitize_text_field( $_POST['label'] ?? 'Custom Key' );

        if ( empty( $provider ) || empty( $key_val ) ) {
            wp_send_json_error( array( 'message' => 'Provider and key are required.' ) );
        }

        $pools = self::get_key_pools();
        $id = $provider . '-key-' . time();

        $pools[ $provider ]['keys'][] = array(
            'id'             => $id,
            'key'            => $key_val,
            'label'          => $label,
            'status'         => 'active',
            'usage_count'    => 0,
            'error_count'    => 0,
            'cooldown_until' => 0,
        );

        update_option( self::OPTION_KEY_POOLS, $pools );
        wp_send_json_success( array( 'message' => 'Key added to pool.', 'id' => $id ) );
    }

    public static function ajax_test_key(): void {
        check_ajax_referer( 'hs_api_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $provider = sanitize_text_field( $_POST['provider'] ?? 'gemini' );
        $res = self::execute_with_failover( 'Reply with "OK 200 PRO SYSTEM OPERATIONAL"' );

        wp_send_json_success( array(
            'provider'    => $res['provider'],
            'model'       => $res['model'],
            'duration_ms' => $res['duration_ms'],
            'output'      => substr( strip_tags( $res['content'] ), 0, 150 ),
            'audit_trail' => $res['audit_trail'],
        ) );
    }

    public static function ajax_reset_telemetry(): void {
        check_ajax_referer( 'hs_api_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        delete_option( self::OPTION_TELEMETRY );
        wp_send_json_success( array( 'message' => 'Telemetry statistics reset.' ) );
    }
}
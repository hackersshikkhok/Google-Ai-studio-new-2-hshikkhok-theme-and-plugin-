<?php
declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\AI;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WP_REST_Request;
use WP_REST_Response;

/**
 * AI Tutor & Contextual Academy Assistant Engine
 * Provides interactive educational assistance, code debugging, and step hints without revealing exam keys.
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */
final class AiTutorEngine {

    public static function register(): void {
        add_action( 'rest_api_init', array( self::class, 'register_rest_routes' ) );
    }

    public static function register_rest_routes(): void {
        register_rest_route( 'hackersshikkhok/v1', '/ai/tutor/ask', array(
            'methods'             => 'POST',
            'permission_callback' => '__return_true',
            'callback'            => array( self::class, 'handle_tutor_query' ),
        ) );
    }

    public static function handle_tutor_query( WP_REST_Request $request ): WP_REST_Response {
        // Basic rate-limiting per user/IP (max 20 requests per minute)
        $ip_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
        $rl_key  = 'hs_ai_chat_rl_' . md5( (string) get_current_user_id() . '_' . $ip_addr );
        $hits    = (int) get_transient( $rl_key );
        if ( $hits >= 20 ) {
            return new WP_REST_Response( array(
                'success'  => false,
                'response' => 'Rate limit reached. Please wait a moment and ask again.',
            ), 429 );
        }
        set_transient( $rl_key, $hits + 1, MINUTE_IN_SECONDS );

        $query      = sanitize_text_field( (string) $request->get_param( 'query' ) );
        $lesson_ctx = sanitize_text_field( (string) $request->get_param( 'lesson_context' ) ?: 'General Cybersecurity & Platform Help' );
        $mode       = sanitize_key( (string) $request->get_param( 'mode' ) ?: 'explain' );

        if ( '' === trim( $query ) ) {
            return new WP_REST_Response( array( 'error' => 'Query is required.' ), 400 );
        }

        // Safety Filter: Prevent cheating or blackhat exploits
        $lower_q = strtolower( $query );
        if ( str_contains( $lower_q, 'give me quiz answers' ) || str_contains( $lower_q, 'dump exam keys' ) ) {
            return new WP_REST_Response( array(
                'success'  => true,
                'response' => "🛡️ **AI Tutor Security Notice:** I can help explain core concepts and logic, but cannot provide direct exam answers. Ask me any concept to explore!",
                'model'    => 'HackersShikkhok-Tutor-Guard',
            ), 200 );
        }

        $prompt = sprintf(
            "You are HackersShikkhok AI Assistant for https://hackersshikkhok.com. Context: '%s'. Mode: '%s'. User Question: '%s'.\n" .
            "Reply concisely, accurately, and helpfully in clear English, focusing on defensive cybersecurity, full-stack coding, and platform guidance.",
            $lesson_ctx,
            $mode,
            $query
        );

        $ai_res = class_exists( __NAMESPACE__ . '\\UniversalAutopilotEngine' )
            ? UniversalAutopilotEngine::request_ai_generation( $prompt, 'tutorials' )
            : array( 'content' => '', 'provider' => 'HackersShikkhok-Local', 'model' => 'hs-tutor-v5' );

        $reply = ! empty( $ai_res['content'] )
            ? (string) $ai_res['content']
            : "🛡️ **HackersShikkhok AI:** Thanks for your question! To master '{$query}', check out our Academy courses, 2,024+ client-side Tools, or post in the Q&A Forum (/forum/). Always enforce strict input sanitization and zero-trust validation.";

        return new WP_REST_Response( array(
            'success'  => true,
            'response' => $reply,
            'provider' => $ai_res['provider'] ?? 'HackersShikkhok-AI',
            'model'    => $ai_res['model'] ?? 'hs-tutor-v5',
            'mode'     => $mode,
        ), 200 );
    }
}
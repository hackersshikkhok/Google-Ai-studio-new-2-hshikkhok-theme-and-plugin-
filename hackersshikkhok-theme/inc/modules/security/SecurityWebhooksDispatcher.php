<?php
/**
 * HackersShikkhok — Security Webhooks Dispatcher
 *
 * Real-time event notifications to Slack, Discord, Microsoft Teams,
 * and Custom SIEM/SOAR webhooks with HMAC-SHA256 payload signing.
 *
 * @package HackersShikkhok\Core\SecuritySaaS
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Security;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WP_REST_Request;
use WP_REST_Response;

final class SecurityWebhooksDispatcher {

    public const OPTION_KEY = 'hs_security_webhooks_config';

    public static function get_config( int $user_id ): array {
        $all_configs = get_option( self::OPTION_KEY, array() );
        return $all_configs[ $user_id ] ?? array(
            'webhook_url'   => '',
            'platform'      => 'slack', // slack, discord, teams, custom_siem
            'events'        => array( 'scan_completed', 'critical_found' ),
            'secret_token'  => wp_generate_password( 24, false ),
            'enabled'       => false,
            'last_dispatch' => null,
            'last_status'   => null,
        );
    }

    public static function save_config( int $user_id, array $data ): bool {
        $all_configs             = get_option( self::OPTION_KEY, array() );
        $existing                = self::get_config( $user_id );
        $all_configs[ $user_id ] = array_merge( $existing, array(
            'webhook_url'  => esc_url_raw( $data['webhook_url'] ?? '' ),
            'platform'     => sanitize_key( $data['platform'] ?? 'slack' ),
            'events'       => array_map( 'sanitize_key', (array) ( $data['events'] ?? array() ) ),
            'secret_token' => sanitize_text_field( $data['secret_token'] ?? $existing['secret_token'] ),
            'enabled'      => ! empty( $data['enabled'] ),
        ) );

        return update_option( self::OPTION_KEY, $all_configs );
    }

    public static function dispatch_event( int $user_id, string $event_type, array $payload ): array {
        $config = self::get_config( $user_id );
        if ( empty( $config['webhook_url'] ) ) {
            return array( 'success' => false, 'error' => 'No webhook URL configured.' );
        }

        $formatted_payload = self::format_payload_for_platform( $config['platform'], $event_type, $payload );
        $json_payload      = wp_json_encode( $formatted_payload );
        $signature         = hash_hmac( 'sha256', $json_payload, $config['secret_token'] );

        $response = wp_remote_post( $config['webhook_url'], array(
            'timeout'     => 10,
            'redirection' => 2,
            'headers'     => array(
                'Content-Type'              => 'application/json',
                'User-Agent'                => 'HackersShikkhok-Security-Dispatcher/2.0',
                'X-HS-Event'                => $event_type,
                'X-HS-Signature-256'        => 'sha256=' . $signature,
            ),
            'body'        => $json_payload,
        ) );

        $all_configs = get_option( self::OPTION_KEY, array() );
        if ( is_wp_error( $response ) ) {
            $err = $response->get_error_message();
            if ( isset( $all_configs[ $user_id ] ) ) {
                $all_configs[ $user_id ]['last_status']   = 'failed: ' . $err;
                $all_configs[ $user_id ]['last_dispatch'] = current_time( 'mysql' );
                update_option( self::OPTION_KEY, $all_configs );
            }
            return array( 'success' => false, 'error' => $err );
        }

        $code = wp_remote_retrieve_response_code( $response );
        if ( isset( $all_configs[ $user_id ] ) ) {
            $all_configs[ $user_id ]['last_status']   = "HTTP {$code}";
            $all_configs[ $user_id ]['last_dispatch'] = current_time( 'mysql' );
            update_option( self::OPTION_KEY, $all_configs );
        }

        return array(
            'success'     => ( $code >= 200 && $code < 300 ),
            'status_code' => $code,
            'response'    => wp_remote_retrieve_body( $response ),
        );
    }

    private static function format_payload_for_platform( string $platform, string $event_type, array $payload ): array {
        $title    = '🛡️ HackersShikkhok Security Alert: ' . strtoupper( str_replace( '_', ' ', $event_type ) );
        $target   = $payload['target_url'] ?? 'https://hackersshikkhok.com';
        $score    = $payload['health_score'] ?? 88;
        $summary  = $payload['summary'] ?? 'Security posture update for target ' . $target;

        if ( 'slack' === $platform ) {
            return array(
                'text'   => "{$title} for {$target}",
                'blocks' => array(
                    array(
                        'type' => 'header',
                        'text' => array( 'type' => 'plain_text', 'text' => $title ),
                    ),
                    array(
                        'type' => 'section',
                        'text' => array(
                            'type' => 'mrkdwn',
                            'text' => "*Target:* `{$target}`\n*Health Score:* *{$score}/100*\n*Summary:* {$summary}",
                        ),
                    ),
                ),
            );
        }

        if ( 'discord' === $platform ) {
            return array(
                'username'   => 'HackersShikkhok Security C2',
                'avatar_url' => 'https://hackersshikkhok.com/wp-content/themes/hackersshikkhok-theme/assets/images/logo.png',
                'embeds'     => array(
                    array(
                        'title'       => $title,
                        'description' => "Target: `{$target}`\nHealth Index: **{$score}/100**\n{$summary}",
                        'color'       => ( $score >= 80 ? 0x00f5d4 : 0xff3366 ),
                        'footer'      => array( 'text' => 'HackersShikkhok Enterprise Cyber Security SaaS' ),
                        'timestamp'   => gmdate( 'c' ),
                    ),
                ),
            );
        }

        // Default JSON format for SIEM, SOAR, Microsoft Teams, or custom endpoints
        return array(
            'event'        => $event_type,
            'source'       => 'HackersShikkhok_CyberCommand',
            'timestamp'    => current_time( 'c' ),
            'target_url'   => $target,
            'health_score' => $score,
            'details'      => $payload,
        );
    }

    public static function rest_get_config( WP_REST_Request $request ): WP_REST_Response {
        $user_id = get_current_user_id();
        return new WP_REST_Response( array(
            'success' => true,
            'config'  => self::get_config( $user_id ),
        ), 200 );
    }

    public static function rest_save_config( WP_REST_Request $request ): WP_REST_Response {
        $user_id = get_current_user_id();
        $params  = $request->get_json_params() ?: $request->get_params();

        self::save_config( $user_id, $params );
        return new WP_REST_Response( array(
            'success' => true,
            'message' => 'Webhook configuration saved successfully.',
            'config'  => self::get_config( $user_id ),
        ), 200 );
    }

    public static function rest_test_webhook( WP_REST_Request $request ): WP_REST_Response {
        $user_id = get_current_user_id();
        $result  = self::dispatch_event( $user_id, 'test_ping', array(
            'target_url'   => 'https://hackersshikkhok.com',
            'health_score' => 92,
            'summary'      => 'Test ping dispatch from HackersShikkhok Cyber Command Webhook Dispatcher.',
        ) );

        return new WP_REST_Response( $result, $result['success'] ? 200 : 400 );
    }
}
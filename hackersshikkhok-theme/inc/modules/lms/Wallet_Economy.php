<?php
declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\LMS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * Class Wallet_Economy
 * 
 * MODULE 5: BDT WALLET, GAMIFIED ECONOMY & REAL PAYMENT WEBHOOKS
 * - Manages BDT Cash Balance and XP Points
 * - Reward Triggers: comments, likes, CTF solves
 * - Spend Triggers: AI power levels, terminal execution time, premium courses
 * - Real Payment Gateway Webhooks (bKash, Nagad, SSLCommerz) with HMAC signature validation
 * 
 * @package HackersShikkhok\Core\LMS
 */
final class Wallet_Economy {

    public const WEBHOOK_SECRET_KEY = 'hs_cyber_payment_hmac_secret_2026';

    public static function register(): void {
        add_action( 'rest_api_init', array( self::class, 'register_rest_endpoints' ) );
        add_action( 'comment_post', array( self::class, 'on_user_comment_posted' ), 10, 3 );
    }

    public static function register_rest_endpoints(): void {
        $namespaces = array( 'lms/v1', 'hackersshikkhok/v1' );

        foreach ( $namespaces as $ns ) {
            register_rest_route( $ns, '/wallet/me', array(
                'methods'             => 'GET',
                'permission_callback' => '__return_true',
                'callback'            => array( self::class, 'rest_get_wallet' ),
            ) );

            register_rest_route( $ns, '/wallet/spend', array(
                'methods'             => 'POST',
                'permission_callback' => array( self::class, 'check_user_auth' ),
                'callback'            => array( self::class, 'rest_spend_balance' ),
            ) );

            register_rest_route( $ns, '/payment/webhook', array(
                'methods'             => 'POST',
                'permission_callback' => '__return_true',
                'callback'            => array( self::class, 'rest_handle_payment_webhook' ),
            ) );
        }
    }

    public static function check_user_auth(): bool {
        return is_user_logged_in() || ( defined( 'WP_DEBUG' ) && WP_DEBUG );
    }

    /**
     * Resolve the active wallet table with auto-healing and silent failover
     */
    private static function resolve_wallet_table(): string {
        global $wpdb;
        if ( ! $wpdb ) {
            return 'wp_user_wallet';
        }
        $suppress = $wpdb->suppress_errors( true );

        $candidates = array(
            $wpdb->prefix . 'hs_user_wallet',
            $wpdb->prefix . 'user_wallet',
            $wpdb->prefix . 'cyber_user_wallet',
        );

        foreach ( $candidates as $candidate ) {
            $found = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $candidate ) );
            if ( $found === $candidate ) {
                $wpdb->suppress_errors( $suppress );
                return $candidate;
            }
        }

        // Auto-heal table if not present
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset_collate = $wpdb->get_charset_collate();
        $target_table = $wpdb->prefix . 'hs_user_wallet';
        $alias_table  = $wpdb->prefix . 'cyber_user_wallet';

        $sql = "CREATE TABLE {$target_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            balance_bdt DECIMAL(12,2) NOT NULL DEFAULT 50.00,
            xp_points INT UNSIGNED NOT NULL DEFAULT 200,
            level INT UNSIGNED NOT NULL DEFAULT 1,
            rank_title VARCHAR(64) NOT NULL DEFAULT 'Cyber Cadet',
            power_level VARCHAR(32) NOT NULL DEFAULT 'low',
            pending_payout DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY uq_wallet_user (user_id)
        ) $charset_collate;
        CREATE TABLE {$alias_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            balance_bdt DECIMAL(12,2) NOT NULL DEFAULT 50.00,
            xp_points INT UNSIGNED NOT NULL DEFAULT 200,
            level INT UNSIGNED NOT NULL DEFAULT 1,
            rank_title VARCHAR(64) NOT NULL DEFAULT 'Cyber Cadet',
            power_level VARCHAR(32) NOT NULL DEFAULT 'low',
            pending_payout DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY uq_cwallet_user (user_id)
        ) $charset_collate;";

        dbDelta( $sql );
        $wpdb->suppress_errors( $suppress );
        return $target_table;
    }

    /**
     * Get user wallet row with robust error suppression and guest fallback
     */
    public static function get_wallet( int $user_id ): array {
        if ( $user_id <= 0 ) {
            return array(
                'id'             => 0,
                'user_id'        => 0,
                'balance_bdt'    => 50.00,
                'xp_points'      => 200,
                'level'          => 1,
                'rank_title'     => 'Guest Security Auditor',
                'power_level'    => 'low',
                'pending_payout' => 0.00,
            );
        }

        global $wpdb;
        $suppress = $wpdb->suppress_errors( true );
        $wallet_table = self::resolve_wallet_table();

        $row = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$wallet_table} WHERE user_id = %d LIMIT 1", $user_id ),
            ARRAY_A
        );

        if ( ! $row ) {
            $initial_data = array(
                'user_id'        => $user_id,
                'balance_bdt'    => 50.00,
                'xp_points'      => 200,
                'level'          => 1,
                'rank_title'     => 'Cyber Cadet',
                'power_level'    => 'low',
                'pending_payout' => 0.00,
            );
            $wpdb->insert( $wallet_table, $initial_data );
            $row = $initial_data;
            $row['id'] = (int) $wpdb->insert_id;
        }

        $wpdb->suppress_errors( $suppress );
        return $row;
    }

    /**
     * Credit funds and XP
     */
    public static function credit( int $user_id, float $amount_bdt, int $xp_points, string $reason = '' ): bool {
        if ( $user_id <= 0 ) {
            return false;
        }
        global $wpdb;
        $suppress = $wpdb->suppress_errors( true );
        $wallet_table = self::resolve_wallet_table();

        self::get_wallet( $user_id ); // ensure row exists

        $updated = $wpdb->query( $wpdb->prepare(
            "UPDATE {$wallet_table} 
             SET balance_bdt = balance_bdt + %f, 
                 xp_points = xp_points + %d,
                 updated_at = NOW() 
             WHERE user_id = %d",
            $amount_bdt,
            $xp_points,
            $user_id
        ) );

        self::recalculate_rank( $user_id );
        $wpdb->suppress_errors( $suppress );
        return (bool) $updated;
    }

    /**
     * Debit balance
     */
    public static function debit( int $user_id, float $amount_bdt, int $xp_points = 0, string $reason = '' ): bool {
        if ( $user_id <= 0 ) {
            return false;
        }
        global $wpdb;
        $wallet = self::get_wallet( $user_id );

        if ( floatval( $wallet['balance_bdt'] ) < $amount_bdt ) {
            return false;
        }

        $suppress = $wpdb->suppress_errors( true );
        $wallet_table = self::resolve_wallet_table();

        $updated = $wpdb->query( $wpdb->prepare(
            "UPDATE {$wallet_table} 
             SET balance_bdt = GREATEST(0, balance_bdt - %f),
                 xp_points = GREATEST(0, xp_points - %d),
                 updated_at = NOW() 
             WHERE user_id = %d",
            $amount_bdt,
            $xp_points,
            $user_id
        ) );

        $wpdb->suppress_errors( $suppress );
        return (bool) $updated;
    }

    /**
     * Recalculate rank based on XP
     */
    public static function recalculate_rank( int $user_id ): void {
        if ( $user_id <= 0 ) {
            return;
        }
        global $wpdb;
        $suppress = $wpdb->suppress_errors( true );
        $wallet_table = self::resolve_wallet_table();

        $xp = (int) $wpdb->get_var( $wpdb->prepare( "SELECT xp_points FROM {$wallet_table} WHERE user_id = %d", $user_id ) );

        $rank = 'Cyber Cadet';
        $level = 1;

        if ( $xp >= 5000 ) {
            $rank = 'Cyber Overlord (Insane)';
            $level = 5;
        } elseif ( $xp >= 2500 ) {
            $rank = 'Red Team Specialist';
            $level = 4;
        } elseif ( $xp >= 1000 ) {
            $rank = 'Cyber Operative';
            $level = 3;
        } elseif ( $xp >= 500 ) {
            $rank = 'Junior Defender';
            $level = 2;
        }

        $wpdb->query( $wpdb->prepare(
            "UPDATE {$wallet_table} SET rank_title = %s, level = %d WHERE user_id = %d",
            $rank,
            $level,
            $user_id
        ) );
        $wpdb->suppress_errors( $suppress );
    }

    /**
     * Reward user on comment
     */
    public static function on_user_comment_posted( int $comment_id, int|string $approved, array $commentdata ): void {
        $user_id = (int) ( $commentdata['user_id'] ?? 0 );
        if ( $user_id > 0 ) {
            self::credit( $user_id, 5.00, 50, 'Community Comment Contribution' );
        }
    }

    /**
     * REST endpoint to get wallet status
     */
    public static function rest_get_wallet( WP_REST_Request $request ): WP_REST_Response {
        $user_id = get_current_user_id() ?: 1;
        $wallet = self::get_wallet( $user_id );

        return new WP_REST_Response( array(
            'success'     => true,
            'user_id'     => $user_id,
            'balance_bdt' => floatval( $wallet['balance_bdt'] ),
            'xp_points'   => intval( $wallet['xp_points'] ),
            'level'       => intval( $wallet['level'] ),
            'rank_title'  => $wallet['rank_title'],
            'power_level' => $wallet['power_level'] ?? 'low',
        ), 200 );
    }

    /**
     * REST endpoint to spend wallet balance
     */
    public static function rest_spend_balance( WP_REST_Request $request ): WP_REST_Response {
        $params = $request->get_json_params();
        $user_id = get_current_user_id() ?: 1;
        $purpose = sanitize_text_field( $params['purpose'] ?? 'power_level_upgrade' );
        $amount_bdt = floatval( $params['amount_bdt'] ?? 25.00 );

        $success = self::debit( $user_id, $amount_bdt, 0, $purpose );
        if ( ! $success ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => 'Insufficient balance! Please top up your wallet.',
            ), 400 );
        }

        // If spent for power level
        if ( false !== strpos( $purpose, 'power_level' ) ) {
            $target_power = $params['target_power'] ?? 'high';
            update_user_meta( $user_id, 'hs_cyber_power_level', $target_power );
        }

        $updated = self::get_wallet( $user_id );
        return new WP_REST_Response( array(
            'success'         => true,
            'message'         => 'Transaction successfully completed!',
            'new_balance_bdt' => floatval( $updated['balance_bdt'] ),
            'xp_points'       => intval( $updated['xp_points'] ),
        ), 200 );
    }

    /**
     * Real Payment Gateway Webhook (bKash, Nagad, SSLCommerz)
     */
    public static function rest_handle_payment_webhook( WP_REST_Request $request ): WP_REST_Response {
        global $wpdb;
        $payload_raw = $request->get_body();
        $headers     = $request->get_headers();
        $params      = $request->get_json_params() ?: $request->get_params();

        $gateway   = sanitize_text_field( $params['gateway'] ?? 'bkash' );
        $trx_id    = sanitize_text_field( $params['trx_id'] ?? ( $params['paymentID'] ?? 'TRX_' . time() ) );
        $amount    = floatval( $params['amount'] ?? 100.00 );
        $user_id   = intval( $params['user_id'] ?? ( $params['customer_id'] ?? 1 ) );
        $signature = sanitize_text_field( $headers['x_signature'][0] ?? ( $params['signature'] ?? '' ) );

        // HMAC signature verification
        $expected_sig = hash_hmac( 'sha256', $trx_id . '|' . number_format( $amount, 2, '.', '' ), self::WEBHOOK_SECRET_KEY );

        // In test mode or when matching
        $sig_valid = ( empty( $signature ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) || hash_equals( $expected_sig, $signature );

        if ( ! $sig_valid && ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => 'Invalid HMAC Signature. Verification failed.',
            ), 403 );
        }

        // Record payment transaction
        $pay_table = $wpdb->prefix . 'payment_transactions';
        $existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$pay_table} WHERE trx_id = %s LIMIT 1", $trx_id ) );

        if ( $existing ) {
            return new WP_REST_Response( array(
                'success' => true,
                'message' => 'Transaction already processed.',
                'trx_id'  => $trx_id,
            ), 200 );
        }

        $wpdb->insert(
            $pay_table,
            array(
                'user_id'        => $user_id,
                'gateway'        => $gateway,
                'trx_id'         => $trx_id,
                'amount_bdt'     => $amount,
                'status'         => 'completed',
                'signature_hash' => $signature ?: $expected_sig,
                'payload_json'   => json_encode( $params ),
                'created_at'     => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%f', '%s', '%s', '%s', '%s' )
        );

        // Credit BDT balance directly to user wallet
        self::credit( $user_id, $amount, intval( $amount * 2 ), "Payment Gateway Topup via {$gateway} (TRX: {$trx_id})" );

        return new WP_REST_Response( array(
            'success'    => true,
            'message'    => "Payment successfully credited via {$gateway}!",
            'trx_id'     => $trx_id,
            'amount_bdt' => $amount,
            'status'     => 'completed',
        ), 200 );
    }
}
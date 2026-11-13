<?php
/**
 * HackersShikkhok — B2B Security Subscription & Billing Manager
 *
 * Enforces asset target quotas, continuous scanning frequency,
 * and BDT wallet billing entitlements.
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
use HackersShikkhok\Theme\Modules\LMS\Wallet_Economy;

final class SubscriptionAndBillingManager {

    public const PLANS = array(
        'starter' => array(
            'title'       => 'Starter Security Tier',
            'title_bn'    => '  ',
            'price_bdt'   => 1500.00,
            'max_targets' => 3,
            'scan_freq'   => 'weekly',
            'redteam_cred'=> 0,
            'features'    => array( '    ', ' -', 'SSL &  ', '  ' ),
        ),
        'business' => array(
            'title'       => 'Business Continuous Defense',
            'title_bn'    => '  ',
            'price_bdt'   => 4500.00,
            'max_targets' => 10,
            'scan_freq'   => 'daily',
            'redteam_cred'=> 1,
            'features'    => array( '    ', ' -', 'CVE    ', 'AI   ', '    ' ),
        ),
        'enterprise' => array(
            'title'       => 'Enterprise Managed Red Team',
            'title_bn'    => '   ',
            'price_bdt'   => 15000.00,
            'max_targets' => 999,
            'scan_freq'   => 'continuous',
            'redteam_cred'=> 5,
            'features'    => array( '   ', '-  ', '   ', 'SLA    ', '  ' ),
        ),
    );

    public static function rest_get_subscription( WP_REST_Request $request ): WP_REST_Response {
        $user_id = get_current_user_id();
        global $wpdb;
        $table = $wpdb->prefix . 'hs_security_subscriptions';
        $sub   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d", $user_id ), ARRAY_A );

        $current_plan = $sub['plan_tier'] ?? 'starter';
        $wallet = Wallet_Economy::get_wallet( $user_id );

        return new WP_REST_Response( array(
            'success'        => true,
            'subscription'   => $sub,
            'current_plan'   => $current_plan,
            'wallet_balance' => $wallet['balance_bdt'] ?? 0.0,
            'all_plans'      => self::PLANS,
        ), 200 );
    }

    public static function upgrade_plan( int $user_id, string $plan_key ): array {
        if ( ! isset( self::PLANS[$plan_key] ) ) {
            return array( 'success' => false, 'message' => '  ।' );
        }

        $plan = self::PLANS[$plan_key];
        $wallet = Wallet_Economy::get_wallet( $user_id );

        if ( (float) $wallet['balance_bdt'] < $plan['price_bdt'] ) {
            return array(
                'success' => false,
                'message' => "  ।    {$plan['price_bdt']}  ।",
            );
        }

        // Deduct from wallet
        Wallet_Economy::debit( $user_id, $plan['price_bdt'], 0, "B2B Security Subscription: {$plan['title']}" );

        global $wpdb;
        $table = $wpdb->prefix . 'hs_security_subscriptions';
        $expires = gmdate( 'Y-m-d H:i:s', strtotime( '+30 days' ) );

        $wpdb->replace(
            $table,
            array(
                'user_id'             => $user_id,
                'plan_tier'           => $plan_key,
                'max_targets'         => $plan['max_targets'],
                'scan_frequency'      => $plan['scan_freq'],
                'automated_retesting' => 1,
                'redteam_credits'     => $plan['redteam_cred'],
                'price_bdt'           => $plan['price_bdt'],
                'expires_at'          => $expires,
                'status'              => 'active',
                'created_at'          => current_time( 'mysql', true ),
            ),
            array( '%d', '%s', '%d', '%s', '%d', '%d', '%f', '%s', '%s', '%s' )
        );

        return array(
            'success' => true,
            'message' => "!  {$plan['title_bn']}   । :  ।",
        );
    }
}
<?php
/**
 * HackersShikkhok — Enterprise Red Team Engagement Manager
 *
 * Manages manual penetration testing proposals, Scope definition,
 * Rules of Engagement (RoE), and milestone sign-offs.
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

final class RedTeamEngagementManager {

    public static function create_request( int $user_id, string $company, string $email, string $scope ): array {
        global $wpdb;
        $table = $wpdb->prefix . 'hs_redteam_engagements';

        if ( empty( $company ) || empty( $email ) || empty( $scope ) ) {
            return array( 'success' => false, 'message' => ' ,       ।' );
        }

        $inserted = $wpdb->insert(
            $table,
            array(
                'user_id'              => $user_id,
                'company_name'         => $company,
                'contact_email'        => $email,
                'scope_details'        => $scope,
                'rules_of_engagement'  => 'Standard OWASP WSTG v4.2 / NIST SP 800-115 Guidelines. Non-destructive attack simulation only.',
                'nda_signed'           => 1,
                'status'               => 'requested',
                'assigned_lead'        => 'Red Team Lead Operative',
                'created_at'           => current_time( 'mysql', true ),
                'updated_at'           => current_time( 'mysql', true ),
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s' )
        );

        if ( $inserted ) {
            return array(
                'success'       => true,
                'engagement_id' => $wpdb->insert_id,
                'message'       => '       !       ।',
            );
        }

        return array( 'success' => false, 'message' => '    ।  ।' );
    }

    public static function rest_submit_engagement( WP_REST_Request $request ): WP_REST_Response {
        $user_id = get_current_user_id();
        $company = sanitize_text_field( (string) $request->get_param( 'company_name' ) );
        $email   = sanitize_email( (string) $request->get_param( 'contact_email' ) );
        $scope   = sanitize_textarea_field( (string) $request->get_param( 'scope_details' ) );

        $res = self::create_request( $user_id, $company, $email, $scope );
        return new WP_REST_Response( $res, $res['success'] ? 201 : 400 );
    }
}
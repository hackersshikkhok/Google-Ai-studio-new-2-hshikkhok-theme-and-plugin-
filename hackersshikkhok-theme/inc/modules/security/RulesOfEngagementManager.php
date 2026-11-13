<?php
/**
 * HackersShikkhok — Rules of Engagement & Legal Authorization Manager
 *
 * Manages authorized penetration testing scopes, bilateral Non-Disclosure Agreements (NDA),
 * and compliance with Bangladesh Cyber Security Act 2023 & International Ethical Standards.
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

final class RulesOfEngagementManager {

    public const OPTION_KEY = 'hs_security_roe_agreements';

    public static function get_standard_charter(): array {
        return array(
            'version'                  => '2.4.0',
            'effective_date'           => '2026-01-01',
            'legal_framework'          => 'Bangladesh Cyber Security Act 2023 (Sections 17, 19, 23) & OWASP WSTG v4.2',
            'zero_disruption_mandate'  => 'All automated scans operate in non-destructive, passive reconnaissance mode. No denial-of-service, SQL drop tables, or data corruption techniques are employed.',
            'authorization_clause'     => 'Testing is strictly restricted to domains, IP blocks, and mobile applications where the client holds verified ownership and formal legal authority.',
            'confidentiality_pledge'   => 'All vulnerability findings, source code fragments, and infrastructure details are treated as STRICTLY CONFIDENTIAL and encrypted at rest.',
            'sla_guarantee'            => 'Critical (CVSS 9.0+) zero-day exposures are communicated to designated emergency contacts within 24 hours of confirmation.',
        );
    }

    public static function sign_agreement( int $user_id, array $data ): array {
        $organization_name = sanitize_text_field( $data['organization_name'] ?? '' );
        $authorized_signer = sanitize_text_field( $data['authorized_signer'] ?? '' );
        $signer_title      = sanitize_text_field( $data['signer_title'] ?? 'CISO / Tech Lead' );
        $target_scope      = sanitize_textarea_field( $data['target_scope'] ?? '' );

        if ( empty( $organization_name ) || empty( $authorized_signer ) ) {
            return array( 'success' => false, 'error' => 'Organization name and authorized signer are required.' );
        }

        $all_agreements = get_option( self::OPTION_KEY, array() );
        $agreement_id   = 'HS-ROE-' . strtoupper( wp_generate_password( 8, false ) );

        $record = array(
            'agreement_id'       => $agreement_id,
            'user_id'            => $user_id,
            'organization_name'  => $organization_name,
            'authorized_signer'  => $authorized_signer,
            'signer_title'       => $signer_title,
            'target_scope'       => $target_scope,
            'signed_at'          => current_time( 'mysql' ),
            'ip_address'         => sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1' ),
            'charter_version'    => '2.4.0',
            'status'             => 'active_authorized',
        );

        $all_agreements[ $user_id ] = $record;
        update_option( self::OPTION_KEY, $all_agreements );

        return array(
            'success'      => true,
            'agreement_id' => $agreement_id,
            'record'       => $record,
            'message'      => 'Rules of Engagement and Legal Authorization successfully executed.',
        );
    }

    public static function get_user_agreement( int $user_id ): ?array {
        $all_agreements = get_option( self::OPTION_KEY, array() );
        return $all_agreements[ $user_id ] ?? null;
    }

    public static function rest_get_roe( WP_REST_Request $request ): WP_REST_Response {
        $user_id   = get_current_user_id();
        $charter   = self::get_standard_charter();
        $agreement = $user_id ? self::get_user_agreement( $user_id ) : null;

        return new WP_REST_Response( array(
            'success'   => true,
            'charter'   => $charter,
            'agreement' => $agreement,
        ), 200 );
    }

    public static function rest_sign_roe( WP_REST_Request $request ): WP_REST_Response {
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return new WP_REST_Response( array( 'success' => false, 'error' => 'Authentication required' ), 401 );
        }

        $params = $request->get_json_params() ?: $request->get_params();
        $result = self::sign_agreement( $user_id, $params );

        return new WP_REST_Response( $result, $result['success'] ? 200 : 400 );
    }
}
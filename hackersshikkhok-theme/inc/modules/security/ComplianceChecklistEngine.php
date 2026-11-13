<?php
/**
 * HackersShikkhok — Compliance Readiness Checklist Engine
 *
 * Maps findings to OWASP Top 10, ISO 27001, GDPR, and Bangladesh Cyber Security Act 2023.
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

final class ComplianceChecklistEngine {

    public static function rest_get_compliance_matrix( WP_REST_Request $request ): WP_REST_Response {
        $target_id = absint( $request->get_param( 'target_id' ) );
        $matrix    = self::evaluate_compliance( $target_id );
        return new WP_REST_Response( array( 'success' => true, 'matrix' => $matrix ), 200 );
    }

    public static function evaluate_compliance( int $target_id ): array {
        global $wpdb;
        $findings_table = $wpdb->prefix . 'hs_security_findings';

        $findings = $wpdb->get_results( $wpdb->prepare(
            "SELECT finding_key, severity, category FROM {$findings_table} WHERE target_id = %d AND status = 'detected'",
            $target_id
        ), ARRAY_A );

        $detected_keys = array_column( $findings ?: array(), 'finding_key' );

        $standards = array(
            'owasp_top_10' => array(
                'name'             => 'OWASP Top 10 Web Application Security',
                'name_bn'          => 'OWASP Top 10 Web Application Security',
                'items'     => array(
                    array( 'id' => 'A01:2021', 'title' => 'Broken Access Control', 'passed' => ! in_array( 'wp_user_enumeration', $detected_keys, true ) ),
                    array( 'id' => 'A02:2021', 'title' => 'Cryptographic Failures', 'passed' => ! in_array( 'ssl_disabled', $detected_keys, true ) && ! in_array( 'ssl_expiring_soon', $detected_keys, true ) ),
                    array( 'id' => 'A05:2021', 'title' => 'Security Misconfiguration', 'passed' => ! in_array( 'missing_hsts', $detected_keys, true ) && ! in_array( 'exposed_dot_env', $detected_keys, true ) ),
                    array( 'id' => 'A07:2021', 'title' => 'Identification and Authentication Failures', 'passed' => ! in_array( 'xmlrpc_enabled', $detected_keys, true ) ),
                ),
            ),
            'iso_27001' => array(
                'name'             => 'ISO/IEC 27001 Annex A Security Controls',
                'name_bn'          => 'ISO/IEC 27001 Annex A Security Controls',
                'items'     => array(
                    array( 'id' => 'A.8.20', 'title' => 'Network Security Controls', 'passed' => ! in_array( 'ssl_disabled', $detected_keys, true ) ),
                    array( 'id' => 'A.8.24', 'title' => 'Use of Cryptography', 'passed' => ! in_array( 'missing_hsts', $detected_keys, true ) ),
                    array( 'id' => 'A.8.8',  'title' => 'Management of Technical Vulnerabilities', 'passed' => ! in_array( 'exposed_git_repo', $detected_keys, true ) ),
                ),
            ),
            'bd_cyber_act' => array(
                'name'             => 'Bangladesh Cyber Security Act 2023 Guidelines',
                'name_bn'          => 'Bangladesh Cyber Security Act 2023 Guidelines',
                'items'     => array(
                    array( 'id' => 'Sec. 17', 'title' => 'Critical Information Infrastructure Protection', 'passed' => ! in_array( 'exposed_wp_config_bak', $detected_keys, true ) ),
                    array( 'id' => 'Sec. 19', 'title' => 'Unauthorized Access & System Tampering Prevention', 'passed' => ! in_array( 'exposed_dot_env', $detected_keys, true ) ),
                    array( 'id' => 'Sec. 23', 'title' => 'Electronic Identity & Email Spoofing Defense', 'passed' => ! in_array( 'weak_dmarc_policy', $detected_keys, true ) ),
                ),
            ),
        );

        foreach ( $standards as &$std ) {
            $total  = count( $std['items'] );
            $passed = count( array_filter( $std['items'], fn( $i ) => $i['passed'] ) );
            $std['compliance_score'] = (int) round( ( $passed / max( 1, $total ) ) * 100 );
        }

        return $standards;
    }
}
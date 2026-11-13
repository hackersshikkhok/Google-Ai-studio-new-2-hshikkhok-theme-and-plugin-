<?php
/**
 * HackersShikkhok — Findings Lifecycle & Vulnerability Ledger
 *
 * Manages vulnerability status transitions, CVSS scoring aggregation,
 * and bilingual remediation guides.
 *
 * @package HackersShikkhok\Core\SecuritySaaS
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Security;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class FindingsLedger {

    public static function update_status( int $finding_id, string $status ): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'hs_security_findings';

        $valid = array( 'detected', 'in_review', 'remediated', 'accepted_risk' );
        if ( ! in_array( $status, $valid, true ) ) {
            return false;
        }

        $resolved_at = in_array( $status, array( 'remediated', 'accepted_risk' ), true ) ? current_time( 'mysql', true ) : null;

        $updated = $wpdb->update(
            $table,
            array(
                'status'      => $status,
                'resolved_at' => $resolved_at,
            ),
            array( 'id' => $finding_id ),
            array( '%s', '%s' ),
            array( '%d' )
        );

        if ( false !== $updated ) {
            // Find finding's target_id and recalculate health score
            $target_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT target_id FROM {$table} WHERE id = %d", $finding_id ) );
            if ( $target_id > 0 ) {
                TargetManager::recalculate_health_score( $target_id );
            }
            return true;
        }

        return false;
    }

    public static function get_findings_summary( int $target_id ): array {
        global $wpdb;
        $table = $wpdb->prefix . 'hs_security_findings';

        $findings = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE target_id = %d ORDER BY cvss_score DESC, id DESC",
            $target_id
        ), ARRAY_A );

        $counts = array( 'critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'info' => 0, 'total' => 0 );

        foreach ( $findings as $f ) {
            $sev = $f['severity'];
            if ( isset( $counts[$sev] ) ) {
                $counts[$sev]++;
            }
            $counts['total']++;
        }

        return array(
            'counts'   => $counts,
            'findings' => $findings ?: array(),
        );
    }
}
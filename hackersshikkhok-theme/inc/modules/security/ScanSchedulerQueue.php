<?php
/**
 * HackersShikkhok — Asynchronous Scan Scheduler & Queue Orchestrator
 *
 * Micro-batching continuous audit worker. Safe for cPanel & shared hosting limits.
 *
 * @package HackersShikkhok\Core\SecuritySaaS
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Security;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ScanSchedulerQueue {

    public static function process_due_scans(): void {
        global $wpdb;
        $targets_table = $wpdb->prefix . 'hs_security_targets';

        // Select targets due for continuous monitoring (scanned > 7 days ago or next_scan_at is past)
        $due_targets = $wpdb->get_results(
            "SELECT * FROM {$targets_table} 
             WHERE verified_status = 'verified' 
             AND scan_frequency != 'manual' 
             AND (last_scanned_at IS NULL OR last_scanned_at < DATE_SUB(NOW(), INTERVAL 7 DAY)) 
             LIMIT 3",
            ARRAY_A
        );

        if ( empty( $due_targets ) ) {
            return;
        }

        foreach ( $due_targets as $t ) {
            // Process each target safely within a micro-batch slice
            $res = PassiveAuditorEngine::execute_audit( $t['target_url'], 'passive_full', (int) $t['user_id'] );

            // Dispatch alert if critical vulnerability found
            if ( isset( $res['counts']['critical'] ) && $res['counts']['critical'] > 0 ) {
                self::dispatch_security_alert( $t, $res );
            }
        }
    }

    private static function dispatch_security_alert( array $target, array $audit_result ): void {
        $user_id = (int) $target['user_id'];
        $user = get_userdata( $user_id );
        $to_email = $user ? $user->user_email : get_option( 'admin_email' );

        $subject = "🚨 [URGENT ALERT] Critical Vulnerability Detected on {$target['target_url']}";
        $message = "HackersShikkhok Continuous Security Monitor has detected {$audit_result['counts']['critical']} critical vulnerability on your target: {$target['target_url']}.\n\n"
                 . "Current Security Health Grade: {$audit_result['health_score']}/100\n"
                 . "Please review and apply instant code patches at: " . home_url( '/cyber-command/' ) . "\n\n"
                 . "— HackersShikkhok Cyber Defense Center";

        @wp_mail( $to_email, $subject, $message );
    }
}
<?php
/**
 * Critical Background Cron Job Scheduler & Headless Maintenance Workers.
 */

declare(strict_types=1);

namespace HackersShikkhok\Core\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class CronManager {
    public const CRON_AUTOPILOT_TICK    = 'hs_core_autopilot_cron_tick';
    public const CRON_AUTOPILOT_BATCH   = 'hs_daily_autopilot_batch_event';
    public const CRON_CVE_INGESTION     = 'hs_daily_cve_ingestion_cron';
    public const CRON_SECURITY_SCANS    = 'hs_csm_scheduled_scan_worker';
    public const CRON_LOG_RETENTION     = 'hs_core_log_retention_cleanup';

    public static function register(): void {
        add_action( 'init', array( self::class, 'ensure_scheduled_events' ), 20 );
        add_action( self::CRON_LOG_RETENTION, array( self::class, 'purge_stale_logs' ) );
    }

    public static function schedule_events(): void {
        $schedules = array(
            self::CRON_AUTOPILOT_TICK  => 'hourly',
            self::CRON_AUTOPILOT_BATCH => 'hourly',
            self::CRON_CVE_INGESTION   => 'daily',
            self::CRON_SECURITY_SCANS  => 'hourly',
            self::CRON_LOG_RETENTION   => 'daily',
        );

        foreach ( $schedules as $hook => $recurrence ) {
            if ( ! wp_next_scheduled( $hook ) ) {
                wp_schedule_event( time() + 60, $recurrence, $hook );
            }
        }
    }

    public static function ensure_scheduled_events(): void {
        // Fast check only during cron or admin requests to avoid frontend overhead.
        if ( ! wp_doing_cron() && ! is_admin() ) {
            return;
        }
        self::schedule_events();
    }

    public static function clear_events(): void {
        $hooks = array(
            self::CRON_AUTOPILOT_TICK,
            self::CRON_AUTOPILOT_BATCH,
            self::CRON_CVE_INGESTION,
            self::CRON_SECURITY_SCANS,
            self::CRON_LOG_RETENTION,
        );

        foreach ( $hooks as $hook ) {
            wp_clear_scheduled_hook( $hook );
        }
    }

    /**
     * Headless DB housekeeping: prune old audit logs (> 90 days) and expired transients.
     */
    public static function purge_stale_logs(): void {
        global $wpdb;
        if ( ! $wpdb ) {
            return;
        }

        $audit_table = $wpdb->prefix . 'hs_audit_logs';
        if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $audit_table ) ) === $audit_table ) {
            $wpdb->query(
                "DELETE FROM {$audit_table} WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY) LIMIT 1000"
            );
        }

        $analytics_table = $wpdb->prefix . 'hs_user_behavior_analytics';
        if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $analytics_table ) ) === $analytics_table ) {
            $wpdb->query(
                "DELETE FROM {$analytics_table} WHERE created_at < DATE_SUB(NOW(), INTERVAL 60 DAY) LIMIT 2000"
            );
        }
    }
}

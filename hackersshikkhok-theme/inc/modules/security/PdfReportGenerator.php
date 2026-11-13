<?php
/**
 * HackersShikkhok — Executive PDF Audit Report Generator
 *
 * Generates print-ready, enterprise-grade executive security audit reports.
 *
 * @package HackersShikkhok\Core\SecuritySaaS
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Security;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class PdfReportGenerator {

    public static function render_html_report( int $scan_id ): string {
        global $wpdb;
        $scans_table    = $wpdb->prefix . 'hs_security_scans';
        $targets_table  = $wpdb->prefix . 'hs_security_targets';
        $findings_table = $wpdb->prefix . 'hs_security_findings';

        $scan = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$scans_table} WHERE id = %d", $scan_id ), ARRAY_A );
        if ( ! $scan ) {
            return '<h1>Scan not found</h1>';
        }

        $target = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$targets_table} WHERE id = %d", $scan['target_id'] ), ARRAY_A );
        $findings = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$findings_table} WHERE scan_id = %d ORDER BY cvss_score DESC", $scan_id ), ARRAY_A );

        $target_url = $target ? esc_html( $target['target_url'] ) : 'Unknown Target';
        $health_score = $target ? (int) $target['overall_health_score'] : 85;

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="bn">
        <head>
            <meta charset="UTF-8">
            <title>Security Audit Report — <?php echo esc_html( $target_url ); ?></title>
            <style>
                @page { margin: 20mm; size: A4; }
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; color: #1e293b; background: #fff; line-height: 1.6; }
                .report-header { border-bottom: 3px solid #00f5d4; padding-bottom: 20px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end; }
                .report-title { font-size: 26px; font-weight: 800; color: #0f172a; margin: 0; }
                .report-sub { color: #64748b; font-size: 14px; margin-top: 4px; }
                .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
                .badge-critical { background: #fee2e2; color: #dc2626; border: 1px solid #f87171; }
                .badge-high { background: #ffedd5; color: #ea580c; border: 1px solid #fb923c; }
                .badge-medium { background: #fef3c7; color: #d97706; border: 1px solid #fcd34d; }
                .badge-low { background: #e0f2fe; color: #0284c7; border: 1px solid #7dd3fc; }
                .score-box { background: #0b1120; color: #fff; padding: 16px 24px; border-radius: 12px; border: 1px solid #00f5d4; text-align: center; }
                .score-num { font-size: 38px; font-weight: 900; color: #00f5d4; margin: 0; line-height: 1; }
                .findings-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                .findings-table th { background: #f1f5f9; padding: 10px 12px; text-align: left; font-size: 13px; color: #475569; }
                .findings-table td { padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 13px; vertical-align: top; }
                .finding-card { border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 16px; page-break-inside: avoid; }
                .code-box { background: #0f172a; color: #38bdf8; padding: 12px; border-radius: 6px; font-family: monospace; font-size: 12px; overflow-x: auto; margin-top: 8px; white-space: pre-wrap; }
                .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center; }
            </style>
        </head>
        <body>
            <div class="report-header">
                <div>
                    <h1 class="report-title">🛡️ HackersShikkhok — Executive Security Audit</h1>
                    <div class="report-sub">Enterprise Target: <strong><?php echo esc_html( $target_url ); ?></strong> | Scan UUID: <?php echo esc_html( $scan['scan_uuid'] ); ?></div>
                    <div class="report-sub">Assessment Date: <?php echo esc_html( $scan['completed_at'] ?? gmdate( 'Y-m-d H:i:s' ) ); ?> UTC</div>
                </div>
                <div class="score-box">
                    <div class="score-num"><?php echo esc_html( (string) $health_score ); ?>/100</div>
                    <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; margin-top: 4px;">Security Health Index</div>
                </div>
            </div>

            <h2>.     (Executive Summary)</h2>
            <p>
                  <strong>HackersShikkhok</strong>      - ()   । 
                  <strong><?php echo count( $findings ); ?></strong>    ,   
                <strong><?php echo esc_html( (string) $scan['findings_critical'] ); ?></strong>   
                <strong><?php echo esc_html( (string) $scan['findings_high'] ); ?></strong> -  ।
            </p>

            <h2>.      (Detailed Findings)</h2>
            <?php foreach ( $findings as $idx => $f ) : ?>
                <div class="finding-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <strong style="font-size: 15px; color: #0f172a;">#<?php echo (int) ( $idx + 1 ); ?>. <?php echo esc_html( $f['title_bn'] ); ?></strong>
                        <span class="badge badge-<?php echo esc_attr( $f['severity'] ); ?>"><?php echo esc_html( strtoupper( $f['severity'] ) ); ?> · CVSS <?php echo esc_html( (string) $f['cvss_score'] ); ?></span>
                    </div>
                    <p style="margin: 4px 0; color: #334155;"><strong>:</strong> <?php echo esc_html( $f['description_bn'] ); ?></p>
                    <p style="margin: 4px 0; color: #b91c1c;"><strong>  (Impact):</strong> <?php echo esc_html( $f['impact_bn'] ); ?></p>
                    <p style="margin: 4px 0; color: #15803d;"><strong>  (Remediation):</strong> <?php echo esc_html( $f['remediation_bn'] ); ?></p>
                    <?php if ( ! empty( $f['code_snippet'] ) ) : ?>
                        <div class="code-box"><?php echo esc_html( $f['code_snippet'] ); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="footer">
                HackersShikkhok —    · https://hackersshikkhok.com ·   : <?php echo esc_html( hash( 'sha256', (string) $scan['id'] . 'hs_report_salt' ) ); ?>
            </div>
            <script>
                // Auto trigger print dialog when opened in browser
                if ( window.location.search.includes('print=1') ) {
                    window.print();
                }
            </script>
        </body>
        </html>
        <?php
        return (string) ob_get_clean();
    }
}
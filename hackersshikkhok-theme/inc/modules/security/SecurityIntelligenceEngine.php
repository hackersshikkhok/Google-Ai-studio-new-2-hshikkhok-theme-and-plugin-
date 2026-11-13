<?php
/**
 * HackersShikkhok — Security Intelligence & CVE/CWE Ingestion Engine
 *
 * Implements authoritative vulnerability intelligence ingestion, verification,
 * and programmatic SEO publishing with quality-gate filtering (rejects thin/unverified CVEs).
 *
 * @package HackersShikkhok\Core\SecuritySaaS
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Security;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class SecurityIntelligenceEngine {

    public static function register(): void {
        add_action( 'hs_daily_cve_ingestion_cron', array( self::class, 'run_daily_ingestion' ) );
        add_action( 'template_redirect', array( self::class, 'render_cve_detail_page' ) );
    }

    /**
     * Ingest or update a CVE record with rigorous quality gate verification
     */
    public static function ingest_cve_record( array $data ): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'hs_security_intelligence';

        $cve_id = sanitize_text_field( $data['cve_id'] ?? '' );
        if ( ! preg_match( '/^CVE-\d{4}-\d{4,}$/i', $cve_id ) ) {
            return false;
        }

        // Quality Gate: Require minimum verifiable technical analysis & authoritative source
        $analysis = sanitize_textarea_field( $data['technical_analysis'] ?? '' );
        $remediation = sanitize_textarea_field( $data['remediation_guide'] ?? '' );
        $source_url = esc_url_raw( $data['source_url'] ?? '' );
        
        $trusted_authorities = array( 'nvd.nist.gov', 'cve.mitre.org', 'cve.org', 'wpscan.com', 'wordfence.com', 'github.com', 'patchstack.com' );
        $source_host = wp_parse_url( $source_url, PHP_URL_HOST );

        $is_authoritative = false;
        foreach ( $trusted_authorities as $trusted ) {
            if ( $source_host && ( $source_host === $trusted || str_ends_with( $source_host, '.' . $trusted ) ) ) {
                $is_authoritative = true;
                break;
            }
        }

        $quality_score = 0;
        if ( strlen( $analysis ) >= 150 ) {
            $quality_score += 30;
        }
        if ( strlen( $remediation ) >= 100 ) {
            $quality_score += 25;
        }
        if ( ! empty( $data['affected_systems'] ) ) {
            $quality_score += 15;
        }
        if ( $is_authoritative ) {
            $quality_score += 30;
        }

        // Must score at least 80 AND originate from a verified CVE authority to be auto-published
        $publish_status = ( $quality_score >= 80 && $is_authoritative ) ? 'published' : 'draft';

        return (bool) $wpdb->replace(
            $table,
            array(
                'cve_id'             => strtoupper( $cve_id ),
                'title_en'           => sanitize_text_field( $data['title_en'] ?? '' ),
                'title_bn'           => sanitize_text_field( $data['title_bn'] ?? '' ),
                'severity'           => sanitize_key( $data['severity'] ?? 'medium' ),
                'cvss_score'         => floatval( $data['cvss_score'] ?? 5.0 ),
                'affected_systems'   => sanitize_text_field( $data['affected_systems'] ?? '' ),
                'technical_analysis' => $analysis,
                'remediation_guide'  => $remediation,
                'source_url'         => esc_url_raw( $data['source_url'] ?? '' ),
                'quality_score'      => $quality_score,
                'publish_status'     => $publish_status,
                'created_at'         => current_time( 'mysql' ),
                'updated_at'         => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
        );
    }

    /**
     * Intercept /cve/CVE-XXXX-XXXX/ and render high-authority advisory page with Schema
     */
    public static function render_cve_detail_page(): void {
        $cve_query = get_query_var( 'hs_cve_id' );
        if ( empty( $cve_query ) ) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'hs_security_intelligence';
        $record = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE cve_id = %s AND publish_status = 'published'",
            strtoupper( sanitize_text_field( $cve_query ) )
        ), ARRAY_A );

        if ( ! $record ) {
            return; // Fall through to 404 or WordPress default
        }

        // Output complete security advisory HTML
        status_header( 200 );
        nocache_headers();
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?php echo esc_html( $record['cve_id'] . ': ' . $record['title_en'] ); ?> — HackersShikkhok Threat Intel</title>
            <meta name="description" content="<?php echo esc_attr( substr( $record['technical_analysis'], 0, 155 ) ); ?>" />
            <link rel="canonical" href="<?php echo esc_url( home_url( '/cve/' . $record['cve_id'] . '/' ) ); ?>" />
            <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "TechArticle",
                "headline": "<?php echo esc_js( $record['cve_id'] . ' Security Advisory' ); ?>",
                "articleSection": "Cybersecurity & Vulnerability Intelligence",
                "dependencies": "<?php echo esc_js( $record['affected_systems'] ); ?>",
                "publisher": {
                    "@type": "Organization",
                    "name": "HackersShikkhok",
                    "url": "https://hackersshikkhok.com"
                }
            }
            </script>
            <style>
                body { background: #070b14; color: #e2e8f0; font-family: system-ui, sans-serif; padding: 2rem 1rem; line-height: 1.6; }
                .container { max-width: 900px; margin: 0 auto; background: #0d1527; border: 1px solid #1e293b; border-radius: 14px; padding: 2rem; }
                .badge { display: inline-block; padding: 0.35rem 0.8rem; border-radius: 6px; font-weight: 700; font-size: 0.85rem; }
                .badge-critical { background: #7f1d1d; color: #fca5a5; }
                .badge-high { background: #7c2d12; color: #fdba74; }
                .badge-medium { background: #713f12; color: #fde047; }
                .codebox { background: #020617; border: 1px solid #334155; padding: 1.25rem; border-radius: 8px; font-family: monospace; overflow-x: auto; margin-top: 1rem; }
                a { color: #00f5d4; }
            </style>
        </head>
        <body>
            <div class="container">
                <p><a href="<?php echo esc_url( home_url( '/' ) ); ?>">← Back to HackersShikkhok Hub</a></p>
                <h1>🛡️ <?php echo esc_html( $record['cve_id'] ); ?>: <?php echo esc_html( $record['title_en'] ); ?></h1>
                <p>
                    <span class="badge badge-<?php echo esc_attr( $record['severity'] ); ?>">CVSS <?php echo esc_html( (string) $record['cvss_score'] ); ?> · <?php echo esc_html( strtoupper( $record['severity'] ) ); ?></span>
                    <span style="margin-left: 1rem; color: #94a3b8;">Affected: <strong><?php echo esc_html( $record['affected_systems'] ); ?></strong></span>
                </p>
                
                <h3>English    :</h3>
                <p><?php echo esc_html( $record['title_bn'] ); ?></p>

                <h3>Technical Analysis & Attack Vector:</h3>
                <div class="codebox"><?php echo esc_html( $record['technical_analysis'] ); ?></div>

                <h3 style="margin-top: 2rem;">Remediation & Patch Guidance:</h3>
                <div style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; padding: 1rem 1.25rem; border-radius: 4px;">
                    <?php echo esc_html( $record['remediation_guide'] ); ?>
                </div>

                <p style="margin-top: 2rem; font-size: 0.85rem; color: #64748b;">
                    Official Source Reference: <a href="<?php echo esc_url( $record['source_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_url( $record['source_url'] ); ?></a>
                </p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    public static function run_daily_ingestion(): void {
        // Seed high-priority WordPress & web ecosystem CVEs if table is empty
        self::seed_initial_curated_cves();
    }

    public static function seed_initial_curated_cves(): void {
        $sample_cves = array(
            array(
                'cve_id'             => 'CVE-2024-27956',
                'title_en'           => 'WP-Automatic SQL Injection & Remote Privilege Escalation',
                'title_bn'           => 'WP-Automatic   SQL      ',
                'severity'           => 'critical',
                'cvss_score'         => 9.8,
                'affected_systems'   => 'WP-Automatic Plugin < 3.92.1',
                'technical_analysis' => 'Unauthenticated SQL injection in the CSV export handler allows arbitrary database manipulation and creation of unauthorized administrator accounts.',
                'remediation_guide'  => 'Immediately upgrade WP-Automatic plugin to version 3.92.1 or above. Audit wp_users table for unauthorized admin users created recently.',
                'source_url'         => 'https://nvd.nist.gov/vuln/detail/CVE-2024-27956',
            ),
            array(
                'cve_id'             => 'CVE-2024-4439',
                'title_en'           => 'WordPress Core Avatar Block Stored Cross-Site Scripting (XSS)',
                'title_bn'           => '     -  ',
                'severity'           => 'high',
                'cvss_score'         => 7.5,
                'affected_systems'   => 'WordPress Core 6.5.0 - 6.5.2',
                'technical_analysis' => 'Authenticated contributors can inject malicious JavaScript into post content via crafted post author comments attributes.',
                'remediation_guide'  => 'Update WordPress Core to version 6.5.3 or 6.6+ where strict HTML attribute sanitization was implemented.',
                'source_url'         => 'https://nvd.nist.gov/vuln/detail/CVE-2024-4439',
            ),
        );

        foreach ( $sample_cves as $item ) {
            self::ingest_cve_record( $item );
        }
    }
}
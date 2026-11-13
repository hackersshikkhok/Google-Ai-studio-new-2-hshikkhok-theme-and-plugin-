<?php
/**
 * HackersShikkhok — AI Remediation & Executive Patching Engine
 *
 * Generates tailored Nginx, Apache, PHP, and Cloudflare WAF code patches,
 * plus business impact summaries translated for C-level executives.
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

final class AiRemediationEngine {

    public static function rest_get_patch( WP_REST_Request $request ): WP_REST_Response {
        $finding_id = absint( $request->get_param( 'id' ) );
        global $wpdb;
        $table = $wpdb->prefix . 'hs_security_findings';
        $finding = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $finding_id ), ARRAY_A );

        if ( ! $finding ) {
            return new WP_REST_Response( array( 'success' => false, 'error' => 'Finding not found' ), 404 );
        }

        $patch_package = self::generate_patch_package( $finding );
        return new WP_REST_Response( array(
            'success'       => true,
            'finding'       => $finding,
            'patch_package' => $patch_package,
        ), 200 );
    }

    public static function generate_patch_package( array $finding ): array {
        $key = $finding['finding_key'] ?? '';

        $patches = array(
            'nginx'       => "# Nginx Server Block Remediation\n# Add inside server { ... } block:\n",
            'apache'      => "# Apache .htaccess Remediation\n<IfModule mod_headers.c>\n",
            'php'         => "// PHP Native Mitigation (functions.php or bootstrap)\n",
            'cloudflare'  => "// Cloudflare WAF / Transform Rule Expression\n",
            'executive'   => "Executive Briefing: Immediate resolution recommended to preserve brand compliance.",
            'business_loss_est' => "Estimated financial risk: BDT 50,000 - 500,000 in potential data breach liabilities.",
        );

        switch ( $key ) {
            case 'missing_hsts':
                $patches['nginx'] .= "add_header Strict-Transport-Security \"max-age=31536000; includeSubDomains; preload\" always;\n";
                $patches['apache'] .= "  Header always set Strict-Transport-Security \"max-age=31536000; includeSubDomains; preload\"\n</IfModule>";
                $patches['php'] .= "header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload' );";
                $patches['cloudflare'] .= "SSL/TLS -> Edge Certificates -> Enable 'HTTP Strict Transport Security (HSTS)'";
                $patches['executive'] = "HSTS enforces end-to-end encryption. Prevents man-in-the-middle packet sniffing on public networks.";
                $patches['business_loss_est'] = "Low-Medium liability: Prevents corporate staff session eavesdropping.";
                break;

            case 'missing_csp':
                $patches['nginx'] .= "add_header Content-Security-Policy \"default-src 'self'; script-src 'self' 'unsafe-inline' https:; style-src 'self' 'unsafe-inline' https:; object-src 'none';\" always;\n";
                $patches['apache'] .= "  Header always set Content-Security-Policy \"default-src 'self'; object-src 'none';\"\n</IfModule>";
                $patches['php'] .= "header( \"Content-Security-Policy: default-src 'self'; object-src 'none';\" );";
                $patches['cloudflare'] .= "Rules -> Transform Rules -> Modify Response Header -> set 'Content-Security-Policy'";
                $patches['executive'] = "Content Security Policy shields users from hostile cross-site script injections and token theft.";
                $patches['business_loss_est'] = "High liability: Critical protection against e-commerce checkout form tampering.";
                break;

            case 'exposed_dot_env':
            case 'exposed_git_repo':
                $patches['nginx'] .= "location ~ /\\.(?!well-known).* {\n    deny all;\n    access_log off;\n    log_not_found off;\n}\n";
                $patches['apache'] .= "  RewriteEngine On\n  RewriteRule ^\\.(.*)$ - [F,L]\n</IfModule>";
                $patches['php'] .= "// Enforce server-level file protection before request hits PHP runtime.";
                $patches['cloudflare'] .= "Security -> WAF -> Custom Rule: (http.request.uri.path contains \"/.env\") -> Block";
                $patches['executive'] = "CRITICAL: Database connection strings exposed. Attacker can dump customer data immediately.";
                $patches['business_loss_est'] = "Severe liability: BDT 500,000+ GDPR / Cyber Security Act regulatory penalty risk.";
                break;

            default:
                $patches['nginx'] .= $finding['code_snippet'] ?? '# Custom patch';
                $patches['apache'] .= "# Insert custom Apache directives\n</IfModule>";
                $patches['php'] .= "// Custom PHP hook applied.";
                $patches['cloudflare'] .= "// Apply Cloudflare managed rule.";
                $patches['executive'] = $finding['impact_en'] ?? 'Mitigate identified security finding.';
                $patches['business_loss_est'] = "Standard technical risk remediation.";
                break;
        }

        return $patches;
    }
}
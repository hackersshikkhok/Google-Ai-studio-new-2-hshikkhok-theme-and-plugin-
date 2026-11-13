<?php
/**
 * HackersShikkhok — Enterprise Cyber Command & Security Audit SaaS Engine
 *
 * 14-Module Enterprise Cyber Defense, Vulnerability Lifecycle Management,
 * Passive Web Auditor, AI Remediation, & B2B Client Portal.
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
use WP_REST_Server;

final class SecuritySaaSEngine {

    public const VERSION = '2.0.0';
    public const REWRITE_SLUG = 'cyber-command';

    public static function register(): void {
        add_action( 'init', array( self::class, 'register_rewrites' ) );
        add_filter( 'query_vars', array( self::class, 'register_query_vars' ) );
        add_action( 'template_include', array( self::class, 'handle_template_routing' ) );
        add_action( 'rest_api_init', array( self::class, 'register_rest_routes' ) );

        // Background Cron worker (scheduled by Core Plugin CronManager)
        add_action( 'hs_csm_scheduled_scan_worker', array( ScanSchedulerQueue::class, 'process_due_scans' ) );

        // AJAX handlers for Client Portal interactions
        add_action( 'wp_ajax_hs_security_dispatch_scan', array( self::class, 'ajax_dispatch_scan' ) );
        add_action( 'wp_ajax_nopriv_hs_security_dispatch_scan', array( self::class, 'ajax_dispatch_scan' ) );

        add_action( 'wp_ajax_hs_security_verify_target', array( self::class, 'ajax_verify_target' ) );
        add_action( 'wp_ajax_hs_security_submit_redteam', array( self::class, 'ajax_submit_redteam' ) );
        add_action( 'wp_ajax_hs_security_remediate_finding', array( self::class, 'ajax_remediate_finding' ) );
        add_action( 'wp_ajax_hs_security_test_webhook', array( self::class, 'ajax_test_webhook' ) );
        add_action( 'wp_ajax_hs_security_sign_roe', array( self::class, 'ajax_sign_roe' ) );
    }

    public static function register_rewrites(): void {
        add_rewrite_rule(
            '^' . self::REWRITE_SLUG . '/?$',
            'index.php?hs_cyber_command=1',
            'top'
        );
        add_rewrite_rule(
            '^' . self::REWRITE_SLUG . '/target/([^/]+)/?$',
            'index.php?hs_cyber_command=1&hs_target_id=$matches[1]',
            'top'
        );
        add_rewrite_rule(
            '^' . self::REWRITE_SLUG . '/report/([^/]+)/?$',
            'index.php?hs_cyber_command=1&hs_report_id=$matches[1]',
            'top'
        );
    }

    public static function register_query_vars( array $vars ): array {
        $vars[] = 'hs_cyber_command';
        $vars[] = 'hs_target_id';
        $vars[] = 'hs_report_id';
        return $vars;
    }

    public static function handle_template_routing( string $template ): string {
        if ( get_query_var( 'hs_cyber_command' ) ) {
            $theme_template = locate_template( array( 'page-cyber-command.php' ) );
            if ( $theme_template ) {
                return $theme_template;
            }
            // Fallback to plugin view if theme template isn't present
            $plugin_template = HS_THEME_DIR . '/inc/modules/security/views/cyber-command-stage.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }
        return $template;
    }

    public static function register_rest_routes(): void {
        $namespace = 'hackersshikkhok/v1';

        // 1. Target Scope Management
        register_rest_route( $namespace, '/security/targets', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( TargetManager::class, 'rest_get_targets' ),
                'permission_callback' => array( self::class, 'check_auth' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( TargetManager::class, 'rest_add_target' ),
                'permission_callback' => array( self::class, 'check_auth' ),
            ),
        ) );

        // 2. Ownership Verification
        register_rest_route( $namespace, '/security/targets/(?P<id>\d+)/verify', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( TargetManager::class, 'rest_verify_target' ),
            'permission_callback' => array( self::class, 'check_auth' ),
        ) );

        // 3. Scan Dispatcher & Progress Polling
        register_rest_route( $namespace, '/security/scans', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( self::class, 'rest_start_scan' ),
                'permission_callback' => '__return_true', // Supports authenticated users & instant demo audit
            ),
        ) );

        register_rest_route( $namespace, '/security/scans/(?P<uuid>[a-zA-Z0-9_\-]+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( self::class, 'rest_get_scan_status' ),
            'permission_callback' => '__return_true',
        ) );

        // 4. Vulnerability Findings & Remediation Patches
        register_rest_route( $namespace, '/security/findings/(?P<id>\d+)/patch', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( AiRemediationEngine::class, 'rest_get_patch' ),
            'permission_callback' => '__return_true',
        ) );

        // 5. Red Team Engagement Submission
        register_rest_route( $namespace, '/security/redteam/request', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( RedTeamEngagementManager::class, 'rest_submit_engagement' ),
            'permission_callback' => array( self::class, 'check_auth' ),
        ) );

        // 6. Subscriptions & B2B Packages
        register_rest_route( $namespace, '/security/subscription', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( SubscriptionAndBillingManager::class, 'rest_get_subscription' ),
            'permission_callback' => array( self::class, 'check_auth' ),
        ) );

        // 7. Compliance Checklist
        register_rest_route( $namespace, '/security/compliance/(?P<target_id>\d+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( ComplianceChecklistEngine::class, 'rest_get_compliance_matrix' ),
            'permission_callback' => '__return_true',
        ) );

        // 8. Security Webhooks Dispatcher
        register_rest_route( $namespace, '/security/webhooks', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( SecurityWebhooksDispatcher::class, 'rest_get_config' ),
                'permission_callback' => array( self::class, 'check_auth' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( SecurityWebhooksDispatcher::class, 'rest_save_config' ),
                'permission_callback' => array( self::class, 'check_auth' ),
            ),
        ) );
        register_rest_route( $namespace, '/security/webhooks/test', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( SecurityWebhooksDispatcher::class, 'rest_test_webhook' ),
            'permission_callback' => array( self::class, 'check_auth' ),
        ) );

        // 9. Rules of Engagement & Bilateral Legal Charter
        register_rest_route( $namespace, '/security/roe', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( RulesOfEngagementManager::class, 'rest_get_roe' ),
                'permission_callback' => '__return_true',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( RulesOfEngagementManager::class, 'rest_sign_roe' ),
                'permission_callback' => array( self::class, 'check_auth' ),
            ),
        ) );
    }

    public static function check_auth(): bool {
        return is_user_logged_in();
    }

    public static function rest_start_scan( WP_REST_Request $request ): WP_REST_Response {
        $target_url = esc_url_raw( (string) $request->get_param( 'target_url' ) );
        $mode       = sanitize_key( (string) $request->get_param( 'mode' ) ?: 'passive_full' );
        $user_id    = get_current_user_id();

        if ( empty( $target_url ) ) {
            return new WP_REST_Response( array(
                'success' => false,
                'error'   => 'A valid target domain or URL is required.',
            ), 400 );
        }

        $result = PassiveAuditorEngine::execute_audit( $target_url, $mode, $user_id );
        return new WP_REST_Response( array(
            'success' => true,
            'data'    => $result,
        ), 200 );
    }

    public static function rest_get_scan_status( WP_REST_Request $request ): WP_REST_Response {
        $uuid = sanitize_text_field( (string) $request->get_param( 'uuid' ) );
        global $wpdb;
        $table = $wpdb->prefix . 'hs_security_scans';
        $scan  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE scan_uuid = %s", $uuid ), ARRAY_A );

        if ( ! $scan ) {
            return new WP_REST_Response( array( 'success' => false, 'error' => 'Scan not found' ), 404 );
        }

        $findings_table = $wpdb->prefix . 'hs_security_findings';
        $findings = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$findings_table} WHERE scan_id = %d ORDER BY cvss_score DESC",
            $scan['id']
        ), ARRAY_A );

        return new WP_REST_Response( array(
            'success'  => true,
            'scan'     => $scan,
            'findings' => $findings,
        ), 200 );
    }

    public static function ajax_dispatch_scan(): void {
        check_ajax_referer( 'hs_cyber_command_nonce', 'nonce' );
        $target_url = isset( $_POST['target_url'] ) ? esc_url_raw( wp_unslash( $_POST['target_url'] ) ) : '';
        $mode       = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : 'passive_full';
        $user_id    = get_current_user_id();

        if ( empty( $target_url ) ) {
            wp_send_json_error( array( 'message' => '     ।' ) );
        }

        $result = PassiveAuditorEngine::execute_audit( $target_url, $mode, $user_id );
        wp_send_json_success( $result );
    }

    public static function ajax_verify_target(): void {
        check_ajax_referer( 'hs_cyber_command_nonce', 'nonce' );
        $target_id = isset( $_POST['target_id'] ) ? absint( $_POST['target_id'] ) : 0;
        $verified  = TargetManager::verify_target_ownership( $target_id );

        if ( $verified ) {
            wp_send_json_success( array( 'message' => '       ।' ) );
        } else {
            wp_send_json_error( array( 'message' => '   । DNS TXT      ।' ) );
        }
    }

    public static function ajax_submit_redteam(): void {
        check_ajax_referer( 'hs_cyber_command_nonce', 'nonce' );
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            wp_send_json_error( array( 'message' => '    ।' ) );
        }

        $company = isset( $_POST['company_name'] ) ? sanitize_text_field( wp_unslash( $_POST['company_name'] ) ) : '';
        $email   = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
        $scope   = isset( $_POST['scope_details'] ) ? sanitize_textarea_field( wp_unslash( $_POST['scope_details'] ) ) : '';

        $res = RedTeamEngagementManager::create_request( $user_id, $company, $email, $scope );
        if ( $res['success'] ) {
            wp_send_json_success( $res );
        } else {
            wp_send_json_error( $res );
        }
    }

    public static function ajax_remediate_finding(): void {
        check_ajax_referer( 'hs_cyber_command_nonce', 'nonce' );
        $finding_id = isset( $_POST['finding_id'] ) ? absint( $_POST['finding_id'] ) : 0;
        $status     = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : 'remediated';

        $updated = FindingsLedger::update_status( $finding_id, $status );
        if ( $updated ) {
            wp_send_json_success( array( 'message' => '     ।' ) );
        } else {
            wp_send_json_error( array( 'message' => '   ।' ) );
        }
    }

    public static function ajax_test_webhook(): void {
        check_ajax_referer( 'hs_cyber_command_nonce', 'nonce' );
        $user_id     = get_current_user_id() ?: 1;
        $webhook_url = isset( $_POST['webhook_url'] ) ? esc_url_raw( wp_unslash( $_POST['webhook_url'] ) ) : '';
        $platform    = isset( $_POST['platform'] ) ? sanitize_key( wp_unslash( $_POST['platform'] ) ) : 'slack';

        if ( ! empty( $webhook_url ) ) {
            SecurityWebhooksDispatcher::save_config( $user_id, array(
                'webhook_url' => $webhook_url,
                'platform'    => $platform,
                'enabled'     => true,
            ) );
        }

        $result = SecurityWebhooksDispatcher::dispatch_event( $user_id, 'test_alert', array(
            'target_url'   => 'https://hackersshikkhok.com',
            'health_score' => 88,
            'summary'      => 'Test dispatch verification from HackersShikkhok Cyber Command Webhook Dispatcher.',
        ) );

        if ( $result['success'] ) {
            wp_send_json_success( array( 'message' => '     !', 'result' => $result ) );
        } else {
            wp_send_json_error( array( 'message' => '  : ' . ( $result['error'] ?? 'Unknown' ), 'result' => $result ) );
        }
    }

    public static function ajax_sign_roe(): void {
        check_ajax_referer( 'hs_cyber_command_nonce', 'nonce' );
        $user_id = get_current_user_id() ?: 1;

        $org_name = isset( $_POST['organization_name'] ) ? sanitize_text_field( wp_unslash( $_POST['organization_name'] ) ) : '';
        $signer   = isset( $_POST['authorized_signer'] ) ? sanitize_text_field( wp_unslash( $_POST['authorized_signer'] ) ) : '';
        $title    = isset( $_POST['signer_title'] ) ? sanitize_text_field( wp_unslash( $_POST['signer_title'] ) ) : '';
        $scope    = isset( $_POST['target_scope'] ) ? sanitize_textarea_field( wp_unslash( $_POST['target_scope'] ) ) : '';

        $result = RulesOfEngagementManager::sign_agreement( $user_id, array(
            'organization_name' => $org_name,
            'authorized_signer' => $signer,
            'signer_title'      => $title,
            'target_scope'      => $scope,
        ) );

        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }
}
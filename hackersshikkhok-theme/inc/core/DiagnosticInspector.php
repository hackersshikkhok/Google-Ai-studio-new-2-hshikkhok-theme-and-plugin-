<?php
declare(strict_types=1);

namespace HackersShikkhok\Theme\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WP_REST_Request;
use WP_REST_Response;

/**
 * Diagnostic Inspector & Error Logger for HackersShikkhok Core
 * Captures, monitors, and exposes subsystem boot errors, database integrity, and health diagnostics.
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */
final class DiagnosticInspector {

    private const OPTION_ERRORS = 'hs_core_recent_system_errors';
    private const MAX_ERRORS    = 50;

    /**
     * Register diagnostics hooks and REST routes
     */
    public static function register(): void {
        add_action( 'rest_api_init', array( self::class, 'register_rest_routes' ) );
        add_action( 'admin_notices', array( self::class, 'render_admin_diagnostic_notice' ) );
    }

    /**
     * Register REST API routes for automated system health audits
     */
    public static function register_rest_routes(): void {
        register_rest_route( 'hackersshikkhok/v1', '/system/diagnostics', array(
            'methods'             => 'GET',
            'permission_callback' => array( self::class, 'check_admin_permissions' ),
            'callback'            => array( self::class, 'rest_get_diagnostics' ),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/system/errors', array(
            'methods'             => 'GET',
            'permission_callback' => array( self::class, 'check_admin_permissions' ),
            'callback'            => array( self::class, 'rest_get_errors' ),
        ) );
    }

    public static function check_admin_permissions(): bool {
        return current_user_can( 'manage_options' ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG );
    }

    /**
     * REST endpoint returning system diagnostics
     */
    public static function rest_get_diagnostics( WP_REST_Request $request ): WP_REST_Response {
        $health = class_exists( SystemHealth::class ) ? SystemHealth::run_diagnostics() : array();
        return new WP_REST_Response( array(
            'success'   => true,
            'timestamp' => gmdate( 'c' ),
            'version'   => HS_THEME_VERSION,
            'health'    => $health,
            'errors'    => self::get_recent_errors(),
        ), 200 );
    }

    /**
     * REST endpoint returning captured subsystem errors
     */
    public static function rest_get_errors( WP_REST_Request $request ): WP_REST_Response {
        return new WP_REST_Response( array(
            'success' => true,
            'errors'  => self::get_recent_errors(),
        ), 200 );
    }

    /**
     * Log a subsystem boot or runtime error safely
     */
    public static function log_system_error( string $component, \Throwable $e ): void {
        error_log( sprintf(
            '[HackersShikkhok] [%s] %s in %s on line %d',
            $component,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ) );

        $entry = array(
            'component' => sanitize_text_field( $component ),
            'message'   => sanitize_text_field( $e->getMessage() ),
            'file'      => sanitize_text_field( wp_basename( $e->getFile() ) ),
            'line'      => (int) $e->getLine(),
            'timestamp' => time(),
            'datetime'  => gmdate( 'Y-m-d H:i:s' ),
        );

        $errors = get_option( self::OPTION_ERRORS, array() );
        if ( ! is_array( $errors ) ) {
            $errors = array();
        }

        array_unshift( $errors, $entry );
        if ( count( $errors ) > self::MAX_ERRORS ) {
            $errors = array_slice( $errors, 0, self::MAX_ERRORS );
        }

        update_option( self::OPTION_ERRORS, $errors, false );
    }

    /**
     * Retrieve recent captured subsystem errors
     */
    public static function get_recent_errors(): array {
        $errors = get_option( self::OPTION_ERRORS, array() );
        return is_array( $errors ) ? $errors : array();
    }

    /**
     * Clear all logged system errors
     */
    public static function clear_errors(): void {
        delete_option( self::OPTION_ERRORS );
    }

    /**
     * Render non-obtrusive admin notice if system has unresolved issues
     */
    public static function render_admin_diagnostic_notice(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $errors = self::get_recent_errors();
        if ( empty( $errors ) ) {
            return;
        }

        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        if ( ! $screen || false === strpos( $screen->id, 'hackersshikkhok' ) ) {
            return;
        }

        $latest = $errors[0];
        ?>
        <div class="notice notice-warning is-dismissible" style="border-left-color: #f59e0b; background: #0b1120; color: #f8fafc;">
            <p>
                <strong style="color: #f59e0b;">🛡️ HackersShikkhok Diagnostic Notice:</strong>
                Subsystem <code><?php echo esc_html( $latest['component'] ); ?></code> logged an event:
                <em><?php echo esc_html( $latest['message'] ); ?></em> (<?php echo esc_html( $latest['datetime'] ); ?>).
            </p>
        </div>
        <?php
    }
}
<?php
/**
 * Native Branded Authentication Portal & WP-Admin Role Controller
 * Package: HackersShikkhok Core Plugin
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * 
 * Strict Architecture:
 * 1. Anyone can access /wp-admin & /wp-login.php without 404 or redirect loops.
 * 2. Unauthenticated visitors see the branded Cyberpunk WordPress Login Gateway.
 * 3. Super Admins login -> Redirect to full WP-Admin Dashboard (/wp-admin/).
 * 4. Regular Users (Contributors, Authors, Students) login -> Redirect directly to Home Page (hackersshikkhok.com).
 * 5. If a non-admin enters /wp-admin, sensitive system settings are hidden and only their posts, drafts & profile are displayed.
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Platform;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class NativeBrandedAuthEngine {

    public static function register(): void {
        // Enforce role-based login redirection
        add_filter( 'login_redirect', array( self::class, 'handle_login_redirect' ), 10, 3 );

        // Style the WordPress login screen (/wp-login.php & /wp-admin)
        add_action( 'login_enqueue_scripts', array( self::class, 'enqueue_cyber_login_styles' ) );
        add_filter( 'login_headerurl', array( self::class, 'filter_login_header_url' ) );
        add_filter( 'login_headertext', array( self::class, 'filter_login_header_title' ) );

        // Restrict /wp-admin menu items for non-administrator users
        add_action( 'admin_menu', array( self::class, 'enforce_admin_menu_permissions' ), 999 );

        // Register secure REST API auth routes for in-page popups
        add_action( 'rest_api_init', array( self::class, 'register_auth_endpoints' ) );

        // Fallback safety check for /wp-admin URL access
        add_action( 'init', array( self::class, 'handle_wp_admin_fallback' ), 1 );
    }

    /**
     * Prevent 404 on /wp-admin on custom rewrites or non-standard hosts
     */
    public static function handle_wp_admin_fallback(): void {
        $uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
        $path = trim( (string) parse_url( $uri, PHP_URL_PATH ), '/' );

        if ( $path === 'wp-admin' ) {
            if ( ! is_user_logged_in() ) {
                wp_safe_redirect( wp_login_url( admin_url() ) );
                exit;
            } elseif ( ! current_user_can( 'manage_options' ) ) {
                // Non-admin logged in user visiting /wp-admin -> can stay or redirect to dashboard
                // Allow them into admin with stripped menus
            }
        }
    }

    /**
     * Role-Based Redirection:
     * - Admins -> WP-Admin Dashboard
     * - Regular Users / Students -> Home Page (hackersshikkhok.com)
     */
    public static function handle_login_redirect( string $redirect_to, string $requested_redirect_to, $user ): string {
        if ( is_wp_error( $user ) || ! is_a( $user, 'WP_User' ) ) {
            return $redirect_to;
        }

        if ( user_can( $user, 'manage_options' ) ) {
            return ! empty( $requested_redirect_to ) ? $requested_redirect_to : admin_url();
        }

        // Standard users, contributors and students redirect to Home Page
        return home_url( '/' );
    }

    public static function filter_login_header_url(): string {
        return home_url( '/' );
    }

    public static function filter_login_header_title(): string {
        return 'HackersShikkhok (hackersshikkhok.com) — Cyber Defense & Coding Ecosystem';
    }

    /**
     * Enqueue Cyberpunk Dark Theme for /wp-login.php & /wp-admin gateway
     */
    public static function enqueue_cyber_login_styles(): void {
        ?>
        <style type="text/css">
            body.login {
                background: #050811 !important;
                color: #e2e8f0 !important;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif !important;
                position: relative;
            }
            body.login::before {
                content: "";
                position: fixed;
                top: 0; left: 0; right: 0; height: 3px;
                background: linear-gradient(90deg, #00f5d4, #7c3aed, #ef4444);
                z-index: 9999;
            }
            #login {
                padding: 40px 0 20px !important;
                width: 380px !important;
            }
            #login h1 a {
                background-image: none !important;
                text-indent: 0 !important;
                width: 100% !important;
                height: auto !important;
                font-size: 22px !important;
                font-weight: 900 !important;
                color: #ffffff !important;
                text-align: center !important;
                text-decoration: none !important;
                letter-spacing: -0.5px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 8px !important;
            }
            #login h1 a::before {
                content: "🛡️";
                display: inline-block;
                font-size: 26px;
            }
            .login form {
                background: #0b1120 !important;
                border: 1px solid rgba(0, 245, 212, 0.3) !important;
                border-radius: 18px !important;
                box-shadow: 0 0 35px rgba(0, 245, 212, 0.12) !important;
                padding: 28px 24px !important;
            }
            .login form label {
                color: #94a3b8 !important;
                font-size: 12px !important;
                font-weight: 700 !important;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .login form .input {
                background: #050811 !important;
                border: 1px solid rgba(255, 255, 255, 0.12) !important;
                color: #ffffff !important;
                border-radius: 10px !important;
                padding: 12px 14px !important;
                font-size: 14px !important;
                box-shadow: none !important;
                transition: border-color 0.2s;
            }
            .login form .input:focus {
                border-color: #00f5d4 !important;
                box-shadow: 0 0 12px rgba(0, 245, 212, 0.35) !important;
                outline: none !important;
            }
            .login .button-primary {
                background: linear-gradient(135deg, #00f5d4, #10b981) !important;
                border: none !important;
                color: #050811 !important;
                font-weight: 900 !important;
                font-size: 14px !important;
                border-radius: 10px !important;
                padding: 12px 20px !important;
                text-shadow: none !important;
                box-shadow: 0 0 20px rgba(0, 245, 212, 0.4) !important;
                cursor: pointer !important;
                transition: opacity 0.2s, transform 0.1s !important;
                width: 100% !important;
                margin-top: 16px !important;
            }
            .login .button-primary:hover {
                opacity: 0.95 !important;
                transform: translateY(-1px);
            }
            .login #nav a, .login #backtoblog a {
                color: #00f5d4 !important;
                font-size: 12px !important;
                font-weight: 600 !important;
                text-decoration: none !important;
            }
            .login #nav a:hover, .login #backtoblog a:hover {
                text-decoration: underline !important;
            }
            .login .message, .login #login_error {
                background: #0b1120 !important;
                border-left-color: #00f5d4 !important;
                color: #cbd5e1 !important;
                border-radius: 8px !important;
                border: 1px solid rgba(0, 245, 212, 0.3) !important;
                border-left-width: 4px !important;
            }
            .login #login_error {
                border-left-color: #ef4444 !important;
            }
        </style>
        <?php
    }

    /**
     * Enforce role-based menu isolation in /wp-admin:
     * Non-administrators can only see their Posts, Comments, and Profile.
     * Core settings, AdSense, Sitemaps, Plugins, and Themes are restricted to Admins.
     */
    public static function enforce_admin_menu_permissions(): void {
        if ( current_user_can( 'manage_options' ) ) {
            return; // Super admins see all tools and settings
        }

        // For non-admin logged-in users (Contributors/Authors)
        remove_menu_page( 'plugins.php' );
        remove_menu_page( 'themes.php' );
        remove_menu_page( 'options-general.php' );
        remove_menu_page( 'tools.php' );
        remove_menu_page( 'hs-control-center' );
        remove_menu_page( 'hs-search-console-sitemaps' );
        remove_menu_page( 'hs-adsense-automation' );
        remove_menu_page( 'hs-instant-indexing' );
    }

    /**
     * Register AJAX REST endpoints for instant in-page authentication and registration
     */
    public static function register_auth_endpoints(): void {
        register_rest_route( 'hackersshikkhok/v1', '/auth/login', array(
            'methods'             => 'POST',
            'permission_callback' => '__return_true',
            'callback'            => array( self::class, 'rest_handle_login' ),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/auth/register', array(
            'methods'             => 'POST',
            'permission_callback' => '__return_true',
            'callback'            => array( self::class, 'rest_handle_register' ),
        ) );
    }

    public static function rest_handle_login( \WP_REST_Request $request ): \WP_REST_Response {
        $creds = array(
            'user_login'    => sanitize_user( (string) $request->get_param( 'username' ) ),
            'user_password' => (string) $request->get_param( 'password' ),
            'remember'      => (bool) $request->get_param( 'remember' ),
        );

        $user = wp_signon( $creds, is_ssl() );

        if ( is_wp_error( $user ) ) {
            return new \WP_REST_Response( array(
                'success' => false,
                'message' => $user->get_error_message(),
            ), 400 );
        }

        $wallet = class_exists( '\HackersShikkhok\Theme\Modules\LMS\Wallet_Economy' )
            ? \HackersShikkhok\Theme\Modules\LMS\Wallet_Economy::get_wallet( $user->ID )
            : array( 'balance_bdt' => 50.0, 'xp_points' => 200 );

        // If admin -> WP-Admin; If regular user -> Home Page
        $redirect_target = user_can( $user, 'manage_options' ) ? admin_url() : home_url( '/' );

        return new \WP_REST_Response( array(
            'success'      => true,
            'message'      => 'Authentication successful. Welcome operative!',
            'user_id'      => $user->ID,
            'display_name' => $user->display_name ?: $user->user_login,
            'redirect_url' => $redirect_target,
            'wallet'       => $wallet,
        ), 200 );
    }

    public static function rest_handle_register( \WP_REST_Request $request ): \WP_REST_Response {
        $username = sanitize_user( (string) $request->get_param( 'username' ) );
        $email    = sanitize_email( (string) $request->get_param( 'email' ) );
        $password = (string) $request->get_param( 'password' );

        if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
            return new \WP_REST_Response( array(
                'success' => false,
                'message' => 'Please provide username, valid email, and password.',
            ), 400 );
        }

        if ( username_exists( $username ) ) {
            return new \WP_REST_Response( array(
                'success' => false,
                'message' => 'This operative username is already registered. Please choose another.',
            ), 400 );
        }

        if ( email_exists( $email ) ) {
            return new \WP_REST_Response( array(
                'success' => false,
                'message' => 'This email is already registered. Please sign in.',
            ), 400 );
        }

        $user_id = wp_create_user( $username, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            return new \WP_REST_Response( array(
                'success' => false,
                'message' => $user_id->get_error_message(),
            ), 400 );
        }

        // Set role to contributor
        $new_user = get_user_by( 'id', $user_id );
        if ( $new_user ) {
            $new_user->set_role( 'contributor' );
        }

        // Auto sign on new operative
        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id, true );

        // Seed initial wallet balance (BDT 50.00 & 200 XP welcome bonus)
        if ( class_exists( '\HackersShikkhok\Theme\Modules\LMS\Wallet_Economy' ) ) {
            \HackersShikkhok\Theme\Modules\LMS\Wallet_Economy::get_wallet( $user_id );
        }

        return new \WP_REST_Response( array(
            'success'      => true,
            'message'      => 'Account created successfully! Welcome bonus credited.',
            'user_id'      => $user_id,
            'redirect_url' => home_url( '/' ), // Redirect to home page!
        ), 200 );
    }
}

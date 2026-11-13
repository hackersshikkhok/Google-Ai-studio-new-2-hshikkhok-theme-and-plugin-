<?php
/**
 * Conditional & Performance-Optimized Asset Enqueue Engine.
 * Loads CSS and JS strictly on templates/pages where each module is active.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_enqueue_scripts', static function (): void {
    $conditional_only = (bool) hs_get_setting( 'conditional_assets_only', true );
    $defer_js         = (bool) hs_get_setting( 'defer_non_critical_js', true );

    // Base Typography & Core Theme Styles
    wp_enqueue_style(
        'hs-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap',
        array(),
        null
    );
    wp_enqueue_style( 'hs-theme-style', get_stylesheet_uri(), array( 'hs-google-fonts' ), HS_THEME_VERSION );
    wp_enqueue_style( 'hs-theme-layout', HS_THEME_URI . '/assets/css/theme.css', array( 'hs-theme-style' ), HS_THEME_VERSION );

    // Inject dynamic theme palette overrides from Unified Admin Settings
    $primary   = sanitize_hex_color( (string) hs_get_setting( 'primary_color', '#00f5d4' ) ) ?: '#00f5d4';
    $secondary = sanitize_hex_color( (string) hs_get_setting( 'secondary_color', '#7928ca' ) ) ?: '#7928ca';
    $accent    = sanitize_hex_color( (string) hs_get_setting( 'accent_color', '#00bbf9' ) ) ?: '#00bbf9';

    wp_add_inline_style(
        'hs-theme-layout',
        ":root{--hs-neon:{$primary};--hs-secondary:{$secondary};--hs-neon-blue:{$accent};}"
    );

    // Core Interactive Scripts (footer-loaded)
    wp_enqueue_script( 'hs-theme-script', HS_THEME_URI . '/assets/js/theme.js', array(), HS_THEME_VERSION, true );
    wp_enqueue_script( 'hs-ui-script', HS_THEME_URI . '/assets/js/hs-ui.js', array(), HS_THEME_VERSION, true );
    wp_enqueue_script( 'hs-theming-engine', HS_THEME_URI . '/assets/js/theming-engine.js', array(), HS_THEME_VERSION, true );
    wp_enqueue_script( 'hs-i18n-engine', HS_THEME_URI . '/assets/js/i18n-engine.js', array(), HS_THEME_VERSION, true );

    // Conditional LMS Styles & Terminal Engine
    $is_lms_context = is_page_template( array( 'page-academy.php', 'page-dashboard.php', 'page-cyber-command.php', 'page-desktop.php' ) )
        || is_page( array( 'academy', 'dashboard', 'cyber-command', 'desktop' ) )
        || is_singular( array( 'hs_course', 'hs_cyber_lab', 'hs_elite_challenge' ) )
        || is_post_type_archive( 'hs_course' );

    if ( ! $conditional_only || $is_lms_context ) {
        wp_enqueue_style( 'hs-cyber-lms', HS_THEME_URI . '/assets/css/cyber-lms.css', array( 'hs-theme-layout' ), HS_THEME_VERSION );
        wp_enqueue_script( 'hs-terminal-engine', HS_THEME_URI . '/assets/js/terminal-engine.js', array(), HS_THEME_VERSION, true );
    }

    // Conditional Cyber Command Assets
    $is_command_context = is_page_template( 'page-cyber-command.php' ) || is_page( 'cyber-command' ) || (bool) get_query_var( 'hs_cyber_command' );
    if ( $is_command_context && hs_is_module_enabled( 'mod_security_saas' ) ) {
        wp_enqueue_style( 'hs-cyber-command', HS_THEME_URI . '/assets/css/cyber-command.css', array( 'hs-theme-layout' ), HS_THEME_VERSION );
        wp_enqueue_script( 'hs-cyber-command-js', HS_THEME_URI . '/assets/js/cyber-command.js', array(), HS_THEME_VERSION, true );
    }

    // Conditional Tools Engine Assets (2,024+ Tools & CSS/RGB Lab)
    $is_tools_context = is_page_template( array( 'page-tools-lab.php', 'page-platform-center.php', 'page-center.php' ) )
        || is_page( array( 'tools', 'tools-lab', 'platform-center', 'center' ) )
        || is_singular( 'tools' )
        || is_post_type_archive( 'tools' );

    if ( $is_tools_context && hs_is_module_enabled( 'mod_tools_engine' ) ) {
        wp_enqueue_style( 'hs-tools-engine', HS_THEME_URI . '/assets/css/tools-engine.css', array( 'hs-theme-layout' ), HS_THEME_VERSION );
        wp_enqueue_script( 'hs-tools-engine', HS_THEME_URI . '/assets/js/tools-engine.js', array(), HS_THEME_VERSION, true );
    }

    // Conditional Code Library & Editor Assets
    $is_code_context = is_singular( 'code' )
        || is_post_type_archive( 'code' )
        || is_page_template( array( 'page-tools-lab.php', 'page-app-maker.php', 'page-academy.php' ) )
        || is_page( array( 'tools-lab', 'app-maker', 'app-studio', 'academy' ) );

    if ( $is_code_context && hs_is_module_enabled( 'mod_code_library' ) ) {
        wp_enqueue_style( 'hs-code-library', HS_THEME_URI . '/assets/css/code-library.css', array( 'hs-theme-layout' ), HS_THEME_VERSION );
        wp_enqueue_script( 'hs-code-editor', HS_THEME_URI . '/assets/js/code-editor.js', array(), HS_THEME_VERSION, true );
    }

    // Conditional Live Demo Sandbox Assets
    $is_demo_context = is_singular( array( 'projects', 'code' ) )
        || is_page_template( array( 'page-app-maker.php', 'page-platform-center.php', 'page-tools-lab.php' ) )
        || is_page( array( 'app-maker', 'app-studio', 'platform-center', 'tools-lab' ) );

    if ( $is_demo_context && hs_is_module_enabled( 'mod_live_demo' ) ) {
        wp_enqueue_style( 'hs-live-demo', HS_THEME_URI . '/assets/css/live-demo.css', array( 'hs-theme-layout' ), HS_THEME_VERSION );
        wp_enqueue_script( 'hs-live-demo', HS_THEME_URI . '/assets/js/live-demo.js', array(), HS_THEME_VERSION, true );
    }

    // Unified Runtime Payload
    $payload = array(
        'ajax_url'          => admin_url( 'admin-ajax.php' ),
        'ajax_nonce'        => wp_create_nonce( 'hs_ajax_nonce' ),
        'rest_url'          => esc_url_raw( rest_url( 'hackersshikkhok/v1/' ) ),
        'rest_nonce'        => wp_create_nonce( 'wp_rest' ),
        'site_url'          => home_url( '/' ),
        'theme_url'         => HS_THEME_URI,
        'is_user_logged_in' => is_user_logged_in(),
        'current_user_id'   => get_current_user_id(),
        'official_brand'    => (string) hs_get_setting( 'brand_name', 'HackersShikkhok' ),
        'official_site_url' => 'https://hackersshikkhok.com',
    );

    wp_localize_script( 'hs-theme-script', 'hsThemeData', $payload );
    wp_localize_script( 'hs-i18n-engine', 'hsI18nData', $payload );
    if ( wp_script_is( 'hs-terminal-engine', 'enqueued' ) ) {
        wp_localize_script( 'hs-terminal-engine', 'hsTerminalData', $payload );
    }
    if ( wp_script_is( 'hs-cyber-command-js', 'enqueued' ) ) {
        wp_localize_script( 'hs-cyber-command-js', 'hsCommandData', $payload );
    }

    // Apply defer strategy for non-critical JS
    if ( $defer_js ) {
        $defer_handles = array( 'hs-ui-script', 'hs-theming-engine', 'hs-i18n-engine', 'hs-tools-engine', 'hs-code-editor', 'hs-live-demo' );
        foreach ( $defer_handles as $handle ) {
            if ( wp_script_is( $handle, 'enqueued' ) ) {
                wp_script_add_data( $handle, 'strategy', 'defer' );
            }
        }
    }
} );

// Load Admin Settings CSS strictly on HackersShikkhok admin screens
add_action( 'admin_enqueue_scripts', static function ( string $hook ): void {
    if ( str_contains( $hook, 'hs-' ) || str_contains( $hook, 'hackersshikkhok' ) ) {
        wp_enqueue_style( 'hs-admin-settings', HS_THEME_URI . '/assets/css/admin-settings.css', array(), HS_THEME_VERSION );
    }
} );

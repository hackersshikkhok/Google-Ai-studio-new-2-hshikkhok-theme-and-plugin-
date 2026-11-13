<?php
/**
 * Theme Setup, Menus, Sidebars, Core Web Vitals Tweaks & One-Time Provisioning.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Prevent zlib output compression conflict on shared hosts/cPanel
add_action( 'init', static function (): void {
    if ( ini_get( 'zlib.output_compression' ) || ( function_exists( 'ob_get_level' ) && ob_get_level() > 0 ) ) {
        remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
    }

    // Optional Core Web Vitals optimization: strip WP emoji scripts/styles
    if ( hs_get_setting( 'disable_wp_emojis', true ) ) {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
    }
}, 0 );

add_action( 'after_setup_theme', static function (): void {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

    register_nav_menus( array(
        'primary'       => __( 'Primary Cyber Header Menu', 'hackersshikkhok-theme' ),
        'mobile_drawer' => __( 'Mobile Drawer Navigation', 'hackersshikkhok-theme' ),
        'mobile_tabs'   => __( 'Bottom 5-Tab Navigation', 'hackersshikkhok-theme' ),
        'footer_legal'  => __( 'Footer Legal & Trust Menu', 'hackersshikkhok-theme' ),
    ) );
} );

add_action( 'widgets_init', static function (): void {
    register_sidebar( array(
        'name'          => __( 'Primary Cyber Sidebar', 'hackersshikkhok-theme' ),
        'id'            => 'cyber-primary-sidebar',
        'description'   => __( 'Appears on tutorials, single posts, and archives.', 'hackersshikkhok-theme' ),
        'before_widget' => '<div id="%1$s" class="hs-sidebar-widget hs-cyber-card %2$s" style="margin-bottom:24px;">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="hs-widget-title" style="font-size:1rem;font-weight:700;color:#00f5d4;margin-bottom:14px;border-bottom:1px solid rgba(0,245,212,0.2);padding-bottom:8px;">',
        'after_title'   => '</h3>',
    ) );
} );

/**
 * One-time theme provisioning: creates permanent pages & sample content ONLY on theme switch
 * (or manual sync from Admin Settings -> Performance & Maintenance), never on every request.
 */
function hs_run_one_time_theme_provisioning( bool $force = false ): void {
    if ( ! $force && get_option( 'hs_theme_provisioned_v5' ) ) {
        return;
    }

    if ( class_exists( '\\HackersShikkhok\\Theme\\Modules\\Platform\\EliteGlobalPlatformEngine' ) ) {
        \HackersShikkhok\Theme\Modules\Platform\EliteGlobalPlatformEngine::ensure_elite_footer_pages_exist();
        \HackersShikkhok\Theme\Modules\Platform\EliteGlobalPlatformEngine::ensure_sample_ecosystem_content_exists();
    }

    flush_rewrite_rules( false );
    update_option( 'hs_theme_provisioned_v5', gmdate( 'c' ) );
}

add_action( 'after_switch_theme', static function (): void {
    hs_run_one_time_theme_provisioning( true );
} );

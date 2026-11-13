<?php
/**
 * Centralized Theme & System Settings Helper.
 * Single cached option (`hs_theme_master_settings`) with automatic synchronization
 * to legacy option keys so all migrated modules obey the Unified Admin Settings Panel.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'HS_MASTER_SETTINGS_OPTION', 'hs_theme_master_settings' );

/**
 * Returns default configuration for every theme & system setting.
 */
function hs_get_default_settings(): array {
    return array(
        // Tab 1: General & Homepage
        'brand_name'                => 'HackersShikkhok',
        'brand_tagline'             => 'Cyber Defense Academy & 2,024+ Developer Tools',
        'color_preset'              => 'cyber_cyan',
        'primary_color'             => '#00f5d4',
        'secondary_color'           => '#7928ca',
        'accent_color'              => '#00bbf9',
        'enable_ai_voice_reader'    => 1,
        'enable_stories_bar'        => 1,
        'enable_notifications_bell' => 1,
        'featured_category'         => 'notice',
        'popular_weight_likes'      => 1.0,
        'popular_weight_comments'   => 1.5,
        'popular_weight_views'      => 0.5,
        'popular_posts_count'       => 6,
        'latest_posts_count'        => 8,

        // Tab 2: Modules & Features
        'mod_tools_engine'          => 1,
        'mod_css_rgb_lab'           => 1,
        'mod_live_demo'             => 1,
        'mod_code_library'          => 1,
        'mod_academy_lms'           => 1,
        'mod_ctf_engine'            => 1,
        'mod_wallet_economy'        => 1,
        'mod_post_pay_counter'      => 1,
        'mod_community_social'      => 1,
        'mod_security_saas'         => 1,
        'mod_device_games_lab'      => 1,
        'mod_platform_centers'      => 1,
        'mod_youtube'               => 1,
        'mod_github'                => 1,
        'mod_downloads'             => 1,
        'mod_user_submissions'      => 1,

        // Tab 3: AI & API Keys
        'emergency_kill_switch'     => 0,
        'enable_ai_autopilot'       => 1,
        'enable_ai_tutor'           => 1,
        'autopilot_publishing_mode' => 'draft_first',
        'autopilot_min_quality'     => 85,
        'autopilot_max_retries'     => 2,
        'gemini_api_key'            => '',
        'openai_api_key'            => '',
        'claude_api_key'            => '',
        'ai_daily_request_limit'    => 250,

        // Tab 4: SEO, Sitemaps & AdSense
        'enable_schema_jsonld'      => 1,
        'enable_og_meta'            => 1,
        'enable_xml_sitemap'        => 1,
        'enable_auto_linking'       => 1,
        'gsc_verification_token'    => '',
        'indexnow_api_key'          => '',
        'adsense_publisher_id'      => '',
        'enable_auto_ads_txt'       => 1,

        // Tab 5: Security, Auth & Limits
        'enable_content_lock'       => 1,
        'enable_video_shield'       => 1,
        'enable_branded_auth'       => 1,
        'enable_branded_email'      => 1,
        'enable_passkey_geo'        => 1,
        'rest_rate_limit_rpm'       => 120,

        // Tab 6: Performance & Cache
        'enable_transient_cache'    => 1,
        'cache_ttl_seconds'         => 600,
        'conditional_assets_only'   => 1,
        'defer_non_critical_js'     => 1,
        'disable_wp_emojis'         => 1,
    );
}

/**
 * Retrieve all merged settings with in-request static caching.
 */
function hs_get_all_settings( bool $force_refresh = false ): array {
    static $cached = null;
    if ( null !== $cached && ! $force_refresh ) {
        return $cached;
    }

    $defaults = hs_get_default_settings();
    $saved    = get_option( HS_MASTER_SETTINGS_OPTION, array() );
    if ( ! is_array( $saved ) ) {
        $saved = array();
    }

    // Inherit legacy individual options on first load if master option isn't saved yet
    if ( empty( $saved ) ) {
        $saved['featured_category']       = (string) get_option( 'hs_featured_category', $defaults['featured_category'] );
        $saved['popular_weight_likes']    = (float) get_option( 'hs_popular_weight_likes', $defaults['popular_weight_likes'] );
        $saved['popular_weight_comments'] = (float) get_option( 'hs_popular_weight_comments', $defaults['popular_weight_comments'] );
        $saved['popular_weight_views']    = (float) get_option( 'hs_popular_weight_views', $defaults['popular_weight_views'] );
        $saved['popular_posts_count']     = (int) get_option( 'hs_popular_posts_count', $defaults['popular_posts_count'] );
        $saved['latest_posts_count']      = (int) get_option( 'hs_latest_posts_count', $defaults['latest_posts_count'] );
    }

    $cached = wp_parse_args( $saved, $defaults );
    return $cached;
}

/**
 * Retrieve a single setting cleanly.
 */
function hs_get_setting( string $key, mixed $default = null ): mixed {
    $all = hs_get_all_settings();
    if ( array_key_exists( $key, $all ) ) {
        return $all[ $key ];
    }
    return $default;
}

/**
 * Check if a modular feature is active (respecting Emergency Kill Switch for AI modules).
 */
function hs_is_module_enabled( string $module_key ): bool {
    if ( in_array( $module_key, array( 'enable_ai_autopilot', 'enable_ai_tutor' ), true ) && (bool) hs_get_setting( 'emergency_kill_switch', false ) ) {
        return false;
    }
    return (bool) hs_get_setting( $module_key, true );
}

/**
 * Synchronize unified settings back to legacy option keys for seamless compatibility.
 */
function hs_sync_legacy_options( array $settings ): void {
    update_option( 'hs_featured_category', (string) ( $settings['featured_category'] ?? 'notice' ) );
    update_option( 'hs_popular_weight_likes', (float) ( $settings['popular_weight_likes'] ?? 1.0 ) );
    update_option( 'hs_popular_weight_comments', (float) ( $settings['popular_weight_comments'] ?? 1.5 ) );
    update_option( 'hs_popular_weight_views', (float) ( $settings['popular_weight_views'] ?? 0.5 ) );
    update_option( 'hs_popular_posts_count', (int) ( $settings['popular_posts_count'] ?? 6 ) );
    update_option( 'hs_latest_posts_count', (int) ( $settings['latest_posts_count'] ?? 8 ) );

    update_option( 'hs_enable_ai_autopilot', ! empty( $settings['enable_ai_autopilot'] ) );
    update_option( 'hs_autopilot_enabled', ! empty( $settings['enable_ai_autopilot'] ) );
    update_option( 'hs_autopilot_publishing_mode', (string) ( $settings['autopilot_publishing_mode'] ?? 'draft_first' ) );
    update_option( 'hs_autopilot_min_quality_score', (int) ( $settings['autopilot_min_quality'] ?? 85 ) );
    update_option( 'hs_autopilot_max_retries', (int) ( $settings['autopilot_max_retries'] ?? 2 ) );
    update_option( 'hs_emergency_kill_switch', ! empty( $settings['emergency_kill_switch'] ) );

    update_option( 'hs_core_master_switches', array(
        'emergency_kill_switch' => ! empty( $settings['emergency_kill_switch'] ),
        'ai_autopilot'          => ! empty( $settings['enable_ai_autopilot'] ),
        'live_demo'             => ! empty( $settings['mod_live_demo'] ),
        'tool_engine'           => ! empty( $settings['mod_tools_engine'] ),
        'creator_studio'        => ! empty( $settings['mod_platform_centers'] ),
        'device_lab'            => ! empty( $settings['mod_device_games_lab'] ),
        'games_center'          => ! empty( $settings['mod_device_games_lab'] ),
        'youtube_integration'   => ! empty( $settings['mod_youtube'] ),
        'github_integration'    => ! empty( $settings['mod_github'] ),
        'auto_linking'          => ! empty( $settings['enable_auto_linking'] ),
        'schema_engine'         => ! empty( $settings['enable_schema_jsonld'] ),
        'download_system'       => ! empty( $settings['mod_downloads'] ),
        'user_submission'       => ! empty( $settings['mod_user_submissions'] ),
    ) );

    if ( ! empty( $settings['adsense_publisher_id'] ) ) {
        update_option( 'hs_adsense_publisher_id', sanitize_text_field( (string) $settings['adsense_publisher_id'] ) );
    }
    if ( ! empty( $settings['gsc_verification_token'] ) ) {
        update_option( 'hs_gsc_verification_token', sanitize_text_field( (string) $settings['gsc_verification_token'] ) );
    }
}

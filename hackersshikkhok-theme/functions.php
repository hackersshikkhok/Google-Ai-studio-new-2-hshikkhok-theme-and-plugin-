<?php
/**
 * HackersShikkhok Permanent Modular Theme Bootstrap (v5.0.0)
 *
 * Clean Architecture:
 * - Zero cluttered code in functions.php; acts purely as an efficient modular loader.
 * - Lazy PSR-4 autoloader ensures heavy registries (2,024+ tools, LMS, AI) only load on demand.
 * - Core Plugin (`hackersshikkhok-core`) retains ONLY CPTs, Taxonomies, dbDelta tables, Capabilities & Crons.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'HS_THEME_VERSION', '5.0.0' );
define( 'HS_THEME_DIR', get_template_directory() );
define( 'HS_THEME_URI', get_template_directory_uri() );

// 1. Core Infrastructure (Autoloader, Settings, Cache, Setup, Conditional Assets, Router, SEO)
require_once HS_THEME_DIR . '/inc/core/autoloader.php';
require_once HS_THEME_DIR . '/inc/core/settings-helper.php';
require_once HS_THEME_DIR . '/inc/core/cache-helper.php';
require_once HS_THEME_DIR . '/inc/core/theme-setup.php';
require_once HS_THEME_DIR . '/inc/core/assets-loader.php';
require_once HS_THEME_DIR . '/inc/core/router.php';
require_once HS_THEME_DIR . '/inc/core/seo-head.php';
require_once HS_THEME_DIR . '/inc/core/module-bootstrap.php';

// 2. Unified REST API Endpoints Loader
require_once HS_THEME_DIR . '/inc/api/rest-endpoints.php';

// 3. Centralized WP Admin Settings Panel (loaded strictly inside wp-admin)
if ( is_admin() ) {
    require_once HS_THEME_DIR . '/inc/admin/settings-panel.php';
}

// 4. Boot enabled modular features conditionally
add_action( 'after_setup_theme', static function (): void {
    \HackersShikkhok\Theme\Core\ModuleBootstrap::boot();
}, 20 );

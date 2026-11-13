<?php
/**
 * Clean Theme Router & View Counter Engine.
 * Guarantees zero-404 resolution for all permanent HackersShikkhok endpoints.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', static function (): void {
    add_rewrite_rule( '^cyber-command/?$', 'index.php?hs_cyber_command=1', 'top' );
    add_rewrite_rule( '^cyber-command/target/([^/]+)/?$', 'index.php?hs_cyber_command=1&hs_target_id=$matches[1]', 'top' );
    add_rewrite_rule( '^cyber-command/report/([^/]+)/?$', 'index.php?hs_cyber_command=1&hs_report_id=$matches[1]', 'top' );
}, 10 );

add_filter( 'query_vars', static function ( array $vars ): array {
    $vars[] = 'hs_cyber_command';
    $vars[] = 'hs_target_id';
    $vars[] = 'hs_report_id';
    return $vars;
} );

add_filter( 'template_include', static function ( string $template ): string {
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
    $path        = trim( (string) parse_url( $request_uri, PHP_URL_PATH ), '/' );

    // Protect /wp-admin access for non-admin users cleanly
    if ( 'wp-admin' === $path ) {
        if ( ! is_user_logged_in() ) {
            wp_safe_redirect( wp_login_url( admin_url() ) );
            exit;
        }
        if ( current_user_can( 'manage_options' ) ) {
            wp_safe_redirect( admin_url() );
            exit;
        }
        wp_safe_redirect( home_url( '/' ) );
        exit;
    }

    if ( get_query_var( 'hs_cyber_command' ) ) {
        $cand = HS_THEME_DIR . '/page-cyber-command.php';
        if ( file_exists( $cand ) ) {
            return $cand;
        }
    }

    $map = array(
        'academy'            => 'page-academy.php',
        'tools'              => 'archive-tools.php',
        'platform-center'    => 'page-platform-center.php',
        'center'             => 'page-platform-center.php',
        'centers'            => 'page-platform-center.php',
        'app-maker'          => 'page-app-maker.php',
        'app-studio'         => 'page-app-maker.php',
        'cyber-command'      => 'page-cyber-command.php',
        'desktop'            => 'page-desktop.php',
        'dashboard'          => 'page-dashboard.php',
        'services'           => 'page-services.php',
        'verify-certificate' => 'page-verify-certificate.php',
        'disclaimer'         => 'page-disclaimer.php',
        'privacy'            => 'page-privacy.php',
        'privacy-policy'     => 'page-privacy.php',
        'terms'              => 'page-terms.php',
        'terms-of-service'   => 'page-terms.php',
        'dmca'               => 'page-dmca.php',
        'dmca-policy'        => 'page-dmca.php',
        'sitemap'            => 'page-sitemap.php',
        'tools-lab'          => 'page-tools-lab.php',
        'new-post'           => 'page-new-post.php',
        'add-new-post'       => 'page-new-post.php',
        'post-new'           => 'page-new-post.php',
        'withdraw'           => 'page-withdraw.php',
        'cashout'            => 'page-withdraw.php',
    );

    if ( isset( $map[ $path ] ) ) {
        if ( 'tools' === $path && isset( $_GET['tool'] ) && ! empty( $_GET['tool'] ) ) {
            $single_tool = HS_THEME_DIR . '/single-tools.php';
            if ( file_exists( $single_tool ) ) {
                return $single_tool;
            }
        }
        $cand = HS_THEME_DIR . '/' . $map[ $path ];
        if ( file_exists( $cand ) ) {
            return $cand;
        }
    }

    // Auth routes
    if ( 'auth' === $path || str_starts_with( $path, 'auth/' ) || 'login' === $path || 'register' === $path ) {
        $cand = HS_THEME_DIR . '/page-dashboard.php';
        if ( file_exists( $cand ) ) {
            return $cand;
        }
    }

    // CPT Single & Archive routes
    $singular_map = array(
        'device_review' => 'single-device_review.php',
        'device_price'  => 'single-device_price.php',
        'tech_news'     => 'single-tech_news.php',
        'tools'         => 'single-tools.php',
        'hs_course'     => 'single-hs_course.php',
    );

    foreach ( $singular_map as $cpt => $file ) {
        if ( is_singular( $cpt ) && file_exists( HS_THEME_DIR . '/' . $file ) ) {
            return HS_THEME_DIR . '/' . $file;
        }
    }

    if ( 'device-reviews' === $path || str_starts_with( $path, 'device-reviews/' ) || is_post_type_archive( 'device_review' ) ) {
        if ( file_exists( HS_THEME_DIR . '/archive-device_review.php' ) ) {
            return HS_THEME_DIR . '/archive-device_review.php';
        }
    }

    if ( 'device-prices' === $path || str_starts_with( $path, 'device-prices/' ) || is_post_type_archive( 'device_price' ) ) {
        if ( file_exists( HS_THEME_DIR . '/archive-device_price.php' ) ) {
            return HS_THEME_DIR . '/archive-device_price.php';
        }
    }

    if ( 'tech-news' === $path || str_starts_with( $path, 'tech-news/' ) || is_post_type_archive( 'tech_news' ) || is_tax( 'news_category' ) ) {
        if ( file_exists( HS_THEME_DIR . '/archive-tech_news.php' ) ) {
            return HS_THEME_DIR . '/archive-tech_news.php';
        }
    }

    if ( 'tools' === $path || str_starts_with( $path, 'tools/' ) || is_post_type_archive( 'tools' ) ) {
        if ( isset( $_GET['tool'] ) && ! empty( $_GET['tool'] ) && file_exists( HS_THEME_DIR . '/single-tools.php' ) ) {
            return HS_THEME_DIR . '/single-tools.php';
        }
        if ( file_exists( HS_THEME_DIR . '/archive-tools.php' ) ) {
            return HS_THEME_DIR . '/archive-tools.php';
        }
    }

    if ( is_post_type_archive( 'hs_course' ) && file_exists( HS_THEME_DIR . '/archive-hs_course.php' ) ) {
        return HS_THEME_DIR . '/archive-hs_course.php';
    }

    return $template;
} );

// Track singular post views safely (skipping admin, REST, cron, and known bots)
add_action( 'template_redirect', static function (): void {
    if ( ! is_singular() || is_admin() || wp_doing_ajax() || wp_is_json_request() ) {
        return;
    }

    $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( (string) wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
    if ( '' !== $ua && preg_match( '/bot|crawl|spider|slurp|lighthouse|pagespeed/i', $ua ) ) {
        return;
    }

    $post_id = (int) get_queried_object_id();
    if ( $post_id > 0 && class_exists( '\\HackersShikkhok\\Theme\\Modules\\Platform\\MetricsManager' ) ) {
        \HackersShikkhok\Theme\Modules\Platform\MetricsManager::increment_views( $post_id );
    }
} );

<?php
/**
 * High-Performance Transient & Query Cache Layer.
 * Minimizes database queries and optimizes Time to First Byte (TTFB).
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Retrieve cached data via WP Transients or compute and store it.
 */
function hs_cache_remember( string $key, callable $callback, ?int $ttl = null ): mixed {
    $cache_enabled = (bool) hs_get_setting( 'enable_transient_cache', true );
    $safe_key      = 'hs_tc_' . md5( $key );

    if ( $cache_enabled ) {
        $cached = get_transient( $safe_key );
        if ( false !== $cached ) {
            return $cached;
        }
    }

    $value = $callback();

    if ( $cache_enabled ) {
        $duration = $ttl ?? max( 60, (int) hs_get_setting( 'cache_ttl_seconds', 600 ) );
        set_transient( $safe_key, $value, $duration );
    }

    return $value;
}

/**
 * Forget a specific transient cache key.
 */
function hs_cache_forget( string $key ): void {
    delete_transient( 'hs_tc_' . md5( $key ) );
}

/**
 * Purge all HackersShikkhok theme transients & object caches.
 */
function hs_purge_all_caches(): void {
    global $wpdb;
    if ( $wpdb ) {
        $wpdb->query(
            "DELETE FROM {$wpdb->options}
             WHERE option_name LIKE '\_transient\_hs\_%'
                OR option_name LIKE '\_transient\_timeout\_hs\_%'
                OR option_name LIKE '\_site\_transient\_hs\_%'"
        );
    }
    wp_cache_flush();
}

// Auto-invalidate homepage & post query caches when content or comments change
add_action( 'save_post', static function (): void {
    hs_cache_forget( 'front_featured_ids' );
    hs_cache_forget( 'front_popular_ids' );
    hs_cache_forget( 'front_latest_ids' );
} );

add_action( 'deleted_post', static function (): void {
    hs_cache_forget( 'front_featured_ids' );
    hs_cache_forget( 'front_popular_ids' );
    hs_cache_forget( 'front_latest_ids' );
} );

add_action( 'comment_post', static function (): void {
    hs_cache_forget( 'front_popular_ids' );
} );

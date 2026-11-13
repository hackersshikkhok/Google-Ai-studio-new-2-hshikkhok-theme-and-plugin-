<?php
/**
 * Advanced Cache & Performance Engine
 * Handles full-site cache purges, transient clearing, and object cache resets.
 */
declare(strict_types=1);

namespace HackersShikkhok\Theme\Core;

final class CacheCompatibilityManager {
    public static function register(): void {
        // Automatically purge cache on specific actions
        add_action( 'save_post', array( self::class, 'purge_post_fragments' ) );
        add_action( 'updated_post_meta', array( self::class, 'purge_post_fragments' ) );
    }

    public static function purge_post_fragments( int $post_id ): void {
        wp_cache_delete( 'hs_post_schema_' . $post_id, 'hs_core' );
        delete_transient( 'hs_center_summary_' . get_post_type( $post_id ) );
    }

    /**
     * Manual Full Site Cache Purge
     */
    public static function purge_all(): bool {
        // 1. Purge WP Transients
        global $wpdb;
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_hs_%' OR option_name LIKE '_site_transient_hs_%'" );

        // 2. Purge Object Cache
        wp_cache_flush();

        // 3. Clear any custom file-based cache if added later
        return true;
    }
}

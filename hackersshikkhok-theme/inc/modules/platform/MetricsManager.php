<?php
/**
 * Metrics Manager - Handle Views and Likes
 */
declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Platform;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MetricsManager {
    public static function register(): void {
        add_action( 'wp_ajax_hs_like_post', array( self::class, 'handle_like' ) );
        add_action( 'wp_ajax_nopriv_hs_like_post', array( self::class, 'handle_like' ) );
    }

    public static function increment_views( int $post_id ): void {
        $views = (int) get_post_meta( $post_id, 'hs_views_count', true );
        update_post_meta( $post_id, 'hs_views_count', $views + 1 );
    }

    public static function get_views( int $post_id ): int {
        return (int) get_post_meta( $post_id, 'hs_views_count', true );
    }

    public static function get_likes( int $post_id ): int {
        return (int) get_post_meta( $post_id, 'hs_likes_count', true );
    }

    public static function handle_like(): void {
        $post_id = intval( $_POST['post_id'] ?? 0 );
        if ( ! $post_id ) wp_send_json_error( 'Invalid Post' );

        $likes = self::get_likes( $post_id );
        $new_likes = $likes + 1;
        update_post_meta( $post_id, 'hs_likes_count', $new_likes );
        
        wp_send_json_success( array( 'likes' => $new_likes ) );
    }
}

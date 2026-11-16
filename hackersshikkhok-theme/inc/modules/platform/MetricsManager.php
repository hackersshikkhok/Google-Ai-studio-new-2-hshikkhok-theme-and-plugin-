<?php
/**
 * Metrics Manager - Handle Views, Likes, Bookmarks & Content Reporting
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
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

        add_action( 'wp_ajax_hs_bookmark_post', array( self::class, 'handle_bookmark' ) );
        add_action( 'wp_ajax_nopriv_hs_bookmark_post', array( self::class, 'handle_bookmark' ) );

        add_action( 'wp_ajax_hs_report_post', array( self::class, 'handle_report' ) );
        add_action( 'wp_ajax_nopriv_hs_report_post', array( self::class, 'handle_report' ) );
    }

    /**
     * Increment views cleanly with session/cookie deduplication
     */
    public static function increment_views( int $post_id ): void {
        if ( $post_id <= 0 ) {
            return;
        }

        // Avoid incrementing if user already viewed within 1 hour
        $cookie_name = 'hs_viewed_' . $post_id;
        if ( isset( $_COOKIE[ $cookie_name ] ) ) {
            return;
        }

        $views = (int) get_post_meta( $post_id, 'hs_views_count', true );
        $new_views = $views + 1;
        update_post_meta( $post_id, 'hs_views_count', $new_views );

        if ( ! headers_sent() ) {
            setcookie( $cookie_name, '1', time() + 3600, COOKIEPATH ?: '/', COOKIE_DOMAIN ?: '', is_ssl(), true );
        }
    }

    public static function get_views( int $post_id ): int {
        return (int) get_post_meta( $post_id, 'hs_views_count', true );
    }

    public static function get_likes( int $post_id ): int {
        return (int) get_post_meta( $post_id, 'hs_likes_count', true );
    }

    /**
     * Interactive AJAX Like handler with toggle capability
     */
    public static function handle_like(): void {
        check_ajax_referer( 'hs_ajax_nonce', 'nonce' );

        $post_id = intval( $_POST['post_id'] ?? 0 );
        if ( ! $post_id ) {
            wp_send_json_error( array( 'message' => 'Invalid Post ID' ), 400 );
        }

        $user_id   = get_current_user_id();
        $cookie_key = 'hs_liked_' . $post_id;
        $is_liked   = false;

        $likes = self::get_likes( $post_id );

        if ( $user_id > 0 ) {
            $user_likes = (array) get_user_meta( $user_id, '_hs_liked_posts', true );
            if ( in_array( $post_id, $user_likes, true ) ) {
                // Unlike
                $user_likes = array_diff( $user_likes, array( $post_id ) );
                $new_likes  = max( 0, $likes - 1 );
                $is_liked   = false;
            } else {
                // Like
                $user_likes[] = $post_id;
                $new_likes    = $likes + 1;
                $is_liked     = true;
            }
            update_user_meta( $user_id, '_hs_liked_posts', array_values( $user_likes ) );
        } else {
            // Guest like toggle via cookie
            if ( isset( $_COOKIE[ $cookie_key ] ) ) {
                $new_likes = max( 0, $likes - 1 );
                $is_liked  = false;
                if ( ! headers_sent() ) {
                    setcookie( $cookie_key, '', time() - 3600, COOKIEPATH ?: '/', COOKIE_DOMAIN ?: '', is_ssl(), true );
                }
            } else {
                $new_likes = $likes + 1;
                $is_liked  = true;
                if ( ! headers_sent() ) {
                    setcookie( $cookie_key, '1', time() + ( 86400 * 30 ), COOKIEPATH ?: '/', COOKIE_DOMAIN ?: '', is_ssl(), true );
                }
            }
        }

        update_post_meta( $post_id, 'hs_likes_count', $new_likes );

        wp_send_json_success( array(
            'likes'    => $new_likes,
            'is_liked' => $is_liked,
            'message'  => $is_liked ? 'Liked!' : 'Unliked',
        ) );
    }

    /**
     * Interactive AJAX Bookmark handler
     */
    public static function handle_bookmark(): void {
        check_ajax_referer( 'hs_ajax_nonce', 'nonce' );

        $post_id = intval( $_POST['post_id'] ?? 0 );
        if ( ! $post_id ) {
            wp_send_json_error( array( 'message' => 'Invalid Post ID' ), 400 );
        }

        $user_id = get_current_user_id();
        if ( $user_id <= 0 ) {
            wp_send_json_success( array(
                'success'       => true,
                'is_bookmarked' => true,
                'message'       => 'Saved to local bookmarks (sign in to sync across devices)',
            ) );
        }

        $bookmarks = (array) get_user_meta( $user_id, '_hs_user_bookmarks', true );
        $key       = (string) $post_id;
        $is_bookmarked = isset( $bookmarks[ $key ] );

        if ( $is_bookmarked ) {
            unset( $bookmarks[ $key ] );
            $action = 'removed';
            $msg    = 'Removed from bookmarks';
        } else {
            $bookmarks[ $key ] = array(
                'item_id'   => $key,
                'item_type' => get_post_type( $post_id ) ?: 'post',
                'title'     => get_the_title( $post_id ),
                'url'       => get_permalink( $post_id ),
                'saved_at'  => gmdate( 'c' ),
            );
            $action = 'added';
            $msg    = 'Added to your bookmarks!';
        }

        update_user_meta( $user_id, '_hs_user_bookmarks', $bookmarks );

        wp_send_json_success( array(
            'success'       => true,
            'action'        => $action,
            'is_bookmarked' => 'added' === $action,
            'message'       => $msg,
            'total_saved'   => count( $bookmarks ),
        ) );
    }

    /**
     * Interactive AJAX Content Reporting handler
     */
    public static function handle_report(): void {
        check_ajax_referer( 'hs_ajax_nonce', 'nonce' );

        $post_id = intval( $_POST['post_id'] ?? 0 );
        $reason  = sanitize_text_field( (string) ( $_POST['reason'] ?? 'Inappropriate Content' ) );
        $details = sanitize_textarea_field( (string) ( $_POST['details'] ?? '' ) );

        if ( ! $post_id ) {
            wp_send_json_error( array( 'message' => 'Invalid target post.' ), 400 );
        }

        $reports = (array) get_post_meta( $post_id, '_hs_reported_flags', true );
        $reports[] = array(
            'timestamp'  => gmdate( 'Y-m-d H:i:s' ),
            'reporter'   => get_current_user_id() ?: 'Guest',
            'reason'     => $reason,
            'details'    => $details,
            'ip_address' => sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ),
        );

        update_post_meta( $post_id, '_hs_reported_flags', $reports );
        update_post_meta( $post_id, '_hs_reported_count', count( $reports ) );

        wp_send_json_success( array(
            'success' => true,
            'message' => 'Report submitted successfully. Our security and moderation team will inspect this content.',
        ) );
    }
}

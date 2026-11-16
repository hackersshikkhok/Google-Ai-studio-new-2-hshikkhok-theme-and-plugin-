<?php
/**
 * HackersShikkhok - Ultra-Pro Real-Time Notification Engine
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * Handles live badge counters, AJAX polling, mark-all-as-read, and interactive drawer alerts.
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Community;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class NotificationEngine {

    public static function init(): void {
        add_action( 'wp_ajax_hs_get_notifications', array( self::class, 'ajax_get_notifications' ) );
        add_action( 'wp_ajax_hs_mark_notifications_read', array( self::class, 'ajax_mark_notifications_read' ) );
        add_action( 'wp_ajax_nopriv_hs_get_notifications', array( self::class, 'ajax_guest_notifications' ) );
        add_action( 'rest_api_init', array( self::class, 'register_rest_routes' ) );
    }

    public static function register_rest_routes(): void {
        register_rest_route( 'hackersshikkhok/v1', '/notifications/mark-read', array(
            'methods'             => 'POST',
            'permission_callback' => static fn() => is_user_logged_in(),
            'callback'            => array( self::class, 'rest_mark_read' ),
        ) );
    }

    public static function rest_mark_read(): \WP_REST_Response {
        $uid = get_current_user_id();
        $notis = self::get_user_notifications( $uid );
        foreach ( $notis as &$n ) {
            $n['read'] = true;
        }
        update_user_meta( $uid, '_hs_user_notifications', $notis );
        update_user_meta( $uid, 'notifications', $notis );
        return new \WP_REST_Response( array( 'success' => true, 'unread_count' => 0 ), 200 );
    }

    /**
     * Get user notifications
     */
    public static function get_user_notifications( int $user_id ): array {
        if ( $user_id <= 0 ) {
            return array();
        }

        $notis = get_user_meta( $user_id, '_hs_user_notifications', true );
        if ( empty( $notis ) || ! is_array( $notis ) ) {
            $alt = get_user_meta( $user_id, 'notifications', true );
            if ( ! empty( $alt ) && is_array( $alt ) ) {
                $notis = $alt;
            }
        }

        if ( empty( $notis ) ) {
            $notis = array(
                array(
                    'id'        => 'noti_welcome',
                    'icon'      => '🛡️',
                    'type'      => 'security',
                    'title'     => 'Welcome to HackersShikkhok!',
                    'message'   => 'Explore 2,024+ client-side tools, Cyber Academy courses, and Q&A community.',
                    'body'      => 'Explore 2,024+ client-side tools, Cyber Academy courses, and Q&A community.',
                    'read'      => false,
                    'time_ago'  => 'Just now',
                    'timestamp' => gmdate( 'M d, Y' ),
                    'url'       => home_url( '/academy/' ),
                ),
            );
            update_user_meta( $user_id, '_hs_user_notifications', $notis );
        }

        return array_values( array_filter( (array) $notis, 'is_array' ) );
    }

    /**
     * Get unread count
     */
    public static function get_unread_count( int $user_id ): int {
        if ( $user_id <= 0 ) {
            return 0;
        }
        $notis = self::get_user_notifications( $user_id );
        return count( array_filter( $notis, static fn( $n ) => empty( $n['read'] ) ) );
    }

    public static function ajax_get_notifications(): void {
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }
        $notis = self::get_user_notifications( $user_id );
        $unread = count( array_filter( $notis, static fn( $n ) => empty( $n['read'] ) ) );
        wp_send_json_success( array(
            'unread_count'  => $unread,
            'notifications' => array_slice( $notis, 0, 8 ),
        ) );
    }

    public static function ajax_guest_notifications(): void {
        wp_send_json_success( array(
            'unread_count'  => 0,
            'notifications' => array(),
        ) );
    }

    public static function ajax_mark_notifications_read(): void {
        check_ajax_referer( 'hs_ajax_nonce', 'nonce' );
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            wp_send_json_error();
        }
        $notis = self::get_user_notifications( $user_id );
        foreach ( $notis as &$n ) {
            $n['read'] = true;
        }
        update_user_meta( $user_id, '_hs_user_notifications', $notis );
        update_user_meta( $user_id, 'notifications', $notis );

        wp_send_json_success( array(
            'success'      => true,
            'unread_count' => 0,
            'message'      => 'All notifications marked as read.',
        ) );
    }
}

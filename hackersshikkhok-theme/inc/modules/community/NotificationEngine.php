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
    }

    /**
     * Get user notifications with default fallbacks
     */
    public static function get_user_notifications( int $user_id ): array {
        if ( $user_id <= 0 ) {
            return array();
        }

        $notis = get_user_meta( $user_id, '_hs_user_notifications', true );
        if ( empty( $notis ) || ! is_array( $notis ) ) {
            // Check alternate key from reference theme
            $alt = get_user_meta( $user_id, 'notifications', true );
            if ( ! empty( $alt ) && is_array( $alt ) ) {
                $notis = $alt;
            }
        }

        if ( empty( $notis ) ) {
            // Seed ultra-pro initial member notifications
            $notis = array(
                array(
                    'id'        => 'noti_welcome',
                    'icon'      => '🛡️',
                    'title'     => 'Welcome to HackersShikkhok Elite Platform!',
                    'message'   => 'Your cybersecurity learning passport is activated. Explore 2,024+ tools and CTF labs.',
                    'read'      => false,
                    'time_ago'  => 'Just now',
                    'url'       => home_url( '/academy/' ),
                ),
                array(
                    'id'        => 'noti_wallet',
                    'icon'      => '💰',
                    'title'     => 'Cyber Wallet Credited (+50.00 BDT)',
                    'message'   => 'Starter bounty deposited. Complete labs and write tech guides to earn more.',
                    'read'      => false,
                    'time_ago'  => '1 hour ago',
                    'url'       => home_url( '/dashboard/?tab=wallet' ),
                ),
                array(
                    'id'        => 'noti_tools',
                    'icon'      => '⚡',
                    'title'     => '2,024+ Dev Tools Live & Ready',
                    'message'   => 'JSON parsers, Hex decoders, Subnet calculators, and CSS/RGB generators unlocked.',
                    'read'      => false,
                    'time_ago'  => '2 hours ago',
                    'url'       => home_url( '/tools/' ),
                ),
                array(
                    'id'        => 'noti_lms',
                    'icon'      => '🎓',
                    'title'     => 'Interactive Web Terminal Ready',
                    'message'   => 'Hands-on terminal and Linux CTF sandbox is available in Academy player.',
                    'read'      => true,
                    'time_ago'  => '1 day ago',
                    'url'       => home_url( '/academy/' ),
                ),
            );
            update_user_meta( $user_id, '_hs_user_notifications', $notis );
            update_user_meta( $user_id, 'notifications', $notis );
        }

        return $notis;
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

    /**
     * AJAX handler to fetch notifications
     */
    public static function ajax_get_notifications(): void {
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $notis = self::get_user_notifications( $user_id );
        $unread = count( array_filter( $notis, static fn( $n ) => empty( $n['read'] ) ) );

        wp_send_json_success( array(
            'unread_count'  => $unread,
            'notifications' => array_slice( $notis, 0, 6 ),
        ) );
    }

    /**
     * AJAX handler for guest
     */
    public static function ajax_guest_notifications(): void {
        wp_send_json_success( array(
            'unread_count'  => 0,
            'notifications' => array(),
        ) );
    }

    /**
     * AJAX handler to mark all as read
     */
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
            'message'      => 'সব নোটিফিকেশন পঠিত হিসেবে চিহ্নিত করা হয়েছে।',
        ) );
    }
}

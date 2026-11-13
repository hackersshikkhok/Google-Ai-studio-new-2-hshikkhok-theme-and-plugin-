<?php
/**
 * HackersShikkhok - Revolutionary Cyber Stories Engine
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * Handles 24h ephemeral member stories, media/text uploads, reactions, comments, and views.
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Community;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class StoryEngine {

    public const OPTION_KEY = 'hs_cyber_stories';

    public static function init(): void {
        add_action( 'wp_ajax_hs_upload_story', array( self::class, 'ajax_upload_story' ) );
        add_action( 'wp_ajax_hs_view_story', array( self::class, 'ajax_view_story' ) );
        add_action( 'wp_ajax_hs_react_story', array( self::class, 'ajax_react_story' ) );
        add_action( 'wp_ajax_hs_comment_story', array( self::class, 'ajax_comment_story' ) );
    }

    /**
     * Retrieve active stories within the last 24 hours (86,400 seconds)
     */
    public static function get_active_stories(): array {
        $stories = get_option( self::OPTION_KEY, array() );
        if ( ! is_array( $stories ) ) {
            $stories = array();
        }

        $now = time();
        $active_stories = array();
        $changed = false;

        foreach ( $stories as $story ) {
            if ( isset( $story['created_at'] ) && ( $now - (int) $story['created_at'] ) < 86400 ) {
                $active_stories[] = $story;
            } else {
                $changed = true;
            }
        }

        if ( $changed ) {
            update_option( self::OPTION_KEY, $active_stories );
        }

        return $active_stories;
    }

    /**
     * AJAX: Upload story (Text, Image, or Video)
     */
    public static function ajax_upload_story(): void {
        check_ajax_referer( 'hs_ajax_nonce', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => 'অনুমতি নেই! দয়া করে লগইন করুন।' ) );
        }

        $user_id    = get_current_user_id();
        $user       = wp_get_current_user();
        $media_type = isset( $_POST['media_type'] ) ? sanitize_text_field( (string) $_POST['media_type'] ) : 'text';
        $caption    = isset( $_POST['caption'] ) ? sanitize_textarea_field( (string) $_POST['caption'] ) : '';
        $bg_grad    = isset( $_POST['bg_gradient'] ) ? sanitize_text_field( (string) $_POST['bg_gradient'] ) : 'linear-gradient(135deg, #00f5d4, #7b2cbf)';
        $media_url  = '';

        // Handle File Upload if provided
        if ( ! empty( $_FILES['story_file'] ) && 0 === $_FILES['story_file']['error'] ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $attachment_id = media_handle_upload( 'story_file', 0 );
            if ( ! is_wp_error( $attachment_id ) ) {
                $media_url = (string) wp_get_attachment_url( $attachment_id );
            } else {
                wp_send_json_error( array( 'message' => 'ফাইল আপলোড ব্যর্থ হয়েছে: ' . $attachment_id->get_error_message() ) );
            }
        } elseif ( ! empty( $_POST['media_url'] ) ) {
            $media_url = esc_url_raw( (string) $_POST['media_url'] );
        }

        if ( 'text' === $media_type && empty( $caption ) ) {
            wp_send_json_error( array( 'message' => 'টেক্সট স্টোরির জন্য কিছু লিখুন!' ) );
        }

        $all_stories = get_option( self::OPTION_KEY, array() );
        if ( ! is_array( $all_stories ) ) {
            $all_stories = array();
        }

        $story_id = 'hs_story_' . uniqid( '', true );
        $new_story = array(
            'id'          => $story_id,
            'user_id'     => $user_id,
            'user_name'   => $user->display_name ?: $user->user_login,
            'user_avatar' => get_avatar_url( $user_id, array( 'size' => 96 ) ),
            'media_type'  => $media_type,
            'media_url'   => $media_url,
            'caption'     => $caption,
            'bg_gradient' => $bg_grad,
            'created_at'  => time(),
            'views'       => array(),
            'reactions'   => array(),
            'comments'    => array(),
        );

        $all_stories[] = $new_story;
        update_option( self::OPTION_KEY, $all_stories );

        // Award XP / Gamification bonus
        if ( class_exists( '\HackersShikkhok\Theme\Modules\LMS\Wallet_Economy' ) ) {
            \HackersShikkhok\Theme\Modules\LMS\Wallet_Economy::award_xp( $user_id, 5, 'Published Cyber Story' );
        }

        wp_send_json_success( array(
            'message' => '🚀 আপনার স্টোরি সফলভাবে পাবলিশ হয়েছে! (+৫ XP অর্জিত)',
            'story'   => $new_story,
        ) );
    }

    /**
     * AJAX: Track story view
     */
    public static function ajax_view_story(): void {
        check_ajax_referer( 'hs_ajax_nonce', 'nonce' );
        $story_id = isset( $_POST['story_id'] ) ? sanitize_text_field( (string) $_POST['story_id'] ) : '';
        if ( empty( $story_id ) ) {
            wp_send_json_error();
        }

        $viewer_id = get_current_user_id();
        $stories   = get_option( self::OPTION_KEY, array() );
        $updated   = false;

        foreach ( $stories as &$story ) {
            if ( ( $story['id'] ?? '' ) === $story_id ) {
                if ( ! in_array( $viewer_id, $story['views'] ?? array(), true ) ) {
                    $story['views'][] = $viewer_id;
                    $updated = true;
                }
                break;
            }
        }

        if ( $updated ) {
            update_option( self::OPTION_KEY, $stories );
        }

        wp_send_json_success();
    }

    /**
     * AJAX: React to story
     */
    public static function ajax_react_story(): void {
        check_ajax_referer( 'hs_ajax_nonce', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error();
        }
        $story_id = isset( $_POST['story_id'] ) ? sanitize_text_field( (string) $_POST['story_id'] ) : '';
        $reaction = isset( $_POST['react_type'] ) ? sanitize_text_field( (string) $_POST['react_type'] ) : '❤️';
        $user_id  = get_current_user_id();

        if ( empty( $story_id ) ) {
            wp_send_json_error();
        }

        $stories = get_option( self::OPTION_KEY, array() );
        $updated = false;

        foreach ( $stories as &$story ) {
            if ( ( $story['id'] ?? '' ) === $story_id ) {
                $story['reactions'][ $user_id ] = $reaction;
                $updated = true;
                break;
            }
        }

        if ( $updated ) {
            update_option( self::OPTION_KEY, $stories );
        }

        wp_send_json_success( array( 'message' => 'Reaction recorded!' ) );
    }

    /**
     * AJAX: Comment on story
     */
    public static function ajax_comment_story(): void {
        check_ajax_referer( 'hs_ajax_nonce', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }
        $story_id = isset( $_POST['story_id'] ) ? sanitize_text_field( (string) $_POST['story_id'] ) : '';
        $text     = isset( $_POST['comment_text'] ) ? sanitize_text_field( (string) $_POST['comment_text'] ) : '';
        $user_id  = get_current_user_id();
        $user     = wp_get_current_user();

        if ( empty( $story_id ) || empty( $text ) ) {
            wp_send_json_error( array( 'message' => 'Invalid comment' ) );
        }

        $stories  = get_option( self::OPTION_KEY, array() );
        $comments = array();
        $updated  = false;

        foreach ( $stories as &$story ) {
            if ( ( $story['id'] ?? '' ) === $story_id ) {
                $new_c = array(
                    'user_id'     => $user_id,
                    'user_name'   => $user->display_name ?: $user->user_login,
                    'user_avatar' => get_avatar_url( $user_id, array( 'size' => 64 ) ),
                    'text'        => $text,
                    'time'        => time(),
                );
                $story['comments'][] = $new_c;
                $comments = $story['comments'];
                $updated = true;
                break;
            }
        }

        if ( $updated ) {
            update_option( self::OPTION_KEY, $stories );
        }

        wp_send_json_success( array( 'comments' => $comments ) );
    }
}

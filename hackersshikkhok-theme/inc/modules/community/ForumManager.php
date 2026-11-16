<?php
declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Community;

use HackersShikkhok\Theme\Modules\Platform\UserEcosystem;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Community Q&A & Discussion Forum Backend Engine.
 * Handles voting, solution acceptance, topic following, answers, and reporting.
 */
final class ForumManager {

    public static function register(): void {
        add_action( 'rest_api_init', array( self::class, 'register_rest_routes' ) );
    }

    public static function register_rest_routes(): void {
        register_rest_route( 'hackersshikkhok/v1', '/forum/vote', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_vote' ),
            'permission_callback' => static fn(): bool => is_user_logged_in(),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/forum/accept-solution', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_accept_solution' ),
            'permission_callback' => static fn(): bool => is_user_logged_in(),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/forum/follow-question', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_follow_question' ),
            'permission_callback' => static fn(): bool => is_user_logged_in(),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/forum/answer', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_submit_answer' ),
            'permission_callback' => static fn(): bool => is_user_logged_in(),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/forum/report', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_report_topic' ),
            'permission_callback' => static fn(): bool => is_user_logged_in(),
        ) );
    }

    public static function mark_solution_accepted( int $question_id, int $comment_id ): bool {
        $author_id = (int) get_post_field( 'post_author', $question_id );
        if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'hs_moderate_forum' ) && get_current_user_id() !== $author_id ) {
            return false;
        }
        update_post_meta( $question_id, '_hs_accepted_answer_id', $comment_id );
        update_post_meta( $question_id, '_hs_question_solved', 1 );

        $comment = get_comment( $comment_id );
        if ( $comment && (int) $comment->user_id > 0 && (int) $comment->user_id !== $author_id ) {
            if ( ! get_post_meta( $question_id, '_hs_solution_rewarded', true ) ) {
                update_post_meta( $question_id, '_hs_solution_rewarded', 1 );
                UserEcosystem::record_ledger_transaction(
                    (int) $comment->user_id,
                    'reward',
                    2.00,
                    25,
                    'Accepted solution for question #' . $question_id,
                    'SOL-' . $question_id . '-' . $comment_id
                );
            }
        }
        return true;
    }

    public static function rest_vote( WP_REST_Request $request ): WP_REST_Response {
        $user_id     = get_current_user_id();
        $target_type = sanitize_key( (string) ( $request->get_param( 'target_type' ) ?: 'question' ) );
        $target_id   = (int) $request->get_param( 'target_id' );
        $direction   = sanitize_key( (string) ( $request->get_param( 'direction' ) ?: 'up' ) );
        if ( $target_id <= 0 || ! in_array( $direction, array( 'up', 'down' ), true ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Invalid vote parameters.' ), 400 );
        }

        $meta_key_votes = 'comment' === $target_type ? '_hs_answer_votes' : '_hs_question_votes';
        $meta_key_map   = '_hs_voter_map';

        if ( 'comment' === $target_type ) {
            $map = get_comment_meta( $target_id, $meta_key_map, true );
            $map = is_array( $map ) ? $map : array();
            $prev = $map[ $user_id ] ?? '';
            $votes = (int) get_comment_meta( $target_id, $meta_key_votes, true );
            if ( $prev === $direction ) {
                unset( $map[ $user_id ] );
                $votes += ( 'up' === $direction ) ? -1 : 1;
                $current = '';
            } else {
                if ( '' !== $prev ) {
                    $votes += ( 'up' === $direction ) ? 2 : -2;
                } else {
                    $votes += ( 'up' === $direction ) ? 1 : -1;
                }
                $map[ $user_id ] = $direction;
                $current = $direction;
            }
            update_comment_meta( $target_id, $meta_key_map, $map );
            update_comment_meta( $target_id, $meta_key_votes, $votes );
        } else {
            $map = get_post_meta( $target_id, $meta_key_map, true );
            $map = is_array( $map ) ? $map : array();
            $prev = $map[ $user_id ] ?? '';
            $votes = (int) ( get_post_meta( $target_id, $meta_key_votes, true ) ?: 1 );
            if ( $prev === $direction ) {
                unset( $map[ $user_id ] );
                $votes += ( 'up' === $direction ) ? -1 : 1;
                $current = '';
            } else {
                if ( '' !== $prev ) {
                    $votes += ( 'up' === $direction ) ? 2 : -2;
                } else {
                    $votes += ( 'up' === $direction ) ? 1 : -1;
                }
                $map[ $user_id ] = $direction;
                $current = $direction;
            }
            update_post_meta( $target_id, $meta_key_map, $map );
            update_post_meta( $target_id, $meta_key_votes, $votes );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'votes'   => $votes,
            'state'   => $current,
        ) );
    }

    public static function rest_accept_solution( WP_REST_Request $request ): WP_REST_Response {
        $qid = (int) $request->get_param( 'question_id' );
        $cid = (int) $request->get_param( 'comment_id' );
        if ( $qid <= 0 || $cid <= 0 ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Missing question or comment ID.' ), 400 );
        }
        $ok = self::mark_solution_accepted( $qid, $cid );
        if ( ! $ok ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Only the question author or a moderator can mark an accepted solution.' ), 403 );
        }
        return new WP_REST_Response( array(
            'success' => true,
            'message' => '✓ Marked as Accepted Solution! Contributor awarded +25 XP & BDT 2.00.',
        ) );
    }

    public static function rest_follow_question( WP_REST_Request $request ): WP_REST_Response {
        $uid = get_current_user_id();
        $qid = (int) $request->get_param( 'question_id' );
        if ( $qid <= 0 ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Invalid question ID.' ), 400 );
        }
        $followed = get_user_meta( $uid, '_hs_followed_questions', true );
        $followed = is_array( $followed ) ? array_map( 'intval', $followed ) : array();
        if ( in_array( $qid, $followed, true ) ) {
            $followed = array_values( array_diff( $followed, array( $qid ) ) );
            $is_following = false;
        } else {
            $followed[] = $qid;
            $is_following = true;
        }
        update_user_meta( $uid, '_hs_followed_questions', $followed );
        $followers = (int) get_post_meta( $qid, '_hs_question_followers', true );
        $followers = max( 0, $followers + ( $is_following ? 1 : -1 ) );
        update_post_meta( $qid, '_hs_question_followers', $followers );

        return new WP_REST_Response( array(
            'success'   => true,
            'following' => $is_following,
            'followers' => $followers,
            'message'   => $is_following ? '✓ Following this thread.' : 'Unfollowed thread.',
        ) );
    }

    public static function rest_submit_answer( WP_REST_Request $request ): WP_REST_Response {
        $user = wp_get_current_user();
        $qid  = (int) $request->get_param( 'question_id' );
        $body = wp_kses_post( (string) $request->get_param( 'content' ) );

        if ( $qid <= 0 || empty( trim( wp_strip_all_tags( $body ) ) ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Please enter an answer before submitting.' ), 400 );
        }
        $post = get_post( $qid );
        if ( ! $post || 'hs_question' !== $post->post_type ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Question not found.' ), 404 );
        }

        $cid = wp_insert_comment( array(
            'comment_post_ID'      => $qid,
            'comment_author'       => $user->display_name ?: $user->user_login,
            'comment_author_email' => $user->user_email,
            'comment_content'      => $body,
            'user_id'              => $user->ID,
            'comment_approved'     => 1,
        ) );

        if ( ! $cid ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Failed to save answer.' ), 500 );
        }

        UserEcosystem::record_ledger_transaction(
            (int) $user->ID,
            'reward',
            0.0,
            10,
            'Submitted answer on question #' . $qid,
            'ANS-' . $cid
        );

        return new WP_REST_Response( array(
            'success'    => true,
            'comment_id' => (int) $cid,
            'message'    => '✓ Answer posted! +10 XP earned.',
        ) );
    }

    public static function rest_report_topic( WP_REST_Request $request ): WP_REST_Response {
        $uid     = get_current_user_id();
        $qid     = (int) $request->get_param( 'question_id' );
        $reason  = sanitize_text_field( (string) ( $request->get_param( 'reason' ) ?: 'Inappropriate / Spam' ) );
        $details = sanitize_textarea_field( (string) $request->get_param( 'details' ) );

        if ( $qid <= 0 ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Invalid target ID.' ), 400 );
        }

        $queue = get_option( '_hs_reported_content_queue', array() );
        if ( ! is_array( $queue ) ) {
            $queue = array();
        }
        $queue[] = array(
            'post_id'      => $qid,
            'target_label' => get_the_title( $qid ) ?: ( 'Topic #' . $qid ),
            'reason'       => $reason,
            'details'      => $details,
            'user_id'      => $uid,
            'status'       => 'open',
            'reported_at'  => current_time( 'mysql' ),
        );
        update_option( '_hs_reported_content_queue', $queue );

        return new WP_REST_Response( array(
            'success' => true,
            'message' => '✓ Report logged for moderation review.',
        ) );
    }
}

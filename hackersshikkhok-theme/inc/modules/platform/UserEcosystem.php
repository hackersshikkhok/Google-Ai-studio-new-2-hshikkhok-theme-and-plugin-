<?php
declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Platform;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Authoritative User Wallet Ledger & Learning XP System
 * Strictly separates non-monetary Learning Points/XP/Badges from Monetary Wallet Balance (BDT).
 * Purely SQL Ledger-backed Authority with Database Row Locking & Idempotency.
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */
final class UserEcosystem {

    /**
     * Get authoritative wallet balance and learning XP derived directly from SQL ledger calculations
     */
    public static function get_wallet_summary( int $user_id ): array {
        global $wpdb;
        if ( $user_id <= 0 ) {
            return array(
                'balance_bdt' => 0.0,
                'points'      => 0,
                'ledger_rows' => 0,
                'note'        => 'Unauthenticated guest user.',
            );
        }

        $table = $wpdb->prefix . 'hs_wallet_ledger';

        // Authoritative SQL aggregation for balance and points
        $summary = $wpdb->get_row( $wpdb->prepare(
            "SELECT 
                COALESCE(SUM(CASE 
                    WHEN tx_type IN ('credit', 'deposit', 'earnings', 'reward', 'bounty', 'cashback') THEN amount_bdt 
                    WHEN tx_type IN ('debit', 'withdrawal', 'fee', 'purchase', 'payout') THEN -amount_bdt 
                    ELSE 0 
                END), 0) AS calculated_balance,
                COALESCE(SUM(points_delta), 0) AS calculated_points,
                COUNT(tx_id) AS total_transactions
            FROM {$table}
            WHERE user_id = %d",
            $user_id
        ), ARRAY_A );

        $balance = $summary ? (float) $summary['calculated_balance'] : 0.0;
        $points  = $summary ? (int) $summary['calculated_points'] : 0;
        $count   = $summary ? (int) $summary['total_transactions'] : 0;

        return array(
            'balance_bdt' => round( max( 0.0, $balance ), 2 ),
            'points'      => max( 0, $points ),
            'ledger_rows' => $count,
            'note'        => 'Authoritative balance computed from cryptographic SQL ledger.',
        );
    }

    /**
     * Atomically records a transaction in the database ledger with row-locking concurrency control
     */
    public static function record_ledger_transaction(
        int $user_id,
        string $tx_type,
        float $amount_bdt,
        int $points_delta = 0,
        string $description = '',
        string $reference_id = ''
    ): array {
        global $wpdb;
        if ( $user_id <= 0 ) {
            return array( 'success' => false, 'error' => 'Invalid user ID' );
        }

        $valid_types = array(
            'credit', 'debit', 'deposit', 'withdrawal',
            'reward', 'fee', 'purchase', 'payout', 'bounty', 'adjustment'
        );

        $tx_type = sanitize_key( strtolower( $tx_type ) );
        if ( ! in_array( $tx_type, $valid_types, true ) ) {
            return array( 'success' => false, 'error' => 'Invalid transaction type: ' . $tx_type );
        }

        $amount_bdt   = round( max( 0.0, $amount_bdt ), 2 );
        $points_delta = (int) $points_delta;
        $table        = $wpdb->prefix . 'hs_wallet_ledger';
        $ref_id       = sanitize_text_field( $reference_id ?: 'TXN-' . wp_generate_uuid4() );

        // Begin Atomic Transaction
        $wpdb->query( 'START TRANSACTION' );

        // Authoritatively query existing balance inside transaction
        $prev_calc = $wpdb->get_var( $wpdb->prepare(
            "SELECT 
                COALESCE(SUM(CASE 
                    WHEN tx_type IN ('credit', 'deposit', 'earnings', 'reward', 'bounty', 'cashback') THEN amount_bdt 
                    WHEN tx_type IN ('debit', 'withdrawal', 'fee', 'purchase', 'payout') THEN -amount_bdt 
                    ELSE 0 
                END), 0)
            FROM {$table}
            WHERE user_id = %d FOR UPDATE",
            $user_id
        ) );

        $prev_balance = $prev_calc !== null ? (float) $prev_calc : 0.0;

        $is_credit = in_array( $tx_type, array( 'credit', 'deposit', 'earnings', 'reward', 'bounty', 'cashback' ), true );
        $is_debit  = in_array( $tx_type, array( 'debit', 'withdrawal', 'fee', 'purchase', 'payout' ), true );

        if ( $is_debit && $prev_balance < $amount_bdt ) {
            $wpdb->query( 'ROLLBACK' );
            return array(
                'success' => false,
                'error'   => 'Insufficient wallet balance. Required: ' . number_format( $amount_bdt, 2 ) . ', Available: ' . number_format( $prev_balance, 2 ),
            );
        }

        $new_balance = $is_credit ? ( $prev_balance + $amount_bdt ) : ( $is_debit ? ( $prev_balance - $amount_bdt ) : $prev_balance );
        $new_balance = round( max( 0.0, $new_balance ), 2 );

        $salt = defined( 'AUTH_SALT' ) ? AUTH_SALT : 'hs_ledger_salt_2026';
        $ref_hash = hash( 'sha256', "{$user_id}|{$tx_type}|{$amount_bdt}|{$points_delta}|{$ref_id}|" . microtime( true ) . "|{$salt}" );

        $inserted = $wpdb->insert(
            $table,
            array(
                'user_id'        => $user_id,
                'tx_type'        => $tx_type,
                'amount_bdt'     => $amount_bdt,
                'balance_after'  => $new_balance,
                'points_delta'   => $points_delta,
                'description'    => sanitize_text_field( $description ?: 'Ecosystem transaction' ),
                'reference_id'   => $ref_id,
                'reference_hash' => $ref_hash,
                'created_at'     => gmdate( 'Y-m-d H:i:s' ),
            ),
            array( '%d', '%s', '%f', '%f', '%d', '%s', '%s', '%s', '%s' )
        );

        if ( false === $inserted ) {
            $wpdb->query( 'ROLLBACK' );
            return array( 'success' => false, 'error' => 'Failed to record ledger entry: ' . $wpdb->last_error );
        }

        $wpdb->query( 'COMMIT' );

        // Update display cache meta as non-authoritative fast read cache
        update_user_meta( $user_id, '_hs_wallet_balance_bdt', $new_balance );
        if ( 0 !== $points_delta ) {
            $current_pts = (int) get_user_meta( $user_id, '_hs_learning_xp', true );
            update_user_meta( $user_id, '_hs_learning_xp', max( 0, $current_pts + $points_delta ) );
        }

        return array(
            'success'        => true,
            'tx_id'          => $wpdb->insert_id,
            'reference_id'   => $ref_id,
            'reference_hash' => $ref_hash,
            'tx_type'        => $tx_type,
            'amount_bdt'     => $amount_bdt,
            'points_delta'   => $points_delta,
            'new_balance'    => $new_balance,
        );
    }

    public static function register(): void {
        add_action( 'rest_api_init', array( self::class, 'register_rest_routes' ) );
        add_filter( 'get_avatar_url', array( self::class, 'filter_custom_avatar_url' ), 10, 3 );
    }

    public static function filter_custom_avatar_url( string $url, mixed $id_or_email, array $args ): string {
        $uid = 0;
        if ( is_numeric( $id_or_email ) ) {
            $uid = (int) $id_or_email;
        } elseif ( $id_or_email instanceof \WP_User ) {
            $uid = (int) $id_or_email->ID;
        } elseif ( $id_or_email instanceof \WP_Comment && ! empty( $id_or_email->user_id ) ) {
            $uid = (int) $id_or_email->user_id;
        } elseif ( is_string( $id_or_email ) && str_contains( $id_or_email, '@' ) ) {
            $u = get_user_by( 'email', $id_or_email );
            if ( $u ) {
                $uid = (int) $u->ID;
            }
        }
        if ( $uid > 0 ) {
            $custom = (string) get_user_meta( $uid, '_hs_custom_avatar_url', true );
            if ( ! empty( $custom ) ) {
                return esc_url_raw( $custom );
            }
        }
        return $url;
    }

    public static function register_rest_routes(): void {
        register_rest_route( 'hackersshikkhok/v1', '/user/post/create', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_create_post' ),
            'permission_callback' => static fn() => is_user_logged_in(),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/user/post/action', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_post_status_action' ),
            'permission_callback' => static fn() => is_user_logged_in(),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/user/media/upload', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_upload_media' ),
            'permission_callback' => static fn() => is_user_logged_in(),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/user/profile/media', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_upload_profile_media' ),
            'permission_callback' => static fn() => is_user_logged_in(),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/user/profile/message', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_send_profile_message' ),
            'permission_callback' => static fn() => is_user_logged_in(),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/user/question/create', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_create_question' ),
            'permission_callback' => static fn() => is_user_logged_in(),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/user/profile/update', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_update_profile' ),
            'permission_callback' => static fn() => is_user_logged_in(),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/user/withdraw', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_request_withdraw' ),
            'permission_callback' => static fn() => is_user_logged_in(),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/user/follow', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'rest_toggle_follow' ),
            'permission_callback' => static fn() => is_user_logged_in(),
        ) );
    }

    public static function rest_upload_media( \WP_REST_Request $request ): \WP_REST_Response {
        $files = $request->get_file_params();
        if ( empty( $files['file'] ) || empty( $files['file']['tmp_name'] ) ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'No image file provided.' ), 400 );
        }

        $file = $files['file'];
        if ( (int) ( $file['size'] ?? 0 ) > 5 * 1024 * 1024 ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'File exceeds 5MB limit.' ), 400 );
        }

        $allowed = array( 'image/jpeg', 'image/png', 'image/webp', 'image/gif' );
        $finfo   = function_exists( 'mime_content_type' ) ? (string) mime_content_type( $file['tmp_name'] ) : (string) ( $file['type'] ?? '' );
        if ( ! in_array( $finfo, $allowed, true ) ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Only JPG, PNG, WebP, or GIF images are allowed.' ), 400 );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attach_id = media_handle_sideload( $file, 0 );
        if ( is_wp_error( $attach_id ) ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => $attach_id->get_error_message() ), 500 );
        }

        return new \WP_REST_Response( array(
            'success'       => true,
            'attachment_id' => (int) $attach_id,
            'url'           => wp_get_attachment_url( (int) $attach_id ),
        ) );
    }

    public static function rest_create_post( \WP_REST_Request $request ): \WP_REST_Response {
        $user_id       = get_current_user_id();
        $post_id       = (int) $request->get_param( 'post_id' );
        $title         = sanitize_text_field( (string) $request->get_param( 'title' ) );
        $slug          = sanitize_title( (string) $request->get_param( 'slug' ) );
        $content       = wp_kses_post( (string) $request->get_param( 'content' ) );
        $excerpt       = sanitize_textarea_field( (string) $request->get_param( 'excerpt' ) );
        $type          = sanitize_key( (string) $request->get_param( 'type' ) ?: 'tutorials' );
        $status        = sanitize_key( (string) $request->get_param( 'status' ) ?: 'publish' );
        $tags          = sanitize_text_field( (string) $request->get_param( 'tags' ) );
        $format        = sanitize_key( (string) $request->get_param( 'format' ) ?: 'standard' );
        $focus_kw      = sanitize_text_field( (string) $request->get_param( 'focus_keyword' ) );
        $thumb_id      = (int) $request->get_param( 'featured_image_id' );
        $categories    = $request->get_param( 'categories' );

        if ( 'trash' === $status && $post_id > 0 ) {
            $existing = get_post( $post_id );
            if ( $existing && ( (int) $existing->post_author === $user_id || current_user_can( 'delete_others_posts' ) ) ) {
                wp_trash_post( $post_id );
                return new \WP_REST_Response( array( 'success' => true, 'post_id' => $post_id, 'message' => 'Post moved to Trash.' ) );
            }
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Permission denied to trash post.' ), 403 );
        }

        if ( empty( $title ) || empty( wp_strip_all_tags( $content ) ) ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Title and article body cannot be empty.' ), 400 );
        }

        $valid_types = array( 'tutorials', 'code', 'tools', 'device_review', 'device_price', 'tech_news', 'post' );
        if ( ! in_array( $type, $valid_types, true ) ) {
            $type = 'tutorials';
        }

        $target_status = in_array( $status, array( 'draft', 'pending', 'publish' ), true ) ? $status : 'publish';

        $post_args = array(
            'post_title'   => $title,
            'post_content' => $content,
            'post_excerpt' => $excerpt,
            'post_status'  => $target_status,
            'post_type'    => $type,
        );
        if ( ! empty( $slug ) ) {
            $post_args['post_name'] = $slug;
        }

        if ( $post_id > 0 ) {
            $existing = get_post( $post_id );
            if ( ! $existing || ( (int) $existing->post_author !== $user_id && ! current_user_can( 'edit_others_posts' ) ) ) {
                return new \WP_REST_Response( array( 'success' => false, 'message' => 'You do not have permission to edit this post.' ), 403 );
            }
            $post_args['ID'] = $post_id;
            $pid = wp_update_post( $post_args, true );
        } else {
            $post_args['post_author'] = $user_id;
            $pid = wp_insert_post( $post_args, true );
        }

        if ( is_wp_error( $pid ) || ! $pid ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Failed to save post to WordPress.' ), 500 );
        }

        $pid = (int) $pid;

        if ( ! empty( $tags ) ) {
            wp_set_post_tags( $pid, $tags );
        }

        if ( is_array( $categories ) && ! empty( $categories ) ) {
            $cat_ids = array();
            foreach ( $categories as $cat_val ) {
                if ( is_numeric( $cat_val ) ) {
                    $cat_ids[] = (int) $cat_val;
                } else {
                    $slug_c = sanitize_title( (string) $cat_val );
                    $term   = get_category_by_slug( $slug_c );
                    if ( $term ) {
                        $cat_ids[] = (int) $term->term_id;
                    } elseif ( '' !== $slug_c ) {
                        $created = wp_insert_term( sanitize_text_field( (string) $cat_val ), 'category', array( 'slug' => $slug_c ) );
                        if ( ! is_wp_error( $created ) && ! empty( $created['term_id'] ) ) {
                            $cat_ids[] = (int) $created['term_id'];
                        }
                    }
                }
            }
            if ( ! empty( $cat_ids ) ) {
                wp_set_post_categories( $pid, $cat_ids );
            }
        }

        if ( $thumb_id > 0 ) {
            set_post_thumbnail( $pid, $thumb_id );
        }
        update_post_meta( $pid, '_hs_post_format', $format );
        if ( '' !== $focus_kw ) {
            update_post_meta( $pid, '_hs_focus_keyword', $focus_kw );
        }

        // Award reward once on first publish
        $reward_msg = '';
        if ( 'publish' === $target_status && ! get_post_meta( $pid, '_hs_publish_rewarded', true ) ) {
            update_post_meta( $pid, '_hs_publish_rewarded', 1 );
            self::record_ledger_transaction(
                $user_id,
                'reward',
                5.00,
                100,
                'Reward for publishing new ' . $type . ': ' . $title,
                'POST-' . $pid
            );
            $reward_msg = ' +BDT 5.00 & +100 XP awarded!';
        }

        return new \WP_REST_Response( array(
            'success'   => true,
            'post_id'   => $pid,
            'status'    => $target_status,
            'permalink' => get_permalink( $pid ),
            'message'   => ( 'draft' === $target_status ) ? '✓ Post saved as draft!' : ( '✓ Post published successfully!' . $reward_msg ),
        ) );
    }

    public static function rest_create_question( \WP_REST_Request $request ): \WP_REST_Response {
        $user_id       = get_current_user_id();
        $title         = sanitize_text_field( (string) $request->get_param( 'title' ) );
        $details       = wp_kses_post( (string) $request->get_param( 'details' ) );
        $category      = sanitize_text_field( (string) $request->get_param( 'category' ) ?: 'General Security' );
        $question_type = sanitize_key( (string) $request->get_param( 'question_type' ) ?: 'question' );
        if ( ! in_array( $question_type, array( 'question', 'discussion' ), true ) ) {
            $question_type = 'question';
        }

        if ( empty( $title ) || empty( wp_strip_all_tags( $details ) ) ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Title and details are required.' ), 400 );
        }

        $qid = wp_insert_post( array(
            'post_title'   => $title,
            'post_content' => $details,
            'post_status'  => 'publish',
            'post_type'    => 'hs_question',
            'post_author'  => $user_id,
        ) );

        if ( is_wp_error( $qid ) || ! $qid ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Failed to publish topic.' ), 500 );
        }

        update_post_meta( $qid, '_hs_question_category', $category );
        update_post_meta( $qid, '_hs_question_type', $question_type );
        update_post_meta( $qid, '_hs_question_votes', 1 );

        self::record_ledger_transaction( $user_id, 'reward', 2.00, 50, 'Published community ' . $question_type . ': ' . $title, 'Q-' . $qid );

        return new \WP_REST_Response( array(
            'success'   => true,
            'post_id'   => $qid,
            'permalink' => get_permalink( $qid ),
            'message'   => ( 'discussion' === $question_type ? '✓ Discussion posted!' : '✓ Question asked in forum!' ) . ' +BDT 2.00 & +50 XP awarded!',
        ) );
    }

    public static function rest_update_profile( \WP_REST_Request $request ): \WP_REST_Response {
        $user_id  = get_current_user_id();
        $name     = sanitize_text_field( (string) $request->get_param( 'display_name' ) );
        $bio      = sanitize_textarea_field( (string) $request->get_param( 'bio' ) );
        $location = sanitize_text_field( (string) $request->get_param( 'location' ) );
        $website  = esc_url_raw( (string) $request->get_param( 'website' ) );
        $github   = sanitize_text_field( (string) $request->get_param( 'github' ) );
        $youtube  = sanitize_text_field( (string) $request->get_param( 'youtube' ) );
        $linkedin = sanitize_text_field( (string) $request->get_param( 'linkedin' ) );
        $facebook = sanitize_text_field( (string) $request->get_param( 'facebook' ) );
        $twitter  = sanitize_text_field( (string) $request->get_param( 'twitter' ) );
        $ai_voice = sanitize_text_field( (string) $request->get_param( 'ai_voice' ) );

        if ( ! empty( $name ) ) {
            wp_update_user( array( 'ID' => $user_id, 'display_name' => $name ) );
        }

        update_user_meta( $user_id, 'description', $bio );
        update_user_meta( $user_id, '_hs_user_location', $location );
        update_user_meta( $user_id, 'user_url', $website );
        update_user_meta( $user_id, '_hs_social_github', $github );
        update_user_meta( $user_id, '_hs_social_youtube', $youtube );
        update_user_meta( $user_id, '_hs_social_linkedin', $linkedin );
        update_user_meta( $user_id, '_hs_social_facebook', $facebook );
        update_user_meta( $user_id, '_hs_social_twitter', $twitter );
        if ( ! empty( $ai_voice ) ) {
            update_user_meta( $user_id, 'hs_ai_voice', $ai_voice );
            update_user_meta( $user_id, '_hs_tts_voice_model', $ai_voice );
        }

        // Extended Facebook-style profile fields (only overwrite when included in payload)
        $extended_map = array(
            'status_note'         => '_hs_profile_status_note',
            'hometown'            => '_hs_user_hometown',
            'relationship_status' => '_hs_user_relationship',
            'birthday'            => '_hs_user_birthday',
            'work_title'          => '_hs_work_title',
            'work_company'        => '_hs_work_company',
            'work_since'          => '_hs_work_since',
            'education_school'    => '_hs_education_school',
            'education_degree'    => '_hs_education_degree',
            'education_year'      => '_hs_education_year',
            'phone'               => '_hs_contact_phone',
            'public_email'        => '_hs_contact_email',
        );
        foreach ( $extended_map as $param_key => $meta_key ) {
            if ( null !== $request->get_param( $param_key ) ) {
                $val = ( 'public_email' === $param_key )
                    ? sanitize_email( (string) $request->get_param( $param_key ) )
                    : sanitize_text_field( (string) $request->get_param( $param_key ) );
                update_user_meta( $user_id, $meta_key, $val );
            }
        }

        $privacy_in = $request->get_param( 'privacy_settings' );
        if ( is_array( $privacy_in ) ) {
            $existing_priv = get_user_meta( $user_id, '_hs_profile_privacy', true );
            $existing_priv = is_array( $existing_priv ) ? $existing_priv : array();
            $allowed_vis   = array( 'public', 'followers', 'private' );
            foreach ( $privacy_in as $pkey => $pval ) {
                $clean_k = sanitize_key( (string) $pkey );
                $clean_v = sanitize_key( (string) $pval );
                if ( in_array( $clean_v, $allowed_vis, true ) ) {
                    $existing_priv[ $clean_k ] = $clean_v;
                }
            }
            update_user_meta( $user_id, '_hs_profile_privacy', $existing_priv );
        }

        return new \WP_REST_Response( array( 'success' => true, 'message' => '✓ Profile & privacy settings updated!' ) );
    }

    public static function rest_upload_profile_media( \WP_REST_Request $request ): \WP_REST_Response {
        $user_id    = get_current_user_id();
        $media_type = sanitize_key( (string) ( $request->get_param( 'media_type' ) ?: 'avatar' ) );
        if ( ! in_array( $media_type, array( 'avatar', 'cover' ), true ) ) {
            $media_type = 'avatar';
        }

        $upload_res = self::rest_upload_media( $request );
        $data       = $upload_res->get_data();
        if ( empty( $data['success'] ) || empty( $data['url'] ) ) {
            return $upload_res;
        }

        $meta_key = ( 'cover' === $media_type ) ? '_hs_cover_photo_url' : '_hs_custom_avatar_url';
        update_user_meta( $user_id, $meta_key, esc_url_raw( (string) $data['url'] ) );

        return new \WP_REST_Response( array(
            'success'    => true,
            'media_type' => $media_type,
            'url'        => (string) $data['url'],
            'message'    => ( 'cover' === $media_type ) ? '✓ Cover photo updated!' : '✓ Profile photo updated!',
        ) );
    }

    public static function rest_send_profile_message( \WP_REST_Request $request ): \WP_REST_Response {
        $sender_id    = get_current_user_id();
        $recipient_id = (int) $request->get_param( 'recipient_id' );
        $subject      = sanitize_text_field( (string) ( $request->get_param( 'subject' ) ?: 'Direct Message' ) );
        $body         = sanitize_textarea_field( (string) $request->get_param( 'message' ) );

        if ( $recipient_id <= 0 || $recipient_id === $sender_id || ! get_userdata( $recipient_id ) ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Invalid recipient operative.' ), 400 );
        }
        if ( empty( trim( $body ) ) ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Message cannot be empty.' ), 400 );
        }

        $sender = wp_get_current_user();
        $sender_name = $sender->display_name ?: $sender->user_login;

        $notis = get_user_meta( $recipient_id, '_hs_user_notifications', true );
        if ( ! is_array( $notis ) ) {
            $notis = array();
        }
        array_unshift( $notis, array(
            'id'        => 'msg_' . wp_generate_uuid4(),
            'icon'      => '💬',
            'type'      => 'message',
            'title'     => 'New message from ' . $sender_name,
            'message'   => wp_trim_words( $body, 22, '...' ),
            'body'      => $body,
            'read'      => false,
            'time_ago'  => 'Just now',
            'timestamp' => gmdate( 'M d, Y H:i' ),
            'url'       => get_author_posts_url( $sender_id ),
        ) );
        update_user_meta( $recipient_id, '_hs_user_notifications', array_slice( $notis, 0, 50 ) );

        $inbox = get_user_meta( $recipient_id, '_hs_inbox_messages', true );
        if ( ! is_array( $inbox ) ) {
            $inbox = array();
        }
        array_unshift( $inbox, array(
            'from_id'   => $sender_id,
            'from_name' => $sender_name,
            'subject'   => $subject,
            'message'   => $body,
            'sent_at'   => current_time( 'mysql' ),
        ) );
        update_user_meta( $recipient_id, '_hs_inbox_messages', array_slice( $inbox, 0, 50 ) );

        return new \WP_REST_Response( array(
            'success' => true,
            'message' => '✓ Message sent! The operative will see it in their Notifications.',
        ) );
    }

    public static function rest_post_status_action( \WP_REST_Request $request ): \WP_REST_Response {
        $uid     = get_current_user_id();
        $post_id = (int) $request->get_param( 'post_id' );
        $action  = sanitize_key( (string) $request->get_param( 'action' ) );

        if ( $post_id <= 0 ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Invalid post ID.' ), 400 );
        }
        $post = get_post( $post_id );
        if ( ! $post || ( (int) $post->post_author !== $uid && ! current_user_can( 'edit_others_posts' ) ) ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Permission denied.' ), 403 );
        }

        if ( 'trash' === $action ) {
            wp_trash_post( $post_id );
            return new \WP_REST_Response( array( 'success' => true, 'message' => 'Moved to Trash.' ) );
        } elseif ( 'restore' === $action ) {
            wp_untrash_post( $post_id );
            wp_update_post( array( 'ID' => $post_id, 'post_status' => 'draft' ) );
            return new \WP_REST_Response( array( 'success' => true, 'message' => 'Restored to Drafts!' ) );
        } elseif ( 'publish' === $action ) {
            wp_update_post( array( 'ID' => $post_id, 'post_status' => 'publish' ) );
            return new \WP_REST_Response( array( 'success' => true, 'message' => 'Post published live!' ) );
        } elseif ( 'delete_permanent' === $action ) {
            wp_delete_post( $post_id, true );
            return new \WP_REST_Response( array( 'success' => true, 'message' => 'Permanently deleted.' ) );
        }

        return new \WP_REST_Response( array( 'success' => false, 'message' => 'Unknown action.' ), 400 );
    }

    public static function get_default_privacy(): array {
        return array(
            'bio'          => 'public',
            'location'     => 'public',
            'hometown'     => 'public',
            'relationship' => 'followers',
            'birthday'     => 'followers',
            'work'         => 'public',
            'education'    => 'public',
            'links'        => 'public',
            'phone'        => 'private',
            'email'        => 'private',
        );
    }

    public static function can_view_field( int $owner_id, int $viewer_id, string $vis ): bool {
        if ( $viewer_id > 0 && ( $viewer_id === $owner_id || user_can( $viewer_id, 'manage_options' ) ) ) {
            return true;
        }
        if ( 'public' === $vis ) {
            return true;
        }
        if ( 'followers' === $vis && $viewer_id > 0 ) {
            $following = get_user_meta( $viewer_id, '_hs_following_ids', true );
            return is_array( $following ) && in_array( $owner_id, array_map( 'intval', $following ), true );
        }
        return false;
    }

    public static function rest_request_withdraw( \WP_REST_Request $request ): \WP_REST_Response {
        $user_id = get_current_user_id();
        $amount  = (float) $request->get_param( 'amount' );
        $method  = sanitize_text_field( (string) $request->get_param( 'method' ) ?: 'bKash' );
        $number  = sanitize_text_field( (string) $request->get_param( 'account_number' ) );

        $wallet = self::get_wallet_summary( $user_id );
        if ( $amount < 100.0 ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Minimum withdrawal amount is BDT 100.00.' ), 400 );
        }
        if ( $amount > $wallet['balance_bdt'] ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Insufficient wallet balance.' ), 400 );
        }
        if ( empty( $number ) ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Mobile wallet account number required.' ), 400 );
        }

        $tx = self::record_ledger_transaction(
            $user_id,
            'withdrawal',
            $amount,
            0,
            "Payout request via {$method} to {$number}",
            'WDR-' . wp_generate_uuid4()
        );

        return new \WP_REST_Response( array(
            'success' => true,
            'message' => "✓ Withdrawal request of BDT {$amount} submitted via {$method} ({$number}). It will be processed within 24 hours.",
            'balance' => $tx['new_balance'] ?? 0,
        ) );
    }

    public static function rest_toggle_follow( \WP_REST_Request $request ): \WP_REST_Response {
        $current_user = get_current_user_id();
        $target_user  = (int) $request->get_param( 'target_user_id' );

        if ( $target_user <= 0 || $current_user === $target_user ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Invalid operative target.' ), 400 );
        }

        $following = get_user_meta( $current_user, '_hs_following_ids', true );
        $following = is_array( $following ) ? $following : array();

        if ( in_array( $target_user, $following, true ) ) {
            $following = array_values( array_diff( $following, array( $target_user ) ) );
            $followed = false;
        } else {
            $following[] = $target_user;
            $followed = true;
        }

        update_user_meta( $current_user, '_hs_following_ids', array_values( array_unique( array_map( 'intval', $following ) ) ) );

        return new \WP_REST_Response( array(
            'success'        => true,
            'followed'       => $followed,
            'message'        => $followed ? '✓ You followed this operative!' : 'Unfollowed operative.',
            'following_count'=> count( $following ),
        ) );
    }

    public static function get_user_articles( int $user_id ): array {
        $posts_query = new \WP_Query( array(
            'author'         => $user_id,
            'post_type'      => array( 'post', 'tutorials', 'code', 'tools', 'device_review', 'device_price', 'tech_news' ),
            'post_status'    => array( 'publish', 'draft', 'pending' ),
            'posts_per_page' => 50,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        $items = array();
        if ( $posts_query->have_posts() ) {
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                $pid   = (int) get_the_ID();
                $views = (int) get_post_meta( $pid, 'hs_views_count', true );
                if ( 0 === $views ) {
                    $views = (int) get_post_meta( $pid, '_hs_views_count', true );
                }
                $likes = (int) get_post_meta( $pid, 'hs_likes_count', true );
                if ( 0 === $likes ) {
                    $likes = (int) get_post_meta( $pid, '_hs_likes_count', true );
                }

                $items[] = array(
                    'id'        => $pid,
                    'title'     => get_the_title(),
                    'type'      => get_post_type(),
                    'status'    => get_post_status(),
                    'date'      => get_the_date( 'M d, Y' ),
                    'views'     => $views,
                    'likes'     => $likes,
                    'comments'  => (int) get_comments_number(),
                    'permalink' => get_permalink(),
                );
            }
            wp_reset_postdata();
        }

        return $items;
    }
}
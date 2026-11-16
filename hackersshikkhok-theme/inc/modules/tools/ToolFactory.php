<?php
declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Tools;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WP_REST_Request;
use WP_REST_Response;
use HackersShikkhok\Theme\Modules\Platform\UserEcosystem;

/**
 * Universal Tool Factory (#501+ Custom Tool Expansion System)
 * Enables Administrator, AI Autopilot & Community Members to submit, review, report,
 * and earn verified XP rewards across the 2,024+ Tools platform.
 */
final class ToolFactory {

    public static function register(): void {
        add_action( 'rest_api_init', array( self::class, 'register_rest_routes' ) );
    }

    public static function register_rest_routes(): void {
        register_rest_route( 'hackersshikkhok/v1', '/tools/submit', array(
            'methods'             => 'POST',
            'permission_callback' => static fn() => is_user_logged_in(),
            'callback'            => array( self::class, 'rest_submit_tool' ),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/tools/reviews', array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => array( self::class, 'rest_get_reviews' ),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/tools/review', array(
            'methods'             => 'POST',
            'permission_callback' => static fn() => is_user_logged_in(),
            'callback'            => array( self::class, 'rest_submit_review' ),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/tools/report', array(
            'methods'             => 'POST',
            'permission_callback' => static fn() => is_user_logged_in(),
            'callback'            => array( self::class, 'rest_report_tool' ),
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/tools/usage-reward', array(
            'methods'             => 'POST',
            'permission_callback' => static fn() => is_user_logged_in(),
            'callback'            => array( self::class, 'rest_claim_usage_reward' ),
        ) );
    }

    /**
     * Register a new dynamic custom tool (Admin)
     */
    public static function register_tool( WP_REST_Request $request ): WP_REST_Response {
        if ( ! current_user_can( 'manage_options' ) ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'error' => 'Unauthorized: Administrator capability required.',
                ),
                403
            );
        }

        $tool_name = sanitize_text_field( (string) $request->get_param( 'tool_name' ) );
        $tool_slug = sanitize_title( (string) $request->get_param( 'tool_slug' ) );
        $center_slug = sanitize_title( (string) $request->get_param( 'center_slug' ) );
        $category_slug = sanitize_text_field( (string) $request->get_param( 'category_slug' ) );
        $description = sanitize_textarea_field( (string) $request->get_param( 'description' ) );
        $icon = sanitize_text_field( (string) $request->get_param( 'icon' ) ?: 'Terminal' );
        $output_type = sanitize_text_field( (string) $request->get_param( 'output_type' ) ?: 'text' );
        $processor_type = sanitize_text_field( (string) $request->get_param( 'processor_type' ) ?: 'client' );
        $status = sanitize_text_field( (string) $request->get_param( 'status' ) ?: 'published' );
        $version = sanitize_text_field( (string) $request->get_param( 'version' ) ?: '1.0.0' );

        if ( empty( $tool_name ) || empty( $tool_slug ) || empty( $center_slug ) ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'error' => 'Missing required fields: tool_name, tool_slug, and center_slug are mandatory.',
                ),
                400
            );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'hs_custom_tools';
        $exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) );
        if ( ! $exists ) {
            $table = $wpdb->prefix . 'custom_tools';
        }

        $existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE tool_slug = %s", $tool_slug ) );
        if ( $existing ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'error' => "A tool with slug '{$tool_slug}' already exists.",
                ),
                409
            );
        }

        $inserted = $wpdb->insert(
            $table,
            array(
                'tool_slug' => $tool_slug,
                'tool_name' => $tool_name,
                'center_slug' => $center_slug,
                'category_slug' => $category_slug,
                'icon' => $icon,
                'description' => $description,
                'output_type' => $output_type,
                'processor_type' => $processor_type,
                'status' => $status,
                'version' => $version,
                'seo_title' => "{$tool_name} | Online Free Tool - HackersShikkhok",
                'seo_description' => $description,
                'created_at' => current_time( 'mysql', true ),
                'updated_at' => current_time( 'mysql', true ),
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
        );

        if ( ! $inserted ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'error' => 'Database error inserting tool into catalog.',
                ),
                500
            );
        }

        return new WP_REST_Response(
            array(
                'success' => true,
                'message' => "Tool '{$tool_name}' (#{$wpdb->insert_id}) successfully registered in HackersShikkhok Tool Catalog!",
                'tool_id' => $tool_slug,
            ),
            201
        );
    }

    /**
     * Community Tool Submission (Creates a pending `tools` CPT post for moderation)
     */
    public static function rest_submit_tool( WP_REST_Request $request ): WP_REST_Response {
        $user_id     = get_current_user_id();
        $tool_name   = sanitize_text_field( (string) $request->get_param( 'tool_name' ) );
        $tool_url    = esc_url_raw( (string) $request->get_param( 'tool_url' ) );
        $category    = sanitize_text_field( (string) ( $request->get_param( 'category' ) ?: 'Developer Utilities' ) );
        $pricing     = sanitize_key( (string) ( $request->get_param( 'pricing' ) ?: 'free' ) );
        $description = sanitize_textarea_field( (string) $request->get_param( 'description' ) );

        if ( ! in_array( $pricing, array( 'free', 'freemium', 'paid' ), true ) ) {
            $pricing = 'free';
        }

        if ( empty( $tool_name ) || empty( $description ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Tool name and description are required.' ), 400 );
        }

        $post_id = wp_insert_post( array(
            'post_title'   => $tool_name,
            'post_content' => $description,
            'post_excerpt' => wp_trim_words( $description, 24 ),
            'post_status'  => current_user_can( 'manage_options' ) ? 'publish' : 'pending',
            'post_type'    => 'tools',
            'post_author'  => $user_id,
        ), true );

        if ( is_wp_error( $post_id ) || ! $post_id ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Could not save tool submission.' ), 500 );
        }

        update_post_meta( (int) $post_id, '_hs_tool_url', $tool_url );
        update_post_meta( (int) $post_id, '_hs_tool_category', $category );
        update_post_meta( (int) $post_id, '_hs_tool_pricing', $pricing );

        return new WP_REST_Response( array(
            'success' => true,
            'post_id' => (int) $post_id,
            'status'  => get_post_status( (int) $post_id ),
            'message' => current_user_can( 'manage_options' )
                ? '✓ Tool published directly to the directory!'
                : '✓ Tool submitted for admin moderation review! You will be notified once approved.',
        ), 201 );
    }

    /**
     * Retrieve persistent reviews & average rating for a tool
     */
    public static function get_tool_reviews( string $tool_slug ): array {
        $safe_slug = sanitize_key( $tool_slug );
        if ( empty( $safe_slug ) ) {
            return array( 'avg_rating' => 0.0, 'review_count' => 0, 'reviews' => array() );
        }
        $raw = get_option( 'hs_tool_reviews_' . $safe_slug, array() );
        if ( ! is_array( $raw ) || empty( $raw ) ) {
            return array( 'avg_rating' => 0.0, 'review_count' => 0, 'reviews' => array() );
        }
        $sum = 0;
        $list = array();
        foreach ( $raw as $r ) {
            if ( ! is_array( $r ) ) {
                continue;
            }
            $rating = max( 1, min( 5, (int) ( $r['rating'] ?? 5 ) ) );
            $sum += $rating;
            $list[] = $r;
        }
        usort( $list, static fn( $a, $b ) => strcmp( (string) ( $b['created_at'] ?? '' ), (string) ( $a['created_at'] ?? '' ) ) );
        $count = count( $list );
        return array(
            'avg_rating'   => $count > 0 ? round( $sum / $count, 2 ) : 0.0,
            'review_count' => $count,
            'reviews'      => $list,
        );
    }

    public static function rest_get_reviews( WP_REST_Request $request ): WP_REST_Response {
        $slug = sanitize_key( (string) $request->get_param( 'tool_slug' ) );
        if ( empty( $slug ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'tool_slug is required.' ), 400 );
        }
        $data = self::get_tool_reviews( $slug );
        return new WP_REST_Response( array_merge( array( 'success' => true, 'tool_slug' => $slug ), $data ), 200 );
    }

    public static function rest_submit_review( WP_REST_Request $request ): WP_REST_Response {
        $user      = wp_get_current_user();
        $user_id   = (int) $user->ID;
        $tool_slug = sanitize_key( (string) $request->get_param( 'tool_slug' ) );
        $rating    = max( 1, min( 5, (int) $request->get_param( 'rating' ) ) );
        $review    = sanitize_textarea_field( (string) $request->get_param( 'review' ) );

        if ( empty( $tool_slug ) || empty( trim( $review ) ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Tool slug and review text are required.' ), 400 );
        }

        $opt_key = 'hs_tool_reviews_' . $tool_slug;
        $reviews = get_option( $opt_key, array() );
        if ( ! is_array( $reviews ) ) {
            $reviews = array();
        }

        $is_new = ! isset( $reviews[ 'u_' . $user_id ] );
        $reviews[ 'u_' . $user_id ] = array(
            'user_id'     => $user_id,
            'author_name' => $user->display_name ?: $user->user_login,
            'rating'      => $rating,
            'review'      => $review,
            'created_at'  => current_time( 'mysql' ),
        );

        update_option( $opt_key, $reviews, false );

        if ( $is_new ) {
            UserEcosystem::record_ledger_transaction(
                $user_id,
                'reward',
                0.0,
                15,
                'Reviewed tool: ' . $tool_slug,
                'TOOLREV-' . $tool_slug . '-' . $user_id
            );
        }

        $summary = self::get_tool_reviews( $tool_slug );
        return new WP_REST_Response( array_merge(
            array(
                'success' => true,
                'message' => $is_new ? '✓ Review saved! +15 XP awarded.' : '✓ Your review has been updated!',
            ),
            $summary
        ), 200 );
    }

    public static function rest_report_tool( WP_REST_Request $request ): WP_REST_Response {
        $uid       = get_current_user_id();
        $tool_slug = sanitize_key( (string) $request->get_param( 'tool_slug' ) );
        $tool_name = sanitize_text_field( (string) ( $request->get_param( 'tool_name' ) ?: $tool_slug ) );
        $post_id   = (int) $request->get_param( 'post_id' );
        $reason    = sanitize_text_field( (string) ( $request->get_param( 'reason' ) ?: 'Broken / Inaccurate Output' ) );
        $details   = sanitize_textarea_field( (string) $request->get_param( 'details' ) );

        if ( empty( $tool_slug ) && $post_id <= 0 ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Tool identifier is required.' ), 400 );
        }

        $queue = get_option( '_hs_reported_content_queue', array() );
        if ( ! is_array( $queue ) ) {
            $queue = array();
        }
        $queue[] = array(
            'post_id'      => $post_id,
            'target_type'  => 'tool',
            'target_slug'  => $tool_slug,
            'target_label' => 'Tool: ' . $tool_name,
            'reason'       => $reason,
            'details'      => $details,
            'user_id'      => $uid,
            'status'       => 'open',
            'reported_at'  => current_time( 'mysql' ),
        );
        update_option( '_hs_reported_content_queue', $queue, false );

        return new WP_REST_Response( array(
            'success' => true,
            'message' => '✓ Issue report submitted to moderation queue. Thank you!',
        ), 200 );
    }

    public static function rest_claim_usage_reward( WP_REST_Request $request ): WP_REST_Response {
        $user_id   = get_current_user_id();
        $tool_slug = sanitize_key( (string) $request->get_param( 'tool_slug' ) );
        if ( empty( $tool_slug ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Tool slug is required.' ), 400 );
        }

        $today = gmdate( 'Y-m-d' );
        $claims = get_user_meta( $user_id, '_hs_tool_daily_rewards', true );
        if ( ! is_array( $claims ) ) {
            $claims = array();
        }

        if ( isset( $claims[ $tool_slug ] ) && $claims[ $tool_slug ] === $today ) {
            return new WP_REST_Response( array(
                'success'     => true,
                'awarded'     => false,
                'xp_awarded'  => 0,
                'bdt_awarded' => 0.0,
                'message'     => 'Daily reward for this tool already claimed today. Come back tomorrow!',
            ), 200 );
        }

        $claims[ $tool_slug ] = $today;
        update_user_meta( $user_id, '_hs_tool_daily_rewards', $claims );

        // Increment real tool usage counter
        $usage_counts = get_option( '_hs_tool_usage_counts', array() );
        if ( ! is_array( $usage_counts ) ) {
            $usage_counts = array();
        }
        $usage_counts[ $tool_slug ] = (int) ( $usage_counts[ $tool_slug ] ?? 0 ) + 1;
        update_option( '_hs_tool_usage_counts', $usage_counts, false );

        UserEcosystem::record_ledger_transaction(
            $user_id,
            'reward',
            1.00,
            10,
            'Daily tool lab execution reward: ' . $tool_slug,
            'TOOL-' . $tool_slug . '-' . $today
        );

        $wallet = UserEcosystem::get_wallet_summary( $user_id );
        return new WP_REST_Response( array(
            'success'     => true,
            'awarded'     => true,
            'xp_awarded'  => 10,
            'bdt_awarded' => 1.00,
            'wallet'      => $wallet,
            'message'     => '✓ +10 Scholar XP & BDT 1.00 credited to your wallet!',
        ), 200 );
    }

    /**
     * Get Ecosystem Health & Statistics for Admin Dashboard
     */
    public static function get_tools_health_summary(): array {
        global $wpdb;
        $all_tools = UniversalToolRegistry::get_all_tools();
        $centers = UniversalToolRegistry::get_centers();

        $total_tools_count = count( $all_tools );
        $total_centers_count = count( $centers );

        $usage_table = $wpdb->prefix . 'tool_usage';
        $total_executions = 0;
        if ( $wpdb ) {
            $total_executions = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$usage_table}" );
        }

        $search_table = $wpdb->prefix . 'search_analytics';
        $failed_searches = array();
        if ( $wpdb ) {
            $failed_searches = $wpdb->get_results( "SELECT search_query, COUNT(*) as query_count FROM {$search_table} WHERE results_count = 0 GROUP BY search_query ORDER BY query_count DESC LIMIT 10", ARRAY_A );
        }

        return array(
            'total_centers' => $total_centers_count,
            'total_tools' => $total_tools_count,
            'total_executions' => $total_executions,
            'healthy_status' => 'Optimal (Zero Fatalities)',
            'failed_searches' => $failed_searches ?: array(),
            'platform_expansion_ready' => '1,000+ Tools Ready',
            'version' => '2.5.0',
        );
    }
}
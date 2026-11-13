<?php
/**
 * Platform Hub Engine - Multi-Studio App Generator & Interactive Creator Center
 *
 * @package HackersShikkhok\Core\Centers
 */

namespace HackersShikkhok\Theme\Modules\Platform;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PlatformHubEngine {

    public static function register(): void {
        add_action( 'rest_api_init', array( self::class, 'register_rest_routes' ) );
        add_shortcode( 'hs_platform_center_hub', array( self::class, 'render_platform_hub' ) );
        add_shortcode( 'hs_center_hub', array( self::class, 'render_platform_hub' ) );
    }

    public static function register_rest_routes(): void {
        register_rest_route( 'hackersshikkhok/v1', '/platform-hub/create-app', array(
            'methods'             => 'POST',
            'callback'            => array( self::class, 'handle_create_app' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'hackersshikkhok/v1', '/platform-hub/list', array(
            'methods'             => 'GET',
            'callback'            => array( self::class, 'handle_list_studios' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public static function handle_create_app( \WP_REST_Request $request ): \WP_REST_Response {
        $nonce = $request->get_header( 'X-WP-Nonce' );
        if ( ! empty( $nonce ) && ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_REST_Response( array( 'error' => 'Invalid security token' ), 403 );
        }

        $app_name    = sanitize_text_field( $request->get_param( 'app_name' ) ?? 'Hackers Web App' );
        $app_url     = esc_url_raw( $request->get_param( 'app_url' ) ?? 'https://hackersshikkhok.com' );
        $package_id  = sanitize_text_field( $request->get_param( 'package_id' ) ?? 'com.hackersshikkhok.app' );
        $studio_type = sanitize_text_field( $request->get_param( 'studio_type' ) ?? 'web-to-app' );

        $post_id = wp_insert_post( array(
            'post_title'   => $app_name,
            'post_content' => wp_kses_post( "Generated via HackersShikkhok Platform Hub: {$studio_type} for {$app_url}" ),
            'post_status'  => 'publish',
            'post_type'    => 'hs_generated_app',
        ) );

        if ( is_wp_error( $post_id ) ) {
            return new \WP_REST_Response( array( 'error' => $post_id->get_error_message() ), 500 );
        }

        update_post_meta( $post_id, '_hs_target_url', $app_url );
        update_post_meta( $post_id, '_hs_package_id', $package_id );
        update_post_meta( $post_id, '_hs_studio_type', $studio_type );

        return new \WP_REST_Response( array(
            'success'     => true,
            'post_id'     => $post_id,
            'app_name'    => $app_name,
            'package_id'  => $package_id,
            'download_url'=> home_url( "/app-maker/?export={$post_id}" ),
            'message'     => 'App generated successfully!',
        ), 200 );
    }

    public static function handle_list_studios( \WP_REST_Request $request ): \WP_REST_Response {
        $studios = self::get_studios_data();
        return new \WP_REST_Response( array(
            'success' => true,
            'studios' => $studios,
        ), 200 );
    }

    public static function get_studios_data(): array {
        return array(
            array(
                'slug'        => 'web-to-app',
                'title'       => 'Web to Android APK Builder',
                'title_bn'    => 'ওয়েব টু অ্যান্ড্রয়েড অ্যাপ বিল্ডার',
                'description' => 'Convert any responsive website or URL into a standalone Android APK and AAB package with offline caching.',
                'icon'        => '📱',
                'badge'       => 'HOT',
                'color'       => '#00f5d4',
                'url'         => home_url( '/app-maker/?studio=web-to-app' ),
                'btn_text'    => 'Launch App Maker',
            ),
            array(
                'slug'        => 'link-to-pwa',
                'title'       => 'Progressive Web App (PWA) Studio',
                'title_bn'    => 'প্রগ্রেসিভ ওয়েব অ্যাপ স্টুডিও',
                'description' => 'Generate installable PWA manifest, service workers, icon badges, and offline fallbacks with 1-click.',
                'icon'        => '🌐',
                'badge'       => 'PWA 3.0',
                'color'       => '#a78bfa',
                'url'         => home_url( '/app-maker/?studio=pwa' ),
                'btn_text'    => 'Create PWA App',
            ),
            array(
                'slug'        => 'card-maker',
                'title'       => 'Cyber Greeting & Certificate Maker',
                'title_bn'    => 'সাইবার গ্রিটিংস ও সোশ্যাল কার্ড মেকার',
                'description' => 'Design vector cyber greeting cards, achievement badges, and cryptographic certificate cards.',
                'icon'        => '🎴',
                'badge'       => 'DESIGN',
                'color'       => '#ff3366',
                'url'         => home_url( '/app-maker/?studio=card-maker' ),
                'btn_text'    => 'Design Cyber Card',
            ),
            array(
                'slug'        => 'gift-maker',
                'title'       => '3D Interactive Gift Box & CTF Mystery Unit',
                'title_bn'    => '৩ডি ইন্টারঅ্যাক্টিভ গিফট ও রিওয়ার্ড বক্স',
                'description' => 'Create 3D interactive surprise units with embedded CTF flag puzzles, secret keys, and confetti bursts.',
                'icon'        => '🎁',
                'badge'       => 'REWARDS',
                'color'       => '#f59e0b',
                'url'         => home_url( '/app-maker/?studio=gift-maker' ),
                'btn_text'    => 'Generate 3D Gift',
            ),
            array(
                'slug'        => 'game-maker',
                'title'       => 'HTML5 Cyber Game & Canvas Sandbox',
                'title_bn'    => 'এইচটিএমএল৫ সাইবার সিকিউরিটি গেম স্টুডিও',
                'description' => 'Build cipher breaker games, terminal hacking challenges, and memory defense games.',
                'icon'        => '🕹️',
                'badge'       => 'CANVAS',
                'color'       => '#10b981',
                'url'         => home_url( '/app-maker/?studio=game-maker' ),
                'btn_text'    => 'Launch Game Studio',
            ),
            array(
                'slug'        => 'command-matrix',
                'title'       => 'Cyber Command Central Matrix',
                'title_bn'    => 'সাইবার কমান্ড ও সিকিউরিটি অপারেশন মেট্রিক্স',
                'description' => 'Live tactical security audit HUD, continuous website monitor, automated web auditor, and vulnerability ledger.',
                'icon'        => '💻',
                'badge'       => 'SAAS v2',
                'color'       => '#00f5d4',
                'url'         => home_url( '/cyber-command/' ),
                'btn_text'    => 'Open Command Center',
            ),
        );
    }

    public static function render_platform_hub(): string {
        $studios = self::get_studios_data();
        ob_start();
        ?>
        <div id="hackers-platform-hub-root" class="hs-platform-hub-container" style="background:#080e1e; border:1px solid rgba(0,245,212,0.3); border-radius:20px; padding:32px 24px; margin:20px 0;">
            <div style="text-align:center; margin-bottom:30px;">
                <div style="display:inline-block; padding:4px 14px; background:rgba(0,245,212,0.12); border:1px solid rgba(0,245,212,0.35); border-radius:999px; color:#00f5d4; font-size:12px; font-weight:800; letter-spacing:1px; margin-bottom:12px;">
                    🚀 MULTI-STUDIO CREATOR SUITE
                </div>
                <h2 style="font-size:28px; font-weight:900; color:#fff; margin-bottom:8px;">
                    Multi-Platform Creator Studios &amp; Tools
                </h2>
                <p style="color:#94a3b8; font-size:14px; max-width:680px; margin:0 auto;">
                    Select an autonomous studio below to build web-to-app packages, PWAs, 3D interactive rewards, or launch tactical security operations.
                </p>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
                <?php foreach ( $studios as $s ) : ?>
                    <div class="hs-studio-card" style="background:#0b1120; border:1px solid rgba(255,255,255,0.08); border-top:3px solid <?php echo esc_attr( $s['color'] ); ?>; border-radius:16px; padding:24px; display:flex; flex-direction:column; justify-content:space-between; transition:transform 0.2s, border-color 0.2s; box-shadow:0 8px 24px rgba(0,0,0,0.4);">
                        <div>
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                                <span style="font-size:32px; filter:drop-shadow(0 0 8px <?php echo esc_attr( $s['color'] ); ?>);"><?php echo esc_html( $s['icon'] ); ?></span>
                                <span style="background:rgba(255,255,255,0.06); color:<?php echo esc_attr( $s['color'] ); ?>; border:1px solid <?php echo esc_attr( $s['color'] ); ?>; font-size:10px; font-weight:800; padding:2px 8px; border-radius:6px; letter-spacing:0.5px;">
                                    <?php echo esc_html( $s['badge'] ); ?>
                                </span>
                            </div>
                            <h3 style="color:#fff; font-size:1.15rem; font-weight:800; margin-bottom:8px; line-height:1.3;">
                                <?php echo esc_html( $s['title'] ); ?>
                            </h3>
                            <div style="color:<?php echo esc_attr( $s['color'] ); ?>; font-size:12px; font-weight:600; margin-bottom:10px;">
                                <?php echo esc_html( $s['title_bn'] ); ?>
                            </div>
                            <p style="color:#94a3b8; font-size:13px; line-height:1.5; margin-bottom:20px;">
                                <?php echo esc_html( $s['description'] ); ?>
                            </p>
                        </div>
                        <a href="<?php echo esc_url( $s['url'] ); ?>" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:10px 18px; background:<?php echo esc_attr( $s['color'] ); ?>; color:#050811; font-weight:800; font-size:13px; border-radius:10px; text-decoration:none; transition:opacity 0.2s, box-shadow 0.2s;">
                            <span><?php echo esc_html( $s['btn_text'] ); ?></span>
                            <span>➔</span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }
}
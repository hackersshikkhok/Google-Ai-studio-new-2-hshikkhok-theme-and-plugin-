<?php
declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Platform;

final class CenterManager {
    public static function register(): void {
        add_shortcode( 'hs_center_hub', array( self::class, 'render_center_shortcode' ) );
        if ( class_exists( __NAMESPACE__ . '\\PlatformHubEngine' ) ) {
            PlatformHubEngine::register();
        } elseif ( file_exists( __DIR__ . '/PlatformHubEngine.php' ) ) {
            require_once __DIR__ . '/PlatformHubEngine.php';
            PlatformHubEngine::register();
        }
    }

    public static function get_all_centers(): array {
        return array(
            'home', 'tutorials', 'cyber', 'coding', 'code', 'css-rgb-lab',
            'tools', 'live-demo', 'creator-studio-hub', 'device-lab', 'games',
            'projects', 'troubleshooting', 'youtube', 'community', 'forum',
            'ai-center', 'courses', 'certificates', 'resources', 'dashboard',
            'profile', 'creator-studio', 'pdf-studio', 'bd-calendar',
            'cyber-labs', 'wp-studio', 'qa-audit', 'platform-hub', 'app-maker',
            'card-maker', 'gift-maker', 'game-maker'
        );
    }

    public static function render_center_shortcode( array $atts = array() ): string {
        if ( class_exists( __NAMESPACE__ . '\\PlatformHubEngine' ) ) {
            return PlatformHubEngine::render_platform_hub();
        }
        $atts = shortcode_atts( array( 'center' => 'home' ), $atts, 'hs_center_hub' );
        $center = sanitize_key( $atts['center'] );
        return sprintf( '<div class="hs-center-container" data-center="%s"></div>', esc_attr( $center ) );
    }
}

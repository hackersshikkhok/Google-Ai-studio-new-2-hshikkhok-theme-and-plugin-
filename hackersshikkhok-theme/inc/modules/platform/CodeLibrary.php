<?php
declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Platform;

final class CodeLibrary {
    public static function register(): void {
        add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue_conditional_assets' ) );
    }

    public static function enqueue_conditional_assets(): void {
        if ( is_singular( 'code' ) || is_page_template( 'page-tools-lab.php' ) ) {
            wp_enqueue_style( 'hs-code-library', HS_THEME_URI . '/assets/css/code-library.css', array(), HS_THEME_VERSION );
            wp_enqueue_script( 'hs-code-editor', HS_THEME_URI . '/assets/js/code-editor.js', array(), HS_THEME_VERSION, true );
        }
    }
}

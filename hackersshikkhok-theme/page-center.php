<?php
/**
 * Template Name: HackersShikkhok Platform Center
 */
declare(strict_types=1);
get_header();
if ( class_exists( '\HackersShikkhok\Theme\Modules\Platform\PlatformHubEngine' ) ) {
    echo \HackersShikkhok\Theme\Modules\Platform\PlatformHubEngine::render_platform_hub();
}
get_footer();

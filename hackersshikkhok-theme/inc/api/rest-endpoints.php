<?php
/**
 * Unified Theme REST API Loader.
 * Registers master endpoints on `rest_api_init` without loading heavy handler classes on normal frontend page views.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', static function (): void {
    \HackersShikkhok\Theme\API\RestController::register_routes();
}, 10 );

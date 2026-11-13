<?php
/**
 * Plugin Name:       HackersShikkhok Core Engine
 * Plugin URI:        https://hackersshikkhok.com
 * Description:       Ultra-lightweight Core Data Layer for HackersShikkhok.com. Registers mission-critical Custom Post Types, Custom Taxonomies, Custom Database Tables (dbDelta), Roles/Capabilities, and Background Cron Workers. All UI, rendering, REST endpoints, AI, and tools live in the permanent HackersShikkhok Theme.
 * Version:           5.0.0
 * Requires at least: 6.4
 * Requires PHP:      8.2
 * Author:            HackersShikkhok
 * Author URI:        https://hackersshikkhok.com
 * Text Domain:       hackersshikkhok-core
 * License:           GPL v2 or later
 */

declare(strict_types=1);

namespace HackersShikkhok\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'HS_CORE_VERSION', '5.0.0' );
define( 'HS_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'HS_CORE_BASENAME', plugin_basename( __FILE__ ) );

spl_autoload_register( static function ( string $class ): void {
    $prefix = 'HackersShikkhok\\Core\\';
    if ( strncmp( $prefix, $class, strlen( $prefix ) ) !== 0 ) {
        return;
    }

    $relative_class = substr( $class, strlen( $prefix ) );
    $file           = HS_CORE_PATH . 'includes/' . str_replace( '\\', '/', $relative_class ) . '.php';

    if ( file_exists( $file ) ) {
        require_once $file;
    }
} );

register_activation_hook( __FILE__, array( Core\Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Core\Deactivator::class, 'deactivate' ) );

add_action( 'plugins_loaded', static function (): void {
    Core\Plugin::instance()->boot();
}, 5 );

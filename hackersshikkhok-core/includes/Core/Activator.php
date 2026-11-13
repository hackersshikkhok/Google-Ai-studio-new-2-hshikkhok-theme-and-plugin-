<?php
declare(strict_types=1);

namespace HackersShikkhok\Core\Core;

use HackersShikkhok\Core\PostTypes\PostTypeRegistrar;
use HackersShikkhok\Core\Taxonomies\TaxonomyRegistrar;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Activator {

    public static function activate(): void {
        if ( version_compare( PHP_VERSION, '8.2.0', '<' ) ) {
            deactivate_plugins( HS_CORE_BASENAME );
            wp_die(
                esc_html(
                    sprintf(
                        'HackersShikkhok Core requires PHP 8.2 or higher. Your server is running PHP %s.',
                        PHP_VERSION
                    )
                )
            );
        }

        try {
            Database::install_tables();
        } catch ( \Throwable $e ) {
            error_log( 'HackersShikkhok DB Install Notice: ' . $e->getMessage() );
        }

        try {
            Capabilities::register_roles_and_caps();
        } catch ( \Throwable $e ) {
            error_log( 'HackersShikkhok Capabilities Notice: ' . $e->getMessage() );
        }

        try {
            PostTypeRegistrar::register_now();
            TaxonomyRegistrar::register_now();
            CronManager::schedule_events();
            flush_rewrite_rules();
        } catch ( \Throwable $e ) {
            error_log( 'HackersShikkhok Activation Notice: ' . $e->getMessage() );
        }
    }
}
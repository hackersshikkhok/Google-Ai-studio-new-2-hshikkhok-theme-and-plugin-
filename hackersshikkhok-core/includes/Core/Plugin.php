<?php
/**
 * Ultra-Lightweight Core Plugin Kernel.
 * Registers ONLY mission-critical data structures: DB Schema, CPTs, Taxonomies, and Background Cron Workers.
 * Zero frontend UI, zero shortcodes, zero admin pages.
 */

declare(strict_types=1);

namespace HackersShikkhok\Core\Core;

use HackersShikkhok\Core\PostTypes\PostTypeRegistrar;
use HackersShikkhok\Core\Taxonomies\TaxonomyRegistrar;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Plugin {
    private static ?self $instance = null;

    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function boot(): void {
        Database::register();
        PostTypeRegistrar::register();
        TaxonomyRegistrar::register();
        CronManager::register();
    }
}
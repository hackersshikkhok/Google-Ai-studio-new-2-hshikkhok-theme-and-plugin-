<?php
declare(strict_types=1);

namespace HackersShikkhok\Core\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Deactivator {
    public static function deactivate(): void {
        CronManager::clear_events();
        flush_rewrite_rules();
    }
}

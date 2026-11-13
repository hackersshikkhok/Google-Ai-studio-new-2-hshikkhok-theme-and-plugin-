<?php
declare(strict_types=1);

namespace HackersShikkhok\Theme\Modules\Platform;

final class EcosystemServices {
    public static function get_bangla_calendar_date(): array {
        return array(
            'bangla_date' => 'Autumn Season',
            'hijri_date'  => 'Islamic Calendar',
            'season'      => 'Autumn',
            'gregorian'   => gmdate( 'Y-m-d' ),
        );
    }
}
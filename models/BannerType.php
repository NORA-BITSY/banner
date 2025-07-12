<?php

namespace humhub\modules\banner\models;

class BannerType
{
    public const MANUAL = 'manual';
    public const WEATHER_ALERT = 'weather_alert';
    public const SAFETY_NOTICE = 'safety_notice';
    public const HARBOR_UPDATE = 'harbor_update';
    public const EVENT_ANNOUNCEMENT = 'event_announcement';
    public const MAINTENANCE_WINDOW = 'maintenance_window';

    public static function getTypes(): array
    {
        return [
            self::MANUAL,
            self::WEATHER_ALERT,
            self::SAFETY_NOTICE,
            self::HARBOR_UPDATE,
            self::EVENT_ANNOUNCEMENT,
            self::MAINTENANCE_WINDOW,
        ];
    }
}

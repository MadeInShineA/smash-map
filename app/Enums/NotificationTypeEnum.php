<?php

namespace App\Enums;

abstract class NotificationTypeEnum
{
    const DISTANCE = 1;
    const ATTENDEES = 2;
    const TIME = 3;

    const TYPES = [
        self::DISTANCE,
        self::ATTENDEES,
        self::TIME
    ];

    const LABELS = [
        self::DISTANCE => 'Distance notification',
        self::ATTENDEES => 'Attendees notification',
        self::TIME => 'Time notification'
    ];
}

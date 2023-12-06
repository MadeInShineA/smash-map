<?php

namespace App\Enums;

abstract class GameEnum
{
    const SMASH64 = 4;
    const MELEE = 1;
    const BRAWL = 5;
    const PROJECT_M = 2;
    const PROJECT_PLUS = 33602;
    const SMASH4 = 3;
    const ULTIMATE = 1386;

    const GAMES_ORDER = [
        self::SMASH64,
        self::MELEE,
        self::BRAWL,
        self::PROJECT_M,
        self::PROJECT_PLUS,
        self::SMASH4,
        self::ULTIMATE
    ];

}

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

    const GAMES = [
        self::SMASH64 => 'Super Smash Bros.',
        self::MELEE => 'Super Smash Bros. Melee',
        self::BRAWL => 'Super Smash Bros. Brawl',
        self::PROJECT_M => 'Project M',
        self::PROJECT_PLUS => 'Project+',
        self::SMASH4 => 'Super Smash Bros. for Wii U',
        self::ULTIMATE => 'Super Smash Bros. Ultimate'
    ];

    const COLORS = [
        self::SMASH64 => '#FAC41A',
        self::MELEE => '#A30010',
        self::BRAWL => '#660d02',
        self::PROJECT_M => '#3B448B',
        self::PROJECT_PLUS => '#6FD19C',
        self::SMASH4 => '#AFC1EE',
        self::ULTIMATE => '#F18A41'
    ];

}

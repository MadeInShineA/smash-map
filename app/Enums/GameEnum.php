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
    const HDR = 34157;
    const RIVALS2 = 53945;

    const IDS = [
        self::SMASH64,
        self::MELEE,
        self::BRAWL,
        self::PROJECT_M,
        self::PROJECT_PLUS,
        self::SMASH4,
        self::ULTIMATE,
        self::HDR,
        self::RIVALS2
    ];

    const NAMES = [
        self::SMASH64 => 'Super Smash Bros.',
        self::MELEE => 'Super Smash Bros. Melee',
        self::BRAWL => 'Super Smash Bros. Brawl',
        self::PROJECT_M => 'Project M',
        self::PROJECT_PLUS => 'Project+',
        self::SMASH4 => 'Super Smash Bros. for Wii U',
        self::ULTIMATE => 'Super Smash Bros. Ultimate',
        self::HDR => 'HDR',
        self::RIVALS2 => 'Rivals of Aether 2'
    ];

    const ABBREVIATIONS = [
        self::SMASH64 => '64',
        self::MELEE => 'Melee',
        self::BRAWL => 'Brawl',
        self::PROJECT_M => 'Project M',
        self::PROJECT_PLUS => 'Project +',
        self::SMASH4 => '3DS / WiiU',
        self::ULTIMATE => 'Ultimate',
        self::HDR => 'HDR',
        self::RIVALS2 => 'Rivals 2'
    ];

    const COLORS = [
        self::SMASH64 => '#FAC41A',
        self::MELEE => '#A30010',
        self::BRAWL => '#660d02',
        self::PROJECT_M => '#3B448B',
        self::PROJECT_PLUS => '#6FD19C',
        self::SMASH4 => '#AFC1EE',
        self::ULTIMATE => '#F18A41',
        self::HDR => '#015500',
        self::RIVALS2 => '#B19EEF'
    ];

    const HOVER_COLORS = [
        self::SMASH64 => '#D8A504',
        self::MELEE => '#82000C',
        self::BRAWL => '#510A01',
        self::PROJECT_M => '#2F366F',
        self::PROJECT_PLUS => '#3EC17A',
        self::SMASH4 => '#6A8CDF',
        self::ULTIMATE => '#E46810',
        self::HDR => '#013D00',
        self::RIVALS2 => '#9A85D1'
    ];

}

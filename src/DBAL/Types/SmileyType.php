<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * @extends AbstractEnumType<string, string>
 */
final class SmileyType extends AbstractEnumType
{
    public const string GREEN = 'GREEN';
    public const string RED = 'RED';
    public const string BLUE = 'BLUE';
    public const string YELLOW = 'YELLOW';

    protected static array $choices = [
        self::GREEN => 'Green',
        self::YELLOW => 'Yellow',
        self::RED => 'Red',
        self::BLUE => 'Blue',
    ];
}

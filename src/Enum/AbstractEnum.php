<?php

namespace App\Enum;

abstract class AbstractEnum
{
    public static function isSupported($value): bool
    {
        return in_array($value, static::getConstants());
    }

    public static abstract function getConstants(): array;
}

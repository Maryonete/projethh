<?php

namespace App\Enum;

use App\Enum\AbstractEnum;


class Role extends AbstractEnum
{
    const PRESIDENT = 'president';
    const REFERENT = 'referent';

    public static function getConstants(): array
    {
        return [
            self::PRESIDENT,
            self::REFERENT,
        ];
    }
}

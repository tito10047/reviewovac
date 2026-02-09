<?php

namespace App\Enum;

enum Language:string {

    case Slovak = 'sk';
    case Hungarian = 'hu';
    case Czech = 'cz';
    case Romanian = 'ro';
    case Croatian = 'hr';
    case Slovenian = 'sl';

    /**
     * @return string[]
     */
    public static function getSupportedLanguages():array {
        return array_map(fn(Language $language) => $language->value, self::cases());
    }
}

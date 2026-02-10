<?php

namespace App\Enum;

enum Language: string
{
    case Slovak = 'sk';
    case Hungarian = 'hu';
    case Czech = 'cz';
    case Romanian = 'ro';
    case Croatian = 'hr';
    case Slovenian = 'sl';

    /**
     * @return string[]
     */
    public static function getSupportedLanguages(): array
    {
        return array_map(fn (Language $language) => $language->value, self::cases());
    }

    public function needTranslationFor(Language $targetLanguage): bool
    {
        $noNeedTranslation = [
            self::Slovak->value => self::Czech->value,
        ];
        if ($this == $targetLanguage) {
            return false;
        }

        return array_all($noNeedTranslation, fn ($value, $key) => ($value !== $targetLanguage->value || ($key !== $this->value)) && ($key !== $targetLanguage->value || ($value !== $this->value))
        );
    }
}
